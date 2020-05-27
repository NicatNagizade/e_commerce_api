<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    const INFO = 0;
    const DİSCOUNT = 1;

    protected $casts = [
        'seen' => 'date:Y-m-d H:i:s',
    ];

    public static function send(int $user_id,array $message = [],array $related = [])
    {
        $title = $message['title'];
        $content = $message['content'];
        $note = $message['note'] ?? null;
        $type = $related['type'] ?? static::INFO;
        $related_id = $related['id'] ?? 0;

        $notification = new static;
        $notification->sender_id = auth()->id();
        $notification->user_id = $user_id;
        $notification->type = $type;
        $notification->related_id = $related_id;
        $notification->title = $title;
        $notification->content = $content;
        $notification->note = $note;
        $notification->created_at = now();
        $notification->save();
    }
}
