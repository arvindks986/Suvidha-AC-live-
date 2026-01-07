@extends('admin.layouts.ac.theme')
@section('content')

@include('admin/common/form-filter')

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col-md-3  pull-right  text-right">
      @if(count($results)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif    
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





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
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
<div class="alert <?php echo $class; ?> in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif
     <div class="page-contant">
       <div class="random-area">
        <br>

        <div class="table-responsive">
          <table class="table table-bordered list-table-remove" id="my-list-table"> 
           <thead>
            <tr> 
              <th>State Name</th>
              <th>AC No & Name</th>
              <th>PS No & Name</th>
              <th>Start</th>
              <th>RAMP</th>
                          <th>Toilet Facility</th>
                          <th>Exit Door</th>
                          <th>Furniture</th>
                          <th>Light</th>
                          <th>Drinking Water</th>
            </tr>

          </thead>
          @if(count($results)>0)
          <tbody>   
            @foreach($results as $result)
            <tr>
              <td>{{$result['st_name']}}</td>
              <td>{{$result['ac_name']}}</td>
              <td>{{$result['ps_name']}}</td>
              <td>{{$result['infra_start']}}</td>
              <td>{{$result['infra_ramp']}}</td>
              <td>{{$result['infra_toilet_facility']}}</td>
              <td>{{$result['infra_exit_door']}}</td>
              <td>{{$result['infra_furniture']}}</td>
              <td>{{$result['infra_light']}}</td>
              <td>{{$result['infra_drinking_water']}}</td>
            </tr>
            @endforeach
         
          @else
          
            <tr>
              <td colspan="6">
                @if(isset($no_record))
                {{$no_record}}
                @else
                No Record Found
                @endif
              </td>
            </tr>
          
          @endif
          <tbody>
        </table>
      </div><!-- End Of  table responsive -->  
    </div><!-- End Of intra-table Div -->   


  </div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 


@endsection
@section("script")
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