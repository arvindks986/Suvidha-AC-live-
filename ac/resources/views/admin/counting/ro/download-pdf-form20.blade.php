    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
    <style>
 
          div.footer {
              display: block; text-align: center;
              position: running(footer);
            }
   
    </style>   
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            
          <div class="row"> 
                 <div class="col"> <h4 class="mr-auto">FORM 20 <br> FINAL RESULT SHEET</h4> 
                    <p>[SEE RULE 56C(2)(C)]</p> <h5 class="mr-auto"> ELECTION TO THE HOUSE OF THE PEOPLE FROM THE 56 PARLIAMENTARY CONSTITUENCY PART-1</h5> 
                   </div>

                 </div>
                 <div class="row">
                 <div class="col"><p class="mb-0">(To be used dor Parliamentary and Assembly Election)</p>
                    <p class="mb-0">Total No. of  Electors in Assembly Constituency/segment  ....<b>{{$totalelectors->total}}</b></p>
                    <p class="mb-0">Name of  Assembly/segment  ...<b>{{$ac_no}}-{{$ac_name}}</b> Assembly Election</p>
                 </div>
         
                </div>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        @if(!empty($results))
        <table  class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr><th rowspan="2">Serial No. Of Polling Station</th>
                    <th colspan="{{$totalcandidate}}" align="text-center"> No of Valid Votes Cast in favour of</th>
                    <th rowspan="2"> Total of Valid Votes</th>
                    <th rowspan="2"> No. Of Rejected Votes</th>
                    <th rowspan="2"> NOTA</th>
                    <th rowspan="2"> Total </th>
                    <th rowspan="2"> No. Of Tendered Votes</th>
                </tr>
                <tr> 
                    @foreach($columecandidate as $cand)  
                          <th><div class="rotext">{{$cand->candidate_name}}</div></th> 
                    @endforeach
                     
                </tr>
                  
          </thead>
          <tbody> 
                @foreach($results as $record)   
                  <tr>  
                     @foreach($record as $rec)
                           <td>{{$rec}}</td>
                     @endforeach
                  </tr>      
                @endforeach 
                <tr><th rowspan="2">Total</th>
                   @foreach($grandsum as $d) 
                     
                          <th> {{$d}}  </th> 
                 @endforeach
                    
                </tr>
                </tr>
             </tbody>  
            </table> 
    
         
        @endif      
    <div class='footer'>Footer</div>
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="15">
                  <tbody>
                    <tr> <td align="left" colspan="2">Place:- {{$ac_no}}-{{$ac_name}} </td> <td align="right" colspan="2"> RETURNING OFFICER <br></td> </tr>
                    <tr> <td align="right" colspan="4"> {{$ac_no}}-{{$ac_name}} <br></td> </tr>
                  </tbody>
              </table>
      </div>
      <htmlpagefooter name="page-footer">
    <b>Page {PAGENO}</b>
</htmlpagefooter>  
    </body>
</html>