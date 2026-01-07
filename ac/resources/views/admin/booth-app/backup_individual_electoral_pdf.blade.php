<!doctype html>
<html lang="en">
  <head>    
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Booth Slip</title>  
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">      
     <style type="text/css">          
      html,body{font-family: 'Roboto', sans-serif; font-size: 14px; margin:0; overflow-x:hidden}    
        * {-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%}
        table{border-collapse: collapse; width: 100%;}
        table td{padding: 0.35rem;}
        table td h4{margin: 0; padding: 0;}
        .container{width: 92%; margin: auto; display: block;}
        .table td{width: 50%;}
  
      </style>
    </head>
<body>
<div class="container"> 
 <table border="1"  class="table" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td style="border-right: 1px solid #fff;"><img src="{!! $spm_url !!}/img/eci-logo.png" alt="Election Commission Of India" width="50" height="50"></td>
    <td align="right">
      <div style="font-family:freeserif; font-size: 16px;"><strong>{{ $master_header->main_heading ?? null }}</strong></div>
        <div><strong>Election Commission Of India</strong></div>
    </td>  
    </tr>
  <tr>
        <td valign="top" align="left"><strong>Booth Slip ID:</strong> {{ $getResult->unique_generated_id ?? null }}</td>
      <td valign="top" align="right"><strong>Epic Number:</strong> {{ $getResult->epic_no ?? null }}</td>
    </tr>
  <tr>   
    <td colspan="2">
      <table border="0">
        <tr>
          <td valign="top" style="width: 50%;">
           <table border="0">
           <tr>
            <td>
              <div style="font-family:freeserif; font-size: 14px;"><strong>{{ $master_header->label_name ?? null }}:</strong> {{ $getResult->name_regional ?? null }}</div>   
              <div><strong>Name:</strong> {{ $getResult->name_en ?? null }}</div> 
            </td>
           </tr>
           <tr>
            <td>
            <div style="font-family:freeserif; font-size: 14px;">
              <strong>{{ $master_header->label_husband_name ?? null }}:</strong> {{ $getResult->father_name_regional ?? null }}
            </div>  
            <div>
              <strong>Father/Husband Name:</strong> {{ $getResult->father_name ?? null }}
            </div>
            </td>
           </tr> 
           <tr>
            <td valign="top"><strong>Gender:</strong> {{ $getResult->gender ?? null }}</td>
           </tr> 
           <tr>
             <td valign="top"><strong>Age:</strong> {{ $getResult->age ?? null }}</td>   
           </tr>
          <tr>
            <td valign="top" colspan="2"><strong>Polling Station:</strong> {{ $getResult->ps_name_en ?? null }}</td>
          </tr>     
           </table> 
        </td>
          <td valign="top" align="right" style="width: 50%;">
            
​
            @if(!empty($getResult->qr_code))
            <!-- <img src="assets/img/barcode.png" alt="Barcode" width="200" height="200"> -->
            <img src="data:image/jpeg;base64,{{$getResult->qr_code}}" width='200' height='200'>
            @elseif(!empty($getResult->qr_code_url))
            <img src="{!! $spm_url !!}/img/qrCode/{{$getResult->id}}.png" alt="Barcode" width="200" height="200">
            @else
            <img src="assets/img/booth-slip-error-barcode.jpg" alt="Barcode" width="200" height="200">
            @endif
          </td>
        </tr> 
      </table>
    </td>
  </tr>
  <tr>
      <td valign="top" style="border: 1px solid #000;"><strong>Polling Date:</strong> {{ date("d-m-Y", strtotime($getResult->polling_date)) }}</td>
   <td valign="top" align="right" style="border: 1px solid #000;"><strong>Polling Timing:</strong> 7AM To 5PM</td>  
  </tr>
  <tr>
    <td colspan="2" style="border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">
    <table border="0">
      <tbody>
        <tr>
          <td valign="top" style="width: 33%; font-size: 12px"><strong>Booth App Pilot</strong></td>
          <td align="center" style="width: 33%;">
            <div style="font-size: 10px;">Please carry this slip to the polling station.</div>
                        <div style="font-family:freeserif; font-size: 11px;">कृपया इस पर्ची को मतदान केंद्र तक ले जाएं।</div>
          </td>
          <td valign="top" align="right" style="width: 33%; font-size: 12px;"><strong>Toll Free No. - 18001801950</strong></td>
        </tr>
      </tbody>
    </table>  
    </td>
  </tr>
  <tr>
    @if($steps == "generalPdf")
    <?php $decode_id = base64_encode($getResult->id); ?>
    <td colspan="2" valign="top" align="center"><a href='{{ url("generate-slips?token=$decode_id") }}' style="display: inline-block; padding: 0.5rem 1.5rem; background-color: #515151; color: #fff; border-radius: 5px; text-decoration: none;">PRINT</a></td>
    @endif  
  </tr> 
  </tbody>  
 </table>
</div><!-- End Of container Div --> 
</body>
</html>