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
                           <td><strong>Null PS Count Report</strong></td>
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
			<th rowspan="2">AC No</th> 
			<th rowspan="2">AC Name </th>
			<th rowspan="2">Counting status</th>	          
				<th rowspan="2">Vote Margin between leading & Trailing candidates</th>	 			          
				<th rowspan="2">Number of Rejected PB</th>	 			          
				<th colspan="2">PS with Null Counts </th>	 			          
				<th rowspan="2">Result </th>	 			          
        </tr>
		
		  <tr>
			<th rowspan="1">No of PS</th>
			<th rowspan="1">Total votes </th> 
				 			          
        </tr>
       
        </thead>
        <tbody>
        @php  

        $count = 1;
         @endphp

        @forelse($dataArr as $result)
          <tr>
             <td>{{ $count }}</td>

            <td>{{ $result['ac_no'] }}</td>
            <td>{{ $result['ac_name'] }}  </td>
            <td>{{ $result['counting_status'] }} </td>
            <td>{{ $result['votes_margin'] }}  </td>
            <td>{{ $result['rejected_postal'] }} </td>
            <td>{{ $result['noofps'] }} </td>
            <td>{{ $result['novotes'] }} </td>
            <td>{{ $result['result_status'] }} </td>
				
			
			
		
	
			

          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Index Card Finalize Statusss</td>                 
              </tr>
          @endforelse
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
