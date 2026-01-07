<!DOCTYPE html>
<html>
<head>
<title>Election Comission Of India:: AC Result Report</title>

</head>


<?php 
ini_set("pcre.backtrack_limit", "5000000000000000");
?>


<body>
<table style="width:100%;  border: 1px solid #d7d7d7;border-collapse: collapse;" border="0" align="center" cellpadding="5">
   <thead>
   	<tr>
   		<th align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/img/logo/eci-logo.png') }}" alt=""  width="85" border="0"/></th>
   		<th align="center" style="font-size: 16px; border-bottom: 1px dotted #d7d7d7;">Election Commission Of India</th>
   		<th align="right" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" width="125" alt="" border="0"/></th>
   	</tr>
   </thead>
</table>
  <table class="table" style="width:100%; background-color: #ffffff; " border="0" align="center" cellpadding="5">
	<tbody>
   	<tr style="font-size: 16px; text-transform: uppercase; background-color: #f7f7f7;">
	 <td colspan="2" align="center" style="padding: 15px;"><strong>AC Result Report</strong></td>
	</tr>
	</tbody>	 
</table>
<table class="table" style="width:100%; background-color: #ffffff;" border="0" align="center">
	<tbody>
	  <tr style="font-size: 10px; text-transform: uppercase; background-color: #f7f7f7;">
		@if(isset($state_name))<td >State:&nbsp;&nbsp;{{$state_name}}@endif</td>
		<td >Result:&nbsp;&nbsp;{{$result_counting_text}}</td>
		<td >Result Type :&nbsp;&nbsp;{{$result_type_text}}</td>
	  </tr>
	</tbody>	
</table>
<br/>
<table id="acViewBody" border="1" class="" width="100%" cellpadding="5" cellspacing="0" style="width:100%;">
<thead style="text-align: center;18px;">
      <tr class="sticky-header">
        <th style="text-align:left;"> S.No </th>
		@if($user=='ECI')
		<th style="text-align:left;">State Name</th>
	   @endif
	   @if($user=='ECI' or $user=='CEO' or $user=='DEO')
		<th style="text-align:left;">AC Name</th>
		<th style="text-align:left;">AC No.</th>
	  @endif
        <th style="text-align:left;">Leading  Party</th>
		<th style="text-align:left;">Leading Candidate</th>
		<th style="text-align:left;background:antiquewhite;">Margin</th>
		@if($result_type!=1)
		<th style="background:burlywood;">Trailing Party</th>
		<th style="background:burlywood;">Trailing Candidate</th>
		@endif	
		<th style="text-align:left;">Result status </th>
		</tr>
	</thead>
    <tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		
		<?php
		$status='';
		if($data->status==1){
		$status='Result Declared';	
		}
		if($data->status==0){
		$status='Result In Progress';	
		}
		?>
        <tr>
        <td>{{$i}}</td> 
		@if($user=='ECI')
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		@endif
		@if($user=='ECI' or $user=='CEO' or $user=='DEO')
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		@endif
		<td style="text-align:left;">
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td style="text-align:left;">@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
		@if($data->status=='1' && $data->margin!='0')<span style="color:green;">({{'WINNER'}})</span>@endif @else{{'NA'}}@endif</td>
		<td style="text-align:left;background:antiquewhite;">@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		@if($result_type!=1)
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		@endif
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		</tr>
	
        
		
		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="7">No Record available</td> 
		</tr>
		@endif
       </tbody></table>
	<h5>&nbsp;Downloaded by: {{ $LoginData['user_data']['name'] }}<span></span></h5>
	<h5>&nbsp;Date: {{ date('d-m-Y H:i:s') }}<span></span></h5>
</body>
</html>
