<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Ex Gratia Count Report</title>

</head>

<body>
	<!--HEADER STARTS HERE-->
	<table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
		<thead>
			<tr>
                <th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;">
                    <img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt=""  width="100" border="0"/>
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

		body, p, td, div { font-family: freesans; }

		.table-strip tr:nth-child(odd) {
			background-color: #f5f5f5;
		}
		@page { sheet-size: A3-L; }
		@page bigger { sheet-size: 420mm 370mm; }
		@page toc { sheet-size: A4; }
	</style>
	<table style="width:100%; border: 1px solid #000;" border="0" align="center">
		<tr><td style="font-size:20px;"><b>Ex Gratia Count Report</b></td></tr>
		<tr>
			<td style="width:50%;">
				<table style="width:100%">
					<tbody>

						<tr>
							<td><strong>User:</strong> {{$user_data->officername}}-{{$user_data->placename}}</td>
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

	<table style="width:100%; border: 1px solid #000;">
			   <thead>
				<tr>
					<th>Sl.No.</th>
					<th>Name of State</th>
					<th>Ex Gratia Cases</th>
					<th>Last Updated Date</th>
					<th>Total Cases</th>
					<th>Total Pending Cases</th>
					<th>Pending Cases Due for payment to the next kin in unfortunate event of death</th>
					<th>Pending Cases Due for payment to the next kin in unfortunate event of death due to any violent acts	</th>
					<th>Pending Cases Due for payment in case permanent disability</th>
				</tr>
			  </thead>
			  <tbody>	
					@php $total_cnt=0;$total_pending=0;$total_death=0;$total_violent=0;$total_perm_dis=0;  @endphp
					@foreach($listData as $k=>$v)
					@php
						$total_cnt = $total_cnt + $v->cnt;
						$total_pending = $total_pending + $v->total_pending;
						$total_death = $total_death + $v->total_death;
						$total_violent = $total_violent + $v->total_violent_act;
						$total_perm_dis = $total_perm_dis + $v->total_permanent_disability;
					@endphp
					<tr>
					  <td>{{++$k}}</td>
					  <td>{{$v->state_name}}</td>
					  <td>@if(!empty($v->nocases) && $total_pending==0 && $v->nocases==1)No cases @else  @endif</td>
					  <td>@if(!empty($v->updated_at) && $v->updated_at<>'0000-00-00'){{date('d-M-Y',strtotime($v->updated_at))}}@endif</td>
					  <td align="center">{{$v->cnt}}</td>
					  <td align="center">{{$v->total_pending}}</td>
					  <td align="center">{{$v->total_death}}</td>
					  <td align="center">{{$v->total_violent_act}}</td>
					  <td align="center">{{$v->total_permanent_disability}}</td>
					</tr>
					
					@endforeach
					@if(count($listData)>0 && ($user_data->role_id=='7' || $user_data->role_id=='50'))
						<tfoot>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><b>Grand Total:</b></td>
							<td align="center"><b>{{$total_cnt}}</b></td>
							<td align="center"><b>{{$total_pending}}</b></td>
							<td align="center"><b>{{$total_death}}</b></td>
							<td align="center"><b>{{$total_violent}}</b></td>
							<td align="center"><b>{{$total_perm_dis}}</b></td>
						</tr>
						</tfoot>
					@endif
			  </tbody>
		</table>
	<table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
		<tbody>
			<tr>
				<td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
			</tr>
		</tbody>
	</table>
</body>

</html>