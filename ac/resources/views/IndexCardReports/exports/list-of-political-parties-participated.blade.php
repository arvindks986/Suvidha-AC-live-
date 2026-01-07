<table>
		<thead>
				<tr>
				  <th class="blcs">PARTY TYPE</th>
				  <th class="blcs">ABBREVIATION</th>
				  <th class="blcs">PARTY</th>
				</tr>
			  </thead>
			  <tbody>
					@php $i = 1;  $countN =  $countS = $countSO = $rec = 0; @endphp
			  @foreach($dataArray as $key=>$data)
				@if($key == 'N-N')
					@php $countN = count($data); @endphp
					<tr><td><b>NATIONAL PARTIES</b></td></tr>
				@elseif($key == 'S-U')
				@php $countSO = count($data); @endphp
					<tr><td colspan="3"><b>STATE PARTIES - OTHER STATES</b></td></tr>
				@elseif($key == 'S-S')
				@php $countS = count($data); @endphp
					<tr><td><b>STATE PARTIES</b></td></tr>
				@elseif($key == 'U-U')
				@php $rec = count($data); @endphp
					<tr><td colspan="3"><b>REGISTERED(Unrecognised) PARTIES</b> </td></tr>
				@elseif($key == 'Z-Z')
					<tr><td><b>INDEPENDENTS</b>  </td></tr>
				@endif
				
				  @foreach($data as $raw)
						<tr>
						  <td>{{$i}}.</td>
						  <td>{{$raw['PARTYABBRE']}}</td>
						  <td>{{ucwords(strtolower($raw['PARTYNAME']))}}</td>
						</tr>	
					@php $i++; @endphp
				  @endforeach				  
			  @endforeach	


<tr><td colspan="3"> </td></tr>
<tr><td colspan="3"> </td></tr>
	  
		
					<tr></tr>
					<tr><th colspan="3" style="text-align:center;"><b>Summary Table</b></th></tr>
					<tr><th colspan="2"><b>Party Type</b></th> <th><b>No. of Parties Participated</b></th></tr>
					<tr><td colspan="2">NATIONAL PARTIES</td> <td style="text-align:right;">{{$countN}}</td></tr>
					<tr><td colspan="2">STATE PARTIES</td> <td style="text-align:right;">{{$countS}}</td></tr>
					<tr><td colspan="2">STATE PARTIES - OTHER STATES</td> <td style="text-align:right;">{{$countSO}}</td></tr>
				
				
			
					<tr><td colspan="2">REGISTERED(Unrecognised) PARTIES </td> <td style="text-align:right;">{{$rec}}</td></tr>
					<tr><th colspan="2"><b>All Parties (Excluding NOTA and Independents) </b></th> <th style="text-align:right;"><b>{{$countN + $countSO + $countS + $rec}}</b></th></tr>



			  <tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="6">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
			
      </tr>		  
		  </tbody>
</table>