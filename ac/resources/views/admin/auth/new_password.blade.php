@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
 
<main>
   <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
            <img src="{{ asset('admintheme/img/vendor/background.png') }}" alt=""></div>
            <div class="row loginTop">
             
              <div class="col ">
                 <div class=" btn-group float-right">
          <input type="button" class="btn btn-primary float-right mt-3" onclick="location.href = '{{$url}}';" value="Home">
          <input type="button" class="btn btn-warning float-right mt-3" onclick="location.href = '{{$url}}/officer-login';" value="Officer's Login">
        </div>
            </div>
            </div>
         
    <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">
  
  
   <div class="col-md-6 login-page "> 
  <figure class="evm-logo">
  <span style="margin: auto;"><img style="max-width:300px;" src="{{ asset('admintheme/img/logo/eci-logo.png') }}"> <!-- <p>Election Commission of India</p>  --></span></figure> </div>
    <div class="col-md-6">
    <div class="login-right">
   
   
                <div class="d-flex align-items-center mb-3">
                  <h1>Officer  <span>New Password</span></h1>
                </div>
               
 <form class="log-frm-area" method="POST" action="{{ $action }}" autocomplete='off' enctype="x-www-urlencoded">
                        {{ csrf_field() }}
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


      <div class="form-group">
        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" autofocus placeholder="New password"  autocomplete="off" >

        @if ($errors->has('password'))
          <span class="invalid-feedback"><strong>{{ $errors->first('password') }}</strong></span>
        @endif 
      </div>

      <div class="form-group">
        <input id="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" autofocus placeholder="Confirm password"  autocomplete="off" >

        @if ($errors->has('password_confirmation'))
          <span class="invalid-feedback"><strong>{{ $errors->first('password_confirmation') }}</strong></span>
        @endif 
      </div>
	 

    
                    <div class="form-group float-right"> 
                    
                    <input type="submit" class="btn btn-primary" value="Submit">

               
                    </div>
                  </form>
               
              
    
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