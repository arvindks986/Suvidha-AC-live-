@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'Add / Edit State Party')
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
              <input type="hidden" name="party_abbre" value="{{$party_abbre}}">
              <input type="hidden" name="partyabbre" value="{{$partyabbre}}">
              @if(isset($record))
              @foreach($record as $rec)
                <input type="hidden" name="id[]" value="{{$rec['dpartyid']}}">
                <input type="hidden" name="state_name[]" value="{{$rec['state_name']}}">

              @endforeach
              @endif
         
        <div class="form-group row">
          <div class="col-md-3">Political Party Name:- </div>  
                
          <div class="col-md-3">
                <input type='text'  name="partyname" id="partyname" class="form-control"  
            value="{{$partyname}}" placeholder="Party Name In English" readonly="readonly"/>
            <span id="err" class="text-danger"></span>
          
           @if ($errors->has('partyname'))
                    <span style="color:red;">{{ $errors->first('partyname') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
         
             <div class="col-md-3"> Party ABBRE:- </div>  
            <div class="col-md-3">
                <input type='text'  name="partyabbre" id="partyabbre" class="form-control"  
            value="{{$partyabbre}}" placeholder="Party Abbre in English" readonly="readonly" />
            <span id="err" class="text-danger  error_message"></span>
           @if ($errors->has('partyabbre'))
                    <span style="color:red;">{{ $errors->first('partyabbre') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
          
          </div>
        </div>
         <div class="line"></div> 
        <div class="form-group row">
             <div class="col-md-4">Party Registered State Name:- </div>  
            <div class="col-md-8"> 
                    @if(isset($record))
              @foreach($record as $rec)
                          <b><U>{{$rec['state_name'] }}</U></b>,
              @endforeach
              @endif

          </div>
        </div>
         <div class="line"></div> 
         <div class="form-group row">
          <div class="col-md-4"> Select State Name:- </div>  
                
          <div class="col-md-4">
            <select name="st_code" id="st_code"  class="form-control">
               <option value="" selected="selected">-- Select One --</option>
                  @foreach($states as $iterate)
                      <option value="{{$iterate['ST_CODE']}}">{{$iterate['ST_NAME']}}</option>
                      
                @endforeach
                </select>
           @if ($errors->has('partytype'))
                    <span style="color:red;">{{ $errors->first('partytype') }}</span>
            @endif
          <span id="err1" class="text-danger"></span>
          
          </div>
        </div>
         <div class="line"></div> 
           

         <div class="form-group row">
             <div class="col-md-4">Remarks:- </div>  
            <div class="col-md-4">
                <input type='text'  name="remarks" id="remarks" class="form-control"  
            value="" placeholder="Remarks" />
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
           
          $('#partyabbre').focusout(function(){
          var partyabbre =  $('#partyabbre').val();
           //alert(partyabbre);
            $.ajax({
                    url: "{{url('/mparty/verifypartyabbre')}}",
                    type: 'GET',
                    data: {partyabbre:partyabbre},
                    success: function(result){
                      $('.err').html('');
                      $('.text-danger').html('');
                     // $('.err').html('This Party Abbree Allready Exit! Please choose Other');
                       $('#partyabbre').after("<span class='text-danger error_message'> This Party Abbree Allready Exit! Please choose Other</span>");
                       //  dd(result);

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
