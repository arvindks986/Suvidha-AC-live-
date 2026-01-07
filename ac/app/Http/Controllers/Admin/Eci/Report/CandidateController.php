<?php namespace App\Http\Controllers\Admin\Eci\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\commonModel;  
use App\models\Admin\PollDayModel;
use App\models\Admin\EndOfPollModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\models\Admin\CandidateModel;
use App\models\Admin\CandidateNominationModel;

//current

class CandidateController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/report/candidate';
  public $action_ac     = 'eci/report/voting/candidate-ac';
  public $view_path     = "admin.ac.eci";

  public function get_candidates(Request $request){
    
      $data = [];
      $request_array = []; 

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      $data['ac_no'] = NULL;
      if($request->has('ac_no')){
        $data['ac_no'] = $request->ac_no;
        $request_array[] = 'ac_no='.$request->ac_no;
      }

      if(\Auth::user()->role_id == '4'){
        $data['state']    = \Auth::user()->st_code;
        $request_array[]  = 'state='.\Auth::user()->st_code;
        $this->action_state  = 'acceo/report/candidate';
        $this->action_ac     = 'acceo/report/voting/candidate-ac';
      }

      if(\Auth::user()->role_id == '19'){
        $data['state']    = \Auth::user()->st_code;
        $request_array[]  = 'state='.\Auth::user()->st_code;
        $data['ac_no']    = \Auth::user()->ac_no;
        $request_array[]  = 'ac_no='.\Auth::user()->ac_no;
        $this->action_state  = 'roac/report/candidate';
        $this->action_ac     = 'roac/report/voting/candidate-ac';
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'List of Nominated Candidates';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }

      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }

      $data['filter_buttons'] = $title_array;

      $states = StateModel::get_states(); 
      $data['states'] = [];
      foreach($states as $result){
        if(\Auth::user()->role_id=='4' || \Auth::user()->role_id=='19' ){
          if(\Auth::user()->st_code == $result->ST_CODE){
            $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
            ];
          }
        }else{
          $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
          ];
        }
      }

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];


      $data['action']         = url($this->action_state);

      $results                = [];
      $filter_election = [
        'state'         => $data['state'],
        'ac_no'         => $data['ac_no']
      ];

      $data['acs']      = [];
      $acs = AcModel::get_acs();
      foreach ($acs as $key => $ac) {

        if(\Auth::user()->role_id=='4' || \Auth::user()->role_id == '19'){
          if(\Auth::user()->st_code == $ac->st_code && \Auth::user()->role_id == '19' && \Auth::user()->ac_no == $ac->ac_no){
            $data['acs'][] = [
              'ac_no' => $ac->ac_no,
              'ac_name' => $ac->ac_name,
              'st_code' => $ac->st_code
            ];
          }else if(\Auth::user()->st_code == $ac->st_code && \Auth::user()->role_id == '4'){
            $data['acs'][] = [
              'ac_no' => $ac->ac_no,
              'ac_name' => $ac->ac_name,
              'st_code' => $ac->st_code
            ];
          }
        }else{
          $data['acs'][] = [
              'ac_no' => $ac->ac_no,
              'ac_name' => $ac->ac_name,
              'st_code' => $ac->st_code
          ];
        }
      }

      if($data['state'] && $data['ac_no']){
        $results_object = CandidateModel::get_candidates($filter_election);
        foreach ($results_object as $result) {
          $text_status    = '';
          $status_array   = [];
          $status_results = CandidateNominationModel::get_nomination_status([
            'candidate_id'  => $result['candidate_id'],
            'ac_no'         => $data['ac_no'],
            'state'         => $data['state'],
          ]);



          $status_result  = [];
          foreach ($status_results as $status_res) {
            if($status_res['application_status'] == '6' && $status_res['finalaccepted'] == '1'){
              $status_result[] = 'final';
            }else{
              $status_result[] = $status_res->application_status;
            }
          }
        
          if(in_array('final',$status_result)){
            $text_status = 'contesting';
          }else if(in_array('5',$status_result)){
            $text_status = 'Withdrawn';
          }else if(in_array('6',$status_result)){
            $text_status = 'Accepted';
          }else if(in_array('4',$status_result)){
            $text_status = 'Rejected';
          }else{

          }
          foreach ($status_result as $status_key => $status_r) {
            if(in_array('final',$status_result)){
              $text_status = 'contesting';
            }else if(in_array('5',$status_result)){
              $text_status = 'Withdrawn';
            }else if(in_array('4',$status_result)){
              $text_status = 'Rejected';
            }else if(in_array('6',$status_result)){
              $text_status = 'Accepted';
            }else{

            }
            $status_array[]     = $text_status;
          }

          if($text_status == 'contesting'){
            $status_array = ['contesting'];
          }

          $results[] = [
            'candidate_id'      => $result->candidate_id,
            'new_srno'          => $result->new_srno,
            'gender'          => $result->cand_gender,
            'name'              => $result->cand_name,
            'total_nomination'  => count($status_results),
            'status'            => implode(', ', $status_array),
            'final_status'      => $text_status,
          ];
        }   
      }

      $data['results']    =   $results;
      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.report.candidate.candidates', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

 

}  // end class