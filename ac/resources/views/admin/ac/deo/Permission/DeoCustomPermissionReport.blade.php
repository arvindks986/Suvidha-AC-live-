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
            <form name ="report" method="post"  action="{{url('/acdeo/customtimewisereport')}}"> 
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
                        <div class="col-sm-12 ">
                                <input class="form-control" type="text" name="time" placeholder="Enter Custom Time" value="{{old('time')}}">
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

