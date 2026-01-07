<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\SmsgatewayHelper;
class PollingStationOfficerModel extends Model
{
  protected $table = 'polling_station_officer';

  protected $fillable = ['id', 'name', 'mobile_number', 'email', 'designation', 'role_id', 'role_level', 'lattitude', 'longitude', 'st_code', 'district_no', 'ac_no', 'ps_no', 'address', 'alloted_location', 'pin', 'otp', 'otp_attempt', 'otp_time', 'device_id', 'ip_address', 'session_id', 'api_token', 'is_login', 'login_time', 'logout_time', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by', 'role_type', 'pro_override', 'is_testing', 'location_id', 'parent_sm_id', 'election_id', 'election_typeid', 'is_import','sector_id'];

  public static function validate_ps($filter = array()){

    $sql = PollingStationOfficerModel::select('*')
          ->where('role_id', $filter['role_id'])
          ->where('sector_id', $filter['sector_no'])
          ->get();
    return $sql;
  }


  public static function total_officer_count($filter = array()){

    $sql = PollingStationOfficerModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_officer.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_officer.ac_no"],
    ])->join('boothapp_enable_acs', [
      ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
      ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
    ])->select('id');

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('polling_station_officer.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('polling_station_officer.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('polling_station_officer.role_id',$filter['role_id']);
    }

    if(!empty($data['mobile'])){
      $sql->where('polling_station_officer.mobile_number',$data['mobile']);
    }

    if(!empty($filter['is_activated'])){
      $sql->whereNotNull('polling_station_officer.login_time');
    }

    if(!empty($filter['is_not_activated'])){
      $sql->whereNull('polling_station_officer.login_time');
    }

    return $sql->count("polling_station_officer.id");

  }

	public static function get_officers_new($data = array()){

		$sql = PollingStationOfficerModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_officer.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_officer.ac_no"],
    ])
    ->join("m_sector_master as msm", "msm.sector_id", "=", "polling_station_officer.sector_id")
    ->selectRaw("msm.sector_id, msm.sector_name, id, name, mobile_number, email, designation, role_id, polling_station_officer.st_code, polling_station_officer.district_no, polling_station_officer.ac_no, polling_station_officer.ps_no, pin, otp, otp_attempt, otp_time, device_id, session_id, api_token, is_login, login_time, logout_time, is_active, role_level, pro_override, is_testing, location_id");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station_officer.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($data['st_code'])){
      $sql->where('polling_station_officer.st_code',$data['st_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$data['ac_no']);
    }

    if(!empty($data['sector_id'])){
      $sql->where('polling_station_officer.sector_id',$data['sector_id']);
    }

    if(!empty($data['ps_no'])){
     // $sql->where('polling_station_officer.ps_no',$data['ps_no']);
      $sql->whereRaw("find_in_set('".$data['ps_no']."',polling_station_officer.ps_no)");
    }

    if(!empty($data['is_activated'])){
      if($data['is_activated']=='yes'){
        $sql->whereNotNull('polling_station_officer.login_time');
      }else if($data['is_activated']=='no'){
        $sql->whereNull('polling_station_officer.login_time');
      }
    }

    if(!empty($data['mobile'])){
      $sql->where('polling_station_officer.mobile_number',$data['mobile']);
    }

    if(!empty($data['role_id'])){
      $sql->where('polling_station_officer.role_id',$data['role_id']);
    }

    if(!empty($data['role_level'])){
      $sql->where('polling_station_officer.role_level',$data['role_level']);
    }

    if(array_key_exists("parent_sm_id",$data)){
     $sql->where('polling_station_officer.parent_sm_id',$data['parent_sm_id']);
    }

    if(!empty($data['not_po'])){
      $sql->where('polling_station_officer.role_id','!=','34');
    }

    $sql->orderByRaw("polling_station_officer.st_code, polling_station_officer.ac_no, polling_station_officer.ps_no ASC");

    if(!empty($data['limit'])){
      $sql->limit($data['limit']);
    }

    if(!empty($data['paginate'])){
        return $sql->paginate(10);
    }else{
        return $sql->get()->toArray();
    }
	}


  public static function get_officers($data = array()){

		$sql = PollingStationOfficerModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_officer.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_officer.ac_no"],
    ])->selectRaw("id, name, mobile_number, email, designation, role_id, polling_station_officer.st_code, polling_station_officer.district_no, polling_station_officer.ac_no, polling_station_officer.ps_no, pin, otp, otp_attempt, otp_time, device_id, session_id, api_token, is_login, login_time, logout_time, is_active, role_level, pro_override, is_testing, location_id");

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($data['st_code'])){
      $sql->where('polling_station_officer.st_code',$data['st_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('polling_station_officer.ps_no',$data['ps_no']);
    }

    if(!empty($data['is_activated'])){
      if($data['is_activated']=='yes'){
        $sql->whereNotNull('polling_station_officer.login_time');
      }else if($data['is_activated']=='no'){
        $sql->whereNull('polling_station_officer.login_time');
      }
    }

    if(!empty($data['mobile'])){
      $sql->where('polling_station_officer.mobile_number',$data['mobile']);
    }

    if(!empty($data['role_id'])){
      $sql->where('polling_station_officer.role_id',$data['role_id']);
    }

    if(array_key_exists("parent_sm_id",$data)){
     $sql->where('polling_station_officer.parent_sm_id',$data['parent_sm_id']);
    }

    if(!empty($data['not_po'])){
      $sql->where('polling_station_officer.role_id','!=','34');
    }

    $sql->orderByRaw("polling_station_officer.st_code, polling_station_officer.ac_no, polling_station_officer.ps_no ASC");

    if(!empty($data['limit'])){
      $sql->limit($data['limit']);
    }

    if(!empty($data['paginate'])){
        return $sql->paginate(10);
    }else{
        return $sql->get()->toArray();
    }
	}

  public static function get_officer($data = array()){
    $sql = PollingStationOfficerModel::selectRaw("id, name, mobile_number, email, designation, role_id, st_code, district_no, ac_no, ps_no, pin, otp, otp_attempt, otp_time, device_id, session_id, api_token, is_login, login_time, logout_time, is_active, role_level, pro_override, is_testing");


    if(!empty($data['st_code'])){
      $sql->where('st_code',$data['st_code']);
    }

    if(!empty($data['id'])){
      $sql->where('id',$data['id']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('ac_no',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('ps_no',$data['ps_no']);
    }

    if(!empty($data['id'])){
      $sql->where('id',$data['id']);
    }

    if(!empty($data['mobile'])){
      $sql->where('mobile_number',$data['mobile']);
    }

    $query = $sql->first();

    if(!$query){
      return false;
    }
    return $query->toArray();
  }

  public static function add_officer($data = array()){

    if(!empty($data['id'])){
      $officer = PollingStationOfficerModel::find(decrypt_string($data['id']));
      $sms_message = "Your number has been deregistered for Booth App as a PO for Polling station no. ".$officer->ps_no;
	  $msgstatus = SmsgatewayHelper::gupshup($officer->mobile_number, $sms_message);
      $officer->updated_by  = \Auth::id();
	  $officer->api_token   = '';

    //for removing data
    try{
    $delete_record =\DB::connection('booth_revamp_test_write')->table('polling_station_officer')
				->where('st_code', $data['st_code'])
				->where('ps_no', $data['ps_no'])
        ->where('ac_no', $data['ac_no'])
        ->delete();
      }
      catch(\Exception $e){
        
      }

    }else{
      $officer = new PollingStationOfficerModel();
    }
    $officer->mobile_number = $data['mobile'];
    $officer->name = $data['name'];
    $officer->is_active = $data['status'];
    $officer->role_id = $data['role_id'];
    $officer->st_code = $data['st_code'];
    $officer->ac_no       = $data['ac_no'];
    $officer->district_no = $data['dist_no'];
    $officer->ps_no       = $data['ps_no'];
    //$officer->pin         = $data['pin'];
	$officer->new_otp = bcrypt('654321');
    $officer->role_level  = $data['role_level'];
    if(isset($data['is_pro_right']) && !empty($data['is_pro_right'])){
      $officer->pro_override  = (int)$data['is_pro_right'];
    }
    if(isset($data['is_testing']) && !empty($data['is_testing'])){
      $officer->is_testing = 1;
    }else{
      $officer->is_testing = 0;
    }
    if(isset($data['location_id']) && !empty($data['location_id'])){
      $officer->location_id = $data['location_id'];
    }else{
      $officer->location_id = 0;
    }
    $officer->created_by  = \Auth::id();
    return $officer->save();
  }

  public static function count_officer($data = array()){

    $sql = PollingStationOfficerModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_officer.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_officer.ac_no"],
    ]);

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }
    if(!empty($data['st_code'])){
    $sql->where('polling_station_officer.st_code',$data['st_code']);
  }

    if(!empty($data['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('polling_station_officer.ps_no',$data['ps_no']);
    }

    if(!empty($data['role_id'])){
      $sql->where('polling_station_officer.role_id',$data['role_id']);
    }

    if(!empty($data['mobile'])){
      $sql->where('polling_station_officer.mobile_number',$data['mobile']);
    }

    if(!empty($data['id'])){
      $sql->where('polling_station_officer.id','!=',decrypt_string($data['id']));
    }

    return $sql->count("polling_station_officer.id");

  }

  public static function update_otp($data = array()){
    $officer = PollingStationOfficerModel::where('mobile_number', $data['mobile'])->first();
    $officer->new_otp = bcrypt($data['otp']);
    $officer->updated_by  = \Auth::id();
    return $officer->save();
  }

  public static function get_assign_officer_count($filter = array()){
    $sql = PollingStationOfficerModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_officer.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_officer.ac_no"],
    ])->select("id");

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('polling_station_officer.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('polling_station_officer.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('polling_station_officer.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('polling_station_officer.role_id',$filter['role_id']);
    }

    return $sql->count(\DB::RAW("DISTINCT polling_station_officer.st_code, polling_station_officer.ac_no, polling_station_officer.ps_no"));
  }

  //so
  public static function add_link_officer($data = array()){
    if(isset($data['id']) && !empty($data['id'])){
      $officer = PollingStationOfficerModel::find(decrypt_string($data['id']));
      $officer->updated_by  = \Auth::id();
    }else{
      $officer = new PollingStationOfficerModel();
    }
    $officer->mobile_number = $data['mobile'];
    $officer->name = $data['name'];
    $officer->is_active = $data['status'];
    $officer->role_id = $data['role_id'];
    $officer->st_code = $data['st_code'];
    $officer->ac_no       = $data['ac_no'];
    $officer->district_no = $data['dist_no'];
    $officer->ps_no       = $data['ps_no_string'];
    $officer->pin         = $data['pin'];
	$officer->new_otp = bcrypt('654321');
    $officer->role_level  = $data['role_level'];
    $officer->created_by  = \Auth::id();
    if(isset($data['parent_id']) && !empty($data['parent_id'])){
      $officer->parent_sm_id = decrypt_string($data['parent_id']);
    }
    if(isset($data['is_testing']) && !empty($data['is_testing'])){
      $officer->is_testing = 1;
    }else{
      $officer->is_testing = 0;
    }
    if($officer->save()){
      return $officer;
    }
    return false;
  }

  public static function add_link_officer_new($data = array()){
    if(isset($data['id']) && !empty($data['id'])){
      $officer = PollingStationOfficerModel::find(decrypt_string($data['id']));
      $officer->updated_by  = \Auth::id();
    }else{
      $officer = new PollingStationOfficerModel();
    }
    $officer->mobile_number = $data['mobile'];
    $officer->name = $data['name'];
    $officer->is_active = $data['status'];
    $officer->role_id = $data['role_id'];
    $officer->st_code = $data['st_code'];
    $officer->ac_no       = $data['ac_no'];
    $officer->district_no = $data['dist_no'];
   
    $officer->pin         = $data['pin'];
	$officer->new_otp = bcrypt('654321');
    $officer->role_level  = $data['role_level'];
    $officer->sector_id    =$data['sector_id'];
    $officer->created_by  = \Auth::id();
    if(isset($data['parent_id']) && !empty($data['parent_id'])){
      $officer->parent_sm_id = decrypt_string($data['parent_id']);
    }
    if(isset($data['is_testing']) && !empty($data['is_testing'])){
      $officer->is_testing = 1;
    }else{
      $officer->is_testing = 0;
    }
    if($officer->save()){
      return $officer;
    }
    return false;
  }

  

  public static function get_parent_sector($id){

    $object =  PollingStationOfficerModel::select('sector_id')->where('id',$id)->get();

    return $object->toArray();
  }

  public static function validate_same_ps($filter = array()){

    $sql = PollingStationOfficerModel::select('ps_no')
            ->where('st_code',$filter['st_code'])
            ->where('ac_no',$filter['ac_no'])
              ->get();
              return $sql;
  }



  public static function get_sub_so($officer_id){
    return PollingStationOfficerModel::where('parent_sm_id', $officer_id)->get();
  }

  public static function update_ps_only($id, $data = array()){
    $officer = PollingStationOfficerModel::find($id);
    $officer->ps_no       = $data['ps_no_string'];
    $officer->save();
  }

  //total officer count
  public static function total_officer_by_query($filter = array()){

    $data = [
      'total_blo' => 0,
      'total_po' => 0,
      'total_pro' => 0,
      'total_sm' => 0,
      'blo_activated' => 0,
      'po_activated' => 0,
      'pro_activated' => 0,
      'sm_activated' => 0,
    ];

    $sql = PollingStationOfficerModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_officer.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_officer.ac_no"],
    ])->selectRaw("COUNT(IF(role_id=33,1,NULL)) as total_blo, COUNT(DISTINCT IF(role_id=34 AND booth_app_excp = 0,CONCAT(polling_station_officer.st_code, polling_station_officer.ac_no, polling_station_officer.ps_no),NULL)) as total_po, COUNT(DISTINCT IF(role_id=35 AND booth_app_excp = 0,CONCAT(polling_station_officer.st_code, polling_station_officer.ac_no, polling_station_officer.ps_no),NULL)) as total_pro, COUNT(IF(role_id=38,1,NULL)) as total_sm,
    COUNT(IF(login_time IS NOT NULL AND role_id=33,1,NULL)) as blo_activated,
    COUNT(DISTINCT IF(login_time IS NOT NULL AND role_id=34, CONCAT(polling_station_officer.st_code, polling_station_officer.ac_no, polling_station_officer.ps_no), NULL)) as po_activated,
    COUNT(DISTINCT IF(login_time IS NOT NULL AND role_id=35, CONCAT(polling_station_officer.st_code, polling_station_officer.ac_no, polling_station_officer.ps_no),NULL)) as pro_activated,
    COUNT(IF(login_time IS NOT NULL AND role_id=38,1,NULL)) as sm_activated");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station_officer.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('polling_station_officer.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->whereRaw("find_in_set('".$filter['ps_no']."',polling_station_officer.ps_no)");
    }

    if(!empty($filter['role_id'])){
      $sql->where('polling_station_officer.role_id',$filter['role_id']);
    }

    if(!empty($data['mobile'])){
      $sql->where('polling_station_officer.mobile_number',$data['mobile']);
    }

    if(!empty($filter['is_activated'])){
      $sql->whereNotNull('polling_station_officer.login_time');
    }

    if(!empty($filter['is_not_activated'])){
      $sql->whereNull('polling_station_officer.login_time');
    }

    $result = $sql->first();
    if($result){
      $data = [
        'total_blo'         => $result->total_blo,
        'total_po'          => $result->total_po,
        'total_pro'         => $result->total_pro,
        'total_sm'          => $result->total_sm,
        'blo_activated' => $result->blo_activated,
        'po_activated'  => $result->po_activated,
        'pro_activated' => $result->pro_activated,
        'sm_activated'  => $result->sm_activated,
      ];
    }
    return $data;
  }

  //blo multiple
  public static function add_blo($data = array()){
    if(isset($data['id']) && !empty($data['id'])){
      $officer = PollingStationOfficerModel::find(decrypt_string($data['id']));
      $officer->updated_by  = \Auth::id();
    }else{
      $officer = new PollingStationOfficerModel();
    }
    $officer->mobile_number = $data['mobile'];
    $officer->name = $data['name'];
    $officer->is_active = $data['status'];
    $officer->role_id = 33;
    $officer->st_code = $data['st_code'];
    $officer->ac_no       = $data['ac_no'];
    $officer->district_no = $data['dist_no'];
    $officer->ps_no       = $data['ps_no_string'];
    $officer->pin         = $data['pin'];
    $officer->role_level  = $data['role_level'];
    $officer->created_by  = \Auth::id();
    if(isset($data['parent_id']) && !empty($data['parent_id'])){
      $officer->parent_sm_id = decrypt_string($data['parent_id']);
    }
    if(isset($data['is_testing']) && !empty($data['is_testing'])){
      $officer->is_testing = 1;
    }else{
      $officer->is_testing = 0;
    }
    if($officer->save()){
      return $officer;
    }
    return false;
  }

  public static function get_sub_blo($officer_id){
    return PollingStationOfficerModel::where('parent_sm_id', $officer_id)->get();
  }


  //total count officer
  public static function get_total_count($filter = array()){
    $total = [
      'total_sm' => 0,
      'total_blo' => 0,
      'total_po' => 0,
      'total_pro' => 0,
      'blo_not_activated' => 0,
      'po_not_activated' => 0,
      'pro_not_activated' => 0,
      'sm_not_activated' => 0,
    ];

    $sql = PollingStationOfficerModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_officer.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_officer.ac_no"],
    ])->join('boothapp_enable_acs', [
      ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
      ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
    ])->leftjoin("polling_station_location_to_ps as plps","plps.location_id","=","polling_station_officer.location_id")
    ->selectRaw('COUNT(DISTINCT IF(role_id = 38, CONCAT(polling_station_officer.st_code,polling_station_officer.ac_no,polling_station_officer.ps_no), NULL)) as total_sm, COUNT(DISTINCT IF(role_id = 33 AND polling_station_officer.location_id > 0, CONCAT(polling_station_officer.st_code,polling_station_officer.ac_no,plps.ps_no) , NULL)) as total_blo, COUNT(DISTINCT IF(role_id = 34, CONCAT(polling_station_officer.st_code,polling_station_officer.ac_no,polling_station_officer.ps_no) , NULL)) as total_po, COUNT(DISTINCT IF(role_id = 35, CONCAT(polling_station_officer.st_code,polling_station_officer.ac_no,polling_station_officer.ps_no) , NULL)) as total_pro, COUNT(DISTINCT IF(login_time IS NULL AND role_id=33, CONCAT(polling_station_officer.st_code,polling_station_officer.ac_no,plps.ps_no),NULL)) as blo_not_activated, COUNT(DISTINCT IF(login_time IS NULL AND role_id=34, CONCAT(polling_station_officer.st_code,polling_station_officer.ac_no,plps.ps_no),NULL)) as po_not_activated, COUNT(DISTINCT IF(login_time IS NULL AND role_id=35, CONCAT( polling_station_officer.st_code,polling_station_officer.ac_no,plps.ps_no),NULL)) as pro_not_activated, COUNT(DISTINCT IF(login_time IS NULL AND role_id=38, CONCAT(polling_station_officer.st_code,polling_station_officer.ac_no, polling_station_officer.ps_no),NULL)) as sm_not_activated');

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station_officer.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('polling_station_officer.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->whereRaw("find_in_set('".$filter['ps_no']."',polling_station_officer.ps_no)");
    }

    $query = $sql->first();
    if($query){
      $total = [
        'total_sm' => $query->total_sm,
        'total_blo' => $query->total_blo,
        'total_po' => $query->total_po,
        'total_pro' => $query->total_pro,
        'blo_not_activated' => $query->blo_not_activated,
        'po_not_activated' => $query->po_not_activated,
        'pro_not_activated' => $query->pro_not_activated,
        'sm_not_activated' => $query->sm_not_activated,
      ];
    }
    return $total;
  }

    public static function count_mobile($mobile){
    return PollingStationOfficerModel::where('mobile_number', $mobile)->count();
  }

  public static function add_exemted($data = array()){
    $officer    = PollingStationOfficerModel::where([
      'st_code' => $data['st_code'],
      'ac_no'   => $data['ac_no'],
      'ps_no'   => $data['ps_no'],
    ])->first();
    $officer->booth_app_excp = 1;
    if($officer->save()){
      return $officer;
    }
    return false;
  }
  
  
  public static function get_sos($filter = array()){
    $object =  PollingStationOfficerModel::join("sector_ac_ps_mapping as sapm","sapm.sector_id","=","polling_station_officer.sector_id")->where('sapm.st_code' , $filter['st_code'])->where('sapm.ac_no' , $filter['ac_no'])->where('sapm.ps_no', $filter['ps_no'])->get();
    return $object;
  }
  
  
  
  public static function get_parent_ps($id){
    $ps_no = [];
    $object =  PollingStationOfficerModel::join("sector_ac_ps_mapping as sapm","sapm.sector_id","=","polling_station_officer.sector_id")->select('sapm.ps_no')->where('polling_station_officer.id',$id)->get();
	
    foreach($object as $iterate_so){
      $ps_no[] = $iterate_so->ps_no;
    }
    return $ps_no;
  }

}
