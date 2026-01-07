<html>
  <head>
    <style>
    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 6px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
    .left-al tr td{
    text-align: left;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-spacing: 0px 8px;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 12px;
    font-weight: bold !important;
    padding: 5px;
    text-align: left;
    }
 
    table{
    width: 100%;
    border-collapse: collapse;
    }
    </style>
  </head>
  <div class="bordertestreport">
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p> <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
            </b>
          <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
        </td>
      </tr>
    </table>
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p style="font-size: 15px;"><b>Booth App - AC Wise Poll Turnout Report</b></p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
				<table align="center" class="table table-bordered  poll-table">
						<thead class="sticky">
					<tr class="turnoutbg">
						<td colspan="6" style="">Poll day Turn out Details</td>
						<td colspan="7" style="" align="right"><span id="poll_turnout_percentage">{{$data['poll_turnout_percentage']}}</span>%</td>
					</tr>

						<tr>
							<th rowspan="2">S.NO.</th>
							<th rowspan="2">State/UT Name</th>
							<th rowspan="2">AC No & Name</th>
							<th colspan="4" style="">Electors</th>
							<th colspan="4" style="">Voters</th>
							<th rowspan="2" style="">Total Voters in Queue</th>
							<th rowspan="2" style="">Poll<br />(%)</th>
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
						@if(count($data['voter_turnouts'])>0)
						@foreach($data['voter_turnouts'] as $key => $iterate_turnout)
						<tr>
							<td>{{$key + 1}}</td>
							<td>{{$iterate_turnout['st_name']}}</td>
							<td>{{$iterate_turnout['name']}}</td>
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
  
  <table>
    <tr style="width: 100%;">
      <td colspan="7" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>