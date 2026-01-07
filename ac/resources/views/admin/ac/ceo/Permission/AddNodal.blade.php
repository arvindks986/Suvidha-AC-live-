@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
@include('admin.ac.ceo.Permission.permission-master-menu')
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
                        <!--  style="max-width:700px; margin:0 auto;" -->
                        <div class="card-header d-flex align-items-center">
                            <h2>ADD Nodal</h2>
                        </div>
                        @if (Session::has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                        @endif
                        <div class="card-body getpermission">



                            <form class="form-horizontal" method="POST" action="{{url('/acceo/addnodaldata')}}">
                                {{csrf_field()}}

                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Mobile No <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Mobile Number"
                                            name="mb" id="mobileno" value="{{old('mb')}}" required  maxlength="10" minlength="10">
                                        <span class="text-danger">{{ $errors->error->first('mb') }}</span>
                                        <span id="messege-text"
                                            style="color: blueviolet;font-size: 14px; line-height: 2px;"></span>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <label class="col-sm-4 form-control-label">Select Approving Authority<sup>*</sup>
                                        <br /><span class="text-danger">(Authority type will be added by CEO)</span>
                                    </label>

                                    <div class="col-sm-8">
                                        <select class="form-control" name="authid" required>
                                            <option value="">Select Approving Authority</option>
                                            @if(!empty($authority))
                                            @foreach($authority as $data)
                                            <option value="{{$data->id}}">
                                                @if (!empty($data->name))
                                                {{$data->name}}
                                                @endif
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger">{{ $errors->error->first('authid') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Department <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Department"
                                            name="dept" value="{{ old('dept') }}" required>
                                        <span class="text-danger">{{ $errors->error->first('dept') }}</span>
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

                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Name <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Name" name="name"
                                            value="{{ old('name') }}" required>
                                        <span class="text-danger">{{ $errors->error->first('name') }}</span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Designation <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Designation"
                                            name="desig" value="{{old('desig')}}" required>
                                        <span class="text-danger">{{ $errors->error->first('desig') }}</span>
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Incharge Email Id <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" placeholder="Enter Email ID"
                                            name="email" value="{{old('email')}}" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+">
                                        <span class="text-danger">{{ $errors->error->first('email') }}</span>
                                    </div>
                                </div>

                                <!--						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Epic No <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" placeholder="Enter Epic Number" name="eno" value="{{old('eno')}}">
                           <span class="text-danger">{{ $errors->error->first('eno') }}</span>
                          </div>
                        </div>-->

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
@section('script')

<script>
$(document).ready(function() {


    $('#mobileno').on('blur', function(event) {

        var mobileno = $("#mobileno").val();
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        let el_down = document.getElementById("messege-text");
        $.ajax({

            method: 'POST',
            url: APP_URL + "/acceo/getmobile",
            data: {
                _token: csrf_token,
                mobileno: mobileno,
            },
            dataType: 'JSON',
            success: function(data) {
                console.log(data);
                if (data.status) {
                    $('input[name=dept]').val(data.result.department);
                    $('textarea[name=addr]').val(data.result.address);
                    $('input[name=name ]').val(data.result.name);
                    $('input[name=desig]').val(data.result.designation);
                    $('input[name=email]').val(data.result.email);
                    $('select[name=authid]').val(data.result.auth_type_id);
                    el_down.innerHTML = "Nodal has been created with this mobile number,";

                    el_down.innerHTML +=
                        "<a href='javascript:void(0);' onclick='window.location.reload();'> Click here </a> to use another mobile number.<br />"
                    el_down.innerHTML +=
                        "If you want to use this nodal then click on the 'ADD' button given below.";
                    $(':input').attr('readonly', 'readonly');
                    // $(':select').attr('disabled', 'disabled');
                    $('option').each(function() {
                            !this.selected ? $(this).attr('disabled', true) : "";
                            });

                }
            },
            error: function(response) {}
        });
    });

});
</script>

@endsection