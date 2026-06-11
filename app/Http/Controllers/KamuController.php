<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\AkuPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KamuController extends Controller
{
    public function index() {
        $user = Auth::user();

        try {
            $posts = Post::where('user_id', $user->id)->orderByDesc('created_at')->get();
        } catch (\Throwable $e) {
            $posts = collect();
        }

        try {
            $notes = \App\Models\KamuNote::where('user_id', Auth::id())->get()
                ->sortByDesc('is_pinned')->sortByDesc('created_at')->values();
        } catch (\Throwable $e) {
            $notes = collect();
        }

        $totalLikes    = $posts->sum('likes_count');
        $totalComments = $posts->sum('comments_count');
        $totalPosts    = $posts->count();
        return view('fanbase.kamu', compact('user', 'posts', 'notes', 'totalLikes', 'totalComments', 'totalPosts'));
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