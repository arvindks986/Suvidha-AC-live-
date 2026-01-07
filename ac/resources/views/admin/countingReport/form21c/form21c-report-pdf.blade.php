<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Form 21 C</title>
    </head>
    <body>
	<?php if($wincan){
            //if($wincan->status=='1'){
        ?>
         <p align="right" class="text-right"><small style="font-size:10px;"> Encore Audit Ref.:-  {!!$ref_no!!} </small></p>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  padding: 25px 0;" border="0" align="center" cellpadding="0">
               <thead>
			    <?php if($wincan->status=='0'){?>
                    <tr> <th style="width:100%; font-size: 30px;margin:5px 0;color: blue;" align="center" >(Preview)</th></tr>
                <?php }?>
                <tr> <th style="width:100%; font-size: 22px;margin:5px 0;" align="center" > Conduct of Elections Rules, 1961 </th></tr>
        		<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" >(Statutory Rules And Order)</td></tr>
        		<tr> <th style="width:100%; font-size:14px;margin:5px 0;" align="center" >FORM 21C</th></tr>
        		<tr><th>&nbsp;</th></tr>
				<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" >(See Rule 64) </td></tr>
				<tr><th>&nbsp;</th></tr>
		        <tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" >(For use in General Election when seat is contested) </td></tr>
              </thead>
            </table>
			<table align="center" width="100%"> 
				<tbody> 
					<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" >Declaration of the result of Election under section 66 of the Representation of the People Act, 1951. </td></tr>
				</tbody>
			</table><br>
			<table align="center" width="100%"> 
				<tbody> 
                                        <tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" >*Election to the Legislative Assembly of <b><u><?php if(isset($ac_state)){ echo $ac_state;}?></u></b>   from <b><u><?php if(isset($acname)){?>{{$acname}}<?php }?></u></b> Assembly constituency.</td></tr>
					<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" >In pursuance of the provisions contained in section 66 of the Representation of the People Act, 1951, read with rule 64 of the Conduct of Elections Rules, 1961, I declare that- </td></tr>
				</tbody>
			</table><br>
			<table align="center" width="100%"> 
				<tbody> 
					<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" ><b><?php if(isset($wincan)){?>{{@$wincan->lead_cand_name}}<?php  
                        $a="DIST"; $b="DISTRICT"; $c="DISTT"; 
            $address=str_replace(strtolower($dist),'',strtolower($wincan->candidate_residence_address));
            $address=str_replace(strtolower($b),'',strtolower($address));
            $address=str_replace(strtolower($c),'',strtolower($address));
            $address=str_replace(strtolower($a),'',strtolower($address));  

           } ?></b> </td></tr>
                                        <tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" ><b>
                                          <?php if(isset($wincan)){?>{{strtolower($wincan->candidate_residence_address)}}

                                          <?php //if(isset($dist)){ echo ', '.$dist;}?><?php //if(isset($state)){ echo ', '.$state;}?><?php }?></b>  [sponsored by <b><?php if(isset($wincan)){?>{{@$wincan->lead_cand_party}}<?php }?></b>  ] has been duly elected to fill the seat in that House from the above constituency.</td></tr>
				</tbody>
			</table>
        <!--HEADER ENDS HERE-->
<br>
<table width="100%" align="left"> 
<tbody>
    <tr><td style="text-align: left;">Place : &nbsp;</td><td></td></tr>   
        <tr><td style="text-align: left;">Date : {{ date('d-m-Y') }}</td><td></td></tr>    
</tbody></table>
<br><br><br><br><br><br><br>
<table width="100%">
	 <tbody>
         <tr><td style=" width: 600px;">&nbsp;</td><td>&nbsp;</td><td style="width: 25%;font-size:18px;" align="center"><b><?php if(isset($user_data)){?>({{@$user_data->name}})<?php }?></b></td> <td></td></tr>
	 <tr><td style=" width: 600px;">&nbsp;</td><td>&nbsp;</td><td style="width: 50%;font-size:18px;" align="center"><b>Returning Officer</b></td> <td></td></tr>
          <tr><td style=" width: 100px;">&nbsp;</td><td>&nbsp;</td><td style="width: 25%;font-size:18px;" align="center"><b><?php if($user_data){?>{{$ac_name1}}<?php }?> <br> Legislative Assembly Constituency</b></td> <td></td></tr>
	 </tbody></table>
	 <?php }else{?>
<div style="padding:50px">No record available, result not declared yet.</div>
<?php }?>
 </body>
</html>

 
