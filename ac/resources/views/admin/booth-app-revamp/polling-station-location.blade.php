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
  .is_assigned{
    color: red;
    font-weight: bold;
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
            <button class="btn btn-success pull-right mb-3" type="button" id="add_new_so">Add Location</button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered list-table-remove" id="my-list-table"> 
              <thead>
                <tr> 
                  <th>S.No</th>
                  <th class="second-td">Name</th>
                  <th class="second-td">PS No</th>
                  <th class="second-td">Edit</th>
                </tr>
              </thead>
              <tbody> 
                <?php $i = 1; ?>  
                @foreach($results as $result)
                <tr class="">
                  <td>{{$i}}</td>
                  <td class="level1"><p>{{$result['name']}}</p>
                  </td>
                   <td>{{$result['ps_no']}}</td>
                   <td>
                    <input type="hidden" class="edit_id" name="edit_id" value="{{$result['edit_id']}}">
                    <input type="hidden" class="name" value="{{$result['name']}}">
                    <input type="hidden" class="ps_no" value="{{$result['ps_no']}}">
                    <button type="button" class="btn btn-primary edit">Edit</button>
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
          <h4 class="modal-title">Add Location</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body" style="min-height: 250px;">

          
          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Name</label>
            <input type="text" class="form-control col-md-9 name" name="name" value="" placeholder="Name">
          </div>
     

          <div class="form-group mb-1">
            <label class="col-md-3  pull-left">Polling Station</label>
            <div class="pull-left col-md-9" id="ps_no_div">
              <div class="row">
                <select class="sumoselect form-control ps_no" name="ps_no[]" id="ps_no" multiple="multiple">
                  @foreach($polling_stations as $iterate_ps)
                    @if($iterate_ps['is_assigned'])
                      <option value="{{$iterate_ps['ps_no']}}" class="is_assigned">{{$iterate_ps['ps_no']}}-{{$iterate_ps['ps_name']}}</option>
                    @else
                      <option value="{{$iterate_ps['ps_no']}}" >{{$iterate_ps['ps_no']}}-{{$iterate_ps['ps_name']}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
          </div>




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
          okCancelInMulti: true, 
          isClickAwayOk: false,
          triggerChangeCombined : true,
          selectAll : false,
          search : true,
          searchText : 'Search...',
        });
      });
    }
    $('#add_new_so').click(function(e){
      $('#add_new_so_modal #id').remove();
      $('#add_new_so_modal .text-danger').remove();
      $('#add_new_so_modal input').removeClass('input-error');
      $('#add_new_so_modal .ps_no').val('');
      $('#add_new_so_modal .ps_no')[0].sumo.reload();
      $('#add_new_so_modal .name').val('');
      $('#add_new_so_modal').modal('show');
    });

    $('.edit').click(function(e){
      $('#add_new_so_modal #id').remove();
      $('#add_new_so_modal .text-danger').remove();
      $('#add_new_so_modal input').removeClass('input-error');
      $('#add_new_so_modal .ps_no').val('');
      $('#add_new_so_modal .ps_no')[0].sumo.reload();
      var values = $(this).parent('td').find('.ps_no').val();
      $.each(values.split(","), function(i,e){
          $('#add_new_so_modal .ps_no')[0].sumo.selectItem(e);
      });
      $('#add_new_so_modal form').prepend("<input type='hidden' name='id' id='id' class='id' value='"+$(this).parent('td').find('.edit_id').val()+"'>");
      $('#add_new_so_modal .name').val($(this).parent('td').find('.name').val());
      $('#add_new_so_modal').modal('show');
    });

    $('#add_new_so_submit').click(function(){
      $.ajax({
        url: "{!! $action !!}",
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
});
</script>
@endsection