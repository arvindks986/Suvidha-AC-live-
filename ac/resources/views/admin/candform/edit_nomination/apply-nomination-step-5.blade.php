    @extends('admin.layouts.ac.theme')
    @section('title', 'Nomination')
    @section('content')
    <style type="text/css">
     .error{font-size: 12px; color: red;}
        .display_none{display: none;}
        .form_steps p{padding: 15px 15px;}
        .heading-part1 p{padding: 0px !important;}
        .fullwidth{width: 100%;float: left;}
        #imagePreview{width: 150px;height: 150px; border: 1px solid #efefef;}
        .button-next{margin-top: 30px;}
        .button-next button{float: right;}
		ul{margin-bottom:0px;}		
		.marked {display: block; position: relative; padding-left: 35px; margin-bottom: 12px; cursor: pointer; font-size: 22px; -webkit-user-select: none; -moz-user-select: none;  -ms-user-select: none;  user-select: none;}
.marked input { position: absolute; opacity: 0; cursor: pointer;}
.checkmark { position: absolute; top: 0; left: 0; height: 25px; width: 25px; background-color: #eee; border-radius: 50%;}
.marked:hover input ~ .checkmark {background-color: #ccc;}
.marked input:checked ~ .checkmark { background-color: #c1448f;}
.checkmark:after {content: ""; position: absolute; display: none;}
.marked input:checked ~ .checkmark:after {display: block;}
.marked .checkmark:after {top: 9px;	left: 9px;	width: 8px;	height: 8px;	border-radius: 50%;	background: white;}
.information {padding-bottom: 10px;  position: relative;   padding-left: 32px;  text-transform: uppercase;  letter-spacing: 1px;  color: #bb4292;}
fieldset{width:100%;}
.nomination-detail .form-control{margin: 0 0 5px 0;   padding: 0 10px;   font-size: 14px;   font-weight: 500;}.nomination-field-2.date_jqueryui{background:transparent;}
    
	/*Help Animate CSS*/
  .animate-wrap{position:relative; display: block;}
  .animate-help-text {
	position: absolute; 
	top: 0.85rem; 
	right: 12rem;  
    background-color: #fbfbfb;
    color: #ee577e;
    border: 1px dashed #ee577e;
    padding: 1rem;
    border-radius: 0;
    font-size: 14px;
    box-shadow: 1px 1px 2px #999;
    display: block;
    align-items: center;
    width: auto;
}
  
.animate-icon {
    font-size: 2.5rem;
    position: absolute;
    right: -2.5rem;
	top: 0;
}
    .box {
        align-self: flex-end;
        animation-duration: 3s;
        animation-iteration-count: infinite;
        margin: 0 auto 0 auto;
        transform-origin: bottom;
    } 
   .bounce-1 {
        animation-name: bounce-1;
        animation-timing-function: linear;
    }
    @keyframes bounce-1 {
        0%   { transform: translateY(0); }
        50%  { transform: translateY(-25px); }
        100% { transform: translateY(0); }
    }
  .bounce-2 {
        animation-name: bounce-2;
        animation-timing-function: linear;
    }
    @keyframes bounce-2 {
        0%   { transform: translateX(0); }
        50%  { transform: translateX(25px); }
        100% { transform: translateX(0); }
    }	
	
	.dir-lft{right: 0rem;}
	.dir-lft .animate-icon {right: auto; left: -4rem;}
	
	.dir-dwn{bottom: 0rem;}
	.dir-dwn .animate-icon {right: auto; left: 5rem;top: 4rem;}
	</style>
    
	
	
    <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">	
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
    
	
	
	
	
	
	
	<main role="main" class="inner cover mb-3">
    @if(count($errors->all())>0 || session('flash-message'))
      <section class="mt-3">         

        <div class="container">
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
   </section>
@endif

  
<div class="container">
 <div class="step-wrap mt-4">
	 <ul class="text-center">
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step1') }}</span></li>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step2') }}</span></li>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step3') }}</span></li>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step4') }}</span></li>
	   <li class="step-current"><b>&#10004;</b><span>{{ __('step1.step5') }}</span></li>
	 </ul>
	 
 </div>
</div>



   <section>
    <div class="container p-0">

			


      <div class="row mt-3">
	  
		

        <div class="col-md-12">
          <div class="card">
			<div class="row" style="margin-top:15px;margin-right:10px;">
				<div class="fullwidth" style="float: left;width: 100%;">
				@if(isset($reference_id) && isset($href_download_application))
                <div class="col-md-5 float-right">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right">{{ __('election_details.ref') }}: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
                  </ul>
                </div>
                @endif
              </div>
           </div>
           <div class="card-header d-flex align-items-center">
             <h4>{{ __('part3a.nomp') }}</h4>
           </div>
		     <form method="post" action="{!! $action !!}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
                    <input type="hidden" name="candidate_id" value="{{$candidate_id}}"/>
                    <input type="hidden" name="reference_id" value="{{ $reference_id }}">
           <div class="card-body form-inline">
             <div class="row">
               <div class="col">
                 <div class="nomination-detail">
                   


                  
               <!--    <div class="row">
                   <div class="col-md-12 mt-3">
                     <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
                       <li class="step step1">Part I/II</li>
                       <li class="step step2 ">Part III<span></span></li>
                       <li class="step step3 current">Part IIIA<span></span></li>
                     </ul>
                   </div>
                 </div> -->


                 <!-- fieldsets -->
                 

                 <div class="part3 form_steps">
                

                    
                    <div class="nomination-parts">
                      <div class="nomination-form-heading text-center">
                        <h3><strong>{{ __('part3a.Part3a') }}</strong></h3>
                        ({{ __('part3a.tobe') }}) 
                      </div>

                      <div class="nomination-detail">
                        <div class="criminal-section">
                         (1)   ({{ __('part3a.whether') }}) —
                          <ul class="Ullist mb-3">
							  <li>(i) {{ __('part3a.conv') }}— 
								  <ul class="">
								  <li>(a)  {{ __('part3a.offe') }}</li> <br />
								  <li>(b)  {{ __('part3a.oro') }} -</li> <br />
								  </ul>
							  </li> <br />
						  <li>(ii) {{ __('part3a.impo') }} </li>
						  <div class="animate-wrap court_wrap" style="display:<?php echo ($have_police_case=='yes' || $have_police_case=='no')?'none':'block';?>">
									<div class="animate-help-text dir-lft">
										<div class="help-text">{{ __('messages.arcrt') }}</div>
										<div class="animate-icon">
											  <div class="box bounce-2"><i class="fa fa-hand-o-left" aria-hidden="true"></i></div>
										</div>
									</div>
								</div>
						 </ul>
                        </div>  
					   <div class="criminal-section">
						  <ul class="Ullist mb-3">
							<li class="d-inline-flex my-3">
								<div class="custom-control custom-radio customRadioBtn mx-4">
								  
								  @if($have_police_case=='yes')
								  <input type="radio" class="custom-control-input have_police_case form-control" id="tata" name="have_police_case" value="yes" checked="checked">
								  @else
								  <input type="radio" class="custom-control-input have_police_case form-control" id="tata" name="have_police_case" value="yes">
								  @endif	
								  <label class="custom-control-label" for="tata">{{ __('part3a.Yes') }}</label>
								</div> 
								<div class="custom-control custom-radio customRadioBtn">
								@if($have_police_case=='no') 	
								  <input type="radio" class="custom-control-input have_police_case form-control" id="atat" name="have_police_case" value="no" checked="checked">
								@else
								 <input type="radio" class="custom-control-input have_police_case form-control" id="atat" name="have_police_case" value="no">
							    @endif
								<label class="custom-control-label" for="atat">{{ __('part3a.No') }}</label>
								</div> 
								
						  </li>	
								
						 </ul>
						 
						</div>
						
					   
					 
					   
                        <!-- Police Case -->
                        <?php //echo "<pre>"; print_r($police_cases); die("__TEST"); ?> 
                        <div class="criminal-section have_police_case_div field_wrapper display_none">      
                         <div class="information"><strong>[{{ __('part3a.ifye') }}]</strong></div>
						
                          <?php $i = 0; $case_heading = 1;?>
                          <div class="fullwidth have_police_case_record">                            
                            <table class="table">
                              <tbody class="police_case_body"> 
                                @foreach($police_cases as $iterate_police_case)
                               <tr class="row_class_{{$i}}">
                                 <td class="nomination-detail" colsan="2">
								 <fieldset>
								 <legend><h5>{{ __('part3a.case') }} <?php echo $case_heading; ?></h5></legend>
								 
                                    <ol type="i">
                                      <li>{{ __('part3a.ca1') }} <input type="text" name="police_case[{{$i}}][case_no]" class="form-control" value="{{$iterate_police_case['case_no']}}" required></li>
                                      <li>{{ __('part3a.pol') }}<input type="text" name="police_case[{{$i}}][police_station]" class="form-control nomination-field-2" value="{{$iterate_police_case['police_station']}}" required><br>
                                        {{ __('part3a.st') }}
                                        <select name="police_case[{{$i}}][st_code]" class="form-control nomination-field-2" onchange="load_district(this.value, <?php echo $i ?>)" required>
                                          <option value="">--  {{ __('part3a.sels') }} --</option>
                                          @foreach($states as $iterate_state)
                                          @if($iterate_state['st_code'] == $iterate_police_case['st_code'])
                                          <option value="{{$iterate_state['st_code']}}" selected="selected">{{$iterate_state['st_name']}}</option>
                                          @else
                                          <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option>
                                          @endif
                                          @endforeach
                                        </select>
                                         {{ __('part3a.dist') }}
                                        <select name="police_case[{{$i}}][district]" class=" form-control nomination-field-2 district_<?php echo $i ?>" required>
                                          <option value="">--  {{ __('part3a.disd') }} --</option>
                                          @foreach($districts as $iterate_district)
                                          @if($iterate_district['st_code'] == $iterate_police_case['st_code'])

                                          @if($iterate_district['st_code'] == $iterate_police_case['st_code'])
                                          <option value="{{$iterate_district['district_no']}}" selected="selected">{{$iterate_district['district_name']}}</option>
                                          @else
                                          <option value="{{$iterate_district['district_no']}}">{{$iterate_district['district_name']}}</option>
                                          @endif

                                          @endif
                                          @endforeach
                                        </select>
                                      </li>
									  
									  
									  
                                      <li>{{ __('part3a.sec1') }}  <input type="text" name="police_case[{{$i}}][convicted_des]" class="form-control nomination-field-2" value="{{$iterate_police_case['convicted_des']}}" style='width:800px;' required></li>

                                      <li> {{ __('part3a.cdat') }} <input id="date_of_conviction<?php echo $i ?>" type="text" name="police_case[{{$i}}][date_of_conviction]" class="form-control  nomination-field-2 date_jqueryui" value="{{$iterate_police_case['date_of_conviction']}}" required></li>
                                      
                                      <li>{{ __('part3a.cour') }}  <input type="text" name="police_case[{{$i}}][court_name]" class="form-control nomination-field-2" value="{{$iterate_police_case['court_name']}}" style='width:800px;' required></li>

                                      <li>{{ __('part3a.puni') }} <input type="text" name="police_case[{{$i}}][punishment_imposed]" class="form-control nomination-field-2" value="{{$iterate_police_case['punishment_imposed']}}" style='width:800px;' required></li>
									  <?php $dt=''; ?>		
									  @if($iterate_police_case['date_of_release']!='1970-01-01')
									  <?php $dt=$iterate_police_case['date_of_release']; ?>		
									  @endif	
									  
                                      <li> {{ __('part3a.rele') }} <input type="text" id="date_of_release<?php echo $i ?>" name="police_case[{{$i}}][date_of_release]" class="form-control nomination-field-2 date_jqueryui" value="{{$dt}}"></li>
									  
									  
                                      <li>{{ __('part3a.aga') }}
                                        <select name="police_case[{{$i}}][revision_against_conviction]" class="form-control nomination-field-2 against_conviction" required>
                                          @foreach($yes_no_lists as $iterate_lis)
                                          @if($iterate_lis['id'] == $iterate_police_case['revision_against_conviction'])
                                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                                          @else
                                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                                          @endif
                                          @endforeach
                                        </select>

                                        <div class="revisedfiled">
                                        </div></li>
										<li>{{ __('part3a.agad') }} <input type="text" id="revision_appeal_date<?php echo $i ?>" name="police_case[{{$i}}][revision_appeal_date]" class="form-control nomination-field-2 date_jqueryui"   value="{{$iterate_police_case['revision_appeal_date']}}" required></li>

                                        <li>{{ __('part3a.revf') }} <input type="text" name="police_case[{{$i}}][rev_court_name]" class="form-control nomination-field-2" value="{{$iterate_police_case['rev_court_name']}}" style='width:800px;' required></li>

                                        <li>{{ __('part3a.dips') }} 
                                          <select name="police_case[{{$i}}][status]" class="form-control nomination-field-2 status" required>
                                            @foreach($yes_no_lists as $iterate_lis)
                                            @if($iterate_lis['id'] == $iterate_police_case['status'])
                                            <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                                            @else
                                            <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                                            @endif
                                            @endforeach

                                          </select>
                                        </li>

                                        <li class="statusReport">{{ __('part3a.diee') }} —

                                          <ul>
                                            <li>(a) {{ __('part3a.didd') }} <input type="text" id="revision_disposal_date<?php echo $i ?>" name="police_case[{{$i}}][revision_disposal_date]" class="form-control date_jqueryui"  value="{{$iterate_police_case['revision_disposal_date']}}" required></li>
                                            
                                            <li>(b) {{ __('part3a.nat') }} <input type="text" name="police_case[{{$i}}][revision_order_description]" class="form-control nomination-field-2" value="{{$iterate_police_case['revision_order_description']}}" style='width:800px;' required></li>
                                          </ul>
                                        </li>

                                        
                                        
                                      </ol>
                                      <button type="button" class="btn btn-default remove_police_case mb-3 float-right" id="remove_<?php echo $i; ?>" onclick="remove_police_case('row_class_<?php echo $i ?>')">{{ __('part3a.remo') }}</button>
									  </fieldset>
                                    </td>
                                  </tr>
                                  
                                  <?php $i++; $case_heading++; ?>
                                  @endforeach
                                </tbody>
                                <tfoot>
                                  <tr>
                                       <td>
                                      
                                    </td>
                                    <td class="text-right">
                                      <button type="button" class="btn btn-primary add_police_case">{{ __('part3a.add') }} </button>
                                    </td>
                                 
                                  </tr>
                                </tfoot>
                              </table>
                              
                            </div>


                          </div>
                          

                          <!-- End Police Case -->

                        </div>
						<?php 
						//echo "<pre>"; print_r($yes_no_lists);// die; 
						
						?>
					

                        <div class="casesec">
                       (2) {{ __('part3a.prop') }} 
							<select name="profit_under_govt" class="form-control nomination-field-2" onchange="return myval(this.value, 'one');">
                            @foreach($yes_no_lists as $iterate_lis)
                            @if($iterate_lis['id'] == $profit_under_govt)
                            <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                            @else
                            <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                            @endif
                            @endforeach
                          </select>
                        <small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>{{ __('part3a.ifyes1') }} 
						<?php 
						$widthStart='';
						if($office_held==''){
						 $d1='readonly';	
						} else {
						  $d1='';	
						  $widthStart='style=width:1000px;';
						} 
						?>
						
						
						<input id="one" type="text" name="office_held" class="form-control nomination-field-2" value="{{$office_held}}" {{$d1}} {{$widthStart}}></li></ul>
						
				

                       (3) {{ __('part3a.inso') }}  <select name="court_insolvent" class="form-control nomination-field-2" onchange="return myval(this.value, 'two');">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $court_insolvent)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>-{{ __('part3a.disc') }}  
						
						<?php $widthoo='';
						if($discharged_insolvency==''){
						 $d2='readonly';	
						} else {
						  $d2='';	
						  $widthoo='style=width:1000px;';
						}
						?>
						
						<input id="two" type="text" name="discharged_insolvency" class="form-control nomination-field-2" value="{{$discharged_insolvency}}" {{$d2}} {{$widthoo}}> 
						
						</li></ul>

                      (4) {{ __('part3a.alle') }} <select name="allegiance_to_foreign_country" class="form-control nomination-field-2" onchange="return myval(this.value, 'three');">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $allegiance_to_foreign_country)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>-{{ __('part3a.alled') }} 
						
						<?php 
						$width1='';
						if($country_detail==''){
						 $d3='readonly';	
						} else {
						  $d3='';	
						  $width1='style=width:1000px;';
						}
						?>
						<input id="three" type="text" name="country_detail" class="form-control nomination-field-2" value="{{$country_detail}}" {{$d3}} {{$width1}}> </li></ul>

                       (5) {{ __('part3a.disq') }} <select name="disqualified_section8A" class="form-control nomination-field-2" onchange="return myval(this.value, 'four');">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $disqualified_section8A)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>-{{ __('part3a.peri') }} 
						
						<?php  $width2='';
						if($disqualified_period==''){
						 $d4='readonly';	
						} else {
						  $d4='';	
						  $width2='style=width:1000px;';
						}
						?>
						
						<input id="four" type="text" name="disqualified_period" class="form-control nomination-field-2"  value="{{$disqualified_period}}" {{$d4}} {{$width2}}></li></ul>

                      (6) {{ __('part3a.corr') }} <select name="disloyalty_status" class="form-control nomination-field-2" onchange="return myval(this.value, 'date_of_dismissal');">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $disloyalty_status)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>-{{ __('part3a.cord') }} 
						
						<?php  
						if($date_of_dismissal==''){
						 $d5='readonly';	
						} else {
						  $d5=''; 
						}
						?>
						
						<input type="text" id="date_of_dismissal" name="date_of_dismissal" class="form-control nomination-field-2 date_jqueryui" value="{{$date_of_dismissal}}"    {{$d5}}></li></ul>

                        (7) {{ __('part3a.subs') }}<select name="subsiting_gov_taken" class="form-control nomination-field-2" onchange="return myval(this.value, 'subsitting_contract');">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $subsiting_gov_taken)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>-{{ __('part3a.subp') }}
						
						<?php $width4='';
						if($subsitting_contract==''){
						 $d6='readonly';	
						} else {
						  $d6='';	
						  $width4='style=width:1000px;';
						}
						?>
						
						<input type="text" id="subsitting_contract" name="subsitting_contract" class="form-control nomination-field-2" value="{{$subsitting_contract}}" {{$d6}} {{$width4}}></li></ul>

                       (8) {{ __('part3a.agen') }}<select name="managing_agent" class="form-control nomination-field-2" onchange="return myval(this.value, 'gov_detail');">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $managing_agent)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>-{{ __('part3a.aged') }} 
						
						<?php $width5='';
						if($gov_detail==''){
						 $d8='readonly';	
						} else {
						  $d8='';	
						  $width5='style=width:1000px;';
						}
						?>
						
						<input type="text" id="gov_detail" name="gov_detail" class="form-control nomination-field-2" value="{{$gov_detail}}" {{$d8}} {{$width5}}></li></ul>

                       (9) {{ __('part3a.comm') }} <select name="disqualified_by_comission_10Asec" class="form-control nomination-field-2" onchange="return myval(this.value, 'date_of_disqualification');">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $disqualified_by_comission_10Asec)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>({{ __('part3a.Yes') }} / {{ __('part3a.No') }} )</small>
                        <ul><li>-{{ __('part3a.comd') }} 
						
						
						<?php 
						if($date_of_disqualification==''){
						 $d7='readonly';	
						} else {
						  $d7='';	
						 
						}
						?>
						<input type="text" id="date_of_disqualification" name="date_of_disqualification" class="form-control nomination-field-2 date_jqueryui" value="{{$date_of_disqualification}}" {{$d7}}></li></ul>

                      </div>
				
                      <div class="nomination-signature">
                        <span class="nomination-date left">{{ __('part3a.Date') }}: <input type="text" name="date_of_disloyal" id="date_of_disloyal" class="nomination-field-4" value="@if($date_of_disloyal==''){{date('Y-m-d')}}@else{{$date_of_disloyal}}@endif" readonly="readonly" required>
						
						
						</span>
                        
                      </div>
                      
                    </div>
 </div>
             
            </div>  </div>
        </div></div>




         <!-- <div class="card-footer">
          <div class="form-group row ">
            <div class="col">
              <a href="{{$href_back}}" id="" class="btn btn-secondary float-left">Back</a>
            </div>
            <div class="col ">
              <div class="form-group row float-right">
              <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button>
              <button type="submit" class="btn btn-primary save_next float-right">Save & Next</button>
            </div>
            </div>
            </div>
         </div>-->
		 
		<div class="card-footer">
        <div class="row align-items-center">
          <div class="col-sm-6 col-12"> <a href="{{$href_back}}" id="" class="btn btn-lg btn-secondary font-big">{{ __('step1.Back') }}</a> </div>
          <div class="col-sm-6 col-12">
            <div class="apt-btn text-right"> 			
			<a href="{{ url('roac/candidateinformation?nom_id='.encrypt_string($reference_id)) }}" class="btn btn-lg font-big dark-pink-btn">{{ __('step1.Cancel') }}</a> 	
			&nbsp;
			&nbsp;
			&nbsp;	
			<button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn">{{ __('Save') }}</button>
			</div>          
		  </div>
        </div>
      </div> 
		  
		 
		 
		 
		 

                

               

            

         
      
	  </form>
    </div>
  </div>
</div>
</div>    
</section>

</main>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>



<script>
  function myval(val, id){
	if(val=='no'){
		$("#"+id).val("");
		$("#"+id).css("width", "178px");
		$("#"+id).prop("readonly", true);
	} else {
		if(id!='date_of_disqualification' && id!='date_of_dismissal'){
		$("#"+id).css("width", "1000px");
		}
		$("#"+id).prop("readonly", false);
	}
	  
  }	
  
  $("input[name='have_police_case']").click(function(){
	  $(".court_wrap").hide();
  });

  $(document).ready(function(){  
   if($('#breadcrumb').length){
     var breadcrumb = '';
     $.each({!! json_encode($breadcrumbs) !!},function(index, object){
      breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
    });
     $('#breadcrumb').html(breadcrumb);
   }


   $('.have_police_case').change(function(e){
	   
    have_police_case();
  });

   initailize_datepicker();
   have_police_case();

   var i = "<?php echo $i ?>";
   var case_heading = "<?php echo $case_heading ?>";
   
   $('.add_police_case').click(function(e){
    var remove_case = "remove_"+i;
    var row_class = "row_class_"+i;
    var html = "";
    html += "<tr class='"+row_class+"'>";
    html += "<td colspan='2' class='nomination-detail'>";
    html += "<fieldset>";
    html += "<legend class='text-center'><h6 class='p-2'> <?php echo __('part3a.case') ?> "+case_heading+" </h6></legend>";
    html += "<ol type='i'>";
    html += "<li>";
    html += "<?php echo __('part3a.ca1') ?>";
    html += "<input type='text' class='form-control' name='police_case["+i+"][case_no]' required>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.pol') ?>";
    html += "<input type='text' class='form-control' name='police_case["+i+"][police_station]' required>";
    html += "<?php echo __('part3a.st') ?> ";
    html += "<select name='police_case["+i+"][st_code]' class='form-control' onchange='load_district(this.value, "+i+")' required>";
    html += "<option value=''>-- <?php echo __('part3a.sels') ?> --</option>"; 
    <?php foreach($states as $iterate_state){ ?>
      html += "<option value='{{$iterate_state['st_code']}}'>{{$iterate_state['st_name']}}</option>";
    <?php } ?>
    html += "</select>";
    html += "<?php echo __('part3a.dist') ?> ";
    html += "<select name='police_case["+i+"][district]' class='form-control district_"+i+"' required>";
    html += "<option value=''>-- <?php echo __('part3a.disd') ?> --</option>"; 
    html += "</select>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.sec1') ?>"; 
    html += "<input type='text' class='form-control' name='police_case["+i+"][convicted_des]' value='' style='width:800px;' required>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.cdat') ?>";
    html += "<input id='date_of_conviction"+i+"' type='text' name='police_case["+i+"][date_of_conviction]' class='form-control nomination-field-2 date_jqueryui' value=''   required>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.cour') ?> ";
    html += "<input type='text' name='police_case["+i+"][court_name]' class='form-control' value='' style='width:800px;' required>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.puni') ?>";
    html += "<input type='text' name='police_case["+i+"][punishment_imposed]' class='form-control' value='' style='width:800px;' required>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.rele') ?> ";
    html += "<input type='text' id='date_of_release"+i+"' name='police_case["+i+"][date_of_release]' class='form-control nomination-field-2 date_jqueryui' value='' >";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.aga') ?> ";
    html += "<select name='police_case["+i+"][revision_against_conviction]' class='form-control nomination-field-2 against_conviction' required>";
    <?php foreach($yes_no_lists as $iterate_lis){ ?>
      html += "<option value='{{$iterate_lis['id']}}'>{{$iterate_lis['name']}}</option>";
    <?php } ?>
    html += "</select>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.agad') ?> ";
    html += "<input type='text' id='revision_appeal_date"+i+"' value='' name='police_case["+i+"][revision_appeal_date]' class='form-control nomination-field-2 date_jqueryui' required>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.revf') ?> ";
    html += "<input type='text' name='police_case["+i+"][rev_court_name]' class='form-control' value='' style='width:800px;' required>";
    html += "</li>";
    html += "<li>";
    html += "<?php echo __('part3a.dips') ?>"; 
    html += "<select name='police_case["+i+"][status]' class='form-control nomination-field-2 status' required>";
    <?php foreach($yes_no_lists as $iterate_lis){ ?>
      html += "<option value='{{$iterate_lis['id']}}'>{{$iterate_lis['name']}}</option>";
    <?php } ?>
    html += "</select>";
    html += "</li>";
    html += "<li class='statusReport'>";
    html += "<?php echo __('part3a.diee') ?>—";
    html += "<ul>";
    html += "<li>";
    html += "(a) <?php echo __('part3a.didd') ?> ";
    html += "<input type='text' id='revision_disposal_date"+i+"' name='police_case["+i+"][revision_disposal_date]' class='form-control date_jqueryui' value='' required>"; 
    html += "</li>";
    html += "<li>";
    html += "(b) <?php echo __('part3a.nat') ?> ";
    html += "<input type='text' name='police_case["+i+"][revision_order_description]' class='form-control'  value='' style='width:800px;' required>";
    html += "</li>";
    html += "</ul>";
    html += "</li>";
    html += "</ol>";
    html += "<button type='button' class='btn btn-default remove_police_case mb-3 float-right' id='"+remove_case+"' onclick=remove_police_case('"+row_class+"')><?php echo __('part3a.remo') ?></button>";
    html += "</fieldset>";
    html += "</td>";
    html += "<tr>";
    $('.police_case_body').append(html);
    /*
    $('.remove_police_case').click(function(e){
      $(this).parent('td').parent('tr').remove();
    });
    */

    if($(".row_class_"+i+" .date_jqueryui").length>0){
      $(".row_class_"+i+" .date_jqueryui").each(function(index,object){
        $(".row_class_"+i+" #"+$(object).attr('id')).datepicker({
          dateFormat: 'dd-mm-yy',
          maxDate: 0,
          changeYear: true
        });
      });
    }

    case_heading++;
    i++;
  });

  });


  function remove_police_case(id){
    $('.'+id).remove();
  }

    function initailize_datepicker(){
      if($('.date_jqueryui').length>0){
        $('.date_jqueryui').each(function(index,object){
          $('#'+$(object).attr('id')).datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: 0,
            changeYear: true
          });
        });
      }
    }

    function have_police_case(){
      if($(".have_police_case:checked").val() == 'yes'){
        $('.have_police_case_div').removeClass('display_none');
      }else{
        $('.have_police_case_div').addClass('display_none');
      }
    }

    function load_district(id, row_number){
      html = '';
      html += "<option value=''>Select</option>";
      var districts = <?php echo json_encode($districts); ?>;
      $.each(districts, function(index, object){
        if(object.st_code == id){
          html += "<option value='"+object.district_no+"'>"+object.district_name+"</option>";
        }
      });
      $(".district_"+row_number).empty().append(html);
      $(".district_"+row_number).val($(".district_"+row_number+" option:first").val());
    }


    $(document).ready(function(e){
      <?php foreach($custom_errors as $key => $custom_error){ ?>
        <?php foreach($custom_error as $second_key => $err){ ?>
          <?php if($err){ ?>
            $("input[name = 'police_case[<?php echo $key ?>][<?php echo $second_key ?>]'").after("<span class='text-danger small_text'><?php echo $err; ?></span>");
           <!-- $("input[name = 'police_case[<?php echo $key ?>][<?php echo $second_key ?>]'").addClass('is-valid'); -->
            $("input[name = 'police_case[<?php echo $key ?>][<?php echo $second_key ?>]'");
          <?php } ?>
        <?php } ?>
      <?php } ?>
    });
  </script>


  @endsection