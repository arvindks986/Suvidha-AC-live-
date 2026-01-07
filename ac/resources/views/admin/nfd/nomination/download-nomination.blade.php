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
			  @if($qrcode)	
              <td align="left">
                <img src="{!! $qrcode !!}" style="max-width: 80px;">
              </td>
			   @endif
			  @if($profileimg)
              <td align="right"><img src="{!! $profileimg !!}" style="max-width: 100px;border: 1px solid #9c9c9c;"></td>
			  @endif
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


      
          @if($recognized_party == '1')
          <tr>
        <td>
          <p><br><br></p>
            <p style="padding-top: 15px;text-align: center !important;">
              <strong>PART I</strong><br>
              (To be used by candidate set up by recognised political party)<br><br>
            </p>
            <p style="padding-top: 15px;">I nominate as a candidate for election to the Legislative Assembly from the  <strong><u>{{strtoupper(getacbyacno($st_code,$legislative_assembly)->AC_NAME)}}</u></strong> Assembly Constituency.</p>

           <p style="padding-top: 15px;">Candidate's name <strong><u>{{$name}}</u></strong> Father's/mother's/husband's name <strong><u>{!! $father_name !!}</u></strong>
          His postal address <strong><u>{!! $address !!}</u></strong> 
          His name is entered at Sl.No <strong><u>{{$serial_no}}</u></strong>
          in part No <strong><u>{{$part_no}}</u></strong>
          of the electoral roll for 
                <strong><u>{{strtoupper(getacbyacno($home_st_code,$home_ac_no)->AC_NAME)}}</u></strong>
              Assembly constituency.<br>
            </p>
         
    <p style="padding-top: 15px;"> My name is <strong><u>{{$proposer_name}} </u></strong>
        and it is entered at Sl.No <strong><u>{{$proposer_serial_no}} </u></strong>
        in part No <strong><u>{{$proposer_part_no}} </u></strong>
        of the electoral roll for 
            <strong><u>{{strtoupper(getacbyacno($st_code,$proposer_assembly)->AC_NAME)}}  </u></strong> 
            Assembly constituency. <br>
            </p>
         

              <p style="text-align: left;padding-top: 15px;">Date <strong><u>{{$apply_date}}</u></strong>
              </p>      
</td>
</tr>



       
            @else

            <tr>
              <td>
           <p class="nomination-form-heading">
              <strong>PART II</strong><br>
              (To be used by candidate NOT set up by recognised political party) <br><br>
            </p>
         

            <div class="nomination-detail">
              <p style="text-align:left">We hereby nominate as candidate for election to the Legislative Assembly from the <strong><u>{{strtoupper(getacbyacno($st_code,$legislative_assembly)->AC_NAME)}}</u></strong>Assembly Constituency</p>
              <p>Candidate's name <strong><u>{{$name}}</u></strong>
                                     Father's/mother's/husband's name<strong><u>{!! $father_name !!}</u></strong>

                                     His postal address <strong><u>{!! $address !!}</u></strong> 

                                     His name is entered at Sl.No <strong><u>{{$serial_no}}</u></strong>

                                     in part No <strong><u>{{$part_no}}</u></strong>

                                     of the electoral roll for <strong><u>{{strtoupper(getacbyacno($home_st_code,$home_ac_no)->AC_NAME)}}</u></strong>

                                     Assembly constituency.<br></p>
      <br><br>
                <p style="text-align: left;">Date :- <strong><u>{{$apply_date}}</u></strong></p>

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
                <li>(b)(b) that I have completed <strong><u>{{$age}}</u></strong> years of age; <br>

                [STRIKE OUT c(i) or c(ii) BELOW WHICHEVER IS NOT APPLICABLE]</li>

                <div class="nomination-options strikeout">

                  @if($recognized_party == '1')
                                    <div class="checkbox recognised" style="">
                                      <label>(c) (i) that I am set up at this election by the <strong><u>{{strtoupper(getpartybyid($party_id)->PARTYNAME)}} </u></strong> party, which is recognised National Party/State Party in this State and that the symbol reserved for the above party be allotted to me.</label>
                                    </div>


                                    @else
                                    <div class="checkbox not-recognized">
                                      <label>(c) (ii) that I am set up at this election by the 
                                        <strong><u>{{strtoupper(getpartybyid($party_id)->PARTYNAME)}} </u></strong> 
                                        party, which is a registered-unrecognised political party/that I am contesting this election as an independent candidate. (Strike out which is not applicable) and that the symbols I have chosen, in order of preference, are:—  <br>
                                        (i) <strong><u>{{$suggest_symbol_1}} </u></strong>  
                                        (ii) <strong><u>{{$suggest_symbol_2}} </u></strong>
                                        (iii) <strong><u>{{$suggest_symbol_3}} </u></strong>  </label>
                                      </div>
                                      @endif


                  </div>

                  <li>(d) that my name and my father's/mother's/husband's name have been correctly spelt out above in <strong><u>{{$language}}</u></strong></li>
                  <li>(e) that to the best of my knowledge and belief, I am qualified and not also disqualified for being chosen to fill the seat in the House of the People.</li></ul><br><br>
                </div>

    
                  <p style="text-align:left">*I further declare that I am a member of the<strong><u>{{$category}}</u></strong>

                  I also declare that I have not been, and shall not be nominated as a candidate at the present general election/the bye-elections being held simultaneously, to the House of the People from more than two Parliamentary Constituencies. <br><br></p>
             

                <p>Date<strong><u> {{date("d-m-Y",strtotime($part3_date))}}</u></strong></p>

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

                    <label class="case"><strong><u>@if($have_police_case==1) Yes @else No @endif </u></strong></label>
                  </div>  




                  <!-- Police Case -->
                  @if($have_police_case == '1')
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
                                                  <li>(i) Case/first information report No./Nos - <strong><u>{{$iterate_police_case['case_no']}}</u></strong></li>
                                                  <li>(ii) Police station(s) - <strong><u>{{$iterate_police_case['police_station']}}</u></strong><br>
                                                    State(s) - <strong><u>{{strtoupper(getstatebystatecode($iterate_police_case['case_st_code'])->ST_NAME)}}</u></strong>
                                                    District(s) - <strong><u>{{strtoupper(getdistrictbydistrictno($iterate_police_case['case_st_code'],$iterate_police_case['case_dist_no'])->DIST_NAME)}}</u></strong>
                                                  </li>
                                                  <li>(iii) Section(s) of the concerned Act(s) and brief description of the offense(s) for which he has been convicted - <strong><u>{{$iterate_police_case['convicted_des']}}</u></strong>
                                                  </li>

                                                  <li>(iv)Date(s) of conviction(s) - <strong><u>{{date("d-m-Y",strtotime($iterate_police_case['date_of_conviction']))}}</u></strong>
                                                  </li>

                                                  <li>(v) Court(s) which convicted the candidate - <strong><u>{{$iterate_police_case['court_name']}}</u></strong>
                                                  </li>

                                                  <li>(vi)Punishment(s) imposed [indicate period of imprisonment(s) and/or quantum offine(s)] - <strong><u>{{$iterate_police_case['punishment_imposed']}}</u></strong>
                                                  </li>

                                                  <li>(vii) Date(s) of release from prison - <strong><u>{{date("d-m-Y",strtotime($iterate_police_case['date_of_release']))}}</u></strong>
                                                  </li>

                                                  <li>(viii)Was/were any appeal(s)/revision(s) filed against above conviction(s) - <strong><u>{{$iterate_police_case['revision_against_conviction']}}</u></strong>
                                                  </li>

                                                  <li>(ix) Date and particulars of appeal(s)/application(s) for revision filed  - <strong><u>{{date("d-m-Y",strtotime($iterate_police_case['revision_appeal_date']))}}</u></strong>
                                                  </li>

                                                  <li>(x) Name of the court(s) before which the appeal(s)/application(s) for revision filed  - <strong><u>{{$iterate_police_case['rev_court_name']}}</u></strong>
                                                  </li>


                                                  <li>(xi) Whether the said appeal(s)/application(s) for revision has/have been disposed of or is/are pending  - <strong><u>{{$iterate_police_case['status']}}</u></strong>
                                                  </li>

                                                  <li class="statusReport">(xii) If the said appeal(s)/application(s) for revision has/have been disposed of—<br>
                                                    <ul>
                                                      <li>(a) Date(s) of disposal  - <strong><u>{{$iterate_police_case['revision_disposal_date']}}</u></strong>
                                                      </li>
                                                      <li>(b) Nature of order(s) passed  - <strong><u>{{$iterate_police_case['revision_order_description']}}</u></strong>
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
            <p style="text-align:left">(2) Whether the candidate is holding any office of profit under the Government of India or State Government? <strong><u>@if($profit_under_govt==1) Yes @else No @endif</u></strong></p>
            
            @if($profit_under_govt == '1')
                <ul><li>If Yes, details of the office held <strong><u>{{$office_held}}</u></strong></li></ul>
            @endif

            <p style="text-align:left">(3) Whether the candidate has been declared insolvent by any Court? <strong><u>@if($court_insolvent==1) Yes @else No @endif </u></strong></p>
             @if($court_insolvent == '1')
              <ul><li>-If Yes, has he been discharged from insolvency <strong><u>{{$discharged_insolvency}}</u></strong></li></ul>
            @endif

            <p style="text-align:left">(4) Whether the candidate is under allegiance or adherence to any foreign country? <strong><u>@if($allegiance_to_foreign_country==1) Yes @else No @endif </u></strong></p>
            @if($allegiance_to_foreign_country == '1')
              <ul><li>-If Yes, give details <strong><u>{{$country_detail}}</u></strong> </li></ul>
            @endif

            <p style="text-align:left">(5) Whether the candidate has been disqualified under section 8A of the said Act by an order of the President? <strong><u>@if($disqualified_section8A==1) Yes @else No @endif </u></strong></p>
                 @if($disqualified_section8A == '1')
            <ul><li>-If Yes, the period for which disqualified <strong><u>{{$disqualified_period}}</u></strong></li></ul>
                 @endif

            <p style="text-align:left">(6) Whether the candidate was dismissed for corruption or for disloyalty while holding office under the Government of India or the Government of any State? <strong><u>@if($disloyalty_status==1) Yes @else No @endif  </u></strong></p>
            
            @if($disloyalty_status == '1')
            <ul><li>-If Yes, the date of such dismissal <strong><u>{{date("d-m-Y",strtotime($date_of_dismissal))}}</u></strong></li></ul>
            @endif

           <p style="text-align:left">(7) Whether the candidate has any subsisting contract(s) with the Government either in individual capacity or by trust or partnership in which the candidate has a share for supply of any goods to that Government or for execution of works undertaken by that Government?<strong><u>@if($subsiting_gov_taken==1) Yes @else No @endif </u></strong></p>
            @if($subsiting_gov_taken == '1')
              <ul><li>-If Yes, with which Government and details of subsisting contract(s) <strong><u>{{$subsitting_contract}}</u></strong></li></ul>
            @endif

          <p style="text-align:left">(8) Whether the candidate is a managing agent, or manager or Secretary of any company or Corporation (other than a cooperative society) in the capital of which the Central/ Government or State Government has not less than twenty-five percent share?<strong><u>@if($managing_agent==1) Yes @else No @endif 
            </u></strong></p>
          @if($managing_agent == '1')
            <ul><li>-If Yes, with which Government and the details thereof <strong><u>{{$gov_detail}}</u></strong></li></ul>
          @endif

          <p style="text-align:left">(9) Whether the candidate has been disqualified by the Commission under section 10A of the said Act <strong><u>@if($disqualified_by_comission_10Asec==1) Yes @else No @endif </u></strong></p>
          @if($disqualified_by_comission_10Asec=='1')
           <ul><li>-If yes, the date of disqualification <strong><u>{{date("d-m-Y",strtotime($date_of_disqualification))}}</u></strong></li></ul>
          @endif

          </div>

<p style="text-align:left">Date: - 
        <strong><u>{{date("d-m-Y",strtotime($date_of_disloyal))}}</u></strong></span>
</p>
  
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