<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;

class SectorAcPsMappingModel extends Model
{
  protected $table = 'sector_ac_ps_mapping';

  public static function getPsNumberSectorWise($id, $st_code){
    $sql = DB::table('sector_ac_ps_mapping')->select('st_code','dist_no','ac_no',
            DB::raw('group_concat(ps_no) as ps_no'),
            'sector_id')
            ->where('st_code', $st_code)
            ->where('sector_id', $id)
            ->groupBy('sector_id')
            ->get()->toArray();

            return $sql;
  }




}
