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
                    <h5 style="text-decoration: underline">Permission Summary Report</h5>
                </div>
            </div>
    <section id="details">

        <div class="container-fluid">
            <form name="report" method="post"  action="{{url('/acceo/permissionsummaryreport')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-5  row">
                        <label for="state" class="col-sm-4 col-form-label">Select District</label>
                        <div class="col-sm-8 distt">
                            <select name="dist" id="dist" class="form-control">
                                <option value="0">-- Select District --</option>
                                <option value="all">Select All</option>
                                @foreach($distvalue as $dist)
                                <option value="{{$dist->DIST_NO }}" {{ (collect(old('dist'))->contains($dist->DIST_NO)) ? 'selected':'' }}> 
                                    {{$dist->DIST_NAME }}
                                </option>
                                @endforeach 
                            </select>
                            <span class="text-danger">{{ $errors->error->first('dist') }}</span>
                        </div>
                    </div>
                     <div class="col-sm-5 row">
                        <label for="ac" class="col-sm-4 col-form-label">Select AC</label>
                        <div class="col-sm-8 distt">
                            <select name="ac" id="ac" class="form-control">
                                <option value="0">-- Select AC --</option>
                                <option value="all">Select All</option>
                            </select>
                            <span class="text-danger">{{ $errors->error->first('ac') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-2  row">
                        <input type="submit"  value="Submit" name="excel" class="btn btn-primary getdata">
                    </div>
                    
                </div>
            </form>
            @if(!empty($ac_no) && !empty($dist_no))
            <div class="row">
                    <div class="col-sm-12" style="">
                        <div class="float-right mt-5">
                        <a href="{{url('/acceo/ceopermissionsummaryreport')}}/{{$ac_no}}/{{$dist_no}}/{{'pdf'}}" class="mr-3" style="float: left"><i class="fa fa-file-pdf-o text-warning"></i> Print PDF</a>
                        <a href="{{url('/acceo/ceopermissionsummaryreport')}}/{{$ac_no}}/{{$dist_no}}/{{'excel'}}" class="" style="float: left"><i class="fa fa-file-excel-o text-success"></i> Print Excel</a>
                    </div>
                    </div>
            </div>
            @else
                <div class="row">
                    <div class="col-sm-12" style="">
                        <div class="float-right mt-5">
                        <a href="{{url('/acceo/ceopermissionsummaryreport')}}/{{'0'}}/{{'0'}}/{{'pdf'}}" class="mr-3" style="float: left"><i class="fa fa-file-pdf-o text-warning"></i> Print PDF</a>
                        <a href="{{url('/acceo/ceopermissionsummaryreport')}}/{{'0'}}/{{'0'}}/{{'excel'}}" class="" style="float: left"><i class="fa fa-file-excel-o text-success"></i> Print Excel</a>
                    </div>
                    </div>
            </div>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>District Name</th>
                                <th>AC Name</th>
                                <th>Pending_within_time</th>
                                <th>Pending_beyond_time</th>
                                <th>Accepted</th>
                                <th>Inprogress</th>
                                <th>Rejected</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($summarydata))
                                @foreach($summarydata as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->DIST_NAME}}</td>
                                    <td>{{$data->ac_name}}</td>
                                    <td><a href="{{url('acceo/permissionsummaryreportdetails')}}/{{$data->dist_no}}/{{$data->ac_no}}/{{'0'}}">{{$data->Pending_within_time}}</a></td>
                                    <td><a href="{{url('acceo/permissionsummaryreportdetails')}}/{{$data->dist_no}}/{{$data->ac_no}}/{{'1'}}">{{$data->Pending_beyond_time}}</a></td>
                                    <td><a href="{{url('acceo/permissionsummaryreportdetails')}}/{{$data->dist_no}}/{{$data->ac_no}}/{{'2'}}">{{$data->Accepted}}</a></td>
                                    <td><a href="{{url('acceo/permissionsummaryreportdetails')}}/{{$data->dist_no}}/{{$data->ac_no}}/{{'3'}}">{{$data->Inprogress}}</a></td>
                                    <td><a href="{{url('acceo/permissionsummaryreportdetails')}}/{{$data->dist_no}}/{{$data->ac_no}}/{{'4'}}">{{$data->Rejected}}</a></td>
                                    <td><a href="{{url('acceo/permissionsummaryreportdetails')}}/{{$data->dist_no}}/{{$data->ac_no}}/{{'5'}}">{{$data->total}}</a></td>
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
@section('script')
<script>
    $(document).ready(function() {
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');

        jQuery("select[name='dist']").change(function()
        {
            var dist = jQuery(this).val();
            jQuery.ajax({
                url: base_url + '/acceo/getAllAC',
                type: 'POST',
                data: {_token: token, dist: dist},
                success: function(data) {
                    //alert(data);
                    if (data != '') {
                        var distselect = jQuery('form select[name=ac]');
                        distselect.empty();
                        var statehtml = '';
                        statehtml = statehtml + '<option value="0"> -- Select AC --</option><option value="all">Select All</option> ';
                        jQuery.each(data, function(key, value) {
                            statehtml = statehtml + '<option value="' + value.AC_NO + '">' + value.AC_NAME + '</option>';
                            jQuery("select[name='ac']").html(statehtml);
                        });
                        var statehtml_end = '';
                        jQuery("select[name='ac']").append(statehtml_end);
                    } else {
                        //alert('test');
                        jQuery("select[name='ac']").html('<option value=""> -- Select AC --</option><option value="all">Select All</option>');
                    }

                }
            });
        });
    });
</script>
@endsection
