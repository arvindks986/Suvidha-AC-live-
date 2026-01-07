<?php namespace App\models\Admin\BoothApp;

use Illuminate\Database\Eloquent\Model;

class PollingStation extends Model
{
  protected $table = 'polling_station as ps';

  public static function get_polling_stations($data = array()){

    $sql = PollingStation::select('*');

    if(!empty($data['st_code'])){
     $sql->where('ps.ST_CODE',$data['st_code']);
   }

    if(!empty($data['ac_no'])){
      $sql->where('ps.AC_NO',$data['ac_no']);
    }

  if(!empty($data['restricted_ps'])){
    $sql->whereIn('ps.PS_NO',$data['restricted_ps']);
  }

  $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");

  if(!empty($data['paginate'])){
    return $sql->paginate(100);
  }else{
    return $sql->get();
  }

}

  public static function get_polling_station($data = array()){

    $sql = PollingStation::select('*');

      if(!empty($data['st_code'])){
       $sql->where('ps.ST_CODE',$data['st_code']);
     }

     if(!empty($data['ac_no'])){
      $sql->where('ps.AC_NO',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('ps.PS_NO',$data['ps_no']);
    }

    if(!empty($data['restricted_ps'])){
      $sql->whereIn('ps.PS_NO',$data['restricted_ps']);
    }

    $query = $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();
    

  }

  

}