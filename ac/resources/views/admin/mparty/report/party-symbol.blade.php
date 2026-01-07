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
<div class="col-md-7"><h4>{{$heading_title}}</h4></div> 
 
<div class="col-md-5">
  @foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
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
 
</tr>
</thead>
<tbody>

@foreach ($results as $key=>$list)  
<tr><td>{{$i}}</td>
<td>{{$list['PARTYABBRE']}}</td>
<td>{{$list['PARTYNAME']}}</td>
<td> @if($list['PARTYTYPE']=="N") National @endif 
     @if($list['PARTYTYPE']=="S") State  @endif 
      @if($list['PARTYTYPE']=="U") Unrecognized @endif
</td>
<td>{{$list['SYMBOL_DES']}}</td>           
 
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