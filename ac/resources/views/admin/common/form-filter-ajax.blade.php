<?php if(isset($phases) && count($phases)>0){ ?>
<section class="color-white statistics dashboard p-2" style="border-bottom:1px solid #eee;">
    <div class="container-fluid">
      <div class="row">  
        <div class="col-md-8">
       
          <form id="generate_report_id"  class="row" method="get" onsubmit="return false;">
            
              @if(isset($active_tab))
            <input type="hidden" name="tab" id="tab" class="filter-form" value="{{$active_tab}}">
            @else
            <input type="hidden" name="tab" id="tab" class="filter-form" value="">
            @endif  

              <div class="form-group col"> 
                <label>Phase</label> 
                 <select name="phase_no" id="phase_no" class="form-control filter-form" >
                  <option value="">Select Phase</option>
                  @foreach($phases as $result)
                    @if($result['phase_no'] == $phase_no)
                    <option value="{{$result['phase_no']}}" selected="selected">{{$result['phase_name']}}</option>  
                    @else
                    <option value="{{$result['phase_no']}}">{{$result['phase_name']}}</option>  
                    @endif
                  @endforeach
                </select>
              </div>

              <div class="form-group col"> 
                <label>State</label> 
                 <select name="st_code" id="st_code" class="form-control filter-form" >
                  <option value="">Select State</option>
                  @foreach($states as $result)
                    @if($result['st_code'] == $st_code)
                    <option value="{{$result['st_code']}}" selected="selected">{{$result['st_name']}}</option> 
                    @else
                    <option value="{{$result['st_code']}}">{{$result['st_name']}}</option>  
                    @endif
                  @endforeach
                </select>
              </div>

              <div class="form-group col"> 
                <label>AC</label> 
                 <select name="ac_no" id="ac_no" class="form-control filter-form" >
                  <option value="">Select AC</option>
                  @foreach($acs as $result)
                    @if($result['ac_no'] == $ac_no)
                    <option value="{{$result['ac_no']}}" selected="selected">{{$result['ac_name']}}</option>  
                    @else
                    <option value="{{$result['ac_no']}}">{{$result['ac_name']}}</option> 
                    @endif
                  @endforeach
                </select>
              </div>

              <div class="form-group col"> 
                <label>Polling Station</label> 
                 <select name="ps_no" id="ps_no" class="form-control filter-form" >
                  <option value="">Select PS</option>
    
                </select>
              </div>
    
            
            <div class="form-group col">
          
              <label style="visibility: hidden;">Filter</label> 
              <button class="form-control btn btn-activelist  active " type="button" onclick="filter()">Filter</button>
           
          </div>
          

        </form> 

      </div>
      <div class='col-md-4' id='filter_poll_percentage'>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $(document).ready(function(e){
    $('#phase_no').change(function(e){
      $.ajax({
        url: "{!! url('load-state-by-phase') !!}",
        type: 'GET',
        data: 'phase_no='+$('#phase_no').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('#phase_no').after("<i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {
          html = '';
          html += "<option value=''>Select</option>";
          $.each(json, function(index, object){
            html += "<option value='"+object.st_code+"'>"+object.st_name+"</option>";
          });
          $("#st_code").empty().append(html);
          $("#ac_no").empty().append('');
          $("#ps_no").empty().append('');
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      }); 
    });

    $('#st_code').change(function(e){
      $.ajax({
        url: "{!! url('load-ac-by-state') !!}",
        type: 'GET',
        data: 'phase_no='+$('#phase_no').val()+'&st_code='+$('#st_code').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('#st_code').after("<i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {
          var st_code = "<?php echo $st_code; ?>";
          var ac_no = "<?php echo $ac_no; ?>";
          html = '';
          html += "<option value=''>Select</option>";
          $.each(json, function(index, object){
            html += "<option value='"+object.ac_no+"'>"+object.ac_name+"</option>";
          });
          $("#ac_no").empty().append(html);
          $("#ps_no").empty().append('');
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      }); 
    });

    $('#ac_no').change(function(e){
      $.ajax({
        url: "{!! url('load-ps-by-ac') !!}",
        type: 'GET',
        data: 'phase_no='+$('#phase_no').val()+'&st_code='+$('#st_code').val()+'&ac_no='+$('#ac_no').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('#ac_no').after("<i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {
          var ps_no = '<?php echo $ps_no ?>';
          html = '';
          html += "<option value=''>Select</option>";
          $.each(json, function(index, object){
            html += "<option value='"+object.ps_no+"'>"+object.ps_name+"</option>";
          });
          $("#ps_no").empty().append(html);
          if($("#ac_no").val()!='' && $("#phase_no").val()!='' && $("#st_code").val()!=''){
            $("#ps_no").val(ps_no);
          }
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      }); 
    });
    if($("#ac_no").val()!='' && $("#phase_no").val()!='' && $("#st_code").val()!=''){
      $('#ac_no').trigger("change");
    }
});
    
</script>


<script type="text/javascript">
  function filter(type_code){
    var url = "<?php echo $filter_action ?>";
    var query = '';
    if($('#tab').val() && $('#tab').val() != ''){
      query += "&tab="+$('#tab').val();
    }
    if($('#phase_no').val() && $('#phase_no').val() != ''){
      query += "&phase_no="+$('#phase_no').val();
    }
    if($('#st_code').val() && $('#st_code').val() != ''){
      query += "&st_code="+$('#st_code').val();
    }
    if($('#ac_no').val() && $('#ac_no').val() != ''){
      query += "&ac_no="+$('#ac_no').val();
    }
    if($('#ps_no').val() && $('#ps_no').val() != ''){
      query += "&ps_no="+$('#ps_no').val();
    }
    window.location.href = url+'?'+query.substring(1);
  }
</script>

<?php } ?>