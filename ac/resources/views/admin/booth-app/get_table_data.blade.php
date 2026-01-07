@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  *{
    font-size: 12px !important;
  }
  td,th{
    max-width: 100px !important;
  }
  .fullwidth{
    width: 100%;
  }
</style>
<main role="main" class="inner cover mb-3">

  <section style="margin-top: 20px;">
    <div class="container-fluid">

      <div class="row">
        <div class="card text-left" style="width:100%; margin:0 auto;">
          <div class=" card-header">
            <div class=" row">
              <div class="col"><h4> {!! $heading_title !!}</h4>

              </div> 
              
            </div>
          </div>

          <div class="card-body">  

            <div class="table-responsive1 fullwidth">
            <table class="table">
              <form id="search_form_id" method="get" onsubmit="return false;" style="margin-bottom: 20px;">
                <tr>
                  <td> 
                    <div class="row">
                      <div class="col-md-6 ">
                        <div class="fullwidth query_div">
                          @if(count($where)>0)
                          @foreach($where as $iterate_where)
                          <div class="row where_row" id="{{$i}}">
                            <div class="col-md-4">
                              <label class="fullwidth">Where</label> 
                              <select name="where[{{$i}}][condition]" id="condition_{{$i}}" class="condition form-control sumoselect where">
                                <option value="">Select</option>
                                @if(count($results)>0)
                                @foreach($results[0] as $head_key => $head_result)
                                @if($iterate_where['condition'] == $head_key)
                                <option value="<?php echo $head_key ?>" selected="selected"><?php echo $head_key ?></option>
                                @else
                                <option value="<?php echo $head_key ?>"><?php echo $head_key ?></option>
                                @endif
                                @endforeach
                                @endif
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label class="fullwidth">Operator</label> 
                              <select name="where[{{$i}}][operator]" id="operator_{{$i}}" class="operator form-control sumoselect where">
                                <option value="">Select</option>
                                @foreach($operators as $iterate_key => $iterate_operator)
                                @if($iterate_where['operator'] == $iterate_key)
                                <option value="<?php echo $iterate_key ?>" selected="selected"><?php echo $iterate_operator; ?></option>
                                @else
                                <option value="<?php echo $iterate_key ?>"><?php echo $iterate_operator; ?></option>
                                @endif
                                @endforeach
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label class="fullwidth">Value</label> 
                              <input name="where[{{$i}}][value]" id="value_{{$i}}" class="value form-control where" value="{{$iterate_where['value']}}">
                            </div>
                          </div>
                          <?php $i++; ?>
                          @endforeach
                          @else 
                          <div class="row where_row" id="{{$i}}">
                            <div class="col-md-4">
                              <label class="fullwidth">Where</label> 
                              <select name="where[{{$i}}][condition]" id="condition_{{$i}}" class="condition form-control sumoselect where">
                                <option value="">Select</option>
                                @if(count($results)>0)
                                @foreach($results[0] as $head_key => $head_result)
                                <option value="<?php echo $head_key ?>"><?php echo $head_key ?></option>
                                @endforeach
                                @endif
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label class="fullwidth">Operator</label> 
                              <select name="where[{{$i}}][operator]" id="operator_{{$i}}" class="operator form-control sumoselect where">
                                <option value="">Select</option>
                                @foreach($operators as $iterate_key => $iterate_operator)
                                <option value="<?php echo $iterate_key ?>"><?php echo $iterate_operator; ?></option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label class="fullwidth">Value</label> 
                              <input name="where[{{$i}}][value]" id="value_{{$i}}" class="value form-control where" value="">
                            </div>
                          </div>
                          @endif
                        </div>
                        <div class="fullwidth" style="margin-top: 15px;">

                          <button type="button" class="btn btn-warning" onclick="add_query_param()">Add Query Param</button>

                        </div>
                      </div>

                      <div class="col-md-4"> 
                        <div class="row">
                          <div class="col-md-6">
                            <label class="fullwidth">Order By </label> 
                            <select name="order_by" id="order_by" class="form-control sumoselect" multiple="multiple">
                              @if(count($results)>0)
                              @foreach($results[0] as $head_key => $head_result)
                              @if(in_array($head_key,$order_by))
                              <option value="<?php echo $head_key ?>" selected="selected"><?php echo $head_key ?></option>
                              @else
                              <option value="<?php echo $head_key ?>"><?php echo $head_key ?></option>
                              @endif
                              @endforeach
                              @endif
                            </select>
                          </div>


                          <div class="col-md-6">
                            <label class="fullwidth">Group By </label> 
                            <select name="group_by" id="group_by" class="form-control sumoselect" multiple="multiple">
                              @if(count($results)>0)
                              @foreach($results[0] as $head_key => $head_result)
                              @if(in_array($head_key,$group_by))
                              <option value="<?php echo $head_key ?>" selected="selected"><?php echo $head_key ?></option>
                              @else
                              <option value="<?php echo $head_key ?>"><?php echo $head_key ?></option>
                              @endif
                              @endforeach
                              @endif
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-1">
                        <label class="fullwidth">Record</label> 
                        <input type="number" name="record" id="record" class="form-control" value="{{$record}}">
                      </div>

                      <div class="col-md-1"> 
                        <label style="visibility: hidden;">Group By </label> 
                        <button type="button" class="btn btn-success form-control" style="color: #FFF" onclick="filter()">Search</button>
                      </div>
                    </div>
                  </td>
                </tr>
              </form>
            </table>
          </div>

            <hr>

            <div class="table-responsive">
            <table style="width: 100%; margin-bottom: 20px;">
              <tr style="width: 100%">
                <td style="width: 100%;font-size: 14px !important">
                  {{$sql_query}}
                </td>
              </tr>
            </table>
          </div>
            <hr>

            <div class="table-responsive">
              <table id="my-list-table" class="table table-bordered">

                @if(count($results)>0)

                <thead>
                  <tr> 
                   @foreach($results[0] as $head_key => $head_result)
                   <th><?php echo $head_key ?></th>
                   @endforeach
                 </tr>
               </thead>

               <tbody> 
                @foreach($results as $result)
                <tr>
                  @foreach($result as $key => $value)
                  @if($key == 'image')
                  <td>{{substr($value,0,20)}}... </td>
                  @else
                  <td>{{$value}} </td>
                  @endif
                  @endforeach
                </tr>
                @endforeach
                @else
                <tr>
                  <td align="center">
                    <p style="font-size: 20px !important;">
                      No Record Found. <a href="<?php echo $action ?>?type=<?php echo $type ?>" class="btn btn-warning">Clear Search</a>
                    </p>
                  </td>
                </tr>
                @endif
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</main>


@endsection

@section("script")
<script type="text/javascript">
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": <?php echo $record ?>,
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
          search : false,
          searchText : 'Search...',
        });
      });
    }
  });
  function filter(){
    var url = "<?php echo $action ?>";
    var query = '&type=<?php echo $type; ?>&page=<?php echo $page; ?>';
    if($("#order_by").length > 0 && $("#order_by").val() != ''){
      query += '&order_by='+$("#order_by").val();
    }
    if($("#group_by").length > 0 && $("#group_by").val() != ''){
      query += '&group_by='+$("#group_by").val();
    }
    if($("#record").length > 0 && $("#record").val() != ''){
      query += '&record='+$("#record").val();
    }
    var where_query = '';
    $('.where_row').each(function(index,object){
      var id = $(object).attr('id');
      var condition = $('#'+id).find('#condition_'+id).val();
      var operator  = $('#'+id).find('#operator_'+id).val();
      var value     = $('#'+id).find('#value_'+id).val();
      if(condition.trim() != '' && operator.trim() != ''){
        where_query += '/';
        where_query += $('#'+id).find('#condition_'+id).val();
        where_query += ' '+$('#'+id).find('#operator_'+id).val()+' ';
        where_query += $('#'+id).find('#value_'+id).val();
      }
    });

    console.log(where_query);

    if(where_query != ''){
      query += '&where='+btoa(where_query.substring(1));
    }

    window.location.href = url+'?'+query.substring(1);

  }

  function clear_search(){
    window.location.href = "<?php echo $action ?>";
  }

  function add_query_param(){
    i = $('.where_row').length;
    var html = '';
    html += "<div class='row where_row' id='"+i+"'>";
    html += "<div class='col-md-4'>";
    html += "<label class='fullwidth'>Where</label>";
    html += "<select name='where["+i+"][condition]' id='condition_"+i+"' class='condition form-control sumoselect where'>";
    @if(count($results)>0)
    @foreach($results[0] as $head_key => $head_result)
    html += "<option value='<?php echo $head_key ?>'><?php echo $head_key ?></option>";
    @endforeach
    @endif
    html += "</select>";
    html += "</div>";
    html += "<div class='col-md-4'>";
    html += "<label class='fullwidth'>Operator</label> ";
    html += "<select name='where["+i+"][operator]' id='operator_"+i+"' class='operator form-control sumoselect where'>";
    @foreach($operators as $iterate_key => $iterate_operator)
    html += "<option value='<?php echo $iterate_key ?>'><?php echo $iterate_operator ?></option>";
    @endforeach
    html += "</select>";
    html += "</div>";
    html += "<div class='col-md-4'>";
    html += "<label class='fullwidth'>Value</label> ";
    html += "<input name='where["+i+"][value]' id='value_"+i+"' class='value form-control where' value=''>";
    html += "</div>";
    html += "</div>";
    $('.query_div').append(html);
    i++;
  }
</script>
@endsection