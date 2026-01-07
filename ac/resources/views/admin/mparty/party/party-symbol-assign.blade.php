@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', $bradcome)
@section('content') 
<?php $i=1; $url = URL::to("/");   ?>
<main role="main">

<section>
<div class="container">
<div class="row">
<div class="card text-left" style="width:100%; margin:0 auto;">
<div class=" card-header">
<div class="row">
<div class="col-md-6"><h4>{{$heading_title}}</h4></div> 
<div class="col-md-3  float-right">
   <span><b>Total :- {{$total}}</b></span>
  <button type="button" class="btn btn-primary getdata" data-toggle="modal" data-target="#addsymbol"> Assign Symbol</button> 
</div> 
<div class="col-md-2">
<form class="form-inline pull-right">



<div class="form-group float-right"> 
<label for="noofcards" class="mr-3">Party Type</label> 
<form name="frmparty" id="frmparty" method="POST"  action="" >
<select name="party_type" id="party_type" onchange="this.form.submit();">
@foreach($mpartytype as $iterate)
@if($party_type==$iterate['id'])
<option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
@else
<option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
@endif
@endforeach
</select>
</form>
</div>        

</form>
</div>   
</div>
</div>

<div class="table-responsive card-body">
<div class="row">
@if (session('success_mes'))
<div class="alert alert-success"> {{session('success_mes') }}</div>
@endif
@if (session('error_mes'))
<div class="alert alert-danger"> {{session('error_mes') }}</div>
@endif

</div>

@if(isset($results) and ($results))  
<table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
<thead>
<tr>
<th>Sl. No.</th>
<th>Party Abbre</th>
<th>Party Name</th> 
<th>Party Type</th>
<th>Party Symbol</th>
<th>Action</th>
</tr>
</thead>
<tbody>

@foreach ($results as $key=>$list)  
<tr><td>{{$i}}</td>
<td>{{$list['PARTYABBRE']}}</td>
<td>{{$list['PARTYNAME']}}</td>
<td>@if($list['PARTYTYPE']=="N") National @endif 
@if($list['PARTYTYPE']=="S") State  @endif 
@if($list['PARTYTYPE']=="U") Unrecognized @endif
</td>
<td>{{$list['SYMBOL_DES']}}</td>           

<td><button type="button" id="" class="btn btn-primary egetdata" data-toggle="modal" data-target="#editassign" data-party="{{$list['PARTYNAME']}}" data-symb="{{$list['PARTYSYM']}}" data-ccode="{{$list['CCODE']}}" data-symdes="{{$list['SYMBOL_DES']}}" >Edit</button>  </td>
</tr>
<?php $i++;?>
@endforeach
</tbody>
</table>

</div>
@else
<div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
@endif
</div>
</div>
</div>
</div>
</section>
</main>

<!-- Modal Content Starts here -->
<!-- Modal -->
<div class="modal fade" id="addsymbol" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content">
<div class="modal-header mb-3">
<h4 class="modal-title" id="exampleModalLabel">Party Symbol Assign</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
    <form class="form-horizontal" id="election_form" method="post" action="{{$action}}" enctype="multipart/form-data" autocomplete='off'>
{{csrf_field()}}

<div class="form-group row">
<div class="col-md-4">Political Party:- </div>  

<div class="col-md-8">
  @if(isset($listparty) and ($listparty)) 
      <select name="party" id="party"  class="form-control">
      <option value=""> -- Select One --</option>

      @foreach($listparty as $p)
      <option value="{{$p['CCODE']}}">{{$p['PARTYABBRE']}}-{{$p['PARTYNAME']}}</option>

      @endforeach

      </select>
      @if ($errors->has('party'))
      <span style="color:red;">{{ $errors->first('party') }}</span>
      @endif
      <span id="err1" class="text-danger"></span>
@else
    <p>No New Party Available.</p>
@endif
</div>
</div>
  
<div class="form-group row">
<div class="col-md-4"> Symbol:- </div>  

<div class="col-md-8">
<select name="symbol" id="symbol"  class="form-control">
<option value=""> -- Select One --</option>
@if(isset($listsuassignsymbol)) 
@foreach($listsuassignsymbol as $s)
<option value="{{$s['SYMBOL_NO']}}">{{$s['SYMBOL_DES']}}</option>

@endforeach
@endif
</select>
@if ($errors->has('symbol'))
<span style="color:red;">{{ $errors->first('symbol') }}</span>
@endif
<span id="err1" class="text-danger"></span>

</div>
</div>
 <div class="modal-footer">
    <div class="col text-left">
        <button type="button" class="btn btn-secondary "  data-dismiss="modal">Close</button>
    </div>
    <div class="col text-right">
        <button type="submit" class="btn btn-primary">Assign</button>
    </div>
      </div> 


</form>

</div>

</div>
</div>
</div>
<!-- Modal Content Ends Here -->
<!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="editassign" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Edit Symbol Assign</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="editelection_form" method="POST"  action="{{$eaction}}" >
                {{ csrf_field() }}   
     
    <input type="hidden" name="eccode" id="eccode" value="" readonly="readonly">
<div class="form-group row">
<div class="col-md-4">Political Party:- </div>  
<div class="col-md-8">
  <input type="text" name="eparty" id="eparty" value="" readonly="readonly"> 
  <span id="err1" class="text-danger"></span>

</div>
</div>
  
<div class="form-group row">
<div class="col-md-4"> Symbol:- </div>  

<div class="col-md-8">
<select name="esymbol" id="esymbol"  class="form-control">
<option value=""> -- Select One --</option>
@if(isset($listsuassignsymbol)) 
@foreach($listsuassignsymbol as $s)
<option value="{{$s['SYMBOL_NO']}}" >{{$s['SYMBOL_DES']}}</option>
@endforeach
@endif
</select>
@if ($errors->has('esymbol'))
<span style="color:red;">{{ $errors->first('esymbol') }}</span>
@endif
<span id="err1" class="text-danger"></span>

</div>
</div>
  <div class="modal-footer">
     <div class="col text-left">
        <button type="button" class="btn btn-secondary "  data-dismiss="modal">Close</button>
    </div>
    <div class="col text-right">
        <button type="submit" class="btn btn-primary">Re-Assign</button>
    </div>
  </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->

@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function(e){
$(document).on("click", ".egetdata", function () {  
       party = $(this).attr('data-party');
       symb = $(this).attr('data-symb');
       ccode = $(this).attr('data-ccode');
       symdes = $(this).attr('data-symdes');

       $("#eccode").val(ccode);
       $("#eparty").val(party);
       $('#esymbol').append('<option value="'+ symb +'" selected="selected">' + symdes + '</option>');
  
       //$("#esymbol").val(symb);     
   });
$("#party").change(function () {
       if($("#party").val()!=""){
      $('#election_form #party').next('.text-danger').text("").hide();
      }
    });
    $("#symbol").change(function () {
       if($("#symbol").val()!=""){
      $('#election_form #symbol').next('.text-danger').text("").hide();
       }
    });
    
$("#election_form").submit(function(){
var is_error = false;   

if($('#election_form #party').val()=="") {  
$('#election_form #party').next('.text-danger').text("please select party.").show();
is_error = true;
}
if($('#election_form #symbol').val()=="") {  
$('#election_form #symbol').next('.text-danger').text("please select symbol.").show();
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