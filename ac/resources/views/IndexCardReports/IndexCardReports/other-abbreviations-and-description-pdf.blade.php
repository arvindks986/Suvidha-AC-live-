<html>
  <head>
    <style>

      @page {
            header: page-header;
            footer: page-footer;
        }


    .devl td {
    font-size: 13px !important;
    font-weight: 500 !important;
    padding: 6px 100px;
    font-family: "Times New Roman", Times, serif;
    } 
   td {
    font-size: 13px !important;
    font-weight: 500 !important;
    padding: 6px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
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
    .blcs{
    font-size: 15px;
    font-weight: bold !important;
    text-align: center;
    padding: 7px;
    }
	
	th{
    text-align: left;
	    font-weight: normal;
    }
    .table td{
    
    }
    table{
    width: 100%;
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
  <tr><td style="text-align:center;">
                        <p style="text-align:center; font-size: 18px;"><b>1 - OTHER ABBREVIATIONS AND DESCRIPTIONS </b></p>
                  </td>
              </tr>

</table>
<br>



    <table class="border">
    <?php  if (verifyreport(1, $st_code) == 0){ ?>
      <tr>
	  
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
		<td><p style="font-size: 12px;font-weight: bold;">Draft</p></td>
      </tr> 
	  <?php } ?>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
  <table class="table devl" style="width: 100%;">
    <thead>
      <tr>
        <th class="blcs">ABBREVIATIONS </th>
        <th class="blcs">DESCRIPTIONS</th>
      </tr>
    </thead>
    <tbody>
              <tr>
                <td>FD</td>
                <td>Forfeited Deposits</td>
              </tr>
              <tr>
                <td>GEN</td>
                <td>General Constituency
                </td>
              </tr>
              <tr>
                <td>SC</td>
                <td>Scheduled Castes
                </td>
              </tr>
              <tr>
                <td>ST</td>
                <td>Scheduled Tribes
                </td>
              </tr>
              <tr>
                <td>M</td>
                <td>Male</td>
              </tr>
              <tr>
                <td>F</td>
                <td>Female</td>
              </tr>
              <tr>
                <td>TG</td>
                <td>Third Gender</td>
              </tr>
              <tr>
                <td>T</td>
                <td>Total</td>
              </tr>
              <tr>
                <td>N</td>
                <td>National Party</td>
              </tr>
              <tr>
                <td>S</td>
                <td>State Party</td>
              </tr>
              <tr>
                <td>U</td>
                <td>Registered (Unrecognised) Party</td>
              </tr>
              <tr>
                <td>IND</td>
                <td>Independent</td>
              </tr>
			  
			  
			<tr>
                <td>NOTA</td>
                <td>None of the Above</td>
              </tr>
			<tr>
                <td>AC</td>
                <td>Assembly Constituency</td>
              </tr>

			<tr>
                <td>PC</td>
                <td>Parliamentary Constituency</td>
              </tr>
			<tr>
                <td>PS</td>
                <td>Polling Stations</td>
              </tr>			  
			  
			<tr>
                <td>VTR</td>
                <td>Voters Turnout Rate</td>
              </tr>	


			<tr>
                <th colspan="2">Type of AC mentioned as SC and ST indicates AC is reserved for SC & ST respectively.</th>
              </tr>	

			<tr>
                <th colspan="2"><u><b>Formulas Used in Report for Calculation of Some Important Indicators: </b></u></th>
              </tr>	

				<tr>
                <th colspan="2"><b>1. VTR / Poll Participation % = (No. of Votes Polled)*100 / (Total No of Electors)</b> <br> Specific Value of Denominator & Numerator may be used to calculate the value of indicator at Specific Level/Group.</th>
              </tr>		
			  
			  <tr>
                <th colspan="2"><b>2. Valid Votes Polled = Total Votes Polled - [Rejected Votes including Test Votes + NOTA Votes]</b> </th>
              </tr>	
			  
			  <tr>
                <th colspan="2"><b>3. Average No. of Electors Per Polling Station = Total No. of Electors / No. of Polling Station</b> </th>
              </tr>	
		
      <tr><td colspan="2"><p style="border-top: 1px solid #000;"></p></td></tr>
    </tbody>
  </table>
 



 <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>
 
 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>
 
</div>

<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
 
</tr>

<?php  if (verifyreport(1, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>

</table>
 </htmlpagefooter>



</html>