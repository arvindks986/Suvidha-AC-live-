<?php namespace App\Http\Controllers\Admin\Nfd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB, Validator, Config, Session, Common, Response;
use \PDF, Auth;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;

class OtpController extends Controller {

  public function __construct(){  
    $this->xssClean = new xssClean;
  }

  public function get_otp(Request $request){
    $data               = [];
    $redirect_url       = url('nfd/nomination/list');
    $data['otp_form']   = Common::get_otp('nfd',$redirect_url,$request);
    $data['user_data']  = Auth::user();
    return view('admin.nfd.get_otp', $data);
  }

}  // end class