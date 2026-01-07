<?php
    namespace App\Http\Controllers\Admin\Common;
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
    use App\adminmodel\ECIModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\CandidateModel;
    use App\adminmodel\PartyMaster;
    use App\adminmodel\CandidateNomination;
    use App\adminmodel\ACCountingModel; 
    use App\Classes\xssClean;
   use App\models\Admin\Form7AdetilsModel;
class ECICommonController extends Controller
{
    
    public function __construct(){   
            $this->middleware('adminsession');
            $this->middleware(['auth:admin','auth']);
            $this->middleware('eci');
            $this->commonModel = new commonModel();
            $this->ECIModel = new ECIModel();
            $this->xssClean = new xssClean;
            $this->CountingModel = new ACCountingModel();
            $this->formmodel = new Form7AdetilsModel;
      }

    protected function guard(){
        return Auth::guard();
    }

    

     public function sendnominationmessage()
            {
             $nom_details =DB::table('candidate_personal_detail')->where('cand_mobile','<>','')->get();
             foreach($nom_details as $nom)
                        { set_time_limit(0);

                          if($nom->cand_mobile!='') {
                            $mob_message="Now you can check your nomination/ permission status through suvidha candidate android app. Download from here https://goo.gl/YGoMmM and login using this mobile number.";
                            echo count($mob_message)."<br>";
                            $response = SmsgatewayHelper::gupshup($nom->cand_mobile,$mob_message);
                            //echo $nom->candidate_id."=".$mob_message;
                          }   
                        }
             } //  End Function


     
       
}  // end class