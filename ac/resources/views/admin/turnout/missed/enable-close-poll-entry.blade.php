@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
	.loader {
		position: fixed;
		left: 50%;
		right: 50%;
		border: 16px solid #f3f3f3;
		/* Light grey */
		border-top: 16px solid #3498db;
		/* Blue */
		border-radius: 50%;
		width: 120px;
		height: 120px;
		animation: spin 2s linear infinite;
		z-index: 99999;
	}

	@keyframes spin {
		0% {
			transform: rotate(0deg);
		}

		100% {
			transform: rotate(360deg);
		}
	}

	.disabled {
		cursor: not-allowed;
	}
</style>

<div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">



	<div class="container-fluid">
		<div class="row">
			<div class="col-md-7 pull-left">
				<h4>AC List </h4>
			</div>


		</div>
	</div>
</section>


<section class="dashboard-header section-padding">
	<div class="container-fluid">


		<form id="generate_report_id" class="row" method="get" onsubmit="return false;">



			<div class="form-group col-md-3"> <label>State </label>
				<select name="state" id="state" class="form-control" onchange="filter()">
					<option value="" {{ ($state == '') ? ' selected="selected"' : '' }}>All States</option>
					@foreach($states as $result)
					@if(base64_encode($state) == $result['code'])
					<option value="{{$result['code']}}" selected="selected">{{$result['name']}}</option>
					@else
					<option value="{{$result['code']}}">{{$result['name']}}</option>
					@endif
					@endforeach

				</select>
			</div>

			<?php if (isset($phases) && count($phases) > 0) { ?>
				<div class="form-group col-md-3"> <label>Election Phase</label>

					<select name="phase" id="phase" class="form-control" onchange="filter()">
						@foreach($phases as $result)
						@if($phase==$result->statePHASE_NO)
						<option value="{{$result->statePHASE_NO}}" selected="selected">{{$result->statePHASE_NO}}-Phase</option>
						@else
						<option value="{{$result->statePHASE_NO}}">{{$result->statePHASE_NO}}-Phase</option>
						@endif
						@endforeach

					</select>
				</div>
			<?php } else { ?>
				<input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
			<?php } ?>

			<div class="form-group col-md-3"> <label>AC No & Name </label>
				<select name="ac_no" id="ac_no" class="form-control" onchange="filter()">
					<option value="" {{ ($ac_no == '') ? ' selected="selected"' : '' }}>All AC</option>
					@foreach($acs as $result)
					@if($ac_no == $result['ac_no'])
					<option value="{{$result['ac_no']}}" selected="selected">{{$result['st_code']}}-{{$result['state_name']}} ({{$result['ac_no']}}-{{$result['ac_name']}})</option>
					@else
					<option value="{{$result['ac_no']}}">{{$result['st_code']}}-{{$result['state_name']}} ({{$result['ac_no']}}-{{$result['ac_name']}})</option>
					@endif
					@endforeach

				</select>
			</div>
			<div class="form-group col-md-3"> <label>Round </label>
				<?php $rounds = [
					[
						'id' => 5,
						'name' => '5 PM',
					],
					[
						'id' => 6,
						'name' => 'Close Of Poll',
					],
				];
				?>
				<select name="round" id="round" class="form-control" onchange="filter()">
					<!-- <option value="all">Select Round</option> -->
					@foreach($rounds as $result)
					@if($round == $result['id'])
					<option value="{{$result['id']}}" selected="selected">Round{{$result['id']}}-{{$result['name']}}</option>
					@else
					<option value="{{$result['id']}}">Round{{$result['id']}}-{{$result['name']}}</option>
					@endif
					@endforeach

				</select>
			</div>
		</form>


	</div>
</section>



<div class="container-fluid">
	<!-- Start parent-wrap div -->
	<div class="parent-wrap">
		<!-- Start child-area Div -->
		<div class="child-area">
			<div class="page-contant">
				<div class="random-area">
					<br>
					<?php
					$current_date = date("Y-m-d H:i:s");
					$poll_date = $estimated_time->poll_date;
					$p6 = $poll_date . " " . "23:59:59";
					?>


					<div class="table-responsive">

						<table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
							<thead>
								@if (session('success_mes'))
								<div class="alert alert-success"> {{session('success_mes') }}</div>
								@endif

								@if (session('error_mes'))
								<div class="alert alert-danger"> {{session('error_mes') }}</div>
								@endif
								<tr>
									<th colspan="12" class="text-center">Ac's List</th>
								</tr>
								<tr>
									<th colspan="3"> State </th>
									<th> AC No & Name </th>
									<th> RO Name </th>
									<th> RO Mobile No </th>
									@if($round==5)
									<th> Open For Missed Entry</th>
									@elseif($round==6)
									<th> Open For Close Poll Entry</th>
									@endif
								</tr>
							</thead>
							<tbody>
								@foreach($results as $k=> $result)
								<tr>
									<td colspan="3">
										<span>{!! $result['label'] !!}</span>
									</td>
									<td class="acname{{$k}}">
										{{$result['ac_no'] }}-{{$result['ac_name'] }}
									</td>
									<td>{{$result['name'] }}</td>
									<td>{{$result['Phone_no'] }}</td>

									@php
									if($round<>'all'){
										$roundVal = 'modification_status_round'.$round;
										$roundVal = $result[$roundVal];
										}else{
										$roundVal = 0;
										}
										if($round<>'all'){
											$roundVal1 = 'missed_status_round'.$round;
											$roundVal1 = $result[$roundVal1];
											}else{
											$roundVal1 = 0;
											}
											$time = date('H:i');
											$msg='';
											if($round==5){$msg = 'Option will open after 07:00 pm';}
											elseif($round==6){$msg = 'Option will open after '. $p6;}
											@endphp
											@if($round <>'all')
												@if($round=='5' && $time > '19:00' && $time < '23:30' && $current_date < $p6) @if($roundVal1==0) @if($result['est_turnout_round5']=='0' ) <td><button class="btn btn-primary enbaleMissedAc" data-phase="{{$phase}}" data-round="{{$round}}" data-ac="{{$result['ac_no']}}" data-state="{{$result['st_code']}}" data-id="{{$k}}" data-option="on">Open For Missed Entry </button></td>
													@else
													<td><button class="btn btn-default">Entry Already Done</button></td>
													@endif
													@else
													<td><button class="btn btn-warning enbaleMissedAc" data-phase="{{$phase}}" data-round="{{$round}}" data-ac="{{$result['ac_no']}}" data-state="{{$result['st_code']}}" data-id="{{$k}}" data-option="off">Disable For Missed</button></td>
													@endif
													@elseif($round=='6' && $current_date>$p6)
													@if($roundVal==0)
													<td><button class="btn btn-success enbaleModifiedAc" data-phase="{{$phase}}" data-round="{{$round}}" data-ac="{{$result['ac_no']}}" data-state="{{$result['st_code']}}" data-id="{{$k}}" data-option="on">Open For Modification </button></td>
													@else
													<td><button class="btn btn-warning enbaleModifiedAc" data-phase="{{$phase}}" data-round="{{$round}}" data-ac="{{$result['ac_no']}}" data-state="{{$result['st_code']}}" data-id="{{$k}}" data-option="off">Disable For Modification</button></td>
													@endif
													@else
													<td><button class="btn btn-default disabled">{{$msg}}</button></td>
													@endif
													@else
													<td><button class="btn btn-default disabled">Please select round first</button></td>
													@endif
								</tr>
								@endforeach
							</tbody>
						</table>

					</div><!-- End Of  table responsive -->
				</div><!-- End Of intra-table Div -->


			</div><!-- End Of random-area Div -->

		</div><!-- End OF page-contant Div -->
	</div>
</div><!-- End Of parent-wrap Div -->
</div>
<div class="modal modal-big fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header mb-3">
				<h5 class="modal-title" id="exampleModalLabel">Confirmation For Missed Entry!</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form class="form-horizontal" id="election_form" method="post" action="{{url('acceo/turnout/enable-missed-acs')}}" autocomplete='off'> {{csrf_field()}}
				<input type="hidden" id="ac_no2" name="ac_no">
				<input type="hidden" id="phase_no2" name="phase_no">
				<input type="hidden" id="round_no2" name="round_no">
				<input type="hidden" id="st_code2" name="st_code">
				<input type="hidden" id="data_option" name="data_option">
				<div class="modal-body">
					<div class="mb-3">
						<div style="font-size:16px;">Are you sure you want to <b id="doption"></b> for re-enter voter turnout by ro?</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" id="submit_final_form" class="btn btn-success submit-button">Update</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal modal-big fade" id="changestatus1" tabindex="-1" role="dialog" aria-labelledby="changestatus1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header mb-3">
				<h5 class="modal-title" id="exampleModalLabel">Confirmation For Turnout Modification!</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form class="form-horizontal" id="election_form" method="post" action="{{url('acceo/turnout/enable-modification-acs')}}" autocomplete='off'> {{csrf_field()}}
				<input type="hidden" id="ac_no1" name="ac_no">
				<input type="hidden" id="phase_no1" name="phase_no">
				<input type="hidden" id="round_no1" name="round_no">
				<input type="hidden" id="st_code1" name="st_code">
				<input type="hidden" id="data_option1" name="data_option">
				<div class="modal-body">
					<div class="mb-3">
						<div style="font-size:16px;">Are you sure you want to <b id="doption1"></b> option for close of poll entry by RO?</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" id="submit_final_form" class="btn btn-success submit-button">Update</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(".enbaleMissedAc").click(function() {
		var getid = $(this).attr("data-id");
		var getacname = $(".acname" + getid).text();
		$("#ac_no2").val($(this).attr("data-ac"));
		$("#phase_no2").val($(this).attr("data-phase"));
		$("#round_no2").val($(this).attr("data-round"));
		$("#st_code2").val($(this).attr("data-state"));
		$("#rndtime").text(getacname);
		var data_option = $(this).attr("data-option");
		var data_option = $(this).attr("data-option");
		if (data_option == 'on') {
			$("#doption").html('<b style="color:green;">enable</b>');
		} else {
			$("#doption").html('<b style="color:red;">disable</b>');
		}
		$("#data_option").val(data_option);
		$('#changestatus').modal('show');
	});
	$(".enbaleModifiedAc").click(function() {
		var getid = $(this).attr("data-id");
		var getacname = $(".acname" + getid).text();
		$("#ac_no1").val($(this).attr("data-ac"));
		$("#phase_no1").val($(this).attr("data-phase"));
		$("#round_no1").val($(this).attr("data-round"));
		$("#st_code1").val($(this).attr("data-state"));
		var data_option = $(this).attr("data-option");
		if (data_option == 'on') {
			$("#doption1").html('<b style="color:green;">enable</b>');
		} else {
			$("#doption1").html('<b style="color:red;">disable</b>');
		}

		$("#data_option1").val(data_option);
		$("#rndtime1").text(getacname);
		$('#changestatus1').modal('show');
	});

	function filter() {
		var url = "<?php echo $action ?>";
		var currentPhase = "<?= $phase ?>";
		var state = "<?= $stateEncript ?>";
		var query = '';
		if (jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined') {
			query += '&state=' + jQuery("#state").val();
		}
		if (state != jQuery("#state").val()) {
			query += '&phase=1';
		} else {
			if (jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined') {
				query += '&phase=' + jQuery("#phase").val();
			}
		}
		if (jQuery("#ac_no").val() != '' && jQuery("#ac_no").val() != 'undefined') {
			query += '&ac_no=' + jQuery("#ac_no").val();
		}
		if (jQuery("#round").val() != '' && jQuery("#round").val() != 'undefined') {
			query += '&round=' + jQuery("#round").val();
		}
		window.location.href = url + '?' + query.substring(1);
	}

	setTimeout(function(e) {
		referesh_page();
	}, 300000);

	function referesh_page() {
		location.reload();
	}
</script>
@endsection