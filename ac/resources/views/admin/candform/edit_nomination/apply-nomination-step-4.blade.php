      @extends('admin.layouts.ac.theme')
      @section('title', 'Nomination')
      @section('content')
      <style type="text/css">
        .error{
          font-size: 12px; 
          color: red;
        }
        .display_none{
          display: none;
        }
        .form_steps p{
          padding: 15px 15px;
        }
        .heading-part1 p{
          padding: 0px !important;
        }
        .fullwidth{
          width: 100%;
          float: left;
        }
        #imagePreview{
          width: 150px;
          height: 150px;
          border: 1px solid #efefef;
        }
        .button-next{
          margin-top: 30px;
        }
        .button-next button{
          float: right;
        }
		
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
      <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
      <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
	  <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}" id="theme-stylesheet">
	  
	

	<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">	
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
	  
	<main class="pt-3 pb-5 pl-5 pr-5">
	
	@if(count($errors->all())>0 || session('flash-message'))
        <section class="mt-3">
       <div class="container">            
               @if(count($errors->all())>0)
               <div class="alert alert-danger">
                 @foreach($errors->all() as $iterate_error)
                 <p class="text-left">
				 {!! $iterate_error !!}
				 </p>
                 @endforeach
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
			   <li class="step-current"><b>&#10004;</b><span>{{ __('step1.step4') }}</span></li>
			   <li class=""><b>&#10004;</b><span>{{ __('step1.step5') }}</span></li>
			 </ul>
		 </div>
		</div>
	
     <div class="container-fluid">
        <div class="card">
        <div class="card-header text-center">
	    
		<div class="row">
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
	  
	  
         <div class="">
          <h4>{{ __('step3.form2b') }}</h4>
          <div>({{ __('step3.rule4') }})</div>
          <div>{{ __('step3.nomp') }}</div>
          <div>{{ __('step3.nommessage') }} <span class="">({{$st_name}})</span></div>
        </div>
      </div>
      <div class="card-body">
	  <div class="part-3">
		<h3 class="part-title mt-2 mb-5"><span>{{ __('part3.Part3') }}</span></h3>  
         <form method="post" action="{!! $action !!}" enctype="multipart/form-data">
		  <input type="hidden" name="_token" value="{{csrf_token()}}"/>
		  <input type="hidden" name="nomination_id" value="{{$nomination_id}}"/>
		  <input type="hidden" name="candidate_id" value="{{$candidate_id}}"/>
						 
	       <fieldset class="py-4 px-5 mt-2 mb-4">
            <legend>{{ __('part3.DECLARATION') }}</legend>
			<p> {{ __('part3.i') }}<b> 
			@if($recognized_party==0 or $recognized_party==1 ) 
			{{ __('step3.partn') }}  
			@elseif($recognized_party==3 ) {{ __('step3.partn') }} , {{ __('step3.part2') }}  
			@else {{ __('step3.part2') }} 
			@endif </b>
			
			{{ __('part3.assent') }}—</p>	
			<div class="info-checkbox">
			 (a)&nbsp; 
				<div class="custom-control custom-checkbox customCheckBtn mr-2">
					<input type="checkbox" class="custom-control-input" id="customCheck" name="example1" checked disabled>
					<label class="custom-control-label" for="customCheck"></label>
				</div>
				<p>{{ __('part3.citi') }}</p> 
			</div>
			<div class="row mt-2 mb-4 align-items-center">
			  <div class="col-sm-3 col-12 pr-0">(b)&nbsp; {{ __('part3.age') }}</div>
			  <div class="col-sm-3 col-12 pl-0">
				<input type="number" name="age" placeholder="Enter Age Of Years" min="25" class="form-control" value="{{$age}}">
			  </div>
			</div>   
            <div class="row align-items-center">
			@if($recognized_party==0 or $recognized_party=='1' or $recognized_party=='' or $recognized_party==3) 
			<?php 
			$dis1='';
			$rec='';
			$rec2='';	
			$d1='';
			$d2='';
			
			
			if($recognized_type==0 or $recognized_type=='1' or $recognized_type=='') {	
			$rec='block';
			$rec2='none';	
			$d2='disabled';
			$d1='';
			}
			if($recognized_type==2){
			$rec='none';
			$rec2='block';	
			$d2='';
			$d1='disabled';
			}
			
			?>	
			
		
			<div class="col-sm-12 col-12">
				<div class="d-flex align-items-center my-3">
                 <p class="mr-2">C(i)&nbsp; {{ __('part3.rec') }}</p>
				  <div class="custom-control custom-radio customRadioBtn mr-3" onclick="return national();">
					<input type="radio" class="custom-control-input" id="nParty" name="recognized_type" value="1" @if($recognized_type==1 or $recognized_type=='') {{'checked'}}@endif>
					<label class="custom-control-label" for="nParty">{{ __('part3.nat') }}</label>
				  </div>
				  <div class="custom-control custom-radio customRadioBtn mr-3" onclick="return state();">
					<input type="radio" class="custom-control-input" id="nonRecg" name="recognized_type" value="2" @if($recognized_type==2) {{'checked'}}@endif>
					<label class="custom-control-label" for="nonRecg">{{ __('part3.stp') }}</label>
				  </div>	
				</div>
			 </div>	
			 
			<div class="col-sm-12">
			<div class="d-flex my-3">
			  <div class="lbl-mandry mt-4 mr-5">{{ __('part3.pname') }}</div>	
			  <div class="mt-4" style="width:70%;">
			  <select name="party_id" class="form-control" id="national" style="display:{{$rec}}" onchange="getPartyVal(this.value);" {{$d1}}>
				<option value="">-- {{ __('part3.ps') }} --</option>    
				@foreach($parties as $iterate_party)
				   @if($party_id == $iterate_party['party_id'])
				   <option value="{{ $iterate_party['party_id'] }}" selected="selected">{{ $iterate_party['name'] }}</option>
				   @else 
				   <option value="{{ $iterate_party['party_id'] }}">{{ $iterate_party['name'] }}</option>
				   @endif
				 @endforeach
			  </select>
			
			 <select name="party_id" class="form-control" id="state" style="display:{{$rec2}}" onchange="getPartyVal(this.value);" {{$d2}}>
				<option value="">-- {{ __('part3.ps') }} --</option>    
				@foreach($parties_state as $iterate_party_state)
				   @if($party_id == $iterate_party_state['party_id_state'])
				   <option value="{{ $iterate_party_state['party_id_state'] }}" selected="selected">{{ $iterate_party_state['name_party_id_state'] }}</option>
				   @else 
				   <option value="{{ $iterate_party_state['party_id_state'] }}"> {{ $iterate_party_state['name_party_id_state'] }}</option>
				   @endif
				 @endforeach
			  </select>	
			<div><small class="text-black-50">{{ __('part3.symbol') }}</small></div></div>		
				<div class="animate-wrap party_wrap" style="width:30%" style="display:<?php echo (!empty($setup_partyDatat['party_id']))?'none':'block';?>">
					<div class="animate-help-text dir-lft">
				    <div class="help-text">{{ __('messages.arparty') }}</div>
					<div class="animate-icon">
					      <div class="box bounce-2"><i class="fa fa-hand-o-left" aria-hidden="true"></i></div>
					</div>
				  </div>
				</div>
			</div>
			</div>
			
			
			@if($recognized_party!=3)
			<!-- Need to strike part 3(II) -->
			<div class="col-sm-12 col-12">
			    <div class="d-flex align-items-center my-3">
				  <div> <hr style="width: 64%; height: -31px; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">c(ii)&nbsp;</hr></div>	
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input pParty" >
					<label class="custom-control-label" for="first"  >{{ __('part3.recp') }}</label>
				  </div> 
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input"> 
					<label class="custom-control-label" for="second"  >{{ __('part3.ind') }}</label>
				  </div>				  
				  <div class="mr-3">			  
				  </div>				  
				</div>	
			  </div>	
			<!--End Need to strike part 3(II) -->
			@endif
			
			
			
			
			
			
			
			@endif
			
			
			@if($recognized_party==2 or $recognized_party==3)
			<?php 
			$dis1='';
			$dis2='';
			if($unrecognized_type==1 or $unrecognized_type==0 or $unrecognized_type==''){
			$dis1='block';
			$dis2='none';	
			}
			
			if($unrecognized_type==2){
			$dis1='none';
			$dis2='block';	
			}
			?>
			@if($recognized_party!=3)
			<!-- Need to strike part 3(I) -->
			<div class="col-sm-12 col-12">
				<div class="d-flex align-items-center my-3">
                 <p class="mr-2"><hr style="width:82%; height: -31px; display: block; border-top:2px solid #000; position: absolute; margin-top: 0px;"> C(i)&nbsp; {{ __('part3.rec') }} </hr></p>
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input">
					<label class="custom-control-label" for="nonRecg">{{ __('part3.nat') }}</label>
				  </div>	
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input">
					<label class="custom-control-label" for="nonRecg">{{ __('part3.stp') }}</label>
				  </div>	
				</div>
			 </div>	
			 <!-- Need to strike part 3(I) -->
			 @endif
			
			<div class="col-sm-12 col-12">
			    <div class="d-flex align-items-center my-3">
				  <div>c(ii)&nbsp;</div>	
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input pParty" id="first" name="unrecognized_type" value="1" 
					@if($unrecognized_type==1 or $unrecognized_type=='') {{'checked'}} @endif>
					<label class="custom-control-label" for="first"   onclick="return shows('1');">{{ __('part3.recp') }}</label>
				  </div> 
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input" id="second" name="unrecognized_type" value="2" @if($unrecognized_type==2) {{'checked'}} @endif> 
					<label class="custom-control-label" for="second"  onclick="return shows('2');">{{ __('part3.ind') }}</label>
				  </div>				  
				  <div class="mr-3">
				  
				  <div id="setupDiv" style="display:<?php echo $dis1; ?>" >
				   <select name="party_id2" class="form-control js-example-basic-single" title="{{ __('part3.ps') }}" id="setup" style="display:<?php echo $dis1; ?>">
				   <option value=""></option>     
					@foreach($setup_party as $setup_partyDatat)
					   @if($party_id2 == $setup_partyDatat['party_id'] and $setup_partyDatat['party_id']!=743)
					   <option value="{{ $setup_partyDatat['party_id'] }}" selected="selected">{{ $setup_partyDatat['name'] }}</option>
					   @else selected
					   <option value="{{ $setup_partyDatat['party_id'] }}"> {{ $setup_partyDatat['name'] }} </option>
					   @endif
					 @endforeach
				  </select>	
				  </div>
				
				  <select name="party_id2" class="form-control" id="setup_independent" style="display:<?php echo $dis2; ?>;" disabled>
					@foreach($setup_party as $setup_partyDatat)
					   @if($setup_partyDatat['PARTYTYPE']=='Z')
					   <option value="{{ $setup_partyDatat['party_id'] }}" selected="">{{ $setup_partyDatat['name'] }}</option>
					   @endif
					 @endforeach
				  </select>				  
				  </div>				  
				</div>	
			  </div>	
              <div class="col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry">{{ __('part3.spre') }}</label>
				  <div class="d-flex">
					<div class="col-sm-4 col-12">1.<input list="sym1" type="text" name="suggest_symbol_1"  id="suggest_symbol_1" class="form-control" value="{{$suggest_symbol_1}}" onmouseover="return getSymbol(1);" onkeypress="return getSymbol(1);" >
					  <datalist id="sym1">
						<option value="Edge">
					  </datalist>
					  </div>
					  <div class="col-sm-4 col-12">2. <input list="sym2" type="text" name="suggest_symbol_2" id="suggest_symbol_2" class="form-control" value="{{$suggest_symbol_2}}" onmouseover="return getSymbol(2);" onkeypress="return getSymbol(2);">
					  <datalist id="sym2">
						<option value="Edge">
					  </datalist>
					  </div>
					    <div class="col-sm-4 col-12">3. <input list="sym3" type="text" name="suggest_symbol_3" id="suggest_symbol_3" class="form-control"  value="{{$suggest_symbol_3}}" onmouseover="return getSymbol(3);" onkeypress="return getSymbol(3);">
						<datalist id="sym3">
						<option value="Edge">
					  </datalist>
						</div>
				  </div>	
                </div>
              </div>
			  
			  
			  
			   <?php if($dis2=='block'){ ?>
				<input type="hidden" name="party_id2" id='cstpid' value="743">
			  <?php } ?>
			   <?php if($recognized_party=='2'){ ?>
				<input type="hidden" name="party_id"  value="330">
			  <?php } ?>
			  
			  
			  
			 @endif 
			 
					  
					  
					  
			  <div class="col-sm-12 col-12">
                <div class="d-flex align-items-center my-3">
                  <div class="lbl-mandry mr-4">(d)&nbsp; {{ __('part3.lang') }}</div>
				<div style="width: 12.50%;">
                  <input type="text" list="language" id="langData" name="language" class="form-control alphaonly" value="{{$language}}" onmouseover="return getLangauge();" style="width: 120%;" autocomplete="off">
				</div>		
				<datalist id="language">
						<option >
					  </datalist>
                </div>
              </div>
		   <div class="col-sm-12 col-12">		
			 <div class="info-checkbox mb-3">
			(e)	 
			    <div class="custom-control custom-checkbox customCheckBtn">
					<input type="checkbox" class="custom-control-input" id="customCheck01" name="" checked disabled>
					<label class="custom-control-label" for="customCheck01"></label>
				</div>
				 
				 <p>{{ __('part3.dec') }}</p>
				
			</div>
		   </div>
		   <div class="col-sm-12 col-12">
			<div class="one-param">
			
			
			@if(isset($category))
			@if($category=='general')	
			<div class="col-sm-12 col-12">		
			 <div class="info-checkbox mb-3">			 
			    <div class="custom-control custom-checkbox customCheckBtn">
					<input type="checkbox" class="custom-control-input" name="not_applicable" id="customCheck013" checked disabled
					@if($not_applicable=='on'){{'checked'}}@endif>
					<label class="custom-control-label" for="customCheck013" style="background: none; position: static; margin-top: -15px; margin-left: 18px;">{{ __('part3.np') }},</label>
				</div>
				
				
				
			</div>
		   </div>
			<input type="hidden" name="part3_cast_state" id="two" value="">
			<input type="hidden" name="part3_address" id="three" value="">
			
			
			
			
			<!-- Strike COntent -->
			
			<span>
			   *{{ __('part3.further') }} 
			   <select name="category" class="form-control" disabled>
			  <option value="">{{ __('part3.Select') }}</option>
			  @foreach($categories as $iterate_category)
			   @if($category == $iterate_category['id'])
			   <option value="{{$iterate_category['id']}}" selected="selected">{{$iterate_category['name']}}</option>
			   @else
			   <option value="{{$iterate_category['id']}}">{{$iterate_category['name']}}</option>
			   @endif
			   @endforeach
			</select>
			    <hr style="width:65%; height: -31px; display: block; border-top:2px solid #000; position: absolute; margin-top: 15px;">  {{ __('part3.caste') }} </hr>
				<select   class="form-control" disabled>
              <option value="">-- {{ __('part3.Select') }} --</option>
             
           </select>
			{{ __('part3.rel') }} <input type="text" class="form-control" readonly>{{ __('part3.area') }}.
			 </span>  
			<!--End Strike Content -->
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		   @endif
		   @endif

		   
		   <?php $chk=''; ?>	
			@if($not_applicable=='on')
			<?php $chk='none'; ?>
			@else
			<?php $chk='black'; ?>	
			@endif
			
			@if(isset($category))
			@if($category!='general')
				
			
			
			
			
			
			<span id="TTT" style="display:<?php echo $chk; ?>;">
			   *{{ __('part3.further') }} 
			  <select name="category" class="form-control" disabled>
			  <option value="">{{ __('part3.Select') }}</option>
			  @foreach($categories as $iterate_category)
			   @if($category == $iterate_category['id'])
			   <option value="{{$iterate_category['id']}}" selected="selected">{{$iterate_category['name']}}</option>
			   @else
			   <option value="{{$iterate_category['id']}}">{{$iterate_category['name']}}</option>
			   @endif
			   @endforeach
			</select>
			  {{ __('part3.caste') }}
				<select name="part3_cast_state" class="form-control" id="part3_cast_state">
             <option value="">-- {{ __('part3.Select') }} --</option>
             @foreach($states as $iterate_state)
               @if($part3_cast_state == $iterate_state['st_code'])
               <option value="{{ $iterate_state['st_code'] }}" selected="selected">{{ $iterate_state['st_name'] }}</option>
               @else 
               <option value="{{ $iterate_state['st_code'] }}"> {{ $iterate_state['st_name'] }}</option>
               @endif
             @endforeach
           </select>
			{{ __('part3.rel') }} <input type="text" name="part3_address" id="part3_address" class="form-control" value="{{$part3_address}}">{{ __('part3.area') }}.
			 </span>  
			@endif
			@endif
			   <br/>
           
				{{ __('part3.also') }}
			<select name="part3_legislative_state" class="form-control" id="part3_legislative_state" disabled>
             <option value="">-- {{ __('part3.Select') }} --</option>
             @foreach($states as $iterate_state)
               @if( $iterate_state['st_code'] == $st_code)
               <option value="{{ $iterate_state['st_code'] }}" selected="selected">{{ $iterate_state['st_name'] }}</option>
               @else 
               <option value="{{ $iterate_state['st_code'] }}"> {{ $iterate_state['st_name'] }}</option>
               @endif
             @endforeach
           </select>{{ __('part3.aca') }}
		   
		   <input type="hidden" name="part3_legislative_state" value="{{$st_code}}" id="part3_legislative_state" >
			
                              
				
			</div>	
		   </div>		
			 
            </div>
          </fieldset>
		  
			<input type="hidden" name="category" id="one" value="{{$category}}">

				
          <div class="row my-3">
            <div class="col-sm-6 col-12"><strong>{{ __('part3.date') }}:</strong> <span>
			<input type="hidden" name="part3_date" id="part3_date" value="{{$part3_date}}" readonly="readonly">
			<input type="text" name="part3_date" id="part3_date" value="{{$part3_date}}" readonly="readonly" disabled>
			</span></div>
            <div class="col-sm-6 col-12"></div>
          </div>
	
      <div class="nomination-note"> <small>*{{ __('part3.bm1') }}.</small> 
        <small>** {{ __('part3.bm2') }}.</small>
        <small>{{ __('part3.bm3') }}</small> 
	</div>
	
	
	<div class="card-footer">
			<div class="row align-items-center">
			  <div class="col-sm-6 col-12"> <a href="{{$href_back}}" id="" class="btn btn-lg btn-secondary font-big">{{ __('step1.Back') }}</a> </div>
			  <div class="col-sm-6 col-12">
				<div class="apt-btn text-right"> 
				
				<a href="{{ url('roac/candidateinformation?nom_id='.encrypt_string($reference_id)) }}" class="btn btn-lg font-big dark-pink-btn">{{ __('step1.Cancel') }}</a> 
			   &nbsp;	
				
				<?php if($recognized_party=='2'){ ?>
				 <button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return chkCat();">{{ __('step1.Save_Next') }}</button>
				<?php } else { ?> 
				  <button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return chkCat();">{{ __('step1.Save_Next') }}</button>
				<?php }  ?> 
				
				</div>
			  </div>
			</div>
		  </div>
	
   </form>
	</div><!-- End Of part-1 Div -->	  
         
	  </div><!-- End Of card-body Div -->  
      
		  
	  </div>
		</div><!-- End Of container-fluid Div -->	  
	</main>
	
     @endsection

     @section('script')
    <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>
	<script type="text/javascript" src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}"></script>
	<script type="text/javascript" src="{{ asset('appoinment/js/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>
	
	<!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>-->
    <link rel="stylesheet" href="{{ asset('appoinment/select-search/select2.min.css') }}" id="theme-stylesheet">	
	<script type="text/javascript" src="{{ asset('appoinment/select-search/select2.min.js') }}"></script>
	
	 <script type="text/javascript">
		var searh = jQuery.noConflict();	
		searh(document).ready(function() {
			searh('.js-example-basic-single').select2({
			placeholder: "{{ __('part3.ps') }}",
			allowClear: true
		});
		});
		function chkCat(){ 
			if("<?php echo $category!='general' ?>"){
				var part3_cast_state = $("#part3_cast_state").val();
				var part3_address = $("#part3_address").val();
				
				if(part3_cast_state==''){
				alert("<?php echo __('messages.tribcaststate') ?>");
				$("#part3_cast_state").focus();
				return false;
				}
				if(part3_address==''){
				alert("<?php echo __('messages.tribcaststatearea') ?>");
				$("#part3_address").focus();
				return false;
				}
			}	
		}
		
		
		function getPartyVal(partyid){
			if(partyid !=''){
				$(".party_wrap").hide();
			}else{
				$(".party_wrap").show();
			}
		}
		
		function getLangauge(){ 
		   
		   $("#langData").val();
		   $("#language").val();
		   $("#langData").empty();
		   $("#language").empty();
		
		   var html = [];
			
			var lang = ['Hindi', 'Urdu',  'Marathi',  'Gujarati',  'Bengali',  'Punjabi',  'Malayalam',  'Kannada',  'Assamese',  'Bodo',  'Dogri',  'Kashmiri',  'Konkani',  'Maithili',  'Manipuri',  'MeeteiMayek',  'Nepali',  'OlChiki',  'Oriya',  'Sanskrit',  'Santali',  'SindhiDev',  'Tamil', 'Telugu' ];
			
			for (var i=0; i<lang.length;i++){
				html.push('<option>' + lang[i] + '</option>');
			}
			
			$('#language').append(html);
						
			 var html = [];
			
			   
		}
	 
	 
		function getSymbol(id){ 
			$.ajax({
				url: "{!! url('nomination/get-symbol') !!}",
				type: 'GET',
				dataType: 'json',        
				success: function(json) { 
				  $('#sym1').html('');
				    if(id==1){
					  
					  var s2=$("#suggest_symbol_2").val();
					  var s3=$("#suggest_symbol_3").val();
					  	
					  for(i=0; i<json.length; i++)
					  {	  
						 var datat = json[i];
						 if(s2!=datat && s3!=datat){
						   $('#sym1').append('<option value="'+datat+'">');
						 }
					  }
					  json=[];
					}
					if(id==2){ 
					  var s1=$("#suggest_symbol_1").val();
					  var s3=$("#suggest_symbol_3").val();	
					
					  $('#sym2').html('');
					  for(i=0; i<json.length; i++)
					  {	  
						 var datat2 = json[i];
						  if(s1!=datat2 && s3!=datat2){
						    $('#sym2').append('<option value="'+datat2+'">');
						  }
					  }
					  json=[];
					}
					if(id==3){
					  var s1=$("#suggest_symbol_1").val();
					  var s2=$("#suggest_symbol_2").val();		
					  $('#sym3').html('');	
					  for(i=0; i<json.length; i++)
					  {	  
						 var datat3 = json[i];
						  if(s1!=datat3 && s2!=datat3){
						   $('#sym3').append('<option value="'+datat3+'">');
						  }  
					  }
					  json=[];
					}
				},
				error: function(data) {
				  console.log(data);	
				  var errors = data.responseJSON;
				  console.log(errors);
			}
		});	
		}
	 
	 
	 
		$( document ).ready(function() {
						$( ".alphaonly" ).keypress(function(e) { 
							 var charCode = e.keyCode;
							if(charCode!=32){
							 if (charCode > 31 && (charCode < 65 || charCode > 90) && (charCode < 97 || charCode > 122)) {
								return false;
							 }
							}
							return true;	
						});
					});
		</script>
	 
	<script type="text/javascript">
		function chkParty(){
		 var pp = $("#setup").val();	
		 if(pp==''){
			alert("Please select party");
			return false;	
		 }	
		}
	
	  function national(){
		 $("#national").show(); 
		 $("#state").hide(); 
		 $("#state").prop("disabled", true);
		 $("#national").prop("disabled", false);
	  }	
	   function state(){
		 $("#national").hide(); 
		 $("#state").show(); 
		 $("#state").prop("disabled", false);
		 $("#national").prop("disabled", true);
	  }	
		
		
	  function shohide(){
		
		if($('input[name="not_applicable"]:checked').length > 0 ){
			$("#TTT").hide();  
			$("#one").prop("disabled", false);
			$("#two").prop("disabled", false);
			$("#three").prop("disabled", false);
		} else {
			$("#TTT").show();  
			$("#one").prop("disabled", true);
			$("#two").prop("disabled", true);
			$("#three").prop("disabled", true);
		}
	  } 
	
	  $(function(){
		  $('.pParty').on('click',function(){
			 $('.selectParty').fadeIn(500); 
		  });
		  
	  });
	  
	  function shows(nu){ 
		
		if(nu==1){
		 $("#setup").show();	
		 $("#setupDiv").show();	
		 $("#setup_independent").hide();	
		 $("#setup_independent").prop("disabled", true);	
		 $("#cstpid").prop("disabled", true);	
		}  
		if(nu==2){
		 $("#setup").hide();	
		 $("#setupDiv").hide();	
		 $("#setup_independent").show();	
		 $("#setup_independent").prop("disabled", false);	
		 $("#cstpid").prop("disabled", false);	
		}  
	  }
	  
      $(document).ready(function(){  
       if($('#breadcrumb').length){
         var breadcrumb = '';
         $.each({!! json_encode($breadcrumbs) !!},function(index, object){
          breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
        });
         $('#breadcrumb').html(breadcrumb);
       }

       $('#part3_date').datepicker({
        dateFormat: 'dd-mm-yy'
       });

     });
   </script>
   @endsection