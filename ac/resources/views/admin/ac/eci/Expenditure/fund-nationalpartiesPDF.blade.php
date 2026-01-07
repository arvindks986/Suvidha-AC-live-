@php 
  $party = !empty($_GET['party'])?$_GET['party']:""; 
  $stcode = !empty($_GET['state'])?$_GET['state']:""; 
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $ac = !empty($_GET['ac'])?$_GET['ac']:""; 

  $st=getstatebystatecode($stcode);
  $partyName = getpartybyid($party);
  $acdetails=getacbyacno($stcode,$cons_no); 
  
  $acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
  $partyname=!empty($partyName->PARTYNAME) ? $partyName->PARTYNAME : 'ALL';
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
        <title>Party Wise Expenditure Report</title>
       
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
                 <td  style="width:100%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>Party Wise Expenditure Report</strong></td>
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
                          </tr>
                         <tr>
                          <?php  
                           if(!empty($party)){?>
                           <td><strong>Party Name : </strong>{{$partyname}}</td>
                          <?php } ?>
                           <td><strong>Election Type : </strong>AC General</td>
                         </tr>
            </table>
@php  
$count = 1;
$allPartylist=[];
$grandTotalParty=0;  
$grandTotalOtherSources=0;  
$grandTotalAvgParty =0;  
$grandTotalOverall  =0;  
		@endphp
            @if(!empty($partylist))
           @foreach ($partylist as $partylists)
        @php
          $totalcandidates=\app(App\models\Expenditure\ExpenditureModel::class)->getcandidatesbyparties($partylists->CCODE,$stcode,$ac);
		 $countPartywiseCandidate = count(explode(',',$totalcandidates));
		 
		 $totalpartyexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getPoliticalpartyExp($totalcandidates);
         $grandTotalParty += $totalpartyexpen; 
		 
		 $avgpartyexpencandidatewise= round($totalpartyexpen/$countPartywiseCandidate,2);
		 $grandTotalAvgParty += $avgpartyexpencandidatewise; 
			
		 $totalothersexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getOtherSourcesExp($totalcandidates);
         $grandTotalOtherSources += $totalothersexpen; 
		 
		 $totaloverallexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getGrandTotalExp($totalcandidates);
         $grandTotalOverall += $totaloverallexpen; 
    
     $allPartylist[]=[
     'PARTYABBRE'=>$partylists->PARTYABBRE,
     'PARTYNAME'=>$partylists->PARTYNAME,
     'avgpartyexpencandidatewise'=> $avgpartyexpencandidatewise,
	 'totalpartyexpen'=>$totalpartyexpen,
	 'totalothersexpen'=>$totalothersexpen,
	 'totaloverallexpen'=>$totaloverallexpen,
	 'totalcandidates'=>$countPartywiseCandidate
     ]; @endphp
     @endforeach 
@endif
<?php 
$amount=array_column($allPartylist,'totalpartyexpen');
array_multisort($amount, SORT_DESC,$allPartylist);
?>

        <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
            <thead>
          <tr>
          <th>S.no</th>
          <th>Party</th>
		  <th>Average funds given to a candidate by national parties(Rs.)</th>
          <th>Total funds given by National Parties(Rs.)</th>
		  <th>Total funds given by Other Sources(Rs.)</th>
		  <th>No. of candidate to whome National Parties gave funds</th>
           </tr>
            </thead>
            <tbody>
            @if(!empty($partylist))
		@foreach($allPartylist as $partylists) 
<tr>
<td><?php echo $count++; ?></td>
<td>{{ $partylists['PARTYABBRE'] }} - {{$partylists['PARTYNAME']}}</td>
<td align="right">{{$partylists['avgpartyexpencandidatewise']}}</td>
<td align="right">{{$partylists['totalpartyexpen']}}</td>
<td align="right">{{$partylists['totalothersexpen']}}</td>
<td align="right"> {{!empty($partylists['totalpartyexpen'])? $partylists['totalcandidates']:0}}</td>
</tr>
@endforeach  
@endif
</tbody>
<tfoot>
 <tr>
    <td colspan="2"><b>Total Expenditure(Rs.)</b></td>
	 <td align="right"><b>{{$grandTotalAvgParty}}</b></td>
	 <td align="right"><b>{{$grandTotalParty}}</b></td>
	 <td align="right"><b>{{$grandTotalOtherSources}}</b></td>
	<td></td>
	<td></td>
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