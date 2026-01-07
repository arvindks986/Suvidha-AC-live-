@extends('admin.layouts.ac.theme')
@section('title', 'Create Schedule')
@section('content') 
 <?php  $st=getstatebystatecode($st_code);  
          $ac=getacbyacno($st_code,$ac_no);
  ?>
 
<main role="main" class="inner cover mb-3">
 <section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h6 class="mr-auto">Counting Finalize for AC</h6></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
                        <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  </p></div>
         
                </div>
                </div>
    
   
       
    <div class="card-body">  
  <table   class="table table-striped table-bordered" style="width:100%">
        <thead><tr><th>Sr. No</th><th>Candidate Name</th><th>Party</th> <th>Evm Votes</th>
                 <th>Postal Votes</th><th>Total Votes</th> </tr>
        </thead>
      <tbody>
           <?php $j=0;  ?> 
            
              @if(!empty($master_data))
            @foreach($master_data as $md)  
            <?php $j++;  ?>
          <tr><td>{{$j}}</td> <td>{{$md->candidate_name}}</td> 
                <td>{{$md->party_name}}</td> <td>{{$md->total_vote-$md->postalballot_vote}}</td><td>{{ $md->postalballot_vote}}</td>
                 <td>{{$md->total_vote}}  </td> </tr>

            @endforeach 
            @endif 
             </tbody> 
            </table> 
             <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/counting-finalized-verify') }}" >
            {{ csrf_field() }}   <!--<input type="hidden" name="otp" value="{{$otp}}">  
       
     <!-- <div class="form-group">
          <label>Verify OTP Number :-<sup>*</sup></label>
              <input type='text'  name="verifyotp" id="verifyotp" lass="form-control" value="{{old('verifyotp') }}"/>
                <span id="err1"  style="color:red;"></span>
                   @if ($errors->has('verifyotp'))  <span style="color:red;"><strong>{{ $errors->first('verifyotp') }}</strong></span>  @endif
                 
                 <div id="clockdiv"></div>
      </div>-->
                 <?php  $url = URL::to("/");  ?>
              <div class="form-group float-right">  
                 <input type="submit" value="Finalize " class="btn btn-primary" onclick="return confirm('Do you really want to Finalize?');">
                  
                 <input type="button" value="Cancel" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/postal-data-entry';">
              </div>
             
      </form>
 </div>
</div>
</div></div>
</section>
</main>
     

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
 