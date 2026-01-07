@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
 
<main>
   <section class="main-box">
    <div class="container-fluid h-100">
     <div class="circle peach-gradient">
            <img src="{{ asset('admintheme/img/vendor/background.png') }}" alt=""></div>
         
         
    <div class="row justify-content-center align-items-center h-100">
  
  
    <div class="col-md-6 login-page "> 
        <figure class="evm-logo officerlogin">
          <span style="margin: auto;"><img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}"><p class="infoTag">General Election to the House<br><span class="Span">of People 2019</span></p></span></figure> 
      </div>
    <div class="col-md-6 loginDiv">
    <div class="login-right">
   
   <fieldset>
		<legend class="text-center">
		       <div class="btn-group main-nav">
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}';" value="Home">
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/officer-login';" value="Back">
        </div>
		</legend>
  

   <legend class="text-center">Officer Forgot Password</legend>
    <?php if(isset($get_election_form)){ ?>
      {!! $get_election_form !!}
    <?php } ?>


@if(Session::has('DB_id'))
    <form class="log-frm-area" method="POST" action="{{ url('/forgot/post') }}" autocomplete='off' enctype="x-www-urlencoded">
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
        <input id="email" type="text" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{old('mobile')}}"  autofocus placeholder="Enter your mobile number"  autocomplete="off" >

        @if ($errors->has('email'))
          <span class="invalid-feedback"><strong>{{ $errors->first('email') }}</strong></span>
        @elseif($errors->has('mobile'))
          <span class="invalid-feedback"><strong>{{ $errors->first('mobile') }}</strong></span>
        @endif 
      </div>
	 
	 <div class="form-group  d-flex flex-column flex-md-row align-items-center mb-3">
                    <div class="col-md-8 m-0 p-0"> 
						<div class="captcha">
								<span id="captcha"><img id="refresh" src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default"></span>
                    <button type="button" data-refresh-config="default" id="btn-refresh" class="btn btn-success btn-refresh captcha-img"><i class="fa fa-refresh"></i> Refresh</button>
								  
								    
						</div>
					</div>
						 
                        <div class="col m-0 p-0">  	  <input id="lcaptcha" type="text" class="form-control{{$errors->has('lcaptcha') ? ' is-invalid' : '' }}" name="lcaptcha"  placeholder="captcha"   autocomplete="off" >

           @if ($errors->has('lcaptcha'))
              <span class="invalid-feedback captchaerror">
                  <strong>{{ $errors->first('lcaptcha') }}</strong>
              </span>
           @endif</div>
                        
                            

                                                 
                  </div>
	 
	 

                    <div class="form-group float-right"> 
                    
                    <input type="submit" class="btn btn-primary" value="Submit">

               
                    </div>
                  </form>
                  @endif
               
           </fieldset>   
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
  <!--   <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
      <div class="col"></div>
            <div class="col">
              <figure class="foot-lft"><img src="{{ asset('admintheme/img/vendor/footer-img.png')}}"></figure>
            </div>
            <div class="col text-right">
      
       
       <nav>
       <a href="#">Privacy Policy</a> &nbsp; | &nbsp; 
       <a href="#">Term &amp; Conditions</a> &nbsp; | &nbsp;   
       <a href="#">About ECI</a>
       </nav>
       
      
              <div class="copyright small">Copyright @2019  Election Commission of India. All rights reserved.</div>
               
            </div>
          </div>
        </div>
      </footer>  -->
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