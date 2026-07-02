<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\AkuPost;
use App\Models\MusicianProfile;
use App\Models\Follow;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KamuController extends Controller
{
    public function index() {
        $user = Auth::user();

        $posts = collect(); $notes = collect();
        $totalLikes = 0; $totalComments = 0; $totalPosts = 0;
        $musician = null; $followers = 0; $following = 0;

        if ($user) {
            try { $posts = Post::where('user_id', $user->id)->orderByDesc('created_at')->get(); } catch (\Throwable $e) {}
            try {
                $notes = \App\Models\KamuNote::where('user_id', $user->id)->get()
                    ->sortByDesc('is_pinned')->sortByDesc('created_at')->values();
            } catch (\Throwable $e) {}
            $totalLikes    = $posts->sum('likes_count');
            $totalComments = $posts->sum('comments_count');
            $totalPosts    = $posts->count();
            try { $musician = MusicianProfile::where('user_id', $user->id)->first(); } catch (\Throwable $e) {}
            try {
                $followers = Follow::where('following_id', $user->id)->count();
                $following = Follow::where('follower_id', $user->id)->count();
            } catch (\Throwable $e) {}
        }

        $articles = collect();
        try { $articles = Article::orderBy('batch')->orderBy('id')->get(['id','slug','title','category','batch','excerpt','reading_time']); } catch (\Throwable $e) {}

        return view('fanbase.kamu', compact(
            'user', 'posts', 'notes', 'totalLikes', 'totalComments', 'totalPosts',
            'musician', 'followers', 'following', 'articles'
        ));
    }
    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);
        if ($post->user_id !== Auth::id()) abort(403);
        $request->validate(['body'=>'required|string|min:2|max:500']);
        $post->update(['body'=>$request->body]);
        return response()->json(['success'=>true]);
    }

    public function destroy($id) {
        $post = Post::findOrFail($id);
        if ($post->user_id !== Auth::id()) abort(403);
        $post->delete();
        return response()->json(['success'=>true]);
    }
}