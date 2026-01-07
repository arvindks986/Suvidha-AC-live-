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

<section class="statistics pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4></h4>
  </div>

   

  </div>
</div>  
</section>



<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        
            
            <span class="pull-right" style="margin-right: 10px;">
            <span><b></b></span>
            <span class="badge badge-info"></span>

            </span>
            
      
      </div>
    </div>
  </div>
</section>





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
       <div class="random-area">


         <div class="table-responsive">
          <table class="table table-bordered " id="my-list-table" data-page-length='50'>
           <thead>
		   
            <tr> 
              <th>S. No.</th>
			  <th>AC No.</th>
              <th>PS No</th>
              <th>PS Name</th>
              <th>Status</th>
              
              
              
            </tr>
          </thead>
          <tbody>  
            
			<?php $i =1; ?>
		  @if(count($result)>0)
		  @foreach($result as $value)
              
			  
            <tr>
              <td>{{$i}}</td>
              <td>{{$value['ac_no']}}</td>
              <td>{{$value['ps_no']}}</td>
              <td>{{$value['PS_NAME_EN']}}</td>
			  @if($value['is_connected'] == 0)
              <td><span style="color:red;">Disconnected</span></td>
			  @else{
			  <td><span style="color:green;">connected</span></td>
			  }
			  @endif
              
             
            </tr>
			<?php $i++; ?>
			@endforeach
			
			@else 
            <tr>
              <td colspan="8">
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
<script type="text/javascript">
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 500,
        "aaSorting": []
      });
    }
  });
</script>
@endsection