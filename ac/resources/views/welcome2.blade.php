@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
 
<main>
   <section class="main-box">
   
     <div class="circle peach-gradient"><img src="{{ asset('theme/img/vendor/background.png') }}" alt=""></div>
	  <div class="container-fluid h-100">
            <div class="row justify-content-center align-items-center h-100" style="width:100%; margin:0 auto;">
             
              <div class="col-md-6 login-page">
			<figure class="evm-logo officerlogin">
				  <span style="margin: auto;">
 @if($url=="https://suvidha.eci.gov.in/suvidhaac/public" || $url=="http://suvidha.eci.gov.in/suvidhaac/public" || $url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")
 
   <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
  @else
    <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
  @endif
   <p>Election Commission of India </p> </span></figure> </div>
			
				<div class="col-md-6 loginDiv">
				<div class="login-right">
				<fieldset>
				
					<legend class="text-center mb-2"> 
			   
			 
					
			 

					
					</legend>
					 <legend class="text-center">Officer Login</legend>	
					 <form class="log-frm-area" method="POST" action="{{ url('/auth/login/two_step_login3') }}" autocomplete='off' enctype="x-www-urlencoded">
                        {{ csrf_field() }}
    @if (session('data_username'))
        <div class="alert alert-danger"> {{session('data_username') }}</div>
    @endif
    <span class="help-block"> 
        <strong>{{ Session::get('log_message') }}</strong>
    </span>
      <div class="form-group">
        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{old('username')}}"  autofocus placeholder="User Name"  autocomplete="off" >

        @if ($errors->has('username'))
          <span class="invalid-feedback"><strong>{{ $errors->first('username') }}</strong>
                      </span>
        @endif 
      </div>


	      <div class="form-group"> 
            <input id="password" type="password" class="form-control{{$errors->has('password') ? ' is-invalid' : '' }}" name="password"  placeholder="Password" autocomplete="new-password" autocomplete="off" >

           @if ($errors->has('password'))
              <span class="invalid-feedback">
                  <strong>{{ $errors->first('password') }}</strong>
              </span>
           @endif
          </div>
	 
	 <div class="form-group  d-flex flex-column flex-md-row align-items-center mb-3">
                   <div class="col col-xs-12 m-0 p-0">
						
						</div>
						<div class="col col-xs-12 pr-0 d-flex align-items-center capchtainpyt">
						 

						&nbsp;&nbsp;<input type="submit" class="btn btn-primary" value="Login"/>
					</div>
						 
                      
                        
                          <!--<button type="button" id="btn-refresh" class="btn btn-success btn-refresh" onclick="refereshcaptch();"><i class="fa fa-refresh"></i> Referesh</button>
                          -->
</div>
	  </form>
				</fieldset>	
				</div>
				</div>
			
			
			
            </div>
         
   
        </div>
   </section>
    
  </main>
@endsection
@section('script')
<script>

 /*function refereshcaptch(){    
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/refresh_captcha",           
       success: function (data) {
         jQuery("#captcha").html(data.captcha);
       },
       error: function (data, textStatus, errorThrown) {
             //do something

       }
           });
    }*/

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
</script>
@endsection