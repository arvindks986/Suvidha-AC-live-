    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of All Election Nomination </title>
       
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
                           <td><strong>List Of All Election Nomination</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <!-- <tr>  
                           <td><strong>District:</strong>  SNAME</td>
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
                           <td align="right"><strong>Report From:</strong> {{$EciNominationStateWiseReport['from']}}</td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Report To:</strong>  {{$EciNominationStateWiseReport['to']}}</td>
                         </tr> 
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              <!--  <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>Total Case:</strong>total count</td>
               </tr> -->
            </table>
        <table class="table-strip" style="width: 98%;" border="1" align="center">
            <thead>
                <tr>
        
          <th>AC No</th> 
          <th>AC Name</th> 
          <th>Total Nomination</th> 
        <th>Affidavit Uploaded</th>
        </tr>
            </thead>
            <tbody>
       
          @php  $count = 1; $total_nom = $total_aff = 0; @endphp
         @forelse ($EciNominationStateWiseReport['EciNominationStateWiseReport'] as $key=>$listdata)
		 
		   @php   $total_nom += $listdata->totalnomination;
		  $total_aff += $listdata->affidavit_count; @endphp
          <tr>
           
            <td>{{ $listdata->AC_NO }}</td>
            <td>{{ $listdata->AC_NAME }}</td>
            <td> @if($listdata->totalnomination =='' )     0  @else  {{ $listdata->totalnomination }} @endif</td>
           <td> @if($listdata->affidavit_count =='' )   0  @else {{ $listdata->affidavit_count }} @endif</td>
          </tr>
      
           @empty
                <tr>
                  <td colspan="4">No Data Found For Nominations</td>                 
              </tr>
          @endforelse
            	<tr>
             <td colspan="2" style="text-align:center;"><b>Total</b></td>
            <td> <b>{{$total_nom}}</b></td>
            <td> <b>{{$total_aff}}</b></td>
          </tr>
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