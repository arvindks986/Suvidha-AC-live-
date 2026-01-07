<!DOCTYPE html>
<html lang="en">
<head>
<title>&nbsp;</title>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
.table{width: 100%; border-collapse: collapse;  font-family: Verdana; margin: auto; color: #000;}
tr.declaredbg {background-color: #e5fbe3;}
tr.progressbg {background-color: #f9efe0;}


#acViewBody a{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}

#acViewBody a:hover{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}
.bold{font-weight:bold;}

.swatch-yellow {
   color: #fff;
    background-color: #17a2b8; padding: 10px;
}
.form-control:disabled, .form-control[readonly]{background:#fff; height:46px; border:1px solid #d5d5d5;}
button.btn.dropdown-toggle.btn-light.bs-placeholder {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
button.btn.dropdown-toggle.btn-light {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
.form-control:disabled, .form-control[readonly]{height:37px;}
.form-control:focus, .form-control:hover{box-shadow:none;}
#divChart {
  margin: auto;
  width: 73%;
   border: 3px solid white;
   border:0px solid #ddd
}
#divChart1 {
  margin: auto;
  width: 70%;
  border: 0px !important;
}
</style>
</head>

<body>
    <!--HEADER STARTS HERE-->
    <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
        <thead>
            <tr>
                <th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img
                        src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt="" width="100" border="0" />
                </th>
                <th style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                    SECRETARIAT OF THE<br>
                    ELECTION COMMISSION OF INDIA<br>
                    Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>
                </th>
            </tr>
        </thead>
    </table>
	
	    <!--HEADER ENDS HERE-->
    <style type="text/css">
        .table-strip {
            border-collapse: collapse;
        }

        .table-strip th,
        .table-strip td {
            text-align: center;
        }

        .table-strip tr:nth-child(odd) {
            background-color: #f5f5f5;
        }
    </style>
    <table style="width:100%; border: 1px solid #000;" border="0" align="center">

        <tr>
            <td style="width:50%;">
                <table style="width:100%">
                    <tbody>

                        <tr>
                            <td><strong>User:</strong>{{$user_data->placename}}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:50%">
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

<div class="container-fluid" id="DivIdToPrint">
<div class="row">
	<div  class="col mt-2">
		

		<table id="list-table"  class="table" border="1" cellpadding="5">
<thead>	
		<tr>
			<td colspan="10" style="text-align:center;font-weight:bold;font-size:16px;">  TRENDS & RESULT 2023 </td> 
		</tr>

		<tr class="sticky-header">
        <th style="background:#f0587e;color:black;"> S.No </th>
		<th style="background:#f0587e;color:black;">State Name</th>
		<th style="background:#f0587e;color:black;">AC Name</th>
        <th style="background:#f0587e;color:black;">AC No.</th>
		<th style="background:#f0587e;color:black;">Leading  Party</th>
		<th style="background:#f0587e;color:black;">Leading Candidate</th>
		<th style="background:#f0587e;color:black;">Trailing Party</th>
		<th style="background:#f0587e;color:black;">Trailing Candidate</th>
		<th style="background:#f0587e;color:black;">Margin</th>
		<th style="background:#f0587e;color:black;">Counting status (Rounds Completed / Total)</th>
		</tr>
 </thead>
		
		<tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$status='';
		
		$scheduled=$data->scheduled_round;
		$completedRound=completeRound($data->st_code,$data->ac_no);
				
		if($data->status==1){
			$status='Result declared';		
		}else if($scheduled==0){
			$status='Rounds Not Scheduled';	
		}else if($scheduled == $completedRound){
			$status='Completed';			
		}else{
			$status = ''.$completedRound.' / '.$scheduled.'';			
		}
	
		?>
        <tr>
        <td>{{$i}}</td> 
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td style="text-align:left;">
		@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
			@if($data->status=='1' && $data->margin!='0')<span style="color:green;">({{'WINNER'}})</span>@endif
		@else{{'NA'}}@endif</td>
		
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:antiquewhite;">@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		</tr>

		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="11">  No record available </td> 
		</tr>
		@endif
       </tbody></table>
	   
	   
	       <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
        <tbody>
            <tr>
                <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
            </tr>
        </tbody>
    </table>
	</div>
</div>
 </div>

 </body>
 </html>




