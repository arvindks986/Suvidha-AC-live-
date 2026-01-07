@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
 <?php  
	 $st_code=!empty($st_code) ? $st_code : '';
 $cons_no=!empty($cons_no) ? $cons_no : '';
 $st=getstatebystatecode($st_code);
 $acdetails=getacbyacno($st_code,$cons_no); 
 $stateName=!empty($st) ? $st->ST_NAME : 'ALL';
 $acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
    ?>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Data Entry Start Candidate List</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>AC:</b> <span class="badge badge-info">{{ $acdetails->AC_NAME}}</span>
                        <b></b> <a href="{{url('/')}}/eci-expenditure/EciExpdashboard/"> <button type="button" id="Back" class="btn btn-primary">Back</button>
									       
                    </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>Candidate Name</th>
					<th>Candidate Name In Hindi</th>
					<th>Candidate Father Name</th>
         
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($DataentryStartCandList))
		@foreach($DataentryStartCandList as $candDetails)  
			<?php
			// dd($candDetails);
				$j++; 
				?>
<tr>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->cand_hname)) {{$candDetails->cand_hname}} @endif</td>
<td>@if(!empty($candDetails->candidate_father_name)) {{$candDetails->candidate_father_name}} @endif</td>
</tr>
@endforeach 
@endif 
<tbody>
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

