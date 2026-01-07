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
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>11 - AC Wise Number Of Electors</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(12, $st_code) == 0){ ?>
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
          <td class="blcs" rowspan="2">AC No.</td>
          <td class="blcs" rowspan="2">AC Name</td>
          <td class="blcs" colspan="4" style="text-align: center;">GENERAL(Including NRIs)</td>
          <td class="blcs" colspan="3" style="text-align: center;">SERVICE </td>
          <td class="blcs" colspan="4" style="text-align: center;">All Electors</td>
          <td class="blcs" colspan="4" style="text-align: center;">NRIs</td>
        </tr>
		
		<tr>
          <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
        </tr>
      </thead>
      <tbody>
	  
		@foreach($electorsdata as $key => $data)
	  
        <tr>
          <td>{{$data->AC_NO}}</td>
          <td>{{$data->AC_NAME}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{$data->gen_male}}</td>
          <td>{{$data->gen_female}}</td>
          <td>{{$data->gen_third}}</td>
          <td>{{$data->gen_total}}</td>
          <td>{{$data->service_male}}</td>
          <td>{{$data->service_female}}</td>
          <td>{{$data->service_total}}</td>
          <td>{{$data->grand_male}}</td>
          <td>{{$data->grand_female}}</td>
          <td>{{$data->grand_third}}</td>
          <td>{{$data->grand_total}}</td>
          <td>{{$data->nri_male}}</td>
          <td>{{$data->nri_female}}</td>
          <td>{{$data->nri_third}}</td>
          <td>{{$data->nri_total}}</td>
        </tr>
		@endforeach
		
		@foreach($electorsdata_total as $key => $data)
	  
        <tr>
          <td colspan="2" ><b>Total</b></td>
          <td><b>{{$data->gen_male}}</b></td>
          <td><b>{{$data->gen_female}}</b></td>
          <td><b>{{$data->gen_third}}</b></td>
          <td><b>{{$data->gen_total}}</b></td>
          <td><b>{{$data->service_male}}</b></td>
          <td><b>{{$data->service_female}}</b></td>
          <td><b>{{$data->service_total}}</b></td>
          <td><b>{{$data->grand_male}}</b></td>
          <td><b>{{$data->grand_female}}</b></td>
          <td><b>{{$data->grand_third}}</b></td>
          <td><b>{{$data->grand_total}}</b></td>
          <td><b>{{$data->nri_male}}</b></td>
          <td><b>{{$data->nri_female}}</b></td>
          <td><b>{{$data->nri_third}}</b></td>
          <td><b>{{$data->nri_total}}</b></td>
        </tr>
		@endforeach
        
      </tbody>
    </table>
   


   <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative; top: -11px; font-size: 12px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>

 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>

  </div>
  
  
  
      <htmlpagefooter name='page-footer'>
 <table>
 
<tr>

<?php  if (verifyreport(12, $st_code) != 0){ ?>


	<td align="left"><span style="font-size:8px; ">{{getreportsequence(7777, $st_code)}}</span></td>


<?php } ?>

 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>


</table>
 </htmlpagefooter>



</html>