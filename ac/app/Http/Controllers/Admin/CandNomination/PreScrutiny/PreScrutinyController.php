<?php
namespace App\Http\Controllers\Admin\CandNomination\PreScrutiny;

use App\Http\Controllers\Controller;
use App\models\Admin\Nomination\PreScrutiny\PreScrutinyModel;
use App\models\Nomination\ProfileModel;
use App\models\Admin\ApplicantsModel;
use App\models\Nomination\NominationModel;
use App\models\Nomination\NominationApplicationModel;
use App\models\Nomination\NominationProposerModel;
use App\models\Nomination\NominationPoliceCaseModel;
use App\models\Common\StateModel;
use App\models\Common\{FileModel, PcModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
use Common;
use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Validator;

class PreScrutinyController extends Controller
{
    public $base = '';
    public $folder = '';
    public $view_path = "admin.candform.prescrutiny.";

	public function list_of_applicants(Request $request){

		$data = [];
		$data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Applications for Pre Scrutiny";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
		$dist_no                    = $request_filter['dist_no'];

		$data['prescrutiny_status'] 		= (!empty($request->prescrutiny_status) ? $request->prescrutiny_status : ''); 
		$data['prescrutiny_status_clear'] 	= (!empty($request->prescrutiny_status_clear) ? $request->prescrutiny_status_clear : '');
        $filter = [
            'st_code'   				=> $st_code,
			'ac_no'     				=> $ac_no,
			'between' 					=> $data['between'],
			'prescrutiny_status' 		=> $data['prescrutiny_status'],
			'prescrutiny_status_clear' 	=> $data['prescrutiny_status_clear'],
        ];
        $title_array    = [];
        if (isset($st_code) && !empty($st_code)) {
            $statename                = getstatebystatecode($st_code);
            $title_array[]            = "State Name: " . $statename->ST_NAME;
            if (isset($ac_no) && !empty($ac_no)) {
                $acame = getacbyacno($st_code, $ac_no);
                $title_array[]        = "AC Name: " . $acame->AC_NAME;
            }
        }
        $data['filter_buttons'] = array_reverse($title_array);
		$data['user_data'] = Auth::user();
		if(isset($data['between'][0]) && !empty($data['between'][0])){
            $request_array[]          = "date=".$request->date;
		}
		
		$response_data = PreScrutinyModel::getall_list($filter);
		
		$data['results'] = [];
		$count_i = 1;
		foreach($response_data as $each_data){

			if(empty($each_data['prescrutiny_status'])){
				$status_color = 'bg-light-cream';
				$status = 'submitted For Pre-Scrutiny';
			}elseif($each_data['prescrutiny_status'] == '1'){
				$status = 'Pre-Scrutiny Cleared';
				$status_color = 'bg-light-green';
			}elseif($each_data['prescrutiny_status'] == '2'){
				$status = 'Defects in Pre-Scrutiny';
				$status_color = 'bg-light-pink';
			}
			$data['results'][] = [
				'sno'						=> $count_i++,
				'st_name'					=> !empty($each_data['st_code']) ? getstatebystatecode($each_data['st_code'])->ST_NAME : '',
				'ac_name'					=> !empty($each_data['st_code']) && !empty($each_data['ac_no']) ? $each_data['ac_no'].'-'.getacbyacno($each_data['st_code'], $each_data['ac_no'])->AC_NAME : '',
				'st_code'					=> $each_data['st_code'],
				'ac_no'						=> $each_data['ac_no'],
				'nomination_no'				=> $each_data['nomination_no'],
				'candidate_name'			=> $each_data['name'],
				'gender'					=> $each_data['gender'],
				'age'						=> $each_data['age'],
				'address'					=> $each_data['address'],
				'prescrutiny_status'		=> $status,
				'status_color'				=> $status_color,
				'epic_no'					=> $each_data['epic_no'],
				'image'						=> (!empty($each_data['image'])) ? url($each_data['image']) : '',
				'prescrutiny_apply_datetime'=> !empty($each_data['prescrutiny_apply_datetime']) ? date('d M Y h:i A', strtotime($each_data['prescrutiny_apply_datetime'])) : '',
				'prescrutiny_status_date'	=> !empty($each_data['prescrutiny_status_date']) ? date('d M Y h:i A', strtotime($each_data['prescrutiny_status_date'])) : '',
				'prescrutiny_status_db'		=> (!empty($each_data['prescrutiny_status'])) ? $each_data['prescrutiny_status'] : '',
				'election_name'				=> PreScrutinyModel::getelectionnamebyid($each_data['election_id']),
				'action_url'				=> url('roac/nomination_detail/'.encrypt_string($each_data['id'])),
				'party_name'				=> getpartybyid($each_data['party_id']),
				'father_name'				=> $each_data['father_name'],
				'father_hname'				=> $each_data['father_hname'],
				'hname'						=> $each_data['hname'],
				'nom_id'					=> $each_data['id'],
			];
		}
		return view($this->view_path.'list_of_applicant',$data);
	}

	public function appointment_request(Request $request) {

		$data = [];
		$data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Appointment Request";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
		$dist_no                    = $request_filter['dist_no'];

		$data['prescrutiny_status'] 		= (!empty($request->prescrutiny_status) ? $request->prescrutiny_status : ''); 
		$data['prescrutiny_status_clear'] 	= (!empty($request->prescrutiny_status_clear) ? $request->prescrutiny_status_clear : '');
        $filter = [
            'st_code'   				=> $st_code,
			'ac_no'     				=> $ac_no,
			'between' 					=> $data['between'],
			'prescrutiny_status' 		=> $data['prescrutiny_status'],
			'prescrutiny_status_clear' 	=> $data['prescrutiny_status_clear'],
        ];
        $title_array    = [];
        if (isset($st_code) && !empty($st_code)) {
            $statename                = getstatebystatecode($st_code);
            $title_array[]            = "State Name: " . $statename->ST_NAME;
            if (isset($ac_no) && !empty($ac_no)) {
                $acame = getacbyacno($st_code, $ac_no);
                $title_array[]        = "AC Name: " . $acame->AC_NAME;
            }
        }
        $data['filter_buttons'] = array_reverse($title_array);
		$data['user_data'] = Auth::user();
		if(isset($data['between'][0]) && !empty($data['between'][0])){
            $request_array[]          = "date=".$request->date;
		}
		$response_data = PreScrutinyModel::get_all_appointment_request($filter);
		$data['request_appointment_table'] = [
			'total_appointment'		=> PreScrutinyModel::get_count_appointment(array_merge($filter, ['is_ro_acccept'=> ''])),
			'appointment_given' 	=> PreScrutinyModel::get_count_appointment(array_merge($filter, ['is_ro_acccept'=> '1'])),
			'appointment_pending'	=> PreScrutinyModel::get_count_appointment(array_merge($filter, ['is_ro_acccept'=> '2'])),
		];
		$data['results'] = [];
		$count_i = 1;

		foreach($response_data as $each_data){
			$data['results'][] = [
				'name'			=> $each_data['name'],
				'hname'			=> $each_data['hname'],
				'father_name'	=> $each_data['father_name'],
				'father_hname'	=> $each_data['father_hname'],
				'gender'		=> $each_data['gender'],
				'age'			=> $each_data['age'],
				'image'			=> $each_data['image'],
				'address'		=> $each_data['address'],
				'nomination_details'	=> PreScrutinyModel::get_all_nomination_details($each_data['candidate_id']),
				'appointment_details'	=> PreScrutinyModel::get_all_appointment_details($each_data['candidate_id'])
			];
		}

		if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
		}
		
		return view($this->view_path.'appointment_request',$data);
	}
	
	public function appointment_request_pdf(Request $request){
		$data = $this->appointment_request($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path.'appointment_request_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
	}

	public function view_nomination($id, Request $request){
		$id = decrypt_string($id);
		$user_nomination = PreScrutinyModel::get_nomination_application($id); 
		$data['nomination_id'] = $user_nomination['id'];
		$data = PreScrutinyModel::get_nomination($user_nomination['id']);
		if($data != false){
		if($data['have_police_case']=='1'){
			$data['have_police_case']='yes';
		}
		if($data['have_police_case']=='2'){
			$data['have_police_case']='no';
		}
		if($data['have_police_case']=='0'){
			$data['have_police_case']='NA';
		}
		//
		if($data['profit_under_govt']=='1'){
			$data['profit_under_govt']='yes';
		}
		if($data['profit_under_govt']=='2'){
			$data['profit_under_govt']='no';
		}
		if($data['profit_under_govt']=='0'){
			$data['profit_under_govt']='NA';
		}
		//
		if($data['court_insolvent']=='1'){
			$data['court_insolvent']='yes';
		}
		if($data['court_insolvent']=='2'){
			$data['court_insolvent']='no';
		}
		if($data['court_insolvent']=='0'){
			$data['court_insolvent']='NA';
		}
		//
		if($data['allegiance_to_foreign_country']=='1'){
			$data['allegiance_to_foreign_country']='yes';
		}
		if($data['allegiance_to_foreign_country']=='2'){
			$data['allegiance_to_foreign_country']='no';
		}
		if($data['allegiance_to_foreign_country']=='0'){
			$data['allegiance_to_foreign_country']='NA';
		}
		//
		if($data['disqualified_section8A']=='1'){
			$data['disqualified_section8A']='yes';
		}
		if($data['disqualified_section8A']=='2'){
			$data['disqualified_section8A']='no';
		}
		if($data['disqualified_section8A']=='0'){
			$data['disqualified_section8A']='NA';
		}
		//
		if($data['disloyalty_status']=='1'){
			$data['disloyalty_status']='yes';
		}
		if($data['disloyalty_status']=='2'){
			$data['disloyalty_status']='no';
		}
		if($data['disloyalty_status']=='0'){
			$data['disloyalty_status']='NA';
		}
		//
		if($data['subsiting_gov_taken']=='1'){
			$data['subsiting_gov_taken']='yes';
		}
		if($data['subsiting_gov_taken']=='2'){
			$data['subsiting_gov_taken']='no';
		}
		if($data['subsiting_gov_taken']=='0'){
			$data['subsiting_gov_taken']='NA';
		}
		//
		if($data['managing_agent']=='1'){
			$data['managing_agent']='yes';
		}
		if($data['managing_agent']=='2'){
			$data['managing_agent']='no';
		}
		if($data['managing_agent']=='0'){
			$data['managing_agent']='NA';
		}
		//
		if($data['disqualified_by_comission_10Asec']=='1'){
			$data['disqualified_by_comission_10Asec']='yes';
		}
		if($data['disqualified_by_comission_10Asec']=='2'){
			$data['disqualified_by_comission_10Asec']='no';
		}
		if($data['disqualified_by_comission_10Asec']=='0'){
			$data['disqualified_by_comission_10Asec']='NA';
		}
		//
		if($data['finalize']=='1'){
			$data['finalize']='yes';
		}
		if($data['finalize']=='2'){
			$data['finalize']='no';
		}
		if($data['finalize']=='0'){
			$data['finalize']='NA';
		}
		
		$data['party_id'] =   DB::connection('mysql')->table('m_party')->select('PARTYNAME')->where('CCODE', '=', $data['party_id'])->value('PARTYNAME'); 	
		
	
		$data['reference_id']               = $user_nomination['nomination_no'];
		$data['href_download_application']  = url("roac/nomination_detail_download/".encrypt_string($id));
	
	
		$data['breadcrumbs']    = [];
		$data['breadcrumbs'][]  = [
		  'href'    => url('/'),
		  'name'    => "<span class='icon icon-home'> </span>",
		  'is_last' => false
		];
		$data['breadcrumbs'][]  = [
		  'href'    => url('/nomination'),
		  'name'    => "Nomination",
		  'is_last' => true
		];
		$data['is_active']     = 'nomination';
		$data['heading_title'] = "Nomination Detail";
		$data['action'] = url('nomination/apply-nomination-finalize/post');
		
		$data['st_name'] = '';
		$state_object = StateModel::get_state($data['st_code']);
		if($state_object){
		  $data['st_name'] = $state_object->ST_NAME;
		}
	
		$data['states'] = [];
		$states = StateModel::get_states();
		foreach ($states as $key => $state_iterage) {
		  $data['states'][] = [
			'st_code'    => $state_iterage->st_code,
			'st_name'    => $state_iterage->st_name,
		  ];
		}
	
		$data['districts'] = [];
		$districts = DistrictModel::get_districts();
		foreach ($districts as $key => $district_iterage) {
		  $data['districts'][] = [
			'district_no'     => $district_iterage->district_no,
			'district_name'   => $district_iterage->district_name,
			'st_code'         => $district_iterage->st_code,
			'encoded'         => base64_encode($district_iterage->district_no),
		  ];
		}
		$data['user_data']  = Auth::user();
		$data['profileimg']  = url($data['image']); 
		$data['qr_code']      = url($data['qrcode']);
		$data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
		$data['non_recognized_proposers']   = NominationProposerModel::where([
			'candidate_id' => $data['candidate_id'],
			'nomination_id' => $id
		  ])->orderBy('id', 'desc')->get()->toArray(); 
		$data['police_cases']               = NominationPoliceCaseModel::get_police_cases_ro($data['nomination_id']); 
		$data['affidavit']  = url($data['affidavit']);
		$data['profile_data'] = ProfileModel::where('candidate_id', $data['candidate_id'])->first();
		$data['comment_section'] = PreScrutinyModel::get_comment_data_by_id($data['nomination_id']);
		return view($this->view_path.'view-nomination',$data);
		}else{ 
			return redirect()->back()->with('error_mes', 'Invaild Nomination data !');
		}
	  }

	  public function view_nomination_full($id, Request $request) { 
		$id = decrypt_string($id);
		$user_nomination = PreScrutinyModel::get_nomination_application($id); 
		$data['nomination_id'] = $user_nomination['id'];
		$data = PreScrutinyModel::get_nomination($user_nomination['id']);
		if($data != false){
		if($data['have_police_case']=='1'){
			$data['have_police_case']='yes';
		}
		if($data['have_police_case']=='2'){
			$data['have_police_case']='no';
		}
		if($data['have_police_case']=='0'){
			$data['have_police_case']='NA';
		}
		//
		if($data['profit_under_govt']=='1'){
			$data['profit_under_govt']='yes';
		}
		if($data['profit_under_govt']=='2'){
			$data['profit_under_govt']='no';
		}
		if($data['profit_under_govt']=='0'){
			$data['profit_under_govt']='NA';
		}
		//
		if($data['court_insolvent']=='1'){
			$data['court_insolvent']='yes';
		}
		if($data['court_insolvent']=='2'){
			$data['court_insolvent']='no';
		}
		if($data['court_insolvent']=='0'){
			$data['court_insolvent']='NA';
		}
		//
		if($data['allegiance_to_foreign_country']=='1'){
			$data['allegiance_to_foreign_country']='yes';
		}
		if($data['allegiance_to_foreign_country']=='2'){
			$data['allegiance_to_foreign_country']='no';
		}
		if($data['allegiance_to_foreign_country']=='0'){
			$data['allegiance_to_foreign_country']='NA';
		}
		//
		if($data['disqualified_section8A']=='1'){
			$data['disqualified_section8A']='yes';
		}
		if($data['disqualified_section8A']=='2'){
			$data['disqualified_section8A']='no';
		}
		if($data['disqualified_section8A']=='0'){
			$data['disqualified_section8A']='NA';
		}
		//
		if($data['disloyalty_status']=='1'){
			$data['disloyalty_status']='yes';
		}
		if($data['disloyalty_status']=='2'){
			$data['disloyalty_status']='no';
		}
		if($data['disloyalty_status']=='0'){
			$data['disloyalty_status']='NA';
		}
		//
		if($data['subsiting_gov_taken']=='1'){
			$data['subsiting_gov_taken']='yes';
		}
		if($data['subsiting_gov_taken']=='2'){
			$data['subsiting_gov_taken']='no';
		}
		if($data['subsiting_gov_taken']=='0'){
			$data['subsiting_gov_taken']='NA';
		}
		//
		if($data['managing_agent']=='1'){
			$data['managing_agent']='yes';
		}
		if($data['managing_agent']=='2'){
			$data['managing_agent']='no';
		}
		if($data['managing_agent']=='0'){
			$data['managing_agent']='NA';
		}
		//
		if($data['disqualified_by_comission_10Asec']=='1'){
			$data['disqualified_by_comission_10Asec']='yes';
		}
		if($data['disqualified_by_comission_10Asec']=='2'){
			$data['disqualified_by_comission_10Asec']='no';
		}
		if($data['disqualified_by_comission_10Asec']=='0'){
			$data['disqualified_by_comission_10Asec']='NA';
		}
		//
		if($data['finalize']=='1'){
			$data['finalize']='yes';
		}
		if($data['finalize']=='2'){
			$data['finalize']='no';
		}
		if($data['finalize']=='0'){
			$data['finalize']='NA';
		}
		
		$data['party_id'] =   DB::connection('mysql')->table('m_party')->select('PARTYNAME')->where('CCODE', '=', $data['party_id'])->value('PARTYNAME'); 	
		
	
		$data['reference_id']               = $user_nomination['nomination_no'];
		$data['href_download_application']  = url("roac/nomination_detail_download/".encrypt_string($id));
	
	
		$data['breadcrumbs']    = [];
		$data['breadcrumbs'][]  = [
		  'href'    => url('/'),
		  'name'    => "<span class='icon icon-home'> </span>",
		  'is_last' => false
		];
		$data['breadcrumbs'][]  = [
		  'href'    => url('/nomination'),
		  'name'    => "Nomination",
		  'is_last' => true
		];
		$data['is_active']     = 'nomination';
		$data['heading_title'] = "Nomination Detail";
		$data['action'] = url('nomination/apply-nomination-finalize/post');
		
		$data['st_name'] = '';
		$state_object = StateModel::get_state($data['st_code']);
		if($state_object){
		  $data['st_name'] = $state_object->ST_NAME;
		}
	
		$data['states'] = [];
		$states = StateModel::get_states();
		foreach ($states as $key => $state_iterage) {
		  $data['states'][] = [
			'st_code'    => $state_iterage->st_code,
			'st_name'    => $state_iterage->st_name,
		  ];
		}
	
		$data['districts'] = [];
		$districts = DistrictModel::get_districts();
		foreach ($districts as $key => $district_iterage) {
		  $data['districts'][] = [
			'district_no'     => $district_iterage->district_no,
			'district_name'   => $district_iterage->district_name,
			'st_code'         => $district_iterage->st_code,
			'encoded'         => base64_encode($district_iterage->district_no),
		  ];
		}
		$data['user_data']  = Auth::user();
		$data['profileimg']  = url($data['image']); 
		$data['qr_code']      = url($data['qrcode']);
		$data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
		$data['non_recognized_proposers']   = NominationProposerModel::where([
			'candidate_id' => $data['candidate_id'],
			'nomination_id' => $id,
			'status'    	=> 1
		  ])->orderBy('id', 'desc')->get()->toArray();
		$data['police_cases']               = NominationPoliceCaseModel::get_police_cases_ro($data['nomination_id']);
		$data['affidavit']  = !empty($data['affidavit']) ? url($data['affidavit']) : '';
		// $data['comment_section'] = PreScrutinyModel::get_comment_data_by_id($data['nomination_id']);
		$data['profile_data'] = ProfileModel::where('candidate_id', $data['candidate_id'])->first();
		return view('admin/candform/view-nomination',$data);
		}else{ 
			return redirect()->back()->with('error_mes', 'Invaild Nomination data !');
		}
	  }
	
	
	  public function download_nomination($id, Request $request){
		
		$id = decrypt_string($id);
		$user_nomination = PreScrutinyModel::get_nomination_application($id);
		$data['nomination_id'] = $user_nomination['id'];
	
		$data = PreScrutinyModel::get_nomination($user_nomination['id']);
		
		if($data['have_police_case']=='1'){
			$data['have_police_case']='yes';
		}
		if($data['have_police_case']=='2'){
			$data['have_police_case']='no';
		}
		if($data['have_police_case']=='0'){
			$data['have_police_case']='NA';
		}
		//
		if($data['profit_under_govt']=='1'){
			$data['profit_under_govt']='yes';
		}
		if($data['profit_under_govt']=='2'){
			$data['profit_under_govt']='no';
		}
		if($data['profit_under_govt']=='0'){
			$data['profit_under_govt']='NA';
		}
		//
		if($data['court_insolvent']=='1'){
			$data['court_insolvent']='yes';
		}
		if($data['court_insolvent']=='2'){
			$data['court_insolvent']='no';
		}
		if($data['court_insolvent']=='0'){
			$data['court_insolvent']='NA';
		}
		//
		if($data['allegiance_to_foreign_country']=='1'){
			$data['allegiance_to_foreign_country']='yes';
		}
		if($data['allegiance_to_foreign_country']=='2'){
			$data['allegiance_to_foreign_country']='no';
		}
		if($data['allegiance_to_foreign_country']=='0'){
			$data['allegiance_to_foreign_country']='NA';
		}
		//
		if($data['disqualified_section8A']=='1'){
			$data['disqualified_section8A']='yes';
		}
		if($data['disqualified_section8A']=='2'){
			$data['disqualified_section8A']='no';
		}
		if($data['disqualified_section8A']=='0'){
			$data['disqualified_section8A']='NA';
		}
		//
		if($data['disloyalty_status']=='1'){
			$data['disloyalty_status']='yes';
		}
		if($data['disloyalty_status']=='2'){
			$data['disloyalty_status']='no';
		}
		if($data['disloyalty_status']=='0'){
			$data['disloyalty_status']='NA';
		}
		//
		if($data['subsiting_gov_taken']=='1'){
			$data['subsiting_gov_taken']='yes';
		}
		if($data['subsiting_gov_taken']=='2'){
			$data['subsiting_gov_taken']='no';
		}
		if($data['subsiting_gov_taken']=='0'){
			$data['subsiting_gov_taken']='NA';
		}
		//
		if($data['managing_agent']=='1'){
			$data['managing_agent']='yes';
		}
		if($data['managing_agent']=='2'){
			$data['managing_agent']='no';
		}
		if($data['managing_agent']=='0'){
			$data['managing_agent']='NA';
		}
		//
		if($data['disqualified_by_comission_10Asec']=='1'){
			$data['disqualified_by_comission_10Asec']='yes';
		}
		if($data['disqualified_by_comission_10Asec']=='2'){
			$data['disqualified_by_comission_10Asec']='no';
		}
		if($data['disqualified_by_comission_10Asec']=='0'){
			$data['disqualified_by_comission_10Asec']='NA';
		}
		//
		if($data['finalize']=='1'){
			$data['finalize']='yes';
		}
		if($data['finalize']=='2'){
			$data['finalize']='no';
		}
		if($data['finalize']=='0'){
			$data['finalize']='NA';
		}
		
		$data['party_id'] =   DB::connection('mysql')->table('m_party')->select('PARTYNAME')->where('CCODE', '=', $data['party_id'])->value('PARTYNAME');
		
		
	
		$data['breadcrumbs']    = [];
		$data['breadcrumbs'][]  = [
		  'href'    => url('/'),
		  'name'    => "<span class='icon icon-home'> </span>",
		  'is_last' => false
		];
		$data['breadcrumbs'][]  = [
		  'href'    => url('/nomination'),
		  'name'    => "Nomination",
		  'is_last' => true
		];
		$data['is_active']     = 'nomination';
		$data['heading_title'] = "Nomination Detail";
		$data['action'] = url('nomination/apply-nomination-finalize/post');
		
		$data['st_name'] = '';
		$state_object = StateModel::get_state($data['st_code']);
		if($state_object){
		  $data['st_name'] = $state_object->ST_NAME;
		}
	
		$data['states'] = [];
		$states = StateModel::get_states();
		foreach ($states as $key => $state_iterage) {
		  $data['states'][] = [
			'st_code'    => $state_iterage->st_code,
			'st_name'    => $state_iterage->st_name,
		  ];
		}
	
		$data['districts'] = [];
		$districts = DistrictModel::get_districts();
		foreach ($districts as $key => $district_iterage) {
		  $data['districts'][] = [
			'district_no'     => $district_iterage->district_no,
			'district_name'   => $district_iterage->district_name,
			'st_code'         => $district_iterage->st_code,
			'encoded'         => base64_encode($district_iterage->district_no),
		  ];
		}
		$data['qrcode'] =='';
		if(!empty($data['image'])){
		$data['profileimg']  =  public_path($data['image']); 
		} else {
		$data['profileimg']  =  '#'; 
		}
		if(isset($data['qrcode'])){
		$data['qrcode']  =  public_path($data['qrcode']); 
		} else {
		$data['qrcode']  =  '#'; 
		}
		
		$data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
		$data['non_recognized_proposers']   = NominationProposerModel::where([
			'candidate_id' => $data['candidate_id'],
			'nomination_id' => $id,
			'status'    => 1
		  ])->orderBy('id', 'desc')->get()->toArray(); 
		$data['profile_data'] = ProfileModel::where('candidate_id', $data['candidate_id'])->first();
		$data['police_cases']               = NominationPoliceCaseModel::get_police_cases_ro($data['nomination_id']); 
		if(!empty($data['affidavit'])){
		$data['affidavit']  =  url($data['affidavit']); 
		} else {
		$data['affidavit']  =  '#'; 
		}
		//$data['affidavit']      = url($data['affidavit']);
		$data['nomination_no']  = $data['nomination_no'];
		$data['user_data']  = Auth::user();
		$name_excel = time();
	
		$setting_pdf = [
		  'margin_top'        => 20,        // Set the page margins for the new document.
		  'margin_bottom'     => 10,    
		];
		
		$pdf = \PDF::loadView($this->view_path.'download-nomination',$data, [], $setting_pdf);
		return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
	}

	public function submit_prescrutiny_details(Request $request) {
		$validator = Validator::make($request->all(), [
			'nomination_no'	=> 'required'
		])->validate();

		$inputValue = $request->except(['_token','nomination_no']);
		$id = decrypt_string($request->nomination_no);
		$user_nomination = PreScrutinyModel::get_nomination_application($id);
		
	DB::beginTransaction();
	try{
		if(!empty($user_nomination)){
			if(count($inputValue)>0){
				foreach($inputValue as $key => $value){
					$part_name = \explode('&&', $key);
					PreScrutinyModel::create([
						'candidate_id'			=> $user_nomination['candidate_id'],
						'nomination_no'			=> $user_nomination['nomination_no'],
						'nomination_id'			=> $user_nomination['id'],
						'st_code'				=> $user_nomination['st_code'],
						'dist_no'				=> $user_nomination['dist_no'],
						'ac_no'					=> $user_nomination['ac_no'],
						'pc_no'					=> $user_nomination['pc_no'],
						'election_id'			=> $user_nomination['election_id'],
						'form_name'				=> 'nomination_form',
						'form_part_no'			=> PreScrutinyModel::getformated($part_name[1]),
						'column_name'			=> $part_name[0],
						'defect'				=> str_replace('_',' ',$part_name[2]),
						'remark'				=> $value,
						'status'				=> '0',
						'added_created_at'		=> Carbon::now(),
						'created_by'			=> Auth::id(),
					]);
				}
			}
		}
				DB::table('nomination_application')->where('id','=', $id)->update(
					['prescrutiny_status' 		=> 2,
					 'prescrutiny_comment' 		=> 'Quire Raised',
					 'prescrutiny_status_date'	=> Carbon::now()
					 ]);
		}catch(\Exception $e){
			DB::rollback();
			Session::flash('status',0);
			Session::flash('flash-message',"Please Try Again.");
			return Redirect::back();
		}
		DB::commit();
		Session::flash('success_mes','Data Added Successfully !');
		return redirect()->back();
	}

	public function cleared_pre_scrutiny(Request $request){
		$validator = Validator::make($request->all(), [
			'nomination_id'	=> 'required'
		])->validate();
		$id = decrypt_string($request->nomination_id);
		DB::table('nomination_application')->where('id','=', $id)->update(
			['prescrutiny_status' => 1,
			 'prescrutiny_comment' => 'Cleared',
			'prescrutiny_status_date'	=> Carbon::now()
			]);
		Session::flash('success_mes','Pre Scrutiny Cleared Successfully !');
		return \Response::json([
			'success' 		=> true
        ]);
	}

	public function get_payment_recipt(Request $request) {
		$transcation_details = DB::table('nomination_application')->select('payment_details_common.*', 'nomination_application.*')
		->join('payment_details_common', [['nomination_application.candidate_id', '=', 'payment_details_common.candidate_id'],
		['nomination_application.st_code', '=', 'payment_details_common.st_code']])
		->where('nomination_application.nomination_no', $request->nom_id)
		->where('payment_details_common.bank_transaction_status', '1')
                ->where('payment_details_common.bank_transtimestamp', '<>','')


		->first();
		
		$response_data = [];
		if(!empty($transcation_details)){
			$payment_recipt_url = '';
			if($transcation_details->st_code == 'S04'){
				$payment_recipt_url = !empty($transcation_details->challan_url) ? $transcation_details->challan_url : '';
			}elseif($transcation_details->st_code == 'S06'){
				$encry_nom_id = !empty($transcation_details->nomination_no) ? encrypt_string($transcation_details->nomination_no) : '';
				$payment_recipt_url = url('download_candidate_payment_receipt/'.$encry_nom_id);
			}elseif($transcation_details->st_code == 'S25'){
				$encry_nom_id = !empty($transcation_details->nomination_no) ? encrypt_string($transcation_details->nomination_no) : '';
				$payment_recipt_url = 'https://wbifms.gov.in/grips-svc/payment/svc/utility/secure/download/challan?grn='.$transcation_details->$challan_ref_id.'&token='.time();
			}elseif($transcation_details->st_code == 'S03'){
				$encry_nom_id = !empty($transcation_details->nomination_no) ? encrypt_string($transcation_details->nomination_no) : '';
				$payment_recipt_url = url('download_candidate_payment_receipt/'.$encry_nom_id);
			}elseif($transcation_details->st_code == 'S11'){
				$encry_nom_id = !empty($transcation_details->nomination_no) ? encrypt_string($transcation_details->nomination_no) : '';
				$payment_recipt_url = url('download_candidate_payment_receipt/'.$encry_nom_id);
			}elseif($transcation_details->st_code == 'S22'){
				$encry_nom_id = !empty($transcation_details->nomination_no) ? encrypt_string($transcation_details->nomination_no) : '';
				$payment_recipt_url = url('download_candidate_payment_receipt/'.$encry_nom_id);
			}elseif($transcation_details->st_code == 'U07'){
				$encry_nom_id = !empty($transcation_details->nomination_no) ? encrypt_string($transcation_details->nomination_no) : '';
				$payment_recipt_url = url('download_candidate_payment_receipt/'.$encry_nom_id);
			}
			else{
				$encry_nom_id = !empty($transcation_details->nomination_no) ? encrypt_string($transcation_details->nomination_no) : '';
				$payment_recipt_url = url('download_candidate_payment_receipt/'.$encry_nom_id);
			}
			

			$response_data = [
				'candidate_name'	=> $transcation_details->name,
				'nomination_no'		=> $transcation_details->nomination_no,
				'election_name_one'	=> 'GENERAL-2021',
				'st_code'			=> $transcation_details->st_code,
				'st_name'			=> !empty($transcation_details->st_code) ? getstatebystatecode($transcation_details->st_code)->ST_NAME : '',
				'ac_no_name'		=> $transcation_details->ac_no.'-'.(getacbyacno($transcation_details->st_code, $transcation_details->ac_no))->AC_NAME,
				'payment_date'		=> !empty($transcation_details->bank_transtimestamp) ? date('d-m-Y', strtotime($transcation_details->bank_transtimestamp)) : '',
				'payment_time'		=> !empty($transcation_details->bank_transtimestamp) ? date('h:i A', strtotime($transcation_details->bank_transtimestamp)) : '',
				'online_receipt'	=> $payment_recipt_url,
				'txn_amount'		=> !empty($transcation_details->challan_amount) ? $transcation_details->challan_amount : '',
				'bank_reff_no'		=> !empty($transcation_details->bank_transaction_id) ? $transcation_details->bank_transaction_id : '',
				'bank_code'			=> !empty($transcation_details->bank_cd) ? $transcation_details->bank_cd : '',
				'challan_ref_id'			=> !empty($transcation_details->challan_ref_id) ? $transcation_details->challan_ref_id : '',
				'challan_amount'			=> !empty($transcation_details->challan_amount) ? $transcation_details->challan_amount : '',
			];
		}


		if ($request->has('is_export')=='1') {
            return $response_data;
		}



		return \Response::json([
			'success' 		=> true,
			'data'			=> !empty($response_data) ? $response_data : (Object)[]
        ]);
	}


	public function get_challan_payment_recipt(Request $request) {
		$transcation_details = DB::table('nomination_application')->select('challan_payment.*', 'nomination_application.*')
		->join('challan_payment', [['nomination_application.candidate_id', '=', 'challan_payment.candidate_id'],
		['nomination_application.st_code', '=', 'challan_payment.st_code']])
		->where('nomination_application.nomination_no', $request->nom_id)
		->first();
		$response_data = [];
		if(!empty($transcation_details)){
			$response_data = [
				'candidate_name'	=> $transcation_details->name,
				'ac_no_name'		=> $transcation_details->ac_no.'-'.(getacbyacno($transcation_details->st_code, $transcation_details->ac_no))->AC_NAME,
				'payment_date'		=> date('d-m-Y', strtotime($transcation_details->challan_date)),
				// 'payment_time'		=> date('h:i A', strtotime($transcation_details->transaction_time)),
				'challan_receipt'			=> url($transcation_details->challan_receipt),
				'challan_no'				=> $transcation_details->challan_no
			];
		}
		
		return \Response::json([
			'success' 		=> true,
			'data'			=> !empty($response_data) ? $response_data : (Object)[]
        ]);
	}

	public function download_payment_receipt(Request $request){
		$decrypted_nom_id = decrypt_string($request->nom_id);
		$data = $this->get_payment_recipt($request->merge(['is_export' => 1, 'nom_id' => $decrypted_nom_id]));
        $name_pdf = strtolower('Candidate_Payment_Receipt');
        $pdf = \PDF::loadView('admin.candform.payment_receipt.download-paymentreceipt',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
	}

	public function appointment_accepted(Request $request) {

		$validator = Validator::make($request->all(), [
			'is_ro_accepted'  	 => 'in:1,null',
			'candidate_id'	     => 'required|numeric',
			'st_code'	     	 => 'required',
			'ac_no'	     		 => 'required',
        ]);

		if ($validator->fails())
        {
            return \Response::json([
              'success' => false,
              'errors'  => $validator->getMessageBag()->toArray()
            ]);
		}
		$data = DB::table('appointment_schedule_date_time')
		->where('candidate_id', $request->candidate_id)
		->where('st_code', $request->st_code)
		->where('ac_no', $request->ac_no)
		->update([
			'is_ro_acccept'	=> $request->is_ro_accepted
		]);
		Session::flash('success_mes','Appointment Updated Successfully!');
		return \Response::json([
			'success' 		=> true
        ]);
	}
	
	public function decrypt_nom_id(Request $request){
		
		$validator = Validator::make($request->all(), [
			'nom_id'  	 => 'required'
        ]);

		if ($validator->fails())
        {
            return \Response::json([
              'success' => false,
              'errors'  => $validator->getMessageBag()->toArray()
            ]);
		}

		$encrypt_method = "AES-256-CBC";
		$key='E(*x5lcyam%$.9dx';
		$iv='E(*x5lcyam%$.9dx';

		try{
			$decrypted_nom = openssl_decrypt($request->nom_id, $encrypt_method, $key, 0, $iv);

			$nomination_details = DB::table('nomination_application')->select('finalize_after_payment')->where('nomination_no', $decrypted_nom)->first();
			
			$inserted = DB::table('nomination_ro_submit_log')->insert([
				'nomination_no'		=> $decrypted_nom,
				'ro_submit_date'	=> date('Y-m-d'),
				'ro_submit_time'	=> date('H:i:s'),
				'created_at'		=> Carbon::now(),
				'created_by'		=> Auth::id()
			]);
			
		}catch(Exception $e){
			$decrypted_nom = '';
		}

		return \Response::json([
			'success' 					=> true,
			'nom_id'					=> ($decrypted_nom) ? $decrypted_nom : $request->nom_id,
			'finalize_after_payment'	=> !empty($nomination_details) ? $nomination_details->finalize_after_payment : '0'
        ]);
	}

	public function updatenominationform($nomid1) {
		$nomid1 = Crypt::encrypt('22');
		$data  = [];    
			   if(Auth::check()){ 
				   $user = Auth::user();
				   $d=$this->commonModel->getunewserbyuserid($user->id); 
					$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
				$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
				if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
				 $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
				}
			  $nomid   = Crypt::decrypt($nomid1);    
			  
				 if($cand_finalize_ro=='1')
				   {
					\Session::flash('finalize_mes', 'Candidate Nomination is Finalize');
					 return Redirect::to('/roac/listnomination');  
				   }  
				   $nom=getById('candidate_nomination_detail','nom_id',$nomid); 
						 $cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
						 $st_code=$ele_details->ST_CODE;
						$const_no=$ele_details->CONST_NO;
						$distno=$d->dist_no;

				   $st=getstatebystatecode($ele_details->ST_CODE);
					$dist=getdistrictbydistrictno($ele_details->ST_CODE,$d->dist_no);
					$ac=$this->commonModel->getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
					$all_state=$this->commonModel->getallstate();
					$all_dist=getalldistrictbystate($ele_details->ST_CODE);
					$all_ac=getacbystate($ele_details->ST_CODE);

					
				   $data['user_data']=$d;
				   $data['ele_details']=$ele_details;
				   $data['nomid']=$nomid;
				   $data['nomDetails']=$nom;
				   $data['persoanlDetails']=$cand;
				   $data['getStateDetail']=$st;
					
				   $data['disttDetails']=$dist;
				   $data['all_state']=$all_state;
				   $data['all_dist']=$all_dist;
				   $data['all_ac']=$all_ac;    
					$data['nomid1']=$nomid1;
			// dd($data);
			  return view($this->view_path.'.updatenomination', $data);           
				   }
			   else{
					  return redirect('/officer-login');
				   }
		   }  
public function updatenomination(request $request, $nomid){
	
   if(Auth::check()){ 
				   $user = Auth::user();
				   $d=$this->commonModel->getunewserbyuserid($user->id); 
				   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		  
		
	  
			 $this->validate(
				 $request,
		 [ 
		   'party_id' => 'required',
		   'symbol_id' => 'required',
		   'name'=>'required',
		   'hname'=>'required',
		   'fname'=>'required',
		   'cand_vname'=>'required',
		   //'fhname'=>'required',
		   //'fvname'=>'required',
		   'gender' => 'required|string|in:male,female,third',
		   'addressline1'=>'required',
		   // 'addresshline1'=>'required',
			'addressv'=>'required',
		   'state'=>'required',
		   'district'=>'required',
		   'ac'=>'required',
		   'cand_category'=>'required',
		   'age'=>'required|numeric',
		   ],
		 [ 
		   'party_id.required' => 'Please select party',
		   'symbol_id.required' => 'Please select symbol',
		   'name.required'=>'Please enter name in english',
		   'hname.required'=>'Please enter name in hindi',
		   'cand_vname.required'=>'Please enter name in vernacular',
		   'fname.required'=>'Please enter father/husband name in english',
		   'fhname.required'=>'Please enter father/husband name in hindi',
		   'fvname.required'=>'Please enter father/husband name in vernacular',
		   'addressline1.required'=>'Please enter address',
		   'addresshline1.required'=>'Please enter hindi address',
		   'addressv.required'=>'Please enter address in vernacular',
		   'state.required'=>'Please select state',
		   'district.required'=>'Please select district',
		   'ac.required'=>'Please select ac',
		   'cand_category.required'=>'Please select candidate category',
		   'age.required'=>'Please enter candidate age',
		   'age.numeric'=>'Please enter valid age', 
		 ]
	   );
	 
	  
		   $dob = $request->input('dob');
		   $age = $request->input('age');;
		   $party=getpartybyid($request->input('party_id'));
		   $nom=getById('candidate_nomination_detail','nom_id',$nomid); 

		   $getPersonalDetails = getById('candidate_personal_detail','candidate_id',$nom->candidate_id); ;
		   $candimg = $getPersonalDetails->cand_image ;

		   if($party->PARTYTYPE=="S"){ 
		   $partyDetails = DB::table('m_party')
				   ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
				   ->where('m_party.PARTYTYPE','=','S')
				   ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
				   ->where('m_party.CCODE','=',$party->CCODE)
				   ->select('m_party.*')->first();
		   if(isset($partyDetails)){
				 $partytype = $party->PARTYTYPE;
			}
			else{
				$partytype ='U';
			}
		  } 
		  else {
				$partytype = $party->PARTYTYPE;
		  }
		   $candImage = '';
		   $request->file('profileimg') ;

		   $constType = $ele_details->CONST_TYPE ;
		   $stcode = $ele_details->ST_CODE ;
		   $electionType = $ele_details->CONST_TYPE;
   

		   $candName = $request->input('name') ;
			
		   if($request->input('addressline2') != ''){
			   $addressEnglish = $this->xssClean->clean_input(Check_Input($request->input('addressline1'))).','.$this->xssClean->clean_input(Check_Input($request->input('addressline2')));
		   }else{
			   $addressEnglish = $this->xssClean->clean_input(Check_Input($request->input('addressline1'))) ;
		   }
		   if($request->input('addresshline2') != ''){
			$addressHindi =$this->xssClean->clean_input(Check_Input($request->input('addresshline1'))). ', ' .$this->xssClean->clean_input(Check_Input($request->input('addresshline2')));
		   }else{
			   $addressHindi = $this->xssClean->clean_input(Check_Input($request->input('addresshline1')));
		   }
		//
   $candPersonalData = array(
			   'cand_name'=>$this->xssClean->clean_input(Check_Input($request->input('name'))),
			   'cand_hname'=>$this->xssClean->clean_input(Check_Input($request->input('hname'))),
			   'cand_vname'=>$this->xssClean->clean_input(Check_Input($request->input('cand_vname'))),
			   'cand_alias_name'=>$this->xssClean->clean_input(Check_Input($request->input('aliasname'))),
			   'cand_alias_hname'=>$this->xssClean->clean_input(Check_Input($request->input('aliashname'))),
			   'candidate_father_name'=>$this->xssClean->clean_input(Check_Input($request->input('fname'))),
			   'cand_email'=>$this->xssClean->clean_input(Check_Input($request->input('email'))),
			   'cand_mobile'=>$this->xssClean->clean_input(Check_Input($request->input('cand_mobile'))),
			   'cand_fhname'=>$this->xssClean->clean_input(Check_Input($request->input('fhname'))),
			   'cand_fvname'=>$this->xssClean->clean_input(Check_Input($request->input('fvname'))),
			   'cand_gender'=>$this->xssClean->clean_input(Check_Input($request->input('gender'))),
			   'candidate_residence_address'=>$addressEnglish,
			   'candidate_residence_addressh'=>$addressHindi,
			   'cand_fvname'=>$this->xssClean->clean_input(Check_Input($request->input('fvname'))),
				'candidate_residence_addressv'=>$this->xssClean->clean_input(Check_Input($request->input('addressv'))),
			   'candidate_residence_stcode'=>$this->xssClean->clean_input(Check_Input($request->input('state'))),
			   'candidate_residence_districtno'=>$this->xssClean->clean_input(Check_Input($request->input('district'))),
			   'candidate_residence_acno'=>$this->xssClean->clean_input(Check_Input($request->input('ac'))),
			   'cand_category'=>$this->xssClean->clean_input(Check_Input($request->input('cand_category'))),
			   'cand_age'=>$age,
			   'cand_panno'=>$this->xssClean->clean_input(Check_Input($request->input('panno'))),
			   //'cand_dob'=>date('Y-m-d', strtotime($request->input('dob'))),
			   'updated_by'=>$d->officername,
			   'updated_at'=>date('Y-m-d h:i:s'),
			   'added_update_at'=>date('Y-m-d'),
	   
   );
	  $n = DB::table('candidate_personal_detail')->where('candidate_id', $nom->candidate_id)->update($candPersonalData);
   
   if($nom->candidate_id != ''){
	 $candNomData = array(
		
	   'party_id'=>$request->input('party_id'),
	   'symbol_id'=>$request->input('symbol_id'),
	   'added_update_at'=>date('Y-m-d'), 
	   'updated_by'=>$d->officername,
	   'updated_at'=>date('Y-m-d'),
	   'cand_party_type'=> $partytype
	 );
				 
				 if(!empty($request->file('profileimg'))){
					 $file = $request->file('profileimg');
					 $cyear = date('Y');
					 $newname=trim(substr($candName,0,5));
					 $newfile =$newname.'-'.$cyear.'-'.date('Ymdhis');
					 $fileNewName =$newfile.'.'.$request->file('profileimg')->getClientOriginalExtension();
					 $destinationPath = 'uploads1/candprofile/E'.$ele_details->ELECTION_ID.'/'.$cyear.'/'.$electionType.'/'.$stcode.'/';
					 $file->move($destinationPath,$fileNewName);
				   
					 $candImage = $destinationPath.$fileNewName ;
				 } else{
					 $candImage = $candimg;
				 }
		 \App\models\Candidate\CandidateLogModel::clone_record($nomid);
		 
				 $n = DB::table('candidate_nomination_detail')->where('candidate_id', $nom->candidate_id)->where('nom_id',$nomid)->update($candNomData);

				 $updateCandData = DB::update('update candidate_personal_detail set cand_image = ? where candidate_id = ?',[$candImage,$nom->candidate_id]);
			  
				 \Session::flash('success_mes', 'Candidate profile has been successfully Updated');
				 return Redirect::to('/roac/listnomination');
			 }
		 }
		 else{
			 return redirect('/officer-login');
		 }
   }
}
