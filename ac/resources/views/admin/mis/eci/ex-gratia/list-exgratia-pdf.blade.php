<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Ex-Gratia Detailed Report</title>

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
		<tr><td style="font-size:20px;"><b>Ex Gratia Detailed Report</b></td></tr>
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
					<th>Name of State and District</th>
					<th>Name of Election</th>
					<th>Name of State who has to pay ex-gratia compensation</th>
					<th>Name of polling personnel</th>
					<th>Designation</th>
					<th>Parent Department</th>
					<th>Address</th>
					<th>Contact number</th>
					<th>Detail of Death/Injury</th>
					<th>Reason of Death/Injury</th>
					<th>Place of Death/Injury</th>
					<th>Date of Death/Injury</th>
					<th>Ex-gratia application date</th>
					<th>Ex gratia Payment amount</th>
					<th>Payment Date</th>
					<th>Ex-gratia Status</th>
					<th>Granted/Rejection Date</th>
					<th>Reason for Pending</th>
					<th>Ex Gratia Case Details</th>
				</tr>
			  </thead>
			  <tbody>	
					@foreach($listData as $k=>$v)
					<tr>
					  <td>{{++$k}}</td>
					  <td>{{$v->ST_NAME}} - {{$v->DIST_NAME}}</td>
					  <td>{{$v->election_year}} - {{(!empty($v->election_type))?$elections[$v->election_type]:''}}</td>
					  <td>{{$v->ST_NAME}}</td>
					  <td>{{$v->applicant_name}}</td>
					  <td>{{$v->applicant_designation}}</td>
					  <td>{{$v->applicant_parent_department}}</td>
					  <td>{{$v->applicant_address}}</td>
					  <td>{{$v->contact_no}}</td>
					  <td>{{(!empty($v->injury_details))?$injury[$v->injury_details]:''}}</td>
					  <td>{{(!empty($v->accident_reason))?$reason[$v->accident_reason]:''}}</td>
					  <td>{{ucfirst($v->accident_place)}}</td>
					  <td>@if(!empty($v->accident_date) && $v->accident_date != '0000-00-00'){{date('d-M-Y',strtotime($v->accident_date))}}@endif</td>
					  <td>@if(!empty($v->created_at) && $v->created_at != '0000-00-00'){{date('d-M-Y',strtotime($v->created_at))}}@endif</td>
					  <td>{{$v->payment_amount}}</td>
					  <td>@if(!empty($v->date_of_payment) && $v->date_of_payment != '0000-00-00'){{$v->date_of_payment}}@endif</td>
					  <td>{{ucfirst($v->application_status)}}</td>
					  <td>@if(!empty($v->date_of_action) && $v->date_of_action != '0000-00-00'){{$v->date_of_action}}@endif</td>
					  <td>{{$v->reason_for_pending}}</td>
					  <td>{{$v->case_details}}</td>
					</tr>
					
					@endforeach
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