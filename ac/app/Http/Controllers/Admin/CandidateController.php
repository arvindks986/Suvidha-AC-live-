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
    use App\commonModel;  
    use App\models\Admin\CandidateModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;

class CandidateController extends Controller {
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct(){
    $this->commonModel  = new commonModel();
    $this->candidate_model = new CandidateModel();
  }

  public function detail($id,Request $request){

    //first argument should be string, second would be $request object
    $request->merge(['ccode' => $id]);
    $request_status = validate_request('',$request);
    if(!$request_status){
      return Redirect::to('logout');
    }
    //end validate request
    
    $data = [];


      $data['heading_title'] = "Scrutiny Reports of ".$id." candidate(s)";

      
      $from_date  = NULL;
      $from_to    = NULL;

      $data['action'] = url('/roac/datewisereport');

      if(!Auth::user()){
        return redirect('/officer-login');
      }
      $d              = Auth::user();

      if($d->designation=='ROAC'){
        $ele_details    = $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $check_finalize = candidate_finalizebyro(@$ele_details->ST_CODE,@$ele_details->CONST_NO,@$ele_details->CONST_TYPE);
        $seched         = getschedulebyid(@$ele_details->ScheduleID);
        $sechdul        = checkscheduledetails($seched);  
        if(isset($ele_details->ScheduleID)) {
          $sched      = $this->commonModel->getschedulebyid(@$ele_details->ScheduleID);
          $const_type = @$ele_details->CONST_TYPE;
        }else {
          $sched      = '';
        }
        $data['cand_finalize_ceo']  = @$check_finalize->finalize_by_ceo;
        $data['cand_finalize_ro']   = @$check_finalize->finalized_ac;
        $data['sechdul']            = $sechdul;
        $data['ele_details']        = $ele_details;
        $data['sched']              = $sched;
      }



      $filter_data = [];
      $id = base64_decode($id);

      $candidate        = $this->candidate_model->get_detail($id, $filter_data);
      if(!$candidate || count($candidate)==0){
        return "Not Found";
      }
      $data['EciViewNomination'] = $candidate;
    
      $data['user_data']          = Auth::user();
      

      return view('admin.ac.ro.candidate.candidate_detail', $data);     
  }  

}  // end class