<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /** Simpan peran user dari modal "Pilih Peranmu". */
    public function saveRoles(Request $request)
    {
        $valid = array_keys(User::roleOptions());
        $roles = collect((array) $request->input('roles', []))
            ->map(fn ($r) => trim((string) $r))
            ->filter(fn ($r) => in_array($r, $valid, true))
            ->unique()->take(6)->implode(',');

        try {
            $u = Auth::user();
            $u->roles = $roles !== '' ? $roles : 'penikmat';
            $u->save();

            // Sinkron ke profil musisi (direktori/matchmaking) bila profil ada & roles masih kosong
            $labels = User::rolesToCleanLabels($u->roles);
            if ($labels !== '') {
                $profile = \App\Models\MusicianProfile::where('user_id', $u->id)->first();
                if ($profile && empty($profile->roles)) {
                    $profile->roles = $labels;
                    $profile->save();
                }
            }
        } catch (\Throwable $e) {
            return response()->json(['ok' => false], 200);
        }

        return response()->json(['ok' => true]);
    }
}
