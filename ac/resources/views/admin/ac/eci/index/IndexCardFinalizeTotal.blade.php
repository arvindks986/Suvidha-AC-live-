@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
           <div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>Index Card Finalization Report</h4>
  </div>

<div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 

  </div>


</div>  


</div>

  <div class="row">
  
    @if(isset($filter_buttons) && count($filter_buttons)>0)
      <div class="col-md-5 statistics pt-4 pb-2"> 
              @foreach($filter_buttons as $button)
                  <?php $but = explode(':',$button); ?>
                  <span class="pull-right" style="margin-right: 10px;">
                  <span><b>{!! $but[0] !!}:</b></span>
                  <span class="badge badge-info">{!! $but[1] !!}</span>

                  </span>
                  
              @endforeach
      </div>
      @endif
</div>
   
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
             <td>{{ $count }}</td>

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
          <tr><td><b>Total</b></td><td></td><td><b>{{$TotalAc}}</b></td>
	  <td><b>{{$NominationFinalize}}</b></td>
	  <td><b>{{$CountingFinalize}}</b></td>
	  <td><b>{{$FinalAc}}</b></td>
	  <td><b>{{$FinalAcCeo}}</b></td>
	  
	  </tr>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>



<script type="text/javascript">

function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
  if(jQuery("#state").val() != ''){
      query += '&state='+jQuery("#state").val();
    }
  window.location.href = url+'?'+query.substring(1);
}

setTimeout(function(e){
    referesh_page();
},300000);

function referesh_page(){
    location.reload();
}
</script>

@endsection


