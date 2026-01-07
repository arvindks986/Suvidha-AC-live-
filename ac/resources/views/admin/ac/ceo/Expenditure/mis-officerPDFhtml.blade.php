@php 
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $acdetails=getacbyacno($st_code, $cons_no); 
  $acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
  //echo $st_code.'cons_no=>'.$cons_no;
   $countingDate=\app(App\models\Expenditure\ExpenditureModel::class)->getResultDeclarationDate();
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Active Users</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="left" cellpadding="5">
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
                           <td><strong>Officer MIS Report</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name: AC General</strong> </td>
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
            <thead>
          <tr>
          <th>Serial No</th>
		  <th>District</th>
		  <th>AC Name</th> 
          <th>Total Candidates</th> 
		  <th>Started</th> 
          <th>Not Started</th> 
		  <th>Not In Time</th> 
		  <th>Finalised By DEO</th> 
          <th>Pending - DEO</th> 
		  <!--<th>Notice At DEO</th> -->
          <th>Pending - CEO</th> 
		  <th>Notice At CEO</th>
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
		$TotalfiledData = 0;
		$TotalFinalByDEO = 0;
		$TotalNotinTime= 0;

        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php

         $TotalUsers +=$listdata->totalcandidate;
		  $cons_no=$listdata->ac_no;
         $distdetails=getdistrictbydistrictno($st_code,$listdata->district_no);
         $stdetails=getstatebystatecode($listdata->st_code);
         $acbystate=getacbystate($listdata->st_code);
         $account=count($acbystate);
         $Totalac += $account;
		 $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);
       
		 $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('AC',$listdata->st_code,$cons_no);
         $TotalFinalByDEO += $finalbyDEO;
		 
         //$pendingatRO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalpartiallypending('AC',$listdata->st_code,$cons_no);
         //$TotalPendingatRO += $pendingatRO;
		 
         $pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('AC',$listdata->st_code,$cons_no);
         $TotalPendingatCEO += $pendingatCEO;
		 
		
		 
         $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('AC',$listdata->st_code,$cons_no);
         $TotalPendingatECI += $pendingatECI;
		 
          $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
         $TotalfiledData +=  $filedcount;
		  
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('AC',$listdata->st_code,$cons_no);
         $Totalfinalcompletedcount += $finalcompletedcount;
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('AC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('AC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
		  $notinTime=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalNotinTime('AC',$listdata->st_code,$cons_no);
		 $TotalNotinTime += $notinTime;
		 
		 //pending at DEO
           if($finalbyDEO >= 0 ){
			$pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
			}  				

         @endphp
          <tr>    
          <td>{{ $count }}</td>
		  	<td align="left">@if(!empty($distdetails->DIST_NAME))   {{ $distdetails->DIST_NAME }}  @else <b> N/A </b> @endif</td>
			<td align="left">@if(!empty($acdetails->AC_NAME))   {{ $acdetails->AC_NAME }}  @else <b> N/A </b> @endif</td>
            <td align="right"> @if($listdata->totalcandidate =='' )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
			<td align="right"> @if( $filedcount =='' )     0  @else <b>{{  $filedcount }}</b> @endif</td>
			<td align="right"> @if($notfiledcount =='' )     0  @else  <b>{{ $notfiledcount }}</b> @endif</td>
			<td align="right">@if($notinTime =='')     0  @else <b>{{  $notinTime }}</b> @endif</td>
			<td align="right"> @if( $finalbyDEO =='' )     0  @else <b>{{  $finalbyDEO }}</b> @endif</td>
            <td align="right"> @if( $pendingatRO =='' )     0  @else <b>{{  $pendingatRO }}</b> @endif</td>
            <td align="right"> @if( $pendingatCEO =='' )     0  @else <b>{{  $pendingatCEO }}</b> @endif</td>
		    <td align="right">@if($noticeatCEOCount =='')     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td>
          <td align="right"><b> </b></td>
		    <td align="right"><b> </b></td>
          <td align="right"><b>{{$TotalUsers}}</b>
          </td>
	      <td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>{{$TotalNotinTime}}</b></td><td align="right"><b>
		  {{$TotalFinalByDEO}}</b></td><td align="right"><b>{{$TotalPendingatRO}}</b></td><td align="right"><b>{{$TotalPendingatCEO}}</b></td><td align="right"><b>{{$TotalDEONotice}}</b></td></tr>
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