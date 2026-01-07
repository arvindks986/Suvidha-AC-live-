@extends('admin.layouts.pc.dashboard-theme')
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
      




      <table id="list-table1" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>State</th>
          <th>Username</th> 
          <th>Designation</th> 
          <th>Email</th> 
          <th>Mobile</th>
          <th>Action</th> 
        </tr>
        </thead>
        <tbody id="oneTimetab"> 
          @if(!empty($results))
           <?php $i = 1; ?>
           @foreach($results as $result)
            <tr id="row_{{$i}}">
              <td>{{$result['state_name']}}</td>
              <td>{{$result['officername']}}</td>
              <td>{{$result['designation']}}</td>
              <td>{{$result['email']}}</td>
              <td>{{$result['mobile']}}</td>
              <td>
                <input type="hidden" name="reset-path" class="reset-path" value="{!! $result['hash_id'] !!}">

                <button class="btn btn-primary reset_pin_button">Reset Pin</button>
                <button class="btn btn-primary reset_password_button">Reset Password</button>

              </td>
            </tr>
            <?php $i++; ?>
            @endforeach
   
            @endif 
        </tbody>
        </table>










      </div><!-- end row-->
          </div> <!-- end COL-->
        </div>

    
    
  </div>
</section>

</main>
      <!--main content end-->
   
 </main>



<div class="modal fade animated zoomIn" id="reset_pin_ceo" tabindex="-1" role="dialog" aria-labelledby="reset_pin_ceoLabel" aria-hidden="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="reset_pin_form" action="return false;">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        <input type="hidden" name="username" class="username" value="">
      <div class="modal-header">
        Please Setup 2 step verification pin <button type="button" class="close" data-dismiss="modal">&times;</button>  
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-md-3">Pin</label>
          <div class="col-md-9">
          <input type="password" name="pin" value="" id="pin" class="form-control">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3">Confirm Pin</label>
          <div class="col-md-9">
          <input type="password" name="pin_confirmation" value="" id="pin_confirmation" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="ceo_pin_submit">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade animated zoomIn" id="reset_password_model" tabindex="-1" role="dialog" aria-labelledby="reset_password_model" aria-hidden="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="reset_pin_form" action="return false;">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        <input type="hidden" name="username" class="username" value="">
      <div class="modal-header">
        Enter new Password <button type="button" class="close" data-dismiss="modal">&times;</button>  
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-md-3">Password</label>
          <div class="col-md-9">
          <input type="password" name="password" value="" id="password" class="form-control">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3">Confirm Password</label>
          <div class="col-md-9">
          <input type="password" name="password_confirmation" value="" id="password_confirmation" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="eci_password_submit">Update</button>
      </div>
      </form>
    </div>
  </div>
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

<script type="text/javascript">
$(document).ready(function(e){
  
  $('.reset_pin_button').click(function(e){
    $('#reset_pin_ceo input').val('');
    $('#reset_pin_ceo').modal('show');
    $('#reset_pin_form .username').val($(this).parent('td').find('.reset-path').val());
  });

  $('#ceo_pin_submit').click(function(e){
      $.ajax({
        url: "{!! url('/eci/officer/update-pin') !!}",
        type: 'POST',
        data: '_token={!! csrf_token() !!}&pin='+$('#reset_pin_form #pin').val()+'&pin_confirmation='+$('#reset_pin_form #pin_confirmation').val()+'&reset_path='+$('#reset_pin_form .username').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#reset_pin_form .text-danger').remove();
          $('#reset_pin_form input').removeClass('input-error');
          $('#ceo_pin_submit').prop('disabled',true);
          $('#ceo_pin_submit').text("Validating...");
          $('#ceo_pin_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#reset_pin_ceo').modal('hide');
            success_messages(json['message']);
            $("#reset_pin_form input").val('');
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
            if(json['errors']['pin']){
              $("#reset_pin_form input[name='pin']").addClass("input-error");
              $("#reset_pin_form input[name='pin']").after("<span class='text-danger'>"+json['errors']['pin'][0]+"</span>");
            }
            if(json['errors']['pin_confirmation']){
              $("#reset_pin_form input[name='pin_confirmation']").addClass("input-error");
              $("#reset_pin_form input[name='pin_confirmation']").after("<span class='text-danger'>"+json['errors']['pin_confirmation'][0]+"</span>");
            }
          }

          $('#ceo_pin_submit').prop('disabled',false);
          $('#ceo_pin_submit').text("Submit");
          $('.loading_spinner').remove();
          
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#ceo_pin_submit').prop('disabled',false);
          $('#ceo_pin_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
  });



  $('.reset_password_button').click(function(e){
    $('#reset_password_model input').val('');
    $('#reset_password_model').modal('show');
    $('#reset_password_model .username').val($(this).parent('td').find('.reset-path').val());
  });

  $('#eci_password_submit').click(function(e){
      $.ajax({
        url: "{!! url('/eci/officer/update-password') !!}",
        type: 'POST',
        data: '_token={!! csrf_token() !!}&password='+$('#reset_password_model #password').val()+'&password_confirmation='+$('#reset_password_model #password_confirmation').val()+'&reset_path='+$('#reset_password_model .username').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#reset_password_model .text-danger').remove();
          $('#reset_password_model input').removeClass('input-error');
          $('#eci_password_submit').prop('disabled',true);
          $('#eci_password_submit').text("Validating...");
          $('#eci_password_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#reset_password_model').modal('hide');
            success_messages(json['message']);
            $("#reset_password_model input").val('');
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
            if(json['errors']['password']){
              $("#reset_password_model input[name='password']").addClass("input-error");
              $("#reset_password_model input[name='password']").after("<span class='text-danger'>"+json['errors']['password'][0]+"</span>");
            }
            if(json['errors']['password_confirmation']){
              $("#reset_password_model input[name='password_confirmation']").addClass("input-error");
              $("#reset_password_model input[name='password_confirmation']").after("<span class='text-danger'>"+json['errors']['password_confirmation'][0]+"</span>");
            }
          }

          $('#eci_password_submit').prop('disabled',false);
          $('#eci_password_submit').text("Submit");
          $('.loading_spinner').remove();
          
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#eci_password_submit').prop('disabled',false);
          $('#eci_password_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
  });

  



});
</script>
@endsection