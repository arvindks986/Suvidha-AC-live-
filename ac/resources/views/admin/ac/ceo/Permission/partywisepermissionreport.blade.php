@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Partywise Permission Report</h5>
                </div>
            </div>
            <!--            new table-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="float-right mt-0">
                        <a href="{{url('/acceo/partywise')}}" class=""><i class="fa fa-file-excel-o text-success"></i> ExportExcel</a>
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
                                <th>Party Name</th>
                                <th>Permission Name</th>
                                <th>Total Request</th>
                                <th>Accepted</th>
                                <th>Rejected</th>
                                <th>Inprogess</th>
                                <th>Pending</th>
                                <th>Cancel</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($partyreport))
                                @foreach($partyreport as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->PARTYNAME}}</td>
                                    <td>{{$data->permission_name}}</td>
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

