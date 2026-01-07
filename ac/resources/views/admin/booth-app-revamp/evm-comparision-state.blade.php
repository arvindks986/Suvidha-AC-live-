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
@if(false)
<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col-md-3 pull-right text-right">
      @if(count($voter_turnouts)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" style="font-size: 32px;" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif    
    </div>
  </div>
</div>  
</section>
@endif

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
          <table class="table table-bordered " id="my-list-table">
           <thead>
            <tr>
	
              <th>State Name</th>
	            <th>Total PRO</th>
			        <th>Total EVM Votes</th>
            </tr>
          </thead>
          <tbody>  
            @if(count($voter_turnouts)>0)
            @foreach($voter_turnouts as $result)
            <tr>
			  <td><a href="{!! $result['href'] !!}">{{$result['st_name']}}</a></td>
              <td><a href="{!! $result['href'] !!}">{{$result['no_of_vote']}}</a></td>
              <td><a href="{!! $result['href'] !!}">{{$result['no_of_vote_evm']}}</a></td>
	
            </tr>
            @endforeach
            @else
            <tr>
              <td colspan="3">
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
@if(isset($add_download_log))
<script type="text/javascript">
  function download_booth_slip(id){
    $.ajax({
        url: "{!! $add_download_log !!}",
        type: 'POST',
        data: '_token=<?php echo csrf_token() ?>&ps_no='+id,
        dataType: 'json', 
        beforeSend: function() {
          
        },  
        complete: function() {
        },        
        success: function(json) {         
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
    });
  }
</script>
@endif

<script type="text/javascript">
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 50,
        "aaSorting": []
      });
    }
  });
</script>
@endsection