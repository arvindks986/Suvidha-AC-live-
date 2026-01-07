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
		font-size: 13px; width: 105px;
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
     
</style>


<section class="statistics dashboard color-grey pt-2 pb-2" style="border-bottom:1px solid #eee;">
	<div class="container-fluid">
		<div class="row d-flex align-items-center">
			@if($role_id==7)
			@endif
			<h4 class="page-title mr-auto">Poll Turnout - Booth App</h4>
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

			@if(Auth::user() && in_array(Auth::user()->role_id,[4,5,7]))
			@include('admin/common/form-filter')
			@endif
			<div class="tab-pane fade @if($active_tab == 'before') show active @endif" id="beforepoll" role="tabpanel" aria-labelledby="before-tab">
			

				<section class="statistics dashboard color-grey pt-2 pb-5 mb-5" style="border-bottom:1px solid #eee;">
					<div class="container-fluid">
						<div class="row d-flex mt-5">


							<div class="col">
								<!-- Income-->
								<div class="card income cardBox text-center">

									<div class="card-body p-2">

										<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>

										<div class="number red">
											<a  id="total_not_assign_officers">{{$total_not_assign_officers}}</a>
											<p><strong class="text-primary">Officers</strong>Not assigned</p>
										</div>

									</div>


									<div class="card-footer p-2">
										<div class="row d-flex align-items-center">
											<p class="col text-left">No. of Polling Stations</p> <span class="text-primary mr-auto pr-3"><a href="{!! $href_polling_station !!}" id="total_polling_booth">{{$total_polling_booth}}</a></span>
										</div>
									</div>

								</div>
							</div>


							<div class="col">
								<!-- Income-->
								<div class="card income cardBox text-center">
									<div class="card-body p-2">
										<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>

										<div class="number red">
											<a href="{!! $href_pro_activate !!}" id="total_pro_not_activated">{{$total_pro_not_activated}}</a>
											<p><strong class="text-primary">Polling Party</strong>Not activated</p>
										</div></div>
										<div class="card-footer p-2">
											<div class="row d-flex align-items-center">
												<p class="col text-left">Total Polling Party</p> <span class="text-primary mr-auto pr-3"><a href="{!! $href_pro_officer !!}" id="total_pro_assign">{{$total_pro_assign}}</a></span>
											</div>
										</div>

									</div>
								</div>


								<div class="col">
									<!-- Income-->
									<div class="card income cardBox text-center">
										<div class="card-body p-2">
											<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>
											<div class="number red">
												<a href="{!! $href_blo_activate !!}" id="total_blo_not_activated">{{$total_blo_not_activated}}</a>
												<p><strong class="text-primary">BLO</strong>Not activated</p>
											</div></div>
											<div class="card-footer p-2">
												<div class="row d-flex align-items-center">
													<p class="col text-left">Total BLO</p> <span class="text-primary mr-auto pr-3"><a href="{!! $href_blo_officer !!}" id="total_pro_assign">{{$total_blo_assign}}</a></span>
												</div>
											</div>
										</div>
									</div>



									<div class="col">
										<!-- Income-->
										<div class="card income cardBox text-center">
											<div class="card-body">
												<div class="icon"><img src="{{ asset('theme/images/app-download-checked.png') }}" alt="" /></div>
												<div class="number green" style="min-height: 76px;"><a href="{!! $href_e_download !!}" id="total_e_download">
													{{$total_e_download}}
												</a>
											</div>
											<p><strong class="text-primary">E Roll download </strong>confirmed</p>
										</div>



									</div>
								</div> 

							</div>



						</div>
					</section>

					<!-- end col -->


					<div class="container-fluid ">
						<div class="row color-white">
							<div class="col">
								<div class="card ">
									<div class="card-header color-grey">
										<h2 class="">Not Activated Officers</h2>
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
											<tbody>
												@if(count($officers) > 0)
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

												@else
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

										<div class="card-body p-2">
											<div class="row">
												<div class="col-md-9">
													<h3 class="">Poll Started</h3>
													<h1 class="numberset lightblue" id="total_poll_started">{{$total_poll_started}}</h1>			
													<div class="row">				
														<div class="col">Total Poll <span id="total_polling_booth">{{$total_polling_booth}}</span></div></div>
													</div>
													<div class="col-md-3 text-center mt-2 text-right" style="position: relative;">
														<div id="greencircle" data-percent="{{$poll_percent}}" class="small green">
														</div>
													</div>

												</div>
												<div class="card-footer p-0 ">		

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
												<div class="card-body p-0">
													<div class="row">
														<div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
															<span class="upperCase">Connected<br />Status</span>
															<h1 class="numberset green" id="total_connected_status">{{$total_connected_status}}</h1>					
														</div>

														<div class="col text-center pt-2 pb-2">
															<span class="upperCase">Disconnected<br />Status</span>
															<h1 class="numberset red" id="total_disconnected_status">{{$total_disconnected_status}}</h1>

														</div>

													</div></div></div></div>
													<div class="col pl-0">
														<div class="card p-0  text-center">
 <!-- <div class="card-header text-center p-2">
	<h3>Total Polling Percentage</h3>	</div>  -->
	<div class="card-body p-0" style="min-height: 113px;">
<!-- 		<table class="table mb-0 table-bordered tabspoint">
<tr>
	<th>MALE</th>
	<th>FEMALE</th>
	<th>OTHER</th>
	<th>TOTAL</th>
</tr>
<tr>
	<td id="total_male_percentage">{{$total_male_percentage}}</td>
	<td id="total_female_percentage">{{$total_female_percentage}}</td>
	<td id="total_other_percentage">{{$total_other_percentage}}</td>
	<td id="total_total_percentage">{{$total_total_percentage}}</td>	
</tr>
<tr>
	<td>Total Voter</td>
	<td>123</td>
	<td>123</td>
	<td>123</td>
	
</tr>
</table> -->

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
					<td style="border:0px;"><b>Average Polling By Age</b></td> 

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

					<canvas id="line-chart"  width="600" height="302"></canvas>


				</div>
			</div>
			<div class="card-footer">							
				<table class="table mb-0">																

					<tr align="center">
						<td style="border:0px;"><b>Electoral Chart</b></td> 

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

</div>



</section>


<div class="container-fluid ">
						<div class="row color-white">
							<div class="col">
								<div class="card ">
									<div class="card-header color-grey">
										<h2 class="">Not Activated Officers</h2>
									</div>
									<div class="card-body p-0">
										<table align="center" class="table table-bordered poll-table mb-0">
											<thead>
           <tr>
             <th rowspan="2">State/UT Name</th>
             <th rowspan="2">AC NO & AC Name</th>
             <th rowspan="2">Total PS</th>
             <th colspan="2">Poll Party Reached</th>
             <th colspan="2">Mock Poll Done</th>
             <th colspan="2">Poll Started</th>
             <th colspan="2">Voting Started</th>
             <th colspan="2">Final Data Sync</th>
             <th colspan="2">Poll End</th>
           </tr>
            
            <tr>
             <th>Yes</th>
             <th>No</th>
              <th>Yes</th>
             <th>No</th>
              <th>Yes</th>
             <th>No</th>
              <th>Yes</th>
             <th>No</th>
              <th>Yes</th>
             <th>No</th>
              <th>Yes</th>
             <th>No</th>
           </tr>
         </thead>
											<tbody  id="polling_event_results">
												@if(count($polling_event_results) > 0)
												@foreach($polling_event_results as $key => $result)
           <tr>
             <td>{{$result['st_name']}}</td>
             <td>{{$result['ac_no']}}-{{$result['ac_name']}}</td>
             <td>{{$result['total_ps']}}</td>
             <td>{{$result['ps_location']}}</td>
             <td>{{($result['total_ps'] -$result['ps_location'])}}</td>
             <td>{{$result['mock_poll_start']}}</td>
             <td>{{$result['total_ps']-$result['mock_poll_start']}}</td>
             <td>{{$result['poll_start']}}</td>
              <td>{{$result['total_ps']-$result['poll_start']}}</td>
             <td>{{$result['total_voter']}}</td>
              <td>{{$result['total_ps']-$result['total_voter']}}</td>
             <td>{{$result['data_sync']}}</td>
              <td>{{$result['total_ps']-$result['data_sync']}}</td>
             <td>{{$result['poll_end']}}</td>
              <td>{{$result['total_ps']-$result['poll_end']}}</td>
           </tr>
@endforeach

												@else
												<tr>
													<td colspan="15" align="center">
														No Record Found
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


<div class="tab-pane fade" id="resultpoll" role="tabpanel" aria-labelledby="poll-tab">
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


@endsection

@section('script')
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4mlo-kY0vyBDIdeXffR2igqE5igx3piE&callback"></script> -->
<script>
// $(function(){
//	var map = new google.maps.Map(document.getElementById('map_in'), {
//		center:new google.maps.LatLng(51.508742,-0.120850),
 //          fullscreenControl: true,
//		  zoom: 5,
//           zoomControl: true,
 //      });
//});
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

		$('#before-tab').click(function(e){
			window.history.pushState("data","Title","?tab=before&<?php echo $request_string ?>");
			e.preventDefault();
		});
		$('#after-tab').click(function(e){
			window.history.pushState("data","Title","?tab=after&<?php echo $request_string ?>");
			e.preventDefault();
		});
		$('#poll-tab').click(function(e){
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
					not_activated += "</table>";
					$('#not_assign_officer_div').html(not_activated);
					if(not_activated){
						load_not_assign_officer(); 
					}

					var event_html = '';
					$.each(json.polling_event_results, function(index,object){
						event_html += "<tr>";
						event_html += "<td>"+object.st_name+"</td>";
						event_html += "<td>"+object.ac_no + '-' + object.ac_name+"</td>";
						event_html += "<td>"+object.total_ps +"</td>";
						event_html += "<td>"+object.ps_location+"</td>";
						event_html += "<td>"+(object.total_ps - object.ps_location)+"</td>";
						event_html += "<td>"+object.mock_poll_start+"</td>";
						event_html += "<td>"+(object.total_ps - object.mock_poll_start)+"</td>";
						event_html += "<td>"+object.poll_start+"</td>";
						event_html += "<td>"+(object.total_ps - object.poll_start)+"</td>";
						event_html += "<td>"+object.total_voter+"</td>";
						event_html += "<td>"+(object.total_ps - object.total_voter)+"</td>";
						event_html += "<td>"+object.data_sync+"</td>";
						event_html += "<td>"+(object.total_ps - object.data_sync)+"</td>";
						event_html += "<td>"+object.poll_end+"</td>";
						event_html += "<td>"+(object.total_ps - object.poll_end)+"</td>";
						event_html += "</tr>";
					});
					$('#polling_event_results').html(event_html);

					/*poll day*/
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

					/* Doughnut Graph */
					var doughnut_array = [];
					$.each(JSON.parse(json.doughnut_data),function(index,object){
						doughnut_array.push(object);
					});
					doughnut_graph.data.datasets[0].data = doughnut_array;
					doughnut_graph.update();

					/*bar chart*/
					var bar_chart_array = [];
					$.each(JSON.parse(json.bar_graph),function(index,object){
						bar_chart_array.push(object);
					});
					bar_chart.data.datasets[0].data = bar_chart_array;
					bar_chart.update();

					/* bar chart for gender */
					var gender_data_for_bar = [];
					$.each(JSON.parse(json.gender_data_for_bar),function(index,object){
						gender_data_for_bar.push(object);
					});
					gender_chart.data.datasets[0].data = gender_data_for_bar;
					gender_chart.update();


					/* line cahrt update */
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
					reload_voter_turnout();


					$('#poll_turnout_percentage').text(json.poll_turnout_percentage);
					$('#show').prop('disabled', false);

				},
				error: function(data) {
					var errors = data.responseJSON;
					$('#show').prop('disabled', false);
					location.reload();
				}
			}); 
});
});


function doughnut_config(gridlines, title, data_array) {
	return {
		type: 'pie',
		data: {
			labels: ['QR', 'Epic Number', 'Booth Slip', 'Name', 'Mobile'],
			datasets: [{
				backgroundColor: ['#4cc0c0','#ffce57','#33b45a','#44a6e7', '#FF6347'],
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
		load_not_assign_officer();
	});

	function load_not_assign_officer(){
		if($('#my-list-table').length>0){
			table = $('#my-list-table').DataTable({
				"pageLength": 10,
				"aaSorting": [],
				"ordering": false,
				"searching": false,
				"bLengthChange": false,
			});

		}
	}
</script>
@endsection