@extends('admin.layouts.pc.theme')
@section('title', 'Create Schedule')
@section('content') 
 

 <section class="tabs-data">
<div class="card text-left" style="max-width:40%; margin:0 auto">
                <div class="card-header ">
                  <h2 class="">Round Schedule AC Wise</h2>
                </div>
    @if(Session::has('success_admin'))
      <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
    @endif   
    @if(Session::has('unsuccess_insert'))
     <div class="alert alert-danger"><strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong></div>
    @endif 
                <div class="card-body">                 
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/round-schedule') }}" >
                {{ csrf_field() }}
            @if(isset($rid))  
              <input type="hidden" class="form-control" name="rid" id="rid" value="{{$rid}}">
            @endif
              <div class="form-group">
                      <label>Enter Total No. of Rounds Scheduled for this AC <sup>*</sup></label>
                      <input type="text" maxlength="2" placeholder="Enter Total No. of Rounds Scheduled for this AC" class="form-control" name="scheduled_round" id="scheduled_round"   readonly="readonly"  value="">
        
            
        
                    </div>
                    <span id="errmsg" class="text-danger"></span>  
              
               
                    <div class="form-group float-right">       
                      <input type="submit" value="Submit" placeholder="" class="btn btn-primary">
                    </div>
               
                
                     
                  </form>
                </div>
              </div>
</section>

  
@endsection
<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script type="text/javascript">
   $(document).ready(function () {  
  //called when key is pressed in textbox
  $("#scheduled_round").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
  $("#election_form").submit(function(){
    
     if($("#scheduled_round").val()=="")
    {
      $("#errmsg").text("");
      $("#errmsg").text("Please enter round schedule");
      $("#scheduled_round").focus();
      return false;
    } 
      
    });
});
 </script>