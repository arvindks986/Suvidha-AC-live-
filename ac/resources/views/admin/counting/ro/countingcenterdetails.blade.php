@extends('admin.layouts.ac.theme')
@section('title', 'Table Wise Counting')
@section('bradcome', 'Counting Center Details')
@section('content') 
 <?php  $url = URL::to("/");    ?>
<section class="tabs-data cover-container d-flex w-80 h-80 p-3 mx-auto flex-column" style="height: 60%;">
<div class="card text-left size-1" style=" margin:auto">
                <div class="card-header ">
                  <h4 class="">Counting Center Information</h4>
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
        <form class="form-horizontal" id="election_form" method="POST"  action="{{$action}}" autocomplete='off' enctype="x-www-urlencoded">
                {{ csrf_field() }}
            @if(isset($table_details))  
              <input type="hidden" class="form-control" name="id" id="id" value="{{$table_details->id}}">
            @endif
             
              <div class="form-group">
                      <label><b>Enter number of Polling Station of your AC</b><sup>*</sup> </label>
                       
                      <input type="text" maxlength="4" placeholder="Number Of Polling Station" class="form-control" name="total_no_ps" id="total_no_ps"    value="{{isset($noofps)?$noofps:old('total_no_ps') }}"  readonly="readonly">
        @if ($errors->has('total_no_ps'))
            <span class="text-danger">{{ $errors->first('total_no_ps') }}</span>
        @endif         <span id="errmsg" class="text-danger"></span> 
                    </div>
              <div class="form-group">
                      <label><b>Enter total number of counting table in Counting centre</b><sup>*</sup> 
                        </label>
                        <input type="text" maxlength="2" placeholder="Number of Table in Counting centre" class="form-control" name="total_no_tables" @if($evmfinalized==1) readonly="readonly" @endif
                        id="total_no_tables" value="{{isset($table_details)?$table_details->total_no_tables:old('total_no_tables') }}" >
                        @if ($errors->has('total_no_tables'))
                            <span class="text-danger">{{ $errors->first('total_no_tables') }}</span>
                        @endif         
                       <span id="errmsg2" class="text-danger"></span> 
              </div>
              <div class="form-group">
                      <label><b>Number of rounds for counting</b><sup>*</sup></label>
                      <br>
                      <span id="noofrounds">@if(isset($table_details)) {{$table_details->total_no_rounds}} @endif</span> 
                        <input type="hidden" maxlength="3" placeholder="Number of Rounds" class="form-control" name="total_no_rounds"  
                        id="total_no_rounds" value="{{isset($table_details)?$table_details->total_no_rounds:old('total_no_rounds') }}" readonly="readonly">
                        @if ($errors->has('total_no_rounds'))
                            <span class="text-danger">{{ $errors->first('total_no_rounds')}}</span>
                        @endif         
                       <span id="errmsg3" class="text-danger"></span> 
              </div>       
                 
              
				<div class="card-footer text-center pt-1 pb-1 ">	
				
				 
				
        <div class="row BtnRds"> 				
						<input type="button" value="Back" class="btn btn-primary col" onclick="location.href = '{{$url}}/roac/counting/counting-user';">
           @if($evmfinalized==0)  
          @if(!isset($countingstart)) 
						<input type="submit" value="Submit" placeholder="" class="btn btn-success submit-button col">
          @endif
           @endif
						<input type="button" value="Next Page" class="btn btn-primary col" onclick="location.href = '{{$url}}/roac/counting/round-schedule-details';">
        </div>
       
        </div>
           
                  </form>
      </div> <!--  card-body -->                
 </div>
</section>


@endsection
@section('script')

<script type="text/javascript">
   $(document).ready(function () {  
  //called when key is pressed in textbox 
   
  $("#total_no_ps").keypress(function (e) {   
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
         return false;
    }
   });
  $("#total_no_tables").keypress(function (e) {   
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg2").html("Digits Only").show().fadeOut("slow");
         return false;
    }
      
   });

      $('#total_no_tables').keyup(function() {
          var psno= $("#total_no_ps").val();
          var tableno= $("#total_no_tables").val();
          var rounds=Math.ceil(psno/tableno);
		  if(rounds >= 130){
			   rounds = '130';
		   }
          if(psno==0 || tableno==0){
              $('#election_form #total_no_rounds').val('');
              $('#election_form #noofrounds').html('');
              $("#errmsg2").text("");
             $("#errmsg2").text("Please enter valid no of tables");
          }else{
			  $("#errmsg2").text("");
           $('#election_form #total_no_rounds').val(rounds);
		   
           $('#election_form #noofrounds').html(rounds);
         }
      });
   $('#total_no_ps').keyup(function() {
          var psno= $("#total_no_ps").val();
          var tableno= $("#total_no_tables").val();
          var rounds=Math.ceil(psno/tableno);
		  if(rounds >= 130){
			   rounds = '130';
		   }
          if(psno==0 || tableno==0){
              $('#election_form #total_no_rounds').val('');
              $('#election_form #noofrounds').html('');
              $("#errmsg").text("");
              $("#errmsg").text("Please enter valid no PS");
          }else{
			  $("#errmsg2").text("");
           $('#election_form #total_no_rounds').val(rounds);
           $('#election_form #noofrounds').html(rounds);
         } 
      });
  $("#total_no_rounds").keypress(function (e) {   
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
              $("#errmsg3").html("Digits Only").show().fadeOut("slow");
              return false;
            }
    });

  $("#election_form").submit(function(){
    
     if($("#total_no_ps").val()=="")
    {
      $("#errmsg").text("");
      $("#errmsg").text("Please enter total no PS");
      $("#total_no_ps").focus();
      return false;
    } 
     if($("#total_no_tables").val()=="")
      {
      $("#errmsg2").text("");
      $("#errmsg2").text("Please enter total no of tables");
      $("#total_no_tables").focus();
      return false;
      } 
      //  if($("#total_no_tables").val()<14)
      // {
      // $("#errmsg2").text("");
      // $("#errmsg2").text("Please enter minumum 14 no of tables");
      // $("#total_no_tables").focus();
      // return false;
      // } 
      if($("#total_no_rounds").val()=="")
      {
      $("#errmsg3").text("");
      $("#errmsg3").text("Please enter rounds");
      $("#total_no_rounds").focus();
      return false;
      }
    });
});
 </script>
 @endsection
