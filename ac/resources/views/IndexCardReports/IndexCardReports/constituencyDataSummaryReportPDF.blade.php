<?php  $st=getstatebystatecode($st_code);   ?>
<html>
  <head>
    <style>
      

  @page {
            header: page-header;
            footer: page-footer;
        }


      p{
        
      }
      .notbl{
        font-weight: normal;
      }
    td {
    font-size: 10px !important;
    font-weight: bold;
    text-align: left;
    padding: 2px;
    text-transform: uppercase;
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
    h3{
    font-size: 16px !important;
    font-weight: 600;
    }
    .bolds{
    font-weight: bold;
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
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
    text-align: left;
    text-transform: uppercase;
    font-size: 11px;
    border-collapse: collapse;
    font-weight: bold !important;
    }
    table{
    width: 100%;
    }
    </style>
  </head>
  <div class="bordertestreport">



          <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, State Election,{{getElectionYear()}} to the legislative assembly of {{$st->ST_NAME}}
</strong></p></td>
            </tr>
             
  </table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;padding: 7px 0px;"><strong>8 - CONSTITUENCY DATA - SUMMARY
</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(8, $st_code) == 0){ ?>
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
          <p style="float: right;width: 100%;font-size: 13px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
            </b>
          <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
        </td>
      </tr>
    </table>
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p style="font-size: 12px;"><b>CONSTITUENCY DATA - SUMMARY</b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 12px;"><strong>State :</strong>{{$st->ST_NAME}}</p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;font-size: 12px;"><b>User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 12px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table> -->
    @foreach($finalArraynew as $key => $value)
    @foreach($value as  $val)
    <div class="table-responsive">
      <table id="" class="" style="width:100%;">
        <thead>
          <tr>
            <th class="blcs" colspan="5"> <p class="" style="font-size: 12px;"><b>Constituency</b> : <span> {{$val['ac_no']}}</span>-<span>   {{$val['AC_NAME'].' '.$val['ac_type']}}</span></p></th>
<!--             <td class="blcs" style="text-align: right;font-size: 12px;"><b>  {{$val['ac_no']}}</b></td>
 -->          </tr>
          <tr>
            <th>I. Candidates</th>
            <th style="width: 10%">Men</th>
            <th>Woman</th>
            <th>Third Gender</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1. NOMINATION FILED</td>
            <td class="notbl">{{$val['c_nom_m_t']}}</td>
            <td class="notbl">{{$val['c_nom_f_t']}}</td>
            <td class="notbl">{{$val['c_nom_o_t']}}</td>
            <td class="notbl">{{$val['c_nom_a_t']}}</td>
          </tr>
          <tr>
            <td>2. NOMINATION REJECTED</td>
            <td class="notbl">{{$val['c_nom_r_m']}}</td>
            <td class="notbl">{{$val['c_nom_r_f']}}</td>
            <td class="notbl">{{$val['c_nom_r_o']}}</td>
            <td class="notbl">{{$val['c_nom_r_a']}}</td>
          </tr>
          <tr>
            <td>3. Withdrawn</td>
            <td class="notbl">{{$val['c_nom_w_m']}}</td>
            <td class="notbl">{{$val['c_nom_w_f']}}</td>
            <td class="notbl">{{$val['c_nom_w_o']}}</td>
            <td class="notbl">{{$val['c_nom_w_t']}}</td>
          </tr>
          <tr>
            <td>4. Contested</td>
            <td class="notbl">{{$val['c_nom_co_m']}}</td>
            <td class="notbl">{{$val['c_nom_co_f']}}</td>
            <td class="notbl">{{$val['c_nom_co_o']}}</td>
            <td class="notbl">{{$val['c_nom_co_t']}}</td>
          </tr>
          <tr>
            <td>5.Forfeited Deposit</td>
            <td class="notbl">{{$val['c_nom_fd_m']}}</td>
            <td class="notbl">{{$val['c_nom_fd_f']}}</td>
            <td class="notbl">{{$val['c_nom_fd_o']}}</td>
            <td class="notbl">{{$val['c_nom_fd_t']}}</td>
          </tr>
          <tr>
            <th colspan="5" style="border-top: 1px solid #000;">II. Electors</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1. GENERAL<b>(Other than OVERSEAS)</b></td>
            <td class="notbl">{{$val['gen_m']}}</td>
            <td class="notbl">{{$val['gen_f']}}</td>
            <td class="notbl">{{$val['gen_o']}}</td>
            <td class="notbl">{{$val['gen_t']}}</td>
          </tr>
          <tr>
            <td>2. Overseas</td>
            <td class="notbl">{{$val['nri_m']}}</td>
            <td class="notbl">{{$val['nri_f']}}</td>
            <td class="notbl">{{$val['nri_o']}}</td>
            <td class="notbl">{{$val['nri_m']+$val['nri_f']+$val['nri_o']}}</td>
          </tr>
          <tr>
            <td>3. Service</td>
            <td class="notbl">{{$val['ser_m']}}</td>
            <td class="notbl">{{$val['ser_f']}}</td>
            <td class="notbl">{{$val['ser_o']}}</td>
            <td class="notbl">{{$val['ser_t']}}</td>
          </tr>
          <tr>
            <td>4. Total</td>
            <td class="notbl">{{$val['total_m']}}</td>
            <td class="notbl">{{$val['total_f']}}</td>
            <td class="notbl">{{$val['total_o']}}</td>
            <td class="notbl">{{$val['total_all']}}</td>
          </tr>
          <tr>
            <th style="border-top: 1px solid #000;" colspan="5">III. VOTERS</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1. GENERAL <span style="font-weight: normal;text-transform: capitalize;"><b>(Other than OVERSEAS)</b></span></td>
            <td class="notbl">{{$val['vt_gen_m']}}</td>
            <td class="notbl">{{$val['vt_gen_f']}}</td>
            <td class="notbl">{{$val['vt_gen_o']}}</td>
            <td class="notbl">{{$val['vt_gen_t']}}</td>
          </tr>
          <tr>
            <td>2. Overseas</td>
            <td class="notbl">{{$val['vt_nri_m']}}</td>
            <td class="notbl">{{$val['vt_nri_f']}}</td>
            <td class="notbl">{{$val['vt_nri_o']}}</td>
            <td class="notbl">{{$val['vt_nri_t']}}</td>
          </tr>
          <tr>
            <td>3. PROXY <span style="font-weight: bold;text-transform: capitalize;">( Already included in III.1 General )</span> </td>
            <td></td>
            <td></td>
            <td></td>
            <td class="notbl">{{$val['proxy_votes']}}</td>
          </tr>
          <tr>
            <td>4. Postal</td>
            <td></td>
            <td></td>
            <td></td>
            <td class="notbl">{{$val['postal_votes']}}</td>
          </tr>
          <tr>
            <td>5. Total</td>
            <td class="notbl">{{$val['vt_gen_m']+$val['vt_nri_m']}}</td>
            <td class="notbl">{{$val['vt_gen_f']+$val['vt_nri_f']}}</td>
            <td class="notbl">{{$val['vt_gen_o']+$val['vt_nri_o']}}</td>
            <td class="notbl">{{$val['total_votes']}}</td>
          </tr>
          <!-- new -->
          <!--   <tr>
            <td>6. Votes Rejected Due to Other Reason</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$val['rejected_votes_due_2_other_reason']}}</td>
          </tr>
          -->
          <tr>
            <td style="border-top: 1px solid #000;" colspan="4">III(a). Polling Percentage</td>
            <?php if($val['total_all'] > 0) { ?>
            <td style="border-top: 1px solid #000;" class="notbl">{{round($val['total_votes']/$val['total_all']*100,2)}}</td>
            <?php } else { ?>
            <td style="border-top: 1px solid #000;" class="notbl">0</td>
            <?php } ?>
          </tr>
          <!-- new -->
          <tr>
            <th colspan="5" style=""> IV. Votes</th>
          </tr>
          <tr>
            <td colspan="4">1. Total Votes Polled On EVM</td>
            <td  class="notbl">{{$val['evm_votes']+$val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote']+$val['test_votes_49_ma']}}</td>
          </tr>
          <tr>
            <td colspan="4">2. TOTAL DEDUCTED VOTES FROM EVM(TEST <br>
              VOTES + VOTES REJECTED <br>
            DUE TO OTHER REASONS + 'NOTA')</td>
            <td  class="notbl">{{$val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote']}}</td>
          </tr>
          <tr>
            <td colspan="4">3.Total valid votes polled on evm
              <td class="notbl">{{$val['evm_votes']+$val['test_votes_49_ma']}}</td>
            </tr>
            <tr>
              <td colspan="4">4. Postal Votes Counted</td>
              <td class="notbl">{{$val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'] }}</td>
            </tr>
            <tr>
              <td colspan="4">5. POSTAL VOTES DEDUCTED(REJECTED POSTAL
              VOTES + POSTAL VOTES POLLED FOR 'NOTA') </td>
              <td class="notbl">{{$val['rej_votes_postal']+$val['nota_postal_vote']}}</td>
            </tr>
            <tr>
			  <td colspan="4">6. Valid Postal Votes</td>
			  <td class="notbl">{{$val['postal_votes']-($val['rej_votes_postal']+$val['nota_postal_vote'])}}</td>
			</tr>
			<tr>
				<td colspan="4">7. Total Valid Votes Polled</td>
				<td class="notbl">{{($val['evm_votes']+$val['postal_votes']+$val['test_votes_49_ma'])-($val['rej_votes_postal']+$val['nota_postal_vote'])}}</td>
			</tr>
            <tr>
              <td colspan="4">8. Test Votes polled On EVM</td>
              <td class="notbl">{{$val['test_votes_49_ma']}}</td>
            </tr>
            <!-- new2 -->
            <tr>
              <td colspan="4">9.  VOTES POLLED FOR 'NOTA' (INCLUDING POSTAL)</td>
              <td class="notbl">{{$val['nota_evm_vote']+$val['nota_postal_vote']}}</td>
            </tr>
            <!-- new2 -->
            <tr>
              <td colspan="4">10. Tendered Votes</td>
              <td class="notbl">{{$val['tended_votes']}}</td>
            </tr>
            <tr>
              <th colspan="5" style="border-top: 1px solid #000;"> V. Polling Stations</th>
            </tr>
            <tr>
              <td colspan="2">Number</td>
              <td class="notbl">{{$val['total_polling_station_s_i_t_c']}}</td>
              <td>Average Electors Per Polling Station</td>
              <?php if($val['total_polling_station_s_i_t_c'] > 0) { ?>
              <td class="notbl">{{round($val['total_all']/$val['total_polling_station_s_i_t_c'],0)}}</td>
              <?php } else { ?>
              <td class="notbl">0</td>
              <?php } ?>
            </tr>
            <tr>
              <td colspan="4">Date(s) of Re-poll, If Any:</td>
              <td class="notbl">{{$val['date_of_repoll']}}</td>
            </tr>
            <tr>
              <td colspan="4">Number Of Polling Stations where Re-Polls Was Ordered</td>
              <td class="notbl">{{$val['no_poll_station_where_repoll']}}</td>
            </tr>
            <tr>
              <th colspan="5" style="border-top: 1px solid #000;">VI. Dates</th>
            </tr>
            <tr>
              <th colspan="2">Polling</th>
              <th colspan="2">Counting</th>
              <th colspan="1">Declaration Of Result</th>
            </tr>
            <tr>
              <td colspan="2" class="notbl">{{$val['DATE_POLL']}}</td>
              <td colspan="2" class="notbl">{{$val['DATE_COUNT']}}</td>
              <td colspan="1" class="notbl">{{$val['result_declared_date']}}</td>
            </tr>
            <tr>
              <th colspan="5" style="border-top: 1px solid #000;">VII. Result</th>
            </tr>
            <tr>
              <th colspan="2"></th>
              <th>Party</th>
              <th>Candidate</th>
              <th>Votes</th>
            </tr>
            <tr>
              <td colspan="2">Winner</td>
              <td class="notbl">{{$val['lead_cand_party']}}</td>
              <td class="notbl">{{$val['lead_cand_name']}}</td>
              <td class="notbl">{{$val['lead_total_vote']}}</td>
            </tr>
            <tr>
              <td colspan="2">Runner-Up</td>
              <td class="notbl">{{$val['trail_cand_party']}}</td>
              <td class="notbl">{{$val['trail_cand_name']}}</td>
              <td class="notbl">{{$val['trail_total_vote']}}</td>
            </tr>
            <tr>
                  <td colspan="2">Margin</td>
                  <td class="notbl">{{$val['margin']}}</td>
                  <td  class="notbl">(  @if(($val['total_votes']) > 0)  {{round($val['margin']/($val['total_votes'])*100,2)}} @else 0 @endif % of Total Votes)</td>
                  <td></td>
              </tr>
          </tbody>
        </table>



      </div>
      <div style="page-break-after: always;"></div>
      @endforeach
	  
	  
	  
      @endforeach
    </div>



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

<?php  if (verifyreport(8, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>

  </html>