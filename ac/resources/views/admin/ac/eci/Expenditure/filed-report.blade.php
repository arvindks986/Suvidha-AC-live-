@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
<?php 
 $st_code=!empty($st_code) ? $st_code : '0';
 $cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
 // echo $st_code.'cons_no'.$cons_no; die;
?>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Filed Data List</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
						<b>State Name:</b> 
						<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
						<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>AC:</b> <span class="badge badge-info">{{$acName}}</span>
                        <b></b> <a href="{{url('/eci-expenditure/statusdashboard/')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
									       
                    </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
         <th>AC No & Name</th>
         <th>Candidate Name</th>
		 <th>Party Name</th>
		 <th>Date Of Lodging Scrutiny Form</th>
		 <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($finalCandList))
		@foreach($finalCandList as $candDetails)  
			<?php
      //dd($candDetails);
	  $date = new DateTime($candDetails->created_at);
     //echo $date->format('d.m.Y'); // 31.07.2012
      $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
      $acDetails=getacbyacno($candDetails->ST_CODE,$candDetails->constituency_no);
				$j++; 
				
				?>

<tr>
<td>@if(!empty($acDetails->AC_NO)) {{ $acDetails->AC_NO}}-{{ $acDetails->AC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>
<td>  @if($candDetails->final_by_ro=='1')
<a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}/{{base64_encode($candDetails->constituency_no) }}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a>@else <span class="btn-secondary text-white btn btn-sm width-100">Partially Filed</span>
@endif</td>
</tr>
@endforeach 
@endif 

            <tbody> </tbody>
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

