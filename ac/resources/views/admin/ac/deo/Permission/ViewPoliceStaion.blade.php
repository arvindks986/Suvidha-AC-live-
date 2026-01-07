@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
@include('admin.ac.deo.Permission.permission-master-menu')
<section>
		 @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
   
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
              <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h5>Police Station Details</h5>
                </div>
                <div class="card-body tabular-pane">
                    <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label"><b>SELECT PC</b> </label>
                                <div class="col pr-0">
        <!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                    <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
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
                        </div>
                    <div class="col-md-12" >
                        <div class="form-group row" id="allac">
                            
                        </div>
                    </div>
                        <hr/>
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
    $(function () {
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');
        $('select#selectpc').on('change', function () {
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
                url: base_url + '/acdeo/getallacps',
                type: 'POST',
                data: {_token: token, acid: acid},
                success: function (data)
                {
                    
                   var str = '';
                  var cnt = data.length;
                  if(data != '')
                  {
                       str +="<table id='list-table' class='table table-striped table-bordered table-hover' style='width:100%'><thead><th>S.no.</th><th>Police Station Name</th><th>Police Station Address</th><th>Police Station Mobile No</th><th>Incharge Name</th><th>Police Station Incharge Mobile No</th><th>Edit/Update</th></thead><tbody>";
                       for (var i = 0; i < cnt; i++) {
                           var id=data[i]['id'];
                           var pstname=data[i]['police_st_name'];
                           var psiname=data[i]['incharge_name'];
                           var paadd=data[i]['police_station_address'];
                           var pstno=data[i]['police_station_no'];
                           var psino=data[i]['police_st_incharge_no'];
                           str +="<tr><td>"+id+"</td><td>"+pstname+"</td><td>"+paadd+"</td><td>"+pstno+"</td><td>"+psiname+"</td><td>"+psino+"</td><td><a href='{{url('/acdeo/editps')}}/"+id+"' class='btn btn-primary btn-block'>Edit</a></td></tr>";
                       }
                       str +="</tbody></table>";
                       $('#viewps').html(str);
                       $('#list-table').DataTable();
                  }
                  else
                  {
                    $('#viewps').html('No data avilable');
                  }
                }
            });
        });
                }
        });
        });
    });

</script>
@endsection
