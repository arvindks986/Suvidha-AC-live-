@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          <div class="form-group col-md-3"> <label>Election</label> 
          
            <select name="election_id" id="election_id" class="form-control" onchange ="filter()">
            @foreach($elections as $election)
              @if($election_id == $election['election_id'])
                <option value="{{$election['election_id']}}" selected="selected" >{{$election['election_type']}}</option> 
              @else 
                <option value="{{$election['election_id']}}">{{$election['election_type']}}</option> 
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

          @if(!empty($ac_no)>0)
          <form action="{!! $action !!}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
            <input type="hidden" name="ac_no" value="{!! $ac_no !!}">
            <input type="hidden" name="election_id" value="{!! $election_id !!}">
          <tbody>   
           <tr>
             <td>
              <div class="form-group">
               <input type="file" class="form-control" name="indexcard" id="indexcard" required="required" style="height: auto !important;">
              </div>
             </td>
           </tr>

          </tbody>
          <tfoot>
            <tr>
              <td colspan="15">
                <button class="btn btn-success pull-right" type="submit">Save</button>
              </td>
            </tr>
          </tfoot>
          </form>
          @else
          <tbody>
          <tr>
            <td colspan="15" cellpadding='5' align="center">
              Please Select a AC.
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


<script type="text/javascript">
function filter(){
  var url = "<?php echo $current_page ?>";
  var query = '';
    if($("#ac_no").val() != ''){
      query += '&ac_no='+$("#ac_no").val();
    }
    if($("#election_id").val() != ''){
      query += '&election_id='+$("#election_id").val();
    }
    window.location.href = url+'?'+query.substring(1);
}

$(document).ready(function(e){
  <?php foreach($custom_errors as $key => $custom_error){ ?>
    <?php foreach($custom_error as $second_key => $err){ ?>
    <?php if($err){ ?>
      $("input[name = 'elector[<?php echo $key ?>][<?php echo $second_key ?>]'").after("<span class='text-danger small_text'><?php echo $err; ?></span>");
      $("input[name = 'elector[<?php echo $key ?>][<?php echo $second_key ?>]'").addClass('is-valid');
    <?php } ?>
    <?php } ?>
  <?php } ?>
});
</script>
@endsection