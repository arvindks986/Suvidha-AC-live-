 
 
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
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
	
	
   <title>Payment Verification</title>
   <script>
    var abc=[];
   </script>
  </head>
  <body>
   <main class="pt-3 pb-5 pl-5 pr-5">
	  <section>
	
	<?php 
	if(!empty(session('is_payment'))){ ?>
	<div style="text-align:center;background:#ee577e;color:white;">
	<?php  echo 'Status '.session('is_payment'); ?>
	 </div>
	<?php 	
	}
	?>
	
	 <div class="container-fluid" id="call">
	
		 <div class="card-header">
		   <div class="row">
		   </div>
		    <span style="margin-left: 41em;margin-top: 16px; font-size: 13px; color: black;cursor:pointer;font-weight: bold;">
			<?php 
			
			//Live
			$url = 'https://himkosh.hp.nic.in/eChallan/webpages/AppVerification.aspx';
			$merchant_code = 'HIMKOSH305';
			
			$Service_code = 'CEO';
			
			?>
			
			
			<table border="1">
			<tr>
			<td>Refrence Number</td>
			<td>Candidate Id</td>
			<td>Amount</td>
			<td>Status</td>
			<td>Action</td>
			</tr>
			<?php foreach($paydata as $data){ ?>
			<tr>
			<td>{{$data->reff_no}}</td>
			<td>{{$data->candidate_id}}</td>
			<td>{{$data->challan_amount}}</td>
			<td>{{$data->bank_transaction_status}}</td>
			@if($data->bank_transaction_status==1)
			<td>SUCCESS</td>
			@elseif($data->bank_transaction_status==3)
			<td>FAIL</td>
			@else
			<td>
				<?php   
				
				$AppRefNo = $data->reff_no;			
				$mString="AppRefNo=".$AppRefNo."|Service_code=".$Service_code."|merchant_code=".$merchant_code;
			
				$mString.="|checkSum=".checkSumHimachal($mString);

				$encdata = $mString;
								
				?>
				
				
					<form method="post" id="verification_him" name="verification_him" action="{{url('payment-verification-hp')}}">
						<input type="hidden" name="url"  value="{{$url}}">
						<input type="hidden" name="encdata"  value="{{$encdata}}">
						<input type="submit" class="btn btn-primary" id ="submit" name="submit_cin"  value="Verify" />
					</form>
				
				
			</td>
			@endif
			</tr>
			<?php } ?>
			</table>
					
			</span>	
		 </div>
  </body>
  
