<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Ex Gratia List</title>

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
	</style>
	<table style="width:100%; border: 1px solid #000;" border="0" align="center">

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

	<table class="table table-bordered table-strip" style="width: 100%;" style="width:100%; border: 1px solid #000;" border="0">
			  <thead>
				<tr style="width:100%; border: 1px solid #000;">
					<th>Sl.No.</th>
					<th>Name of State and District</th>
					<th>Name of Election</th>
					<th>Name of State who has to pay ex-gratia compensation</th>
					<th>Name of polling personnel</th>
					<th>Deatils Of Death/Injury For Pending Cases Due for payment</th>
					<th>Reason Of Death/Injury For Pending Cases Due for payment</th>
				</tr>
			  </thead>
			  <tbody>	
					@foreach($listData as $k=>$v)
					<tr>
					  <td>{{++$k}}</td>
					  <td>{{$v->ST_NAME}} - {{$v->DIST_NAME}}</td>
					  <td>{{(!empty($v->election_type))?$elections[$v->election_type]:''}}</td>
					  <td>{{$v->ST_NAME}}</td>
					  <td>{{$v->applicant_name}}</td>
					  <td>{{(!empty($v->injury_details))?$injury[$v->injury_details]:''}}</td>
					  <td>{{(!empty($v->accident_reason))?$reason[$v->accident_reason]:''}}</td>
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