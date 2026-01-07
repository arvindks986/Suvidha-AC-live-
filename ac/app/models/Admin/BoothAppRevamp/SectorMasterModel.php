<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class SectorMasterModel extends Model
{

  protected $table = 'm_sector_master';

  public static function getsector(){

    $sql = SectorMasterModel::select('*')->get()->toArray();

    return $sql;
  }





}
