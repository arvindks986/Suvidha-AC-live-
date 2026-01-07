@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Permission Raw Report</h5>
                </div>
            </div>
            <!--            new table-->
            <div class="row">
                <div class="col-sm-12">

                    <div class="float-right mt-0">
                        <a href="{{url('/acceo/ceoreport')}}" class=""><i class="fa fa-file-excel-o text-success"></i> ExportExcel</a>
<!--                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">-->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 mt-2 table-responsive">

                    <table id="list-table" class="table table-striped table-bordered table-hover" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Ref no</th>
                                <th>STATE NAME</th>
                                <th>DISTRICT NAME</th>
                                <th>AC NAME</th>
                                <th>User Name</th>
                                <th>Permission Name</th>
                                <th>User Type</th>
                                <th>Party name</th>
                                <th>Date of Submittion</th>
                                <th>Action Date</th>
                                <th>Event Start Date</th>
                                <th>Event End Date</th>
                                <th>Permission Mode</th>
                                <th>Previous Status</th>
                                <th>Current Status</th>
                                <th>Comment</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($rawreport))
                                @foreach($rawreport as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->reference_id}}</td>
                                    <td>{{$data->ST_NAME}}</td>
                                    <td>{{$data->DIST_NAME}}</td>
                                    <td>{{$data->AC_NAME}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->pname}}</td>
                                    <td>{{$data->role_name}}</td>
                                    <td>{{$data->PARTYNAME}}</td>
                                    <td>{{$data->added_at}}</td>
                                    <td>{{$data->updated_at}}</td>
                                    <td>{{$data->date_time_start}}</td>
                                    <td>{{$data->date_time_end}}</td>
                                    <td>{{$data->pmode}}</td>
                                    <td>{{$data->status}}</td>
                                    <td>{{$data->cancelstatus}}</td>
                                    <td>{{$data->comment}}</td>
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

