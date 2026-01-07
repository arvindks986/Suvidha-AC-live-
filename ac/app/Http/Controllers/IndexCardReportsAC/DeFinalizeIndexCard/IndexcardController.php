<?php namespace App\Http\Controllers\IndexCardReportsAC\DeFinalizeIndexCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB, Validator, Session, Redirect;
use App\models\Admin\AcModel;
use App\models\Admin\ElectionModel;
use App\models\Admin\IndexCardUploadModel;

class IndexcardController extends Controller {
  
  public $base          = '';
  public $folder        = '';
  public $action        = '';
  public $current_page  = '';
  public $pc_no         = 0;
  public $view_path     = "IndexCardReports.DeFinalizeIndexCard";

  public function __construct(){
    $role_id = 0;
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        $role_id = Auth::user()->role_id;
        if($role_id == '18'){
          $this->base         = 'roac';
          $this->action       = 'roac/indexcard/upload-indexcard/post';
          $this->current_page = 'roac/indexcard/upload-indexcard';
          $this->view_path    = '';
          $this->pc_no        = \Auth::user()->pc_no;
        }else if($role_id == '4'){
          $this->base         = 'acceo';
          $this->action       = 'acceo/indexcard/upload-indexcard/post';
          $this->current_page = 'acceo/indexcard/upload-indexcard';
          $this->view_path    = '';
        }else if($role_id == '27'){
          $this->base         = 'eci-index';
          $this->action       = 'eci-index/indexcard/upload/post';
          $this->current_page = 'eci-index/indexcard/upload/edit';
          $this->view_path    = '';
        } else{
          $this->base         = 'eci';
          $this->action       = 'eci/indexcard/upload/post';
          $this->current_page = 'eci/indexcard/upload/edit';
          $this->view_path    = '';
        }
        return $next($request);
    });
  }


  public function upload_indexcard_request(Request $request){


      $data                   = [];
      $filter                 = [];
      $data['pc_no']          = NULL;
      $data['election_id']    = NULL;
      $data['custom_errors']  = [];
      if($request->has('election_id')){
        $data['election_id']       = $request->election_id;
      }

      if($request->has('ac_no')){
        $data['ac_no']       = $request->ac_no;
      }    

      $data['action']         = url($this->action);
      $data['current_page']   = url($this->current_page);
      $filter['st_code']      = Auth::user()->st_code; 

      if(\Auth::user()->role_id == '18'){
        $data['ac_no']          = Auth::user()->ac_no;
        $filter['ac_no']        = $data['ac_no'];
        $data['current_page']   = url($this->current_page);
      }
      
      $data['heading_title']  = 'Index Card Finalize Request Form - Draft Report';
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

      $pcs          = [];
      foreach (PcModel::get_pcs($filter) as $pc_result){
        $pcs[] = [
          'ac_no'   => $pc_result['ac_no'],
          'ac_name' => $pc_result['ac_name']
        ];
      }
      $data['acs']      = $acs;
      $data['results']  = [];
      //data elector cdec
 
      $data['user_data']  =   Auth::user();

      if($request->old('elector')){
        $data['results'] = $request->old('elector');
      }

      if($request->old('custom_errors')){
        $data['custom_errors'] = $request->old('custom_errors');
      }

      return view('IndexCardReports.DeFinalizeIndexCard.upload_indexcard',$data);

  }

  public function post_upload_indexcard_file(Request $request){

    $data     = $request->all();
    $messages = [ 
      'indexcard.required' => 'Please upload the valid pdf affidavit',
      'indexcard.mimes' => 'Please upload the valid pdf affidavit',
      'indexcard.max' => 'Please upload the maximum size 10 MB',
    ];
    $rules = [
      'pc_no'     => 'required|exists:m_pc,PC_NO',
      'indexcard' => 'mimes:pdf|max:10000',
      'election_id' => 'required|exists:election_master,election_id'
    ];
    $validator = \Validator::make($data, $rules, $messages);
    if($validator->fails()){
      \Session::flash('status', 0);
      \Session::flash('flash-message', 'Please choose a valid pdf file.');
      return Redirect::back()->withInput($request->all());
    }

    $st_code      = \Auth::user()->st_code;
    $ac_no        = $request->ac_no;
    $file         = $request->file('indexcard');
    $year         = date('Y');
    $election_id  = $request->election_id;
    $is_approve = IndexCardUploadModel::get_total_approved([
      'st_code'     => $st_code,
      'ac_no'       => $ac_no,
      'election_id' => $election_id
    ]);
    if($is_approve > 0){
      \Session::flash('status', 0);
      \Session::flash('flash-message','Indexcard for this AC number - '.$ac_no.' is already uploaded and in progress/approved.');
      return Redirect::back()->withInput($request->all());
    }
    $ext = $request->file('indexcard')->getClientOriginalExtension();
    if($ext != "pdf" || !$request->file('indexcard')){
      \Session::flash('status', 0);
      \Session::flash('flash-message', 'Please choose a valid pdf file.');
      return Redirect::back()->withInput($request->all());
    }

    $newfile          = $st_code.date('Ymdhis');  
    $file_temp_name   = $newfile.'.'.$request->file('indexcard')->getClientOriginalExtension();

    if (!file_exists('uploads/indexcard')) {
      mkdir('uploads/indexcard', 0777, true);
    }

    if (!file_exists('uploads/indexcard/'.$year)) {
      mkdir('uploads/indexcard/'.$year, 0777, true);
    }

    if (!file_exists('uploads/indexcard/ac')) {
      mkdir('uploads/indexcard/ac', 0777, true);
    }

    $election_name = 'election_id_'.$election_id;
      
    if (!file_exists('uploads/indexcard/'.$year.'/'.$election_name)) {
      mkdir('uploads/indexcard/'.$year.'/'.$election_name, 0777, true);
    }

      if (!file_exists('uploads/indexcard/'.$year.'/'.$election_name.'/'.$st_code)) {
        mkdir('uploads/indexcard/'.$year.'/'.$election_name.'/'.$st_code, 0777, true);
      }

      if (!file_exists('uploads/indexcard/'.$year.'/'.$election_name.'/'.$st_code.'/'. $pc_no)) {
        mkdir('uploads/indexcard/'.$year.'/'.$election_name.'/'.$st_code.'/'. $pc_no, 0777, true);
      }

      $destination_path = 'uploads/indexcard/'.$year.'/pc/'.$election_name.'/'.$st_code .'/'. $pc_no;
      $file->move($destination_path,$file_temp_name);
      $file_name = $destination_path.'/'.$file_temp_name;

      try{
        DB::beginTransaction();
        IndexCardUploadModel::add_upload([
          'st_code'   => $st_code,
          'ac_no'     => $ac_no,
          'file_name' => $file_name,
          'election_id' => $election_id
        ]);
        DB::commit();
      }catch(\Exception $e){
        DB::rollback();
        Session::flash('status',0);
        Session::flash('flash-message','Please check your form data.');
        return Redirect::back()->withInput($request->all());
      }
      Session::flash('status',1);
      Session::flash('flash-message','Data has been updated.');
      return Redirect::back()->withInput($request->all());
  
  }

  public function get_uploaded_indexcard(Request $request){

    $data           = [];
    $data['current_page'] = url($this->base.'/indexcard/get-uploaded-indexcard');
    $election_id    = NULL;
    if($request->has('election_id')){
      $election_id    = $request->election_id;
    }
    $data['election_id']  = $election_id;
    //election id
    $data['elections']      = [];
    $elections              = ElectionModel::get_current_elections();
    foreach ($elections as $key => $result) {
      $data['elections'][] = [
        'election_id'      => $result['ELECTION_ID'],
        'election_type'    => $result['ELECTION_TYPE'].'-'.$result['YEAR'],
      ];
    }

    $status = [
      '0' => 'Pending',
      '1' => 'Approved',
      '2' => 'Rejected',
    ];
    $filter = [];
    $filter['st_code']      = Auth::user()->st_code;
    $filter['election_id']  = $election_id;

    $data['heading_title']  = 'My Index Card Requests - Draft Report';
    $data['filter_buttons'] = [];
    $data['results']        = [];
    $results                = [];
    if($filter['election_id']){
      $results = IndexCardUploadModel::get_finalize_records($filter);
    }
    foreach ($results as $result) {
      $review_at = '--';
      if($result['review_at'] != '0000-00-00'){
        $review_at = date('d-m-Y',strtotime($result['review_at']));
      }
      $issue = [];
      if($result['issue']){
        $issue = unserialize($result['issue']);
        if(!is_array($issue)){
          $issue = [];
        }
      }
      $data['results'][] = [
        'st_name'         => $result['st_name'],
        'ac_name'         => $result['ac_no'].'-'.$result['AC_NAME'],
        'review_comment'  => $result['review_comment'],
        'submitted_at'    => date('d-m-Y',strtotime($result['submitted_at'])),
        'review_at'       => $review_at,
        'issue'           => implode(",<br>", $issue),
        'review_status'   => $status[$result['review_status']],
      ];
    }
    $data['user_data']  =   Auth::user();
    return view('IndexCardReports.DeFinalizeIndexCard.get_uploaded_indexcard',$data);
  }

  public function get_indexcard_for_eci(Request $request){
    $data           = [];
    $id             = 0;
    $comment        = '';
    $review_status  = 0;
    if($request->old('id')){
      $id = $request->old('id');
    }
    if($request->old('comment')){
      $comment = $request->old('comment');
    }
    if($request->old('review_status')){
      $review_status = $request->old('review_status');
    }
    $data['id']             = $id;
    $data['comment']        = $comment;
    $data['review_status']  = $review_status;
    
    $data['current_page'] = url($this->base.'/indexcard/get-indexcard-eci');
    $data['action']       = url($this->base.'/indexcard/upload-indexcard/post');

    $election_id    = NULL;
    if($request->has('election_id')){
      $election_id    = $request->election_id;
    }
    $data['election_id']  = $election_id;

    $data['st_code']    = NULL;
    if($request->has('state')){
      $data['st_code']    = $request->st_code;
    }

    //election id
    $data['elections']      = [];
    $elections              = ElectionModel::get_all_election();
    foreach ($elections as $key => $result) {
      $data['elections'][] = [
        'election_id'      => $result['ELECTION_ID'],
        'election_type'    => $result['ELECTION_TYPE'].'-'.$result['YEAR'],
      ];
    }

    $status = [
      '0' => 'Pending',
      '1' => 'Approved',
      '2' => 'Rejected',
    ];
    $filter = [];
    $filter['st_code']      = $data['st_code'];
    $filter['election_id']  = $election_id;

    $data['heading_title']  = 'My Index Card Requests - Draft Report';
    $data['filter_buttons'] = [];
    $data['results']        = [];
    $results                = [];
    if($filter['election_id']){
      $results = IndexCardUploadModel::get_finalize_records($filter);
    }
    foreach ($results as $result) {
      $review_at = '--';
      if($result['review_at'] != '0000-00-00'){
        $review_at = date('d-m-Y',strtotime($result['review_at']));
      }
      $issue = [];
      if($result['issue']){
        $issue = unserialize($result['issue']);
        if(!is_array($issue)){
          $issue = [];
        }
      }

      $data['results'][] = [
        'id'              => $result['id'],
        'st_name'         => $result['st_name'],
        'ac_name'         => $result['ac_no'].'-'.$result['AC_NAME'],
        'review_comment'  => $result['review_comment'],
        'submitted_at'    => date('d-m-Y',strtotime($result['submitted_at'])),
        'review_at'       => $review_at,
        'review_status'   => $status[$result['review_status']],
        'status_id'       => $result['review_status'],
        'file_url'        => url($result['file_name']),
        'issue'           => implode(",<br>",$issue) ,
      ];
    }
    $data['user_data']  =   Auth::user();
    return view('IndexCardReports.DeFinalizeIndexCard.get_indexcard_for_eci',$data);
  }

  public function post_indexcard_accepted(Request $request){
    $data     = $request->all();
    $messages = [];
    $rules = [
      'id'            => 'required|exists:indexcard_upload_request,id',
      'review_status' => 'in:1,2',
      'issue'         => 'required_if:review_status,2',
    ];
    $validator = \Validator::make($data, $rules, $messages);
    if($validator->fails()){
      \Session::flash('status', 0);
      \Session::flash('flash-message', 'Please select all ther required field.');
      return Redirect::back()->withInput($request->all());
    }
    try{
        DB::beginTransaction();
        IndexCardUploadModel::update_indexcard_status($data);
        DB::commit();
      }catch(\Exception $e){
        DB::rollback();
        Session::flash('status',0);
        Session::flash('flash-message','Please try again.');
        return Redirect::back()->withInput($request->all());
      }
      Session::flash('status',1);
      Session::flash('flash-message','Status has been updated.');
      return Redirect::back();
  }

}  // end class