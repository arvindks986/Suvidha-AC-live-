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
                        <!-- <tr>  
                           <td align="right"><strong>Report From:</strong> 18-Mar-2019</td>
                         </tr> -->
                         <tr>  
                           <td align="right"><strong>Report To:</strong>  {{ date('d-M-Y') }}</td>
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
                   <th>Serial No</th>
                   <th>Phase</th>
                    <th>State/Uts</th> 
                    <th>Total Nominations Filed</th> 
                    <th>National Parties</th> 
                    <th>State Parties</th> 
                    <th>Other Parties</th> 
                    <th>Independent</th> 
                    <th>Male</th> 
                    <th>Female</th> 
					<th>Others</th> 
                    <th>Total Valid Nominations</th>
                </tr>
            </thead>
            <tbody>
        @php  
        $count = 1; 

        $TotalNomination = 0; 
        $TotalNational = 0;
        $TotalState = 0;
        $TotalOther= 0;
        $TotalIndependent = 0;
        $TotalMale = 0;
        $TotalFemale = 0;
		$TotalOthers = 0;
        $TotalValidNomination=0;


        @endphp

         @forelse ($EciPhaseInfoDataPdf as $key=>$listdata)

         @php 

         $TotalNomination             +=   $listdata->TOTAL_NOMINATION;
         $TotalNational               +=   $listdata->NATIONAL;
         $TotalState                  +=   $listdata->STATE;
         $TotalOther                  +=   $listdata->OTHER;
         $TotalIndependent            +=   $listdata->INDEPENDENT;
         $TotalMale                   +=   $listdata->male;
         $TotalFemale                 +=   $listdata->female;
		 $TotalOthers                 +=   $listdata->others;
         $TotalValidNomination        +=   $listdata->total;

        @endphp


          <tr>
             <td>{{ $count }}</td>
             <td>Phase-{{$listdata->StatePHASE_NO}}</td>
            <!-- <td><a href="{{url('/eci/EciNominationStateWiseReport')}}/{{base64_encode($listdata->ST_CODE)}}">{{$listdata->ST_NAME }}</a></td> -->
            <td>{{$listdata->ST_NAME }}</td>
            <td>{{$listdata->TOTAL_NOMINATION }}</td>
            <td>{{$listdata->NATIONAL }}</td>
            <td>{{$listdata->STATE }}</td>
            <td>{{$listdata->OTHER }}</td>
            <td>{{$listdata->INDEPENDENT }}</td>
            <td>{{$listdata->male }}</td>
            <td>{{$listdata->female }}</td>
			<td>{{$listdata->others }}</td>
            <td><b>{{$listdata->total }}</b></td>
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Valid Nomination Data</td>                 
              </tr>
          @endforelse
           <tr class="totalClass">
            <td><b>Total</b></td>
            <td></td>
            <td></td>
            <td><b>{{$TotalNomination}}</b></td>
            <td><b>{{$TotalNational}}</b></td>
            <td><b>{{$TotalState}}</b></td>
            <td><b>{{$TotalOther}}</b></td>
            <td><b>{{$TotalIndependent}}</b></td>
            <td><b>{{$TotalMale}}</b></td>
            <td><b>{{$TotalFemale}}</b></td>
			<td><b>{{$TotalOthers}}</b></td>
            <td><b>{{$TotalValidNomination}}</b></td>
            
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