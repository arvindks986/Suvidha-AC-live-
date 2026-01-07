    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Counting Status</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt=""  width="100" border="0"/></th>
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
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>List Of Counting Status</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <!--<tr>  
                           <td><strong>Phase:</strong>   aa</td>
                         </tr>
                          <tr>  
                           <td><strong>Assembly:</strong> SNAME</td>
                         </tr>  --> 
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
                       <!-- <tr>  
                           <td align="right"><strong>Phase Starts:</strong> </td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Phase Ends:</strong>  </td>
                         </tr>  -->
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>
                <tr>
                <th>Serial No</th>
          <th>State</th> 
          <th>Total AC</th> 
          <th>Counting Started</th> 
          <th>Result Declared</th> 
          <th>Percentage</th> 
                </tr>
            </thead>
            <tbody>
     @php  
		$count = 1; 
		$TotalAc= 0;
        $TotalCountingStarted = 0;
        $TotalDeclared = 0;
		@endphp
         @forelse ($EciCountingStatusReportPdf as $key=>$listdata)
		 
		 @php 

         $TotalAc              += $listdata->TOTAL_AC;
         $TotalCountingStarted += $listdata->COUNTING_STARTED;
         $TotalDeclared        += $listdata->RESULT_DECLARED;


         @endphp
          <tr>
             <td>{{ $count }}</td>
            <td>{{ $listdata->ST_NAME }}</td>
            <td> @if($listdata->TOTAL_AC =='' )     0  @else  {{ $listdata->TOTAL_AC }} @endif</td>
            <td> @if($listdata->COUNTING_STARTED =='' )   0  @else {{ $listdata->COUNTING_STARTED }} @endif</td>
            <td> @if($listdata->RESULT_DECLARED =='' )   0  @else {{ $listdata->RESULT_DECLARED }} @endif</td>
            <td> @if($listdata->PERCENTAGE =='' )   0  @else {{ $listdata->PERCENTAGE }} @endif</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Counting Status</td>                 
              </tr>
          @endforelse
		  <tr><td><b>Total</b></td><td><b></b></td><td><b>{{$TotalAc}}</b></td><td><b>{{$TotalCountingStarted}}</b></td><td><b>{{$TotalDeclared}}</b></td><td><b>{{ROUND(($TotalDeclared/$TotalAc*100),2)}} %</b></td></tr>
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