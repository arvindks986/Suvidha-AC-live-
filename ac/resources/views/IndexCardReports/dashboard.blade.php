@extends('admin.layouts.ac.theme')

@section('content')
@section('title', 'ECI-INDEX')

@section('bradcome')
  <li><span class="icon icon-beaker"> </span>Dashboard</li>
@endsection
<style>
b {
    font-weight: 800;
}
.table-striped th {
    color: #fff !important;
}
.col-sm-3{
	float: left;
}
.progress {
    height: 20px;
}
.icon-box {
    font-size: 4.5rem;
    margin-left: 4.5rem;
	color: #717171;
}

.text-danger{
	font-size:1.0rem;
}


</style>
<main>
<section>
<div class="container-fluid">
<div class="row">
	<div class="col-md-6 col-12">
	  <div class="card mt-4">
       <div class="card-body d-flex justify-content-between align-items-center">	
        <div class="prgss-box">
		 <h5 class="pb-2 mb-4 text-primary">Index Card Send By RO To CEO Office</h5>
		  <div class="progress">
			  <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{$data['ro']}}%" aria-valuenow="{{$data['ro']}}" aria-valuemin="0" aria-valuemax="100">{{$data['ro']}}%</div>
		   </div>
	    </div><!-- End OF prgss Div -->		
	    <div class="icon-box"><i class="fa fa-file-text" aria-hidden="true"></i></div><!-- End Of icon-box Div -->
		</div>	
	   </div>
	</div>
	
	<div class="col-md-6 col-12">	
	<div class="card mt-4">
	   <div class="card-body d-flex justify-content-between align-items-center">
	   <div class="prgss-box"> 
	   <h5 class="pb-2 mb-4 text-success">Index Card Send By CEO To ECI Office</h5>
		<div class="progress">
		  <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{$data['ceo']}}%" aria-valuenow="{{$data['ceo']}}" aria-valuemin="0" aria-valuemax="100">{{$data['ceo']}}%</div>
		</div>
		</div><!-- End OF prgss Div -->	
		<div class="icon-box"><i class="fa fa-file-text" aria-hidden="true"></i></div><!-- End Of icon-box Div -->
		</div>
	   </div>
	</div>
	
	<div class="col-md-6 col-12">
	<h4>Verified Statistical Reports</h4>
	<div class="card">		
     <div class="card-body">	
	    <ol class="mb-0 pl-3">
			@foreach($stateList as $raw)
				@if($raw->is_verified == '1')
					<li class="text-success pb-3">Statistical Reports for '{{$raw->ST_NAME}}' Verified By EDMD Section on {{date('d-m-Y H:i A', strtotime($raw->verifiat_date))}}.</li>							
				@else
					<li class="text-danger pb-3">Statistical Reports for '{{$raw->ST_NAME}}' Not Yet Verified By EDMD Section.</li> 
				@endif
			@endforeach
		</ol>	
			</div>
		</div>
	</div>
	
	<div class="col-md-6 col-12">
	<h4>Published Statistical Reports</h4>
		<div class="card">
		 <div class="card-body">
		    <ol class="mb-0 pl-3">
			@foreach($publishReportList as $raw)
				@if($raw->is_verified == '1')
					<li class="text-success pb-3">Statistical Reports for '{{$raw->ST_NAME}}' Published On ECI Website on {{date('d-m-Y H:i A', strtotime($raw->verifiat_date))}}.</li>							
				@else
					<li class="text-danger pb-3">Statistical Reports for '{{$raw->ST_NAME}}' Not Yet Published On ECI Website.</li> 
				@endif
			@endforeach
		    </ol>	
		 </div>
		</div>
	</div>
	

</div>

<h4>Index Card Finalization Status</h4>
<div class="card">
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
			<tr>
				<th rowspan="2">Serial No</th>
				<th rowspan="2">State Name</th> 
				<th rowspan="2">Total ACs</th> 
				<th rowspan="2">Nomination Module Finalized</th>  
				<th rowspan="2">Counting Module Finalized</th> 		  
				<th colspan="2">Index Card Varification & Finalization</th> 		          
			</tr>
			<tr>
				<th>Finalized By RO</th> 
				<th>Finalized By CEO</th>
			</tr>
		</thead>
        <tbody>
        @php  

        $count = 1;

        $TotalAc = 0;
        $FinalAc = 0;
		 $FinalAcCeo = 0;
		 $NominationFinalize = 0;
		 $CountingFinalize = 0;
         @endphp

        @forelse($results as $result)

        @php
         $TotalAc +=$result->total_ac;
         $FinalAc +=$result->finalize;
		  $FinalAcCeo  +=$result->FinalizeCeo;
		  $NominationFinalize  +=$result->NominationFinalize;
		  $CountingFinalize  +=$result->CountingFinalize;

         @endphp

          <tr>
             <td>{{ $count }}.</td>

             @php
           if($user_data->role_id=='27'){ @endphp
            <td> <a href="{{url('/eci-index/indexcard/IndexCardFinalize?state=')}}{{base64_encode($result->st_code)}}">{{ $result->st_name }}</a></td>
           @php }else{ @endphp
             <td> <a href="{{url('/eci/indexcard/IndexCardFinalize?state=')}}{{base64_encode($result->st_code)}}">{{ $result->st_name }}</a></td>
        @php   } 

            @endphp
			
            <td>{{ $result->total_ac }}  </td>
			<td>{{ $result->NominationFinalize }}  </td>
			 <td>{{ $result->CountingFinalize }}  </td>
            <td>{{ $result->finalize }}  </td>
			 <td>{{ $result->FinalizeCeo }}  </td>
			 
          
          
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Index Card Finalize </td>                 
              </tr>
          @endforelse
          <tr><td colspan="2" style="text-align:center;"><b>Total</b></td>
			  <td><b>{{$TotalAc}}</b></td>
			  <td><b>{{$NominationFinalize}}</b></td>
			  <td><b>{{$CountingFinalize}}</b></td>
			  <td><b>{{$FinalAc}}</b></td>
			  <td><b>{{$FinalAcCeo}}</b></td>	  
		  </tr>
        </tbody>
    </table>
   </div>
</div><!-- End Of card Div -->
	</div>
    </div>
  </section>


</main>  
 

@endsection