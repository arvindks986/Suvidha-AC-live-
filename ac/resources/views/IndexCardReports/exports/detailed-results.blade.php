<table>
			  <thead>
			  <tr>
				  <th colspan="14" align="center"><b>10 - Detailed Results</b></th>
				</tr>
				<tr>
				  <td colspan="14" align="center"></td>
				</tr>
				<tr>
				  <td colspan="9"></td>
				  <td colspan="3" style="text-decoration: underline;text-align: center;"><b>VALID VOTES POLLED </b></td>
				</tr>
				<tr>
				  <td><b>STATE/UT NAME</b></td>
				  <td><b>AC NO.</b></td>
				  <td><b>AC NAME</b></td>
				  <td><b>CANDIDATE NAME </b></td>
				  <td><b>SEX</b></td>
				  <td><b>AGE</b></td>
				  <td><b>CATEGORY</b></td>
				  <td><b>PARTY</b></td>
				  <td><b>SYMBOL</b></td>
				  <td><b>GENERAL</b></td>
				  <td><b>POSTAL</b></td>
				  <td><b>TOTAL</b></td>
				  <td><b>% VOTES POLLED</b></td>
				  <td><b>TOTAL ELECTORS </b></td>
				</tr>
			  </thead>
			  <tbody>
			  
				@foreach($dataArr as $key => $data)
					
					@php $i =1; $per = 0; $gen_total = $postal_total = $all_total = $total_electors = $total_votes =0;
					@endphp
					
					@foreach($data as $raw)
					
						<?php 
						$gen_total += $raw['general_vote'];
						$postal_total += $raw['postal_vote'];
						$all_total += $raw['cand_total_vote'];
						$total_electors = $raw['total_electors'];
						$total_votes = $raw['total_votes'];
						
						
						if($raw['total_votes'] > 0){
							$per = round((($raw['cand_total_vote']/$raw['total_votes'])*100),2);
						}						
						?>					
						<tr>
						  <td>{{$state_name}}</td>
						  <td>{{$raw['AC_NO']}}</td>
						  <td>{{$raw['AC_NAME']}} </td>
						  <td>{{$i}} {{ucwords(strtolower($raw['cand_name']))}}</td>
						  <td>{{strtoupper($raw['cand_gender'])}}</td>
						  <td>{{$raw['cand_age']}}</td>
						  <td>{{strtoupper($raw['cand_category'])}}</td>
						  <td>{{$raw['party_abbre']}}</td>
						  <td>{{$raw['SYMBOL_DES']}}</td>
						  <td>{{$raw['general_vote']}}</td>
						  <td>{{$raw['postal_vote']}}</td>
						  <td>{{$raw['cand_total_vote']}}</td>
						  <td>{{$per}}</td>
						  <td>{{$raw['total_electors']}}</td>
						</tr>
						@php $i++; @endphp
						
					@endforeach
					
					<?php
					if($total_electors > 0){
							$pertotal = round((($total_votes/$total_electors)*100),2);
						}else{
							$pertotal = 0;
						}					
						?>
					<tr>
					  <td colspan="3" style="text-align: left;" ><b>TURN OUT</b></td>
					  <td colspan="3"></td>
					  <td colspan="3"><b>TOTAL:</b></td>
					  <td><b>{{$gen_total}}</b></td>
					  <td><b>{{$postal_total}}</b></td>
					  <td><b>{{$all_total}}</b></td>
					  <td><b>{{$pertotal}}</b></td>
					</tr>
					
				@endforeach
				<tr>
				  <td colspan="9"  style="text-align: right;"><b>GRAND TOTAL: </b></td>
				  <td><b>{{$all_state_Data[0]->all_state_total - $all_state_Data[0]->all_state_postal}}</b></td>
				  <td><b>{{$all_state_Data[0]->all_state_postal}}</b></td>
				  <td><b>{{$all_state_Data[0]->all_state_total}}</b></td>
				  <td></td>
				</tr>
																	  <tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="14">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>

      </tr>
			  </tbody>
			</table>
