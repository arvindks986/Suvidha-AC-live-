<?php  
		namespace App\Http\Controllers\Admin\mparty;
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
		use App\models\Admin\StatepartyModel;
    use App\models\Admin\StateSymbolModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		use Illuminate\Support\Facades\Crypt;
    use App\models\Admin\mparty\{MPartyModel,SymbolModel}; 
    use App\Exports\ExcelExport;
    use Maatwebsite\Excel\Facades\Excel;
 
class CeoPartyController extends Controller
{
    public $base      = '/mparty/';
    public $folder    = 'mparty';
    public $action    = 'mparty/';
    public $view_path = "admin.mparty.ceo";
 
   public function __construct()
        {   
			     $this->middleware('adminsession');
			     $this->middleware(['auth:admin','auth']);
			     $this->commonModel = new commonModel();
           $this->xssClean = new xssClean;
           $this->stateparty = new StatepartyModel;  
           $this->statesymbol = new StateSymbolModel;
			     $this->sym = new SymbolMaster();
			if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}
          $this->middleware('ceo');
          //$this->middleware('clean_url');
			 }

public function index(request $request){  
        $data = []; 
        $data['action']= url('mparty/ceo/state-party-update');  
	      $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $st_code=$d->st_code;
        $party_type='';
        $party_type=$request->party_type;
        $data['party_type'] =$party_type;
        //StatepartyModel::insert_party($st_code);
        //dd("hello");
        if(empty($party_type)){
          $filter = [
                  'party_type' =>'', 
                  'st_code' =>$st_code, 
                  ];
               }
          else {
          $filter = [
                    'party_type' =>$party_type, 
                    'st_code' =>$st_code, 
                    ];
            } 
	      $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->stateparty->Allpartybystate($filter); 
        $data['mpartytype'] = [
              ['id' => '',  'name' => 'Select one'],
              ['id' => 'N', 'name' => 'National'],
              ['id' => 'S', 'name' => 'State'],
              ['id' => 'U', 'name' => 'Unrecognized'],
          ];
        $data['user_data']=$d;
        $data['record']=$record;
        $data['st_name']=$st->ST_NAME;
        $data['state_language']=$state_language;  
        //dd($data);       
        return view($this->view_path.'.state-party-list', $data); 
       
	    }
     
  public function updateparty(Request $request){    //dd($request->all());
            $rules = ['party_vname'        => 'required',];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) { 
            return redirect::back()->withErrors($validator)->withInput();
            }
            $party_vname = $this->xssClean->clean_input($request->input('party_vname'));
            if(empty($party_vname)) {
             \Session::flash('error_messsage', 'enter Party Name vernacular');
                 return Redirect::back()->withInput($request->all());  
              }
              $id = $this->xssClean->clean_input(Check_Input($request->input('id')));
              $st_code =$this->xssClean->clean_input(Check_Input($request->input('st_code')));
               
          
            $up_data = array('party_vname'=>$party_vname,
                              'updated_by'=> Auth::user()->officername,
                              'updated_at'=>date("Y-m-d H:i:s"));
             $rec=updatedata('m_state_party','id',$id,$up_data);
             

             \Session::flash('success_mes', 'Party vernacular name updated successfully');
              return redirect::back();
             
      }


  public function symbol(request $request) {      
        $data['action']= url('mparty/ceo/symbol-list-update');  
        $user = Auth::user();
        $st_code=$user->st_code;
        //StateSymbolModel::insert_symbol($st_code);
       // dd("Hello");
        $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->statesymbol->getallsymbolbystate($st_code); 

        $data['user_data']=$user;
        $data['record']=$record;
        $data['st_name']=$st->ST_NAME;
        $data['state_language']=$state_language;  
       // dd($data);         
        return view($this->view_path.'.state-symbol-list', $data); 
       
      }   //
     
    function symbolupdate(Request $request) {    
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $symbol_vname = $this->xssClean->clean_input($request->input('symbol_vname'));
            if(empty($symbol_vname)) {
             \Session::flash('error_messsage', 'enter Symbol Name vernacular');
                 return Redirect::back()->withInput($request->all());  
              }
              $id = $this->xssClean->clean_input(Check_Input($request->input('id')));
              $st_code =$this->xssClean->clean_input(Check_Input($request->input('st_code')));
               
          
            $up_data = array('symbol_vname'=>$symbol_vname,
                              'updated_by'=> $d->officername,
                              'updated_at'=>date("Y-m-d H:i:s"));
             $rec=updatedata('m_state_symbol','id',$id,$up_data);
             

             \Session::flash('success_mes', 'Symbol vernacular name updated successfully');
            return redirect::back();
             
      }

  public function partywise_reports(request $request){  
        $data = []; 
        $user = Auth::user();
        $st_code=Auth::user()->st_code;
        $party_type='';
        $party_type=$request->party_type;
        $data['party_type'] =$party_type;
        
        $filter =[
              'party_type' =>$party_type, 
              'st_code' =>$st_code, 
              ];
        $data['filter']=$filter;
        $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->stateparty->Allpartybystate($filter); 
        $data['mpartytype'] = [
              ['id' => '',  'name' => 'Select one'],
              ['id' => 'N', 'name' => 'National'],
              ['id' => 'S', 'name' => 'State'],
              ['id' => 'U', 'name' => 'Unrecognized'],
          ];
        $data['action']= url('mparty/ceo/partywise-reports');
        $data['buttons']    = [];
        // $data['buttons'][]  = [
        // 'name' => 'Export Excel',
        // 'href' =>  url('mparty/ceo/partywisereportsexcel').'?party_type='.$party_type,
        // 'target' => false
        // ];
        $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('mparty/ceo/partywisereportspdf').'?st_code='.$st_code.'&party_type='.$party_type,
        'target' => false
        ];
 
        $data['user_data']=$user;
        $data['record']=$record;
        $data['st_name']=$st->ST_NAME;
        $data['state_language']=$state_language;  
        //dd($data);       
        return view($this->view_path.'.state-reports', $data); 
       
      }
  public function partywisereportspdf(request $request){  
        $data = []; 
        $user = Auth::user();
        $st_code=Auth::user()->st_code;
        $party_type='';
        $party_type=$request->party_type;       
        $filter =[
              'party_type' =>$party_type, 
              'st_code' =>$st_code, 
              ];
        $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->stateparty->Allpartybystate($filter);
      
        $data['user_data']=$user;
        $data['records']=$record;
        $data['st_name']=$st->ST_NAME;
        $data['state_language']=$state_language;  
        //dd($data);
        if($st_code=="S10" and $state_language=="KAN"){
            $data['font_data']="kannad";
            $data['fonts']="tunga";
          }
        elseif(($st_code=="S29"||$st_code=="S01") and $state_language=="TEL"){
          $data['font_data']="telugu";
          $data['fonts']="gautami";
        }else{
          $data['font_data']="manny";
          $data['fonts']="freeserif";
        }
        if($party_type=='N') $type="National";
        elseif($party_type=='S') $type="State";
        elseif($party_type=='U') $type="Unrecognized"; else $type="All";

        $data['heading_title']="List Of ".$type." Parties";
        $data['ref_no']  =rand(100000,999999);
        $data['subhead']= $data['ref_no'];
        $name_excel = 'partyreport-'.$st_code.'-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
        $data['user']=\Auth::user()->officername;
        $data['print_date']=date('d-m-Y H:i:a');
        $setting_pdf = [
            'margin_top'        =>30,  
            'margin_bottom'     =>10,
            'margin_left'       =>10,  
            'margin_right'      =>10,
            'show_warnings'     =>false,
            'mode'              =>'utf-8',    
        ];
       dd($data);
        $pdf = \MPDF::loadView($this->view_path.'.statepartyreport',$data,[], $setting_pdf);
        return $pdf->download($name_excel.'.pdf'); 
        
      }
public function partywisereportsexcel(request $request){  
      $data = []; 
      $st_code=Auth::user()->st_code;
      $party_type='';
      $party_type=$request->party_type;       
      $filter =[
        'party_type' =>$party_type, 
        'st_code' =>$st_code, 
        ];
      $st=getstatebystatecode($st_code);
      $state_language=getstatelanguage($st_code);
      $results=$this->stateparty->Allpartybystate($filter); 
      if($party_type=='N') $type="National";
      elseif($party_type=='S') $type="State";
      elseif($party_type=='U') $type="Unrecognized"; else $type="All";
      $data['heading_title']="List Of ".$type." Parties";

      $data['results']=$results;
      $data['st_name']=$st->ST_NAME;
      $data['state_language']=$state_language; 
      $data['ref_no']  =rand(100000,999999);
      $name_excel = 'partyreport-'.$st_code.'-'.$data['ref_no'];

    set_time_limit(6000);
    $export_data = [];
    $headings[] = [$data['heading_title']];

    $export_data[] = ['State Name:-',$data['st_name'],'State vernacular Language:-',$data['state_language']];

    $export_data[] = ['Sr. No.', 'Party Abbree','Party Name In English','Party Name In Vernacular'];
    $i=0;
    foreach ($data['results'] as $lis) { $i++;
      $export_data[] = [
        ($i)?$i:'',
        ($lis->party_abbre)?$lis->party_abbre:'',
        ($lis->party_name)?$lis->party_name:'',
        // ($lis->party_hname)?$lis->party_hname:'',
        ($lis->party_vname)?$lis->party_vname:'',
      ];
    }
     
   return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');

   //  $export_data = [];
   //  $export_data[] = [$data['heading_title']];

   //  $export_data[] = [$data['st_name'],'',$data['state_language']];
   //  $export_data[] = ['Sr. No.', 'Party Abbree','Party Name In English','Party Name In Hindi', 'Party Name In Vernacular'];
   //  $i=0;
   //  foreach ($data['results'] as $lis) { $i++;
   //    $export_data[] = [
   //      ($i)?$i:'',
   //      ($lis->party_abbre)?$lis->party_abbre:'',
   //      ($lis->party_abbre)?$lis->party_name:'',
   //      ($lis->party_abbre)?$lis->party_hname:'',
   //      ($lis->party_abbre)?$lis->party_vname:'',
   //    ];
   //  }
     

   // //return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');
   //  \Excel::ExcelExport($name_excel, function($excel) use($export_data) {
   //      $excel->sheet('Sheet1', function($sheet) use($export_data) {
   //        $sheet->mergeCells('A1:E1');
   //        $sheet->mergeCells('A2:B2');
   //        $sheet->mergeCells('D2:E2');
   //        $sheet->cell('A1', function($cell) {
   //          $cell->setAlignment('center');
   //          $cell->setFontWeight('bold');
   //        });
   //        $sheet->fromArray($export_data,null,'A1',false,false);
   //      });
   //  })->export('xls'); 

}
public function symbol_reports(request $request) {      
        $user = Auth::user();
        $st_code=$user->st_code;
         
        $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->statesymbol->getallsymbolbystate($st_code); 
        
        $data['buttons']    = [];
        // $data['buttons'][]  = [
        // 'name' => 'Export Excel',
        // 'href' =>  url('mparty/ceo/symbolreportsexcel'),
        // 'target' => false
        // ];
        $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('mparty/ceo/symbolreportspdf'),
        'target' => false
        ];
        $data['user_data']=$user;
        $data['record']=$record;
        $data['st_name']=$st->ST_NAME;
        $data['state_language']=$state_language;  
                
        return view($this->view_path.'.statesymbolreport', $data); 
       
      }   //
   public function symbolreportspdf(request $request){  
        $data = []; 
        $user = Auth::user();
        $st_code=Auth::user()->st_code;
        $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->statesymbol->getallsymbolbystate($st_code); 
         
        $data['user_data']=$user;
        $data['records']=$record;
        $data['st_name']=$st->ST_NAME;
        $data['state_language']=$state_language;  
        //dd($data);
        if($st_code=="S10" and $state_language=="KAN"){
            $data['font_data']="kannad";
            $data['fonts']="tunga";
          }
        elseif(($st_code=="S29"||$st_code=="S01") and $state_language=="TEL"){
          $data['font_data']="telugu";
          $data['fonts']="gautami";
        }else{
          $data['font_data']="manny";
          $data['fonts']="freeserif";
        }

        $data['heading_title']="List Of All Symbol";
        $data['ref_no']  =rand(100000,999999);
        $data['subhead']= $data['ref_no'];
        $name_excel = 'symbol-'.$st_code.'-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
        $data['user']=\Auth::user()->officername;
        $data['print_date']=date('d-m-Y H:i:a');
        $setting_pdf = [
            'margin_top'        =>30,  
            'margin_bottom'     =>10,
            'margin_left'       =>10,  
            'margin_right'      =>10,
            'show_warnings'     =>false,
            'mode'              =>'utf-8',    
        ];
       //dd($data);
        $pdf = \MPDF::loadView($this->view_path.'.symbolreportpdf',$data,[], $setting_pdf);
        return $pdf->download($name_excel.'.pdf'); 
        
      } 
  public function symbolreportsexcel(request $request){  
      $data = []; 
        $user = Auth::user();
        $st_code=Auth::user()->st_code;
        $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $results=$this->statesymbol->getallsymbolbystate($st_code);

      $data['heading_title']="List Of All Symbol";

      $data['results']=$results;
      $data['st_name']=$st->ST_NAME;
      $data['state_language']=$state_language; 
      $data['ref_no']  =rand(100000,999999);
      $name_excel = 'symbolreport-'.$st_code.'-'.$data['ref_no'];

    set_time_limit(6000);
    $export_data = [];
    $headings[] = [$data['heading_title']];
     
    $export_data[] = ['State Name:-',$data['st_name'],'State vernacular Language:-'.$data['state_language']];

    $export_data[] = ['Sr. No.', 'Symbol Name In English', 'Symbol Name In Vernacular'];
    $i=0;
    foreach ($data['results'] as $lis) { $i++;
      $export_data[] = [
        ($i)?$i:'',
        ($lis->symbol_name)?$lis->symbol_name:'',
        // ($lis->symbol_hname)?$lis->symbol_hname:'',
        ($lis->symbol_vname)?$lis->symbol_vname:'',
         
      ];
    }
     
   return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'.xlsx');
   }
  public function generate_party(request $request){ 
         $list_state=DB::table('m_state')->orderBy('m_state.ST_CODE', 'ASC')->get();
         
        foreach($list_state as $st)
          {  set_time_limit(0);
            
            StatepartyModel::insert_party($st->ST_CODE);

          }
  }
  public function generate_symbol(request $request){ 
          $list_state=DB::table('m_state')->orderBy('m_state.ST_CODE', 'ASC')->get();
         
        foreach($list_state as $st)
          {  set_time_limit(0);
            
            StateSymbolModel::insert_symbol($st->ST_CODE);

          }
  }
}  // end class    
