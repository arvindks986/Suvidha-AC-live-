@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<main>
<section class="countdownTimer">
<div class="container-fluid">
	<div class="row">
	<div class="col mr-auto"><h1 class="mt-2 display 1">DEO Dashboard</h1></div>
	<div class="col-md-3 mt-2 mb-2 countdown">
	<span  id="demo"></span> LEFT FOR ELECTION
	</div>
	</div>
	</div>
</section>
<?php 
    $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code,'district_no' =>$user_data->dist_no])->where('party_id', '!=' ,'1180')->where(['application_status' =>'4'])->get()->count();
  $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code,'district_no' =>$user_data->dist_no])->where('party_id', '!=' ,'1180')->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code,'district_no' =>$user_data->dist_no ])->where('party_id', '!=' ,'1180')->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code,'district_no' =>$user_data->dist_no])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code,'district_no' =>$user_data->dist_no])->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->get()->count();

  

     ?>
<main>

<section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
        <div class="container-fluid">
          <div class="row d-flex">
            <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number yellow">{{$total}}</div><p>Application<strong class="text-primary">Open</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                  <div class="icon"><img src="{{ asset('admintheme/img/icon/verified.png') }}" alt="" /></div>
                <div class="number green">{{$totaccepted}}</div><p>Applications<strong class="text-primary">Accepted </strong></p>
               
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('admintheme/img/icon/generate.png') }}" alt="" /></div>
                <div class="number orange">{{$totrej}}</div><p>Total Receipt<strong class="text-primary">Rejected</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('admintheme/img/icon/notverified.png') }}" alt="" /></div>
                <div class="number red">{{$totalwith}}</div><p>Applications<strong class="text-primary">Withdrawn</strong></p>
              </div>
            </div>
          </div>
        </div>
</section>
      <!-- Counts Section -->
      <section class="dashboard-counts  section-padding">
        <div class="container-fluid">
          <div class="row">
            <!-- Count item widget-->
            <div class="col-xl-2 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-user"></i></div>
                <div class="name"><strong class="text-uppercase">Notification Date</strong><span>@if(!empty($sched['DT_ISS_NOM'])){{date("d M Y",strtotime($sched['DT_ISS_NOM']))}}
                  @endif</span>
                 <!--  <div class="count-number">25</div> -->
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-2 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name"><strong class="text-uppercase">Nomination LT DT</strong><span>@if(!empty($sched['LDT_IS_NOM'])){{date("d M Y",strtotime($sched['LDT_IS_NOM']))}}@endif</span>
                <!--   <div class="count-number">400</div> -->
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-2 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-check"></i></div>
                <div class="name"><strong class="text-uppercase">Scrutiny Date</strong><span>@if(!empty($sched['DT_SCR_NOM'])){{date("d M Y",strtotime($sched['DT_SCR_NOM']))}}@endif</span>
                  <!-- <div class="count-number">342</div> -->
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-2 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-bill"></i></div>
                <div class="name"><strong class="text-uppercase">Last Date of Withdrawn</strong><span>@if(!empty($sched['LDT_WD_CAN'])){{date("d M Y",strtotime($sched['LDT_WD_CAN']))}}@endif</span>
                 <!--  <div class="count-number">123</div> -->
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-2 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-list"></i></div>
                <div class="name"><strong class="text-uppercase">Poll Date</strong><span>@if(!empty($sched['DATE_POLL'])){{date("d M Y",strtotime($sched['DATE_POLL']))}}@endif</span>
                 <!--  <div class="count-number">92</div> -->
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-2 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-list-1"></i></div>
                <div class="name"><strong class="text-uppercase">Counting Date</strong><span>@if(!empty($sched['DATE_COUNT'])){{date("d M Y",strtotime($sched['DATE_COUNT']))}}@endif</span>
                  <!-- <div class="count-number">70</div> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Header Section-->
      <!-- <section class="dashboard-header section-padding">
        <div class="container-fluid">
          <div class="row d-flex align-items-md-stretch">
          
            <div class="col-lg-6 col-md-6">
              <div class="card project-progress">
                <h2 class="display h4">Total Candidates Votes</h2>
               
                <div class="pie-chart"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                  <canvas id="pieChart" width="450" height="450" class="chartjs-render-monitor" style="display: block; height: 400px; width: 400px;"> </canvas>
                </div>
				<p class="text-right mr-5">Details</p>
              </div>
            </div>
            <!-- Line Chart -->
            <!-- <div class="col-lg-6 col-md-12 flex-lg-last flex-md-first align-self-baseline">
              <div class="card sales-report">
                <h2 class="display h4">Candidate Datewise Nomination</h2>
                
                <div class="line-chart"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                  <canvas id="lineCahrt" width="778" height="388" class="chartjs-render-monitor" style="display: block; height: 222px; width: 445px;"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Statistics Section-->
</main>  
<script src="{{ asset('admintheme/js/front.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/charts-home.js') }}"></script>

<script type="text/javascript">
  // Set the date we're counting down to
    var po = "@if(!empty($sched['DATE_POLL'])){{date("M d, Y 12:00:0",strtotime($sched['DATE_POLL']))}}@endif" ;

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