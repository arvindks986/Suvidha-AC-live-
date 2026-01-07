<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Candidate Nomination</title>
  <style type="text/css">
    
      @page {
        header: page-header;
        footer: page-footer;
      }
	.table-strip{border-collapse: collapse; }
	.wrap{padding: 0 15px;}
	.table-wrap{width: 92%; margin: auto;}
	div{padding-left: 10px; padding-right: 10px;}
    ul{list-style-type: none;}  
	.customTable{width: 100%; margin: auto; font-size:14px; line-height: 21px;}
	.customTable td,.customTable th{padding: 5px;}
	.customTable th{background-color:#fafafa;}
	.td-center{text-align: center;}
	.customTable h4, .customTable h5, .customTable h6{margin-bottom: 0;}
	.customTable td span{border-bottom: 1px dashed #313131; width: 215px; display: inline-block;}
	.td-right{text-align: right;}
	.passport-img{width: 115px; height: 135px; position: relative; display: block; background-color: #fafafa; border: 1px solid #d7d7d7; float: right; font-size: 11px; text-align: justify; padding: 0.5rem;}
	.passport-img img{position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: auto; display: block; max-width: 100%; max-height: 100%;}
	.td-bold{font-weight: 600;}
	.td-bold p{font-weight: 100; font-size: 12px;}
	.param-area p{margin: 0.25rem 0; line-height: 32px; padding-left: 8px; padding-right: 8px;}
	.bordr-one{border-top: 2px solid #313131;}
	.pt-one{padding-top: 1rem;}
	.pb-three{padding-bottom: 1rem;}
	.sm-note {font-size: 11px; border-top: 1px solid #313131; padding: 0.25rem 0 1.5rem 0; line-height: 21px;}
	.sub-area {padding-right: 0.5rem;}
	.sub-area p{padding-left: 1.5rem; padding-top: 0.5rem;}
	ul.list-area{margin-bottom: 0;}
	ul.list-area li{padding-left: 1rem; padding-top: 0.25rem;}
	.big-param p{border-bottom: 1px dashed #313131; margin-top: 1rem; }
	.cont-center{border-bottom: 1px dotted #313131; text-align: center; margin-bottom: 2.5rem;}
	.cont-center span{border-bottom: none !important;}
	
	@page { margin: 180px 50px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; text-align: center; border:1px solid gray; }
    #footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 150px; }
    #footer .page:after { content: counter(page, upper-roman); }
	
	 body, p, td, div { font-family: freesans; }
	</style>
</head>
<body> 
<htmlpageheader name="page-header">

 <table  align="center" id="header">  
    <tr>
      <td  style="width:50%;">
        <table  style="width:100%">
          <tbody>
            <tr> 
               <td align="left"> <?php if($qrcode!='NA') { ?> <img src="{!! $qrcode !!}" style="max-width: 80px;float:right;"><?php } ?></td> 
               <td align="left">{{ __('election_details.ref') }}: <strong>{{$nomination_no}}</strong> </td>
			    <td align="left"><strong>{{ __('download.print') }}:</strong> {{ date('d-M-Y h:i a') }}</td> 
            </tr>
        </table>
      </td>
    </tr>
  </table>

  
  	</htmlpageheader>

	<htmlpagebody> 
<div id="content" >   
  <div style="width: 100%; border: 1px solid #000;">
	<div class="td-center">
			
			  @if($finalize!='yes')
				<h5 style="color: #ee577e; font-size: 18px;">{{ __('finalize.Preview') }}</h5>
			@endif
		  
	
			<h5>{{ __('step3.form2b') }}</h5>
			<div>({{ __('step3.rule4') }})</div>
			<div>{{ __('step3.nomp') }}</div>
	</div>	
	 <div style="width: 100%;">  
			  <?php if($qrcode!='NA') { ?>
              <div  style="width: 50%;float:left;">
                <!--<img src="{!! $qrcode !!}" style="max-width: 80px;">-->
              </div>
			  <?php } ?>
			 <?php if($profileimg!='NA') { ?>
              <div  style="float:right;"><img src="{!! $profileimg !!}" style="max-width: 80px;float:right;"></div>
			 <?php } ?>
   </div> 
	
	
	
	<div class="td-center">{{ __('step3.nommessage') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b> {{$st_name}}</b></span></div>  
	<div class="td-center">{{ __('finalize.STRIKE_OFF') }}</div>  
	@if($recognized_party == 'recognized' or $recognized_party ==1 or $recognized_party ==0 or $recognized_party ==3)

		
	  <div class="td-center" style="border-top: 1px solid #000; font-weight: 600; padding-top:0.5rem;"><b>{{ __('finalize.PART1') }}</b></div>
	  <div class="td-center">({{ __('finalize.recognized_party') }})</div>
	  <div>{{ __('finalize.nominate_ac') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b>&nbsp;   <?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?></b></span>  {{ __('finalize.Assembly_Constituency') }}. </div>
		<div class="param-area">
			<p>{{ __('finalize.Candidate_name') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"> <b>&nbsp;{{$name}}</b></span> {{ __('finalize.Father_husband_mother') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{!! $father_name !!}</b></span> {{ __('finalize.His_postal_address') }} <span  style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;  <b>{!! $address !!}</b> </span> {{ __('finalize.His_name_is_entered_at_Sl') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$serial_no}}</b></span> {{ __('finalize.in_Part_No') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{$part_no}}</b></span> {{ __('finalize.of_the_electoral_roll_for') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?>&nbsp;</b></span> {{ __('finalize.Assembly_Constituency') }}. 
			</p>
			<p> {{ __('finalize.My_name_is') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{$proposer_name}}</b> </span> {{ __('finalize.and_it_is_entered_at_Sl') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$proposer_serial_no}}</b> </span>{{ __('finalize.in_Part_No') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$proposer_part_no}}</b> </span> {{ __('finalize.of_the_electoral_roll_for') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$proposer_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $proposer_assembly); ?>	</b> </span> {{ __('finalize.Assembly_Constituency') }}. </p>
		</div>
	    <div class="table-wrap">
			<table style="width: 100%; margin: 1.5rem 0;">
			 <tbody>
			   <tr>
				<td>{{ __('finalize.Date') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$apply_date}}</b></span></td>
				<td class="td-right">
					<div>{{ __('finalize.Signature_of_the_Proposer') }} </div>
				</td>
			   </tr> 
			 </tbody>
			</table>	   
	    </div>
		
		
		
		
		<!-- For Strike -->
		@if($recognized_party != '3')	
			
		<div class="td-center td-bold" style="border-top: 2px solid #000; padding-top:0.85rem;"><strong> {{ __('finalize.PART2') }} </strong></div>
		  <div class="param-area">
		  <p> <strike>  {{ __('finalize.nominate_ac') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b>&nbsp; ...........</b></span> {{ __('finalize.Assembly_Constituency') }}. 
		  </strike> </p><br>  
		  <p> <strike>{{ __('finalize.Candidate_name') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>...........</b></span> {{ __('finalize.Father_husband_mother') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>...........</b></span> {{ __('finalize.His_postal_address') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;  <b>...........</b> </span>

			{{ __('finalize.His_name_is_entered_at_Sl') }} <span  style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>...........</b></span> {{ __('finalize.in_Part_No') }} <span  style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>...........</b></span> {{ __('finalize.of_the_electoral_roll_for') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>...........</b></span> {{ __('finalize.Assembly_Constituency') }}. <strike>
		</p>	
		</div>  </strike> 
		<div>
		  <p>
		<strike>	{{ __('finalize.We_declare_that_we_are_electors') }}:-  </strike> 
		  </p>
		</div>
	   <div class="td-center"><h6 class="pt-one">  <strike> {{ __('finalize.Particulars_of_the_proposers') }}  </strike> </h6></div>
	  
		<div class="table-wrap">
		  <table style="width:100%; text-align: center; border-collapse: collapse;" border="0">
			<thead>
			  <tr>
				<th style="width: 55px; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;"> <strike> {{ __('finalize.serial_no') }}  </strike> </th>
				<th style="padding: 0; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;">
				  <table style="width:100%;  border-collapse: collapse;" border="0">
						<tr>
							 <th colspan="2" class="td-center" style="border-bottom: 1px solid #000;"> <strike> {{ __('finalize.Elector_Roll_No') }}</strike> </th> 
						</tr>
						<tr>
							<th  style=" width: 50%; border-right: 1px solid #000;"> <strike>{{ __('finalize.Part_No_of_Electoral') }} </strike> </th>
							<th> <strike> {{ __('finalize.SNo_in_that_part') }}  </strike> </th>
						</tr>
				  </table>  
				</th> <strike> 
				<th style="border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;"><strike>{{ __('finalize.Full_Name') }}</strike> </th>
				<th style="border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;"><strike>{{ __('finalize.Signature') }}</strike> </th>
				<th style="border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;"><strike> {{ __('finalize.Date') }}</strike> </th>
			  </tr>
			</thead>  
			<?php $i=1;  for($k=0; $k<10; $k++){ ?>
				<tr>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">{{$k+1}}</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 0;">
					 <table style="width:100%; border-collapse: collapse;" border="0">
					   <tr>
						<td style="width: 50%; border-right: 1px solid #000;">&nbsp;</td>
						<td>&nbsp;</td>
					   </tr>	
					 </table>
					</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">&nbsp;</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">&nbsp;</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">&nbsp;</td>
				</tr>

			<?php }  ?>
		  </table>
		</div>
	    <div class="pb-three"><strong>N.B.-</strong> <strike> {{ __('finalize.There_should_be') }} </strike> .</div>
		@endif	
	<!-- EndForStrike -->
		
		
		@endif
	    @if($recognized_party == 2  or $recognized_party ==3)		
		@if($recognized_party == 2  or $recognized_party ==3)	
		<!-- Strike Start Here -->
		
	@if($recognized_party != '3')		
	  <div class="td-center" style="border-top: 1px solid #000; font-weight: 600; padding-top:0.5rem;"><b>{{ __('finalize.PART1') }}</b></div>
	  <div class="td-center"><strike>({{ __('finalize.recognized_party') }})</strike></div>
	  <div><strike>{{ __('finalize.nominate_ac') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b>&nbsp;........</b></span>  {{ __('finalize.Assembly_Constituency') }}. </strike></div>
		<div class="param-area">
			<p><strike>{{ __('finalize.Candidate_name') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"> <b>&nbsp;........</b></span> {{ __('finalize.Father_husband_mother') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>........</b></span> {{ __('finalize.His_postal_address') }} <span  style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;  <b>........</b> </span> {{ __('finalize.His_name_is_entered_at_Sl') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>........</b></span> {{ __('finalize.in_Part_No') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>........</b></span> {{ __('finalize.of_the_electoral_roll_for') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>........&nbsp;</b></span> {{ __('finalize.Assembly_Constituency') }}.</strike> 
			</p>
			<p><strike> {{ __('finalize.My_name_is') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>........</b> </span> {{ __('finalize.and_it_is_entered_at_Sl') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>........</b> </span>{{ __('finalize.in_Part_No') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b> ........	</b> </span> {{ __('finalize.of_the_electoral_roll_for') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b> ........	</b> </span> {{ __('finalize.Assembly_Constituency') }}. </strike></p>
		</div>
	    <div class="table-wrap">
			<table style="width: 100%; margin: 1.5rem 0;">
			 <tbody>
			   <tr>
				<td><strike>{{ __('finalize.Date') }}</strike><span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>........</b></span></td>
				<td class="td-right">
					<div><strike>{{ __('finalize.Signature_of_the_Proposer') }}</strike></div>
				</td>
			   </tr> 
			 </tbody>
			</table>	   
	    </div> 
		<!-- End Strike Start Here -->
	 @endif	











			
		
		<div class="td-center td-bold" style="border-top: 2px solid #000; padding-top:0.85rem;"><strong> {{ __('finalize.PART2') }} </strong></div>
		<div class="param-area">
		  <p>
			{{ __('finalize.nominate_ac') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b>&nbsp; {{$legislative_assembly}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?></b></span> {{ __('finalize.Assembly_Constituency') }}. 
		  </p><br>  
		  <p>{{ __('finalize.Candidate_name') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$name}}</b></span> {{ __('finalize.Father_husband_mother') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{!! $father_name !!}</b></span> {{ __('finalize.His_postal_address') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;  <b>{!! $address !!}</b> </span>

			{{ __('finalize.His_name_is_entered_at_Sl') }} <span  style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$serial_no}}</b></span> {{ __('finalize.in_Part_No') }} <span  style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$part_no}}</b></span> {{ __('finalize.of_the_electoral_roll_for') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$resident_ac_no}}-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?></b></span> {{ __('finalize.Assembly_Constituency') }}. 
		</p>	
		</div>
		<div>
		  <p>
			{{ __('finalize.We_declare_that_we_are_electors') }}:- 
		  </p>
		</div>
	   <div class="td-center"><h6 class="pt-one">{{ __('finalize.Particulars_of_the_proposers') }} </h6></div>
	  
		<div class="table-wrap">
		  <table style="width:100%; text-align: center; border-collapse: collapse;" border="0">
			<thead>
			  <tr>
				<th style="width: 55px; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;">{{ __('finalize.serial_no') }}</th>
				<th style="padding: 0; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;">
				  <table style="width:100%;  border-collapse: collapse;" border="0">
						<tr>
							<th colspan="2" class="td-center" style="border-bottom: 1px solid #000;">{{ __('finalize.Elector_Roll_No') }}</th>
						</tr>
						<tr>
							<th  style=" width: 50%; border-right: 1px solid #000;">{{ __('finalize.Part_No_of_Electoral') }}</th>
							<th>{{ __('finalize.SNo_in_that_part') }}</th>
						</tr>
				  </table>  
				</th>
				<th style="border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;">{{ __('finalize.Full_Name') }}</th>
				<th style="border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;">{{ __('finalize.Signature') }}</th>
				<th style="border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">{{ __('finalize.Date') }} </th>
			  </tr>
			</thead>  
			<?php $i=1; if(count($non_recognized_proposers)!=0){  
			 foreach($non_recognized_proposers as $iterate_proposer){ ?> 

			  <tr>
				<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">{{$i}}.</td>
				<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 0;">
				 <table style="width:100%; border-collapse: collapse;" border="0">
				   <tr>
					<td style="width: 50%; border-right: 1px solid #000;height:30px;">@if($iterate_proposer['part_no']!=0){{$iterate_proposer['part_no']}}@endif</td>
					<td>@if($iterate_proposer['serial_no']!=0){{$iterate_proposer['serial_no']}}@endif</td>
				   </tr>	
				 </table>
				</td>
				<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">{{$iterate_proposer['fullname']}}</td>
				<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">&nbsp; {{$iterate_proposer['signature']}}</td>
				<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">@if($iterate_proposer['part_no']!=0 or 
									$iterate_proposer['serial_no']!=0 or
									$iterate_proposer['fullname']!=0 )
									@if(!empty($iterate_proposer['date'])){{date('d/m/Y',strtotime($iterate_proposer['date']))}}@endif
									
								@endif	
				</td>
			 </tr>
			<?php $i++; } } else { for($k=0; $k<10; $k++){ ?>
				<tr>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">{{$k+1}}</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 0;">
					 <table style="width:100%; border-collapse: collapse;" border="0">
					   <tr>
						<td style="width: 50%; border-right: 1px solid #000;">&nbsp;</td>
						<td>&nbsp;</td>
					   </tr>	
					 </table>
					</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">&nbsp;</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">&nbsp;</td>
					<td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">&nbsp;</td>
				</tr>

			<?php } } ?>
		  </table>
		</div>
	    <div class="pb-three"><strong>N.B.-</strong> {{ __('finalize.There_should_be') }} .</div>
	    @endif	
		@endif	
				
		<div class="td-center" style="border-top: 2px solid #000; padding-top: 0.85rem;"><strong>{{ __('finalize.PART3') }} </strong></div>
		<div class="param-area">
			<p>{{ __('finalize.I_the_candidate_mentioned') }}-
			</p>
			<p><b>(a)</b> {{ __('finalize.I_AM_ACITIZEN') }};</p> 
			<p><b>(b)</b> {{ __('finalize.that_I_have_completed') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>
			@if(!empty($age))
			{{$age}}
			@endif
			</b> </span> {{ __('finalize.years_of_age') }}  </p>
					<p><h6 class="td-center pt-one pb-three">[ {{ __('nomination.STRIKE_OUT') }} ]</h6></p>
			
			
			
			
					@if($recognized_party==0 or $recognized_party=='1' or $recognized_party=='' or $recognized_party==3)  
					 <p><b>(c)</b> (i) {{ __('nomination.i_am_set_1') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b> &nbsp; {{$party_id}} </b> </span>  {{ __('nomination.i_am_set_2') }} 
					  </p>
					@if($recognized_party!=3)    
					 <h6 class="td-center">{{ __('finalize.OR') }}</h6>
					 <p><strike><b>(c)</b> (ii) {{ __('nomination.i_am_set_1') }}   <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b> .............  </b> </span>  {{ __('nomination.i_am_set_3') }} / {{ __('nomination.i_am_set_333') }}  </strike></p>
					 
					<p><strike>{{ __('part3.spre') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b> &nbsp; 
					1. ........... </b> <b> &nbsp; 2. ............ </b> <b> &nbsp; 3. ............ </b> </span> </strike></p> 
				  
				  
				  
					@endif  
					  
					@endif
					@if($recognized_party==3)    	
					 <h6 class="td-center">{{ __('finalize.OR') }}</h6>
					@endif
					
					
					@if($recognized_party==2 or $recognized_party==3)	  
				
			@if($recognized_party!=3)	  	 
					<p><b>(c)</b>(i)  <strike>{{ __('nomination.i_am_set_1') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"> <b> &nbsp; ..........</b>    </span> {{ __('nomination.i_am_set_2') }} </strike></p> 
					 <h6 class="td-center">{{ __('finalize.OR') }}</h6>
				
				@endif   
					
					 
					@if($party_id2!=743) 
					 <p><b>(c)</b> (ii) {{ __('nomination.i_am_set_1') }}   <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b> &nbsp; <?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getPartyName($party_id2); ?>  </b> </span>  {{ __('nomination.i_am_set_3') }} / <strike> {{ __('nomination.i_am_set_333') }}  </strike> </p>					 
					 @endif
					 
					 @if($party_id2==743) 
					 <p><b>(c)</b> (ii) <strike> {{ __('nomination.i_am_set_1') }}   <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b> &nbsp; ... </b> </span>  {{ __('nomination.i_am_set_3') }} </strike> /  {{ __('nomination.i_am_set_333') }} </p>					 
					 @endif
					 
					<p>{{ __('part3.spre') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;"><b> &nbsp; 1. {{$suggest_symbol_1}} </b> <b> &nbsp; 2. {{$suggest_symbol_2}} </b> <b> &nbsp; 3. {{$suggest_symbol_3}} </b> </span> </p> 
					@endif
			
			
			
			
			
			
			
			<p><b>(d)</b> {{ __('finalize.my_name_and_my_father') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b> @if(!empty($language)) {{$language}} @endif </b>&nbsp;</span>{{ __('finalize.name_of_the') }}
			</p>
			<p><b>(e)</b> {{ __('finalize.That_to_the_best_of_my_knowledge_and_belief') }} </p>
			
			
			@if(!empty($part3_address))
			
			<p>
			* {{ __('finalize.I_further_declare') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>
			
			@if(!empty($category)) {{$category}} @endif</b>&nbsp;</span> ** {{ __('finalize.Caste_tribe_which') }}
			
						@if($category!='general')
						@if(!empty($part3_cast_state))
						**{{ __('finalize.Caste_tribe_state') }} 	
						<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>	
						<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_cast_state); ?>
						</b></span>
						@endif
						{{ __('finalize.in_relation_to') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>@if(!empty($part3_address)) {{$part3_address}} @endif</b></span> {{ __('finalize.in_that_State') }}.
				
					@else
					<strike>	**{{ __('finalize.Caste_tribe_state') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>	
						....................</b></span>	
					{{ __('finalize.in_relation_to') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>....................</b></span> {{ __('finalize.in_that_State') }}. </strike>
		@endif
			
			
			
			
			
			</p>
			
			
			
			
			
			
			
			
			
			
			
			@else 
				
			<p>
			* {{ __('finalize.I_further_declare') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>
			
			@if(!empty($category)) {{$category}} @endif</b>&nbsp;</span> ** {{ __('finalize.Caste_tribe_which') }}
			
			@if($category=='general')
			
			<strike>** {{ __('finalize.Caste_tribe_state') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>
			.........................
			</b></span> {{ __('finalize.in_relation_to') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
			<b>.........................</b></span> {{ __('finalize.in_that_State') }}.  </strike>
			
			@endif
			
			
			
			</p>
			
			
			@endif
			
			<p>
			 {{ __('finalize.That_to_the_best_of_my_knowledge') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
			@if(!empty($part3_legislative_state))
			<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_legislative_state); ?>
			@endif
			</b></span> {{ __('finalize.more_than_two') }}. 
		</p>
		</div>
		<div>
		  <table style="width: 100%; margin: 1.5rem 0;">
			 <tbody>
			   <tr>
				<td>{{ __('finalize.Date') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b> @if(!empty($part3_date)){{date('d/m/Y',strtotime($part3_date))}}@endif</b></span></td>
				<td class="td-right">
					<div>{{ __('finalize.Signature_of_Candidate') }} </div>
				</td>
			   </tr> 
			 </tbody>
		  </table> 
		</div>	
		<div style="width: 100%; border-top: 1px solid #000; font-size: 10px; line-height: 21px;">
			* {{ __('finalize.Score_out_this_paragraph') }}.<br>
			** {{ __('finalize.Score_out_the_words') }}.<br>
			<b>N.B.—</b> {{ __('finalize.recognized_political_party_text') }}<br>  
		</div>
		<div class="td-center" style="border-top: 2px solid #000; width: 100%; padding-top: 0.85rem;">
			<div ><strong>{{ __('finalize.PART3A') }}</strong></div>
			<p>({{ __('step3.To_be_filled_by_the_candidate') }})</p> 
		</div>
		<div class="table-wrap">
			  <table style="width: 100%">
				<tr>
					<td style="width:80%;">
					  <div class="param-area">
						<p><b>(1)</b>  {{ __('part3a.whether') }}—</p>
						<div class="sub-area" style="border-right: 1px solid #313131; padding-left: 15px; width: 95%;">
						  <p>(i)  {{ __('part3a.conv') }}— </p> 

							<div>(a) {{ __('part3a.offe') }} </div>
							<div>(b) {{ __('part3a.oro') }} </div>

						  <p>(ii) {{ __('part3a.impo') }}. <b>@if(!empty($have_police_case)){{ucfirst($have_police_case)}}  @endif</b></p> 
						</div><!-- End Of sub-area Div -->  
					  </div>
					</td>
					<td style="width:20%" valign="middle">{{ __('part3a.Yes') }}/{{ __('part3a.No') }}</td>
				</tr>
			  </table> 
		   </div>
		   @if(!empty($have_police_case))
	        @if($have_police_case == 'yes')
		     	<div>   <?php $i = 1; ?>
				   {{ __('part3a.ifye') }}
				    @if(!empty($police_cases))
				     @foreach($police_cases as $iterate_police_case)
					<div class="sub-area">
						<p>{{ __('part3a.case') }} <span  style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>{{$i}}</span></b></p> 
						<p>(i) {{ __('part3a.ca1') }}. <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>@if(!empty($iterate_police_case['case_no'])) {{$iterate_police_case['case_no']}} @endif</span></b></p> 
						<p>(ii) {{ __('part3a.pol') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; <b>@if(!empty($iterate_police_case['police_station'])) {{$iterate_police_case['police_station']}} @endif </b></span>&nbsp;&nbsp; {{ __('part3a.dist') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						
						@if(!empty($iterate_police_case['case_dist_no'])) {{$iterate_police_case['case_dist_no']}} @endif 
						
						
						@if(!empty($iterate_police_case['st_code']))
						 @if(!empty($iterate_police_case['case_dist_no']))	
						<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getDist($iterate_police_case['st_code'], $iterate_police_case['case_dist_no']); ?>
						  @endif 
						@endif   
						
						
						</b></span>&nbsp;&nbsp; {{ __('part3a.st') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['st_code']))
						<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($iterate_police_case['st_code']); ?>
						@endif   
						</b></span></p>
						<p>(iii)  {{ __('part3a.sec1') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['convicted_des']))
						   {{$iterate_police_case['convicted_des']}} 
						@endif   
						</b>&nbsp;</span></p>
						<p>(iv) {{ __('part3a.cdat') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['date_of_conviction']))
						  {{$iterate_police_case['date_of_conviction']}}
						@endif   
						
						
						</b></span></p>
						<p>(v) {{ __('part3a.cour') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['court_name']))
						{{$iterate_police_case['court_name']}}
						@endif   
						</b></span></p>
						<p>(vi) {{ __('part3a.puni') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['punishment_imposed']))
							{{$iterate_police_case['punishment_imposed']}}
						@endif   
						</b></span></p>		
						
						 <?php $dt='NA'; ?>		
						  @if($iterate_police_case['date_of_release']!='1970-01-01')
						  <?php $dt=$iterate_police_case['date_of_release']; ?>		
						  @endif	

						
						<p>(vii) {{ __('part3a.rele') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['date_of_release']))
						  {{$dt}}
						@endif   
						</b></span></p>
						<p>(viii) {{ __('part3a.aga') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['revision_against_conviction']))
						  {{$iterate_police_case['revision_against_conviction']}}
						@endif   
						</b></span> {{ __('part3a.Yes') }}/{{ __('part3a.No') }} </p>
						<p>(ix) {{ __('part3a.agad') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['revision_against_conviction']))
							{{$iterate_police_case['revision_against_conviction']}}
						@endif  
						</b></span></p>
						<p>(x)  {{ __('part3a.revf') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						@if(!empty($iterate_police_case['rev_court_name']))
							{{$iterate_police_case['rev_court_name']}}
						@endif
						</b></span></p>
						<p>(xi) {{ __('part3a.dips') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
						
						@if(!empty($iterate_police_case['status']))
						  {{$iterate_police_case['status']}}
						@endif
						
						</b></span></p>
						<p>(xii) {{ __('part3a.diee') }}—</p>
						
							<div>(a) {{ __('part3a.didd') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
							@if(!empty($iterate_police_case['revision_disposal_date']))
							  {{$iterate_police_case['revision_disposal_date']}}
							@endif
							</b></span></div>
							<div>(b) {{ __('part3a.nat') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>
							@if(!empty($iterate_police_case['revision_order_description']))
							  {{$iterate_police_case['revision_order_description']}}
							@endif
							</b></span></div>
					
					</div><!-- End Of sub-area Div -->
					  <?php $i++; ?>	
					@endforeach
					@endif 
				</div>
			@endif 
			@endif 
	      
		     	<div>
				   <b>(2)</b> {{ __('part3a.prop') }}
					<div class="sub-area">
					    <p><span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
						@if(!empty($profit_under_govt))
						  <b>  {{ucfirst($profit_under_govt)}} </b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }})</p>
							@if($profit_under_govt == 'yes')
							<p>- {{ __('part3a.ifyes1') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($office_held)}}</b></span></p>
							@endif
						@endif
						 
						 
					</div>
				</div>
		     
		  
			<div>
				<b>(3)</b>  {{ __('part3a.inso') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
				@if(!empty($court_insolvent))
				<b>{{ucfirst($court_insolvent)}}</b></span> &nbsp;  ({{ __('part3a.Yes') }}/{{ __('part3a.No') }})
				 <div class="sub-area">
				  @if($court_insolvent == 'yes')
					<p>-  {{ __('part3a.disc') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($discharged_insolvency)}}</b></span></p>
				  @endif
				 </div>	
				@endif
			</div>
		  <div>
				<b>(4)</b>  {{ __('part3a.alle') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp; 
				@if(!empty($allegiance_to_foreign_country))
					<b>{{ucfirst($allegiance_to_foreign_country)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }})
					<div class="sub-area">
						 @if($allegiance_to_foreign_country == 'yes')
						<p>-  {{ __('part3a.alled') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($country_detail)}}</b></span></p>
						@endif
					</div>	
				@endif
		</div>
		  <div>
				<b>(5)</b> {{ __('part3a.disq') }}   <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
				@if(!empty($disqualified_section8A))
				 <b>{{ucfirst($disqualified_section8A)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }})
				  <div class="sub-area">
					 @if($disqualified_section8A == 'yes')
					<p>- {{ __('part3a.peri') }}  <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($disqualified_section8A)}}</b></span></p>
					@endif
				  </div>		
				@endif	
			</div>
		  <div>
				<b>(6)</b> {{ __('part3a.corr') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
			@if(!empty($disloyalty_status))
			  <b>{{ucfirst($disloyalty_status)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }})
				<div class="sub-area">
					 @if($disloyalty_status == 'yes')
                    <p>-- {{ __('part3a.cord') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($date_of_dismissal)}}</b></span></p>
					@endif
				</div>		
			@endif		
		</div>
		  <div>
				<b>(7)</b> {{ __('part3a.subs') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
			@if(!empty($subsiting_gov_taken))
			<b>{{ucfirst($subsiting_gov_taken)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
				<div class="sub-area">
			@if($subsiting_gov_taken == 'yes')
			  <p>- {{ __('part3a.subp') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($subsitting_contract)}}</b></span></p>
			@endif
				</div>	
			@endif		
			</div>
		  <div>
				<b>(8)</b> {{ __('part3a.agen') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
					@if(!empty($managing_agent))		 
					<b>{{ucfirst($managing_agent)}}</b></span> &nbsp; ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
					<div class="sub-area">
					@if($managing_agent == 'yes')
                    <p>- {{ __('part3a.aged') }}<span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($gov_detail)}}</b></span></p>
					@endif
					</div>		
				@endif	
			</div>
		  <div>
				<b>(9)</b> {{ __('part3a.comm') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;
			@if(!empty($disqualified_by_comission_10Asec))		 
				<b>{{ucfirst($disqualified_by_comission_10Asec)}}</b></span> ({{ __('part3a.Yes') }}/{{ __('part3a.No') }}))
				<div class="sub-area">
					@if($disqualified_by_comission_10Asec=='yes')
                    <p>- {{ __('part3a.comd') }} <span style="border-bottom: 1px dashed #000; display: inline-block; width: 100%;">&nbsp;<b>{{ucfirst($date_of_disqualification)}}</b></span></p>
					@endif
				</div>		
			@endif 		
		</div>
		<div class="table-wrap">
		  <table style="width: 100%; margin: 1.5rem 0;">
			 <tbody>
			   <tr>
				<td>
					<div>{{ __('finalize.Place') }}: </div>
					<div>{{ __('finalize.Date') }}: <b>@if(!empty($date_of_disloyal)){{date('d/m/Y',strtotime($date_of_disloyal))}}@endif</b></div>
				</td>
				<td class="td-right">
					<div>{{ __('finalize.Signature_of_Candidate') }}</div>
				</td>
			   </tr> 
			 </tbody>
		  </table> 
		</div>
			  	  
  </div><!-- End Of wrap Div -->
  
  </div><!-- End Of wrap Div -->
  </htmlpagebody>
    <br>
  
	<htmlpagefooter name="page-footer">
		<table style="width:100%; border-collapse: collapse;" align="center" cellpadding="5">
			<tbody>
				<tr>
					<td colspan="2" align="center"><strong></strong>
					</td>
				</tr>
			</tbody>
		</table>
	</htmlpagefooter>
 	
        </body>
        </html>