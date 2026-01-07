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
		<table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
			<thead>
				<tr>
					<th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt="" width="100" border="0"/>
					</th>
					<th style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
					{{ __('finalize.eci') }}
					</th>
				</tr>
			</thead>
		</table>

	</htmlpageheader>

	<htmlpagebody> 
	   <br><br><br><br><br><br>
		<table class="table-strip" style="width: 100%;" border="1" cellpadding="9">
			<tbody>
				<tr>
					<td>
						<div><strong>{{$candidate_name}}</strong>
						</div>
					</td>
				</tr>
				
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
				<tr>
					<td>
						<table style="width: 100%;">							
							<tbody>
								<tr>
									<td style="text-align:left;">{{$nomination_no}}</td>
									<td style="text-align:left;">{{$election_name_one}}</td>
									<td style="text-align:left;">{{ $ac_no_name }}, {{$st_name}}</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>

								
					<tr><td style="border-bottom:3px solid gray;"> <br><b></b></td></tr> 
				
					<tr><td><h5>
					 Payment Details
					</h5></td></tr>
						
					<tr><td>
					 	{{ __('messages.Mode') }} : {{ __('messages.online') }} 
					</td></tr>
					
					<tr><td>
					 	Refrence No. : {{ !empty($bank_reff_no) ? $bank_reff_no : '' }}  
					</td></tr>

					
					<tr><td>
					 	Bank Code : {{ !empty($bank_code) ? $bank_code : '' }} 
					</td></tr>
					
					
					<tr><td>
					 	Amount : RS. {{ !empty($txn_amount) ? $txn_amount : '' }} 
					</td></tr>
						
					
					<tr><td>
					 	Status : Success 
					</td></tr>
					
					
					<tr><td>
					 	Date of Payment  :  {{ !empty($payment_date) ? $payment_date : '' }} 
					</td></tr>
					
					<tr><td style="border-bottom:3px solid gray;"> </td></tr> 
				<tr>
					<td style="width:100%; text-align: left;"><strong>* Payment is subject to realisation</strong>
					</td>
				</tr>
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