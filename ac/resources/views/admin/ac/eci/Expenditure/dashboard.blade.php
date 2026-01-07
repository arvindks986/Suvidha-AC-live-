@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Dashboard')
@section('description', '')
@section('content')
@php
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
 // echo $st_code.'cons_no'.$cons_no; die;
@endphp 
<main role="main" class="inner cover mb-3">
    <div class="card-header pt-3" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center pt-2 pb-1">
                <div class="col-sm-12"><h4><b> ECI ELECTION EXPENDITURE MONITORING SYSTEM GENERAL AC ELECTION- {{ session()->get('DB_YEAR') }}</b>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:window.print()"> 
                           <i class="fa fa-print"></i>  </a>

                </h4></div>
				         <div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/eci-expenditure/expdashboard')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                    {{ csrf_field() }}
                      <!--STATE LIST DROPDOWN STARTS-->
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select State</label>    
                        <select name="state" id="state" class="form-control">
                        <?php if($stateName=='ALL') { ?> <option value="">All States</option> <?php } ?>
                      @foreach ($statelist as $state_List ))
                        @if ($st_code == $state_List->ST_CODE)
                              <option value="{{ $state_List->ST_CODE }}" selected>{{$state_List->ST_NAME}}</option>
                        @else
                              <option value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>
                        @endif
                      @endforeach

                      @if ($errors->has('state'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('state') }}</strong>
                      </span>
                      @endif
                      <div class="stateerrormsg errormsg errorred"></div>
                  </select> 
                        </div>
                           <!--STATE LIST DROPDOWN ENDS-->
					       	<div class="col-sm-3">
                        <label for="" class="mr-3">Select AC</label>    
                        <select name="ac" id="ac" class="consttype form-control" >
								<option value="">-- All AC --</option>
                @if (!empty($all_ac))
                <?php //dd($all_pc);?>
								@foreach($all_ac as $getAc)
								 @if ($cons_no ==  $getAc->AC_NO)
                              <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NO }} - {{$getAc->AC_NAME }}</option>
                              @else
									<option value="{{ $getAc->AC_NO }}" > 
									{{$getAc->AC_NO }} - {{$getAc->AC_NAME }}</option>
									 @endif
								@endforeach 
                @endif
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
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
                <div class="feature"><img src="{{ asset('admintheme/img/icon/dataEntry-s.png') }}" alt="" /></div>
                  <div class="number mb-3 mt-3">
                    <a href="{{url('/')}}/eci-expenditure/analytic-summary/dataentry" target="" style="font-size: 17px;" class="text-danger">{{ $Percent_startdataentry }} % Data entry started</a>
					<p style="font-size: 13px; font-weight: bold;">({{ $startdatacount }} /{{$totalContestedCandidate}})</p>
					
                    <a href="{{url('/')}}/eci-expenditure/analytic-summary/finalize" target="" style="font-size: 17px;" class="text-info">{{ $Percent_finaldatacount }} % Report Finalised</a>
					<p style="font-size: 13px; font-weight: bold;">({{ $finaldatacount }} /{{$totalContestedCandidate}})</p>
                  </div>
                </div>
            </div>
           
			<!-- <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income--
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/eci-expenditure/finalizeData/{{$st_code}}/{{$cons_no}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/exp-icon-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">{{ $Percent_finaldatacount }} %</div>
                <p><strong class="text-primary">Report Finalised ({{ $finaldatacount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
               </a>
              </div>
            </div>-->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/eci-expenditure/analytic-summary/logedaccount" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/accLodged-s.png') }}" alt="" />            
                </div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_logedaccount }} %</div>
				<p><strong class="text-primary">Account Lodged <br/>
				<span style="font-size: 13px;">({{ $logedaccount }} /{{$totalContestedCandidate}})</span></strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
			   <a href="{{url('/')}}/eci-expenditure/analytic-summary/notintime" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/noTime-s.png') }}" alt="" />            
                </div>
                <div class="number text-success mb-1 mt-4">{{ $Percent_notintimeaccount }} %</div>
				<p><strong class="text-primary">Not in Time  <br/>
				<span style="font-size: 13px;">({{ $notintimeaccount }} /{{$totalContestedCandidate}})</span></strong></p>
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
				<a href="{{url('/')}}/eci-expenditure/analytic-summary/formatedefects" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/defectFormat-s.png') }}" alt="" />            
                </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_formateDefectscount }}%</div>
				<p><strong class="text-primary">Defect in account <br/>
				<span style="font-size: 13px;">({{ $formateDefectscount }} /{{$totalContestedCandidate}})</span></strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
			<!---RO Not Agree----
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
            </div> RO Not Agree end------->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/eci-expenditure/analytic-summary/understatedexpense" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" />            
                </div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_expenseunderstated}} %</div>
				<p><strong class="text-primary">Expenses understated <br/>
				<span style="font-size: 13px;">({{ $expenseunderstated }} /{{$totalContestedCandidate}})</span></strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
			<!---Data entry defects---
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
            </div> ------------data entry defects end--->
    <!-- End of EEMS box Row 2 -->
     <!-- EEMS box Row 3 -->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/eci-expenditure/analytic-summary/partyfund" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/fundParty-s.png') }}" alt="" />            
                </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_partyFund}} %</div>
				<p><strong class="text-primary">Taken funds from party</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/eci-expenditure/analytic-summary/othersfund" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/fundOther-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">{{ $Percent_OthersourcesFund}} %</div>
                <p><strong class="text-primary">Taken funds from other sources</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
              </div>
            </div> 
			<!------Exceed ceiling start---
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="#" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/ceilingAmount-s.png') }}" alt="" />            
                </div>
                <div class="number text-warning mb-1 mt-4">11.9%</div>
				<p><strong class="text-primary">Exceed the Ceiling amount</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> ---Exceed ceiling end--------->
                 <!--- Data return -->
          <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/eci-expenditure/analytic-summary/return" target="">
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
			        	<a href="{{url('/')}}/eci-expenditure/analytic-summary/non-return" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_nonreturncount}} %</div>
				       <p><strong class="text-primary">Non-Return({{ $nonreturncount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 

          <?php
            $st_code = ($st_code=='0')?'':$st_code;
            $cons_no = ($cons_no=='0')?'':$cons_no;  
            ?>
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
                <a href="{{url('/')}}/eci-expenditure/getPartyWiseExpenditure?state={{$st_code}}&pc={{$cons_no}}" target="">
                <div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div><br><br><br>
<!--                 <div class="number text-warning mb-1 mt-4">{{ $Percent_nonreturncount}} %</div>
 -->               <p><strong class="text-primary">Party Wise Expenditure</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a> 
              </div>
            </div> 


             <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
                <a href="{{url('/')}}/eci-expenditure/candidate_wise_expenditure?state={{$st_code}}&pc={{$cons_no}}" target="">
                <div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div><br><br><br>
<!--                 <div class="number text-warning mb-1 mt-4">{{ $Percent_nonreturncount}} %</div>
 -->               <p><strong class="text-primary">Candidate Wise Expenditure</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
               <!--end return/ non return-->
    <!-- End of EEMS box Row 3 -->
     </div> 
    </div>
 </section>
</main>
@endsection

@section('script')

<script>
jQuery(document).ready(function(){ 
	jQuery("select[name='state']").change(function(){
		var state = jQuery(this).val();  
       // alert(state);
        jQuery.ajax({ 
        	url: '<?php echo url('/') ?>/eci-expenditure/getacbystate',
            type: 'GET',
            data: {state:state},
         
            success: function(result){  
				//console.log(result); 
                var stateselect = jQuery('form select[name=ac]');
                stateselect.empty();
                var achtml = '';
                achtml = achtml + '<option value="">-- All AC --</option> ';
                jQuery.each(result,function(key, value) { 
                    achtml = achtml + '<option value="'+value.AC_NO+'">'+value.AC_NO+' - '+value.AC_NAME + '</option>';
                    jQuery("select[name='ac']").html(achtml);
                });
                var achtml_end = '';
                jQuery("select[name='ac']").append(achtml_end)
            }
        });
    });
	/*
	//Check Validation
    jQuery('#psinfo').click(function(){
		var distt = jQuery('select[name="state"]').val();
		var acname = jQuery('select[name="pc"]').val();
		
		if(distt == ''){
			jQuery('.errormsg').html('');
			jQuery('.stateerrormsg').html('Please select district');
			jQuery( "input[name='district']" ).focus();
			return false;
		}
		if(acname == ''){
            jQuery('.errormsg').html('');
			jQuery('.acerrormsg').html('Please select ac');
			jQuery( "input[name='ac']" ).focus();
			return false;
		}
	});
  */
	
});

</script>
@endsection