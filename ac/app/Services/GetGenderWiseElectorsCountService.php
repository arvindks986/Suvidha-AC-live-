<?php

namespace App\Services;

use App\models\Admin\ElectorModel;
use App\models\Admin\polling_station\PollingStationModel;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetGenderWiseElectorsCountService
{
    public static $api_key = 'GARUDA-5AC822DA-AA04-4291-96A3';
    public static $client_id = 'GARUDA';
    public static $secure_key = 'qwYg@nF2@$Aeh*dnW!Fsf#Jg';
    public static $endpoint = 'https://eronetservices2.ecinet.in/api/ERONET/GetGenderWiseElectorsCount';

    /**
     * 
     * @params
     * PollingStationModel  $pollingStation
     */
    public static function getDataForPSFromEROnet(PollingStationModel $pollingStation){
        try {
            /*Config::set('database.connections.mysql.host', '10.247.137.49'); 
            Config::set('database.connections.mysql.database', 'suvidha_ac_2022_03_e13'); 
            Config::set('database.connections.mysql.username', 'suvidhaapp'); 
            Config::set('database.connections.mysql.password', 'P7$b&n#367BYaRt91'); 
            DB::reconnect('mysql');
            DB::purge('mysql');
            DB::setDefaultConnection('mysql');*/
            $clientHash = self::getClientHash($pollingStation->ST_CODE,$pollingStation->AC_NO, $pollingStation->PART_NO);
            
            $curl = curl_init(self::$endpoint);
            curl_setopt($curl, CURLOPT_URL, self::$endpoint);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
            "Content-Type: application/json",
            "X-API-KEY: ".self::$api_key,
            "Hash-Value: ".$clientHash['HashValue'],
            "Accept: application/json",
            "Cookie: Blonet=426c6f6e65742d7365727665722d31",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $clientHashCode = $clientHash['ClientHashCode'];
            $client_id = self::$client_id;
            $data = <<<DATA
            {
            "ST_CODE":"$pollingStation->ST_CODE",
            "AC_NO":$pollingStation->AC_NO,
            "PART_NO":$pollingStation->PART_NO,
            "CLIENT_ID":"$client_id",
            "CLIENT_HASHCODE":"$clientHashCode"
            }
            DATA;
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);
            $jsonResponse = json_decode($response);
            if (isset($jsonResponse->STATUS) && $jsonResponse->STATUS == "SUCCESS" && $jsonResponse->MESSAGE == "SUCCESS") {
                $pollingStation->electors_male = $jsonResponse->RESULT->MALES_COUNT;
                $pollingStation->electors_female = $jsonResponse->RESULT->FEMALES_COUNT;
                $pollingStation->electors_other = $jsonResponse->RESULT->THIRD_GENDER_COUNT;
                $pollingStation->electors_total = $jsonResponse->RESULT->TOTAL_ELECTORS_COUNT;
                $pollingStation->is_fecthed_from_eronet = 1;
                $pollingStation->fetched_at = Carbon::now()->format('Y-m-d H:i:s');
                
                $pollingStation->save();
                Log::info("### PS Electors Data is Fetch from EROnet is DONE for STATE Code: ".$pollingStation->ST_CODE." | AC NO:".$pollingStation->AC_NO." | PART NO:".$pollingStation->PART_NO ." | AT .".$pollingStation->fetched_at);
            }else{
                $pollingStation->is_fecthed_from_eronet = 2;
                $pollingStation->fetched_at = Carbon::now()->format('Y-m-d H:i:s');
                $pollingStation->save();
                throw new Exception($response);
            }            
        } catch (\Exception $e) {
            Log::error($e->getMessage() ." ".$e->getCode());
            Log::debug("### PS Electors Data is Fetch from EROnet is Failed for STATE Code: ".$pollingStation->ST_CODE." | AC NO:".$pollingStation->AC_NO." | PART NO:".$pollingStation->PART_NO);
        }
    }

    public static function getClientHash($st_code, $ac_no, $part_no = null){
        $randomNumber = rand(10, 1000);

        $now = new DateTime('now');
        $now->setTimezone(new DateTimeZone("UTC"));

        $date = new DateTime('1970-01-01 01:00:00');
        $date->setTimezone(new DateTimeZone("UTC"));

        $timestamps = $now->getTimestamp() - $date->getTimestamp();
        //header Hash-Value
        $headerHashValue = base64_encode($timestamps . "&" . $randomNumber); 
        if ($part_no != null) {
            $keyCode = $st_code.$ac_no.$part_no.$timestamps.$randomNumber.self::$secure_key;// ALL
        }else{
            $keyCode = $st_code.$ac_no.$timestamps.$randomNumber.self::$secure_key;// ALL
        }
        //CLIENT_HASHCODE
        $clientHash = strtoupper(hash('sha512', $keyCode)); 

        return ["HashValue"=>$headerHashValue, "ClientHashCode"=>$clientHash];
    }

    /**
     * 
     * @params
     * ElectorModel  $electorModel
     */
    public static function getDataForACFromEROnet(ElectorModel $elector){
        try {
     /*       Config::set('database.connections.mysql.host', '10.247.137.49'); 
            Config::set('database.connections.mysql.database', 'suvidha_ac_2022_03_e13'); 
            Config::set('database.connections.mysql.username', 'suvidhaapp'); 
            Config::set('database.connections.mysql.password', 'P7$b&n#367BYaRt91'); 
            DB::reconnect('mysql');
            DB::purge('mysql');
            DB::setDefaultConnection('mysql');*/
            $clientHash = self::getClientHash($elector->st_code,$elector->ac_no);
            
            $curl = curl_init(self::$endpoint);
            curl_setopt($curl, CURLOPT_URL, self::$endpoint);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
            "Content-Type: application/json",
            "X-API-KEY: ".self::$api_key,
            "Hash-Value: ".$clientHash['HashValue'],
            "Accept: application/json",
            "Cookie: Blonet=426c6f6e65742d7365727665722d31",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $clientHashCode = $clientHash['ClientHashCode'];
            $client_id = self::$client_id;
            $data = <<<DATA
            {
            "ST_CODE":"$elector->st_code",
            "AC_NO":$elector->ac_no,
            "CLIENT_ID":"$client_id",
            "CLIENT_HASHCODE":"$clientHashCode"
            }
            DATA;
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);
            $jsonResponse = json_decode($response);
            if (isset($jsonResponse->STATUS) && $jsonResponse->STATUS == "SUCCESS" && $jsonResponse->MESSAGE == "SUCCESS") {
                $elector->electors_male = $jsonResponse->RESULT->MALES_COUNT;
                $elector->electors_female = $jsonResponse->RESULT->FEMALES_COUNT;
                $elector->electors_other = $jsonResponse->RESULT->THIRD_GENDER_COUNT;
                $elector->electors_total = $jsonResponse->RESULT->TOTAL_ELECTORS_COUNT;
                $elector->is_fecthed_from_eronet = 1;
                $elector->fetched_at = Carbon::now()->format('Y-m-d H:i:s');
                
                $elector->save();
                Log::info("### AC Electors Data is Fetch from EROnet is DONE for STATE Code: ".$elector->st_code." | AC NO:".$elector->ac_no);
            }else{
                $elector->is_fecthed_from_eronet = 2;
                $elector->fetched_at = Carbon::now()->format('Y-m-d H:i:s');
                $elector->save();
                throw new Exception($response);
            }            
        } catch (\Exception $e) {
            Log::error($e->getMessage() ." ".$e->getCode());
            Log::debug("### AC Electors Data is Fetch from EROnet is Failed for STATE Code: ".$elector->st_code." | AC NO:".$elector->ac_no);
        }
    }
}
