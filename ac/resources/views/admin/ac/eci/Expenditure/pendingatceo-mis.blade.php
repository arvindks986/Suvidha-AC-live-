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
?> 
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col-md-5 mt-2 mb-2"><h5 class="mr-auto">Pending At CEO : {{$count}}</h5></div> 
                <div class="col-md-7 mt-2 mb-2 text-right"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>AC:</b> <span class="badge badge-info">{{ $acName}}</span>
                        <a href="{{url('/eci-expenditure/pendingatceoPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/eci-expenditure/pendingatceoEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                        <b></b><a href="{{url('/eci-expenditure/mis-officer')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
<section class="mt-5">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12">
<div class="card text-left" style="width:100%;">
<!--SELECT CANDIDATE-->
<div class="card-body" id="demo" class="collapse show">  
	<table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
		<thead>
			<tr>
				<th>AC No & Name</th>
				<th>Candidate Name</th>
				<th>Party Name</th>
				<th>Last Date of Submission</th>
				<th>Date of Scrutiny Report Submission</th>
				<th>Date of Lodging A/C By Candidate</th>
				<th>Date of Sending to the CEO</th>
				<th>Date of Receipt By CEO</th>
				<th>Action</th>
			</tr>
		</thead>
		<?php $j = 0; ?>
		@if(!empty($finalbyceoCandList))
		@foreach($finalbyceoCandList as $candDetails)  
		<?php
	   
		$ac = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
		 $acDetails=getacbyacno($candDetails->ST_CODE,$candDetails->constituency_no);
       $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
		$j++;
	    
		?>
<tr>
	<td>@if(!empty($ac->AC_NO))  {{ $ac->AC_NO}}-{{ $ac->AC_NAME}} @endif</td>
	<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
	<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
	<td>@if(!empty($lodgingDate) && strtotime($lodgingDate) >0 ) {{ $lodgingDate }} @else {{ '22-06-2019'}} @endif</td>
<td>@if(!empty($candDetails->report_submitted_date) && strtotime($candDetails->report_submitted_date) > 0)  {{ date('d-m-Y',strtotime($candDetails->report_submitted_date))}}  @else {{ 'N/A'}} @endif</td>
<td>@if(strtotime($candDetails->date_orginal_acct) > 0 && ($candDetails->date_orginal_acct !='0000-00-00'))  {{ date('d-m-Y',strtotime($candDetails->date_orginal_acct))}} @else {{ 'N/A'}} @endif</td>
<td>@if(strtotime($candDetails->date_of_sending_deo) > 0 && ($candDetails->date_of_sending_deo !='0000-00-00'))  {{  date('d-m-Y',strtotime($candDetails->date_of_sending_deo))}} @else {{ 'N/A'}} @endif</td>
<td>@if(strtotime($candDetails->date_of_receipt) > 0  && ($candDetails->date_of_receipt !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->date_of_receipt))}}  @else {{ 'N/A'}} @endif</td>    
<td>  @if($candDetails->final_by_ro=='1')
<a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a>@else <span class="btn-secondary text-white btn btn-sm width-100">Partially Filed</span>
                @endif</td>
</tr>
		@endforeach 
		@endif 
		<tbody>
		</tbody>
	</table>
</div>
</div>
<!--END OF SELECT CANDIDATE-->
</div>
</div>
</div>
</section>
</main>

@endsection


