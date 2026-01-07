<?php  $st=getstatebystatecode($st_code);   ?>
<html>
  <head>
    <style>
      

  @page {
            header: page-header;
            footer: page-footer;
        }


    td {
    font-size: 11px !important;
    font-weight: 500;
    text-align: left;
    padding: 7px 0px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
      .bold{
    padding: 12px 0px 0px 30px !important;
    font-weight: bold;
  }
.bolds{
  font-weight: bold;
}

.bolds span{
  font-weight: normal;
}

.bold span{
  font-weight: normal;
}
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    border-spacing: 0px;
    }
    .border{
    border: 1px solid #000;
    text-align: left;
    }
    th {
    font-size: 12px;
    padding: 6px 0px;
    font-weight: bold !important;
    }

    table{
    width: 100%;
    border-collapse: collapse;
    font-weight: bold;
    }
    </style>
  </head>
  <div class="bordertestreport">



          <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, State Election,{{getElectionYear()}} to the legislative assembly of {{$st->ST_NAME}}
</strong></p></td>
            </tr>
             
  </table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>6 - ELECTORS DATA SUMMARY</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(6, $st_code) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style=""><p style="font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
	  <?php } ?>


  </table>






    <!-- <table class="border">
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
          <p style="font-size: 15px;"><b>6 - ELECTORS DATA SUMMARY
          </b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong>{{$st->ST_NAME}}</p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> 27-06-2019</p></td>
      </tr>
    </table> -->

    <table class="table" style="width: 100%;">
      <thead>
        <tr>
          <th style="border-top: 1px solid #000;"></th>
          <th colspan="3" class="bolds" style="text-align: center;border-top: 1px solid #000;">TYPE OF CONSTITUENCY</th>
          <th style="border-top: 1px solid #000;"></th>
        </tr>
        <tr>
          <th class="bolds blc"></th>
          <th class="bolds blc">GEN</th>
          <th class="bolds blc">SC</th>
          <th class="bolds blc">ST</th>
          <th class="bolds blc">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bolds">1. NO. OF CONSTITUENCIES
          </td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalscac']) ? ($electorsvotersdataNew['SC']['totalscac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalstac']) ? ($electorsvotersdataNew['ST']['totalstac']) :0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) +(isset($electorsvotersdataNew['SC']['totalscac'])? ($electorsvotersdataNew['SC']['totalscac']) :0) + (isset($electorsvotersdataNew['ST']['totalstac'])?$electorsvotersdataNew['ST']['totalstac']:0)}}</td>
        </tr>
        <tr>
          <td colspan="4"><b>2. ELECTORS</b> (Including SERVICE VOTERS)
          </td>
        </tr>
        <tr>
          <td class="bold">a. MALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['maleElectors']) ? ($electorsvotersdataNew['SC']['maleElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['maleElectors']) ? ($electorsvotersdataNew['ST']['maleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0)+(isset($electorsvotersdataNew['SC']['maleElectors'])? ($electorsvotersdataNew['SC']['maleElectors']):0)+(isset($electorsvotersdataNew['ST']['maleElectors'])? ($electorsvotersdataNew['ST']['maleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. FEMALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors'])? ($electorsvotersdataNew['GEN']['femaleElectors']):0 )}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']): 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['femaleElectors']) ? ($electorsvotersdataNew['ST']['femaleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors']) ? ($electorsvotersdataNew['GEN']['femaleElectors']) :0)+ (isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']):0)+(isset($electorsvotersdataNew['ST']['femaleElectors'])? ($electorsvotersdataNew['ST']['femaleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. THIRD GENDER</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['thirdElectors'])? ($electorsvotersdataNew['SC']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['thirdElectors'])? ($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']):0)+ (isset($electorsvotersdataNew['SC']['thirdElectors'])?($electorsvotersdataNew['SC']['thirdElectors']):0)+(isset($electorsvotersdataNew['ST']['thirdElectors'])?($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. TOTAL</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])? ($electorsvotersdataNew['GEN']['totalElectors']):0)+ (isset($electorsvotersdataNew['SC']['totalElectors']) ? ($electorsvotersdataNew['SC']['totalElectors']):0)+ (isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">3. ELECTORS WHO VOTED
          </td>
		  
		  
        </tr>
        <tr>
          <td class="bold">a. MALE</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. FEMALE</td>
           <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])? ($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. THIRD GENDER</td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)+(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
        </tr>

        <tr>
          <td class="bold" align="left"><b>d. POSTAL</b></td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
        </tr>
		
		<tr>
          <td class="bold">e. TEST VOTES </td>
          <td>{{(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
        </tr>
		
        <tr>
          <td class="bold">f. TOTAL</td>
		  
		  <?php 

		  
		  $totalgen = (isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters']) ? ($totalvoteNew['GEN']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0);
		  
		  $totalsc = (isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0) + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0);
		  
		  $totalst = (isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0);
		  
		  $totalvoters = (isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])? ($totalvoteNew['SC']['totalOtherVoters']):0) + (isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0) +(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0) + +(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0) + +(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)  + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0);
		  
		  
		  ?>
		  
          <td>{{$totalgen}}</td>

          <td>{{$totalsc}}</td>
          <td>{{$totalst}}</td>
          <td>
            {{$totalvoters}}
        </td>
        </tr>
        <tr>
          <td class="bold">PROXY <span style="font-weight: normal;"></span>
          </td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)+(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)+(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">4. OVERSEAS ELECTORS
          </td>
        </tr>
        <tr>
          <td class="bold">a. MALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}
          </td>
        </tr>
        <tr>
          <td class="bold">b. FEMALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. THIRD GENDER</td>
           <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. TOTAL</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>
            {{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">5. OVERSEAS ELECTORS WHO VOTED
          </td>
        </tr>
        <tr>
          <td class="bold">a. MALE</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. FEMALE</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. THIRD GENDER</td>
           <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?$totalvoteNew['GEN']['overseasthirdvoters']:0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. TOTAL</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">6. REJECTED POSTAL VOTES
          </td>
        </tr>
        <tr>
          <td class="bold">a. NO OF  REJECTED POSTAL VOTES
          </td>
          </td>
          <td>{{(isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. AS % OF TOTAL POSTAL VOTES</td>

          <td>
            {{round((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)/(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)*100,2)}}
          </td>
          <?php if(isset($totalpostalvoteNew['SC']['postaltotalreceived']) && ($totalpostalvoteNew['SC']['postaltotalreceived'] > 0 )) { ?>
          <td>



          {{round((isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)/(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)*100,2)}}

        </td>
      <?php } else { ?>
        <td>0</td>

      <?php } ?>

      <?php if(isset($totalpostalvoteNew['ST']['postaltotalreceived']) && ($totalpostalvoteNew['ST']['postaltotalreceived'] > 0 )) { ?>

          <td>
          {{round((isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)/(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)*100,2)}}

        </td>
         <?php } else { ?>
            <td>0</td>

          <?php } ?>

          <td>{{round(((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0))/((isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0))*100,2)}}</td>
        </tr>
        <tr>
        
        <tr>
          <td class="bold">c. VOTES REJECTED FROM <br>EVM <span style="font-weight: bold;">(TEST VOTES+REJECTED DUE TO OTHER <br> REASON)</span>
          </td>
          <td>{{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+
          (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)+(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)+(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds">7. NOTA VOTES <span style="font-weight: bold;">(POSTAL + EVM)</span></td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds">8. VALID VOTES 
          </td>


          <?php $total1 = ($totalgen)-((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)
          +((isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)
          )+(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)); ?>

          <?php $total2 = ($totalsc)-((isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+((isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0))
          +(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)); ?>

          <?php $total3 = ($totalst)-((isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)+((isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?
          ($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0))+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)); ?>

          <td>
            {{ $total1 }}

          </td>
          <td>
             {{ $total2 }}

          </td>
          <td>
             {{ $total3 }}

          </td>
          <td>{{$total1+$total2+$total3}}</td>

        </tr>
        <tr>
          <td class="bolds">9. POLL PERCENTAGE
          </td>
          <?php if(isset($electorsvotersdataNew['GEN']['totalElectors']) && ($electorsvotersdataNew['GEN']['totalElectors'] > 0)) { ?>
            <td>{{round($totalgen/(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)*100,2)}}</td>
          
          <?php } else { ?>
          <td>0</td>
        <?php } ?>

          <?php if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) { ?>
          <td>{{round($totalsc/$electorsvotersdataNew['SC']['totalElectors']*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($electorsvotersdataNew['ST']['totalElectors']) && ($electorsvotersdataNew['ST']['totalElectors'] > 0)) { ?>
          <td>{{round($totalst/(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>


          <td>{{round(($totalvoters)/((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)+(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)
            +(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0))*100,2)}}</td>
        </tr>
        <tr>
          <td class="bolds">10. NO. OF POLLING STATIONS
          </td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)
            +(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)
            +(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds">11. AVERAGE NO. OF <br> ELECTORS PER POLLING <br>STATION 
          </td>
          <?php
                if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { 


              $total4 = (isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0);

            }else{
              $total4 = 0;
            }
            if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) {
              $total5 = (isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0);
            } else{
              $total5 = 0;
            }

            if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation'] > 0)) {
              $total6 = (isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0);
            } else{
              $total6 = 0;
            }
            

          ?>
          <?php if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { ?>
		  
		  <?php $totalelectors = (isset($electorsvotersdataNew['GEN']['totalElectors'])? ($electorsvotersdataNew['GEN']['totalElectors']):0)+ (isset($electorsvotersdataNew['SC']['totalElectors']) ? ($electorsvotersdataNew['SC']['totalElectors']):0)+ (isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0); 
		
		$totalpollingstations = (isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)
            +(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)
            +(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0);
		
		?>

          <td>{{round((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($totalvoteNew['SC']['totalpollingstation']) && ($totalvoteNew['SC']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
        <?php if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
          @if($totalpollingstations > 0)
				<td>{{round($totalelectors/$totalpollingstations,0)}}</td>
		@else
		 <td>0</td>	
		@endif
        
        </tr>
      </tbody>
    </table>
  


     <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>

 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>

   <htmlpagefooter name='page-footer'>
 <table>
 <tr>
 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>

<?php  if (verifyreport(6, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>




  </div>
</html>
