<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class ElectorsVerificationByPsModel extends Model
{
  protected $table = 'booth_app_elector_verify';

  protected $fillable = ['st_code', 'dist_no', 'ac_no', 'ps_no', 'is_verify'];

  public static function add_record($data = array()){
    $officer    = ElectorsVerificationByPsModel::firstOrNew([
      'st_code'     => $data['st_code'],
      'ac_no'       => $data['ac_no'],
      'dist_no'     => $data['dist_no'],
      'ps_no'       => $data['ps_no'],
    ]);
    $officer->is_verify  = $data['is_verify'];
    if($officer->save()){
      return $officer;
    }
    return false;
  }

  public static function total_count_record($filter = array()){
    return ElectorsVerificationByPsModel::where([
      'st_code' => $filter['st_code'],
      'ac_no' => $filter['ac_no'],
      'ps_no' => $filter['ps_no'],
      'is_verify' => $filter['is_verify'],
    ])->count();
  }

}