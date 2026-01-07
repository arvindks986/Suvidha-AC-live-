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
      .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
          .header_section{
            height:50px !important;
            width: 100%;
            float: left;
          }
          .small{
            display: none;
          }
      </style>
    </head>
    <body>

<htmlpageheader name="page-header" >
  <div class="header_section1">

         <!--HEADER STARTS HERE-->
            <table style="width:100%;padding: 25px 0;" border="0" align="center" cellpadding="5">
               <thead>
                <tr> <th style="width:100%; font-size: 20px;margin:20px 0;" align="center" > Annexure for Tabulating Trends / Results </th></tr>
                
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%" border="0" align="center">  
          <tbody>
                <tr> <td> State: <b>{!! $st_name !!}</b></td>
                           <td>Number & Name of the constituency :<b style="min-width: 250px;">{!! $ac_no !!}-{!! $ac_name !!}</b></td> 
                           <td>Round Number :<b style="min-width: 250px;">{!! $round !!}</b></td> 
                        </tr>
                    </tbody>
          </table>  
                  

        </div>
</htmlpageheader>
        <?php $c=0; 
          for($k=0; $k<$noofpage; $k++) { 
           $r1count=count($row1[$k]);  $r1count= $r1count-4;  
           $r2count=count($row1[$k]);  $r2count= $r2count-4; 
           $r3count=count($row1[$k]);  $r3count= $r3count-4;
           $pagecount=count($pagetotal[$k]);  $pagecount= $pagecount-4; 
            
          ?>          
        <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
          <thead>
                  <tr><th colspan="2">{{$row1[$k][0]}}</th>
                        <?php for($i=1; $i<=$r1count; $i++) { ?>
                                  <th>{{$row1[$k][$i]}}</th> 
                         <?php  } ?>
                    <th>{{$row1[$k][7]}}</th> 
                    <th>{{$row1[$k][8]}}</th> 
                    <th>{{$row1[$k][9]}}</th> 
                 </tr>
                 <tr><th colspan="2">{{$row2[$k][0]}}</th>
                          <?php for($i=1; $i<=$r2count; $i++) { ?>
                                  <th>{{$row2[$k][$i]}}</th> 
                         <?php  } ?>
                          <th>{{$row2[$k][7]}}</th> 
                        <th>{{$row2[$k][8]}}</th> 
                        <th>{{$row2[$k][9]}}</th> 
                       </tr>

                <tr><th>{{$row3[$k][0]}}</th><th>{{$row3[$k][1]}}</th>
                        <?php for($i=2; $i<=$r2count+1; $i++) { ?>
                                <th>{{$row3[$k][$i]}}</th> 
                       <?php  } ?>
                        <th>{{$row3[$k][7]}}</th> 
                         <th>{{$row3[$k][8]}}</th> 
                        <th>{{$row3[$k][9]}}</th>   </tr> 
               
                 
        </thead>
                  <tbody> <?php $j=1; ?> 
                          @foreach($table[$k] as $tab)  <?php   $colcount=count($tab);  $colcount=$colcount-4;   ?>
                                   <tr> <td>{{$j}}</td>
                                      <?php for($i=0; $i<=$colcount; $i++) { ?>
                                              <td>{{$tab[$i]}}</td> 
                                           <?php  } 
                                                 $j++; ?>
                                      <td>{{$tab[7]}}</td>  <td>{{$tab[8]}}</td> <td>{{$tab[9]}}</td>   </tr> 
                             
                          @endforeach  
                        <tr><td colspan="2">{{$pagetotal[$k][0]}}</td>
                                    <?php for($i=1; $i<=$pagecount; $i++) { ?>
                                            <td>{{$pagetotal[$k][$i]}}</td> 
                                   <?php  } ?>
                              <td>{{$pagetotal[$k][7]}}</td>  <td>{{$pagetotal[$k][8]}}</td> <td>{{$pagetotal[$k][9]}}</td>   </tr>
                        <tr style="height: 50px"><td colspan="2" style="height: 50px">&nbsp;</td>
                                    <?php for($i=1; $i<=$pagecount+1; $i++) { ?>
                                            <td style="height: 50px">Initial Of Ro</td> 
                                   <?php  } ?>
                              <tdstyle="height: 50px">&nbsp;</td >  <td style="height: 50px">&nbsp;</td>  </tr>
                      <tr style="height: 50px"><td colspan="2" style="height: 50px">&nbsp;</td>
                                    <?php for($i=1; $i<=$pagecount+1; $i++) { ?>
                                            <td style="height: 50px">Initial Of Observer</td> 
                                   <?php  } ?>
                              <td style="height: 50px">&nbsp;</td>  <td style="height: 50px">&nbsp;</td>  </tr>    
          </tbody> 
     </table>
      <?php } ?>

    </body>
</html>