@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'Performance of Political Parties')
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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(5 - Performance of Political Parties)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/performance-of-political-parties-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/performance-of-political-parties-xls/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
			  <th class="" rowspan="2">PARTY</th>
			  <th class="" colspan="3" style="text-align: center; text-decoration: underline;">CANDIDATES</th>
			  <th class=""></th>
			  <th class="" colspan="2" style="text-align: center; text-decoration: underline;">Votes Polled as Share (%) of</th>
			  <th class="" rowspan="">Share of Vote <br> Polled in SEATS <br>CONTESTED</th>
			</tr>
			<tr>
			  <th class="">CONTESTED</th>
			  <th class="">WON</th>
			  <th class="">FD</th>
			  <th class="">Votes Polled</th>
			  <th class="">Valid Votes</th>
			  <th class="">Valid Votes + NOTA</th>
			  <th class=""></th>
			</tr>
		  </thead>
		  <tbody>
			@php $i = 1; 
			$all_total_contested = $all_total_won = $all_total_fd = $all_total_fd = $all_total_party = 0;
			@endphp
			  @foreach($dataArray as $key=>$data)
				@if($key == 'N-N')
					<tr><th colspan="8">NATIONAL PARTIES</th></tr>
				@elseif($key == 'S-U')
					<tr><th colspan="8">STATE PARTIES - OTHER STATES</th></tr>
				@elseif($key == 'S-S')
					<tr><th colspan="8">STATE PARTIES</th></tr>
				@elseif($key == 'U-U')
					<tr><th colspan="8">REGISTERED(Unrecognised) PARTIES </th></tr>
				@elseif($key == 'Z-Z')
					<tr><th colspan="8">INDEPENDENTS  </th></tr>
				@endif
				
				@php $total_contested = $total_won = $total_fd = $total_fd = $total_party = 0; 
				@endphp
				
				
				  @foreach($data as $raw)
				  
					<?php 
					if($raw['total_valid_votes'] > 0){
						$per_valid = round((($raw['vote_secured_by_party']/$raw['total_valid_votes'])*100),2);
					}else{
						$per_valid = 0;
					}
					
					if($raw['total_valid_votes'] > 0){
						$per = round((($raw['vote_secured_by_party']/$raw['total_votes'])*100),2);
					}else{
						$per = 0;
					}
					
					if($raw['contests_total_votes'] > 0){
						$per_c = round((($raw['vote_secured_by_party']/$raw['contests_total_votes'])*100),2);
					}else{
						$per_c = 0;
					}
					
					$total_contested += $raw['contested'];
					$total_won += $raw['won'];
					$total_fd += $raw['fd'];
					$total_party += $raw['vote_secured_by_party'];
					
					if($raw['PARTYABBRE']!='NOTA'){
						$all_total_contested += $raw['contested'];
						$all_total_won += $raw['won'];
						$all_total_fd += $raw['fd'];
					}
					
					$all_total_party += $raw['vote_secured_by_party'];
					
					$total_valid_votes = $raw['total_valid_votes'];
					$total_votes = $raw['total_votes'];
					
					?>				  
						<tr>
						  <td style="font-weight: bold;">{{$i}}. &nbsp; {{$raw['PARTYABBRE']}}</td>
						  <td>@if($raw['PARTYABBRE']=='NOTA') - @else{{$raw['contested']}}@endif</td>
						  <td>@if($raw['PARTYABBRE']=='NOTA') - @else{{$raw['won']}}@endif</td>
						  <td>@if($raw['PARTYABBRE']=='NOTA') - @else{{$raw['fd']}}@endif</td>
						  <td>{{$raw['vote_secured_by_party']}}</td>
						  <td>{{$per_valid}}%</td>
						  <td>{{$per}}%</td>
						  <td>{{$per_c}}</td>
						</tr>
			
			
			@php $i++; @endphp
				  @endforeach
				  <?php 
					if($total_valid_votes > 0){
						$per_valid = round((($total_party/$total_valid_votes)*100),2);
					}else{
						$per_valid = 0;
					}
					
					if($total_votes > 0){
						$per = round((($total_party/$total_votes)*100),2);
					}else{
						$per = 0;
					}
					?>

			@if($raw['PARTYABBRE']=='NOTA')
			<tr>
			  <td class="blcs"></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>{{$total_party}}</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>{{$per}}</b></td>
			  <td class="blcs">  </td>
			</tr>
			@else
				<tr>
				  <td class="blcs"></td>
				  <td class="blcs"><b>{{$total_contested}}</b></td>
				  <td class="blcs"><b>{{$total_won}}</b></td>
				  <td class="blcs"><b>{{$total_fd}}</b></td>
				  <td class="blcs"><b>{{$total_party}}</b></td>
				  <td class="blcs"><b>{{$per_valid}}</b></td>
				  <td class="blcs"><b>{{$per}}</b></td>
				  <td class="blcs">  </td>
				</tr>
			@endif
				  
			  @endforeach
			  <?php 
					if($total_valid_votes > 0){
						$per_valid = round((($all_total_party/$total_valid_votes)*100),2);
					}else{
						$per_valid = 0;
					}
					
					if($total_votes > 0){
						$per = round((($all_total_party/$total_votes)*100),2);
					}else{
						$per = 0;
					}
					?>
			  
			  
				<tr>
				  <td class="blc"><b>Grand Total:</b></td>
				  <td class="blcs"><b>{{$all_total_contested}}</b></td>
			  <td class="blcs"><b>{{$all_total_won}}</b></td>
			  <td class="blcs"><b>{{$all_total_fd}}</b></td>
			  <td class="blcs"><b>{{$all_total_party}}</b></td>
			  <td class="blcs"><b>-</b></td>
			  <td class="blcs"><b>{{$per}}</b></td>
				  <td class="blc">  </td>
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