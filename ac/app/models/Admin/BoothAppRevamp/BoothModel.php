<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class BoothModel extends Model
{
    protected $table = 'polling_station_officer as booth';

	public static function get_booths($data = array()){

		$sql = BoothModel::select('*');

        if(!empty($data['st_code'])){
           $sql->where('booth.st_code',$data['st_code']);
        }

        if(!empty($data['ac_no'])){
          $sql->where('booth.AC_NO',$data['ac_no']);
        }

        if(!empty($data['ps_no'])){
          $sql->where('booth.ps_no',$data['ps_no']);
        }

        $sql->orderByRaw("booth.st_code, booth.ac_no, booth.ps_no ASC");

        return $sql->get()->toArray();
   
	}

}