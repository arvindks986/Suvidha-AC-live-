<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB,Auth;

class ElectionModel extends Model
{
    protected $table = 'm_election_details';

    public static function get_current_election(){

    	$object = ElectionModel::where('ST_CODE', Auth::user()->st_code)->where('CONST_NO', Auth::user()->ac_no)->where('CONST_TYPE', 'AC')->where('CURRENTELECTION','Y')->first();
    	if(!$object){
    		return false;
    	}
    	return $object->toArray();

    }

    public static function get_current_elections(){

    	$results = [];
    	$object = ElectionModel::where('ST_CODE', Auth::user()->st_code)->where('CONST_TYPE', 'AC')->where('CURRENTELECTION','Y')->groupBy('ELECTION_ID')->groupBy('ELECTION_TYPE')->groupBy('YEAR')->orderByRaw("YEAR DESC, ELECTION_TYPE ASC")->get()->toArray();
    	foreach ($object as $result) {
    		$results[] = $result;
    	}
    	return $results;

    }

    public static function get_all_election(){

        $results = [];
        $object = ElectionModel::where('CONST_TYPE', 'AC')->where('CURRENTELECTION','Y')->groupBy('ELECTION_ID')->groupBy('ELECTION_TYPE')->groupBy('YEAR')->orderByRaw("YEAR DESC, ELECTION_TYPE ASC")->get()->toArray();
        foreach ($object as $result) {
            $results[] = $result;
        }
        return $results;

    }
	
}