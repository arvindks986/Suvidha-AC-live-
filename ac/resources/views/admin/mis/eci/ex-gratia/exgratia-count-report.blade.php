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
            <div class="col"><h4>Ex Gratia Count Report @if(session()->has('success_msg'))<div class="alert alert-success alert-dismissible">{{ session()->get('success_msg') }}</div>@endif</div>
            <div class="col">
            <p class="mb-0 text-right">
			  <a href="{{Common::generate_url('mis/list-exgratia')}}" class="btn btn-success"><i class="fa fa-home"></i>&nbsp;Home</a>
              <a href="{{Common::generate_url('mis/report-exgratia')}}" class="btn btn-warning">Ex-Gratia Detailed Report</a>
			  @if(count($listData)>0)
              <a href="{{Common::generate_url('mis/count-report-exgratia-pdf')}}" target="_blank" class="btn btn-primary"><i class="fa fa-download"></i> PDF</a>
              <!--<a href="{{Common::generate_url('mis/report-exgratia-excel')}}" target="_blank" class="btn btn-success">Download Excel</a>-->
			  @endif
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <table class="table table-bordered table-striped" style="width: 100%;" id="list-table" data-page-length="50">
			  <thead>
				<tr>
					<th>Sl.No.</th>
					<th>Name of State</th>
					<th>Ex Gratia Cases</th>
					<th>Last Updated Date</th>
					<th>Total Cases</th>
					<th>Total Pending Cases</th>
					<th>Pending Cases Due for payment to the next kin in unfortunate event of death</th>
					<th>Pending Cases Due for payment to the next kin in unfortunate event of death due to any violent acts	</th>
					<th>Pending Cases Due for payment in case permanent disability</th>
				</tr>
			  </thead>
			  <tbody>	
					@php $total_cnt=0;$total_pending=0;$total_death=0;$total_violent=0;$total_perm_dis=0;  @endphp
					@foreach($listData as $k=>$v)
					@php
						$total_cnt = $total_cnt + $v->cnt;
						$total_pending = $total_pending + $v->total_pending;
						$total_death = $total_death + $v->total_death;
						$total_violent = $total_violent + $v->total_violent_act;
						$total_perm_dis = $total_perm_dis + $v->total_permanent_disability;
					@endphp
					<tr>
					  <td>{{++$k}}</td>
					  <td>{{$v->state_name}}</td>
					  <td>@if(!empty($v->nocases) && $total_pending==0 && $v->nocases==1)No cases @else  @endif</td>
					  <td>@if(!empty($v->updated_at) && $v->updated_at<>'0000-00-00'){{date('d-M-Y',strtotime($v->updated_at))}}@endif</td>
					  <td>{{$v->cnt}}</td>
					  <td>{{$v->total_pending}}</td>
					  <td>{{$v->total_death}}</td>
					  <td>{{$v->total_violent_act}}</td>
					  <td>{{$v->total_permanent_disability}}</td>
					</tr>
					
					@endforeach
					@if(count($listData)>0 && ($user_data->role_id=='7' || $user_data->role_id=='50'))
						<tfoot>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><b>Grand Total:</b></td>
							<td><b>{{$total_cnt}}</b></td>
							<td><b>{{$total_pending}}</b></td>
							<td><b>{{$total_death}}</b></td>
							<td><b>{{$total_violent}}</b></td>
							<td><b>{{$total_perm_dis}}</b></td>
						</tr>
						</tfoot>
					@endif
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