@extends('admin.layouts.ac.theme')
@section('title', 'MParty')
@section('bradcome', 'Dashboard')
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
                    <div class="number yellow"><a href="{{url('/mparty/list-party-report')}}">{{$totalparties}}</a></div><p>Toatal <strong class="text-primary">Parties</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                      <div class="icon"><img src="{{ asset('theme/img/icon/verified.png') }}" alt="" /></div>
                    <div class="number green"><a href="{{url('/mparty/list-party-report')}}?party_type=N">{{$national}}</a></div><p>National<strong class="text-primary">Parties </strong></p>
                   
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/generate.png') }}" alt="" /></div>
                    <div class="number orange"><a href="{{url('/mparty/list-party-report')}}?party_type=S">{{$state}}</a></div><p>State<strong class="text-primary">Parties</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/notverified.png') }}" alt="" /></div>
                    <div class="number red"><a href="{{url('/mparty/list-party-report')}}?party_type=U">{{$unreconized}}</a></div><p>Unrecognized<strong class="text-primary">Parties</strong></p>
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
                    <div class="number yellow"><a href="{{url('/mparty/list-symbol-report')}}">{{$totalsymbol}}</a></div><p>Toatal <strong class="text-primary">Symbol</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                      <div class="icon"><img src="{{ asset('theme/img/icon/verified.png') }}" alt="" /></div>
                    <div class="number green"><a href="{{url('/mparty/list-symbol-report')}}?freesymbol=PARTY">{{$allotedtoparties}}</a></div><p>Symbol<strong class="text-primary">Alloted To Parties </strong></p>
                   
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/generate.png') }}" alt="" /></div>
                    <div class="number orange"><a href="{{url('/mparty/list-symbol-report')}}?freesymbol=T">{{$freesymbol}}</a></div><p>Free<strong class="text-primary">Symbol</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('theme/img/icon/notverified.png') }}" alt="" /></div>
                    <div class="number red"><a href="{{url('/mparty/list-symbol-report')}}?freesymbol=F">{{$reservesymbol}}</a></div><p>Reserved<strong class="text-primary">Symbol</strong></p>
                  </div>
                </div>
              </div>
            </div>
    </section>
           
           
    </main>  
     
@endsection
  
