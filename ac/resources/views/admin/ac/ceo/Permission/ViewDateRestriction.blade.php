@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.css') }}">
<script src="{{ asset('js/bootstrap-multiselect.js') }}"></script>

<main role="main" class="inner cover mb-3 mb-auto">
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    @if(count($errors->error))
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.
        <br />
        <ul>
            @foreach($errors->error->all() as $erro)
            <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @include('admin.ac.ceo.Permission.permission-master-menu')
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h2>Permission Day Restriction List</h2>
                    </div>
                    <div class="card-body tabular-pane">
                        <table id="list-table1" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Permission Name</th>
                                    <th>Restriction Day</th>
                                    <th>Edit/Update</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @if(!empty($ViewDateRestrictData))
                                @foreach($ViewDateRestrictData as $data)
                                <tr>
                                    <td>{{$i}}</td>

                                    <td>{{$data->permission_name}}</td>
                                    <td>{{$data->restriction_day}} Days</td>
                                    <td id="edit"><a href="{{url('/acceo/editDateRestrict')}}/{{Crypt::encryptString($data->id)}}"><span
                                                class=" btn btn-success float-right">Edit</span></a></td>
                                    <td id="delete"><a href="{{url('/acceo/deleteDateRestrict')}}/{{$data->id}}"
                                            onclick="return confirm('Are you sure?')"><span
                                                class=" btn btn-success float-right">Delete</span></a></td>
                                </tr>
                                @php $i++; @endphp
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