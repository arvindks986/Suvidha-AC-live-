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
use App\models\Common\{StateModel,FileModel,PcModel,AcModel,DistrictModel,ElectionModel};
use App\models\Admin\mparty\{MPartyModel,MpartyLogModel,SymbolModel,MpartysymbollogModel}; 

class MpartyController extends Controller
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
$party_type='';    
$party_type=$request->input('party_type');
if($party_type=="N") $pname=" National "; elseif($party_type=="S") $pname=" State "; 
elseif($party_type=="U") $pname=" Unrecognized "; else $pname=" type ";
$data['action']        = url('mparty/list-party');
$data['saction']        = url('mparty/party-status');
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
$data['total']=count($data['lists']);
 $data['party_type']=$party_type;
// dd($data);
return view($this->view_path.'.listparty', $data);
}

//add_new_party
public function add_new_party(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/add-new-party');
$data['heading_title']  ="Add New Political party";
$data['bradcome']       ='Add New Political party';
$data['mpartytype'] = [
['id' => '',  'name' => 'Select one'],
['id' => 'N', 'name' => 'National'],
['id' => 'S', 'name' => 'State'],
['id' => 'U', 'name' => 'Unrecognized'],
];
$form_data =$this->get_form($request, $data);
$data = array_merge($form_data, $data);
//dd($data);
return view($this->view_path.'.addnewparty', $data);
}

private function get_form($request,$data = array()){
//dd($data);
if($request->old('partyabbre')){
$data['partyabbre']  = $request->old('partyabbre');
}else if(isset($data['lists']) and ($data['lists'])){
$data['partyabbre']  = $data['lists']['PARTYABBRE'];
}else{
$data['partyabbre']  =''; 
}

if($request->old('partyhabbr')){
$data['partyhabbr']  = $request->old('partyhabbr');
}else if(isset($data['lists']) and ($data['lists'])){
$data['partyhabbr']  =  $data['lists']['PARTYHABBR'];
}else{
$data['partyhabbr']  =''; 
}

if($request->old('partyname')){
$data['partyname']  = $request->old('partyname');
}else if(isset($data['lists']) and ($data['lists'])){
$data['partyname']  =  $data['lists']['PARTYNAME'];
}else{
$data['partyname']  =''; 
}

if($request->old('partyhname')){
$data['partyhname']  = $request->old('partyhname');
}else if(isset($data['lists']) and ($data['lists'])){
$data['partyhname']  =  $data['lists']['PARTYHNAME'];
}else{
$data['partyhname']  =''; 
}

if($request->old('partytype')){
$data['partytype']  = $request->old('partytype');
}else if(isset($data['lists']) and ($data['lists'])){
$data['partytype']  =  $data['lists']['PARTYTYPE'];
}else{
$data['partytype']  =''; 
}
if($request->old('remarks')){
$data['remarks']  = $request->old('remarks');
}else if(isset($data['lists']) and ($data['lists'])){
$data['remarks']  =  $data['lists']['remarks'];
}else{
$data['remarks']  =''; 
}
if($request->old('party_reg_date')){
	$data['party_reg_date']  = $request->old('party_reg_date');
}else if(isset($data['lists']) and ($data['lists'])){
	$data['party_reg_date']  =  $data['lists']['party_reg_date'];
}else{
	$data['party_reg_date']  =''; 
}
 if($data['party_reg_date'] !='') $data['party_reg_date'] =date("d-m-yy",strtotime($data['party_reg_date']));
//dd($data);
return $data;   
}
public function verifypartyabbre(Request $request) { 
$partyabbre=$request->partyabbre;
$party=MPartyModel::getpartybyabbre($partyabbre); 
if(isset($party) and ($party)){
$data['partyname']   =$party['PARTYNAME'];
$data['partyabbre']   =$party['PARTYABBRE'];
$data['message']=0;
} 
else{
$data['message']="This party abbre Not Exit!";
} 
return $data;
}
public function getdparty(Request $request) { 
$partyabbre=$request->partyabbre;
$party = DB::table('d_party')->where('PARTYABBRE',$partyabbre)->get();
  
if(isset($party) and ($party)){ $str='';  
	foreach ($party as $key => $v) {
		$st=getstatebystatecode($v->ST_CODE);
		$data['state_code'][]=$st->ST_CODE; 
		$str=$str." ". $st->ST_CODE."-".$st->ST_NAME.",";
	}
		$data['state']=$str;
 		$data['message']=0;
} 
else{
$data['message']="This party has no Recognized state!";
} 
return $data;
}


public function save_new_party(Request $request){
	$this->validate( 
                $request, 
                [
                 	'partytype'        => 'required|in:N,S,U',
					'partyname'        => 'required|min:3|max:255',
					'partyabbre'       => 'required|unique:m_party',
					'partyhname'       => 'required|min:3|max:255',
					'partyhabbr'       => 'required',
                ],
                [ 
                  'partytype.required' => 'Please Select Party Type',
                  'partytype.in' => 'Party Type only National, State, Unrecognized.', 
                  'partyname.required'=>'Please enter party Name in English',
                  'partyname.min'=>'Please enter minimum 3 character',
                  'partyname.max'=>'Please enter maximum 255 character',
                  'partyhname.required'=>'Please enter party Name in Hindi',
                  'partyhname.min'=>'Please enter minimum 3 character',
                  'partyhname.max'=>'Please enter maximum 255 character',
                  'partyabbre.required'=>'Please enter party abbre in English',
                  'partyabbre.unique'=>'The party abbre has already taken',
                  'partyhabbr.required'=>'Please enter party abbre in Hindi',
                ]
            ); 
// $rules = [
// 'partytype'        => 'required|in:N,S,U',
// 'partyname'        => 'required|min:3|max:255',
// 'partyabbre'       => 'required|unique:m_party',
// 'partyhname'       => 'required|min:3|max:255',
// 'partyhabbr'       => 'required',
// ];
// $validator = Validator::make($request->all(), $rules);
// if ($validator->fails()) { 
// return redirect::back()
// ->withErrors($validator)
// ->withInput();
// }

if($request->partytype=="N") $pid=1; elseif($request->partytype=="S") $pid=2;
if($request->partytype=="U") $pid=3; else  $pid=4;
DB::beginTransaction();
try{
$record = array(
'partyabbre'=>$this->xssClean->clean_input($request->partyabbre),
'partyhabbr'=>$this->xssClean->clean_input($request->partyhabbr),
'partyname'=>$this->xssClean->clean_input($request->partyname),
'partyhname'=>$this->xssClean->clean_input($request->partyhname),
'partytype'=>$this->xssClean->clean_input($request->partytype),
'party_reg_date'=>date("Y-m-d",strtotime(($request->party_reg_date))),
'party_typeid'=>$pid,
'remarks'=>$this->xssClean->clean_input($request->remarks)
);
$v=MPartyModel::add_mparty($record);
}

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Political Party Added successfully.");

return redirect($this->base.'list-party');

}

public function edit_party(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/update-party');
$data['heading_title']  ="Edit Political party";
$data['bradcome']       ='Edit Political party';
$data['id']=$request->id;
$data['ccode']=decrypt_string($request->id);
$data['lists']=MPartyModel::get_byccode(decrypt_string($request->id));
if(isset($data['lists']) and ($data['lists'])) {

$data['mpartytype'] = [
['id' => '',  'name' => 'Select one'],
['id' => 'N', 'name' => 'National'],
['id' => 'S', 'name' => 'State'],
['id' => 'U', 'name' => 'Unrecognized'],
];
$form_data =$this->get_form($request, $data);
$data = array_merge($form_data, $data);
//dd($data);
return view($this->view_path.'.editparty', $data);
}
else{
return redirect($this->base.'list-party');
}

}

public function update_party(Request $request){ //dd($request->all());
	$this->validate( 
                $request, 
                [
                 	'partytype'        => 'required|in:N,S,U',
					'partyname'        => 'required|min:3|max:255',
					'partyabbre'       => 'required',
					'partyhname'       => 'required|min:3|max:255',
					'partyhabbr'       => 'required',
                ],
                [ 
                  'partytype.required' => 'Please Select Party Type',
                  'partytype.in' => 'Party Type only National, State, Unrecognized.', 
                  'partyname.required'=>'Please enter party Name in English',
                  'partyname.min'=>'Please enter minimum 3 character',
                  'partyname.max'=>'Please enter maximum 255 character',
                  'partyhname.required'=>'Please enter party Name in Hindi',
                  'partyhname.min'=>'Please enter minimum 3 character',
                  'partyhname.max'=>'Please enter maximum 255 character',
                  'partyabbre.required'=>'Please enter party abbre in English',
                  'partyabbre.unique'=>'The party abbre has already taken',
                  'partyhabbr.required'=>'Please enter party abbre in Hindi',
                ]
            ); 
// $rules = [
// 'partytype'        => 'required|in:N,S,U',
// 'partyname'        => 'required|min:3|max:255',
// 'partyabbre'       => 'required',
// 'partyhname'       => 'required|min:3|max:255',
// 'partyhabbr'       => 'required',
// ];
// $validator = Validator::make($request->all(), $rules);
// if ($validator->fails()) { 
// return redirect::back()
// ->withErrors($validator)
// ->withInput();
// }

if($request->partytype=="N") $pid=1; elseif($request->partytype=="S") $pid=2;
if($request->partytype=="U") $pid=3; else  $pid=4;
$ccode=$request->ccode;
DB::beginTransaction();
try{    
$record = array(

'PARTYABBRE'=>$this->xssClean->clean_input($request->partyabbre),
'PARTYHABBR'=>$this->xssClean->clean_input($request->partyhabbr),
'PARTYNAME'=>$this->xssClean->clean_input($request->partyname),
'PARTYHNAME'=>$this->xssClean->clean_input($request->partyhname),
'PARTYTYPE'=>$this->xssClean->clean_input($request->partytype),
'party_reg_date'=>date("Y-m-d",strtotime(($request->party_reg_date))),
'party_typeid'=>$pid,
'remarks'=>$this->xssClean->clean_input($request->remarks),
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,

);
//dd($record);
//$v=MPartyModel::add_mparty($record);
MpartyLogModel::clone_record($ccode);
MPartyModel::where('CCODE',$ccode)->update($record);

}

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Political Party Updated successfully .");

return redirect($this->base.'list-party');

}

public function party_status(Request $request){   //dd($request->all());
$rules = [
'status'   => 'required|in:N,Y',
'remarks'  => 'required',
];
$validator = Validator::make($request->all(), $rules);
if ($validator->fails()) { 
return redirect::back()
->withErrors($validator)
->withInput();
}
$remarks=$request->remarks; 
$status=$request->status;
$ccode=$request->ccode; 

DB::beginTransaction();
try{
$record = array(
'deleteflag'=>$status,
'remarks'=>$remarks,
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,
);
MpartyLogModel::clone_record($ccode);
MPartyModel::where('CCODE',$ccode)->update($record);
}

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Political Parties Delete Flag change successfully .");
return Redirect::back();
//return redirect($this->base.'list-party');

}

public function change_party_status(Request $request){ // dd($request->all()); 
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/change-status');
$data['heading_title']  ="Change Political Parties Status / Type";
$data['bradcome']       ='Change Political Parties Status / Type';
$data['partytype']='';
$data['mpartytype'] = [
['id' => '',  'name' => 'Select one'],
['id' => 'N', 'name' => 'National'],
['id' => 'S', 'name' => 'State'],
['id' => 'U', 'name' => 'Unrecognized'],
];

$party_type='';    
$party_type=$request->party_type; 
if(empty($party_type)){
$filter = [
'party_type' =>$party_type, 
];

}
else {
$filter = [
'party_type' =>$party_type, 
];
}    
$data['parties']=MPartyModel::get_allpartie( $filter );
// dd($data);
return view($this->view_path.'.change-status', $data);

}
public function getpartybypartytype(Request $request) { 
$partytype=$request->partytype;
if($partytype=="N" || $partytype=="S" || $partytype=="U"){
$party=MPartyModel::getpartybytype($partytype); 
$st='';

if(isset($party) and ($party)){
$st .='<option value="">-- Select One --</option>'; 
foreach ($party as $key => $p) {
$st .='<option value="'.$p['CCODE'].'">'.$p['PARTYNAME'].'</option>'; 
}

return $st;
}
else 
return false;
}
else{
return false;
}
}
public function change_status(Request $request){  //dd($request->all());
$rules = [
'partytype'        => 'required|in:N,S,U',
'newpartytype'     => 'required|in:N,S,U',
'partyname'        => 'required',
'remarks'        => 'required',
];
$validator = Validator::make($request->all(), $rules);
if ($validator->fails()) { 
return redirect::back()->withErrors($validator)->withInput();
}

$partytype=$request->partytype;
$newpartytype=$request->newpartytype; 
$ccode=$request->partyname; 
$remarks=$request->remarks; 
if($request->newpartytype=="N") $pid=1; elseif($request->newpartytype=="S") $pid=2;
if($request->newpartytype=="U") $pid=3; else  $pid=4;

DB::beginTransaction();
 try{    
$record = array(

'PARTYTYPE'=>$newpartytype,
'party_typeid'=>$pid,
'remarks'=>$this->xssClean->clean_input($request->remarks),
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,

);
MpartyLogModel::clone_record($ccode);
MPartyModel::where('CCODE',$ccode)->update($record);

  }

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Political Party type has been changed successfully .");

return redirect($this->base.'list-party');

}

public function state_party_register(Request $request){   
$data = [];
$data['user_data'] = Auth::user();
$party_type='S';    
$data['action']        = url('mparty/add-dparty');
$data['saction']        = url('mparty/dparty-status');
$data['heading_title']="All State Party Recognized State";
$data['bradcome']='All State Party Recognized';


if(empty($party_type)){
$filter = [
'party_type' =>$party_type, 
'st_code' =>'S04', 
];

}
else {
$filter = [
'party_type' =>$party_type, 
'st_code' =>'', 
];
} 
$data['lists']=MPartyModel::get_dparties($filter);
$data['total']=count($data['lists']);
$data['party_type']=$party_type;
//dd($data);
return view($this->view_path.'.statewiseparty', $data);
}

public function edit_dparty(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/update-dparty');
$data['heading_title']  ="Add / Edit State Party";
$data['bradcome']       ='Add / Edit State Party';
$data['party_abbre']=$request->partyabbre;
$data['partyabbre']=decrypt_string($request->partyabbre);
$filter = [
'partyabbre' =>$data['partyabbre'], 
];
$data['lists']=MPartyModel::get_partiesbypartyabbre($filter); 
$data['partyname']='';
$data['record']=array();
// $data['state_name']=''; 
$i=0;
if(isset($data['lists']) and ($data['lists'])) {
foreach ($data['lists'] as $key => $value) {   
$data['partyname']=$value['PARTYNAME'];
$data['record'][$i]['state_name']=$value['ST_CODE']."-".$value['ST_NAME'];
$data['record'][$i]['dpartyid']=$value['id'];
$i++;
}
$data['states']=StateModel::orderBy('m_state.ST_CODE', 'ASC')->get()->toArray();
// dd($data);
return view($this->view_path.'.editdparty', $data);
}
else{
return redirect($this->base.'list-party');
}

}

public function dparty_status(Request $request){  //dd($request->all());
$rules = [
'status'        => 'required|in:0,1',
'remarks'        => 'required',
];

$validator = Validator::make($request->all(), $rules);
if ($validator->fails()) { 
return redirect::back()
->withErrors($validator)
->withInput();
}

$id=$request->id;
$partyabbre=$request->partyabbre; 
$status=$request->status; 
$remarks=$request->remarks; 
DB::beginTransaction();
try{    
$record = array(
'deleted'=>$status,
'remarks'=>$this->xssClean->clean_input($request->remarks),
'added_update_at' =>date('Y-m-d'),
'updated_at' =>date('Y-m-d H:i:s'),
'updated_by' =>\Auth::user()->officername,
);
updatedata('d_party','id',$id,$record);

}

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"State Recognized Party Delisting  successfully .");

return Redirect::back();

}
public function add_dparty(Request $request){ //dd($request->all());
$data = [];
$data['user_data'] = Auth::user();
$data['action']         = url('mparty/insert-dparty');
$data['heading_title']  ="Add State Party Recognized";
$data['bradcome']       ='Add State Party Recognized';

if($request->old('partyabbre')){
$data['partyabbre']  = $request->old('partyabbre');
}else{
$data['partyabbre']  =''; 
}
if($request->old('st_code')){
$data['st_code']  = $request->old('st_code');
}else{
$data['st_code']  =''; 
}
if($request->old('remarks')){
$data['remarks']  = $request->old('remarks');
}else{
$data['remarks']  =''; 
}
$data['parties']=MPartyModel::select('PARTYABBRE','PARTYNAME')
				->where('PARTYTYPE','S')->where('deleteflag','N')->get()->toArray();
$data['states']=StateModel::select('ST_CODE','ST_NAME')
				->orderBy('m_state.ST_CODE', 'ASC')->get()->toArray();
// dd($data);
return view($this->view_path.'.adddparty', $data);
}
public function save_dparty(Request $request){ //dd($request->all());

$rules = [
'partyabbre'    => 'required',
'st_code'       => 'required',
'remarks'       => 'required',
];
$validator = Validator::make($request->all(), $rules);
if ($validator->fails()) { 
      return redirect::back()
      ->withErrors($validator)
      ->withInput();
  }
 $par = DB::table('d_party')->where('PARTYABBRE',$request->partyabbre)
 		->where('ST_CODE',$request->st_code)->first(); 
 if(isset($par) and ($par)){
 		\Session::flash('error_mes', 'This Party already recognized.');
		return redirect::back()
      ->withErrors($validator)
      ->withInput();
 }
$party=MPartyModel::where('PARTYABBRE',$request->partyabbre)->first();

DB::beginTransaction();
try{
$record = array(
'CCODE'=>$party->CCODE,
'PARTYABBRE'=>$this->xssClean->clean_input($request->partyabbre),
'ST_CODE'=>$this->xssClean->clean_input($request->st_code),
'PARTYSYM'=>$party->PARTYSYM,
'remarks'=>$this->xssClean->clean_input($request->remarks),
'added_created_at' =>date('Y-m-d'),
'created_at' =>date('Y-m-d H:i:s'),
'created_by' =>\Auth::user()->officername,
);
$v=insertData('d_party', $record);
}
catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"This Party recognized in State successfully.");

return redirect($this->base.'state-party-recognized');

}

public function party_symbol_assign(Request $request){   
$data = [];
$data['user_data']      = Auth::user();
$data['action']         = url('mparty/symbol-assign');  
$data['eaction']         = url('mparty/editsymbol-assign');
$data['heading_title']  ="Assign Party Symbol --- National and state parties";
$data['bradcome']       ="Assign Party Symbol";
$data['heading']        ="Assign Party Symbol --- National and state parties";
$party_type='';    
$data['party_type']=$request->input('party_type');
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

//$data['listparty']=MPartyModel::getpartiesnotassignsymbol();
  $data['listparty']=MPartyModel::partiesnotassignsymbol();

$data['listsuassignsymbol']=SymbolModel::get_allfreesymbol($filter);
$data['results']=MPartyModel::partyassignsymbol($filter);
$data['total']=count($data['results']);
// dd($data);
return view($this->view_path.'.party-symbol-assign', $data);
}
public function symbol_assign(Request $request){ //dd($request->all()); 
$rules = [
'party'    => 'required',
'symbol'       => 'required',
];
$validator = Validator::make($request->all(), $rules);
if($validator->fails()) { 
return redirect::back()->withErrors($validator)->withInput();
}
$partyrec=MPartyModel::get_byccode($request->party);
$sym=SymbolModel::get_bysysno($request->symbol);
//dd($sym);
	$recordlog = array(
		'ccode' => $partyrec['CCODE'], 
        'partyabbre' => $partyrec['PARTYABBRE'], 
       	'symbolno' => $partyrec['PARTYSYM'],
      	'symbol' => $sym['SYMBOL_DES'],
		);
DB::beginTransaction();
try{
	MpartysymbollogModel::add_data($recordlog);
$record = array(
'PARTYSYM'=>$this->xssClean->clean_input($request->symbol),
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,
);
MPartyModel::where('CCODE',$this->xssClean->clean_input($request->party))
    ->update($record);
}
catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Party Symbol assigned successfully.");
return Redirect::back();

//return redirect($this->base.'list-party');

}
public function editsymbol_assign(Request $request){ //dd($request->all()); 
$rules = [
'eparty'    => 'required',
'esymbol'   => 'required',
];
$validator = Validator::make($request->all(), $rules);
if($validator->fails()) { 
return redirect::back()->withErrors($validator)->withInput();
} 
 $eccode=$this->xssClean->clean_input($request->eccode);
 $partyrec=MPartyModel::get_byccode($eccode);
 $sym=SymbolModel::get_bysysno($partyrec['PARTYSYM']);
 //dd($sym);
 if(isset($sym) and ($sym)){
	$recordlog = array(
		'ccode' 		=> $partyrec['CCODE'], 
        'partyabbre' 	=> $partyrec['PARTYABBRE'], 
       	'symbolno' 		=> $partyrec['PARTYSYM'],
      	'symbol' 		=> $sym['SYMBOL_DES'],
		);
   }else{
   	$recordlog = array(
		'ccode' 		=> $partyrec['CCODE'], 
        'partyabbre' 	=> $partyrec['PARTYABBRE'], 
       	'symbolno' 		=> $partyrec['PARTYSYM'],
      	'symbol' 		=>'(NULL)', 
		);
   }
DB::beginTransaction();
try{
	 MpartysymbollogModel::add_data($recordlog);

$record = array(
'PARTYSYM'=>$this->xssClean->clean_input($request->esymbol),
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,
);
MPartyModel::where('CCODE',$eccode)->update($record);
}
catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Party Symbol assigned successfully.");
return Redirect::back();

//return redirect($this->base.'list-party');

}  
//delisting_party
public function delisting_party(Request $request){ //dd($request->all());
	$data = [];
	$data['user_data'] = Auth::user();
	$party_type='';    
	$party_type=$request->input('party_type');
	if($party_type=="N") $pname=" National "; elseif($party_type=="S") $pname=" State "; 
	elseif($party_type=="U") $pname=" Unrecognized "; else $pname=" type ";
	$data['action']        = url('mparty/delistparty');
	$data['saction']        = url('mparty/party-status');
	$data['heading_title']="List of delisted parties";
	$data['bradcome']='List of delisted parties';
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
	$filter1 = [
	'party_type' =>'', 
	'st_code' =>'', 
	]; 
	$data['lists']=MPartyModel::delisting_party($filter);
	$data['parties']=MPartyModel::get_allpartie($filter1);
	$data['total']=count($data['lists']);
	$data['party_type']=$party_type;
	 //dd($data);
	return view($this->view_path.'.delistingparty', $data);
	}
public function view_details(Request $request){ //dd($request->all());
	$data = [];
	$data['user_data'] = Auth::user();
	$data['action']         = url('mparty/list-party');
	$data['heading_title']  ="Update History Logs of Political Parties";
	$data['bradcome']       ='Update History Logs';
	$data['id']=$request->id;
	$data['ccode']=decrypt_string($request->id);
	$data['singlelist']=MPartyModel::get_byccode(decrypt_string($request->id));
	$data['lists']=MpartyLogModel::where('CCODE',$data['ccode'])->orderBy('log_updated_at','DESC')->get();
    if(isset($data['lists']) and ($data['lists'])){
    	 $data['lists']=$data['lists']->toArray();
    }
   // dd($data);
   return view($this->view_path.'.view-details', $data);
 

}
 
public function delistparty(Request $request){   //dd($request->all());
$rules = [
'party'   => 'required',
'remarks'  => 'required',
];
$validator = Validator::make($request->all(), $rules);
if ($validator->fails()) { 
return redirect::back()->withErrors($validator)->withInput();
}
$remarks=$request->remarks; 
$ccode=$request->party; 

DB::beginTransaction();
try{
$record = array(
'deleteflag'=>'Y',
'remarks'=>$remarks,
'added_updated_at' =>date('Y-m-d'),
'updated_by' =>\Auth::user()->officername,
);
MpartyLogModel::clone_record($ccode);
MPartyModel::where('CCODE',$ccode)->update($record);
}

catch(\Exception $e){
DB::rollback();

\Session::flash('error_mes', 'Please try Again');
return Redirect::back();
}
DB::commit();  

Session::flash('status',1);
Session::flash('success_mes',"Political Parties Delisting successfully .");
return Redirect::back();
 
}

public function view_delisted_details(Request $request){ //dd($request->all());
	$data = [];
	$data['user_data'] = Auth::user();
	$data['action']         = url('mparty/delisting-party');
	$data['heading_title']  ="Update History Logs of Political Parties";
	$data['bradcome']       ='Update History Logs';
	$data['id']=$request->id;
	$data['ccode']=decrypt_string($request->id);

	$data['lists']=MpartyLogModel::where('CCODE',$data['ccode'])->orderBy('log_updated_at','DESC')->get();
    if(isset($data['lists']) and ($data['lists'])){
    	 $data['lists']=$data['lists']->toArray();
    }
   // dd($data);
   return view($this->view_path.'.view-delisted', $data);
 

}

}    //  end class