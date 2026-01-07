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
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>15 - Constituency wise detailed Result</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(15, $st_code) == 0){ ?>
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



                @foreach($dataArr as $key1 => $raw)
				
				<p class="contituency" style="font-size: 13px;"><b>Constituency:</b> <span> {!!$key1!!}</span></p>
				

                <table id="example" class="table borders" style="width:100%;">
                    <thead>
                        <tr>
                            <th rowspan="2" class="blc">SL NO</th>
                            <th rowspan="2" class="blc">CANDIDATE <br> NAME</th>
                            <th rowspan="2" class="blc">SEX</th>
                            <th rowspan="2" class="blc">AGE</th>
                            <th rowspan="2" class="blc">CATEGORY</th>
                            <th rowspan="2" class="blc">PARTY</th>
                            <th rowspan="2" class="blc">Symbol</th>
                           <th style="text-decoration: underline;" colspan="3">Votes Secured</th>
                           <th style="text-decoration: underline;" colspan="2">% of votes secured</th>
                        </tr>


                        <tr>
                             <th class="blc">GENERAL</th>
                            <th class="blc">POSTAL</th>
                            <th class="blc">TOTAL</th>
 <th class="blc">Over total elctors in constituency</th>
                            <th class="blc">Over total votes polled in constituency</th>

                        </tr>


                    </thead>
                    <tbody><?php $count=1;$totalgeneral_vote=0;$totalpostal_vote=0;$grandtotal=0; $totalelectorspercent =0; $grandelector=0; $grandpolled=0; ?>
                        @foreach($raw as $row)
                 <?php
				$electors = $row['total_electors'];
				 $totalvotespolled = $row['total_votes'];

                  
                  $totalelectorPercent = ($electors!=0)?((($row['general_vote']+$row['postal_vote'])/$electors)*100):0;
                  $grandelector+=$totalelectorPercent;


                 $totalvotespolled=($totalvotespolled!=0)?((($row['general_vote']+$row['postal_vote'])/$totalvotespolled)*100):0;
                 $grandpolled+=$totalvotespolled;

                 ?>
                        <tr>
                            <td>{{$count}}</td>
                            <td style="text-transform: capitalize;">{{$row['cand_name']}}</td>
                            <td style="text-transform: capitalize;">{{ucfirst($row['cand_gender'])}}</td>
                            <td>{{$row['cand_age']}}</td>
                            <td>{{ucfirst($row['cand_category'])}}</td>
                            <td>{{$row['party_abbre']}}</td>
                            <td>{{$row['SYMBOL_DES']}}</td>
                            <td>{{$row['general_vote']}}</td>
                            <td>{{$row['postal_vote']}}</td>
                            <td>{{$row['general_vote']+$row['postal_vote']}}</td>
                            <td>{{round($totalelectorPercent,2)}}</td>
                            <td>{{round($totalvotespolled,2)}}</td>
                            
                        </tr>
						<?php $totalgeneral_vote+=$row['general_vote'];
						$totalpostal_vote+=$row['postal_vote'];
						$grandtotal+=$row['general_vote']+$row['postal_vote'];
						$count++;?>
                     
                        @endforeach
                        <tr>
                           <td colspan="5" class="blcs"></td>
                            <td colspan="2" class="blcs"><b>TOTAL</b></td>                          
                            <td class="blcs"><b>{{$totalgeneral_vote}}</b></td>
                            <td class="blcs"><b>{{$totalpostal_vote}}</b></td>
                            <td class="blcs"><b>{{$grandtotal}}</b></td>
                            <td class="blcs"><b>{{round($grandelector,2)}}</b></td>
                            <td class="blcs"><b>{{round($grandpolled,2)}}</b></td>
                        </tr>
                    </tbody>

                </table>

			<div style="page-break-after: always;"></div>

               
                @endforeach
          
             
		<!--		
                <table class="table table-bordered">
        <tr>
                           
                  
                            <td colspan="5" style="width: 42%;" class="blc"></td>
              <td style="width: 12%;" colspan="2" class="blc">STATE TOTAL:</td>
                            <td style="width: 10%;" class="blc"><b>{{$all_Data[0]->all_evm}}</b></td>
                            <td style="width: 10%; " class="blc"><b>{{$all_Data[0]->all_postal}}</b></td>
                            <td style="" class="blc"><b>{{$all_Data[0]->all_total}}</b></td>
                           
                        </tr>
        </table>
   
   -->
   
   
   <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 12px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>

 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>

  </div>
   <htmlpagefooter name='page-footer'>
 <table>
 
<tr>

<?php  if (verifyreport(15, $st_code) != 0){ ?>


	<td align="left"><span style="font-size:8px; ">{{getreportsequence(7777, $st_code)}}</span></td>


<?php } ?>

 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>


</table>
 </htmlpagefooter>



</html>