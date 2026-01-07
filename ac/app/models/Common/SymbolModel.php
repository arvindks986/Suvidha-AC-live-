<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class SymbolModel extends Model
{
  
  protected $table = 'm_symbol';

	public static function get_symbols($data = array()){
		$results = SymbolModel::where('SYMBOL_NO','!=','-1')->get();
    	return $results;
	}

}