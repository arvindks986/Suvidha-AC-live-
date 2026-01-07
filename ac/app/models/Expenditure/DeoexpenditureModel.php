<?php

namespace App\models\Expenditure;

use Illuminate\Database\Eloquent\Model;
use DB;

class DeoexpenditureModel extends Model {

    protected $table = 'expenditure_reports';
    protected $fillable = ['candidate_id', 'constituency_no', 'ST_CODE', 'date_of_declaration', 'date_of_account_rec_meetng',
        'reconciliation_meeting_writing', 'agent_attend_meeting', 'defect_reconciliation_meeting',
        'last_date_prescribed_acct_lodge', 'candidate_lodged_acct', 'date_orginal_acct', 'date_revised_acct',
        'account_lodged_time', 'not_lodged_period_delay', 'reason_lodged_not_lodged', 'explaination_by_candidate',
        'comment_by_deo', 'grand_total_election_exp_by_cadidate',
        'rp_act', 'comprising', 'comprising_comment', 'duly_sworn', 'duly_sworn_comment', 'Vouchers', 'Vouchers_comment',
        'seprate', 'seprate_comment', 'routed', 'routed_comment', 'rectifying', 'rectifying_comment', 'rectified', 'rectified_comment',
        'comment_of_deo', 'created_at', 'updated_at',
        'agent_attend_meeting_comment',
        'defect_reconciliation_meeting_comment',
        'candidate_lodged_acct_comment',
        'reason_lodged_not_lodged_comment',
         'dist_no','election_id'
    ];

    public static function Add($dataArray) {
        return self::create($dataArray);
    }

    public static function updateData($checkArrayData, $candidateId, $ac_no) {
        //dd($checkArrayData);

        return self::where('candidate_id', $candidateId)->where('constituency_no', $ac_no)
        ->update($checkArrayData);
    }
     public static function updateDatacandidate($checkArrayData, $candidateId) {
        

        return self::where('candidate_id', $candidateId)
        ->update($checkArrayData);
    }
 
   public static function viewById($candidateId,$ac_no) {
        
        $data = DB::select("SELECT
            P.PARTYNAME,
    C.candidate_id as c_id,
    C.cand_name,
    C.candidate_residence_address,
    R.candidate_id,
    N.ac_no,
    R.constituency_no,
    R.date_of_declaration,
    R.date_of_account_rec_meetng,
    R.reconciliation_meeting_writing,
    R.reconciliation_meeting_writing_comment,
    R.agent_attend_meeting,    
    R.defect_reconciliation_meeting,
    R.last_date_prescribed_acct_lodge,
    R.candidate_lodged_acct,
    R.date_orginal_acct,
    R.date_revised_acct,
    R.account_lodged_time,
    R.not_lodged_period_delay,
    R.reason_lodged_not_lodged,
    R.explaination_by_candidate,
    R.comment_by_deo,
    R.grand_total_election_exp_by_cadidate,
    R.rp_act,
    R.comprising,
    R.comprising_comment,
    R.duly_sworn,
    R.duly_sworn_comment,
    R.Vouchers,
    R.Vouchers_comment,
    R.seprate,
    R.seprate_comment,
    R.routed,
    R.routed_comment,
    R.rectifying,
    R.rectifying_comment,
    R.rectified,
    R.rectified_comment,
    R.comment_of_deo,
    R.agent_attend_meeting_comment, 
    R.defect_reconciliation_meeting_comment,
    R.reason_lodged_not_lodged_comment,
    R.candidate_lodged_acct_comment,
    R.finalized_status,
      R.noticefile,
    R.notice_date       
FROM
    candidate_personal_detail C
LEFT JOIN expenditure_reports R ON
    R.candidate_id = C.candidate_id      
    INNER JOIN candidate_nomination_detail N ON N.candidate_id = C.candidate_id
    INNER JOIN m_party P on P.CCODE = N.party_id
WHERE
    C.candidate_id = $candidateId and R.constituency_no=$ac_no ");
        return $data = !empty($data) && count($data) > 0 ? $data[0] : [];
    }

    public static function isCandidate($candidateId,$acno) {

        $response = self::where('candidate_id', '=', $candidateId)->where('constituency_no', '=', $acno)->first();
        return !empty($response) ? true : false;
    }

    public static function getCandidateByCandidateId($candidateId) {
        $data = DB::select("SELECT
                            `st_code`,
                            `district_no`,
                            `pc_no`
                          FROM
                            `candidate_nomination_detail`  C
                          WHERE 
                            C.candidate_id = $candidateId");
        return $data = !empty($data) && count($data) > 0 ? $data[0] : [];
    }

    public static function getPCName($pcNo, $districtId, $statecode) {
        $data = DB::select("SELECT
                            PC_NAME_EN                            
                            FROM
                              dist_pc_mapping                         
                             WHERE
                            dc_id_id = '$districtId' AND ST_CODE = '$statecode' AND PC_NO = '$pcNo'");
        return $data = !empty($data) && count($data) > 0 ? $data[0] : [];
    }

}
