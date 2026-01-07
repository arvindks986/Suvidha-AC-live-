<?php namespace App\models\Admin\BoothApp;

use Illuminate\Database\Eloquent\Model;

class PollingStationOfficerModel extends Model
{
  protected $table = 'polling_station_officer';

  public static function total_officer_count($filter = array()){

    $sql = PollingStationOfficerModel::select('id');

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('role_id',$filter['role_id']);
    }

    if(!empty($filter['is_activated'])){
      $sql->whereNotNull('login_time');
    }

    if(!empty($filter['is_not_activated'])){
      $sql->whereNull('login_time');
    }

    return $sql->count();
   
  }

	public static function get_officers($data = array()){

		$sql = PollingStationOfficerModel::select('*');

    if(!empty($data['st_code'])){
      $sql->where('st_code',$data['st_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('ac_no',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('ps_no',$data['ps_no']);
    }

    if(!empty($data['is_activated'])){
      if($data['is_activated']=='yes'){
        $sql->whereNotNull('login_time');
      }else if($data['is_activated']=='no'){
        $sql->whereNull('login_time');
      }
    }

    if(!empty($data['role_id'])){
      $sql->where('role_id',$data['role_id']);
    }

    if(!empty($data['not_po'])){
      $sql->where('role_id','!=','34');
    }

    $sql->orderByRaw("st_code, ac_no, ps_no ASC");

    if(!empty($data['paginate'])){
        return $sql->paginate(100);
    }else{
        return $sql->get()->toArray();
    }   
	}

  public static function get_officer($data = array()){
    $sql = PollingStationOfficerModel::select('*');

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

    $query = $sql->first();

    if(!$query){
      return false;
    }
    return $query->toArray();
  }

  public static function add_officer($data = array()){
    if(!empty($data['id'])){
      $officer = PollingStationOfficerModel::find(decrypt_string($data['id']));
    }else{
      $officer = new PollingStationOfficerModel();
    }
    $officer->mobile_number = $data['mobile'];
    $officer->name = $data['name'];
    $officer->is_active = $data['status'];
    $officer->role_id = $data['role_id'];
    $officer->st_code = $data['st_code'];
    $officer->ac_no = $data['ac_no'];
    $officer->ps_no = $data['ps_no'];
    $officer->pin = $data['pin'];
    return $officer->save();
  }

  public static function count_officer($data = array()){

    $sql = PollingStationOfficerModel::where('st_code',$data['st_code']);

    if(!empty($data['ac_no'])){
      $sql->where('ac_no',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('ps_no',$data['ps_no']);
    }

    if(!empty($data['role_id'])){
      $sql->where('role_id',$data['role_id']);
    }

    if(!empty($data['id'])){
      $sql->where('id','!=',decrypt_string($data['id']));
    }

    return $sql->count();

  }

}