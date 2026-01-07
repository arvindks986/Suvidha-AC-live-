<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Candidate CA Report</title>

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
							<td><strong>Phase(s):</strong> {{ $phase_name }}</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td style="width:50%">
				
			</td>
		</tr>
		<tr>
			<td style="width:50%;">
				<table style="width:100%">
					<tbody>
						<tr>
							<td><strong>State:</strong> {{ $state_name }}</td>
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
	<table class="table-strip" style="width: 100%; margin-bottom: 10px;" border="1" align="center">
		<thead>
			<tr>
				<th class="text-center">Nomination Report (CA YES/NO) </th>
			</tr>
		</thead>
	</table>
	@if(count($state_list_pdf)>0)
		@foreach($state_list_pdf as $state)
			<?php 
				$count = 0;
			?>
			<table class="table-strip" style="width: 100%; margin-top: 10px;" border="1" align="center">
				<thead>
					<tr>
						<th class="text-center">{{$state->ST_NAME}}</th>
					</tr>
				</thead>
			</table>
			<table class="table-strip" style="width: 100%;" border="1" align="center">
				<thead>
					<tr>
						<th>S.No</th>
						<th>PARTY NAME</th>
						<th>PHASE</th>
						<th>NO OF CANDIDATE WITH CA - YES</th>
						<th>NO OF CANDIDATE WITH CA - NO</th>
						<th>TOTAL</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i=1;
						$total_yes = 0; 
			          	$total_no = 0; 
			          	$total = 0; 
					 ?>
				 	@if(count($results)>0)
						@foreach($results as $item)
						<?php 
								$yes = 0;
								$no = 0; ?>
							@if($item->st_code==$state->ST_CODE)
								<?php
								$yes = get_ca_count($item->st_code,$item->party_id,'1', $phase_list_pdf, $app_status);
								$no = get_ca_count($item->st_code,$item->party_id,'0', $phase_list_pdf, $app_status);
								 ?>
								 @if($yes+$no==0)
								 	<?php continue; ?>
								 @else
								 <?php 
								 	$total_yes = $total_yes+$yes; 
					              	$total_no = $total_no+$no; 
					              	$total = $total+$yes+$no; 
								 ?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$item->PARTYNAME}}</td>
									<td>{{$item->scheduleid}}</td>
									<td>{{$yes}}</td>
									<td>{{$no}}</td>
									<td>{{$yes+$no}}</td>
								</tr>
								@endif
								<?php $count++; $i++; ?>
							@endif
						@endforeach
						<tr>
									<td>Total</td>
									<td></td>
									<td></td>
									<td>{{$total_yes}}</td>
									<td>{{$total_no}}</td>
									<td>{{$total}}</td>
								</tr>
					@else
					<tr>
						<td class="text-center" colspan="5">No Data Found</td>
					</tr>
					@endif			
				</tbody>
			</table>
			@if($count==0)
				<table class="table-strip" style="width: 100%; margin-bottom: 10px;" border="1" align="center">
					<thead>
						<tr>
							<th class="text-center">No Data Found</th>
						</tr>
					</thead>
				</table>
			@endif
		@endforeach
	@endif
	<table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
		<tbody>
			<tr>
				<td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
			</tr>
		</tbody>
	</table>
</body>

</html>