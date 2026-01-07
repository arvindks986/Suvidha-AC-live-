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
					<a type="button" href="#" class="btn btn-sm btn-outline-primary count" data-statusid="22">@if(!empty($allrecord)) {{$allrecord[0]->Total}} @else {{'0'}} @endif</a>
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
    <section id="details" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label"><b>SELECT AC</b> </label>
                        <div class="col pr-0">
<!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                            <select name="pname" class="form-control" id="selectac">
                                <option value="0">Select AC</option>
                                @if(!empty($getallac))
                                @foreach($getallac as $pdata)
                                <option value="{{$pdata->AC_NO}}" {{ (collect(old('pname'))->contains($pdata->AC_NO)) ? 'selected':'' }}>{{$pdata->AC_NAME}}</option>
                                @endforeach
                                @endif
                            </select>
                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                        </div>
                                        
                    </div>
                </div>
                <div class="col-md-12" id="reportdata">
                     <table id="list-table" class='table' style='width:100%'><thead><tr><th>Permission ID</th><th>User Name</th><th>User Type</th><th>Permission Type</th><th>Permission Mode</th><th>DateTime of Submission</th><th>Status</th></tr></thead>
                    @if(!empty($totalPermissionReport))
                    @foreach($totalPermissionReport as $rdata)
                        <tbody>
                            <tr><tr><td><a href='{{url('/acdeo/permissiondetailsview')}}/{{$rdata->permission_id}}/{{$rdata->location_id}}/{{$rdata->approved_status}}' >{{$rdata->permission_id}}</a></td>
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
                                    {{'Pending'}}
                                    @elseif($rdata->approved_status == 1)
                                    {{'Inprogress'}}
                                    @elseif($rdata->approved_status == 2)
                                    {{'Accepted'}}
                                    @else
                                    {{'Rejected'}}
                                    @endif
                                </td>
                            </tr>
                      
                        </tbody>
                        @endforeach
                    @endif
                    </table>
                </div>
        </div>
        </div>
    </section>
    <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
</main>
@endsection
@section('script')
<script>
    $(document).ready(function() {
     $('#list-table').DataTable();
    var base_url = $("#base_url").val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $('.count').on('click',function(){
        var statusid=$(this).attr('data-statusid');
//        alert(statusid);exit;
        $.ajax({
            url:base_url+'/acdeo/permissioncountdetails',
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
                      str +="<table id='list-table' class='table' style='width:100%'><thead><tr><th>Permission ID</th><th>User Name</th><th>User Type</th><th>Permission Type</th><th>Permission Mode</th><th>DateTime of Submission</th><th>Status</th></tr></thead><tbody>";
                      $('#details').css('display','');
                      for (var i = 0; i < cnt; i++) {
                       var id='<?php echo $ele_details[0]->ELECTION_ID.$ele_details[0]->CONST_TYPE ;?>'+data[i]['id'];
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
                           var pstatus = 'Online';
                       }
                       else
                       {
                           var pstatus = 'Offline';
                       }
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
//                      str +="<tr><td><button id='"+id+"' onclick='test(id)'>"+id+"</button></td><td>"+name+"</td><td>"+role_name+"</td><td>"+p_name+"</td><td>"+added_at+"</td><td>"+status+"</td></tr>";
                        str +="<tr><td><a href='{{url('/acdeo/permissiondetailsview')}}/"+id+"/"+location_id+"/"+astatus+"' >"+referenceid+"</a></td><td>"+name+"</td><td>"+role_name+"</td><td>"+p_name+"</td><td>"+pstatus+"</td><td>"+added_at+"</td><td>"+status+"</td></tr>";
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
    
     $('select#selectac').on('change',function () {
        var acid = $(this).val();
         $.ajax({
            url:base_url+'/acdeo/getallacpermission',
            type: 'POST',
            data: {_token: token, acid:acid},
            success: function (data)
            {
//                alert(data);die;
                var str = '';
                var cnt = data.length;
                  if(data != '')
                  {
                      
                      str +="<table id='list-table' class='table' style='width:100%'><thead><tr><th>Permission ID</th><th>User Name</th><th>User Type</th><th>Permission Type</th><th>Permission Mode</th><th>Date of Submission</th><th>Status</th></tr></thead><tbody>";
                      $('#details').css('display','');
                      for (var i = 0; i < cnt; i++) {
                       var id=data[i]['id'];
                       var name=data[i]['name'];
                       var role_name=data[i]['role_name'];
                       var p_name=data[i]['pname'];
                       var added_at=data[i]['added_at'];
                       var location_id=data[i]['location_id'];
                       var astatus=data[i]['approved_status'];
                       var pmode=data[i]['permission_mode']
                       if(pmode == 1)
                       {
                           var pstatus = 'Online';
                       }
                       else
                       {
                           var pstatus = 'Offline';
                       }
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
//                      str +="<tr><td><button id='"+id+"' onclick='test(id)'>"+id+"</button></td><td>"+name+"</td><td>"+role_name+"</td><td>"+p_name+"</td><td>"+added_at+"</td><td>"+status+"</td></tr>";
                        str +="<tr><td><a href='{{url('/acdeo/permissiondetailsview')}}/"+id+"/"+location_id+"/"+astatus+"' >"+id+"</a></td><td>"+name+"</td><td>"+role_name+"</td><td>"+p_name+"</td><td>"+pstatus+"</td><td>"+added_at+"</td><td>"+status+"</td></tr>";
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