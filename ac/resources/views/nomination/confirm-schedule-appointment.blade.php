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
  <div class="container-fluid"    id="call"> 
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6 col-12">
            <h3>{{ __('csa.Appointment_Details') }}</h3>
          </div>
        </div>
		
      </div>
      <div class="card-body" >
        <div class="row">
          <div class="col-md-8 col-12 pr-2">
            <div class="nomin-list" style="height: 357px;">
              <div class="owl-carousel owl-theme">
			  
				<?php if(isset($_REQUEST['id'])){ 
				$ids = explode(",", $_REQUEST['id']);
				$i=1;
				foreach($ids as $nomid){
				$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid); 
				//echo "<br>".$datadd."<br>";
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
				?>
			  
			  
                <div class="item">
                  <div class="appnt-detail list-detail">
                    <h4 class="text-center d-flex justify-content-between"><b>{{$i}}</b>{{$NOMNO}}</h4>
                    <ul>
					
                      <li class="status stat-clear"><strong>{{ __('nomination.Status') }}</strong> <span> {{ __('csa.Pre_Scurtiny_Done') }} <b><i class="fa fa-check" aria-hidden="true"></i></b></span></li>
                      <li><strong>{{ __('nomination.Name') }}</strong> <span>{{$candidate_name}}</span></li>
                      <li><strong>{{ __('nomination.Election') }}</strong> <span>{{$election_name_one}}</span></li>
                      <li><strong>{{ __('nomination.State') }}</strong> <span>{{$state}}</span></li>
                      <li><strong>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}</strong> <span>{{$ACNO}} - {{$ACname}}</span></li>
                      <li><strong>{{ __('nomination.Party') }}</strong> <span>{{$party}}</span></li>
                    </ul>
                    <div class="row m-0 p-3">
                      <div class="col-md-4 col-12 p-0"><strong>{{ __('nomination.Action') }}</strong></div>
                      <div class="col-md-8 col-12 p-0 text-right">
                        <div class="apt-btn"> <a href="{{$view_href_cust}}<?php echo '?acs='. encrypt_String($ACNO).'&std='.encrypt_String($std); ?>" class="btn sm-btn dark-pink-btn">{{ __('nomination.View') }}</a> 
						<a href="{{$download_href_cust}}" class="btn sm-btn dark-purple-btn">{{ __('nomination.Download') }}</a> </div>
                      </div>
                    </div>
                  </div>
                  <!-- End Of appnt-detail Div --> 
                </div>
				<?php $i++; } } } ?> 
              </div>
            </div>
            <!-- End Of nomin-list Div --> 
          </div>
		  <?php 
			$pickupDate = $nomiantaion_start_date;
			$returnDate  = $nomiantaion_end_date;
			
			//$diff=date_diff($pickupDate,$returnDate);
			
			$cdsate = date('d-m-Y');
			$shodate ='';
			
			
			$diff = abs(strtotime($returnDate) - strtotime($pickupDate)); 
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			
			
			$k=1;
			for($j=0; $j<$days; $j++){	
			$abssdate  =    date('d-m-Y', strtotime($pickupDate . +$j.'  day')); 
				if( strtotime($abssdate) > strtotime($cdsate) && $k==1 ){
					$shodate =  $abssdate;
					$k=2;
				}		
			}	 
			 $fslot=''; 	
		     $fslot = app(App\Http\Controllers\Nomination\NominationController::class)->getFreeSlot();					
		  ?>	
          <!-- Right Side Calendar Starts Here -->
           <div class="col-md-4 col-12 pl-0">
            <div class="appnt-calndr new-calndr" style="height: 358px;">
			  <h5 class="pt-2 text-center" id="textmsg">{{ __('csa.Date_time_flot') }}</h5>
              <div class="appionted-wrap">
                <div class="appionted-date-time">
                  <div class="schdule-watch-icon"></div>
                  <div class="dy-dt-tm">
					<?php 
					 $eep=array();
					 $slotTime="";
					 if( !empty($fslot)  ){
					 $eep = explode("***", $fslot);	 
					 
					 if($eep[1]==1){
						$slotTime="11AM To 1PM"; 
					 }
					 if($eep[1]==2){
						$slotTime="1PM To 3PM"; 
					 }
					 $day =  date('D', strtotime($eep[0]));
					?> 
                    <h2 class="day-nm">{{$day}}</h2>
                    <div>{{date('d-m-Y', strtotime($eep[0]))}}</div>
                    <div>{{$slotTime}}</div>
					
					<input type="hidden" name="sdate" id="sd" value="{{$eep[0]}}">
					<input type="hidden" name="sslot" id="sslot" value="{{$eep[1]}}">
					  
					
					<?php } else { ?>
					{{ __('csa.Nomination_not_scheduled') }}
					<?php }  ?>
				  </div>
                </div>
              </div>
              <!-- End of appionted-date Div  --> 
			 
              <div class="change-date-btn date-range"  style="float: right; margin-top: -3px; margin-right: -113px;"><a href="#" class="link-btn" onclick="return resetDate();">{{ __('csa.Choose_Another_Date') }}</a></div> 
			  
              <div class="multi-days-calndr">
                <h4 class="text-center mb-0 py-3">{{ __('csa.select_date_time') }}</h4>
                <div class="multi-owl-carousel owl-theme">
				
				<?php 
				for($i=0; $i<=$days; $i++){	
				
				$nd =    date('d-m-Y', strtotime($pickupDate . +$i.'  day')); 
				$day =   date('D', strtotime($nd));
				if( strtotime($nd) > strtotime($cdsate)  ){
				$slotInfo1 = app(App\Http\Controllers\Nomination\NominationController::class)->getSlotInfo1($nd);
				$slotInfo2 = app(App\Http\Controllers\Nomination\NominationController::class)->getSlotInfo2($nd); 
				
				?>
                <div class="item">
                    <div class="appnt-detail">
                      <div class="multi-slct">
                        <div class="multi-day-date text-center">
                          <h3 class="mb-0">{{$day}}</h3>
                          <div>{{$nd}}</div>
						  <input type="hidden" name="sdate"  id="sdate" value="{{$nd}}">
                        </div>
                        <div class="text-center">
						 @if($slotInfo1=='disabled')
					      <div class="customRadio">
						 	<input type="radio" name="slot" value="1" onclick="return mydate('<?php echo $nd; ?>', '1');" disabled>
							<label for="slotOne_{{$i}}">11 AM To 1 PM <span style="color: red; font-size: 10px; padding: 1px; border-radius: 4px; margin-left: -2px;">Slot Full</span><span class="trik-icon"><i class="fa fa-check" aria-hidden="true"></i></span></label>
							
                          </div>
						 @else	 
                          <div class="customRadio">
						 	<input type="radio" id="slotOne_{{$i}}" name="slot" value="1" onclick="return mydate('<?php echo $nd; ?>', '1');">
							<label for="slotOne_{{$i}}">11 AM To 1 PM <span class="trik-icon"><i class="fa fa-check" aria-hidden="true"></i></span></label>
                          </div>
						 @endif 
						 @if($slotInfo2=='disabled')
							<div class="customRadio">
						 	<input type="radio" name="slot" value="1" onclick="return mydate('<?php echo $nd; ?>', '1');" disabled>
							<label for="slotOne_{{$i}}">1 PM To 3 PM <span style="color: red; font-size: 10px; padding: 1px; border-radius: 4px; margin-left: -2px;">Slot Full</span><span class="trik-icon"><i class="fa fa-check" aria-hidden="true"></i></span></label>
                          </div> 
						@else 
                          <div class="customRadio">
                          <input type="radio" id="slotTwo_{{$i}}" name="slot" value="2" onclick="return mydate('<?php echo $nd; ?>','2');">
                          <label for="slotTwo_{{$i}}">1 PM To 3 PM <span class="trik-icon"><i class="fa fa-check" aria-hidden="true"></i></span></label>
						  </div>
						@endif   
						  
						  
                        </div>
                      </div>
                    </div>
                </div>
				<?php }	 } ?>  
                </div>
               <!-- <div class="date-range">Nomination Date Range <strong>{{date('d-m-Y', strtotime($pickupDate))}} to {{date('d-m-Y', strtotime($returnDate .' -1 day'))}}</strong></div>-->
                <div class="date-range"> {{ __('csa.Nomination_Date_Range') }} <strong>{{date('d-m-Y', strtotime($pickupDate))}} to {{date('d-m-Y', strtotime($returnDate))}}</strong></div>
              </div>
            </div>
            <!-- End Of appnt-calndr Div --> 
          </div>
        </div>
      </div>
	  
	  <?php 
		$stno='';
		$acnno='';
		$expd = explode(",", $_REQUEST['id']);
		$acst = app(App\Http\Controllers\Nomination\NominationController::class)->getAcStByNo($expd[0]); 
			 if(!empty($acst)){
				$entt = explode("***", $acst);
				$stno=encrypt_String($entt[0]);
				$acnno=encrypt_String($entt[1]);
			 }
		
		?>
	  
	  
      <div class="card-footer"><div class="text-left col-md-3 col-3 pl-3" style="height:1px;">
			  <a href="{{'nominations?acs='.$acnno.'&std='.$stno}}" class="btn btn-lg font-big dark-pink-btn" style="color: white;">{{ __('step1.Back') }}</a>  
			</div> 
        <div class="apt-btn text-right"> 
		<!--<a href="#" class="btn btn-lg font-big dark-pink-btn">CANCEL</a>--> 
		<a href="#" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return submitScheduled();" id="scheduleButton">{{ __('csa.SCHEDULE_APPOINTMENT') }}</a> </div>
       </div>
      </div>
    </div>
   </main>
   <main class="pt-3 pb-5 pl-5 pr-5" style="margin-top: -61px;">	
	<!--End Bank Details-->
	
	<!-- Modal For Schedule Appoinment-->
	<div class="modal fade" id="basicExampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="appoinment_form" id="appoinment_form" method="POST"  action="{{url('/nomination/confirm-schedule-appointment/post') }}" autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="areyu">{{ __('csa.Confirmation') }}</h5>			
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
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
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
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" id="reareyu" onclick="return showmsg('loader');">
			{{ __('csa.Schedule') }}</button>
		  </div>
		  <span style="text-align: center;display:none;" id="loader">
		 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('finalize.Please_Wait') }}
		</span>
		</div>
		</div>
	  </div>
	  </form>
	</div> 
	
	
  
  <!-- footerMod confirm schedule -->
    <div class="modal fade modal-confirm" id="footerMod" style="margin-left:100px;padding:5px;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div  style="margin-left:100px;padding:5px;">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title"></h5>
		<div class="header-caption">
		  <img src="{{ asset('appoinment/loader.gif') }}" height="200" width="200"></img>	
		  <p style="padding:10px;">{{ __('finalize.Please_Wait') }}</p>	
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
		  <p>{{ __('csa.success_meesage') }}.</p>	
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
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			if($datadd!="NA"){
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			
			
			<li><label>{{ __('nomination.Nomination_No') }}:</label> <span>{{$NOMNO}}</span></li>
          	<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}:</label> <span>{{$ACNO}} - {{$ACname}}</span></li>
			<?php }} } ?>
			<li><label>{{ __('nomination.Status') }}:</label> <span>{{$appoinment_status}}</span></li>
		 </ul>
		 <p class="note-warn"><strong><i> {{ __('csa.Instruction') }} <sup>*</sup></i></strong> {{ __('csa.verification_doc') }}</p>	
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('finalize.Ok') }}</button>
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
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
    <script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script> 
	
	
	<script type="text/javascript"> 
		function showmsg(id){ 
		  var j = jQuery.noConflict();	
		  j('#'+id).show();
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
			
			if("<?php echo $fslot; ?>"==''){
				alert("<?php echo __('csa.Nomination_not_scheduled'); ?>");	
				return false;
			}
			
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
			
			var ddp = sdate.split("-").reverse().join("-");
			
			j("#datea").text(ddp);
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
	
		  var j = jQuery.noConflict();
	       j(document).ready(function() {
              var owl = j('.owl-carousel');
              owl.owlCarousel({
                margin: 2,
                nav: true,
                loop: false,
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items: 2
                  },
                  1000: {
                    items: 2
                  }
                }
              });
		     var owl = j('.multi-owl-carousel');
              owl.owlCarousel({
                margin: 2,
                nav: true,
                loop: false,
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
	
       //This Function For Page Right Side Calendar
		j(function(){
		   j('.change-date-btn').on('click',function(){
			  j(this).fadeOut(); 
			   j('.appionted-wrap').hide(); 
			     j('.head-apinted').removeClass('curnt-dy-tm');
			       j('.multi-days-calndr').fadeIn();
		   });
	     });  	
	
	
			
		   
	    
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
				alert("Please enter condidate name");
				j("#candidate_name").focus();
				return false; 
			}
			if(bank_name==''){
				alert("Please enter bank name");
				j("#bank_name").focus();
				return false; 
			}
			if(account_number==''){
				alert("Please enter account number");
				j("#account_number").focus();
				return false; 
			}
			if(confirm_account_number==''){
				alert("Please enter confirm account number");
				j("#confirm_account_number").focus();
				return false; 
			}
			
			if(confirm_account_number!=account_number){
				alert("Account number and confirm account number should be same");
				j("#account_number").focus();
				return false; 
			}
			
			if(ifsc_code==''){
				alert("Please enter IFSC code");
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