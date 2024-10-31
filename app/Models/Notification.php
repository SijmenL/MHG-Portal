<?php

namespace App\Models;

use App\Mail\accountChange;
use App\Mail\adminMail;
use App\Mail\newPost;
use App\Mail\passwordChange;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

    public function sendNotification($senderId, $recieverIds, $displayText, $link, $location, $notificationType, $relevant_id = null)
    {
        foreach ($recieverIds as $recieverId) {

            $user = User::find($recieverId);

            if ($user->getNotificationSetting('app_' . $notificationType)) {
                // Create a notification within portal
                $notification = new Notification();
                $notification->sender_id = $senderId;
                $notification->receiver_id = $recieverId;
                $notification->display_text = $displayText;
                $notification->link = $link;
                $notification->location = $location;
                $notification->seen = false;
                $notification->save();
            }


            if ($user->getNotificationSetting('mail_' . $notificationType)) {
                if (isset($senderId)) {
                    $sender = User::find($senderId);
                }

                $isDolfijn = false;

                if ($user->roles->contains('role', 'dolfijn')) {
                    $isDolfijn = true;
                }

                if ($user) {
                    // Gather the necessary data for the email
                    $data = [
                        'reciever_name' => $user->name,
                        'message' => $displayText,
                        'link' => $link,
                        'location' => $location,
                        'sender_dolfijnen_name' => $senderId ? $sender->dolfijnen_name : null,
                        'reciever_is_dolfijn' => $senderId && $isDolfijn ? $isDolfijn : null,
                        'sender_full_name' => $senderId ? $sender->name . " " . ($sender->infix ?? '') . " " . $sender->last_name : null,
                        'email' => $user->email,
                        'relevant_id' => $relevant_id
                    ];

                    switch ($notificationType) {
                        case 'account_change':
                            Mail::to($data['email'])->send(new accountChange($data));
                            break;

                        case 'password_change':
                            Mail::to($data['email'])->send(new passwordChange($data));
                            break;

                        case 'new_post':
                            Mail::to($data['email'])->send(new newPost($data));
                            break;
                    }


                }
            }
        }
    }
}
