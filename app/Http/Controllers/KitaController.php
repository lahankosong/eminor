<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotifHelper;


class KitaController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $likedIds = PostLike::where('user_id', Auth::id())
            ->pluck('post_id')->toArray();

        $likersByPost = PostLike::whereIn('post_id', $posts->pluck('id'))
            ->with('user')
            ->latest()
            ->get()
            ->groupBy('post_id')
            ->map(fn($likes) => $likes->take(5)->map(fn($l) => [
                'name'   => $l->user->name ?? '?',
                'avatar' => $l->user->avatar ?? null,
            ])->values());

        return view('fanbase.kita', compact('posts', 'likedIds', 'likersByPost'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'body'     => 'required|string|min:2|max:500',
            'location' => 'nullable|string|max:100',
        ]);

        $badWords = ['bangsat','anjing','babi','goblok','tolol','kontol','memek','bajingan','sialan','kampret'];
        $body = $request->body;
        foreach ($badWords as $word) {
            $body = preg_replace('/\b'.preg_quote($word,'/').'\b/i', str_repeat('*', strlen($word)), $body);
        }

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
                NotifHelper::send(
                    $post->user_id, $userId,
                    'like', Auth::user()->name . ' menyukai postinganmu',
                    \Illuminate\Support\Str::limit($post->body, 50),
                    url('/kita')
                );
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

    public function comment(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|min:1|max:300']);

        $post = Post::findOrFail($id);
        $comment = PostComment::create([
            'user_id' => Auth::id(),
            'post_id' => $id,
            'body'    => $request->body,
        ]);
        $post->increment('comments_count');

        if ($post->user_id !== Auth::id()) {
            try {
                NotifHelper::send(
                    $post->user_id, Auth::id(),
                    'comment', Auth::user()->name . ' mengomentari postinganmu',
                    $request->body,
                    url('/kita')
                );
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id'     => $comment->id,
                'body'   => $comment->body,
                'user'   => Auth::user()->name,
                'avatar' => Auth::user()->avatar,
                'time'   => 'Baru saja',
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