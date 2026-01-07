@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'Edit Symbol')
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
                 <input type="hidden" name="id" value="{{$id}}">
                <input type="hidden" name="sysno" value="{{$sysno}}">
         
        <div class="form-group row">
          <div class="col-md-3">Name in English:- </div>  
          <div class="col-md-3">
                      <input type='text'  name="symbol_des" id="symbol_des" class="form-control"  
                  value="{{$symbol_des}}" placeholder="Symbol Name In English" />
                  <span id="err" class="text-danger"></span>
                
                 @if ($errors->has('symbol_des'))
                          <span style="color:red;">{{ $errors->first('symbol_des') }}</span>
                  @endif
                <span id="err2" class="text-danger"></span>
          </div>
          <div class="col-md-3">Name in Hindi:- </div>  
                
          <div class="col-md-3">
                <input type='text'  name="symbol_hdes" id="symbol_hdes" class="form-control"  
            value="{{$symbol_hdes}}" placeholder="Symbol Name In Hindi" />
           @if ($errors->has('symbol_hdes'))
                    <span style="color:red;">{{ $errors->first('symbol_hdes') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         <div class="line"></div> 
            
          <div class="form-group row">
             <div class="col-md-3">Symbol Image:- </div>  
            <div class="col-md-3">
              @if(isset($lists['Symbol_Img']))
                    <img src="data:{{$lists['CONTENT_TYPE']}};base64, {{$lists['Symbol_Img']}}" alt="Red dot" style="height:50px; width:50px;"  />
                @endif
                  <input type="file" name="symbol_img" id="symbol_img" value="{{$symbol_img}}">
                
           @if ($errors->has('symbol_img'))
                    <span style="color:red;">{{ $errors->first('symbol_img') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
         
             <div class="col-md-3">Remarks:- </div>  
            <div class="col-md-3">
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

            <button type="submit" id="submit_form" class="btn btn-primary custombtn" align="text-right">Save</button></div>
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
   $(document).ready(function () { 

   $('#symbol_img').bind('change', function() {
        var a=(this.files[0].size);
        $('#symbol_img').next('.text-danger').text("").hide();
         
        if(a > 500000) {

         $('#symbol_img').next('.text-danger').text("please select less than 500kb .").show();
         $('#symbol_img').val("");
        };
    });
     
   $("#symbol_des").keypress(function () {
       if($("#symbol_des").val()!=""){
      $('#election_form #symbol_des').next('.text-danger').text("").hide();
       }
    });
    $("#symbol_hdes").keypress(function () {
       if($("#symbol_hdes").val()!=""){
      $('#election_form #symbol_hdes').next('.text-danger').text("").hide();
       }
    });
     
    $("#remarks").keypress(function () {
      if($("#remarks").val()!=""){
        $('#election_form #remarks').next('.text-danger').text("").hide();
      }
    });

  $("#election_form").submit(function(){
    var is_error = false;   

     if($('#election_form #symbol_des').val()=="") {  
          $('#election_form #symbol_des').next('.text-danger').text("please enter sysmbol name in english.").show();
         is_error = true;
         }
     if($('#election_form #symbol_hdes').val()=="") {  
        $('#election_form #symbol_hdes').next('.text-danger').text("please enter sysmbol name in hindi.").show();
         is_error = true;
          
         } 
      // if($('#election_form #symbol_img').val()=="") {  
      //   $('#election_form #symbol_img').next('.text-danger').text("please select images.").show();
      //    is_error = true;
          
      //    } 
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
 