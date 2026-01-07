    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Finalized ACs State and Phase Wise</title>
       
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
                           <td><strong>List Of Finalized ACs State and Phase Wise </strong></td>
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
                           <td align="right"><strong>Notification Date:</strong> {{GetReadableDateFormat(@$PhaseInfo->DT_ISS_NOM)}}</td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Last Date of Withdrawl:</strong> {{GetReadableDateFormat(@$PhaseInfo->LDT_WD_CAN)}}</td>
                         </tr> 
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>State:</strong>&nbsp;&nbsp; {{$state}}<strong>&nbsp;&nbsp; Phase Number:</strong>&nbsp;&nbsp; {{$phaseid}}</td>
               </tr>
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>
                <tr>
                 <th>Serial No</th>
          <th>AC No</th> 
          <th>AC Name</th> 
          <th>Finalized ACs</th> 
                </tr>
            </thead>
            <tbody>
      @php  
        $count = 1; 
        

        @endphp
         @forelse ($EciNominationFinalizedByStatePhaseIdPdf as $key=>$listdata)

         
          <tr>
            <td>{{ $count }}</td>
            <td> {{$listdata->AC_NO }}</td>
            <td>{{$listdata->AC_NAME }}</td>

            @php if($listdata->finalized_ac == 'Yes'){  @endphp
            <td style="color:#008000;">{{$listdata->finalized_ac }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$listdata->finalized_ac }}</td>
            @php } @endphp
            
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Finalized AC In This Phase and State</td>                 
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