<?php namespace App\Http\Controllers\Admin\Profile;
    
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Illuminate\Support\Facades\Hash;
    use Carbon\Carbon;
use DB, Validator;
use App\Http\Requests\Admin\Profile\PinUpdateRequest;

class TwoStepPinController extends Controller
  {

    public $action = "profile/pin/update_via_web";

    public function __construct(){   
        $this->middleware(['auth:admin','auth']);
    }

    public function index(Request $request){

        $data                   = [];
        $data['buttons']        = [];
        $data['heading_title']  = "Change Pin";
        $data['action']         = url($this->action);
        $data['user_data']      = Auth::user();
        return view('admin.common.pin', $data);

    }

    public function update_via_web(PinUpdateRequest $request){
        $data = [];
        $data = $request->all();
        try{
            $result = \App\models\Admin\OfficerModel::update_via_web($data);
            if(!$result){
                Session::flash('error_mes', 'Old pin mismatched.');
                return Redirect::back();
            }
        }catch(\Exception $e){
            Session::flash('error_mes', 'We have encounted an error. Please try again.');
            return Redirect::back();
        }
        Session::flash('success_mes', 'Pin has been updated successfully.');
        return Redirect::back();
    }

    public function update(Request $request){
       
        if(!Auth::user()){
            return \Response::json([
                'status'            => 0,
                'login_required'    => true,
                'message'           => "Please login to continue"
            ]);
        }

        $validator = Validator::make($request->all(),[
            'pin'              => 'required|confirmed|digits:6',
            'pin_confirmation' => 'required|digits:6'
        ],[
            'regex'     => 'Please enter only numeric value.',
            'numeric'   => 'Please enter only numeric value.',
            'pin'       => 'Please enter a valid 6 digit number.'
        ]);
//dd($validator);
        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }

        $data           = [];
        $data['pin']    = $request->pin;

        try{
            \App\models\Admin\OfficerModel::update_pin($data);
        }catch(\Exception $e){
            \Response::json([
                'status'    => false,
                'message'   => "Please try again."
            ]);
        }
        return \Response::json([
            'status'    => true,
            'message'   => "Pin has been updated successfully. Please login to continue"
        ]);

    }
    
} // end