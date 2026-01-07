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
use App\models\Permission\PermissionModel;
use App\models\Permission\User_dataModel;
use Illuminate\Support\Facades\DB;
use Session;
use App\models\{States, Districts, AC};
use Mail;
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Redirect;
use PDF;
use Carbon\Carbon;
use App\Helpers\SendNotification;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Classes\xssClean;
use App\Jobs\Messgup;
use App\models\Admin\Nomination\UserModel;
use Illuminate\Support\Facades\Response;

class PermissionApiController extends Controller
{

    protected $gcmkey = "ed8cf08edc53edfr";
    protected $gcmiv = "3436jnha98fab441";

    public function getparty(Request $request)
    {


        try {
            $validator = Validator::make($request->all(), [
                "access_token" => "required",


            ], [
                'access_token.required' => 'The access token is required.',

            ]);
            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $accessToken = trim($inputs["access_token"]);
            //$mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();

            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    $allParty = DB::table("m_party")
                        ->select("*")
                        ->where("CCODE", "<>", "1180")
                        ->where("PARTYSYM", "<>", "-1")
                        ->where("deleteflag", "N")
                        ->orderBy("PARTYNAME")
                        ->get()
                        ->toArray();
                    $result = DB::table('m_election_history')
                        ->where('const_type', 'AC')
                        ->select('election_id')
                        ->max('election_id');



                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  [
                        "status" => true,
                        "message" => 'Get Party Successfully',
                        "data" => [
                            //"token" => $accessToken,
                            "partlist" => $allParty,
                            'elc_id' => $result,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
                }
            }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }
    public function get_state(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "access_token" => "required",


            ], [
                'access_token.required' => 'The access token is required.',

            ]);
            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $accessToken = trim($inputs["access_token"]);
            // $mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();

            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    $allstate = DB::table('m_cur_elec')->join('m_state', 'm_state.ST_CODE', 'm_cur_elec.ST_CODE')->where('ConstType', 'AC')->select('m_state.ST_CODE', 'm_state.ST_NAME')->groupBy('ST_CODE')->orderBy('m_state.ST_NAME', 'ASC')->get();
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => true,
                        "message" => 'Get State Successfully',
                        "data" => [
                            //"token" => $accessToken,
                            "allstate" => $allstate,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
 $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);

                }
            }else{

 $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);

            }
        } catch (\Throwable $th) {
            //throw $th;

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }

    public function getdist(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "access_token" => "required|string",
                "state" => [
                    "required",
                    "max:40",
                    "regex:/^[a-zA-Z0-9 -_,\/\\\s]+$/i",
                ],
            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'state.required' => 'The state is required.',
                'state.max' => 'The state may not be greater than 40 characters.',
                'state.regex' => 'The state may only contain letters, numbers, spaces, and some special characters (e.g., hyphen, underscore, comma, slash).',
            ]);


            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $st = trim($inputs["state"]);
            $accessToken = trim($inputs["access_token"]);
            // $mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();

            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    $alldist = DB::table("m_district")
                        ->select("*")
                        ->where("st_code", $st)
                        ->get()
                        ->toArray();
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => true,
                        "message" => 'Get district Successfully',
                        "data" => [
                            //"token" => $accessToken,
                            "alldist" => $alldist,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
                }
            }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }
    public function getAc(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "access_token" => "required|string",
                "state" => "required|max:40|regex:/^[a-zA-Z0-9 -_,\/\\\s]+$/i",
                "dist" => "required|numeric|min:1",

            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'state.required' => 'The state is required.',
                'state.max' => 'The state may not be greater than 40 characters.',
                'state.regex' => 'The state may only contain letters, numbers, spaces, and some special characters (e.g., hyphen, underscore, comma, slash).',
                'dist.required' => 'The district is required.',
                'dist.numeric' => 'The district must be a numeric value.',
                'dist.min' => 'The district must be at least 1.',

            ]);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $st = trim($inputs["state"]);

            $dist = trim($inputs["dist"]);
            $accessToken = trim($inputs["access_token"]);
            // $mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();

            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    $allsac = DB::table("m_ac")
                        ->select("*")
                        ->where("st_code", $st)
                        ->where("DIST_NO_HDQTR", $dist)
                        ->get()
                        ->toArray();

                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => true,
                        "message" => 'Get Assembly Successfully',
                        "data" => [
                            // "token" => $accessToken,
                            "allac" => $allsac,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
                }
            }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }


    public function profile(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                "access_token" => "required",


            ], [
                'access_token.required' => 'The access token is required.',

            ]);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => $validator->errors()->first(),
                ]);
                return response()->json($r_data, 200);
            }

            // Extract the access token
            $accessToken = trim($request->input("access_token"));

            // Find the user by access token
            $user = User::where("access_token", $accessToken)->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "User not found.",
                ]);
                return response()->json($r_data, 200);
            }

            // Retrieve additional data
            $userid = $user->id;
            $mobile = $user->mobile;
            $party = $user->party_id;
            $role_id = $user->role_id;

            // $allParty = DB::table('m_party')
            //     ->select('CCODE','PARTYNAME')
            //     ->where('CCODE', '<>', '1180')
            //     ->where('PARTYSYM', '<>', '-1')
            //     ->where('deleteflag', 'N')
            //     ->orderBy('PARTYNAME')
            //     ->get()
            //     ->toArray();

            $result = DB::table('m_election_history')
                ->where('const_type', 'AC')
                ->select('election_id')
                ->max('election_id');
            $user_role = DB::table('user_role')
                ->where('role_id', $role_id)
                ->select('role_id', 'role_name')
                ->get();

            $state = DB::table('m_cur_elec')
                ->join('m_state', 'm_state.ST_CODE', '=', 'm_cur_elec.ST_CODE')
                ->where('ConstType', 'AC')
                ->select('m_state.ST_CODE', 'm_state.ST_NAME')
                ->groupBy('ST_CODE')
                ->orderBy('m_state.ST_NAME', 'ASC')
                ->get();

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => true,
                "message" => 'Get UserProfile Successfully',
                'user_id' => $user->id,
                'getStates' => $state,
                'mobile' => $mobile,
                'user_role' => $user_role,
                'elc_id' => $result,

            ]);
            return response()->json($r_data, 200);
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Something went wrong: " . $th->getMessage()
            ]);
            return response()->json($r_data, 200);
        }
    }

    public function addProfile(Request $request)
    {
        try {
            $data = $request->all();

            // Request Validation
            $validator = Validator::make($data, [
                "access_token" => "required|string",
                'name' => [
                    'required',
                    'max:100',
                    'regex:/^[a-zA-Z. ]+$/u',
                ],
                'father_name' => [
                    'required',
                    'max:100',
                    'regex:/^[a-zA-Z. ]+$/u',
                ],
                'email' => [
                    'required',
                    'email:rfc,dns',
                    'max:100',
                ],
                'gender' => [
                    'required',
                    'in:male,female,other',
                ],
                'state' => [
                    'required',
                    'max:40',
                    'regex:/^[a-zA-Z0-9 -_,\/\\\s]+$/i',
                ],

                'dob' => [
                    'required',
                    'date',
                    'before:-18 years',
                ],
                'party_master' => [
                    'required',
                    'numeric',
                    'not_in:0',
                ],
            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'name.required' => 'The name is required.',
                'name.max' => 'The name may not be greater than 100 characters.',
                'name.regex' => 'The name may only contain letters, periods, and spaces.',
                'father_name.required' => 'The father\'s name is required.',
                'father_name.max' => 'The father\'s name may not be greater than 100 characters.',
                'father_name.regex' => 'The father\'s name may only contain letters, periods, and spaces.',
                'email.required' => 'The email address is required.',
                'email.email' => 'The email address must be a valid email address.',
                'email.email:rfc,dns' => 'The email address must be valid and have a valid DNS record.',
                'email.max' => 'The email address may not be greater than 100 characters.',
                'gender.required' => 'The gender is required.',
                'gender.in' => 'The gender must be one of the following: male, female, or other.',
                'state.required' => 'The state is required.',
                'state.max' => 'The state may not be greater than 40 characters.',
                'state.regex' => 'The state may only contain letters, digits, spaces, and certain special characters.',

                'dob.required' => 'The date of birth is required.',
                'dob.date' => 'The date of birth must be a valid date.',
                'dob.before' => 'You must be at least 18 years old.',
                'party_master.required' => 'The party master field is required.',
                'party_master.numeric' => 'The party master must be a numeric value.',
                'party_master.not_in' => 'The party master must not be zero.',
            ]);


            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => $validator->errors()->first()
                ]);
                return response()->json($r_data, 200);
            }

            // Access Token Validation
            $accessToken = trim($request->input("access_token"));
            $user = User::where("access_token", $accessToken)->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Invalid access token"
                ]);
                return response()->json($r_data, 200);
            }

           
            $mobile = $user->mobile;
           
            $existingProfile = User_dataModel::where('user_login_id', $user->id)->first();
            if ($existingProfile) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Your profile is already created"
                ]);
                return response()->json($r_data, 200);
            }

            // Preparing Data for Insertion
            $permission = new User_dataModel;
            $data = [
                'user_login_id' => $user->id,
                'name' => $request->name,
                'fathers_name' => $request->father_name,
                'email' => $request->email,
                'mobileno' => $mobile,
                'gender' => $request->gender,
                'epic_no' => 'NULL',
                'part_no' => '0',
                'slno' => '0',
                'dob' => $request->dob,
                'party_id' => $request->party_master,
                'address' => $request->Address1,
                'state_id' => $request->state,
                'district_id' => $request->district,
                'ac_id' => $request->ac,
                'religion' => '0',
                'caste' => '0',
                'mark_as_delete' => '0',
                'added_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'added_update_at' => Carbon::now(),
                'updated_at' => null,
                'election_id' => $request->election_id
            ];

            $updateDetails = [
                'registration_type' => '1',
                'login_access' => '1',
                'is_profile_update' => '1',
                'email' => $request->email,
                'updated_at' => Carbon::now(),
                'election_id' => $request->election_id,
                'party_id' => $user->party_master
            ];

            // Database Transaction
            DB::beginTransaction();
            try {
                // Insert user data
                $res = $permission->create($data);
                $id = DB::getPdo()->lastInsertId();

                // Update user login table
                DB::table('user_login')->where('mobile', $mobile)->update($updateDetails);

                DB::commit();

                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => true,
                    "message" => "Profile successfully saved!"
                ]);
                return response()->json($r_data, 200);
            } catch (Exception $e) {
                DB::rollBack();
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Database error: " . $e->getMessage()
                ]);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Something went wrong: " . $th->getMessage()
            ]);
            return response()->json($r_data, 200);
        }
    }


    public function getpermissiondata(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "access_token" => "required",


            ], [
                'access_token.required' => 'The access token is required.',

            ]);
            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            // Validate Access Token
            $accessToken = trim($request->input("access_token"));
            $user = User::where("access_token", $accessToken)->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Invalid access token"
                ]);
                return response()->json($r_data, 200);
            }

            $userid = $user->id;
            $mobile = $user->mobile;

            // Check if User Data Exists
            $res = DB::table('user_data')->where('mobileno', $mobile)->where('user_login_id', $userid)->first();

            if (!$res) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Please Fill Profile Details First to Apply Permission!"
                ]);
                return response()->json($r_data, 200);
            }

            if ($res->election_id == 0 || $res->election_id == null) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Please Update Election Id in Profile Details!"
                ]);
                return response()->json($r_data, 200);
            }



            // Get user role
            $userrole = $user->role_id;
            $roleType = DB::table('user_role')->where('role_id', $userrole)->select('role_name', 'role_id')->get();

            // Get necessary data
            $state = DB::table('m_state')->where('ST_CODE', $res->state_id)->select('st_code', 'ST_NAME')->get();
            $userDetails = DB::table('user_data')
                ->join('m_party', 'm_party.CCODE', 'user_data.party_id')
                ->join('m_state as e', 'e.ST_CODE', '=', 'user_data.state_id')
                ->where('m_party.deleteflag', 'N')
                ->where('user_data.user_login_id', $userid)
                ->select('user_data.user_login_id', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'e.ST_CODE', 'e.ST_NAME', 'm_party.CCODE', 'm_party.PARTYNAME', 'user_data.election_id')
                ->first();





            $st = $res->state_id;
            $permissionType = DB::table('permission_type')
                ->join('permission_master', 'permission_master.id', 'permission_type.permission_type_id')
                ->leftJoin('restriction_day_master', 'restriction_day_master.permission_type_id', 'permission_type.permission_type_id')
                ->orderBy('permission_master.permission_name')
                ->where('permission_master.status', '1')
                ->where('permission_type.st_code', $st)
                ->where('restriction_day_master.st_code', $st)
                ->select(
                        DB::raw("CASE
                                    WHEN restriction_day_master.restriction_day <= 3
                                    THEN 7
                                    ELSE restriction_day_master.restriction_day
                                 END as restriction_day"),
                        'permission_master.permission_name',
                        'permission_type.id as permission_type',
                        'permission_type.permission_type_id',
                        'permission_type.role_id as perm_role_id'
                    )
                ->groupBy('permission_type_id')
                ->get();
            $restriction_master = DB::table('restriction_master')->where('ST_CODE', $res->state_id)->select('st_code', 'restriction_status')->get();

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => true,
                "message" => 'Get Permission Type Successfully',
                "data" => [
                    'permission_type' => $permissionType,
                    'user_details' => $userDetails,
                    // 'user_details_location' => $userDetailsLocation,
                    //'user_details_police' => $userDetailsPolice,
                    'restriction_master' => $restriction_master,
                    'state' => $state,
                    'role_type' => $roleType
                ]
            ]);
            return response()->json($r_data, 200);
        } catch (Exception $e) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Something went wrong: " . $e->getMessage()
            ]);
            return response()->json($r_data, 200);
        }
    }



    public function get_police_station(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "access_token" => "required|string",
                "state" => "required|max:40|regex:/^[a-zA-Z0-9 -_,\/\\\s]+$/i",
                "ac" => "required|numeric|min:1",

            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'state.required' => 'The state is required.',
                'state.max' => 'The state may not be greater than 40 characters.',
                'state.regex' => 'The state may only contain letters, digits, spaces, and certain special characters.',
                'ac.required' => 'The assembly constituency (ac) is required.',
                'ac.numeric' => 'The assembly constituency (ac) must be a numeric value.',
                'ac.min' => 'The assembly constituency (ac) must be at least 1.',

            ]);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $stateID = trim($inputs["state"]);
            $acID = trim($inputs["ac"]);
            $accessToken = trim($inputs["access_token"]);
            // $mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();

            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    $police = DB::table('police_station_master')->where('ST_CODE', $stateID)->where('ac_no', $acID)->orderBy('police_st_name')->get();

                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => true,
                        "message" => 'Get Police Station  Successfully',
                        "data" => [
                            // "token" => $accessToken,
                            "police" => $police,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
                }
            }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }

    public function getSelectpermission_doc(Request $request)
    {


        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                "access_token" => "required|string",
                "permsn_id" => "required|numeric|min:1",
            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'permsn_id.required' => 'The permission ID is required.',
                'permsn_id.numeric' => 'The permission ID must be a numeric value.',
                'permsn_id.min' => 'The permission ID must be at least 1.',
            ]);



            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }

            // Access Token Validation
            $accessToken = trim($request->input("access_token"));
            $user = User::where("access_token", $accessToken)->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Invalid access token"
                ]);
                return response()->json($r_data, 200);
            }

            // Retrieve user data by mobile and user login ID
            $userData = DB::table('user_data')
                ->where('mobileno', $user->mobile)
                ->where('user_login_id', $user->id)
                ->first();

            if (!$userData) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "User data not found"
                ]);
                return response()->json($r_data, 200);
            }

            $stateCode = $userData->state_id;

            if (!empty($request->permsn_id)) {
                // Retrieve permission details based on permission ID and state code
                $getPermissionDetails = DB::table('permission_required_doc')
                    ->where('permission_id', $request->permsn_id)
                    ->where('st_code', $stateCode)
                    ->where('authority_type_id', 'cand01')
                    ->get();

                if ($getPermissionDetails->isNotEmpty()) {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => true,
                        "message" => 'Get PermissionType Doc Successfully',
                        "data" => $getPermissionDetails
                    ]);
                    return response()->json($r_data, 200);
                } else {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => false,
                        "error" => "No permission details found"
                    ]);
                    return response()->json($r_data, 200);
                }
            }

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Permission ID is required"
            ]);
            return response()->json($r_data, 200);
        } catch (\Exception $e) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Something went wrong: " . $e->getMessage()
            ]);
            return response()->json($r_data, 200);
        }
    }

    public function get_location(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "access_token" => "required|string",
                "state" => "required|max:40|regex:/^[a-zA-Z0-9 -_,\/\\\s]+$/i",
                "ac" => "required|numeric|min:1",

            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'state.required' => 'The state is required.',
                'state.max' => 'The state may not be greater than 40 characters.',
                'state.regex' => 'The state may only contain letters, digits, spaces, and certain special characters.',
                'ac.required' => 'The assembly constituency (ac) is required.',
                'ac.numeric' => 'The assembly constituency (ac) must be a numeric value.',
                'ac.min' => 'The assembly constituency (ac) must be at least 1.',

            ]);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $stateID = trim($inputs["state"]);
            $acID = trim($inputs["ac"]);
            $accessToken = trim($inputs["access_token"]);
            // $mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();

            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    //$police = DB::table('police_station_master')->where('ST_CODE', $stateID)->where('ac_no', $acID)->orderBy('police_st_name')->get();
                    //$getACLists = DB::table('location_master')->orderBy('location_name', 'DESC')->where('ST_CODE', $stateID)
                    //    ->where('ac_no', $acID)->select('id', 'location_name', 'st_code', 'ac_no')
                    //   ->get();

                    $getACLists = DB::table('location_master')
                        ->where('ST_CODE', $stateID)
                        ->where('ac_no', $acID)
                        ->select('id', 'location_name', 'st_code', 'ac_no')
                        ->orderBy('location_name', 'DESC')
                        ->get()
                        ->map(function ($item) {
                            return (array) $item;
                        });
                    $getACLists->push(['id' => 'other', 'location_name' => 'other']);
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => true,
                        "message" => 'Get Location Successfully',
                        "data" => [
                            // "token" => $accessToken,
                            "Location" => $getACLists,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
                }
            }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }



    public function updateProfile(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            "access_token" => "required",


        ], [
            'access_token.required' => 'The access token is required.',

        ]);

        if ($validator->fails()) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => $validator->errors()->first()
            ]);
            return response()->json($r_data, 200);
        }

        // Fetch user based on access_token
        $accessToken = $request->input('access_token');
        $user = User::where('access_token', $accessToken)->first();

        if (!$user) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Invalid access token"
            ]);
            return response()->json($r_data, 200);
        }

        $mobile = $user->mobile;
        $id = $user->id;

        // Initialize data array


        try {
            // Get Election data
            $data['election'] = DB::table('m_election_history')
                ->where('const_type', 'AC')
                ->max('election_id');

            // Fetch User Profile
            $profile = DB::table('user_data')
                ->join('m_party', 'm_party.CCODE', '=', 'user_data.party_id')
                ->join('user_login', 'user_login.mobile', '=', 'user_data.mobileno')
                //->join('m_state', 'user_data.state_id', '=', 'm_state.ST_CODE')
                ->select('user_data.name', 'user_login.role_id', 'user_data.fathers_name', 'user_data.email', 'user_data.gender', 'user_data.mobileno', 'user_data.dob', 'user_data.address', 'user_data.state_id', 'user_login.permission_request_status', 'm_party.CCODE', 'm_party.PARTYNAME', 'user_data.district_id', 'user_data.user_login_id', 'user_data.ac_id')
                ->where('user_data.mobileno', $mobile)
                ->get();

            foreach ($profile as $value) {
                $data['profile'] = [
                    'name' => $value->name,
                    'f_name' => $value->fathers_name,
                    'email' => $value->email,
                    'gender' => $value->gender,
                    'mobile' => $value->mobileno,
                    'dob' => $value->dob,
                    'state' => $value->state_id,
                    'address' => $value->address,
                    'status' => $value->permission_request_status,
                    'login_id' => $value->user_login_id,
                    'party_code' => $value->CCODE,
                    'party_name' => $value->PARTYNAME,
                    'dist_id' => $value->district_id,
                    'ac' => $value->ac_id
                ];
            }

            // Check if profile exists
            $res = DB::table('user_data')
                ->where('mobileno', $mobile)
                ->where('user_login_id', $id)
                ->first();

            if ($res) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => true,
                    "message" => 'Get User Profile Successfully',
                    "data" => $data
                ]);
                return response()->json($r_data, 200);
            } else {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Profile not found"
                ]);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => "Something went wrong: " . $th->getMessage()
            ]);
            return response()->json($r_data, 200);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate access token and request data in a single step for faster execution
            $validator = Validator::make($request->all(), [
                'access_token'   => 'required|string',
                'name'           => 'required|max:100|regex:/^[a-zA-Z. ]+$/u',
                'father_name'    => 'required|max:100|regex:/^[a-zA-Z. ]+$/u',
                'email'          => 'required|email|max:100',
                'radio_stacked'  => 'required',
                'dob'            => 'required|date|before:-18 years',
            ]);

            // Handle validation failure
            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status'  => false,
                    'message' => 'Validation failed',
                    "error" => $validator->errors()->first()
                ]);
                return response()->json($r_data, 200);
            }

            // Find the user by access token
            $user = User::where('access_token', $request->input('access_token'))->first();

            // If user is not found, return an error
            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status'  => false,
                    'message' => 'Invalid access token'
                ]);
                return response()->json($r_data, 200);
            }

            // Determine the update details based on permission_request_status
            $updateDetails = [
                'email'  => $request->input('email'),
                'gender' => $request->input('radio_stacked'),
                'dob'    => $request->input('dob'),
            ];

            // Additional details if permission_request_status is not 1
            if ($user->permission_request_status != 1) {
                $updateDetails = array_merge($updateDetails, [
                    'name'           => $request->input('name'),
                    'fathers_name'   => $request->input('father_name'),
                    'party_id'       => $request->input('party_master'),
                    'election_id'    => $request->input('election_id'),
                ]);
            }

            // Update user data in the database
            $update = DB::table('user_data')
                ->where('user_login_id', $user->id)
                ->where('mobileno', $user->mobile)
                ->update($updateDetails);

            // Return success or failure response
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [

                'status'  => $update ? true : false,
                'message' => $update ? 'Updated Successfully!' : 'Failed to update user details'
            ]);
            return response()->json($r_data, 200);
        } catch (\Exception $e) {
            // Handle any exceptions
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                'status'  => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
            return response()->json($r_data, 200);
        }
    }



    public function getpolldays(Request $request)
    {
        try {
            // Conditional validation rules
            $validator = Validator::make($request->all(), [
                'access_token' => 'required|string',
                'state' => [
                    'required',
                    'max:40',
                    'regex:/^[a-zA-Z0-9 -_,\/\\\s]+$/i',
                ],
                'ac' => [
                    'nullable',
                    'required_if:districtID,null',
                    'numeric',
                    'min:1',
                ],
                'districtID' => [
                    'nullable',
                    'required_if:ac,null',
                    'numeric',
                    'min:1',
                ],
            ], [
                // Custom messages for validation
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'state.required' => 'The state is required.',
                'state.max' => 'The state may not be greater than 40 characters.',
                'state.regex' => 'The state may only contain letters, numbers, spaces, and some special characters (e.g., hyphen, underscore, comma, slash).',
                'ac.required_if' => 'The AC number is required when district ID is not provided.',
                'ac.numeric' => 'The AC number must be a number.',
                'ac.min' => 'The AC number must be at least 1.',
                'districtID.required_if' => 'The district ID is required when AC is not provided.',
                'districtID.numeric' => 'The district ID must be a number.',
                'districtID.min' => 'The district ID must be at least 1.',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "message" => "Validation error",
                    "error" => $validator->errors()->first(),
                ]);
                return response()->json($r_data, 200);
            }

            // Trim input data
            $accessToken = trim($request->input("access_token"));
            $sid = trim($request->input("state"));

            $acid = trim($request->input("ac", ''));
            $districtID = trim($request->input("districtID", ''));

            // Verify access token
            $newuser = User::where("access_token", $accessToken)->first();
            if (!$newuser) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    "status" => false,
                    "error" => "Invalid access token",
                ]);
                return response()->json($r_data, 200);
            }

            $poll_day = null;
            $type = 'AC';

            // Condition: state and AC provided
            if ($request->has('state') && $request->has('ac')) {
                $schedule = DB::table('m_election_details as a')
                    ->join('m_ac as g', function ($join) {
                        $join->on('g.AC_NO', '=', 'a.CONST_NO')
                            ->on('g.ST_CODE', '=', 'a.ST_CODE');
                    })
                    ->join('m_schedule as m', function ($join) {
                        $join->on('m.SCHEDULEID', '=', 'a.ScheduleID');
                    })
                    ->where([
                        ['g.AC_NO', $acid],
                        ['a.ST_CODE', $sid],
                        ['a.CONST_TYPE', $type],
                    ])->get();

                if ($schedule->isEmpty()) {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => false,
                        "error" => "Election date is not available",
                    ]);
                    return response()->json($r_data, 200);
                }

                $schedule_id = $schedule[0]->ScheduleID;
                $pollday = DB::table('m_schedule')
                    ->where('SCHEDULEID', $schedule_id)
                    ->orderBy('DATE_POLL', 'desc')
                    ->first();
                $poll_day = $pollday ? GetReadableDate($pollday->DATE_POLL) : null;

                // Condition: state and districtID provided
            } elseif ($request->has('state') && $request->has('districtID')) {
                $poll_day = DB::table('pd_scheduledetail as pds')
                    ->select(
                        DB::raw('MAX(mch.date_poll) as max_date_poll'),
                        'mch.date_count'
                    )
                    ->join('m_state as mst', 'mst.st_code', '=', 'pds.st_code')
                    ->join('m_district as mdt', function ($join) {
                        $join->on('mdt.st_code', '=', 'pds.st_code')
                            ->on('mdt.dist_no', '=', 'pds.dist_no');
                    })
                    ->join('m_schedule as mch', 'mch.scheduleno', '=', 'pds.scheduleid')
                    ->where('pds.st_code', $sid)
                    ->where('pds.dist_no', $districtID)
                    ->groupBy('pds.st_code', 'pds.dist_no')
                    ->first();

                if ($poll_day && !empty($poll_day->max_date_poll)) {
                    $poll_day = GetReadableDate($poll_day->max_date_poll);
                } else {
                    $poll_day = null; // Handle the case where no polling day is found
                }


                // Condition: only state provided
            } elseif ($request->has('state')) {

                $schedule = DB::table('m_election_details')
                    ->where([
                        ['ST_CODE', $sid],
                        ['CONST_TYPE', $type],
                    ])->orderBy('ScheduleID', 'desc')->get();

                if ($schedule->isEmpty()) {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => false,
                        "error" => "Election date is not available",
                    ]);
                    return response()->json($r_data, 200);
                }

                $schedule_id = $schedule[0]->ScheduleID;

                $pollday = DB::table('m_schedule')
                    ->where('SCHEDULEID', $schedule_id)
                    ->orderBy('DATE_POLL', 'desc')
                    ->first();
                $poll_day = $pollday ? GetReadableDate($pollday->DATE_POLL) : null;
            }

            // Return success response
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => true,
                "message" => "Polling day retrieved successfully",
                "data" => [
                    "poll_day" => $poll_day,
                ]
            ]);
            return response()->json($r_data, 200);
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "message" => "Something went wrong",
                "error" => $th->getMessage(),
            ]);
            return response()->json($r_data, 200);
        }
    }


    public function statedatevalidation($StateId)
    {
        $data = DB::table('restriction_master')->where('st_code', $StateId)->select('st_code', 'restriction_status')->get();
        return $data;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $accessToken = trim($request->input("access_token"));
            if (!$accessToken) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Token not provided']);
                return response()->json($r_data, 200);
            }

            $data = $request->all();
            if (count($data) == 0) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Token not provided']);
                return response()->json($r_data, 200);
            }


            // Validate API token
            $user = User::where('access_token', $accessToken)->first();

            if (!$user) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
            $usermaster = UserModel::where('user_login_id', $user->id)->first();

            if (!$usermaster) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Please Create Profile First']);
                return response()->json($r_data, 200);
            }

            $doc_data = $request->file('permsndoc');
            $doc_names = '';

            if (!empty($doc_data)) {

                if (is_array($doc_data) && count($doc_data) > 0) {
                    sort($doc_data);
                } else {

                    $doc_data = [$doc_data];
                }
            } else {
                $doc_data;
            }

            $sid = $usermaster->state_id;
           // dd($sid);
            $msg = '';
            // dd($sid);
            $kyk = $this->statedatevalidation($sid);
            foreach ($kyk as $item) {
                $khs = $item->restriction_status;
            }
            $document = $request->input('doc');
            $ptypeid = $request->permission_type;
            $permission_type_role = DB::table('permission_type')
                ->where('permission_type.status', '1')
                ->where('st_code', $sid)
                ->where('id', $ptypeid)
                ->select('permission_type.role_id')
                ->first();

            if ($permission_type_role == null) {
                $role_type_id = null;
            } else {
                $role_type_id = $permission_type_role->role_id;
            }
           $PermissionCount = DB::table('permission_request')
    ->where('user_id', $user->id)
    ->where('st_code', $sid)
    ->where('permission_type_id', $ptypeid)
    ->where('created_at', '>=', Carbon::now()->subMinutes(10)) // Filter by the last 10 minutes
    ->count();
       if ($PermissionCount >= 5) {
         $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'You cannot apply for the same permission more than 5 times in 10 minutes.']);
                if ($request->header('Transfer-Encoding') == 'chunked') {
                   return SmsgatewayHelper::return_chunk($r_data);
                }
                return response()->json($r_data, 200);
      
    }

$requiredDocCount = DB::table('permission_required_doc')
    ->where('permission_id', $ptypeid)
    ->where('authority_type_id', 'cand01')
    ->where('required_status', 1)
    ->count();
            $rule = $messages = [];
            if (!empty($role_type_id) || $role_type_id > 0) {

                    // Common rules and messages
                    $commonRules = [
                        'permission_type'   => 'required|not_in:0',
                        'start'             => 'required|date',
                        'end'               => 'required|date|after_or_equal:start',
                        'permsndoc'         => 'array',
                        'permsndoc.*'       => 'mimes:pdf|max:50120', // Max 5MB
                        
                    ];

                    $commonMessages = [
                        'permission_type.required' => 'Please Select Permission Type!',
                        'start.date'               => 'Event Start date should be a valid date!',
                        'end.after_or_equal'       => 'Event end Time should be greater than or equal to Event start time!',
                        'permsndoc.required'       => 'Please Upload Permission Documents!',
                        'permsndoc.*.mimes'        => 'Please Upload Only PDF File!',
                        'permsndoc.*.max'          => 'File size should not exceed 5MB!',
                        
                    ];
 if ($requiredDocCount > 0) {
    $commonRules['permsndoc'] = 'required|array|min:' . $requiredDocCount;
}
                    // Role-specific rules and messages
                    if ($role_type_id == 5) {
                        $rule = array_merge($commonRules, [
                            'district' => 'required|not_in:0'
                        ]);
                        $messages = array_merge($commonMessages, [
                            'district.required' => 'Please Select District Name!'
                        ]);

                   

                    } elseif ($role_type_id == 4) {
                        // No additional rules for role_type_id 4
                        $rule = $commonRules;
                        $messages = $commonMessages;
                    } else {
                        // Additional rules for other role_type_ids
                        $rule = array_merge($commonRules, [
                            'district'        => 'required|not_in:0',
                            'ac'              => 'required|not_in:0',
                            'police_station'  => 'required|not_in:0',
                            'location1'       => 'required|not_in:0'
                            //'Other_location'    => 'required_if:location1,other' // Conditional required if location1 is 'other'
                        ]);
                        $messages = array_merge($commonMessages, [
                            'district.required'       => 'Please Select District Name!',
                            'ac.required'             => 'Please Select Assembly Constituency!',
                            'police_station.required' => 'Please Select Police Station!',
                            'location1.required'      => 'Please Select Event Place Name!'
                            //'Other_location.required_if' => 'Please specify the other location when "Other" is selected!'
                        ]);
                             }



            }
        
            $validator = Validator::make($request->all(), $rule, $messages);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }

 if ($role_type_id == 5) {
                       

                       $districtExists = DB::table('m_district')
                    ->where('ST_CODE', $sid)
                    ->where('dist_no', $request->district)
                    ->exists();

if (!$districtExists) {
    $response_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
        "status" => false, 
        "error" => 'Please select a valid district first, then apply permission'
    ]);

    return response()->json($response_data, 200); 
}

                    }

                     if ($role_type_id == 19) {
                               $assemblyExists = DB::table('m_ac')
        ->where('ST_CODE', $sid)
        ->where('DIST_NO_HDQTR', $request->district)
        ->where('ac_no', $request->ac)
        ->exists();

    if (!$assemblyExists) {
        $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
            "status" => false,
            "error" => 'Please Select valid Assembly first then apply permission'
        ]);
        return response()->json($r_data, 200);
    }

    // Check if a valid police station exists
    $policeStationExists = DB::table('police_station_master')
        ->where('ST_CODE', $sid)
        ->where('ac_no', $request->ac)
        ->where('id', $request->police_station)
        ->exists();

    if (!$policeStationExists) {
        $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
            "status" => false,
            "error" => 'Please Select valid Police station first then apply permission'
        ]);
        return response()->json($r_data, 200);
    }

    // Check if a valid location exists, only if location1 is not 'other'
    if ($request->location1 !== 'other') {
        $locationExists = DB::table('location_master')
            ->where('st_code', $sid)
            ->where('ac_no', $request->ac)
            ->where('id', $request->location1)
            ->exists();

        if (!$locationExists) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => 'Please Select valid Location first then apply permission'
            ]);
            return response()->json($r_data, 200);
        }
    }
       
                    }
            $ed = date_create($request->electiondate);

    $checkElectionDate = DB::table('m_schedule')
    ->where('ELECTION_ID', $usermaster->election_id)
    ->where('DATE_POLL', $ed)
    ->value('DATE_POLL');
    

if (!$checkElectionDate) {
    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Please select a valid election schedule date']);
                return response()->json($r_data, 200);
  
}

            if ($ed != false) {
                $election_date = date_sub($ed, date_interval_create_from_date_string("2 days"));
                $final_election_date = date_format($election_date, "d-m-Y");

                if (strtotime($final_election_date) <  strtotime(date('d-m-Y', strtotime($request->end)))) {

                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'You can not apply for permission because election Pollday date is ' . $request->electiondate . ' .']);
                    return response()->json($r_data, 200);
                }
            } else {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'You can not apply for permission as ' . $request->electiondate . ' .']);
                return response()->json($r_data, 200);
            }

            $startdate = $request->start;
            $enddate = $request->end;
            $cur_timenow = Carbon::now();
            $addtimestart = $cur_timenow->addHours(48);
            $addtime1 = $addtimestart->format('d-m-Y H:m:s');

            if ($khs != '0') {

                if (strtotime($startdate) < strtotime($addtime1)) {
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Permission to be applied 48 hour before !']);
                    return response()->json($r_data, 200);
                }
            }

            $getpermissonmasteid = DB::table('permission_type')
    ->where('ST_CODE', $sid)
    ->where('id', $ptypeid)
    ->value('permission_type_id');

$getdayvalidity = DB::table('restriction_day_master')
    ->where('ST_CODE', $sid)
    ->where('permission_type_id', $getpermissonmasteid)
    ->value('restriction_day');

// Change condition to >= to include 3
if ($getdayvalidity <= 3) {
    $getdayvalidity = 7;
}

// Get and parse the start and end dates
$startdate1 = Carbon::parse($request->start);
$enddate1 = Carbon::parse($request->end);
$cur_timenow1 = Carbon::now(); // Get the current date and time


$daysDifference = $cur_timenow1->diffInDays($enddate1);

// Check if start and end dates are the same
// if ($startdate1->isSameDay($enddate1)) {
//     $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
//         'status' => false,
//         'error' => 'Submission is not possible because the Start Date & Time and End Date & Time cannot be the same.'
//     ]);
//                     return response()->json($r_data, 200);
   
// }

// Check if both start and end dates are less than the current date
if ($startdate < $cur_timenow1 && $enddate1 < $cur_timenow1) {
      $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
        'status' => false,
        'error' => 'Submission is not possible because both the start Date and end Date are earlier than the current date.'
    ]);
                    return response()->json($r_data, 200);
  
}

// Check conditions for submission
if ($daysDifference < $getdayvalidity) {
    
} else {
    // Submission is not allowed
      $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
        'status' => false,
        'error' => 'Submission is not possible because the count of days from the current date to the end date is ' . $daysDifference . ', which is greater than the permissions valid days of ' . $getdayvalidity . '.'
    ]);
                    return response()->json($r_data, 200);
   
}

           


                    $pidarr = explode('#', $request->permission_type);
                    $pid_type_id = $pidarr[0];

                    if ($pid_type_id) {
                        $doc_data = $doc_data ?? [];
                        $datatotal = DB::table('permission_required_doc')
                            ->where('permission_id', $pid_type_id)
                            ->where('authority_type_id', 'cand01')
                            ->count('authority_type_id'); // Count authority_type_id column

                        if (count($doc_data) > $datatotal) {
                            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'File count exceeds the limit']);
                return response()->json($r_data, 200);
                           
                        }

                        if (!empty($doc_data) && is_array($doc_data) && count($doc_data) > 0) {
                            $doc_names = '';
                            foreach ($doc_data as $key => $file) {

                                $contentFileS = file_get_contents($file);
                                    $checkArrF=[ "<script", "<?php"];
                                    foreach ($checkArrF as $checkArrv) {
                                      if(stristr( strtolower($contentFileS), $checkArrv )){
                                     $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "Invalid file format, Please attach .pdf file"]);
                                          return response()->json($r_data, 200);
                                      
                                     }
                                   }
                                   $extMme = explode(".", $file->getClientOriginalName());

                                if(count($extMme) > 2) {
                                 $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "Invalid file format, Please attach .pdf file"]);
                                          return response()->json($r_data, 200);       
                                   // return response()->json(["status" => false, "message" => "Invalid file format, Please attach .pdf file"], 200);
                                }   

                                $doc = preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
                                $name = pathinfo($doc, PATHINFO_FILENAME);
                                $name = preg_replace('/[^A-Za-z0-9\-_]/', '', $name);
                                $extension = $file->getClientOriginalExtension();
                                $size = $file->getSize();
                                $time = Carbon::now()->timestamp;
                                $newFilename = 'uploads1/userdoc/permission-document/' . $usermaster->election_id . '/' . $sid . '/' . $user->id . '_' . $sid . '_' . $time . '_' . mt_rand(6, 1000000) . '.' . $extension;
                                $destinationPath3 = public_path('/uploads1/userdoc/permission-document/' . $usermaster->election_id . '/' . $sid);

                                // Move the file to the destination path
                                $file->move($destinationPath3, $newFilename);
                                $doc_names .= $newFilename . ',';
                            }

                           
                        } //else {
                        //      $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'No files uploaded']);
                        //                   return response()->json($r_data, 200);

                        
                       // }
                    } else {
                         $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid permission type']);
                                          return response()->json($r_data, 200);
                       
                    }



            // Prepare permission data

            $permission = new PermissionModel;
            $data['user_id']                 = $user->id;
            $data['st_code']                 = $sid;
            $data['dist_no']                = $request->district;
            $data['ac_no']                     = $request->ac;
            $data['party_id']                     = $usermaster->party_id;
            $data['permission_type_id']     = $request->permission_type;
            /* insert filename */
            if (($data['required_files'] = $doc_names) != null) {
                $data['required_files'] = $doc_names;
            } else {
                $data['required_files'] = 'null';
            }
            /* insert fileserver path */
            $data['fileserver_dir']            = 'uploads1';
            /* insert location */
            if (!empty($request->location1)) {
                $data['location_id']  = strip_tags($request->location1);
            } else {
                $data['location_id']  = '0';
            }

            //$data['location_id'] 			= $request->location1;

            $data['Other_location']         = strip_tags($request->other);

            if (($data['Other_location']) != null) {
                $data['Other_location'] = strip_tags($request->other);
            } else {
                $data['Other_location'] = 'NULL';
            }
            $data['latitude']                 = 'NULL';
            $data['longitude']                 = 'NULL';
            $timestamp = date('Y-m-d H:i:s', strtotime($request->start));
            $timestamp1 = date('Y-m-d H:i:s', strtotime($request->end));
            $data['date_time_start']         = $timestamp;

            $data['date_time_end']             = $timestamp1;
            $data['permission_request.assigned_police_st_id']     = $request->police_station;
            $data['draft_status']             = '0';
            $data['approved_status']         = '0';
            $data['permission_mode']        = '1';
            /* $today=explode(' ',Carbon::today()); */
            $data['added_at']                = Carbon::now();
            $data['created_at']             = Carbon::now();
            $data['updated_at']             = Carbon::now();
            $data['created_by']             = $user->id;
            $data['updated_by']             = '0';
            $data['election_id']            = $usermaster->election_id;
            $data['did']            = $request->did;
            $data['device_name']            = $request->device_name;
            $res = $permission->create($data);
            $LastInsertId = DB::getPdo()->lastInsertId();
            $update_request_status = DB::table('user_login')->where('id', $request->userid)->update(['permission_request_status' => '1']);
            $query = DB::table('permission_request')->where('id', $LastInsertId)->get();

            $query_ptype = DB::table('permission_type')->select('role_id')->where('id', $query[0]->permission_type_id)->get();

            $query_officer = DB::table('officer_login');
            if ($query[0]->ac_no) {
                if ($query_ptype[0]->role_id == 5) {
                    $query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code)->where('dist_no', $query[0]->dist_no);
                } else if ($query_ptype[0]->role_id == 4) {
                    $query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code);
                } else {
                    $query_officer->where('ac_no', $query[0]->ac_no)->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code)->where('dist_no', $query[0]->dist_no);
                }
            } else {
                if ($query_ptype[0]->role_id == 5) {
                    $query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code)->where('dist_no', $query[0]->dist_no);
                } else {
                    $query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code);
                }
            }
            $recorrd = $query_officer->first();
          $refrenceID=$usermaster->election_id . 'AC' . $LastInsertId;
            $a = PermissionModel::where('id', $LastInsertId)->update([
                'reference_id' => $usermaster->election_id . 'AC' . $LastInsertId
            ]);
            if (!empty($recorrd) && $recorrd != null) {
                //dd($LastInsertId);
                if ($recorrd->Phone_no != '') {
                    $permsn_details = DB::table('permission_request as a')
                        ->join('permission_type as b', 'b.id', '=', 'a.permission_type_id')
                        ->join('permission_master as c', 'c.id', '=', 'b.permission_type_id')
                        ->where('a.id', $LastInsertId)
                        ->select('a.reference_id', 'a.added_at', 'c.permission_name')
                        ->get()->first();
                    //dd($permsn_details);
                    $mob_message = "A New Request has been received for " . $permsn_details->permission_name . "-" . $permsn_details->reference_id . " " . $permsn_details->added_at . ", ECI";
                    Messgup::dispatch($recorrd->Phone_no, $mob_message)->onQueue('Messgup');
                }
            }


            if (!empty($LastInsertId)) {

                $pidarr = explode('#', $request->permission_type);
                $pid_type_id = $pidarr[0];

                $data1 = DB::table('permission_required_doc')
                    ->select('authority_type_id')
                    ->where('permission_id', $pid_type_id)
                    ->get()
                    ->toArray();

                foreach ($data1 as $doc_auth) {
                   
                    $nodaldetails1 = DB::table('authority_masters as a')
                        ->leftJoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                        ->select('a.id', 'a.name', 'b.auth_type_id')
                        ->where('a.st_code', $sid)
                        ->where('b.auth_type_id', $doc_auth->authority_type_id)
                        ->where('b.is_active', 1);

                    // Handle conditions based on role_type_id
                    if ($role_type_id == '4') {
                        $nodaldetails1->where('b.dist_no', 0)->where('b.ac_no', 0);
                    } elseif ($role_type_id == '5') {
                        $nodaldetails1->where('b.dist_no', $request->district)->where('b.ac_no', 0);
                    } elseif ($role_type_id == '19') {
                        $nodaldetails1->where('b.ac_no', $request->ac);
                    }

                    // Execute the query and convert the result to an array
                    $nodaldetails1 = $nodaldetails1->get()->toArray();


                    if (!empty($nodaldetails1)) {
                        for ($i = 0; $i < count($nodaldetails1); $i++) {
                            if (!empty($nodaldetails1[$i])) {
                                $today = explode(' ', Carbon::today());
                                $nodaldata = array(
                                    'permission_request_id' => $LastInsertId,
                                    'authority_id' => $nodaldetails1[$i]->id,
                                    'accept_status' => 0,
                                    'added_at' => $today[0],
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                );
                                $insert = DB::table('permission_assigned_auth')->insert($nodaldata);

                                // Push Notification
                                $type = 'Nodal';
                                $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails1[$i]->id)->first();

                                $state = DB::table('m_state')->where('ST_CODE', $sid)->select('ST_NAME')->first();
                                $district = DB::table('m_district')->where('ST_CODE', $sid)->orWhere('DIST_NO', $request->district)->select('DIST_NAME')->first();

                                // if (!empty($state) && !empty($district)) {
                                //     $msg = 'New permission has been received at ' . Carbon::now() . ' From ' . $district->DIST_NAME . ',' . $state->ST_NAME;
                                // }

                               
                            }
                        }
                    }
                }
            }
            DB::commit();
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => true,
                'message' => 'Permission request stored successfully',
                'reference_id' =>  $refrenceID
            ]);
            return response()->json($r_data, 200);
        } catch (Exception $e) {
            DB::rollback();
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                "status" => false,
                "error" => 'An error occurred: ' . $e->getMessage()
            ]);
            return response()->json($r_data, 200);
        }
    }

    public function AllPermissionRequest(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                "access_token" => "required|string",
            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
            ]);

            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $accessToken = trim($inputs["access_token"]);
            // $mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();

            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    $applied_permission = DB::table('permission_request')
                        ->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
                        ->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
                        ->where('user_id', '=', $newuser->id)
                        ->select('permission_request.reference_id', 'permission_request.id as permission_id', 'm.permission_name', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.added_at', 'permission_request.updated_at', 'permission_request.approved_status', 'permission_request.location_id')
                        ->groupBy('permission_id')
                        ->orderBy('permission_request.id', 'DESC')
                        ->get();

                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  [
                        "status" => true,
                        "message" => 'Get All Permission Request Successfully',
                        "data" => [
                            // "token" => $accessToken,
                            "AllPermissionRequestlist" => $applied_permission,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
                }
            }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            throw $th;

            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }
    public function getPermissionDetails(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'access_token' => 'required|string',
                'id' => 'required|numeric|exists:permission_request',
                'status' => 'required|numeric',
                'location' => 'required|string|max:10',
            ], [
                // Custom error messages
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a valid string.',
                'id.required' => 'The ID is required.',
                'id.numeric' => 'The ID must be a valid number.',
                'status.required' => 'The status is required.',
                'status.numeric' => 'The status must be a valid number.',
                'location.required' => 'The location is required.',
                'location.string' => 'The location must be a valid string.',
                'location.max' => 'The location cannot exceed 10 characters.',
            ]);




            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,  ["status" => false, "error" => $validator->errors()->first()]);
                return response()->json($r_data, 200);
            }
            $inputs = $request->all();
            $id = $request->input('id');
            $status = $request->input('status');
            $location = $request->input('location');
            $accessToken = trim($inputs["access_token"]);
            // $mobile = trim($inputs["mobile"]);
            $newuser = User::where([["access_token", "=", $accessToken]])
                ->first();


            if (isset($newuser)) {
                if ($newuser->access_token == $accessToken) {
                    $result = DB::table('permission_request')
                        ->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
                        ->leftjoin('location_master', 'location_master.id', '=', 'permission_request.location_id')
                        ->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
                        ->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
                        ->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
                        ->leftjoin('m_district as district', function ($join) {
                            $join->on('district.DIST_NO', '=', 'permission_request.dist_no')
                                ->on('district.ST_CODE', '=', 'permission_request.st_code');
                        })
                        ->leftjoin('m_ac as ac', function ($join) {
                            $join->on('ac.AC_NO', '=', 'permission_request.ac_no')
                                ->on('ac.ST_CODE', '=', 'permission_request.st_code')
                                ->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
                        })
                        ->where(['permission_request.id' => $id, 'permission_request.user_id' => $newuser->id])
                        ->where([
                            'permission_request.approved_status' => $status,



                        ])
                        ->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'location_master.location_name', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.reference_id', 'permission_request.assigned_police_st_id', 'location_master.location_details', 'permission_request.fileserver_dir', 'permission_request.dist_no')
                        ->groupBy('user_data.name')
                        ->get()->toArray();


                    $pdf = DB::table('permission_request_comment')->where('permission_request_id', $id)->get();
                    // dd($result, $pdf);
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        "status" => true,
                        "message" => 'Get Permission Details Successfully',
                        "data" => [
                            // "token" => $accessToken,
                            "permissiondetails" => $result,
                            "order_permission" => $pdf,
                        ],
                    ]);
                    return response()->json($r_data, 200);
                }else{
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
                }
            }else{
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => 'Invalid Token provided']);
                return response()->json($r_data, 200);
            }
        } catch (\Throwable $th) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, ["status" => false, "error" => "something went wrong"]);
            return response()->json($r_data, 200);
        }
    }
}