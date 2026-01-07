<?php 
namespace App\Http\Controllers\Admin\mparty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use MPDF;
use App\commonModel;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use DB, Validator, Config, \PDF, Response;
use App\models\Common\{StateModel, FileModel, PcModel, AcModel, DistrictModel, ElectionModel};
use App\models\Admin\mparty\{MPartyModel,MpartyLogModel,SymbolModel}; 
 use App\Exports\{MpartyExport,ReconizedpartyExport,PartySymbolExport,DelistingExport,FreesymbolExport};
use Maatwebsite\Excel\Facades\Excel;
class MpartyReportController extends Controller
{
public $base            = '/mparty/';
public $folder  		= 'mparty';
public $action    		= 'mparty/';
public $view_path 		= "admin.mparty.report";

public function __construct()
{   
$this->middleware('adminsession');
$this->middleware(['auth:admin','auth']);
$this->middleware('mparty');
$this->commonModel = new commonModel();

$this->xssClean = new xssClean;
if(!Auth::check()){ 
return redirect('/officer-login');
}
}

public function index(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$party_type='';    
$party_type=$request->input('party_type');
if($party_type=="N") $pname=" National "; elseif($party_type=="S") $pname=" State "; 
elseif($party_type=="U") $pname=" Unrecognized "; else $pname=" type ";
$data['action']        = url('mparty/list-party-report');
$data['buttons']    = [];
    // $data['buttons'][]  = [
    // 'name' => 'Export Excel',
    // 'href' =>  url('mparty/partyreportsexcel').'?party_type='.$party_type,
    // 'target' => false
    // ];
    $data['buttons'][]  = [
    'name' => 'Export Pdf',
    'href' =>  url('mparty/partyreportspdf').'?party_type='.$party_type,
    'target' => false
    ];

$data['heading_title']="List of All ".$pname." Parties";
$data['bradcome']='List of All Parties';
$data['mpartytype'] = [
		['id' => '',  'name' => 'Select one'],
		['id' => 'N', 'name' => 'National'],
		['id' => 'S', 'name' => 'State'],
		['id' => 'U', 'name' => 'Unrecognized'],
		];


if(empty($party_type)){
$filter = [
'party_type' =>$party_type, 
'st_code' =>'S04', 
];

}
else {
$filter = [
'party_type' =>$party_type, 
'st_code' =>'S04', 
];
} 
$data['lists']=MPartyModel::get_allpartie($filter);

$data['party_type']=$party_type;
//dd($data);
return view($this->view_path.'.partyreport', $data);
}
public function partyreportspdf(request $request){  
        $user = Auth::user();
        $party_type='';    
		$party_type=$request->input('party_type'); 
        $filter = [
                'st_code'        =>'',
                'party_type'     =>$party_type,
                ];
        if($party_type=="N") $type=" National "; 
        elseif($party_type=="S") $type=" State "; 
		elseif($party_type=="U") $type=" Unrecognized "; else $type=" type "; 
	$st_code='';    $state_language='';
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
        $data['lists']=MPartyModel::get_allpartie($filter);
        $data['heading_title']="List Of ".$type." Parties";
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'partyreport-'.$data['ref_no'];
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
        
       // dd($data);
         ini_set("pcre.backtrack_limit", "5000000");
         $pdf = \MPDF::loadView($this->view_path.'.partyreportpdf',$data,[], $setting_pdf);
          return $pdf->download($name_excel.'.pdf'); 
        
      }
    public function partyreportsexcel(request $request){ 
    	$user = Auth::user();
        $party_type=$request->input('party_type'); 
        $filter = [
                'st_code'        =>'',
                'party_type'     =>$party_type,
                ];
        if($party_type=="N") $type=" National "; 
        elseif($party_type=="S") $type=" State "; 
		elseif($party_type=="U") $type=" Unrecognized "; else $type=" All type "; 
	  
        $data['lists']=MPartyModel::get_allpartie($filter);
        $data['heading_title']="List Of ".$type." Parties";
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'partyreport-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
         
       
    set_time_limit(6000);
    	$export_data = [];
    	//$headings[] = [$data['heading_title']];
		//$export_data[] = ['Sr. No.', 'Party Abbree','Party Name','Party Name in Hindi','Party Name In Hindi','Party type'];
    $i=0;
    foreach ($data['lists'] as $lis) { $i++;

    	if($lis['PARTYTYPE']=="N") $ptype=" National "; 
        elseif($lis['PARTYTYPE']=="S") $ptype=" State "; 
		elseif($lis['PARTYTYPE']=="U") $ptype=" Unrecognized ";  
      $export_data[] = [
        ($i)?$i:'',
        ($lis['PARTYABBRE'])?$lis['PARTYABBRE']:'',
        ($lis['PARTYNAME'])?$lis['PARTYNAME']:'',
        ($lis['PARTYHABBR'])?$lis['PARTYHABBR']:'',
        ($lis['PARTYHNAME'])?$lis['PARTYHNAME']:'',
        ($ptype)?$ptype:'',
      ];
    }
     
   return Excel::download(new MpartyExport($data['heading_title'], $export_data), $name_excel.'.xlsx');

}
public function state_wise_recognized_parties(Request $request){ 
	$data = [];
	$data['user_data'] = Auth::user();
	$party_type='S';    
	$data['action']    = url('mparty/state-wise-recognized-parties');

	$data['buttons']    = [];
    // $data['buttons'][]  = [
    // 	'name' => 'Export Excel',
    // 	'href' =>  url('mparty/state-wise-recognized-partiesexcel'),
    // 	'target' => false
    // ];
    $data['buttons'][]  = [
    	'name' => 'Export Pdf',
    	'href' =>  url('mparty/state-wise-recognized-partiespdf'),
    	'target' => false
    ];

	$data['heading_title']="Reports:-List of State wise Recognized Parties";
	$data['bradcome']='Reports:- Recognized Parties';
  
$filter = [
	'party_type' =>$party_type, 
	'st_code' =>'', 
	];
  
	$data['lists']=MPartyModel::get_parties($filter);
 
	//dd($data);
	return view($this->view_path.'.state-wise-recognized', $data);	

}
//state_wise_recognized_partiesexcel  start
 public function state_wise_recognized_partiesexcel(request $request){ 
    	$user = Auth::user();
        $party_type='S'; 
        $filter = [
					'party_type' =>$party_type, 
					'st_code' =>'', 
				 ];
        
        $data['lists']=MPartyModel::get_parties($filter);
        $data['heading_title']="Reports:-List of State wise Recognized Parties";
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'recognizedparty-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
         
       
    set_time_limit(6000);
    	$export_data = [];
    	 
    $i=0;
    foreach ($data['lists'] as $lis) { $i++;
    $export_data[] = [
        ($i)?$i:'',
        ($lis['PARTYABBRE'])?$lis['PARTYABBRE']:'',
        ($lis['PARTYNAME'])?$lis['PARTYNAME']:'',
        ($lis['ST_CODE'])?$lis['ST_CODE'].'-'.$lis['ST_NAME']:'',
      ];
    }
     
   return Excel::download(new ReconizedpartyExport($data['heading_title'], $export_data), $name_excel.'.xlsx');

}

public function state_wise_recognized_partiespdf(request $request){  
        $user = Auth::user();
        $party_type='S';    
		$filter = [
                'st_code'        =>'',
                'party_type'     =>$party_type,
                ];
        
        $data['font_data']="manny";
        $data['fonts']="freeserif";
        
        $lists1=MPartyModel::get_parties($filter);
        $lists=array();$i=0;
        foreach ($lists1 as $key => $v) {
        	$lists[$i]['PARTYABBRE']=$v['PARTYABBRE'];
        	$lists[$i]['PARTYNAME']=$v['PARTYNAME'];
        	$lists[$i]['ST_CODE']=$v['ST_CODE'];
        	$lists[$i]['ST_NAME']=$v['ST_NAME'];
        	$i++;
        }
        $data['lists']=$lists;
        //dd($lists);
        $data['heading_title']="Reports:-List of State wise Recognized Parties";
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'recognizedparty-'.$data['ref_no'];
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
        
       // dd($data);
         ini_set("pcre.backtrack_limit", "5000000");
         $pdf = \MPDF::loadView($this->view_path.'.reconizedpartypdf',$data,[], $setting_pdf);
          return $pdf->download($name_excel.'.pdf'); 
        
      }
    public function party_symbol_report(request $request){ 
    	$data = [];
		$data['user_data']      = Auth::user();
		$party_type='';    
		$party_type=$request->input('party_type');
		$data['party_type']=$party_type;
		$data['action']    = url('mparty/party-symbol-report');
		$data['buttons']    = [];
	    // $data['buttons'][]  = [
	    // 	'name' => 'Export Excel',
	    // 	'href' =>  url('mparty/party-symbol-reportexcel').'?party_type='.$party_type,
	    // 	'target' => false
	    // ];
	    $data['buttons'][]  = [
	    	'name' => 'Export Pdf',
	    	'href' =>  url('mparty/party-symbol-reportpdf').'?party_type='.$party_type,
	    	'target' => false
	    ];

		$data['heading_title']="Reports:-List Of Party Symbol";
		$data['bradcome']='Reports:- List Of Party Symbol';

		
		$data['mpartytype'] = [
		    ['id' => '',  'name' => 'Select one'],
		    ['id' => 'N', 'name' => 'National'],
		    ['id' => 'S', 'name' => 'State'],
		    ['id' => 'U', 'name' => 'Unrecognized'],
		];       
		$symbol_img='';    
		$data['symbol_img']=$request->input('symbol_img');
		$filter='';
		$filter = [
		    'symbol_img' =>$data['symbol_img'],
		    'party_type' =>$data['party_type'],
		  ];

		$data['results']=MPartyModel::partyassignsymbol($filter);
		//dd($data);
		return view($this->view_path.'.party-symbol', $data);
    }
   public function party_symbol_reportpdf(request $request){  
        $user = Auth::user();
        $party_type=$request->party_type;    
		        
        $data['font_data']="manny";
        $data['fonts']="freeserif";
        $symbol_img='';    
		$data['symbol_img']=$request->input('symbol_img');
		$filter='';
		$filter = [
		    'symbol_img' =>$symbol_img,
		    'party_type' =>$party_type,
		  ];

		$lists1=MPartyModel::partyassignsymbol($filter);
        
        $lists=array();$i=0;
        foreach ($lists1 as $key => $v) {
        	$lists[$i]['PARTYABBRE']=$v['PARTYABBRE'];
        	$lists[$i]['PARTYNAME']=$v['PARTYNAME'];
        	if($v['PARTYTYPE']=="N") $lists[$i]['PARTYTYPE']="National" ;
        	if($v['PARTYTYPE']=="S") $lists[$i]['PARTYTYPE']="State" ;
        	if($v['PARTYTYPE']=="U") $lists[$i]['PARTYTYPE']="Unrecognized" ;
            $lists[$i]['SYMBOL_DES']=$v['SYMBOL_DES'];
        	$i++;
        }
        
        $data['lists']=$lists;
       // dd($lists);
        $data['heading_title']="Reports:-List Of Party Symbol";
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'partysymbol-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
        $data['user']=\Auth::user()->officername;
        $data['print_date']=date('d-m-Y H:i:a');
        $setting_pdf = [
            'margin_top'        =>20,  
            'margin_bottom'     =>10,
            'margin_left'       =>10,  
            'margin_right'      =>10,
            'show_warnings'     =>false,
            'mode'              =>'utf-8',    
        ];
        
       // dd($data);
         ini_set("pcre.backtrack_limit", "5000000");
         $pdf = \MPDF::loadView($this->view_path.'.partysymbolpdf',$data,[], $setting_pdf);
          return $pdf->download($name_excel.'.pdf'); 
        
      } // end PDF
    public function party_symbol_reportexcel(request $request){ 
    	$user = Auth::user();
        $party_type=$request->party_type; 
        $symbol_img='';    
		$data['symbol_img']=$request->input('symbol_img');
		$filter='';
		$filter = [
		    'symbol_img' =>$symbol_img,
		    'party_type' =>$party_type,
		  ];

		$data['lists']=MPartyModel::partyassignsymbol($filter);
         
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'partysymbol-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
         
       
    set_time_limit(6000);
    	$export_data = [];
    $data['heading_title']="Reports:-List Of Party Symbol";	 
    $i=0;
    foreach ($data['lists'] as $lis) { $i++;
    	    if($lis['PARTYTYPE']=="N") $partytype="National" ;
        	if($lis['PARTYTYPE']=="S") $partytype="State" ;
        	if($lis['PARTYTYPE']=="U") $partytype="Unrecognized" ;
    $export_data[] = [
        ($i)?$i:'',
        ($lis['PARTYABBRE'])?$lis['PARTYABBRE']:'',
        ($lis['PARTYNAME'])?$lis['PARTYNAME']:'',
        ($partytype)?$partytype:'',
        ($lis['SYMBOL_DES'])?$lis['SYMBOL_DES']:'', 
      ];
    }   
     
   return Excel::download(new PartySymbolExport($data['heading_title'], $export_data), $name_excel.'.xlsx');

}
//delisting_party
public function delisting_report(Request $request){  
	$data = [];
	$data['user_data'] = Auth::user();
	 
	$data['action']        = url('mparty/delisting-report');
	    $data['buttons']    = [];
	    // $data['buttons'][]  = [
	    // 	'name' => 'Export Excel',
	    // 	'href' =>  url('mparty/delisting-reportexcel'),
	    // 	'target' => false
	    // ];
	    $data['buttons'][]  = [
	    	'name' => 'Export Pdf',
	    	'href' =>  url('mparty/delisting-reportpdf'),
	    	'target' => false
	    ];

		$data['heading_title']="Reports:-List Of Delisted Party";
		$data['bradcome']='Reports:- List Of Delisted Party';
	 
	   $data['results']=MPartyModel::delisting_report();
	 
	   //dd($data);
	   return view($this->view_path.'.delistingreport', $data);
	}
    public function delisting_reportpdf(request $request){  
        $user = Auth::user();
        
        $data['font_data']="manny";
        $data['fonts']="freeserif";
        $data['heading_title']="Reports:-List Of Delisted Party";
		$data['bradcome']='Reports:- List Of Delisted Party';
	 
	    $lists1=MPartyModel::delisting_report();

        $lists=array();$i=0;
        foreach ($lists1 as $key => $v) {
        	$lists[$i]['PARTYABBRE']=$v['PARTYABBRE'];
        	$lists[$i]['PARTYNAME']=$v['PARTYNAME'];
        	$lists[$i]['PARTYHABBR']=$v['PARTYHABBR'];
        	$lists[$i]['PARTYHNAME']=$v['PARTYHNAME'];
        	 
        	if($v['PARTYTYPE']=="N") $lists[$i]['PARTYTYPE']="National" ;
        	if($v['PARTYTYPE']=="S") $lists[$i]['PARTYTYPE']="State" ;
        	if($v['PARTYTYPE']=="U") $lists[$i]['PARTYTYPE']="Unrecognized" ;
             
        	$i++;
        }
        
        $data['lists']=$lists;
         
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'delistedparty-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
        $data['user']=\Auth::user()->officername;
        $data['print_date']=date('d-m-Y H:i:a');
        $setting_pdf = [
            'margin_top'        =>20,  
            'margin_bottom'     =>10,
            'margin_left'       =>10,  
            'margin_right'      =>10,
            'show_warnings'     =>false,
            'mode'              =>'utf-8',    
        ];
        
       // dd($data);
         ini_set("pcre.backtrack_limit", "5000000");
         $pdf = \MPDF::loadView($this->view_path.'.delistedpartypdf',$data,[], $setting_pdf);
          return $pdf->download($name_excel.'.pdf'); 
        
      } // end PDF
    public function delisting_reportexcel(request $request){ 
    	$user = Auth::user();
        $data['heading_title']="Reports:-List Of Delisted Party";
		$data['bradcome']='Reports:- List Of Delisted Party';
	 
	    $data['lists']=MPartyModel::delisting_report();
 
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'partysymbol-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
         
       
        set_time_limit(6000);
    	$export_data = [];
    	 
    $i=0;
    foreach ($data['lists'] as $lis) { $i++;
    		if($lis['PARTYTYPE']=="N") $partytype="National" ;
        	if($lis['PARTYTYPE']=="S") $partytype="State" ;
        	if($lis['PARTYTYPE']=="U") $partytype="Unrecognized" ;
    $export_data[] = [
        ($i)?$i:'',
        ($lis['PARTYABBRE'])?$lis['PARTYABBRE']:'',
        ($lis['PARTYNAME'])?$lis['PARTYNAME']:'',
        ($lis['PARTYHABBR'])?$lis['PARTYHABBR']:'',
        ($lis['PARTYHNAME'])?$lis['PARTYHNAME']:'',
        ($partytype)?$partytype:'',
         
      ];
    }
     
   return Excel::download(new DelistingExport($data['heading_title'], $export_data), $name_excel.'.xlsx');

}
public function list_symbol_report(Request $request){ 
	$data = [];
	$data['user_data'] = Auth::user();
	$data['action']        = url('mparty/list-symbol-report');
	$data['heading_title']="Reports:-List of Symbols";
	$data['bradcome']='Reports:- List of Symbols';
	$freesymbol='';    
	$freesymbol=$request->input('freesymbol');

 		$data['buttons']    = [];
	    // $data['buttons'][]  = [
	    // 	'name' => 'Export Excel',
	    // 	'href' =>  url('mparty/list-symbol-reportexcel').'?freesymbol='.$freesymbol,
	    // 	'target' => false
	    // ];
	    $data['buttons'][]  = [
	    	'name' => 'Export Pdf',
	    	'href' =>  url('mparty/list-symbol-reportpdf').'?freesymbol='.$freesymbol,
	    	'target' => false
	    ];

	
	$data['symboltype'] = [
	  ['id' => '',  'name' => '-- All --'],
	  ['id' => 'PARTY', 'name' => 'Party Assign'],
	  ['id' => 'F', 'name' => 'Alloted Symbol'],
	  ['id' => 'T', 'name' => 'Free Symbol'],
	];
 
	$filter='';
	$data['freesymbol']=$freesymbol;
	$filter = [
	        'freesymbol' =>$freesymbol,
	         ];
	$data['lists']=SymbolModel::reportsymbol($filter);
	$data['total']=count($data['lists']);
	//dd($data);
	return view($this->view_path.'.listsymbolreport', $data);
	}

  public function list_symbol_reportpdf(request $request){  
        $user = Auth::user();
        
        $data['font_data']="manny";
        $data['fonts']="freeserif";
        $freesymbol=$request->input('freesymbol');
    	if($freesymbol=='') {$free="All";}
    	elseif ($freesymbol=='PARTY') {
    		$free="Party";
    	}
    	elseif ($freesymbol=='F') {
    		$free="Alloted";
    	}
    	elseif ($freesymbol=='T') {
    		$free="Free";
    	}
    	  
        $data['heading_title']="Reports:-List of ".$free." Symbols";
		$data['bradcome']="Reports:-List of ".$free." Symbols";
		
  		$filter=''; 
		$filter = [
		        'freesymbol' =>$freesymbol,
		         ];
		$lists1=SymbolModel::reportsymbol($filter);

        $lists=array();$i=0;
        foreach ($lists1 as $key => $v) {
        	$lists[$i]['SYMBOL_DES']=$v['SYMBOL_DES'];
        	$lists[$i]['SYMBOL_HDES']=$v['SYMBOL_HDES'];
        	$i++;
        }
        
        $data['lists']=$lists;
         
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'listsymbol-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
        $data['user']=\Auth::user()->officername;
        $data['print_date']=date('d-m-Y H:i:a');
        $setting_pdf = [
            'margin_top'        =>20,  
            'margin_bottom'     =>10,
            'margin_left'       =>10,  
            'margin_right'      =>10,
            'show_warnings'     =>false,
            'mode'              =>'utf-8',    
        ];
        
       // dd($data);
         ini_set("pcre.backtrack_limit", "5000000");
         $pdf = \MPDF::loadView($this->view_path.'.listsymbolpdf',$data,[], $setting_pdf);
          return $pdf->download($name_excel.'.pdf'); 
        
      } // end PDF
    public function list_symbol_reportexcel(request $request){ 
    	$user = Auth::user();
    	$freesymbol=$request->input('freesymbol');
    	if($freesymbol=='') {$free="All";}
    	elseif ($freesymbol=='PARTY') {
    		$free="Party";
    	}
    	elseif ($freesymbol=='F') {
    		$free="Alloted";
    	}
    	elseif ($freesymbol=='T') {
    		$free="Free";
    	}
    	  
        $data['heading_title']="Reports:-List of ".$free." Symbols";
		$data['bradcome']="Reports:-List of ".$free." Symbols";
		
  		$filter=''; 
		$filter = [
		        'freesymbol' =>$freesymbol,
		         ];
		$data['lists']=SymbolModel::reportsymbol($filter);
        $data['ref_no']  =rand(100000,999999);
        $name_excel = 'listsymbol-'.$data['ref_no'];
        $data['file_name']=$name_excel; 
         
        set_time_limit(6000);
    	$export_data = [];
    	 
    $i=0;
    foreach ($data['lists'] as $lis) { $i++;
    $export_data[] = [
        ($i)?$i:'',
        ($lis['SYMBOL_DES'])?$lis['SYMBOL_DES']:'',
        ($lis['SYMBOL_HDES'])?$lis['SYMBOL_HDES']:'',
      ];
    }
     
   return Excel::download(new FreesymbolExport($data['heading_title'], $export_data), $name_excel.'.xlsx');

}
}    //  end class