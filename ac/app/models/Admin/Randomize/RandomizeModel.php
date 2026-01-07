<?php namespace App\models\Admin\Randomize;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RandomizeModel extends Model
{
    protected $table = 'randomization_details';

    public $fillable = ['st_code','ac_no'];

    public static function get_randomization($filter = array()){
        $sql = RandomizeModel::where('ac_no',$filter['ac_no'])->where('st_code',$filter['st_code'])->first();
        if(!$sql){
          return false;
        }
        return $sql;
    }

    public static function add_or_update($filter = array(), $data = array()){
         $sql = RandomizeModel::firstOrNew([
            'st_code' => $filter['st_code'],
            'ac_no' => $filter['ac_no'],
         ]);
        $sql->randomize_date    = date('Y-m-d',strtotime($data['randomize_date']));
        $sql->randomize_time    = $data['randomize_time'];
        $sql->dispatched_date   = date('Y-m-d',strtotime($data['dispatched_date']));
        $sql->dispatched_time   = $data['dispatched_time'];
        return $sql->save();
    }

}