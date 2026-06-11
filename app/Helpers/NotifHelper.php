<?php

namespace App\Helpers;

use App\Models\AppNotification;

class NotifHelper
{
    public static function send($toUserId, $fromUserId, $type, $title, $body = null, $url = null)
    {
        if ($toUserId === $fromUserId) return; // jangan notif diri sendiri

        $icons = [
            'like'    => '♥',
            'comment' => '💬',
            'reply'   => '↩️',
            'mention' => '@',
            'message' => '📩',
            'post'    => '📝',
            'invite'  => '🤝',
        ];

        AppNotification::create([
            'user_id'      => $toUserId,
            'from_user_id' => $fromUserId,
            'type'         => $type,
            'title'        => $title,
            'body'         => $body,
            'url'          => $url,
            'icon'         => $icons[$type] ?? '🔔',
        ]);
    }
}