@extends('layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
<?php //$elec_details=get_election_history_details('AC'); ?>
<?php 
if(Session::has('DB_id')){
          $DB_id = Session::get('DB_id');
        }else{
          $DB_id = 0;
        }
if(Session::has('elec_details')){
	$elec_details = Session::get('elec_details');
}else{
	$elec_details=get_election_history_details('AC','2');//1=>officer,2=>candidate
}	

$radio_elec='AC';
$election_category = 1;	
if(Session::has('radio_elec')){
	$radio_elec = Session::get('radio_elec');
	if($radio_elec=='MLC'){
		$election_category = 2;	
	}else{
		$election_category = 1;	
	}
}	
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
.login-soc-foot{}
.login-soc-foot>ul{padding: 0;}
.login-soc-foot>ul>li{display: inline-block; vertical-align: middle; width: 280px;}
.login-soc-foot>ul>li>a{display: block; padding: 0.5rem 1rem 0.5rem 0rem;}
.login-soc-foot>ul>li>a:hover,.login-soc-foot>ul>li>a:focus{color: #fff;}
.login-soc-foot>ul>li>a>img{display: inline-block; margin-right: 0.85rem; margin-left: 0.85rem;}
.reg-soc>ul>li{margin-top: 0; margin-bottom: 0;}
.fb-bg,.gmal-bg,.twtr-bg,.lnkd-bg{background-color: #48629B; color: #fff;}
.gmal-bg{background-color: #DC4B38;}
.twtr-bg{background-color: #23B0E6;}
.lnkd-bg{background-color: #0E76A8;}
.fb-bg:hover,.gmal-bg:hover,.twtr-bg:hover,.lnkd-bg:hover{text-decoration: none; box-shadow: 2px 2px 5px #666; transition: all 0.3s ease-in-out; -webkit-transition: all 0.3s ease-in-out;}
.fb-bg:hover,.fb-bg:focus{background-color: #20469B; }
.gmal-bg:hover,.gmal-bg:focus{background-color: #DF230A;}
.twtr-bg:hover,.twtr-bg:focus{background-color: #01A0DD;}
.lnkd-bg:hover,.lnkd-bg:focus{background-color: #0273A9;}
.orArea{position: relative; display: block; height: auto; border-top: 2px dashed #d7d7d7;}
.orArea>span {position: absolute;width: 100px;background-color: #fff;padding: 0.5rem;top: -1.5rem;left: 0;right: 0;margin: auto;display:block;text-align: center;text-transform: uppercase;font-weight: 600;}
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
          <span style="margin: auto;"><img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}"><p class="infoTag">Election Commission of India</span></p></span></figure> 
		 </span></figure> </div>
 
  
    <div class="col-md-6 loginDiv">
	
	 <div style="margin-left: 351px; margin-bottom: 22px; margin-right: 18px;">Select Language	 
	 <select name="language" class="form-control" onchange=" return dynamic_select(this.value);">
	 <option value=" ">Select Language</option>
	 <option value="{{ url('locale/en') }}" @if(Session::get('locale')=='en'){{'selected'}}@endif @if(Session::get('locale')==''){{'selected'}}@endif   >English</option>
	 <option value="{{ url('locale/hi') }}" @if(Session::get('locale')=='hi'){{'selected'}}@endif>Hindi</option>
	 </select>
	 
	 </div>
		
      <div class="login-right">
	  @php
		$uname = Session::get('uname');
		$uemail = Session::get('uemail');
	  @endphp
	  @if(!@$uname)
	  <!--<div class="login-soc-foot">
			<ul>
				<li class="mr-3"><a href="redirect/facebook" class="fb-bg"><img src="{{ asset('img/social/facebook-icon.png') }}" alt=""><span>Sign in with Facebook</span></a></li>
				<li><a href="redirect/google" class="gmal-bg"><img src="{{ asset('img/social/gmail-icon.png') }}" alt=""><span>Sign in with Gmail</span></a></li>
			</ul>
		  </div>-->
	  @endif
	    @if(@$uname)
		<div class="title m-b-md">
			Hi, <b>{{ $uname}}</b> <span style="float:right;">Email:<b>{{ $uemail}}</b></span>
		</div>
		@endif
	  <fieldset>
	
		<legend class="text-center mb-2"> 
		<div class=" btn-group main-nav">
			 <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}';" value="Home"/> 
			<input type="button" class="btn btn-link active" onclick="location.href = '{{$url}}/login';" value="Candidate Login"> 
            <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/officer-login';" value="Officer Login"/>	
			
        </div>
		</legend>
		<legend class="text-center"></legend>
		<form method="POST" action="{!! url('change-election') !!}" id="change_election"> 
		<input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
			<div class="row mb-3">
				<div class="col"> 
					<div class="inputGroup">
						<input id="radio1" name="radio" type="radio" onclick="redirect_parliament()"/>
						<label for="radio1">{{ __('messages.pc_election') }}</label>
					</div>
				</div>
				<div class="col">
					<div class="inputGroup">
					<input id="radio2" name="radio_elec" type="radio" @if($radio_elec=='AC')checked="checked"@endif class="elec_type" onchange="submit()" value="AC" />
					<label for="radio2">{{ __('messages.ac_election') }}</label>
				  </div>
				</div>
			</div>
		</form>
		<form method="POST" action="{!! url('change-database') !!}" id="change_databsse"> 
			<input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
	  
		
    </form>
     <script type="text/javascript">
      function change_database(){
        $('#change_databsse').submit();
      }
	  function change_election(){
        $('#change_election').submit();
      }
    </script>
 <form class="log-frm-area" method="POST" action="{{ url('/candidate-postlogin') }}" autocomplete='off' enctype="x-www-urlencoded" id="otpsend22">
      
        {{ csrf_field() }}
      
      <span class="help-block"> 
          <strong>{{ Session::get('log_message') }}</strong>
      </span>


		<input type="hidden" name="uname" value="{{@$uname}}">
		<input type="hidden" name="uemail" value="{{@$uemail}}">
		<input type="hidden" name="election_category" id="election_category" value="{{$election_category}}">
		
	  
      <div class="form-group">
        <input id="mobile" type="text" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{old('mobile')}}"  autofocus placeholder="{{ __('messages.enter_mobile') }} "  autocomplete="off" maxlength="10" minlength="10" >

        @if ($errors->has('mobile'))
          <span class="invalid-feedback"><strong>{{ $errors->first('mobile') }}</strong></span>
                      
        @endif 
      </div>
       
     
    <div class="form-group  d-flex flex-column flex-md-row align-items-center mb-3">
      <div class="col col-xs-12 m-0 p-0"> 
        <div class="captcha">
          <span id="captcha"><img id="refresh" src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default"></span>
          <button type="button" data-refresh-config="default" id="btn-refresh" class="btn btn-success btn-refresh captcha-img"><i class="fa fa-refresh"></i>  {{ __('messages.Refresh') }} </button>  
             
        </div>
      </div>

    <div class="col col-xs-12 pr-0 d-flex align-items-center capchtainpyt">
		  <input id="captcha" type="text" class="form-control{{$errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha"  placeholder="{{ __('messages.capcha') }}"   autocomplete="off" >

    &nbsp; &nbsp;
		
          <input type="submit" class="btn btn-primary" value="{{ __('messages.submit') }}"/> 
   		  
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
		<!-- @if(!@$uname)
		 <div class="orArea my-5"><span>or</span></div>  
		  <div class="login-soc-foot">
			   
				<ul class="d-flex">
					<li class="mr-3"><a href="#" class="fb-bg"><img src="{{ asset('img/social/facebook-icon.png') }}" alt=""><span>{{ __('messages.facebook') }}</span></a></li>
					<li><a href="#" class="gmal-bg"><img src="{{ asset('img/social/gmail-icon.png') }}" alt=""><span>{{ __('messages.gmail') }}</span></a></li>
				</ul>
				<ul class="d-flex">
					<li class="mr-3"><a href="#" class="twtr-bg"><img src="{{ asset('img/social/twitter-icon.png') }}" alt=""><span>{{ __('messages.twitter') }}</span></a></li>
					<li><a href="#" class="lnkd-bg"><img src="{{ asset('img/social/linkdin-icon.png') }}" alt=""><span>{{ __('messages.linkdin') }}</span></a></li>
				</ul>
				
				
			  </div>
		  @endif -->
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

function redirect_parliament(){
    window.location.href = "{{ config('public_config.pc_url') }}";
}
function redirect_assembly(){
    window.location.href = "{{ config('public_config.pc_url') }}";
}
function redirect_mlc(){
    window.location.href = "{{ config('public_config.pc_url') }}";
}
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
