	@extends('admin.layouts.ac.theme')
	@section('title', 'Officer-details')
	@section('bradcome', 'Officer-Profile')
	@section('description', '')
	@section('content') 
	<main role="main" class="inner cover mb-3">
	
	<?php //dd(); ?>
	
	
		<section>	 
			<form enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('eci/officer-profile-update') }}" >
				{{ csrf_field() }}
				<div class="container">
					<div class="row">
						<div class="card text-left" style="width:100%; margin:0 auto;">
							<div class="card-header">
								<div class="row">
									<div class="col"><h4>Profile Details ({{$getofficerdetails->st_code}} - {{$getofficerdetails->placename}})</h4></div> 
									
								</div> <!--End row-->
							</div><!--End card-header-->
							@if ($errors->any())
							<div class="alert alert-danger">
								<ul>@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
							@endif
       @if(Session::has('success_mes'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_mes')) }}</strong> 
              </div>
          @endif

							<div class="container p-0"> 
								<div class="row">
									<div class="col-md-12">
										<div class="card">
											<div class="card-body">

												<div class="form-group row">
													<label class="col-sm-3">Name<sup>*</sup></label>
													<div class="col">
														<input id="name" type="text" class="form-control" name="name" value="{{ $getofficerdetails->name }}">
														@if ($errors->has('name'))
														<span class="help-block" style="color:red;">
															<strong> {{ $errors->first('name') }}</strong></span>
															@endif
														</div>
													</div>
													<div class="nameerrormsg errormsg errorred"></div>
													<div class="line"></div>


													<div class="form-group row">
														<label class="col-sm-3">Designation<sup>*</sup></label>
														<div class="col">
															<input id="designation" type="text" class="form-control" name="designation" value="{{ $getofficerdetails->designation }}" readonly>
															@if ($errors->has('designation'))
															<span class="help-block" style="color:red;">
																<strong> {{ $errors->first('designation') }}</strong></span>
																@endif
															</div>
														</div>
														<div class="currenterrormsg errormsg errorred"></div>
														<div class="line"></div>

														<div class="form-group row">
															<label class="col-sm-3">Email<sup>*</sup></label>
															<div class="col">
																<input id="email" type="text" class="form-control" name="email" value="{{ $getofficerdetails->email }}">
																@if ($errors->has('email'))
																<span class="help-block" style="color:red;">
																	<strong> {{ $errors->first('email') }} </strong> </span>
																	@endif
																</div>
															</div>
															<div class="emailerrormsg errormsg errorred"></div>
															<div class="line"></div>

															 <div class="form-group row">
																		<label class="col-sm-3">Phone<sup>*</sup></label>
																		<div class="col">
																			<input id="Phone_no" type="text" class="form-control" name="Phone_no" value="{{ $getofficerdetails->Phone_no }}">
																			@if ($errors->has('Phone_no'))
																			<span class="help-block" style="color:red;">
																				<strong> {{ $errors->first('Phone_no') }} </strong>
																			</span>
																			@endif
																		</div>
																	</div>
																	<div class="Phone_noerrormsg errormsg errorred"></div>
																	<div class="line"></div>

                 <div class="form-group row">
                  <label class="col-sm-3">Address Line1 </label>
                  <div class="col">
                   <input id="address1" type="text" class="form-control" name="address1" value="{{ $getofficerdetails->ro_address_l1 }}">
                   @if ($errors->has('address1'))
                   <span class="help-block" style="color:red;">
                    <strong>{{ $errors->first('address1') }}  </strong>
                   </span>
                   @endif
                  </div>
                 </div>
                 <div class="address1_noerrormsg errormsg errorred"></div>
                 <div class="line"></div>

                 <div class="form-group row">
                  <label class="col-sm-3">Address Line2 </label>
                  <div class="col">
                   <input id="address2" type="text" class="form-control" name="address2" value="{{ $getofficerdetails->ro_address_l2 }}">
                   @if ($errors->has('address2'))
                   <span class="help-block" style="color:red;">
                    <strong>{{ $errors->first('address2') }}  </strong>
                   </span>
                   @endif
                  </div>
                 </div>
                 <div class="address2_noerrormsg errormsg errorred"></div>
                 <div class="line"></div>
                 <div class="form-group row">
                  <label class="col-sm-3">Address Zip Code</label>
                  <div class="col">
                   <input id="zip" type="text" class="form-control" name="zip" maxlength="6" value="{{($getofficerdetails->ro_address_pin_code) ?  $getofficerdetails->ro_address_pin_code : '' }}">
                   @if ($errors->first('zip'))
                   <span class="help-block" style="color:red;">
                    <strong>{{ $errors->first('zip') }}  </strong>
                   </span>
                   @endif
                  </div>
                 </div>
                 <div class="zip_noerrormsg errormsg errorred"></div>
                 <div class="line"></div>
				<div class="form-group row float-right">     
					<div class="col">
						<button type="submit" id="profileUpdate" name="profileUpdate" value={{ $getofficerdetails->id}} class="btn btn-primary">Submit</button>
						<a href="{{ url('eci/ceo-profile-details')}}" class="btn btn-warning">Cancel</a>
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
			if(mobile == ''){
				jQuery('.errormsg').html('');
				jQuery('.Phone_noerrormsg').html('Please enter valid mobile number');
				jQuery( "input[name='Phone_no']" ).focus();
				return false;
			}
   
  /*  if(address1 == ''){
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
  } */
			
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