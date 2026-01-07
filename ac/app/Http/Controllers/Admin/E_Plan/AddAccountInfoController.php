<?php

namespace App\Http\Controllers\Admin\E_Plan;

use App\commonModel;
use App\Http\Controllers\Controller;
use App\models\Admin\E_Plan\{AddAccountModel,AddAccountEpicModel,AddAccountLogModel};

use App\Helpers\SmsgatewayHelper;
use Auth;
use DB;
use Illuminate\Http\Request;
use Validator;
use Mail;


class AddAccountInfoController extends Controller
{
    protected $view_path_status = 'admin.e_plan.';

    public function __construct()
    {
        $this->commonModel = new commonModel();
    }

    public function account_form(Request $request){
        
        $user_data['user_data'] = Auth::user();
        $st_code = Auth::user()->st_code;
        $user_data['menu_list'] = DB::table('eplan_account_info_nom')
            ->select('*')
            ->get();
        $user_data['account_data_epic'] = AddAccountEpicModel::where('st_code',$st_code)->where('account_payment_for', 2)->get();
        $user_data['account_data_nom'] = AddAccountModel::where('st_code',$st_code)->where('account_payment_for', 1)->get();

        $user_data['dist_no'] = DB::table('m_district')->select('*')->where('st_code',$st_code)->get()->toarray();

        $account_data_epic=  AddAccountEpicModel::select('*')->where('st_code',$st_code)->get()->toArray();
        $account_data_nom =  AddAccountModel::select('*')->where('st_code',$st_code)->get()->toArray();

        $user_data['account_data_merge'] = array_merge($account_data_epic,$account_data_nom);
        
        return view($this->view_path_status . 'account_form', $user_data);
        
    }


    public function addaccountinfo(Request $request)
    {  
        
        $input = $request->all();
        
      if($input['account_for'] == 1){
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|max:255',
            'acc_mobile' => 'required|numeric',
            'acc_email' => 'required|email',
            'acc_number' => 'required|max:255',
            'acc_ifsc' => 'required|alpha_num',
            'acc_beni' => 'required|max:255',
            'dist_select'=> 'required',
                ], [
            'account_name.required' => 'Please input account name',
            'acc_mobile.required'      => 'Please enter mobile number',
            'acc_email.required'      => 'Please enter email id',
            'acc_number.required'      => 'Please enter account number',
            'acc_ifsc.required'      => 'Please enter ifsc number',
            'acc_beni.required'      => 'Please enter beneficiary name',
            'dist_select.required'   => 'Please select district number'
            
        ]);
      }elseif($input['account_for'] == 2){

        $validator = Validator::make($request->all(), [
            'account_name' => 'required|max:255',
            'acc_mobile' => 'required|numeric',
            'acc_email' => 'required|email',
            'acc_number' => 'required|max:255',
            'acc_ifsc' => 'required|alpha_num',
            'acc_beni' => 'required|max:255',
            'dist_select'=> 'required',
            'charges_epic' => 'required',
                ], [
            'account_name.required' => 'Please input account name',
            'acc_mobile.required'      => 'Please enter mobile number',
            'acc_email.required'      => 'Please enter email id',
            'acc_number.required'      => 'Please enter account number',
            'acc_ifsc.required'      => 'Please enter ifsc number',
            'acc_beni.required'      => 'Please enter beneficiary name',
            'dist_select.required'   => 'Please select district number',
            'charges_epic.required'  => 'Please enter charges amount'
            
        ]);

      }
       
        

        if ($validator->fails()) {
            return response()->json(['status' => 'validation', 'response' => $validator->errors()->all()]);
        }

        

        $st_code = Auth::user()->st_code;
        
		$data = array();
		$data['account_payment_for'] = empty($input['account_for']) ? 0 : $input['account_for'];
		$data['account_name'] = empty($input['account_name']) ? 0 : $input['account_name'];
		$data['account_mobile'] = empty($input['acc_mobile']) ? 0 : $input['acc_mobile'];
		$data['account_email'] = empty($input['acc_email']) ? 0 : $input['acc_email'];
		$data['account_number'] = empty($input['acc_number']) ? 0 : $input['acc_number'];
		$data['account_type'] = empty($input['acc_type']) ? 0 : $input['acc_type'];
        $data['account_ifsc'] = empty($input['acc_ifsc']) ? 0 : $input['acc_ifsc'];
        $data['account_benificeary'] = empty($input['acc_beni']) ? 0 : $input['acc_beni'];
        $data['st_code'] = $st_code;
        $data['dist_no'] = empty($input['dist_select']) ? 0 : $input['dist_select'];
        $data['is_finalised'] = 0;
        $data['is_verified'] = 0;
        $duplicate_account_nom = AddAccountModel::where('st_code',$st_code)->where('account_payment_for', '=', 1)->where('dist_no',$input['dist_select'])->get();
        $duplicate_account_epic = AddAccountEpicModel::where('st_code',$st_code)->where('account_payment_for', '=', 2)->where('dist_no',$input['dist_select'])->get();
        
        if($input['account_for'] ==1){
            if(count($duplicate_account_nom) > 0){
                return response()->json(['status' => 'error', 'response' => 'Account already added please update']);
            }
        }
        
        if($input['account_for'] ==2){
            
            if(count($duplicate_account_epic) > 0 ){
                return response()->json(['status' => 'error', 'response' => 'Account already added please update']);
            }
        }
        $duplicate_account_epic = AddAccountEpicModel::where('account_number',$input['acc_number'])->get();
        $duplicate_account_nom = AddAccountModel::where('account_number',$input['acc_number'])->get();
       
        if(count($duplicate_account_nom) == 0 && count($duplicate_account_epic) == 0 ){
            if($input['account_for'] == 1){
                $last_id = AddAccountModel::create($data);
            }elseif($input['account_for'] == 2){
                $data['amount_for_duplicate_epic'] = $input['charges_epic'];
                $last_id = AddAccountEpicModel::create($data);
            }
           
            if($last_id->id){
                return response()->json(['status' => 'success', 'response' => 'Data saved.']);
            }else{
                return response()->json(['status' => 'error', 'response' => 'Data not saved.']);
            }
        }else{
            return response()->json(['status' => 'error', 'response' => 'Duplicate Account number found']);
        }
		

   }


   public function editaccountinfoepic(Request $request){
        $st_code = Auth::user()->st_code;
        $account_data_epic = AddAccountEpicModel::where('st_code',$st_code)->where('account_payment_for', 2)->first();

        if($account_data_epic){
            return \Response::json(['account_data_epic'=>$account_data_epic,'success' => true]);
        }else{
            return \Response::json([
                'success' => false,
                'success_mes' => 'No Record found',
            ]);
        }
        
   }


   public function update_nom_account(Request $request){
    


            $validator = Validator::make($request->all(), [
                'linked_acc_name_nom' => 'required|max:255',
                'mobile_account_nom' => 'required|numeric',
                'email_account_nom' => 'required|email',
                'account_number_nom' => 'required|max:255',
                'account_ifsc_nom' => 'required|alpha_num',
                'account_beni_nom' => 'required|max:255',
                    ], [
                'linked_acc_name_nom.required' => 'Please input account name',
                'mobile_account_nom.required'      => 'Please enter mobile number',
                'email_account_nom.required'      => 'Please enter email id',
                'account_number_nom.required'      => 'Please enter account number',
                'account_ifsc_nom.required'      => 'Please enter ifsc number',
                'account_beni_nom.required'      => 'Please enter beneficiary name'
                
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'validation', 'response' => $validator->errors()->all()]);
            }
        $last_id = 0;
        $st_code = Auth::user()->st_code;
        $input = $request->all();
		$data = array();
		$data['account_payment_for'] = empty($input['account_for_nom']) ? 0 : $input['account_for_nom'];
		$data['account_name'] = empty($input['linked_acc_name_nom']) ? 0 : $input['linked_acc_name_nom'];
		$data['account_mobile'] = empty($input['mobile_account_nom']) ? 0 : $input['mobile_account_nom'];
		$data['account_email'] = empty($input['email_account_nom']) ? 0 : $input['email_account_nom'];
		$data['account_number'] = empty($input['account_number_nom']) ? 0 : $input['account_number_nom'];
		$data['account_type'] = empty($input['account_type_nom']) ? 0 : $input['account_type_nom'];
        $data['account_ifsc'] = empty($input['account_ifsc_nom']) ? 0 : $input['account_ifsc_nom'];
        $data['account_benificeary'] = empty($input['account_beni_nom']) ? 0 : $input['account_beni_nom'];
        $data['st_code'] = $st_code;
        $data['is_finalised'] = 0;
        $data['is_verified'] = 0;

        // validation update
        if($input['account_number_nom'] == $input['account_number_nom_change']){
            if($input['account_for_nom'] == 1){
                
                $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no','=',0)->update($data);
                $data['updated_by'] = Auth::user()->officername;
                $data['dist_no'] = 0;
                $log_table = AddAccountLogModel::insert($data);
            }elseif($input['account_for_epic'] == 2){
                $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
            }
        }else{
              
               $duplicate_account_epic = AddAccountEpicModel::where('account_number',$input['account_number_nom'])->get();
                $duplicate_account_nom = AddAccountModel::where('account_number',$input['account_number_nom'])->get();
               
                if(count($duplicate_account_nom) == 0 && count($duplicate_account_epic) == 0 ){
                    if($input['account_for_nom'] == 1){
                        
                        //$last_id = AddAccountModel::create($data);
                        $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no', '=',0)->update($data);
                        $data['updated_by'] = Auth::user()->officername;
                        $data['dist_no'] = 0;
                        $log_table = AddAccountLogModel::insert($data);
                    }elseif($input['account_for_epic'] == 2){
                        $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', '=', 0)->update($data);
                    }
                   
                }
            }


        //validation update

        

        if($last_id){
            return redirect('acceo/ep/add_payment_info')->with('success', ' Account Information Updated Successfully');
        }else{
            return redirect('acceo/ep/add_payment_info')->with('error','Account Number alrady exists please check');
        }

   }

   //for deo

   public function update_nom_account_deo(Request $request){
    
    

    $validator = Validator::make($request->all(), [
        'linked_acc_name_nom' => 'required|max:255',
        'mobile_account_nom' => 'required|numeric',
        'email_account_nom' => 'required|email',
        'account_number_nom' => 'required|max:255',
        'account_ifsc_nom' => 'required|alpha_num',
        'account_beni_nom' => 'required|max:255',
        'update_bk_name_nom_deo' => 'required|regex:/^[a-zA-Z\s]+$/u'
            ], [
        'linked_acc_name_nom.required' => 'Please input account name',
        'mobile_account_nom.required'      => 'Please enter mobile number',
        'email_account_nom.required'      => 'Please enter email id',
        'account_number_nom.required'      => 'Please enter account number',
        'account_ifsc_nom.required'      => 'Please enter ifsc number',
        'account_beni_nom.required'      => 'Please enter beneficiary name',
        'update_bk_name_nom_deo.required' => 'Please enter bank name'
        
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'validation', 'response' => $validator->errors()->all()]);
    }
$last_id = 0;
$st_code = Auth::user()->st_code;
$dist_no = Auth::user()->dist_no;
$input = $request->all();
$data = array();
$data['bank_name'] = empty($input['update_bk_name_nom_deo']) ? 0 : $input['update_bk_name_nom_deo'];
$data['account_payment_for'] = empty($input['account_for_nom']) ? 0 : $input['account_for_nom'];
$data['account_name'] = empty($input['linked_acc_name_nom']) ? 0 : $input['linked_acc_name_nom'];
$data['account_mobile'] = empty($input['mobile_account_nom']) ? 0 : $input['mobile_account_nom'];
$data['account_email'] = empty($input['email_account_nom']) ? 0 : $input['email_account_nom'];
$data['account_number'] = empty($input['account_number_nom']) ? 0 : $input['account_number_nom'];
$data['account_type'] = empty($input['account_type_nom']) ? 0 : $input['account_type_nom'];
$data['account_ifsc'] = empty($input['account_ifsc_nom']) ? 0 : $input['account_ifsc_nom'];
$data['account_benificeary'] = empty($input['account_beni_nom']) ? 0 : $input['account_beni_nom'];
$data['st_code'] = $st_code;
$data['is_finalised'] = 0;
$data['is_verified'] = 0;

if($input['account_number_nom'] == $input['account_number_nom_change']){
    if($input['account_for_nom'] == 1){
        
        $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
        $data['updated_by'] = Auth::user()->officername;
        $data['dist_no'] = $dist_no;
        $log_table = AddAccountLogModel::insert($data);
    }elseif($input['account_for_epic'] == 2){
        $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
    }
}else{
      
       $duplicate_account_epic = AddAccountEpicModel::where('account_number',$input['account_number_nom'])->get();
        $duplicate_account_nom = AddAccountModel::where('account_number',$input['account_number_nom'])->get();
       
        if(count($duplicate_account_nom) == 0 && count($duplicate_account_epic) == 0 ){
            if($input['account_for_nom'] == 1){
                
                //$last_id = AddAccountModel::create($data);
                $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
                $data['updated_by'] = Auth::user()->officername;
                $data['dist_no'] = $dist_no;
                $log_table = AddAccountLogModel::insert($data);
            }elseif($input['account_for_epic'] == 2){
                $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
            }
           
        }
    }

        if($last_id){
            return redirect('acdeo/add_account_info')->with('success', ' Account Information Updated Successfully');
        }else{
            return redirect('acdeo/add_account_info')->with('error','Account Already exists please check');
        }

}

public function update_epic_account_deo(Request $request){
    
 
    $validator = Validator::make($request->all(), [
        'linked_acc_name_epic' => 'required|max:255',
        'mobile_account_epic' => 'required|numeric',
        'email_account_epic' => 'required|email',
        'account_number_epic' => 'required|max:255',
        'account_ifsc_epic' => 'required|alpha_num',
        'account_beni_epic' => 'required|max:255',
        'charges_epic_update' => 'required',
        'deo_bank_name_update_epic' => 'required|regex:/^[a-zA-Z\s]+$/u'
    
            ], [
        'linked_acc_name_epic.required' => 'Please input account name',
        'mobile_account_epic.required'      => 'Please enter mobile number',
        'email_account_epic.required'      => 'Please enter email id',
        'account_number_epic.required'      => 'Please enter account number',
        'account_ifsc_epic.required'      => 'Please enter ifsc number',
        'account_beni_epic.required'      => 'Please enter beneficiary name',
        'charges_epic_update.required'   =>  'Please enter charge for EPIC',
        'deo_bank_name_update_epic.required' => 'Please enter Bank Name'
        
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'validation', 'response' => $validator->errors()->all()]);
    }
$last_id = 0;
$st_code = Auth::user()->st_code;
$dist_no = Auth::user()->dist_no;
$input = $request->all();
$data = array();
$data['bank_name'] = empty($input['deo_bank_name_update_epic']) ? 0 : $input['deo_bank_name_update_epic'];
$data['account_payment_for'] = empty($input['account_for_epic']) ? 0 : $input['account_for_epic'];
$data['account_name'] = empty($input['linked_acc_name_epic']) ? 0 : $input['linked_acc_name_epic'];
$data['account_mobile'] = empty($input['mobile_account_epic']) ? 0 : $input['mobile_account_epic'];
$data['account_email'] = empty($input['email_account_epic']) ? 0 : $input['email_account_epic'];
$data['account_number'] = empty($input['account_number_epic']) ? 0 : $input['account_number_epic'];
$data['account_type'] = empty($input['account_type_epic']) ? 0 : $input['account_type_epic'];
$data['account_ifsc'] = empty($input['account_ifsc_epic']) ? 0 : $input['account_ifsc_epic'];
$data['account_benificeary'] = empty($input['account_beni_epic']) ? 0 : $input['account_beni_epic'];
$data['amount_for_duplicate_epic'] = empty($input['charges_epic_update']) ? 0 : $input['charges_epic_update'];
$data['st_code'] = $st_code;
$data['is_finalised'] = 0;
$data['is_verified'] = 0;

if($input['account_number_epic'] == $input['account_number_epic_change']){
    if($input['account_for_epic'] == 1){
        
        $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
        $data['updated_by'] = Auth::user()->officername;
        $data['dist_no'] = $dist_no;
        $log_table = AddAccountLogModel::insert($data);
    }elseif($input['account_for_epic'] == 2){
        $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
    }
}else{

$duplicate_account_epic = AddAccountEpicModel::where('account_number',$input['account_number_epic'])->get();
$duplicate_account_nom = AddAccountModel::where('account_number',$input['account_number_epic'])->get();

if(count($duplicate_account_nom) == 0 && count($duplicate_account_epic) == 0 ){
    if($input['account_for_epic'] == 1){
        
        //$last_id = AddAccountModel::create($data);
        $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
        $data['updated_by'] = Auth::user()->officername;
        $data['dist_no'] = $dist_no;
        $log_table = AddAccountLogModel::insert($data);
    }elseif($input['account_for_epic'] == 2){
        $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
    }
   
}

}

if($last_id){
    return redirect('acdeo/add_account_info')->with('success', ' Account Information Updated Successfully');
}else{
    return redirect('acdeo/add_account_info')->with('error','Account Already exists please check');
}



}

//deo ends


   public function update_epic_account(Request $request){
    
        $validator = Validator::make($request->all(), [
            'linked_acc_name_epic' => 'required|max:255',
            'mobile_account_epic' => 'required|numeric',
            'email_account_epic' => 'required|email',
            'account_number_epic' => 'required|max:255',
            'account_ifsc_epic' => 'required|alpha_num',
            'account_beni_epic' => 'required|max:255',
                ], [
            'linked_acc_name_epic.required' => 'Please input account name',
            'mobile_account_epic.required'      => 'Please enter mobile number',
            'email_account_epic.required'      => 'Please enter email id',
            'account_number_epic.required'      => 'Please enter account number',
            'account_ifsc_epic.required'      => 'Please enter ifsc number',
            'account_beni_epic.required'      => 'Please enter beneficiary name'
            
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'validation', 'response' => $validator->errors()->all()]);
        }
    $last_id = 0;
    $st_code = Auth::user()->st_code;
    $input = $request->all();
    $data = array();
    $data['account_payment_for'] = empty($input['account_for_epic']) ? 0 : $input['account_for_epic'];
    $data['account_name'] = empty($input['linked_acc_name_epic']) ? 0 : $input['linked_acc_name_epic'];
    $data['account_mobile'] = empty($input['mobile_account_epic']) ? 0 : $input['mobile_account_epic'];
    $data['account_email'] = empty($input['email_account_epic']) ? 0 : $input['email_account_epic'];
    $data['account_number'] = empty($input['account_number_epic']) ? 0 : $input['account_number_epic'];
    $data['account_type'] = empty($input['account_type_epic']) ? 0 : $input['account_type_epic'];
    $data['account_ifsc'] = empty($input['account_ifsc_epic']) ? 0 : $input['account_ifsc_epic'];
    $data['account_benificeary'] = empty($input['account_beni_epic']) ? 0 : $input['account_beni_epic'];
    $data['st_code'] = $st_code;
    $data['is_finalised'] = 0;
    $data['is_verified'] = 0;

    if($input['account_number_epic'] == $input['account_number_epic_change']){
        if($input['account_for_epic'] == 1){
            
            $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no', '=', 0)->update($data);
            $data['updated_by'] = Auth::user()->officername;
            $data['dist_no'] = 0;
            $log_table = AddAccountLogModel::insert($data);
        }elseif($input['account_for_epic'] == 2){
            $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', '=', 0)->update($data);
        }
    }else{
    
    $duplicate_account_epic = AddAccountEpicModel::where('account_number',$input['account_number_epic'])->get();
    $duplicate_account_nom = AddAccountModel::where('account_number',$input['account_number_epic'])->get();
    
    if(count($duplicate_account_nom) == 0 && count($duplicate_account_epic) == 0 ){
        if($input['account_for_epic'] == 1){
            
            //$last_id = AddAccountModel::create($data);
            $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no', '=', 0)->update($data);
            $data['updated_by'] = Auth::user()->officername;
            $data['dist_no'] = 0;
            $log_table = AddAccountLogModel::insert($data);
        }elseif($input['account_for_epic'] == 2){
            $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', '=', 0)->update($data);
        }
       
    }
    
    }

    


    if($last_id){
        return redirect('acceo/ep/add_payment_info')->with('success', ' Account Information Updated Successfully');
    }else{
        return redirect('acceo/ep/add_payment_info')->with('error','Account Already exists please check');
    }
   }

    public function view_added_account(Request $request){
        //dd($request->all());
        $user_data['selected_dist_no'] = '';
        $user_data['user_data'] = Auth::user();
        $st_code = Auth::user()->st_code;
        $user_data['districtlist'] = DB::table('m_district')->where('ST_CODE', $st_code)->get();
        if(isset($request->dist_no) && !empty($request->dist_no)){
        $user_data['selected_dist_no'] = $request->dist_no;
        $account_data_epic=  AddAccountEpicModel::select('*')->where('st_code',$st_code)->where('dist_no',$request->dist_no)->get()->toArray();
        $account_data_nom =  AddAccountModel::select('*')->where('st_code',$st_code)->where('dist_no',$request->dist_no)->get()->toArray();
        }else{
        
        $account_data_epic=  AddAccountEpicModel::select('*')->where('st_code',$st_code)->get()->toArray();
        $account_data_nom =  AddAccountModel::select('*')->where('st_code',$st_code)->get()->toArray();
        }
        
        $user_data['account_data_merge'] = array_merge($account_data_epic,$account_data_nom);
        return view($this->view_path_status . 'view_added_account', $user_data);
    }


    public function deoaccountform(Request $request){

        $user_data['user_data'] = Auth::user();
        $st_code = Auth::user()->st_code;
        $dist_no = Auth::user()->dist_no;
        $user_data['menu_list'] = DB::table('eplan_account_info_nom')
            ->select('*')
            ->get();
        $user_data['account_data_epic'] = AddAccountEpicModel::where('st_code',$st_code)->where('account_payment_for', 2)->where('dist_no', $dist_no)->get();
        $user_data['account_data_nom'] = AddAccountModel::where('st_code',$st_code)->where('account_payment_for', 1)->where('dist_no', $dist_no)->get();
        
        return view($this->view_path_status . 'account_form_deo', $user_data);
    }

  

    public function addaccountinfodeo(Request $request){
       $input = $request->all();
       
       if($input['account_for'] == 2){

        $validator = Validator::make($request->all(), [
            'account_name' => 'required|max:255',
            'acc_mobile' => 'required|numeric',
            'acc_email' => 'required|email',
            'acc_number' => 'required|max:255',
            'acc_ifsc' => 'required|alpha_num',
            'acc_beni' => 'required|max:255',
            'acc_charges_epic'=>'required',
            'bank_namee' => 'required|regex:/^[a-zA-Z\s]+$/u'
                ], [
            'account_name.required' => 'Please input account name',
            'acc_mobile.required'      => 'Please enter mobile number',
            'acc_email.required'      => 'Please enter email id',
            'acc_number.required'      => 'Please enter account number',
            'acc_ifsc.required'      => 'Please enter ifsc number',
            'acc_beni.required'      => 'Please enter beneficiary name',
            'acc_charges_epic.required' => 'Please input charges for EPIC',
            'bank_namee.required' => 'Please input Bank Name',
            'bank_namee.regex' => 'Please enter a valid Bank Name'
            
        ]);

       }else{
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|max:255',
            'acc_mobile' => 'required|numeric',
            'acc_email' => 'required|email',
            'acc_number' => 'required|max:255',
            'acc_ifsc' => 'required|alpha_num',
            'acc_beni' => 'required|max:255',
            'bank_namee' => 'required|regex:/^[a-zA-Z\s]+$/u',
                ], [
            'account_name.required' => 'Please input account name',
            'acc_mobile.required'      => 'Please enter mobile number',
            'acc_email.required'      => 'Please enter email id',
            'acc_number.required'      => 'Please enter account number',
            'acc_ifsc.required'      => 'Please enter ifsc number',
            'acc_beni.required'      => 'Please enter beneficiary name',
            'bank_namee.required' => 'Please input Bank Name',
            
            
        ]);
       }
        

        if ($validator->fails()) {
            return response()->json(['status' => 'validation', 'response' => $validator->errors()->all()]);
        }

        $st_code = Auth::user()->st_code;
        $dist_no = Auth::user()->dist_no;
        
        $data = array();
        $data['bank_name'] = empty($input['bank_namee']) ? 0 : $input['bank_namee'];
		$data['account_payment_for'] = empty($input['account_for']) ? 0 : $input['account_for'];
		$data['account_name'] = empty($input['account_name']) ? 0 : $input['account_name'];
		$data['account_mobile'] = empty($input['acc_mobile']) ? 0 : $input['acc_mobile'];
		$data['account_email'] = empty($input['acc_email']) ? 0 : $input['acc_email'];
		$data['account_number'] = empty($input['acc_number']) ? 0 : $input['acc_number'];
		$data['account_type'] = empty($input['acc_type']) ? 0 : $input['acc_type'];
        $data['account_ifsc'] = empty($input['acc_ifsc']) ? 0 : $input['acc_ifsc'];
        $data['account_benificeary'] = empty($input['acc_beni']) ? 0 : $input['acc_beni'];
        $data['st_code'] = $st_code;
        $data['dist_no'] = $dist_no;
        $data['is_finalised'] = 0;
        $data['is_verified'] = 0;

        // check duplicate
        $duplicate_account_nom = AddAccountModel::where('st_code',$st_code)->where('account_payment_for', '=', 1)->where('dist_no',$dist_no)->get();
        $duplicate_account_epic = AddAccountEpicModel::where('st_code',$st_code)->where('account_payment_for', '=', 2)->where('dist_no',$dist_no)->get();
        // check duplicate ends
        if($input['account_for'] ==1){
            if(count($duplicate_account_nom) > 0){
                return response()->json(['status' => 'error', 'response' => 'Account already added please update']);
            }
        }
        
        if($input['account_for'] ==2){
            if(count($duplicate_account_epic) > 0 ){
                return response()->json(['status' => 'error', 'response' => 'Account already added please update12']);
            }
        }
        $duplicate_account_epic = AddAccountEpicModel::where('account_number',$input['acc_number'])->get();
        $duplicate_account_nom = AddAccountModel::where('account_number',$input['acc_number'])->get();
       
        if(count($duplicate_account_nom) == 0 && count($duplicate_account_epic) == 0 ){
            if($input['account_for'] == 1){
                $last_id = AddAccountModel::create($data);
            }elseif($input['account_for'] == 2){
                $data['amount_for_duplicate_epic'] = $input['acc_charges_epic'];
                $last_id = AddAccountEpicModel::create($data);
            }
           
            if($last_id->id){
                return response()->json(['status' => 'success', 'response' => 'Data saved.']);
            }else{
                return response()->json(['status' => 'error', 'response' => 'Data not saved.']);
            }
        }else{
            return response()->json(['status' => 'error', 'response' => 'Duplicate Account number found']);
        }

    }


    public function eciaccountinfo(Request $request){
        //dd($request->all());
        if(isset($request->acc_for)){
            $account_for = $request->acc_for;
            $user_data['selected_acc_type'] = $request->acc_for;
         }else{
            $user_data['selected_acc_type'] = '';
         }


        $user_data['user_data'] = Auth::user();
        $user_data['st_code_array'] = DB::table('m_state')->orderBy('st_code')->get(); 
        
        if(isset($request->st_codeee) && isset($request->dist_no)){
            
            $account_data_epic=  AddAccountEpicModel::select('*')->where('st_code',$request->st_codeee)->where('dist_no',$request->dist_no);

            if(isset($request->acc_for)){
                $account_data_epic->where('account_payment_for', $account_for);
            }
            
            $account_data_epic = $account_data_epic->get()->toArray();

            $account_data_nom =  AddAccountModel::select('*')->where('st_code',$request->st_codeee)->where('dist_no',$request->dist_no);
            
            if(isset($request->acc_for)){
                $account_data_nom->where('account_payment_for', $account_for);
            }
            
            $account_data_nom = $account_data_nom->get()->toArray();

            $user_data['districtlist'] = DB::table('m_district')->where('ST_CODE', $request->st_codeee)->get();

            $user_data['selected_state'] = $request->st_codeee;
            $user_data['selected_district'] = $request->dist_no;

        }elseif(isset($request->st_codeee)){
            
            $account_data_epic=  AddAccountEpicModel::select('*')->where('st_code',$request->st_codeee);

            if(isset($request->acc_for)){
                $account_data_epic->where('account_payment_for', $account_for);
            }
            
            $account_data_epic = $account_data_epic->get()->toArray();
            
            

            $account_data_nom =  AddAccountModel::select('*')->where('st_code',$request->st_codeee);
            if(isset($request->acc_for)){
                $account_data_nom->where('account_payment_for', $account_for);
            }
            
            $account_data_nom = $account_data_nom->get()->toArray();
            

            $user_data['districtlist'] = DB::table('m_district')->where('ST_CODE', $request->st_codeee)->get();
            $user_data['selected_state'] = $request->st_codeee;
            $user_data['selected_district'] = '';
        }else{
            
            $account_data_epic=  AddAccountEpicModel::select('*');
            if(isset($request->acc_for)){
                $account_data_epic->where('account_payment_for', $account_for);
            }
            
            $account_data_epic = $account_data_epic->get()->toArray();
            
            
            $account_data_nom =  AddAccountModel::select('*');
            if(isset($request->acc_for)){
                $account_data_nom->where('account_payment_for', $account_for);
            }
            
            $account_data_nom = $account_data_nom->get()->toArray();
            
            $user_data['districtlist'] = [];
            $user_data['selected_state'] = '';
            $user_data['selected_district'] = '';
        }

        
        
        $user_data['account_data_merge'] = array_merge($account_data_epic,$account_data_nom);

       
        return view($this->view_path_status . 'view_added_account_eci', $user_data);

    }


    public function ajaxdistrictcall(Request $request){
			
        $districtList = DB::table('m_district')
            ->select(['m_district.DIST_NO','m_district.DIST_NAME'])
            ->where('m_district.ST_CODE',$request->st_code);
            
            
        $districtList = $districtList->orderBy('m_district.DIST_NO','ASC')
        ->get()->toArray();

    ?>
    <option value="">Select District</option>
    <?php
        foreach ($districtList as $districtLists) {
            ?>
    <option value="<?php echo $districtLists->DIST_NO; ?>"><?php echo $districtLists->DIST_NO.'-'.$districtLists->DIST_NAME; ?></option>
    <?php
        }
    }



    public function finialised_account(Request $request){

        $user_data['user_data'] = Auth::user();
        $st_code = Auth::user()->st_code;
        $dist_no = Auth::user()->dist_no;
        $last_id_nom = 0;
        $last_id_epic = 0;
        $check_finilised_epic = AddAccountEpicModel::select('*')->where('st_code',$st_code)->where('dist_no',$dist_no)->get(); 

        $check_finilised_nom = AddAccountModel::select('*')->where('st_code',$st_code)->where('dist_no',$dist_no)->get(); 

        $data['is_finalised'] = 1;

        if(count($check_finilised_epic) > 0 && count($check_finilised_epic) > 0){
        $last_id_epic = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no',$dist_no)->update($data);
        $last_id_nom = AddAccountModel::where('st_code', $st_code)->where('dist_no',$dist_no)->update($data);
        }

        if($last_id_epic){
            if($last_id_nom){
            return redirect('acdeo/add_account_info')->with('success', ' Account Information finilised Successfully');
        }}else{
            return redirect('acdeo/add_account_info')->with('error','Please update both account first');
        }

        

        
    }


    public function update_account_distwise(Request $request){
        $input = $request->all();
        
        $st_code = Auth::user()->st_code;
        $dist_no = $input['dist_no_hidden'];
        $last_id = 0;
        
        if($input['account_for_payemt'] == 2){
 
         $validator = Validator::make($request->all(), [
             'ACC_NAME_EN' => 'required|max:255',
             'ACC_MOBILE_EN' => 'required|numeric',
             'ACC_EMAIL_EN' => 'required|email',
             'ACC_NUM_EN' => 'required|max:255',
             'acc_typecs' => 'required',
             'ACC_IFSC_EN'=> 'required',
             'ACC_BENI_EN' => 'required|max:255',
             'ACC_charges_for'=>'required'
                 ], [
             'ACC_NAME_EN.required' => 'Please input account name',
             'ACC_MOBILE_EN.required'      => 'Please enter mobile number',
             'ACC_EMAIL_EN.required'      => 'Please enter email id',
             'ACC_NUM_EN.required'      => 'Please enter account number',
             'ACC_IFSC_EN.required'      => 'Please enter ifsc number',
             'acc_typecs.required'       => 'Please enter account type',
             'ACC_BENI_EN.required'      => 'Please enter beneficiary name',
             'ACC_charges_for.required' => 'Please input charges for EPIC'
             
         ]);
 
        }else{
         $validator = Validator::make($request->all(), [
             'ACC_NAME_EN' => 'required|max:255',
             'ACC_MOBILE_EN' => 'required|numeric',
             'ACC_EMAIL_EN' => 'required|email',
             'ACC_NUM_EN' => 'required|max:255',
             'ACC_IFSC_EN' => 'required|alpha_num',
             'acc_typecs' => 'required|alpha_num',
             'ACC_BENI_EN' => 'required|max:255',
                 ], [
             'ACC_NAME_EN.required' => 'Please input account name',
             'ACC_MOBILE_EN.required'      => 'Please enter mobile number',
             'ACC_EMAIL_EN.required'      => 'Please enter email id',
             'ACC_NUM_EN.required'      => 'Please enter account number',
             'ACC_IFSC_EN.required'      => 'Please enter ifsc number',
             'acc_typecs.required'      => 'Please enter account type',
             'ACC_BENI_EN.required'      => 'Please enter beneficiary name'
             
         ]);
        }
         
 
         if ($validator->fails()) {
            return redirect('acceo/ep/add_payment_info')->with('error', 'Data not updated');
         }

         $data = array();
		$data['account_payment_for'] = empty($input['account_for_payemt']) ? 0 : $input['account_for_payemt'];
		$data['account_name'] = empty($input['ACC_NAME_EN']) ? 0 : $input['ACC_NAME_EN'];
		$data['account_mobile'] = empty($input['ACC_MOBILE_EN']) ? 0 : $input['ACC_MOBILE_EN'];
		$data['account_email'] = empty($input['ACC_EMAIL_EN']) ? 0 : $input['ACC_EMAIL_EN'];
		$data['account_number'] = empty($input['ACC_NUM_EN']) ? 0 : $input['ACC_NUM_EN'];
		$data['account_type'] = empty($input['acc_typecs']) ? 0 : $input['acc_typecs'];
        $data['account_ifsc'] = empty($input['ACC_IFSC_EN']) ? 0 : $input['ACC_IFSC_EN'];
        $data['account_benificeary'] = empty($input['ACC_BENI_EN']) ? 0 : $input['ACC_BENI_EN'];
        $data['st_code'] = $st_code;
        $data['dist_no'] = $dist_no;
        


          // validation update
        if($input['account_number_previous'] == $input['ACC_NUM_EN']){
            if($input['account_for_payemt'] == 1){
                
                $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no',$dist_no)->update($data);
                $data['updated_by'] = Auth::user()->officername;
                $data['dist_no'] = 0;
                $log_table = AddAccountLogModel::insert($data);
            }elseif($input['account_for_payemt'] == 2){
                $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no', $dist_no)->update($data);
            }
        }else{
              
               $duplicate_account_epic = AddAccountEpicModel::where('account_number',$input['ACC_NUM_EN'])->get();
                $duplicate_account_nom = AddAccountModel::where('account_number',$input['ACC_NUM_EN'])->get();
               
                if(count($duplicate_account_nom) == 0 && count($duplicate_account_epic) == 0 ){
                    if($input['account_for_payemt'] == 1){
                        
                        //$last_id = AddAccountModel::create($data);
                        $last_id = AddAccountModel::where('st_code', $st_code)->where('dist_no',$dist_no)->update($data);
                        $data['updated_by'] = Auth::user()->officername;
                        $data['dist_no'] = $dist_no;
                        $log_table = AddAccountLogModel::insert($data);
                    }elseif($input['account_for_payemt'] == 2){
                        $last_id = AddAccountEpicModel::where('st_code', $st_code)->where('dist_no',$dist_no)->update($data);
                    }
                   
                }
            }


        //validation update


        if($last_id){
            return redirect('acceo/ep/add_payment_info')->with('success', 'Account Info Updated Successfully');
        }else{
            return redirect('acceo/ep/add_payment_info')->with('error', 'Data not updated');
        }
       
           
    }



    public function countreport(Request $request){
        $all_state_list = DB::table('m_state')->Select('ST_CODE','ST_NAME')->orderBy('ST_CODE','ASC')->get()->toArray();
        $final_array = [];
        
        foreach($all_state_list  as $st){
            $dist_count = DB::table('m_district')->select('*')->where('st_code',$st->ST_CODE)->count();
            $nom_acc_count = DB::table('eplan_account_info_nom')->select('*')->where('st_code',$st->ST_CODE)->count();
            $epic_acc_count = DB::table('eplan_account_info_epic')->select('*')->where('st_code',$st->ST_CODE)->count();
            $nom_fin_count = DB::table('eplan_account_info_nom')->select('*')->where('st_code',$st->ST_CODE)->where('is_finalised',1)->count();
            $epic_fin_count = DB::table('eplan_account_info_epic')->select('*')->where('st_code',$st->ST_CODE)->where('is_finalised',1)->count();
            $final_array[] = [

                'st_code' => $st->ST_CODE,
                'st_name' => $st->ST_NAME,
                'dist_count' => $dist_count,
                'nom_count' =>  $nom_acc_count,
                'epic_count' =>  $epic_acc_count,
                'nom_count_fin' => $nom_fin_count,
                'epic_count_fin' => $epic_fin_count
            ];

        }
        
        $user_data['user_data'] = Auth::user();
        $user_data['account_data_merge'] = $final_array;
       // dd($user_data);
        return view($this->view_path_status . 'view_added_account_count_eci', $user_data);
    }


}
