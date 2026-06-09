<?php
namespace App\Http\Controllers;
use App\Models\KamuNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KamuNoteController extends Controller {
    public function store(Request $request) {
        $request->validate(['body'=>'required|string|min:1|max:2000']);
        KamuNote::create([
            'user_id' => Auth::id(),
            'title'   => $request->title,
            'body'    => $request->body,
            'color'   => $request->color ?? '#FFF8F0',
        ]);
        return redirect()->route('kamu')->with('success','Catatan disimpan.');
    }
    public function update(Request $request, $id) {
        $note = KamuNote::findOrFail($id);
        if ($note->user_id !== Auth::id()) abort(403);
        $request->validate(['body'=>'required|string|min:1|max:2000']);
        $note->update(['title'=>$request->title,'body'=>$request->body,'color'=>$request->color??$note->color]);
        return response()->json(['success'=>true,'note'=>['title'=>$note->title,'body'=>$note->body]]);
    }
    public function destroy($id) {
        $note = KamuNote::findOrFail($id);
        if ($note->user_id !== Auth::id()) abort(403);
        $note->delete();
        return response()->json(['success'=>true]);
    }
}