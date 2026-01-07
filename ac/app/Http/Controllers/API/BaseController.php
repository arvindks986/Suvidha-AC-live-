<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Config,Crypt;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        
        if(Config::get('api_setting.encryption')) {
            return response()->json(Crypt::encryptString(json_encode($response)), 200);
        }else{
            return response()->json(($response), 200);
        }
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages, $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data'    => $errorMessages
        ];
        // if(empty($errorMessages)){
        //     $response['data'] = $errorMessages;
        // }
         if(Config::get('api_setting.encryption')) {
            return response()->json(Crypt::encryptString(json_encode($response)), $code);
        }else{
            return response()->json(($response), $code);
        }
    }
}
