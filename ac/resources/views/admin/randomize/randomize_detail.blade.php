@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  td{
    position: relative;
  }
</style>
<div class="container-fluid mt-3">

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

  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     
     <div class="page-contant card">

      <div class="random-area card-header">
        <div class="col pull-left">
        <h4>Schedule for polling party Randomization & Dispatch</h4>
        </div>
        <div class="col  pull-right  text-right">
        @if(isset($buttons) && count($buttons)>0)
          @foreach($buttons as $button)
          <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
          @endforeach
        @endif 
        @if(isset($filter_buttons) && count($filter_buttons)>0)
          @foreach($filter_buttons as $button)
          <?php $but = explode(':',$button); ?>
          <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>
          </span>
          @endforeach
        @endif    
        </div>
      </div>

       <div class="random-area card-body">
        
        <div class="view-form display_none">
        <table class="table">
          <tr>
            <td align="right">
                <button class="btn btn-primary" id="edit_randomize">Edit Randomization & Dispatch</button>
            </td>
          </tr>
        </table>
        <table class="table table-bordered list-table-remove"> 
            <thead>
              <tr>
                <th>Polling Party </th>
                <th>Date</th>
                <th>Time</th>
              </tr>  
            </thead>
          <tbody>
            <tr>
              <td>Randomization Details</td>
              <td>{{$randomize_date}}</td>
              <td>{{$randomize_time}}</td>
            </tr>
            <tr>
              <td>Dispatch Details</td>
              <td>{{$dispatched_date}}</td>
              <td>{{$dispatched_time}}</td>
            </tr>
          </tbody>
        </table>
        </div>
        <div class="edit-form display_none">
          <form onsubmit="return false;" id="randomize_form">
          <input type="hidden" name="_token" value="{{csrf_token()}}">
          <table class="table table-bordered list-table-remove"> 
            <thead>
              <tr>
                <th>Polling Party </th>
                <th>Date</th>
                <th>Time</th>
              </tr>  
            </thead>
          <tbody>
            <tr>
              <td>Randomization Details</td>
              <td><input type="text" class="form-control date_picker" name="randomize_date" id="randomize_date" value="{{$randomize_date}}">
              </td>
              <td><input type="text" class="form-control time_picker" name="randomize_time" id="randomize_time" value="{{$randomize_time}}"></td>
            </tr>
            <tr>
              <td>Dispatch Details</td>
              <td><input type="text" class="form-control date_picker" name="dispatched_date" id="dispatched_date" value="{{$dispatched_date}}"></td>
              <td><input type="text" class="form-control time_picker" name="dispatched_time" id="dispatched_time" value="{{$dispatched_time}}"></td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td align="right" colspan="3">
                  <button type="submit" class="btn-primary btn" id="randomize_button">Submit</button>
              </td>
            </tr>
          </tfoot>
        </table>
      </form>
    </div>
    </div><!-- End Of intra-table Div -->   


  </div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
@endsection
@section("script")
<script type="text/javascript">
  $(document).ready(function(e){

    $('#randomize_button').click(function(e){
      $.ajax({
        url: "{!! $action !!}",
        type: 'POST',
        data: $('#randomize_form').serialize(),
        dataType: 'json', 
        beforeSend: function() {
          $('#randomize_form .text-danger').remove();
          $('#randomize_form .form-control').removeClass("is-invalid");
          $('#randomize_button').prop('disabled',true).append("<i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {

          if(json['success'] == true){
            location.reload();
          }

          if(json['success'] == false){

            $('.form-control').each(function(index, object){
                var field_name = $(object).attr('name');
                if(json['errors'][field_name]){
                  $("[name="+field_name+"]").after("<span class='error text-danger'>"+json['errors'][field_name][0]+"</span>");
                  $("[name="+field_name+"]").addClass("is-invalid");
                }
            });


            if(json['errors']['warning']){
              alert(json['errors']['warning']);
            }
          }
          $('#randomize_button').prop('disabled',false);
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#randomize_button').prop('disabled',false);
          $('.loading_spinner').remove();
        }
      }); 
    });

    $('.time_picker').datetimepicker({
      format: 'HH:mm:ss'
    });

    $('.date_picker').datetimepicker({
         format: "DD/MM/YYYY"
    });

    $('#randomize_date').val("<?php echo $randomize_date; ?>");
    $('#dispatched_date').val("<?php echo $dispatched_date; ?>");
    var have_record = "<?php echo $have_record; ?>";
    if(have_record){
      $('.view-form').removeClass('display_none');
      $('.edit-form').addClass('display_none');
    }else{
      $('.edit-form').removeClass('display_none');
      $('.view-form').addClass('display_none');
    }

    $('#edit_randomize').click(function(e){
      $('.edit-form').removeClass('display_none');
      $('.view-form').addClass('display_none');
    });

});
</script>
@endsection