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
       <h4>Booth App - Poll Event Report</h4>
     </div>

     <div class="col-md-3 pull-right text-right">
      
      <a href="{{url($pdf_btn)}}" target="_blank" class="btn btn-primary">Download PDF</a>
      <a href="{{url($excel_btn)}}" target="_blank" class="btn btn-secondary">Download Excel</a>
         
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
          <table class="table table-bordered " id="list-table" data-page-length='50'>
           <thead>
		   
            <tr> 
              <th rowspan="2">State/UT Name</th>
              <th rowspan="2">AC NO & AC Name</th>
              <th rowspan="2">Total PS</th>
			  @if(isset($event_filter) && $event_filter=='0')
			 
              <th colspan="2">Mock Poll Done</th>
              <th colspan="2">Poll Started</th>
              <th colspan="2">Voting Started</th>
			 
              <th colspan="2">Poll End</th>
			  
			 
			  @elseif(isset($event_filter) && $event_filter=='2')
				<th colspan="2">Mock Poll Done</th>
		      @elseif(isset($event_filter) && $event_filter=='3')
				<th colspan="2">Poll Started</th>
		      @elseif(isset($event_filter) && $event_filter=='4')
				<th colspan="2">Voting Started</th>
			 
			  @elseif(isset($event_filter) && $event_filter=='6')
				<th colspan="2">Poll End</th>
			  
		      @endif
            </tr>
			
			<tr>
			@if(isset($event_filter) && $event_filter=='0')
             
			  <th>Yes</th>
              <th>No</th>
			  <th>Yes</th>
              <th>No</th>
			  <th>Yes</th>
              <th>No</th>
			 
			  <th>Yes</th>
              <th>No</th>
			 
			   
				@elseif(isset($event_filter) && $event_filter=='2')
					<th>Yes</th>
					<th>No</th>
				@elseif(isset($event_filter) && $event_filter=='3')
					<th>Yes</th>
					<th>No</th>
				@elseif(isset($event_filter) && $event_filter=='4')
					<th>Yes</th>
					<th>No</th>
				
				@elseif(isset($event_filter) && $event_filter=='6')
					<th>Yes</th>
					<th>No</th>
				
				@endif
					  
				  
            </tr>
          </thead>
          <tbody>
			@php
				$total_ps = $ps_location= $mock_poll_start = $poll_start = $total_voter = $data_sync = $poll_end = $poll_mat_rec = $poll_mat_sub = $pro_diary_sub = 0 ;
			 @endphp
			 

		  
             @if(count($results)>0)
			 
            @foreach($results as $key => $result)  
			@php
				$st_code = $result['st_code'];
				$ac_no = $result['ac_no'];
			 @endphp
			
            <tr>
              <td>{{$result['st_name']}}</td>
              <td>{{$result['ac_no']}}-{{$result['ac_name']}}</td>
              <td><a href='{{url("$prefix/booth-app-revamp/poll-event-ps-wise-report?st_code=$st_code&ac_no=$ac_no")}}' target="_blank">{{$result['total_ps']}}</a></td>
				@if(isset($event_filter) && $event_filter=='0')
				 
				  <td>{{$result['mock_poll_start']}}</td>
				  <td>{{$result['total_ps']-$result['mock_poll_start']}}</td>
				  <td>{{$result['poll_start']}}</td>
				  <td>{{$result['total_ps']-$result['poll_start']}}</td>
				  <td>{{$result['total_voter']}}</td>
				  <td>{{$result['total_ps']-$result['total_voter']}}</td>
				  
				  <td>{{$result['poll_end']}}</td>
				  <td>{{$result['total_ps']-$result['poll_end']}}</td>
				  
				
				@elseif(isset($event_filter) && $event_filter=='2')
					<td>{{$result['mock_poll_start']}}</td>
				    <td>{{$result['total_ps']-$result['mock_poll_start']}}</td>
				@elseif(isset($event_filter) && $event_filter=='3')
					<td>{{$result['poll_start']}}</td>
				    <td>{{$result['total_ps']-$result['poll_start']}}</td>
				@elseif(isset($event_filter) && $event_filter=='4')
					<td>{{$result['total_voter']}}</td>
				    <td>{{$result['total_ps']-$result['total_voter']}}</td>
				
				@elseif(isset($event_filter) && $event_filter=='6')
					<td>{{$result['poll_end']}}</td>
					<td>{{$result['total_ps']-$result['poll_end']}}</td>
				
				@endif
				
            </tr>
			@php
				$total_ps += $result['total_ps'];
				$ps_location += $result['ps_location'];
				$mock_poll_start += $result['mock_poll_start'];
				$poll_start += $result['poll_start'];
				$total_voter += $result['total_voter'];
				$data_sync += $result['data_sync'];
				$poll_end += $result['poll_end'];
				$poll_mat_rec += $result['total_received'];
				$pro_diary_sub += $result['pro_diary_sub'];
				$poll_mat_sub += $result['total_submited'];
			 @endphp
			
            @endforeach
			
			<tr>
              <td colspan="2" style="text-align: center;"><b>Total</b></td>
              <td><b>{{$total_ps}}</b></td>
			  @if(isset($event_filter) && $event_filter=='0')
			 
			  <td><b>{{$mock_poll_start}}</b></td>
			  <td>{{$total_ps-$mock_poll_start}}</td>
              <td><b>{{$poll_start}}</b></td>
			  <td>{{$total_ps-$poll_start}}</td>
              <td><b>{{$total_voter}}</b></td>
			  <td>{{$total_ps-$total_voter}}</td>
             
              <td><b>{{$poll_end}}</b></td>
			  <td>{{$total_ps-$poll_end}}</td>
			  
			  
			  @elseif(isset($event_filter) && $event_filter=='2')
				<td><b>{{$mock_poll_start}}</b></td>
			    <td>{{$total_ps-$mock_poll_start}}</td>
			  @elseif(isset($event_filter) && $event_filter=='3')
				<td><b>{{$poll_start}}</b></td>
			    <td>{{$total_ps-$poll_start}}</td>
		      @elseif(isset($event_filter) && $event_filter=='4')
				<td><b>{{$total_voter}}</b></td>
			    <td>{{$total_ps-$total_voter}}</td>
		     
			  @elseif(isset($event_filter) && $event_filter=='6')
				<td><b>{{$poll_end}}</b></td>
			    <td>{{$total_ps-$poll_end}}</td>
			 
			  @endif
            </tr>
            @else 
            <tr>
              <td colspan="6">
                No Record Found.
              </td>
            </tr>
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