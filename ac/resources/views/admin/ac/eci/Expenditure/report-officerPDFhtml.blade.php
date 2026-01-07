@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
 //echo $st_code.'cons_no'.$cons_no; die;
  $countingDate=\app(App\models\Expenditure\ExpenditureModel::class)->getResultDeclarationDate();
  $DB_MONTH=Session::get('DB_MONTH');
  $DB_MONTH=!empty($DB_MONTH) ? $DB_MONTH : '';
  $DB_YEAR=Session::get('DB_YEAR');
  $DB_CONS_TYPE=Session::get('DB_CONS_TYPE');
  $DB_ELE_TYPE=Session::get('DB_ELE_TYPE');
  $monthName = date("F", mktime(0, 0, 0, $DB_MONTH, 10));
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Officer Report</title>
       
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
                           <td><strong>Officer Report</strong></td>
                         </tr>
                          <tr>  
                           <td><strong>Name: {{ $DB_CONS_TYPE.' '.$DB_ELE_TYPE.' '.'ELECTION-'.' '.$monthName.' '.$DB_YEAR }}</strong> </td>
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
          <th>State</th> 
		  <th>Total AC</th> 
          <th>Total Candidate</th> 
          <th>Completed</th> 
          <th>InProgress</th> 
          <th>NotStarted</th> 
                </tr>
            </thead>
            <tbody>
     @php  
     $count = 1; 
        $TotalUsers = 0;
        $TotalfiledData = 0;
        $TotalnotfiledData = 0;
        $Totalfinalcompletedcount= 0;
        $Totalac = 0;

        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)

         @php

        $TotalUsers +=$listdata->totalcandidate;
         
         $stdetails=getstatebystatecode($listdata->st_code);
		 $acbystate=getacbystate($listdata->st_code);
		 $account=count($acbystate);
		 $Totalac += $account;
         $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
       
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('AC',$listdata->st_code,$cons_no);
         $TotalfiledData +=  $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $Totalfinalcompletedcount += $finalcompletedcount;

         @endphp
          <tr>
           <td>{{ $count }}</td>
            <td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>
			<td align="right">@if($account =='' )   0  @else <b>{{  $account }}</b> @endif</td>
            <td align="right">@if($listdata->totalcandidate =='' )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
            <td align="right"> @if($finalcompletedcount =='' )     0  @else <b>{{  $finalcompletedcount }}</b> @endif </td>
            <td align="right"> @if($filedcount =='')     0  @else <b>{{  $filedcount }}</b> @endif</td>
            <td align="right">@if($notfiledcount =='')     0  @else <b>{{  $notfiledcount }}</b> @endif</td>         
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">Records Not Found</td>                 
              </tr>
          @endforelse
         <tr><td><b>Total</b></td><td></td><td align="right"><b>{{$Totalac}}</b></td><td align="right"><b>{{$TotalUsers}}</b></td><td align="right"><b>{{$Totalfinalcompletedcount}}</b></td><td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td></tr>
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