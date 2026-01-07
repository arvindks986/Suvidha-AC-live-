<table>
      <thead>
		<tr>
          <th colspan="23" align="center"> <b> 13 - AC Wise Candidate data Summary </b> </th>
        </tr>
		<tr>
          <td class="blcs" rowspan="2"><b>AC No.</b></td>
          <td class="blcs" rowspan="2"><b>AC Name</b></td>
          <td class="blcs" rowspan="2"><b>CANDIDATE'S CATEGORY</b></td>
          <td class="blcs" colspan="4" style="text-align: center;"><b>NOMINATIONS FILED</b></td>
          <td class="blcs" colspan="4" style="text-align: center;"><b>NOMINATIONS REJECTED</b></td>
          <td class="blcs" colspan="4" style="text-align: center;"><b>NOMINATIONS WITHDRAWN</b></td>
          <td class="blcs" colspan="4" style="text-align: center;"><b>CONTESTING CANDIDATES</b></td>
          <td class="blcs" colspan="4" style="text-align: center;"><b>DEPOSIT FORFIETED</b></td>
        </tr>
		
		<tr>

          <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
		  <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
        </tr>
      </thead>
      <tbody>
	  
	  <?php 
	  
	  foreach($dataAcType as $ac_data){
		
		$ac_no = $ac_data->AC_NO;
		
		 $nom_male_total = $nom_female_total = $nom_third_total = $nom_total_total = $rej_male_total = $rej_female_total = $rej_third_total = $rej_total_total = $with_male_total = $with_female_total = $with_third_total = $with_total_total = $cont_male_total = $cont_female_total = $cont_third_total = $cont_total_total = $df_male_total = $df_female_total = $df_third_total = $df_total_total = 0;
		
		foreach($cat as $category){			
			$candatawise = App\models\Admin\CandidateCountModel::get_count_by_status_category_ac($st_code,$ac_no, $category);
			
			
			$nom_male_total += $candatawise['nom_male'] ?? 0;
			$nom_female_total += $candatawise['nom_female'] ?? 0;
			$nom_third_total += $candatawise['nom_third'] ?? 0;
			$nom_total_total += $candatawise['nom_total'] ?? 0;
			$rej_male_total += $candatawise['rej_male'] ?? 0;
			$rej_female_total += $candatawise['rej_female'] ?? 0;
			$rej_third_total += $candatawise['rej_third'] ?? 0;
			$rej_total_total += $candatawise['rej_total'] ?? 0;
			$with_male_total += $candatawise['with_male'] ?? 0;
			$with_female_total += $candatawise['with_female'] ?? 0;
			$with_third_total += $candatawise['with_third'] ?? 0;
			$with_total_total += $candatawise['with_total'] ?? 0;
			$cont_male_total += $candatawise['cont_male'] ?? 0;
			$cont_female_total += $candatawise['cont_female'] ?? 0;
			$cont_third_total += $candatawise['cont_third'] ?? 0;
			$cont_total_total += $candatawise['cont_total'] ?? 0;
			$df_male_total += $candatawise['df_male'] ?? 0;
			$df_female_total += $candatawise['df_female'] ?? 0;
			$df_third_total += $candatawise['df_third'] ?? 0;
			$df_total_total += $candatawise['df_total'] ?? 0;
		?>
		
		
		 <tr>
          <td>{{$ac_no}}</td>
          <td>{{$ac_data->AC_NAME}} @if($ac_data->AC_TYPE != 'GEN')({{$ac_data->AC_TYPE}}) @endif</td>
          <td>{{strtoupper($category)}}</td>
          <td>{{ $candatawise['nom_male'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_female'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_third'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_total'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_male'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_female'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_third'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_total'] ?? '0' }}</td>
          <td>{{ $candatawise['with_male'] ?? '0' }}</td>
          <td>{{ $candatawise['with_female'] ?? '0' }}</td>
          <td>{{ $candatawise['with_third'] ?? '0' }}</td>
          <td>{{ $candatawise['with_total'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_male'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_female'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_third'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_total'] ?? '0' }}</td>
          <td>{{ $candatawise['df_male'] ?? '0' }}</td>
          <td>{{ $candatawise['df_female'] ?? '0' }}</td>
          <td>{{ $candatawise['df_third'] ?? '0' }}</td>
          <td>{{ $candatawise['df_total'] ?? '0' }}</td>
         
        </tr>
		
		
		<?php } ?>

		<tr>
          <td colspan="3" style="text-align: center;"><b>Total</b></td>
          <td><b>{{ $nom_male_total }}</b></td>
          <td><b>{{ $nom_female_total }}</b></td>
          <td><b>{{ $nom_third_total }}</b></td> 
		  <td><b>{{ $nom_total_total }}</b></td>
		  <td><b>{{ $rej_male_total }}</b></td>
		  <td><b>{{ $rej_female_total }}</b></td> 
		  <td><b>{{ $rej_third_total }}</b></td> 
		  <td><b>{{ $rej_total_total }}</b></td> 
		  <td><b>{{ $with_male_total }}</b></td> 
		  <td><b>{{ $with_female_total }}</b></td> 
		  <td><b>{{ $with_third_total }}</b></td>
		  <td><b>{{ $with_total_total }}</b></td>
		  <td><b>{{ $cont_male_total }}</b></td> 
		  <td><b>{{ $cont_female_total }}</b></td>
		  <td><b>{{ $cont_third_total }}</b></td>
		  <td><b>{{ $cont_total_total }}</b></td>
		  <td><b>{{ $df_male_total }}</b></td> 
		  <td><b>{{ $df_female_total }}</b></td>
		  <td><b>{{ $df_third_total }}</b></td>
		  <td><b>{{ $df_total_total }}</b></td>         
        </tr>
		
		
	<?php	}  
	  

		 $nom_male_total = $nom_female_total = $nom_third_total = $nom_total_total = $rej_male_total = $rej_female_total = $rej_third_total = $rej_total_total = $with_male_total = $with_female_total = $with_third_total = $with_total_total = $cont_male_total = $cont_female_total = $cont_third_total = $cont_total_total = $df_male_total = $df_female_total = $df_third_total = $df_total_total = 0;
		
		foreach($cat as $category){			
			$candatawise = App\models\Admin\CandidateCountModel::get_count_by_status_category_state($st_code, $category);
			
			
			$nom_male_total += $candatawise['nom_male'] ?? 0;
			$nom_female_total += $candatawise['nom_female'] ?? 0;
			$nom_third_total += $candatawise['nom_third'] ?? 0;
			$nom_total_total += $candatawise['nom_total'] ?? 0;
			$rej_male_total += $candatawise['rej_male'] ?? 0;
			$rej_female_total += $candatawise['rej_female'] ?? 0;
			$rej_third_total += $candatawise['rej_third'] ?? 0;
			$rej_total_total += $candatawise['rej_total'] ?? 0;
			$with_male_total += $candatawise['with_male'] ?? 0;
			$with_female_total += $candatawise['with_female'] ?? 0;
			$with_third_total += $candatawise['with_third'] ?? 0;
			$with_total_total += $candatawise['with_total'] ?? 0;
			$cont_male_total += $candatawise['cont_male'] ?? 0;
			$cont_female_total += $candatawise['cont_female'] ?? 0;
			$cont_third_total += $candatawise['cont_third'] ?? 0;
			$cont_total_total += $candatawise['cont_total'] ?? 0;
			$df_male_total += $candatawise['df_male'] ?? 0;
			$df_female_total += $candatawise['df_female'] ?? 0;
			$df_third_total += $candatawise['df_third'] ?? 0;
			$df_total_total += $candatawise['df_total'] ?? 0;
		?>
		
		
		 <tr>
          <td colspan="2" style="text-align: center;"><b>ALL</b></td>
          <td><b>{{strtoupper($category)}}</b></td>
          <td>{{ $candatawise['nom_male'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_female'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_third'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_total'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_male'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_female'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_third'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_total'] ?? '0' }}</td>
          <td>{{ $candatawise['with_male'] ?? '0' }}</td>
          <td>{{ $candatawise['with_female'] ?? '0' }}</td>
          <td>{{ $candatawise['with_third'] ?? '0' }}</td>
          <td>{{ $candatawise['with_total'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_male'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_female'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_third'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_total'] ?? '0' }}</td>
          <td>{{ $candatawise['df_male'] ?? '0' }}</td>
          <td>{{ $candatawise['df_female'] ?? '0' }}</td>
          <td>{{ $candatawise['df_third'] ?? '0' }}</td>
          <td>{{ $candatawise['df_total'] ?? '0' }}</td>
         
        </tr>
		
		
		<?php } ?>

		<tr>
          <td colspan="3" style="text-align: center;"><b>ALL Total</b></td>
          <td><b>{{ $nom_male_total }}</b></td>
          <td><b>{{ $nom_female_total }}</b></td>
          <td><b>{{ $nom_third_total }}</b></td> 
		  <td><b>{{ $nom_total_total }}</b></td>
		  <td><b>{{ $rej_male_total }}</b></td>
		  <td><b>{{ $rej_female_total }}</b></td> 
		  <td><b>{{ $rej_third_total }}</b></td> 
		  <td><b>{{ $rej_total_total }}</b></td> 
		  <td><b>{{ $with_male_total }}</b></td> 
		  <td><b>{{ $with_female_total }}</b></td> 
		  <td><b>{{ $with_third_total }}</b></td>
		  <td><b>{{ $with_total_total }}</b></td>
		  <td><b>{{ $cont_male_total }}</b></td> 
		  <td><b>{{ $cont_female_total }}</b></td>
		  <td><b>{{ $cont_third_total }}</b></td>
		  <td><b>{{ $cont_total_total }}</b></td>
		  <td><b>{{ $df_male_total }}</b></td> 
		  <td><b>{{ $df_female_total }}</b></td>
		  <td><b>{{ $df_third_total }}</b></td>
		  <td><b>{{ $df_total_total }}</b></td>         
        </tr>
		
		
		
		
	<tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="23">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
		
      </tr>
      </tbody>
    </table>
