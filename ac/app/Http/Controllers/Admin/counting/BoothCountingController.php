<?php  
namespace App\Http\Controllers\Admin\counting;
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
use \MPDF;
use \PDF;
use App\commonModel;
use Illuminate\Support\Facades\Schema;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use App\models\Counting\BoothCountingModel;
use App\adminmodel\ACCountingModel;
use App\models\Counting\UsercountingModel;
use App\models\Counting\PostalCountingModel; 
use App\models\Counting\CountingResultsPublishModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;  
use App\Helpers\LogNotification; 
class BoothCountingController  extends Controller
{

    public $base    = 'roac';
    public $folder  = 'counting';
    public $action    = 'roac/counting/';
    public $view_path = "admin.counting.ro";

    public function __construct()
    {
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->boothcounting=new BoothCountingModel;
        $this->users=new UsercountingModel;
        $this->CountingModel = new ACCountingModel();
        $this->postal = new PostalCountingModel();
        if(!Auth::check()){ 
          return redirect('/officer-login');
      }
  }

  protected function guard(){

    return Auth::guard('admin');
}

function polling_station_wisevote_entry(Request $request){
	
	
	$counting = \DB::table('setting')->select('*')->where('key','counting')->first();
			 if($counting->value < 1){
			  \Session::flash('error_mes', 'Counting menu is not enabled now. ');
			  return Redirect::back();
		  }
	
	
	
   $data  = [];
   $data['round_id'] = '';
   $data['table_id'] = '';
   $data['user_data'] = '';
   $data['ele_details'] = '';
   $data['new_table'] = '';
   $data['total_no_ps'] = '';
   $data['total_no_tables'] = '';
   $data['complete_rounds'] =0;
   $data['current_rounds']  =1;
   $data['complete_table']  ='';
   $data['master_table'] = '';
   $data['master_data'] = '';
   $data['counting_pstabledeails'] = '';
   $data['counting_ps_evmvote']=array();
   $data['scheduled_round']='';
    
   $user = Auth::user();
    
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
    
   $new_table=strtolower("counting_ps_".$d->st_code);

   $filter = [
    'st_code' => $ele_details->ST_CODE,
    'pc_no'   =>'',
    'election_id' => $ele_details->ELECTION_ID,
    'ac_no'   =>$d->ac_no,
    'table'   =>"counting_master_".strtolower($ele_details->ST_CODE), 
];
    $evm_finalized= evm_votes_finalized( $filter);
    $round_details=$this->postal->roundsechudle($filter);
     $data['evm_finalized']   = $evm_finalized;
            
       // if($evm_finalized==0){
         //               \Session::flash('error_mes', 'EVM Rounds not finalize! please finalize first.');
          //               return Redirect::to('/roac/dashboard'); 
          // } 

    if(!isset($round_details)) {
      if($d->role_id=="19") { 
                \Session::flash('success_admin', 'Round Schedule Not Created! Please ask  RO to Create roundschedule');
                return Redirect::to('roac/counting/round-schedule-details');
              }
        elseif($d->role_id=="36") {   
             \Session::flash('error_mes', 'Round Schedule Not Created! Please Create to roundschedule');
                return Redirect::to('/roac/dashboard');
        }
    }   
    
$assigntable=$this->users->getallassigntable($filter);
    $checkuser=$this->boothcounting->checkmasterrecords($filter);
    if(!isset($checkuser)){
       \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
       return Redirect::to('roac/counting/prepare-counting-data');
    }
//dd($request->round_id);
 $ctype=$request->ctype;  
//echo "==".$ctype;
 if(Auth::user()->role_id==19)
if(!empty($request->round_id))$round_id=base64_decode($request->round_id);  else $round_id=$checkuser->complete_round+1;
else
  $round_id=$checkuser->complete_round+1;

if(!empty($request->table_id))$table_id=base64_decode($request->table_id);  else $table_id='';
if($round_id>$round_details->scheduled_round) { $round_id=$round_details->scheduled_round; 
  $data['current_rounds']=$round_details->scheduled_round; } 
//   echo $data['current_rounds']; 
// dd($round_id);
$filter_table = [
    'st_code'       => '',
    'pc_no'         =>'',
    'election_id'   => $ele_details->ELECTION_ID, 
    'ac_no'         =>$d->ac_no,
    'table_name'    =>$new_table,
    'round_id'      =>$round_id,
];

$table_details=$this->boothcounting->get_table_master_details($filter); 
 if(!isset($table_details)) {
         if($d->role_id=="19") { 
       \Session::flash('error_mes', 'Please enter counting center details to move forward.');
                return Redirect::to('roac/counting/counting-center-details');
        }
        elseif($d->role_id=="36") {   
             \Session::flash('error_mes', ' RO Please enter counting center details');
                return Redirect::to('/roac/dashboard');
        }
    }
    if($table_details->total_no_ps==0 || $table_details->total_no_tables==0 || $table_details->total_no_rounds==0){ 
      if($d->role_id=="19") { 
       \Session::flash('error_mes', 'Please enter counting center details to move forward.');
                return Redirect::to('roac/counting/counting-center-details');
        }
        elseif($d->role_id=="36") {   
             \Session::flash('error_mes', 'RO Please enter counting center details');
                return Redirect::to('/roac/dashboard');
        }
    }
  if($assigntable['countassigntable']!=$table_details->total_no_tables)
    {    if($d->role_id=="19") { 
          \Session::flash('error_mes', 'All tables are not assigned to user');
          return Redirect::to('roac/counting/user-assign-table-details'); 
          }
        elseif($d->role_id=="36") {   
             \Session::flash('error_mes', 'RO Not Assign All table');
               return Redirect::to('/roac/dashboard');
        }
    }
$round_details=$this->boothcounting->roundsechudle($filter);
 

$c_data=DB::table(strtolower("counting_master_".$d->st_code))->select('complete_round','finalized_round')
                  ->where('ac_no', $d->ac_no)
                  ->where('election_id',$ele_details->ELECTION_ID)
                  ->orderBy('id')->first();
 $complete_round=0; $finalized_round=0;
              

$list_table=$this->boothcounting->getcompletetables($filter_table); 
   $st=getstatebystatecode($ele_details->ST_CODE);  
   $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
 
   $data['st_name']   = $st->ST_NAME;
   $data['ac_name']   = $ac->AC_NAME;   
  $data['round_id']   = $round_id;
  $data['round']      = $round_id;
  $data['table_id']   = $table_id;
  $data['st_code']    = $ele_details->ST_CODE;
  $data['ac_no']      = $d->ac_no;
  $data['user_data']  = $d;
  $data['ele_details'] = $ele_details;
  $data['new_table']  = $new_table;
  $data['ctype']  = $ctype;
 
if(isset($table_details)) $data['total_no_ps'] = $table_details->total_no_ps;
if(isset($table_details)) $data['total_no_tables'] = $table_details->total_no_tables;

if(isset($table_details)) $data['complete_rounds'] = $checkuser->complete_round; 
if(isset($table_details)) { if($ctype=='') { $data['current_rounds']  = $checkuser->complete_round+1;}  
elseif($ctype=='edit') {$data['current_rounds']=$round_id;} }
 
if(isset($table_details)) $data['complete_table']  = $list_table;  
if(isset($round_details)) $data['scheduled_round'] = $round_details->scheduled_round;
if($round_id>=$round_details->scheduled_round) { $round_id=$round_details->scheduled_round; 
  $data['current_rounds']=$round_details->scheduled_round; } 
//   echo $data['current_rounds']; 
// dd($round_id);
$master_table=strtolower("counting_master_".$d->st_code);

$filter_m   = [
 'ac_no'          => $ele_details->CONST_NO,
 'election_id'    => $ele_details->ELECTION_ID,
 'order_by'       => 'id', 
];

$filter_ps = [
    'pc_no'       =>'',
    'election_id' => $ele_details->ELECTION_ID,
    'ac_no'       =>$d->ac_no,
    'table_name'  =>$new_table,
    'round_id'    =>$round_id,
    'table_id'    =>$table_id,
    'st_code'    =>$ele_details->ST_CODE,
];

$master_data=$this->CountingModel->master_records($master_table,$filter_m);

$counting_pstabledeails  =$this->boothcounting->getpollingstationgroupby($filter_ps);

$counting_ps_evmvote   =$this->boothcounting->getvotedetailsbyroundid($filter_ps);

$listps=$this->boothcounting->get_acwisepollingstation($filter);
$completetable=$this->boothcounting->completetable($filter_ps);
$selfassign_complate=$this->boothcounting->loginuserassigntable($filter_ps);

//dd($selfassign_complate);  

$data['master_table'] = $master_table;
$data['master_data'] = $master_data;
$data['counting_pstabledeails'] = $counting_pstabledeails;
$data['counting_ps_evmvote'] = $counting_ps_evmvote;
$data['ps_list'] = $listps;
            // Quick View Code 
$filter_ps=['st_code'=>$ele_details->ST_CODE,
            'election_id'=>'',
            'ac_no'=>'',
            'pc_no'=>'',
            'ps_no'=>''];
//dd($counting_pstabledeails['ps_no']);
if($counting_pstabledeails['ps_no']!=""){
    $filter_ps = [
        'st_code'     => $ele_details->ST_CODE,
        'election_id' => $ele_details->ELECTION_ID,
        'ac_no'       =>$d->ac_no,
        'pc_no'       =>'',
        'ps_no'       =>$counting_pstabledeails['ps_no'],
        'round_id'    =>$round_id, 
    ];
  }
 // dd($filter_ps);
$psdetails=$this->boothcounting->getbypsno($filter_ps);

 $data['psname'] = '';
 if(isset($psdetails) and ($psdetails) and $counting_pstabledeails['ps_no']!="") $data['psname']=$psdetails->PS_NAME_EN;
$filter = [
    'st_code'     => $ele_details->ST_CODE,
    'election_id' => $ele_details->ELECTION_ID,
    'ac_no'       =>$d->ac_no,
    'pc_no'       =>'',
    'table'       =>"counting_master_".strtolower($ele_details->ST_CODE), 
];
 
$object = $this->boothcounting->get_previous_total($data);

$data['previous_vote']=$object;
$table_details=$this->boothcounting->get_table_master_details($filter);
$round_details=$this->boothcounting->roundsechudle($filter);

$filter_data = [
    'st_code'           =>$ele_details->ST_CODE,
    'pc_no'             =>'',
    'election_id'       =>$ele_details->ELECTION_ID,
    'ac_no'             =>$d->ac_no,
    'round_id'          =>$round_id,
    'total_no_tables'   =>$table_details->total_no_tables,
    'table_name'        =>$new_table,
];
// $lists=$this->boothcounting->tabulating_trend($filter_data);
// $pollingstationlist=$this->boothcounting->get_roundwise_psnumber($filter_data);
$allpollingstationlist=$this->boothcounting->get_allpollingstation($filter_data);
    $lists=$this->boothcounting->tabulating_trend($filter_data);
    $grandresults=$this->boothcounting->grandtotal_tabulating_trend_columwise($filter_data);
    $pollingstationlist=$this->boothcounting->get_roundwise_psnumber($filter_data);
$filter_a = [
            'st_code'       => $ele_details->ST_CODE,
            'ac_no'         => $ele_details->CONST_NO,
            'election_id'   => $ele_details->ELECTION_ID,
            'users_name'    => $d->officername,
         ];
   
$assigntable=$this->boothcounting->getassigntable($filter_a);

//$grandresults=$this->boothcounting->grandtotal_tabulating_trend_columwise($filter_data);

          $i=0; $j=0; $grandprevious=0; $grandtotal=0;

          if(!empty($lists))
              {
                  foreach($lists as $list){ $sum=0;  

                    $data['results'][$j]['nom_id'] =$list->nom_id;
                    $data['results'][$j]['candidate_id'] =$list->candidate_id;
                    $data['results'][$j]['candidate_name'] =$list->candidate_name;
                    $data['results'][$j]['party_name'] =$list->party_name;
                    for($i=1; $i<=$table_details->total_no_tables;$i++){ 
                      $field="table".$i;
                      $data['results'][$j][$field] =$list->$field;
                  }
                  $data['results'][$j]['total'] =$list->total; 
                  foreach ($object as $key => $val) {
                      if($list->nom_id==$val->nom_id) 
                      {
                         $data['results'][$j]['previous_total'] =$val->previous_total;  
                         $grandprevious += $val->previous_total;   
                         break; 
                     }
                 }
                 $sum=$list->total+ $data['results'][$j]['previous_total'];
                 $data['results'][$j]['accumlative_total'] =$sum;
                 $grandtotal +=$sum;
                 $j++; 
              }

                           } // end if
             $data['grandresults']=$grandresults;
             $data['grandprevious'] = $grandprevious;
             $data['grandtotal'] = $grandtotal;
             $data['pollingstationlist']=$pollingstationlist;
             $data['listassigntable'] = $assigntable['assigntable'];
             $data['totalassigntable'] = $assigntable['countassigntable'];
             $data['completetable'] = $completetable;
             $data['pendingtable'] = $data['total_no_tables']-$completetable;
             $data['selfassign_complate'] = $selfassign_complate;
             $data['allpollingstationlist'] = $allpollingstationlist;
            //  dd( $data);   
             return view($this->view_path.'.polling-station-wisevote-entry', $data);
         }

function verifypolling_station_wisevote_entry(Request $request) {
             $user = Auth::user(); 

             $d=$this->commonModel->getunewserbyuserid($user->id);
             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');


          $ST_CODE =$ele_details->ST_CODE;  
          $CONST_TYPE =$ele_details->CONST_TYPE;   //$this->xssClean->clean_input($request->input('CONST_TYPE'));
          $CONST_NO = $ele_details->CONST_NO;  //$this->xssClean->clean_input($request->input('CONST_NO'));
          $ELECTION_ID=$ele_details->ELECTION_ID;  //$this->xssClean->clean_input($request->input('ELECTION_ID'));
          $ctype=$this->xssClean->clean_input($request->input('ctype')); 
          $round_id=$this->xssClean->clean_input($request->input('round_id')); 
          $table_id=$this->xssClean->clean_input($request->input('table_id'));  
          $cu_no=$this->xssClean->clean_input($request->input('cu_no'));
          $vvpat_no=$this->xssClean->clean_input($request->input('vvpat_no'));  
          $ps_no=$this->xssClean->clean_input($request->input('ps_no'));    //
          $val = $this->xssClean->clean_input($request->input('val'));
          $rejected_vote=$this->xssClean->clean_input($request->input('rejected_vote'));
          $tendered_vote=$this->xssClean->clean_input($request->input('tendered_vote')); 
          $poll=$this->xssClean->clean_input($request->input('poll')); 

            if($request->input('cu_defect_id')=='on') $cu_defect_id =1; else $cu_defect_id =0;
            if($request->input('vvpat_defect_id')=='on') $vvpat_defect_id =1; else $vvpat_defect_id =0;
           // $vvpat_defect_id = $this->xssClean->clean_input($request->input('vvpat_defect_id'));
            $input = $request->all();

            $date = Carbon::now();
            $currentTime = $date->format('Y-m-d H:i:s');
            $currentdate = $date->format('Y-m-d');  


            $rules = ['Please enter all new serial number'];
            $total_voters = 0;

            for ($i=1; $i<=$val;$i++){  

             $this->validate($request, [
               'currentvote'.$i => 'required|digits_between:0,999999',
               'rejected_vote' => 'required|digits_between:0,9999',
               'tendered_vote' => 'required|digits_between:0,999',
               'ps_no' => 'required',
               'table_id' =>'required','round_id'=>'required',
           ],
           [
            'currentvote'.$i.'required' => 'Please enter current vote ',
            'currentvote'.$i.'digits_between' => 'Please enter integer value max 999999 ',
            'rejected_vote.required' => 'Please enter number of polling station ',
            'rejected_vote.digits_between' =>'Please enter  number of PS 1 and 9999',
            'tendered_vote.required' => 'Please enter number of polling station ',
            'tendered_vote.digits_between' =>'Please enter number of PS 1 and 999',
            'ps_no.required' => 'Please enter  polling station no',
            'table_id.required' => 'Please select table number', 
            'round_id.required' => 'Please select round number',
        ]); 

             $total_voters += $input['currentvote'.$i];

         }
         $total_voters += $input['rejected_vote'];
        // $total_voters += $input['tendered_vote'];

         if($total_voters != $request->total){
             \Session::flash('error_mes', 'Total value is wrong.');
             return Redirect::back()->withInput($request->all());
         }
         $new_table=strtolower("counting_ps_".$d->st_code);
         $filter= [
            'st_code'       =>$d->st_code,
            'table_name'    =>$new_table,
            'ac_no'         =>$d->ac_no,
            'ps_no'         =>$ps_no,
            'election_id'   => $ele_details->ELECTION_ID,
        ]; 
		
		if(empty($ps_no) && $ps_no ==''){
				 \Session::flash('error_mes', 'Please select polling station first.');
				 return Redirect::back()->withInput($request->all());
	    }
			   
        $psexit=$this->boothcounting->check_pollingstation($filter);
        if($poll=="new"){
               if(isset($psexit)){
                         \Session::flash('error_mes', 'Evm votes for this polling station already entered.');
                         return Redirect::back()->withInput($request->all());
               }
        }else{
			$old_ps = $request->old_ps;
			if(!empty($old_ps)){
				if($old_ps != $ps_no){
					if(isset($psexit)){
							 \Session::flash('error_mes', 'Evm votes for this polling station already entered.');
							 return Redirect::back()->withInput($request->all());
				   }
				}
			}
		}

        $pslist=$this->boothcounting->getbypsno($filter);
          $listtendered=$this->boothcounting->get_counting_tendered_vote($filter);
        DB::beginTransaction();
        try{
           $new_table=strtolower("counting_ps_".$d->st_code);
           for ($i=1; $i<=$val;$i++)
              {    
                 $mid=$this->xssClean->clean_input($request->input('mid'.$i));
                 $nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
                 $candidate_id=$this->xssClean->clean_input($request->input('candidate_id'.$i));
                 $party_id=$this->xssClean->clean_input($request->input('party_id'.$i));
                 $currentvote=$this->xssClean->clean_input($request->input('currentvote'.$i));

                 $filter1 = [
                 'election_id'  => $ele_details->ELECTION_ID,
                 'ac_no'        =>$d->ac_no,
                 'table_name'   =>$new_table,
                 'round_id'     =>$round_id,
                 'table_id'     =>$table_id,
                 'nom_id'       =>$nom_id,
                 'candidate_id' =>$candidate_id,
                 'ps_no'        =>$ps_no,
             ];
             $records=$this->boothcounting->getpswiserecord($filter1); 
			 $log_entry_type = '';
             if(!isset($records)){
				$log_entry_type = 'insert';
                $n_data = array('nom_id'=>$nom_id,
                    'candidate_id'=>$candidate_id,
                    'party_id'=>$party_id, 
                    'ac_no'=>$CONST_NO ,
                    'election_id'=>$ELECTION_ID, 
                    'election_typeid'=>$ele_details->ELECTION_TYPEID , 
                    'month'=>date("m"), 
                    'year'=>date("Y"),
                    'ps_no'=>$ps_no, 
                    'cu_no'=>$cu_no, 
                    'vvpat_no'=>$vvpat_no,
                    'table_id'=>$table_id,  
                    'round_id'=>$round_id, 
                    'evm_vote'=>$currentvote,
                    'part_no'=> $pslist->PART_NO,
                    'rejected_vote'=>$rejected_vote,
                    'tendered_vote'=>$tendered_vote,
                    'cu_defect_id'=>$cu_defect_id, 
                    'vvpat_defect_id'=>$vvpat_defect_id,
                    'results'=>'0',
                    'dist_no'=>$d->dist_no,
                    'added_create_at'=>$currentdate,
                    'created_at'=>$currentTime,
                    'created_by'=>$d->officername); 
                
                $tended_data = array('st_code'=>$ST_CODE,
                    'ac_no'=>$CONST_NO ,
                    'election_id'=>$ELECTION_ID, 
                    'election_typeid'=>$ele_details->ELECTION_TYPEID , 
                    'month'=>date("m"), 
                    'year'=>date("Y"),
                    'ps_no'=>$ps_no, 
                    'dist_no'=>$d->dist_no,
                    'rejected_vote'=>$rejected_vote,
                    'tendered_vote'=>$tendered_vote,
                    'added_create_at'=>$currentdate,
                    'created_at'=>$currentTime,
                    'created_by'=>$d->officername); 
                $this->commonModel->insertData($new_table, $n_data);
                $ins=1;  
            }
            else {
			   $log_entry_type = 'update';
               $u_data = array( 'month'=>date("m"),
                'year'=>date("Y"),
                'ps_no'=>$ps_no, 
                'cu_no'=>$cu_no, 
                'vvpat_no'=>$vvpat_no, 
                'evm_vote'=>$currentvote,
                'part_no'=> $pslist->PART_NO,
                'rejected_vote'=>$rejected_vote,
                'tendered_vote'=>$tendered_vote,
                'cu_defect_id'=>$cu_defect_id, 
                'vvpat_defect_id'=>$vvpat_defect_id,
                'results'=>'0',
                'dist_no'=>$d->dist_no,
                'updated_at'=>$currentTime,
                'updated_by'=>$d->officername); 

               $tended_data = array(  
                'rejected_vote'=>$rejected_vote,
                'tendered_vote'=>$tendered_vote,
                'dist_no'=>$d->dist_no,
                'updated_at'=>$currentTime,
                'updated_by'=>$d->officername); 
               $ins=0;

               $this->commonModel->updatedata($new_table,'id',$records->id,$u_data);
           }
			
			$log_data = array(
					'st_code'=>$ST_CODE,
					'nom_id'=>$nom_id,
                    'candidate_id'=>$candidate_id,
                    'party_id'=>$party_id, 
                    'ac_no'=>$CONST_NO ,
                    'election_id'=>$ELECTION_ID, 
                    'election_typeid'=>$ele_details->ELECTION_TYPEID , 
                    'month'=>date("m"), 
                    'year'=>date("Y"),
                    'ps_no'=>$ps_no, 
                    'cu_no'=>$cu_no, 
                    'vvpat_no'=>$vvpat_no,
                    'table_id'=>$table_id,  
                    'round_id'=>$round_id, 
                    'evm_vote'=>$currentvote,
                    'part_no'=> $pslist->PART_NO,
                    'rejected_vote'=>$rejected_vote,
                    'tendered_vote'=>$tendered_vote,
                    'cu_defect_id'=>$cu_defect_id, 
                    'vvpat_defect_id'=>$vvpat_defect_id,
                    'results'=>'0',
                    'dist_no'=>$d->dist_no,
                    'added_create_at'=>$currentdate,
                    'entry_type'=>$log_entry_type,
                    'created_at'=>$currentTime,
                    'created_by'=>$d->officername); 
				if(!empty($log_data)){
					$this->commonModel->insertData('counting_ps_log', $log_data);
				}
       }
       //dd($listtendered);
       if(($listtendered==NULL)) 
        $this->commonModel->insertData('counting_ps_tendered', $tended_data);
    else
        $this->commonModel->updatedata('counting_ps_tendered','id',$listtendered->id,$tended_data);

    DB::commit();

}
catch(\Exception $e){
   DB::rollback();

   \Session::flash('error_mes', 'Please try again Data  do not inserted');
   return Redirect::back();
}
           
		   
							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'Counting';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'PS_Wise_Vote';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'PS Wise Record Successfully Added';
								LogNotification::LogInfo($ErrorMessage);
							}		   
		   
		   
		   

\Session::flash('success_mes', 'This record was successfully saved.');
return Redirect::to('roac/counting/polling-station-wisevote-entry?ctype='.$ctype.'&round_id='.base64_encode($round_id));

    }  // end function tabulating_trend_results

   public function pswisepdf(Request $request){

        if($request->has('print_table') && $request->has('ac_no') && $request->has('round')){

            \Session::put('print_table',$request->print_table);
            \Session::put('ac_no',$request->ac_no);
            \Session::put('round',$request->round);
            \Session::put('table_id',$request->table_id);
            \Session::put('ps_no',$request->ps_no);
            \Session::put('cu_no',$request->cu_no);
            \Session::put('vvpat_no',$request->vvpat_no);
            \Session::put('rejected_vote',$request->rejected_vote);
            \Session::put('tendered_vote',$request->tendered_vote);
            \Session::put('psname',$request->psname);
        }
    // 
        $d        = \Auth::user();
        $ele_details  = $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');

        if(\Session::has('print_table') && \Auth::user()){
           $st_name = '';
           $state_object = \App\models\Admin\StateModel::get_state_by_code(\Auth::user()->st_code);
           if($state_object){
              $st_name = $state_object['ST_NAME'];
          }
          $data = [];
          $data['table']  = \Session::get('print_table');
          $data['ac_no']  = \Session::get('ac_no');

          $get_ac = $this->commonModel->getacbyacno(\Auth::user()->st_code,$data['ac_no']);
		  
          $ac_name = '';
          if($get_ac){
			  if($get_ac->AC_TYPE<>'GEN'){
				  $ac_name = $get_ac->AC_NAME.' ('.$get_ac->AC_TYPE.')';;
			  }else{
				  $ac_name = $get_ac->AC_NAME;
			  }
           
       }

       $data['ac_name']         = $ac_name;
       $data['round']           = \Session::get('round');
       $data['heading_title']   = 'Tablewise Recording of Votes';
       $data['st_name']     = $st_name;
       $data['election']    = "AC-".@$ele_details->ELECTION_TYPE;
       $data['st_code']        = \Auth::user()->st_code;
       $data['table_id']       = \Session::get('table_id');
       $data['ps_no']          = \Session::get('ps_no')."-".\Session::get('psname');
       $data['cu_no']          = \Session::get('cu_no');
       $data['vvpat_no']       = \Session::get('vvpat_no');
       $data['rejected_vote']  = \Session::get('rejected_vote');
       $data['tendered_vote']  = \Session::get('tendered_vote');
       $data['enter_by']       = $d->officername;
       $data['print_date']     = date('d-m-Y H:i:s');  
       $name_excel = 'TRV'.$data['round'].'_Table'.$data['table_id'].'_ac_no'.$data['ac_no'].'_'.date('d-m-Y').'_'.time();
       $data['ref_no']         = time();
        //round to be sum and print previous
      $data['name_excel']     = $name_excel;
          $filter= [
            'st_code'       =>$d->st_code,
            'ac_no'         =>$d->ac_no,
            'ps_no'         =>Session::get('ps_no'),
            'election_id'   =>$ele_details->ELECTION_ID,
        ];   
            $date = Carbon::now();
            $currentTime = $date->format('Y-m-d H:i:s');
            $currentdate = $date->format('Y-m-d');  
        $mid=(int)$this->boothcounting->maxidoftable($filter);
        $table="counting_ps_".strtolower($d->st_code);
        if($mid==0)$mid++;
        $log_data = array( 'st_code'=>\Auth::user()->st_code,
                'election_id'=>$ele_details->ELECTION_ID,
                'election_typeid'=>$ele_details->ELECTION_TYPEID, 
                'pc_no'=>'0', 
                'ac_no'=>$d->ac_no, 
                'ps_no'=>$data['ps_no'],
                'doc_type'=>"TABLEWISE RECORDING OF VOTES",
                'file_name'=>$name_excel.".pdf",
                'table_name'=>$table,
                'table_primary_key'=>$mid, 
                'log_date_time'=>$currentTime,
                'added_create_at'=>$currentdate,
                'ref_no'=> $data['ref_no'],
                'created_by'=>$d->officername);
        
       $object = $this->boothcounting->get_details($data);

       $nominator = [];
       foreach (explode(',',$data['table']) as $key => $value) {
          $explode_array = explode('_', $value);
          $nominator[trim($explode_array[0])] = [
             'nom_id' => trim($explode_array[0]),
             'vote'   => trim($explode_array[1])
         ];
     }

     $i = 1;

     $aggregate_current_total   = 0;
     foreach ($object as $result) {
      $current_total  = 0;
      $total      = 0;

      if(isset($nominator[$result->nom_id])){
         $current_total   = $nominator[$result->nom_id]['vote'];

     }

     $results[] = [
         'sr_no'      => $i,
         'candidate_name'   => $result->candidate_name,
         'current_total'    => format_digit($current_total),
     ];
     $aggregate_current_total   += $current_total;
     $i++;
 }

    //  $results[] = [
    //     'sr_no'             => '',
    //     'candidate_name'    => 'Rejected Votes',
    //     'current_total'     => format_digit($data['rejected_vote']),
    // ];
    $results[] = [
        'sr_no'             => '',
        'candidate_name'    => 'Tendered Vote',
        'current_total'     => format_digit($data['tendered_vote']),
    ];

    $results[] = [
      'sr_no'       => '',
      'candidate_name'  => 'Total',
      'current_total'   => format_digit($aggregate_current_total),
    ];

$data['results'] = $results;
   
   
      // dd($results);

$setting_pdf = [
      'margin_top'        =>75,        // Set the page margins for the new document.
      'margin_bottom'     => 10,    
    ];

        $pdf = \PDF::loadView($this->view_path.'.pswisepdf',$data,[], $setting_pdf);


        if($request->has('json')){
            return \Response::json([
                'success' => true
            ]);
        }
     \App\models\Counting\CountingPrintlogModel::clone_record($log_data);
    
    //  $this->commonModel->insertData('counting_print_log',$log_data);
        return $pdf->download($name_excel.'.pdf');
    }else{
        return \Redirect::to('/officer-login');
    }
}
public  function tabulating_trend_results(Request $request){
                 $data  = [];
                 $user = Auth::user();
                 $d=$this->commonModel->getunewserbyuserid($user->id);
                 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
                 $new_table=strtolower("counting_ps_".$d->st_code);
                 $data['encround']  =$request->round_id;
            
            $round_id=base64_decode($request->round_id); 
            $st_code=$request->st_code; 
            $ac_no=$request->ac_no; 
            $election_id=$request->election_id; 

            if(empty($round_id)) $round_id=0;     if(empty($st_code)) $st_code=$d->st_code;
            if(empty($ac_no)) $ac_no=$d->ac_no;   if(empty($election_id)) $election_id=$d->election_id;

            $st=getstatebystatecode($st_code);  
            $ac=getacbyacno($st_code,$ac_no); 

            $data['ac_no']          = $d->ac_no;
            $data['round']          = $round_id;
            $data['st_code']        = \Auth::user()->st_code;
            $data['st_name']        = $st->ST_NAME;
            $data['ac_name']        = $ac->AC_NAME;      
                 
            $filter = [
                      'st_code'       => $st_code,
                      'election_id'   => $election_id,
                      'ac_no'         =>$ac_no,
                      'pc_no'         =>'',
                      'table'         =>strtolower("counting_master_".$d->st_code),
                    ];

            $checkuser=$this->boothcounting->checkmasterrecords($filter);
              if(!isset($checkuser)){
                 \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
                 return Redirect::to('roac/counting/prepare-counting-data');
              }   


              $object = $this->boothcounting->get_previous_total($data);

              $data['previous_vote']=$object;
              $table_details=$this->boothcounting->get_table_master_details($filter);
              $round_details=$this->boothcounting->roundsechudle($filter);
              $table_details=$this->boothcounting->get_table_master_details($filter); 
               if(!isset($table_details)) {
                     \Session::flash('error_mes', 'Please enter counting center details to move forward.');
                              return Redirect::to('roac/counting/counting-center-details');
                  }
                  if($table_details->total_no_ps==0 || $table_details->total_no_tables==0 || $table_details->total_no_rounds==0){
                     \Session::flash('error_mes', 'Please enter counting center details to move forward.');
                              return Redirect::to('roac/counting/counting-center-details');
                  }
              $filter_data = [
                  'st_code'       =>$ele_details->ST_CODE,
                  'pc_no'       =>'',
                  'election_id'   =>$ele_details->ELECTION_ID,
                  'ac_no'       =>$d->ac_no,
                  'round_id'      =>$round_id,
                  'total_no_tables' =>$table_details->total_no_tables,
                  'table_name'      =>$new_table,
              ];
              $lists=$this->boothcounting->tabulating_trend($filter_data);

              $grandresults=$this->boothcounting->grandtotal_tabulating_trend_columwise($filter_data);
              $pollingstationlist=$this->boothcounting->get_roundwise_psnumber($filter_data);
              
              $i=0; $j=0; $grandprevious=0; $grandtotal=0;

              if(!empty($lists))
              {
                  foreach($lists as $list){ $sum=0;  

                    $data['results'][$j]['nom_id'] =$list->nom_id;
                    $data['results'][$j]['candidate_id'] =$list->candidate_id;
                    $data['results'][$j]['candidate_name'] =$list->candidate_name;
                    $data['results'][$j]['party_name'] =$list->party_name;
                    for($i=1; $i<=$table_details->total_no_tables;$i++){ 
                      $field="table".$i;
                      $data['results'][$j][$field] =$list->$field;
                  }
                  $data['results'][$j]['total'] =$list->total; 
                  foreach ($object as $key => $val) {
                      if($list->nom_id==$val->nom_id) 
                      {
                         $data['results'][$j]['previous_total'] =$val->previous_total;  
                         $grandprevious += $val->previous_total;   
                         break; 
                     }
                 }
                 $sum=$list->total+ $data['results'][$j]['previous_total'];
                 $data['results'][$j]['accumlative_total'] =$sum;
                 $grandtotal +=$sum;
                 $j++; 
              }

                           } // end if
                           

                            // dd($data['results']);
                           $list_table=$this->boothcounting->getcompletetables($filter_data);  
                           $data['round_id'] = $round_id;
                           $data['user_data'] = $d;
                           $data['ele_details'] = $ele_details;
                           $data['grandprevious'] = $grandprevious;
                           $data['grandtotal'] = $grandtotal;

                           $data['total_no_ps'] = $table_details->total_no_ps;
                           $data['total_no_tables'] = $table_details->total_no_tables;
                           $data['scheduled_round'] = $round_details->scheduled_round;
                           $data['pollingstationlist'] = $pollingstationlist; 
                           
                           $data['grandresults'] = $grandresults;
                           //dd($data);
                           return view($this->view_path.'.tabulating-trend-results', $data);
      }

  public  function download_tabulating_trend_results(Request $request){
            $data  = [];
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
             
           $ele_details  = $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
           $data['election']    = "AC-".@$ele_details->ELECTION_TYPE;
            $new_table=strtolower("counting_ps_".$d->st_code);

            $round_id=base64_decode($request->round_id); 
            $st_code=$request->st_code; 
            $ac_no=$request->ac_no; 
            $election_id=$request->election_id; 

            if(empty($round_id)) $round_id=1;     if(empty($st_code)) $st_code=$d->st_code;
            if(empty($ac_no)) $ac_no=$d->ac_no;   if(empty($election_id)) $election_id=$d->election_id;

            $st=getstatebystatecode($st_code);  
            $ac=getacbyacno($st_code,$ac_no); 
			
			$ac_name = '';
			if($ac){
			  if($ac->AC_TYPE<>'GEN'){
				  $ac_name = $ac->AC_NAME.' ('.$ac->AC_TYPE.')';;
			  }else{
				  $ac_name = $ac->AC_NAME;
			  }
           
			}

            $data['ac_no']          = $d->ac_no;
            $data['round']          = $round_id;
            $data['st_code']        = \Auth::user()->st_code;
            $data['st_name']        = $st->ST_NAME;
            $data['ac_name']        = $ac_name;   

            $filter = [
                'st_code'       => $st_code,
                'election_id'   => $election_id,
                'ac_no'         =>$ac_no,
                'pc_no'         =>'',
            ];

            $object = $this->boothcounting->get_previous_total($data);
           
            $table_details=$this->boothcounting->get_table_master_details($filter);
            $round_details=$this->boothcounting->roundsechudle($filter);
          
            $filter_data = [
                'st_code'           =>$st_code,
                'pc_no'             =>'',
                'election_id'       =>$election_id,
                'ac_no'             =>$ac_no,
                'round_id'          =>$round_id,
                'total_no_tables'   =>$table_details->total_no_tables,
                'table_name'        =>$new_table,
            ];
            $lists=$this->boothcounting->tabulating_trend($filter_data);
            
            $grandresults=$this->boothcounting->grandtotal_tabulating_trend_columwise($filter_data);
            $pollingstationlist=$this->boothcounting->get_roundwise_psnumber($filter_data); 

            $i=0; $j=0; $grandprevious=0; $grandtotal=0;

            if(!empty($lists))
            {
                foreach($lists as $list){ $sum=0;  

                  $data['results'][$j]['nom_id'] =$list->nom_id;
                  $data['results'][$j]['candidate_id'] =$list->candidate_id;
                  $data['results'][$j]['candidate_name'] =$list->candidate_name;
                  $data['results'][$j]['party_name'] =$list->party_name;
                  for($i=1; $i<=$table_details->total_no_tables;$i++){ 
                    $field="table".$i;
                    $data['results'][$j][$field] =$list->$field;
                }
                $data['results'][$j]['total'] =$list->total; 
                foreach ($object as $key => $val) {
                    if($list->nom_id==$val->nom_id) 
                    {
                       $data['results'][$j]['previous_total'] =$val->previous_total;  
                       $grandprevious += $val->previous_total;   
                       break; 
                   }
               }
               $sum=$list->total+ $data['results'][$j]['previous_total'];
               $data['results'][$j]['accumlative_total'] =$sum;
               $grandtotal +=$sum;
               $j++; 
           }

             } // end if
             $name_excel = 'Trendresults_rounds'.$data['round']."_".$data['st_code']."_".$data['ac_no'].'_'.date('dmY').'_'.time();
             $data['ref_no']            = time();
             $data['print_date']        = date('d-m-Y H:i:a');

            $log_data = array( 'st_code'=>\Auth::user()->st_code,
                              'election_id'=>$ele_details->ELECTION_ID,
                              'election_typeid'=>$ele_details->ELECTION_TYPEID, 
                              'pc_no'=>'0', 
                              'ac_no'=>$ac_no, 
                              'ps_no'=>'0',
                              'round_id'=>$data['round'],
                              'doc_type'=>"Annexure for Tabulating Trends and RDF",
                              'file_name'=>$name_excel.".pdf",
                              'table_name'=>$new_table,
                              'table_primary_key'=>'0', 
                              'log_date_time'=>date('Y-m-d H:i:a'),
                              'added_create_at'=>date('Y-m-d'),
                              'ref_no'=> $data['ref_no'],
                              'created_by'=>$d->officername);
             

             $data['round_id']          = $round_id;
             $data['grandprevious']     = $grandprevious;
             $data['grandtotal']        = $grandtotal;
             $data['total_no_ps']       = $table_details->total_no_ps;
             $data['total_no_tables']   = $table_details->total_no_tables;
             $data['scheduled_round']   = $round_details->scheduled_round;
             $data['grandresults']      = $grandresults;
             $data['pollingstationlist'] = $pollingstationlist; 
             $data['heading_title']     = 'Tabulating Trend & Results';
			 
			 
			 $filter_data = [
				'st_code'         =>$ele_details->ST_CODE,
				'pc_no'           =>'',
				'election_id'     =>$ele_details->ELECTION_ID,
				'ac_no'           =>$d->ac_no,
				'round_id'        =>$round_id,
				'total_no_tables' =>$table_details->total_no_tables,
				'table_name'      =>$new_table,
			]; 

			$publish = 0;
			$publish= $this->boothcounting->checkpublish($filter_data);
			$data['publish']  = $publish;
             
             $setting_pdf = [
                'margin_top'        =>10,  
                'margin_bottom'     => 10,
                'show_warnings'     => false,    
                'orientation'       => 'landscape',    
            ];

            $pdf = \MPDF::loadView($this->view_path.'.downloadtabulating-trend-results',$data,[], $setting_pdf);
             
             \App\models\Counting\CountingPrintlogModel::clone_record($log_data);

            return $pdf->download($name_excel.'.pdf'); 
   } // end function



   public function round_wise_calculate_vote(Request $request){
    $data  = [];   
    $user = Auth::user();  
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($user->st_code,$user->ac_no,$user->dist_no,$user->id,'AC');
    $this->validate(
                $request,
                    [ 
                      'ename' => 'required',
                     
                    ],
                    [ 
                      'resultstrends.required' => 'Please enter Ro name',
                      
                    ]
                  );
                 
    if(str_replace(" ","",$request->input('ename')) <> str_replace(" ","",Auth::user()->name)){
		\Session::flash('error_mes', 'Please enter correct returning officer name.');
		return Redirect::back();	
	}              
    $new_table=strtolower("counting_ps_".$user->st_code);
    $date = Carbon::now();
    $currentTime = $date->format('Y-m-d H:i:s');
    $currentdate = $date->format('Y-m-d');   
    $round_id=base64_decode($request->round_id); 
    $oldround=$request->round_id;   
    $data['ac_no']          = $user->ac_no;
    $data['round']          = $round_id;
    $data['st_code']        = \Auth::user()->st_code;

    $filter = [
        'st_code'       => $ele_details->ST_CODE,
        'election_id'   => $ele_details->ELECTION_ID,
        'ac_no'         =>$user->ac_no,
        'pc_no'         =>'',
        'round'         =>$round_id,
        'table'         =>"counting_master_".strtolower($ele_details->ST_CODE), 
    ];

    $checkuser=$this->boothcounting->checkmasterrecords($filter);
    if(!isset($checkuser)){
       \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
       return Redirect::to('roac/counting/prepare-counting-data');
   }   
   $new_table=strtolower("counting_master_".$d->st_code);
   $result = $this->boothcounting->round_wise_calculate_vote($filter);
   $round="round".$round_id;

   $ST_CODE=$ele_details->ST_CODE;
   $CONST_NO=$ele_details->CONST_NO;
   $CONST_TYPE=$ele_details->CONST_TYPE;
   $ELECTION_ID=$ele_details->ELECTION_ID;

   $winnlead = DB::table('winning_leading_candidate')->where("st_code",$ST_CODE)
   ->where("ac_no",$CONST_NO)
   ->where("election_id",$ELECTION_ID)
   ->first();

   DB::beginTransaction();
   try{
      if(isset($result)) {

        foreach($result as $list){
         $filter_ele = ['nom_id'=>$list->nom_id,
                        'ac_no'=>$d->ac_no,
                        'table'=>$new_table,
                        'st_code'=>$d->st_code,
                        'round'=>$round];
         $currentvote=$list->totalevmvote;
         $total_value=''; //dd("hello");
         $total_value=total_evm_votes($filter_ele);
        // print_r($total_value);  die;
         $total_vote   = 0; 
         $round_vote=0;
         $total_vote1=0;
         if(isset($total_value) && $total_value){
            $total_vote   = $total_value->grant_total;
            $total_vote1 = $total_value->grant_total;
            $round_vote=$total_value->$round;
            $postal_vote=$total_value->postalballot_vote;
           if($total_value->complete_round>$round_id) $c_round=$total_value->complete_round; else
              $c_round=$round_id; 
                      } //if(isset
                      $total_vote= ($total_vote-$round_vote)+$currentvote+$postal_vote; 
                      if($currentvote!=0){
                         $n_data = array($round=>$currentvote,
                            'total_vote'=>$total_vote,
                            'complete_round'=>$c_round,
                            'added_update_at'=>$currentdate,
                            'month'=>date("m"),
                            'year'=>date("Y"),
                            'updated_at'=>$currentTime,
                            'updated_by'=>$d->officername); 
                     }
                     else {  
                        $n_data = array($round=>$currentvote,
                            'total_vote'=>$total_vote,
                            'month'=>date("m"),
                            'year'=>date("Y"),
                            'added_update_at'=>$currentdate,
                            'complete_round'=>$c_round,
                            'updated_at'=>$currentTime,
                            'updated_by'=>$d->officername);
                    } 
                 
                    DB::table($new_table)->where('nom_id',$list->nom_id)->where('ac_no',$list->ac_no)->update($n_data); 
                } // end foreach

                
                $sdata=$this->CountingModel->selectsecondhightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
                $fdata=$this->CountingModel->selectfirsthightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);

                $lead_cand=getById('candidate_personal_detail','candidate_id',$fdata->candidate_id);

                $lead_nom=getById('candidate_nomination_detail','nom_id',$fdata->nom_id);
                
                $lead_party=getById('m_party','CCODE',$lead_nom->party_id);
                

                $trail_cand=getById('candidate_personal_detail','candidate_id',$sdata->candidate_id);
                $trail_nom=getById('candidate_nomination_detail','nom_id',$sdata->nom_id);

                $trail_party=getById('m_party','CCODE',$trail_nom->party_id);


                $margin=$fdata->max_total-$sdata->max_total;
                $winn_update=array('candidate_id'=>$fdata->candidate_id,
                    'nomination_id'=>$fdata->nom_id,
                    'lead_cand_name'=>str_replace('  ',' ',$lead_cand->cand_name), 
                    'lead_cand_partyid'=>$lead_party->CCODE,
                    'lead_cand_party'=>$lead_party->PARTYNAME,
                    'lead_party_type'=>$lead_party->PARTYTYPE,
                    'lead_party_abbre'=>$lead_party->PARTYABBRE,
                    'lead_cand_hname'=>$lead_cand->cand_hname,
                    'lead_cand_hparty'=>$lead_party->PARTYHNAME,
                    'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
                    'trail_candidate_id'=>$sdata->candidate_id,
                    'trail_nomination_id'=>$sdata->nom_id,
                    'trail_cand_name'=>str_replace('  ',' ',$trail_cand->cand_name), 
                    'trail_cand_partyid'=>$trail_party->CCODE,
                    'trail_cand_party'=>$trail_party->PARTYNAME,
                    'trail_party_type'=>$trail_party->PARTYTYPE,
                    'trail_party_abbre'=>$trail_party->PARTYABBRE,
                    'trail_cand_hname'=>$trail_cand->cand_hname,
                    'trail_cand_hparty'=>$trail_party->PARTYHNAME,
                    'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
                    'margin'=>$margin,
                    'lead_total_vote'=>$fdata->max_total,
                    'trail_total_vote'=>$sdata->max_total,
                    'added_update_at'=>$currentdate,
                    'updated_at'=>$currentTime);
                     // dd($winn_update);
					DB::table('winning_leading_candidate')->where('leading_id',$winnlead->leading_id)->where("st_code",$ST_CODE)
				   ->where("ac_no",$CONST_NO)->where("election_id",$ELECTION_ID)->update($winn_update);
				   
                $updat=array('results'=>'1');
                DB::table('counting_ps_'.strtolower($ST_CODE))->where('ac_no',$CONST_NO)->where('election_id',$ELECTION_ID)->where('round_id',$round_id)->update($updat);

              $pubresult=['st_code'=>$ele_details->ST_CODE,
                        'election_id'=>$ele_details->ELECTION_ID,
                        'pc_no'=>0,
                        'ac_no'=>$ele_details->CONST_NO,
                        'certificate'=>"I, ".Auth::user()->name."certify that the table-wise data entered/ updated for  round ".$data['round']." has been printed & manually verified by me & the observer and is correct., 
                          I, understand that upon pressing the 'Publish' button below,the round will be immediately published/ updated with the correct data and round-wise data will be  available in public domain. ,
                          I, certify that the round-wise publication on the server and at the counting center is done simultaneously.",
                        'name'=>$this->xssClean->clean_input($request->input('ename')),
                        'roname'=>Auth::user()->name,
                        'agree'=>'1',
                        'round_id'=>$data['round']];
                  CountingResultsPublishModel::add_records($pubresult);

            }
         }catch(\Exception $e){
            DB::rollback();

            \Session::flash('error_mes', 'Please try again');
            return Redirect::back();
        } 
        DB::commit();

							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'Counting';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Round_Publish';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'Round Successfully Published.';
								LogNotification::LogInfo($ErrorMessage);
							}



        \Session::flash('success_mes', 'This Round Successfully Published.');
        return Redirect::to('/roac/counting/result-publish?round_id='.encrypt_string($round_id));

        } //end round_wise_calculate_vote

        public function generate_form20(Request $request){
            $data  = [];  
            $user = Auth::user();  
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $ele_details=$this->commonModel->election_detailsac($user->st_code,$user->ac_no,$user->dist_no,$user->id,'AC');
            $st=getstatebystatecode($ele_details->ST_CODE);  
            $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
			
			$ac_name = '';
				if($ac){
			  if($ac->AC_TYPE<>'GEN'){
				  $ac_name = $ac->AC_NAME.' ('.$ac->AC_TYPE.')';;
			  }else{
				  $ac_name = $ac->AC_NAME;
			  }
		   
			}

             $data['user_data']      = $d;
             $data['ele_details']    = $ele_details;
             $data['st_code']        = $ele_details->ST_CODE;
             $data['ac_no']          = $ele_details->CONST_NO;
             $data['ac_name']        = $ac_name;
             $data['st_name']        = $st->ST_NAME;

            $filter = [
                'st_code'       => $ele_details->ST_CODE,
                'election_id'   => $ele_details->ELECTION_ID,
                'ac_no'         =>$user->ac_no,
                'pc_no'         =>'',
                'ps_no'         =>'',
                'table'         =>"counting_master_".strtolower($ele_details->ST_CODE), 
            ];
            $request_array[]='';
            $request_array[] = 'st_code='.base64_encode($ele_details->ST_CODE);
            $request_array[]  = 'ac_no='.base64_encode($d->ac_no);
            $request_array[]  = 'election_id='.base64_encode($ele_details->ELECTION_ID);
       
	   
	 
	   
            $checkuser=$this->boothcounting->checkmasterrecords($filter);
            if(!isset($checkuser)){
               \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
               return Redirect::to('roac/counting/prepare-counting-data');
           }   
		   
		    
           $totalelectors= $this->boothcounting->totalelectors($filter);

           $data['totalelectors']        = $totalelectors; 

           $new_table=strtolower("counting_master_".$d->st_code);

           $totalcandidate = $this->boothcounting->noofcandidate($filter);
		   
		  
		   
           $columecandidate = $this->boothcounting->getallcandidate($filter);

			//  dd($columecandidate);



              //
           $listallac = $this->boothcounting->get_acwisepollingstation($filter);
               
           $resultsum = $this->boothcounting->getpsvotessum($filter);
           $postaldetails = $this->boothcounting->get_allpostalvotes($filter);
               //  dd($resultsum );
           $data['totalcandidate'] = $totalcandidate;
           $data['columecandidate'] = $columecandidate;
           $data['listallac'] = $listallac;

           $j=0; $k=0;
           foreach ($listallac as $key => $val) { $i=0; $field="data".$i; $k++;
           $data['results'][$j][$field]=$k;  
            $i++;
            $field="data".$i;
           $data['results'][$j][$field]=$val->PS_NO;
           $filter_new = [
            'st_code'       => $ele_details->ST_CODE,
            'election_id'   => $ele_details->ELECTION_ID,
            'ac_no'         =>$user->ac_no,
            'pc_no'         =>'',
            'ps_no'         =>$val->PS_NO,
        ];

                   // $list = $this->boothcounting->getallvotesbypswise($filter_new);
        $list = $this->boothcounting->getallpsvotes($filter_new);
                    // dd($list);
        $sum=0; $nota=0; $rejected_vote=0;  $tendered_vote=0; $finalsum=array();
                   // if(!empty($list)) {
        foreach ( $list as  $new) { $i++; $field="data".$i;
        if($new->party_id!='1180'){
            $data['results'][$j][$field] =$new->evm_vote;
            $sum +=$new->evm_vote;
            $rejected_vote=$new->rejected_vote;

            $tendered_vote=$new->tendered_vote;
        }       
        else {
           $nota =$new->evm_vote;
       }
   }
         
   $field="data".$i;
   $data['results'][$j][$field] = $sum;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $rejected_vote;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $nota;
   $net=0;
   $net=$sum+$nota+ $rejected_vote;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $net;

   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $tendered_vote;
   $j++;    
      }
      $data['colcount'] = $totalcandidate+6;
      $data['grand_allsum'] = array();
      $k=0; $gsum=0;  $grejected_vote=0;  $gtendered_vote=0;  $gnota=0;
      foreach ( $resultsum as  $sum) {  
        if($sum->party_id!='1180'){
          $data['grandsum'][$k]=$sum->evm_vote;
          $data['grand_allsum'][$k] =$sum->evm_vote;
          $gsum=$gsum+$sum->evm_vote;
          $grejected_vote=$sum->rejected_vote;
          $gtendered_vote=$sum->tendered_vote;
      }       
      else {
         $gnota =$sum->evm_vote;
      }
      $k++;
      }

      $data['grandsum'][$k]=$gsum; 
      $data['grand_allsum'][$k] =$gsum;
      $k++;  
      $data['grandsum'][$k]=$grejected_vote;
      $data['grand_allsum'][$k] =$grejected_vote;
      $k++;  
      $data['grandsum'][$k]=$gnota; 
      $data['grand_allsum'][$k] =$gnota; 
      $gnet= $gsum+$grejected_vote+$gnota;
      $k++;  
      $data['grandsum'][$k]=$gnet; 
       $data['grand_allsum'][$k] =$gnet;   
      $k++;  
      $data['grandsum'][$k]=$gtendered_vote;
      $data['grand_allsum'][$k] =$gtendered_vote;
     
      $data['postal_vote'] = array();
      

      $data['colcount'] = $totalcandidate+6;
       $k=0; $postalsum=0;  $prejected_votes=0;  $tended_votes=0;  $pnota=0;
      foreach ( $postaldetails as  $postal) {  
        if($postal->party_id!='1180'){
          $data['postal_vote'][$k]=$postal->postalballot_vote;
          $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postal->postalballot_vote;
          $postalsum=$postalsum+$postal->postalballot_vote;
          $prejected_votes=$postal->rejected_votes;
           
      }       
      else {
         $pnota =$postal->postalballot_vote;
      }
      $k++;
      }
      $data['postal_vote'][$k]=$postalsum;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postalsum; 
      $k++;  
      $data['postal_vote'][$k]=$prejected_votes;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$prejected_votes; 
      $k++;  
      $data['postal_vote'][$k]=$pnota;  
       $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnota; 
      $pnet= $postalsum+$prejected_votes+$pnota;

      $k++;  
      $data['postal_vote'][$k]=$pnet; 
       $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnet;   
      $k++;  
      $data['postal_vote'][$k]=$tended_votes;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$tended_votes; 
      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action.'form20excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action.'form20pdf').'?'.implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action);
      
     // dd($data); 
      return view($this->view_path.'.generate-from20', $data);  
}

public function pollingstationdetails(Request $request){

   $ps_no=$request->input('ps_no'); 
   $ac_no=$request->input('ac_no'); 
   $st_code=$request->input('st_code'); 
   $records = DB::table('counting_ps_'.strtolower($st_code))->where('ac_no','=',$ac_no)->where('ps_no','=',$ps_no)->first();

   $filter= [
    'st_code'       => $st_code,
    'ac_no'         =>$ac_no,
    'ps_no'         =>$ps_no,
    ];
    $list = $this->boothcounting->getbypsno($filter);    

    if($records){
        $data['success'] = false;
    }else{
        $data['success'] = true;
    }

    $data['name'] = $list->PS_NAME_EN;

    return  \Response::json($data);

  }

  // excelexport_excel_form20

  public function export_excel_form20(Request $request){
        $GLOBALS['cellarr']=array('0' =>'A8','1' =>'B8','2' =>'C8','3' =>'D8','4' =>'E8','5' =>'F8','6' =>'G8','7' =>'H8',
          '8' =>'I8','9' =>'J8', '10' =>'K8','11' =>'L8','12' =>'M8','13' =>'N8','14' =>'O8',
              '15' =>'P8','16' =>'Q8','17' =>'R8','18' =>'S8','19' =>'T8', '20' =>'U8','21' =>'V8','22' =>'W8','23' =>'X8',
              '24' =>'Y8', '25' =>'Z8', '26' =>'AA8','27' =>'AB8','28' =>'AC8','29' =>'AD8','30' =>'AE8','31' =>'AF8',
              '32' =>'G8','33' =>'H8',
              '34' =>'I8','35' =>'J8', '36' =>'K8','37' =>'L8','38' =>'M8','39' =>'N8','40' =>'O8',
              '41' =>'P8','42' =>'Q8','43' =>'R8','44' =>'S8','45' =>'T8', '46' =>'U8','47' =>'V8','48' =>'W8',
              '49' =>'X8', '50' =>'Y8', '51' =>'Z8',);
            $data=[];
            $st_code=base64_decode($request->st_code);
            $ac_no=base64_decode($request->ac_no);
            $election_id=base64_decode($request->election_id);
            $st=getstatebystatecode($st_code);  
            $ac=getacbyacno($st_code,$ac_no); 
			$ac_name = '';
				if($ac){
			  if($ac->AC_TYPE<>'GEN'){
				  $ac_name = $ac->AC_NAME.' ('.$ac->AC_TYPE.')';;
			  }else{
				  $ac_name = $ac->AC_NAME;
			  }
		   
			}
             $data['st_code']        = $st_code;
             $data['ac_no']          = $ac_no;
             $data['ac_name']        = $ac_name;
             $data['st_name']        = $st->ST_NAME;

            $filter = [
                'st_code'       => $st_code,
                'election_id'   => $election_id,
                'ac_no'         =>$ac_no,
                'pc_no'         =>'',
                'ps_no'         =>'',
                'table'         =>"counting_master_".strtolower($st_code), 
            ];
           $totalelectors= $this->boothcounting->totalelectors($filter);
           $totalcandidate = $this->boothcounting->noofcandidate($filter);
           $c=$GLOBALS['cellarr'][$totalcandidate];
            
           $columecandidate = $this->boothcounting->getallcandidate($filter);

           $GLOBALS['totalcandidate']=$totalcandidate;
           $listallac = $this->boothcounting->get_acwisepollingstation($filter);
               
           $resultsum = $this->boothcounting->getpsvotessum($filter);
           $postaldetails = $this->boothcounting->get_allpostalvotes($filter);

           $data['totalcandidate'] = $totalcandidate;
           $data['columecandidate'] = $columecandidate;
           $data['listallac'] = $listallac;

            $j=0; $k=0;
           foreach ($listallac as $key => $val) { $i=0; $field="data".$i; $k++;
           $data['results'][$j][$field]=$k;  
            $i++;
            $field="data".$i;
           $data['results'][$j][$field]=$val->PS_NO;

           $filter_new = [
            'st_code'       => $st_code,
            'election_id'   => $election_id,
            'ac_no'         =>$ac_no,
            'pc_no'         =>'',
            'ps_no'         =>$val->PS_NO,
        ];

          $list = $this->boothcounting->getallpsvotes($filter_new);
           
        $sum=0; $nota=0; $rejected_vote=0;  $tendered_vote=0; 

         
        foreach ( $list as  $new) { $i++; $field="data".$i;
        if($new->party_id!='1180'){
            $data['results'][$j][$field] =$new->evm_vote;
            $sum +=$new->evm_vote;
            $rejected_vote=$new->rejected_vote;

            $tendered_vote=$new->tendered_vote;
        }       
        else {
           $nota =$new->evm_vote;
       }
   }
      
   $field="data".$i;
   if (empty($sum))$sum=0;
   if (empty($rejected_vote))$rejected_vote=0;
   if (empty($nota))$nota=0;
   if (empty($tendered_vote))$tendered_vote=0;
   

    $data['results'][$j][$field] = $sum;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $rejected_vote;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $nota;
   $net=0;
   $net=$sum+$nota+ $rejected_vote;
   $i++;
   
   $field="data".$i;
   if( $net==0 || ($net)) $data['results'][$j][$field]='0';
   $data['results'][$j][$field] = $net;

   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $tendered_vote;
   $j++;    
      }
      
       $data['grand_allsum'] = array();
      $k=0; $gsum=0;  $grejected_vote=0;  $gtendered_vote=0;  $gnota=0;
      foreach ( $resultsum as  $sum) {  
        if($sum->party_id!='1180'){
          $data['grandsum'][$k]=$sum->evm_vote;
          $data['grand_allsum'][$k] =$sum->evm_vote;
          $gsum=$gsum+$sum->evm_vote;
          $grejected_vote=$sum->rejected_vote;
          $gtendered_vote=$sum->tendered_vote;
      }       
      else {
         $gnota =$sum->evm_vote;
      }
      $k++;
      }

      $data['grandsum'][$k]=$gsum; 
      $data['grand_allsum'][$k] =$gsum;
      $k++;  
      $data['grandsum'][$k]=$grejected_vote;
      $data['grand_allsum'][$k] =$grejected_vote;
      $k++;  
      $data['grandsum'][$k]=$gnota; 
      $data['grand_allsum'][$k] =$gnota; 
      $gnet= $gsum+$grejected_vote+$gnota;
      $k++;  
      $data['grandsum'][$k]=$gnet; 
       $data['grand_allsum'][$k] =$gnet;   
      $k++;  
      $data['grandsum'][$k]=$gtendered_vote;
      $data['grand_allsum'][$k] =$gtendered_vote;
     
      $data['postal_vote'] = array();
      

      $data['colcount'] = $totalcandidate+6;
       $k=0; $postalsum=0;  $prejected_votes=0;  $tended_votes=0;  $pnota=0;
      foreach ( $postaldetails as  $postal) {  
        if($postal->party_id!='1180'){
          $data['postal_vote'][$k]=$postal->postalballot_vote;
          $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postal->postalballot_vote;
          $postalsum=$postalsum+$postal->postalballot_vote;
          $prejected_votes=$postal->rejected_votes;
           
      }       
      else {
         $pnota =$postal->postalballot_vote;
      }
      $k++;
      }
      $data['postal_vote'][$k]=$postalsum;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postalsum; 
      $k++;  
      $data['postal_vote'][$k]=$prejected_votes;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$prejected_votes; 
      $k++;  
      $data['postal_vote'][$k]=$pnota;  
       $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnota; 
      $pnet= $postalsum+$prejected_votes+$pnota;

      $k++;  
      $data['postal_vote'][$k]=$pnet; 
       $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnet;   
      $k++;  
      $data['postal_vote'][$k]=$tended_votes;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$tended_votes; 
     
              $export_data = [];
              $export_data[] = [' FORM 20 '];
              $export_data[] = [' FINAL RESULT SHEET '];
              $export_data[] = [' ELECTION TO THE LEGISLATIVE ASSEMBLY'];
              $export_data[] = [' (To be used    Assembly Election) '];
              $export_data[] = [' Total No. of  Electors in Assembly Constituency/segment  ....'.$totalelectors->total];
              $export_data[] = [' Name of  Assembly/segment  ...'. $data['ac_no'].'-'.$data['ac_name'].' Assembly Election'];
                            
            $export_data[] = ['','', 'No of Valid Votes Cast in favour of',' ','', '','',''];
            $i=0;
            $export_data[7][$i] ='Serial No.';
            $i++;
            $export_data[7][$i] ='Serial No. Of Polling Station';  
             $st='';  
                   foreach ($columecandidate as   $val) { $i++;
                         
                              $export_data[7][$i]=$val->candidate_name;
                     }
             $i++;
             $export_data[7][$i]='Total of Valid Votes';
             $i++;
             $export_data[7][$i]='No. Of Rejected Votes';         
             $i++;
             $export_data[7][$i]='NOTA'; 
             $i++;
             $export_data[7][$i]='Total'; 
             $i++;
             $export_data[7][$i]='No. Of Tendered Votes'; 
        
     $i=8; $j=0;
    foreach ($data['results'] as $lists) {

          foreach ($lists as $lis) {
               if($lis==0) $export_data[$i][$j]='0';
               else
               $export_data[$i][$j] =$lis;
               $j++;
            }   // end foreach
        $i++;
        } // end foreach 
        $j=0; 
         $export_data[$i][$j] ='Total EVM ';
         $j++;   
         $export_data[$i][$j] =' Votes ';    
         foreach($data['grandsum'] as $d){ $j++; 
              if($d==0) $export_data[$i][$j]='0'; 
              else  
              $export_data[$i][$j] =$d;
                 
         } 

       $j=0; $i++; 
         $export_data[$i][$j] ='Total Postal Ballot '; 
         $j++;   
         $export_data[$i][$j] =' Votes ';       
         foreach($data['postal_vote'] as $d){ $j++;    
               if($d==0) 
                        $export_data[$i][$j]='0'; 
                else  
                        $export_data[$i][$j] =$d;
                 
         }  
         $j=0; $i++; 
         $export_data[$i][$j] ='Total Votes '; 
         $j++;   
         $export_data[$i][$j] =' Polled '; 
         $headings[]=[];      
         foreach($data['grand_allsum'] as $d){ $j++;    
               if($d==0) 
                        $export_data[$i][$j]='0'; 
              else  
                        $export_data[$i][$j] =$d;
                 
         }  


    //dd($export_data); 
    $name_excel = 'form20-'.strtolower($data['st_code'])."_".$data['ac_no'].'_'.date('d-m-Y').'_'.time();
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    \Excel::create($name_excel, function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:J1');
          $sheet->mergeCells('A2:J2');
          $sheet->mergeCells('A3:J3');
          $sheet->mergeCells('A4:J4');
          $sheet->mergeCells('A5:J5');
          $sheet->mergeCells('A6:J6');
         // $sheet->mergeCells('A8:B8');
          $sheet->mergeCells('C7:K7');
          
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A2', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A3', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A4', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
         $sheet->cell('A5', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
         $sheet->cell('A6', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
            for($c=0; $c<=$GLOBALS['totalcandidate']+6;$c++){
             $newcell=strtoupper($GLOBALS['cellarr'][$c]);
            
              $sheet->cell($newcell, function($cell) {
                      $cell->setTextRotation(90);
                      $cell->setFontWeight('bold');
             });
           }
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');
  }

  public function download_pdf_form20(Request $request){
			ini_set("pcre.backtrack_limit", "5000000");
            $data=[];
            $st_code=base64_decode($request->st_code);
            $ac_no=base64_decode($request->ac_no);
            $election_id=base64_decode($request->election_id);
            $st=getstatebystatecode($st_code);  
            $ac=getacbyacno($st_code,$ac_no); 
			
			$ac_name = '';
				if($ac){
			  if($ac->AC_TYPE<>'GEN'){
				  $ac_name = $ac->AC_NAME.' ('.$ac->AC_TYPE.')';;
			  }else{
				  $ac_name = $ac->AC_NAME;
			  }
		   
			}
			
             $data['st_code']        = $st_code;
             $data['ac_no']          = $ac_no;
             $data['ac_name']        = $ac_name;
             $data['st_name']        = $st->ST_NAME;

            $filter = [
                'st_code'       => $st_code,
                'election_id'   => $election_id,
                'ac_no'         =>$ac_no,
                'pc_no'         =>'',
                'ps_no'         =>'',
                'table'         =>"counting_master_".strtolower($st_code), 
            ];
             
       
              
           $totalelectors= $this->boothcounting->totalelectors($filter);

           $data['totalelectors'] = $totalelectors; 

           $new_table=strtolower("counting_master_".$st_code);

           $totalcandidate = $this->boothcounting->noofcandidate($filter);
           $columecandidate = $this->boothcounting->getallcandidate($filter);
              //
           $listallac = $this->boothcounting->get_acwisepollingstation($filter);
               
           $resultsum = $this->boothcounting->getpsvotessum($filter);
            $postaldetails = $this->boothcounting->get_allpostalvotes($filter); 
           $data['totalcandidate'] = $totalcandidate;
           $data['columecandidate'] = $columecandidate;
           $data['listallac'] = $listallac;

           $j=0; $k=0;
           foreach ($listallac as $key => $val) { $i=0; $field="data".$i; $k++;
           $data['results'][$j][$field]=$k;  
            $i++;
            $field="data".$i;
           $data['results'][$j][$field]=$val->PS_NO;
           $filter_new = [
            'st_code'       => $st_code,
            'election_id'   => $election_id,
            'ac_no'         =>$ac_no,
            'pc_no'         =>'',
            'ps_no'         =>$val->PS_NO,
        ];

          $list = $this->boothcounting->getallpsvotes($filter_new);
                    // dd($list);
        $sum=0; $nota=0; $rejected_vote=0;  $tendered_vote=0; $finalsum=array();
                   // if(!empty($list)) {
        foreach ( $list as  $new) { $i++; $field="data".$i;
        if($new->party_id!='1180'){
            $data['results'][$j][$field] =$new->evm_vote;
            $sum +=$new->evm_vote;
            $rejected_vote=$new->rejected_vote;

            $tendered_vote=$new->tendered_vote;
        }       
        else {
           $nota =$new->evm_vote;
       }
   }
         
   $field="data".$i;
   $data['results'][$j][$field] = $sum;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $rejected_vote;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $nota;
   $net=0;
   $net=$sum+$nota+ $rejected_vote;
   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $net;

   $i++;
   $field="data".$i;
   $data['results'][$j][$field] = $tendered_vote;
   $j++;    
      }

  $sub_array_res = [];
  $i = 0;
  $data['sub_results']=array_chunk($data['results'],15);

  foreach ($data['sub_results'] as $key => $sub_result) {

    $sub_array_res[$i]['results'] =  $sub_result;
    $sum_array = [];
    if(count($sub_result)>0 && count($sum_array) == 0){

      foreach ($sub_result[0] as $key => $value) {
        $sum_array[$key] = array_sum(array_column($sub_result,(int)$key));
      }

    }
    
    $sub_array_res[$i]['page_sum'] =  $sum_array;
    $i++;
  }
   
  $data['sub_array_res']=$sub_array_res;
   
      
      $data['colcount'] = $totalcandidate+6;
      $data['grand_allsum'] = array();
      $k=0; $gsum=0;  $grejected_vote=0;  $gtendered_vote=0;  $gnota=0;
      foreach ( $resultsum as  $sum) {  
        if($sum->party_id!='1180'){
          $data['grandsum'][$k]=$sum->evm_vote;
          $data['grand_allsum'][$k] =$sum->evm_vote;
          $gsum=$gsum+$sum->evm_vote;
          $grejected_vote=$sum->rejected_vote;
          $gtendered_vote=$sum->tendered_vote;
      }       
      else {
         $gnota =$sum->evm_vote;
      }
      $k++;
      }

      $data['grandsum'][$k]=$gsum; 
      $data['grand_allsum'][$k] =$gsum;
      $k++;  
      $data['grandsum'][$k]=$grejected_vote;
      $data['grand_allsum'][$k] =$grejected_vote;
      $k++;  
      $data['grandsum'][$k]=$gnota; 
      $data['grand_allsum'][$k] =$gnota; 
      $gnet= $gsum+$grejected_vote+$gnota;
      $k++;  
      $data['grandsum'][$k]=$gnet; 
       $data['grand_allsum'][$k] =$gnet;   
      $k++;  
      $data['grandsum'][$k]=$gtendered_vote;
      $data['grand_allsum'][$k] =$gtendered_vote;
     
      $data['postal_vote'] = array();
      

      $data['colcount'] = $totalcandidate+6;
       $k=0; $postalsum=0;  $prejected_votes=0;  $tended_votes=0;  $pnota=0;
      foreach ( $postaldetails as  $postal) {  
        if($postal->party_id!='1180'){
          $data['postal_vote'][$k]=$postal->postalballot_vote;
          $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postal->postalballot_vote;
          $postalsum=$postalsum+$postal->postalballot_vote;
          $prejected_votes=$postal->rejected_votes;
           
      }       
      else {
         $pnota =$postal->postalballot_vote;
      }
      $k++;
      }
      $data['postal_vote'][$k]=$postalsum;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postalsum; 
      $k++;  
      $data['postal_vote'][$k]=$prejected_votes;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$prejected_votes; 
      $k++;  
      $data['postal_vote'][$k]=$pnota;  
       $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnota; 
      $pnet= $postalsum+$prejected_votes+$pnota;

      $k++;  
      $data['postal_vote'][$k]=$pnet; 
       $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnet;   
      $k++;  
      $data['postal_vote'][$k]=$tended_votes;
      $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$tended_votes; 
      
      $name_excel = 'Form20-'.$data['st_code']."_ac_no".$data['ac_no'].'_'.date('d-m-Y').'_'.time();
      $data['file_name']=$name_excel; 
      $data['heading_title']  ='Form20 Generated';  
       $data['ref_no']  =time();

        $log_data = array( 'st_code'=>$st_code,
                              'election_id'=>$election_id,
                              'election_typeid'=>'0', 
                              'pc_no'=>'0', 
                              'ac_no'=>$ac_no, 
                              'ps_no'=>'0',
                              'doc_type'=>"Generate From20 PDF",
                              'file_name'=>$name_excel.".pdf",
                              'table_name'=>$new_table,
                              'table_primary_key'=>'0', 
                              'log_date_time'=>date('Y-m-d H:i:a'),
                              'added_create_at'=>date('Y-m-d'),
                              'ref_no'=> $data['ref_no'],
                              'created_by'=>\Auth::user()->officername);
            
            \App\models\Counting\CountingPrintlogModel::clone_record($log_data);

      $data['user']=\Auth::user()->officername;
      $data['print_date']=date('d-m-Y H:i:a');
            $setting_pdf = [
                'margin_top'        =>55,  
                'margin_bottom'     =>10,
                'show_warnings'     => false,    
                'orientation'       => 'landscape',    
            ];
     
        $pdf = \MPDF::loadView($this->view_path.'.download_pdf_form20',$data,[], $setting_pdf);

        return $pdf->download($name_excel.'.pdf');
  }

  public function round_wise_results(Request $request){
            $data  = [];  
            $user = Auth::user();  
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $ele_details=$this->commonModel->election_detailsac($user->st_code,$user->ac_no,$user->dist_no,$user->id,'AC');
            $st=getstatebystatecode($ele_details->ST_CODE);  
            $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 

             $data['user_data']      = $d;
             $data['ele_details']    = $ele_details;
             $data['st_code']        = $ele_details->ST_CODE;
             $data['ac_no']          = $ele_details->CONST_NO;
             $data['ac_name']        = $ac->AC_NAME;
             $data['st_name']        = $st->ST_NAME;

            $filter = [
              'st_code'     => $ele_details->ST_CODE,
              'election_id' => $ele_details->ELECTION_ID,
              'election_typeid'=> $ele_details->ELECTION_TYPEID,
              'ac_no'         =>$user->ac_no,
              'pc_no'         =>'',
              'table' =>"counting_ps_".strtolower($ele_details->ST_CODE), 
            ];
            
           $results= $this->boothcounting->roundwiseresults($filter);
           $data['results']=$results;
          // dd( $data);
           return view($this->view_path.'.round_wise_results', $data);  
      }
   function counting_data_entry_edit(Request $request) {    
        $rid =$request->input('rid');
        if($rid!=''){
          $nrid= base64_encode($rid);
         
        return Redirect::to('roac/counting/polling-station-wisevote-entry?ctype=edit&round_id='.$nrid);
      }
      else {
        \Session::flash('error_mes', '  Please Select   roundschedule');
             return Redirect::to('roac/counting/polling-station-wisevote-entry');
      }

         
      }
  public  function result_publish(Request $request){
   $data  = [];
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
   $new_table=strtolower("counting_ps_".$d->st_code);
   $data['encround']  =base64_encode(decrypt_string($request->round_id));
   $round_id=decrypt_string($request->round_id); 
  if(empty($round_id)) $round_id=0;
   
   $st=getstatebystatecode($ele_details->ST_CODE);  
   $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 

   $data['ac_no']          = $d->ac_no;
   $data['round']          = $round_id;
   $data['st_code']        = \Auth::user()->st_code;
   $data['st_name']        = $st->ST_NAME;
   $data['ac_name']        = $ac->AC_NAME;   

   $filter = [
    'st_code'       => $ele_details->ST_CODE,
    'election_id'   => $ele_details->ELECTION_ID,
    'ac_no'         =>$d->ac_no,
    'pc_no'         =>'',
     
    'table'         =>"counting_master_".strtolower($ele_details->ST_CODE), 
];

$object = $this->boothcounting->get_previous_total($data);

$data['previous_vote']=$object;
$table_details=$this->boothcounting->get_table_master_details($filter);
$round_details=$this->boothcounting->roundsechudle($filter);
$filter_data = [
    'st_code'         =>$ele_details->ST_CODE,
    'pc_no'           =>'',
    'election_id'     =>$ele_details->ELECTION_ID,
    'ac_no'           =>$d->ac_no,
    'round_id'        =>$round_id,
    'total_no_tables' =>$table_details->total_no_tables,
    'table_name'      =>$new_table,
]; 


 $publish= $this->boothcounting->checkpublish($filter_data);
 $record = $this->boothcounting->getresultsuploads($filter_data); 

 if(isset($record))  $data['results_upload']=1; else $data['results_upload']=0;

$lists=$this->boothcounting->tabulating_trend($filter_data);

$grandresults=$this->boothcounting->grandtotal_tabulating_trend_columwise($filter_data);

 $pollingstationlist=$this->boothcounting->get_roundwise_psnumber($filter_data);

$i=0; $j=0; $grandprevious=0; $grandtotal=0;

if(!empty($lists))
{
    foreach($lists as $list){ $sum=0;  

      $data['results'][$j]['nom_id'] =$list->nom_id;
      $data['results'][$j]['candidate_id'] =$list->candidate_id;
      $data['results'][$j]['candidate_name'] =$list->candidate_name;
      $data['results'][$j]['party_name'] =$list->party_name;
      for($i=1; $i<=$table_details->total_no_tables;$i++){ 
        $field="table".$i;
        $data['results'][$j][$field] =$list->$field;
    }
    $data['results'][$j]['total'] =$list->total; 
    foreach ($object as $key => $val) {
        if($list->nom_id==$val->nom_id) 
        {
           $data['results'][$j]['previous_total'] =$val->previous_total;  
           $grandprevious += $val->previous_total;   
           break; 
       }
   }
   $sum=$list->total+ $data['results'][$j]['previous_total'];
   $data['results'][$j]['accumlative_total'] =$sum;
   $grandtotal +=$sum;
   $j++; 
}

             } // end if
             

              // dd($data['results']);
             $list_table=$this->boothcounting->getcompletetables($filter_data);  
             $data['round_id'] = $round_id;
             $data['user_data'] = $d;
             $data['ele_details'] = $ele_details;
             $data['grandprevious'] = $grandprevious;
             $data['grandtotal'] = $grandtotal;
             $data['name'] =\Auth::user()->name;
             $data['sub_date'] = date("d-m-Y H:i:s");
             $data['total_no_ps'] = $table_details->total_no_ps;
             $data['total_no_tables'] = $table_details->total_no_tables;
             $data['scheduled_round'] = $round_details->scheduled_round;
             $data['pollingstationlist'] = $pollingstationlist; 
              $data['publish'] = $publish;
             $data['grandresults'] = $grandresults;
             // dd($data); 
             return view($this->view_path.'.publish-results', $data);
         }

     public function upload_postal_results(request $request){

            $data  = [];
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
     $this->validate(
                $request,
                    [ 
                      'resultstrends' => 'required|mimes:pdf|max:10000',
                     
                    ],
                    [ 
                      'resultstrends.required' => 'Please upload the valid pdf',
                      'resultstrends.mimes' => 'Please upload the valid pdf',
                      'resultstrends.max' => 'Please upload the maximum size 10mb',
                    ]
                  );
                 
                  $ext=$request->file('resultstrends')->getClientOriginalExtension();
                  if($ext!="pdf")
                    {
                      \Session::flash('error_mes', 'Only Pdf File uploaded');
                    return Redirect::to('roac/counting/result-publish?round_id='.$request->input('encround'));
                    }
       
        $st_code = $ele_details->ST_CODE;
        $election_id = $ele_details->ELECTION_ID;
        $election_type_id = $ele_details->ELECTION_TYPEID;
        $ac_no = $ele_details->CONST_NO ;
         
        $file_name = $request->input('file_name');
        $file = $request->file('resultstrends');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        
         $record = DB::table("counting_results_pdf")
                  ->where('st_code',$st_code)
                  ->where('ac_no',$ac_no)
                  ->where('file_name',$file_name)
                  ->where('election_id',$election_id)->first();

         
        
        if($request->file('resultstrends')){
          //Move Uploaded File
          $newfile =$st_code.'_'.$ac_no.'_postal_'.time(); 

          $fileNewName =$newfile.'.'.$request->file('resultstrends')->getClientOriginalExtension();
         
         if(!validate_pdf_file($request->file('resultstrends'))){
           \Session::flash('error_mes', 'Only Pdf File uploaded');
           return Redirect::back()->withInput($request->all());
         }
      
          $destinationPath ='uploads1/results/E'.$election_id.'/'.$st_code .'/'.$ac_no;
      
          $file->move($destinationPath,$fileNewName);
          
          $file_path = $destinationPath .'/'.$fileNewName ;
          if(!file_exists($file_path)){
           \Session::flash('error_mes', 'File is not uploaded. Please try again.');
           return Redirect::back()->withInput($request->all());
         }
           
          if(!empty($record) ){
            if($request->file('resultstrends') != ''){
              $updateNomDetail = DB::update('update counting_results_pdf set 
                          uploading_path ="'. $file_path. '", 
                          added_update_at="'.$date.'",
                          updated_at="'.$datetime.'",
                          file_name="'.$file_name.'", 
                          updated_by="'.$d->officername.'" 
                          where st_code ="'.$st_code.'" and ac_no="'.$ac_no.'" 
                          and file_name="'.$file_name.'" and election_id='.$election_id);
            } 
          }else{
            DB::table('counting_results_pdf')->insert([
                         ['st_code' => $st_code, 
                            'ac_no' => $ac_no, 
                            'round_id' => '0', 
                            'election_id' => $election_id, 
                            'election_type_id' => $election_type_id, 
                            'uploading_path' => $file_path, 
                            'file_name'=>$file_name, 
                            'created_by' => $d->officername, 
                            'uploaded_time'=>$datetime,
                            'created_at' => $datetime,
                            ]
                          ]);
           
          }
        }
          
        \Session::flash('success_mes', 'Your files has been successfully added');
          return Redirect::to('roac/counting/bpostal-data-entry');
      }
    
    public function store_upload_results(request $request){

            $data  = [];
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
     $this->validate(
                $request,
                    [ 
                      'resultstrends' => 'required|mimes:pdf|max:10000',
                     
                    ],
                    [ 
                      'resultstrends.required' => 'Please upload the valid pdf',
                      'resultstrends.mimes' => 'Please upload the valid pdf',
                      'resultstrends.max' => 'Please upload the maximum size 10mb',
                    ]
                  );
                 
                  $ext=$request->file('resultstrends')->getClientOriginalExtension();
                  if($ext!="pdf")
                    {
                      \Session::flash('error_mes', 'Only Pdf File uploaded');
                    return Redirect::to('roac/counting/result-publish?round_id='.$request->input('encround'));
                    }
       
        $st_code = $ele_details->ST_CODE;
        $election_id = $ele_details->ELECTION_ID;
        $election_type_id = $ele_details->ELECTION_TYPEID;
        $ac_no = $ele_details->CONST_NO ;
         
        $round  = $request->input('round');
        $encround  = $request->input('encround');
        $file_name = $request->input('file_name');
        $file = $request->file('resultstrends');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        
         $record = DB::table("counting_results_pdf")->where('st_code',$st_code)
                  ->where('ac_no',$ac_no)
                  ->where('round_id',$round)
                  ->where('election_id',$election_id)->first();

         
        
        if($request->file('resultstrends')){
          //Move Uploaded File
          $newfile =$st_code.'_'.$ac_no.'_round'.$round.'_'.time(); 

          $fileNewName =$newfile.'.'.$request->file('resultstrends')->getClientOriginalExtension();
         
         if(!validate_pdf_file($request->file('resultstrends'))){
           \Session::flash('error_mes', 'Only Pdf File uploaded');
           return Redirect::back()->withInput($request->all());
         }
      
          $destinationPath ='uploads1/results/E'.$election_id.'/'.$st_code .'/'.$ac_no;
      
          $file->move($destinationPath,$fileNewName);
          
          $file_path = $destinationPath .'/'.$fileNewName ;
          if(!file_exists($file_path)){
           \Session::flash('error_mes', 'File is not uploaded. Please try again.');
           return Redirect::back()->withInput($request->all());
         }
           
          if(!empty($record) ){
            if($request->file('resultstrends') != ''){
              $updateNomDetail = DB::update('update counting_results_pdf set 
                          uploading_path ="'. $file_path. '", 
                          added_update_at="'.$date.'",
                          updated_at="'.$datetime.'",
                          file_name="'.$file_name.'", 
                          updated_by="'.$d->officername.'" 
                          where st_code ="'.$st_code.'" and ac_no="'.$ac_no.'" 
                          and round_id="'.$round.'" and election_id='.$election_id);
            } 
          }else{
            DB::table('counting_results_pdf')->insert([
                         ['st_code' => $st_code, 
                            'ac_no' => $ac_no, 
                            'round_id' => $round, 
                            'election_id' => $election_id, 
                            'election_type_id' => $election_type_id, 
                            'uploading_path' => $file_path, 
                            'file_name'=>$file_name, 
                            'created_by' => $d->officername, 
                            'uploaded_time'=>$datetime,
                            'created_at' => $datetime,
                            ]
                          ]);
           
          }
        }
          
        \Session::flash('success_mes', 'Your files has been successfully added');
          return Redirect::to('roac/counting/result-publish?round_id='.encrypt_string($round));
      }
}  // end class results-declaration    
