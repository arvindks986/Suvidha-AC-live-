@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
@if (session('message'))
<div class="alert alert-success">
    {{ session('message') }}
</div>
@endif
<section class="statistics">
    <div class="container-fluid mt-5 mb-5">
        <div class="row d-flex">
            <div class="col pl-0">
                <!-- Income-->
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
                <!-- Income-->
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


@if (session('chckmessage'))
<div class="alert alert-danger">
    {{ session('chckmessage') }}
</div>
@endif
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="card" style="max-width:700px; margin:0 auto;">
                    <div class="card-header d-flex align-items-center">
                        <h5>ADD Police Station</h5>
                    </div>
                    <form class="form-horizontal" action="{{url('/roac/permission/AddPSData')}}" method="POST">
                        <div class="card-body">

                            {{csrf_field()}}
                            <div class="form-group row">
                                <label class="col-sm-4 form-control-label">Police Station Name <sup>*</sup></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="ps_name" value="{{old('ps_name')}}" required>
                                    <span class="text-danger" required>{{ $errors->first('ps_name') }}</span>
                                </div>
                            </div>
                            <div class="line"></div>
                            <div class="form-group row">
                                <label class="col-sm-4 form-control-label">Police Station Address <sup>*</sup></label>
                                <div class="col-sm-8">
                                    <textarea name="ps_addr" id="" cols="3" rows="2"
                                        class="form-control" required>{{old('ps_addr')}}</textarea>
                                    <span class="text-danger" required>{{ $errors->first('ps_addr') }}</span>
                                </div>
                            </div>
                            <div class="line"></div>
                            <div class="form-group row">

                                <label class="col-sm-4 form-control-label">Incharge Name<sup>*</sup></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="uname" value="{{old('uname')}}" required>
                                    <span class="text-danger" required>{{ $errors->first('uname') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">

                                <label class="col-sm-4 form-control-label">Police Station Incharge Mobile
                                    No<sup>*</sup></label>
                                <div class="col-sm-8">
                                    <input type="tel" class="form-control" name="ps_imb" value="{{old('ps_imb')}}" required pattern="[6789][0-9]{9}">
                                    <span class="text-danger" required>{{ $errors->first('ps_imb') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 form-control-label">Police Station Mobile No<sup>*</sup></label>
                                <div class="col-sm-8">
                                    <input type="tel" class="form-control" name="ps_smb" value="{{old('ps_smb')}}" required pattern="[6789][0-9]{9}">
                                    <span class="text-danger" required>{{ $errors->first('ps_smb') }}</span>
                                </div>
                            </div>




                        </div>
                        <div class="card-footer">
                            <div class="form-group row ">
                                <div class="col">
                                    
                                    <button type="submit" class="btn btn-primary float-right" name="AddPS"
                                        value="Save">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection