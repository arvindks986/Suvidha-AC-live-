@extends('admin.layouts.theme')
@section('content') 
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
    <div class="head-title">
      <h3><i><img src="{{ asset('theme/images/icons/tab-icon-002.png')}}" /></i>Results Decelaration Verify</h3>
    </div>   
      <!-- Start Datatabe Wrap Div -->  
       @if(Session::has('ro_opt_messsage'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('ro_opt_messsage')) }}</strong> 
              </div>
          @endif  
      <div class="datatable-wrap">
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ro/results-declaration') }}" >  
                {{ csrf_field() }}   
            
            <input type="hidden" name="otp" value="{{$otp}}">
            <input type="hidden" name="otp_time" value="{{$otp_time}}">
           
           
                
                <div class="nomination-detail"> Verify OTP Number 
                <input type='text'  name="verifyotp" class="nomination-field-2" value="{{ old( 'verifyotp') }}"/>
                   @if ($errors->has('verifyotp'))  <span style="color:red;"><strong>{{ $errors->first('verifyotp') }}</strong></span>  @endif
               
                  
                 </div> 
                 <div class="nomination-detail"> <div id="clockdiv"></div> </div>
              <div class="btns-actn">
                 <input type="submit" value="Submit">
                 <a href="{{ url('/ro/results-verified') }}"><input type="button" class="btn" value="Resend OTP"></a> 
              </div>
            </form>
              
      </div><!-- End Of datatable-wrap Div --> 
     
    </div><!-- End OF page-contant Div -->
    </div>          
  </div><!-- End Of parent-wrap Div -->
  </div> 
 
<script type="text/javascript">
  var time_in_minutes = 30;
var current_time = Date.parse(new Date());
var deadline = new Date(current_time + time_in_minutes*60*1000);


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
    if(t.total<=0){ clearInterval(timeinterval); }
  }
  update_clock(); // run function once at first to avoid delay
  var timeinterval = setInterval(update_clock,1000);
}
run_clock('clockdiv',deadline);
</script>
@endsection