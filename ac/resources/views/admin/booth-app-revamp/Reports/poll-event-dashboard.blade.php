

          <table class="table table-bordered " id="list-table">
           <thead>
            <tr> 
              <th rowspan="2">State/UT Name</th>
              <th rowspan="2">AC NO & AC Name</th>
              <th rowspan="2">Total PS</th>
			  <th colspan="2">Mock Poll Done</th>
              <th colspan="2">Poll Started</th>
              <th colspan="2">Voting Started</th>
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
			 
            </tr>
          </thead>
          <tbody>
			@php
				$total_ps = $ps_location= $mock_poll_start = $poll_start = $total_voter = $data_sync = $poll_end = $poll_mat_rec = $poll_mat_sub = $pro_diary_sub = 0 ;
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
              <td><a href='{{url("eci/booth-app-revamp/poll-event-ps-wise-report?st_code=$st_code&ac_no=$ac_no")}}' target="_blank">{{$result['total_ps']}}</a></td>
			 
              <td>{{$result['mock_poll_start']}}</td>
              <td>{{$result['total_ps']-$result['mock_poll_start']}}</td>
              <td>{{$result['poll_start']}}</td>
			  <td>{{$result['total_ps']-$result['poll_start']}}</td>
              <td>{{$result['total_voter']}}</td>
			  <td>{{$result['total_ps']-$result['total_voter']}}</td>
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
				$poll_mat_rec += $result['total_received'];
				$pro_diary_sub += $result['pro_diary_sub'];
				$poll_mat_sub += $result['total_submited'];
			 @endphp
			
            @endforeach
			
			<tr>
			
              <td colspan="2" style="text-align: center;"><b>Total</b></td>
              <td><b>{{$total_ps}}</b></td>
			  <td><b>{{$mock_poll_start}}</b></td>
			  <td><b>{{$total_ps-$mock_poll_start}}</b></td>
              <td><b>{{$poll_start}}</b></td>
			  <td><b>{{$total_ps-$poll_start}}</b></td>
              <td><b>{{$total_voter}}</b></td>
			  <td><b>{{$total_ps-$total_voter}}</b></td>
			  <td><b>{{$poll_end}}</b></td>
			  <td><b>{{$total_ps-$poll_end}}</b></td>
			
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
 
