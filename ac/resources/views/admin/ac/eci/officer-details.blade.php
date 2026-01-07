@extends('admin.layouts.ac.theme')
@section('title', 'Officer Details')
@section('bradcome', 'Officer Details')
@section('description', '')
@section('content')

<main role="main" class="inner cover mb-3">
<section class="mt-5">
  <div class="container-fluid">
    @if(Session::has('success_mes'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_mes')) }}</strong> 
              </div>
          @endif
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h6 class="mr-auto">Officer Details</h6></div> 
            </div>

            <div class="row">
            <div class="col"><p class="mb-0 text-center">
              <span class="alert alert-info">Please enter valid mobile number / email id as same will be used in OTP verification while login.</span>&nbsp;&nbsp; 
          </p></div> </div>

            </div>
<div class="card-body">  
         @if (session('success_error'))
            <div class="alert alert-danger">
                {{ session('success_error') }}
            </div>
          @endif
          @if (session('success_success'))
            <div class="alert alert-success">
                {{ session('success_success') }}
            </div>
          @endif

  <div class="table-responsive">
  <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>Sr. No</th>
          <th>User Id</th>
          <th>Designation</th>
          <th>State</th>
          <th>Officer Name</th>
           <th>Email</th>
          <th>Mobile</th>
          <th>Account Activated</th>
          <th>Edit</th>
        </tr>
        </thead>
        <tbody>
            <?php $j=0;   ?>
           
              @if(!empty($officerlist))
            @foreach($officerlist as $officerDetailsList)  
              <?php
               $j++; 
                  //$acdetails=getacname($officerDetailsList->st_code,$officerDetailsList->ac_no); 
                  //$acname= !empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : '';
                  //$pcdetails=getpcbypcno($officerDetailsList->st_code,$officerDetailsList->pc_no); 
                  //$pcname= !empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : '';
              
                ?>
              <tr>
               <td>{{$j}}</td>
               <td>@if(!empty($officerDetailsList->officername)) {{$officerDetailsList->officername}} @endif</td>
               <td>@if(!empty($officerDetailsList->designation)) {{$officerDetailsList->designation}} @endif</td> 
               <td>@if(!empty($officerDetailsList->placename)) {{$officerDetailsList->placename}} @endif</td> 
               <td>@if(!empty($officerDetailsList->name)) {{$officerDetailsList->name}} @endif</td>
               <td>@if(!empty($officerDetailsList->email)) {{$officerDetailsList->email}} @endif</td>
               <td >@if(!empty($officerDetailsList->Phone_no)) {{$officerDetailsList->Phone_no}} @endif</td>
               <td >@if(!empty($officerDetailsList->password)) yes @else No @endif</td>
<td style="background-color:white;"><a href="{{url('/eci/officer-profile-edit/'.encrypt($officerDetailsList->id).'/')}}" class="btn btn-primary btn-block">Edit</a></td>

<!-- <td><a href="#" class="btn btn-primary btn-block">Edit</a></td>-->
              </tr>
            @endforeach 
            @endif 
             </tbody>
            </table>
           </div> <!-- end reponcive-->
          </div>
        </div>
  </div>
  </div>
  </section>
  </main>

@endsection