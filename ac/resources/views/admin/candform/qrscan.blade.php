@extends('admin.layouts.ac.theme')
@section('bradcome', 'Online Nomination')
@section('content')  
<style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
   

<link href="{{ asset('theme/main.css') }}" rel="stylesheet">
<?php   
          $url = URL::to("/"); $j=0;
    ?>
  <main role="main" class="inner cover mb-3">
 <section class="bg-scroll">
<div class="container">
	 <div class="row">
     <div class="col pr-0 pl-0">
                 <ul class="steps mb-0" id="progressbar">
                  <li class="step active">Nomination ID</li>
                  <li class="step">Verify Nomination </li>
                  <li class="step">Decision by RO</li>
                  <li class="step">Final Receipt</li>
                  <li class="step">Print Receipt</li>
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
                 <div class="col"> <h3>Nomination ID </h3> </div>  <!-- <i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>&nbsp;&nbsp;  -->
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp;  
            </p></div>
         
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
            @if(!empty($errors->first()))
        <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
      @endif 
         
    </div>
    </div>
  
       
    <div class="card-body">  
       <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/verifyqrcode')}}" enctype="multipart/form-data" autocomplete='off'>
  {{csrf_field()}}
     
     <div class="row d-flex align-items-center ">
         <div class="col-md-2"> 
             <label for="candidate_id" class="col-form-label">Nomination ID:- <span class="errorred">*</span></label>
          </div> 
          <div class="col">  
          <input type="text" name="qrcode" id="qrcode" class="form-control" value="{{isset($qr)?$qr:old('qrcode')}}"/>
         
               @if ($errors->has('qrcode'))
                  <span style="color:red;"><strong>{{ $errors->first('qrcode') }}</strong></span>
               @endif   
               <span id="errmsg" class="text-danger"></span>           
          </div>           
           
           <button type="submit" id="candnomination" class="btn btn-primary ml-auto  mr-3">Search & Next</button> 
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
   $("#election_form").submit(function(){
      
      if($("#qrcode").val()=='')
          {  
          $("#errmsg").text("");
          $("#errmsg").text("Please enter  Nomination No.");
          $("#qrcode").focus();
          return false;
          }
    
    });
});
 </script>
 @endsection