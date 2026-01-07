<style>
td {
font-size: 10.7px !important;
font-weight: 500 !important;
color: #4a4646 !important;
padding: 3px;
font-family: "Times New Roman", Times, serif;
}
tr{
border-bottom: 1px solid #000 !important;
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
.bordertestreport{
border:1px solid #000;
}
.borders{
    border-top: 1px solid #000;
}
.border{
  border-bottom: 1px solid #000;
}
th {
color: #000 !important;
text-align: center;
font-size: 11px;
font-weight: bold !important;
}
table{
width: 100%;
}
</style>
<div class="bordertestreport">

<?php  $st=getstatebystatecode($st_code);   ?>



	<?php  if (verifyreport_index($st_code,$ac) == 0){ ?>

    <table class="border">
        <tr>
            <td style="text-align: left;">
                <p> <img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
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
            <td><p style="font-size: 16px;"><b>Index Card Assembly - {{getElectionYear()}}</b></p></td>
			
			@if(($getIndexCardDataACWise['finalize_by_ceo']== 0) || (@Auth::user()->designation == 'ECIINDEX'))
            <td style="text-align: right;"><p style="font-size: 16px;"><b>Draft
			@endif
        </tr>
		 <tr>
                <td style="text-align: left;"><b style="font-size: 13px; ">User</b>:
                    @if(@Auth::user()->designation == 'ECIINDEX')
                        ECI
                     @elseif(@Auth::user()->designation == 'ECI') 
                      ECI
                    @else {{@Auth::user()->officername}}
                    @endif</td>


        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 13px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
  </tr>
    </table>
	
	<?php  }   ?>
	
	<table>
			<tr>
			<td>&nbsp;</td>
			<td style="text-align:center;">
			    <b>ELECTION INDEX CARD<br/>FOR LEGISLATIVE ASSEMBLY ELECTIONS ONLY</b>
			</td>
			<td style="text-align: right;">
			  {{getElectionType($st_code,$ac)}}&nbsp;ELECTION
			</td>
			</tr>
			  <tr>
					<td><strong>State:</strong> {{$st->ST_NAME}}</td>
				   <td style="text-align:center;"><b>District:</b> {{$acinfo->DIST_NAME_EN}}</td>
				   <td style="text-align:right;"><b>Year of Election:</b> {{getElectionYear()}}</td>
				</tr>
				 <tr>
					<td colspan="2"><b>Number & Name of Assembly Constituency: </b>{{$acinfo->AC_NO}} : {{$acinfo->AC_NAME}} </td>
					<td style="text-align:right;"><b>Type of Constituency:</b> {{$acinfo->AC_TYPE}}</td>
				</tr>
	</table>
	
</div>
<div class="card-body">
    <div class="wapper">
        <div class="grids">
            <div class="whole">
                <!--Start row-->
                <!--Basic Information-->
                <!--Start row-->
                <div class="row" id="index_cus_ch">
                    <!--tabs starts-->
                    <div class="tab-content" style="overflow: auto;">
                        <div id="menu1" role="tabpanel" class="">
                            <div class="col-md-12 col-sm-12 col-xs-12 tab_card">
                                <div class="table-responsive">
                                    <table class="table table-bordered tableindexcard" style="width: 100%;">
                                        <tr>
                                            <th>I</th>
                                            <th>CANDIDATES</th>
                                            <th>MALE</th>
                                            <th>FEMALE</th>
                                            <th>THIRD GENDER</th>
                                            <th>TOTAL</th>
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
                                            <th>II</th>
                                            <th>ELECTORS</th>
                                            <th colspan="2">GENERAL</th>
                                            <th>SERVICE</th>
                                            <th>TOTAL</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan=""></td>
                                            <td>Other than NRIs</td>
                                            <td>NRIs</td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Male</td>
                                            <td>{{ $getIndexCardDataACWise['e_gen_m'] }}</td>
                                            <td>{{ $getIndexCardDataACWise['e_nri_m'] }}</td>
                                            <td>{{ $getIndexCardDataACWise['e_ser_m'] }}</td>
                                            <td>{{ $getIndexCardDataACWise['e_all_t_m'] }}</td>
                                        </tr>
                                        <tr data-parent="e" data-category="nri">
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
                                            <th>III</th>
                                            <th>VOTERS TURNED UP FOR VOTING</th>
                                            <th colspan="2">GENERAL</th>
                                            <th colspan="2" style="text-align:center;">TOTAL</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan=""></td>
                                            <td>Other than NRIs</td>
                                            <td>NRIs</td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Male</td>
                                            <td>{{ $getIndexCardDataACWise['vt_gen_m'] }}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_m']}}</td>
                                            <td  colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_m_t']}}</td>
                                        </tr>
                                        <tr data-parent="e" data-category="nri">
                                            <td>2</td>
                                            <td>Female</td>
                                            <td>{{$getIndexCardDataACWise['vt_gen_f']}}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_f']}}</td>
                                            <td   colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_f_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Third Gender</td>
                                            <td>{{$getIndexCardDataACWise['vt_gen_o']}}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_o']}}</td>
                                            <td   colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_o_t']}}</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Total(Male + Female + Third Gender) </td>
                                            <td>{{$getIndexCardDataACWise['vt_gen_t']}}</td>
                                            <td>{{$getIndexCardDataACWise['vt_nri_t']}}</td>
                                            <td colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_all_t']}}</td>
                                        </tr>
                                        <tr>
                                            <th>IV</th>
                                            <th colspan="5">DETAILS OF VOTES POLLED ON EVM</th>
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
                                       <!-- <tr>
                                            <td>3</td>
                                            <td colspan="4"> Votes not retrieved from EVM</td>
                                            <td>{{$getIndexCardDataACWise['not_retrieved_vote_evm']}}</td>
                                        </tr>-->
										
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
                                            <td colspan="4">Total of test votes + Votes not counted from CU(s) as per ECI Instruction + 'NOTA'[2+4+5]</td>
                                            <td>{{$getIndexCardDataACWise['all_reject_on_evm']}}</td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td colspan="4">Total valid votes counted from EVM [1-6]</span></td>
                                            <td>{{$getIndexCardDataACWise['v_votes_evm_all']}}</td>
                                        </tr>
                                        <tr>
                                            <th>V</th>
                                            <th colspan="5">DETAILS OF POSTAL VOTES</th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td colspan="4">Postal votes counted for service voters under sub-section (8) of Section 20 of R.P.Act, 1950</span></td>
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
                                            <td class="dev" colspan="1">{{$getIndexCardDataACWise['postal_vote_rejected']}}</td>
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
                                            <th>VI</th>
                                            <th colspan="5">COMBINED DETAILS OF EVM & POSTAL VOTES</th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td colspan="4"> Total votes polled [IV(1) + V(1+2)] </td>
                                            <td class="dev">{{$getIndexCardDataACWise['total_votes_polled']}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td colspan="4">Total of test votes + Votes not counted from CU(s) as per ECI Instruction + 'NOTA'[IV(6) + V(5)]</td>
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
                                            <th>VII</th>
                                            <th colspan="5">MISCELLANEOUS</th>
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
                                                @if($uncontested == '1')
														NA
													@else
													 {{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_poll']))}}
													@endif
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
                                            <td colspan="1">@if($uncontested == '1')
													NA
												@else
													{{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_counting']))}}
												@endif
												</td>
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
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- menu1 -->
                        <div id="menu2" class="tab-pane fade">
<!--                             <h4 style="text-align: center;">Information About Candidates in AC</h4>
 -->                            <h4 style="text-align: center;"> VIII. DETAILS OF VOTES POLLED BY EACH CANDIDATE</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL. No.</th>
                                            <th>Name of the <br> Contesting Candidates <br/>(in Block Letters)</th>
                                            <th>Sex <br>(Male/Female/<br>Third Gender)</th>
                                            <th>Age(Years)</th>
                                            <th>Category <br>(Gen./SC/ST)</th>
                                            <th>Full name of <br> the Party</th>
                                            <th>Election Symbol <br>Alloted</th>
                                            <th colspan="3" style="text-align:center;">Valid Votes Polled</th>
                                        </tr>
                                        <tr>
                                            <th colspan="7"></th>
                                            <th>Counted from EVM</th>
                                            <th>Postal</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody color="CandidateBodyIDWise">
                                        <?php $count=1; ?>
                                        <?php $total_votes = $total_postel_votes = 0; ?>
                                        @foreach($getIndexCardDataCandidatesVotesACWise as $canddata)
                                        <?php //echo "<pre>"; print_r($canddata); die; ?>
                                            <tr>
                                                <td>{{$count."."}} </td>
                                                <td >{{$canddata->cand_name}}</td>
                                                <td style="text-transform:capitalize;">{{$canddata->cand_gender}}</td>
                                                <td>{{$canddata->cand_age}}</td>
                                                <td>{{strtoupper($canddata->cand_category)}}</td>
                                                <td>{{$canddata->PARTYNAME}}</td>
                                                <td>{{$canddata->SYMBOL_DES}}</td>
												@if($uncontested == '1')
												<td>NA</td>
												<td>NA</td>
												<td>NA</td>
												@else
                                                <td>{{$canddata->total_vote - $canddata->postalballot_vote}}</td>
                                                <td>{{$canddata->postalballot_vote}}</td>                                
                                                <td>{{$canddata->total_vote}}</td>
												@endif
                                                <?php $total_votes += $canddata->total_vote;
                                                $total_postel_votes += $canddata->postalballot_vote; ?>
                                            </tr>
                                            <?php $count++; ?>
                                            @endforeach
                                            <tr>
                                                <td colspan="7" style="text-align:right"><b>TOTAL</b></td>
												@if($uncontested == '1')
												<td>NA</td>
												<td>NA</td>
												<td>NA</td>
												@else
                                                <td>{{$total_votes - $total_postel_votes}}</td>
                                                <td>{{$total_postel_votes}}</td>
                                                <td>{{$total_votes}}</td>											
												@endif				
												
                                            </tr>
                                        </tbody>
                                    </table>

<?php  if ($uncontested == '1'){ ?>

<p style="font-size: 12px;">The Election in this AC - {{$ac}} {{$acinfo->AC_NAME}} ({{$acinfo->AC_TYPE}}) was uncontested.</p>  
<?php  }?>


<?php  if (verifyreport_index($st_code,$ac) == 0){ ?>
     <p style="font-size: 12px;">&nbsp;<span class="font-weight-bold">Note :</span> Arrange serially contesting candidates in descending order of valid votes polled. </p>    
<p class="" style="font-size: 12px;position: relative;margin-bottom: 13px;">&nbsp;Certified that the Election Index Card has been checked with Forms 3A, 4, 7A, 20 and 21C or 21D or 21E and R.O.'s &nbsp;Reports etc. and that there is no discrepancy.<br>&nbsp;Further it is certified that the Party Affiliations and symbols alloted have been verified from the list of contesting &nbsp;candidates in Forms 7 A.</p>                       



                    <table class="table">
                        <tr>
                            <td class="borders" style="border-right: 1px solid #000;border-left: 1px solid #000;width: 38%;">Date : </td>
                            <td class="borders" style="border-right: 1px solid #000;width: 31%;">Date of Press Note: {{date('d-m-Y', strtotime($getIndexCardDataACWise['DT_PRESS_ANNC']))}} </td>
                            <td class="borders" style="border-right: 1px solid #000;width: 31%;">Signature : </td>
                        </tr>
                     
                        <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr> 
                         <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr> 
                         <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>

                        <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>
                         <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>
                         <tr>
                            <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="border-right: 1px solid #000;"></td>
                        </tr>



                            <tr>
                                <td class="border" style="border-right: 1px solid #000;border-left: 1px solid #000;">(Signature & Seal) Cheif Electoral Officer</td>
                                <td class="border" style="border-right: 1px solid #000;">Date of Notification: {{date('d-m-Y', strtotime($getIndexCardDataACWise['DT_ISS_NOM']))}}</td>
                                <td class="border" style="border-right: 1px solid #000;">(Seal)Returning Officer</td>
                            </tr>
                    </table>       
                                </div>
                            </div>
                            <!-- menu3 -->
                        </div>
                    </div>
					
<?php } ?>			
					   <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 10px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>
					
					
                    <!--tabs ends-->
                    <div class="row">
                        <div class="col-sm-10 col-offset-sm-1 pull-right">
                        </div>
                    </div>
                </div>
            </div>
            </div> <!-- end grids -->
        </div>
        <!-- End Wrapper-->
    </div>
</div>
</div>
</div>
</section>