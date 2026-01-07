<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\adminmodel\DeoPcPermissionModel;
use Illuminate\Http\Request;
use Session;
use DB;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use PDF;
//use Excel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportroController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    public function reportro() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $distvalue = DB::table('m_ac')->where('ST_CODE', $d->st_code)->where('DIST_NO_HDQTR', $d->dist_no)->where('AC_NO', $d->ac_no)->get();
            $statecode = $d->st_code;
            $distval = $d->dist_no;
            $acno = $d->ac_no;
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acno' group by 1,2";
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
                                        'AC_NAME'=>$record_data->AC_NAME,
                                            'total_request'=>$record_data->total_request,
                                            'approved'=>$record_data->approved,
                                            'rejected'=>$record_data->rejected,
                                            'inprogress'=>$record_data->inprogress,
                                            'pending'=>$record_data->pending,
                                            'Cancel'=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                        
                                    }
                                    $object = json_decode(json_encode($arr));
            return view('admin.ac.ro.Permission.reportro', ['datereport'=>$object,'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'ele_details' => $ele_details, 'distvalue' => $distvalue, 'user_data' => $d]);
        }
    }

    public function permissionraw() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $st_code = $d->st_code;
             $cur_time    = Carbon::now();
            $name_excel = 'Permission Raw report'.'_'.$cur_time;
            $headings[] = ['Permission ID', 'State Name', 'District Name', 'AC Name', 'User Name', 'Permission Type', 'User Type', 'Party Name', 'Date of Submission', 'Action Date', 'Event Start Date', 'Event End Date', 'Permission Mode', 'Previous Status', 'Current Status'];
//            return Excel::create('Permission Raw report', function($excel) use ($d) {
//                        $excel->sheet('mySheet', function($sheet) use ($d) {
                            $st_code = $d->st_code;
                            $distno = $d->dist_no;
                            $acno = $d->ac_no;

                            $allrecord = $data = DB::table('permission_request as a')
                                            ->join('user_login as b', 'a.user_id', '=', 'b.id')
                                            ->join('user_data as ud', 'ud.user_login_id', '=', 'b.id')
                                            ->join('user_role as c', 'b.role_id', '=', 'c.role_id')
                                            ->join('permission_type as d', 'a.permission_type_id', '=', 'd.id')
                                            ->join('permission_master as m', 'm.id', '=', 'd.permission_type_id')
                                            ->join('m_party as mp', 'mp.CCODE', '=', 'a.party_id')
                                            ->join('m_state as ms', 'ms.ST_CODE', '=', 'a.st_code')
                                            ->select('a.*', 'ud.name', 'c.role_name', 'mp.PARTYNAME', 'ms.ST_NAME', 'm.permission_name as pname', 'a.id as permission_id', 'b.id as login _id')
                                            ->where('a.st_code', $st_code)
                                            ->where('a.dist_no', $distno)
                                            ->where('a.ac_no', $acno)
                                            ->get()->toArray();
                            $arr = array();
                            foreach ($allrecord as $excelrecord) {
                                $uservalue = DB::table('user_data')
                                        ->select('*')
                                        ->where('user_login_id', $excelrecord->user_id)
                                        ->get();
                                $stvalue = array('ST_CODE' => $excelrecord->st_code);
                                $datastate = DB::table('m_state')->select('ST_NAME')->where($stvalue)->get();
                                if ($excelrecord->dist_no != 0 or $excelrecord->dist_no != '') {
                                    $datavalue = array('ST_CODE' => $excelrecord->st_code, 'DIST_NO' => $excelrecord->dist_no);
                                    $g = DB::table('m_district')->select('DIST_NAME')->where($datavalue)->get();
                                }
                                if ($excelrecord->ac_no != 0 or $excelrecord->ac_no != '') {
                                    $acvalue = array('ST_CODE' => $excelrecord->st_code, 'AC_NO' => $excelrecord->ac_no);
                                    $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->get();
                                }

                                if ($excelrecord->cancel_status == 1) {
                                    $cancelstatus = 'Cancel';
                                } else if ($excelrecord->cancel_status == 0) {
                                    if ($excelrecord->approved_status == 0) {
                                        $cancelstatus = 'Pending';
                                    } else if ($excelrecord->approved_status == 1) {
                                        $cancelstatus = 'Inprogress';
                                    } else if ($excelrecord->approved_status == 2) {
                                        $cancelstatus = 'Accepted';
                                    } else if ($excelrecord->approved_status == 3) {
                                        $cancelstatus = 'Rejected';
                                    }
                                }
                                if ($excelrecord->permission_mode == 0) {
                                    $pmode = 'Offline';
                                } else if ($excelrecord->permission_mode == 1) {
                                    $pmode = 'Online';
                                }
                                if ($excelrecord->approved_status == 0) {
                                    $status = 'Pending';
                                } else if ($excelrecord->approved_status == 1) {
                                    $status = 'Inprogress';
                                } else if ($excelrecord->approved_status == 2) {
                                    $status = 'Accepted';
                                } else if ($excelrecord->approved_status == 3) {
                                    $status = 'Rejected';
                                }
                                $data = array(
                                    $excelrecord->reference_id,
                                    $datastate[0]->ST_NAME,
                                    $g[0]->DIST_NAME,
                                    $acname[0]->AC_NAME,
                                    $uservalue[0]->name,
                                    $excelrecord->pname,
                                    $excelrecord->role_name,
                                    $excelrecord->PARTYNAME,
                                    $excelrecord->added_at,
                                    $excelrecord->updated_at,
                                    $excelrecord->date_time_start,
                                    $excelrecord->date_time_end,
                                    $pmode,
                                    $status,
                                    $cancelstatus,
                                );
                                array_push($arr, $data);
                            }
                            return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
//                            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                'Permission ID', 'State Name', 'District Name', 'AC Name', 'User Name', 'Permission Type', 'User Type', 'Party Name', 'Date of Submission', 'Action Date', 'Event Start Date', 'Event End Date', 'Permission Mode', 'Previous Status', 'Current Status'
//                                    )
//                            );
//                        });
//                    })->download();
        } else {
            return redirect('/officer-login');
        }
    }

    public function partywise() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $statecode = $d->st_code;
            $distno = $d->dist_no;
            $acno = $d->ac_no;
             $cur_time    = Carbon::now();
            $name_excel = 'party wise report'.'_'.$cur_time;
            $headings[] = ['Party Name', 'Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
            if ($user_data) {

//                return Excel::create('Party Wise report', function($excel) use ($d, $statecode, $distno, $acno) {
//                            $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $distno, $acno) {
                                $excelrecord = "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p
JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
LEFT JOIN permission_master s ON s.id=t.permission_type_id
LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' and p.dist_no='$distno' and p.ac_no='$acno'  GROUP BY permission_name,PARTYNAME";

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
//                                    'Party Name', 'Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
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
            $distno = $d->dist_no;
            $acno = $d->ac_no;
            $cur_time    = Carbon::now();
            $name_excel = 'Permission Type report'.'_'.$cur_time;
            $headings[] = ['Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
            if ($user_data) {

//                return Excel::create('Permission Type report', function($excel) use ($d, $statecode, $distno, $acno ) {
//                            $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $distno, $acno) {
                                $excelrecord = "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' and p.dist_no='$distno' and p.ac_no='$acno' GROUP BY permission_name";


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
//                                    'Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'
//                                        )
//                                );
//                            });
//                        })->download();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function reportdates(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $statecode = $d->st_code;
            $distval = $d->dist_no;
            $acno = $d->ac_no;
            $cur_time    = Carbon::now();
            $datevalue = $req->input('datefilter');
            if(!empty($datevalue))
            {
            $dates = explode("~", $datevalue);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
             $data = DB::table('permission_request as a')
                               ->join('m_ac as ac',function($join){
                                   $join->on('ac.AC_NO','=','a.ac_no')
                                        ->on('ac.St_CODE','=','a.st_code');
                               })
                               ->select('ac.AC_NAME',DB::raw('count(*) as total_request'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as approved'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as rejected'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as pending'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                               ->where('a.st_code',$statecode)
                                ->where('a.ac_no',$acno);
                               if(!empty($_REQUEST['datefilter']))
                               {
                                    $data->whereBetween('a.created_at',[$dte1,$dte2]);
                               }
                               $data->get();
                               $result=$data->get()->toArray();
            $name_excel = 'DateWise Permission report'.'_'.$cur_time;
            $headings[] = ['AC Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
            if ($req->input('excel')) {
                return Excel::download(new ExcelExport($headings, $result), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
               
            } else {
                $pdf = PDF::loadView('admin.ac.ro.Permission.reportac', ['user_data' => $d, 'records' => $result]);
                    return $pdf->download('DateWise Permission report' . $cur_time . '.pdf');
            }
        }
    }
    
     public function permissionrawview() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $st_code = $d->st_code;
             $cur_time    = Carbon::now();
            
                            $st_code = $d->st_code;
                            $distno = $d->dist_no;
                            $acno = $d->ac_no;

                            $allrecord = $data = DB::table('permission_request as a')
                                            ->join('user_login as b', 'a.user_id', '=', 'b.id')
                                            ->join('user_data as ud', 'ud.user_login_id', '=', 'b.id')
                                            ->join('user_role as c', 'b.role_id', '=', 'c.role_id')
                                            ->join('permission_type as d', 'a.permission_type_id', '=', 'd.id')
                                            ->join('permission_master as m', 'm.id', '=', 'd.permission_type_id')
                                            ->join('m_party as mp', 'mp.CCODE', '=', 'a.party_id')
                                            ->join('m_state as ms', 'ms.ST_CODE', '=', 'a.st_code')
                                            ->select('a.*', 'ud.name', 'c.role_name', 'mp.PARTYNAME', 'ms.ST_NAME', 'm.permission_name as pname', 'a.id as permission_id', 'b.id as login _id')
                                            ->where('a.st_code', $st_code)
                                            ->where('a.dist_no', $distno)
                                            ->where('a.ac_no', $acno)
                                            ->get()->toArray();
                            $arr = array();
                            foreach ($allrecord as $excelrecord) {
                                $uservalue = DB::table('user_data')
                                        ->select('*')
                                        ->where('user_login_id', $excelrecord->user_id)
                                        ->get();
                                $stvalue = array('ST_CODE' => $excelrecord->st_code);
                                $datastate = DB::table('m_state')->select('ST_NAME')->where($stvalue)->get();
                                if ($excelrecord->dist_no != 0 or $excelrecord->dist_no != '') {
                                    $datavalue = array('ST_CODE' => $excelrecord->st_code, 'DIST_NO' => $excelrecord->dist_no);
                                    $g = DB::table('m_district')->select('DIST_NAME')->where($datavalue)->get();
                                }
                                if ($excelrecord->ac_no != 0 or $excelrecord->ac_no != '') {
                                    $acvalue = array('ST_CODE' => $excelrecord->st_code, 'AC_NO' => $excelrecord->ac_no);
                                    $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->first();
                                    $acname = $acname->AC_NAME;
                                }

                                if ($excelrecord->cancel_status == 1) {
                                    $cancelstatus = 'Cancel';
                                } else if ($excelrecord->cancel_status == 0) {
                                    if ($excelrecord->approved_status == 0) {
                                        $cancelstatus = 'Pending';
                                    } else if ($excelrecord->approved_status == 1) {
                                        $cancelstatus = 'Inprogress';
                                    } else if ($excelrecord->approved_status == 2) {
                                        $cancelstatus = 'Accepted';
                                    } else if ($excelrecord->approved_status == 3) {
                                        $cancelstatus = 'Rejected';
                                    }
                                }
                                if ($excelrecord->permission_mode == 0) {
                                    $pmode = 'Offline';
                                } else if ($excelrecord->permission_mode == 1) {
                                    $pmode = 'Online';
                                }
                                if ($excelrecord->approved_status == 0) {
                                    $status = 'Pending';
                                } else if ($excelrecord->approved_status == 1) {
                                    $status = 'Inprogress';
                                } else if ($excelrecord->approved_status == 2) {
                                    $status = 'Accepted';
                                } else if ($excelrecord->approved_status == 3) {
                                    $status = 'Rejected';
                                }
                                $data = array(
                                'reference_id'=>$excelrecord->reference_id,
                                    'ST_NAME'=>$datastate[0]->ST_NAME,
                                    'DIST_NAME'=>$g[0]->DIST_NAME,
                                    'AC_NAME'=>$acname,
                                    'name'=>$uservalue[0]->name,
                                    'pname'=>$excelrecord->pname,
                                    'role_name'=>$excelrecord->role_name,
                                    'PARTYNAME'=>$excelrecord->PARTYNAME,
                                    'added_at'=>$excelrecord->added_at,
                                    'updated_at'=>$excelrecord->updated_at,
                                    'date_time_start'=>$excelrecord->date_time_start,
                                    'date_time_end'=>$excelrecord->date_time_end,
                                    'pmode'=>$pmode,
                                    'status'=>$status,
                                    'cancelstatus'=>$cancelstatus,
                                );
                                array_push($arr, $data);
                                $acname = "";
                            }
                            $object = json_decode(json_encode($arr));
                           
            return view('admin.ac.ro.Permission.permissionrawreport', ['user_data' => $d,'rawreport'=>$object]);
        } else {
            return redirect('/officer-login');
        }
    }

     public function partywiseview() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $statecode = $d->st_code;
            $distno = $d->dist_no;
            $acno = $d->ac_no;
             $cur_time    = Carbon::now();
           
            if ($user_data) {
                                $excelrecord = "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p
JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
LEFT JOIN permission_master s ON s.id=t.permission_type_id
LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' and p.dist_no='$distno' and p.ac_no='$acno'  GROUP BY permission_name,PARTYNAME";

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
                                return view('admin.ac.ro.Permission.partywisepermissionreport', ['user_data' => $d,'partyreport'=>$object]);
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
            $distno = $d->dist_no;
            $acno = $d->ac_no;
            $cur_time    = Carbon::now();
            if ($user_data) {

//                return Excel::create('Permission Type report', function($excel) use ($d, $statecode, $distno, $acno ) {
//                            $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $distno, $acno) {
                                $excelrecord = "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' and p.dist_no='$distno' and p.ac_no='$acno' GROUP BY permission_name";


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
                                return view('admin.ac.ro.Permission.permissionwisereport', ['user_data' => $d,'permissionwisereport'=>$object]);
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function reportdatesview(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $statecode = $d->st_code;
            $distval = $d->dist_no;
            $acno = $d->ac_no;
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
             if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }  
             $distvalue = DB::table('m_ac')->where('ST_CODE', $d->st_code)->where('DIST_NO_HDQTR', $d->dist_no)->where('AC_NO', $d->ac_no)->get();
            if($req->method() == 'POST')
                {
             $cur_time    = Carbon::now();
                if ((!empty($_REQUEST['ac'])) and ( empty($_REQUEST['datefilter']))) {
                    $d = $this->commonModel->getunewserbyuserid($user->id);
//                    return Excel::create('DateWise Permission report', function($excel) use ($d, $statecode, $distval, $acno) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $distval, $acno) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acno' group by 1,2";
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
                                        'AC_NAME'=>$record_data->AC_NAME,
                                            'total_request'=>$record_data->total_request,
                                            'approved'=>$record_data->approved,
                                            'rejected'=>$record_data->rejected,
                                            'inprogress'=>$record_data->inprogress,
                                            'pending'=>$record_data->pending,
                                            'Cancel'=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                       
                                    }
                                     $object = json_decode(json_encode($arr));
                                     return view('admin.ac.ro.Permission.reportro', ['distvalue' => $distvalue, 'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter']]);
                } else if (!empty($_REQUEST['ac']) and ( !empty($_REQUEST['datefilter']))) {
                    $datevalue = $req->input('datefilter');
                    $dates = explode("~", $datevalue);
                    $dte1 = $dates[0];
                    $dte2 = $dates[1];
                    $d = $this->commonModel->getunewserbyuserid($user->id);
//                    return Excel::create('DateWise Permission report', function($excel) use ($d, $statecode, $distval, $acno, $dte1, $dte2) {
//                                $excel->sheet('mySheet', function($sheet) use ($d, $statecode, $distval, $acno, $dte1, $dte2) {
                                    $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acno' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
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
                                           'AC_NAME'=>$record_data->AC_NAME,
                                            'total_request'=>$record_data->total_request,
                                            'approved'=>$record_data->approved,
                                            'rejected'=>$record_data->rejected,
                                            'inprogress'=>$record_data->inprogress,
                                            'pending'=>$record_data->pending,
                                            'Cancel'=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                        
                                    }
                                    $object = json_decode(json_encode($arr));
                                     return view('admin.ac.ro.Permission.reportro', ['distvalue' => $distvalue, 'user_data' => $d,'datereport'=>$object,'ac_no'=>$_REQUEST['ac'],'datefilter'=>$_REQUEST['datefilter']]);
                }
        }
        else
        {
            $excelrecord = "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acno' group by 1,2";
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
                                        'AC_NAME'=>$record_data->AC_NAME,
                                            'total_request'=>$record_data->total_request,
                                            'approved'=>$record_data->approved,
                                            'rejected'=>$record_data->rejected,
                                            'inprogress'=>$record_data->inprogress,
                                            'pending'=>$record_data->pending,
                                            'Cancel'=>$record_data->Cancel,
                                        );
                                        array_push($arr, $data);
                                        
                                    }
                                    $object = json_decode(json_encode($arr));
           return view('admin.ac.ro.Permission.reportro', ['datereport'=>$object,'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'ele_details' => $ele_details, 'distvalue' => $distvalue, 'user_data' => $d]); 
        }
        }
    }
}

// end class