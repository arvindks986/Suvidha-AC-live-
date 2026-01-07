@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'Add State Party Recognized')
@section('content') 
<?php $i=1; $url = URL::to("/");   ?>
<style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
     
    .text-red{
       font-size: 12px; 
      color: red;
    }
    .text-green{
      font-size: 12px; 
      color:green;
    }
  
  </style>
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
          <div class="col-md-4"> Select Political Party:- </div>  
                
          <div class="col-md-4">
            <select name="partyabbre" id="partyabbre"  class="form-control">
                  <option value="">-- Select One --</option>
                  @foreach($parties as $iterate)
                     @if($partyabbre==$iterate['PARTYABBRE'])
                     <option value="{{$iterate['PARTYABBRE']}}" selected="selected">{{$iterate['PARTYNAME']}}</option>
                     @else
                      <option value="{{$iterate['PARTYABBRE']}}">{{$iterate['PARTYNAME']}}</option>
                     
                     @endif
                @endforeach
                </select>
                <span id="err1" class="text-danger"></span>
                     <span id="err" class="text-green"></span>
           @if ($errors->has('partyabbre'))
                    <span style="color:red;">{{ $errors->first('partyabbre') }}</span>
            @endif
          
          
          </div>
        </div>
         <div class="line"></div> 
         <div class="form-group row">
          <div class="col-md-4"> Select State:- </div>  
                
          <div class="col-md-4">
            <select name="st_code" id="st_code"  class="form-control">
                  <option value="">-- Select One --</option>
                  @foreach($states as $iterate)
                     @if($st_code==$iterate['ST_CODE'])
                     <option value="{{$iterate['ST_CODE']}}" selected="selected">{{$iterate['ST_NAME']}}</option>
                     @else
                      <option value="{{$iterate['ST_CODE']}}">{{$iterate['ST_NAME']}}</option>
                     
                     @endif
                @endforeach
                </select>
           @if ($errors->has('st_code'))
                    <span style="color:red;">{{ $errors->first('st_code') }}</span>
            @endif
          <span id="err1" class="text-danger"></span>
          
          </div>
        </div>
         <div class="line"></div> 
        

         <div class="form-group row">
             <div class="col-md-4">Remarks:- </div>  
            <div class="col-md-4">
              <textarea name="remarks" id="remarks" class="form-control">{{$remarks}}</textarea> 
           @if ($errors->has('remarks'))
                    <span style="color:red;">{{ $errors->first('remarks') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         <div class="line"></div> 
         
         <div class="card-footer">
          <div class="form-group text-right" align="text-right">

            <button type="submit" id="candnomination" class="btn btn-primary custombtn" align="text-right">Save</button></div>
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

    $("#partyabbre").focusout(function () {
        var partyabbre = $("#partyabbre").val();  
    
        $.ajax({
            url: "{{url('/mparty/getdparty')}}",
              type: 'GET',
              data: {partyabbre:partyabbre},
            success: function(result){
              console.log(result);
              if(result.message==0){
                $("#err").html('');
                if(result.state!=''){
                    $("#err").html('This Party already recognized in State' + result.state);
                  }
                }
              else{
                 $("#err").html('');
                 $("#err").html(result.message);
              }
            },   
            error: function(data){
              $("#partyabbre").val('');
              $("#err").html('');
              $("#err").html('Error to party name');
             
            }
        });

    });
   $("#partyabbre").change(function () {
       if($("#partyabbre").val()!=""){
      $('#election_form #partyabbre').next('.text-danger').text("").hide();
      }
    });
    $("#st_code").change(function () {
       if($("#st_code").val()!=""){
      $('#election_form #st_code').next('.text-danger').text("").hide();
       }
    });
     
     
    $("#remarks").keypress(function () {
      if($("#remarks").val()!=""){
        $('#election_form #remarks').next('.text-danger').text("").hide();
      }
    });  

    $("#election_form").submit(function(){
    var is_error = false;   
    
     if($('#election_form #partyabbre').val()=="") {  
          $('#election_form #partyabbre').next('.text-danger').text("please Select Political Party.").show();
         is_error = true;
         }
     if($('#election_form #st_code').val()=="") {  
        $('#election_form #st_code').next('.text-danger').text("please select State Name.").show();
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