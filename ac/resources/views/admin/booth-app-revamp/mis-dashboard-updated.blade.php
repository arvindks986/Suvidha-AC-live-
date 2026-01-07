@extends('admin.layouts.ac.theme')
@section('content')

<style type="text/css">
h3{font-size: 16px; font-weight:400;}
td{padding:0px;}
.card-footer.p-0 { border-top: 0px;}
.btn-blue{background:#44a6e7; color:#fff;}
.btn-blue:hover{background:#0689e0; color:#fff;}
.lightblue{color:#44a6e7;}
.card.sales-report.p-0 { margin-top: -15px;}
.turnoutbg{background:#49a8a4; color:#fff;}
.poll-table td,.poll-table th{
	padding: 4px !important;
}
</style>


<section class="statistics dashboard color-grey pt-2 pb-2" style="border-bottom:1px solid #eee;">

<!-- <div class="row">

<div class="col-xs-12 col-sm-6 col-md-3">
<div class="card p-0">
<div class="card-body p-0">
<div class="row">
<div class="col-md-4"><i class="fa fa-exchange"></i></div>
<div class="col-md-8">
<div class="row d-flex align-items-center">
<div class="col pt-2 pb-2"><h5 class="text-muted font-weight-normal m-0 boxHeading" title="Number of Customers">No. of Polling Stations</h5></div>
</div>
<div class="row d-flex align-items-center">							
<div class="col"><h1 class="mt-3 mb-3 number numberB yellow">36,254</h1></div>
</div>
</div>
</div>					
</div>
</div>
</div>
</div> -->
<div class="container-fluid">
<div class="row">
@if($role_id==7)
<style type="text/css">

</style>
@endif

<h4 class="page-title col-md-8">Poll Turnout - Booth App</h4>
<div class="col-md-4 ">
<div class="nav mr-auto row btn-group" id="myTab" role="tablist" >

<a class="btn btn-activelist active col-md-5" style="font-size:18px" id="before-tab" data-toggle="tab" href="#beforepoll" role="tab" aria-controls="before" aria-selected="true" >Before Poll</a>

<a class="btn btn-activelist col-md-4"  style="font-size:18px"  id="after-tab" data-toggle="tab" href="#afterpoll" role="tab" aria-controls="after" aria-selected="false">Poll Day</a>

@if($role_id == '19')
<a class="btn btn-activelist col-md-3"  style="font-size:18px"    href="{!! url('roac/booth-app/officer-list') !!}">Add User</a>
@endif



</div>

</div>
</div></div>					


<div class="tab-content" id="myTabContent">
<div class="tab-pane fade show active" id="beforepoll" role="tabpanel" aria-labelledby="before-tab">
<section class="statistics dashboard color-grey pt-2 pb-2" style="border-bottom:1px solid #eee;">
<div class="container-fluid">
<div class="row d-flex">
<div class="col">
<!-- Income-->
<div class="card income text-center">
	<div class="icon"><img src="{{ asset('theme/images/polling-station.png') }}" alt="" /></div>
	<div class="number yellow">{{$total_polling_booth}}</div><p>No. of Polling Stations <!-- <strong class="text-primary">Applied</strong> --></p>

</div>
</div> 

<div class="col">
<!-- Income-->
<div class="card income text-center">
	<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>
	<div class="number green">{{$total_blo_assign+$total_pro_assign}}</div><p>Officers assigned<!-- <strong class="text-primary">Verified</strong> --></p>
</div>
</div> 

<div class="col">
<!-- Income-->
<div class="card income text-center">
	<div class="icon"><img src="{{ asset('theme/images/officer.png') }}" alt="" /></div>
	<div class="row">
	<div class="col-md-6">
	<div class="number green">
	{{$total_blo_assign}}
	</div>
	<p>
	BLO
	</p>
	</div>
	<div class="col-md-6">
	<div class="number green">
	{{$total_pro_assign}}
	</div>
	<p>
	PRO
	</p>
</div>
	</div>
</div>
</div>

<?php /*
<div class="col">
<!-- Income-->
<div class="card income text-center">
	<div class="icon"><img src="{{ asset('theme/images/app-download.png') }}" alt="" /></div>
	<div class="number orange">{{$total_app_downloaded}}</div><p>App downloaded / Login Activated <!-- <strong class="text-primary">Generated</strong> --></p>

</div>
</div>
*/?> 



<div class="col">
<!-- Income-->
<div class="card income text-center">
	<div class="icon"><img src="{{ asset('theme/images/app-download.png') }}" alt="" /></div>
	<div class="row">
	<div class="col-md-6">
	<div class="number green">
	{{$total_blo_activated}}
	</div>
	<p>
	BLO Activated
	</p>
	</div>
	<div class="col-md-6">
	<div class="number green">
	{{$total_pro_activated}}
	</div>
	<p>
	PRO Activated
	</p>
</div>
	</div>
</div>
</div>



<div class="col">
<!-- Income-->
<div class="card income text-center">
	<div class="icon"><img src="{{ asset('theme/images/app-download-checked.png') }}" alt="" /></div>
	<div class="number green">{{$total_e_download}}</div><p>E Roll download confirmed<!-- <strong class="text-primary">Verified</strong> --></p>

</div>
</div> 
<!-- <div class="col">

<div class="card income text-center">
<div class="icon"><img src="img/icon/generate.png" alt=""></div>
<div class="number orange">126</div><p>Total Receipt<strong class="text-primary">Generated</strong></p>

</div>
</div>
-->

</div>



<!-- dashboard officer started -->
<div class="row d-flex">
<div class="table-responsive">
          <table class="table table-bordered list-table-remove" id=""> 
           <thead>
            <tr> 
              <th>PS No</th>
              <th>PS Name</th>
              <th>BLO</th>
              <th>PRO</th>
              @for($i = 1; $i <= $max_po; $i++)
              <th>PO {{$i}}</th>
              @endfor

            </tr>

          </thead>
          
          <tbody id="oneTimetab">   
            @foreach($results as $result)
            <tr>
              <td>{{$result['ps_no']}} </td>
              <td>{{$result['ps_name']}}</td>
              <td>
                @if(count($result['blo'])>0)
                <p>{{$result['blo']['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i> {{$result['blo']['mobile']}}</p>
                @else
                <p class="btn btn-add">--</p>
                @endif
              </td>
              <td>
                @if(count($result['pro'])>0)
                <p>{{$result['pro']['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i> {{$result['pro']['mobile']}}</p>
                
                @else
                <p class="btn btn-add">--</p>
                @endif
              </td>
              @for($i = 1; $i <= $max_po; $i++)
              <td>
                @if(array_key_exists($i, $result['po']))
                <p>{{$result['po'][$i]['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i>  {{$result['po'][$i]['mobile']}}</p>
               
                @else
                <p class="btn btn-add">--</p>
                @endif
              </td>
              @endfor

            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="{{$max_po+4}}">
                {!! urldecode($pag_results->appends(Request::except('page'))->render()) !!}
              </td>
            </tr>
          </tfoot>
          @else
          <tbody>
            <tr>
              <td colspan="{{$max_po+4}}">
                No Record Found.
              </td>
            </tr>
          </tbody>
          @endif
        </table>
      </div><!-- End Of  table responsive --> 
</div>
<!-- dashboard officer end -->

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

</div>




<div class="tab-pane fade " id="afterpoll" role="tabpanel" aria-labelledby="after-tab">


<section class="statistics dashboard color-grey pt-2 pb-2" style="border-bottom:1px solid #eee;">
<div class="container-fluid p-2 pr-5 pl-5">
<div class="row">
<div class="col">
	<div class="card p-0">

		<div class="card-body p-2">
			<div class="row">
				<div class="col-md-9">
					<h3 class="">Polling Started</h3>
					<h1 class="numberset lightblue">{{$total_poll_started}}</h1>
					<small>
						Total Polling Station: <span style="font-size: 25px;">{{$total_polling_booth}}</span><br>
						Total Poll End: <span style="font-size: 25px;">{{$total_poll_end}}</span>
					</small>
				
				</div>
				<div class="col-md-3 text-center">
					<div id="pinkcircle" class="c100 p94 small green">
						<span>{{$poll_percent}}%</span>
						<div class="slice">
							<div class="bar" style="transform: rotate(338.4deg);"></div>
							<div class="fill"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer p-0 " style="display: none;">
			<a class="btn btn-block btn-blue btn-radius text-right" href="#">View Details</a>
		</div></div>
	</div>

	<div class="col pl-0">
		<div class="card p-0">
			<div class="card-header text-center p-2">
				<h3>Polling Station in Working Status</h3>
			</div>
			<div class="card-body p-0">
				<div class="row">
					<div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5;">
						<span class="upperCase">Connected Status</span>
						<h1 class="numberset green">{{$total_connected_status}}</h1>					
					</div>

					<div class="col text-center pt-2 pb-2">
						<span class="upperCase">Disconnected Status</span>
						<h1 class="numberset red">{{$total_disconnected_status}}</h1>

					</div>

				</div></div></div></div>

				<div class="col pl-0">
					<div class="card p-0">	
						<div class="card-header text-left p-2">
							<h3>Polling Station in Disconnected Status</h3>
						</div>
						<div class="card-body  p-2">

							<div class="row">
								<div class="col">
									<table>
										<tr>
											<td width="30%"><b>Polling Station:</b></td>
											<td> {{$last_disc_ps_name}}</td>
										</tr>
										<tr>
											<td><b>AC Name:</b></td>
											<td>{{$last_disc_ac_name}}</td>
										</tr>
									</table></div>

								</div>
							</div>
							<div class="card-footer p-0" style="display: none;">
								<div class="row">
									<small class="col pt-2 ml-2" style="font-size: 12px;">Based Upon 15 min response rate</small>
									<div class="col text-right"><a class="btn btn-primary btn-radius mr-auto" href="#"><span class="text-right">View Details</span></a></div></div>


								</div></div>
							</div>

						</div>
					</div>
				</section>

				<section class="dashboard-header section-padding">
					<div class="container-fluid">
						<div class="row d-flex align-items-md-stretch">

							<div class="col-lg-4 col-md-4">
								<div class="card project-progress chart-pie">              




								</div>
								<table width="50%" class="pull-right" style="display: none;">
									<tr>
										<td><b>Fastest Poll Happening Polling station</b></td>
										<td>Polling Station: Nagar Nigam Prathamik (co-ed) Utkrisht school village lampur Village Lampur</td>
									</tr>
									<tr>
										<td colspan="2"><hr /></td>
									</tr>
									<tr>
										<td><b>Average Timing</b></td>
										<td>Data Here</td>
									</tr>
								</table>

							</div>
							<!-- Line Chart -->
							<div class="col-lg-8 col-md-8 flex-lg-last flex-md-first align-self-baseline" style=" box-shadow: 0px 0 4px 0px #0000004a;">
								<div class="card sales-report p-0">
									<div class="turnoutbg text-center p-1"><div style="font-size: 66px;" class="text-white display h1">{{$poll_turnout_percentage}}%</div><div class="heading text-left text-white">Poll day Turn out Details</div></div>
									<div class="card p-0">
										<div class="card-body p-2 divFull">	<div class="row"><div class="col">

											<div class="row" style="display: none;">
												<h3 class="page-title col">PS Wise Report</h3>
												<div class="mr-auto pr-2">
													<form class="form-inline d-flex align-items-center mb-2" >
														<select name="" class="form-control" id="">
															<option>Select State</option>
															<option>Delhi1</option>
															<option>Delhi2</option>
															<option>Delhi3</option>
														</select>
														&nbsp;
														<select name="" class="form-control" id="">
															<option>Select Phase</option>
															<option>Delhi1</option>
															<option>Delhi2</option>
															<option>Delhi3</option>
														</select>

													</form>
												</div>
											</div>
											<div class="row">
												<div class="col">
													<table align="center" class="table table-bordered table-responsive poll-table">
														<tr>
															<th rowspan="2">PS Name</th>
															<th colspan="4">Electors</th>
															<th colspan="4">Voters</th>
															<th rowspan="2">Percentage</th>
														</tr>
														<tr>


															<th>Male</th>
															<th>Female</th>
															<th>Others</th>
															<th>Total</th>

															<th>Male</th>
															<th>Female</th>
															<th>Others</th>
															<th>Total</th>
														</tr>
														@if(count($voter_turnouts)>0)
														@foreach($voter_turnouts as $iterate_turnout)
														<tr align="center">
															<td><small>{{$iterate_turnout['ps_name']}}</small></td>
															<td>{{$iterate_turnout['e_male']}}</td>
															<td>{{$iterate_turnout['e_female']}}</td>
															<td>{{$iterate_turnout['e_other']}}</td>
															<td>{{$iterate_turnout['e_total']}}</td>
															<td>{{$iterate_turnout['male']}}</td>
															<td>{{$iterate_turnout['female']}}</td>
															<td>{{$iterate_turnout['other']}}</td>
															<td>{{$iterate_turnout['total']}}</td>
															<td>{{$iterate_turnout['percentage']}}%</td>
														</tr>
														@endforeach
														@else
														<tr align="center"><td colspan="10">No Record</td></tr>
														@endif
													</table>
												</div>
											</div>


										</div>
									</div>
								</div>
							</div>


						</div>
					</div>
				</div>
			</div>
		</section>

	</div>
</div>

</section>
@endsection

@section('script')
<script>

function createConfig(gridlines, title) {

	var data_array = [];
	$.each({!!$doughnut_data!!}, function(index,object){
		data_array.push(object);
	});

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
			},
		}
	};
}

window.onload = function() {
	var container = document.querySelector('.chart-pie');

	[{
		title: 'Electoral Scan Percentage in %',
		gridLines: {
			display: true
		}
	}].forEach(function(details) {
		var div = document.createElement('div');
		div.classList.add('chart-container');
		var canvas = document.createElement('canvas');
		div.appendChild(canvas);
		container.appendChild(div);
		var ctx = canvas.getContext('2d');
		var config = createConfig(details.gridLines, details.title);
		new Chart(ctx, config);
	});
};
</script>



@endsection