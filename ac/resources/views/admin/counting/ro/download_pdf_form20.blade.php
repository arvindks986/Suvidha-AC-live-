<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
      <style type="text/css">
        @page {
        header: page-header;
        footer: page-footer;
      }
      table .th{
            -ms-transform: rotate(-90deg); /* IE 9 */
            -webkit-transform: rotate(-90deg); /* Safari 3-8 */
            transform: rotate(-90deg);
            margin: 5px;
           }
      </style>
      
    </head>
    <body>

<htmlpageheader name="page-header">
  <div class="header_section">
           <table style="width:100%; padding:10px 0;" border="0"   cellpadding="5">
            <thead>
             <tr> <td style="width:100%; font-size:20px;" align="left" > User:-{{$user}} </td>
               <td style="width:100%; font-size:20px;" align="center" > Date:-{{$print_date}} </td>
               <td style="width:100%; font-size:20px;" align="right" > Encore Audit Ref.:-{{$ref_no}} </td> </tr>
            <<thead>
            </table>   
         <table style="width:100%;padding:10px 0;" border="0" align="center" cellpadding="5">
            <thead>
               <tr> <th style="width:100%; font-size:18px;margin:10px 0;" align="center" > 
                FORM 20 <br> FINAL RESULT SHEET</th></tr>
               <tr> <th style="width:100%; font-size:14px;margin:5px 0;" align="center" > 
                ELECTION TO THE LEGISLATIVE ASSEMBLY </th></tr>
              <tr> <th style="width:100%; font-size:14px;margin:5px 0;" align="center" > 
                <p class="mb-0">Total No. of  Electors in Assembly Constituency/segment  ....<u>{{$totalelectors->total}}</u></p>
              <p class="mb-0">Name of  Assembly/segment  ...<u>{{$ac_no}}-{{$ac_name}}</u> Assembly Election</p> </th></tr>    
              </thead>
          </table>   
   </div>
</htmlpageheader>
                   
        @if(!empty($results))
        <table style="width:100%; text-align:center;" border="1" align="center" cellpadding="0" cellspacing="0" >
            <thead>
                <tr><th rowspan="2" colspan="2" class="rotext">Serial No. Of Polling Station</th>
                    <th colspan="{{$totalcandidate}}"> No of Valid Votes Cast in favour of</th>
                    <th rowspan="2" class="rotext"> Total of Valid Votes</th>
                    <th rowspan="2" class="rotext"> No. Of Rejected Votes</th>
                    <th rowspan="2" class="rotext"> NOTA</th>
                    <th rowspan="2" class="rotext"> Total </th>
                    <th rowspan="2" class="rotext"> No. Of Tendered Votes</th>
                    
                </tr>
                <tr> 
                    @foreach($columecandidate as $cand)  
                          <th><div class="rotext">{{$cand->candidate_name}}</div></th> 
                    @endforeach
                     
                </tr>
                  
          </thead>
          <tbody> 
                <?php  
                for($i=0; $i<count($sub_array_res); $i++) {  ?>
                
                @foreach($sub_array_res[$i]['results'] as $record)   
                  <tr hight="35"> 
                     @foreach($record as $rec)
                           <td>{{$rec}}</td>
                     @endforeach
                  </tr>      
                @endforeach 
               <?php /*<tr hight="35" bgcolor="#000000;" >
                <?php $j=0; ?> 
               @foreach($sub_array_res[$i]['page_sum'] as $record) 
                        @if($j==0) 
                              <td style="color:#FFF;">Total</td> 
                        @else 
                              <td style="color:#FFF;">{{$record}}</td>
                        @endif 
                        <?php $j++; ?> 
                  @endforeach 
                </tr>     
                <tr hight="35"><th>Grand Total</th>
                @foreach($grandsum as $d) 
                     
                          <th> {{$d}}  </th> 
                 @endforeach
                </tr>  
              <?php */ }    ?>

                 <tr><th  colspan="2">Total EVM Votes </th>
                   @foreach($grandsum as $d) 
                     
                          <th> {{$d}}  </th> 
                 @endforeach
                    
                </tr>
                <tr><td  colspan="2">Total Postal Ballot Votes </td>
                   @foreach($postal_vote as $d) 
                     <td> {{$d}}  </td> 
                 @endforeach
                    
                </tr>
                <tr><th  colspan="2">Total Votes Polled</th>
                   @foreach($grand_allsum as $d) 
                     
                          <th> {{$d}}  </th> 
                 @endforeach
                    
                </tr> 

             </tbody>  
            </table> 
            <div >

            </div>
        @endif          
   
          <table style="width:100%; text-align:center;" border="0" align="center" cellpadding="0" cellspacing="0" >
            <tr><td align="left" colspan="4"> &nbsp;</td>   </tr>
            <tr><td align="left" colspan="4"> &nbsp;</td>   </tr>
             <tr><td align="left">Place:-</td> <td align="left">{{$ac_name}}</td> <td align="right">&nbsp;</td><td align="right">&nbsp;</td> </tr>
             <tr><td align="left">&nbsp;</td> <td align="left">&nbsp;</td> <td align="right">RETURNING OFFICER</td><td align="right">&nbsp;</td> </tr>
             <tr><td align="left">&nbsp;</td> <td align="left">&nbsp;</td> <td align="right">{{$ac_no}}-{{$ac_name}} </td><td align="right">&nbsp;</td> </tr>
          </table> 

           <htmlpagefooter name="page-footer">
    <b>Page {PAGENO}</b>
</htmlpagefooter>       
          <tbody>
    </body>
</html>