<?php

namespace App\Http\Controllers;

use App\Models\GigPost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GigPostController extends Controller
{
    public function publicBoard(Request $request)
    {
        $type     = $request->query('type', '');
        $location = trim((string) $request->query('kota', ''));

        $query = GigPost::with('user')
            ->where('status', 'open')
            ->latest();

        if ($type && array_key_exists($type, GigPost::types())) {
            $query->where('type', $type);
        }
        if ($location !== '') {
            $query->where('location', 'like', '%' . $location . '%');
        }

        $gigs = $query->take(20)->get();

        $seo = [
            'title'       => 'Papan Gig Musisi Indonesia — Audisi, Open Mic & Session Player',
            'description' => 'Temukan gig, audisi band, open mic, dan peluang session player untuk musisi indie Indonesia. Gratis, diperbarui setiap hari.',
            'url'         => url('/gig'),
            'image'       => asset('images/Margonoandi.jpeg'),
            'schema'      => [
                '@context' => 'https://schema.org',
                '@type'    => 'ItemList',
                'name'     => 'Papan Gig Musisi Indonesia',
                'url'      => url('/gig'),
                'description' => 'Daftar peluang gig, audisi, dan session player untuk musisi indie Indonesia.',
            ],
        ];

        return view('gig.board', compact('gigs', 'seo', 'type', 'location'));
    }

    public function create()
    {
        return view('fanbase.gig.create', ['types' => GigPost::types()]);
    }

    public function edit($id)
    {
        $gig = GigPost::findOrFail($id);
        if ($gig->user_id !== Auth::id()) abort(403);
        return view('fanbase.gig.create', ['types' => GigPost::types(), 'gig' => $gig]);
    }

    public function update(Request $request, $id)
    {
        $gig = GigPost::findOrFail($id);
        if ($gig->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            'title'        => 'required|string|max:120',
            'type'         => 'required|string|max:50',
            'description'  => 'nullable|string|max:2000',
            'location'     => 'nullable|string|max:120',
            'date_event'   => 'nullable|date',
            'requirements' => 'nullable|string|max:1000',
        ]);

        try {
            $gig->update($data);

            // Sinkronkan body postingan Kita yang ter-link
            $label = GigPost::typeLabel($gig->type);
            $body  = "{$label}: {$gig->title}";
            if ($gig->location)   $body .= "\n📍 {$gig->location}";
            if ($gig->date_event) $body .= "  🗓 " . $gig->date_event->format('d M Y');
            if ($gig->description) $body .= "\n\n" . Str::limit($gig->description, 250);
            Post::where('linked_type', 'gig')->where('linked_id', $gig->id)->update(['body' => $body]);
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        return redirect()->route('musisi.index', ['tab' => 'gig'])
            ->with('success', 'Pengumuman diperbarui.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:120',
            'type'         => 'required|string|max:50',
            'description'  => 'nullable|string|max:2000',
            'location'     => 'nullable|string|max:120',
            'date_event'   => 'nullable|date',
            'requirements' => 'nullable|string|max:1000',
        ]);
        $data['user_id'] = Auth::id();
        $data['status']  = 'open';

        try {
            $gig = GigPost::create($data);

            // Auto-post ke Kita
            $label = GigPost::typeLabel($gig->type);
            $body  = "{$label}: {$gig->title}";
            if ($gig->location)   $body .= "\n📍 {$gig->location}";
            if ($gig->date_event) $body .= "  🗓 " . $gig->date_event->format('d M Y');
            if ($gig->description) $body .= "\n\n" . Str::limit($gig->description, 250);

            // linked_* hanya dipakai jika kolomnya ada (hindari 500 bila skema belum di-fixdb)
            $postData = ['user_id' => Auth::id(), 'body' => $body];
            if (\Illuminate\Support\Facades\Schema::hasColumn('posts', 'linked_type')) {
                $postData['linked_type'] = 'gig';
                $postData['linked_id']   = $gig->id;
            }
            Post::create($postData);
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()
                ->with('error', 'Gagal memasang Gig: ' . $e->getMessage());
        }

        return redirect()->route('musisi.index', ['tab' => 'gig'])
            ->with('success', 'Pengumuman dipasang dan otomatis dibagikan ke Kita.');
    }

    public function toggleStatus($id)
    {
        $gig = GigPost::findOrFail($id);
        if ($gig->user_id !== Auth::id()) abort(403);
        $gig->update(['status' => $gig->status === 'open' ? 'closed' : 'open']);
        return back()->with('success', 'Status pengumuman diperbarui.');
    }

    public function destroy($id)
    {
        $gig = GigPost::findOrFail($id);
        if ($gig->user_id !== Auth::id()) abort(403);

        // Hapus linked kita post juga
        Post::where('linked_type', 'gig')->where('linked_id', $gig->id)->delete();

        $gig->delete();
        return redirect()->route('musisi.index', ['tab' => 'gig'])
            ->with('success', 'Pengumuman dihapus.');
    }
}
