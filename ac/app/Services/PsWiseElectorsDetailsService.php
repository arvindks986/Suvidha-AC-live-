<?php

namespace App\Services;

use App\Classes\xssClean;
use App\models\Admin\AcModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\polling_station\PollingStationModel;
use App\models\Admin\StateModel;
use App\models\Admin\turnout\TurnoutModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

ini_set("memory_limit", "1500M");
set_time_limit('6000');
ini_set("pcre.backtrack_limit", "50000000");
class PsWiseElectorsDetailsService
{
    /**
     * 
     * @params
     * PollingStationModel  $pollingStation
     */
    public static function getPSWiseElectorsDetails(Request $request, $action, $view_path)
    {
        try {
            $turnoutModel = new TurnoutModel();
            $data = [];
            $xss = new xssClean;
            $user = Auth::user();
            $default_phase = PhaseModel::get_current_phase();
            $phase = PhaseModel::get_state_phase(['st_code' => $user->st_code]);
            if ($phase) {
                $default_phase = $phase->sechudle_id;
            }
            $request_array = [];
            //set title
            $title_array  = [];
            $data['heading_title'] = "PS Wise Electoral Details";
            $data['ac_id'] = ($request->has('ac_id')) ? $request->ac_id : NULL;
            $data['state'] = ($request->has('state')) ? $request->state : NULL;
            if ($request->has('election_type')) {
                $data['election_type'] = $request->election_type;
                $request_array[] =  'election_type=' . $request->election_type;
            } else {
                $data['election_type'] = NULL;
            }
            $filter_for_phases = [
                'election_type' => $data['election_type']
            ];

            $data['phases'] = PhaseModel::get_phases($filter_for_phases);
            if ($request->has('phase')) {
                if ($request->phase != 'all') {
                    $data['phase'] = $request->phase;
                }
                $request_array[] =  'phase=' . $request->phase;
            } else {
                $data['phase']    = $default_phase;
                $request_array[]  =  'phase=' . $default_phase;
            }
            $data['user_data']  =   Auth::user();
            if (Auth::user()->designation == 'CEO') {
                $data['state'] = Auth::user()->st_code;
                $data['states'] = StateModel::where('st_code', $data['state'])->get();
            } else if (Auth::user()->designation == 'RO') {
                $data['state'] = Auth::user()->st_code;
                $data['states'] = StateModel::where('st_code', $data['state'])->get();
            } else {
                $data['states'] = StateModel::get_ac_states_with_filter_for_close_poll([
                    'state' => $data['state'],
                    'election_type' => $data['election_type'],
                    'phase' => $data['phase']
                ]);
            }
            // dd( $data['states']);
            $data['dist_no'] = '';
            $ac_id    = $xss->clean_input($request->ac_id);
            $filter_election = [
                'state'         => $data['state'],
                // 'ac_no'         => $ac_id,
                'phase'       => $data['phase']
            ];
            $request_array[] =  'state=' . $data['state'];
            $request_array[] =  'ac_id=' . $ac_id;

            $statename = getstatebystatecode($data['state']);
            $acame = getacbyacno($data['state'], $ac_id);

            $data['consituencies']  = AcModel::get_records([
                'state'         => $data['state'],
                'phase'       => $data['phase']
            ]);
            // dd($data['consituencies']);

            //CHECKING REQUEST VARIABES STARTS
            // if ($request->has('ac_id')) {

            // } else {
            //     $lists = $turnoutModel->get_scheduledetail([
            //         'st_code'       => $data['state'],
            //         'ac_no'         => $ac_id
            //     ]);

            //     $data['lists'] = $lists;
            //     $data['buttons']    = [];
            //     $data['action']         = url($action);
            //     $data['results'] = [];
            //     $data['consituencies']  = AcModel::get_records([
            //         'state'         => $data['state'],
            //         'phase'       => $data['phase']
            //     ]);
            // }
            $filter_election['ac_no'] = ($ac_id == 'all') ? '' : $ac_id;
            $title_array[] = "State: " . $statename->ST_NAME;
            $title_array[] = "AC: " . (($acame != null) ? $acame->AC_NAME : 'All');
            $data['filter_buttons'] = $title_array;
            //buttons
            $data['buttons']    = [];
            $data['buttons'][]  = [
                'name' => 'Export Excel',
                'href' =>  url($action . '/excel') . '?' . implode('&', $request_array),
                'target' => true
            ];
            if ($ac_id != 'all') {
                $data['buttons'][]  = [
                    'name' => 'Export Pdf',
                    'href' =>  url($action . '/pdf') . '?' . implode('&', $request_array),
                    'target' => true
                ];
            }
            $data['action']         = url($action);
            $data['ac_data'] = PollingStationModel::get_ac_data($filter_election);
            $object         = PollingStationModel::get_ps_data_for_electoral($filter_election);
            $lists = $turnoutModel->get_scheduledetail([
                'st_code'       => $data['state'],
                'ac_no'         => $ac_id
            ]);
            // dd( $object[0]);
            $data['results']    =   $object;
            $data['lists'] = $lists;
            if ($request->has('is_excel')) {
                if (isset($title_array) && count($title_array) > 0) {
                    $data['heading_title'] .= "- " . implode(', ', $title_array);
                }
                return $data;
            }
            return view($view_path, $data);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . " " . $e->getCode());
            return Redirect::back()
                ->withErrors('Something went wrong.')
                ->withInput();
        }
    }
}
