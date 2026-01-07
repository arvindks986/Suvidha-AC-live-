<!doctype html>
<html lang="en">
<head>    
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Booth Slip</title>  
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">      
  <style type="text/css">          
    html,body{font-family: 'Roboto', sans-serif; font-size: 12px; margin:0; overflow-x:hidden}     
    * {-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%}
    table{border-collapse: collapse; width: 100%;}
    table td{padding: 0.35rem; white-space: nowrap;}
    table td h4{margin: 0; padding: 0;}
    .container{width: 92%; margin: auto; display: block;}
    .table td{width: 50%; white-space: nowrap;}

  </style>
</head>
<body>
  <div class="container">  
        
      
    <table border="1"  class="table" cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <td style="border-right: 1px solid #fff;"><img src="{!! $spm_url !!}assets/img/eci-logo.png" alt="Election Commission Of India" width="50" height="50"></td>
          <td align="right">
            <div style="font-family:freeserif; font-size: 14px;"><strong>{{ $master_header->main_heading ?? null }}</strong></div>
            <div><strong>Election Commission Of India</strong></div>
          </td>  
        </tr>

        <tr>
          <td valign="top" align="left"><strong>Serial No/Booth Slip ID:</strong> {{ $getResults->unique_generated_id ?? null }}</td>
          <td valign="top" align="right"><strong>Epic Number:</strong> {{ $getResults->epic_no ?? null }}</td>
        </tr>
        <tr>   
          <td colspan="2" style="padding: 0;">
            <table border="0">
              <tbody>
                <tr>
                  <td style="width: 15%;" align="center" valign="top">
                    <div>


                      @if(!empty($getResults->qr_code))
                      <!-- <img src="assets/img/barcode.png" alt="Barcode" width="200" height="200"> -->
                      <img src="data:image/jpeg;base64,{{$getResults->qr_code}}" width='200' height='200'>
                      @elseif(!empty($getResults->qr_code_url))
                      <img src="{!! $spm_url !!}/img/qrCode/{{$getResults->id}}.png" alt="Barcode" width="200" height="200">
                      @else
                      <img src="assets/img/booth-slip-error-barcode.jpg" alt="Barcode" width="200" height="200">
                      @endif



                    </div>
                    <div><img src="data:image/jpeg;base64,{{$getResults->image}}" alt="Barcode" width="95" height="100" style="margin-top: 15px;"></div> 
                  </td>
                  <td style="width: 85%; padding: 0;" valign="top">
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                            <div><strong><span style="font-family:freeserif; font-size: 14px;">राज्य</span> (State):</strong></div>   
                          </td>
                          <td style="border-bottom: 1px dotted #666;" align="right">
                            <div><span  style="font-family:freeserif; font-size: 14px;">{{ $getResults->state_name_regional ?? null }}</span> ({{ $getResults->state_name_en ?? null }})</div>  
                          </td> 
                        </tr>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                            <div style="font-family:freeserif; font-size: 14px;"><strong>विधानसभा क्षेत्र </strong></div>   
                            <div>Assembly constituency:</div>
                          </td>
                          <td style="border-bottom: 1px dotted #666;" align="right">
                            <div style="font-family:freeserif; font-size: 14px;">228-हमीरपुर </div>
                            <div>228-Hamirpur</div> 
                          </td> 
                        </tr>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                            <div><strong><span style="font-family:freeserif; font-size: 14px;">{{ $master_header->label_name ?? null }}</span>  (Name):</strong></div> 
                          </td>
                          <td style="border-bottom: 1px dotted #666;" align="right">
                            <div><span style="font-family:freeserif; font-size: 14px;">{{ $getResults->name_regional ?? null }}</span>  ({{ $getResults->name_en ?? null }})</div>    
                          </td> 
                        </tr>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                            <div style="font-family:freeserif; font-size: 14px;"><strong>{{ $master_header->label_husband_name ?? null }}:</strong></div>   
                            <div><strong>Father/Husband Name:</strong></div>     
                          </td>
                          <td style="border-bottom: 1px dotted #666;" align="right">
                            <div style="font-family:freeserif; font-size: 14px;">{{ $getResults->father_name_regional ?? null }}</div>   
                            <div>{{ $getResults->father_name ?? null }}</div>  
                          </td> 
                        </tr>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                            <div><strong><span style="font-family:freeserif; font-size: 14px;">लिंग</span> (Gender):</strong></div>     
                          </td>
                          <td style="border-bottom: 1px dotted #666;" align="right">
                            <div><strong><span style="font-family:freeserif; font-size: 14px;">{{ $getResults->gender_hindi ?? null }}</span> ({{ $getResults->gender ?? null }})</strong></div>  
                          </td> 
                        </tr>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                            <div><strong><span style="font-family:freeserif; font-size: 14px;">आयु</span> (Age):</strong></div>     
                          </td>
                          <td style="border-bottom: 1px dotted #666;" align="right">
                            <div><strong>{{ $getResults->age ?? null }}</strong></div>   
                          </td> 
                        </tr>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                            <div><strong><span style="font-family:freeserif; font-size: 14px;">भाग संख्या</span> (Part No):</strong></div>   
                          </td>
                          <td style="border-bottom: 1px dotted #666;" align="right">
                            <div>{{ $getResults->part_no ?? null }}</div>  
                          </td> 
                        </tr>
                        <tr>
                          <td colspan="2" style="border-left: 1px solid #666; border-bottom: 1px dotted #666; font-size: 12px;" align="left">
                            <div><strong><span style="font-family:freeserif; font-size: 14px;">भाग का नाम</span> (Part Name):</strong></div>  
                            <div>{{ $getResults->ps_name_en ?? null }}</div>     
                          </td>
                        </tr>
                        <tr>
                          <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                           <div><strong><span style="font-family:freeserif; font-size: 14px;">मतदान की तारीख </span> (Polling Date):</strong></div> 
                         </td>
                         <td style="border-bottom: 1px dotted #666;" align="right">
                          <div>{{ date("d-m-Y", strtotime($getResults->polling_date)) }}</div>  
                        </td> 
                      </tr>
                      <tr>
                        <td style="border-left: 1px solid #666; border-bottom: 1px dotted #666;" align="left">
                         <div><strong><span style="font-family:freeserif; font-size: 14px;">मतदान का समय </span> (Polling Timing):</strong></div>   
                       </td>
                       <td style="border-bottom: 1px dotted #666;" align="right">
                        <div>7AM - 6PM</div>  
                      </td> 
                    </tr>
                    <tr>
                      <td colspan="2" style="border-left: 1px solid #666; font-size: 12px;" align="left">
                        <div><strong><span style="font-family:freeserif; font-size: 14px;">मतदान केंद्र </span> (Polling Station):</strong></div>
                        <div>{{ $getResults->ps_name_en ?? null }}</div>  
                        <div style="font-family:freeserif; font-size: 14px;">{{ $getResults->ps_name_regional ?? null }}</div>  
                        
                      </td>
                    </tr>

                  </tbody> 
                </table>  
              </td>
            </tr>  
          </tbody>  
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="padding: 0.35rem; border: 1px solid #000;">
        <table border="0">
          <tbody>
            <tr>
              <td valign="middle" style="width: 32%; padding: 0; font-size: 10px;"><strong>Booth App Pilot</strong></td>
              <td valign="middle" align="center"  style="width: 32%; padding: 0; font-size: 10px;"><strong>www.ceouttarpradesh.nic.in</strong></td>
              <td valign="middle" align="right"  style="width: 32%; padding: 0; font-size: 10px;"><strong>CEO Call Center Toll Free No - 18001801950</strong></td>
            </tr>
          </tbody>
        </table>  
      </td>
    </tr> 
  </tbody>  
</table>


</div><!-- End Of container Div --> 
</body>
</html>