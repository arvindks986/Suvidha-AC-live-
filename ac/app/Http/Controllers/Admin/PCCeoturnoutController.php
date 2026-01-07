<?php  
    namespace App\Http\Controllers\Admin;
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
     
    use App\Helpers\SmsgatewayHelper;
     
    use App\Classes\xssClean;
    use App\adminmodel\SymbolMaster;
    use Illuminate\Support\Facades\Crypt;
        use App\adminmodel\Pollday;
 
class PCCeoturnoutController extends Controller
{
    //
    public function __construct(){
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        
    
    }
 
  protected function guard(){
        return Auth::guard('admin');
      }

    public function index()
      {     
      if(Auth::check()){
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);

           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          return view('admin.pc.ceo.end-of-poll-finalize', ['user_data' => $d,'ele_details'=>$ele_details]);            
          }
          else {
                return redirect('/officer-login');
              }
      }  // end index function
 
    public function veryfyend_of_poll_finalize(Request $request)
            {
            if(Auth::check()){
               $user = Auth::user();
                 $d=$this->commonModel->getunewserbyuserid($user->id);
                 $phasenumber=$request->input('phasenumber'); 

                 $st = array('updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"),'updated_by'=>$d->officername,'end_of_poll_finalize'=>'1'); 
                 
                $i = DB::table('pd_scheduledetail')->where('st_code', $d->st_code)->where('scheduleid', $phasenumber)->update($st);
                \Session::flash('success_mes', 'Voter Turnout finalize successfully');
                 return Redirect::to('pcceo/end-of-poll-finalize');        
            }
            else {
                  return redirect('/officer-login');
                }
      }  // end index function
}  // end class  //accepted_candidate  
