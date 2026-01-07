 <table>
		  <thead>
			<tr>
			  <td class="bolds top blc" colspan="8" align="center"><b>5 - Performance of Political Parties</b></td>
			</tr>
			
			<tr>			  
			  <td></td>
			  <td></td>
			  <td colspan="3" align="center"><b> CANDIDATES </b></td>
			  <td></td>
			  <td colspan="2"align="center"><b>Votes Polled as Share (%) of</b></td>
			  <td align="center"><b>Share of Vote <br> Polled in SEATS <br>CONTESTED</b></td>
			</tr>
			
			<tr>
			  <td><b> PARTY TYPE </b></td>
			  <td><b> ABBREVIATION </b></td>
			  <td><b> CONTESTED </b></td>
			  <td><b> WON </b></td>
			  <td><b> FD </b></td>
			  <td><b> Votes Polled </b></td>
			  <td><b> Valid Votes </b></td>
			  <td><b> Valid Votes + NOTA </b></td>
			  <td></td>
			</tr>
		  </thead>
		  <tbody>
			@php $i = 1; 
			$all_total_contested = $all_total_won = $all_total_fd = $all_total_fd = $all_total_party = 0;
			@endphp
			  @foreach($dataArray as $key=>$data)
				@if($key == 'N-N')
					<tr><th colspan="2"><b>NATIONAL PARTIES</b></th></tr>
				@elseif($key == 'S-U')
					<tr><th colspan="2"><b>STATE PARTIES - OTHER STATES</b></th></tr>
				@elseif($key == 'S-S')
					<tr><th colspan="2"><b>STATE PARTIES</b></th></tr>
				@elseif($key == 'U-U')
					<tr><th colspan="2"><b>REGISTERED(Unrecognised) PARTIES</b> </th></tr>
				@elseif($key == 'Z-Z')
					<tr><th colspan="2"><b>INDEPENDENTS</b></th></tr>
				@endif
				
				@php $total_contested = $total_won = $total_fd = $total_fd = $total_party = 0; 
				@endphp
				
				
				  @foreach($data as $raw)
				  
					<?php 
					if($raw['total_valid_votes'] > 0){
						$per_valid = round((($raw['vote_secured_by_party']/$raw['total_valid_votes'])*100),2);
					}else{
						$per_valid = 0;
					}
					
					if($raw['total_votes'] > 0){
						$per = round((($raw['vote_secured_by_party']/$raw['total_votes'])*100),2);
					}else{
						$per = 0;
					}
					
					if($raw['contests_total_votes'] > 0){
						$per_c = round((($raw['vote_secured_by_party']/$raw['contests_total_votes'])*100),2);
					}else{
						$per_c = 0;
					}
					
					$total_contested += $raw['contested'];
					$total_won += $raw['won'];
					$total_fd += $raw['fd'];
					$total_party += $raw['vote_secured_by_party'];
					
					if($raw['PARTYABBRE']!='NOTA'){
						$all_total_contested += $raw['contested'];
						$all_total_won += $raw['won'];
						$all_total_fd += $raw['fd'];
					}
					$all_total_party += $raw['vote_secured_by_party'];
					
					$total_valid_votes = $raw['total_valid_votes'];
					$total_votes = $raw['total_votes'];
					
					?>				  
						<tr>
						  <td class="padd">{{$i}}</td>
						  <td class="padd">{{$raw['PARTYABBRE']}}</td>
						  <td>@if($raw['PARTYABBRE']=='NOTA') - @else{{$raw['contested']}}@endif</td>
						  <td>@if($raw['PARTYABBRE']=='NOTA') - @else{{$raw['won']}}@endif</td>
						  <td>@if($raw['PARTYABBRE']=='NOTA') - @else{{$raw['fd']}}@endif</td>
						  <td>{{$raw['vote_secured_by_party']}}</td>
						  <td>{{$per_valid}}%</td>
						  <td>{{$per}}%</td>
						  <td>{{$per_c}}</td>
						</tr>
			
			
			@php $i++; @endphp
				  @endforeach
				  <?php 
					if($total_valid_votes > 0){
						$per_valid = round((($total_party/$total_valid_votes)*100),2);
					}else{
						$per_valid = 0;
					}
					
					if($total_votes > 0){
						$per = round((($total_party/$total_votes)*100),2);
					}else{
						$per = 0;
					}
					?>

			@if($raw['PARTYABBRE']=='NOTA')
			<tr>
			  <td class="blcs"></td>
			  <td class="blcs"></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>{{$total_party}}</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>{{$per}}</b></td>
			  <td class="blcs">  </td>
			</tr>
			@else
				<tr>
				  <td class="blcs"></td>
				  <td class="blcs"></td>
				  <td class="blcs"><b>{{$total_contested}}</b></td>
				  <td class="blcs"><b>{{$total_won}}</b></td>
				  <td class="blcs"><b>{{$total_fd}}</b></td>
				  <td class="blcs"><b>{{$total_party}}</b></td>
				  <td class="blcs"><b>{{$per_valid}}</b></td>
				  <td class="blcs"><b>{{$per}}</b></td>
				  <td class="blcs">  </td>
				</tr>
			@endif
				  
			  @endforeach
			  <?php 
					if($total_valid_votes > 0){
						$per_valid = round((($all_total_party/$total_valid_votes)*100),2);
					}else{
						$per_valid = 0;
					}
					
					if($total_votes > 0){
						$per = round((($all_total_party/$total_votes)*100),2);
					}else{
						$per = 0;
					}
					?>
			  
			  
				<tr>
				  <td class="blc"><b>Grand Total:</b></td>
				  <td class="blc"></td>
				  <td class="blcs"><b>{{$all_total_contested}}</b></td>
			  <td class="blcs"><b>{{$all_total_won}}</b></td>
			  <td class="blcs"><b>{{$all_total_fd}}</b></td>
			  <td class="blcs"><b>{{$all_total_party}}</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>{{$per}}</b></td>
				  <td class="blc">  </td>
				</tr>
				
							  <tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="10">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
				
      </tr>
				
				
			  </tbody>
			</table>