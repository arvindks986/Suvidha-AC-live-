@extends('admin.layouts.ac.theme')
@section('title', 'Pre-Scrutiny')
@section('bradcome', 'Pre-Scrutiny')
@section('content')

<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom-dark.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
<section class="data_table mt-5">
	<div class="container-fluid">
		<?php   $url = URL::to("/");  ?>

		<div class="d-flex align-items-center justify-content-between">
			<h5>{!! $heading_title !!}</h5>
			<div>
				<!-- <a href="javascript:void(0)" class="btn btn-primary float-right" id="add_new_candidate">Apply New Nomination</a> -->
				{{-- <a href="{{url('/roac/nomination/apply-nomination-step-1')}}" class="btn btn-primary float-right"
					id="">Apply New Nomination</a> --}}
				<a href="{{url('/roac/dashboard')}}" class="btn btn-primary float-right"
				id="">Home</a>
			</div>
		</div>
	</div>
</section>
<main class="mt-4">
			<section class="fliter-wrap">
					<div class="my-1 mb-3 p-3">
						  <div class="d-flex align-items-center justify-content-between">
							<div id="reportrange">
								<i class="fa fa-calendar"></i>&nbsp;
								<span></span> <i class="fa fa-caret-down"></i>
							</div> 
							<div class="reason_msg">
								<select class="form-control" id="prescrutiny_status"
								name="prescrutiny_status">
								<option value="">Select Pre-Scrutiny Status</option>
								<option value="all"
									{{ $prescrutiny_status=='all' ? 'selected' : '' }}>
									All</option>
								<option value="true"
									{{ $prescrutiny_status=='true' ? 'selected' : '' }}>
									PreScrutiny Done
								</option>
								<option value="false"
									{{ $prescrutiny_status=='false' ? 'selected' : '' }}>
									PreScrutiny Pending
								</option>
								</select>
							</div>
							@if(isset($prescrutiny_status) && $prescrutiny_status=='true')
							<div class="reason_msg">
								<select class="form-control" id="prescrutiny_status_clear"
									name="prescrutiny_status_clear">
									<option value="all"
										{{ $prescrutiny_status_clear=='all' ? 'selected' : '' }}>
										All</option>
									<option value="true"
										{{ $prescrutiny_status_clear=='true' ? 'selected' : '' }}>
										PreScrutiny Cleared
									</option>
									<option value="false"
										{{ $prescrutiny_status_clear=='false' ? 'selected' : '' }}>
										Mark with Defect
									</option>
								</select>
							</div>
							@endif	
							<div class="input-group w-18">
								<input type="text" class="form-control input-lg" name="search"
									placeholder="Search By Nomination No." id="myInput" />
								<span class="input-group-append">
									<button class="btn btn-primary b-padding" type="submit"><i class="fa fa-search"></i></button>
								</span>
							</div>
							<div>
							<a href="{{ url('roac/listallapplicant_prescrutiny') }}" class="btn btn-primary btn-lg">Reset Filter</a>
							</div>	
									  
						  </div>	  
						</div>
			</section>
		
	

	<div class="container-fluid">
	  
	  <div class="pre-scurtiny-wrap">
		<div class="row">
		@if(count($results)>0)
		@php $i=1; @endphp
		@foreach($results as $item)
		<?php $item['gender'] =='male' ? $hgen = '(पु)' : '(म)' ?>
          <?php $item['gender'] =='male' ? $gen = '(M)' : '(F)' ?>
		  <div class="col-md-6 col-12 myTable">
			<div class="card shadow-sm mb-0">
			  <div class="row">
				<div class="col-sm-3 col-12">
				<figure class="img-id">
					@if(!empty($item['image']))
					<figcaption>{{ $i++ }}</figcaption>
					<img src="{{$item['image']}}" class="prfl-pic img-thumbnail" alt="">
					@else
					<img src="{{ asset('theme/img/male_avatar.png') }}"
						class="prfl-pic img-thumbnail" alt="">
					@endif</figure>
				</div>  
				<div class="col-sm-9 col-12">
				<div class="full-name px-3 pt-3 dark-purple-text">	
				<h5>{{ $item['hname'] }} <span>{{ $hgen }}</span></h5>
				 <h5>{{$item['candidate_name']}} <span>{{ $gen }}</span></h5>	
				</div>
				<div class="row">
				 <div class="col-sm-6 col-12">
				  <div class="d-inline-flex align-items-center mt-1">
				  <figure class="mb-0"><img src="{{ asset('theme/img/vendor/icon-001.png') }}"></figure>
				   <div>
					  <h6 class="mb-0">{{$item['father_hname']}}</h6>
					   <h6>{{$item['father_name']}}</h6>
				   </div> 
				 </div>  
				 </div>	  
				 <div class="col-sm-6 col-12">
				   <div class="d-inline-flex align-items-center mt-1">
				   <figure class="mb-0"><img src="{{ asset('theme/img/vendor/icon-003.png') }}"></figure>
					   <div>
						  <h6 id="newsrno">{{$item['nomination_no']}}</h6>
					   </div> 
					</div>  
				 </div>	  
				 <div class="col-sm-6 col-12">
					<div class="d-inline-flex align-items-center mt-1">
					<figure class="mb-0"><img src="{{ asset('theme/img/vendor/icon-002.png') }}"></figure>
					   <div>
						  <h6>{{$item['age']}}</h6>
					   </div> 
					</div> 
				 </div>	  
				 <div class="col-sm-6 col-12">
				  <div class="d-inline-flex align-items-center mt-1">
				  <figure class="mb-0"><img src="{{ asset('theme/img/vendor/icon-004.png') }}"></figure>
				   <div>
					  <h6>{{ $item['party_name']->PARTYNAME }}</h6>
				   </div> 
				</div>  
				 </div>	  
				</div>	  
				</div>  
			  </div>
			  <div class="card-footer card-foot-italic {{ $item['status_color'] }} border-0 py-2 px-0">
			  <div class="row mx-0">	
				<div class="col-sm-4 col-12 px-0">
				  <div class="p-2 border-right-dashed">
					<div>Application Status</div>
					<h6>{{$item['prescrutiny_status']}}</h6>	
				  </div>
				</div>
				<div class="col-sm-4 col-12 px-0">
				  <div class="p-2 border-right-dashed">
					<div>Status Date</div>
				  <h6>{{ !empty($item['prescrutiny_status_date']) ? $item['prescrutiny_status_date'] : $item['prescrutiny_apply_datetime'] }}</h6>	
				  </div>
				</div>
				<div class="col-sm-4 col-12 px-0">
				  <div class="p-2">
					<div>Application Date</div>
					<h6>{{ $item['prescrutiny_apply_datetime'] }}</h6>	
				  </div>
				</div>
			  </div>	
			  </div>  
			</div> 
			 <div class="row p-3 mb-3">
			   <div class="col">
			   <a href="{{$item['action_url']}}" class="btn purple-btn font-big shadow btn-block py-2">{{ empty($item['prescrutiny_status_db']) ? 'Procced For Pre-Scurtiny': 'View Pre-Scurtiny' }}</a>
			   </div> 
			   <div class="col">
			   <a href="{{ url('/roac/detail/'.encrypt_string($item['nom_id'])) }}" class="btn purple-btn font-big shadow btn-block py-2">View All Details</a>
			   </div>  
			   <div class="col">
			   <a href="{{ url('roac/checklist_genration', ['nom_id'=>encrypt_string($item['nom_id'])]) }}" class="btn purple-btn font-big shadow btn-block mt-2">Checklist</a>
			   </div>
			  </div>	
		  </div>
		  @endforeach
		  @endif
		  </div><!-- End Of pre-scurtiny-wrap Div -->
	</div><!-- End Of container-fluid Div --> 
  </main>
@endsection
@section('script')
<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script> 
<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>
@if (session('success_mes'))
	<script type="text/javascript">
	success_messages("{{session('success_mes') }}");
</script>
@endif

@if (session('error_mes'))
<script type="text/javascript">
	error_messages("{{session('error_mes') }}");
</script>
@endif

<script type="text/javascript">

$(document).ready(function(e) {

	var v = $("#noval").val();
	//By Searh Text
	jQuery("#myInput").on("keyup", function() {
	var value = $(this).val().toUpperCase();
	jQuery(".myTable").filter(function() {
		jQuery(this).toggle(jQuery(this).text().toUpperCase().indexOf(value) > -1)
	});
	});

	var prescrutiny_status = '';
	var start = moment(new Date("<?php echo isset($between[0])? $between[0] : Date('m/d/Y') ?>"));
	var end = moment(new Date("<?php echo isset($between[1])? $between[1] : Date('m/d/Y') ?>"));
	function cb(start, end) {
	$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	}

	jQuery('#reportrange').daterangepicker({
	ranges: {
	'Today': [moment(), moment()],
	'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	'Last 14 Days': [moment().subtract(13, 'days'), moment()]
	// 'This Month': [moment().startOf('month'), moment().endOf('month')],
	//'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	},
	maxDate: new Date()
	}, cb);

	cb(start, end);

	<?php if(empty($between[0])){ ?>
	$('#reportrange span').html("Select Date Range");
	<?php } ?>

	$("#all").click(function(e) {
	var url = "<?php echo url('/eci/online_nom/count-report') ?>";
	window.location.href = url;
	});

	$('#reportrange').on('hide.daterangepicker', function(ev, picker) {
	var start = picker.startDate.format('MM/DD/YYYY');
	var end = picker.endDate.format('MM/DD/YYYY');
	var val = start +' - '+ end;
	let newurl = addParam('date', val);
	window.location.href = newurl;
	});

	$('#prescrutiny_status').change(function(e) {
	prescrutiny_status = $('#prescrutiny_status').val();
	let newurl = addParam('prescrutiny_status', prescrutiny_status);
	window.location.href = newurl;
	});

	$('#prescrutiny_status_clear').change(function(e) {
	prescrutiny_status_clear = $('#prescrutiny_status_clear').val();
	let newurl = addParam('prescrutiny_status_clear', prescrutiny_status_clear);
	window.location.href = newurl;
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
	});
</script>
@endsection