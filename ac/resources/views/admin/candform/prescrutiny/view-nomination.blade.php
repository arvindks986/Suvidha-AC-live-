@extends('admin.layouts.ac.theme')
@section('title', 'Pre Scrutiny')
@section('content')
<link rel="stylesheet" href="{{ asset('theme/css/dark_custom.css')}}" />
<style type="text/css">
  .fullwidth {
    width: 100%;
    float: left;
  }

  .button-next {
    margin-top: 30px;
  }

  .button-next button {
    float: right;
  }

  .affidavit-preview {
    min-height: 600px;
  }
</style>
<main role="main" class="inner cover mt-3 mb-3">
  <div class="container-fluid">
    <div class="row">
    <div class="{{($prescrutiny_status == '1') ? 'col-md-12' : 'col-md-8'}} col-12 pl-0">
      <section>
        <div class="container py-2">
          <div class="d-flex align-items-center justify-content-between">
            <div><a href="{{url('/roac/listallapplicant_prescrutiny')}}" class="btn btn-primary">Back</a></div>
              @if(isset($reference_id) && isset($href_download_application))
              <div>
               <ul class="d-inline-flex align-items-center list-unstyled mb-0 p-0">
                 <li class="pr-3"><a href="{!! $href_download_application !!}" target="_blank" class="text-dark"> Reference ID: <b style="text-decoration: underline;">{{$reference_id}}</b> <i class="fa fa-download" aria-hidden="true"></i></a></li>  
               </ul> 
              </div>
             
              @endif
              @if($prescrutiny_status == '1')
                <div class="text-success"  style="font-size:15px;"> Pre Scrutiny Cleared </div>
              @elseif($prescrutiny_status == '2')
                <div class="text-danger" style="font-size:15px;"> Pre Scrutiny Marked With Defect </div>
              @else
              
              @endif
            
          </div>
          <div class="mt-4">*Select and Highlignt the text in the nomination form below to Mark Defects</div>
        </div>
      </section>
    <section>
   <div id="main_body_to_show" class="card card-shadow">
     <div class="card-body p-1">	
      <div class="prnt-wrp">
        <div class="sub-scroll">
         <div class="nomin-frm border-four  p-2">
          <h6 class="text-center">FORM 2B</h6> 
            <h6 class="text-center">(See rule 4)</h6>
            <h6 class="text-center">NOMINATION</h6> 
          <h6 class="text-center pt-2 pb-3 border-three">Election to the Legislative Assembly of <span class="nominationvalue"><b>({{$st_name}})</b></span></h6>
          @if($recognized_party == '1')
                        <div class="nomination-parts box recognized">
                          <div class="d-flex align-items-center justify-content-between">
                              <div class="img-area">
                                <img src="{!! $qr_code !!}" class="rounded img-thumbnail">
                              </div>
                            
                              <div class="img-area">
                                <img src="{!! $profileimg !!}" class="rounded img-thumbnail">
                              </div>
                            </div>

                            <div class="nomination-form-heading text-center border-one">
                              <strong field_part='1' class="part_search">PART I</strong><br>
                              (To be used by candidate set up by recognised political party)
                            </div>

                            <div field_part='I' class="nomination-detail">
                              <p style="font-size: 15px;">I nominate as a candidate for election to the Legislative
                                Assembly from the
                                <input type="text" name="legislative_assembly" value="{{$legislative_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?>" class="highLighter" width="8%"> Assembly Constituency.

                                Candidate's name <input type="text" name="name" value="{{$name}}" class="highLighter"> Father's/mother's/husband's name <input type="text" name="father_name" value="{!! $father_name !!}" class="highLighter">

                                His postal address <input type="text" name="address" value="{!! $address !!}" class="highLighter">

                                His name is entered at S.No <input type="text" name="serial_no" value="{{$serial_no}}" class="highLighter">

                                in Serial No <input type="text" name="part_no" value="{{$part_no}}" class="highLighter">

                                of the electoral roll for
                                <input type="text" name="resident_ac_no" value="{{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($profile_data->state, $resident_ac_no); ?>" class="highLighter">
                                Assembly constituency.<br><br>

                                <!-- Recognised Party Proposer Detail -->
                                My name is <input type="text" name="proposer_name" value="{{$proposer_name}}" class="highLighter">

                                and it is entered at S.No <input type="text" name="proposer_serial_no" value="{{$proposer_serial_no}}" class="highLighter">

                                in Serial No <input type="text" name="proposer_part_no" value="{{$proposer_part_no}}" class="highLighter">

                                of the electoral roll for
                                <input type="text" name="proposer_assembly" value="{{$proposer_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $proposer_assembly); ?>" class="highLighter">
                                 Assembly constituency.</p>
                            </div>
                            <div class="nomination-signature">
                              <span class="nomination-date left">Date
                                <input type="text" name="apply_date" value="{{$apply_date}}" >
                              </span>
                            </div>
                          </div>
                          @else
                          <div class="nomination-parts box not-recognized">
                            <div class="d-flex align-items-center justify-content-between">
                              <div class="img-area">
                                <img src="{!! $qr_code !!}" class="rounded img-thumbnail">
                              </div>
                            
                              <div class="img-area">
                                <img src="{!! $profileimg !!}" class="rounded img-thumbnail">
                              </div>
                            </div>
                            <div class="nomination-form-heading border-one">
                              <strong >PART II</strong><br>
                              (To be used by candidate NOT set up by recognised political party)
                            </div>
                            <div class="nomination-detail" field_part='II'>
                              <div style="font-size: 15px;">We hereby nominate as candidate for election to the
                                Legislative Assembly from the <input type="text" name="legislative_assembly" value="{{$legislative_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?>" class="highLighter" style="width: 150px;"> Assembly
                                Constituency. Candidate's name <input type="text"  name="name" value="{{$name}}" class="highLighter"> Father's/mother's/husband's name <input type="text" name="father_name" value="{!! $father_name !!}" class="highLighter" >

                                His postal address <input type="text" name="postal_add" value="{!! $address !!}" class="highLighter" style="width: 425px;">

                                His name is entered at S.No <input type="text" name="serial_no" value="{{$serial_no}}" class="highLighter" style="width: 50px;">

                                in Serial No <input type="text" name="part_no" value="{{$part_no}}" class="highLighter" style="width: 50px;">

                                of the electoral roll for <input type="text" name="resident_ac_no" value="{{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($profile_data->state, $resident_ac_no); ?>" class="highLighter" style="width: 80px;">
                                Assembly constituency.<br/><br/>

                                <div class="nomination-signature">
                                  <span class="nomination-date left">Date
                                    <input type="text" name="apply_date" value="{{$apply_date}}" style="width: 95px;">
                                  </span>
                                </div>
                              <p class="pt-4">
                                We declare that we are electors of this Assembly constituency and our names are
                                entered in the electoral roll for this Assembly constituency as indicated below and we
                                append our signatures below in token of subscribing to this nomination: -
                              </p>  
                              </div>

                              <div class="table-heading" style="text-align:center;margin-top:20px;">
                                <h6>Particulars of the proposers and their signatures<h6>
                              </div>
                              <table class="table table-bordered proposers-table">
                                <thead>
                                  <tr>
                                    <th>Sr No.</th>
                                    <th colspan="2">Elector Roll No. of Proposer</th>
                                    <th>Full Name</th>
                                    <th>Signature</th>
                                    <th>Date</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>&nbsp;</td>
                                    <td>Part No. of Electoral Roll</td>
                                    <td>S.No. in that part</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                  </tr>
                                  <?php $i=1;
                                  foreach($non_recognized_proposers as $iterate_proposer){ ?>
                                  <tr class="non_recognized_proposers_row">
                                    <!--<td>{{$iterate_proposer['s_no']}}</td>-->
                                    <td>{{$i}}</td>
                                    <td><input type="text" name="non_recognized_proposers['part_no']" value="@if($iterate_proposer['part_no']!=0){{$iterate_proposer['part_no']}}@endif" class="highLighter" >
                                    </td>
                                    <td><input type="text" name="non_recognized_proposers['serial_no']" value="@if($iterate_proposer['serial_no']!=0){{$iterate_proposer['serial_no']}}@endif" class="highLighter">
                                    </td>
                                    <td><input type="text" name="non_recognized_proposers['fullname']" value="{{$iterate_proposer['fullname']}}" class="highLighter" style="width: 150px;"></td>
                                    <td><input type="text" name="non_recognized_proposers['signature']" value="{{$iterate_proposer['signature']}}" class="highLighter"></td>
                                    <td><input type="text" name="non_recognized_proposers['details']" value="{{ ($iterate_proposer['part_no']!=0 || 
                                      $iterate_proposer['serial_no']!=0 || $iterate_proposer['fullname']!=0) ? $iterate_proposer['date'] : ''}}" class="highLighter" style="width: 100px;">
                                    </td>
                                  </tr>
                                  <?php $i++; } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                          @endif

                          <div class="nomination-parts">
                            <div class="nomination-form-heading border-one">
                              <strong>PART III</strong>
                            </div>
                            
                            <div class="nomination-detail" field_part='III'>
                            <div>
                              <p style="font-size: 14px;">I, the candidate mentioned in Part I/Part II (Strike out which
                                is not applicable) assent to this nomination and hereby declare—</p>
                              <ul style="font-size: 14px;">
                                <li>(a) that I am a citizen of India and have not acquired the citizenship of any
                                  foreign State/country.</li>
                                <li>(b) that I have completed <input type="text" name="age" value="{{$age}}" class="highLighter" style="width: 35px;"> years of age; <br>
  
                                  [STRIKE OUT c(i) or c(ii) BELOW WHICHEVER IS NOT APPLICABLE]</li>
  
                                <div class="nomination-options strikeout">
  
                                  @if($recognized_party == '1')
                                  <div class="checkbox recognised" style="">
                                    <label>(c) (i) that I am set up at this election by the <input type="text" name="party_id" value="{{$party_id}}" class="highLighter"> party,
                                      which is recognised National Party/State Party in this State and that the symbol
                                      reserved for the above party be allotted to me.</label>
                                  </div>
  
  
                                  @else
                                  <div class="checkbox not-recognized">
                                    <label>(c) (ii) that I am set up at this election by the <input type="text" name="party_id" value="{{$party_id}}" class="highLighter">
                                      party, which is a registered-unrecognised political party/that I am contesting
                                      this election as an independent candidate. (Strike out which is not applicable)
                                      and that the symbols I have chosen, in order of preference, are:— <br>
                                      (i) <input type="text" name="suggest_symbol_1" value="{{$suggest_symbol_1}}" class="highLighter"> (ii) <input type="text" name="suggest_symbol_2" value="{{$suggest_symbol_2}}" class="highLighter"> (iii)
                                      <b><input type="text" name="suggest_symbol_3" value="{{$suggest_symbol_3}}" class="highLighter"></b></label>
                                  </div>
                                  @endif
  
                                </div>
  
                                <li>(d) that my name and my father's/mother's/husband's name have been correctly spelt
                                  out above in <input type="text" name="language" value="{{$language}}" class="highLighter" style="width: 100px;"></li>
                                <li>(e) that to the best of my knowledge and belief, I am qualified and not also
                                  disqualified for being chosen to fill the seat in the House of the People.</li>
                              </ul>
                            </div>
  
                            <div class="nomination-detail">
                              <p style="font-size: 14px;">*I further declare that I am a member of the
                                <input type="text" name="category" value="{{$category}}" class="highLighter" style="width: 125px;">
  
                                I also declare that I have not been, and shall not be nominated as a candidate at the
                                present general election/the bye-elections being held simultaneously, to the House of
                                the People from more than two Parliamentary Constituencies. </p>
                            </div>
  
                            <div class="nomination-signature">
                              <span class="nomination-date left">Date <input type="text" name="part3_date" value="{{$part3_date}}" class="highLighter" style="width: 95px;"></span>
                            </div>
  
                            <div class="nomination-note py-3">
                              <p>
                              *Score out the words "assembly constituency comprised within" in the case of Jammu and
                              Kashmir, Andaman and Nicobar Islands, Chandigarh, Dadra and Nagar Haveli, Daman and Diu
                              and Lakshadweep.</p>
  
                             <p> *Score out this paragraph, if not applicable.</p>
  
                              <p>**Score out the words not applicable. N.B.—A "recognised political party" means a
                              political party recognised by the Election Commission under the Election Symbols
                              (Reservation and Allotment) Order, 1968 in the State concerned.</p>
                            </div>
                          </div>

                          <div class="nomination-parts">
                            <div class="nomination-form-heading border-one">
                              <strong>PART IIIA </strong><br>
                              (To be filled by the candidate)
                            </div>
                            
                            <div class="nomination-detail" field_part='IIIA'>
                            <div>
                              <div class="criminal-section">
                                <p style="font-size: 14px;">(1) Whether the candidate— </p>
                                <ul style="font-size: 14px;">
                                  <li>(i) has been convicted— <ul>
                                      <li>(a) of any offense(s) under sub-section (1); or</li>
                                      <li>(b) for contravention of any law specified in sub-section (2), of section 8 of
                                        the Representation of the People Act, 1951 (43 of 1951); or -</li>
                                    </ul>
                                  </li>
                                  <li>(ii) has been convicted for any other offense(s) for which he has been sentenced
                                    to imprisonment for two years or more. <input type="text" name="have_police_case" value="{{$have_police_case}}" class="highLighter" style="width: 65px;"></li>
                                </ul>
                              </div>
  
                              <!-- Police Case -->
                              @if($have_police_case == 'yes')
                              <div class="criminal-section have_police_case_div field_wrapper">
  
                                <p>If the answer is "Yes", the candidate shall furnish the following information:</p>
  
                                <?php $i = 1; ?>
                                <div class="fullwidth have_police_case_record">
  
                                  <table>
                                    <tbody class="police_case_body">
                                      @foreach($police_cases as $iterate_police_case)
  
                                      <tr>
                                        <td>
                                          <h3>Case {{$i}}</h3>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                          <ul>
                                            <li>(i) Case/first information report No./Nos -
                                            <input type="text" name="{{$i.'-'."iterate_police_case['case_no']"}}" value="{{$iterate_police_case['case_no']}}" class="highLighter"  style="width: 65px;"></li>
                                            <li>(ii) Police station(s) -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['police_station']"}}" value="{{$iterate_police_case['police_station']}}" class="highLighter"  style="width: 65px;"><br>
                                              State(s) -
                                            <input type="text" name="{{$i.'-'."iterate_police_case['st_code']"}}" value="<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($iterate_police_case['st_code']); ?>" class="highLighter"  style="width: 65px;">
                                              District(s) -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['case_dist_no']"}}" value="{{$iterate_police_case['case_dist_no']}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getDist($iterate_police_case['st_code'], $iterate_police_case['case_dist_no']); ?>" class="highLighter"  style="width: 65px;">
                                            </li>
                                            <li>(iii) Section(s) of the concerned Act(s) and brief description of the
                                              offense(s) for which he has been convicted -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['convicted_des']"}}" value="{{$iterate_police_case['convicted_des']}}" class="highLighter">
                                            </li>
  
                                            <li>(iv)Date(s) of conviction(s) -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['date_of_conviction']"}}" value="{{$iterate_police_case['date_of_conviction']}}" class="highLighter">
                                            </li>
  
                                            <li>(v) Court(s) which convicted the candidate -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['court_name']"}}" value="{{$iterate_police_case['court_name']}}" class="highLighter">
                                            </li>
  
                                            <li>(vi)Punishment(s) imposed [indicate period of imprisonment(s) and/or
                                              quantum offine(s)] -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['punishment_imposed']"}}" value="{{$iterate_police_case['punishment_imposed']}}" class="highLighter">
                                            </li>
  
                                            <li>(vii) Date(s) of release from prison -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['date_of_release']"}}" value="{{$iterate_police_case['date_of_release']}}" class="highLighter">
                                            </li>
  
                                            <li>(viii)Was/were any appeal(s)/revision(s) field against above
                                              conviction(s) -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['revision_against_conviction']"}}" value="{{$iterate_police_case['revision_against_conviction']}}" class="highLighter">
                                            </li>
  
                                            <li>(ix) Date and particulars of appeal(s)/application(s) for revision field
                                              -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['revision_appeal_date']"}}" value="{{$iterate_police_case['revision_appeal_date']}}" class="highLighter">
                                            </li>
  
                                            <li>(x) Name of the court(s) before which the appeal(s)/application(s) for
                                              revision field -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['rev_court_name']"}}" value="{{$iterate_police_case['rev_court_name']}}" class="highLighter">
                                            </li>
  
  
                                            <li>(xi) Whether the said appeal(s)/application(s) for revision has/have
                                              been disposed of or is/are pending -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['status']"}}" value="{{$iterate_police_case['status']}}" class="highLighter">
                                            </li>
  
                                            <li class="statusReport">(xii) If the said appeal(s)/application(s) for
                                              revision has/have been disposed of—<br>
                                              <ul>
                                                <li>(a) Date(s) of disposal -
                                                  <input type="text" name="{{$i.'-'."iterate_police_case['revision_disposal_date']"}}" value="{{$iterate_police_case['revision_disposal_date']}}" class="highLighter">
                                                </li>
                                                <li>(b) Nature of order(s) passed -
                                                  <input type="text" name="{{$i.'-'."iterate_police_case['revision_order_description']"}}" value="{{$iterate_police_case['revision_order_description']}}" class="highLighter">
                                                </li>
                                              </ul>
                                            </li>
                                          </ul>
                                        </td>
                                      </tr>
                                      <?php $i++ ?>
                                      @endforeach
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                              @endif
                              <!-- End Police Case -->
                            </div>
                            <div class="casesec">
                              <p>(2) Whether the candidate is holding any office of profit under the Government of India
                                or State Government?<input type="text" value="{{$profit_under_govt}}" class="highLighter" style="width: 65px;"></p>
                              @if($profit_under_govt == 'yes')
                              <ul>
                                <li>If Yes, details of the office held <input type="text" value="{{$office_held}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(3) Whether the candidate has been declared insolvent by any Court?
                                <input type="text" name="court_insolvent" value="{{$court_insolvent}}" class="highLighter"  style="width: 65px;"></p>
                              @if($court_insolvent == 'yes')
                              <ul>
                                <li>-If Yes, has he been discharged from insolvency <input type="text" name="discharged_insolvency" value="{{$discharged_insolvency}}" class="highLighter"  style="width: 65px;">
                                </li>
                              </ul>
                              @endif
  
                              <p>(4) Whether the candidate is under allegiance or adherence to any foreign country?
                                <input type="text" name="allegiance_to_foreign_country" value="{{$allegiance_to_foreign_country}}" class="highLighter"  style="width: 65px;"></p>
                              @if($allegiance_to_foreign_country == 'yes')
                              <ul>
                                <li>-If Yes, give details <input type="text" name="country_detail" value="{{$country_detail}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(5) Whether the candidate has been disqualified under section 8A of the said Act by an
                                order of the President? <input type="text" name="disqualified_section8A" value="{{$disqualified_section8A}}" class="highLighter"  style="width: 65px;"></p>
                              @if($disqualified_section8A == 'yes')
                              <ul>
                                <li>-If Yes, the period for which disqualified <input type="text" name="disqualified_period" value="{{$disqualified_period}}" class="highLighter"></li>
                              </ul>
                              @endif
  
                              <p>(6) Whether the candidate was dismissed for corruption or for disloyalty while holding
                                office under the Government of India or the Government of any State?
                                <input type="text" name="disloyalty_status" value="{{$disloyalty_status}}" class="highLighter"  style="width: 65px;"></p>
                              @if($disloyalty_status == 'yes')
                              <ul>
                                <li>-If Yes, the date of such dismissal <input type="text" name="date_of_dismissal" value="{{$date_of_dismissal}}" class="highLighter"  style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(7) Whether the candidate has any subsisting contract(s) with the Government either in
                                individual capacity or by trust or partnership in which the candidate has a share for
                                supply of any goods to that Government or for execution of works undertaken by that
                                Government? <input type="text" name="subsiting_gov_taken" value="{{$subsiting_gov_taken}}" class="highLighter" style="width: 65px;"></p>
                              @if($subsiting_gov_taken == 'yes')
                              <ul>
                                <li>-If Yes, with which Government and details of subsisting contract(s)
                                  <input type="text" name="subsitting_contract" value="{{$subsitting_contract}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(8) Whether the candidate is a managing agent, or manager or Secretary of any company
                                or Corporation (other than a cooperative society) in the capital of which the Central/
                                Government or State Government has not less than twenty-five percent
                                share? <input type="text" name="managing_agent" value="{{$managing_agent}}" class="highLighter" style="width: 65px;"></p>
                              @if($managing_agent == 'yes')
                              <ul>
                                <li>-If Yes, with which Government and the details thereof <b></b><input type="text" name="gov_detail" value="{{$gov_detail}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(9) Whether the candidate has been disqualified by the Commission under section 10A of
                                the said Act <b></b><input type="text" name="disqualified_by_comission_10Asec" value="{{$disqualified_by_comission_10Asec}}" class="highLighter" style="width: 65px;"></p>
                              @if($disqualified_by_comission_10Asec=='yes')
                              <ul>
                                <li>-If yes, the date of disqualification <b></b><input type="text" name="date_of_disqualification" value="{{$date_of_disqualification}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
                              <div class="nomination-signature mt-2">
                                <span class="nomination-date left">Date: - <input type="text"  name="date_of_disloyal" value="{{$date_of_disloyal}}" class="highLighter" style="width: 95px;"></span>
                              </div>
                            </div>
                          </div>
                          </div>
                          <div id="affidavit-preview" class="affidavit-preview py-4">
                            <embed src="<?php echo $affidavit; ?>" width='100%' height='500px' />
                          </div> 
         </div><!-- End Of nomin-frm Div -->
        </div><!-- End Of sub-scroll Div -->
        </div><!-- End Of prnt-wrp Div -->
     </div>
   </div>
 </section>	   
   </div>  
   <div class="col-md-4 col-12 px-0">
  
  @if(empty($prescrutiny_status) || $prescrutiny_status == '2')
  <section>
  <div class="d-flex align-items-center justify-content-between py-3">
  <h5 class="text-center">Pre-Scrutiny Defects <span class="defet-count">{{ count($comment_section) }}</span></h5>
    @if(empty($prescrutiny_status))
        <button id="clear_pre_scrutiny" type="button" class="btn btn-success">Clear Pre Scrutiny</button>
    @endif
  
  </div> 
		<div class="card card-shadow mt-4">
			<div class="card-body defect-bg p-1">
      <form id="comment_form" action="{{ url('roac/submit_prescrutiny_deatils') }}" method="post">
        @csrf
      <input type="hidden" name="nomination_no" value="{{ isset($nomination_id) ? encrypt_string($nomination_id) : ''  }}">
        <div class="prnt-wrp">
				 <div id="comment_box" class="sub-scroll p-3">
          @if(!empty($prescrutiny_status))
          @if(count($comment_section)>0)
            @foreach ($comment_section as $item)
              <div class="defect-reslt">
                <div class="row">
                  <div class="col-md-12 col-12">
                    @php $part_state = \App\models\Admin\Nomination\PreScrutiny\PreScrutinyModel::getformated_part($item['form_part_no']); @endphp
                  <div class="hghted-txt">{{ $item['defect'].' (PART '.$part_state.')' }}</div> 
                  </div>  
                </div>
                <div class="commt-wrap py-2">
                  <div class="row">
                  <div class="col-md-9 col-12">
                  <textarea class="comnt-box" disabled>{{ $item['remark'] }}</textarea>
                </div>
                  </div>
                  @if($item['is_defect_resolved'] == 1)
                  <div class="row">
                    <div class="offset-5 col-md-7">
                      <div class="rslt-success">
                        Status:- Resolved
                      <p> Status Date:- {{ date('d-m-Y h:i A', strtotime($item['defect_resolved_datetime'])) }} </p>
                    </div>
                    </div>
                  </div>
                  @else
                  <div class="row">
                    <div class="offset-5 col-md-7">
                      <div class="rslt-pendding">
                        Status:- Not Resolved       
                      </div>
                      
                    </div>
                  </div>
                  @endif
                </div> 
                </div>
            @endforeach
          @endif
          @endif
         </div><!-- End Of sub-scroll Div -->
         @if(empty($prescrutiny_status))
				<div id="submit_button" class="foot-box" style="display:none">
          <button id="submit_form_preview" type="button" class="btn btn-primary">Proceed</button>
        </div><!-- End Of foot-box Div -->
        @endif   
        </div><!-- End Of prnt-wrp Div -->
      </form>
			</div>
		</div>
   </section>
   @endif  
   </div>
    </div>
 </div>
</main>

<!-- Simple Modal For Clearing Pre Scrutiny -->
  <div class="modal fade" id="pre_scrutiny_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Please Confirm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
          <div class="container-fluid">
            <h6 class="text-center">Are You Sure You Want to mark the Pre Scrutiny as <span class="text-danger"><strong>'Cleared'</strong></span></h6>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button id="pre_scrutiny_modal_btn" type="button" class="btn btn-success">Proceed</button>
        </div>
      </div>
    </div>
  </div>

<!-- Marked With Defect Model -->
<div class="modal fade" id="pre_scrutiny_preview_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Please Confirm</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
      <div class="modal-body">
        <div class="container-fluid">
          <h6 class="text-center">Are You Sure You Want to mark the Pre Scrutiny as <span class="text-danger"><strong>'Defect'</strong></span></h6>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button id="pre_scrutiny_preview_modal_btn" type="button" class="btn btn-success" style="display: none">Proceed</button>
      </div>
    </div>
  </div>
</div>

  <script>
    $('#exampleModal').on('show.bs.modal', event => {
      var button = $(event.relatedTarget);
      var modal = $(this);
      // Use above variables to manipulate the DOM 
    });
  </script>

<!-- End Simple Model For Clearing Pre Scrutiny -->
@endsection

@section('script')

<script src='{{ asset('theme/js/TextHighlighter.js') }}'></script>
@if (session('success_mes'))
<script type="text/javascript">
  success_messages("{{session('success_mes') }}");
</script>
@endif

@if (session('error_mes'))
<script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif

<script>
$(document).ready(function(){ 
  // $('.highLighter').highlight(['jhhjhjjhhj']);
  // console.log(hltr);
  // var hltr = new TextHighlighter('.highLighter'); console.log(hltr);
    if($('#breadcrumb').length){
      var breadcrumb = '';
      $.each({!! json_encode($breadcrumbs) !!},function(index, object){
      breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
    });
      $('#breadcrumb').html(breadcrumb);
    }

    	//This function for text Highlighter and comment box 
      <?php if(empty($prescrutiny_status)) { ?>

      var getSelected = function(){
      var t = '';
      if(window.getSelection) {
        t = window.getSelection();
      } else if(document.getSelection) {
        t = document.getSelection();
      } else if(document.selection) {
        t = document.selection.createRange().text;
      }
        return t;
      }
      var html_dyn = '';
      var data_array_to_show = [];
      $(function(){
        let count_append_no = 1;
        $(".highLighter").blur(function() {
            $(".highLighter").unbind("select");
        });

				$(".highLighter").focusin(function() {

          $(".highLighter").select(function() {
            let selText    = getSelected().toString();
            let part_name  = $(this).parents().closest('.nomination-detail').attr('field_part');
            let filed_name = $(this).attr('name');
            let single_data = {'selText': selText, 'part_name': part_name, 'filed_name': filed_name};

            const index = data_array_to_show.findIndex((e) => e.filed_name === single_data.filed_name);
            if (index === -1) {
              data_array_to_show.push(single_data);
              let exp_text = single_data['filed_name']+'&&'+single_data['part_name']+'&&'+single_data['selText'];
              console.log(exp_text);
              html_dyn =  '   <div id=srn'+count_append_no+' class="defect-reslt">  '  + 
            '   					 <div class="row">  '  + 
            '   					   <div class="col-md-10 col-12">  '  + 
            '   						 <div class="hghted-txt">'+single_data['selText']+' ('+'PART'+single_data['part_name']+')</div>   '  + 
            '   					   </div>    ' +
            '   					 </div>  '  + 
            '   					 <div class="commt-wrap py-2">  '  + 
            '   					   <div class="row">  '  + 
            '   					    <div class="col-md-9 col-12">  '  + 
            '   						   <textarea  name="'+exp_text+'"'+'class="comnt-box"></textarea>  '  + 
            '   						</div>  '  + 
            '   					    <div class="col-md-3 col-12 text-right">  '  + 
            '   						 <div class="edt-cncl-btn mt-5">	  '  + 
            '   						   <a srial_no_cmt='+count_append_no+' '+'class="add-cmt-btn"  title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>'+ 
            '   						   <a srial_no='+count_append_no+' field_name_a='+single_data['filed_name']+' '+'class="cancel-btn"  title="Cancel"><i class="fa fa-times" aria-hidden="true"></i></a>'+ 
            '   						 </div>   '  + 
            '   						</div>  '  + 
            '   					   </div>  '  + 
            '   					 </div>   '  + 
            '  				   </div>  ' ; 
                  count_append_no++;
                  $('#comment_box').append(html_dyn);
                  $('.defet-count').text(data_array_to_show.length);
                  if(data_array_to_show.length>0){
                    $('#clear_pre_scrutiny').fadeOut();
                  }
            }else{
                alert('already Selected Text !');
            }
            $(this).addClass('selected-filled');
            $('.add-cmt-btn').fadeIn();
            $('#submit_button').show();
				  });
         });

      $(document).on('click', '.cancel-btn', function(e){
        let val_new = "#srn" + $(this).attr('srial_no');
        let text_high = $(this).attr('field_name_a');
        $("input[name="+text_high+"]").removeClass('selected-filled');
        console.log(data_array_to_show);
        data_array_to_show = $.grep(data_array_to_show, function(value) {
          console.log(text_high+'  '+value.filed_name);
          return value.filed_name != text_high;
        });
        console.log(data_array_to_show);
        $(val_new).slideUp(800);
        $(val_new).remove();
        $('.defet-count').text(data_array_to_show.length);
        if(data_array_to_show.length=='0'){
            $('#clear_pre_scrutiny').fadeIn();
        }
      });   
    });
    <?php } ?>

      // Ajax for Clearing Pre Scrutiny
      $('#clear_pre_scrutiny').click(function(e) {
        // $('#pre_scrutiny_modal').modal('show')

      var newhtml1 = '<tr><td class="text-center" colspan="4"><strong>No Defects Marked</strong></td></tr>'
      $('#heading_pre_scr').html("Are You Sure You Want to mark the Pre Scrutiny as <span class="+'text-danger'+"><strong>'Cleared'</strong></span>");
      $('#PreScrutiny_data').html(newhtml1);
      $('#pre_scrutiny_modal').modal('show');

      $('#pre_scrutiny_modal_btn').click(function(e) { console.log('clicked');
       $.ajax({
       url: "{{ url('/roac/cleared_pre_scrutiny') }}",
       type: 'POST',
       data: '_token=<?php echo csrf_token() ?>&nomination_id={{encrypt_string($nomination_id)}}',
       dataType: 'json',
       beforeSend: function() {
         $('#pre_scrutiny_modal_btn').prop('disabled',true);
         $('#pre_scrutiny_modal_btn').text("populating data...");
         $('#pre_scrutiny_modal_btn').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
       },
       complete: function() {
       },
       success: function(json) {
         if(json['success'] == true){
          location.reload();
         }
         if(json['success'] == false){
           if(json['errors']['warning']){
             alert(json['errors']['warning']);
           }
         }
         $('#pre_scrutiny_modal_btn').prop('disabled',false);
         $('#pre_scrutiny_modal_btn').text("Submit");
         $('.loading_spinner').remove();
       },
       error: function(data) {
         var errors = data.responseJSON;
         $('#pre_scrutiny_modal_btn').prop('disabled',false);
         $('#pre_scrutiny_modal_btn').text("Submit");
         $('.loading_spinner').remove();
       }
     });
    });
    });

    $('#submit_form_preview').click(function(e) {
      var newhtml = '';
      // $('#data_to_clone').html($('#main_body_to_show').clone());

      if(data_array_to_show.length>0){
        let count_var = 1;
        $.each(data_array_to_show, function(key, value){
          let field_name_var = '';
          let exp_text = value['filed_name']+'&&'+value['part_name']+'&&'+value['selText']; console.log(exp_text);
          let comment_value = $('textarea[name="'+exp_text+'"]').val();
             newhtml +='<tr>'+
      '           <td scope="row">'+count_var+'</td>'+
      '           <td>PART'+value['part_name']+'</td>'+
      // '           <td>PART'+value['filed_name']+'</td>'+
      '           <td>'+value['selText']+'</td>'+
      '           <td>'+comment_value+'</td>'+
      '                 </tr>';
      count_var++;
        });
      }else{
        newhtml = '<tr><td class="text-center" colspan="4">No Data Avilable</td></tr>'
      }
      $('#PreScrutiny_data').html(newhtml)
      $('#pre_scrutiny_preview_modal_btn').show();
      $('#pre_scrutiny_preview_modal').modal('show');
    });

    $('#pre_scrutiny_preview_modal_btn').click(function(e) {
      $('#comment_form').submit();
    });

    var comment_data = @json($comment_section);
    console.log(comment_data);
    $.each(comment_data, function(key, value){
      $("input[name='"+value['column_name']+"']").addClass('selected-filled');
    });
});

</script>
@endsection