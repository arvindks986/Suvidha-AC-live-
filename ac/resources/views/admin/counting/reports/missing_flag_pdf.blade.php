    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Scrutiny Reports</title>
       
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
            <thead>
       <tr>
          <th align="left">State</th>
          <th align="left">Const No - Name</th>
          <th align="left">EVM Finalized</th>
          <th align="left">Postal Finalized</th>      
       </tr>


    </thead>
       <tbody>
      @forelse($results as $result)
        <tr>
        <td align="left"><span>{!! $result['label'] !!}</span></td> 
         <td align="left">{{$result['const_no'] }} - {{$result['const_name'] }} </td>

         @php if($result['evm_finalized'] == 'Yes'){  @endphp
          <td style="color:#008000;">{{$result['evm_finalized'] }}</td>
           @php }else{ @endphp
          <td style="color:#FF0000;">{{$result['evm_finalized'] }}</td>
          @php } @endphp

          @php if($result['postal_finalize'] == 'Yes'){  @endphp
          <td style="color:#008000;">{{$result['postal_finalize'] }}</td>
           @php }else{ @endphp
          <td style="color:#FF0000;">{{$result['postal_finalize'] }}</td>
          @php } @endphp


        </tr>
       @empty
                <tr>
                  <td colspan="4">No Data Found For Missing Flags</td>                 
              </tr>
          @endforelse
        <tr>
        
        <tfoot>
        </tfoot>
       </tbody></table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>