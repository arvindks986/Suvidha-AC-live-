@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 

<main role="main" class="inner cover mb-3 mb-auto">
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
 @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
   @include('admin.ac.deo.Permission.permission-master-menu')
    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>Assign permission To PCI</h2>
                            </div>
                            <div class="card-body getpermission">



                                <form class="form-horizontal" method="POST" action="{{url('/acdeo/assigntopcidata')}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                                        <div class="col-sm-8">
<!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                                            <select name="pname" class="form-control" >
                                                <option value="0">Select Permission</option>
                                                @if(!empty($permissionDetails))
                                                @foreach($permissionDetails as $pdata)
                                                <option value="{{$pdata->permission_type_id}}" {{ (collect(old('pname'))->contains($pdata->permission_type_id)) ? 'selected':'' }}>
                                                {{$pdata->permission_name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                        </div>
                                        
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Assigned to Level<sup>*</sup></label>
                                        <div class="col-sm-8">
<!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                                            <select name="ofcrlevel" class="form-control" >
                                                <option value="0">Select Assigned to Level</option>
                                                @if(!empty($getrole))
                                                @foreach($getrole as $pdata)
                                                <option value="{{$pdata->role_id}}" {{ (collect(old('ofcrlevel'))->contains($pdata->role_id)) ? 'selected':'' }}>{{$pdata->role_name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->error->first('ofcrlevel') }}</span>
                                        </div>
                                        
                                    </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group row">

                                    <div class="col">
                                        <button class="btn btn-success float-right">Submit</button>
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

</main>
@endsection
