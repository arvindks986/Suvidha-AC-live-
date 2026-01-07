<?php 
namespace App\Http\Controllers\Admin\counting;
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
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\models\Admin\CountingFinalize;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
//current

class CountingFinalizeReportController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/counting-finalize-status';
  public $action_pc     = 'eci/counting-finalize-status/state';
  public $view_path     = "admin.ac.eci";

  public function __construct(){
    $this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->middleware(function ($request, $next) {
        if(Auth::user() && Auth::user()->role_id=='26'){
          $this->action_state  = str_replace('eci','eci-agent',$this->action_state);
          $this->action_pc     = str_replace('eci','eci-agent',$this->action_pc);
          $this->action_ac     = str_replace('eci','eci-agent',$this->action_ac);
		  
        }
        return $next($request);
    });
  }

  public function CountingFinalize(Request $request){

 
      $data = [];
      $request_array = [];

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Null PS Count Report';
   
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;

      if(Auth::user()->role_id == '4'){
        $data['state']  = Auth::user()->st_code;
      }

      $states = StateModel::get_states();
      $data['states'] = [];
      foreach($states as $result){
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
          ];
        }

        if(Auth::user()->role_id == '7' || Auth::user()->role_id == '27'){
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
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];



      $data['action']         = url($this->action_state);

      $results                = [];
      $filter_election = [
        'state'         => $data['state'],
      ];


      $object_states = CountingFinalize::get_reports($filter_election);

	 // dd($object_states);
  
      $data['results']    =   $object_states;
      $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];
  /*    
       if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
            return $data;
       }
*/
      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.counting.CountingFinalize', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }
  
  public function getNulledPsData($st_code,$ac_no){
	  
	  $new_table=strtolower("counting_ps_".$st_code);
	  $sqllist = DB::select( DB::raw("SELECT ps.* FROM `polling_station` AS ps
				LEFT JOIN ".$new_table." AS cps ON ps.`AC_NO` = cps.ac_no AND ps.ps_no = cps.ps_no
				WHERE cps.ps_no IS NULL AND ps.`ST_CODE` = '$st_code' AND ps.ac_no = '$ac_no'") );
		//dd($sqllist);
		
		$margin_sql = DB::table('winning_leading_candidate')->where('st_code',$st_code)->where('ac_no',$ac_no)->first();
		
		$rejected_vote_sql = DB::table('round_master')->where('st_code',$st_code)->where('ac_no',$ac_no)->first();

		$vote_margin = 0;
		$rejected_votes = 0;
		$postal_total_votes = 0;
		
		if($margin_sql){
			$vote_margin = $margin_sql->margin;
		}
		
		if($rejected_vote_sql){
			$rejected_votes = $rejected_vote_sql->rejected_votes;
			$postal_total_votes = $rejected_vote_sql->postal_total_votes;
		}

		
		
		$data['empty_ps_list'] = $sqllist;
		$data['votes_margin'] = $vote_margin;
		$data['votes_margin_data'] = $margin_sql;
		$data['rejected_votes'] = $rejected_votes;
		$data['st_name'] = DB::table('m_state')->where('ST_CODE',$st_code)->first()->ST_NAME;
		$data['ac_name'] = DB::table('m_ac')->where('ST_CODE',$st_code)->where('AC_NO',$ac_no)->first()->AC_NAME;
		$data['user_data'] = Auth::user();
		
		return view($this->view_path.'.counting.check-empty-polling-station', $data);
  }


  public function CountingPsNullTotalAc(Request $request){

 
      $data = [];
      $request_array = [];
      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }


		$data['dist_no'] = '';

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Null PS Count Report';
   
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;

      if(Auth::user()->role_id == '4'){
		  $this->action_state = 'acceo/counting/counting-ps-null-ac';
        $data['state']  = Auth::user()->st_code;
        $data['distname']  = '';
        $data['stname']  = getstatebystatecode(Auth::user()->st_code);
      }

	if(Auth::user()->role_id == '5'){
		$this->action_state = 'acdeo/counting/counting-ps-null-ac';
        $data['state']  = Auth::user()->st_code;
        $data['dist_no']  = Auth::user()->dist_no;
        $data['distname']  = getdistrictbydistrictno(Auth::user()->st_code,Auth::user()->dist_no);
        $data['stname']  = getstatebystatecode(Auth::user()->st_code);
      }
	//dd($data);
		

      $states = StateModel::get_states();
      $data['states'] = [];
      foreach($states as $result){
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
          ];
        }
		
		if(Auth::user()->role_id == '5' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
          ];
        }

        if(Auth::user()->role_id == '7' || Auth::user()->role_id == '27'){
          $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
          ];
        }
      }
      
	 // dd($data);

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'?excel=yes').'&'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'?pdf=yes').'&'.implode('&', $request_array),
        'target' => true
      ];



      $data['action']         = url($this->action_state);

      $results                = [];
      $filter_election = [
        'state'         => $data['state'],
        'dist_no'       => $data['dist_no'],
      ];
		
	

      $object_states = CountingFinalize::get_ps_null_ac($filter_election);
      //$object_states = CountingFinalize::get_reports($filter_election);

	// dd($filter_election);
	 $dataArr1 = array();
	 
	 foreach($object_states as $k=>$v){
		$st_code = $v->st_code;
		$ac_no = $v->acno;
		$new_table=strtolower("counting_ps_".$st_code);
		$sqllist = DB::select( DB::raw("SELECT ps.* FROM `polling_station` AS ps
				LEFT JOIN ".$new_table." AS cps ON ps.`AC_NO` = cps.ac_no AND ps.ps_no = cps.ps_no
				WHERE cps.ps_no IS NULL AND ps.`ST_CODE` = '$st_code' AND ps.ac_no = '$ac_no'") );
		//dd($sqllist);
		$total_voter = 0;
		foreach($sqllist as $key=>$val){
			$total_voter = $total_voter + $val->electors_total;
		}
		
		$margin_sql = DB::table('winning_leading_candidate')->where('st_code',$st_code)->where('ac_no',$ac_no)->first();
		
		$rejected_vote_sql = DB::table('round_master')->where('st_code',$st_code)->where('ac_no',$ac_no)->first();

		$vote_margin = 0;
		$rejected_votes = 0;
		$postal_total_votes = 0;
		$counting_status = 0;
		$result_status = 0;
		
		if($margin_sql){
			$vote_margin = $margin_sql->margin;
			$counting_status = $margin_sql->status;
			$result_status = $margin_sql->status;
		}
		
		if($rejected_vote_sql){
			$rejected_votes = $rejected_vote_sql->rejected_votes;
			$postal_total_votes = $rejected_vote_sql->postal_total_votes;
		}
		
		$count_str = '';
		$result_str = '';
		if($counting_status==0){
			$count_str = 'In Progress';
		}else{
			$count_str = 'Completed';
		}
		
		if($result_status==0){
			$result_str = 'Not Declared';
		}else{
			$result_str = 'Declared';
		}
		
		$noofps = 0;
		$novotes = 0;
		
		$dataArr1[$k]['ac_no'] = $v->acno;
		$dataArr1[$k]['ac_name'] = $v->ac_name;
		$dataArr1[$k]['counting_status'] = $count_str;
		$dataArr1[$k]['votes_margin'] = $vote_margin;
		$dataArr1[$k]['rejected_postal'] = $rejected_votes;
		$dataArr1[$k]['noofps'] = count($sqllist);
		$dataArr1[$k]['novotes'] = $total_voter;
		$dataArr1[$k]['result_status'] = $result_str;

		
	 }
  
      $data['results']    =   $object_states;
      $data['user_data']  =   Auth::user();
      $data['dataArr']  =   $dataArr1;

       $data['heading_title_with_all'] = $data['heading_title'];
  /*    
       if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
            return $data;
       }
*/
      if($request->has('excel')){
		set_time_limit(6000);
		$export_data = [];
		$export_data[] = [$data['heading_title']];
		$export_data[] = ['Serial No', 'AC No','AC Name','Counting status','Vote Margin between leading & Trailing candidates','Number of Rejected PB','No of PS','Total votes','Result'];

		$i=1;
		foreach ($data['dataArr'] as $result) {
		  $export_data[] = [
			$i,
			$result['ac_no'],
            $result['ac_name'],
            $result['counting_status'],
            $result['votes_margin'],
            $result['rejected_postal'],
            $result['noofps'],
            $result['novotes'],
            $result['result_status']
		  ];
		  $i++;
		}

		//$export_data[] = ['TOTAL','','','','',$sum ? $sum : '0'];
		$name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

		$headings[] = [$data['heading_title']];
		return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
		
      }
	  
	  if($request->has('pdf')){
		set_time_limit(6000);
		$name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
		$pdf = \PDF::loadView($this->view_path.'.counting.CountingPSNullPdf',$data);
		return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
      }

      return view($this->view_path.'.counting.CountingPSNull', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

 
  public function CountingFinalizeExcel(Request $request){

    set_time_limit(6000);
    $data = $this->CountingFinalize($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['State', 'AC Name - No','Nomination Module Finalized','Counting Module Finalized','Finalized By Ro','Finalized By CEO'];


    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis->st_name,
        $lis->acno.'-'.$lis->ac_name,
		$lis->NominationFinalize,
        $lis->CountingFinalize,
		$lis->FinalizeRo,
        $lis->FinalizeCeo,
      ];
    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:D1');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }

  public function CountingFinalizePdf(Request $request){
    $data = $this->CountingFinalize($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.counting.CountingFinalizePdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  
  
  public function CountingFinalizeTotal(Request $request){
    
      $data = [];
      $request_array = [];
	  
	
      //set title
      $title_array  = [];
      $data['heading_title'] = 'Counting Finalize Total';
   
      $data['filter_buttons'] = $title_array;

      if(Auth::user()->role_id == '4'){
        $data['state']  = Auth::user()->st_code;
      }
      

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'Total/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'Total/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];



      $data['action']         = url($this->action_state);

      $results                = [];
      

      $object_states = CountingFinalize::get_states();

  
      $data['results']    =   $object_states;
      $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];
  /*    
       if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
            return $data;
       }
*/
      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.counting.CountingFinalizeTotal', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }


	  public function CountingPsNullTotal(Request $request){
    
      $data = [];
      $request_array = [];
	  $this->action_state = 'eci/counting-ps-null';
	
      //set title
      $title_array  = [];
      $data['heading_title'] = 'Counting PS Null Total';
   
      $data['filter_buttons'] = $title_array;

      if(Auth::user()->role_id == '4'){
        $data['state']  = Auth::user()->st_code;
      }
      

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];



      $data['action']         = url($this->action_state);

      $results                = [];
      

      $object_states = CountingFinalize::get_ps_null();

  
      $data['results']    =   $object_states;
      $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];
  /*    
       if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
            return $data;
       }
*/
      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.counting.CountingPsNullTotal', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }



   public function CountingFinalizeTotalExcel(Request $request){

    set_time_limit(6000);
    $data = $this->CountingFinalizeTotal($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    //$export_data[] = ['', 'Index','','Card','','Voters','','Finalized','','Status'];

    $export_data[] = ['State Name', 'Total ACs','Nomination Module Finalized','Counting Module Finalized','ACs Finalized By RO','ACs Finalized By CEO'];
    $headings[]=[];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis->st_name,
        $lis->total_ac,
		$lis->NominationFinalize,
		$lis->CountingFinalize,
		$lis->finalize,
		$lis->FinalizeCeo,
      ];
    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:D1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function CountingFinalizeTotalPdf(Request $request){
    $data = $this->CountingFinalizeTotal($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.counting.CountingFinalizeTotalPdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

}  // end class