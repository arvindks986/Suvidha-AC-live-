@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
            <div class="row d-flex">
                <div class="col-lg-3" style="text-align: center; margin: 0 auto;">
                    <!-- Income-->
                    <div class="card income text-center">
                        <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                        <div class="text-success"><b>Agent</b> &nbsp; <div class="btn-group float-right">
                                <a type="button" href="{{url('/roac/permission/viewagent')}}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/roac/permission/agentCreation')}}"
                                    class="btn btn-sm btn-primary">Add</a>
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
                            <h2>Agent Details</h2>
                        </div>
                        <div class="card-body tabular-pane">
                            <table id="example" class="table table-striped table-bordered table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>User Id</th>
                                        <th>Agent Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Designation</th>
                                        <th>Active/Inactive</th>
                                        <th>Edit/Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($getAgentList))
                                    @foreach($getAgentList as $data)
                                    <tr>
                                        <td>{{$data->officername}}</td>
                                        <td>{{$data->name}}</td>
                                        <td>{{$data->email}}</td>
                                        <td>{{$data->Phone_no}}</td>
                                        <td>{{$data->designation}}</td>
                                        @if($data->is_active == 1)
                                        <td id="setStatus"><span class="btn btn-secondary setStatus"
                                                id="{{$data->is_active}}{{'#'}}{{$data->id}}">{{'Active'}} </span></td>
                                        @else
                                        <td id="setStatus"><span class="btn btn-danger setStatus"
                                                id="{{$data->is_active}}{{'#'}}{{$data->id}}">{{'InActive'}} </span>
                                        </td>
                                        @endif
                                        <td id="edit"><a
                                                href="{{url('/roac/permission/editagent')}}/{{Crypt::encryptString($data->id)}}"><span
                                                    class=" btn btn-success">Edit</span></a></td>
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
<input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
@endsection
@section('script')
<script type="text/javascript">
$(function() {
    var token = $('meta[name="csrf-token"]').attr('content');
    var base_url = $("#base_url").val();
    $('.setStatus').on('click', function() {
        var status = $(this).attr('id');
         
        var getArray = status.split("#");
         
        if (getArray[0] == 1) {
            var res = confirm('Are you sure you want to Inctive this user')
            if (res == true) {
                $.ajax({
                    url: base_url + '/roac/permission/agentstatus',
                    type: 'POST',
                    data: {
                        _token: token,
                        status: status
                    },
                    success: function(data) {
                        //                    alert(data);
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
                    url: base_url + '/roac/permission/agentstatus',
                    type: 'POST',
                    data: {
                        _token: token,
                        status: status
                    },
                    success: function(data) {
                        //                    alert(data);
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