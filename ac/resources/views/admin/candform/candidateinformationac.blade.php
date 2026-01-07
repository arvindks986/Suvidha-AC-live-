@extends('admin.layouts.ac.theme')
@section('title', 'Nomination')
@section('bradcome','Nomination')
@section('content')
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
<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom-dark.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
<?php 
    $getDetails =getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
    $st=getstatebystatecode($ele_details->ST_CODE);
    $dist=getdistrictbydistrictno($ele_details->ST_CODE,$user_data->dist_no);
    $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
    $all_state=getallstate();
    $all_dist=getalldistrictbystate($ele_details->ST_CODE);
    $all_ac=getacbystate($ele_details->ST_CODE);
    $partyd= getallpartylist();   
		$symb= getsymbollist();
	 	$url = URL::to("/");  
	 	$sys_id=$symbol_id; 
	 	if($sys_id=='0' || $sys_id=='') $sys_id=200;
	 	//dd($persoanlDetails);
	?>
<div class="container">
  <div class="step-wrap mt-4">
    <ul class="text-center">
      <li class="step-current"><b>&#10004;</b><span>Verify Nomination Details</span></li>
      <li class=""><b>&#10004;</b><span>Decision by RO (Part IV)</span></li>
      <li class=""><b>&#10004;</b><span>Genrate Receipt (Part VI)</span></li>
      <li class=""><b>&#10004;</b><span>Print Receipt</span></li>
    </ul>
  </div>
</div>
<main class="pb-5 pl-5 pr-5">
  @if(isset($reference_id) && isset($href_download_application))
  <div class="container">
    <div class="col-md-5 float-right">
      <ul class="list-inline float-right">
        <li class="list-inline-item text-right">Reference ID: <b
            style="text-decoration: underline;">{{$reference_id}}</b>
        </li>
        {{-- <li class="list-inline-item text-right"><a style="background: #D04A8A;" href="{!! $href_download_application !!}"
          class="btn btn-primary" target="_blank">Download Application</a></li> --}}
      </ul>
    </div>
  </div>
  @endif
  <div class="container">
    <div class="card card-shadow">
      <div class="card-body">

        <form class="form-horizontal" id="election_form" method="POST" action="{{url('roac/candidatevalidation') }}">
          {{ csrf_field() }}
          <input type="hidden" name="candidate_id" value="{{$candidate_id}}">
          <input type="hidden" name="nom_id" value="{{$id}}">
          <input type="hidden" name="nomination_no" value="{{$nomination_no}}">

          <table class="customTable">
            <tbody>
                <tr class="text-right">
                    <td>
                      <a style="background: #D04A8A;" href="{{ url('roac/apply-nomination-step-2/'.$encrypt_id) }}" class="btn btn-primary">Edit Details</a>
                    </td>
                    @if(!empty($affidavitId))
                    <td>
                      <a style="background: #D04A8A;" href="{{ url('roac/affidavitdashboard/edit/'.$affidavitId) }}" class="btn btn-primary">Edit Affidavit</a>
                    </td>
                    @endif
                  </tr>
              <tr>
                <td class="td-center"><h5>{{ __('step3.form2b') }}</h5></td>
              </tr> 
              <tr>
                <td class="td-center">({{ __('step3.rule4') }})</td>
              </tr> 
              <tr>
                <td class="td-center"><h5>{{ __('step3.nomp') }}</h5></td>
              </tr> 
              <tr>
                <td class="td-center"><i>{{ __('step3.nommessage') }}<span>{{$st_name}}</span>({{ __('finalize.State') }}) </i></td>
              </tr> 
              <tr>
                <td><div class="col-lg-2 pull-left">
                           <?php if($qr_code!='NA') { ?>
                            <img src="{!! $qr_code !!}" style="max-width: 150px;">
                            <?php } ?>
                          </div>
                <div class="passport-img">
                <!--<img src="#" alt="">-->
                <img src="{!! $profileimg !!}" style="height: 160px;">
            </div>
         </td>
      </tr>	
      
      
      
      
      
      <tr>
        <td class="td-center">{{ __('finalize.STRIKE_OFF') }}</td>
      </tr>
    
     @if($recognized_party == '1' or $recognized_party == '0' or $recognized_party == '3')	
      <tr>
        <!--<td class="td-center td-bold">PART I</td>-->
        <td class="td-center"><div class="pt-one"><b>{{ __('finalize.PART1') }}</b></div></td>
      </tr> 
      <tr>
        <td class="td-center">({{ __('finalize.recognized_party') }}) </td> 
      </tr> 
      <tr>
          <td>{{ __('finalize.nominate_ac') }}<span><b>&nbsp; {{$legislative_assembly}}-  <?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?></b></span>{{ __('finalize.Assembly_Constituency') }}. </td>
      </tr>	
     <tr>
         <td class="param-area">
            <p>{{ __('finalize.Candidate_name') }}<span> <b>&nbsp;{{$name}}</b></span> {{ __('finalize.Father_husband_mother') }} <span>&nbsp; <b>{!! $father_name !!}</b></span>{{ __('finalize.His_postal_address') }}<span style="width:auto;">&nbsp;  <b>{!! $address !!}</b> </span> {{ __('finalize.His_name_is_entered_at_Sl') }} <span>&nbsp; <b>{{$serial_no}}</b></span> {{ __('finalize.in_Part_No') }} <span>&nbsp;<b>{{$part_no}}</b></span> {{ __('finalize.of_the_electoral_roll_for') }} <span>&nbsp; {{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?></b></span>{{ __('finalize.Assembly_Constituency') }}. 
            </p>
            <br>
            <p> {{ __('finalize.My_name_is') }} <span>&nbsp;<b>{{$proposer_name}}</b> </span> {{ __('finalize.and_it_is_entered_at_Sl') }} <span>&nbsp; <b>{{$proposer_serial_no}}</b> </span> {{ __('finalize.in_Part_No') }} <span>&nbsp; <b>{{$proposer_part_no}}</b> </span> {{ __('finalize.of_the_electoral_roll_for') }} <span>&nbsp; <b>{{$proposer_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $proposer_assembly); ?>	</b> </span> {{ __('finalize.Assembly_Constituency') }}. </p>
         </td>
     </tr>
     <tr>
         <td>
          <table style="width: 100%; margin: 1.5rem 0;">
             <tbody>
               <tr>
                   <td>{{ __('finalize.Date') }} <span>&nbsp; <b>{{$apply_date}}</b></span></td>
                   <td class="td-right">
                    <div>{{ __('finalize.Signature_of_the_Proposer') }} </div>
                </td>
               </tr> 
             </tbody>
          </table> 
        </td>
     </tr>
     
     
     <!-- For Strike -->
     
     
    @if($recognized_party != '3')	 
     <tr>
        <!--<td class="td-center td-bold bordr-one"><div class="pt-one">PART II</div> </td>-->
        <td class="td-center td-bold"><div class="pt-one">{{ __('finalize.PART2') }}</div> </td> 
      </tr> 
      <tr>
          <td class="param-area">
          <p>
            <hr style="width: 85%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">{{ __('finalize.nominate_ac') }} <span>&nbsp; <b></b></span>{{ __('finalize.Assembly_Constituency') }}. </hr>
          </p>  
          <p><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"> {{ __('finalize.Candidate_name') }}<span>&nbsp; <b></b></span>{{ __('finalize.Father_husband_mother') }}<span>&nbsp; <b></b></span>{{ __('finalize.His_postal_address') }}<span style="width:auto;">&nbsp;  <b></b> </span></hr>
          
        <hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	{{ __('finalize.His_name_is_entered_at_Sl') }} <span>&nbsp; <b></b></span> {{ __('finalize.in_Part_No') }} <span>&nbsp; <b></b></span><span>&nbsp; <b></b></span>{{ __('finalize.Assembly_Constituency') }}. </hr>
             
        </p>	
        </td>
      </tr>	
        <tr>
            <td>
              <p><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">
                {{ __('finalize.We_declare_that_we_are_electors') }}:- </hr>
              </p>
            </td>
        </tr>
        <tr> 
            <td class="td-center"><h6 class="pt-one"><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	{{ __('finalize.Particulars_of_the_proposers') }}</hr></h6></td>
        </tr>
        
        <tr>
            <td>
              <table style="width:100%; text-align: center;" border="1">
                  <tr>
                      <th style="width: 55px;">{{ __('finalize.serial_no') }}</th>
                      <th style="padding: 0;">
                      <table style="width:100%;" border="0">
                            <tr>
                                <th colspan="2" class="td-center" style="border-bottom: 1px solid #313131;">{{ __('finalize.Elector_Roll_No') }}</th>
                            </tr>
                            <tr>
                                <th  style=" width: 50%; border-right: 1px solid #313131;">{{ __('finalize.Part_No_of_Electoral') }}</th>
                                <th>{{ __('finalize.SNo_in_that_part') }}</th>
                            </tr>
                      </table>  
                    </th>
                    
                      <th>{{ __('finalize.Full_Name') }}</th>
                      <th>{{ __('finalize.Signature') }}</th>
                      <th>{{ __('finalize.Date') }} </th>
                  </tr>
                <?php $i=1;  for($k=0; $k<10; $k++){ ?>
                    <tr>
                        <td>{{$k+1}}</td>
                        <td style="padding: 0;">
                         <table style="width:100%;" border="0">
                           <tr>
                            <td style="width: 50%; border-right: 1px solid #313131;height: 27px;">&nbsp;</td>
                            <td>&nbsp;</td>
                           </tr>	
                         </table>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                
                <?php } ?>
              </table>
            </td>
        </tr>
        <tr>
            <td>
                <div class="pb-three"><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	<strong>N.B.-</strong>{{ __('finalize.There_should_be') }} .</hr></div>
            </td>
        </tr> 
        
    @endif	
     
     <!-- EndForStrike -->
     
     <br>
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     @endif
      @if($recognized_party == '2' or $recognized_party == '3')
          
        @if($recognized_party != '3')
      <!-- Strike Start  -->			  
      <tr>
        <!--<td class="td-center td-bold">PART I</td>-->
        <td class="td-center"><div class="pt-one"><b>{{ __('finalize.PART1') }}</b></div></td>
      </tr> 
      <tr>
        <td class="td-center"><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">({{ __('finalize.recognized_party') }}) </hr></td> 
        
      </tr> 
      <tr>
          <td><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">{{ __('finalize.nominate_ac') }}<span><b>&nbsp; </hr>  </td>
      </tr>	
     <tr>
         <td class="param-area">
            <p><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">{{ __('finalize.Candidate_name') }}<span> <b>&nbsp;</b></span> {{ __('finalize.Father_husband_mother') }} <span>&nbsp; <b></b></span>{{ __('finalize.His_postal_address') }} </hr>
            <hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">
            <span style="width:auto;">&nbsp;  <b></b> </span> {{ __('finalize.His_name_is_entered_at_Sl') }} <span>&nbsp; <b> </b></span> {{ __('finalize.in_Part_No') }} <span>&nbsp;<b> </b></span> {{ __('finalize.of_the_electoral_roll_for') }} <span>&nbsp; </b></span>{{ __('finalize.Assembly_Constituency') }}. </hr>
            </p>
            <br>
            
            <p> <hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"> {{ __('finalize.My_name_is') }} <span>&nbsp;<b> </b> </span> {{ __('finalize.and_it_is_entered_at_Sl') }} <span>&nbsp; <b> </b> </span> {{ __('finalize.in_Part_No') }} <span>&nbsp; <b>  </b> </span> </hr>
            
            <hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">{{ __('finalize.of_the_electoral_roll_for') }} <span>&nbsp; <b></b> </span> {{ __('finalize.Assembly_Constituency') }}. </hr> </p>
         </td>
     </tr>
     <tr>
         <td>
          <table style="width: 100%; margin: 1.5rem 0;">
             <tbody>
               <tr>
                   <td><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">{{ __('finalize.Date') }} <span>&nbsp; <b>  </b></span></td>
                   <td class="td-right">
                    <div>{{ __('finalize.Signature_of_the_Proposer') }} </div>
                </td>
               </tr> 
             </tbody>
          </table> 
        </td>
     </tr>
    @endif  
      
      <!--End Strike Start  -->
      
      
      
      
      
      
      
      
      
      
      
      
      
      
    <tr>
        <!--<td class="td-center td-bold bordr-one"><div class="pt-one">PART II</div> </td>-->
        <td class="td-center td-bold"><div class="pt-one">{{ __('finalize.PART2') }}</div> </td> 
      </tr> 
      <tr>
          <td class="param-area">
          <p>
            {{ __('finalize.nominate_ac') }} <span>&nbsp; <b>{{$legislative_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?></b></span>{{ __('finalize.Assembly_Constituency') }}. 
          </p>  
          <p>{{ __('finalize.Candidate_name') }}<span>&nbsp; <b>{{$name}}</b></span>{{ __('finalize.Father_husband_mother') }}<span>&nbsp; <b>{!! $father_name !!}</b></span>{{ __('finalize.His_postal_address') }}<span style="width:auto;">&nbsp;  <b>{!! $address !!}</b> </span>
          
            {{ __('finalize.His_name_is_entered_at_Sl') }} <span>&nbsp; <b>{{$serial_no}}</b></span> {{ __('finalize.in_Part_No') }} <span>&nbsp; <b>{{$part_no}}</b></span>{{ __('finalize.of_the_electoral_roll_for') }} <span>&nbsp; <b>{{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?></b></span>{{ __('finalize.Assembly_Constituency') }}. 
        </p>	
        </td>
      </tr>	
        <tr>
            <td>
              <p>
                {{ __('finalize.We_declare_that_we_are_electors') }}:- 
              </p>
            </td>
        </tr>
        <tr>
            <td class="td-center"><h6 class="pt-one">{{ __('finalize.Particulars_of_the_proposers') }}</h6></td>
        </tr>
        
        <tr>
            <td>
              <table style="width:100%; text-align: center;" border="1">
                  <tr>
                      <th style="width: 55px;">{{ __('finalize.serial_no') }}</th>
                      <th style="padding: 0;">
                      <table style="width:100%;" border="0">
                            <tr>
                                <th colspan="2" class="td-center" style="border-bottom: 1px solid #313131;">{{ __('finalize.Elector_Roll_No') }}</th>
                            </tr>
                            <tr>
                                <th  style=" width: 50%; border-right: 1px solid #313131;">{{ __('finalize.Part_No_of_Electoral') }}</th>
                                <th>{{ __('finalize.SNo_in_that_part') }}</th>
                            </tr>
                      </table>  
                    </th>
                      <th>{{ __('finalize.Full_Name') }}</th>
                      <th>{{ __('finalize.Signature') }}</th>
                      <th>{{ __('finalize.Date') }} </th>
                  </tr>
                <?php $i=1; if(count($non_recognized_proposers)!=0){  
                 foreach($non_recognized_proposers as $iterate_proposer){ ?> 
                  <tr>
                     <td>{{$i}}.</td>
                     <td style="padding: 0;">
                     <table style="width:100%;" border="0">
                       <tr>
                           <td style="width: 50%; border-right: 1px solid #313131;height: 27px;">@if($iterate_proposer['part_no']!=0){{$iterate_proposer['part_no']}}@endif</td>
                           <td>@if($iterate_proposer['serial_no']!=0){{$iterate_proposer['serial_no']}}@endif</td>
                       </tr>	
                     </table>
                    </td>
                     <td>{{$iterate_proposer['fullname']}}</td>
                     <td>&nbsp; {{$iterate_proposer['signature']}}</td>
                     <td>@if($iterate_proposer['part_no']!=0 or 
                                        $iterate_proposer['serial_no']!=0 or
                                        $iterate_proposer['fullname']!=0 )
                                        {{$iterate_proposer['date']}}
                                    @endif	
                    </td>
                 </tr>
                <?php $i++; } } else { for($k=0; $k<10; $k++){ ?>
                    <tr>
                        <td>{{$k+1}}</td>
                        <td style="padding: 0;">
                         <table style="width:100%;" border="0">
                           <tr>
                            <td style="width: 50%; border-right: 1px solid #313131;height: 27px;">&nbsp;</td>
                            <td>&nbsp;</td>
                           </tr>	
                         </table>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                
                <?php } } ?>
              </table>
            </td>
        </tr>
        <tr>
            <td>
                <div class="pb-three"><strong>N.B.-</strong>{{ __('finalize.There_should_be') }} .</div>
            </td>
        </tr> 
       @endif	
        <tr>
         <td class="td-center td-bold bordr-one"><div class="pt-one">{{ __('finalize.PART3') }}</div> </td>
        </tr>
        <tr>
          <td class="param-area">
            <p>{{ __('finalize.I_the_candidate_mentioned') }} -
              </p>
             <p><b>(a)</b> {{ __('finalize.I_AM_ACITIZEN') }} </p> 
             <p><b>(b)</b> {{ __('finalize.that_I_have_completed') }} <span>&nbsp; <b>{{$age}}</b> </span> {{ __('finalize.years_of_age') }} </p>
              <p><h6 class="td-center pt-one pb-three">[ {{ __('finalize.STRIKE_OUT') }} ]</h6></p> 
             
             
            @if($recognized_party==0 or $recognized_party=='1' or $recognized_party=='' or $recognized_party==3)  
            <p><b>(c)</b> (i) {{ __('finalize.I_am_set_up') }} <span style="width: auto;"><b> &nbsp; {{$party_id}} </b> </span>  {{ __('finalize.party_which_is_recognized') }} 
              </p>
              
            <!-- Stike Start -->
                @if($recognized_party!=3)  		
                 <h6 class="td-center">{{ __('finalize.OR') }}</h6>
                 <p><strike><b>(c)</b> (ii) {{ __('nomination.i_am_set_1') }}   <span ><b></b> </span>  {{ __('nomination.i_am_set_3') }} </strike></p>	
                <p>
                    <hr style="width: 85%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">
                      {{ __('part3.spre') }} <span style="width: auto;"><b> &nbsp; 1................................... </b> <b> &nbsp; 2...................................  </b> <b> &nbsp; 3...................................	   </b> </span> 
                    </hr>
                    </p> 
                @endif	  
           <!--End Stike Start -->
            @endif
            
            @if($recognized_party==3)    
                    <h6 class="td-center"> {{ __('finalize.OR') }} </h6> 
            @endif	
            
            @if($recognized_party==2 or $recognized_party==3)	  
            @if($recognized_party!=3)    
              <p><b>(c)</b> (i) <strike> {{ __('finalize.I_am_set_up') }} <span style="width: auto;"><b> &nbsp;</b> </span>  {{ __('finalize.party_which_is_recognized') }} </strike>
              </p>
              <h6 class="td-center"> {{ __('finalize.OR') }} </h6>					  
            @endif  
            

             @if($party_id2!=743)
             <p><b>(c)</b> (ii) {{ __('nomination.i_am_set_1') }}  <span style="width: auto;"><b> &nbsp; <?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getPartyName($party_id2); ?>  </b> </span>  {{ __('nomination.i_am_set_3') }}  / <strike> {{ __('nomination.i_am_set_333') }} </strike></p>	
            @endif
            
            @if($party_id2==743)
             <p><b>(c)</b> (ii) <strike> {{ __('nomination.i_am_set_1') }}  <span style="width: auto;"><b> &nbsp; ...  </b> </span>  {{ __('nomination.i_am_set_3') }}   </strike> / {{ __('nomination.i_am_set_333') }} </p>	
            @endif 


             
            <p><b></b> {{ __('part3.spre') }} <span style="width: auto;"><b> &nbsp; 1. {{$suggest_symbol_1}} </b> <b> &nbsp; 2. {{$suggest_symbol_2}} </b> <b> &nbsp; 3. {{$suggest_symbol_3}} </b> </span> </p> 
            @endif

             
           
             <p><b>(d)</b> {{ __('finalize.my_name_and_my_father') }} <span>&nbsp; <b>{{$language}}</b></span>{{ __('finalize.name_of_the') }}
             </p>
             <p><b>(e)</b> {{ __('finalize.That_to_the_best_of_my_knowledge_and_belief') }} </p>
             
             
             
            @if(!empty($part3_address))				
             <p>
                
         
                * {{ __('finalize.I_further_declare') }} <span>&nbsp; <b>{{$category}}</b></span>** {{ __('finalize.Caste_tribe_which') }}
** {{ __('finalize.Caste_tribe_state') }}
        
        @if($category!='general')
        
        <span>&nbsp;<b><?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_cast_state); ?></b></span>{{ __('finalize.in_relation_to') }} <span>&nbsp;<b>{{$part3_address}}</b></span> {{ __('finalize.in_that_State') }}.   
        
        @else
            
        <hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	
        <span>&nbsp;<b> </b></span>
        {{ __('finalize.in_relation_to') }} <span>&nbsp;
        <b></b></span> {{ __('finalize.in_that_State') }}.
        </hr>	
        @endif
        
     </p> 
             
             
             @else 
                 
             
              <p>
                * {{ __('finalize.I_further_declare') }} <span>&nbsp; <b>{{$category}}</b></span>** {{ __('finalize.Caste_tribe_which') }}
                ** 
                @if($category=='general')
                    
                    <hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	
                      <span>&nbsp;<b> </b></span>
                        {{ __('finalize.in_relation_to') }} <span>&nbsp;
                        <b></b></span> {{ __('finalize.in_that_State') }}.
                    </hr>	
                
                @else
                <span>&nbsp;<b>
                <?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_cast_state); ?></b></span>
                    {{ __('finalize.in_relation_to') }}<span>&nbsp;
                    <b></b></span> {{ __('finalize.in_that_State') }}.
                @endif
                
        
        
             </p>  	 	 
             
             
             @endif  
             <p> 
                {{ __('finalize.That_to_the_best_of_my_knowledge') }} <span>&nbsp;<b><?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_legislative_state); ?></b></span> {{ __('finalize.more_than_two') }}. 
             </p> 
          </td>
        </tr>
       <tr>
         <td>
          <table style="width: 100%; margin: 1.5rem 0;">
             <tbody>
               <tr>
                   <td>{{ __('finalize.Date') }} <span>&nbsp;<b>{{$part3_date}}</b></span></td>
                   <td class="td-right">
                    <div>{{ __('finalize.Signature_of_Candidate') }} </div>
                </td>
               </tr> 
             </tbody>
          </table> 
        </td>
     </tr>
     <tr>
       <td>
         <div class="sm-note">
           * {{ __('finalize.Score_out_this_paragraph') }}.<br>
        ** {{ __('finalize.Score_out_the_words') }}.<br> 
        <b>N.B.—</b> {{ __('finalize.recognized_political_party_text') }}<br>  
         </div> 
       </td>
     </tr>
      <tr>
        <td class="td-center td-bold bordr-one">
            <div class="pt-one">{{ __('finalize.PART3A') }}</div>
            <p>({{ __('step3.To_be_filled_by_the_candidate') }})</p> 
        </td>
      </tr>
     <tr>
       <td>
          <table style="width: 100%">
            <tr>
                <td style="width:80%;">
                  <div class="param-area">
                    <p><b>(1)</b> {{ __('part3a.whether') }}—</p>
                    <div class="sub-area" style="border-right: 1px solid #313131;">
                      <p>(i)  {{ __('part3a.conv') }}— </p> 
                       <ul class="list-area">
                        <li>(a) {{ __('part3a.offe') }} </li>
                        <li>(b) {{ __('part3a.oro') }} </li>
                       </ul>
                      <p>(ii) {{ __('part3a.impo') }}. <b>{{ucfirst($have_police_case)}}</b></p> 
                    </div><!-- End Of sub-area Div -->  
                  </div>
                </td>
                <td style="width:20%" valign="middle">{{ __('part3a.Yes') }}/{{ __('part3a.No') }}</td>
            </tr>
          </table> 
       </td>
     </tr>
     
      @if($have_police_case == 'yes')
     <tr>
         <td>   <?php $i = 1; ?>
            {{ __('part3a.ifye') }}
             @foreach($police_cases as $iterate_police_case)
            <div class="sub-area">
                <p>{{ __('part3a.case') }} <span>&nbsp; <b>{{$i}}</span></b></p> 
                <p>(i) {{ __('part3a.ca1') }}. <span>&nbsp; <b>{{$iterate_police_case['case_no']}}</span></b></p> 
                <p>(ii) {{ __('part3a.pol') }} <span>&nbsp; <b>{{$iterate_police_case['police_station']}}</b></span> {{ __('part3a.dist') }} <span>&nbsp;<b>{{$iterate_police_case['case_dist_no']}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getDist($iterate_police_case['st_code'], $iterate_police_case['case_dist_no']); ?></b></span>&nbsp; {{ __('part3a.st') }}  <span>&nbsp;<b><?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($iterate_police_case['st_code']); ?></b></span>.</p>
                <p>(iii) {{ __('part3a.sec1') }}   <span style="width:100%;">&nbsp;<b>{{$iterate_police_case['convicted_des']}}</b></span></p>
                <p>(iv) {{ __('part3a.cdat') }} <span style="width:100%;"> &nbsp;<b>{{$iterate_police_case['date_of_conviction']}}</b></span></p>
                <p>(v)  {{ __('part3a.cour') }} <span style="width:100%;">&nbsp;<b>{{$iterate_police_case['court_name']}}</b></span></p>
                <p>(vi) {{ __('part3a.puni') }} <span style="width:100%;">&nbsp;<b>{{$iterate_police_case['punishment_imposed']}}</b></span>.</p>					
                 <?php $dt='NA'; ?>		
                  @if($iterate_police_case['date_of_release']!='1970-01-01')
                  <?php $dt=$iterate_police_case['date_of_release']; ?>		
                  @endif	
                
                <p>(vii) {{ __('part3a.rele') }} <span>&nbsp;<b>{{$dt}}</b></span></p>
                <p>(viii) {{ __('part3a.aga') }} <span>&nbsp;<b>{{$iterate_police_case['revision_against_conviction']}}</b></span>{{ __('part3a.Yes') }}/{{ __('part3a.No') }}</p>
                <p>(ix) {{ __('part3a.agad') }}  <span>&nbsp;<b>{{$iterate_police_case['revision_appeal_date']}}</b></span>.</p>
                <p>(x) {{ __('part3a.revf') }}  <span style="width:100%;">&nbsp;<b>{{$iterate_police_case['rev_court_name']}}</b></span></p>
                <p>(xi) {{ __('part3a.dips') }} <span>&nbsp;<b>{{$iterate_police_case['status']}}</b></span></p>
                <p>(xii) {{ __('part3a.diee') }}—</p>
                <ul>
                    <li>(a) {{ __('part3a.didd') }} <span>&nbsp;<b>{{$iterate_police_case['revision_disposal_date']}}</b></span></li>
                    <li>(b) {{ __('part3a.nat') }} <span style="width:100%;">&nbsp;<b>{{$iterate_police_case['revision_order_description']}}</b></span></li>
                </ul>
            </div><!-- End Of sub-area Div -->
              <?php $i++; ?>	
            @endforeach
        </td>
     </tr>
    @endif 
     
     
     <tr>
         <td>
           <b>(2)</b> {{ __('part3a.prop') }}
            <div class="sub-area">
                <p><span>&nbsp;<b>{{ucfirst($profit_under_govt)}}</b></span>({{ __('part3a.Yes') }}/{{ __('part3a.No') }})</p>
                  @if($profit_under_govt == 'yes')
                 <p>-{{ __('part3a.ifyes1') }} <span style="width:100%;">&nbsp;<b>{{ucfirst($office_held)}}</b></span></p>
                 @endif
            </div>
        </td>
     </tr>
  <tr>
    <td>
        <b>(3)</b>  {{ __('part3a.inso') }} <span>&nbsp;<b>{{ucfirst($court_insolvent)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }})
        <div class="sub-area">
          @if($court_insolvent == 'yes')
            <p>- {{ __('part3a.disc') }}<span style="width:100%;">&nbsp;<b>{{ucfirst($discharged_insolvency)}}</b></span></p>
          @endif
        </div>					
    </td>
  </tr>
  <tr>
    <td>
        <b>(4)</b> {{ __('part3a.alle') }}<span>&nbsp; <b>{{ucfirst($allegiance_to_foreign_country)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }})
        <div class="sub-area">
             @if($allegiance_to_foreign_country == 'yes')
            <p>- {{ __('part3a.alled') }}<span style="width:100%;">&nbsp;<b>{{ucfirst($country_detail)}}</b></span></p>
            @endif
        </div>					
    </td>
  </tr>
  <tr>
    <td>
        <b>(5)</b> {{ __('part3a.disq') }}  <span>&nbsp;<b>{{ucfirst($disqualified_section8A)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
        <div class="sub-area">
             @if($disqualified_section8A == 'yes')
            <p>- {{ __('part3a.peri') }}<span>&nbsp;<b>{{ucfirst($disqualified_section8A)}}</b></span></p>
            @endif
        </div>					
    </td>
  </tr>
  <tr>
    <td>
        <b>(6)</b> {{ __('part3a.corr') }} <span>&nbsp;<b>{{ucfirst($disloyalty_status)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
        <div class="sub-area">
             @if($disloyalty_status == 'yes')
            <p>-- {{ __('part3a.cord') }} <span>&nbsp;<b>{{ucfirst($date_of_dismissal)}}</b></span></p>
            @endif
        </div>					
    </td>
  </tr>
  <tr>
    <td>
        <b>(7)</b> {{ __('part3a.subs') }}  <span>&nbsp;<b>{{ucfirst($subsiting_gov_taken)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
        <div class="sub-area">
    @if($subsiting_gov_taken == 'yes')
      <p>-  {{ __('part3a.subp') }}<span style="width:100%;">&nbsp;<b>{{ucfirst($subsitting_contract)}}</b></span></p>
    @endif
        </div>					
    </td>
  </tr>
  <tr>
    <td>
        <b>(8)</b> {{ __('part3a.agen') }}<span>&nbsp;<b>{{ucfirst($managing_agent)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
        <div class="sub-area">
            @if($managing_agent == 'yes')
            <p>- {{ __('part3a.aged') }} <span style="width:100%;">&nbsp;<b>{{ucfirst($gov_detail)}}</b></span></p>
            @endif

        </div>					
    </td>
  </tr>
  <tr>
    <td>
        <b>(9)</b> {{ __('part3a.comm') }} <span>&nbsp;<b>{{ucfirst($disqualified_by_comission_10Asec)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
        <div class="sub-area">
            @if($disqualified_by_comission_10Asec=='yes')
            <p>- {{ __('part3a.comd') }} <span>&nbsp;<b>{{ucfirst($date_of_disqualification)}}</b></span></p>
            @endif
        </div>					
    </td>
  </tr>
   <tr>
         <td>
          <table style="width: 100%; margin: 1.5rem 0;">
             <tbody>
               <tr>
                   <td>
                    <div>{{ __('finalize.Place') }}: </div>
                    <div>{{ __('part3a.Date') }}: <b>{{$date_of_disloyal}}</b></div>
                </td>
                   <td class="td-right">
                    <div>{{ __('finalize.Signature_of_Candidate') }}</div>
                </td>
               </tr> 
             </tbody>
          </table> 
        </td>
     </tr>
     
      <tr>
         <td>
          <table style="width: 100%; margin: 1.5rem 0;">
             <tbody>
               <tr>
                   <td>
    <?php if( $affidavit!='NA'){ ?>				
     <fieldset class="fullwidth">
      <div id="affidavit-preview" class="affidavit-preview">
        <embed src="<?php echo $affidavit; ?>" width='100%' height='500px' />
      </div>
    </fieldset>
    <?php } ?>
                </td>
                   <td class="td-right">
                    
                </td>
               </tr> 
             </tbody>
          </table> 
        </td>
     </tr>
     <tr>
        <td class="td-center td-bold bordr-one">
          <h5 class="pt-one">Candidate Details</h5>
          <h6>(This Data Will be Shown Publicly)</h6>
        </td>
      </tr>
   </tbody> 
   </table>
              <!-- Nomination View Details -->

              <main role="main" class="inner cover mb-3">

                <div autocomplete='off' class="disabled">
                <section class="mt-5">
                <div class="container">
                  <div class="row">
                    <div class="col-md-12">
                    <div class="card">
                      <div class="card-header d-flex align-items-center">
                        <h4>Candidate</h4>
                      </div>
                      <div class="card-body">
                      <div class="row">
                        <div class="col-md-3">
                          <div class="avatar-upload">
                            @if($profileimg != '' )
                            <div class="avatar-preview">
                              <div id="imagePreview">
                                <img src="{!! $profileimg !!}" style="height: 160px;">
                              </div>
                            </div>
                            @else
                              <div class="avatar-preview">
                                <div id="imagePreview">
                                  <img src="{{asset('img/avtar.jpg')}}" style="height: 160px;">
                                </div>
                              </div>
                            @endif
                            <div class="profileerrormsg errormsg errorred"></div>
                          </div>
                          <?php /*<img class="rounded-circle" src="{{ asset('admintheme/img/vendor/avtar.jpg')}}" alt="" />*/?>
                        </div>
                        <div class="col">                  
                        <div class="form-group row mt-5">
                          <label class="col-sm-4">Party Name <sup>*</sup></label>
                          <div class="col-sm-8">
                            <div class="" style="width:100%;">
                              <select name="party_id" class="form-control party_id" disabled>
                                <option value="">-- Select Party --</option>
                                @foreach($partyd as $Party)
                                  <option value="{{ $Party->CCODE }}" @if($cand_party->CCODE == $Party->CCODE) selected @endif > {{$Party->PARTYABBRE}}-{{$Party->PARTYNAME}} </option>
                                @endforeach
                              </select>
                              <div class="perrormsg errormsg errorred"></div>
                            </div>
                          </div>
                        </div>
                        <?php   //dd($symb); ?>
                        <div class="form-group row">
                          <label class="col-sm-4">Symbol <sup>*</sup></label>
                          <div class="col-sm-8">
                           <div class="" style="width:100%;">
                            <select name="symbol_id" class="form-control" disabled>
                              <option value="">-- Select Symbol --</option>
                              <?php foreach($symb as $symbolDetails){
                               echo $symbolDetails->SYMBOL_NO;
                              ?>
                                <option value="{{$symbolDetails->SYMBOL_NO }}"@if( $sys_id== $symbolDetails->SYMBOL_NO) selected @endif {{ $symbolDetails->SYMBOL_DES }}> {{ $symbolDetails->SYMBOL_DES }}</option>
            <?php } ?>
                            </select>
                      <div id="mysysDiv" style="display: none;"> <input type="checkbox" name="nosymb" id="nosymb" value="200" checked="checked"> Symbole Not Alloted</div> 
                            <div class="serrormsg errormsg errorred"></div>
                           </div>
                          </div>
                        </div>
                       
                        </div>
                      </div>
                      </div>
                    </div>
                    </div>
                  </div>
                </div>	  
                </section>
                <section class="">
                <div class="container">
                  <div class="row">
                  
                    <div class="col-md-12">
                    <div class="card">
                            <div class="card-header d-flex align-items-center">
                              <h4>Candidate Personal Details</h4>
                            </div>
                            <div class="card-body">
                    <div class="row">
                    
                      <div class="col">                  
                              <div class="form-horizontal">
                                <div class="form-group row">
                                  <label class="col-sm-3">Name<sup>*</sup></label>
                                  <div class="col">
                                      <label>Name in English<sup>*</sup></label>
                          {!! Form::text('name', $name, ['class' => 'form-control', 'readonly', 'id' => 'name', 'placeholder' => 'In English','']) !!}
                          @if ($errors->has('name'))
                                                 <span style="color:red;">{{ $errors->first('name') }}</span>
                                              @endif
                          <div class="nameerrormsg errormsg errorred"></div>
                                  </div>  
                        <div class="col">
                          <label>Name in Hindi<sup>*</sup></label>
                         {!! Form::text('hname', $hname, ['class' => 'form-control', 'readonly', 'id' => 'hname', 'placeholder' => 'In Hindi','']) !!}
                         @if ($errors->has('hname'))
                                                 <span style="color:red;">{{ $errors->first('hname') }}</span>
                                              @endif
                         <div class="nhindierrormsg errormsg errorred"></div>
                                  </div>
                                   <div class="col">
                                      <label>Name in Vernacular <sup>*</sup></label>
                         {!! Form::text('cand_vname', $vname, ['class' => 'form-control', 'readonly', 'id' => 'cand_vname', 'placeholder' => 'Name in Vernacular','']) !!}
                         @if ($errors->has('cand_vname'))
                                                 <span style="color:red;">{{ $errors->first('cand_vname') }}</span>
                                              @endif
                         <div class="vererrormsg errormsg errorred"></div>   
                                  </div>
                                </div>
                    <div class="form-group row">
                                  <label class="col-sm-3">Candidate Alias Name </label>
                                  <div class="col">
                          {!! Form::text('aliasname', $alias_name, ['class' => 'form-control', 'readonly','id' => 'aliasname', 'placeholder' => 'Alias  Name English','']) !!}
                        @if ($errors->has('aliasname'))
                                <span style="color:red;">{{ $errors->first('aliasname') }}</span>
                               @endif 
                          
                                  </div>  
                        <div class="col">
                         {!! Form::text('aliashname', $alias_hname, ['class' => 'form-control', 'readonly','id' => 'aliashname', 'placeholder' => 'Alias Name In Hindi','']) !!}
                         @if ($errors->has('aliashname'))
                                <span style="color:red;">{{ $errors->first('aliashname') }}</span>
                               @endif 
                         
                                  </div>
                                </div>
                      
                      <div class="form-group row">
                                  <label class="col-sm-3">Father's / Mother's Name / Husband's Name <sup>*</sup></label>
                                  <div class="col">
                                     <label>Name in English<sup>*</sup></label>
                         {!! Form::text('fname', $father_name, ['class' => 'form-control', 'readonly','id' => 'fname', 'placeholder' => 'In English','']) !!}
                         @if ($errors->has('fname'))
                                                 <span style="color:red;">{{ $errors->first('fname') }}</span>
                                              @endif
                         <div class="ferrormsg errormsg errorred"></div> 
                                  </div>  
                        <div class="col">
                           <label>Name in Hindi<sup>  </sup></label>
                         {!! Form::text('fhname', $father_hname, ['class' => 'form-control', 'readonly','id' => 'fhname', 'placeholder' => 'In Hindi','']) !!}
                         @if ($errors->has('fhname'))
                                                 <span style="color:red;">{{ $errors->first('fhname') }}</span>
                                              @endif
                         <div class="fhindierrormsg errormsg errorred"></div>
                                  </div>
                                   <div class="col">
                        <label>Name in Vernacular <sup>  </sup></label>
                         {!! Form::text('fvname', $father_vname, ['class' => 'form-control', 'readonly','id' => 'fvname', 'placeholder' => 'In Hindi','']) !!}
                         @if ($errors->has('fvname'))
                                                 <span style="color:red;">{{ $errors->first('fvname') }}</span>
                                              @endif
                         
                        <div class="fverrormsg errormsg errorred"></div>   
                                  </div>
                                </div>
                      <div class="line"></div>
                      
                      <div class="form-group row">
                        <label class="col-sm-2">Email  </label>
                                    <div class="col">
                          {!! Form::text('email', $email, ['class' => 'form-control', 'readonly','id' => 'email','']) !!}
                          @if ($errors->has('email'))
                                                 <span style="color:red;">{{ $errors->first('email') }}</span>
                                              @endif
                          <div class="eerrormsg errormsg errorred"></div>
                                    </div>  
                          <label class="col-sm-2">Mobile No  </label>
                        <div class="col">
                          {!! Form::text('cand_mobile', $mobile, ['class' => 'form-control', 'readonly','id' => 'cand_mobile','','maxlength' => 10]) !!}
                          @if ($errors->has('cand_mobile'))
                                                 <span style="color:red;">{{ $errors->first('cand_mobile') }}</span>
                                              @endif
                          <div class="merrormsg errormsg errorred"></div> 
                        </div>
                                </div>
                      
                      
                      <div class="form-group row">
                                  <label class="col-sm-2">Gender <sup>*</sup></label>
                   
                          <div class="col">
                           <div class="custom-control custom-radio">
                          <input type="radio" name="gender" class="custom-control-input" id="customControlValidation2" value="female" @if($gender == 'female') checked @endif id="radio1" disabled>
                          <label class="custom-control-label" for="customControlValidation2">Female</label>
                          </div>
                          <div class="custom-control custom-radio ">
                            <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" id="radio2" @if($gender == 'male') checked @endif id="radio2" disabled>  
                          <label class="custom-control-label" for="customControlValidation3">Male</label>
                           
                          </div><div class="custom-control custom-radio mb-3">
                          <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" @if($gender == 'third') checked @endif id="radio3" disabled>  
                          <label class="custom-control-label" for="customControlValidation4">Others</label>
                          </div>
                          @if ($errors->has('gender'))
                              <span style="color:red;">{{ $errors->first('gender') }}</span>
                          @endif
                          <div class="gerrormsg errormsg errorred"></div>
                          </div> 
                          <label class="col-sm-2">PAN Number  </label>
                          <div class="col">
                            {!! Form::text('panno', $pan_number, ['class' => 'form-control','readonly', 'id' => 'panno','','maxlength' => 10]) !!}
                            @if ($errors->has('panno'))
                                                 <span style="color:red;">{{ $errors->first('panno') }}</span>
                                              @endif
                            <div class="pannoerrormsg errormsg errorred"></div>
                          </div>
                        </div>
                        <div class="form-group row">
                         <label class="col-sm-2">Age <sup>*</sup></label>
                          <div class="col">
                            {!! Form::text('age', $age, ['class' => 'form-control','readonly', 'maxlength'=>'2', 'id' => 'age','']) !!}
                            @if ($errors->has('age'))
                                                 <span style="color:red;">{{ $errors->first('age') }}</span>
                                              @endif
                            <div class="ageerrormsg errormsg errorred"></div>
                          </div>
                        <div class="col">&nbsp;</div>
                        </div>
                                <div class="line"></div>	
                                <?php 
                                 
                                  $address = $address;
                               
                                  $resAddress = '';
                                  if (strpos($address, ',') !== false) {
                                    $resAddress = explode(",", $address);
                                  }
                                  else{
                                    $resAddress = '' ;
                                  }
                                  
                                  $addressHindi = $haddress ;
                                  //	echo $addressHindi ; exit;
                                  $resAddressHindi = '' ;
                                  if (strpos($addressHindi, ',') !== false) {
                                    $resAddressHindi = explode(",", $addressHindi);
                                  }
                                  else{
                                    $resAddressHindi == '' ;
                                  }
                                ?>
                        <div class="form-group row">
                                  <label class="col-sm-2">Address <sup>*</sup></label>
                                   <div class="col">
                                     <label>Full Address in English  print as form 7A <sup>*</sup></label>
                                   @if($resAddress != '' )
                                     {!! Form::text('addressline1', $address, ['class' => 'form-control', 'readonly','id' => 'addressline1','placeholder'=>'In English']) !!}
                                  @else
                                  {!! Form::text('addressline1', $address, ['class' => 'form-control', 'readonly','id' => 'addressline1','placeholder'=>'In English']) !!}
            
                                  @endif
                                  @if ($errors->has('addressline1'))
                                                 <span style="color:red;">{{ $errors->first('addressline1') }}</span>
                                              @endif
                          <div class="addressline1errormsg errormsg errorred"></div>
                                  </div>  
                        <div class="col">
                          <label>Full Address in Hindi  print as form 7A  </label>
                        @if($resAddressHindi != '' )
                              {!! Form::text('addresshline1', $haddress, ['class' => 'form-control', 'readonly','id' => 'addresshline1','placeholder'=>'In Hindi']) !!}
                                  @else
                                  {!! Form::text('addresshline1', $haddress, ['class' => 'form-control', 'readonly','id' => 'addresshline1','placeholder'=>'In Hindi']) !!}
                                  
                                  @endif
                                  @if ($errors->has('addresshline1'))
                                                 <span style="color:red;">{{ $errors->first('addresshline1') }}</span>
                                              @endif
                          <div class="addresshline1errormsg errormsg errorred"></div>
                         </div>  
                                </div>
                      
                      <div class="line"></div>
                      
                      <!-- <div class="form-group row">
                          <label class="col-sm-2">Address Line2<sup></sup></label>
                            <div class="col">
                            @if($resAddress != '' )
                            {!! Form::text('addressline2', $resAddress[1], ['class' => 'form-control', 'id' => 'addressline2','placeholder'=>'In English']) !!}
                          @else
                          {!! Form::text('addressline2', null, ['class' => 'form-control', 'id' => 'addressline2','placeholder'=>'In English']) !!}
                          @endif
                          <div class="addressline2errormsg errormsg errorred"></div>
                      </div>  
            
                      <div class="col">
                        @if($resAddressHindi != '' )
                        {!! Form::text('addresshline2', $resAddressHindi[1], ['class' => 'form-control', 'id' => 'addresshline2','placeholder'=>'In Hindi']) !!}
                        @else
                          {!! Form::text('addresshline2', null, ['class' => 'form-control', 'id' => 'addresshline2','placeholder'=>'In Hindi']) !!}
                        @endif
                          <div class="addresshline2errormsg errormsg errorred"></div>
                        </div>  
                        </div> -->
                      <!-- <div class="line"></div> -->
                       <div class="form-group row">
                                  <label class="col-sm-2">Address Vernacular<sup>*</sup></label>
                                   <div class="col">
                                      <label>Full Address in Vernacular  print as form 7A <sup>*</sup></label>
                          {!! Form::text('addressv',$vaddress, ['class' => 'form-control', 'readonly','id' => 'addressv','placeholder'=>'Full Address In Vernacular as print in form 7A']) !!}
                          <div class="addressline3errormsg errormsg errorred"></div>
                                  
                         </div>  
                                </div>
                      <div class="line"></div>
                      <div class="form-group row">
                      <div class="col-sm-2"><label for="statename">State Name <sup>*</sup></label></div>
                      <div class="col"><div class="" style="width:100%;">
                        <select name="state" class="form-control" disabled>
                          <option value="">-- Select States --</option>
                          
                          @foreach($all_state as $State)
                            <option value="{{ $State->ST_CODE }}" @if($profile_data['state'] == $State->ST_CODE ) selected @endif> {{ $State->ST_NAME }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('state'))
                                                 <span style="color:red;">{{ $errors->first('state') }}</span>
                                              @endif
                        <div class="stateerrormsg errormsg errorred"></div></div>
                      </div>  
                      <div class="col-sm-2"><label for="statename">District <sup>*</sup></label></div>
                      <div class="col"><div class="" style="width:100%;">
                        <select name="district" class="form-control" disabled>
                          <option value="">-- Select Ditricts --</option>
                          @foreach($all_dist as $district)
                            <option value="{{ $district->DIST_NO }}" @if($profile_data['district'] == $State->ST_CODE ) selected @endif> 
                              {{$district->DIST_NO}} - {{$district->DIST_NAME }} - {{$district->DIST_NAME_HI}}
                            </option>
                          @endforeach 
                        </select>
                        @if ($errors->has('district'))
                                                 <span style="color:red;">{{ $errors->first('district') }}</span>
                                              @endif
                        <div class="districterrormsg errormsg errorred"></div>
                      </div>
                        </div> 
                      </div> 
                      <div class="form-group row">
                      @if($getDetails->AC_NAME != '')
                       <div class="col-sm-2"><label for="statename">AC <sup>*</sup></label></div>
                       <div class="col">
                        <div class="" style="width:100%;">
                          <select name="ac" class="consttype form-control" disabled>
                            <option value="">-- Select AC --</option>
                            @foreach($all_ac as $getAc)
                              <option value="{{ $getAc->AC_NO }}"> 
                              {{$getAc->AC_NO }} - {{$getAc->AC_NAME }} - {{$getAc->AC_NAME_HI}}
                              </option>
                            @endforeach 
                          </select>
                          @if ($errors->has('ac'))
                                                 <span style="color:red;">{{ $errors->first('ac') }}</span>
                                              @endif
                          <div class="consterrormsg errormsg errorred"></div>
                        </div>
                      </div> 
                       
                        @endif  
                      <div class="col-sm-2"><label for="statename">Category <sup>*</sup></label></div>
                         <div class="col"><div class="" style="width:100%;">
                          <select name="cand_category" class="form-control" disabled>
                            <option value="">--Select Category--</option>
                            <option value="general" @if($profile_data['category'] == 'general' ) selected @endif >General</option>
                            <option value="sc" @if($profile_data['category'] == 'sc' ) selected @endif >SC</option>
                            <option value="st" @if($profile_data['category'] == 'st' ) selected @endif >ST</option>
                            <!--<option value="obc" @if($profile_data['category'] == 'obc' ) selected @endif >OBC</option>-->
                            </select>
                            @if ($errors->has('cand_category'))
                                                 <span style="color:red;">{{ $errors->first('cand_category') }}</span>
                                              @endif
                          <div class="caterrormsg errormsg errorred"></div>
                        </div>
                      </div> 
                      </div> 
                    </div>
                      </div>
                    </div>
                            </div>
                          </div>
                    </div>
                  </div>
                </div>	  
                </section>
              </div>
            </main>

              <!-- End Nomination View Details -->
              


                  <div class="form-group row" style="margin-left: 5%;">
                    <div class="col-sm-3"><label for="statename">Candidate have Shown Criminal antecedents <sup>*</sup></label></div> 
                    <div class="col"> 
                      <div class="custom-control custom-radio">
                        <input type="radio" name="is_criminal" class="custom-control-input" id="customControl1" value="1" required 
                        @if("1"==old('is_criminal')) checked="checked" @endif>
                        <label class="custom-control-label" for="customControl1">Yes</label>
                      </div>
                      <div class="custom-control custom-radio">
                          <input type="radio" name="is_criminal" class="custom-control-input" id="customControl2" value="0" 
                          @if("0"==old('is_criminal')) checked="checked" @endif>
                          <label class="custom-control-label" for="customControl2">No</label>
                      </div>
                      <div class="cerrormsg errormsg errorred" style="font-size:12px;"></div>
                    </div>
                    @php
                      $display='none';
                      if(old('is_criminal')=='1' || !empty(session('error_mes'))){
                        $display='block';
                      }
                      
                    @endphp
                    <div class="caa" style="display:{{$display}};">
                      <div class="col" style="margin-left: -20%;">
                      <label for="affidavit" class="col-form-label">Candidate Criminal Antecedents File <span class="errorred">*</span> (Maximum size 3 MB - Only PDF)</label>
                      <div class="file-upload">
                        <div class="file-select">
                        <div class="file-select-name" id="noFile">Document not selected</div> 
                        <input type="file" name="affidavit" id="affidavit" class="custom-file-input affidavit form-control mr-auto" accept=".pdf" >
                        <div class="file-select-button customchoose" id="fileName">Choose File</div>
                        </div>
                        </div>
                        @if ($errors->has('affidavit'))
                        <span style="color:red;">{{ $errors->first('affidavit') }}</span>
                        @endif
                        <span id="fileerrormsg" class="fileerrormsg errormsg errorred" style="font-size:12px;">@if (session('error_mes')) {{ session('error_mes') }} @endif</span>
                      </div>
                    </div>
                  </div>
             
              
          












          
                  <div class="btns-actn <?php echo $nomination_id ?>">
                    <div class="row">
                      <div class="col">
                        <input type="hidden" name="Verify" value="1">
                        <a href="{{ url('/roac/listallapplicant') }}" class="btn btn-secondary font-big">Back</a>
                        <input type="hidden" name="encrypt_id" class="encrypt_id" value="{{ $encrypt_id }}">
                      </div>
                      <div class="col text-right">
                        @if($finalize=='yes')
                        <button class="btn dark-purple-btn font-big" type="submit"
                          value="Verify and Proceed for Receipt generation">Verify and Proceed for Receipt
                          generation</button>
                        @endif
                        {{-- <a href="{{url('/roac/nomination/apply-nomination-step-1?nom_id='.$encrypt_id)}}"
                        class="btn btn-primary" id="">Edit Nomination</a> --}}
                      </div>
                    </div>
                  </div>
        </form>
      </div>
    </div>
  </div>
</main>
{{-- @include('admin/nfd/nomination/form/step1'); --}}
<script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
@endsection
@section('script')
<script>
  function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function(e) {
              jQuery('#imagePreview').css('background-image', 'url('+e.target.result +')');
              jQuery('#imagePreview').hide();
              jQuery('#imagePreview').fadeIn(650);
          }
          reader.readAsDataURL(input.files[0]);
      }
  }
  jQuery("#imageUpload").change(function() {
      readURL(this);
  });
  jQuery(document).ready(function(){
    var d = new Date();
        var year = d.getFullYear() - 25;
  jQuery('#dob').datetimepicker({
  
        format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
           
    });
      
     
    if(jQuery('select[name="state"]').val() != ''){
      var stcode = jQuery('select[name="state"]').val() ;
      var selconst = '' ;
      var ac = "<?php echo $profile_data['ac'] ?>";
      var pc = "";
      if( ac != ''){
        selconst = "<?php echo $profile_data['ac'] ?>";	
      }else if(pc != ''){
        selconst = "";
      }
      jQuery.ajax({
              url: "{{url('/roac/getDistricts')}}",
              type: 'GET',
              data: {stcode:stcode},
              success: function(result){
                  var districtselect = jQuery('form select[name=district]');
          var seldistrict = "<?php echo $profile_data['district'] ?>";
          
                  districtselect.empty();
                  var districthtml = '';
          if(result != ''){
            districthtml = districthtml + '<option value="">-- Select District --</option> ';
              var selectedcons = "<?php echo $profile_data['ac'] ?>";
              var distval ='' ;
              jQuery.each(result,function(key, value) {
                if(seldistrict == value.DIST_NO){
                  distval = value.DIST_NO;
                  districthtml = districthtml + '<option value="'+value.DIST_NO+'" selected="selected">'+value.DIST_NO+' - '+value.DIST_NAME + ' - ' +value.DIST_NAME_HI+'</option>';
                }else{
                  districthtml = districthtml + '<option value="'+value.DIST_NO+'">'+value.DIST_NO+' - '+value.DIST_NAME + ' - ' +value.DIST_NAME_HI+'</option>';
                }
              
              jQuery("select[name='district']").html(districthtml);
              });
            var districthtml_end = '';
            if(jQuery('select[name="district"]').val() != ''){
              var stcode = jQuery('select[name="state"]').val() ;
              var district = jQuery('select[name="district"]').val() ;
              jQuery.ajax({
                url: '<?php echo url('/') ?>/roac/getallac',
                type: 'GET',
                data: {district:district,stcode:stcode},
                success: function(result){
                  var distselect = jQuery('form select[name=ac]');
                  distselect.empty();
                  var achtml = '';
                  //alert(selectedcons);
                  achtml = achtml + '<option value="">-- Select AC --</option> ';
                  jQuery.each(result,function(key, value) {
                    //alert(value.AC_NO);
                    if(selconst == value.AC_NO){
                      //alert('test');
                      achtml = achtml + '<option value="'+value.AC_NO+'" selected="selected">'+value.AC_NO+' - '+value.AC_NAME + ' - ' +value.AC_NAME_HI+'</option>';
                    }else{
                      achtml = achtml + '<option value="'+value.AC_NO+'">'+value.AC_NO+' - '+value.AC_NAME + ' - ' +value.AC_NAME_HI+'</option>';
                    }
                    jQuery("select[name='ac']").html(achtml);
                  });
                  var achtml_end = '';
                  jQuery("select[name='ac']").append(achtml_end)
                }
              });
            }
          }
                  else{
            districthtml = districthtml + '<option value="0">No Symbol Found</option>';
          }
                  
                  jQuery("select[name='district']").html(districthtml);
                  
                  var districthtml_end = '';
                  jQuery("select[name='district']").append(districthtml_end)
              }
          });
    }
    
    jQuery('select[name="party_id"]').change(function(){ 
      var partyid = jQuery(this).val();   
      $('#mysysDiv').hide();  
      jQuery.ajax({
              url: "{{url('/roac/getSymbol')}}",
              type: 'GET',
              data: {partyid:partyid},
              success: function(result){  
                jQuery("select[name='symbol_id']").html(result);
         },
             error: function (data, textStatus, errorThrown) {
               var symbolselect = jQuery('form select[name=symbol_id]');
                symbolselect.empty();
             var symbolhtml = '';
               symbolhtml = symbolhtml + '<option value="200">200 - Not Alloted</option>';
             jQuery("select[name='symbol_id']").html(symbolhtml);
              var symbolhtml_end = '';
              jQuery("select[name='symbol_id']").append(symbolhtml_end);
             }
          });
    });
    jQuery("select[name='state']").change(function(){
      var stcode = jQuery(this).val();
          jQuery.ajax({
              url: "{{url('/roac/getDistricts')}}",
              type: 'GET',
              data: {stcode:stcode},
              success: function(result){
                  var distselect = jQuery('form select[name=district]');
                  distselect.empty();
                  var districthtml = '';
                  districthtml = districthtml + '<option value="">-- Select District --</option> ';
                  jQuery.each(result,function(key, value) {
                      districthtml = districthtml + '<option value="'+value.DIST_NO+'">'+value.DIST_NO+' - '+value.DIST_NAME + ' - ' +value.DIST_NAME_HI+'</option>';
                      jQuery("select[name='district']").html(districthtml);
                  });
                  var districthtml_end = '';
                  jQuery("select[name='district']").append(districthtml_end)
              }
          });
      });
    jQuery("select[name='district']").change(function(){
      var district = jQuery(this).val();
      var stcode = jQuery('select[name="state"]').val();
          jQuery.ajax({
              url: "{{url('/roac/getallac')}}",
              type: 'GET',
              data: {district:district,stcode:stcode},
              success: function(result){
                  var distselect = jQuery('form select[name=ac]');
                  distselect.empty();
                  var achtml = '';
                  achtml = achtml + '<option value="">-- Select AC --</option> ';
                  jQuery.each(result,function(key, value) {
                      achtml = achtml + '<option value="'+value.AC_NO+'">'+value.AC_NO+' - '+value.AC_NAME + ' - ' +value.AC_NAME_HI+'</option>';
                      jQuery("select[name='ac']").html(achtml);
                  });
                  var achtml_end = '';
                  jQuery("select[name='ac']").append(achtml_end)
              }
          });
      });
    // Validation
      jQuery('#candnomination').click(function(){
      var partyid = jQuery('select[name="party_id"]').val();
      var symbolid = jQuery('select[name="symbol_id"]').val();
      var name = jQuery('input[name="name"]').val();
      var hindiname = jQuery('input[name="hname"]').val();
      var cand_vname = jQuery('input[name="cand_vname"]').val();
      var fname = jQuery('input[name="fname"]').val();
      var fhname = jQuery('input[name="fhname"]').val();
      var fvname = jQuery('input[name="fvname"]').val();
      var email = jQuery('input[name="email"]').val();
      var cand_mobile = jQuery('input[name="cand_mobile"]').val();
      var dob = jQuery('input[name="dob"]').val();
      var age = jQuery('input[name="age"]').val();
      var addressline1 = jQuery('input[name="addressline1"]').val();
      var addresshline1 = jQuery('input[name="addresshline1"]').val();
       var addressline2 = jQuery('input[name="addressline2"]').val(); 
       var addresshline2 = jQuery('input[name="addresshline2"]').val();
      var addressv = jQuery('input[name="addressv"]').val();
      var state = jQuery('select[name="state"]').val();
      var distt = jQuery('select[name="district"]').val();
      var consttype = jQuery('.consttype').val();
      var candcategory = jQuery('select[name="cand_category"]').val();
      var candimage = '<?php echo $image ?>';
      
      if(partyid == ''){
              jQuery('.errormsg').html('');
        jQuery('.perrormsg').html('Please select party');
        jQuery( "input[name='party_id']" ).focus();
        return false;
      }
      if(symbolid == ''){
              jQuery('.errormsg').html('');
        jQuery('.serrormsg').html('Please select symbol');
        jQuery( "input[name='symbol_id']" ).focus();
        return false;
      }
      if(name == ''){
              jQuery('.errormsg').html('');
        jQuery('.nameerrormsg').html('Please enter name in english');
        jQuery( "input[name='name']" ).focus();
        return false;
      }
      if(hindiname == ''){
              jQuery('.errormsg').html('');
        jQuery('.nhindierrormsg').html('Please enter name in hindi');
        jQuery( "input[name='hname']" ).focus();
        return false;
      }
      if(cand_vname == ''){
              jQuery('.errormsg').html('');
        jQuery('.vererrormsg').html('Please enter name in vernacular');
        jQuery( "input[name='cand_vname']" ).focus();
        return false;
      }
      if(fname == ''){
              jQuery('.errormsg').html('');
        jQuery('.ferrormsg').html('Please enter father/husband name in english');
        jQuery( "input[name='fname']" ).focus();
        return false;
      }
      // if(fhname == ''){
      // 	jQuery('.errormsg').html('');
      // 	jQuery('.fhindierrormsg').html('Please enter father/husband name in hindi');
      // 	jQuery( "input[name='fhname']" ).focus();
      // 	return false;
      // }
      // if(fvname == ''){
      // 	jQuery('.errormsg').html('');
      // 	jQuery('.fverrormsg').html('Please enter father/husband name in vernacular');
      // 	jQuery( "input[name='fvname']" ).focus();
      // 	return false;
      // }
       
      if(jQuery('input[type=radio][name=gender]:checked').length == 0)
      {
        jQuery('.errormsg').html('');
        jQuery('.gerrormsg').html('Please select gender');
        //jQuery('input[type=radio][name=gender]:checked').focus();
        return false;
      }
      if(age == ''){
        jQuery('.ageerrormsg').html('');
        jQuery('.ageerrormsg').html('please enter candidate age');
        jQuery( "input[name='age']" ).focus();
        return false;
      } 
      if(addressline1 == ''){
        jQuery('.errormsg').html('');
        jQuery('.addressline1errormsg').html('Please enter address line1 in english');
        jQuery( "input[name='addressline1']" ).focus();
        return false;
      }
      // if(addresshline1 == ''){
      // 	jQuery('.errormsg').html('');
      // 	jQuery('.addresshline1errormsg').html('Please enter address line1 in hindi');
      // 	jQuery( "input[name='addresshline1']" ).focus();
      // 	return false;
      // }
      if(addressv == ''){
        jQuery('.errormsg').html('');
        jQuery('.addressline3errormsg').html('Please enter address in vernacular');
        jQuery( "input[name='addressv']" ).focus();
        return false;
      } 
      if(state == ''){
        jQuery('.errormsg').html('');
        jQuery('.stateerrormsg').html('Please select state');
        jQuery( "input[name='state']" ).focus();
        return false;
      }
      if(distt == ''){
        jQuery('.errormsg').html('');
        jQuery('.districterrormsg').html('Please select district');
        jQuery( "input[name='district']" ).focus();
        return false;
      }
      if(consttype == ''){
        jQuery('.errormsg').html('');
        jQuery('.consterrormsg').html('Please select const type');
        jQuery( "input[name='district']" ).focus();
        return false;
      }
      if(candcategory == ''){
        jQuery('.errormsg').html('');
        jQuery('.caterrormsg').html('Please select candidate category');
        jQuery( "select[name='cand_category']" ).focus();
        return false;
      }
       
    });
    jQuery("#cand_mobile").keypress(function (e) {
      //if the letter is not digit then display error and don't type anything
         var length = jQuery(this).val().length;
         if(length > 9) {
          return false;
         } else if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          jQuery('.errormsg').html('');
          jQuery('.merrormsg').html('Digits Only').show().fadeOut("slow");
          jQuery( "input[name='cand_mobile']" ).focus();
          return false;
         } else if((length == 0) && (e.which == 48)) {
          return false;
         }
      });
  });
  function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test(email)) {
      return false;
    }else{
      return true;
    }
  }
  </script>
@endsection