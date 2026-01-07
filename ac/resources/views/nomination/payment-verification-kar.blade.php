 
 
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
				<?php   
				
				$Applicationnumber = $data->reff_no;	

				$uir=mt_rand(10000000,99999999); ;

				$agencyCode="EA_ECI"; 
				$integrationCode="RCT034";
				$uirNo='EA_ECI-RCT034-'.Date('dmY').'-'.$uir;
			
		
$requestdata = '<data xmlns="">
<RctReceivePaymentStatusRq>
<deptRefNum>'.$Applicationnumber.'</deptRefNum>
</RctReceivePaymentStatusRq>
</data>';
		

	$path = storage_path("key/KarnatakaPGSigner.jar");
	
	
	$dis = shell_exec("java -jar $path sign '$requestdata'");
	
	//echo $dis; die;
	
	 $start1 = "-----BEGIN PKCS7-----";
$end1 = "-----END PKCS7-----";

$start_pos1 = strpos($dis, $start1);
$end_pos1 = strpos($dis, $end1);

$digsign = substr($dis, $start_pos1, $end_pos1 - $start_pos1 + strlen($end1));

$xml_post_string ='<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
<Header>
<Header xmlns="http://service.receivepymntstatus.dept.rct.integration.ifms.gov.in/">
<agencyCode xmlns="http://header.ei.integration.ifms.gov.in/">'.$agencyCode.'</agencyCode>
<integrationCode xmlns="http://header.ei.integration.ifms.gov.in/">'.$integrationCode.'</integrationCode>
<uirNo xmlns="http://header.ei.integration.ifms.gov.in/">'.$uirNo.'</uirNo>
</Header>
</Header>
<Body>
<envelopedDataReq xmlns="http://service.receivepymntstatus.dept.rct.integration.ifms.gov.in/">
<Signature xmlns="">'.$digsign.'</Signature>
<data xmlns="">
<RctReceivePaymentStatusRq>
<deptRefNum>'.$Applicationnumber.'</deptRefNum>
</RctReceivePaymentStatusRq>
</data>
</envelopedDataReq>
</Body>
</Envelope>';


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://khajane2.karnataka.gov.in/KhajaneWs/rct/rrpys/secbc/RctReceivePaymentStatusService?wsdl',
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

$start = "<data>";
$end = "</data>";

$start_pos = strpos($response, $start);
$end_pos = strpos($response, $end);

$substring = substr($response, $start_pos, $end_pos - $start_pos + strlen($end));

if(($substring != '<soap:E') || ($substring != '')){



$xml = simplexml_load_string($substring, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

if($array['RctReceivePaymentStatusRs']){
	
	
	if($array['RctReceivePaymentStatusRs']['statusCode'] == 'KII-RCTER-00'){
		$sttusdd = 1;
	}else{
		$sttusdd = 3;
	}


	$myvar = DB::connection('suivhdalivetest')
		->table('payment_details_common')
		->where('reff_no', '=', $array['RctReceivePaymentStatusRs']['deptRefNum'])
		->update([
		   'bank_transaction_message'      			=> $array['RctReceivePaymentStatusRs']['statusDesc'], 		   
		   'payment_mode'      						=> (($array['RctReceivePaymentStatusRs']['pymntMode']) ? ($array['RctReceivePaymentStatusRs']['pymntMode']) : ''),		   
		   'status_from_bank_status_code'      		=> (isset($array['RctReceivePaymentStatusRs']['statusCode']) ? ($array['RctReceivePaymentStatusRs']['statusCode']) : ''),		   
		   'bank_transtimestamp'      				=> (isset($array['RctReceivePaymentStatusRs']['currentTimeStamp']) ? ($array['RctReceivePaymentStatusRs']['currentTimeStamp']) : ''),
		   'bank_cd'      							=> (isset($array['RctReceivePaymentStatusRs']['bank_cd']) ? ($array['RctReceivePaymentStatusRs']['bank_cd']) : ''),,
		   'bank_transaction_status'  		  		=> $sttusdd,
		   'updated_at'  		  					=> date('Y-m-d H:i:s', time()), 
		]);

	
}}


?>
				
				
				
			</td>
			@endif
			</tr>
			<?php } ?>
			</table>
					
			</span>	
		 </div>
  </body>
  
