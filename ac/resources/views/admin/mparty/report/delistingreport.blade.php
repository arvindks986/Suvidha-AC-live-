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
<div class="col-md-9"><h4>{{$heading_title}}</h4></div> 
 
<div class="col-md-3">
  @foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
 
</div>   
</div>
</div>

<div class="table-responsive card-body">
 

@if(isset($results) and ($results))  
<table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
<thead>
<tr>
<th>Sl. No.</th>
<th>Party Abbre</th>
<th>Party Name</th>
<th>Party Abbre In Hindi</th>
<th>Party Name In Hindi</th>  
<th>Party Type</th>
 
 
</tr>
</thead>
<tbody>

@foreach ($results as $key=>$list)  
<tr><td>{{$i}}</td>
<td>{{$list['PARTYABBRE']}}</td>
<td>{{$list['PARTYNAME']}}</td>
<td width="100px">{{$list['PARTYHABBR']}}</td>
<td>{{$list['PARTYHNAME']}}</td>
<td> @if($list['PARTYTYPE']=="N") National @endif 
     @if($list['PARTYTYPE']=="S") State  @endif 
      @if($list['PARTYTYPE']=="U") Unrecognized @endif
</td>
  
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