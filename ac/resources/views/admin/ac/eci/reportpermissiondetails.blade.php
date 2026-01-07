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
                    <h5 style="text-decoration: underline">Datewise Permission Report</h5>
                </div>
            </div>
        <div class="container-fluid">
            <form name = "report" method="post"  action="{{url('/eci/reportdatesview')}}"> 
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
                <div class="col-sm-12 mt-2">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>State Name</th>
                                <th>District Name</th>
                                <th>AC Name</th>
                                <th>Reference ID.</th>
                                <th>Applicant Name</th>
                                <th>Applicant Type</th>
                                <th>Permission Type</th>
                                <th>Permission Mode</th>
                                <th>DateTime of Submission</th>
                                <th>Status</th>   
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($datereport))
                                @foreach($datereport as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->ST_NAME}}</td>
                                    <td>{{$data->DIST_NAME}}</td>
                                    <td>{{$data->AC_NAME}}</td>
                                    <td>{{$data->reference_id}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->role_name}}</td>
                                    <td>{{$data->pname}}</td>
                                    @if($data->permission_mode == 1)
                                     <td>{{'Online'}}</td>
                                     @else
                                     <td>{{'Offline'}}</td>
                                     @endif

                                    <td>{{$data->added_at}}</td>
                                    <td>
                                    <div class="text-warning text-center">
                                    @if($data->cancel_status == 0)
                                    @if($data->approved_status == 0)
                                    <p class='text-info'>{{'Pending'}}</p>
                                    @elseif($data->approved_status == 1)
                                    <p class='lightgreen'>{{'Inprogress'}}</p>
                                    @elseif($data->approved_status == 2)
                                    <p class='text-success'>{{'Accepted'}}</p>
                                    @else
                                    <p class='text-danger'>{{'Rejected'}}</p>
                                    @endif
                                    @else
                                    <p class='text-danger'>{{'Cancelled'}}</p>
                                    @endif
                                    </div>
                                    </td>
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
