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
              <th>Permission Name</th>
              <th>Total Request</th> 
              <th>Accepted</th>
              <th>Rejected</th>
              <th>Inprogess</th>
              <th>Pending</th> 
              <th>Cancel</th> 
            </tr>
          </thead>
          <tbody id="oneTimetab">   
            @php $counttotal = 0;$countaccept = 0;$countreject = 0;$countinprogress = 0;$countpending = 0;$countcancel = 0; @endphp
             @foreach($records as $key =>$recordvalue)
             @php 
                                $counttotal = $counttotal + $recordvalue->Total;
                                $countaccept = $countaccept + $recordvalue->Accepted;
                                $countreject = $countreject + $recordvalue->Rejected;
                                $countinprogress = $countinprogress + $recordvalue->Inprogress;
                                $countpending = $countpending + $recordvalue->Pending;
                                $countcancel = $countcancel + $recordvalue->Cancel;
                                @endphp
              <tr>
                 <td>{{$recordvalue->ST_NAME}}</td>
                <td>{{$recordvalue->permission_name}}</td>
                <td>{{ $recordvalue->Total}} </td>
                <td>{{ $recordvalue->Accepted}}</td> 
                <td>{{ $recordvalue->Rejected}}</td>
                <td>{{ $recordvalue->Inprogress}}</td>
                <td>{{ $recordvalue->Pending}}</td>
                <td>{{ $recordvalue->Cancel}}</td>
              </tr>
              @endforeach
                     <tr>
                                    <td colspan="2"><a href="javascript::void(0)"><span>Grand Total</span></td>
                                    <td><a href="javascript::void(0)">{{$counttotal}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countaccept}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countreject}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countinprogress}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countpending}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countcancel}}</a></td>
                                </tr>
            
              
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