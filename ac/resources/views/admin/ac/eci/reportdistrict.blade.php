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
             <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">District Datewise Permission Report</h5>
                </div>
            </div>
        <div class="container-fluid">
            <form name = "report" method="post"  action="{{url('/eci/districtwisereportview')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-4 row">
                        <label for="state" class="col-sm-4 col-form-label">Election Type</label>
                        <div class="col-sm-8 distt">
                            <select name="elect" id="state" class="form-control">
                                <option value="0">-- All Election --</option>                             
                                <option value="3">GEN Election</option>
                                <option value="4">BYE Election</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4  row">
                        <label for="state" class="col-sm-4 col-form-label">State</label>
                        <div class="col-sm-8 distt">
                            <select name="state" id="state" class="form-control">
                                 <option value="0">-- All State --</option>
                                @foreach($statevalue as $State)
                                <option value="{{$State->ST_CODE }}"> 
                                    {{$State->ST_NAME }}
                                </option>
                                @endforeach 
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4  row">
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
                </div>
            </form>
            
            <div class="row">
                 <div class="col-sm-12 text-right">
                <form name ="report" method="post"  action="{{url('/eci/districtwisereport')}}">
                {{csrf_field()}}
                <input type="hidden" name="datefilter" value="{{$datefilter}}" class="form-control" >
                <input type="hidden" name="state" class="form-control" value="{{$state}}">
                <input type="hidden" name="elect" class="form-control" value="{{$election}}">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>State Name</th>
                                <th>District Name</th>
                                <th>Total request</th>
                                <th>Approved</th>
                                <th>Rejected</th>
                                <th>Inprogress</th>
                               <!--  <th>Pending</th>
                                <th>Cancel</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @php $counttotal = 0;$countaccept = 0;$countreject = 0;$countinprogress = 0;$countpending = 0;$countcancel = 0; @endphp
                                @if(!empty($datereport))
                                @foreach($datereport as $key => $data)
                                @php 
                                 $reject = $data->Rejected+$data->Cancel;
                                $inprogress = $data->Inprogress+$data->Pending;

                                $counttotal = $counttotal + $data->Total;
                                $countaccept = $countaccept + $data->Accepted;
                                $countreject = $countreject + $reject;
                                $countinprogress = $countinprogress + $inprogress;
                                $countpending = $countpending + $data->Pending;
                                $countcancel = $countcancel + $data->Cancel;
                                @endphp
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->ST_NAME}}</td>
                                    <td>{{$data->DIST_NAME}}</td>
                                    <td><a href="{{url('eci/districtwisereportdetails')}}/{{$data->st_code}}/{{$data->dist_no}}/{{$election}}/{{$datefilter}}/{{'6'}}">{{$data->Total}}</a></td>
                                    <td><a href="{{url('eci/districtwisereportdetails')}}/{{$data->st_code}}/{{$data->dist_no}}/{{$election}}/{{$datefilter}}/{{'2'}}">{{$data->Accepted}}</a></td>
                                     <td><a href="{{url('eci/districtwisereportdetails')}}/{{$data->st_code}}/{{$data->dist_no}}/{{$election}}/{{$datefilter}}/{{'3'}}">{{$reject}}</a></td>
                                    <td><a href="{{url('eci/districtwisereportdetails')}}/{{$data->st_code}}/{{$data->dist_no}}/{{$election}}/{{$datefilter}}/{{'1'}}">{{$inprogress}}</a></td>
                                   <!--  <td><a href="{{url('eci/districtwisereportdetails')}}/{{$data->st_code}}/{{$data->dist_no}}/{{$election}}/{{$datefilter}}/{{'0'}}">{{$data->Pending}}</a></td>
                                    <td><a href="{{url('eci/districtwisereportdetails')}}/{{$data->st_code}}/{{$data->dist_no}}/{{$election}}/{{$datefilter}}/{{'5'}}">{{$data->Cancel}}</a></td> -->
                                </tr>
                                @endforeach
                                @endif
                                 <tr>
                                    <td colspan="3"><a href="javascript::void(0)"><span>Grand Total</span></td>
                                    <td><a href="javascript::void(0)">{{$counttotal}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countaccept}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countreject}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countinprogress}}</a></td>
                                    <!-- <td><a href="javascript::void(0)">{{$countpending}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countcancel}}</a></td> -->
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
@section('script')


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
