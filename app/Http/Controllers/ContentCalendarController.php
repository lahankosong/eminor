<?php

namespace App\Http\Controllers;

use App\Models\ContentPlan;
use App\Models\Song;
use Illuminate\Http\Request;

class ContentCalendarController extends Controller
{
    public function index()
    {
        $plans = collect();
        try {
            $plans = ContentPlan::with('song')
                ->orderBy('plan_date')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            // tabel belum ada di server — tampilkan kosong, jalankan fixdb.php
        }
        $songs = Song::orderBy('track_number')->get();
        return view('admin.calendar', compact('plans', 'songs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_date'    => 'required|date',
            'title'        => 'nullable|string|max:255',
            'song_id'      => 'nullable|integer',
            'content_type' => 'nullable|string|max:20',
            'status'       => 'nullable|string|max:20',
            'notes'        => 'nullable|string',
        ]);

        ContentPlan::create([
            'plan_date'    => $request->plan_date,
            'song_id'      => $request->song_id ?: null,
            'platforms'    => $request->platforms
                ? implode(',', (array) $request->platforms)
                : null,
            'content_type' => $request->content_type ?: 'short',
            'title'        => $request->title,
            'status'       => $request->status ?: 'rencana',
            'notes'        => $request->notes,
        ]);

        return redirect()->route('admin.calendar')
            ->with('success', 'Jadwal konten ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $plan = ContentPlan::findOrFail($id);
        $plan->update([
            'status' => $request->status ?? $plan->status,
        ]);

        return redirect()->route('admin.calendar')
            ->with('success', 'Status diperbarui.');
    }

    public function destroy($id)
    {
        ContentPlan::findOrFail($id)->delete();
        return redirect()->route('admin.calendar')
            ->with('success', 'Jadwal dihapus.');
    }
}
