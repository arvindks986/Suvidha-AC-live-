
<main>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
   <title>Dashboard</title>
  </head>
  <body>
     
	
<body class="full-window">
<div id="carouselControl" class="carousel slide custom-slider" data-ride="carousel"> 
  
  <!-- Indicators -->
  <ul class="carousel-indicators">
    <li data-target="#carouselControl" data-slide-to="0" class="active"></li>
    <li data-target="#carouselControl" data-slide-to="1"></li>
    <li data-target="#carouselControl" data-slide-to="2"></li>
  </ul>
  
  <!-- The slideshow -->
  <div class="carousel-inner">
    <div class="carousel-item active"> 
	  <figure class="fit-img"><img src="{{ asset('appoinment/img/online-nomination-banner.jpg') }}" alt="">
	    <figcaption class="carousel-caption" style="display: block;">
            <h2><span>{{ __('messages.online') }}</span> {{ __('messages.Nomination') }} </h2>
            <div class="star-list">
              <ul>
                <li>{{ __('messages.Nomination_text1') }}</li>
                <li>{{ __('messages.Nomination_text2') }}</li>
                <li>{{ __('messages.Nomination_text3') }}</li>
              </ul>
            </div>
		</figcaption>	
	  </figure>
	</div>
    <div class="carousel-item">
	 <figure class="fit-img"><img src="{{ asset('appoinment/img/online-permission-banner.jpg') }}" alt=""> 
	  <figcaption class="carousel-caption" style="display: block;">
            <h2> <span>{{ __('messages.online') }}</span> {{ __('messages.PERMISSION') }} </h2>
            <div class="star-list">
              <ul>
                <li>{{ __('messages.PERMISSION_text1') }}</</li>
                <li>{{ __('messages.PERMISSION_text2') }}</li>
              </ul>
            </div>
		</figcaption>
	</figure>  
	</div>
   <!-- <div class="carousel-item"> 
	 <figure class="fit-img"><img src="{{ asset('appoinment/img/advertisment-banner.jpg') }}" alt="">
	   <figcaption class="carousel-caption" style="display: block;">
            <h2><span>{{ __('messages.Media') }}</span>{{ __('messages.PRE_CERTIFICATION') }}</h2>
           <div class="star-list">
              <ul>
                <li>{{ __('messages.PRE_CERTIFICATION_text1') }}</li>
                <li>{{ __('messages.PRE_CERTIFICATION_text2') }}</li>
              </ul>
            </div>
		</figcaption>
	 </figure>  
	</div>-->
  </div>
<!-- Left and right controls --> 
<!--  <a class="carousel-control-prev" href="#carouselControl" data-slide="prev"> <span class="carousel-control-prev-icon"></span> </a> -->
	<a class="carousel-control-next btn" href="#carouselControl" data-slide="next" id="Nextb">{{ __('messages.next') }}</a> 
	<a class="carousel-control-next btn" href="#carouselControl" data-slide="next" id="go" style="display:none;" onclick="letstart();">{{ __('messages.get_started') }}</a> 
</div>

<script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
   
	  function letstart(){
               var roleid = '{{Auth::user()->role_id}}';
               if(roleid == 2)
                 {
		   window.location="<?php echo url('/'); ?>/nomination/apply-nomination-step-1";
             }
             else
             {
		 window.location="<?php echo url('/'); ?>/update profile";
             }
	  }

	  $(function(){
		  
	   //This Function For Full Screen Banner	  
		var wdth = $(window).width();  
		  var hght = $(window).height();  
		   $('.fit-img>img').css({'width': wdth,'height': hght});
		  
		  
    // init carousel
    $('.carousel').carousel({
        pause: true,        // init without autoplay (optional)
        interval: false,    // do not autoplay after sliding (optional)
        wrap:false          // do not loop
    });
    
	//This Function For 
	 var i = 0; 
	 $('.carousel-control-next').on('click',function(){
		i++;
		if(i >= 1){
			
			$("#Nextb").hide();
			$("#go").show();
			
			}
	 }); 
		  
	});  
</script>
</body>
</html>
	 
	

