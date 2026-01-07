<html>
  <head>
    <style>


  @page {
            header: page-header;
            footer: page-footer;
        }


    td {
    font-size: 16px !important;
    font-weight: 500 !important;
    text-align: center;
    padding: 8px;
    color: #000;
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
    font-size: 1.9em;
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
    font-size: 19px;
    font-weight: bold !important;
    text-align: left;
    }
  .bolds{
  font-weight: bold;
}
    table{
    width: 100%;
    }
    </style>
  </head>
  <?php  $st=getstatebystatecode($st_code);   ?>
  <div class="bordertestreport">


      <table class="">
        <thead>
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, State Election,{{getElectionYear()}} to the legislative assembly of {{$st->ST_NAME}}
</strong></p></td>
</thead>
            </tr>
             
  </table>

<table class="border">
  <thead>
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>10 - DETAILED RESULTS
</strong></p>
                  </td>
   
              </tr>

</thead>
</table>


  <table class="">
    <thead>
	<?php  if (verifyreport(10, $st_code) == 0){ ?>
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
	  

    </thead>

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
          <p style="font-size: 15px;"><b>10 - DETAILED RESULTS
</b></p>
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
				  <td colspan="7"></td>
				  <td colspan="3" style="text-decoration: underline;text-align: center;" class="bolds">VALID VOTES POLLED </td>
				  <td rowspan="2" class="blc bolds">% VOTES  <br>POLLED</td>
				</tr>
				<tr>
				  <td class="blc bolds"></td>
				  <td class="blc bolds" style="text-align: left;">CANDIDATE NAME </td>
				  <td class="blc bolds">SEX</td>
				  <td class="blc bolds">AGE</td>
				  <td class="blc bolds">CATEGORY</td>
				  <td class="blc bolds">PARTY</td>
				  <td class="blc bolds">SYMBOL</td>
				  <td class="blc bolds">GENERAL</td>
				  <td class="blc bolds">POSTAL</td>
				  <td class="blc bolds">TOTAL</td>
				</tr>
			  </thead>
			  <tbody>
			  
				@foreach($dataArr as $key => $data)
					<tr>
					  <td class="bolds" colspan=""><b>Constituency</b> </td>
					  <td colspan="10">{!! $key !!}</td>
					</tr>
					@php $i =1; $per = 0; $gen_total = $postal_total = $all_total = $total_electors = $total_votes =0;
					@endphp
					
					@foreach($data as $raw)
					
						<?php 
						$gen_total += $raw['general_vote'];
						$postal_total += $raw['postal_vote'];
						$all_total += $raw['cand_total_vote'];
						$total_electors = $raw['total_electors'];
						$total_votes = $raw['total_votes'];
						
						
						if($raw['total_votes'] > 0){
							$per = round((($raw['cand_total_vote']/$raw['total_votes'])*100),2);
						}						
						?>					
						<tr>
						  <td></td>
						  <td style="text-align: left;">{{$i}} {{ucwords(strtolower($raw['cand_name']))}}</td>
						  <td>{{strtoupper($raw['cand_gender'])}}</td>
						  <td>{{$raw['cand_age']}}</td>
						  <td>{{strtoupper($raw['cand_category'])}}</td>
						  <td>{{$raw['party_abbre']}}</td>
						  <td>{{$raw['SYMBOL_DES']}}</td>
						  <td>{{$raw['general_vote']}}</td>
						  <td>{{$raw['postal_vote']}}</td>
						  <td>{{$raw['cand_total_vote']}}</td>
						  <td>{{$per}}</td>
						</tr>
						@php $i++; @endphp
						
					@endforeach
					
					<?php
					if($total_electors > 0){
							$pertotal = round((($total_votes/$total_electors)*100),2);
						}else{
							$pertotal = 0;
						}					
						?>
					<tr>
					  <td colspan="3" style="text-align: left;" class="blcs bolds">TURN OUT</td>
					  <td class="blcs"></td>
					  <td colspan="3" class="blcs bolds">TOTAL:</td>
					  <td class="blcs bolds">{{$gen_total}}</td>
					  <td class="blcs bolds">{{$postal_total}}</td>
					  <td class="blcs bolds">{{$all_total}}</td>
					  <td class="blcs bolds">{{$pertotal}}</td>
					</tr>
					
				@endforeach
				<tr>
				  <td colspan="5" class="blcs bolds" style="text-align: right;">GRAND TOTAL:</td>
				  <td class="blcs" colspan="2"></td>
				  <td class="blcs bolds">{{$all_state_Data[0]->all_state_total - $all_state_Data[0]->all_state_postal}}</td>
				  <td class="blcs bolds">{{$all_state_Data[0]->all_state_postal}}</td>
				  <td class="blcs bolds">{{$all_state_Data[0]->all_state_total}}</td>
				  <td class="blcs bolds"></td>
				</tr>
			  </tbody>
			</table>
 


    <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 10px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>




   <htmlpagefooter name='page-footer'>
 <table>
 <tr>
 
 <td align="right"><span style="float:right;font-size: 10px;">Page {PAGENO}</span></td>
</tr>

<?php  if (verifyreport(10, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>

</div>




</html>