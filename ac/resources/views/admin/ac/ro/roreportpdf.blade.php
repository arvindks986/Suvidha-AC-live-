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
              <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img style="width:20%;" src="{{ asset('admintheme/img/logo/eci-logo.png') }}"></th>
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
                         <td><strong>AC Name: {{$const_name}}</strong></td>
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
                           <td align="right">
                               <?php
                                if($date!='all'){
                                    $date_range = explode('~', $date);
                                    $fromDate=$date_range[0] ;
                                    $toDate=$date_range[1];                                                
                                    if($fromDate==$toDate){
                                      echo $gettimeInterval= '<span id=""><b>Report Of: </b>'.$fromDate.'</span>';
                                    }else {
                                      echo $gettimeInterval= '<span id=""><b>Report From: </b>'.$fromDate.'<b> To: </b>'.$toDate.'</span>';
                                    }
                                }
                                ?>
                               
                           </td>
                         </tr>
                        
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>Total AC:</strong> {{ count($allTypeCountArr) }}</td>
               </tr>
            </table>
        <table class="table-strip" style="width: 98%;" border="1" align="center">
            <thead>
                <thead>
          <tr> 
            <th rowspan="2">Constituency Name</th>
            <th colspan="1">Before Scrutiny</th>
            <th colspan="3">After Scrutiny</th>
            <th rowspan="2">Total</th> 
          </tr>
          <tr> 
            <th>Applied</th>
            <th>Withdrawn</th>
            <th>Rejected</th>
            <th>Accepted</th>  
          </tr>
           </thead>
            </thead>
            <tbody>
                @foreach($allTypeCountArr as $list)
         <?php 
              
              $totalag=$totalag+$list['totala'];  $totalvg=$totalvg+$list['totalv']; $totalrecg=$totalrecg+$list['totalrec']; 
              $totalwg=$totalwg+$list['totalw']; $totalrg=$totalrg+$list['totalr']; 
              $totalaccg=$totalaccg+$list['totalacc']; $totalg=$totalg+$list['total'];           
          ?>            
              <tr>
                  <td>{{$list['const_name']}} </td>
                  <td>{{$list['total']}}</td> 
                  <td>{{$list['totalw']}}</td>
                  <td>{{$list['totalr']}}</td>
                  <td>{{$list['totalacc']}}</td>
                  <td>{{$list['total']}}</td> 
                </tr>
                  @endforeach
                  <tr>
                    <td>Total:- </td>
                    <td>{{$totalg}}</td> 
                    <td>{{$totalwg}}</td>
                    <td>{{$totalrg}}</td>
                    <td>{{$totalaccg}}</td>
                    <td>{{$totalg}}</td> 
                  </tr>  
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