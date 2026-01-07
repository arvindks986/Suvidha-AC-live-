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
 .list-inline li{
  display: inline;
}
td {
    height: 125px !important;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
table td{position:relative;}
p.btn.btn-table {
    position: absolute;
    bottom: 0;
    width: 100%;
    text-align: center;
    left: 0;
    background: #17a2b8;
    border-radius: 0;
}
p.btn.btn-add { position: absolute;
    bottom: 0;
    width: 100%;
    /* text-align: center; */
    left: 0;
    background: #c0c0c0;
    border-radius: 0;
    /* height: 100%; */
    display: flex;
    height: 125px; padding:0;}
p.btn.btn-add a {
   padding: 50px 0 0 0;
    display: block;
    width: 100%;
}
p.btn.btn-table a, p.btn.btn-add a { color: #fff; font-size:14px;}
p.btn.btn-table:hover{background:#49a8a4;}
p.btn.btn-add:hover{background:#49a8a4;}
.second-td{
  max-width: 150px;
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
      @if(count($results)>0)
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
</div>  
</section>








<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
		<!--<div class="col-sm-12" style="margin-top:5px;">
            <button class="btn btn-success pull-right mb-3" type="button" id="clear_all_data" >Clear officer Data</button>
        </div>	 -->
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
     <div class="page-contant">
       <div class="random-area">
        <br>

        <div class="table-responsive">
          <table class="table table-bordered list-table-remove" id="my-list-table"> 
           <thead>
            <tr> 
              <th>PS No</th>
              <th class="second-td">PS Name</th>
			  
			  
             <!-- @for($i = 1; $i <= $max_sm; $i++)
              <th>SM {{$i}}</th>
              @endfor
              
              @for($i = 1; $i <= $max_pro; $i++)
              <th>PRO {{$i}}</th>
              @endfor -->
			  
              
              <th>PO 1</th>
             

            </tr>

          </thead>
          @if(count($results)>0)
          <tbody id="oneTimetab">   
            @foreach($results as $result)

            <tr>
              <td style="width:5%">{{$result['ps_no']}} </td>
              <td class="second-td">{{$result['ps_name']}}</td>

             <!-- @for($i = 1; $i <= $max_sm; $i++)
              <td>
                @if(array_key_exists($i, $result['sm']))
                <p>{{$result['sm'][$i]['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i>  {{$result['sm'][$i]['mobile']}}</p>
                <p>{{$result['sm'][$i]['is_active']}}</p>
                <p class="btn btn-table"><a href="javascript:void(0)"  onclick="reset_otp(<?php //echo $result['sm'][$i]['mobile'] ?>)" class="pull-right">Reset OTP</a></p>
                @else 
                <p class="btn btn-add"><a href="{{$add_sm_url}}?open=1" class=""><i class="fa fa-plus"></i> Add</a></p>
                @endif
              </td>
              @endfor

              @for($i = 1; $i <= $max_pro; $i++)
              <td>
                @if(array_key_exists($i, $result['pro']))
                <p>{{$result['pro'][$i]['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i>  {{$result['pro'][$i]['mobile']}}</p>
                <p>{{$result['pro'][$i]['is_active']}}</p>
                <p class="btn btn-table"><a href="{{$result['pro'][$i]['href']}}" class="pull-left"><i class="fa fa-edit"></i> Edit</a>| <a href="javascript:void(0)"  onclick="reset_otp(<?php //echo $result['pro'][$i]['mobile'] ?>)" class="pull-right">Reset OTP</a></p>
                @else
                <p class="btn btn-add"><a href="{{$add_new_url}}?role_id=35&ps_no={{$result['ps_no']}}&role_level={{$i}}" class=""><i class="fa fa-plus"></i> Add</a></p>
                @endif
              </td>
              @endfor -->



              
              <td>
                @if(array_key_exists(1, $result['po']))
                <p>{{$result['po'][1]['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i>  {{$result['po'][1]['mobile']}}</p>
                <p>{{$result['po'][1]['is_active']}}</p>
                <p>@if($result['po'][1]['pin'])PIN: {{$result['po'][1]['pin']}}@endif</p>
                <p class="btn btn-table"><a href="{{$result['po'][1]['href']}}" class="pull-left"><i class="fa fa-edit"></i> Edit</a>
                
                  <a href="javascript:void(0)"  onclick="reset_pin(<?php echo $result['po'][1]['mobile'] ?>)" class="pull-right"><i class="fa fa-edit"></i> Set PIN</a>
                </p>
                @else
                <p class="btn btn-add"><a href="{{$add_new_url}}?role_id=34&ps_no={{$result['ps_no']}}&role_level={{1}}" class=""><i class="fa fa-plus"></i> Add</a></p>
                @endif
              </td>
             

            </tr>
            @endforeach
          </tbody>

          @else
          <tbody>
            <tr>
              <td colspan="{{$max_blo+$max_po+$max_pro}}">
                No Record Found.
              </td>
            </tr>
          </tbody>
          @endif
        </table>
      </div><!-- End Of  table responsive -->  
    </div><!-- End Of intra-table Div -->   


  </div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
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

{{-- PIN generation modal--}}
<div class="modal fade animated zoomIn" id="pin_update_form" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="pin_update_form" action="return false;">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        <input type="hidden" name="mobile" class="mobile" value="">
      <div class="modal-header">
        6 digit PIN
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-md-3"><button type="button" class="btn btn-primary" onclick="genPin()">Generate PIN</button></label>
          <div class="col-md-9">
          <input type="text" name="pin" disabled id="pin" class="form-control" size="6" maxlength="6">
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="pin_submit">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal HTML -->

	
	<div class="modal fade" id="clear_all_data1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                					
				<h4 class="modal-title w-100">Are you sure?</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p><span style="color:red">Do you really want to delete these records? This process cannot be undone.<span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok confirm_button">Confirm</a>
            </div>
        </div>
    </div>
</div>


<!-- Model Ends -->

@endsection
@section("script")
<script type="text/javascript">

  $(document).ready(function () {

    $("#otp").on('keyup change keydown',function (e) {
      $('.text-error').remove();
      if (parseInt($(this).val()) >= 0 && !isNaN($(this).val()) && $(this).val().indexOf('.') == '-1'){
        $(this).removeClass("input-error");
      }else{
        $(this).addClass("input-error");
        $(this).after("<span class='text-error text-danger text-right pull-right'>Please enter a valid 6 digit number.</span>").show();
      }
    });



    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 50,
        "aaSorting": []
      });
    }

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

  $('#pin_submit').click(function(e){
      $.ajax({
        url: "{!! $reset_pin_link !!}",
        type: 'POST',
        data: '_token='+$('#pin_update_form .token').val()+'&mobile='+$('#pin_update_form .mobile').val()+'&pin='+$('#pin_update_form #pin').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#pin_update_form .text-danger').remove();
          $('#pin_update_form input').removeClass('input-error');
          $('#pin_submit').prop('disabled',true);
          $('#pin_submit').text("Setting PIN...");
          $('#pin_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#pin_update_form').modal('hide');
            success_messages(json['message']);
            $('#pin').val('');
            location.reload();
          }

          if(json['status'] == false){
            if(json['errors']){
              error_messages(json['errors']);
            }
          }

          $('#pin_submit').prop('disabled',false);
          $('#pin_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#pin_submit').prop('disabled',false);
          $('#pin_submit').text("Submit");
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
  //for pin generation
  function reset_pin(mobile){
  
  	$('#pin_update_form .mobile').val(mobile);
  	$('#pin_update_form').modal('show');
  }
  function genPin() {
    $('#pin').val(Math.floor(100000 + Math.random() * 900000));
   }

  $('#clear_all_data').click(function(e){
	  var st_code = '{{Auth::user()->st_code}}';
	  var ac_no = '{{Auth::user()->ac_no}}';
	  var _token = $('#otp_update_form .token').val();
	  $('#clear_all_data1').modal('show');
	   $('.confirm_button').click(function(e){
		  
		  $.ajax({
        type:'POST',
				data:{st_code:st_code,ac_no:ac_no,_token:_token},
        url:"delete_user_pso",
				success:function(data){
					console.log(data.success);
					$
					$('#successDiv1').show();
          $('#successMsg1').html(data.success);
					setTimeout(function(){ $("#successDiv1").hide();
					location.reload();
					}, 3000);
				
                }
                
            });
		  
	  });
  });

</script>
@endsection