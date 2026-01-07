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
	<table style="width:100%; border: 1px solid #000;" border="0" align="center">
        <tr>
            <td style="width:100%;">
                <table style="width:100%">
                    <tbody>
                        <tr>
                            @if((!empty($between[0]))) <td align="right"><strong>Date Rage:</strong>{{ date('d-M-Y', strtotime($between[0])).'-'.date('d-M-Y', strtotime($between[1])) }}</td> @endif
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

	<table class="table-strip" style="width: 100%;" border="1" align="center">
		<thead>

			<tr>
				<th colspan="9" class="text-center">{!! $heading_title !!} </th>
			</tr>
			<tr>
				<th colspan="3">Total Appointment</th>
                <th colspan="3">Appointment Given</th>
                <th colspan="3">Appointment Pending</th>
            </tr>
            <tr>
				<th colspan="3">{{$request_appointment_table['total_appointment']}}</th>
                <th colspan="3">{{$request_appointment_table['appointment_given']}}</th>
                <th colspan="3">{{$request_appointment_table['appointment_pending']}}</th>
			</tr>
			<tr>
				<th colspan="9">Application Details</th>
            </tr>
            <tr>
                <th>Candidate</th>
                <th>Candidate Name</th>
                <th>Father Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Address</th>
                <th>Nomination Details</th>
                <th>Preferable Appointment Date</th>
                <th>Appointment Status</th>
			</tr>
		</thead>
		<tbody>
			@if(count($results)>0)
			@foreach ($results as $each_data)
			<tr>
				<td><img src="{{ !empty($each_data['image']) ? public_path($each_data['image']) : public_path('img/vendor/avtar.jpg') }}" alt=""  width="100" height="100" border="0"/></td>
				<td>{{  $each_data['name'].'('.$each_data['hname'].')'  }}</td>
				<td>{{ $each_data['father_name'].'('.$each_data['father_hname'].')' }}</td>
				<td>{{$each_data['gender'] }}</td>
				<td>{{$each_data['age'] }}</td>
                <td>{{$each_data['address'] }}</td>
                <td>
                    <h5>Total Nominations:- {{ count($each_data['nomination_details']) }}</h5>
					@if(count($each_data['nomination_details'])>0)
					<br>
                    @foreach ($each_data['nomination_details'] as $item)
						<?php
							if($item->recognized_party == '1'){
								$party=getpartybyid($item->party_id)->PARTYNAME; 
							}elseif($item->recognized_party == '2'){
								$party=getpartybyid($item->party_id2)->PARTYNAME; 
							}else{
								$party=getpartybyid($item->party_id)->PARTYNAME; 
							}
						?>
                        <h5 class="py-2 d-flex align-items-center justify-content-between">{{ $item->nomination_no.' - '.$party }}</h5><br>
                    @endforeach
                    @endif
                </td>
                <td>
                    @if(count($each_data['appointment_details'])>0)
                        @foreach ($each_data['appointment_details'] as $item)
                        <h5 class="my-3 dyTm"><i class="fa fa-calendar mr-1" aria-hidden="true"></i>{{ date('d-m-Y', strtotime($item->appointment_date)) }}<span class="ml-2 mr-1"><i class="fa fa-clock-o" aria-hidden="true"></i></span>{{ date('h:i A', strtotime($item->appointment_time)) }}</h5>
                        @endforeach
                    @endif
                </td>
                <td>{{ $each_data['appointment_details'][0]->is_ro_acccept=='1' ? 'Appointment Given' : 'Appointment Pending' }}</td>
			</tr>
			@endforeach
			@else
			<tr>
				<td class="text-center" colspan="9">No Data Found</td>
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