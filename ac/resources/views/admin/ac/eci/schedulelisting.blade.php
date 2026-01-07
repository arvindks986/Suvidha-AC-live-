@extends('admin.layouts.themenew')
@section('title', 'Schedule Listing')
@section('content') 
@include('admin.includes.script')
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
   
   
<!--    Listing -->

<div class="page-contant">
      
            
      <!-- Start Of Page Sub Setion Div --> 
       <div class="page-sub-setion"> 
      <!-- Start Of Intra section Div -->
          <div class="intra-section">
          <div class="row">
		   
	      </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
           <!-- Start Table Here -->  
            <div class="table-responsive">
			  
			  <div class="announcedbox"> 
					<?php $i=0; ?>
						@foreach($list_schedule as $list)
							<?php $i++; $modulo =($i % 3)?>

							@if($modulo==0)
								<?php $bg_color = '#e0ffff' ;?>
							@elseif($modulo==1)
								<?php $bg_color = '#f5f5db' ;?>
							@elseif($modulo==2)
								<?php	$bg_color = '#ffe4e1'; ?>
							@endif
							<div class="announcedelec electionbox" id="{{'electionbox-'.$list->ELECTION_TYPEID}}" style="background-color:{{$bg_color}}">
								 <div class="announceddetails">
									<?php $eleTypeData = getElectionbyElecID($list->ELECTION_TYPEID) ;?>
									<?php $schListingByElectionId = schListingByElectionId($list->ELECTION_TYPEID,$list->DT_PRESS_ANNC) ; ?>
									<div class="detaildata">
										<strong>Election Commission announces dates for 2018 {{$eleTypeData[0]->election_name}}({{$eleTypeData[0]->election_sort_name}}) -  {{$eleTypeData[0]->election_type}} Election in 
										@if(count($schListingByElectionId) >= 36) All @else {{count($schListingByElectionId)}} @endif States are
											@if(count($schListingByElectionId) < 35)
												<?php $elecstname = array() ; ?>
												@foreach($schListingByElectionId as $elecData)
												
													<?php $getstatedetail = getStatebyId($elecData->ST_CODE);?>			
													@if(!empty($getstatedetail->ST_NAME))
														<?php $elecstname[] = $getstatedetail->ST_NAME ;?>
													@else
														<?php $elecstname[] = '' ; ?>
													@endif
												@endforeach
												{{implode(', ' , $elecstname)}}
												
											@endif
										</strong>
									</div>
											
									<div class="announcedheader"> Press Announcement Date: {{ date("d-m-Y",strtotime($list->DT_PRESS_ANNC)) }} </div>
									<div class="announcedbody"> 
										<div class="detaildata"> Election Completion Date: {{ date("d-m-Y",strtotime($list->DTB_EL_COM)) }}</div> 
										<div class="detaildata"> Date of counting: {{ date("d-m-Y",strtotime($list->DATE_COUNT)) }}</div>
										<?php
											$polldate = new DateTime(date("Y-m-d",strtotime($list->DATE_POLL)));
											$curdate = new DateTime(Date('Y-m-d'));
										?>
										@if($list->finalize_sch == 0 )
											<a href="{{url('eci/modifyschedule/'.$list->ELECTION_TYPEID)}}" class="btn btn-default btn-sm addschedulebtn">Modify Schedule</a>
											
											<a href="{{url('eci/electiondetailsnew/'.$list->ELECTION_TYPEID)}}" class="btn btn-default btn-sm addschedulebtn">Modify Election Details</a>
										@else
											<a href="{{url('eci/electionlisting/'.$list->ELECTION_TYPEID)}}" class="btn btn-default btn-sm addschedulebtn">Election Listing</a>
										@endif
									</div>
								 </div>
							</div>
							
						@endforeach 
            </div><!-- End Of table-responsive Div -->
          </div>
           
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
      
    </div><!-- End OF page-contant Div -->


       <!-- end list-->
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 
<script language="javascript">
/*function showadd()
  {
  document.getElementById('add_menu').style.display="block";
  document.getElementById('btnadd').style.display="none";
  document.getElementById('btncancel').style.display="block";
  }
function canceladd()
  { 
  document.getElementById('add_menu').style.display="none";
  document.getElementById('btncancel').style.display="none";
  document.getElementById('btnadd').style.display="block";
  }*/
</script>
@endsection