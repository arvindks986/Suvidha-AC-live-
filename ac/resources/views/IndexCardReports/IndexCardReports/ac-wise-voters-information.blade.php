@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Voters Information')
@section('bradcome', 'AC Wise Voters Information')
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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(12 - AC Wise Voters Information)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/ac-wise-voters-information-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/ac-wise-voters-information-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
          <td rowspan="2"><b>AC No.</b></td>
          <td rowspan="2"><b>AC Name</b></td>
          <th colspan="4" style="text-align: center;"><b>Total Electors (Including Service Electors)</b></th>
		  <td rowspan="2"><b>Overseas Electors</b></td>
          <td rowspan="2"><b>SERVICE Electors</b></td>
          <th colspan="6" style="text-align: center;"><b>Electors who Voted</b></th>
          <td rowspan="2"><b>Overseas Electors who Voted</b></td>
          <td rowspan="2"><b>POLL %</b></td>
          <td rowspan="2"><b>Rejected Votes (Postal)</b></td>
          <td rowspan="2"><b>Votes Rejected From EVM (Test Votes + Rejected Votes due to Other Reasons)</b></td>
          <td rowspan="2"><b>NOTA Votes</b></td>
          <td rowspan="2"><b>Valid Votes Polled</b></td>
          <td rowspan="2"><b>Tendered Votes</b></td>
        </tr>
		
		<tr>

          <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>	
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>POSTAL </b></td>
          <td><b>TEST Votes </b></td>
          <td><b>TOTAL</b></td>
		  
        </tr>
      </thead>
      <tbody>
	  
		@foreach($electorsdata as $key => $data)
	  
        <tr>
          <td>{{$data->AC_NO}}</td>
          <td>{{$data->AC_NAME}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{$data->grand_male}}</td>
          <td>{{$data->grand_female}}</td>
          <td>{{$data->grand_third}}</td>
          <td>{{$data->grand_total}}</td>
		  <td>{{$data->nri_total}}</td>
          <td>{{$data->service_total}}</td>
          
          <td>{{$data->male_voter}}</td>
          <td>{{$data->female_voter}}</td>
          <td>{{$data->third_voter}}</td>
          <td>{{$data->postal}}</td>
          <td>{{$data->test_votes}}</td>
          <td>{{$data->total_voter}}</td>
          <td>{{$data->nri_voter}}</td>
          <td>@if($data->grand_total > 0){{round((($data->total_voter/$data->grand_total)*100),2)}} @else 0 @endif</td>
		  
          <td>{{$data->postal_rejected}}</td>
          <td>{{$data->rejected_votes}}</td>
		  <td>{{$data->nota_votes}}</td>
          <td>{{$data->total_valid_votes}}</td>          
          <td>{{$data->tended_votes}}</td>
        </tr>
		@endforeach
		
		@foreach($electorsdata_total as $key => $data)
	  
        <tr>
          <td colspan="2" ><b>Total</b></td>
          <td><b>{{$data->grand_male}}</b></td>
          <td><b>{{$data->grand_female}}</b></td>
          <td><b>{{$data->grand_third}}</b></td>
          <td><b>{{$data->grand_total}}</b></td>
          <td><b>{{$data->nri_total}}</b></td>
          <td><b>{{$data->service_total}}</b></td>

          <td><b>{{$data->male_voter}}</b></td>
          <td><b>{{$data->female_voter}}</b></td>
          <td><b>{{$data->third_voter}}</b></td>
          <td><b>{{$data->postal}}</b></td>
          <td><b>{{$data->test_votes}}</b></td>
          <td><b>{{$data->total_voter}}</b></td>
          <td><b>{{$data->nri_voter}}</b></td>
          <td><b>@if($data->grand_total > 0){{round(($data->total_voter/$data->grand_total)*100,2)}} @else 0 @endif</b></td>
		  
          <td><b>{{$data->postal_rejected}}</b></td>
          <td><b>{{$data->rejected_votes}}</b></td>
		  <td><b>{{$data->nota_votes}}</b></td>
          <td><b>{{$data->total_valid_votes}}</b></td>          
          <td><b>{{$data->tended_votes}}</b></td>
        </tr>
		@endforeach
        
      </tbody>
    </table>
  
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@endsection