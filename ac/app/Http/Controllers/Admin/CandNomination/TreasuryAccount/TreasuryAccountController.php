<?php
namespace App\Http\Controllers\Admin\CandNomination\TreasuryAccount;

use App\Http\Controllers\Controller;
use App\models\Admin\Nomination\TreasuryAccount\TreasuryAccount;
use App\models\Nomination\ProfileModel;
use App\models\Nomination\NominationProposerModel;
use App\models\Nomination\NominationPoliceCaseModel;
use App\models\Common\StateModel;
use App\models\Common\{FileModel, PcModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
use Common;
use App\models\Nomination\NominationApplicationModel;
use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\models\Admin\ApplicantsModel;
use Validator;

class TreasuryAccountController extends Controller
{
    public function __construct() {
        $this->ApplicantsModel = new ApplicantsModel();
    }

    public $base = '';
    public $folder = '';
    public $view_path = "admin.candform.treasuryAccount.";

	public function index(Request $request){
        $data = [];
        $data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Treasury Account";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("treasury_account");

        $filter = [
            'st_code'   => $st_code,
            'ac_no'     => $ac_no,
            'dist_no'   => $dist_no
        ];
        $title_array    = [];
        $request_array  = [];
        $default_user   = [];
        if (isset($st_code) && !empty($st_code)) {
            $statename                = getstatebystatecode($st_code);
            $title_array[]            = "State: " . $statename->ST_NAME;
            $request_array[]          =  'st_code='.$st_code;
            $default_user             = ['st_code'=> $st_code];
            if (isset($dist_no) && !empty($dist_no)) {
                $distname = getdistrictbydistrictno($st_code, $dist_no);
                $title_array[]        = "District: " . $distname->DIST_NAME;
                $request_array[]      =  'dist_no='.$dist_no;
                $default_user         = array_merge($default_user, ["dist_no"=>$dist_no]);
            }
            if (isset($ac_no) && !empty($ac_no)) {
                $acame = getacbyacno($st_code, $ac_no);
                $title_array[]        = "AC: " . $acame->AC_NAME;
                $request_array[]      =  'ac_no='.$ac_no;
                $default_user         = array_merge($default_user, ["ac_no"=>$ac_no]);
            }
        }
        $data['filter_buttons'] = $title_array;

        //buttons
        $data['buttons']    = [];
        // $data['buttons'][]  = [
        //   'name' => 'Export Excel',
        //   'href' =>  url($action.'/excel').'?'.implode('&', $request_array),
        //   'target' => true
        // ];
        $data['buttons'][]  = [
          'name' => 'Export Pdf',
          'href' =>  url($action.'/pdf').'?'.implode('&', $request_array),
          'target' => true
        ];
        $result_data = TreasuryAccount::getalldetail($filter);
        $data['results'] = [];
        $counter = 1;
        if(count($result_data)>0){
            $data['results'] = array_map(function($value)use(&$counter){
                $value['sno'] = $counter++;
                $value['st_name'] = trim(getstatebystatecode($value['st_code'])->ST_NAME);
                return $value;
            }, $result_data);
        }

        //form filters
        $data['filter_action'] = $action;
        $form_filter_array = [
            'st_code' => true,
            'dist_no' => true,
            'ac_no' => true,
            'ps_no' => false,
            'designation' => false,
        ];
        $form_filters = Common::get_form_filters($form_filter_array, $request);
        $data['form_filters'] = $form_filters;

        $data['user_data'] = Auth::user();
        $data['heading_title_with_all'] = $data['heading_title'];

        if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
        }

		return view($this->view_path.'treasuryAccount',$data);
    }

    public function get_report_pdf(Request $request){
        $data = $this->index($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path.'treasuryAccountPdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
    }

    public function update_verification_details(Request $request){
        $validator = Validator::make($request->all(), [
            'is_ceo_verified'  	 => 'in:1,null',
            'st_code'            => 'required|alpha_num|max:4',
			'dist_no'	         => 'required|numeric',
        ]);

		if ($validator->fails())
        {
            return \Response::json([
              'success' => false,
              'errors'  => $validator->getMessageBag()->toArray()
            ]);
		}
		$data = TreasuryAccount::where([
            'st_code'               => $request->st_code,
            'dist_code_nomination'  => $request->dist_no
        ])
		->update([
			'is_verified'	=> $request->is_ceo_verified
		]);
		Session::flash('success_mes','Data Verified Successfully!');
		return \Response::json([
			'success' 		=> true
        ]);
    }
}
