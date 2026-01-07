<?php

namespace App\Http\Middleware;
use Illuminate\Contracts\Encryption\DecryptException;
use Closure,Response,Redirect,Crypt,Request;
use App\Http\Controllers\API\ResponseController;
//INCLUDING CLASSES
use App\Classes\Secure;


class Encrypt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function __construct() {
        $this->ResponseMethod = new ResponseController;
        $this->okStatus = "success";
        $this->errStatus = "error";
        $this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
        $this->ok_response = $this->ResponseMethod::HTTP_ACCEPTED; 
        
        $this->cipher = new Secure();      

    }

    public function handle($request, Closure $next)
    {   
        try {   
                
                $input = $request->except(['file']);

                    array_walk_recursive($input, function(&$input) {
                        
                        $input = $this->cipher->encrypt_decrypt('decrypt',$input);
                        //$input = $this->cipher->encrypt($input);
                       //$input = Crypt::decryptString($input);   
                       //$input = Crypt::encryptString($input);
                        //$input = decrypt($input);
                        //dd($input);
                      
                     });                
                
                $request->merge($input);
                return $next($request);

            } catch (\Exception $e) {
                    //return response()->json(encrypt(['error' => true, 'message' => 'Inputs data manimulated. Re-enter correct data.']), 404);
                $data = 'Inputs data manimulated. Re-enter correct data.';
                return $this->ResponseMethod->get_http_response($this->errStatus, $data, $this->bad_response);
                }   
        
    }
    
//    public function terminate($request, $response)
//    {
//        $input = $request->all();
//    
//        array_walk_recursive($input, function(&$input) {
//            $input = Crypt::decryptString($input);            
//        });
//        $request->merge($input);
//        return $next($request);
//    }
    
}
