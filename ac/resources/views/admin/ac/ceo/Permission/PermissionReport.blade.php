@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
  <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-3 pl-0">
              <!-- Income-->
              <div class="card income">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success "><b>Total</b> &nbsp; <div class="btn-group float-right">
					<a type="button" href="#" class="btn btn-sm btn-outline-primary count" data-statusid="22">@if(!empty($allrecord)) {{$allrecord[0]->total}} @else {{'0'}} @endif</a>
<!--                  <a type="button" href="addps.html" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>
              </div>
            </div>
          <div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Pending</b> &nbsp; <div class="btn-group float-right mt-2">
					<a type="button" href="#" class="btn btn-sm btn-outline-primary count" data-statusid="01">@if(!empty($allrecord)) {{$allrecord[0]->Pending}} @else {{'0'}} @endif</a>
<!--                  <a href="addpermission.html" type="button" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>
              </div>
            </div> 
			<div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Accepted</b> &nbsp; <div class="btn-group float-right mt-2">
					<a type="button" href="#" class="btn btn-sm btn-outline-primary count" data-statusid="2">@if(!empty($allrecord)) {{$allrecord[0]->Accepted}} @else {{'0'}} @endif</a>
<!--                  <button type="button" class="btn btn-sm btn-primary">Add</button>-->
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3 pr-0">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-warning"><b>Rejected</b> &nbsp; <div class="btn-group float-right mt-2">
					<a type="button" href="#" class="btn btn-sm btn-outline-danger count" data-statusid="3">@if(!empty($allrecord)) {{$allrecord[0]->Rejected}} @else {{'0'}} @endif</a>
<!--                  <button type="button" class="btn btn-sm btn-danger">Add</button>-->
				  </div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>
    <section id="details" style="display: none">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12" id="reportdata">

                </div>
        </div>
        </div>
    </section>
    <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
</main>
@endsection
@section('script')
<script>$(document).ready(function() {
    var base_url = $("#base_url").val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $('.count').on('click',function(){
        var statusid=$(this).attr('data-statusid');
        $.ajax({
            url:base_url+'/acceo/permissioncountdetails',
            type: 'POST',
            data: {_token: token, statusid:statusid},
            success: function (data)
            {
//                alert(data);exit;
                  var str = '';
                  var cnt = data.length;
                  var referenceid;
                  if(data != '')
                  {
                      str +="<table id='list-table' class='table' style='width:100%'><thead><tr><th>Reference ID</th><th>User Name</th><th>User Type</th><th>Permission Type</th><th>Date of Submission</th><th>Permission Mode</th><th>Status</th></tr></thead><tbody>";
                      $('#details').css('display','');
                      for (var i = 0; i < cnt; i++) {
                       var id=data[i]['id'];
                       var name=data[i]['name'];
                       var role_name=data[i]['role_name'];
                       var p_name=data[i]['pname'];
                       var added_at=data[i]['added_at'];
                       var location_id=data[i]['location_id'];
                       var astatus=data[i]['approved_status'];
                       var cancel_status=data[i]['cancel_status'];
                       if(data[i]['reference_id'] != '' && data[i]['reference_id'] != null)
                       {
                           referenceid = data[i]['reference_id'];
                       }
                       else
                       {
                           referenceid = data[i]['id'];
                       }
                       if(data[i]['permission_mode'] == 0)
                       {
                           var pmode = 'Offline';
                       }
                       else if(data[i]['permission_mode'] == 1)
                       {
                           var pmode = 'Online';
                       }
                       if(cancel_status == 0)
                       {
                       if(data[i]['approved_status'] == 0)
                       {
                           var status = 'Pending';
                       }
                       else if(data[i]['approved_status'] == 1)
                       {
                           var status = 'Inprogress';
                       }
                       else if(data[i]['approved_status'] == 2)
                       {
                           var status = 'Accepted';
                       }
                       else if(data[i]['approved_status'] == 3)
                       {
                           var status = 'Rejected';
                       }
                       }
                        else
                       {
                       var status = "<p class='text-danger'>Cancelled</p>";
                       }
                      str +="<tr><td><a href='{{url('/acceo/permissiondetailsview')}}/"+id+"/"+location_id+"/"+astatus+"' >"+referenceid+"</a></td><td>"+name+"</td><td>"+role_name+"</td><td>"+p_name+"</td><td>"+added_at+"</td><td>"+pmode+"</td><td>"+status+"</td></tr>";
                  }
                  str +="</tbody></table>";
                  $('#reportdata').html(str);
                  $('#list-table').DataTable();
                  }
                  else
                  {
                        $('#reportdata').html("No data avaiable");
                  }
            }
            
        });
    });
});

</script>
    @endsection