@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$district=!empty($district) ? $district : '0';
$all_ac=!empty($all_ac)?$all_ac:getacbystate($st_code);
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
        <title>List Of Active Users</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
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
                           <td><strong> District Wise Report Regarding DEO's Scrutiny Report On Account Of Contesting Candidates.</strong></td>
                         </tr>
                          <tr>  
                           <td><strong>Name: {{ $DB_CONS_TYPE.' '.$DB_ELE_TYPE.' '.'ELECTION-'.' '.$monthName.' '.$DB_YEAR }}</strong> </td>
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
                           <td align="right"><strong>Counting Date:</strong> {{ !empty($countingDate['start_result_declared_date']) ? date('d-M-Y',strtotime($countingDate['start_result_declared_date'])) :'NA'}}</td>
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
          <th>Serial No</th>
                                        <th>State</th> 
                                        <th>District</th> 
                                        <th>AC No & AC Name</th> 
                                        <th>Total Candidates</th> 
                                        <th>Started</th> 
                                        <th>Not Started</th> 
                                        <th>Finalised By DEO</th> 
                                        <th>Pending - DEO</th> 
                                                <!--<th>Notice At DEO</th> -->
                                        <th>Pending - CEO</th> 
                                                <!--<th>Notice At CEO</th> -->
                                        <th>Pending - ECI</th> 
                                        <th>Closed/Disqualified/Case Dropped</th> 
                </tr>
            </thead>
            <tbody>
      @php  
                                $count = 1; 
                                $TotalUsers = 0;
                                $TotalPendingatRO = 0;
                                $TotalPendingatCEO = 0;
                                $TotalPendingatECI= 0;
                                $TotalfiledData = 0;
                                $TotalnotfiledData = 0;
                                $Totalfinalcompletedcount= 0;
                                $Totalac = 0;
                                $TotalDEONotice = 0;
                                $TotalCEONotice = 0;
                                $TotalfiledData = 0;
                                $TotalFinalByDEO = 0;
                                $allStates=[];
                                @endphp
                                @forelse ($totalContestedCandidate as $key=>$listdata)
                                @php
                                //dd($listdata);
                                $TotalUsers +=$listdata->totalcandidate;

                                $stdetails=getstatebystatecode($listdata->st_code);
                                $st_code=!empty($st_code)? $st_code :$listdata->st_code;       
                                $allStates[]=[
                                'st_code'=>$st_code,
                                'ac_no'=>$listdata->ac_no,
                                ];
                                $cons_no=$listdata->ac_no;
                                $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('AC',$listdata->st_code,$cons_no);
                                $TotalFinalByDEO += $finalbyDEO;

                                // $pendingatROold=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalpartiallypending('AC',$listdata->st_code,$cons_no);


                                $pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('AC',$listdata->st_code,$cons_no);
                                $TotalPendingatCEO += $pendingatCEO;




                                $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('AC',$listdata->st_code,$cons_no);
                                $TotalPendingatECI += $pendingatECI;





                                $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
                                $TotalfiledData +=  $filedcount;

                                // Get Pending Data Count 
                                $notfiledcount= $listdata->totalcandidate - $filedcount;
                                $TotalnotfiledData += $notfiledcount;
                                $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('AC',$listdata->st_code,$cons_no);
                                $Totalfinalcompletedcount += $finalcompletedcount;

                                $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('AC',$listdata->st_code,$cons_no);
                                $TotalCEONotice += $noticeatCEOCount;

                                $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('AC',$listdata->st_code,$cons_no);
                                $TotalDEONotice += $noticeatDEOCount;

                                //pending at DEO
                                $pendingatRO='';
           if($pendingatECI > 0 || $pendingatCEO >= 0 || $finalcompletedcount > 0){
            $pendingatRO=$listdata->totalcandidate-($pendingatCEO+$pendingatECI+$finalcompletedcount);
            $TotalPendingatRO += $pendingatRO;
            }

                                // get district start here
                                $detriectdetails = DB::table('m_ac')
                                ->where('ST_CODE',$listdata->st_code)
                                ->where('AC_NO',$listdata->ac_no)
                                ->groupBy('m_ac.DIST_NO_HDQTR')
                                ->get();
                                $districtids=[];
                                if(!empty($detriectdetails)){
                                foreach($detriectdetails as $item){                         
                                $districtids[]=$item->DIST_NO_HDQTR;
                                }

                                }

                                $allDistrict='';
                                if(!empty($districtids)){
                                foreach($districtids as $id)
                                { 
                                $district=getdistrictbydistrictno($listdata->st_code,$id);
                                $allDistrict.=$district->DIST_NAME.' ,';
                                }
                                }
                                $alldistricts1=rtrim($allDistrict,',');
                                // get district end here 
                                $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);


                                @endphp
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>
                                    <td>@if(empty($alldistricts1) && $alldistricts1=='' )   'N/A'  @else <b>{{  $alldistricts1 }}</b> @endif</td>
                                    <td align="right">{{$acdetails->AC_NO}}-{{$acdetails->AC_NAME}}</td>



                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/allcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($listdata->totalcandidate =='' )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</a></td>

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/Ecistartedcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($filedcount =='' )     0  @else  <b>{{ $filedcount }}</b> @endif</a></td>

                                    <td align="right"> <a href="{{url('/')}}/eci-expenditure/Ecinotstarted/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($notfiledcount =='' )     0  @else <b>{{  $notfiledcount }}</b> @endif </a></td>

                                    <td align="right"> <a href="{{url('/')}}/eci-expenditure/EcifinalbyDEO/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($finalbyDEO =='' )     0  @else <b>{{  $finalbyDEO }}</b> @endif </a></td>


                                    <td align="right"> @if($pendingatRO !='' )  <a href="{{url('/')}}/eci-expenditure/pendingatro/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" title="toalcandidate-pendingatCEO">    {{  $pendingatRO }}   @else <b> 0 </b>  </a> @endif</td>


<!-- <td align="right"> <a href="{{url('/')}}/eci-expenditure/noticeatdeo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($noticeatDEOCount =='' )     0  @else <b>{{  $noticeatDEOCount }}</b> @endif </a></td>-->

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/pendingatceo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($pendingatCEO =='')     0  @else <b>{{  $pendingatCEO }}</b> @endif</a></td>

<!-- <td align="right"><a href="{{url('/')}}/eci-expenditure/noticeatceo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($noticeatCEOCount =='')     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</a></td>-->

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/pendingateci/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($pendingatECI =='')     0  @else <b>{{  $pendingatECI }}</b> @endif</a></td>

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/closedbyeci/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($finalcompletedcount =='')     0  @else <b>{{  $finalcompletedcount }}</b> @endif</a></td>
                                </tr>
                                @php  $count++;  @endphp

                                @empty
                                <tr>
                                    <td colspan="6">No Data Found For Active Users</td>                  
                                </tr>
                                @endforelse

                                <?php
 
 
                                if (!empty($allStates)) {

                                   if(!empty($allStates[0]['st_code']) && $allStates[0]['st_code']=="All"){
                                        foreach ($permitstates as $item) {                                        
                                              $Totalac += DB::table('m_ac')
                                                ->where('ST_CODE', $item)                                                 
                                                ->count();
                                        }
                                               

                                          }else{
                                             foreach ($allStates as $item) {
                                              $Totalac += DB::table('m_ac')
                                                ->where('ST_CODE', $item['st_code'])
                                                ->where('AC_NO', $item['ac_no'])
                                                ->count();
                                              }
                                          }
                                   
                                }
                                ?>
                                <tr><td><b>Total</b></td>
                                    <td></td><td></td>
                                    <td align="right"><b>{{$Totalac>0 ? $Totalac:0}}</b></td><td align="right"><b>{{$TotalUsers}}</b></td><td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>{{$TotalFinalByDEO}}</b></td><td align="right"><b>{{$TotalPendingatRO}}</b></td><td align="right"><b>{{$TotalPendingatCEO}}</b></td><td align="right"><b>{{$TotalPendingatECI}}</b></td><td align="right"><b>{{$Totalfinalcompletedcount}}</b></td></tr>
                               
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