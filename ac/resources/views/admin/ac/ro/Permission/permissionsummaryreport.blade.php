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

        <div class="container-fluid">
            <form name="report" method="post"  action="{{url('/roac/permissionsummaryreport')}}"> 
                {{csrf_field()}}
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </div>
            </form>
<div class="row">
                <div class="col-sm-12">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>District Name</th>
                                <th>AC Name</th>
                                <th>Pending_within_time</th>
                                <th>Pending_beyond_time</th>
                                <th>Accepted</th>
                                <th>Inprogress</th>
                                <th>Rejected</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(!empty($summarydata))
                                @foreach($summarydata as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->DIST_NAME}}</td>
                                    <td>{{$data->ac_name}}</td>
                                    <td>{{$data->Pending_within_time}}</td>
                                    <td>{{$data->Pending_beyond_time}}</td>
                                    <td>{{$data->Accepted}}</td>
                                    <td>{{$data->Inprogress}}</td>
                                    <td>{{$data->Rejected}}</td>
                                    <td>{{$data->total}}</td>
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
                        statehtml = statehtml + '<option value="0"> -- Select AC --</option> ';
                        jQuery.each(data, function(key, value) {
                            statehtml = statehtml + '<option value="' + value.AC_NO + '">' + value.AC_NAME + '</option>';
                            jQuery("select[name='ac']").html(statehtml);
                        });
                        var statehtml_end = '';
                        jQuery("select[name='ac']").append(statehtml_end);
                    } else {
                        //alert('test');
                        jQuery("select[name='ac']").html('<option value=""> -- Select AC --</option>');
                    }

                }
            });
        });
    });
</script>
@endsection
