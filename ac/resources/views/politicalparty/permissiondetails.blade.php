<?php // echo '<pre/>';
//print_r($user_data);
//echo $user_data[0]['name'];
?>
<div class="card">
<div class="card-header">
  <h4>Permission Details</h4>
</div>
@if(!empty($user_data))
@foreach($user_data as $data)
<div class="card-body">
  <div class="table-responsive">
    <table class="table table-striped table-sm" border="1">
        <tr><th>PermissionId</th><td align="left">{{$data['user_id']}}</td></tr>
        <tr><th>Name</th><td>{{$data['name']}}</td></tr>
        <tr><th>Address</th><td>{{$data['address']}}</td></tr>
        <tr><th>Mobile No</th><td>{{$data['mobileno']}}</td></tr>
        <tr><th>Epic No</th><td>{{$data['epic_no']}}</td></tr>
        <tr><th>Permission Type</th><td>{{'p_type'}}</td></tr>
        <tr><th>State</th><td>{{$data['state_id']}}</td></tr>
        <tr><th>District</th><td>{{$data['district_id']}}</td></tr>
        <tr><th>AC</th><td>{{$data['ac_id']}}</td></tr>
        <tr><th>Location</th><td>{{'location'}}</td></tr>
        <tr><th>Date & Timing</th><td>{{'datetime'}}</td></tr>
    </table>
      <div class="col-s text-center">
        <button class="btn btn-success" value="0">Accept</button>&nbsp;<button class="btn btn-danger" value="1">Reject</button>&nbsp;
        <button class="btn btn-primary" value="3">Download</button>
      </div>
  </div>
</div>
@endforeach
@endif
</div>
<script>
    $(function(){
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');
        $('.btn').click(function(){
            var action_id=$(this).val();
            $.ajax({
                url:base_url+'/updateaction',
                type: 'POST',
                data:{ _token:token,action_id:action_id},
                success:function(response)
                {
                    alert(response);
                    window.location=base_url+'/dashboard';
                }
            });
        });
    });
</script>