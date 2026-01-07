<?php

namespace App\Http\Controllers\API\vv1;

use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\commonModel;
use Illuminate\Validation\Rule;
use DB;
use Session;
use App\models\{States, Districts, AC};
use Mail;
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Redirect;
use PDF;
use Carbon\Carbon;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Classes\xssClean;

class AuthenticationController extends Controller
{

    public $bad_request = 400;
    public $unauthorized = 401;
    public $forbidden = 403;
    public $not_found = 404;
    public $request_timeout = 408;
    public $too_many_requests = 429;
    public $internal_server_error = 500;
    public $bad_gateway = 502;
    public $service_unavailable = 503;
    public $gateway_timeout = 504;
    public $http_version_not_supported = 505;
    public $insufficient_storage = 507;
    public $loop_detected = 508;
    protected $gcmkey = "ed8cf08edc53edfr";
    protected $gcmiv = "3436jnha98fab441";



    public function login(Request $request)
    {
        try {

            // Add validation rules with 'exists' and custom messages
            $validator = Validator::make($request->all(), [
                "mobile" => "required|numeric|digits:10|exists:user_login,mobile",
                "device_id" => "required|string",
            ], [

                'mobile.required' => 'Mobile number is required.',
                'mobile.numeric' => 'Mobile number must be numeric.',
                'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                'mobile.exists' => 'This mobile number is not registered.',
                'device_id.required' => 'Device ID is required.',
                'device_id.string' => 'Device ID must be a valid string.',
            ]);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }


            $mobile = $request->input("mobile");
            $deviceId = $request->input("device_id");
            $app_id = "Suvidhaapp";


            $user = User::where("mobile", $mobile)->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Invalid credentials. Please try again",
                ]);
                return response()->json($r_data, 200);
            }


            if ($user->role_id == 2) {

                $loginDb = DB::table('v_candidate_personal_detail')
                    ->where('cand_mobile', $mobile)
                    ->first();

                if ($loginDb) {

                    $partyDetails = DB::table('v_candidate_nomination_detail')
                        ->where('candidate_id', $loginDb->candidate_id)
                        ->first();





                    $partyId = !empty($partyDetails->party_id) ? $partyDetails->party_id : '1180';


                    $user = User::updateOrCreate(
                        ['mobile' => $loginDb->cand_mobile],
                        [
                            'name' => $loginDb->cand_name,
                            'candidate_id' => $loginDb->candidate_id,
                            'authority_id' => "0",
                            'role_id' => '2',
                            'party_id' => $partyId,
                            'device_id' => $deviceId,
                            'device_type' => 'Mobile',
                            'otp_attempt' => '0',
                            'isActive' => 1,
                            'created_at' => now(),
                            'verify_otp' => '0',
                            'app_id' => $app_id
                        ]
                    );
                }
            }
            if ($user) {
                if ($user->isActive != "1") {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "User is inactive."]);
                    return response()->json($r_data, 200);
                }

                 if ($user->otp_time) {
                $otpLastSentTime = Carbon::parse($user->otp_time); // Assuming 'otp_time' is the column storing last OTP time
                $resendLimit = Carbon::now()->subMinutes(2);

                if ($otpLastSentTime->greaterThan($resendLimit)) {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        'status' => false,
                        'error' => 'You can resend OTP after 2 minutes',
                    ]);
                    return response()->json($r_data, 200);
                }
            }

                // Check OTP attempt limit (e.g., max 3 attempts)
        
        if($mobile =='9871124359'){
                  $otp = '123456';
          } else {
                        // Generate a random OTP and send it via SMS
                        $otp = rand(100000, 999999); // Generate a 6-digit OTP
                        $mobile_message = 'Your OTP is ' . $otp . ' for ECI Candidate App. Please enter the OTP to proceed. Do not share this OTP.';
                        $response = SmsgatewayHelper::gupshup($mobile, $mobile_message);
                        // Handle response from SMS gateway (optional)
                    }     // Send OTP (Static for testing purposes)
                
                //$otp = '123456';
                $otpTime = Carbon::now();

                // Update user OTP, OTP time, device ID, and app ID
                $addUserOtp = User::where("id", $user->id)->update([
                    "otp" => $otp,
                    "otp_time" => $otpTime,
                    "device_id" => $deviceId,
                    "app_id" => $app_id
                ]);

                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => true,
                    "message" => "OTP sent. Please verify.",
                    "data" => [
                        "role_id" => intval($user->role_id),
                        "is_profile_update" => $user->is_profile_update,
                        "pwd_id" => $user->pwd_id,
                    ],
                ]);
                return response()->json($r_data, 200);
            } else {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Invalid user.",
                ]);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "Something went wrong."]);
            return response()->json($r_data, 200);
        }
    }



    public function verifyOtp(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make(
                $request->all(),
                [
                    "otp" => "required|numeric|digits:6|exists:user_login,otp",
                    "mobile" => "required|numeric|digits:10|exists:user_login,mobile",
                    "role_id" => "required|numeric", //
                ],
                [
                    'otp.required' => 'OTP is required.',
                    'otp.numeric' => 'OTP must be numeric.',
                    'otp.digits' => 'OTP must be exactly 6 digits.',
                    'otp.exists' => 'Invalid or expired OTP.',
                    'mobile.required' => 'Mobile number is required.',
                    'mobile.numeric' => 'Mobile number must be numeric.',
                    'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                    'mobile.exists' => 'This mobile number is not registered.',
                    'role_id.required' => 'Role ID is required.',
                    'role_id.numeric' => 'Role ID must be a valid number.',
                ]
            );


            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }

            // Find user based on mobile, otp, and role_id
            $user = User::where([
                "mobile" => $request->mobile,
                "otp" => $request->otp,
                "role_id" => $request->role_id,
            ])->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "User not found or invalid OTP"]);
                return response()->json($r_data, 200);
            }

            // Check if OTP is expired (example: expire after 5 minutes)
            $otpExpiryTime = Carbon::parse($user->otp_time)->addMinutes(5); // Expire OTP after 5 minutes
            if (Carbon::now()->greaterThan($otpExpiryTime)) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "OTP has expired"]);
                return response()->json($r_data, 200);
            }

            // Check if OTP is correct
            if ($user->otp != $request->otp) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "Invalid OTP"]);
                return response()->json($r_data, 200);
            }

            // Check if user is inactive
            if ($user->isActive == 0) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "User is inactive"]);
                return response()->json($r_data, 200);
            }


            // OTP is valid and not expired, user is active
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  [
                "status" => true,
                "message" => "OTP verify Successfully.",
                "data" => ["role_id" => intval($user->role_id)],
            ]);
            return response()->json($r_data, 200);
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Something went wrong",
                "message" => $th->getMessage(), // Error message
                "trace" => $th->getTraceAsString() // Stack trace
            ]);
            return response()->json($r_data, 200);
        }
    }


    

    public function verifyuser(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    "mobile" => "required|numeric|digits:10|exists:user_login,mobile",
                    "password" => [
                        'required',
                        'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/'
                    ],
                    "role_id" => "required|numeric|exists:user_login,role_id",
                    "otp" => "required|numeric|digits:6|exists:user_login,otp",
                ],
                [
                    'mobile.required' => 'Mobile number is required.',
                    'mobile.numeric' => 'Mobile number must be numeric.',
                    'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                    'mobile.exists' => 'This mobile number is not registered.',
                    'password.required' => 'Please enter password.',
                    'password.regex' => 'Password must have 8+ chars, 1 uppercase, 1 lowercase, 1 number, and 1 special char.',
                    'role_id.required' => 'Role ID is required.',
                    'role_id.numeric' => 'Role ID must be a valid number.',
                    'role_id.exists' => 'Invalid Role ID.',
                    'otp.required' => 'OTP is required.',
                    'otp.numeric' => 'OTP must be numeric.',
                    'otp.digits' => 'OTP must be exactly 6 digits.',
                    'otp.exists' => 'Invalid or expired OTP.',
                ]
            );



            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "message" => "Validation failed",
                    "error" => $validator->errors()->first()
                ]);
                return response()->json($r_data, 200);
            }

            // Get the user by mobile and role_id
            $user = User::where([
                "mobile" => $request->mobile,
                "role_id" => $request->role_id,
            ])->first();
            $roleType = DB::table('user_role')->where('role_id', $request->role_id)->select('role_name', 'role_id')->first();

            if (empty($user)) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "User not found"
                ]);
                return response()->json($r_data, 200);
            }

            if ($user->isActive == 0) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "User is inactive please contact system administrator"
                ]);
                return response()->json($r_data, 200);
            }

            if ($user->failed_attempts >= 3) {
                $lastFailedAttemptTime = Carbon::parse($user->last_failed_attempt_at);
                if (Carbon::now()->diffInMinutes($lastFailedAttemptTime) < 10) {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => false,
                        "error" => "Your account is temporarily blocked. Please try again later."
                    ]);
                    return response()->json($r_data, 200);
                } else {
                    // Reset failed attempts if more than 10 minutes have passed
                    User::where("id", $user->id)->update([
                        "failed_attempts" => 0,
                        "last_failed_attempt_at" => null
                    ]);
                }
            }

            // Check OTP
            if ($user->otp != $request->otp) {
                // Increment failed attempts
                User::where("id", $user->id)->update([
                    "failed_attempts" => $user->failed_attempts + 1,
                    "last_failed_attempt_at" => Carbon::now()
                ]);
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "Invalid OTP"]);
                return response()->json($r_data, 200);
            } else {
                $otpExpiryTime = Carbon::parse($user->otp_time)->addMinutes(2); // Expire OTP after 2 minutes
                if (Carbon::now()->greaterThan($otpExpiryTime)) {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "OTP has expired"]);
                    return response()->json($r_data, 200);
                }
            }


            // Check if user is inactive


            // Verify the password using Hash::check()
            if (Hash::check($request->password, $user->password)) {
                // Generate token
                $token = base64_encode(
                    openssl_encrypt(
                        $user->mobile . "-" . time() . "-" . date("dmY") . $user->id,
                        "aes-128-gcm",
                        "ed8cf08edc53edfr",
                        $raw_output = false,
                        "3436jnha98fab441",
                        $tag
                    )
                );

                User::where("id", $user->id)->update([
                    "access_token" => $token,
                    // "otp" => "",
                    "otp_attempt" => 0,
					'verify_otp' => 1,
                    "token_expires_at" => date("Y-m-d H:i:s"),
                    "failed_attempts" => 0, // Reset failed attempts on successful login
                    "last_failed_attempt_at" => null
                ]);



                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => true,
                    "message" => "Login successful",
                    "data" => [
                        "access_token" => $token,
                        "role_id" => intval($user->role_id),
                        "rolename" => $roleType->role_name,
                        "user_id" => $user->id,
                        "pwd_id" => $user->pwd_id,
                       "candidate_id" => !empty($user->candidate_id) ? $user->candidate_id : 0,
                        "is_profile_update" => $user->is_profile_update
                    ]
                ]);
                return response()->json($r_data, 200);
            } else {

                User::where("id", $user->id)->update([
                    "failed_attempts" => $user->failed_attempts + 1,
                    "last_failed_attempt_at" => Carbon::now()
                ]);
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  [
                    "status" => false,
                    "error" => "Invalid credentials"
                ]);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Something went wrong"
            ]);
            return response()->json($r_data, 200);
        }
    }



    public function resendOtp(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                "mobile" => "required|numeric|digits:10|exists:user_login,mobile",
                "device_id" => "required|string",
            ], [

                'mobile.required' => 'Mobile number is required.',
                'mobile.numeric' => 'Mobile number must be numeric.',
                'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                'mobile.exists' => 'This mobile number is not registered.',
                'device_id.required' => 'Device ID is required.',
                'device_id.string' => 'Device ID must be a valid string.',
            ]);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    "error" => $validator->errors()->first(),
                ]);
                return response()->json($r_data, 200);
            }

            // Retrieve mobile and device ID
            $mobile = $request->input('mobile');
            $deviceId = $request->input('device_id');

            // Check if the user exists
            $user = User::where('mobile', $mobile)->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => 'User not found',
                ]);
                return response()->json($r_data, 200);
            }

            // Check the last OTP sent time
            $otpLastSentTime = Carbon::parse($user->otp_time); // Assuming 'otp_time' is the column storing last OTP time
            $resendLimit = Carbon::now()->subMinutes(2);

            if ($otpLastSentTime->greaterThan($resendLimit)) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => 'You can resend OTP after 2 minutes',
                ]);
                return response()->json($r_data, 200);
            }

            // Check OTP attempt limit (e.g., max 3 attempts)
            if ($user->otp_attempt >= 3) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => 'Maximum OTP attempts reached',
                ]);
                return response()->json($r_data, 200);
            }
//$otp = '123456';
            // Generate OTP and send it to the user
            $otp = rand(100000, 999999); // Generate a 6-digit OTP
            $mobile_message = 'Your OTP is ' . $otp . ' for ECI Candidate App. Please enter the OTP to proceed. Do not share this OTP';

            // Send OTP via SMS
            $response = SmsgatewayHelper::gupshup($mobile, $mobile_message);

            // Save OTP and device ID
            User::where("id", $user->id)->update([
                'otp' => $otp,
                'otp_time' => Carbon::now(),
                'device_id' => $deviceId,
                'otp_attempt' => $user->otp_attempt + 1, // Increment OTP attempts
            ]);


            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                'status' => true,
                'message' => 'OTP resent successfully.',
            ]);
            return response()->json($r_data, 200);
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                'status' => false,
                'error' => $th->getMessage(),
            ]);
            return response()->json($r_data, 200);
        }
    }




    public function singupmobile(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make(
                $request->all(),
                [
                    "role_id" => "required|numeric",
                    "mobile" => ["required", "numeric", "digits:10", "regex:/^[6-9]\d{9}$/"],
                    "device_id" => "required",
                ],
                [
                    'role_id.required' => 'Role ID is required.',
                    'role_id.numeric' => 'Role ID must be a valid number.',

                    'mobile.required' => 'Mobile number is required.',
                    'mobile.numeric' => 'Mobile number must be numeric.',
                    'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                    'mobile.regex' => 'Mobile number must start with 6, 7, 8, or 9.',

                    'device_id.required' => 'Device ID is required.',
                    // 'device_id.string' => 'Device ID must be a valid string.'
                ]
            );


            if ($validator->fails()) {


                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }

            $checkUserExistOrNot = User::where("mobile", $request->input("mobile"))->where('pwd_id', '1')->first();
            if ($checkUserExistOrNot) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Mobile number is already registered.",
                ]);
                return response()->json($r_data, 200);
            }

            // Proceed if validation passes
            $mobile = $request->input("mobile");
            $roleId = $request->input("role_id");
            $deviceId = $request->input("device_id");
            $added_at = Carbon::now()->format("Y-m-d");
            $created_at = Carbon::now()->format("Y-m-d");
            $app_id = "Suvidhaapp";
            $isActive = 1;

            $loginDbC = DB::table('v_candidate_personal_detail')->where('cand_mobile', $mobile)->first();
            if ($loginDbC) {
                if ($roleId != '2') {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => false,
                        "error" => "Invalid Applicant Type. Please Select Candidate.",
                    ]);
                    return response()->json($r_data, 200);
                }
            }
            $createOrUpdateC = User::where("mobile", $mobile)->first();
            if ($createOrUpdateC) {
                if ($createOrUpdateC->role_id != $roleId && $createOrUpdateC->permission_request_status == '1') {
                    $arr = [];
                    $roleTypes = DB::table('user_role')->select("role_id", "role_name")->whereIn('role_id', [2, 3, 5, 6, 7])->get();
                    foreach ($roleTypes as $roleType) {
                        $arr[$roleType->role_id] = $roleType->role_name;
                    }
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => false,
                        "error" => "Invalid Applicant Type. Please Select " . $arr[$createOrUpdateC->role_id] ?? "" . ".",
                    ]);
                    return response()->json($r_data, 200);
                }
            }

            if ($roleId == 2) {

                $loginDb = DB::table('v_candidate_personal_detail')->where('cand_mobile', $mobile)->first();

                if ($loginDb) {
                    $partyid = DB::table('v_candidate_nomination_detail')
                        ->where('candidate_id', $loginDb->candidate_id)
                        ->first();

                    $PartyID = !empty($partyid->party_id) ? $partyid->party_id : '1180';

                    $user = User::updateOrCreate(
                        ['mobile' => $loginDb->cand_mobile],
                        [
                            'name' => $loginDb->cand_name,
                            'candidate_id' => $loginDb->candidate_id,
                            'authority_id' => "0",
                            'role_id' => '2',
                            'party_id' => $PartyID,
                            'device_id' => $deviceId,
                            'device_type' => 'Mobile',
                            'otp_attempt' => '0',
                            'created_at' => now(),
                            'verify_otp' => '0',
                            'isActive' => 1, // Fixed the typo
                            'app_id' => $app_id
                        ]
                    );
                }
            }


            $createOrUpdate = User::where("mobile", $mobile)->first();
            if ($createOrUpdate) {

                $flight = User::where("id", $createOrUpdate->id)->update([
                    //"mobile" => $mobile,
                    "role_id" => $roleId,
                    "isActive" => $isActive,
                    "device_id" => $deviceId,
                    "added_at" => $added_at,
                    "created_at" => $created_at,
                    "app_id" => $app_id,
                ]);
            } else {

                $createOrUpdate = User::create([
                    "mobile" => $mobile,
                    "role_id" => $roleId,
                    "isActive" => $isActive,
                    "device_id" => $deviceId,
                    "added_at" => $added_at,
                    "created_at" => $created_at,
                    "app_id" => $app_id,
                ]);
            }
            if ($createOrUpdate->otp_time) {
            $otpLastSentTime = Carbon::parse($createOrUpdate->otp_time); // Assuming 'otp_time' is the column storing last OTP time
            $resendLimit = Carbon::now()->subMinutes(2);

            if ($createOrUpdate->otp_time && $otpLastSentTime->greaterThan($resendLimit)) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => 'You can resend OTP after 2 minutes',
                ]);
                return response()->json($r_data, 200);
            }
        }
				
				$otp = rand(100000, 999999); // Generate a 6-digit OTP
            $mobile_message = 'Your OTP is ' . $otp . ' for ECI Candidate App. Please enter the OTP to proceed. Do not share this OTP';
            $response = SmsgatewayHelper::gupshup($mobile, $mobile_message);
				
			//$otp = '123456';



            // Send OTP
            // $otp = '123456';
            

            $otpTime = Carbon::now();

            $addUserOtp = User::where("id", $createOrUpdate->id)->update([
                "otp" => $otp,
                "otp_time" => $otpTime,
            ]);


            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => true,
                "message" => "OTP sent. Please verify.",
                "data" => ["role_id" => $roleId],
            ]);
            return response()->json($r_data, 200);
        } catch (\Throwable $th) {
            // Log the actual error for debugging
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => $th->getMessage(),
                "trace" => $th->getTrace(),
            ]);
            return response()->json($r_data, 200);
        }
    }



    public function singuppsssword(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    "role_id" => "required|numeric",
                    "mobile" => ["required", "numeric", "digits:10", "regex:/^[6-9]\d{9}$/"],
                    "password" => [
                        'required',
                        'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/'
                    ],
                    "confirm_password" => "required|same:password",
                    "device_id" => "required|string",
                ],
                [
                    'role_id.required' => 'Role ID is required.',
                    'role_id.numeric' => 'Role ID must be a valid number.',

                    'mobile.required' => 'Mobile number is required.',
                    'mobile.numeric' => 'Mobile number must be numeric.',
                    'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                    'mobile.regex' => 'Mobile number must start with 6, 7, 8, or 9.',

                    'password.required' => 'Password is required.',
                    'password.regex' => 'Password must have 8+ chars, 1 uppercase, 1 lowercase, 1 number, and 1 special char.',

                    'confirm_password.required' => 'Confirm password is required.',
                    'confirm_password.same' => 'Confirm password must match the password.',

                    'device_id.required' => 'Device ID is required.',
                    'device_id.string' => 'Device ID must be a valid string.'
                ]
            );




            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }

            // Retrieve the mobile number and other details from the request
            $mobile = $request->input("mobile");
            $roleId = $request->input("role_id");
            $password = Hash::make($request->input("password"));
            // $registration_type = 1;
            $pwd_id = 1;
            $device_type = $request->input("device_type");

            // Fetch the user by mobile
            $user = User::where(['mobile' => $mobile, 'role_id' => $roleId, 'pwd_id' => 0])->first();
            $roleType = DB::table('user_role')->where('role_id', $roleId)->select('role_name', 'role_id')->first();
            //dd($user);

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "User not found"
                ]);
                return response()->json($r_data, 200);
            }

            // Generate the token
            $token = base64_encode(
                openssl_encrypt(
                    $mobile . "-" . time() . "-" . date("dmY") . $user->id,
                    "aes-128-gcm",
                    "ed8cf08edc53edfr",
                    $raw_output = false,
                    "3436jnha98fab441",
                    $tag
                )
            );




            // Update the user record with token and password
            $user->where('id', $user->id)->update([
                "access_token" => $token,
                "password" => $password,
                "token_expires_at" => date("Y-m-d H:i:s"),
                //"registration_type" => $registration_type,
                "device_type" => $device_type,
                "first_login" => "1",
				'verify_otp' => 1,
                "pwd_id" => $pwd_id,
            ]);

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => true,
                "message" => "User Created Successfully",
                "data" => ["role_id" => $roleId, "rolename" => $roleType->role_name, "pwd_id" => $pwd_id, "is_profile_update" => $user->is_profile_update, "user_id" => $user->id, "candidate_id" => !empty($user->candidate_id) ? $user->candidate_id : 0, "access_token" => $token],
            ]);
            return response()->json($r_data, 200);
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => $th->getMessage(), // This will give the actual error message
            ]);
            return response()->json($r_data, 200);
        }
    }

    public function logout(Request $request)
    {

        try {
            // Validate the request
            $validator = Validator::make(
                $request->all(),
                [
                    'mobile' => ['required', 'numeric', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
                    'access_token' => 'required|string',
                ],
                [
                    'mobile.required' => 'Mobile number is required.',
                    'mobile.numeric' => 'Mobile number must be numeric.',
                    'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                    'mobile.regex' => 'Mobile number must start with 6, 7, 8, or 9.',

                    'access_token.required' => 'Access token is required.',
                    'access_token.string' => 'Access token must be a valid string.'
                ]
            );


            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    "error" => $validator->errors()->first(),
                ]);
                return response()->json($r_data, 200);
            }

            // Retrieve mobile and access_token from request
            $mobile = $request->input('mobile');
            $access_token = $request->input('access_token');

            // Find user by mobile and check access_token
            $user = User::where('mobile', $mobile)
                ->where('access_token', $access_token)
                ->first();
            // dd($user);

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => 'Invalid credentials or user not found',
                ]);
                return response()->json($r_data, 200);
            }

            // Invalidate the access_token (set to null or clear it)
            $user->where('mobile', $mobile)
                ->where('access_token', $access_token)->update([
                    'access_token' => null,
                ]);

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                'status' => true,
                'message' => 'Logged out successfully',
            ]);
            return response()->json($r_data, 200);
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                'status' => false,
                'error' => $th->getMessage(),
            ]);
            return response()->json($r_data, 200);
        }
    }



    public function getElectionByDate()
    {

        $filter_array = [
            'const_type'                => 'AC',
        ];
        $election_details = DB::connection('mysql')->table('m_election_history')
            ->where($filter_array)
            ->orderby('id', 'desc')
            ->groupBy('election_id')
            ->limit(3)
            ->get();

        if (!empty($election_details) > 0) {
            foreach ($election_details as $raw) {
                $election_data[] = array(
                    "election_id"       => $raw->id,
                    "election_type_id"  => $raw->election_type_id,
                    "const_type"        => $raw->const_type,
                    "elect_type"        => $raw->elect_type,
                    "description"       => $raw->description,
                );
            }
            $success['success'] = true;
            $success['data'] = $election_data;
        } else {
            $error['success'] = false;

            return response()->json($error, $errStatus);
            //return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
        }
        return response()->json($success, $this->successStatus);
        //return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
        //return $this->sendResponse($election_data, 'Record Found!');
    }
}
