@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Round Schedule AC Wise')
@section('content') 
 
<?php  $url = URL::to("/");    ?>
 <section class="tabs-data cover-container d-flex w-80 h-80 p-3 mx-auto flex-column" style="height: 60%;">
<div class="card text-left size-1" style=" margin:auto">
                <div class="card-header ">
                  <h4 class="">Round Schedule AC Wise</h4>
                </div>
    @if(Session::has('success_admin'))
      <div class="alert alert-success mb-3"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
    @endif 
     @if(Session::has('error_mes'))
     <div class="alert alert-danger mb-3"><strong> {{ nl2br(Session::get('error_mes')) }}</strong></div>
    @endif 
    @if(Session::has('unsuccess_insert'))
     <div class="alert alert-danger mb-3"><strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong></div>
    @endif 
                <div class="card-body">                 
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/verifyround-schedule') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{ csrf_field() }}
            @if(isset($rid))  
              <input type="hidden" class="form-control" name="rid" id="rid" value="{{$rid}}">
            @endif
            <input type="hidden" maxlength="3"  class="form-control" name="scheduled_round1" id="scheduled_round1" readonly="readonly" value="{{isset($list)?$list->scheduled_round:old('scheduled_round') }}">
              <div class="form-group">
                      <label>Enter number of Rounds Scheduled for your AC <sup>*</sup></label>
                      <input type="text" maxlength="3" placeholder="Enter Total No. of Rounds Scheduled for this AC" class="form-control" name="scheduled_round" id="scheduled_round"  @if($finalized_round==1) readonly="readonly" @endif value="{{isset($list)?$list->scheduled_round:old('scheduled_round') }}">
        @if ($errors->has('scheduled_round'))
            <span class="text-danger">{{ $errors->first('scheduled_round') }}</span>
        @endif
                    </div>
                    <span id="errmsg" class="text-danger"></span>  
              <?php if(!isset($list)) { $ac=0; } elseif($list->finalized_ac==0)  {$ac=0;} else {$ac=1;}?>
          </div>
                   <div class="card-footer text-center pt-1 pb-1 ">
                     <div class="row BtnRds"> 
                    <input type="button" value="Back" class="btn btn-primary col" onclick="location.href = '{{$url}}/roac/counting/counting-center-details';">
                     @if($finalized_round==0)
                      <input type="submit" value="Submit" placeholder="" class="btn btn-success submit-button col">
                     @endif
                      <input type="button" value="Next Page" class="btn btn-primary col" onclick="location.href = '{{$url}}/roac/counting/user-assign-table-details';">
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