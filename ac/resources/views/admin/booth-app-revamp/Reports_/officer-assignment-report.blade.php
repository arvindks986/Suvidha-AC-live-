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
       <h4>Booth App - Officer Assignment Report</h4>
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
              <th>S. No.</th>
              <th>State/UT Name</th>
              <th>AC NO & AC Name</th>
              <th>Total PS</th>
              <th>BLO Assigned in PS</th>
              <th>Polling Party Assigned in PS</th>
            </tr>
          </thead>
          <tbody>
			@php
				$total_ps = $total_blo = $total_pro = 0;
			 @endphp
			 

		  
             @if(count($data)>0)
			 
            @foreach($data as $key => $result)  
            <tr>
              <td>{{$key+1}}</td>
              <td>{{$result->st_name}}</td>
              <td>{{$result->AC_NO}}-{{$result->ac_name}}</td>
              <td>{{$result->total_ps}}</td>
              <td>{{$result->total_blo}}</td>
              <td>{{$result->total_pro}}</td>
            </tr>
			@php
				$total_ps += $result->total_ps;
				$total_blo += $result->total_blo;
				$total_pro += $result->total_pro;
			 @endphp
			
            @endforeach
			
			<tr>
              <td colspan="3" style="text-align: center;"><b>Total</b></td>
              <td><b>{{$total_ps}}</b></td>
              <td><b>{{$total_blo}}</b></td>
              <td><b>{{$total_pro}}</b></td>
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