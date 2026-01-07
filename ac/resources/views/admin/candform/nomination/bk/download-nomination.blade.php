<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Candidate Nomination</title>
  <style type="text/css">
    p{
      text-align: left;
    }
    .table-strip{border-collapse: collapse;}


      @page {
        header: page-header;
        footer: page-footer;
      }
    </style>
</head>
<body>
<htmlpageheader name="page-header">
  <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
    <thead>
      <tr>
        <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
        <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">

        </th>
      </tr>
    </thead>
  </table>
  <table style="width:100%; border: 1px solid #000;" border="0" align="center">  

    <tr>
      <td  style="width:50%;">
        <table  style="width:100%">
          <tbody>

            <tr> 
              <td align="left"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td> 
              <td align="right">Reference ID: <strong>{{$nomination_no}}</strong> </td>
            </tr>

            <tr>  
              <td align="left">
                <img src="{!! $qr_code !!}" style="max-width: 80px;">
              </td>
              <td align="right"><img src="{!! $profileimg !!}" style="max-width: 100px;border: 1px solid #9c9c9c;"></td>
            </tr> 
          </tbody>
        </table>
      </td>
    </tr>



  </table>
  </htmlpageheader>


<htmlpagebody>
  

  <table class="table-strip" style="width: 100%;" border="1" cellpadding="9">

    <tbody>

      <tr>
        <td align="center">
          <p style="text-align:center;">Election to the Legislative Assembly of <span class="nominationvalue"><b>{{$st_name}}</b></span>(State)</p>
        </td>
      </tr>


      
          @if($recognized_party == 'recognized')
          <tr>
        <td>
          <p><br><br></p>
            <p style="padding-top: 15px;text-align: center !important;">
              <strong>PART I</strong><br>
              (To be used by candidate set up by recognised political party)<br><br>
            </p>
            <p style="padding-top: 15px;">I nominate as a candidate for election to the Legislative Assembly from the 
              <b>{!! $legislative_assembly !!}</b> Assembly Constituency.<br><br></p>

           <p style="padding-top: 15px;">   Candidate's name <b>{{$name}}</b> Father's/mother's/husband's name <b>{!! $father_name !!}</b>

              His postal address <b>{!! $address !!}</b> 

              His name is entered at Sl.No <b>{{$serial_no}}</b>

              in part No <b>{{$part_no}}</b>

              of the electoral roll for <b>{{$resident_ac_no}}</b>
              Assembly constituency.<br><br>
            </p>
         
             <p style="padding-top: 15px;">My name is <b>{{$proposer_name}}</b> 

              and it is entered at Sl.No <b>{{$proposer_serial_no}}</b> 

              in part No <b>{{$proposer_part_no}}</b> 

              of the electoral roll for <b>{{$proposer_assembly}}</b> Assembly constituency.<br><br>
            </p>
         

              <p style="text-align: left;padding-top: 15px;">
                Date <b>{{$apply_date}}</b>
                <br><br>
              </p>
              <p><br><br></p>
</td>
</tr>



       
            @else

            <tr>
              <td>
                <p><br><br></p>
            <p class="nomination-form-heading">
              <strong>PART II</strong><br>
              (To be used by candidate NOT set up by recognised political party) <br><br>
            </p>
         

            <div class="nomination-detail">
              <p style="text-align:left">We hereby nominate as candidate for election to the Legislative Assembly from the <b>{{$legislative_assembly}}</b>Assembly Constituency<br><br></p>

              <p>
                Candidate's name <b>{{$name}}</b> Father's/mother's/husband's name <b>{!! $father_name !!}</b>

                His postal address <b>{!! $address !!}</b> 

                His name is entered at Sl.No <b>{{$serial_no}}</b>

                in part No <b>{{$part_no}}</b>

                of the electoral roll for <b>{{$resident_ac_no}}</b>

                Assembly constituency.<br><br></p>

                <p style="text-align: left;"><br><br>
                  Date <b>{{$apply_date}}</b>
                </p>

                <p><br><br>
              We declare that we are electors of this Assembly constituency and our names are entered in the electoral roll for this Assembly constituency as indicated below and we append our signatures below in token of subscribing to this nomination: -</p>
<p><br><br></p>
              <p class="table-heading">Particulars of the proposers and their signatures<br><br></p>

              <table class="table table-bordered proposers-table">
                <thead>
                  <tr>
                    <th>Sr No.</th>
                    <th colspan="2">Part No of Proposer </th>
                    <th>Full Name</th>
                    <th>Signature</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>&nbsp;</td>
                    <td>Serial No. of Electoral Roll</td>
                    <td>part no. in that part</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <?php 
                  foreach($non_recognized_proposers as $iterate_proposer){ ?>
                    <tr class="non_recognized_proposers_row">
                      <td>{{$iterate_proposer['s_no']}}</td>            
                      <td>{{$iterate_proposer['serial_no']}}</td>
                      <td>{{$iterate_proposer['part_no']}}</td>
                      <td>{{$iterate_proposer['fullname']}}</td>
                      <td>{{$iterate_proposer['signature']}}</td>
                      <td>{{$iterate_proposer['date']}}</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

            </td>
        </tr>

            @endif
          


        <tr>
          <td>

            <p><br><br></p>
            <p style="text-align:left"><strong>PART III</strong><br><br></p>

            <div class="nomination-detail">
              <p style="text-align:left">I, the candidate mentioned in Part I/Part II (Strike out which is not applicable) assent to this nomination and hereby declare—<br><br></p>
              <ul>
                <li>(a) that I am a citizen of India and have not acquired the citizenship of any foreign State/country.</li>
                <li>(b) that I have completed <b>{{$age}}</b> years of age; <br>

                [STRIKE OUT c(i) or c(ii) BELOW WHICHEVER IS NOT APPLICABLE]</li>

                <div class="nomination-options strikeout">

                  @if($recognized_party == 'recognized')
                  <div class="checkbox recognised" style="">
                    <label>(c) (i) that I am set up at this election by the <b>{{$party_id}}</b> party, which is recognised National Party/State Party in this State and that the symbol reserved for the above party be allotted to me.</label>
                  </div>


                  @else
                  <div class="checkbox not-recognized">
                    <label>(c) (ii) that I am set up at this election by the <b>{{$party_id}}</b>
                      party, which is a registered-unrecognised political party/that I am contesting this election as an independent candidate. (Strike out which is not applicable) and that the symbols I have chosen, in order of preference, are:—  <br>
                      (i) <b>{{$suggest_symbol_1}}</b> (ii) <b>{{$suggest_symbol_2}}</b> (iii) <b>{{$suggest_symbol_3}}</b></label>
                    </div>
                    @endif

                  </div>

                  <li>(d) that my name and my father's/mother's/husband's name have been correctly spelt out above in <b>{{$language}}</b></li>
                  <li>(e) that to the best of my knowledge and belief, I am qualified and not also disqualified for being chosen to fill the seat in the House of the People.</li></ul><br><br>
                </div>

    
                  <p style="text-align:left">*I further declare that I am a member of the <b>{{$category}}</b>

                  I also declare that I have not been, and shall not be nominated as a candidate at the present general election/the bye-elections being held simultaneously, to the House of the People from more than two Parliamentary Constituencies. <br><br></p>
             

                <p>
                  Date <b>{{$part3_date}}</b>
                </p>

                <p><br><br></p>
              </td> 
            </tr>


            <tr>
              <td>


                <p><br><br></p>



                <p class="nomination-form-heading">
                  <strong>PART IIIA </strong><br>
                  (To be filled by the candidate) <br><br>
                </p>

                <div class="nomination-detail">
                  <div class="criminal-section">
                    <p style="text-align:left">(1) Whether the candidate— </p>
                    <ul><li>(i) has been convicted— <ul>
                      <li>(a) of any offense(s) under sub-section (1); or</li>
                      <li>(b) for contravention of any law specified in sub-section (2), of section 8 of the Representation of the People Act, 1951 (43 of 1951); or -</li></ul>
                    </li><li>(ii) has been convicted for any other offense(s) for which he has been sentenced to imprisonment for two years or more. </li></ul>

                    <label class="case"><b>{{$have_police_case}}</b></label>
                  </div>  




                  <!-- Police Case -->
                  @if($have_police_case == 'yes')
                  <div class="criminal-section have_police_case_div field_wrapper">      

                    <p style="text-align:left">If the answer is "Yes", the candidate shall furnish the following information:</p>

                    <?php $i = 1; ?>
                    <div class="fullwidth have_police_case_record">

                      <table>
                        <tbody class="police_case_body"> 
                          @foreach($police_cases as $iterate_police_case)

                          <tr><td><h3>Case {{$i}}</h3></td></tr>
                          <tr>
                            <td>
                              <ul>
                                <li>(i) Case/first information report No./Nos - <b>{{$iterate_police_case['case_no']}}</b></li>
                                <li>(ii) Police station(s) - <b>{{$iterate_police_case['police_station']}}</b><br>
                                  State(s) - <b>{{$iterate_police_case['state']}}</b>
                                  District(s) - <b>{{$iterate_police_case['district']}}</b>
                                </li>
                                <li>(iii) Section(s) of the concerned Act(s) and brief description of the offense(s) for which he has been convicted - <b>{{$iterate_police_case['convicted_des']}}</b>
                                </li>

                                <li>(iv)Date(s) of conviction(s) - <b>{{$iterate_police_case['date_of_conviction']}}</b>
                                </li>

                                <li>(v) Court(s) which convicted the candidate - <b>{{$iterate_police_case['court_name']}}</b>
                                </li>

                                <li>(vi)Punishment(s) imposed [indicate period of imprisonment(s) and/or quantum offine(s)] - <b>{{$iterate_police_case['punishment_imposed']}}</b>
                                </li>

                                <li>(vii) Date(s) of release from prison - <b>{{$iterate_police_case['date_of_release']}}</b>
                                </li>

                                <li>(viii)Was/were any appeal(s)/revision(s) filed against above conviction(s) - <b>{{$iterate_police_case['revision_against_conviction']}}</b>
                                </li>

                                <li>(ix) Date and particulars of appeal(s)/application(s) for revision filed  - <b>{{$iterate_police_case['revision_appeal_date']}}</b>
                                </li>

                                <li>(x) Name of the court(s) before which the appeal(s)/application(s) for revision filed  - <b>{{$iterate_police_case['rev_court_name']}}</b>
                                </li>


                                <li>(xi) Whether the said appeal(s)/application(s) for revision has/have been disposed of or is/are pending  - <b>{{$iterate_police_case['status']}}</b>
                                </li>

                                <li class="statusReport">(xii) If the said appeal(s)/application(s) for revision has/have been disposed of—<br>
                                  <ul>
                                    <li>(a) Date(s) of disposal  - <b>{{$iterate_police_case['revision_disposal_date']}}</b>
                                    </li>
                                    <li>(b) Nature of order(s) passed  - <b>{{$iterate_police_case['revision_order_description']}}</b>
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
                  <p style="text-align:left">(2) Whether the candidate is holding any office of profit under the Government of India or State Government? <b>{{$profit_under_govt}}</b></p>
                  @if($profit_under_govt == 'yes')
                  <ul><li>If Yes, details of the office held <b>{{$office_held}}</b></li></ul>
                  @endif

                  <p style="text-align:left">(3) Whether the candidate has been declared insolvent by any Court? <b>{{$court_insolvent}}</b></p>
                  @if($court_insolvent == 'yes')
                  <ul><li>-If Yes, has he been discharged from insolvency <b>{{$discharged_insolvency}}</b></li></ul>
                  @endif

                  <p style="text-align:left">(4) Whether the candidate is under allegiance or adherence to any foreign country? <b>{{$allegiance_to_foreign_country}}</b></p>
                  @if($allegiance_to_foreign_country == 'yes')
                  <ul><li>-If Yes, give details <b>{{$country_detail}}</b> </li></ul>
                  @endif

                  <p style="text-align:left">(5) Whether the candidate has been disqualified under section 8A of the said Act by an order of the President? <b>{{$disqualified_section8A}}</b></p>
                  @if($disqualified_section8A == 'yes')
                  <ul><li>-If Yes, the period for which disqualified <b>{{$disqualified_period}}</b></li></ul>
                  @endif

                  <p style="text-align:left">(6) Whether the candidate was dismissed for corruption or for disloyalty while holding office under the Government of India or the Government of any State? <b>{{$disloyalty_status}}</b></p>
                  @if($disloyalty_status == 'yes')
                  <ul><li>-If Yes, the date of such dismissal <b>{{$date_of_dismissal}}</b></li></ul>
                  @endif

                  <p style="text-align:left">(7) Whether the candidate has any subsisting contract(s) with the Government either in individual capacity or by trust or partnership in which the candidate has a share for supply of any goods to that Government or for execution of works undertaken by that Government?<b>{{$subsiting_gov_taken}}</b></p>
                  @if($subsiting_gov_taken == 'yes')
                  <ul><li>-If Yes, with which Government and details of subsisting contract(s) <b>{{$subsitting_contract}}</b></li></ul>
                  @endif

                  <p style="text-align:left">(8) Whether the candidate is a managing agent, or manager or Secretary of any company or Corporation (other than a cooperative society) in the capital of which the Central/ Government or State Government has not less than twenty-five percent share?<b>{{$managing_agent}}</b></p>
                  @if($managing_agent == 'yes')
                  <ul><li>-If Yes, with which Government and the details thereof <b>{{$gov_detail}}</b></li></ul>
                  @endif

                  <p style="text-align:left">(9) Whether the candidate has been disqualified by the Commission under section 10A of the said Act <b>{{$disqualified_by_comission_10Asec}}</b></p>
                  @if($disqualified_by_comission_10Asec=='yes')
                  <ul><li>-If yes, the date of disqualification <b>{{$date_of_disqualification}}</b></li></ul>
                  @endif

                </div>


                <p style="text-align:left"><br><br>Date: - <b>{{$date_of_disloyal}}</b></p>

                <p><br><br></p>







              </td>
            </tr>
          </tbody></table>
          </htmlpagebody>
<htmlpagefooter name="page-footer">
          <table style="width:100%; border-collapse: collapse;" align="center" cellpadding="5">
            <tbody>
              <tr>
                <td colspan="2" align="center"><strong></strong></td>  
              </tr>
            </tbody>
          </table>
          </htmlpagefooter>
        </body>
        </html>