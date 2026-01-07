@extends('admin.central.common.theme')
@section('title', 'Profile Form')
@section('bradcome')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => Common::generate_url('mis/list-exgratia'),
    'name' => 'List Ex-Gratia'
  ]; 
  ?>
@endsection
@section('content') 
@section('style')
<style type="text/css">
.bordernone{border:none;}
</style>
@endsection
<link rel="stylesheet" href="{{ asset('css/custom-profile.css') }}">
 <main class="pt-3 pb-5 pl-5 pr-5 ac-prof-form">
	 <div class="container-fluid">
	  <h4>Ex-Gratia Form</h4>
	<div class="card card-shadow p-1">
    <div class="card-body">
	<form id="profile_form" name="profile_form" method="POST" enctype="multipart/form-data">
 {{ csrf_field() }}
	<div class="row">
		<div class="col-12">
			<div class="col-6">
				@if(session()->has('success_msg'))
				<div class="alert alert-success alert-dismissible">{{ session()->get('success_msg') }}</div>
				@endif
				@if(session()->has('error_msg'))
				<div class="alert alert-danger alert-dismissible">{{ session()->get('error_msg') }}</div>
				@endif
			</div>
		</div>
	<div class="col-sm-6 col-6">
	  <div class="pl-3 pr-3">
		<div class="form-group row">
			<label for="election_type" class="col-6 col-form-label">Select Election</label>
			<div class="col-6">
				<select class="form-control" name="election_type" id="election_type">
				<option value="">Select election</option>
				<option value="1">AC-General</option>
				<option value="2">AC-BYE</option>
				<option value="3">PC-General</option>
				<option value="4">PC-BYE</option>
				</select>
				<span class="red">{!! $errors->first('election_type') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="election_year" class="col-6 col-form-label">Select Year</label>
			<div class="col-6">
				<select class="form-control" name="election_year" id="election_year">
				<option value="">Select year</option>
				@php for($i=2019;$i<=2025;$i++){ @endphp
				<option value="{{$i}}">{{$i}}</option>
				@php } @endphp
				</select>
				<span class="red">{!! $errors->first('election_year') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="st_name" class="col-6 col-form-label">Name of the state/UT</label>
			<div class="col-6">
				<input class="form-control" type="text" value="{{$st_name}}" id="st_name" name="st_name" readonly>
				<input class="form-control" type="hidden" value="{{$st_code}}" id="st_code" name="st_code">
				<span class="red">{!! $errors->first('st_code') !!}</span>
			</div>
		</div>
		
		<div class="form-group row">
			<label for="dist_no" class="col-6 col-form-label">Name of the district</label>
			<div class="col-6">
				<select class="form-control" name="dist_no" id="dist_no" disabled>
					<option value="">Select district</option>
					@if(!empty($districtlist))
						@foreach($districtlist as $k=>$v)
							<option value="{{$v->DIST_NO}}" @if($dist_no==$v->DIST_NO)selected @endif>{{$v->DIST_NAME}}</option>
						@endforeach
					@endif
				</select>
				<span class="red">{!! $errors->first('dist_no') !!}</span>
			</div>
		</div>
		<div class="form-group row acdiv" style="display:none;">
			<label for="ac_no" class="col-6 col-form-label">AC</label>
			<div class="col-6">
				<select class="form-control" name="ac_no" id="ac_no">
				<option value="">Select ac</option>
				@if(!empty($acArr))
					@foreach($acArr as $k=>$v)
					<option value="{{$v->AC_NO}}">{{$v->AC_NAME}}</option>
					@endforeach
				@endif
				</select>
				<span class="red">{!! $errors->first('ac_no') !!}</span>
			</div>
		</div>
		<div class="form-group row pcdiv" style="display:none;">
			<label for="pc_no" class="col-6 col-form-label">PC</label>
			<div class="col-6">
				<select class="form-control" name="pc_no" id="pc_no">
				<option value="">Select pc</option>
				@if($pcArr)
					@foreach($pcArr as $k=>$v)
					<option value="{{$v->PC_NO}}">{{$v->PC_NAME}}</option>
					@endforeach
				@endif
				</select>
				<span class="red">{!! $errors->first('pc_no') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="applicant_name" class="col-6 col-form-label">Name of polling personnel</label>
			<div class="col-6">
				<input class="form-control" type="text" value="" id="applicant_name" name="applicant_name">
				<span class="red">{!! $errors->first('applicant_name') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="applicant_address" class="col-6 col-form-label">Address of the applicant</label>
			<div class="col-6">
				<textarea class="form-control" type="text" value="" id="applicant_address" name="applicant_address"></textarea>
				<span class="red">{!! $errors->first('applicant_address') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="contact_no" class="col-6 col-form-label">Contact No If Any</label>
			<div class="col-6">
				<input class="form-control" type="text" value="" id="contact_no" name="contact_no">
				<span class="red">{!! $errors->first('contact_no') !!}</span>
			</div>
		</div>
		</div>   
</div>   

<div class="col-sm-6 col-6">
	  <div class="pl-3 pr-3">
		<div class="form-group row">
			<label for="exgratia_pending" class="col-6 col-form-label">Ex-gratia Pending If Any</label>
			<div class="col-6">
				<select class="form-control" id="exgratia_pending" name="exgratia_pending">
					<option value="">Select if pending</option>
					<option value="yes">Yes</option>
					<option value="no">No</option>
				</select>
				<span class="red">{!! $errors->first('exgratia_pending') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="reason_for_pending" class="col-6 col-form-label">Reasons for pending</label>
			<div class="col-6">
				<textarea class="form-control" type="text" value="" id="reason_for_pending" name="reason_for_pending"></textarea>
				<span class="red">{!! $errors->first('reason_for_pending') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="accident_date" class="col-6 col-form-label">Date of injury/Death</label>
			<div class="col-6">
				<input class="form-control" type="text" value="" id="accident_date" name="accident_date" autocomplete="off">
				<span class="red">{!! $errors->first('accident_date') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="accident_place" class="col-6 col-form-label">Place of injury/Death</label>
			<div class="col-6">
				<input class="form-control" type="text" value="" id="accident_place" name="accident_place">
				<span class="red">{!! $errors->first('accident_place') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="injury_details" class="col-6 col-form-label">Injury Details</label>
			<div class="col-6">
				<select class="form-control" id="injury_details" name="injury_details">
					<option value="">Select details</option>
					<option value="1">Injury</option>
					<option value="2">Death</option>
					<option value="3">Permanent disability</option>
				</select>
				<span class="red">{!! $errors->first('injury_details') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="accident_reason" class="col-6 col-form-label">Reason of injury/Death</label>
			<div class="col-6">
				<select class="form-control" id="accident_reason" name="accident_reason">
					<option value="">Select reason</option>
					<option value="1">Health Issue</option>
					<option value="2">Due to voilent act</option>
					<option value="3">Any other</option>
				</select>
				<span class="red">{!! $errors->first('accident_reason') !!}</span>
			</div>
		</div>
		<div class="form-group row othdesc" style="display:none;">
			<label for="injury_description" class="col-6 col-form-label">Other Description</label>
			<div class="col-6">
				<textarea class="form-control" type="text" id="injury_description" name="injury_description"></textarea>
				<span class="red">{!! $errors->first('injury_description') !!}</span>
			</div>
		</div>
		</div>   
</div>

  </div>
	<div class="text-center pt-3 pb-3">
	 <button class="btn btn-primary submt " type="button">@if(isset($ac_details))Update @else Submit @endif</button>
	 <a href="{{Common::generate_url('mis/list-exgratia')}}"><button class="btn btn-default" type="button">Cancel</button></a>
   </div>	
	</form>	
	</div>
		</div>
	</div>

   </main>
 

   
@endsection
<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
@section('script') 
 <script type="text/javascript">
    var role_id = '<?php echo $user_data->role_id ?>';
    var prefix = 'acdeo';
    
	$("#election_type").change(function(){
		var val = $(this).val();
		if(val==1 || val==2){
			$(".acdiv").show();
			$(".pcdiv").hide();
		}else{
			$(".acdiv").hide();
			$(".pcdiv").show();
		}
	});
	$("#accident_reason").change(function(){
		if($(this).val()==3 && $(this).val() !=''){
			$(".othdesc").show();
		}else{
			$(".othdesc").hide();
		}
	});
	$("#dist_no").change(function(){
		var stcode = $("#st_code").val();
		var distno = $(this).val();
		
		$.ajax({
			url: "{{url('/acdeo/mis/get-acno')}}",
			type: 'GET',
			data: {district: distno, stcode: stcode},
			success: function (result) {
				var distselect = $("select[name='ac_no']");
				distselect.empty();
				var achtml = '';
				achtml = achtml + '<option value="">-- Select AC --</option> ';
				$.each(result, function (key, value) {
					achtml = achtml + '<option value="' + value.AC_NO + '">'+ value.AC_NAME + '</option>';
					$("select[name='ac_no']").html(achtml);
				});
				var achtml_end = '';
				$("select[name='ac_no']").append(achtml_end)
			}
		});
	});
	
	$(document).ready(function(){
		$("#accident_date").datetimepicker({
            format: 'DD-MM-YYYY'
        });
		 // This function for Edit Form	
		 $('.edt-btn').click(function(){
			  $('.vlu-show').hide();
			  $('.inpt-hid').show(); 
			  $('.statemap').show(); 
			  $('.submt').show(); 
		 });
		 
		  $('.submt').click(function(){
			var err_cnt = 0;
		     $('.inpt-hid').each(function(){
			    if($(this).val() == ''){
				    $(this).next('span').html('This field is required.');
					//$(this).focus();
					err_cnt++;
			    }	 
			 });
			 var total_input = 9;
			 err_cnt = total_input - err_cnt;
			 if(err_cnt > 0 && err_cnt < total_input){
				 return false;
			 }else{
				 $("#profile_form").submit();
			 }
		
		  });  
		  
		$('.inpt-hid').keyup(function(){
		     $(this).each(function(){
			   if($(this).val() != ''){
				    $(this).next('span').html('');
			   }	 
			 });

		});  
		
	});
	
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}
    
</script>
@endsection