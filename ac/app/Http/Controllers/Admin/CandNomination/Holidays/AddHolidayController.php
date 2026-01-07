<?php

namespace App\Http\Controllers\Admin\CandNomination\Holidays;

use Auth;
use Common;
use Session;
use App\commonModel;
use Validator;
use App\models\Admin\E_Plan\AddHolidayModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Admin\BoothAppRevamp\AcModel;
use App\models\Admin\PcModel;

class AddHolidayController extends Controller
{
    protected $view_path_status          = 'admin.candform.holidays.';

    public function __construct() {
        $this->commonModel = new commonModel();
    }

    function add_holiday(Request $request) {
        $user_data  = Auth::user();
        $ceo_filter_array =  $user_data->st_code;
        $final_data = array();
        $data                  = AddHolidayModel::getdetailsbyst_code($user_data->st_code);
        $all_gaz_holiday       = AddHolidayModel::getdetailsbyst_code('');
        $final_holiday         = $data->merge($all_gaz_holiday)->sortBy('holiday_start_date');
        foreach ($final_holiday as $value) {
            if($value['holiday_start_date'] == $value['holiday_end_date']){
                $date_to_show = date('d-M-Y',strtotime($value['holiday_start_date'])) ;
            }else{
                $date_to_show = date('d-M-Y',strtotime($value['holiday_start_date'])).' - '.date('d-M-Y',strtotime($value['holiday_end_date']));
            }
            $final_data[] = [
                'id'                 => isset($value['id']) ? $value['id'] : '',
                'title'              => isset($value['holiday_description']) ? $value['holiday_description'] : '',
                'start'              => isset($value['holiday_start_date']) ? $value['holiday_start_date'] : '',
                'end'                => isset($value['holiday_end_date']) ? date('Y-m-d', strtotime("+1 day", strtotime($value['holiday_end_date']))) : '',
                'className'          => isset($value['holiday_color']) ? $value['holiday_color'] : '',
                'datetoshow'         => $date_to_show,
            ];
        }
        $all_data = [
            'final_data'            =>  $final_data,
            'user_data'             =>  $user_data,
            'Heading_title'         =>  'List of holidays'
        ];
        if($request->is_pdf == '1'){
            return $all_data;
        }

        return view($this->view_path_status.'add-holiday', [
          'user_data'=>$user_data,
          'final_data'=>$final_data
      ]);
    }

    function submit_add_holiday(Request $request) {
        $date = explode(" - ", $request->edate);

        if(isset($date[0]) && !empty($date[0])){
            $date_array = [
                'start_date' => date('Y-m-d', strtotime($date[0]))
            ];
        }
        if(isset($date[1]) && !empty($date[1])){
            $date_array = array_merge($date_array, ['end_date' => date('Y-m-d', strtotime($date[1]))]);
        }else{
            $date_array = array_merge($date_array, ['end_date' => date('Y-m-d', strtotime($date[0]))]);
        }
        $all_inputs = array_merge($request->all(), $date_array);

        $rules = [
            'start_date'     => 'required|date_format:Y-m-d',
            'end_date'       => 'nullable|date_format:Y-m-d',
            'ename'          => 'required',
            'ecolor'         => 'required'
        ];
        $msg = [
                'start_date.*'  => 'Holiday Date is invalid',
                'end_date.*'    => 'Holiday Date is invalid',
                'ename.*'       => 'Holiday Description is invalid',
                'ecolor.*'      => 'Holiday Type is invalid',
        ];
        $user_data = Auth::user();

        $validator = Validator::make($all_inputs, $rules, $msg);

        if ($validator->fails())
        {
            return \Response::json([
              'success' => false,
              'errors'  => $validator->getMessageBag()->toArray()
            ]);
        }

        $query_data = [
            'st_code'                         => $user_data->st_code,
            'holiday_start_date'              => $all_inputs['start_date'],
            'holiday_end_date'                => $all_inputs['end_date'],
            'holiday_description'             => $request->ename,
            'holiday_color'                    => $request->ecolor,
            'created_by'                      => $user_data->id
        ];

        $data = AddHolidayModel::updateOrCreate(
            ['id'                   => $all_inputs['event_id']], $query_data);

        if(isset($data->wasRecentlyCreated) && $data->wasRecentlyCreated == false) {
            Session::flash('success_mes', "Holiday Updated Successfully !");
        }elseif(isset($data->wasRecentlyCreated) && $data->wasRecentlyCreated == true) {
            Session::flash('success_mes', "Holiday Added Successfully !");
        }
        $data       = AddHolidayModel::getdetailsbyst_code($user_data->st_code);

        return \Response::json([
            'success' => true
        ]);
    }

    function preview_holiday(Request $request) {
        $user_data  = Auth::user();
        $final_data = array();
        $data       = AddHolidayModel::getdetailsbyst_code($user_data->st_code);
        foreach ($data as $value) {
            $final_data[] = [
                'title'              => isset($value['holiday_description']) ? $value['holiday_description'] : '',
                'start'              => isset($value['holiday_start_date']) ? $value['holiday_start_date'] : '',
                'end'                => isset($value['holiday_end_date']) ? $value['holiday_end_date'] : '',
                'className'          => isset($value['holiday_color']) ? $value['holiday_color'] : '',
            ];
        }
        return view($this->view_path_status.'preview-holiday', ['user_data'=>$user_data,'final_data'=>$final_data]);
    }

    public function print_holidays_pdf(Request $request) {
        $data = $this->add_holiday($request->merge(['is_pdf'=>'1']));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['Heading_title']));
        $pdf = \PDF::loadView($this->view_path_status.'print-holiday-pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
    }
}
