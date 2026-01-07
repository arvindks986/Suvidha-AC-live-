      <table>
        <thead>
			<tr>
				  <th colspan="5" align="center"><b>8 - CONSTITUENCY DATA - SUMMARY</b></th>
				</tr>
          <tr>
            <th colspan="1"><b>State/UT</b></th> 
            <th colspan="1">{{$val['st_code']}}-{{$val['ST_NAME']}}</th> 
            <th colspan="1"><b> Constituency Name</b></th> 
            <th colspan="1"> {{$val['ac_no']}}-{{$val['AC_NAME'].'-'.$val['ac_type']}}</th> 
		 </tr>
		 
          <tr>
            <th>I. Candidates</th>
            <th></th>
            <th>Men</th>
            <th>Woman</th>
            <th>Third Gender</th>
            <th>Total</th>
          </tr>
        </thead>
		
        <tbody>
          <tr>
            <td></td>
            <td>1. NOMINATION FILED</td>
            <td>{{$val['c_nom_m_t']}}</td>
            <td>{{$val['c_nom_f_t']}}</td>
            <td>{{$val['c_nom_o_t']}}</td>
            <td>{{$val['c_nom_a_t']}}</td>
          </tr>
          <tr>
		   <td></td>
            <td>2. NOMINATION REJECTED</td>
            <td>{{$val['c_nom_r_m']}}</td>
            <td>{{$val['c_nom_r_f']}}</td>
            <td>{{$val['c_nom_r_o']}}</td>
            <td>{{$val['c_nom_r_a']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>3. Withdrawn</td>
            <td>{{$val['c_nom_w_m']}}</td>
            <td>{{$val['c_nom_w_f']}}</td>
            <td>{{$val['c_nom_w_o']}}</td>
            <td>{{$val['c_nom_w_t']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>4. Contested</td>
            <td>{{$val['c_nom_co_m']}}</td>
            <td>{{$val['c_nom_co_f']}}</td>
            <td>{{$val['c_nom_co_o']}}</td>
            <td>{{$val['c_nom_co_t']}}</td>
          </tr>
          <tr>
		  <td></td>

            <td>5. Forfeited Deposit</td>
            <td>{{$val['c_nom_fd_m']}}</td>
            <td>{{$val['c_nom_fd_f']}}</td>
            <td>{{$val['c_nom_fd_o']}}</td>
            <td>{{$val['c_nom_fd_t']}}</td>
          </tr>
          <tr>
            <th colspan="5">II. Electors</th>
          </tr>
        
          <tr>
		  <td></td>
            <td>1. GENERAL<b>(Other than OVERSEAS)</b></td>
            <td>{{$val['gen_m']}}</td>
            <td>{{$val['gen_f']}}</td>
            <td>{{$val['gen_o']}}</td>
            <td>{{$val['gen_t']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>2. Overseas</td>
            <td>{{$val['nri_m']}}</td>
            <td>{{$val['nri_f']}}</td>
            <td>{{$val['nri_o']}}</td>
            <td>{{$val['nri_m']+$val['nri_f']+$val['nri_o']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>3. Service</td>
            <td>{{$val['ser_m']}}</td>
            <td>{{$val['ser_f']}}</td>
            <td>{{$val['ser_o']}}</td>
            <td>{{$val['ser_t']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>4. Total</td>
            <td>{{$val['total_m']}}</td>
            <td>{{$val['total_f']}}</td>
            <td>{{$val['total_o']}}</td>
            <td>{{$val['total_all']}}</td>
          </tr>
          <tr>
            <th colspan="5">III. VOTERS</th>
          </tr>
       
          <tr>
		  <td></td>
            <td>1. GENERAL <b>(Other than OVERSEAS)</b></td>
            <td>{{$val['vt_gen_m']}}</td>
            <td>{{$val['vt_gen_f']}}</td>
            <td>{{$val['vt_gen_o']}}</td>
            <td>{{$val['vt_gen_t']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>2. Overseas</td>
            <td>{{$val['vt_nri_m']}}</td>
            <td>{{$val['vt_nri_f']}}</td>
            <td>{{$val['vt_nri_o']}}</td>
            <td>{{$val['vt_nri_t']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>3. PROXY ( Already included in III.1 General )</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$val['proxy_votes']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>4. Postal</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$val['postal_votes']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td>5. Total</td>
            <td>{{$val['vt_gen_m']+$val['vt_nri_m']}}</td>
            <td>{{$val['vt_gen_f']+$val['vt_nri_f']}}</td>
            <td>{{$val['vt_gen_o']+$val['vt_nri_o']}}</td>
            <td>{{$val['total_votes']}}</td>
          </tr>
          <tr>
            <td colspan="5">III. Polling Percentage</td>
            <?php if($val['total_all'] > 0) { ?>
            <td>{{round($val['total_votes']/$val['total_all']*100,2)}}</td>
            <?php } else { ?>
            <td>0</td>
            <?php } ?>
          </tr>
          <tr>
            <th colspan="5"> IV. Votes</th>
          </tr>
          <tr>
		  <td></td>
            <td colspan="4">1. Total Votes Polled On EVM</td>
            <td >{{$val['evm_votes']+$val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote']+$val['test_votes_49_ma']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td colspan="4">2. TOTAL DEDUCTED VOTES FROM EVM(TEST <br>
              VOTES + VOTES REJECTED <br>
            DUE TO OTHER REASONS + 'NOTA')</td>
            <td >{{$val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote']}}</td>
          </tr>
          <tr>
		  <td></td>
            <td colspan="4">3.Total valid votes polled on evm
              <td>{{$val['evm_votes']+$val['test_votes_49_ma']}}</td>
            </tr>
            <tr>
			<td></td>
              <td colspan="4">4. Postal Votes Counted</td>
              <td>{{$val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'] }}</td>
            </tr>
            <tr>
			<td></td>
              <td colspan="4">5. POSTAL VOTES DEDUCTED(REJECTED POSTAL
              VOTES + POSTAL VOTES POLLED FOR 'NOTA') </td>
              <td>{{$val['rej_votes_postal']+$val['nota_postal_vote']}}</td>
            </tr>
            <tr>
			<td></td>
			  <td colspan="4">6. Valid Postal Votes</td>
			  <td>{{$val['postal_votes']-($val['rej_votes_postal']+$val['nota_postal_vote'])}}</td>
			</tr>
			<tr>
			<td></td>
				<td colspan="4">7. Total Valid Votes Polled</td>
				<td>{{($val['evm_votes']+$val['postal_votes']+$val['test_votes_49_ma'])-($val['rej_votes_postal']+$val['nota_postal_vote'])}}</td>
			</tr>
            <tr>
			<td></td>
              <td colspan="4">8. Test Votes polled On EVM</td>
              <td>{{$val['test_votes_49_ma']}}</td>
            </tr>
            <tr>
			<td></td>
              <td colspan="4">9.  VOTES POLLED FOR 'NOTA' (INCLUDING POSTAL)</td>
              <td>{{$val['nota_evm_vote']+$val['nota_postal_vote']}}</td>
            </tr>
            <tr>
			<td></td>
              <td colspan="4">10. Tendered Votes</td>
              <td>{{$val['tended_votes']}}</td>
            </tr>
            <tr>
              <th colspan="5" style="border-top: 1px solid #000;"> V. Polling Stations</th>
            </tr>
            <tr>
			<td></td>
              <td colspan="2">Number</td>
              <td>{{$val['total_polling_station_s_i_t_c']}}</td>
              <td>Average Electors Per Polling Station</td>
              <?php if($val['total_polling_station_s_i_t_c'] > 0) { ?>
              <td>{{round($val['total_all']/$val['total_polling_station_s_i_t_c'],0)}}</td>
              <?php } else { ?>
              <td>0</td>
              <?php } ?>
            </tr>
            <tr>
			<td></td>
              <td colspan="4">Date(s) of Re-poll, If Any:</td>
              <td>{{$val['date_of_repoll']}}</td>
            </tr>
            <tr>
			<td></td>
              <td colspan="4">Number Of Polling Stations where Re-Polls Was Ordered</td>
              <td>{{$val['no_poll_station_where_repoll']}}</td>
            </tr>
            <tr>
              <th colspan="5" style="border-top: 1px solid #000;">VI. Dates</th>
            </tr>
            <tr>
			<td></td>
              <th colspan="2">Polling</th>
              <th colspan="2">Counting</th>
              <th colspan="1">Declaration Of Result</th>
            </tr>
            <tr>
			<td></td>
              <td colspan="2">{{$val['DATE_POLL']}}</td>
              <td colspan="2">{{$val['DATE_COUNT']}}</td>
              <td colspan="1">{{$val['result_declared_date']}}</td>
            </tr>
            <tr>
              <th colspan="5" style="border-top: 1px solid #000;">VII. Result</th>
            </tr>
            <tr>
			<td></td>
              <th colspan="2"></th>
              <th>Party</th>
              <th>Candidate</th>
              <th>Votes</th>
            </tr>
            <tr>
			<td></td>
              <td colspan="2">Winner</td>
              <td>{{$val['lead_cand_party']}}</td>
              <td>{{$val['lead_cand_name']}}</td>
              <td>{{$val['lead_total_vote']}}</td>
            </tr>
            <tr>
			<td></td>
              <td colspan="2">Runner-Up</td>
              <td>{{$val['trail_cand_party']}}</td>
              <td>{{$val['trail_cand_name']}}</td>
              <td>{{$val['trail_total_vote']}}</td>
            </tr>
            <tr>
				<td></td>
                  <td colspan="2">Margin</td>
                  <td>{{$val['margin']}}</td>
                  <td>(  @if(($val['total_votes']) > 0)  {{round($val['margin']/($val['total_votes'])*100,2)}} @else 0 @endif % of Total Votes)</td>
                  <td></td>
              </tr>
			  
			  
																  <tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="10">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
		
      </tr>  
			  
          </tbody>
        </table>

