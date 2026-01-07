@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col-md-5"><h2 class="mr-auto">Not Stared Data List</h2></div> 
                   <div class="col-md-7"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>AC:</b> <span class="badge badge-info">{{ $acName }}</span>
                        <a href="{{url('/eci-expenditure/EciNotfiledCandidateMISPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/eci-expenditure/EciNotfiledCandidateMISEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                        <b></b><a href="{{url('/eci-expenditure/mis-officer')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
									       
                    </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
        <th>AC No & Name</th>
          <th>Candidate Name</th>
		  <th>Party Name</th>
          <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($Ecinotstarted))
		@foreach($Ecinotstarted as $candDetails)  
		<?php
      //dd($candDetails);
		 $date = new DateTime($candDetails->created_at);
     //echo $date->format('d.m.Y'); // 31.07.2012
     $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
     $ac=getacbyacno($candDetails->st_code,$candDetails->ac_no); 
		 $j++; 
		?>

<tr>
<td>@if(!empty($ac->AC_NO)) {{$ac->AC_NO}}-{{ $ac->AC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>  <span class="btn-secondary text-white btn btn-sm width-100">Not-Started</span>
                 </td>
</tr>
@endforeach 
@endif 

<tbody>   </tbody>
            </table>
           </div> <!-- end responcive-->
          </div> <!-- end card-body-->
        </div>
      </div>
     </div>
   	</div>
  </section>
	
	</main>
@endsection

