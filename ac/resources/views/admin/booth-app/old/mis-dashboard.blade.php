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
</style>


<section class="statistics dashboard color-grey pt-2 pb-2" style="border-bottom:1px solid #eee;">
<div class="container-fluid">
<div class="row d-flex align-items-center">
@if($role_id==7)

@endif

<h4 class="page-title col-md-7">Poll Turnout - Booth App</h4>
<div class="ml-auto mr-3">
<div class="nav btn-group" id="myTab" role="tablist">
 <button type="button" class="btn btn-blue refreshbtn" id="show"><span class="fa fa-refresh"></span>  Refresh</button>
 <!-- <button type="button" href="javascript:void(0)" onclick="referesh_page()" class="btn btn-blue refreshbtn"><span class="fa fa-refresh"></span>  Refresh</button>  -->&nbsp; &nbsp;

<a class="btn btn-activelist @if($active_tab == 'before') active @endif btn-radius1" id="before-tab" data-toggle="tab" href="#beforepoll" role="tab" aria-controls="before" aria-selected="true" >Before Poll</a>

<a class="btn btn-activelist @if($active_tab == 'after') active @endif" id="after-tab" data-toggle="tab" href="#afterpoll" role="tab" aria-controls="after" aria-selected="false">Poll Day</a>

@if($role_id == '19')
<a class="btn btn-activelist" href="{!! url('roac/booth-app/officer-list') !!}">Add User</a>
@endif



</div>

</div>
</div></div>					

</section>
<div class="tab-content" id="myTabContent">
<div class="tab-pane fade @if($active_tab == 'before') show active @endif" id="beforepoll" role="tabpanel" aria-labelledby="before-tab">
<section class="statistics dashboard color-grey pt-2 pb-5 mb-5" style="border-bottom:1px solid #eee;">
<div class="container-fluid">
<div class="row d-flex mt-5">
<!-- <div class="col">

<div class="card income cardBox text-center">
<div class="icon"><img src="{{ asset('theme/images/polling-station.png') }}" alt="" /></div>
<div class="number yellow"><a href="{!! $href_polling_station !!}" id="total_polling_booth">{{$total_polling_booth}}</a></div><p><strong class="text-primary">No. of Polling</strong>Stations 
</p>

</div>
</div>  -->

<!-- <div class="col">
<div class="card income cardBox text-center">
<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>
<div class="number green"><a href="{!! $href_officer !!}" id="total_blo_pro_assign">{{$total_blo_pro_assign}}</a></div><p><strong class="text-primary">Officers</strong>Not assigned</p>
</div>
</div>  -->

<?php /* ?>
<div class="col">
<!-- Income-->
<div class="card income cardBox text-center">
<div class="card-body p-2">
<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>

<div class="number red">
<a href="{!! $href_officer !!}" id="total_blo_pro_assign">{{$total_blo_pro_assign}}</a>
<p><strong class="text-primary">Officers</strong>Not assigned</p>
</div></div>
<div class="card-footer p-2">
<div class="row d-flex align-items-center">
<p class="col text-left">No. of Polling Stations</p> <span class="text-primary mr-auto pr-3"><a href="{!! $href_polling_station !!}" id="total_polling_booth">{{$total_polling_booth}}</a></span>
</div>
</div>

</div>
</div>
<?php */ ?>

<div class="col">
<!-- Income-->
<div class="card income cardBox text-center">
<div class="card-body p-2">
<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>

<div class="number red">
<a  id="total_not_assign_officers">{{$total_not_assign_officers}}</a>
<p><strong class="text-primary">Officers</strong>Not assigned</p>
</div></div>
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
<p><strong class="text-primary">PRO</strong>Not activated</p>
</div></div>
<div class="card-footer p-2">
<div class="row d-flex align-items-center">
<p class="col text-left">Total PRO</p> <span class="text-primary mr-auto pr-3"><a href="{!! $href_pro_officer !!}" id="total_pro_assign">{{$total_pro_assign}}</a></span>
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

<?php /* <div class="col-md-6">
<div class="number orange" >
<a href="{!! $href_pro_officer !!}" id="total_pro_assign">
{{$total_pro_assign}}
</a>
</div>
<p><strong class="text-primary">PRO</strong>Total</p>
</div> 

<div class="col">

<div class="card income cardBox text-center">
<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>
<div class="row">
<div class="col-md-6">
<div class="number red">
<a href="{!! $href_blo_officer !!}" id="total_blo_assign">
{{$total_blo_assign}}
</a>
</div>
<p><strong class="text-primary">BLO</strong>Total</p>
</div>
<div class="col-md-6">
<div class="number orange" >
<a href="{!! $href_pro_officer !!}" id="total_pro_assign">
{{$total_pro_assign}}
</a>
</div>
<p><strong class="text-primary">PRO</strong>Total</p>
</div>
</div>
</div>
</div> 

<div class="col">
<!-- Income-->
<div class="card income cardBox text-center">
<div class="icon"><img src="{{ asset('theme/images/app-download.png') }}" alt="" /></div>
<div class="number orange">{{$total_app_downloaded}}</div><p>App downloaded / Login Activated <!-- <strong class="text-primary">Generated</strong> --></p>

</div>
</div>




<div class="col">

<div class="card income cardBox text-center">
<div class="icon"><img src="{{ asset('theme/images/app-download.png') }}" alt="" /></div>
<div class="row">
<div class="col-md-6">
<div class="number green">
<a href="{!! $href_blo_activate !!}" id="total_blo_activated">
{{$total_blo_activated}}
</a>
</div>

<p>
<strong class="text-primary">BLO</strong>Not Activated
</p>
</div>
<div class="col-md-6">
<div class="number green">
<a href="{!! $href_pro_activate !!}" id="total_pro_activated">
{{$total_pro_activated}}
</a>
</div>
<p>
<strong class="text-primary">PRO</strong>Not Activated
</p>
</div>
</div>
</div>
</div> */?>



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
<!-- <div class="row">
<div class="col-xl-12 col-md-12">
<div class="card">
<div class="card-body">

<h4 class="header-title mb-3">Graphs </h4>

 <div class="container"></div>
</div> 
</div> 
</div> 
</div> -->

<div class="container-fluid">
	<div class="row">
		<div class="col">
			<div class="card ">
<div class="card-header color-grey">
<h2 class="">Not Activated Officers</h2>
</div>
<div class="card-body p-0">
	<table align="center" class="table table-bordered poll-table mb-0">
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
	<tbody id="not_activated_officer">
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
<div class="row">
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
			<div class="col-md-3 text-center mt-2 text-right">
				<div id="pinkcircle" class="c100 p94 small grey">
					<span id="poll_percent">{{$poll_percent}}%</span>



					<div class="slice">
						<div class="bar" id="poll_percent_for_css" style="transform: rotate({{$poll_percent_for_css}}deg);"></div>
						<div class="fill"></div>
					</div>
				</div>
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

<div class="col pl-0">
	<div class="card p-0">
		<div class="card-header text-center p-2">
			<h3>Polling Station in Working Status</h3>
		</div>
		<div class="card-body p-0">
			<div class="row">
				<div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
					<span class="upperCase">Connected Status</span>
					<h1 class="numberset green" id="total_connected_status">{{$total_connected_status}}</h1>					
				</div>

				<div class="col text-center pt-2 pb-2">
					<span class="upperCase">Disconnected Status</span>
					<h1 class="numberset red" id="total_disconnected_status">{{$total_disconnected_status}}</h1>

				</div>

			</div></div></div></div>

			<div class="col pl-0">
				<div class="card p-0 greenbg text-center " style="    border-radius: 0 100px 100px 0;">	

					
					<div class="card-body p-0 not-connected" style="min-height: 80px;" >
						<div class="row">
							<div class="col">
				
					<div class="row d-flex align-items-center" style="height: 150px;">
						<div class="col"><h2 class="text-white mb-0">Average Polling Time</h2></div>
						<div class="mr-auto">
							
							<img class="image-responsive" src="{{ asset('theme/images/clock.png') }}" alt="" />
							<div class="timeSet">
								<span id="average_timing">{{$average_timing}}</span>
								<br /> <small>Second</small>							</div>
						</div>
					</div>
							<!-- <h2 class="text-white " style="margin:auto;" id="last_disc_ps_name">
								@if($last_disc_ps_name == '')
								<i class="fa fa-check checked"></i><br />All Polling S
tations are Connected
								@else
								{{$last_disc_ps_name}}
								@endif
							</h2>  -->
							</div></div>

							
						</div>

					
						</div>
						</div>
						
						
						
						<!-- <div class="col pl-0">
				<div class="card p-0">	
					<div class="card-header text-left p-2">
						<h3>Polling Station in Disconnected Status</h3>
					</div>
					<div class="card-body  p-2" style="min-height: 80px;">

						<div class="row">
							<div class="col">
								<table class="table table-sm">
									<tr>
										<td width="30%"><b>Polling Station:</b></td>
										<td><span style="white-space: nowrap; width:215px;  font-size:13px; overflow: hidden;  text-overflow: ellipsis; display: block;" id="last_disc_ps_name">{{$last_disc_ps_name}}</span></td>
									</tr>
									<tr>
										<td><b>AC Name:</b></td>
										<td><span style="white-space: nowrap; width:215px; font-size:13px;  overflow: hidden;  text-overflow: ellipsis; display: block;" id="last_disc_ac_name">{{$last_disc_ac_name}}</span></td>
									</tr>
								</table></div>

							</div>
						</div>
						<div class="card-footer p-0">
							<div class="row">
								<small class="col pt-2 ml-2" style="font-size: 12px;">Based Upon 15 min response rate</small>
								<div class="col-md-4 text-right"><a class="btn btn-primary btn-radius mr-auto" href="{{$href_connected_status}}"><span class="text-right">View Details</span></a></div></div>


							</div></div>
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
							
						
							<div class="project-progress chart-pie"  width="600" height="400">              

								<canvas id="doughnut_graph"></canvas>


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
										<td style="border:0px;" class="p-1 d-flex align-items-center"><b class="text-left col pl-0">Electoral Scan Percentage</b> <button type="button" class="btn btn-blue pull-right scan_history">Click here</button></td>  
											
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
					
		</div>
	<br />

	
			<div class="container-fluid">
			<div class="card ">
				<div class="card-body">
				
				
				<div class="row">
											<div class="col" style="    padding: 7px; margin: -15px 0 0 0;">
												<table align="center" class="table table-bordered  poll-table">
													<thead>
												<tr class="turnoutbg">
													<td colspan="5" style="border-color: #49a8a4;">Poll day Turn out Details</td>
													<td colspan="6" style="border-color: #49a8a4;" align="right"><span id="poll_turnout_percentage">{{$poll_turnout_percentage}}</span>%</td>
												</tr>

													<tr>
														<th rowspan="2">PS Name</th>
														<th colspan="4" style="background:#6ccac6;">Electors</th>
														<th colspan="4" style="background:#6ccac6;">Voters</th>
														<th rowspan="2" style="background:#6ccac6;">Total Voters in Queue</th>
														<th rowspan="2" style="background:#6ccac6;">Poll<br />(%)</th>
													</tr>
													<tr>


														<th>(M)</th>
														<th>(F)</th>
														<th>(TG)</th>
														<th>Total</th>

														<th>(M)</th>
														<th>(F)</th>
														<th>(TG)</th>
														<th>Total</th>
													</tr>
													</thead>
													<tbody id="voter_turnouts">
													@if(count($voter_turnouts)>0)
													@foreach($voter_turnouts as $iterate_turnout)
													<tr>
														<td width="40%">{{$iterate_turnout['ps_name_and_no']}}</td>
														<td>{{$iterate_turnout['e_male']}}</td>
														<td>{{$iterate_turnout['e_female']}}</td>
														<td>{{$iterate_turnout['e_other']}}</td>
														<td>{{$iterate_turnout['e_total']}}</td>
														<td>{{$iterate_turnout['male']}}</td>
														<td>{{$iterate_turnout['female']}}</td>
														<td>{{$iterate_turnout['other']}}</td>
														<td>{{$iterate_turnout['total']}}</td>
														<td align="center">{{$iterate_turnout['total_in_queue']}}</td>
														<td align="center">{{$iterate_turnout['percentage']}}%</td>
													</tr>
													@endforeach
													@else
													<tr align="center"><td colspan="11">No Record</td></tr>
													@endif
													</tbody>
												</table>
											</div>
										</div>
										</div>
			</div>
			</div>
	</section>

</div>


</section>

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
<script>
$(document).ready(function(e){

	$('#before-tab').click(function(e){
		window.history.pushState("data","Title","?tab=before");
		e.preventDefault();
	});
	$('#after-tab').click(function(e){
		window.history.pushState("data","Title","?tab=after");
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
		    dataType: 'json', 
		    beforeSend: function() {
		      
		    },  
		    complete: function() {
		    	$('.loading_spinner').remove();
		    },        
		    success: function(json) {
		      	$('#booth-app .modal-header h3').text(json.heading_title);
		      	var scan_report = '';
		      	scan_report += "<table class='table table-bordered'>";
		      	scan_report += '<thead>';
		      	scan_report += '<tr>';
		      	<?php if($role_id != 19){ ?>
		      	scan_report += "<th>State</th>";
		      	scan_report += "<th>Ac No & Name</th>";
		      	<?php } ?>
		      	scan_report += "<th>PS No & Name</th>";
		      	scan_report += "<th>QR Scan</th>";
		      	scan_report += "<th>EPic No.</th>";
		      	scan_report += "<th>Booth ID</th>";
		      	scan_report += "<th>Name</th>";
		      	scan_report += '</tr>';
		      	scan_report += '</thead>';
		      	$.each(json.results, function(index,object){
					scan_report += "<tr>";
					<?php if($role_id != 19){ ?>
						scan_report += "<td>"+object.st_name+"</td>";
						scan_report += "<td>"+object.ac_no + '-' + object.ac_name+"</td>";
					<?php } ?>
					scan_report += "<td>"+object.ps_no + '-' + object.ps_name+"</td>";
					scan_report += "<td>"+object.total_qr+"</td>";
					scan_report += "<td>"+object.total_epic+"</td>";
					scan_report += "<td>"+object.total_booth_id+"</td>";
					scan_report += "<td>"+object.total_name+"</td>";
					scan_report += "</tr>";
				});
				scan_report += "</table>";
		      	$('#booth-app .modal-body').html(scan_report);
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
		      	$('#not_activated_officer').html(not_activated);

		      
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
		

		      	var html = '';
		      	$.each(json.voter_turnouts, function(index,object){
			      	html += "<tr>";
					html += "<td width='40%'>"+object.ps_name_and_no+"</td>";
					html += "<td>"+object.e_male+"</td>";
					html += "<td>"+object.e_female+"</td>";
					html += "<td>"+object.e_other+"</td>";
					html += "<td>"+object.e_total+"</td>";
					html += "<td>"+object.male+"</td>";
					html += "<td>"+object.female+"</td>";
					html += "<td>"+object.other+"</td>";
					html += "<td>"+object.total+"</td>";
					html += "<td align='center'>"+object.total_in_queue+"</td>";
					html += "<td align='center'>"+object.percentage+"%</td>";
					html += "</tr>";
		      	});
		      	$('#voter_turnouts').html(html);
		      	$('#poll_turnout_percentage').text(json.poll_turnout_percentage);
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
		type: 'doughnut',
		data: {
			labels: ['QR', 'Epic Number', 'Booth Slip', 'Name'],
			datasets: [{
				backgroundColor: ['#4cc0c0','#ffce57','#33b45a','#44a6e7'],
				data: data_array,
				fill: true
			}]
		},
		options: {
			responsive: true,
			title: {
				display: true,
				text: title
			},		circumference: 2*Math.PI,cutoutPercentage: 30,
			legend: {
				position: 'right'
			},
		},
	};
}

/* bar chart */
var data_array = [];
$.each({!!$doughnut_data!!}, function(index,object){
	data_array.push(object);
});

var ctx = document.getElementById('doughnut_graph').getContext('2d');
var config = doughnut_config({
	display: true
}, 'Electoral Scan Percentage', data_array);
var doughnut_graph = new Chart(ctx, config);



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
	      label: '# of Electoral',
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
	    responsive: true,
	    scales: {
	      xAxes: [{
	        ticks: {
	          maxRotation: 0,
	          minRotation: 0
	        }
	      }],
	      yAxes: [{
	        ticks: {
	          beginAtZero: true
	        }
	      }]
	    }
	  }
	});
</script>
<!-- 
<div class="col pl-0">
					<div class="card p-0">	
						<div class="card-header text-left p-2">
							<h3>Polling Station in Disconnected Status</h3>
						</div>
						<div class="card-body  p-2" style="min-height: 80px;">

							<div class="row">
								<div class="col">
									<table class="table table-sm">
										<tbody><tr>
											<td width="30%"><b>Polling Station:</b></td>
											<td><span style="white-space: nowrap; width:215px;  font-size:13px; overflow: hidden;  text-overflow: ellipsis; display: block;">--</span></td>
										</tr>
										<tr>
											<td><b>AC Name:</b></td>
											<td><span style="white-space: nowrap; width:215px; font-size:13px;  overflow: hidden;  text-overflow: ellipsis; display: block;">Hamirpur</span></td>
										</tr>
									</tbody></table></div>

								</div>
							</div>
							<div class="card-footer p-0">
								<div class="row">
									<small class="col pt-2 ml-2" style="font-size: 12px;">Based Upon 15 min response rate</small>
									<div class="col-md-4 text-right"><a class="btn btn-primary btn-radius mr-auto" href="https://suvidha.eci.gov.in/suvidhaac/public/roac/booth-app/poll-detail"><span class="text-right">View Details</span></a></div></div>


								</div></div>
							</div>
 -->
@endsection