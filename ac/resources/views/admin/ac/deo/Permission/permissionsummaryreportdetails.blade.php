@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">

        <div class="container-fluid">
             <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Permission Summary Report Details</h5>
                </div>
            </div>
            <!--            new table-->
            <form name="report" method="post"  action="{{url('/acdeo/permissionsummaryreport')}}"> 
                {{csrf_field()}}
                <div class="row">
                    <div class="col-sm-5  row">
                        <label for="state" class="col-sm-4 col-form-label">Select District</label>
                        <div class="col-sm-8 distt">
                            <select name="dist" id="dist" class="form-control">
                                <option value="{{$distvalue->DIST_NO}}" selected="">{{$distvalue->DIST_NAME}}</option>
                            </select>
                            <span class="text-danger">{{ $errors->error->first('dist') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-5  row">
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
                    <div class="col-sm-2  row">
                        <input type="submit"  value="Submit" name="excel" class="btn btn-primary getdata">
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
                                @if(!empty($countdetails))
                                @foreach($countdetails as $key => $data)
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

