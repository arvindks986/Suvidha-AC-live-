@extends('admin.layouts.ac.theme')
@section('content')

 <main class="mb-auto">
     
      <!--main content start-->
       
 <main role="main" class="inner cover mb-3">


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

<section class="mt-4">
  <div class="container-fluid">
  
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
     <div class=" card-header">
    <div class=" row">
      <div class="col-md-4"><h4>{!! $heading_title !!}</h4></div> 
      <div class="col"><p class="mb-0 text-right">
      </p><div class="" style="width:100%; margin:0 auto;"></div>
      &nbsp;&nbsp;  
      <p></p>
      </div><!--end col-->
    </div> <!--end row-->
    </div><!--end card-header -->
      
    <div class="card-body">  
      <form id="change_password" method="POST" action="{!! $action !!}" autocomplete="off">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <div class="form-group row">
                                <label for="new-password" class="col-md-4 control-label">Current Pin <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('old_pin')){ echo 'is-invalid'; } ?>" name="old_pin" value=""  autocomplete="off">
                                    @if ($errors->has('old_pin'))
          <span class="newpassword errormsg errorred">{{ $errors->first('old_pin') }}</span>
        @endif
                                                                    </div>
                           
                            </div>

                            <div class="form-group row">
                                <label for="new-password" class="col-md-4 control-label">New Pin <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('pin')){ echo 'is-invalid'; } ?>" name="pin" value="" autocomplete="off">
                                    @if ($errors->has('pin'))
          <span class="newpassword errormsg errorred">{{ $errors->first('pin') }}</span>
        @endif
                                                                    </div>
                                

                                


                            </div>

                            <div class="form-group row">
                                <label for="new-password-confirm" class="col-md-4 control-label">Confirm New Pin <sup>*</sup></label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('pin_confirmation')){ echo 'is-invalid'; } ?>" name="pin_confirmation" value="" autocomplete="off">
                                    @if ($errors->has('pin_confirmation'))
          <span class="newpassword errormsg errorred">{{ $errors->first('pin_confirmation') }}</span>
        @endif
                                </div>
                                
                            </div>

                            <div class="form-group float-right row">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>

            </form></div><!-- end row-->
          </div> <!-- end COL-->
        </div>

    
    
  </div>
</section>

</main>
      <!--main content end-->
   
 </main>

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

<script type="text/javascript">
$(document).ready(function(e){
  /*var i = 0;
  $('input').each(function(index,object){
    $(object).attr("autocomplete", i+Math.random().toString(36).substring(7)); 
  });*/
});
</script>
@endsection