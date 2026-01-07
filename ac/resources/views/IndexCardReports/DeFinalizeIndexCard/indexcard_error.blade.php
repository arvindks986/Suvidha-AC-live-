@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<style type="text/css">
  .heading th{
    text-transform: capitalize;
    text-align: left;
  }
  .complain-heading-main{
    text-transform: capitalize;
    text-align: center;
  }
</style>
<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          <div class="form-group col-md-3"> <label>State</label> 
          
            <select name="st_code" id="st_code" class="form-control" onchange ="filter()">
              <option value="">Select State</option>
            @foreach($states as $iterate_state)
              @if($st_code == $iterate_state['st_code'])
                <option value="{{$iterate_state['st_code']}}" selected="selected" >{{$iterate_state['st_name']}}</option> 
              @else 
                <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>

          <div class="form-group col-md-3"> <label>AC </label> 
          
            <select name="ac_no" id="ac_no" class="form-control" onchange ="filter()">
            <option value="">Select AC</option>
            @foreach($acs as $result)
              @if($ac_no == $result['ac_no'])
                <option value="{{$result['ac_no']}}" selected="selected" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
              @else 
                <option value="{{$result['ac_no']}}" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
         
        </form>   
  
    
  </div>
</section>

<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


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
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
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
          <table class="table table-bordered ">

          @if(!empty($ac_no)>0 && count($results)>0)
            
 
          @else
          <tbody>
          <tr>
            <td colspan="6" cellpadding='5' align="center">
              No Record Found.
            </td>
          </tr>
          </tbody>
          @endif

           </table>
         </div><!-- End Of  table responsive -->  
       </div>
     </div>
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
</section>
</main>


<!-- Modal for finalised Cheack -->
<div class="modal fade complain_info_modal" id="complain_info_modal" tabindex="-1" role="dialog"  aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">List of Changes</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">

    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal for finalised -->



<script type="text/javascript">
$(document).ready(function(e){      
  $('.btn_more_info').click(function(e){
    var html = $(this).next('.complain_info').html();
    $('#complain_info_modal .modal-body').html(html);
    $('#complain_info_modal').modal('show');
  });
});
function filter(){
  var url = "<?php echo $current_page ?>";
  var query = '';
  query += "&complain=1";
    if($("#ac_no").val() != ''){
      query += '&ac_no='+$("#ac_no").val();
    }
    if($("#st_code").val() != ''){
      query += '&st_code='+$("#st_code").val();
    }
    window.location.href = url+'?'+query.substring(1);
}
</script>
@endsection