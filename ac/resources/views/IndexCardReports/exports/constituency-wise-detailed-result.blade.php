<table>
      <thead>
		<tr>
          <th colspan="12" align="center"> <b> 15 - Constituency wise detailed Result </b></th>
        </tr>
		
		</thead>
		
        <tbody> 
		        <tr>
                            <th rowspan="2"><b>CONSTITUENCY</b></th>                           
                            <th rowspan="2"><b>CANDIDATE NAME</b></th>
                            <th rowspan="2"><b>SEX</b></th>
                            <th rowspan="2"><b>AGE</b></th>
                            <th rowspan="2"><b>CATEGORY</b></th>
                            <th rowspan="2"><b>PARTY</b></th>
                            <th rowspan="2"><b>SYMBOL</b></th>
                           <th style="text-decoration: underline;" colspan="3"><b>Votes Secured</b></th>
                           <th style="text-decoration: underline;" colspan="2"><b>% of votes secured</b></th>
						   <th rowspan="2"><b>TOTAL ELECTORS</b></th>
                        </tr>

                        <tr>
                            <th><b>GENERAL</b></th>
                            <th><b>POSTAL</b></th>
                            <th><b>TOTAL</b></th>
							<th><b>Over total elctors in constituency</b></th>
                            <th><b>Over total votes polled in constituency</b></th>
                        </tr>
		
		@foreach($dataArr as $key1 => $raw)
				
                

                        @foreach($raw as $count=>$row)
                 <?php
				$electors = $row['total_electors'];
				 $totalvotespolled = $row['total_votes'];

                  
                  $totalelectorPercent = ($electors!=0)?((($row['general_vote']+$row['postal_vote'])/$electors)*100):0;
                  //$grandelector+=$totalelectorPercent;


                 $totalvotespolled=($totalvotespolled!=0)?((($row['general_vote']+$row['postal_vote'])/$totalvotespolled)*100):0;
                 //$grandpolled+=$totalvotespolled;

                 ?>
                        <tr>
                            <td style="text-transform: capitalize;">{{$row['PC_NAME']}}</td>
                            <td style="text-transform: capitalize;">{{$row['cand_name']}}</td>
                            <td style="text-transform: capitalize;">{{ucfirst($row['cand_gender'])}}</td>
                            <td>{{$row['cand_age']}}</td>
                            <td>{{ucfirst($row['cand_category'])}}</td>
                            <td>{{$row['party_abbre']}}</td>
                            <td>{{$row['SYMBOL_DES']}}</td>
                            <td>{{$row['general_vote']}}</td>
                            <td>{{$row['postal_vote']}}</td>
                            <td>{{$row['general_vote']+$row['postal_vote']}}</td>
                            <td>{{$totalelectorPercent}}</td>
                            <td>{{$totalvotespolled}}</td>
                            <td style="text-transform: capitalize;">{{$row['total_electors']}}</td>
                        </tr>
					
                        @endforeach  

						
                @endforeach
		
		
	<tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="17">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>

      </tr>
      </tbody>
    </table>