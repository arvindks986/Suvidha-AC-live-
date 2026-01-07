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
    font-size: 13px;
    font-weight: bold !important;
    text-align: left;
    padding: 7px;
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
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>3 - LIST OF PARTICIPATING POLITICAL PARTIES</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(3, $st_code) == 0){ ?>
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
          <p style="font-size: 15px;"><b>3 - LIST OF PARTICIPATING POLITICAL PARTIES</b></p>
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
  <table class="" style="width: 100%;">
			<thead>
				<tr>
				  <th class="blcs">PARTY TYPE</th>
				  <th class="blcs">ABBREVIATION</th>
				  <th class="blcs">PARTY</th>
				</tr>
			  </thead>
			  <tbody>
					@php $i = 1;  $countN =  $countS = $countSO = $rec = 0; @endphp
			  @foreach($dataArray as $key=>$data)
				@if($key == 'N-N')
					@php $countN = count($data); @endphp
					<tr><td><b>NATIONAL PARTIES</b></td></tr>
				@elseif($key == 'S-U')
				@php $countSO = count($data); @endphp
					<tr><td colspan="3"><b>STATE PARTIES - OTHER STATES</b></td></tr>
				@elseif($key == 'S-S')
				@php $countS = count($data); @endphp
					<tr><td><b>STATE PARTIES</b></td></tr>
				@elseif($key == 'U-U')
				@php $rec = count($data); @endphp
					<tr><td colspan="3"><b>REGISTERED(Unrecognised) PARTIES</b> </td></tr>
				@elseif($key == 'Z-Z')
					<tr><td><b>INDEPENDENTS</b>  </td></tr>
				@endif
				
				  @foreach($data as $raw)
						<tr>
						  <td>{{$i}}.</td>
						  <td>{{$raw['PARTYABBRE']}}</td>
						  <td>{{ucwords(strtolower($raw['PARTYNAME']))}}</td>
						</tr>	
					@php $i++; @endphp
				  @endforeach				  
			  @endforeach	


<tr><td colspan="3"> </td></tr>
<tr><td colspan="3"> </td></tr>
	  
			  </tbody>
          </table>

 <table border="1">
<tbody>
					<tr></tr>
					<tr><th colspan="3" style="text-align:center;">Summary Table</th></tr>
					<tr><th colspan="2">Party Type</th> <th>No. of Parties Participated</th></tr>
					<tr><td colspan="2">NATIONAL PARTIES</td> <td style="text-align:right;">{{$countN}}</td></tr>				
					<tr><td colspan="2">STATE PARTIES</td> <td style="text-align:right;">{{$countS}}</td></tr>
					<tr><td colspan="2">STATE PARTIES - OTHER STATES</td> <td style="text-align:right;">{{$countSO}}</td></tr>
			
					<tr><td colspan="2">REGISTERED(Unrecognised) PARTIES </td> <td style="text-align:right;">{{$rec}}</td></tr>
					<tr><th colspan="2">All Parties (Excluding NOTA and Independents) </th> <th style="text-align:right;">{{$countN + $countSO + $countS + $rec}}</th></tr>





			  
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

<?php  if (verifyreport(3, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>

</div>
</html>