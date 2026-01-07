   
@extends('layouts.theme')
  @section('title', 'Nomination')
  @section('content')
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
    </style>
    <script>
	  document.onkeypress = function (event) {
        event = (event || window.event);
        if (event.keyCode == 123) {
          return false;
        }
      }
      document.onmousedown = function (event) {
        event = (event || window.event);
        if (event.keyCode == 123) {
          return false;
        }
      }
      document.onkeydown = function (event) {
        event = (event || window.event);
        if (event.keyCode == 123) {
          return false;
        }
      }
    </script>
    <!--CODE FOR Block F12 Key-->
    <script language="javascript">
	function setVisibility(id,btnval) {
	  if(document.getElementById("btn"+btnval).value=='-'){
	  document.getElementById("btn"+btnval).value = '+';
	  document.getElementById(id).style.display = 'none';
	  }else{
	  document.getElementById("btn"+btnval).value = '-';
	  document.getElementById(id).style.display = 'inline';
	  }
	}
    </script>

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
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }}" type="text/css">
	<title>Schedule Appointment</title>
   </head>
   <body>  
   <?php //echo $is_appoinment_scheduled_for_one; die;  ?>
	@if($is_appoinment_scheduled_for_one==1 or $is_appoinment_scheduled_for_one==2)
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
	<main class="pt-3 pb-5 pl-5 pr-5">
	 <div class="container-fluid">
	   <div class="card">
		 <div class="card-header">
		   <div class="row">
			<div class="col-md-6 col-12"><h4>{{ __('csa.Appointment_Details') }}</h4> </div> 
			<div class="col-md-6 col-12"></div> 
		   </div>	 
		 </div> 
		 <div class="card-body">
		   <div class="row">
			<div class="col-md-4 col-12 pr-2">
			  <div class="nomin-list" style="height: 356px;">
			  <div class="owl-carousel owl-theme">
			  
			  <?php 
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
				$stst=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($NOMNO); 
				?>
				<div class="item">
				<div class="appnt-detail list-detail">
					<h4 class="text-center d-flex justify-content-between"><b>{{$d}}</b>{{$NOMNO}}</h4>
					<ul>
					<li><strong> {{ __('nomination.Name') }}</strong> <span>{{$candidate_name}}</span></li>
					<li><strong>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}</strong> <span>{{$ACNO}} - {{$ACname}}</span></li>
					<li><strong>{{ __('nomination.Status') }}</strong> <span>@if($stst!='0'){{$stst}}@else{{$appoinment_status}}@endif</span></li>
					<li><strong>{{ __('nomination.State') }}</strong> <span>{{$state}}</span></li>
					<li><strong>{{ __('nomination.Election') }}</strong> <span>{{$election_name_one}}</span></li>
					<li><strong>{{ __('nomination.Party') }}</strong> <span>{{$party}}</span></li>
				  </ul> 
					<div class="row m-0 p-3">
					  <div class="col-md-4 col-12 p-0"><strong>{{ __('nomination.Action') }}</strong></div>  
					  <div class="col-md-8 col-12 p-0 text-right">
						 <div class="apt-btn">
						  <a href="{{$view_href_cust}}?acs=<?php echo encrypt_String($ACNO); ?>&std=<?php echo encrypt_String($std); ?>" class="btn sm-btn dark-pink-btn">{{ __('nomination.View') }}</a>  
						  <a href="{{$download_href_cust}}" class="btn sm-btn dark-purple-btn">{{ __('nomination.Download') }}</a>  
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
                       <div class="booked-appoint">
					     <div class="book-header">
						  <div class="d-flex justify-content-between">
							<div>
							  <ul class="list-inline">
								<li class="list-inline-item mr-4">{{ __('csa.Appointment') }}</li>
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-calendar" aria-hidden="true"></i></b>&nbsp;{{$appoinment_scheduled_day_one}}</span></li>
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-calendar" aria-hidden="true"></i></b>&nbsp;{{$appoinment_scheduled_date_one}}</span></li>
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></b>&nbsp;{{$appoinment_scheduled_time_one}}</span></li>
							  </ul>  
							</div>  
							<div class="down-print-link">
							  <ul class="list-inline">
								<li class="list-inline-item"><a href="download-scheduled?id=<?php echo $_REQUEST['id'];?>">{{ __('nomination.Download') }} <i class="fa fa-download" aria-hidden="true"></i></a></li>
								<!--<li class="list-inline-item"><a href="#">Print <i class="fa fa-print" aria-hidden="true"></i></a></li>-->
							  </ul>  
							</div>  
						  </div>	 
						   
						 </div><!-- End Of book-header Div -->
						 <div class="book-body p-3">
						   <div class="row">
						     <?php if(isset($_REQUEST['id'])){ 
							$ids = explode(",", $_REQUEST['id']);
							//foreach($ids as $nomid){
							$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($ids[0]);	
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
							$ROname=$str[8];
							$ROaddress1=$str[9];
							$ROaddress2=$str[10];
							$DistName=$str[11];
							?>
							 <div class="col-md-4 col-12"><label>{{ __('csa.RO_Name') }}:</label> {{$ROname}} </div> 
							 <div class="col-md-4 col-12"><label>{{ __('nomination.ac') }} </label> {{$ACNO}} - {{$ACname}}</div> 
							 <div class="col-md-4 col-12"><label>{{ __('csa.District') }}:</label> {{$DistName}}</div> 
							 <div class="col-md-12 col-12 pt-3"><label>{{ __('csa.Address') }}:</label> {{$ROaddress1}} {{$ROaddress2}}</div>
							 <!--<div class="col-md-4 col-12"><label>Status:</label> {{$appoinment_status}}</div> -->
							<?php } } //} ?>	
							</div>	 
						   </div> 
						    <?php 
							 $expd = explode(",", $_REQUEST['id']);
							 $inr=   count($expd)*100;
							 $dmt=1000000;
							 
							
							 $psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus($_REQUEST['id']);
							 
							 $bank = app(App\Http\Controllers\Nomination\NominationController::class)->bankDetails();
							
							 $cEdata = app(App\Http\Controllers\Nomination\NominationController::class)->getEmail();	
							 
							 $bnk='';	
							 if(count($bank)>0){
							  $bnk=__('csa.Edit');	 
							 } else {
							  $bnk=__('csa.Enter'); 
							 }
							 
							?>	
							
							<div class="d-flex justify-content-between align-items-center my-3">
							<div class="info-text"><div onclick="return showBankDetails();" class="btn btn-primary">{{$bnk}} {{ __('csa.Bank_Details') }}</div></div>
										   
		
  
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
								<?php //echo "<pre>"; print_r($bank); ?>
                                
						
								@if(count($bank)>0)
								@if((count($psta)<=0))
								<div>	
									<strong class="pr-2 btn font-big blink-effect"> {{ __('csa.Pay_Security_Deposit_Online') }} </strong> 
                                    <form action="{!!('payment')!!}" method="POST" onclick="showMyform();" style="float: right;">
								
										
                                        <script src="https://checkout.razorpay.com/v1/checkout.js"
                                        data-key="{{ Config::get('razorpay.razor_key') }}"
                                        data-amount="{{$dmt}}"
                                        data-buttontext="Pay"
                                        data-name="Candidate Nomination"
                                        data-description="Candidate Nomination Fee"
                                        data-image="<?php echo url('/'); ?>/img/eci-logo.png"
                                        data-prefill.name="{{$candidate_name}}"
                                        data-prefill.contact="{{$cEdata[0]->mobile}}"
                                        data-prefill.email="{{$cEdata[0]->email}}"
                                        data-prefill.nomination_id="<?php echo $_REQUEST['id']; ?>"
                                        data-theme.color="#ff7529">
                                        </script>
                                        <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                                        <!--a href="<?php echo url('/'); ?>/request_rti" class="btn bck-btn">Back</a
                                        <button class="btn bck-btn" onclick="goBack()">Go Back</button>-->
                                        <script>
                                        function goBack() {
                                            window.history.back();
                                        }
                                        </script>
                                </form>								
                                </div>								
								@else 
								<div><strong class="pr-2">  {{ __('csa.Paid') }} </strong>
							    <span style="color: blue; font-size: 13px; margin-right: 10px; cursor: pointer;" onclick="return showPaymentDetails();">{{ __('csa.Details') }} </span></div>
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
										$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($psta[0]->st_code, $psta[0]->ac_no);
										$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($psta[0]->st_code);
										$expdText = explode(" ", $psta[0]->transaction_time);
										?>	
											
											
											
											<div class="modal-body">
											  <ul>
												<!--<li><label>Transaction Id:</label><span><?php echo $psta[0]->transaction_id;  ?></span></li>-->
												<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}:</label><span><?php echo $psta[0]->ac_no;  ?>-<?php echo $acnamed,', '.$stated; ?></span></li>
												<li><label>{{ __('csa.Payment_Status') }}:</label> <span>{{ __('csa.Done') }}</span></li>
												<li><label>{{ __('csa.Amount') }}:</label> <span>INR <?php echo $psta[0]->transaction_amount;  ?></span></li>
												<li><label>{{ __('csa.Payment_Date') }}:</label><span><?php echo date('d-m-Y', strtotime($psta[0]->transaction_date));  ?></span></li>
												<li><label>{{ __('csa.Payment_Time') }}:</label><span><?php echo $expdText[1];  ?></span></li>
											 </ul> 
											 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }} </p>	
											</div>
											<div class="confirm-footer">
											  <button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">{{ __('nomination.ok') }}</button>
											</div>
										  </div>
										</div>
									  </div><!-- End Of confirm Modal popup Div -->
								
								@endif	
								@endif	
                             </div><!-- End Of d-flex Div -->	
					   </div><!-- End Of booked-appoint Div -->
					   
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
			   <a href="<?php echo url('/') ?>/nomination/confirm-schedule-appointment?query=<?php echo encrypt_String('abc') ?>&id=<?php echo $_REQUEST['id']; ?>&data=<?php echo encrypt_String('abc') ?>" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return showhide();">
			   {{ __('csa.RESCHEDULE_APPOINTMENT') }}</a>  
			</div> 
		 </div>  
	   </div>
	 </div>
   </main> 
  
   
   <main class="pt-3 pb-5 pl-5 pr-5" style="margin-top: -61px;" style="display:block;">
	 
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	<!-- Bank Details-->
	<div class="modal fade" id="bankDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/save-bank') }}" autocomplete='off' enctype="x-www-urlencoded">
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
	<!--End Bank Details-->
	<!-- Modal For Cancel-->
	<div class="modal fade" id="basicExampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/cancel-nomination') }}" autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">{{ __('csa.Cancel_Scheduled_Appointment') }}</h5> 
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" style="border-bottom: 1px solid #e9ecef;">
			<div class="row">
							<div class="col-md-8 col-12">
							  <ul class="list-inline" style="width: 157%;">
							    <input type="hidden" name="nom_id" value="<?php echo $_REQUEST['id']; ?>">
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-calendar" aria-hidden="true"></i></b>{{$appoinment_scheduled_day_one}} </span></li>
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-calendar" aria-hidden="true"></i></b>{{$appoinment_scheduled_date_one}}</span></li>
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></b>{{$appoinment_scheduled_time_one}}</span></li>
							  </ul>  
							</div>   
						  </div>
		  </div>
		  
		  
		
		  <div class="modal-body" style="margin-left:-37px;">
          <ul>
          	<li><label>{{ __('nomination.Name') }}:</label> <span> {{$candidate_name}}  </span></li>
          	<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);	
			if($datadd!="NA"){
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			
			$stst=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($NOMNO); 
			?>
			<li><label>{{ __('nomination.Nomination_No') }}: </label> <span> {{$NOMNO}}</span></li>
          	<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}: </label> <span> {{$ACNO}} - {{$ACname}}</span></li> 
			<?php }}} ?>
			<!--<li><label>Status:</label> <span>@if($stst!='0'){{$stst}}@else{{$appoinment_status}}@endif</span></li>-->
		   </ul>
		</div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" onclick="return showmsg('loader3');">{{ __('csa.Cancel_Scheduled_Appointment') }}</button>
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
	  <form name="appoinment_form" id="appoinment_form" method="POST"  action="{{url('/nomination/confirm-schedule-appointment/post') }}" autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="areyu">{{ __('finalize.Confirmation') }}</h5>			
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <input type="hidden" name="daytime" id="daytime">
		  <input type="hidden" name="id" value="{{$_REQUEST['id']}}">
		  <input type="hidden" name="slot" id="slot">
		  <div class="modal-body" style="border-bottom: 1px solid #e9ecef;">
			<div class="row">
							<div class="col-md-8 col-12">
							  <ul class="list-inline" style="width: 157%;">
							    <input type="hidden" name="nom_id" value="{{$nom_id}}">
								<li class="list-inline-item mr-4" style="font-size: 17px;">
								{{ __('csa.are_you_sure') }} <b><span id="datea" style="font-weight:bold;"></span></b> {{ __('csa.and_time_slot') }} <b><span id="ampm"  style="font-weight:bold;"></span> </b>
								</li>	
							  </ul>  
							</div>   
						  </div>
		  </div>
		  <!--<div class="modal-body" style="margin-left:-37px;">
          <ul>
          	<li><label>Name:</label> <span> {{$candidate_name}}</span></li>
			
			<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);	
			if($datadd!="NA"){
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>Nomination No.: </label> <span> {{$NOMNO}}</span></li>
          	<li><label>AC No. &amp; Name: </label> <span> {{$ACNO}} - {{$ACname}}</span></li>
			  
			<?php } } } ?>  -->
			<!--<li><label>Election</label> <span>GENERAL-2020</span></li>
			<li><label>Status:</label> <span> {{$appoinment_status}}</span></li>-->
		   </ul>
		   
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" id="reareyu" onclick="return showmsg('loader2');">{{ __('csa.Schedule') }}</button>
		  </div>
		  
		   <span style="text-align: center;display:none;" id="loader2">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
			</span>
		  
		</div>
		</div>
	  </div>
	  </form>
	</div> 
	
	
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
    <div class="modal fade modal-confirm" id="payment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p>{{ __('csa.Payment_done') }}.</p>	
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
  
  <!-- footerMod confirm schedule -->
    <div class="modal fade modal-confirm" id="footerMod" style="margin-left:100px;padding:5px;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div  style="margin-left:100px;padding:5px;">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title"></h5>
		<div class="header-caption">
		  <img src="{{ asset('appoinment/loader.gif') }}" height="200" width="200"></img>	
		  <p style="padding:10px;">{{ __('nomination.Please_Wait') }}</p>	
		</div>		
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		</div>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
  
  
  
  
	 
	 
	<!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="confirm">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5>
		<div class="header-caption">
		  <p> {{ __('csa.success_meesage') }}.</p>	
		  <ul class="list-inline">
			<li class="list-inline-item mr-4">{{ __('csa.Appointment') }}</li>
			<li class="list-inline-item"><span class="list-bg"><b class="circle-icon">
			<i class="fa fa-calendar" aria-hidden="true"></i></b> {{$appoinment_scheduled_date_one}}</span></li>
			<li class="list-inline-item"><span class="list-bg"><b class="circle-icon">
			<i class="fa fa-clock-o" aria-hidden="true"></i></b> {{$appoinment_scheduled_time_one}}</span></li>
		  </ul>
		</div>		
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <ul>
          	<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);
			if($datadd!="NA"){
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($NOMNO);
			
			?> 
			

			<li><label>{{ __('nomination.Nomination_No') }}:</label> <span>{{$NOMNO}}</span></li>
          	<li><label>{{ __('nomination.ac') }}  &amp; {{ __('nomination.Name') }} :</label> <span>{{$ACNO}} - {{$ACname}}</span></li>
			<?php }}} ?>
			<li><label>{{ __('csa.Status') }}:</label> <span>@if($datadd!=0){{$datadd}}@else{{$appoinment_status}}@endif</span></li>
		 </ul>
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
  
  
  <!-- Modal Cancel on success-->
    <div class="modal fade modal-cancel" id="cancel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5> 
		<div class="header-caption">
		  <p>{{ __('csa.cancel_info') }}.</p>	
		  <!--<ul class="list-inline">
			<li class="list-inline-item mr-4">Appointment</li>
			<li class="list-inline-item"><span class="list-bg"><b class="circle-icon">
			<i class="fa fa-calendar" aria-hidden="true"></i></b> {{$appoinment_scheduled_date_one}}</span></li>
			<li class="list-inline-item"><span class="list-bg"><b class="circle-icon">
			<i class="fa fa-clock-o" aria-hidden="true"></i></b> {{$appoinment_scheduled_time_one}}</span></li>
		  </ul>-->
		</div>		
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <ul>
          	<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);	
			if($datadd!="NA"){
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			
			<li><label>{{ __('nomination.Nomination_No') }}:</label> <span>{{$NOMNO}}</span></li>
          	<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}:</label> <span>{{$ACNO}} - {{$ACname}}</span></li>
			<?php }}} ?>
			
			<li><label>{{ __('csa.Status') }}:</label> <span>{{$appoinment_status}}</span></li>
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
  </div><!-- End Of confirm Modal popup Div -->

	<div id=""></div> 
   </main>   
   
   
   
	<!-- Optional JavaScript -->
    <!-- jQuery Bootstrap JS -->
    <script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>	
	<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>  
	<script type="text/javascript">
		$(".close").click(function(){
		  //alert("The paragraph was clicked.");
		});
				 
	
		function showmsg(id){ 
		  var j = jQuery.noConflict();	
		  j('#'+id).show();
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
	
	</script>
	
	
	
	
	<script type="text/javascript"> 
		function showPaymentDetails(){
			var j = jQuery.noConflict();	
			j('#paymentPopUp').modal('show');
		}
		
		function showMyform(){
			var j = jQuery.noConflict();	
			j('#footerMod').modal('show');
		}
		
		function showBankDetails(){
			var j = jQuery.noConflict();	
			j('#bankDetails').modal('show');
		}
		function resetDate(){
			var j = jQuery.noConflict();		
			j("#sd").val('');	
			j("#sslot").val('');	
			j("#textmsg").hide();	
		}


		<?php if(session('is_payment')!==null){ 
		if(session('is_payment') == 'yes'){ ?>
		var j = jQuery.noConflict();	
		j('#payment').modal('show');
		<?php } } ?> 	
		
		<?php if(session('bank')!==null){ 
		if(session('bank') == 'bank'){ ?>
		var j = jQuery.noConflict();	
		j('#bank_model').modal('show');
		<?php } } ?> 
		
		
		
		<?php if(session('isSch')!==null){ 
		if(session('isSch') == 'yes'){ ?>
		var j = jQuery.noConflict();	
		j('#confirm').modal('show');
		<?php } } ?> 
		
		<?php if(session('is_scheduled')!==null){ 
		if(session('is_scheduled') == 'cancel'){ ?>
		var j = jQuery.noConflict();	
		j('#cancel').modal('show');
		<?php } } ?> 
		
	
		function mydate(date, slot){ 
		 var j = jQuery.noConflict();	
		 j("#sd").val(date);	
		 j("#sslot").val(slot);	
		}
		function submitScheduled(){
			var j = jQuery.noConflict();	
			var sdate = j("#sd").val();	
			var sslot = j("#sslot").val();
			
			if(sdate ==undefined || sdate == ''){
			  alert("<?php echo __('csa.selectappday'); ?>");	
			return false;
			}
			if(sslot ==undefined || sslot == ''){
			alert("<?php echo __('csa.selappslot'); ?>");	
			return false;
			}	
			
			if(sdate != undefined){
			
			
			if(sslot==1){
				var sl = "11AM To 1PM";
			}
			if(sslot==2){
				var sl = "1PM To 3AM";
			}
			j("#daytime").val(sdate);
			j("#slot").val(sslot);
			
			j("#datea").text(sdate);
			j("#ampm").text(sl);
			
			}
			
			
			j.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/get-nom-total-current", 
				data: {
					"_token": "{{ csrf_token() }}",
					"date": sdate,
					"slot": sslot,
					"rid": '<?php echo $_REQUEST['id']; ?>'
					},
				dataType: "html",
				success: function(msg){ 
				  if(msg<=3){
					j('#basicExampleModal2').modal('show');
				  }else{
					alert("<?php echo __('csa.max3'); ?>");
					return false;
				  }
				},
				error: function(error){
					console.log("Error"+error);
					console.log(error.responseText);				
					var obj =  j.parseJSON(error.responseText);
				}
			});
			
			
			
		}
	
	
	
		
	
			
		   
	    
	</script>

	
	
	
	
	
	
	 <script type="text/javascript"> 
	 
	 	
		
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
			var j = jQuery.noConflict();	
			j("#slip").hide();
			j("#call").show();
			j("#scheduleButton").text("RESCHEDULE APPOINTMENT");
			j("#areyu").text("Are you sure to rescheduled appointment?");
			j("#reareyu").text("Reschedule");
		}
		function showhide2(){
			var j = jQuery.noConflict();	
			j("#slip").show();
			j("#call").hide();
		}
		
		
		
		
		function getval(daytime){
			var j = jQuery.noConflict();	
			j("#daytime").val(daytime);
			var mdaat = daytime.replace(":", "");
			var mdaat = mdaat.replace("___", "");
			var mdaat = mdaat.replace("***", "");
			j(".time-slot").css('background', '');
			j("."+mdaat).css('background', '#f0587e');
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