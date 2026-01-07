@extends('admin.layouts.ac.theme')
@section('content') 
<div class="container mt-5">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
    <div class="head-title">
      <h3><i><img src="{{ asset('theme/images/icons/tab-icon-002.png')}}" /></i>Nomination De-Finalize AC</h3>
    </div>   
      <!-- Start Datatabe Wrap Div -->  
       @if(Session::has('ro_opt_messsage'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('ro_opt_messsage')) }}</strong> 
              </div>
          @endif  
      <div class="datatable-wrap">
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ceo/definalizevalidation') }}" >  
                {{ csrf_field() }}   
            <input type="hidden" name="st_code" value="{{$ST_CODE}}"><input type="hidden" name="id" value="{{$list->id}}"><input type="hidden" name="actype" value="{{$actype}}">
            <input type="hidden" name="ac_no" value="{{$ac_no}}">
            <input type="hidden" name="otp" value="{{$otp}}">
            <input type="hidden" name="otp_time" value="{{$otp_time}}">
          <div class="radio-area"> De-Finalize:- 
               <div class="custom-radio-btn">
               <input type="radio" name="action" id="marks1" value="1" checked="checked">
               <label for="marks1" >&nbsp;&nbsp; Yes</label>
               </div><!-- End Of custom-radio-btn Div -->
               <!--<div class="custom-radio-btn">
               <input type="radio" name="action" id="marks2" value="0"><label for="marks2" >NO</label>
               </div><!-- End Of custom-radio-btn Div -->
          </div>
           
               <div class="nomination-detail">
                COE De-finalize Message:— <br><textarea name="definalized_message" rows="5" cols="90"  >{{isset($list) ?$list->definalized_message:old('definalized_message') }} </textarea>
                @if ($errors->has('definalized_message'))  <span style="color:red;"><strong>{{ $errors->first('definalized_message') }}</strong></span>  @endif
                </div>
                <div class="nomination-detail"> Verify OTP Number 
                <input type='text'  name="verifyotp" class="nomination-field-2" value="{{ old( 'verifyotp') }}"/>
                   @if ($errors->has('verifyotp'))  <span style="color:red;"><strong>{{ $errors->first('verifyotp') }}</strong></span>  @endif
               
                  
                 </div> 
                 <div class="nomination-detail"> <div id="clockdiv"></div> </div>
              <div class="btns-actn">
                 <input type="submit" value="Submit">
                  <?php  $url = URL::to("/");  ?>
                <!-- <a href="{{ url('/ceo/candidate-definalize') }}/{{$ac_no}}/{{$actype}}"><input type="button" class="btn" value="Resend OTP"></a>-->
                 <input type="button" value="Resend OTP" onclick="location.href = '{{$url}}/ceo/candidate-definalize/{{$ac_no}}/{{$actype}}';">
                 <input type="button" value="Cancel" onclick="location.href ='{{$url}}/ceo/candidate-finalize';">
                  
              </div>
            </form>
              
      </div><!-- End Of datatable-wrap Div --> 
     
    </div><!-- End OF page-contant Div -->
    </div>          
  </div><!-- End Of parent-wrap Div -->
  </div> 
 
<script type="text/javascript">
  var time_in_minutes = 10;
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