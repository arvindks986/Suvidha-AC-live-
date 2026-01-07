@extends('admin.layouts.ac.theme')
@section('title', 'List of candidate')
@section('bradcome', 'List of candidate')
@section('content')

<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom-dark.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/dark_custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/prenom.css')}}" />
<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
<?php   $url = URL::to("/");  ?>

<style>
	.qrBar #preview {
		position: absolute;
		max-width: 100%;
		max-height: 100%;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		margin: auto;
		display: block;
	}
</style>

<main class="pt-3 px-2">
	@php
		$getallsche = getallschedule();
	@endphp
	<div class="container-fluid">
		<div class="card card-shadow">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col-md-6 text-left">
						<a href="{{ Common::generate_url("dashboard") }}" class="btn btn-primary" id="">Home</a>
					</div>
					<div class="col-md-6 text-right">
						<a href="{{ $print_pdf_url }}" class="btn btn-primary" id="">Print Pdf</a>
						<a class="btn btn-primary" href="{{ redirect()->getUrlGenerator()->previous() }}">Back</a>
					</div>
				</div>
			</div>
			<div id="all_body" class="card-body vlight-pink p-0">
				<div class="row m-2">
					<div class="col-md-6">
						<select class="form-control" name="election_type_id" id="election_type_id">
							<option value="" {{ ($election_type_id=='') ? 'selected' : '' }}>All Election Type</option>
							<option value="3" {{ ($election_type_id=='3') ? 'selected' : '' }}>AC-GENERAL</option>
							<option value="4" {{ ($election_type_id=='4') ? 'selected' : '' }}>AC-BYE</option>
						</select>
					</div>
					
					<div class="col-md-6">
						<select class="form-control" name="election_phase" id="election_phase">
							<option value="" {{ ($election_phase=='') ? 'selected' : '' }}>All Phase</option>
							@foreach ($getallsche as $each_data)
								<option value="{{ $each_data->SCHEDULEID }}" {{ ($election_phase==$each_data->SCHEDULEID) ? 'selected' : '' }}>{{ $each_data->SCHEDULEID.'-'.'Phase' }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-9">
						@include('admin/common/form-filter')
					</div>
					<div class="col-md-3 mt-4">
						<div class="srchBox">
							<div id="input_search_box" class="input-group">
								<input type="text" id="qrcode" class="form-control"
									placeholder="Search By Nomination No./Name" value="">
								<div class="input-group-append">
									<button class="btn dark-purple-btn" type="button"><i class="fa fa-search"
											aria-hidden="true"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="filter_row" class="row m-1">
					<div class="col">
						<fieldset class="mb-2">
							<legend>Physical verification<sup>*</sup></legend>
							<div class="row">
								<div class="col">
									<label class="radioBtn">All applications
										<input type="radio" class="filter_dropdown" name="filter" value="all"
											{{ $status_filter=='all' ? 'checked' : '' }} checked>
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="col">
									<label class="radioBtn">Pending physical verification
										<input type="radio" class="filter_dropdown" name="filter" value="pending"
											{{ $status_filter=='pending' ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="col">
									<label class="radioBtn">Physical verification done
										<input type="radio" class="filter_dropdown" name="filter" value="cleared"
											{{ $status_filter=='cleared' ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
						</fieldset>
					</div>
				</div>

				<table class="table-strip text-center" style="width: 90%; margin-bottom: 10px;" border="1"
					align="center">
					@if(is_null(request()->get('appointment')))
					<tr>
						<th colspan="3">Total Physical Verification</th>
						<th colspan="3">Physical Verification Done</th>
						<th colspan="4">Physical Verification Pending</th>
					</tr>
					<tr>
						<th colspan="3">{{$application_count['total_application']}}</th>
						<th colspan="3">{{$application_count['application_done']}}</th>
						<th colspan="4">{{$application_count['application_pending']}}</th>
					</tr>
					@else
					<tr>
						<th colspan="3">Total Appointment</th>
						<th colspan="3">Appointment Given</th>
						<th colspan="4">Appointment Pending</th>
					</tr>
					<tr>
						<th colspan="3">{{$application_appointment_count['total_appointment']}}</th>
						<th colspan="3">{{$application_appointment_count['appointment_done']}}</th>
						<th colspan="4">{{$application_appointment_count['appointment_pending']}}</th>
					</tr>
					@endif
				</table>
			</div>
		</div>

	</div><!-- End Of container-fluid Div -->
</main>

<main class="pt-3 px-2">
	<div class="container-fluid">
		<div class="physc-wrap">
			<?php $i=1; ?>
			@foreach($results as $result)
			<?php 
				if($result['recognized_party'] == '1'){
					$party=getpartybyid($result['party_id']); 
				}elseif($result['recognized_party'] == '2'){
					$party=getpartybyid($result['party_id2']); 
				}else{
					$party=getpartybyid($result['party_id']); 
				}
			?>
			<div class="d-flex tr-bg shadow-sm mb-3 myTable">
				<div class="py-3">
					<figure class="img-id">
						<figcaption>{{ $i }}</figcaption>
						@if(!empty($result['image']))
						<img src="{{$url.'/'.$result['image']}}" class="prfl-pic img-thumbnail" alt="">
						@else
						<img src="{{ asset('theme/img/nominator-icon.png') }}" class="prfl-pic img-thumbnail" alt="">
						@endif
					</figure>
				</div>
				<div class="py-4 px-3 w-50 phys-bdy">
					<div class="full-name">
						<?php 
		  if($result['gender'] =='male'){
			  $gen = '(M)';
			  $hgen = '(पु)';
		  }elseif ($result['gender'] =='female') {
				$gen = '(F)';
			  	$hgen = '(म)';
		  }else{
				$gen = '(O)';
			  	$hgen = 'अ';
		  } ?>
						<div class="pull-left">
							<h5>{{ !empty($result['hname']) ? $result['hname'] : '' }} <span>{{ $hgen }}</span></h5>
							<h5>{{ $result['name'] }} {{ $gen }}</h5>
						</div>

						<div class="pull-right">
							<h5><i class="fa fa-mobile fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;+91-{{ !empty($result['mobile']) ? $result['mobile'] : '' }}</h5>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="d-inline-flex align-items-center mt-1">
						<figure class="mb-0"><img src="{{ asset('theme/img/vendor/icon-001.png') }}"></figure>
						<div>
							<h6 class="mb-0">{{ $result['father_name'] }}</h6>
							<h6>{{ !empty($result['father_hname']) ? $result['father_hname'] : '' }}</h6>
						</div>
					</div>

					<div class="d-inline-flex align-items-center mt-1">
						<figure class="mb-0"><img src="{{ asset('theme/img/vendor/icon-003.png') }}"></figure>
						<div>
							<h6>@if(isset($result['nomination_no'])){{$result['nomination_no']}}@endif</h6>
						</div>
					</div>

					<div class="d-inline-flex align-items-center mt-1">
						<figure class="mb-0"><img src="{{ asset('theme/img/vendor/district-icon.png') }}"></figure>
						<div>
							<h6>{{ !empty($result['DIST_NO_HDQTR']) ? $result['DIST_NO_HDQTR'].'-'.getdistrictbydistrictno($result['st_code'],$result['DIST_NO_HDQTR'])->DIST_NAME : '' }}</h6>
						</div>
					</div>
					

					<div class="d-inline-flex align-items-center mt-1">
						<figure class="mb-0"><img src="{{ asset('theme/img/vendor/icon-004.png') }}"></figure>
						<div>
							<h6>@if(isset($party)){{$party->PARTYNAME}} @endif</h6>
						</div>
					</div>

					
					<div class="d-inline-flex align-items-center mt-1">
						<figure class="mb-0"><img src="{{ asset('theme/img/vendor/state-icon.png') }}"></figure>
						<div>
							<h6>{{ !empty($result['st_code']) ? getstatebystatecode($result['st_code'])->ST_NAME : '' }}</h6>
						</div>
					</div>
					
					<div class="d-inline-flex align-items-center mt-1">
						<figure class="mb-0"><img src="{{ asset('theme/img/vendor/ac-name-icon.png') }}"></figure>
						<div>
							<h6>{{ !empty($result['ac_no']) ? $result['ac_no'].'-'.getacbyacno($result['st_code'], $result['ac_no'])->AC_NAME : '' }}</h6>
						</div>
					</div>
				</div>
				<?php 
				if($result['finalize_after_payment'] == 1){
					$status_color = 'cleared';
					$status_txt   = 'Finalized';
				}else{
					$status_color = 'defected';
					$status_txt   = 'Not Finalized';
				}
			?>

				<div class="bg-light p-2 custom-border-right w-15">
					<strong>Form Status</strong>
					<h5>{{ $status_txt }}</h5>
					<div class="phyStatus">
						<span class="{{ $status_color }}"></span>
					</div>
					@if(!empty($result['finalize_after_payment_date']))
					<strong>Status Date</strong>
					<h5>{{ !empty($result['finalize_after_payment_date']) ? date('d-m-Y', strtotime($result['finalize_after_payment_date'])) : '' }}</h5>
					@endif
				</div>
				<?php $payment_details = app(App\Http\Controllers\Admin\CandNomination\ApplicantController::class)->getpaymentStatus($result['id'], $result['candidate_id']);
				
				if(count($payment_details['payment_detail']) > 0){
					$status_color = 'cleared';
					$status_txt   = 'Payment Done';
				}else{
					$status_color = 'defected';
					$status_txt   = 'Payment Pending';
				}
			?>

				<div class="bg-custom-deposit w-25 p-2">
					<strong>Security Deposit</strong>
					<h5>{{ $status_txt }}</h5>
					<div class="phyStatus">
						<span class="{{ $status_color }}"></span>
					</div>
					<strong>Payment Mode</strong>
					<div class="d-flex align-items-center">
						@if($payment_details['payment_type'] == 'Online')
						<h5>Online/</h5>&nbsp;
						@if(count($payment_details['payment_detail']) > 0)
						<h5>{{ date('d-m-Y',strtotime($payment_details['payment_detail'][0]->bank_transtimestamp)) }}</h5>
						/&nbsp;
						@else
						<h5>-</h5>/&nbsp;
						@endif
						@if($status_color=='defected')
						<h5>Not Avilable</h5>&nbsp;
						@else
						<h5><a href="#" class="payment_recipt_view" nom_id="{{$result['nomination_no']}}">View</a></h5>
						&nbsp;
						@endif
						@elseif($payment_details['payment_type'] == 'Challan')
						<h5>Challan/</h5>&nbsp;
						@if(count($payment_details['payment_detail']) > 0)
						<h5>{{ date('d-m-Y',strtotime($payment_details['payment_detail'][0]->challan_date)) }}</h5>
						/&nbsp;
						@else
						<h5>-</h5>/&nbsp;
						@endif
						@if($status_color=='defected')
						<h5>Not Avilable</h5>&nbsp;
						@else
						<h5><a href="#" class="challan_payment_recipt_view"
								nom_id="{{$result['nomination_no']}}">View</a></h5> &nbsp;
						@endif
						@elseif($payment_details['payment_type'] == 'Pay By Cash Paid')
						<h5>Paid by cash/</h5>&nbsp;
						@if(count($payment_details['payment_detail']) > 0 &&
						$payment_details['payment_detail'][0]->pay_by_cash_paid == '1')
						<h5>{{ date('d-m-Y',strtotime($payment_details['payment_detail'][0]->date_time_of_pbc)) }}</h5>
						&nbsp;
						@else
						<h5>-</h5>&nbsp;
						@endif
						@if($status_color=='defected')
						<h5>Not Avilable</h5>&nbsp;&nbsp;
						@endif
						@elseif($payment_details['payment_opt_ro'] == 'Pay By Cash')
						<h5>Pay by cash</h5>
						@endif
					</div>
				</div>
				@php
				$btn_status = \app(App\Http\Controllers\Admin\CandNomination\ApplicantController::class)->is_nomination_exist($result['nomination_no']);
				@endphp
				<div class="text-center w-15 p-2">
					<div class="font-big">Status</div>
					@if(!$btn_status)
					<div class="my-2 phy-pending"><a href="#"><i class="fa fa-hourglass-end" aria-hidden="true"></i></a>
					</div>
					@else
					<div class="my-2 phy-success"><a href="#"><i class="fa fa-check" aria-hidden="true"></i></a></div>
					@endif
					<div class="my-2">
						@if(!$btn_status)
						<h5>Physical Verification Pending</h5>
						@else
						<h5>Physical Verification Completed</h5>
						@endif
					</div>
					@if(Auth::user()->designation == 'ECI')
					<div><a href="{{ url('/eci/detail/'.encrypt_string($result['id'])) }}" class="btn font-big">View All Details</a></div>
					@endif
					<div>

						{{-- <div><a href="{{ url('/roac/detail/'.encrypt_string($result['id'])) }}" class="btn
						font-big">View All Details</a></div> --}}
				</div>
			</div>
		</div>
		<?php $i++; ?>
		@endforeach
	</div>
	</div><!-- End Of container-fluid Div -->
</main>

<!-- Modal confirm schedule -->
<div class="modal fade modal-confirm" id="payment_recipt">
	<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
		<div class="modal-content">
			<div class="pop-header pt-3 pb-1">
				<div class="animte-tick"><span>&#10003;</span></div>
				<h5 class="modal-title cnd_name"></h5>
				<div class="header-caption">
					<p>Payment Receipt</p>
				</div>
			</div>
			<div class="modal-body">
				<ul style="list-style: none;">
					<li><label>AC No. &amp; Name:</label><span id="ac_name_no"></span></li>
					<li><label>Payment Status:</label> <span>Done</span></li>
					<!-- <li class="is_bihar"><label>Receipt:</label><span><a href="#" class="online_recipt" target="_blank">View</a></span></li>
					<li class="is_guj"><label>Bank Code:</label><span id="bank_code"></span></li> -->
					<li class="is_guj"><label>bank Reference Number:</label><span id="bank_reff_no"></span></li>
					<li class="is_guj"><label>Amount:</label><span id="txn_amount"></span></li>
					<li><label>Payment Date:</label><span id="payment_date"></span></li>
					<li><label>Payment Time:</label><span id="payment_time"></span></li>
				</ul>
				<p class="note-warn"><strong><i>Instruction <sup>*</sup></i></strong>Please carry all original and
					necessary documents for verification</p>
			</div>

			<!-- Modal footer -->
			<div class="confirm-footer">
				<button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">Ok</button>
				<!--<button type="button" class="btn dark-purple-btn">Print</button>-->
			</div>

		</div>
	</div>
</div><!-- End Of confirm Modal popup Div -->

<!-- Modal confirm schedule -->
<div class="modal fade modal-confirm" id="challan_payment_recipt">
	<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
		<div class="modal-content">
			<div class="pop-header pt-3 pb-1">
				<div class="animte-tick"><span>&#10003;</span></div>
				<h5 class="modal-title cnd_name"></h5>
				<div class="header-caption">
					<p>Challan Details</p>
				</div>
			</div>
			<div class="modal-body">
				<ul style="list-style: none;">
					<li><label>AC No. &amp; Name:</label><span class="ac_name_no"></span></li>
					<li><label>Challan No:</label><span class="challan_no"></span></li>
					<li><label>Challan Receipt:</label><span><a href="#" class="challan_recipt"
								target="_blank">View</a></span></li>
					<li><label>Challan Date:</label><span class="challan_date"></span></li>
				</ul>
				<p class="note-warn"><strong><i>Instruction <sup>*</sup></i></strong>Please carry all original and
					necessary documents for verification</p>
			</div>

			<!-- Modal footer -->
			<div class="confirm-footer">
				<button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">Ok</button>
				<!--<button type="button" class="btn dark-purple-btn">Print</button>-->
			</div>

		</div>
	</div>
</div><!-- End Of confirm Modal popup Div -->
@endsection
@section('script')
<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>
<script type="text/javascript" src="{{ asset('theme/js/instascan.min.js') }}"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){

		$('.date').datetimepicker({
			format: 'DD-MM-YYYY',
			maxDate: moment().format('MM-DD-YYYY')
		});

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$('#filter_btn').click(function(e) {
			// $('#input_search_box').fadeOut(500);
			// $('#filter_row').fadeIn(500);
			// $('.qrCode').fadeOut(500);
			// $('#filter_btn').fadeOut(500);
			// $('#filter_row_hide').show();
			let new_url = addParam('status', 'all');
			window.location.href = new_url;
		});

		$('#filter_row_hide').click(function(e) {
			// $('#filter_row').fadeOut(500);
			// $('#filter_btn').fadeIn(500);
			// $('#filter_row_hide').hide();
		});

		var nomination_no = '';

		//By Searh Text
		jQuery("#qrcode").on("keyup", function() {
		var value = $(this).val().toUpperCase();
		// if(value != ''){
		// 	$('.physc-wrap').show();
		// }else{
		// 	$('.physc-wrap').hide();
		// }
		jQuery(".myTable").filter(function() {
			// jQuery(this).toggle();
			const display = jQuery(this).text().toUpperCase().indexOf(value) > -1
			if ( display === true ) {
				// $('html, body').animate({
				// 		scrollTop: $(".physc-wrap").offset().top
				// 	}, 2000);
				$(this).addClass('d-flex');
			} else if ( display === false ) {
				$(this).removeClass('d-flex');
				$(this).hide();
			}
		});
		});

		var filter = '';
		$('.filter_dropdown').change(function(e) {
			filter = $('.filter_dropdown:checked').val();
			let new_url = addParam('status', filter);
			window.location.href = new_url;
		});

		function addParam(key,val) {
			var currentUrl = "<?php echo url()->full(); ?>";
			if(key == 'prescrutiny_status' && val == 'all'){
			currentUrl = "{{url()->current()}}";
			}
			var url = new URL(currentUrl);
			url.searchParams.set(key, val);
			return url.href;
		}
			var response_data_js = [];
		$('.payment_recipt_view').click(function(e) {
			var nom_id_value = $(this).attr('nom_id');
			$.ajax({
			url: "{{ url('/roac/recipt_details') }}",
			type: 'POST',
			data: '_token=<?php echo csrf_token() ?>&nom_id='+nom_id_value,
			dataType: 'json',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(json) {
				response_data_js = json['data']
				// console.log($.isEmptyObject(response_data_js));
				$('.cnd_name').text(json['data']['candidate_name']);
				$('#ac_name_no').text(json['data']['ac_no_name']);
				if(json['data']['st_code']=='S06'){
					$('.is_bihar').show();
					$('.is_guj').show();
					$('.online_recipt').attr("href", json['data']['online_receipt']);
					$('#bank_code').text(json['data']['bank_code']);
					$('#bank_reff_no').text(json['data']['bank_reff_no']);
					$('#txn_amount').text(json['data']['txn_amount']);
				}else if(json['data']['st_code']=='S04'){
					$('.is_bihar').show();
					$('.is_guj').hide();
					$('.online_recipt').attr("href", json['data']['online_receipt']);
				}
				$('#payment_date').text(json['data']['payment_date']);
				$('#payment_time').text(json['data']['payment_time']);
				$('#bank_reff_no').text(json['data']['challan_ref_id']);
				$('#txn_amount').text(json['data']['challan_amount']);

				if($.isEmptyObject(response_data_js) == false) {
					$('#payment_recipt').modal('show');
				}
			},
			error: function(data) {
			}
			});
			
		});

		$('.challan_payment_recipt_view').click(function(e) {
			var nom_id_value = $(this).attr('nom_id');
			$.ajax({
			url: "{{ url('/roac/challan_recipt_details') }}",
			type: 'POST',
			data: '_token=<?php echo csrf_token() ?>&nom_id='+nom_id_value,
			dataType: 'json',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(json) {
				response_data_js = json['data']
				// console.log($.isEmptyObject(response_data_js));
				$('.cnd_name').text(json['data']['candidate_name']);
				$('.ac_name_no').text(json['data']['ac_no_name']);
				$('.challan_date').text(json['data']['payment_date']);
				$('.challan_no').text(json['data']['challan_no']);
				$('.challan_recipt').attr("href", json['data']['challan_receipt'])
				if($.isEmptyObject(response_data_js) == false) {
					$('#challan_payment_recipt').modal('show');
				}
			},
			error: function(data) {
			}
			});
			
		});

		$("#election_type_id, #election_phase").change(function(e) {
            val = $(this).val();
            if($(this)[0].id == 'election_type_id'){
                var newurl = addParam('election_type_id', val);
			    window.location.href = newurl;
            }else if($(this)[0].id == 'election_phase'){
                var newurl = addParam('election_phase', val);
			    window.location.href = newurl;
            }
        });

	});
</script>
@endsection