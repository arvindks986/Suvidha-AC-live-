@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Dashboard')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
  @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
  <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-3 pl-0">
              <!-- Income-->
              <div class="card income">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success "><b>Total</b> &nbsp; <div class="btn-group float-right">
					<a type="button" href="#" class="btn btn-sm btn-primary count" data-statusid="22">@if(!empty($allrecord)) {{$allrecord[0]->Total}} @else {{'0'}} @endif</a>
<!--                  <a type="button" href="addps.html" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>
              </div>
            </div>
          <div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Pending</b> &nbsp; <div class="btn-group float-right">
					<a type="button" href="#" class="btn btn-sm btn-primary count" data-statusid="01">@if(!empty($allrecord)) {{$allrecord[0]->Pending }} @else {{'0'}} @endif</a>
<!--                  <a href="addpermission.html" type="button" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>
              </div>
            </div> 
			<div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success"><b>Accepted</b> &nbsp; <div class="btn-group float-right">
					<a type="button" href="#" class="btn btn-sm btn-primary count" data-statusid="2">@if(!empty($allrecord)) {{$allrecord[0]->Accepted}} @else {{'0'}} @endif</a>
<!--                  <button type="button" class="btn btn-sm btn-primary">Add</button>-->
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3 pr-0">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-danger"><b>Rejected</b> &nbsp; <div class="btn-group float-right">
					<a type="button" href="#" class="btn btn-sm btn-danger count" data-statusid="3">@if(!empty($allrecord)) {{$allrecord[0]->Rejected}} @else {{'0'}} @endif</a>
<!--                  <button type="button" class="btn btn-sm btn-danger">Add</button>-->
				  </div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>
    <section id="details" class="dashboard-header section-padding" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12" id="reportdata">
                     <table id="list-table" class='table table-bordered' style='width:100%'><thead><tr><th>Reference No.</th><th>User Name</th><th>User Type</th><th>Permission Type</th><th>Permission Mode</th><th>DateTime of Submission</th><th>Status</th></tr></thead>
					  <tbody>
                    @if(!empty($totalPermissionReport))
                    @foreach($totalPermissionReport as $rdata)
                       
                            <tr><td><a href='{{url('/roac/permission/permissiondetailsview')}}/{{$rdata->permission_id}}/{{$rdata->location_id}}/{{$rdata->approved_status}}' >{{$rdata->permission_id}}</a></td>
                                <td>{{$rdata->name}}</td><td>{{$rdata->role_name}}</td>
                                <td><p>{{$rdata->pname}}</p></td>
                                @if($rdata->permission_mode == 1)
                                         <td>{{'Online'}}</td>
                                         @else
                                         <td>{{'Offline'}}</td>
                                         @endif
                                <td>{{$rdata->added_at}}</td>
                                <td>
                                    @if($rdata->approved_status == 0)
                                    <p class='text-info'>{{'Pending'}}</p>
                                    @elseif($rdata->approved_status == 1)
                                    <p class='lightgreen'>{{'Inprogress'}}</p>
                                    @elseif($rdata->approved_status == 2)
                                    <p class='text-success'>{{'Accepted'}}</p>
                                    @else
                                    <p class='text-danger'>{{'Rejected'}}</p>
                                    @endif
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
    <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
</main>
@endsection
@section('script')
<script>$(document).ready(function() {
     $('#list-table').DataTable();
    var base_url = $("#base_url").val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $('.count').on('click',function(){
        var statusid=$(this).attr('data-statusid');
//        alert(statusid);exit;
        $.ajax({
            url:base_url+'/roac/permission/permissioncountdetails',
            type: 'POST',
            data: {_token: token, statusid:statusid},
            success: function (data)
            {
//                alert(data);
                  var str = '';
                  var cnt = data.length;
                  var referenceid;
                  if(data != '')
                  {
                      str +="<table id='list-table' class='table table-bordered' style='width:100%'><thead><tr><th>Permission ID</th><th>User Name</th><th>User Type</th><th>Permission Type</th><th>Permission Mode</th><th>DateTime of Submission</th><th>Status</th></tr></thead><tbody>";
                      $('#details').css('display','');
                      for (var i = 0; i < cnt; i++) {
                       var id=<?php if(!empty($ele_details))echo $ele_details->ELECTION_ID.$ele_details->CONST_TYPE; ?> + data[i]['id'];
                       var name=data[i]['name'];
                       var role_name=data[i]['role_name'];
                       var p_name=data[i]['pname'];
                       var added_at=data[i]['added_at'];
                       var location_id=data[i]['location_id'];
                       var astatus=data[i]['approved_status'];
                       var pmode=data[i]['permission_mode']
                        if(data[i]['reference_id'] != '' && data[i]['reference_id'] != null)
                       {
                           referenceid = data[i]['reference_id'];
                       }
                       else
                       {
                           referenceid = data[i]['id'];
                       }
                       if(pmode == 1)
                       {
                           var pstatus = "<p class='online'>Online</p>";
                       }
                       else
                       {
                           var pstatus = "<p class='offline'>Offline</p>";
                       }
                       if(data[i]['approved_status'] == 0)
                       {
                           var status = "<p class='text-info'>Pending</p>";
                       }
                       else if(data[i]['approved_status'] == 1)
						   {
                           var status = "<p class='lightgreen'>Inprogress</p>";
                       }
                       else if(data[i]['approved_status'] == 2)
                       {
                           var status = "<p class='text-success'>Accepted</p>";
                       }
                       else if(data[i]['approved_status'] == 3)
                       {
                           var status = "<p class='text-danger'>Rejected</p>";
                       }
						str +="<tr><td><a href='{{url('/roac/permission/permissiondetailsview')}}/"+id+"/"+location_id+"/"+astatus+"' >"+referenceid+"</a></td><td>"+name+"</td><td>"+role_name+"</td><td>"+p_name+"</td><td>"+pstatus+"</td><td>"+added_at+"</td><td>"+status+"</td></tr>";
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
//function test(id)
//    {
//        var base_url = $("#base_url").val();
//        var token = $('meta[name="csrf-token"]').attr('content');
//        $.ajax({
//            url:base_url+'/roac/permission/permissiondetailsview',
//            type: 'POST',
//            data: {_token: token, id:id},
//            success: function (data)
//            {
//               
//            }
//        });
//    }
</script>
    @endsection