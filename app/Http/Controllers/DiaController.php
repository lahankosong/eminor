<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiaController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderByDesc('last_message_at')
            ->get();

        $groups = Group::with(['members.user'])
            ->whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('last_message_at')
            ->get();

        $users = User::where('id', '!=', $userId)->get();

        return view('fanbase.dia', compact('conversations', 'groups', 'users'));
    }

    public function conversation($id)
    {
        $userId = Auth::id();
        $conversation = Conversation::with(['userOne', 'userTwo', 'messages.user'])
            ->findOrFail($id);

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $conversation->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderByDesc('last_message_at')
            ->get();

        $groups = Group::whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('last_message_at')
            ->get();

        $users = User::where('id', '!=', $userId)->get();

        return view('fanbase.dia', compact(
            'conversations', 'groups', 'users', 'conversation'
        ));
    }

    public function start($userId)
    {
        $myId  = Auth::id();
        $minId = min($myId, (int)$userId);
        $maxId = max($myId, (int)$userId);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $minId,
            'user_two_id' => $maxId,
        ]);

        return redirect()->route('dia.conversation', $conversation->id);
    }

    public function send(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:1000']);
        $userId       = Auth::id();
        $conversation = Conversation::findOrFail($id);

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $message = Message::create([
            'conversation_id' => $id,
            'user_id'         => $userId,
            'body'            => $request->body,
        ]);

        $conversation->update([
            'last_message'    => $request->body,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id'     => $message->id,
                'body'   => $message->body,
                'user_id'=> $userId,
                'name'   => Auth::user()->name,
                'avatar' => Auth::user()->avatar,
                'time'   => now()->format('H:i'),
            ]
        ]);
        NotifHelper::send(
            $conversation->getOtherUser(Auth::id())->id,
            Auth::id(),
            'message', Auth::user()->name . ' mengirim pesan',
            $request->body,
            url('/dia/conversation/' . $id)
        );
    }

    public function group($id)
    {
        $userId = Auth::id();
        $group  = Group::with(['members.user', 'messages.user', 'creator'])
            ->findOrFail($id);

        if (!$group->isMember($userId)) abort(403);

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderByDesc('last_message_at')
            ->get();

        $groups = Group::whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('last_message_at')
            ->get();

        $users = User::where('id', '!=', $userId)->get();

        return view('fanbase.dia', compact(
            'conversations', 'groups', 'users', 'group'
        ));
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'members' => 'required|array|min:1',
        ]);

        $group = Group::create([
            'name'        => $request->name,
            'description' => $request->description,
            'created_by'  => Auth::id(),
        ]);

        GroupMember::create(['group_id' => $group->id, 'user_id' => Auth::id(), 'role' => 'admin']);

        foreach ($request->members as $memberId) {
            if ($memberId != Auth::id()) {
                GroupMember::create(['group_id' => $group->id, 'user_id' => $memberId, 'role' => 'member']);
            }
        }

        return redirect()->route('dia.group', $group->id);
    }

    public function sendGroup(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:1000']);
        $userId = Auth::id();
        $group  = Group::findOrFail($id);

        if (!$group->isMember($userId)) abort(403);

        $message = GroupMessage::create([
            'group_id' => $id,
            'user_id'  => $userId,
            'body'     => $request->body,
        ]);

        $group->update([
            'last_message'    => $request->body,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id'      => $message->id,
                'body'    => $message->body,
                'user_id' => $userId,
                'name'    => Auth::user()->name,
                'avatar'  => Auth::user()->avatar,
                'time'    => now()->format('H:i'),
            ]
        ]);

        
    }
}