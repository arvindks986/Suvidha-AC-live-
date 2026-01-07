    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Index Card Finalization Report</title>
       
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
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>Index Card Finalization Report</strong></td>
                         </tr>
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
				<th rowspan="2">Serial No</th>
				<th rowspan="2">State Name</th> 
				<th rowspan="2">Total ACs</th> 
				<th rowspan="2">Nomination Module Finalized</th>  
				<th rowspan="2">Counting Module Finalized</th> 		  
				<th colspan="2">Index Card Varification & Finalization</th> 		          
			</tr>
			<tr>
				<th>Finalized By RO</th> 
				<th>Finalized By CEO</th>
			</tr>
		</thead>
		
		
        <tbody>
        @php  

        $count = 1;

        $TotalAc = 0;
        $FinalAc = 0;
		  $FinalAcCeo  =0;
		  $NominationFinalize = 0;
		 $CountingFinalize = 0;
         @endphp

        @forelse($results as $result)

        @php

         $TotalAc +=$result->total_ac;
         $FinalAc +=$result->finalize;
		 $FinalAcCeo +=$result->FinalizeCeo;
		 $NominationFinalize  +=$result->NominationFinalize;
		 $CountingFinalize  +=$result->CountingFinalize;

         @endphp

          <tr>
             <td>{{ $count }}</td>

            <td> {{ $result->st_name }}</td>
            <td>{{ $result->total_ac }}  </td>
			<td>{{ $result->NominationFinalize }}  </td>
			<td>{{ $result->CountingFinalize }}  </td>
            <td>{{ $result->finalize }}  </td>
			<td>{{ $result->FinalizeCeo }}  </td>
			
          
          
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Index Card Finalize </td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td></td><td><b>{{$TotalAc}}</b></td>
	  <td><b>{{$NominationFinalize}}</b></td>
	  <td><b>{{$CountingFinalize}}</b></td>
	  <td><b>{{$FinalAc}}</b></td>
	  <td><b>{{$FinalAcCeo}}</b></td>
	  
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