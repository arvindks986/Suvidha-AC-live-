<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class ElectionModel extends Model
{
    protected $table = 'm_election_details';
	
	public static function get_election_types($filter = array()){
        $sql = ElectionModel::where('election_status','1')->where('CONST_TYPE','AC')->groupBy('ELECTION_TYPEID')->groupBy('ELECTION_ID');
        return $sql->get();
    }

}