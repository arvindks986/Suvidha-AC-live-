<!-- nomination popup start -->
<input type="hidden" id="nomination_id" name="nomination_id" value="0">
<div class="modal fade animated zoomIn" id="nomination_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content" id="nomination_modal_body">

      <div class="nomination_form_div" id="nomination_form_div">
      </div>

      <div class="candidate_form_div" id="candidate_form_div">
      <form class="form-horizontal" method="post" action="{!! $action !!}" id="candidate_form">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        <div class="modal-header">
          <h4 class="modal-title">Candidate Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body">
          <div class="row">
      <div class="col">


        <div class="form-group row">
          <label class="col-sm-3">Name<sup>*</sup></label>
          <div class="col">
            <label>Name(in English)<sup>*</sup></label>

            <input type="text" name="name" class="form-control" value="{{$name}}"> 

            @if ($errors->has('name'))
            <span class="error">{{ $errors->first('name') }}</span>
            @endif 
          </div>  
          <div class="col">
            <label>Name(in Hindi)<sup>*</sup></label>
            <input type="text" name="name_hindi" class="form-control" value="{{$name_hindi}}"> 

            @if ($errors->has('name_hindi'))
            <span class="error">{{ $errors->first('name_hindi') }}</span>
            @endif 
          </div>
          <div class="col">
            <label>Name in Vernacular </label>
            <input type="text" name="vernacular_name" class="form-control" value="{{$vernacular_name}}"> 
            @if ($errors->has('vernacular_name'))
            <span class="error">{{ $errors->first('vernacular_name') }}</span>
            @endif 
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">Candidate Alias Name </label>
          <div class="col">
            <input type="text" name="alias_name" class="form-control" value="{{$alias_name}}" placeholder="Alias Name(English)"> 
            @if ($errors->has('alias_name'))
            <span class="error">{{ $errors->first('alias_name') }}</span>
            @endif 

          </div>  
          <div class="col">
            <input type="text" name="alias_name_hindi" class="form-control" value="{{$alias_name_hindi}}" placeholder="Alias Name(Hindi)"> 
            @if ($errors->has('alias_name_hindi'))
            <span class="error">{{ $errors->first('alias_name_hindi') }}</span>
            @endif 

          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3">Father's / Husband's Name <sup>*</sup></label>
          <div class="col">

            <input type="text" name="father_name" class="form-control" value="{{$father_name}}" placeholder="In English"> 
            @if ($errors->has('father_name'))
            <span class="error">{{ $errors->first('father_name') }}</span>
            @endif 


          </div>  
          <div class="col">

            <input type="text" name="father_name_hindi" class="form-control" value="{{$father_name_hindi}}" placeholder="In Hindi"> 
            @if ($errors->has('father_name_hindi'))
            <span class="error">{{ $errors->first('father_name_hindi') }}</span>
            @endif 

          </div>

          <div class="col">

            <input type="text" name="father_name_vernacular" class="form-control" value="{{$father_name_vernacular}}" placeholder="In Varnacular"> 
            @if ($errors->has('father_name_vernacular'))
            <span class="error">{{ $errors->first('father_name_vernacular') }}</span>
            @endif 

          </div>

        </div>

        <div class="line"></div>

        <div class="form-group row">
          <label class="col-sm-2">Email  </label>
          <div class="col">
            <input type="text" name="email" class="form-control" value="{{$email}}" placeholder="Email"> 
            @if ($errors->has('email'))
            <span class="error">{{ $errors->first('email') }}</span>
            @endif 
          </div>  
          <label class="col-sm-2">Mobile No  </label>
          <div class="col">
            <input type="text" name="mobile" class="form-control" value="{{$mobile}}" placeholder="Mobile" maxlength="10"> 
            @if ($errors->has('mobile'))
            <span class="error">{{ $errors->first('mobile') }}</span>
            @endif 

            <div class="merrormsg errormsg errorred"></div> 
          </div>
        </div>


        <div class="form-group row">
          <label class="col-sm-2">Gender <sup>*</sup></label>

          <div class="col">
            <div class="row">
            <div class="col">
            <div class="custom-control custom-radio">
              <input type="radio" name="gender" class="custom-control-input" id="customControlValidation2" value="female" 
              @if("female"== $gender) checked="checked" @endif>
              <label class="custom-control-label" for="customControlValidation2">Female</label>
            </div>
          </div>
          <div class="col">
            <div class="custom-control custom-radio ">
              <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" id="radio2"@if("male" == $gender)) checked="checked" @endif> 
              <label class="custom-control-label" for="customControlValidation3">Male</label>

            </div>
          </div>
          <div class="col">
            <div class="custom-control custom-radio mb-3">
              <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" @if("third" == $gender) checked="checked" @endif>  
              <label class="custom-control-label" for="customControlValidation4">Others</label>
            </div>
          </div>
          </div>
            @if ($errors->has('gender'))
            <span class="error">{{ $errors->first('gender') }}</span>
            @endif 
          </div> 

          <label class="col-sm-2">PAN Number  </label>
          <div class="col">
            <input type="text" name="pan_number" class="form-control" value="{{$pan_number}}" placeholder="PAN No." maxlength="10"> 
            @if ($errors->has('pan_number'))
            <span class="error">{{ $errors->first('pan_number') }}</span>
            @endif
          </div>
        </div>
        <div class="form-group row">

          <label class="col-sm-2">Age <sup>*</sup></label>
         <div class="col">
           <input type="text" name="age" class="form-control" value="{{$age}}" placeholder="Age"> 
           @if ($errors->has('age'))
           <span class="error">{{ $errors->first('age') }}</span>
           @endif
         </div>

          <label class="col-sm-2">Category <sup>*</sup></label> 
          <div class="col"> 
            <select name="category" class="form-control">
              <option value="">--Select Category--</option>
              @foreach($categories as $iterate_category)
              @if($category == $iterate_category['id'])
              <option value="{{$iterate_category['id']}}" selected="selected">{{$iterate_category['name']}}</option>
              @else
              <option value="{{$iterate_category['id']}}">{{$iterate_category['name']}}</option>
              @endif
              @endforeach
            </select>
            @if ($errors->has('category'))
            <span class="error">{{ $errors->first('category') }}</span>
            @endif

          </div>
        </div> 
        <div class="line"></div>    

        <div class="form-group row">
          <label class="col-sm-2">Address Line<sup>*</sup></label>
          <div class="col">
            <input type="text" name="address_1" class="form-control" value="{{$address_1}}" placeholder="Address Line 1(English)"> 
            @if ($errors->has('address_1'))
            <span class="error">{{ $errors->first('address_1') }}</span>
            @endif


          </div>  
          <div class="col">
            <input type="text" name="address_1_hindi" class="form-control" value="{{$address_1_hindi}}" placeholder="Address Line(Hindi)"> 
            @if ($errors->has('address_1_hindi'))
            <span class="error">{{ $errors->first('address_1_hindi') }}</span>
            @endif
          </div>  
          <div class="col">
            <input type="text" name="address_1_vernacular" class="form-control" value="{{$address_1_vernacular}}" placeholder="Address Line(Vernacular)"> 
            @if ($errors->has('address_1_vernacular'))
            <span class="error">{{ $errors->first('address_1_vernacular') }}</span>
            @endif
          </div> 
        </div>


        <div class="line"></div>

        <div class="form-group row">
          <label class="col-sm-1">Epic No<sup>*</sup></label>
          <div class="col">
            <input type="text" name="epic_no" class="form-control" value="{{$epic_no}}" placeholder="Epic no"> 
            @if ($errors->has('epic_no'))
            <span class="error">{{ $errors->first('epic_no') }}</span>
            @endif


          </div> 
          <label class="col-sm-1">Part No<sup>*</sup></label> 
          <div class="col">
            <input type="text" name="part_no" class="form-control" value="{{$part_no}}" placeholder="Part no"> 
            @if ($errors->has('part_no'))
            <span class="error">{{ $errors->first('part_no') }}</span>
            @endif
          </div>  

          <label class="col-sm-1">Serial No<sup>*</sup></label>
          <div class="col">
            <input type="text" name="serial_no" class="form-control" value="{{$serial_no}}" placeholder="Serial no"> 
            @if ($errors->has('serial_no'))
            <span class="error">{{ $errors->first('serial_no') }}</span>
            @endif
          </div> 

        </div>



        <div class="line"></div>



        <div class="form-group row">
          <div class="col-sm-1"><label for="statename">State Name <sup>*</sup></label></div>
          <div class="col">
            <div class="" style="width:100%;">
              <select name="state" class="form-control" id="state" onchange="filter_respective_district(this.value)">
                <option value="">-- Select States --</option>
                @foreach($states as $iterate_state)
                @if($state == $iterate_state['st_code'])
                <option value="{{ $iterate_state['st_code'] }}" selected="selected">{{ $iterate_state['st_name'] }}</option>
                @else 
                <option value="{{ $iterate_state['st_code'] }}"> {{ $iterate_state['st_name'] }}</option>
                @endif
                @endforeach
              </select>
              @if ($errors->has('state'))
              <span class="error">{{ $errors->first('state') }}</span>
              @endif 
            </div>
          </div>  
          <div class="col-sm-1"><label for="statename">District <sup>*</sup></label></div>
          <div class="col"><div class="" style="width:100%;">
            <select name="district" class="form-control" id="district" onchange="filter_respective_acs(this.value)">
              <option value="">-- Select Ditricts --</option>     
            </select>
            @if ($errors->has('district'))
            <span class="error">{{ $errors->first('district') }}</span>
            @endif 
          </div>
        </div> 

        <div class="col-sm-1"><label for="statename">AC <sup>*</sup></label></div>
        <div class="col">
          <div class="" style="width:100%;">
            <select name="ac" class="consttype form-control" id="ac">
              <option value="">-- Select AC --</option>
            </select>
            @if ($errors->has('ac'))
            <span class="error">{{ $errors->first('ac') }}</span>
            @endif

          </div>
        </div>
      </div> 



    </div>
</div>
        </div>
        <div class="modal-footer">
          <div class="form-group mb-1">
            <label class="col-md-3 pull-left" style="visibility: hidden;"></label>
            <button type="button" id="new_candidate" class="btn btn-large btn-primary">Next</button>
          </div>
        </div>
      </form>
    </div>
    </div>
  </div>
</div>

  <script>

    function load_form(class_name){
      $('#nomination_id').val($('.'+class_name).find('.encrypt_id').val());
      show_candidate_modal();
    }

    function show_candidate_modal(){
      $('#nomination_form_div').addClass("display_none");
      $('#candidate_form_div').removeClass("display_none");
      $('#nomination_modal .text-danger').remove();
      $('#nomination_modal').modal('show');
    }


    $(document).ready(function(){  
      $('#add_new_candidate').click(function(e){
        show_candidate_modal();
      });

    $('#new_candidate').click(function(){
      $.ajax({
        url: "{!! $action !!}",
        type: 'POST',
        data: $('#candidate_form').serialize(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#nomination_modal .text-danger').remove();
          $('#nomination_modal input').removeClass('input-error');
          $('#candidate_form ').prop('disabled',true);
          $('#candidate_form #new_candidate').text("Validating...");
          $('#candidate_form #new_candidate').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          if(json['status'] == true){
            $('#nomination_form_div').removeClass("display_none");
            $('#candidate_form_div').addClass("display_none");
            load_step_2();
          }
          if(json['status'] == false){
            if(json['errors']['warning']){
              alert(json['errors']['warning']);
            }
            $('.form-control').each(function(index, object){
              name = $(object).attr('name');
              if(json['errors'][name]){
                $("#candidate_form [name="+name+"]").addClass("input-error");
                $("#candidate_form [name="+name+"]").after("<span class='text-error text-danger text-right pull-right'>"+json['errors'][name][0]+"</span>");
              }
            });
          }
          $('#candidate_form #new_candidate').prop('disabled',false);
          $('#candidate_form #new_candidate').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#candidate_form #new_candidate').prop('disabled',false);
          $('#candidate_form #new_candidate').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
    });




    });



function load_step_2(){
   $.ajax({
    url: "{!! $nomination_page !!}/"+$('#nomination_id').val(),
    type: 'GET',
    dataType: 'html', 
    beforeSend: function() {
    },
    success: function(json) {
      $('#nomination_form_div').html(json);
    },
    error: function(data) {
      var errors = data.responseJSON;
    }
  });
}

function filter_respective_district(id){
  html = '';
  html += "<option value=''>Select</option>";
  var districts = <?php echo json_encode($districts); ?>;
  var district = "<?php echo $district ?>";
  $.each(districts, function(index, object){
    if(object.st_code == id){
      if(object.district_no == district){
        html += "<option value='"+object.district_no+"' selected='selected'>"+object.district_name+"</option>";
      }else{
        html += "<option value='"+object.district_no+"'>"+object.district_name+"</option>";
      }
    }
  });
  $("#district").empty().append(html);
  if(district==''){
    $("#district").val($("#district option:first").val());
  }
}

function filter_respective_acs(id){
  html = '';
  html += "<option value=''>Select</option>";
  var acs = <?php echo json_encode($acs); ?>;
  var ac = "<?php echo $ac ?>";
  var district = $('#district').val();
  var state = $('#state').val();
  $.each(acs, function(index, object){
    if(object.st_code == state && object.district_no == district){
      if(object.ac_no == ac){
        html += "<option value='"+object.ac_no+"' selected='selected'>"+object.ac_name+"</option>";
      }else{
        html += "<option value='"+object.ac_no+"'>"+object.ac_name+"</option>";
      }
    }
  });
  $("#ac").empty().append(html);
  if(ac == ''){
    $("#ac").val($("#ac option:first").val());
  }
}

$(document).ready(function(e){
  filter_respective_district("<?php echo $state ?>");
  filter_respective_acs("<?php echo $district ?>");
});



  </script>