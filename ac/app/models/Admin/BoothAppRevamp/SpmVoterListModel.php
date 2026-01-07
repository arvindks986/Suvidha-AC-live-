<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;

class SpmVoterListModel extends Model
{
protected $table = 'voter_info';

protected $connection = 'booth_revamp';

public static function total_poll_station_count($filter = array()){

  $sql = SpmVoterListModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","voter_info.st_code"],
      ["m_election_details.CONST_NO","=","voter_info.ac_no"],
    ])->selectRaw('id');

  $sql->where('CONST_TYPE','AC');

  if(!empty($filter['phase_no'])){
    $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
  }

  if(!empty($filter['st_code'])){
    $sql->where('voter_info.state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('voter_info.ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('voter_info.ps_no',$filter['ps_no']);
  }

  $sql->where('voter_info.row_status','A');

  return $sql->count(DB::raw('DISTINCT voter_info.ps_no'));

}


public static function get_vooter_list($data = array()){

  	$sql = SpmVoterListModel::join("m_election_details",[
        ["m_election_details.ST_CODE","=","voter_info.st_code"],
        ["m_election_details.CONST_NO","=","voter_info.ac_no"],
      ])->selectRaw('epic_no, name_en, gender, voter_serial_no, unique_generated_id, id');

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

      if(!empty($data['st_code'])){
         $sql->where('voter_info.state_code',$data['st_code']);
      }

      if(!empty($data['ac_no'])){
        $sql->where('voter_info.ac_no',$data['ac_no']);
      }

      if(!empty($data['ps_no'])){
        $sql->where('voter_info.ps_no',$data['ps_no']);
      }

      $sql->where('voter_info.row_status','A');

      $sql->orderByRaw("voter_info.state_code ASC")->groupBy('voter_info.unique_generated_id');

    if(!empty($data['paginate'])){
      return $sql->paginate(100);
    }else{
        return $sql->get();
    }

 
}

  
  public static function get_polling_stations($data = array()){


    $sql = SpmVoterListModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","voter_info.st_code"],
      ["m_election_details.CONST_NO","=","voter_info.ac_no"],
    ])->selectRaw('voter_info.epic_no, voter_info.name_en, voter_info.gender, voter_info.voter_serial_no, voter_info.unique_generated_id, voter_info.ps_no, voter_info.ps_name_en, voter_info.id');

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($data['st_code'])){
     $sql->where('voter_info.state_code',$data['st_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('voter_info.ac_no',$data['ac_no']);
    }

    $sql->where('voter_info.row_status','A');

    $sql->orderByRaw("voter_info.state_code, voter_info.ac_no, voter_info.ps_no ASC")->groupBy('voter_info.ps_no')->groupBy('voter_info.state_code')->groupBy('ac_no');
    if(!empty($data['paginate'])){
      return $sql->paginate(100);
    }else{
      return $sql->get();
    }

  }

  public static function get_polling_station($data = array()){

    $sql = SpmVoterListModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","voter_info.st_code"],
      ["m_election_details.CONST_NO","=","voter_info.ac_no"],
    ])->select('*');

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

      if(!empty($data['st_code'])){
       $sql->where('voter_info.state_code',$data['st_code']);
     }

     $sql->where('voter_info.row_status','A');

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

     $sql = SpmVoterListModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","voter_info.st_code"],
      ["m_election_details.CONST_NO","=","voter_info.ac_no"],
    ])->select("voter_info.ps_no, voter_info.ac_no, voter_info.state_code as st_code");

     $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }
    
    if(!empty($filter['st_code'])){
      $sql->where('voter_info.state_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('voter_info.voter_info.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('voter_info.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereNull('voter_info.gender');
      }else{
        $sql->where('voter_info.gender',$filter['gender']);
      }
    }

    $sql->where('voter_info.row_status','A');

    return $sql->count();
  
  }

  


}