<?php

namespace App\Http\Controllers\Admin;

use App\adminmodel\ReportModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use App\models\Admin\StateModel;
class ReportCeoController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->PM = new ReportModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    public function reportceo() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->get();
//dd($distvalue);
            $data = DB::table('permission_request as a')
                            ->join('user_login as b', 'a.user_id', '=', 'b.id')
                            ->join('user_data as ud', 'ud.user_login_id', '=', 'b.id')
                            ->join('user_role as c', 'b.role_id', '=', 'c.role_id')
                            ->join('permission_type as d', 'a.permission_type_id', '=', 'd.id')
                            ->join('permission_master as m', 'm.id', '=', 'd.permission_type_id')
                            ->select('a.*', 'ud.name', 'c.role_name', 'm.permission_name as pname', 'a.id as permission_id', 'b.id as login _id')
                            ->where('a.st_code', $d->st_code)
                            ->get()->toArray();
            return view('admin.ac.ceo.Permission.reportceo', ['data' => $data, 'distvalue' => $distvalue, 'user_data' => $d,'filter'=>1,'ac_no'=>"",'datefilter'=>"",'district'=>"statevalue"]);
        }
    }

    public function partywise() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $statecode = $d->st_code;
            if ($user_data) {
                $cur_time    = Carbon::now();
            $name_excel = 'party wise report'.'_'.$cur_time;
            $headings[] = ['Party Name', 'Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogess', 'Pending', 'Cancel'];

//                return Excel::create('report', function($excel) use ($d, $statecode) {
//                            $excel->sheet('mySheet', function($sheet) use ($d, $statecode) {
                                $excelrecord = "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p
JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
LEFT JOIN permission_master s ON s.id=t.permission_type_id
LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' GROUP BY permission_name,PARTYNAME";

                                $records = DB::select($excelrecord);
                                $arr = array();
                                foreach ($records as $record_data) {
                                    if ($record_data->pending == '') {
                                        $record_data->pending = '0';
                                    }
                                    if ($record_data->total_request == '') {
                                        $record_data->total_request = '0';
                                    }
                                    if ($record_data->approved == '') {
                                        $record_data->approved = '0';
                                    }
                                    if ($record_data->inprogress == '') {
                                        $record_data->inprogress = '0';
                                    }
                                    if ($record_data->rejected == '') {
                                        $record_data->rejected = '0';
                                    }
                                    if ($record_data->Cancel == '') {
                                        $record_data->Cancel = '0';
                                    }
                                    $data = array(
                                        $record_data->PARTYNAME,
                                        $record_data->permission_name,
                                        $record_data->total_request,
                                        $record_data->approved,
                                        $record_data->rejected,
                                        $record_data->inprogress,
                                        $record_data->pending,
                                        $record_data->Cancel,
                                    );
                                    array_push($arr, $data);
                                }
                                return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
//                                $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                    'Party Name', 'Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogess', 'Pending', 'Cancel'
//                                        )
//                                );
//                            });
//                        })->download();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function permissiontype() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $statecode = $d->st_code;
            if ($user_data) {
            $cur_time    = Carbon::now();
            $name_excel = 'permission wise report'.'_'.$cur_time;
            $headings[] = ['Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogess', 'Pending', 'Cancel'];
//                return Excel::create('report', function($excel) use ($d, $statecode) {
//                            $excel->sheet('mySheet', function($sheet) use ($d, $statecode) {
                                $excelrecord = "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' GROUP BY permission_name";


                                $records = DB::select($excelrecord);
                                $arr = array();
                                foreach ($records as $record_data) {
                                    if ($record_data->pending == '') {
                                        $record_data->pending = '0';
                                    }
                                    if ($record_data->total_request == '') {
                                        $record_data->total_request = '0';
                                    }
                                    if ($record_data->approved == '') {
                                        $record_data->approved = '0';
                                    }
                                    if ($record_data->inprogress == '') {
                                        $record_data->inprogress = '0';
                                    }
                                    if ($record_data->rejected == '') {
                                        $record_data->rejected = '0';
                                    }
                                    if ($record_data->Cancel == '') {
                                        $record_data->Cancel = '0';
                                    }
                                    $data = array(
                                        $record_data->permission_name,
                                        $record_data->total_request,
                                        $record_data->approved,
                                        $record_data->rejected,
                                        $record_data->inprogress,
                                        $record_data->pending,
                                        $record_data->Cancel,
                                    );
                                    array_push($arr, $data);
                                }
                                return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
//                                $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                    'Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogess', 'Pending', 'Cancel'
//                                        )
//                                );
//                            });
//                        })->download();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function getDistrictsval(Request $req) {
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        if (base64_decode($_REQUEST['pc'])) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $pc = base64_decode($_REQUEST['pc']);
            $distvalue = DB::table('m_ac')->select('AC_NAME', 'AC_NO')->where('ST_CODE', $d->st_code)->where('DIST_NO_HDQTR', $pc)->get();
            return $distvalue;
        } else {
            return "record not exist";
        }
    }

    public function reportdates(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $statecode = $d->st_code;
            $cur_time = Carbon::now();
            $name_excel = 'datewise report'.'_'.$cur_time;
            if ($req->input('excel')) {
                if (($_REQUEST['pc'] == 'statevalue') and ( empty($_REQUEST['ac'])) and ( empty($_REQUEST['datefilter']))) {
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['State Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode) {
                                    $excelrecord = "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' group by 1,2";

                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->ST_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'State Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                } else if ((($_REQUEST['pc']) == 'statevalue') and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['State Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $dte1, $dte2) {
                                    $excelrecord = "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->ST_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'State Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                } else if (($_REQUEST['pc'] == 'all') and ( empty($_REQUEST['datefilter'])) and ( empty($_REQUEST['ac']))) {
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['District Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode) {
                                    $excelrecord = "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' group by 1,2";

                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->DIST_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'PC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                } else if ((($_REQUEST['pc']) == 'all') and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['District Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $dte1, $dte2) {
                                    $excelrecord = "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->DIST_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'PC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                } else if ((!empty($_REQUEST['pc'])) and ( empty($_REQUEST['datefilter'])) and ( empty($_REQUEST['ac']))) {
                    $pc = $req->input('pc');
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                     $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $pc) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $pc) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$pc' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->AC_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                } else if ((!empty($_REQUEST['pc'])) and ( !empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
//print_r($_REQUEST);
                    $pc = $req->input('pc');
                    $ac = $req->input('ac');
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $ac, $pc, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $ac, $pc, $dte1, $dte2) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac'  and s.DIST_NO_HDQTR = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->AC_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                } else if ((!empty($_REQUEST['pc'])) and ( !empty($_REQUEST['ac'])) and ( empty($_REQUEST['datefilter']))) {
                    $pc = $req->input('pc');
                    $ac = $req->input('ac');
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $ac, $pc) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $ac, $pc) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.DIST_NO_HDQTR = '$pc' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->AC_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                } else if ((!empty($_REQUEST['pc'])) and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $pc = $req->input('pc');
                    $acval = $this->commonModel->getallacbypcno($statecode, $pc);
                    $acvalue = $acval->AC_NO;
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $pc, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $pc, $dte1, $dte2) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                                    "";


                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            $record_data->AC_NAME,
                                            $record_data->total_request,
                                            $record_data->approved,
                                            $record_data->rejected,
                                            $record_data->inprogress,
                                            $record_data->pending,
                                            $record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

//                                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                        'AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                            )
//                                    );
//                                });
//                            })->download();
                }
            } else {

                if (($_REQUEST['pc'] == 'statevalue') and ( empty($_REQUEST['ac'])) and ( empty($_REQUEST['datefilter']))) {
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $excelrecord = "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' group by 1,2";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportpage', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportpage');
                } else if ((($_REQUEST['pc']) == 'statevalue') and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $excelrecord = "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportpage', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportpage');
                } else if (($_REQUEST['pc'] == 'all') and ( empty($_REQUEST['datefilter'])) and ( empty($_REQUEST['ac']))) {
                    $excelrecord = "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' group by 1,2";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportpc', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportac');
                } else if ((($_REQUEST['pc']) == 'all') and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $excelrecord = "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportpc', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportac');
                } else if ((!empty($_REQUEST['pc'])) and ( empty($_REQUEST['datefilter'])) and ( empty($_REQUEST['ac']))) {
                    $pc = $req->input('pc');
                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$pc' group by 1,2";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportac', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportac');
                } else if ((!empty($_REQUEST['pc'])) and ( !empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $pc = $req->input('pc');
                    $ac = $req->input('ac');
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];

                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac'  and s.DIST_NO_HDQTR = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportac', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportac');
                } else if ((!empty($_REQUEST['pc'])) and ( !empty($_REQUEST['ac'])) and ( empty($_REQUEST['datefilter']))) {
                    $pc = $req->input('pc');
                    $ac = $req->input('ac');
                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.DIST_NO_HDQTR = '$pc' group by 1,2";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportac', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportac');
                } else if ((!empty($_REQUEST['pc'])) and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $pc = $req->input('pc');
                    $acval = $this->commonModel->getallacbypcno($statecode, $pc);
                    $acvalue = $acval->AC_NO;
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                    "";
                    $records = DB::select($excelrecord);
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.reportac', ['user_data' => $d, 'records' => $records]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    return view('admin.ac.ceo.Permission.reportac');
                }
            }
        }
    }
    
    public function reportdatesview(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->get();
            $statecode = $d->st_code;
            if($req->method() == 'POST')
        {
                if (($_REQUEST['pc'] == 'statevalue') and ( empty($_REQUEST['ac'])) and ( empty($_REQUEST['datefilter']))) {
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['State Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode) {
                                    $excelrecord = "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' group by 1,2";

                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            "Dist_no"=>'0',      
                                            "st_code"=>$record_data->ST_CODE,
                                            "ST_NAME"=>$record_data->ST_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                   
                                    }
                                     $object = json_decode(json_encode($arr));
//                                    dd($object);
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>1]);
                } else if ((($_REQUEST['pc']) == 'statevalue') and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
//                    return Excel::create('report', function($excel) use ($d, $statecode, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $dte1, $dte2) {
                                    $excelrecord = "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                             "Dist_no"=>'0',                                             
                                             "st_code"=>$record_data->ST_CODE,
                                            "ST_NAME"=>$record_data->ST_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    
                                    }
                                    $object = json_decode(json_encode($arr));
//                                    dd($object);
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>1]);

                } else if (($_REQUEST['pc'] == 'all') and ( empty($_REQUEST['datefilter'])) and ( empty($_REQUEST['ac']))) {
                    $d = $this->commonModel->getunewserbyuserid($user->id);
//                    return Excel::create('report', function($excel) use ($d, $statecode) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode) {
                                    $excelrecord = "SELECT s.ST_CODE,s.DIST_NAME,s.DIST_NO, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' group by 1,2";

                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        //dd($record_data);
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            "Dist_no"=>$record_data->DIST_NO,
                                             "st_code"=>$record_data->ST_CODE,
                                            "DIST_NAME"=>$record_data->DIST_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     $object = json_decode(json_encode($arr));
//                                    dd($object);
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>2]);
                } else if ((($_REQUEST['pc']) == 'all') and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['PC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $dte1, $dte2) {
                                    $excelrecord = "SELECT s.ST_CODE,s.DIST_NAME,s.DIST_NO,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            "Dist_no"=>$record_data->DIST_NO,
                                             "st_code"=>$record_data->ST_CODE,
                                            "DIST_NAME"=>$record_data->DIST_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     $object = json_decode(json_encode($arr));
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>2]);
//                                    dd($object);
                } else if ((!empty($_REQUEST['pc'])) and ( empty($_REQUEST['datefilter'])) and ( empty($_REQUEST['ac']))) {
                    $pc = $req->input('pc');
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                     $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $pc) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $pc) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,s.DIST_NO_HDQTR, COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$pc' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                        $data = array(
                                            "Dist_no"=>$record_data->DIST_NO_HDQTR,
                                             "st_code"=>$record_data->ST_CODE,
                                            "AC_NAME"=>$record_data->AC_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     $object = json_decode(json_encode($arr));
//                                    dd($object);
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>0]);
                } else if ((!empty($_REQUEST['pc'])) and ( !empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
//print_r($_REQUEST);
                    $pc = $req->input('pc');
                    $ac = $req->input('ac');
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $ac, $pc, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $ac, $pc, $dte1, $dte2) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,s.DIST_NO_HDQTR,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac'  and s.DIST_NO_HDQTR = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                         $data = array(
                                            "Dist_no"=>$record_data->DIST_NO_HDQTR,
                                             "st_code"=>$record_data->ST_CODE,
                                            "AC_NAME"=>$record_data->AC_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     $object = json_decode(json_encode($arr));
//                                    dd($object);
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>0]);
                } else if ((!empty($_REQUEST['pc'])) and ( !empty($_REQUEST['ac'])) and ( empty($_REQUEST['datefilter']))) {
                    $pc = $req->input('pc');
                    $ac = $req->input('ac');
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $ac, $pc) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $ac, $pc) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,s.DIST_NO_HDQTR,COUNT(user_id)total_request,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.DIST_NO_HDQTR = '$pc' group by 1,2";
                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                         $data = array(
                                            "Dist_no"=>$record_data->DIST_NO_HDQTR,
                                             "st_code"=>$record_data->ST_CODE,
                                            "AC_NAME"=>$record_data->AC_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     $object = json_decode(json_encode($arr));
//                                    dd($object);
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>0]);
                } else if ((!empty($_REQUEST['pc'])) and ( empty($_REQUEST['ac'])) and ( !empty($_REQUEST['datefilter']))) {
                    $pc = $req->input('pc');
                    $acval = $this->commonModel->getallacbypcno($statecode, $pc);
                    $acvalue = $acval->AC_NO;
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
//                    return Excel::create('report', function($excel) use ($d, $statecode, $pc, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $pc, $dte1, $dte2) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,s.DIST_NO_HDQTR,COUNT(user_id)total_request,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
                                    "";


                                    $records = DB::select($excelrecord);
                                    $arr = array();
                                    foreach ($records as $record_data) {
                                        if ($record_data->pending == '') {
                                            $record_data->pending = '0';
                                        }
                                        if ($record_data->total_request == '') {
                                            $record_data->total_request = '0';
                                        }
                                        if ($record_data->approved == '') {
                                            $record_data->approved = '0';
                                        }
                                        if ($record_data->inprogress == '') {
                                            $record_data->inprogress = '0';
                                        }
                                        if ($record_data->rejected == '') {
                                            $record_data->rejected = '0';
                                        }
                                        if ($record_data->Cancel == '') {
                                            $record_data->Cancel = '0';
                                        }
                                       $data = array(
                                            "Dist_no"=>$record_data->DIST_NO_HDQTR,
                                            "st_code"=>$record_data->ST_CODE,
                                            "AC_NAME"=>$record_data->AC_NAME,
                                            "total_request"=>$record_data->total_request,
                                            "approved"=>$record_data->approved,
                                            "rejected"=>$record_data->rejected,
                                            "inprogress"=>$record_data->inprogress,
                                            "pending"=>$record_data->pending,
                                            "Cancel"=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                    }
                                     $object = json_decode(json_encode($arr));
//                                    dd($object);
                                     return view('admin.ac.ceo.Permission.reportceo', ['distvalue' => $distvalue,'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter'],'district'=>$_REQUEST['pc'],'filter'=>0]);
                }
        }
        else
        {
            $data = DB::table('permission_request as a')
                            ->join('user_login as b', 'a.user_id', '=', 'b.id')
                            ->join('user_data as ud', 'ud.user_login_id', '=', 'b.id')
                            ->join('user_role as c', 'b.role_id', '=', 'c.role_id')
                            ->join('permission_type as d', 'a.permission_type_id', '=', 'd.id')
                            ->join('permission_master as m', 'm.id', '=', 'd.permission_type_id')
                            ->select('a.*', 'ud.name', 'c.role_name', 'm.permission_name as pname', 'a.id as permission_id', 'b.id as login _id')
                            ->where('a.st_code', $d->st_code)
                            ->get()->toArray();
            return view('admin.ac.ceo.Permission.reportceo', ['data' => $data, 'distvalue' => $distvalue, 'user_data' => $d,'filter'=>1,'ac_no'=>"",'datefilter'=>"",'district'=>"statevalue"]);
        }
        }
    }
    

    public function permissiondetails(Request $req)
    {
          //dd(request()->segments());
         if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $cur_time    = Carbon::now();
            $perm = $this->PM->getpermisson();
            //dd($perm);
            $statevalue = StateModel::get_states();
            //dd($statevalue);
            $datevalue = $req->input('datefilter');
            $details = request()->segments();
            $st = $details[2];
            $ele = $details[3];
            $dt = $details[4];
            $dist = $details[5];
            $status = $details[6];
            if(!empty($dt) && $dt != 0)
            {
            $dates = explode("~", $dt);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            $dtt=0;
            if(!empty($dt) && $dt != 0)
            {
                $dtt = $details[4];
            }
             if($st != $d->st_code){ 

                abort(404);
            }
            else{
            $data = DB::table('permission_request as a')
                    ->join('m_state as st','a.st_code','=','st.ST_CODE')
                    ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                        $join->on('med.ST_CODE','=','a.st_code');
                    })
                    ->join('user_login as b','b.id','=','a.user_id')
                    ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                    ->join('permission_type as d','a.permission_type_id','=','d.id')
                   ->join('permission_master as m','m.id','=','d.permission_type_id')
                    ->join('m_party as p','a.party_id','=','p.CCODE')
                    ->join('user_role as c','b.role_id','=','c.role_id')
                    ->leftjoin('m_district as f',function ($join){
                        $join->on('f.DIST_NO','=','a.dist_no')
                             ->on('f.ST_CODE', '=', 'a.st_code');
                    })
                    ->leftjoin('m_ac as g',function ($join){
                        $join->on('g.AC_NO','=','a.ac_no')
                             ->on('g.ST_CODE', '=', 'a.st_code');
                    })
                    ->select('m.permission_name as pname','c.role_name','ud.name','p.PARTYNAME','f.DIST_NAME','g.AC_NAME','st.ST_NAME','a.reference_id','a.approved_status','a.cancel_status','a.permission_mode','a.added_at');
                    if(!empty($ele) && $ele != '0')
                    {
                         $data->where('med.ELECTION_TYPEID',$ele);
                    }
                     if(!empty($dist) && $dist != '0')
                    {
                         $data->where('a.dist_no',$dist);
                    }
                    if(!empty($st) && $st != '0')
                    {
                         $data->where('a.st_code',$st);
                    }
                    if(!empty($dt) && $dt != '0')
                    {
                         $data->whereBetween('a.created_at',[$dte1,$dte2]);
                    }
                    if($status != '6' && $status != '5')
                    {
                        $data->where('a.approved_status',$status)->where('a.cancel_status',0);
                    }
                    elseif($status == '5')
                    {
                        $data->where('a.cancel_status',1);
                    }
                    $data->get();
                    $result=$data->get()->toArray();
                    return view('admin.ac.ceo.Permission.reportpermissiondetails', ['election'=>$ele,'state' => $st, 'user_data' => $d,'datereport'=>$result,'datefilter'=>$dtt,'statevalue' => $statevalue]);
               
        } } else {
            return redirect('/officer-login');
        }
    }
     
    //view partywise
    public function partywiseview() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $statecode = $d->st_code;
            if ($user_data) {
                $cur_time    = Carbon::now();
            
                                $excelrecord = "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p
JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
LEFT JOIN permission_master s ON s.id=t.permission_type_id
LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' GROUP BY permission_name,PARTYNAME";

                                $records = DB::select($excelrecord);
                                $arr = array();
                                foreach ($records as $record_data) {
                                    if ($record_data->pending == '') {
                                        $record_data->pending = '0';
                                    }
                                    if ($record_data->total_request == '') {
                                        $record_data->total_request = '0';
                                    }
                                    if ($record_data->approved == '') {
                                        $record_data->approved = '0';
                                    }
                                    if ($record_data->inprogress == '') {
                                        $record_data->inprogress = '0';
                                    }
                                    if ($record_data->rejected == '') {
                                        $record_data->rejected = '0';
                                    }
                                    if ($record_data->Cancel == '') {
                                        $record_data->Cancel = '0';
                                    }
                                    $data = array(
                                        "PARTYNAME"=>$record_data->PARTYNAME,
                                        "permission_name"=>$record_data->permission_name,
                                        "total_request"=>$record_data->total_request,
                                        "approved"=>$record_data->approved,
                                        "rejected"=>$record_data->rejected,
                                        "inprogress"=>$record_data->inprogress,
                                        "pending"=>$record_data->pending,
                                        "Cancel"=>$record_data->Cancel,
                                    );
                                    array_push($arr, $data);
                                }
                               $object = json_decode(json_encode($arr));
                                return view('admin.ac.ceo.Permission.partywisepermissionreport', ['user_data' => $d,'partyreport'=>$object]);
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function permissiontypeview() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $statecode = $d->st_code;
            if ($user_data) {
            $cur_time    = Carbon::now();
            
                                $excelrecord = "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' GROUP BY permission_name";


                                $records = DB::select($excelrecord);
                                $arr = array();
                                foreach ($records as $record_data) {
                                    if ($record_data->pending == '') {
                                        $record_data->pending = '0';
                                    }
                                    if ($record_data->total_request == '') {
                                        $record_data->total_request = '0';
                                    }
                                    if ($record_data->approved == '') {
                                        $record_data->approved = '0';
                                    }
                                    if ($record_data->inprogress == '') {
                                        $record_data->inprogress = '0';
                                    }
                                    if ($record_data->rejected == '') {
                                        $record_data->rejected = '0';
                                    }
                                    if ($record_data->Cancel == '') {
                                        $record_data->Cancel = '0';
                                    }
                                    $data = array(
                                        "permission_name"=>$record_data->permission_name,
                                        "total_request"=>$record_data->total_request,
                                        "approved"=>$record_data->approved,
                                        "rejected"=>$record_data->rejected,
                                        "inprogress"=>$record_data->inprogress,
                                        "pending"=>$record_data->pending,
                                        "Cancel"=>$record_data->Cancel,
                                    );
                                    array_push($arr, $data);
                                }
                               $object = json_decode(json_encode($arr));
                                return view('admin.ac.ceo.Permission.permissionwisereport', ['user_data' => $d,'permissionwisereport'=>$object]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

}

// end class