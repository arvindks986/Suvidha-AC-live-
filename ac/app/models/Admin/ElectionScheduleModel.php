<?php 
namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth, Session, Cookie;
use Carbon\Carbon;
use DateTime;


class ElectionScheduleModel extends Model
{
    protected $table = 'm_election_details';

    public $timestamps = false;

    public static function state_schedule($data = array()){

        $election_id = Auth::user()->election_id;

       $sql_raw = "e.StatePHASE_NO AS sid, st.ST_NAME AS state,st.ST_CODE AS st_code,COUNT(a.AC_NO) AS acs, s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date,s.DATE_COUNT AS count_date,s.DTB_EL_COM as complete_date";

        $sql = DB::table('m_election_details as e')
        ->rightjoin('m_ac as a',[
              ['e.st_code', '=','a.ST_CODE'],
              ['e.CONST_NO', '=','a.AC_NO'],
        ])
        ->rightjoin('m_schedule as s',[
              ['e.PHASE_NO', '=','s.SCHEDULEID']
        ])
        ->rightjoin('m_state as st',[
              ['st.ST_CODE', '=','a.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("a.ST_CODE", $data['state']);
        }

        if(!empty($data['phase'])){
                //$sql->where("e.PHASE_NO", $data['phase']);
                $sql->where("e.StatePHASE_NO", $data['phase']);
        }

        $sql->where('e.ELECTION_ID', $election_id);
        $sql->where('e.election_status', '1');
        $sql->where('e.CONST_TYPE', 'AC');
        

        if(!empty($data['order_by'])){
            if($data['order_by']=='ac_no'){
                $sql->groupBy("a.ac_no, a.AC_NAME ASC");
            }
        }else{
            $sql->groupBy("e.PHASE_NO")->groupBy("e.st_code");
        }

        if(!empty($data['group_by'])){
            $sql->groupBy("e.PHASE_NO")->groupBy("e.st_code");
        }

        if(!empty($data['order_by'])){
            if($data['order_by']=='ac_no'){
                $sql->orderByRaw("st.ST_NAME, a.ac_no, a.AC_NAME ASC");
            }
        }else{
            $sql->orderByRaw("st.ST_NAME, e.PHASE_NO ASC");
        }

        $query = $sql->get();
     
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION STARTS
        $array = $query->map(function($obj){
          return (array) $obj;
        })->toArray();
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION ENDS

        if(!$array){
            return [];
        }
        return $array;
	
    }


    public static function ac_schedule($data = array()){

        $election_id = Auth::user()->election_id;

       $sql_raw = "e.CONST_NO AS cno, e.CONST_TYPE AS ctype, a.AC_NO AS const_no , a.AC_NAME AS const_name,e.PHASE_NO AS sid, st.ST_NAME AS state,st.ST_CODE AS st_code, s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date,s.DATE_COUNT AS count_date,s.DTB_EL_COM as complete_date";

        $sql = DB::table('m_election_details as e')
        ->rightjoin('m_ac as a',[
              ['e.st_code', '=','a.ST_CODE'],
              ['e.CONST_NO', '=','a.AC_NO'],
        ])
        ->rightjoin('m_schedule as s',[
              ['e.PHASE_NO', '=','s.SCHEDULEID']
        ])
        ->rightjoin('m_state as st',[
              ['st.ST_CODE', '=','a.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("a.ST_CODE", $data['state']);
        }

        if(!empty($data['phase'])){
                $sql->where("e.PHASE_NO", $data['phase']);
        }

        $sql->where('e.ELECTION_ID', $election_id);
        

        if(!empty($data['order_by'])){
            if($data['order_by']=='ac_no'){
                $sql->groupBy("a.AC_NO")->groupBy("a.AC_NAME");
            }
        }else{
            $sql->groupBy("a.AC_NO")->groupBy("a.AC_NAME");
        }

        if(!empty($data['order_by'])){
            if($data['order_by']=='ac_no'){
                $sql->orderByRaw("st.ST_NAME, a.AC_NO, a.AC_NAME ASC");
            }
        }else{
            $sql->orderByRaw("st.ST_NAME, e.PHASE_NO ASC");
        }

        $query = $sql->get();
     
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION STARTS
        $array = $query->map(function($obj){
          return (array) $obj;
        })->toArray();
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION ENDS

        if(!$array){
            return [];
        }
        return $array;
    
    }


    //CHECKING ELECTION EVENTS DATES 
    public static function date_diff($db_date){
  
        //$today  = Carbon::now();
        $today  = new DateTime(date('Y-m-d 00:00:00'));
        $class_name = null;
		
		///date('Y-m-d 00:00:00');

        if(!empty($db_date)){
         
           //$start_date       = Carbon::parse($db_date);
           //$diff_in_days = Carbon::parse($db_date)->diffForHumans();
           //$diff_in_days  = strtotime($today, 'Y-m-d') - strtotime($db_date, 'Y-m-d');
        
           //$diff_in_days     = $start_date->diffInDays($today);

           $db_date          = new DateTime($db_date);
         
           $interval         = $db_date->diff($today);
           $diff_in_days     = $interval->format("%r%a");

           
               if($diff_in_days < -3){
                   $class_name = 'color:#ff0000!important'; //RED
                }else if($diff_in_days == -3 || $diff_in_days == -2 || $diff_in_days  == -1){
                   $class_name = 'color:#FF7E00!important'; //YELLOW
                }else if ($diff_in_days == 0 || $diff_in_days > 0) {
                   $class_name = 'color:#209a3d!important'; //GREEN
                }

                return $class_name;
      
        }else{
            
             return $class_name = null;
        }

        
    
    }

}