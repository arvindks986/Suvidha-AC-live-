@extends('admin.layouts.login')
 
@section('content')

<main>  
  <div class="container">
   <div class="wrap">
     <div class="logn-form">
	     <figure class="evm-logo"><img src="{{ asset('admintheme/images/logo/foot-logo-eci.png') }}"></figure>
       
       <form class="log-frm-area" method="POST" action="{{ url('/admin-postlogin') }}" autocomplete='off' enctype="x-www-urlencoded">
                        {{ csrf_field() }}
                          <span class="help-block">
                              <strong>{{ Session::get('data_username') }}{{ Session::get('data_mismatch') }}</strong>
                          </span>
                        <div class="form-group">
                            
                                <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}"  autofocus placeholder="user Name"  autocomplete="off" >

                                    @if ($errors->has('username'))
                                        <span class="invalid-feedback">
                                                     <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                            
                        </div>

                        <div class="form-group">
                             
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"  placeholder="Password" autocomplete="new-password" autocomplete="off" >

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                             
                        </div>
                       <div class="form-group {{ $errors->has('captcha') ? ' has-error' : '' }}">
                        
                          
                        <span id="captcha">{!! captcha_img() !!}</span>
                         <span id="ref-btn">
                              <img class="btn-refresh" id="btn-refresh" src="{{ asset('admintheme/images/refresh.png') }}" title="Refresh" onclick="refereshcaptch();" alt="Refresh">
                        </span>
                          <!--<button type="button" id="btn-refresh" class="btn btn-success btn-refresh" onclick="refereshcaptch();"><i class="fa fa-refresh"></i> Referesh</button>
                          -->

                          <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha">
                           

                          @if ($errors->has('captcha'))
                              <span class="invalid-feedback"> <strong>{{ $errors->first('captcha') }}</strong>
                              </span>
                          @endif
                       
                  </div>
                         <!--<div class="form-group">
                             <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                                    
                        </div>-->
                    <!--<div class="frgt-pass"><a href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}</a></div>-->
                       <div class="actn-btn"> 
                                    <input type="submit" class="btn" value="Login">
                                 
                        </div>
                    </form>

       
     </div><!-- End Of logn-form div -->
   </div><!-- End Of wrap div -->   
  </div><!-- End Of container div -->
</main>
<footer class="foter-fixed">
 <div class="container-fluid">
  <div class="row">
    <div class="col-lg-3 col-md-3">
      <figure class="foot-lft"><img src="{{ asset('admintheme/images/logo/foot-logo-eci.png') }}"></figure>
    </div>
    <div class="col-lg-6 col-md-6">
      <div class="foot-mid">
        <ul>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Term & Conditions</a></li>
          <li><a href="#">About ECI</a></li>
        </ul>
          <div>Copyright @2018 Election Commission of India. All rights reserved.</div>
      </div><!-- End Of foot-mid Div -->
    </div>
    <div class="col-lg-3 col-md-3"></div>
  </div>
 </div> 
</footer><!-- End Of foter-fixed footer -->
 
 
@endsection

<script type="text/javascript">

 function refereshcaptch(){    
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
    }
</script>