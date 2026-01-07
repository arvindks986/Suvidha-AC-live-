<html>
  <head>
    <style>
      

  @page {
            header: page-header;
            footer: page-footer;
        }


    td {
    font-size: 11px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 6px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
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
    font-size: 12px;
    font-weight: bold !important;
    padding: 5px;
    text-align: left;
    }
 
    table{
    width: 100%;
    border-collapse: collapse;
    }
    </style>
  </head>
  <?php  $st=getstatebystatecode($st_code);   ?>
  <div class="bordertestreport">

          <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, State Election,{{getElectionYear()}} to the legislative assembly of {{$st->ST_NAME}}
</strong></p></td>
            </tr>
             
  </table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>2 - LIST OF SUCCESSFUL CANDIDATES </strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(2, $st_code) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
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


<!--     <table class="border">
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
          <p style="font-size: 15px;"><b>2 - LIST OF SUCCESSFUL CANDIDATES</b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong> {{$st->ST_NAME}} </p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table> -->
    <table><tr><td><p></p></td></tr>
  </table>
  <table class="" style="width: 100%; margin-bottom:20px;">
      <thead>
	     <tr>
          <th class="blcs"></th>
          <th class="blcs"> </th>
          <th class="blcs" colspan="7" style="text-align:center;">WINNER </th>
		  <th class="blcs" colspan="4" style="text-align:center;">RUNNER-UP </th>
		  <th class="blcs"></th>
        </tr>
	  
        <tr>
          <th class="blcs">AC No.</th>
          <th class="blcs">CONSTITUENCY </th>
          <th class="blcs">Name </th>
          <th class="blcs">SEX</th>
          <th class="blcs">PARTY</th>
          <th class="blcs">SYMBOL</th>
          <th class="blcs">AGE</th>
          <th class="blcs">SOCIAL CATEGORY</th>
          <th class="blcs">VOTES SECURED</th>
		  <th class="blcs">Name </th>
		  <th class="blcs">SEX</th>
          <th class="blcs">PARTY</th>
		  <th class="blcs">VOTES SECURED</th>
		  <th class="blcs">WINNING MARGIN</th>
        </tr>
      </thead>
      <tbody>
	  
	@foreach($dataCaddidateWise as $key => $data)
	  
        <tr>
          <td>{{$data->ac_no}}</td>
          <td>{{$data->ac_name}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{ucwords(strtolower($data->lead_cand_name))}}</td>
           <td>@if($data->cand_gender == 'male')
			 M
		  @elseif($data->cand_gender == 'female')
			F
		  @elseif($data->cand_gender == 'third')
			TG
		  @endif
		  </td>
          <td>{{$data->lead_party_abbre}}</td>
          <td>{{$data->SYMBOL_DES}}</td>
          <td>{{$data->cand_age}}</td>
          <td>{{strtoupper($data->cand_category)}}</td>
		  <td>{{$data->lead_total_vote}}</td>
		  <td>{{$data->trail_cand_name}}</td>
		   <td>@if($data->trail_cand_gender == 'male')
			 M
		  @elseif($data->trail_cand_gender == 'female')
			F
		  @elseif($data->trail_cand_gender == 'third')
			TG
		  @endif
		  </td>
		  <td>{{$data->trail_party_abbre}}</td>
		  <td>{{$data->trail_total_vote}}</td>
		  <td>{{$data->margin}}</td>
        </tr>
		@endforeach
        
      </tbody>
    </table>
	
    <table class="border" border = "1" style="width: 60%; margin: auto; text-align: center;">
      <thead>
        <tr><th colspan="6" style="font-weight: bold; font-size: 14px; text-align:center;">PARTY WISE SUMMARY</th></tr>
        <tr><td colspan="2" style="font-size: 12px;text-align: center;"><p><b>{{$dataPartyWise[0]->st_name}}</b></p></td>
		<td colspan="4" style="font-size: 12px;text-align: center;"><p><b>Winning Candidates</b></p></td></tr>
        <tr>
          <td class="blc"  colspan="2"><b>PARTY NAME</b></td>
          <td class="blc"><b>Total</b></td>
          <td class="blc"><b>Male</b></td>
          <td class="blc"><b>Female</b></td>
          <td class="blc"><b>TG</b></td>
        </tr>
      </thead>
      <tbody>
		<?php $all_total = $all_male = $all_female = $all_tg = 0; ?>
		@foreach($dataPartyWise as $key => $data)
		
		<?php $all_total += $data->total_seats; 
		$all_male += $data->male;
		$all_female += $data->female;
		$all_tg += $data->third; ?>
		
        <tr>
          <td colspan="2">{{$data->lead_cand_party}}</td>
          <td>{{$data->total_seats}}</td>
          <td>{{$data->male}}</td>
          <td>{{$data->female}}</td>
          <td>{{$data->third}}</td>
        </tr>
		@endforeach
		
		<tr>
          <td colspan="2"><b>Total of Successful Candidates</b></td>
          <td><b>{{$all_total}}</b></td>
          <td><b>{{$all_male}}</b></td>
          <td><b>{{$all_female}}</b></td>
          <td><b>{{$all_tg}}</b></td>
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

<?php  if (verifyreport(2, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>



</div>
</html>