<table>
<tbody>
			<tr>
              <td colspan="10" align="center"><b>4 - Highlight</b></td>
            </tr>
           <tr>
              <td colspan="1" align="center"><b>1.</b></td>
              <td colspan="1" align="center"><b> No. of Constituencies</b></td>
            </tr>

            <tr>
			  <td></td>
              <td align="center"><b>Type Of Constituency</b></td>
              <td><b> GEN </b></td>
              <td><b> SC </b></td>
              <td><b> ST </b></td>
              <td colspan="1"><b>Total </b></td>
            </tr>
			
            <tr>
				<td></td>
				<td align="center"><b>No Of Constituencies</b></td>
				<td>{{(isset($candidates->genac) ? $candidates->genac : 0)}}</td>
				<td>{{(isset($candidates->scac) ? $candidates->scac : 0) }}</td>
				<td>{{ (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
				<td colspan="">{{(isset($candidates->genac) ? $candidates->genac : 0) +(isset($candidates->scac) ? $candidates->scac: 0) + (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
            </tr>
         
			<tr>
              <td colspan="1" align="center"><b>2. </b></td>
              <td colspan="6" align="center"><b>NO. of Contestants </b></td>
            </tr>

            <tr>
			  <td></td>
              <td colspan="1" align="center"><b>NO. of Contestants in a Constituency</b></td>
              <td><b>1</b></td>
              <td><b>2</b></td>
              <td><b>3</b></td>
              <td><b>4</b></td>
              <td><b>5</b></td>
              <td><b>6-10</b></td>
              <td><b>11-15</b></td>
              <td><b>Above 15</b></td>
            </tr>
			
            <tr>
			  <td></td>
              <td colspan="1" align="center"><b>NO Of Such CONSTITUENCIES
              </b></td>
              <td>{{$candidates->one}}</td>
              <td>{{$candidates->two}}</td>
              <td>{{$candidates->three}}</td>
              <td>{{$candidates->four}}</td>
              <td>{{$candidates->five}}</td>
              <td>{{$candidates->fiveten}}</td>
              <td>{{$candidates->tenfifteen}}</td>
              <td>{{$candidates->fifteen}}</td>
            </tr>

            <tr>
              <td colspan="1"></td>
              <td colspan="1" align="center"><b>Total Contestants in a Fray</b></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
              <td colspan="1">{{$candidates->Total_Candidates}}</td>
            </tr>
			
            <tr>
			  <td colspan="1"></td>
              <td colspan="1" align="center"><b>Average Contestants Per Constituency</b></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
              <td colspan="1">{{$candidates->Avg}}</td>
            </tr>
			
            <tr>
			  <td colspan="1"></td>
              <td colspan="1" align="center"><b>Minimum Contestants in a Constituency</b></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
              <td colspan="1">{{$candidates->maxcnd}}</td>
            </tr>
			
            <tr>
			  <td colspan="1"></td>
              <td colspan="1" align="center"><b>Maximum Contestants in a Constituency</b></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
			  <td colspan="1"></td>
              <td colspan="1">{{$candidates->mincnd}}</td>
            </tr>

			<tr>
              <td colspan="1" align="center"><b>3.</b></td>
              <td colspan="1" align="center"><b> Electors</b></td>
              <td colspan="" align="center"><b>Male</b></td>
              <td colspan="" align="center"><b>Female</b></td>
              <td colspan="" align="center"><b>Third Gender</b> </td>
              <td colspan="" align="center"><b>Total</b></td>
            </tr>
			
            <tr>
              <td align="center">i.</td>
              <td  class="dev2" colspan="1" align="center"><b>NO. OF ELECTORS</b>(Including Service Electors)</td>
              <td colspan="">{{$candidates->maleElectors}}</td>
              <td colspan="">{{$candidates->femaleElectors}}</td>
              <td colspan="">{{$candidates->thirdElectors}}</td>
              <td colspan="">{{$candidates->totalElectors}}</td>
            </tr>
			
            <tr>
              <td align="center">ii.</td>
              <td colspan="1" align="center"> <b>No. of Electors Who
              Voted</b>(Excluding Postal Votes)</td>
              <td colspan="">{{$candidates->totalMaleVoters}}</td>
              <td colspan="">{{$candidates->totalFemaleVoters}}</td>
              <td colspan="">{{$candidates->totalOtherVoters}}</td>
              <td colspan="">{{$candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters}}</td>
            </tr>
			
            <tr>
              <td align="center">iii. </td>
              <td colspan="1" align="center"><b>Polling Percentage</b>(Excluding Postal Ballots)</td>
              <td colspan="">{{round($candidates->totalMaleVoters/$candidates->maleElectors * 100,2)}}</td>
              <td colspan="">{{round($candidates->totalFemaleVoters/$candidates->femaleElectors * 100,2)}}</td>
              <?php if($candidates->thirdElectors != 0)  { ?>
                <td colspan="">{{round($candidates->totalOtherVoters/$candidates->thirdElectors * 100,2)}}</td>
              <?php } else { ?>
                <td>0</td>
              <?php } ?>
              <td colspan="">{{round(($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters)/$candidates->totalElectors * 100,2)}}</td>
            </tr>
			
			
			
			<tr>
            <td colspan="1" align="center"><b>3a</b></td>
            <td colspan="1" align="center"><b> Polling Percentage (State) </b></td>
			<td colspan="6"></td>
            <td colspan="1">{{round(($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters +$candidates->totalPostalVoters)/$candidates->totalElectors * 100,2)}}</td>
          </tr>
		
		

<tr>
            <td colspan="1" align="center"><b>4a</b></td>
            <td colspan="1" align="center"><b> Total Votes Polled (EVM + Postal)</b></td>
			<td colspan="6"></td>
            <td colspan="1">{{$candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters +$candidates->totalPostalVoters  + $candidates->test_votes_49_ma}}</td>
          </tr>
			
			
          <tr>
            <td colspan="1" align="center"><b>4b</b></td>
            <td colspan="1" align="center"><b> Total valid Votes (EVM + Postal)</b></td>
			<td colspan="6"></td>
            <td colspan="1">{{$candidates->totalEvmPostalvote}}</td>
          </tr>
		  
		  <tr>
            <td colspan="1" align="center"><b>5a</b></td>
            <td colspan="1" align="center"><b> NOTA Votes (EVM + Postal)</b></td>
			<td colspan="6"></td>
            <td colspan="1">{{$candidates->notatotal}}</td>
          </tr>
		  
		  <tr>
            <td colspan="1" align="center"><b>5b</b></td>
            <td colspan="1" align="center"><b> NOTA Votes As % of Total Votes </b></td>
			<td colspan="6"></td>
            <td colspan="1">{{round(($candidates->notatotal)/($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters +$candidates->totalPostalVoters) * 100,2)}}</td>
          </tr>
		  
		  
		  <tr>
            <td colspan="1" align="center"><b>6a</b></td>
           <td colspan="1" align="center"><b> Total Postal Votes </b></td>
		   <td colspan="6"></td>
            <td colspan="1">{{$candidates->totalPostalVoters}}</td>
          </tr>
		  
		  <tr>
            <td colspan="1" align="center"><b>6b</b></td>
            <td colspan="1" align="center"><b> No Of Rejected Postal Votes </b></td>
			<td colspan="6"></td>
            <td colspan="1">{{$candidates->rejectedpostalvote}}</td>
          </tr>
		  
		  <tr>
            <td colspan="1" align="center"><b>6c</b></td>
            <td colspan="1" align="center"><b> % of Rejected Postal Votes Over Total Postal Votes </b></td>
			<td colspan="6"></td>
            <td colspan="1">{{round(($candidates->rejectedpostalvote)/($candidates->totalPostalVoters) * 100,2)}}</td>
          </tr>
		  
		  <tr>
		  <td colspan="1" align="center"><b>7a</b></td>
            <td colspan="1" align="center"><b> NO. OF POLLING STATIONS </b></td>
			<td colspan="6"></td>
            <td colspan="1">{{$candidates->totalpollingstation}}</td>
          </tr>
          <tr>
            <td colspan="1" align="center"><b>7b</b></td>
            <td colspan="1" align="center"><b>AVERAGE NO. OF ELECTORS PER POLLING STATION </b></td>
			<td colspan="6"></td>
            <td colspan="1">{{round($candidates->totalElectors/$candidates->totalpollingstation,0)}}</td>
          </tr>
			
			

			<tr>
				<td colspan="1" align="center"><b>8.</b></td>
				<td colspan="1" align="center"><b>Performance of Contesting Candidates</b></td>
				<td align="center"><b>Male</b></td>
				<td align="center"><b>Female</b></td>
				<td align="center"><b>Third Gender</b></td>
				<td colspan="1" align="center"><b>Total</b></td>
			</tr>
		  
          <tr>
            <td colspan="1" align="center"><b>i. </b></td>
            <td colspan="1" align="center"><b>No. Of Contestants</b></td>
            <td>{{$candidates->totalnominatedmale}}</td>
            <td>{{$candidates->totalnominatedfemale}}</td>
            <td>{{$candidates->totalnominatedthird}}</td>
            <td>{{$candidates->totalnominatedmale+$candidates->totalnominatedfemale+$candidates->totalnominatedthird}}</td>
          </tr>
		  
          <tr>
            <td align="center"><b>ii. </b></td>
            <td colspan="1" align="center"><b>Elected Candidates</b></td>
            <td>{{$candidates->totalwinnermale}}</td>
            <td>{{$candidates->totalwinnerfemale}}</td>
            <td>{{$candidates->totalwinnerthird}}</td>
            <td colspan="">{{$candidates->totalwinnermale+$candidates->totalwinnerfemale+$candidates->totalwinnerthird}}</td>
          </tr>
		  
          <tr>
            <td align="center"><b>iii. </b></td>
            <td colspan="1" align="center"><b> Forfeited Deposits</b></td>
            <td>{{$candidates->fdmale}}</td>
            <td>{{$candidates->fdfemale}}</td>
            <td>{{$candidates->fdthird}}</td>
            <td colspan="1">{{$candidates->fdmale+$candidates->fdfemale+$candidates->fdthird}}</td>
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