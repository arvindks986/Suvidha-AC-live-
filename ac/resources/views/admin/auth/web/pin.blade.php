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
          <span style="margin: auto;"> @if($url=="https://suvidha.eci.gov.in/suvidhaac/public" || $url=="http://suvidha.eci.gov.in/suvidhaac/public" || $url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")

 
   <img class="logoSize" src="{{ asset('img/eci-logo1.png') }}" alt="" />
  @else
    <img class="logoSize" src="{{ asset('img/eci-logo.png') }}" alt="" />
  @endif
   <p>Election Commission of India </p> </span></figure> </div>
	  
	
	
	
  
    <div class="col-md-6 loginDiv">
    <div class="login-right">
   
   <fieldset>
   <legend class="text-center mb-2"> 
   
 
   <div class=" btn-group main-nav">
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}';" value="Home"/> 
          <?php /* @if($url=="https://encore.eci.gov.in/suvidhaac/public" || $url=="http://encore.eci.gov.in/suvidhaac/public" || $url=="https://encore.eci.gov.in" || $url=="http://encore.eci.gov.in")

             <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/login';" value="Candidate Login"/>
          @endif */ ?>
          <input type="button" class="btn btn-link active"  value="Officer Login"/>
		  
        </div>
 

        
		</legend>
  <legend class="text-center login_for_office">Enter your 6 digits PIN</legend>
       <!--    <h3 class="display 1">Officer Login</h3>   -->
               
<div class="pos_relative" style="position: relative;overflow: hidden;">
 <form class="log-frm-area" id="login_via_two_step" method="POST" action="{!! $action !!}" autocomplete='off' enctype="x-www-urlencoded">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
      <div class="form-group d-flex flex-column flex-md-row align-items-center">
        <input id="pin" type="password" class="form-control" name="pin" value="" placeholder="Enter your 6 digits pin"  autocomplete="off" >
     
   &nbsp;
      <button type="submit" class="btn btn-primary" id="two_step_button" style="width: 150px;">Login</button>
  
 </div>

@if ($errors->has('pin'))
  <span class="invalid-feedback"><strong>{{ $errors->first('pin') }}</strong></span>
@endif
   
 </form>


 <div class="row">
  <div class="col-md-12">
  <small><a href="{!! url('/officer-login') !!}" class="pull-right">Back to Login</a></small>
  </div>
</div>


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
@endsection