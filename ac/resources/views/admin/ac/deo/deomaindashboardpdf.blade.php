<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>State & District Wise Report</title>        
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
      <!--HEADER STARTS HERE-->
      <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
          <thead>
           <tr>
              <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;">ECI Report</th>
               <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                   SECRETARIAT OF THE<br>
                   ELECTION COMMISSION OF INDIA<br>
                   Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
               </th>
           </tr>
         </thead>
       </table>
   <!--HEADER ENDS HERE-->
      
       <?php $i=0;   $totalag=0;  $totalvg=0; $totalrecg=0; $totalwg=0; $totalaccg=0; $totalrg=0; $totalg=0; ?>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:49%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                         <td><strong>State: {{$state->ST_NAME}}</strong></td>
                         </tr>
                         <tr>  
                         <td><strong>District: {{$distname->DIST_NAME}}</strong></td>
                         </tr>
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:49%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d.m.Y h:i a') }}</td>
                         </tr>
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>Total Constituency:</strong>@if(isset($allTypeCountArr)) {{ count($allTypeCountArr) }} @else {{0}} @endif()</td>
               </tr>
            </table>
        <table class="table-strip" style="width: 98%;" border="1" align="center">
            <thead>
                <thead>
          <tr> 
            <th>QR Code</th>
            <th>Cand Name</th>
            <th>Constituency Name</th>
            <th>Status</th> 
          </tr>
           </thead>
            </thead>
            <tbody>
                @if(isset($allTypeCountArr))
                @foreach($allTypeCountArr as $list)

              <tr>
                  <td>{{$list['qrcode']}} </td>
                  <td>{{$list['cand_name']}}</td> 
                  <td>{{$list['const_name']}}</td>
                  <td>{{$list['s']}}</td>
                </tr>
                  @endforeach 
                  @endif()
            </tbody> 
        </table>
      <table style="width:98%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>