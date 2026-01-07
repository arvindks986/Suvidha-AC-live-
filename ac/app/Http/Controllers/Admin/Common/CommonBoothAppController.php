<?php namespace App\Http\Controllers\Admin\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;
use App\models\Admin\BoothAppRevamp\{PollingStation, StateModel, AcModel, DistrictModel, PhaseModel};
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use App\Classes\xssClean;

class CommonBoothAppController extends Controller{

    //generate json files for speed
    public static function generate_json_files($value = array(), $name = '', $filter = array()){
        $file_path = Common::get_json_file_path($name, $filter);
        $fp        = fopen($file_path, 'w');
        fwrite($fp, json_encode($value));
        fclose($fp);
    }

    public static function get_allowed_acs($request){
		
        $allowed_st_code = ['S06','S17','S12','S14'];
        $allowed_dist_no = [1,2,3,4,5,6,7,8,9,10,11];
        $allowed_acs = [10,15,22,27,36,38,40,41,46,55,64,65,61];
        return [
            'allowed_st_code' => $allowed_st_code,
            'allowed_dist_no' => $allowed_dist_no,
            'allowed_acs' => $allowed_acs
        ];
    }

    public static function get_json_file_path($name = '', $filter = array()){

        //create json file to decrease the load
        if(!empty($filter['st_code']) && $filter['st_code'] && !empty($filter['ac_no']) && $filter['ac_no'] && !empty($filter['ps_no']) && $filter['ps_no']){
          $key = $name.'_ac_ps_'.$filter['st_code'].'_'.$filter['ac_no'];
        }else if(!empty($filter['st_code']) && $filter['st_code'] && !empty($filter['ac_no']) && $filter['ac_no']){
          $key = $name.'_ac_'.$filter['st_code'].'_'.$filter['ac_no'];
        }else if(!empty($filter['st_code']) && $filter['st_code'] && !empty($filter['dist_no']) && $filter['dist_no'] ){
          $key = $name.'_dist_'.$filter['st_code'].'_'.$filter['dist_no'];
        }else if(!empty($filter['st_code']) && $filter['st_code']){
          $key = $name.'_st_'.$filter['st_code'];
        }else{
          $key = null;
        }
        $layout_path  = public_path().'/data';
        $file_name    = $key.'.json';
        return $layout_path.'/'.$file_name;
    }


    public static function get_request_filter($request){
        $ac_no      = NULL;
        $st_code    = NULL;
        $ps_no      = NULL;
        $role_id    = NULL;
        $dist_no    = NULL;
        $base       = NULL;
        $phase_no   = NULL;
        $restricted_ps = [];
        $filter_role_id    = NULL;
        $filter     = Common::get_auth_filter();

        $xss_clean = new xssClean;
        if($request->has('phase_no')){
            $phase_no = $xss_clean->clean_input($request->phase_no);
        }
        if($request->has('st_code')){
            $st_code = $xss_clean->clean_input($request->st_code);
        }
        if($request->has('dist_no')){
            $dist_no = $xss_clean->clean_input($request->dist_no);
        }
        if($request->has('ac_no')){
            $ac_no = $xss_clean->clean_input($request->ac_no);
        }
        if($request->has('ps_no')){
            $ps_no = $request->ps_no;
        }
        if($request->has('role_id')){
            $filter_role_id = $xss_clean->clean_input($request->role_id);
        }


        $role_id = $filter['role_id'];

        if($filter['role_id'] == '19'){
            $ac_no      = $filter['ac_no'];
            $dist_no    = $filter['dist_no'];
            $st_code    = $filter['st_code'];
        }else if($filter['role_id'] == '5'){
            $dist_no    = $filter['dist_no'];
            $st_code    = $filter['st_code'];
        }else if($filter['role_id'] == '4'){
            $st_code    = $filter['st_code'];
        }else{

        }

        return [
            'st_code'   => $st_code,
            'dist_no'   => $dist_no,
            'ac_no'     => $ac_no,
            'ps_no'     => $ps_no,
            'role_id'           => $role_id,
            'filter_role_id'    => $filter_role_id,
            'base'              => $filter['base'],
            'phase_no'          => $phase_no
        ];
    }

    public static function get_form_filters($filter_by = [],$request){
        $data           = [];
        $state_filter=[];
        $filter         = Common::get_auth_filter();
        $request_filter = Common::get_request_filter($request);
        $ac_no          = $request_filter['ac_no'];
        $st_code        = $request_filter['st_code'];
        $dist_no        = $request_filter['dist_no'];
        $ps_no          = $request_filter['ps_no'];
        $role_id        = $request_filter['role_id'];
        $phase_no       = $request_filter['phase_no'];
        $request_filter['state'] = $request_filter['st_code'];

        $allowed_acs = NULL;
        if(!empty($filter_by['allowed_acs'])){
            $allowed_acs = $filter_by['allowed_acs'];
        }

        $allowed_dist_no = NULL;
        if(!empty($filter_by['allowed_dist_no'])){
            $allowed_dist_no = $filter_by['allowed_dist_no'];
        }

        $allowed_st_code = NULL;
        if(!empty($filter_by['allowed_st_code'])){
            $allowed_st_code = $filter_by['allowed_st_code'];
        }

        $filter['allowed_acs']      = $allowed_acs;
        $filter['allowed_dist_no']  = $allowed_dist_no;
        $filter['allowed_st_code']  = $allowed_st_code;



        if(!empty($filter_by['phase_no'])){ 
            $phases = [];
            $phases_results = PhaseModel::get_phases(array_merge($filter,[
                'group_by'  => 'phase_no'
            ]));
            foreach($phases_results as $iterate_phase){
                $is_active = false;
                if($phase_no == $iterate_phase['phase_no']){
                    $is_active = true;
                }
                $phases[] = [
                    'id'        => $iterate_phase['phase_no'],
                    'name'      => 'Phase '.$iterate_phase['phase_no'],
                    'active'    => $is_active
                ];
            }
            $data[]   = [
                'id'        => 'phase_no',
                'name'      => 'Phase',
                'results'   => $phases,
            ];
        }


        //states
        if(!empty($filter_by['st_code'])){ 
            $states = [];
            if(!in_array(Auth::user()->role_id,['19'])){
                $state_filter = $filter;
            }else{
                $state_filter = [
                    'st_code' => $request_filter['st_code'],
                    'allowed_st_code'   => $allowed_st_code
                ];
            }

            $state_filter['phase_no'] = $request_filter['phase_no'];

            $states_results = StateModel::get_states($state_filter);
            foreach($states_results as $iterate_state){
                $is_active = false;
                if($st_code == $iterate_state['ST_CODE']){
                    $is_active = true;
                }
                $states[] = [
                    'id'        => $iterate_state['ST_CODE'],
                    'name'      => $iterate_state['ST_NAME'],
                    'active'    => $is_active
                ];
            }
            $data[]   = [
                'id'        => 'st_code',
                'name'      => 'State',
                'results'   => $states,
            ];
        }

        //acs
        if(!empty($filter_by['dist_no'])){
            $dists = [];
            if($st_code){
                if(in_array(Auth::user()->role_id,['19','5'])){
                    $dist_filter = $filter;
                }else{
                    $dist_filter = [
                        'state' => $request_filter['st_code'],
                        'allowed_st_code'   => $allowed_st_code,
                        'allowed_dist_no'   => $allowed_dist_no,
                        'allowed_acs'     => $allowed_acs,
                    ];
                }
                $dist_filter['phase_no'] = $request_filter['phase_no'];
                $dists_results = DistrictModel::get_districts($dist_filter);
                foreach($dists_results as $iterate_dist){
                    $is_active = false;
                    if($dist_no == $iterate_dist['dist_no']){
                        $is_active = true;
                    }
                    $dists[] = [
                        'id' => $iterate_dist['dist_no'],
                        'name' => $iterate_dist['dist_no'].'-'.$iterate_dist['dist_name'],
                        'active'  => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'dist_no',
                'name'      => 'District',
                'results'   => $dists,
            ];
        }

        //acs
        if(!empty($filter_by['ac_no'])){
            $acs = [];
            if($st_code){
                if(in_array(Auth::user()->role_id,['19'])){
                    $ac_filter = $filter;
                }else{
                    $ac_filter = [
                        'st_code' => $request_filter['st_code'],
                        'dist_no' => $request_filter['dist_no'],
                        'allowed_st_code'   => $allowed_st_code,
                        'allowed_dist_no'   => $allowed_dist_no,
                        'allowed_acs'     => $allowed_acs,
                    ];
                }

                $ac_filter['phase_no'] = $request_filter['phase_no'];
            
                $acs_results = AcModel::get_acs($ac_filter);
                
                foreach($acs_results as $iterate_ac){
                    $is_active = false;
                    if($ac_no == $iterate_ac['ac_no']){
                        $is_active = true;
                    }
                    $acs[] = [
                        'id' => $iterate_ac['ac_no'],
                        'name' => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
                        'active'  => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'ac_no',
                'name'      => 'AC',
                'results'   => $acs,
            ];
        }

        //polling station
        if(!empty($filter_by['ps_no'])){
            $pss = [];
            if($st_code && $ac_no){
                $ps_results = PollingStation::get_polling_stations([
                    'st_code'       => $st_code,
                    'ac_no'         => $ac_no
                ]);
                foreach($ps_results as $iterate_ps){
                    $is_active = false;
                    if($ps_no == $iterate_ps['PS_NO']){
                        $is_active = true;
                    }
                    $pss[] = [
                        'id'        => $iterate_ps['PS_NO'],
                        'name'      => $iterate_ps['PS_NO'].'-'.$iterate_ps['PS_NAME_EN'],
                        'active'    => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'ps_no',
                'name'      => 'Polling Station',
                'results'   => $pss,
            ];
        }

        if(!empty($filter_by['designation'])){
            //role filter
              $role_id = 0;
              if($request->has('role_id')){
                $role_id = $request->role_id;
              }
              $roles   = [];
              $roles[] = 
			  /* [
                'name'  => 'BLO',
                'id'      => 33
              ];
              $roles[] = [
                'name'  => 'PRO',
                'id'      => 35,
              ]; */
              $roles[] = [
                'name'  => 'PO',
                'id'      => 34,
              ];
             /*  $roles[] = [
                'name'  => 'SM',
                'id'      => 38,
              ]; */
			  
              foreach ($roles as $role_iterate) {
                $is_role_active = false;
                if($role_id == $role_iterate['id']){
                  $is_role_active = true;
                }
                $role_types[] = [
                  'name'  => $role_iterate['name'],
                  'id'      => $role_iterate['id'],
                  'active'  => $is_role_active
                ];
              }
              $data[] = [
                'id'      => 'role_id',
                'name'    => 'Designation',
                'results' => $role_types
              ];
        }
        

        return $data;
    }
	
	public static function get_form_filters_exempt($filter_by = [],$request){
	
        $data           = [];
        $state_filter=[];
        $filter         = Common::get_auth_filter();
        $request_filter = Common::get_request_filter($request);
        $ac_no          = $request_filter['ac_no'];
        $st_code        = $request_filter['st_code'];
        $dist_no        = $request_filter['dist_no'];
        $ps_no          = $request_filter['ps_no'];
        $role_id        = $request_filter['role_id'];
        $phase_no       = $request_filter['phase_no'];
        $request_filter['state'] = $request_filter['st_code'];

        $allowed_acs = NULL;
        if(!empty($filter_by['allowed_acs'])){
            $allowed_acs = $filter_by['allowed_acs'];
        }

        $allowed_dist_no = NULL;
        if(!empty($filter_by['allowed_dist_no'])){
            $allowed_dist_no = $filter_by['allowed_dist_no'];
        }

        $allowed_st_code = NULL;
        if(!empty($filter_by['allowed_st_code'])){
            $allowed_st_code = $filter_by['allowed_st_code'];
        }

        $filter['allowed_acs']      = $allowed_acs;
        $filter['allowed_dist_no']  = $allowed_dist_no;
        $filter['allowed_st_code']  = $allowed_st_code;



        if(!empty($filter_by['phase_no'])){ 
            $phases = [];
            $phases_results = PhaseModel::get_phases(array_merge($filter,[
                'group_by'  => 'phase_no'
            ]));
            foreach($phases_results as $iterate_phase){
                $is_active = false;
                if($phase_no == $iterate_phase['phase_no']){
                    $is_active = true;
                }
                $phases[] = [
                    'id'        => $iterate_phase['phase_no'],
                    'name'      => 'Phase '.$iterate_phase['phase_no'],
                    'active'    => $is_active
                ];
            }
            $data[]   = [
                'id'        => 'phase_no',
                'name'      => 'Phase',
                'results'   => $phases,
            ];
        }


        //states
        if(!empty($filter_by['st_code'])){ 
            $states = [];
            if(!in_array(Auth::user()->role_id,['19'])){
                $state_filter = $filter;
            }else{
                $state_filter = [
                    'st_code' => $request_filter['st_code'],
                    'allowed_st_code'   => $allowed_st_code
                ];
            }

            $state_filter['phase_no'] = $request_filter['phase_no'];

            $states_results = StateModel::get_states($state_filter);
            foreach($states_results as $iterate_state){
                $is_active = false;
                if($st_code == $iterate_state['ST_CODE']){
                    $is_active = true;
                }
                $states[] = [
                    'id'        => $iterate_state['ST_CODE'],
                    'name'      => $iterate_state['ST_NAME'],
                    'active'    => $is_active
                ];
            }
            $data[]   = [
                'id'        => 'st_code',
                'name'      => 'State',
                'results'   => $states,
            ];
        }

        //acs
        if(!empty($filter_by['dist_no'])){
            $dists = [];
            if($st_code){
                if(in_array(Auth::user()->role_id,['19','5'])){
                    $dist_filter = $filter;
                }else{
                    $dist_filter = [
                        'state' => $request_filter['st_code'],
                        'allowed_st_code'   => $allowed_st_code,
                        'allowed_dist_no'   => $allowed_dist_no,
                        'allowed_acs'     => $allowed_acs,
                    ];
                }
                $dist_filter['phase_no'] = $request_filter['phase_no'];
                $dists_results = DistrictModel::get_districts($dist_filter);
                foreach($dists_results as $iterate_dist){
                    $is_active = false;
                    if($dist_no == $iterate_dist['dist_no']){
                        $is_active = true;
                    }
                    $dists[] = [
                        'id' => $iterate_dist['dist_no'],
                        'name' => $iterate_dist['dist_no'].'-'.$iterate_dist['dist_name'],
                        'active'  => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'dist_no',
                'name'      => 'District',
                'results'   => $dists,
            ];
        }

        //acs
        if(!empty($filter_by['ac_no'])){
            $acs = [];
            if($st_code){
                if(in_array(Auth::user()->role_id,['19'])){
                    $ac_filter = $filter;
                }else{
                    $ac_filter = [
                        'st_code' => $request_filter['st_code'],
                        'dist_no' => $request_filter['dist_no'],
                        'allowed_st_code'   => $allowed_st_code,
                        'allowed_dist_no'   => $allowed_dist_no,
                        'allowed_acs'     => $allowed_acs,
                    ];
                }

                $ac_filter['phase_no'] = $request_filter['phase_no'];
            
                $acs_results = AcModel::get_acs($ac_filter);
                
                foreach($acs_results as $iterate_ac){
                    $is_active = false;
                    if($ac_no == $iterate_ac['ac_no']){
                        $is_active = true;
                    }
                    $acs[] = [
                        'id' => $iterate_ac['ac_no'],
                        'name' => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
                        'active'  => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'ac_no',
                'name'      => 'AC',
                'results'   => $acs,
            ];
        }

        //polling station
        if(!empty($filter_by['ps_no'])){
            $pss = [];
            if($st_code && $ac_no){
                $ps_results = PollingStation::get_polling_stations_exem_new([
                    'st_code'       => $st_code,
                    'ac_no'         => $ac_no
                ]);
                foreach($ps_results as $iterate_ps){
                    $is_active = false;
                    if($ps_no == $iterate_ps['PS_NO']){
                        $is_active = true;
                    }
                    $pss[] = [
                        'id'        => $iterate_ps['PS_NO'],
                        'name'      => $iterate_ps['PS_NO'].'-'.$iterate_ps['PS_NAME_EN'],
                        'active'    => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'ps_no',
                'name'      => 'Polling Station',
                'results'   => $pss,
            ];
        }

        if(!empty($filter_by['designation'])){
            //role filter
              $role_id = 0;
              if($request->has('role_id')){
                $role_id = $request->role_id;
              }
              $roles   = [];
              $roles[] = 
			  /* [
                'name'  => 'BLO',
                'id'      => 33
              ];
              $roles[] = [
                'name'  => 'PRO',
                'id'      => 35,
              ]; */
              $roles[] = [
                'name'  => 'PO',
                'id'      => 34,
              ];
             /*  $roles[] = [
                'name'  => 'SM',
                'id'      => 38,
              ]; */
			  
              foreach ($roles as $role_iterate) {
                $is_role_active = false;
                if($role_id == $role_iterate['id']){
                  $is_role_active = true;
                }
                $role_types[] = [
                  'name'  => $role_iterate['name'],
                  'id'      => $role_iterate['id'],
                  'active'  => $is_role_active
                ];
              }
              $data[] = [
                'id'      => 'role_id',
                'name'    => 'Designation',
                'results' => $role_types
              ];
        }
        

        return $data;
    }

    public static function encrypt_string($string){
        return Crypt::encryptString($string);
    }

    public static function decrypt_string($string){
        return Crypt::decryptString($string);
    }

    public static function generate_url($path){
        $url    = '/';
        $filter = Common::get_auth_filter();
        $url    .= $filter['base'];
        return url($url.'/'.$path);
    }

    public static function get_auth_filter(){
        $ac_no      = '';
        $dist_no    = '';
        $st_code    = '';
        $base       = '';
        $role_id    = Auth::user()->role_id;
        if(Auth::user() && $role_id == '19'){
            $ac_no    = Auth::user()->ac_no;
            $st_code  = Auth::user()->st_code;
            $dist_no  = Auth::user()->dist_no;
            $base     = 'roac';
        }else if(Auth::user() && $role_id == '5'){
            $st_code  = Auth::user()->st_code;
            $dist_no  = Auth::user()->dist_no;
            $base     = 'acdeo';
        }else if(Auth::user() && $role_id == '4'){
            $st_code  = Auth::user()->st_code;
            $base     = 'acceo';
        }else if(Auth::user() && $role_id == '27'){
            $base     = 'eci-index';
        }else if(Auth::user() && $role_id == '36'){
            $base     = 'maintenance';
        }else{
            $base     = 'eci';
        }
        return [
            'ac_no'     => $ac_no,
            'dist_no'   => $dist_no,
            'st_code'   => $st_code,
            'base'      => $base,
            'role_id'   => $role_id
        ];
    }

    public static function get_common_query($is_capital = false){
        $acs = [];
        $setting = \App\models\Admin\SettingModel::get_setting_cache();
        if(!empty($setting['booth_app'])){
            foreach($setting['booth_app'] as $iterate_booth_app){
                $acs = array_merge($acs, $iterate_booth_app['acs']);
            }
        }

        if($is_capital){
            $where_raw = "st_code = 'S27' and ac_no IN (".implode(',', $acs).")";
        }else{
            $where_raw = "ST_CODE = 'S27' and AC_NO IN (".implode(',', $acs).")";
        }

        return $where_raw;
    }

    //ajax filter common
    public static function load_phase_by_ajax($request){
        $data           = [];
        $filter         = Common::get_auth_filter();
        $request_filter = Common::get_request_filter($request);
        $ac_no          = $request_filter['ac_no'];
        $st_code        = $request_filter['st_code'];
        $dist_no        = $request_filter['dist_no'];
        $ps_no          = $request_filter['ps_no'];
        $role_id        = $request_filter['role_id'];
        $phase_no       = 0;
        $request_filter['state'] = $request_filter['st_code'];

        $data = [];
        $ac_filter = array_merge($filter,[
            'group_by' => 'phase_no',
        ]);
        $acs_results = PhaseModel::get_phases($ac_filter);            
        foreach($acs_results as $iterate_ac){
            $data[] = [
                'phase_no'      => $iterate_ac['phase_no'],
                'phase_name'    => 'Phase '.$iterate_ac['phase_no'],
            ];
        }     

        return $data;
    }

    public function load_state_by_ajax(Request $request){
        if(!$request->has('phase_no')){
            return [];
        }
        $filter         = Common::get_auth_filter();
        $request_filter = Common::get_request_filter($request);
        $ac_no          = $request_filter['ac_no'];
        $st_code        = $request_filter['st_code'];
        $dist_no        = $request_filter['dist_no'];
        $ps_no          = $request_filter['ps_no'];
        $role_id        = $request_filter['role_id'];

        $phase_filter = array_merge($filter,[
            'group_by' => 'st_code',
            'phase_no' => $request->phase_no
        ]);
        $phases_results = PhaseModel::get_phases($phase_filter);  
        foreach($phases_results as $iterate_state){
            $data[] = [
                'st_code'   => $iterate_state['st_code'],
                'st_name'   => $iterate_state['st_name'],
            ];
        }
      
        return $data;
    } 

    public static function default_filter_values($request){

        $filter         = Common::get_auth_filter();
		
        $phases = [];
        $state_filter=[];
        $phases_results = PhaseModel::get_phases(array_merge($filter,[
            'group_by'  => 'phase_no'
        ]));
        foreach($phases_results as $iterate_state){
            $phases[] = [
                'phase_no'   => $iterate_state['phase_no'],
                'phase_name' => 'Phase '.$iterate_state['phase_no']
            ];
        }
		//code added for multistate
        if($filter['role_id'] == 7){
			 $states = [];
			$state_filter = [
            'group_by'  => 'st_code',
            'phase_no'  => $request->phase_no,
            'st_code'   => $request->st_code,
           
        ];
		}else{
		foreach($phases_results as $iterate_state){
        $states = [];
        $state_filter = [
            'group_by'  => 'st_code',
            'phase_no'  => $iterate_state['phase_no'],
            'st_code'   => $iterate_state['st_code'],
            //'st_code'   => $request->st_code,
            //'st_code'   => $request->st_code,
        ];
		}}
		
        if(!in_array($filter['role_id'],[5,4])){
            $ac_filter['st_code']   = $filter['st_code'];
        }
        $state_results = PhaseModel::get_phases($state_filter);
        foreach($state_results as $iterate_state){
            $states[] = [
                'st_code'   => $iterate_state['st_code'],
                'st_name'   => $iterate_state['st_name']
            ];
        }
        //code added for multistate ac
		if($filter['role_id'] == 7){
			$acs = [];
			$ac_filter = [
            'group_by'  => 'ac_no',
            'phase_no'  => $request->phase_no,
            'st_code'   => $request->st_code,
			];
		}
		else{
		foreach($phases_results as $iterate_state){
        $acs = [];
        $ac_filter = [
            'group_by'  => 'ac_no',
            'phase_no'  => $iterate_state['phase_no'],
            'st_code'   => $iterate_state['st_code'],
        ];
		}}
		
        if($filter['role_id']==19){
            $ac_filter['st_code']   = $filter['st_code'];
            $ac_filter['ac_no']     = $filter['ac_no'];
        }
        $ac_results = PhaseModel::get_phases($ac_filter);
        foreach($ac_results as $iterate_ac){
            $acs[] = [
                'st_code'   => $iterate_ac['st_code'],
                'ac_no'     => $iterate_ac['ac_no'],
                'ac_name'   => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
            ];
        }

        return [
            'phases' => $phases,
            'states' => $states,
            'acs'    => $acs
        ];
    }

    public function load_ac_by_ajax(Request $request){
        if(!$request->has('phase_no') || !$request->has('st_code') || $request->st_code == ''){
            return [];
        }
        $filter         = Common::get_auth_filter();
        $request_filter = Common::get_request_filter($request);
        $ac_no          = $request_filter['ac_no'];
        $st_code        = $request_filter['st_code'];
        $dist_no        = $request_filter['dist_no'];
        $ps_no          = $request_filter['ps_no'];
        $role_id        = $request_filter['role_id'];

        $phase_filter = array_merge($filter,[
            'group_by' => 'ac_no',
            'phase_no' => $request->phase_no,
            'st_code' => $request->st_code,
        ]);

        $phases_results = PhaseModel::get_phases($phase_filter);  
        foreach($phases_results as $iterate_state){
            $data[] = [
                'st_code'   => $iterate_state['st_code'],
                'ac_no'     => $iterate_state['ac_no'],
                'ac_name'   => $iterate_state['ac_no'].'-'.$iterate_state['ac_name'],
            ];
        }
      
        return $data;
    } 


    public function load_ps_by_ajax(Request $request){
        if(!$request->has('st_code') || !$request->has('ac_no') || trim($request->ac_no) == ''){
            return [];
        }

        $filter         = Common::get_auth_filter();
        $request_filter = Common::get_request_filter($request);
        $ac_no          = $request_filter['ac_no'];
        $st_code        = $request_filter['st_code'];
        $dist_no        = $request_filter['dist_no'];
        $ps_no          = $request_filter['ps_no'];
        $role_id        = $request_filter['role_id'];

        $data = [];
        $filter_ps = [
            'st_code'       => $request->st_code,
            'ac_no'         => $request->ac_no
        ];
        if($filter['role_id'] == 19){
            $filter_ps = [
                'st_code'       => $filter['st_code'],
                'ac_no'         => $filter['ac_no']
            ];
        }
      
        $ps_results = PollingStation::get_polling_stations($filter_ps);

        foreach($ps_results as $iterate_ps){
            $data[] = [
                'st_code'      => $iterate_ps['ST_CODE'],
                'ac_no'        => $iterate_ps['AC_NO'],
                'ps_no'        => $iterate_ps['PS_NO'],
                'ps_name'      => $iterate_ps['PS_NO'].'-'.$iterate_ps['PS_NAME_EN'],
            ];
        }
      
        return $data;
    } 

    public function index($st_code=NULL,$ac_no=NULL,$ps_no=NULL)
    {
       
        $AUTH_USER = 'Boothaap';
        $AUTH_PASS = '#121#eci@';
            header('Cache-Control: no-cache, must-revalidate, max-age=0');
            $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
            $is_not_authenticated = (
                !$has_supplied_credentials ||
                $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
                $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
            );
            if ($is_not_authenticated) {
                header('HTTP/1.1 401 Authorization Required');
                header('WWW-Authenticate: Basic realm="Access denied"');
                exit;
            }
        $updb = array('updated_at'=>date("Y-m-d H:i:s"),'booth_app_excp'=>'0');
        if($st_code!=NULL && $ac_no!=NULL && $ps_no!=NULL)
        {
            $update_exm_ps = DB::table('polling_station_officer')
            ->where('st_code',$st_code)
            ->where('ac_no',$ac_no)
            ->where('ps_no',$ps_no)
            ->update($updb);
        }
        elseif($st_code!=NULL && $ac_no!=NULL && $ps_no==NULL){
            $update_exm_ps = DB::table('polling_station_officer')
            ->where('st_code',$st_code)
            ->where('ac_no',$ac_no)
            ->update($updb);
        }
        elseif($st_code!=NULL && $ac_no==NULL && $ps_no==NULL){
            $update_exm_ps = DB::table('polling_station_officer')
            ->where('st_code',$st_code)
            ->update($updb);
        }
        else{
            return 'Please give aleast state code in URL. Ex- /{st_code}/{ac_no}/{ps_no}';
        }

		
        if($update_exm_ps)
        {
            return 'Done';
        }
        else{
            return 'Something went wrong. Pls check';
        }  
    }

} // end