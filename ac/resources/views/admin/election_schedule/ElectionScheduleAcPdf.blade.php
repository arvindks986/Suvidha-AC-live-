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
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                          <td><strong>{!! $heading_title !!}</strong></td>
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
           <th>Poll Events (Phase)</th>
          <th>State</th>
          <th>ACs No & Name</th> 
          <th>Date of Issue of Gazette Notification</th> 
          <th>Last Date For Making Nominations</th> 
          <th>Date for Scrutiny of Nominations</th> 
          <th>Last Date For Withdrawl of Candidature</th> 
          <th>Date Of Poll</th> 
          <th>Date Of Counting</th> 
          <th>Date Of Completion</th>
                </tr>
            </thead>
            <tbody>
     @php  $count = 1; @endphp
         @forelse ($results as $result)
          <tr>
            <td>{{ $count }}</td>
            <td style="color:#000000">Phase - {{$result['sid'] }}</td>

            <td style="color:#000000">{!! $result['label'] !!}</a></td>

            <td style="color:#000000">{{$result['const_no'] }} - {{$result['const_name'] }}</td>

            <td style="<?php echo $result['start_nomi_class'] ?>">
            {{GetReadableDateFormat($result['start_nomi_date']) }}</td>

            <td style="<?php echo $result['last_nomi_class'] ?>">{{GetReadableDateFormat($result['last_nomi_date']) }}</td>

            <td style="<?php echo $result['nomi_scr_class'] ?>">{{GetReadableDateFormat($result['dt_nomi_scr']) }}</td>

            <td style="<?php echo $result['last_wid_class'] ?>">{{GetReadableDateFormat($result['last_wid_date']) }}</td>

            <td style="<?php echo $result['poll_date_class'] ?>">{{GetReadableDateFormat($result['poll_date']) }}</td>

            <td style="<?php echo $result['count_date_class'] ?>">{{GetReadableDateFormat($result['count_date']) }}</td>

            <td style="<?php echo $result['complete_date_class'] ?>">{{GetReadableDateFormat($result['complete_date']) }}</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Election Schedule</td>                 
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