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
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
          
                <tr>
                 <td align="left">
                    <strong>User: </strong> {{$user_data->placename}}</td> 
                 </td>
                  <td align="right"><strong>Date of Print: </strong> {{ date('d-M-Y h:i a') }}</td>
                 </tr>
               <tr>
                 <td align="left">@if(isset($phase_no) && $phase_no) <strong>Phase: </strong> {!! $phase_no !!} @endif</td>
                 <td align="right"><strong>Total Incident: </strong> {!! $total_incidents !!}</td>
               </tr>


              
              
            </table>

                       
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>

              <tr><th colspan="11" class="text-center">{!! $heading_title !!}</th></tr>

       <tr>
         <th>State Name</th>
              <th>AC No & Name</th>
              <th>PS No & Name</th>
              <th>Incident Detail</th>
              <th>Incident Type</th>
              <th>Description</th>
              <th>Reported Date</th>
         
       </tr>


    </thead>
        <tbody>

      @foreach($results as $result)

          <tr >
         <td>{{$result['st_name']}}</td>
              <td>{{$result['ac_name']}}</td>
              <td>{{$result['ps_name']}}</td>
              <td>{{$result['incident_detail']}}</td>
              <td>{{$result['incident_type']}}</td>
              <td>{{$result['description']}}</td>
              <td>{{$result['created_at']}}</td>
   
           </tr>


        @endforeach

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