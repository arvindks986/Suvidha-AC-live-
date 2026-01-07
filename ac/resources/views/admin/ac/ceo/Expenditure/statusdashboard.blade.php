@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Dashboard')
@section('description', '')
@section('content')
<?php

$cons_no=!empty($cons_no) ? $cons_no : '0';

//dd($cons_no);
$st=getstatebystatecode($user_data->st_code);
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$distdetails=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
$distName=!empty($distdetails) ? $distdetails->DIST_NAME : 'ALL';

$acdetails=getacbyacno($user_data->st_code,$cons_no);
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
?>
<main role="main" class="inner cover mb-3">
    <div class="card-header pt-3" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center pt-2 pb-1">
                <div class="col-sm-12"><h4><b> CEO ELECTION EXPENDITURE MONITORING SYSTEM GENERAL AC ELECTION-2021</b></h4></div>
				<div class="col-sm-12 mt-3">
               <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/acceo/statusExpdashboard')}}" id="EcidashboardFilter">           
                 <div class="row justify-content-center">
                    {{ csrf_field() }}
					       	<div class="col-sm-3">
                  <label for="" class="mr-3">Select AC</label>    
                  <select name="ac" id="ac" class="consttype form-control" >
                    <option value="">-- All AC --</option>
                    @php $all_ac = getacbystate($user_data->st_code);



                     @endphp
                    @foreach($all_ac as $getAc)
                    @if ($cons_no == $getAc->AC_NO)
                      <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                      @else
                      <option value="{{ $getAc->AC_NO }}">{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                    @endif
								@endforeach 
							</select>
					    @if ($errors->has('ac'))
                  		  <span style="color:red;">{{ $errors->first('ac') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-1 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
            	</p>
                        </div>
                    </div>
                </form> 
                 <!--FILTER ENDS HERE-->
				</div> 
            </div> 
        </div>
    </div>
   <section class="breadcrumb-section">
	<div class="container-fluid">
		<div class="row">
		 <div class="col">
		  <ul id="breadcrumb" class="pt-1">
			<li><a href="#">EEMS-Election Expenditure Monitoring System (Displayed in %)</a></li>
		  </ul>
		 </div>
     <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>AC:</b> <span class="badge badge-info">{{$acName}}</span>
                        <!-- <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button> -->
									       
                    </p></div>
		 </div>
	</div>
  </section>
<section class="statistics color-grey pt-2 pb-5">
        <div class="container-fluid">
          <!-- EEMS box Row 1 -->
          <div class="row d-flex mb-2">
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/acceo/pendingdataentry/{{base64_encode($cons_no)}}" target="">
				<div class="feature"><img src="{{ asset('admintheme/img/icon/dataEntry-s.png') }}" alt="" /> </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_pendingdatacount }} %</div>
                <p><strong class="text-primary">Not filed ({{$pendingdatacount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
			     	</a>
              </div>
            </div>
		
          
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			      	<a href="{{url('/')}}/acceo/filedData/{{base64_encode($cons_no)}}" target="">
			    	<div class="feature">
			     	<img src="{{ asset('admintheme/img/icon/exp-icon-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">{{ $Percent_finaldatacount }} %</div>
                <p><strong class="text-primary">Filed Data ({{$finaldatacount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
               </a>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			         <a href="{{url('/')}}/acceo/defaulter/{{base64_encode($cons_no)}}" target="">
			      	<div class="feature"><img src="{{ asset('admintheme/img/icon/noTime-s.png') }}" alt="" /> </div>
                <div class="number text-success mb-1 mt-4">{{ $Percent_defaultercount }} %</div>
			        	<p><strong class="text-primary">Default Account ({{$defaultercount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>  

              <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			      	<a href="{{url('/')}}/acceo/partiallypending/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/accLodged-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_partiallypendingcount }} %</div>
			         	<p><strong class="text-primary">Pending At DEO ({{$partiallypendingcount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>      
    <!-- End of EEMS box Row 1 -->
    <!-- EEMS box Row 2 -->
	<!-- Start Notice at CEO -->
	<div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/acceo/noticeatceo/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/defectFormat-s.png') }}" alt="" />            
                </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_noticeatceocount }}%</div>
				<p><strong class="text-primary">Notice At CEO ({{ $noticeatceocount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
	<!-- End Notice at CEO -->
   <!-- <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/acceo/finalbyceo/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/defectFormat-s.png') }}" alt="" />            
                </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_finalbyceocount }}%</div>
				<p><strong class="text-primary">Pending At CEO ({{ $finalbyceocount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
              <!-- RO Not Agreee start--
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="#" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/notAgree-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">23.5%</div>
				<p><strong class="text-primary">RO not Agree</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
 <!-- RO Not Agreee start--
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/acceo/finalbyeci/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_finalbyecicount}} %</div>
				       <p><strong class="text-primary">Pending At ECI ({{ $finalbyecicount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
            <!---Data entry defect--
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="#" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/dataDefect-s.png') }}" alt="" />            
                </div>
                <div class="number text-success mb-1 mt-4">43.7%</div>
				<p><strong class="text-primary">Data entry defects</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> -->
            <!----Data entry defects end--->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/acceo/return/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_returncount}} %</div>
                
				       <p><strong class="text-primary">Return({{ $returncount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
            <!--- Data return end -->
               <!--- Data non return -->
          <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/acceo/non-return/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_nonreturncount}} %</div>
                
				       <p><strong class="text-primary">Non-Return({{ $nonreturncount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
    <!-- End of EEMS box Row 2 -->
     
     </div> 
    </div>
 </section>
</main>
@endsection