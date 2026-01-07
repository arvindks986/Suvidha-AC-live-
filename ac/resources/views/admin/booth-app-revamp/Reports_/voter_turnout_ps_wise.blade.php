@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
 }
 @keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<div class="loader" style="display:none;"></div>

@include('admin/common/form-filter')

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
       <h4>Booth App - PS Wise Poll Turnout Report</h4>
     </div>

     <div class="col-md-3 pull-right text-right">
      
      <a href="{{url($pdf_btn)}}" target="_blank" class="btn btn-primary">Download PDF</a>
      <a href="{{url($excel_btn)}}" target="_blank" class="btn btn-secondary">Download Excal</a>
         
    </div>
  </div>
</div>  
</section>

@if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
  $class = 'alert-success';
}
else{
  $class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> fade in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif


<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
       <div class="random-area">

       <div class="table-responsive">
		<table align="center" class="table table-bordered  poll-table">
			<thead class="sticky">
				<tr class="turnoutbg">
					<td colspan="2" style="border-color: #49a8a4;font-size: 20px" width="35%">State Name: {{$data['st_name']}}</td>
					<td colspan="6" style="border-color: #49a8a4; font-size: 20px;" width="35%">AC Name: {{$data['ac_name']}}</td>
					<td colspan="4" style="border-color: #49a8a4; font-size: 20px" align="right" width="30%">Poll day Turn out Details: <span id="poll_turnout_percentage">{{$data['poll_turnout_percentage']}}</span>%</td>
				</tr>

			<tr> 
			  <th rowspan="2">S. No.</th>
			  <th rowspan="2">PS NO & PS Name</th>
			  <th colspan="4">Electors</th>
			  <th colspan="4">Voters</th>
			  <th rowspan="2">Total Voters in Queue</th>
			  <th rowspan="2">Poll(%)</th>
			</tr>
			
			<tr> 
			  <th>(M)</th>
			  <th>(F)</th>
			  <th>(TG)</th>
			  <th>Total</th>
			  <th>(M)</th>
			  <th>(F)</th>
			  <th>(TG)</th>
			  <th>Total</th>
			</tr>
			</thead>
		

			<tbody id="voter_turnouts">
			@if(count($data['voter_turnouts'])>0)
			@foreach($data['voter_turnouts'] as $key => $iterate_turnout)
			<tr>
				<td>{{$key + 1}}</td>
				<td>{{$iterate_turnout['ps_name_and_no']}}</td>
				<td>{{$iterate_turnout['e_male']}}</td>
				<td>{{$iterate_turnout['e_female']}}</td>
				<td>{{$iterate_turnout['e_other']}}</td>
				<td>{{$iterate_turnout['e_total']}}</td>
				<td>{{$iterate_turnout['male']}}</td>
				<td>{{$iterate_turnout['female']}}</td>
				<td>{{$iterate_turnout['other']}}</td>
				<td>{{$iterate_turnout['total']}}</td>
				<td align="center">{{$iterate_turnout['total_in_queue']}}</td>
				<td align="center">{{$iterate_turnout['percentage']}}%</td>
			</tr>
			@endforeach
			@else
			<tr align="center"><td colspan="12">No Record</td></tr>
			@endif
			</tbody>
		</table>
		</div><!-- End Of  table responsive -->  
    </div><!-- End Of intra-table Div -->   
    
    
  </div><!-- End Of random-area Div -->
  
</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 


@endsection

@section('script')

@endsection