@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
 <?php  
$st=getstatebystatecode($user_data->st_code);
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acdetails=getacbyacno($user_data->st_code,$user_data->ac_no); 
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
//dd($pcdetails);
    ?>
<main role="main" class="inner cover mb-3">
    <div class="card-header pt-3" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center pt-2 pb-1">
                <div class="col-sm-12"><h4><b> DEO ELECTION EXPENDITURE MONITORING SYSTEM GENERAL AC ELECTION-2021</b></h4></div>
            </div> 
        </div>
    </div>
   <section class="breadcrumb-section">
	<div class="container-fluid">
		<div class="row">
		 <div class="col">
		  <ul id="breadcrumb" class="pt-1">
			<li><a href="#">DEO-Election Expenditure Monitoring System (Displayed in %)</a></li>
		  </ul>
		 </div>
     <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>District Name:</b> <span class="badge badge-info">{{ $distname->DIST_NAME}}</span>
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
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
                <a href="{{url('/')}}/acdeo/pendingdataentry" target="">
                <div class="feature"><img src="{{ asset('admintheme/img/icon/dataEntry-s.png') }}" alt="" /></div>
                  <div class="number text-danger mb-1 mt-4">{{ $Percent_pendingdataentrycount }} % </div>
                  <p><strong class="text-primary">Pending / Not Filed ({{$pendingdataentrycount}})</strong></p>
                  <p class="mb-2 mt-4">
                  <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                  </p>
                </a>
                </div>
            </div>

			 <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/acdeo/filedData" target="">
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
          <div class="card cardNew income reportBox text-center mt-5"> <a href="{{url('/')}}/acdeo/defaulter" target="">
			       	<div class="feature"><img src="{{ asset('admintheme/img/icon/noTime-s.png') }}" alt="" /> </div>
                <div class="number text-success mb-1 mt-4">{{ $Percent_defaultercount }} %</div>
			        	<p><strong class="text-primary">Default Account({{$defaultercount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>    
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			           	<a href="{{url('/')}}/acdeo/partiallypending" target="">
			    	   <div class="feature"><img src="{{ asset('admintheme/img/icon/accLodged-s.png') }}" alt="" /> </div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_partiallypendingcount }} %</div>
			        	<p><strong class="text-primary"> Pending At DEO ({{$partiallypendingcount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>    
    <!-- End of EEMS box Row 1 -->
    <!-- EEMS box Row 2 -->
    <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/acdeo/finalbyceo" target="">
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
 <!-- RO Not Agreee start-->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/acdeo/finalbyeci" target="">
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
            </div> 
            <!----Data entry defects end--->
    <!-- End of EEMS box Row 2 -->
     
     </div> 
    </div>
 </section>
</main>
@endsection