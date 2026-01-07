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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(15 - Constituency wise detailed Result)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/constituency-wise-detailed-result-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/constituency-wise-detailed-result-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
            </p>
          </div>
        </div>
      </div>
     
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 	<div class="card-body">
					<div class="table-responsive">
						
                
                <tr>
               
                @foreach($dataArr as $key1 => $raw)
				
				<p class="contituency" style="margin-bottom: 4px;"><b>Constituency:&emsp; <span></b> {!!$key1!!}</span></p>
				</tr>
                <table id="example" class="table table-striped table-bordered" style="width:100%;">
                  
                        <tr>
                            <th rowspan="2">SL NO</th>
                            <th rowspan="2">CANDIDATE <br> NAME</th>
                            <th rowspan="2">SEX</th>
                            <th rowspan="2">AGE</th>
                            <th rowspan="2">CATEGORY</th>
                            <th rowspan="2">PARTY</th>
                            <th rowspan="2">Symbol</th>
                           <th style="text-decoration: underline;" colspan="3">Votes Secured</th>
                           <th style="text-decoration: underline;" colspan="2">% of votes secured</th>
                        </tr>


                        <tr>
                            <th>GENERAL</th>
                            <th>POSTAL</th>
                            <th>TOTAL</th>
							<th>Over total elctors in constituency</th>
                            <th>Over total votes polled in constituency</th>

                        </tr>
                
                    <tbody><?php $count=1;$totalgeneral_vote=0;$totalpostal_vote=0;$grandtotal=0; $totalelectorspercent =0; $grandelector=0; $grandpolled=0; ?>
                        @foreach($raw as $row)
                 <?php
				 $electors = $row['total_electors'];
				 $totalvotespolled = $row['total_votes'];

                  
                  $totalelectorPercent = ($electors!=0)?((($row['general_vote']+$row['postal_vote'])/$electors)*100):0;
                  $grandelector+=$totalelectorPercent;

                 $totalvotespolled=($totalvotespolled!=0)?((($row['general_vote']+$row['postal_vote'])/$totalvotespolled)*100):0;
                 $grandpolled+=$totalvotespolled;


                 ?>
                        <tr>
                            <td>{{$count}}</td>
                            <td style="text-transform: capitalize !important;">{{$row['cand_name']}}</td>
                            <td style="text-transform: capitalize;">{{ucfirst($row['cand_gender'])}}</td>
                            <td>{{$row['cand_age']}}</td>
                            <td>{{ucfirst($row['cand_category'])}}</td>
                            <td>{{$row['party_abbre']}}</td>
                            <td>{{$row['SYMBOL_DES']}}</td>
                            <td>{{$row['general_vote']}}</td>
                            <td>{{$row['postal_vote']}}</td>
                            <td>{{$row['general_vote']+$row['postal_vote']}}</td>
                            <td>{{round($totalelectorPercent,2)}}</td>
                            <td>{{round($totalvotespolled,2)}}</td>
                            
                        </tr>
						<?php $totalgeneral_vote+=$row['general_vote'];
						$totalpostal_vote+=$row['postal_vote'];
						$grandtotal+=$row['general_vote']+$row['postal_vote'];
						$count++;?>
                     
                        @endforeach
                        <tr>
                           
                            <td colspan="5"></td>
                            <td colspan="2"><b>TOTAL:</b></td>
                            <td><b>{{$totalgeneral_vote}}</b></td>
                            <td><b>{{$totalpostal_vote}}</b></td>
                            <td><b>{{$grandtotal}}</b></td>
                            <td><b>{{round($grandelector,2)}}</b></td>
                            <td><b>{{round($grandpolled,2)}}</b></td>
                        </tr>

                        <tr style="height: 10px;">    

                        </tr>
                    </tbody>
                </table>
               
                @endforeach
         
			<!--	<table class="table table-bordered">
				<tr>
                           
                  
                            <td colspan="5" style="width: 40%;"></td>
							<td style="background: #2c3b48;color: #fff;width: 10%;" colspan="2">STATE TOTAL:</td>
                            <td style="width: 10%;"><b>{{$all_Data[0]->all_evm}}</b></td>
                            <td style="width: 10%; "><b>{{$all_Data[0]->all_postal}}</b></td>
                            <td style=""><b>{{$all_Data[0]->all_total}}</b></td>
                           
                        </tr>
				</table>

-->

         
					</div>
				</div> 
    </div>
  </div>
</div>
</section>
@endsection