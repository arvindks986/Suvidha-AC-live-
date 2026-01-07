@extends('admin.layouts.ac.theme')
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

      <form id="change_password" method="POST" action="{!! $action !!}" autocomplete="off">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
      
      <?php }else{ ?>
        <form id="change_password" method="POST" action="{!! $action !!}" autocomplete="off" >
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
      <?php } ?>
		@if($user_data->pass_flag==1)
        <div class="form-group row">
                                <label for="new-password" class="col-md-4 control-label">Current Password <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('old_password')){ echo 'is-invalid'; } ?>" onkeyup="CheckPasswordStrength(this.value,'oldpass')" name="old_password" id="old_password" value=""  autocomplete="off">
                                    @if ($errors->has('old_password'))
          <span class="newpassword errormsg errorred">{{ $errors->first('old_password') }}</span>
        @endif
		<span class="oldpass errormsg errorred" style="display:none;"></span>
                                                                    </div>
                           
                            </div>
							@endif

                            <div class="form-group row">
                                <label for="new-password" class="col-md-4 control-label">New password <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('password')){ echo 'is-invalid'; } ?>" onkeyup="CheckPasswordStrength(this.value,'newpass')" name="password" id="password" value="" autocomplete="off">
                                    @if ($errors->has('password'))
          <span class="newpassword errormsg errorred">{{ $errors->first('password') }}</span>
        @endif
		<span class="newpass errormsg errorred" style="display:none;"></span>
                                                                    </div>
                                

                                


                            </div>

                            <div class="form-group row">
                                <label for="new-password-confirm" class="col-md-4 control-label">Confirm New password <sup>*</sup></label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('password_confirmation')){ echo 'is-invalid'; } ?>" onkeyup="CheckPasswordStrength(this.value,'conpass')" name="password_confirmation" id="password_confirmation" value="" autocomplete="off">
                                    @if ($errors->has('password_confirmation'))
          <span class="newpassword errormsg errorred">{{ $errors->first('password_confirmation') }}</span>
        @endif
		<span class="conpass errormsg errorred" style="display:none;"></span>
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
<script src="{{ asset('theme/js/shaen.js') }}"></script>
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

<?php //if(Auth::user()->role_id == '7'){ ?>
<script type="text/javascript">
$(document).ready(function(e){
	var pass_flag = '<?php echo $user_data->pass_flag ?>';
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
	  if(pass_flag==1){
		  if($("#old_password").val()==''){
			  $("#old_password").focus();
			  $(".oldpass").show().text("Please enter old password");return false;
		  }else{
			  $(".oldpass").hide().text("");
		  }
	  }
	  
	  if($("#password").val()==''){
		  $("#password").focus();
		  $(".newpass").show().text("Please enter new password");return false;
	  }else{
		  $(".newpass").hide().text("");
	  }
	  
	  if($("#password_confirmation").val()==''){
		  $("#password_confirmation").focus();
		  $(".conpass").show().text("Please enter confirm password");return false;
	  }else{
		  $(".conpass").hide().text("");
		  if($("#password").val() != $("#password_confirmation").val()){
			$("#password_confirmation").focus();
			$(".conpass").show().text("Confirm password does not match.");return false;  
		  }else{
			$(".conpass").hide().text("");  
		  }
		  
	  }
	  if(pass_flag=='1'){
		var old_password = SHA256($("#old_password").val());
	  }
	  var password = SHA256($("#password").val());
	  var password_confirmation = SHA256($("#password_confirmation").val());
	  if(pass_flag=='1'){
		$("#old_password").val(SHA256(old_password+'<?php echo $xcs?>'));
	  }
	  $("#password").val(password);
	  $("#password_confirmation").val(password_confirmation);
	  
	  
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
            //$('#reset_pin_ceo').modal('hide');
            //$('#secure_pin_check').modal('show');
			$("#change_password").submit();
			return true;
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
			$("#old_password").val('');
			$("#password").val('');
			$("#password_confirmation").val('');
			
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
function CheckPasswordStrength(password,errClass) {
        //var password_strength = $(".errorred");
 
        //TextBox left blank.
        if (password.length == 0) {
            $("."+errClass).text("").css("color","red");
            return;
        }
 
        //Regular Expressions.
        var regex = new Array();
        regex.push("[A-Z]"); //Uppercase Alphabet.
        regex.push("[a-z]"); //Lowercase Alphabet.
        regex.push("[0-9]"); //Digit.
        regex.push("[$@$!%*#?&]"); //Special Character.
 
        var passed = 0;
 
        //Validate for each Regular Expression.
        for (var i = 0; i < regex.length; i++) {
            if (new RegExp(regex[i]).test(password)) {
                passed++;
            }
        }
 
        //Validate for length of Password.
        if (passed > 2 && password.length > 8) {
            passed++;
        }
		
        //Display status.
        var color = "";
        var strength = "";
        switch (passed) {
            case 0:
            case 1:
                strength = "Weak";
                color = "red";
                break;
            case 2:
                strength = "Good";
                color = "darkorange";
                break;
            case 3:
            case 4:
                strength = "Strong";
                color = "green";
                break;
            case 5:
                strength = "Very Strong";
                color = "darkgreen";
                break;
        }
        $("."+errClass).text(strength).css("color",color).show();
        //password_strength.style.color = color;
}
</script>
<?php //} ?>
@endsection
