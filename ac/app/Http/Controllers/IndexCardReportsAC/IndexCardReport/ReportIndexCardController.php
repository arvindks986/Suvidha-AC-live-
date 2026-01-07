<?php

namespace App\Http\Controllers\IndexCardReportsAC\IndexCardReport;

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
    use MPDF;
    use App;
    use App\commonModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\ACCEOModel;
	use App\adminmodel\ACCEOReportModel;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Crypt;
    use Excel;
	
	use App\models\indexcard\ConstituencyDataSummary;
	use App\models\indexcard\Highlights;
	use App\models\indexcard\Annxure;
	use App\models\indexcard\ElectorsDataSummary;

   
	
	ini_set("memory_limit","1500M");
    set_time_limit('240');
    ini_set("pcre.backtrack_limit", "100000000");

class ReportIndexCardController extends Controller
{
  public function __construct(){

	$this->middleware(['auth:admin', 'auth']);
       $this->middleware(function (Request $request, $next) {
           if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
           }

           $user = Auth::user();
           switch ($user->role_id) {
               case '7':
                   $this->middleware('eci');
                   break;
               case '4':
                   $this->middleware('ceo');
                   break;
               case '18':
                   $this->middleware('ro');
                   break;
			  case '27':
                   $this->middleware('eci_index');
                   break;

               default:
                   $this->middleware('eci');
           }
           return $next($request);
       });

        $this->middleware('adminsession');
        $this->commonModel = new commonModel();
        $this->ceomodel = new ACCEOModel();
        $this->acceoreportModel = new ACCEOReportModel();
        $this->xssClean = new xssClean;
    }
	protected function guard(){
        return Auth::guard();
    }


    public function highlight(request $request, $st_code){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);

        $session['election_detail'] = array();

        $user_data = $d;
$y = getElectionYear();
      $st_code = $st_code;

      $actypecount = DB::select("SELECT SUM(CASE WHEN AC_TYPE = 'GEN' THEN 1 ELSE 0 END) AS genac, SUM(CASE WHEN AC_TYPE = 'SC' THEN 1 ELSE 0 END) AS scac, SUM(CASE WHEN AC_TYPE = 'ST' THEN 1 ELSE 0 END) AS stac FROM m_ac inner join m_election_details as med on med.st_code = m_ac.ST_CODE and med.CONST_NO = m_ac.AC_NO where med.CONST_TYPE = 'AC' and med.CURRENTELECTION = 'Y' and m_ac.ST_CODE = '$st_code'");



      $candidates = DB::select("SELECT COUNT(cand_count) AS 'No_of_Seats',
                    SUM(CASE WHEN cand_count = 1 THEN 1 ELSE 0 END) AS 'one' ,
                    SUM(CASE WHEN cand_count = 2 THEN 1 ELSE 0 END) AS 'two',
                    SUM(CASE WHEN cand_count = 3 THEN 1 ELSE 0 END) AS 'three',
                    SUM(CASE WHEN cand_count = 4 THEN 1 ELSE 0 END) AS 'four',
                    SUM(CASE WHEN cand_count = 5 THEN 1 ELSE 0 END) AS 'five',
                    SUM(CASE WHEN cand_count > 5 AND cand_count <= 10 THEN 1 ELSE 0 END) AS 'fiveten',
                    SUM(CASE WHEN cand_count > 10 AND cand_count <= 15 THEN 1 ELSE 0 END) AS 'tenfifteen',
                    SUM(CASE WHEN cand_count > 15  THEN 1 ELSE 0 END) AS 'fifteen',

                    SUM(cand_count) AS 'Total_Candidates',
                    MIN(cand_count) as maxcnd,MAX(cand_count) as mincnd,ROUND(SUM(cand_count)/COUNT(cand_count),0) AS 'Avg' FROM (SELECT cnd.st_code,cnd.pc_no,
                    COUNT(distinct(cnd.candidate_id)) 'cand_count' FROM candidate_nomination_detail as cnd
                    inner join m_election_details as med on med.st_code = cnd.st_code and med.CONST_NO = cnd.ac_no
                    WHERE cnd.application_status = '6' AND cnd.finalaccepted = '1' AND cnd.party_id != '1180' and med.CONST_TYPE = 'AC' and  med.CURRENTELECTION = 'Y' and cnd.st_code = '$st_code' and cnd.symbol_id != '200' GROUP BY cnd.st_code, cnd.ac_no) a");


    $electorsvotersdata = DB::select("SELECT
                          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS maleElectors,
                          SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS femaleElectors,
                          SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS thirdElectors,
                          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+
                            ec.service_female_electors+ec.nri_female_electors+ec.gen_electors_other+
                            ec.nri_third_electors+ec.service_third_electors) AS totalElectors,
                          SUM(ec.service_male_electors) AS maleServiceElector,
                          SUM(ec.service_female_electors) AS femaleServiceElector

                          FROM electors_cdac as ec

                          inner join m_election_details as med on med.st_code = ec.ST_CODE and med.CONST_NO = ec.ac_no


                          WHERE ec.YEAR = '$y' and  med.CONST_TYPE = 'AC' and  med.CURRENTELECTION = 'Y' and ec.st_code = '$st_code'");

    $bt = 'counting_master_'.strtolower($st_code);

    $totalvotesfromRm = DB::select("SELECT SUM(postal_total_votes) AS total_postal_vote_received, SUM(rejected_votes) AS rejectedpostalvote,
                        SUM(tended_votes) AS tended_votes
                        FROM round_master WHERE st_code = '$st_code'");

    $totalvotesfromCp1 = DB::select("SELECT SUM(total_vote) AS totalEvmPostalvote, SUM(postalballot_vote) AS totalvalidpostalvote FROM `".$bt."` WHERE party_id != '1180' ");


    $totalvote =  DB::select("SELECT SUM(ecoi.general_male_voters+ecoi.nri_male_voters) AS totalMaleVoters,
                          SUM(ecoi.general_female_voters+ecoi.nri_female_voters) AS totalFemaleVoters,
                          SUM(ecoi.general_other_voters+ecoi.nri_other_voters) AS totalOtherVoters,
                          SUM(ecoi.service_postal_votes_under_section_8+ecoi.service_postal_votes_gov) AS totalPostalVoters,
                          SUM(ecoi.total_polling_station_s_i_t_c) AS totalpollingstation,
                          SUM(ecoi.votes_not_retreived_from_evm) AS votes_not_retreived_from_evm ,
                          SUM(ecoi.rejected_votes_due_2_other_reason) AS rejected_votes_due_2_other_reason ,
                          SUM(ecoi.proxy_votes) AS proxy_votes,
                          SUM(ecoi.test_votes_49_ma) AS test_votes_49_ma
                          FROM electors_cdac_other_information AS ecoi
                          INNER JOIN m_election_details AS med ON med.st_code = ecoi.ST_CODE AND med.CONST_NO = ecoi.AC_NO
                          WHERE   med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y' AND  ecoi.`st_code` = '$st_code'" );

    $notavote = DB::select("SELECT SUM(cp.total_vote) AS notatotal,
                SUM(cp.postalballot_vote) AS notapostaltotal
                FROM `".$bt."` AS cp
                WHERE cp.`party_id` = '1180'");

    $fddate = DB::select("SELECT
              SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS actotalvotes FROM `".$bt."` AS cp1
              WHERE cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no AND cp.ac_no =cp1.ac_no AND C.cand_gender = 'male' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) AS fdmale,

              SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS actotalvotes FROM `".$bt."` AS cp1
              WHERE cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no AND  C.cand_gender = 'female' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) AS fdfemale,

              SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS actotalvotes FROM `".$bt."` AS cp1
              WHERE cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no AND  C.cand_gender = 'third' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) AS fdthird,


              SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS actotalvotes FROM `".$bt."` AS cp1
              WHERE cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) AS fd


              FROM `".$bt."` AS cp
              JOIN candidate_personal_detail AS C ON C.candidate_id = cp.candidate_id
              WHERE cp.candidate_id NOT IN (SELECT candidate_id FROM winning_leading_candidate AS w1 WHERE w1.ac_no = cp.ac_no)
              AND cp.party_id != 1180");

    $wincandidatedatemale = DB::select("SELECT COUNT(leading_id) AS totalwinnermale
                           FROM winning_leading_candidate AS wlc
                           INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`

                          WHERE cpd.cand_gender = 'male' AND wlc.`st_code` = '$st_code'");


    $wincandidatedatefemale = DB::select("SELECT COUNT(leading_id) AS totalwinnerfemale
                           FROM winning_leading_candidate AS wlc
                           INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`

                          WHERE cpd.cand_gender = 'female' AND wlc.`st_code` = '$st_code'");

    $wincandidatedatethird = DB::select("SELECT COUNT(leading_id) AS totalwinnerthird
                           FROM winning_leading_candidate AS wlc
                           INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`

                          WHERE cpd.cand_gender = 'third' AND wlc.`st_code` = '$st_code'");

    $totalnominatedmale = DB::select("SELECT COUNT(distinct(wlc.nom_id)) AS totalnominatedmale
                          FROM candidate_nomination_detail AS wlc
                          INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
						  INNER JOIN m_election_details AS med ON med.st_code = wlc.ST_CODE AND med.CONST_NO = wlc.AC_NO
                          WHERE   med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y'
                          AND cpd.cand_gender = 'male' AND wlc.application_status = '6'
                          AND wlc.finalaccepted = '1' AND wlc.party_id != '1180'  AND wlc.`st_code` = '$st_code' and wlc.symbol_id != '200'");

     $totalnominatedfemale = DB::select("SELECT COUNT(distinct(wlc.nom_id)) AS totalnominatedfemale
                          FROM candidate_nomination_detail AS wlc
                          INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                          INNER JOIN m_election_details AS med ON med.st_code = wlc.ST_CODE AND med.CONST_NO = wlc.AC_NO
                          WHERE   med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y'
                          AND cpd.cand_gender = 'female' AND wlc.application_status = '6'
                          AND wlc.finalaccepted = '1' AND wlc.party_id != '1180' AND wlc.`st_code` = '$st_code' and wlc.symbol_id != '200'");

      $totalnominatedthird = DB::select("SELECT COUNT(distinct(wlc.nom_id)) AS totalnominatedthird
                          FROM candidate_nomination_detail AS wlc
                          INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                          INNER JOIN m_election_details AS med ON med.st_code = wlc.ST_CODE AND med.CONST_NO = wlc.AC_NO
                          WHERE   med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y'
                          AND cpd.cand_gender = 'third' AND wlc.application_status = '6'
                          AND wlc.finalaccepted = '1' AND wlc.party_id != '1180' AND wlc.`st_code` = '$st_code' and wlc.symbol_id != '200'");

      $noofrepolls = DB::select("SELECT COUNT(date_of_repoll) AS total_repoll
                    FROM `electors_cdac_other_information`
                    INNER JOIN m_election_details AS med ON med.st_code = electors_cdac_other_information.st_code
                    AND med.CONST_NO = electors_cdac_other_information.ac_no
                    WHERE  electors_cdac_other_information.date_of_repoll !='Null' AND med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y'
                    AND electors_cdac_other_information.`st_code` = '$st_code'");



      $candidates = (object) array_merge(
        (array) $candidates[0], (array) $actypecount[0], (array) $electorsvotersdata[0], (array) $totalvotesfromRm[0],
        (array) $totalvotesfromCp1[0], (array) $totalvote[0], (array) $notavote[0], (array) $fddate[0],
        (array) $wincandidatedatemale[0], (array) $wincandidatedatefemale[0], (array) $wincandidatedatethird[0],(array) $totalnominatedmale[0],
         (array) $totalnominatedfemale[0], (array) $totalnominatedthird[0], (array) $noofrepolls[0]);

          if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
          }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
          }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
          }else if($user->role_id == '7'){
                  $prefix     = 'eci';
          }
		  
		//dd($candidates);

            if($request->path() == "$prefix/highlights/$st_code"){
                return view("IndexCardReports.IndexCardReports.highlight",compact('candidates','user_data','st_code'));
            }elseif($request->path() == "$prefix/highlights-pdf/$st_code"){
                   $pdf=PDF::loadView('IndexCardReports.IndexCardReports.highlight_pdf',compact('candidates','user_data','st_code'));
				 // code for verified pdf check and upload report no 4
				 
				 if(verifyreport(4, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'highlights'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '4',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				
				//code ends here
				   
		
                   return $pdf->download('4-Highlight.pdf');
            }elseif($request->path() == "$prefix/highlights-excel/$st_code"){		
				return Excel::download(new Highlights($candidates), '4-Highlight.xlsx');

       }




    }


    // Report Highlights Ends Here

    // Report Electores data Summary function start

    public function electorsdatasummary(request $request, $st_code){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);

        $session['election_detail'] = array();

        $user_data = $d;
		$y = getElectionYear();
        $st_code = $st_code;

        $bt = 'counting_master_'.strtolower($st_code);

        $actypecount = DB::select("SELECT SUM(CASE WHEN AC_TYPE = 'GEN' THEN 1 ELSE 0 END) AS genac,
                       SUM(CASE WHEN AC_TYPE = 'SC' THEN 1 ELSE 0 END) AS scac, SUM(CASE WHEN AC_TYPE = 'ST' THEN 1 ELSE 0 END) AS stac
                       FROM m_ac inner join m_election_details as med on med.st_code = m_ac.ST_CODE and med.CONST_NO = m_ac.AC_NO
                       where med.CONST_TYPE = 'AC' and med.CURRENTELECTION = 'Y' and m_ac.ST_CODE = '$st_code'");


        $electorsvotersdata = DB::select("SELECT m_ac.`AC_TYPE`,

                              SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS maleElectors,
                              SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS femaleElectors,
                              SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS thirdElectors,
                              SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+
                                  ec.service_female_electors+ec.nri_female_electors+ec.gen_electors_other+
                                   ec.nri_third_electors+ec.service_third_electors) AS totalElectors,
                              SUM(ec.service_male_electors) AS maleServiceElector,
                              SUM(ec.service_female_electors) AS femaleServiceElector,
                              SUM(ec.nri_male_electors) AS overseasmaleElector,
                              SUM(ec.nri_female_electors) AS overseasFemaleElector,
                              SUM(ec.nri_third_electors) AS overseasthirdElector,
                              SUM(CASE WHEN m_ac.`AC_TYPE` = 'GEN' THEN 1 ELSE 0 END) AS totalgenac,
                              SUM(CASE WHEN m_ac.`AC_TYPE` = 'SC' THEN 1 ELSE 0 END) AS totalscac,
                              SUM(CASE WHEN m_ac.`AC_TYPE` = 'ST' THEN 1 ELSE 0 END) AS totalstac


                              FROM electors_cdac AS ec

                              INNER JOIN m_election_details AS med ON med.st_code = ec.ST_CODE AND med.CONST_NO = ec.ac_no
                              INNER JOIN m_ac ON m_ac.`AC_NO` = ec.`ac_no` AND m_ac.`ST_CODE` = ec.`st_code`
                              WHERE ec.YEAR = '$y' AND  med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y' AND  ec.st_code = '$st_code'
                              GROUP BY m_ac.`AC_TYPE`");


          $totalvote =    DB::select("SELECT m_ac.`AC_TYPE`,
                          SUM(ecoi.general_male_voters+ecoi.nri_male_voters) AS totalMaleVoters,
                          SUM(ecoi.general_female_voters+ecoi.nri_female_voters) AS totalFemaleVoters,
                          SUM(ecoi.general_other_voters+ecoi.nri_other_voters) AS totalOtherVoters,
                          SUM(ecoi.total_polling_station_s_i_t_c) AS totalpollingstation,
                          SUM(ecoi.votes_not_retreived_from_evm) AS votes_not_retreived_from_evm ,
                          SUM(ecoi.rejected_votes_due_2_other_reason) AS rejected_votes_due_2_other_reason ,
                          SUM(ecoi.proxy_votes) AS proxy_votes,
                          SUM(ecoi.test_votes_49_ma) AS test_votes_49_ma,
                          sum(ecoi.nri_male_voters) as overseasmalevoters,
                          sum(ecoi.nri_female_voters) as overseasFemalevoters,
                          sum(ecoi.nri_other_voters) as overseasthirdvoters

                          FROM electors_cdac_other_information AS ecoi

                          INNER JOIN m_election_details AS med ON med.st_code = ecoi.ST_CODE AND med.CONST_NO = ecoi.AC_NO
                          INNER JOIN m_ac ON m_ac.`AC_NO` = ecoi.`ac_no` AND m_ac.`ST_CODE` = ecoi.`st_code`

                          WHERE   med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y' AND  ecoi.`st_code` = '$st_code'
                          GROUP BY  m_ac.`AC_TYPE`");

           $totalpostalvote = DB::select("SELECT SUM(ecoi.`service_postal_votes_under_section_8`+ecoi.`service_postal_votes_gov`) AS postaltotalreceived,  
           m_ac.`AC_TYPE` FROM  electors_cdac_other_information AS ecoi
           INNER JOIN m_ac ON m_ac.`AC_NO` = ecoi.`ac_no` AND m_ac.`ST_CODE` = ecoi.`st_code`
           WHERE m_ac.`ST_CODE` = '$st_code' GROUP BY m_ac.`AC_TYPE`");

            $totalpostalvoterejected = DB::select("SELECT SUM(rejected_votes) as postalrejected, m_ac.`AC_TYPE` FROM round_master AS rm
            INNER JOIN m_ac ON m_ac.`ST_CODE` = rm.`st_code` AND m_ac.`AC_NO` = rm.`ac_no`
            WHERE m_ac.`ST_CODE` = '$st_code' GROUP BY m_ac.`AC_TYPE`");



           $notavote  = DB::select("SELECT m_ac.`AC_TYPE`, SUM(total_vote) as totalEvmPostalvotenota FROM `".$bt."` AS cp1
                        INNER JOIN m_ac ON m_ac.`AC_NO` = cp1.`ac_no` AND m_ac.`ST_CODE` = '$st_code'
                        WHERE party_id = '1180' GROUP BY m_ac.`AC_TYPE`");

           foreach ($notavote as $key4 => $value4) {

                  $notavoteNew[$value4->AC_TYPE] = array(
                    'totalEvmPostalvotenota' => $value4->totalEvmPostalvotenota,
                  );
           }

           foreach ($totalpostalvoterejected as $key2 => $value2) {
            $totalpostalvoterejectedNew[$value2->AC_TYPE] = array(
             
             'postalrejected' =>      $value2->postalrejected
            );
          }


           foreach ($totalpostalvote as $key3 => $value3) {
             $totalpostalvoteNew[$value3->AC_TYPE] = array(
              'postaltotalreceived' => $value3->postaltotalreceived
            //'postalrejected' =>      $value3->postalrejected,
             );
           }



         foreach ($electorsvotersdata as $key => $value) {
                  $electorsvotersdataNew[$value->AC_TYPE] = array(
                    'AC_TYPE' => $value->AC_TYPE,
                    'maleElectors' => $value->maleElectors,
                    'femaleElectors' => $value->femaleElectors,
                    'thirdElectors' => $value->thirdElectors,
                    'totalElectors' => $value->totalElectors,
                    'totalgenac' => $value->totalgenac,
                    'totalscac' => $value->totalscac,
                    'totalstac' => $value->totalstac,

                    'overseasmaleElector' => $value->overseasmaleElector,
                    'overseasFemaleElector' => $value->overseasFemaleElector,
                    'overseasthirdElector' => $value->overseasthirdElector,

                  );
         }

         foreach ($totalvote as $key1 => $value1) {
                  $totalvoteNew[$value1->AC_TYPE] = array(
                    'AC_TYPE' => $value1->AC_TYPE,
                    'totalMaleVoters' => $value1->totalMaleVoters,
                    'totalFemaleVoters' => $value1->totalFemaleVoters,
                    'totalOtherVoters' => $value1->totalOtherVoters,
                    'totalpollingstation' => $value1->totalpollingstation,
                    'votes_not_retreived_from_evm' => $value1->votes_not_retreived_from_evm,
                    'rejected_votes_due_2_other_reason' => $value1->rejected_votes_due_2_other_reason,
                    'proxy_votes' => $value1->proxy_votes,
                    'test_votes_49_ma' => $value1->test_votes_49_ma,


                    'overseasmalevoters' => $value1->overseasmalevoters,
                    'overseasFemalevoters' => $value1->overseasFemalevoters,
                    'overseasthirdvoters' => $value1->overseasthirdvoters,

                  );
         }

                  if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                  }

                if($request->path() == "$prefix/electorsdatasummary/$st_code"){
                    return view('IndexCardReports.IndexCardReports.electorsdatasummary',
                      compact('user_data','totalvoteNew','electorsvotersdataNew','totalpostalvoteNew','notavoteNew','st_code','totalpostalvoterejectedNew'));
                }elseif($request->path() == "$prefix/electorsdatasummary-pdf/$st_code"){
                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.electorsdatasummary-pdf',compact('user_data','totalvoteNew','electorsvotersdataNew','totalpostalvoteNew','notavoteNew','st_code','totalpostalvoterejectedNew'));
				// code for report verify and download report no 6
				if(verifyreport(6, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'electorsdatasummary'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '6',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //code for verify report ends here
                return $pdf->download('6-Electors Data Summary.pdf');
                }elseif($request->path() == "$prefix/electorsdatasummary-excel/$st_code"){
				
					return Excel::download(new ElectorsDataSummary($totalvoteNew,$electorsvotersdataNew,$totalpostalvoteNew,$notavoteNew,$totalpostalvoterejectedNew), '6-Electors Data Summary.xlsx');

               }
    }

    // Report Constituency Data Summary

    public function constituencydatasummary(request $request, $st_code){

       $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);

        $session['election_detail'] = array();

        $user_data = $d;

        $st_code = $st_code;

        $sSelect = array(
           'B.ST_NAME','B.st_code','A.ac_no','A.AC_TYPE','A.AC_NAME'

       );
       $sTable = 'm_ac AS A';
       $sGroup = array(
           'A.st_code','A.ac_no'
       );
       $countSeats = DB::table($sTable)
                       ->select($sSelect)
                       ->join('m_state AS B','B.ST_CODE','A.st_code')
                       ->join('m_election_details AS med',[['med.CONST_NO','A.AC_NO'],['med.ST_CODE','A.ST_CODE']])
                       ->WHERE('med.CONST_TYPE', 'AC')
                       ->WHERE('med.CURRENTELECTION',  'Y')
                       //->WHERE('med.ELECTION_ID' ,1)
                       ->WHERE('B.st_code' , $st_code)
                       ->groupBy($sGroup)
                       ->get()->toArray();


    //echo '<pre>'; print_r($countSeats); die; 

        foreach ($countSeats as  $value) {

          $bt = 'counting_master_'.strtolower($st_code);

            $ac = $value->ac_no;
           
                  $fWhere = array(
                        'ec.st_code'   => $st_code,
                        'ec.ac_no'     => $ac,
                    );
            
            $electorData = DB::table('electors_cdac AS ec')
                          ->select(array(
                  'ovi.general_male_voters AS male_voter',
                                'ovi.general_female_voters AS female_voter',
                                'ovi.general_other_voters AS other_voter',
                                'ovi.nri_male_voters AS nri_male_voters',
                                'ovi.nri_female_voters AS nri_female_voters',
                                'ovi.nri_other_voters AS nri_other_voters',
                                'ovi.test_votes_49_ma AS test_votes_49_ma',
                                'ovi.votes_not_retreived_from_evm AS votes_not_retreived_from_evm',
                                'ovi.rejected_votes_due_2_other_reason AS rejected_votes_due_2_other_reason',
                                'ovi.service_postal_votes_under_section_8 AS service_postal_votes_under_section_8',

                                'ovi.service_postal_votes_gov AS service_postal_votes_gov',
                                'rm.rejected_votes AS postal_votes_rejected',
                                'ovi.proxy_votes AS proxy_votes',
                                'rm.tended_votes AS tendered_votes',

                                'ovi.total_polling_station_s_i_t_c AS total_polling_station_s_i_t_c',
                                'ovi.date_of_repoll AS date_of_repoll',
                                'ovi.no_poll_station_where_repoll AS no_poll_station_where_repoll',
                                'ovi.is_by_or_countermanded_election AS is_by_or_countermanded_election',
                                'ovi.reasons_for_by_or_countermanded_election AS reasons_for_by_or_countermanded_election',
                                'ovi.finalize_by_ceo AS finalize_by_ceo',
                                'ovi.finalize AS finalize_by_ro',
                                'ovi.finalize_by_eci AS finalize_by_eci',
                                'ovi.date_of_finalize_by_ro AS finalize_by_ro_date',
                                'ovi.date_of_finalize_by_ceo AS finalize_by_ceo_date',

                            DB::raw('SUM(ec.gen_electors_male) AS gen_m'),
                            DB::raw("SUM(ec.service_male_electors) AS ser_m"),
                            DB::raw("SUM(ec.nri_male_electors) AS nri_m"), 

                            DB::raw("SUM(ec.gen_electors_female) AS gen_f"),
                            DB::raw("SUM(ec.service_female_electors) AS ser_f"),
                            DB::raw("SUM(ec.nri_female_electors) AS nri_f"),

                            DB::raw("SUM(ec.gen_electors_other) AS gen_o"),
                            DB::raw("SUM(ec.nri_third_electors) AS nri_o"),
                            DB::raw("SUM(ec.service_third_electors) AS ser_o"),

                            DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other) AS gen_t"),
                            DB::raw("SUM(ec.service_male_electors+ec.service_female_electors+ec.service_third_electors) AS ser_t"),
                            DB::raw("SUM(ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS nri_t"),
                        ))
                         ->leftJoin('electors_cdac_other_information as ovi',function($query){
                           $query->on('ovi.st_code','ec.st_code')
                                   ->on('ovi.ac_no','ec.ac_no');                                  
                       })
             ->join('round_master as rm',function($query){
                           $query->on('rm.st_code','ec.st_code')
                                   ->on('rm.ac_no','ec.ac_no');                                  
                       })
                        ->where(array(
                            'ec.st_code' => $st_code,
                            'ec.ac_no'   => $ac,
                            'ec.year'    => getElectionYear()
                        ))
                        ->groupBy('ec.ac_no')                       
                        ->first();


                          DB::enableQueryLog();

         $indexCardDataACs = DB::table($bt.' AS A')
                           ->select(
                                DB::raw("SUM(A.postalballot_vote) AS totalpostal_votes"),
                                DB::raw("SUM(A.total_vote - A.postalballot_vote) AS totalevm_votes"),
                                DB::raw("SUM(A.total_vote) AS total_votes")
                                
                            )
                            ->where(array(
                                'A.ac_no'    => $ac                        
                            ))
                            ->groupBy('A.ac_no')
                            ->first();
              


        $indexCardDataACNota = DB::table($bt.' AS A')
                           ->select('A.postalballot_vote as postel_nota',
               DB::raw("(A.total_vote - A.postalballot_vote) AS nota_evm"),
               'A.total_vote as total_nota')
                           ->where(array(                        
                                'A.party_id'=>'1180',
                'A.ac_no'    => $ac  
                            ))
                            ->first();

  
  
  

    $indexCardDatas = App\models\Admin\CandidateModel::get_count_nominated($st_code,$ac);


    //echo '<pre>'; print_r($indexCardDatas); die;

            $indexCardDatasDf = DB::select("SELECT cp.ac_no,
            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where  cp1.party_id != 1180 and cp1.ac_no = cp.ac_no and cp.ac_no =cp1.ac_no and C.cand_gender = 'male' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fdmale,

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where  cp1.party_id != 1180 and cp1.ac_no = cp.ac_no and  C.cand_gender = 'female' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fdfemale, 

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where  cp1.party_id != 1180 and cp1.ac_no = cp.ac_no and  C.cand_gender = 'third' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fdthird,


            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where  cp1.party_id != 1180 and cp1.ac_no = cp.ac_no GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fd


            FROM `".$bt."` as cp
            join candidate_personal_detail as C on C.candidate_id = cp.candidate_id
            WHERE cp.candidate_id NOT IN (select candidate_id from winning_leading_candidate as w1 where w1.ac_no = cp.ac_no) 
            AND cp.party_id != 1180 and cp.ac_no = ".$ac);


      
      $candidateData = array();

            $candidateData = array_merge($candidateData,array(

                                'c_nom_m_t'=> $indexCardDatas['nom_male'],
                                'c_nom_f_t'=> $indexCardDatas['nom_female'],
                                'c_nom_o_t'=> $indexCardDatas['nom_third'],
                                'c_nom_all_t'=> $indexCardDatas['nom_total'],

                                'c_wd_m_t'=> $indexCardDatas['with_male'],
                                'c_wd_f_t'=> $indexCardDatas['with_female'],
                                'c_wd_o_t'=> $indexCardDatas['with_third'],

                                 'c_rej_m_t'=> $indexCardDatas['rej_male'],
                                'c_rej_f_t'=> $indexCardDatas['rej_female'],
                                'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                 'c_acp_m_t'=> $indexCardDatas['cont_male'],
                                'c_acp_f_t'=> $indexCardDatas['cont_female'],
                                'c_acp_o_t'=> $indexCardDatas['cont_third'],

                                ));


         $candidateData = array_merge($candidateData,array(

                                'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                                'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                                'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                                'c_fd_t'=> $indexCardDatasDf[0]->fd,
                                

                                ));

 
            $candidateData = (object) $candidateData;

            $pollDateInfoacwise = DB::table('m_schedule as ms')
                                ->select('ms.DATE_POLL','ms.DATE_COUNT','wlc.result_declared_date',
                                'cpdw.cand_name as lead_cand_name','wlc.lead_cand_party','wlc.lead_total_vote','wlc.trail_total_vote',
                                'wlc.trail_cand_party','cpdr.cand_name as trail_cand_name','wlc.margin','mres.DATE_COUNT as RE_DATE_COUNT')								
                                ->join('m_election_details as med','med.SCHEDULEID','ms.SCHEDULEID')
								->leftJoin('m_reschedule as mres',[['mres.SCHEDULEID','ms.SCHEDULEID'],['med.ST_CODE','mres.st_code'],['med.CONST_NO','mres.ac_no']])
                                ->join('winning_leading_candidate as wlc', function($join){
                                    $join->on('wlc.st_code', 'med.ST_CODE')
                                            ->on('wlc.ac_no', 'med.CONST_NO');
                                })
								->join('candidate_personal_detail as cpdw', function($join){
                                    $join->on('cpdw.candidate_id', 'wlc.candidate_id');
                                })
								->join('candidate_personal_detail as cpdr', function($join){
                                    $join->on('cpdr.candidate_id', 'wlc.trail_candidate_id');
                                })
                                ->where(array(
                                        'med.ST_CODE' => $st_code,
                                        'med.CONST_NO'    => $ac,
                                        'med.CONST_TYPE'    => "AC",
                                        'med.CURRENTELECTION'   => 'Y'
                                 ))
                                ->first();
 

          if(@$electorData->total_polling_station_s_i_t_c > 0){
            $avg_elec_polling_stn = round(($electorData->gen_m +  $electorData->gen_f + $electorData->gen_o + $electorData->nri_m + $electorData->nri_f + $electorData->nri_o + $electorData->ser_m + $electorData->ser_f + $electorData->ser_o)/$electorData->total_polling_station_s_i_t_c);
          }else{
            $avg_elec_polling_stn = 0;


          }

            $finalArray = array();

          $finalArray = array(
                                'st_code' => $st_code,
                                'ac_no' => $ac,
                                "c_nom_m_t"                     =>$candidateData->c_nom_m_t,
                                "c_nom_f_t"                     => $candidateData->c_nom_f_t,
                                "c_nom_o_t"                     =>$candidateData->c_nom_o_t,
                                "c_nom_a_t"                     =>$candidateData->c_nom_all_t,

                                "c_nom_w_m"                     =>$candidateData->c_wd_m_t,
                                "c_nom_w_f"                     =>$candidateData->c_wd_f_t,
                                "c_nom_w_o"                     =>$candidateData->c_wd_o_t, 
                                "c_nom_w_t"                     =>$candidateData->c_wd_m_t  + $candidateData->c_wd_f_t +$candidateData->c_wd_o_t,

                                "c_nom_r_m"                     =>$candidateData->c_rej_m_t,
                                "c_nom_r_f"                     =>$candidateData->c_rej_f_t,
                                "c_nom_r_o"                     =>$candidateData->c_rej_o_t,
                                "c_nom_r_a"                     =>$candidateData->c_rej_m_t + $candidateData->c_rej_f_t + $candidateData->c_rej_o_t,

                                "c_nom_co_m"                     =>$candidateData->c_acp_m_t,
                                "c_nom_co_f"                     =>$candidateData->c_acp_f_t,
                                "c_nom_co_o"                     =>$candidateData->c_acp_o_t,
                                'c_nom_co_t'                     =>$candidateData->c_acp_m_t + $candidateData->c_acp_f_t +$candidateData->c_acp_o_t,

                                "c_nom_fd_m"                     =>$candidateData->c_fd_m_t, 
                                "c_nom_fd_f"                     =>$candidateData->c_fd_f_t, 
                                "c_nom_fd_o"                     =>$candidateData->c_fd_o_t, 
                                "c_nom_fd_t"                     =>$candidateData->c_fd_t, 

                                //voters

                                "vt_gen_m"                     =>@$electorData->male_voter ? : 0,
                                "vt_gen_f"                     =>@$electorData->female_voter ? : 0,
                                "vt_gen_o"                     =>@$electorData->other_voter ? : 0,
                                "vt_gen_t"                     =>@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter,

                                "vt_nri_m"                     =>@$electorData->nri_male_voters ? : 0,
                                "vt_nri_f"                     =>@$electorData->nri_female_voters ? : 0,
                                "vt_nri_o"                     =>@$electorData->nri_other_voters ? : 0,
                                "vt_nri_t"                     =>@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters,

                                "vt_m_t"                     =>@$electorData->male_voter+@$electorData->nri_male_voters,
                                "vt_f_t"                     =>@$electorData->female_voter+@$electorData->nri_female_voters,
                                "vt_o_t"                     =>@$electorData->other_voter+@$electorData->nri_other_voters,
                                "vt_all_t"                     =>@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters,
                                'test_votes_49_ma' => @$electorData->test_votes_49_ma ? :0,
                                'votes_not_retreived_from_evm' =>@$electorData->votes_not_retreived_from_evm ? :0,
                                'rejected_votes_due_2_other_reason' => @$electorData->rejected_votes_due_2_other_reason ? :0,

                                'service_postal_votes_under_section_8' => @$electorData->service_postal_votes_under_section_8 ? :0,
                                'service_postal_votes_gov' => @$electorData->service_postal_votes_gov ? :0,

                                'proxy_votes' => @$electorData->proxy_votes ? : 0,

                                'total_polling_station_s_i_t_c' => @$electorData->total_polling_station_s_i_t_c ? : 0,
                                'date_of_repoll' => @$electorData->date_of_repoll,
                                'no_poll_station_where_repoll' => @$electorData->no_poll_station_where_repoll,

                                'gen_m' => @$electorData->gen_m,
                                'ser_m' => @$electorData->ser_m,
                                'nri_m' =>  @$electorData->nri_m,
                                
                                'gen_f' => @$electorData->gen_f,
                                'ser_f' => @$electorData->ser_f,
                                'nri_f' => @$electorData->nri_f,
                                
                                'gen_o' => @$electorData->gen_o,
                                'nri_o' => @$electorData->nri_o,
                                'ser_o' =>  @$electorData->ser_o,
                                
                                'gen_t' => @$electorData->gen_m +  @$electorData->gen_f + @$electorData->gen_o,
                                'ser_t' => @$electorData->ser_f + @$electorData->ser_m + @$electorData->ser_o,
                                'nri_t' => @$electorData->nri_m + @$electorData->nri_f + @$electorData->nri_o,
                                
                                'total_o' => @$electorData->nri_o + @$electorData->gen_o + @$electorData->ser_o, 
                                'total_m' =>  @$electorData->nri_m + @$electorData->gen_m + @$electorData->ser_m,
                                'total_f' => @$electorData->nri_f + @$electorData->gen_f + @$electorData->ser_f,
                                
                                
                                'total_all' => @$electorData->gen_m +  @$electorData->gen_f + @$electorData->gen_o + @$electorData->ser_f + @$electorData->ser_m + @$electorData->ser_o + @$electorData->nri_m + @$electorData->nri_f + @$electorData->nri_o,

                                'evm_votes'=> (@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters) - (@$electorData->test_votes_49_ma + @$electorData->votes_not_retreived_from_evm + @$electorData->rejected_votes_due_2_other_reason + @$indexCardDataACNota->nota_evm),

                                'postal_votes'=> @$electorData->service_postal_votes_under_section_8 + @$electorData->service_postal_votes_gov,

                                'total_votes'=> (@$electorData->service_postal_votes_under_section_8 + @$electorData->service_postal_votes_gov) + (@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters),
                                'rej_votes_postal'=> @$electorData->postal_votes_rejected ? :0,
                                'tended_votes'=>  @$electorData->tendered_votes ? : 0,

                                'nota_postal_vote'=>  @$indexCardDataACNota->postel_nota ? :0,
                                'nota_evm_vote'=> @$indexCardDataACNota->nota_evm,
                                'DATE_POLL'=> @$pollDateInfoacwise->DATE_POLL,
                                'DATE_COUNT'=> (@$pollDateInfoacwise->RE_DATE_COUNT) ? @$pollDateInfoacwise->RE_DATE_COUNT:@$pollDateInfoacwise->DATE_COUNT,
                                'result_declared_date'=> @$pollDateInfoacwise->result_declared_date,

                                'lead_cand_name'=> @$pollDateInfoacwise->lead_cand_name,
                                'lead_cand_party'=> @$pollDateInfoacwise->lead_cand_party,
                                'lead_total_vote'=> @$pollDateInfoacwise->lead_total_vote,
                                'trail_cand_name'=> @$pollDateInfoacwise->trail_cand_name,
                                'trail_cand_party'=> @$pollDateInfoacwise->trail_cand_party,
                                'trail_total_vote'=> @$pollDateInfoacwise->trail_total_vote,
                                'margin'=> @$pollDateInfoacwise->margin,
                                );

                              $finalArraynew[$st_code][$ac] = array(
                                'st_code' => $st_code,
                                'ST_NAME' => $value->ST_NAME,
                                'AC_NAME' => $value->AC_NAME,
                                'ac_type' => '('.strtoupper($value->AC_TYPE).')',
                                'ac_no' => $ac,
                                "c_nom_m_t"                     =>$candidateData->c_nom_m_t,
                                "c_nom_f_t"                     => $candidateData->c_nom_f_t,
                                "c_nom_o_t"                     =>$candidateData->c_nom_o_t,
                                "c_nom_a_t"                     =>$candidateData->c_nom_all_t,

                                "c_nom_w_m"                     =>$candidateData->c_wd_m_t,
                                "c_nom_w_f"                     =>$candidateData->c_wd_f_t,
                                "c_nom_w_o"                     =>$candidateData->c_wd_o_t, 
                                "c_nom_w_t"                     =>$candidateData->c_wd_m_t  + $candidateData->c_wd_f_t +$candidateData->c_wd_o_t,

                                "c_nom_r_m"                     =>$candidateData->c_rej_m_t,
                                "c_nom_r_f"                     =>$candidateData->c_rej_f_t,
                                "c_nom_r_o"                     =>$candidateData->c_rej_o_t,
                                "c_nom_r_a"                     =>$candidateData->c_rej_m_t + $candidateData->c_rej_f_t + $candidateData->c_rej_o_t,

                                "c_nom_co_m"                     =>$candidateData->c_acp_m_t,
                                "c_nom_co_f"                     =>$candidateData->c_acp_f_t,
                                "c_nom_co_o"                     =>$candidateData->c_acp_o_t,
                                'c_nom_co_t'                     =>$candidateData->c_acp_m_t + $candidateData->c_acp_f_t +$candidateData->c_acp_o_t,

                                "c_nom_fd_m"                     =>$candidateData->c_fd_m_t, 
                                "c_nom_fd_f"                     =>$candidateData->c_fd_f_t, 
                                "c_nom_fd_o"                     =>$candidateData->c_fd_o_t, 
                                "c_nom_fd_t"                     =>$candidateData->c_fd_t, 

                                //voters

                                "vt_gen_m"                     =>@$electorData->male_voter ? : 0,
                                "vt_gen_f"                     =>@$electorData->female_voter ? : 0,
                                "vt_gen_o"                     =>@$electorData->other_voter ? : 0,
                                "vt_gen_t"                     =>@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter,

                                "vt_nri_m"                     =>@$electorData->nri_male_voters ? : 0,
                                "vt_nri_f"                     =>@$electorData->nri_female_voters ? : 0,
                                "vt_nri_o"                     =>@$electorData->nri_other_voters ? : 0,
                                "vt_nri_t"                     =>@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters,

                                "vt_m_t"                     =>@$electorData->male_voter+@$electorData->nri_male_voters,
                                "vt_f_t"                     =>@$electorData->female_voter+@$electorData->nri_female_voters,
                                "vt_o_t"                     =>@$electorData->other_voter+@$electorData->nri_other_voters,
                                "vt_all_t"                     =>@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters,
                                'test_votes_49_ma' => @$electorData->test_votes_49_ma ? :0,
                                'votes_not_retreived_from_evm' =>@$electorData->votes_not_retreived_from_evm ? :0,
                                'rejected_votes_due_2_other_reason' => @$electorData->rejected_votes_due_2_other_reason ? :0,

                                'service_postal_votes_under_section_8' => @$electorData->service_postal_votes_under_section_8 ? :0,
                                'service_postal_votes_gov' => @$electorData->service_postal_votes_gov ? :0,

                                'proxy_votes' => @$electorData->proxy_votes ? : 0,

                                'total_polling_station_s_i_t_c' => @$electorData->total_polling_station_s_i_t_c ? : 0,
                                'date_of_repoll' => @$electorData->date_of_repoll,
                                'no_poll_station_where_repoll' => @$electorData->no_poll_station_where_repoll,

                                'gen_m' => @$electorData->gen_m,
                                'ser_m' => @$electorData->ser_m,
                                'nri_m' =>  @$electorData->nri_m,
                                
                                'gen_f' => @$electorData->gen_f,
                                'ser_f' => @$electorData->ser_f,
                                'nri_f' => @$electorData->nri_f,
                                
                                'gen_o' => @$electorData->gen_o,
                                'nri_o' => @$electorData->nri_o,
                                'ser_o' =>  @$electorData->ser_o,
                                
                                'gen_t' => @$electorData->gen_m +  @$electorData->gen_f + @$electorData->gen_o,
                                'ser_t' => @$electorData->ser_f + @$electorData->ser_m + @$electorData->ser_o,
                                'nri_t' => @$electorData->nri_m + @$electorData->nri_f + @$electorData->nri_o,
                                
                                'total_o' => @$electorData->nri_o + @$electorData->gen_o + @$electorData->ser_o, 
                                'total_m' =>  @$electorData->nri_m + @$electorData->gen_m + @$electorData->ser_m,
                                'total_f' => @$electorData->nri_f + @$electorData->gen_f + @$electorData->ser_f,
                                
                                
                                'total_all' => @$electorData->gen_m +  @$electorData->gen_f + @$electorData->gen_o + @$electorData->ser_f + @$electorData->ser_m + @$electorData->ser_o + @$electorData->nri_m + @$electorData->nri_f + @$electorData->nri_o,

                                'evm_votes'=> (@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters) - (@$electorData->test_votes_49_ma + @$electorData->votes_not_retreived_from_evm + @$electorData->rejected_votes_due_2_other_reason + @$indexCardDataACNota->nota_evm),

                                'postal_votes'=> @$electorData->service_postal_votes_under_section_8 + @$electorData->service_postal_votes_gov,

                                'total_votes'=> ((@$electorData->service_postal_votes_under_section_8 + @$electorData->service_postal_votes_gov) + (@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters)),

                                'rej_votes_postal'=> @$electorData->postal_votes_rejected ? :0,
                                'tended_votes'=>  @$electorData->tendered_votes ? : 0,

                                'nota_postal_vote'=>  @$indexCardDataACNota->postel_nota ? :0,
                                'nota_evm_vote'=> @$indexCardDataACNota->nota_evm,
                                'DATE_POLL'=> @$pollDateInfoacwise->DATE_POLL,
                                'DATE_COUNT'=> (@$pollDateInfoacwise->RE_DATE_COUNT) ? @$pollDateInfoacwise->RE_DATE_COUNT:@$pollDateInfoacwise->DATE_COUNT,
                                'result_declared_date'=> @$pollDateInfoacwise->result_declared_date,

                                'lead_cand_name'=> @$pollDateInfoacwise->lead_cand_name,
                                'lead_cand_party'=> @$pollDateInfoacwise->lead_cand_party,
                                'lead_total_vote'=> @$pollDateInfoacwise->lead_total_vote,
                                'trail_cand_name'=> @$pollDateInfoacwise->trail_cand_name,
                                'trail_cand_party'=> @$pollDateInfoacwise->trail_cand_party,
                                'trail_total_vote'=> @$pollDateInfoacwise->trail_total_vote,
                                'margin'=> @$pollDateInfoacwise->margin,

                                );




        }

                if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
                }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
                }else if($user->role_id == '7'){
                  $prefix     = 'eci';
                }


                if($request->path() == "$prefix/constituency-data-summary/$st_code"){
                return view('IndexCardReports.IndexCardReports.constituency-data-summary-report',compact('finalArraynew','user_data','st_code'));
                }elseif($request->path() == "$prefix/constituency-data-summary-pdf/$st_code"){

                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.constituencyDataSummaryReportPDF',[
                    'session'=>$session,
                    'finalArraynew'=>$finalArraynew,
                    'st_code'=>$st_code
                ]);
				
				// code for report verify and download report no 8
				if(verifyreport(8, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'constituency-data-summary'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '8',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //code for verify report ends here
                return $pdf->download('8-Constituency Data Summery Report.pdf');


              }elseif($request->path() == "$prefix/constituency-data-summary-excel/$st_code") {
                    $finalArraynew   = json_decode( json_encode($finalArraynew), true);
                    $date = date('Y-m-d');
					
					
					return Excel::download(new ConstituencyDataSummary($finalArraynew), '8-Constituency Data Summery Report.xlsx');
					
            
                }   //Constituency data summary excel ends here

        //echo "<pre>"; print_r($finalArraynew); die;


    } //function constituency data summary ends here

    public function annxure(request $request, $st_code){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);

        $session['election_detail'] = array();

        $user_data = $d;

        $st_code = $st_code;

        $actypecount =DB::select("SELECT m_ac.`AC_TYPE`,SUM(CASE WHEN AC_TYPE = 'GEN' THEN 1 ELSE 0 END) AS genac, 
                      SUM(CASE WHEN AC_TYPE = 'SC' THEN 1 ELSE 0 END) AS scac, 
                      SUM(CASE WHEN AC_TYPE = 'ST' THEN 1 ELSE 0 END) AS stac FROM m_ac 
                      INNER JOIN m_election_details AS med ON med.st_code = m_ac.ST_CODE AND med.CONST_NO = m_ac.AC_NO 
                      WHERE med.CONST_TYPE = 'AC' AND med.CURRENTELECTION = 'Y' AND m_ac.ST_CODE = '$st_code' GROUP BY m_ac.`AC_TYPE`");

        

        foreach ($actypecount as $key => $value) {
                $actypecountNew[$value->AC_TYPE] = array(
                 'genac' => $value->genac,
                 'scac' => $value->scac,
                 'stac' => $value->stac,
                );
        }

        $postalvote = DB::select("SELECT SUM(service_postal_votes_under_section_8) AS postalvotesec8,  m_ac.`AC_TYPE`, SUM(service_postal_votes_gov) AS postalvoteservice
                              FROM electors_cdac_other_information AS ecoi
                              INNER JOIN m_ac ON m_ac.`AC_NO` = ecoi.ac_no AND m_ac.`ST_CODE` = ecoi.`st_code`
                              WHERE m_ac.`ST_CODE` = '$st_code' GROUP BY m_ac.`AC_TYPE`");

        

        foreach ($postalvote as $key1 => $value1) {

          $postalvoteNew[$value1->AC_TYPE] = array(
                 'postalvotesec8' => $value1->postalvotesec8,
                 'postalvoteservice' => $value1->postalvoteservice
                 
                );
          
        }

        //echo "<pre>"; print_r($postalvoteNew); die;


          if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                  }

                if($request->path() == "$prefix/annxure/$st_code"){
                    return view('IndexCardReports.IndexCardReports.annxure',
                      compact('user_data','postalvoteNew','actypecountNew','st_code'));
                }elseif($request->path() == "$prefix/annxure-pdf/$st_code"){

                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.annxure-pdf',[
                    'session'=>$session,
                    'postalvoteNew'=>$postalvoteNew,
                    'actypecountNew'=>$actypecountNew,
                    'st_code'=>$st_code
                ]);
				
				 // code for verified pdf check and upload report no 11
			
			if(verifyreport(11, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'annxure'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '11',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //Code end for verify and download
                return $pdf->download('6(a)-Electors Data Summary Annxure.pdf');


              }elseif($request->path() == "$prefix/annxure-excel/$st_code") {
					return Excel::download(new Annxure($postalvoteNew,$actypecountNew), '6(a)-Electors Data Summary Annxure.xlsx');
		}
	}
}