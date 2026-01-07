@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'Change Political Parties Status')
@section('content') 
<?php $i=1; $url = URL::to("/");   ?>
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4>{{$heading_title}}</h4></div> 
             
            </div>
      </div>
  
 <div class="card-body">
      <div class="row">
	    @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
           <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         
	   </div>
     <form class="form-horizontal" id="election_form" method="post" action="{{$action}}" enctype="multipart/form-data" autocomplete='off'>
        {{csrf_field()}}
                 
        <div class="form-group row">
          <div class="col-md-3"> Select Political Party Type:- <sup>*</sup></div>  
                
          <div class="col-md-3">
            <select name="partytype" id="partytype"  class="form-control">
                  @foreach($mpartytype as $iterate)
                     @if($partytype==$iterate['id'])
                     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
                @endforeach
                </select>
           @if ($errors->has('partytype'))
                    <span style="color:red;">{{ $errors->first('partytype') }}</span>
            @endif
          <span id="err1" class="text-danger"></span>
          
          </div>
        
          <div class="col-md-3">Political Party:- <sup>*</sup></div>  
                
          <div class="col-md-3">
                <select name="partyname" id="partyname"  class="form-control">
                  <option value=""> -- Select One-- </option>
                  @foreach($parties as $party)
                      <option value="{{$party['CCODE']}}">{{$party['PARTYNAME']}}</option>
                  @endforeach
                </select>
           @if ($errors->has('partyname'))
                    <span style="color:red;">{{ $errors->first('partyname') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         <div class="line"></div> 
          
           <div class="form-group row">
          <div class="col-md-3"> New Political Party Type:- <sup>*</sup></div>  
                
          <div class="col-md-3">
            <select name="newpartytype" id="newpartytype"  class="form-control">
                  @foreach($mpartytype as $iterate)
                     @if($partytype==$iterate['id'])
                     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
                @endforeach
                </select>
           @if ($errors->has('partytype'))
                    <span style="color:red;">{{ $errors->first('partytype') }}</span>
            @endif
          <span id="err1" class="text-danger"></span>
          
          </div>
         
          <div class="col-md-3">Remarks:- <sup>*</sup></div>  
            <div class="col-md-3">
              <textarea name="remarks" id="remarks" class="form-control"  ></textarea>
                
           @if ($errors->has('remarks'))
                    <span style="color:red;">{{ $errors->first('remarks') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         <div class="line"></div> 
         <div class="card-footer">
          <div class="form-group text-right" align="text-right">

            <button type="submit" id="candnomination" class="btn btn-primary custombtn" align="text-right">Update</button></div>
        </div>  


     </form>
     
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>
 

@endsection
 @section('script')
   <script type="text/javascript">
        $(document).ready(function(e){
           
          $('#partytype').focusout(function(){
          var partytype =  $('#partytype').val();
           //alert(partytype);
            $.ajax({
                    url: "{{url('/mparty/getpartybypartytype')}}",
                    type: 'GET',
                    data: {partytype:partytype},
                    success: function(result){
                      console.log(result);
                      var partyselect = $('form select[name=partyname]');
                       partyselect.empty();
                      $("select[name='partyname']").html(result);
      //                 var partyselect = $('form select[name=partyname]');
      //                 partyselect.empty();
      //                 var partyhtml = '';
      //                     partyhtml = partyhtml + '<option value="">-- Select Party --</option> ';
      //                 $.each(result,function(key, value) {
      //                   partyhtml = partyhtml + '<option value="'+value.CCODE+'">'+value.PARTYNAME+'</option>';
      //                   $("select[name='partyname']").html(partyhtml);
      //                 });
      //                 var partyhtml_end = '';
      //                 $("select[name='partyname']").append(partyhtml_end)

                     }
                });
              });
  $("#partytype").change(function () {
       if($("#partytype").val()!=""){
      $('#election_form #partytype').next('.text-danger').text("").hide();
      }
    });
    $("#partyname").change(function () {
       if($("#partyname").val()!=""){
      $('#election_form #partyname').next('.text-danger').text("").hide();
       }
    });
    $("#newpartytype").change(function () {
       if($("#newpartytype").val()!=""){
      $('#election_form #newpartytype').next('.text-danger').text("").hide();
       }
    });
     
    $("#remarks").keypress(function () {
      if($("#remarks").val()!=""){
        $('#election_form #remarks').next('.text-danger').text("").hide();
      }
    });

  $("#election_form").submit(function(){
    var is_error = false;   
    
     if($('#election_form #partytype').val()=="") {  
          $('#election_form #partytype').next('.text-danger').text("please  select party type.").show();
         is_error = true;
         }
     if($('#election_form #partyname').val()=="") {  
        $('#election_form #partyname').next('.text-danger').text("please Select party name.").show();
         is_error = true;
          
         } 
      if($('#election_form #newpartytype').val()=="") {  
        $('#election_form #newpartytype').next('.text-danger').text("please Select New Party type.").show();
         is_error = true;
          
         } 
     
    if($('#election_form #remarks').val()=="") {  
        $('#election_form #remarks').next('.text-danger').text("please enter remarks.").show();
         is_error = true;
          
         } 
      if(is_error){
          return false;
        }   
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