@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
 
<style type="text/css">
  
  .captcha #captcha img {
    min-height: 44px;
    margin-top: 3px;
}
.send-otp-btn a {
    margin-right: 1rem;
}
input.form-control {
    border-radius: 10px !important;
}
button#send-otp-btn {
    border: none;
    border-radius: 10px;
}
.have-otp button {
    border: none;
    border-radius: 10px;
	margin-top: 1rem;
}

.sbtn button{
	border:none;
	border-radius:10px;
}

    .loadingClass{ background: url("{{ asset('theme/img/loader.gif') }}") no-repeat center !important; opacity: 0.5; }
    .smallLoadingClass{ background: url("{{ asset('theme/img/small-loader.gif') }}") no-repeat center !important;opacity: 0.5;padding: 7px;  }
	
	.otp > a.otp-refresh {
    font-size: 0.75rem;
    display: flex;
    justify-content: end;
    padding: 0.5rem;
    color: #f16b6f;
	cursor: pointer;
}

a.disabled {
  pointer-events: none;
  cursor: default;
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
          @if($url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")
 
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
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}';" value="Home"/> 
          <!-- <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/login';" value="Candidate Login"/>  -->
          <input type="button" class="btn btn-link active"  value="Officer Login"/>
		  
        </div>
 

        
		</legend>
  <!--<legend class="text-center login_for_office">Enter your 6 digit PIN</legend>-->
  <legend class="text-center login_for_office">Enter your OTP</legend>
       <!--    <h3 class="display 1">Officer Login</h3>   -->
               
	   
			   
	<div class="container-fluid cont">
    <div class="row">
        <div class="col-12">
            <div class="forms-container">
                <div class="signin-signup">
                    <div class="login-right">
                        <form class="sign-in-form login" id="login_via_ajax" method="POST" action="{!! $action_auth !!}" autocomplete='off' enctype="x-www-urlencoded">
                           
                              <div class="alert alert-success green" id="otpSent" style="display: none;width: 100%;text-align: center;"></div>
							  
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
							
                            <!--<div class="input-field">
                              
                                <input type="tel" class="form-control form-control-lg" id="mobile" placeholder="Mobile No." value="" name="mobile" minlength="10" maxlength="10">
                            </div> -->
							
                            <div id="mobErr" style="font-size:12px;color:red;"></div>
                               <div class="otp mt-3" style="width:100% !important;">
                                <div class="input-field">
                                  <!--  <i class="fas fa-lock"></i> -->
                                    <input type="password" id="otp" name="otp" placeholder="Please enter otp" class="form-control form-control-lg" minlength="1" value="">
                                </div>
                               <span id="timerDiv" style="padding-left: 7px;display: none;"> Time Remaining: <span id="timer"></span></span>
                                <a title="Click here to resend OTP" class="otp-refresh obtn">Resend OTP </a>
                            </div>
                                             


                            <div class="text-center m-3">
                                <div class="sbtn">
                                    <button type="submit" class="btn btn-primary px-5"><span class="">Log In</span></button>
                                </div>
                            </div>
                        </form>
						
						
						
						<!--
                        <div class="text-center" style="">
                            <div class="send-otp mb-3">
                                <button class="btn solid btn-primary" id="send-otp-btn" onclick="sendOtp();"><span class="">Send otp</span></button>
                            </div>
                            <div class="have-otp mb-3">
                                <span id="already-text" style="text-align: center;font-size: 13px;color: #000;font-weight: 500;">Already have an OTP? Click the button below to proceed.</span>
                                <button class="btn solid btn-primary" onclick="haveOtp(); var e=this;setTimeout(function(){e.disabled=true;},0);return true;">
                                    <span class="">Already Have otp</span>
                                </button>
                            </div>
                        </div>
						-->
						
						
                    </div>
                </div>
            </div>
        </div>
       
    </div>
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
              <figure class="foot-lft"><img src="{{ asset('admintheme/img/vendor/footer-img.png')}}"></figure>
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
@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif

<script type="text/javascript">
        document.onkeydown = (e) => {
            if (e.key == 123) {
                e.preventDefault();
            }
            if (e.ctrlKey && e.shiftKey && e.key == 'I') {
                e.preventDefault();
            }
            if (e.ctrlKey && e.shiftKey && e.key == 'c') {
                e.preventDefault();
            }
            if (e.ctrlKey && e.shiftKey && e.key == 'J') {
                e.preventDefault();
            }
            if (e.ctrlKey && e.key == 'u') {
                e.preventDefault();
            }
        };
        $(document).keypress("u",function(e) {
            if(e.ctrlKey) { return false; } else { return true; }
        });
    </script>
        <script type="text/javascript">
        $( "#mobile" ).on('keyup', function(){
            var mobile = $('#mobile').val();
            if ( mobile.length > 10 ){
                $('#mobErr').html('Please enter valid mobile'); return false;
            } else {
                $('#mobErr').html(''); return true;
            }
        });

        $(document).ready(function(){
            var invalid_input = "";
            var invalid_input1 = "";
            var invalid_inputtime = "";
            // console.log('pppp ' + invalid_input);
            if (invalid_input && invalid_input != '' || invalid_input1 && invalid_input1 != '') {
                $('.otp').show(); $('.cpt').show(); $('.sbtn').show(); $('.send-otp').hide(); $('.have-otp').hide();
            } else {
                //$('.otp').hide(); $('.cpt').hide(); $('.sbtn').hide();
            }
        });

        function sendOtp() {
             
               // event.preventDefault();
                $('#send-otp-btn').attr('disabled');
                
                $('#send-otp-btn').addClass('loadingClass');
                setTimeout( function(){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    // alert($('#mobile').val());
                    $.ajax({
                        url: APP_URL + '/sendotp',
                        type: 'post',
                        data: {
                            'mobile': $('#mobile').val()
                        },
                        success: function(data) {
                            // alert(data);
                            $('#send-otp-btn').removeClass('loadingClass');							
							
                            if (data != 0) {
								$('.obtn').hide();
                                $('.otp').show();
                                $('.cpt').show();
                                $('.sbtn').show();
								$('.send-otp').hide();
                                $('.have-otp').hide();
                                $('#send-otp-btn').removeAttr('disabled');
								timer(60);
								
								
								setTimeout( function(){
								$('.obtn').show();
								}, 60000 );
								
								
                                
                                
                            } else {
                                $('#mobErr').html('Mobile Number does not exist. Please enter a valid mobile.')
                               
                            }
                        },
                    });
                }, 2000 );
            
        }

        function haveOtp() {
            // alert($('#mobile').val());
            $('.otp').show();
            $('.cpt').show();
            $('.sbtn').show();
            $('.send-otp').hide();
            $('.have-otp').hide();
        }

      
        $(".otp-refresh").click(function() {
            
           
            $('.otp-refresh').addClass('smallLoadingClass');
            $('.otp-refresh').addClass('disabled');
            $('#timerDiv').show();
            timer(60);
            setTimeout( function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: APP_URL + '/sendotp',
                    data: {
                       // 'mobile': $('#mobile').val()
                    },
                    success: function(){
                        $('.otp-refresh').removeClass('smallLoadingClass');
                        $('#otpSent').show().html('OTP Sent');
						$('.otp-refresh').addClass('disabled');
                    }
                });
            }, 1000 );
        });
		
		

        let timerOn = true;
        function timer(remaining) {
          var m = Math.floor(remaining / 60);
          var s = remaining % 60;
          
          m = m < 10 ? '0' + m : m;
          s = s < 10 ? '0' + s : s;
          document.getElementById('timer').innerHTML = m + ':' + s;
          remaining -= 1;
          
          if(remaining >= 0 && timerOn) {
            setTimeout(function() {
                timer(remaining);
            }, 1000);
            return;
          }

          if(!timerOn) {
            // Do validate stuff here
            return;
          }
          
          // Do timeout stuff here
          $('.otp-refresh').removeClass('disabled');
          $('#timerDiv').hide();
          $('#otpSent').html('').hide();
        }
    </script>

<script type="text/javascript">
//RESEND OTP LOGIN STARTS


$(document).on("click", ".sendotpform", function () {    

    var mobile = $("#mobile").val();

        $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: APP_URL + '/resendotp',                
            type: 'POST',
            data: 'mobile='+mobile,
            success: function (data) {
                if(data == 1){
                    $('#otpsend').hide();
                    $('#attempts').addClass('alert alert-info').text('Reached maximum otp attempts. Request for new OTP.');
                }else if(data == 3){
                    $('#otpsend').hide();
                    $('.success').hide();
                    $('#attempts').addClass('alert alert-info').text('Can Send only 1 OTP per minute.');
                }else{
                  $('#attempts').hide();
                  $('#otpsend').addClass('alert alert-info').text('OTP successfully Send.');
                         //$('#attempts').hide();
                }
                
            }
        });
    
});
//RESEND OTP LOGIN ENDS  
</script>
@endsection