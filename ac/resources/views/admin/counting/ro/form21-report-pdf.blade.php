<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Form 21 E Details</title>
		 
    </head>
    <body>
<?php
ini_set("pcre.backtrack_limit", "5000000");
if(isset($winning_candidate)){
?>
           <p align="right" class="text-right"><small style="font-size:10px;"> Encore Audit Ref.:-  {!!$ref_no!!} </small></p>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  padding:10px 0;" border="0" align="center" cellpadding="0">
               <thead>
				<?php if($winning_candidate->status=='0'){?>
                <tr> <th style="width:100%; font-size:25px;margin:5px 0;color: blue;" align="center" >(Preview)</th></tr>
                <?php }?>
                <tr> <th style="width:100%; font-size:25px;margin:5px 0;" align="center" > Conduct of Elections Rules, 1961 </th></tr>
                <tr> <th style="width:100%; font-size:15px;margin:5px 0;" align="center" >(Statutory Rules And Order)</th></tr>
                <tr> <th style="width:100%; font-size:25px; margin:5px 0;" align="center" > FORM-21E </th></tr>
                <tr> <th style="width:100%; font-size:14px; margin:5px 0;" align="center" >(See Rule 64)</th></tr>
        	<tr> <th style="width:100%; font-size:20px;margin:5px 0;" align="center" >Return of Election</th></tr>
                <tr><td style="line-height: 32px;font-size: 18px;">Election to the Legislative Assembly of <b><u><?php if(isset($state)){ echo $state;}?></u></b>  from <b><u><?php if(isset($ac_name)){?>{{$ac_no}}-{{$ac_name}}<?php }?></u></b> Assembly constituency.</td></tr>
              </thead>
            </table>
         <table align="center" width="100%"> 
                <tbody> 
 
                </tbody>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
          .showname {font-size: 18px; margin-left: 10px; margin-right:10px; font-weight:bold;  text-decoration-line: underline;
              text-decoration-style: solid; border-bottom:1px solid #000;}
      </style>
        <table style="width:100%;" border="0" align="center">  
             <tr> <td  style="width:100%;" align="center"> <h2>RETURN OF ELECTION</h2> </td> </tr>
         </table>
       <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="2">
            <thead>  
            <tr>
            <th>Serial No.</th>
            <th>Name of Candidate</th>
            <th>Party Affiliation</th>
            <th>Number of votes polled</th>
            </tr>
        </thead>
        <tbody>   
        @php $i=0;
        @endphp
        @if(!empty($cont_list))
        @foreach($cont_list as $list)
        @php $i++;
        @endphp
        <tr>
            <td>{{$i}}</td>
            <td align="left">{{$list->candidate_name}}</td>
            <td align="left">{{$list->party_name}}</td>
             <td align="right">{{$list->total_vote}}</td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
        @endif
       </tbody>
     </table>
      <br/>
      <table style="width: 100%;margin-bottom:5px;">
       
      <tbody>
          <tr> <td style="font-size: 14px;" width="32%">Total numbers of electors:</td>
                     <td align="right"><?php if(isset($tot_electrol)){echo $tot_electrol + @$service_vote;}?></td>
          </tr>
      </tbody>
    </table>
    <table style="width: 100%;margin-bottom:5px;">
       <tbody>
            <tr>
                    <td style="font-size: 14px;" width="45%">Total numbers of valid votes polled:</td>
                     <td align="right"><?php if(isset($total_validpol)){echo $total_validpol;}?></td>
                </tr>
            
      </tbody>
    </table>
    <table style="width: 100%;margin-bottom:5px;">
       <tbody>
            <tbody>
            <tr>
                    <td style="font-size: 14px;" width="45%">Total numbers of None of the above (NOTA):</td>
                     <td align="right"><?php if(isset($total_nota)){echo $total_nota;}else{echo '0';}?></td>
                </tr>
      </tbody>
            
      </tbody>
    </table>
     
    <table style="width: 100%;margin-bottom:5px;">
       <tbody>
           <tr> 
            <td style="font-size: 14px;" width="40%">Total numbers of rejected votes:</td>
            <td align="right"><?php if(isset($round_details)){?>{{@$round_details->rejected_votes}}<?php }else{echo '0';}?></td>
          </tr>
      </tbody>
    </table>            
    <table style="width: 100%;margin-bottom:5px;">
       <tbody>
           <tr>
             <td style="font-size: 14px;" width="41%">Total numbers of tendered votes:</td>
              <td align="right"><?php if(isset($round_details)){?>{{@$round_details->tended_votes}}<?php }else{echo '0';}?></td>
            </tr>
      </tbody>
    </table>
    <table style="width: 100%;margin-bottom:5px;">
       <tbody>
          <tr> <td style="font-size: 12px;" width="41%">I declare that :- </td>  </tr>
       <tr> <td ><span class="showname"> @if(isset($winning_candidate)) 
                        @if(isset($winning_candidate->lead_cand_name)) 
                              {{$winning_candidate->lead_cand_name}} 
                        @endif 
                      @endif </span></td>
        
    </tr>
</tbody>
</table>
<table style="width: 100%;margin-bottom:5px;">
    <tbody><tr>
        <td   align="left">of </td>
        <td ><span class="showname">@if(isset($winning_candidate))
               <?php  
                    // $a="DIST"; $b="DISTRICT"; $c="DISTT"; 
                    // $address=str_replace(strtolower($dist),'',strtolower($winning_candidate->candidate_residence_address));
                    // $address=str_replace(strtolower($b),'',strtolower($address));
                    // $address=str_replace(strtolower($c),'',strtolower($address));
                    //$address=str_replace(strtolower($a),'',strtolower($address));  
                   ?>  
                    {{ucwords($winning_candidate->candidate_residence_address)}}
              @endif</span></td>
        
    </tr>
</tbody>
</table>
<table style="width: 100%;margin-bottom:5px;">
    <tr align="left">
        <td>has been duly elected to fill the seat.</td>
        
    </tr>
</table>
 
<table style="width: 100%;margin-bottom: 5px;">
    <tr><td style="text-align: left;">Place:- <span>{{$ac_name}}</span></td><td></td></tr>   
</table>
<table style="width: 100%;margin-bottom: 5px;">
     <tr>   <td>Date:-</td><td>{{$print_date}}</td> 
            <td align="right">Returning Officer <br><br> {{$ac_no}}-{{$ac_name}}</td>
    </tr>
</table>
<htmlpagefooter name="page-footer">
    <b>Page {PAGENO}</b>
</htmlpagefooter>
<?php }else{?>
<div style="padding:50px">No record available, result not declared yet.</div>
 <?php } ?>
 
 </body>
</html>

  
