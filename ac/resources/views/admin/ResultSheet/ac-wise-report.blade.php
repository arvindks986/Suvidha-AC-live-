@extends('admin.layouts.ac.dashboard-theme')
@section('content')

<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/jquery.stickytable.min.css') }}">

<div class="loader" style="display:none;"></div>
<section class="dashboard-header section-padding">
    <div class="container-fluid">
		<div class="row" >
		
		 <div class="col-md-8">
		 
        <form  action="{{url('eci/ac-wise-report')}}" class="row" method="POST" >
		{{csrf_field()}}
            <div class="form-group col-md-6">
                <label>Select State</label>
                <select name="state_code" id="state_code" class="form-control"  onchange="getACList(this.value);" required>
                    <option value="">Select State</option>
					<!--<option value="0" @if(isset($data['state']) && $data['state']==0 && $data['state']!="") selected @endif>Select All</option>-->
					@foreach($data['m_state'] as $raw)
                    <option value="{{$raw->ST_CODE}}" @if(isset($data['state']) && $data['state']==$raw->ST_CODE) selected @endif>{{$raw->ST_NAME}} </option>
					@endforeach
                </select>
            </div>
            
			
			 <div class="form-group col-md-1">
                <button type="submit" name="search" id="submit-report" class="btn btn-success" style="margin-top:31px;">Search</button>
            </div>
        </form>
  </div>
  
  <div class="col-md-4">

	<div class="col-md-4">
        <form  action="{{url('eci/ac-wise-report')}}" method="GET" >
			<input type="hidden" name="state_code" value="{{$data['state']}}">
			<input type="hidden" name="pdf" value="yes">
            <button type="submit" class="btn btn-danger">Export Pdf</button>
        </form>
</div>
	</div>

		  </div>
</section>

<div class="container-fluid" >
<div class="row">
	<div  class="col mt-2">
		<div style="text-align:center;font-weight:bold;font-size:22px;"> AC Wise Result Report - @if($st_code) {{(getstatebystatecode($st_code))->ST_NAME}} @endif</div>

		<table id="list-table"  class="table table-striped table-bordered datatable ">
<thead>	
		<tr class="sticky-header">
        <th style="background:#f0587e;color:black;"> S.No </th>		
        <th style="background:#f0587e;color:black;">AC No.</th>
		<th style="background:#f0587e;color:black;">AC Name</th>
		<th style="background:#f0587e;color:black;">Counting status (Rounds Completed / Total)</th>
		<th style="background:#f0587e;color:black;">Total Polling Station</th>
		<th style="background:#f0587e;color:black;">Total Counted Polling Station</th>
		<th style="background:#f0587e;color:black;">Pending Polling Station </th>
		<th style="background:#f0587e;color:black;">Total Votes</th>
		<th style="background:#f0587e;color:black;">Counted Votes</th>
		<th style="background:#f0587e;color:black;">Pending Votes</th>
		</tr>
 </thead>
		
		<tbody style="text-align: center;">
		@if($result)
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$status='';
		
		$scheduled=$data->scheduled_round;
		$completedRound=completeRound($data->st_code,$data->ac_no);
				
		
		if($scheduled==0 || $scheduled == null){
			$status='Rounds Not Scheduled';	
		}else if($scheduled == $completedRound){
			$status='Completed';			
		}else{
			$status = ''.$completedRound.' / '.$scheduled.'';			
		}
	
		?>
        <tr>
        <td>{{$i}}</td> 
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>		
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		
		
		<td>@if(isset($data->total_ps_count) && (!empty($data->total_ps_count))){{$data->total_ps_count}}@else{{'0'}}@endif</td>
		<td>@if(isset($data->total_ps_counting) && (!empty($data->total_ps_counting))){{$data->total_ps_counting}}@else{{'0'}}@endif</td>
		<td>@if(isset($data->pending_counting_ps) && (!empty($data->pending_counting_ps))){{$data->pending_counting_ps}}@else{{'0'}}@endif</td>

		<td>@if(isset($data->total_voters) && (!empty($data->total_voters))){{$data->total_voters}}@else{{'0'}}@endif</td>
		<td>@if(isset($data->total_counted_votes) && (!empty($data->total_counted_votes))){{$data->total_counted_votes}}@else{{'0'}}@endif</td>
		<td>@if(isset($data->pending_votes) && (!empty($data->pending_votes))){{$data->pending_votes}}@else{{'0'}}@endif</td>
		
		</tr>

		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="11">  No record available </td> 
		</tr>
		@endif
       </tbody></table>
	</div>
</div>
 </div>

<script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.stickytable.min.js') }}"></script>
<script>
  function getACList(state){	 
        jQuery.ajax({
          type: "GET",
          url: "<?php echo url('/'); ?>/eci/counting/boothstate-by-ac/"+encodeURI(state),
          dataType: "html",
          success: function (response) { 		 
          jQuery("#show_ac_list").show();   
          jQuery("#ac_no").hide();   
          jQuery('#show_ac_list').html(response);	
          },
          error: function (xhr, ajaxOptions, thrownError) {
          alert(thrownError);
          }
      });
  }

	</script>
@endsection




