 
 
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
								
				<form method="post" name="getCIN" action="https://megepayment.gov.in/challan/models/frmgetgrn.php" >
				 <input type="hidden" id ="DEPARTMENT_ID" name=DEPARTMENT_ID value="<?php echo $data->reff_no;?>" />
				 <input type="hidden" id ="OFFICE_CODE" name="OFFICE_CODE" value="CEO000" />
				 <input type="hidden" id ="AMOUNT" name="AMOUNT" value="<?php echo $data->challan_amount;?>" />
				 <input type="hidden" id ="ACTION_CODE" name="ACTION_CODE" value="GETCIN"/>
				 <input type="hidden" id ="SUB_SYSTEM" name="SUB_SYSTEM" value="ESUVIDHA" />
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
  
