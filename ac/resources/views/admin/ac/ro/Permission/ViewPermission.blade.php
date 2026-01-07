@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 

<section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-3 pl-0">
              <!-- Income-->
              <div class="card income">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/roac/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div>
          <div class="col ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right mt-2">
		  <a type="button" href="{{url('/roac/permission/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> 
			<div class="col ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right mt-2">
	          <a type="button" href="{{url('/roac/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
          <div class="col pr-0">
              <!-- Income-->
              <div class="card income ">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right mt-2">
		<a type="button" href="{{url('/roac/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>


<section>
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
              <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h2>Permission List</h2>
                </div>
                <div class="card-body tabular-pane">
                 <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
                <th>S.no.</th>
              <th>Permission Name</th>
              <th>NOC Required from authority</th>
              <th>Document name</th>
              <th>Edit/Update</th>            
            </tr>
        </thead>
        <tbody>
            @if(!empty($getAllPermsData))
            @foreach($getAllPermsData as $data)
                <tr>
                    <td>{{$data->id}}</td>
                    <td>{{$data->pname}}</td>
                    <td>{{$data->auth_name}}</td>
                    <td>{{$data->doc_name}}</td>
                    <td id="edit"><a href="{{url('/roac/permission/editpermsn')}}/{{$data->id}}"><span class=" btn btn-success">Edit</span></a></td>
                </tr>
            @endforeach
            @endif
        </tbody>
     
    </table>
                </div>
              </div>
            </div>
</div>
</div>

</section>

</main>


@endsection
@section('script')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    var table = $('#example').DataTable();
} );
</script>
@endsection