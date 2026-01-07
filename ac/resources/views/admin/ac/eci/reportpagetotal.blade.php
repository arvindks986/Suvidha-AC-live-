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
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>

</th>
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
		<td align="center" ><strong>Status of Permissions sought by Candidates/Political Parties</strong></td>
		</tr>
		</tbody>
		</table>
                
        <table class="table-strip" style="width: 100%;" border="1" align="center">
           <thead>
            <tr>  <th style="text-align: left;">S.No</th>
              <th style="text-align: left;">State</th>
              <th style="text-align: left;">Total Request</th> 
              <th style="text-align: left;">Approved</th>
              <th style="text-align: left;">Rejected</th>
              <th style="text-align: left;">Inprogress</th>
              <th style="text-align: left;">Pending</th> 
              <th style="text-align: left;">Cancel</th>

            </tr>
          </thead>
          <tbody id="oneTimetab">   
            @php $i='1'; $counttotal = 0;$countaccept = 0;$countreject = 0;$countinprogress = 0;$countpending = 0;$countcancel = 0; @endphp
		     @foreach($records as $recordvalue)
             @php 
                                $counttotal = $counttotal + $recordvalue->Total;
                                $countaccept = $countaccept + $recordvalue->Accepted;
                                $countreject = $countreject + $recordvalue->Rejected;
                                $countinprogress = $countinprogress + $recordvalue->Inprogress;
                                $countpending = $countpending + $recordvalue->Pending;
                                $countcancel = $countcancel + $recordvalue->Cancel;
                                @endphp
              <tr>
                 <td style="text-align: right;">{{ $i}} </td>
                <td style="text-align: left;">{{ $recordvalue->ST_NAME}} </td>
                <td style="text-align: right;">{{ $recordvalue->Total}}</td>
                <td style="text-align: right;">{{ $recordvalue->Accepted}}</td>
                <td style="text-align: right;">{{ $recordvalue->Rejected}}</td>
                <td style="text-align: right;">{{ $recordvalue->Inprogress}}</td>
                <td style="text-align: right;">{{ $recordvalue->Pending}}</td>
                <td style="text-align: right;">{{ $recordvalue->Cancel}}</td>
              </tr>
               @php ++$i; @endphp
	@endforeach
			<tr>
                                    <td colspan="2"  style="text-align: left;"><span><strong>Grand Total</strong></span></td>
                                    <td style="text-align: right;"><strong>{{$counttotal}}</strong></td>
                                    <td style="text-align: right;"><strong>{{$countaccept}}</strong></td>
                                    <td style="text-align: right;"><strong>{{$countreject}}</strong></td>
                                    <td style="text-align: right;"><strong>{{$countinprogress}}</strong></td>
                                    <td style="text-align: right;"><strong>{{$countpending}}</strong></td>
                                    <td style="text-align: right;"><strong>{{$countcancel}}</strong></td>
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