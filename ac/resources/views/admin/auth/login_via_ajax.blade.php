@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
 
<style type="text/css">
  
  .captcha #captcha img {
    min-height: 44px;
    margin-top: 3px;
}
</style>


<main>
   <section class="main-box">
     <div class="circle peach-gradient">
            <img src="{{ asset('admintheme/img/vendor/background.png') }}" alt=""></div>
    <div class="container-fluid h-100">
   
         
         
    <div class="row justify-content-center align-items-center h-100" style="width:100%; margin:0 auto;">
  
  <div class="col-md-6 login-page "> 
        <figure class="evm-logo officerlogin">
          <span style="margin: auto;"><img class="logoSize" src="{{ asset('admintheme/img/logo/eci-logo.png') }}">
		  <p class="infoTag">General Election to the House<br><span class="Span">of People 2019</span></p></span>
		  </figure> 
      </div>
	  
	
	
	
  
    <div class="col-md-6 loginDiv">
    <div class="login-right">
   
   <fieldset>
   <legend class="text-center mb-2"> 
   
 
   <div class=" btn-group main-nav">
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}';" value="Home"/> 
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/login';" value="Candidate Login"/> 
          <input type="button" class="btn btn-link active"  value="Officer Login"/>
		  
        </div>
 

        
		</legend>
  <legend class="text-center login_for_office">Login For Officer</legend>
       <!--    <h3 class="display 1">Officer Login</h3>   -->
               
<div class="pos_relative" style="position: relative;overflow: hidden;">
 <form class="log-frm-area" id="login_via_ajax" method="POST" onsubmit="return false;" autocomplete='off' enctype="x-www-urlencoded">              
    @if (session('data_username'))
        <div class="alert alert-danger"> {{session('data_username') }}</div>
    @endif
    <span class="help-block"> 
        <strong>{{ Session::get('log_message') }}</strong>
    </span>
    @if(Session::has('flash-message'))
      @if(Session::has('status'))
        <?php
        $status = Session::get('status');
        if($status==1){
          $class = 'alert-success';
        }
        else{
          $class = 'alert-danger';
        }
        ?>
      @endif
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
      </div>
    @endif
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
      <div class="form-group">
        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{old('username')}}"  autofocus placeholder="User Name"  autocomplete="off" >
      </div>

      <?php if(isset($skip_password_network) && $skip_password_network == true){ ?>

      <?php }else{ ?>
      <div class="form-group"> 
          <input id="password" type="password" class="form-control{{$errors->has('password') ? ' is-invalid' : '' }}" name="password"  placeholder="Password" autocomplete="new-password" autocomplete="off" >
      </div>
      <?php } ?>
     
	 
	   <div class="form-group  d-flex flex-column flex-md-row align-items-center mb-3">
                    <div class="col col-xs-12 m-0 p-0"> 
						<div class="captcha">
								<span id="captcha"><img id="refresh" src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default"></span>
                    <button type="button" data-refresh-config="default" id="btn-refresh" class="btn btn-success btn-refresh captcha-img refresh"><i class="fa fa-refresh"></i> Refresh</button>
								  
								    
						</div>
					</div>
						 
        <div class="col  pr-0 d-flex align-items-center capchtainpyt">  	

          <div class="row">
         <input id="lcaptcha" type="text" class=" col-md-7 form-control{{$errors->has('lcaptcha') ? ' is-invalid' : '' }}" name="lcaptcha"  placeholder="captcha"   autocomplete="off"/>&nbsp;
				<button type="submit" class="btn btn-primary col-md-4" id="login">Submit</button>

       </div>
				        </div>              
        </div>
	 		 <div class="row">
					
						  
						 </div>
            
            <div class="row">
   	          <div class="col captcha_error">
						  
						  </div>
              <div class="col form-group"> 
                <small></small>
              </div>
            </div>
	 
 </form>


<form class="log-frm-area display_none" id="login_via_two_step" method="POST" onsubmit="return false;" autocomplete='off' enctype="x-www-urlencoded">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
      <div class="form-group d-flex flex-column flex-md-row align-items-center">
        <input id="pin" type="password" class="form-control" name="pin" value="" placeholder="Enter your 4 digits pin"  autocomplete="off" >
     
   &nbsp;
      <button type="submit" class="btn btn-primary" id="two_step_button" style="width: 150px;">Login</button>
  
 </div>

   
 </form>
</div>


               
               </fieldset>
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
   <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
      <div class="col"></div>
            <div class="col">
              <figure class="foot-lft"><img src="{{ asset('theme/img/vendor/footer-img.png')}}"></figure>
            </div>
            <div class="col text-right">

				<a style="color:#bbb;float:right;" href="https://eci.gov.in/divisions-of-eci/ict-apps/" target="_blank">Made In ECI</a>
   
            </div>
          </div>
        </div>
      </footer>  
  </main>





@endsection
@section('script')
<script type="text/javascript">
  $('.captcha-img').on('click', function () {
     var captcha = $(this);
     var config = captcha.data('refresh-config');
     $.ajax({
         method: 'GET',
         url: APP_URL +'/get_captcha/' + config,
     }).done(function (response) {
         $('#refresh').prop('src', response);
     });
  }); 

  $(document).ready(function(e){
    $('#login_via_ajax #login').click(function(){
       $.ajax({
        url: "{!! url('/auth/login/step1') !!}",
        type: 'POST',
        data: "_token="+$("#login_via_ajax input[name='_token']").val()+"&username="+$("#login_via_ajax input[name='username']").val()+"&password="+$("#login_via_ajax input[name='password']").val()+"&lcaptcha="+$("#login_via_ajax input[name='lcaptcha']").val(),
        dataType: 'json', 
        beforeSend: function() {
          $('#login_via_ajax .invalid-feedback').remove();
          $('#login_via_ajax input').removeClass('is-invalid');
          $('#login_via_ajax #login').prop('disabled',true);
          $('#login_via_ajax #login').text("Validating...");
          $('#login_via_ajax #login').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
      
          if(json['status'] == true){
            const element =  document.querySelector('#login_via_ajax');
            element.classList.add('animated', 'bounceOutLeft');
            element.addEventListener('animationend', function() { 
              $('#login_via_ajax').addClass("display_none");
              $('#login_via_two_step').removeClass("display_none"); 
              $('.login_for_office').text("Enter your 4 digit PIN");
            });
          }

          if(json['status'] == false){
            if(json['errors'] && json['errors']['username']){
              $("#login_via_ajax input[name='username']").addClass("is-invalid");
              $("#login_via_ajax input[name='username']").after("<span class='invalid-feedback'><strong>"+json['errors']['username'][0]+"<strong></span>");
            }
            if(json['errors'] && json['errors']['password']){
              $("#login_via_ajax input[name='password']").addClass("is-invalid");
              $("#login_via_ajax input[name='password']").after("<span class='invalid-feedback'><strong>"+json['errors']['password'][0]+"<strong></span>");
            }
            if(json['errors'] && json['errors']['lcaptcha']){
              $("#login_via_ajax input[name='lcaptcha']").addClass("is-invalid");
              $("#login_via_ajax .captcha_error").html("<span class='invalid-feedback'><strong>"+json['errors']['lcaptcha'][0]+"<strong></span>");
            }
            if(json['message']){
              $("#login_via_ajax input").addClass("is-invalid");
              error_messages(json['message']);
            }
            
            $(".captcha-img").click();
            $("#login_via_ajax input[name='lcaptcha']").val('');
          }

          $('#login_via_ajax #login').prop('disabled',false);
          $('#login_via_ajax #login').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#login_via_ajax #login').prop('disabled',false);
          $('#login_via_ajax #login').text("Submit");
          $('.loading_spinner').remove();
          error_messages("Please try again.");
        }
      }); 
    });

    $('#login_via_two_step #two_step_button').click(function(){
       $.ajax({
        url: "{!! url('/auth/login/step2') !!}",
        type: 'POST',
        data: "_token="+$("#login_via_two_step input[name='_token']").val()+"&pin="+$("#login_via_two_step input[name='pin']").val(),
        dataType: 'json', 
        beforeSend: function() {
          $('#login_via_two_step .invalid-feedback').remove();
          $('#login_via_two_step input').removeClass('is-invalid');
          $('#login_via_two_step #two_step_button').prop('disabled',true);
          $('#login_via_two_step #two_step_button').text("Validating...");
          $('#login_via_two_step #two_step_button').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
      
          if(json['status'] == true){
            location.reload();
          }

          if(json['status'] == false){
            if(json['errors'] && json['errors']['pin']){
              $("#login_via_two_step input[name='pin']").addClass("is-invalid");
              $("#login_via_two_step input[name='pin']").parent('.form-group').after("<span class='invalid-feedback'><strong>"+json['errors']['pin'][0]+"<strong></span>");
            }
            if(json['message']){
              $("#login_via_two_step input").addClass("is-invalid");
              error_messages(json['message']);
            }
          }

          $('#login_via_two_step #two_step_button').prop('disabled',false);
          $('#login_via_two_step #two_step_button').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#login_via_two_step #two_step_button').prop('disabled',false);
          $('#login_via_two_step #two_step_button').text("Submit");
          $('.loading_spinner').remove();
          error_messages("Please try again.");
        }
      }); 
    });



  });
</script>
@endsection