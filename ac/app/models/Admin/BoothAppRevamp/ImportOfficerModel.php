<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ImportOfficerModel extends Model
{
  protected $table = 'polling_station_officer_import';

  public $fillable = ['st_code', 'district_no', 'ac_no', 'ps_no', 'name1', 'mobile1', 'name2', 'mobile2', 'role_id', 'batch', 'is_deleted'];

  public static function add_officer($data = array()){

    $officer = new ImportOfficerModel();
    $officer->st_code = $data['st_code'];
    $officer->ac_no       = $data['ac_no'];
    $officer->district_no = $data['district_no'];
    $officer->ps_no   = $data['ps_no'];
    $officer->role_id = $data['role_id'];

    if(!empty($data['mobile1'])){
      $officer->name1       = $data['name1'];
      $officer->mobile1     = $data['mobile1'];
    }

    if(!empty($data['mobile2'])){
      $officer->name2       = $data['name2'];
      $officer->mobile2     = $data['mobile2'];
    }
    $officer->batch     = $data['batch'];
    return $officer->save();

  }

  public static function deactivate_previous($data = array()){
    ImportOfficerModel::where($data)->update(['is_deleted' => 1]);
  }

 

}