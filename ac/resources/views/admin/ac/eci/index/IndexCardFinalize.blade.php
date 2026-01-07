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

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 

  </div>


</div>  


</div>

  <div class="row">
   <div class="col-md-5 statistics pt-4 pb-2">  
      <form id="generate_report_id" method="get" onsubmit="return false;">

        <select name="state" id="state" class="form-control" onchange ="filter()">
            <option value="">All State</option>
            @foreach($states as $result)
              @if($state== base64_decode($result['code']))
                <option value="{{$result['code']}}" selected="selected">{{$result['name']}}</option> 
              @else 
                <option value="{{$result['code']}}" >{{$result['name']}}</option> 
              @endif  
            @endforeach
            </select>

        </form>      
    </div>
    
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
			<th rowspan="2">AC Num - AC Name</th>
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
         @endphp

        @forelse($results as $result)
          <tr>
             <td>{{ $count }}</td>

            <td>{{ $result->st_name }}</td>
            <td>{{ $result->acno }} - {{ $result->ac_name }}  </td>
				
			@php if($result->NominationFinalize  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->NominationFinalize  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->NominationFinalize }}</td>
            @php } @endphp
			
			
			@php if($result->CountingFinalize  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->CountingFinalize  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->CountingFinalize }}</td>
            @php } @endphp
			
			 @php if($result->FinalizeRo  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->FinalizeRo  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->FinalizeRo }}</td>
            @php } @endphp


            @php if($result->FinalizeCeo  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->FinalizeCeo  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->FinalizeCeo }}</td>
            @php } @endphp
			

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


