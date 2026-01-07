<?php  $st=getstatebystatecode($st_code);   ?>
<html>
  <head>
    <style>


  @page {
            header: page-header;
            footer: page-footer;
        }


    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    text-transform: uppercase;
    padding: 5px 0px;
    font-family: "Times New Roman", Times, serif;
    }  
    .dev td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 5px 0px;
    text-transform: uppercase;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 17px !important;
    font-weight: 600;
    }
    .left-al tr td{
    text-align: left;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-spacing: 0px 8px;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 13px;
    font-weight: bold !important;
    text-align: left;
    padding: 7px;
    text-transform: uppercase;
    }
p{
  text-transform: uppercase;
  font-size: 13px !important;
}
.dev tr td{
  width: 9.7%;
  text-align: center;
}
    table{
    width: 100%;
    border-collapse: collapse;
    }
    </style>
  </head>
  <div class="bordertestreport">





          <table class="devl">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;text-transform: capitalize;"><strong style="text-transform: capitalize;">Election Commission of India, State Election,{{getElectionYear()}} to the legislative assembly of {{$st->ST_NAME}}
</strong></p></td>
            </tr>
             
  </table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>4 - HIGHLIGHTS</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(4, $st_code) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 12px; ">User</b>: ECI</td>
        <td colspan=""></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style=""><p style="font-size: 12px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 12px;font-weight: bold;">Draft</p></td>
      </tr>
	  <?php } ?>


  </table>





   <!--  <table class="border">
      <tr>
        <td style="text-align: left;">
          <p> <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
            </b>
          <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
        </td>
      </tr>
    </table>
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p style="font-size: 15px;"><b>4 - Highlights </b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong>{{$st->ST_NAME}}</p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table> -->
    <table><tr><td><p></p></td></tr>
  </table>

              <p><b>1. No. of Constituencies</b></p>
      

   <table class="table table-bordered dev" style="width: 100%;table-layout: fixed;">
           
            <tr>
              <td colspan="6"><b>Type Of Constituency</b></td>
              <td><b>GEN</b></td>
              <td><b>SC</b></td>
              <td><b>ST</b></td>
              <td colspan=""><b>Total</b></td>
            </tr>
            <tr>
              <td colspan="6"><b>No Of Constituencies</b></td>
             <td>{{(isset($candidates->genac) ? $candidates->genac : 0)}}</td>
              <td>{{(isset($candidates->scac) ? $candidates->scac : 0) }}</td>
              <td>{{ (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
              <td colspan="">{{(isset($candidates->genac) ? $candidates->genac : 0) +(isset($candidates->scac) ? $candidates->scac: 0) + (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
            </tr>
         
</table>
       
 
              <p><b>2. NO. of Contestants</b></p>
        

         <table class="table table-bordered dev">  

            <tr>
              <td colspan="2"><b>NO. of Contestants in a Constituency</b></td>
              <td><b>1</b></td>
              <td><b>2</b></td>
              <td><b>3</b></td>
              <td><b>4</b></td>
              <td><b>5</b></td>
              <td><b>6-10</b></td>
              <td><b>11-15</b></td>
              <td><b>Above 15</b></td>
            </tr>
            <tr>
              <td colspan="2"><b>NO Of Such CONSTITUENCIES
              </b></td>
              <td>{{$candidates->one}}</td>
              <td>{{$candidates->two}}</td>
              <td>{{$candidates->three}}</td>
              <td>{{$candidates->four}}</td>
              <td>{{$candidates->five}}</td>
              <td>{{$candidates->fiveten}}</td>
              <td>{{$candidates->tenfifteen}}</td>
              <td>{{$candidates->fifteen}}</td>
            </tr>

                     </table>


<table> 
            <tr>
              <td colspan="8">Total Contestants in a Fray</td>
              <td colspan="2">{{$candidates->Total_Candidates}}</td>
            </tr>
            <tr>
              <td colspan="8">Average Contestants Per Constituency</td>
              <td colspan="2">{{$candidates->Avg}}</td>
            </tr>
            <tr>
              <td colspan="8">Minimum Contestants in a Constituency</td>
              <td colspan="2">{{$candidates->maxcnd}}</td>
            </tr>
            <tr>
              <td colspan="8">Maximum Contestants in a Constituency</td>
              <td colspan="2">{{$candidates->mincnd}}</td>
            </tr>

</table>
            <p><b>3.Electors </b></p>

<table class="table table-bordered dev">

            <tr>
              <td colspan="6"></td>
              <td colspan=""><b>Male<b></td>
              <td colspan=""><b>Female</b></td>
              <td colspan=""><b>Third Gender</b> </td>
              <td colspan=""><b>Total</b></td>
            </tr>
            <tr>
              <td>i.</td>
              <td  class="dev2" colspan="5"><b>NO. OF ELECTORS</b>(Including Service Electors)</td>
              <td colspan="">{{$candidates->maleElectors}}</td>
              <td colspan="">{{$candidates->femaleElectors}}</td>
              <td colspan="">{{$candidates->thirdElectors}}</td>
              <td colspan="">{{$candidates->totalElectors}}</td>
            </tr>
            <tr>
              <td>ii.</td>
              <td colspan="5"> <b>No. of Electors Who
              Voted</b>(Excluding Postal Votes)</td>
              <td colspan="">{{$candidates->totalMaleVoters}}</td>
              <td colspan="">{{$candidates->totalFemaleVoters}}</td>
              <td colspan="">{{$candidates->totalOtherVoters}}</td>
              <td colspan="">{{$candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters}}</td>
            </tr>
            <tr>
              <td>iii. </td>
              <td colspan="5"><b>Polling Percentage</b>(Excluding Postal Ballots)</td>
              <td colspan="">{{round($candidates->totalMaleVoters/$candidates->maleElectors * 100,2)}}</td>
              <td colspan="">{{round($candidates->totalFemaleVoters/$candidates->femaleElectors * 100,2)}}</td>
              <?php if($candidates->thirdElectors != 0)  { ?>
                <td colspan="">{{round($candidates->totalOtherVoters/$candidates->thirdElectors * 100,2)}}</td>
              <?php } else { ?>
                <td>0</td>
              <?php } ?>
              <td colspan="">{{round(($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters)/$candidates->totalElectors * 100,2)}}</td>
            </tr>
</table>


        <table class="table">
		<tr>
            <th>3a</th>
            <td colspan="7"> Polling Percentage (State)</td>
            <td colspan="2">{{round(($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters +$candidates->totalPostalVoters)/$candidates->totalElectors * 100,2)}}</td>
          </tr>
		
		

<tr>
            <th>4a</th>
            <td colspan="7"> Total Votes Polled (EVM + Postal)</td>
            <td colspan="2">{{$candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters +$candidates->totalPostalVoters + $candidates->test_votes_49_ma}}</td>
          </tr>
			
			
          <tr>
            <th>4b</th>
            <td colspan="7"> Total valid Votes (EVM + Postal)</td>
            <td colspan="2">{{$candidates->totalEvmPostalvote}}</td>
          </tr>
		  
		  <tr>
            <th>5a</th>
            <td colspan="7"> NOTA Votes (EVM + Postal)</td>
            <td colspan="2">{{$candidates->notatotal}}</td>
          </tr>
		  
		  <tr>
            <th>5b</th>
            <td colspan="7"> NOTA Votes As % of Total Votes</td>
            <td colspan="2">{{round(($candidates->notatotal)/($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters +$candidates->totalPostalVoters) * 100,2)}}</td>
          </tr>
		  
		  
		  <tr>
            <th>6a</th>
            <td colspan="7"> Total Postal Votes</td>
            <td colspan="2">{{$candidates->totalPostalVoters}}</td>
          </tr>
		  
		  <tr>
            <th>6b</th>
            <td colspan="7"> No Of Rejected Postal Votes</td>
            <td colspan="2">{{$candidates->rejectedpostalvote}}</td>
          </tr>
		  
		  <tr>
            <th>6c</th>
            <td colspan="7"> % of Rejected Postal Votes Over Total Postal Votes</td>
            <td colspan="2">{{round(($candidates->rejectedpostalvote)/($candidates->totalPostalVoters) * 100,2)}}</td>
          </tr>
		  
		  <tr>
		  <th>7a</th>
            <td colspan="7"> NO. OF POLLING STATIONS
            </td>
            <td colspan="2">{{$candidates->totalpollingstation}}</td>
          </tr>
          <tr>
            <th>7b</th>
            <td colspan="7">AVERAGE NO. OF ELECTORS PER POLLING STATION
            </td>
            <td colspan="2">{{round($candidates->totalElectors/$candidates->totalpollingstation,0)}}</td>
          </tr>

        </table>
		
		
		<div style="page-break-after: always;"></div>

                <p><b>8. Performance of Contesting Candidates</b></p>

        <table class="table table-bordered dev">
          
          <tr>
            <td colspan="6"></td>
            <td><b>Male</b></td>
            <td><b>Female</b></td>
            <td><b>Third Gender</b></td>
            <td colspan=""><b>Total</b></td>
          </tr>
          <tr>
            <td colspan=""><b>i. </b></td>
            <td colspan="5"><b>No. Of Contestants</b></td>
            <td>{{$candidates->totalnominatedmale}}</td>
            <td>{{$candidates->totalnominatedfemale}}</td>
            <td>{{$candidates->totalnominatedthird}}</td>
            <td>{{$candidates->totalnominatedmale+$candidates->totalnominatedfemale+$candidates->totalnominatedthird}}</td>
          </tr>
          <tr>
            <td><b>ii. </b></td>
            <td colspan="5"><b>Elected Candidates</b></td>
            <td>{{$candidates->totalwinnermale}}</td>
            <td>{{$candidates->totalwinnerfemale}}</td>
            <td>{{$candidates->totalwinnerthird}}</td>
            <td colspan="">{{$candidates->totalwinnermale+$candidates->totalwinnerfemale+$candidates->totalwinnerthird}}</td>
          </tr>
          <tr>
            <td><b>iii. </b></td>
            <td colspan="5"><b> Forfeited Deposits</b></td>
            <td>{{$candidates->fdmale}}</td>
            <td>{{$candidates->fdfemale}}</td>
            <td>{{$candidates->fdthird}}</td>
            <td colspan="">{{$candidates->fdmale+$candidates->fdfemale+$candidates->fdthird}}</td>
          </tr>
        </table>
    
</div>


   <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 10px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>

 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>

   <htmlpagefooter name='page-footer'>
 <table>
 <tr>
 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>

<?php  if (verifyreport(4, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>

</html>