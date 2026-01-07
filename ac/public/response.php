 <?php

 $data = '<data xmlns="">
<RctReceiveValidateChlnRq>
<chlnDate>10/04/2023</chlnDate>
<deptCode>52A</deptCode>
<ddoCode>88815O</ddoCode>
<deptRefNum>AG0223800910001304</deptRefNum>
<rctReceiveValidateChlnDtls>
<amount>900</amount>
<deptPrpsId>3</deptPrpsId>
<prpsName>8009~01~101~0~01~000</prpsName>
<subPrpsName>004</subPrpsName>
<subDeptRefNum>AG0223800910001304</subDeptRefNum>
</rctReceiveValidateChlnDtls>
<rmtrName>manoj</rmtrName>
<totalAmount>900</totalAmount>
<trsryCode>572H</trsryCode>
</RctReceiveValidateChlnRq>
</data>';
 



$pkeyid = openssl_pkey_get_private("https://suvidha.eci.gov.in/suvidhaac/public/key.pem");


//var_dump($pkeyid); die;

//create signature
openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
file_put_contents('signature.dat', $signature);

 var_dump($signature);
 
 //die;
 
$xml_post_string = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/"><Header><Header xmlns="http://service.receivevalidatechallan.dept.rct.integration.ifms.gov.in/"><agencyCode xmlns="http://header.ei.integration.ifms.gov.in/">EA_AGK</agencyCode><integrationCode xmlns="http://header.ei.integration.ifms.gov.in/">RCT033</integrationCode><uirNo xmlns="http://header.ei.integration.ifms.gov.in/">EA_AGK-RCT033-06042023-EC0223800910001305</uirNo></Header></Header><Body><envelopedDataReq xmlns="http://service.receivevalidatechallan.dept.rct.integration.ifms.gov.in/"><Signature xmlns="">-----BEGIN PKCS7-----'.base64_encode($signature).'-----END PKCS7-----</Signature><data xmlns=""><RctReceiveValidateChlnRq><chlnDate>10/04/2023</chlnDate><deptCode>52A</deptCode><ddoCode>88815O</ddoCode><deptRefNum>AG0223800910001304</deptRefNum><rctReceiveValidateChlnDtls><amount>900</amount><deptPrpsId>3</deptPrpsId><prpsName>8009~01~101~0~01~000</prpsName><subPrpsName>004</subPrpsName><subDeptRefNum>AG0223800910001304</subDeptRefNum></rctReceiveValidateChlnDtls><rmtrName>manoj</rmtrName><totalAmount>900</totalAmount><trsryCode>572H</trsryCode></RctReceiveValidateChlnRq></data></envelopedDataReq></Body></Envelope>';

var_dump($xml_post_string);
		

		$posturl = 'https://preprodkhajane2.karnataka.gov.in/KhajaneWs/rct/rrvcs/secbc/RctReceiveValidateChlnService?wsdl';
		   
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $posturl,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>$xml_post_string,
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/xml'
		  ),
		));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
