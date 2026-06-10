<?php

namespace App\Http\Controllers;

use App\Models\AkuPost;
use App\Models\AkuComment;
use App\Models\AkuLike;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotifHelper;


class AkuController extends Controller
{
    public function index()
    {
        $posts = AkuPost::with(['user', 'comments.user', 'comments.replies.user'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        $likedIds = AkuLike::where('user_id', Auth::id())
            ->pluck('aku_post_id')->toArray();

        $likersByPost = AkuLike::whereIn('aku_post_id', $posts->pluck('id'))
            ->with('user')
            ->latest()
            ->get()
            ->groupBy('aku_post_id')
            ->map(fn($likes) => $likes->take(5)->map(fn($l) => [
                'name'   => $l->user->name ?? '?',
                'avatar' => $l->user->avatar ?? null,
            ])->values());

        return view('fanbase.aku', compact('posts', 'likedIds', 'likersByPost'));
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin()) abort(403);

        $request->validate([
            'body'  => 'required|string|min:2',
            'title' => 'nullable|string|max:200',
            'mood'  => 'nullable|string|max:50',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $filename  = 'aku_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/aku'), $filename);
            $imagePath = 'images/aku/' . $filename;
        }

        AkuPost::create([
            'user_id' => Auth::id(),
            'title'   => $request->title,
            'body'    => $request->body,
            'image'   => $imagePath,
            'mood'    => $request->mood,
        ]);

        return redirect()->route('aku')->with('success', 'Postingan berhasil dibuat.');
    }

    public function destroy($id)
    {
        if (!$this->isAdmin()) abort(403);
        $post = AkuPost::findOrFail($id);
        if ($post->image && file_exists(public_path($post->image))) {
            unlink(public_path($post->image));
        }
        $post->delete();
        return redirect()->route('aku')->with('success', 'Postingan dihapus.');
    }

    public function like($id)
    {
        $post     = AkuPost::findOrFail($id);
        $userId   = Auth::id();
        $existing = AkuLike::where('aku_post_id', $id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            AkuLike::create(['aku_post_id' => $id, 'user_id' => $userId]);
            $post->increment('likes_count');
            $liked = true;
            if ($post->user_id !== $userId) {
                NotifHelper::send(
                    $post->user_id, $userId,
                    'like', Auth::user()->name . ' menyukai postinganmu',
                    $post->title ?? \Illuminate\Support\Str::limit($post->body, 50),
                    url('/aku')
                );
            }
        }

        $likers = AkuLike::where('aku_post_id', $id)
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

    public function comment(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|min:1|max:500']);

        $post = AkuPost::findOrFail($id);

        $comment = AkuComment::create([
            'aku_post_id' => $id,
            'user_id'     => Auth::id(),
            'parent_id'   => $request->parent_id ?? null,
            'body'        => $request->body,
        ]);

        $post->increment('comments_count');

        if ($post->user_id !== Auth::id()) {
            try {
                NotifHelper::send(
                    $post->user_id, Auth::id(),
                    'comment', Auth::user()->name . ' mengomentari postinganmu',
                    $request->body,
                    url('/aku')
                );
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id'        => $comment->id,
                'body'      => $comment->body,
                'user'      => Auth::user()->name,
                'avatar'    => Auth::user()->avatar,
                'parent_id' => $comment->parent_id,
                'time'      => 'Baru saja',
            ]
        ]);
    }

    private function isAdmin()
    {
        return in_array(Auth::user()->email,
            explode(',', env('ADMIN_EMAILS', '')));
    }

    public function update(Request $request, $id) {
        if (!$this->isAdmin()) abort(403);
        $post = AkuPost::findOrFail($id);
        $request->validate(['body'=>'required|string|min:2']);
        $post->update(['title'=>$request->title,'body'=>$request->body]);
        return response()->json(['success'=>true]);
    }

    public function destroyComment($postId, $id) {
        $comment = AkuComment::findOrFail($id);
        if ($comment->user_id !== Auth::id() && !$this->isAdmin()) abort(403);
        $comment->delete();
        AkuPost::findOrFail($postId)->decrement('comments_count');
        return response()->json(['success'=>true]);
    }
}