@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .upload_button_browse{
    margin-left: 25px;
  }
</style>
<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col  pull-right  text-right">

        @if(isset($filter_buttons) && count($filter_buttons)>0)

        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach 
        @endif    
		
        @if(count($buttons)>0)
        @foreach($buttons as $button)
        <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
        @endforeach
        @endif  



        
      </div>
    </div>
  </div>  
</section>





<div class="container-fluid">

  <!-- Start parent-wrap div --> 
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area" id="child_area">
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
      <div class="page-contant card">
        <div style="width: 69%; margin-top: 2%; margin-left: 2%;">
        <p><span style="color:red;">
          Note: Import user facility will only work when there are no users registration in ENCORE for AC. Ensure complete cleaning of users before importing from excel.Also ensure there is no duplicate Mobile number, PS number and user in the exel sheet.Ensure all data in the excel sheet is correct and in proper format</span>
        </p>
      </div>
        <div class="random-area card-body">
          <div class="col-sm-12">
            <button class="btn btn-success pull-right mb-3 upload_button_browse" data-role-id="34" type="button">Import PO From Excel <i class="fa fa-upload"></i></button>
            <!--<button class="btn btn-success pull-right mb-3 upload_button_browse" data-role-id="35" type="button">Import PRO From Excel <i class="fa fa-upload"></i></button> -->
          </div>






        <div class="table-responsive table-dynamic-parent">
          <table class='table table-bordered table-dynamic'>
          @if(count($results))
          <tr>
            <th>PS No</th>
            <th>{{$designation}}1 Name</th>
            <th>{{$designation}}1 Mobile</th>
            
          </tr>
          @foreach($results as $result)
          <tr>
            <th>{{$result['ps_no']}}</th>
            <th>{{$result['name1']}}</th>
            <th>{{$result['mobile1']}}</th>
            
          </tr>
          @endforeach
          @else
            <tr>
              <td colspan="">No Pending officers to import</td>
            </tr>
          @endif
          </table>

        </div>
        <div class="verify_and_upload_div" @if(count($results)==0) style="display: none;" @endif>
          <button class="btn btn-primary" id="verify_and_upload">Verify and upload</button>

        </div>
          </div>





        </div>
      </div>
    </div> 
  </div>

</div><!-- End Of parent-wrap Div -->
</div> 

@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function () {
  $('.upload_button_browse').on('click', function() {
  $('#form-upload').remove();

  $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file[]" value="" /></form>');

  $('#form-upload input[name=\'file[]\']').trigger('click');

  if (typeof timer != 'undefined') {
      clearInterval(timer);
  }

  var role_id = $(this).attr('data-role-id');

  timer = setInterval(function() {
    if ($('#form-upload input[name=\'file[]\']').val() != '') {
      clearInterval(timer);
      
      $.ajax({
        url: "<?php echo $file_upload; ?>?_token=<?php echo csrf_token(); ?>&role_id="+role_id,
        type: 'post',
        dataType: 'json',
        data: new FormData($('#form-upload')[0]),
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
          $('.excel-error, .table-dynamic').remove();
          $('.upload_button_browse i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
          $('.upload_button_browse').prop('disabled', true);
        },
        complete: function() {
          $('.upload_button_browse i').replaceWith('<i class="fa fa-upload"></i>');
          $('.upload_button_browse').prop('disabled', false);
        },
        success: function(json) {

          if(json['success'] == false) {
            html = '';
            $.each(json['errors'], function(index, object){
              html += object+"<br/>";
            });
            $('#child_area').before("<div class='alert alert-danger row text-left mt-3 mb-3 excel-error'>"+html+"</div>");
          }

          if (json['error']) {
            error_messages(json['error']);
          }

          if (json['success'] == true) {
            success_messages(json['message']);
            html = "";
            html += "<table class='table table-bordered table-dynamic'>";
            html += "<tr>";
            html += "<th>PS No</th>";
            html += "<th>"+json.role_type+"1 Name</th>";
            html += "<th>"+json.role_type+"1 Mobile</th>";
            
            html += "</tr>";
            $.each(json.data, function(index, object){
              html += "<tr>";
              html += "<td>"+object.ps_no+"</td>";
              html += "<td>"+object.name1+"</td>";
              html += "<td>"+object.mobile1+"</td>";
             
              html += "</tr>";
            });
            html += "</table>";
            $(".table-dynamic-parent").html(html);
            $('#button-refresh').trigger('click');
            $('.verify_and_upload_div').css("display", "block");
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    }
  }, 500);
});


  $('#verify_and_upload').click(function(e){
    $.ajax({
        url: "{!! $verify_and_upload !!}",
        type: 'POST',
        data: "_token=<?php echo csrf_token() ?>",
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#verify_and_upload .text-danger').remove();
          $('#verify_and_upload').prop('disabled',true);
          $('#verify_and_upload').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          if(json['success'] == true){
            location.reload();
          }
          if(json['success'] == false){
            html = '';
            $.each(json['errors'], function(index, object){
              html += object+"<br/>";
            });
			$('#child_area').before("<div class='alert alert-danger row text-left mt-3 mb-3 excel-error'>"+html+"</div>");
          }
          $('#verify_and_upload').prop('disabled',false);
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#verify_and_upload').prop('disabled',false);
          $('.loading_spinner').remove();
        }
      }); 
  });


});
</script>
@endsection