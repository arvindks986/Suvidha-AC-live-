<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\User;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;

class suvidhaapiauth
{
    protected $gcmkey = "ed8cf08edc53edfr";
   protected $gcmiv = "3436jnha98fab441";
  
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $xss = new xssClean();
        $header = $request->header('Authorization-Token');
        $exp = explode(" ", $header);
         $token = $exp[1] ?? "";
         
        if(empty($token)){
             $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ['status' => false, 'error' => 'Invalid token']);
                return response()->json($r_data, 200);
           // return response()->json(['status' => false, 'error' => 'Invalid token'], 200);
        }
        $user = User::where('access_token', '=', $xss->clean_input(trim($token)))->first();
        if(!$user){
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ['status' => false, 'error' => 'Invalid token']);
                return response()->json($r_data, 200);
           // return response()->json(['status' => false, 'error' => 'Invalid token'], 200);
        }


        $token_expires_at = strtotime($user->token_expires_at);
        $current_time = time();
       //dd($current_time - $token_expires_at);
        $expiry_time = 48 * 60 * 60; // 48 hours in seconds

        // Check if the token has expired
        if (($current_time - $token_expires_at) > $expiry_time) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ['status' => false, 'error' => 'Invalid token', 'access_token' => false]);
                return response()->json($r_data, 200);
        }
           

        $request->request->add(["access_token" => $token]);
        return $next($request);
    }
}
