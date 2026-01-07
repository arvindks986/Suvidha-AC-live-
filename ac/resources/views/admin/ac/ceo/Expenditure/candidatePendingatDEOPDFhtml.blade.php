@php 
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $acdetails=getacbyacno($st_code, $cons_no); 
  $acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
  //echo $st_code.'cons_no=>'.$cons_no;
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Contested Candidate</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="left" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="">
                    <img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>
                   </th>
                    <th  style="width:50%" align="right" style="font-weight:normal;">
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
          .table-strip th,.table-strip td{text-align: left;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%;" border="0" align="left">  
                <tr>
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>Candidate List</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name: AC General</strong> </td>
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
        <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
         <thead>
          <tr>
          <th>AC No & Name</th>
          <th>Candidates Name</th>
          <th>Party Name</th>
           <th>Date Of Submit Scrutiny Form</th>
           </tr>
            </thead>
            <tbody>
     @php  
     $count = 1; 
        $TotalUsers = 0;
        @endphp
         @forelse ($pendingatDEOCandList as $candDetails)

         @php
         $TotalUsers =count($pendingatDEOCandList);
          $ac = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
         $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
         //echo $date->format('d.m.Y'); // 31.07.2012
         $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
         // dd($candDetails);
		  $stDetails=getstatebystatecode($candDetails->ST_CODE);
		  $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
         @endphp
          <tr>
          <td>@if(!empty($ac->AC_NO))  {{ $ac->AC_NO }}-{{ $ac->AC_NAME}} @endif</td>
          <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
          <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
          <td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>        
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Record Found </td>                 
              </tr>
            @endforelse
            <!-- <tr><td><b>Total</b></td><td></td><td><b></b></td><td><b></b></td></tr> -->
            </tbody>
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>