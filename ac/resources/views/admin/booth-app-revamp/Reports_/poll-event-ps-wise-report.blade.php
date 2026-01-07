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
       <h4>Booth App - Poll Event PS Wise Report</h4>
       <h5>State Name: {{$st_name}}<span style="float:right;"> AC NO & AC Name: {{$ac_name}}</span></h5>
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
          <table class="table table-bordered " id="list-table">
           <thead>
            <tr> 
              <th>Sn. No.</th>
              <th>PS NO & PS Name</th>
              <th>Poll Party Reached</th>
              <th>Mock Poll Done</th>
              <th>Poll Started</th>
              <th>Voting Started</th>
              <th>Final Data Sync</th>
              <th>Poll End</th>
            </tr>
          </thead>
          <tbody>
		  
             @if(count($results)>0)
			 
            @foreach($results as $key => $result)  
			@php
				$st_code = $result['st_code'];
				$ac_no = $result['ac_no'];
			 @endphp
			
            <tr>
              <td>{{$key +1}}</td>
              <td>{{$result['ps_no']}}-{{$result['ps_name']}}</td>
			  <td> @if($result['ps_location']) Yes @else No @endif </td>
              <td> @if($result['mock_poll_start']) Yes @else No @endif </td>             
              <td> @if($result['poll_start']) Yes @else No @endif </td>
              <td> @if($result['total_voter']) Yes @else No @endif </td>
              <td>@if($result['data_sync'] > 0) Yes @else No @endif</td>
              <td>@if($result['poll_end'] > 0) Yes @else No @endif</td>
            </tr>
					
            @endforeach
			
			
            @else 
            <tr>
              <td colspan="7">
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