<?php
    namespace App\Http\Controllers\Admin\Mis\Roac\Profile;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Validator;
    use Session;
    use Illuminate\Support\Facades\Hash;
    use App\commonModel;  
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Crypt;
    use App\models\Admin\Ro\Profile\ProfileModel;
class RoAcProfileUpdateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $view_path = "admin.mis.roac.account";

    public function __construct(){       
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
    }

    public function get_profile(){
        $user_data = Auth::user();
        return view($this->view_path.'.officer-profile',['user_data'=>$user_data]);
    }

    public function update_profile(Request $request) {

        if($request->has('_token')) {
			$user = Auth::user();
			  $getofficerdetails =DB::table('officer_login')->where('id',$user->id)->first();
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
        Session::flash('success_mes', 'Officer Details has Updated successfully !'); 
        return redirect()->back();
    }

}