    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Counting Status</title>
       
    </head>
	
	  <style type="text/css">
.table{width: 100%; border-collapse: collapse;  font-family: Verdana; margin: auto; color: #000;}
tr.declaredbg {background-color: #e5fbe3;}
tr.progressbg {background-color: #f9efe0;}
</style>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br>
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
	  

        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>List Of Counting Status</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <!--<tr>  
                           <td><strong>Phase:</strong>   aa</td>
                         </tr>
                          <tr>  
                           <td><strong>Assembly:</strong> SNAME</td>
                         </tr>  --> 
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
                       <!-- <tr>  
                           <td align="right"><strong>Phase Starts:</strong> </td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Phase Ends:</strong>  </td>
                         </tr>  -->
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>
        <br><br>
		
		 <table  id="list-table"  class="table" border="1" cellpadding="5">
        <thead>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
          <th style="background-color: #54c752; color: #000;">Total AC's</th>
          <!--<th>AC's Scheduled the Round</th>
          <th>AC's Not Scheduled Round</th>
          <th>Total Round Scheduled</th>
          <th>Total Round Completed</th>
          <th>Counting Started</th> -->
		  <th style="background-color: #ecb241; color: #000;"> ACs Where Rounds Are Pending</th>	  
          <th style="background-color: #3accae; color: #000;">ACs Where Result Declaration Is Pending</th> 
          <th style="background-color: #d7d7d7;">ACs Where Result Is Declared</th>
          <!--<th>Percentage</th> -->
        </tr>
        </thead>
        <tbody>
        @php  
		$count = 1; 
		$TotalAc= 0;
        $TotalCountingStarted = 0;
        $TotalDeclared = 0;
        $total_round_pending = 0;
		@endphp
         @forelse ($EciCountingStatusReport as $key=>$listdata)
		 
		 @php 

         $TotalAc              += $listdata->TOTAL_AC;
         $TotalCountingStarted += $listdata->COUNTING_STARTED;
         $TotalDeclared        += $listdata->RESULT_DECLARED;
         $total_round_pending        += $listdata->total_round_pending;


         @endphp
          <tr>
            <td>{{ $count }}</td>
            <td>{{ $listdata->ST_NAME }}</td>
            <td align='center' style=' background-color: #c3e9c0;'> @if($listdata->TOTAL_AC =='' )     0  @else  {{ $listdata->TOTAL_AC }} @endif</td>
			
             <?php /*<td> @if($listdata->not_scheduled_round =='' )   0  @else {{ ($listdata->TOTAL_AC - $listdata->not_scheduled_round) }} @endif</td>
            <td>@if(@$listdata->not_scheduled_round =='' )     0  @else  {{ @$listdata->not_scheduled_round }} @endif</td>
            <td>@if($listdata->total_round =='' )     0  @else  {{ $listdata->total_round }} @endif</td>
            <td>@if(@$listdata->total_round_completed =='' )     0  @else  {{ @$listdata->total_round_completed }} @endif</td>
			
            <td> @if($listdata->COUNTING_STARTED =='' )   0  @else {{ $listdata->COUNTING_STARTED }} @endif</td>*/?>
			<td align='center' style='background-color: #f8d79e;'> @if($listdata->total_round_pending =='' )   0  @else {{ $listdata->total_round_pending }} @endif</td>
			  <td align='center' style='background-color: #a5edde; color: #000;'> @if($listdata->NOT_DECLARED =='' )   0  @else {{$listdata->NOT_DECLARED}} @endif</td>
            <td  style='background-color: #f9efe0;' align='center'> @if($listdata->RESULT_DECLARED =='' )   0  @else {{ $listdata->RESULT_DECLARED }} @endif</td>
			
          
            <?php /*<td> @if($listdata->PERCENTAGE =='' )   0  @else {{ $listdata->PERCENTAGE }} @endif</td> */?>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Counting Status</td>                 
              </tr>
          @endforelse
		  <tr>
	  <td colspan="2" style='color:#d53858; background-color: #fce0e6; font-weight: bold;'  align='left'><b>Total</b></td>
	  <td style='color:#000; background-color: #54c752;' align='center'><b>{{$TotalAc}}</b></td>
	  <?php /*<td><b></b></td>
	  <td><b></b></td>
	  <td><b></b></td>
	  <td><b></b></td>
	  <td><b>{{$TotalCountingStarted}}</b></td>*/?>
	  <td style='color:#000; background-color: #ecb241;' align='center'><b>{{$total_round_pending}}</b></td>
	  
	  <td style='color:#000; background-color: #3accae; color: #000;' align='center'><b>{{($TotalAc-$TotalDeclared)}}</b></td>
	  <td style=" color:#000; background-color: #d7d7d7;" align='center'><b>{{$TotalDeclared}}</b></td>
	  <?php /* <td><b>@if($TotalAc!=0){{ROUND(($TotalDeclared/$TotalAc*100),2)}}@else 0 @endif %</b></td>*/?>
	  </tr>
        </tbody>
    </table>
	
	
	<br><br>
	<h2 style="text-align: center;">Top 10 AC's with rounds Pending</h2> 
	
	<br>
	<table id="list-table"  class="table" border="1" cellpadding="5">
		<thead>	
				<tr style="background-color: #d7d7d7;">
					<th style=" color:#000;">S.No</th>
					<th style=" color:#000;">State Name</th>
					<th style=" color:#000;">AC No.</th>
					<th style=" color:#000;">AC Name</th>
					<th style=" color:#000;">Pending Round</th>
					<!--<th style=" color:#000;">Total Round</th>-->
				</tr>
		 </thead>
		
		
		<tbody style="text-align: center;">
		@if(count($max_round_pending) > 0 )
		@php $i=1 @endphp
		@foreach($max_round_pending as  $data)
	
        <tr class="declaredbg">
        <td>{{$i}}</td> 
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		
		<td style="text-align:left;">@if(isset($data->pendinground) && (!empty($data->pendinground))){{$data->pendinground}}@else{{'0'}}@endif</td>
		<!--<td style="text-align:left;">@if(isset($data->scheduled_round) && (!empty($data->scheduled_round))){{$data->scheduled_round}}@else{{'0'}}@endif</td>-->
		</tr>

		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="11">  No record available </td> 
		</tr>
		@endif
       </tbody>
	 </table>
	
	<div style="page-break-after:always"></div>
	
	<h2 style="text-align: center;">Constituency Wise Result In Progress</h2>
	<br>
	<table id="list-table"  class="table" border="1" cellpadding="5">
		<thead>	
				<tr style="background-color: #d7d7d7;">
					<th style=" color:#000;">S.No</th>
					<th style=" color:#000;">State Name</th>
					<th style=" color:#000;">AC Name</th>
					<th style=" color:#000;">AC No.</th>
					<th style=" color:#000;">Leading/Won  Party</th>
					<th style=" color:#000;">Leading/Won Candidate</th>
					<th style=" color:#000;">Margin</th>
					<th style=" color:#000;">Trailing Party</th>
					<th style=" color:#000;">Trailing Candidate</th>
					<th style=" color:#000;">Result status </th>
				</tr>
		 </thead>
		
		
		<tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$status='';
		if(@$data->status==1){
		$status='Result Declared';
		$class = 'declaredbg';
		}
		if(@$data->status=='0'){
		$status='Result In Progress';	
		$class = 'progressbg';
		}
	
		?>
        <tr class="{{$class}}">
        <td>{{$i}}</td> 
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td style="text-align:left;">
		@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
			@if($data->status=='1' && $data->margin!='0')<span>({{'WINNER'}})</span>@endif
		@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		</tr>

		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="11">  No record available </td> 
		</tr>
		@endif
       </tbody>
	 </table>
	
		
		
		
		
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>