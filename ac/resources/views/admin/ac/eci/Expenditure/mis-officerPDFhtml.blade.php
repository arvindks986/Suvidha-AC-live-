@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
  $countingDate=\app(App\models\Expenditure\ExpenditureModel::class)->getResultDeclarationDate();
  $DB_MONTH=Session::get('DB_MONTH');
  $DB_MONTH=!empty($DB_MONTH) ? $DB_MONTH : '';
  $DB_YEAR=Session::get('DB_YEAR');
  $DB_CONS_TYPE=Session::get('DB_CONS_TYPE');
  $DB_ELE_TYPE=Session::get('DB_ELE_TYPE');
  $monthName = date("F", mktime(0, 0, 0, $DB_MONTH, 10));
  
  //echo $st_code.'cons_no'.$cons_no; die;
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Active Users</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="">
                    <img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>
                   </th>
                    <th  style="width:50%" align="right" style="font-weight:normal;">
                        SECRETARIAT OF THE<br>
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: left;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%;" border="0" align="left">  
                <tr>
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>MIS Of Account Cases Of General Election To Legislative Assembly</strong></td>
                         </tr>
                          <tr>  
                           <!--<td><strong>Name: {{ $DB_CONS_TYPE.' '.$DB_ELE_TYPE.' '.'ELECTION-'.' '.$monthName.' '.$DB_YEAR }}</strong> </td>-->
						   <td><strong>Name: {{ 'General Election To Legislative Assembly-'.' '.$monthName.' '.$DB_YEAR }}</strong> </td>
                         </tr>
                         <!--<tr>  
                           <td><strong>Phase:</strong>   aa</td>
                         </tr>
                          <tr>  
                           <td><strong>Assembly:</strong> SNAME</td>
                         </tr>  --> 
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
						   <tr>  
                           <td align="right"><strong>Counting Date:</strong> {{ !empty($countingDate['start_result_declared_date']) ? date('d-M-Y',strtotime($countingDate['start_result_declared_date'])) :'NA'}}</td>
                         </tr>
                       <!-- <tr>  
                           <td align="right"><strong>Phase Starts:</strong> </td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Phase Ends:</strong>  </td>
                         </tr>  -->
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
             <thead class="text-center">
		  <tr>
          <th>I</th>
          <th>II</th>
          <th>III</th>
          <th>IV</th> 
		   <th>V</th>
          <!--<th>VI</th>
		  <th>VII</th>
          <th>VIII</th> 
          <th>IX</th>--> 
          <th colspan="">VI</th> 
		  <th colspan="">VII</th> 
		  <th colspan="">VIII</th> 
		  <th colspan="">IX</th> 
         </tr>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
		  @if(empty($cons_no)) 
          <th>Total AC</th> 
	      @else
		  <th>AC Name</th> 
		  @endif
          <th>Total Candidates</th> 
		  <!--<th>Started</th> 
          <th>Not Started</th> -->
		  <th>Finalised By DEO</th> 
          <!--<th>Pending - For Finalisation By DEO <BR /> IV-V</th> 
		   <th>Notice - DEO</th>
           <th>Pending - CEO <BR /> V-(X+XI+XII) </th> 
		  <th>Notice - CEO</th>-->
          <th>A/C Cases Under Scrutiny </th> 
          <th>Cases Dropped / Closed After Scrutiny</th> 
		  <th>Notice Issued After Scrutiny</th>
		  <th>Disqualified</th> 
         </tr>
        </thead>
            <tbody>
     @php  
     $count = 1; 
        $TotalUsers = 0;
        $TotalPendingatRO = 0;
        $TotalPendingatCEO = 0;
        $TotalPendingatECI= 0;
		$TotalfiledData = 0;
        $TotalnotfiledData = 0;
        $Totalfinalcompletedcount= 0;
        $Totalac = 0;
		$TotalDEONotice = 0;
		$TotalCEONotice = 0;
		$TotalFinalByDEO = 0;
        $pendingatRO=0;
		$pendingatCEO=0;
		$Totaldisqualifiedcount=0;

        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php
        
         $TotalUsers +=$listdata->totalcandidate;
         $stdetails=getstatebystatecode($listdata->st_code);
		 $acbystate=getacbystate($listdata->st_code);
		 $election_id=Session::get('DB_ELECTION_ID');
		 $currelectionbyeid=\app(App\models\Expenditure\EciExpenditureModel::class)->expcurrentelectiondetails('AC',$listdata->st_code,$election_id,'');
         $account=count($currelectionbyeid);
		 $Totalac += $account;
		 $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);
		 $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('AC',$listdata->st_code,$cons_no);
         $TotalFinalByDEO += $finalbyDEO;
		 
         //$pendingatROOLD=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalpartiallypending('AC',$listdata->st_code,$cons_no);
         //$pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('AC',$listdata->st_code,$cons_no);
         //$TotalPendingatCEO += $pendingatCEO;
       
         $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('AC',$listdata->st_code,$cons_no);
         $TotalPendingatECI += $pendingatECI;
		 
		 $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
         
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('AC',$listdata->st_code,$cons_no);
         $Totalfinalcompletedcount += $finalcompletedcount;
		 
		 $disqualifiedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalDisqualifiedbyEci('AC',$listdata->st_code,$cons_no);
         $Totaldisqualifiedcount += $disqualifiedcount;
		 
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('AC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('AC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
		 //pending at DEO
		  if($finalbyDEO >= 0 ){
			$pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
			} 
			
		 //pending at CEO	
		 if($finalbyDEO >=  0 && $pendingatECI >=0 && $finalcompletedcount >=0 && $noticeatCEOCount >=0){
		 $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount + $disqualifiedcount+$noticeatCEOCount);
		 if($pendingatCEO >= 0) { $TotalPendingatCEO += $pendingatCEO; }
		}
		

         @endphp
          <tr>
          <td>{{ $count }}</td>
            <td>{{ $stdetails->ST_NAME }}</td>
			<td align="right">@if(empty($cons_no))   {{  $account }}  @else <b>{{$acdetails->AC_NAME}}</b> @endif</td>
            <td align="right"> @if(empty($listdata->totalcandidate) || $listdata->totalcandidate < 1 )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
             <td align="right"> @if( empty($finalbyDEO) || $finalbyDEO < 1)     0  @else <b>{{  $finalbyDEO }}</b> @endif</td>
            <!--<td align="right"> @if( empty($pendingatRO) || $pendingatRO < 1)     0  @else <b>{{  $pendingatRO }}</b> @endif</td>
			 <td align="right"> @if( empty($noticeatDEOCount) || $noticeatDEOCount < 1)     0  @else <b>{{  $noticeatDEOCount }}</b> @endif</td>		
            <td align="right"> @if( empty($pendingatCEO) || $pendingatCEO < 1)     0  @else <b>{{  $pendingatCEO }}</b> @endif</td>
			  <td align="right"> @if( empty($noticeatCEOCount) || $noticeatCEOCount < 1)     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</td>-->	
            <td align="right"> @if( empty($pendingatECI) || $pendingatECI < 1)     0  @else <b>{{  $pendingatECI }}</b> @endif</td>  
            <td align="right"> @if( empty($finalcompletedcount) || $finalcompletedcount < 1)     0  @else <b>{{  $finalcompletedcount }}</b> @endif</td>
			<td align="right"> @if( empty($noticeatCEOCount) || $noticeatCEOCount < 1)     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</td>
           <td align="right"> @if( empty($disqualifiedcount) || $disqualifiedcount < 1)     0  @else <b>{{  $disqualifiedcount }}</b> @endif</td>		 			
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
         <tr><td><b>Total</b></td><td></td>
	 <td align="right"><b>@if(empty($cons_no)) {{$Totalac}} @endif </b></td>
	 <td align="right"><b>{{$TotalUsers}}</b></td>
	 <td align="right"><b>{{$TotalFinalByDEO}}</b></td>
	 <!--<td align="right"><b>{{$TotalPendingatRO}}</b></td>
	 <td align="right"><b>{{$TotalDEONotice}}</b></td>
	 <td align="right"><b>{{$TotalPendingatCEO}}</b></td>
	 <td align="right"><b>{{$TotalCEONotice}}</b></td>-->
	 <td align="right"><b>{{$TotalPendingatECI}}</b></td>
	 <td align="right"><b>{{$Totalfinalcompletedcount}}</b></td>
	 <td align="right"><b>{{$TotalCEONotice}}</b></td>
	 <td align="right"><b>{{$Totaldisqualifiedcount}}</b></td>
	 </tr>
            </tbody>
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>