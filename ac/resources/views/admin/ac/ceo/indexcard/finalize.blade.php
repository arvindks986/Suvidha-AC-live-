@extends('admin.layouts.ac.theme')
@section('bradcome')
  <li><span class="icon icon-beaker"> </span> Finalize Indexcard Entry</li>
@endsection
@section('content')
<style type="text/css">
  .capatlize th{
    text-transform: capitalize;
    font-size: 12px;
    text-align: center;
  }
  .table th, .table td{
    padding: 3px !important;
  }
  .table td .form-control{
    font-size: 12px;
  }
  .small_text{
    font-size: 10px;
    line-height: 12px;
  }
</style>



<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


  @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
          @endif
          @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
          @endif   


<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>{!! $heading_title !!}</h4></div> 
                  <div class="col"><p class="mb-0 text-right">

                    @if(isset($filter_buttons) && count($filter_buttons)>0)
                            @foreach($filter_buttons as $button)
                                <?php $but = explode(':',$button); ?>
                                <b>{!! $but[0] !!}:</b>
                                <span class="badge badge-info">{!! $but[1] !!}</span>
                            @endforeach  
                    @endif
                



                    &nbsp;&nbsp; 
                  <b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp;  </p></div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
              
              @if($is_finalize == 0)
              <div class="fullwidth" style="float: left;width: 100%;padding: 40px 30px;text-align: center;">
              <p style="padding-bottom: 30px;">Are you sure you want to finalize Index card entry. please verify the detail filled as after finalizing you will not be able to edit the details again.</p>
              <p>
              <button class="btn btn-success index_card_finalize" type="button">Yes</button>
              <button class="btn btn-danger go_to_dashboard" type="button">Go to dashboard</button>
              </p>
              </div>
              @else
              <div class="fullwidth" style="float: left;width: 100%;padding: 40px 30px;text-align: center;">
              <p style="padding-bottom: 30px;">Index Card has been finalized. </p>
              
              </div>
              @endif
            
         </div>
       </div>
     </div>
      </div>  
        
         
      </div>
      
</section>
</main>

@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function(e){
    $('.go_to_dashboard').click(function(e){
      window.location.href = "<?php echo url('/officer-login') ?>";
    });

    $('.index_card_finalize').click(function(e){
      if(confirm("Are you sure you want to finalize.")){
        $.ajax({
          url: "{!! url('/roac/indexcard/finalize/post') !!}",
          type: 'POST',
          data: '_token={!! csrf_token() !!}',
          dataType: 'json', 
          beforeSend: function() {
            $('.index_card_finalize').prop('disabled',true);
            $('.index_card_finalize').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          },  
          complete: function() {
            $('.loading_spinner').remove();
            $('.index_card_finalize').prop('disabled',false);
          },        
          success: function(json) {
            if(json['status'] == true){
              location.reload();
            }
            if(json['status'] == false){
              error_messages(json['message']);
            }
            $('.loading_spinner').remove();
            $('.index_card_finalize').prop('disabled',false);
          },
          error: function(data) {
            var errors = data.responseJSON;
            $('.loading_spinner').remove();
            $('.index_card_finalize').prop('disabled',false);
          }
        });
      } 
    });

  });
</script>
@endsection