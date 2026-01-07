@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .fullwidth{
    float: left;
    width: 100%;
  }
</style>
<section class="statistics color-grey pt-4 pb-2">
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





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
<br>

      <form class="form-horizontal" method="post" action="{!! $action !!}">
       
            <input type="hidden" value="{!! csrf_token() !!}" name="_token">
           


                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Message</label>
                <textarea class="form-control col-md-9 pull-left" name="message" id="message" rows="4">{{$message}}</textarea>
                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('message') !!}</span>
                 @endif 
      
                </div>


                


          <div class="form-group fullwidth">
            <button type="submit" class="pull-right btn btn-primary" style="margin-top: 30px;">Update</button>
          </div>


     </form>        
      
        


         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
@endsection


@section('script')
@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif
@endsection