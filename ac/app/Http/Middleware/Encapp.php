<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\User;
use App\Classes\xssClean;
use Illuminate\Support\Facades\Validator;
use App\Helpers\SmsgatewayHelper;

class Encapp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUrl = $request->url();
        try {
            if (stristr($currentUrl, "v5")) {

                $expA = explode("/", $currentUrl);
                $l = array_pop($expA);
                $checkMethods = [
                    "login",
                    "verifyuser",
                    "verify-otp",
                    "resendOtp",
                    "singupmobile",
                    "singuppsssword",
                    "logout",
                    // "getparty",
                    //"get_state",
                    "getdist",
                    "getAc",
                    "get_police_station",
                    "get_location",
                    "addprofile",
                    //"getpermissiondata",
                    "getSelectpermission_doc",
                    "getpolldays",
                    "permisson_apply",
                    // "AllPermissionRequest",
                    "getPermissionDetails",
                    "getnominationlist",
                    "nominationstatus"

                ];
                if (in_array($l, $checkMethods)) {

                    //$r_data = SmsgatewayHelper::encrypt_data("ed8cf08edc53edfr", "3436jnha98fab441", $request->all()); 
                    //return response()->json($r_data, 200);
                    // echo "<pre>"; print_r($request->all()); exit; 

                    $rules = [
                        //'access_token' => 'required',
                        "data" => 'required',
                        "sig" => 'required',


                    ];


                    $validator = Validator::make($request->all(), $rules);
                    if ($validator->fails()) {
                        $messages = $validator->messages();
                        //echo "<pre>"; print_r($messages); exit;
                        return response()->json(['success' => false, 'message' => "Invalid data provided!"], 200);
                    }



                    $r_data = SmsgatewayHelper::decrypt_data("ed8cf08edc53edfr", "3436jnha98fab441", $request->sig, $request->data);

                    // $r_data = SmsgatewayHelper::decrypt_data("5b364304ea32b27d","7846529b7ea5c9f3", $request->sig, $request->data);
                    // echo "<pre>"; print_r($r_data); exit;

                    if (!empty($r_data)) {
                        foreach ($r_data as $rkey => $rvalue) {
                            $request->request->add([$rkey => $rvalue]);
                        }
                        unset($r_data["data"], $r_data["sig"]);
                    }
                }
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "status" => false,
                    "error" => "Something went wrong"
                ],
                200
            );
        }

        return $next($request);
    }
}