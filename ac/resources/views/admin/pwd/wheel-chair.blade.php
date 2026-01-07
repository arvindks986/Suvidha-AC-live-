@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Pwd Wheel Chair Requests')
@section('content')


@if($errors->any())
<div class="alert alert-info">{{$errors->first()}}</div>
@endif

@if (session('error'))
<div class="alert alert-info">{{ session('error') }}</div>
@endif

<style type="text/css">
    .loader {
        position: fixed;
        left: 50%;
        right: 50%;
        border: 16px solid #f3f3f3;
        /* Light grey */
        border-top: 16px solid #3498db;
        /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        z-index: 99999;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7 pull-left">
                <h4>Pwd Wheel Chair Requests</h4>
            </div>

            <div class="col-md-5  pull-right text-right">



            </div>

        </div>
    </div>
</section>

<div class="container-fluid">
    <!-- Start parent-wrap div -->
    <div class="parent-wrap">
        <!-- Start child-area Div -->
        <div class="child-area">
            <div class="page-contant">
                <div class="random-area">
                    @if (session('success_mes'))
                    <div class="alert alert-success"> {{session('success_mes') }}</div>
                    @endif
                    @if (session('error_mes'))
                    <div class="alert alert-danger"> {{session('error_mes') }}</div>
                    @endif
                    <br>



                    <div class="table-responsive">

                        <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
                            <thead>

                                <tr>
                                    <th colspan="{{(($user_data['role_id'] != '19') ? '12' : '10')}}" class="text-center">Pwd Wheel Chair Requests</th>
                                </tr>
                                <tr>
                                    <th>S.no</th>
                                    @if($user_data['role_id'] != '19')
                                    <th>AC Name</th>
                                    <th>AC No</th>
                                    @endif
                                    <th>Reference Id</th>
                                    <th>PS no</th>
                                    <th>PS Name</th>
                                    <th>Epic No</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Mobile</th>
                                    <th>Requested at</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $key =>$request)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    @if($user_data['role_id'] != '19')
                                    <td>{{$request->ac->AC_NAME}}</td>
                                    <td>{{$request->ac_no}}</td>
                                    @endif
                                    <td>{{$request->referenceid}}</td>
                                    <td>{{$request->ps_no}}</td>
                                    <td>{{$request->ps_name}}</td>
                                    <td>{{$request->epic_no}}</td>
                                    <td>{{$request->name}}</td>
                                    <td>{{$request->age}}</td>
                                    <td>{{$request->mobile}}</td>
                                    <td>{{$request->created_at}}</td>
                                    <td>
                                        @if($user_data['role_id'] == '19')
                                        <form action="{{url('roac/pwd/update-remarks')}}" method="post">
                                            <input type="hidden" name="referenceid" value="{{$request->referenceid}}">
                                            <input type="hidden" name="for" value="WheelChair">
                                            {{@csrf_field()}}
                                            <textarea class="form-control" name="remarks" id="" cols="30" rows="5">{{$request->remarks}}</textarea><br />
                                            <button class="btn btn-info float-right">Update</button>
                                        </form>
                                        @else
                                        {{$request->remarks ?? 'N/A'}}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @if(count($requests) == 0)
                                <tr>
                                    <td colspan="9">No Data Found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>

                    </div><!-- End Of  table responsive -->
                </div><!-- End Of intra-table Div -->


            </div><!-- End Of random-area Div -->

        </div><!-- End OF page-contant Div -->
    </div>
</div><!-- End Of parent-wrap Div -->
</div>



@endsection

@section('script')
<script>
    $(function() {
        $('#data_table_table').DataTable();
    })
</script>
@endsection