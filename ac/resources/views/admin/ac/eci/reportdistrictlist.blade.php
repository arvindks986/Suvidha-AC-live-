<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Permission Report</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br>
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
               
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong>{{ date('d-M-Y h:i a') }}</td>
                         </tr>
                     
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
		<td align="center" ><strong>Permission Report</strong></td>
		</tr>
		</tbody>
		</table>
                
        <table class="table-strip" style="width: 100%;" border="1" align="center">
           <thead>
            <tr>  
              <th>State Name</th>
              <th>District Name</th>
              <th>Total Request</th> 
              <th>Approved</th>
              <th>Rejected</th>
              <th>Inprogress</th>
             <!--  <th>Pending</th> 
              <th>Cancel</th>  -->
            </tr>
          </thead>
          <tbody id="oneTimetab">   
		     @foreach($records as $recordvalue)
              @php
         $reject = $recordvalue->Rejected+$recordvalue->Cancel;
        $inprogress = $recordvalue->Inprogress+$recordvalue->Pending;
​
         @endphp
              <tr>
                <td>{{$recordvalue->ST_NAME}}</td>
                <td>{{ $recordvalue->DIST_NAME}} </td>
                <td>{{ $recordvalue->Total}}</td>
                <td>{{ $recordvalue->Accepted}}</td>
                <td>{{ $reject}}</td>
                <td>{{ $inprogress}}</td>
               <!--  <td>{{ $recordvalue->Pending}}</td>
                <td>{{ $recordvalue->Cancel}}</td> -->
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