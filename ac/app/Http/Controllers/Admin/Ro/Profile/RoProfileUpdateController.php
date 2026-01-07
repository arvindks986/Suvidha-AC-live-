<?php
    namespace App\Http\Controllers\Admin\Ro\Profile;
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
class RoProfileUpdateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $view_path = "admin.ac.ro.profile";

    public function __construct(){       
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
    }

    public function get_profile(){
        $user_data = Auth::user();
        return view($this->view_path.'.officer-profile',['user_data'=>$user_data]);
    }

    public function update_profile(Request $request) {

        $validator = Validator::make($request->all(), [
            'address1'  => 'required|regex:/(^[-0-9A-Za-z.,\/ ]+$)/',
            'address2'  => 'required|regex:/(^[-0-9A-Za-z.,\/ ]+$)/',
            'zip'       => 'required|digits:6'
        ], [
            'address1.*' => 'Please Enter The Correct Address',
            'address2.*' => 'Please Enter The Correct Address',
            'zip.*'      => 'Please Enter The Correct Pincode',
        ])->validate();

        ProfileModel::where('id', '=', Auth::id())->update([
            'ro_address_l1'       => $request->address1,
            'ro_address_l2'       => $request->address2,
            'ro_address_pin_code' => $request->zip
        ]);
        Session::flash('success_mes', 'Officer Details has Updated successfully !'); 
        return redirect()->back();
    }

}