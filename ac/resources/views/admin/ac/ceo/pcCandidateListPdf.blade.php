    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Duplicate Symbol Candidate Reports</title>
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
      date_default_timezone_set('Asia/Kolkata'); 
  ?>

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/images/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
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
                    <table  style="width:100%">ST_NAME
                      <tbody>
                         <tr>
                           <td><strong>Candidate List PC Wise</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{$st->ST_NAME}}</td>
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
                <thead>
          <tr>
          <th>Serial No</th> 
          <th>PC Number&Name</th> 
          <th>Candidate Name</th> 
          <th>Party Name</th> 
          <th>Symbol</th>
            </tr>
        </thead>
        <tbody> 
             <?php $count = 1; 
       
       if(count($candListbyPC)>1){ ?>
       
         @foreach($candListbyPC as $candListbyPCData)
         <?php
          $candidatedetails=getById('candidate_personal_detail','candidate_id',$candListbyPCData->candidate_id);
          $partyDetails=getById('m_party','CCODE',$candListbyPCData->party_id);
          $pcDetails=getpcbypcno($user_data->st_code,$candListbyPCData->pc_no);
          $symbolDetails=getsymbolbyid($candListbyPCData->symbol_id);
         // print_r( $candidatedetails);
         ?>@if(isset($symbol_data)) {{$symbol_data->SYMBOL_DES}} @endif
          <tr>
            <td>{{$count}}</td>  
            <td >{{ $candListbyPCData->pc_no.' - '.$pcDetails->PC_NAME}}</td>
            <td >{{ $candidatedetails->cand_name}}</td>
            <td >@if(isset($partyDetails)){{ $partyDetails->PARTYNAME }} @endif</td>
            <td >@if(isset($symbolDetails)){{$symbolDetails->SYMBOL_DES  }} @endif</td>
          </tr>
          <?php $count++ ?>
          @endforeach
          <?php } else { ?>
          <tr>
            <td class="col-md-6" colspan='6'> <p>No Records  Founds </p></td>
          </tr>   
          <?php }  ?>
      
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