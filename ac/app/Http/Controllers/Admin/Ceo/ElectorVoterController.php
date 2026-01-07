<?php namespace App\Http\Controllers\Admin\Ceo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB, Validator, Config, Session, Redirect;
use App\models\Admin\ElectorVoterModel;
use App\models\Admin\VoterModel;
use App\models\Admin\IndexcardLogModel;

class ElectorVoterController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'acceo';
  public $action        = 'acceo/elector/post';
  public $current_page  = 'acceo/elector/edit';
  public $view_path     = "admin.ac.ceo";

  public $action_voter        = 'acceo/voters/post';
  public $current_page_voter  = 'acceo/voters/edit';


  public function edit_elector_form(Request $request){



    //USER DATA MATCHING STARTS
    if(\Auth::user()->role_id == '19'){
      $this->action             = 'roac/elector/post';
      $this->current_page        = 'roac/elector/edit';
    }


	//dd(getElectionYear());



      $data                   = [];
      $data['ac_no']          = NULL;
      $data['custom_errors']  = [];
      
      if($request->has('ac_no')){
        $data['ac_no']       = $request->ac_no;
      }     


      if(\Auth::user()->role_id == '19'){
        $data['ac_no']       = \Auth::user()->ac_no;
      }
      
      $filter                 = [];
      $data['action']         = url($this->action);
      $data['current_page']   = url($this->current_page);
      $filter['state']        = Auth::user()->st_code;
      $filter['ac_no']        = $data['ac_no'];

      $data['is_finalize'] = VoterModel::get_finalize_pc(array_merge($filter,['st_code' => $filter['state']]));
      
      $data['heading_title']  = 'Index Card Electors/Voters Entry Form';
      $data['filter_buttons'] = [];

      //years
     
      $acs          = [];
      $acs          = ElectorVoterModel::get_acs($filter);

      $acs_for_ro   = [];
      if(\Auth::user()->role_id == '19'){
        foreach ($acs as $pc_result) {
          if($pc_result['ac_no'] == \Auth::user()->ac_no){
            $acs_for_ro[] = [
              'ac_no' => $pc_result['ac_no'],
              'ac_name' => $pc_result['ac_name']
            ];
          }
        }
        $acs = $acs_for_ro;
      }

      $data['acs']  = $acs;

      $data['results'] = [];
      //data elector cdec
      $results            = ElectorVoterModel::get_records($filter);
      foreach ($results as $key => $result) {
        $data['results'][] = [
          'id'      => $result['id'],
          'ac_no'   => $result['ac_no'],
          'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
          'electors_male'     => $result['electors_male'], 
          'electors_female'   => $result['electors_female'], 
          'electors_other'    => $result['electors_other'], 
          'electors_total'    => $result['electors_total'], 
          'gen_electors_male' => $result['gen_electors_male'], 
          'gen_electors_female' => $result['gen_electors_female'], 
          'gen_electors_other' => $result['gen_electors_other'], 
          'nri_male_electors' => $result['nri_male_electors'], 
          'nri_female_electors' => $result['nri_female_electors'], 
          'nri_third_electors' => $result['nri_third_electors'], 
          'service_male_electors' => $result['service_male_electors'], 
          'service_female_electors' => $result['service_female_electors'], 
          'service_third_electors' => $result['service_third_electors'], 
        ];
      }

      $data['user_data']  =   Auth::user();

      if($request->old('elector')){
        $data['results'] = $request->old('elector');
      }


      if($request->old('custom_errors')){
        $data['custom_errors'] = $request->old('custom_errors');
      }

 

      return view($this->view_path.'.elector.edit_elector_form',$data);

  }

  public function post_elector_form(Request $request){

    //USER DATA MATCHING STARTS
    if(\Auth::user()->role_id == '19' && ($request->ac_no !=  \Auth::user()->ac_no || !$request->has('ac_no'))){
      return Redirect::back()->with('error', 'You are not autorized for this AC.');
   }
   //USER DATA MATCHING ENDS

    if(!$request->has('elector')){
      return Redirect::back()->withInput($request->all()); 
    }

     $filter            = [];
   $filter['ac_no']   = $request->ac_no;
   $filter['st_code'] = \Auth::user()->st_code;

   if(VoterModel::get_finalize_pc($filter)){
    return Redirect::back()->with('error', 'You already finalized the AC.');
   }

    $errors   = [];
    $is_error = false;
    foreach ($request->elector as $key => $result) {
      foreach ($result as $second_key => $value) {

          if($second_key=='id'){
            $validate = ElectorVoterModel::get_validate([
                'id'      => $value,
                'ac_no'   => $request->ac_no,
                'st_code' => Auth::user()->st_code,
            ]);
            if($validate != '1'){
              $is_error = true;
              $errors[$key]['ac_name'] = "Please enter a valid ac no.";
            }
          }else if($second_key=='ac_name'){
       
          }else{

            if(!preg_match("/^[0-9]{1,6}$/", $value)){
              $errors[$key][$second_key] = "Please enter a valid integer.";
              $is_error = true;
            }else{
              $errors[$key][$second_key] = false;
            }
          }

      }
    }

    if(count($errors)>0){
      $request->merge(['custom_errors' => $errors]);
    }

    if($is_error){
      Session::flash('error','Please check your form data.');
    }

    if(!$is_error){
      try{
        DB::beginTransaction();
        foreach ($request->elector as $key => $result) {
           $data_to_be_updated = [
              'gen_electors_male' => $result['gen_electors_male'], 
              'gen_electors_female' => $result['gen_electors_female'], 
              'gen_electors_other' => $result['gen_electors_other'], 
              'nri_male_electors' => $result['nri_male_electors'], 
              'nri_female_electors' => $result['nri_female_electors'], 
              'nri_third_electors' => $result['nri_third_electors'], 
              'service_male_electors' => $result['service_male_electors'], 
              'service_female_electors' => $result['service_female_electors'], 
              'service_third_electors' => $result['service_third_electors'], 
          ];

          $filter_data = [
            'id' => $result['id'],
            'ac_no'   => $request->ac_no,
            'st_code' => Auth::user()->st_code,
          ];
          ElectorVoterModel::update_index_card_data($data_to_be_updated, $filter_data);
		  
		  $logdata = [
			  'main_id' 				=> $result['id'],
			  'ac_no'   				=> $request->ac_no,
			  'st_code' 				=> Auth::user()->st_code,
              'year'    				=> date('Y'),
              'gen_electors_male' 		=> $result['gen_electors_male'], 
              'gen_electors_female' 	=> $result['gen_electors_female'], 
              'gen_electors_other' 		=> $result['gen_electors_other'], 
              'nri_male_electors' 		=> $result['nri_male_electors'], 
              'nri_female_electors' 	=> $result['nri_female_electors'], 
              'nri_third_electors' 		=> $result['nri_third_electors'], 
              'service_male_electors' 	=> $result['service_male_electors'], 
              'service_female_electors' => $result['service_female_electors'], 
              'service_third_electors' 	=> $result['service_third_electors'], 
              'log_added_updated_at' 	=> date('Y-m-d'), 
              'log_updated_at' 			=> date('Y-m-d h:i:s'), 
              'log_updated_by' 			=> Auth::user()->officername
          ];
		  
        DB::table('electors_cdac_logs')->insert($logdata); 
		  
		  
        }

      }catch(\Exception $e){
        DB::rollback();
        Session::flash('error','Please check your form data.');
        return Redirect::back()->withInput($request->all());
      }
      DB::commit();
      Session::flash('success','Data has been updated.');
    }

    return Redirect::back()->withInput($request->all());
  
  }


  public function edit_voters_form(Request $request){

    //USER DATA MATCHING STARTS
    if(\Auth::user()->role_id == '19'){
      $this->action_voter        = 'roac/voters/post';
      $this->current_page_voter  = 'roac/voters/edit';
      
    }

    $data                   = [];


      $data['ac_no']          = NULL;
      $data['custom_errors']  = [];
      
      if($request->has('ac_no')){
        $data['ac_no']       = $request->ac_no;
      }   

      if(\Auth::user()->role_id == '19'){
        $data['ac_no']       = \Auth::user()->ac_no;
      }

      $filter                 = [];
      $data['action']         = url($this->action_voter);
      $data['current_page']   = url($this->current_page_voter);
      $filter['state']        = Auth::user()->st_code;
      $filter['ac_no']        = $data['ac_no'];

      $data['is_finalize'] = VoterModel::get_finalize_pc(array_merge($filter,['st_code' => $filter['state']]));
      
      $data['heading_title']  = 'Index Card Electors/Voters Entry Form';
      $data['filter_buttons'] = [];


      $acs          = [];
      $acs          = ElectorVoterModel::get_acs($filter);

      $acs_for_ro   = [];
      if(\Auth::user()->role_id == '19'){
        foreach ($acs as $pc_result) {
          if($pc_result['ac_no'] == \Auth::user()->ac_no){
            $acs_for_ro[] = [
              'ac_no' => $pc_result['ac_no'],
              'ac_name' => $pc_result['ac_name']
            ];
          }
        }
        $acs = $acs_for_ro;
      }

      $data['acs']  = $acs;

      $data['results'] = [];
      //data elector cdec


      //get electors
      $total_nri_and_general_male = 0;
      $total_nri_and_general_female = 0;
      $total_nri_and_general_other = 0;
      $total_general_voters       = 0;
      $total_nri_voters           = 0;
      $total_nri_and_general      = 0;



      $data['total_nri_and_general_male']   = $total_nri_and_general_male;
      $data['total_nri_and_general_female'] = $total_nri_and_general_female;
      $data['total_nri_and_general_other']  = $total_nri_and_general_other;
      $data['total_nri_voters']             = $total_nri_voters;
      $data['total_nri_and_general']        = $total_nri_and_general;



        $object = VoterModel::get_voter_by_pc([
          'st_code' => $filter['state'],
          'ac_no' => $filter['ac_no']
        ]);
        $data['object'] = [
          'ac_no'   => $filter['ac_no'],
          'general_male_voters'   => $object['general_male_voters'],  
          'general_female_voters' => $object['general_female_voters'],  
          'general_other_voters'  => $object['general_other_voters'], 
          'total_general_voters'  => $object['general_male_voters']+$object['general_female_voters']+$object['general_other_voters'],
          'nri_male_voters'   => $object['nri_male_voters'],  
          'nri_female_voters' => $object['nri_female_voters'],  
          'nri_other_voters'              => $object['nri_other_voters'],  
          'test_votes_49_ma'              => $object['test_votes_49_ma'],  
          'votes_not_retreived_from_evm'  => $object['votes_not_retreived_from_evm'],  
          'votes_counted_from_evm'  => $object['votes_counted_from_evm'],  
          'votes_counted_from_vvpat'  => $object['votes_counted_from_vvpat'],  
          'rejected_votes_due_2_other_reason' => $object['rejected_votes_due_2_other_reason'],  
          'service_postal_votes_under_section_8' => $object['service_postal_votes_under_section_8'],  
          'service_postal_votes_gov' => $object['service_postal_votes_gov'],  
          'postal_votes_rejected' => $object['postal_votes_rejected'],  
          'proxy_votes' => $object['proxy_votes'],  
          'tendered_votes' => $object['tendered_votes'],  
          'total_polling_station_s_i_t_c' => $object['total_polling_station_s_i_t_c'],  
          'date_of_repoll' => $object['date_of_repoll'],  
          'no_poll_station_where_repoll' => $object['no_poll_station_where_repoll'],  
          'is_by_or_countermanded_election' => $object['is_by_or_countermanded_election'],  
          'reasons_for_by_or_countermanded_election' => $object['reasons_for_by_or_countermanded_election'],
        ];
		
		//result declared date
      $data['object']['result_declared_date'] = VoterModel::get_result_declared_date($filter['state'], $filter['ac_no']);
      if($request->old('result_declared_date')){
        $data['object']['result_declared_date'] = $request->old('result_declared_date');
      }
      //end result declared date

      $data['user_data']  =   Auth::user();

      if($request->old('ac_no')){
        $data['object']['ac_no'] = $request->old('ac_no');
      }
     
      if($request->old('general_male_voters')){
        $data['object']['general_male_voters'] = $request->old('general_male_voters');
      }
      if($request->old('general_female_voters')){
        $data['object']['general_female_voters'] = $request->old('general_female_voters');
      }
      if($request->old('general_other_voters')){
        $data['object']['general_other_voters'] = $request->old('general_other_voters');
      }
      if($request->old('nri_male_voters')){
        $data['object']['nri_male_voters'] = $request->old('nri_male_voters');
      }
      if($request->old('nri_female_voters')){
        $data['object']['nri_female_voters'] = $request->old('nri_female_voters');
      }
      if($request->old('nri_other_voters')){
        $data['object']['nri_other_voters'] = $request->old('nri_other_voters');
      }
      if($request->old('test_votes_49_ma')){
        $data['object']['test_votes_49_ma'] = $request->old('test_votes_49_ma');
      }
      if($request->old('votes_not_retreived_from_evm')){
        $data['object']['votes_not_retreived_from_evm'] = $request->old('votes_not_retreived_from_evm');
      }
	  
	  if($request->old('votes_counted_from_evm')){
        $data['object']['votes_counted_from_evm'] = $request->old('votes_counted_from_evm');
      }
	  
	  if($request->old('votes_counted_from_vvpat')){
        $data['object']['votes_counted_from_vvpat'] = $request->old('votes_counted_from_vvpat');
      }
	  
      if($request->old('rejected_votes_due_2_other_reason')){
        $data['object']['rejected_votes_due_2_other_reason'] = $request->old('rejected_votes_due_2_other_reason');
      }
      if($request->old('service_postal_votes_under_section_8')){
        $data['object']['service_postal_votes_under_section_8'] = $request->old('service_postal_votes_under_section_8');
      }
      if($request->old('service_postal_votes_gov')){
        $data['object']['service_postal_votes_gov'] = $request->old('service_postal_votes_gov');
      }
      if($request->old('postal_votes_rejected')){
        $data['object']['postal_votes_rejected'] = $request->old('postal_votes_rejected');
      }
      if($request->old('proxy_votes')){
        $data['object']['proxy_votes'] = $request->old('proxy_votes');
      }
      if($request->old('tendered_votes')){
        $data['object']['tendered_votes'] = $request->old('tendered_votes');
      }
      if($request->old('total_polling_station_s_i_t_c')){
        $data['object']['total_polling_station_s_i_t_c'] = $request->old('total_polling_station_s_i_t_c');
      }
      if($request->old('date_of_repoll')){
        $data['object']['date_of_repoll'] = $request->old('date_of_repoll');
      }
      if($request->old('no_poll_station_where_repoll')){
        $data['object']['no_poll_station_where_repoll'] = $request->old('no_poll_station_where_repoll');
      }
      if($request->old('is_by_or_countermanded_election')){
        $data['object']['is_by_or_countermanded_election'] = $request->old('is_by_or_countermanded_election');
      }
      if($request->old('reasons_for_by_or_countermanded_election')){
        $data['object']['reasons_for_by_or_countermanded_election'] = $request->old('reasons_for_by_or_countermanded_election');
      }
      if($request->old('custom_errors')){
        $data['custom_errors'] = $request->old('custom_errors');
      }

      return view($this->view_path.'.elector.edit_voters_form',$data);

  }

  public function post_voters_form(Request $request){

    //USER DATA MATCHING STARTS
    if(\Auth::user()->role_id == '19' && ($request->ac_no !=  \Auth::user()->ac_no || !$request->has('ac_no'))){
      return Redirect::back()->with('error', 'You are not autorized for this PC.');
   }
   //USER DATA MATCHING ENDS

   $filter            = [];
   $filter['ac_no']   = $request->ac_no;
   $filter['st_code'] = \Auth::user()->st_code;

   if(VoterModel::get_finalize_pc($filter)){
    return Redirect::back()->with('error', 'You already finalized the AC.');
   }

//dd($request->all());

    $errors   = [];
    $is_error = false;

    foreach ($request->except('_token') as $key => $result) {

          if($key == 'no_poll_station_where_repoll' || $key == 'reasons_for_by_or_countermanded_election'){
              if(!preg_match("/^[a-zA-Z0-9-_,.\s&\/ ]{1,255}$/", $result) && strlen($result)>0){
                $errors[$key] = "Please enter a valid value.";
                $is_error = true;
              }
          }else if($key == 'date_of_repoll'){

            if(strlen($result)>0){
              $date_array = explode(',',$result);
              foreach ($date_array as $date_value) {
                if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",trim($date_value))) {
                  $errors[$key] = "Please enter a valid date.";
                  $is_error = true;
                }
              }
            }

          }else if($key == 'result_declared_date'){

            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$request->result_declared_date)) {
                  $errors['result_declared_date'] = "Please enter a valid date.";
                  $is_error = true;
            }

          }else{

            if(!preg_match("/^[0-9]{1,6}$/", $result)){
              $errors[$key] = "Please enter a valid integer.";
              $is_error = true;
            }else{
              $errors[$key] = false;
            }
          }
    }

    if(count($errors)>0){
      $request->merge(['custom_errors' => $errors]);
    }

    if($is_error){
      Session::flash('error','Please check your form data.');
    }


    if(!$is_error){
      try{
        DB::beginTransaction();
       
           $data_to_be_updated = [
              "general_male_voters" => $request->general_male_voters,
              "general_female_voters" => $request->general_female_voters,
              "general_other_voters" => $request->general_other_voters,
              "nri_male_voters" => $request->nri_male_voters,
              "nri_female_voters" => $request->nri_female_voters,
              "nri_other_voters" => $request->nri_other_voters,
              "test_votes_49_ma" => $request->test_votes_49_ma,
              "votes_not_retreived_from_evm" => $request->votes_not_retreived_from_evm,
              "votes_counted_from_evm" => $request->votes_counted_from_evm,
              "votes_counted_from_vvpat" => $request->votes_counted_from_vvpat,
              "rejected_votes_due_2_other_reason" => $request->rejected_votes_due_2_other_reason,
              "service_postal_votes_under_section_8" => $request->service_postal_votes_under_section_8,
              "service_postal_votes_gov" => $request->service_postal_votes_gov,
              "proxy_votes" => $request->proxy_votes,
              "total_polling_station_s_i_t_c" => $request->total_polling_station_s_i_t_c,
              "date_of_repoll" => $request->date_of_repoll,
              "no_poll_station_where_repoll" => ($request->no_poll_station_where_repoll)?$request->no_poll_station_where_repoll:'',
              "is_by_or_countermanded_election" => $request->is_by_or_countermanded_election,
              "reasons_for_by_or_countermanded_election" => ($request->reasons_for_by_or_countermanded_election)?$request->reasons_for_by_or_countermanded_election:'',
              "submitted_by"  => \Auth::user()->officername,
          ];
        

          $filter_data = [
            'ac_no'   => $request->ac_no,
            'st_code' => Auth::user()->st_code,
          ];
       
          VoterModel::update_index_card_pc_data($data_to_be_updated, $filter_data);
		  
		  VoterModel::update_result_date($request->result_declared_date, [
          'ac_no'   => $request->ac_no,
          'st_code' => Auth::user()->st_code
        ]);
        
		
		$logdata = [
			  'main_id' 						=> 0,
			  'ac_no'   						=> $request->ac_no,
			  'st_code' 						=> Auth::user()->st_code,
              'year'    						=> date('Y'),
              "general_male_voters" 			=> $request->general_male_voters,
              "general_female_voters" 			=> $request->general_female_voters,
              "general_other_voters" 			=> $request->general_other_voters,
              "nri_male_voters" 				=> $request->nri_male_voters,
              "nri_female_voters" 				=> $request->nri_female_voters,
              "nri_other_voters" 				=> $request->nri_other_voters,
              "test_votes_49_ma" 				=> $request->test_votes_49_ma,
              "votes_not_retreived_from_evm" 	=> $request->votes_not_retreived_from_evm,
              "votes_counted_from_evm" 			=> $request->votes_counted_from_evm,
              "votes_counted_from_vvpat" 		=> $request->votes_counted_from_vvpat,
              "rejected_votes_due_2_other_reason" 		=> $request->rejected_votes_due_2_other_reason,
              "service_postal_votes_under_section_8" 	=> $request->service_postal_votes_under_section_8,
              "service_postal_votes_gov" 				=> $request->service_postal_votes_gov,
              "proxy_votes" 							=> $request->proxy_votes,
              "total_polling_station_s_i_t_c" 			=> $request->total_polling_station_s_i_t_c,
              "date_of_repoll" 							=> $request->date_of_repoll,
              "no_poll_station_where_repoll" 			=> ($request->no_poll_station_where_repoll)?$request->no_poll_station_where_repoll:'',
              "is_by_or_countermanded_election" 		=> $request->is_by_or_countermanded_election,
              "reasons_for_by_or_countermanded_election" => ($request->reasons_for_by_or_countermanded_election)?$request->reasons_for_by_or_countermanded_election:'',
              "result_declared_date" 					=> ($request->result_declared_date)?$request->result_declared_date:'',
              "submitted_by"  							=> \Auth::user()->officername,
			  'log_added_updated_at' 					=> date('Y-m-d'),
              'log_updated_at' 							=> date('Y-m-d h:i:s'), 
              'log_updated_by' 							=> Auth::user()->officername, 
          ];
        
		DB::table('electors_cdac_other_information_logs')->insert($logdata);
		

      }catch(\Exception $e){
        DB::rollback();
        Session::flash('error','Please check your form data.');
        return Redirect::back()->withInput($request->all());
      }
      DB::commit();
      Session::flash('success','Data has been updated.');
    }

    return Redirect::back()->withInput($request->all());
  }

   public function finalize(Request $request){
    $data = [];
    $data['heading_title']  = 'Finalize Indexcard Entry';
    $data['filter_buttons'] = [];
    $filter = [];
    $filter['ac_no']    = \Auth::user()->ac_no;
    $filter['st_code']  = \Auth::user()->st_code; 
    $data['is_finalize']  = VoterModel::get_finalize_pc($filter);
    $data['user_data']      = Auth::user();
    return view($this->view_path.'.indexcard.finalize',$data);
  }

  public function post_finalize(Request $request){
    $filter = [];
	
	//dd('aaaa');
	
	
 //echo '<pre>';print_r($request->all());die;
	
	/* return \Response::json([
        'status' => true,
        'message' => "Aaaaaaaa"
    ]);

 */
	
    if(\Auth::user()->role_id == '19'){
      $filter['ac_no']    = \Auth::user()->ac_no;
      $filter['st_code']  = \Auth::user()->st_code;
      $filter['finalize_by_ro']       = 1; 
      $filter['ro_name']       		  = $request->ro_name; 
      $filter['ro_certificate']       = $request->ro_certificate; 
    }

    $is_data_exist = VoterModel::check_indexcard_pc_entry($filter);
    if(!$is_data_exist){
      /* return \Response::json([
        'status' => false,
        'message' => "Please update the indexcard data first."
      ]); */
	  
	  return Redirect::back()->with('error', 'Please update the indexcard data first.'); 
	  
	  
    }

    $is_finalize = VoterModel::get_finalize_pc($filter);
	
	
	$is_polling_station = VoterModel::select('total_polling_station_s_i_t_c')->where('st_code',$filter['st_code'])->where('ac_no',$filter['ac_no'])->first();
	
	$is_counting_finalize = DB::table('winning_leading_candidate')->select('status')->where('st_code',$filter['st_code'])->where('ac_no',$filter['ac_no'])->first();
	
	
	 if(@$is_counting_finalize->status == '0'){  
	  return Redirect::back()->with('error', 'Please finalize the Counting Module first.');   
    }
	
	if(@$is_polling_station->total_polling_station_s_i_t_c == 0){	  
	  return Redirect::back()->with('error', 'Please update the Total Polling Stations In indexcard data Form.');   
    }
//	dd($is_polling_station->total_polling_station_s_i_t_c);
	
    if($is_finalize){
      /* return \Response::json([
        'status' => false,
        'message' => "AC already finalized."
      ]); */
	  
	  return Redirect::back()->with('error', 'AC already finalized.'); 
    }


    $result = VoterModel::update_finalize_pc($filter);
    if(!$result){
      /* return \Response::json([
        'status' => false,
        'message' => "Please try again."
      ]); */
	  
	  
	  return Redirect::back()->with('error', 'Please try again.'); 
    }

    IndexcardLogModel::add_log(array_merge($filter, ['finalize' => 1]));
	
	
	return Redirect::to('roac/index-card')->with('success', 'Index Card Finalized successfully.'); 
	

    Session::flash('success','Indexcard finalized successfully.');
	
	
    return \Response::json([
        'status' => true,
        'message' => "AC already finalized"
    ]);
	
	
	return \Response::json([
        'status' => false,
        'message' => "Please try again."
    ]);


  }

  public function get_ceo_finalize(Request $request){


      $data                   = [];


      $filter                 = [];
      $data['action']         = url($this->action_voter);
      $filter['state']        = Auth::user()->st_code;      
      $data['heading_title']  = 'Index Card Electors/Voters Entry Form';
      $data['filter_buttons'] = [];

      $data['results']          = [];
      $acs              = [];
      $acs          = ElectorVoterModel::get_finalize_acs($filter);
      foreach ($acs as $ac) {
        $data['results'][] = [
          'id'          => $ac['id'],
          'ac_no'       => $ac['ac_no'],
          'ac_name'     => $ac['ac_name'],
          'st_code'     => $ac['st_code'],
          'text_finalize'   => ($ac['finalize'])?'Yes':'No',
          'finalize'        => $ac['finalize'],
          'finalize_by_ro'  => $ac['finalize_by_ro'],
          'finalize_by_ceo' => $ac['finalize_by_ceo'],
        ];
      }
      $data['user_data']      = Auth::user();
      return view($this->view_path.'.indexcard.ceo-finalize',$data);
  }


  public function post_ceo_finalize(Request $request){
    $filter = [];
    if(\Auth::user()->role_id != '4'){
    
	return Redirect::back()->with('error', 'You have no access.');
    
      // return \Response::json([
        // 'status' => false,
        // 'message' => "You have no access."
      // ]);
    }

    $validator = \Validator::make($request->all(),[
      'id'          => 'required|exists:electors_cdac_other_information,id',
      'finalized'   => 'required|in:0,1',
    ]);

    if($validator->fails()){
    
	return Redirect::back()->with('error', 'Please referesh and try again.');
    
      // return \Response::json([
        // 'status' => false,
        // 'message' => "Please referesh and try again."
      // ]);
    }

    if($request->finalized == '1'){
      $result = VoterModel::update_finalize_ceo([
        'finalize' 			=> $request->finalized,
        'id'       			=> $request->id,
        'st_code'  			=> \Auth::user()->st_code,
        'year'     			=> date('Y'),
		'ceo_name'      	=> $request->ceo_name,
        'ceo_certificate'   => $request->ceo_certificate
      ]);
    }else{
      $result = VoterModel::update_definalize_ceo([
        'finalize' 			=> $request->finalized,
        'id'       			=> $request->id,
        'st_code'  			=> \Auth::user()->st_code
      ]);
    }
    if(!$result){
    
    
	return Redirect::back()->with('error', 'Please try again.');
    
    
      // return \Response::json([
        // 'status' => false,
        // 'message' => "Please try again."
      // ]);
    }

    IndexcardLogModel::add_ceo_log([
      'finalize' => $request->finalized,
      'id'       => $request->id,
      'st_code'  => \Auth::user()->st_code,
      'year'     => date('Y')
    ]);
    
    
    
    return Redirect::to('acceo/indexcard/finalize')->with('success', 'Index Card Finalized successfully.');

    Session::flash('success','Indexcard finalized successfully.');
    // return \Response::json([
        // 'status' => true,
        // 'message' => "AC already finalized"
     // ]);


  }


}  // end class