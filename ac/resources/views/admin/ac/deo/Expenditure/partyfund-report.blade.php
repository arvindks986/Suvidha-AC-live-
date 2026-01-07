@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
 <?php  
$st=getstatebystatecode($user_data->st_code);
$acdetails=getacbyacno($user_data->st_code,$user_data->ac_no); 
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
//	dd($pcdetails);
    ?>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Fund From Party</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
						<b>State Name:</b> 
						<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
						<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
						<b>District Name:</b> <span class="badge badge-info">{{ $distname->DIST_NAME}}</span>
                        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
									       
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
		<th>Candidate Father Name</th>
        <th>Fund From Party</th>
        <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($partyfund))
		@foreach($partyfund as $candDetails)  
			<?php
      //echo "<pre>";
			//print_R($candDetails);
      $totalpartyfund=0;
        $j++; 
		 $ac=getacbyacno($candDetails->ST_CODE,$candDetails->constituency_no);
        $totalpartyfund=$candDetails->political_fund_cash+$candDetails->political_fund_checque+$candDetails->political_fund_kind;
				?>
<tbody>
<tr>
<td>@if(!empty($ac->AC_NO))  {{ $ac->AC_NO}}-{{ $ac->AC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->candidate_father_name)) {{$candDetails->candidate_father_name}} @endif</td>
<td>@if(!empty($totalpartyfund)) {{$totalpartyfund}}  @endif </td>
<td >         <a href="{{url('/')}}/acdeo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}/{{base64_encode($candDetails->constituency_no)}}" class="btn btn-primary" target="_blank">Scrutiny Report</a>    <td>

</tr>
@endforeach 
@endif 


             </tbody>
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

