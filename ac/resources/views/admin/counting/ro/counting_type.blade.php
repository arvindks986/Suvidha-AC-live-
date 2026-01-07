@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Counting Type')
@section('content') 
 

 <section class="tabs-data cover-container d-flex w-80 h-80 p-3 mx-auto flex-column" style="height: 60%;">
<div class="card text-left size-1" style=" margin:auto">
                <div class="card-header ">
                  <h4 class="">RO Counting Type</h4>
                </div>
    @if(Session::has('success_admin'))
      <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
    @endif 
     @if(Session::has('error_mes'))
     <div class="alert alert-danger"><strong> {{ nl2br(Session::get('error_mes')) }}</strong></div>
    @endif 
     
    <div class="card-body">                 
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/verifycounting-type') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{ csrf_field() }}
            @if(!empty($counting_type))  
              <input type="hidden" class="form-control" name="id" id="id" value="{{$counting_type->id}}">
            @endif
             
              <div class="form-group">
                <label>Select Counting Type <sup>*</sup></label><br>
                 
                 <label><input type="radio" name="counting_type" id="counting_type" value="0" @if(!empty($counting_type)) @if($counting_type->counting_type==0) checked="checked" @endif @endif> Table Wise Counting &nbsp;&nbsp;&nbsp;&nbsp; </label>
                <label><input type="radio" id="counting_type" name="counting_type" value="1" @if(!empty($counting_type)) @if($counting_type->counting_type==1) checked="checked" @endif @endif> By Pass Table wise Counting  &nbsp;&nbsp;&nbsp;&nbsp; </label>      
               <!--  <label><input type="radio" id="counting_type" name="counting_type" value="2" @if(!empty($counting_type)) @if($counting_type->counting_type==2) checked="checked" @endif @endif > Both  &nbsp;&nbsp;&nbsp;&nbsp; <br></label> -->
                        
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
 @section('script')
<script type="text/javascript">
   $(document).ready(function () {  
 
  $("#election_form").submit(function(){
    if(!$("input[name='counting_type']").is(':checked')){  
          $("#errmsg").text("");
          $("#errmsg").text("Please select counting type");
          $("#counting_type").focus();
          return false;
         }
      
    });
});
 </script>
 @endsection