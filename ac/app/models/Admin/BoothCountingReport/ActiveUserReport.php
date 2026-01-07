<?php

namespace App\models\Admin\BoothCountingReport;
use DB;
use Illuminate\Database\Eloquent\Model;

class ActiveUserReport extends Model
{
    protected $table = 'officer_login';

    static function get_user_by_filter($st_code='', $dist_no='', $ac_no='', $degi) {
            $list = ActiveUserReport::select('id','parent_id','officername','designation','placename','name',
        'st_code','ac_no','dist_no','is_active','role_id','officerlevel','election_id');
        if(!empty($st_code)) {
            $list->where('st_code', $st_code);
        }
        if(!empty($dist_no)) {
            $list->where('dist_no', $dist_no);
        }
        if (!empty($ac_no)) {
        $list->where('ac_no', $ac_no);
        }
        $list_all = $list->where('role_id', 36)->get();
        return $list_all;
    }

    static function get_user_by_count($st_code='', $dist_no='', $ac_no='', $degi) {
        $count = ActiveUserReport::select('id','parent_id','st_code','ac_no','dist_no',
        'role_id','election_id',DB::raw('COUNT(role_id) AS totalAsistantent'));
        if(!empty($st_code)) {
            $count->where('st_code', $st_code);
        }
        if(!empty($dist_no)) {
            $count->where('dist_no', $dist_no);
        }
        if (!empty($ac_no)) {
        $count->where('ac_no', $ac_no);
        }
        $count->where('role_id', 36);
        // if(!empty($degi) && $degi == 'CEO' || $degi == 'ECI'){
        // $list_all = $count->groupby('st_code')->get();
        // }
        // if(!empty($degi) && $degi == 'DEO'){
        // $list_all = $count->groupby('dist_no')->get();
        // }
        // if(!empty($degi) && $degi == 'RO'){
        $list_all = $count->groupby(['st_code','ac_no'])->get();
        // }
        return $list_all;
    }
}
