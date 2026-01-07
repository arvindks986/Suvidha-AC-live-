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
  .table-strip{border-collapse: collapse; font-size:12px;}
  .table-strip th,.table-strip td{text-align: center;}
  .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
  .header_section{
    height:5px !important;
    width: 100%;
    float: left;
  }
  .small{
    display: none;
  }
</style>
</head>
<body>
   
   <div class="header_section">
	<htmlpageheader name="page-header">
		@if(isset($publish) && $publish=='0')
		<p align="center" class="text-center"> <span style="font-size:20px;font-weight:bold;color:blue;"> (Preview)</span></p>
		@endif
	</htmlpageheader>
		
      <p align="right" class="text-right"> <small style="font-size:10px;">Date.:-  {!!$print_date!!} </small><small style="font-size:10px;"> Encore Audit Ref.:-  {!!$ref_no!!} </small></p>
      <!--HEADER STARTS HERE-->
      <table style="width:100%;padding:5px 0;" border="0" align="center" cellpadding="5">
        <thead>
          <tr> <th style="width:100%; font-size: 25px;margin:20px 0;" align="center" > Annexure for Tabulating Trends / Results </th></tr>

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
        <tr> <td> State: <b>{!! $st_code !!}-{!! $st_name !!}</b></td>
          <td>Number & Name of the constituency :<b style="min-width: 250px;">{!! $ac_no !!}-{!! $ac_name !!}</b></td> 
          <td>Round Number :<b style="min-width: 250px;">{!! $round !!}</b></td> 
        </tr>
      </tbody>
    </table>  
  </div>
 
  <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
    <thead>
      <tr><th colspan="2">Table No.</th>
        <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
        <th>{{$i}}</th> 
        <?php  } ?>
        <th rowspan="2">Total</th><th rowspan="2"  style="width:70px;">Brought From Previous Round</th>
        <th rowspan="2" style="width:70px;">Cumulative Total</th> </tr>
        <tr><th colspan="2">Polling Booth Number</th>
          <?php for($i=1; $i<=$total_no_tables; $i++) { $field="ps".$i; ?>
          <th> {{$pollingstationlist[$field]}}  </th> 
          <?php  } ?>  </tr>
          <tr><th>Sr No.</th><th>Candidate Name</th>
            <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
            <th>  </th> 
            <?php  } ?> <th>   </th><th>   </th><th>   </th> </tr>
          </thead>
          <tbody>
            <?php  $j=0; $k=0;   $sum = 0;?>
            @if(!empty($results))
            @foreach($results as $md)  
            <?php $j++;   ?>
            <tr><td>{{$j}}</td> <td align="left"  style="width:200px;">{{$md['candidate_name']}} </td> 
              <?php for($i=1; $i<=$total_no_tables; $i++) { $field="table".$i;  ?>
              <td> {{$md[$field]}} </td> 
              <?php  } ?>
              <td> @if($md['total']>0){{$md['total']}} @else 0 @endif </td> 
			  <td>@if($md['previous_total']>0){{$md['previous_total']}} @endif </td> 
              <td>@if($md['accumlative_total']>0){{$md['accumlative_total']}} @else 0  @endif </td></tr>

              <?php $k++; ?> 
               
              @endforeach 
              <tr><td colspan="2">Total</td>
                <?php for($i=1; $i<=$total_no_tables; $i++) {  $field="table".$i;?>
                <td> @if($grandresults->$field > 0){{$grandresults->$field}} @endif</td> 
                <?php  } ?>  <td>@if($grandresults->total >0){{$grandresults->total}}@endif</td><td>@if($grandprevious >0){{$grandprevious}}@endif</td><td>@if($grandtotal >0){{$grandtotal}}@endif</td></tr>  

                <tr style="height:50px"><td  style="height:50px" colspan="2">Initial of Ro</td>
                  <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
                  <td> &nbsp; </td> 
                  <?php  } ?>  <td>&nbsp; </td> <td>&nbsp;</td><td>&nbsp;</td></tr>
                  <tr style="height:50px"><td  style="height:50px" colspan="2">Initial of Observer</td>
                    <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
                    <td> &nbsp; </td> 
                    <?php  } ?>  <td> &nbsp; </td> <td>&nbsp;</td><td>&nbsp;</td></tr>
                    @endif 
                  </tbody> 
                </table>
            

 
                <div style="page-break-before:always">&nbsp;</div> 
  <div class="header_section" style="margin-top:0px;">
                    <p align="right" class="text-right"> 
                      <small style="font-size:10px;"> Encore Audit Ref.:-  {!!$ref_no!!} </small></p>

                       
                       <!--HEADER STARTS HERE-->
      <table style="width:100%;padding:5px 0;" border="0" align="center" cellpadding="5">
        <thead>
          <tr> <th style="width:100%; font-size: 25px;margin:20px 0;" align="center" >Election Commission of India </th></tr>
          <tr><th  style="width: 100%; text-align: center;font-weight: bold;font-size: 20px;">
                              Round Declaration Form  </th> </tr>
        </thead>
      </table>
      <!--HEADER ENDS HERE-->
   
        <table style="width:100%" border="0" align="center">  

                      <tr> <td  style="width:100%;">
                        <table  style="width:100%;padding: 15px 0;">
                          <tbody>
                            <tr> <td> State: <b>{!! $st_code !!}-{!! $st_name !!}</b>  </td>
                            <td align="right">Date <u>{!! $print_date !!} </u></td>
                          </tr>
                          <tr>
                            <td>Election: <b>{!! $election !!}</b></td>
                            <td align="right">
                              Round Number <u style="min-width: 250px;">{!! $round !!}</u>
                            </td>
                           </tr>
                          <tr>  
                            <td>Number & Name of the constituency <b style="min-width: 250px;">{!! $ac_no !!}-{!! $ac_name !!}</b></td>
                          </tr>

                        </tbody>
                      </table>  
                    </td>

                  </tr>
            </table>
            </div>
             

              <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
                <thead>     

                  <tr>
                    <th>Sr. no.</th>
                    <th>Candidate Name</th>
                    <th>Party</th>
                    <th>Votes brought from Previous rounds</th>
                    <th>Votes from current round</th>
                    <th>Total Cumulative Votes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php  $j=0;   
                  foreach ($results as $result) {   $j++; ?>
                  <tr>
                    <td>{!! $j !!}</td>
                    <td align="left"  style="width:150px;">{!! $result['candidate_name'] !!}</td>
                    <td>{!! $result['party_name'] !!}</td>
                    <td>{!! $result['previous_total'] !!}</td>
                    <td>{!! $result['total'] !!}</td>
                    <td>{!! $result['accumlative_total'] !!}</td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
             
                <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="5">
                  <tbody>
                    <tr style="height: 70px"><td colspan="4">&nbsp;</td></tr>
                    <tr> <td align="left" colspan="2">RETURNING OFFICER</td> <td align="right" colspan="2">  Observer </td> </tr>
                    <tr> <td align="left" colspan="2"> {{$ac_no}}-{{$ac_name}}  </td><td align="right" colspan="2"> &nbsp; </td> </tr>
                  </tbody>
              </table>
 
                <htmlpagefooter name="page-footer">
    <b>Page {PAGENO}</b>
</htmlpagefooter>
            </body>
            </html>
