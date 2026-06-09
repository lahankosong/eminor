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
            'youtube_url', 'apple_music_url', 'community_tagline',
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

        return redirect()->route('admin.settings')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}