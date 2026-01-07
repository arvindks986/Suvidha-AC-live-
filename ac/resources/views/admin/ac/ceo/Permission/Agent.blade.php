@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    @if (Session::has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
            <div class="row d-flex">
                <div class="col-lg-3" style="text-align: center; margin: 0 auto;">
                    <!-- Income-->
                    <div class="card income text-center">
                        <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                        <div class="text-success"><b>Agent</b> &nbsp; <div class="btn-group float-right">
                                <a type="button" href="{{url('/acceo/viewagent')}}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                                <a type="button" href="{{url('/acceo/agentCreation')}}"
                                    class="btn btn-sm btn-primary">Add</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card">
                            <!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>ADD Agent</h2>
                            </div>
                            <div class="card-body getpermission">



                                <form class="form-horizontal" method="POST" action="{{url('/acceo/addagent')}}">
                                    {{csrf_field()}}

                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Name <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Enter Name"
                                                name="uname" value="{{old('uname')}}" required>
                                            <span class="text-danger">{{ $errors->error->first('uname') }}</span>
                                        </div>
                                    </div>

                                    <!-- <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Department <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Enter Department" name="dept" value="{{old('dept')}}">
                                            <span class="text-danger">{{ $errors->error->first('dept') }}</span>
                                        </div>
                                    </div>-->

                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Designation <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Enter Designation"
                                                name="desig" value="{{old('desig')}}" required>
                                            <span class="text-danger">{{ $errors->error->first('desig') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Mobile No <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Enter Mobile Number"
                                                name="mb" value="{{old('mb')}}" required maxlength="10"  minlength="10">
                                            <span class="text-danger">{{ $errors->error->first('mb') }}</span>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Email Id <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control" placeholder="Enter Email ID"
                                                name="email" value="{{old('email')}}" required
                                                pattern="[^@\s]+@[^@\s]+\.[^@\s]+">
                                            <span class="text-danger">{{ $errors->error->first('email') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Password <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="password" class="form-control" placeholder="Enter Password"
                                                name="pass" value="{{old('pass')}}" required>
                                            <span class="text-danger">{{ $errors->error->first('pass') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Pin <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="pincode" maxlength="6"
                                                id="pin" pattern="^[0-9]{6}$" placeholder="Enter 6 digit PIN" required
                                                onkeypress="return mask(this,event);" />

                                            <span class="text-danger">{{ $errors->error->first('pincode') }}</span>
                                        </div>
                                    </div>

                                    <!--                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <textarea name="address" class="form-control" placeholder="Add Address Here" id="" cols="3" rows="4"></textarea>
                                        </div>
                                    </div>-->

                                    <div class="card-footer">
                                        <div class="form-group row">

                                            <div class="col">
                                                <button class="btn btn-success float-right" name="addag"
                                                    type="submit">ADD</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

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
function mask(textbox, e) {

    var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode == 46 || charCode > 31 && (charCode < 48 || charCode > 57)) {
        alert("Only Numbers Allowed");
        return false;
    } else {
        return true;
    }
}
</script>
@endsection