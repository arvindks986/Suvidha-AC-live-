@extends('admin.layouts.ac.theme')
@section('content')

<style type="text/css">
	h3{font-size: 16px; font-weight:400;}
	td{padding:0px;}
	.card-footer.p-0 { border-top: 0px;}
	.btn-blue{background:#44a6e7; color:#fff;}
	.btn-blue:hover{background:#0689e0; color:#fff;}
	.lightblue{color:#44a6e7;}
	.turnoutbg{background:#49a8a4; color:#fff;}
	.poll-table td,.poll-table th{

	}
	.sticky{
		position: sticky;
		top: 0px;
	}
	.loader_bottom{
		bottom: 0px;
		left: 0px;
		width: 100%;
		position: fixed;
		text-align: center;
		color: #FFF;
		background: #000;
		z-index: 99999;
	}
	.statistics.dashboard .card {
		padding: 0px;
		margin-bottom: 0px;
	}
	.tabspoint{min-height: 149px;}

	.clockSize {
		color: green;

	}
	.time {
		margin-right: 12px;
		font-size: 13px; width: 125px;
	}
	.color-white{background:#fff;}
	caption {
		display: block;
		line-height: 3em;
		width: 100%;
		-webkit-align-items: stretch;
		border: 1px solid #eee;
	}
	.color-white{background:#fff;}
	div.sticky {
		position: -webkit-sticky; /* Safari */
		position: sticky;
		top: 0;
	}
	.nav-header.welcome ul.float-right {  margin-bottom: 0;    margin-top: 8px;}
	.nav-header.welcome ul.float-right li {  list-style: none;    margin: 3px 0 0 0; font-size:14px;}
    #map_in {
         min-height: 100%;
       }
    #mock_poll tbody tr td, #infra_table tbody tr td{
    	text-align: center;
    }

</style>

 <link rel="stylesheet" href="{!! url('theme/css/booth.css') !!}">    <!-- Favicon-->
<section class="statistics dashboard color-grey pt-2 pb-2" style="border-bottom:1px solid #eee;">
	<div class="container-fluid">
		<div class="row d-flex align-items-center">
			@if($role_id==7)
			@endif
			<h4 class="page-title mr-auto">{!! $heading_title !!}</h4>
			<div class="ml-auto mr-3">
				<div class="nav btn-group" id="myTab" role="tablist">




					<div class="time" id="print_time">
						<div id="clock" > <i class="fa fa-clock-o clockSize">&nbsp;&nbsp; </i>
							<span class="unit" id="hours"></span> : <span class="unit" id="minutes"></span> : <span class="unit" id="seconds"></span>  <span class="unit" id="ampm"></span>
						</div>
						<p id="date"></p></div>


						<button type="button" class="btn btn-blue refreshbtn" id="show"><span class="fa fa-refresh"></span>  Refresh</button>
						<!-- <button type="button" href="javascript:void(0)" onclick="referesh_page()" class="btn btn-blue refreshbtn"><span class="fa fa-refresh"></span>  Refresh</button>  -->&nbsp; &nbsp;

						<a class="btn btn-activelist  @if($active_tab == 'before') active @endif  btn-radius1" id="before-tab" data-toggle="tab" href="#beforepoll" role="tab" aria-controls="before" aria-selected="true" >Before Poll</a>

						<a class="btn btn-activelist @if($active_tab == 'after') active @endif" id="after-tab" data-toggle="tab" href="#afterpoll" role="tab" aria-controls="after" aria-selected="false">Poll Day</a>


						<a class="btn btn-activelist  @if($active_tab == 'pollday') active @endif" id="poll-tab" data-toggle="tab" href="#resultpoll" role="tab" aria-controls="after" aria-selected="false">Poll Result</a>
						@if($role_id == '19')
						<a class="btn btn-activelist " href="{!! url('roac/booth-app-revamp/officer-list') !!}"><i class="fa fa-plus"></i> Add User</a>
						@endif



					</div>

				</div>
			</div></div>


		</section>

		<div class="tab-content" id="myTabContent">


			@include('admin/common/form-filter-ajax')
			@if(Auth::user() && in_array(Auth::user()->role_id,[4,5,7]))
			@endif
			<div class="tab-pane fade @if($active_tab == 'before') show active @endif" id="beforepoll" role="tabpanel" aria-labelledby="before-tab">


				<section class="pollDashboard dashboard bg-midnight-bloom pt-2 pb-5 mb-5" style="border-bottom:1px solid #eee;">
					<div class="container-fluid">

					<div class="row mt-4">
					<div class="col-md-9">
					<div class="row">
							<div class="col pr-0" style="display: none;">
								<div class="card income cardBox">
									<div class="card-body p-2">
										<div class="row d-flex align-items-center">
											<p class="col text-left">Officers Not assigned</p> <span class="text-primary mr-auto pr-3"><a target="_blank" id="total_not_assign_officers">{{$total_not_assign_officers}}</a></span>
										</div>
									</div>

								</div>
							</div>

							<!--<div class="col pr-0">
								<div class="card income cardBox">
									<div class="card-body p-2">
										<div class="row d-flex align-items-center">
											<p class="col text-left">No. of Polling Stations</p> <span class="text-primary mr-auto pr-3"><a href="{!! $href_polling_station !!}" target="_blank" id="total_polling_booth">{{$total_polling_booth}}</a></span>
										</div>
									</div>

								</div>
							</div> -->

							<div class="col pr-0">
								<div class="card income cardBox">
									<div class="card-body p-2">
										<div class="row d-flex align-items-center">
											<p class="col text-left">No. of Polling Station Locations</p> <span class="text-primary mr-auto pr-3" id="total_polling_location">
												<a href="{!! $href_mapped_location !!}" target="_blank">{{$total_polling_location}}</a>
											</span>
										</div>
									</div>

								</div>
							</div>

					</div>
					<br />
						<div class="row d-flex ">





							<!--<div class="col pr-0">
								
								<div class="card income cardBox">
								<div class="card-body p-2 orangeLite">
								<div class="row d-flex align-items-center">
												<p class="col text-left"><strong>PRO</strong>Not activated</p>
												<span class="text-primary mr-auto pr-3"><a href="{{$href_not_activated}}" id="total_pro_not_activated" target="_blank">{{$total_pro_not_activated}}</a></span>
											</div>
										</div>

									</div>

									<div class="card income cardBox cardBottom">

										<div class="card-body p-2">
											<div class="row d-flex align-items-center">
												<p class="col text-left">Total PRO</p> <span class="text-primary mr-auto pr-3"><a href="{{$href_not_activated}}" id="total_pro_assign" target="_blank">{{$total_pro_assign}}</a></span>
											</div>
										</div>

									</div>
								</div> -->

								<div class="col pr-0">
								<!-- Income-->
								<div class="card income cardBox greenLite">
									<div class="card-body p-2">
									<div class="row d-flex align-items-center">
												<p class="col text-left"><strong class="text-primary">PO</strong>Not activated</p> <span class="text-primary mr-auto pr-3"><a href="{{$href_not_activated}}" id="total_po_not_activated" target="_blank">{{$total_po_not_activated}}</a></span>
											</div>


										</div>

									</div>

									<div class="card income cardBox cardBottom">
										<div class="card-body p-2">
											<div class="row d-flex align-items-center">
												<p class="col text-left">Total PO</p> <span class="text-primary mr-auto pr-3"><a href="{{$href_not_activated}}" id="total_po_assign" target="_blank">{{$total_po_assign}}</a></span>
											</div>
										</div>

									</div>
								</div>

								<!--<div class="col pr-0" >
								
								<div class="card income cardBox purpleLite">
								<div class="card-body p-2">
											<div class="row d-flex align-items-center">
												<p class="col text-left"><strong class="text-primary">SM</strong>Not activated</p> <span class="text-primary mr-auto pr-3">
												<a href="{{$href_not_activated}}" id="total_so_not_activated" target="_blank">{{$total_so_not_activated}}</a></span>
											</div>
										</div>
									</div>

									<div class="card income cardBox cardBottom">
									<div class="card-body p-2">
											<div class="row d-flex align-items-center">
												<p class="col text-left">Total SM</p> <span class="text-primary mr-auto pr-3"><a href="{{$href_not_activated}}" id="total_so_assign" target="_blank">{{$total_so_assign}}</a></span>
											</div>
										</div>

									</div>
								</div>


								<div class="col pr-0" >
									
									<div class="card income cardBox ">

									<div class="card income cardBox blueLite">
											<div class="card-body p-2">
												<div class="row d-flex align-items-center">
													<p class="col text-left"><strong class="text-primary">BLO</strong>Not activated</p> <span class="text-primary mr-auto pr-3"><a href="{{$href_not_activated}}" id="total_blo_not_activated" target="_blank">{{$total_blo_not_activated}}</a></span>
												</div>
											</div>
										</div>

										</div>

										<div class="card income cardBox cardBottom">
											<div class="card-body p-2">
												<div class="row d-flex align-items-center">
													<p class="col text-left">Total BLO</p> <span class="text-primary mr-auto pr-3"><a href="{{$href_not_activated}}" id="total_pro_assign" target="_blank">{{$total_blo_assign}}</a></span>
												</div>
											</div>
										</div>
									</div> -->


								<div class="col pr-0" style="display: none;">
									<!-- Income-->
									<div class="card income cardBox ">

									<div class="card income cardBox blueLite">
										<div class="card-body p-2">
											<div class="row d-flex align-items-center">
												<p class="col text-left"><strong class="text-primary">Unmapped</strong>PS</p> <span class="text-primary mr-auto pr-3"  id="total_unmapped_location"><a href="{!! $href_mapped_location !!}" target="_blank">{{$total_unmapped_location}}</a></span>
											</div>
										</div>
									</div>

										</div>

										<div class="card income cardBox cardBottom">
											<div class="card-body p-2">
												<div class="row d-flex align-items-center">
													<p class="col text-left">Mapped Locations</p> <span class="text-primary mr-auto pr-3" id="total_mapped_location" ><a href="{!! $href_mapped_location !!}" target="_blank">{{$total_mapped_location}}</a></span>
												</div>
											</div>
										</div>
									</div>



							</div>

					</div>
					<div class="col">
					<div class="row">


									<div class="col">
										<!-- Income-->
										<div class="card income cardBox text-center" style="">
											<div class="card-body">
												<!-- <div class="icon"><img src="{{ asset('theme/images/app-download-checked.png') }}" alt="" /></div> -->
												<div class="number green" style="min-height: 100px;"><a style=" font-size:70px;" href="{!! $href_e_download !!}" id="total_e_download" target="_blank">
													{{$total_e_download}}
												</a>
											</div>
											<p><strong class="text-primary">E Roll download </strong>confirmed</p>
										</div>
										</div>

								</div>
					</div>
					</div>
					</div>





						</div>
					</section>

					<!-- end col -->

					<div class="container-fluid " style="display: none;">
						<div class="row color-white">
							<div class="col">
								<div class="card ">
									<div class="card-header bg-white">
										<h2 class="">Infra Mapping</h2>
									</div>
									<div class="card-body p-0" id="infra_table">
										<table align="center" class="table table-bordered poll-table mb-0">
											<thead>
												<tr>
													<th>Start</th>
													<th>RAMP</th>
													<th>Toilet Facility</th>
													<th>Exit Door</th>
													<th>Furniture</th>
													<th>Light</th>
													<th>Drinking Water</th>

												</tr>
											</thead>
											<tbody>

												<tr>
												<td id="infra_start">{{$infra_start}}</td>
												<td id="infra_ramp">{{$infra_ramp}}</td>
												<td id="infra_toilet_facility">{{$infra_toilet_facility}}</td>
												<td id="infra_exit_door">{{$infra_exit_door}}</td>
												<td id="infra_furniture">{{$infra_furniture}}</td>
												<td id="infra_light">{{$infra_light}}</td>
												<td id="infra_drinking_water">{{$infra_drinking_water}}</td>
												</tr>

											</tbody>
											<tfoot>
												<tr>
												<td colspan="7" align="right">
												<a href="{{$href_infra_poll}}" target="_blank"><b class="col text-right">View Details</b></a>
												</td>
												</tr>
											</tfoot>


										</table>
									</div>
								</div>
							</div>

							<div class="col">
								<div class="card ">
									<div class="card-header bg-white">
										<h2 class="">Mock Poll</h2>
									</div>
									<div class="card-body p-0" id="mock_poll">
										<table align="center" class="table table-bordered poll-table mb-0">
											<thead>
												<tr>
													<th>Start</th>
													<th>Result</th>
													<th>Clear</th>
													<th>Remove Slip</th>

												</tr>
											</thead>
											<tbody>

												<tr>
												<td id="mock_poll_start">{{$mock_poll_start}}</td>
												<td id="mock_poll_result_shown">{{$mock_poll_result_shown}}</td>
												<td id="mock_button_clear">{{$mock_button_clear}}</td>
												<td id="mock_slip_remove">{{$mock_slip_remove}}</td>
												</tr>

											</tbody>
											<tfoot>
												<tr>
												<td colspan="7" align="right">
												<a href="{{$href_mock_poll}}" target="_blank"><b class="col text-right">View Details</b></a>
												</td>
												</tr>
											</tfoot>


										</table>
									</div>
								</div>
							</div>

						</div>
					</div>




					<div class="container-fluid ">
						<div class="row color-white">
							<div class="col">
								<div class="card ">
									<div class="card-header bg-white">
										<h2 class="col-md-8 float-left">Not Activated Officers</h2>



										<div class="input-group float-right col-md-4">
										  <input type="text" name="search_officer" id="search_officer" class="form-control" placeholder="Search by Mobile">
										  <div class="input-group-append">
										    <button class="btn btn-outline-secondary" id="search_by_mobile" type="button">Search</button>
										  </div>
										</div>


									</div>
									<div class="card-body p-0" id="not_assign_officer_div">
										<table align="center" class="table table-bordered poll-table mb-0" id="my-list-table">
											<thead>
												<tr>
													@if($role_id != 19)
													<th>State</th>
													<th>AC No & Name</th>
													@endif
													<th>PS No & Name</th>
													<th>Officer Name</th>
													<th>Officer Mobile</th>
													<th>Designation</th>
												</tr>
											</thead>

												@if(count($officers) > 0)
												<tbody>
												@foreach($officers as $iterate_officer)
												<tr>
													@if($role_id != 19)
													<td>{{$iterate_officer['st_name']}}</td>
													<td>{{$iterate_officer['ac_no'].'-'.$iterate_officer['ac_name']}}</td>
													@endif
													<td>{{$iterate_officer['ps_no'].'-'.$iterate_officer['ps_name']}}</td>
													<td>{{$iterate_officer['name']}}</td>
													<td>{{$iterate_officer['mobile']}}</td>
													<td>{{$iterate_officer['designation']}}</td>
												</tr>
												@endforeach
												</tbody>
												<tfoot>
											<tr>
													<td colspan="6" align="right">
														<a href="<?php echo $href_not_activate ?>" target="_blank">View More</a>
													</td>
												</tr>
												</tfoot>
												@else
												<tbody>
												<tr>
													<td colspan="6" align="center">
														All are activated
													</td>
												</tr>
											</tbody>

											@endif

										</table>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>




				<div class="tab-pane fade @if($active_tab == 'after') show active @endif " id="afterpoll" role="tabpanel" aria-labelledby="after-tab">



					<section class="statistics dashboard color-grey pt-2 pb-2" style="border-bottom:1px solid #eee;">
						<div class="container-fluid p-2 pr-5 pl-5">
						<div class="row d-flex">
							<div class="col">

							<div class="row mb-3">
								<div class="col">
									<div class="card p-0">

										<div class="card-body p-2" style="height:221px;">
											<div class="row">
												<div class="col-md-8">
													<h3 class="">Poll Started</h3>
													<h1 class="numberset lightblue" id="total_poll_started">{{$total_poll_started}}</h1>
													<div class="row">
														<div class="col">Total PS <span id="total_polling_booth">{{$total_polling_booth}}</span></div></div>
													</div>
													<div class="col-md-4 text-center mt-2 text-right" style="position: relative;">
														<div id="greencircle" data-percent="{{$poll_percent}}" class="small green">
														</div>
													</div>

											</div>
											<div class="card-footer p-0 " style="margin-top:20px;">

												<a class="btn  btn-blue btn-block btn-radius" href="{!! $href_poll_detail !!}">
													<div class="row">
														<div class="col text-left" style="color:#b6f3ff;">Total Poll End: <span id="total_poll_end">{{$total_poll_end}}</span></div>
														<b class="col text-right">View Details</b>
													</div></a>
											</div>

										</div>
									</div>
									</div>
										<div class="col pl-0">
											<div class="card p-0">
												<div class="card-header text-center p-2">
													<h3>Polling Station in Working Status</h3>
												</div>
												<div class="card-body p-0" style="height:187px;">
													<div class="row" style="margin-top:20px;">
														<div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
															<span class="upperCase">Connected<br />Status</span>
															<h1 class="numberset green" id="total_connected_status">{{$total_connected_status}}</h1>
														</div>

														<div class="col text-center pt-2 pb-2">
															<span class="upperCase">Disconnected<br />Status</span>
															<h1 class="numberset red" id="total_disconnected_status"><a href="disconnected-ps-report">{{$total_disconnected_status}}</a></h1>

														</div>

													</div></div></div></div>
													
													<div class="col pl-0" style="max-width: 10%;">
														<div class="card p-0">
														<div class="card-header text-center p-2">
														<h3>49(O)/</h3>
														<h3>Refused</h3>
														</div>
												<div class="card-body p-0" style="height:168px;">
													<div class="row" style="margin-top:20px;">
														<div class="col text-center pt-3 pb-3" style="border-right:1px solid #d5d5d5; min-height: 113px;">
															
															<h1 class="numberset green"><a href="form49-ps-report">{{$grand_49}}</a></h1>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="col pl-0" style="max-width: 10%;">
											<div class="card p-0">
													<div class="card-header text-center p-2">
													<h3>49(M)/</h3>
													<h3>Tendered</h3>
													</div>
												<div class="card-body p-0" style="height:168px;">
													<div class="row" style="margin-top:20px;">
														<div class="col text-center pt-3 pb-3" style="border-right:1px solid #d5d5d5; min-height: 113px;">
															
															<h1 class="numberset green"><a href="tendered-ps-report">{{$tendered_count}}</a></h1>
														</div>
													</div>
												</div>
										    </div>
							             </div>


											<!--<div class="col pl-0">
											 <div class="card p-0">
												<div class="card-header text-center p-2">
													<h3>Polling Station with Incident</h3>
												</div>
												<div class="card-body p-0">
													<div class="row">
														<div class="col text-center pt-2 pb-2" style="min-height: 113px;">
															<span class="upperCase">Total<br />Incident</span>
															<h1 class="numberset green"><a href="{{$incident_href}}" id="total_incident" target="_blank">{{$total_incident}}</a></h1>
														</div>
													</div>
												</div>
											 </div>
											</div> -->




											<!--<div class="col pl-0">
											 <div class="card p-0">
												<div class="card-header text-center p-2">
													<h3>Poll Material Status</h3>
												</div>
												<div class="card-body p-0">
													<div class="row">
														<div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
															<span class="upperCase">Received<br />Total</span>
															<h1 class="numberset green" id="total_received"><a href="{!! $href_poll_material !!}" target="_blank">{{$total_received}}</a></h1>
														</div>

														<div class="col text-center pt-2 pb-2">
															<span class="upperCase">Submitted<br />Total</span>
															<h1 class="numberset red" id="total_submited"><a href="{!! $href_poll_material !!}" target="_blank">{{$total_submited}}</a></h1>
														</div>
													</div>
												</div>
											</div>
											</div> -->
											<div class="col pl-0">
											<div class="card p-0  text-center">
												  <div class="card-header text-center p-2">
													<h3>Total Polling Percentage</h3>	</div>
													<div class="card-body p-0" style="min-height: 113px;">

												<table class="table mb-0 table-bordered tabspoint">
													<tr>
														<th align="left" style="text-align:left; padding-left:10px">Polling (%)</th>
														<th>Male</th>
														<th>Female</th>
														<th>Others</th>
														<th>Total</th>

													</tr>
													<tr>
														<th align="left" style="text-align:left; padding-left:10px">Total (%)</th>
														<td id="total_male_percentage">{{$total_male_percentage}}</td>
														<td id="total_female_percentage">{{$total_female_percentage}}</td>
														<td id="total_other_percentage">{{$total_other_percentage}}</td>
														<td id="total_total_percentage">{{$total_total_percentage}}</td>
													</tr>
													<tr>
														<th align="left" style="text-align:left; padding-left:10px">Total Electors</th>
														<td id="total_e_male">{{$grand_e_male}}</td>
														<td id="total_e_female">{{$grand_e_female}}</td>
														<td id="total_e_other">{{$grand_e_other}}</td>
														<td id="total_e_total">{{$grand_e_total}}</td>
													</tr>
													<tr>
														<th align="left" style="text-align:left; padding-left:10px">Total Voters</th>
														<td id="total_male">{{$total_male}}</td>
														<td id="total_female">{{$total_female}}</td>
														<td id="total_other">{{$total_other}}</td>
														<td id="total_total">{{$total_total}}</td>
													</tr>

												</table>


												</div>
												<!-- 	<div class="card-footer">2</div> -->
												</div>

												</div>


												</div>
<!--<div class="row"> --> <!-- Start -->
<!--<div class="col">
<div class="card p-0  text-center">
 <div class="card-header text-center p-2">
	<h3>Person with Disablity</h3>	</div>
	<div class="card-body p-0" style="min-height: 113px;">
<table class="table mb-0 table-bordered tabspoint">
	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Polling (%)</th>
		<th>Male</th>
		<th>Female</th>
		<th>Others</th>
		<th>Total</th>

	</tr>
	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Total (%)</th>
		<td id="pwd_male_percentage">{{$pwd_male_percentage}}</td>
		<td id="pwd_female_percentage">{{$pwd_female_percentage}}</td>
		<td id="pwd_other_percentage">{{$pwd_other_percentage}}</td>
		<td id="pwd_total_percentage">{{$pwd_total_percentage}}</td>
	</tr>

	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Total Electors</th>
		<td id="pwd_male">{{$pwd_e_male}}</td>
		<td id="pwd_female">{{$pwd_e_female}}</td>
		<td id="pwd_other">{{$pwd_e_other}}</td>
		<td id="pwd_total">{{$pwd_e_total}}</td>
	</tr>
	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Total Voters</th>
		<td id="pwd_e_male">{{$pwd_male}}</td>
		<td id="pwd_e_female">{{$pwd_female}}</td>
		<td id="pwd_e_other">{{$pwd_other}}</td>
		<td id="pwd_e_total">{{$pwd_total}}</td>
	</tr>

</table>


</div>  -->
<!-- 	<div class="card-footer">2</div> -->
<!--</div>

</div>
<div class="col pl-0">
<div class="card p-0  text-center">
  <div class="card-header text-center p-2">
	<h3>Total Polling Percentage</h3>	</div>
	<div class="card-body p-0" style="min-height: 113px;">

<table class="table mb-0 table-bordered tabspoint">
	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Polling (%)</th>
		<th>Male</th>
		<th>Female</th>
		<th>Others</th>
		<th>Total</th>

	</tr>
	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Total (%)</th>
		<td id="total_male_percentage">{{$total_male_percentage}}</td>
		<td id="total_female_percentage">{{$total_female_percentage}}</td>
		<td id="total_other_percentage">{{$total_other_percentage}}</td>
		<td id="total_total_percentage">{{$total_total_percentage}}</td>
	</tr>
	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Total Electors</th>
		<td id="total_e_male">{{$grand_e_male}}</td>
		<td id="total_e_female">{{$grand_e_female}}</td>
		<td id="total_e_other">{{$grand_e_other}}</td>
		<td id="total_e_total">{{$grand_e_total}}</td>
	</tr>
	<tr>
		<th align="left" style="text-align:left; padding-left:10px">Total Voters</th>
		<td id="total_male">{{$total_male}}</td>
		<td id="total_female">{{$total_female}}</td>
		<td id="total_other">{{$total_other}}</td>
		<td id="total_total">{{$total_total}}</td>
	</tr>

</table>


</div> -->
<!-- 	<div class="card-footer">2</div> -->
<!--</div>

</div>
</div> --> <!-- ends -->



</div>
						<!-- 	<div class="col-md-4 col-lg-4">
							<div class="card p-0" style="height:100%">
								<div class="map" id="map_in"></div>

							</div>


							</div> -->
						</div>
</div>
</section>

<section class="statistics  dashboard-header section-padding">
	<div class="container-fluid">
		<div class="row d-flex align-items-md-stretch">

			<div class="col-lg-6 col-md-6">
				<div class="card p-0">
					<div class="card-body">


						<div class="project-progress chart-pie" >

							<canvas id="doughnut_graph"  width="600" height="302"></canvas>


						</div>
					</div>
					<div class="card-footer">
						<table class="table  mb-0">
			<?php /*
			<tr>
				<td><b>Fastest Poll Happening PS</b><br /><span id="last_disc_ps_name">{{$last_disc_ps_name}}</span></td>
				<td><button type="button" class="btn btn-blue pull-right scan_history">Click here</button></td>
			</tr>
			*/?>

			<tr>
				<?php /* <td><b>Average Polling Time</b><br /><span id="average_timing">{{$average_timing}}</span></td>  */?>
				<td style="border:0px;" class="p-1 d-flex align-items-center"><b class="text-left col pl-0">Electoral Scan Data</b> <button type="button" class="btn btn-blue pull-right scan_history">Click here</button></td>

			</tr><tr>

			</tr>
		</table>

	</div>
</div>
</div>

<div class="col-lg-6 col-md-6">
	<div class="card p-0">
		<div class="card-body">
			<canvas id="bar-chart"></canvas>

		</div>
		<div class="card-footer">


			<table class="table mb-0">

				<tr align="center">
					<td style="border:0px;"><b>Age Profile of Voter Turnout</b></td>

				</tr><tr>

				</tr>
			</table>

		</div>

	</div>

</div>
<!-- Line Chart -->
</div>


<!-- lien and Bar chart for male female -->
<div class="row d-flex align-items-md-stretch" style="margin-top: 15px;">



	<div class="col-lg-6 col-md-6">
		<div class="card p-0">
			<div class="card-body">


				<div class="project-progress chart-pie" >

					<canvas id="voter-line-chart"  width="600" height="302"></canvas>
					

				</div>
			</div>
			<div class="card-footer">
				<table class="table mb-0">

					<tr align="center">
						<td style="border:0px;"><b>Half Hourly Voters Turnout</b></td>

					</tr><tr>

					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6">
		<div class="card p-0">
			<div class="card-body">
				<canvas id="bar-chart-male"></canvas>

			</div>
			<div class="card-footer">


				<table class="table mb-0">

					<tr align="center">
						<td style="border:0px;"><b>Graph By Gender</b></td>

					</tr><tr>

					</tr>
				</table>

			</div>
		</div>
	</div>
	<!-- Line Chart -->
</div>

<div class="row d-flex align-items-md-stretch" style="margin-top: 15px;display: none !important;" >
	<div class="col-lg-6 col-md-6">
		<div class="card p-0">
			<div class="card-body">


				<div class="project-progress chart-pie" >

					<canvas id="line-chart"  width="600" height="302"></canvas>


				</div>
			</div>
			<div class="card-footer">
				<table class="table mb-0">

					<tr align="center">
						<td style="border:0px;"><b>Half Hourly Cumulative Voter Turnout</b></td>

					</tr><tr>

					</tr>
				</table>
			</div>
		</div>
	</div>
</div>


</div>
@if(config('public_config.google_map_api'))

	<div class="container-fluid">
		<div class="card p-0">
			<div class="card-body">
				<div id="polling_station_reached" style="height: 500px;"></div>
			</div>
			<div class="card-footer">
				<table class="table mb-0">
					<tr align="center">
					<td style="border:0px;"><b>GIS Location of officers</b></td>
					</tr>

				</table>

			</div>
		</div>
	</div>
	@endif

	<div class="container-fluid mt-3" id="poll_event_dashboard">

	</div>
</section>

</div>


<div class="tab-pane fade @if($active_tab == 'pollday') show active @endif " id="resultpoll"  role="tabpanel" aria-labelledby="poll-tab">
	<section class="statistics  dashboard-header section-padding color-grey">
		<div class="container-fluid">
			<div class="card ">
				<div class="card-body">
					<div class="row">

						<div class="col"  style="padding: 7px; margin: -15px 0 0 0;" id="table_voter_turnout">


						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


</div>




</div>


<div class="modal fade animated zoomIn booth-app" id="booth-app" role="dialog" aria-hidden="false"> <!--  -->
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<h3 class="mb-0"></h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">

			</div>
<!--  <div class="modal-footer">

</div> -->

</div>
</div>
</div>


<div class="modal fade animated zoomIn" id="search_by_mobile_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Search Officer Details<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      	<div class="card-body p-0">
			<table align="center" class="table table-bordered poll-table mb-0" >
			<thead>
			<tr>
			<th>State</th>
			<th>AC No & Name</th>
			<th>PS No & Name</th>
			<th>Officer Name</th>
			<th>Officer Mobile</th>
			<th>Designation</th>
			<th>Activated</th>
			</tr>
			</thead>
			<tbody id="get_mobile_search_body">


			</tbody>
			</table>
		</div>
      </div>
    </div>
  </div>
</div>


@endsection

@section('script')

@if(config('public_config.google_map_api'))
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo config('public_config.google_map_api'); ?>&callback"></script>
<script>
$(document).ready(function(e){
    var map = new google.maps.Map(document.getElementById('polling_station_reached'), {
        fullscreenControl: false,
        zoom: 7,
        zoomControl: true,
        mapTypeControlOptions: {
      		mapTypeIds: [google.maps.MapTypeId.ROADMAP]
    	}
    });

    var geocoder = new google.maps.Geocoder();
	var location = "Jharkhand";
	geocoder.geocode( { 'address': location }, function(results, status) {
	    if (status == google.maps.GeocoderStatus.OK) {
	        map.setCenter(results[0].geometry.location);
	    } else {
	        alert("Could not find location: " + location);
	    }
	});

	var activeInfoWindow = null;
    $.each(<?php echo $allpsdata; ?>, function (index, value) {

        var image = {
        	url: "{{url('theme/img/map-marker-red.png')}}", // url
        	scaledSize: new google.maps.Size(35, 52), // scaled size
        };

        var data = {lat: parseFloat(value.lat), lng: parseFloat(value.lng)};
        var marker = new google.maps.Marker({
            position: data,
            map: map,
            title: value.ps_name,
            label: {
                color: "#000",
                fontSize: "9px",
                width: "40px",
                top: "5px",
                position: "absolute",
                fontWeight: "bold",
                class: "marker"
            },
            icon: image
        });

        var contentString = "<div id='map_popup'>";
        contentString += "<div id='siteNotice'></div>";
        contentString += "<h3 class='map_popup_title'>"+value.designation+"</h1>";
        contentString += "<div class='map_popup_body'>";
        contentString += "<p>Name: "+value.name+"</p>";
        contentString += "<p>Mobile: "+value.mobile+"</p>";
        contentString += "<p>State: "+value.st_name+"</p>";
        contentString += "<p>AC No & Name: "+value.ac_no_name+"</p>";
        contentString += "<p>PS No & Name: "+value.ps_no_name+"</p>";
        contentString += "</div>";
        contentString += "</div>";
        contentString += "</div>";

        var infowindow = new google.maps.InfoWindow({
          content: contentString
        });

        marker.addListener('click', function() {
        	if (activeInfoWindow) {
        		activeInfoWindow.close();
        	}
	        infowindow.open(map, marker);
	        activeInfoWindow = infowindow;
        });

    });
});
</script>
@endif




<script type="text/javascript">
function poll_event_dashboard(){
	$.ajax({
		url: "{!! $poll_event_dashboard !!}",
		type: 'GET',
		dataType: 'html',
		beforeSend: function() {
			var src = "<?php echo url('admintheme/images/loader.gif') ?>";
			var loading_image = "<div class='text-center remove_poll_event_dashboard'>";
			loading_image += "<h2>Loading Poll Event Data...</h2>";
			loading_image += "<img src='"+src+"' width='50px'>";
			loading_image += "</div>";
			$('#poll_event_dashboard').html(loading_image);
		},
		complete: function() {
			$('.remove_poll_event_dashboard').remove();
		},
		success: function(json) {
			$('#poll_event_dashboard').html(json);
		},
		error: function(data) {
			$('.remove_poll_event_dashboard').remove();
		}
	});
}
	function reload_voter_turnout(){
		$.ajax({
			url: "{!! $get_voter_turnout !!}",
			type: 'GET',
			dataType: 'html',
			beforeSend: function() {
				var src = "<?php echo url('admintheme/images/loader.gif') ?>";
				var loading_image = "<div class='text-center'>";
				loading_image += "<h2>Please wait...</h2>";
				loading_image += "<img src='"+src+"' width='50px'>";
				loading_image += "</div>";
				$('#table_voter_turnout').html(loading_image);
			},
			complete: function() {
				$('.loading_spinner').remove();
			},
			success: function(json) {
				$('#table_voter_turnout').html(json);
			},
			error: function(data) {
				var errors = data.responseJSON;
			}
		});
	}
	$(document).ready(function(e){

		reload_voter_turnout();

		<?php if($role_id == '7'){ ?>
			poll_event_dashboard();
		<?php } ?>

		$('#before-tab').click(function(e){
			$('#tab').val('before');
			window.history.pushState("data","Title","?tab=before&<?php echo $request_string ?>");
			e.preventDefault();
		});
		$('#after-tab').click(function(e){
			$('#tab').val('after');
			window.history.pushState("data","Title","?tab=after&<?php echo $request_string ?>");
			e.preventDefault();
		});
		$('#poll-tab').click(function(e){
			$('#tab').val('pollday');
			window.history.pushState("data","Title","?tab=pollday&<?php echo $request_string ?>");
			e.preventDefault();
		});


		setInterval(function(){
			$('#show').click();
		},300000);

		$('.scan_history').click(function(e){
			$.ajax({
				url: "{!! $scan_page_url !!}",
				type: 'GET',
				data: 'is_ajax=1',
				dataType: 'html',
				beforeSend: function() {

				},
				complete: function() {
					$('.loading_spinner').remove();
				},
				success: function(json) {
					$('#booth-app .modal-header h3').text('Scan Data');
					$('#booth-app .modal-body').html(json);
					$('#booth-app').modal();
				},
				error: function(data) {
					var errors = data.responseJSON;
				}
			});
		});
		$('#show').click(function(e){
			$.ajax({
				url: "{!! $referesh_page_url !!}",
				type: 'GET',
				data: 'is_ajax=1',
				dataType: 'json',
				beforeSend: function() {
					/*$('.loader_bottom').removeClass('display_none');*/
					$('#show').prop('disabled', true);
				},
				complete: function() {
					$('#show').prop('disabled', false);
				},
				success: function(json) {
					/*before poll*/
					$('#total_not_assign_officers').text(json.total_not_assign_officers);
					$('#total_polling_booth').text(json.total_polling_booth);
					$('#total_pro_assign').text(json.total_pro_assign);
					$('#total_pro_not_activated').text(json.total_pro_not_activated);
					$('#total_pro_assign').text(json.total_pro_assign);
					$('#total_blo_not_activated').text(json.total_blo_not_activated);
					$('#total_e_download').text(json.total_e_download);
					$('#total_incident').text(json.total_incident);
					$('#total_so_not_activated').text(json.total_so_not_activated);
					$('#total_so_assign').text(json.total_so_assign);
					$('#total_po_assign').text(json.total_po_assign);
					$('#total_po_not_activated').text(json.total_po_not_activated);
					$('#infra_start').text(json.infra_start);
					$('#infra_ramp').text(json.infra_ramp);
					$('#infra_toilet_facility').text(json.infra_toilet_facility);
					$('#infra_exit_door').text(json.infra_exit_door);
					$('#infra_furniture').text(json.infra_furniture);
					$('#infra_light').text(json.infra_light);
					$('#infra_drinking_water').text(json.infra_drinking_water);
					$('#mock_poll_start').text(json.mock_poll_start);
					$('#mock_poll_result_shown').text(json.mock_poll_result_shown);
					$('#mock_button_clear').text(json.mock_button_clear);
					$('#mock_slip_remove').text(json.mock_slip_remove);

					var not_activated = '';
					not_activated += "<table align='center' class='table table-bordered poll-table mb-0' id='my-list-table'>";
					not_activated += "<thead>";
					not_activated += "<tr>";
					<?php if($role_id != 19){ ?>
						not_activated += "<th>State</th>";
						not_activated += "<th>AC No & Name</th>";
					<?php } ?>
					not_activated += "<th>PS No & Name</th>";
					not_activated += "<th>Officer Name</th>";
					not_activated += "<th>Officer Mobile</th>";
					not_activated += "<th>Designation</th>";
					not_activated += "</tr>";
					not_activated += "</thead>";
					not_activated += "<tbody>";
					not_activated += "<tbody>";
					$.each(json.officers, function(index,object){
						not_activated += "<tr>";
						<?php if($role_id != 19){ ?>
							not_activated += "<td>"+object.st_name+"</td>";
							not_activated += "<td>"+object.ac_no + '-' + object.ac_name+"</td>";
						<?php } ?>
						not_activated += "<td>"+object.ps_no + '-' + object.ps_name+"</td>";
						not_activated += "<td>"+object.name+"</td>";
						not_activated += "<td>"+object.mobile+"</td>";
						not_activated += "<td>"+object.designation+"</td>";
						not_activated += "</tr>";
					});
					not_activated += "</tbody>";


					not_activated += "<tfoot>";
					not_activated += "<tr><td colspan='6' align='right'><a href='<?php echo $href_not_activate; ?>'  target='_blank'>View More</a></td></tr>";
					not_activated += "</foot>";

					not_activated += "</table>";
					$('#not_assign_officer_div').html(not_activated);


					/*poll day*/
					$('#total_received').html("<a href='"+json.href_poll_material+"' target='_blank'>"+json.total_received+"</a>");
					$('#total_submited').html("<a href='"+json.href_poll_material+"' target='_blank'>"+json.total_submited+"</a>");
					$('#total_polling_location').html("<a href='"+json.href_mapped_location+"' target='_blank'>"+json.total_polling_location+"</a>");
					$('#total_unmapped_location').html("<a href='"+json.href_mapped_location+"' target='_blank'>"+json.total_unmapped_location+"</a>");
					$('#total_mapped_location').html("<a href='"+json.href_mapped_location+"' target='_blank'>"+json.total_mapped_location+"</a>");



					$('#total_poll_started').text(json.total_poll_started);
					$('#total_poll_end').text(json.total_poll_end);
					$('#poll_percent').text(json.poll_percent+'%');
					$('#poll_percent_for_css').css('transform','rotate('+json.poll_percent_for_css+')');
					$('#total_connected_status').text(json.total_connected_status);
					$('#total_disconnected_status').text(json.total_disconnected_status);
					if(json.last_disc_ps_name == ''){
						$('#last_disc_ps_name').html("<i class='fa fa-check checked'></i><br />All Polling Stations are Connected");
					}else{
						$('#last_disc_ps_name').text(json.last_disc_ps_name);
					}
					/*donght here*/
					$('#average_timing').text(json.average_timing);

					$('#total_male_percentage').text(json.total_male_percentage);
					$('#total_female_percentage').text(json.total_female_percentage);
					$('#total_other_percentage').text(json.total_other_percentage);
					$('#total_total_percentage').text(json.total_total_percentage);

					$('#total_male').text(json.total_male);
					$('#total_female').text(json.total_female);
					$('#total_other').text(json.total_other);
					$('#total_total').text(json.total_total);

					/*PWD*/
					$('#pwd_male_percentage').text(json.pwd_male_percentage);
					$('#pwd_female_percentage').text(json.pwd_female_percentage);
					$('#pwd_other_percentage').text(json.pwd_other_percentage);
					$('#pwd_total_percentage').text(json.pwd_total_percentage);
					$('#pwd_male').text(json.pwd_male);
					$('#pwd_female').text(json.pwd_female);
					$('#pwd_other').text(json.pwd_other);
					$('#pwd_total').text(json.pwd_total);

					$('#pwd_e_male').text(json.pwd_e_male);
					$('#pwd_e_female').text(json.pwd_e_female);
					$('#pwd_e_other').text(json.pwd_e_other);
					$('#pwd_e_total').text(json.pwd_e_total);


					/* line cahrt update */
					<?php if(isset($is_time_slot)){ ?>
						var time_slot_label_for_line = [];
						$.each(JSON.parse(json.time_slot_label_for_line),function(index,object){
							time_slot_label_for_line.push(object);
						});
						var time_slot_data_for_line = [];
						$.each(JSON.parse(json.time_slot_data_for_line),function(index,object){
							time_slot_data_for_line.push(object);
						});
						stacked_line.data.labels = time_slot_label_for_line;
						stacked_line.data.datasets[0].data = time_slot_data_for_line;
						stacked_line.update();
					<?php } ?>


					load_data();
					reload_voter_turnout();
					poll_event_dashboard();

					$('#poll_turnout_percentage').text(json.poll_turnout_percentage);

					var filter_poll_percentage ="<table class='table  mb-0' style='background: #49a8a4;  border-radius: 60px;'>";
					filter_poll_percentage +="<tr class=''>";
					filter_poll_percentage +="<td style='border: none;  font-size: 18px;  color: #fff;  vertical-align: middle;    padding-left: 20px;'>Poll day Turn out Details</td>";
					filter_poll_percentage +="<td  style='border: 0px; font-size: 33px; color: #fff;     padding-right: 20px;' align='right'><span id='header_poll_turnout_percentage'>"+json.poll_turnout_percentage+"</span>%</td>";
					filter_poll_percentage +="</tr>";
					filter_poll_percentage +="</table>";
					$('#filter_poll_percentage').html(filter_poll_percentage);



					$('#show').prop('disabled', false);

				},
				error: function(data) {
					var errors = data.responseJSON;
					$('#show').prop('disabled', false);

				}
			});
});
});


function doughnut_config(gridlines, title, data_array) {
	return {
		type: 'pie',
		data: {
			labels: ['QR','Epic Number', 'Serial Number', 'Name'],
			datasets: [{
				backgroundColor: ['#EC6B56','#ffce57','#33b45a','#44a6e7'],
				data: data_array,
				fill: true
			}]
		},
		options: {
			responsive: true,
			plugins: {
				labels: {
					render: 'value',
				}
			},
			title: {
				display: true,
				text: title
			},
			circumference: 2*Math.PI,
			cutoutPercentage: 30,
			legend: {
				position: 'right'
			},
		},
	};
}



/* doughnut graph */
var data_array = [];
$.each({!!$doughnut_data!!}, function(index,object){
	data_array.push(object);
});
console.log(data_array)
var ctx = document.getElementById('doughnut_graph').getContext('2d');
var config = doughnut_config({
	display: true
}, 'Electoral Scan Percentage', data_array);
var doughnut_graph = new Chart(ctx, config);

/* bar chart for male female */

var gender_label_for_bar = [];
$.each({!!$gender_label_for_bar!!}, function(index,object){
	gender_label_for_bar.push(object);
});

var gender_data_for_bar = [];
$.each({!!$gender_data_for_bar!!}, function(index,object){
	gender_data_for_bar.push(object);
});

var ctx = document.getElementById("bar-chart-male");
var gender_chart = new Chart(ctx, {
	type: 'bar',
	data: {
		labels: gender_label_for_bar,
		datasets: [{
			label: '',
			data: gender_data_for_bar,
			backgroundColor: [
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			],
			borderColor: [
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			],
			borderWidth: 1
		}]
	},
	options: {
		layout: {
			padding: {
				top: 40,
			}
		},
		legend: {
			display: false
		},
		responsive: true,
		plugins: {
			labels: {
// render 'label', 'value', 'percentage', 'image' or custom function, default is 'percentage'
render: function (args) {
	if(args.value != 0)
		return args.value;
},
fontColor: '#000',
position: 'border'
}
},
scales: {
	xAxes: [{
		ticks: {
			maxRotation: 0,
			minRotation: 0,
			callback: function(value) {
				return value
			}
		}
	}],
	yAxes: [{
		ticks: {
			maxRotation: 0,
			minRotation: 0,
			beginAtZero: true,
			callback: function(value) {
				return value
			}
		}
	}]
}
}
});


/*bar chart*/
var age_gap = [];
$.each({!!$age_gap!!}, function(index,object){
	age_gap.push(object);
});
var bar_graph = [];
$.each({!!$bar_graph!!}, function(index,object){
	bar_graph.push(object);
});

var ctx = document.getElementById("bar-chart");
var bar_chart = new Chart(ctx, {
	type: 'bar',
	data: {
		labels: age_gap,
		datasets: [{
			label: '',
			data: bar_graph,
			backgroundColor: [
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)',
			'rgba(153, 102, 255, 0.2)',
			'rgba(255, 159, 64, 0.2)',
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)'
			],
			borderColor: [
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)',
			'rgba(153, 102, 255, 1)',
			'rgba(255, 159, 64, 1)',
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)'
			],
			borderWidth: 1
		}]
	},
	options: {
		layout: {
			padding: {
				top: 40,
			}
		},
		legend: {
			display: false
		},
		responsive: true,
		plugins: {
			labels: {
// render 'label', 'value', 'percentage', 'image' or custom function, default is 'percentage'
render: function (args) {
	if(args.value != 0)
		return args.value;
},
fontColor: '#000',
position: 'border'
}
},
scales: {
	xAxes: [{
		ticks: {
			maxRotation: 0,
			minRotation: 0,
			callback: function(value) {
				return value
			}
		}
	}],
	yAxes: [{
		ticks: {
			maxRotation: 0,
			minRotation: 0,
			beginAtZero: true,
			callback: function(value) {
				return value
			}
		}
	}]
}
}
});


<?php if(isset($is_time_slot)){ ?>
/* Line chart */
var time_slot_label_for_line = [];
$.each({!!$time_slot_label_for_line!!}, function(index,object){
	time_slot_label_for_line.push(object);
});
var time_slot_data_for_line = [];
$.each({!!$time_slot_data_for_line!!}, function(index,object){
	time_slot_data_for_line.push(object);
});

var ctx = document.getElementById("line-chart");
var stacked_line = new Chart(ctx, {
	type: 'line',
	data: {
		labels: time_slot_label_for_line,
		datasets: [{
			label: '',
			data: time_slot_data_for_line,
		}]
	},
	options: {
		legend: {
			display: false
		},
		scaleBeginAtZero: true,
		scales: {
			yAxes: [{
				min:0,
				stacked: true,
			}],
			ticks: {
				beginAtZero:true
			}
		}
	}
});
<?php } ?>


/* get voter data total */
var voters_label_by_time = [];
$.each({!!$voters_label_by_time!!}, function(index,object){
	voters_label_by_time.push(object);
});
var voters_data_by_time = [];
$.each({!!$voters_data_by_time!!}, function(index,object){
	voters_data_by_time.push(object);
});

var ctx = document.getElementById("voter-line-chart");
var voter_time_graph = new Chart(ctx, {
	type: 'line',
	data: {
		labels: voters_label_by_time,
		datasets: [{
			label: '',
			data: voters_data_by_time,
		}]
	},
	options: {
		legend: {
			display: false
		},
		scaleBeginAtZero: true,
		scales: {
			yAxes: [{
		       scaleLabel: {
		           display: true,
		           labelString: "Voters"
		       }
		   }]
		}
	}
});


/* Line chart */
var cumulative_label_for_line = [];
$.each({!!$cumulative_label_for_line!!}, function(index,object){
	cumulative_label_for_line.push(object);
});
var cumulative_data_for_line = [];
$.each({!!$cumulative_data_for_line!!}, function(index,object){
	cumulative_data_for_line.push(object);
});

var ctx = document.getElementById("line-chart");
var stacked_line = new Chart(ctx, {
	type: 'line',
	data: {
		labels: cumulative_label_for_line,
		datasets: [{
			label: '',
			data: cumulative_data_for_line,
		}]
	},
	options: {
		legend: {
			display: false
		},
		scaleBeginAtZero: true,
		scales: {
			yAxes: [{
		       ticks: {
		           min: 0,
		           max: 100,
		           callback: function(value) {
		               return value + "%"
		           }
		       },
		       scaleLabel: {
		           display: true,
		           labelString: "Percentage"
		       }
		   }]
		}
	}
});

</script>
<script type="text/javascript">
	var $dOut = $('#date'),
	$hOut = $('#hours'),
	$mOut = $('#minutes'),
	$sOut = $('#seconds'),
	$ampmOut = $('#ampm');
	var months = [
	'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
	];

	var days = [
	'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
	];

	function update(){
		var date = new Date();

		var ampm = date.getHours() < 12
		? 'AM'
		: 'PM';

		var hours = date.getHours() == 0
		? 12
		: date.getHours() > 12
		? date.getHours() - 12
		: date.getHours();

		var minutes = date.getMinutes() < 10
		? '0' + date.getMinutes()
		: date.getMinutes();

		var seconds = date.getSeconds() < 10
		? '0' + date.getSeconds()
		: date.getSeconds();

		var dayOfWeek = days[date.getDay()];
		var month = months[date.getMonth()];
		var day = date.getDate();
		var year = date.getFullYear();

		var dateString = month + ' ' + day + ', ' + year;

		$dOut.text(dateString);
		$hOut.text(hours);
		$mOut.text(minutes);
		$sOut.text(seconds);
		$ampmOut.text(ampm);
	}

	update();
	window.setInterval(update, 1000);
</script>
<script type="text/javascript">
	$("#greencircle").percircle();
</script>
<script type="text/javascript">
	$(document).ready(function () {
		load_data();
		var filter_poll_percentage ="<table class='table  mb-0' style='background: #49a8a4;  border-radius: 60px;'>";
		filter_poll_percentage +="<tr class=''>";
		filter_poll_percentage +="<td style='border: none;  font-size: 18px;  color: #fff;  vertical-align: middle;    padding-left: 20px;'>Poll day Turn out Details</td>";
		filter_poll_percentage +="<td  style='border: 0px; font-size: 33px; color: #fff;     padding-right: 20px;' align='right'><span id='header_poll_turnout_percentage'><?php echo $poll_turnout_percentage; ?></span>%</td>";
		filter_poll_percentage +="</tr>";
		filter_poll_percentage +="</table>";
		$('#filter_poll_percentage').html(filter_poll_percentage);
	});

	function bar_age_graph(){
		$.ajax({
			url: "{!! $referesh_age_graph !!}",
			type: 'GET',
			data: 'is_ajax=1',
			dataType: 'json',
			beforeSend: function() {
				/*$('.loader_bottom').removeClass('display_none');*/
				$('#show').prop('disabled', true);
			},
			complete: function() {
				$('#show').prop('disabled', false);
			},
			success: function(json) {
			/*bar chart*/
				var bar_chart_array = [];
				$.each(JSON.parse(json.bar_graph),function(index,object){
					bar_chart_array.push(object);
				});
				bar_chart.data.datasets[0].data = bar_chart_array;
				bar_chart.update();
			},
			error: function(data) {
				var errors = data.responseJSON;
			}
		});
	}

	function get_cumulative_time_data(){
		$.ajax({
			url: "{!! $get_cumulative_time_data !!}",
			type: 'GET',
			data: 'is_ajax=1',
			dataType: 'json',
			beforeSend: function() {
				/*$('.loader_bottom').removeClass('display_none');*/
				$('#show').prop('disabled', true);
			},
			complete: function() {
				$('#show').prop('disabled', false);
			},
			success: function(json) {
				var cumulative_label_for_line = [];
				$.each(JSON.parse(json.cumulative_label_for_line),function(index,object){
					cumulative_label_for_line.push(object);
				});
				var cumulative_data_for_line = [];
				$.each(JSON.parse(json.cumulative_data_for_line),function(index,object){
					cumulative_data_for_line.push(object);
				});
				stacked_line.data.labels = cumulative_label_for_line;
				stacked_line.data.datasets[0].data = cumulative_data_for_line;
				stacked_line.update();
			},
			error: function(data) {
				var errors = data.responseJSON;
			}
		});
	}

	function get_scan_data(){
		$.ajax({
			url: "{!! $get_doughnut_data !!}",
			type: 'GET',
			data: 'is_ajax=1',
			dataType: 'json',
			beforeSend: function() {
				$('#show').prop('disabled', true);
			},
			complete: function() {
				$('#show').prop('disabled', false);
			},
			success: function(json) {
				/* Doughnut Graph */
				var doughnut_array = [];
				$.each(JSON.parse(json.doughnut_data),function(index,object){
					doughnut_array.push(object);
				});
				doughnut_graph.data.datasets[0].data = doughnut_array;
				doughnut_graph.update();
			},
			error: function(data) {
				var errors = data.responseJSON;
			}
		});
	}

	function get_gender_data(){
		$.ajax({
			url: "{!! $get_gender_data !!}",
			type: 'GET',
			data: 'is_ajax=1',
			dataType: 'json',
			beforeSend: function() {
				$('#show').prop('disabled', true);
			},
			complete: function() {
				$('#show').prop('disabled', false);
			},
			success: function(json) {
				/* bar chart for gender */
				var gender_data_for_bar = [];
				$.each(JSON.parse(json.gender_data_for_bar),function(index,object){
					gender_data_for_bar.push(object);
				});
				gender_chart.data.datasets[0].data = gender_data_for_bar;
				gender_chart.update();
			},
			error: function(data) {
				var errors = data.responseJSON;
			}
		});
	}

	function get_voters_by_time(){
		$.ajax({
			url: "{!! $get_voters_by_time !!}",
			type: 'GET',
			data: 'is_ajax=1',
			dataType: 'json',
			beforeSend: function() {
				$('#show').prop('disabled', true);
			},
			complete: function() {
				$('#show').prop('disabled', false);
			},
			success: function(json) {
				/* bar chart for gender */
				var cumulative_label_for_line = [];
				$.each(JSON.parse(json.cumulative_label_for_line),function(index,object){
					cumulative_label_for_line.push(object);
				});
				var cumulative_data_for_line = [];
				$.each(JSON.parse(json.cumulative_data_for_line),function(index,object){
					cumulative_data_for_line.push(object);
				});
				voter_time_graph.data.labels = cumulative_label_for_line;
				voter_time_graph.data.datasets[0].data = cumulative_data_for_line;
				voter_time_graph.update();
			},
			error: function(data) {
				var errors = data.responseJSON;
			}
		});
	}

	function load_data(){
		bar_age_graph();
		get_cumulative_time_data();
		get_scan_data();
		get_gender_data();
		get_voters_by_time();
	}

	$(document).ready(function(e){
		$("#search_by_mobile").click(function(e){
			$.ajax({
				url: "{!! Common::generate_url('booth-app-revamp/search_officer') !!}",
				type: 'GET',
				data: 'mobile='+$("#search_officer").val(),
				dataType: 'json',
				beforeSend: function() {
					$('#search_by_mobile').prop('disabled', true);
				},
				complete: function() {
					$('#search_by_mobile').prop('disabled', false);
				},
				success: function(json) {

					if(json['success'] == false){
			            error_messages(json['warning']);
			        }else{
						html = "";
						html += "<tr>";
						html += "<td>"+json['st_name']+"</td>";
						html += "<td>"+json.ac_no+' '+json.ac_name+"</td>";
						html += "<td>"+json.ps_no+' '+json.ps_name+"</td>";
						html += "<td>"+json.name+"</td>";
						html += "<td>"+json.mobile+"</td>";
						html += "<td>"+json.designation+"</td>";
						html += "<td>"+json.is_login+"</td>";
						html += "</tr>";
						$('#get_mobile_search_body').html(html);
						$('#search_by_mobile_modal').modal('show');
					}
				},
				error: function(data) {
					var errors = data.responseJSON;
				}
			});
		});
	});

</script>
@endsection
