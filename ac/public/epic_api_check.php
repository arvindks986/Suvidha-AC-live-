<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://electoralsearch.in/api/search?passKey=b91d601d7137766f4173e6da172d05fed05473f76584303d047f1fe7b4cd9b3a09924920fdb8613f8243f3e13259a2ba7fe4ac4287eb8aa93755e689c14421eb&search_type=epic&epic_no=NWD4934667',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: Electoral=456c656374726f6c7365617263682d73657276657232'
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
exit;
//curl_close($curl);
//echo $response;	
