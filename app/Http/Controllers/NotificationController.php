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
        
        // set default values
        $app_message = 'Er is een nieuwe notificatie ontvangen.';
        $mail_template_type = 'default';	


        switch($notification_type){
            // alleen voor administratie!
            case 'contact_message':

                $app_message = 'Er is een nieuw bericht ontvangen via het contactformulier.';
                $mail_template_type = 'new_contact_message';

                break;
            // alleen voor administratie!
            case 'new_registration':

                $app_message = 'Er is een nieuwe inschrijving ontvangen.';
                $mail_template_type = 'new_registration';

                break;
        }

        // check if role exists
        $role = Role::where('name', $role_name)->first();
        if(!$role)
        {
            return;
        }

        // get users of role
        $users = User::whereHas('roles', function($q){
            $q->where('name', $role_name);
        })->get();

        // get notification settings
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


        // send notifications
        $this->sendBulkAppNotification($user_ids_app, $message);
        $this->sendBulkMailNotification($user_ids_mail, $mail_template_type, $data);	
    }

    public function sendBulkAppNotification($user_ids, $message)
    {
        
    }

    public function sendBulkMailNotification($user_ids, $template, $data)
    {
        $user_ids = array_unique($user_ids);



        $mailSender = new mailSender($template);
        foreach($user_ids as $user_id)
        {
            $user = User::find($user_id);
            $mailSender->setRecieverName($user->name);
            $mailSender->setData($data);
            $mailSender->setSubject('MHG Portal Notificatie');
            $mail = $mailSender->generateMail();
            // Mail::to($user->email)
            //     ->send($mail);
        }


    }



}