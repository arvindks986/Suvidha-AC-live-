@extends('admin.layouts.ac.theme')
@section('title', 'Create Schedule')
@section('content') 
<style>
.modal-big .modal-dialog{max-width: 900px;}
.modal-big .modal-header{background-color: #f0587e; color: #fff; text-shadow: 1px 1px 1px #666; text-align: center;}
.mcenter{font-size:18px; line-height: 30px;}
</style>
<main role="main" class="inner cover mb-3">
 <section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h6 class="mr-auto">Counting Finalize for AC</h6></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
                        <span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp;  </p></div>
         
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
          <tr><td>{{$j}}</td> <td>{{$md->candidate_name}} <br>{{$md->candidate_hname}} 
                                  @if($md->nom_id==$winn_data->nomination_id) <b>(Leading) </b>  @endif
                                  @if($md->nom_id==$winn_data->trail_nomination_id) <b>(Trailing)</b>  @endif</td>   
                                  <td>{{$md->party_name}} <br>{{$md->party_hname}} </td> 
                 <td>{{$md->total_vote-$md->postalballot_vote}}</td><td>{{ $md->postalballot_vote}}</td>
                 <td>{{$md->total_vote}}  </td> </tr>

            @endforeach 
            @endif 
             </tbody> 
            </table> 
             <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/postal-counting-finalized-verify') }}" >
            {{ csrf_field() }}    
                 <?php  $url = URL::to("/");  ?>
              <div class="form-group float-right">  
                <input type="button" value="Cancel" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/bpostal-data-entry';">

                 <input type="button" value="Verify & Finalize" class="btn btn-primary" id="preview_submit" data-toggle="modal" data-target="#changestatus">
                  
              </div>
             
      </form>
 </div>
</div>
</div></div>
</section>
</main>
<!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal modal-big fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Declaration Alert</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form1" method="get">
                 {{csrf_field()}} 
				 <input type="hidden" id="roname" name="roname">
   <div class="mb-3">
     <ol class="mcenter">
      <li> &nbsp; I, <strong>{{Auth::user()->name}}</strong> certify that Postal ballot votes has been compared and matches with the Form-20 compiled manually.</li>

     <li> &nbsp;  I, understand that upon pressing the 'Finalize' button below,editing can't be done.</li>
    </ol>
		<ol class="mcenter">
      <p align="left"><input type="checkbox" name="verify">&nbsp;&nbsp;I agree to finalize the postal ballot votes </p>
      <p align="left"><span id="errorMsg" class="red"></span></p>
	  </ol>
      </div>
    
    
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="submit_final_form" class="btn btn-success submit-button">Verify & Finalize</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->     

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
 

 @section('script')
<script type="text/javascript">
$(document).ready(function(e){
    /*$('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to Finalize the Vote Count. Upon Finalization Changes can't be done and the same data will be reflected in trends and result Website.")){
      $(this).text('Processing...');
      $(this).prop('disabled',true);
      $("#election_form").submit();
    }else{

    }
  });*/
  
  $('#submit_final_form').click(function(e){
	var atLeastOneIsChecked = $('input[name="verify"]:checked').length > 0;
	if(atLeastOneIsChecked === false){
		$("#errorMsg").text("Please agree to finalize the postal ballot votes.");
		return false;
	}else{
		$(this).prop('disabled',true);
		$("#election_form").submit();
	}
	
  });

});
</script>
@endsection
