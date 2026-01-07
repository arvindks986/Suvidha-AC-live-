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


<section class="dashboard-header section-padding" style="margin: 0px !important;">
  <div class="container-fluid">

    
    <form id="generate_report_id"  class="row" method="get" onsubmit="return false;">

     
      <div class="form-group col-md-3"> <label>Polling Station </label> 
        
        <select name="ps_no" id="ps_no" class="form-control" onchange ="filter()">
          <option value="">Select PS</option>
          @foreach($results_ps as $iterate_ps)
          @if($ps_no == $iterate_ps['id'])
          <option value="{{$iterate_ps['id']}}" selected="selected">{{$iterate_ps['ps_name']}}</option> 
          @else 
          <option value="{{$iterate_ps['id']}}" >{{$iterate_ps['ps_name']}}</option> 
          @endif  
          @endforeach
          
        </select>
      </div>
      
    </form>   

    
  </div>
</section>



<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col-md-3  pull-right  text-right">

      @if(isset($slip_download_button) && isset($ps_no))
      <span class="report-btn"><a class="btn btn-primary" onclick="download_booth_slip('<?php echo $ps_no ?>')" style="font-size: 32px;" href="{{ $slip_download_button }}" title="Download Booth Slip" target='_blank'>Download Booth Slip</a></span>
      @endif

      @if(count($results)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" style="font-size: 32px;" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
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
              <th>Epic No</th>
              <th>Name</th>
              <th>Gender</th>
              <th>Voter Serial No</th>
              <th>Download Booth Slip</th>
            </tr>
          </thead>
          <tbody>  
            @if(count($results)>0)
            @foreach($results as $result)
            <tr>
              <td>{{$result['epic_no']}} </td>
              <td>{{$result['name']}}</td>
              <td>{{$result['gender']}}</td>
              <td>{{$result['voter_serial_no']}}</td>
              <td><a href="{!! $result['href'] !!}" target="_blank"><i class="fa fa-fw"></i> Download </a></td>
            </tr>
            @endforeach
            @else
            <tr>
              <td colspan="5">
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
  function filter(){
    var url = "<?php echo $action ?>";
    var query = '';
    if(jQuery("#ps_no").val() != '' && jQuery("#ps_no").val() != 'undefined'){
      query += '&ps_no='+jQuery("#ps_no").val();
    }
    window.location.href = url+'?'+query.substring(1);
  }
</script>
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
@endsection