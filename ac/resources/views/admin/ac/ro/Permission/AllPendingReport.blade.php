@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-3 pl-0">
              <!-- Income-->
              <div class="card income text-center">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success text-center"><b>Pending</b> &nbsp; <div class="btn-group float-right">
					<a type="button" href="{{url('/roac/permission/permissioncountdetails')}}/{{'0'}}" class="btn btn-sm btn-outline-primary">@if(!empty($allrecord)) {{$allrecord[0]->Pending}} @else {{'0'}} @endif</a>
<!--                  <a type="button" href="addps.html" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>
              </div>
            </div>
          <div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Inprogress</b> &nbsp; <div class="btn-group float-right mt-2">
					<a type="button" href="{{url('/roac/permission/permissioncountdetails')}}/{{'1'}}" class="btn btn-sm btn-outline-primary">@if(!empty($allrecord)) {{$allrecord[0]->Inprogress}} @else {{'0'}} @endif</a>
<!--                  <a href="addpermission.html" type="button" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>
              </div>
            </div> 
			<div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Accepted</b> &nbsp; <div class="btn-group float-right mt-2">
					<a type="button" href="{{url('/roac/permission/permissioncountdetails')}}/{{'2'}}" class="btn btn-sm btn-outline-primary">@if(!empty($allrecord)) {{$allrecord[0]->Accepted}} @else {{'0'}} @endif</a>
<!--                  <button type="button" class="btn btn-sm btn-primary">Add</button>-->
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3 pr-0">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-warning"><b>Rejected</b> &nbsp; <div class="btn-group float-right mt-2">
					<a type="button" href="{{url('/roac/permission/permissioncountdetails')}}/{{'3'}}" class="btn btn-sm btn-outline-danger">@if(!empty($allrecord)) {{$allrecord[0]->Rejected}} @else {{'0'}} @endif</a>
<!--                  <button type="button" class="btn btn-sm btn-danger">Add</button>-->
				  </div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>
    
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
         <table id="list-table" class="table" style="width:100%">
        <thead>
            <tr>
                <th>Permission ID</th>
                <th>User Name</th>
                <th>User Type</th>
                <th>Permission Type</th>
                <th>Date of Submission</th>
                <th>Status</th>              
             </tr>
        </thead>
        @if(!empty($totalReportDetails))
        @foreach($totalReportDetails as $data)
        <tbody>
              <tr>
                <td>{{$data->id}}</td>
                <td>{{$data->name}}</td>
                <td>{{$data->role_name}}</td>
                 <td>{{$data->pname}}</td>
                <td>{{$data->added_at}}</td>
                <td>
                    <div class="text-success text-center">Pending</div>
                </td>
            </tr>
        </tbody>
        @endforeach
        @endif
    </table>
                    
                </div>
        </div>
        </div>
    </section>
</main>
@endsection

@section('script')
  <script
  src="https://code.jquery.com/jquery-3.3.1.js"
  integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
  crossorigin="anonymous"></script>
    <script src="vendor/popper.js/umd/popper.min.js"> </script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="js/grasp_mobile_progress_circle-1.0.0.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"> </script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
	
    <script src="js/charts-home.js"></script>
    <script>$(document).ready(function() {
    $('#list-table').DataTable();
} );</script>
    @endsection
