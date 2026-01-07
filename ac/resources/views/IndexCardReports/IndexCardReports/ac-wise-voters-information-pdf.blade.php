<?php  $st=getstatebystatecode($st_code);   ?>
<html>
  <head>
    <style>

		@page { sheet-size: A4-L; }
        @page bigger { sheet-size: 420mm 370mm; }
        @page toc { sheet-size: A4; }

  @page {
            header: page-header;
            footer: page-footer;
        }



    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 9px;
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
    .bolds{
      font-weight: bold;
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
    .top
    {
      border-top: 1px solid #000;
    }
    .boldn{
      font-weight: bold;
      padding: 12px 0px 0px 30px;
    }  

     .bold{
      font-weight: bold;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    font-weight: bold;
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
    text-align: left;
    }
    table{
    width: 100%;
    }
    </style>
  </head>
  <div class="bordertestreport">



          <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, State Election,{{getElectionYear()}} to the legislative Assembly of {{$st->ST_NAME}}
</strong></p></td>
            </tr>
             
  </table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>12 - AC Wise Voters Information</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(13, $st_code) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 12px; ">User</b>: ECI</td>
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



<table>
  <tr><td><p></p></td></tr>
</table>



    <table class="table table-bordered table-striped" style="width: 100%;">
      <thead>
        <tr>
          <td rowspan="2"><b>AC No.</b></td>
          <td rowspan="2"><b>AC Name</b></td>
          <th colspan="4" style="text-align: center;"><b>Total Electors (Including Service Electors)</b></th>
		  <td rowspan="2"><b>Overseas Electors</b></td>
          <td rowspan="2"><b>SERVICE Electors</b></td>
          <th colspan="6" style="text-align: center;"><b>Electors who Voted</b></th>
          <td rowspan="2"><b>Overseas Electors who Voted</b></td>
          <td rowspan="2"><b>POLL %</b></td>
          <td rowspan="2"><b>Rejected Votes (Postal)</b></td>
          <td rowspan="2"><b>Votes Rejected From EVM (Test Votes + Rejected Votes due to Other Reasons)</b></td>
          <td rowspan="2"><b>NOTA Votes</b></td>
          <td rowspan="2"><b>Valid Votes Polled</b></td>
          <td rowspan="2"><b>Tendered Votes</b></td>
        </tr>
		
		<tr>

          <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>	
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>POSTAL </b></td>
          <td><b>TEST Votes </b></td>
          <td><b>TOTAL</b></td>
		  
        </tr>
      </thead>
      <tbody>
	  
		@foreach($electorsdata as $key => $data)
	  
        <tr>
          <td>{{$data->AC_NO}}</td>
          <td>{{$data->AC_NAME}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{$data->grand_male}}</td>
          <td>{{$data->grand_female}}</td>
          <td>{{$data->grand_third}}</td>
          <td>{{$data->grand_total}}</td>
		  <td>{{$data->nri_total}}</td>
          <td>{{$data->service_total}}</td>
          
          <td>{{$data->male_voter}}</td>
          <td>{{$data->female_voter}}</td>
          <td>{{$data->third_voter}}</td>
          <td>{{$data->postal}}</td>
          <td>{{$data->test_votes}}</td>
          <td>{{$data->total_voter}}</td>
          <td>{{$data->nri_voter}}</td>
          <td>@if($data->grand_total > 0){{round((($data->total_voter/$data->grand_total)*100),2)}} @else 0 @endif</td>
		  
          <td>{{$data->postal_rejected}}</td>
          <td>{{$data->rejected_votes}}</td>
          <td>{{$data->nota_votes}}</td>
          <td>{{$data->total_valid_votes}}</td>
          <td>{{$data->tended_votes}}</td>
        </tr>
		@endforeach
		
		@foreach($electorsdata_total as $key => $data)
	  
        <tr>
          <td colspan="2" ><b>Total</b></td>
          <td><b>{{$data->grand_male}}</b></td>
          <td><b>{{$data->grand_female}}</b></td>
          <td><b>{{$data->grand_third}}</b></td>
          <td><b>{{$data->grand_total}}</b></td>
          <td><b>{{$data->nri_total}}</b></td>
          <td><b>{{$data->service_total}}</b></td>

          <td><b>{{$data->male_voter}}</b></td>
          <td><b>{{$data->female_voter}}</b></td>
          <td><b>{{$data->third_voter}}</b></td>
          <td><b>{{$data->postal}}</b></td>
          <td><b>{{$data->test_votes}}</b></td>
          <td><b>{{$data->total_voter}}</b></td>
          <td><b>{{$data->nri_voter}}</b></td>
          <td><b>@if($data->grand_total > 0){{round(($data->total_voter/$data->grand_total)*100,2)}} @else 0 @endif</b></td>
		  
          <td><b>{{$data->postal_rejected}}</b></td>
          <td><b>{{$data->rejected_votes}}</b></td>
		   <td><b>{{$data->nota_votes}}</b></td>
          <td><b>{{$data->total_valid_votes}}</b></td>        
          <td><b>{{$data->tended_votes}}</b></td>
        </tr>
		@endforeach
        
      </tbody>
    </table>
   


   <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 12px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>

 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>

  </div>
  <htmlpagefooter name='page-footer'>
 <table>
 
<tr>

<?php  if (verifyreport(13, $st_code) != 0){ ?>


	<td align="left"><span style="font-size:8px; ">{{getreportsequence(7777, $st_code)}}</span></td>


<?php } ?>

 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>


</table>
 </htmlpagefooter>



</html>