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
                           <td><strong>{!! $heading_title !!}</strong></td>
                         </tr>
                         <tr>  
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
        
           <thead>
            <tr> 

              <th rowspan="2">Constituency Name</th>
              <th colspan="1">Before Scrutiny</th>
              <th colspan="4">After Scrutiny</th> 
            
            </tr>
            <tr>  
              <th>Total Nomination Applied</th>
              <th>Accepted</th> 
              <th>Rejected</th>
              <th>Withdrawn</th>
              <th>Contesting</th> 
            </tr>
          </thead>
          <tbody id="oneTimetab">   
              @foreach($results as $result)
              <tr>
                <td>{{$result['label']}} </td>
                <td>
                @if($result['total_applied']>0)
                <a href="<?php echo $action.'/detail/applied?'.$result['filter'] ?>">
                {{$result['total_applied']}}
                </a>
                @else
                {{$result['total_applied']}}
                @endif

                </td>
                
                <td>
                @if($result['total_accepted']>0)
                <a href="<?php echo $action.'/detail/accepted?'.$result['filter'] ?>">
                {{$result['total_accepted']}}</a>
                @else
                {{$result['total_accepted']}}
                @endif
                </td>

                <td>
                @if($result['total_rejected']>0)
                <a href="<?php echo $action.'/detail/rejected?'.$result['filter'] ?>">
                {{$result['total_rejected']}}</a>
                @else
                {{$result['total_rejected']}}
                @endif
                </td>

                <td>
                @if($result['total_withdraw']>0)
                <a href="<?php echo $action.'/detail/withdraw?'.$result['filter'] ?>">
                {{$result['total_withdraw']}}</a>
                @else
                {{$result['total_withdraw']}}
                @endif

                </td>
                
                <td>
                @if($result['total_contested']>0)
                <a href="<?php echo $action.'/detail/contested?'.$result['filter'] ?>">
                {{$result['total_contested']}}</a>
                @else
                {{$result['total_contested']}}
                @endif
                </td> 
           
                
              </tr>
              @endforeach

       








            
              <tr>
                <td>{{$totals['label']}} </td>
                <td>{{$totals['total_applied']}}</td>
                <td>{{$totals['total_accepted']}}</td>
                <td>{{$totals['total_rejected']}}</td>
                <td>{{$totals['total_withdraw']}}</td>
                <td>{{$totals['total_contested']}}</td>
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