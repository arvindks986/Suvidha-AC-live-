@if(Auth::user()->role_id == 2)
<section class="breadcrumb-section">
<div class="container-fluid">
<div class="row">
  <div class="col" style="padding: 4px; margin-left: 64px;">
	<a href="{{url('/dashboard-nomination-new')}}"><span class="icon icon-home"> <span class="icon icon-beaker"> </span></span> Nomination</a> 
    <div class="nav-header float-right welcome">
	<!--<ul class="float-right">
    <li><a href="javascript:void(0)" >Welcome:- <b>{{$users=Session::get('Applicant_type')}}</b></a></li>
    </ul>-->
</div>
  </div>
</div>
</div>
</section>
<div class="container-fluid">
   <div id="showtimer" style="display:none;" class="schd-title"><h6>The Online nomination for phase 3 constituencies will be closed by <strong>18/03/2021</strong>: <i class="fa fa-clock-o text-primary ml-2"></i> Time Left - <b id="pdemo"></b></a></h6></div>
</div>


<style type="text/css">
  .schd-title {
    text-align: center;
    margin-top: 1rem;
}
.schd-title>h6 b {
    width: 150px;
    background-color: #f1f1f1;
    display: inline-block;
    padding: 0.25rem;
}
</style>

<script>
	// Set the date we're counting down to
	var countDownDate = new Date("Mar 18, 2021 23:59:59").getTime();

	// Update the count down every 1 second
	var x = setInterval(function() {

	  // Get today's date and time
	  var now = new Date().getTime();

	  // Find the distance between now and the count down date
	  var distance = countDownDate - now;

	  // Time calculations for days, hours, minutes and seconds
	  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

	  // Display the result in the element with id="demo"
	  document.getElementById("pdemo").innerHTML = days + "d " + hours + "h "
	  + minutes + "m " + seconds + "s ";
	  
	  $("#showtimer").show();

	  // If the count down is finished, write some text
	  if (distance < 0) {
		clearInterval(x);
		document.getElementById("pdemo").innerHTML = "EXPIRED";
		$("#showtimer").hide();
	  }
	}, 1000);

</script>
@else
<section class="breadcrumb-section">
<div class="container-fluid">
<div class="row">
  <div class="col">
    <ul id="breadcrumb" class="pt-2 mr-auto">
      <li><a href="Javascript:;"><span class="icon icon-home"> </span></a></li>
      <li><a href="Javascript:;"><span class="icon icon-beaker"> </span> Permission</a></li>
      <li><span class="icon icon-double-angle-right"></span> @yield('bradcome')</li>  
    </ul>
	<div class="nav-header float-right welcome">
	
	<ul class="float-right">
         
          <li><a href="javascript:void(0)" >Welcome:- <b>{{$users=Session::get('Applicant_type')}}</b></a></li>
        </ul>
	



</div>
  </div>
</div>
</div>
</section> 
@endif
