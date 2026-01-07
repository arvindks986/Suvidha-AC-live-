@extends('admin.layouts.login')
 
@section('content')
<main>
   <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
            <img src="{{ asset('theme/img/vendor/background.png') }}" alt=""></div>
    <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">
  
  
   <div class="col-md-6 login-page "> 
  <figure class="evm-logo">
  <span style="margin: auto;"><img style="max-width:300px;" src="{{ asset('theme/img/logo/eci-logo.png') }}"> <p>Election Commission of India</p> </span></figure> </div>
    <div class="col-md-6">
    <div class="login-right">
    
   
                <div class="d-flex align-items-center mb-3">
                  <h4>OTP <span>Verification</span></h4>
                </div>
               
  <form class="log-frm-area " method="POST" action="{{ url('/verifyloginotp') }}" autocomplete='off' enctype="x-www-urlencoded">
                        {{ csrf_field() }}
                          <span class="help-block">
                              <strong>{{ Session::get('opterror') }} </strong>
                          </span>
                    
                    <div class="form-inline {{ $errors->has('mobile_otp') ? ' has-error' : '' }}">
                        <input id="mobile_otp" type="password" class="form-control col-md-9" name="mobile_otp" value="{{ old('mobile_otp') }}"  placeholder="Mobile Otp" > &nbsp;
							<input type="submit" class="btn btn-primary float-right" value="Verify OTP">
                        
                    </div>
     @if ($errors->has('mobile_otp'))
                              <span class="invalid-feedback"> <strong>{{ $errors->first('mobile_otp') }}</strong>   </span>
                          @endif
                        <div id="clockdiv" class="clockdiv"></div>
                     
                        
                    </form>
                     <form class="log-frm-area resendotp" method="POST" action="{{ url('/resendotp') }}" autocomplete='off' enctype="x-www-urlencoded">
                        {{ csrf_field() }}
                       <input type="hidden" name="id" value="ss" >
				                <input type="submit" id="resend" class="text-danger btn btn-link p-0" value="Resend OTP" >
                        
                    </form>
                     <a href="{{ url('/officer-login') }}" class="btn btn-primary float-right mt-3">Back </a>
              
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
 
  </main>
 <script type="text/javascript">
  var time_in_minutes = 2;
var current_time = Date.parse(new Date());
var deadline = new Date(current_time + time_in_minutes*60*1000);
document.getElementById("resend").disabled = true;

function time_remaining(endtime){
  var t = Date.parse(endtime) - Date.parse(new Date());
  var seconds = Math.floor( (t/1000) % 60 );
  var minutes = Math.floor( (t/1000/60) % 60 );
  var hours = Math.floor( (t/(1000*60*60)) % 24 );
  var days = Math.floor( t/(1000*60*60*24) );
  return {'total':t, 'days':days, 'hours':hours, 'minutes':minutes, 'seconds':seconds};
}
function run_clock(id,endtime){
  var clock = document.getElementById(id);
  function update_clock(){
    var t = time_remaining(endtime);
    clock.innerHTML = 'Left Time For OTP : '+t.minutes+' : '+t.seconds;
    if(t.total<=0){ clearInterval(timeinterval); document.getElementById("resend").disabled = false; }
  }
  update_clock(); // run function once at first to avoid delay
  var timeinterval = setInterval(update_clock,1000);
}
run_clock('clockdiv',deadline);
</script>

@endsection
