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
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 

  </div>
</div>  
</section>
@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
            <?php $but = explode(':',$button); ?>
            <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>

            </span>
            
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

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
              <th>State/UT Name</th>
              <th>Const No-Name</th>
              <th>Total PS</th>
              <th>Total Location</th>
              <th>Mapped Location</th>              
              <th>Unmapped Location</th>
			  <th>Unmapped PS</th>
            </tr>
          </thead>
          <tbody>
			@php
				$total_ps = $location = $total_ml = $total_uml = $unmapped_ps = 0;
			 @endphp
			 

     
			 
            @foreach($results as $result) 
            <tr>
             <td>
              {!! $result['label'] !!}
            </td> 
            <td>
              {!! $result['const_no'] !!}-{!! $result['const_name'] !!}
            </td> 
            <td>
              {!! $result['total_ps'] !!}
            </td> 
            <td>
              {!! $result['total_location'] !!} 
            </td> 
            <td>
              {!! $result['mapped_location'] !!}
            </td> 
            <td>
              @if($result['unmapped_location'] >0)<a href="<?php echo $result['href'] ?>"><span>{!! $result['unmapped_location'] !!}</span></a>@else {!! $result['unmapped_location'] !!} @endif
            </td> 
			<td>
			@if($result['unmapped_ps'] >0)<a href="<?php echo $result['hrefps'] ?>"><span>{!! $result['unmapped_ps'] !!}</span></a>@else {!! $result['unmapped_ps'] !!} @endif
            </td>
            </td>  
            </tr>
			@php
				$total_ps += $result['total_ps'];
				$location +=  $result['total_location'];
				$total_ml += $result['mapped_location'];
				$total_uml += $result['unmapped_location'];
				$unmapped_ps += $result['unmapped_ps'];
			 @endphp
			
            @endforeach
  
          </tbody>
            <tr>
              <td style="text-align: center;"><b>Grand Total</b></td>
              <td><b></b></td>
              <td><b>{{$total_ps}}</b></td>
               <td><b>{{$location}}</b></td>
              <td><b>{{$total_ml}}</b></td>
              <td><b>{{$total_uml}}</b></td>
			  <td><b>{{$unmapped_ps}}</b></td>
             
            </tr>
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