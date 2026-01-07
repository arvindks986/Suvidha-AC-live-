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
                            @if((!empty($between[0]))) <td align="left"><strong>Date Rage:</strong>{{ date('d-M-Y', strtotime($between[0])).'-'.date('d-M-Y', strtotime($between[1])) }}</td> @endif
						</tr>
						<tr>
                            @if((!empty($election_phase))) <td align="left"><strong>Election Phase: </strong>{{ $election_phase.'-Phase' }}</td> @endif
						</tr>
						<tr>
                            @if((!empty($election_type_id))) <td align="left"><strong>Election Type: </strong>{{ ($election_type_id == '3') ? 'AC-GENRAL' : 'AC-BYE' }}</td> @endif
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

	<table class="table-strip" style="width: 100%;" border="1" align="center">
		<thead>

			<tr>
				<th colspan="4" class="text-center">{!! $heading_title !!} </th>
			</tr>
			<tr>
				<th colspan="3"></th>
				<th colspan="1">Nomination Count</th>
			</tr>
			<tr>
				<th>Serial No</th>
				<th>State Name</th>
				<th>District Name</th>
				{{-- <th>Offline Nomination</th> --}}
				<th>Online Nomination</th>
				{{-- <th>Total Nomination</th> --}}
			</tr>
		</thead>
		<tbody>
			@if(count($results)>0)
			@foreach ($results as $each_data)
			<tr>
				<td>{{$each_data['sno'] }}</td>
				<td>{{$each_data['st_name'] }}</td>
				<td>{{$each_data['dist_name'] }}</td>
				{{-- <td>{{$each_data['offline_nom'] }}</td> --}}
				<td>{{$each_data['online_nom'] }}</td>
				{{-- <td>{{$each_data['total_nom'] }}</td> --}}
			</tr>
			@endforeach
			@else
			<tr>
				<td class="text-center" colspan="4">No Data Found</td>
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