<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class PsSectorOfficer extends Model
{
  protected $table = 'ps_sector_officer';

  protected $fillable = ['ps_officer_id','is_deleted'];

  public static function add_link_officer($data = array()){
    $officer = new PsSectorOfficer();
    $officer->st_code       = $data['st_code'];
    $officer->ac_no         = $data['ac_no'];
    $officer->ps_no         = $data['ps_no'];
    $officer->ps_officer_id = $data['ps_officer_id'];
    $officer->created_by    = $data['created_by'];
    return $officer->save();
  }

  public static function delete_so($id){
    PsSectorOfficer::where('ps_officer_id',$id)->update(['is_deleted' => 1]);
  }

  public static function delete_link_officer($id){
    PsSectorOfficer::where('ps_officer_id',$id)->update(['is_deleted' => 1]);
  }

  public static function get_so($id){
    return PsSectorOfficer::where('ps_officer_id',$id)->update(['is_deleted' => 0]);
  }

  public static function get_parent_ps($id){
    $ps_no = [];
    $object =  PsSectorOfficer::select('ps_no')->where('ps_officer_id',$id)->where(['is_deleted' => 0])->get();
    foreach($object as $iterate_so){
      $ps_no[] = $iterate_so->ps_no;
    }
    return $ps_no;
  }

  public static function validate_ps($filter = array()){

    $sql = PsSectorOfficer::join("polling_station_officer as ps_officer","ps_officer.id","=","ps_sector_officer.ps_officer_id")->select('ps_sector_officer.ps_no')->where('ps_sector_officer.st_code',$filter['st_code'])->where('ps_sector_officer.ac_no',$filter['ac_no'])->whereIn('ps_sector_officer.ps_no',$filter['ps_no'])->where('ps_sector_officer.is_deleted', 0)->where('ps_officer.parent_sm_id', 0);
    if(isset($filter['id']) && !empty($filter['id'])){
      $sql->where('ps_officer.id', "!=", decrypt_string($filter['id']));
    }
    if(isset($filter['role_id']) && !empty($filter['role_id'])){
      $sql->where('ps_officer.role_id', $filter['role_id']);
    }
    return $sql->get();

  }

  public static function validate_same_ps($filter = array()){

    $sql = PsSectorOfficer::join("polling_station_officer as ps_officer","ps_officer.id","=","ps_sector_officer.ps_officer_id")->select('ps_sector_officer.ps_no')->where('ps_sector_officer.st_code',$filter['st_code'])->where('ps_sector_officer.ac_no',$filter['ac_no'])->where('ps_sector_officer.is_deleted', 0);
    foreach($filter['ps_no'] as $ps_no){
      $sql->where('ps_sector_officer.ps_no',$ps_no);
    }
    if(isset($filter['id']) && !empty($filter['id'])){
      $sql->where('ps_officer.id', "!=", decrypt_string($filter['id']));
    }
    return $sql->get();

  }

  public static function get_so_detail($filter = array()){
    $object =  PsSectorOfficer::join("polling_station_officer as ps_officer","ps_officer.id","=","ps_sector_officer.ps_officer_id")->where('ps_sector_officer.st_code' , $filter['st_code'])->where('ps_sector_officer.ac_no' , $filter['ac_no'])->where('ps_sector_officer.ps_no', $filter['ps_no'])->first();
    if(!$object){
      return false;
    }
    return $object;
  }

  public static function get_sos($filter = array()){
    $object =  PsSectorOfficer::join("polling_station_officer as ps_officer","ps_officer.id","=","ps_sector_officer.ps_officer_id")->where('ps_sector_officer.st_code' , $filter['st_code'])->where('ps_sector_officer.ac_no' , $filter['ac_no'])->where('ps_sector_officer.ps_no', $filter['ps_no'])->groupBy("ps_sector_officer.ps_officer_id")->get();
    return $object;
  }



}
