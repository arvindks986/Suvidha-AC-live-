<?php header('Access-Control-Allow-Origin: *'); //dd(1234);  ?>
 @extends('layouts.theme')
  @section('title', 'Nomination')
  @section('content')
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
  
  
<style>

#faq-From-div ul li {
color: blue;
font-size: 15px;
font-weight: 700;
list-style: decimal;
padding-top: 10px;
width: 50%;
}

#faq-From-div ul li a {
color: blue;
font-size: 15px;
font-weight: 700;
list-style: decimal;
text-decoration: none;
}
#Answer {
width: 815px;
height: auto;
margin: 0 auto 0 0;
border: 1px #0F0F0A dotted;
color: #272727;
font-size: 15px;
font-weight: 400;
text-align: justify;
padding: 5px;
}

.pay-slct-pge{margin: 10rem 0 0 0;}
.pay-slct-pge .panel-heading{font-size: 21px; color: #1c6e91;}
.pay-slct-pge .panel-body{padding: 35px;}
table.Payment td {padding: 0 0 0 1rem;}
.pay-slct-pge .panel-default>.panel-heading {background-color: #ededed;}
.razorpay-payment-button {
    padding: 0.5rem 2rem;
    background-color: #06518b;
    border: 1px solid #06518b;
    color: #fff;
}
#resizeableDive .panel{box-shadow: none; -webkit-box-shadow: none; margin: 8.5rem 0 0 0;}
.Refresh h5{color: #12485e; text-transform: capitalize; text-align: center;}
.razorpay-payment-button:hover,
.razorpay-payment-button:focus,a.btn.bck-btn:hover,a.btn.bck-btn:focus {background-color: #046fc1; color: #fff; border-color: #046fc1;}
a.btn.bck-btn {border: 1px solid #d7d7d7; background-color: #f1f1f1; border-radius: 0; padding: 1rem 2.5rem; margin: 0 0 0 1rem; }
.no-pay {text-align: center; margin: 5rem 0 1.5rem 0; color: #1c6e91; line-height: 28px; font-weight: 400; font-size: 15px;}
.mchild {
 width: 50%;
}
</style>
  
   <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
	
		
    <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
	
	
   <title>Schedule Appointment</title>
   <script>
    var abc=[];
   </script>
  </head>
  <body>
   <main class="pt-3 pb-5 pl-5 pr-5">
	  <section>
	@if(count($errors->all())>0)
		  <div class="container" style="padding: 16px;">
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $iterate_error)
              <li><p class="text-left">{!! $iterate_error !!}</p></li>
              @endforeach
            </ul>
          </div>
		  </div>    	
    @endif
	<!--	  
	@if (session('flash-message'))
		<div class="container">
			<div class="row" style="padding: 16px;">
           @if (session('flash-message'))
           <div class="alert alert-success"> {{session('flash-message') }}</div>
           @endif
		</div>    
    @endif -->
    </section>	
	<?php  
	 //Session::flash('is_payment',"yes");
	//Session::flash('is_payment',"yes"); //echo $is_appoinment_scheduled_for_one; die; ?>
	@if($is_appoinment_scheduled_for_one==1)
		@php 
		$disp='none';
		$disp2='block';
		@endphp
	@else 
		@php 
		$disp='block';
		$disp2='none';
		@endphp
	@endif
	
	
	
	
	 <div class="container-fluid" style="display:{{$disp}}" id="call">
	   <div class="card">
		 <div class="card-header">
		   <div class="row">
			<div class="col-md-6 col-12"><h4>{{ __('messages.Preference') }}</h4> </div> 
			<div class="col-md-6 col-12"><div id="dmy" class="text-right"></div></div> 
		   </div>
		    <span style="margin-left: 41em;margin-top: 16px; font-size: 13px; color: black;cursor:pointer;font-weight: bold;">
			@if($nomiantaion_end_date!=0)
			{{ __('messages.lastdate') }} 
			{{date("D d, M Y", strtotime($nomiantaion_end_date))}}  
			@endif
			</span>	
		   @if($is_appoinment_scheduled_for_one==1)
		   <span style="float: right; margin-top: 16px; font-size: 13px; color: gray;cursor:pointer;" onclick="return showhide2();">{{ __('messages.Preference_Details') }}</span>	 
		   @endif	
		 </div> 
		 <div class="card-body">
		   <div class="row">
			<div class="col-md-4 col-12 pr-2">
			  <div class="nomin-list" style="height: 356px;">
			  <div class="owl-carousel owl-theme">
			  
			  <?php 
			  
				$challanno ='';
			  
			  
			    $d=1;
			    if(isset($_REQUEST['id'])){ 
				$ids = explode(",", $_REQUEST['id']);
				foreach($ids as $nomid){
				$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);	
				if($datadd!="NA"){
				$str = explode("***", $datadd);
				$NOMNO=$str[0];
				$candidate_name=$str[1];
				$ACNO=$str[2];
				$ACname=$str[3];
				$appoinment_status=$str[4];
				$updated_at=$str[5];
				$view_href_cust=$str[6];
				$download_href_cust=$str[7];
				$election_name_one=$str[12];
				$state=$str[13];
				$party=$str[14];
				$std=$str[15];
				$cat=$str[16];
				
				
				//$stst=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($NOMNO);
				
				$udurl='';
				$udurl='detail/'.encrypt_String($nomid);
				
				$dwdulr='';
				$dwdulr='download/'.encrypt_String($nomid);
				
				
				
				?>
				<div class="item">
				<div class="appnt-detail list-detail">
					<h4 class="text-center d-flex justify-content-between"><b>{{$d}}</b>{{$NOMNO}}</h4>
					<ul style="line-height: 1;">
					<li><strong> {{ __('nomination.Name') }}</strong> <span>{{$candidate_name}}</span></li>
					<li><strong>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}</strong> <span>{{$ACNO}} - {{$ACname}}</span></li>
					<!--<li><strong>{{ __('nomination.Status') }}</strong> <span>{{ __('csa.Pre_Scurtiny_Done') }}</span></li>-->
					<li><strong>{{ __('nomination.State') }}</strong> <span>{{$state}}</span></li>
					<li><strong>{{ __('nomination.Election') }}</strong> <span>{{$election_name_one}}</span></li>
					<li><strong>{{ __('nomination.Party') }}</strong> <span>{{$party}}</span></li>
					<li><strong>{{ __('step1.Category') }}</strong> <span> {{strtoupper($cat)}} </span></li>
				  </ul> 
					<div class="row m-0 p-3">
					  <div class="col-md-4 col-12 p-0"><strong>{{ __('nomination.Nomination') }}</strong></div>  
					  <div class="col-md-8 col-12 p-0 text-right">
						 <div class="apt-btn">
						  <a href="{{$udurl}}?acs=<?php echo encrypt_String($ACNO); ?>&std=<?php echo encrypt_String($std); ?>" class="btn sm-btn dark-pink-btn">{{ __('nomination.View') }}</a>  
						  <a href="{{$dwdulr}}" class="btn sm-btn dark-purple-btn">{{ __('nomination.Download') }}</a>  
						</div> 
					  </div>  
					</div>
					</div><!-- End Of appnt-detail Div --> 
				</div><!-- End Of item Div -->
				<?php $d++; }}} ?>
			  </div>			   
		    </div><!-- End Of nomin-list Div --> 
			</div> 	
			<div class="col-md-8 col-12 pl-0">
			   <div class="appnt-calndr">
				   <div class="caldr-wrap frst-week m-3">
                         <div id="day-schedule"></div>
					   <div class="help-arrow">	 
						 <img src="<?php echo url('/'); ?>/appoinment/img/vendor/help-left-icon.png" height="60" width="70">
						 <span>{{ __('messages.click') }}</span>
					   </div>	 
						 <div class="d-flex justify-content-end trffic">
							 <div><span class="lowTrf"></span> Low Traffic</div>
							 <div><span class="highTrf"></span> High Traffic</div>
							 <div><span class="selectTrf"></span> Selected</div>
							 <div><span class="disableTrf"></span> Disabled</div>
						 </div>
					  </div><!-- End of frst week -->				 
			  </div><!-- End Of appnt-calndr Div -->   
			</div> 
		   </div>
		 </div>  
		 
		  <?php 
		$expd = explode(",", $_REQUEST['id']); 
		$stno='';
		$acnno='';
		$acst = app(App\Http\Controllers\Nomination\NominationController::class)->getAcStByNo($expd[0]); 
			 if(!empty($acst)){
				$entt = explode("***", $acst);
				$stno=encrypt_String($entt[0]);
				$acnno=encrypt_String($entt[1]);
			 } 
		?>	
		 
		 <div class="card-footer">
		    <div class="text-left col-md-3 col-3 pl-3" style="height:1px;">
			  <a href="{{'nominations?acs='.$acnno.'&std='.$stno}}" class="btn btn-lg font-big dark-pink-btn" style="color: white;">{{ __('step1.Back') }}</a>  
			</div> 		
			@if($is_appoinment_scheduled_for_one==1)
		   <div style="font-size: 13px; color: gray; cursor: pointer; position: absolute; margin-top: 35px; margin-left: 43em;" onclick="return showhide2();">{{ __('messages.Preference_Details') }}</div>	 
		   @endif	
		   <div class="apt-btn text-right">
			  @if($appoinment_status!='Cancel')
			  <!-- <a href="schedule-appointment" class="btn btn-lg font-big dark-pink-btn">CANCEL</a>  -->
			  @endif
			  <a href="#" class="btn btn-lg font-big dark-purple-btn" onclick="return submitScheduled();" id="scheduleButton">{{ __('messages.SCHEDULE_APPOINTMENT') }}</a>  
			</div> 
		 </div>  
	   </div>
	 </div>
	 
	 
	  <div class="container-fluid" style="display:{{$disp2}}" id="slip">
	   <div class="card">
		 <div class="card-header">
		   <div class="row">
			<div class="col-md-12 col-12"><h4>{{ __('messages.predetails') }}</h4> </div> 
			<div class="col-md-6 col-12"></div> 
		   </div>	 
		 </div> 
		 <div class="card-body">
		   <div class="row">
			<div class="col-md-4 col-12 pr-2">
			  <div class="nomin-list" style="height: 356px;">
			  <div class="owl-carousel owl-theme">
			  
			  <?php 
			    $d=1; $std= 0; $ACNO=0;
			    if(isset($_REQUEST['id'])){ 
				$ids = explode(",", $_REQUEST['id']);
				foreach($ids as $nomid){
				$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);	
				if($datadd!="NA"){
				$str = explode("***", $datadd);
				$NOMNO=$str[0];
				$candidate_name=$str[1];
				$ACNO=$str[2];
				$ACname=$str[3];
				$appoinment_status=$str[4];
				$updated_at=$str[5];
				$view_href_cust=$str[6];
				$download_href_cust=$str[7];
				$election_name_one=$str[12];
				$state=$str[13];
				$party=$str[14];
				$std=$str[15];
				$cat=$str[16];
				//$stst=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($NOMNO); 
				
				
				$udurl1='';
				$udurl1='detail/'.encrypt_String($nomid);
				
				$dwdulr2='';
				$dwdulr2='download/'.encrypt_String($nomid);
				
				
				?>
				
				<div class="item">
				<div class="appnt-detail list-detail">
					<h4 class="text-center d-flex justify-content-between"><b>{{$d}}</b>{{$NOMNO}}</h4>
					<ul>
					<li><strong> {{ __('nomination.Name') }}</strong> <span>{{$candidate_name}}</span></li>
					<li><strong>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}</strong> <span>{{$ACNO}} - {{$ACname}}</span></li>
					
					<li><strong>{{ __('nomination.State') }}</strong> <span>{{$state}}</span></li>
					<li><strong>{{ __('nomination.Election') }}</strong> <span>{{$election_name_one}}</span></li>
					<li><strong>{{ __('nomination.Party') }}</strong> <span>{{$party}}</span></li>
					<li><strong>{{ __('step1.Category') }}</strong> <span> {{strtoupper($cat)}}</span></li>
				  </ul> 
					<div class="row m-0 p-3">
					  <div class="col-md-4 col-12 p-0"><strong>{{ __('nomination.Nomination') }}</strong></div>  
					  <div class="col-md-8 col-12 p-0 text-right">
						 <div class="apt-btn">
						  <a href="{{$udurl1}}?acs=<?php echo encrypt_String($ACNO); ?>&std=<?php echo encrypt_String($std); ?>" class="btn sm-btn dark-pink-btn">{{ __('nomination.View') }}</a>  
						  <a href="{{$dwdulr2}}" class="btn sm-btn dark-purple-btn">{{ __('nomination.DownloadnPrint') }}</a>  
						</div> 
					  </div>  
					</div>
					</div><!-- End Of appnt-detail Div --> 
				</div><!-- End Of item Div -->
				<?php $d++; }}} ?>
			  </div>			   
		    </div><!-- End Of nomin-list Div --> 
			<?php 
			 $psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus($_REQUEST['id']);
			 $isChallanSubmitted = app(App\Http\Controllers\Nomination\NominationController::class)->getChallan($std, $ACNO);
			  
			//echo count($psta) .'--'. count($isChallanSubmitted).'----'.$std.'---'.$ACNO; die;?>
					@if((count($psta)>0) or (count($isChallanSubmitted)>0))
					<?php $isAllFinalized = app(App\Http\Controllers\Nomination\NominationController::class)->isAllFinalized($std, $ACNO);	
					 //echo count($isAllFinalized); die;
					?>	
					@if(count($isAllFinalized)!=0)				
					<div class="text-center py-3"><a href="#" class="btn btn-success btn-big" id="online_pay2" onclick="return finalizeNomination('online_pay2');">{{ __('messages.fn') }}</a></div>	
			       <div class="animate-wrap" id="ttump">
				    <div class="animate-help-text-3">
					 <div class="animate-icon-3">
					      <div class="box bounce-3"><i class="fa fa-hand-o-up" aria-hidden="true"></i></div>
						</div>
					  </div><!-- End Of animate-help-text Div -->
					</div>	
				
					@else 
					<div style="text-align: center; margin-top: 21px; background: #caede3;" class="text-center" id="online_pay3">{{ __('messages.nmf') }}</div>		
				   @endif
				   @endif
		   <div style="text-align: center; margin-top: 21px; background: #caede3;display:none;"  id="NeedToShowSucceess" >{{ __('messages.nmf') }}</div>
			</div> 
			
				
			
			<div class="col-md-8 col-12 pl-0">
			   <div class="appnt-calndr" style="height:608px;">
				   <div class="caldr-wrap frst-week m-3">
                       <div class="booked-appoint">
					     <div class="book-header">
						  <div class="d-flex justify-content-between">
							<div>
							  <ul class="list-inline" style="background: #caede3; margin-right: 40px; width: 328%; height: 50px; margin-bottom: 36px; padding-bottom: 6px; margin-left: -17px; margin-top: -16px;">
								<li class="list-inline-item mr-4" style="margin-left: 30px; margin-top: -8px;">&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; <br>{{ __('messages.predetails2') }}</li> 
								</ul>
								
							 
							<?php $ppArray = app(App\Http\Controllers\Nomination\NominationController::class)->getschedule_appoinment($ACNO, $std);	 
							if(count($ppArray) > 0 ) {
							foreach($ppArray as $dataaad){
							
							?>  
							  
							 <ul class="list-inline" style="font-size: 17px;margin-bottom:10px;width: 141%;margin-left: 20px;">
								<li class="list-inline-item" style="width: 250px;"><span class="list-bg"><b class="circle-icon"><i class="fa fa-calendar" aria-hidden="true"></i></b>&nbsp; {{date("D d, M Y", strtotime($dataaad->appointment_date))}}   </span></li>
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></b>&nbsp; {{$dataaad->appointment_time}} O'clock</span></li>
								@if($dataaad->is_ro_acccept=='1')
								<!--<li class="list-inline-item"><span class="list-bg" style="background:yellowgreen;"><b></b>&nbsp; {{ __('messages.roacc') }} </span></li>-->
								@endif
								
							</ul> 
							<?php }} ?>  	
							</div>  
							<div class="down-print-link">
							  <ul class="list-inline" style="background: #caede3; margin-right: 40px; width: 108%; margin-bottom: 36px; padding-bottom: 8px;">
								<li class="list-inline-item"><a href="download-scheduled?id=<?php echo $_REQUEST['id'];?>">{{ __('nomination.Download') }} <i class="fa fa-download" aria-hidden="true"></i></a></li>
								<!--<li class="list-inline-item"><a href="#">Print <i class="fa fa-print" aria-hidden="true"></i></a></li>-->
							  </ul>  
							</div>  
							
							
						  </div>	 
						   
						 </div><!-- End Of book-header Div -->
						 <div class="book-body p-3">
						   <div class="row">
						     <?php 
							 $acnnod=0;
							 $stndn=0;
							 if(isset($_REQUEST['id'])){ 
							$ids = explode(",", $_REQUEST['id']);
							//foreach($ids as $nomid){
							$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($ids[0]);	
						
							if($datadd!="NA"){
							$str = explode("***", $datadd);
							$NOMNO=$str[0];
							$candidate_name=$str[1];
							$ACNO=$str[2];
							$acnnod=$str[2];
							$ACname=$str[3];
							$appoinment_status=$str[4];
							$updated_at=$str[5];
							$view_href_cust=$str[6];
							$download_href_cust=$str[7];
							$ROname=$str[8];
							$ROaddress1=$str[9];
							$ROaddress2=$str[10];
							$DistName=$str[11];
							$stndn= $str[15];
							?>
							 <div class="col-md-4 col-12"><label>{{ __('csa.RO_Name') }}:</label> {{$ROname}} </div> 
							 <div class="col-md-4 col-12"><label>{{ __('nomination.ac') }} </label> {{$ACNO}} - {{$ACname}}</div> 
							 <div class="col-md-4 col-12"><label>{{ __('csa.District') }}:</label> {{$DistName}}</div> 
							 <!--<div class="col-md-12 col-12 pt-3"><label>{{ __('csa.Address') }}:</label> {{$ROaddress1}} {{$ROaddress2}}</div>
							 <div class="col-md-4 col-12"><label>Status:</label> {{$appoinment_status}}</div> -->
							<?php } } //} ?>	
							</div>	 
						   </div> 
						    <?php 
							 $expd = explode(",", $_REQUEST['id']);
							 $inr=   count($expd)*100;
							 $dmt=1000000;
							 $psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus($_REQUEST['id']);
							 $challanCnt = app(App\Http\Controllers\Nomination\NominationController::class)->getChallan($stndn, $acnnod);

							 $bank = app(App\Http\Controllers\Nomination\NominationController::class)->bankDetails();
							
							 $cEdata = app(App\Http\Controllers\Nomination\NominationController::class)->getEmail();	
							 
							 $bnk='';	
							 if(count($bank)>0){
							  $bnk=__('csa.Edit');	 
							 } else {
							  $bnk=__('csa.Enter'); 
							 }
							 
							?>	
							
							
							
							<div class="d-flex justify-content-between align-items-center p-3 payment-bg">
							

							<div class="info-text">
							  @if((count($psta)>0) or (count($challanCnt)>0))
								<span style="color:black;font-size:15px;"><b>{{ __('messages.chnmesage') }}</b></span>
								<br><br>
								@if(count($psta)>0)
								@if($psta[0]->bank_transaction_status=='2' ||  $psta[0]->bank_transaction_status=='' || $psta[0]->bank_transaction_status=='0') 
									
									@if($payState=='S03')
									<form method="post" name="getCIN" action="https://assamegras.gov.in/challan/models/frmgetgrn.php" >
									 <input type="hidden" id ="DEPARTMENT_ID" name=DEPARTMENT_ID value="<?php echo $psta[0]->reff_no;?>" />
									 <input type="hidden" id ="OFFICE_CODE" name="OFFICE_CODE" value="ELE005" />
									 <input type="hidden" id ="AMOUNT" name="AMOUNT" value="<?php echo $psta[0]->challan_amount;?>" />
									 <input type="hidden" id ="ACTION_CODE" name="ACTION_CODE" value="GETCIN"/>
									 <input type="hidden" id ="SUB_SYSTEM" name="SUB_SYSTEM" value="Election" />
									 <input type="submit" class="btn btn-primary" id ="submit" name="submit_cin" target = "_BLANK" value="Check Payment Status" />
									</form>
									<br>
									@endif
									
									@if($payState=='U07')
									<input type="button" class="btn btn-primary" id="checkPdStatus" data-grn="<?php echo $psta[0]->reff_no;?>" name="submit_cin"  value="Check Payment Status" /><span style="display:none;" id="pststicon"><b>&nbsp;&nbsp;Checking Status...</b></span>
									<br>
									@endif
									
									@if($payState=='S22')
									<input type="button" class="btn btn-primary" id="checkTmStatus" data-grn="<?php echo $psta[0]->reff_no;?>" name="submit_cin"  value="Check Payment Status" /><span style="display:none;" id="pststicon"><b>&nbsp;&nbsp;Checking Status...</b></span>
									<br>
									@endif
									@if($payState=='S11')
									<form method="post" name="getCIN" action="https://etreasury.kerala.gov.in/challan/models/frmgetgrn_dept.php" >
									 <input type="hidden" id ="DEPARTMENT_ID" name=DEPARTMENT_ID value="<?php echo $psta[0]->reff_no;?>" />
									 <input type="hidden" id ="OFFICE_CODE" name="OFFICE_CODE" value="<?php echo $office_code;?>" />
									 <input type="hidden" id ="AMOUNT" name="AMOUNT" value="<?php echo $psta[0]->challan_amount;?>" />
									 <input type="hidden" id ="SERVICE_DEPT_CODE" name="SERVICE_DEPT_CODE" value="180" />
									 <input type="hidden" id ="ENTRY_DATE" name="ENTRY_DATE" value="<?php echo date('Y-m-d',strtotime($psta[0]->created_at));?>" />
									 <input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
									</form>
									<br>
									@endif
									
									@if($payState=='S14')
										
									<?php 
									//$url_d = 'https://164.100.122.143/echallan/challan/models/frmgetgrn.php'; 
									$url_d='https://egrasmanipur.nic.in/echallan/challan/models/frmgetgrn.php';  
									?>
									
									<form method="post" name="getCIN" action="<?php echo $url_d; ?>" >
									 <input type="hidden" id ="DEPARTMENT_ID" name='DEPARTMENT_ID' value="<?php echo $psta[0]->reff_no;?>" />
									 <input type="hidden" id ="OFFICE_CODE" name="OFFICE_CODE" value="<?php echo $office_code;?>" />
									 <input type="hidden" id ="AMOUNT" name="AMOUNT" value="<?php echo $psta[0]->challan_amount;?>" />
									 <input type="hidden" id ="ENTRY_DATE" name="ENTRY_DATE" value="<?php echo date('Y-m-d',strtotime($psta[0]->created_at));?>" />
									 <input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
									</form>
									<br>
									@endif
									
									
									@if($payState=='S24')
									<form method="post" id="verification_up" name="verification_up" action="{{url('payment-verification-up')}}">
											<input type="hidden" name="challan_ref_id" id="challan_ref_id" value="{{$psta[0]->challan_ref_id}}">
											<input type="hidden" name="amount" id="amount" value="{{$psta[0]->challan_amount}}">
											<input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
										</form>
									@endif
									
									
									
									@if($payState=='S06')
									<?php	
									$key='q4UOLnbuVc0mP8Jf634f1zCGVy2pf9lj';
									$iv='q4UOLnbuVc0mP8Jf';
									$enc_method = "AES-128-CBC";

									//$rd='https://cybertreasuryuat.gujarat.gov.in/CyberTreasury_UAT/connectDept?service=DeptPortalConnection'; // UAT
									$rd=' https://cybertreasury.gujarat.gov.in/CyberTreasury/connectDept?service=DeptPortalConnection';  // Live	

									//$RU="http://localhost/suvidhaac/public/payment-gujrat-verification";	// Local
									//$RU="https://demo.eci.nic.in/suvidhaac/public/payment-gujrat-verification";	// Demo
									$RU="https://suvidha.eci.gov.in/suvidhaac/public/payment-gujrat-verification";	// Live
									

			
									$fnlStrDAta="User_id=".$candidate_id."|Transaction_id=".$psta[0]->reff_no;;
									$trd=trim($fnlStrDAta);						
									$encdata=openssl_encrypt($trd, $enc_method, $key, $options=0, $iv);								
									?>
									
									<form method="POST" name="redGuj"  action="<?php echo $rd; ?>">
									<input type="hidden" name="_token" value="{{csrf_token()}}">
									<input type="hidden" name="CTP_DATA" value="<?php echo $encdata; ?>">
									<input type="hidden" name="Dept_call" value="Recon_data">
									<input type="hidden" name="RU" value="<?php echo $RU; ?>">
									<input type="Submit" name="submit" value="Check Payment Status">
									</form>

									@endif
									
									
									
									
									@if($payState=='S28')
										
									<?php 
									$user_Pass="0070";//Static fixed value			
									$head_desc = '0070028000601';
									$amount = $psta[0]->challan_amount;									
									$depositor_name=trim($depositor_name);
									$address=trim(substr($address, 0, 90));
									$mobile=trim($mobile);
									$department_id = $reff_no;
									$remarks=trim('Payment');
									$dept_name = 'ECI';
									$estr = $amount.$head_desc.$depositor_name.$candidate_id;
																		
									$url_d = 'https://cts.uk.gov.in/E-Chalan/Revreverify.aspx';
									?>
									
									<form method="post" name="getCIN" action="<?php echo $url_d; ?>">									
									<input type="hidden" name="user_Pass" value="<?php echo $user_Pass;?>">
									<input type="hidden" name="user_id" value="<?php echo $candidate_id;?>">
									<input type="hidden" name="address" value="<?php echo $address;?>">
									<input type="hidden" name="d_name" value="<?php echo $depositor_name;?>">
									<input type="hidden" name="Head_desc" value="<?php echo $head_desc;?>">
									<input type="hidden" name="Total_Amount" value="<?php echo $psta[0]->challan_amount;?>">
									<input type="hidden" name="u_no" value="<?php echo $psta[0]->reff_no;?>">
									<input type="hidden" name="dept_name" value="<?php echo $dept_name;?>">
									<input type="hidden" name="estr" value="<?php echo $estr;?>">
									 <input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
									</form>
									<br>
									@endif
									
									@if($payState=='S19')
										<?php
											//Demo
											/* $url = 'https://ifmstg.punjab.gov.in/eRctDeptInt/OnePlusTresury/VerifyChallan';
											$ip = '52.172.29.171';
											$clientId = '3dad2e78100f42ec999abb423f193333';
											$clientSecret = '6abcc8627e34498abb4bc7c562603333';
											$integratingAgency = 'DUAT01';
											$head_of_account = '0041-00-101-02-00'; */
											
											//Live
											$url = 'https://ifms.punjab.gov.in/ifmsintapi/OnePlusTresury/VerifyChallan';
											$ip = '164.100.229.227';	
											$clientId = '3d8d248dc2604ad2984606ff27951eae';	
											$clientSecret = 'c3968310cf304ba9bc058f927c2bf67d';
											$integratingAgency = 'ECI01';
											$head_of_account = '8443-00-121-01-00';

											$challanDate = date('Y-m-d',strtotime($psta[0]->created_at));
											$reff_no = $psta[0]->reff_no;
											//PETSDD152
											
											$json = '{"deptRefNo":"'.$reff_no.'","clientId":"'.$integratingAgency.'","deptCode":"ELC","challanDate":"'.$challanDate.'"}';
											
											
											$checksum_re = CheckSum($json);	
											
											$data_pun_re ='{"chcksum":"'.$checksum_re.'","challandata":{"deptRefNo":"'.$reff_no.'","clientId":"'.$integratingAgency.'","deptCode":"ELC","challanDate":"'.$challanDate.'"}}';
																						
											$enc_data_pun_re = encrypt_punjab($data_pun_re); 
	
											$postData = '{"encData":"'.$enc_data_pun_re.'","clientId":"'.$clientId.'","clientSecret":"'.$clientSecret.'","integratingAgency":"'.$integratingAgency.'","ipAddress":"'.$ip.'","transactionID":"'.$psta[0]->challan_ref_id.'"}';
											?>						


										<form method="post" id="verification_pun" name="verification_pun" action="{{url('payment-verification-pun')}}">
											<input type="hidden" name="url" id="encrypt_pun" value="{{$url}}">
											<input type="hidden" name="encData" id="postData" value="{{$postData}}">
											<input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
										</form>

									<br>
									@endif

									@if($payState=='S18')
										
									<?php 
									$dept_code = 'ECI'; //Static fixed value
									$amount = $psta[0]->challan_amount;
									$reff_no = $psta[0]->reff_no;
									$string = $dept_code.'|'.$reff_no.'|'.$amount;

									$hmac = app(App\Http\Controllers\OdiPaymentController::class)->checksum_odi($string);
									$string_enc = $string.'|'.base64_encode($hmac);
					
									$encdata = app(App\Http\Controllers\OdiPaymentController::class)->encrypt_od($string_enc);
									
									?>
									
									<form method="post" name="getCIN" action="{{ route('payment-verification-odi') }}">
										<input type="hidden" name="msg" id="encdata" value="{{$encdata}}">
										<input type="hidden" name="deptCode" value="{{$dept_code}}">
									 	<input type="submit" class="btn btn-primary" id ="submit" name="submit_cin" value="Check Payment Status" />
									</form>

									<br>
									@endif
									
									@if($payState=='S08')
										<?php
											
											//Live
											$url = 'https://himkosh.hp.nic.in/eChallan/webpages/AppVerification.aspx';
											$merchant_code = 'HIMKOSH305';
											$AppRefNo = $psta[0]->reff_no;
											$Service_code = 'CEO';
											
											$mString="AppRefNo=".$AppRefNo."|Service_code=".$Service_code."|merchant_code=".$merchant_code;
											
											$mString.="|checkSum=".checkSumHimachal($mString);

											$encdata = $mString;	
											
											?>						


										<form method="post" id="verification_him" name="verification_him" action="{{url('payment-verification-hp')}}">
											<input type="hidden" name="url"  value="{{$url}}">
											<input type="hidden" name="encdata"  value="{{$encdata}}">
											<input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
										</form>

									<br>
									@endif
									
									
									@if($payState=='S23')
										<?php
											
									
											$Applicationnumber=$psta[0]->reff_no;
											?>						


										<form method="post" id="verification_tri" name="verification_tri" action="{{url('payment-verification-tri')}}">
											<input type="hidden" name="Applicationnumber"  value="{{$Applicationnumber}}">
											<input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
										</form>

									<br>
									@endif
									
									@if($payState=='S15')
									<form method="post" name="getCIN" action="https://megepayment.gov.in/challan/models/frmgetgrn.php" >
									 <input type="hidden" id ="DEPARTMENT_ID" name=DEPARTMENT_ID value="<?php echo $psta[0]->reff_no;?>" />
									 <input type="hidden" id ="OFFICE_CODE" name="OFFICE_CODE" value="CEO000" />
									 <input type="hidden" id ="AMOUNT" name="AMOUNT" value="<?php echo $psta[0]->challan_amount;?>" />
									 <input type="hidden" id ="ACTION_CODE" name="ACTION_CODE" value="GETCIN"/>
									 <input type="hidden" id ="SUB_SYSTEM" name="SUB_SYSTEM" value="ESUVIDHA" />
									 <input type="submit" class="btn btn-primary" id ="submit" name="submit_cin" target = "_BLANK" value="Check Payment Status" />
									</form>
									<br>
									@endif
									
									
									@if($payState=='S12')
										<?php
											
											//demo
											//$url ='https://117.240.217.54/MPCTP/ResponseToDept.jsp?CALLTYPE=0&encryptionType=4&APPLICATION_ID=MPCTP&txn_type=txn_type_challan_pay';
											
											//Live
											$url ='https://mptreasury.gov.in/MPCTP/ResponseToDept.jsp?CALLTYPE=0&encryptionType=4&APPLICATION_ID=MPCTP&txn_type=txn_type_challan_pay';
											$merchant_code = 'ECI';
											$urn = $psta[0]->reff_no;
											$ru="https://suvidha.eci.gov.in/suvidhaac/public/payment-return-handle-mp";
											
											$mString = "urn=".$urn."|ru=".$ru;

											$encdata = encrypt_mp($mString);
											
											?>						


										<form method="post" name="getCIN" action="{{$url}}" >
											<input type="hidden" name="encdata" id="encdata" value="{{$encdata}}">
											<input type="hidden" name="merchant_code" id="merchant_code" value="{{$merchant_code}}">
											<input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Check Payment Status" />
										</form>

									<br>
									@endif
									
									
									
								@endif
								@if($psta[0]->bank_transaction_status=='1') 
									@if($payState=='S03')
										<form method="post" name="getCIN" action="https://assamegras.gov.in/challan/views/frmEpayEchallanPrintMerge.php" target="_blank">
											 <input type="hidden" id ="DEPARTMENT_ID" name=DEPARTMENT_ID value="<?php echo $psta[0]->reff_no;?>" />
											 <input type="hidden" id ="OFFICE_CODE" name="OFFICE_CODE" value="ELE005" />
											 <input type="hidden" id ="GRN" name="GRN" value="<?php echo $psta[0]->challan_ref_id;?>" />
											 <input type="hidden" id ="AMOUNT" name="AMOUNT" value="<?php echo $psta[0]->challan_amount;?>" />
											 <input type="hidden" id ="hcin_no" name="hcin_no" value="<?php echo $psta[0]->challan_ref_id;?>"/>
											 <input type="hidden" id ="VIEWCHALLAN" name="VIEWCHALLAN" value="Y" />
											 <input type="hidden" id ="OUTSIDE" name="OUTSIDE" value="OUTSIDE" />
											 <input type="submit" class="btn btn-primary" id ="submit123" name="submit_cin" value="Print Challan" />
										</form>
										<br>
									@endif
									@if($payState=='U07')
										<form method="post" name="getCIN" action="https://gras.py.gov.in/PrintEchallan.php" target="_blank">
										 <input type="hidden" id ="grn" name="grn" value="<?php echo $psta[0]->challan_ref_id;?>" />
										 <input type="submit" class="btn btn-primary"  value="Print Challan" />
										</form>
										<br>
									@endif
									@if($payState=='S25')
											<a href="https://wbifms.gov.in/grips-svc/payment/svc/utility/secure/download/challan?grn=<?php echo $psta[0]->challan_ref_id;?>&token=<?php echo time();?>" target="_blank">
												<input type="button" class="btn btn-primary"  value="Print Challan" />
											</a>
											<br>
									@endif
									@if($payState=='S22')
											<a href="https://www.karuvoolam.tn.gov.in/challan/echallan/download/<?php echo $psta[0]->challan_ref_id;?>" target="_blank">
												<input type="button" class="btn btn-primary"  value="Print Challan" />
											</a>
											<br>
									@endif
									@if($payState=='S11')
											<form method="post" name="getCIN" action="https://etreasury.kerala.gov.in/challan/views/frmEpayEchallanPrint.php" target="_blank">
											 <input type="hidden" id ="hcin_no" name="hcin_no" value="<?php echo $psta[0]->challan_ref_id;?>" />
											 <input type="hidden" name="OUTSIDE" id="OUTSIDE" value="OUTSIDE">
											 <input type="submit" class="btn btn-primary"  value="Print Challan" />
											</form>
											<br>
									@endif
									
									
									@if($payState=='S14')
											
											<br>
									@endif
									
									@if($payState=='S08')
											<a class="btn btn-primary" href="https://himkosh.hp.nic.in/eChallan/challan_reports/reportViewer.aspx?reportName=PaidChallan&TransId=<?php echo $psta[0]->challan_ref_id;?>" target="_BLANK">Print Challan </a>
											<br>
									@endif
									
									
									@if($payState=='S23')
											<a class="btn btn-primary" href="<?php echo url('payment-verification-tri-print').'/'.$psta[0]->challan_ref_id;?>" target="_BLANK">Print Challan </a>
											<br>
									@endif
									@if($payState=='S15')
										<form method="post" name="getCIN" action=" https://megepayment.gov.in/challan/models/frmgrnverificationoutsidebe.php" target="_blank">
											 <input type="hidden" id ="USERID" name="USERID" value="<?php echo $psta[0]->reff_no;?>" />
											 <input type="hidden" id ="OFFICECODE" name="OFFICECODE" value="CEO000" />
											 <input type="hidden" id ="GRN" name="GRN" value="<?php echo $psta[0]->challan_ref_id;?>" />
											 <input type="hidden" id ="AMOUNT" name="AMOUNT" value="<?php echo $psta[0]->challan_amount;?>" />
											 <input type="hidden" id ="VIEWCHALLAN" name="VIEWCHALLAN" value="Y" />
											 <input type="submit" class="btn btn-primary" value="Print Challan" />
										</form>
										<br>
									@endif
									
									
									@if($payState=='S12')
											<a class="btn btn-primary" href="https://mptreasury.gov.in/MPCTP/portal.htm?viewName=printChallan&registered=N" target="_BLANK">Print Challan </a>
											<br>
									@endif
									
								@endif
								@endif
								<span style="color:black;font-size:13px;"><b>{{ __('messages.challanPayment') }}:</b> 
								
										<?php 
										$expdText = '';
										$mydated = 'NA';
										if(!empty($psta[0]->bank_transtimestamp)){ 
										$expdText = explode(" ", $psta[0]->bank_transtimestamp);
											if(!empty($expdText[1])){
											$mydated  = $expdText[0] .' '. $expdText[1];
											}
										}
										$ost='Unknown';
										if(!empty($psta[0]->bank_transaction_status)){ 
										    if($psta[0]->bank_transaction_status==''){
										     $ost='Unknown';
										    }
											if($psta[0]->bank_transaction_status=='2'){
										     $ost='Pending';
										    }
											if($psta[0]->bank_transaction_status=='1'){
										     $ost='Success';
										    }
											if($psta[0]->bank_transaction_status=='3'){
										     $ost='Failed';
										    }
											if($psta[0]->bank_transaction_status=='4'){
										     $ost='Aborted';
										    }
										} else {
											$ost='Unknown';
										}
								?>
										
										 @if((count($psta)>0))	
											<span >{{$ost}}
										   @endif		
										   @if(count($challanCnt)>0)
												   @if(!empty($challanCnt[0]->payByCash) && ($challanCnt[0]->payByCash > 0))
													Opt to be paid by Cash
												   @else
													Opt to be paid by Challan
												   @endif  	
										   @endif	
								
								</span>
								
								
								
								
								
								<span style="color:black;margin-left:50px;font-size:13px"><b>{{ __('messages.challanDteTime') }}:</b> 
											
										   @if((count($psta)>0))	
												@if($mydated != '1970-01-01 05:30:00' && $mydated != '0000-00-00 00:00:00'){{$mydated}} @else NA @endif
										   @endif		
										   @if(count($challanCnt)>0)
												 @if(!empty($challanCnt[0]->payByCash) && ($challanCnt[0]->payByCash > 0))
												  	@if(!empty($challanCnt[0]->createdAt)){{date('d-m-Y h:i:s', strtotime($challanCnt[0]->createdAt))}}@endif			
												 
												 @else	 
													@if(!empty($challanCnt[0]->createdAt)){{date('d-m-Y h:i:s', strtotime($challanCnt[0]->createdAt))}}@endif		
												 @endif 
										   
										   @endif	
											  
											  
											  
											  
								
								</span>
								<span style="color:black;margin-left:50px;font-size:13px"><b>{{ __('messages.Mode') }}:</b> 
											 @if((count($psta)>0))
												<!--<span  onclick="return showPaymentDetails();" style="color:blue;cursor:pointer;">Online</span>-->
											   <span style="color:blue;cursor:pointer;">Online</span>
											  @endif	
											  
											 @if(count($challanCnt)>0)
												 @if(!empty($challanCnt[0]->payByCash) && ($challanCnt[0]->payByCash > 0))
													 <span>Pay By Cash</span>
												 @else		
											    	<span  onclick="return showPaymentDetails();" style="color:blue;cursor:pointer;">Challan</span>
											     @endif
											  @endif	
												</span>
									<span style="color:black;margin-left:10px;font-size:13px"><b>GRN No:</b> 
											 @if((count($psta)>0))
												<span>{{$psta[0]->challan_ref_id}}</span>
											   <!--<span>Online</span>-->
											  @endif		
												</span>
								<br><br>
							@endif	
								
								
							@if(count($psta)>0)
								@if($psta[0]->bank_transaction_status=='3')	
								<div>	
									<strong class="pr-2 btn font-big blink-effect"> {{ __('csa.Pay_Security_Deposit_Online') }} </strong>
                                    <div  onclick="red();" style="float: right;cursor:pointer;" class="razorpay-payment-button">
										PAY
									</div>								
                                </div>	
								
								@endif	
							@endif								
								
							  <!--<div onclick="return showBankDetails();" class="btn btn-primary">{{$bnk}} {{ __('csa.Bank_Details') }}</div>-->
							</div>								   
							
  
                                        <div class="row" style="display:none;">
                                            <div class="col-md-4 col-md-offset-4">
                                            <form name="frmPayment" id="frmPayment" action="" method="post">
                                                <!-- if session found create new Token -->
                                                <!--end create new Token-->
                                                <br class="clearfix">
                                                <table width="100%" border="1" class="Payment" cellpadding="0" cellspacing="0">
                                                <tbody><tr>
                                                <td width="16%"><b>{{ __('nomination.Name') }}</b></td>
                                                <td width="30%"><spacer>{{$candidate_name}}	</spacer></td>
                                                <td width="30%"><strong>{{ __('nomination.Fee') }}</strong> :&nbsp; <img src="https://rtionline.gov.in/images/rsSymbol.png" width="8" height="10" style="border:none;"><cite>{{$dmt}}</cite></td>
                                                <!--<td width="24%">&nbsp;</td>-->
                                                </tr>
                                                </tbody></table>
                                                <br class="clearfix">
                                                <div id="SBIBankDiv">
                                                </div>
                                            </form>
                                        </div>
										<!--End Left SideBar--> 

                                       <br class="clearfix">
                                    </div>
								<?php //echo "<pre>"; print_r($psta); ?>
                                
						
							    @if((count($psta)<=0) && (count($challanCnt)<=0))
								<div>	
									<strong class="pr-2 btn font-big blink-effect"> {{ __('csa.Pay_Security_Deposit_Online') }} </strong>
                                    <div  onclick="red();" style="float: right;cursor:pointer;" class="razorpay-payment-button">
										PAY
									</div>								
                                </div>			
								@elseif( (count($challanCnt) > 0))	
					@if($finalize_after_payment!=1)				
				<!--	<div style="cursor:pointer;color:blue;" id="online_pay2" onclick="return finalizeNomination('online_pay2');">{{ __('messages.fn') }}-- pp</div>	-->
					@else
					<!--<div style="cursor:pointer;color:gray;" id="online_pay2" >{{ __('messages.nmf') }}</div>	-->
					@endif	
								<!--<div><strong class="pr-2">{{ __('messages.Challan') }}</strong>
							    <span style="color: blue; font-size: 13px; margin-right: 10px; cursor: pointer;" onclick="return showPaymentDetails();">{{ __('messages.Challan_Submitted') }} </span></div> -->
								<!-- Modal confirm schedule --> 
									<div class="modal fade modal-confirm" id="paymentPopUp">
										<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
										  <div class="modal-content">
										   <div class="pop-header pt-3 pb-1">
											  <div class="animte-tick"><span>&#10003;</span></div>	
											<h5 class="modal-title">{{ __('messages.Challan') }}</h5> 
											<div class="header-caption">
											</div>		
											</div>
											<?php 
											$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($stndn, $acnnod);
											$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($stndn);
											?>
											<div class="modal-body">
											<ul>
											<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}:</label><span><?php echo $acnnod;  ?>-<?php echo $acnamed,', '.$stated; ?></span></li>
											
											<li><label>{{ __('messages.ChallanNO') }}</label><span> {{ $challanCnt[0]->challan_no }}</span></li> 
											
											<li><label>{{ __('messages.ChallanDate') }}</label><span> {{ $challanCnt[0]->createdAt	}}</span></li>
											
											<li><label>{{ __('messages.ViewChallan') }}</label><span><a href="<?php echo url('/'); ?>/{{$challanCnt[0]->challan_receipt}}" target="_blank">View</a></span></li> 
											
											 </ul> 
											 
											  <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
											 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	 
											 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }} </p>	
											</div>
											<div class="confirm-footer">
											  <button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">{{ __('nomination.ok') }}</button>
											</div>
										  </div>
										</div> 
									  </div><!-- End Of confirm Modal popup Div -->
								@else 
					@if($finalize_after_payment!=1)				
					<!--<div style="cursor:pointer;color:blue;" id="online_pay2" onclick="return finalizeNomination('online_pay2');">{{ __('messages.fn') }}</div>	-->
					@else
					<!--<div style="cursor:pointer;color:gray;" id="online_pay2" >{{ __('messages.nmf') }}</div>-->	
					@endif		
								<!--<div><strong class="pr-2">{{ __('csa.Paid') }} </strong>
							    <span style="color: blue; font-size: 13px; margin-right: 10px; cursor: pointer;" onclick="return showPaymentDetails();">{{ __('csa.Details') }} </span></div>-->
									<!-- Modal confirm schedule -->
									
									<div class="modal fade modal-confirm" id="paymentPopUp">
										<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
										  <div class="modal-content">
										   <div class="pop-header pt-3 pb-1">
											  <div class="animte-tick"><span>&#10003;</span></div>	
											<h5 class="modal-title">{{ __('csa.Receipt') }} </h5> 
											<div class="header-caption">
											</div>		
											</div>  
										<?php 
										$expdText = '';
										$mydated = 'NA';
										$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($stndn, $acnnod);
										$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($stndn);
										if(count($psta)>0){
										if(!empty($psta[0]->bank_transtimestamp)){ 
										$expdText = explode(" ", $psta[0]->bank_transtimestamp);
										   if(!empty($expdText[1])){
											$mydated  = $expdText[1];
											}
										}
										$ost='Unknown';
										if(!empty($psta[0]->bank_transaction_status)){ 
										    if($psta[0]->bank_transaction_status==''){
										     $ost='Unknown';
										    }
											if($psta[0]->bank_transaction_status=='2'){
										     $ost='Pending';
										    }
											if($psta[0]->bank_transaction_status=='1'){
										     $ost='Success';
										    }
											if($psta[0]->bank_transaction_status=='3'){
										     $ost='Failed';
										    }
											if($psta[0]->bank_transaction_status=='4'){
										     $ost='Aborted';
										    }
										} else {
											$ost='Unknown';
										}
										}else{
											$ost='NA';
										}
										//echo "<pre>"; print_r($psta); 
										
										?>	
											<div class="modal-body">
											  <ul>
												@if($ost=='Success' || $ost=='Pending')
												<li><label>GRN No:</label><span><?php echo $psta[0]->challan_ref_id;  ?></span></li>
												@endif
												<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}:</label><span><?php echo $psta[0]->ac_no;  ?>-<?php echo $acnamed,', '.$stated; ?></span></li>
												<!--<li><label>{{ __('csa.Amount') }}:</label> <span>Rs. <?php //echo $psta[0]->amount1;  ?></span></li>
												<li><label>{{ __('csa.Payment_Status') }}:</label> <span>{{$ost}}</span></li>-->
												<!--<li><label>{{ __('csa.Amount') }}:</label> <span>INR <?php //echo $psta[0]->transaction_amount;  ?></span></li>-->
												
												<li><label>{{ __('csa.Payment_Date') }}:</label><span>@if(!empty($psta[0]->bank_transtimestamp) && $psta[0]->bank_transtimestamp != '0000-00-00 00:00:00')<?php echo date('d-m-Y', strtotime($psta[0]->bank_transtimestamp));  ?>@else{{'NA'}}@endif</span></li>
												<li><label>{{ __('csa.Payment_Time') }}:</label><span><?php echo $mydated;  ?></span></li>
												@if(isset(($psta[0]->challan_url)) && !empty($psta[0]->challan_url))
												<li><label>Receipt:</label><span><a href="<?php echo $psta[0]->challan_url; ?>" target="_blank">Download</a></span></li>
												@endif
											 </ul> 
											  <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
											  <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	
											 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }} </p>	
											</div>
											<div class="confirm-footer">
											  <button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">{{ __('nomination.ok') }}</button>
											</div>
										  </div>
										</div>
									  </div><!-- End Of confirm Modal popup Div -->
									
								@endif	
                             </div><!-- End Of d-flex Div -->	
							 
					   </div><!-- End Of booked-appoint Div -->
					  <br>
					   @if($payState=='S03')
					   <p class="note-warn">For any queries related to online payment : <strong>Mr. Tamojyoti Bose</strong>   <strong><i class="fa fa-mobile"></i> +91 99540 30309</strong></p>
					   
					   @endif
						<p class="note-warn"><strong><i>{{ __('csa.Instruction') }}<sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
						<p class="note-warn"><strong><i>{{ __('csa.Instruction') }}<sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	
						<p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }}</p>
					  </div><!-- End of frst week -->	
			  </div><!-- End Of appnt-calndr Div -->   
			</div> 
		   </div>
		 </div>  
		 <div class="card-footer">
		<div class="text-left col-md-3 col-3 pl-3" style="height:1px;">
		<?php 
		$stno='';
		$acnno='';
		$acst = app(App\Http\Controllers\Nomination\NominationController::class)->getAcStByNo($expd[0]); 
			 if(!empty($acst)){
				$entt = explode("***", $acst);
				$stno=encrypt_String($entt[0]);
				$acnno=encrypt_String($entt[1]);
			 }
		
		?>
				
			  <a href="{{'nominations?acs='.$acnno.'&std='.$stno}}" class="btn btn-lg font-big dark-pink-btn" style="color: white;">{{ __('step1.Back') }} </a>  
			</div> 	
		   <div class="apt-btn text-right"> 
				@if($appoinment_status!='Cancel')
			  <a href="#" class="btn btn-lg font-big dark-pink-btn"  data-target="#basicExampleModal" data-toggle="modal">{{ __('step1.Cancel') }}</a>  
			  @endif	
			  
			    <a href="#" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return showhide();"> {{ __('messages.res') }}</a>  
			</div> 
		 </div>  
	   </div>
	 </div>

  
	 
	 
	 
	<!-- Modal For Cancel-->
	<div class="modal fade" id="basicExampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/cancel-nomination-prev') }}" autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">{{ __('messages.canpre') }}</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		   <input type="hidden" name="nom_id" value="<?php echo $_REQUEST['id']; ?>">
		  <div class="modal-body" style="margin-left:-37px;">
          <ul>
          	<!--<li><label>Name:</label> <span> {{$candidate_name}}  </span></li>-->
          	<?php  if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>Nomination No.: </label> <span> {{$NOMNO}}</span></li>
          	<!--<li><label>AC No. &amp; Name: </label> <span> {{$ACNO}} - {{$ACname}}</span></li>-->
			<?php }}  ?>
			<li><label>{{ __('nomination.Status') }}:</label> <span> {{$appoinment_status}}</span></li>
		   </ul>
		</div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" onclick="return showLoader();" style="background: #007bff; border: none;">{{ __('messages.Cancel_Preference') }}</button>
		  </div>
		  
		  <span style="text-align: center;display:none;" id="loader3">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		  
		  
		</div>
	  </div>
	  </form>
	</div>
	
	<!-- Modal For Schedule Appoinment-->
	<div class="modal fade" id="basicExampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="appoinment_form" id="appoinment_form" method="POST"  action="{{url('/nomination/prev/post') }}"  autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
		<!--	<h5 class="modal-title" id="areyu">{{ __('csa.Confirmation') }}</h5>		-->	
			<h5 class="modal-title" >Confirmation</h5>			
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <input type="hidden" name="daytime" id="daytime">
		  <input type="hidden" name="testing" id="testing">
		  <input type="hidden" name="id" value="{{$_REQUEST['id']}}">
		  <input type="hidden" name="slot" id="slot">
		<div class="modal-body" id="textmsg" style="border-bottom: 1px solid #e9ecef;">
			<div class="row">
							<div class="col-md-8 col-12">
							  <ul class="list-inline" style="width: 157%;">
							    <input type="hidden" name="nom_id" value="{{$nom_id}}">
								<li class="list-inline-item mr-4" style="font-size: 17px;"> 
								<?php echo __('messages.are_you_sure_p'); ?>
							  </ul>  
							</div>   
						  </div>
		  </div>
		 <!-- <div class="modal-body" style="margin-left:-37px;">
          <ul>
          	<li><label>Name:</label> <span> {{$candidate_name}}</span></li>
			
			<?php  if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			if($datadd!="NA"){
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>Nomination No.: </label> <span> {{$NOMNO}}</span></li>
          	<li><label>AC No. &amp; Name: </label> <span> {{$ACNO}} - {{$ACname}}</span></li>
			  
			<?php } } } ?>  
			<li><label>Election</label> <span>GENERAL-2020</span></li>
			<li><label>Status:</label> <span> {{$appoinment_status}}</span></li>
		   </ul>-->
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #007bff; border: none;" id="reareyu" onclick="return showsc();">
			{{ __('messages.Yes') }}</button>
		  </div>
		  <span style="text-align: center;display:none;" id="loader">
		   <!--<span>{{ __('messages.emailsms') }} </span>-->
		  <br>
		 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('finalize.Please_Wait') }}
		</span>
		</div>
		</div>
	  </div>
	  </form>
	</div> 
	
	
	
	 
	 
	<!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="confirm">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5>
		<div class="header-caption">
		  <p><?php //echo  __('messages.succpre');  ?>			
			{{ __('messages.confpre') }} 
		  </p>			  
		</div>		
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <ul>
          	<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>{{ __('nomination.Nomination_No') }}</label> <span>{{$NOMNO}}</span></li>
			<?php }} ?>
		 </ul>
		 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
		 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	
		 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }}</p>	
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
        
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
  
  
  
	
	<div class="modal fade modal-cancel" id="emptySlot">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>
		<div class="header-caption">
		  <p>{{ __('messages.pleasesel') }}</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div>
	
    <div class="modal fade modal-cancel" id="maxError">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>
		<div class="header-caption">
		  <p>{{ __('messages.3max') }}</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div>
	
	
	<div class="modal fade modal-cancel" id="red">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1 mb-2">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5>
        </div>
	   <div class="px-3">	
		<div style="padding: 0px; text-align: center; color:#f0587e; margin:1rem 0;">
          **{{ __('messages.rosubject') }}
		</div>
	  </div>	
		@if($isPaymentConfig=="YES"  && $payState != 'S06')
		@php 
		if($payState){
			$stname = getstatebystatecode($payState);
			if($stname){
				$stname = $stname->ST_NAME;
			}
		}else{
			$stname = '';
		}
		@endphp
        <div id="paySec">
		<div class="modal-body p-0 text-center">
          <ul>
			<li><label>You will be redirected at {{$stname}} State Payment Gateway</label></li>
			<li><label>{{ __('messages.sdepam') }} : RS. {{$amount}} </label></li>
		 </ul>
		
        <div class="confirm-footer p-0">  
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>	
			&nbsp; 
			<button type="button" class="btn dark-pink-btn" data-dismiss="modal" style="height: 30px; width: 12%;"  onclick="return redirectPayment();">
				{{ __('nomination.ok') }}</button>
		  &nbsp; &nbsp; &nbsp;
		  
		  
          <div class="foot-highlight">  
		    <div class="orSparate" style="width: 50px;">{{ __('messages.OR') }}</div>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			  {{ __('messages.payBy') }} <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('1');">
			 {{ __('messages.SubBy') }} </span>
         </div>
        </div>
		 </div>
		 </div>
		@endif
		@if($payState=='S06')
        <div id="payGuj">
		<div class="modal-body p-0 text-center">
          <ul>
			<li><label><b>For Online payment support please call 07923257494</b></label></li>
			<li><label><b>10 am to 10 pm</b></label></li>
			<li><label>You will be redirected at Gujrat State Payment Gateway</label></li>
			<li><label>{{ __('messages.sdepam') }} : RS. {{$Total_amount}} </label></li>
		 </ul>
		
        <div class="confirm-footer p-0">  
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>	
			&nbsp; 
		   <button type="button" class="btn dark-pink-btn" data-dismiss="modal" style="height: 30px; width: 12%;"  onclick="return redirect_gujrat();">
		  {{ __('nomination.ok') }}</button>
		  &nbsp; &nbsp; &nbsp;
		  
		  
		  
		  
		  
		  
		  
          <div class="foot-highlight">   
		    <div class="orSparate" style="width: 50px;">{{ __('messages.OR') }}</div>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			  {{ __('messages.payBy') }} <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('1');">
			 {{ __('messages.SubBy') }} </span>
         </div>
        </div>
		 </div>
		 </div>
		@endif
		
		
		<?php 
		$strdtdtd='';
		if($isPaymentConfig=='NO'){
			$strdtdtd='block';
		} else {
			$strdtdtd='none';
		}
		?>
		
		
		
		 <form name="submit_challan" id="submit_challan" method="POST"  action="{{url('/nomination/challan/post') }}"  autocomplete='off' enctype="multipart/form-data"  style="margin-top:-26px;display:{{$strdtdtd}};">
		  {{ csrf_field() }}
		 <div class="modal-body p-0">
          <!--<div style="text-align: center; font-size: 15px; font-weight: bold;">{{ __('messages.challanSubmit') }}</div> -->
		  <!-- @if(!empty($state_payment_url)) <?php //echo $state_payment_url; ?>
		    <div class="mx-5 mt-3 text-center">{{ __('messages.paylink') }}<span><a href="http://<?php echo $state_payment_url; ?>" target="_blank">&nbsp; Payment Link</a></span></div>
		   @endif	-->
		  <br> 
		    <ul style="text-align: center; background: aliceblue; font-size: 16px; font-weight: bold;opacity: .8;"> Pay By Challan </ul>
		  <input type="hidden" name="st" value="{{$stndn}}">
		  <input type="hidden" name="ac" value="{{$acnnod}}">
		  <ul>
			<li><label>{{ __('messages.chananNumber') }}</label><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="challan_number" id="challan_number" style="margin-left: 14px; width: 167px;"  maxlength="15"></label></li>
			<li><label>{{ __('messages.challanDateSub') }}</label>&nbsp;&nbsp;&nbsp;<label><input type="text" name="challan_date"  id="challan_date"  readonly style="width: 167px;margin-left: 5px;"></label></li>
			<li><label>{{ __('messages.ChallanReceipt') }}</label><label>&nbsp;&nbsp;&nbsp;<input type="file" name="file" id="challan_receipt" style="margin-left: 60px; width: 97px;" ></label></li> 
			<li style="color:red;" id="preError"></li>    
		 </ul>
		 <div style="text-align:center;display:none;" id="previewId">
		     <iframe id="iframeid"  style="width: 360px; height: 218px;"></iframe>
		 </div>
		
		  <div class="confirm-footer p-0">  
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>	
			&nbsp; 
		   <button type="submit" class="btn dark-pink-btn" style="height: 30px;"  onclick="return submitChallan();">
		  {{ __('messages.Submit') }} </button><br> <br>
		  &nbsp; &nbsp; &nbsp;	
		  
		  
	  <div class="foot-highlight" style="background: white;"> 
		 <div class="orSparate" style="width: 50px;">  {{ __('messages.OR') }} </div>
		 
		    <ul style="text-align: center; background: aliceblue; font-size: 16px; font-weight: bold;margin-top: 11px;"> Pay By Cash </ul>
		 <ul style=" margin-top: 29px;">
			<input type="hidden" name="payByCash" id="payByCash">
			<li> <i class="fa fa-check-square-o" aria-hidden="true"></i> I will opt for cash payment of security deposit to the Returning officer</li>
			<button type="submit" class="btn dark-pink-btn" style="height: 30px;" onclick="return payByCashSubmit();">  {{ __('messages.Submit') }}</button>
		 </ul>
	  </div>
		
			
          @if($isPaymentConfig=='YES')
		
		<div class="foot-highlight"> 
         <div class="orSparate" style="width: 50px;">  {{ __('messages.OR') }} </div>
		 <i class="fa fa-check-square-o" aria-hidden="true"></i>
		{{ __('messages.payByGateway') }}  <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('2');">
		 {{ __('messages.SubBy') }} </span>
		 </div>
		 @endif		  
		 
		 
		  @if($isPaymentConfig=='GUJ')
		
		<div class="foot-highlight"> 
         <div class="orSparate" style="width: 50px;">  {{ __('messages.OR') }} </div>
		 <i class="fa fa-check-square-o" aria-hidden="true"></i>
		{{ __('messages.payByGateway') }}  <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('3');">
		 {{ __('messages.SubBy') }} </span>
		 </div>
		 @endif		  
		 
		 
        </div> 
		
		
		  <span style="text-align: center;display:none;margin-left: 9em;" id="afhbdsfbdjg">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		
		 </div>
		 </form>
      </div>
    </div>
  </div>
	
	
	
	
  <!-- Modal Cancel on success-->
    <div class="modal fade modal-cancel" id="cancel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5>
		<div class="header-caption">
		  <p>{{ __('messages.Succcanpre') }}</p>	
		</div>		
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <ul>
          	<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>{{ __('nomination.Nomination_No') }}:</label> <span>{{$NOMNO}}</span></li>
          	<!--<li><label>AC No. &amp; Name:</label> <span>{{$ACNO}} - {{$ACname}}</span></li>-->
			<?php }} ?>
			
			<!--<li><label>{{ __('csa.Status') }}:</label> <span>{{$appoinment_status}}</span></li>-->
		 </ul>
		<!-- <p class="note-warn"><strong><i>Instruction <sup>*</sup></i></strong>Please carry all original and necessary documents for verification</p>-->
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div>
  <!-- Bank Details-->
	<div class="modal fade" id="bankDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/save-bank-prev') }}" autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header pop-header">
			<h5 class="modal-title" id="exampleModalLabel">{{ __('csa.Bank_Details') }}</h5>
			<input type="hidden" name="nid" value="<?php echo $_REQUEST['id']; ?>">
			<input type="hidden" name="bank_id" value="<?php if(!empty($bank[0]->id)){ echo $bank[0]->id; } else { echo 0; }; ?>">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">  
          <table>
          	<tr><td>{{ __('csa.Name_as_account') }}:</td> <td><input type="text" name="candidate_name" id="candidate_name" value="<?php if(!empty($bank[0]->candidate_name)){ echo $bank[0]->candidate_name; }  ?>"></td></tr>
          	<tr><td>{{ __('csa.Bank_Name') }}:</td> <td><input type="text" name="bank_name" id="bank_name" value="<?php if(!empty($bank[0]->bank_name)){ echo $bank[0]->bank_name; }  ?>"></td></tr> 
          	<tr><td>{{ __('csa.Account_Number') }}:</td> <td><input type="number" name="account_number"  id="account_number" value="<?php if(!empty($bank[0]->account_number)){ echo $bank[0]->account_number; }  ?>" min="8"></td></tr>
          	<tr><td>{{ __('csa.Confirm_Account_Number') }}:</td> <td><input type="number" name="confirm_account_number"  id="confirm_account_number"  value="<?php if(!empty($bank[0]->confirm_account_number)){ echo $bank[0]->confirm_account_number; }  ?>" min="8"></td></tr>
          	<tr><td>{{ __('csa.IFSC') }}:</td> <td><input type="text" name="ifsc_code"  id="ifsc_code" value="<?php if(!empty($bank[0]->ifsc_code)){ echo $bank[0]->ifsc_code; }  ?>" min="5"></td></tr>
		  </table>	
		</div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" onclick="return valBank();">{{ __('csa.Submit') }}</button>
		  </div>
		</div>
	  </div>
	  </form>
	</div>
	<div id=""></div> 
   </main>   
   
   <?php   
   
   //echo "PPPPP";
   
	if($payState=='S06'){
		
$fnlStrDAta="User_id=".$User_id."|Init_date=".$Init_date."|Transaction_id=".$Transaction_id."|Tax_type=".$Tax_type."|RegNo=".$RegNo."|Name=".$NameGuj."|Token_no=".$Token_no."|Total_amount=".$Total_amount."|Phone_no=".$Phone_no."|Tax_period_from=".$Tax_period_from."|Tax_period_to=".$Tax_period_to."|Purpose=".$Purpose.'~'.$Total_amount."|MerchantId=".$MerchantId;
		
		
		$key='q4UOLnbuVc0mP8Jf634f1zCGVy2pf9lj';
		$iv='q4UOLnbuVc0mP8Jf';
		
		$enc_method = "AES-128-CBC"; 	
		
		$trd=trim($fnlStrDAta);		
		
		
		
		$encdata=openssl_encrypt($trd, $enc_method, $key, $options=0, $iv);			
		$encryptedDU=openssl_encrypt($DU, $enc_method, $key, $options=0, $iv);	
		
		//$rd='https://cybertreasuryuat.gujarat.gov.in/CyberTreasury_UAT/connectDept?service=DeptPortalConnection'; //UAT
		$rd="https://cybertreasury.gujarat.gov.in/CyberTreasury/connectDept?service=DeptPortalConnection";  // Live
		
		
		?>
		
		<form method="POST" name="redGuj"  action="<?php echo $rd; ?>">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<input type="hidden" name="CTP_DATA" value="<?php echo $encdata; ?>">
		<input type="hidden" name="Dept_call" value="first">
		<input type="hidden" name="RU" value="<?php echo $RU; ?>">
		<input type="hidden" name="DU" value="<?php echo $encryptedDU; ?>">
		<input type="hidden" name="MerchantId" value="<?php echo $MerchantId ?>">
		<input type="hidden" name="batchId" value="0810202000101">
		<input type="hidden" name="token" value="24343">
		</form>
		
		
		
		
	
	<?php } ?>
   
   
   
	
	
	<?php    
	//dd($_SERVER);
	if($isPaymentConfig=="YES"){
		
		$depositor_name=trim($depositor_name);
		$email=trim($email);
		$mobile=trim($mobile);
		$candidate_id=trim($candidate_id);
		$address=trim(substr($address, 0, 90));
		$remarks=trim('Payment');
		$amount=trim($amount);
		
		######################################### West bengol ############################
		if($payState=='S25'){
		
			$return_url=trim($return_url);
			$reff_no=trim($reff_no);
			$dep_code="093";//Static fixed value
			$service_code="401";//Static fixed value
			$dept_ref_no=$reff_no;//Unique transaction number
			$identification_no="1";//Will use for increasing payment attempt
			$in_favor_of="ECI";//The payment will be done in favor of
			$rtn_prd_frm=date('dmY');	
			$rtn_prd_to=date('dmY');		
			$depositor_name=trim($depositor_name);
			$email=trim($email);
			$mobile=trim($mobile);
			$address=trim(substr($address, 0, 90));
			$remarks=trim('Payment');
			
			$amount=trim($amount);
			$head_of_account = '8443-00-121-001-07';
			
			
			$xml_encdata = '';
			$xml_encdata .= '<GRIPS_DEPT_EPAYMENT_REQ>';
				$xml_encdata .= '<DEPT_CD>'.$dep_code.'</DEPT_CD>';
				$xml_encdata .= '<SERVICE_CD>'.$service_code.'</SERVICE_CD>';
				$xml_encdata .= '<DEPT_REF_NO>'.$dept_ref_no.'</DEPT_REF_NO>';
				$xml_encdata .= '<IDENTIFICATION_NO>'.$identification_no.'</IDENTIFICATION_NO>';
				$xml_encdata .= '<DEPOSITED_BY>'.$depositor_name.'</DEPOSITED_BY>';
				$xml_encdata .= '<DEPOSITOR_MOBILE_NO>'.$mobile.'</DEPOSITOR_MOBILE_NO>';
				$xml_encdata .= '<DEPOSITOR_EMAIL_ID>'.$email.'</DEPOSITOR_EMAIL_ID>';
				$xml_encdata .= '<DEPOSITOR_ADDRESS>'.$address.'</DEPOSITOR_ADDRESS>';
				$xml_encdata .= '<IN_FAVOR_OF>'.$in_favor_of.'</IN_FAVOR_OF>';
				$xml_encdata .= '<RTN_PRD_FRM>'.$rtn_prd_frm.'</RTN_PRD_FRM>';
				$xml_encdata .= '<RTN_PRD_TO>'.$rtn_prd_to.'</RTN_PRD_TO>';
				$xml_encdata .= '<TOTAL_AMOUNT>'.$amount.'</TOTAL_AMOUNT>';
				$xml_encdata .= '<REMARKS>'.$remarks.'</REMARKS>';
				$xml_encdata .= '<REPEATED_HEAD ROW="1">';
				$xml_encdata .= '<HEAD_OF_ACCOUNT>'.$head_of_account.'</HEAD_OF_ACCOUNT>';
				$xml_encdata .= '<AMOUNT>'.$amount.'</AMOUNT>';
				$xml_encdata .= '</REPEATED_HEAD>';
			$xml_encdata .= '</GRIPS_DEPT_EPAYMENT_REQ>';
			
			$wb_checksum = wb_hash256($xml_encdata);
			
			$xml_encdata .= '|CHECKSUM='.$wb_checksum;
			
			//$key='1234567890123456';//Demo server
			$key='EhA6g$hsd6Vcds@k';//Live Server
			//$iv='abcdefghijklmnop';//Demo server
			$iv='h6Vfsh42hxhjgdFi';//Live Server
			$enc_method = "aes-128-cbc";		
		
			$encdata=wb_payment_encrypt($xml_encdata, $enc_method, $key, $iv);
			
			//$rd='http://202.61.117.90/GRIPS/ecepay.do';
			$rd='https://wbifms.gov.in/GRIPS/ecepay.do';		
			?>
			<form method="POST" name="paymentred" action="<?php echo $rd; ?>">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			<input type="hidden" name="ENCDATA" value="<?php echo $encdata; ?>">
			<input type="hidden" name="DEPT_CD" value="<?php echo $dep_code; ?>">
			</form>
		
			
		<?php }else if($payState=='U07'){//Puddchery
			$payment_type='01';
			$dep_code="009";//Static fixed value
			$treasury_code='0001';//Static fixed value
			$dept_ref_no=$reff_no;//Unique transaction number
			$office_code='009000';//Will use for increasing payment attempt
			$rec_fin_year='2020-2021';//The payment will be done in favor of
			$period="O";//The payment will be done in favor of
			$rtn_prd_frm='01/05/2021';	
			$rtn_prd_to='31/05/2021';		
			$depositor_name=trim($depositor_name);
			$major_head='8443';
			$email=trim($email);
			$mobile=trim($mobile);
			$address=trim(substr($address, 0, 90));
			$remarks=trim('Payment');
			$amount=trim($amount);
			$challan_amount=trim($amount);
			$head_of_account = '8443-00-121-01-00-00';
			$txn_id = '';
			$department_id = $reff_no;
			$subsytem = 'ECIPYSYSTEM';
			$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$reqkey = 'ECI2021REQPRIKEY';
			$reskey = 'ECI2021RESPRIKEY';
			$checksum_str = 'DEPT_CODE='.$dep_code.'|PAYMENT_TYPE='.$payment_type.'|CHALLAN_AMOUNT='.$amount.'|'.$reqkey;
			$checksum= hash('sha256',$checksum_str);
			$rd = 'https://gras.py.gov.in/challan/views/frmgrnfordept.php';
			
			
					
		?>
			<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>">
				<input type="hidden" name="DEPT_CODE" value="<?php echo $dep_code;?>" />
				<input type="hidden"  name="PAYMENT_TYPE" value="<?php echo $payment_type;?>" />
				<input type="hidden" name="TREASURY_CODE" value="<?php echo $treasury_code;?>" />
				<input type="hidden"  name="OFFICE_CODE" value="<?php echo $office_code;?>"/>
				<input type="hidden"  name="REC_FIN_YEAR" value="<?php echo $rec_fin_year;?>" />
				<input type="hidden"  name="PERIOD" value="<?php echo $period;?>" />
				<input type="hidden"  name="FROM_DATE" value="<?php echo $rtn_prd_frm;?>" />
				<input type="hidden"  name="TO_DATE" value="<?php echo $rtn_prd_to;?>" />
				<input type="hidden"  name="MAJOR_HEAD" value="<?php echo $major_head;?>" />
				<input type="hidden"  name="HOA1" value="<?php echo $head_of_account;?>" />
				<input type="hidden"  name="AMOUNT1" value="<?php echo $amount;?>" />
				<input type="hidden"  name="CHALLAN_AMOUNT" value="<?php echo $challan_amount;?>" />
				<input type="hidden"  name="TAX_ID" value="<?php echo $txn_id;?>" />
				<input type="hidden"  name="PARTY_NAME" value="<?php echo $depositor_name;?>" />
				<input type="hidden"  name="MOBILE_NO" value="<?php echo $mobile;?>" />
				<input type="hidden"  name="DEPARTMENT_ID" value="<?php echo $department_id;?>" />
				<input type="hidden"  name="SUB_SYSTEM" value="<?php echo $subsytem;?>" />
				<input type="hidden"  name="Checksum" value="<?php echo $checksum;?>" />
			</form>
		
		<?php }else if($payState=='S11'){//Kerla
		
			$reff_no=trim($reff_no);
			$payment_type='01';
			$dep_code="180";//Static fixed value
			$treasury_code=substr($office_code, 0, 4);//Static fixed value
			$dept_ref_no=$reff_no;//Unique transaction number
			$office_code=$office_code;//Will use for increasing payment attempt
			$rec_fin_year='2020-2021';//The payment will be done in favor of
			$period="A";//The payment will be done in favor of
			$rtn_prd_frm='01/04/2019';	
			$rtn_prd_to='31/03/2020';		
			$depositor_name=trim($depositor_name);
			$major_head='8443';
			$email=trim($email);
			$mobile=trim($mobile);
			$address=trim(substr($address, 0, 90));
			$remarks=trim('Payment');
			$amount=trim($amount);
			$challan_amount=trim($amount);
			$head_of_account = '17099';
			$txn_id = '';
			$department_id = $reff_no;
			$subsytem = 'registration';
			//$rd = 'http://103.251.43.79/echallan/challan/views/frmgrnfordept.php';
			$rd = 'https://etreasury.kerala.gov.in/challan/views/frmgrnfordept.php';
			//dd($rd);
		?>
			<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>">
				<input type="hidden" name="DEPT_CODE" value="<?php echo $dep_code;?>" />
				<input type="hidden" name="SERVICE_DEPT_CODE" value="<?php echo $dep_code;?>" />
				<input type="hidden"  name="PAYMENT_TYPE" value="<?php echo $payment_type;?>" />
				<input type="hidden" name="TREASURY_CODE" value="<?php echo $treasury_code;?>" />
				<input type="hidden"  name="OFFICE_CODE" value="<?php echo $office_code;?>"/>
				<input type="hidden"  name="REC_FIN_YEAR" value="<?php echo $rec_fin_year;?>" />
				<input type="hidden"  name="PERIOD" value="<?php echo $period;?>" />
				<input type="hidden"  name="FROM_DATE" value="<?php echo $rtn_prd_frm;?>" />
				<input type="hidden"  name="TO_DATE" value="<?php echo $rtn_prd_to;?>" />
				<input type="hidden"  name="MAJOR_HEAD" value="<?php echo $major_head;?>" />
				<input type="hidden"  name="HOA1" value="<?php echo $head_of_account;?>" />
				<input type="hidden"  name="AMOUNT1" value="<?php echo $amount;?>" />
				<input type="hidden"  name="CHALLAN_AMOUNT" value="<?php echo $challan_amount;?>" />
				<input type="hidden"  name="TAX_ID" value="<?php echo $txn_id;?>" />
				<input type="hidden"  name="PARTY_NAME" value="<?php echo $depositor_name;?>" />
				<input type="hidden"  name="MOBILE_NO" value="<?php echo $mobile;?>" />
				<input type="hidden"  name="DEPARTMENT_ID" value="<?php echo $department_id;?>" />
				<input type="hidden"  name="SUB_SYSTEM" value="<?php echo $subsytem;?>" />
			</form>






		<?php }else if($payState=='S22'){ //Tamilnadu
		$reff_no=trim($reff_no);
			$payment_type='01';
			$dept_ref_no=$reff_no;//Unique transaction number	
			$depositor_name=trim($depositor_name);
			$email=trim($email);
			$mobile=trim($mobile);
			$address=trim(substr($address, 0, 90));
			$amount=trim($amount);
			$department_id = $reff_no;
			
			if($nom_dist=='1' || $nom_dist=='2' || $nom_dist=='3' || $nom_dist=='13'){
				$department = '03802';
			}else{
				$department = '04102';
			}
			
			
			//$rd = 'http://164.100.134.244:6080/echallan/';
			$rd = 'https://www.karuvoolam.tn.gov.in/challan/deptchallan';
			
			$fnlStrDAta="department=".$department."|ddo_code=".$ddoCode."|total_amt=".$amount."|RU=".$return_url;
			
			$key = 'secretKeyIfhrmst';
			$iv = 'secretInitIfhrms';
			//$enc = AesCipher::encrypt($key, $iv, $fnlStrDAta);
			//$decrypted = AesCipher::decrypt($key,$iv, $enc);
		
			$encdata=\App\Helpers\AesCipher::encrypt($key, $iv, $fnlStrDAta);	
		?>
		<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>">
			<input name="enc_data" type="hidden" value="<?php echo $encdata;?>"/>
			<input type="hidden" name="department" value="<?php echo $department;?>">
			<input type="hidden" name="district" value="<?php echo $district;?>">
			<input type="hidden" name="ddoCode" value="<?php echo $ddoCode;?>">
			<input type="hidden" name="ddoOffice" value="<?php echo $ddoOffice;?>">
			<input type="hidden" name="remitterType" value="Others">
			<input type="hidden" name="remitterName" value="<?php echo $depositor_name;?>">
			<input type="hidden" name="block" value="<?php echo $address;?>">
			<input type="hidden" name="city" value="Chennai">
			<input type="hidden" name="pincode" value="500050">
			<input type="hidden" name="contactNo" value="<?php echo $mobile;?>"></td>
			<input type="hidden" name="receiptType" value="18"/>
			<input type="hidden" name="subType" value="001" />
			<input type="hidden" name="accountCode" value="844300121AA80102"/>
			<input type="hidden" name="amount" value="<?php echo $amount;?>"/>
			<input type="hidden" name="dept_tran_ref_no" value="<?php echo $department_id;?>"/>
			<input type="hidden" name="deptRefNo" value=""/>
			<input type="hidden" name="pan" value=""/>
			<input type="hidden" name="paymentMode" value="1"/>
			<input type="hidden" name="state" value="TN"/>
		</form>
		<?php }else if($payState=='S03'){//Assam
			$reff_no=trim($reff_no);
			$payment_type='04';
			$dep_code="ELE";//Static fixed value
			$treasury_code="DIS";//Static fixed value
			$dept_ref_no=$reff_no;//Unique transaction number
			$office_code="ELE005";//Will use for increasing payment attempt
			$rec_fin_year='2019-2020';//The payment will be done in favor of
			$period="A";//The payment will be done in favor of
			$rtn_prd_frm='01/04/2019';	
			$rtn_prd_to='31/03/2020';		
			$depositor_name=trim($depositor_name);
			$major_head='8443';
			$email=trim($email);
			$mobile=trim($mobile);
			$address=trim(substr($address, 0, 90));
			$remarks=trim('Payment');
			$amount=trim($amount);
			$challan_amount=trim($amount);
			$head_of_account = '8443-00-121-0000-000-01';
			$txn_id = '';
			$department_id = $reff_no;
			$subsytem = 'Election';
			//$rd = 'https://uatgras.assam.gov.in/challan/views/frmgrnfordept.php';
			$rd = 'https://assamegras.gov.in/challan/views/frmgrnfordept.php';
			//dd($rd);
		?>
			<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>">
				<input type="hidden" name="DEPT_CODE" value="<?php echo $dep_code;?>" />
				<input type="hidden"  name="PAYMENT_TYPE" value="<?php echo $payment_type;?>" />
				<input type="hidden" name="TREASURY_CODE" value="<?php echo $treasury_code;?>" />
				<input type="hidden"  name="OFFICE_CODE" value="<?php echo $office_code;?>"/>
				<input type="hidden"  name="REC_FIN_YEAR" value="<?php echo $rec_fin_year;?>" />
				<input type="hidden"  name="PERIOD" value="<?php echo $period;?>" />
				<input type="hidden"  name="FROM_DATE" value="<?php echo $rtn_prd_frm;?>" />
				<input type="hidden"  name="TO_DATE" value="<?php echo $rtn_prd_to;?>" />
				<input type="hidden"  name="MAJOR_HEAD" value="<?php echo $major_head;?>" />
				<input type="hidden"  name="HOA1" value="<?php echo $head_of_account;?>" />
				<input type="hidden"  name="AMOUNT1" value="<?php echo $amount;?>" />
				<input type="hidden"  name="CHALLAN_AMOUNT" value="<?php echo $challan_amount;?>" />
				<input type="hidden"  name="TAX_ID" value="<?php echo $txn_id;?>" />
				<input type="hidden"  name="PARTY_NAME" value="<?php echo $depositor_name;?>" />
				<input type="hidden"  name="MOBILE_NO" value="<?php echo $mobile;?>" />
				<input type="hidden"  name="DEPARTMENT_ID" value="<?php echo $department_id;?>" />
				<input type="hidden"  name="SUB_SYSTEM" value="<?php echo $subsytem;?>" />
			</form>


		<?php }else if($payState=='S14'){ //Manipur
		
			$reff_no=trim($reff_no);
			$payment_type='01';
			$dep_code="026";//Static fixed value
			$treasury_code= $treasury_code;//Static fixed value
			$office_code= $office_code;//Will use for increasing payment attempt
			$rec_fin_year='2021-2022';//The payment will be done in favor of
			$period="O";//The payment will be done in favor of
			$rtn_prd_frm='01/04/2021';	
			$rtn_prd_to='31/03/2022';
			$major_head='0070';
			
			$head_of_account = '0070021040000';
			$amount = 1; //trim($amount);
			$challan_amount= 1; //trim($amount);
			$txn_id = $candidate_id;
			$depositor_name=trim($depositor_name);
			//$pan_no ='';
			
			$address=trim(substr($address, 0, 90));
			$mobile=trim($mobile);
			$department_id = $reff_no;
			$remarks=trim('Payment');
			$subsytem = 'MNECI';
			$from_id = 'TR6';
			
			//dd($amount);
			
		//demo
		//$rd = 'https://164.100.122.143/echallan/challan/views/frmgrnfordept.php';
		
		//live
		$rd = 'https://egrasmanipur.nic.in/echallan/challan/views/frmgrnfordept.php';
			//dd($rd);
		?>
			<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>">
				<input type="hidden" name="DEPT_CODE" value="<?php echo $dep_code;?>" />
				<input type="hidden"  name="PAYMENT_TYPE" value="<?php echo $payment_type;?>" />
				<input type="hidden" name="TREASURY_CODE" value="<?php echo $treasury_code;?>" />
				<input type="hidden"  name="OFFICE_CODE" value="<?php echo $office_code;?>"/>
				<input type="hidden"  name="REC_FIN_YEAR" value="<?php echo $rec_fin_year;?>" />
				<input type="hidden"  name="PERIOD" value="<?php echo $period;?>" />
				<input type="hidden"  name="FROM_DATE" value="<?php echo $rtn_prd_frm;?>" />
				<input type="hidden"  name="TO_DATE" value="<?php echo $rtn_prd_to;?>" />
				<input type="hidden"  name="MAJOR_HEAD" value="<?php echo $major_head;?>" />
				<input type="hidden"  name="HOA1" value="<?php echo $head_of_account;?>" />
				<input type="hidden"  name="AMOUNT1" value="<?php echo $amount;?>" />
				<input type="hidden"  name="CHALLAN_AMOUNT" value="<?php echo $challan_amount;?>" />
				<input type="hidden"  name="TAX_ID" value="<?php echo $txn_id;?>" />
				<input type="hidden"  name="PARTY_NAME" value="<?php echo $depositor_name;?>" />
				<input type="hidden"  name="MOBILE_NO" value="<?php echo $mobile;?>" />
				<input type="hidden"  name="DEPARTMENT_ID" value="<?php echo $department_id;?>" />
				<input type="hidden"  name="SUB_SYSTEM" value="<?php echo $subsytem;?>" />
				<input type="hidden"  name="FORM_ID" value="<?php echo $from_id;?>" />
			</form>
			
			
	
		<?php }else if($payState=='S28'){ //Uttrakhand		
			$reff_no=trim($reff_no);
			$payment_type='01';
			$user_Pass="0070";//Static fixed value
			$head_desc = '0070028000601';
			$amount = trim($amount);
			$challan_amount=trim($amount);
			$depositor_name=trim($depositor_name);
			$address=trim(substr($address, 0, 90));
			$mobile=trim($mobile);
			$department_id = $reff_no;
			$remarks=trim('Payment');
			$dept_name = 'ECI';
			$estr = $amount.$head_desc.$depositor_name.$candidate_id;

		//demo
		//$rd = 'https://cts.uk.gov.in/E-Chalan/UTK_DEPT/eChallan.aspx';
		$rd = 'https://cts.uk.gov.in/e-chalan/UTK_DEPT/eChallanECI.aspx';
		
		//live
		//$rd = '';
			//print_r($estr);
		?>
			
		<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>" >    
           <input type="hidden" name="user_Pass" value="<?php echo $user_Pass;?>">
           <input type="hidden" name="user_id" value="<?php echo $candidate_id;?>">
           <input type="hidden" name="address" value="<?php echo $address;?>">
           <input type="hidden" name="d_name" value="<?php echo $depositor_name;?>">
           <input type="hidden" name="Head_desc" value="<?php echo $head_desc;?>">
           <input type="hidden" name="Total_Amount" value="<?php echo $amount;?>">
           <input type="hidden" name="u_no" value="<?php echo $department_id;?>">
           <input type="hidden" name="dept_name" value="<?php echo $dept_name;?>">
           <input type="hidden" name="estr" value="<?php echo $estr;?>">
		</form>		
					
		<?php }else if($payState=='S24'){ ?>//Uttar Pradesh
		
	
		<?php }else if($payState=='S05'){ //Goa
		
			$return_url=trim($return_url);
			$reff_no=trim($reff_no);
			$dep_code="0070021040100";//Static fixed value
			//$service_code="286";//Static fixed value
			$service_code="278";//Static fixed value
			$demand="06";//Static fixed value
			$dept_ref_no=$reff_no;//Unique transaction number
	
			$depositor_name=trim($depositor_name);
			$email=trim($email);
			$mobile=trim($mobile);
			$address=trim(substr($address, 0, 90));
			$remarks=trim('Payment');
			
			$amount= 1; //trim($amount);
			$id_address = get_client_ip();
			$party_pin = '403001';
					
			$xml_encdata = '';
			$xml_encdata .= '<USERDATA>';
			$xml_encdata .= '<MobileNo>'.$mobile.'</MobileNo>';
			$xml_encdata .= '<ChallanCount>1</ChallanCount>';			
			$xml_encdata .= '<Demand>'.$demand.'</Demand>';			
			$xml_encdata .= '<Head1>'.$dep_code.'</Head1>';
			$xml_encdata .= '<Amount1>'.$amount.'</Amount1>';
			$xml_encdata .= '<TotalAmt>'.$amount.'</TotalAmt>';
			$xml_encdata .= '<IPAddress>'.$id_address.'</IPAddress>';
			$xml_encdata .= '<PartyName>'.$depositor_name.'</PartyName>';
			$xml_encdata .= '<PartyAddress>'.$address.'</PartyAddress>';
			$xml_encdata .= '<PartyPin>'.$party_pin.'</PartyPin>';
			$xml_encdata .= '<PartyTaluka>Taluka</PartyTaluka>';			
			$xml_encdata .= '<PartyRef>'.$dept_ref_no.'</PartyRef>';
			$xml_encdata .= '<OtherDetails>'.$dept_ref_no.'</OtherDetails>';
			$xml_encdata .= '<Reason>'.$remarks.'</Reason>';
			$xml_encdata .= '</USERDATA>';
			
			//var_dump($xml_encdata);
	
			echo 'goa-->';
	
			$encdata=goa_payment_encrypt($xml_encdata);
			//echo $encdata;
			
			echo '<--goa2-->';
			
			
			//$soapUrl = "http://10.190.22.7/echallanws/service.asmx?op=generateEchallanPDFNew";
			
		$soapUrl = "https://echallanws.goa.gov.in/service.asmx?op=generateEchallanPDFNew";
		
		
		//$soapUrl = "https://echallanws.goa.gov.in/generateEchallanPDFNew";

			
			

/* 
	$xml_post_string ='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><generateEchallanPDFNew xmlns="https://echallanws.goa.gov.in"><demand>06</demand><encrypt_XML_data>'.$encdata.'</encrypt_XML_data><serviceCode>278</serviceCode></generateEchallanPDFNew></soap:Body></soap:Envelope>'; */
			
		
$xml_post_string ='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><generateEchallanPDFNew xmlns="http://tempuri.org/"><demand>06</demand><encrypt_XML_data>'.$encdata.'</encrypt_XML_data><serviceCode>278</serviceCode></generateEchallanPDFNew></soap:Body></soap:Envelope>';

		
			
			 /* $xml_post_string ='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><generateEchallanPDFNew xmlns="https://echallanws.goa.gov.in/service.asmx/"><demand>06</demand><encrypt_XML_data>'.$encdata.'</encrypt_XML_data><serviceCode>278</serviceCode></generateEchallanPDFNew></soap:Body></soap:Envelope>'; */


		echo '<--goa3--><pre>';

		//echo $xml_post_string;

		/* $headers = array(
		"POST /echallanws/service.asmx HTTP/1.1",
		"Host: 10.190.22.7",
		"Content-Type: text/xml; charset=utf-8",
		"Content-Length: ".strlen($xml_post_string)
		); 
 */


			$headers = array(
			"POST /service.asmx HTTP/1.1",
			"Host: echallanws.goa.gov.in",
			"Content-Type: text/xml; charset=utf-8",
			"Content-Length: ".strlen($xml_post_string)
			); 

			/* 	$url = $soapUrl;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$response = curl_exec($ch); 
				curl_close($ch);


				//echo $response; die;

//die;
				
				$response1 = str_replace("<soap:Body>","",$response);
				$response2 = str_replace("</soap:Body>","",$response1);
				//echo $response2.'<pre>'; die;
				$response_soap = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
				$xml = new SimpleXMLElement($response_soap);
				$body = $xml->xpath('//soapBody');
				$array = json_decode(json_encode((array)$body[0]), TRUE); 
				
				//var_dump($array); die;
				
				
				$eno = $array['generateEchallanPDFNewResponse']['generateEchallanPDFNewResult']['diffgrdiffgram']['NewDataSet']['Table1']['eno'];
			
				$decdata=goa_payment_decrypt($eno);
				
				
				$challanno = $decdata;
				
				$plain_text_str = 'echallanno='.$decdata.'|totalamt='.$amount;
				
				$encdata=goa_payment_encrypt($plain_text_str);
				 */
				//202200000091
				
			//echo $encdata; die;
			//$url = 'http://10.190.22.7/echallanpg/haveechallan.aspx';
			//
			
			$url = 'https://echallanpg.goa.gov.in/haveechallan.aspx';
			
			
			
			//var_dump($decdata);
			?>
			
			
				<form method="post" id="paymentred" name="paymentred" action="{{$url}}">
						<input type="hidden" name="demand" id="demand" value="{{$demand}}">
						<input type="hidden" name="encdata" value="{{$encdata}}">
					</form>

	<?php }else if($payState=='S19'){  // Punjab
	
	$depositor_name=trim($depositor_name);
	$email=trim($email);
	$candidate_id=$candidate_id;
	$mobile=trim($mobile);
	$address=trim(substr($address, 0, 90));
	$remarks=trim('Payment');
	$amount= trim($amount);
	
	
	//Demo
	/* $url = 'https://ifmstg.punjab.gov.in/eRctDeptInt/Server/ProcessChallan';
	$ip = '52.172.29.171';
	$clientId = '3dad2e78100f42ec999abb423f193333';
	$clientSecret = '6abcc8627e34498abb4bc7c562603333';
	$integratingAgency = 'DUAT01';
	$head_of_account = '0041-00-101-02-00'; */
	
	//Live
	$url = 'https://ifms.punjab.gov.in/eRctDeptInt/Server/ProcessChallan';
	$ip = '164.100.229.227';	
	$clientId = '3d8d248dc2604ad2984606ff27951eae';	
	$clientSecret = 'c3968310cf304ba9bc058f927c2bf67d';
	$integratingAgency = 'ECI01';
	$head_of_account = '8443-00-121-01-00';
	
	//0070-02-101-03-00
	
		
	$date = date('Y-m-d 00:00:00');
	$datas = '{"deptRefNo":"'.$reff_no.'","receiptNo":"","clientId":"'.$integratingAgency.'","challanDate":"'.$date.'","expiryDate":"'.$date.'","companyName":"'.$depositor_name.'","deptCode":"ELC","totalAmt":"'.$amount.'","trsyAmt":"'.$amount.'","nonTrsyAmt":"0","noOfTrans":"1","ddoCode":"'.$ddo_code.'","payLocCode":"'.$pay_loc_code.'","add1":"","add2":"","add3":"","add4":"","add5":"","sURL":"'.$return_url.'","fURL":"'.$return_url.'","trsyPayments":[{"Head":"'.$head_of_account.'","amt":"'.$amount.'"}],"nonTrsyPayments":null,"payee_info":{"payerName":"'.$depositor_name.'","teleNumber":"'.$mobile.'","mobNumber":"'.$mobile.'","emailId":"'.$email.'","addLine1":"'.$address.'","addLine2":"","addPincode":"140055","district":"'.$district_lgdid.'","tehsil":"'.$tehsil_lgdid.'"}}';
	$checksum = CheckSum($datas);
	$checksum_datas = '{"chcksum":"'.$checksum.'","challandata":{"deptRefNo":"'.$reff_no.'","receiptNo":"","clientId":"'.$integratingAgency.'","challanDate":"'.$date.'","expiryDate":"'.$date.'","companyName":"'.$depositor_name.'","deptCode":"ELC","totalAmt":"'.$amount.'","trsyAmt":"'.$amount.'","nonTrsyAmt":"0","noOfTrans":"1","ddoCode":"'.$ddo_code.'","payLocCode":"'.$pay_loc_code.'","add1":"","add2":"","add3":"","add4":"","add5":"","sURL":"'.$return_url.'","fURL":"'.$return_url.'","trsyPayments":[{"Head":"'.$head_of_account.'","amt":"'.$amount.'"}],"nonTrsyPayments":null,"payee_info":{"payerName":"'.$depositor_name.'","teleNumber":"'.$mobile.'","mobNumber":"'.$mobile.'","emailId":"'.$email.'","addLine1":"'.$address.'","addLine2":"","addPincode":"140055","district":"'.$district_lgdid.'","tehsil":"'.$tehsil_lgdid.'"}}}';
	$enc_data_pun = encrypt_punjab($checksum_datas); ?>
	
	<form method="post" id="paymentred" name="paymentred" action="{{$url}}">
		<input type="hidden" name="encData" id="encrypt_pun" value="{{$enc_data_pun}}">
		<input type="hidden" name="clientId" value="{{$clientId}}">
		<input type="hidden" name="clientSecret" value="{{$clientSecret}}">
		<input type="hidden" name="integratingAgency" value="{{$integratingAgency}}">
		<input type="hidden" name="transactionID" value="{{$reff_no}}">
		<input type="hidden" name="ipAddress" value="{{$ip}}">
	</form>
	
	<?php }else if($payState=='S18'){  // Odisha - Shubham
	
	$depositor_name=trim($depositor_name);
	$email=trim($email);
	$candidate_id=$candidate_id;
	$mobile=trim($mobile);
	$address=trim(substr($address, 0, 90));
	$remarks=trim('Online Election Nomination Deposit');
	$state = trim($State_name->ST_NAME);
	$dist_name = $DistName ? $DistName : 'NA';
	$amount= trim($amount);
	// $amount= 1;
	$dept_code ='ECI'; //Static value for Election Dept

	//Demo
	// $head_of_account = '0070-02-104-0252-01026-000';

	//live
	$head_of_account = '8443-00-121-1657-91358-000'; //Deposits made by candidates for State Legislature Elections
	
	$ru="https://suvidha.eci.gov.in/ac/public/payment-return-handle-odi";
	
	//Demo
	// $url = 'https://uat.odishatreasury.gov.in/echallan/dept-intg';
	//$ip = '164.100.141.12';

	//Live
	$url = 'https://www.odishatreasury.gov.in/echallan/dept-intg';
	// $ip = ' 117.240.239.66';
	
	$string = $dept_code.'|'.$reff_no.'|'.$head_of_account.'|'.$remarks.'|'.$amount.'||||||||||||||||'.$amount.'|'.$depositor_name.'|'.$address.'||'.$state.'|'.$DistName.'|761110|'.$mobile.'|'.$email.'|||||||'.$ru;
	
	$hmac =  app(App\Http\Controllers\OdiPaymentController::class)->checksum_odi($string);
	// echo base64_encode($hmac);
	// echo "\n";

	$string_enc = $string.'|'.base64_encode($hmac);
	// echo $string_enc;
	// echo "\n";

	$encrypt = app(App\Http\Controllers\OdiPaymentController::class)->encrypt_od($string_enc);
	// echo $encrypt;
	// echo "\n";

	// $decrypt = decrypt_od($encrypt, $key);
	// echo $decrypt;
	
	 ?>
	
	<form method="post" id="paymentred" name="paymentred" action="{{$url}}">
		<input type="hidden" name="msg" id="encrypt_odisha" value="{{$encrypt}}">
		<input type="hidden" name="deptCode" value="{{$dept_code}}">
	</form>
	
<?php }else if($payState=='S08'){  // Himachal Pradesh
	
	$depositor_name=trim($depositor_name);
	$email=trim($email);
	$candidate_id=$candidate_id;
	$mobile=trim($mobile);
	$address=trim(substr($address, 0, 90));
	$remarks=trim('Payment');
	$amount= trim($amount);
	
	
	//Live
	$url = 'https://himkosh.hp.nic.in/echallan/WebPages/wrfApplicationRequest.aspx';
	$merchant_code = 'HIMKOSH305';
	$DeptRefNo = $candidate_id;	
	$AppRefNo = $reff_no;
	$DeptID = '305';
	$head_of_account = '8443-00-121-01';
	$Service_code = 'CEO';
	
	$RU="https://suvidha.eci.gov.in/suvidhaac/public/payment-return-handle-hp";	// Live
	
	$mString="DeptID=".$DeptID."|DeptRefNo=".$DeptRefNo."|TotalAmount=".$amount."|TenderBy=".$depositor_name."|AppRefNo=".$AppRefNo."|Head1=".$head_of_account."|Amount1=".$amount."|Ddo=".$ddo."|PeriodFrom=01-01-".date("Y")."|PeriodTo=31-12-".date("Y")."|Service_code=".$Service_code."|return_url=".$RU;

		$mString.="|checkSum=".checkSumHimachal($mString);
		$encdata = encrypt_himachal($mString);	
 ?>
	

	<form method="post" id="paymentred" name="paymentred" action="{{$url}}">
		<input type="hidden" name="encdata" id="encrypt_him" value="{{$encdata}}">
		<input type="hidden" name="merchant_code" value="{{$merchant_code}}">
	</form>
	
	
<?php }else if($payState=='S23'){//Tripura
			$reff_no=trim($reff_no);
			$DTO=99;
			$STO=99;
			$DDO=99001;
			$Deptcode='ELE';
			// $UserID="elesuvid"; //UAT
			$UserID="eleceotr";   //Live
			$Applicationnumber=$reff_no; 
			$Cityname=$city; 
			$Address=trim(substr($address, 0, 90));
			//$Officename='080015128';	
			$Officename='999999001';	
			$ChallanYear='2223';		
			$SCHEMECOUNT='1';		
			$FullName=trim($depositor_name);
			
			$PINCODE='0';
			$bank='0001509';
			$Securityemail=trim($email);
			$Securityphone=trim($mobile);
			//$VALID_UPTO='06/01/2023';
			$VALID_UPTO='';
			$ptype='N';
			
			$Remarks='Election Suvidha Nomination Fee ';
			$TotalAmount=trim($amount);
			$FEEAMOUNT1= trim($amount);
			//$SCHEMENAME1 = '0070-60-800-90-50'; //UAT
			$SCHEMENAME1 = '0070-02-104-04-02'; //Live
			$txn_id = '';
			$department_id = $reff_no;
			$subsytem = 'Election';
			//$rd = 'http://164.100.127.83/egras/DeptPrePaymentReqHandler.aspx'; // UAT
			$rd = 'https://www.egras.tripura.gov.in/DeptPrePaymentReqHandler.aspx'; // Live
			
			$UURL = 'https://suvidha.eci.gov.in/suvidhaac/public/payment-return-handle-tri';
			//$UURL = 'http://127.0.0.1:8000/payment-return-handle-tri';
			
			//$AppKey = '29204bc44f0a927a550819a7558ef0bbe782e2ba8373a55541a9c111673f7cd7';  // UAT
			$AppKey = 'd01cb1bc2fbc0fa1f76ba79e271d3528775725675589960ec9fe94e6904a866b'; // Live
			
			
			$string = $DTO."|".$STO."|".$DDO."|".$Deptcode."|".$UserID."|".$Applicationnumber."|".$FullName."|".$Securityphone."|".$TotalAmount."|".$SCHEMECOUNT."|".$SCHEMENAME1."|".$FEEAMOUNT1."|".$UURL;
					
			  $hash  = base64_encode(hash_hmac('sha256',$string,$AppKey,true));
			
			
			//echo 'SERVER-->'.$_SERVER['HTTP_REFERER'];
			
		?>
			<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>">
				<input type="hidden" name="DTO" value="<?php echo $DTO;?>" />
				<input type="hidden"  name="STO" value="<?php echo $STO;?>" />
				<input type="hidden" name="DDO" value="<?php echo $DDO;?>" />
				<input type="hidden"  name="Deptcode" value="<?php echo $Deptcode;?>"/>
				<input type="hidden"  name="UserID" value="<?php echo $UserID;?>" />
				<input type="hidden"  name="Applicationnumber" value="<?php echo $Applicationnumber;?>" />
				<input type="hidden"  name="Fullname" value="<?php echo $FullName;?>" />
				<input type="hidden"  name="Cityname" value="<?php echo $Cityname;?>" />
				<input type="hidden"  name="Address" value="<?php echo $Address;?>" />
				<input type="hidden"  name="Officename" value="<?php echo $Officename;?>" />
				<input type="hidden"  name="ChallanYear" value="<?php echo $ChallanYear;?>" />
				<input type="hidden"  name="PINCODE" value="<?php echo $PINCODE;?>" />
				<input type="hidden"  name="bank" value="<?php echo $bank;?>" />
				<input type="hidden"  name="Remarks" value="<?php echo $Remarks;?>" />
				<input type="hidden"  name="Securityemail" value="<?php echo $Securityemail;?>" />
				<input type="hidden"  name="Securityphone" value="<?php echo $Securityphone;?>" />
				<input type="hidden"  name="VALID_UPTO" value="<?php echo $VALID_UPTO;?>" />
				<input type="hidden"  name="ptype" value="<?php echo $ptype;?>" />
				<input type="hidden"  name="TotalAmount" value="<?php echo $TotalAmount;?>" />
				<input type="hidden"  name="SCHEMECOUNT" value="<?php echo $SCHEMECOUNT;?>" />
				<input type="hidden"  name="SCHEMENAME1" value="<?php echo $SCHEMENAME1;?>" />
				<input type="hidden"  name="FEEAMOUNT1" value="<?php echo $FEEAMOUNT1;?>" />
				<input type="hidden"  name="hash" value="<?php echo $hash;?>" />
				<input type="hidden"  name="UURL" value="<?php echo $UURL;?>" />
			</form>

	<?php }else if($payState=='S15'){//Meghalaya
	
		$reff_no=trim($reff_no);
			$payment_type='01';
			$dep_code="CEO";//Static fixed value
			$treasury_code="01";//Static fixed value
			$dept_ref_no=$reff_no;//Unique transaction number
			$office_code="CEO000";//Will use for increasing payment attempt
			$rec_fin_year='2022-2023';//The payment will be done in favor of
			$period="O";//The payment will be done in favor of
			$rtn_prd_frm='01/01/2023';
			$rtn_prd_to='31/12/2023';		
			$depositor_name=trim($depositor_name);
			$major_head='8443';
			$email=trim($email);
			$mobile=trim($mobile);
			$address=trim(substr($address, 0, 90));
			$remarks=trim('Election Nomination Payment');
			$amount=trim($amount);
			$challan_amount=trim($amount);
			//$head_of_account = '8443-00-121-0000-000-01';
			$head_of_account = '8443001210100-89';
			$txn_id = '';
			$department_id = $reff_no;
			$subsytem = 'ESUVIDHA';

			$rd ='https://megepayment.gov.in/challan/views/frmgrnfordept.php'; // Live
			//$rd ='https://megepayment.gov.in/challan/views/frmSamplePortalInt.php'; // Test
			
		?>
			<form method="POST" name="paymentred" id="paymentred" action="<?php echo $rd;?>">
				<input type="hidden"  name="DEPT_CODE" value="<?php echo $dep_code;?>" />
				<input type="hidden"  name="PAYMENT_TYPE" value="<?php echo $payment_type;?>" />
				<input type="hidden"  name="TREASURY_CODE" value="<?php echo $treasury_code;?>" />
				<input type="hidden"  name="OFFICE_CODE" value="<?php echo $office_code;?>"/>
				<input type="hidden"  name="REC_FIN_YEAR" value="<?php echo $rec_fin_year;?>" />
				<input type="hidden"  name="PERIOD" value="<?php echo $period;?>" />
				<input type="hidden"  name="FROM_DATE" value="<?php echo $rtn_prd_frm;?>" />
				<input type="hidden"  name="TO_DATE" value="<?php echo $rtn_prd_to;?>" />
				<input type="hidden"  name="MAJOR_HEAD" value="<?php echo $major_head;?>" />
				<input type="hidden"  name="HOA1" value="<?php echo $head_of_account;?>" />
				<input type="hidden"  name="AMOUNT1" value="<?php echo $amount;?>" />
				<input type="hidden"  name="CHALLAN_AMOUNT" value="<?php echo $challan_amount;?>" />
				<input type="hidden"  name="TAX_ID" value="<?php echo $txn_id;?>" />
				<input type="hidden"  name="PARTY_NAME" value="<?php echo $depositor_name;?>" />
				<input type="hidden"  name="MOBILE_NO" value="<?php echo $mobile;?>" />
				<input type="hidden"  name="DEPARTMENT_ID" value="<?php echo $department_id;?>" />
				<input type="hidden"  name="SUB_SYSTEM" value="<?php echo $subsytem;?>" />
			</form>
	
	<?php }else if($payState=='S10'){ //Karnataka
			$reff_no=trim($reff_no);
			
			
			$amount = $totalAmount= 1; //trim($amount);
			$chlnDate = Date('d/m/Y');			
			$deptCode = "34E";
			
			$ddoCode = "17753D";
			//$ddoCode = "88815O";
			
			$reff_no = $deptRefNum='EC'.Date('my').'0070'.$reff_no;
			
			//$reff_no = $deptRefNum='EC'.Date('my').'8009'.$reff_no;
			
			$trsryCode = "572H";
			$deptPrpsId = $candidate_id;	
			
			$prpsName = "0070~02~104~3~06~000";	

			//$prpsName = "8009~01~101~0~01~000";	
			
			$subPrpsName = "";
			$subDeptRefNum=$deptRefNum.'AA';
			
			$agencyCode="EA_ECI"; 
			$integrationCode="RCT033";
			$uirNo='EA_ECI-RCT033-'.Date('dmY').'-'.$deptRefNum;			
			$rmtrName=trim($depositor_name);
			
		
$requestdata = '<data xmlns="">
<RctReceiveValidateChlnRq>
<chlnDate>'.$chlnDate.'</chlnDate>
<deptCode>'.$deptCode.'</deptCode>
<ddoCode>'.$ddoCode.'</ddoCode>
<deptRefNum>'.$deptRefNum.'</deptRefNum>
<rctReceiveValidateChlnDtls>
<amount>'.$amount.'</amount>
<deptPrpsId>'.$deptPrpsId.'</deptPrpsId>
<prpsName>'.$prpsName.'</prpsName>
<subPrpsName>'.$subPrpsName.'</subPrpsName>
<subDeptRefNum>'.$subDeptRefNum.'</subDeptRefNum>
</rctReceiveValidateChlnDtls>
<rmtrName>'.$rmtrName.'</rmtrName>
<totalAmount>'.$totalAmount.'</totalAmount>
<trsryCode>'.$trsryCode.'</trsryCode>
</RctReceiveValidateChlnRq>
</data>';
		
/* 	echo 'requestdata---->';	
	echo $requestdata;	
	echo 'requestdata---->'; */	


	$path = storage_path("key/KarnatakaPGSigner.jar");
	
	
	$dis = shell_exec("java -jar $path sign '$requestdata'");
	
	//echo $dis; die;
	
	 $start1 = "-----BEGIN PKCS7-----";
$end1 = "-----END PKCS7-----";

$start_pos1 = strpos($dis, $start1);
$end_pos1 = strpos($dis, $end1);

$digsign = substr($dis, $start_pos1, $end_pos1 - $start_pos1 + strlen($end1));
	
/* 	echo 'digsign---->';	
	echo $digsign;	
	echo 'digsign---->';	
	 */	
$xml_post_string ='<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
<Header>
<Header xmlns="http://service.receivevalidatechallan.dept.rct.integration.ifms.gov.in/">
<agencyCode xmlns="http://header.ei.integration.ifms.gov.in/">EA_ECI</agencyCode>
<integrationCode xmlns="http://header.ei.integration.ifms.gov.in/">RCT033</integrationCode>
<uirNo xmlns="http://header.ei.integration.ifms.gov.in/">EA_ECI-RCT033-'.date('dmY').'-'.$deptRefNum.'</uirNo>
</Header>
</Header>
<Body>
<envelopedDataReq xmlns="http://service.receivevalidatechallan.dept.rct.integration.ifms.gov.in/">
<Signature xmlns="">'.$digsign.'</Signature>
<data xmlns="">
<RctReceiveValidateChlnRq>
<chlnDate>'.$chlnDate.'</chlnDate>
<deptCode>'.$deptCode.'</deptCode>
<ddoCode>'.$ddoCode.'</ddoCode>
<deptRefNum>'.$deptRefNum.'</deptRefNum>
<rctReceiveValidateChlnDtls>
<amount>'.$amount.'</amount>
<deptPrpsId>'.$deptPrpsId.'</deptPrpsId>
<prpsName>'.$prpsName.'</prpsName>
<subPrpsName>'.$subPrpsName.'</subPrpsName>
<subDeptRefNum>'.$subDeptRefNum.'</subDeptRefNum>
</rctReceiveValidateChlnDtls>
<rmtrName>'.$rmtrName.'</rmtrName>
<totalAmount>'.$totalAmount.'</totalAmount>
<trsryCode>'.$trsryCode.'</trsryCode>
</RctReceiveValidateChlnRq>
</data>
</envelopedDataReq>
</Body>
</Envelope>';

	/* echo 'xml_post_string---->';	
	echo $xml_post_string;	
	echo 'xml_post_string---->';
 */

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://khajane2.karnataka.gov.in/KhajaneWs/rct/rrvcs/secbc/RctReceiveValidateChlnService?wsdl',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$xml_post_string,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/xml',
    'Cookie: AlteonP=AP+ga+sLEKwQG9V9h9whSg$$; dtCookie=v_4_srv_1_sn_A13FA68F8B98F1B3A7EF87E2A85E515A_perc_100000_ol_0_mul_1_app-3Aea7c4b59f27d43eb_1'
  ),
));

//die;

$response = curl_exec($curl);


curl_close($curl);
 
 	/* echo 'response---->';	
	echo $response;	
	echo 'response---->';
  */
 
 $start = "<data>";
$end = "</data>";

$start_pos = strpos($response, $start);
$end_pos = strpos($response, $end);

$substring = substr($response, $start_pos, $end_pos - $start_pos + strlen($end));



if(($substring != '<soap:E') || ($substring != '')){

//echo var_dump($substring);  die;

$xml = simplexml_load_string($substring, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

if(isset($array['RctReceiveValidateChlnRs']['statusDescription'])=='Success'){

			$mString="dept_ref_no=$deptRefNum|txn_date=".Date('dmY')."|amount=$amount|dept_pwd=1234";

			$checkSum = $mString.="|checkSum=".md5($mString);


			/* echo 'checkSum---->';	
			echo $checkSum;	
			echo 'checkSum---->'; */
		
		//echo $checkSum;  die;


			//Generate a 128-bit encryption key
			$encdata = shell_exec("java -jar $path encrypt '$checkSum'");
			
			/* echo 'encdata---->';	
			echo $encdata;	
			echo 'encdata---->'; */
			
			
			
			//$url ='https://preprodk2.karnataka.gov.in/wps/portal/Home/DepartmentPayment/?uri=receiptsample:com.tcs.departmentpage:departmentportlet'; 
			$url ='https://k2.karnataka.gov.in/wps/portal/Home/DepartmentPayment/?uri=receiptsample:com.tcs.departmentpage:departmentportlet';
?>
				<form method="post" id="paymentred" name="paymentred" action="{{$url}}">
					<input type="hidden" name="dept_code" id="dept_code" value="{{$deptCode}}">
					<input type="hidden" name="encdata" value="{{$encdata}}">
				</form>
				
<?php }}else{			
		echo 'Something went wrong';
	} ?>
				



<?php }else if($payState=='S12'){//Madhya Pradesh
	
		$reff_no=trim($reff_no);
	
	
		$reff_no1=substr($reff_no,0,6);
		$reff_no2=substr($reff_no,0,7);
		
		//dd($reff_no);
		
		$amount = $totalAmount= 1; //trim($amount);
		
		
		$deptCode = 'ECI';
		$major_head = '0070';
		$merchant_code = 'ECI';
		
			$crn = $deptCode.$major_head.date('dmY').$reff_no1;
			$reff_no = $urn = 'MPT'.$deptCode.date('dmY').$reff_no2;	
			
			//dd($crn);
		
			$ru="https://suvidha.eci.gov.in/suvidhaac/public/payment-return-handle-mp";	
			//$ru="https://demo.eci.nic.in/suvidhaac/public/payment-return-handle-mp";	
			//$fru="https://demo.eci.nic.in/suvidhaac/public/payment-return-handle-mp";	
			$fru="https://suvidha.eci.gov.in/suvidhaac/public/payment-return-handle-mp";	
		
			$name = trim($depositor_name);
			$add_line1 = $address;
			$add_line2 = $address;
			$add_line3 = $address;
			$add_line4 = $address;
			$add_line5 = $address;
			$add_state = $address;
			$add_pin = "";
			$email=trim($email);
			$mob_no=trim($mobile);	
			$tin = '';	
			$from_date = '01/07/2023';	
			$to_date = '01/07/2024';
			$submajor_head = '02';	
			$minor_head = '104';
			$scheme_code = '0143';	
			$purpose = 'Online Nomination Fee';	
			$amount = $amount;	
			$total_amount = $amount;	
			$remarks = 'Online Nomination Fee';	
			$deptt_code = 21;	
			//$dist_code = 10;	
			//$office_name = 'BALAGHAT TREASURY';	
			$ifsc = '';	
			$account_number = '';	
			$account_name = '';	
			$New1 = '';	
			$New2 = '';	
			$New3 = '';	
			
	
			$req_data = "crn=$crn|ru=$ru|fru=$fru|name=$name|add_line1=$add_line1|add_line2=$add_line2|add_line3=$add_line3|add_line4=$add_line4|add_line5=$add_line5|add_state=MP|add_pin=462022|email=$email|mob_no=$mob_no|tin=$tin|from_date=$from_date|to_date=$to_date|major_head=$major_head|submajor_head=$submajor_head|minor_head=$minor_head|scheme_code=$scheme_code|purpose=$purpose|amount=$amount|circle_code=19|total_amount=$amount|remarks=$remarks|deptt_code=$deptt_code|dist_code=$dist_code|office_name=$office_name|IFSC=|account_number=|account_name=|New1=|New2=|New3=|urn=$urn";
			
			//dd($req_data);
		//	$req_data = "crn=ECI007026092023922541|ru=https://demo.eci.nic.in/suvidhaac/public/payment-return-handle-mp|fru=https://demo.eci.nic.in/suvidhaac/public/payment-return-handle-mp|name=Test Jay|add_line1=Test Address|add_line2=Test Address|add_line3=Test Address|add_line4=Test Address|add_line5=Test Address|add_state=MP|add_pin=462022|email=jitendrasinghjan15@gmail.com|mob_no=8010618155|tin=|from_date=01/07/2023|to_date=01/07/2024|major_head=0070|submajor_head=02|minor_head=104|scheme_code=0143|purpose=Online Nomination Fee|amount=1|circle_code=19|total_amount=1|remarks=Online Nomination Fee|deptt_code=21|dist_code=10|office_name=BALAGHAT TREASURY|IFSC=|account_number=|account_name=|New1=|New2=|New3=|urn=MPTECI260920239225419";
	
	
	
	$enc_data = $req_data.'|checkSum='.md5($req_data);
	
	
		 echo '--------->Request Data ';
			echo $enc_data;
			echo ' --------->Request Data '; 
	
		$encdata = encrypt_mp($enc_data);
		
		 echo '</br>';
		
		echo '--------->Request Data encdata ';
			echo $encdata;
			echo ' --------->Request Data encdata'; 
		
		
		//dd($encdata);

			//$url ='https://117.240.217.54/MPCTP/IntegrationAgentGMT';
			$url ='https://mptreasury.gov.in/IntegrationAgentGMT';
			?>
				<form method="post" id="paymentred" name="paymentred" action="{{$url}}">
					<input type="hidden" name="merchant_code" id="merchant_code" value="{{$merchant_code}}">
					<input type="hidden" name="ctp_data" value="{{$encdata}}">
				</form>

	<?php } ?>				



	<?php }  // Session::flash('is_payment',"yes"); ?>
	
	<?php 
		$payment_response='NA';
		$pay_message = '';
		if((count($psta)>0)){
			if(!empty($psta[0]->bank_transaction_status)){ 
				if($psta[0]->bank_transaction_status=='2'){
					$payment_response='Pending';
					$pay_message =  __('csa.Payment_pending');
				}
				if($psta[0]->bank_transaction_status=='1'){
					$payment_response='Success';
					$pay_message =  __('csa.Payment_done');
				}
				if($psta[0]->bank_transaction_status=='3'){
					$payment_response='Failed';
					$pay_message =  __('csa.Payment_failed');
				}
				if($psta[0]->bank_transaction_status=='4'){
					$payment_response='Aborted';
					$pay_message =  'Transaction Aborted';
				}
			}else{
				$payment_response='NA';
				$pay_message = 'Payment status is unknown.';
			}
		}
	?>
	
	<!-- Modal confirm schedule --> 
    <div class="modal fade modal-confirm" id="payment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  @if($payment_response=='Success')
		  <div class="animte-tick"><span>&#10003;</span></div>	
		  @elseif($payment_response=='Failed')
		  <div class="animte-tick failed-tick"><span>&#215;</span></div>
		  @elseif($payment_response=='Pending')
		  <div class="animte-tick pending-tick"><span>&#33;</span></div>
		  @else($payment_response=='')
		  <div class="animte-tick pending-tick"><span>&#33;</span></div>
		  @endif
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p id="challanMessage1">{{$pay_message}}.</p>	
		</div>		
        </div> 
		<br>
		@if($payment_response=='Success')
		<span style="text-align: center;" id="txtchalan1">{{ __('step1.nowFina') }}</span> 
		<div class="text-center py-3"><a id="txt_final1" href="#" class="btn btn-success btn-big"  onclick="return moveLoader('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">{{ __('messages.fn') }}</a>
		&nbsp; &nbsp; &nbsp;
		
		<span class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('step1.Cancel') }}</button>
        </span>
		</div>
		@else
		<div class="text-center py-3">
		&nbsp; &nbsp; &nbsp;
		
		<span class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
        </span>
		</div>
		@endif
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div --> 
  
  <!-- Modal confirm schedule --> 
    <div class="modal fade modal-confirm" id="nopayment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p id="challanMessage1">Payment status not available.</p>	
		</div>		
        </div> 
		<br>
		&nbsp; &nbsp; &nbsp;
		
		<span class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
        </span>
		</div>	
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div --> 
	
<!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="nopdf_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p style="color:red;font-size:20px;">{{ __('messages.pleasePDF') }}</p>	
		</div>		
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->		
  
  
  
 <!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="pay_by_cash_message">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">  
		  <p id="challanMessage3">{{ __('messages.payByCashSucc') }}</p>	
		</div>		
        </div>
		<br>
		<span style="margin-left:60px;text-align:center;" id="txtchalan3">{{ __('step1.nowFina') }}</span> 
		
		 <span style="text-align: center;display:none;margin-left: 9em;" id="L123">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		<div class="text-center py-3"><a id="txt_final3" href="#" class="btn btn-success btn-big"  onclick="return moveLoader('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">{{ __('messages.fn') }}</a>
		&nbsp; &nbsp; &nbsp;
		
		<span class="confirm-footer">
		  <button type="button" id="c123" class="btn dark-pink-btn" data-dismiss="modal">{{ __('step1.Cancel') }}</button>
        </span>
		
		</div>	
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->	
	<!-- bank_model-->
    <div class="modal fade modal-confirm" id="bank_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p>{{ __('csa.success') }}.</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
        
      </div>
    </div>
  </div><!-- End Of bank_model -->
	
 <!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="challan_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p id="challanMessage">{{ __('messages.challanSUcc') }}</p>	
		</div>		
        </div>
		<br>
		<span style="margin-left:60px;text-align:center;" id="txtchalan">{{ __('step1.nowFina') }}</span> 
		
		<span style="text-align: center;display:none;margin-left: 9em;" id="L3333">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		
		<div class="text-center py-3"><a id="txt_final" href="#" class="btn btn-success btn-big"  onclick="return moveLoader('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">{{ __('messages.fn') }}</a>
		&nbsp; &nbsp; &nbsp;
		
		  <button type="button" id="c333" class="btn dark-pink-btn" data-dismiss="modal">{{ __('step1.Cancel') }}</button>
		<span class="confirm-footer">
        </span>
		
		</div>	
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->	
	<!-- bank_model-->
    <div class="modal fade modal-confirm" id="bank_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p>{{ __('csa.success') }}.</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
        
      </div>
    </div>
  </div><!-- End Of bank_model -->

  		<div class="modal fade modal-confirm" id="finalizeNomination">
		 <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/finalize-nomination-payment') }}" autocomplete='off' enctype="x-www-urlencoded">
		{{ csrf_field() }}
		<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
		  <div class="modal-content">
		   <div class="pop-header pt-3 pb-1">
			  <div class="animte-tick"><span>&#10003;</span></div>	
			<h5 class="modal-title">{{ __('messages.fn') }}</h5> 
			<div class="header-caption">
			</div>		
			</div>
			
			<?php 
			$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($stndn, $acnnod);
			$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($stndn);
			?>
			<div class="modal-body" style="text-align: center;">
			<ul>
			<li><label  id="oneqqq">{{ __('messages.fnsure') }}</li>
			 </ul> 
			</div>
			<div class="confirm-footer" id="oneqqq2">
			<input type="hidden" id="messageNeedToShow">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="button" class="btn btn-primary" style="background: #007bff; border: none;" id="reareyu" onclick="return showLoaderP('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">
			{{ __('messages.Yes') }}</button>
			</div>
			<button id="oneqqq3" type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;display:none;">{{ __('nomination.ok') }}</button>
			
			  <span style="text-align: center;display:none;" id="ppppppppppppp">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
			</span>
		  
			
		  </div>
		</div>
		</form>
	  </div><!-- End Of confirm Modal popup Div -->
								




  
	<!-- Optional JavaScript -->
    <!-- jQuery Bootstrap JS -->
    <script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }} " type="text/javascript"></script>
	<?php include('appoinment/js/week-scheduale.php'); ?>
	<script src="{{ asset('appoinment/js/week-scheduale.php') }} " ></script>	
    <script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
	
	<script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>	
	<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>  
	<script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>
	 <script type="text/javascript">  
	 
		$.ajaxSetup({
		  headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});
		$("#checkPdStatus").click(function(){
			var getGrn = $(this).attr("data-grn");
			
			if(getGrn !=''){
				$("#pststicon").show();
				$.ajax({
					type: "POST",
					url: "{{ url('payment-verification-pd') }}",
					data: {
						
					},
					success: function(data) {
						location.reload();
					}
				});
			}
		});
		
		$("#checkTmStatus").click(function(){
			var getGrn = $(this).attr("data-grn");
			
			if(getGrn !=''){
				$("#pststicon").show();
				$.ajax({
					type: "POST",
					url: "{{ url('payment-verification-tm') }}",
					data: {
						
					},
					success: function(data) {
						location.reload();
					}
				});
			}
		});
		$("#printPdChallan").click(function(){
			var getGrn = $(this).attr("data-grn");
			
			if(getGrn !=''){
				$("#pststicon").show();
				$.ajax({
					type: "POST",
					url: "{{ url('print-challan-pd') }}",
					data: {grn:getGrn,_token:$('meta[name="csrf-token"]').attr('content')},
					success: function(data) {
						//location.reload();
					}
				});
			}
		});
		function moveLoader(st, ac){     
						var fnp = jQuery.noConflict();	
						fnp("#L333").show();
						fnp("#L123").show();
						
		
						
						$.ajax({
						type: "POST",
						url: "<?php echo url('/'); ?>/nomination/finalize-nomination-payment", 
						data: {
							"_token": "{{ csrf_token() }}",
							"st_code": st,
							"ac_no": ac,
							},
						dataType: "html",
						success: function(msg){     
						const match = msg.match(/\d+$/);    
						//if(msg==1){	  
							if(match==1 || msg==1){	
							fnp('#txtchalan').html('<?php echo __('step1.finalizedMessage'); ?>');	
							fnp('#txtchalan1').html('<?php echo __('step1.finalizedMessage'); ?>');	
							fnp('#txtchalan3').html('<?php echo __('step1.finalizedMessage'); ?>');	
							fnp('#c123').html('<?php echo __('nomination.ok'); ?>');	
							fnp('#c333').html('<?php echo __('nomination.ok'); ?>');	
							fnp('#challanMessage').html('');	 
							fnp('#challanMessage1').html('');	 
							fnp('#challanMessage3').html('');	
							fnp("#L123").hide();	
							fnp("#L333").hide();	
							fnp('#txt_final').hide();	
							fnp('#txt_final1').hide();	
							fnp('#txt_final3').hide();	
							fnp('#online_pay2').hide();	
							fnp('#ttump').hide();	
							fnp('#NeedToShowSucceess').show();	
						} else {
							fnp('#challanMessage').html('<?php echo __('step1.someissue'); ?>');	
							fnp('#challanMessage1').html('<?php echo __('step1.someissue'); ?>');	
							fnp('#challanMessage3').html('<?php echo __('step1.someissue'); ?>');	
							fnp('#c123').html('<?php echo __('nomination.ok'); ?>');	
							fnp('#c333').html('<?php echo __('nomination.ok'); ?>');	
							fnp("#L333").hide();	
							fnp("#L123").hide();	
							fnp('#txtchalan').html('');	
							fnp('#txtchalan1').html('');	
							fnp('#txtchalan3').hide();	
							fnp('#txt_final').hide();	
							fnp('#txt_final1').hide();	
							fnp('#txt_final3').hide();	
						}	
						 
						},
						error: function(error){
							fnp("#fnp").hide();
							console.log(error);
							console.log(error.responseText);				
							var obj =  $.parseJSON(error.responseText);
						}
					  });
		}
		
		
		function showLoaderP(st, ac){  
						var fnp = jQuery.noConflict();	
						fnp("#fnp").show();
						fnp("#ppppppppppppp").show();
						$.ajax({
						type: "POST",
						url: "<?php echo url('/'); ?>/nomination/finalize-nomination-payment", 
						data: {
							"_token": "{{ csrf_token() }}",
							"st_code": st,
							"ac_no": ac,
							},
						dataType: "html",
						success: function(msg){ 
						const match = msg.match(/\d+$/);
						if(match==1 || msg==1){	 
						//if(msg==1){	
							var messageNeedToShow=fnp("#messageNeedToShow").val(); 
							fnp('#challan_model').modal('hide');
							fnp("#ppppppppppppp").hide();
							fnp("#fnp").hide();
							fnp("#oneqqq3").show();
							fnp("#online_pay3").show();
							fnp("#NeedToShowSucceess").show();
							fnp("#oneqqq2").hide();
							fnp("#ttump").hide();
							fnp("#online_pay2").hide();
							fnp("#oneqqq").html("<?php echo __('messages.nmf'); ?>"); 
							fnp("#"+messageNeedToShow).html("<?php echo __('messages.nmf'); ?>");
							
						} else {
							fnp('#challan_model').modal('hide');
							fnp("#ppppppppppppp").hide();
							fnp("#fnp").hide();
							fnp("#oneqqq").html("<?php echo __('messages.delIssue'); ?>"); 
							fnp("#oneqqq3").show();
							fnp("#oneqqq2").hide();
						}	
						 
						},
						error: function(error){
							fnp("#ppppppppppppp").hide();
							fnp("#fnp").hide();
							console.log(error);
							console.log(error.responseText);				
							var obj =  $.parseJSON(error.responseText);
						}
					  });
		}
	 
		function showHidePay(nu){ 
			if(nu==1){
				var pay = jQuery.noConflict();	
						  pay("#submit_challan").show();
						  pay("#paySec").hide();
						  pay("#payGuj").hide();
			}
			if(nu==2){
				var pay = jQuery.noConflict();	
						  pay("#submit_challan").hide();
						  pay("#paySec").show();
						  pay("#payGuj").hide();
			}
			if(nu==3){
				var pay = jQuery.noConflict();	
						  pay("#submit_challan").hide();
						  pay("#paySec").hide();
						  pay("#payGuj").show();
			}
			
		}
	 
	 	
		var j = jQuery.noConflict();	
	   j(document).ready(function() {
              var owl = j('.owl-carousel');
              owl.owlCarousel({
                margin: 2,
                nav: true,
                loop: true,
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items: 1
                  },
                  1000: {
                    items: 1
                  }
                }
              });
		  }); 
		
		
		<?php if(session('nopdf')!==null){ 
		if(session('nopdf') == 'nopdf'){ ?>
		var sd = jQuery.noConflict();	
		sd('#nopdf_model').modal('show');
		<?php } } ?> 
		
		<?php if(session('challan_message')!==null){ 
		if(session('challan_message') == 'challan_message'){ ?>
		var sd = jQuery.noConflict();	
		sd('#challan_model').modal('show');
		<?php } } ?> 	
		
		<?php if(session('pay_by_cash_message')!==null){ 
		if(session('pay_by_cash_message') == 'pay_by_cash_message'){ ?>
		var sd = jQuery.noConflict();	
		sd('#pay_by_cash_message').modal('show');
		<?php } } ?> 	
		
		
		 var dt = jQuery.noConflict();	
		 dt( function() {
			dt( "#challan_date" ).datepicker({
				dateFormat: 'dd-mm-yy',
                maxDate: 0,
                changeYear: true
			});
			  
		  } );
		  
		  
		
		
		 challan_date.max = new Date().toISOString().split("T")[0];
		 
		    var zz = jQuery.noConflict();	
		 zz(document).ready(function(){ 
		 zz('#challan_receipt').bind('change', function() {
					var a=(this.files[0].size);
					//alert(a);
					if(a > 4000000) {
						zz("#preError").html("<?php echo __('messages.fsize'); ?>");
						zz("#challan_receipt").val("");
						zz("#challan_receipt").focus();
						zz('#previewId').hide();
						return false;
					};
				});
			});	
		 
		 $(document).ready(function(){
          $("#challan_receipt").change(function(){
            var pre = jQuery.noConflict();	
			var challan_receipt= pre("#challan_receipt").val();
			var file_type = challan_receipt.substr(challan_receipt.lastIndexOf('.')).toLowerCase();
			
			if (file_type  != '.pdf' && file_type  != '.jpg' && file_type  != '.JPEG' && file_type  != '.PNG' && file_type  != '.png') {
					pre("#preError").html("<?php echo __('messages.chalaanPDF'); ?>");
					pre("#challan_receipt").val("");
					pre("#challan_receipt").focus();
					pre('#previewId').hide();
					return false;
			}
			
			
				
				
			pre("#preError").html("");
		    var file =document.getElementById("challan_receipt").files[0];
		    var url =URL.createObjectURL(file );
		    pre('#previewId').show();
		    pre('#iframeid').attr('src',url);
        });
		});
		
		function payByCashSubmit(){
			var aaaa = jQuery.noConflict();
			aaaa("#payByCash").val('payByCash');
		}	
		 
		  
		function submitChallan(){
			var f = jQuery.noConflict();
			
			
			
			var challan_number = f("#challan_number").val();
			var challan_date =   f("#challan_date").val();
			var challan_receipt= f("#challan_receipt").val();
			var file_type = challan_receipt.substr(challan_receipt.lastIndexOf('.')).toLowerCase();
			
			
			
			
			if(challan_number=='' || challan_number.length<3){ 
				alert("<?php echo __('messages.correctChalan'); ?>");
				f("#challan_number").focus();
				return false;
			}
			
			if(challan_date=='' ){
				alert("<?php echo __('messages.challanDateError'); ?>");
				f("#challan_date").focus();
				return false;
			}
			
			if (file_type  != '.pdf' && file_type  != '.jpg' && file_type  != '.JPEG' && file_type  != '.PNG' && file_type  != '.png') {
					f("#preError").html("<?php echo __('messages.chalaanPDF'); ?>");
					f("#challan_receipt").val("");
					f("#challan_receipt").focus();
					f('#previewId').hide();
					return false;
			}
			
			f("#afhbdsfbdjg").show();
		}	
		  
		  
		 
		 function redirect_gujrat(){
			var j = jQuery.noConflict();
			<?php if($payState=='S06'){ ?>
			$.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/save-payment-details-gujrat_our_end", 
				data: {
					"_token": "{{ csrf_token() }}",
					"sId": "{{$Guj_state}}",
					"dist_code": "{{$dist_code}}",
					"ac":  "{{$Guj_AC}}",
					"amount1": "{{$Total_amount}}",
					"txn_amount": "{{$Total_amount}}",
					"reff_no": "{{$Transaction_id}}"
					},
				dataType: "html",
				success: function(msg){ 
				  if(msg==1){
					document.redGuj.submit();
				  } 
				},
				error: function(error){
					console.log(error);
					console.log(error.responseText);				
					var obj =  $.parseJSON(error.responseText);
				}
			  });
			<?php } ?> 
		}   
		 
		 
		 
		function redirectPayment(){
			var j = jQuery.noConflict();	
			<?php if($isPaymentConfig=='YES'){
			?>
			$.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/save-payment-details-our-end", 
				data: {
					"_token": "{{ csrf_token() }}",
					"sId": "{{$st_save}}",
					"dist_code": "{{$dist_code}}",
					"ac":  "{{$ac_save}}",
					"amount": "{{$amount}}",
					"txn_amount": "{{$amount}}",
					"reff_no": "{{$reff_no}}",
					"depositor_name": "{{$depositor_name}}",
					"depositor_address": "{{$address}}",
					"challanno": "{{$challanno}}"
					},
				dataType: "html",
				success: function(msg){			
					msg = JSON.parse(msg)
				  if(msg.result==1){	
					
					if(msg.st_code=='S24'){
						window.location = ''+msg.redirect_url+'';						
					}else{ 
						document.paymentred.submit();
					}  
					 
				  } 
				},
				error: function(error){
					console.log(error);
					console.log(error.responseText);				
					var obj =  $.parseJSON(error.responseText);
				}
			  });
			<?php } ?> 
		}  

		
		
		function finalizeNomination(id){ 
			var dsd = jQuery.noConflict();	
			dsd("#messageNeedToShow").val(id);
			dsd('#finalizeNomination').modal('show');
		}
		
		function finalizeNominationTwo(id){ 
			var pppnnbb = jQuery.noConflict();	
			pppnnbb('#challan_model').modal('hide');
			pppnnbb('#finalizeNomination').modal('show');
		}
		
		
		function showPaymentDetails(){
			var j = jQuery.noConflict();	
			j('#paymentPopUp').modal('show');
		}  
		
		function red(){
		var j = jQuery.noConflict();	
		 j('#red').modal('show');	
			
		}
		  
		function showLoader(){
		 $("#loader3").show();	
		}  
		function showsc(){
		 $("#loader").show();	
		} 
		
		function showBankDetails(){
			var j = jQuery.noConflict();	
			j('#bankDetails').modal('show');
		}
		
		<?php if(session('is_payment')!==null){ 
		if(session('is_payment') == 'yes'){ ?>
		var j = jQuery.noConflict();	
		j('#payment').modal('show');
		<?php } } ?> 	
		
		<?php if(session('is_payment')!==null){ 
		if(session('is_payment') == 'no'){ ?>
		var j = jQuery.noConflict();	
		j('#nopayment').modal('show');
		<?php } } ?>
		
		<?php if(session('is_scheduled')!==null){ 
		if(session('is_scheduled') == 'yes'){ ?>
		$('#confirm').modal('show');
		<?php } } ?> 
		<?php if(session('is_scheduled')!==null){ 
		if(session('is_scheduled') == 'cancel'){ ?>
		$('#cancel').modal('show');
		<?php } } ?> 
		<?php if(session('bank')!==null){ 
		if(session('bank') == 'bank'){ ?>
		var j = jQuery.noConflict();	
		j('#bank_model').modal('show');
		<?php } } ?> 
		
		function valBank(){
			var j = jQuery.noConflict();	
			var candidate_name = j("#candidate_name").val();
			var bank_name = j("#bank_name").val();
			var account_number = j("#account_number").val();
			var confirm_account_number = j("#confirm_account_number").val();
			var ifsc_code = j("#ifsc_code").val();
			
			if(candidate_name==''){
				alert("<?php echo __('csa.cand_error'); ?>");
				j("#candidate_name").focus();
				return false; 
			}
			if(bank_name==''){ 
				alert("<?php echo __('csa.bank_error'); ?>");
				j("#bank_name").focus();
				return false; 
			}
			if(account_number==''){ 
				alert("<?php echo __('csa.accoun_error'); ?>");
				j("#account_number").focus();
				return false; 
			}
			if(confirm_account_number==''){ 
				alert("<?php echo __('csa.con_accoun_error'); ?>");
				j("#confirm_account_number").focus();
				return false; 
			}
			
			if(confirm_account_number!=account_number){ 
				alert("<?php echo __('csa.same_error'); ?>");
				j("#account_number").focus();
				return false; 
			}
			
			if(ifsc_code==''){ 
				alert("<?php echo __('csa.IFSC_Code_error'); ?>");
				j("#ifsc_code").focus();
				return false; 
			}
			
		}
		
		function showhide(){
			$("#slip").hide();
			//$("#textmsg").hide();
			$("#call").show();
			$("#scheduleButton").text("<?php echo __('messages.res'); ?>");
			$("#areyu").text("<?php echo __('messages.are_you_sure_p'); ?>"); 
			$("#reareyu").text("<?php echo __('messages.Yes'); ?>");
		}
		function showhide2(){
			$("#slip").show();
			$("#call").hide();
		}
		
		function submitScheduled(){
			
		    var datad = $("#daytime").val();
			if(datad == ''){ 
			  //alert("<?php echo __('messages.pleasesel'); ?>");	srjkgdfn
			  $('#emptySlot').modal('show');
			return false;
			}	
			//alert($("#daytime").val());
			$('#basicExampleModal2').modal('show');
			var sch = $("#daytime").val();
			var darr = sch.split('___');
			var dated = darr[1].split('***');
			
			var exacttme = darr[0];
			$("#ampm").text(exacttme);
			var exactdate = dated[1];
			
			var dates = exactdate.split('-');
			var cdd = dates[2]+'-'+dates['1']+'-'+dates['0']	
			$("#datea").text(cdd);	
	
			var myDate = new Date(dated[1]);
			var dayname = myDate.toString().split(' ')[0];
			$("#dayname").text(dayname);
			
			
			 //if(confirm("Are you sure to schedule appoinment?")==true){
			 //document.appoinment_form.submit();	
			//}
		}
		
		 
		function getval(daytime, i){ //alert(i);
			
			
var dated = daytime.split('***');
var dated1 = dated[1].split('-');
		
			var timezone = daytime.split('___');
			var hour_min = timezone[0].split(':');
			
		var currentTime = new Date()
		var month = currentTime.getMonth() + 1
		var day = currentTime.getDate();
		var year = currentTime.getFullYear() ;
    var date1_compare='';
    var date2_compare='';
		var date1_compare = new Date ( month-1, day, year);
    var date2_compare = new Date (8, 10,dated1[0]);
   // console.log(month,day,year)
   // console.log(dated1[1],dated1[2],dated1[0])
     
    const monthss = (currentTime.getMonth() + 1).toString().padStart(2, '0');
    const dayss = currentTime.getDate().toString().padStart(2, '0');

    var set_to=currentTime.getFullYear()+"-"+monthss+"-"+dayss;
   // const date1 = new Date(dated[1])
    //const date2 = new Date(set_to)

	  const hours = currentTime.getHours().toString().padStart(2, '0');
	  const minutes = currentTime.getMinutes().toString().padStart(2, '0');

	  var selected_date_time = new Date ( hour_min[0], hour_min[1]);
    var current_date_time = new Date ( hours, minutes);

var regex = new RegExp(':', 'g'),
    timeStr1 = hour_min[0]+':'+hour_min[1],
    timeStr2 = hours+':'+minutes;
// if(parseInt(timeStr1.replace(regex, ''), 10) < parseInt(timeStr2.replace(regex, ''), 10)){
//   console.log('timeStr1 is smaller then timeStr2');
// } else {
//   console.log('timeStr2 is smaller then timeStr1');
// }
  
	   if (dated[1] > set_to) {
     
     }else if (dated[1] < set_to){
     
     }else{
//alert(timeStr1);
  	if(parseInt(timeStr1.replace(regex, ''), 10) < parseInt(timeStr2.replace(regex, ''), 10)){
		alert("Please Verify Selected Time Slot");
		return false;
   }
 }



		
			if ($.inArray(daytime, abc)!='-1') {
				if(abc.length>=3){	
				   var index = abc.indexOf(daytime);
					abc.splice(index, 1);
					$("#testing").val(abc);
					$("#"+i).css('background-color', 'white');
				return false;	
			}
			}	
			
			
			if(abc.length>=3){
			//alert("<?php echo __('messages.3max'); ?>");	
			 $('#maxError').modal('show');
			return false;	
			}
			
			
			var getabc = $("#testing").val();
				if ($.inArray(daytime, abc)!='-1') {
				   var index = abc.indexOf(daytime);
					abc.splice(index, 1);
					$("#testing").val(abc);
					$("#"+i).css('background-color', 'white');
				} else {
					    abc.push(daytime);
						$("#testing").val(abc);
						$("#"+i).css("opacity", 1);
						$("#"+i).css("background-color", 'yellow');
				}
			
			
			//alert(getabc);
			
			
			
			$("#daytime").val(daytime);
			var mdaat = daytime.replace(":", "");
			var mdaat = mdaat.replace("___", "");
			var mdaat = mdaat.replace("***", "");
			//$(".time-slot").css('background', '');
			//$("."+mdaat).css('background', '#f0587e');
		}

	  //Current Date-Month-Year	 
	    var today = new Date();
		var date = today.getDate(); 
		var dmy = today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear(); 
		document.getElementById("dmy").innerHTML = dmy;
		 //Calendar Schedule  
		(function ($) {
		  $("#day-schedule").dayScheduleSelector({
			/*
			days: [1, 2, 3, 5, 6],
			interval: 15,
			startTime: '09:50',
			endTime: '21:06'
			*/
		  });
		  $("#day-schedule").on('selected.artsy.dayScheduleSelector', function (e, selected) {
			console.log(selected);
		  })
		  $("#day-schedule").data('artsy.dayScheduleSelector').deserialize({
			'0': [['09:30', '11:00'], ['13:00', '16:30']]
		  });
		})($);
	  
	 </script> 
  </body>
  
  
  
@endsection
