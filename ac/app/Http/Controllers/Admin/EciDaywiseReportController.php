<?php namespace App\Http\Controllers\Admin;
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
    use App\models\Admin\ReportModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\models\Admin\StateModel;
    use App\models\Nomination\OnlineNomModel;

    use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Classes\xssClean;

 ini_set("memory_limit","1500M");
    set_time_limit('6000');
    ini_set("pcre.backtrack_limit", "10000000");
    $dbname = DB::connection()->getDatabaseName();
    //dd($dbname);

class EciDaywiseReportController extends Controller {
  
  public $base    = 'ro';
  public $folder  = 'eci';
  public $action    = 'eci/report/scrutiny';
  public $view_path = "admin.ac.eci";

  public function __construct(){
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
  }

public function contestingNominationcandForm(Request $request){ 
      //AC ECI PHASE INFO REPORT DATA BY PHASE ID FORM TRY CATCH STARTS HERE
      try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $phaseid        = $xss->clean_input($request['phaseid']);
         //dd($phaseid);
            
            if (!$phaseid) {
                 $phaseid = "";
            }else{
                $phaseid = $phaseid;
            }


            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
                  if(empty($phaseid)){
                     return redirect('/eci/contestingNominationcand/'); 

                  }else{
        
             return redirect('/eci/contestingNominationcandfilter/'.base64_encode($phaseid)); 
             }        
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
     
    }




        public function ContestingNominationcandfilter(Request $request,$phaseid){ 
    

           $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_m          = 0;
                $total_f          = 0;
                $total_tg         = 0;
                $total_category    =0;
                $ca_report         =0;
                $TotalContesting   =0;
                  $TotalMale       =0;
                  $TotalFemale     =0;
                  $TotalTg         =0;
                  $TotalCategory   =0;
                  $TotalCA         =0;

                  $TotalAge_from_25=0;
                  $TotalAge_from_40=0;
                  $TotalAge_from_60=0;
    
      

         $tgcount                = $this->report_model->NominationDetails_tgcount('Third gender');
         $tgcountis=count($tgcount);
          $getphase                = $this->getphase();
        $user = Auth::user();   

        

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

            //STATE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

                  $p1=1;$p2=10;
                  if($phaseid==1){
                      $phaseid=$p1.','.$p2;
                  }

  

$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`PHASE_NO`IN ($phaseid) AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";

             

                // $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."' AND `application_status`=6 AND `finalaccepted`=1 AND `finalize` ='1' AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

          
             
            
             $contestingNominationcandfilter = DB::select($contestingNominationcandfilter);
            foreach ($contestingNominationcandfilter as $key=>$lis) {   

          
          //dd($lis);
               // count_nomination_phase

          $nomination        = $this->report_model->NominationDetails('','', $lis->ST_CODE,$phaseid);
          $male              = $this->report_model->NominationDetails('','male', $lis->ST_CODE,$phaseid);
          $female            = $this->report_model->NominationDetails('','female', $lis->ST_CODE,$phaseid);
          $tg                = $this->report_model->NominationDetails('','Third gender', $lis->ST_CODE,$phaseid);
          $category          = $this->report_model->NominationDetails('st','', $lis->ST_CODE,$phaseid);
          $ca_count          = $this->report_model->Contesting_Cand('1', $lis->ST_CODE,$phaseid);

           $AgeGroup_25          = $this->report_model->Contesting_Cand_age('25', $lis->ST_CODE,$phaseid);
            $AgeGroup_40          = $this->report_model->Contesting_Cand_age('40', $lis->ST_CODE,$phaseid);
            $AgeGroup_60          = $this->report_model->Contesting_Cand_age('60', $lis->ST_CODE,$phaseid);
         
     //dd(count($AgeGroup_25),count($AgeGroup_40));
          $TotalContesting += count($nomination);
          $TotalMale       +=count($male);
          $TotalFemale     +=count($female);
          $TotalTg         +=count($tg);
          $TotalCategory   +=count($category);
          $TotalCA         += count($ca_count);

          $TotalAge_from_25     +=count($AgeGroup_25);
          $TotalAge_from_40     +=count($AgeGroup_40);
          $TotalAge_from_60     +=count($AgeGroup_60);
         
          
          $total_online     = count($nomination);
          $total_m          = count($male);
          $total_f          = count($female);
          $total_tg         = count($tg);
          $total_category   = count($category);
          $ca_report=  count($ca_count);

          $Agefrom25     =count($AgeGroup_25);
          $Agefrom40     =count($AgeGroup_40);
          $Agefrom60     =count($AgeGroup_60);
          //$total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis->ST_NAME,
       
             'phase'           => $lis->StatePHASE_NO,
         
         
            'nomination'  => $nomination,
            'male'  => $male,
            'female'  => $female,
            'tg'  => $tg,
            'category'=>$category,
            'cadetail' => $ca_count,
            'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            
          ];             

    }

      

          $data['results']      =$results;
          $data['phaseid']      =$phaseid;
          $data['getphase']     =$getphase;
          $data['user_data']    = Auth::user();
          $data['tgcountis']    = $tgcountis;   

          $data['TotalContesting']   = $TotalContesting;  
          $data['TotalMale']         = $TotalMale;  
          $data['TotalFemale']       = $TotalFemale;  
          $data['TotalTg']           = $TotalTg;   
          $data['TotalCategory']     = $TotalCategory;
          $data['TotalCA']           = $TotalCA; 
          $data['TotalAge_from_25']     =$TotalAge_from_25;
          $data['TotalAge_from_40']     =$TotalAge_from_40;
          $data['TotalAge_from_60']     =$TotalAge_from_60;
          //dd($data);
          return view($this->view_path.'.report.contesting_candidate_filter', $data);      

       
        //}
    }


        
         public function contestingNominationcand(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_m          = 0;
                $total_f          = 0;
                $total_tg         = 0;
                $total_category    =0;
                $ca_report         =0;
                $TotalContesting   =0;
                  $TotalMale       =0;
                  $TotalFemale     =0;
                  $TotalTg         =0;
                  $TotalCategory   =0;
                  $TotalCA         =0;
                  $TotalAge_from_25=0;
                  $TotalAge_from_40=0;
                  $TotalAge_from_60=0;
            
      

         $tgcount                = $this->report_model->NominationDetails_tgcount('Third gender');
         $tgcountis=count($tgcount);
         $getphase          = $this->getphase();
//dd($getphase);
        $user = Auth::user();   


         $EciPhaseInfoDataSelect = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `application_status`=6 AND `finalaccepted`=1 AND c.`finalize` ='1' AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

$EciPhaseInfoDataSelect = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";

          $EciPhaseInfoData = DB::select($EciPhaseInfoDataSelect);
                  
           


       foreach ($EciPhaseInfoData as $key=>$lis) {   

          
          //dd($lis);

          $nomination        = $this->report_model->NominationDetails('','', $lis->ST_CODE,'');
          $male              = $this->report_model->NominationDetails('','male', $lis->ST_CODE,'');
          $female            = $this->report_model->NominationDetails('','female', $lis->ST_CODE,'');
          $tg                = $this->report_model->NominationDetails('','Third gender', $lis->ST_CODE,'');
          $category          = $this->report_model->NominationDetails('st','', $lis->ST_CODE,'');
          $ca_count          = $this->report_model->Contesting_Cand('1', $lis->ST_CODE,'');
          $AgeGroup_25          = $this->report_model->Contesting_Cand_age('25', $lis->ST_CODE,'');
          $AgeGroup_40          = $this->report_model->Contesting_Cand_age('40', $lis->ST_CODE,'');
          $AgeGroup_60          = $this->report_model->Contesting_Cand_age('60', $lis->ST_CODE,'');
         // dd(count($AgeGroup_25),count($AgeGroup_40),count($AgeGroup_60));

          $TotalContesting += count($nomination);
          $TotalMale       +=count($male);
          $TotalFemale     +=count($female);
          $TotalTg         +=count($tg);
          $TotalCategory   +=count($category);
          $TotalCA         += count($ca_count);
          $TotalAge_from_25     +=count($AgeGroup_25);
          $TotalAge_from_40     +=count($AgeGroup_40);
          $TotalAge_from_60     +=count($AgeGroup_60);
      //dd($female);
           //dd($nomination);
          $third_g[]=$tg;
         
          
          $total_online     = count($nomination);
          $total_m          = count($male);
          $total_f          = count($female);
          $total_tg         = count($tg);
          $total_category   = count($category);
          $ca_report=  count($ca_count);
           $Agefrom25     =count($AgeGroup_25);
          $Agefrom40     =count($AgeGroup_40);
          $Agefrom60     =count($AgeGroup_60);
          //$total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis->ST_NAME,
       
             'phase'           => $lis->StatePHASE_NO,
         
         
            'nomination'  => $nomination,
            'male'  => $male,
            'female'  => $female,
            'tg'  => $tg,
            'category'=>$category,
            'cadetail' => $ca_count,
            'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            
          ];             

    }

       

          $data['results']    =  $results;
          $data['getphase']    =  $getphase;
          $data['user_data']  = Auth::user();
          $data['tgcountis'] =     $tgcountis; 

          $data['TotalContesting'] =     $TotalContesting;  
          $data['TotalMale'] =     $TotalMale;  
          $data['TotalFemale'] =     $TotalFemale;  
          $data['TotalTg'] =     $TotalTg;   
          $data['TotalCategory'] =     $TotalCategory;
          $data['TotalCA'] =     $TotalCA; 
          $data['TotalAge_from_25']     =$TotalAge_from_25;
          $data['TotalAge_from_40']     =$TotalAge_from_40;
          $data['TotalAge_from_60']     =$TotalAge_from_60;
           // dd($data);
          return view($this->view_path.'.report.contesting_candidate', $data);

         }

          

        
         public function contestingNominationcand_pdf(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_m          = 0;
                $total_f          = 0;
                $total_tg         = 0;
                $total_category    =0;
                $ca_report         =0;

                 $TotalContesting   =0;
                  $TotalMale       =0;
                  $TotalFemale     =0;
                  $TotalTg         =0;
                  $TotalCategory   =0;
                  $TotalCA         =0;
                   $TotalAge_from_25=0;
                  $TotalAge_from_40=0;
                  $TotalAge_from_60=0;
    

               $tgcount  = $this->report_model->NominationDetails_tgcount('Third gender');
               $tgcountis=count($tgcount);

               $user = Auth::user();   

                $getphase          = $this->getphase();


           

//dd($phaseid);
             

                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180  AND `application_status`=6 AND `finalize` ='1' AND `finalaccepted`=1 AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";



          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                  
           



       foreach ($EciPhaseInfoData as $key=>$lis) {   

          
          //dd($lis);

          $nomination        = $this->report_model->NominationDetails('','', $lis->ST_CODE,'');
          $male              = $this->report_model->NominationDetails('','male', $lis->ST_CODE,'');
          $female            = $this->report_model->NominationDetails('','female', $lis->ST_CODE,'');
          $tg                = $this->report_model->NominationDetails('','Third gender', $lis->ST_CODE,'');
          $category          = $this->report_model->NominationDetails('st','', $lis->ST_CODE,'');
          $ca_count          = $this->report_model->Contesting_Cand('1', $lis->ST_CODE,'');
          $AgeGroup_25          = $this->report_model->Contesting_Cand_age('25', $lis->ST_CODE,'');
            $AgeGroup_40          = $this->report_model->Contesting_Cand_age('40', $lis->ST_CODE,'');
            $AgeGroup_60          = $this->report_model->Contesting_Cand_age('60', $lis->ST_CODE,'');
         
          $TotalContesting += count($nomination);
          $TotalMale       +=count($male);
          $TotalFemale     +=count($female);
          $TotalTg         +=count($tg);
          $TotalCategory   +=count($category);
          $TotalCA         += count($ca_count);
           $TotalAge_from_25     +=count($AgeGroup_25);
          $TotalAge_from_40     +=count($AgeGroup_40);
          $TotalAge_from_60     +=count($AgeGroup_60);
         
          
          $total_online     = count($nomination);
          $total_m          = count($male);
          $total_f          = count($female);
          $total_tg         = count($tg);
          $total_category   = count($category);
          $ca_report=  count($ca_count);

           $Agefrom25     =count($AgeGroup_25);
          $Agefrom40     =count($AgeGroup_40);
          $Agefrom60     =count($AgeGroup_60);
          //$total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis->ST_NAME,
       
            'phase'           => $lis->StatePHASE_NO,
         
            'nomination'  => $nomination,
            'male'  => $male,
            'female'  => $female,
            'tg'  => $tg,
            'category'=>$category,
            'cadetail' => $ca_count,
            'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            
          ];             

    }

      

         
          $data['heading_title'] = 'Contesting Candidate';
          $data['results']    =  $results;
          $data['tgcountis']    =  $tgcountis;
           $data['getphase']    =  $getphase;
          $data['user_data']  = Auth::user();

           $data['TotalContesting'] =     $TotalContesting;  
          $data['TotalMale'] =     $TotalMale;  
          $data['TotalFemale'] =     $TotalFemale;  
          $data['TotalTg'] =     $TotalTg;   
          $data['TotalCategory'] =     $TotalCategory;
          $data['TotalCA'] =     $TotalCA;  
           $data['TotalAge_from_25']     =$TotalAge_from_25;
          $data['TotalAge_from_40']     =$TotalAge_from_40;
          $data['TotalAge_from_60']     =$TotalAge_from_60;
           //dd($data);
           $pdf = \PDF::loadView('admin.ac.eci.report.contesting_candidate_pdf',$data);
            return $pdf->download('ContestinCandidate_report_'.date('d-m-Y').'_'.time().'.pdf');
           
          return view($this->view_path.'.report.contesting_candidate', $data);

         }


         public function contestingNominationcand_excel(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_m          = 0;
                $total_f          = 0;
                $total_tg         = 0;
                $total_category    =0;
                $ca_count          =0;

                 $TotalContesting   =0;
                  $TotalMale       =0;
                  $TotalFemale     =0;
                  $TotalTg         =0;
                  $TotalCategory   =0;
                  $TotalCA         =0;
                   $TotalAge_from_25=0;
                  $TotalAge_from_40=0;
                  $TotalAge_from_60=0;
    

                $tgcount    = $this->report_model->NominationDetails_tgcount('Third gender');
                $tgcountis=count($tgcount);

                $user = Auth::user();   


       


             

                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180  AND `application_status`=6 AND `finalaccepted`=1 AND `finalize` ='1' AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";


          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
          //$EciPhaseInfoData = DB::select($EciPhaseInfoDataSelect);
                  
           



       foreach ($EciPhaseInfoData as $key=>$lis) {   

          
          //dd($lis);

          $nomination        = $this->report_model->NominationDetails('','', $lis->ST_CODE,'');
          $male              = $this->report_model->NominationDetails('','male', $lis->ST_CODE,'');
          $female            = $this->report_model->NominationDetails('','female', $lis->ST_CODE,'');
          $tg                = $this->report_model->NominationDetails('','Third gender', $lis->ST_CODE,'');
          $category          = $this->report_model->NominationDetails('st','', $lis->ST_CODE,'');
          $ca_count          = $this->report_model->Contesting_Cand('1', $lis->ST_CODE,'');

          $AgeGroup_25          = $this->report_model->Contesting_Cand_age('25', $lis->ST_CODE,'');
            $AgeGroup_40          = $this->report_model->Contesting_Cand_age('40', $lis->ST_CODE,'');
            $AgeGroup_60          = $this->report_model->Contesting_Cand_age('60', $lis->ST_CODE,'');


             $TotalContesting += count($nomination);
          $TotalMale       +=count($male);
          $TotalFemale     +=count($female);
          $TotalTg         +=count($tg);
          $TotalCategory   +=count($category);
          $TotalCA         += count($ca_count);

          $TotalAge_from_25     +=count($AgeGroup_25);
          $TotalAge_from_40     +=count($AgeGroup_40);
          $TotalAge_from_60     +=count($AgeGroup_60);
         
         
      //dd($female);
         
         
          
          $total_online     = count($nomination);
          $total_m          = count($male);
          $total_f          = count($female);
          $total_tg         = count($tg);
          $total_category   = count($category);
          $ca_report=  count($ca_count);

           $Agefrom25     =count($AgeGroup_25);
          $Agefrom40     =count($AgeGroup_40);
          $Agefrom60     =count($AgeGroup_60);
          //$total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis->ST_NAME,
       
            'phase'           => $lis->StatePHASE_NO,
         
            'nomination'  => $nomination,
            'male'  => $male,
            'female'  => $female,
            'tg'  => $tg,
            'category'=>$category,
            'cadetail' => $ca_count,
             'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,

            
          ];             

    }



    $data['heading_title'] = 'Contesting Candidate';
          $data['results']    =  $results;
          $data['tgcountis']    =  $tgcountis;
          $data['user_data']  = Auth::user();

          $data['TotalContesting'] =     $TotalContesting;  
          $data['TotalMale'] =     $TotalMale;  
          $data['TotalFemale'] =     $TotalFemale;  
          $data['TotalTg'] =     $TotalTg;   
          $data['TotalCategory'] =     $TotalCategory;
          $data['TotalCA'] =     $TotalCA;  
          $data['TotalAge_from_25']     =$TotalAge_from_25;
          $data['TotalAge_from_40']     =$TotalAge_from_40;
          $data['TotalAge_from_60']     =$TotalAge_from_60;
            //dd($data);
          $headings[]=[];
    $export_data[] = [$data['heading_title']];
    if($tgcountis > 0){
    $export_data[] = ['SL NO','State' ,'Contesting Candidate','Age(25-40)','Age(41-60)','Age(61-Above)','Male','Female','TG','ST/SC','Criminal
Antecedents'];
}else{
$export_data[] = ['SL NO','State' ,'Contesting Candidate','Age(25-40)','Age(41-60)','Age(61-Above)','Male','Female','ST/SC','Criminal
Antecedents'];

}
    $i=1;
    foreach ($data['results'] as $lis) {
      //dd($lis);
      if(count($lis['nomination']) > 0){
         $nomination=count($lis['nomination']);
      }else{
       $nomination='0';
      }
      if($lis['Agefrom25'] > 0){
         $Agefrom25=$lis['Agefrom25'];
      }else{
       $Agefrom25='0';
      }
      if($lis['Agefrom40'] > 0){
         $Agefrom40=$lis['Agefrom40'];
      }else{
       $Agefrom40='0';
      }
      if($lis['Agefrom60'] > 0){
         $Agefrom60=$lis['Agefrom60'];
      }else{
       $Agefrom60='0';
      }
      if(count($lis['male']) > 0){
         $male=count($lis['male']);
      }else{
       $male='0';
      }
      if(count($lis['female']) > 0){
         $female=count($lis['female']);
      }else{
       $female='0';
      }
       
         if(count($lis['tg']) > 0){
         $tg=count($lis['tg']);
      }else{
       $tg='0';
      }
      
            if(count($lis['category']) > 0){
         $category=count($lis['category']);
      }else{
       $category='0';
      }
      if(count($lis['cadetail']) > 0){
         $cadetail=count($lis['cadetail']);
      }else{
       $cadetail='0';
      }
      
      //dd($lis);

      if($tgcountis > 0){
      $export_data[] = [
            'slno'              =>$i++,
            'label'       => $lis['label'],
             // 'phase'       => $lis['phase'],
            'nomination'  => $nomination,
            'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            'male'     =>    $male,
            'female'   =>    $female,
            'tg'       =>    $tg,
            'category' =>    $category,
             'cadetail' => $cadetail,
            
           
      ];
    } else{
 $export_data[] = [
            'slno'              =>$i++,
            'label'       => $lis['label'],
             //'phase'       => $lis['phase'],
            'nomination'  => $nomination,
            'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            'male'     =>    $male,
            'female'   =>    $female,
            
            'category' =>    $category,
             'cadetail' => $cadetail,
            
           
      ];

 }
       
        


    }
     if($tgcountis > 0){
         $export_data[] = ['Total','',$TotalContesting,$TotalAge_from_25,$TotalAge_from_40,$TotalAge_from_60,$TotalMale,$TotalFemale,$TotalTg,$TotalCategory,$TotalCA];
           }else{
         $export_data[] = ['Total','',$TotalContesting,$TotalAge_from_25,$TotalAge_from_40,$TotalAge_from_60,$TotalMale,$TotalFemale,$TotalCategory,$TotalCA];

}

    
          $name_excel = 'Contesting_Candidate_'.date('d-m-Y').'_'.time();

          return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');

          return view($this->view_path.'.report.contesting_candidate', $data);

      

      

          
         }







         public function countnomination(Request $request)
         {

               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_offline          = 0;
                $TotalOnline            = 0;
                $TotalOffline           = 0;
                $TotalAC           = 0;

                   $user = Auth::user();  

                $getphase          = $this->getphase();
                //dd($getphase);

                     // $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180   AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";


$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";





          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
        
          foreach ($EciPhaseInfoData as $key=>$lis) {

      

          
          //dd($filter_data);

          $onlinnomination_count        = $this->report_model->count_nomination(1, $lis->ST_CODE);
          $offline_count     = $this->report_model->count_nomination(0, $lis->ST_CODE);
          $electionDate     = $this->report_model->electionDate($lis->ST_CODE);
          $TotalOnline += count($onlinnomination_count);
          $TotalOffline += count($offline_count);
           $ac_count        = $this->report_model->count_ac($lis->ST_CODE);
           $TotalAC += $ac_count;
         
       // $electionDate[0]->LDT_IS_NOM;
         
          
          $total_online     = count($onlinnomination_count);
          $total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
if(!empty($electionDate[0]->LDT_IS_NOM)){
$newDate = Carbon::createFromFormat('Y-m-d',$electionDate[0]->LDT_IS_NOM)
                                    ->format('d-m-Y');
                                }else{
                                    $newDate='';
                                   // dd($newDate);
                                }
          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            //'const_no'           => $lis['code'],
           // 'const_name'         => $lis['name'],
            'online_nomination'  => $onlinnomination_count,
            'offline_nomination' => $offline_count,
            'total_perct'   => "sd",
            'LDT_IS_NOM'  =>  $newDate,
            'ac_count'  => $ac_count,
            
          ];                        
    }
    //dd($TotalOffline);
      

          $data['results']    =  $results;
          $data['onlineCount']=  $TotalOnline;
          $data['offlineCount']=  $TotalOffline;
          $data['user_data']  = Auth::user();
           $data['getphase']=  $getphase;
           $data['ac_count']=  $ac_count;
          $data['TotalAC']= $TotalAC;
          //  dd($data);
          return view($this->view_path.'.report.nomination_count_statewise', $data);

         }

         public function nomination_count_pdf(Request $request)
         {

          $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_offline          = 0;
                $TotalOnline            = 0;
                $TotalOffline           = 0;
                $TotalAC           = 0;

                   $user = Auth::user();  

                $getphase          = $this->getphase();

       $user = Auth::user();  
                   $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180  AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";



        
$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";



          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
          foreach ($EciPhaseInfoData as $key=>$lis) {

      

          
          //dd($filter_data);

          $onlinnomination_count        = $this->report_model->count_nomination(1, $lis->ST_CODE);
          $offline_count     = $this->report_model->count_nomination(0, $lis->ST_CODE);
           $electionDate     = $this->report_model->electionDate($lis->ST_CODE);
          $TotalOnline += count($onlinnomination_count);
          $TotalOffline += count($offline_count);
          $ac_count        = $this->report_model->count_ac($lis->ST_CODE);
           $TotalAC += $ac_count;
         
      //dd($onlinnomination_count);
         
          
          $total_online     = count($onlinnomination_count);
          $total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
$newDate = Carbon::createFromFormat('Y-m-d',$electionDate[0]->LDT_IS_NOM)
                                    ->format('d-m-Y');

          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
          //  'const_no'           => $lis['code'],
           // 'const_name'         => $lis['name'],
            'online_nomination'  => count($onlinnomination_count),
            'offline_nomination' => count($offline_count),
            'total_perct'   => "sd",
            'LDT_IS_NOM'  =>  $newDate,
            'ac_count'  => $ac_count,
            
          ];                        
    }
      
           $data['heading_title'] = 'Nomination Report';
          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
          $data['onlineCount']=  $TotalOnline;
          $data['offlineCount']=  $TotalOffline;
          $data['ac_count']=  $ac_count;
          $data['TotalAC']= $TotalAC;

            //$data = $this->get_report($request->merge(['is_excel' => 1]));
            $pdf = \PDF::loadView('admin.ac.eci.report.nominationcount_pdf',$data);
            return $pdf->download('NominationCount_report_'.date('d-m-Y').'_'.time().'.pdf');

         }

         public function nomination_count_excel(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_offline          = 0;
                $TotalOnline            = 0;
                $TotalOffline           = 0;

      $user = Auth::user();  
                 
                      $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180  AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
          foreach ($EciPhaseInfoData as $key=>$lis) {

      

          
          //dd($filter_data);

          $onlinnomination_count        = $this->report_model->count_nomination(1, $lis->ST_CODE);
          $offline_count     = $this->report_model->count_nomination(0, $lis->ST_CODE);
          $electionDate     = $this->report_model->electionDate($lis->ST_CODE);
          $TotalOnline += count($onlinnomination_count);
          $TotalOffline += count($offline_count);
         
      //dd($onlinnomination_count);
         
          
          $total_online     = count($onlinnomination_count);
          $total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;

$newDate = Carbon::createFromFormat('Y-m-d',$electionDate[0]->LDT_IS_NOM)
                                    ->format('d-m-Y');
          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            //'const_no'           => $lis['code'],
           // 'const_name'         => $lis['name'],
            'online_nomination'  => $onlinnomination_count,
            'offline_nomination' => $offline_count,
               'LDT_IS_NOM'  =>  $newDate,
            'total_perct'   => "sd"
            
          ];                        
    }
      
          $data['heading_title'] = 'Nomination Report';
          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
          $data['onlineCount']=  $TotalOnline;
          $data['offlineCount']=  $TotalOffline;
            //dd($data);
          $headings[]=[];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['SL NO','State','Last Date Of Nomination', 'OnLine Nomination','Offline Nominations'];
    
    $i=1;
    foreach ($data['results'] as $lis) {

        $cals=0;  $cal=(count($lis['online_nomination'])* count($lis['offline_nomination']))/100 ;
        if(!empty($cal)){$cal=$cal;}else{$cal="0";}
      if(count($lis['online_nomination']) > 0){
         $onlinenomination=count($lis['online_nomination']);
      }else{
       $onlinenomination='0';
      }
      if(count($lis['offline_nomination']) > 0){
         $offlinenomination=count($lis['offline_nomination']);
      }else{
       $offlinenomination='0';
      }
      //dd($lis);
      $export_data[] = [
            'slno'              =>$i++,
            'label'              => $lis['label'],
            'LDT_IS_NOM'              => $lis['LDT_IS_NOM'],
            'online'      => $onlinenomination,
            'offline'     => $offlinenomination,
            
           
      ];
    }
    $export_data[] = ['Total','','',$TotalOnline,$TotalOffline];
   
    
      $name_excel = 'nomination_report_'.date('d-m-Y').'_'.time();

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');

          return view($this->view_path.'.report.nomination_count_statewise', $data);

         }



 
public function loginrecord(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results               = [];
                $loginrecord           = 0;
                $nom_recv              =0;
               
    

       foreach ($data['states'] as $lis) {   

          $filter_data = [
            
            'st_code'       => base64_decode($lis['code']),
            'date'   => date('Y-m-d'),           
            
          ];
          //dd($filter_data);

          $loginrecord        = $this->report_model->loginrecord(1, $filter_data);
          $nom_recv     = $this->report_model->NominationRecv(0, $filter_data);
         
      
         
          
          $total_login     = count($loginrecord);
          $nomination_recv    = count($nom_recv);
          //dd($total_login);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis['name'],
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            
            'login_history'  => $total_login,
            'nomination_recv'=> $nomination_recv
            
            
          ];                        
    }
      

          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
            //dd($data);
          return view($this->view_path.'.report.login_history', $data);

         }


      
      public function loginrecord_pdf(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $loginrecord           = 0;
               
                $nom_recv              =0;
               
    

       foreach ($data['states'] as $lis) {   

          $filter_data = [
            
            'st_code'       => base64_decode($lis['code']),
            'date'   => date('Y-m-d'),           
            
          ];
          //dd($filter_data);

          $loginrecord        = $this->report_model->loginrecord(1, $filter_data);
          $nom_recv     = $this->report_model->NominationRecv(0, $filter_data);
         
      
         
          
          $total_login     = count($loginrecord);
          $nomination_recv    = count($nom_recv);
          //dd($total_login);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis['name'],
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            
            'login_history'  => $total_login,
            'nomination_recv'=> $nomination_recv
            
            
          ];                        
    }
             $data['heading_title'] = 'Login History at'.' '.date('d-m-Y');
           $data['results']    =  $results;
           $data['user_data']  = Auth::user();

         $pdf = \PDF::loadView('admin.ac.eci.report.login_history_pdf',$data);
            return $pdf->download('login_history_report_'.date('d-m-Y').'_'.time().'.pdf');
            return view($this->view_path.'.report.login_history', $data);


         }


         public function loginrecord_excel(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $loginrecord           = 0;
                $nom_recv              =0;
               
    

       foreach ($data['states'] as $lis) {   

          $filter_data = [
            
            'st_code'       => base64_decode($lis['code']),
            'date'   => date('Y-m-d'),           
            
          ];
          //dd($filter_data);

          $loginrecord        = $this->report_model->loginrecord(1, $filter_data);
          $nom_recv     = $this->report_model->NominationRecv(0, $filter_data);
         
      
         
          
          $total_login     = count($loginrecord);
          $nomination_recv    = count($nom_recv);
          //dd($total_login);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis['name'],
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            
            'login_history'  => $total_login,
            'nomination_recv'=> $nomination_recv
            
            
          ];                        
    }
      
      

          
   $data['heading_title'] = 'Login History at'.' '.date('d-m-Y');
          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
            //dd($data);
          $headings[]=[];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['SL NO','State', 'Total Login','Nomination Receive'];
    $i=1;
    foreach ($data['results'] as $lis) {
      if($lis['login_history'] > 0){
         $loginhistory=$lis['login_history'];
      }else{
       $loginhistory='0';
      }

       if($lis['nomination_recv'] > 0){
         $nomination_recv=$lis['nomination_recv'];
      }else{
       $nomination_recv='0';
      }
      
      //dd($lis);
      $export_data[] = [
            'slno'              =>$i++,
            'label'              => $lis['label'],
            'online'      => $loginhistory,
            'nomination_recv'=>$nomination_recv,
            
           
      ];
    }

    
          $name_excel = 'login_record_'.date('d-m-Y').'_'.time();

          return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');

          return view($this->view_path.'.report.login_history', $data);

      


         }






        
         public function afterscrutiny(Request $request)
         {
               $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_nomination          = 0;
                $total_online         = 0;
                $total_offline         = 0;
                //$total_tg         = 0;
               // $total_category    =0;
              $user=Auth::user();
              //dd($user->election_id);
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `application_status`=6  AND `symbol_id`!=200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                   $getphase          = $this->report_model->getphase();
           
//  dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

       
          //dd($filter_data);

          $nomination             = $this->report_model->AfterScrutiny('', $lis->ST_CODE,'');
          $online_nomination      = $this->report_model->AfterScrutiny('1', $lis->ST_CODE,'');
          $offline_nomination     = $this->report_model->AfterScrutiny('2', $lis->ST_CODE,'');
          $payment_online = $this->report_model->get_count_payment_wise($lis->ST_CODE,'online','');
          $payment_challan = $this->report_model->get_count_payment_wise($lis->ST_CODE,'challan','');
           $payment_cash = $this->report_model->get_count_payment_wise($lis->ST_CODE,'cash','');
         // $tg     = $this->report_model->NominationDetails('','Third gender', $filter_data);
         // $category     = $this->report_model->NominationDetails('st','', $filter_data);
         //dd($offline_nomination);
      //dd($payment_online,$payment_challan,$payment_cash);
         
          
          $total_nomination     = count($nomination);
          $total_online         = count($online_nomination);
          $total_offline        = count($offline_nomination);
          $payment_online       = count($payment_online);
          $payment_challan      = count($payment_challan);
          $payment_cash         = count($payment_cash);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
         $offline_cash= ($total_nomination-($payment_online + $payment_challan + $payment_cash));
        // dd($offline_cash);

          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'phase'           => $lis->StatePHASE_NO,
           // 'const_name'         => $lis['name'],
            'nomination'     => $nomination,
            'online'         => $online_nomination,
            'offline'        => $offline_nomination,
            'payment_online' =>$payment_online,
            'payment_challan'=> $payment_challan,
            'payment_cash'   => $payment_cash,
            'offline_cash'   => $offline_cash,
            
          ];                        
    }
      

          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
          $data['getphase'] = $getphase;
            //dd($data);
          return view($this->view_path.'.report.afterscrutiny_report', $data);

         }


         
        
         public function afterscrutiny_pdf(Request $request)
         {
                $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_nomination          = 0;
                $total_online         = 0;
                $total_offline         = 0;
                //$total_tg         = 0;
               // $total_category    =0;
    

          $user=Auth::user();
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `application_status`=6  AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                  
           
//dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

       
          //dd($filter_data);

          $nomination             = $this->report_model->AfterScrutiny('', $lis->ST_CODE,'');
          $online_nomination      = $this->report_model->AfterScrutiny('1', $lis->ST_CODE,'');
          $offline_nomination     = $this->report_model->AfterScrutiny('2', $lis->ST_CODE,'');
          $payment_online = $this->report_model->get_count_payment_wise($lis->ST_CODE,'online','');
          $payment_challan = $this->report_model->get_count_payment_wise($lis->ST_CODE,'challan','');
           $payment_cash = $this->report_model->get_count_payment_wise($lis->ST_CODE,'cash','');  // $tg     = $this->report_model->NominationDetails('','Third gender', $filter_data);
         // $category     = $this->report_model->NominationDetails('st','', $filter_data);
         //dd($offline_nomination);
      //dd($payment_online,$payment_challan,$payment_cash);
         
          
          $total_nomination     = count($nomination);
          $total_online         = count($online_nomination);
          $total_offline        = count($offline_nomination);
          $payment_online       = count($payment_online);
          $payment_challan      = count($payment_challan);
          $payment_cash         = count($payment_cash);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
         $offline_cash= ($total_nomination-($payment_online + $payment_challan + $payment_cash));
        // dd($offline_cash);

          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'phase'           => $lis->StatePHASE_NO,
           // 'const_name'         => $lis['name'],
            'nomination'     => $nomination,
            'online'         => $online_nomination,
            'offline'        => $offline_nomination,
            'payment_online' =>$payment_online,
            'payment_challan'=> $payment_challan,
            'payment_cash'   => $payment_cash,
            'offline_cash'   => $offline_cash,
            
          ];                        
    }
      
          $data['heading_title'] = 'After Scrutiny Report';
          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
            //dd($data);
           $pdf = \PDF::loadView('admin.ac.eci.report.afterscrutiny_report_pdf',$data);
            return $pdf->download('afterscrutiny_report_'.date('d-m-Y').'_'.time().'.pdf');
            //dd($data);
          
          return view($this->view_path.'.report.afterscrutiny_report', $data);

         }



         public function afterscrutiny_excel(Request $request)
         {
                $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_nomination          = 0;
                $total_online         = 0;
                $total_offline         = 0;
                //$total_tg         = 0;
               // $total_category    =0;
    

      $user=Auth::user();
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `application_status`=6  AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                  
           
//dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

       
          //dd($filter_data);

          $nomination             = $this->report_model->AfterScrutiny('', $lis->ST_CODE,'');
          $online_nomination      = $this->report_model->AfterScrutiny('1', $lis->ST_CODE,'');
          $offline_nomination     = $this->report_model->AfterScrutiny('2', $lis->ST_CODE,'');
          $payment_online = $this->report_model->get_count_payment_wise($lis->ST_CODE,'online','');
          $payment_challan = $this->report_model->get_count_payment_wise($lis->ST_CODE,'challan','');
           $payment_cash = $this->report_model->get_count_payment_wise($lis->ST_CODE,'cash','');  // $tg     =    // $tg     = $this->report_model->NominationDetails('','Third gender', $filter_data);
         // $category     = $this->report_model->NominationDetails('st','', $filter_data);
         //dd($offline_nomination);
      //dd($payment_online,$payment_challan,$payment_cash);
         
          
          $total_nomination     = count($nomination);
          $total_online         = count($online_nomination);
          $total_offline        = count($offline_nomination);
          $payment_online       = count($payment_online);
          $payment_challan      = count($payment_challan);
          $payment_cash         = count($payment_cash);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
         $offline_cash= ($total_nomination-($payment_online + $payment_challan + $payment_cash));
        // dd($offline_cash);

          $results[] = [
             'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'phase'           => $lis->StatePHASE_NO,
           // 'const_name'         => $lis['name'],
            'nomination'     => $nomination,
            'online'         => $online_nomination,
            'offline'        => $offline_nomination,
            'payment_online' =>$payment_online,
            'payment_challan'=> $payment_challan,
            'payment_cash'   => $payment_cash,
            'offline_cash'   => $offline_cash,
            
          ];                        
    }
      

          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
            //dd($data);
          



    $data['heading_title'] = 'After Scrutiny Report';
          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
            //dd($data);
          $headings[]=[];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['SL NO','State','Total Nomination','Online ','Offline','Payment BY Online','Payment BY Challan','Payment BY Cash','Offline Payment'];
    $i=1;
    foreach ($data['results'] as $lis) {
      //dd($lis);
      if(count($lis['nomination']) > 0){
         $nomination=count($lis['nomination']);
      }else{
       $nomination='0';
      }
      if(count($lis['online']) > 0){
         $online=count($lis['online']);
      }else{
       $online='0';
      }
      if(count($lis['offline']) > 0){
         $offline=count($lis['offline']);
      }else{
       $offline='0';
      }
      if($lis['payment_online'] > 0){
         $payment_online=$lis['payment_online'];
      }else{
       $payment_online='0';
      }
      if($lis['payment_challan'] > 0){
         $payment_challan=$lis['payment_challan'];
      }else{
       $payment_challan='0';
      }

      if($lis['payment_cash'] > 0){
         $payment_cash=$lis['payment_cash'];
      }else{
       $payment_cash='0';
      }
      if($lis['offline_cash'] > 0){
         $offline_cash=$lis['offline_cash'];
      }else{
       $offline_cash='0';
      }
      
      //dd($lis);
      $export_data[] = [
            'slno'              =>$i++,
            'label'       => $lis['label'],
            'nomination'  => $nomination,
            'online'     =>    $online,
            'offline'   =>    $offline,
            'payment_online'       =>    $payment_online,
            'payment_challan' =>    $payment_challan,
            'payment_cash' =>    $payment_cash,
            'offline_cash' =>    $offline_cash,
            
           
      ];
    }

    
          $name_excel = 'AfterScrutiny_Candidate_Report'.date('d-m-Y').'_'.time();

          return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');

          return view($this->view_path.'.report.afterscrutiny_report', $data);

      

      

          
         }


         public function contestingNominationcandfilter_pdf(Request $request,$phaseid)
         {



            $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_m          = 0;
                $total_f          = 0;
                $total_tg         = 0;
                $total_category    =0;
                $ca_report         =0;

                 $TotalContesting   =0;
                  $TotalMale       =0;
                  $TotalFemale     =0;
                  $TotalTg         =0;
                  $TotalCategory   =0;
                  $TotalCA         =0;
        
                  $TotalAge_from_25=0;
                  $TotalAge_from_40=0;
                  $TotalAge_from_60=0;


               $tgcount  = $this->report_model->NominationDetails_tgcount('Third gender');
               $tgcountis=count($tgcount);

               $user = Auth::user();   

                  $xss = new xssClean;
                $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

             //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

//dd($phaseid);
             
              $p1=1;$p2=10;
                  if($phaseid==1){
                      $phaseid=$p1.','.$p2;
                  }

  

$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`PHASE_NO`IN ($phaseid) AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";

             

                // $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."' AND `application_status`=6 AND `finalaccepted`=1 AND `finalize` ='1' AND `symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";


          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                  
           
//dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

          
          //dd($lis);

          $nomination        = $this->report_model->NominationDetails('','', $lis->ST_CODE,$phaseid);
          $male              = $this->report_model->NominationDetails('','male', $lis->ST_CODE,$phaseid);
          $female            = $this->report_model->NominationDetails('','female', $lis->ST_CODE,$phaseid);
          $tg                = $this->report_model->NominationDetails('','Third gender', $lis->ST_CODE,$phaseid);
          $category          = $this->report_model->NominationDetails('st','', $lis->ST_CODE,$phaseid);
          $ca_count          = $this->report_model->Contesting_Cand('1', $lis->ST_CODE,$phaseid);

           $AgeGroup_25          = $this->report_model->Contesting_Cand_age('25', $lis->ST_CODE,$phaseid);
            $AgeGroup_40          = $this->report_model->Contesting_Cand_age('40', $lis->ST_CODE,$phaseid);
            $AgeGroup_60          = $this->report_model->Contesting_Cand_age('60', $lis->ST_CODE,$phaseid);
         
      //dd($female);
          $TotalContesting += count($nomination);
          $TotalMale       +=count($male);
          $TotalFemale     +=count($female);
          $TotalTg         +=count($tg);
          $TotalCategory   +=count($category);
          $TotalCA         += count($ca_count);

           $TotalAge_from_25     +=count($AgeGroup_25);
          $TotalAge_from_40     +=count($AgeGroup_40);
          $TotalAge_from_60     +=count($AgeGroup_60);

         
          
          $total_online     = count($nomination);
          $total_m          = count($male);
          $total_f          = count($female);
          $total_tg         = count($tg);
          $total_category   = count($category);
          $ca_report=  count($ca_count);

           $Agefrom25     =count($AgeGroup_25);
          $Agefrom40     =count($AgeGroup_40);
          $Agefrom60     =count($AgeGroup_60);

          //$total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis->ST_NAME,
       
            'phase'           => $lis->StatePHASE_NO,
         
            'nomination'  => $nomination,
            'male'  => $male,
            'female'  => $female,
            'tg'  => $tg,
            'category'=>$category,
            'cadetail' => $ca_count,
             'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            
          ];             

    }

      

         
          $data['heading_title'] = 'Contesting Candidate';
          $data['results']    =  $results;
          $data['tgcountis']    =  $tgcountis;
          $data['user_data']  = Auth::user();

           $data['TotalContesting'] =     $TotalContesting;  
          $data['TotalMale'] =     $TotalMale;  
          $data['TotalFemale'] =     $TotalFemale;  
          $data['TotalTg'] =     $TotalTg;   
          $data['TotalCategory'] =     $TotalCategory;
          $data['TotalCA'] =     $TotalCA;  
          $data['TotalAge_from_25']     =$TotalAge_from_25;
          $data['TotalAge_from_40']     =$TotalAge_from_40;
          $data['TotalAge_from_60']     =$TotalAge_from_60;
           $pdf = \PDF::loadView('admin.ac.eci.report.contesting_candidate_filter_pdf',$data);
            return $pdf->download('ContestinCandidate_report_'.date('d-m-Y').'_'.time().'.pdf');
            //dd($data);
          return view($this->view_path.'.report.contesting_candidate_filter', $data);





         }


        
        public function contestingNominationcandfilter_excel(Request $request,$phaseid)
         {



            $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_m          = 0;
                $total_f          = 0;
                $total_tg         = 0;
                $total_category    =0;
                $ca_report         =0;

                   $TotalContesting   =0;
                  $TotalMale       =0;
                  $TotalFemale     =0;
                  $TotalTg         =0;
                  $TotalCategory   =0;
                  $TotalCA         =0;

                  $TotalAge_from_25=0;
                  $TotalAge_from_40=0;
                  $TotalAge_from_60=0;

    

               $tgcount  = $this->report_model->NominationDetails_tgcount('Third gender');
               $tgcountis=count($tgcount);

               $user = Auth::user();   

                  $xss = new xssClean;
                $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

             //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

//dd($phaseid);
             

                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."' AND `application_status`=6 AND `finalaccepted`=1 AND  `finalize` ='1' AND`symbol_id`<>200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";


          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                  
           
//dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

          
          //dd($lis);

          $nomination        = $this->report_model->NominationDetails('','', $lis->ST_CODE,$phaseid);
          $male              = $this->report_model->NominationDetails('','male', $lis->ST_CODE,$phaseid);
          $female            = $this->report_model->NominationDetails('','female', $lis->ST_CODE,$phaseid);
          $tg                = $this->report_model->NominationDetails('','Third gender', $lis->ST_CODE,$phaseid);
          $category          = $this->report_model->NominationDetails('st','', $lis->ST_CODE,$phaseid);
          $ca_count          = $this->report_model->Contesting_Cand('1', $lis->ST_CODE,$phaseid);

             $AgeGroup_25          = $this->report_model->Contesting_Cand_age('25', $lis->ST_CODE,$phaseid);
            $AgeGroup_40          = $this->report_model->Contesting_Cand_age('40', $lis->ST_CODE,$phaseid);
            $AgeGroup_60          = $this->report_model->Contesting_Cand_age('60', $lis->ST_CODE,$phaseid);
         
      //dd($female);
          $third_g[]=$tg;

           $TotalContesting += count($nomination);
          $TotalMale       +=count($male);
          $TotalFemale     +=count($female);
          $TotalTg         +=count($tg);
          $TotalCategory   +=count($category);
          $TotalCA         += count($ca_count);

          $TotalAge_from_25     +=count($AgeGroup_25);
          $TotalAge_from_40     +=count($AgeGroup_40);
          $TotalAge_from_60     +=count($AgeGroup_60);
         
         
          
          $total_online     = count($nomination);
          $total_m          = count($male);
          $total_f          = count($female);
          $total_tg         = count($tg);
          $total_category   = count($category);
          $ca_report=  count($ca_count);

           $Agefrom25     =count($AgeGroup_25);
          $Agefrom40     =count($AgeGroup_40);
          $Agefrom60     =count($AgeGroup_60);
          //$total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;


          $results[] = [
            'label'              => $lis->ST_NAME,
       
            'phase'           => $lis->StatePHASE_NO,
         
            'nomination'  => $nomination,
            'male'  => $male,
            'female'  => $female,
            'tg'  => $tg,
            'category'=>$category,
            'cadetail' => $ca_count,
            'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            
          ];             

    }

      
        
         $data['heading_title'] = 'Contesting Candidate';
          $data['results']    =  $results;
           $data['tgcountis']    =  $tgcountis;
          $data['user_data']  = Auth::user();
            //dd($data);
          $headings[]=[];
    $export_data[] = [$data['heading_title']];
    if($tgcountis > 0){
    $export_data[] = ['SL NO','State','Phase','Contesting Candidate','Age(25-40)','Age(41-60)','Age(61-Above)','Male','Female','TG','ST/SC','Criminal
Antecedents'];
}else{
$export_data[] = ['SL NO','State','Phase' ,'Contesting Candidate','Age(25-40)','Age(41-60)','Age(61-Above)','Male','Female','ST/SC','Criminal
Antecedents'];

}
    $i=1;
    foreach ($data['results'] as $lis) {
      
      if(count($lis['nomination']) > 0){
         $nomination=count($lis['nomination']);
      }else{
       $nomination='0';
      }

      if($lis['Agefrom25'] > 0){
         $Agefrom25=$lis['Agefrom25'];
      }else{
       $Agefrom25='0';
      }
      if($lis['Agefrom40'] > 0){
         $Agefrom40=$lis['Agefrom40'];
      }else{
       $Agefrom40='0';
      }
      if($lis['Agefrom60'] > 0){
         $Agefrom60=$lis['Agefrom60'];
      }else{
       $Agefrom60='0';
      }

      if(count($lis['male']) > 0){
         $male=count($lis['male']);
      }else{
       $male='0';
      }
      if(count($lis['female']) > 0){
         $female=count($lis['female']);
      }else{
       $female='0';
      }
       
         if(count($lis['tg']) > 0){
         $tg=count($lis['tg']);
      }else{
       $tg='0';
      }
      
            if(count($lis['category']) > 0){
         $category=count($lis['category']);
      }else{
       $category='0';
      }
      if(count($lis['cadetail']) > 0){
         $cadetail=count($lis['cadetail']);
      }else{
       $cadetail='0';
      }
      
      //dd($lis);

      if($tgcountis > 0){
      $export_data[] = [
            'slno'              =>$i++,
            'label'       => $lis['label'],
            'phase'       => $lis['phase'],
            'nomination'  => $nomination,
            'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,

            'male'     =>    $male,
            'female'   =>    $female,
            'tg'       =>    $tg,
            'category' =>    $category,
             'cadetail' => $cadetail,

            
            
           
      ];
    } else{
 $export_data[] = [
            'slno'              =>$i++,
            'label'       => $lis['label'],
            'phase'       => $lis['phase'],
            'nomination'  => $nomination,
              'Agefrom25' => $Agefrom25,
            'Agefrom40' => $Agefrom40,
            'Agefrom60' => $Agefrom60,
            'male'     =>    $male,
            'female'   =>    $female,
            
            'category' =>    $category,
             'cadetail' => $cadetail,
           
            
           
      ];



    }




    }
    if($tgcountis > 0){
         $export_data[] = ['Total','','',$TotalContesting,$TotalAge_from_25,$TotalAge_from_40,$TotalAge_from_60,$TotalMale,$TotalFemale,$TotalTg,$TotalCategory,$TotalCA];
           }else{
         $export_data[] = ['Total','','',$TotalContesting,$TotalAge_from_25,$TotalAge_from_40,$TotalAge_from_60,$TotalMale,$TotalFemale,$TotalCategory,$TotalCA];

}


    
          $name_excel = 'Contesting_Candidate_'.date('d-m-Y').'_'.time();

          return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');

          return view($this->view_path.'.report.contesting_candidate_filter', $data);

         }


public function afterscrutinyform(Request $request){ 
      //AC ECI PHASE INFO REPORT DATA BY PHASE ID FORM TRY CATCH STARTS HERE
      try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $phaseid        = $xss->clean_input($request['phaseid']);
         //dd($phaseid);
            
            if (!$phaseid) {
                 $phaseid = "";
            }else{
                $phaseid = $phaseid;
            }


            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
                  if(empty($phaseid)){
                     return redirect('/eci/afterscrutiny/'); 

                  }else{
        
             return redirect('/eci/afterscrutinyfilter/'.base64_encode($phaseid)); 
             }        
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
     
    }




        public function afterscrutinyfilter(Request $request,$phaseid){ 
    

            $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_nomination          = 0;
                $total_online         = 0;
                $total_offline         = 0;
                //$total_tg         = 0;
                $xss = new xssClean;
                $phaseid         = base64_decode($xss->clean_input($request['phaseid']));


                 if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

              $user=Auth::user();
              //dd($user->election_id);
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."' AND `application_status`=6  AND `symbol_id`!=200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                   $getphase          = $this->report_model->getphase();
           
 //dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

       
          //dd($filter_data);

          $nomination             = $this->report_model->AfterScrutiny('', $lis->ST_CODE,$phaseid);
          $online_nomination      = $this->report_model->AfterScrutiny('1', $lis->ST_CODE,$phaseid);
          $offline_nomination     = $this->report_model->AfterScrutiny('2', $lis->ST_CODE,$phaseid);
          $payment_online = $this->report_model->get_count_payment_wise($lis->ST_CODE,'online',$phaseid);
          $payment_challan = $this->report_model->get_count_payment_wise($lis->ST_CODE,'challan',$phaseid);
           $payment_cash = $this->report_model->get_count_payment_wise($lis->ST_CODE,'cash',$phaseid);
         // $tg     = $this->report_model->NominationDetails('','Third gender', $filter_data);
         // $category     = $this->report_model->NominationDetails('st','', $filter_data);
         //dd($offline_nomination);
      //dd($payment_online,$payment_challan,$payment_cash);
         
          
          $total_nomination     = count($nomination);
          $total_online         = count($online_nomination);
          $total_offline        = count($offline_nomination);
          $payment_online       = count($payment_online);
          $payment_challan      = count($payment_challan);
          $payment_cash         = count($payment_cash);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
         $offline_cash= ($total_nomination-($payment_online + $payment_challan + $payment_cash));
        // dd($offline_cash);

          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'phase'           => $lis->StatePHASE_NO,
           // 'const_name'         => $lis['name'],
            'nomination'     => $nomination,
            'online'         => $online_nomination,
            'offline'        => $offline_nomination,
            'payment_online' =>$payment_online,
            'payment_challan'=> $payment_challan,
            'payment_cash'   => $payment_cash,
            'offline_cash'   => $offline_cash,
            
          ];                        
    }
      

          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
          $data['getphase'] = $getphase;
           $data['phaseid']  =$phaseid;
            //dd($data);
          return view($this->view_path.'.report.afterscrutiny_report_filter', $data);
         
       
        //}
    }

    public function afterscrutinyfilter_pdf(Request $request,$phaseid)
    {

           $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_nomination          = 0;
                $total_online         = 0;
                $total_offline         = 0;
                //$total_tg         = 0;
                $xss = new xssClean;
                $phaseid         = base64_decode($xss->clean_input($request['phaseid']));


                 if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

              $user=Auth::user();
              //dd($user->election_id);
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."' AND `application_status`=6  AND `symbol_id`!=200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                   $getphase          = $this->report_model->getphase();
           
 //dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

       
          //dd($filter_data);

          $nomination             = $this->report_model->AfterScrutiny('', $lis->ST_CODE,$phaseid);
          $online_nomination      = $this->report_model->AfterScrutiny('1', $lis->ST_CODE,$phaseid);
          $offline_nomination     = $this->report_model->AfterScrutiny('2', $lis->ST_CODE,$phaseid);
          $payment_online = $this->report_model->get_count_payment_wise($lis->ST_CODE,'online',$phaseid);
          $payment_challan = $this->report_model->get_count_payment_wise($lis->ST_CODE,'challan',$phaseid);
           $payment_cash = $this->report_model->get_count_payment_wise($lis->ST_CODE,'cash',$phaseid);
         // $tg     = $this->report_model->NominationDetails('','Third gender', $filter_data);
         // $category     = $this->report_model->NominationDetails('st','', $filter_data);
         //dd($offline_nomination);
      //dd($payment_online,$payment_challan,$payment_cash);
         
          
          $total_nomination     = count($nomination);
          $total_online         = count($online_nomination);
          $total_offline        = count($offline_nomination);
          $payment_online       = count($payment_online);
          $payment_challan      = count($payment_challan);
          $payment_cash         = count($payment_cash);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
         $offline_cash= ($total_nomination-($payment_online + $payment_challan + $payment_cash));
        // dd($offline_cash);

          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'phase'           => $lis->StatePHASE_NO,
           // 'const_name'         => $lis['name'],
            'nomination'     => $nomination,
            'online'         => $online_nomination,
            'offline'        => $offline_nomination,
            'payment_online' =>$payment_online,
            'payment_challan'=> $payment_challan,
            'payment_cash'   => $payment_cash,
            'offline_cash'   => $offline_cash,
            
          ];                        
    }
      
           $data['heading_title'] = 'After Scrutiny Report';
          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
          $data['getphase'] = $getphase;
            //dd($data);
          $pdf = \PDF::loadView('admin.ac.eci.report.afterscrutiny_report_filter_pdf',$data);
            return $pdf->download('afterscrutiny_report_'.date('d-m-Y').'_'.time().'.pdf');
          return view($this->view_path.'.report.afterscrutiny_report_filter', $data);



    }

     public function afterscrutinyfilter_excel(Request $request,$phaseid)
    {

           $data = [];
               if(!Auth::user()){
               return redirect('/officer-login');
                }

              $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_nomination          = 0;
                $total_online         = 0;
                $total_offline         = 0;
                //$total_tg         = 0;
                $xss = new xssClean;
                $phaseid         = base64_decode($xss->clean_input($request['phaseid']));


                 if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

              $user=Auth::user();
              //dd($user->election_id);
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."' AND `application_status`=6  AND `symbol_id`!=200  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
                   $getphase          = $this->report_model->getphase();
           
 //dd($EciPhaseInfoData);


       foreach ($EciPhaseInfoData as $key=>$lis) {   

       
          //dd($filter_data);

          $nomination             = $this->report_model->AfterScrutiny('', $lis->ST_CODE,$phaseid);
          $online_nomination      = $this->report_model->AfterScrutiny('1', $lis->ST_CODE,$phaseid);
          $offline_nomination     = $this->report_model->AfterScrutiny('2', $lis->ST_CODE,$phaseid);
          $payment_online = $this->report_model->get_count_payment_wise($lis->ST_CODE,'online',$phaseid);
          $payment_challan = $this->report_model->get_count_payment_wise($lis->ST_CODE,'challan',$phaseid);
           $payment_cash = $this->report_model->get_count_payment_wise($lis->ST_CODE,'cash',$phaseid);
         // $tg     = $this->report_model->NominationDetails('','Third gender', $filter_data);
         // $category     = $this->report_model->NominationDetails('st','', $filter_data);
         //dd($offline_nomination);
      //dd($payment_online,$payment_challan,$payment_cash);
         
          
          $total_nomination     = count($nomination);
          $total_online         = count($online_nomination);
          $total_offline        = count($offline_nomination);
          $payment_online       = count($payment_online);
          $payment_challan      = count($payment_challan);
          $payment_cash         = count($payment_cash);
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;
         $offline_cash= ($total_nomination-($payment_online + $payment_challan + $payment_cash));
        // dd($offline_cash);

          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'phase'           => $lis->StatePHASE_NO,
           // 'const_name'         => $lis['name'],
            'nomination'     => $nomination,
            'online'         => $online_nomination,
            'offline'        => $offline_nomination,
            'payment_online' =>$payment_online,
            'payment_challan'=> $payment_challan,
            'payment_cash'   => $payment_cash,
            'offline_cash'   => $offline_cash,
            
          ];                        
    }
      
           
    $data['heading_title'] = 'After Scrutiny Report';
          $data['results']    =  $results;
          $data['user_data']  = Auth::user();
            //dd($data);
          $headings[]=[];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['SL NO','State','Phase','Total Nomination','Online ','Offline','Payment BY Online','Payment BY Challan','Payment BY Cash','Offline Payment'];
    $i=1;
    foreach ($data['results'] as $lis) {
      //dd($lis);
      if(count($lis['nomination']) > 0){
         $nomination=count($lis['nomination']);
      }else{
       $nomination='0';
      }
      if(count($lis['online']) > 0){
         $online=count($lis['online']);
      }else{
       $online='0';
      }
      if(count($lis['offline']) > 0){
         $offline=count($lis['offline']);
      }else{
       $offline='0';
      }
      if($lis['payment_online'] > 0){
         $payment_online=$lis['payment_online'];
      }else{
       $payment_online='0';
      }
      if($lis['payment_challan'] > 0){
         $payment_challan=$lis['payment_challan'];
      }else{
       $payment_challan='0';
      }

      if($lis['payment_cash'] > 0){
         $payment_cash=$lis['payment_cash'];
      }else{
       $payment_cash='0';
      }
      if($lis['offline_cash'] > 0){
         $offline_cash=$lis['offline_cash'];
      }else{
       $offline_cash='0';
      }
      
      //dd($lis);
      $export_data[] = [
            'slno'              =>$i++,
            'label'       => $lis['label'],
            'phase'       => $lis['phase'],
            'nomination'  => $nomination,
            'online'     =>    $online,
            'offline'   =>    $offline,
            'payment_online'       =>    $payment_online,
            'payment_challan' =>    $payment_challan,
            'payment_cash' =>    $payment_cash,
            'offline_cash' =>    $offline_cash,
            
           
      ];
    }

    
          $name_excel = 'AfterScrutiny_Report'.date('d-m-Y').'_'.time();

          return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');

         

          return view($this->view_path.'.report.afterscrutiny_report_filter', $data);



    }






         ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

  public function state_wise_pdf(Request $request){
    $data = $this->get_report_by_state($request->merge(['is_excel' => 1]));
    $pdf = \PDF::loadView('admin.ac.eci.report.pdf',$data);
    return $pdf->download('scrutiny_report_'.date('d-m-Y').'_'.time().'.pdf');
  }

  public function constancy_wise_pdf(Request $request){
    $data = $this->get_report($request->merge(['is_excel' => 1]));
    $pdf = \PDF::loadView('admin.ac.eci.report.pdf',$data);
    return $pdf->download('scrutiny_report_'.date('d-m-Y').'_'.time().'.pdf');
  }



  ////////////////////////////////////////////////////////////////////////////////////////



public function countnominationform(Request $request){ 
      //AC ECI PHASE INFO REPORT DATA BY PHASE ID FORM TRY CATCH STARTS HERE
      try{

         // $users=Session::get('admin_login_details');
          $user = Auth::user();   
           $data = [];
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $phaseid        = $xss->clean_input($request['phaseid']);
         //dd($phaseid);
            
            if (!$phaseid) {
                 $phaseid = "";
            }else{
                $phaseid = $phaseid;
            }


            $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_offline          = 0;
                $TotalOnline            = 0;
                $TotalOffline           = 0;

                   $user = Auth::user();  
  $getphase          = $this->getphase();
               
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.pc_no=e.`CONST_NO` AND `party_id`!=1180   AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

//dd($contestingNominationcandfilter);
$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";


          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
        
          foreach ($EciPhaseInfoData as $key=>$lis) {

      

          
          //dd($filter_data);

          $onlinnomination_count        = $this->report_model->count_nomination(1, $lis->ST_CODE);
          $offline_count     = $this->report_model->count_nomination(0, $lis->ST_CODE);
          $electionDate     = $this->report_model->electionDate($lis->ST_CODE);
          $TotalOnline += count($onlinnomination_count);
          $TotalOffline += count($offline_count);
         
       // $electionDate[0]->LDT_IS_NOM;
         
          
          $total_online     = count($onlinnomination_count);
          $total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;

$newDate = Carbon::createFromFormat('Y-m-d',$electionDate[0]->LDT_IS_NOM)
                                    ->format('d-m-Y');
                                   // dd($newDate);
          $results[] = [
            'label'              => $lis->ST_NAME,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            //'const_no'           => $lis['code'],
           // 'const_name'         => $lis['name'],
            'online_nomination'  => $onlinnomination_count,
            'offline_nomination' => $offline_count,
            'total_perct'   => "sd",
            'LDT_IS_NOM'  =>  $newDate,
            
          ];                        
    }
    //dd($TotalOffline);
      

          $data['results']    =  $results;
          $data['onlineCount']=  $TotalOnline;
          $data['offlineCount']=  $TotalOffline;
          $data['getphase'] = $getphase;
          $data['user_data']  = Auth::user();




            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
                  if(empty($phaseid)){
                     return redirect('/eci/countnomination/'); 

                  }else{
        //dd($phaseid);
             return redirect('/eci/countnominationfilter/'.base64_encode($phaseid)); 
             }        
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
     
    }


    public function countnominationfilter(Request $request,$phaseid){


       try{

         // $users=Session::get('admin_login_details');
          $user = Auth::user();   
           $data = [];
         // if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;
 $phaseid         = base64_decode($xss->clean_input($request['phaseid']));


                 if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }


            $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_offline          = 0;
                $TotalOnline            = 0;
                $TotalOffline           = 0;
                $TotalPC =0;

                   $user = Auth::user();  
                   $getphase = $this->getphase();
                   //dd($getphase);
               
                /*$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.pc_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."'  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2"; */

                //dd($phaseid);
                // $p1=1;$p2=2;$p3=3;$p4=4;$p5=5;$p6=6;$p7=7;$p8=8;$p9=9;$p10=10;  //1,6,10,3,5,7,8,9
                $p1=1;$p2=10;
                  if($phaseid==1){
                      $phaseid=$p1.','.$p2;
                  }
//dd($phaseid);
  

$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`PHASE_NO`IN ($phaseid) AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";
//dd($contestingNominationcandfilter);


//dd($contestingNominationcandfilter);



          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
        //  dd($EciPhaseInfoData);
        
          foreach ($EciPhaseInfoData as $key=>$lis) {

      

          
          //dd($lis);

          $onlinnomination_count        = $this->report_model->count_nomination_phase(1, $lis->ST_CODE,$phaseid);
          $offline_count     = $this->report_model->count_nomination_phase(0, $lis->ST_CODE,$phaseid);
          $electionDate     = $this->report_model->electionDate_phase($lis->ST_CODE,$phaseid);
          $TotalOnline += count($onlinnomination_count);
          $TotalOffline += count($offline_count);

           $pc_count        = $this->report_model->count_ac_wise($lis->ST_CODE,$phaseid);
           $TotalPC += $pc_count;
        //dd($electionDate);
       // $electionDate[0]->LDT_IS_NOM;
         
          
          $total_online     = count($onlinnomination_count);
          $total_offline    = $offline_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;

         $newDate = Carbon::createFromFormat('Y-m-d',$electionDate[0]->LDT_IS_NOM)
                                    ->format('d-m-Y');
                                   // dd($newDate);
          $results[] = [
            'label'              => $lis->ST_NAME,
            'phase'              => $lis->StatePHASE_NO,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            //'const_no'           => $lis['code'],
           // 'const_name'         => $lis['name'],
            'online_nomination'  => $onlinnomination_count,
            'offline_nomination' => $offline_count,
            'total_perct'   => "sd",
            'LDT_IS_NOM'  =>  $newDate,
            'pc_count'  => $pc_count,
            
          ];                        
    }
    //dd($TotalOffline);
      

          $data['results']    =  $results;
          $data['onlineCount']=  $TotalOnline;
          $data['offlineCount']=  $TotalOffline;
          $data['getphase'] = $getphase;
           $data['phaseid']  =$phaseid;
           $data['TotalPC']= $TotalPC;
          $data['user_data']  = Auth::user();




                  
                return view($this->view_path.'.report.nomination_count_statewise_filter', $data);            

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
    

    }



// End Function

      public function countnominationfilter_pdf(Request $request,$phaseid){


       try{

         // $users=Session::get('admin_login_details');
          $user = Auth::user();   
           $data = [];
         // if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;
 $phaseid         = base64_decode($xss->clean_input($request['phaseid']));


                 if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }


            $data['states'] = [];
              foreach(StateModel::get_states() as $result){
                $data['states'][] = [
                    'code' => base64_encode($result->ST_CODE),
                    'name' => $result->ST_NAME,
                ];
              }
                
                $results                = [];
                $total_online           = 0;
                $total_offline          = 0;
                $TotalOnline            = 0;
                $TotalOffline           = 0;
                $TotalPC =0;

                   $user = Auth::user();  
                   $getphase = $this->report_model->getphase();
               
                $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.StatePHASE_NO=c.state_phase_no AND c.pc_no=e.`CONST_NO` AND `party_id`!=1180 AND e.`StatePHASE_NO`='".$phaseid."'  AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 2  ORDER BY 2";

                // $p1=1;$p2=2;$p3=3;$p4=4;
                //  if($phaseid==1){
                //      $phaseids=$p1.','.$p2;
                //  }elseif($phaseid==2)
                //  {$phaseids=$p3.','.$p4; }
                //  elseif($phaseid==3)
                //     { $phaseids=5;}
                //  elseif($phaseid==4){ $phaseids=6; }
                //  elseif($phaseid==5){$phaseids=7; }
                //  elseif($phaseid==6){ $phaseids=8;}
                //  elseif($phaseid==7){ $phaseids=9;}   

  
//dd($phaseid);
$p1=1;$p2=10;
                  if($phaseid==1){
                      $phaseid=$p1.','.$p2;
                  }
//dd($phaseid);
  

$contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`PHASE_NO`IN ($phaseid) AND e.`election_id`=".$user->election_id." GROUP BY 2  ORDER BY  s.ST_NAME ASC";

//dd($contestingNominationcandfilter);



          $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
        
          foreach ($EciPhaseInfoData as $key=>$lis) {

      

          
          //dd($filter_data);

          $onlinnomination_count        = $this->report_model->count_nomination_phase(1, $lis->ST_CODE,$phaseid);
          $offline_count     = $this->report_model->count_nomination_phase(0, $lis->ST_CODE,$phaseid);
          $electionDate     = $this->report_model->electionDate_phase($lis->ST_CODE,$phaseid);
          $TotalOnline += count($onlinnomination_count);
          $TotalOffline += count($offline_count);
         
       // $electionDate[0]->LDT_IS_NOM;
         
          
          $total_online     = count($onlinnomination_count);
          $total_offline    = $offline_count;
          $pc_count        = $this->report_model->count_ac_wise($lis->ST_CODE,$phaseid);
           $TotalPC += $pc_count;
         // $total_without_affidavit_nomination +=$total_without_affidavit_nomination_count;

$newDate = Carbon::createFromFormat('Y-m-d',$electionDate[0]->LDT_IS_NOM)
                                    ->format('d-m-Y');
                                   // dd($newDate);
          $results[] = [
            'label'              => $lis->ST_NAME,
              'phase'              => $lis->StatePHASE_NO,
          //  'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            //'const_no'           => $lis['code'],
           // 'const_name'         => $lis['name'],
            'online_nomination'  => $onlinnomination_count,
            'offline_nomination' => $offline_count,
            'total_perct'   => "sd",
            'LDT_IS_NOM'  =>  $newDate,
            'pc_count'  => $pc_count,
            
          ];                        
    }
    //dd($TotalOffline);
      
             $data['heading_title'] = 'Nomination Report';
          $data['results']    =  $results;
          $data['onlineCount']=  $TotalOnline;
          $data['offlineCount']=  $TotalOffline;
          $data['getphase'] = $getphase;
           $data['phaseid']  =$phaseid;
           $data['TotalPC']= $TotalPC;
          $data['user_data']  = Auth::user();

                  $pdf = \PDF::loadView('admin.ac.eci.report.nomination_count_statewise_filter_pdf',$data);
            return $pdf->download('NominationCount_report_'.date('d-m-Y').'_'.time().'.pdf');
          return view($this->view_path.'.report.nomination_count_statewise_filter', $data);


                  
                            

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
    

    }







public function getphase() {

   $get=DB::table('m_election_details')->groupBy('m_election_details.PHASE_NO')->orderBy('m_election_details.PHASE_NO','ASC')->get();

   return $get;




}























}  // end class