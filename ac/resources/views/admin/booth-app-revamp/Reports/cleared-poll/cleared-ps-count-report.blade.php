@extends('admin.layouts.ac.theme')
@section('bradcome', 'Booth App - PS Location Mapping')
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
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php //if($button['target']){?> target='_blank' <?php //} ?> >{{ $button['name'] }}</a></span>
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
			  <th>SNo.</th>
              <th>State/UT Name</th>
              <th>Total Cleared Data</th>
            </tr>
          </thead>
          <tbody>
			@php
				$total_ps = $total_cleared = 0;$i=1;
			 @endphp
			 

     
			 
            @foreach($results as $result) 
            <tr>
			<td>{{$i}}</td>
             <td>
              <a href="<?php echo $result['href'] ?>"><span>{!! $result['label'] !!}</span></a>
            </td> 
            <td>
              @if($result['total_cleared'] > 0)<a href="<?php echo $result['href'] ?>"><span>{!! $result['total_cleared'] !!} </span></a>@else {!!$result['total_cleared']!!} @endif
            </td>
            </tr>
			@php
			$i++;
				$total_ps += $result['total_ps'];
				$total_cleared +=  $result['total_cleared'];
			 @endphp
			
            @endforeach
  
          </tbody>
            <tr>
			  <td>&nbsp;</td>
              <td style="text-align: center;"><b>Grand Total</b></td>
              <td><b>{{$total_cleared}}</b></td>
              
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