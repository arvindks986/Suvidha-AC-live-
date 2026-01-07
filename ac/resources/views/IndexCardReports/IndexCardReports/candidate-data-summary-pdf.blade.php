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
    padding: 5.2px;
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
    .bolds{
     
      font-weight: bold;
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
    .bold{
		 padding: 12px 0px 0px 30px;
      font-weight: bold;
    }
    th {
    font-size: 13px;
    font-weight: bold !important;
    text-align: left;
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
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong> 9 - CANDIDATE DATA SUMMARY
</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(9, $st_code) == 0){ ?>
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




  <!--   <table class="border">
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
          <p style="font-size: 15px;"><b>9 - Candidate Data Summary</b></p>
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
  <table class="table" style="width: 100%;">
      <thead>
        <tr>
          <th style="border-top: 1px solid #000;"></th>
          <th colspan="3" style="border-top: 1px solid #000;text-decoration: underline; text-align: center;">TYPE OF CONSTITUENCY</th>
          <th style="border-top: 1px solid #000;"></th>
        </tr>
        <tr>
          <th class="blc"></th>
          <th class="blc">GEN</th>
          <th class="blc">SC</th>
          <th class="blc">ST</th>
          <th class="blc">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bolds">1. NO. OF CONSTITUENCIES </td>
          <td>{{isset($acdataarray['GEN']['seats'])?$acdataarray['GEN']['seats']:0}}</td>
          <td>{{isset($acdataarray['SC']['seats'])?$acdataarray['SC']['seats']:0}}</td>
          <td>{{isset($acdataarray['ST']['seats'])?$acdataarray['ST']['seats']:0}}</td>
          <td>{{(isset($acdataarray['GEN']['seats'])?$acdataarray['GEN']['seats']:0) + (isset($acdataarray['SC']['seats'])?$acdataarray['SC']['seats']:0) + (isset($acdataarray['ST']['seats'])?$acdataarray['ST']['seats']:0)}}</td>
        </tr>
		
        <tr>
          <td class="bolds">2. &nbsp;NOMINATIONS FILED</td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['nom_male'])?$candatawise['GEN']['nom_male']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_male'])?$candatawise['SC']['nom_male']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_male'])?$candatawise['ST']['nom_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_male'])?$candatawise['GEN']['nom_male']:0) + (isset($candatawise['SC']['nom_male'])?$candatawise['SC']['nom_male']:0) + (isset($candatawise['ST']['nom_male'])?$candatawise['ST']['nom_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['nom_female'])?$candatawise['GEN']['nom_female']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_female'])?$candatawise['SC']['nom_female']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_female'])?$candatawise['ST']['nom_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_female'])?$candatawise['GEN']['nom_female']:0) + (isset($candatawise['SC']['nom_female'])?$candatawise['SC']['nom_female']:0) + (isset($candatawise['ST']['nom_female'])?$candatawise['ST']['nom_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['nom_third'])?$candatawise['GEN']['nom_third']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_third'])?$candatawise['SC']['nom_third']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_third'])?$candatawise['ST']['nom_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_third'])?$candatawise['GEN']['nom_third']:0) + (isset($candatawise['SC']['nom_third'])?$candatawise['SC']['nom_third']:0) + (isset($candatawise['ST']['nom_third'])?$candatawise['ST']['nom_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['nom_total'])?$candatawise['GEN']['nom_total']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_total'])?$candatawise['SC']['nom_total']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_total'])?$candatawise['ST']['nom_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_total'])?$candatawise['GEN']['nom_total']:0) + (isset($candatawise['SC']['nom_total'])?$candatawise['SC']['nom_total']:0) + (isset($candatawise['ST']['nom_total'])?$candatawise['ST']['nom_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">3.&nbsp; NOMINATIONS REJECTED
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['rej_male'])?$candatawise['GEN']['rej_male']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_male'])?$candatawise['SC']['rej_male']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_male'])?$candatawise['ST']['rej_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_male'])?$candatawise['GEN']['rej_male']:0) + (isset($candatawise['SC']['rej_male'])?$candatawise['SC']['rej_male']:0) + (isset($candatawise['ST']['rej_male'])?$candatawise['ST']['rej_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['rej_female'])?$candatawise['GEN']['rej_female']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_female'])?$candatawise['SC']['rej_female']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_female'])?$candatawise['ST']['rej_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_female'])?$candatawise['GEN']['rej_female']:0) + (isset($candatawise['SC']['rej_female'])?$candatawise['SC']['rej_female']:0) + (isset($candatawise['ST']['rej_female'])?$candatawise['ST']['rej_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['rej_third'])?$candatawise['GEN']['rej_third']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_third'])?$candatawise['SC']['rej_third']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_third'])?$candatawise['ST']['rej_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_third'])?$candatawise['GEN']['rej_third']:0) + (isset($candatawise['SC']['rej_third'])?$candatawise['SC']['rej_third']:0) + (isset($candatawise['ST']['rej_third'])?$candatawise['ST']['rej_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['rej_total'])?$candatawise['GEN']['rej_total']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_total'])?$candatawise['SC']['rej_total']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_total'])?$candatawise['ST']['rej_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_total'])?$candatawise['GEN']['rej_total']:0) + (isset($candatawise['SC']['rej_total'])?$candatawise['SC']['rej_total']:0) + (isset($candatawise['ST']['rej_total'])?$candatawise['ST']['rej_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">4.&nbsp; NOMINATIONS WITHDRAWN
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['with_male'])?$candatawise['GEN']['with_male']:0}}</td>
          <td>{{isset($candatawise['SC']['with_male'])?$candatawise['SC']['with_male']:0}}</td>
          <td>{{isset($candatawise['ST']['with_male'])?$candatawise['ST']['with_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_male'])?$candatawise['GEN']['with_male']:0) + (isset($candatawise['SC']['with_male'])?$candatawise['SC']['with_male']:0) + (isset($candatawise['ST']['with_male'])?$candatawise['ST']['with_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['with_female'])?$candatawise['GEN']['with_female']:0}}</td>
          <td>{{isset($candatawise['SC']['with_female'])?$candatawise['SC']['with_female']:0}}</td>
          <td>{{isset($candatawise['ST']['with_female'])?$candatawise['ST']['with_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_female'])?$candatawise['GEN']['with_female']:0) + (isset($candatawise['SC']['with_female'])?$candatawise['SC']['with_female']:0) + (isset($candatawise['ST']['with_female'])?$candatawise['ST']['with_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['with_third'])?$candatawise['GEN']['with_third']:0}}</td>
          <td>{{isset($candatawise['SC']['with_third'])?$candatawise['SC']['with_third']:0}}</td>
          <td>{{isset($candatawise['ST']['with_third'])?$candatawise['ST']['with_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_third'])?$candatawise['GEN']['with_third']:0) + (isset($candatawise['SC']['with_third'])?$candatawise['SC']['with_third']:0) + (isset($candatawise['ST']['with_third'])?$candatawise['ST']['with_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['with_total'])?$candatawise['GEN']['with_total']:0}}</td>
          <td>{{isset($candatawise['SC']['with_total'])?$candatawise['SC']['with_total']:0}}</td>
          <td>{{isset($candatawise['ST']['with_total'])?$candatawise['ST']['with_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_total'])?$candatawise['GEN']['with_total']:0) + (isset($candatawise['SC']['with_total'])?$candatawise['SC']['with_total']:0) + (isset($candatawise['ST']['with_total'])?$candatawise['ST']['with_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">5. &nbsp;CONTESTING CANDIDATES
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['cont_male'])?$candatawise['GEN']['cont_male']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_male'])?$candatawise['SC']['cont_male']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_male'])?$candatawise['ST']['cont_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_male'])?$candatawise['GEN']['cont_male']:0) + (isset($candatawise['SC']['cont_male'])?$candatawise['SC']['cont_male']:0) + (isset($candatawise['ST']['cont_male'])?$candatawise['ST']['cont_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['cont_female'])?$candatawise['GEN']['cont_female']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_female'])?$candatawise['SC']['cont_female']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_female'])?$candatawise['ST']['cont_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_female'])?$candatawise['GEN']['cont_female']:0) + (isset($candatawise['SC']['cont_female'])?$candatawise['SC']['cont_female']:0) + (isset($candatawise['ST']['cont_female'])?$candatawise['ST']['cont_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['cont_third'])?$candatawise['GEN']['cont_third']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_third'])?$candatawise['SC']['cont_third']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_third'])?$candatawise['ST']['cont_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_third'])?$candatawise['GEN']['cont_third']:0) + (isset($candatawise['SC']['cont_third'])?$candatawise['SC']['cont_third']:0) + (isset($candatawise['ST']['cont_third'])?$candatawise['ST']['cont_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['cont_total'])?$candatawise['GEN']['cont_total']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_total'])?$candatawise['SC']['cont_total']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_total'])?$candatawise['ST']['cont_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_total'])?$candatawise['GEN']['cont_total']:0) + (isset($candatawise['SC']['cont_total'])?$candatawise['SC']['cont_total']:0) + (isset($candatawise['ST']['cont_total'])?$candatawise['ST']['cont_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">6.&nbsp; FORFEITED DEPOSITS
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($dfdataarray['GEN']['male'])?$dfdataarray['GEN']['male']:0}}</td>
          <td>{{isset($dfdataarray['SC']['male'])?$dfdataarray['SC']['male']:0}}</td>
          <td>{{isset($dfdataarray['ST']['male'])?$dfdataarray['ST']['male']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['male'])?$dfdataarray['GEN']['male']:0) + (isset($dfdataarray['SC']['male'])?$dfdataarray['SC']['male']:0) + (isset($dfdataarray['ST']['male'])?$dfdataarray['ST']['male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($dfdataarray['GEN']['female'])?$dfdataarray['GEN']['female']:0}}</td>
          <td>{{isset($dfdataarray['SC']['female'])?$dfdataarray['SC']['female']:0}}</td>
          <td>{{isset($dfdataarray['ST']['female'])?$dfdataarray['ST']['female']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['female'])?$dfdataarray['GEN']['female']:0) + (isset($dfdataarray['SC']['female'])?$dfdataarray['SC']['female']:0) + (isset($dfdataarray['ST']['female'])?$dfdataarray['ST']['female']:0)}}</td>
        </tr>
        <tr>
		  <td class="bold">c. Third Gender</td>
          <td>{{isset($dfdataarray['GEN']['third'])?$dfdataarray['GEN']['third']:0}}</td>
          <td>{{isset($dfdataarray['SC']['third'])?$dfdataarray['SC']['third']:0}}</td>
          <td>{{isset($dfdataarray['ST']['third'])?$dfdataarray['ST']['third']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['third'])?$dfdataarray['GEN']['third']:0) + (isset($dfdataarray['SC']['third'])?$dfdataarray['SC']['third']:0) + (isset($dfdataarray['ST']['third'])?$dfdataarray['ST']['third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($dfdataarray['GEN']['total'])?$dfdataarray['GEN']['total']:0}}</td>
          <td>{{isset($dfdataarray['SC']['total'])?$dfdataarray['SC']['total']:0}}</td>
          <td>{{isset($dfdataarray['ST']['total'])?$dfdataarray['ST']['total']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['total'])?$dfdataarray['GEN']['total']:0) + (isset($dfdataarray['SC']['total'])?$dfdataarray['SC']['total']:0) + (isset($dfdataarray['ST']['total'])?$dfdataarray['ST']['total']:0)}}</td>
        </tr>
      </tbody>
    </table>
 
   <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
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

<?php  if (verifyreport(9, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>
  
</html>