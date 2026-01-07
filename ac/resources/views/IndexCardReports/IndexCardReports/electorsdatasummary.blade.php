@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'ELECTORS DATA SUMMARY')
@section('content')

@php
  if(Auth::user()->designation == 'ROAC'){
    $prefix   = 'roac';
  }else if(Auth::user()->designation == 'CEO'){
    $prefix   = 'acceo';
  }else if(Auth::user()->role_id == '27'){
    $prefix   = 'eci-index';
  }else if(Auth::user()->role_id == '7'){
    $prefix   = 'eci';
  }
@endphp


<?php  $st=getstatebystatecode($st_code);   ?>
<style>
  .bold{
    padding: 12px 0px 0px 30px !important;
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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(6 - ELECTORS DATA SUMMARY)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/electorsdatasummary-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/electorsdatasummary-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">

    <table class="table table-bordered table-striped" style="width: 100%;">
      <thead>
        <tr>
          <th></th>
          <th colspan="3" class="bolds" style="text-align: center;">TYPE OF CONSTITUENCY</th>
          <th></th>
        </tr>
        <tr>
          <th class="bolds blc"></th>
          <th class="bolds blc">GEN</th>
          <th class="bolds blc">SC</th>
          <th class="bolds blc">ST</th>
          <th class="bolds blc">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bolds">1. NO. OF CONSTITUENCIES
          </td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalscac']) ? ($electorsvotersdataNew['SC']['totalscac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalstac']) ? ($electorsvotersdataNew['ST']['totalstac']) :0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) +(isset($electorsvotersdataNew['SC']['totalscac'])? ($electorsvotersdataNew['SC']['totalscac']) :0) + (isset($electorsvotersdataNew['ST']['totalstac'])?$electorsvotersdataNew['ST']['totalstac']:0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">2. ELECTORS (including SERVICE VOTERS)
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['maleElectors']) ? ($electorsvotersdataNew['SC']['maleElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['maleElectors']) ? ($electorsvotersdataNew['ST']['maleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0)+(isset($electorsvotersdataNew['SC']['maleElectors'])? ($electorsvotersdataNew['SC']['maleElectors']):0)+(isset($electorsvotersdataNew['ST']['maleElectors'])? ($electorsvotersdataNew['ST']['maleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors'])? ($electorsvotersdataNew['GEN']['femaleElectors']):0 )}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']): 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['femaleElectors']) ? ($electorsvotersdataNew['ST']['femaleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors']) ? ($electorsvotersdataNew['GEN']['femaleElectors']) :0)+ (isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']):0)+(isset($electorsvotersdataNew['ST']['femaleElectors'])? ($electorsvotersdataNew['ST']['femaleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['thirdElectors'])? ($electorsvotersdataNew['SC']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['thirdElectors'])? ($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']):0)+ (isset($electorsvotersdataNew['SC']['thirdElectors'])?($electorsvotersdataNew['SC']['thirdElectors']):0)+(isset($electorsvotersdataNew['ST']['thirdElectors'])?($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d.TOTAL</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])? ($electorsvotersdataNew['GEN']['totalElectors']):0)+ (isset($electorsvotersdataNew['SC']['totalElectors']) ? ($electorsvotersdataNew['SC']['totalElectors']):0)+ (isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">3. ELECTORS WHO VOTED
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
           <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])? ($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)+(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
        </tr>

        <tr>
            <td class="bold">d. POSTAL<span style="font-weight: normal;"></span>
          <!-- <td class="bold">d. POSTAL <span style="font-weight: normal;"> (Details given in Annxure-1)</span> -->
          </td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
        </tr>
		
		
		<tr>
          <td class="bold">e.TEST VOTES </td>
          <td>{{(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
        </tr>
		
		
		
		
        <tr>
          <td class="bold">f.TOTAL</td>
		   <?php 

		  
		  $totalgen = (isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters']) ? ($totalvoteNew['GEN']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0);
		  
		  $totalsc = (isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0) + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0);
		  
		  $totalst = (isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0) + (isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0);
		  
		  $totalvoters = (isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])? ($totalvoteNew['SC']['totalOtherVoters']):0) + (isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0) +(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0) + +(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0) + +(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)  + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0);
		  
		  
		  ?>
          <td>{{$totalgen}}</td>
		  <td>{{$totalsc}}</td>
          <td>{{$totalst}}</td>
          <td>
            {{$totalvoters}}
        </td>
        </tr>
        <tr>
          <!-- <td class="bold">PROXY <span style="font-weight: normal;">(already included in 3.a/3.b)</span> -->
             <td class="bold">PROXY <span style="font-weight: normal;"></span>
          </td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)+(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)+(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">4. OVERSEAS ELECTORS
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}
          </td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
           <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d.TOTAL</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>
            {{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">5. OVERSEAS ELECTORS WHO VOTED
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
           <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?$totalvoteNew['GEN']['overseasthirdvoters']:0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d.TOTAL</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">6. REJECTED POSTAL VOTES
          </td>
        </tr>
        <tr>
          <td class="bold">a. NO OF  REJECTED POSTAL VOTES
          </td>
          <td>{{(isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)}}</td>
        </tr>
        <tr>
          <!-- <td class="bold">b. PERCENTAGE <span style="font-weight: normal;">(to Postal <br> Votes)</td> -->
            <td class="bold">b. AS % OF TOTAL POSTAL VOTES</td>

          <td>
            {{round((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)/(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)*100,2)}}
          </td>
          <?php if(isset($totalpostalvoteNew['SC']['postaltotalreceived']) && ($totalpostalvoteNew['SC']['postaltotalreceived'] > 0 )) { ?>
          <td>



          {{round((isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)/(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)*100,2)}}

        </td>
      <?php } else { ?>
        <td>0</td>

      <?php } ?>

      <?php if(isset($totalpostalvoteNew['ST']['postaltotalreceived']) && ($totalpostalvoteNew['ST']['postaltotalreceived'] > 0 )) { ?>

          <td>
          {{round((isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)/(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)*100,2)}}

        </td>
         <?php } else { ?>
            <td>0</td>

          <?php } ?>

          <td>{{round(((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0))/((isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0))*100,2)}}</td>
        </tr>
        <tr>
          <td class="bold">c. VOTES REJECTED FROM <br>EVM <span style="font-weight: normal;">(TEST VOTES+REJECTED DUE TO OTHER <br> REASON)</span>
          </td>
          <td>{{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+
          (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)+(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)+(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds">7. NOTA VOTES <span style="font-weight: normal;">(POSTAL + EVM)</span></td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
        </tr>
        <tr>
          <!-- <td class="bolds">8. VALID VOTES <span style="font-weight: normal;">(EXCLUDING NOTA VOTES) <br> 3.f-(6.a+6.c+7)</span> -->
            <td class="bolds">8. VALID VOTES 
          </td>


          <?php $total1 = ($totalgen)-((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)
          +((isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)
          )+(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)); ?>

          <?php $total2 = ($totalsc)-((isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+((isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0))
          +(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)); ?>

          <?php $total3 = ($totalst)-((isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)+((isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?
          ($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0))+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0) + (isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)); ?>

          <td>
            {{ $total1 }}

          </td>
          <td>
             {{ $total2 }}

          </td>
          <td>
             {{ $total3 }}

          </td>
          <td>{{$total1+$total2+$total3}}</td>

        </tr>
        <tr>
          <td class="bolds">9. POLL PERCENTAGE
          </td>
          <?php if(isset($electorsvotersdataNew['GEN']['totalElectors']) && ($electorsvotersdataNew['GEN']['totalElectors'] > 0)) { ?>
            <td>{{round($totalgen/(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)*100,2)}}</td>
          
          <?php } else { ?>
          <td>0</td>
        <?php } ?>

          <?php if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) { ?>
          <td>{{round($totalsc/$electorsvotersdataNew['SC']['totalElectors']*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($electorsvotersdataNew['ST']['totalElectors']) && ($electorsvotersdataNew['ST']['totalElectors'] > 0)) { ?>
          <td>{{round($totalst/(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>


          <td>{{round(($totalvoters)/((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)+(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)
            +(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0))*100,2)}}</td>
        </tr>
        <tr>
          <td class="bolds">10. NO. OF POLLING STATIONS
          </td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)
            +(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)
            +(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
        </tr>
		
		<?php $totalelectors = (isset($electorsvotersdataNew['GEN']['totalElectors'])? ($electorsvotersdataNew['GEN']['totalElectors']):0)+ (isset($electorsvotersdataNew['SC']['totalElectors']) ? ($electorsvotersdataNew['SC']['totalElectors']):0)+ (isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0); 
		
		$totalpollingstations = (isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)
            +(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)
            +(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0);
		
		?>
        <tr>
          <td class="bolds">11. AVERAGE NO. OF <br> ELECTORS PER POLLING <br>STATION 
          </td>
          <?php
                if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { 


              $total4 = (isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0);

            }else{
              $total4 = 0;
            }
            if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) {
              $total5 = (isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0);
            } else{
              $total5 = 0;
            }

            if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation'] > 0)) {
              $total6 = (isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0);
            } else{
              $total6 = 0;
            }
            

          ?>
          <?php if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { ?>

          <td>{{round((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($totalvoteNew['SC']['totalpollingstation']) && ($totalvoteNew['SC']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
        <?php if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
		
		@if($totalpollingstations > 0)
				<td>{{round($totalelectors/$totalpollingstations,0)}}</td>
		@else
		 <td>0</td>	
		@endif
         
        
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
