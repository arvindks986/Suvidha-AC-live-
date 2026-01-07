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
          <p style="font-size: 15px;"><b>Booth App - Poll Event Report</b></p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
  <table class="table table-bordered">
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
          <tbody>
			@php
				$total_ps = $ps_location= $mock_poll_start = $poll_start = $total_voter = $data_sync = $poll_end = 0;
			 @endphp
			 

		  
             @if(count($results)>0)
			 
            @foreach($results as $key => $result)  
			@php
				$st_code = $result['st_code'];
				$ac_no = $result['ac_no'];
			 @endphp
			
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
			@php
				$total_ps += $result['total_ps'];
				$ps_location += $result['ps_location'];
				$mock_poll_start += $result['mock_poll_start'];
				$poll_start += $result['poll_start'];
				$total_voter += $result['total_voter'];
				$data_sync += $result['data_sync'];
				$poll_end += $result['poll_end'];
			 @endphp
			
            @endforeach
			
			<tr>
              <td colspan="2" style="text-align: center;"><b>Total</b></td>
              <td><b>{{$total_ps}}</b></td>
              <td><b>{{$ps_location}}</b></td>
			  <td>{{$total_ps-$ps_location}}</td>
			  <td><b>{{$mock_poll_start}}</b></td>
			  <td>{{$total_ps-$mock_poll_start}}</td>
              <td><b>{{$poll_start}}</b></td>
			  <td>{{$total_ps-$poll_start}}</td>
              <td><b>{{$total_voter}}</b></td>
			  <td>{{$total_ps-$total_voter}}</td>
              <td><b>{{$data_sync}}</b></td>
			  <td>{{$total_ps-$data_sync}}</td>
              <td><b>{{$poll_end}}</b></td>
			  <td>{{$total_ps-$poll_end}}</td>
            </tr>
            @else 
            <tr>
              <td colspan="6">
                No Record Found.
              </td>
            </tr>
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