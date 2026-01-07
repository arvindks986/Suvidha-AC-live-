    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Duplicate Symbol Candidate Reports</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
      <?php  $st=app(App\commonModel::class)->getstatebystatecode($st_code);  
      date_default_timezone_set('Asia/Kolkata'); 
       // print_r($st);die;
  ?> 

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/images/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br> 
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:49%;">
                    <table  style="width:100%">ST_NAME
                      <tbody>
                         <tr>
                           <td><strong>List Of PC With Candidate Details</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{$st->ST_NAME}}</td>
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
               <
            </table>
            <?php $i=0;    $totalrg=0; $totalwg=0; $totalaccg=0;  $totalg=0; ?>
        <table class="table-strip" style="width: 98%;" border="1" align="center">
            <thead>
                <thead>
          <tr>
          <th>Serial No</th> 
          <th>PC Number&Name</th>
          <th>Accepted</th> 
          <th>Rejected</th>  
          <th>Withdrawn</th> 
          <!--<th>PC Name Hindi</th> -->
          <th>Total</th>
            </tr>
        </thead>
        <tbody> 
            <?php $count = 1; ?>
         @foreach($allTypeCountArr as $pcList)
          <?php  
               $totalwg=$totalwg+$pcList['Withdrawn']; 
               $totalrg=$totalrg+$pcList['rejected']; 
               $totalaccg=$totalaccg+$pcList['accepted'];
               $totalg=$totalg+$pcList['total'];          
       ?>  
          <tr>
            <td>{{$pcList['srno']}}</td>  
            <td >{{ $pcList['pc_name']}}</td>
            <td> {{$pcList['accepted']}}</td>
            <td> {{$pcList['rejected']}}</td>
            <td> {{$pcList['Withdrawn']}}</td>
            <td>{{$pcList['total']}}
            </td>
          </tr>
          <?php $count++ ?>
          @endforeach
          <tr> 
                  <td>Total:- </td>
                  <td> </td> 
                  <td>{{$totalaccg}}</td>
                  <td>{{$totalrg}}</td>
                  <td>{{$totalwg}}</td>
                  <td>{{$totalg}}</td> </tr>
      
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