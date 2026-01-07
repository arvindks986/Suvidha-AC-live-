    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Election Nomination In Phase</title>
       
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
                           <td><strong>List Of Election Nomination In Phase </strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <!--<tr>  
                           <td><strong>Place:</strong>  </td>
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
        <table class="table-strip" style="width: 98%;" border="1" align="center">
            <thead>
                <tr>
          <th>Serial No</th>
          <th>Candidate Name</th> 
          <th>Gender</th> 
          <th>AC Name</th> 
          <th>Party Name</th> 
          <th>Affidavit</th>
          <th>Applied Date</th> 
        </tr>
            </thead>
            <tbody>
 @php  $count = 1; @endphp
         @forelse ($EciNominationAcWiseReport as $key=>$listdata)
          <tr>
            <td>{{ $count }}</td>
            <td>{{ $listdata->cand_name }}</td>
            <td>{{ $listdata->cand_gender }}</td>
            <td>{{ $listdata->AC_NAME }}</td>
            <td>{{ $listdata->PARTYNAME }}</td>
            <td>@if(!empty($listdata->affidavit_path)) Affidavit Uploaded </a>@else No Affidavit @endif</td>        
            <td>{{ date('d-m-Y', strtotime($listdata->date_of_submit))}}</td>          
          </tr>
        @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">No Data Found For Nominations</td>                 
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