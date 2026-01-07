 
 
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
			
			$key='q4UOLnbuVc0mP8Jf634f1zCGVy2pf9lj';
			$iv='q4UOLnbuVc0mP8Jf';
			$enc_method = "AES-128-CBC";
		
			
			//$rd='https://cybertreasuryuat.gujarat.gov.in/CyberTreasury_UAT/connectDept?service=DeptPortalConnection'; // UAT
			$rd=' https://cybertreasury.gujarat.gov.in/CyberTreasury/connectDept?service=DeptPortalConnection';  // Live	

			
			//$RU="http://localhost/suvidhaac/public/payment-gujrat-verification";	// Local
			$RU="https://suvidha.eci.gov.in/suvidhaac/public/payment-gujrat-verification";	// Live
			
			
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
			<td>Success</td>
			@else
			<td>
				<?php   
				$fnlStrDAta="User_id=".$data->candidate_id."|Transaction_id=".$data->reff_no;
				$trd=trim($fnlStrDAta);						
				$encdata=openssl_encrypt($trd, $enc_method, $key, $options=0, $iv);								
				?>
				<form method="POST" name="redGuj"  action="<?php echo $rd; ?>">
				<input type="hidden" name="_token" value="{{csrf_token()}}">
				<input type="hidden" name="CTP_DATA" value="<?php echo $encdata; ?>">
				<input type="hidden" name="Dept_call" value="Recon_data">
				<input type="hidden" name="RU" value="<?php echo $RU; ?>">
				<input type="Submit" name="submit" value="Verify">
				</form>
			</td>
			@endif
			</tr>
			<?php } ?>
			</table>
					
			</span>	
		 </div>
  </body>
  
