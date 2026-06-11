<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationInvite;
use App\Models\Message;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\AppNotification;
use App\Helpers\NotifHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiaController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $conversations = $this->userConversations($userId);
        $groups        = $this->userGroups($userId);
        $users         = $this->sortedUsers($userId);
        $unreadCounts  = $this->getUnreadCounts($conversations, $userId);

        return view('fanbase.dia', compact('conversations', 'groups', 'users', 'unreadCounts'));
    }

    public function ping()
    {
        try {
            User::where('id', Auth::id())->update([
                'last_seen' => now(),
                'is_online' => true,
            ]);
        } catch (\Throwable $e) {}
        return response()->json(['ok' => true]);
    }

    public function conversation($id)
    {
        $userId       = Auth::id();
        $conversation = Conversation::with(['userOne', 'userTwo'])->findOrFail($id);

        $joinedAt = $this->resolveConversationAccess($conversation, $userId);

        // Mark received messages as read
        $readQuery = $conversation->messages()->where('user_id', '!=', $userId)->whereNull('read_at');
        if ($joinedAt) $readQuery->where('created_at', '>=', $joinedAt);
        $readQuery->update(['read_at' => now()]);

        $msgQuery = Message::with('user')->where('conversation_id', $id)->orderBy('created_at');
        if ($joinedAt) $msgQuery->where('created_at', '>=', $joinedAt);
        $conversation->setRelation('messages', $msgQuery->get());

        $conversations = $this->userConversations($userId);
        $groups        = $this->userGroups($userId);
        $users         = $this->sortedUsers($userId);
        $unreadCounts  = $this->getUnreadCounts($conversations, $userId);

        return view('fanbase.dia', compact(
            'conversations', 'groups', 'users', 'conversation', 'unreadCounts'
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

        $this->resolveConversationAccess($conversation, $userId);

        $message = Message::create([
            'conversation_id' => $id,
            'user_id'         => $userId,
            'body'            => $request->body,
        ]);

        $conversation->update([
            'last_message'    => $request->body,
            'last_message_at' => now(),
        ]);

        // Notify all participants
        $recipientIds = $this->conversationParticipants($conversation, $userId);
        foreach ($recipientIds as $recipId) {
            try {
                NotifHelper::send(
                    $recipId, $userId,
                    'message', Auth::user()->name . ' mengirim pesan',
                    $request->body,
                    url('/dia/conversation/' . $id)
                );
            } catch (\Throwable $e) {}
        }

        // Detect @mentions — invite users not yet in conversation
        $this->processMentions($request->body, $conversation, $userId);

        return response()->json([
            'success' => true,
            'message' => [
                'id'      => $message->id,
                'body'    => $message->body,
                'user_id' => $userId,
                'name'    => Auth::user()->name,
                'avatar'  => Auth::user()->avatar,
                'time'    => $message->created_at->diffForHumans(),
                'mine'    => true,
            ]
        ]);
    }

    public function pollMessages(Request $request, $id)
    {
        $userId       = Auth::id();
        $conversation = Conversation::findOrFail($id);

        $joinedAt = $this->resolveConversationAccess($conversation, $userId);

        $afterId  = (int) ($request->after ?? 0);
        $query    = Message::with('user')
            ->where('conversation_id', $id)
            ->where('id', '>', $afterId)
            ->orderBy('id');

        if ($joinedAt) $query->where('created_at', '>=', $joinedAt);

        $messages = $query->get()->map(fn($msg) => [
            'id'      => $msg->id,
            'body'    => $msg->body,
            'user_id' => $msg->user_id,
            'name'    => $msg->user->name,
            'avatar'  => $msg->user->avatar,
            'time'    => $msg->created_at->diffForHumans(),
            'mine'    => ($msg->user_id === $userId),
        ]);

        return response()->json(['messages' => $messages]);
    }

    public function acceptInvite($id)
    {
        $invite = ConversationInvite::where('id', $id)
            ->where('to_user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $invite->update(['status' => 'accepted', 'joined_at' => now()]);

        // Mark the invite notification as read
        try {
            AppNotification::where('user_id', Auth::id())
                ->where('url', url('/dia/invite/' . $id . '/accept'))
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } catch (\Throwable $e) {}

        return redirect()->route('dia.conversation', $invite->conversation_id);
    }

    public function declineInvite($id)
    {
        $invite = ConversationInvite::where('id', $id)
            ->where('to_user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $invite->update(['status' => 'declined']);

        try {
            AppNotification::where('user_id', Auth::id())
                ->where('url', url('/dia/invite/' . $id . '/accept'))
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } catch (\Throwable $e) {}

        return response()->json(['success' => true]);
    }

    public function pollGroupMessages(Request $request, $id)
    {
        $userId = Auth::id();
        $group  = Group::findOrFail($id);

        if (!$group->isMember($userId)) abort(403);

        $afterId  = (int) ($request->after ?? 0);
        $messages = GroupMessage::with('user')
            ->where('group_id', $id)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(fn($msg) => [
                'id'      => $msg->id,
                'body'    => $msg->body,
                'user_id' => $msg->user_id,
                'name'    => $msg->user->name,
                'avatar'  => $msg->user->avatar,
                'time'    => $msg->created_at->diffForHumans(),
                'mine'    => ($msg->user_id === $userId),
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function group($id)
    {
        $userId = Auth::id();
        $group  = Group::with(['members.user', 'messages.user', 'creator'])->findOrFail($id);

        if (!$group->isMember($userId)) abort(403);

        $conversations = $this->userConversations($userId);
        $groups        = $this->userGroups($userId);
        $users         = $this->sortedUsers($userId);
        $unreadCounts  = $this->getUnreadCounts($conversations, $userId);

        return view('fanbase.dia', compact(
            'conversations', 'groups', 'users', 'group', 'unreadCounts'
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
            'description' => $request->description ?? null,
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

        // Notify all other group members
        foreach ($group->members as $gm) {
            if ($gm->user_id === $userId) continue;
            try {
                NotifHelper::send(
                    $gm->user_id, $userId,
                    'message', Auth::user()->name . ' di grup ' . $group->name,
                    $request->body,
                    url('/dia/group/' . $id)
                );
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id'      => $message->id,
                'body'    => $message->body,
                'user_id' => $userId,
                'name'    => Auth::user()->name,
                'avatar'  => Auth::user()->avatar,
                'time'    => $message->created_at->diffForHumans(),
                'mine'    => true,
            ]
        ]);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    private function resolveConversationAccess(Conversation $conv, int $userId): ?\Carbon\Carbon
    {
        // Original member
        if ($conv->user_one_id === $userId || $conv->user_two_id === $userId) {
            return null; // no join restriction
        }

        // Invited and accepted member
        $invite = ConversationInvite::where('conversation_id', $conv->id)
            ->where('to_user_id', $userId)
            ->where('status', 'accepted')
            ->first();

        if (!$invite) abort(403);

        return $invite->joined_at;
    }

    private function conversationParticipants(Conversation $conv, int $senderUserId): array
    {
        $participants = [];

        // Original two users (except sender)
        foreach ([$conv->user_one_id, $conv->user_two_id] as $uid) {
            if ($uid !== $senderUserId) $participants[] = $uid;
        }

        // Accepted invited members
        $invitedIds = ConversationInvite::where('conversation_id', $conv->id)
            ->where('status', 'accepted')
            ->where('to_user_id', '!=', $senderUserId)
            ->pluck('to_user_id')
            ->toArray();

        return array_unique(array_merge($participants, $invitedIds));
    }

    private function processMentions(string $body, Conversation $conv, int $senderUserId): void
    {
        try {
            // Match @Word or @Word_Word patterns
            preg_match_all('/@([\w]+)/', $body, $matches);
            if (empty($matches[1])) return;

            $participants = array_merge(
                [$conv->user_one_id, $conv->user_two_id],
                ConversationInvite::where('conversation_id', $conv->id)
                    ->where('status', 'accepted')
                    ->pluck('to_user_id')->toArray()
            );

            $allUsers = User::where('id', '!=', $senderUserId)->get();

            foreach ($matches[1] as $token) {
                $searchName = str_replace('_', ' ', $token);

                $matched = $allUsers->first(function ($u) use ($searchName) {
                    $firstName = explode(' ', $u->name)[0];
                    return strcasecmp($u->name, $searchName) === 0
                        || strcasecmp($firstName, $searchName) === 0;
                });

                if (!$matched) continue;
                if (in_array($matched->id, $participants)) continue;

                // Check not already invited
                $existing = ConversationInvite::where('conversation_id', $conv->id)
                    ->where('to_user_id', $matched->id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->first();
                if ($existing) continue;

                $invite = ConversationInvite::create([
                    'conversation_id' => $conv->id,
                    'from_user_id'    => $senderUserId,
                    'to_user_id'      => $matched->id,
                    'status'          => 'pending',
                ]);

                NotifHelper::send(
                    $matched->id, $senderUserId,
                    'invite',
                    Auth::user()->name . ' mengundang kamu ke obrolan',
                    'Ketuk Terima untuk bergabung',
                    url('/dia/invite/' . $invite->id . '/accept')
                );
            }
        } catch (\Throwable $e) {}
    }

    private function userConversations(int $userId)
    {
        $direct = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId);

        $invitedIds = ConversationInvite::where('to_user_id', $userId)
            ->where('status', 'accepted')
            ->pluck('conversation_id');

        if ($invitedIds->isNotEmpty()) {
            $direct->orWhereIn('id', $invitedIds);
        }

        return $direct->orderByDesc('last_message_at')->get();
    }

    private function userGroups(int $userId)
    {
        return Group::with(['members.user'])
            ->whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('last_message_at')
            ->get();
    }

    private function sortedUsers(int $excludeId)
    {
        return User::where('id', '!=', $excludeId)->get()
            ->sortByDesc(fn($u) => [
                $u->getAttribute('is_online') ? 1 : 0,
                $u->getAttribute('last_seen') ? strtotime($u->getAttribute('last_seen')) : 0,
            ])
            ->values();
    }

    private function getUnreadCounts($conversations, int $userId): array
    {
        $counts = [];
        foreach ($conversations as $conv) {
            try {
                $counts[$conv->id] = $conv->unreadCount($userId);
            } catch (\Throwable $e) {
                $counts[$conv->id] = 0;
            }
        }
        return $counts;
    }
}
