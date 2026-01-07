<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//classes
use Carbon\Carbon;
use DB, Validator, Config, PDF, QrCode, Session, Auth, Input, Redirect, Hash, Response, Crypt;
use App\Classes\Secure;
//helpers
//use App\Helpers\ResponseHelper;

//models
use App\adminmodel\{Addsearches, OfficerLogin,CandidateModel,CEOModel};
use App\preMedia\adminmediamodel\PreMediaApplicationModel;
use App\Helpers\SmsgatewayHelper;
use App\commonModel;

class ResponseController extends Controller
{
  const HTTP_OK = 200;
  const HTTP_CREATED = 201;
  const HTTP_ACCEPTED = 202;
  const HTTP_TEMPORARY_REDIRECT = 307;
  const HTTP_PERMANENTLY_REDIRECT = 308;
  const HTTP_BAD_REQUEST = 400;
  const HTTP_UNAUTHORIZED = 401;
  const HTTP_NOT_FOUND = 404;
  const HTTP_METHOD_NOT_ALLOWED = 405;
  const HTTP_REQUEST_TIMEOUT = 408;
  const HTTP_INTERNAL_SERVER_ERROR = 500;
  const HTTP_NOT_IMPLEMENTED = 501;
  const HTTP_BAD_GATEWAY = 502;

  //response from the http request starts
  public function get_http_response( string $status = null, $data = null, $response ){
    
    $cipher = new Secure();

    $data = array("status" => $status,"data" => $data);

    /*return response()->json([
        
        //'status' => $status, 
        //'data' =>   $cipher->encrypt_decrypt('encrypt',json_encode($data)),
        //$cipher->encrypt_decrypt('encrypt',json_encode($data)),
        'data' => $data,

    ], $response);*/

    return response()->json($cipher->encrypt_decrypt('encrypt',json_encode($data)),$response);
    }
  //response from the http request ends
public function get_http_response_error( string $status = null, $data = null, $response ){
    


    $cipher = new Secure();

    $data = array("status" => $status,"errorMessage" => $data);
    
    return response()->json($cipher->encrypt_decrypt('encrypt',json_encode($data)),$response);
    }

  //checking user is authenticated or not starts
  public function get_user_token( $user, string $token_name = null ) {
      return $user->createToken($token_name)->accessToken; 
      
  }
  //checking user is authenticated or not ends
  
  
}
