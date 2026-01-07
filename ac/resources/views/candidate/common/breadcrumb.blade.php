@if(isset($bredcrumbs) && count($bredcrumbs)>0)
<section class="breadcrumb-section mybradcom">
<div class="container-fluid">
<div class="row">
  <div class="col">
    <ul id="breadcrumb" class="pt-2 mr-auto">
      
      <li><a href="{!! url('/') !!}">Home <span class="icon icon-double-angle-right"></span></a></li>

      @foreach($bredcrumbs as $iterate_bredcrumb)
      <li><a href="{!! $iterate_bredcrumb['href'] !!}">{!! $iterate_bredcrumb['name'] !!}</a></li>  
      @endforeach

      


    </ul>
	<div class="nav-header welcome float-right">

   	<ul class="list-inline"> 
       <li>
	     
	 	</li>
      </ul>
  	  
      <input type="hidden" value="{{$_SERVER['SERVER_ADDR']}}" readonly="readonly">
</div>
  </div>
</div>
</div>
</section> 
@endif