<?php

namespace App\Helpers;

use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use App\Http\Controllers\API\NotificationController;
use App\Notification;
use Carbon\Carbon;

class SendNotification
{
   public static function send_notification_fcm($title, $message, $fcm_id, $type, $authority_login_id){    
      
        //CURL METHOD FOR SENDING NOTIFICTION TO MOBILE STARTS
       /* $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array (
                'registration_ids' => $fcm_id,
                'notification'  => array (
                        "body"  => $message,
                        "title" => $title
                )
        );
        $fields = json_encode ( $fields );
        $headers = array (
                'Authorization: key=' . "AIzaSyA35mgXSz16ioGBDk_LW085fxhL77t7CH8",
                'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        //SAVING NOTIFICATION DATA IN TABLE STARTS
        $noti=new Notification;
        $noti->authority_login_id=$authority_login_id;
        $noti->text=$message;
        $noti->title=$title;
        $noti->fcm_id=$fcm_id;
        $noti->created_at=Carbon::now();
        $noti->updated_at=Carbon::now();
        $noti->save();

        //SAVING NOTIFICATION DATA IN TABLE ENDS


        $result = curl_exec ( $ch );
        dd($result);
        curl_close ( $ch );*/

        //CURL METHOD FOR SENDING NOTIFICTION TO MOBILE ENDS

        $url = 'https://fcm.googleapis.com/fcm/send';

        $now = Carbon::now();

        $date =  strtotime($now);
        //$date = Carbon::parse($now)->format('Y-m-d h:i:sa');
       
        $fields = array (
                    'to' => $fcm_id,
                    'data'  => array (
                            "message"  => $message,
                            "title" => $title,
                            "type"  =>  $type
                            //'date'  => date('Y-m-d H:i:s')
                            //'date'  => $date
                    )
            );

        $fields = json_encode ( $fields );
       
       //dd($fields); 

        $headers = array (
                'Authorization: key=' . "AIzaSyA35mgXSz16ioGBDk_LW085fxhL77t7CH8",
                'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        $noti=new Notification;
        $noti->authority_login_id=$authority_login_id;
        $noti->text=$message;
        $noti->title=$title;
        $noti->fcm_id=$fcm_id;
        $noti->created_at=Carbon::now();
        $noti->updated_at=Carbon::now();
        $noti->save();

        $result = curl_exec ( $ch );
        //dd($result);
        curl_close ( $ch );

    }
}
