@extends('admin.layouts.themenew')
@section('title', 'Create Schedule')
@section('content') 
@include('admin.includes.script')

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
   <div class="child-area">
   <div class="nw-crte-usr newschedule">
		<div class="row">
		    <div class="profileSteps">
				  <div class="profileStep profileStepActive profileStepOne">
					<div class="connect"></div>
					<div class="icon">Finalize Election Listing</div>
				  </div>
				  <div class="profileStep profileStepPassive profileStepOne">
					<div class="connect"></div>
					<div class="icon">Add Schedule Details</div>
				  </div>
				  <div class="profileStep profileStepPassive profileStepOne">
					<div class="icon">Announce Election</div>
				  </div>
			</div>
		</div>
          
		<div class="row">
			<div class="col-sm-12">
			<?php $electionData = getElectionbyElecID($ELECTION_TYPEID) ?>
			<strong>Election Commission announces dates for 2018 {{$electionData[0]->election_name}}({{$electionData[0]->election_sort_name}}) {{$electionData[0]->election_type}} Election in {{count($list_schedule)}} Schedules</strong>
				<table class="table">
				  <thead class="thead-light">
					<tr>
					  <th scope="col">State</th>
					  <th scope="col" width="17%">Date of issue of Gazette Notification</th>
					  <th scope="col" width="17%">Last Date of Nominations</th>
					  <th scope="col" width="17%">Date of Scrutiny of Notifications</th>
					  <th scope="col" width="17%">Last Date of Withdrawal of candidatures</th>
					  <th scope="col" width="17%">Date of Poll</th>
					  <th scope="col">Total Acs</th>
					</tr>
				  </thead>
				  <tbody>
					<?php 
						  $announcementDate = ''; 
						  $countingDate = ''; 
						  $electionCompletionDate = ''; 
						  //print_r($elecschdata);
					?>
					@foreach($list_schedule as $listData)
						<?php 
							$announcementDate = $listData->DT_PRESS_ANNC; 
						    $countingDate = $listData->DATE_COUNT; 
						    $electionCompletionDate = $listData->DTB_EL_COM;
							
								
							$data = totalacs($listData->ELECTION_TYPEID,$listData->SCHEDULEID,$listData->ST_CODE);
							$countschedule = statetotalround($listData->ELECTION_TYPEID,$listData->ST_CODE) ;
							
						?>
						<tr>
							<?php $getStateDetail = getStatebyId($listData->ST_CODE);
							$statename = '' ;
							?>
							<?php $stcode = $listData->ST_CODE; ?>
							@if(!empty($getStateDetail->ST_NAME))
								<td>{{$getStateDetail->ST_NAME}} <br/>
								@if($countschedule > 1)
									<strong>Phase {{$listData->SCHEDULENO}}</strong>
								@endif
							@else
								<td>{{$statename}}</td>
							@endif
							<td>{{date("d-m-Y",strtotime($listData->DT_ISS_NOM))}}</td>
							<td>{{date("d-m-Y",strtotime($listData->LDT_IS_NOM))}}</td>
							<td>{{date("d-m-Y",strtotime($listData->DT_SCR_NOM))}}</td>
							<td>{{date("d-m-Y",strtotime($listData->LDT_WD_CAN))}}</td>
							<td>{{date("d-m-Y",strtotime($listData->DATE_POLL))}}</td>
							<td>{{count($data)}}
							@if(($listData->ELECTION_TYPEID == 1) ||  ($listData->ELECTION_TYPEID == 2))
								PCs
							@elseif(($listData->ELECTION_TYPEID == 3) ||  ($listData->ELECTION_TYPEID == 4))
								Acs
							@endif
							</td>
						</tr>
					@endforeach
				  </tbody>
				</table>
			</div>
		</div>
    </div><!-- End Of nw-crte-usr Div -->
   
       <!--    Listing -->


          </div>
           
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
      
    </div><!-- End OF page-contant Div -->


       <!-- end list-->
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 
@endsection

@section('script')

@endsection