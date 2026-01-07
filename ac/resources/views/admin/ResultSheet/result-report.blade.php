@extends('admin.layouts.ac.dashboard-theme')
@section('content')

<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/jquery.stickytable.min.css') }}">

<div class="loader" style="display:none;"></div>
<section class="dashboard-header section-padding">
    <div class="container-fluid">
		<div class="row" >
		
		 <div class="col-md-10">
		 
        <form  action="{{url('eci/result-report')}}" class="row" method="POST" >
		{{csrf_field()}}
            <div class="form-group col-md-2">
                <label>Select State</label>
                <select name="state_code" id="state_code" class="form-control"  onchange="getACList(this.value);">
                    <option value="">Select State</option>
					<option value="0" @if(isset($data['state']) && $data['state']==0 && $data['state']!="") selected @endif>Select All</option>
					@foreach($data['m_state'] as $raw)
                    <option value="{{$raw->ST_CODE}}" @if(isset($data['state']) && $data['state']==$raw->ST_CODE) selected @endif>{{$raw->ST_NAME}} </option>
					@endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Select AC</label>
                    @if(isset($data['state']) && $data['state']!="")
				    <select name="ac_no" id="ac_no" class="form-control">
					   <?php echo getAcListDropdown($data['state'],$data['ac_no']) ?>
					</select>
				     @else
				     <select name="acno" id="ac_no" class="form-control">
						<option value="">Select AC</option> 
					</select>
				     @endif
                </select>
				<div id="show_ac_list" style="display:inline"></div> 
            </div>
			
			<div class="form-group col-md-3">
                <label>Select Party</label>
                <select name="party" id="party" class="form-control">
                    <option value="">Select Party</option>
					@foreach($data['party_data'] as $raw)
                    <option value="{{$raw->party}}" @if(isset($data['party']) && $data['party']==$raw->party) selected @endif>{{$raw->party_name}} </option>
					@endforeach
                </select>
            </div>
			
			<div class="form-group col-md-3">
                <label>Select Party Type</label>
                <select name="party_type" id="party_type" class="form-control">
                    <option value="">Select Party Type</option>					
                    <option value="1" @if(isset($data['party_type']) && $data['party_type']=='1') selected @endif>Leading Party</option>					
                    <option value="2" @if(isset($data['party_type']) && $data['party_type']=='2') selected @endif>Trailing Party</option>					
                </select>
            </div>
			
			 <div class="form-group col-md-1">
                <button type="submit" name="search" id="submit-report" class="btn btn-success" style="margin-top:31px;">Search</button>
            </div>
        </form>
  </div>
  
  <div class="col-md-1">
  	<form  action="{{url('eci/result-report')}}" method="GET" >
			<input type="hidden" name="state_code" value="{{$data['state']}}">
			<input type="hidden" name="ac_no" value="{{$data['ac_no']}}">
			<input type="hidden" name="party" value="{{$data['party']}}">
			<input type="hidden" name="party_type" value="{{$data['party_type']}}">
			<input type="hidden" name="excel" value="yes">			
			<button type="submit" class="btn btn-danger">Export Excel</button>
        </form>
	</div>	
	<div class="col-md-1">
        <form  action="{{url('eci/result-report')}}" method="GET" >
			<input type="hidden" name="state_code" value="{{$data['state']}}">
			<input type="hidden" name="ac_no" value="{{$data['ac_no']}}">
			<input type="hidden" name="party" value="{{$data['party']}}">
			<input type="hidden" name="party_type" value="{{$data['party_type']}}">
			<input type="hidden" name="pdf" value="yes">
            <button type="submit" class="btn btn-success">Export Pdf</button>
        </form>
</div>
	</div>

		  </div>
</section>

<div class="container-fluid" id="DivIdToPrint">
<div class="row">
	<div  class="col mt-2">
		<div style="text-align:center;font-weight:bold;font-size:22px;"> TRENDS & RESULT 2023</div>

		<table id="list-table"  class="table table-striped table-bordered datatable  ">
<thead>	
		<tr class="sticky-header">
        <th style="background:#f0587e;color:black;"> S.No </th>
		<th style="background:#f0587e;color:black;">State Name</th>
		<th style="background:#f0587e;color:black;">AC Name</th>
        <th style="background:#f0587e;color:black;">AC No.</th>
		<th style="background:#f0587e;color:black;">Leading  Party</th>
		<th style="background:#f0587e;color:black;">Leading Candidate</th>
		<th style="background:#f0587e;color:black;">Trailing Party</th>
		<th style="background:#f0587e;color:black;">Trailing Candidate</th>
		<th style="background:#f0587e;color:black;">Margin</th>
		<th style="background:#f0587e;color:black;">Counting status (Rounds Completed / Total)</th>
		</tr>
 </thead>
		
		<tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$status='';
		
		$scheduled=$data->scheduled_round;
		$completedRound=completeRound($data->st_code,$data->ac_no);
				
		if($data->status==1){
			$status='Result declared';		
		}else if($scheduled==0){
			$status='Rounds Not Scheduled';	
		}else if($scheduled == $completedRound){
			$status='Completed';			
		}else{
			$status = ''.$completedRound.' / '.$scheduled.'';			
		}
	
		?>
        <tr>
        <td>{{$i}}</td> 
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td style="text-align:left;">
		@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
			@if($data->status=='1' && $data->margin!='0')<span style="color:green;">({{'WINNER'}})</span>@endif
		@else{{'NA'}}@endif</td>
		
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:antiquewhite;">@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
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




