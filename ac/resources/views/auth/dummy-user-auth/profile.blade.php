@extends('layouts.theme')
@section('title', 'Permission')
@section('content')
<main role="main" class="inner cover mb-3">


<section class="mt-5 prflTop">
<div class="container">
<div class="row">
<div class="col-md-12">
@if(session()->has('msg'))
    <div  style="" class="alert alert-warning text-center">
        {{ session()->get('msg') }}
    </div >
@endif
</div>
</div>
<div class="row">

<div class="col-md-12">
<div class="card">
	

<div class="card-header d-flex align-items-center">
<h4>Applicant Personal Details</h4>
</div>
<div class="card-body">
<div class="row">
	
<div class="col">                  
<form class="form-horizontal" action="{{url('/addprofile')}}" method="post" autocomplete="off">
	{{ csrf_field() }}
<!--  -->
<div class="form-group row">
<label class="col-sm-2">Applicant Type <sup style="color:red">*</sup></label>
<div class="col">
	<input type="text" value="{{$users=Session::get('Applicant_type')}}"  class="form-control" readonly/>

	<input type="hidden" name="election_id" value="{{$elc_id}}"  class="form-control" readonly/>	
</div> 
<div class="col-sm-2"><label for="statename">Political Party/Independent <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
<select name="party_master" class="form-control">
<option value="{{$party_master[0]->CCODE}}">{{$party_master[0]->PARTYNAME}}</option>
</select>
<span class="text-danger">{{ $errors->first('district') }}</span>

</div>
</div>


</div>
<!--  -->

<div class="form-group row">
<label class="col-sm-2">Name<sup style="color:red">*</sup></label>
<div class="col">
	<input type="hidden" value="{{$user_login_id}}" name="user_login_id" class="form-control" required/>
<input type="text" placeholder="Enter Name" name="name" value="{{old('name')}}" class="form-control" required/>
<span class="text-danger">{{ $errors->first('name') }}</span>
</div>  
<label class="col-sm-2">Father's / Husband's Name <sup style="color:red">*</sup></label>
<div class="col">
<input type="text" placeholder="Enter Name" name="father_name" value="{{old('father_name')}}" class="form-control" required/>
<span class="text-danger">{{ $errors->first('father_name') }}</span>
</div> 
</div>

<div class="line"></div>
<div class="form-group row">
<label class="col-sm-2">Email <sup style="color:red">*</sup></label>

<div class="col">
<input type="email" placeholder="Email ID" name="email" value="{{old('email')}}" class="form-control" required/>
<span class="text-danger">{{ $errors->first('email') }}</span>
</div>  
<label class="col-sm-2">Mobile No <sup style="color:red">*</sup></label>
<div class="col">
<input type="tel" value="{{$mobile}}" name="mobile" class="form-control" maxlength="10" readonly/>
</div>
</div>


<div class="form-group row">
<label class="col-sm-2">Gender <sup style="color:red">*</sup></label>

<div class="col">
<div class="custom-control custom-radio">
<input type="radio" class="custom-control-input" id="customControlValidation2" name="gender" value="female">
<label class="custom-control-label" for="customControlValidation2">Female</label>
</div>
<div class="custom-control custom-radio ">
<input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male">
<label class="custom-control-label" for="customControlValidation3">Male</label>

</div>
<div class="custom-control custom-radio mb-3">
<input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="transgender">
<label class="custom-control-label" for="customControlValidation4">Other</label>
</div>
<span class="text-danger">{{ $errors->first('gender') }}</span>


</div> 

<label class="col-sm-2">Date of Birth <sup style="color:red">*</sup></label>

<div class="col">
<input type="text" placeholder="date" id="datetimepicker3" name="dob" value="{{old('dob')}}" class="form-control" required/>
<span class="text-danger">{{ $errors->first('dob') }}</span>
</div>  
</div>


<div class="line"></div>	

<div class="form-group row">
<label class="col-sm-2">Address <sup style="color:red">*</sup></label>

<div class="col">
<textarea name="Address1" id="" cols="10" rows="4" class="form-control" value="{{old('Address1')}}" placeholder="Enter current address" required></textarea>
<span class="text-danger">{{ $errors->first('Address1') }}</span>
<br />	 
</div>  

</div>
<div class="line"></div>

<div class="form-group row">
<div class="col-sm-2"><label for="statename">State Name <sup style="color:red">*</sup></label></div>
<div class="col">
<div class="custom-select1" style="width:100%;">
<select name="state" id="state" class="form-control">
<option value="">-- Select State --</option>
@foreach($getStates as $State)  
<option value="{{ $State->ST_CODE }}"> {{$State->ST_NAME }}</option>
@endforeach 
</select>
<span class="text-danger">{{ $errors->first('state') }}</span>
</div>
</div>  
<div class="col-sm-2"><label for="statename">District <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
<select name="district" id="district" class="form-control">
<option value="">-- Select Districts --</option>

</select>
<span class="text-danger">{{ $errors->first('district') }}</span>

</div>
</div> 
</div> 
<div class="form-group row">
<div class="col-sm-2"><label for="statename">AC <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
<select name="ac" id="ac" class="form-control">
<option value="">-- Select AC --</option>
</select>
<span class="text-danger">{{ $errors->first('ac') }}</span>
</div>
</div> 



</div> 
<div class="form-group row float-right">       
<div class="col">
<input type="submit" value="Submit" class="btn btn-primary">
</div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
</div>	  
</section>

</main>
@endsection
@section('script')

<script>
jQuery(function(){
jQuery('#datetimepicker3').datetimepicker({
format: 'YYYY-MM-DD',
useCurrent: false, 
maxDate: new Date() 
 });
       
});
jQuery(document).ready(function(){
	//alert("hello");
	jQuery("select[name='state']").change(function()
	{
		var stcode = jQuery(this).val();
		//alert(stcode);
		jQuery.ajax({
		url: "{{url('/getDistrictsval')}}",
		type: 'GET',
		dataType: 'json',
		data: {stcode:stcode},
		success: function(result){
		//alert(result);
		var distselect = jQuery('form select[name=district]');
		distselect.empty();
		var statehtml = '';
		statehtml = statehtml + '<option value=""> Select District</option> ';
		jQuery.each(result,function(key, value) {
		statehtml = statehtml + '<option value="'+value.DIST_NO+'">'+value.DIST_NAME+'</option>'; 
		jQuery("select[name='district']").html(statehtml);
		});
		var statehtml_end = '';
		jQuery("select[name='district']").append(statehtml_end)
		}
		});
	
	});

	jQuery("#district").change(function()
	{
		///alert('test');
		var stcode = jQuery("select[name='state']").val();
		var district = jQuery(this).val();
		//alert(stcode);
		jQuery.ajax({
		url: "{{url('/getACListsval')}}",
		type: 'GET',
		dataType: 'json',
		data: {stcode:stcode,district:district},
		success: function(result){
		//alert(result);
		var acselect = jQuery('select[name="ac"]');
		acselect.empty();
		var achtml = '';
		achtml = achtml + '<option value=""> Select AC</option> ';
		jQuery.each(result,function(key, value) {
		achtml = achtml + '<option value=' + value.AC_NO + '>' + value.AC_NAME + '</option>';
		jQuery("select[name='ac']").html(achtml);		
		});
		var achtmlend = '';
		jQuery("select[name='ac']").append(achtmlend)
		}
		});
	});

});
</script>
@endsection