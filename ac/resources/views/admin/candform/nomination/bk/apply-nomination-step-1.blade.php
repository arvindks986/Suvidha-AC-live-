  @extends('admin.layouts.ac.theme')
@section('bradcome', 'New Nomination Nomination')
@section('content') 
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
  <?php   $url = URL::to("/"); $j=0; ?>
  <link href="{{ asset('theme/main.css') }}" rel="stylesheet">
   
  <main role="main" class="inner cover mb-3">
  <section class="bg-scroll">
    <div class="container">
      <div class="row">
          <div class="col pr-0 pl-0">
                 <ul class="steps mb-0" id="progressbar">
                  <li class="step active">Personal Details</li>
                  <li class="step">Election Details</li>
                  <li class="step">Part I/II</li>
                  <li class="step">Part III<span></span></li>
                  <li class="step">Part IIIA<span></span></li>
                 <!-- <li class="step">Upload Affidavit<span></span></li>
                     <li class="step">Finalize Application<span></span></li> -->
                </ul> 
                 
          </div>
        </div>
    </div>
</section>
 
 <section class="mt-1">
  <div class="container">
       
  <div class="row">
            
  <div class="card mt-3" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h3>Candidate Personal Details</h3> 
                 </div>  
                <div class="col">
                        <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st_name}} </span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
                        <span class="badge badge-info"> {{$ac_name}}</span>&nbsp;&nbsp;  </p>
                </div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
        @if (session('success'))
           <div class="alert alert-success"> {{session('success') }}</div>
        @endif
            @if(count($errors->all())>0)
               <div class="alert alert-danger">
                 @foreach($errors->all() as $iterate_error)
                 <p class="text-left">{!! $iterate_error !!}</p>
                 @endforeach
               </div>
               @endif 
         
    </div>
    </div>
   <div class="card-body">  

 <form enctype="multipart/form-data" id="election_form" method="POST"  action="{{$action}}" autocomplete='off' enctype="x-www-urlencoded">
{{ csrf_field() }}
<input type="hidden" name="nomination_id" value="{{$nomination_id}}">
   
  
 
<section>
  <div class="container p-0">
    <div class="row">

      <div class="col-md-12">
        <div class="card">
          
         <div class="card-body">
           <div class="row">
      <div class="col">
      <form class="form-inline pull-right">
      <div class="form-group row">
              <label class="col-sm-2">Epic No<sup>*</sup></label>
       <div class="col">
            <div class="input-group epic_no_div" id="epic_no_div">
              <input type="text" name="epic_no" maxlength="16" id="epic_no" class="form-control" value="{{$epic_no}}" placeholder="Epic no"/>
              <div class="input-group-append"><button class="btn btn-success" type="button" id="epic_no_search">Search</button></div>
            </div>
            @if ($errors->has('epic_no'))
               <span class="error">{{ $errors->first('epic_no') }}</span>
            @endif
      </div> 
        <label class="col-sm-2">Search Mobile No.<sup>*</sup></label>
       <div class="col">
            <div class="input-group epic_no_div" id="mobile_div">
              <input type="text" name="mobile" maxlength="10" id="mobile_no" class="form-control" value="{{$mobile}}" placeholder="Mobile Number"/>
              <div class="input-group-append"><button class="btn btn-success" type="button" id="mobile_search">Search</button></div>
            </div>
            @if ($errors->has('mobile'))
               <span class="error">{{ $errors->first('mobile') }}</span>
            @endif
      </div> 
   </div>
 </form>
    <div class="line"></div>


               <div class="form-group row">
                <label class="col-sm-3">Name<sup>*</sup></label>
                <div class="col">
                 <label>In English<sup>*</sup></label>

                 <input type="text" name="name" class="form-control" value="{{$name}}"> 

                 @if ($errors->has('name'))
                 <span class="error">{{ $errors->first('name') }}</span>
                 @endif 
               </div>  
               <div class="col">
                 <label>In Hindi<sup>*</sup></label>
                 <input type="text" name="name_hindi" class="form-control" value="{{$name_hindi}}"> 

                 @if ($errors->has('name_hindi'))
                 <span class="error">{{ $errors->first('name_hindi') }}</span>
                 @endif 
               </div>
               <div class="col">
                <label>In Vernacular </label>
                <input type="text" name="vernacular_name" class="form-control" value="{{$vernacular_name}}"> 
                @if ($errors->has('vernacular_name'))
                <span class="error">{{ $errors->first('vernacular_name') }}</span>
                @endif 
              </div>
            </div>

            <div class="line"></div>

            <div class="form-group row">
                <label class="col-sm-3">Father's / Husband's Name<sup>*</sup></label>
                <div class="col">
                 <label>in English<sup>*</sup></label>

                 <input type="text" name="father_name" class="form-control" value="{{$father_name}}"> 

                 @if ($errors->has('father_name'))
                 <span class="error">{{ $errors->first('father_name') }}</span>
                 @endif 
               </div>  
               <div class="col">
                 <label>in Hindi<sup>*</sup></label>
                 <input type="text" name="father_name_hindi" class="form-control" value="{{$father_name_hindi}}"> 

                 @if ($errors->has('father_name_hindi'))
                 <span class="error">{{ $errors->first('father_name_hindi') }}</span>
                 @endif 
               </div>
               <div class="col">
                <label>in Vernacular </label>
                <input type="text" name="father_name_vernacular" class="form-control" value="{{$father_name_vernacular}}"> 
                @if ($errors->has('father_name_vernacular'))
                <span class="error">{{ $errors->first('father_name_vernacular') }}</span>
                @endif 
              </div>
            </div>

            <div class="line"></div>

            <div class="form-group row">
             <label class="col-sm-3">Candidate Alias Name </label>
             <div class="col">
                 <label>in English </label>

                 <input type="text" name="alias_name" class="form-control" value="{{$alias_name}}" placeholder="Alias Name(English)"> 
              @if ($errors->has('alias_name'))
              <span class="error">{{ $errors->first('alias_name') }}</span>
              @endif 
               </div>  
               <div class="col">
                 <label>in Hindi</label>
                  <input type="text" name="alias_name_hindi" class="form-control" value="{{$alias_name_hindi}}" placeholder="Alias Name(Hindi)"> 
                    @if ($errors->has('alias_name_hindi'))
                    <span class="error">{{ $errors->first('alias_name_hindi') }}</span>
                    @endif 
               </div>
               <div class="col">
                <label>in Vernacular </label>
                <input type="text" name="alias_vname" class="form-control" value="{{$alias_vname}}"> 
                @if ($errors->has('alias_vname'))
                <span class="error">{{ $errors->first('alias_vname') }}</span>
                @endif 
              </div>
 
          </div>

          
        
       <div class="line"></div>

       <div class="form-group row">
         <label class="col-sm-2">Email <sup>*</sup></label>
         <div class="col">
           <input type="text" name="email" class="form-control" value="{{$email}}" placeholder="Email"> 
           @if ($errors->has('email'))
           <span class="error">{{ $errors->first('email') }}</span>
           @endif 
         </div>  
         <label class="col-sm-2">Mobile No <sup>*</sup></label>
         <div class="col">
           <input type="text" name="mobile" maxlength="10" class="form-control" value="{{$mobile}}" placeholder="Mobile"> 
           @if ($errors->has('mobile'))
           <span class="error">{{ $errors->first('mobile') }}</span>
           @endif 

           <div class="merrormsg errormsg errorred"></div> 
         </div>
       </div>


       <div class="form-group row">
         <label class="col-sm-2">Gender <sup>*</sup></label>

         <div class="col">
          <div class="custom-control custom-radio">
            @if("female" == $gender)
            <input type="radio" class="custom-control-input" id="customControlValidation2" name="gender" value="female" checked="checked">  
            @else
            <input type="radio" class="custom-control-input" id="customControlValidation2" name="gender" value="female">  
            @endif
            <label class="custom-control-label" for="customControlValidation2">Female</label>
         </div>
         <div class="custom-control custom-radio ">
           @if("male" == $gender)
            <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" checked="checked">  
            @else
            <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male">  
            @endif
            <label class="custom-control-label" for="customControlValidation3">Male</label>
         </div>
         <div class="custom-control custom-radio mb-3">
            @if("third" == $gender)
            <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" checked="checked">  
            @else
            <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third">  
            @endif
            <label class="custom-control-label" for="customControlValidation4">Others</label>
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
     <div class="line"></div>
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
       <label class="col-sm-2">Serial No<sup>*</sup></label>
       <div class="col">
         <input type="text" name="serial_no" class="form-control" value="{{$serial_no}}" placeholder="Serial no"> 
         @if ($errors->has('serial_no'))
         <span class="error">{{ $errors->first('serial_no') }}</span>
         @endif
       </div> 
       <label class="col-sm-2">Part No<sup>*</sup></label> 
       <div class="col">
         <input type="text" name="part_no" class="form-control" value="{{$part_no}}" placeholder="Part no"> 
         @if ($errors->has('part_no'))
         <span class="error">{{ $errors->first('part_no') }}</span>
         @endif
       </div>  
     </div>


     <div class="line"></div>    

     <div class="form-group row">
       <label class="col-sm-3">Address<sup>*</sup></label>
       <div class="col">
        <label>in English<sup>*</sup></label>
         <input type="text" name="address_1" class="form-control" value="{{$address_1}}" placeholder="Address Line 1(English)"> 
         @if ($errors->has('address_1'))
         <span class="error">{{ $errors->first('address_1') }}</span>
         @endif


       </div>  
       <div class="col">
        <label>in Hindi<sup>*</sup></label>
         <input type="text" name="address_1_hindi" class="form-control" value="{{$address_1_hindi}}" placeholder="Address Line 1(Hindi)"> 
         @if ($errors->has('address_1_hindi'))
         <span class="error">{{ $errors->first('address_1_hindi') }}</span>
         @endif
       </div> 


       <div class="col">
        <label>in Vernacular<sup>*</sup></label>
         <input type="text" name="address_1_vernacular" class="form-control" value="{{$address_1_vernacular}}" placeholder="Address Line 1(Hindi)"> 
         @if ($errors->has('address_1_vernacular'))
         <span class="error">{{ $errors->first('address_1_vernacular') }}</span>
         @endif
       </div> 

     </div>



     <div class="line"></div>

     <div class="form-group row">
       <div class="col-sm-2"><label for="statename">State Name <sup>*</sup></label></div>
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
       <div class="col-sm-2"><label for="statename">District <sup>*</sup></label></div>
       <div class="col"><div class="" style="width:100%;">
         <select name="district" class="form-control" id="district" onchange="filter_respective_acs(this.value)">
           <option value="">-- Select Ditricts --</option>     
         </select>
         @if ($errors->has('district'))
         <span class="error">{{ $errors->first('district') }}</span>
         @endif 
       </div>
     </div> 
   </div> 
   <div class="form-group row">


    <div class="col-sm-2"><label for="statename">AC <sup>*</sup></label></div>
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
   <div class="col-sm-2"> </div> 
   <div class="col"> </div>

 </div> 


</div>
</div>
</div>

<div class="card-footer">
	 <div class="form-group row float-right">       
  <div class="col">
    <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button>
   <button type="submit" id="candnomination" class="btn btn-primary">Save & Next</button>
 </div>
</div>
</div>

</div>
</div>
</div>
</div>    
</section>

</form>
</div></div>
</div>
</div>
</section>

</main>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>

<script>

 function readURL(input) {
   if (input.files && input.files[0]) {
     var reader = new FileReader();
     reader.onload = function(e) {
      $('#imagePreview').css('background-image', 'url('+e.target.result +')');
      $('#imagePreview').hide();
      $('#imagePreview').fadeIn(650);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#imageUpload").change(function() {
 readURL(this);
});
$(document).ready(function(){  
  $('#dob').datepicker({ 
    dateFormat: 'yy-mm-dd',
    yearRange: '1910:<?php echo date('Y')-18; ?>',
    changeMonth: true,
    changeYear: true
  });

/* if($('#breadcrumb').length){
   var breadcrumb = '';
   $.each({!! json_encode($breadcrumbs) !!},function(index, object){
    breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
  });
   $('#breadcrumb').html(breadcrumb);
 }*/
});

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

$(document).ready(function(e){

  $('#epic_no_search').click(function(){
      $.ajax({
        url: "{!! url('search-by-epic-cdac') !!}",
        type: 'GET',
        data: 'epic_no='+$('#epic_no').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
          $('#epic_no_search').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search').prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search').prop('disabled', false);
        },        
        success: function(json) {   
          if(json['success'] == false){
            $('#epic_no').parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
          }else{
            $(".main_div").removeClass("display_none");
            if(json['basic'].name != '' && json['basic'].name != null){
              $("input[name=name]").val(json['basic'].name);
            }
            if(json['basic'].rln_name != '' && json['basic'].rln_name != null){
              $("input[name=father_name]").val(json['basic'].rln_name);
            }
            if(json['basic'].age != '' && json['basic'].age != null){
              $("input[name=age]").val(json['basic'].age);
            }
            if(json['address'].MOBILE_NO != '' && json['address'].MOBILE_NO != null){
              $("input[name=mobile]").val(json['address'].MOBILE_NO);
            }
            if(json['basic'].part_no != '' && json['basic'].part_no != null){
              $("input[name=part_no]").val(json['basic'].part_no);
            }
            if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
              $("input[name=serial_no]").val(json['basic'].slno_inpart);
            }
            if(json['address'].Address != '' && json['address'].Address != null){
              $("input[name=address]").val(json['address'].Address);
            }
            if(json['basic'].gender != '' && json['basic'].gender != null){
              if(json['basic'].gender=='M'){
                gender = 'male';
              }else if(json['basic'].gender=='F'){
                gender = 'female';
              }else{
                gender = 'third';
              }
              $("input[name=gender][value=" + gender + "]").prop('checked', true);
            }


            // if(json['address'].C_VILLAGE != '' && json['address'].C_VILLAGE != null){
            //   $("#village").val(json['address'].C_VILLAGE);
            //   $("#village").prop('readonly',true);
            // }
            // if(json['address'].C_STREET_AREA != '' && json['address'].C_STREET_AREA != null){
            //   $("#tehsil").val(json['address'].C_STREET_AREA);
            //   $("#tehsil").prop('readonly',true);
            // }

            if(json['basic'].st_code != '' && json['basic'].st_code != null){
              filter_respective_district(json['basic'].st_code);
              $('#state').val(json['basic'].st_code);
            }  

            if(json['basic'].dist_no != '' && json['basic'].dist_no != null){
              filter_respective_acs(json['basic'].dist_no);
              $('#district').val(json['basic'].dist_no);
            }

            if(json['basic'].ac_no != '' && json['basic'].ac_no != null){
              $('#ac').val(json['basic'].ac_no);
            }

          }  
          $('.loading_spinner').remove();    
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      });
    });

  $('#mobile_search').click(function(){
          var mobile_no =  $('#mobile_no').val();
           //alert(mobile_no);
            jQuery.ajax({
                    url: "{{url('/roac/nomination/apply-nomination-step-1')}}",
                    type: 'GET',
                    data: {mobile:mobile_no},
                    success: function(result){

                    location.reload();

              }
            });
    });
});

</script>

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

@endsection