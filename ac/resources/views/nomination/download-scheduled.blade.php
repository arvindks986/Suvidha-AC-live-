<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Candidate Nomination</title>
	<style type="text/css">
		.table-strip {
			border-collapse: collapse;
		}
		
		ul {
			list-style-type: none;
		}
		
		@page {
			header: page-header;
			footer: page-footer;
		}
		
		body, p, td, div { font-family: freesans; }
	</style>
</head>

<body>
	<htmlpageheader name="page-header">
		<?php 
			$stateCode='';
			if(isset($ids)){ 
			$ssssss = explode(",", $ids);
			foreach($ssssss as $nomidssssss){
			$zzzzz = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomidssssss);	
			//echo $datadd; die;
			$aaaaaa = explode("***", $zzzzz); 
			$stateCode=$aaaaaa[15]; 
			} }
			
			
			?>
	
	
	
		<table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
			<thead>
				<tr>
					<th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt="" width="100" border="0"/>
					</th>
					<th style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
					{{ __('finalize.eci') }}
					</th>
				</tr>
			</thead>
		</table>

	</htmlpageheader>

	<htmlpagebody> 
	   <br>
	   <br>
		<table class="table-strip" style="width: 100%;" border="1" cellpadding="9">
			<tbody>
				<tr>
					<td>
						<div><strong>{{$candidate_name}}</strong>
						</div>
						@if($stateCode!='S06')
						<div style="text-align: center;">{{ __('finalize.app_details') }}</div>
						@endif
					</td>
				</tr>
				<!--<tr>
					<td>
						<table style="width: 100%;" border="0">
							<thead>
								<tr>
									<th style="text-align:left;">{{ __('finalize.Appointment_Status') }}</th>
									<th style="text-align:left;">{{ __('finalize.Appointment_Date_Time') }}</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{{$appoinment_status}}</td>
									<td><span style="background-color: #19c790;color:white;height:5px;"> <b>{{$appoinment_scheduled_day_one}}</b>,  <b>{{$appoinment_scheduled_date_one}}, {{$slot}}</b></span>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>-->
			<tr>
				<td>
					<table style="width: 100%;">
						<thead>
							<tr>
								<th style="text-align:left;">{{ __('nomination.Nomination_No') }}.</th>
								<th style="text-align:left;">{{ __('nomination.Election') }}</th>
								<th style="text-align:left;">{{ __('finalize.Assembly_Constituency') }}</th>
							</tr>
						</thead>
					</table>
				</td>
			</tr>	
				<?php if(isset($ids)){ 
			$ids = explode(",", $ids);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			//echo $datadd; die;
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
			$election_name_one=$str[12];
			$st_code=$str[15];
			$stname=$str[13];
			?>
				<tr>
					<td>
						<table style="width: 100%;">							
							<tbody>
								<tr>
									<td style="text-align:left;">{{$NOMNO}}</td>
									<td style="text-align:left;">{{$election_name_one}}</td>
									<td style="text-align:left;">{{$ACNO}} - {{$ACname}}, {{$stname}}</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<?php } ?> 
				<br><br>
			<!--	<tr><td style="border-bottom:3px solid gray;"> <br><b>{{ __('finalize.RO_Details') }}</b></td></tr> 
				<tr> 
					<td style="border-bottom:3px solid gray;"> <br>
						<table style="width: 100%;" border="0">
							<thead>
								<tr>
									<th style="text-align:left;">{{ __('csa.RO_Name') }}</th>
									<th style="text-align:left;">{{ __('csa.Address') }}</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{{$ROname}}</td>
									<td>{{$ROaddress1}} {{$ROaddress2}}, {{$stname}}</span>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>-->
				
							@if($stateCode!='S06')
							<tr><td style="border-bottom:3px solid gray;"> <br><b>{{ __('messages.predetails') }}</b></td></tr> 
							@endif
							
							<?php $ppArray = app(App\Http\Controllers\Nomination\NominationController::class)->getschedule_appoinment($ACNO, $st_code);	 
							if(count($ppArray) > 0 ) {
							foreach($ppArray as $dataaad){
							
							?>  
							  @if($stateCode!='S06')
							 <tr style="font-size: 17px;margin-bottom:10px;margin-left: 20px;">
								<td class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-calendar" aria-hidden="true"></i></b>&nbsp; {{date("D d, M Y", strtotime($dataaad->appointment_date))}}, {{$dataaad->appointment_time}} O'clock   
								@if($dataaad->is_ro_acccept=='1')
								<!--<span style="background-color:yellowgreen;color;white;"> &nbsp; {{ __('messages.roacc') }} </span>	-->
								@endif
								</span></td>
							</tr> 
							@endif
							<?php }} ?>  	
							
				
				
					<?php 
					
					$psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus_download($st_code, $ACNO);
					$challanCnt = app(App\Http\Controllers\Nomination\NominationController::class)->getChallan($st_code, $ACNO);
					$mydated = 'NA';
					
					//echo "<pre>"; print_r($psta); die;
					
					
					if(count($psta)>0){
					$expd = '';
					$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $ACNO);
					
					
					$ost='NA';
					if(!empty($psta[0]->status)){
						if($psta[0]->status==3){
						 $ost='NA';
						}
						if($psta[0]->status==2){
						 $ost='Pending';
						}
						if($psta[0]->status==1){
						 $ost='Success';
						}
					} else {
						$ost='NA';
					}
					?>	
				@if($psta[0]->st_code=='S06')	
					
					@if($stateCode=='S06')
					<tr><td style="border-bottom:3px solid gray;"> <br><b></b></td></tr> 
					@endif
				
					<tr><td><h5>
					 Payment Details
					</h5></td></tr>
						
					<tr><td>
					 	Mode: Online
					</td></tr>
					
					@if(!empty($psta[0]->reff_no))
					<tr><td>
					 	Refrence No. : {{ $psta[0]->reff_no }} 
					</td></tr>
					@endif
					@if(!empty($psta[0]->bank_code))
					<tr><td>
					 	Bank Code : {{ $psta[0]->bank_code }} 
					</td></tr>
					@endif
					@if(!empty($psta[0]->amount1))
					<tr><td>
					 	Amount : RS. {{ $psta[0]->amount1 }} 
					</td></tr>
					@endif	
					@if(!empty($ost))
					<tr><td>
					 	Status : {{ $ost }} 
					</td></tr>
					@endif	
					@if(!empty($psta[0]->pay_date_time))
					<tr><td>
					 	Date of Payment  :  {{ $psta[0]->pay_date_time }} 
					</td></tr>
					@endif	
				@endif	
						
				  <br>	
				<?php } else {  
				if(count($challanCnt) > 0){
				?>
				<?php 
				$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $ACNO);
				$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($st_code);
				?>
				<br>	<br>		
				  <table style="line-height:2;width:100%; border:1px solid gray;">
					
					@if(!empty($challanCnt[0]->payByCash) && ($challanCnt[0]->payByCash > 0))
						
					<tr><td><h3> 
					 	{{ __('messages.Mode') }} : {{ __('messages.PayByCash') }} 
					</h3></td></tr>

					
					@else
					<tr><td><h3> 
					 	{{ __('messages.Challan') }} 
					</h3></td></tr>
					<tr><td>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}: </label><span><?php echo $ACNO;  ?>-<?php echo $acnamed,', '.$stated; ?></span></td>
					</tr>
					<tr><td>{{ __('messages.ChallanNO') }}: </label> <span> {{ $challanCnt[0]->challan_no }}</span></td></tr>
					<tr><td>{{ __('messages.ChallanDate') }}</label> <span> {{ $challanCnt[0]->challan_date	}}</span></td></tr>
					
				@endif	
				 </table>
				<?php } } } ?>
				<tr><td style="border-bottom:3px solid gray;"> <br></td></tr> 
				@if($stateCode!='S06')
				<tr>
					<td style="width:100%; text-align: left;"><strong>{{ __('csa.Instruction') }} * {{ __('csa.verification_doc') }}</strong>
					</td>
				</tr>
				@endif
				@if($stateCode=='S06')
				<tr>
					<td style="width:100%; text-align: left;"><strong>* Payment is subject to realisation</strong>
					</td>
				</tr>
				@endif
				<tr>
					<td>
						<p style="text-align: left;padding-top: 15px;font-size:10px;">
							Print Date & Time
							<b>
								<?php echo date('d-m-y h:i:s'); ?>
							</b>
							<br><br>
						</p>
					</td>
				</tr>
			</tbody>
		</table>


	</htmlpagebody>
	<htmlpagefooter name="page-footer">
		<table style="width:100%; border-collapse: collapse;" align="center" cellpadding="5">
			<tbody>
				<tr>
					<td colspan="2" align="center"><strong></strong>
					</td>
				</tr>
			</tbody>
		</table>
	</htmlpagefooter>
</body>
</html>