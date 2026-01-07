@extends('layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
<?php $elec_details=get_election_history_details('AC'); ?>
<?php 

          $DB_id = 1;

		
     ?>
	 <style type="text/css">
  
  .captcha #captcha img {
    min-height: 44px;
    margin-top: 3px;
}
.inputGroup {
    background-color: #fff;
    /* display: block; */
    /* margin: 10px 0; */
    /* position: relative; */
    /* width: 32%; */
    /* float: left; */
}
.inputGroup label {
   padding: 6px 15px 6px 30px;
    width: 100%;
    display: block;
    text-align: left;
    color: #3C454C;
    cursor: pointer;
    position: relative;
    z-index: 2;
    -webkit-transition: color 200ms ease-in;
    transition: color 200ms ease-in;
    overflow: hidden;
    font-size: 14px!important;
    border-radius: 6px;
    border: 1px #bb4292 solid;
    text-align: right;
}
.inputGroup label:before {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    content: '';
    background-color: #bb4292;
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%, -50%) scale3d(1, 1, 1);
    transform: translate(-50%, -50%) scale3d(1, 1, 1);
    -webkit-transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
    z-index: -1;
}
.inputGroup input:checked ~ label:after {
    background-color: #ffc517;
    border-color: #ffc517;
}

.inputGroup label:after {
    width: 24px;
    height: 24px;
    content: '';
    border: 2px solid #bb4292;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
    background-repeat: no-repeat;
    background-position: 0px 0px;
    border-radius: 50%;
    z-index: 2;
    position: absolute;
    left: 10px;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    cursor: pointer;
    -webkit-transition: all 200ms ease-in;
    transition: all 200ms ease-in;
}
.inputGroup input:checked ~ label {
  color: #fff;
}
.inputGroup input:checked ~ label:before {
  -webkit-transform: translate(-50%, -50%) scale3d(56, 56, 1);
          transform: translate(-50%, -50%) scale3d(56, 56, 1);
  opacity: 1;
}

.inputGroup input {
    width: 32px;
    height: 32px;
    -webkit-box-ordinal-group: 2;
    order: 1;
    z-index: 2;
    position: absolute;
    right: 30px;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    cursor: pointer;
    visibility: hidden;
}

.form {
  padding: 0 16px;
  max-width: 550px;
  margin: 50px auto;
  font-size: 18px;
  font-weight: 600;
  line-height: 36px;
}


code {
  background-color: #9AA3AC;
  padding: 0 8px;
}

</style>

 
<style type="text/css">
  
  .captcha #captcha img {
    min-height: 44px;
    margin-top: 3px;
}
</style>
<main>

  <section class="main-box">
   
     
     <div class="circle peach-gradient">
            <img src="{{ asset('theme/img/vendor/background.png') }}" alt="">
     </div>
		 <div class="container-fluid h-100">	
		 <div class="row justify-content-center align-items-center h-100" style="width:100%; margin:0 auto;">
		
		
		<div class="col-md-6 login-page "> 
        <figure class="evm-logo officerlogin">
          <span style="margin: auto;"> 
 @if($url=="https://suvidha.eci.gov.in/suvidhaac/public" || $url=="http://suvidha.eci.gov.in/suvidhaac/public" || $url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")
 
   <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
  @else
    <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
  @endif
   <p>Election Commission of India </p> </span></figure> </div>
  
    <div class="col-md-6 loginDiv">
		<div style="margin-left: 351px; margin-bottom: 22px; margin-right: 18px;">Select Language	 
			 <select name="language" class="form-control" onchange=" return dynamic_select(this.value);">
			 <option value=" ">Select Language</option>
			 <option value="{{ url('locale/en') }}" @if(Session::get('locale')=='en'){{'selected'}}@endif @if(Session::get('locale')==''){{'selected'}}@endif   >English</option>
			 <option value="{{ url('locale/hi') }}" @if(Session::get('locale')=='hi'){{'selected'}}@endif>Hindi</option>
			 </select>
		</div>
		
      <div class="login-right">
	  <fieldset>
	
		<legend class="text-center mb-2"> 
		<div class=" btn-group main-nav">
			 <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}';" value="{{ __('messages.home') }}"/> 
       
			 @if($url=="https://suvidha.eci.gov.in/suvidhaac/public" || $url=="http://suvidha.eci.gov.in/suvidhaac/public" || $url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")
			 <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/login';" value="{{ __('messages.candidate_login') }}"/>
		    @else
		   <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/officer-login';" value="{{ __('messages.officer_login') }}"/>
          @endif 
           
        </div>
		</legend>
		<legend class="text-center">{{ __('messages.apply_nom_per') }}</legend>
		<div class="row mb-3">
 <div class="col"> 
 
 <div class="inputGroup">
    <input id="radio1" name="radio" type="radio" onclick="redirect_parliament()"/>
    <label for="radio1">{{ __('messages.pc_election') }}</label>
</div>
</div>
<div class="col">

<div class="inputGroup">
    <input id="radio2" name="radio" type="radio" checked="checked"  />
    <label for="radio2">{{ __('messages.ac_election') }}</label>
  </div>
  
  </div>
 
     
  
</div>
<script type="text/javascript">
  function redirect_parliament(){
    window.location.href = "{{ config('public_config.pc_url_cand') }}";
  }
</script>
		
 <form class="log-frm-area" method="POST" action="{{ url('/user-postlogin') }}" autocomplete='off' enctype="x-www-urlencoded" id="otpsend">
      
        {{ csrf_field() }}
      
      <span class="help-block"> 
          <strong>{{ Session::get('log_message') }}</strong>
      </span>
<div class="row" id="logincondrow1"><!-- made this id to logincondrow for enable no election schedule-->
    <div class="col" id="logincond">  
      <div class="form-group">
        <input id="mobile" type="text" class="form-control {{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{old('mobile')}}"  autofocus placeholder="{{ __('messages.enter_mobile') }}"  autocomplete="off" maxlength="10" minlength="10" >
        @if ($errors->has('mobile'))
          <span class="invalid-feedback"><strong>{{ $errors->first('mobile') }}</strong>
                      </span>
        @endif 
      </div>
	<input type="hidden" name="election" id="election" value="{{$DB_id}}">
    <div class="form-group  d-flex flex-column flex-md-row align-items-center mb-3">
      <div class="col col-xs-12 m-0 p-0"> 
        <div class="captcha">
          <span id="captcha"><img id="refresh" src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default"></span>
            <button type="button" data-refresh-config="default" id="btn-refresh" class="btn btn-success btn-refresh captcha-img"></i> {{ __('messages.Refresh') }}</button>  
             
        </div>
      </div>

    <div class="col col-xs-12 pr-0 d-flex align-items-center capchtainpyt">
		  <input id="captcha" type="text" class="form-control{{$errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha"  placeholder="{{ __('messages.capcha') }}"   autocomplete="off" >


				@if($errors->has('captcha'))
					@php
						$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
						$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
						$ErrorMessage['MobNo']= old('mobile') ?? '';
						$ErrorMessage['applicationType']= 'WebApp';
						$ErrorMessage['Module']= 'SUVIDHA';
						$ErrorMessage['TransectionType']= 'User';
						$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
						$ErrorMessage['TransectionAction']= 'Captcha_Verify';
						$ErrorMessage['TransectionStatus']= 'FAILURE';
						$ErrorMessage['LogDescription']= 'Captcha Invalid';
						App\Helpers\LogNotification::LogInfo($ErrorMessage);
					@endphp
				@endif



    &nbsp; &nbsp;
		
          <input type="submit" class="btn btn-primary" value="{{ __('messages.submit') }}"/> 
   		  
  </div>
  </div>
  </div>
  </div>
      @if ($errors->has('captcha'))
          <span class="invalid-feedback">
              <strong>{{ __('messages.captcha_error') }}</strong>
          </span>
       @endif
 
  </form>
 

        
		
	  </fieldset>
	        
          <div class="form-inline">
          @if (session('success'))
               <div class="alert alert-info">{{ session('success') }}</div>
          @endif
         </div>    
    
    </div>    
    </div>    
    </div>
        </div>
</section>
<footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
      <div class="col"></div>
            <div class="col">
              <figure class="foot-lft"><img src="{{ asset('theme/img/vendor/footer-img.png')}}"></figure>
            </div>
            <div class="col text-right">

				<a style="color:#bbb;float:right;" href="https://eci.gov.in/divisions-of-eci/ict-apps/" target="_blank">Made In ECI</a>
   
            </div>
          </div>
        </div>
      </footer>  
</main>
@endsection
@section('script')

<script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
<!-- Validation  JavaScript -->
<!--**********DCO FORM VALIDATION STARTS**********-->
    <script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
    <!--**********DCO FORM VALIDATIONS SCRIPT**********-->
    <script src="{{ asset('formvalidations/loginformvalidations.js') }}"></script>
    <!--**********DCO FORM VALIDATION ENDS*************-->
    
<script type="text/javascript">
function dynamic_select(url){
        window.location = url; // redirect
	 } 
$(function(){
         var db = $("#new :selected").val();
    if(db == '')
    {
        $('#logincond').css('display','none');
        $('#logincondrow').html('<div class="alert alert-warning mb-4" role="alert">'+
        '<i class="fa fa-bullhorn"></i>&nbsp; &nbsp; <b class="text-center">No elections are scheduled</b>.</div>');
    }
    else
    {
        $('#logincond').css('display','block');
    }
     
   
    $('select#new').change(function () {
        var db1 = $(this).val();
         if(db1 == '')
        {
            $('#logincond').css('display','none');
            $('#logincondrow').html('<div class="alert alert-warning mb-4" role="alert">'+
      '<i class="fa fa-bullhorn"></i>&nbsp; &nbsp; <b class="text-center">No elections are scheduled</b>.</div>');
        }
        else
        {
            $('#logincond').css('display','block');
            //$('#logincondrow').html('');
        }
    });
    });
 /*function refereshcaptch(){    
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/refresh_captcha",           
       success: function (data) {
         jQuery("#captcha").html(data.captcha);
       },
       error: function (data, textStatus, errorThrown) {
             //do something

       }
           });
    }*/

//CAPTCHA REFRESH STARTS HERE
 $('.captcha-img').on('click', function () {
    var captcha = $(this);
    var config = captcha.data('refresh-config');
    $.ajax({
        method: 'GET',
        url: APP_URL +'/get_captcha/' + config,
    }).done(function (response) {
        $('#refresh').prop('src', response);
    });
});
//CAPTCHA REFRESH ENDS HERE
</script>
@endsection