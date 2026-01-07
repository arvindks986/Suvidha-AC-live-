<!DOCTYPE HTML>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="poppins" content="all,follow">
  <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
  @yield('seo')
  <link rel="stylesheet" href="{{ asset('theme/css/sumoselect.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/vendor/font-awesome/css/font-awesome.min.css') }}">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">   
  <!-- Custom stylesheet - for your changes-->
  <link rel="stylesheet" href="{{ asset('theme/css/custom.css') }}">    <!-- Favicon-->
  <link rel="shortcut icon" href="{{ asset('theme/img/favicon.ico') }}">   
  <link href="{!! url('admintheme/css/jquery.toast.css') !!}" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('admintheme/css/animate.css') }}">
  @yield('style')
  @yield('headscript')
</head>
<body class="d-flex flex-column h-100">
  <!--main content start-->
  @yield('content')
  <!--main content end-->
  <!-- JavaScript files-->
  <script type="text/javascript" src="{{ asset('theme/js/jquery.min.js') }}"></script>
  <script src="{{ asset('theme/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('theme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('theme/js/utils.js') }}"></script>
  <script src="{{ url('theme/js/jquery.toast.js') }}"></script>
  <script type="text/javascript" src="{{ asset('theme/js/sumoselect.min.js') }}"></script>
  @yield('footerscript')
</body>
</html>