    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Officer Login Detail</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
       <?php  $st=getstatebystatecode($st_code);  
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
                           <td><strong>Officer Login Detail</strong></td>
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
        <tr>
          <th>Sr. No</th>
          <th>Officer Name</th>
          <th>Designation</th>
          <th>User Id</th>
          <th>Password</th>
        </tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
              @if(!empty($officerDetails))
            @foreach($officerDetails as $officerDetailsList)  
              <?php
               $j++; 
                 ?>
            
              <tr>
               <td>{{$j}}</td> 
               <td>@if(!empty($officerDetailsList->name)) {{$officerDetailsList->name}} @endif</td>
               <td>@if(!empty($officerDetailsList->designation)) {{$officerDetailsList->designation}} @endif</td>
               <td>@if(!empty($officerDetailsList->officername)) {{$officerDetailsList->officername}} @endif</td>
               <td style="background-color:white;"><font color="black">@if(!empty($officerDetailsList->officername)) {{'demo@123'}} @endif</font>
</td>
              </tr>
            @endforeach 
            @endif 
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