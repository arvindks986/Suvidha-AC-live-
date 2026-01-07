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
            <form name = "report" method="post"  action="{{url('/eci/partywise')}}"> 
                {{csrf_field()}}
            <div class="row">
                <div class="col-sm-10 row">
                        <label for="state" class="col-sm-4 col-form-label">Election Type</label>
                        <div class="col-sm-8 distt">
                            <select name="elect" id="state" class="form-control">
                                <option value="0">-- All Election --</option>                             
                                <option value="3">GEN Election</option>
                                <option value="4">BYE Election</option>
                            </select>
                        </div>
                </div>
                <div class="col-sm-1  row">
                        <input type="submit"  value="Submit" name="submit" class="btn btn-primary getdata">
                </div>
            </div>
            </form>
            <div class="row">
                 <div class="col-sm-12 text-right">
                <form name ="report" method="post"  action="{{url('/eci/partywise')}}">
                {{csrf_field()}}
                
                <input type="hidden" name="elect" class="form-control" value="{{$election}}">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
<!--                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">-->
                    </div>
                </form>
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
                                    <td><a href="{{url('eci/partywisedetails')}}/{{$election}}/{{$data->party_id}}/{{$data->permission_name}}/{{'6'}}">{{$data->Total}}</a></td>
                                    <td><a href="{{url('eci/partywisedetails')}}/{{$election}}/{{$data->party_id}}/{{$data->permission_name}}/{{'2'}}">{{$data->Accepted}}</a></td>
                                    <td><a href="{{url('eci/partywisedetails')}}/{{$election}}/{{$data->party_id}}/{{$data->permission_name}}/{{'3'}}">{{$data->Rejected}}</a></td>
                                    <td><a href="{{url('eci/partywisedetails')}}/{{$election}}/{{$data->party_id}}/{{$data->permission_name}}/{{'1'}}">{{$data->Inprogress}}</a></td>
                                    <td><a href="{{url('eci/partywisedetails')}}/{{$election}}/{{$data->party_id}}/{{$data->permission_name}}/{{'0'}}">{{$data->Pending}}</a></td>
                                    <td><a href="{{url('eci/partywisedetails')}}/{{$election}}/{{$data->party_id}}/{{$data->permission_name}}/{{'5'}}">{{$data->Cancel}}</a></td>
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

