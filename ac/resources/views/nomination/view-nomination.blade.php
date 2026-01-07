      @extends('layouts.theme')
      @section('title', 'Nomination')
      @section('content')
      <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
	  <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}" id="theme-stylesheet">	  
		<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,400i,500,500i,600,700,700i,800,900&display=swap" rel="stylesheet">
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
	<div class="container">
	 <div class="step-wrap mt-4">
		
	 </div>
	</div>
     <section>
      <div class="container p-0">
         <div class="row">
			
			
			
			<form method="post" name="preview" action="{!! $action !!}" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			<input type="hidden" name="nomination_id" value="{{$nomination_id}}">		  
			 <div class="container-fluid">
			  <div class="card card-shadow">
				<div class="row" style="margin-top:15px;margin-right:10px;">
				<div class="fullwidth" style="float: left;width: 100%;">
				@if(isset($reference_id) && isset($href_download_application))
                <div class="col-md-5 float-right">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right">{{ __('election_details.ref') }}: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
                    <li class="list-inline-item text-right"><a href="{!! $href_download_application !!}" class="btn btn-primary" target="_blank">{{ __('election_details.down') }}</a></li>
                  </ul>
                </div>
                @endif
              </div>
           </div>
				
				  <table class="customTable">
					<tbody>
					  
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
												@if(!empty($iterate_proposer['date'])){{date('d/m/Y',strtotime($iterate_proposer['date']))}}@endif
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
					   	<td>{{ __('finalize.Date') }} <span>&nbsp;<b>@if(!empty($part3_date)){{date("d/m/Y", strtotime($part3_date))}}@endif </b></span></td>
					   	<td class="td-right">
							<div> {{ __('finalize.Signature_of_Candidate') }} </div>
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
							<div>{{ __('part3a.Date') }}: <b>@if(!empty($date_of_disloyal)){{date("d/m/Y", strtotime($date_of_disloyal))}}@endif</b></div>
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
		   </tbody> 
		   </table>
		    <div class="fullwidth" style="margin-top: 30px;"> 
	  <div class="form-group">
		<div class="col">
		  <a href="{{url('nomination/nominations?')}}<?php echo 'acs='. $_REQUEST['acs'].'&std='.$_REQUEST['std']; ?>"  id="" class="btn btn-secondary float-left font-big">{{ __('step1.Back') }}</a>
		  
		</div>
		<br>
		<br>
		
		<?php $ref=0; ?>
		@if(isset($reference_id))
        <?php  $ref =  app(App\Http\Controllers\Nomination\NominationController::class)->getprescrutiny($reference_id); ?>       
        @endif
		</div>
	  </div>
		</div> 
	  </div>	
	  </form>
	 </div>
			  
        </div>
      </div>    
    </section>
	
	<!-- The Confirmation Modal Starts Here -->
  <div class="modal fade modal-confirm" id="confirm">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="pop-header py-4">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h2 class="modal-title">{{ __('finalize.Confirmation') }}</h2> 
		<div class="header-caption px-4">
		  <p class="font-big">{{ __('finalize.are_you_sure') }} </p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn font-big mr-4" data-dismiss="modal">{{ __('step1.Cancel') }}</button>
          <button type="button" class="btn dark-purple-btn font-big" onclick="submitForm();">{{ __('finalize.Ok') }}</button>
        </div>
		<span style="text-align: center;display:none;" id="loader">
		 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('finalize.Please_Wait') }}
		</span>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
	
	
	
	
	
   
  </main>
  
  <script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
  @endsection

  @section('script')
  <script>
	function submitForm(){
		document.preview.submit();
		var j = jQuery.noConflict();		
		j("#loader").show();
	}
	function finalize(){
	 $('#confirm').modal('show');
	}
  
  
  
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
  @endsection