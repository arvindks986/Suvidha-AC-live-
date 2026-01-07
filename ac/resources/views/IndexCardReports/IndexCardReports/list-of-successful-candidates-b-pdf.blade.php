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
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>16 - LIST OF SUCCESSFUL CANDIDATES (B) </strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(16, $st_code) == 0){ ?>
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


    <table><tr><td><p></p></td></tr>
  </table>
  
  <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                        <thead class="">
                            <tr>
                                <th scope="col"></th>
                                <th><b> CONSTITUENCY </b></th>
								<th><b> CATEGORY </b></th>
								<th><b> WINNER </b></th>
								<th><b> SOCIAL CATEGORY </b></th>
								<th><b> PARTY </b></th>
								<th><b> PARTY SYMBOL </b></th>
								<th><b> MARGIN </b></th>
                            </tr>
                        </thead>
                        <tbody>
							<?php $sn = 1; ?>
 
                            @foreach($arraydata as  $catwise)
                            <tr>
                                <td>{{$sn}}</td>
                                <td>{{$catwise->AC_NAME}}</td>
                                <td>{{$catwise->AC_TYPE}}</td>
                                <td>{{$catwise->Cand_Name}}</td>
								<td>{{ucfirst($catwise->cand_category)}}</td>
                                <td>{{$catwise->Party_Abbre}}</td>
                                <td>{{$catwise->Party_symbol}}</td>
                                <td> {{$catwise->margin}} @if($catwise->TotalVote > 0)({{round($catwise->margin/$catwise->TotalVote*100,2)}}%) @endif</td>
                            </tr>
							<?php $sn++; ?>
                            @endforeach
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

<?php  if (verifyreport(16, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>



</div>
</html>