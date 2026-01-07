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
 @if($errors->any())
                    <div class="alert alert-danger">{{$errors->first()}}</div>
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
<form class="form-horizontal" action="{{url('/addprofile')}}" id="testForm" method="post" autocomplete="off">
	{{ csrf_field() }}
<!--  -->
<div class="form-group row">
<label class="col-sm-2">Applicant Type <sup style="color:red">*</sup></label>
<div class="col">
	<input type="text" value="{{$users=Session::get('Applicant_type')}}"  id="Applicanttype" class="form-control" readonly/>

	<input type="hidden" name="election_id" value="{{$elc_id}}"  class="form-control" readonly/>	
</div> 
<div class="col-sm-2"><label for="statename">Political Party/Independent <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
<select name="party_master" class="form-control" id="party_master">
<option value="">Select Political Party/Independent</option>
@if(!empty($allParty))
@foreach($allParty as $party)  
<option value="{{ $party->CCODE}}" {{ (collect(old('party_master'))->contains($party->CCODE)) ? 'selected':'' }}> {{$party->PARTYNAME }}</option>
@endforeach 
@endif 
</select>
<span class="text-danger">{{ $errors->first('party_master') }}</span>

</div>
</div>


</div>
<!--  -->

<div class="form-group row">
<label class="col-sm-2">Name<sup style="color:red">*</sup></label>
<div class="col">
	<input type="hidden" value="{{$user_login_id}}" name="user_login_id" class="form-control" required/>
<input type="text" placeholder="Enter Name" name="name" value="" id="name" class="form-control" required/>
<span class="text-danger">{{ $errors->first('name') }}</span>
</div>  
<label class="col-sm-2">Father's / Husband's Name <sup style="color:red">*</sup></label>
<div class="col">
<input type="text" placeholder="Enter Name" name="father_name" id="father_name" value="{{old('father_name')}}" class="form-control" required/>
<span class="text-danger">{{ $errors->first('father_name') }}</span>
</div> 
</div>

<div class="line"></div>
<div class="form-group row">
<label class="col-sm-2">Email <sup style="color:red">*</sup></label>

<div class="col">
<input type="email" placeholder="Email ID" name="email" id="email" value="" class="form-control" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+"/>
<span class="text-danger">{{ $errors->first('email') }}</span>
</div>  
<label class="col-sm-2">Mobile No <sup style="color:red">*</sup></label>
<div class="col">
<input type="tel" value="{{$mobile}}" name="mobile" id="mobile" class="form-control" maxlength="10" readonly required pattern="[6789][0-9]{9}"/>
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
<input type="text" placeholder="date" id="datetimepicker3"  name="dob" value="{{old('dob')}}" class="form-control" required/>
<span class="text-danger">{{ $errors->first('dob') }}</span>
</div>  
</div>


<div class="line"></div>	

<!-- <div class="form-group row">
<label class="col-sm-2">Address <sup style="color:red">*</sup></label>

<div class="col">
<textarea name="Address1" id="Address1" cols="10" rows="4" class="form-control" value="{{old('Address1')}}" placeholder="Enter current address" required></textarea>
<span class="text-danger">{{ $errors->first('Address1') }}</span>
<br />	 
</div>  

</div> -->
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
<!-- <div class="col-sm-2"><label for="statename">District <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
<select name="district" id="district" class="form-control">
<option value="">-- Select Districts --</option>

</select>
<span class="text-danger">{{ $errors->first('district') }}</span>

</div>
</div>  -->
</div> 
<!-- <div class="form-group row">
<div class="col-sm-2"><label for="statename">AC <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
<select name="ac" id="ac" class="form-control">
<option value="">-- Select AC --</option>
</select>
<span class="text-danger">{{ $errors->first('ac') }}</span>
</div>
</div> 



</div> --> 
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
// jQuery(function(){
// jQuery('#datetimepicker3').datetimepicker({
// format: 'YYYY-MM-DD',
// useCurrent: false, 
// maxDate: new Date() 
//  });
       
// });
		var today = new Date();
const minAge = 18;
const maxAge = 100;
var minDate = new Date(today);
minDate.setFullYear(today.getFullYear() - maxAge);
var maxDate = new Date(today);
maxDate.setFullYear(today.getFullYear() - minAge);

jQuery(function () {
    jQuery('#datetimepicker3').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        minDate: minDate,
        maxDate: maxDate,
    });

    // Add client-side form validation on form submission
    jQuery('#testform').on('submit', function (e) {
        var selectedDate = jQuery('#datetimepicker3').data('DateTimePicker').date().toDate();

         if (selectedDate < minDate || selectedDate > maxDate) {
            // Display an error message
           errorContainer.html('Please select a date between ' + minAge + ' and ' + maxAge + ' years ago.');
            
         e.preventDefault();
        } else {
            // Clear the error message if the date is valid
            errorContainer.html('');
        }
    });
});

$(document).ready(function() {
    // Add a custom method for mobile validation
    $.validator.addMethod("mobileIndia", function(value, element) {
        return this.optional(element) || /^[6789]\d{9}$/.test(value);
    }, "Please enter a valid mobile number starting with 6, 7, 8, or 9");

    // Add a custom method for email validation
    $.validator.addMethod("emailPattern", function(value, element) {
        return this.optional(element) || /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value);
    }, "Please enter a valid email address");

    // Add a custom method for radio group validation
    $.validator.addMethod("genderValidation", function(value, element) {
        return $("input[name='gender']:checked").length > 0;
    }, "Please select a gender");

    // Initialize form validation
    $('#testForm').validate({
        rules: {
        	Applicanttype:{
             required:true
        	},
            party_master: {
                required: true
            },
            name: {
                required: true
            },
            father_name: {
                required: true
            },
            email: {
                required: true,
                email: true,
                emailPattern: true
            },
            mobile: {
                required: true,
                mobileIndia: true
            },
            datetimepicker3: {
                required: true
            },
            state: {
                required: true
            },
           
            
            
            gender: {
                genderValidation: true
            }
        },
        messages: {
        	Applicanttype: 'Please Select Applicant Type',
            party_master: 'Please Select Political Party',
            name: 'Please Enter Candidate Name',
            father_name: 'Please Enter Father Name',
            email: 'Please enter a valid email address',
            mobile: 'Please enter a valid mobile number starting with 6, 7, 8, or 9',
            datetimepicker3: 'Please Select a DateOfBirth Above 18 Year',
            
            state: 'Please Select State First',
            district: 'Please Select District First',
           
            gender: 'Please select a gender'
        },
        errorPlacement: function(error, element) {
            if (element.attr("type") === "radio") {
                error.insertAfter(element.closest(".custom-control"));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});

</script>
@endsection
