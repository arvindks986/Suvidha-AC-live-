@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
@include('admin.ac.deo.Permission.permission-master-menu')
<section>
    @if (Session::has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h2>Nodal List</h2>
                    </div>
                    <div class="card-body tabular-pane">
                        <table id="list-table1" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Authority Type Name</th>
                                    <th>Department</th>
                                    <th>Address</th>
                                    <th>Mobile No.</th>
                                    <th>Incharge Name</th>
                                    <th>Active/InActive</th>
                                    <th>Edit/Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php $i='1'; ?>
                                @if(!empty($getAllACAuthorityData1))
                                @foreach($getAllACAuthorityData1 as $data)
                                <tr>
                                    <td>{{$i}}</td>
                                    @if(!empty($data->auth_type_name1))
                                    <td>{{$data->auth_type_name1}}</td>
                                    @else
                                    @if(!empty($data->auth_type_name2))
                                    <td>{{$data->auth_type_name2}}</td>
                                    @endif
                                    @endif
                                    <td>{{$data->department}}</td>
                                    <td>{{$data->address}}</td>
                                    <td>{{$data->mobile}}</td>
                                    <td>{{$data->name}} </td>
                                    @if($data->is_active == 0)
                                    <td id="setStatus"><span class="btn btn-danger setStatus"
                                            id="{{'0'}}{{'#'}}{{$data->nodal_id}}{{'#'}}{{$data->auth_type_id}}">{{'InActive'}}
                                        </span></td>
                                    @else
                                    <td id="setStatus"><span class="btn btn-success setStatus"
                                            id="{{'1'}}{{'#'}}{{$data->nodal_id}}{{'#'}}{{$data->auth_type_id}}">{{'Active'}}
                                        </span></td>
                                    @endif
                                    <td id="edit"><a
                                            href="{{url('/acdeo/editauthority')}}/{{Crypt::encryptString($data->nodal_id)}}{{'&'}}{{Crypt::encryptString($data->auth_type_id)}}"><span
                                                class=" btn btn-success float-right">Edit</span></a></td>
                                </tr>
                                 <?php $i++; ?>
                                @endforeach
                                @endif
                            </tbody>

                        </table>
                        <!-- <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label"><b>SELECT PC</b> </label>
                                <div class="col pr-0">
                                                  <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                    <span class="text-danger">{{ $errors->error->first('pname') }}</span> 
                                    <select name="pname" class="form-control" id="selectpc">
                                        <option value="0">Select PC</option>
                                        @if(!empty($getAllPC))
                                        @foreach($getAllPC as $pdata)
                                        <option value="{{$pdata->PC_NO}}" {{ (collect(old('pname'))->contains($pdata->PC_NO)) ? 'selected':'' }}>{{$pdata->PC_NAME_EN}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                </div>

                            </div>
                        </div> -->


                        <div class="col-md-12">
                            <div class="form-group row" id="allac">

                            </div>
                        </div>
                        <hr />
                        <div id="viewps">

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</section>


@endsection
@section('script')
<script type="text/javascript">
$(function() {
    var base_url = $("#base_url").val();
    var token = $('meta[name="csrf-token"]').attr('content');
    /* $('select#selectpc').on('change', function () {
            var pc_id = $(this).val();
            $.ajax({
                url: base_url + '/acdeo/getAllAC',
                type: 'POST',
                data: {_token: token, pc_id:pc_id},
                success: function (response)
                {
//                    alert(response);exit;
                    var achtml = '';
                    achtml = achtml+ "<label class='col-sm-2 form-control-label'><b>SELECT AC</b> </label><div class='col pr-0'><select name='acno' class='form-control' id='ac'><option value='0'>Select AC</option>";
                    var cnt = response.length;
                    if(cnt != 0 )
                    {
                    for (var i = 0; i < cnt; i++) {
                        var ac_no=response[i]['AC_NO'];
                        var ac_name=response[i]['AC_NAME'];
                       achtml = achtml + "<option value="+ac_no+">"+ac_name+"</option>";
                   }
                   achtml = achtml+"</select></div>";
                   if(achtml != '')
                   {
                    $("#allac").empty();
                    $("#allac").css('display','');
                    $("#allac").append(achtml);
                   }
               }
                   else
                   {
                       $("#allac").empty();
                       $("#allac").css('display','none');
                   }
                     $('select#ac').on('change', function () {
       
            var acid = $(this).val();
            $.ajax({
                 url: base_url + '/acdeo/getallacauthority',
                type: 'POST',
                data: {_token: token, acid: acid,pcid : pc_id},
                success: function (data)
                {
//                    alert(data);
                   var str = '';
                   var j = 1;
                  var cnt = data.length;
                  if(data != '')
                  {
                       str +="<table id='list-table' class='table table-striped table-bordered table-hover' style='width:100%'><thead><th>S.no.</th><th>Authority Type Name</th><th>Department</th><th>Address</th><th>Mobile No.</th><th>InchargeName</th><th>Active/Inactive</th><th>Edit/Update</th></thead><tbody>";
                       for (var i = 0; i < cnt; i++) {
                           var id=data[i]['nodal_id'];
                           if((data[i]['auth_type_name1'] != '') && (data[i]['auth_type_name1'] != null))
                           {
                           var authtype=data[i]['auth_type_name1'];
                            }
                            else
                            {
                                if((data[i]['auth_type_name2'] != '') && (data[i]['auth_type_name2'] != null))
                           {
                           var authtype=data[i]['auth_type_name2'];
                            }
                            }
                            
                           var dept=data[i]['department'];
                           var add=data[i]['address'];
                           var mb=data[i]['mobile'];
                           var name=data[i]['name'];
                           var auth_id=data[i]['auth_type_id'];
                           str +="<tr><td>"+j+"</td><td>"+authtype+"</td><td>"+dept+"</td><td>"+add+"</td><td>"+mb+"</td><td>"+name+"</td>";
                           if(('is_active'in data[i]) && (data[i]['is_active'] == 0))
                           {
                               str +="<td id='setStatus'><span class='btn btn-danger setStatus' id='0#"+id+"'>InActive</span></td>";
                           }
                           else
                           {
                               str +="<td id='setStatus'><span class='btn btn-success setStatus' id='1#"+id+"'>Active</span></td>";
                           }
                           str +="<td id='edit'><a href='{{url('/acdeo/editauthority')}}/"+id+"&"+auth_id+"&"+pc_id+"&"+acid+"'><span class='btn btn-success float-right'>Edit</span></a></td></tr>";
                           j++;
                       }
                       str +="</tbody></table>";
                       $('#viewps').html(str);
                       $('#list-table').DataTable({
						    "pageLength": 50
					   });
                  }
                  else
                  {
                    $('#viewps').html('No data avilable');
                  }
                  */


    $('body').on('click','.setStatus',function(){    //  $(this) = your current element that clicked.    // additional code});
    // $('.setStatus').on('click', function() {
        var status = $(this).attr('id');
        //alert(status);exit;
        var getArray = status.split("#");
        //            alert(getArray[0]);exit;
        if (getArray[0] == 1) {
            var res = confirm('Are you sure you want to Inactive this user')
            if (res == true) {
                $.ajax({
                    url: base_url + '/acdeo/authoritystatus',
                    type: 'POST',
                    data: {
                        _token: token,
                        status: status
                    },
                    success: function(data) {
                        //                            alert(data);exit;
                        location.reload();
                    }
                });
            } else {
                return false;
            }
        } else {
            var res = confirm('Are you sure you want to Active this user')
            if (res == true) {
                $.ajax({
                    url: base_url + '/acdeo/authoritystatus',
                    type: 'POST',
                    data: {
                        _token: token,
                        status: status
                    },
                    success: function(data) {
                        //                            alert(data);exit;
                        location.reload();
                    }
                });
            } else {
                return false;
            }
        }
    });

    /*  }
            });
        });
                }
        });
        });*/
});
</script>
@endsection