@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Pwd Dashboard')
@section('content')


@if($errors->any())
<div class="alert alert-info">{{$errors->first()}}</div>
@endif

@if (session('error'))
<div class="alert alert-info">{{ session('error') }}</div>
@endif

<style type="text/css">
  .loader {
    position: fixed;
    left: 50%;
    right: 50%;
    border: 16px solid #f3f3f3;
    /* Light grey */
    border-top: 16px solid #3498db;
    /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    z-index: 99999;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>

<div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-7 pull-left">
        <h4>Pwd Dashboard</h4>
      </div>

      <div class="col-md-5  pull-right text-right">



      </div>

    </div>
  </div>
</section>
<section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 pb-3">
        <div class="card income text-center mt-3">
          <div class="d-flex justify-content-between">
            <div>
              <strong class="text-primary"><a href="{{url('acceo/pwd/wheel-chair')}}">Wheel Chair Requests</a></strong>
            </div>
            <div class="number orange">{{$wheel_chair}}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4 pb-3">
        <div class="card income text-center mt-3">
          <div class="d-flex justify-content-between">
            <div>
              <strong class="text-primary"><a href="{{url('acceo/pwd/pick-drop')}}">Pick & Drop Requests</a></strong>
            </div>
            <div class="number orange">{{$pick_drop}}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4 pb-3">
        <div class="card income text-center mt-3">
          <div class="d-flex justify-content-between">
            <div>
              <strong class="text-primary"><a href="{{url('acceo/pwd/volunteer')}}">Volunteer Requests</a></strong>
            </div>
            <div class="number orange">{{$volunteer}}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>




@endsection

@section('script')
@endsection