@extends('layouts.theme') @section('title', 'Affidavit e-File') @section('content')
<style type="text/css">
    .error {
        font-size: 12px;
        color: red;
    }
    .step-wrap.mt-4 ul li {
        margin-bottom: 21px;
    }
</style>

<link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet" />
<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet" />

<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
<main role="main" class="inner cover mb-3">     
    <section>
        <div class="container pt-3">
            <div class="row">
                <div class="card">
                    <div class="tab-content">
		  <div id="nomin" class="tab-pane active">
			<div class="header-title">
				<div class="row align-items-center">
					<div class="col-12">
						
						<h4>{{Lang::get('affidavit.affidavit')}} <a href="{{ url()->previous() }}" class="btn btn-default float-right"> {{Lang::get('affidavit.back')}} </a></h4> 
						
					</div> 
				</div>
			</div>
		 <div class="affidavitId">
		  <div class="tab-body">
		  	 <div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="{{ route('affidavit.dashboard') }}">{{Lang::get('affidavit.file')}} <br />{{Lang::get('affidavit.e_affidavit')}}</a>
						   <div class="help-txt">{{Lang::get('affidavit.here_you_can_apply_for_new_affidavit_application')}}</div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="{{ route('my.affidavit') }}">{{Lang::get('affidavit.my')}}<br>{{Lang::get('affidavit.e_affidavit_s')}}</a>
						   <div class="help-txt">{{Lang::get('affidavit.all_your_saved_and_submitted_applications_are_listed_here')}} </div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
		  </div><!-- End Of nomin Div -->  
	  </div>
		</div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection @section('script')
@endsection
