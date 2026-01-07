@extends('admin.layouts.ac.theme')
@section('title', 'MLC')
@section('bradcome', 'MLC RO Dashboard')
@section('content')
<main>
    <!-- <section class="countdownTimer">
		<div class="container-fluid">
		  <div class="row">
		  <div class="col mr-auto"><h1 class="mt-2 display 1">Dashboard</h1></div>
		  <div class="col-md-3 mt-2 mb-2 countdown">
		  <span  id="demo"></span>  
		  </div>
		  </div>
		  </div>
		</section> -->
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
                    <div class="number yellow">{{$total}}</div><p>Toatal <strong class="text-primary">Application</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                      <div class="icon"><img src="{{ asset('theme/img/icon/verified.png') }}" alt="" /></div>
                    <div class="number green">{{$totaccepted}}</div><p>xx<strong class="text-primary">xx </strong></p>
                   
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/generate.png') }}" alt="" /></div>
                    <div class="number orange">{{$totrej}}</div><p>xx<strong class="text-primary">xx</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/notverified.png') }}" alt="" /></div>
                    <div class="number red">{{$totalwith}}</div><p>xx<strong class="text-primary">xx</strong></p>
                  </div>
                </div>
              </div>
            </div>
    </section>
     
           
    </main>  
     
@endsection
  
