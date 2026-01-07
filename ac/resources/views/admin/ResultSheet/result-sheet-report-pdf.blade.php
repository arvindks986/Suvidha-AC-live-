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
<table class="table" border="0">
  <tr>
    <td colspan="2" style="text-align:right; padding: 1rem 0;">Date : {{date('d/m/Y H:i:s A')}} </td>
  </tr>
</table>

<div id="DivIdToPrint">

@foreach($state_list as $key=>$raw)

	@if($key == 0 || $key == 1)
	<div style="text-align:center;font-weight:bold;font-size:21px;">{{$elec_type[$key]}} ELECTION TO VIDHAN SABHA TRENDS & RESULT {{$elec_name[$key]}}</div>

	@endif
		<div>&nbsp;</div>
		<table class="table" cellspacing="0" cellpadding="5">  
		 <tbody><tr> <td colspan="4" style="text-align:center;" align="center">
    	</table>
		<table class="table" style="border: solid 1px black;font-weight:lighter;" cellspacing="0" cellpadding="5" border="1">
		 <tbody>
		  <tr> 
		  <td colspan="4" align="center" style="font-weight:bold; background-color: #ffc0cd;"> 
			 <div>{{$state_name[$key]}}</div>
              Result Status <div id="divStatusHR" style="font-size: 10px;"></div>
		   </td> 
		  </tr>  
			<tr align="center">
			  <td colspan="4" align="center" style="background-color: #ffc0cd;">
			    <div style="font-size: 10px; font-weight: bold" id="divStatus"> Status Known For {{$Constituencies_out_of_count[$key]}} out of {{$Constituencies_count[$key]}} Constituencies</div>
			  </td>
			</tr>
			<tr align="center;"> 
			  <th align="left" style="background-color: #fce0e6; color: #d53858;" >Party</th> 
			  <th style="background-color: #54c752; color: #000;">Won</th>
			  <th style="background-color: #ecb241; color: #000;">Leading</th>
			  <th style="background-color: #3accae; color: #000;">Total</th>
			</tr>
			@foreach($resultPartywisedata[$key] as $dat)
				{!! $dat !!}
			@endforeach
			
			  
        </tbody>
	</table>
	<br><br><br>
	@if(($key == 0) || ($key == 3) || ($key == 6) || ($key == 9) || ($key == 12))
	 <div style="page-break-after:always"></div>
 @endif
	@endforeach
	<div style="page-break-after:always"></div>
	<?php //if(count($state_list) == 1) { ?>
	@foreach($state_list as $key=>$raw)
   <div>
    <h2 style="text-align: center;">{{$state_name[$key]}} Constituency Wise Result Status</h2>   
   <table id="list-table"  class="table" border="1" cellpadding="5">
		<thead>	
				<tr style="background-color: #d7d7d7;">
					<th style=" color:#000;">S.No</th>
					<th style=" color:#000;">State Name</th>
					<th style=" color:#000;">AC Name</th>
					<th style=" color:#000;">AC No.</th>
					<th style=" color:#000;">Leading/Won  Party</th>
					<th style=" color:#000;">Leading/Won Candidate</th>
					<th style=" color:#000;">Margin</th>
					<th style=" color:#000;">Trailing Party</th>
					<th style=" color:#000;">Trailing Candidate</th>
					<th style=" color:#000;">Result status </th>
				</tr>
		 </thead>
		
		
		<tbody style="text-align: center;">
		@if(count($result[$key]) > 0 )
		@php $i=1 @endphp
		@foreach($result[$key] as  $data)
		<?php
		$status='';
		if(@$data->status==1){
		$status='Result Declared';
		$class = 'declaredbg';
		}
		if(@$data->status=='0'){
		$status='Result In Progress';	
		$class = 'progressbg';
		}
	
		?>
        <tr class="{{$class}}">
        <td>{{$i}}</td> 
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td style="text-align:left;">
		@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
			@if($data->status=='1' && $data->margin!='0')<span>({{'WINNER'}})</span>@endif
		@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		</tr>

		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="11">  No record available </td> 
		</tr>
		@endif
       </tbody>
	 </table>
	 <br><br><br>
   </div>
   
   
  
   @endforeach
	
 </div>
 </body>
 </html>