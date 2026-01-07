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
   <h4>{!! $heading_title !!}</h4>
  </div>

<div class="col-md-5  pull-right text-right">

@if($stname)
State : <span class="badge badge-info" style="padding:10px;">{{$stname->ST_NAME}}</span>
@endif
@if($distname)
District : <span class="badge badge-info" style="padding:10px;">{{$distname->DIST_NAME}}</span>
@endif
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
			<th rowspan="2">AC No</th> 
			<th rowspan="2">AC Name </th>
			<th rowspan="2">Counting status</th>	          
				<th rowspan="2">Vote Margin <br/> between leading & <br/> Trailing candidates</th>	 			          
				<th rowspan="2">Number of <br/>Rejected PB</th>	 			          
				<th colspan="2">PS with Null Counts </th>	 			          
				<th rowspan="2">Result </th>	 			          
        </tr>
		
		  <tr>
			<th rowspan="1" align="right">No of PS</th>
			<th rowspan="1" align="right">Total votes </th> 
				 			          
        </tr>
       
        </thead>
        <tbody>
        @php  

        $count = 1;
         @endphp

        @forelse($dataArr as $result)
          <tr>
             <td>{{ $count }}</td>

            <td>{{ $result['ac_no'] }}</td>
            <td>{{ $result['ac_name'] }}  </td>
            <td>{{ $result['counting_status'] }} </td>
            <td align="right">{{ $result['votes_margin'] }}  </td>
            <td align="right">{{ $result['rejected_postal'] }} </td>
            <td align="right">{{ $result['noofps'] }} </td>
            <td align="right">{{ $result['novotes'] }} </td>
            <td>{{ $result['result_status'] }} </td>
				
			
			
		
	
			

          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Index Card Finalize Statusss</td>                 
              </tr>
          @endforelse
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


