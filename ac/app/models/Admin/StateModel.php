<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class StateModel extends Model
{
    protected $table = 'm_state';
	
	//get phase wise filter start
    public static function get_phasewise_states(){
        $sql = StateModel::join('m_ac',[
            ['m_ac.ST_CODE','=','m_state.ST_CODE']
        ])
        ->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_ac.AC_NO']
        ])
        ->where('m_election_details.CONST_TYPE','AC')
        ->whereIn('m_election_details.ScheduleID',config('public_config.phases'))
        ->orderBy('ST_NAME','ASC')
        ->groupBy('m_state.ST_CODE');
        return $sql->get();
    }
    
    public static function get_states($filter = array()){
        $election_id = Auth::user()->election_id;
        $sql = StateModel::join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
         ])->where('m_election_details.CONST_TYPE','AC')
          ->where('m_election_details.election_status','1')
          ->where('m_election_details.ELECTION_ID',$election_id)
          ->orderBy('ST_NAME','ASC');
		 if(!empty($filter['st_code'])){
            $sql->where('m_state.ST_CODE', $filter['st_code']);
        }
		$sql->groupBy('m_election_details.ST_CODE');
        return $sql->get();
    }
public static function get_stateswithac($filter = array()){
        $election_id = Auth::user()->election_id;
       /* dd($elect);
        if($elect=='3'){
$election_type='GENERAL';
        }
        elseif($elect=='4'){ 
$election_type='BYE';
        } */
        $sql = StateModel::join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
         ])->where('m_election_details.CONST_TYPE','AC')
          ->where('m_election_details.election_status','1')
          ->where('m_election_details.ELECTION_ID',$election_id)
           ->where('m_election_details.ELECTION_TYPE', 'GENERAL')
          ->orderBy('ST_NAME','ASC');
         if(!empty($filter['st_code'])){
            $sql->where('m_state.ST_CODE', $filter['st_code']);
        }
        $sql->groupBy('m_election_details.ST_CODE');
        return $sql->get();
    }
    public static function get_pc_states($filter = array()){

        $election_id = Auth::user()->election_id;

        $sql = StateModel::join('m_pc',[
            ['m_pc.ST_CODE','=','m_state.ST_CODE']
            ])->join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ])->where('m_election_details.CONST_TYPE','PC')
        ->where('m_election_details.election_status','1')
        ->where('m_election_details.ELECTION_ID',$election_id)
        ->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE');
        return $sql->get();
    }

    public static function get_pc_states_with_filter($filter = array()){


		//dd($filter);

         $election_id = Auth::user()->election_id;
        $sql = StateModel::join('m_ac',[
            ['m_ac.ST_CODE','=','m_state.ST_CODE']
        ])
        ->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_ac.AC_NO']
        ])
        ->where('m_election_details.CONST_TYPE','AC');

		if(!empty($filter['election_type'])){
            $sql->where('m_election_details.ELECTION_TYPEID',$filter['election_type']);
        }

        if(!empty($filter['phase'])){
            $sql->where('m_election_details.ScheduleID',$filter['phase']);
        }

        if(!empty($filter['state'])){
            $sql->where('m_election_details.ST_CODE',$filter['state']);
        }

        $sql->where('m_election_details.CONST_TYPE','AC');
        $sql->where('m_election_details.election_status','1');
        $sql->where('m_election_details.ELECTION_ID',$election_id);

        $sql->select('m_state.*')->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE');
        return $sql->get();
    }

    public static function get_ac_states_with_filter_for_close_poll($filter = array()){


		//dd($filter);

         $election_id = Auth::user()->election_id;
        $sql = StateModel::join('pd_schedulemaster',[
            ['pd_schedulemaster.st_code','=','m_state.ST_CODE']
        ])
        ->where('pd_schedulemaster.const_type','AC');

		if(!empty($filter['election_type'])){
            $sql->where('pd_schedulemaster.election_type_id',$filter['election_type']);
        }

        if(!empty($filter['phase'])){
            $sql->where('pd_schedulemaster.schedule_id',$filter['phase']);
        }

        if(!empty($filter['state'])){
            $sql->where('pd_schedulemaster.st_code',$filter['state']);
        }
        $sql->where('pd_schedulemaster.electionid',$election_id);

        $sql->select('m_state.*')->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE');
        return $sql->get();
    }

    public static function get_state_by_code($state_code = ''){
        $sql = StateModel::where('ST_CODE',$state_code)->first();
        if(!$sql){
            return false;
        }
        return $sql->toArray();
    }
//Jitendra Code Statrt
	
	
	public static function get_states_index($filter = array()){
		
		$sql = StateModel::select('m_state.ST_CODE','m_state.ST_NAME')
			->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
			->where('m_election_details.CONST_TYPE','AC')
			->where('m_election_details.CURRENTELECTION','Y')
			->orderBy('m_state.ST_NAME','ASC')
			->groupBy('m_state.ST_CODE')
			->get();
        return $sql;
    }
	
	public static function get_acs($filter = array()){
		
        $sql = AcModel::join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_ac.AC_NO']
        ]);

        $sql->where('m_election_details.CONST_TYPE','AC');
        $sql->where('m_election_details.CURRENTELECTION','Y');
		
		
        if(!empty($filter['st_code']) && isset($filter['st_code'])){
            $sql->where('m_ac.ST_CODE',$filter['st_code']);
        }
        if(!empty($filter['ac_no']) && isset($filter['ac_no'])){
            $sql->where('m_ac.AC_NO',$filter['ac_no']);
        }
        $query = $sql->select('m_ac.AC_NO as ac_no','m_ac.AC_NAME as ac_name','m_ac.ST_CODE as st_code')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy('m_ac.AC_NO')->groupBy("m_ac.ST_CODE")->get();
        return $query;
    }
	
	public static function get_states_index_bye($filter = array()){
		
		$sql = StateModel::select('m_state.ST_CODE','m_state.ST_NAME')
			->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
			->where('m_election_details.CONST_TYPE','AC')
			->where('m_election_details.CURRENTELECTION','Y')
			->where('m_election_details.ELECTION_TYPE','BYE')
			->orderBy('m_state.ST_NAME','ASC')
			->groupBy('m_state.ST_CODE')
			->get();
        return $sql;
    }
	
	public static function get_acs_bye($filter = array()){
		
        $sql = AcModel::join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_ac.AC_NO']
        ]);

        $sql->where('m_election_details.CONST_TYPE','AC');
        $sql->where('m_election_details.CURRENTELECTION','Y');
        $sql->where('m_election_details.ELECTION_TYPE','BYE');
		
		
        if(!empty($filter['st_code']) && isset($filter['st_code'])){
            $sql->where('m_ac.ST_CODE',$filter['st_code']);
        }
        if(!empty($filter['ac_no']) && isset($filter['ac_no'])){
            $sql->where('m_ac.AC_NO',$filter['ac_no']);
        }
		
		
        $query = $sql->select('m_ac.AC_NO as ac_no','m_ac.AC_NAME as ac_name','m_ac.ST_CODE as st_code')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy('m_ac.AC_NO')->groupBy("m_ac.ST_CODE")->get();
        return $query;
    }
	
	
	
	public static function get_list($filter = array()){
      $sql = AcModel::join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_ac.AC_NO']
        ]);
		
		$sql->join('m_state',[
            ['m_state.ST_CODE', '=','m_ac.ST_CODE']
        ]);

        $sql->where('m_election_details.CONST_TYPE','AC');
        $sql->where('m_election_details.CURRENTELECTION','Y');
        $sql->where('m_election_details.ELECTION_TYPE','BYE');
		
		
        if(!empty($filter['st_code']) && isset($filter['st_code'])){
            $sql->where('m_ac.ST_CODE',$filter['st_code']);
        }
        if(!empty($filter['ac_no']) && isset($filter['ac_no'])){
            $sql->where('m_ac.AC_NO',$filter['ac_no']);
        }
        $query = $sql->select('m_state.ST_Name as st_name','m_ac.AC_NO as ac_no','m_ac.AC_NAME as ac_name','m_ac.ST_CODE as st_code')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy('m_ac.AC_NO')->groupBy("m_ac.ST_CODE")->get();
				
		return $query;
    }
	
	//Jitendra Code End
}

