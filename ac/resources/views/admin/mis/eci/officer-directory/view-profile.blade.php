@extends('admin.central.common.theme')
@section('title', 'Update Officer Details')
<?php
$breadcrumbs = [];
$breadcrumbs[] = [
    'href' => Common::generate_url('mis/officer-details'),
    'name' => 'Officer Details'
];
?>
@section('content') 
 <?php   
		    $st=getstatebystatecode($getofficerdetails->st_code); 
       
  ?>
  <style>
  /* your styles go here */
.avatar-upload {
    position: relative;
    max-width: 185px;
    /* margin: 50px auto; */
}
.avatar-upload .avatar-edit {
    position: absolute;
    right: 12px;
    z-index: 1;
    top: 10px;
}
.avatar-upload .avatar-edit input {
    display: none;
}
.avatar-upload .avatar-edit input + label {
    display: inline-block;
    width: 34px;
    height: 34px;
    margin-bottom: 0;
    border-radius: 100%;
    background: #FFFFFF;
    border: 1px solid transparent;
    box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
    cursor: pointer;
    font-weight: normal;
    transition: all .2s ease-in-out;
}
.avatar-preview {
    width: 200px;
    height: 200px;
    position: relative;
    border-radius: 100%;
    border: 6px solid #F8F8F8;
    box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
}
.avatar-preview > div {
    width: 100%;
    height: 100%;
    border-radius: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    overflow: hidden;
}
.avatar-preview #imagePreview img {
    /* border: 6px solid #f8f8f8; */
    /* border-radius: 50%; */
    /* box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1); */
    width: 101%;
    height: 101%;
    overflow: hidden;
    background: #f1f1f1;
    left: -2px;
    position: relative;
    top: -2px;
}
i.fa.fa-edit.pencil-edit {
    font-size: 24px;
    padding: 9px;
    color: #fff;
    background: #c5468e;
    border-radius: 50px;
}
  </style>
<main role="main" class="inner cover mb-3 mt-3">
	<section>
		<form enctype="multipart/form-data" id="election_form" method="POST" action="{{Common::generate_url('mis/update-officer-profile')}}">
			{{ csrf_field() }}
			<div class="container">
				<div class="row">
					<div class="card text-left" style="width:100%; margin:0 auto;">
						<div class="card-header">
							<div class="row">
								<div class="col">
									<h4>Profile Details</h4>
								</div>
								<div class="col">
									<p class="mb-0 text-right"><b>State Name:</b> <span
											class="badge badge-info">{{ !empty($getofficerdetails->st_code) ? getstatebystatecode($getofficerdetails->st_code)->ST_NAME : ''}}</span></p>
								</div>
							</div>
							<!--End row-->
						</div>
						<!--End card-header-->
						@if(Session::has('success_mes'))
						<div class="alert alert-success">
							<strong> {{ nl2br(Session::get('success_mes')) }}</strong>
						</div>
						@endif
						@if(Session::has('error_mes'))
						<div class="alert alert-danger">
							<strong> {{ nl2br(Session::get('error_mes')) }}</strong>
						</div>
						@endif

						<div class="container p-0">
							<div class="row">
								<div class="col-md-12">
									<div class="card">
										<div class="card-body">
											<fieldset id="pc_div" class="mb-2">
												<legend> Personal Details <sup>*</sup></legend>
												<div class="row">
													<div class="col">
														<div class="box pc_no_msg">
											<div class="col-md-3">
											<div class="avatar-upload">
												<label for="imageUpload">Profile Image</label>
												
												@if(@$getofficerdetails->profile_pic != '' )
													<div class="avatar-preview ">
														<div id="imagePreview">
															<img src="{{ asset($getofficerdetails->profile_pic) }}"/>
														</div>
													</div>
												@else
													<div class="avatar-preview">
														<div id="imagePreview" style="padding-top:85px;padding-left:50px;">No Image</div>
													</div>
												@endif
												<div class="profileerrormsg errormsg errorred"></div>
											</div>
											<?php /*<img class="rounded-circle" src="{{ asset('admintheme/img/vendor/avtar.jpg')}}" alt="" />*/?>
										</div>
											

											

											
														</div></div></div></fieldset>
											<fieldset id="pc_div" class="mb-2">
												<legend> Personal Details <sup>*</sup></legend>
												<div class="row">
													<div class="col">
														<div class="box pc_no_msg">
											<div class="form-group row">
												<label class="col-sm-3">Name</label>
												<div class="col">
													<span>{{ $getofficerdetails->name }}</span>
												</div>
											</div>
											

											<div class="form-group row">
												<label class="col-sm-3">Designation</label>
												<div class="col">
													<span>{{ $getofficerdetails->designation }}</span>
												</div>
											</div>
											

											<div class="form-group row">
												<label class="col-sm-3">Email</label>
												<div class="col">
													<span>{{ !empty($getofficerdetails->email) ? $getofficerdetails->email : '-' }}</span>
												</div>
											</div>
											

											<div class="form-group row">
												<label class="col-sm-3">Phone</label>
												<div class="col">
													<span>{{ !empty($getofficerdetails->Phone_no) ? $getofficerdetails->Phone_no : '-' }}</span>
												</div>
											</div>
														</div></div></div></fieldset>

											<fieldset id="pc_div" class="mb-2">
                                                    <legend> DEO Address <sup>*</sup></legend>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="box pc_no_msg">
											<div class="form-group row">
												<label class="col-sm-3">Address Line1 <sup>*</sup></label>
												<div class="col">
													<span>{{ $getofficerdetails->ro_address_l1 }}</span>
												</div>
											</div>
											<div class="address1_noerrormsg errormsg errorred"></div>
									

											<div class="form-group row">
												<label class="col-sm-3">Address Line2 <sup>*</sup></label>
												<div class="col">
													<span>{{ $getofficerdetails->ro_address_l2 }}</span>
												</div>
											</div>
											<div class="address2_noerrormsg errormsg errorred"></div>
									
											<div class="form-group row">
												<label class="col-sm-3">Pin Code<sup>*</sup></label>
												<div class="col">
													{{ $getofficerdetails->ro_address_pin_code }}
												</div>
											</div>
											<div class="zip_noerrormsg errormsg errorred"></div>
									

											<div class="form-group row">
												<label class="col-sm-3">District</label>
												<div class="col">
													<span>{{ (!empty($getofficerdetails->st_code) && !empty($getofficerdetails->dist_no)) ? getdistrictbydistrictno($getofficerdetails->st_code, $getofficerdetails->dist_no)->DIST_NAME : '-'}}</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-3">State</label>
												<div class="col">
													<span>{{ !empty($getofficerdetails->st_code) ? getstatebystatecode($getofficerdetails->st_code)->ST_NAME : '-'}}</span>
												</div>
											</div>
									
                                                            </div></div></div></fieldset>
											<div class="form-group row float-right">
												<div class="col">
													<a href="{{Common::generate_url('mis/officer-directory')}}" class="actn-btn-icon"><button type="button" class="btn btn-primary">Back</button></a>
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</form>
	</section>
</main>
@endsection

@section('script')

<script>
<?php if(Session::has('success_mes')){?>
setTimeout(function(){ $(".alert-success").hide(); }, 5000);
<?php }?>
<?php if(Session::has('error_mes')){?>
setTimeout(function(){ $(".alert-danger").hide(); }, 5000);
<?php }?>
jQuery(document).ready(function(){  
	
	//Check Validation
    jQuery('#profileUpdate').click(function(){
					var name = jQuery('input[name="name"]').val();
					var email = jQuery('input[name="email"]').val();
					var mobile = jQuery('input[name="Phone_no"]').val();
					var address1 = jQuery('input[name="address1"]').val();
					var address2 = jQuery('input[name="address2"]').val();
					var zip = jQuery('input[name="zip"]').val();
		
		if(name == ''){
      jQuery('.errormsg').html('');
						jQuery('.nameerrormsg').html('Please enter name in english');
						jQuery( "input[name='name']" ).focus();
						return false;
		}
		
	
	
		if(email == ''){
						jQuery('.errormsg').html('');
						jQuery('.emailerrormsg').html('Please enter email');
						jQuery( "input[name='email']" ).focus();
						return false;
		}
		if(IsEmail(email)==false){
          jQuery('.errormsg').html('');
								  jQuery('.emailerrormsg').html('Please enter valid email');
								  jQuery( "input[name='email']" ).focus();
          return false;
        }
		if(address1 == ''){
									jQuery('.errormsg').html('');
									jQuery('.address1_noerrormsg').html('Please enter address');
									jQuery( "input[name='address1']" ).focus();
									return false;
		}
		if(address2 == ''){
									jQuery('.errormsg').html('');
									jQuery('.address2_noerrormsg').html('Please enter address');
									jQuery( "input[name='address2']" ).focus();
									return false;
		}
		if(zip == ''){
									jQuery('.errormsg').html('');
									jQuery('.zip_noerrormsg').html('Please enter zip code');
									jQuery( "input[name='zip']" ).focus();
									return false;
		}
		if(zip.length!=6){
			jQuery('.errormsg').html('');
			jQuery('.zip_noerrormsg').html('Zip Code must be 6 digits');
			jQuery( "input[name='zip']" ).focus();
			return false;
		}
	 
	
		
	});
	jQuery("#Phone_no").keypress(function (e) {
		//if the letter is not digit then display error and don't type anything
		   var length = jQuery(this).val().length;
		   if(length > 9) {
				return false;
		   } else if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				jQuery('.errormsg').html('');
				jQuery('.Phone_noerrormsg').html('Digits Only').show().fadeOut("slow");
				jQuery( "input[name='Phone_no']" ).focus();
				return false;
		   } else if((length == 0) && (e.which == 48)) {
				return false;
		   }
    });
});
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}
</script>
@endsection