@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'Performance of women Candidates')
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
	

	.boldes{
		font-weight: bold;
	}

	.bolds{
		font-weight: bold;
	}
</style>
<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(7 -Individual Performance of Women Candidates)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/performance-of-women-candidates-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/performance-of-women-candidates-xls/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
				  <th colspan="15" class="bolds" style="">Name of Constituency </th>
				</tr>
				<tr>
					 <th class="bolds">AC No</th>
				  <th class="bolds">Name of AC</th>
				  <th class="bolds">Name of candidate </th>
				  <th class="bolds">Party</th>
				  <th class="bolds">Party <br>Type</th>
				  <th class="bolds">Votes <br>Secured</th>
				   <th class="bolds">Status</th>
				     <th class="bolds">Total votes polled</th>
				     <th class="bolds">Valid Votes</th>

				  <th colspan="4" class="bolds" style="text-decoration: underline;text-align: center;">% of votes secured over</th>
				  <!-- <th class="bolds">Status</th>
				  <th class="bolds">Total <br>Valid <br>votes<br> + NOTA</th> -->
				</tr>
				<tr>
				  <th colspan="9" class="bolds blc"></th>
				  <th class="bolds blc">Total <br>Electors</th>
				  <th class="bolds blc">Total <br>votes polled</th>
				  <th colspan="2" class="bolds blc">Valid Votes</th>
				</tr>
			  </thead>
			  <tbody>
				<tr>
				  <td colspan="12" class="bolds">State/UT : {{$st->ST_NAME}}</td>
				</tr>
				
				@foreach($dataArray as $key => $data)
				
			<!-- 	<tr>
				  <td class="boldes">{{$key}}</td>
				</tr> -->
				@foreach($data as $key1 => $raw)
				<?php   if($raw['PARTYTYPE']=='Z'){
					        $partytype='IND';
				}else{
             $partytype=$raw['PARTYTYPE'];

				} ?> 
				<tr>
					<td>{{$raw['AC_NO']}}</td>
					<td>{{$raw['AC_NAME']}}</td>
				  <td> {{ucwords(strtolower($raw['candidate_name']))}} </td>
				  <td> {{$raw['party_abbre']}} </td>
				  <td> {{$partytype}} </td>
				   <td>{{$raw['candidate_votes']}} </td>
				   <td> {{$raw['status']}} </td>
				   
				  <td> {{$raw['total_votes_polled']}} </td>
				    <td>{{$raw['total_votes']}} </td>

				  <td>@if($raw['total_electors'] > 0)
						{{number_format((float)($raw['candidate_votes']*100)/$raw['total_electors'], 2, '.', '')}}
						@else
							0
						@endif
					</td>
					<td>@if($raw['total_votes'])
					{{number_format((float)($raw['candidate_votes']*100)/$raw['total_votes_polled'], 2, '.', '')}}
						@else
							0
						@endif</td>
					<td>@if($raw['total_votes'])
					{{number_format((float)($raw['candidate_votes']*100)/$raw['total_votes'], 2, '.', '')}}
						@else
							0
						@endif</td>
				 
				  <!-- <td> {{$raw['total_votes']}} </td> -->
				</tr>
				@endforeach
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