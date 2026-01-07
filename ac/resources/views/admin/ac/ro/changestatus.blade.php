@extends('admin.layouts.pc.theme')
@section('content') 
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
    <div class="head-title">
      <h3><i><img src="{{ asset('theme/images/icons/tab-icon-002.png')}}" /></i>Change Candidate Status</h3>
    </div>   
      <!-- Start Datatabe Wrap Div -->  
       @if(Session::has('ro_opt_messsage'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('ro_opt_messsage')) }}</strong> 
              </div>
          @endif  
      <div class="datatable-wrap">
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/statusvalidation') }}" >
                {{ csrf_field() }}   
            <input type="hidden" name="candidate_id" value="{{$lists->candidate_id}}">
            <input type="hidden" name="nom_id" value="{{$lists->nom_id}}">
    <table >
        <tr><td> Action : - <span class="pagespanred">*</span></td> <td>
          <div class="radio-area">
               <div class="custom-radio-btn">
               <input type="radio" name="marks" id="marks1" value="4" 
                        @if($val=="4") checked="checked" @endif ><label for="marks1" >Rejected</label>
               </div><!-- End Of custom-radio-btn Div -->
               <div class="custom-radio-btn">
               <input type="radio" name="marks" id="marks2" value="5" 
              @if($val=="5") checked="checked" @endif ><label for="marks2" >Withdrawn</label>
               </div><!-- End Of custom-radio-btn Div -->
              <div class="custom-radio-btn">
                <input type="radio" name="marks" id="marks3" value="6" 
              @if($val=="6") checked="checked" @endif ><label for="marks3" >Accepted</label>
                
               </div><!-- End Of custom-radio-btn Div -->    
          </div>
             
          </td></tr>
         </table> 
               <div class="nomination-detail">
                I have examined this nomination paper in accordance with section 36 of the Representation of the People Act, 1951(43 of 1951) and decide as follows:— <span class="pagespanred">*</span>
              <textarea name="rejection_message" rows="5" cols="90" id="rejection_message">{{isset($caddata)?$caddata->rejection_message:old('rejection_message')}}</textarea>
                <span id="err"  style="color:red;"></span>
                @if ($errors->has('rejection_message'))  <span style="color:red;"><strong>{{ $errors->first('rejection_message') }}</strong></span>  @endif</div>
                <div class="nomination-detail"> Verify OTP Number <span class="pagespanred">*</span>
                <input type='text'  name="verifyotp" id="verifyotp" class="nomination-field-2" value="{{ old( 'verifyotp') }}"/>
                <span id="err1"  style="color:red;"></span>
                   @if ($errors->has('verifyotp'))  <span style="color:red;"><strong>{{ $errors->first('verifyotp') }}</strong></span>  @endif
               
                 <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Affidavit Public <span class="pagespanred">*</span> &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="affidavit" id="affidavit" value="1" checked="checked">
                <span id="err2"  style="color:red;"></span>
                  </span>
                            @if ($errors->has('affidavit'))
                                <span style="color:red;"><strong>{{ $errors->first('affidavit') }}</strong></span>
                            @endif 
                 </div> 
                 <div class="nomination-detail">      <div id="clockdiv"></div> </div>
              <div class="btns-actn">
                <?php  $url = URL::to("/");  ?>
                 <input type="submit" value="Submit">
                 <input type="button" value="Resend OTP" onclick="location.href = '{{$url}}/ropc/change-status/{{$lists->nom_id}}/{{$val}}';">
                 
              </div>
            </form>
              
      </div><!-- End Of datatable-wrap Div --> 
     
    </div><!-- End OF page-contant Div -->
    </div>          
  </div><!-- End Of parent-wrap Div -->
  </div> 
<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script>  
$(document).ready(function(){
 
  $("#election_form").submit(function(){
   
    if($("#rejection_message").val()=="")
    {
      $("#err").text("Please enter message");
      $("#rejection_message").focus();
      return false;
    }
     if($("#verifyotp").val()=="")
    {
      $("#err").text("");
      $("#err1").text("Please enter verifyotp");
      $("#verifyotp").focus();
      return false;
    } 
    /* if($("#affidavit").prop("checked", false))
    {
      $("#err").text("");$("#err1").text("");
      $("#err2").text("Please check affidavit");
      //$("#affidavit").focus();
      $("#affidavit").prop("checked", true)
      
    } */
    });
  });
</script>
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