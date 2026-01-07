@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'Add New Political party')
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
            <div class="col-md-2"><button type="submit" id="" onclick="window.location.href='{{$url}}/mparty/list-party';"class="btn btn-primary custombtn" align="text-right">List Of Political Party</button></div> 
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
          <div class="col-md-3">Party Type:- <sup>*</sup></div>  
                
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
        
          <div class="col-md-3">Party Name in English:- <sup>*</sup></div>  
                
          <div class="col-md-3">
                <input type='text'  name="partyname" id="partyname" class="form-control"  
            value="{{$partyname}}" placeholder="Party Name In English" />
            <span id="err" class="text-danger"></span>
          
           @if ($errors->has('partyname'))
                    <span style="color:red;">{{ $errors->first('partyname') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         
          
          <div class="form-group row">
             <div class="col-md-3"> Party ABBRE in English:-<sup>*</sup> </div>  
            <div class="col-md-3">
            <input type='text'  name="partyabbre" id="partyabbre" class="form-control"  
            value="{{$partyabbre}}" placeholder="Party Abbre in English" />
            <span id="err11" class="text-danger"></span>
            <span id="err12" class="text-green"></span>
           @if ($errors->has('partyabbre'))
                    <span style="color:red;">{{ $errors->first('partyabbre') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
         
          <div class="col-md-3">Party Name in Hindi:- <sup>*</sup></div>  
                
          <div class="col-md-3">
                <input type='text'  name="partyhname" id="partyhname" class="form-control"  
            value="{{$partyhname}}" placeholder="Party Name In Hindi" />
           @if ($errors->has('partyhname'))
                    <span style="color:red;">{{ $errors->first('partyhname') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         
          
          <div class="form-group row">
             <div class="col-md-3"> Party ABBRE in Hindi:- <sup>*</sup></div>  
            <div class="col-md-3">
                <input type='text'  name="partyhabbr" id="partyhabbr" class="form-control"  
            value="{{$partyhabbr}}" placeholder="Party Abbre in Hindi" />
           @if ($errors->has('partyhabbr'))
                    <span style="color:red;">{{ $errors->first('partyhabbr') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
         
             <div class="col-md-3">Remarks:- <sup>*</sup></div>  
            <div class="col-md-3">
              <textarea name="remarks" id="remarks" class="form-control">{{$remarks}}</textarea> 
           @if ($errors->has('remarks'))
                    <span style="color:red;">{{ $errors->first('remarks') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         <div class="form-group row">
             <div class="col-md-3"> Party Registration Date:-  </div>  
            <div class="col-md-3">
              <input type="text" name="party_reg_date" id="party_reg_date" class="form-control" value="{{$party_reg_date}}">
               
           @if ($errors->has('party_reg_date'))
                    <span style="color:red;">{{ $errors->first('party_reg_date') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
         
              
        </div>
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
     $('#party_reg_date').datetimepicker({
                maxDate: new Date(),
                format: "D-M-YYYY"
            }); 
    $("#partyabbre").focusout(function () {
    var partyabbre = $("#partyabbre").val();  
    
        $.ajax({
            url: "{{url('/mparty/verifypartyabbre')}}",
              type: 'GET',
              data: {partyabbre:partyabbre},
            success: function(result){
              if(result.message==0){
                //$("#err11").html('');
                $("#err12").html('This party abbre exit ' + result.partyname);
               // $("#err11").html('This party abbre exit ' + result.partyname);
                }
              else{
                 //$("#partyabbre").val('');
                 $("#err11").html('');
                 $("#err12").html('party abbre not Exit ');
              }
            },   
            error: function(data){
              $("#partyabbre").val('');
              $("#err11").html('');
              $("#err12").html('');
              $("#err12").html('Error to party abbre');
             
            }
        });

    });
    $("#partytype").change(function () {
       if($("#partytype").val()!=""){
    	$('#election_form #partytype').next('.text-danger').text("").hide();
      }
    });
    $("#partyname").keypress(function () {
       if($("#partyname").val()!=""){
    	$('#election_form #partyname').next('.text-danger').text("").hide();
       }
    });
    $("#partyabbre").keypress(function () {
       if($("#partyabbre").val()!=""){
    	$('#election_form #partyabbre').next('.text-danger').text("").hide();
       }
    });
    $("#partyhname").keypress(function () {
       if($("#partyhname").val()!=""){
    	$('#election_form #partyhname').next('.text-danger').text("").hide();
       }
    });
    $("#partyhabbr").keypress(function () {
      if($("#partyhabbr").val()!=""){
    	$('#election_form #partyhabbr').next('.text-danger').text("").hide();
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
        $('#election_form #partyname').next('.text-danger').text("please enter party name in english.").show();
         is_error = true;
          
         } 
      if($('#election_form #partyabbre').val()=="") {  
        $('#election_form #partyabbre').next('.text-danger').text("please enter party abbre in english.").show();
         is_error = true;
          
         } 
    if($('#election_form #partyhname').val()=="") {  
        $('#election_form #partyhname').next('.text-danger').text("please enter party name in hindi.").show();
         is_error = true;
          
         } 
    if($('#election_form #partyhabbr').val()=="") {  
        $('#election_form #partyhabbr').next('.text-danger').text("please enter party habbre in hindi.").show();
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