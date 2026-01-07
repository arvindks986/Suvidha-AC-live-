@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')


<main role="main" class="inner cover mb-3 mb-auto">

    <section>
        <div class="tabs-inner">
            <div class="row d-flex align-items-md-stretch">
                <div class="col">
                    <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                role="tab" aria-controls="pills-home" aria-selected="true">Total Applied Permission</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                role="tab" aria-controls="pills-profile" aria-selected="false">Accepted Permission</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact"
                                role="tab" aria-controls="pills-contact" aria-selected="false">Rejected Permission</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-pending-tab" data-toggle="pill" href="#pills-pending"
                                role="tab" aria-controls="pills-pending" aria-selected="false">Pending Permission</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @if (Session::has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        @if(count($errors))
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <br />
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </section>
    <section class="dashboard-header section-padding">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col">
                    <div class="tab-content tabular-pane" id="pills-tabContent">
                        <div class="tab-pane fade show active bgactive" id="pills-home" role="tabpanel"
                            aria-labelledby="pills-home-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference ID.</th>
                                        <th>Applicant Name</th>
                                        <th>Applicant Type</th>
                                        <th>Permission Type</th>
                                        <th>Permission Mode</th>
                                        <th>Approving Authority</th>
                                        <th>DateTime of Submission</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $data)
                                    <tr>

                                        <td>
                                            <a class="btn btn-outline-danger btn-block" style=" text-align: left;"
                                                href="{{url('/roac/permission/getpermissiondetails')}}/{{encrypt($data->permission_id)}}">
                                                @if(!empty($data->reference_id))
                                                {{$data->reference_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @else
                                                {{$data->permission_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @endif
                                            </a>
                                        </td>

                                        <td>{{$data->name}}</td>
                                        <td>{{$data->role_name}}</td>
                                        <td>{{$data->pname}}</td>
                                        @if($data->permission_mode == 1)
                                        <td>{{'Online'}}</td>
                                        @else
                                        <td>{{'Offline'}}</td>
                                        @endif
                                        <td>
                                            {{$data->Approval_name}}
                                            @switch($data->Approval_name)
                                                @case('CEO')
                                                <span>(S-{{$data->ST_NAME}})</span>
                                                @break
                                                
                                                @case('DEO')
                                                <span>(D-{{$data->DIST_NAME}})</span>
                                                @break
                                              @case('ROAC')
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                                @break
                                                
                                                @default
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                            @endswitch

                                        </td>
                                        <td>{{$data->added_at}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($data->cancel_status == 0)
                                                @if($data->approved_status == 0)
                                                <p class='text-info'>{{'Pending'}}</p>
                                                @elseif($data->approved_status == 1)
                                                <p class='lightgreen'>{{'Inprogress'}}</p>
                                                @elseif($data->approved_status == 2)
                                                <p class='text-success'>{{'Accepted'}}</p>
                                                @else
                                                <p class='text-danger'>{{'Rejected'}}</p>
                                                @endif
                                                @else
                                                <p class='text-danger'>{{'Cancelled'}}</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>

                            </table>
                        </div>
                        <div class="tab-pane fade bgactive" id="pills-profile" role="tabpanel"
                            aria-labelledby="pills-profile-tab">
                            <table id="list-table1" class="table table-striped table-bordered table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference ID</th>
                                        <th>User Name</th>
                                        <th>User Type</th>
                                        <th>Permission Type</th>
                                        <th>Permission Mode</th>
                                        <th>Approving Authority</th>
                                        <th>DateTime of Submission</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $rdata)
                                    @if($rdata->approved_status == 2 && $rdata->cancel_status == 0)
                                    <tr>
                                        <td>
                                            <a class="btn btn-outline-danger btn-block" style=" text-align: left;"
                                                href="{{url('/roac/permission/getpermissiondetails')}}/{{encrypt($rdata->permission_id)}}">
                                                @if(!empty($rdata->reference_id))
                                                {{$rdata->reference_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @else
                                                {{$rdata->permission_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{$rdata->name}}</td>
                                        <td>{{$rdata->role_name}}</td>
                                        <td>{{$rdata->pname}}</td>
                                        @if($rdata->permission_mode == 1)
                                        <td>{{'Online'}}</td>
                                        @else
                                        <td>{{'Offline'}}</td>
                                        @endif
                                        <td>
                                            {{$data->Approval_name}}
                                            @switch($data->Approval_name)
                                                @case('CEO')
                                                <span>(S-{{$data->ST_NAME}})</span>
                                                @break
                                                
                                                @case('DEO')
                                                <span>(D-{{$data->DIST_NAME}})</span>
                                                @break
                                                
                                              @case('ROAC')
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                                @break
                                                
                                                @default
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                            @endswitch

                                        </td>
                                        <td>{{$rdata->added_at}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($rdata->approved_status == 2){{'Accept'}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif
                                </tbody>

                            </table>
                        </div>


                        <div class="tab-pane fade bgactive" id="pills-contact" role="tabpanel"
                            aria-labelledby="pills-contact-tab">
                            <table id="list-table2" class="table table-striped table-bordered table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference ID</th>
                                        <th>User Name</th>
                                        <th>User Type</th>
                                        <th>Permission Type</th>
                                        <th>Permission Mode</th>
                                        <th>Approving Authority</th>
                                        <th>DateTime of Submission</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $adata)
                                    @if($adata->approved_status == 3 && $adata->cancel_status == 0)
                                    <tr>
                                        <td>
                                            <a class="btn btn-outline-danger btn-block" style=" text-align: left;"
                                                href="{{url('/roac/permission/getpermissiondetails')}}/{{encrypt($adata->permission_id)}}">
                                                @if(!empty($adata->reference_id))
                                                {{$adata->reference_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @else
                                                {{$adata->permission_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{$adata->name}}</td>
                                        <td>{{$adata->role_name}}</td>
                                        <td>{{$adata->pname}}</td>
                                        @if($adata->permission_mode == 1)
                                        <td>{{'Online'}}</td>
                                        @else
                                        <td>{{'Offline'}}</td>
                                        @endif
                                        <td>
                                            {{$data->Approval_name}}
                                            @switch($data->Approval_name)
                                                @case('CEO')
                                                <span>(S-{{$data->ST_NAME}})</span>
                                                @break
                                                
                                                @case('DEO')
                                                <span>(D-{{$data->DIST_NAME}})</span>
                                                @break
                                               @case('ROAC')
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                                @break
                                                
                                                @default
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                            @endswitch

                                        </td>
                                        <td>{{$adata->added_at}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($adata->approved_status == 3){{'Reject'}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif
                                </tbody>

                            </table>
                        </div>
                        <div class="tab-pane fade bgactive" id="pills-pending" role="tabpanel"
                            aria-labelledby="pills-contact-tab">
                            <table id="list-table3" class="table table-striped table-bordered table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference ID</th>
                                        <th>User Name</th>
                                        <th>User Type</th>
                                        <th>Permission Type</th>
                                        <th>Permission Mode</th>
                                        <th>Approving Authority</th>
                                        <th>DateTime of Submission</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $pdata)
                                    @if($pdata->approved_status == 0 && $pdata->cancel_status == 0)
                                    <tr>
                                        <td>
                                            <a class="btn btn-outline-danger btn-block" style=" text-align: left;"
                                                href="{{url('/roac/permission/getpermissiondetails')}}/{{encrypt($pdata->permission_id)}}">
                                                @if(!empty($pdata->reference_id))
                                                {{$pdata->reference_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @else
                                                {{$pdata->permission_id}}<i
                                                    class="fa fa-edit float-right font-size01"></i>
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{$pdata->name}}</td>
                                        <td>{{$pdata->role_name}}</td>
                                        <td>{{$pdata->pname}}</td>
                                        @if($pdata->permission_mode == 1)
                                        <td>{{'Online'}}</td>
                                        @else
                                        <td>{{'Offline'}}</td>
                                        @endif
                                        <td>
                                            {{$data->Approval_name}}
                                            @switch($data->Approval_name)
                                                @case('CEO')
                                                <span>(S-{{$data->ST_NAME}})</span>
                                                @break
                                                
                                                @case('DEO')
                                                <span>(D-{{$data->DIST_NAME}})</span>
                                                @break
                                              @case('ROAC')
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                                @break
                                                
                                                @default
                                                <span>(AC-{{$data->AC_NAME}})</span>
                                            @endswitch

                                        </td>
                                        <td>{{$pdata->added_at}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($pdata->approved_status == 0){{'Pending'}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
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
<script type="text/javascript">
$(function() {

});
</script>

@endsection