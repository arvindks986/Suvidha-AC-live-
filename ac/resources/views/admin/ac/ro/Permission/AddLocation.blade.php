@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')

@if (Session::has('message'))
<div class="alert alert-success">
    {{ session()->get('message') }}
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
<section class="mt-5" id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="sidebar__inner">
                    <div class="card">
                         
                        <div class="card-header d-flex align-items-center">
                            <h2>ADD Location</h2>
                        </div>
                        <div class="card-body getpermission">



                            <form class="form-horizontal" method="POST"
                                action="{{url('/roac/permission/AddLocationinsert')}}">

                                {{csrf_field()}}
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Name <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Name" name="name"
                                            value="{{old('name')}}" required>
                                        <span class="text-danger">{{ $errors->error->first('name') }}</span>
                                    </div>
                                </div>



                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <textarea name="addr" class="form-control" placeholder="Add Address Here" id=""
                                            cols="3" rows="4" required>{{old('addr')}}</textarea>
                                        <span class="text-danger">{{ $errors->error->first('addr') }}</span>
                                    </div>
                                </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group row">

                                <div class="col">
                                    <button class="btn btn-success float-right" name="submit" value="ADD">ADD</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>





        </div>
    </div>

</section>


@endsection