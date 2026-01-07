      @extends('admin.layouts.ac.theme')
        @section('title', 'Nomination')
      @section('content')
      <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
      <style type="text/css">
        .fullwidth{
          width: 100%;
          float: left;
        }
        .button-next{
          margin-top: 30px;
        }
        .button-next button{
          float: right;
        }
        .affidavit-preview{
          min-height: 600px;
        }
      </style>
      <main role="main" class="inner cover mb-3">
        <section>
          <div class="container">
            <div class="row">

            @if(count($errors->all())>0)
               <div class="alert alert-danger">
                <ul>
                 @foreach($errors->all() as $iterate_error)
                 <li><p class="text-left">{!! $iterate_error !!}</p></li>
                 @endforeach
               </ul>
             </div>
             @endif

             @if (session('flash-message'))
             <div class="alert alert-success"> {{session('flash-message') }}</div>
             @endif
         </div>
       </div>    
     </section>

<div class="container-fluid">
   <div class="col-md-12 mt-3">
     <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
      <li class="step step1">Personal Details</li>
      <li class="step step2 ">Election Details</li>
       <li class="step step3">Part I/II</li>
       <li class="step step4">Part III<span></span></li>
       <li class="step step5 ">Part IIIA<span></span></li>
       <li class="step step6 ">Upload Affidavit<span></span></li>
       <li class="step step7 current first">Finalize Application<span></span></li>
     </ul>
 </div>

</div>



     <section>
      <div class="container p-0">

         <div class="row">
<div class="fullwidth" style="float: left;width: 100%;">
     
                
                @if(isset($reference_id) && isset($href_download_application))
                <div class="col-md-5 float-right">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right">Reference ID: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
                    <li class="list-inline-item text-right"><a href="{!! $href_download_application !!}" class="btn btn-primary" target="_blank">Download Application</a></li>
                  </ul>
                </div>
                @endif
              </div>
        </div>


        <div class="row">

          <div class="col-md-12">
            <div class="card">
             <div class="card-header d-flex align-items-center">
               <h4>{!! $heading_title !!}</h4>
             </div>
             <div class="card-body">
               <div class="row">

                 <div class="col">



                   <div class="form-group row">


                    <!-- fieldsets -->
                    

                    
                    

                    <div class="nomination-parts box recognized fullwidth">

                      <form method="post" action="{!! $action !!}" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="nomination_id" value="{{$nomination_id}}">

                        <div class="fullwidth">

                         <!-- fieldsets -->
                         <fieldset class="step1_2 form_steps fullwidth">
                          <div class="nomination-parts1">

                            <div class="fullwidth">
                              <div class="text-center fullwidth heading-part1">
                                <p>FORM 2B</p>
                                <p>(See rule 4)</p>
                                <p>NOMINATION PAPER<p>
                                  <p>Election to the Legislative Assembly of <span class="nominationvalue"><b>{{$st_name}}</b></span>(State)</p>
                                </div>
                              </div>


                            </div>



                            @if($recognized_party == 'recognized')
                            <div class="nomination-parts box recognized fullwidth">


                              <div class="fullwidth">
                                <div class="text-center fullwidth">
                                  <div class="col-lg-2 pull-left">
                                    <img src="{!! $qrcode !!}" style="max-width: 150px;">
                                  </div>
                                  <div class="col-lg-2 pull-right">
                                    <img src="{!! $profileimg !!}" style="max-width: 150px;">
                                  </div>
                                </div>

                                <div class="nomination-form-heading text-center fullwidth">
                                  <strong>PART I</strong><br>
                                  (To be used by candidate set up by recognised political party)
                                </div>

                                <div class="nomination-detail">
                                  <p>I nominate as a candidate for election to the Legislative Assembly from the 
                                    <b>{!! $legislative_assembly !!}</b> Assembly Constituency.<br>

                                    Candidate's name <b>{{$name}}</b> Father's/mother's/husband's name <b>{!! $father_name !!}</b>

                                    His postal address <b>{!! $address !!}</b> 

                                    His name is entered at Sl.no <b>{{$serial_no}}</b>

                                    in part No <b>{{$part_no}}</b>

                                    of the electoral roll for <b>{{$resident_ac_no}}</b>
                                    Assembly constituency.<br><br>

                                    <!-- Recognised Party Proposer Detail -->
                                    My name is <b>{{$proposer_name}}</b> 

                                    and it is entered at Sl.No <b>{{$proposer_serial_no}}</b> 

                                    in part no <b>{{$proposer_part_no}}</b> 

                                    of the electoral roll for <b>{{$proposer_assembly}}</b> Assembly constituency.</p>
                                  </div>

                                  <div class="nomination-signature">
                                    <span class="nomination-date left">Date 
                                      <b>{{$apply_date}}</b>
                                    </span>

                                  </div>



                                </div>
                                @else
                                <div class="nomination-parts box not-recognized fullwidth">



                                  <div class="text-center fullwidth">
                                    <div class="col-lg-2 pull-left">
                                    <img src="{!! $qr_code !!}" style="max-width: 150px;">
                                  </div>
                                    <div class="col-lg-2 pull-right">
                                      <img src="{!! $profileimg !!}" style="max-width: 150px;">
                                    </div>
                                  </div>


                                  <div class="nomination-form-heading">
                                    <strong>PART II</strong><br>
                                    (To be used by candidate NOT set up by recognised political party) 
                                  </div>

                                  <div class="nomination-detail">
                                   <p>We hereby nominate as candidate for election to the Legislative Assembly from the <b>{{$legislative_assembly}}</b>Assembly Constituency<br>

                                     Candidate's name <b>{{$name}}</b> Father's/mother's/husband's name <b>{!! $father_name !!}</b>

                                     His postal address <b>{!! $address !!}</b> 

                                     His name is entered at Sl.No <b>{{$serial_no}}</b>

                                     in part no <b>{{$part_no}}</b>

                                     of the electoral roll for <b>{{$resident_ac_no}}</b>

                                     Assembly constituency.<br><br>

                                     <div class="nomination-signature">
                                      <span class="nomination-date left">Date 
                                        <b>{{$apply_date}}</b>
                                      </span>

                                    </div>

                                  We declare that we are electors of this Assembly constituency and our names are entered in the electoral roll for this Assembly constituency as indicated below and we append our signatures below in token of subscribing to this nomination: -</p>

                                  <div class="table-heading">Particulars of the proposers and their signatures</div>
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
                                      <td>serial no. of Electoral Roll</td>
                                      <td>part. no. in that part</td>
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


                            </div>
                            @endif
                          </div>

                          </fieldset>


                          <fieldset>
                            <div class="nomination-parts">
                              <div class="nomination-form-heading">
                                <strong>PART III</strong>
                              </div>

                              <div class="nomination-detail">
                                <p>I, the candidate mentioned in Part I/Part II (Strike out which is not applicable) assent to this nomination and hereby declare—</p>
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
                                    <li>(e) that to the best of my knowledge and belief, I am qualified and not also disqualified for being chosen to fill the seat in the House of the People.</li></ul>
                                  </div>

                                  <div class="nomination-detail">
                                    <p>*I further declare that I am a member of the <b>{{$category}}</b>

                                    I also declare that I have not been, and shall not be nominated as a candidate at the present general election/the bye-elections being held simultaneously, to the House of the People from more than two Parliamentary Constituencies. </p>
                                  </div>

                                  <div class="nomination-signature">
                                    <span class="nomination-date left">Date <b>{{$part3_date}}</b></span>
                                  </div>

                                  <div class="nomination-note">
                                    *Score out the words "assembly constituency comprised within" in the case of Jammu and Kashmir, Andaman and Nicobar Islands, Chandigarh, Dadra and Nagar Haveli, Daman and Diu and Lakshadweep.<br>

                                    *Score out this paragraph, if not applicable.<br>

                                    **Score out the words not applicable. N.B.—A "recognised political party" means a political party recognised by the Election Commission under the Election Symbols (Reservation and Allotment) Order, 1968 in the State concerned. 
                                  </div>
                                </div>
                              </fieldset>


                              <fieldset>
                                <div class="nomination-parts">
                                  <div class="nomination-form-heading">
                                    <strong>PART IIIA </strong><br>
                                    (To be filled by the candidate) 
                                  </div>

                                  <div class="nomination-detail">
                                    <div class="criminal-section">
                                      <p>(1) Whether the candidate— </p>
                                      <ul><li>(i) has been convicted— <ul>
                                        <li>(a) of any offense(s) under sub-section (1); or</li>
                                        <li>(b) for contravention of any law specified in sub-section (2), of section 8 of the Representation of the People Act, 1951 (43 of 1951); or -</li></ul>
                                      </li><li>(ii) has been convicted for any other offense(s) for which he has been sentenced to imprisonment for two years or more. </li></ul>

                                      <label class="case"><b>{{$have_police_case}}</b></label>
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
                                    <p>(2) Whether the candidate is holding any office of profit under the Government of India or State Government? <b>{{$profit_under_govt}}</b></p>
                                    @if($profit_under_govt == 'yes')
                                    <ul><li>If Yes, details of the office held <b>{{$office_held}}</b></li></ul>
                                    @endif

                                    <p>(3) Whether the candidate has been declared insolvent by any Court? <b>{{$court_insolvent}}</b></p>
                                    @if($court_insolvent == 'yes')
                                    <ul><li>-If Yes, has he been discharged from insolvency <b>{{$discharged_insolvency}}</b></li></ul>
                                    @endif

                                    <p>(4) Whether the candidate is under allegiance or adherence to any foreign country? <b>{{$allegiance_to_foreign_country}}</b></p>
                                    @if($allegiance_to_foreign_country == 'yes')
                                    <ul><li>-If Yes, give details <b>{{$country_detail}}</b> </li></ul>
                                    @endif

                                    <p>(5) Whether the candidate has been disqualified under section 8A of the said Act by an order of the President? <b>{{$disqualified_section8A}}</b></p>
                                    @if($disqualified_section8A == 'yes')
                                    <ul><li>-If Yes, the period for which disqualified <b>{{$disqualified_period}}</b></li></ul>
                                    @endif

                                    <p>(6) Whether the candidate was dismissed for corruption or for disloyalty while holding office under the Government of India or the Government of any State? <b>{{$disloyalty_status}}</b></p>
                                    @if($disloyalty_status == 'yes')
                                    <ul><li>-If Yes, the date of such dismissal <b>{{$date_of_dismissal}}</b></li></ul>
                                    @endif

                                    <p>(7) Whether the candidate has any subsisting contract(s) with the Government either in individual capacity or by trust or partnership in which the candidate has a share for supply of any goods to that Government or for execution of works undertaken by that Government?<b>{{$subsiting_gov_taken}}</b></p>
                                    @if($subsiting_gov_taken == 'yes')
                                    <ul><li>-If Yes, with which Government and details of subsisting contract(s) <b>{{$subsitting_contract}}</b></li></ul>
                                    @endif

                                    <p>(8) Whether the candidate is a managing agent, or manager or Secretary of any company or Corporation (other than a cooperative society) in the capital of which the Central/ Government or State Government has not less than twenty-five percent share?<b>{{$managing_agent}}</b></p>
                                    @if($managing_agent == 'yes')
                                    <ul><li>-If Yes, with which Government and the details thereof <b>{{$gov_detail}}</b></li></ul>
                                    @endif

                                    <p>(9) Whether the candidate has been disqualified by the Commission under section 10A of the said Act <b>{{$disqualified_by_comission_10Asec}}</b></p>
                                    @if($disqualified_by_comission_10Asec=='yes')
                                    <ul><li>-If yes, the date of disqualification <b>{{$date_of_disqualification}}</b></li></ul>
                                    @endif

                                  </div>


                                  <div class="nomination-signature">
                                    <span class="nomination-date left">Date: - <b>{{$date_of_disloyal}}</b></span>

                                  </div>

                                </div>
                              </fieldset>


                              <fieldset class="fullwidth">
                             
                              <div id="affidavit-preview" class="affidavit-preview">
                                <embed src="<?php echo $affidavit; ?>" width='100%' height='500px' />
                              </div>
                              
                              
                            </fieldset>


                            </div>



                             <div class="fullwidth" style="margin-top: 30px;"> 
          <div class="form-group">
            <!-- <div class="col">
              <a href="" id="" class="btn btn-secondary float-left">Back</a>
            </div> -->
            <div class="col ">
              <div class="form-group row float-right">
            
              <button type="submit" class="btn btn-primary save_next">Verify and Finalize</button>
            </div>
            </div>
            </div>
          </div>



                          </form>
                        </div>






                      </form>
                    </div> 



                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>    
    </section>

  </main>
  @endsection

  @section('script')
  <script>
    $(document).ready(function(){  
     if($('#breadcrumb').length){
       var breadcrumb = '';
       $.each({!! json_encode($breadcrumbs) !!},function(index, object){
        breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
      });
       $('#breadcrumb').html(breadcrumb);
     }
   });
  </script>
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

@endsection