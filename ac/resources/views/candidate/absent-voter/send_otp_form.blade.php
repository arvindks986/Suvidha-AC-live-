<?php 

 if(Session::get('DB_id') != '10'){
   Session::put('DB_id',10);
   Session::put('DB_DATABASE','suvidha_ac_2019_12_e6');
   Redirect::to('absentee-voters/get-otp-form');
 }

?>
@extends('candidate.common.app')
  @section('seo')
    <title>Absentee Voter Form - 12D</title>
  @endsection
  @section('style')
  <style type="text/css">
  .midBox {
    max-width: 570px;
    margin: auto;     box-shadow: 0 0 9px 4px #e5e5e5;
}
.loadbtn{
  bottom:0;
  font-size:24px;
  bottom: 5px;
  font-size: 24px;
  color: grey;
  position: absolute;
  left: -28px;
}
.error_message { text-transform: capitalize;}
.title-case{text-transform:capitalize;     padding: 6px 0 0 0;}
label{padding-top:10px; margin-bottom:0;}
</style>
  @endsection
@section('content')
{!!$header!!}
@include('candidate.common.breadcrumb')
<div class="midBox">
 <div class="row">

      <div class="col-md-12">
	     <form action="{{$action}}" method="post" class="mb-0">
        <div class="card" style="min-height:200px;">
         <div class="card-header d-flex align-items-center">
           <h4 class="title-case">{{$heading_title}}</h4>
         </div>
      
     <div class="card-body mb-0">
           <div class="row">
            
             <div class="col">
              <input type="hidden" name="_token" value="{!! csrf_token() !!}">

              <div class="form-group row">
       <label class="col-sm-4">Mobile Number<sup>*</sup></label>
       <div class="col">

        <div class="input-group">
  <input type="text" name="mobile" id="mobile" class="form-control" value="{{$mobile}}" placeholder="Mobile">
  <div class="input-group-append">
    <button class="btn btn-success" style="position:relative;" type="button" id="get_otp">Click Here To Get OTP</button>
  </div>
  @if ($errors->has('mobile'))
         <span class="error">{{ $errors->first('mobile') }}</span>
         @endif
</div>


  

       </div> 
     </div>

     <div class="form-group row show_on_success display_none align-items-center">
       <label class="col-sm-4">OTP<sup>*</sup></label>
       <div class="col">

  <input type="text" name="otp" id="otp" class="form-control" value="{{$otp}}" placeholder="Enter Valid OTP">

  @if ($errors->has('otp'))				
         <span class="error">{{ $errors->first('otp') }}</span>
         @endif



  

       </div> 
     </div></div> 



</div>
</div>
<div class="card-footer">
    <div class="row float-right show_on_success display_none align-items-center">       
  <div class="col">
   <button type="button" id="verify_otp" class="btn btn-primary">Verify OTP</button>
 </div>
</div></div>
</form>
</div>
</div>
</div>
</div>
   



      

{!!$footer!!}
@endsection
@section('footerscript')
<script type="text/javascript">
$('#get_otp').click(function(){
      $.ajax({
        url: "{!! $action !!}",
        type: 'POST',
        data: '_token=<?php echo csrf_token() ?>&mobile='+$('#mobile').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.error_message').remove();
          $('#get_otp').append("<i class='fa fa-circle-o-notch loading_spinner fa-spin load loadbtn' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {   
          if(json['success'] == false){
            if(json['errors']['mobile']){
               $('#mobile').parent('.input-group').after("<span class='text-danger error_message'>"+json['errors']['mobile']+"</span>");
            }

            if(json['errors']['warning']){
              error_messages(json['errors']['warning']);
            }
            
          }else{
            $('.show_on_success').removeClass("display_none");
          }      
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      });
    });

$('#verify_otp').click(function(){
      $.ajax({
        url: "{!! $action_verify_otp !!}",
        type: 'POST',
        data: '_token=<?php echo csrf_token() ?>&mobile='+$('#mobile').val()+'&otp='+$('#otp').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.error_message').remove();
          $('#verify_otp').append("<i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {   
          if(json['success'] == false){
            if(json['errors']['mobile']){
               $('#mobile').parent('.input-group').after("<span class='text-danger error_message'>"+json['errors']['mobile']+"</span>");
            }
            if(json['errors']['otp']){
               $('#otp').after("<span class='text-danger error_message'>"+json['errors']['otp']+"</span>");
            }
            if(json['errors']['warning']){
              error_messages(json['errors']['warning']);
            }
            
          }else{
            $('.show_on_success').removeClass("display_none");
            window.location.href = json.redirect_to;
          }      
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      });
    });

</script>
@endsection