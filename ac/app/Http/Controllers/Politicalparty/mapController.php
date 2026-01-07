<?php

namespace App\Http\Controllers\politicalparty;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use App\commonModel;
use App\models\permissionModel;
use App\Http\helpers;
use App\models\{States, Districts, AC};

class mapController extends Controller
{
    public function mapindex(request $request)
    {
		$userId  = $request->session()->get('userID');
		$permissiondata = DB::table('permission_type')->get();
		$locationdata = DB::table('location_master')->get();
		//return view('politicalparty.createpermission');
	    return view('politicalparty.createpermission',['locationdata'=>$locationdata,'permissiondata'=>$permissiondata,'userid'=>$userId,'getStates' => States::all(),'getDistricts' => Districts::all(),'getAclist' => AC::all()]);
	}
   public function getDistricts(Request $request)
    { 
	
		$state = $request->input('stcode');
        $getDistricts = DB::table('m_district')->where('ST_CODE',$state)->get();
    	return $getDistricts;
    }
	public function getACList(Request $request)
    {
		$state = $request->input('stcode');
		$district = $request->input('district');
        $getACList = DB::table('m_ac')->where('ST_CODE',$state)  
		->where('DIST_NO_HDQTR', '=', $district)
		->get();
    	return json_encode($getACList);
    }
	
		public function getlocationList(Request $request)
        {
        $state = $request->input('stcode');
		$ac = $request->input('ac'); 
        $getACLists = DB::table('location_master')->where('ST_CODE',$state)  
		->where('AC_NO', '=', $ac)
		->get(); 
		return json_encode($getACLists);
	    }
		
		public function getlatlongs(Request $request)
        { 
        $locationid = $request->input('locationid');
		$locationdetails = DB::table('location_master')->where('id',$locationid)  
		->get(); 
		return json_encode($locationdetails);
	    }
		
		/* public function savecalendarvalue(Request $request)
        {
		 //echo "hello";  
		 print_r($_REQUEST);
		 exit;
	     $dateval = $request->input('dateval');
		 $datevalues = explode('-',$dateval);
		 echo $startdate = $datevalues[0];
		 echo $enddate = $datevalues[1];
		 print_r($datevalues);
		 //exit;
		 $userid = $request->input('userid'); 
		 echo $dbqry = DB::table('permission_request')
             ->where('id', $userid)
             ->update(['date_time_start' => $startdate,'date_time_end' => $enddate]);
			 
		 dd($dbqry);
		 exit;
        } */
		
		   public function saveprofile(Request $request)
        {
			 print_r($_REQUEST);  exit;
		 $usersId  = $request->session()->get('userID');
		 $permissiondata = DB::table('permission_type')->get();
		 $locationdata = DB::table('location_master')->get();
		 $permission = new permission();
		 $permission->user_id = $request['userId'];
		 $permission->st_code = $request['state'];
		 $permission->dist_no = $request['district'];
		 $permission->ac_no = $request['ac'];
		 $permission->permission_type_id = $request['permission'];
		 $permission->location_id = $request['location'];
		 $permission->longitude = $request['longitude'];
		 $permission->latitude = $request['latitude'];
		 if($request['latitude'])
		 {
		 $permission->Other_location = $request['other'];
		 }
		 else
		 {
		  $permission->Other_location = ''; 
		 }
		 $permission->save();
		
		 return view('politicalparty.createpermission',['locationdata'=>$locationdata,'permissiondata'=>$permissiondata,'userid'=>$usersId,'getStates' => States::all(),'getDistricts' => Districts::all(),'getAclist' => AC::all()]);
	    }
}
 