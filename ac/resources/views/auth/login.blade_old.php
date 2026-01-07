@extends('layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
<main>
   <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
            <img src="{{ asset('theme/img/vendor/background.png') }}" alt=""></div>
			     <div class="row loginTop">
             
              <div class="col ">
                 <div class=" btn-group float-right">
          <input type="button" class="btn btn-primary float-right mt-3" onclick="location.href = '{{$url}}';" value="Home">
          <input type="button" class="btn btn-warning float-right mt-3" onclick="location.href = '{{$url}}/officer-login';" value="Officer Login">
        </div>
            </div>
            </div>
			   
    <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">
  
  
   <div class="col-md-6 login-page "> 
  <figure class="evm-logo">
  <span style="margin: auto;"><img style="max-width: 140px;" src="{{ asset('theme/img/logo/eci-logo.png') }}"> <p>Election Commission of India</p> </span></figure> </div>
    <div class="col-md-6">
    <div class="login-right">
    
   
                <div class="d-flex align-items-center mb-3">
                  <h4>Apply  for  <span>Permission </span></h4>
                </div>
               
 <form class="log-frm-area" method="POST" action="{{ url('/user-postlogin') }}" autocomplete='off' enctype="x-www-urlencoded">
                        {{ csrf_field() }}
      
    <span class="help-block"> 
        <strong>{{ Session::get('log_message') }}</strong>
    </span>
      <div class="form-group">
        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{old('username')}}"  autofocus placeholder="Mobile Number"  autocomplete="off" maxlength="10" minlength="10" >

        @if ($errors->has('username'))
          <span class="invalid-feedback"><strong>{{ $errors->first('username') }}</strong>
                      </span>
        @endif 
      </div>
       
     
    <div class="form-group  d-flex flex-column flex-md-row align-items-center">
        <div class="col-md-8 m-0 p-0"> 
            <div class="captcha">
             <span id="captcha">{!!captcha_img() !!}</span>
            <button type="button" id="btn-refresh" class="btn btn-success btn-refresh" onclick="refereshcaptch();"><i class="fa fa-refresh"></i> Refresh</button>  
             
            </div>
        </div>
        <div class="col pr-0">
		  <input id="captcha" type="text" class="form-control{{$errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha"  placeholder="captcha"   autocomplete="off" >

           @if ($errors->has('captcha'))
              <span class="invalid-feedback">
                  <strong>{{ $errors->first('captcha') }}</strong>
              </span>
           @endif
		</div>
        
     </div>
      <div class="form-group"> 
          
          </div>
                <div class="form-group text-right"> 
                  
                   <!-- <input type="button" class="btn btn-primary btn-lg" onclick="location.href = '{{$url}}/signup';" value="Signup"> -->  
                <input type="submit" class="btn btn-primary" value="Login">    
                    
              </div>
        </form>
               
              
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
  
  </main>
@endsection
@section('script')
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
@endsection