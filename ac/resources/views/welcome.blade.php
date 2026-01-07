<!DOCTYPE HTML>
      <html lang="{{ app()->getLocale() }}">
 <head>
    <?php $url=url('/'); ?> 
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="poppins" content="all,follow">
    <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
    <title>Candidate & Counting  Management System</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="{{ asset('theme/vendor/font-awesome/css/font-awesome.min.css')}}">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="{{ asset('theme/css/fontastic.css')}}">
    <!-- Google fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <!-- jQuery Circle-->
   <link rel="stylesheet" href="{{ asset('theme/css/grasp_mobile_progress_circle-1.0.0.min.css')}}">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="{{ asset('theme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css')}}">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{ asset('theme/css/style.red.css')}}" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{ asset('theme/css/custom.css')}}">
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{ asset('theme/img/favicon.ico')}}">
    
  <!-- Scripts -->
 
</head>

<body>
 <main>
   <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
   <img src="{{ asset('theme/img/vendor/background.png') }}" alt="" />
     </div>
      
       
    <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">
  
  
   <div class="col-md-6 login-page "> 
  <figure class="evm-logo">
  <span style="margin: auto;">
 @if($url=="http://10.153.40.52/suvidhaac/public" || $url=="http://10.153.40.52/suvidhaac/public" || $url=="http://10.153.40.52" || $url=="http://10.153.40.52")
 
   <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
  @else
    <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
  @endif
   <p>Election Commission of India </p> </span></figure> </div>


    <div class="col-md-6 loginDiv">
    <div class="login-right">
    
   
                <div class=" mb-3">
        
  
    <div class="row">
    
      <div class="col">
      <div class="card">
    
      <div class="card-body"> 
<div class="row">   
@if($url=="http://10.153.40.52/suvidhaac/public" || $url=="http://10.153.40.52/suvidhaac/public" || $url=="http://10.153.40.52" || $url=="http://10.153.40.52")
<h5 class="col-md-12 mb-0"><a href="http://10.153.40.52/login">General Election to the House of People  <small class="float-right"><i class="fa fa-angle-right"></i></small></a></h5> 
@else
	<h5 class="col-md-12 mb-0"><a href="http://10.153.40.52/officer-login">General Election to the House of People  <small class="float-right"><i class="fa fa-angle-right"></i></small></a></h5> 
@endif
<div class="col-md-12">
<div class="mr-auto"> 
        <span class=" badge-custom">All India</span>&nbsp; 
        <!--<span class="  float-right"><a href="qr1.html" class="btn btn-danger"> Click here </a>  </span>      -->  
      </div></div>  
      
      </div>
    </div>
      </div>
</div>
</div>
  <div class="row">
      <div class="col">
      <div class="card">
    
      <div class="card-body"> 
<div class="row"> 
@if($url=="http://10.153.40.52/suvidhaac/public" || $url=="http://10.153.40.52/suvidhaac/public" || $url=="http://10.153.40.52" || $url=="http://10.153.40.52")  
<h5 class="col-md-12 mb-0"><a href="{{url('login') }}">General Elections to the Legislative Assembly  <small class="float-right"><i class="fa fa-angle-right"></i></small></a> </h5> 
@else
<h5 class="col-md-12 mb-0"><a href="{{url('officer-login') }}">General Elections to the Legislative Assembly  <small class="float-right"><i class="fa fa-angle-right"></i></small></a> </h5> 
@endif
<div class="col-md-12">
<div class="mr-auto"> 
        <!--<span class=" badge-custom">Delhi,</span> 
        <span class=" badge-custom">Jammu,</span> 
        <span class=" badge-custom">Pune,</span>
        <span class=" badge-custom">Himachal Pradesh,</span>        
      <!--  <span class="  float-right"><a href="qr1.html" class="btn btn-danger"> Click here </a>  </span>  -->      
      </div></div>  
      
      </div>
    </div>
      </div>
</div>
</div>

 </div>
               
          
              
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
  <input type="hidden" name="ip" value='{{$_SERVER["SERVER_ADDR"]}}'>
  </main>
    <script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/vendor/popper.js/umd/popper.min.js') }}"> </script>
    <script src="{{ asset('theme/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('theme/js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script>
    <script src="{{ asset('theme/vendor/jquery.cookie/jquery.cookie.js') }}"> </script>
    <script src="{{ asset('theme/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('theme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('theme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <!-- Main File-->
    <script src="{{ asset('theme/js/front.js') }}"></script>
    @yield('script');
  </body>
</html>