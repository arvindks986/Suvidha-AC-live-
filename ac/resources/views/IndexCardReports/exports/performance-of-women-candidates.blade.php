<table>
			  <thead>
			  <tr>
				  <th colspan="12" align="center"><b>7 -Individual Performance of Women Candidates</b></th>
				</tr>
				<tr>
				  <td colspan="12" align="center"></td>
				</tr>
				
				<tr>
					 <th class="bolds">AC No</th>
				  <th class="bolds">Name of AC</th>
				  <th class="bolds">Name of candidate </th>
				  <th class="bolds">Party</th>
				  <th class="bolds">Party <br>Type</th>
				  <th class="bolds">Votes <br>Secured</th>
				   <th class="bolds">Status</th>
				     <th class="bolds">Total votes polled</th>
				     <th class="bolds">Valid Votes</th>

				  <th colspan="3" class="bolds" style="text-decoration: underline;text-align: center;">% of votes secured over</th>
				  <!-- <th class="bolds">Status</th>
				  <th class="bolds">Total <br>Valid <br>votes<br> + NOTA</th> -->
				</tr>
				<tr>
				  <th colspan="9" class="bolds blc"></th>
				  <th class="bolds blc">Total <br>Electors</th>
				  <th class="bolds blc">Total <br>votes polled</th>
				  <th class="bolds blc">Valid Votes</th>
				</tr>
			  </thead>
			  <tbody>
				
				
				@foreach($dataArray as $key => $data)
				
			<!-- 	<tr>
				  <td class="boldes">{{$key}}</td>
				</tr> -->
				@foreach($data as $key1 => $raw)
				<?php   if($raw['PARTYTYPE']=='Z'){
					        $partytype='IND';
				}else{
             $partytype=$raw['PARTYTYPE'];

				} ?> 
				<tr>
					<td>{{$raw['AC_NO']}}</td>
					<td>{{$raw['AC_NAME']}}</td>
				  <td> {{ucwords(strtolower($raw['candidate_name']))}} </td>
				  <td> {{$raw['party_abbre']}} </td>
				  <td> {{$partytype}} </td>
				   <td>{{$raw['candidate_votes']}} </td>
				   <td> {{$raw['status']}} </td>
				   
				  <td> {{$raw['total_votes_polled']}} </td>
				    <td>{{$raw['total_votes']}} </td>

				  <td>@if($raw['total_electors'] > 0)
						{{number_format((float)($raw['candidate_votes']*100)/$raw['total_electors'], 2, '.', '')}}
						@else
							0
						@endif
					</td>
					<td>@if($raw['total_votes'])
					{{number_format((float)($raw['candidate_votes']*100)/$raw['total_votes_polled'], 2, '.', '')}}
						@else
							0
						@endif</td>
					<td>@if($raw['total_votes'])
					{{number_format((float)($raw['candidate_votes']*100)/$raw['total_votes'], 2, '.', '')}}
						@else
							0
						@endif</td>
				 
				  <!-- <td> {{$raw['total_votes']}} </td> -->
				</tr>
				@endforeach
				@endforeach

														  <tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="12">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
				
      </tr>
			  </tbody>
			</table>
 