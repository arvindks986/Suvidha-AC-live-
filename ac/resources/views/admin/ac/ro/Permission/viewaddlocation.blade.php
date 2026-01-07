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
                                <a type="button" href="{{url('/roac/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/roac/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <!-- Income-->
                <div class="card income ">
                    <div class="card-body">
                        <div class="text-info"><b>Nodal</b> &nbsp; <div class="btn-group float-right">
                                <a type="button" href="{{url('/roac/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/roac/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col pr-0">

                <div class="card income">
                    <div class="card-body">
                        <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right">
                                <a type="button" href="{{url('/roac/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/roac/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>
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
    <div class="container">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h2>Location List</h2>
                    </div>
                    <div class="card-body tabular-pane">
                        <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Edit/Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($getAllPermsDatas))
                                @foreach($getAllPermsDatas as $key=> $data)
                                <tr>
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$data->location_name}}</td>
                                    <td>{{$data->location_details}}</td>
                                    <td id="edit"><a href="{{url('/roac/permission/locationeditpermsn')}}/{{Crypt::encryptString($data->id)}}"><span class=" btn btn-success float-right">Edit</span></a></td>
                                </tr>
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