<?php  
    namespace App\Http\Controllers\Admin\CandNomination;
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
    use App\models\Admin\BallotModel;
    use App\adminmodel\PartyMaster;
    use App\adminmodel\CandidateNomination;
    use App\Helpers\SmsgatewayHelper;
    use App\Classes\xssClean;
    use App\adminmodel\SymbolMaster;
    use Illuminate\Support\Facades\Crypt;

 
class BallotGerationController  extends Controller
{
    //  

    public $base    = 'roac';
    public $folder  = 'CandNomination';
    public $action    = 'roac/';
    public $view_path = "admin.candform";
   public function __construct()
        {   
      $this->middleware('adminsession');
      $this->middleware(['auth:admin','auth']);
      $this->commonModel = new commonModel();
      $this->xssClean = new xssClean;
      $this->ballot = new BallotModel;

      $this->sym = new SymbolMaster();
      if(!Auth::check()){ 
            return redirect('/officer-login');
          }
          $user = Auth::user();
            switch ($user->role_id) {
                case '5':
                    $this->middleware('deo');
                    break;
                case '4':
                    $this->middleware('ceo');
                    break;
                case '19':
                    $this->middleware('ro');
                    break;
                default:
                    $this->middleware('eci');
            }
       
      $this->middleware('clean_url');
       
      
     }

    
     

   public function download_etpbs_ballot(Request $request)
        {
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          //dd($ele_details);
         $st_code=$this->xssClean->clean_input($request->st_code);
         $ac_no=$this->xssClean->clean_input($request->ac_no);
         if($st_code=='' || $ac_no=="")
           {
             return Redirect::to('/logout');  
           }
        $filter = [
                'st_code'               => $st_code,
                'ac_no'                  => $ac_no,
                'election_id'           => Auth::user()->election_id,
                'const_type'            => "AC",
               ];
             
          
        $record=$this->ballot->etpbscandidate($filter);
            $ac=getacbyacno($st_code,$ac_no);
        $state=getstatebystatecode($st_code); 
              $state_language=getstatelanguage($st_code);
              if($st_code=="S10" and $state_language=="KAN")
                 {
                    $data['font_data']="kannad";
                    $data['fonts']="tunga";
                 }
              elseif(($st_code=="S29"||$st_code=="S01") and $state_language=="TEL")
                {
                   $data['font_data']="telugu";
                    $data['fonts']="gautami";
                }
              // elseif(($st_code=="S23"||$st_code=="S25") and $state_language=="BEN")
              //   {
              //      $data['font_data']="bangla";
              //       $data['fonts']="mitra";
              //   }
              else{
                    $data['font_data']="manny";
                    $data['fonts']="freeserif";
                  }


                    $data['heading_title']="ETPBS BAllOT";
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['postal_subhead']=$ac->AC_NO."-".strtoupper($ac->AC_NAME)."  ".strtoupper($ele_details->CONST_TYPE)."/".$ele_details->YEAR." ".strtoupper($ele_details->ELECTION_TYPE);
                   
                    $data['st_code']=$st_code;
                    $data['st_name']=$state->ST_NAME;
                    $data['ac']   =$ac;
                    $data['ac_no']=$ac->AC_NO;
                $data['ac_name']=$ac->AC_NAME;    
                    
                    $data['record']=$record;
             
          $name_excel = 'etpbsballot-'.$st_code."-ac_no-".$ac_no.'-'.time();
          $data['file_name']=$name_excel; 
          $data['ref_no']  =time();
 

          $data['user']=\Auth::user()->officername;
          $data['print_date']=date('d-m-Y H:i:a');
                $setting_pdf = [
                    'margin_top'        =>32,  
                    'margin_bottom'     =>5,
                    'margin_left'       =>0,  
                    'margin_right'      =>0,
                    'show_warnings'     => false,    
                   // 'orientation'       => 'portlet',    
                ];
          //dd($data);
             //return view($this->view_path.'.etpbs-ballot', $data); 
             $pdf = \MPDF::loadView($this->view_path.'.etpbs-ballot',$data,[], $setting_pdf);
             return $pdf->download($name_excel.'.pdf'); 
          
       
        }
       
       public function evmballotpaperpdfview(Request $request)
        {
         $user = Auth::user();
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
               
               $st_code=$this->xssClean->clean_input($request->st_code);
         $ac_no=$this->xssClean->clean_input($request->ac_no);
         if($st_code=='' || $ac_no=="")
           {
             return Redirect::to('/logout');  
           }
         $filter = [
                'st_code'               => $st_code,
                'ac_no'           => $ac_no,
                'election_id'           => Auth::user()->election_id,
                'const_type'            => "AC",
               ];
               $record=$this->ballot->evmballots($filter);
               $ac=getacbyacno($st_code,$ac_no);
           $state=getstatebystatecode($st_code); 
                $state_language=getstatelanguage($st_code);
              if($st_code=="S10" and $state_language=="KAN")
                 {
                    $data['font_data']="kannad";
                    $data['fonts']="tunga";
                 }
              elseif(($st_code=="S29"||$st_code=="S01") and $state_language=="TEL")
                {
                   $data['font_data']="telugu";
                    $data['fonts']="gautami";
                }
               
              else{
                    $data['font_data']="manny";
                    $data['fonts']="freeserif";
                  }


                    $data['heading_title']="ETPBS BAllOT";
                    $data['user_data']=$d;
                    $data['ref_no']  =rand(100000,999999);
                    $data['ele_details']=$ele_details;
                    $data['subhead']= $data['ref_no'];
                    $data['subhead1']= $ac->AC_NO."-".strtoupper($ac->AC_NAME)."/".$ele_details->YEAR."-".substr($ele_details->ELECTION_TYPE,0,3);
                   
                    $data['st_code']=$st_code;
                    $data['st_name']=$state->ST_NAME;
                    $data['ac_no']=$ac->AC_NO;
                    $data['ac_name']=$ac->AC_NAME;    
                    $data['record']=$record;
             
              $name_excel = 'evmballot-'.$st_code."-ac_no-".$ac_no.'-'.time();
              $data['file_name']=$name_excel; 
              
              $data['user']=\Auth::user()->officername;
              $data['print_date']=date('d-m-Y H:i:a');

                $setting_pdf = [
                    'margin_top'        =>5,  
                    'margin_bottom'     =>1,
                    'margin_left'       =>1,  
                    'margin_right'      =>1,
                    'show_warnings'     => false,
                    'mode'              => 'utf-8', 
                    'format'            =>[140, 461.5]    
                   // 'orientation'       => 'portlet',    
                ];
           //dd($data);
        // return view($this->view_path.'.evm-ballot', $data); 
             $pdf = \MPDF::loadView($this->view_path.'.evm-ballot',$data,[], $setting_pdf);
             return $pdf->download($name_excel.'.pdf'); 
        
        } 
      
}  // end class   
