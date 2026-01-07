<table>
      <thead>
		<tr>
          <th colspan="8" align="center"> <b> 16 - LIST OF SUCCESSFUL CANDIDATES (B) </b> </th>
        </tr>
		<tr>
			<th></th>
			<th><b> CONSTITUENCY </b></th>
			<th><b> CATEGORY </b></th>
			<th><b> WINNER </b></th>
			<th><b> SOCIAL CATEGORY </b></th>
			<th><b> PARTY </b></th>
			<th><b> PARTY SYMBOL </b></th>
			<th><b> MARGIN </b></th>
		</tr>
	
		</thead>
		
        <tbody>       
		<?php $sn = 1; ?>
 
		@foreach($arraydata as  $catwise)
		<tr>
			<td>{{$sn}}</td>
			<td>{{$catwise->AC_NAME}}</td>
			<td>{{$catwise->AC_TYPE}}</td>
			<td>{{$catwise->Cand_Name}}</td>
			<td>{{ucfirst($catwise->cand_category)}}</td>
			<td>{{$catwise->Party_Abbre}}</td>
			<td>{{$catwise->Party_symbol}}</td>
			<td> {{$catwise->margin}} @if($catwise->TotalVote > 0)({{round($catwise->margin/$catwise->TotalVote*100,2)}}%) @endif</td>
		</tr>
		<?php $sn++; ?>
		@endforeach
		
	<tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="8">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
				
      </tr>
      </tbody>
    </table>