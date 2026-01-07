<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class LogNotification
{
    public static function LogInfo($data)
    {

        self::removeExtraLogs();
        if (!empty($data)) {

            try {
                if (!empty($data["MobNo"])) {
                    $data["MobNo"] = str_replace(" ", "", $data["MobNo"]);
					$user = substr($data["MobNo"], 0, -3);					
					$user = $user.'xxx';
                }

                $message['eventTime'] = date('Y-m-d H:i:s');
                $message['srcIp'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
                $message['MobNo'] = $data["MobNo"];
                $message['applicationType'] = $data['applicationType'];
                $message['Module'] = $data['Module'];
                $message['TransectionType'] = $data['TransectionType'];
                $message["serverAdd"] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
                $message['TransectionAction'] = $data['TransectionAction'];
                $message['TransectionStatus'] = $data['TransectionStatus'];
                $message['LogDescription'] = $data['LogDescription'];

                //$keys = array_keys($message);
                $Values = array_values($message);
                $Values = implode(" | ", $Values);

                $Errormessage = $Values;

                $path = storage_path() . '/logger/';
                if (!file_exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $path = $path . "suvidha-" . date("d-m-Y") . '.log';

                $myfile = file_put_contents($path, $Errormessage . PHP_EOL, FILE_APPEND | LOCK_EX);
            } catch (\Exception $e) {
                // $data =  $e->getMessage();
                // Log::info($data);

            }
        }
    }


    public static function removeExtraLogs()
    {

        $dirpath = storage_path() . '/logger/' . "*.log";
        $files = array();
        $files = glob($dirpath);

        usort($files, function ($x, $y) {
            return filemtime($x) < filemtime($y);
        });

        $length = count($files);
        if ($length < 7) {
            return;
        }

        for ($i = $length; $i > 7; $i--) {
            //echo "Erase : " .$files[$i-1]; 
            unlink($files[$i - 1]);
        }
    }
}
