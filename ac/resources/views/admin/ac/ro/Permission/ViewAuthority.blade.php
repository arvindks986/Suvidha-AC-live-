@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')

<section class="statistics">
    <div class="container-fluid mt-5 mb-5">
        <div class="row d-flex">
            <div class="col pl-0">
                
                <div class="card income">
                    <div class="card-body">
                        <div class="text-success"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
                                <a type="button" href="{{url('/roac/permission/viewps')}}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/roac/permission/addps')}}"
                                    class="btn btn-sm btn-primary">Add</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                 
                <div class="card income ">
                    <div class="card-body">
                        <div class="text-info"><b>Nodal</b> &nbsp; <div class="btn-group float-right">
                                <a type="button" href="{{url('/roac/permission/viewauthority')}}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/roac/permission/addauthority')}}"
                                    class="btn btn-sm btn-primary">Add</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col pr-0">
                <!-- Income-->
                <div class="card income">
                    <div class="card-body">
                        <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right">
                                <a type="button" href="{{url('/roac/permission/viewaddlocation')}}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/roac/permission/addlocation')}}"
                                    class="btn btn-sm btn-primary">Add</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section>
    @if (Session::has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h2>Nodal List</h2>
                    </div>
                    <div class="card-body tabular-pane">
                        <table id="list-table1" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Authority Type Name</th>
                                    <th>Department</th>
                                    <th>Address</th>
                                    <th>Mobile No.</th>
                                    <th>Incharge Name</th>
                                    <th>Active/InActive</th>
                                    <th>Edit/Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php $i='1'; ?>
                                @if(!empty($getAllAuthorityData))
                                @foreach($getAllAuthorityData as $data)
                                <tr>
                                    <td>{{$i}}</td>
                                    @if(!empty($data->auth_type_name1))
                                    <td>{{$data->auth_type_name1}}</td>
                                    @else
                                    @if(!empty($data->auth_type_name2))
                                    <td>{{$data->auth_type_name2}}</td>
                                    @endif
                                    @endif
                                    <td>{{$data->department}}</td>
                                    <td>{{$data->address}}</td>
                                    <td>{{$data->mobile}}</td>
                                    <td>{{$data->name}}</td>
                                    @if(property_exists($data,'is_active') && $data->is_active == 0)
                                    <td id="setStatus"><span class="btn btn-danger setStatus"
                                            id="{{'0'}}{{'#'}}{{$data->nodal_id}}">{{'InActive'}} </span></td>
                                    @else
                                    <td id="setStatus"><span class="btn btn-success setStatus"
                                            id="{{'1'}}{{'#'}}{{$data->nodal_id}}{{'#'}}">{{'Active'}} </span></td>
                                    @endif
                                    <td id="edit"><a
                                            href="{{url('/roac/permission/editauthority')}}/{{Crypt::encryptString($data->nodal_id)}}{{'&'}}{{Crypt::encryptString($data->auth_type_id)}}"><span
                                                class=" btn btn-success float-right">Edit</span></a></td>
                                </tr>
                                 <?php $i++; ?>
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


@endsection
@section('script')
<script type="text/javascript">
$(function() {
    var token = $('meta[name="csrf-token"]').attr('content');
    var base_url = $("#base_url").val();
    $(document).on('click', '.setStatus', function() {
        var status = $(this).attr('id');
        //            alert(status);
        var getArray = status.split("#");
        //            alert(getArray[0]);exit;
        if (getArray[0] == 1) {
            var res = confirm('Are you sure you want to Inactive this user')
            if (res == true) {
                $.ajax({
                    url: base_url + '/roac/permission/authoritystatus',
                    type: 'POST',
                    data: {
                        _token: token,
                        status: status
                    },
                    success: function(data) {
                        //                            alert(data);exit;
                        location.reload();
                    }
                });
            } else {
                return false;
            }
        } else {
            var res = confirm('Are you sure you want to Active this user')
            if (res == true) {
                $.ajax({
                    url: base_url + '/roac/permission/authoritystatus',
                    type: 'POST',
                    data: {
                        _token: token,
                        status: status
                    },
                    success: function(data) {
                        //                            alert(data);exit;
                        location.reload();
                    }
                });
            } else {
                return false;
            }
        }
    });
});
</script>
@endsection