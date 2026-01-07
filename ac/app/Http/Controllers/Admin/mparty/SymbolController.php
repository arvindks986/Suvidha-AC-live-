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
use App\models\Admin\mparty\{SymbolModel,SymbolLogModel}; 

class SymbolController extends Controller
{
public $base            = '/mparty/';
public $folder  		= 'mparty';
public $action    		= 'mparty/';
public $view_path 		= "admin.mparty.party";

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

$data['action']        = url('mparty/add-symbol');
$data['eaction']        = url('mparty/edit-symbol');
$data['faction']        = url('mparty/free-symbol');
$data['heading_title']="List of All  Symbol";
$data['bradcome']='List of All Symbol';
$freesymbol='';    
$freesymbol=$request->input('freesymbol');
$symbol_img='';    
$symbol_img=$request->input('symbol_img');

$data['symboltype'] = [
  ['id' => '',  'name' => '-- All --'],
  ['id' => 'PARTY', 'name' => 'Party Assign'],
  ['id' => 'F', 'name' => 'Alloted Symbol'],
  ['id' => 'T', 'name' => 'Free Symbol'],
];
$data['symbol'] = [
  ['id' => '',  'name' => '-- All --'],
  ['id' => 'NOT', 'name' => 'Not Uploaded'],
  ['id' => 'T', 'name' => 'Uploaded'], 
];
$filter='';

$data['freesymbol']=$freesymbol;
$data['symbol_img']=$symbol_img;
$filter = [
        'freesymbol' =>$freesymbol,
        'symbol_img' =>$symbol_img, 
        ];
$data['lists']=SymbolModel::get_allsymbol($filter);
$data['total']=count($data['lists']);
//  dd($data);
return view($this->view_path.'.listsymbol', $data);
}

//add_new_party
public function add_symbol(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/add-new-symbol');
$data['heading_title']  ="Add New Symbol";
$data['bradcome']       ='Add New Symbol';
 
$form_data =$this->get_form($request, $data);
$data = array_merge($form_data, $data);
// dd($data);
return view($this->view_path.'.addnewsymbol', $data);
}

private function get_form($request,$data = array()){
//dd($data);

if($request->old('symbol_no')){
$data['symbol_no']  = $request->old('symbol_no');
}else if(isset($data['lists']) and ($data['lists'])){
$data['symbol_no']  = $data['lists']['SYMBOL_NO'];
}else{
$data['symbol_no']  =''; 
}

if($request->old('symbol_des')){
$data['symbol_des']  = $request->old('symbol_des');
}else if(isset($data['lists']) and ($data['lists'])){
$data['symbol_des']  =  $data['lists']['SYMBOL_DES'];
}else{
$data['symbol_des']  =''; 
}

if($request->old('symbol_hdes')){
$data['symbol_hdes']  = $request->old('symbol_hdes');
}else if(isset($data['lists']) and ($data['lists'])){
$data['symbol_hdes']  =  $data['lists']['SYMBOL_HDES'];
}else{
$data['symbol_hdes']  =''; 
}

if($request->old('ind_symbol')){
$data['ind_symbol']  = $request->old('ind_symbol');
}else if(isset($data['lists']) and ($data['lists'])){
$data['ind_symbol']  =  $data['lists']['Ind_Symbol'];
}else{
$data['ind_symbol']  =''; 
}
if(isset($data['lists']) and ($data['lists'])){
//if($data['lists']['Symbol_Img'] && file_exists($data['lists']['Symbol_Img'])){
$data['symbol_img']  =  $data['lists']['Symbol_Img'];
$data['thumb']       = url( $data['lists']['Symbol_Img']);
}else{
$data['symbol_img']  = '';
$data['thumb']       = url('img/vendor/avtar.jpg');
}

if($request->old('remarks')){
$data['remarks']  = $request->old('remarks');
}else if(isset($data['lists']) and ($data['lists'])){
$data['remarks']  =  $data['lists']['remarks'];
}else{
$data['remarks']  =''; 
}
return $data;
}



public function save_symbol(Request $request){ //dd($request->all());
$this->validate( 
                $request, 
                [
                 'symbol_des'        => 'required|unique:m_symbol',
                 'symbol_hdes'       => 'required|min:3|max:255',
                 'symbol_img'        =>'image|mimes:jpeg,png,jpg|max:500',
                ],
                [ 
                  'symbol_des.required' => 'Please enter symbol Name in English',
                  'symbol_des.unique' => 'The symbol has already been taken.', 
                  'symbol_img.image'=>'Please only jpg, jpeg, png format',
                  'symbol_img.max'=>'image size maximum 500kb',
                  'symbol_hdes.required'=>'Please enter symbol name in hindi',
                  'symbol_hdes.min'=>'Please enter minimum 3 character',
                  'symbol_hdes.max'=>'Please enter maximum 255 character',
                   
                ]
            ); 
// $rules = [
// 'symbol_des'        => 'required|unique:m_symbol',
// 'symbol_hdes'       => 'required|min:3|max:255',
// 'symbol_img'        =>'image|mimes:jpeg,png,jpg|max:500',
// ];
// $message =[ 
//                   'symbol_img.image' => 'Please select image file',
//                   'symbol_img.mimes' => 'Please select Jpeg, png, Jpg', 
//                   'symbol_img.max'=>'Symbole Image May not be greater than 500 kilobyte',
                   
//                 ];
// $validator = Validator::make($request->all(), $rules);
// if ($validator->fails()) { 
//         return redirect::back()
//         ->withErrors($validator)
//         ->withInput();
//     }
$file = $request->file('symbol_img');
// Get the contents of the file

// DB::beginTransaction();
// try{
  if(!empty($file)){
    $contents = base64_encode($file->openFile()->fread($file->getSize()));
    $record = array(
    'symbol_des'=>$this->xssClean->clean_input($request->symbol_des),
    'symbol_hdes'=>$this->xssClean->clean_input($request->symbol_hdes),
    'ind_symbol'=>'F',
    'symbol_img'=>$contents,
    'content_type'=>'image/jpg',
    'remarks'=>$this->xssClean->clean_input($request->remarks)
    );
  }
  else{
    $record = array(
    'symbol_des'=>$this->xssClean->clean_input($request->symbol_des),
    'symbol_hdes'=>$this->xssClean->clean_input($request->symbol_hdes),
    'ind_symbol'=>'F',
    'remarks'=>$this->xssClean->clean_input($request->remarks),
    'symbol_img'=>'(NULL)',
    'content_type'=>'(NULL)',
    );
  }
  $v=SymbolModel::add_symbol($record);
// }
// catch(\Exception $e){
// DB::rollback();
// \Session::flash('error_mes', 'Please try Again');
// return Redirect::back();
// }
// DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Symbol Added successfully.");

return redirect($this->base.'list-symbol');
}

public function edit_symbol(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/update-symbol');
$data['heading_title']  ="Edit Symbol";
$data['bradcome']       ='Edit Symbol';
$data['id']=$request->id;
$data['sysno']=decrypt_string($request->id); 
$data['lists']=SymbolModel::get_bysysno(decrypt_string($request->id));
if(isset($data['lists']) and ($data['lists'])) {

$form_data =$this->get_form($request, $data);
$data = array_merge($form_data, $data);
//dd($data);
return view($this->view_path.'.editsysmbol', $data);
}
else{
return redirect($this->base.'list-symbol');
}

}

public function update_symbol(Request $request){
  $this->validate( 
                $request, 
                [
                 'symbol_des'        => 'required|min:3|max:255',
                 'symbol_hdes'       => 'required|min:3|max:255',
                 'symbol_img'        =>'image|mimes:jpeg,png,jpg|max:500',
                ],
                [ 
                  'symbol_des.required' => 'Please enter symbol Name in English',
                  'symbol_des.unique' => 'The symbol has already been taken.', 
                  'symbol_img.image'=>'Please only jpg, jpeg, png format',
                  'symbol_img.max'=>'image size maximum 500kb',
                  'symbol_hdes.required'=>'Please enter symbol name in hindi',
                  'symbol_hdes.min'=>'Please enter minimum 3 character',
                  'symbol_hdes.max'=>'Please enter maximum 255 character',
                   
                ]
            ); 
// $rules = [
// 'symbol_des'        => 'required|min:3|max:255',
// 'symbol_hdes'       => 'required|min:3|max:255',
// 'symbol_img'        =>'image|mimes:jpeg,png,jpg|max:500',
// ];
// $validator = Validator::make($request->all(), $rules);
// if ($validator->fails()) { 
//         return redirect::back()
//         ->withErrors($validator)
//         ->withInput();
//     }
$file = $request->file('symbol_img');
// Get the contents of the file
if($file!='')
$contents = base64_encode($file->openFile()->fread($file->getSize()));

$sysno=$request->sysno; 
DB::beginTransaction();
try{
if($file!=''){
$record = array(
'symbol_des'=>$this->xssClean->clean_input($request->symbol_des),
'symbol_hdes'=>$this->xssClean->clean_input($request->symbol_hdes),
'symbol_img'=>$contents,
'content_type'=>'image/jpg',
'remarks'=>$this->xssClean->clean_input($request->remarks),
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,
);
}
else{
 $record = array(
'symbol_des'=>$this->xssClean->clean_input($request->symbol_des),
'symbol_hdes'=>$this->xssClean->clean_input($request->symbol_hdes),
'remarks'=>$this->xssClean->clean_input($request->remarks),
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,
);
}
 SymbolLogModel::clone_record($sysno);
 SymbolModel::where('SYMBOL_NO',$sysno)->update($record);

}

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Symbol Updated successfully .");

return redirect($this->base.'list-symbol');

}



public function free_symbol(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/symbol-status');
$data['heading_title']  ="List of All Free  Symbol";
$data['bradcome']       ="List of All Free  Symbol";

$symbol_img='';    
$symbol_img=$request->input('symbol_img');
$freesymbol='';    
$freesymbol=$request->input('freesymbol');
$data['freesymbol'] =$freesymbol;
$data['symboltype'] = [
  ['id' => '',  'name' => '-- All --'],
  // ['id' => 'PARTY', 'name' => 'Party Assign'],
  ['id' => 'F', 'name' => 'Alloted Symbol'],
  ['id' => 'T', 'name' => 'Free Symbol'],
];
 
$data['symbol'] = [
['id' => '',  'name' => '-- All --'],
['id' => 'NOT', 'name' => 'Not Uploaded'],
['id' => 'T', 'name' => 'Uploaded'], 
];
$filter='';

$data['symbol_img']=$symbol_img;
$filter = [   
  'symbol_img' =>$symbol_img,
  'freesymbol' =>$freesymbol,  
  ];
$data['lists']=SymbolModel::get_allfreesymbol($filter);
$data['total']=count($data['lists']);
//  dd($data);
return view($this->view_path.'.listfreesymbol', $data);
}

public function change_status(Request $request){   //dd($request->all());
  $rules = [
        'status' => 'required|in:F,T',
   ];
$validator = Validator::make($request->all(), $rules);
if ($validator->fails()) { 
        return redirect::back()
        ->withErrors($validator)
        ->withInput();
    }

$sysno=$request->sysno;
$status=$request->status; 
DB::beginTransaction();
try{    
$record = array(
        'Ind_Symbol'=>$status,
        'added_updated_at' =>date('Y-m-d'),
        'updated_by' =>\Auth::user()->officername,
         );
   SymbolLogModel::clone_record($sysno);
   SymbolModel::where('SYMBOL_NO',$sysno)->update($record);
}

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Symbol Updated successfully .");
return redirect($this->base.'free-symbol');
}
public function verifysymbol(Request $request) { 
$symbol_des=$request->symbol_des;
$sym=SymbolModel::where('SYMBOL_DES',$symbol_des)->first(); 
if(isset($sym) and ($sym)){
$data['symbol_des']   =$sym->SYMBOL_DES;
$data['message']=0;
} 
else{
  $data['message']="This symbol Not Exit!";
} 
 return $data;
}
public function symbollog_details(Request $request){ //dd($request->all());
  $data = [];
  $data['user_data'] = Auth::user();
  $data['action']         = url('mparty/list-symbol');
  $data['heading_title']  ="Update History Logs of Symbols";
  $data['bradcome']       ='Update History Logs';
  $data['id']=$request->id;
  $data['symbol_no']=decrypt_string($request->id);

  $data['lists']=SymbolLogModel::where('SYMBOL_NO',$data['symbol_no'])
                  ->orderBy('log_updated_at','DESC')->get();
    if(isset($data['lists']) and ($data['lists'])){
       $data['lists']=$data['lists']->toArray();
    }
   // dd($data);
   return view($this->view_path.'.symbollog-details', $data);
 

}
}    //