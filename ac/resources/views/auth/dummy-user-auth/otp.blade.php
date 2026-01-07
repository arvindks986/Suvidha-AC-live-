@extends('layouts.login')
 
@section('content')
<?php $url=url('/'); ?> 
<main>
  <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
        <img src="{{ asset('theme/img/vendor/background.png') }}" alt=""></div>
          <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">

   <div class="col-md-6 login-page "> 
    <figure class="evm-logo">
      <span style="margin: auto;"> @if($url=="https://suvidha.eci.gov.in/suvidhaac/public" || $url=="http://suvidha.eci.gov.in/suvidhaac/public" || $url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")

 
   <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
  @else
    <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
  @endif
   <p>Election Commission of India </p> </span></figure> </div>
  
  <div class="col-md-6">
    <div class="login-right">
      <div class="d-flex align-items-center mb-3">
          <h4>OTP<span> Verification </span></h4>
      </div>
               
  <form class="log-frm-area" method="POST" action="{{ url('candidate-customlogin') }}" autocomplete='off' enctype="x-www-urlencoded" id="loginval">
      {{ csrf_field() }}

      <span class="help-block"><strong>{{ Session::get('opterror') }} </strong></span>
        
        <!--MOBILE NUMBERT FIELD STARTS-->
        <div class=" form-inline{{ $errors->has('mobile') ? ' has-error' : '' }}">
          
          <input id="mobile" type="text" class="form-control col-md-9" name="mobile" value="{{$mobile}}"  placeholder="Mobile Number" maxlength="10" minlength="10" readonly="readonly">
          
          @if ($errors->has('mobile'))
                <span class="invalid-feedback"> <strong>{{ $errors->first('mobile') }}</strong>   </span>
          @endif
        </div>
        <!--MOBILE NUMBER FIELD ENDS-->
      
       <!--OTP FIELD STARTS-->
        <div class="mt-3 form-inline{{ $errors->has('otp') ? ' has-error' : '' }}">
            
          <input id="otp" type="password" class="form-control col-md-9" name="otp" value="{{ old('otp') }}"  placeholder="Mobile Otp" maxlength="6" minlength="6">&nbsp;

         @if ($errors->has('otp'))
              <span class="invalid-feedback"> <strong>{{ $errors->first('otp') }}</strong>   </span>
         @endif
	       
         
        </div>
        <!--OTP FIELD ENDS-->

        <div class="form-inline">
          <input type="submit" class="btn btn-primary mt-3" value="Verify OTP"> 
          &nbsp;&nbsp;&nbsp;<span class="btn btn-primary mt-3 resendotpform">Resend OTP</span>
          &nbsp;&nbsp;&nbsp;<a href="{{url('/candidate-login')}}" class="btn btn-primary mt-3">Back </a>
        </div>


      <!-- <div id="clockdiv" class="clockdiv"></div> -->
                      
    </form><br>

     <div class="form-inline">
     @if (session('error'))
           <div class="alert alert-info">{{ session('error') }}</div>
      @endif
      
      @if($errors->any())
        <div class="alert alert-info">{{$errors->first()}}</div>
      @endif

      @if (session('success'))
           <div class="alert alert-info success">{{ session('success') }}</div>
      @endif

    </div>
    
    <div  id="otpsend"></div> 
     
     <div id="attempts"></div>     
              
    
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
 

<script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
<!-- Validation  JavaScript -->
<!--**********DCO FORM VALIDATION STARTS**********-->
    <script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
    <!--**********DCO FORM VALIDATIONS SCRIPT**********-->
    <script src="{{ asset('formvalidations/loginformvalidations.js') }}"></script>
    <!--**********DCO FORM VALIDATION ENDS*************-->
<script type="text/javascript">
//RESEND OTP LOGIN STARTS


$(document).on("click", ".resendotpform", function () {    

    var mobile = $("#mobile").val();

        $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: APP_URL + '/candidate-resendotp',                
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
