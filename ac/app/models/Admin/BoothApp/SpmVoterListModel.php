<?php namespace App\models\Admin\BoothApp;

use Illuminate\Database\Eloquent\Model;
use DB;

class SpmVoterListModel extends Model
{
protected $table = 'voter_info';

protected $connection = 'spm';

public static function total_poll_station_count($filter = array()){

  $sql = SpmVoterListModel::selectRaw('id');

  if(!empty($filter['st_code'])){
    $sql->where('voter_info.state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('voter_info.ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('voter_info.ps_no',$filter['ps_no']);
  }

  $sql->where('row_status','A');

  return $sql->count(DB::raw('DISTINCT ps_no'));

}


public static function get_vooter_list($data = array()){

	$sql = SpmVoterListModel::selectRaw('epic_no, name_en, gender, voter_serial_no, unique_generated_id, id');

      if(!empty($data['st_code'])){
         $sql->where('voter_info.state_code',$data['st_code']);
      }

      if(!empty($data['ac_no'])){
        $sql->where('voter_info.ac_no',$data['ac_no']);
      }

      if(!empty($data['ps_no'])){
        $sql->where('voter_info.ps_no',$data['ps_no']);
      }

  $sql->where('row_status','A');

      $sql->orderByRaw("voter_info.state_code ASC")->groupBy('unique_generated_id');

    if(!empty($data['paginate'])){
      return $sql->paginate(100);
  }else{
      return $sql->get();
  }

 
}

  
  public static function get_polling_stations($data = array()){


    $sql = SpmVoterListModel::selectRaw('epic_no, name_en, gender, voter_serial_no, unique_generated_id, ps_no, ps_name_en, id');

    if(!empty($data['st_code'])){
     $sql->where('voter_info.state_code',$data['st_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('voter_info.ac_no',$data['ac_no']);
    }

    $sql->where('row_status','A');

    $sql->orderByRaw("state_code, ac_no, ps_no ASC")->groupBy('ps_no')->groupBy('state_code')->groupBy('ac_no');
    if(!empty($data['paginate'])){
      return $sql->paginate(100);
    }else{
      return $sql->get();
    }

  }

  public static function get_polling_station($data = array()){

    $sql = SpmVoterListModel::select('*');

      if(!empty($data['st_code'])){
       $sql->where('voter_info.state_code',$data['st_code']);
     }

     $sql->where('row_status','A');

     if(!empty($data['ac_no'])){
      $sql->where('voter_info.ac_no',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('voter_info.ps_no',$data['ps_no']);
    }

    $query = $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();
    

  }

  public static function is_seal_encrypted($data = array()){

    $sql = SpmVoterListModel::where('voter_info.state_code',$data['st_code']);
    $sql->where('voter_info.ac_no',$data['ac_no']);
    $sql->where('voter_info.ps_no',$data['ps_no']);
    $sql->where('voter_info.bar_code','!=','');
    $sql->where('row_status','A');
    return $sql->count();

  }

  public static function get_elector_count($filter = array()){

    //$sql = SpmVoterListModel::selectRaw("count(case when gender='M' then 1 end) as male, count(case when gender='F' then 1 end) as female, count(case when gender='O' then 1 end) as other, count(gender) as total, ps_no, ac_no, state_code as st_code");

     $sql = SpmVoterListModel::select("ps_no, ac_no, state_code as st_code");

    if(!empty($filter['st_code'])){
      $sql->where('state_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereNull('gender');
      }else{
        $sql->where('gender',$filter['gender']);
      }
    }

    $sql->where('row_status','A');

    return $sql->count();
  
  }

  


}