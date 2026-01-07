@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
.btn-danger{
  background: red !important;
  color: #FFF !important;
}
</style>
<link rel="stylesheet" href="{!! url('theme/css/booth.css') !!}">
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


            <div class="row">

              <div class="col">


                      <div class="card p-0">
                        <div class="card-header text-center p-2">
                          <h5>Polling Stations</h5>
                        </div>
                        <div class="card-body p-0">
                          <div class="row">
                            <div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
                              <span class="upperCase">Total</span>
                              <h1 class="numberset green" id="total_ps">{{$total_ps}}</h1>          
                            </div>

                            <div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
                              <span class="upperCase">Verified</span>
                              <h1 class="numberset green" id="total_verified">{{$total_verified}}</h1>

                            </div>
                            <div class="col text-center pt-2 pb-2">
                              <span class="upperCase">Unverified</span>
                              <h1 class="numberset red" id="total_unverfied">{{$total_unverified}}</h1>

                            </div>

                          </div></div></div></div>
                
       

              <div class="col">


                <div class="card p-0">
                        <div class="card-header text-center p-2">
                          <h5>Electors</h5>
                        </div>
                        <div class="card-body p-0">
                          <div class="row">
                            <div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
                              <span class="upperCase">Male</span>
                              <h1 class="numberset green" id="">{{$male}}</h1>          
                            </div>

                            <div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
                              <span class="upperCase">Female</span>
                              <h1 class="numberset green" id="">{{$female}}</h1>

                            </div>
                            <div class="col text-center pt-2 pb-2" style="border-right:1px solid #d5d5d5; min-height: 113px;">
                              <span class="upperCase">Other</span>
                              <h1 class="numberset green" id="">{{$other}}</h1>

                            </div>
                            <div class="col text-center pt-2 pb-2">
                              <span class="upperCase">Total</span>
                              <h1 class="numberset green" id="">{{$total}}</h1>

                            </div>


                          </div></div></div>





                </div> 
 
              </div>
            


      <div class="page-contant card">
        <div class="random-area card-body">
          


          <div class="col-sm-12">
            <button class="btn btn-success pull-right mb-3" type="button" id="verify_all" onclick="verify('all')">Mark all as Verified</button>
          </div>


   
              
         
   


          <div class="table-responsive">
            <table class="table table-bordered list-table-remove" id="my-list-table"> 
              <thead>
                <tr> 
                  
                  <th class="second-td">PS No & Name</th>
                  <th class="second-td">Male</th>
                  <th class="second-td">Female</th>
                  <th class="second-td">Other</th>
                  <th class="second-td">Total</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($results as $result)
                <tr class="">
                  <td>{{$result['ps_name_and_no']}}</td>
                  <td class="second-td">{{$result['male']}}</td>
                  <td class="second-td">{{$result['female']}}</td>
                  <td class="second-td">{{$result['other']}}</td>
                  <td class="second-td">{{$result['total']}}</td>  
                  <td class="second-td status verify_status">{{$result['status']}}</td>     
                  <td>
                    @if(!$result['is_verify'])
                    <button class="btn btn-primary verify_ps " type="button" id="verify_{{$result['ps_no']}}" onclick="verify(<?php echo $result['ps_no'] ?>)" >Mark as Verified</button>
                    <button class="btn un_verify_ps display_none btn-danger" type="button" id="unverify_{{$result['ps_no']}}" onclick="unverify(<?php echo $result['ps_no'] ?>)">Mark as not verified</button>
                    @else
                    <button class="btn btn-primary verify_ps display_none" type="button" id="verify_{{$result['ps_no']}}" onclick="verify(<?php echo $result['ps_no'] ?>)" >Mark as Verified</button>
                    <button class="btn un_verify_ps btn-danger" type="button" id="unverify_{{$result['ps_no']}}" onclick="unverify(<?php echo $result['ps_no'] ?>)">Mark as not verified</button>
                    
                    @endif
                    
                  </td>           
                </tr>
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

@endsection

@section('script')
<script type="text/javascript">
function verify(ps_no){
  $.ajax({
      url: "{!! $action !!}",
      type: 'POST',
      data: "_token=<?php echo csrf_token() ?>&is_verify=1&ps_no="+ps_no,
      dataType: 'json', 
      beforeSend: function() {
        $('.jq-toast-wrap').html('');
        $('.modal').removeClass('animated shake');
        $('#verify_'+ps_no).prop('disabled',true);
        $('#verify_'+ps_no).append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
      },  
      complete: function() {
      },        
      success: function(json) {
        if(json['success'] == false){
          if(json['errors']['warning']){
            error_messages(json['errors']['warning']);
          }else{
            error_messages("");
          }
        }else{
          if(ps_no == 'all'){
            $(".verify_ps").addClass("display_none");
            $(".un_verify_ps").removeClass("display_none");
            $('.status').text("Verified");
          }else{
            $('#verify_'+ps_no).addClass("display_none");
            $('#unverify_'+ps_no).removeClass("display_none");
            $('#verify_'+ps_no).parent('td').parent('tr').find('.status').text("Verified");
          }
          success_messages(json['messages']);
        }
        count_update();
        $('#verify_'+ps_no).prop('disabled',false);
        $('.loading_spinner').remove();
      },
      error: function(data) {
        var errors = data.responseJSON;
        $('#verify_'+ps_no).prop('disabled',false);
        $('.loading_spinner').remove();
      }
    }); 
}

function unverify(ps_no){
  $.ajax({
      url: "{!! $action !!}",
      type: 'POST',
      data: "_token=<?php echo csrf_token() ?>&is_verify=0&ps_no="+ps_no,
      dataType: 'json', 
      beforeSend: function() {
        $('.jq-toast-wrap').html('');
        $('.modal').removeClass('animated shake');
        $('#unverify_'+ps_no).prop('disabled',true);
        $('#unverify_'+ps_no).append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
      },  
      complete: function() {
      },        
      success: function(json) {
        if(json['success'] == false){
          if(json['errors']['warning']){
            error_messages(json['errors']['warning']);
          }
        }else{
          $('#unverify_'+ps_no).addClass("display_none");
          $('#verify_'+ps_no).removeClass("display_none");
          $('#unverify_'+ps_no).parent('td').parent('tr').find('.status').text("Not Verified");
          success_messages(json['messages']);
        }
        count_update();
        $('#unverify_'+ps_no).prop('disabled',false);
        $('.loading_spinner').remove();
      },
      error: function(data) {
        var errors = data.responseJSON;
        $('#unverify_'+ps_no).prop('disabled',false);
        $('.loading_spinner').remove();
      }
    }); 
} 

function count_update(){
  var total_verified = 0;
  var total_unverified = 0;
  $(".verify_status").each(function(index, object){
    console.log($(object).text());
    if($(object).text().toLowerCase() == 'verified'){
      total_verified++;
    }else{
      total_unverified++;
    }
  });
  $('#total_verified').text(total_verified);
  $('#total_unverfied').text(total_unverified);
}
</script>
@endsection