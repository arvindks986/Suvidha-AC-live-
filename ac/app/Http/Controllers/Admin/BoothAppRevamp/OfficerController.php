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
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, PsSectorOfficer, TblBoothUserModel, StateModel, AcModel, DistrictModel, BoothAppPollingStation, ImportOfficerModel, PollingStationLocationModel, PollingStationLocationToPsModel, SectorMasterModel, SectorAcPsMappingModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Excel;

class OfficerController extends Controller
{

  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $phase_no      = NULL;
  public $filter_role_id  = NULL;
  public $no_allowed_po   = 2;
  public $no_allowed_blo  = 2;
  public $no_allowed_pro  = 2;
  public $no_allowed_sm   = 2;
  public $cache           = true;

  public function __construct(Request $request)
  {
    $this->commonModel  = new commonModel();
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {

      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id        = $default_values['role_id'];
      $this->filter_role_id = $default_values['filter_role_id'];
      $this->base           = $default_values['base'];
      $this->ps_no           = $default_values['ps_no'];
      $this->phase_no        = $default_values['phase_no'];

      $this->request_param   = http_build_query([
        'st_code' => $this->st_code,
        'ac_no' => $this->ac_no,
        'dist_no' => $this->dist_no,
        'ps_no' => $this->ps_no,
        'phase_no' => $this->phase_no
      ]);
      return $next($request);
    });
  }

  public function import_excel(Request $request)
  {

    $data = [];
    $data['verify_and_upload'] = Common::generate_url("booth-app-revamp/confirm-import");
    $data['heading_title']  = 'List of Polling Officers';
    $data['file_upload']    = Common::generate_url("booth-app-revamp/upload-excel");
    $results    = ImportOfficerModel::where([
      'st_code' => Auth::user()->st_code,
      'district_no' => Auth::user()->dist_no,
      'ac_no' => Auth::user()->ac_no,
      'is_deleted' => 0
    ])->get();
    foreach ($results as $result) {
      $data['designation'] = ($result->role_id == 35) ? 'PRO' : 'PO';
    }
    $data['buttons'] = [];
    $data['buttons'][]  = [
      'name' => 'Download PO Sample Sheet',
      'href' =>  url("excel/po_sample.xls"),
      'target' => true
    ];
    /*  $data['buttons'][]  = [
      'name' => 'Download PRO Sample Sheet',
      'href' =>  url("excel/pro_sample.xls"),
      'target' => true
    ]; */
    $data['results']    = $results;
    $data['user_data']  =  Auth::user();
    return view($this->view . '.officer.import-excel', $data);
  }

  public function upload_excel(Request $request)
  {

    if (!Auth::user()) {
      $json['error'] = "Please login to continue.";
    }

    $json = array();

    if (!$json) {
      // Check if multiple files are uploaded or just one
      $files = array();

      if ($request->hasFile('file')) {

        foreach ($request->file('file') as $key => $value) {

          $file_up  =   $value;
          $filename   =   $file_up->getClientOriginalName();
          $filetype   =   $value->getMimeType();

          $validator = Validator::make(['filename' => $file_up->getClientOriginalName()], ['filename' => 'min:3|max:255'], []);
          if ($validator->fails()) {
            $json['error'] = $validator->getMessageBag()->first();
          }

          if ($file_up->getSize() > 5000000) {
            $json['error'] = "You can only upload max size 4MB file.";
          }

          //Allowed file extension types
          $allowed = array(
            'csv',
            'xlv',
            'xlsx',
          );

          if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
            $json['error'] = "Only CSV File Allowed";
          }

          //Allowed file mime types
          $allowed = array(
            'text/csv',
            'application/vnd.ms-excel',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
          );

          if (!in_array($filetype, $allowed)) {
            $json['error'] = "File Type Not Allowed";
          }

          $error_array    = [];
          $data_to_import = [];

          // $allResults = \Excel::load($file_up, function ($reader) {
          // })->get();

          $allResults=\Excel::toArray([],$file_up);
          // echo "<pre>";print_r($allResults);die;



          $columns = $allResults[0][0];
          if (count($columns) < 3) {
            $error_array[] = "Excel Column mis-matached.";
          }

          $valid_column = [];
          $role_type    = '';
          $role_id      = $request->role_id;

          // if ($role_id == 35) {
          //   $role_type = "pro";
          //   $valid_column = ['ps_no', 'pro1_name', 'pro1_mobile', 'pro2_name', 'pro2_mobile'];
          // } else if ($role_id == 34) {
          //   $role_type = "po";
            $valid_column = ['ps_no', 'po1_name', 'po1_mobile'];
          // }

          foreach ($columns as $key => $column_name) {
            if (!in_array($column_name, $valid_column)) {
              $error_array[] = "Column - " . $column_name . " is not valid column for " . $role_type . ".";
            }
          }

          if (count($error_array) > 0) {
            return Response::json([
              'success' => false,
              'errors'  => $error_array
            ]);
          }
          
          foreach ($allResults[0] as $key => $row) {
            if($key != 0){
              $blo1_mobile = (string)trim($row[2]); // For po1_mobile
              // dd($blo1_mobile);
              // $blo1_mobile = (string)trim($row[$role_type . '1_mobile']);
              //$blo2_mobile = (string)trim($row[$role_type.'2_mobile']);
  
              // if (empty($row['ps_no'])) {
                if (empty($row[0])) {
                $error_array[] = "You can not leave ps_no column as blank.";
              }
  
              if ($blo1_mobile != '' && !preg_match('/^[0-9]{10}$/', $blo1_mobile)) {
                // $error_array[] = $role_type . '1 mobile is not valid for ps no - ' . $row['ps_no'];
                $error_array[] = $role_type . '1 mobile is not valid for ps no - ' . $row[0];
              }
  
              if (count($error_array) == 0) {
                $validate_ps = PollingStation::where([
                  'ST_CODE' => Auth::user()->st_code,
                  'AC_NO'   => Auth::user()->ac_no,
                  // 'PS_NO'   => $row['ps_no'],
                  'PS_NO'   => $row[0],
                ])->count();
                if ($validate_ps == 0) {
                  // $error_array[] = $row['ps_no'] . " not valid ps. please enter a valid number of your ps";
                  $error_array[] = $row[0] . " not valid ps. please enter a valid number of your ps";
                }
              }
  
              if (count($error_array) == 0) {
                if ($blo1_mobile != '' && PollingStationOfficerModel::count_mobile($blo1_mobile)) {
                  $error_array[] = "Mobile number " . $blo1_mobile . " already exist.";
                }
              }

              //validation: if one polling station is assigned with PO. Multiple PO's for one polling station is not allowed.
              $already_assigned_ps_count=PollingStationOfficerModel::
              where('ps_no', $row[0])->where('ac_no',Auth::user()->ac_no)
              ->where('st_code',Auth::user()->st_code)
              ->count();
              if($already_assigned_ps_count>0)
              {
                $error_array[] = "PS ".$row[0]." is already assined with polling officer.";
              }

              if (count($error_array) == 0) {
                $data_to_import[] = [
                  // 'ps_no' => $this->xssClean->clean_input($row['ps_no']),
                  'ps_no' => $this->xssClean->clean_input($row[0]),
                  // 'name1' => ($row[$role_type . '1_name']) ? $this->xssClean->clean_input($row[$role_type . '1_name']) : '',
                  'name1' => ($row[1]) ? $this->xssClean->clean_input($row[1]) : '',
                  // 'mobile1' => ($row[$role_type . '1_mobile']) ? $this->xssClean->clean_input($row[$role_type . '1_mobile']) : '',
                  'mobile1' => ($row[2]) ? $this->xssClean->clean_input($row[2]) : '',
  
                  'role_id' => $this->xssClean->clean_input($role_id),
                ];
              }
            }
          }
          if (count($error_array) > 0) {
            return Response::json([
              'success' => false,
              'errors'  => $error_array
            ]);
          } else {

            $batch_number = 1;
            $batch_object = ImportOfficerModel::select('batch')->orderBy('batch', 'DESC')->groupBy('batch')->first();
            if ($batch_object) {
              $batch_number = $batch_object->batch + 1;
            }

            DB::beginTransaction();
            try {
              $filter_user = [
                'st_code' => Auth::user()->st_code,
                'district_no' => Auth::user()->dist_no,
                'ac_no' => Auth::user()->ac_no,
              ];
              ImportOfficerModel::deactivate_previous($filter_user);
              $filter_user['batch'] = $batch_number;
              foreach ($data_to_import as $iterate_res) {
                ImportOfficerModel::add_officer(array_merge($iterate_res, $filter_user));
              }
            } catch (\Exception $e) {
              return Response::json([
                'error'  => "Please Verify file and try again."
              ]);
            }
            DB::commit();
            return Response::json([
              'data'     => $data_to_import,
              'message'  => "Please verify your data and click finalize.",
              'role_type' => $role_type,
              'success'   => true
            ]);
          }
        }
      }
    }

    if (!$json) {
      $json['success'] = true;
    }

    return Response::json($json);
  }




  public function verify_and_import(Request $request)
  {
    $json = [];
    $error_array = [];
    $results  = ImportOfficerModel::where([
      'st_code' => Auth::user()->st_code,
      'district_no' => Auth::user()->dist_no,
      'ac_no' => Auth::user()->ac_no,
      'is_deleted' => 0
    ])->get();

    $filter = [
      'st_code' => Auth::user()->st_code,
      'ac_no' => Auth::user()->ac_no,
    ];

    foreach ($results as $result) {
      if (count($error_array) == 0) {
        if ($result->mobile1 != '' && PollingStationOfficerModel::count_mobile($result->mobile1)) {
          $error_array[] = "Mobile number " . $result->mobile1 . " already exist.";
        }
        
        //validation: if one polling station is assigned with PO. Multiple PO's for one polling station is not allowed.
        $already_assigned_ps_count=PollingStationOfficerModel::
        where('ps_no', $result->ps_no)->where('ac_no',Auth::user()->ac_no)->where('st_code',Auth::user()->st_code)
        ->count();
        if($already_assigned_ps_count>0)
        {
          $error_array[] = "PS ".$result->ps_no." is already assined with polling officer.";
        }
        $filter = [
          'st_code' => Auth::user()->st_code,
          'ac_no' => Auth::user()->ac_no,
          'mobile' => $result->mobile1,
        ];
        $no_active_officer = PollingStationOfficerModel::count_officer(array_merge($filter, ['ps_no' => $result->ps_no]));
        $no_allowed_po = 2;
        if ($result->role_id == '34' && $no_active_officer > $no_allowed_po) {

          $error_array[] = "You can only add " . $no_allowed_po . " PO to a Polling Station.";
        }

        if ($result->mobile2 != '' && PollingStationOfficerModel::count_mobile($result->mobile2)) {
          $error_array[] = "Mobile number " . $result->mobile2 . " already exist.";
        }
      }
    }

    if (count($error_array) > 0) {
      return Response::json([
        'success' => false,
        'errors'  => $error_array
      ]);
    }

    DB::beginTransaction();
    try {
      foreach ($results as $result) {
        if (trim($result->mobile1) != '') {
          $officer_object = new PollingStationOfficerModel();
          $officer_object->ps_no        = $result->ps_no;
          $officer_object->ac_no        = $result->ac_no;
          $officer_object->st_code      = $result->st_code;
          $officer_object->district_no  = $result->district_no;
          $officer_object->name           = $result->name1;
          $officer_object->mobile_number  = $result->mobile1;
          $officer_object->email          = $result->mobile1;
          $officer_object->designation    = $result->role_id;
          $officer_object->role_id    = $result->role_id;
          $officer_object->is_import      = $result->batch;
          $officer_object->role_level     = 1;
          $officer_object->is_active     = 0;
          $officer_object->save();
        }

        if (trim($result->mobile2) != '') {
          $officer_object = new PollingStationOfficerModel();
          $officer_object->ps_no        = $result->ps_no;
          $officer_object->ac_no        = $result->ac_no;
          $officer_object->st_code      = $result->st_code;
          $officer_object->district_no  = $result->district_no;
          $officer_object->name           = $result->name2;
          $officer_object->mobile_number  = $result->mobile2;
          $officer_object->email          = $result->mobile2;
          $officer_object->designation    = $result->role_id;
          $officer_object->role_id    = $result->role_id;
          $officer_object->is_import      = $result->batch;
          $officer_object->role_level     = 2;
          $officer_object->is_active     = 0;
          $officer_object->save();
        }
      }
      ImportOfficerModel::where([
        'st_code' => Auth::user()->st_code,
        'district_no' => Auth::user()->dist_no,
        'ac_no' => Auth::user()->ac_no,
      ])->update(['is_deleted' => 1]);
    } catch (\Exception $e) {
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    Session::flash('status', 1);
    Session::flash('flash-message', "Data has been imported successfully.");
    return Response::json([
      'success' => true
    ]);

    return Response::json($json);
  }




  public function get_polling_station(Request $request)
  {

    $data = [];
    $request_array = [];

    //set title
    $title_array  = [];
    $data['heading_title'] = 'List of Polling Officers';

    if ($this->st_code) {
      $state_object = StateModel::get_state_by_code($this->st_code);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }
    if ($this->ac_no) {
      $ac_object = AcModel::get_ac(['state' => $this->st_code, 'ac_no' => $this->ac_no]);
      if ($ac_object) {
        $title_array[]  = "AC: " . $ac_object['ac_name'];
      }
    }
    $data['filter_buttons'] = $title_array;


    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

    //buttons
    $data['buttons']    = [];
    $data['action']     = url($this->action . '/officer-list');
    $data['reset_otp_link']  = Common::generate_url('booth-app-revamp/reset_otp');

    $results                = [];
    $filter_election = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => true,
      'limit'     => 30,
      'restricted_ps' => NULL
    ];

    $data['add_new_url'] = Common::generate_url($this->action . '/officer-list/add/0');
    $data['add_sm_url'] = Common::generate_url($this->action . '/assign-blo');


    $max_po           = [];
    $max_blo          = [];
    $max_sm           = [];
    $data['results']  = [];
    //$results    =   PollingStation::get_polling_stations($filter_election);

    $results = BoothAppPollingStation::get_assign_officers($filter_election);

    $data['pag_results'] = $results;
    foreach ($results as $result) {
      $blo  = [];
      $pro  = [];
      $po   = [];
      $sm   = [];

      $officers = PollingStationOfficerModel::get_officers(array_merge($filter_election, ['ps_no' => $result->ps_no]));

      foreach ($officers as $officer) {

        if ($officer['role_id'] == '33') {
          $blo[$officer['role_level']] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'href'    => Common::generate_url($this->action . '/officer-list/add/' . encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active']) ? 'Enable' : 'Disable',
            'role_level' => $officer['role_level']
          ];
        }
        if ($officer['role_id'] == '35') {
          $pro[$officer['role_level']] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'href'    => Common::generate_url($this->action . '/officer-list/add/' . encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active']) ? 'Enable' : 'Disable',
            'role_level' => $officer['role_level']
          ];
        }
        if ($officer['role_id'] == '34') {
          $po[$officer['role_level']] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'href'    => Common::generate_url($this->action . '/officer-list/add/' . encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active']) ? 'Enable' : 'Disable',
            'role_level' => $officer['role_level']
          ];
        }
      }

      $sm_officers = PsSectorOfficer::get_sos(array_merge($filter_election, ['ps_no' => $result->ps_no]));
      foreach ($sm_officers as $iterate_sm) {
        $sm[$iterate_sm['role_level']] = [
          'name'    => $iterate_sm['name'],
          'mobile'  => $iterate_sm['mobile_number'],
          'href'    => Common::generate_url($this->action . '/officer-list/add/' . encrypt_string($iterate_sm['id'])),
          'is_active' => ($iterate_sm['is_active']) ? 'Enable' : 'Disable',
          'role_level' => $iterate_sm['role_level']
        ];
      }


      uasort($po, function ($a, $b) {
        return $a['role_level'] - $b['role_level'];
      });

      uasort($blo, function ($a, $b) {
        return $a['role_level'] - $b['role_level'];
      });

      uasort($pro, function ($a, $b) {
        return $a['role_level'] - $b['role_level'];
      });

      uasort($sm, function ($a, $b) {
        return $a['role_level'] - $b['role_level'];
      });

      $max_po[]     = count($po);
      $max_blo[]    = count($blo);
      $max_pro[]    = count($pro);
      $max_sm[]     = count($sm);
      $data['results'][] = [
        'ps_no'   => $result->ps_no,
        'ps_name' => $result->ps_name,
        'blo'     => $blo,
        'pro'     => $pro,
        'po'      => $po,
        'sm'      => $sm
      ];
    }


    $data['max_po']     =  $this->no_allowed_po; //max($max_po);
    $data['max_blo']    =  $this->no_allowed_blo; //max($max_po);
    $data['max_pro']    =  $this->no_allowed_pro; //max($max_po);
    $data['max_sm']     =  $this->no_allowed_sm; //max($max_po);

    $data['user_data']  =  Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view . '.officer-list', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to($this->base . '/dashboard');
    }
  }

  public function add_officer($id = 0, Request $request)
  {

    $data = [];
    $request_array = [];

    $data['st_code']  = NULL;
    $data['ac_no']    = NULL;
    if ($request->has('st_code')) {
      $data['st_code'] = base64_decode($request->st_code);
      $request_array[] = 'state=' . $request->st_code;
    }
    if ($request->has('ac_no')) {
      $data['ac_no'] = base64_decode($request->ac_no);
      $request_array[] = 'ac_no=' . $request->ac_no;
    }


    if ($this->role_id == '19') {
      $data['st_code'] = $this->st_code;
      $data['ac_no'] = $this->ac_no;
    }

    //set title
    $title_array  = [];
    $data['heading_title'] = 'Add/Edit Officer Form';

    if ($data['st_code']) {
      $state_object = StateModel::get_state_by_code($data['st_code']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }
    if ($data['ac_no']) {
      $ac_object = AcModel::get_ac(['state' => $data['st_code'], 'ac_no' => $data['ac_no']]);
      if ($ac_object) {
        $title_array[]  = "AC: " . $ac_object['ac_name'];
      }
    }
    $data['filter_buttons'] = $title_array;

    if (Auth::user()->role_id == '4') {
      $data['state']  = Auth::user()->st_code;
    }

    $states = StateModel::get_states();
    $data['states'] = [];
    foreach ($states as $result) {
      if (Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code) {
        $data['states'][] = [
          'code' => base64_encode($result->ST_CODE),
          'name' => $result->ST_NAME,
        ];
      }

      if (Auth::user()->role_id == '7' || Auth::user()->role_id == '27') {
        $data['states'][] = [
          'code' => base64_encode($result->ST_CODE),
          'name' => $result->ST_NAME,
        ];
      }
    }

    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action . '/officer-list'),
      'name' => 'List of Officers',
      'target' => false,
    ];
    $data['action']         = Common::generate_url($this->action . '/officer-list/post');
    $filter_election = [
      'st_code'   => $data['st_code'],
      'ac_no'     => $data['ac_no'],
      'paginate'  => true,
      'restricted_ps' => []
    ];


    $results   = [];
    $filter_ps = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => false,
      'restricted_ps' => []
    ];


    $data['polling_stations'] = [];
    $results    =   PollingStation::get_polling_stations($filter_ps);
    foreach ($results as $result) {
      $data['polling_stations'][] = [
        'ps_no'   => $result['PS_NO'],
        'ps_name' => $result['PS_NAME_EN'],
      ];
    }

    if ($id != '0') {
      $data['encrpt_id'] = $id;
      $id     = decrypt_string($id);
      $object = PollingStationOfficerModel::get_officer(['id' => $id]);
    }

    $data['id'] = $id;

    if ($request->old('ps_no')) {
      $data['ps_no']  = $request->old('ps_no');
    } else if (isset($object) && $object) {
      $data['ps_no']  = $object['ps_no'];
    } else {
      if ($request->has('ps_no')) {
        $data['ps_no']  = $request->ps_no;
      } else {
        $data['ps_no']  = '';
      }
    }

    if ($request->old('name')) {
      $data['name']  = $request->old('name');
    } else if (isset($object) && $object) {
      $data['name']  = $object['name'];
    } else {
      $data['name']  = '';
    }

    if ($request->old('mobile')) {
      $data['mobile']  = $request->old('mobile');
    } else if (isset($object) && $object) {
      $data['mobile']  = $object['mobile_number'];
    } else {
      $data['mobile']  = '';
    }

    if ($request->old('status')) {
      $data['status']  = $request->old('status');
    } else if (isset($object) && $object) {
      $data['status']  = $object['is_active'];
    } else {
      $data['status']  = '';
    }

    if ($request->old('is_pro_right')) {
      $data['is_pro_right']  = $request->old('is_pro_right');
    } else if (isset($object) && $object) {
      $data['is_pro_right']  = $object['pro_override'];
    } else {
      $data['is_pro_right']  = '';
    }

    if ($request->old('is_testing')) {
      $data['is_testing']  = $request->old('is_testing');
    } else if (isset($object) && $object) {
      $data['is_testing']  = $object['is_testing'];
    } else {
      $data['is_testing']  = 0;
    }


    if ($request->old('role_id')) {
      $data['role_id']  = $request->old('role_id');
    } else if (isset($object) && $object) {
      $data['role_id']  = $object['role_id'];
    } else {
      if ($request->has('role_id')) {
        $data['role_id']  = $request->role_id;
      } else {
        $data['role_id']  = '';
      }
    }

    if ($request->old('role_level')) {
      $data['role_level']  = $request->old('role_level');
    } else if (isset($object) && $object) {
      $data['role_level']  = $object['role_level'];
    } else {
      if ($request->has('role_level')) {
        $data['role_level']  = $request->role_level;
      } else {
        $data['role_level']  = 0;
      }
    }

    if ($request->old('pin')) {
      $data['pin']  = $request->old('pin');
    } else if (isset($object) && $object) {
      $data['pin']  = $object['pin'];
    } else {
      $data['pin']  = '';
    }

    if ($request->old('pin_confirmation')) {
      $data['pin_confirmation']  = $request->old('name');
    } else {
      $data['pin_confirmation']  = '';
    }

    $data['roles']      = [
      [
        'role_id' => '',
        'name'    => 'Select',
      ],
      [
        'role_id' => '33',
        'name'    => 'BLO',
      ],
      [
        'role_id' => '34',
        'name'    => 'PO',
      ],
      [
        'role_id' => '35',
        'name'    => 'PRO',
      ]
    ];
    $data['user_data']  =  Auth::user();
    $data['heading_title_with_all'] = $data['heading_title'];

    return view($this->view . '.officer-list-form', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to($this->base . '/dashboard');
    }
  }

  public function post_officer(OfficerRequest $request)
  {

    $no_allowed_po = $this->no_allowed_po;
    $no_allowed_blo    = $this->no_allowed_blo;
    $no_allowed_pro    = $this->no_allowed_pro;
    $merge            = [];
    $merge['st_code'] = \Auth::user()->st_code;
    $merge['dist_no'] = \Auth::user()->dist_no;
    $merge['ac_no']   = \Auth::user()->ac_no;

    if (in_array($request->role_id, ['33', '35'])) {
      $merge['pin'] = '';
    }
    $request->merge($merge);

    $no_active_officer = PollingStationOfficerModel::count_officer($request->all());

    if (in_array($request->role_id, ['35']) && $no_active_officer > $no_allowed_pro) {
      Session::flash('status', 0);
      Session::flash('flash-message', "You can only add 3 Polling Party to a Polling Station.");
      return Redirect::back()->withInput($request->all());
    }

    if (in_array($request->role_id, ['33']) && $no_active_officer > $no_allowed_blo) {
      Session::flash('status', 0);
      Session::flash('flash-message', "You can only add " . $this->no_allowed_blo . " BLO to a Polling Station.");
      return Redirect::back()->withInput($request->all());
    }

    if ($request->role_id == '34' && $no_active_officer > $no_allowed_po) {
      Session::flash('status', 0);
      Session::flash('flash-message', "You can only add " . $no_allowed_po . " PO to a Polling Station.");
      return Redirect::back()->withInput($request->all());
    }

    DB::beginTransaction();
    try {
      $result   = PollingStationOfficerModel::add_officer($request->all());
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('status', 0);
      Session::flash('flash-message', "Please Try Again.");
      return Redirect::back();
    }
    DB::commit();

    $role = '';
    $app_link = config('public_config.booth_app_link');
    if ($request->role_id == '33') {
      $role = 'BLO';
    } else if ($request->role_id == '35') {
      $role = 'PRO';
    } else if ($request->role_id == '34') {
      $role = 'PO';
    }
    $polling_name = '';
    $poll_station = PollingStation::get_polling_station([
      'st_code' => $this->st_code,
      'ac_no'   => $this->ac_no,
      'ps_no'   => $request->ps_no
    ]);
    if ($poll_station) {
      $polling_name              = $poll_station['PS_NAME_EN'];
    }

    try {
      $sms_message = "Your number has been registered for Booth App as a " . $role . " for Polling station no. " . $request->ps_no . '-' . $polling_name . " by the Returning officer.  Please download Booth App " . $app_link;
      $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
    } catch (\Exception $e) {
    }


    Session::flash('status', 1);
    Session::flash('flash-message', "Profile has been updated successfully.");
    return redirect($this->base . '/booth-app-revamp/officer-list');
  }

  public function reset_otp(Request $request)
  {

    $data   = [
      'otp'     => $request->otp,
      'mobile'  => $request->mobile
    ];
    $rules = [
      "mobile"  => "required|mobile",
      "otp"     => "required|regex:/^[0-9]{6}$/"
    ];

    $messages = [
      'mobile'  => 'Please enter valid a valid mobile number',
      'otp'     => 'Please enter a valid 6 digit number.',
      'regex'   => 'Please enter a valid 6 digit number.',
    ];

    $validator = Validator::make($data, $rules, $messages);
    if ($validator->fails()) {
      return \Response::json([
        'status' => false,
        'errors' => $validator->errors()->getMessageBag()->first()
      ]);
    }

    $is_valid_mobile = PollingStationOfficerModel::get_officer(['mobile' => $request->mobile]);
    if (!$is_valid_mobile) {
      return \Response::json([
        'status' => false,
        'errors' => "Please enter a valid mobile number"
      ]);
    }

    try {
      PollingStationOfficerModel::update_otp([
        'mobile'  => $request->mobile,
        'otp'     => $request->otp,
      ]);
    } catch (\Exception $e) {
      return \Response::json([
        'status' => false,
        'errors' => "Please try again."
      ]);
    }

    return \Response::json([
      'status'  => true,
      'message' => "OTP has been updated."
    ]);
  }


  //SO section
  public function assign_so(Request $request)
  {

    $data = [];
    $request_array = [];

    $filter_ps = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => false,
      'restricted_ps' => []
    ];

    if ($request->has('open')) {
      $data['auto_open'] = 1;
    }

    $data['polling_stations'] = [];
    $results    =   PollingStation::get_polling_stations($filter_ps);
    foreach ($results as $result) {
      $data['polling_stations'][] = [
        'ps_no'   => $result['PS_NO'],
        'ps_name' => $result['PS_NAME_EN'],
      ];
    }



    $data['heading_title'] = '';

    if ($this->st_code) {
      $state_object = StateModel::get_state_by_code($this->st_code);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }
    if ($this->ac_no) {
      $ac_object = AcModel::get_ac(['state' => $this->st_code, 'ac_no' => $this->ac_no]);
      if ($ac_object) {
        $title_array[]  = "AC: " . $ac_object['ac_name'];
      }
    }
    $data['filter_buttons'] = $title_array;
    //end set title

    //buttons
    $data['buttons']    = [];
    $data['action']     = Common::generate_url($this->action . '/assign-so/post');

    $filter_ps = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => false,
      'restricted_ps' => []
    ];

    $data['so_results'] = [];
    $officers = PollingStationOfficerModel::get_officers(array_merge($filter_ps, ['role_id' => 38, 'parent_sm_id' => 0]));

    foreach ($officers as $officer) {
      $sub_sm = [];

      $sub_sms = PollingStationOfficerModel::get_officers(array_merge($filter_ps, ['ps_no' => $officer['ps_no'], 'role_id' => 38, 'parent_sm_id' => $officer['id']]));
      foreach ($sub_sms as $iterate_sub_sm) {
        $sub_sm = [
          'name'        => $iterate_sub_sm['name'],
          'mobile'      => $iterate_sub_sm['mobile_number'],
          'is_active'   => ($iterate_sub_sm['is_active']) ? 'Enable' : 'Disable',
          'status'      => $iterate_sub_sm['is_active'],
          'is_testing'  => $iterate_sub_sm['is_testing'],
          'ps_no'       => $iterate_sub_sm['ps_no'],
          'role_level'  => $iterate_sub_sm['role_level'],
          'encrpt_id'   => encrypt_string($iterate_sub_sm['id'])
        ];
      }

      $data['so_results'][] = [
        'name'        => $officer['name'],
        'mobile'      => $officer['mobile_number'],
        'is_active'   => ($officer['is_active']) ? 'Enable' : 'Disable',
        'status'      => $officer['is_active'],
        'is_testing'  => $officer['is_testing'],
        'ps_no'       => $officer['ps_no'],
        'role_level'  => $officer['role_level'],
        'sub_sm'      => $sub_sm,
        'encrpt_id'   => encrypt_string($officer['id'])
      ];
    }


    $data['user_data']  =  Auth::user();
    return view($this->view . '.so-form', $data);
    try {
    } catch (\Exception $e) {
      return Redirect::to($this->base . '/dashboard');
    }
  }

  public function post_so_ajax(Request $request)
  {

    $role_id = 0;
    if ($request->has('role_id')) {
      $role_id = $request->role_id;
    }
    if ($request->has('id')) {
      $id = decrypt_string($request->id);
    }

    $rules = [
      'name'   => 'required',
      'status' => 'required|in:1,0',
      'ps_no'  => 'required|array'
    ];

    if (isset($id)) {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number,' . $id;
    } else {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number';
    }
    $messages = [
      'ps_no.array'     => "Please select a PS",
      'ps_no.required'  => "Please select a PS"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json([
        'success' => false,
        'errors'  => $validator->getMessageBag()->toArray()
      ]);
    }

    $merge            = [];
    $merge['st_code'] = \Auth::user()->st_code;
    $merge['dist_no'] = \Auth::user()->dist_no;
    $merge['ac_no']           = \Auth::user()->ac_no;
    $merge['ps_no_string']    = implode(',', $request->ps_no);
    $merge['pin']     = '';
    $request->merge($merge);

    $exist_ps = PsSectorOfficer::validate_ps($request->all());


    if (count($exist_ps) > 0) {
      $exist_ps_no = [];
      foreach ($exist_ps as $ex_ps) {
        $exist_ps_no[] = $ex_ps->ps_no;
      }
      usort($exist_ps_no, function ($a, $b) {
        return $a - $b;
      });
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Polling Station with ps no " . implode(', ', $exist_ps_no) . " is already asigned to other SO."]]
      ]);
    }

    DB::beginTransaction();
    try {
      $result   = PollingStationOfficerModel::add_link_officer($request->all());

      //check sub officer and update ps for sub officer
      $get_sub_so = PollingStationOfficerModel::get_sub_so($result->id);

      if (count($get_sub_so) > 0) {
        foreach ($get_sub_so as $iterate_sub_so) {
          PollingStationOfficerModel::update_ps_only($iterate_sub_so->id, $request->all());
          PsSectorOfficer::delete_so($iterate_sub_so->id);
          foreach ($request->ps_no as $key) {
            PsSectorOfficer::add_link_officer([
              'ps_no' => $key,
              'ac_no' => $merge['ac_no'],
              'st_code' => $merge['st_code'],
              'ps_officer_id' => $iterate_sub_so->id,
              'created_by' => Auth::id()
            ]);
          }
        }
      }

      PsSectorOfficer::delete_so($result->id);
      foreach ($request->ps_no as $key) {
        PsSectorOfficer::add_link_officer([
          'ps_no' => $key,
          'ac_no' => $merge['ac_no'],
          'st_code' => $merge['st_code'],
          'ps_officer_id' => $result->id,
          'created_by' => Auth::id()
        ]);
      }
    } catch (\Exception $e) {
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    $role = 'SM';
    $app_link = config('public_config.booth_app_link');
    try {
      $sms_message = "Your number has been registered for Booth App as a " . $role . " for Polling station no. " . $merge['ps_no_string'] . " by the Returning officer.  Please download Booth App " . $app_link;
      $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
    } catch (\Exception $e) {
    }

    Session::flash('status', 1);
    Session::flash('flash-message', "Sector Officer has been added successfully.");
    return Response::json([
      'success' => true
    ]);
  }


  //new function for adding SM

  public function post_so_ajax_new(Request $request)
  {

    $role_id = 0;
    if ($request->has('role_id')) {
      $role_id = $request->role_id;
    }
    if ($request->has('id')) {
      $id = decrypt_string($request->id);
    }

    $rules = [
      'name'   => 'required',
      'status' => 'required|in:1,0',
      'sector_no'  => 'required'
    ];

    if (isset($id)) {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number,' . $id;
    } else {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number';
    }
    $messages = [
      'sector_no'     => "Please select a sector",
      'sector_no.required'  => "Please select a Sector"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json([
        'success' => false,
        'errors'  => $validator->getMessageBag()->toArray()
      ]);
    }
    $st_code = Auth::user()->st_code;
    $ps_no_arr = [];
    $ps_no = SectorAcPsMappingModel::getPsNumberSectorWise($request->sector_no, $st_code);
    $ps_array = explode(",", $ps_no['0']->ps_no);


    $merge            = [];
    $merge['st_code'] = \Auth::user()->st_code;
    $merge['dist_no'] = \Auth::user()->dist_no;
    $merge['ac_no']           = \Auth::user()->ac_no;
    $merge['ps_no_string']    = implode(',', $ps_array);
    $merge['pin']     = '';
    $merge['sector_id']     = $request->sector_no;
    $merge['ps_no']     = $ps_array;
    $request->merge($merge);
    //dd($request->all());
    $exist_ps = PsSectorOfficer::validate_ps($request->all());
    $exist_sector = DB::table('polling_station_officer')->select('*')->where('sector_id', $request->sector_no)->get()->toArray();

    if (count($exist_sector) > 0) {

      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Polling Station with sector number " . $request->sector_no . " and ps no " . implode(', ', $ps_array) . " is already asigned to other SO."]]
      ]);
    }

    DB::beginTransaction();
    try {
      $result   = PollingStationOfficerModel::add_link_officer_new($request->all());
      //check sub officer and update ps for sub officer
      $get_sub_so = PollingStationOfficerModel::get_sub_so($result->id);
      if (count($get_sub_so) > 0) {
        foreach ($get_sub_so as $iterate_sub_so) {
          PollingStationOfficerModel::update_ps_only($iterate_sub_so->id, $request->all());
        }
      }
    } catch (\Exception $e) {
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    $role = 'SM';
    $app_link = config('public_config.booth_app_link');
    try {
      $sms_message = "Your number has been registered for Booth App as a " . $role . " for Polling station no. " . $merge['ps_no_string'] . " by the Returning officer.  Please download Booth App " . $app_link;
      $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
    } catch (\Exception $e) {
    }

    Session::flash('status', 1);
    Session::flash('flash-message', "Sector Officer has been added successfully.");
    return Response::json([
      'success' => true
    ]);
  }


  //New function ends for adding SM

  //save sub so  old method currently not used in boothapp 2020
  public function save_sub_so(Request $request)
  {

    $role_id = 0;
    if ($request->has('role_id')) {
      $role_id = $request->role_id;
    }
    if ($request->has('id')) {
      $id = decrypt_string($request->id);
    }

    $rules = [
      'name'   => 'required',
      'status' => 'required|in:1,0',
    ];

    if (isset($id)) {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number,' . $id;
    } else {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number';
    }
    $messages = [
      'ps_no.array'     => "Please refresh page and try again",
      'ps_no.required'  => "Please refresh page and try again",
      'mobile.mobile'   => "Please enter a valid 10 digit mobile number"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json([
        'success' => false,
        'errors'  => $validator->getMessageBag()->toArray()
      ]);
    }

    if (!$request->has('parent_id')) {
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Please refresh the page and try again."]]
      ]);
    }

    $ps_no = PsSectorOfficer::get_parent_ps(decrypt_string($request->parent_id));
    if (count($ps_no) == 0) {
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Please refresh the page and try again."]]
      ]);
    }

    $merge            = [];
    $merge['st_code'] = \Auth::user()->st_code;
    $merge['dist_no'] = \Auth::user()->dist_no;
    $merge['ac_no']           = \Auth::user()->ac_no;
    $merge['ps_no_string']    = implode(',', $ps_no);
    $merge['ps_no']           = $ps_no;
    $merge['pin']     = '';
    $request->merge($merge);

    $exist_ps = PsSectorOfficer::validate_same_ps($request->all());

    if (count($exist_ps) == 0 && count($exist_ps) > 1) {
      $exist_ps_no = [];
      foreach ($exist_ps as $ex_ps) {
        $exist_ps_no[] = $ex_ps->ps_no;
      }
      usort($exist_ps_no, function ($a, $b) {
        return $a - $b;
      });
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Polling Station with ps no " . implode(', ', $exist_ps_no) . " is already asigned to other SO."]]
      ]);
    }
    DB::beginTransaction();
    try {
      $result   = PollingStationOfficerModel::add_link_officer($request->all());
      PsSectorOfficer::delete_so($result->id);
      foreach ($ps_no as $key) {
        PsSectorOfficer::add_link_officer([
          'ps_no' => $key,
          'ac_no' => $merge['ac_no'],
          'st_code' => $merge['st_code'],
          'ps_officer_id' => $result->id,
          'created_by' => Auth::id()
        ]);
      }
    } catch (\Exception $e) {
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    $role = 'SM';
    $app_link = config('public_config.booth_app_link');
    try {
      $sms_message = "Your number has been registered for Booth App as a " . $role . " for Polling station no. " . $merge['ps_no_string'] . " by the Returning officer.  Please download Booth App " . $app_link;
      $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
    } catch (\Exception $e) {
    }

    Session::flash('status', 1);
    Session::flash('flash-message', "Sector Officer has been added successfully.");
    return Response::json([
      'success' => true
    ]);
  }


  //save_sub_so_new
  public function save_sub_so_new(Request $request)
  {
    $role_id = 0;
    if ($request->has('role_id')) {
      $role_id = $request->role_id;
    }
    if ($request->has('id')) {
      $id = decrypt_string($request->id);
    }

    $rules = [
      'name'   => 'required',
      'status' => 'required|in:1,0',
    ];

    if (isset($id)) {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number,' . $id;
    } else {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number';
    }
    $messages = [
      'ps_no.array'     => "Please refresh page and try again",
      'ps_no.required'  => "Please refresh page and try again",
      'mobile.mobile'   => "Please enter a valid 10 digit mobile number"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json([
        'success' => false,
        'errors'  => $validator->getMessageBag()->toArray()
      ]);
    }

    if (!$request->has('parent_id')) {
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Please refresh the page and try again."]]
      ]);
    }
    $ps_no = PollingStationOfficerModel::get_parent_ps(decrypt_string($request->parent_id));


    //$ps_no = PollingStationOfficerModel::get_parent_ps(decrypt_string($request->parent_id));
    $sector_id = PollingStationOfficerModel::get_parent_sector(decrypt_string($request->parent_id));
    //  dd($sector_id['0']['sector_id']);
    if (count($ps_no) == 0) {
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Please refresh the page and try again."]]
      ]);
    }
    //dd($request->all());
    $merge            = [];
    $merge['st_code'] = \Auth::user()->st_code;
    $merge['dist_no'] = \Auth::user()->dist_no;
    $merge['ac_no']           = \Auth::user()->ac_no;
    $merge['ps_no_string']    = implode(',', $ps_no);
    $merge['ps_no']           = $ps_no;
    $merge['sector_id']           = $sector_id['0']['sector_id'];
    $merge['pin']     = '';
    $request->merge($merge);
    //dd($request->all());
    //$exist_ps = PsSectorOfficer::validate_same_ps($request->all());
    $exist_ps = PollingStationOfficerModel::validate_same_ps($request->all());
    //dd($exist_ps);

    if (count($exist_ps) == 0 && count($exist_ps) > 1) {
      $exist_ps_no = [];
      foreach ($exist_ps as $ex_ps) {
        $exist_ps_no[] = $ex_ps->ps_no;
      }
      usort($exist_ps_no, function ($a, $b) {
        return $a - $b;
      });
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Polling Station with ps no " . implode(', ', $exist_ps_no) . " is already asigned to other SO."]]
      ]);
    }
    DB::beginTransaction();
    try {
      $result   = PollingStationOfficerModel::add_link_officer_new($request->all());
    } catch (\Exception $e) {
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    $role = 'SM';
    $app_link = config('public_config.booth_app_link');
    try {
      $sms_message = "Your number has been registered for Booth App as a " . $role . " for Polling station no. " . $merge['ps_no_string'] . " by the Returning officer.  Please download Booth App " . $app_link;
      $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
    } catch (\Exception $e) {
    }

    Session::flash('status', 1);
    Session::flash('flash-message', "Sector Officer has been added successfully.");
    return Response::json([
      'success' => true
    ]);
  }


  //Save_sub_so_new_ends



  //multiple SM start
  public function assign_blo(Request $request)
  {
    try {
      $data = [];
      $request_array = [];

      $filter_ps = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
        'paginate'  => false,
        'restricted_ps' => []
      ];

      if ($request->has('open')) {
        $data['auto_open'] = 1;
      }

      $data['locations'] = [];
      $results    =   PollingStationLocationModel::get_locations($filter_ps);

      foreach ($results as $result) {
        $data['locations'][] = [
          'id'   => $result['id'],
          'name' => $result['name'],
        ];
      }
      $data['reset_otp_link']  = Common::generate_url('booth-app-revamp/reset_otp');
      $data['heading_title'] = '';

      if ($this->st_code) {
        $state_object = StateModel::get_state_by_code($this->st_code);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
        }
      }
      if ($this->ac_no) {
        $ac_object = AcModel::get_ac(['state' => $this->st_code, 'ac_no' => $this->ac_no]);
        if ($ac_object) {
          $title_array[]  = "AC: " . $ac_object['ac_name'];
        }
      }
      $data['filter_buttons'] = $title_array;
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['action']     = Common::generate_url($this->action . '/assign-blo/post');

      $filter_ps = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
        'paginate'  => false,
        'restricted_ps' => []
      ];

      $data['results'] = [];
      $officers = PollingStationOfficerModel::get_officers(array_merge($filter_ps, ['role_id' => 33]));

      foreach ($officers as $officer) {
        $location_name = '';
        $location = PollingStationLocationModel::find($officer['location_id']);
        if ($location) {
          $location_name = $location->name;
        }
        $data['results'][] = [
          'name'        => $officer['name'],
          'mobile'      => $officer['mobile_number'],
          'is_active'   => ($officer['is_active']) ? 'Enable' : 'Disable',
          'status'      => $officer['is_active'],
          'is_testing'  => $officer['is_testing'],
          'ps_no'       => $officer['ps_no'],
          'encrpt_id'   => encrypt_string($officer['id']),
          'location_name' => $location_name,
          'location_id' => $officer['location_id']
        ];
      }

      $data['user_data']  =  Auth::user();


      return view($this->view . '.blo-form', $data);
    } catch (\Exception $e) {
      return Redirect::to($this->base . '/dashboard');
    }
  }

  public function post_blo_ajax(Request $request)
  {

    $role_id = 0;
    if ($request->has('role_id')) {
      $role_id = $request->role_id;
    }
    if ($request->has('id')) {
      $id = decrypt_string($request->id);
    }

    $rules = [
      'name'        => 'required',
      'status'      => 'required|in:1,0',
      'location_id' => 'required|exists:polling_station_location,id'
    ];

    if (isset($id)) {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number,' . $id;
    } else {
      $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number';
    }
    $messages = [
      'location_id.required'  => "Please select a PS"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json([
        'success' => false,
        'errors'  => $validator->getMessageBag()->toArray()
      ]);
    }

    $merge                  = [];
    $merge['st_code']       = \Auth::user()->st_code;
    $merge['dist_no']       = \Auth::user()->dist_no;
    $merge['ac_no']         = \Auth::user()->ac_no;
    $merge['location_id']   = $request->location_id;
    $merge['role_id']       = 33;
    $merge['ps_no_string']  = '';
    $merge['ps_no']         = 0;
    $location = PollingStationLocationModel::find($request->location_id);
    if ($location) {
      $merge['ps_no_string']  = $location->ps_no;
      $merge['ps_no'] = $location->ps_no;
    }
    $merge['pin']           = '';
    $request->merge($merge);
    DB::beginTransaction();
    try {
      $result   = PollingStationOfficerModel::add_officer($request->all());
    } catch (\Exception $e) {
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    $role = 'BLO';
    $app_link = config('public_config.booth_app_link');
    try {
      $sms_message = "Your number has been registered for Booth App as a " . $role . " for Polling station no. " . $merge['ps_no_string'] . " by the Returning officer.  Please download Booth App " . $app_link;
      $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
    } catch (\Exception $e) {
    }

    Session::flash('status', 1);
    Session::flash('flash-message', "Updated Successfully.");
    return Response::json([
      'success' => true
    ]);
  }


  public function search_officer(Request $request)
  {

    $rules = [
      'mobile' => 'required|mobile'
    ];

    $messages = [
      'mobile'  => "Please enter a valid mobile number"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json([
        'success' => false,
        'warning' => "Please enter a valid mobile number"
      ]);
    }

    $filter = [
      'st_code' => $this->st_code,
      'ac_no'   => $this->ac_no,
      'mobile'  => $request->mobile
    ];

    //officers
    $officers_data = [];
    $officers = PollingStationOfficerModel::get_officers($filter);

    if (count($officers) == 0) {
      return Response::json([
        'success' => false,
        'warning' => "No Record Found"
      ]);
    }

    foreach ($officers as $officer) {

      $role = '';
      if ($officer['role_id'] == '33') {
        $role = 'BLO';
      }
      if ($officer['role_id'] == '35') {
        $role = 'PRO';
      }
      if ($officer['role_id'] == '34') {
        $role = 'PO';
      }
      if ($officer['role_id'] == '38') {
        $role = 'SM';
      }

      $st_name = '';
      $state_object = StateModel::get_state_by_code($officer['st_code']);
      if ($state_object) {
        $st_name = $state_object['ST_NAME'];
      }

      $ac_name = '';
      $ac_object = AcModel::get_ac(['state' => $officer['st_code'], 'ac_no' => $officer['ac_no']]);
      if ($ac_object) {
        $ac_name = $ac_object['ac_name'];
      }


      $poll_station_name = '';
      if ($officer['role_id'] == 38 && $officer['ps_no'] != '') {
        $array_ps_name = [];
        foreach (explode(',', $officer['ps_no']) as $iterate_ps_no) {
          $poll_station = PollingStation::get_polling_station([
            'st_code' => $officer['st_code'],
            'ac_no'   => $officer['ac_no'],
            'ps_no'   => $iterate_ps_no
          ]);
          if ($poll_station) {
            $array_ps_name[] = $poll_station['PS_NAME_EN'];
          }
          $poll_station_name = implode(', ', $array_ps_name);
        }
      } else {
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $officer['st_code'],
          'ac_no'   => $officer['ac_no'],
          'ps_no'   => $officer['ps_no']
        ]);
        if ($poll_station) {
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }
      }

      $officers_data =  [
        'st_code'       => $officer['st_code'],
        'st_name'     => $st_name,
        'ac_no'       => $officer['ac_no'],
        'ac_name'     => $ac_name,
        'ps_no'         => $officer['ps_no'],
        'ps_name'       => $poll_station_name,
        'name'          => $officer['name'],
        'mobile'        => $officer['mobile_number'],
        'is_login'      => ($officer['login_time']) ? $officer['login_time'] : 'No',
        'designation'   => $role . $officer['role_level'],
      ];
    }

    return Response::json($officers_data);
  }


  public function assign_so_new(Request $request)
  {


    $data = [];
    $request_array = [];

    $filter_ps = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => false,
      'restricted_ps' => []
    ];

    if ($request->has('open')) {
      $data['auto_open'] = 1;
    }

    $data['polling_stations'] = [];
    $results    =   PollingStation::get_polling_stations($filter_ps);
    foreach ($results as $result) {
      $data['polling_stations'][] = [
        'ps_no'   => $result['PS_NO'],
        'ps_name' => $result['PS_NAME_EN'],
      ];
    }

    $sector_info = SectorMasterModel::getsector();
    $data['sector_info'] = [];
    foreach ($sector_info as $value) {
      $data['sector_info'][] = [
        'sector_id'   => $value['sector_id'],
        'sector_name' => $value['sector_name']
      ];
    }

    $data['sector'] = [];
    $sector_data = DB::table('sector_ac_ps_mapping as sapm')
      ->select(
        'sapm.st_code',
        'sapm.dist_no',
        'sapm.ac_no',
        DB::raw('group_concat(sapm.ps_no) as ps_no'),
        'sapm.sector_id',
        'msm.sector_name'
      )
      ->join('m_sector_master as msm', 'msm.sector_id', '=', 'sapm.sector_id')
      ->where('sapm.st_code', $this->st_code)
      ->groupBy('sapm.sector_id')
      ->get()->toArray();

    foreach ($sector_data as $sector) {
      $data['sector'][] = [
        'sector_id'   => $sector->sector_id,
        'sector_name' => $sector->sector_name,
        'ps_no'      => $sector->ps_no
      ];
    }


    $data['heading_title'] = '';

    if ($this->st_code) {
      $state_object = StateModel::get_state_by_code($this->st_code);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }
    if ($this->ac_no) {
      $ac_object = AcModel::get_ac(['state' => $this->st_code, 'ac_no' => $this->ac_no]);
      if ($ac_object) {
        $title_array[]  = "AC: " . $ac_object['ac_name'];
      }
    }
    $data['filter_buttons'] = $title_array;
    //end set title

    //buttons
    $data['buttons']    = [];
    $data['action']     = Common::generate_url($this->action . '/assign-so/post');

    $data['polling_stations'] = [];
    $results    =   PollingStation::get_polling_stations($filter_ps);
    foreach ($results as $result) {
      $data['polling_stations'][] = [
        'ps_no'   => $result['PS_NO'],
        'ps_name' => $result['PS_NAME_EN'],
      ];
    }

    $filter_ps = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => false,
      'restricted_ps' => []
    ];

    $data['so_results'] = [];
    foreach ($sector_data as $sec) {
      $officers[] = PollingStationOfficerModel::get_officers_new(array_merge($filter_ps, ['role_id' => 38, 'parent_sm_id' => 0, 'sector_id' => $sec->sector_id]));
    }
    // dd($officers);

    foreach ($officers as $officer) {
      foreach ($officer as  $value) {
        $sub_sm = [];

        $sector_ac_ps_mapping = DB::table('sector_ac_ps_mapping as sapm')
          ->select(
            'sapm.st_code',
            'sapm.dist_no',
            'sapm.ac_no',
            DB::raw('group_concat(sapm.ps_no) as ps_no'),
            'sapm.sector_id',
            'msm.sector_name'
          )
          ->join('m_sector_master as msm', 'msm.sector_id', '=', 'sapm.sector_id')
          ->where('sapm.st_code', $this->st_code)
          ->where('sapm.sector_id', $value['sector_id'])
          ->groupBy('sapm.sector_id')
          ->get()->toArray();


        $sub_sms = PollingStationOfficerModel::get_officers_new(array_merge($filter_ps, ['role_id' => 38, 'parent_sm_id' => $value['id'], 'sector_id' => $value['sector_id']]));

        foreach ($sub_sms as $iterate_sub_sm) {
          $sub_sm = [
            'name'        => $iterate_sub_sm['name'],
            'mobile'      => $iterate_sub_sm['mobile_number'],
            'is_active'   => ($iterate_sub_sm['is_active']) ? 'Enable' : 'Disable',
            'status'      => $iterate_sub_sm['is_active'],
            'is_testing'  => $iterate_sub_sm['is_testing'],
            'ps_no'       => $sector_ac_ps_mapping['0']->ps_no,
            'role_level'  => $iterate_sub_sm['role_level'],
            'encrpt_id'   => encrypt_string($iterate_sub_sm['id'])
          ];
        }

        $data['so_results'][] = [
          'name'        => $value['name'],
          'sector_id'   => $value['sector_id'],
          'sector_name'   => $value['sector_name'],
          'mobile'      => $value['mobile_number'],
          'is_active'   => ($value['is_active']) ? 'Enable' : 'Disable',
          'status'      => $value['is_active'],
          'is_testing'  => $value['is_testing'],
          'ps_no'       => $sector_ac_ps_mapping['0']->ps_no,
          'role_level'  => $value['role_level'],
          'sub_sm'      => $sub_sm,
          'encrpt_id'   => encrypt_string($value['id'])
        ];
      }
    }


    $data['user_data']  =  Auth::user();

    return view($this->view . '.so-form-new', $data);
    try {
    } catch (\Exception $e) {
      return Redirect::to($this->base . '/dashboard');
    }
  }
}  // end class
