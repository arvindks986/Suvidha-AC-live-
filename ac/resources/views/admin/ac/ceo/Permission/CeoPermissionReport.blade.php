@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')

<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
     <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Timewise Permission Report</h5>
                </div>
            </div>
    <section id="details">

        <div class="container-fluid">
            <form name ="report" method="post"  action="{{url('/acceo/reporttimesview')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-3  row">
                        <label for="state" class="col-sm-4 col-form-label">Select District</label>
                        <div class="col-sm-8 distt">
                            <select name="dist" id="dist" class="form-control">
                                <option value="0">-- Select District --</option>
                                 <option value="all">Select All</option>
                                @foreach($distvalue as $dist)
                                <option value="{{$dist->DIST_NO }}" {{ (collect(old('dist'))->contains($dist->DIST_NO)) ? 'selected':'' }}> 
                                    {{$dist->DIST_NAME }}
                                </option>
                                @endforeach 
                            </select>
                            <span class="text-danger">{{ $errors->error->first('dist') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-3  row">
                        <label for="ac" class="col-sm-4 col-form-label">Select AC</label>
                        <div class="col-sm-8 distt">
                            <select name="ac" id="ac" class="form-control">
                                <option value="0">-- Select AC --</option>
                                <option value="all">Select All</option>
                            </select>
                            <span class="text-danger">{{ $errors->error->first('ac') }}</span>
                        </div>
                    </div>

                    <div class="col-sm-3  row">
                        <label for="time" class="col-sm-2 col-form-label">Select Time</label>
                        <div class="col-sm-8 distt">
                                <select name="time" id="time" class="form-control">
                                    <option value="0">-- Select Time --</option>
                                    <option value="1" {{ (collect(old('time'))->contains(1)) ? 'selected':'' }}>Up to 1 hours</option>
                                    <option value="12" {{ (collect(old('time'))->contains(12)) ? 'selected':'' }}>Up to 12 hours</option>
                                    <option value="24" {{ (collect(old('time'))->contains(24)) ? 'selected':'' }}>Up to 24 hours</option>
                                </select>
                                <span class="text-danger">{{ $errors->error->first('time') }}</span>
                        </div>
                    </div>


                    <div class="col-sm-1  row">
                        <input type="submit"  value="Submit" name="submit" class="btn btn-primary getdata">
                    </div>
                </div>
            </form>
            <form name ="report" method="post"  action="{{url('/acceo/reporttimes')}}">
                {{csrf_field()}}
            <input type="hidden" name="time" value="{{$time}}" class="form-control" >
             <input type="hidden" name="dist" class="form-control" value="{{$dist_no}}">
                <input type="hidden" name="ac" class="form-control" value="{{$ac_no}}">
            <div class="row">
                <div class="col-sm-12">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </div>
            </div>
            </form>
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Reference id</th>
                                <th>Username</th>
                                <th>Permission name</th>
                                <th>Permission Mode</th>
                                <th>Applicant Type</th>
                                <th>DateTime of Submission</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($report))
                                @foreach($report as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->reference_id}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->pname}}</td>
                                    @if($data->permission_mode == 0)
                                    <td>{{"Offline"}}</td>
                                    @else
                                    <td>{{"Online"}}</td>
                                    @endif
                                    <td>{{ $data->role_name}}</td>
                                    <td>{{ $data->added_at}}</td>
                                     @if($data->approved_status == 0)
                                    <td>{{"Pending"}}</td>
                                    @else
                                     <td>{{"Inprogress"}}</td>
                                    @endif
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
<script>
    $(document).ready(function() {
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');

        jQuery("select[name='dist']").change(function()
        {
            var dist = jQuery(this).val();
            jQuery.ajax({
                url: base_url + '/acceo/getAllAC',
                type: 'POST',
                data: {_token: token, dist: dist},
                success: function(data) {
                    //alert(data);
                    if (data != '') {
                        var distselect = jQuery('form select[name=ac]');
                        distselect.empty();
                        var statehtml = '';
                        statehtml = statehtml + '<option value="0"> -- Select AC --</option><option value="all">Select All</option> ';
                        jQuery.each(data, function(key, value) {
                            statehtml = statehtml + '<option value="' + value.AC_NO + '">' + value.AC_NAME + '</option>';
                            jQuery("select[name='ac']").html(statehtml);
                        });
                        var statehtml_end = '';
                        jQuery("select[name='ac']").append(statehtml_end);
                    } else {
                        //alert('test');
                        jQuery("select[name='ac']").html('<option value=""> -- Select AC --</option><option value="all">Select All</option>');
                    }

                }
            });
        });
    });
</script>
@endsection
