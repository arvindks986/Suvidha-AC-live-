<section class="breadcrumb-section mybradcom">
<div class="container-fluid">
<div class="row">
  <div class="col">
    <ul id="breadcrumb" class="pt-2 mr-auto">
      <li><a href="{{url('/roac/dashboard')}}"><span class="icon icon-home"> </span></a></li>
      @yield('bradcome')
    </ul>
	<div class="nav-header welcome float-right">
   <ul class="float-right"> 
       <li>
	     Welcome :- {{$user_data->designation}} LoginId:- {{$user_data->officername}} </li>
      </ul>
	  <input type="hidden" value="" readonly>
</div>
  </div>
</div>
</div>
</section> 
<!-- print header start -->
<style>
    th{color: black !important;
    }
</style>
<!-- print header end -->