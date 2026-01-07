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
                <div class="col-sm-12 mt-2 table-responsive">
                    <table id="list-table" class="table table-striped table-bordered table-hover" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>State Name</th>
                                <th>District Name</th>
                                <th>AC Name</th>
                                <th>Reference ID.</th>
                                <th>Partyname</th>
                                <th>Applicant Name</th>
                                <th>Applicant Type</th>
                                <th>Permission Type</th>
                                <th>Permission Mode</th>
                                <th>DateTime of Submission</th>
                                <th>Status</th> 
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($permissionwisereport))
                                @foreach($permissionwisereport as $key => $data)
                                <tr>
                                   <td>{{$key + 1}}</td>
                                    <td>{{$data->ST_NAME}}</td>
                                    <td>{{$data->DIST_NAME}}</td>
                                    <td>{{$data->AC_NAME}}</td>
                                    <td>{{$data->reference_id}}</td>
                                    <td>{{$data->PARTYNAME}}</td>
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

