<?php namespace App\Http\Controllers\IndexCardReportsAC\DeFinalizeIndexCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB, Validator, Session, Redirect;
use App\models\Admin\AcModel;
use App\models\Admin\{ElectionModel, StateModel};
use App\models\Admin\IndexCardUploadModel;
use App\Classes\xssClean;
use App\models\Admin\IndexCardDeFinalizeModel;
use App\models\Admin\IndexCardFinalize;
use App\models\Admin\IndexcardLogModel;
use App\commonModel;
use PDF;
use Excel;
use App\models\indexcard\DeFinalize;

class IndexCardDeFinalizeController extends Controller {

  public $base          = '';
  public $folder        = '';
  public $action        = '';
  public $current_page  = '';
  public $ac_no         = 0;
  public $st_code       = 0;
  public $view_path     = "IndexCardReports.DeFinalizeIndexCard";
  public $definalize_access = false;

  public function __construct(){
    $role_id = 0;
    $this->xssClean = new xssClean;
	$this->commonModel = new commonModel();
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        $role_id = Auth::user()->role_id;
        if($role_id == '18'){
          $this->base         = 'roac';
          $this->action       = 'roac/indexcard/de-finalize-acs';
          $this->current_page = 'roac/indexcard/de-finalize-acs';
          $this->definalize_action = 'roac/indexcard/de-finalize-acs/post';
          $this->view_path    = '';
          $this->ac_no        = \Auth::user()->ac_no;
          $this->st_code      = \Auth::user()->st_code;
        }else if($role_id == '4'){
          $this->base         = 'acceo';
          $this->action       = 'acceo/indexcard/upload-indexcard/post';
          $this->current_page = 'acceo/indexcard/de-finalize-acs';
          $this->definalize_action = 'acceo/indexcard/de-finalize-acs/post';
          $this->view_path    = '';
          $this->st_code      = \Auth::user()->st_code;
        }else if($role_id == '27'){
          $this->base         = 'eci-index';
          $this->action       = 'eci-index/indexcard/de-finalize-acs/post';
          $this->current_page = 'eci-index/indexcard/de-finalize-acs';
          $this->definalize_action = 'eci-index/indexcard/de-finalize-acs/post';
          $this->view_path    = '';
        } else{
          $this->base         = 'eci';
          $this->action       = 'eci/indexcard/de-finalize-acs/post';
          $this->current_page = 'eci/indexcard/de-finalize-acs';
          $this->definalize_action = 'eci/indexcard/de-finalize-acs/post';
          $this->view_path    = '';
        }

        if(in_array($role_id,['7','27'])){
          $this->definalize_access = true;
        }

        return $next($request);
    });
  }


  public function get_complains_list(Request $request){
    $data                   = [];
      $filter                 = [];
      $data['ac_no']          = NULL;
      $data['st_code']        = NULL;
      $data['election_id']    = NULL;
      $data['custom_errors']  = [];
      if($request->has('election_id')){
        $data['election_id']       = $request->election_id;
        $filter['election_id']     = $data['election_id'];
      }

      if($request->has('ac_no')){
        $data['ac_no']       = $request->ac_no;
      } 

      if($request->has('st_code')){
        $data['st_code']       = $request->st_code;
      }     

      if(\Auth::user()->role_id == '18'){
        $data['ac_no']          = $this->ac_no;
        $data['st_code']        = $this->st_code;
        $filter['ac_no']        = $data['ac_no'];
        $filter['st_code']      = $data['st_code'];
      }

      if(\Auth::user()->role_id == '4'){
        $data['st_code']        = $this->st_code;
        $filter['st_code']      = $data['st_code'];
      }
      
      $data['action']         = url($this->action);
      $data['current_page']   = url($this->current_page);

      $data['heading_title']  = 'Index Card';
      $data['filter_buttons'] = [];

      //years
      $data['elections']      = [];
      $elections              = ElectionModel::get_current_elections();
      foreach ($elections as $key => $result) {
        $data['elections'][] = [
           'election_id'      => $result['ELECTION_ID'],
           'election_type'    => $result['ELECTION_TYPE'].'-'.$result['YEAR'],
        ];
      }

      $data['states'] = [];
      $states = StateModel::get_states_index();
      foreach ($states as $key => $iterate_state) {
         $data['states'][] = [
           'st_code' => $iterate_state->ST_CODE,
           'st_name' => $iterate_state->ST_NAME,
         ];
      }

      $acs          = [];
      foreach (StateModel::get_acs(['st_code' => $data['st_code']]) as $ac_result){
		  if(\Auth::user()->role_id == '18'){    
			if($ac_result['ac_no']==$this->ac_no){
				$acs[] = [
				  'ac_no'   => $ac_result['ac_no'],
				  'ac_name' => $ac_result['ac_name']
				];
			}		  
		  }else{
			  $acs[] = [
				  'ac_no'   => $ac_result['ac_no'],
				  'ac_name' => $ac_result['ac_name']
				];
		  }
      }
	  
	  //echo '<pre>'; print_r($acs); die;
	  	  
      $data['acs']      = $acs;
      $data['results']  = [];

      $results = IndexCardDeFinalizeModel::get_list(['ac_no' => $data['ac_no'], 'st_code' => $data['st_code']]);
     
       foreach ($results as $res_iterate) {

        $data['results'][] = [
            'st_code'           => $res_iterate->st_code,
            'ac_no'             => $res_iterate->ac_no,
            'definalize_action' => url($this->definalize_action),
            'definalize_access' => $this->definalize_access,
            'st_name'        => $res_iterate->st_name,
            'ac_name'        => $res_iterate->ac_name,
        ];

      } 
	  
	  
	   //echo '<pre>'; print_r($data['results']); die;

		$data['definalize_action_nomination'] = url('eci-index/indexcard/definalize-nomination');
		$data['definalize_action_counting'] = url('eci-index/indexcard/definalize-counting');

      //data elector cdec
 
      $data['user_data']  =   Auth::user();

      return view('IndexCardReports.DeFinalizeIndexCard.complain_indexcard_list',$data);
  }


  public function definalize_indexcard(Request $request){
    if($this->definalize_access){
      $filter = [];
      $filter['ac_no'] = $request->ac_no;
      $filter['st_code'] = $request->st_code;
	  
      try{
        IndexCardDeFinalizeModel::definalize_status($filter);
		
		$filter['reason'] = $request->reason;
		
        IndexcardLogModel::indexcard_definalize_log($filter);
         }catch(\Exception $e){
        Session::flash('status',0);
        Session::flash('flash-message','Please try again.');
        return Redirect::back()->withInput($request->all());
      }
      Session::flash('status',1);
      Session::flash('flash-message','Definalized successfully.');
    }
    return Redirect::back();
  }

	public function definalize_nomination(Request $request){
  //dd($request->all());
   if($this->definalize_access){
     $ele_details=$this->commonModel->election_details_cons($request->st_code,$request->ac_no,'AC');
     $filter = [];
     $filter['ac_no'] = $request->ac_no;
     $filter['const_no'] = $request->ac_no;
     $filter['st_code'] = $request->st_code;
     $filter['reason'] = $request->reason;
     $filter['election_id'] = @$ele_details->ELECTION_ID;
     $filter['const_type'] = 'AC';
     $filter['finalize_by'] = \Auth::user()->officername;
     $filter['message'] = @$request->comment;
     try{
        IndexcardLogModel::nomination_definalize_log($filter);
       candidate_definalize($filter);
     }catch(\Exception $e){
       Session::flash('status',0);
       Session::flash('flash-message','Please try again.');
       return Redirect::back()->withInput($request->all());
     } 
     Session::flash('status',1);
     Session::flash('flash-message','Nomination Definalized Successfully.');
   }
   return Redirect::back();
 }
 public function definalize_counting(Request $request){
   if($this->definalize_access){        
      $ele_details=$this->commonModel->election_details_cons($request->st_code,$request->ac_no,'AC');
     $filter = [];
     $filter['ac_no'] = $request->ac_no;
     $filter['const_no'] = $request->ac_no;
     $filter['st_code'] = $request->st_code;
	 $filter['reason'] = $request->reason;
     $filter['election_id'] = $ele_details->ELECTION_ID;
     $filter['const_type'] = 'AC';
     $filter['finalize_by'] = \Auth::user()->officername;
     $filter['message'] = @$request->comment;
    try{
        IndexcardLogModel::counting_definalize_log($filter);
       counting_definalize($filter);
      }catch(\Exception $e){
       Session::flash('status',0);
       Session::flash('flash-message','Please try again.');
       return Redirect::back()->withInput($request->all());
     }
     Session::flash('status',1);
     Session::flash('flash-message','Counting Definalized Successfully.');
   }
   return Redirect::back();
 }
  public function deFinalizeAcs(Request $request){
		//try{
			$data['results'] = IndexCardDeFinalizeModel::definalize_acs();
			$data['user_data'] = \Auth::user();
		
			if($request->path() == "eci-index/indexcard/de-finalize-acs/pdf"){
				
			$pdf = PDF::loadView('IndexCardReports.DeFinalizeIndexCard.IndexCardDeFinalizePdf', $data);
			
				
			return $pdf->download('IndexCard De-Finalize Report.pdf');
			
		}else if($request->path() == "eci-index/indexcard/de-finalize-acs/excel"){
			
			return Excel::download(new DeFinalize($data['results']), 'IndexCard De-Finalize Report.xlsx');
			
		}else{
			return view('IndexCardReports.DeFinalizeIndexCard.IndexCardDeFinalize', $data);
		}	
			
		
		
       /* }catch(\Exception $e){
        return Redirect::back();
      } */
  }
  
}  // end class