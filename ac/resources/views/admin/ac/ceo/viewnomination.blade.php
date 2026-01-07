@extends('admin.layouts.ac.dashboard-theme')
@section('content')
 <link rel="stylesheet" href="{{ asset('theme/css/nomination.css') }}" id="theme-stylesheet">
	<?php
		$getDetails =getacbyacno($nomDetails->st_code,$nomDetails->ac_no);
		//dd($nomDetails);
		$partyd= getpartybyid($nomDetails->party_id);   
		$symb= getsymbolbyid($nomDetails->symbol_id);
		$st= getstatebystatecode($persoanlDetails->candidate_residence_stcode);
		$dist= getdistrictbydistrictno($persoanlDetails->candidate_residence_stcode,$persoanlDetails->candidate_residence_districtno);
		$ac= getacbyacno($persoanlDetails->candidate_residence_stcode,$persoanlDetails->candidate_residence_acno);
	 	$url = URL::to("/");  
	 	// dd($st);  // 
	?>
<main role="main" class="inner cover mb-3">
  
	  <section class="mt-5">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
					  <h4>View Candidate Information</h4>
					</div>
					<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="avatar-upload">
								 
								@if($persoanlDetails->cand_image != '' )
									<div class="avatar-preview">
										<div id="imagePreview">
											<img src="{{url($persoanlDetails->cand_image)}}" height="180" width="180"/>
										</div>
									</div>
								@else
									<div class="avatar-preview">
										<div id="imagePreview"></div>
									</div>
								@endif
								 
							</div>
							 
						</div>
						<div class="col">                  
						<div class="form-group row mt-5">
						  <label class="col-sm-4">Party Name </label>
						  <div class="col-sm-8"> 
								<div class="" style="width:100%;"> @if(isset($partyd)){{ $partyd->PARTYNAME }} @endif</div>
						  </div>
						</div>
						<?php   //dd($symb); ?>
						<div class="form-group row">
						  <label class="col-sm-4">Symbol </label>
						  <div class="col-sm-8">
							 <div class="" style="width:100%;">@if(isset($symb)){{$symb->SYMBOL_DES}}@else  No Symbol @endif </div>
						  </div>
						</div>
					 
					  </div>
					</div>
					</div>
				</div>
				</div>
			</div>
		</div>	  
	  </section>
	  <section class="">
		<div class="container">
			<div class="row">
			
				<div class="col-md-12">
				<div class="card">
                <div class="card-header d-flex align-items-center">
                  <h4>Candidate Personal Details</h4>
                </div>
                <div class="card-body">
				<div class="row">
				
					<div class="col">                  
                  <form class="form-horizontal">
                    <div class="form-group row">
                      <label class="col-sm-3">Name</label>
                      <div class="col">{{$persoanlDetails->cand_name}}  </div>  
					   <div class="col">{{$persoanlDetails->cand_hname}} </div>
					   <div class="col">{{$persoanlDetails->cand_vname}} </div>
                    </div>
				<div class="form-group row">
                      <label class="col-sm-3">Candidate Alias Name </label>
                      <div class="col">{{$persoanlDetails->cand_alias_name}}  </div>  
					   <label class="col-sm-3">Hindi</label><div class="col">{{$persoanlDetails->cand_alias_hname}} </div>
						  
                    </div>
					
					<div class="form-group row">
                      <label class="col-sm-3">Father's / Husband's Name </label>
                      <div class="col">{{$persoanlDetails->candidate_father_name}}  </div>  
					   <label class="col-sm-3">Hindi</label><div class="col">{{$persoanlDetails->cand_fhname}} </div>
						 
                    </div>
					 
					
					<div class="form-group row">
						<label class="col-sm-3">Email </label>
                        <div class="col">{{$persoanlDetails->cand_email}} </div>
							 
					    <label class="col-sm-3">Mobile No </label>
						 <div class="col">{{$persoanlDetails->cand_mobile}} </div>
							 
                    </div>
					
					
					<div class="form-group row">
                      <label class="col-sm-3">Gender </label>
       				  <div class="col">{{$persoanlDetails->cand_gender}} </div>
               
					 <label class="col-sm-3">PAN Number </label>
						<div class="col">{{$persoanlDetails->cand_panno}} </div>
						 
					</div>
						<div class="form-group row">
							<!--<label class="col-sm-3">Date of Birth </label>
							<div class="col">{{$persoanlDetails->cand_dob}} </div>-->
							 
							<label class="col-sm-3">Age </label>
							<div class="col">{{$persoanlDetails->cand_age}} </div>
						</div>
                    
			 							 
				    <div class="form-group row">
                      <label class="col-sm-3">Address</label>
                      <div class="col">{{$persoanlDetails->candidate_residence_address}} </div>
                      <label class="col-sm-3">Address In Hindi </label>  
					  <div class="col">{{$persoanlDetails->candidate_residence_addressh}} </div>
						 
                    </div>
					 
					 
				  
				  <div class="form-group row">
					<div class="col-sm-3"><label for="statename">State Name </label></div>
					<div class="col">{{$st->ST_NAME}} </div>
						 
					<div class="col-sm-3"><label for="statename">District </label></div>
					<div class="col">{{$dist->DIST_NAME}} </div>
					
				  </div> 
				  <div class="form-group row">
				  
					 <div class="col-sm-3"><label for="statename">AC </label></div>
					  <div class="col">{{$ac->AC_NAME}} </div>
						 
					 
				  
					<div class="col-sm-3"><label for="statename">Category </label></div>
				 		<div class="col">{{$persoanlDetails->cand_category}} </div>
							 
						 
					</div> 
				 
				 <div class="form-group row float-right">       
					  <div class="col">
					  	<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
						 
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