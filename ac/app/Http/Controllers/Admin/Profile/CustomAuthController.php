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
    use App\Classes\xssClean;

class CustomAuthController extends Controller
  {

    public function logout(Request $request){
        
        $data           = [];
        try{
            \App\models\Admin\OfficerModel::logout();
        }catch(\Exception $e){
        }
        
        return \Response::json([
            'status'    => true,
            'message'   => "Pin has been updated successfully. Please login to continue"
        ]);

    }
    
} // end