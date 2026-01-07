@extends('admin.layouts.pc.dashboard-theme')
@section('content')

<main role="main" class="inner cover mb-3">
    <div class="card-header pt-3" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center pt-3 pb-3">
                <div class="col-sm-12"><h4><b> ECI ELECTION EXPENDITURE MONITORING SYSTEM</b></h4></div>  
            </div> 
        </div>
    </div>
    <section class="breadcrumb-section">
      <div class="container-fluid">
      <div class="row">
        <div class="col">
          <ul id="breadcrumb" class="pt-1">
            <li><a href="#">EEMS-Election Expenditure Monitoring System (Displayed in %)</a></li>
          </ul>
        </div>
      </div>
      </div>
</section>
<section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
        <div class="container-fluid">
          <!-- EEMS box Row 1 -->
          <div class="row d-flex mb-4">
            <div class="col-md-3">
              <!-- Income-->
              
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/dataEntry.png" alt="" /></div>
                <div class="number text-warning mb-1"><a href="{{url('/')}}/eci/dataentryStart/">{{ $Percent_startdataentry }} %</div><p><strong class="text-primary">Data entry started</strong></p></a>
              </div>
              </a>
            </div> 
			<div class="col-md-3">
              <!-- Income-->
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/reportFinal.png" alt="" /></div>
                <div class="number text-info mb-1"><a href="{{url('/')}}/eci/finalizeData/">{{ $Percent_finaldatacount }} %</div><p><strong class="text-primary">Report Finalised</strong></p></a>
              </div>
            </a>
            </div> 
			<div class="col-md-3">
              <!-- Income-->
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/accLodged.png" alt="" /></div>
                <div class="number text-danger mb-1"><a href="{{url('/')}}/eci/logedaccount/">{{ $Percent_logedaccount }} %</div><p><strong class="text-primary">Account Lodged</strong></p></a>
              </div>
              </a>
            </div> 
			<div class="col-md-3">
              <!-- Income--> 
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/noTime.png" alt="" /></div>
                <div class="number text-success mb-1"><a href="{{url('/')}}/eci/notintime/">{{ $Percent_notintimeaccount }} %</div><p><strong class="text-primary">Not in Time</strong></p></a>
              </div>
              </a>
            </div>
          </div>
    <!-- End of EEMS box Row 1 -->
     <!-- EEMS box Row 2 -->
     <div class="row d-flex mb-4">
            <div class="col-md-3">
              <!-- Income-->
              
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/defectFormat.png" alt="" /></div>
                <div class="number text-warning mb-1"><a href="{{url('/')}}/eci/formatedefects/">{{ $Percent_formateDefectscount }}%</div><p><strong class="text-primary">Defects in format </strong></p></a>
              </div>
              </a>
            </div> 
			<div class="col-md-3">
              <!-- Income-->
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/notAgree.png" alt="" /></div>
                <div class="number text-info mb-1"><a href="{{url('/')}}/eci/ronotagree/">23.5%</div><p><strong class="text-primary"> RO not Agree</strong></p></a>
              </div>
            </a>
            </div> 
			<div class="col-md-3">
              <!-- Income-->
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/expUnder.png" alt="" /></div>
                <div class="number text-danger mb-1"><a href="{{url('/')}}/eci/understatedexpense/">{{ $Percent_expenseunderstated}} %</div><p><strong class="text-primary">Expenses understated</strong></p></a>
              </div>
              </a>
            </div> 
			<div class="col-md-3">
              <!-- Income-->
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/dataDefect.png" alt="" /></div>
                <div class="number text-success mb-1"><a href="{{url('/')}}/eci/dataentrydefects/">43.7%</div><p><strong class="text-primary">Data entry defects</strong></p></a>
              </div>
              </a>
            </div>
          </div>
    <!-- End of EEMS box Row 2 -->
     <!-- EEMS box Row 3 -->
     <div class="row d-flex mb-4">
            <div class="col-md-3">
              <!-- Income-->
              
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/fundParty.png" alt="" /></div>
                <div class="number text-warning mb-1"><a href="{{url('/')}}/eci/partyfund/">{{ $Percent_partyFund}} %</div><p><strong class="text-primary">Taken funds from party</strong></p></a>
              </div>
              </a>
            </div> 
			<div class="col-md-3">
              <!-- Income-->
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/fundOther.png" alt="" /></div>
                <div class="number text-info mb-1"><a href="{{url('/')}}/eci/othersfund/">{{ $Percent_OthersourcesFund}} %</div><p><strong class="text-primary">Taken funds from other sources</strong></p></a>
              </div>
            </a>
            </div> 
			<div class="col-md-3">
              <!-- Income-->
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/ceilingAmount.png" alt="" /></div>
                <div class="number text-danger mb-1"><a href="{{url('/')}}/eci/exeedceiling/">11.9%</div><p><strong class="text-primary">Exceed the Ceiling amount</strong></p></a>
              </div>
              </a>
            </div> 
			    <!-- <div class="col-md-3">
              <!-- Income--
              <div class="card income reportBox text-center">
                <a href="" target="_blank">
                <div class="icon mb-1"><img src="http://localhost/suvidha/public/admintheme/img/icon/other.png" alt="" /></div>
                <div class="number text-success mb-1">77.1%</div><p><strong class="text-primary">Other Resources Points</strong></p>
              </div>
              </a>
            </div>-->
          </div>
    <!-- End of EEMS box Row 3 -->
      
    </div>
 </section>

</main>
 <!-- Validation  JavaScript -->

<script src="{{ asset('admintheme/js/front.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/charts-home.js') }}"></script>

<script type="text/javascript">
  // Set the date we're counting down to
  var po = "@if(!empty($sched->DATE_POLL)){{date("M d, Y 12:00:0",strtotime($sched->DATE_POLL))}}@endif" ;

  var countDownDate = new Date(po).getTime();
  
  // Update the count down every 1 second
  var x = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
  
    // Find the distance between now and the count down date
    var distance = countDownDate - now;
    // console.log(distance);
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
  
    // Display the result in the element with id="demo"
    document.getElementById("demo").innerHTML = days + " DAYS";
  
    // If the count down is finished, write some text 
    if (distance < 0) {
      clearInterval(x);
      document.getElementById("demo").innerHTML = "EXPIRED";
    }
  }, 1000);
  </script>
@endsection