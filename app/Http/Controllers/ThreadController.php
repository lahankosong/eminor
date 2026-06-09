<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{
    public function index()
    {
        $categories = ['umum', 'lagu', 'kolaborasi', 'saran', 'perkenalan'];

        $threads = Thread::with(['user', 'latestReply.user'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_reply_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('community.threads', compact('threads', 'categories'));
    }

    public function create()
    {
        $categories = ['umum', 'lagu', 'kolaborasi', 'saran', 'perkenalan'];
        return view('community.thread-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|min:5|max:150',
            'body'     => 'required|string|min:10|max:5000',
            'category' => 'required|in:umum,lagu,kolaborasi,saran,perkenalan',
        ]);

        $thread = Thread::create([
            'user_id'  => Auth::id(),
            'title'    => $request->title,
            'body'     => $request->body,
            'category' => $request->category,
        ]);

        return redirect()->route('community.thread.show', $thread->id)
            ->with('success', 'Thread berhasil dibuat.');
    }

    public function show($id)
    {
        $thread = Thread::with(['user', 'replies.user'])->findOrFail($id);
        $thread->increment('views_count');

        return view('community.thread-show', compact('thread'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string|min:2|max:2000',
        ]);

        $thread = Thread::findOrFail($id);

        if ($thread->is_locked) {
            return back()->with('error', 'Thread ini sudah dikunci.');
        }

        $badWords = ['bangsat', 'anjing', 'babi', 'goblok', 'tolol',
                     'kontol', 'memek', 'bajingan', 'sialan', 'kampret'];
        $body = $request->body;
        foreach ($badWords as $word) {
            $body = preg_replace('/\b' . preg_quote($word, '/') . '\b/i',
                str_repeat('*', strlen($word)), $body);
        }

        ThreadReply::create([
            'user_id'   => Auth::id(),
            'thread_id' => $id,
            'body'      => $body,
        ]);

        $thread->increment('replies_count');
        $thread->update(['last_reply_at' => now()]);

        return redirect()->route('community.thread.show', $id)
            ->with('success', 'Balasan berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $thread = Thread::findOrFail($id);
        if ($thread->user_id !== Auth::id()) {
            abort(403);
        }
        $thread->delete();
        return redirect()->route('community.threads')
            ->with('success', 'Thread berhasil dihapus.');
    }
}