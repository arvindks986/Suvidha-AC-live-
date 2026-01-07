 <?php

$data = '<data xmlns="">
<RctReceiveValidateChlnRq>
<chlnDate>11/04/2023</chlnDate>
<deptCode>52A</deptCode>
<ddoCode>88815O</ddoCode>
<deptRefNum>EC0423800910001304</deptRefNum>
<rctReceiveValidateChlnDtls>
<amount>900</amount>
<deptPrpsId>3</deptPrpsId>
<prpsName>8009~01~101~0~01~000</prpsName>
<subPrpsName>004</subPrpsName>
<subDeptRefNum>EC0423800910001304</subDeptRefNum>
</rctReceiveValidateChlnDtls>
<rmtrName>manoj</rmtrName>
<totalAmount>900</totalAmount>
<trsryCode>572H</trsryCode>
</RctReceiveValidateChlnRq>
</data>';
 
echo 'Request Data</br>';
echo $data; 

/* 
$file_con = file_get_contents("private.pem");

//var_dump($file_con); die;

$pkeyid = openssl_pkey_get_private($file_con);

//var_dump($pkeyid); die;

//create signature
$reslt = openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA1);

 if(!$reslt){
	  echo '</br></br>';
	 echo 'Error';
	 
 }else{
	 echo '</br></br>';
	 echo 'Generate signature</br>';	 
	 echo base64_encode($signature);
	 echo '</br></br>';
 } */
 
 //die;
 
$xml_post_string = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/"><Header><Header xmlns="http://service.receivevalidatechallan.dept.rct.integration.ifms.gov.in/"   xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><agencyCode xmlns="http://header.ei.integration.ifms.gov.in/">EA_ECI</agencyCode><integrationCode xmlns="http://header.ei.integration.ifms.gov.in/">RCT033</integrationCode><uirNo xmlns="http://header.ei.integration.ifms.gov.in/">EA_ECI-RCT033-11042023-EC0423800910001304</uirNo></Header></Header>
<Body><envelopedDataReq xmlns="http://service.receivevalidatechallan.dept.rct.integration.ifms.gov.in/"><Signature xmlns="">-----BEGIN PKCS7-----MIAGCSqGSIb3DQEHAqCAMIACAQExCzAJBgUrDgMCGgUAMIAGCSqGSIb3DQEHAQAAoIAwggNqMIICUgIJAL/Ys5ohb42RMA0GCSqGSIb3DQEBCwUAMHcxCzAJBgNVBAYTAklOMQswCQYDVQQIDAJJTjELMAkGA1UEBwwCSU4xCzAJBgNVBAoMAklOMQswCQYDVQQLDAJJTjETMBEGA1UEAwwKZWNpLmdvdi5pbjEfMB0GCSqGSIb3DQEJARYQYWRtaW5AZWNpLmdvdi5pbjAeFw0yMzA0MTExMTIxMjRaFw0yNDA0MTAxMTIxMjRaMHcxCzAJBgNVBAYTAklOMQswCQYDVQQIDAJJTjELMAkGA1UEBwwCSU4xCzAJBgNVBAoMAklOMQswCQYDVQQLDAJJTjETMBEGA1UEAwwKZWNpLmdvdi5pbjEfMB0GCSqGSIb3DQEJARYQYWRtaW5AZWNpLmdvdi5pbjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMNKX0Q271vJgTzAI6MUnYiieWpvZRvQpTt+uwtRl0DLuHbbsqu8k+nY3KTkGnBjVWGuA/KVNLD2nrl8gFk1uG1s70qYjWcTpGPDwfvFzEG9cK0TyaPMdRyT5V0s/9BeyhhLn6eqWV6ilB5UmWCBZCYEsTh94EZNF8qeH3wzks86FqK6v+kyKQvEewv19UVSRGBz3LlEK9FIYgRcdEbNscqVs9D8XzinUJ7t71C1fWMI8hq/YMjqaOsDW5XPcyntWOfvfGStMiHBA9hzE3+Y6/L1afYS+cia7roZW+OJifyaXHvd6tCRT2Innk9ABSok5sBe423+SJk4XRDX50adRa0CAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAOdIgbLzRTQs8UL+H5KpTEP8r660LevPKzBuE/z6SG7bsL+Jqj/C2lr0Ryp6/XExMA52iTmEKcRs8cmHKX/knwWO/HOO6BWDKMfuTvNXZanlEe3oUOHLfxaWQOzW3gpKf2Snciyil48zQk+hhq4l+bQFnyH9aWp2QUR6c/uvMvIgwMCSuv0w+/IaAZPxt1lmsUXfJwJwyV6ZSEvizwU2OMOh1yAmDfq4DRKyAzPCQj1Qrz4V/GkGQqa5uP8Iy1sfQaUIfLm2hdwM6xrUVyDDSdySRp1v8oOKcl7ICTV0XZpflVRLcFeNj3cUp10Ld1h6FgZik1iY8Rf+FrgogRYjQLgAAMYICCzCCAgcCAQEwgYQwdzELMAkGA1UEBhMCSU4xCzAJBgNVBAgMAklOMQswCQYDVQQHDAJJTjELMAkGA1UECgwCSU4xCzAJBgNVBAsMAklOMRMwEQYDVQQDDAplY2kuZ292LmluMR8wHQYJKoZIhvcNAQkBFhBhZG1pbkBlY2kuZ292LmluAgkAv9izmiFvjZEwCQYFKw4DAhoFAKBdMBgGCSqGSIb3DQEJAzELBgkqhkiG9w0BBwEwHAYJKoZIhvcNAQkFMQ8XDTIzMDQxMTExNDI1MVowIwYJKoZIhvcNAQkEMRYEFIzW14t7kw5HsCsDM7eb8KXuQZTwMA0GCSqGSIb3DQEBAQUABIIBAJ/TutRXW2yDJGRF0k1HdJSVpgnA/Ht1SjoBpFvkkxw3fN/QJfax5V8WAcQMFTKo1E7o94/79/DKgLvF5GRebqf0s4kKCMHVJsrE+Fhu5suRy4y/q1HH+VU7Uh+FhnFE5RvYDLYuVe9yhpXXoXLxL/LRPvyFd8xivZbrfkpZd2RmcQxmc7OMRCSFIi/ziZ1FB9iXl+FxU+nzKrgd9IVxURbfiadtn8iK+2yATrbevGSLdRTAJvsSUzwjLFD+x7RxYGWrX/Xd/baczRWvCa9lilZ+9ILRkaNKgbeMnMwXpJWiyCfaRJym3bp82s2OzOLZm0k2CX7cHE0lRLinA3v2UXwAAAAAAAA=-----END PKCS7-----</Signature><data xmlns=""><RctReceiveValidateChlnRq><chlnDate>11/04/2023</chlnDate><deptCode>52A</deptCode><ddoCode>88815O</ddoCode><deptRefNum>EC0423800910001304</deptRefNum><rctReceiveValidateChlnDtls><amount>900</amount><deptPrpsId>3</deptPrpsId><prpsName>8009~01~101~0~01~000</prpsName><subPrpsName>004</subPrpsName><subDeptRefNum>EC0423800910001304</subDeptRefNum></rctReceiveValidateChlnDtls><rmtrName>manoj</rmtrName><totalAmount>900</totalAmount><trsryCode>572H</trsryCode></RctReceiveValidateChlnRq></data></envelopedDataReq></Body></Envelope>';


 echo 'Request data with signature</br>';

echo $xml_post_string;
	echo '</br></br>';	

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
