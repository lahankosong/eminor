<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'artist_name', 'artist_role', 'artist_project',
            'tagline_1', 'tagline_2', 'tagline_3',
            'hero_story', 'bio', 'spotify_url',
            'youtube_url', 'apple_music_url',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                SiteSetting::set($field, $request->input($field));
            }
        }

        // Handle photo upload
        if ($request->hasFile('artist_photo')) {
            $file = $request->file('artist_photo');
            $filename = 'margonoandi.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            SiteSetting::set('artist_photo', 'images/' . $filename);
        }

        // Handle feature screenshots (6 slots)
        $featLabels = ['maftuner', 'chord', 'portofolio', 'cari-personil', 'chat', 'posting'];
        foreach ($featLabels as $i => $slug) {
            $field = 'feat_screenshot_' . $i;
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = 'feat_' . $slug . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/features'), $filename);
                SiteSetting::set($field, 'images/features/' . $filename);
            }
            // Allow clearing individual screenshot
            if ($request->input('clear_' . $field)) {
                $old = SiteSetting::get($field);
                if ($old && file_exists(public_path($old))) {
                    @unlink(public_path($old));
                }
                SiteSetting::set($field, '');
            }
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}