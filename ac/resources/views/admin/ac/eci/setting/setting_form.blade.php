@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .fullwidth{
    float: left;
    width: 100%;
  }
</style>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 

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





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
<br>

      <form class="form-horizontal" method="post" action="{!! $action !!}">
       
            <input type="hidden" value="{!! csrf_token() !!}" name="_token">
            <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Pin Setup Popup after login</label>
                <select class="form-control col-md-9 pull-left" name="two_step" id="two_step">
                  <?php if(isset($two_step) && $two_step==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('two_step') !!}</span>
                 @endif 
           
                </div>

                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">2 Step login with PIN</label>
                <select class="form-control col-md-9 pull-left" name="two_step_login" id="two_step_login">
                  <?php if(isset($two_step_login) && $two_step_login==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('two_step_login') !!}</span>
                 @endif 
           
                </div>


                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Skip Login password on same netowrk</label>
                <select class="form-control col-md-9 pull-left" name="skip_password_network" id="skip_password_network">
                  <?php if(isset($skip_password_network) && $skip_password_network==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('skip_password_network') !!}</span>
                 @endif 
           
                </div>


                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Disable Concurrent login</label>
                <select class="form-control col-md-9 pull-left" name="concurrent_login" id="concurrent_login">
                  <?php if(isset($concurrent_login) && $concurrent_login==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('concurrent_login') !!}</span>
                 @endif 
           
                </div>


                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Auto Logout After<small>(in min)</small></label>
                <input type="text" class="form-control col-md-9 pull-left" name="auto_logout_after" id="auto_logout_after" value="{{$auto_logout_after}}">
                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('auto_logout_after') !!}</span>
                 @endif 
                <small class="text-error text-warning text-right pull-right">enter 0 if you want to keep Auto Logout disabled.</small>
                </div>
				
				<fieldset class="mt-3 fieldset">
            <legend>Booth App</legend>
            <div class="form-group fullwidth">
             

                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>State</th>
                      <th>District</th>
                      <th>AC</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="booth_app_body">

                  @foreach($booth_app as $iterate_booth)

                  @if(!empty($iterate_booth['states']) && !empty($iterate_booth['districts']) && !empty($iterate_booth['acs']))

                  <tr id="row_<?php echo $i ?>">
                    <td>

            <select name="booth_app[<?php echo $i ?>][states]" id="booth_app_state_<?php echo $i ?>" class="sumoselect pull-left form-control fullwidth" onchange="load_district(<?php echo $i ?>)">
            <option value="">Select</option>
            @foreach($states as $iterate_state)
              @if($iterate_booth['states'] == $iterate_state['st_code'])
              <option value="{{$iterate_state['st_code']}}" selected="selected">{{$iterate_state['st_name']}}</option>
              @else
              <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option>
              @endif
            @endforeach
            </select>
       
            </td>

            <td>
  
          <select name="booth_app[<?php echo $i ?>][districts]" id="booth_app_district_<?php echo $i ?>" class="sumoselect pull-left form-control fullwidth"  onchange="load_acs(<?php echo $i ?>)">
          @foreach($districts as $iterate_district)
            @if($iterate_booth['states'] == $iterate_district['st_code'])
              @if($iterate_district['dist_no'] == $iterate_booth['districts'])
              <option value="{{$iterate_district['dist_no']}}" selected="selected">{{$iterate_district['dist_no'].'-'.$iterate_district['dist_name']}}</option>
              @else
              <option value="{{$iterate_district['dist_no']}}">{{$iterate_district['dist_no'].'-'.$iterate_district['dist_name']}}</option>
              @endif
            @endif
          @endforeach
          </select>
  
          </td>

          <td>

          <select name="booth_app[<?php echo $i ?>][acs][]" id="booth_app_ac_<?php echo $i ?>" class="sumoselect pull-left form-control fullwidth" multiple="multiple">
          @foreach($acs as $iterate_ac)
            @if($iterate_booth['states'] == $iterate_ac['st_code'] && $iterate_booth['districts'] == $iterate_ac['dist_no'])
              @if(in_array($iterate_ac['ac_no'], $iterate_booth['acs']))
              <option value="{{$iterate_ac['ac_no']}}" selected="selected">{{$iterate_ac['ac_no'].'-'.$iterate_ac['ac_name']}}</option>
              @else
              <option value="{{$iterate_ac['ac_no']}}">{{$iterate_ac['ac_no'].'-'.$iterate_ac['ac_name']}}</option>
              @endif
            @endif
          @endforeach
          </select>

          </td>
          <td>
            <button type='button' class='btn btn-success' onclick='remove_row(<?php echo $i ?>)'><i class='fa fa-trash'></i></button>
          </td>
        </tr>
         <?php $i++; ?>
         @endif
         @endforeach


        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" align="right"><button type="button" id="add_new_booth_app" class="btn btn-success"><i class='fa fa-plus'></i> Add New</button></td>
          </tr>
        </tfoot>
      </table>
  
      </div>


          </fieldset>


                


          <div class="form-group fullwidth">
            <button type="submit" class="pull-right btn btn-primary" style="margin-top: 30px;">Save</button>
          </div>


     </form>        
      
        


         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
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
  var i = <?php echo $i ?>;
  $(document).ready(function () {
    $('#add_new_booth_app').click(function(){
      var html = "";
      html += "<tr id='row_"+i+"'>";
      html += "<td><select name='booth_app["+i+"][states]' id='booth_app_state_"+i+"' class='sumoselect pull-left form-control fullwidth' onchange='load_district("+i+")'>";
      html += "<option value=''>Select</option>";
      $.each(<?php echo json_encode($states) ?>, function(index, object){
         html += "<option value='"+object.st_code+"'>"+object.st_name+"</option>";
      });
      html += "</td>";
      html += "<td><select name='booth_app["+i+"][districts]' id='booth_app_district_"+i+"' class='sumoselect pull-left form-control fullwidth' onchange='load_acs("+i+")'>";
      html += "</td>";
      html += "<td><select name='booth_app["+i+"][acs][]' id='booth_app_ac_"+i+"' class='sumoselect pull-left form-control fullwidth' multiple='multiple'></td>";
      html += "<td><button type='button' class='btn btn-success' onclick='remove_row("+i+")'><i class='fa fa-trash'></i></button></td>";
      html += "</tr>";
      $("#booth_app_body").append(html);
      i++;
    });
  });

  function load_district(index_no){
    var html = '';
    html += "<option value=''>Select</option>";
    $.each(<?php echo json_encode($districts) ?>, function(index, object){
      if(object.st_code == $('#row_'+index_no).find('#booth_app_state_'+index_no).val()){
        html += "<option value='"+object.dist_no+"'>"+object.dist_no+'-'+object.dist_name+"</option>";
      }
    });
    $("#booth_app_district_"+index_no).html(html);
  }

  function load_acs(index_no){
    var html = '';
    $.each(<?php echo json_encode($acs) ?>, function(index, object){
      if(object.st_code == $('#row_'+index_no).find('#booth_app_state_'+index_no).val() && object.dist_no == $('#row_'+index_no).find('#booth_app_district_'+index_no).val()){
        html += "<option value='"+object.ac_no+"'>"+object.ac_no+'-'+object.ac_name+"</option>";
      }
    });
    $("#booth_app_ac_"+index_no).html(html);
  }

  function remove_row(id){
    $('#row_'+id).remove();
  }

</script>

@endsection