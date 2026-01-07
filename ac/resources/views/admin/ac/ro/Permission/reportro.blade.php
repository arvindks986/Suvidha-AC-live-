@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Datewise Permission Report</h5>
                </div>
            </div>
            <form name ="report" method="post"  action="{{url('/roac/reportdatesview')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-4  row">
                        <label for="state" class="col-sm-4 col-form-label">AC</label>
                        <div class="col-sm-8 distt">
                            <select name="ac" id="ac" class="form-control">
                                @foreach($distvalue as $dist)
                                <option value="{{$dist->AC_NO }}"> 
                                    {{$dist->AC_NAME }}
                                </option>
                                @endforeach 
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-5  row">
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
            <form name ="report" method="post"  action="{{url('/roac/reportdates')}}">
                {{csrf_field()}}
            @if(!empty($ac_no) && !empty($datefilter))
            <input type="hidden" name="datefilter" value="{{$datefilter}}" class="form-control" >
                <input type="hidden" name="ac" class="form-control" value="{{$ac_no}}">
            <div class="row">
                <div class="col-sm-12">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </div>
            </div>
            @else
            <input type="hidden" name="datefilter" value="" class="form-control" >
            <input type="hidden" name="ac" value="0" class="form-control" >
            <div class="row">
                <div class="col-sm-12">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </div>
            </div>
            @endif
            </form>
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>AC Name</th>
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
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->AC_NAME}}</td>
                                    <td>{{$data->total_request}}</td>
                                    <td>{{$data->approved}}</td>
                                    <td>{{$data->rejected}}</td>
                                    <td>{{$data->inprogress}}</td>
                                    <td>{{$data->pending}}</td>
                                    <td>{{$data->Cancel}}</td>
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
