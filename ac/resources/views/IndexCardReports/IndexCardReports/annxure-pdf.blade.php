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
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>ANNXURE - 1 (ELECTORS DATA SUMMARY)</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(11, $st_code) == 0){ ?>
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
          <p style="font-size: 15px;"><b>ANNXURE - 1 (ELECTORS DATA SUMMARY )</b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong>{{$st->ST_NAME}}</p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
 -->

<table>
  <tr><td><p></p></td></tr>
</table>



    <table class="table" style="width: 100%;">
      <thead>
        <tr>
          <th rowspan="2" class="top blc"></th>
          <th colspan="3" class="bolds top" style="border-bottom: 1px solid #000;text-align: center;">TYPE OF CONSTITUENCY</th>
          <th class="top"></th>
        </tr>
        <tr>
          <td class="bolds blc">GEN</td>
          <td class="bolds blc">SC</td>
          <td class="bolds blc">ST</td>
          <td class="bolds blc">TOTAL</td>
        </tr>
      </thead>
          <tbody>
        <tr>
          <td class="bold">1. NO. OF CONSTITUENCIES
          </td>
          <td>{{isset($actypecountNew['GEN']['genac']) ? $actypecountNew['GEN']['genac'] : 0 }}</td>
         
          <td>{{isset($actypecountNew['SC']['scac']) ? $actypecountNew['SC']['scac']:0}}</td>
         
          <td>{{isset($actypecountNew['ST']['stac']) ? $actypecountNew['ST']['stac'] : 0}}</td>
          <td>{{(isset($actypecountNew['GEN']['genac']) ? $actypecountNew['GEN']['genac'] : 0)+ (isset($actypecountNew['SC']['scac'])? $actypecountNew['SC']['scac'] :0 )
          + (isset($actypecountNew['ST']['stac'])?$actypecountNew['ST']['stac']:0)}}</td>
        </tr>
        <tr>
          <td colspan="4" class="bold">2. POSTAL VOTES</td>
        </tr>
        <tr>
          <td class="boldn">a. Postal Votes(For Service Voters <br> Under sub-Section(8) of Section 20 of <br> R.P. Act,1950)
          </td>
          <td>{{isset($postalvoteNew['GEN']['postalvotesec8'])?$postalvoteNew['GEN']['postalvotesec8'] : 0}}</td>
          <td>{{isset($postalvoteNew['SC']['postalvotesec8'])? $postalvoteNew['SC']['postalvotesec8']:0}}</td>
          <td>{{isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8'] : 0}}</td>
          <td>{{(isset($postalvoteNew['GEN']['postalvotesec8'])? $postalvoteNew['GEN']['postalvotesec8']:0)+(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8']:0) +(isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8']:0)}}</td>
        </tr>
        <tr><td><p></p></td></tr>
        <tr>
          <td class="boldn">b. Postal Votes(For Govt. Servants <br> on election duty(including all Police <br>Pesonnel, drivers, conductors, <br> cleaners) and Absentee Voters.
          </td>
          <td>{{isset($postalvoteNew['GEN']['postalvoteservice']) ? $postalvoteNew['GEN']['postalvoteservice']: 0}}</td>
          <td>{{isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] : 0}}</td>
          <td>{{isset($postalvoteNew['ST']['postalvoteservice']) ? $postalvoteNew['ST']['postalvoteservice'] : 0}}</td>
          <td>{{(isset($postalvoteNew['GEN']['postalvoteservice']) ?$postalvoteNew['GEN']['postalvoteservice'] :0) +(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice']:0)+(isset($postalvoteNew['ST']['postalvoteservice'])? $postalvoteNew['ST']['postalvoteservice']:0)}}</td>
        </tr>
        <tr>
          <td class="blcs">TOTAL POSTAL VOTES</td>
          <td class="blcs">{{(isset($postalvoteNew['GEN']['postalvotesec8']) ?$postalvoteNew['GEN']['postalvotesec8']:0) +(isset($postalvoteNew['GEN']['postalvoteservice']) ? $postalvoteNew['GEN']['postalvoteservice'] :0)}}</td>
          <td class="blcs">{{(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8']:0)+(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] :0) }}</td>
          <td class="blcs">{{(isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8'] :0)+(isset($postalvoteNew['ST']['postalvoteservice'])? $postalvoteNew['ST']['postalvoteservice'] :0) }}</td>
          <td class="blcs">{{(isset($postalvoteNew['GEN']['postalvotesec8']) ? $postalvoteNew['GEN']['postalvotesec8'] :0) +(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8'] : 0) + (isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8']:0) +(isset($postalvoteNew['GEN']['postalvoteservice'])? $postalvoteNew['GEN']['postalvoteservice']:0)+(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] : 0)+ (isset($postalvoteNew['ST']['postalvoteservice'])?$postalvoteNew['ST']['postalvoteservice']:0) }}</td>
        </tr>
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
 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>

<?php  if (verifyreport(11, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>



</html>