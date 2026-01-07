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
				<th colspan="12" class="text-center">{!! $heading_title !!} </th>
			</tr>
			<tr>
				<th colspan="4">Total Physical Verification</th>
                <th colspan="4">Physical Verification Done</th>
                <th colspan="4">Physical Verification Pending</th>
            </tr>
            <tr>
				<th colspan="4">{{$application_count['total_application']}}</th>
                <th colspan="4">{{$application_count['application_done']}}</th>
                <th colspan="4">{{$application_count['application_pending']}}</th>
			</tr>
			<tr>
				<th colspan="12">Application Details</th>
            </tr>
            <tr>
                <th>SNo.</th>
                <th>Candidate</th>
                <th>Candidate Name</th>
                <th>Father Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>State Name</th>
				<th>District Name</th>
				<th>AC Name</th>
                <th>Nomination Details</th>
                <th>Payment Status</th>
                <th>Application Status</th>
			</tr>
		</thead>
		<tbody>
            @if(count($results)>0)
            @php
                $srno = 1;
            @endphp
			@foreach ($results as $each_data)
			<?php
				$st= getstatebystatecode($each_data['st_code']);
				$ac= getacbyacno($each_data['st_code'],$each_data['ac_no']);
			?>
			<tr>
                <td>{{ $srno++ }}</td>
				<td><img src="{{ !empty($each_data['image']) ? public_path($each_data['image']) : public_path('img/vendor/avtar.jpg') }}" alt=""  width="100" height="100" border="0"/></td>
				<td>{{  $each_data['name'].'('.$each_data['hname'].')'  }}</td>
				<td>{{ $each_data['father_name'].'('.$each_data['father_hname'].')' }}</td>
				<td>{{$each_data['gender'] }}</td>
				<td>{{$each_data['age'] }}</td>
                <td>{{ !empty($st) ? $st->ST_NAME : '' }}</td>
				<td>{{ !empty($each_data['DIST_NO_HDQTR']) ? $each_data['DIST_NO_HDQTR'].'-'.getdistrictbydistrictno($each_data['st_code'],$each_data['DIST_NO_HDQTR'])->DIST_NAME : '' }}</td>
				<td>{{ !empty($ac) ? $ac->AC_NO.'-'.$ac->AC_NAME : '' }}</td>
                <td>
					<?php 
					if($each_data['recognized_party'] == '1'){
						$party=getpartybyid($each_data['party_id'])->PARTYNAME; 
					}elseif($each_data['recognized_party'] == '2'){
						$party=getpartybyid($each_data['party_id2'])->PARTYNAME; 
					}else{
						$party=getpartybyid($each_data['party_id'])->PARTYNAME; 
					} 
					?>
                    {{ $each_data['nomination_no'].' - '.$party }}
                </td>
                <?php $payment_details = app(App\Http\Controllers\Admin\CandNomination\ApplicantController::class)->getpaymentStatus($each_data['id'], $each_data['candidate_id']);
				if(count($payment_details['payment_detail']) > 0){
					$status_txt   = 'Payment Done';
				}else{
					$status_txt   = 'Payment Pending';
                }
                
                $btn_status = \app(App\Http\Controllers\Admin\CandNomination\ApplicantController::class)->is_nomination_exist($each_data['nomination_no']);
                if(!$btn_status){
                    $status_txt_data = 'Physical Verification Pending';
                }else {
                    $status_txt_data = 'Physical Verification Completed';
                }
                ?>
                <td>{{ $status_txt }}</td>
                <td>{{ $status_txt_data }}</td>
			</tr>
			@endforeach
			@else
			<tr>
				<td class="text-center" colspan="10">No Data Found</td>
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