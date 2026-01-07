@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Candidate data Summary')
@section('bradcome', 'AC Wise Candidate data Summary')
@section('content')
@php
	if(Auth::user()->designation == 'ROAC'){
		$prefix 	= 'roac';
	}else if(Auth::user()->designation == 'CEO'){	
		$prefix 	= 'acceo';
	}else if(Auth::user()->role_id == '27'){
		$prefix 	= 'eci-index';
	}else if(Auth::user()->role_id == '7'){
		$prefix 	= 'eci';
	}
@endphp


<?php  $st=getstatebystatecode($st_code);   ?>


<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(13 - AC Wise Candidate data Summary)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/ac-wise-candidate-data-summary-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/ac-wise-candidate-data-summary-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <!-- Content goes Here -->
      <table class="table table-bordered table-striped" style="width: 100%;">
      <thead>
        <tr>
          <td class="blcs" rowspan="2"><b>AC No.</b></td>
          <td class="blcs" rowspan="2"><b>AC Name</b></td>
          <td class="blcs" rowspan="2"><b>CANDIDATE'S CATEGORY<b></td>
          <td class="blcs" colspan="4" style="text-align: center;">NOMINATIONS FILED</td>
          <td class="blcs" colspan="4" style="text-align: center;">NOMINATIONS REJECTED</td>
          <td class="blcs" colspan="4" style="text-align: center;">NOMINATIONS WITHDRAWN</td>
          <td class="blcs" colspan="4" style="text-align: center;">CONTESTING CANDIDATES</td>
          <td class="blcs" colspan="4" style="text-align: center;">DEPOSIT FORFIETED</td>
        </tr>
		
		<tr>

          <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
		  <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
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
		
      </tbody>
    </table>
  
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@endsection
