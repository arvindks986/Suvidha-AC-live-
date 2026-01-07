<?php namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use DB, Cache;
use App\models\Admin\BoothAppRevamp\{PollingStation, VoterInfoModel};

class JsonFile extends Model
{

  public static function generate_polling_station_file($filter = array()){

    $sql = PollingStation::selectRaw('ST_CODE as st_code, AC_NO as ac_no, PS_NO as ps_no, PART_NO as part_no, PS_NAME_EN as ps_name, slip_path');
    
    if(!empty($filter['st_code'])){
      $sql->where('ST_CODE',$filter['st_code']);
    }
    
    if(!empty($filter['allowed_st_code'])){
      $sql->whereIn('ST_CODE',$filter['allowed_st_code']);
    }
    
    if(!empty($filter['ac_no'])){
      $sql->where('AC_NO',$filter['ac_no']);
    }
    
    if(!empty($filter['ps_no'])){
      $sql->where('PS_NO',$filter['ps_no']);
    }

    if(!empty($filter['allowed_acs'])){
      $sql->whereIn('AC_NO',$filter['allowed_acs']);
    }

    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");
    
    $value =  $sql->get()->toArray();

    Common::generate_json_files($value, 'polling_station', $filter);

  }

  public static function get_polling_station_by_file($filter){
    $data = [];
    $file_name = Common::get_json_file_path('polling_station', $filter);
    try{
      $data = json_decode(file_get_contents($file_name),true);
    }catch(\Exception $e){
      
    }
    return $data;
  }

  public static function generate_electors_for_ps($filter = array()){
    $electors = [];
    $grand_male   = 0;
    $grand_female = 0;
    $grand_other  = 0;
    $grand_total  = 0;
    $filter_for_ps = $filter;
    $polling_stations = JsonFile::get_polling_station_by_file($filter);
    foreach ($polling_stations as $key => $iterate_p_s) {

      $filter_for_ps = array_merge($filter,['ps_no' => $iterate_p_s['ps_no']]);
      $ps_no    = $iterate_p_s['ps_no'];
      $e_male   = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'M']));
      $e_female = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'F']));
      $e_other  = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'O']));
      $e_total  = $e_male+$e_female+$e_other;

      $grand_male += $e_male;
      $grand_female += $e_female;
      $grand_other += $e_other;
      $grand_total += $e_total;

      $electors[] = [
        'ps_no'  => $ps_no,
        'male'   => $e_male,
        'female' => $e_female,
        'other'  => $e_other,
        'total'  => $e_total
      ];
      
    }
    Common::generate_json_files($electors, 'electors', $filter_for_ps);

    //ac electors generate
    $ac_electors = [
      'male'   => $grand_male,
      'female' => $grand_female,
      'other'  => $grand_other,
      'total'  => $grand_total
    ];
    Common::generate_json_files($ac_electors, 'electors', $filter);

  }

  public static function generate_electors_for_ac($filter = array()){
    $electors = [];
    $e_male   = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'M']));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'F']));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'O']));
    $e_total  = $e_male+$e_female+$e_other;
    $electors['male']   = $e_male;
    $electors['female'] = $e_female;
    $electors['other']  = $e_other;
    $electors['total']  = $e_total;
    Common::generate_json_files($electors, 'electors', $filter);
  }

  public static function generate_electors_for_district($filter = array()){
    $electors = [];
    $e_male   = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'M']));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'F']));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'O']));
    $e_total  = $e_male+$e_female+$e_other;
    $electors['male']   = $e_male;
    $electors['female'] = $e_female;
    $electors['other']  = $e_other;
    $electors['total']  = $e_total;
    Common::generate_json_files($electors, 'electors', $filter);
  }

  public static function generate_electors_for_state($filter = array()){
    $electors = [];
    $e_male   = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'M']));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'F']));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'O']));
    $e_total  = $e_male+$e_female+$e_other;
    $electors['male']   = $e_male;
    $electors['female'] = $e_female;
    $electors['other']  = $e_other;
    $electors['total']  = $e_total;
    Common::generate_json_files($electors, 'electors', $filter);
  }

}