@extends('admin.central.common.theme')
@section('title', 'MParty')
@section('bradcome')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => Common::generate_url('dashboard'),
    'name' => 'Mparty Dashboard'
  ]; 
  ?>
@endsection
 
@section('content')
<main>
     
     @if (session('error_mes'))
          <div class="alert alert-success"> {{session('error_mes') }}</div>
        @endif
     <?php $total=0;  $totaccepted=0; $totrej=0; $totalwith=0; ?>
   
    <section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
            <div class="container-fluid">
              <div class="row d-flex">
                <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                    <div class="icon"><img src="{{ asset('theme/img/icon/applied.png') }}" alt="" /></div>
                    <div class="number yellow">{{$totalparties}}</div><p>Toatal <strong class="text-primary">Parties</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                      <div class="icon"><img src="{{ asset('theme/img/icon/verified.png') }}" alt="" /></div>
                    <div class="number green">{{$national}}</div><p>National<strong class="text-primary">Parties </strong></p>
                   
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/generate.png') }}" alt="" /></div>
                    <div class="number orange">{{$state}}</div><p>State<strong class="text-primary">Parties</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/notverified.png') }}" alt="" /></div>
                    <div class="number red">{{$unreconized}}</div><p>Unrecognized<strong class="text-primary">Parties</strong></p>
                  </div>
                </div>
              </div>
            </div>
    </section>
    <section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
            <div class="container-fluid">
              <div class="row d-flex">
                <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                    <div class="icon"><img src="{{ asset('theme/img/icon/applied.png') }}" alt="" /></div>
                    <div class="number yellow">{{$totalsymbol}}</div><p>Toatal <strong class="text-primary">Symbol</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                      <div class="icon"><img src="{{ asset('theme/img/icon/verified.png') }}" alt="" /></div>
                    <div class="number green">{{$allotedtoparties}}</div><p>Symbol<strong class="text-primary">Alloted To Parties </strong></p>
                   
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/generate.png') }}" alt="" /></div>
                    <div class="number orange">{{$freesymbol}}</div><p>Free<strong class="text-primary">Symbol</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/notverified.png') }}" alt="" /></div>
                    <div class="number red">{{$reservesymbol}}</div><p>Reserved<strong class="text-primary">Symbol</strong></p>
                  </div>
                </div>
              </div>
            </div>
    </section>
          <?php /*<!-- Counts Section -->
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
                    <div class="name"><strong class="text-uppercase">Withdrawan Date</strong><span>@if(!empty($sched['LDT_WD_CAN'])){{date("d M Y",strtotime($sched['LDT_WD_CAN']))}}@endif</span>
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
          </section> */ ?>
           
    </main>  
     
@endsection
  
