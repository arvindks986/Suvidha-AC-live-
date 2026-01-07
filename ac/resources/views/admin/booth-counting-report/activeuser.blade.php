@extends('admin.layouts.ac.dashboard-theme')
@section('content')

<main role="main" class="inner cover mt-4">
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        <div class=" row align-items-center d-flex">
                            <div class="col">
                                <h4>{{$heading_title}}</h4>
                            </div>
                            <?php 
                                                            $request = \Request::all();
                                                            $request_array = [];
                                                            foreach ($request as $key => $value){
                                                                $request_array[] = $key.'='.$value;
                                                            }
                                                            $request_string = implode('&',$request_array);
                                                        ?>
                            <div class="mr-auto">
                                <button type="button" id="Cancel" class="btn btn-primary"
                                    onclick="window.history.back();">Back</button>
                            <a href="{{$pdf_url}}?{{$request_string}}" class="btn btn-primary">Export Pdf</a>
							<a href="{{$excel_url}}?{{$request_string}}" class="btn btn-primary">Export Excel</a>
                            </div>
                        </div>
                    </div>
                    <div class=" card-header">
                        <div class=" row">
                            <div class="col-md-4">
                                <label>State </label> 
                                <select name="state" id="state" class="form-control" onchange="filter()">
                                    <option value="">Select State</option>
                                    @foreach($filter_data[0]['results'] as $result)
                                    @if($st_code == ($result['id']))
                                    <option value="{{$result['id']}}" selected="selected">{{$result['name']}}</option>
                                    @else
                                    <option value="{{$result["id"]}}">{{ $result["name"] }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                            <label>District</label>
                            <select name="district" id="district" class="form-control" onchange="filter()">
                                <option value="">Select District</option>
                                @foreach($filter_data[1]['results'] as $result)
                                @if($dist_no == ($result['id']))
                                    <option value="{{$result['id']}}" selected="selected">{{$result['name']}}
                                </option>
                                @else
                                <option value="{{$result["id"]}}">{{ $result["name"] }}</option>
                                @endif
                                @endforeach
                            </select>
                            </div>

                            <div class="col-md-4">
                                <label>AC</label>
                                <select name="ac" id="ac" class="form-control" onchange="filter()">
                                    <option value="">Select AC</option>
                                    @foreach($filter_data[2]['results'] as $result)
                                    @if($ac_no == ($result['id']))
                                        <option value="{{$result['id']}}" selected="selected">{{$result['name']}}
                                    </option>
                                    @else
                                    <option value="{{$result["id"]}}">{{ $result["name"] }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Serial No</th>
                                    <th>State Name</th>
                                    <th>District Name</th>
                                    <th>AC Name</th>
                                    <th>Name</th>
                                    <th>User Name</th>
                                    <th>Designation</th>
                                    <th>Active Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 1; @endphp
                                @if(!is_null($list_all))
                                @foreach ($list_all as $one_list_of_count)
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td>{{ getstatebystatecode($one_list_of_count->st_code)->ST_NAME }}</td>
                                    <td>{{ getdistrictbydistrictno($one_list_of_count->st_code, $one_list_of_count->dist_no)->DIST_NAME }}</td>
                                    <td>{{ getacbyacno($one_list_of_count->st_code, $one_list_of_count->ac_no)->AC_NAME }}</td>
                                    <td>{{$one_list_of_count->name }}</td>
                                    <td>{{$one_list_of_count->officername }}</td>
                                    <td>{{$one_list_of_count->designation }}</td>
                                    @if($one_list_of_count->is_active == 1)
                                    <td>Active</td>
                                    @else
                                    <td>Inactive</td>
                                    @endif
                                </tr>
                                @php $count++; @endphp
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center" colspan="7">No Data Found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script type="text/javascript">
    function filter(){
    var url = "<?php echo url()->current(); ?>";
    var query = '';
    if(jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined'){
    query += "&st_code="+jQuery("#state").val();
    }
    if(jQuery("#district").val() != '' && jQuery("#district").val() != 'undefined'){
    query += '&dist_no='+jQuery("#district").val();
    }
    if(jQuery("#ac").val() != '' && jQuery("#ac").val() != 'undefined'){
    query += '&ac_no='+jQuery("#ac").val();
    }
    window.location.href = url+'?'+query.substring(1);
    }
</script>
@endsection()