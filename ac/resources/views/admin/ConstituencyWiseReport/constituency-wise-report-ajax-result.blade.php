<style>
table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
    padding-right: 30px;
    color: black;
}

::-webkit-scrollbar {
    width: -9px;
    background: transparent;
}

::-webkit-scrollbar {
    width: 6px;
    background: transparent;
}
</style>


<table id="list-table"  class="table table-striped table-bordered datatable  ">
<thead>	
		<tr class="sticky-header">
        <th> S.No </th>
		@if($user=='ECI')
		<th>State Name</th>
		@endif
		@if($user=='ECI' or $user=='CEO' or $user=='DEO')
		<th>AC Name</th>
        <th>AC No.</th>
	    @endif	
		<th>Leading  Party</th>
		<th>Leading Candidate</th>
		<th style="background:antiquewhite;">Margin</th>
		@if($result_type!=1)
		<th style="background:burlywood;">Trailing Party</th>
		<th style="background:burlywood;">Trailing Candidate</th>
		@endif	
		<th>Result status </th>
		</tr>
 </thead>
		
		<tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$status='';
		if($data->status==1){
		$status='Result Declared';	
		}
		if($data->status=='0'){
		$status='Result In Progress';	
		
		}
	
		?>
        <tr>
        <td>{{$i}}</td> 
		@if($user=='ECI')
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		@endif
		@if($user=='ECI' or $user=='CEO' or $user=='DEO')
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		@endif
		<td style="text-align:left;">
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td style="text-align:left;">
		@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
			@if($data->status=='1' && $data->margin!='0')<span style="color:green;">({{'WINNER'}})</span>@endif
		@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:antiquewhite;">@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		@if($result_type!=1)
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		@endif
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		</tr>
		
		
		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="7">  No record available </td> 
		</tr>
		@endif
       </tbody></table>
   




<script type="text/javascript">

$(document).ready( function () {
    $('.datatable').DataTable();
} );
</script>
  
<script type="text/javascript">
if($(window).width() < 767)
{
   $(document).ready(function(){
 $('.table').wrap('<div class="table-responsive"></div>');
});
} else {
   $(document).ready(function(){
 $('.table').wrap('<div class="sticky-table sticky-ltr-cells "></div>');
});
}
</script>