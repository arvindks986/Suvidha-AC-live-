<table>
      <thead>
	  <tr>
          <th colspan="6" align="Center"><b>6 - Electors Data Summary</b></th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th colspan="4" align="Center"><b>TYPE OF CONSTITUENCY</b></th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th>GEN</th>
          <th>SC</th>
          <th>ST</th>
          <th>TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bolds"><b>1</b></td>
          <td class="bolds" align="left"><b>NO. OF CONSTITUENCIES</b></td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalscac']) ? ($electorsvotersdataNew['SC']['totalscac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalstac']) ? ($electorsvotersdataNew['ST']['totalstac']) :0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) +(isset($electorsvotersdataNew['SC']['totalscac'])? ($electorsvotersdataNew['SC']['totalscac']) :0) + (isset($electorsvotersdataNew['ST']['totalstac'])?$electorsvotersdataNew['ST']['totalstac']:0)}}</td>
        </tr>
        <tr>
          <td colspan="1"><b>2</b></td>
          <td colspan="1" align="left"><b>ELECTORS</b> (Including SERVICE VOTERS)</td>
        </tr>
        <tr>
          <td class="bold"><b>a.</b></td>
          <td class="bold" align="left"> <b>MALE</b> </td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['maleElectors']) ? ($electorsvotersdataNew['SC']['maleElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['maleElectors']) ? ($electorsvotersdataNew['ST']['maleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0)+(isset($electorsvotersdataNew['SC']['maleElectors'])? ($electorsvotersdataNew['SC']['maleElectors']):0)+(isset($electorsvotersdataNew['ST']['maleElectors'])? ($electorsvotersdataNew['ST']['maleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>b.</b></td>
          <td class="bold" align="left"><b>FEMALE</b></td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors'])? ($electorsvotersdataNew['GEN']['femaleElectors']):0 )}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']): 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['femaleElectors']) ? ($electorsvotersdataNew['ST']['femaleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors']) ? ($electorsvotersdataNew['GEN']['femaleElectors']) :0)+ (isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']):0)+(isset($electorsvotersdataNew['ST']['femaleElectors'])? ($electorsvotersdataNew['ST']['femaleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>c.</b></td>
          <td class="bold" align="left"><b>THIRD GENDER</b></td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['thirdElectors'])? ($electorsvotersdataNew['SC']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['thirdElectors'])? ($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']):0)+ (isset($electorsvotersdataNew['SC']['thirdElectors'])?($electorsvotersdataNew['SC']['thirdElectors']):0)+(isset($electorsvotersdataNew['ST']['thirdElectors'])?($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>d.</b></td>
          <td class="bold" align="left"><b>TOTAL</b></td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])? ($electorsvotersdataNew['GEN']['totalElectors']):0)+ (isset($electorsvotersdataNew['SC']['totalElectors']) ? ($electorsvotersdataNew['SC']['totalElectors']):0)+ (isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="1"><b>3</b></td>		  
          <td class="bolds" colspan="1" align="left"><b>ELECTORS WHO VOTED</b></td>		  
        </tr>
        <tr>
          <td class="bold"><b>a.</b></td>
          <td class="bold" align="left"><b>MALE</b></td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>b.</b></td>
          <td class="bold" align="left"><b>FEMALE</b></td>
           <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])? ($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>c.</b></td>
          <td class="bold" align="left"><b>THIRD GENDER</b></td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)+(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
        </tr>

        <tr>
          <td class="bold"><b>d.</b></td>
           <td class="bold" align="left"><b>POSTAL</b></td>
          <!-- <td class="bold" align="left"><b>POSTAL</b>(Details given in Annxure-1)</td> -->
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
        </tr>
		
		<tr>
          <td class="bold"><b>e.</b></td>
          <td class="bold" align="left"> <b>TEST VOTES </b></td>
          <td>{{(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
        </tr>
		
        <tr>
          <td class="bold"><b>f.</b></td>
          <td class="bold" align="left"><b>TOTAL</b></td>
		  
		  <?php 

		  
		  $totalgen = (isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters']) ? ($totalvoteNew['GEN']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0);
		  
		  $totalsc = (isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0) + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0);
		  
		  $totalst = (isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0);
		  
		  $totalvoters = (isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])? ($totalvoteNew['SC']['totalOtherVoters']):0) + (isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0) +(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0) + +(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0) + +(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)  + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0);
		  
		  
		  ?>
		  
          <td>{{$totalgen}}</td>

          <td>{{$totalsc}}</td>
          <td>{{$totalst}}</td>
          <td>
            {{$totalvoters}}
        </td>
        </tr>
        <tr>
          <td class="bold"></td>
          <td class="bold" align="left"><b>PROXY</b></td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)+(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)+(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="1"><b>4</b></td>
          <td class="bolds" colspan="1" align="left"><b>OVERSEAS ELECTORS</b></td>
        </tr>
        <tr>
          <td class="bold"><b>a.</b></td>
          <td class="bold" align="left"><b>MALE</b></td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}
          </td>
        </tr>
        <tr>
          <td class="bold"><b>b.</b></td>
          <td class="bold" align="left"><b>FEMALE</b></td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>c.</b></td>
          <td class="bold" align="left"><b>THIRD GENDER</b></td>
           <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>d.</b></td>
          <td class="bold" align="left"><b>TOTAL</b></td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>
            {{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="1"><b>5</b></td>
          <td class="bolds" colspan="1" align="left"><b>OVERSEAS ELECTORS WHO VOTED</b></td>
        </tr>
        <tr>
          <td class="bold"><b>a.</b></td>
          <td class="bold" align="left"><b>MALE</b></td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>b.</b></td>
          <td class="bold" align="left"><b>FEMALE</b></td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>c.</b></td>
          <td class="bold" align="left"><b>THIRD GENDER</b></td>
           <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?$totalvoteNew['GEN']['overseasthirdvoters']:0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>d.</b></td>
          <td class="bold" align="left"><b>TOTAL</b></td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="1"><b>6</b></td>
          <td class="bolds" colspan="1" align="left"><b>REJECTED POSTAL VOTES</b></td>
        </tr>
        <tr>
          <td class="bold"><b>a.</b></td>
         <td class="bold" align="left"><b>a. NO OF  REJECTED POSTAL VOTES</b>
          <!-- <td class="bold" align="left"><b>VOTES</b> <span style="font-weight: normal;"> (POSTAL)</span></td> -->
          <td>{{(isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)}}</td>
        </tr>
        <tr>
          <td class="bold"><b>b.</b></td>
          <td class="bold" align="left"><b>b. AS % OF TOTAL POSTAL VOTES</b></td>


          <td>
            {{round((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)/(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)*100,2)}}
          </td>
          <?php if(isset($totalpostalvoteNew['SC']['postaltotalreceived']) && ($totalpostalvoteNew['SC']['postaltotalreceived'] > 0 )) { ?>
          <td>



          {{round((isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)/(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)*100,2)}}

        </td>
      <?php } else { ?>
        <td>0</td>

      <?php } ?>

      <?php if(isset($totalpostalvoteNew['ST']['postaltotalreceived']) && ($totalpostalvoteNew['ST']['postaltotalreceived'] > 0 )) { ?>

          <td>
          {{round((isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)/(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)*100,2)}}

        </td>
         <?php } else { ?>
            <td>0</td>

          <?php } ?>

          <td>{{round(((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0))/((isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0))*100,2)}}</td>
        </tr>
        <tr>
        
        <tr>
          <td class="bold"><b>c.</b></td>
          <td class="bold" align="left"><b>VOTES REJECTED FROM</b> <br>EVM <span style="font-weight: bold;">(TEST VOTES+REJECTED DUE TO OTHER <br> REASON)</span></td>
          <td>{{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+
          (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)+(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)+(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds"><b>7</b></td>
          <td class="bolds" align="left"><b>NOTA VOTES</b> <span style="font-weight: bold;">(POSTAL + EVM)</span></td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds"><b>8</b></td>
          <td class="bolds" align="left"><b>VALID VOTES</b></td>


          <?php $total1 = ($totalgen)-((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)
          +((isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)
          )+(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)); ?>

          <?php $total2 = ($totalsc)-((isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+((isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0))
          +(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)); ?>

          <?php $total3 = ($totalst)-((isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)+((isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?
          ($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0))+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)); ?>

          <td>
            {{ $total1 }}

          </td>
          <td>
             {{ $total2 }}

          </td>
          <td>
             {{ $total3 }}

          </td>
          <td>{{$total1+$total2+$total3}}</td>

        </tr>
        <tr>
          <td class="bolds"><b>9</b></td>
          <td class="bolds" align="left"><b>POLL PERCENTAGE</b></td>
          <?php if(isset($electorsvotersdataNew['GEN']['totalElectors']) && ($electorsvotersdataNew['GEN']['totalElectors'] > 0)) { ?>
            <td>{{round($totalgen/(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)*100,2)}}</td>
          
          <?php } else { ?>
          <td>0</td>
        <?php } ?>

          <?php if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) { ?>
          <td>{{round($totalsc/$electorsvotersdataNew['SC']['totalElectors']*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($electorsvotersdataNew['ST']['totalElectors']) && ($electorsvotersdataNew['ST']['totalElectors'] > 0)) { ?>
          <td>{{round($totalst/(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>


          <td>{{round(($totalvoters)/((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)+(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)
            +(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0))*100,2)}}</td>
        </tr>
        <tr>
          <td class="bolds"><b>10</b></td>
          <td class="bolds" align="left"><b>NO. OF POLLING STATIONS</b></td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)
            +(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)
            +(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds"><b>11</b></td>
          <td class="bolds" align="left"><b>AVERAGE NO. OF <br> ELECTORS PER POLLING <br>STATION</b> </td>
          <?php
                if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { 


              $total4 = (isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0);

            }else{
              $total4 = 0;
            }
            if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) {
              $total5 = (isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0);
            } else{
              $total5 = 0;
            }

            if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation'] > 0)) {
              $total6 = (isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0);
            } else{
              $total6 = 0;
            }
            

          ?>
          <?php if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { ?>
		  
		  <?php $totalelectors = (isset($electorsvotersdataNew['GEN']['totalElectors'])? ($electorsvotersdataNew['GEN']['totalElectors']):0)+ (isset($electorsvotersdataNew['SC']['totalElectors']) ? ($electorsvotersdataNew['SC']['totalElectors']):0)+ (isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0); 
		
		$totalpollingstations = (isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)
            +(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)
            +(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0);
		
		?>

          <td>{{round((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($totalvoteNew['SC']['totalpollingstation']) && ($totalvoteNew['SC']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
        <?php if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
          @if($totalpollingstations > 0)
				<td>{{round($totalelectors/$totalpollingstations,0)}}</td>
		@else
		 <td>0</td>	
		@endif
        
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