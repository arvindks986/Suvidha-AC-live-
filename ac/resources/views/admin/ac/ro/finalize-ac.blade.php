@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'Finalize Nomination Details')
@section('content')
<?php  $url = URL::to("/");    ?>
<section class="tabs-data">
<div class="card text-left" style="max-width:40%; margin:0 auto">
                <div class="card-header ">
                  <h2 class="">Finalize Nomination Details </h2>
                </div>
              @if(Session::has('ro_opt_messsage'))
                  <div class="alert alert-danger">
                        <strong> {{ nl2br(Session::get('ro_opt_messsage')) }}</strong> 
                    </div>
              @endif  
      <div class="card-body">                 
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/finalize-candidate') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{ csrf_field() }} 
        <input type="hidden" name="id" value="{{$lists->id}}"><input type="hidden" name="cons_no" value="{{$lists->const_no}}">
            <input type="hidden" name="st_code" value="{{$lists->st_code}}">
            <input type="hidden" name="cons_type" value="{{$lists->const_type}}">
            <input type="hidden" name="election_id" value="{{$lists->election_id}}">
              <input type="hidden" name="otp" value="{{$otp}}">  <input type="hidden" name="otp_time" value="{{$otp_time}}">
          <div class="form-group">
                      <label>Verify OTP Number :- <sup>*</sup></label>
               <input type='text'  name="verifyotp" id="verifyotp" class="nomination-field-2" value="{{old('verifyotp') }}"/>
                <span id="err1"  style="color:red;"></span>
                   @if ($errors->has('verifyotp'))  <span style="color:red;"><strong>{{ $errors->first('verifyotp') }}</strong></span>  @endif
                 
                 <div id="clockdiv"></div>
          </div>
          <div class="form-group float-right">       
                <input type="submit" value="Submit" placeholder="" class="btn btn-primary">
                 <input type="button" value="Resend OTP" onclick="location.href = '{{$url}}/roac/finalize-ac';" class="btn btn-primary">
          </div>  
            
              
        </form>
    </div>
  </div>
</section> 
 
<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script>  
$(document).ready(function(){
 
  $("#election_form").submit(function(){
    if($("#verifyotp").val()=="")
    {
      $("#err").text("");
      $("#err1").text("Please enter verifyotp");
      $("#verifyotp").focus();
      return false;
    } 
      
    });
  });
</script> 
<script type="text/javascript">
  var time_in_minutes = 5;
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