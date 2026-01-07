@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'List of Successful Candidates')
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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(2 - List of Successful Candidates)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/list-of-successful-candidates-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/list-of-successful-candidates-xls/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
          <th class="blcs"></th>
          <th class="blcs"> </th>
          <th class="blcs" colspan="7" style="text-align:center;">WINNER </th>
		  <th class="blcs" colspan="4" style="text-align:center;">RUNNER-UP </th>
		  <th class="blcs"></th>
        </tr>
	  
	  
	  
	  
	  
        <tr>
          <th class="blcs">AC No.</th>
          <th class="blcs">CONSTITUENCY </th>
          <th class="blcs">Name </th>
          <th class="blcs">SEX</th>
          <th class="blcs">PARTY</th>
          <th class="blcs">SYMBOL</th>
          <th class="blcs">AGE</th>
          <th class="blcs">SOCIAL CATEGORY</th>
          <th class="blcs">VOTES SECURED</th>
		  <th class="blcs">Name </th>
		  <th class="blcs">SEX</th>
          <th class="blcs">PARTY</th>
		  <th class="blcs">VOTES SECURED</th>
		  <th class="blcs">WINNING MARGIN</th>
        </tr>
      </thead>
      <tbody>
	  
		@foreach($dataCaddidateWise as $key => $data)
	  
        <tr>
          <td>{{$data->ac_no}}</td>
          <td>{{$data->ac_name}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{ucwords(strtolower($data->lead_cand_name))}}</td>
          <td>{{strtoupper($data->cand_gender)}}</td>
          <td>{{$data->lead_party_abbre}}</td>
          <td>{{$data->SYMBOL_DES}}</td>
          <td>{{$data->cand_age}}</td>
          <td>{{strtoupper($data->cand_category)}}</td>
		  <td>{{$data->lead_total_vote}}</td>
		  <td>{{$data->trail_cand_name}}</td>
		  <td>{{strtoupper($data->trail_cand_gender)}}</td>
		  <td>{{$data->trail_party_abbre}}</td>
		  <td>{{$data->trail_total_vote}}</td>
		  <td>{{$data->margin}}</td>
        </tr>
		@endforeach
        
      </tbody>
    </table>
    <table class="table border" border="1" style="width: 40%;margin: auto;">
        <tr><th colspan="6"><p style="font-weight: bold;font-size: 19px;text-align: center;">PARTY WISE SUMMARY</p></th></tr>
        <tr><td colspan="2" style="font-size: 17px;text-align: center;"><p><b>{{$dataPartyWise[0]->st_name}}</b></p></td>
		<td colspan="4" style="font-size: 17px;text-align: center;"><p><b>Winning Candidates</b></p></td></tr>
        <tr>
          <td class="blc"  colspan="2"><b>PARTY NAME</b></td>
          <td class="blc"><b>Total</b></td>
          <td class="blc"><b>Male</b></td>
          <td class="blc"><b>Female</b></td>
          <td class="blc"><b>TG</b></td>
        </tr>
      <tbody>
	  
		<?php $all_total = $all_male = $all_female = $all_tg = 0; ?>
		@foreach($dataPartyWise as $key => $data)
		
		<?php $all_total += $data->total_seats; 
		$all_male += $data->male;
		$all_female += $data->female;
		$all_tg += $data->third; ?>
		
        <tr>
          <td colspan="2">{{$data->lead_cand_party}}</td>
          <td>{{$data->total_seats}}</td>
          <td>{{$data->male}}</td>
          <td>{{$data->female}}</td>
          <td>{{$data->third}}</td>
        </tr>
		@endforeach
		
		<tr>
          <td colspan="2"><b>Total of Successful Candidates</b></td>
          <td><b>{{$all_total}}</b></td>
          <td><b>{{$all_male}}</b></td>
          <td><b>{{$all_female}}</b></td>
          <td><b>{{$all_tg}}</b></td>
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