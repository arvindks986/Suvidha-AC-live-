@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<?php
//echo "<pre>";
//print_r($perm);
//exit;
?>
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Timewise Permission Report</h5>
                </div>
            </div>
    <section id="details">

        <div class="container-fluid">
            <form name ="report" method="post"  action="{{url('/acdeo/reporttimesview')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-3  row">
                        <label for="state" class="col-sm-4 col-form-label">Select District</label>
                        <div class="col-sm-8 distt">
                            <select name="dist" id="dist" class="form-control">
                                <option value="{{$distvalue->DIST_NO}}" selected="">{{$distvalue->DIST_NAME}}</option>
                            </select>
                            <span class="text-danger">{{ $errors->error->first('dist') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-3  row">
                        <label for="ac" class="col-sm-4 col-form-label">Select AC</label>
                        <div class="col-sm-8 distt">
                            <select name="ac" id="ac" class="form-control">
                                <option value="0">-- Select AC --</option>
                                <option value="all">Select All</option>
                                @foreach($allac as $ac)
                                <option value="{{$ac->AC_NO }}" {{ (collect(old('ac'))->contains($ac->AC_NO)) ? 'selected':'' }}> 
                                    {{$ac->AC_NAME }}
                                </option>
                                @endforeach 
                            </select>
                            <span class="text-danger">{{ $errors->error->first('ac') }}</span>
                        </div>
                    </div>

                    <div class="col-sm-3  row">
                        <label for="time" class="col-sm-2 col-form-label">Select Time</label>
                        <div class="col-sm-8 distt">
                                <select name="time" id="time" class="form-control">
                                    <option value="0">-- Select Time --</option>
                                    <option value="1" {{ (collect(old('time'))->contains(1)) ? 'selected':'' }}>Up to 1 hours</option>
                                    <option value="12" {{ (collect(old('time'))->contains(12)) ? 'selected':'' }}>Up to 12 hours</option>
                                    <option value="24" {{ (collect(old('time'))->contains(24)) ? 'selected':'' }}>Up to 24 hours</option>
                                </select>
                                <span class="text-danger">{{ $errors->error->first('time') }}</span>
                        </div>
                    </div>


                    <div class="col-sm-1  row">
                        <input type="submit"  value="Submit" name="submit" class="btn btn-primary getdata">
                    </div>
                   
                </div>
            </form>
             <form name ="report" method="post"  action="{{url('/acdeo/reporttimes')}}">
                {{csrf_field()}}
            <input type="hidden" name="time" value="{{$time}}" class="form-control" >
                <input type="hidden" name="ac" class="form-control" value="{{$ac_no}}">
            <div class="row">
                <div class="col-sm-12">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </div>
            </div>
            </form>
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Reference id</th>
                                <th>Username</th>
                                <th>Permission name</th>
                                <th>Permission Mode</th>
                                <th>Applicant Type</th>
                                <th>DateTime of Submission</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($report))
                                @foreach($report as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->reference_id}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->pname}}</td>
                                    @if($data->permission_mode == 0)
                                    <td>{{"Offline"}}</td>
                                    @else
                                    <td>{{"Online"}}</td>
                                    @endif
                                    <td>{{ $data->role_name}}</td>
                                    <td>{{ $data->added_at}}</td>
                                     @if($data->approved_status == 0)
                                    <td>{{"Pending"}}</td>
                                    @else
                                     <td>{{"Inprogress"}}</td>
                                    @endif
                                </tr>
                                @endforeach
                                @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection

