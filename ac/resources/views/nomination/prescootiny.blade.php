@extends('layouts.theme')
@section('title', 'Pre Scrutiny')
@section('content')
<link rel="stylesheet" href="{{ asset('scss/custom-dark.css')}}" />
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
        <div class="container p-0">
          <div class="row"> 
			<?php 	
			
					$nid=$_REQUEST['did'];
					$send='apply-nomination-step-3?nid='.$nid.'&setintosession=ppp';
			
			?>
			<div class="col-md-12 text-center" style="color: #bb4292;">
               <h6> {{ __('finalize.pretext') }}</h6>
			    @if($is_re_finalize==1)
				<span style="color:black;">{{ __('finalize.formfiine') }} </span> 	
				<a target="_blank" href="{{$view_href_cust}}?acs=<?php echo encrypt_String($ac_no); ?>&std=<?php echo encrypt_String($st_code); ?>" style="color: blue; padding: 3px;  border-radius: 3px; text-decoration: underline;">
				{{ __('finalize.viewform') }} 
				</a> 
    		    @else 
				<a target="_blank" href="<?php echo url('/'); ?>/nomination/{{$send}}" style="color: blue; padding: 3px;  border-radius: 3px; text-decoration: underline;">
				{{ __('finalize.Edit_Form') }} 
				</a> 
    		    @endif
			
		
				   
            </div>
			<br>
			<br>
			<br><br>
			   
			
            <div class="col-md-6 text-left">
              @if(isset($reference_id) && isset($href_download_application))
              <div>
                <ul class="list-inline">
                  <li class="list-inline-item text-left">{{ __('election_details.ref') }}: <b style="text-decoration: underline;">{{$reference_id}}</b>
                  </li>
                  <!--<li class="list-inline-item"><a href="{!! $href_download_application !!}" class="btn btn-primary"
                      target="_blank">Download Application</a></li>-->
                </ul>
              </div>
              @endif
            </div>
            
            <div class="col-md-6 text-right">
              <a href="{{url('/nomination/nominations')}}?acs=<?php echo $_REQUEST['acs'];  ?>&std=<?php echo  $_REQUEST['std'];  ?>" class="btn btn-primary">{{ __('step1.Back') }}</a>
            </div>
          </div>
        </div>
      </section>
	  
	  
	  
    <section>
   <div id="main_body_to_show" class="card card-shadow">
     <div class="card-body p-1">	
      <div class="prnt-wrp">
        <div class="sub-scroll">
         <div class="nomin-frm border-four  p-2">
          <h6 class="text-center pt-2 pb-3 border-three"> {{ __('step3.nommessage') }} <span class="nominationvalue"><b>({{$st_name}})</b></span></h6> 
          @if($recognized_party == '1' or $recognized_party == '0')
                        <div class="nomination-parts box recognized">
                          <div class="row">
                            <div class="col-sm-8 col-12">
                              <div class="img-area">
                                <img src="{!! $qr_code !!}" class="rounded img-thumbnail">
                              </div>
                            </div>
                              <div class="col-sm-4 col-12">
                              <div class="img-area">
                                <img src="{!! $profileimg !!}" class="rounded img-thumbnail">
                              </div>
                              </div>
                            </div>

                            <div class="nomination-form-heading text-center border-one">
                              <strong field_part='1' class="part_search">{{ __('finalize.PART1') }}</strong><br>
                              ({{ __('finalize.recognized_party') }})
                            </div>

                            <div field_part='I' class="nomination-detail">
                              <p style="font-size: 15px;">{{ __('finalize.nominate_ac') }}
                                <input type="text" name="legislative_assembly" value="{!! $legislative_assembly !!}" class="highLighter" width="8%">
								{{ __('finalize.Assembly_Constituency') }}.

                                {{ __('finalize.Candidate_name') }} <input type="text" name="name" value="{{$name}}" class="highLighter"> {{ __('finalize.Father_husband_mother') }} <input type="text" name="father_name" value="{!! $father_name !!}" class="highLighter">

                                {{ __('finalize.His_postal_address') }}  <input type="text" name="address" value="{!! $address !!}" class="highLighter">

                                {{ __('finalize.His_name_is_entered_at_Sl') }} <input type="text" name="serial_no" value="{{$serial_no}}" class="highLighter">

                                {{ __('finalize.in_Part_No') }} <input type="text" name="part_no" value="{{$part_no}}" class="highLighter">

                                {{ __('finalize.of_the_electoral_roll_for') }}
                                <input type="text" name="resident_ac_no" value="{{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?>" class="highLighter">
                                {{ __('finalize.Assembly_Constituency') }}.<br><br>

                                <!-- Recognised Party Proposer Detail -->
                              {{ __('finalize.My_name_is') }} <input type="text" name="proposer_name" value="{{$proposer_name}}" class="highLighter">

                               {{ __('finalize.and_it_is_entered_at_Sl') }}  <input type="text" name="proposer_serial_no" value="{{$proposer_serial_no}}" class="highLighter">

                                {{ __('finalize.in_Part_No') }}  <input type="text" name="proposer_part_no" value="{{$proposer_part_no}}" class="highLighter">

                               {{ __('finalize.of_the_electoral_roll_for') }} 
                                <input type="text" name="proposer_assembly" value="{{$proposer_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $proposer_assembly); ?>" class="highLighter">
                                {{ __('finalize.Assembly_Constituency') }}.</p>
                            </div>
                            <div class="nomination-signature">
                              <span class="nomination-date left">{{ __('finalize.Date') }} 
                                <input type="text" name="apply_date" value="{{$apply_date}}" class="highLighter">
                              </span>
							  <span class="nomination-date right">{{ __('finalize.Signature_of_the_Proposer') }} 
                              </span>
                            </div>
                          </div>
                          @else
                          <div class="nomination-parts box not-recognized">
                            <div class="row">
                              <div class="col-sm-8 col-12"></div>
                              <div class="col-sm-4 col-12 pt-3">
                                <div class="img-area">
                                  <img src="{!! $profileimg !!}">
                                </div>
                              </div>
                            </div>
                            <div class="nomination-form-heading border-one">
                              <strong > {{ __('finalize.PART2') }} </strong><br>
                              ({{ __('finalize.UN_recognized_party') }})
                            </div>
                            <div class="nomination-detail" field_part='II'>
							<?php $acc=''; 
							$acc = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?>
                              <div style="font-size: 15px;"> {{ __('finalize.nominate_ac') }} -<input type="text" name="legislative_assembly" value="{{$legislative_assembly}}-<?php echo $acc; ?>" class="highLighter" style="width: 205px;">  
							  {{ __('finalize.Assembly_Constituency') }}
                                <br>

                                {{ __('finalize.Candidate_name') }} <input type="text"  name="name" value="{{$name}}" class="highLighter"> {{ __('finalize.Father_husband_mother') }} <input type="text" name="father_name" value="{!! $father_name !!}" class="highLighter" >

                                {{ __('finalize.His_postal_address') }} <input type="text" name="postal_add" value="{!! $address !!}" class="highLighter" style="width: 425px;">

                                 {{ __('finalize.His_name_is_entered_at_Sl') }} <input type="text" name="serial_no" value="{{$serial_no}}" class="highLighter" style="width: 50px;">

                                 {{ __('finalize.in_Part_No') }} <input type="text" name="part_no" value="{{$part_no}}" class="highLighter" style="width: 50px;">

                                 {{ __('finalize.of_the_electoral_roll_for') }}
                                <input type="text" name="resident_ac_no" value="{{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?>" class="highLighter" style="width: 80px;">
                                {{ __('finalize.Assembly_Constituency') }}.<br><br>

                                <div class="nomination-signature">
                                  <span class="nomination-date left">{{ __('finalize.Date') }} 
                                    <input type="text" name="apply_date" value="{{$apply_date}}" class="highLighter" style="width: 95px;">
                                  </span>

                                </div>
                              <p class="pt-4">
                                {{ __('finalize.We_declare_that_we_are_electors') }}: -
                              </p>  
                              </div>

                              <div class="table-heading" style="text-align:center;margin-top:20px;">
                                <h6>{{ __('finalize.Particulars_of_the_proposers') }}<h6>
                              </div>
                              <table class="table table-bordered proposers-table" >
                                <thead>
                                  <tr>
                                    <th>{{ __('finalize.serial_no') }}</th>
                                    <th colspan="2">{{ __('finalize.Elector_Roll_No') }}</th>
                                    <th>{{ __('finalize.Full_Name') }}</th>
                                    <th>{{ __('finalize.Date') }}</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>&nbsp;</td>
                                    <td>{{ __('finalize.Part_No_of_Electoral') }}</td>
                                    <td>{{ __('finalize.SNo_in_that_part') }}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                  </tr>
                                  <?php $i=1;
                                  foreach($non_recognized_proposers as $iterate_proposer){ ?>
                                  <tr class="non_recognized_proposers_row">
                                    <!--<td>{{$iterate_proposer['s_no']}}</td>-->
                                    <td>{{$i}}</td>
                                    <td><input type="text" name="non_recognized_proposers['part_no']" value="@if($iterate_proposer['part_no']!=0){{$iterate_proposer['part_no']}}@endif" class="highLighter">
                                    </td>
                                    <td><input type="text" name="non_recognized_proposers['serial_no']" value="@if($iterate_proposer['serial_no']!=0){{$iterate_proposer['serial_no']}}@endif" class="highLighter">
                                    </td>
                                    <td><input type="text" name="non_recognized_proposers['fullname']" value="{{$iterate_proposer['fullname']}}" class="highLighter" style="width: 100%;"></td>
                                   <td><input type="text" name="non_recognized_proposers['details']" value="@if($iterate_proposer['part_no']!=0 or
                                      $iterate_proposer['serial_no']!=0 or   $iterate_proposer['fullname']!=0 )  {{$iterate_proposer['date']}}
                                      @endif" class="highLighter" style="width: 100%;">
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
                              <strong>{{ __('finalize.PART3') }}</strong>
                            </div>
                            
                            <div class="nomination-detail" field_part='III'>
                            <div>
                              <p style="font-size: 14px;">{{ __('finalize.I_the_candidate_mentioned') }}—</p>
                              <ul style="font-size: 14px;">
                                <li>(a) {{ __('finalize.I_AM_ACITIZEN') }}.</li>
                                <li>(b) {{ __('finalize.that_I_have_completed') }} <input type="text" name="age" value="{{$age}}" class="highLighter" style="width: 35px;"> {{ __('finalize.years_of_age') }} <br>
  
                                  [ {{ __('finalize.STRIKE_OUT') }} ]</li>
  
                                <div class="nomination-options strikeout">
  
                                  @if($recognized_party == 'recognized')
                                  <div class="checkbox recognised" style="">
                                    <label>(c) (i) {{ __('finalize.I_am_set_up') }} <input type="text" name="party_id" value="{{$party_id}}" class="highLighter"> {{ __('finalize.party_which_is_recognized') }} </label>
                                  </div>  
                                  @else
                                  <div class="checkbox not-recognized">
                                    <label>(c) (ii) {{ __('finalize.I_am_set_up') }} <input type="text" name="party_id" value="{{$party_id}}" class="highLighter">
                                      {{ __('finalize.party_which_is_UN_recognized') }}:— <br>
                                      (i) <input type="text" name="suggest_symbol_1" value="{{$suggest_symbol_1}}" class="highLighter"> (ii) <input type="text" name="suggest_symbol_2" value="{{$suggest_symbol_2}}" class="highLighter"> (iii)
                                      <b><input type="text" name="suggest_symbol_3" value="{{$suggest_symbol_3}}" class="highLighter"></b></label>
                                  </div>
                                  @endif
  
                                </div>
  
                                <li>(d) {{ __('finalize.my_name_and_my_father') }} <input type="text" name="language" value="{{$language}}" class="highLighter" style="width: 100px;"></li>
                                <li>(e) {{ __('finalize.That_to_the_best_of_my_knowledge_and_belief') }}.</li>
                              </ul>
                            </div>
  
                            <div class="nomination-detail">
                              <p style="font-size: 14px;">* {{ __('finalize.I_further_declare') }} 
                                <input type="text" name="category" value="{{$category}}" class="highLighter" style="width: 125px;">
								** {{ __('finalize.Caste_tribe_which') }}   
                                 {{ __('finalize.That_to_the_best_of_my_knowledge') }}. </p>
								 <br>
                            </div>
  
                            <div class="nomination-signature">
                              <span class="nomination-date left">{{ __('finalize.Date') }}  <input type="text" name="part3_date" value="{{$part3_date}}" class="highLighter" style="width: 95px;"></span>
							  
							   <span class="nomination-date right">{{ __('finalize.Signature_of_Candidate') }} </span>
                            </div>
							
  
                            <div class="nomination-note py-3">
                              <p>
                              * {{ __('finalize.Score_out_this_paragraph') }}.</p>
  
                             <p> * {{ __('finalize.Score_out_the_words') }}.</p>
  
                              <p>**  N.B.—A {{ __('finalize.recognized_political_party_text') }}</p>
                            </div>
                          </div>

                          <div class="nomination-parts">
                            <div class="nomination-form-heading border-one">
                              <strong>{{ __('finalize.PART3A') }} </strong><br>
                              ({{ __('finalize.To_be_filled_by_the_candidate') }})
                            </div>
                            
                            <div class="nomination-detail" field_part='IIIA'>
                            <div>
                              <div class="criminal-section">
                                <p style="font-size: 14px;">(1)  {{ __('part3a.whether') }}— </p>
                                <ul style="font-size: 14px;">
                                  <li>(i)  {{ __('part3a.conv') }}— <ul>
                                      <li>(a) {{ __('part3a.offe') }}</li>
                                      <li>(b) {{ __('part3a.oro') }}  -</li>
                                    </ul>
                                  </li>
                                  <li>(ii) {{ __('part3a.impo') }}. <input type="text" name="have_police_case" value="{{$have_police_case}}" class="highLighter" style="width: 65px;"></li>
                                </ul>
                              </div>
  
                              <!-- Police Case -->
                              @if($have_police_case == 'yes')
                              <div class="criminal-section have_police_case_div field_wrapper">
  
                                <p>{{ __('part3a.ifye') }}:</p>
  
                                <?php $i = 1; ?>
                                <div class="fullwidth have_police_case_record">
  
                                  <table>
                                    <tbody class="police_case_body">
                                      @foreach($police_cases as $iterate_police_case)
  
                                      <tr>
                                        <td>
                                          <h3>{{ __('part3a.case') }} {{$i}}</h3>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                          <ul>
                                            <li>(i) {{ __('part3a.ca1') }} -
                                            <input type="text" name="{{$i.'-'."iterate_police_case['case_no']"}}" value="{{$iterate_police_case['case_no']}}" class="highLighter"  style="width: 65px;"></li>
                                            <li>(ii) {{ __('part3a.pol') }} -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['police_station']"}}" value="{{$iterate_police_case['police_station']}}" class="highLighter"  style="width: 65px;"><br>
                                              {{ __('part3a.st') }}  -
                                            <input type="text" name="{{$i.'-'."iterate_police_case['st_code']"}}" value="<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($iterate_police_case['st_code']); ?>" class="highLighter"  style="width: 65px;">
                                              {{ __('part3a.dist') }}  -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['case_dist_no']"}}" value="{{$iterate_police_case['case_dist_no']}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getDist($iterate_police_case['st_code'], $iterate_police_case['case_dist_no']); ?>" class="highLighter"  style="width: 65px;">
                                            </li>
                                            <li>(iii)  {{ __('part3a.sec1') }}  -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['convicted_des']"}}" value="{{$iterate_police_case['convicted_des']}}" class="highLighter">
                                            </li>
  
                                            <li>(iv) {{ __('part3a.cdat') }} -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['date_of_conviction']"}}" value="{{$iterate_police_case['date_of_conviction']}}" class="highLighter">
                                            </li>
  
                                            <li>(v) {{ __('part3a.cour') }}  -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['court_name']"}}" value="{{$iterate_police_case['court_name']}}" class="highLighter">
                                            </li>
  
                                            <li>(vi) {{ __('part3a.puni') }}  -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['punishment_imposed']"}}" value="{{$iterate_police_case['punishment_imposed']}}" class="highLighter">
                                            </li>
  
                                            <li>(vii) {{ __('part3a.rele') }} -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['date_of_release']"}}" value="{{$iterate_police_case['date_of_release']}}" class="highLighter">
                                            </li>
  
                                            <li>(viii) {{ __('part3a.aga') }} -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['revision_against_conviction']"}}" value="{{$iterate_police_case['revision_against_conviction']}}" class="highLighter">
                                            </li>
  
                                            <li>(ix) {{ __('part3a.agad') }}
                                              -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['revision_appeal_date']"}}" value="{{$iterate_police_case['revision_appeal_date']}}" class="highLighter">
                                            </li>
  
                                            <li>(x) {{ __('part3a.revf') }} -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['rev_court_name']"}}" value="{{$iterate_police_case['rev_court_name']}}" class="highLighter">
                                            </li>
  
  
                                            <li>(xi) {{ __('part3a.dips') }}  -
                                              <input type="text" name="{{$i.'-'."iterate_police_case['status']"}}" value="{{$iterate_police_case['status']}}" class="highLighter">
                                            </li>
  
                                            <li class="statusReport">(xii) {{ __('part3a.diee') }} —<br>
                                              <ul>
                                                <li>(a) {{ __('part3a.didd') }} -
                                                  <input type="text" name="{{$i.'-'."iterate_police_case['revision_disposal_date']"}}" value="{{$iterate_police_case['revision_disposal_date']}}" class="highLighter">
                                                </li>
                                                <li>(b) {{ __('part3a.nat') }} -
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
                              <p>(2) {{ __('part3a.prop') }}<input type="text" value="{{$profit_under_govt}}" class="highLighter" style="width: 65px;"></p>
                              @if($profit_under_govt == 'yes')
                              <ul>
                                <li> {{ __('part3a.ifyes1') }}  <input type="text" value="{{$office_held}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(3)  {{ __('part3a.inso') }} 
                                <input type="text" name="court_insolvent" value="{{$court_insolvent}}" class="highLighter"  style="width: 65px;"></p>
                              @if($court_insolvent == 'yes')
                              <ul>
                                <li>-  {{ __('part3a.disc') }} <input type="text" name="discharged_insolvency" value="{{$discharged_insolvency}}" class="highLighter"  style="width: 65px;">
                                </li>
                              </ul>
                              @endif
  
                              <p>(4)  {{ __('part3a.alle') }}
                                <input type="text" name="allegiance_to_foreign_country" value="{{$allegiance_to_foreign_country}}" class="highLighter"  style="width: 65px;"></p>
                              @if($allegiance_to_foreign_country == 'yes')
                              <ul>
                                <li>- {{ __('part3a.alled') }}  <input type="text" name="country_detail" value="{{$country_detail}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(5) {{ __('part3a.disq') }}  <input type="text" name="disqualified_section8A" value="{{$disqualified_section8A}}" class="highLighter"  style="width: 65px;"></p>
                              @if($disqualified_section8A == 'yes')
                              <ul>
                                <li>- {{ __('part3a.peri') }} <input type="text" name="disqualified_period" value="{{$disqualified_period}}" class="highLighter"></li>
                              </ul>
                              @endif
  
                              <p>(6) {{ __('part3a.corr') }}
                                <input type="text" name="disloyalty_status" value="{{$disloyalty_status}}" class="highLighter"  style="width: 65px;"></p>
                              @if($disloyalty_status == 'yes')
                              <ul>
                                <li>- {{ __('part3a.cord') }}  <input type="text" name="date_of_dismissal" value="{{$date_of_dismissal}}" class="highLighter"  style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(7) {{ __('part3a.subs') }}  <input type="text" name="subsiting_gov_taken" value="{{$subsiting_gov_taken}}" class="highLighter" style="width: 65px;"></p>
                              @if($subsiting_gov_taken == 'yes')
                              <ul>
                                <li>- {{ __('part3a.subp') }}
                                  <input type="text" name="subsitting_contract" value="{{$subsitting_contract}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(8) {{ __('part3a.agen') }}<input type="text" name="managing_agent" value="{{$managing_agent}}" class="highLighter" style="width: 65px;"></p>
                              @if($managing_agent == 'yes')
                              <ul>
                                <li>- {{ __('part3a.aged') }} <b></b><input type="text" name="gov_detail" value="{{$gov_detail}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
  
                              <p>(9)  {{ __('part3a.comm') }}  <b></b><input type="text" name="disqualified_by_comission_10Asec" value="{{$disqualified_by_comission_10Asec}}" class="highLighter" style="width: 65px;"></p>
                              @if($disqualified_by_comission_10Asec=='yes')
                              <ul>
                                <li>- {{ __('part3a.comd') }} <b></b><input type="text" name="date_of_disqualification" value="{{$date_of_disqualification}}" class="highLighter" style="width: 65px;"></li>
                              </ul>
                              @endif
                              <div class="nomination-signature mt-2">
                                <span class="nomination-date left">{{ __('finalize.Date') }}: - <input type="text"  name="date_of_disloyal" value="{{$date_of_disloyal}}" class="highLighter" style="width: 95px;"></span>
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
  
  @if($prescrutiny_status == '0' || $prescrutiny_status == '2')
  <section>
    @if($prescrutiny_status == '0')
    <div class="row">
      <div class="col-md-12 text-center">
        <button id="clear_pre_scrutiny" type="button" class="btn btn-success">{{ __('finalize.Clear_Pre_Scrutiny') }}</button>
      </div>
    </div>
    @endif
    <h5 class="text-center">{{ __('finalize.Pre_scrutiny_defects') }}</h5>
		<div class="card card-shadow">
			<div class="card-body defect-bg p-1">
      <form id="comment_form" action="{{ url('roac/submit_prescrutiny_deatils') }}" method="post">
        @csrf
      <input type="hidden" name="nomination_no" value="{{ isset($nomination_id) ? encrypt_string($nomination_id) : ''  }}">
        <div class="prnt-wrp">
		 <div id="comment_box" class="sub-scroll p-3">
		  <?php $k=0; ?>		 
          @if($prescrutiny_status != '0')
          @if(count($comment_section)>0)
            @foreach ($comment_section as $item)
			   <?php $k++; ?>	
              <div class="defect-reslt">
                <div class="row">
                  <div class="col-md-12 col-12">
                    @php $part_state = app(App\Http\Controllers\Nomination\NominationController::class)->getformated_part($item['form_part_no']); @endphp
					
                  <div class="hghted-txt ">{{ $item['defect'].' (PART '.$part_state.')' }}
				  
				  </div> 
				  
                  </div>  
                </div>
                <div class="commt-wrap py-2">
                  <div class="row">
                  <div class="col-md-9 col-12">
                  <textarea class="comnt-box" disabled>{{ $item['remark'] }}</textarea>
				  @if($item['is_defect_resolved']!='1')
				    <div class="col-md-12 text-right" onclick="return markAsResolved({{$item['id']}});" style="cursor:pointer;color:white;"
						id="notdone_{{$item['id']}}">
					  <a class="btn btn-primary">{{ __('finalize.Mark_As_Resolved') }}</a> 
					</div>
				 @else 
				  <div class="col-md-12 text-right" style="cursor:pointer;color:white;" id="done_{{$item['id']}}">
					  <a style="color: black; font-size: 12px; font-style: oblique;">{{ __('finalize.Defect_Resolved') }}
					  <?php 
					  $rdt='';
					  if(!empty($item['defect_resolved_datetime'])){
						$exp = '';
						$exp = explode(" ", $item['defect_resolved_datetime']);
						$yrdata= strtotime($item['defect_resolved_datetime']);
						$dt = date('d M Y', $yrdata).' ';
						$tm = $exp[1];
						$rdt='('.$dt.' '.$tm.')';
					}
					  ?>
					  {{$rdt}}</a>
					</div>
				 @endif		
                </div>
                  </div>
                </div> 
                </div>
            @endforeach
          @endif
          @endif
		  
		  @if($k==0)
			 <div class="col-md-9 col-12"> 
              {{ __('finalize.Defect_not_mark') }}
			 </div>	
		  @endif
         </div><!-- End Of sub-scroll Div -->
         @if($prescrutiny_status == '0')
				<div id="submit_button" class="foot-box" style="display:none"> 
          <button id="submit_form_preview" type="button" class="btn btn-primary"> {{ __('finalize.Proceed') }}</button>
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

	<!-- bank_model-->
    <div class="modal fade modal-confirm" id="defect_popup">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title"></h5> 
		<div class="header-caption">
		  <p> {{ __('finalize.are_you') }}</p>	
		</div>		
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal" onclick="return markAsResolvedVal();">{{ __('finalize.Yes') }}</button>
        </div>
		<span style="text-align: center;display:none;" id="loader">
		 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('finalize.Please_Wait') }}
		</span>
        
      </div>
    </div>
  </div><!-- End Of bank_model --> 
  
  <!-- bank_model-->
    <div class="modal fade modal-confirm" id="defect_popup_resolved">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title"></h5> 
		<div class="header-caption">
		  <p>{{ __('finalize.succ') }}</p>	
		</div>		
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('finalize.Ok') }}</button>
        </div>
      </div>
    </div>
  </div><!-- End Of bank_model --> 
  
  
  <!-- bank_model-->
    <div class="modal fade modal-confirm" id="defect_popup_issue">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title"></h5>
		<div class="header-caption">
		  <p>{{ __('finalize.some_issue') }}</p>	
		</div>		
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('finalize.Ok') }}</button>
        </div>
      </div>
    </div>
  </div><!-- End Of bank_model --> 
  
  
  <input type="hidden" id="did" name="did">
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
function markAsResolved(id){
	var j = jQuery.noConflict();
	j("#loader").hide();
	j("#did").val(id);
	j('#defect_popup').modal('show');
}


function markAsResolvedVal(){
	var j = jQuery.noConflict();	
	var didid = j("#did").val();
	j("#loader").show();
	j.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/mark-defect-as-resolved", 
				data: {
					"_token": "{{ csrf_token() }}",
					"rid": didid
					},
				dataType: "html",
				success: function(msg){ 
				  if(msg==1){
					var j = jQuery.noConflict();
					window.location.reload(true);
					$("#done_"+didid).html("Defect Reolved");
					j('#defect_popup_resolved').modal('show'); 
				  } else {
					var j = jQuery.noConflict();
					j('#defect_popup_issue').modal('show');	
				  }
				},
				error: function(error){
					console.log("Error"+error);
					console.log(error.responseText);				
					var obj =  j.parseJSON(error.responseText);
				}
			});
}





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
      <?php if($prescrutiny_status == '0') { ?>

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
            if(data_array_to_show.length>0){
              $('#clear_pre_scrutiny').hide();
            }
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
    $.each(comment_data, function(key, value){ console.log(value['column_name']);
      $("input[name='"+value['column_name']+"']").addClass('selected-filled');
    });
});

</script>
@endsection