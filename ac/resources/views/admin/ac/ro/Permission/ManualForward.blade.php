@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
     @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    <section class="dashboard-header section-padding">
        <div class="container-fluid">
            <!-- <div class="line"></div> -->
            <div class="row">
                <div class="col">
                    <div class="tab-content tabular-pane" id="pills-tabContent">
                        <div class="tab-pane fade show active bgactive" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Permission ID</th>
                                        <th>Manual Forrward</th>
                                        <th>Applicant Name</th>
                                        <th>Applicant Type</th>
                                        <th>Permission Type</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $data)
                                    <tr>
<!--                                        @if($data->approved_status == 0)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/roac/permission/getpermissiondetails')}}/{{$data->permission_id}}{{'&'}}{{$data->approved_status}}{{'&'}}{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @elseif($data->approved_status == 1)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/roac/permission/getpermissiondetails')}}/{{$data->permission_id}}{{'&'}}{{$data->approved_status}}{{'&'}}{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @elseif($data->approved_status == 2)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/roac/permission/getpermissiondetails')}}/{{$data->permission_id}}{{'&'}}{{$data->approved_status}}{{'&'}}{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @elseif($data->approved_status == 3)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/roac/permission/getpermissiondetails')}}/{{$data->permission_id}}{{'&'}}{{$data->approved_status}}{{'&'}}{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @endif-->
                                        <td>{{$data->permission_id}}</td>
                                        <td><a href="{{url('/roac/permission/manulaforwarddownload')}}/{{$data->permission_id}}">Manual Forward</a></td>
                                        <td>{{$data->name}}</td>
                                        <td>{{$data->role_name}}</td>
                                        <td>{{$data->permission_name}}</td>
                                        <td>{{$data->created_at}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($data->approved_status == 0) {{'Pending'}}
                                                @elseif($data->approved_status == 1) {{'Inprogress'}}
                                                @elseif($data->approved_status == 2){{'Accept'}}
                                                @elseif($data->approved_status == 3){{'reject'}}
                                                @elseif($data->approved_status == 4){{'Manual Forwarded'}}
                                                @endif
                                            </div>
                                        </td>
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