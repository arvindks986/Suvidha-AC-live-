@extends('layouts.theme')
  @section('title', 'Nomination')
  @section('content')
  
  <!-- Keyboar for All Language -->
	
 


  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
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
  


  <link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
  <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">   
  
  <link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
  <link rel="stylesheet" type="text/css" href="{{ asset('appoinment/css/keyboard.css')}}">
  
  
  <main role="main" class="inner cover mb-3">
   
 <form name="myform" enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('/nomination/apply-nomination-step-1/post') }}" autocomplete='off' enctype="x-www-urlencoded">
{{ csrf_field() }}
   <section >
  <div class="container">
        @if (session('flash-message'))
        <div class="alert alert-success mt-4"> {{session('flash-message') }}</div>
        @endif
     </div> 
</section>
<div class="container">

 <!--<div class="step-wrap mt-4">
     <ul>
       <li class="step-current"><b>&#10004;</b><span>{{ __('step1.step1') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step2') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step3') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step4') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step5') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step6') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step7') }}</span></li>
     </ul>
 </div> -->
</div>
</div>
<section>
  <div class="container p-0">
    <div class="row">

      <div class="col-md-12">
        <div class="card">
         <div class="card-header d-flex align-items-center">
           <h4>{{ __('step1.Candidate_Personal_Details') }}</h4>
         </div>
         <div class="card-body">
           <div class="row">
             <div class="col">
              <div class="form-group row">
			   <label class="col-sm-1">{{ __('step1.epic') }}<sup>*</sup></label>
			   <div class="col-sm-8"> <span style="color:red;font-size:14px;">{{ __('step1.epic_text') }} </span>
			  <div class="input-group epic_no_div" id="epic_no_div">
				<input type="text" name="epic_no" id="epic_no" class="form-control" value="{{$epic_no}}" placeholder="{{ __('step1.epic') }}" maxlength="20"  onblur='return checkemail();'/>
				<div class="input-group-append"><button class="btn btn-success" type="button" id="epic_no_search">{{ __('step1.Search') }}</button></div>
			  </div>
			  
				<img id="load" style="padding-top: 15px;display:none;" src="<?php echo url('/'); ?>/img/nom-load.gif" />
				<img id="load1" style="padding-top: 15px; display: none; height: 70px; margin-left: -57px;" src="<?php echo url('/'); ?>/img/gif.gif" />
			  <?php //echo "<pre>"; print_r($errors); ?>
			  @if ($errors->has('epic_no'))
				 <span class="error">{{ $errors->first('epic_no') }}</span>
				 @endif
				</div> 
				<div class="col-sm-3 animate-wrap epic_wrap" style="display:<?php echo (!empty($epic_no))?'none':'block';?>">
					<div class="animate-help-text dir-lft">
				    <div class="help-text">{{ __('messages.arepic') }}</div>
					<div class="animate-icon">
					      <div class="box bounce-2"><i class="fa fa-hand-o-left" aria-hidden="true"></i></div>
					</div>
				  </div>
				</div>
			</div>
		
		
			
    <div class="line"></div>


               <div class="form-group row">
                <label class="col-sm-3">{{ __('step1.Name') }}<sup>*</sup></label>
                <div class="col">
                 <label>{{ __('step1.English') }} <sup>*</sup></label>

                 <input type="text" name="name" id="name" class="form-control alphanumeric" value="{{$name}}" onkeyup="this.value = this.value.toUpperCase();" > 

                  <!-- <input type="text" name="name" id="name" class="form-control alphaonly alphanumeric" value="{{$name}}"  >  -->

                 @if ($errors->has('name'))
                 <span class="error">{{ $errors->first('name') }}</span>
                 @endif 
               </div>  
               <div class="col">
                 <label>{{ __('step1.Hindi') }} </label> <!-- data-trigger-keyboard="true" -->
                 <input type="text" name="hname" id="hname" class="form-control alphanumeric" value="{{$hname}}"  data-id="hname" data-error=".errorTxt11111" onclick="return langKeyboardFunc('hname');"> 

                 @if ($errors->has('hname'))
                 <span class="error">{{ $errors->first('hname') }}</span>
                 @endif 
               </div>
			   
			   
               <div class="col">
                <label>{{ __('step1.Vernacular') }}  <sup>*</sup></label>
                <input type="text" name="vname"  id="vname" class="form-control alphaonly" value="{{$vname}}" data-id="vname" data-error=".errorTxt11111" onclick="return langKeyboardFunc('vname');"> 
                @if ($errors->has('vname'))
                <span class="error">{{ $errors->first('vname') }}</span>
                @endif 
              </div>
            </div>
			
            <div class="line"></div>

            <div class="form-group row">
                <label class="col-sm-3">{{ __('step1.father_husband') }} <sup>*</sup></label>
                <div class="col">
                 <label>{{ __('step1.English') }} <sup>*</sup></label>

                 <input type="text" name="father_name" id="father_name" class="form-control alphanumeric" value="{{$father_name}}"  onkeyup="translationInOtherLang('father_name','father_hname')" > 

                 @if ($errors->has('father_name'))
                 <span class="error">{{ $errors->first('father_name') }}</span>
                 @endif 
               </div>  
               <div class="col">
                 <label>{{ __('step1.Hindi') }}  </label>
                 <input type="text" name="father_hname" id="father_hname" class="form-control alphanumeric" value="{{$father_hname}}" data-id="father_hname" data-error=".errorTxt11111" onclick="return langKeyboardFunc('father_hname');"> 

                 @if ($errors->has('father_hname'))
                 <span class="error">{{ $errors->first('father_hname') }}</span>
                 @endif 
               </div>
               <div class="col">
                <label>{{ __('step1.Vernacular') }}   <sup>*</sup></label>
                <input type="text" name="father_vname" id="father_vname" class="form-control" value="{{$father_vname}}" data-id="father_vname" data-error=".errorTxt11111" onclick="return langKeyboardFunc('father_vname');">
                @if ($errors->has('father_vname'))
                <span class="error">{{ $errors->first('father_vname') }}</span>
                @endif 
              </div>
            </div>

            <div class="line"></div>

            <div class="form-group row">
             <label class="col-sm-3">{{ __('step1.Candidate_Alias_Name') }}</label>
             <div class="col">
              <input type="text" name="alias_name" id="alias_name"  class="form-control alphanumeric" value="{{$alias_name}}" placeholder="{{ __('step1.alias_en') }}" onkeyup="translationInOtherLang('alias_name','alias_hname')"> 
              @if ($errors->has('alias_name'))
              <span class="error">{{ $errors->first('alias_name') }}</span>
              @endif 

            </div>  
            <div class="col">
              <input type="text" name="alias_hname" id="alias_hname" class="form-control alphanumeric" value="{{$alias_hname}}" placeholder="{{ __('step1.alias_hindi') }}"  data-id="alias_hname" data-error=".errorTxt11111" onclick="return langKeyboardFunc('alias_hname');" > 
              @if ($errors->has('alias_hname'))
              <span class="error">{{ $errors->first('alias_hname') }}</span>
              @endif 
            </div>
			
			 <div class="col">
                <input type="text" name="alias_vname" id="alias_vname" class="form-control" value="{{$alias_vname}}" placeholder="{{ __('messages.anameVer') }}" data-id="alias_vname" data-error=".errorTxt11111" onclick="return langKeyboardFunc('alias_vname');"> 
                @if ($errors->has('alias_vname'))
                <span class="error">{{ $errors->first('alias_vname') }}</span>
                @endif 
              </div>
			
          </div>        
       <div class="line"></div>
	   
     <div class="line"></div> 
     <div class="form-group row">
       <div class="col-sm-2"><label for="statename">{{ __('step1.State') }}<sup>*</sup></label></div>
       <div class="col">
         <div class="" style="width:100%;">
           <select name="state" class="form-control" id="state" onchange="filter_respective_district(this.value)" >
             <option value="">--{{ __('step1.select_state') }}--</option> 
             @foreach($states as $iterate_state)
               @if($state == $iterate_state['st_code'])
               <option value="{{ $iterate_state['st_code'] }}" selected="selected">{{ $iterate_state['st_name'] }}</option>
               @else 
               <option value="{{ $iterate_state['st_code'] }}"> {{ $iterate_state['st_name'] }}</option>
               @endif
             @endforeach
           </select>
           @if ($errors->has('state'))
           <span class="error">{{ $errors->first('state') }}</span>
           @endif 
         </div>
       </div>  
       <div class="col-sm-2"><label for="statename">{{ __('step1.District') }} <sup>*</sup></label></div>
       <div class="col"><div class="" style="width:100%;">
         <select name="district" class="form-control" id="district" onchange="filter_respective_acs(this.value)" >
           <option value="">--{{ __('step1.select_district') }}--</option>     
         </select>
         @if ($errors->has('district'))
         <span class="error">{{ $errors->first('district') }}</span>
         @endif 
       </div>
     </div> 
   </div> 
   <div class="form-group row">
	
	
	
    <div class="col-sm-2"><label for="statename">{{ __('step1.AC') }} <sup>*</sup></label></div>
    <div class="col">
     <div class="" style="width:100%;">
       <select name="ac" class="consttype form-control" id="ac" onchange="setAC(this.value)" >
         <option value="">--{{ __('step1.select_ac') }}--</option>
       </select>
       @if ($errors->has('ac'))
       <span class="error">{{ $errors->first('ac') }}</span>
       @endif

     </div>
   </div>
   
    

   <div class="col-sm-2"> </div> 
   <div class="col"> </div>

 </div> 
 
  <div class="line"></div>
     <div class="form-group row">
		
       <label class="col-sm-2">{{ __('step1.Part') }}<sup>*</sup></label> 
       <div class="col">
         <input type="number" name="part_no" class="form-control" value="{{$part_no}}" placeholder="{{ __('step1.Part') }}" > 
         @if ($errors->has('part_no'))
         <span class="error">{{ $errors->first('part_no') }}</span>
         @endif
       </div>  
       <label class="col-sm-2">{{ __('step1.Serial') }}<sup>*</sup></label>
       <div class="col">
         <input type="number" name="serial_no" class="form-control" value="{{$serial_no}}" placeholder="{{ __('step1.Serial') }}" > 
         @if ($errors->has('serial_no'))
         <span class="error">{{ $errors->first('serial_no') }}</span>
         @endif
       </div> 
     </div>
	       <div class="line"></div>
       <div class="form-group row" style="padding: 5px;"> 
         <label class="col-sm-2">{{ __('step1.Email') }}</label> 
         <div class="col"> 
			<!-- <span style="color: #e83e8c; position: absolute; margin-top: -19px; font-size: 12px; padding: 1px;">{{ __('step1.emailRequired') }}
				  @if($is_verified_email_otp!=1)	
			        <span style="color: red; margin-left: 33px;">**{{ __('step1.NotVerified') }}</span>
				  @endif
			</span> -->
			
           <input type="email" name="email" id="email" class="form-control" value="{{$email}}" placeholder="{{ __('step1.Email') }}" onblur="return checkEmail(this.value);" autocomplete="off" required> 
		   @if($is_verified_email_otp!=1)
		    <!-- <button class="btn btn-success" type="button" id="otpEmail" onclick="showEmailVerification();" style="float: right; margin-top: 7px;">{{ __('step1.get_otp') }}</button> -->
			@else
		    <!-- <span id="tick" style="float: right; margin-top: 7px;font-size: 11px;">&nbsp; <img src="{{ asset('appoinment/tick.jpg') }}" height="30" width="30"></img> {{ __('step1.email_verified') }}</span> -->
		  @endif 
		    <span id="tickemail" style="float: right; margin-top: 7px;font-size: 11px;display:none;">&nbsp; <img src="{{ asset('appoinment/tick.jpg') }}" height="30" width="30"></img> {{ __('step1.email_verified') }}</span>
			
			
		    <span style="text-align:left;display:none;" id="loader">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="40" width="40"></img>{{ __('step1.please_wait') }}
			</span>
			
			
		    <span id="email_exist" style="color:red;display:none;">{{ __('step1.email_error_unique') }}</span>
			
			 <!-- <button class="btn btn-success" type="button" id="otpEmail2" onclick="showEmailVerification();" style="float: right; margin-top: 7px;display:none;">{{ __('step1.get_otp') }}</button> -->
		    
		   <div class="input-group-append" style="margin-top: 60px;margin-right: 8px;display:none;" id="EmailDiv"> 
		   
		   <input type="password" name="verify_email"  id="verify_email" class="form-control" value="" placeholder="{{ __('step1.Emai_verify_otp') }}" style="margin-top: 8px;margin-right: 8px;"> 		   
		   <button class="btn btn-success" type="button" id="verifyEmail" style="height: 34px; margin-top: 9px;" onclick="return verifyOtpEmail();">{{ __('step1.verify') }}</button>		   
		   </div> 
		    <span id="otp_verification_message_email" style="color:red;display:none;">{{ __('step1.Emai_verify_otp') }}</span>
		   <span id="otp_blank_email" style="color:red;display:none;">{{ __('step1.otp_blank') }}</span>
		   <span id="verifiedmessage_email" style="color:green;display:none;"></span>
		   
		 
		   
           @if ($errors->has('email'))
           <span class="error">{{ $errors->first('email') }}</span>
           @endif 
         </div>  
		 
		
		 <label class="col-sm-2">{{ __('step1.mobile') }}*</label>
         <div class="col"> 
           <input type="text" name="mobile" id="mobile" class="form-control" value="{{$mobile}}" placeholder="{{ __('step1.mobile') }}" onblur="return checkMobile(this.value);" autocomplete="off" maxlength="10" pattern="\d{10}" />  
			
			@if($is_verified_mobile_otp!=1)
			@if($mobile_from_user_login!=$mobile)  
            <button class="btn btn-success" type="button" id="otpMobile" onclick="return showMobileVerification();" style="float: right; margin-top: 7px;">{{ __('step1.get_otp') }}
		    </button>
			@else
			<span id="tickmobile" style="float: right; margin-top: 7px;font-size: 11px;"> &nbsp; <img src="{{ asset('appoinment/tick.jpg') }}" height="30" width="30"></img>{{ __('step1.mobile_verified') }} </span>
		  @endif
			@else
			<span id="tickmobile" style="float: right; margin-top: 7px;font-size: 11px;"> &nbsp; <img src="{{ asset('appoinment/tick.jpg') }}" height="30" width="30"></img>{{ __('step1.mobile_verified') }} </span>
		 @endif  
		 <span id="specialcase" style="float: right; margin-top: 7px;font-size: 11px;display:none;"> &nbsp; <img src="{{ asset('appoinment/tick.jpg') }}" height="30" width="30"></img>{{ __('step1.mobile_verified') }} </span>
		 
		 
		 
		 
		 
		 
		 <span style="text-align:left;display:none;" id="loader2">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="40" width="40"></img>{{ __('step1.please_wait') }}
			</span>
		
		<button class="btn btn-success" type="button" id="otpMobile2" onclick="return showMobileVerification();" style="float: right; margin-top: 8px; display:none;">{{ __('step1.get_otp') }}</button>
		
		 
		  
		  
		   <span id="mobile_exist" style="color:red;display:none;">{{ __('step1.mobile_error_unique') }}</span>
		  
		   <div class="input-group-append" style="margin-top:60px;margin-right: 8px;display:none;" id="MobileDiv"> 
		   
		   <input type="password" name="verify_mobile"  id="verify_mobile" class="form-control" value="" placeholder="{{ __('step1.Mobile_verify_otp') }}" style="margin-top: 8px;margin-right: 8px;"> 
		   
		   <button class="btn btn-success" type="button" id="verifyMobile" style="height: 34px; margin-top: 9px;" onclick="verifyOtp();">{{ __('step1.verify') }}</button>
		   
		   </div>
		   
		   
		   <span id="otp_verification_message" style="color:red;display:none;">{{ __('step1.Mobile_verify_otp') }}</span>
		   <span id="otp_blank" style="color:red;display:none;">{{ __('step1.otp_blank') }}</span>
		   <span id="verifiedmessage" style="color:green;display:none;"></span>
		   
		   
			 @if ($errors->has('mobile'))
           <span class="error">{{ $errors->first('mobile') }}</span>
           @endif 
           <div class="merrormsg errormsg errorred"></div> 
         </div>
       </div>
       <div class="form-group row">
         <label class="col-sm-2">{{ __('step1.Gender') }} <sup>*</sup></label>

         <div class="col">
          <div class="custom-control custom-radio">
            @if("female" == $gender)
            <input type="radio" class="custom-control-input" id="customControlValidation2" name="gender" value="female" checked="checked">  
            @else
            <input type="radio" class="custom-control-input" id="customControlValidation2" name="gender" value="female">  
            @endif
            <label class="custom-control-label" for="customControlValidation2">{{ __('step1.Female') }}</label>
         </div>
         <div class="custom-control custom-radio ">
           @if("male" == $gender)
            <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" checked="checked">  
            @else
            <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male">  
            @endif
            <label class="custom-control-label" for="customControlValidation3">{{ __('step1.Male') }}</label>
         </div>
         <div class="custom-control custom-radio mb-3">
            @if("third" == $gender)
            <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" checked="checked">  
            @else
            <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third">  
            @endif
            <label class="custom-control-label" for="customControlValidation4">{{ __('step1.Others') }}</label>
         </div>
          @if ($errors->has('gender'))
          <span class="error">{{ $errors->first('gender') }}</span>
          @endif 
       </div> 

       <label class="col-sm-2">{{ __('step1.pan') }} </label>
       <div class="col">
         <input type="text" name="pan_number" id="pan_number"  class="form-control" value="{{$pan_number}}" placeholder="{{ __('step1.pan') }}" maxlength="10" onchange="return checkPan(this.value);"> 
		 <span id="panError" style="color: red; font-size: 12px;"></span>
         @if ($errors->has('pan_number'))
         <span class="error">{{ $errors->first('pan_number') }}</span>
         @endif
       </div>
     </div>
     <div class="line"></div>
     <div class="form-group row">

       <label class="col-sm-2">{{ __('step1.Age') }}<sup>*</sup></label>
       <div class="col">
         <input type="number" name="age" class="form-control" value="{{$age}}" placeholder="{{ __('step1.Age') }}" min="25" max="120"> 
         @if ($errors->has('age'))
         <span class="error">{{ $errors->first('age') }}</span>
         @endif
       </div>   
         <label class="col-sm-2">{{ __('step1.Category') }}<sup>*</sup></label> 
         <div class="col"> 
           <select name="category" class="form-control">
             <option value="">--{{ __('step1.select_category') }}--</option>
             @foreach($categories as $iterate_category)
             @if($category == $iterate_category['id'])
             <option  id="{{$iterate_category['id']}}" value="{{$iterate_category['id']}}" selected="selected">{{$iterate_category['name']}}</option>
             @else
             <option id="{{$iterate_category['id']}}" value="{{$iterate_category['id']}}">{{$iterate_category['name']}}</option>
             @endif
             @endforeach
           </select>
           @if ($errors->has('category'))
           <span class="error">{{ $errors->first('category') }}</span>
           @endif
         </div> 
     </div> 
    


     <div class="line"></div>    

     <div class="form-group row">
       <label class="col-sm-2">{{ __('step1.Address') }}<sup></sup></label>
       <div class="col">
        <label>{{ __('step1.English') }}<sup>*</sup></label>
         <textarea name="address" id="address" class="form-control address" placeholder="{{ __('step1.address2') }}" onkeyup="translationInOtherLang('address','haddress')">{{$address}}</textarea>
         @if ($errors->has('address'))
         <span class="error">{{ $errors->first('address') }}</span>
         @endif


       </div>  
       <div class="col">
        <label>{{ __('step1.Hindi') }}<sup>*</sup></label>
         <textarea name="haddress" id="haddress" class="form-control address"  placeholder="{{ __('step1.address1') }}"  data-id="haddress" data-error=".errorTxt11111" onclick="return langKeyboardFunc('haddress');" >{{$haddress}}</textarea> 
         @if ($errors->has('haddress'))
         <span class="error">{{ $errors->first('haddress') }}</span>
         @endif
       </div> 
       <div class="col">
        <label>{{ __('step1.Vernacular') }}<sup>*</sup></label>
         <textarea name="vaddress"  id="vaddress" class="form-control address" placeholder="{{ __('step1.address3') }}" data-id="vaddress" data-error=".errorTxt11111" onclick="return langKeyboardFunc('vaddress');">{{$vaddress}}</textarea> 
         @if ($errors->has('vaddress'))
         <span class="error">{{ $errors->first('vaddress') }}</span>
         @endif
       </div> 
     </div>
 
 
	<!--<input type="hidden" name="state" id="st" value="{{ $state }}">
	<input type="hidden" name="district" id="dist" value="{{$district}}">
	<input type="hidden" name="ac" id="aca" value="{{$ac}}"> -->

</div>
</div>
</div>


<!--<div class="card-footer">
	 <div class="form-group row float-right">       
  <div class="col">
    <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button>
   <button type="submit" id="candnomination" class="btn btn-primary">Save & Next</button>   
 </div>
</div>
</div>-->

<div class="card-footer">
        <div class="row align-items-center">
          <div class="col-sm-12 col-12">
            <div class="apt-btn text-right float-right"> 			
			<a href="<?php echo url('/'); ?>/dashboard-nomination-new" class="btn btn-lg font-big dark-pink-btn">{{ __('step1.Cancel') }}</a> 		
			&nbsp;
			&nbsp;
			&nbsp;	
			<button type="button" id="candnomination"  class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="valProf();">{{ __('messages.SAVE') }}</button>
	</div>          
  </div>
</div>
</div> 
<input type="hidden" id="chkccnd">



</div>
</div>
</div>
</div>    
</section>
</form>
</main>

		<!--<div class="row">
			<div class="col-sm-6 mx-auto">
				<div class="searchBox mt-4">
					<input class="form-control alphanumeric" id="epic_no_diff" type="search" maxlength="15" placeholder="Search by EPCI No.">
					<button class="btn btn-success" type="button" onclick="return getDetailsByEPIC();">Search</button>
				</div>
				<div id="error_epic"></div>
			</div>
	  </div>	-->
			
@endsection
@section('script')


<script type="text/javascript">
$( document ).ready(function() {

    
  $('#epic_no').keyup(function() {
  if (this.value.match(/[^a-zA-Z0-9/]/g)) {
    this.value = this.value.replace(/[^a-zA-Z0-9/]/g, '');
  } 
});



				$("#epic_no").blur(function(){
					var epic_val = $(this).val();
					if(epic_val==''){
						$(".epic_wrap").show();
					}else{
						$(".epic_wrap").hide();
					}
				});
	
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
<script>
   //"url": "https://transservice.ecinet.in/api/Transliteration/GetSuggestions?transliteration=NAME&itext="+Name+"&locale=hi_in",
	//Lang_Translate Section start 
    function translationInOtherLang(NameForTraslate, Name_v2) {
       var Name = $('#' + NameForTraslate).val();
        $('#' + Name_v2).val("");
          if($.trim(Name) == "")
      {
           $('#'+Name_v2).val("");  
      }
      else{
      // alert(Name_v2);
        var settings = {
        
          "url": "https://gateway-officials.eci.gov.in/CDAC-EnhanceTransliterationAPI/Transliteration.aspx?transliteration=NAME&itext="+Name+"&locale=hi_in&transRev=false",
         
         "method": "GET",
         "timeout": 0,
         "headers": {
           "Content-Type": "application/x-www-form-urlencoded",
           //"Cookie": "ARRAffinity=cfe5d9f8418b77d7c64de5d453223f22283962480cd38c2ee67dd602c4bd7233"
         },
       };
     var text = "";
        $.ajax(settings).done(function (response) {
             //alert(response);
                    var r = response.split(';');
                    var x = r.length;
                    for (i = 0; i < x; i++) {
                        text += r[i].split('^')[0];
                        text += ' ';
                    }               
             $('#'+Name_v2).val(text);
              $('#'+Name_v2).parents(".field").find(".highlights").html("Your entered text has been translated into regional language, but you can change it by clicking the regional language text if it is not correct.").show()
          console.log(response);
        });
        }  
    }
	function checkPan(pan){
			var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
			if(regpan.test(pan)){ 
				$("#panError").html("");
			} else {
			    $("#panError").html("{{__('messages.pancardError')}}");;
			}
	}
	
	function checkemail(){ 
		var epic = $("#epic_no").val();
		/*
		 if(epic!="<?php echo $epic_no; ?>") { 
			 $("#st").val("");
			 $("#dist").val("");
			 $("#aca").val("");
			 $("#district").val("");
			 $("#state").val("");
			 $("#ac").val("");
		 } else { 
			 $("#st").val("{{$state}}");
			 $("#dist").val("{{$district}}");
			 $("#aca").val("{{$ac}}");
			 $("#district").val("{{$district}}");
			 $("#state").val("{{$state}}");
			 $("#ac").val("{{$ac}}");
		 } */
	}
	
	function setAC(id){ 
	  //$("#aca").val(id);	
	}
	function checkEmail(email){ 
			if(email==''){
			 $("#email_exist").show().html("{{__('step1.email_error') }} ");
			 return false;
			}
			if(email!=''){
			var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;	
				if (!testEmail.test(email)){	
				 $("#email_exist").show().html("{{__('step1.email_error') }} ");
				 return false;
				} else {
			     $("#email_exist").hide();
				}
			}
			
		 if("<?php echo $email; ?>"!=''){	
			if(email!="<?php echo $email; ?>" ){
			  $("#otpEmail2").show();
			  $("#otpEmail").hide();
			} else {
			  $("#otpEmail2").hide();
			}		
		 }	
		  if("<?php echo $email; ?>"!=''){	
			if(email=="<?php echo $email; ?>" ){
			  $("#tick").show();
			} else {
			  $("#tick").hide();
			}		
		 }	
	}
	
	function checkMobile(mobile){ 
    $("#mobile_exist").hide();	
    $("#mobile_exist").html("");	
	
	if("<?php echo $mobile_from_user_login; ?>" == mobile)	{ 
			 $("#otpMobile").hide();
			 $("#otpMobile2").hide();
		 }	 
		
		 if("<?php echo $mobile; ?>" ==mobile)	{
			 $("#otpMobile").hide();
			 $("#otpMobile2").hide();
		 }	
	
	 if(mobile.length==10){	
		if(mobile!="<?php echo $mobile_from_user_login; ?>" ) { 
			if(mobile!="<?php echo $mobile; ?>" ) { 
			   $("#otpMobile2").show();	
			   $("#otpMobile").hide();	
			} else {
				 $("#otpMobile2").hide();
				 $("#otpMobile").show();
			}
		} else {
		  $("#otpMobile2").hide();
		  $("#otpMobile").hide();
		}
	 }	
	 
	    if("<?php echo $mobile_from_user_login; ?>"!=''){	
			if(mobile=="<?php echo $mobile; ?>" ){
			  $("#specialcase").show();
			  $("#mobile_exist").hide();
			  $("#otpMobile").hide();
			} else {
			  $("#specialcase").hide();
			}		
		}
	    if("<?php echo $mobile_from_user_login; ?>"!=''){	
			if(mobile=="<?php echo $mobile_from_user_login; ?>" ){ 
			  $("#specialcase").show();
			  $("#mobile_exist").hide();
			  $("#otpMobile").hide();
			} else {
			  $("#specialcase").hide();
			  $("#tickmobile").hide();
			}		
		}	   	
		if(mobile=="<?php echo $mobile_from_user_login; ?>" || mobile=="<?php echo $mobile; ?>" ){ 
			  $("#specialcase").show();
			  $("#mobile_exist").hide();
			  $("#tickmobile").hide();
			  $("#otpMobile").hide();
			  $("#otpMobile2").hide();
		} else {
			  $("#specialcase").hide();
			  $("#tickmobile").hide();
			}
	}
	
	function enc(str) {
    var encoded = "";
    for (i=0; i<str.length;i++) {
        var a = str.charCodeAt(i);
        var b = a ^ 123;    // bitwise XOR with any number, e.g. 123
        encoded = encoded+String.fromCharCode(b);
    }
    return encoded;
	}
	
	function valProf(){  
		var epic=$("#epic_no").val();
		var mobile=$("#mobile").val();
		var email=$("#email").val();
		if(epic.length<10){ 
		alert("{{__('step1.Epic_error') }} ");	
		return false;	
		}
		
	
		
		var pan_number = $("#pan_number").val();
		
		var cnd = 'ppp';
		// if(cnd!='test') { 
		// 	var encoded = enc(pan_number);
		// 	$("#pan_number").val(encoded);
		// 	cnd = $("#chkccnd").val();
		// }
		
		$("#chkccnd").val('test');
		//$("#candnomination").prop('disabled', true);
		
		

		$.ajax({
		url: "{!! url('nomination/check-email-mobile-onsubmit') !!}",
		type: 'GET',
		data: 'mobile='+encryptData(mobile)+'&email='+encryptData(email),
		dataType: 'html',     
		success: function(json) { 
		if(json!=0){
		  //alert(json);
		} else {
			document.myform.submit();
		}
		 			  
		},
		error: function(data) { 
		 console.log(data);	
		 var errors = data.responseJSON;
		}
	  });
			  
			  
		 //document.myform.submit();	
		
	}
	

	
	function verifyOtpEmail(){		  
			 var otp = $("#verify_email").val();;
			  if(otp==''){
				$("#otp_verification_message_email").hide();  
				$("#otp_blank_email").show();  
			  } else {
				$("#otp_blank_email").hide();    
			  }		  

         var otp = btoa(otp);
		      $.ajax({
				url: "{!! url('nomination/verifyOTPEmail') !!}",
				type: 'GET',
				data: 'otp='+otp,
				dataType: 'html',     
				success: function(json) {
				 if(json==1){ 
					 $("#otp_verification_message_email").hide();		
					 $("#tickemail").show();		
					 $("#EmailDiv").hide();	
					 $("#otpEmail").hide();	
					 $("#otpEmail2").hide();	
				 } else {
					$("#otp_verification_message_email").show().html("<?php echo __('step1.invalid_otp'); ?>");		 
				 }
				 
				 
				},
				error: function(data) { 
				 console.log(data);	
				  var errors = data.responseJSON;
				}
			  });
	}
	
	function verifyOtp(){		  
		  var otp = $("#verify_mobile").val();;
			  if(otp==''){
				$("#otp_verification_message").hide();  
				$("#otp_blank").show();  
			  } else {
				$("#otp_blank").hide();    
			  }		  
		      $.ajax({
				url: "{!! url('nomination/verifyOTP') !!}",
				type: 'GET',
				data: 'otp='+otp,
				dataType: 'html',     
				success: function(json) { 
				if(json==1){
				 $("#specialcase").show();	
				 $("#otp_verification_message").hide();		
				 $("#MobileDiv").hide();		
				 $("#otpMobile2").hide();
				 $("#otpMobile").hide();
				} else {
				$("#otp_verification_message").show().html("<?php echo __('step1.invalid_otp'); ?>");			
				}	
				},
				error: function(data) { 
				 console.log(data);	
				  var errors = data.responseJSON;
				}
			  });
	}
	
	function showEmailVerification(){ 
			var email = $("#email").val();
		
			$("#loader").show();
			$("#otp_verification_message_email").hide();
			if(email==''){
			 $("#email_exist").show().html("{{__('step1.email_error') }} ");
			 $("#EmailDiv").hide();
			 $("#loader").hide();
			 $("#otp_verification_message_email").hide(); 
			 return false;
			}
			if(email!=''){
			var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;	
				if (!testEmail.test(email)){	
				 $("#email_exist").show().html("{{__('step1.email_error') }} ");
				 $("#EmailDiv").hide();
				 $("#otp_verification_message_email").hide(); 
				 return false;
				}
			}

      var email =btoa(email);
		 
		    $.ajax({
				url: "{!! url('nomination/send-otp-on-email') !!}",
				type: 'GET',
				data: 'email='+email,
				dataType: 'json',     
				success: function(json) { 
				  if(json=='1'){
					$("#email_exist").show().html("{{__('step1.email_error_unique') }} ");
					$("#EmailDiv").hide();
					$("#otp_verification_message_email").hide();
					$("#otpEmail2").hide();
					$("#tick").hide();
				  }
				  if(json=='2'){
					$("#EmailDiv").show();
					$("#email_exist").hide();
					$("#otp_verification_message_email").show(); 
				  }
				  $("#loader").hide();
				},
				error: function(data) {
				  console.log(data);	
				  var errors = data.responseJSON;
				  console.log(errors);
				}
			  });
	}
	
	
	function showMobileVerification(mob){ mobile_exist
		var mob = $("#mobile").val();
		
		if(mob==''){
			 $("#mobile_exist").show().html("{{__('step1.mobile_error') }} ");
			 $("#MobileDiv").hide();
			 $("#otp_verification_message").hide(); 
			 return false;
		 }
		 if(mob.length!=10){
			 $("#mobile_exist").show().html("{{__('step1.mobile_error') }} ");
			 $("#MobileDiv").hide();
			 $("#otp_verification_message").hide(); 
			 return false;
		 }
		 	
		 
	    if("<?php echo $mobile_from_user_login; ?>" !=mob)	{
		    $("#loader2").show();
		    $.ajax({
				url: "{!! url('nomination/send-otp-on-mobile') !!}",
				type: 'GET',
				data: 'mobile='+mob,
				dataType: 'json',     
				success: function(json) { 
				  if(json=='1'){
					$("#mobile_exist").show().html("{{__('step1.mobile_error_unique') }} ");
					$("#otp_verification_message").hide();
				  }
				  if(json=='2'){
					$("#mobile_exist").hide();
					$("#MobileDiv").show();
					$("#otp_verification_message").show(); 
				  }
				  $("#loader2").hide();
				},
				error: function(data) {
				  console.log(data);	
				  var errors = data.responseJSON;
				  console.log(errors);
				}
			  });
	    }
	}
    function getDetailsByEPIC(){
    var epic = $("#epic_no_diff").val();
    if(epic==""){
        $("#error_epic").html('<label class="text-danger">Please enter the EPIC no.</label>');
        return false;
    }
    $.ajax({
        url: "{!! url('epc-new') !!}",
        type: 'GET',
        data: 'epic_no='+$('#epic_no_diff').val(),
        dataType: 'json',
        beforeSend: function() {
            $('#overlay').show();
        },
        success: function(response) {
          
		console.log(response);	
        if(response.status == 200 && response.error == false){
            //$("#candidate_name").val(response.result.name);
			$("input[name=name]").val(response.result.name);
			$("input[name=vname]").val(response.result.rln_name_v1);
			$("input[name=father_vname]").val(json['basic'].rln_name);
            $("#name_on_epic").val(response.result.name);
            $("#st_name_by_epic").val(response.result.state);
            $("#st_code_by_epic").val(response.result.st_code);
            $("#dist_name_by_epic").val(response.result.district);
            $("#dist_no_by_epic").val(response.result.district_code);
            $("#ac_name_by_epic").val(response.result.ac_name);
            $("#ac_no_by_epic").val(response.result.ac_no);
            $("#part_number_by_epic").val(response.result.part_no);
            $("#serial_no_by_epic").val(response.result.slno_inpart);
        } else if(response.status == 401 && response.result=="") {
            $("#error_epic").html('<label class="text-danger">'+response.msg+'</label>');
          } else {
            alert('Please try again, Something wrong with EPIC number');
          }
        },complete: function() {
          $('#overlay').hide();
        },
		error: function(data) {
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });
}


 function readURL(input) {
   if (input.files && input.files[0]) {
     var reader = new FileReader();
     reader.onload = function(e) {
      $('#imagePreview').css('background-image', 'url('+e.target.result +')');
      $('#imagePreview').hide();
      $('#imagePreview').fadeIn(650);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#imageUpload").change(function() {
 readURL(this);
});


$(document).ready(function(){ 
  //keyboard init
	
   
  $('#dob').datepicker({ 
    dateFormat: 'yy-mm-dd',
    yearRange: '1910:<?php echo date('Y')-18; ?>',
    changeMonth: true,
    changeYear: true
  });

 if($('#breadcrumb').length){
   var breadcrumb = '';
   $.each({!! json_encode($breadcrumbs) !!},function(index, object){
    breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
  });
   $('#breadcrumb').html(breadcrumb);
 }
});

function filter_respective_district(id){ 




    if(id=='S21')
    {

      $('#BL').show();
    }else{
      $('#BL').hide();
    }


  //$("#st").val(id);	
  $("#ac").empty();
  //$("#dist").val("");	
  html = '';
  html += "<option value=''>{{ __('step1.select_district') }} </option>";
  var districts = <?php echo json_encode($districts); ?>;
  var district = "<?php echo $district ?>";
  $.each(districts, function(index, object){
    if(object.st_code == id){
      if(object.district_no == district){
        html += "<option value='"+object.district_no+"' selected='selected'>"+object.district_name+"</option>";
      }else{
        html += "<option value='"+object.district_no+"'>"+object.district_name+"</option>";
      }
    }
  });
  $("#district").empty().append(html);
  if(district==''){
    $("#district").val($("#district option:first").val());
  }
}

function filter_respective_acs(id){ 
 
  //$("#dist").val(id);
  $("#ac").val("");	  
  html = '';
  html += "<option value=''>{{ __('step1.select_ac') }} </option>";
  var acs = <?php echo json_encode($acs); ?>;
  var ac = "<?php echo $ac ?>";
  var district = $('#district').val();
  var state = $('#state').val();
 
  
  $.each(acs, function(index, object){ 
    if(object.st_code == state && object.district_no == district){
      if(object.ac_no == ac){ 
        html += "<option value='"+object.ac_no+"' selected='selected'>"+object.ac_name+"</option>";
      }else{
        html += "<option value='"+object.ac_no+"'>"+object.ac_name+"</option>";
      }
    }
  });
  $("#ac").empty().append(html);
  if(ac == ''){
    $("#ac").val($("#ac option:first").val());
  }
}

$(document).ready(function(e){
  filter_respective_district("<?php echo $state ?>");
  filter_respective_acs("<?php echo $district ?>");
});

$(document).ready(function(e){

  $('#epic_no_search').click(function(){
	  
	 
	  var epic=$('#epic_no').val();
	    if(epic.length<10){ 
			alert("{{__('step1.Epic_error') }} ");	
			return false;	
		}
	  
	  
	  $("#load").show();	

      $.ajax({
        url: "{!! url('search-by-epic-cdac-new') !!}",
        type: 'GET',
        data: 'epic_no='+$('#epic_no').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
         // $('#epic_no_search').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search').prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search').prop('disabled', false);
		  $("#load").hide();	
        },        
        success: function(json) { 
		  $("#load").hide();	

		  //console.log(json);  
          if(json['success'] == false){
            $('#epic_no').parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
			$("#load").hide();	
          }else{
			$(".epic_wrap").hide();
            $(".main_div").removeClass("display_none");
            if(json['basic'].fullName != '' && json['basic'].fullName != null){
              $("input[name=name]").val(json['basic'].fullName);
            }
			 if(json['basic'].applicantFirstNameL1 != '' && json['basic'].applicantFirstNameL1 != null){
              $("input[name=vname]").val(json['basic'].applicantFirstNameL1);
            }
            if(json['basic'].relativeFullName != '' && json['basic'].relativeFullName != null){
              $("input[name=father_name]").val(json['basic'].relativeFullName);
            }
			if(json['basic'].relationNameL1 != '' && json['basic'].relationNameL1 != null){
              $("input[name=father_vname]").val(json['basic'].relationNameL1);
            }
            if(json['basic'].age != '' && json['basic'].age != null){
              $("input[name=age]").val(json['basic'].age);
            }
			//alert(json['basic'].mob_no);
			//alert("<?php echo $mobile_from_user_login; ?>");
			//alert("<?php echo $mobile; ?>");
           /* if(json['basic'].mob_no != '' && json['basic'].mob_no != null){
              $("input[name=mobile]").val(json['basic'].mob_no);
			  
			  if(json['basic'].mob_no!="<?php echo $mobile_from_user_login; ?>" ) {
				if(json['basic'].mob_no!="<?php echo $mobile; ?>" ) { 
				    $("#otpMobile2").show();	
				    $("#otpMobile").hide();	
				    $("#vermes").hide();	
				    $("#tickmobile").hide();	
				    $("#specialcase").hide();	
				} else { 
					$("#otpMobile2").hide();	
					$("#otpMobile").hide();	
				}
				} else { 
					$("#otpMobile2").hide();	
					$("#otpMobile").hide();	
				}
            } */
			
			if(json['basic'].email_id != '' && json['basic'].email_id != null){
              $("input[name=email]").val(json['basic'].email_id);
			  if(json['basic'].email_id!="<?php echo $email; ?>" ) {
				    $("#otpEmail2").show();	
				    $("#otpEmail").hide();	
				    $("#tick").hide();	
				} else {
					$("#otpEmail").hide();	
				}
            }
			
			
			
			
            if(json['basic'].partNumber != '' && json['basic'].partNumber != null){
              $("input[name=part_no]").val(json['basic'].partNumber);
            }
            if(json['basic'].partSerialNumber != '' && json['basic'].partSerialNumber != null){
              $("input[name=serial_no]").val(json['basic'].partSerialNumber);
            }
           
            if(json['basic'].gender != '' && json['basic'].gender != null){
              if(json['basic'].gender=='M'){
                gender = 'male';
              }else if(json['basic'].gender=='F'){
                gender = 'female';
              }else{
                gender = 'Third gender';
              }
              $("input[name=gender][value=" + gender + "]").prop('checked', true);
            }
            if(json['basic'].stateCd != '' && json['basic'].stateCd != null){

              filter_respective_district(json['basic'].stateCd);
              $('#state').val(json['basic'].stateCd);
              $('#st').val(json['basic'].stateCd);
			  
            }  
			
            if(json['basic'].districtNo != '' && json['basic'].districtNo != null){  
              $('#district').val(parseInt(json['basic'].districtNo));
              $('#dist').val(parseInt(json['basic'].districtNo));
			  filter_respective_acs(parseInt(json['basic'].districtNo));
            }
            if(json['basic'].acNumber != '' && json['basic'].acNumber != null){ 
              $('#ac').val(parseInt(json['basic'].acNumber));
              $('#aca').val(parseInt(json['basic'].acNumber));
            }
			
			$("#name").trigger("keyup");
			$("#father_name").trigger("keyup");
			$("#address").trigger("keyup");
          }  
          $('.loading_spinner').remove();    
        },
        error: function(data) {
		  $("#load").hide();		
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });
    });


});


</script>



 <script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
<script type="text/javascript" src="{{ asset('appoinment/js/keyboard.js') }}"></script> 
 <script>

$(document).ready(function(){ 
$(document).keyboard({
	language: 'us,gujarati,hindi',
	specifiedFieldsOnly:true,
	enterKey: function() {
		alert('Hey there! This is a callback function example.');
	},

	keyboardPosition: 'bottom'
});  
});
</script>	

  <link rel="stylesheet" href="{{ asset('nomination/keytext/css/cdac-gist-fonts.css') }}" id="theme-stylesheet">
  <link rel="stylesheet" href="{{ asset('nomination/keytext/css/GistFloatingKeyboard.css') }}" id="theme-stylesheet">
  <script type="text/javascript" src="{{ asset('nomination/keytext/js/KeyboardScript.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('nomination/keytext/js/LanguagesArray.js') }}"></script>
  
  <script type="text/javascript" src="{{ asset('nomination/keytext/js/jquery-ui.min.js') }}"></script> 
  <script type="text/javascript" src="{{ asset('appoinment/js/crypto-js.min.js') }}"></script> 
<script>
	function langKeyboardFunc(id){ 
	 document.getElementById(id).focus();
	 closeKeyboard();
	 openKeyboard('hindi');
	 document.getElementById(id).focus();
	}

$(".address").keypress(
  function(event){
    if (event.which == '13') {
      event.preventDefault();
    }
});

 $('#pan_number').keyup(function(e) {
      if (this.value.match(/[^a-zA-Z0-9 ]/g)) {
        this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
      }
    });
	 $(document).on('input', '#address,#haddress', function () {
    let value = $(this).val();

    // allow letters, numbers, space and , . # _ -
    let cleaned = value.replace(/[^a-zA-Z0-9 ,.#_-]/g, '');

    if (value !== cleaned) {
        $(this).val(cleaned);
    }
});
     $('#age').change(function(e) {

      if (this.value.match(/[^0-9]/g)) {
        this.value = this.value.replace(/[^0-9]/g, '');
      }

      var parsed_val = parseInt(this.value);
      
      if (parsed_val < 25 || parsed_val > 120) {
        alert('candidate Min Age should be 25');
        $('#age').val('');
        ('#messages').show().html('candidate Min Age should be 25');
        //alert('candidate Min Age should be 25');
      }

    });

    function encryptData(data) {
    var SECRET_KEY="AwdL2cXoGHtULolv"
  return CryptoJS.AES.encrypt(
    JSON.stringify(data),
    SECRET_KEY
  ).toString();
}
 </script>		


@endsection