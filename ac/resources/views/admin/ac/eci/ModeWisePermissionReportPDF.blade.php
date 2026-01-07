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
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"> <img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
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
		<td align="center" ><strong>Status of Permissions sought by Candidates/Political Parties</strong></td>
		</tr>
		</tbody>
		</table>
                
        <table class="table-strip" style="width: 100%;" border="1" align="center">
        
           <thead>
            <tr> 
            
            </tr>
            <tr>  
                <th style="text-align: left;">S. No.</th>
              <th style="text-align: left;">State</th>
              @if($pname != '0')
            <th style="text-align: left;">Permission Name</th>
            @endif
              <th style="text-align: left;">Total Request</th> 
              <th style="text-align: left;">Approved</th>
              <th style="text-align: left;">Rejected</th>
              <th style="text-align: left;">Inprogress</th>
              <th style="text-align: left;">Pending</th> 
              <th style="text-align: left;">Cancel</th> 
              <th style="text-align: left;">Permission Mode</th>
            </tr>
          </thead>
          <tbody id="oneTimetab">   
             @php $counttotal = 0;$countaccept = 0;$countreject = 0;$countinprogress = 0;$countpending = 0;$countcancel = 0; @endphp
		     @foreach($report as $key =>$recordvalue)
              @php 
                                $counttotal = $counttotal + $recordvalue->Total;
                                $countaccept = $countaccept + $recordvalue->Accepted;
                                $countreject = $countreject + $recordvalue->Rejected;
                                $countinprogress = $countinprogress + $recordvalue->Inprogress;
                                $countpending = $countpending + $recordvalue->Pending;
                                $countcancel = $countcancel + $recordvalue->Cancel;
                                @endphp
              <tr>
                <td  style="text-align: right;">{{$key + 1}} </td>
                <td style="text-align: left;">{{ $recordvalue->ST_NAME}} </td>
                 @if($pname != '0')
                <td style="text-align: left;">{{$recordvalue->permission_name}}</td>
                @endif
                <td  style="text-align: right;">{{ $recordvalue->Total}}</td>
                <td  style="text-align: right;">{{ $recordvalue->Accepted}}</td>
                <td  style="text-align: right;">{{ $recordvalue->Rejected}}</td>
                <td  style="text-align: right;">{{ $recordvalue->Inprogress}}</td>
                <td  style="text-align: right;">{{ $recordvalue->Pending}}</td>
                <td  style="text-align: right;">{{ $recordvalue->Cancel}}</td>
                <td style="text-align: left;">
                    @if($recordvalue->permission_mode == '0')
                     {{'Offline'}}
                    @else
                    {{'Online'}}
                    @endif
                </td>
              </tr>
			  @endforeach
              <tr>
                                    @if($pname != '0')
                                    <td colspan="3" style="text-align: left;"><a href="javascript::void(0)"><span>Grand Total</span></a></td>
                                    @else
                                    <td colspan="2" style="text-align: left;"><a href="javascript::void(0)"><span>Grand Total</span></td>
                                    @endif
                                    <td  style="text-align: right;"><a href="javascript::void(0)">{{$counttotal}}</a></td>
                                    <td  style="text-align: right;"><a href="javascript::void(0)">{{$countaccept}}</a></td>
                                    <td  style="text-align: right;"><a href="javascript::void(0)">{{$countreject}}</a></td>
                                    <td  style="text-align: right;"><a href="javascript::void(0)">{{$countinprogress}}</a></td>
                                    <td  style="text-align: right;"><a href="javascript::void(0)">{{$countpending}}</a></td>
                                    <td  style="text-align: right;"><a href="javascript::void(0)">{{$countcancel}}</a></td><td></td>
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