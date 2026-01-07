@extends('admin.layouts.ac.theme')
@section('content')



 <main class="mb-auto">
     
      <!--main content start-->


       
 <main role="main" class="inner cover mb-3">

<section class="mt-4">
  <div class="container-fluid">
    <div class="row">
  <button class="btn btn-danger pull-right btn_state_data" >Reset State Data</button>
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
      




      <table id="list-table5" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
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
              <td>{{$result['officername']}}</td>
              <td>{{$result['designation']}}</td>
              <td>{{$result['email']}}</td>
              <td>{{$result['mobile']}}</td>
              <td><input type="hidden" name="reset-path" class="reset-path" value="{!! $result['hash_id'] !!}"><button class="btn btn-primary reset_pin_button">Reset Couting Data</button></td>
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

    if(confirm("Are you sure you want to clear the couting data.")){
      $.ajax({
        url: "{!! url('/acceo/officer/reset-counting-data') !!}",
        type: 'POST',
        data: '_token={!! csrf_token() !!}&reset_path='+$(this).parent('td').find('.reset-path').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.reset_pin_button').prop('disabled',true);
          $(this).text("Validating...");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            success_messages(json['message']);
          }

          if(json['status'] == false){
            if(json['message']){
              error_messages(json['message']);
            }
          }

          $('.reset_pin_button').prop('disabled',false);
          $(this).text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('.reset_pin_button').prop('disabled',false);
          $(this).text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
    }else{

    }

  });





  $('.btn_state_data').click(function(e){

    if(confirm("Are you sure you want to clear the couting data.")){
      $.ajax({
        url: "{!! url('/acceo/officer/reset-counting-data-state') !!}",
        type: 'POST',
        data: '_token={!! csrf_token() !!}',
        dataType: 'json', 
        beforeSend: function() {
          $('.btn_state_data').prop('disabled',true);
          $(this).text("Validating...");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            success_messages(json['message']);
          }

          if(json['status'] == false){
            if(json['message']){
              error_messages(json['message']);
            }
          }

          $('.btn_state_data').prop('disabled',false);
          $(this).text("Submit");
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('.btn_state_data').prop('disabled',false);
          $(this).text("Submit");
        }
      }); 
    }else{

    }

  });











});
</script>
@endsection