
<!-- New Here -->
@php
	$maxRound=0
@endphp
@php
	$maxRound = app(App\Http\Controllers\Admin\RoundWiseReport\RoundWiseReportController::class)->getMaxRound($st_code, $ac)
@endphp
<!-- End New Here -->

<div class="table-responsive">
<table id="example"  class="table table-striped table-bordered datatable">
<thead style="text-align: center;">
       <tr>
        <th> S.No </th>
		<th>AC Name (No.)</th>
        <th>AC No.</th>
		<th style="text-align: left;">Candidate(Party)</th>

			
			
			<?php $b=0; if($maxRound == 0 ){ ?>
			<th>Round Wise EVM Votes
			<?php } ?>
			<?php  for($m=1; $m<=$maxRound; $m++){   ?>
			<th> <span> <?php  echo 'R'.$m; ?> </span></th>
			<?php   }   ?>
			<?php if($maxRound == 0 ){ ?>
			</th>	
			<?php } ?>

			
			
		<!--<th style="text-align: center;">Postal Votes</th>-->
        <th style="text-align: center;">Total Votes</th>
       </tr>
 </thead>
 
        <tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$mData = (array)$data; 
		?>
		@php
		$getAc = app(App\Http\Controllers\Admin\RoundWiseReport\RoundWiseReportController::class)->getAc($st_code, $mData['ac_no'])
		@endphp
        <tr>
        <td>  <span>{{$i}}</span>  </td> 
		<td>{{$getAc}}({{$mData['ac_no']}})</td>
		<td>{{$mData['ac_no']}}</td>
		 <td style="text-align: left;">  <span>{{$mData['candidate_name']}} ({{$mData['party_name']}}) </span>  </a>  </td> 
		
					
								<?php 	
								$total_votes=0; $p=0; $remain=0; $isRound=0;
								for($k=1; $k<=$maxRound; $k++){  
											$dataok = 'round'.$k;
											 $p++; $isRound++;
											?>
												<td> <span>
													<?php  
														echo $mData[$dataok];	
													    $total_votes=$total_votes + $mData[$dataok];
													?> 
												</span></td>								
								<?php  }  
								if(($isRound==0)){ ?>
								<td> <span>
									NA
								</span></td>
								<?php  }  ?>
				

			
			<!--<td >  <span>34948</span>  </td> -->
			<td >  <span>{{$total_votes}}</span>  </td> 
		</tr>
		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="7">  No record available </td> 
		</tr>
		@endif
       </tbody></table>
     </div>

<link rel="stylesheet" href="{{asset('css/dataTables.bootstrap.min.css')}}">
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>

<script type="text/javascript">

$(document).ready( function () {
    $('.datatable').DataTable();
} );
</script>
     
