
<form id="nomination_form" method="POST"  action="{{$action}}" autocomplete='off' enctype="x-www-urlencoded">


  <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
  @if(isset($nomination_id) && $nomination_id)
  <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
  @endif
  <div class="modal-header">
    <h4 class="modal-title">{!! $heading_title !!}</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>

  </div>
  <div class="modal-body">






    <div class="form-group row">
      <div class="col-sm-2"><label for="statename">Election Type <sup>*</sup></label></div>
      <div class="col">
        <div class="" style="width:100%;">
          <select name="election_id" class="form-control" id="nom_election_id" onchange="filter_nom_respective_state(this.value)">
            <option value="">-- Select Election --</option>
            @foreach($election_types as $iterate_election)
            @if($election_id == $iterate_election['election_id'])
            <option value="{{ $iterate_election['election_id'] }}" data_type_id= "{{ $iterate_election['election_type_id'] }}" selected="selected">{{ $iterate_election['name'] }}</option>
            @else 
            <option value="{{ $iterate_election['election_id'] }}" data_type_id= "{{ $iterate_election['election_type_id'] }}"> {{ $iterate_election['name'] }}</option>
            @endif
            @endforeach

          </select>
          @if ($errors->has('election_id'))
          <span class="error">{{ $errors->first('election_id') }}</span>
          @endif 
        </div>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-sm-2"><label for="statename">State Name <sup>*</sup></label></div>
      <div class="col">
        <div class="" style="width:100%;">
          <select name="st_code" class="form-control" id="nom_st_code" onchange="filter_nom_respective_acs(this.value)">
            <option value="">Select State</option>
          </select>
          @if ($errors->has('st_code'))
          <span class="error">{{ $errors->first('st_code') }}</span>
          @endif 
        </div>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-sm-2"><label for="statename">AC <sup>*</sup></label></div>
      <div class="col">
        <div class="" style="width:100%;">
          <select name="ac_no" class="consttype form-control" id="nom_ac_no">
            <option value="">-- Select AC --</option>
          </select>
          @if ($errors->has('ac_no'))
          <span class="error">{{ $errors->first('ac_no') }}</span>
          @endif
        </div>
      </div>
    </div>




  </div>

  <div class="modal-footer">
    <div class="form-group mb-1">
      <label class="col-md-3 pull-left" style="visibility: hidden;"></label>
      <button type="button" id="new_nomination" class="btn btn-large btn-primary">Next</button>
    </div>
  </div>
</form>




<script>
  function filter_nom_respective_state(nom_election_id){
    html = '';
    html += "<option value=''>Select</option>";
    var states = <?php echo json_encode($states); ?>;
    var nom_st_code = "<?php echo $st_code ?>";
    $.each(states, function(index, object){
      if(object.election_id == nom_election_id){
        if(object.st_code == nom_st_code){
          html += "<option value='"+object.st_code+"' selected='selected'>"+object.st_name+"</option>";
        }else{
          html += "<option value='"+object.st_code+"'>"+object.st_name+"</option>";
        }
      }
    });
    $("#nom_st_code").empty().append(html);
    if(nom_st_code == ''){
      $("#nom_st_code").val($("#nom_st_code option:first").val());
    }
  }

  function filter_nom_respective_acs(nom_st_code){
    html = '';
    html += "<option value=''>Select</option>";
    var acs = <?php echo json_encode($acs); ?>;
    var nom_election_id = $('#nom_election_id').val();
    var nom_st_code = $('#nom_st_code').val();
    var nom_ac_no = "<?php echo $ac_no; ?>";
    $.each(acs, function(index, object){
      if(object.st_code == nom_st_code && object.election_id == nom_election_id){
        if(object.ac_no == nom_ac_no){
          html += "<option value='"+object.ac_no+"' selected='selected'>"+object.ac_name+"</option>";
        }else{
          html += "<option value='"+object.ac_no+"'>"+object.ac_name+"</option>";
        }
      }
    });
    $("#nom_ac_no").empty().append(html);
    if(nom_ac_no == ''){
      $("#nom_ac_no").val($("#nom_ac_no option:first").val());
    }
  }

  $(document).ready(function(e){
    filter_nom_respective_state("<?php echo $election_id ?>");
    filter_nom_respective_acs("<?php echo $st_code ?>");

    $('#new_nomination').click(function(){
      $.ajax({
        url: "{!! $action !!}",
        type: 'POST',
        data: $('#nomination_form').serialize(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#nomination_form .text-danger').remove();
          $('#nomination_form input').removeClass('input-error');
          $('#new_nomination').prop('disabled',true);
          $('#new_nomination').text("Validating...");
          $('#new_nomination').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          if(json['status'] == true){
            window.location.href = json.redirect;
          }
          if(json['status'] == false){
            if(json['errors']['warning']){
              alert(json['errors']['warning']);
            }
            $('.form-control').each(function(index, object){
              name = $(object).attr('name');
              if(json['errors'][name]){
                $("#nomination_form [name="+name+"]").addClass("input-error");
                $("#nomination_form [name="+name+"]").after("<span class='text-error text-danger text-right pull-right'>"+json['errors'][name][0]+"</span>");
              }
            });
          }
          $('#new_nomination').prop('disabled',false);
          $('#new_nomination').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#new_nomination').prop('disabled',false);
          $('#new_nomination').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
    });

  });
</script>