@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'List of Delisted Parties')
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
            <a href="{{$action}}"><button type="button" class="btn btn-primary" align="text-right">Back</button></a>
            </div>
      </div>
  
 <div class="table-responsive card-body">
    @if(isset($singlelist))  
    <table  class="table table-striped table-bordered table-hover" style="width:100%">
         
        <tr><td>Current Records</td>
           <td width="100px">{{$singlelist['PARTYABBRE']}}</td>
           <td>{{$singlelist['PARTYNAME']}}</td>
           <td width="100px">{{$singlelist['PARTYHABBR']}}</td>
           <td>{{$singlelist['PARTYHNAME']}}</td>
           <td>@if($singlelist['PARTYTYPE']=="N") National @endif 
                 @if($singlelist['PARTYTYPE']=="S") State  @endif 
                @if($singlelist['PARTYTYPE']=="U") Unrecognized @endif
           </td>
           <td>{{$singlelist['remarks']}}</td> 
           
          </tr>
    </table>
   @endif   
    @if(isset($lists))  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
              <th>Sl. No.</th>
              <th>Party Abbre</th>
              <th>Party Name</th> 
              <th>Party Abbre In Hindi</th>
              <th>Party Name In Hindi</th> 
              <th>Party Type</th>
              <th>Remarks</th>
              <th>Date</th>
              <th>Updated BY</th>
          </tr>
        </thead>
        <tbody>
        	 
      @foreach ($lists as $key=>$list)  
           <tr><td>{{$i}}</td>
           <td width="100px">{{$list['PARTYABBRE']}}</td>
           <td>{{$list['PARTYNAME']}}</td>
           <td width="100px">{{$list['PARTYHABBR']}}</td>
           <td>{{$list['PARTYHNAME']}}</td>
           <td>@if($list['PARTYTYPE']=="N") National @endif 
			           @if($list['PARTYTYPE']=="S") State  @endif 
			          @if($list['PARTYTYPE']=="U") Unrecognized @endif
			     </td>
           <td>{{$list['remarks']}}</td> 
           <td>{{date("d-m-Y H:i:s",strtotime($list['log_updated_at']))}}</td>
           <td>{{$list['log_updated_by']}}</td>  
          </tr>
           <?php $i++;?>
          @endforeach
        </tbody>
    </table>
       <a href="{{$action}}" align="right"><button type="button" class="btn btn-primary" align="text-right">Back</button></a>
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