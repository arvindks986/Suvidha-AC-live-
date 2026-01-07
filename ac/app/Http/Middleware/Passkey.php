<?php

namespace App\Http\Middleware;
use App\Http\Controllers\API\BaseController as BaseController;

use Closure,Config;

class Passkey  extends BaseController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Config::get('api_setting.pass_base_auth')) { 
            $pass_key_by_user = $request->pass_key;
            #  epic_no is used to genrate pass key
            $key  = "ABCD1234#123521GISTECIKEY";
            
            if($request->self_epic_no != null){
                $epic_no = $request->self_epic_no;
            }elseif($request->epic_no != null) {
                $epic_no = $request->epic_no;
            }elseif($request->mobile_number != null){
                $mobile_no = $request->mobile_number;
            }
        
            if(!empty($epic_no)) {
                $hash = strtoupper(hash('sha512', $epic_no.$key));
                $checking_status = strcmp($hash, strtoupper($pass_key_by_user));
                if($checking_status != 0){
                    return $this->sendError('Please Enter a valid pass key.',(object)[] ,$this->successStatus);
                }
            }
			elseif(!empty($mobile_no)) {
                $hash = strtoupper(hash('sha512', $mobile_no.$key)); 
                $checking_status = strcmp($hash, $pass_key_by_user);
                if($checking_status != 0){
                    return $this->sendError('Please Enter a valid pass key.',(object)[] ,$this->successStatus);
                }
            }
        }
        return $next($request);
    }
}
