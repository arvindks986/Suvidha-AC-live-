@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");
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
@media all and (max-width: 1024px){
 .garuda-link {position: absolute; top: 0; right: 1.5rem; background-color: #fff; color: #BB4292;z-index:9}
 .garuda-link:hover, .garuda-link:focus {background-color: #FFC517;}
}
</style>


<main>
   <section class="main-box">
     <div class="circle peach-gradient">
            <img src="{{ asset('admintheme/img/vendor/background.png') }}" alt=""></div>
    <div class="container-fluid h-100">    
         
    <div class="row justify-content-center align-items-center h-100" style="width:100%; margin:0 auto;">
  
  <div class="col-md-6 login-page "> 
        <figure class="evm-logo officerlogin">
          <span style="margin: auto;"> @if($url=="https://suvidha.eci.gov.in/suvidhaac/public" || $url=="http://suvidha.eci.gov.in/suvidhaac/public" || $url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")

 
   <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
  @else
    <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
  @endif
   <p>Election Commission of India </p> </span></figure> </div>
	  
	
	
	
  
    <div class="col-md-6 loginDiv">
    <div class="login-right">
   
   <fieldset>
   <legend class="text-center mb-2"> 
   
 
   <div class=" btn-group main-nav">
          
         <?php /* @if($url=="https://encore.eci.gov.in/suvidhaac/public" || $url=="http://encore.eci.gov.in/suvidhaac/public" || $url=="https://encore.eci.gov.in" || $url=="http://encore.eci.gov.in")
			<input type="button" class="btn btn-link active"  value="Officer Login"/>
		 
             <!--<input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/login';" value="Candidate Login"/>-->
          @endif */ ?>
          <legend class="text-center login_for_office">&nbsp;&nbsp;&nbsp;&nbsp;Login For Officer&nbsp;&nbsp;&nbsp;&nbsp;</legend>
		  
        </div>
 

        
		</legend>
  
       <!--    <h3 class="display 1">Officer Login</h3>   -->
               
<div class="pos_relative" style="position: relative;overflow: hidden;">
<script type="text/javascript">
  function redirect_parliament(){
    window.location.href = "{{ config('public_config.pc_url') }}";
  }
</script>
               
    @if (session('data_username'))
        <div class="alert alert-danger"> {{session('data_username') }}</div>
    @endif
    <span class="help-block"> 
        <strong>{{ Session::get('log_message') }}</strong>
    </span>
    @if(Session::has('flash-message'))
      @if(Session::has('status'))
        <?php
        $status = Session::get('status');
        
        
        if($status==1){
          $class = 'alert-success';
        }
        else{
          $class = 'alert-danger';
        }
        ?>
      @endif
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
      </div>
    @endif


    <?php 
if(Session::has('DB_id')){
          $DB_id = Session::get('DB_id');
        }else{
          $DB_id = 0;
        }
     ?>

    <form class="log-frm-area" id="login_via_ajax" method="POST" action="{!! $action !!}" autocomplete='off' enctype="x-www-urlencoded">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
    <input type="hidden" name="eleid" value="{{$DB_id}}">
      <div class="form-group">
        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{old('username')}}"  autofocus placeholder="User Name"  autocomplete="off" >
        <?php if($errors->has('username')){ ?>
          <span class='invalid-feedback'><strong>{!! $errors->first('username'); !!}</strong></span>
        <?php } ?>
      </div>

      <?php if(isset($skip_password_network) && $skip_password_network == true){ ?>

      <?php }else{ ?>
      <div class="form-group"> 
          <input id="password" type="password" class="form-control{{$errors->has('password') ? ' is-invalid' : '' }}" name="password"  placeholder="Password" autocomplete="off" >
          <?php if($errors->has('password')){ ?>
          <span class='invalid-feedback'><strong>{!! $errors->first('password'); !!}</strong></span>
          <?php } ?>
      </div>
      <?php } ?>
     
	 
	   <div class="form-group  d-flex flex-column flex-md-row align-items-center mb-3">
                    <div class="col col-xs-12 m-0 p-0"> 
						<div class="captcha">
								<span id="captcha"><img id="refresh" src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default"></span>
                    <button type="button" data-refresh-config="default" id="btn-refresh" class="btn btn-success btn-refresh captcha-img refresh"><i class="fa fa-refresh"></i> Refresh</button>
								  
								    
						</div>
					</div>
						 
        <div class="col  pr-0 d-flex align-items-center capchtainpyt">  	

          <div class="row">
         <input id="lcaptcha" type="text" class=" col-md-7 form-control{{$errors->has('lcaptcha') ? ' is-invalid' : '' }}" name="lcaptcha"  placeholder="captcha"   autocomplete="off"/>&nbsp;
				<button type="button" class="btn btn-primary col-md-4" id="login">Submit</button>

       </div>
				        </div>              
        </div>
	 		 <div class="row">
					
						  
						 </div>
            
            <div class="row">
   	          <div class="col captcha_error">
						    <?php if($errors->has('lcaptcha')){ ?>
          <span class='invalid-feedback'><strong>{!! $errors->first('lcaptcha'); !!}</strong></span>
          <?php } ?>
						  </div>
              <div class="col form-group"> 
                <small><a href="{!! url('/forgot') !!}" class="pull-right">Forgot Password</a></small>
              </div>
            </div>
	 
 </form>

</div>


               
               </fieldset>
    
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
<script src="{{ asset('theme/js/aes.js') }}"></script>
<script src="{{ asset('theme/js/md5.min.js') }}"></script>
<script src="{{ asset('theme/js/shaen.js') }}"></script>
<script type="text/javascript">
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
  $('#login').click(function() {
		$.ajax({
			url: "{!! url('/profile/checkstatus') !!}",
			type: 'POST',
			data: '_token={!! csrf_token() !!}&username='+$('#username').val(),
			dataType: 'json',  
			complete: function() {
			},        
			success: function(json) {
				var pwdObj = document.getElementById('password');
				if(json['status'] == true){
					if(pwdObj.value !=''){
						if(json['flag']=='1'){
							cfx1();
						}else{
							cfx();
						}
						$("#login_via_ajax").submit();
					}
				}else{
					if(pwdObj.value !=''){
						cfx();
						$("#login_via_ajax").submit();
					}
				}
			},
			error: function(data) {
            }
        }); 
        //setTimeout(function(){ return true; }, 2000);  
        // return false to cancel form action
    });
	  
    function cfx(){
	  var passme = document.getElementById('password').value;
	  var CryptoJSAesJson = {
		  stringify: function (cipherParams) {
			  var j = {ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)};
			  if (cipherParams.iv) j.iv = cipherParams.iv.toString();
			  if (cipherParams.salt) j.s = cipherParams.salt.toString();
			  return JSON.stringify(j).replace(/\s/g, '');
		  },
		  parse: function (jsonStr) {
			  var j = JSON.parse(jsonStr);
			  var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(j.ct)});
			  if (j.iv) cipherParams.iv = CryptoJS.enc.Hex.parse(j.iv);
			  if (j.s) cipherParams.salt = CryptoJS.enc.Hex.parse(j.s);
			  return cipherParams;
		  }
	  }

	  var encrypted2 = CryptoJS.AES.encrypt(JSON.stringify(passme), '<?php echo $xyx ?>', {format: CryptoJSAesJson}).toString();
	  document.getElementById('password').value=encrypted2;
	}
	function cfx1() {
		var e = document.getElementById("password").value,
        te = SHA256(e),
        t = SHA256(te + '<?php echo $xcs?>');
		document.getElementById("password").value = t
	}
</script>
@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
<script type="text/javascript">
  $(document).ready(function(e){
  $("input[name='password']").val('');
  $("input[name='lcaptcha']").val('');
  });
  error_messages("{{session('error_mes') }}");
</script>
@endif
@endsection
