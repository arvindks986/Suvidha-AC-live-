@extends('admin.layouts.ac.theme')
  @section('content')
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
  <link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
   <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
  <main role="main" class="inner cover mb-3">
   
 <form enctype="multipart/form-data" id="election_form" method="POST"  action="{{$action}}" autocomplete='off' enctype="x-www-urlencoded">

   <section>

    

      {{ csrf_field() }}

      <div class="container">
        <div class="row">

         <div class="text-left mt-3" style="width:100%; margin:0 auto 10px auto;">


         @if (session('flash-message'))
         <div class="alert alert-success"> {{session('flash-message') }}</div>
         @endif

      
</div>
</div>    
</section>
<section>
  <div class="container p-0">
    <div class="row">

      <div class="col-md-12">
        <div class="card">
         <div class="card-header d-flex align-items-center">
           <h4>Candidate Personal Details</h4>
         </div>
         <div class="card-body">
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
                 <input type="text" name="hname" class="form-control" value="{{$hname}}"> 

                 @if ($errors->has('hname'))
                 <span class="error">{{ $errors->first('hname') }}</span>
                 @endif 
               </div>
               <div class="col">
                <label>Name in Vernacular </label>
                <input type="text" name="vname" class="form-control" value="{{$vname}}"> 
                @if ($errors->has('vname'))
                <span class="error">{{ $errors->first('vname') }}</span>
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
             
             <input type="text" name="alias_hname" class="form-control" value="{{$alias_hname}}" placeholder="Alias Name(Hindi)"> 
            @if ($errors->has('alias_hname'))
            <span class="error">{{ $errors->first('alias_hname') }}</span>
            @endif 
            
            
          </div>  
          <div class="col">
            
           <input type="text" name="alias_vname" class="form-control" value="{{$alias_vname}}" placeholder="Alias Name(Vernacular)"> > 

            @if ($errors->has('alias_vname'))
            <span class="error">{{ $errors->first('alias_vname') }}</span>
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

            <input type="text" name="father_hname" class="form-control" value="{{$father_hname}}" placeholder="In Hindi"> 
            @if ($errors->has('father_hname'))
            <span class="error">{{ $errors->first('father_hname') }}</span>
            @endif 

          </div>
        </div>
        <div class="form-group row">
         <label class="col-sm-3">Category <sup>*</sup></label> 
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
         <div class="col"> 

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
           <input type="text" name="mobile" class="form-control" value="{{$mobile}}" placeholder="Mobile" readonly="readonly"> 
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
           <input type="radio" name="gender" class="custom-control-input" id="customControlValidation2" value="female" 
           @if("female"== $gender) checked="checked" @endif>
           <label class="custom-control-label" for="customControlValidation2">Female</label>
         </div>
         <div class="custom-control custom-radio ">
           <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" id="radio2"@if("male" == $gender)) checked="checked" @endif> 
           <label class="custom-control-label" for="customControlValidation3">Male</label>

         </div><div class="custom-control custom-radio mb-3">
           <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" @if("third" == $gender) checked="checked" @endif>  
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
     <div class="form-group row">

       <label class="col-sm-2">Age <sup>*</sup></label>
       <div class="col">
         <input type="text" name="dob" id="dob" class="form-control" value="{{$dob}}" placeholder="D.O.B" readonly="readonly"> 
         @if ($errors->has('dob'))
         <span class="error">{{ $errors->first('dob') }}</span>
         @endif
       </div>
       <div class="col">
         &nbsp;
       </div>
     </div> 
     <div class="line"></div>    

     <div class="form-group row">
       <label class="col-sm-2">Address Line1<sup>*</sup></label>
       <div class="col">
         <input type="text" name="address" class="form-control" value="{{$address}}" placeholder="Address Line 1(English)"> 
         @if ($errors->has('address'))
         <span class="error">{{ $errors->first('address') }}</span>
         @endif


       </div>  
       <div class="col">
         <input type="text" name="vaddress" class="form-control" value="{{$vaddress}}" placeholder="Address Line 1(Hindi)"> 
         @if ($errors->has('vaddress'))
         <span class="error">{{ $errors->first('vaddress') }}</span>
         @endif
       </div>  
     </div>

     <div class="line"></div>

     <div class="form-group row">
       <label class="col-sm-2">Address Line 2<sup>*</sup></label>
       <div class="col">
         <input type="text" name="address_2" class="form-control" value="{{$address_2}}" placeholder="Address Line 2(English)"> 
         @if ($errors->has('address_2'))
         <span class="error">{{ $errors->first('address_2') }}</span>
         @endif


       </div>  
       <div class="col">
         <input type="text" name="address_2_hindi" class="form-control" value="{{$address_2_hindi}}" placeholder="Address Line 1(Hindi)"> 
         @if ($errors->has('address_2_hindi'))
         <span class="error">{{ $errors->first('address_2_hindi') }}</span>
         @endif
       </div>  
     </div>
     <div class="line"></div>

     <div class="form-group row">
       <label class="col-sm-2">Epic No<sup>*</sup></label>
       <div class="col">
         <input type="text" name="epic_no" class="form-control" value="{{$epic_no}}" placeholder="Epic no"> 
         @if ($errors->has('epic_no'))
         <span class="error">{{ $errors->first('epic_no') }}</span>
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
       <label class="col-sm-2">Serial No<sup>*</sup></label>
       <div class="col">
         <input type="text" name="serial_no" class="form-control" value="{{$serial_no}}" placeholder="Serial no"> 
         @if ($errors->has('serial_no'))
         <span class="error">{{ $errors->first('serial_no') }}</span>
         @endif
       </div>  
       <div class="col">
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
   <button type="submit" id="candnomination" class="btn btn-primary">Next</button>
 </div>
</div>
</div>

</div>
</div>
</div>
</div>    
</section>
</form>
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

 if($('#breadcrumb').length){
   var breadcrumb = '';
   $.each({!! json_encode($breadcrumbs) !!},function(index, object){
    breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
  });
   $('#breadcrumb').html(breadcrumb);
 }
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


</script>
@endsection