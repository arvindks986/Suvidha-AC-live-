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
            <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Datewise Permission Report</h5>
                </div>
            </div>
            <form name ="report" method="post"  action="{{url('/acceo/reportdatesview')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-3  row">
                        <label for="state" class="col-sm-4 col-form-label">Select District</label>
                        <div class="col-sm-8 distt">
                            <select name="pc" id="pc" class="form-control">
                                <option value="statevalue">-- Select District --</option>
                                <option value="all">All</option>
                                @foreach($distvalue as $dist)
                                <option value="{{$dist->DIST_NO }}"> 
                                    {{$dist->DIST_NAME }}
                                </option>
                                @endforeach 
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3  row">
                        <label for="ac" class="col-sm-4 col-form-label">Select AC</label>
                        <div class="col-sm-8 distt">
                            <select name="ac" id="ac" class="form-control">
                                <option value="">-- Select AC --</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3  row">
                        <label for="ac" class="col-sm-2 col-form-label">Date</label>
                        <div class="col-sm-8 distt">
                            <div class='input-group date datetimepicker1' id=''>
                                <input type="text" autocomplete = "off" id="demo" placeholder='Search via Date' name="datefilter" class="form-control" >

                            </div>
                        </div>
                    </div>


                    <div class="col-sm-1  row">
                        <input type="submit"  value="Submit" name="submit" class="btn btn-primary getdata">
                    </div>
                    <div class="col-sm-1  row">
                    </div>
<!--                    <div class="col-sm-1  row">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>-->
                </div>
            </form>
            <!--            new table-->
            <form name ="report" method="post"  action="{{url('/acceo/reportdates')}}">
                {{csrf_field()}}
            <input type="hidden" name="datefilter" value="{{$datefilter}}" class="form-control" >
                <input type="hidden" name="ac" class="form-control" value="{{$ac_no}}">
                <input type="hidden" name="pc" class="form-control" value="{{$district}}">
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
                                @if($filter == 1)
                                <th>State Name</th>
                                @elseif($filter == 2)
                                <th>District Name</th>
                                @else
                                <th>AC Name</th>
                                @endif
                                <th>Total_request</th>
                                <th>Approved</th>
                                <th>Rejected</th>
                                <th>Inprogress</th>
                                <th>Pending</th>
                                <th>Cancel</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($datereport))
                                @foreach($datereport as $key => $data)
                                <?php
                                if(!empty($datefilter)){
                                     $datefilters = $datefilter;
                                 }
                                 else{
                                    $datefilters ='0';
                                 }
                                ?>
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    @if($filter == 1)
                                    <td>{{$data->ST_NAME}}</td>
                                    @elseif($filter == 2)
                                    <td>{{$data->DIST_NAME}}</td>
                                    @else
                                    <td>{{$data->AC_NAME}}</td>
                                    @endif
                                    <td><a href="{{url('acceo/permissiondetails')}}/{{$data->st_code}}/{{'3'}}/{{$datefilters}}/{{$data->Dist_no}}/{{'6'}}">{{$data->total_request}}</a></td>
                                    <td><a href="{{url('acceo/permissiondetails')}}/{{$data->st_code}}/{{'3'}}/{{$datefilters}}/{{$data->Dist_no}}/{{'2'}}">{{$data->approved}}</a></td>
                                    <td><a href="{{url('acceo/permissiondetails')}}/{{$data->st_code}}/{{'3'}}/{{$datefilters}}/{{$data->Dist_no}}/{{'3'}}">{{$data->rejected}}</a></td>
                                    <td><a href="{{url('acceo/permissiondetails')}}/{{$data->st_code}}/{{'3'}}/{{$datefilters}}/{{$data->Dist_no}}/{{'1'}}">{{$data->inprogress}}</a></td>
                                    <td><a href="{{url('acceo/permissiondetails')}}/{{$data->st_code}}/{{'3'}}/{{$datefilters}}/{{$data->Dist_no}}/{{'0'}}">{{$data->pending}}</a></td>
                                    <td><a href="{{url('acceo/permissiondetails')}}/{{$data->st_code}}/{{'3'}}/{{$datefilters}}/{{$data->Dist_no}}/{{'5'}}">{{$data->Cancel}}</a></td>
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
    $(document).ready(function () {
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');

        jQuery("select[name='pc']").change(function ()
        {
            var pc = jQuery(this).val();
//alert(pc);
            jQuery.ajax({
                url: base_url + '/acceo/getDistrictsval',
                type: 'GET',
                data: {token: token, pc: btoa(pc)},
                success: function (data) {
                    //alert(data);
                    if (data != '') {
                        var distselect = jQuery('form select[name=ac]');
                        distselect.empty();
                        var statehtml = '';
                        statehtml = statehtml + '<option value=""> -- Select AC --</option> ';
                        jQuery.each(data, function (key, value) {
                            statehtml = statehtml + '<option value="' + value.AC_NO + '">' + value.AC_NAME + '</option>';
                            jQuery("select[name='ac']").html(statehtml);
                        });
                        var statehtml_end = '';
                        jQuery("select[name='ac']").append(statehtml_end);
                    } else {
                        //alert('test');
                        jQuery("select[name='ac']").html('<option value=""> -- Select AC --</option>');
                    }

                }
            });
        });
    });
</script>

<link href="{{url('/admintheme/css/daterangepicker.css')}}" rel="stylesheet" id="bootstrap-css">
<script type="text/javascript" src="{{url('/admintheme/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{url('admintheme/js/daterangepicker.js')}}"></script>
<script>
    var selectdate = "";
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    today = yyyy + '-' + mm + '-' + dd;
    var start_date = today;
    var end_date = today;
    $('#demo').daterangepicker({
        "ranges": {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "startDate": Date.now(),
        "endDate": Date.now(),
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    }, function (start, end, label) {
        //alert("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
        //alert(start.format('YYYY-MM-DD'));
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');

    });
    $('#demo').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + '~' + picker.endDate.format('YYYY-MM-DD'));
        //alert(picker.startDate.format('YYYY-MM-DD') + '~' + picker.endDate.format('YYYY-MM-DD'));


    });

    $('#demo').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
</script>
@endsection
