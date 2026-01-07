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

          <div class="table-responsive">
            <table class="table table-bordered list-table-remove" > 
              <thead>
                <tr> 
                  <th class="second-td" rowspan="1">PS No & Name</th>
                  <th class="second-td text-center" colspan="6">Voter Turnout</th>
                </tr>
                <tr>
                  <td>
                    <th>7:00 AM-9:00AM</th>
                    <th>9:00 AM-11:00 AM</th>
                    <th>11:00 AM-1:00 PM</th>
                    <th>1:00 PM-3:00 PM</th>
                    <th>3:00 PM-5:00AM</th>
                    <th>End of Poll</th>
                  </td>
                </tr>
              </thead>
              <tbody> 
                <?php $i = 1; ?>  
                @foreach($results as $result)
                <tr class="">
                  <td class="level1"><p>{{$result['ps_no']}}-{{$result['ps_name']}}</p>
                  </td>
                  <td>
                    <input type="hidden" class="round" value="1">
                    <input type="hidden" class="ps_no" value="{{$result['ps_no']}}">
                    @if($result['round_1_total'])
                    <table>
                      <thead>
                        <tr>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Other</th>
                        <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{$result['round_1_male']}}</td>
                          <td>{{$result['round_1_female']}}</td>
                          <td>{{$result['round_1_other']}}</td>
                          <td>{{$result['round_1_total']}}</td>
                        </tr>
                      </tbody>
                    </table>
                    @else
                    <button class="btn btn-link">Add Turnout</button>
                    @endif
                  </td>
                  <td>
                    <input type="hidden" class="round" value="2">
                    <input type="hidden" class="ps_no" value="{{$result['ps_no']}}">
                    @if($result['round_2_total'])
                    <table>
                      <thead>
                        <tr>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Other</th>
                        <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{$result['round_2_male']}}</td>
                          <td>{{$result['round_2_female']}}</td>
                          <td>{{$result['round_2_other']}}</td>
                          <td>{{$result['round_2_total']}}</td>
                        </tr>
                      </tbody>
                    </table>
                    @else
                    <button class="btn btn-link">Add Turnout</button>
                    @endif
                  </td>
                  <td>
                    <input type="hidden" class="round" value="3">
                    <input type="hidden" class="ps_no" value="{{$result['ps_no']}}">
                    @if($result['round_3_total'])
                    <table>
                      <thead>
                        <tr>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Other</th>
                        <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{$result['round_3_male']}}</td>
                          <td>{{$result['round_3_female']}}</td>
                          <td>{{$result['round_3_other']}}</td>
                          <td>{{$result['round_3_total']}}</td>
                        </tr>
                      </tbody>
                    </table>
                    @else
                    <button class="btn btn-link">Add Turnout</button>
                    @endif
                  </td>
                  <td>
                    <input type="hidden" class="round" value="4">
                    <input type="hidden" class="ps_no" value="{{$result['ps_no']}}">
                    @if($result['round_4_total'])
                    <table>
                      <thead>
                        <tr>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Other</th>
                        <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{$result['round_4_male']}}</td>
                          <td>{{$result['round_4_female']}}</td>
                          <td>{{$result['round_4_other']}}</td>
                          <td>{{$result['round_4_total']}}</td>
                        </tr>
                      </tbody>
                    </table>
                    @else
                    <button class="btn btn-link">Add Turnout</button>
                    @endif
                  </td>
                  <td>
                    <input type="hidden" class="round" value="5">
                    <input type="hidden" class="ps_no" value="{{$result['ps_no']}}">
                    @if($result['round_5_total'])
                    <table>
                      <thead>
                        <tr>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Other</th>
                        <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{$result['round_5_male']}}</td>
                          <td>{{$result['round_5_female']}}</td>
                          <td>{{$result['round_5_other']}}</td>
                          <td>{{$result['round_5_total']}}</td>
                        </tr>
                      </tbody>
                    </table>
                    @else
                    <button class="btn btn-link">Add Turnout</button>
                    @endif
                  </td>
                  <td>
                    <input type="hidden" class="round" value="0">
                    <input type="hidden" class="ps_no" value="{{$result['ps_no']}}">
                    @if($result['round_total'])
                    <table>
                      <thead>
                        <tr>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Other</th>
                        <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{$result['round_male']}}</td>
                          <td>{{$result['round_female']}}</td>
                          <td>{{$result['round_other']}}</td>
                          <td>{{$result['round_total']}}</td>
                        </tr>
                      </tbody>
                    </table>
                    @else
                    <button class="btn btn-link">Add Turnout</button>
                    @endif
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



<div class="modal fade animated zoomIn" id="turnout_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">

      <form class="form-horizontal" method="post" action="{!! $action !!}">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">

        <input type="hidden" name="ps_no" id="ps_no" class="ps_no form-control" value="">
        <input type="hidden" name="round" id="round" class="round form-control" value="">



        <div class="modal-header">
          <h4 class="modal-title">Voter Turnout Form<br><small style="font-size: 12px;">Please enter cumulative value</small></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body" style="min-height: 250px;">

          <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">Male</span>
    </div>
    <input type="text" class="form-control input_number" placeholder="Male" name="male" id="male" size="6" length="6">
  </div>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">Female</span>
    </div>
    <input type="text" class="form-control input_number" placeholder="Female" name="female" id="female" size="6" length="6">
  </div>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">Other</span>
    </div>
    <input type="text" class="form-control input_number" placeholder="Other" name="other" id="other" size="6" length="6">
  </div>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">Total</span>
    </div>
    <input type="text" class="form-control" placeholder="Total" name="total" id="total" readonly="readonly">
  </div>



        </div>
        <div class="modal-footer">
          <div class="form-group mb-1">
            <label class="col-md-3 pull-left" style="visibility: hidden;"></label>
            <button type="button" id="turnout_modal_submit" class="btn btn-large btn-primary">Submit</button>
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

    $('#turnout_modal .input_number').each(function(i,object){
      $(".input_number").removeClass("input-error");
      $(object).on('keyup change',function (e) {
        if (parseInt($(object).val()) >= 0 && !isNaN($(object).val()) && $(object).val().indexOf('.') == '-1'){
          $(object).val(trim_number($(object).val()));
        }else{
          $(object).addClass("input-error");
          $(object).parent('td').find('.text-danger').text("please enter positive numeric value..").show();
          $(object).val('');
        }
        calculate_total();
      });
    });

    $('.btn-link').click(function(e){
      $('#turnout_modal .text-danger').remove();
      $('#turnout_modal input').removeClass('input-error');
      $('#turnout_modal .form-control').val('');
      $("#turnout_modal input[name=ps_no]").val($(this).parent('td').find(".ps_no").val());
      $("#turnout_modal input[name=round]").val($(this).parent('td').find(".round").val());
      $('#turnout_modal').modal('show');
    });

    $('#turnout_modal_submit').click(function(){
      $.ajax({
        url: "{!! $action !!}",
        type: 'POST',
        data: $('#turnout_modal form').serialize(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#turnout_modal .text-danger').remove();
          $('#turnout_modal input').removeClass('input-error');
          $('#turnout_modal_submit').prop('disabled',true);
          $('#turnout_modal_submit').text("Validating...");
          $('#turnout_modal_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
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
            if(json['errors']['male']){
              $("#turnout_modal #male").addClass("input-error");
              $("#turnout_modal #male").after("<span class='text-error text-danger text-right pull-right' style='width:100%'>"+json['errors']['male'][0]+"</span>");
            }
            if(json['errors']['female']){
              $("#turnout_modal #female").addClass("input-error");
              $("#turnout_modal #female").after("<span class='text-error text-danger text-right pull-right' style='width:100%'>"+json['errors']['female'][0]+"</span>");
            }
            if(json['errors']['other']){
              $("#turnout_modal #other").addClass("input-error");
              $("#turnout_modal #other").after("<span class='text-error text-danger text-right pull-right' style='width:100%'>"+json['errors']['other'][0]+"</span>");
            }
            if(json['errors']['total']){
              $("#turnout_modal #total").addClass("input-error");
              $("#turnout_modal #total").after("<span class='text-error text-danger text-right pull-right' style='width:100%'>"+json['errors']['total'][0]+"</span>");
            }
          }

          $('#turnout_modal_submit').prop('disabled',false);
          $('#turnout_modal_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#turnout_modal_submit').prop('disabled',false);
          $('#turnout_modal_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
    });
});

function trim_number(s) {
  while (s.substr(0,1) == '0' && s.length>1) { s = s.substr(1,9999); }
  return s;
}

function calculate_total(){
  var total_count = 0;
  $('#turnout_modal .input_number').each(function(i,object){
    if(parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
      total_count = parseInt(total_count) + parseInt($(object).val());
    }
  });
  $('#turnout_modal #total').val(total_count);
}

</script>
@endsection