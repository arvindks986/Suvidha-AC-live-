@extends('candidate.common.app')
  @section('seo')
    <title>Absentee Voter Form - 12D</title>
  @endsection
  @section('style')
  <style type="text/css">
  .midBox {
    max-width: 570px;
    margin: auto;     box-shadow: 0 0 9px 4px #e5e5e5;
}
.loadbtn{position:absolute; top:10px; bottom:0; left:-20px; display:block; font-size:24px; width:10px; height:10px; color:grey;}
.error_message { text-transform: capitalize;}
.title-case{text-transform:capitalize;     padding: 6px 0 0 0;}
label{padding-top:10px; margin-bottom:0;}
</style>
  @endsection
@section('content')
{!!$header!!}
@include('candidate.common.breadcrumb')
<div class="midBox">
 <div class="row">

      <div class="col-md-12">
	   
        <div class="card" style="min-height:200px;">
         <div class="card-header d-flex align-items-center">
           <h4 class="title-case">{{$heading_title}}</h4>
         </div>
      
     <div class="card-body mb-0">
           <div class="row">
            
             <div class="col">
              {{$message}}
            </div> 



</div>
</div>


</div>
</div>
</div>
</div>
   



      

{!!$footer!!}
@endsection
@section('footerscript')
@endsection