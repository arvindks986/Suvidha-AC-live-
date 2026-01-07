
<table align="center" class="table table-bordered  poll-table">
													<thead class="sticky">
												<tr class="turnoutbg">
													<td colspan="5" style="border-color: #49a8a4;">Poll day Turn out Details</td>
													<td colspan="6" style="border-color: #49a8a4;" align="right"><span id="poll_turnout_percentage">{{$poll_turnout_percentage}}</span>%</td>
												</tr>

													<tr>
														<th rowspan="2">State Name</th>
														<th colspan="4" style="background:#6ccac6;">Electors</th>
														<th colspan="4" style="background:#6ccac6;">Voters</th>
														<!--<th rowspan="2" style="background:#6ccac6;">Total Voters in Queue</th> -->
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
														<td width="40%">{{$iterate_turnout['st_name']}}</td>
														<td>{{$iterate_turnout['e_male']}}</td>
														<td>{{$iterate_turnout['e_female']}}</td>
														<td>{{$iterate_turnout['e_other']}}</td>
														<td>{{$iterate_turnout['e_total']}}</td>
														<td>{{$iterate_turnout['male']}}</td>
														<td>{{$iterate_turnout['female']}}</td>
														<td>{{$iterate_turnout['other']}}</td>
														<td>{{$iterate_turnout['total']}}</td>
														<!--<td align="center">{{$iterate_turnout['total_in_queue']}}</td> -->
														<td align="center">{{$iterate_turnout['percentage']}}%</td>
													</tr>
													@endforeach
													@else
													<tr align="center"><td colspan="11">No Record</td></tr>
													@endif
													</tbody>
												</table>