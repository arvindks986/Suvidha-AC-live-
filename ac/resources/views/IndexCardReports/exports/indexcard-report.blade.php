<?php  $st=getstatebystatecode($st_code);   ?>		
			<table>
			<tr><td colspan="6" align="center"><b>Election Commistion of India</b></td> </tr>
			<tr><td colspan="6" align="center"><b>{{getElectionType($st_code,$ac)}} Election,{{getElectionYear()}}</b></td> </tr>
			<tr><td colspan="6" align="center"></td> </tr>
			<tr><td colspan="6" align="center">Assembly Constituency of {{$st->ST_NAME}}, District {{$acinfo->DIST_NAME_EN}}</td> </tr>
			<tr><td colspan="6" align="center">No. and Name of Assembly Constituancy {{$acinfo->AC_NO}} ({{$acinfo->AC_TYPE}}) {{$acinfo->AC_NAME}}</td></tr>
			</table>


	

                                    <table>
									<tbody>
                                        <tr>
                                            <th><b>I</b></th>
                                            <th><b>CANDIDATES</b></th>
                                            <th><b>MALE</b></th>
                                            <th><b>FEMALE</b></th>
                                            <th><b>THIRD GENDER</b></th>
                                            <th><b>TOTAL</b></th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Nominated </td>
                                            <td>{{$getIndexCardDataACWise['c_nom_m_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_f_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_o_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_a_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Nominations  Rejected</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_r_m']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_r_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_r_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_r_a']}}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Withdrawn</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_w_m']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_w_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_w_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_w_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td> Contested </td>
                                            <td>{{$getIndexCardDataACWise['c_nom_co_m']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_co_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_co_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_co_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Deposit Forfeited </td>
                                            <td>{{$getIndexCardDataACWise['c_nom_fd_m']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_fd_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_fd_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['c_nom_fd_t']}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>II</b></th>
                                            <th><b>ELECTORS</b></th>
                                            <th colspan="2"><b>GENERAL</b></th>
                                            <th><b>SERVICE</b></th>
                                            <th><b>TOTAL</b></th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan=""></td>
                                            <td><b>Other than NRIs</b></td>
                                            <td><b>NRIs</b></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Male</td>
                                            <td>{{ $getIndexCardDataACWise['e_gen_m'] }}</td>
                                            <td>{{ $getIndexCardDataACWise['e_nri_m'] }}</td>
                                            <td>{{ $getIndexCardDataACWise['e_ser_m'] }}</td>
                                            <td>{{ $getIndexCardDataACWise['e_all_t_m'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Female</td>
                                            <td>{{$getIndexCardDataACWise['e_gen_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_nri_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_ser_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_all_t_f']}}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Third Gender(Not applicable to Service electors)</td>
                                            <td>{{$getIndexCardDataACWise['e_gen_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_nri_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_ser_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_all_t_o']}}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Total </td>
                                            <td>{{$getIndexCardDataACWise['e_gen_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_nri_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_ser_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['e_all_t']}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>III</b></th>
                                            <th><b>VOTERS TURNED UP FOR VOTING</b></th>
                                            <th colspan="2"><b>GENERAL</b></th>
                                            <th colspan="2" style="text-align:center;"><b>TOTAL</b></th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan=""></td>
                                            <td><b>Other than NRIs</b></td>
                                            <td><b>NRIs</b></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Male</td>
                                            <td>{{ $getIndexCardDataACWise['vt_gen_m'] }}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_m']}}</td>
                                            <td  colspan="2">{{$getIndexCardDataACWise['vt_m_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Female</td>
                                            <td>{{$getIndexCardDataACWise['vt_gen_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_f']}}</td>
                                            <td colspan="2">{{$getIndexCardDataACWise['vt_f_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Third Gender</td>
                                            <td>{{$getIndexCardDataACWise['vt_gen_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_o']}}</td>
                                            <td   colspan="2">{{$getIndexCardDataACWise['vt_o_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Total(Male + Female + Third Gender) </td>
                                            <td>{{$getIndexCardDataACWise['vt_gen_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_t']}}</td>
                                            <td colspan="2" >{{$getIndexCardDataACWise['vt_all_t']}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>IV</b></th>
                                            <th colspan="5"><b>DETAILS OF VOTES POLLED ON EVM</b></th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td colspan="4"> Total votes polled on EVM</td>
                                            <td>{{$getIndexCardDataACWise['t_votes_evm']}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td colspan="4">Test votes under Rule 49 MA</td>
                                            <td>{{$getIndexCardDataACWise['mock_poll_evm']}}</td>
                                        </tr>
                                     
										
										<tr>
										<td>3A</td>
											<td colspan="4">Votes Counted From CU Of EVM</td>
											<td>{{$getIndexCardDataACWise['votes_counted_from_evm']}}</td>
										</tr>
										
										<tr>
										<td>3B</td>
											<td colspan="4">Votes Counted From VVPAT (Whenever Votes Not Retrieved From CU)</td>
											<td>{{$getIndexCardDataACWise['votes_counted_from_vvpat']}}</td>
										</tr>
										
										
										
                                        <tr>
                                            <td>4</td>
                                            <td colspan="4">Votes not counted from CU(s) as per ECI Instructions</td>
                                            <td>{{$getIndexCardDataACWise['r_votes_evm']}}</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td colspan="4">Votes polled for 'NOTA' on EVM</td>
                                            <td>{{$getIndexCardDataACWise['nota_vote_evm']}}</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td colspan="4">Total of test votes + Votes not counted from CU(s) as per ECI Instructions + 'NOTA'[2+4+5]</td>
                                            <td>{{$getIndexCardDataACWise['all_reject_on_evm']}}</td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td colspan="4">Total valid votes counted from EVM [1-6]</td>
                                            <td>{{$getIndexCardDataACWise['v_votes_evm_all']}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>V</b></th>
                                            <th colspan="5"><b>DETAILS OF POSTAL VOTES</b></th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td colspan="4">Postal votes counted for service voters under sub-section (8) of Section 20 of R.P.Act, 1950</td>
                                            <td>{{$getIndexCardDataACWise['postal_vote_ser_u']}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td colspan="4">Postal votes counted for Govt. election duty servants on (including all police personnel, driver, conductors, cleaner) and Absentee Voters.</td>
                                            <td>{{$getIndexCardDataACWise['postal_vote_ser_o']}}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td colspan="4"> Postal votes rejected</td>
                                            <td colspan="1">{{$getIndexCardDataACWise['postal_vote_rejected']}}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td colspan="4">Postal votes polled for 'NOTA'</td>
                                            <td>{{$getIndexCardDataACWise['postal_vote_nota']}}</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td colspan="4">Total of postal votes rejected + NOTA [3+4]</td>
                                            <td>{{$getIndexCardDataACWise['postal_vote_r_nota']}}</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td colspan="4">Total valid postal votes [1+2-5] </td>
                                            <td>{{$getIndexCardDataACWise['postal_valid_votes']}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>VI</b></th>
                                            <th colspan="5"><b>COMBINED DETAILS OF EVM and POSTAL VOTES</b></th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td colspan="4"> Total votes polled [IV(1) + V(1+2)] </td>
                                            <td class="dev">{{$getIndexCardDataACWise['total_votes_polled']}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td colspan="4">Total of test votes + Votes not counted from CU(s) as per ECI Instructions + 'NOTA'[IV(6) + V(5)]</td>
                                            <td>{{$getIndexCardDataACWise['total_not_count_votes']}}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td colspan="4">Total valid votes [IV(7)+ V(6)]</td>
                                            <td>{{$getIndexCardDataACWise['total_valid_votes']}}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td colspan="4"> Total votes polled for 'NOTA'[IV(5) + V(4)]</td>
                                            <td>{{$getIndexCardDataACWise['total_votes_nota']}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>VII</b></th>
                                            <th colspan="5"><b>MISCELLANEOUS</b></th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td colspan="4">Proxy votes</td>
                                            <td colspan="1">{{$getIndexCardDataACWise['proxy_votes']}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td colspan="4">Tendered votes</td>
                                            <td colspan="1">{{$getIndexCardDataACWise['tendered_votes']}}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td colspan="4">Total number of polling station set up in a Constituency</td>
                                            <td colspan="1">{{$getIndexCardDataACWise['total_no_polling_station']}}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td colspan="4">Averages number of Electors assigned to a polling station</td>
                                            <td colspan="1">{{$getIndexCardDataACWise['avg_elec_polling_stn']}}</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td colspan="4">Date(s) Of Poll</td>
                                            <td colspan="1">
                                                {{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_poll']))}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td colspan="4">Date(s) Of Re-Poll,(if any)</td>
                                            <td colspan="1">
                                                @if (trim($getIndexCardDataACWise['date_of_repoll']) != 0 && $getIndexCardDataACWise['date_of_repoll'])
                                                <?php
                                                    $repoll_dates   = explode(',',$getIndexCardDataACWise['date_of_repoll']);
                                                    $dates_array    = [];
                                                foreach($repoll_dates as $res_repoll){
                                                $dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
                                                    }
                                                ?>
                                                {!! implode(', ', $dates_array) !!}
                                                @else{{'NA'}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td colspan="4">Number of Polling stations where Re-poll was ordered(mention date of order also)</td>
                                            <td colspan="1">
                                                @if($getIndexCardDataACWise['dt_poll_reasion'])
                                                {{$getIndexCardDataACWise['dt_poll_reasion']}}
                                                @else
                                                NA
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td colspan="4">Date(s) Of counting</td>
                                            <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_counting']))}}</td>
                                        </tr>
                                        <tr>
                                            <td>9</td>
                                            <td colspan="4">Date Of Declaration Of result</td>
                                            <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_declare']))}}</td>
                                        </tr>
                                        <tr>
                                            <td>10</td>
                                            <td colspan="4">Whether this is Bye election or Countermanded election?</td>
                                            <td class="dev" colspan="1">
                                                @if ($getIndexCardDataACWise['flag_bye_counter'] == 1)
                                                Yes
                                                @else
                                                No
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>11</td>
                                            <td colspan="4">If yes, reason thereof</td>
                                            <td>
                                                @if ($getIndexCardDataACWise['flag_bye_counter_reason'])
                                                {{$getIndexCardDataACWise['flag_bye_counter_reason']}}
                                                @else
                                                NA
                                                @endif
                                            </td>
                                        </tr>
										</tbody>
                                    </table>
                          
                                <table>
                                    <thead>
                                        <tr>
										<th  colspan="1"><b>VIII.</b></th>
										<th  colspan="5"><b>DETAILS OF VOTES POLLED BY EACH CANDIDATE</b></th>
                                        </tr>
                                        <tr>
                                            <th><b>SL. No.</b></th>
                                            <th><b>Name of the Contesting Candidates(in Block Letters)</b></th>
                                            <th><b>Sex(Male/Female/Third Gender)</b></th>
                                            <th><b>Age(Years)</b></th>
                                            <th><b>Category (Gen./SC/ST)</b></th>
                                            <th><b>Full name of the Party</b></th>
                                            <th><b>Election Symbol Alloted</b></th>
                                            <th colspan="3" style="text-align:center;"><b>Valid Votes Polled</b></th>
                                        </tr>
                                        <tr>
                                            <th colspan="7"></th>
                                            <th><b>Counted from EVM</b></th>
                                            <th><b>Postal</b></th>
                                            <th><b>Total</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count=1; ?>
                                        <?php $total_votes = $total_postel_votes = 0; ?>
                                        @foreach($getIndexCardDataCandidatesVotesACWise as $canddata)
                             
                                            <tr>
                                                <td>{{$count."."}} </td>
                                                <td >{{$canddata->cand_name}}</td>
                                                <td style="text-transform:capitalize;">{{$canddata->cand_gender}}</td>
                                                <td>{{$canddata->cand_age}}</td>
                                                <td>{{strtoupper($canddata->cand_category)}}</td>
                                                <td>{{$canddata->PARTYNAME}}</td>
                                                <td>{{$canddata->SYMBOL_DES}}</td>
                                                <td>{{$canddata->total_vote - $canddata->postalballot_vote}}</td>
                                                <td>{{$canddata->postalballot_vote}}</td>                                
                                                <td>{{$canddata->total_vote}}</td>
                                                <?php $total_votes += $canddata->total_vote;
                                                $total_postel_votes += $canddata->postalballot_vote; ?>
                                            </tr>
                                            <?php $count++; ?>
                                            @endforeach
                                            <tr>
                                                <td colspan="7" style="text-align:right"><b>TOTAL</b></td>
                                                <td>{{$total_votes - $total_postel_votes}}</td>
                                                <td>{{$total_postel_votes}}</td>
                                                <td>{{$total_votes}}</td>
                                            </tr>
											
											
											
<tr></tr>	  
<tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="10">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
      </tr>
                                        </tbody>
                                    </table>



        
                              