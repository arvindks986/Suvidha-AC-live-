@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'ANNXURE - 1')
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

<style>
  .blcs{
    font-weight: bold;
  }
  .boldn{
      font-weight: bold;
      padding: 12px 0px 0px 30px;
    }  
</style>
<?php  $st=getstatebystatecode($st_code);   ?>





<section class="">
 <div class="container-fluid">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4>Election Commission Of India, General Elections, {{getElectionYear()}}<br>ANNXURE - 1 (ELECTORS DATA SUMMARY )</h4></div>
             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="{!! url('/'.$prefix.'/annxure-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
       <a href="{!! url('/'.$prefix.'/annxure-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">
  <div class="table-responsive">
    <table class="table table-striped table-bordered" style="width: 100%;">
      <thead>
        <tr>
          <td rowspan="2"></td>
          <th colspan="3" class="bolds" style="text-align: center;">TYPE OF CONSTITUENCY</th>
          <th></th>
        </tr>
        <tr>
          <th class="bolds blc">GEN</th>
          <th class="bolds blc">SC</th>
          <th class="bolds blc">ST</th>
          <th class="bolds blc">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1. NO. OF CONSTITUENCIES
          </td>
          <td>{{isset($actypecountNew['GEN']['genac']) ? $actypecountNew['GEN']['genac'] : 0 }}</td>
         
          <td>{{isset($actypecountNew['SC']['scac']) ? $actypecountNew['SC']['scac']:0}}</td>
         
          <td>{{isset($actypecountNew['ST']['stac']) ? $actypecountNew['ST']['stac'] : 0}}</td>
          <td>{{(isset($actypecountNew['GEN']['genac']) ? $actypecountNew['GEN']['genac'] : 0)+ (isset($actypecountNew['SC']['scac'])? $actypecountNew['SC']['scac'] :0 )
          + (isset($actypecountNew['ST']['stac'])?$actypecountNew['ST']['stac']:0)}}</td>
        </tr>
        <tr>
          <td colspan="4">2. POSTAL VOTES</td>
        </tr>
        <tr>
          <td class="bold"> &nbsp;&nbsp;&nbsp;a.&nbsp;&nbsp; Postal Votes(For Service Voters <br> Under sub-Section(8) of Section 20 of <br> R.P. Act,1950)
          </td>
          <td>{{isset($postalvoteNew['GEN']['postalvotesec8'])?$postalvoteNew['GEN']['postalvotesec8'] : 0}}</td>
          <td>{{isset($postalvoteNew['SC']['postalvotesec8'])? $postalvoteNew['SC']['postalvotesec8']:0}}</td>
          <td>{{isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8'] : 0}}</td>
          <td>{{(isset($postalvoteNew['GEN']['postalvotesec8'])? $postalvoteNew['GEN']['postalvotesec8']:0)+(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8']:0) +(isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8']:0)}}</td>
        </tr>
        <tr>
          <td class="bold">&nbsp;&nbsp;&nbsp;b.&nbsp;&nbsp; Postal Votes(For Govt. Servants <br> on election duty(including all Police <br>Pesonnel, drivers, conductors, <br> cleaners) and Absentee Voters.
          </td>
          <td>{{isset($postalvoteNew['GEN']['postalvoteservice']) ? $postalvoteNew['GEN']['postalvoteservice']: 0}}</td>
          <td>{{isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] : 0}}</td>
          <td>{{isset($postalvoteNew['ST']['postalvoteservice']) ? $postalvoteNew['ST']['postalvoteservice'] : 0}}</td>
          <td>{{(isset($postalvoteNew['GEN']['postalvoteservice']) ?$postalvoteNew['GEN']['postalvoteservice'] :0) +(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice']:0)+(isset($postalvoteNew['ST']['postalvoteservice'])? $postalvoteNew['ST']['postalvoteservice']:0)}}</td>
        </tr>
        <tr>
          <td class="blcs">TOTAL POSTAL VOTES</td>
          <td class="blcs">{{(isset($postalvoteNew['GEN']['postalvotesec8']) ?$postalvoteNew['GEN']['postalvotesec8']:0) +(isset($postalvoteNew['GEN']['postalvoteservice']) ? $postalvoteNew['GEN']['postalvoteservice'] :0)}}</td>
          <td class="blcs">{{(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8']:0)+(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] :0) }}</td>
          <td class="blcs">{{(isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8'] :0)+(isset($postalvoteNew['ST']['postalvoteservice'])? $postalvoteNew['ST']['postalvoteservice'] :0) }}</td>
          <td class="blcs">{{(isset($postalvoteNew['GEN']['postalvotesec8']) ? $postalvoteNew['GEN']['postalvotesec8'] :0) +(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8'] : 0) + (isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8']:0) +(isset($postalvoteNew['GEN']['postalvoteservice'])? $postalvoteNew['GEN']['postalvoteservice']:0)+(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] : 0)+ (isset($postalvoteNew['ST']['postalvoteservice'])?$postalvoteNew['ST']['postalvoteservice']:0) }}</td>
        </tr>
      </tbody>
    </table>
  
  </div>
  </div>

  </div>

  </div>

  </div>

<section>
  @endsection


