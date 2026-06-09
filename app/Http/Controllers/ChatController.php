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

class ChatController extends Controller
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

        return view('community.chat', compact('conversations', 'groups', 'users'));
    }

    public function showConversation($id)
    {
        $userId       = Auth::id();
        $conversation = Conversation::with(['userOne', 'userTwo', 'messages.user'])
            ->findOrFail($id);

        if ($conversation->user_one_id !== $userId &&
            $conversation->user_two_id !== $userId) {
            abort(403);
        }

        // Mark messages as read
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

        return view('community.chat', compact(
            'conversations', 'groups', 'users',
            'conversation'
        ));
    }

    public function startConversation($userId)
    {
        $myId       = Auth::id();
        $otherUser  = User::findOrFail($userId);

        $minId = min($myId, $userId);
        $maxId = max($myId, $userId);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $minId,
            'user_two_id' => $maxId,
        ]);

        return redirect()->route('chat.conversation', $conversation->id);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $userId       = Auth::id();
        $conversation = Conversation::findOrFail($id);

        if ($conversation->user_one_id !== $userId &&
            $conversation->user_two_id !== $userId) {
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
                'id'      => $message->id,
                'body'    => $message->body,
                'user_id' => $userId,
                'name'    => Auth::user()->name,
                'avatar'  => Auth::user()->avatar,
                'time'    => 'Baru saja',
            ]
        ]);
    }

    // GROUP CHAT
    public function showGroup($id)
    {
        $userId = Auth::id();
        $group  = Group::with(['members.user', 'messages.user', 'creator'])
            ->findOrFail($id);

        if (!$group->isMember($userId)) {
            abort(403);
        }

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderByDesc('last_message_at')
            ->get();

        $groups = Group::whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('last_message_at')
            ->get();

        $users = User::where('id', '!=', $userId)->get();

        return view('community.chat', compact(
            'conversations', 'groups', 'users', 'group'
        ));
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:300',
            'members'     => 'required|array|min:1',
            'members.*'   => 'exists:users,id',
        ]);

        $group = Group::create([
            'name'        => $request->name,
            'description' => $request->description,
            'created_by'  => Auth::id(),
        ]);

        // Add creator as admin
        GroupMember::create([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'role'     => 'admin',
        ]);

        // Add other members
        foreach ($request->members as $memberId) {
            if ($memberId != Auth::id()) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id'  => $memberId,
                    'role'     => 'member',
                ]);
            }
        }

        return redirect()->route('chat.group', $group->id)
            ->with('success', 'Group berhasil dibuat.');
    }

    public function sendGroupMessage(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $userId = Auth::id();
        $group  = Group::findOrFail($id);

        if (!$group->isMember($userId)) {
            abort(403);
        }

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
                'time'    => 'Baru saja',
            ]
        ]);
    }
}