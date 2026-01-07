<?php 
namespace App\Http\Controllers\Admin\index;
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
use App\models\Admin\IndexCardFinalize;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
//current

class IndexCardFinalizeController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/indexcard/IndexCardFinalize';
  public $action_pc     = 'eci/index/IndexCardFinalize/state';
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

  public function IndexCardFinalize(Request $request){

    if(Auth::user()->role_id == '27'){
      $this->action_state  = 'eci-index/indexcard/IndexCardFinalize';
      $this->action_pc     = 'eci-index/indexcard/IndexCardFinalize/state';
    }
    
      $data = [];
      $request_array = [];

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Index Card Finalization AC Wise Report';
   
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


      $object_states = IndexCardFinalize::get_reports($filter_election);

  
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

      return view($this->view_path.'.index.IndexCardFinalize', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

 
  public function IndexCardFinalizeExcel(Request $request){

    set_time_limit(6000);
    $data = $this->IndexCardFinalize($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    //$export_data[] = ['', 'Index','','Card','','Voters','','Finalized','','Status'];

    $export_data[] = ['State', 'AC Name - No','Nomination Module Finalized','Counting Module Finalized','Finalized By Ro','Finalized By CEO'];
    $headings[]=[];

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

  public function IndexCardFinalizePdf(Request $request){
    $data = $this->IndexCardFinalize($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.index.IndexCardFinalizePdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  
  
  public function IndexCardFinalizeTotal(Request $request){
    
      $data = [];
      $request_array = [];
	  
	  if(Auth::user()->role_id == '27'){
      $this->action_state  = 'eci-index/indexcard/IndexCardFinalize';
      $this->action_pc     = 'eci-index/indexcard/IndexCardFinalize/state';
    }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Index Card Finalize Total';
   
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
      

      $object_states = IndexCardFinalize::get_states();

  
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

      return view($this->view_path.'.index.IndexCardFinalizeTotal', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }



   public function IndexCardFinalizeTotalExcel(Request $request){

    set_time_limit(6000);
    $data = $this->IndexCardFinalizeTotal($request->merge(['is_excel' => 1]));

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

  public function IndexCardFinalizeTotalPdf(Request $request){
    $data = $this->IndexCardFinalizeTotal($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.index.IndexCardFinalizeTotalPdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

}  // end class