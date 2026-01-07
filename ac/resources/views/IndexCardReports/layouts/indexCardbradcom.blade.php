<section class="breadcrumb-section">
<div class="container-fluid">
<div class="row">
  <div class="col">
    <ul id="breadcrumb" class="pt-2 mr-auto">
	<?php if(Auth::user()->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}else if(Auth::user()->designation == 'CEO'){
			$prefix 	= 'acceo';
		}else if(Auth::user()->designation == 'DEO'){
			$prefix 	= 'acdeo';
		}   ?>
	
      <li><a href='{{url("$prefix/dashboard")}}'><span class="icon icon-home"> </span></a></li>
      <!--<li><a href="#"><span class="icon icon-beaker"> </span> @yield('title') </a></li>-->
      <li><span class="icon icon-double-angle-right"></span> @yield('bradcome')</li>  
    </ul>
	<div class="nav-header welcome float-right">
   <ul class="float-right"> 
       <li><a href="javascript:void(0)">   Welcome :- {{$user_data->designation}} LoginId:- {{$user_data->officername}}</a> </li>
      </ul>
</div>
  </div>
</div>
</div>
</section>

