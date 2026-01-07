<?php

namespace App\models;

use App\models\Admin\ElectionModel;
use App\models\States;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EstimatedEntryLog extends Model
{
    protected $table = 'estimated_entry_logs';

    protected $fillable = [
        'scheduleid',
        'st_code',
        'pc_no',
        'ac_no',
        'round',
        'percentage',
        'state_percentage',
        'updatedby',
    ];

    public function state()
    {
        return $this->belongsTo(States::class, 'st_code', 'ST_CODE');
    }

    public function ac()
    {
        return $this->belongsTo(AC::class, 'ac_no', 'AC_NO');
    }

    public function phase()
    {
        return $this->belongsTo(ElectionModel::class, 'scheduleid', 'ScheduleID');
    }

    public static function roundPerWithExcludedMissedAc($data)
    {
        $q = EstimatedEntryLog::with(['phase' => function ($q) use ($data) {
            $q->where('ST_CODE', $data['state']);
        }, 'state', 'ac' => function ($q) use ($data) {
            $q->where('ST_CODE', $data['state']);
        }])->where('st_code', $data['state'])
            ->where(function ($q) use ($data) {
                if ($data['phase'] != null) {
                    $q->where('scheduleid', $data['phase']);
                }
            })
            ->where('round', $data['round'])
            ->whereTime('created_at', '>', Carbon::parse($data['startTime']))
            ->whereTime('created_at', '<', Carbon::parse($data['endTime']));
        if ($data['row'] == 'first') {
            $q->orderBy('created_at');
            $q = $q->first();
            return ($q) ? $q->state_percentage : 0;
        } elseif ($data['row'] == 'last') {
            $q->orderBy('created_at', 'desc');
            $q = $q->first();
            return ($q) ? $q->state_percentage : 0;
        }
        $q->groupBy('ac_no');
        $q->orderBy('created_at');
        $q->get();
        return count($q->get());
    }
}
