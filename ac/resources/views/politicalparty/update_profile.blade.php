@extends('layouts.theme')
@section('title', 'Permission')
@section('content')
<main role="main" class="inner cover mb-3">

<section class="mt-5"></section>
<section class="">
<div class="container">
<div class="row">

<div class="col-md-12">
<div class="card">
<div class="card-header d-flex align-items-center">
<h4>Applicant Personal Details</h4>
</div>
<div class="card-body">
	@if(session()->has('message'))
	    <div class="alert alert-success">
	        {{ session()->get('message') }}
	    </div>
	@endif
<div class="row">
	@foreach($profile as $value)
		<?php $permission=$value['status'];?>


<div class="col">  

<form class="form-horizontal" action="{{url('/update')}}" method="post" autocomplete="off">
	{{ csrf_field() }}
<!--  -->

<!--  -->
<div class="form-group row">
<label class="col-sm-2">Applicant Type <sup style="color:red">*</sup></label>
<div class="col">
	<input type="text" value="{{$users=Session::get('Applicant_type')}}"  class="form-control" readonly/>	
</div>  
<label class="col-sm-2">Political Party/Independent <sup style="color:red">*</sup></label>
<div class="col">
	<select name="party_master" class="form-control" selected="selected">
		<option value="{{$value['party_code']}}">{{$value['party_name']}}</option>
	</select>
</div> 
</div>
<!--  -->
<?php 
	// print_r($state);
	// die;
?>

<div class="form-group row">
<label class="col-sm-2">Name <sup style="color:red">*</sup></label>
<div class="col">

	<input type="hidden" value="{{$election}}" name="election_id" class="form-control" />

	<input type="hidden" value="{{$value['login_id']}}" name="user_login_id" class="form-control" required/>
	@if($permission>0)
	<input type="text" value="{{$value['name']}}" name="name" class="form-control" readonly/>
	@else
	<input type="text" value="{{$value['name']}}" name="name" class="form-control" required/>
	@endif
	<span class="text-danger">{{ $errors->first('name') }}</span>
</div>  
<label class="col-sm-2">Father's / Husband's Name <sup style="color:red">*</sup></label>
<div class="col">
	@if($permission>0)
	<input type="text" value="{{$value['f_name']}}" name="father_name" class="form-control" readonly/>
	@else
	<input type="text" value="{{$value['f_name']}}" name="father_name" class="form-control" required/>
	@endif
	<span class="text-danger">{{ $errors->first('father_name') }}</span>
</div> 
</div>

<div class="line"></div>
<div class="form-group row">
<label class="col-sm-2">Email <sup style="color:red">*</sup></label>

<div class="col">
@if($permission>0)
	<input type="email" value="{{$value['email']}}" name="email" class="form-control" required/>
	@else
	<input type="email" value="{{$value['email']}}" name="email" class="form-control" required/>
	@endif
	<span class="text-danger">{{ $errors->first('email') }}</span>	
</div>  
<label class="col-sm-2">Mobile No <sup style="color:red">*</sup></label>
<div class="col">
<input type="tel" value="{{$value['mobile']}}" name="mobile" class="form-control" maxlength="10" readonly/>
</div>
</div>


<div class="form-group row">
	<label class="col-sm-2">Gender<sup style="color:red">*</sup></label>
<div class="col">
	<select name="radio_stacked" id="gender" class="form-control">
	<option value="male">
		@if(($value['gender'] == 'third'))
		Other
		@elseif(($value['gender'] == 'male'))
		Male
		@elseif(($value['gender'] == 'female'))
		Female
		@else
		{{$value['gender']}}
		@endif
	</option>
	<option value="male">Male</option>
	<option value="female">Female</option>
	<option value="third">Other</option>
	</select>
	
<span class="text-danger">{{ $errors->first('gender') }}</span>



</div> 

<label class="col-sm-2">Date of Birth <sup style="color:red">*</sup></label>

<div class="col">
	@if($permission>0)
	<input type="text" value="{{$value['dob']}}" id="datetimepicker3" name="dob" class="form-control" required/>
	@else
	<input type="text" value="{{$value['dob']}}" id="datetimepicker3" name="dob" class="form-control" required/>
	@endif

<span class="text-danger">{{ $errors->first('dob') }}</span>
</div>  
</div>


<div class="line"></div>	


<div class="line"></div>

<div class="form-group row">
<div class="col-sm-2"><label for="statename">State Name <sup style="color:red">*</sup></label></div>
<div class="col">
<div class="custom-select1" style="width:100%;">
	
<select name="state" id="state" class="form-control" @if($permission>0) disabled="disabled" @endif>

@foreach($state as $key)
	@if($key['code'] == $value['state'])
	<option value="{!!$key['code']!!}">{!!$key['name']!!}</option>
	@endif
@endforeach	

</select>
<span class="text-danger">{{ $errors->first('state') }}</span>
</div>
</div>  

</div> 
<!-- select AC -->

<!-- // -->


<div class="form-group row float-right">       
<div class="col">
<input type="submit" value="Submit" class="btn btn-primary">
</div>
</div>
</form>
</div>
@endforeach

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

 


</script>
@endsection