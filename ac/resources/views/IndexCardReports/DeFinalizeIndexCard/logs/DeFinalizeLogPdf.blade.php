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
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
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
			@page { sheet-size: A4-L; }
			@page bigger { sheet-size: 420mm 370mm; }
			@page toc { sheet-size: A4; }

	  
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
                           <td><strong>De-Finalized Log Report</strong></td>
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
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i A') }}</td>
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
          <th>Sl No</th>
          <th>State Name</th> 
          <th>AC No - AC Name</th> 
          <th>Candidate Name</th> 
          <th>Gender</th> 
          <th>Age</th> 
          <th>Category</th> 
          <th>Party Name</th> 
          <th>Symbol</th> 
          <th>Updated By</th> 
          <th>Updated At</th>        
        </tr>
        </thead>
        <tbody>
        @php  

        $count = 1;
         @endphp

        @forelse($results as $result)
          <tr>
            <td>{{ $count }}</td>
            <td>{{ $result->st_name }}</td>
            <td>{{ $result->ac_no }} - {{ $result->ac_name }}  </td>
			<td>{{ $result->cand_name }}  </td>
            <td>{{ ucfirst($result->cand_gender) }}  </td>
            <td>{{ $result->cand_age }}  </td>
            <td>{{ strtoupper($result->cand_category) }}  </td>
            <td>{{ $result->party_name }}  </td>
            <td>{{ $result->symbol_name }}  </td>
            <td>{{ $result->log_updated_by }}  </td>
            <td>{{ date('d-m-Y h:i A', strtotime($result->log_updated_at)) }}  </td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="11">No Data Found For Index Card Finalize Statusss</td>                 
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