<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\PostCommentLike;
use App\Models\MemberLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotifHelper;


class KitaController extends Controller
{
    public function index()
    {
        $posts = Post::with([
            'user',
            'comments' => fn($q) => $q->whereNull('parent_id')
                ->with(['user', 'replies.user'])->latest(),
        ])->orderByDesc('created_at')->paginate(15);

        // Member join logs — pakai member_logs jika ada, fallback ke users.created_at
        $memberLogs = collect();
        try {
            $logs = MemberLog::with('user')->orderByDesc('created_at')->get();
            if ($logs->isNotEmpty()) {
                $memberLogs = $logs;
            } else {
                throw new \RuntimeException('empty');
            }
        } catch (\Throwable $e) {
            // Fallback: tiap user punya created_at sebagai waktu bergabung
            try {
                $memberLogs = \App\Models\User::orderByDesc('created_at')->get()
                    ->map(fn($u) => (object)[
                        'id'         => $u->id,
                        'user'       => $u,
                        'created_at' => $u->created_at,
                    ]);
            } catch (\Throwable $e2) {}
        }

        $likedIds = PostLike::where('user_id', Auth::id())
            ->pluck('post_id')->toArray();

        $likersByPost = PostLike::whereIn('post_id', $posts->pluck('id'))
            ->with('user')->latest()->get()
            ->groupBy('post_id')
            ->map(fn($likes) => $likes->take(5)->map(fn($l) => [
                'name'   => $l->user->name ?? '?',
                'avatar' => $l->user->avatar ?? null,
            ])->values());

        // Komentar yang sudah di-like oleh user (safe jika tabel belum ada)
        $likedCommentIds = [];
        try {
            $likedCommentIds = PostCommentLike::where('user_id', Auth::id())
                ->pluck('comment_id')->toArray();
        } catch (\Throwable $e) {}

        // Peta musisi (level) untuk badge berwarna di tiap penulis
        $musicianMap = [];
        try {
            $authorIds = $posts->pluck('user_id')->all();
            foreach ($posts as $p) {
                foreach ($p->comments as $c) {
                    $authorIds[] = $c->user_id;
                    foreach ($c->replies as $r) $authorIds[] = $r->user_id;
                }
            }
            $musicianMap = \App\Models\MusicianProfile::whereIn('user_id', array_values(array_unique($authorIds)))
                ->get(['id', 'user_id', 'skill_level'])
                ->keyBy('user_id')
                ->map(fn($m) => ['level' => $m->skill_level, 'profile_id' => $m->id])
                ->toArray();
        } catch (\Throwable $e) {}

        return view('fanbase.kita', compact('posts', 'memberLogs', 'likedIds', 'likersByPost', 'likedCommentIds', 'musicianMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'body'     => 'required|string|min:2|max:500',
            'location' => 'nullable|string|max:100',
        ]);

        $body = \App\Helpers\WordFilter::clean($request->body);

        Post::create([
            'user_id'  => Auth::id(),
            'body'     => $body,
            'location' => $request->location,
        ]);

        return redirect()->route('kita')->with('success', 'Postingan berhasil dibuat.');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id !== Auth::id()) abort(403);
        $post->delete();
        return response()->json(['success' => true]);
    }

    public function like($id)
    {
        $post     = Post::findOrFail($id);
        $userId   = Auth::id();
        $existing = PostLike::where('user_id', $userId)->where('post_id', $id)->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            PostLike::create(['user_id' => $userId, 'post_id' => $id]);
            $post->increment('likes_count');
            $liked = true;
            if ($post->user_id !== $userId) {
                try {
                    NotifHelper::send(
                        $post->user_id, $userId,
                        'like', Auth::user()->name . ' menyukai postinganmu',
                        \Illuminate\Support\Str::limit($post->body, 50),
                        url('/kita') . '?openPost=' . $id . '#kitaPost' . $id
                    );
                } catch (\Throwable $e) {}
            }
        }

        $likers = PostLike::where('post_id', $id)
            ->with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($l) => ['name' => $l->user->name ?? '?', 'avatar' => $l->user->avatar ?? null]);

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $post->fresh()->likes_count,
            'likers'      => $likers,
        ]);
    }

    public function likeComment(Request $request, $postId, $commentId)
    {
        try {
            $comment  = PostComment::findOrFail($commentId);
            $userId   = Auth::id();
            $existing = PostCommentLike::where('comment_id', $commentId)->where('user_id', $userId)->first();
            if ($existing) {
                $existing->delete();
                $comment->decrement('likes_count');
                return response()->json(['liked' => false, 'likes_count' => max(0, $comment->fresh()->likes_count)]);
            }
            PostCommentLike::create(['comment_id' => $commentId, 'user_id' => $userId]);
            $comment->increment('likes_count');
            return response()->json(['liked' => true, 'likes_count' => $comment->fresh()->likes_count]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Fitur belum tersedia'], 422);
        }
    }

    public function comment(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|min:1|max:300']);

        $post    = Post::findOrFail($id);
        $parentId = $request->parent_id ?? null;
        $comment = PostComment::create([
            'user_id'   => Auth::id(),
            'post_id'   => $id,
            'parent_id' => $parentId,
            'body'      => $request->body,
        ]);
        // Hanya increment comments_count untuk komentar root
        if (!$parentId) $post->increment('comments_count');

        if ($post->user_id !== Auth::id()) {
            try {
                NotifHelper::send(
                    $post->user_id, Auth::id(),
                    'comment', Auth::user()->name . ' mengomentari postinganmu',
                    $request->body,
                    url('/kita') . '?openPost=' . $id . '#kitaPost' . $id
                );
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id'        => $comment->id,
                'body'      => $comment->body,
                'parent_id' => $parentId,
                'user'      => Auth::user()->name,
                'avatar'    => Auth::user()->avatar ?? asset('images/default-avatar.png'),
                'time'      => 'Baru saja',
            ]
        ]);
    }
    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);
        if ($post->user_id !== Auth::id()) abort(403);
        $request->validate(['body'=>'required|string|min:2|max:500']);
        $post->update(['body'=>$request->body]);
        return response()->json(['success'=>true]);
    }

    public function destroyComment($postId, $id) {
        $comment = PostComment::findOrFail($id);
        if ($comment->user_id !== Auth::id()) abort(403);
        $comment->delete();
        Post::findOrFail($postId)->decrement('comments_count');
        return response()->json(['success'=>true]);
    }
}