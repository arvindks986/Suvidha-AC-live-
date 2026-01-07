@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
 @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
 @include('admin.ac.deo.Permission.permission-master-menu')

@if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
<section>
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
              <div class="card" style="max-width:700px; margin:0 auto;">
                <div class="card-header d-flex align-items-center">
                  <h5>ADD Police Station</h5>
                </div>
				 <form class="form-horizontal" action="{{url('/acdeo/AddPSData')}}" method="POST">
                <div class="card-body">
                 
                      {{csrf_field()}}
                    <div class="form-group row">
                      <label class="col-sm-4 form-control-label">Select PC<sup>*</sup></label>
                      <div class="col-sm-8">
                          <select name="pc" class="form-control" id="pc">
                            <option value="0">Select PC</option>
                            @if(!empty($getAllPC))
                            @foreach($getAllPC as $pc)  
                            <option value="{{$pc->PC_NO}}" {{ (collect(old('pc'))->contains($pc->PC_NO)) ? 'selected':'' }}> {{$pc->PC_NAME_EN}}</option>
                            @endforeach 
                            @endif
                        </select>
                        <span class="text-danger">{{ $errors->error->first('pc') }}</span>
                        <span class="text-danger">{{ $errors->error->first('ac') }}</span>
                      </div>
                    </div>
                      <div class="form-group row" id="allac">
                          <span class="text-danger">{{ $errors->error->first('ac') }}</span>
                      </div>
                    <div class="line"></div>
                    <div class="form-group row">
                      <label class="col-sm-4 form-control-label">Police Station Name <sup>*</sup></label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control" name="ps_name" value="{{old('ps_name')}}">
                          <span class="text-danger">{{ $errors->first('ps_name') }}</span>
                      </div>
                    </div>
                    <div class="line"></div>
                    <div class="form-group row">
                      <label class="col-sm-4 form-control-label" >Police Station Address <sup>*</sup></label>
                      <div class="col-sm-8">
		        <textarea name="ps_addr" id="" cols="3" rows="2" class="form-control">{{old('ps_addr')}}</textarea>
                        <span class="text-danger">{{ $errors->first('ps_addr') }}</span>
                      </div>
                    </div>
                    <div class="line"></div>
                    <div class="form-group row">
					
                      <label class="col-sm-4 form-control-label">Incharge Name<sup>*</sup></label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control" name="uname" value="{{old('uname')}}">
                        <span class="text-danger">{{ $errors->first('uname') }}</span>
                      </div>
                     </div>
                    <div class="form-group row">
					
                      <label class="col-sm-4 form-control-label">Police Station Incharge Mobile No<sup>*</sup></label>
                      <div class="col-sm-8">
                          <input type="tel" class="form-control" name="ps_imb" value="{{old('ps_imb')}}">
                        <span class="text-danger">{{ $errors->first('ps_imb') }}</span>
                      </div>
                     </div>
                 
					  <div class="form-group row">
                      <label class="col-sm-4 form-control-label">Police Station Mobile No<sup>*</sup></label>
                      <div class="col-sm-8">
                          <input type="tel" class="form-control" name="ps_smb" value="{{old('ps_smb')}}">
                        <span class="text-danger">{{ $errors->first('ps_smb') }}</span>
                      </div>
                      </div>
                 
                  
                   
               
                </div>
				<div class="card-footer">
				<div class="form-group row ">
                      <div class="col">
<!--                        <button type="submit" class="btn btn-secondary" >Cancel</button>-->
                        <button type="submit" class="btn btn-primary float-right" name="AddPS" value="Save">Save</button>
                      </div>
                    </div>
				</div>
				   </form>
              </div>
            </div>
</div>
</div>
 <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
</section>

@endsection
@section('script')
<script type="text/javascript">
    $(function () {
        var base_url = $("#base_url").val();
         var token = $('meta[name="csrf-token"]').attr('content');
          $("#allac").empty();
        $('select#pc').change(function () {
            var pc_id = $(this).val();
//            var stcode = $("#state :selected").val();
//            $("select[name='police_station']").empty();
//            var achtml = '';
//            achtml = achtml + "<option value='0'>Select Police Station</option>";
            
            $.ajax({
                url: base_url + '/acdeo/getAllAC',
                type: 'POST',
                data: {_token: token, pc_id:pc_id},
                success: function (response)
                {
//                    alert(response);exit;
                    var achtml = '';
                    achtml = achtml+ "<label class='col-sm-4 form-control-label'>Select AC<sup>*</sup></label><div class='col-sm-8'><select name='acno' class='form-control' id='ac'><option value='0'>Select AC</option>";
                    var cnt = response.length;
                    if(cnt != 0 )
                    {
                    for (var i = 0; i < cnt; i++) {
                        var ac_no=response[i]['AC_NO'];
                        var ac_name=response[i]['AC_NAME'];
                       achtml = achtml + "<option value="+ac_no+">"+ac_name+"</option>";
                   }
                   achtml = achtml+"</div></select";
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
                }
        });
    });
    });
</script>
@endsection