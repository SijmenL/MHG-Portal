<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotificationSettings;
use Illuminate\Support\Facades\Mail;

use App\Mail\plainMail;


class NotificationController extends Controller
{
    public function sendNotificationToRole($role_name, $notification_type, $data = array())
    {
        switch($role_name){
            case 'administratie':
                switch($notification_type){
                    case 'contact_message':

                        $users = User::whereHas('roles', function($q){
                            $q->where('name', 'Administratie');
                        })->get();
                        
                        $notification_settings = new UserNotificationSettings();
                        $user_ids_app = array();
                        $user_ids_mail = array();
                        foreach($users as $user)
                        {
                            // check if app is allowed to send notification
                            $app_notification_disabled = $notification_settings->checkNotificationOff($user->id, 'app_'.$notification_type);
                            $mail_notification_disabled = $notification_settings->checkNotificationOff($user->id, 'mail_'.$notification_type);

                            if(!$app_notification_disabled)
                            {
                                $user_ids_app[] = $user->id;
                            }

                            if(!$mail_notification_disabled)
                            {
                                $user_ids_mail[] = $user->id;
                            }
                        }

                        $message = 'Er is een nieuw bericht ontvangen via het contactformulier.';

                        $this->sendBulkAppNotification($user_ids_app, $message);
                        $this->sendBulkMailNotification($user_ids_mail, 'new_contact_message', $data['message']);	

                        break;
                    case 'new_registration':
                        // $users = User::whereHas('roles', function($q){
                        //     $q->where('name', 'Administratie');
                        // })->get();
                        
                        // $notification_settings = new UserNotificationSettings();
                        // $user_ids_app = array();
                        // $user_ids_mail = array();
                        // foreach($users as $user)
                        // {
                        //     // check if app is allowed to send notification
                        //     $app_notification_disabled = $notification_settings->checkNotificationOff($user->id, 'app_'.$notification_type);
                        //     $mail_notification_disabled = $notification_settings->checkNotificationOff($user->id, 'mail_'.$notification_type);

                        //     if(!$app_notification_disabled)
                        //     {
                        //         $user_ids_app[] = $user->id;
                        //     }

                        //     if(!$mail_notification_disabled)
                        //     {
                        //         $user_ids_mail[] = $user->id;
                        //     }
                        // }

                        // $message = 'Er is een nieuwe inschrijving ontvangen.';

                        // $this->sendBulkAppNotification($user_ids_app, $message);
                        // $this->sendBulkMailNotification($user_ids_mail, 'new_registration', $data['name']);	
                        break;
                }
                break;
        }
    }

    public function sendBulkAppNotification($user_ids, $message)
    {
        
    }

    public function sendBulkMailNotification($user_ids, $template, $message)
    {
        
    }



}