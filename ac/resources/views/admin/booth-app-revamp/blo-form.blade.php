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
  .SumoSelect{
    width: 100% !important;
  }
  #add_new_so{
    float: right;
  }
</style>

<div class="loader" style="display:none;"></div>

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

        @if(count($buttons)>0)
        @foreach($buttons as $button)
        <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
        @endforeach
        @endif  



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
      <div class="page-contant card">
        <div class="random-area card-body">

          <div class="col-sm-12">
            <button class="btn btn-success pull-right mb-3" type="button" id="add_new_so">Add BLO</button>
          </div>







          <div class="table-responsive">
            <table class="table table-bordered list-table-remove" id="my-list-table"> 
              <thead>
                <tr> 
                  <th>S.No</th>
                  <th class="second-td">BLO</th>
                  <th class="second-td">Assigned Location</th>
                  <th class="second-td">PS No</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody> 
                <?php $i = 1; ?>  
                @foreach($results as $result)
                <tr class="">
                  <td>{{$i}}</td>
                  <td class="level1"><p>Name: <b>{{$result['name']}}</b></p><p>Mobile: <b>{{$result['mobile']}}</b> </p><p>Status: <b>{{$result['is_active']}}</b> </p> 

                    <input type="hidden" value="{{$result['encrpt_id']}}" class="edit_id" name="edit_id">
                    <input type="hidden" value="{{$result['mobile']}}" class="mobile" name="mobile">
                    <input type="hidden" value="{{$result['name']}}" class="name" name="name">
                    <input type="hidden" value="{{$result['status']}}" class="status" name="status">
                    <input type="hidden" value="{{$result['location_id']}}" class="location_id" name="location_id">
                    <input type="hidden" value="{{$result['is_testing']}}" class="is_testing" name="is_testing">
                    <button type="button" class="btn btn-success edit_id_button" onclick="">Edit</button>
                  </td>
                  <td>{{@$result['location_name']}}</td>
                  <td>{{$result['ps_no']}}</td>
                  <td>
                  <p ><a href="javascript:void(0)"  onclick="reset_otp(<?php echo $result['mobile'] ?>)" class="pull-right btn btn-success pull-right mb-3">Reset Pin</a></p>
                  </td>
                </tr>
                <?php $i++ ?>
                @endforeach
              </tbody>
            </table>
          </div>





        </div>
      </div>
    </div> 
  </div>

</div><!-- End Of parent-wrap Div -->
</div> 



<div class="modal fade animated zoomIn" id="add_new_so_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">

      <form class="form-horizontal" method="post" action="{!! $action !!}">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        <div class="modal-header">
          <h4 class="modal-title">Add BLO</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body">

          
     

          <div class="form-group mb-1">
            <label class="col-md-3  pull-left">Location Station</label>
            <div class="pull-left col-md-9" id="location_id_div">
              <div class="row">
                <select class="sumoselect form-control location_id" name="location_id" id="location_id">
                  @foreach($locations as $iterate_location)
                    <option value="{{$iterate_location['id']}}">{{$iterate_location['name']}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>


          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Name</label>
            <input type="text" class="form-control col-md-9 name" name="name" value="" placeholder="Name">
          </div>


          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Mobile</label>
            <input type="text" class="form-control col-md-9 mobile" name="mobile" value="" placeholder="Mobile" size="10" maxlength="10">
          </div>

          <div class="form-group mb-1">
            <label class="col-md-3  pull-left">Status</label>
            <select class="form-control col-md-9 status" name="status">
              <option value="1">Enable</option>
              <option value="0">Disable</option>
            </select>
          </div>

          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Testing User</label>
            <select class="form-control col-md-9 is_testing" name="is_testing">
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>

          <input type="hidden" name="role_id" value="38" id="role_id">
          <input type="hidden" name="role_level" value="1" id="role_level">

        </div>
        <div class="modal-footer">
          <div class="form-group mb-1">
            <label class="col-md-3 pull-left" style="visibility: hidden;"></label>
            <button type="button" id="add_new_so_submit" class="btn btn-large btn-primary">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade animated zoomIn" id="otp_update_form" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="otp_update_form" action="return false;">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        <input type="hidden" name="mobile" class="mobile" value="">
      <div class="modal-header">
        Please Enter 6 digit otp pin<button type="button" class="close" data-dismiss="modal">&times;</button> 
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-md-3">OTP Pin</label>
          <div class="col-md-9">
          <input type="pasword" name="otp" value="" id="otp" class="form-control" size="6" maxlength="6">
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="otp_submit">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>



@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 500,
        "aaSorting": []
      });
    }
    if($('.sumoselect').length>0){
      $('.sumoselect').each(function(index,object){
        $("#"+$(object).attr('id')).SumoSelect({
          okCancelInMulti: false, 
          isClickAwayOk: false,
          triggerChangeCombined : true,
          selectAll : false,
          search : true,
          searchText : 'Search...',
        });
      });
    }
    $('#add_new_so').click(function(e){
      $('#add_new_so_modal .text-danger').remove();
      $('#add_new_so_modal input').removeClass('input-error');
      $('#add_new_so_modal #id').remove();
      $('#add_new_so_modal .location_id').val('');
      $('#add_new_so_modal .location_id')[0].sumo.reload();
      $('#add_new_so_modal .name').val('');
      $('#add_new_so_modal .mobile').val('');
      $('#add_new_so_modal .status').val(0);
      $('#add_new_so_modal .is_testing').val(0);
      $('#add_new_so_modal').modal('show');
    });

    $('.edit_id_button').click(function(e){
      $('#add_new_so_modal #id').remove();
      $('#add_new_so_modal .text-danger').remove();
      $('#add_new_so_modal input').removeClass('input-error');
      $('#add_new_so_modal .location_id').val('');
      $('#add_new_so_modal .location_id')[0].sumo.reload();
      var values = $(this).parent('td').find('.location_id').val();
      $.each(values.split(","), function(i,e){
          $('#add_new_so_modal .location_id')[0].sumo.selectItem(e);
      });
      $('#add_new_so_modal form').prepend("<input type='hidden' name='id' id='id' class='id' value='"+$(this).parent('td').find('.edit_id').val()+"'>");
      $('#add_new_so_modal .name').val($(this).parent('td').find('.name').val());
      $('#add_new_so_modal .mobile').val($(this).parent('td').find('.mobile').val());
      $('#add_new_so_modal .status').val($(this).parent('td').find('.status').val());
      $('#add_new_so_modal .is_testing').val($(this).parent('td').find('.is_testing').val());
      $('#add_new_so_modal').modal('show');
    });

    $('#add_new_so_submit').click(function(){
      $.ajax({
        url: "{!! url('/roac/booth-app-revamp/assign-blo/save-via-ajax') !!}",
        type: 'POST',
        data: $('#add_new_so_modal form').serialize(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#add_new_so_modal .text-danger').remove();
          $('#add_new_so_modal input').removeClass('input-error');
          $('#add_new_so_submit').prop('disabled',true);
          $('#add_new_so_submit').text("Validating...");
          $('#add_new_so_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {

          if(json['success'] == true){
            location.reload();
          }

          if(json['success'] == false){
            if(json['errors']['warning']){
              alert(json['errors']['warning']);
            }
            if(json['errors']['name']){
              $("#add_new_so_modal .name").addClass("input-error");
              $("#add_new_so_modal .name").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['name'][0]+"</span>");
            }
            if(json['errors']['mobile']){
              $("#add_new_so_modal .mobile").addClass("input-error");
              $("#add_new_so_modal .mobile").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['mobile'][0]+"</span>");
            }
            if(json['errors']['status']){
              $("#add_new_so_modal .status").addClass("input-error");
              $("#add_new_so_modal .status").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['status'][0]+"</span>");
            }
            if(json['errors']['ps_no']){
              $("#add_new_so_modal #ps_no_div").addClass("input-error");
              $("#add_new_so_modal #ps_no_div").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['ps_no'][0]+"</span>");
            }
          }

          $('#add_new_so_submit').prop('disabled',false);
          $('#add_new_so_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#add_new_so_submit').prop('disabled',false);
          $('#add_new_so_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
    });




    $('#otp_submit').click(function(e){
      $.ajax({
        url: "{!! $reset_otp_link !!}",
        type: 'POST',
        data: '_token='+$('#otp_update_form .token').val()+'&mobile='+$('#otp_update_form .mobile').val()+'&otp='+$('#otp_update_form #otp').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#otp_update_form .text-danger').remove();
          $('#otp_update_form input').removeClass('input-error');
          $('#otp_submit').prop('disabled',true);
          $('#otp_submit').text("Updating...");
          $('#otp_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#otp_update_form').modal('hide');
            success_messages(json['message']);
          }

          if(json['status'] == false){
            if(json['errors']){
              error_messages(json['errors']);
            }
          }

          $('#otp_submit').prop('disabled',false);
          $('#otp_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#otp_submit').prop('disabled',false);
          $('#otp_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
  });






});

  function reset_otp(mobile){
    $('#otp_update_form #otp').val('');
    $('#otp_update_form .mobile').val(mobile);
    $('#otp_update_form').modal('show');
  }

</script>
@endsection