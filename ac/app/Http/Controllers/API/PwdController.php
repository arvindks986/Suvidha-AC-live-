<?php
	
	namespace App\Http\Controllers\API;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Validator;
	use App\Http\Controllers\Controller;
	use Illuminate\Validation\Rule;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Response;
	use App\commonModel;
	use DB;

	
	class PwdController extends Controller
	{
		public function __construct() {
			$this->commonModel = new commonModel();
		}
		
		public $successStatus = 200;
		public $createdStatus = 201;
		public $nocontentStatus = 204;
		public $notmodifiedStatus = 304;
		public $badrequestStatus = 400;
		public $unauthorizedStatus = 401;
		public $notfoundStatus = 404;
		public $intservererrorStatus = 500;
		
		public function AbsenteeAdd(Request $request){
			try{
				$validator = Validator::make($request->all(), [
				'st_code' => 'required',
				'dist_no' => 'required',
				'ac_no' => 'required',
				'ps_no' => 'required',
				'ps_name' => 'required',
				'epic_no' => 'required',
				'name' => 'required',
				'father_name' => 'required',
				'gender' => 'required',
				'age' => 'required',
				'mobile' => 'required',
				'address1' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->successStatus);           
				} 
				if(isset($userInputs['mobile2']))
				$cmobile=trim($userInputs['mobile2']);
				else
				$cmobile="";
				
				$userInputs = $request->all();
				$add_data=array();
				$add_data['request_type'] = 3;
				$add_data['otp'] = 1234;
				$add_data['tracking_id'] = 'ABS'.strtoupper(str_random(5));
				$add_data['auth_mobile'] = trim($userInputs['mobile']);
				$add_data['mobile'] = $cmobile;
				$add_data['st_code'] = trim($userInputs['st_code']);
				$add_data['ac_no'] = trim($userInputs['ac_no']);
				$add_data['dist_no'] = trim($userInputs['dist_no']);
				$add_data['ps_no'] = trim($userInputs['ps_no']);
				$add_data['ps_name'] = trim($userInputs['ps_name']);
				$add_data['name'] = trim($userInputs['name']);
				$add_data['father_name'] = trim($userInputs['father_name']);
				$add_data['address'] = trim($userInputs['address1']);
				if(isset($userInputs['address2']) && strlen($userInputs['address2'])>3)
				{
					$add_data['new_address'] = trim($userInputs['address2']);
					$add_data['same_address'] = 1;
				}
				else
				{
					$add_data['same_address'] = 0;
				}
				$add_data['age'] = trim($userInputs['age']);
				$add_data['gender'] = trim($userInputs['gender']);
				$add_data['epic_no'] = trim($userInputs['epic_no']);
				
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Submission Failed";
				$eldata= DB::table('absent_voters')->where('epic_no',trim($userInputs['epic_no']))->first();
				if($eldata)
				{
					$summary['success'] = true;
					$summary['reference_id']=$eldata->tracking_id;
					$summary['message'] = "This EPIC has already applied for 12D form.Your reference id is : ".$eldata->tracking_id;
				}
				else
				{
					$result = DB::table('absent_voters')->insert($add_data);
					if($result)
					{
						$summary['success'] = true;
						$nid=DB::getPdo()->lastInsertId();
						$updata=array();
						$summary['reference_id']=$updata['tracking_id']=$add_data['tracking_id'].$nid;
						DB::table('absent_voters')->where('id',$nid)->update($updata);
						$summary['message'] = "Request submitted successfully";
					}
				}
				
				return response()->json($summary, $this->successStatus);
				
			} 
			catch (Exception $ex) 
			{
				return response()->json(['statuscode' => false,'message'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}
		
		public function D12(Request $request){
			try{
				$validator = Validator::make($request->all(), [
				'election_id' => 'required',
				'st_code' => 'required',
				'dist_no' => 'required',
				'ac_no' => 'required',
				'ps_no' => 'required',
				'ps_name' => 'required',
				'epic_no' => 'required',
				'name' => 'required',
				'father_name' => 'required',
				'gender' => 'required',
				'age' => 'required',
				'mobile' => 'required',
				'address1' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->successStatus);           
				} 
				if(isset($userInputs['mobile2']))
				$cmobile=trim($userInputs['mobile2']);
				else
				$cmobile="";
				
				$userInputs = $request->all();
				$add_data=array();
				$add_data['request_type'] = 3;
				$add_data['otp'] = 1234;
				$add_data['tracking_id'] = 'ABS'.strtoupper(str_random(5));
				$add_data['auth_mobile'] = trim($userInputs['mobile']);
				$add_data['mobile'] = $cmobile;
				$add_data['st_code'] = trim($userInputs['st_code']);
				$add_data['ac_no'] = trim($userInputs['ac_no']);
				$add_data['dist_no'] = trim($userInputs['dist_no']);
				$add_data['ps_no'] = trim($userInputs['ps_no']);
				$add_data['ps_name'] = trim($userInputs['ps_name']);
				$add_data['name'] = trim($userInputs['name']);
				$add_data['father_name'] = trim($userInputs['father_name']);
				$add_data['address'] = trim($userInputs['address1']);
				if(isset($userInputs['address2']) && strlen($userInputs['address2'])>3)
				{
					$add_data['new_address'] = trim($userInputs['address2']);
					$add_data['same_address'] = 1;
				}
				else
				{
					$add_data['same_address'] = 0;
				}
				$add_data['age'] = trim($userInputs['age']);
				$add_data['gender'] = trim($userInputs['gender']);
				$add_data['epic_no'] = trim($userInputs['epic_no']);
				
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Submission Failed";
				$eldata= DB::table('absent_voters')->where('epic_no',trim($userInputs['epic_no']))->first();
				if($eldata)
				{
					$summary['success'] = true;
					$summary['reference_id']=$eldata->tracking_id;
					$summary['message'] = "This EPIC has already applied for 12D form.Your reference id is : ".$eldata->tracking_id;
				}
				else
				{
					$result = DB::table('absent_voters')->insert($add_data);
					if($result)
					{
						$summary['success'] = true;
						$nid=DB::getPdo()->lastInsertId();
						$updata=array();
						$summary['reference_id']=$updata['tracking_id']=$add_data['tracking_id'].$nid;
						DB::table('absent_voters')->where('id',$nid)->update($updata);
						$summary['message'] = "Request submitted successfully";
					}
				}
				return response()->json($summary, $this->successStatus);
				
			} 
			catch (Exception $ex) 
			{
				return response()->json(['statuscode' => false,'message'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}
		
		
	}					