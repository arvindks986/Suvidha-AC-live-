<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>   
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/theme/img/logo/eci-logo.png') }}" alt=""  width="100" border="0"/></th>
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
                  <td align="center" ><strong>{!! $heading_title !!}</strong></td>
                  </tr>
				 
                </tbody>
                </table>
				<table class="table-strip" style="width: 100%;" border="1" align="center">
                  <tbody>
                  <tr>
                  <td style="width:30%;"><strong>State Name:</strong></td>
				  <td style="width:80%;" ><strong>@if($state) {{getstatebystatecode($state)->ST_NAME}} @else All @endif </strong></td>
                  </tr> 
                </tbody>
                </table>
				<table class="table-strip" style="width: 100%;" border="1" align="center">
                  <tbody>
                  <tr>
                  <td style="width:30%;"><strong>AC Name:</strong></td>
				  <td style="width:80%;" ><strong>@if($ac_no!=0) {{getacbyacno($state,$ac_no)->AC_NAME}} @else All @endif </strong></td>
                  </tr> 
                </tbody>
                </table>        
    <table class="table-strip" style="width: 100%;" border="1" align="center">
      <thead>
       <tr>
        <th colspan="1">S.No</th>
		<th colspan="1">State Name</th>
        <th colspan="1">AC No & Name</th>
        <th colspan="1">Scheduled Rounds</th>
        <th colspan="1">Completed Rounds</th>
        <th colspan="1">Pending Rounds</th>
       </tr>
    </thead>
        <tbody>
       @if(count($result)>0)
			@php

       $i=1; 
       $GrandTotalScheduled = 0;
       $GrandCompletedRound = 0;

      @endphp
			@foreach($result as $data)
			<?php 
 				$totalScheduled=$data->S_ROUND; 
				$completedRound=completeRound($data->STATE,$data->AC_NO);
				$pending=$totalScheduled-$completedRound;
        $GrandTotalScheduled   +=$totalScheduled;
        $GrandCompletedRound   +=$completedRound;
			?>       
        <tr>
		 <tr>
                  <td>{{$i}} </td>
				  <td>{{getstatebystatecode($data->STATE)->ST_NAME}} </td>
                  <td>{{$data->AC_NO}} -{{getacbyacno($data->STATE,$data->AC_NO)->AC_NAME}}</td>
                  <td>{{$totalScheduled}}</td>
                  <td>{{$completedRound}}</td> 
				  <td>{{$pending}}</td> 
         </tr>
        @php

         $i++ 
          

         @endphp
		@endforeach
			  @else
				<tr>
                  <td colspan="8" style="text-align:center">--No Record Found--</td>
                </tr>
				@endif		
				
       </tbody>
       <tr>
            <td><b>Total</b></td>
            <td></td>
             <td></td>
            <td><b>{{$GrandTotalScheduled}}</b></td>
            <td><b>{{$GrandCompletedRound}}</b></td>
            <td><b>{{$GrandTotalScheduled - $GrandCompletedRound}}</b></td>
          </tr> 

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