@php 
  $stcode = !empty($_GET['state'])?$_GET['state']:""; 
  $ac = !empty($_GET['ac'])?$_GET['ac']:""; 
  $all_ac=getacbystate($stcode);

  $st=getstatebystatecode($stcode);
  $acdetails=getacbyacno($stcode, $ac); 
  
  $acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
  
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
        <title>Candidate Wise Expenditure Report</title>
       
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
                           <td><strong>Candidate Wise Breaching Detail Report</strong></td>
                         </tr>
						  <tr>  
                           <td><strong>Name: {{ $DB_CONS_TYPE.' '.$DB_ELE_TYPE.' '.'ELECTION-'.' '.$monthName.' '.$DB_YEAR }}</strong> </td>
                         </tr>
                         
                  
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
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>

             <table style="width:100%;margin-bottom:20px;" border="0" >
              <tr>         
                           <?php  
                           if(!empty($stcode)){?> 
                           <td><strong>State : </strong>{{$stateName}}</td>
                            <?php } ?>

                           <?php  
                           if(!empty($ac)){?> 
                           <td><strong>AC Name : </strong>{{$acName}}</td>
                           <?php } ?>
                          <td><strong>Election Type : </strong>AC General</td>

                          </tr>
                          
            </table>

      <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
		<thead>
		<tr>
			<th>S. No.:</th>
			<th>Candidate Name</th>
			<th>State</th>
			<th>AC No & AC Name</th>
			<th>Election Year</th>
			<th>Election Type</th>
			<th>Total Expenditure Assessed <br />By DEO(Rs.)</th>
			<th>Total Expenditure Declared <br />By Candidate(Rs.)</th>
			<th>Total Breach Amount(Rs.) <br /> (VIII-VII)</th>
		</tr>
		
		</thead>
<?php 
		$j=0;
		$grandTotal = 0;
		$grandTotalAssessbyDEO=0;
		$avgTotalbycand=0;
		$avgbyAssessbyDEO=0;
		$grandTotalBreachAmnt=0;
		$count=1;
 ?>
    @if(!empty($candList))
    @foreach($candList as $candDetails)  
      <?php 
			$count=count($candList);
			$candidate_id=$candDetails->candidate_id;
			$acdetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
			$st = getstatebystatecode($candDetails->ST_CODE);
			$totalamntassesbyDEO=$candDetails->amt_as_per_observation;
        ?>
        @php
		$grandTotalAssessbyDEO += $totalamntassesbyDEO;
		$totalamount = !empty($candDetails->grand_total_election_exp_by_cadidate)? $candDetails->grand_total_election_exp_by_cadidate : 0; 
		$grandTotal += $totalamount;
		$BreachAmnt=0;
		if(!empty($totalamntassesbyDEO) && ($totalamount != $totalamntassesbyDEO)){ 
		$BreachAmnt=$totalamntassesbyDEO-$totalamount;
		}
		if(!empty($BreachAmnt) && $BreachAmnt > 0){
		$BreachAmnt = '+'.$BreachAmnt;
		}elseif(!empty($BreachAmnt) && $BreachAmnt < 0){
		$BreachAmnt = $BreachAmnt;
		}else{
		$BreachAmnt = 0;
		}

        @endphp
<tr>
<td>{{++$j}}</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif </td>
<td>@if(!empty($st->ST_NAME)) {{$st->ST_NAME}} @endif</td>
<td>@if(!empty($acdetails->AC_NO)) {{$acdetails->AC_NO}} - {{$acdetails->AC_NAME}} @endif</td>
<td>@if(!empty($candDetails->YEAR)) {{$candDetails->YEAR}} @endif</td>
<td>@if(!empty($candDetails->election_type)) {{$candDetails->election_type}} @endif</td>
<td align="left">{{$totalamntassesbyDEO}}</td>
<td align="left">{{$totalamount}}</td>
<td align="right">{{$BreachAmnt}}</td>
</td>

</tr>
@endforeach 
@endif 
            </tbody>
<tfoot>
<tr>
<td colspan="6">Total(Rs.)</td>
<td align="right"><b> {{$grandTotalAssessbyDEO}}</b></td>
<td align="right"><b> {{$grandTotal}}</b></td>
</tr>
@php
 $avgTotalbycand= round($grandTotal/$count);
 $avgbyAssessbyDEO= round($grandTotalAssessbyDEO/$count);
 $avgBreachAmnt =round($grandTotalBreachAmnt/$count);
@endphp
<tr>
<td colspan="6">Average(Rs.)</td>
<td align="right"><b> {{$avgbyAssessbyDEO}}</b></td>
<td align="right"><b> {{$avgTotalbycand}}</b></td>
</tr>
</tfoot>
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