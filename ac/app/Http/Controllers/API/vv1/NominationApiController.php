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
use Illuminate\Support\Facades\Response;

class NominationApiController extends Controller
{
    public function __construct()
    {
        $this->xssClean = new xssClean;
        $this->commonModel = new commonModel();
        $this->ResponseMethod = new ResponseController;
        $this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
        $this->ok_response = $this->ResponseMethod::HTTP_OK;
        $this->okStatus = true;
        $this->errStatus = "error";
    }
    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;
    public $bad_response;
    public $ok_response;
    public $okStatus;
    public $errStatus;
    protected $gcmkey = "ed8cf08edc53edfr";
    protected $gcmiv = "3436jnha98fab441";
    


    public function getnominationlist(Request $request)
    {
        try {
            

            // Validation happens here after checking and setting candidate_id
            $validator = Validator::make($request->all(), [
                'access_token' => 'required|string',
                'candidateId' => 'required|numeric',
            ], [
                'access_token.required' => 'The access token is required.',
                'access_token.string' => 'The access token must be a string.',
                'candidateId.required' => 'The candidate ID is required.',
                'candidateId.numeric' => 'The candidate ID must be a numeric value.',
            ]);

            // If validation fails, return a bad response with error message
            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ]);
                return response()->json($r_data, 200);
                
            }
            $inputs = $request->all();
            $accessToken = trim($inputs['access_token']);
            $candidate_id = trim($inputs['candidateId']);

            // Fetch user based on access token
            $loginuser = User::where('access_token', $accessToken)->first();

            // Check if candidate_id is empty
            if (empty($candidate_id) || $candidate_id==0) {
                // If candidateId is not provided, find candidate based on the user's mobile
                $loginDb = DB::table('v_candidate_personal_detail')
                    ->where('cand_mobile', $loginuser->mobile)
                    ->first();

                if ($loginDb) {
                    // Fetch party details and fallback to default if not available
                    $partyDetails = DB::table('v_candidate_nomination_detail')
                        ->where('candidate_id', $loginDb->candidate_id)
                        ->first();

                    $partyId = !empty($partyDetails->party_id) ? $partyDetails->party_id : '1180';

                    // Update or create the user
                    $user = User::updateOrCreate(
                        ['mobile' => $loginDb->cand_mobile],
                        [
                            'name' => $loginDb->cand_name,
                            'candidate_id' => $loginDb->candidate_id,
                            'authority_id' => "0",
                            'role_id' => '2',
                            'party_id' => $partyId,
                          // 'device_id' => $deviceId,
                            'device_type' => 'Mobile',
                            'otp_attempt' => '0',
                            'isActive' => 1,
                            'created_at' => now(),
                            'verify_otp' => '0',
                           // 'app_id' => $app_id
                        ]
                    );

                    $candidate_id = intval($loginDb->candidate_id); // Set candidate_id for further usage
                } else {
                    // If no candidate data is found
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        'status' => false,
                        'error' => 'No nomination data found.'
                    ]);
                return response()->json($r_data, 200);
                }
            }

            // Fetch user based on access token and candidate ID
            $newuser = User::where('access_token', $accessToken)
                ->where('candidate_id', $candidate_id)
                ->first();

            if (!$newuser) {
                 $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,[
                    "status" => false,
                    "error" => "Invalid access token"
                ]);
                return response()->json($r_data, 200);
            }

            // Fetch nomination details if user exists
            $nom_d = DB::table('v_candidate_nomination_detail')
                ->where('candidate_id', $candidate_id)
                ->get();

            if (count($nom_d) > 0) {
                $dat = [];
                foreach ($nom_d as $k) {
                    $ac = $k->ac_no != null ? trim($this->commonModel->getacbyacno($k->st_code, $k->ac_no)->AC_NAME) : '';
                    $pc = $k->pc_no != null ? trim($this->commonModel->getpcbypcno($k->st_code, $k->pc_no)->PC_NAME) : '';

                    // Fetch candidate and party details
                    $candidateData = DB::table('v_candidate_nomination_detail as cnd')
                        ->join('v_candidate_personal_detail as cpd', 'cnd.candidate_id', '=', 'cpd.candidate_id')
                        ->join('m_party as mp', 'cnd.party_id', '=', 'mp.CCODE')
                        ->join('m_ac as mpw', function ($join) {
                            $join->on('cpd.candidate_residence_acno', '=', 'mpw.ac_no')
                                ->on('cpd.candidate_residence_stcode', '=', 'mpw.st_code');
                        })
                        ->join('m_state as mpg', 'cpd.candidate_residence_stcode', '=', 'mpg.st_code')
                        ->where('cpd.candidate_id', $candidate_id)
                        ->where('mp.CCODE', $nom_d[0]->party_id)
                        ->orderBy('mp.PARTYNAME', 'ASC')
                        ->select(
                            'mpw.AC_NAME as constituency_name',
                            'mpw.AC_NO as constituency_number',
                            'cnd.st_code as state_code',
                            'cpd.cand_name as candidate_name',
                            'cpd.cand_age as candidate_age',
                            'mp.PARTYNAME as party_name'
                        )
                        ->first();

                    $const_type = DB::table('m_election_history')
                        ->where('election_id', $nom_d[0]->election_id)
                        ->select('const_type', 'id', 'elect_type')
                        ->orderBy('id', 'DESC')
                        ->first();

                    // Populate response data
                    $dat[] = [
                        "nomId" => $k->nom_id,
                        "stateName" => trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                        'districtName' => trim($this->commonModel->getdistrictbydistrictno($k->st_code, $k->district_no)->DIST_NAME),
                        "PCName" => $pc,
                        "ACName" => $ac,
                        "application_status" => ucwords(trim($this->commonModel->getnameBystatusid($k->application_status))),
                        "application_status_data" => $k->application_status
                    ];
                }

                // Success response
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        'status' => true,
                        'message' => 'Nomination details retrieved successfully',
                        'data' => [
                            'candidateId' => intval($candidate_id),
                            'nominationData' => $dat,
                            'personalDetails' => $candidateData,
                            'session' => '1',
                            'nom_election_id'=>$const_type->id,
                        ]
                    ]);
                return response()->json($r_data, 200);
            } else {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,[
                        'status' => false,
                        'error' => 'No nomination data found',
                        'data' => [],
                        'session' => '1'
                    ]);
                return response()->json($r_data, 200);
            }
        } catch (Exception $ex) {
            $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,[
                'status' => false,
                'error' => 'Internal Server Error'
            ]);
                return response()->json($r_data, 200);
        }
    }



   
    public function nominationstatus(Request $request)
    {
        try {
            // Validate the request inputs
            $validator = Validator::make($request->all(), [
                    'access_token' => 'required|string',
                    'candidateId' => 'required|numeric', 
                    'nomId' => 'required|numeric',
                ], [
                    'access_token.required' => 'The access token is required.',
                    'access_token.string' => 'The access token must be a string.',
                    'candidateId.required' => 'The candidate ID is required.',
                    'candidateId.numeric' => 'The candidate ID must be a numeric value.',
                    'nomId.required' => 'The nomination ID is required.',
                    'nomId.numeric' => 'The nomination ID must be a numeric value.',
                ]);


            // Return a bad response with validation error message
            if ($validator->fails()) {
                $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ]);
                return response()->json($r_data, 200);
            }

            // Extract inputs
            $inputs = $request->all();
            $accessToken = trim($inputs['access_token']);
            $candidate_id = trim($inputs['candidateId']);
            $nom_id = trim($inputs['nomId']);

            // Fetch user based on access token and candidate ID
            $newuser = User::where('access_token', $accessToken)
                ->where('candidate_id', $candidate_id)
                ->first();

            if ($newuser) {
                // Fetch candidate and nomination details
                $cand_d = DB::table('v_candidate_personal_detail')->where('candidate_id', $candidate_id)->first();
                $nom_d = DB::table('v_candidate_nomination_detail')
                    ->where('nom_id', $nom_id)
                    ->where('candidate_id', $candidate_id)
                    ->get();
                $afidav = DB::table('v_candidate_affidavit_detail')
                    ->where('candidate_id', $candidate_id)
                    ->where('nom_id', $nom_id)
                    ->where('is_deleted', '1')
                    ->get();
                $cand_criminal = DB::table('v_candidate_criminaluploads')
                    ->where('candidate_id', $candidate_id)
                    ->first();

                $const_type = DB::table('m_election_history')
                    ->where('id', $inputs['election_id'])
                    ->first();

                // Define the path for AC/PC based on election type
                $path = (!empty($const_type->const_type) && $const_type->const_type == 'AC') ?
                    'https://suvidha.eci.gov.in/ac/public/' :
                    'https://suvidha.eci.gov.in/pc/public/';

                // Criminal upload details
                $criminal = !empty($cand_criminal) ?
                    ['criminal_link' => $path . '/' . $cand_criminal->path] :
                    ['criminal_link' => ''];

                // Affidavit details
                $affid = [];
                foreach ($afidav as $affi) {
                    $url = isset($affi->ac_no) ? 'https://suvidha.eci.gov.in/ac/public/' : (isset($affi->pc_no) ? 'https://suvidha.eci.gov.in/pc/public/' : '');
                    $affid[] = [
                        "affidavit_link" => $url . $affi->affidavit_path,
                        "affidavit_name" => 'Form 26',
                        "date_time" => $affi->created_at,
                    ];
                }

                // Candidate image
                $candidateImg = !empty($cand_d->cand_image) ? $path . $cand_d->cand_image : '';

                // If nomination data exists
                if (count($nom_d) > 0) {
                    foreach ($nom_d as $k) {
                        $ac = $k->ac_no ? trim($this->commonModel->getacbyacno($k->st_code, $k->ac_no)->AC_NAME) : '';
                        $pc = $k->pc_no ? trim($this->commonModel->getpcbypcno($k->st_code, $k->pc_no)->PC_NAME) : '';
                        $nominationaccept = $k->scrutiny_date ?? '';

                        $dat = [
                            "nomId" => $k->nom_id,
                            "stateName" => trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                            'districtName' => trim($this->commonModel->getdistrictbydistrictno($k->st_code, $k->district_no)->DIST_NAME),
                            "ACName" => $ac,
                            "PCName" => $pc,
                            "application_status" => ucwords(trim($this->commonModel->getnameBystatusid($k->application_status))),
                            "application_status_data" => $k->application_status,
                            "date_of_submit_nomination" => $k->date_of_submit,
                            "accept_nomination_date" => $nominationaccept
                        ];
                    }

                    // Success response
                   $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                        'status' => true,
                        'message' => 'Nomination status retrieved successfully',
                        'data' => [
                            'candidateId' => $candidate_id,
                            'name' => $cand_d->cand_name,
                            'candImage' => $candidateImg,
                            'nominationData' => $dat,
                            'is_criminal' => $cand_d->is_criminal,
                            'affidavit' => $affid,
                            'criminal' => $criminal,
                            'session' => '1',
                        ]
                    ]);
                return response()->json($r_data, 200);
                } else {
                    // No nomination data found
                    $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv,[
                        'status' => false,
                        'error' => 'No nomination data found. Please check your nomId.',
                    ]);
                return response()->json($r_data, 200);
                }
            } else {
                // Invalid access token or candidate ID
               $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                    'status' => false,
                    'error' => 'Invalid access token or candidate ID',
                    'session' => '0'
                ]);
                return response()->json($r_data, 200);
            }
        } catch (Exception $ex) {
            // Internal server error response
             $r_data = SmsgatewayHelper::encrypt_data($this->gcmkey, $this->gcmiv, [
                'status' => false,
                'error' => 'Internal Server Error'
            ]);
                return response()->json($r_data, 200);
        }
    }
}
