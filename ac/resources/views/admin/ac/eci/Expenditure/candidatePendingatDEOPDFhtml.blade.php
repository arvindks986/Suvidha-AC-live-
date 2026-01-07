@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getpcbystate($st_code);
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
        <title>List Of Contested Candidate</title>
       
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
                           <td><strong>Candidate List Pending At DEO</strong></td>
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
	  <th>S.No</th>
	  <th>AC No & Name</th>
	  <th>Candidates Name</th>
	  <th>Party Name</th>
	  <th>Last Date Of Submission</th>
	  <th>Date Of Scrutiny Report Submission</th>
	  <th>Date Of Lodging A/C By Candidate</th>
	  <th>Date Of Sending To CEO</th>
  </tr>
   </thead>
            <tbody>
     @php  
     $count = 1; 
        $TotalUsers = 0;
        @endphp
         @forelse ($pendingatDEOCandList as $candDetails)
         @php
         $TotalUsers =count($pendingatDEOCandList);
         $acDetails=getacbyacno($candDetails->ST_CODE,$candDetails->constituency_no);
         $lastdatedata = new DateTime($candDetails->last_date_prescribed_acct_lodge);
			//echo $date->format('d.m.Y'); // 31.07.2012
			$lastdate = $lastdatedata->format('d-m-Y'); // 31-07-2012

			$scrutinysubmit = new DateTime($candDetails->report_submitted_date);
			$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
			//$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
			$candidatelodging = new DateTime($candDetails->date_orginal_acct);
			$candidatelodgingdate = $candidatelodging->format('d-m-Y'); // 31-07-2012

			$sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
			$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012

			$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
			$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012

			

			$lastdate =$lastdate ??  'N/A';
			$scrutinyreportsubmitdate = !empty($scrutinyreportsubmitdate && $scrutinyreportsubmitdate !='30-11--0001')  ?  $scrutinyreportsubmitdate : 'N/A';
			$candidatelodgingdate =  !empty($candidatelodgingdate && $candidatelodgingdate !='30-11--0001')  ?  $candidatelodgingdate : 'N/A' ;
			$ceosendingdate =  !empty($ceosendingdate && $ceosendingdate !='30-11--0001' && $candDetails->final_by_ro=='1')  ?  $ceosendingdate : 'N/A' ; 
			$ceoreceivedate = !empty($ceoreceivedate && $ceoreceivedate !='30-11--0001' && $candDetails->final_by_ro=='1')  ?  $ceoreceivedate : 'N/A' ; 

         // dd($candDetails);
         @endphp
          <tr>
		  <td>{{ $count}}</td>
         <td>@if(!empty($acDetails->AC_NO)) {{ $acDetails->AC_NO}} - {{ $acDetails->AC_NAME}} @endif</td>
          <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
          <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
          <td>@if(!empty($lastdate )) {{$lastdate }} @endif</td>
          <td>@if(!empty($scrutinyreportsubmitdate)) {{$scrutinyreportsubmitdate}} @endif</td>
          <td>@if(!empty($candidatelodgingdate)) {{$candidatelodgingdate}} @endif</td>       	
           <td>@if(!empty($ceosendingdate)) {{$ceosendingdate}} @endif</td>       			  
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Record Found </td>                 
              </tr>
            @endforelse
            <!-- <tr><td><b>Total</b></td><td></td><td><b></b></td><td><b></b></td></tr> -->
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