@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
@include('admin.ac.deo.Permission.permission-master-menu')
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
                    <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                        <div class="card-header d-flex align-items-center">
                            <h2>ADD Permission Cell Incharge</h2>
                        </div>
                        @if (Session::has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                        @endif
                        <div class="card-body getpermission">



                            <form class="form-horizontal" method="POST" action="{{url('/acdeo/addpcidata')}}">
                                {{csrf_field()}}
                                <div class="form-group row">
                                            <label class="col-sm-4 form-control-label">Select Role <sup>*</sup></label>
                                            <div class="col-sm-8">
                                            <select name="pname" class="form-control" >
                                                <option value="0">Select Role</option>
                                                @if(!empty($getPCIrole))
                                                @foreach($getPCIrole as $pdata)
                                                <option value="{{$pdata->role_id}}" {{ (collect(old('pname'))->contains($pdata->role_id)) ? 'selected':'' }}>{{$pdata->role_name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                        </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Name <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ old('name') }}">
                                        <span class="text-danger">{{ $errors->error->first('name') }}</span>
                                    </div>
                                </div>	

                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Designation <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Designation" name="desig" value="{{old('desig')}}">
                                        <span class="text-danger">{{ $errors->error->first('desig') }}</span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Mobile No <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Mobile Number" name="mb" value="{{old('mb')}}">
                                        <span class="text-danger">{{ $errors->error->first('mb') }}</span>
                                    </div>
                                </div>	
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Email Id <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" placeholder="Enter Email ID" name="email" value="{{old('email')}}">
                                        <span class="text-danger">{{ $errors->error->first('email') }}</span>
                                    </div>
                                </div>
                                 <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Department <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Department" name="dept" value="{{ old('dept') }}">
                                        <span class="text-danger">{{ $errors->error->first('dept') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <textarea name="addr" class="form-control" placeholder="Add Address Here" id="" cols="3" rows="4">{{old('addr')}}</textarea>
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