<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session, Response;
use App\commonModel;  
use App\models\Admin\BoothAppRevamp\{PollingStationOfficerModel, PsSectorOfficer};
use App\Classes\xssClean;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;

//current

class FixedController extends Controller {

	public function index(Request $request){
		$officers = PsSectorOfficer::join("polling_station_officer as ps_officer","ps_officer.id","=","ps_sector_officer.ps_officer_id")->groupBy("ps_sector_officer.ps_officer_id")->where('ps_sector_officer.is_deleted', 0)->where('ps_officer.role_id', 38)->selectRaw("GROUP_CONCAT(ps_sector_officer.ps_no) as ps_string, ps_officer.id")->orderByRaw("ps_sector_officer.st_code, ps_sector_officer.ac_no, CONVERT(ps_sector_officer.ps_no, INT) ASC")->get();
		$i = 0;
		foreach ($officers as $sector_officer) {
			PollingStationOfficerModel::where('id', $sector_officer['id'])->update([
				'ps_no' => $sector_officer['ps_string']
			]);
			$i++;
		}
		echo $i;
	}

}  // end class