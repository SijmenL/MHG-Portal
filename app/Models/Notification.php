<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'display_text',
        'link',
        'seen',
        'sender_id',
        'reciever_id'
    ];

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function sendNotification($senderId, $recieverIds, $displayText, $link) {
        foreach ($recieverIds as $recieverId) {
            $notification = new Notification();
            $notification->sender_id = $senderId;
            $notification->receiver_id = $recieverId;
            $notification->display_text = $displayText;
            $notification->link = $link;
            $notification->seen = false;
            $notification->save();
        }

    }
}
