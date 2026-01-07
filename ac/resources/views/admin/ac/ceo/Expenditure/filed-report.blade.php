@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Pending Report')
@section('description', '')
@section('content')
 <?php  
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($user_data->st_code);
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$distdetails=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
$distName=!empty($distdetails) ? $distdetails->DIST_NAME : 'ALL';
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
												<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>District:</b> <span class="badge badge-info">{{ $distName }}</span>
                                               <b></b><a href="{{url('/acceo/statusExpdashboard')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>

									       
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
		<th>Date Of Lodging</th>
         <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($finalCandList))
		@foreach($finalCandList as $candDetails)  
			<?php
      //dd($candDetails);
      $acDetails=getacbyacno($candDetails->ST_CODE,$candDetails->constituency_no);
		 $date = new DateTime($candDetails->created_at);
     //echo $date->format('d.m.Y'); // 31.07.2012
     $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
				$j++; 
				?>

<tr>
<td>@if(!empty($acDetails->AC_NO)) {{ $acDetails->AC_NO}}-{{ $acDetails->AC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>
<td>@if(($candDetails->final_by_ro=='1'))
                <a href="{{url('/')}}/acceo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}/{{base64_encode($candDetails->constituency_no) }}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a>  @else
                                      N/A
                                      @endif</td>
</tr>
@endforeach 
@endif 

             <tbody></tbody>
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

