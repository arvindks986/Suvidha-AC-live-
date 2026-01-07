<?php

namespace App\models\Admin\Nomination\TreasuryAccount;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class TreasuryAccount extends Model
{
    protected $table = 'bihar_district_mapping';
    protected $guarded  = [];

	public static function getalldetail($filter=array()){
		$filter_arr = [
			'st_code' => Auth::user()->st_code
		];
		$query_data = TreasuryAccount::orderByRaw('LENGTH(dist_code_nomination)', 'ASC')
		->where($filter_arr)
		->orderBy('dist_code_nomination')
		->get()->toArray();
		return $query_data;
	}
}