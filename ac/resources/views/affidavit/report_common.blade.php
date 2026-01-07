<table width="100%" border="0">
	<caption style="caption-side: top;" >
		<h3 style="text-align:center; margin:9px auto 0; font-size:22px; line-height:25px; font-weight:bold; color:black;">{{Lang::get('affidavit.form26') }} <br>{{Lang::get('affidavit.see_rule_4a')}}</h3>						
	</caption>
		<tr>
			<td align="right">
				<div style="width: 110px; height: 130px; text-align:right;">
					
					@if(@$data['cand_details']->cimage)
						@if(@$data['pdf'] == '1')
							<img style="width: 100px;" src="{!! @$data['cand_details']->cimage !!}">
						@else
							<img style="width: 100px;" src="{{asset(@$data['cand_details']->cimage)}}">
						@endif
					@endif
				</div>
			</td>
		</tr>											
	</table>
	<table width="100%" bgcolor="#fff" border="0">


		<?php if(session()->get('locale') == 'hi') { ?>
			<tr>
			<th  class="justify">			
				<p> <u class="inputLine">@if(@$data['cand_details']->st_code  && @$data['cand_details']->ac_no )
				{{getacbyacno(@$data['cand_details']->st_code,@$data['cand_details']->ac_no)->AC_NAME}} @endif</u> (निर्वाचन क्षेत्र का नाम)   निर्वाचन-क्षेत्र  से   <u class="inputLine"><span style="width:350px;">{{Lang::get('affidavit.general_election_to_legislative_assembly')}}</span></u> (सदन का नाम) के निर्वाचन के लिए रिटर्निंग आफिसर के समक्ष अभ्‍यर्थी द्वारा नाम-निर्देशन पत्र के साथ प्रस्‍तुत किया जाने वाला शपथ पत्र 
				</p>
			</th>
			</tr>
		<?php } else { ?>

	
		<tr>
			<th  class="justify">			
				<p> {{Lang::get('affidavit.affidavit_to_be_giled_by_the_candidate_alogwith_nomination_paper_before_the_returning_officer_for_election')}} {{Lang::get('affidavit.to')}} <u class="inputLine"><span style="width:350px;">{{Lang::get('affidavit.general_election_to_legislative_assembly')}}</span></u> <span style="text-align:right!important; display:inline-block">{{Lang::get('affidavit.name_of_the_house')}}</span> {{Lang::get('affidavit.from')}} <u class="inputLine">@if(@$data['cand_details']->st_code  && @$data['cand_details']->ac_no )
				{{getacbyacno(@$data['cand_details']->st_code,@$data['cand_details']->ac_no)->AC_NAME}} @endif</u>{{Lang::get('affidavit.constituency')}}</p>
			</th>
		</tr>  
		<?php } ?>
		
 	</table>
 	<table width="100%" class="top-20 top" bgcolor="#fff" border="0">	
		<tr>
		<th align="center" style="text-align:center; margin:20px auto 0;  line-height:25px"><h3 style="text-decoration: underline; font-weight:bold; color:black;font-size:22px;">{{Lang::get('affidavit.part_a')}}</h3>	
		</th>
		</tr>
	</table>
 	<table width="100%" border="0" class="top padd-0" >						
		<tr>
			<td colspan="2" style="line-height: 1.8; margin-bottom: 20px;">
			<p>{{Lang::get('affidavit.i')}}<u class="inputLine"> {{@$data['cand_details']->cand_name}} </u>**
			@if(@$data['cand_details']->relation_name == 2)
				<del>{{Lang::get('affidavit.son')}}/</del>{{Lang::get('affidavit.daughter')}}<del>/{{Lang::get('affidavit.wife')}}</del>
			@elseif(@$data['cand_details']->relation_name == 3)
				<del>{{Lang::get('affidavit.son')}}/{{Lang::get('affidavit.daughter')}}/</del>{{Lang::get('affidavit.wife')}}
			@else
				{{Lang::get('affidavit.son')}}<del>/{{Lang::get('affidavit.daughter')}}/{{Lang::get('affidavit.wife')}}/</del>
			@endif 
			 @if(session()->get('locale') == 'hi') @else {{Lang::get('affidavit.of')}} @endif <u class="inputLine">{{$data['cand_details']->son_daughter_wife_of}} </u> {{Lang::get('affidavit.aged')}} <u class="inputLine">{{@$data['cand_details']->age}}</u> {{Lang::get('affidavit.years_resident_of')}} <u class="inputLine">{{@$data['cand_details']->postal_address}} </u> {{Lang::get('affidavit.mention_full_postal_address')}}</p>
			</td>
		</tr>
		
		
		<?php if(session()->get('locale') == 'hi') { ?>
		
		<tr>
			<td width="4"><b>(1)</b></td>
			
			
			<td>
				    	मैं. <u class="inputLine">@if(@$data['cand_details']->partyabbre){{getpartybyid(@$data['cand_details']->partyabbre)->PARTYNAME}}@endif</u>  @if(@$data['cand_details']->partytype) (**राजनैतिक दल का नाम) द्वारा खड़ा किया गया अभ्‍यर्थी  <del> /**एक स्‍वतंत्र अभ्‍यर्थी </del> @else <del> (**राजनैतिक दल का नाम) द्वारा खड़ा किया गया अभ्‍यर्थी / </del> **एक स्‍वतंत्र अभ्‍यर्थी  @endif के रूप में लड़ रहा हूं। 
				<br />	(**जो लागू न हो उसे काट दें) 

		    </td>

			
			
		</tr>
		
		<?php } else { ?>
		<tr>
			<td width="4"><b>(1)</b></td>
			
				<td>
				 {{Lang::get('affidavit.i_am_a_candidate_set_up_by')}} <u class="inputLine">@if(@$data['cand_details']->partyabbre){{getpartybyid(@$data['cand_details']->partyabbre)->PARTYNAME}}@endif</u>
				</td>
			
		</tr>
		
		<tr>
			<td></td>
			<td>
				<span style="display: block; line-height: 30px;" class="block">@if(@$data['cand_details']->partytype)(<b>**</b> {{Lang::get('affidavit.name_of_the_political_party')}}) / <b>**</b> <del>{{Lang::get('affidavit.am_contesting_as_an_independent_candidate')}} </del>
				
				@else
					
				<del>(<b>**</b> {{Lang::get('affidavit.name_of_the_political_party')}})</del> / <b>**</b> {{Lang::get('affidavit.am_contesting_as_an_independent_candidate')}}
				@endif
				.</span>
				<p style="paddint-top:20px; display:block">(<b>**</b>{{Lang::get('affidavit.strike_out_whichever_is_not_applicable')}})</p>
			</td>
		</tr>
		
		<?php } ?>
		
			
			<?php if(session()->get('locale') == 'hi') { ?>
			
			<tr>
				<td width="4"><b>(2)</b></td>
				<td>
				<p>
						मेरा नाम  <u class="inputLine">
					@if(@$data['cand_details']->state_enrolled && @$data['cand_details']->constituency_enrolled)
					{{getacbyacno(@$data['cand_details']->state_enrolled,@$data['cand_details']->constituency_enrolled)->AC_NAME}},
					@endif
					@if(@$data['cand_details']->state_enrolled)
					{{getstatebystatecode(@$data['cand_details']->state_enrolled)->ST_NAME}} @endif 
				</u>(निर्वाचन-क्षेत्र और राज्‍य का नाम) में भाग सं <u class="inputLine">{{@$data['cand_details']->part_no_enrolled}}</u> के क्रम सं <u class="inputLine">{{@$data['cand_details']->serial_no_enrolled}}</u> पर प्रविष्‍ट है। </p>
				</td>
			</tr>
			
			
			
			<?php } else { ?>
			
			
			<tr>
				<td width="4"><b>(2)</b></td>
				<td>
				<p>
					<span> {{Lang::get('affidavit.my_name_is_enrolled_in')}} <u class="inputLine">
					@if(@$data['cand_details']->state_enrolled && @$data['cand_details']->constituency_enrolled)
					{{getacbyacno(@$data['cand_details']->state_enrolled,@$data['cand_details']->constituency_enrolled)->AC_NAME}},
					@endif
					@if(@$data['cand_details']->state_enrolled)
					{{getstatebystatecode(@$data['cand_details']->state_enrolled)->ST_NAME}} @endif 
				</u> {{Lang::get('affidavit.at_serial_no')}} <u class="inputLine">{{@$data['cand_details']->serial_no_enrolled}}</u> {{Lang::get('affidavit.in_part_no')}} <u class="inputLine">{{@$data['cand_details']->part_no_enrolled}}</u></span></p>
				</td>
			</tr>	
			
			<?php } ?>
			
				<?php if(session()->get('locale') == 'hi') { ?>
			
			<tr>
				<td width="4"><b>(3)</b></td>
				<td>
					<p>(3)	मेरा/मेरे <u class="inputLine">{{@$data['cand_details']->phoneno_1}}   @if(@$data['cand_details']->phoneno_2) ,{{@$data['cand_details']->std_code}}-{{@$data['cand_details']->phoneno_2}} @endif</u>  संपर्क दूरभाष संख्‍या/संख्‍याएं है/हैं और <u class="inputLine">{{@$data['cand_details']->emailid}}</u>  मेरा ईमेल पता (यदि कोई हो) है तथा मेरा/मेरे सोशल मीडिया खाता/खाते (यदि कोई हो) निम्‍नलिखित है/हैं।</p>
				</td>
			</tr>
			
			
			
			<?php } else { ?>
			<tr>
				<td width="4"><b>(3)</b></td>
				<td>
					<p>{{Lang::get('affidavit.my_contact_telephone_number')}} <u class="inputLine">{{@$data['cand_details']->phoneno_1}}   @if(@$data['cand_details']->phoneno_2) ,{{@$data['cand_details']->std_code}}-{{@$data['cand_details']->phoneno_2}} @endif</u> {{Lang::get('affidavit.and_my_e_mail_id')}} <u class="inputLine">{{@$data['cand_details']->emailid}}</u> {{Lang::get('affidavit.and_my_social_media_account')}}</p>
				</td>
			</tr>

			<?php } ?>
			
	</table>
	
	
					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<td><b>{{Lang::get('affidavit.sr_no')}}</b></td>
								<td><b>{{Lang::get('affidavit.social_media')}}</b></td>
								<td><b> {{Lang::get('affidavit.account')}} </b></td>
								
							</tr>
							@if(count($data['social_media'])>0)
							@foreach($data['social_media'] as $key => $raw)
							
							<tr>
								<td>{{$key+1}}</td>
								<td>{{$raw->media_account}}</td>
								<td>{{$raw->other_account_name}}</td>									
							</tr>	
							
							@endforeach
							@else
								<tr>
								<td>1</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>	
							</tr>
							@endif


						</tbody>
					</table>			
					
		
		
			<table width="100%" class="top-20 top" bgcolor="#fff" border="0">
				<tr>
					<th align="left">(4) {{Lang::get('affidavit.details_of_permanent_account_number_and_status_of_filing_of_income_tax_return')}}</th>
				</tr>
			</table>

	<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
		<tbody>
			<tr class="thHeading">
				<th>Sl.NO</th>
				<th>{{Lang::get('affidavit.relation')}}</th>
				<th>{{Lang::get('affidavit.pan')}} </th>
				<th>{{Lang::get('affidavit.the_financial_year_for_which_the_last_incometax_return_has_been_filed')}}</th>
				<th>{{Lang::get('affidavit.total_income_shown_in_income_tax_return_for_the_last_five_financial_years_completed')}}
				</th>
			</tr>			
			<?php $k=0; ?>	
			@foreach($data['pan_details'] as $key => $raw)				
			<tr>
				<td>{{++$k}}</td>
				<td>{{$raw->relation_type}} </td>
				<td>@if($raw->pan)
					{{$raw->pan}}
					@else
						{{Lang::get('affidavit.no_pan_allotted')}}
					@endif
					</td>
				<td>
				@if($raw->financial_year)
					{{$raw->financial_year}}
				@else
					{{Lang::get('affidavit.nil')}}
				@endif
				</td>
				<td class="padd-0">
					<table class="bdrLeass" align="left" width="100%" border="1">
						<tr><td width="10">(i)</td><td> 
						@if($raw->financialyr1) &#8377; {{$raw->financialyr1}}
						@else {{Lang::get('affidavit.nil')}} @endif
						
						@if($raw->financial_year)
						({{$raw->financial_year}})
						@endif
						</td></tr>
						<tr><td>(ii)</td> <td>
						@if($raw->financialyr2) &#8377; {{$raw->financialyr2}}
						@else {{Lang::get('affidavit.nil')}} @endif
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -2}} - {{$financial_year -1}})
							@endif
						@endif
						</td></tr>
						<tr><td>(iii)</td> <td>
						@if($raw->financialyr3) &#8377; {{$raw->financialyr3}}
						@else {{Lang::get('affidavit.nil')}} @endif
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -3}} - {{$financial_year -2}})
							@endif
						@endif
						</td></tr>
						
						<tr><td>(iv)</td> <td>
						@if($raw->financialyr4) &#8377; {{$raw->financialyr4}}
						@else {{Lang::get('affidavit.nil')}} @endif 
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -4}} - {{$financial_year -3}})
							@endif
						@endif
						</td></tr>
						
						<tr><td>(v)</td> <td>
						@if($raw->financialyr5) &#8377; {{$raw->financialyr5}}
						@else {{Lang::get('affidavit.nil')}} @endif 
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -5}} - {{$financial_year -4}})
							@endif
						@endif
						</td></tr>
					</table>
				</td>
			</tr>							
			@endforeach
		</tbody>
	</table>	

		<table width="100%;" class="top top-20">
			<tr>
				<th>{{Lang::get('affidavit.note')}}: </th>
				<th>{{Lang::get('affidavit.it_is_mandatory_for_pan_holder_to_mention_pan')}} </th>
			</tr>			
			@php $not_applicable = ''; @endphp
			@foreach($data['pending_cases'] as $key => $raw)
				@php $not_applicable = $raw->not_applicable; @endphp			
			@endforeach	
			<tr align="left">
				<th>(5)</th>
				<th align="left">{{Lang::get('affidavit.pending_criminal_cases')}}</th>
			</tr>
			<tr>
				<th>(i)</th>
				@if(($not_applicable == 'NOT APPLICABLE') || (count($data['pending_cases'])  == 0)  )
				<td>{{Lang::get('affidavit.i_declare_that_there_is_no_pending_criminal_case_against_me')}}</td>
				@else
				<td><del>{{Lang::get('affidavit.i_declare_that_there_is_no_pending_criminal_case_against_me')}} </del></td>	
				@endif
			</tr>
			<tr>
				<td colspan="2" align="center"><span class="bold">{{Lang::get('affidavit.or')}}</span></td>
			</tr>
			<tr>
				<th>(ii)</th>
				<td> {{Lang::get('affidavit.the_following_criminal_cases_are_pending_against')}}
				@if(($not_applicable == 'NOT APPLICABLE') || (count($data['pending_cases'])  == 0)  )
				<span class="bold">&nbsp;&nbsp;&nbsp;</span>
				@endif
				</td>				
			</tr>
			<tr>
							<td colspan="2">{{Lang::get('affidavit.the_following_criminal_cases_are_pending_against_me')}}</td>
						</tr>
		</table>
<table width="100%" class="top-20 top" style="border:1px solid black;">
					@if(!empty($data['pending_cases_check'][0]->fir_no)  )

					
				    <tr style="border:1px solid black;">
				    	<th style="border: 1px solid black;border-collapse: collapse;">(a)</th>
				        <th style="border: 1px solid black;border-collapse: collapse;float:left"><span>FIR No. with name <br>and address of Police Station concerned</span></th>

				       @foreach($data['pending_cases'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->fir_no }}/{{ $raw->police_station }}/{{ $raw->police_station_address }}</td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(b)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Case No. with Name of the Court</th>

				        @foreach($data['pending_cases'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->case_no }}/{{ $raw->name_court_cognizance }}</td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(c)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Section(s) of concerned<br>Acts/Codes involved(give no. of the Section,<br> e.g.Section...of IPC,etc.).</th>

				      @foreach($data['pending_cases'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->sections }}/{{ $raw->acts }} </td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(d)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Brief description of<br>offence </th>

				      @foreach($data['pending_cases'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->offence_description }} </td>
				        @endforeach
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(e)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Whether charges <br>have been framed(mention YES or NO)</th>

				      @foreach($data['pending_cases'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">@if($raw->framed_charge == 1) {{Lang::get('affidavit.yes')}} @else {{Lang::get('affidavit.no')}} @endif</td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(f)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">If answer against (e)<br> above is YES, then <br>give the date on which charges were framed</th>

				      @foreach($data['pending_cases'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">@if($raw->framed_charge == 1) {{\Carbon\Carbon::parse($raw->date_charges)->format('d/m/Y')}} @endif </td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(g)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Whether any Appeal/Application<br> for revision has been <br>filed against the proceedings (Mention YES or NO)</th>

				      @foreach($data['pending_cases'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">@if($raw->appeal_application == 1) {{Lang::get('affidavit.yes')}} @else {{Lang::get('affidavit.no')}} @endif </td>
				        @endforeach
				    </tr>
				    







				 @else






<tr style="border:1px solid black;">
	<th style=" border: 1px solid black;border-collapse: collapse;">(a)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;float:left">FIR No. with name and address of Police Station concerned</th>

				       
				            <td style=" border: 1px solid black;border-collapse: collapse;">N/A</td>
				        
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(b)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;float:left">Case No. with Name of the Court</th>

				       
				            <td style=" border: 1px solid black;border-collapse: collapse;">N/A</td>
				        
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(c)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;float:left;">Section(s) of concerned<br>Acts/Codes involved<br>(give no. of the Section, e.g.Section...of IPC,etc.).</th>

				     
				            <td style=" border: 1px solid black;border-collapse: collapse;">N/A </td>
				       
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(d)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;float:left;">Brief description of offence </th>

				     
				            <td style=" border: 1px solid black;border-collapse: collapse;">N/A </td>
				        
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(e)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;float:left;">Whether charges <br>have been framed(mention YES or NO)</th>

				     
				            <td style=" border: 1px solid black;border-collapse: collapse;">N/A</td>
				       
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(f)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;float:left;">If answer against (e) above is YES, then <br>give the date on which charges were framed</th>

				     
				            <td style=" border: 1px solid black;border-collapse: collapse;">N/A</td>
				       
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(g)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;float:left;">Whether any Appeal/Application for revision has been <br>filed against the proceedings (Mention YES or NO)</th>

				     
				            <td style=" border: 1px solid black;border-collapse: collapse;">N/A </td>
				       
				    </tr>
				    
				@endif
				</table>

					<table width="100%;" class="top top-20">
					
					@php $not_applicable = ''; @endphp
					@foreach($data['imprisonment_criminal'] as $key => $raw)
						@php $not_applicable = $raw->not_applicable; @endphp
					
					@endforeach
					
						<tr align="left">
							<th width="4">(6)</th>
							<th>{{Lang::get('affidavit.cases_of_conviction')}}</th>
						</tr>						
						<tr>
							<th width="4">(i)</th>
							@if(($not_applicable == 'NOT APPLICABLE') || (count($data['imprisonment_criminal'])  == 0)  )
							<td>{{Lang::get('affidavit.i_declare_that_i_have_not_been_convicted_for_any_criminal_offence')}}</td>
							@else
							<td><del>{{Lang::get('affidavit.i_declare_that_i_have_not_been_convicted_for_any_criminal_offence')}}</del></td>	
							@endif
						</tr>
						<tr>
							<td colspan="2" align="center"><span class="bold">{{Lang::get('affidavit.or')}}</span></td>
						</tr>
						<tr>
							<th width="3">(ii)</th>
							<td>
								{{Lang::get('affidavit.i_have_been_convicted_for_the_offences_mentioned')}}
								@if(($not_applicable == 'NOT APPLICABLE') || (count($data['imprisonment_criminal'])  == 0)  )
								<b class="bold"> </b> 
								@endif
							 </td>							
						</tr>
					</table>					
					

					<table width="100%" class="top-20" border="0">
						<tr>
							<td colspan="2">{{Lang::get('affidavit.if_the_candidate_has_been_convicted')}}</td>
						</tr>
					</table>



					<table width="100%" class="top-20 top" style="border:1px solid black;">
					@if(!empty($data['imprisonment_criminal_check'][0]->case_no)  )
				    <tr style="border:1px solid black;">

				        <th style=" border: 1px solid black;border-collapse: collapse;">(a)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Case No</th>

				       @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->case_no }}</td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(b)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Name of the Court</th>

				        @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->convicting_court }}</td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(c)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Section of ACts/Code involved (give no. of the Section, e.g.Section……. of IPC,etc.).</th>

				      @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->sections }}/{{ $raw->acts }} </td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(d)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Brief description of<br>offence for which convicted</th>

				      @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->offence_description }} </td>
				        @endforeach
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(e)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Dates of orders of conviction</th>

				      @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{\Carbon\Carbon::parse($raw->order_date)->format('d/m/Y')}}</td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(f)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;"> Punishment Imposed</th>

				      @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">{{ $raw->punish }} </td>
				        @endforeach
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(g)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Whether any Appeal has been<br> filed against conviction order (Mention YES or NO)</th>

				      @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">@if($raw->appeal_filed == 1) {{Lang::get('affidavit.yes')}} @else {{Lang::get('affidavit.no')}} @endif </td>
				        @endforeach
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(h)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">If answer to (g)above is<br> YES, give details and present status of appeal</th>

				      @foreach($data['imprisonment_criminal'] as $key => $raw)
				            <td style=" border: 1px solid black;border-collapse: collapse;">@if($raw->appeal_filed == 1) {{$raw->appeal}} @endif </td>
				        @endforeach
				    </tr> 


				 @else




                   <tr style="border:1px solid black;">
                   	<th style=" border: 1px solid black;border-collapse: collapse;">(a)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Case No</th>

				      
				            <td >N/A</td>
				        
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(b)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Name of the Court</th>

				        
				            <td>N/A</td>
				       
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(c)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Section of ACts/Code involved (give no. of the Section, e.g.Section……. of IPC,etc.).</th>

				     
				            <td>N/A </td>
				        
				    </tr>

				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(d)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Brief description of offence for which convicted</th>

				     
				            <td>N/A </td>
				       
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(e)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Dates of orders of conviction</th>

				      
				            <td>N/A </td>
				       
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(f)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;"> Punishment Imposed</th>

				     
				            <td>N/A </td>
				        
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(g)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">Whether any Appeal has been<br> filed against conviction<br>order (Mention YES or NO)</th>

				     
				            <td>N/A</td>
				        
				    </tr>
				    <tr style="border:1px solid black;">
				    	<th style=" border: 1px solid black;border-collapse: collapse;">(h)</th>
				        <th style=" border: 1px solid black;border-collapse: collapse;">If answer to (g)above is<br> YES, give details and present status of appeal</th>

				      
				            <td>N/A </td>
				       
				    </tr>
				@endif
				</table>
					<table width="100%" class="top top-20">
						<tr>  
							<th width="4">(6A)</th>							
							<td>
								<span>{{Lang::get('affidavit.i_have_given_full_and_up_to_date_information_to_my_political_party')}}</span>
							</td>

						</tr>
						<tr>  
							<th width="4"></th>							
							<td>
								<span>{{Lang::get('affidavit.candidates_to_whom_this_item_is_not_applicable_should_clearly_write')}}</span>
							</td>

						</tr>



						<tr>
							<th colspan="2" align="left">{{Lang::get('affidavit.note')}} :</th>
						</tr>	
						<tr>
							<th width="4" align="right">1.</th>
							<td >{{Lang::get('affidavit.details_should_be_entered_clearly_and_legibly_in_bold_letters')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">2.</th>
							<td >{{Lang::get('affidavit.details_to_be_given_separately_for_each_case_under_different_columns_against_eachitem')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">3.</th>
							<td >{{Lang::get('affidavit.details_should_be_given_in_reverse_chronological_order')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">4.</th>
							<td>{{Lang::get('affidavit.additional_sheet_may_be_added_if_required')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">5.</th>
							<td>{{Lang::get('affidavit.candidate_is_responsible_for_supplying_all_information_in_compliance_of')}}</td>
						</tr>
                    </table>

                    <table width="100%" class="top top-20">
                    	<tr>
                    		<th width="4">(7)</th>
                    		<th>                    			
                    			That I give herein below the details of the assets (movable and immovable etc.) of myself, my spouse and all dependents:
                    		</th>
                    	</tr>
                    	<tr align="left">
                    		<th colspan="2" align="left"><span class="block"><u> {{Lang::get('affidavit.details_of_movable_assets')}}:</u></span></th>
                    	</tr>
                    	<tr>
                    		<td colspan="2" class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="95">{{Lang::get('affidavit.note')}}: 1. </th>
                    					<td>{{Lang::get('affidavit.assets_in_joint_name_indicating_the_extent_of_joint_ownership_will_also_have_to_be_given')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 2. </th>
                    					<td>{{Lang::get('affidavit.in_case_of_deposit_investment_the_details_including_serial_number')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>	
                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 3. </th>
                    					<td>{{Lang::get('affidavit.value_of_bonds_share_debentures_as_per_the_current_market_value_in_stock_exchange')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 4. </th>
                    					<td>{{Lang::get('affidavit.dependent_means_parents_son_daughter_of_candidate')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>

                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 5. </th>
                    					<td>{{Lang::get('affidavit.details_including_amount_is_to_be_given_separately_in_respect_of_each_investment')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>	

                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 6. </th>
                    					<td >{{Lang::get('affidavit.details_should_include_the_interest_in_or_ownership_of_offshore_assets')}} </td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>

                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top top-20" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.explanation')}},-  </th>
                    					<td>{{Lang::get('affidavit.for_the_purpose_of_this_form_the_expression')}} </td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>                    	
                    </table>



<!----------------------------->




  <table width="100%" class="top-20" border="1">                    	
                    	<tr class="thHeading">
                    		<th style="max-width:15%;min-width:3%;">Sl. NO</th>
                    		<th style="max-width:45%;min-width:25%;"> Description</th>
                        @foreach($data['non_relation_details'] as $key => $raw)	
                    		<th style="max-width:90%;min-width:27%;">{{$raw->relation_type}}</th>
                    		
                    		@endforeach
                    	</tr>	
                    
 

					
				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(i)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Cash In Hand</td>
                      
				   @foreach($data['cash_in_hand'] as $key => $raw)

				            <td style=" border: 1px solid black;border-collapse: collapse;">@if($raw->cash_in_hand)
							&#8377; {{$raw->cash_in_hand}}
							@else {{Lang::get('affidavit.nil')}} @endif
							</td>
				        @endforeach

				    </tr>

				    <tr style="border:1px solid black;">
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(ii)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Details of deposit in Bank accounts (FDRs, Term Deposits and all other types <br> of deposits including saving accounts), Deposits with Financial Institutions,Non-Banking Financial<br> Companies and Cooperative societies and the amount in each such deposit</td>
                  <td style=" border: 1px solid black;border-collapse: collapse;">

                  	@if(!empty($data['bank_details_1']))
				        @foreach($data['bank_details_1'] as $key => $raw)
				       
				            @if($raw->bank_name)
							 {{$raw->bank_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                            
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif

							@if($raw->amount)
							 {{$raw->amount}}
							@else {{Lang::get('affidavit.nil')}} @endif
                       <br></br>
                       <u></u>
                             @endforeach
                       @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
				        	@if(!empty($data['bank_details_2']))
				        @foreach($data['bank_details_2'] as $key => $raw)
				            @if($raw->bank_name)
							 {{$raw->bank_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                            
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif

							@if($raw->amount)
							 {{$raw->amount}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       <br></br>
                             @endforeach
                       @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
				        	@if(!empty($data['bank_details_3']))
				        @foreach($data['bank_details_3'] as $key => $raw)
				            @if($raw->bank_name)
							 {{$raw->bank_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                           
							@if($raw->account_type)
							{{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif

							@if($raw->amount)
							 {{$raw->amount}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       <br></br>
                            @endforeach
                       @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
				        	@if(!empty($data['bank_details_4']))
				        @foreach($data['bank_details_4'] as $key => $raw)
				            @if($raw->bank_name)
							 {{$raw->bank_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                            
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif

							@if($raw->amount)
							{{$raw->amount}},
							@else {{Lang::get('affidavit.nil')}} @endif
<br></br>
							@endforeach
                       @else
                       Nil
                       @endif
                            <br></br>
                           	
				        
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
				        	@if(!empty($data['bank_details_5']))
				        @foreach($data['bank_details_5'] as $key => $raw)
				            @if($raw->bank_name)
							 {{$raw->bank_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                            
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif

							@if($raw->amount)
							 {{$raw->amount}}
							@else {{Lang::get('affidavit.nil')}} @endif
							<br></br>
                       @endforeach
                       @else
                       Nil
                       @endif
				        </td>
				         <td style=" border: 1px solid black;border-collapse: collapse;">
				        	@if(!empty($data['bank_details_6']))
				        @foreach($data['bank_details_6'] as $key => $raw)
				            @if($raw->bank_name)
							 {{$raw->bank_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                            
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif

							@if($raw->amount)
							 {{$raw->amount}}
							@else {{Lang::get('affidavit.nil')}} @endif
							<br></br>
                       @endforeach
                       @else
                       Nil
                       @endif
				        </td>
				    </tr>

				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(iii)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Details of investment in Bonds, Debentures/Shares and units in companies/Mutual Funds and others and the amount.</td>
                     <td style=" border: 1px solid black;border-collapse: collapse;">

                     	@if(!empty($data['investment_details_1']))
				      @foreach($data['investment_details_1'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 {{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->company_investment_type)
							 {{$raw->company_investment_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                                 	
				            	<br></br>
				        @endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">

                     	@if(!empty($data['investment_details_2']))
				      @foreach($data['investment_details_2'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 {{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->company_investment_type)
							 {{$raw->company_investment_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->account_type)
							{{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                                 	
				            	<br></br>
				        @endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">

                     	@if(!empty($data['investment_details_3']))
				      @foreach($data['investment_details_3'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 {{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->company_investment_type)
							 {{$raw->company_investment_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                                 	
				           <br></br> 	
				        @endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">

                     	@if(!empty($data['investment_details_4']))
				      @foreach($data['investment_details_4'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 {{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->company_investment_type)
							 {{$raw->company_investment_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                          <br></br>       	
				            	
				        @endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">

                     	@if(!empty($data['investment_details_5']))
				      @foreach($data['investment_details_5'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 {{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->company_investment_type)
							 {{$raw->company_investment_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                                 	
				          <br></br>  	
				        @endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">

                     	@if(!empty($data['investment_details_6']))
				      @foreach($data['investment_details_6'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 {{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->company_investment_type)
							 {{$raw->company_investment_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->account_type)
							 {{$raw->account_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; Amount:{{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                                 	
				            	
				        @endforeach
				        @else
                       Nil
                       @endif
				        </td>
				    </tr>

				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(iv)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Details of investment in NSS, Postal Saving,Insurance Policies and investment in any Financial instruments in Post office or Insurance Company and the amount</td>

                    <td style=" border: 1px solid black;border-collapse: collapse;">
                      @if(!empty($data['savings_and_policies_1']))
				      @foreach($data['savings_and_policies_1'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 Company Name:{{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->saving_type)
							 Type:{{$raw->saving_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->account_type)
							Account Type: {{$raw->account_type}} {{$raw->joint_account_with_name}}
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; Amount:{{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
                      @if(!empty($data['savings_and_policies_2']))
				      @foreach($data['savings_and_policies_2'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 Company Name:{{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->saving_type)
							 Type:{{$raw->saving_type}}, 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->account_type)
							Account Type: {{$raw->account_type}} {{$raw->joint_account_with_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; Amount:{{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
                      @if(!empty($data['savings_and_policies_3']))
				      @foreach($data['savings_and_policies_3'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 Company Name:{{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->saving_type)
							 Type:{{$raw->saving_type}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->account_type)
							Account Type: {{$raw->account_type}} {{$raw->joint_account_with_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; Amount:{{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
                      @if(!empty($data['savings_and_policies_4']))
				      @foreach($data['savings_and_policies_4'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 Company Name:{{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->saving_type)
							 Type:{{$raw->saving_type}}, 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->account_type)
							Account Type: {{$raw->account_type}} {{$raw->joint_account_with_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; Amount:{{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
                      @if(!empty($data['savings_and_policies_5']))
				      @foreach($data['savings_and_policies_5'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 Company Name:{{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->saving_type)
							 Type:{{$raw->saving_type}}, 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->account_type)
							Account Type: {{$raw->account_type}} {{$raw->joint_account_with_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; Amount:{{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
				        </td>
				        <td style=" border: 1px solid black;border-collapse: collapse;">
                      @if(!empty($data['savings_and_policies_6']))
				      @foreach($data['savings_and_policies_6'] as $key => $raw)
				            
				            	
                        @if($raw->company)
							 Company Name:{{$raw->company}},
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->saving_type)
							 Type:{{$raw->saving_type}} ,
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->account_type)
							Account Type: {{$raw->account_type}} {{$raw->joint_account_with_name}},
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            @if($raw->amount) &#8377; Amount:{{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
				        </td>

				    </tr>
				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(v)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Personal loans/advance given to any person or entity including firm,company, Trust etc. and other receivables from debtors and the amount</td>
                      <td style=" border: 1px solid black;border-collapse: collapse;">
                      	@if(!empty($data['loan_details_1']))
				      @foreach($data['loan_details_1'] as $key => $raw)
				            
				            	
                        {{Lang::get('affidavit.bank_company_name')}}:@if($raw->loan_to)
							 {{Lang::get('affidavit.bank_company_name')}}:{{$raw->loan_to}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.nature_of_loan')}}:@if($raw->nature_of_loan)
							 {{$raw->nature_of_loan}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.account_details')}}:@if($raw->loan_account_type)
							 {{$raw->loan_account_type}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                        
                        
                        OutStanding Amount: @if($raw->outstanding_amount) 
                         {{$raw->outstanding_amount}} 
                         @else {{Lang::get('affidavit.nil')}} @endif
                        
                        <br></br>
                       

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                      	@if(!empty($data['loan_details_2']))
				      @foreach($data['loan_details_2'] as $key => $raw)
				            
				            	
                         {{Lang::get('affidavit.bank_company_name')}}:@if($raw->loan_to)
							 {{Lang::get('affidavit.bank_company_name')}}:{{$raw->loan_to}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.nature_of_loan')}}:@if($raw->nature_of_loan)
							 {{$raw->nature_of_loan}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.account_details')}}:@if($raw->loan_account_type)
							 {{$raw->loan_account_type}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                        
                        
                        OutStanding Amount: @if($raw->outstanding_amount) 
                         {{$raw->outstanding_amount}} 
                         @else {{Lang::get('affidavit.nil')}} @endif
                        
                        <br></br>
                       

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                      	@if(!empty($data['loan_details_3']))
				      @foreach($data['loan_details_3'] as $key => $raw)
				            
				            	
                         {{Lang::get('affidavit.bank_company_name')}}:@if($raw->loan_to)
							 {{Lang::get('affidavit.bank_company_name')}}:{{$raw->loan_to}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.nature_of_loan')}}:@if($raw->nature_of_loan)
							 {{$raw->nature_of_loan}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.account_details')}}:@if($raw->loan_account_type)
							 {{$raw->loan_account_type}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                        
                        
                        OutStanding Amount: @if($raw->outstanding_amount) 
                         {{$raw->outstanding_amount}} 
                         @else {{Lang::get('affidavit.nil')}} @endif
                        
                        <br></br>
                       

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                      	@if(!empty($data['loan_details_4']))
				      @foreach($data['loan_details_4'] as $key => $raw)
				            
				            	
                         {{Lang::get('affidavit.bank_company_name')}}:@if($raw->loan_to)
							 {{Lang::get('affidavit.bank_company_name')}}:{{$raw->loan_to}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.nature_of_loan')}}:@if($raw->nature_of_loan)
							 {{$raw->nature_of_loan}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.account_details')}}:@if($raw->loan_account_type)
							 {{$raw->loan_account_type}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                        
                        
                        OutStanding Amount: @if($raw->outstanding_amount) 
                         {{$raw->outstanding_amount}} 
                         @else {{Lang::get('affidavit.nil')}} @endif
                        <br></br>
                       

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                      	@if(!empty($data['loan_details_5']))
				      @foreach($data['loan_details_5'] as $key => $raw)
				            
				            	
                         {{Lang::get('affidavit.bank_company_name')}}:@if($raw->loan_to)
							 {{Lang::get('affidavit.bank_company_name')}}:{{$raw->loan_to}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.nature_of_loan')}}:@if($raw->nature_of_loan)
							 {{$raw->nature_of_loan}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.account_details')}}:@if($raw->loan_account_type)
							 {{$raw->loan_account_type}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                        
                        
                        OutStanding Amount: @if($raw->outstanding_amount) 
                         {{$raw->outstanding_amount}} 
                         @else {{Lang::get('affidavit.nil')}} @endif
                        
                        <br></br>
                       

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                      	@if(!empty($data['loan_details_6']))
				      @foreach($data['loan_details_6'] as $key => $raw)
				            
				            	
                         {{Lang::get('affidavit.bank_company_name')}}:@if($raw->loan_to)
							 {{Lang::get('affidavit.bank_company_name')}}:{{$raw->loan_to}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.nature_of_loan')}}:@if($raw->nature_of_loan)
							 {{$raw->nature_of_loan}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
						{{Lang::get('affidavit.account_details')}}:@if($raw->loan_account_type)
							 {{$raw->loan_account_type}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                        
                        
                        OutStanding Amount: @if($raw->outstanding_amount) 
                         {{$raw->outstanding_amount}} 
                         @else {{Lang::get('affidavit.nil')}} @endif
                        
                        <br></br>
                       

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
				    </tr>

				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(vi)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Motor Vehicles/Aircrafts/Yachts/Ships (Details of Make, registration number.etc. year of purchase and amount )</td>



                    <td style=" border: 1px solid black;border-collapse: collapse;">
                    	@if(!empty($data['vehicle_details_1']))
				      @foreach($data['vehicle_details_1'] as $key => $raw)
				            
				            	
                        @if($raw->vehicle_type)
							 Veh Type:{{$raw->vehicle_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->make)
							 {{$raw->make}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->registration_no)
							 Reg No:{{$raw->registration_no}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            

                             @if($raw->year_of_purchase) Year: {{$raw->year_of_purchase}} @else {{Lang::get('affidavit.nil')}} @endif
                            
                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                    	@if(!empty($data['vehicle_details_2']))
				      @foreach($data['vehicle_details_2'] as $key => $raw)
				            
				            	
                        @if($raw->vehicle_type)
							 Veh Type:{{$raw->vehicle_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->make)
							 {{$raw->make}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->registration_no)
							 Reg No:{{$raw->registration_no}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            

                             @if($raw->year_of_purchase) Year: {{$raw->year_of_purchase}} @else {{Lang::get('affidavit.nil')}} @endif
                            
                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                    	@if(!empty($data['vehicle_details_3']))
				      @foreach($data['vehicle_details_3'] as $key => $raw)
				            
				            	
                        @if($raw->vehicle_type)
							 Veh Type:{{$raw->vehicle_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->make)
							 {{$raw->make}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->registration_no)
							 Reg No:{{$raw->registration_no}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            

                             @if($raw->year_of_purchase) Year: {{$raw->year_of_purchase}} @else {{Lang::get('affidavit.nil')}} @endif
                            
                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                    	@if(!empty($data['vehicle_details_4']))
				      @foreach($data['vehicle_details_4'] as $key => $raw)
				            
				            	
                        @if($raw->vehicle_type)
							 Veh Type:{{$raw->vehicle_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->make)
							 {{$raw->make}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->registration_no)
							 Reg No:{{$raw->registration_no}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            

                             @if($raw->year_of_purchase) Year: {{$raw->year_of_purchase}} @else {{Lang::get('affidavit.nil')}} @endif
                            
                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                    	@if(!empty($data['vehicle_details_5']))
				      @foreach($data['vehicle_details_5'] as $key => $raw)
				            
				            	
                        @if($raw->vehicle_type)
							 Veh Type:{{$raw->vehicle_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->make)
							 {{$raw->make}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->registration_no)
							 Reg No:{{$raw->registration_no}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            

                             @if($raw->year_of_purchase) Year: {{$raw->year_of_purchase}} @else {{Lang::get('affidavit.nil')}} @endif
                            
                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                    	@if(!empty($data['vehicle_details_6']))
				      @foreach($data['vehicle_details_6'] as $key => $raw)
				            
				            	
                        @if($raw->vehicle_type)
							 Veh Type:{{$raw->vehicle_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->make)
							 {{$raw->make}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                        
							@if($raw->registration_no)
							 Reg No:{{$raw->registration_no}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                       
                            

                             @if($raw->year_of_purchase) Year: {{$raw->year_of_purchase}} @else {{Lang::get('affidavit.nil')}} @endif
                            
                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
				    </tr>

				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(vii)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Jewellery, bullion and valuable thing(s) (give details of weight and value)</td>

				        <td style=" border: 1px solid black;border-collapse: collapse;">
                     @if(!empty($data['valuable_things_details_1']))
				     @foreach($data['valuable_things_details_1'] as $key => $raw)
				            
				            	
                       Type: @if($raw->valuable_type)
							 {{$raw->valuable_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                         
							Weight: @if($raw->weight)
							 {{$raw->weight}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                         
						

                             Amount: @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>

                   <td style=" border: 1px solid black;border-collapse: collapse;">
                     @if(!empty($data['valuable_things_details_2']))
				     @foreach($data['valuable_things_details_2'] as $key => $raw)
				            
				            	
                       Type: @if($raw->valuable_type)
							 {{$raw->valuable_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                         
							Weight: @if($raw->weight)
							 {{$raw->weight}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                         
						

                             Amount: @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                     @if(!empty($data['valuable_things_details_3']))
				     @foreach($data['valuable_things_details_3'] as $key => $raw)
				            
				            	
                        Type: @if($raw->valuable_type)
							 {{$raw->valuable_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                         
							Weight: @if($raw->weight)
							 {{$raw->weight}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                         
						

                             Amount: @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                     @if(!empty($data['valuable_things_details_4']))
				     @foreach($data['valuable_things_details_4'] as $key => $raw)
				            
				            	
                        Type: @if($raw->valuable_type)
							 {{$raw->valuable_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                         
							Weight: @if($raw->weight)
							 {{$raw->weight}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                         
						

                             Amount: @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                     @if(!empty($data['valuable_things_details_5']))
				     @foreach($data['valuable_things_details_5'] as $key => $raw)
				            
				            	
                        Type: @if($raw->valuable_type)
							 {{$raw->valuable_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                         
							Weight: @if($raw->weight)
							 {{$raw->weight}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                         
						

                             Amount: @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                     @if(!empty($data['valuable_things_details_6']))
				     @foreach($data['valuable_things_details_6'] as $key => $raw)
				            
				            	
                        Type: @if($raw->valuable_type)
							 {{$raw->valuable_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                         
							Weight: @if($raw->weight)
							 {{$raw->weight}} 
							@else {{Lang::get('affidavit.nil')}} @endif
                         
						

                             Amount: @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         <br></br>

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
				    </tr>



				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(viii)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">Any other assets such as value of claims/interest</td>


                       <td style=" border: 1px solid black;border-collapse: collapse;">
                       @if(!empty($data['other_assets_1']))
				       @foreach($data['other_assets_1'] as $key => $raw)
				            
				            	
                        @if($raw->asset_type)
							 Asset type:{{$raw->asset_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->brief_details)
							Details:{{$raw->brief_details}} 
							@else {{Lang::get('affidavit.nil')}} @endif

						

                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['other_assets_2']))
				       @foreach($data['other_assets_2'] as $key => $raw)
				            
				            	
                        @if($raw->asset_type)
							 Asset type:{{$raw->asset_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->brief_details)
							Details:{{$raw->brief_details}} 
							@else {{Lang::get('affidavit.nil')}} @endif

						

                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['other_assets_3']))
				       @foreach($data['other_assets_3'] as $key => $raw)
				            
				            	
                        @if($raw->asset_type)
							 Asset type:{{$raw->asset_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->brief_details)
							Details:{{$raw->brief_details}} 
							@else {{Lang::get('affidavit.nil')}} @endif

						

                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['other_assets_4']))
				       @foreach($data['other_assets_4'] as $key => $raw)
				            
				            	
                        @if($raw->asset_type)
							 Asset type:{{$raw->asset_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->brief_details)
							Details:{{$raw->brief_details}} 
							@else {{Lang::get('affidavit.nil')}} @endif

						

                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['other_assets_5']))
				       @foreach($data['other_assets_5'] as $key => $raw)
				            
				            	
                        @if($raw->asset_type)
							 Asset type:{{$raw->asset_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->brief_details)
							Details:{{$raw->brief_details}} 
							@else {{Lang::get('affidavit.nil')}} @endif

						

                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['other_assets_6']))
				       @foreach($data['other_assets_6'] as $key => $raw)
				            
				            	
                        @if($raw->asset_type)
							 Asset type:{{$raw->asset_type}}
							@else {{Lang::get('affidavit.nil')}} @endif
                          
							@if($raw->brief_details)
							Details:{{$raw->brief_details}} 
							@else {{Lang::get('affidavit.nil')}} @endif

						

                             @if($raw->amount) &#8377; Amount: {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
				    </tr>
				    <tr style="border:1px solid black;">
				    	<td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">(ix)</td>
				        <td style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><b>Gross Total Value</b></td>




                            
                            



                       <td style=" border: 1px solid black;border-collapse: collapse;">
                      @if(!empty($data['movable_assets_total_test1']))
                          	@foreach($data['movable_assets_total_test1'] as $raw)
				            
				            	
                        @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['movable_assets_total_test2']))
                          	@foreach($data['movable_assets_total_test2'] as $raw)
				            
				            	
                        @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['movable_assets_total_test3']))
                          	@foreach($data['movable_assets_total_test3'] as $raw)
				            
				            	
                        @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                    @if(!empty($data['movable_assets_total_test4']))
                          	@foreach($data['movable_assets_total_test4'] as $raw)
				            
				            	
                        @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                    @if(!empty($data['movable_assets_total_test5']))
                          	@foreach($data['movable_assets_total_test5'] as $raw)
				            
				            	
                        @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                  @if(!empty($data['movable_assets_total_test6']))
                          	@foreach($data['movable_assets_total_test6'] as $raw)
				            
				            	
                        @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                        

				            	
				            	@endforeach
				        @else
                       Nil
                       @endif
                   </td>
				    </tr>

				    
</table>



<table width="100%" class="top top-20" border="0">
						<tr align="left">
                    		<th align="left">{{Lang::get('affidavit.details_of_immovable_assets')}}</th>
                    	</tr>
                    	<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="105" class="pad-35">{{Lang::get('affidavit.note')}}: 1. </th>
                    					<td>{{Lang::get('affidavit.properties_in_joint_ownership_indicating_the_extent_of_joint_ownership_will_also_have_to_be_indicated')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="105" class="pad-35">{{Lang::get('affidavit.note')}}: 2. </th>
                    					<td>{{Lang::get('affidavit.each_land_or_building_or_apartment_should_be_mentioned_separately_in_this_format')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="105" class="pad-35">{{Lang::get('affidavit.note')}}: 3. </th>
                    					<td>{{Lang::get('affidavit.details_should_include_the_interest_in_or_ownership_of_offshore_assets')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr> 
					</table>







<table width="100%" class="top-20" border="1">
	
            	
                        
                   <!-------------------- Agreculture land Start----------------------> 	
                               
                  
                  <tr style="border:1px solid black;" class="thHeading">
                    		<th style="max-width:15%;min-width:3%;">Sl. NO</th>
                    		<th style="max-width:45%;min-width:25%;"> Description</th>
                        @foreach($data['non_relation_details'] as $key => $raw)	
                    		<th style="max-width:90%;min-width:27%;">{{$raw->relation_type}}</th>
                    		
                    		@endforeach
                    	</tr>	

                        
                    	<tr style="border:1px solid black;">
						<td rowspan="7"  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(i)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b colspan="2">{{Lang::get('affidavit.agricultural_land')}}</b>
								 <p>{{Lang::get('affidavit.location')}},
                                {{Lang::get('affidavit.survey_no')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_1']))
							@foreach($data['agricultural_land_test_1'] as $key => $raw)
                             
                             @if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,
                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_2']))
							@foreach($data['agricultural_land_test_2'] as $key => $raw)
                            
                            @if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,
                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_4']))
							@foreach($data['agricultural_land_test_4'] as $key => $raw)
							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_5']))
							@foreach($data['agricultural_land_test_5'] as $key => $raw)

							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_6']))
							@foreach($data['agricultural_land_test_6'] as $key => $raw)

							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.area')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_1']))
							@foreach($data['agricultural_land_test_1'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_2']))
							@foreach($data['agricultural_land_test_2'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_4']))
							@foreach($data['agricultural_land_test_4'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_5']))
							@foreach($data['agricultural_land_test_5'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_6']))
							@foreach($data['agricultural_land_test_6'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>


<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.whether_inherited_property')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_1']))
							@foreach($data['agricultural_land_test_1'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_2']))
							@foreach($data['agricultural_land_test_2'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_4']))
							@foreach($data['agricultural_land_test_4'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_5']))
							@foreach($data['agricultural_land_test_5'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_6']))
							@foreach($data['agricultural_land_test_6'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
                                <p>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_1']))
							@foreach($data['agricultural_land_test_1'] as $key => $raw)

                            @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_2']))
							@foreach($data['agricultural_land_test_2'] as $key => $raw)

                           @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

                            @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_4']))
							@foreach($data['agricultural_land_test_4'] as $key => $raw)

                          @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_5']))
							@foreach($data['agricultural_land_test_5'] as $key => $raw)

                         @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_6']))
							@foreach($data['agricultural_land_test_6'] as $key => $raw)

                         @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_1']))
							@foreach($data['agricultural_land_test_1'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_2']))
							@foreach($data['agricultural_land_test_2'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_4']))
							@foreach($data['agricultural_land_test_4'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_5']))
							@foreach($data['agricultural_land_test_5'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_6']))
							@foreach($data['agricultural_land_test_6'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_1']))
							@foreach($data['agricultural_land_test_1'] as $key => $raw)

                           @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_2']))
							@foreach($data['agricultural_land_test_2'] as $key => $raw)

                          @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

                             @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_4']))
							@foreach($data['agricultural_land_test_4'] as $key => $raw)

                             @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_5']))
							@foreach($data['agricultural_land_test_5'] as $key => $raw)
                         @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_6']))
							@foreach($data['agricultural_land_test_6'] as $key => $raw)

                             @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>

<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.approximate_current_market_value')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_1']))
							@foreach($data['agricultural_land_test_1'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_2']))
							@foreach($data['agricultural_land_test_2'] as $key => $raw)

                           @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

                         @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_4']))
							@foreach($data['agricultural_land_test_4'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_5']))
							@foreach($data['agricultural_land_test_5'] as $key => $raw)

                           @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['agricultural_land_test_6']))
							@foreach($data['agricultural_land_test_6'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<!------------------------ End Agreculture Land------------------------------->


<!-------------------- Non Agreculture land Start----------------------> 	
                               
              

                        
                    	<tr style="border:1px solid black;">
						<td rowspan="7"  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(ii)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b colspan="2">{{Lang::get('affidavit.non_agricultural_land')}}</b>
								<p>{{Lang::get('affidavit.location')}},
                                {{Lang::get('affidavit.survey_no')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_1']))
							@foreach($data['non_agricultural_land_test_1'] as $key => $raw)
                            
                            @if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,
                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_2']))
							@foreach($data['non_agricultural_land_test_2'] as $key => $raw)

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_3']))
							@foreach($data['non_agricultural_land_test_3'] as $key => $raw)

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_4']))
							@foreach($data['non_agricultural_land_test_4'] as $key => $raw)

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_5']))
							@foreach($data['non_agricultural_land_test_5'] as $key => $raw)

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_6']))
							@foreach($data['non_agricultural_land_test_6'] as $key => $raw)

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.area')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_1']))
							@foreach($data['non_agricultural_land_test_1'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_2']))
							@foreach($data['non_agricultural_land_test_2'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_3']))
							@foreach($data['non_agricultural_land_test_3'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_4']))
							@foreach($data['non_agricultural_land_test_4'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_5']))
							@foreach($data['non_agricultural_land_test_5'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_6']))
							@foreach($data['non_agricultural_land_test_6'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>


<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.whether_inherited_property')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_1']))
							@foreach($data['non_agricultural_land_test_1'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_2']))
							@foreach($data['non_agricultural_land_test_2'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_3']))
							@foreach($data['agricultural_land_test_3'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_4']))
							@foreach($data['non_agricultural_land_test_4'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_5']))
							@foreach($data['non_agricultural_land_test_5'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_6']))
							@foreach($data['non_agricultural_land_test_6'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
                                <p>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_1']))
							@foreach($data['non_agricultural_land_test_1'] as $key => $raw)

                            @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_2']))
							@foreach($data['non_agricultural_land_test_2'] as $key => $raw)

                           @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_3']))
							@foreach($data['non_agricultural_land_test_3'] as $key => $raw)

                            @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_4']))
							@foreach($data['non_agricultural_land_test_4'] as $key => $raw)

                          @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_5']))
							@foreach($data['non_agricultural_land_test_5'] as $key => $raw)

                         @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_6']))
							@foreach($data['non_agricultural_land_test_6'] as $key => $raw)

                         @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_1']))
							@foreach($data['non_agricultural_land_test_1'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_2']))
							@foreach($data['non_agricultural_land_test_2'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_3']))
							@foreach($data['non_agricultural_land_test_3'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_4']))
							@foreach($data['non_agricultural_land_test_4'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_5']))
							@foreach($data['non_agricultural_land_test_5'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_6']))
							@foreach($data['non_agricultural_land_test_6'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_1']))
							@foreach($data['non_agricultural_land_test_1'] as $key => $raw)

                           @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_2']))
							@foreach($data['non_agricultural_land_test_2'] as $key => $raw)

                          @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_3']))
							@foreach($data['non_agricultural_land_test_3'] as $key => $raw)

                             @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_4']))
							@foreach($data['non_agricultural_land_test_4'] as $key => $raw)

                             @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_5']))
							@foreach($data['non_agricultural_land_test_5'] as $key => $raw)
                         @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_6']))
							@foreach($data['non_agricultural_land_test_6'] as $key => $raw)

                             @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>

<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.approximate_current_market_value')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_1']))
							@foreach($data['non_agricultural_land_test_1'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_2']))
							@foreach($data['non_agricultural_land_test_2'] as $key => $raw)

                           @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_3']))
							@foreach($data['non_agricultural_land_test_3'] as $key => $raw)

                         @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_4']))
							@foreach($data['non_agricultural_land_test_4'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_5']))
							@foreach($data['non_agricultural_land_test_5'] as $key => $raw)

                           @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['non_agricultural_land_test_6']))
							@foreach($data['non_agricultural_land_test_6'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<!------------------------ End  Non Agreculture Land------------------------------->


<!-------------------- Commercial Building Start----------------------> 	
                               
              

                        
                    	<tr style="border:1px solid black;">
						<td rowspan="8"  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(iii)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b colspan="2">Commercial Buildings<br>(including apartments)</br></b>
								<p>{{Lang::get('affidavit.location')}},
                                {{Lang::get('affidavit.survey_no')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)
                            
                            @if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,
                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

							   @if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)
							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)
							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)

							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.area')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>


<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.built_up_area')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)

                            @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

                            @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)

                            @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)

                            @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)

                            @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

                            @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
                                <p>{{Lang::get('affidavit.whether_inherited_property')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

                          @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)

                            @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)

                          @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)

                         @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

                         @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)

                          @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

                            @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)

                          @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)

                           @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)

                           @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

                            @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.cost_of_property_at_the_time_of_purchase')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)

                            @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

                           @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)

                              @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)

                             @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)
                          @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

                             @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>

<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.any_investment_on_the_property_by_way_of_development')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)

                          @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

                         @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)

                         @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)

                          @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)

                           @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

                           @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>



<tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								
                                <p>{{Lang::get('affidavit.approximate_current_market_value')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_1']))
							@foreach($data['aff_commercial_buildings_details_1'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_2']))
							@foreach($data['aff_commercial_buildings_details_2'] as $key => $raw)

                         @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_3']))
							@foreach($data['aff_commercial_buildings_details_3'] as $key => $raw)

                         @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_4']))
							@foreach($data['aff_commercial_buildings_details_4'] as $key => $raw)

                          @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_5']))
							@foreach($data['aff_commercial_buildings_details_5'] as $key => $raw)

                           @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_commercial_buildings_details_6']))
							@foreach($data['aff_commercial_buildings_details_6'] as $key => $raw)

                           @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	


                 			
              </tr>


   

   


<!------------------------ End  Commercial Building------------------------------->


<!------------------------   Residential Building Start------------------------------->

     <tr style="border:1px solid black;">
						<td rowspan="8"  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(iv)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b colspan="2">Residential Building<br>(including apartments)</br></b>
								<p>{{Lang::get('affidavit.location')}},
                                {{Lang::get('affidavit.survey_no')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                            @if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,
                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							   @if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)
							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)
							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif,

                            @if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

              <tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.area')}}</p>
             

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                          
                            @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							     @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)


							   @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)



							   @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

							 @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							 @if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>



    <tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.built_up_area')}}</p>
             

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                          
                             @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							     @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)


							  @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)


							
							  @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

							 @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							 @if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

               <tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.whether_inherited_property')}} ({{Lang::get('affidavit.yes')}} {{Lang::get('affidavit.or')}} {{Lang::get('affidavit.no')}})</p>
             

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                          
                             @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							     @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)


							  @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)


							
							  @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

							 @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							 @if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

                 <tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}} </p>
             

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                          
                            @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							    @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)


							 @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)


							
							 @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

							@if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							 @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

               <tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.cost_of_property_at_the_time_of_purchase')}} </p>
             

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                          
                            @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							    @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)


							 @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)


							
							@if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

							@if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							 @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

                  <tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.any_investment_on_the_property_by_way_of_development')}} </p>
             

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                          
                           @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							    @if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)


							@if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)


							
						@if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

						@if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							@if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

 <tr style="border:1px solid black;">
						
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.approximate_current_market_value')}} </p>
             

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_1']))
							@foreach($data['aff_residential_buildings_details_1'] as $key => $raw)
                            
                          
                           @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_2']))
							@foreach($data['aff_residential_buildings_details_2'] as $key => $raw)

							     @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_3']))
							@foreach($data['aff_residential_buildings_details_3'] as $key => $raw)


						  @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_4']))
							@foreach($data['aff_residential_buildings_details_4'] as $key => $raw)


							
						  @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_5']))
							@foreach($data['aff_residential_buildings_details_5'] as $key => $raw)

						  @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_residential_buildings_details_6']))
							@foreach($data['aff_residential_buildings_details_6'] as $key => $raw)

							  @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>



<!------------------------ Residential Building End------------------------------->
<!------------------------ Other interst in property Start------------------------------->



    <tr style="border:1px solid black;">
						<td rowspan="1"  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(v)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>{{Lang::get('affidavit.other_assets')}}</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_other_immovable_assets_1']))
							@foreach($data['aff_other_immovable_assets_1'] as $key => $raw)
                            
                           @if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif,
                           @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_other_immovable_assets_2']))
							@foreach($data['aff_other_immovable_assets_2'] as $key => $raw)

							    @if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif,
                           @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_other_immovable_assets_3']))
							@foreach($data['aff_other_immovable_assets_3'] as $key => $raw)


							 @if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif,
                           @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_other_immovable_assets_4']))
							@foreach($data['aff_other_immovable_assets_4'] as $key => $raw)


							 @if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif,
                           @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_other_immovable_assets_5']))
							@foreach($data['aff_other_immovable_assets_5'] as $key => $raw)

							 @if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif,
                           @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['aff_other_immovable_assets_6']))
							@foreach($data['aff_other_immovable_assets_6'] as $key => $raw)

							 @if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif,
                           @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

<!------------------------ Other interst in property End------------------------------->

<!------------------------ Other interst in property Start------------------------------->



    <tr style="border:1px solid black;">
						<td rowspan="1"  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(vi)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								
								<p>Total of current<br>market value of<br>(i) to (iv) above</p>

							</td>

							<td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['immoveable_assets_total_test1']))
							@foreach($data['immoveable_assets_total_test1'] as $key => $raw)
                            
                          @if($raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value) &#8377; {{$raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value}}@else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		
                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['immoveable_assets_total_test2']))
							@foreach($data['immoveable_assets_total_test2'] as $key => $raw)

							    @if($raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value) &#8377; {{$raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value}}@else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['immoveable_assets_total_test3']))
							@foreach($data['immoveable_assets_total_test3'] as $key => $raw)


							 @if($raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value) &#8377; {{$raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value}}@else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		

                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['immoveable_assets_total_test4']))
							@foreach($data['immoveable_assets_total_test4'] as $key => $raw)


							 @if($raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value) &#8377; {{$raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value}}@else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['immoveable_assets_total_test5']))
							@foreach($data['immoveable_assets_total_test5'] as $key => $raw)

							 @if($raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value) &#8377; {{$raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value}}@else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>		



                 <td style=" border: 1px solid black;border-collapse: collapse;">
                   @if(!empty($data['immoveable_assets_total_test6']))
							@foreach($data['immoveable_assets_total_test6'] as $key => $raw)

							  @if($raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value) &#8377; {{$raw->self_acquired_Assets_Value + $raw->Inherited_assets_Value}}@else {{Lang::get('affidavit.nil')}} @endif
                         
                             
                            <br></br>

                             @endforeach
				        @else
                       Nil
                       @endif	
                 </td>	



              </tr>

<!------------------------ Total Current market value not incude approximate End------------------------------->



								
              


</table>

<table width="100%" class="top-20 top">
                    	<tr>
                    		<td>
                    			<span class="w-20 bold">(8)</span>
                    			<span class="inBlock bold">{{Lang::get('affidavit.i_give_herein_below_the_details_of_liabilities_dues_to_public_financial_institutions_and_government')}}-</span>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td>
                    			<span class="w-20"></span>
                    			<span class="inBlock">({{Lang::get('affidavit.note')}}: {{Lang::get('affidavit.please_give_separate_details_of_name_of_bank_institution_entity_or_individual_and_amount_before_each_item')}}) </span>
                    		</td>
                    	</tr>
                    	                 	
                    	<!--<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="95">{{Lang::get('affidavit.note')}}: 1. </th>
                    					<td align="left">Assets in joint name indicating the extent of joint ownership will also have to be given.</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>-->
                    </table>



<table width="100%" class="top-20" border="1">
	
                  <tr style="border:1px solid black;" class="thHeading">
                    		<th style="max-width:15%;min-width:3%;">Sl. NO</th>
                    		<th style="max-width:45%;min-width:25%;"> Description</th>
                        @foreach($data['non_relation_details'] as $key => $raw)	
                    		<th style="max-width:90%;min-width:27%;">{{$raw->relation_type}}</th>
                    		
                    		@endforeach
                    	</tr>	
                        
                    	<tr style="border:1px solid black;">
								<td rowspan="3" style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(i)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b>{{Lang::get('affidavit.loan_or_dues_to_bank_financial_institution')}}</b>
								<p>{{Lang::get('affidavit.name_of_bank_or_financial_institution_amount_outstanding_nature_of_loan')}}</p>
                               

							</td>

							
                   @if(!empty($data['l_loan_details']))
							@foreach($data['l_loan_details'] as $key => $raw)
<td style=" border: 1px solid black;border-collapse: collapse;">
                            @if($raw->bank_inst_name) {{$raw->bank_inst_name}} @else {{Lang::get('affidavit.nil')}} @endif,<br></br>
                            @if($raw->outstanding_amount) {{$raw->outstanding_amount}} @else {{Lang::get('affidavit.nil')}} @endif,
                            <br></br>
                            @if($raw->loan_type) {{$raw->loan_type}} @else {{Lang::get('affidavit.nil')}} @endif
                            
                            <br></br>
                             
                            <br></br>
                             
                           
                               
                         </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                  
                  
								
              </tr>

                  	
                        
                    	<!-- <tr style="border:1px solid black;">
								<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(ii)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b>Loan or dues to any other individuals/ entity other than mentioned above</b>
								<p>Name(s), Amount outstanding, nature of loan</p>
                               

							</td>

							
                   @if(!empty($data['l_loan_individual']))
							@foreach($data['l_loan_individual'] as $key => $raw)
<td style=" border: 1px solid black;border-collapse: collapse;">
                           @if($raw->individual_entity_name) {{$raw->individual_entity_name}}@else {{Lang::get('affidavit.nil')}} @endif,@if($raw->loan_type) {{$raw->loan_type}}@else {{Lang::get('affidavit.nil')}} @endif,@if($raw->loan_account_type) {{$raw->loan_account_type}}@else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>
                            
                            <br></br>
                               
                         </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                  
                  
								
              </tr>

 -->
                    	<tr style="border:1px solid black;">
								
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b>Loan or dues to any other individuals/ entity other than mentioned above</b>
								<p>Name(s), Amount outstanding, nature of loan</p>
                               

							</td>

							
                   @if(!empty($data['l_loan_individual']))
							@foreach($data['l_loan_individual'] as $key => $raw)
<td style=" border: 1px solid black;border-collapse: collapse;">
                           @if($raw->individual_entity_name) {{$raw->individual_entity_name}}@else {{Lang::get('affidavit.nil')}} @endif,<br></br>
                          
                           <!-- @if($raw->loan_account_type) {{$raw->loan_account_type}}@else {{Lang::get('affidavit.nil')}} @endif,<br></br> -->
                            @if($raw->outstanding_amount) {{$raw->outstanding_amount}}@else {{Lang::get('affidavit.nil')}} @endif <br></br>
                             @if($raw->loan_type) {{$raw->loan_type}}@else {{Lang::get('affidavit.nil')}} @endif,
                            <br></br>
                            
                            <br></br>
                               
                         </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                  
                  
								
              </tr>

<tr style="border:1px solid black;">
								<!-- <td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(iii)</span></td> -->
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b>Any other liability</b>
								

							</td>






							
                   @if(!empty($data['l_other_liabilities']))
							@foreach($data['l_other_liabilities'] as $key => $raw)
<td style=" border: 1px solid black;border-collapse: collapse;">
                           @if($raw->authority_name) {{$raw->authority_name}}@else {{Lang::get('affidavit.nil')}} @endif,@if($raw->details) {{$raw->details}}@else {{Lang::get('affidavit.nil')}} @endif,@if($raw->amount) &#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>
                            
                            <br></br>
                               
                         </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                  
                  
								
              </tr>



<tr style="border:1px solid black;">
								<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block"></span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b>Grand total of liabilities</b>
								

							</td>






							
                   @if(!empty($data['liabilites_total_1']))
							@foreach($data['liabilites_total_1'] as $key => $raw)
							

<td style=" border: 1px solid black;border-collapse: collapse;">
                       @if($raw->Total_Loan > 0) &#8377; {{$raw->Total_Loan+$raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif
                            </td>
                           @endforeach
                            @else
                            <td>Nil</td>
                        @endif




                       @if(!empty($data['liabilites_total_2']))
						@foreach($data['liabilites_total_2'] as $key => $raw)
							
<td style=" border: 1px solid black;border-collapse: collapse;">
                       @if($raw->Total_Loan > 0) &#8377; {{$raw->Total_Loan+$raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif
                            </td>
                             @endforeach
                            @else
                            <td style=" border: 1px solid black;border-collapse: collapse;">Nil</td>
                        @endif



                        @if(!empty($data['liabilites_total_3']))
							@foreach($data['liabilites_total_3'] as $key => $raw)
							<td style=" border: 1px solid black;border-collapse: collapse;">
                       @if($raw->Total_Loan > 0) &#8377; {{$raw->Total_Loan+$raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif
                            </td>
                            @endforeach
                            @else
                            <td style=" border: 1px solid black;border-collapse: collapse;">Nil</td>
                        @endif


                        @if(!empty($data['liabilites_total_4']))
							@foreach($data['liabilites_total_4'] as $key => $raw)
							<td style=" border: 1px solid black;border-collapse: collapse;">
                       @if($raw->Total_Loan > 0) &#8377; {{$raw->Total_Loan+$raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif
                            </td>
                            @endforeach
                            @else
                            <td style=" border: 1px solid black;border-collapse: collapse;">Nil</td>
                        @endif


                         @if(!empty($data['liabilites_total_5']))
							@foreach($data['liabilites_total_5'] as $key => $raw)
							<td style=" border: 1px solid black;border-collapse: collapse;">
                       @if($raw->Total_Loan > 0) &#8377; {{$raw->Total_Loan+$raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif
                            </td>
                          @endforeach
                            @else
                            <td>Nil</td>
                        @endif


                          @if(!empty($data['liabilites_total_6']))
							@foreach($data['liabilites_total_6'] as $key => $raw)
							<td style=" border: 1px solid black;border-collapse: collapse;">
                       @if($raw->Total_Loan > 0) &#8377; {{$raw->Total_Loan+$raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif
                            </td>
                            @endforeach
                            @else
                            <td>Nil</td>
                        @endif


                  
                  
								
              </tr>

              <tr style="border:1px solid black;">
								<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(ii)</span></td>
								

							<td   style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b>Government Dues:</b><br></br>
								<p>Dues to departments dealing with Government accommodation </p>

							</td>

							<td  colspan="4" style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<p>(A) Has the Deponent been in occupation of accommodation provided by the Government at any time during the last ten years before the date of notification of the current election ?<br></br>
						<u> </u>
						<p>(B) If answer to (A) above is YES, the following
						declaration may be furnished namely:-</p><br></br>
						<p>(i) The address of the Government
						accommodation:
						@if(isset($data['l_govt_dues_accommodation']) && !empty($data['l_govt_dues_accommodation']))
						@foreach($data['l_govt_dues_accommodation'] as $key=>$raw)

                         <b>@if($raw->government_accomodation_address) {{$raw->government_accomodation_address}}@else 
                         'N/A' @endif </b> 
                       <b>Amount:@if($raw->amount)&#8377; {{$raw->amount}} @else 
                         'N/A' @endif </b>
						@endforeach
						@endif
						</p>
						<br></br>
						<p>(ii) There is no dues payable in respect of above Government accommodation,
						towards-<br></br>
						(a) rent<br></br>
						(b) electricity charges<br></br>
						(c) water charges<br></br>
						(d) telephone charges as on
						@if(isset($data['l_govt_dues_accommodation']) && !empty($data['l_govt_dues_accommodation']))
						@foreach($data['l_govt_dues_accommodation'] as $key=>$raw)

                        <b> @if($raw->telephone_charges) 
                            {{\Carbon\Carbon::parse($raw->telephone_charges)->format('d/m/Y')}}

                        	@else 
                         'N/A' @endif </b>

						@endforeach
						@endif.



						(date)<br></br>
						[the date should be the last date of the
						third month prior to the month in which
						the election is notified or any date
						thereafter].<br></br>
						Note- ‘No Dues Certificate’ from the
						agencies concerned in respect of rent,
						electricity charges, water charges and
						telephone charges for the above
						Government accommodation should be
						submitted.
														<br></br>
														

							</td>

                    <td  colspan="2" style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								<b>YES/NO(Pl. tick the appropriate alternative)</b><br></br>
								@foreach($data['l_govt_dues_accommodation'] as $key=>$raw)

								<b>@if($raw->is_government_accomodation==0)
                                                    {{Lang::get('affidavit.no') }}
                                                @else
                                            Yes
                                            @endif
                                            @endforeach
								</b>

							</td>


                            
                  
								
              </tr>








</table>










<table width="100%" class="top-20" border="1">
	
                  <tr style="border:1px solid black;" class="thHeading">
                    		<th style="max-width:15%;min-width:3%;">Sl. NO</th>
                    		<th style="max-width:45%;min-width:25%;"> Description</th>
                        @foreach($data['non_relation_details'] as $key => $raw)	
                    		<th style="max-width:90%;min-width:27%;">{{$raw->relation_type}}</th>
                    		
                    		@endforeach
                    	</tr>	
                        
                    	<tr style="border:1px solid black;">
							<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(iii)</span></td>
								
							<td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								Dues to department dealing with Government transport(including aircrafts and helicopters)
								
                            </td>

							
                   @if(!empty($data['l_govt_dues_trasport']))
				   @foreach($data['l_govt_dues_trasport'] as $key => $raw)

                    <td style=" border: 1px solid black;border-collapse: collapse;">
                              @if($raw->due_details) {{$raw->due_details}}@else {{Lang::get('affidavit.nil')}} 
                              @endif,@if($raw->amount)&#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>
                             
                            </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                   

                        </tr>
                        <tr>
                        	<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(iv)</span></td>
                            <td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								Income Tax dues
								
                            </td>
                            @if(!empty($data['l_govt_dues_incometax']))
				   @foreach($data['l_govt_dues_incometax'] as $key => $raw)
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                            @if($raw->due_details) {{$raw->due_details}}@else {{Lang::get('affidavit.nil')}} 
                              @endif,@if($raw->amount)&#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>
                             
                            </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                   

                            </tr>
                        <tr>
                        	<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(v)</span></td>
                            <td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								GST Dues
								
                            </td>
                            @if(!empty($data['l_govt_dues_gstdues']))
				   @foreach($data['l_govt_dues_gstdues'] as $key => $raw)
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                            @if($raw->due_details) {{$raw->due_details}}@else {{Lang::get('affidavit.nil')}} 
                              @endif,@if($raw->amount)&#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>
                             
                            </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                   

                            </tr>
                        <tr>
                        	<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(vi)</span></td>
                            <td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								Municipal/Property tax dues
								
                            </td>
                            @if(!empty($data['l_govt_dues_muncipal']))
				   @foreach($data['l_govt_dues_muncipal'] as $key => $raw)
                    <td style=" border: 1px solid black;border-collapse: collapse;">
                            @if($raw->due_details) {{$raw->due_details}}@else {{Lang::get('affidavit.nil')}} 
                              @endif,@if($raw->amount)&#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>
                             
                            </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                   

                            </tr>
                        
                        <tr>
                        	<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(vii)</span></td>
                            <td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								Any Other dues
								
                            </td>
                            @if(!empty($data['l_govt_dues_anyother']))
				   @foreach($data['l_govt_dues_anyother'] as $key => $raw)
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                            @if($raw->due_details) {{$raw->due_details}}@else {{Lang::get('affidavit.nil')}} 
                              @endif,@if($raw->amount)&#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif
                            <br></br>
                             
                            </td>
                             @endforeach
				        @else
                       Nil
                       @endif
                   

                         </tr>
                        

 <tr>
                        	<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(viii)</span></td>
                            <td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								Grand total of all Government dues
                            </td>
                            @if(!empty($data['total_govt_dues']))
				   @foreach($data['total_govt_dues'] as $key => $raw)
                   <td style=" border: 1px solid black;border-collapse: collapse;">
                           @if($raw->totalamount)&#8377; {{$raw->totalamount}}@else {{Lang::get('affidavit.nil')}} @endif
                      <br></br>
                             
                       </td>
                       @endforeach
				       @else
                       Nil
                       @endif
                       </tr> 
<tr>
                        	<td  style=" border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;"><span class="block">(iX)</span></td>
                            <td style="border: 1px solid black;border-collapse: collapse;max-width:25px;min-width:22px;">
								Whether any other liabilities are in dispute,if so, mention the amount involved and the authority before which it is pending
                            </td>
                           

                       <td>
                          	@if(!empty($data['liabilites_total_test1']))
                          	@foreach($data['liabilites_total_test1'] as $raw)
                            
                           <br></br>
                            <p> @if($raw->Other_Amt_Dispute) &#8377;{{$raw->Other_Amt_Dispute}} @else {{Lang::get('affidavit.nil')}} @endif </p>

                           
                             
                          	@endforeach
                            @else
                            <br></br>
                        
                            {{Lang::get('affidavit.nil')}}
                           @endif


                          </td>
                          <td>@if(!empty($data['liabilites_total_test2']))
                          	@foreach($data['liabilites_total_test2'] as $raw)

                          	<br></br>
                            <p> @if($raw->Other_Amt_Dispute) &#8377;{{$raw->Other_Amt_Dispute}} @else {{Lang::get('affidavit.nil')}} @endif </p>

                             
                          	@endforeach
                            @else
                         <br></br>
                         
                            {{Lang::get('affidavit.nil')}}
                           @endif
                        </td>
                          <td>@if(!empty($data['liabilites_total_test3']))
                          	@foreach($data['liabilites_total_test3'] as $raw)
                            
                           <br></br>
                            <p> @if($raw->Other_Amt_Dispute) &#8377;{{$raw->Other_Amt_Dispute}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                             
                          	@endforeach
                            @else
                           <br></br>
                          
                            {{Lang::get('affidavit.nil')}}
                           @endif

                        </td>
                          <td>@if(!empty($data['liabilites_total_test4']))
                          	@foreach($data['liabilites_total_test4'] as $raw)
                            
                            <br></br>
                            <p> @if($raw->Other_Amt_Dispute) &#8377;{{$raw->Other_Amt_Dispute}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                             
                          	@endforeach
                             @else
                          <br></br>
                        
                            {{Lang::get('affidavit.nil')}}
                           @endif</td>
                          <td>@if(!empty($data['liabilites_total_test5']))
                          	@foreach($data['liabilites_total_test5'] as $raw)
                            
                           <br></br>
                            <p> @if($raw->Other_Amt_Dispute) &#8377;{{$raw->Other_Amt_Dispute}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                             
                          	@endforeach
                             @else
                         <br></br>
                           
                            {{Lang::get('affidavit.nil')}}
                           @endif</td>
                        <td>@if(!empty($data['liabilites_total_test6']))
                          	@foreach($data['liabilites_total_test6'] as $raw)
                            <br></br>
                            <p> @if($raw->Other_Amt_Dispute) &#8377;{{$raw->Other_Amt_Dispute}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                             
                          	@endforeach
                             @else
                           <br></br>
                         
                            <p>{{Lang::get('affidavit.nil')}}</p>
                           @endif</td>
                     
				      
                       </tr>




</table>


<div style="page-break-after:always;"></div>


 
<table width="100%" class="top-20" border="0" >
		

					
	<tr class="changecss"><td>(9)<b>Details of profession or Occupation:</b></td></tr>
				<tr>
					@if(isset($data['l_9A']))
				   @foreach($data['l_9A'] as $key => $raw)
				   @if($raw->relation_type_code==1)
				   <td class="changecss">&nbsp;&nbsp;&nbsp;<b>(a)</b> Self :&nbsp;
					 @if($raw->occupation) {{$raw->occupation}}@else 'N/A' @endif 
					</td><br></br>
				</tr>

				<tr>
					@endif
                     @if($raw->relation_type_code==2)
                     <td class="changecss">&nbsp;&nbsp;&nbsp;<b>(b)</b> Spouse :&nbsp; 
 @if($raw->occupation) {{$raw->occupation}}@else 'N/A' @endif 
</td>
					@endif
					
					@endforeach
					@endif
					</tr>
				
				
				<tr class="changecss"><td>(9A) Details of source(s) of income:</td></tr>
				<tr>
					<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(a)</b> Self :&nbsp;
					@if(isset($data['l_9B']))
				   @foreach($data['l_9B'] as $key => $raw)
				   @if($raw->relation_type_code==1)
					 @if($raw->source_of_income) {{$raw->source_of_income}}@else 'N/A' @endif </td>
					@endif
					@endforeach
					@endif
				</tr><br></br>
				<tr>
					<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(b)</b> Spouse :&nbsp;
					@if(isset($data['l_9B']))
				   @foreach($data['l_9B'] as $key => $raw)
                     @if($raw->relation_type_code==2)
 @if($raw->source_of_income) {{$raw->source_of_income}}@else 'N/A' @endif </td>
					@endif
					@endforeach
					@endif
					</tr><br></br>




 <tr>
 	<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(c)</b> Source of income, if any, of dependents :&nbsp; 
                   @if(isset($data['l_9Bsource']))
				   @foreach($data['l_9Bsource'] as $key => $raw)
					
@if($raw->source_of_income) {{$raw->source_of_income}}@else 'N/A' @endif </td>
					
					
					@endforeach
					@endif
				</tr>
					
						
						<tr>
					<td class="changecss">(9B) Contracts with appropriate Government and any public company or companies</td>
				</tr>
				<tr>
					
					<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(a)</b> details of contracts entered by the candidate :&nbsp;
@if(isset($data['l_9C']))
				   @foreach($data['l_9C'] as $key => $raw)
				   @if($raw->relation_type_code==1)
					 @if($raw->details) {{$raw->govt_public_company}},{{$raw->details}}@else 'N/A' @endif </td>
                    @endif
                   @endforeach
					@endif
 </tr>
				 <tr>
				 	<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(b)</b> details of contracts entered by the spouse :&nbsp;
					@if(isset($data['l_9C']))
				   @foreach($data['l_9C'] as $key => $raw)
                     @if($raw->relation_type_code==2)
 @if($raw->details){{$raw->govt_public_company}}, {{$raw->details}}@else 'N/A' @endif </td>
					@endif
					@endforeach
					@endif
				</tr> 
					 <tr>
<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(c)</b> details of contracts entered into by dependents :&nbsp;
					@if(isset($data['l_9Cs']))
				   @foreach($data['l_9Cs'] as $key => $raw)
					
 @if($raw->details) {{$raw->govt_public_company}},{{$raw->details}}@else 'N/A' @endif </td>
					
					
					@endforeach
					@endif
				</tr> 
					
					 <tr>
					 	<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(d)</b> details of contracts entered into by Hindu Undivided Family or trust in which the candidate or spouse or dependents have interest :&nbsp; 
				  @if(isset($data['l_9D']))
				   @foreach($data['l_9D'] as $key => $raw)
					@if($raw->details) {{$raw->details}}@else 'N/A' @endif</td>

					@endforeach
					@endif
        </tr> 
         <tr>
         	<td class="changecss">&nbsp;&nbsp;&nbsp;<b>(e)</b> details of contracts, entered into by Partnership Firms in which candidate or spouse or dependents are partners :&nbsp;
					@if(isset($data['l_9_partnership_firm']))
				   @foreach($data['l_9_partnership_firm'] as $key => $raw)
					 @if($raw->details) {{$raw->details}}@else 'N/A' @endif</td>
					@endforeach
					@endif
				</tr>
				<tr>
                  <td class="changecss">&nbsp;&nbsp;&nbsp;<b>(f)</b> details of contracts, entered into by private companies in which candidate or spouse or dependents have share :&nbsp;
					@if(isset($data['l_9_private_company']))
				   @foreach($data['l_9_private_company'] as $key => $raw)
					 @if($raw->details) {{$raw->details}}@else 'N/A' @endif</td>
					@endforeach
					@endif
					
						</tr> 
						<tr>
					<td class="changecss"><b>(10) My educational qualification is as under:</b></td>
				</tr>
				<tr>
					@if(isset($data['education']))
		                   @if(count($data['education']) > 0)
							@foreach($data['education'] as $key => $raw)
							
								<td class="changecss"><u>{{$raw->full_form_course}},{{$raw->school_college}},{{$raw->board_univ}},{{$raw->q_year}}</u></td>
								
							
							@endforeach
							@else
							<td class="changecss">N/A</td>
							@endif
          @endif
      </tr>
          <tr>
<td class="changecss">(Give details of highest School / University education mentioning the full form of the
certificate/ diploma/ degree course, name of the School /College/ University and the year
in which the course was completed.)</td>
</tr>
					
				  	</table>
 





<div style="page-break-after:always;"></div>
					
                     <table width="100%" class="top top-20" border="0">
                    	<caption align="center" align="center" style="width:100%; text-align:center; margin:20px auto 0;  line-height:25px; display: block;">
						<h3 style="text-decoration: underline; font-weight:bold; color:black;font-size:22px;">{{Lang::get('affidavit.part_b')}}</h3>	
					    </caption>					    
					 </table>   
					 <table width="100%" border="0">					 	
						<tr>
							<th width="4" align="left"><b>(11).</b></th>
							<td align="left">{{Lang::get('affidavit.abstract_of_the_details_given_in')}}</td>
						</tr>					    
					 </table>

                 
<table width="100%" class="top top-20" border="1">				    
					    <tbody>
					    	<tr>
					    		<td>1.</td>
					    		<td colspan="4">{{Lang::get('affidavit.name_of_the_candidate')}} </td>
					    		<td colspan="4">{{Lang::get('affidavit.sh_smt_kum')}} {{@$data['cand_details']->cand_name}}</td>
					    	</tr>
					    	<tr>
					    		<td>2.</td>
					    		<td colspan="4">{{Lang::get('affidavit.full_postal_address')}} </td>
					    		<td colspan="4">{{@$data['cand_details']->postal_address}}</td>
					    	</tr>
					    	<tr>
					    		<td>3.</td>
					    		<td colspan="4">{{Lang::get('affidavit.number_and_name_of_the_constituency_and_state')}}</td>
					    		<td colspan="4">@if(@$data['cand_details']->ac_no && @$data['cand_details']->st_code)
								{{@$data['cand_details']->ac_no}}-{{getacbyacno(@$data['cand_details']->st_code,@$data['cand_details']->ac_no)->AC_NAME}},@endif
							@if(@$data['cand_details']->st_code){{getstatebystatecode(@$data['cand_details']->st_code)->ST_NAME}}@endif</td>
					    	</tr>
					    	<tr>
					    		<td>4.</td>
					    		<td colspan="4">{{Lang::get('affidavit.name_of_the_political_party_which_set_up_the_candidate')}}</td>
					    		<td colspan="4">@if(@$data['cand_details']->partyabbre){{getpartybyid(@$data['cand_details']->partyabbre)->PARTYNAME}} @else Independent @endif</td>
					    	</tr>
					    	<tr>
					    		<td>5.</td>
					    		<td colspan="4">{{Lang::get('affidavit.total_number_of_pending_criminal_cases')}}</td>
					    		<td colspan="4">{{count($data['pending_cases_count'])}}</td>
					    	</tr>
					    	<tr>
					    		<td>6.</td>
					    		<td colspan="4">{{Lang::get('affidavit.total_number_of_cases_in_which_convicted')}} </td>
					    		<td colspan="4">{{count($data['imprisonment_criminal_count'])}}</td>
					    	</tr>

					    	<tr>
					    		<td rowspan="{{count($data['pan_details'])+1}}" >7.</td>
					    		<td colspan="2"></td>
					    		<td colspan="2">{{Lang::get('affidavit.pan_of')}}</td>
					    		<td colspan="3">{{Lang::get('affidavit.year_for_which_last_income_tax_return_filed')}}</td>
					    		<td>{{Lang::get('affidavit.total_income_shown')}}</td>
					    	</tr>
							
							
							@foreach($data['pan_details'] as $key => $raw)
							
							<tr>
								<td colspan="2">{{$key+1}}. @if($raw->relation_type == 'self' || $raw->relation_type == 'Self') {{Lang::get('affidavit.candidate')}} @else {{$raw->relation_type}} @endif</td>
								<td colspan="2">{{$raw->name}}</td>
								<td colspan="3"> @if($raw->financial_year) {{$raw->financial_year}} @else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->financialyr1) &#8377;{{$raw->financialyr1}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							
							@endforeach
							
				
							</tr>
							<tr><td rowspan="6">8</td><th colspan="8" >{{Lang::get('affidavit.details_of_assets_and_liabilities')}}</th></tr>	

								<tr class="thHeading">
                    		<th style="max-width:15%;min-width:3%;">Sl. NO</th>
                    		<th style="max-width:45%;min-width:25%;"> Description</th>
                        @foreach($data['non_relation_details'] as $key => $raw)	
                    		<th style="max-width:90%;min-width:27%;">{{$raw->relation_type}}</th>
                    		
                    		@endforeach
                    	</tr>			

                        <tr>
                          <td colspan="1">A</td>
                          <td colspan="1">{{Lang::get('affidavit.movable_assets_total_value')}}</td>
                          <td>
                          	@if(!empty($data['movable_assets_total_test1']))
                          	@foreach($data['movable_assets_total_test1'] as $raw)
                            
                             @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                           @else
                           {{Lang::get('affidavit.nil')}}
                           @endif


                          </td>
                          <td>@if(!empty($data['movable_assets_total_test2']))
                          	@foreach($data['movable_assets_total_test2'] as $raw)
                            
                             @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif
                        </td>
                          <td>@if(!empty($data['movable_assets_total_test3']))
                          	@foreach($data['movable_assets_total_test3'] as $raw)
                            
                             @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif

                        </td>
                          <td>@if(!empty($data['movable_assets_total_test4']))
                          	@foreach($data['movable_assets_total_test4'] as $raw)
                            
                             @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                          <td>@if(!empty($data['movable_assets_total_test5']))
                          	@foreach($data['movable_assets_total_test5'] as $raw)
                            
                             @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                        <td>@if(!empty($data['movable_assets_total_test6']))
                          	@foreach($data['movable_assets_total_test6'] as $raw)
                            
                             @if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>

                        	
                    		
</tr>




      <tr>
                          <td colspan="1">B(I)</td>
                          <td colspan="1">
                          <b>Immovable Assets</b><br></br>
                          {{Lang::get('affidavit.purchase_price_of_self_acquired_immovable_property')}}</td>
                          <td>
                          	@if(!empty($data['immoveable_assets_total_test1']))
                          	@foreach($data['immoveable_assets_total_test1'] as $raw)
                            
                             @if($raw->purcahse_price_self_acquired_immov) &#8377;{{$raw->purcahse_price_self_acquired_immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                           @else
                           {{Lang::get('affidavit.nil')}}
                           @endif


                          </td>
                          <td>@if(!empty($data['immoveable_assets_total_test2']))
                          	@foreach($data['immoveable_assets_total_test2'] as $raw)
                            
                             @if($raw->purcahse_price_self_acquired_immov) &#8377;{{$raw->purcahse_price_self_acquired_immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif
                        </td>
                          <td>@if(!empty($data['immoveable_assets_total_test3']))
                          	@foreach($data['immoveable_assets_total_test3'] as $raw)
                            
                             @if($raw->purcahse_price_self_acquired_immov) &#8377;{{$raw->purcahse_price_self_acquired_immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif

                        </td>
                          <td>@if(!empty($data['immoveable_assets_total_test4']))
                          	@foreach($data['immoveable_assets_total_test4'] as $raw)
                            
                             @if($raw->purcahse_price_self_acquired_immov) &#8377;{{$raw->purcahse_price_self_acquired_immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                          <td>@if(!empty($data['immoveable_assets_total_test5']))
                          	@foreach($data['immoveable_assets_total_test5'] as $raw)
                            
                             @if($raw->purcahse_price_self_acquired_immov) &#8377;{{$raw->purcahse_price_self_acquired_immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                        <td>@if(!empty($data['immoveable_assets_total_test6']))
                          	@foreach($data['immoveable_assets_total_test6'] as $raw)
                            
                             @if($raw->purcahse_price_self_acquired_immov) &#8377;{{$raw->purcahse_price_self_acquired_immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>

                        </tr>

	<tr>
                          <td colspan="1">B(II)</td>
                          <td colspan="1">{{Lang::get('affidavit.development_construction_cost_of_immovable_property_after_purchase')}}</td>
                          <td>
                          	@if(!empty($data['immoveable_assets_total_test1']))
                          	@foreach($data['immoveable_assets_total_test1'] as $raw)
                            
                             @if($raw->Investment_Immov) &#8377;{{$raw->Investment_Immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif


                          </td>
                          <td>@if(!empty($data['immoveable_assets_total_test2']))
                          	@foreach($data['immoveable_assets_total_test2'] as $raw)
                            
                             @if($raw->Investment_Immov) &#8377;{{$raw->Investment_Immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif
                        </td>
                          <td>@if(!empty($data['immoveable_assets_total_test3']))
                          	@foreach($data['immoveable_assets_total_test3'] as $raw)
                            
                             @if($raw->Investment_Immov) &#8377;{{$raw->Investment_Immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif

                        </td>
                          <td>@if(!empty($data['immoveable_assets_total_test4']))
                          	@foreach($data['immoveable_assets_total_test4'] as $raw)
                            
                             @if($raw->Investment_Immov) &#8377;{{$raw->Investment_Immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                          <td>@if(!empty($data['immoveable_assets_total_test5']))
                          	@foreach($data['immoveable_assets_total_test5'] as $raw)
                            
                             @if($raw->Investment_Immov) &#8377;{{$raw->Investment_Immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                        <td>@if(!empty($data['immoveable_assets_total_test6']))
                          	@foreach($data['immoveable_assets_total_test6'] as $raw)
                            
                             @if($raw->Investment_Immov) &#8377;{{$raw->Investment_Immov}} @else {{Lang::get('affidavit.nil')}} @endif
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>

</tr>

                          








   	<tr>
                          <td colspan="1">B(III)</td>
                          <td colspan="1">{{Lang::get('affidavit.approximate_current_market_price')}}
                             <p>(a){{Lang::get('affidavit.self_acquired_assets_total_value')}} </p>
                             <p>(b){{Lang::get('affidavit.inherited_assets_total_value')}} </p>
                          </td>

                          <td>
                          	@if(!empty($data['immoveable_assets_total_test1']))
                          	@foreach($data['immoveable_assets_total_test1'] as $raw)
                            <br></br>
                            <p> @if($raw->self_acquired_Assets_Value) Self :&#8377;{{$raw->self_acquired_Assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>

                             <p> @if($raw->Inherited_assets_Value) Inherited :&#8377;{{$raw->Inherited_assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>

                          
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif


                          </td>
                          <td>@if(!empty($data['immoveable_assets_total_test2']))
                          	@foreach($data['immoveable_assets_total_test2'] as $raw)
                            
                           <br></br>
                            <p> @if($raw->self_acquired_Assets_Value) Self :&#8377;{{$raw->self_acquired_Assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>

                             <p> @if($raw->Inherited_assets_Value) Inherited :&#8377;{{$raw->Inherited_assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif
                        </td>
                          <td>@if(!empty($data['immoveable_assets_total_test3']))
                          	@foreach($data['immoveable_assets_total_test3'] as $raw)
                            
                            <br></br>
                            <p> @if($raw->self_acquired_Assets_Value) Self :&#8377;{{$raw->self_acquired_Assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>

                             <p> @if($raw->Inherited_assets_Value) Inherited :&#8377;{{$raw->Inherited_assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>
                             
                          	@endforeach
                            @else
                           {{Lang::get('affidavit.nil')}}
                           @endif

                        </td>
                          <td>@if(!empty($data['immoveable_assets_total_test4']))
                          	@foreach($data['immoveable_assets_total_test4'] as $raw)
                            
                           <br></br>
                           <p> @if($raw->self_acquired_Assets_Value) Self :&#8377;{{$raw->self_acquired_Assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>

                             <p> @if($raw->Inherited_assets_Value) Inherited :&#8377;{{$raw->Inherited_assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                          <td>@if(!empty($data['immoveable_assets_total_test5']))
                          	@foreach($data['immoveable_assets_total_test5'] as $raw)
                            
                          <br></br>
                            <p> @if($raw->self_acquired_Assets_Value) Self :&#8377;{{$raw->self_acquired_Assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>

                             <p> @if($raw->Inherited_assets_Value) Inherited :&#8377;{{$raw->Inherited_assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>
                        <td>@if(!empty($data['immoveable_assets_total_test6']))
                          	@foreach($data['immoveable_assets_total_test6'] as $raw)
                            
                           <br></br>
                            <p> @if($raw->self_acquired_Assets_Value) Self :&#8377;{{$raw->self_acquired_Assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>

                             <p> @if($raw->Inherited_assets_Value) Inherited :&#8377;{{$raw->Inherited_assets_Value}} @else {{Lang::get('affidavit.nil')}} @endif
                             </p>
                             
                          	@endforeach
                             @else
                           {{Lang::get('affidavit.nil')}}
                           @endif</td>

                        </tr>

 


 	<tr>


 	

                          <td>9</td>
                          <td>(i)</td>
                          <td colspan="1">
                          		<p><b>Liabilities</b></p>
                          
                          	<p>(i){{Lang::get('affidavit.government_dues_total')}}</p>
                            <p>(ii){{Lang::get('affidavit.loan_from_bank_financial_other')}}</p>
                          	</td>
                          <td>
                          	@if(!empty($data['liabilites_total_test1']))
                          	@foreach($data['liabilites_total_test1'] as $raw)
                            
                           <br></br>
                            <p> @if($raw->Govt_dues) &#8377;{{$raw->Govt_dues}} @else {{Lang::get('affidavit.nil')}} @endif </p>

                            <br></br>

                            <p> @if($raw->Total_Loan + $raw->Other_Amt) &#8377; {{$raw->Total_Loan + $raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                            @else
                            <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif


                          </td>
                          <td>@if(!empty($data['liabilites_total_test2']))
                          	@foreach($data['liabilites_total_test2'] as $raw)
                            
                          <br></br>
                            <p> @if($raw->Govt_dues) &#8377;{{$raw->Govt_dues}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                           <br></br>
                            <p> @if($raw->Total_Loan + $raw->Other_Amt) &#8377; {{$raw->Total_Loan + $raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                            @else
                         <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif
                        </td>
                          <td>@if(!empty($data['liabilites_total_test3']))
                          	@foreach($data['liabilites_total_test3'] as $raw)
                            
                           <br></br>
                            <p> @if($raw->Govt_dues) &#8377;{{$raw->Govt_dues}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                           <br></br>
                            <p> @if($raw->Total_Loan + $raw->Other_Amt) &#8377; {{$raw->Total_Loan + $raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                            @else
                           <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif

                        </td>
                          <td>@if(!empty($data['liabilites_total_test4']))
                          	@foreach($data['liabilites_total_test4'] as $raw)
                            
                            <br></br>
                            <p> @if($raw->Govt_dues) &#8377;{{$raw->Govt_dues}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                            <br></br>

                            <p> @if($raw->Total_Loan + $raw->Other_Amt) &#8377; {{$raw->Total_Loan + $raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                             @else
                          <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif</td>
                          <td>@if(!empty($data['liabilites_total_test5']))
                          	@foreach($data['liabilites_total_test5'] as $raw)
                            
                            <br></br>
                            <p> @if($raw->Govt_dues) &#8377;{{$raw->Govt_dues}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                            <br></br>

                            <p> @if($raw->Total_Loan + $raw->Other_Amt) &#8377; {{$raw->Total_Loan + $raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                             @else
                         <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif</td>
                        <td>@if(!empty($data['liabilites_total_test6']))
                          	@foreach($data['liabilites_total_test6'] as $raw)
                            <br></br>
                            <p> @if($raw->Govt_dues) &#8377;{{$raw->Govt_dues}} @else {{Lang::get('affidavit.nil')}} @endif </p>
                            <br></br>

                            <p> @if($raw->Total_Loan + $raw->Other_Amt) &#8377; {{$raw->Total_Loan + $raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                             @else
                           <br></br>
                           <p>{{Lang::get('affidavit.nil')}}</p> <br></br>
                            <p>{{Lang::get('affidavit.nil')}}</p>
                           @endif</td>

                 </tr>     




                  <tr>
                           <td>10</td>
                          <td>(i)</td>
                          <td colspan="1">
                        <p><b> {{Lang::get('affidavit.liabilities_that_are_under_dispute')}}</b></p>
                          
                          	<p>(i){{Lang::get('affidavit.government_dues_total')}}</p>
                            <p>(ii){{Lang::get('affidavit.loan_from_bank_financial_other')}}</p>
                          	</td>
                          <td>


                          	@if(!empty($data['liabilites_total_test1']))
                          	@foreach($data['liabilites_total_test1'] as $raw)
                            
                           <br></br>
                             <br></br>
                          <p>  {{Lang::get('affidavit.nil')}} </p>

                            <br></br>

                            <p> @if($raw->Other_Amt_Dispute) &#8377; {{$raw->Other_Amt_Dispute}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                            @else
                            <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif


                          </td>
                          <td>@if(!empty($data['liabilites_total_test2']))
                          	@foreach($data['liabilites_total_test2'] as $raw)
                            
                          <br></br>
                             <br></br>
                           <p>  {{Lang::get('affidavit.nil')}} </p>
                           <br></br>
                           <p> @if($raw->Other_Amt_Dispute) &#8377; {{$raw->Other_Amt_Dispute}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                            @else
                          <br></br>
                             <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif
                        </td>
                          <td>@if(!empty($data['liabilites_total_test3']))
                          	@foreach($data['liabilites_total_test3'] as $raw)
                            
                           <br></br>
                             <br></br>
                            <p>  {{Lang::get('affidavit.nil')}} </p>
                           <br></br>
                            <p> @if($raw->Other_Amt_Dispute) &#8377; {{$raw->Other_Amt_Dispute}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                            @else
                            <br></br>
                             <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif

                        </td>
                          <td>@if(!empty($data['liabilites_total_test4']))
                          	@foreach($data['liabilites_total_test4'] as $raw)
                            
                            <br></br>
                             <br></br>
                           <p>  {{Lang::get('affidavit.nil')}} </p>
                            <br></br>

                          <p> @if($raw->Other_Amt_Dispute) &#8377; {{$raw->Other_Amt_Dispute}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                             @else
                           <br></br>
                             <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif</td>
                          <td>@if(!empty($data['liabilites_total_test5']))
                          	@foreach($data['liabilites_total_test5'] as $raw)
                            
                          <br></br>
                             <br></br>
                           <p>  {{Lang::get('affidavit.nil')}} </p>
                            <br></br>

                          <p> @if($raw->Other_Amt_Dispute) &#8377; {{$raw->Other_Amt_Dispute}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                             @else
                          <br></br>
                             <br></br>
                           {{Lang::get('affidavit.nil')}} <br></br>
                            {{Lang::get('affidavit.nil')}}
                           @endif</td>
                        <td>@if(!empty($data['liabilites_total_test6']))
                          	@foreach($data['liabilites_total_test6'] as $raw)
                            <br></br>
                             <br></br>
                            <p>  {{Lang::get('affidavit.nil')}} </p>
                            <br></br>

                           <p> @if($raw->Other_Amt_Dispute) &#8377; {{$raw->Other_Amt_Dispute}}@else {{Lang::get('affidavit.nil')}} @endif</p>
                             
                          	@endforeach
                             @else
                            <br></br>
                             <br></br>
                           <p>{{Lang::get('affidavit.nil')}}</p> <br></br>
                            <p>{{Lang::get('affidavit.nil')}}</p>
                           @endif
                       </td>

                 </tr>      



<tr>
						<td class="padd-0 bdrLeass" colspan="9">
							<table width="100%" class="" border="1">
							<tbody>
							<tr>
								<th colspan="2">11</th>
								<th>{{Lang::get('affidavit.qualification')}}</th>
								<th>{{Lang::get('affidavit.full_form_certificate')}}</th>
								<th>{{Lang::get('affidavit.school_college')}}</th>
								<th>{{Lang::get('affidavit.board_university')}}</th>
								<th>{{Lang::get('affidavit.year_of_completion')}}</th>								
							</tr>
							@if(count($data['education']) > 0)
							@foreach($data['education'] as $key => $raw)
							<tr>
								<td colspan="2"></td>
								<td><span class="block">{{ucfirst($raw->qualification)}}</span></td>
								<td>{{$raw->full_form_course}}</td>
								<td>{{$raw->school_college}}</td>
								<td>{{$raw->board_univ}}</td>
								<td colspan="2">{{$raw->q_year}}</td>
							</tr>
							@break;
							@endforeach
							@else
							<tr>
									<td colspan="2"></td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
							@endif
						</tbody>
					</table>
				</td>
							</tr>






					    </tbody>
                    </table>
					
					<div style="page-break-after:always;"></div>
					
					

                    <table width="100%" class="top top-20" border="0">
                    	
						<tr>
                    		<th  colspan="3" align="center" style="text-align:center">{{Lang::get('affidavit.verification')}}</th>
						</tr>
                    	<tbody>
                    		<tr>
                    			<td colspan="3"><span class="pad-20">&nbsp;&nbsp;&nbsp;{{Lang::get('affidavit.i_the_deponent_above_named')}}</span></td>
                    		</tr>
                    		<tr>
                    			<td colspan="3">{{Lang::get('affidavit.there_is_no_case_of_conviction_or_pending_case_against_me')}}</td>
                    		</tr>
                    		<tr>
                    			<td colspan="3">{{Lang::get('affidavit.i_my_spouse_or_my_dependents_do_not_have_any_asset')}}</td>
                    		</tr>
							<?php if(session()->get('locale') == 'hi') { ?>
								<tr>
									<td colspan="2"> आज तारीख.......................... को सत्‍यापित किया गया। </td>                   			
								</tr>
							<?php } else { ?>
                    		<tr>
                    			<td colspan="2"> Verified at <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>                    				
								<td align="right"> this the <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>                    			
                    		</tr>
                    		<tr>
                    			<td colspan="3">day of <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
                    		</tr>
							<?php } ?>
							
							
                    		<tr> 
                    			<td colspan="3" align="right"><span class="bold"> {{Lang::get('affidavit.deponent')}}</span></b>
                    			
								<hr style="height:1px;border-width:0;color:#101010;background-color:#101010; margin-top: 10px;">
								</td>
                    		</tr>
                    	</tbody>
                    </table>
					

                   <table width="100%" class="top-20 top" border="0">
                   	<tbody>
                   	<tr>
                   		<th width="90">{{Lang::get('affidavit.note')}}: 1.</th>
                   		<td align="left">{{Lang::get('affidavit.affidavit_should_be_filed_latest_by_3_pm_on_the_last_day_of_filing_nominations')}}
						</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 2.</th>
                   		<td>{{Lang::get('affidavit.affidavit_should_be_sworn_before_an_oath_commissioner_or_magistrate_of_the_first_class_or_before_a_notary_public')}}</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 3.</th>
                   		<td>{{Lang::get('affidavit.all_columns_should_be_filled_up_and_no_column_to_be_left_blank')}}</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 4.</th>
                   		<td>{{Lang::get('affidavit.the_affidavit_should_be_either_typed_or_written_legibly_and_neatly')}}
						</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 5.</th>
                   		<td>{{Lang::get('affidavit.each_page_of_the_affidavit_should_be_signed_by_the_deponent')}}</td>
                   	</tr>
                   	</tbody>	
                   </table>
                
					



 



