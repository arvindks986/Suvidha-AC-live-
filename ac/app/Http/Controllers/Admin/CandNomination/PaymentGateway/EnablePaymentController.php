<?php

namespace App\Http\Controllers\Admin\CandNomination\PaymentGateway;

use Auth;
use DB;
use Common;
use Session;
use App\commonModel;
use Validator;
use Carbon\Carbon;
use App\models\Admin\PollingStation;
use App\models\Admin\Nomination\PaymentGateway\{PaymentGateway, Challan};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnablePaymentController extends Controller
{
    protected $view_path_status          = 'admin.candform.payment_gateway.';

    public function __construct() {
        $this->commonModel = new commonModel();
    }

    ############################ CEO ENABLE PWD SERVICE ###############################################

    function Show_gateway_status_ceo(Request $request) {
        $user_data = Auth::user();
        $form_data = PaymentGateway::getCeoStatus($user_data->st_code);
        $enable_status = (isset($form_data->payment_gateway_available)) ? $form_data->payment_gateway_available : 0;
        $challen_status = (isset($form_data->payment_challan_available)) ? $form_data->payment_challan_available : 0;
        return view($this->view_path_status.'enablePayment',
        ['user_data'=>$user_data, 
        'enable_status'=>$enable_status, 
        'challen_status'=>$challen_status,
        'challen_url'=>(!empty($form_data->state_payment_url)) ? $form_data->state_payment_url : ''
        ]);
    }

    function Submit_gateway_status_ceo(Request $request) { //dd($request->all());

        $enable_status = ($request->has('enable_status') && $request->enable_status == 'on') ? '1' : '0';

        $challen_status = ($request->has('challen_status') && $request->challen_status == 'on') ? '1' : '0';

        $user_data = Auth::user();
        $to_filter = [
            'st_code'               => ($user_data->st_code)
        ]; 
        $user = PaymentGateway::updateOrCreate($to_filter, [
            'payment_gateway_available'      => $enable_status,
            'payment_challan_available'      => $challen_status
        ]);
        $get_changes = $user->getChanges();
        if(isset($get_changes['payment_gateway_available'])){
            if($get_changes['payment_gateway_available'] == '1'){
                $msg    = 'Payment Gateway Service Enabled Successfully!';
                Session::flash('alert-success', $msg);
            }else{
                $msg    = 'Payment Gateway Service Disabled Successfully!';
                Session::flash('alert-danger', $msg);
            }
        }else{
            if($get_changes['payment_challan_available'] == '1'){
                $msg    = 'Payment Challan Service Enabled Successfully!';
                Session::flash('alert-success', $msg);
            }else{
                $msg    = 'Payment Challan Service Disabled Successfully!';
                Session::flash('alert-danger', $msg);
            }
        }

        if($user->wasRecentlyCreated){
            $user->created_by = $user_data->id;
        }else{
            $user->updated_by = $user_data->id;
        }
        $user->save();

        return redirect('/acceo/enable_payment_gateway');
    }

    ########################################### END CEO #######################################################

    public function update_challan_details(Request $request) {

        if($request->pay_by_cash == '1'){
            $validator = Validator::make($request->all(), [
                'candidate_id'          => 'required',
                'pay_by_cash'            => 'required|in:0,1'
            ],[
                'pay_by_cash.required'   => 'Invalid data',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'candidate_id'          => 'required',
                'challan_no'            => 'required|alpha_num|max:15',
                'challan_date'          => 'required|date',
                'challan_recipt'        => 'required|mimes:pdf,jpg,png|max:5000'
            ],[
                'challan_no.required'   => 'please enter a valid Challan No.',
                'challan_no.alpha_num'  => 'please enter a valid Challan No.',
                'challan_no.max'        => 'Pease enter a valid Chalan No. less than 15 characters',
                'challan_date.*'        => 'please enter a valid Challan Date.',
                'challan_recipt.*'      => 'please enter a valid File (PDF,JPG,PNG Only and less than 5mb.)'
            ]);
        }

        if ($validator->fails())
        {
            return \Response::json([
              'success' => false,
              'errors'  => $validator->getMessageBag()->toArray()
            ]);
        }
        $user_data = Auth::user();

        if($request->pay_by_cash != '1'){
            ########################## File Upload ###########################
            $destination_path = '';
            $st_code          = $user_data->st_code;
            $year             = date('Y');
            $ac_no            = $user_data->ac_no;
            $election_name    = 'E-Challan';
            $destination_path = 'acaffidavit/'.$year.'/ac/'.$election_name.'/'.$st_code .'/'. $ac_no;
            $destination_path = 'uploads1'.'/'.$destination_path;
            
            foreach (explode('/',$destination_path) as $itr_folder) {
            if(empty($tmp_folder)){
                $tmp_folder = $itr_folder;
            }else{
                $tmp_folder = $tmp_folder.'/'.$itr_folder;
            }
            if (!file_exists($tmp_folder)) {
            mkdir($tmp_folder, 0777, true);
            }
            }
            
            $file       =   $request->file('challan_recipt');
            $filename   =   time().$file->getClientOriginalName();
            $filetype   =   $file->getMimeType();
            
            $file->move($destination_path,$filename);	
            
            $path = $destination_path.'/'.$filename;
            ############################ End File Upload #####################

            $data_to_update = [
                'st_code'                   => $user_data->st_code,
                'ac_no'                     => $user_data->ac_no,
                'challan_no'                => $request->challan_no,
                'challan_date'              => date('Y-m-d', strtotime($request->challan_date)),
                'challan_receipt'           => $path,
                "candidate_id"              => $request->candidate_id,
            ];
        }else{
            $data_to_update = [
                'st_code'                   => $user_data->st_code,
                'ac_no'                     => $user_data->ac_no,
                'payByCash'                 => '1',
                'pay_by_cash_paid'          => $request->pay_by_cash,
                'date_time_of_pbc'          => Carbon::now(),
                "candidate_id"              => $request->candidate_id
            ];
        }

        $data = Challan::updateOrCreate([
            'st_code'                   => $user_data->st_code,
            "candidate_id"              => $request->candidate_id
        ],$data_to_update);

        Session::flash('success_mes','Payment Details Uploaded Successfully !');

        return \Response::json([
            'success'           => true
        ]);    
    }
}
