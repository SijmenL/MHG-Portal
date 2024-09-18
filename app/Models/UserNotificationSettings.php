<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSettings extends Model
{
    protected $table = 'user_notification_settings';

    protected $fillable = [
        'user_id',
        'type',
        'on_status'
    ];

    public function getUserNotifications(int $userId)
    {
        // return array of notification_types where userid = $userId and on_status == true
        return $this->where('user_id', $userId)->where('on_status', true)->pluck('type')->toArray();
    }

    public function checkNotificationOff(int $userId, string $type)
    {
        return $this->where('user_id', $userId)->where('type', $type)->where('on_status', 0)->exists();
    }

}
