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
                         
                        <!--  <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr> -->
                          <tr>  
                           <!-- <td><strong>Phase:</strong> All Phase</td> -->
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
                  <td align="center" ><strong>Assembly Elections/Bye: {!! $heading_title !!}</strong></td>
                  </tr>
                </tbody>
                </table>
                
        <table class="table-strip" style="width: 100%;" border="1" align="center">
        
           <thead>
            <tr> 

              <tr> 
              <th rowspan="2" style="text-align: center;">SL NO </th>
              <th rowspan="2" style="text-align: center;">State </th>
               <th rowspan="2" style="text-align: center;">Number of <br>AC(s)</br> </th>
               <th rowspan="2" style="text-align: center;">Last Date<br> of Nomination</br> </th>
              <th colspan="2" style="text-align: center;">Nomination</th>
              
              
            
            </tr>
            <tr>  
               
              <th style="text-align: center;">Online</th>
              <th style="text-align: center;">Offline</th>
              
            </tr>
          </thead>
          <tbody id="oneTimetab">
          <?php $i=1; ?>   
              @foreach($results as $result)

               <?php $cals=0;  

               $onliecount=$result['online_nomination'];
               $totalcount=$result['online_nomination'] + $result['offline_nomination'];
               //$onliecount=150;
              // $tipslastmonth == 0 ? 0 : round( (float)$onliecount/$totalcount, 4);
               if(!empty($totalcount)){
              $cal=round( (float)$onliecount/$totalcount, 4)*100;
          }else{

            $cal=0;
          }  ?>
              <tr>
                <td><b>{{ $i++ }}</b></td>
                <td style="text-align: left;"><b>{{$result['label']}}</b> </td>
                <td style="text-align: right;"><b>{{$result['ac_count']}}</b> </td>
                  <td style="text-align: center;"><b>{{date('d-m-Y',strtotime($result['LDT_IS_NOM']))}}</b> </td>
                
                <td style="text-align: right;">
               {{$result['online_nomination']}}
                
                </td>
                <td style="text-align: right;">
                {{$result['offline_nomination']}}
                </td>
                
                
<!---->

 
              </tr>
              @endforeach

              <tr class="totalClass">
                <td></td>
            <td style="text-align:center"><b>Total</b></td>
            
             <td style="text-align:right"><b>{{$TotalAC}}</b></td>
            <td></td>
            <td style="text-align:right"><b>{{$onlineCount}}</b></td>
            <td style="text-align:right"><b>{{$offlineCount}}</b></td>
            </tr>
            <?php $total_nomination= $onlineCount + $offlineCount; ?>
            <tr class="">
            <td colspan="6" style="text-align:right"><b><h4>Total Nominations:   {{ $total_nomination }}</h4></b></td>
            
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