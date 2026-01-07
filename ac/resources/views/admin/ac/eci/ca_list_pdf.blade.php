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
            			<th>Phase</th>
						<th>NOMINATION ID</th>
						<th>CANDIDATE NAME</th>
						<th>SON/HUSBAND OF</th>
						<th>GENDER</th>
						<th>AGE</th>
						<th>CATEGORY</th>
						<th>STATE NO.</th>
						<th>STATE</th>
            			<th>DISTRICT NO.</th>  
            			<th>DISTRICT</th>  
						<th>AC NO.</th>
						<th>AC</th>
						<th>PARTY</th>
						<th>SYMBOL</th>
						<th>IS CRIMINAL</th>
            			<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=1; ?>
				 	@if(count($results)>0)
						@foreach($results as $item)
						
							@if($item->st_code==$state->ST_CODE)
								<tr>
									<td>{{$i}}</td>
          							<td>Phase-{{$item->state_phase_no}}</td>
									<td>{{$item->nom_id}}</td> 
									<td>{{$item->cand_name}}</td> 
									<td>{{$item->candidate_father_name}}</td> 
									<td>{{$item->cand_gender}}</td> 
									<td>{{$item->cand_age}}</td> 
									<td>{{$item->cand_category}}</td> 
									<td>{{$item->ST_CODE}}</td>
									<td>{{$item->ST_NAME}}</td>
          							<td>{{$item->DIST_NO}}</td>
          							<td>{{$item->DIST_NAME}}</td>
									<td>{{$item->AC_NO}}</td>
									<td>{{$item->AC_NAME}}</td>
									<td>{{$item->PARTYNAME}}</td>
									<td>{{$item->SYMBOL_DES}}</td>
									<td>@if($item->is_criminal==1) <span class="text-danger">Yes</span> @else <span class="text-success">No</span> @endif</td>
									<td>{{$item->application_status}}</td>
								</tr>
								<?php $count++; $i++; ?>
							@endif
						@endforeach
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