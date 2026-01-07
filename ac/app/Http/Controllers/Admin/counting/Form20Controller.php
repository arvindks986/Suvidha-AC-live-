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
use App\Http\Controllers\Admin\counting\BoothCountingController;
use Common;
use App\models\Admin\{StateModel,AcModel};

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class Form20Controller  extends Controller
{

  public $folder        = 'counting';
  public $view          = "admin.counting";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $base          = 'roac';
  public $view_path = "admin.counting.ro";

  public function __construct(Request $request)
  {
    $this->commonModel = new commonModel();
    $this->xssClean = new xssClean;
    $this->boothcounting=new BoothCountingModel;
    $this->users    = new UsercountingModel;
    $this->CountingModel = new ACCountingModel();
    $this->postal = new PostalCountingModel();
    $this->middleware(function ($request, $next) {
      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id        = $default_values['role_id'];
      $this->ps_no          = $default_values['ps_no'];
      return $next($request);
    });
  }



  public function get_form_20(Request $request){
    $data = [];
    $data['buttons'] = [];
    $filter = [
      'st_code' => $this->st_code,
      'dist_no' => $this->dist_no,
      'ac_no'   => $this->ac_no,
      'role_id' => $this->role_id,
    ];
    $data['results'] = [];
    $results = AcModel::get_distinct_acs($filter);
    foreach ($results as $key => $iterate_ac) {
      $st_name = '';
      $state_object = StateModel::get_state_by_code($iterate_ac['st_code']);
      if($state_object){
        $st_name  = $state_object['ST_NAME'];
      }
      $request_string = "?st_code=".$iterate_ac['st_code']."&ac_no=".$iterate_ac['ac_no'];
      $data['results'][] = [
        'st_name' => $st_name,
        'ac_name' => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
        'href_excel'  => Common::generate_url("counting/get_form_20/excel").$request_string,
        'href_pdf'    => Common::generate_url("counting/get_form_20/pdf").$request_string,
      ];

    }
    //form filters
    $data['filter_action'] = Common::generate_url("counting/get_form_20");
    $form_filter_array = [
      'st_code'     => true,
      'dist_no'     => true, 
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;
    $data['user_data']      = Auth::user();

    return view($this->view.'.reports.get_form_20', $data);  
    }


  // excelexport_excel_form20

  public function excel(Request $request){

    if(!$this->st_code || !$this->ac_no){
      return Redirect::back();
    }

        $GLOBALS['cellarr']=array('0' =>'A8','1' =>'B8','2' =>'C8','3' =>'D8','4' =>'E8','5' =>'F8','6' =>'G8','7' =>'H8',
          '8' =>'I8','9' =>'J8', '10' =>'K8','11' =>'L8','12' =>'M8','13' =>'N8','14' =>'O8',
              '15' =>'P8','16' =>'Q8','17' =>'R8','18' =>'S8','19' =>'T8', '20' =>'U8','21' =>'V8','22' =>'W8','23' =>'X8',
              '24' =>'Y8', '25' =>'Z8', '26' =>'AA8','27' =>'AB8','28' =>'AC8','29' =>'AD8','30' =>'AE8','31' =>'AF8',
              '32' =>'G8','33' =>'H8',
              '34' =>'I8','35' =>'J8', '36' =>'K8','37' =>'L8','38' =>'M8','39' =>'N8','40' =>'O8',
              '41' =>'P8','42' =>'Q8','43' =>'R8','44' =>'S8','45' =>'T8', '46' =>'U8','47' =>'V8','48' =>'W8',
              '49' =>'X8', '50' =>'Y8', '51' =>'Z8',);
            $data=[];
            $st_code=$this->xssClean->clean_input($this->st_code);
            $ac_no=$this->xssClean->clean_input($this->ac_no);
            $election_id=Auth::user()->election_id;
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
     
     if(isset($data['results'])){
      foreach ($data['results'] as $lists) {

        foreach ($lists as $lis) {
             if($lis==0) $export_data[$i][$j]='0';
             else
             $export_data[$i][$j] =$lis;
             $j++;
          }   // end foreach
      $i++;
      } // end foreach 
     }
  
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
         foreach($data['grand_allsum'] as $d){ $j++;    
               if($d==0) 
                        $export_data[$i][$j]='0'; 
              else  
                        $export_data[$i][$j] =$d;
                 
         }  


    //dd($export_data); 
    $name_excel = 'form20-'.strtolower($data['st_code'])."_".$data['ac_no'].'_'.date('d-m-Y').'_'.time();
    $headings[] = [];

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    // \Excel::create($name_excel, function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:J1');
    //       $sheet->mergeCells('A2:J2');
    //       $sheet->mergeCells('A3:J3');
    //       $sheet->mergeCells('A4:J4');
    //       $sheet->mergeCells('A5:J5');
    //       $sheet->mergeCells('A6:J6');
    //      // $sheet->mergeCells('A8:B8');
    //       $sheet->mergeCells('C7:K7');
          
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->cell('A2', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->cell('A3', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->cell('A4', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //      $sheet->cell('A5', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //      $sheet->cell('A6', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //         for($c=0; $c<=$GLOBALS['totalcandidate']+6;$c++){
    //          $newcell=strtoupper($GLOBALS['cellarr'][$c]);
            
    //           $sheet->cell($newcell, function($cell) {
    //                   $cell->setTextRotation(90);
    //                   $cell->setFontWeight('bold');
    //          });
    //        }
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');
  }

  public function pdf(Request $request){
	ini_set("pcre.backtrack_limit", "5000000");
    if(!$this->st_code || !$this->ac_no){
      return Redirect::back();
    }
            $data=[];
            $st_code=$this->xssClean->clean_input($this->st_code);
            $ac_no=$this->xssClean->clean_input($this->ac_no);
            $election_id=Auth::user()->election_id;
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
  if(isset($data['results'])){
  $data['sub_results']=array_chunk($data['results'],15);
  }else{
	 $data['sub_results'] = []; 
  }

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
    'st_code'     => $ele_details->ST_CODE,
    'election_id' => $ele_details->ELECTION_ID,
    'ac_no'     =>$d->ac_no,
    'pc_no'     =>'',
    'table'         =>"counting_master_".strtolower($ele_details->ST_CODE), 
];
 

$object = $this->boothcounting->get_previous_total($data);

$data['previous_vote']=$object;
$table_details=$this->boothcounting->get_table_master_details($filter);
$round_details=$this->boothcounting->roundsechudle($filter);

$filter_data = [
    'st_code'       =>$ele_details->ST_CODE,
    'pc_no'       =>'',
    'election_id'   =>$ele_details->ELECTION_ID,
    'ac_no'       =>$d->ac_no,
    'round_id'      =>$round_id,
    'total_no_tables' =>$table_details->total_no_tables,
    'table_name'      =>$new_table,
];
 $publish= $this->boothcounting->checkpublish($filter_data);
 
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
              $data['publish'] = $publish;
             $data['grandresults'] = $grandresults;
              //dd($data); 
             return view($this->view_path.'.publish-results', $data);
         }
}  // end class results-declaration    
