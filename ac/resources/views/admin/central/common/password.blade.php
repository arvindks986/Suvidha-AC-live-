@extends('admin.central.common.theme')
@section('title', 'Password Change')
@section('content')

 <main class="mb-auto">
     
      <!--main content start-->
       
 <main role="main" class="inner cover mb-3">


@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
            <?php $but = explode(':',$button); ?>
            <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>

            </span>
            
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

<section class="mt-4">
  <div class="container-fluid">
  
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
     <div class=" card-header">
    <div class=" row">
      <div class="col-md-4"><h4>{!! $heading_title !!}</h4></div> 
      <div class="col"><p class="mb-0 text-right">
      </p><div class="" style="width:100%; margin:0 auto;"></div>
      &nbsp;&nbsp;  
      <p></p>
      </div><!--end col-->
    </div> <!--end row-->
    </div><!--end card-header -->
      
    <div class="card-body">  

      <?php if(Auth::user()->role_id == '7'){ ?>

      <form id="change_password" method="POST" action="{!! $action !!}" autocomplete="off" onsubmit="return false;">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
      
      <?php }else{ ?>
        <form id="change_password" method="POST" action="{!! $action !!}" autocomplete="off" >
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
      <?php } ?>

        <div class="form-group row">
                                <label for="new-password" class="col-md-4 control-label">Current Password <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('old_password')){ echo 'is-invalid'; } ?>" name="old_password" value=""  autocomplete="off">
                                    @if ($errors->has('old_password'))
          <span class="newpassword errormsg errorred">{{ $errors->first('old_password') }}</span>
        @endif
                                                                    </div>
                           
                            </div>

                            <div class="form-group row">
                                <label for="new-password" class="col-md-4 control-label">New password <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('password')){ echo 'is-invalid'; } ?>" name="password" value="" autocomplete="off">
                                    @if ($errors->has('password'))
          <span class="newpassword errormsg errorred">{{ $errors->first('password') }}</span>
        @endif
                                                                    </div>
                                

                                


                            </div>

                            <div class="form-group row">
                                <label for="new-password-confirm" class="col-md-4 control-label">Confirm New password <sup>*</sup></label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('password_confirmation')){ echo 'is-invalid'; } ?>" name="password_confirmation" value="" autocomplete="off">
                                    @if ($errors->has('password_confirmation'))
          <span class="newpassword errormsg errorred">{{ $errors->first('password_confirmation') }}</span>
        @endif
                                </div>
                                
                            </div>

                            <div class="form-group float-right row">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary secure_pin_check">
                                        Update
                                    </button>
                                </div>
                            </div>

            </form></div><!-- end row-->
          </div> <!-- end COL-->
        </div>


    
    
  </div>
</section>

</main>
      <!--main content end-->
   
 </main>

 <div class="modal fade animated zoomIn" id="secure_pin_check" tabindex="-1" role="dialog" aria-labelledby="reset_pin_ceoLabel" aria-hidden="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div id="reset_pin_form" >
          <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        
        <div class="modal-header">
          Please Setup 2 step verification pin
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <label class="col-md-3">Pin</label>
            <div class="col-md-9">
            <input type="password" name="pin" value="" id="pin" class="form-control" autocomplete="off">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="secure_pin">Verify</button>
        </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')

@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif

<?php if(Auth::user()->role_id == '7'){ ?>
<script type="text/javascript">
$(document).ready(function(e){
  /*var i = 0;
  $('input').each(function(index,object){
    $(object).attr("autocomplete", i+Math.random().toString(36).substring(7)); 
  });*/

  $("#reset_pin_form input[name='pin']").keyup(function(e) {
    if (e.keyCode === 13) {
        $("#secure_pin").click();
    }
  });


  $('.secure_pin_check').click(function(e){
      $.ajax({
        url: "{!! url('/profile/password/validate') !!}",
        type: 'POST',
        data: '_token={!! csrf_token() !!}&old_password='+$('#change_password input[name="old_password"]').val()+'&password='+$('#change_password input[name="password"]').val()+'&password_confirmation='+$('#change_password input[name="password_confirmation"]').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#change_password .text-danger').remove();
          $('#change_password input').removeClass('is-invalid');
          $('.secure_pin_check').prop('disabled',true);
          $('.secure_pin_check').text("Validating...");
          $('.secure_pin_check').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

          
        
        },        
        success: function(json) {

          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#reset_pin_ceo').modal('hide');
            $('#secure_pin_check').modal('show');
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
            if(json['errors']['old_password']){
              $("#change_password input[name='old_password']").addClass("is-invalid");
              $("#change_password input[name='old_password']").after("<span class='text-danger'>"+json['errors']['old_password'][0]+"</span>");
            }
            if(json['errors']['password']){
              $("#change_password input[name='password']").addClass("is-invalid");
              $("#change_password input[name='password']").after("<span class='text-danger'>"+json['errors']['password'][0]+"</span>");
            }
            if(json['errors']['password_confirmation']){
              $("#change_password input[name='password_confirmation']").addClass("is-invalid");
              $("#change_password input[name='password_confirmation']").after("<span class='text-danger'>"+json['errors']['password_confirmation'][0]+"</span>");
            }
          }

          $('.secure_pin_check').prop('disabled',false);
          $('.secure_pin_check').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('.secure_pin_check').prop('disabled',false);
          $('.secure_pin_check').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
  });

  $('#secure_pin').click(function(e){
      $.ajax({
        url: "{!! url('/profile/pin/validate') !!}",
        type: 'POST',
        data: '_token={!! csrf_token() !!}&pin='+$('#reset_pin_form input[name="pin"]').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#reset_pin_form .text-danger').remove();
          $('#reset_pin_form input').removeClass('is-invalid');
          $('#secure_pin').prop('disabled',true);
          $('#secure_pin').text("Validating...");
          $('#secure_pin').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {

          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#secure_pin_check').modal('hide');
            success_messages(json['message']);

            $('#change_password input,  #reset_pin_form input').val('');
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
            if(json['errors']['pin']){
              $("#reset_pin_form input[name='pin']").addClass("input-error");
              $("#reset_pin_form input[name='pin']").after("<span class='text-danger'>"+json['errors']['pin'][0]+"</span>");
            }
          }

          $('#secure_pin').prop('disabled',false);
          $('#secure_pin').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#secure_pin').prop('disabled',false);
          $('#secure_pin').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
  });

});
</script>
<?php } ?>
@endsection