@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Number Of Electors')
@section('bradcome', 'AC Wise Number Of Electors')
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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(11 - AC Wise Number Of Electors)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/ac-wise-no-of-electors-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/ac-wise-no-of-electors-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
          <td class="blcs" rowspan="2">AC No.</td>
          <td class="blcs" rowspan="2">AC Name</td>
          <td class="blcs" colspan="4" style="text-align: center;">GENERAL(Including NRIs)</td>
          <td class="blcs" colspan="3" style="text-align: center;">SERVICE </td>
          <td class="blcs" colspan="4" style="text-align: center;">All Electors</td>
          <td class="blcs" colspan="4" style="text-align: center;">NRIs</td>
        </tr>
		
		<tr>
          
          <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
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
	  
		@foreach($electorsdata as $key => $data)
	  
        <tr>
          <td>{{$data->AC_NO}}</td>
          <td>{{$data->AC_NAME}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{$data->gen_male}}</td>
          <td>{{$data->gen_female}}</td>
          <td>{{$data->gen_third}}</td>
          <td>{{$data->gen_total}}</td>
          <td>{{$data->service_male}}</td>
          <td>{{$data->service_female}}</td>
          <td>{{$data->service_total}}</td>
          <td>{{$data->grand_male}}</td>
          <td>{{$data->grand_female}}</td>
          <td>{{$data->grand_third}}</td>
          <td>{{$data->grand_total}}</td>
          <td>{{$data->nri_male}}</td>
          <td>{{$data->nri_female}}</td>
          <td>{{$data->nri_third}}</td>
          <td>{{$data->nri_total}}</td>
        </tr>
		@endforeach
		
		@foreach($electorsdata_total as $key => $data)
	  
        <tr>
          <td colspan="2" ><b>Total</b></td>
          <td><b>{{$data->gen_male}}</b></td>
          <td><b>{{$data->gen_female}}</b></td>
          <td><b>{{$data->gen_third}}</b></td>
          <td><b>{{$data->gen_total}}</b></td>
          <td><b>{{$data->service_male}}</b></td>
          <td><b>{{$data->service_female}}</b></td>
          <td><b>{{$data->service_total}}</b></td>
          <td><b>{{$data->grand_male}}</b></td>
          <td><b>{{$data->grand_female}}</b></td>
          <td><b>{{$data->grand_third}}</b></td>
          <td><b>{{$data->grand_total}}</b></td>
          <td><b>{{$data->nri_male}}</b></td>
          <td><b>{{$data->nri_female}}</b></td>
          <td><b>{{$data->nri_third}}</b></td>
          <td><b>{{$data->nri_total}}</b></td>
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