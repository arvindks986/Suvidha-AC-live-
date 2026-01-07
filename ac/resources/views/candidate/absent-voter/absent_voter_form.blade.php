@extends('candidate.common.app')
  @section('seo')
    <title>Absentee Voter Form - 12D</title>
  @endsection
  @section('style')
  <style type="text/css">
  #breadcrumb li, #breadcrumb li a { color: #e65482;  margin-bottom: 10px;}
    .display_none{ display: none; }
<!-- 	.body{    margin: 50px auto 0; padding: 40px;   box-shadow: 0px 0px 8px 1px #d5d5d5;} -->
  .form2d span {  display: inline-block;   min-width: 200px;}
  .form2d span input { border: none;  width: 100%;   border-bottom: 1px dashed #000; font-weight:600;}
  .form2d span input[type="text"]:focus { border-bottom: 1px dashed red;  outline: none;}
  td{padding:5px;}
  .form2d{    margin: 50px auto 0 auto;   padding: 20px;    box-shadow: 0 0 19px #d5d5d5;}
  .readonly{background:#fff;}
  
   @media only screen and (max-width:600px){
	  .form2d span{display:block;}
	  .form2d{padding:10px; margin:0; box-shadow:none;}
	
.statistics div[class*=col-] .card {
    padding:0px;
}
.p-4{padding:5px;}
   }
  </style>
  <style type="text/css">

</style>
  @endsection
@section('content')
{!!$header!!}
@include('candidate.common.breadcrumb')
<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">


     <div class="col  pull-right  text-right">
   
   @if(isset($filter_buttons) && count($filter_buttons)>0)

        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach 
    
      @if(count($buttons)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif  


   
@endif    
    </div>
  </div>
</div>  

 <form class="container-fluid mb-5" enctype="multipart/form-data" id="form12d_form" method="POST"  action="{{ $action }}" autocomplete='off' enctype="x-www-urlencoded">
 <input type="hidden" value="{!! csrf_token() !!}" name="_token"/>
 <div class="row">

      <div class="col-md-12">
        <div class="card">
         <div class="card-header d-flex align-items-center">
           <h4>{{$heading_title}}</h4>
         </div>
         <div class="card-body">
         


     



<div class="form2d container">
  <div class="form-group row">
       <label class="col-sm-2">Epic No<sup>*</sup></label>
       <div class="col">
      <div class="input-group epic_no_div" id="epic_no_div">
        <input type="text" name="epic_no" id="epic_no" class="form-control" value="{{$epic_no}}" placeholder="Epic no"/>
        <div class="input-group-append"><button class="btn btn-success" type="button" id="epic_no_search">Search</button></div>
      </div>
        </div> 
     </div>
     <hr>


<div class="main_div display_none">

 <div class="form-group row ">

        <label class="col-sm-2">Name<sup>*</sup></label>
                <div class="col">
 
                  
                 <input type="text" name="name" id="name" class="form-control readonly" value="{{$name}}"> 

                 @if ($errors->has('name'))
                 <span class="error">{{ $errors->first('name') }}</span>
                 @endif 
               </div> 
     
                
               <label class="col-sm-2">Father's / Husband's Name <sup>*</sup></label>
               <div class="col">
            
             <input type="text" name="father_name" id="father_name" class="form-control readonly" value="{{$father_name}}" placeholder=""> 
             @if ($errors->has('father_name'))
             <span class="error">{{ $errors->first('father_name') }}</span>
             @endif 


           </div> 

            </div>


      


     <div class="line"></div>  
<fieldset class="p-4">
<legend>Address in Electoral Roll</legend>
 <div class="form-group row">
  <label class="col-sm-2">House No.<sup>*</sup></label>
       <div class="col">
         <input type="text" name="house_no" id="house_no" class="form-control readonly">
       </div> 

       <label class="col-sm-2">Address<sup>*</sup></label>
       <div class="col">
         <textarea type="text" name="address" id="address" class="form-control readonly " value="{{$address}}" placeholder="Address"> </textarea>
         @if ($errors->has('address'))
         <span class="error">{{ $errors->first('address') }}</span>
         @endif


       </div>  
 
     </div>
 <div class="line"></div>    

     <div class="form-group row">
      
       <label class="col-sm-2">Village/Mohalla<sup>*</sup></label>
       <div class="col">
         <input type="text" name="village" id="village" class="form-control readonly ">
         @if ($errors->has('village'))
         <span class="error">{{ $errors->first('village') }}</span>
         @endif
       </div>  


    

    
       <label class="col-sm-2">Town/city/tehsil<sup>*</sup></label>
       <div class="col">
         <input type="text" name="tehsil" id="tehsil" class="form-control readonly ">
         @if ($errors->has('tehsil'))
         <span class="error">{{ $errors->first('tehsil') }}</span>
         @endif
       </div>  
    
    </div>

     <div class="line"></div>    

     <div class="form-group row">
       <label class="col-sm-2">Pincode<sup>*</sup></label>
       <div class="col">
         <input type="text" name="pincode" id="pincode" class="form-control readonly ">
         @if ($errors->has('pincode'))
         <span class="error">{{ $errors->first('pincode') }}</span>
         @endif
       </div>  

       <label class="col-sm-2">District<sup>*</sup></label>
       <div class="col">
         <input type="text" name="district" id="district" class="form-control readonly ">
         @if ($errors->has('district'))
         <span class="error">{{ $errors->first('district') }}</span>
         @endif
       </div>  
     </div>

     <div class="line"></div>    

     <div class="form-group row">
       <label class="col-sm-2">State<sup>*</sup></label>
       <div class="col">
         <input type="text" name="st_name" id="st_name" class="form-control readonly ">
       </div>  
       <label class="col-sm-2"></label>
       <div class="col">
      </div>
     </div>



</fieldset>  

    



    

     <div class="line"></div> 

  <div class="form-group row">
         <label class="col">My complete present postal address is same as above<sup>*</sup></label>
         <div class="col">
     <div class="row">
         <div class="col-md-2">
         <div class="custom-control custom-radio ">
           <input type="radio" class="custom-control-input" name="same_address" id="same_address_yes" value="1" @if($same_address == 1) checked="checked" @endif> 
           <label class="custom-control-label" for="same_address_yes">Yes</label>

         </div>
        </div>
       

        <div class="col-md-2">
         <div class="custom-control custom-radio ">
           <input type="radio" class="custom-control-input" id="same_address_no" name="same_address" value="0" @if($same_address == 0) checked="checked" @endif> 
           <label class="custom-control-label" for="same_address_no">No</label>

         </div>
        </div>

      </div>
      </div>
    </div>



       <div class="same_address_no display_none">



   <fieldset class="p-4">
  <legend>Complete Postal Address</legend>
    
    <div class="form-group row">
      <label class="col-sm-2">House No.<sup>*</sup></label>
       <div class="col">
         <input type="text" name="new_house_no" id="new_house_no" class="form-control readonly">
       </div> 
       <label class="col-sm-2">Address<sup>*</sup></label>
       <div class="col">
         <textarea type="text" name="new_address" id="new_address" class="form-control readonly" placeholder="Address"> </textarea>
       </div>   
     </div>
 <div class="line"></div>  

     <div class="form-group row">
       <label class="col-sm-2">Village/Mohalla<sup>*</sup></label>
       <div class="col">
         <input type="text" name="new_village" id="new_village" class="form-control readonly ">
         @if ($errors->has('village'))
         <span class="error">{{ $errors->first('village') }}</span>
         @endif
       </div>  

       <label class="col-sm-2">Town/city/tehsil<sup>*</sup></label>
       <div class="col">
         <input type="text" name="new_tehsil" id="new_tehsil" class="form-control readonly ">
         @if ($errors->has('tehsil'))
         <span class="error">{{ $errors->first('tehsil') }}</span>
         @endif
       </div>  
     </div>

     <div class="line"></div>    

     <div class="form-group row">
       <label class="col-sm-2">Pincode<sup>*</sup></label>
       <div class="col">
         <input type="text" name="new_pincode" id="new_pincode" class="form-control readonly ">
         @if ($errors->has('pincode'))
         <span class="error">{{ $errors->first('pincode') }}</span>
         @endif
       </div>  
       <label class="col-sm-2"></label>
       <div class="col">
       </div>
     </div>

     <div class="line"></div>    

     <div class="form-group row">
       <div class="col-sm-2"><label for="statename">State Name <sup>*</sup></label></div>
       <div class="col">
         <div class="" style="width:100%;">
           <select name="new_st_code" class="form-control" id="new_st_code" onchange="filter_respective_district(this.value)">
             <option value="">Select State</option>
             @foreach($states as $iterate_state)
              <option value="{{ $iterate_state['st_code'] }}"> {{ $iterate_state['st_name'] }}</option>
             @endforeach
           </select>
   
         </div>
       </div>  
       <div class="col-sm-2"><label for="statename">District <sup>*</sup></label></div>
       <div class="col"><div class="" style="width:100%;">
         <select name="new_dist_no" class="form-control" id="new_district">
           <option value="">Select Ditrict</option>     
         </select>

       </div>
     </div> 
   </div> 





 </fieldset> 
 </div> 








    

   

     <div class="line"></div>    
<fieldset class="p-4 mb-3">
  <legend>My Name is Enrolled at</legend>
     <div class="form-group row">
       <label class="col-sm-2">Serial Number<sup>*</sup></label>
       <div class="col">
         <input type="text" name="serial_number" id="serial_number" class="form-control readonly ">
         @if ($errors->has('serial_number'))
         <span class="error">{{ $errors->first('serial_number') }}</span>
         @endif
       </div>  

       
       <label class="col-sm-2">Part No.<sup>*</sup></label> 
       <div class="col">
         <input type="text" name="part_no" id="part_no" class="form-control readonly" value="" placeholder=""> 
         @if ($errors->has('part_no'))
         <span class="error">{{ $errors->first('part_no') }}</span>
         @endif
       </div>  
     </div>


     
     <div class="line"></div>


     <div class="form-group row">
       
       <label class="col-sm-2">AC/PC.<sup>*</sup></label> 
       <div class="col">
         <input type="text" name="ac_name" id="ac_name" class="form-control readonly" value="" placeholder=""> 
       </div> 
       <label class="col-sm-2"></label> 
       <div class="col">
       </div> 
     </div>
</fieldset>
 <div class="line"></div>

       <div class="form-group row">
 
         <label class="col-sm-2">Mobile No  </label>
         <div class="col">
           <input type="text" name="mobile" id="mobile" class="form-control readonly" value="{{$mobile}}" placeholder=""> 
           @if ($errors->has('mobile'))
           <span class="error">{{ $errors->first('mobile') }}</span>
           @endif 

           <div class="merrormsg errormsg errorred"></div> 
         </div>
 

       <label class="col-sm-2">Age <sup>*</sup></label>
       <div class="col">
         <input type="text" name="age" id="age" class="form-control readonly" value="{{$age}}"> 
         @if ($errors->has('age'))
         <span class="error">{{ $errors->first('age') }}</span>
         @endif
       </div>

     </div> 



    

    <div class="line"></div>   
     <div class="form-group row">
         <label class="col-sm-2">Person With Disability<sup>*</sup></label>
         <div class="col row">
         <div class="col-sm-2">
         <div class="custom-control custom-radio ">
           <input type="radio" class="custom-control-input" name="is_pwd_checkbox" id="is_pwd_yes" value="1" @if($same_address == 1) checked="checked" @endif> 
           <label class="custom-control-label" for="is_pwd_yes">Yes</label>

         </div>
        </div>

        <div class="col-sm-2">
         <div class="custom-control custom-radio ">
           <input type="radio" class="custom-control-input" id="is_pwd_no" name="is_pwd_checkbox" value="0" @if($same_address == 0) checked="checked" @endif> 
           <label class="custom-control-label" for="is_pwd_no">No</label>

         </div>
        </div>

      </div>
    </div>

  
 


 <div class="form-group row">       
  <div class="col">
   <button type="button" id="absentee-submit" style="float: right;" class="btn btn-primary">Preview</button>
 </div>
</div>

</div>
</div>

</div>



</div>
</div>
</div> 
<input type="hidden" name="st_code" id="st_code">
<input type="hidden" name="ac_no" id="ac_no">
<input type="hidden" name="is_pwd" id="is_pwd">
</form>

 </section>     

{!!$footer!!}
@endsection
@section('footerscript')
<script type="text/javascript">
  $(document).ready(function(e){

    $('input[name=same_address]').change(function(){
      if($('input[name=same_address]:checked').val() == '1'){
        $('.same_address_no').addClass("display_none");
      }else{
        $('.same_address_no').removeClass("display_none");  
      }
    });   

    $('#epic_no_search').click(function(){
      $.ajax({
        url: "{!! $load_by_epic !!}",
        type: 'POST',
        data: '_token=<?php echo csrf_token() ?>&epic_no='+$('#epic_no').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.error_message').remove();
          $('#epic_no_search').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {   
          if(json['success'] == false){
            $('#epic_no').parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
             error_messages(json['message']);
          }else{
            $(".main_div").removeClass("display_none");
            if(json['basic'].name != '' && json['basic'].name != null){
              $("#name").val(json['basic'].name);
              $("#name").prop('readonly',true);
            }
            if(json['basic'].rln_name != '' && json['basic'].rln_name != null){
              $("#father_name").val(json['basic'].rln_name);
              $("#father_name").prop('readonly',true);
            }
            if(json['basic'].age != '' && json['basic'].age != null){
              $("#age").val(json['basic'].age);
              $("#age").prop('readonly',true);
            }
            if(json['address'].MOBILE_NO != '' && json['address'].MOBILE_NO != null){
              $("#mobile").val(json['address'].MOBILE_NO);
              $("#mobile").prop('readonly',true);
            }
            if(json['basic'].part_no != '' && json['basic'].part_no != null){
              $("#part_no").val(json['basic'].part_no);
              $("#part_no").prop('readonly',true);
            }
            if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
              $("#serial_number").val(json['basic'].slno_inpart);
              $("#serial_number").prop('readonly',true);
            }
            if(json['address'].Address != '' && json['address'].Address != null){
              $("#address").val(json['address'].Address);
              $("#address").prop('readonly',true);
            }
            if(json['address'].C_VILLAGE != '' && json['address'].C_VILLAGE != null){
              $("#village").val(json['address'].C_VILLAGE);
              $("#village").prop('readonly',true);
            }
            if(json['address'].C_STREET_AREA != '' && json['address'].C_STREET_AREA != null){
              $("#tehsil").val(json['address'].C_STREET_AREA);
              $("#tehsil").prop('readonly',true);
            }
            if(json['address'].C_PIN_CODE != '' && json['address'].C_PIN_CODE != null){
              $("#pincode").val(json['address'].C_PIN_CODE);
              $("#pincode").prop('readonly',true);
            }
            if(json['basic'].dist_name != '' && json['basic'].dist_name != null){
              $("#district").val(json['basic'].dist_name);
              $("#district").prop('readonly',true);
            }
            if(json['basic'].st_name != '' && json['basic'].st_name != null){
              $("#st_name").val(json['basic'].st_name);
              $("#st_name").prop('readonly',true);
            } 
            if(json['basic'].st_code != '' && json['basic'].st_code != null){
              $("#st_code").val(json['basic'].st_code);
              $("#st_code").prop('readonly',true);
            }        
            if(json['basic'].ac_name != '' && json['basic'].ac_name != null){
              $("#ac_name").val(json['basic'].ac_name);
              $("#ac_name").prop('readonly',true);
            }

            if(json['basic'].ac_no != '' && json['basic'].ac_no != null){
              $("#ac_no").val(json['basic'].ac_no);
              $("#ac_no").prop('readonly',true);
            }

            if(json['address'].pwd_status != '' && json['basic'].ac_no != null){
              $("#ac_no").val(json['basic'].ac_no);
              $("#ac_no").prop('readonly',true);
            }

            if(json['basic']['is_pwd'] == 1){
              $("#is_pwd_yes").prop('checked', true);
              $("input[name=is_pwd_checkbox]").prop('disabled',true);
            }else{
              $("#is_pwd_no").prop('checked', true);
              $("input[name=is_pwd_checkbox]").prop('disabled',true);
            }
            $('input[name=is_pwd]').val(json['basic']['is_pwd']);

          }  
          $('.loading_spinner').remove();    
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      });
    });

    $('#absentee-submit').click(function(e){
      $.ajax({
        url: "{!! $post_12d_form !!}",
        type: 'POST',
        data: $('#form12d_form').serialize(),
        dataType: 'json', 
        beforeSend: function() {
          $('.error_message').remove();
          $('#absentee-submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {   
          if(json['success'] == false){
            if(json['errors']['name']){
                $('#name').after("<span class='text-danger error_message'>"+json['errors']['name'][0]+"</span>");
              }
              if(json['errors']['father_name']){
                $('#father_name').after("<span class='text-danger error_message'>"+json['errors']['father_name'][0]+"</span>");
              }
              if(json['errors']['house_no']){
                $('#house_no').after("<span class='text-danger error_message'>"+json['errors']['house_no'][0]+"</span>");
              }
              if(json['errors']['address']){
                $('#address').after("<span class='text-danger error_message'>"+json['errors']['address'][0]+"</span>");
              }
              if(json['errors']['tehsil']){
                $('#tehsil').after("<span class='text-danger error_message'>"+json['errors']['tehsil'][0]+"</span>");
              }
              if(json['errors']['village']){
                $('#village').after("<span class='text-danger error_message'>"+json['errors']['village'][0]+"</span>");
              }
              if(json['errors']['pincode']){
                $('#pincode').after("<span class='text-danger error_message'>"+json['errors']['pincode'][0]+"</span>");
              }

              if(json['errors']['new_house_no']){
                $('#new_house_no').after("<span class='text-danger error_message'>"+json['errors']['new_house_no'][0]+"</span>");
              }
              if(json['errors']['new_address']){
                $('#new_address').after("<span class='text-danger error_message'>"+json['errors']['new_address'][0]+"</span>");
              }
              if(json['errors']['new_tehsil']){
                $('#new_tehsil').after("<span class='text-danger error_message'>"+json['errors']['new_tehsil'][0]+"</span>");
              }
              if(json['errors']['new_village']){
                $('#new_village').after("<span class='text-danger error_message'>"+json['errors']['new_village'][0]+"</span>");
              }
              if(json['errors']['new_pincode']){
                $('#new_pincode').after("<span class='text-danger error_message'>"+json['errors']['new_pincode'][0]+"</span>");
              }

              if(json['errors']['new_st_code']){
                $('#new_st_code').after("<span class='text-danger error_message'>"+json['errors']['new_st_code'][0]+"</span>");
              }

              if(json['errors']['new_dist_no']){
                $('#new_district').after("<span class='text-danger error_message'>"+json['errors']['new_dist_no'][0]+"</span>");
              }

              if(json['errors']['warning']){
                error_messages(json['errors']['warning']);
              }

              if($(".error_message").length){
                $('html, body').animate({
                    scrollTop: $(".error_message").first().offset().top-120
                }, 700);
              }
  
          }else{
            window.location.href = json.redirect_to;
          }
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      });
    });
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
  $("#new_district").empty().append(html);
  if(district==''){
    $("#new_district").val($("#district option:first").val());
  }
}


</script>
@endsection