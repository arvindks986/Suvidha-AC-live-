@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
 <style type="text/css">
   .pos_relative{
    position: relative;
   }
   .timer_div{
      position: absolute;
      right: 0px;
      top: 0px;
      width: 32px;
      height: 32px;
      z-index: 999;
   }
   #timer{
      padding: 10px 3px;
      text-align: center;
      float: right;
      font-size: 10px;
      width: 32px;
      height: 32px;
   }
   .display_none{
      display: none;
   }
   .fa-spin {
      transform: scaleX(-1);
      animation: spin-reverse 2s infinite linear;
      position: absolute;
      right: 5px;
      top: 6px;
      font-size: 24px;
    }
@keyframes spin-reverse {
  0% {
    transform: scaleX(-1) rotate(-360deg);
  }
  100% {
    transform: scaleX(-1) rotate(0deg);
  }
}
 </style>
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
                  <h1>Officer  <span>OTP Verification </span></h1>
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
        <input id="mobile" type="text" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" autofocus placeholder="Mobile"  autocomplete="off" readonly="readonly" value="{{$mobile}}">

        @if ($errors->has('mobile'))
          <span class="invalid-feedback"><strong>{{ $errors->first('mobile') }}</strong></span>
        @endif 
      </div>

      <div class="form-group pos_relative">
        <input id="otp" type="text" class="form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}" name="otp" autofocus placeholder="Enter OTP"  autocomplete="off" >
        <div class="timer_div">
          <span class="fa fa-circle-o-notch fa-spin"></span>
        <span id=timer></span>
        </div>
        @if ($errors->has('otp'))
          <span class="invalid-feedback"><strong>{{ $errors->first('otp') }}</strong></span>
        @endif 
      </div>

      <div class="form-group">
        <a class="resend_otp pull-right display_none" href="javascript:void(0)">Resend OTP</a>
      </div>

      

                    <div class="form-group"> 
                    
                    <div class="form-inline float-left">
          <input type="submit" class="btn btn-primary mt-3" value="Verify OTP"> 
          &nbsp;&nbsp;&nbsp;<a href="{{url('/officer-login')}}" class="btn btn-primary mt-3">Back </a>
        </div>

               
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

<script type="text/javascript">
var maxTicks = 60;
var tickCount = 0;
var tick = function(){
  if(tickCount >= maxTicks){
    // Stops the interval.
    display_resend_otp();
    clearInterval(myInterval);
    return;
  }
  $("#timer").text(maxTicks - tickCount);
  tickCount++;
};
// Start calling tick function every 1 second.
var myInterval = setInterval(tick, 1000);

function display_resend_otp(){
  $('.resend_otp').removeClass('display_none');
  $('.timer_div').addClass("display_none");
}
$(document).ready(function(e){

  $('.resend_otp').click(function(e){
     $.ajax({
        url: '{!! $resend_otp !!}',
        type: 'POST',
        data: '_token={!! csrf_token() !!}',
        dataType:'json',
        beforeSend:function(){
          $('.alert').remove();
          $('.resend_otp').text('Sending...');
        },
        complete:function(){
        },
        success: function(json){
            $('.resend_otp').text('Resend OTP');
            location.reload();
        },
        error:function(data){
          $('.resend_otp').text('Resend OTP');
          var errors = data.responseJSON;
        }

      });
  });

});
</script>
@endsection