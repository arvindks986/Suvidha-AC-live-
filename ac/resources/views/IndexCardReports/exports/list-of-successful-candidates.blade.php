<table>
      <thead>
		 <tr>
          <th colspan="14" align="center"><b>2 - List of Successful Candidates</b></th>
        </tr>
	  
	  
	  
	  <thead>
	     <tr>
          <th></th>
          <th></th>
          <th></th>
          <th colspan="7" style="text-align:center;"><b>WINNER </b></th>
		  <th colspan="4" style="text-align:center;"><b>RUNNER-UP </b></th>
		  <th></th>
        </tr>
	  
        <tr>
		  <th><b>STATE/UT</b></th>
          <th><b>AC No.</b></th>
          <th><b>CONSTITUENCY </b></th>
          <th><b>Name </b></th>
          <th><b>SEX</b></th>
          <th><b>PARTY</b></th>
          <th><b>SYMBOL</b></th>
          <th><b>AGE</b></th>
          <th><b>SOCIAL CATEGORY</b></th>
          <th><b>VOTES SECURED</b></th>
		  <th><b>Name </b></th>
		  <th><b>SEX</b></th>
          <th><b>PARTY</b></th>
		  <th><b>VOTES SECURED</b></th>
		  <th><b>WINNING MARGIN</b></th>
        </tr>
      </thead>
	  
	  
      </thead>
      <tbody>
	  
		@foreach($dataCaddidateWise as $key => $data)
	  
        <tr>
          <td>{{$data->st_name}}</td>
          <td>{{$data->ac_no}}</td>
          <td>{{$data->ac_name}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{ucwords(strtolower($data->lead_cand_name))}}</td>
           <td>@if($data->cand_gender == 'male')
			 M
		  @elseif($data->cand_gender == 'female')
			F
		  @elseif($data->cand_gender == 'third')
			TG
		  @endif
		  </td>
          <td>{{$data->lead_party_abbre}}</td>
          <td>{{$data->SYMBOL_DES}}</td>
          <td>{{$data->cand_age}}</td>
          <td>{{strtoupper($data->cand_category)}}</td>
		  <td>{{$data->lead_total_vote}}</td>
		  <td>{{$data->trail_cand_name}}</td>
		   <td>@if($data->trail_cand_gender == 'male')
			 M
		  @elseif($data->trail_cand_gender == 'female')
			F
		  @elseif($data->trail_cand_gender == 'third')
			TG
		  @endif
		  </td>
		  <td>{{$data->trail_party_abbre}}</td>
		  <td>{{$data->trail_total_vote}}</td>
		  <td>{{$data->margin}}</td>
        </tr>
		@endforeach
        
      </tbody>
    </table>
    <table>
      <thead>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
       <tr><th colspan="7" style="font-weight: bold; font-size: 14px; text-align:center;">PARTY WISE SUMMARY</th></tr>
        <tr><td colspan="3" style="font-size: 12px;text-align: center;"><p><b>{{$dataPartyWise[0]->st_name}}</b></p></td>
		<td colspan="4" style="font-size: 12px;text-align: center;"><p><b>Winning Candidates</b></p></td></tr>
        <tr>
          <td class="blc"  colspan="3"><b>PARTY NAME</b></td>
          <td class="blc"><b>Total</b></td>
          <td class="blc"><b>Male</b></td>
          <td class="blc"><b>Female</b></td>
          <td class="blc"><b>TG</b></td>
        </tr>
      </thead>
      <tbody>
		<?php $all_total = $all_male = $all_female = $all_tg = 0; ?>
		@foreach($dataPartyWise as $key => $data)
		
		<?php $all_total += $data->total_seats; 
		$all_male += $data->male;
		$all_female += $data->female;
		$all_tg += $data->third; ?>
		
        <tr>
          <td colspan="3">{{$data->lead_cand_party}}</td>
          <td>{{$data->total_seats}}</td>
          <td>{{$data->male}}</td>
          <td>{{$data->female}}</td>
          <td>{{$data->third}}</td>
        </tr>
		@endforeach
		
		<tr>
          <td colspan="3"><b>Total of Successful Candidates</b></td>
          <td><b>{{$all_total}}</b></td>
          <td><b>{{$all_male}}</b></td>
          <td><b>{{$all_female}}</b></td>
          <td><b>{{$all_tg}}</b></td>
        </tr>
		
			  <tr></tr>	  
	  <tr>
        <td colspan="3"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="14">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
				
      </tr>
      </tbody>
    </table>