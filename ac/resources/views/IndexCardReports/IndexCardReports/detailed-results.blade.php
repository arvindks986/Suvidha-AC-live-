@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'Detailed Results')
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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(10 - Detailed Results)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/detailed-results-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/detailed-results-xls/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
				  <th colspan="7"></th>
				  <th colspan="3" style="text-decoration: underline;text-align: center;" class="bolds">VALID VOTES POLLED </th>
				  <th rowspan="2" class="blc bolds">% VOTES <br> POLLED</th>
				</tr>
				<tr>
				  <td class="blc bolds"></td>
				  <th class="blc bolds">CANDIDATE NAME </th>
				  <th class="blc bolds">SEX</th>
				  <th class="blc bolds">AGE</th>
				  <th class="blc bolds">CATEGORY</th>
				  <th class="blc bolds">PARTY</th>
				  <th class="blc bolds">SYMBOL</th>
				  <th class="blc bolds">GENERAL</th>
				  <th class="blc bolds">POSTAL</th>
				  <th class="blc bolds">TOTAL</th>
				</tr>
			  </thead>
			  <tbody>
			  
				@foreach($dataArr as $key => $data)
					<tr>
					  <th class="bolds" colspan=""><b>Constituency</b> </th>
					  <td colspan="10">{!! $key !!}</td></td>
					</tr>
					@php $i =1; $per = 0; $gen_total = $postal_total = $all_total = $total_electors = $total_votes =0;
					@endphp
					
					@foreach($data as $raw)
					
						<?php 
						$gen_total += $raw['general_vote'];
						$postal_total += $raw['postal_vote'];
						$all_total += $raw['cand_total_vote'];
						$total_electors = $raw['total_electors'];
						$total_votes = $raw['total_votes'];
						
						
						if($raw['total_votes'] > 0){
							$per = round((($raw['cand_total_vote']/$raw['total_votes'])*100),2);
						}						
						?>					
						<tr>
						  <td></td>
						  <td>{{$i}} {{ucwords(strtolower($raw['cand_name']))}}</td>
						  <td>{{strtoupper($raw['cand_gender'])}}</td>
						  <td>{{$raw['cand_age']}}</td>
						  <td>{{strtoupper($raw['cand_category'])}}</td>
						  <td>{{$raw['party_abbre']}}</td>
						  <td>{{$raw['SYMBOL_DES']}}</td>
						  <td>{{$raw['general_vote']}}</td>
						  <td>{{$raw['postal_vote']}}</td>
						  <td>{{$raw['cand_total_vote']}}</td>
						  <td>{{$per}}</td>
						</tr>
						@php $i++; @endphp
						
					@endforeach
					
					<?php
					if($total_electors > 0){
							$pertotal = round((($total_votes/$total_electors)*100),2);
						}else{
							$pertotal = 0;
						}					
						?>
					<tr>
					  <td colspan="3" class="blcs bolds">TURN OUT</td>
					  <td class="blcs"></td>
					  <td colspan="3" class="blcs bolds">TOTAL:</td>
					  <td class="blcs bolds">{{$gen_total}}</td>
					  <td class="blcs bolds">{{$postal_total}}</td>
					  <td class="blcs bolds">{{$all_total}}</td>
					  <td class="blcs bolds">{{$pertotal}}</td>
					</tr>
					
				@endforeach
				<tr>
				  <td colspan="5" class="blcs bolds" style="text-align: right;">GRAND TOTAL:</td>
				  <td class="blcs bolds" colspan="2"></td>
				  <td class="blcs bolds">{{$all_state_Data[0]->all_state_total - $all_state_Data[0]->all_state_postal}}</td>
				  <td class="blcs bolds">{{$all_state_Data[0]->all_state_postal}}</td>
				  <td class="blcs bolds">{{$all_state_Data[0]->all_state_total}}</td>
				  <td class="blcs bolds"></td>
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