@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'Candidate Data Summary')
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


<style>
  .bold{font-weight: bold;
    padding: 13px 0px 0px 30px !important;
  }

  .bolds{font-weight: bold;
  }
</style>
<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(9 - Candidate Data Summary)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/candidate-data-summary-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/candidate-data-summary-xls/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
          <th rowspan="2"></th>
          <th colspan="3" style="text-align: center;">TYPE OF CONSTITUENCY</th>
          <th rowspan="2">TOTAL</th>
        </tr>
        <tr>
          <th>GEN</th>
          <th>SC</th>
          <th>ST</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bolds">1. NO. OF CONSTITUENCIES </td>
          <td>{{isset($acdataarray['GEN']['seats'])?$acdataarray['GEN']['seats']:0}}</td>
          <td>{{isset($acdataarray['SC']['seats'])?$acdataarray['SC']['seats']:0}}</td>
          <td>{{isset($acdataarray['ST']['seats'])?$acdataarray['ST']['seats']:0}}</td>
          <td>{{(isset($acdataarray['GEN']['seats'])?$acdataarray['GEN']['seats']:0) + (isset($acdataarray['SC']['seats'])?$acdataarray['SC']['seats']:0) + (isset($acdataarray['ST']['seats'])?$acdataarray['ST']['seats']:0)}}</td>
        </tr>
		
        <tr>
          <td class="bolds">2. &nbsp;NOMINATIONS FILED</td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['nom_male'])?$candatawise['GEN']['nom_male']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_male'])?$candatawise['SC']['nom_male']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_male'])?$candatawise['ST']['nom_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_male'])?$candatawise['GEN']['nom_male']:0) + (isset($candatawise['SC']['nom_male'])?$candatawise['SC']['nom_male']:0) + (isset($candatawise['ST']['nom_male'])?$candatawise['ST']['nom_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['nom_female'])?$candatawise['GEN']['nom_female']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_female'])?$candatawise['SC']['nom_female']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_female'])?$candatawise['ST']['nom_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_female'])?$candatawise['GEN']['nom_female']:0) + (isset($candatawise['SC']['nom_female'])?$candatawise['SC']['nom_female']:0) + (isset($candatawise['ST']['nom_female'])?$candatawise['ST']['nom_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['nom_third'])?$candatawise['GEN']['nom_third']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_third'])?$candatawise['SC']['nom_third']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_third'])?$candatawise['ST']['nom_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_third'])?$candatawise['GEN']['nom_third']:0) + (isset($candatawise['SC']['nom_third'])?$candatawise['SC']['nom_third']:0) + (isset($candatawise['ST']['nom_third'])?$candatawise['ST']['nom_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['nom_total'])?$candatawise['GEN']['nom_total']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_total'])?$candatawise['SC']['nom_total']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_total'])?$candatawise['ST']['nom_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_total'])?$candatawise['GEN']['nom_total']:0) + (isset($candatawise['SC']['nom_total'])?$candatawise['SC']['nom_total']:0) + (isset($candatawise['ST']['nom_total'])?$candatawise['ST']['nom_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">3.&nbsp; NOMINATIONS REJECTED
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['rej_male'])?$candatawise['GEN']['rej_male']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_male'])?$candatawise['SC']['rej_male']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_male'])?$candatawise['ST']['rej_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_male'])?$candatawise['GEN']['rej_male']:0) + (isset($candatawise['SC']['rej_male'])?$candatawise['SC']['rej_male']:0) + (isset($candatawise['ST']['rej_male'])?$candatawise['ST']['rej_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['rej_female'])?$candatawise['GEN']['rej_female']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_female'])?$candatawise['SC']['rej_female']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_female'])?$candatawise['ST']['rej_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_female'])?$candatawise['GEN']['rej_female']:0) + (isset($candatawise['SC']['rej_female'])?$candatawise['SC']['rej_female']:0) + (isset($candatawise['ST']['rej_female'])?$candatawise['ST']['rej_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['rej_third'])?$candatawise['GEN']['rej_third']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_third'])?$candatawise['SC']['rej_third']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_third'])?$candatawise['ST']['rej_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_third'])?$candatawise['GEN']['rej_third']:0) + (isset($candatawise['SC']['rej_third'])?$candatawise['SC']['rej_third']:0) + (isset($candatawise['ST']['rej_third'])?$candatawise['ST']['rej_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['rej_total'])?$candatawise['GEN']['rej_total']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_total'])?$candatawise['SC']['rej_total']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_total'])?$candatawise['ST']['rej_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_total'])?$candatawise['GEN']['rej_total']:0) + (isset($candatawise['SC']['rej_total'])?$candatawise['SC']['rej_total']:0) + (isset($candatawise['ST']['rej_total'])?$candatawise['ST']['rej_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">4.&nbsp; NOMINATIONS WITHDRAWN
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['with_male'])?$candatawise['GEN']['with_male']:0}}</td>
          <td>{{isset($candatawise['SC']['with_male'])?$candatawise['SC']['with_male']:0}}</td>
          <td>{{isset($candatawise['ST']['with_male'])?$candatawise['ST']['with_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_male'])?$candatawise['GEN']['with_male']:0) + (isset($candatawise['SC']['with_male'])?$candatawise['SC']['with_male']:0) + (isset($candatawise['ST']['with_male'])?$candatawise['ST']['with_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['with_female'])?$candatawise['GEN']['with_female']:0}}</td>
          <td>{{isset($candatawise['SC']['with_female'])?$candatawise['SC']['with_female']:0}}</td>
          <td>{{isset($candatawise['ST']['with_female'])?$candatawise['ST']['with_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_female'])?$candatawise['GEN']['with_female']:0) + (isset($candatawise['SC']['with_female'])?$candatawise['SC']['with_female']:0) + (isset($candatawise['ST']['with_female'])?$candatawise['ST']['with_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['with_third'])?$candatawise['GEN']['with_third']:0}}</td>
          <td>{{isset($candatawise['SC']['with_third'])?$candatawise['SC']['with_third']:0}}</td>
          <td>{{isset($candatawise['ST']['with_third'])?$candatawise['ST']['with_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_third'])?$candatawise['GEN']['with_third']:0) + (isset($candatawise['SC']['with_third'])?$candatawise['SC']['with_third']:0) + (isset($candatawise['ST']['with_third'])?$candatawise['ST']['with_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['with_total'])?$candatawise['GEN']['with_total']:0}}</td>
          <td>{{isset($candatawise['SC']['with_total'])?$candatawise['SC']['with_total']:0}}</td>
          <td>{{isset($candatawise['ST']['with_total'])?$candatawise['ST']['with_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_total'])?$candatawise['GEN']['with_total']:0) + (isset($candatawise['SC']['with_total'])?$candatawise['SC']['with_total']:0) + (isset($candatawise['ST']['with_total'])?$candatawise['ST']['with_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">5. &nbsp;CONTESTING CANDIDATES
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($candatawise['GEN']['cont_male'])?$candatawise['GEN']['cont_male']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_male'])?$candatawise['SC']['cont_male']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_male'])?$candatawise['ST']['cont_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_male'])?$candatawise['GEN']['cont_male']:0) + (isset($candatawise['SC']['cont_male'])?$candatawise['SC']['cont_male']:0) + (isset($candatawise['ST']['cont_male'])?$candatawise['ST']['cont_male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($candatawise['GEN']['cont_female'])?$candatawise['GEN']['cont_female']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_female'])?$candatawise['SC']['cont_female']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_female'])?$candatawise['ST']['cont_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_female'])?$candatawise['GEN']['cont_female']:0) + (isset($candatawise['SC']['cont_female'])?$candatawise['SC']['cont_female']:0) + (isset($candatawise['ST']['cont_female'])?$candatawise['ST']['cont_female']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">c. Third Gender</td>
          <td>{{isset($candatawise['GEN']['cont_third'])?$candatawise['GEN']['cont_third']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_third'])?$candatawise['SC']['cont_third']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_third'])?$candatawise['ST']['cont_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_third'])?$candatawise['GEN']['cont_third']:0) + (isset($candatawise['SC']['cont_third'])?$candatawise['SC']['cont_third']:0) + (isset($candatawise['ST']['cont_third'])?$candatawise['ST']['cont_third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($candatawise['GEN']['cont_total'])?$candatawise['GEN']['cont_total']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_total'])?$candatawise['SC']['cont_total']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_total'])?$candatawise['ST']['cont_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_total'])?$candatawise['GEN']['cont_total']:0) + (isset($candatawise['SC']['cont_total'])?$candatawise['SC']['cont_total']:0) + (isset($candatawise['ST']['cont_total'])?$candatawise['ST']['cont_total']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds">6.&nbsp; FORFEITED DEPOSITS
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td class="bold">a. Male</td>
          <td>{{isset($dfdataarray['GEN']['male'])?$dfdataarray['GEN']['male']:0}}</td>
          <td>{{isset($dfdataarray['SC']['male'])?$dfdataarray['SC']['male']:0}}</td>
          <td>{{isset($dfdataarray['ST']['male'])?$dfdataarray['ST']['male']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['male'])?$dfdataarray['GEN']['male']:0) + (isset($dfdataarray['SC']['male'])?$dfdataarray['SC']['male']:0) + (isset($dfdataarray['ST']['male'])?$dfdataarray['ST']['male']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. Female</td>
          <td>{{isset($dfdataarray['GEN']['female'])?$dfdataarray['GEN']['female']:0}}</td>
          <td>{{isset($dfdataarray['SC']['female'])?$dfdataarray['SC']['female']:0}}</td>
          <td>{{isset($dfdataarray['ST']['female'])?$dfdataarray['ST']['female']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['female'])?$dfdataarray['GEN']['female']:0) + (isset($dfdataarray['SC']['female'])?$dfdataarray['SC']['female']:0) + (isset($dfdataarray['ST']['female'])?$dfdataarray['ST']['female']:0)}}</td>
        </tr>
        <tr>
		  <td class="bold">c. Third Gender</td>
          <td>{{isset($dfdataarray['GEN']['third'])?$dfdataarray['GEN']['third']:0}}</td>
          <td>{{isset($dfdataarray['SC']['third'])?$dfdataarray['SC']['third']:0}}</td>
          <td>{{isset($dfdataarray['ST']['third'])?$dfdataarray['ST']['third']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['third'])?$dfdataarray['GEN']['third']:0) + (isset($dfdataarray['SC']['third'])?$dfdataarray['SC']['third']:0) + (isset($dfdataarray['ST']['third'])?$dfdataarray['ST']['third']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">d. Total</td>
          <td>{{isset($dfdataarray['GEN']['total'])?$dfdataarray['GEN']['total']:0}}</td>
          <td>{{isset($dfdataarray['SC']['total'])?$dfdataarray['SC']['total']:0}}</td>
          <td>{{isset($dfdataarray['ST']['total'])?$dfdataarray['ST']['total']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['total'])?$dfdataarray['GEN']['total']:0) + (isset($dfdataarray['SC']['total'])?$dfdataarray['SC']['total']:0) + (isset($dfdataarray['ST']['total'])?$dfdataarray['ST']['total']:0)}}</td>
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