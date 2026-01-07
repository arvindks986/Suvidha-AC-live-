<?php
namespace App\Http\Controllers\Admin\Mis\Deo;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Support\Facades\Hash;
    use Validator;
    use Config;
    use \PDF;
    use MPDF;
    use App\commonModel;  
    use App\adminmodel\ACDEOModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;  
class DeoOfficerUpdateController extends Controller {
    public $base    = 'roac';
    public $folder  = 'deo';
    public $action    = 'roac/deo/';
    public $view_path = "admin.mis.deo.account";

  public function __construct(){
    $this->middleware(['auth:admin','auth']);
        $this->middleware('deo');
        $this->commonModel = new commonModel();
        $this->deomodel = new ACDEOModel();
        $this->xssClean = new xssClean;
        if(!Auth::check()){
          return redirect('/officer-login');
        }
     }

  /**
  * Show the application dashboard.
  *
  * @return \Illuminate\Http\Response
  */

   protected function guard(){
        return Auth::guard();
    }

    
      public function officerList(Request $request){
        if(Auth::check()){
               $user = Auth::user();
               $d=$this->commonModel->getunewserbyuserid($user->id);
              $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
               
               $officerlist =DB::table('officer_login')->where('st_code',$d->st_code)
               ->where('dist_no',$d->dist_no) ->whereIn('role_id', [19])->get();
               
              return view('admin.mis.deo.account.officer-details',['user_data' => $d,'ele_details' => $ele_details,'officerlist' => $officerlist]);
            }
        else {
              return redirect('/officer-login');
            }   
      }   // end officerList function  
       
    public function officerProfileUpdate(Request $request,$id='') {
       if(Auth::check()){
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);
         $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
            $decryptedid = decrypt($id);
            $getofficerdetails =DB::table('officer_login')->where('id',$decryptedid)->get();
                return view('admin.mis.deo.account.officer-profile')->with(array('user_data' => $d, 'showpage' => 'officer-profile', 'getofficerdetails' => $getofficerdetails));
            }
          else {
                return redirect('/officer-login');
                }
    } // end officerProfileUpdate function  
	
	function profilePicUpdate(Request $request){
		if(Auth::check()){
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
          $decryptedid = $user->id;

		  if($request->has('_token')) {
			  $getofficerdetails =DB::table('officer_login')->where('id',$decryptedid)->first();
			  if ($request->profileimg) {
				$filenameWithExt = $request->file('profileimg')->getClientOriginalName();
				$extension = $request->file('profileimg')->getClientOriginalExtension();
				$fileNameToStore = $user->officername . '_' . time() . '.' . $extension;
				$file_path = '';

				$file_path = '/uploads1/officer-profile/' . $fileNameToStore;
				$allow_ext = array("jpg", "png", "jpeg", "webp");
				if (!in_array($extension, $allow_ext)) {
					return Redirect::back()->with('error_msg', 'Submitted file type not allowed. select only png,jpg,jpeg.');
				}
				} else {
					if($getofficerdetails){
						$file_path = $getofficerdetails->profile_pic;
					}
				}
			$validator = Validator::make($request->all(), [
            'address1'  => 'required|regex:/(^[-0-9A-Za-z.,\/ ]+$)/',
            'address2'  => 'required|regex:/(^[-0-9A-Za-z.,\/ ]+$)/',
            'zip'       => 'required|digits:6'
			], [
				'address1.*' => 'Please Enter The Correct Address',
				'address2.*' => 'Please Enter The Correct Address',
				'zip.*'      => 'Please Enter The Correct Pincode',
			])->validate();

			$affected = DB::table('officer_login')->where('id', '=', Auth::id())->update([
				'ro_address_l1'       => $request->address1,
				'ro_address_l2'       => $request->address2,
				'ro_address_pin_code' => $request->zip,
				'profile_pic' => $file_path
			]);
			
			if($affected){
				if ($request->profileimg) {
					$request->file('profileimg')->move(public_path('/uploads1/officer-profile/'), $fileNameToStore);
				}
				if ($getofficerdetails->profile_pic) {
					
					$file_path = $getofficerdetails->profile_pic;
					@unlink(public_path($file_path));
				}
				
			
				Session::flash('success_mes', 'Officer Details has Updated successfully !');
				return redirect()->back();
			}else{
				Session::flash('error_mes', 'Details not updated, please try again !');
				return redirect()->back();
			}
			 
			
		  }
			
			
            $getofficerdetails =DB::table('officer_login')->where('id',$decryptedid)->get();
                return view('admin.mis.deo.profile-update.profile-update')->with(array('user_data' => $d, 'showpage' => 'officer-profile', 'getofficerdetails' => $getofficerdetails));
            }
          else {
                return redirect('/officer-login');
                }
	}
        
      public function updateuser(Request $request){  
        if(Auth::check()){
              $user = Auth::user();
              $uid=$user->id;
              $d=$this->commonModel->getunewserbyuserid($uid);
               $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
               $this->validate(
                $request, 
                    [
                     'name' => 'required',
                      'email' => 'required|email',
                      'Phone_no'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|numeric|digits:10',
                      'address1' => 'required',
                      'address2' => 'required',
                      'zip'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|numeric|digits:6',
                     ],
                    [
                    'name.required' => 'Please enter name', 
                     'email.required' => 'Please enter your email',
                     'email.email' => 'Please enter valid email',
                      'Phone_no.required'=>'Please enter validate mobileno',
                      'Phone_no.min'=>'Mobile Number minimum 10 digit',
                      'Phone_no.digits'=>'Mobile Number minimum 10 digit',
                      'Phone_no.numeric'=>'Please enter validate mobileno',
                      'zip.digits'=>'Zip Code minimum 6 digit',
                      'zip.numeric'=>'Please enter validate Zip Code',
                      'address1.required' => 'Please enter Address line1', 
                      'address2.required' => 'Please enter Address line2', 
                     ]);
              
               $id=$this->xssClean->clean_input(Check_Input($request->input('profileUpdate')));
               $name=$this->xssClean->clean_input(Check_Input($request->input('name')));
               $mobile=$this->xssClean->clean_input(Check_Input($request->input('Phone_no')));
               $email=$this->xssClean->clean_input(Check_Input($request->input('email')));
                $address1=$this->xssClean->clean_input(Check_Input($request->input('address1')));
               $address2=$this->xssClean->clean_input(Check_Input($request->input('address2')));
               $zip=$this->xssClean->clean_input(Check_Input($request->input('zip')));
                $date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s'); 
                $code = Hash::make(str_random(10));
                $mobile_otp =rand(100000,999999);
                $rec=getById('officer_login','id',$id);   
              $record = array(
                'name'=>$name,
                //'password'=>'',
                'Phone_no'=>$mobile,
                'email'=>$email,
                'mobile_otp' => $mobile_otp,
                'otp_time' => $currentTime,
                'auth_token' => $code,
                'ro_address_l1' => $address1,
                'ro_address_l2' => $address2,
                'ro_address_pin_code' => $zip,
             );
              $n = DB::table('officer_login')->where('id', $id)->update($record);
                $encodeid=encrypt_string($id);
                $passcreaturl = URL::to("/updateprofile/$encodeid");
              $html = "Dear $name,\n\n";
                                  $html .= "Your account has been updated in Suvidha Portal"
                                      . "Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
                                  $html .= "$passcreaturl\n\n";
                                  $html .= "OTP: $mobile_otp\n\n";
                                  $html .= "Login ID:  $rec->officername\n\n";
                                  $html .= "For verifying  your account,  kindly enter OTP $mobile_otp and this OTP has also sent on your registered mobile no.:\n\n";
                                  
                                  $html .= "Thanks & Regards,\n\n";
                                  $html .= "Suvidha Team,\n\n";

                                $html = strip_tags($html);
                                //sendotpmail($email,'UserLogin Credential',$html);  
                                //mail ($email, 'UserLogin Credential',$html,'suvidha.eci.gov.in');
                            
                
          if($mobile!=""){
            $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." and Login ID: ".$rec->officername." for SUVIDHA Portal.Activation link has been sent on your email. ".$passcreaturl." Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
             //$response = SmsgatewayHelper::gupshup($mobile,$mob_message);
            }   
 
                  \Session::flash('success_mes', 'officer profile updated successfully');  //
                   return Redirect::to('/acdeo/mis/officer-details');
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function   
  
}  // end class