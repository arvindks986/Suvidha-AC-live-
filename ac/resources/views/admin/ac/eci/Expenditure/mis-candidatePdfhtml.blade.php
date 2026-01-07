@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
 //echo $st_code.'cons_no'.$cons_no; die;
 
  $countingDate=\app(App\models\Expenditure\ExpenditureModel::class)->getResultDeclarationDate();
  $DB_MONTH=Session::get('DB_MONTH');
  $DB_MONTH=!empty($DB_MONTH) ? $DB_MONTH : '';
  $DB_YEAR=Session::get('DB_YEAR');
  $DB_CONS_TYPE=Session::get('DB_CONS_TYPE');
  $DB_ELE_TYPE=Session::get('DB_ELE_TYPE');
  $monthName = date("F", mktime(0, 0, 0, $DB_MONTH, 10));
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Officer Login Detail</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
       <?php  $st=getstatebystatecode($st_code);  
     ?> 

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;">
                    <img src="<?php echo url('/'); ?>/admintheme/images/logo/eci-logo.png" alt=""  width="100" border="0"/>
                  </th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br> 
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:49%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>Candidate MIS Report</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name: {{ $DB_CONS_TYPE.' '.$DB_ELE_TYPE.' '.'ELECTION-'.' '.$monthName.' '.$DB_YEAR }}</strong> </td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{$stateName}}</td>
                         </tr>
                  
                         
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:49%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d.m.Y h:i a') }}</td>
                         </tr>
						  <tr>  
                           <td align="right"><strong>Counting Date:</strong> {{ !empty($countingDate['start_result_declared_date']) ? date('d-M-Y',strtotime($countingDate['start_result_declared_date'])) :'NA'}}</td>
                         </tr>
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <
            </table>
      
        <table class="table-strip" style="width: 98%;" border="1" align="center">
          <thead>
        <tr>
          <th>Serial No</th>
          <th>State</th> 
          <th>Total Filed Candidate</th> 
          <th>Not Filed Candidate</th> 
          <th>Not In Time Candidate</th> 
          <th>Defaulter Candidate</th> 
        </tr>
        </thead>
        <tbody>
            <?php 
             $count = 1; 
             $TotalUsers = 0;
             $TotalfiledData = 0;
             $TotalnotfiledData = 0;
             $TotalDefaulter= 0;
             $TotalNotinTime= 0;
            ?>
              @if(!empty($totalContestedCandidate))
             @foreach($totalContestedCandidate as $candDetails)  
              <?php
               $count++; 
               $TotalUsers =count($totalContestedCandidate);
               $stdetails=getstatebystatecode($candDetails->st_code);
               $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$candDetails->st_code,$cons_no);
             
               // Get Pending Data Count 
               $notfiledcount= $TotalUsers- $filedcount;
      
               $defaulter=\app(App\models\Expenditure\EciExpenditureModel::class)->getdefaulter('AC',$candDetails->st_code,$cons_no);
               //dd($defaulter);
               $defaultercount=!empty($defaulter) ? count($defaulter) : '0';
               $notinTime=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalNotinTime('AC',$candDetails->st_code,$cons_no);
               $TotalfiledData +=  $filedcount;
               $TotalnotfiledData += $notfiledcount;
               $TotalDefaulter += $defaultercount;
               $TotalNotinTime += $notinTime;
                 ?>
            
            <tr>
            <td>{{ $count }}</td>
            <td>{{ $stdetails->ST_NAME }}</td>
            <td align="right"> @if($filedcount =='')   0  @else  <b>{{ $filedcount }}</b> @endif</td>
            <td align="right"> @if($notfiledcount =='')  0  @else <b>{{  $notfiledcount }}</b> @endif </td>
            <td align="right"> @if($notinTime =='')   0  @else <b>{{  $notinTime }}</b> @endif</td>
            <td align="right"> @if($defaultercount =='')   0  @else <b>{{ $defaultercount }}</b> @endif</td>
            </tr>
            @endforeach 
            @endif 
             </tbody>
        </table>
      <table style="width:98%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>