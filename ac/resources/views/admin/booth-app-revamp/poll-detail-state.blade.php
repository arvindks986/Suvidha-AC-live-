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
          <table class="table table-bordered"> 
           <thead>
            <tr> 
              <th>State Name</th>
              <th>Poll Started</th>
              <th>Poll End</th>
            </tr>

          </thead>
          @if(count($results)>0)
          <tbody id="oneTimetab">   
            @foreach($results as $result)
            <tr>
              <td><a href="{{$result['href']}}">{{$result['st_name']}}</a></td>
              <td><a href="{{$result['href']}}">{{$result['total_start']}}</a></td>
              <td><a href="{{$result['href']}}">{{$result['total_end']}}</a></td>
             </tr>
            @endforeach

            @if(isset($total))
            <tr>
              <td>{{$total['st_name']}}</td>
              <td>{{$total['total_start']}}</td>
              <td>{{$total['total_end']}}</td>
             </tr>
            @endif

          </tbody>
          @else
          <tbody>
            <tr>
              <td colspan="4">
                @if(isset($no_record))
                {{$no_record}}
                @else
                No Record Found
                @endif
              </td>
            </tr>
          </tbody>
          @endif
        </table>
      </div><!-- End Of  table responsive -->  
    </div><!-- End Of intra-table Div -->   


  </div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 
@endsection