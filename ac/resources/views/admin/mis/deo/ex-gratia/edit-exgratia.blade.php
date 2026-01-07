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
	<form id="profile_form" name="profile_form" method="POST" enctype="multipart/form-data" action="{{Common::generate_url('mis/update-exgratia')}}">
	<input type="hidden" name="fid" value="{{encrypt($listData->id)}}">
	@csrf
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
			<label for="" class="col-6 col-form-label">Name of Election<span class="red">*</span></label>
			<div class="col-6">
				<select class="form-control" name="election_type" id="election_type">
				<option value="">Select election</option>
				<option value="1" @if($listData->election_type=='1')selected @endif>AC-General</option>
				<option value="2" @if($listData->election_type=='2')selected @endif>AC-BYE</option>
				<option value="3" @if($listData->election_type=='3')selected @endif>PC-General</option>
				<option value="4" @if($listData->election_type=='4')selected @endif>PC-BYE</option>
				</select>
				<span class="red">{!! $errors->first('election_type') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="" class="col-6 col-form-label">Election Year<span class="red">*</span></label>
			<div class="col-6">
				<select class="form-control" name="election_year" id="election_year">
				<option value="">Select year</option>
				@php for($i=1990;$i<=2025;$i++){ @endphp
				<option value="{{$i}}" @if($listData->election_year==$i)selected @endif>{{$i}}</option>
				@php } @endphp
				</select>
				<span class="red">{!! $errors->first('election_year') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="" class="col-6 col-form-label">Name of the state/UT<span class="red">*</span></label>
			<div class="col-6">
				<input class="form-control" type="text" value="{{$st_name}}" id="st_name" name="st_name" readonly>
				<input class="form-control" type="hidden" value="{{$listData->st_code}}" id="st_code" name="st_code">
				<span class="red">{!! $errors->first('st_code') !!}</span>
			</div>
		</div>
		
		<div class="form-group row">
			<label for="dist_no" class="col-6 col-form-label">Name of the district<span class="red">*</span></label>
			<div class="col-6">
				<select class="form-control" name="dist_no" id="dist_no" disabled>
					<option value="">Select district</option>
					@if(!empty($districtlist))
						@foreach($districtlist as $k=>$v)
							<option value="{{$v->DIST_NO}}" @if($listData->dist_no==$v->DIST_NO)selected @endif>{{$v->DIST_NAME}}</option>
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
				@if(!empty($ac_list))
					@foreach($ac_list as $k=>$v)
					<option value="{{$v->AC_NO}}" @if($listData->ac_no==$v->AC_NO)selected @endif>{{$v->AC_NAME}}</option>
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
					<option value="{{$v->PC_NO}}" @if($listData->election_type==$v->PC_NO)selected @endif>{{$v->PC_NAME}}</option>
					@endforeach
				@endif
				</select>
				<span class="red">{!! $errors->first('pc_no') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="applicant_name" class="col-6 col-form-label">Name of polling personnel<span class="red">*</span></label>
			<div class="col-6">
				<input class="form-control" type="text" value="{{$listData->applicant_name}}" id="applicant_name" name="applicant_name">
				<span class="red">{!! $errors->first('applicant_name') !!}</span>
			</div>
		</div>
		
		<div class="form-group row">
			<label for="applicant_designation" class="col-6 col-form-label">Designation<span class="red">*</span></label>
			<div class="col-6">
				<input type="text" class="form-control" type="text" value="{{$listData->applicant_designation}}" id="applicant_designation" name="applicant_designation">
				<span class="red">{!! $errors->first('applicant_designation') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="applicant_parent_department" class="col-6 col-form-label">Parent Department</label>
			<div class="col-6">
				<input type="text" class="form-control" type="text" value="{{$listData->applicant_parent_department}}" id="applicant_parent_department" name="applicant_parent_department">
				<span class="red">{!! $errors->first('applicant_parent_department') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="applicant_address" class="col-6 col-form-label">Address<span class="red">*</span></label>
			<div class="col-6">
				<textarea class="form-control" type="text" id="applicant_address" name="applicant_address">{{$listData->applicant_address}}</textarea>
				<span class="red">{!! $errors->first('applicant_address') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="contact_no" class="col-6 col-form-label">Contact</label>
			<div class="col-6">
				<input class="form-control" type="text" value="{{$listData->contact_no}}" id="contact_no" name="contact_no" onkeypress="return isNumber(event);" maxlength="10">
				<span class="red">{!! $errors->first('contact_no') !!}</span>
			</div>
		</div>
		</div>   
</div>   

<div class="col-sm-6 col-6">
	  <div class="pl-3 pr-3">
		<div class="form-group row">
			<label for="injury_details" class="col-6 col-form-label">Injury Details<span class="red">*</span></label>
			<div class="col-6">
				<select class="form-control" id="injury_details" name="injury_details">
					<option value="">Select details</option>
					<option value="1" @if($listData->injury_details=='1')selected @endif>Injury</option>
					<option value="2" @if($listData->injury_details=='2')selected @endif>Death</option>
					<option value="3" @if($listData->injury_details=='3')selected @endif>Permanent disability</option>
				</select>
				<span class="red">{!! $errors->first('injury_details') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="accident_reason" class="col-6 col-form-label">Reason of injury/Death<span class="red">*</span></label>
			<div class="col-6">
				<select class="form-control" id="accident_reason" name="accident_reason">
					<option value="">Select reason</option>
					<option value="1" @if($listData->accident_reason=='1')selected @endif>Health Issue</option>
					<option value="2" @if($listData->accident_reason=='2')selected @endif>Due to voilent act</option>
					<option value="3" @if($listData->accident_reason=='3')selected @endif>Any other</option>
				</select>
				<span class="red">{!! $errors->first('accident_reason') !!}</span>
			</div>
		</div>
		<div class="form-group row othdesc" style="display:none;">
			<label for="injury_description" class="col-6 col-form-label">Other Description</label>
			<div class="col-6">
				<textarea class="form-control" type="text"  id="injury_description" name="injury_description">{{$listData->injury_description}}</textarea>
				<span class="red">{!! $errors->first('injury_description') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="accident_place" class="col-6 col-form-label">Place of injury/Death<span class="red">*</span></label>
			<div class="col-6">
				<input class="form-control" type="text" value="{{$listData->accident_place}}" id="accident_place" name="accident_place">
				<span class="red">{!! $errors->first('accident_place') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="accident_date" class="col-6 col-form-label">Date of injury/Death<span class="red">*</span></label>
			<div class="col-6">
				<input class="form-control" type="text" value="@if(!empty($listData->accident_date)){{date('d-m-Y',strtotime($listData->accident_date))}}@endif" id="accident_date" name="accident_date" autocomplete="off">
				<span class="red">{!! $errors->first('accident_date') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="exgratia_pending" class="col-6 col-form-label">Ex-gratia Status<span class="red">*</span></label>
			<div class="col-6">
				<select class="form-control" id="exgratia_pending" name="exgratia_pending">
					<option value="">Select status</option>
					<option value="pending" @if($listData->application_status=='pending')selected @endif>Pending</option>
					<option value="granted" @if($listData->application_status=='granted')selected @endif>Granted</option>
					<option value="rejected" @if($listData->application_status=='rejected')selected @endif>Rejected</option>
				</select>
				<span class="red">{!! $errors->first('exgratia_pending') !!}</span>
			</div>
		</div>
		
		<div class="form-group row" id="doa" style="display:none;">
			<label for="date_of_payment" class="col-6 col-form-label">Ex gratia <span id="action_head"></span> Date</label>
			<div class="col-6">
				<input type="text" class="form-control" type="text" value="@if(!empty($listData->date_of_action)){{date('d-m-Y',strtotime($listData->date_of_action))}}@endif" id="date_of_action" name="date_of_action">
				<span class="red">{!! $errors->first('date_of_action') !!}</span>
			</div>
		</div>
		<div class="form-group row" id="rof" style="display:none;">
			<label for="reason_for_pending" class="col-6 col-form-label">Reasons for <span id="reason_head"></span></label>
			<div class="col-6">
				<textarea class="form-control" type="text" id="reason_for_pending" name="reason_for_pending">{{$listData->reason_for_pending}}</textarea>
				<span class="red">{!! $errors->first('reason_for_pending') !!}</span>
			</div>
		</div>
		
		<div class="form-group row" id="paydiv">
			<label for="payment_amount" class="col-6 col-form-label">Payment amount</label>
			<div class="col-6">
				<input type="text" class="form-control" type="text" value="{{$listData->payment_amount}}" id="payment_amount" name="payment_amount" onkeypress="return isNumber(event);">
				<span class="red">{!! $errors->first('payment_amount') !!}</span>
			</div>
		</div>
		<div class="form-group row" id="paydate">
			<label for="date_of_payment" class="col-6 col-form-label">Date of Payment</label>
			<div class="col-6">
				<input type="text" class="form-control" type="text" value="@if(!empty($listData->date_of_payment)){{date('d-m-Y',strtotime($listData->date_of_payment))}}@endif" id="date_of_payment" name="date_of_payment">
				<span class="red">{!! $errors->first('date_of_payment') !!}</span>
			</div>
		</div>
		<div class="form-group row">
			<label for="case_details" class="col-6 col-form-label">Ex Gratia Case Details</label>
			<div class="col-6">
				<textarea class="form-control" type="text" id="case_details" name="case_details">{{$listData->case_details}}</textarea>
				<span class="red">{!! $errors->first('case_details') !!}</span>
			</div>
		</div>
		</div>   
</div>

  </div>
	<div class="text-center pt-3 pb-3">
	 <button class="btn btn-primary submt " type="button">Submit</button>
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
	
	var get_election_type = $("#election_type").val();
	getElectionOption(get_election_type);
	
	var get_reason_type = $("#accident_reason").val();
	getReason(get_reason_type);
	
	var exgratia_pending_value = $("#exgratia_pending").val();
	statusSelection(exgratia_pending_value);
	
	$("#exgratia_pending").change(function(){
		var value = $(this).val();
		statusSelection(value);
	});
	
	function statusSelection(value){
		//alert(value);
		$("#paydiv").hide();
		$("#paydate").hide();
		
		if(value !='' && value !='pending'){
			$("#doa").show();
			$("#action_head").text(value);
			if(value =='rejected'){
				$("#rof").show();
				$("#reason_head").text('rejection');
				$("#date_of_payment").val("");
			}else{
				$("#paydiv").show();
				$("#paydate").show();
				$("#rof").hide();
				$("#reason_head").text('');
			}
		}else{
			$("#rof").show();
			$("#reason_head").text(value);
			$("#doa").hide();
			$("#date_of_payment").val("");
		}
	}
	
	function getElectionOption(val){
		if(val==1 || val==2){
			$(".acdiv").show();
			$(".pcdiv").hide();
		}else{
			$(".acdiv").hide();
			$(".pcdiv").show();
		}
	}
	
	$("#accident_reason").change(function(){
		getReason($(this).val());
	});
	
	function getReason(val){
		if(val==3 && val !=''){
			$(".othdesc").show();
		}else{
			$(".othdesc").hide();
		}
	}
    
	$("#election_type").change(function(){
		var val = $(this).val();
		getElectionOption();
	});
	$("#dist_no").change(function(){
		var stcode = $("#st_code").val();
		var distno = $(this).val();
		
		$.ajax({
			url: "{{url('/acceo/mis/get-acno')}}",
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
		var date = new Date();
		$("#accident_date ,#date_of_payment, #date_of_action").datetimepicker({
            format: 'DD-MM-YYYY',
			maxDate: 'now'
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