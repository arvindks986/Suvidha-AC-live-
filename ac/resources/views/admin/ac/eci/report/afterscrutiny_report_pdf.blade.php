    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Scrutiny Reports</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                       
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
          
                <tr>
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>

              
              
            </table>
          <table class="table-strip" style="width: 100%;" border="1" align="center">
                  <tbody>
                  <tr>
                  <td align="center" ><strong> Assembly Elections/Bye: {!! $heading_title !!}</strong></td>
                  </tr>
                </tbody>
                </table>
                
        <table class="table-strip" style="width: 100%;" border="1" align="center">
        
           <thead>
            <tr> 
              <th style="text-align: center;">Sl NO</th>
              <th style="text-align: center;">State</th>
              <th  style="text-align: center;">Total Nomination</th>
             
                <th rowspan="2"style="text-align: center;">Offline</th>
                  <th rowspan="2"style="text-align: center;">Online</th>
              <th colspan="5" style="text-align: center;">Payment Mode</th> 
            
            </tr>
            <tr>  
                <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              
              
              <th style="text-align: center;">Online <br>Mode</th> 
              <th style="text-align: center;">Challan</th>
              <th style="text-align: center;">Cash </th>
              
              <th style="text-align: center;">Offline</th> 

            </tr> 
          </thead>
          <tbody id="oneTimetab">
          <?php $i=1; ?>
              @foreach($results as $result)
              <tr>
                <td><b>{{ $i++ }}</b></td>
                <td style="text-align: left;"><b>{{$result['label']}}</b> </td>
                
                <td style="text-align: right;">
               {{count($result['nomination'])}}
                
                </td>
               
                <td style="text-align: right;">{{count($result['offline'])}}</td>
                 <td style="text-align: right;">
                {{count($result['online'])}}
                </td>

                  <td style="text-align: right;">
                {{$result['payment_online']}}
                </td>
                <td style="text-align: right;">{{$result['payment_challan']}}</td>

                 <td style="text-align: right;">
               {{$result['payment_cash']}}
                
                </td>
                 <td style="text-align: right;">
               {{$result['offline_cash']}}
                
                </td>
                 
                
<!---->

 
              </tr>
              @endforeach

  
          </tbody>
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>