<?php 
namespace App\Http\Controllers\Admin\ResultSheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Validator;
use Config;
use \PDF,Excel;
use App\commonModel;
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use App\adminmodel\ECIModel;

class ResultSheetReportController extends Controller {
  
    public $view_path     = "admin.countingReport.scheduleReport";
    public $aro           = "aro";
    public $ro            = "ro";
    public $eci           = "eci";
    public $ceo           = "ceo";
    public $election_id; 
    
    public function __construct(){
		$this->commonModel  = new commonModel();
        $this->report_model = new ReportModel();
		$this->ECIModel = new ECIModel();
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
            }
           
            $this->election_id  = Auth::user()->election_id;

            return $next($request);
        });

         
    }
  
public function index(){ 
  
	$data=array();
	 $d = Auth::user();	
	 $data['user_data']  =  $d;
	 
	$user_type=null;
	$ST_CODE = '';
	$semi_chart_data = '[]';
	$semi_chart_labels = '[]';
	$semi_chart_bgcolor = '[]';
	$sum=''; $sumData=''; $result=array();
	$state_name='';
	$total_win=0;
	$total_lead=0;
	$grand_total_win_lead=0;
	$Constituencies_count = 0;
	$Constituencies_out_of_count = 0;
	
	$Constituencies_count_chart = array();
	$voteshare = array();
	$votesharedata = '[]';
	$votesharecolor = '[]';
	$resultPartywisedata='';
	$list_desc = array();
	$elec_name = '';
	$LeadWinCount = array();
	$pwdata = array();
	$total_win_lead = 0;

	$list_details = $this->ECIModel->getstatebyelectionid($d->election_id);
	
	if($list_details){
		$ST_CODE = $list_details->ST_CODE;
		$list_desc = DB::table('m_election_history')->select('description')->where('election_id','=',$d->election_id)->where('election_type_id','=',$list_details->ELECTION_TYPEID)->first(); 
		if($list_desc){
			$elec_name = strtoupper(str_replace('-',' ',$list_desc->description));
		}		  	  
	}
	
	if(isset($ST_CODE) && $ST_CODE <>''){
		$query = "SELECT * from  winning_leading_candidate where st_code='$ST_CODE' order by leading_id asc"; 
		$result = DB::select($query);
		
		$state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $ST_CODE)->first()->ST_NAME;

		$LeadWinCount = DB::table('winning_leading_candidate')
		 ->select('lead_cand_party','lead_cand_hparty', DB::raw('sum(CASE WHEN STATUS = "1" THEN "1" ELSE 0 END) as win'),DB::raw('sum(CASE WHEN STATUS = "0" THEN "1" ELSE 0 END) as lead'))
		 ->where('lead_cand_party', '!='," ")
		 ->where('ST_CODE',$ST_CODE)
		 ->groupBy('lead_cand_party','lead_cand_hparty')
		 ->get()->toArray();
 
		$pwdata = json_decode( json_encode($LeadWinCount), true);
		
		foreach ($pwdata as $k)
		{
			$total_win=$total_win+$k['win'];
			$total_lead=$total_lead+$k['lead'];
			$grand_total_win_lead=$total_win+$total_lead;
			$total_win_lead=$k['win']+$k['lead'];
		  $resultPartywisedata.="<tr style='font-size:12px;'>
			<td align='left' style='font-weight:bold;'>".$k['lead_cand_party']."</td><td align='center' style='font-weight:bold;'>".$k['win']."</td><td align='center' style='font-weight:bold;'>".$k['lead']."</td>
			<td align='center' style='font-weight:bold;'>".$total_win_lead."</td>
			</tr>";
		}
		$resultPartywisedata.="
			<tr style='font-size:12px;font-weight:bold;color:#FFF;text-align:center;Background-color:#FFC0CD'><td style='color:#000'  align='left'>Total</td><td style='color:#000' align='center'>".$total_win."</td><td style='color:#000' align='center'>".$total_lead."</td><td style='color:#000' align='center'>".$grand_total_win_lead."</td></tr>"; 
		 
		$Constituencies_count = DB::table('m_election_details')
				 ->where('ST_CODE',$ST_CODE)
				 ->where('CONST_TYPE','AC')
				 ->where('CURRENTELECTION','Y')
				 ->get()
				->count();
		$Constituencies_out_of_count = DB::table('winning_leading_candidate')
				 ->where('ST_CODE',$ST_CODE)
				 ->where('lead_cand_party', '!='," ")
				 ->count();
		
		
		$Constituencies_count_chart=$this->ResultStatusChart($ST_CODE);		 
		$semi_chart_data = $Constituencies_count_chart['data'][0];
		$semi_chart_labels =$Constituencies_count_chart['labels'][0];
		$semi_chart_bgcolor =$Constituencies_count_chart['bgcolor'][0];
		
		//Vote share chart
		$voteshare = $this->VoteShare($ST_CODE);
		$votesharedata = $voteshare['data'][0];
		$votesharecolor = $voteshare['color'][0];
	}
	

	 return view('admin.ResultSheet.result-sheet-report', $data, compact('data','state_name','user_type','Constituencies_out_of_count', 'Constituencies_count','result','resultPartywisedata','semi_chart_data','semi_chart_labels','semi_chart_bgcolor','votesharedata','votesharecolor','elec_name'));
  }
  
   public function ResultStatusChart($ST_CODE)
    {
      // echo $ST_CODE; die('dd');
        $total_win=0;
        $total_lead=0;
        $grand_total_win_lead=0;
        $ST_CODE=$ST_CODE;
		$partywiseData = DB::table('winning_leading_candidate')
                 ->select('lead_party_abbre','lead_hpartyabbre', DB::raw('sum(CASE WHEN STATUS = "1" THEN "1" ELSE 0 END) as win'),DB::raw('sum(CASE WHEN STATUS = "0" THEN "1" ELSE 0 END) as lead'))
                 ->where('lead_cand_party', '!='," ")
                 ->where('ST_CODE',$ST_CODE)
                 ->groupBy('lead_party_abbre','lead_hpartyabbre')
                 ->get()->toArray();
        $pwdata = json_decode( json_encode($partywiseData), true);
        $result_labels='';		
        $result_data='';
        $color_code ='';
        $backgroundColor ='';

        foreach ($pwdata as $k)
        {
            $total_win=$total_win+$k['win'];
            $total_lead=$total_lead+$k['lead'];
            $grand_total_win_lead=$total_win+$total_lead;
            $total_win_lead=$k['win']+$k['lead'];
			$leadAbbre_win_count= $k['lead_party_abbre'];
			$party_win_count= $total_win_lead;
            $result_labels.="'$leadAbbre_win_count',";
			$result_data.=$party_win_count.",";
		    $val =$k['lead_party_abbre'];	
		if($val=="BJP")
        {
         $color_code="#FF6600";
         //3D19FB
        }
        else if($val=="INC")
        {
         $color_code="#AA0078";
         //3D19FB
        }
        else if($val=="Other")
        {
         $color_code="#C0C0C0";
         //3D19FB
        }
        else if($val=="AAAP")
        {
         $color_code="#0FB40C";
         //3D19FB
        }
        else if($val=="NCP")
        {
         $color_code="#C3C300";
         //3D19FB
        }
        else if($val=="CPM")
        {
         $color_code="#801F32";
         //3D19FB
        }
        else if($val=="CPI")
        {
         $color_code="#FF3366";
         //3D19FB
        }
        else if($val=="BSP")
        {
         $color_code="#000078";
         //3D19FB
        }
        else if($val=="AITC")
        {
         $color_code="#33FF33";
         //3D19FB
        }
        else if($val=="NOTA")
        {
         $color_code="#9CFF00";
         //3D19FB
        }
        else if($val=="SP")
        {
         $color_code="#663300";
         //3D19FB
        }
        else if($val=="IND")
        {
         $color_code="#117700";
         //3D19FB
        }
        else
        {
            $color_code="#".$this->password_generate(6);
        }
			$backgroundColor.="'".$color_code."',";
			
        }
		$backgroundColor  = trim($backgroundColor,',');
		$result_data  = trim($result_data,',');
		$result_labels  = trim($result_labels,',');
		
		$data_semi_chart_array['data']=array("data: [".$result_data."]");
		$data_semi_chart_array['labels']=array("labels: [".$result_labels."]");
		$data_semi_chart_array['bgcolor']=array("backgroundColor: [".$backgroundColor."]");
		return $data_semi_chart_array;
    }
	
	public function VoteShare($ST_CODE) {
		$tbl = "counting_master_".strtolower($ST_CODE);
			$PartiesVoteShare = DB::select(
					  "SELECT   IFNULL($tbl.party_abbre, 'Total') AS party_abbre,$tbl.party_habbre,
					 sum($tbl.total_vote) as total,
			concat(round(( SUM($tbl.total_vote)/t.Total * 100 ),2),'%') AS percentage,m_party.CCODE,$tbl.party_id,m_party.PARTYABBRE,m_party.PARTYTYPE
			FROM      $tbl INNER join m_party on $tbl.party_id=m_party.CCODE, (
				  SELECT SUM($tbl.total_vote) AS Total
				  FROM  $tbl
				) t
		where m_party.PARTYTYPE  in('S','N','Z1')
		GROUP BY $tbl.party_abbre with ROLLUP"
		);
        $Partyarray = json_decode( json_encode($PartiesVoteShare), true);
        //Other Excluding (N,S,Z1 )
		 $tbl = "counting_master_".strtolower($ST_CODE);
		  $OtherPartiesVoteShare = DB::select(
				  "SELECT   IFNULL($tbl.party_abbre, 'Total') AS party_abbre,$tbl.party_habbre,
				  sum($tbl.total_vote) as total,
	   concat(round(( SUM($tbl.total_vote)/t.Total * 100 ),2),'%') AS percentage,m_party.CCODE,$tbl.party_id,m_party.PARTYABBRE,m_party.PARTYTYPE
	   FROM      $tbl INNER join m_party on $tbl.party_id=m_party.CCODE, (
			   SELECT SUM($tbl.total_vote) AS Total
			   FROM   $tbl
			 ) t
	 where  m_party.PARTYTYPE Not in('S','N','Z1')
	GROUP BY $tbl.party_abbre with ROLLUP"
				 );
        $OtherPartyarray = json_decode( json_encode($OtherPartiesVoteShare), true);
      $graph='';
      $party_name=array();
      $SlicesColor=array();
      $slicesIndex='';
      $index=0;
      $slices="";
   foreach($Partyarray as $dataValue)
   {
       if($dataValue['party_abbre']!="Total")
       {
           $party_name[]=$dataValue['party_abbre'];
            $partyname=$dataValue['party_abbre'];
            $SlicesColor[$partyname]=$index;
           // $slicesIndex.="".$index.":{color:'".$color_code."'},";
           $graph.="['".$dataValue['party_abbre']."{".$dataValue['percentage']."}',".$dataValue['total']."],";
           $index++;
       }
   }
   foreach($OtherPartyarray as $dataValueOther)
   {
       if($dataValueOther['party_abbre']=="Total")
       {
           $party_name[]="Other";
           $graph.="['Other{".$dataValueOther['percentage']."}',".$dataValueOther['total']."],";
       }
   }
    /*
      BJP    ="#FF6600";
      INC    ="#AA0078";
      Other  ="#C0C0C0";
      AAAP   ="#663300";
      NCP    ="#C3C300";
      CPM    ="#801F32";
      CPI    ="#FF3366";
      BSP    ="#000078";
      NOTA   ="#9CFF00";
      AITC   ="#33FF33";
      SP     ="#663300";
      IND    ="#000000";
  */
   $color_code='';
   $backgroundColor='';
   foreach($party_name as $key=>$val)
   {
       if($val=="BJP")
       {
         $color_code="#FF6600";
         //3D19FB
       }
       else if($val=="INC")
       {
         $color_code="#AA0078";
         //3D19FB
       }
       else if($val=="Other")
       {
         $color_code="#C0C0C0";
         //3D19FB
       }
       else if($val=="AAAP")
       {
         $color_code="#0FB40C";
         //3D19FB
       }
       else if($val=="NCP")
       {
         $color_code="#C3C300";
         //3D19FB
       }
       else if($val=="CPM")
       {
         $color_code="#801F32";
         //3D19FB
       }
       else if($val=="CPI")
       {
         $color_code="#FF3366";
         //3D19FB
       }
      else if($val=="BSP")
       {
         $color_code="#000078";
         //3D19FB
        }
        else if($val=="AITC")
         {
         $color_code="#33FF33";
         //3D19FB
       }
        else if($val=="NOTA")
       {
         $color_code="#9CFF00";
         //3D19FB
       }
        else if($val=="SP")
       {
         $color_code="#663300";
         //3D19FB
       }
        else if($val=="IND")
       {
         $color_code="#000000";
         //3D19FB
       }
        else if($val=="AAAP")
       {
         $color_code="#8C4D0B";
         //3D19FB
       }
       else
       {
            $color_code="#".$this->password_generate(6);
       }
        $slicesIndex.="".$key.":{color:'".$color_code."'},";
   }
	   $data1= "data.addRows([".$graph."]);";
	   $slices="slices:{ $slicesIndex }";
	   $data_color_array['data']=array($data1);
	   $data_color_array['color']=array($slices);
	   return $data_color_array;
    }

	function password_generate($chars)
	{
	  $data = '1234567890ABCDEF';
	  return substr(str_shuffle($data), 0, $chars);
	}
	public function validator(){
		$data=array();
		$user_data  =   Auth::user();	
		return view('admin.nfd.nfd-page', [
		'heading_title'=>'', 'user_data'=>$user_data,'statename'=>'','distname'=>'']);
	}

}  