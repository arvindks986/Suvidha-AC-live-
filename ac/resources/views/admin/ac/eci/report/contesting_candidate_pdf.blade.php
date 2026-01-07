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
                         
                         <tr>  
                          <!-- <td><strong>Phase:</strong>All Phase</td> -->
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
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
              <<th  rowspan="2" style="text-align: center;">SL NO </th>
              <th  rowspan="2" style="text-align: center;">State </th>
              <!-- <th  style="text-align: center;">Phase </th> -->
              <th   rowspan="2" style="text-align: center;">Contesting Candidate</th>
              <th colspan="3" style="text-align: center;">Age Groups</th>
              <th rowspan="2"  style="text-align: center;">Male</th>
              <th rowspan="2"  style="text-align: center;">Female</th>
<?php if($tgcountis > 0 ) { ?>

              <th rowspan="2"  style="text-align: center;">TG</th>
            <?php } ?>
              <th  rowspan="2"  style="text-align: center;">ST/SC</br></th>
              <th rowspan="2"  style="text-align: center;">Criminal <br>Antecedents</br></th>
              <!-- <th rowspan="2" style="text-align: center;">Percentage</th> -->
              
                   
            
            </tr>
           <tr>
              <th>25-40</th>
              <th>41-60</th>
              <th>61 To Above</th>
             
               
            </tr>
          </thead>
          <tbody id="oneTimetab">
          <?php $i=1; ?>   
              @foreach($results as $result)
              <tr>
                <td style="text-align:center"><b>{{ $i++ }}</b></td>
                <td style="text-align:left">{{$result['label']}} </td>
                <!-- <td style="text-align:right">{{$result['phase']}} </td> -->


                
                <td style="text-align:right">
               {{count($result['nomination'])}}
                
                </td>
                <td style="text-align:right">
               {{$result['Agefrom25']}}
                
                </td>
                   <td style="text-align:right">
               {{  $result['Agefrom40']}}
                
                </td>
                   <td style="text-align:right">
               {{ $result['Agefrom60']}}
                
                </td>
                <td style="text-align:right">
                {{count($result['male'])}}
                </td>
                <td style="text-align:right">{{count($result['female'])}}</td>

                   <?php if($tgcountis > 0 ) { ?>
                <td style="text-align:right">
                {{count($result['tg'])}}
                </td>
              <?php } ?>



                
                <td style="text-align:right">{{count($result['category'])}}</td>

                 <td style="text-align:right">
               {{count($result['cadetail'])}}
                
                </td>
                 
                
<!---->

 
              </tr>
              @endforeach
 <tr class="totalClass">
           
            <td></td>
             <td style="text-align:center"><b>Total</b></td>
            
            <td style="text-align:right">{{$TotalContesting}}</td>
            <td style="text-align:right">{{$TotalAge_from_25}}</td>
            <td style="text-align:right">{{$TotalAge_from_40}}</td>
            <td style="text-align:right">{{$TotalAge_from_60}}</td>
            <td style="text-align:right">{{$TotalMale}}</td>
            <td style="text-align:right">{{$TotalFemale}}</td>
           <?php if($tgcountis > 0 ) { ?>
            <td style="text-align:right">{{$TotalTg}}</td>
          <?php } ?>
            <td style="text-align:right">{{$TotalCategory}}</td>
            <td style="text-align:right">{{$TotalCA}}</td>
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