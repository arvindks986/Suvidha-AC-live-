@extends('admin.central.common.theme')
@section('title', 'Descriptive Election Period Report')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => Common::generate_url('mis/list-exgratia'),
    'name' => 'List Ex-Gratia'
  ]; 
  ?>
@section('content')


<style>	

.bolds{
	font-weight: bold;
}
</style>
<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class="card-header">
          <div class=" row">
            <div class="col"><h4>Ex-Gratia Detailed Report @if(session()->has('success_msg'))<div class="alert alert-success alert-dismissible">{{ session()->get('success_msg') }}</div>@endif</div>
            <div class="col">
            <p class="mb-0 text-right">
			  <a href="{{Common::generate_url('mis/list-exgratia')}}" class="btn btn-success"><i class="fa fa-home"></i>&nbsp;Home</a>
              <a href="{{Common::generate_url('mis/exgratia-count-report')}}" class="btn btn-warning">Ex-Gratia Count Report</a>
			  @if(count($listData)>0)
              <a href="{{Common::generate_url('mis/report-exgratia-pdf')}}" target="_blank" class="btn btn-primary"><i class="fa fa-download"></i> PDF</a>
			  @endif
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <table class="table table-bordered table-striped" style="width: 100%;" id="list-table">
			  <thead>
				<tr>
					<th>Sl.No.</th>
					<th>Name of State and District</th>
					<th>Name of Election</th>
					<th>Name of State who has to pay ex-gratia compensation</th>
					<th>Name of polling personnel</th>
					<th>Designation</th>
					<th>Parent Department</th>
					<th>Address</th>
					<th>Contact number</th>
					<th>Detail of Death/Injury</th>
					<th>Reason of Death/Injury</th>
					<th>Place of Death/Injury</th>
					<th>Date of Death/Injury</th>
					<th>Ex-gratia application date</th>
					<th>Ex gratia Payment amount</th>
					<th>Payment Date</th>
					<th>Ex-gratia Status</th>
					<th>Granted/Rejection Date</th>
					<th>Reason for Pending</th>
					<th>Ex Gratia Case Details</th>
				</tr>
			  </thead>
			  <tbody>	
					@foreach($listData as $k=>$v)
					<tr>
					  <td>{{++$k}}</td>
					  <td>{{$v->ST_NAME}} @if($v->DIST_NAME)- {{$v->DIST_NAME}}@endif</td>
					  <td>{{$v->election_year}} - {{(!empty($v->election_type))?$elections[$v->election_type]:''}}</td>
					  <td>{{$v->ST_NAME}}</td>
					  <td>{{$v->applicant_name}}</td>
					  <td>{{$v->applicant_designation}}</td>
					  <td>{{$v->applicant_parent_department}}</td>
					  <td>{{$v->applicant_address}}</td>
					  <td>{{$v->contact_no}}</td>
					  <td>{{(!empty($v->injury_details))?$injury[$v->injury_details]:''}}</td>
					  <td>{{(!empty($v->accident_reason))?$reason[$v->accident_reason]:''}}</td>
					  <td>{{ucfirst($v->accident_place)}}</td>
					  <td>@if(!empty($v->accident_date) && $v->accident_date != '0000-00-00'){{date('d-M-Y',strtotime($v->accident_date))}}@endif</td>
					  <td>@if(!empty($v->created_at) && $v->created_at != '0000-00-00'){{date('d-M-Y',strtotime($v->created_at))}}@endif</td>
					  <td>{{$v->payment_amount}}</td>
					  <td>@if(!empty($v->date_of_payment) && $v->date_of_payment != '0000-00-00'){{$v->date_of_payment}}@endif</td>
					  <td>{{ucfirst($v->application_status)}}</td>
					  <td>@if(!empty($v->date_of_action) && $v->date_of_action != '0000-00-00'){{$v->date_of_action}}@endif</td>
					  <td>{{$v->reason_for_pending}}</td>
					  <td>{{$v->case_details}}</td>
					</tr>
					
					@endforeach
			  </tbody>
			</table>			
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@endsection
@section('script')
<script>
<?php if(session()->has('success_msg')){?>
	setTimeout(function(){ $(".alert-dismissible").hide(); }, 3000);
<?php }?> 
</script>
@endsection