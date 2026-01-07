<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>{!! $heading_title !!}</title>

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
							<td><strong>User:</strong> {{$user_data->officername}}--{{$user_data->placename}}</td>
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

	<table style="width:100%; border: 1px solid #000;" border="0" align="center">
		<tr>
			<td style="width:100%;">
				<table style="width:100%">
					<tbody>
						<tr>
							@if((!empty($between[0]))) <td align="right"><strong>Date
									Rage:</strong>{{ date('d-M-Y', strtotime($between[0])).'-'.date('d-M-Y', strtotime($between[1])) }}
							</td> @endif
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>

	<table class="table-strip" style="width: 100%;" border="1" align="center">
		<thead>

			<tr>
				<th colspan="8" class="text-center">{!! $heading_title !!} </th>
			</tr>
			<tr>
				<th>S.No</th>
				<th>State Name</th>
				<th>District Name</th>
				<th>District Code of Bihar</th>
				<th>Transaction Code</th>
				<th>Treasury Account No</th>
				<th>Merchant Code</th>
				@if(isset($results[0]['is_verified']))<th>Is Verified</th>@endif
			</tr>
		</thead>
		<tbody>
			@if(count($results)>0)
			@foreach ($results as $item)
			@php
			$status = '';
			if(isset($item['is_verified'])){
				$status = ($item['is_verified']=='1') ? 'YES' : 'NO';
			}	
			@endphp
			<tr>
				<td>{{$item['sno']}}</td>
				<td>{{$item['st_name']}}</td>
				<td>{{$item['dist_code_nomination'].'-'.$item['district_treasury_name']}}</td>
				<td>{{$item['dist_code_bihar']}}</td>
				<td>{{$item['trs_code']}}</td>
				<td>{{$item['hd_ac1']}}</td>
				<td>{{$item['merchant_code']}}</td>
				@if(isset($item['is_verified']))<td>{{ $status }}</td>@endif
			</tr>
			@endforeach
			@else
			<tr>
				<td class="text-center" colspan="6">No Data Found</td>
			</tr>
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