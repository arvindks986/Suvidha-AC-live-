@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Physical Verification')
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
	<div class="container-fluid">
		<div class="card card-shadow">
			<div class="card-header">
				<div class="row align-items-center">
				<div class="col-md-6 col-12"><!--<h4 class="mb-0">Physical Verification</h4>-->
					<a href="{{url('/roac/dashboard')}}" class="btn btn-primary"
					id="">Home</a>
					<a href="{{url('/roac/list_of_applicatiant_pdf')}}" class="btn btn-primary" id="">Print Pdf</a>
					<button id="show_qr_code" class="btn btn-primary" type="button" style="display: none;">Scan QR CODE</button>
				</div> 
				<div class="col-md-6 col-12 text-right">
					<button type="button" id="filter_btn" class="btn btn-primary" style='{{ empty($status_filter) ? '' : 'display: none;'}}'>Show All Application</button>
					<a id="filter_row_hide" href="{{ url('/roac/listallapplicant') }}" class="btn btn-primary" style='{{ empty($status_filter) ? "display: none;" : ''}}'>Scan QR CODE</a>
				</div> 
				</div>	 
			</div>
			<div id="all_body" class="card-body vlight-pink p-0">
				<div class="qrCode" style='{{ !empty($status_filter) ? "display: none;" : ''}}'>
				<div class="qrBar">
				<img id="qr_image" src="{{ asset('theme/img/vendor/qrcode.png') }}" alt="">
				<div id="video_section" style="display: none;">
					<video id="preview" ></video>
				</div>
				</div>  
				<div class="scanQr">
					<button type="button" id="camera_btn" class="btn dark-pink-btn font-big"><i class="fa fa-camera" aria-hidden="true"></i> Scan QR Code</button>
					<button type="button" id="stop_camera_btn" class="btn btn-primary font-big" style="display: none;"><i class="fa fa-ban" aria-hidden="true"></i> Stop Scanner</button>
				</div>  
				</div>
				<div id="filter_row" class="row m-1" style="{{ empty($status_filter) ? "display: none;" : ''}}">
					<div class="col">
						<fieldset class="mb-2">
							<legend>Physical verification<sup>*</sup></legend>
							<div class="row">
								<div class="col">
									<label class="radioBtn">All applications
										<input type="radio" class="filter_dropdown" name="filter"
											value="all" {{ $status_filter=='all' ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="col">
									<label class="radioBtn">Pending physical verification
										<input type="radio" class="filter_dropdown" name="filter"
											value="pending" {{ $status_filter=='pending' ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="col">
									<label class="radioBtn">Physical verification done
										<input type="radio" class="filter_dropdown" name="filter"
											value="cleared" {{ $status_filter=='cleared' ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
				<div class="srchBox">
				<div id="input_search_box" class="input-group" style='{{ !empty($status_filter) ? "display: none;" : ''}}'>
					<input type="text" id="qrcode" class="form-control" placeholder="Search By Nomination No./Name" value="">
					<div class="input-group-append">
						<button class="btn dark-purple-btn" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>
					</div>
				</div> 
				</div>	
			</div>
		</div>  
		
	</div><!-- End Of container-fluid Div --> 
	</main>

<main class="pt-3 px-2">
  <div class="container-fluid">
	{{-- <div class="col-md-12 mb-2">
		<div id="video_section" class="row" style="display: none;">
			<div class="col-md-12 d-flex justify-content-center">
				<video id="preview"></video>
			</div>
		</div>
		<div class="row mb-1">
			<div class="col-md-6 text-left">
				<button type="button" class="btn btn-success btn-lg" id="camera_btn">Scan QR CODE</button>
			</div>
			<div class="col-md-6 text-right">
				<button type="button" class="btn btn-primary btn-lg" id="stop_camera_btn" style="display: none;">Stop Scanner</button>
			</div>
		</div>
	</div> --}}

	{{-- <div class="full-search-box">
	  <select id="filter_dropdown" name="filter" class="form-control">
		  <option value="">Physical Verification Status</option>	  
		<option value="all" {{ $status_filter=='all' ? 'selected' : '' }}>All</option>	
		<option value="pending" {{ $status_filter=='pending' ? 'selected' : '' }}>Pending</option>	
		<option value="cleared" {{ $status_filter=='cleared' ? 'selected' : '' }}>Done</option>	  
	  </select>	
	  <div class="input-group">
		  <input type="text" name="qrcode" id="qrcode" class="form-control" placeholder="Search By Nomination No." value="">
		  <div class="input-group-append">
			<button class="btn btn-lg font-big dark-purple-btn" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>
		  </div>
		</div>
	</div><!-- End Of full-search-box Div -->   --}}
<div class="physc-wrap" style="{{ ($status_filter=='') ? 'display: none' : '' }}">
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
			   <h5>{{ !empty($result['hname']) ? $result['hname'] : '' }} <span>{{ $hgen }}</span></h5>
        <h5>{{ $result['name'] }} {{ $gen }}</h5>	
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

		{{-- <div class="bg-light p-2 custom-border-right w-35">
        <strong>Application Status</strong> --}}
				<?php 
				// if(empty($result['prescrutiny_status'])){
				// 	$status = 'submitted For Pre-Scrutiny';
				// 	$status_color = 'pending';
				// }elseif($result['prescrutiny_status'] == '1'){
				// 	$status = 'Pre-Scrutiny Cleared';
				// 	$status_color = 'cleared';
				// }elseif($result['prescrutiny_status'] == '2'){
				// 	$status = 'Defects in Pre-Scrutiny';
				// 	$status_color = 'defected';
				// }
				?>
				{{-- <h5>{{$status}}</h5> 
				<div class="phyStatus">
				  <span class="{{ $status_color }}"></span>
				</div> 
				<strong>Status Date</strong>
        <h5>{{ date('d-m -Y', strtotime($result['prescrutiny_apply_datetime'])) }}</h5>   
			</div> --}}
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
				<strong>Status Date</strong>
				@if($result['finalize_after_payment'] == '1')
				<h5>{{ !empty($result['finalize_after_payment_date']) ? date('d-m-Y', strtotime($result['finalize_after_payment_date'])) : '' }}</h5>
				@else
				<h5>-</h5>
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
						@if($payment_details['payment_detail'][0]->bank_transtimestamp)
							<h5>{{ date('d-m-Y',strtotime($payment_details['payment_detail'][0]->bank_transtimestamp)) }}</h5>/&nbsp;
						@else
							<h5>{{ $payment_details['payment_detail'][0]->bank_transtimestamp }}</h5>/&nbsp;
						@endif
					@else
					<h5>-</h5>/&nbsp;
					@endif
					@if($status_color=='defected')
					<h5>Not Avilable</h5>&nbsp;
				@else
					<h5><a href="#" class="payment_recipt_view" nom_id="{{$result['nomination_no']}}" >View</a></h5> &nbsp;
				@endif
				@elseif($payment_details['payment_type'] == 'Challan')
				<h5>Challan/</h5>&nbsp;
					@if(count($payment_details['payment_detail']) > 0)
					<h5>{{ date('d-m-Y',strtotime($payment_details['payment_detail'][0]->challan_date)) }}</h5>/&nbsp;
					@else
					<h5>-</h5>/&nbsp;
					@endif
					@if($status_color=='defected')
					<h5>Not Avilable</h5>&nbsp;
					@else
						<h5><a href="#" class="challan_payment_recipt_view" nom_id="{{$result['nomination_no']}}" >View</a></h5> &nbsp;
					@endif
					@elseif($payment_details['payment_type'] == 'Pay By Cash Paid')
					<h5>Paid by cash/</h5>&nbsp;
						@if(count($payment_details['payment_detail']) > 0 && $payment_details['payment_detail'][0]->pay_by_cash_paid == '1')
						<h5>{{ date('d-m-Y',strtotime($payment_details['payment_detail'][0]->date_time_of_pbc)) }}</h5>&nbsp;
						@else
						<h5>-</h5>&nbsp;
						@endif
						@if($status_color=='defected')
						<h5>Not Avilable</h5>&nbsp;&nbsp;
						@endif
				@else
				<h5><button cand_id="{{ $result['candidate_id'] }}" Pay_by_cash={{ $payment_details['payment_opt_ro'] == 'Pay By Cash' ? '1' : '0'  }} class="btn btn-primary enter_details">Enter Payment Detail</button></h5> &nbsp;
				@endif
			</div>
			</div>
					@php 
						$btn_status = \app(App\Http\Controllers\Admin\CandNomination\ApplicantController::class)->is_nomination_exist($result['nomination_no']);
					@endphp
			<div class="text-center w-15 p-2">
				<div class="font-big">Action</div>
				@if(!$btn_status)
				<div class="my-2 phy-pending"><a href="#"><i class="fa fa-hourglass-end" aria-hidden="true"></i></a></div>
				@else
				<div class="my-2 phy-success"><a href="#"><i class="fa fa-check" aria-hidden="true"></i></a></div>		
				@endif
				<div class="my-2">
					@if(!$btn_status)
					<a href="{{$url.'/roac/candidateinformation?nom_id='.encrypt_string($result['nomination_no'])}}" class="phy-btn dark-purple-btn">Proceed For Physical Verification</a>
					@else
					Receipt <i class="fa fa-download" aria-hidden="true"><a href="{{ url('/roac/nomination-receipt-print').'?'.http_build_query(['nom_id' => encrypt_string($result['nomination_no']), 'direct_print'=> true]) }}" target="_blank">English</a></i>\<i class="fa fa-download" aria-hidden="true"><a href="{{ url('/roac/nomination-receipt-print/Hindi').'?'.http_build_query(['nom_id' => encrypt_string($result['nomination_no']), 'direct_print'=> true]) }}" target="_blank">Hindi</a></i>
					@endif
				</div>
				
            <div>
		<div><a href="{{ url('/roac/detail/'.encrypt_string($result['id'])) }}" class="btn font-big">View All Details</a></div>
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
				<li class="is_guj"><label>Bank Reference Number:</label><span id="bank_reff_no"></span></li>
				<li class="is_guj"><label>Amount:</label><span id="txn_amount"></span></li>
				<li><label>Payment Date:</label><span id="payment_date"></span></li>
				<li><label>Payment Time:</label><span id="payment_time"></span></li>
			 </ul>
			 <p class="note-warn"><strong><i>Instruction <sup>*</sup></i></strong>Please carry all original and necessary documents for verification</p>	
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
				<li><label>Challan Receipt:</label><span><a href="#" class="challan_recipt" target="_blank">View</a></span></li>
				<li><label>Challan Date:</label><span class="challan_date"></span></li>
			 </ul>
			 <p class="note-warn"><strong><i>Instruction <sup>*</sup></i></strong>Please carry all original and necessary documents for verification</p>	
			</div>
			
			<!-- Modal footer -->
			<div class="confirm-footer">
			  <button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">Ok</button>
			  <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
			</div>
			
		  </div>
		</div>
	  </div><!-- End Of confirm Modal popup Div -->

	  <!-- Challan Recipt Entry Form -->

	  <div class="modal fade modal-cancel" id="payment_detail_form">
		<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
		  <div class="modal-content">
		   <div class="pop-header pt-3 pb-1">
			  <div class="animte-tick"><span>&#10003;</span></div>	
			  <h5 class="modal-title"></h5>
			</div>
			<form class="form-horizontal" method="post" action="" multiple="multiple">
			  {{ csrf_field() }}
			 <div class="modal-body">
				<input type="hidden" name="candidate_id" value="" id="candidate_id">
			  <div class="challan_text" style="text-align: center; font-size: 15px; font-weight: bold;">{{ __('Paid by challan') }}</div>
			   @if(!empty($state_payment_url))
				<div style="text-align: center;">{{ __('messages.paylink') }}<span><a href="http://<?php echo $state_payment_url; ?>" target="_blank">&nbsp; Payment Link</a></span></div>
			   @endif	
			  <br>
			  <ul style="list-style: none;">
				<div class="challan_payment_opt">
				<li>
					<div class="row">
					<div class="col-md-4">
						<label>{{ __('Challan No') }}</label>
					</div>
					<div class="col-md-8">
						<input type="text" name="challan_no" id="challan_no" placeholder="Challan No" required maxlength="15">
					</div>
					</div>
				</li>
				<li>
					<div class="row">
						<div class="col-md-4">
							<label>{{ __('Challan Date') }}</label>
						</div>
						<div class="col-md-8">
							<input type="text" name="challan_date" class="date"  id="challan_date" placeholder="DD-MM-YYYY" required>
						</div>
						</div>
				</li>
				<li class="mb-3">
					<div class="row">
						<div class="col-md-4
						">
							<label>{{ __('Challan Receipt') }}</label>
						</div>
						<div class="col-md-8">
							<input type="file" name="challan_recipt" id="challan_recipt" placeholder="Recipt" required accept="image/*,.pdf">
						</div>
					</div>
				</li>
				</div>
				<li class="mtb-2 text-center text_sep_or">
					<h3 class="text-danger">OR</h3>
				</li>
				<div class="pay_by_cash_opt">
				<li>
					<div class="row">
						<div class="col-md-12">
							<fieldset class="mb-2">
                                <legend>Paid by cash <sup>*</sup></legend>
                                <div class="row">
                                    <div class="col">
                                        <label class="radioBtn">Amount Paid
                                            <input type="checkbox"
                                            class="pay_by_cash" name="pay_by_cash"
                                            value="1">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
						</div>
					</div>
				</li>
			</div>
			 </ul>
			  <div class="confirm-footer mt-4">
				<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('Cancel') }}</button>	
				&nbsp;
			   <button type="button" id="payment_detail_form_submit" class="btn dark-pink-btn" style="height: 30px;">
			  {{ __('Submit') }}</button>
			  &nbsp; &nbsp; &nbsp;		
			</div>
			 </div>
			 </form>
		  </div>
		</div>
	  </div>
	  <!-- End Challan Recipt Entry Form  -->
	  
	  <!--- Modal For Warning Message Finalize --->

	  <div class="modal fade modal-cancel" id="nomination_finalize_model">
		<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
		  <div class="modal-content">
		   <div class="pop-header pt-3 pb-1">
			  <div class="animte-tick"><span>&#10003;</span></div>	
			  <h5 class="modal-title"></h5>
			</div>
			 <div class="modal-body">	
			  <br>
			  <ul style="list-style: none;" class="text-center">
				<li>
					<h5>Online Nomination filled up has not been finalised by the Candidate</h5>
				</li>
			 </ul>
			  <div class="confirm-footer mt-4 text-center">
				<button type="button" class="btn btn-secondary " data-dismiss="modal" style="background:#f0587e; border: none;">OK</button>			
			</div>
			 </div>
			 </form>
		  </div>
		</div>
	  </div>

	  <!-- End Finalize -->
	  

<!-- Modal Content Starts here -->
@include('admin/nfd/nomination/form/step1')
@endsection
@section('script')
	<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>
	<script type="text/javascript" src="{{ asset('theme/js/instascan.min.js') }}"></script>
<script type="text/javascript">

    jQuery(document).ready(function(){

		$('.date').datetimepicker({
			format: 'DD-MM-YYYY',
			//maxDate: moment().format('MM-DD-YYYY')
			maxDate: new Date()
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
		if(value != ''){
			$('.physc-wrap').show();
		}else{
			$('.physc-wrap').hide();
		}
		jQuery(".myTable").filter(function() {
			// jQuery(this).toggle();
			const display = jQuery(this).text().toUpperCase().indexOf(value) > -1
			if ( display === true ) {
				$('html, body').animate({
						scrollTop: $(".physc-wrap").offset().top
					}, 2000);
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
				$('.challan_recipt').attr("href", json['data']['challan_receipt']);
				if($.isEmptyObject(response_data_js) == false) {
					$('#challan_payment_recipt').modal('show');
				}
			},
			error: function(data) {
			}
			});
			
		});

		var scanner = '';
		// QR Code Scanner
		$('#camera_btn').click(function(e) {
			$('#qr_image').hide();
			$('#camera_btn').hide();
			$('#video_section').show();
			$('#stop_camera_btn').show();
			scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
			scanner.addListener('scan', function (content) {
					$.ajax({
						url: "{{ url('/roac/decrypt_nom_id') }}",
						type: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'nom_id': content
						},
						dataType: 'json',
						beforeSend: function() {
						},
						complete: function() {
						},
						success: function(json) {
							if(json['finalize_after_payment'] == 0){
								$('#nomination_finalize_model').modal('show');
							}
							$('#qrcode').val(json['nom_id']);
							$('#qrcode').trigger('keyup');
							$('html, body').animate({
								scrollTop: $(".physc-wrap").offset().top
							}, 2000);
							scanner.stop().then(function(succ) {
								$('#all_body').fadeOut(500);
								$('#show_qr_code').fadeIn(500);
								$('#stop_camera_btn').hide();
								$('#qr_image').hide();
								$('#camera_btn').removeAttr('disabled');
								// return console.log('camera Stop');
							}).catch(function(err) {
								return console.log(err);
							});
						},
						error: function(data) {
						}
					});
				});
				Instascan.Camera.getCameras().then(function (cameras) {
					if (cameras.length > 0) {
					scanner.start(cameras[0]);
					} else {
					console.error('No cameras found.');
					}
				}).catch(function (e) {
					console.error(e);
				});
				
				//let result = scanner.scan()
				
				let opts = {
			// Whether to scan continuously for QR codes. If false, use scanner.scan() to manually scan.
			// If true, the scanner emits the "scan" event when a QR code is scanned. Default true.
			continuous: true,
			
			// The HTML element to use for the camera's video preview. Must be a <video> element.
			// When the camera is active, this element will have the "active" CSS class, otherwise,
			// it will have the "inactive" class. By default, an invisible element will be created to
			// host the video.
			video: document.getElementById('preview'),
			
			// Whether to horizontally mirror the video preview. This is helpful when trying to
			// scan a QR code with a user-facing camera. Default true.
			mirror: true,
			
			// Whether to include the scanned image data as part of the scan result. See the "scan" event
			// for image format details. Default false.
			captureImage: true,
			
			// Only applies to continuous mode. Whether to actively scan when the tab is not active.
			// When false, this reduces CPU usage when the tab is not active. Default true.
			backgroundScan: true,
			
			// Only applies to continuous mode. The period, in milliseconds, before the same QR code
			// will be recognized in succession. Default 5000 (5 seconds).
			refractoryPeriod: 5000,
			
			// Only applies to continuous mode. The period, in rendered frames, between scans. A lower scan period
			// increases CPU usage but makes scan response faster. Default 1 (i.e. analyze every frame).
			scanPeriod: 1
			};
		});

		$('#show_qr_code').click(function(e) {
			$('#all_body').fadeIn(500);
			$('#show_qr_code').fadeOut(500);
			$('#camera_btn').show();
			$('#qr_image').fadeIn(500);
			$('#qrcode').val('');
			$('#qrcode').trigger('keyup');
			$('html, body').animate({
				scrollTop: $(".physc-wrap").offset().top
			}, 2000);
		});

		$('#stop_camera_btn').click(function(e) {
			scanner.stop().then(function(succ) {
				$('#stop_camera_btn').hide();
				$('#qr_image').show();
				$('#camera_btn').show();
				// return console.log('camera Stop');
			}).catch(function(err) {
				return console.log(err);
			});
		});
		var cand_id_val = '';
		var pay_by_cash = '';
		$('.enter_details').click(function(e) {
			cand_id_val = $(this).attr('cand_id');
			pay_by_cash = $(this).attr('Pay_by_cash');
			console.log(pay_by_cash);
			$('#candidate_id').val(cand_id_val);
			if(pay_by_cash=='1'){
				$('.challan_text').text('');
				$('.challan_payment_opt').hide();
				$('.text_sep_or').hide();
				$('.pay_by_cash_opt').show();
			}else if(pay_by_cash=='0'){
				$('.challan_payment_opt').show();
				$('.text_sep_or').show();
				$('.pay_by_cash_opt').show();
			}
			$('#payment_detail_form').modal('show');
		});

		$('#payment_detail_form_submit').click(function(e) {
			var form = $("#payment_detail_form form");
			var formData = new FormData(form[0]);
				$.ajax({
				url: "{{ url('/roac/submit_challan_details') }}",
				type: 'POST',
				data: formData,
				dataType: 'json',
				processData: false,
    			contentType: false,
				beforeSend: function() {
					$('#payment_detail_form .text-danger').remove();
					$('#payment_detail_form input').removeClass('input-error');
					$('#payment_detail_form_submit').prop('disabled',true);
					$('#payment_detail_form_submit').text("Validating...");
					$('#payment_detail_form_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
				},
				complete: function() {
				},
				success: function(json) {
					if(json['success'] == true){
						window.location.reload();
					}
					if(json['success'] == false){
					if(json['errors']['warning']){
						alert(json['errors']['warning']);
					}
					if(json['errors']['challan_no']){
						$("#payment_detail_form #challan_no").addClass("input-error");
						$("#payment_detail_form #challan_no").after("<li class='text-error text-danger text-right pull-right'>"+json['errors']['challan_no'][0]+"</li>");
					}
					if(json['errors']['challan_date']){
						$("#payment_detail_form #challan_date").addClass("input-error");
						$("#payment_detail_form #challan_date").after("<li class='text-error text-danger text-right pull-right'>"+json['errors']['challan_date'][0]+"</li>");
					}
					if(json['errors']['challan_recipt']){
						$("#payment_detail_form #challan_recipt").addClass("input-error");
						$("#payment_detail_form #challan_recipt").after("<li class='text-error text-danger text-right pull-right'>"+json['errors']['challan_recipt'][0]+"</li>");
					}
					}
					$('#payment_detail_form_submit').prop('disabled',false);
					$('#payment_detail_form_submit').text("Submit");
					$('.loading_spinner').remove();
				},
				error: function(data) {
					var errors = data.responseJSON;
					$('#payment_detail_form_submit').prop('disabled',false);
					$('#payment_detail_form_submit').text("Submit");
					$('.loading_spinner').remove();
				}
				});
			});

	});
</script>
  @endsection
