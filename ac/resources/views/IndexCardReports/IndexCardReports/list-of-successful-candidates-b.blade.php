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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(16 - List of Successful Candidates (B))<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/list-of-successful-candidates-b-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/list-of-successful-candidates-b-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <!-- Content goes Here -->

			<table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                        <thead class="">
                            <tr>
                                <th scope="col"></th>
                                <th><b> CONSTITUENCY </b></th>
								<th><b> CATEGORY </b></th>
								<th><b> WINNER </b></th>
								<th><b> SOCIAL CATEGORY </b></th>
								<th><b> PARTY </b></th>
								<th><b> PARTY SYMBOL </b></th>
								<th><b> MARGIN </b></th>
                            </tr>
                        </thead>
                        <tbody>
							<?php $sn = 1; ?>
 
                            @foreach($arraydata as  $catwise)
                            <tr>
                                <td>{{$sn}}</td>
                                <td>{{$catwise->AC_NAME}}</td>
                                <td>{{$catwise->AC_TYPE}}</td>
                                <td>{{$catwise->Cand_Name}}</td>
								<td>{{ucfirst($catwise->cand_category)}}</td>
                                <td>{{$catwise->Party_Abbre}}</td>
                                <td>{{$catwise->Party_symbol}}</td>
                                <td> {{$catwise->margin}} @if($catwise->TotalVote > 0)({{round($catwise->margin/$catwise->TotalVote*100,2)}}%) @endif</td>
                            </tr>
							<?php $sn++; ?>
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