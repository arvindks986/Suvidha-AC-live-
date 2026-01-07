@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Active Users</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('/eci/EciActiveUsersPdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci/EciActiveUsersReportExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
          <th>Total User</th> 
          <th>Active Users</th> 
          <th>Percentage</th> 
        </tr>
        </thead>
        <tbody>
        @php  
		$count = 1;
         $TotalUsers = 0;
        $ActiveUsers = 0;		
		
		@endphp
         @forelse ($EciActiveUsers as $key=>$listdata)
		 
		 @php

         $TotalUsers +=$listdata->total_user;
         $ActiveUsers +=$listdata->active_users;

         @endphp
          <tr>
             <td>{{ $count }}</td>
            <td>{{ $listdata->ST_NAME }}</td>
            <td> @if($listdata->total_user =='' )     0  @else  {{ $listdata->total_user }} @endif</td>
            <td> @if($listdata->active_users =='' )   0  @else {{ $listdata->active_users }} @endif</td>
            <td> @if($listdata->percentage =='' )     0  @else {{ $listdata->percentage }} @endif</td>
            
          </tr>
		@php  $count++;  @endphp
	   @empty
			<tr>
			  <td colspan="4">No Data Found For Active Users</td>                 
		  </tr>
	   @endforelse
			  <tr><td><b>Total</b></td><td></td><td><b>{{$TotalUsers}}</b></td><td><b>{{$ActiveUsers}}</b></td><td></td></tr>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

@endsection


