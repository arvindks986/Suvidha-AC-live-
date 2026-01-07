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
    <section id="details">

        <div class="container-fluid">
            <form name ="report" method="post"  action="{{url('/roac/reporttimes')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-3  row">
                        <label for="state" class="col-sm-4 col-form-label">Select District</label>
                        <div class="col-sm-8 distt">
                            <select name="dist" id="dist" class="form-control">
                                <option value="{{$distvalue->DIST_NO }}" selected=""> {{$distvalue->DIST_NAME}}</option>
                            </select>
                            <span class="text-danger">{{ $errors->error->first('dist') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-3  row">
                        <label for="ac" class="col-sm-4 col-form-label">Select AC</label>
                        <div class="col-sm-8 distt">
                            <select name="ac" id="ac" class="form-control">
                                <option value="{{$acdata->AC_NO }}" selected=""> {{$acdata->AC_NAME}}</option>
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
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                    </div>
                    <div class="col-sm-1  row">
                    </div>
                    <div class="col-sm-1  row">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </div>
            </form>

        </div>
    </section>

</main>
@endsection
