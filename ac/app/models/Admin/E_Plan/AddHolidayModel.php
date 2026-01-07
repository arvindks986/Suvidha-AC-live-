<?php

namespace App\models\Admin\E_Plan;

use Illuminate\Database\Eloquent\Model;
use App\models\Admin\E_Plan\ElectionTermModel;

class AddHolidayModel extends Model
{
    protected $table = 'eplan_holiday_master';
    protected $guarded  = [];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getdetailsbyst_code($st_code) {
        $data = AddHolidayModel::select('id','holiday_start_date', 'st_code', 'holiday_end_date',
                                        'holiday_description', 'holiday_color','created_by','finalize_status'
                                        ,'created_at');
        if(!empty($st_code)){
            $data->where('st_code', $st_code);
        }                    
        $final_data = $data->get();
        return $final_data;
    }

    public static function getallgaz() {
        $data = AddHolidayModel::select('id','holiday_start_date', 'st_code', 'holiday_end_date',
                                        'holiday_description', 'holiday_color','created_by','finalize_status'
                                        ,'created_at')->where('holiday_color', 'fc-bg-blue');                  
        $final_data = $data->get();
        return $final_data;
    }

    public static function checkforholidays($data) {
        $data = AddHolidayModel::select('holiday_description','holiday_color','st_code')->whereIn('holiday_color', ['fc-bg-blue'])
                ->where('holiday_start_date', $data)->get()->toArray();
        $status['details'] = $data;
        count($data)>0 ? $status['status'] = true : $status['status'] = false ;
        return $status;
    }

    public static function checkfor_st_holidays($data) { 
        $data = AddHolidayModel::select('holiday_description','holiday_color', 'st_code')->whereIn('holiday_color', ['fc-bg-lightgreen'])
                ->where('holiday_start_date', $data)->get()->toArray();
        $status['details'] = $data;
        count($data)>0 ? $status['status'] = true : $status['status'] = false ;
        return $status;
    }

    public static function checkforavoidable_date($data, $st_code) {
        $st_code_arr = [];
        if(!empty($st_code)){
            $st_code_arr = \explode(',', $st_code);
        }
        $result_data = [];
        $status = [];
        foreach($st_code_arr as $each){
            $result_data = AddHolidayModel::select('holiday_description','holiday_color','st_code')
            ->where('holiday_color','=' ,'fc-bg-lightblue')
            ->where('finalize_status', '=' ,'1')
            ->where('holiday_start_date', '<=' ,$data)
            ->where('holiday_end_date', '>=' ,$data)
            ->where('st_code', '=' ,$each)
            ->get()->toArray();
            $status['details'] = $result_data;
            count($result_data)>0 ? $status['status'] = true : $status['status'] = false ;
        } 
        return $status;
    }

    public static function checkforout_of_rage($data, $st_code){
        $st_code_arr = [];
        if(!empty($st_code)){
            $st_code_arr = \explode(',', $st_code);
        }
        $status = false;
        $temp_arr = [];
        foreach($st_code_arr as $each){
            $temp = AddHolidayModel::get_outer_limit_date($each);
            ($temp != false) ? $temp = $temp['terms_of_election'] : $temp = '';
            array_push($temp_arr, $temp);
        }
        $date_max = isset($temp_arr[0]) ? $temp_arr[0] : '';
        if(count($temp_arr)>1){
            $max = max(array_map('strtotime', $temp_arr));
            $date_max = date('Y-m-d', $max);
        }
        $date_max<$data ? $status = true : $status ;
        return $status;
    }

    public static function getalldetails($filter=array()) {
        $data = AddHolidayModel::select('id','holiday_start_date', 'st_code', 'holiday_end_date',
                                        'holiday_description', 'holiday_color','created_by','finalize_status'
                                        ,'created_at')->where('finalize_status', '1');
        if(!empty($filter['date'])){
            $data->whereIn('holiday_start_date', $filter['date']);
        }

        if(!empty($filter['st_code'])){
            $data->whereNull('st_code')->orWhereIn('st_code', $filter['st_code']);
        }
        
        $final_data = $data->get();
        return $final_data;
    }

    public static function get_final_status($st_code) {
        $data = AddHolidayModel::select('st_code','finalize_status');
        $data->where('st_code', $st_code);                    
        $final_data = $data->first();
        return ($final_data && $final_data->finalize_status == 1) ? true : false;
    }

    public static function get_final_status_ceo($st_code) {
        $data = AddHolidayModel::select('st_code','finalize_status','ceo_finalize_status');
        $data->where('st_code', $st_code);                    
        $final_data = $data->first();
        return ($final_data && $final_data->ceo_finalize_status == 1) ? true : false;
    }

    public static function getallfinalizestate() {
        $data = AddHolidayModel::select('st_code','finalize_status')
                ->where('finalize_status', '1')->where('st_code','!=','null')->groupBy('st_code')->get()->toArray();
        return $data;
    }

    public static function get_outer_limit_date($st_code){
        $data = ElectionTermModel::select('term_end_date')
                ->where('st_code','!=','null')
                ->where('term_end_date','!=','null');
                if(!empty($st_code)){
                    $data->where('st_code','=',$st_code);
                }
        $response = ($data->first() != null) ? $data->first()->toArray() : false;
        return $response;
    }
}