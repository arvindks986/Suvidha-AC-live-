@extends('admin.central.common.theme')
@section('title', 'Descriptive Election Period Report')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => Common::generate_url('mis/list-exgratia'),
    'name' => 'List Ex-Gratia'
  ]; 
  ?>
@section('content')

<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
    <link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
    <link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
    <link rel="stylesheet" href="{{ asset('appoinment/css/ex-custom-dark.css') }} " type="text/css">
    <link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
    <link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
<style>	

.bolds{
	font-weight: bold;
}
</style>
<section class="">
  <div class="container-fluid">
    <div class="row">
	  <div class="card card-shadow mt-4">
		<div class="card-header">
          <div class="row">
            <div class="col"><h4>Ex-Gratia List @if(session()->has('success_msg'))<div class="alert alert-success alert-dismissible">{{ session()->get('success_msg') }}</div>@endif</div>
            <div class="col">
            <p class="mb-0 text-right">
              <a href="{{Common::generate_url('mis/report-exgratia')}}" class="btn btn-warning">Ex-Gratia Detailed Report</a>
			  <a href="{{Common::generate_url('mis/exgratia-count-report')}}" class="btn btn-primary">Ex-Gratia Count Report</a>
            </p>
          </div>
        </div>
		<div>&nbsp;</div>
		
		<div class="row" style="width: 53%;margin-left: 20%;">
            <div class="col-sm-12">
            <p class="mb-0 text-right">
			  <fieldset>
			  <div class="p-2">
				  <a href="{{Common::generate_url('mis/add-exgratia')}}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;Add New Case</a>
				  <a>Or</a>
				  <a>&nbsp;</a>
				  @if(!empty($noncases_data))
					  <input type="checkbox" `class="checkbox" name="nocases" value="1" @if($noncases_data->nocases==1)checked @endif  id="nocases">&nbsp;&nbsp; No pending cases
				  @else
					  <input type="checkbox" `class="checkbox" name="nocases" value="1"  id="nocases">&nbsp;&nbsp; No pending cases
				  @endif
				  
				  <a>&nbsp;</a>
				  @php 
					$remarks = '';
					$mr="";
					if(!empty($noncases_data)){
						$mr="250px;";
						$remarks = $noncases_data->remarks;
					}
				  @endphp
				  
				  <input type="text" name="remarks" id="remarks" placeholder="Enter remarks" value="{{$remarks}}">
				  <button class="btn btn-secondary" type="button" id="update_noncases">&nbsp;Update</button>
				  @if(!empty($noncases_data))
					  <br>
					  @if(!empty($noncases_data->record_date))
					  <b>Updated Date:</b> {{date('d-M-Y',strtotime($noncases_data->record_date))}}
					  @endif
				  @endif
				  
			  </div>
			  <a id="errorMsg" class="red">&nbsp;</a>
			  <a id="successMsg" class="green">&nbsp;</a>
			  </fieldset>
			  
            </p>
          </div>
        </div>
		
      </div>
	  
              <div class="card-body p-2">
                <!-- Tab panes -->
                <div class="tab-content">
                  <div id="submtd" class="tab-pane active">
                    <div class="tab-body tab-panel-bg">
                      <div class="inner-nav-custom">
                        <ul class="nav nav-tabs">
                          <li class="nav-item pl-2">
                            <a class="nav-link d-flex align-items-center justify-content-around apld active" data-toggle="tab" href="#apld">
                              <div><i class="fa fa-user-circle-o" aria-hidden="true"></i>
                              </div>
                              <div>All Cases</div>
                              <div><span>@if(!empty($allcases)){{count($allcases)}}@endif</span>
                              </div>
                            </a>
                          </li>
                          <li class="nav-item pl-2">
                            <a class="nav-link d-flex align-items-center justify-content-around acptd" data-toggle="tab" href="#acptd">
                              <div><i class="fa fa-check" aria-hidden="true"></i>
                              </div>
                              <div>Granted</div>
                              <div><span>@if(!empty($garantedcases)){{count($garantedcases)}}@endif</span>
                              </div>
                            </a>
                          </li>
                          <li class="nav-item pl-2">
                            <a class="nav-link d-flex align-items-center justify-content-around rejetd" data-toggle="tab" href="#rejctd">
                              <div><i class="fa fa-times-circle-o" aria-hidden="true"></i> 
                              </div>
                              <div>Rejected</div>
                              <div><span>@if(!empty($rejectedcases)){{count($rejectedcases)}}@endif</span>
                              </div>
                            </a>
                          </li>
                          <li class="nav-item pl-2">
                            <a class="nav-link d-flex align-items-center justify-content-around pendg" data-toggle="tab" href="#pndg">
                              <div><i class="fa fa-clock-o" aria-hidden="true"></i>
                              </div>
                              <div>Pending</div>
                              <div><span>@if(!empty($pendingcases)){{count($pendingcases)}}@endif</span>
                              </div>
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div class="tab-content custom-tab-content">
                        <div id="apld" class="tab-pane active">
							@if(count($allcases)>0)
							<table class="table table-borderless mediaTable">
							 <tbody>
							 
							     @php $status_class='stats-pending';$color_class='#FFCA36'; $count = 1;@endphp  @foreach	 ($allcases as $key=>$listdata)
                          
							  @php 
							  if($listdata->application_status =='granted'){ $status_class = 'stats-accepted';$color_class='green';}
							  if($listdata->application_status =='pending'){ $status_class = 'stats-pending';$color_class='#FFCA36';}
							  if($listdata->application_status =='rejected'){ $status_class = 'stats-rejected';$color_class='red';}

							  @endphp
							  
								<tr>
							   	<td align="center">{{ $count }}.</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								   State/UT:
									<h5>{{$listdata->ST_NAME}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Election:
									<h5>{{(!empty($listdata->election_type))?$elections[$listdata->election_type]:''}} - {{$listdata->election_year}}</h5>  
								  </div>
								  <div class="tdBox">
								    District:
									<h5>{{$listdata->DIST_NAME}}</h5>  
								  </div>
								  <div class="tdBox">
								    @if($listdata->election_type==1 || $listdata->election_type==2)AC @else PC @endif:
									@php 
										$ac_val = getacname($listdata->st_code,$listdata->ac_no);
										$pc_val = getpcbypcno($listdata->st_code,$listdata->pc_no);
									@endphp
									@if($listdata->election_type==1 || $listdata->election_type==2)
									<h5>@if(!empty($ac_val)){{$ac_val->AC_NAME}}@endif</h5> 
									@else
									<h5>@if(!empty($pc_val)){{$pc_val->PC_NAME}}@endif</h5> 	
									@endif
									
								  </div>
								</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								    Polling Personnel Name:
									<h5>{{$listdata->applicant_name}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Designation
									<h5>{{$listdata->applicant_designation}}</h5>  
								  </div>
								  <div class="tdBox">
								    Parent Department
									<h5>{{$listdata->applicant_parent_department}}</h5>  
								  </div>
								  <div class="tdBox">
								    Address:
									<h5>{{$listdata->applicant_address}}</h5>  
								  </div>
								</td>
								<td>
								  <div class="tdBox mb-3 pt-2">
								    Contact No:
									<h5>{{$listdata->contact_no}}</h5>  
								  </div> 
								  <div class="tdBox mb-3 pt-2">
								    Applied Date:
									<h5>{{date('d-M-Y',strtotime($listdata->created_at))}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Injury Details:
									<h5 >{{(!empty($listdata->injury_details))?$injury[$listdata->injury_details]:''}}</h5>  
								  </div>
								  <div class="tdBox">
								    Reason of injury/Death:
									<h5 >{{(!empty($listdata->accident_reason))?$reason[$listdata->accident_reason]:''}}</h5>  
								  </div>
								</td>
							   	<td>
								 <div class="tdBox mb-3 pt-2">
								     Place of injury/Death:
									<h5>{{$listdata->accident_place}}</h5>  									
								  </div>  
								   <div class="tdBox">
								    Date of injury/Death:
									<h5>@if(!empty($listdata->accident_date)){{date('d-M-Y',strtotime($listdata->accident_date))}}@endif</h5>  
								  </div>
								  @if($listdata->application_status=='granted')
								  <div class="tdBox">
								    Date Of Payment:
									<h5>@if(!empty($listdata->date_of_payment)){{date('d-M-Y',strtotime($listdata->date_of_payment))}}@endif</h5>   
								  </div>
								  <div class="tdBox">
								    Payment Amount:
									<h5>{{$listdata->payment_amount}}</h5>  
								  </div>
								  @endif
								  @if($listdata->application_status=='pending')
									<div class="tdBox">
								    Pending Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								  @if($listdata->application_status=='rejected')
									<div class="tdBox">
								    Rejection Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								</td>
								<td class="pr-0">
								   <div class="{{$status_class}} pt-5">
								    Application Status:
									<h5>{{ucfirst($listdata->application_status)}}</h5>  
								  </div>  
								</td>
								
							   	<td>
								 <a href="{{url('acceo/mis/edit-exgratia/'.encrypt($listdata->id))}}">Edit</a>   
								</td>
							   </tr> 
							  <tr><td colspan="7" class="td-Blank">&nbsp;</td></tr>
								@php $count++; @endphp
							  @endforeach
							 </tbody>
						   </table>
						   @else
							   <div class="tableWrap"><table class="table table-borderless tableLess"><tbody><tr><td>No Data Found</td></tbody></table></div>
						   @endif
						                           </div>
                        <!-- End Of apld Div -->
                        <div id="acptd" class="tab-pane">
						@if(count($garantedcases)>0)
							<table class="table table-borderless mediaTable">
							 <tbody>
                                                      @php $status_class='stats-pending';$color_class='#FFCA36'; $count = 1;@endphp  @foreach	 ($garantedcases as $key=>$listdata)
                          
							  @php 
							   $status_class = 'stats-accepted';$color_class='green';

							  @endphp
							  
								<tr>
							   	<td align="center">{{ $count }}.</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								   State/UT:
									<h5>{{$listdata->ST_NAME}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Election:
									<h5>{{(!empty($listdata->election_type))?$elections[$listdata->election_type]:''}} - {{$listdata->election_year}}</h5>  
								  </div>
								  <div class="tdBox">
								    District:
									<h5>{{$listdata->DIST_NAME}}</h5>  
								  </div>
								  <div class="tdBox">
								    @if($listdata->election_type==1 || $listdata->election_type==2)AC @else PC @endif:
									@php 
										$ac_val = getacname($listdata->st_code,$listdata->ac_no);
										$pc_val = getpcbypcno($listdata->st_code,$listdata->pc_no);
									@endphp
									@if($listdata->election_type==1 || $listdata->election_type==2)
									<h5>@if(!empty($ac_val)){{$ac_val->AC_NAME}}@endif</h5> 
									@else
									<h5>@if(!empty($pc_val)){{$pc_val->PC_NAME}}@endif</h5> 	
									@endif  
								  </div>
								</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								    Polling Personnel Name:
									<h5>{{$listdata->applicant_name}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Designation
									<h5>{{$listdata->applicant_designation}}</h5>  
								  </div>
								  <div class="tdBox">
								    Parent Department
									<h5>{{$listdata->applicant_parent_department}}</h5>  
								  </div>
								  <div class="tdBox">
								    Address:
									<h5>{{$listdata->applicant_address}}</h5>  
								  </div>
								</td>
								<td>
								  <div class="tdBox mb-3 pt-2">
								    Contact No:
									<h5>{{$listdata->contact_no}}</h5>  
								  </div> 
								  <div class="tdBox mb-3 pt-2">
								    Applied Date:
									<h5>{{date('d-M-Y',strtotime($listdata->created_at))}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Injury Details:
									<h5 >{{(!empty($listdata->injury_details))?$injury[$listdata->injury_details]:''}}</h5>  
								  </div>
								  <div class="tdBox">
								    Reason of injury/Death:
									<h5 >{{(!empty($listdata->accident_reason))?$reason[$listdata->accident_reason]:''}}</h5>  
								  </div>
								</td>
							   	<td>
								 <div class="tdBox mb-3 pt-2">
								     Place of injury/Death:
									<h5>{{$listdata->accident_place}}</h5>  									
								  </div>  
								   <div class="tdBox">
								    Date of injury/Death:
									<h5>@if(!empty($listdata->accident_date)){{date('d-M-Y',strtotime($listdata->accident_date))}}@endif</h5>  
								  </div>
								  @if($listdata->application_status=='granted')
								  <div class="tdBox">
								    Date Of Payment:
									<h5>@if(!empty($listdata->date_of_payment)){{date('d-M-Y',strtotime($listdata->date_of_payment))}}@endif</h5>   
								  </div>
								  <div class="tdBox">
								    Payment Amount:
									<h5>{{$listdata->payment_amount}}</h5>  
								  </div>
								  @endif
								  @if($listdata->application_status=='pending')
									<div class="tdBox">
								    Pending Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								  @if($listdata->application_status=='rejected')
									<div class="tdBox">
								    Rejection Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								</td>
								<td class="pr-0">
								   <div class="{{$status_class}} pt-5">
								    Application Status:
									<h5>{{ucfirst($listdata->application_status)}}</h5>  
								  </div>  
								</td>
								
							   	<td>
								 <a href="{{url('acceo/mis/edit-exgratia/'.encrypt($listdata->id))}}">Edit</a>   
								</td>
							   </tr> 
							  <tr><td colspan="7" class="td-Blank">&nbsp;</td></tr>
								@php $count++; @endphp
							  @endforeach
							            
														 </tbody>
						   </table>	
							@else
							   <div class="tableWrap"><table class="table table-borderless tableLess"><tbody><tr><td>No Data Found</td></tbody></table></div>
						   @endif
				                            <!-- End Of tableWrap Div -->
                        </div>
                        <!-- End Of acptd Div -->

                        <div id="rejctd" class="tab-pane">
							@if(count($rejectedcases)>0)
							<table class="table table-borderless mediaTable">
							 <tbody>
                                                     @php $status_class='stats-pending';$color_class='#FFCA36'; $count = 1;@endphp  @foreach	 ($rejectedcases as $key=>$listdata)
                          
							  @php 
							  $status_class = 'stats-rejected';$color_class='red';

							  @endphp
							  
								<tr>
							   	<td align="center">{{ $count }}.</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								   State/UT:
									<h5>{{$listdata->ST_NAME}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Election:
									<h5>{{(!empty($listdata->election_type))?$elections[$listdata->election_type]:''}} - {{$listdata->election_year}}</h5>  
								  </div>
								  <div class="tdBox">
								    District:
									<h5>{{$listdata->DIST_NAME}}</h5>  
								  </div>
								  <div class="tdBox">
								    @if($listdata->election_type==1 || $listdata->election_type==2)AC @else PC @endif:
									@php 
										$ac_val = getacname($listdata->st_code,$listdata->ac_no);
										$pc_val = getpcbypcno($listdata->st_code,$listdata->pc_no);
									@endphp
									@if($listdata->election_type==1 || $listdata->election_type==2)
									<h5>@if(!empty($ac_val)){{$ac_val->AC_NAME}}@endif</h5> 
									@else
									<h5>@if(!empty($pc_val)){{$pc_val->PC_NAME}}@endif</h5> 	
									@endif  
								  </div>
								</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								    Polling Personnel Name:
									<h5>{{$listdata->applicant_name}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Designation
									<h5>{{$listdata->applicant_designation}}</h5>  
								  </div>
								  <div class="tdBox">
								    Parent Department
									<h5>{{$listdata->applicant_parent_department}}</h5>  
								  </div>
								  <div class="tdBox">
								    Address:
									<h5>{{$listdata->applicant_address}}</h5>  
								  </div>
								</td>
								<td>
								  <div class="tdBox mb-3 pt-2">
								    Contact No:
									<h5>{{$listdata->contact_no}}</h5>  
								  </div> 
								  <div class="tdBox mb-3 pt-2">
								    Applied Date:
									<h5>{{date('d-M-Y',strtotime($listdata->created_at))}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Injury Details:
									<h5 >{{(!empty($listdata->injury_details))?$injury[$listdata->injury_details]:''}}</h5>  
								  </div>
								  <div class="tdBox">
								    Reason of injury/Death:
									<h5 >{{(!empty($listdata->accident_reason))?$reason[$listdata->accident_reason]:''}}</h5>  
								  </div>
								</td>
							   	<td>
								 <div class="tdBox mb-3 pt-2">
								     Place of injury/Death:
									<h5>{{$listdata->accident_place}}</h5>  									
								  </div>  
								   <div class="tdBox">
								    Date of injury/Death:
									<h5>@if(!empty($listdata->accident_date)){{date('d-M-Y',strtotime($listdata->accident_date))}}@endif</h5>  
								  </div>
								  @if($listdata->application_status=='granted')
								  <div class="tdBox">
								    Date Of Payment:
									<h5>@if(!empty($listdata->date_of_payment)){{date('d-M-Y',strtotime($listdata->date_of_payment))}}@endif</h5>   
								  </div>
								  <div class="tdBox">
								    Payment Amount:
									<h5>{{$listdata->payment_amount}}</h5>  
								  </div>
								  @endif
								  @if($listdata->application_status=='pending')
									<div class="tdBox">
								    Pending Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								  @if($listdata->application_status=='rejected')
									<div class="tdBox">
								    Rejection Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								</td>
								<td class="pr-0">
								   <div class="{{$status_class}} pt-5">
								    Application Status:
									<h5>{{ucfirst($listdata->application_status)}}</h5>  
								  </div>  
								</td>
								
							   	<td>
								 <a href="{{url('acceo/mis/edit-exgratia/'.encrypt($listdata->id))}}">Edit</a>   
								</td>
							   </tr>
							  <tr><td colspan="7" class="td-Blank">&nbsp;</td></tr>
								@php $count++; @endphp
							  @endforeach
														 </tbody>
						   </table>	
							@else
							   <div class="tableWrap"><table class="table table-borderless tableLess"><tbody><tr><td>No Data Found</td></tbody></table></div>
						   @endif
                        </div>
                        <!-- End Of rejctd Div -->
                        <div id="pndg" class="tab-pane">
						@if(count($pendingcases)>0)
												<table class="table table-borderless mediaTable">
							 <tbody>
								@php $status_class='stats-pending';$color_class='#FFCA36'; $count = 1;@endphp  @foreach	 ($pendingcases as $key=>$listdata)
                          
							  @php 
							  $status_class = 'stats-pending';$color_class='#FFCA36';
							 
							  @endphp
							  
								<tr>
							   	<td align="center">{{ $count }}.</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								   State/UT:
									<h5>{{$listdata->ST_NAME}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Election:
									<h5>{{(!empty($listdata->election_type))?$elections[$listdata->election_type]:''}} - {{$listdata->election_year}}</h5>  
								  </div>
								  <div class="tdBox">
								    District:
									<h5>{{$listdata->DIST_NAME}}</h5>  
								  </div>
								  <div class="tdBox">
								    @if($listdata->election_type==1 || $listdata->election_type==2)AC @else PC @endif:
									@php 
										$ac_val = getacname($listdata->st_code,$listdata->ac_no);
										$pc_val = getpcbypcno($listdata->st_code,$listdata->pc_no);
									@endphp
									@if($listdata->election_type==1 || $listdata->election_type==2)
									<h5>@if(!empty($ac_val)){{$ac_val->AC_NAME}}@endif</h5> 
									@else
									<h5>@if(!empty($pc_val)){{$pc_val->PC_NAME}}@endif</h5> 	
									@endif 
								  </div>
								</td>
							   	<td>
								  <div class="tdBox mb-3 pt-2">
								    Polling Personnel Name:
									<h5>{{$listdata->applicant_name}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Designation
									<h5>{{$listdata->applicant_designation}}</h5>  
								  </div>
								  <div class="tdBox">
								    Parent Department
									<h5>{{$listdata->applicant_parent_department}}</h5>  
								  </div>
								  <div class="tdBox">
								    Address:
									<h5>{{$listdata->applicant_address}}</h5>  
								  </div>
								</td>
								<td>
								  <div class="tdBox mb-3 pt-2">
								    Contact No:
									<h5>{{$listdata->contact_no}}</h5>  
								  </div> 
								  <div class="tdBox mb-3 pt-2">
								    Applied Date:
									<h5>{{date('d-M-Y',strtotime($listdata->created_at))}}</h5>  
								  </div>  
								  <div class="tdBox">
								    Injury Details:
									<h5 >{{(!empty($listdata->injury_details))?$injury[$listdata->injury_details]:''}}</h5>  
								  </div>
								  <div class="tdBox">
								    Reason of injury/Death:
									<h5 >{{(!empty($listdata->accident_reason))?$reason[$listdata->accident_reason]:''}}</h5>  
								  </div>
								</td>
							   	<td>
								 <div class="tdBox mb-3 pt-2">
								     Place of injury/Death:
									<h5>{{$listdata->accident_place}}</h5>  									
								  </div>  
								   <div class="tdBox">
								    Date of injury/Death:
									<h5>@if(!empty($listdata->accident_date)){{date('d-M-Y',strtotime($listdata->accident_date))}}@endif</h5>  
								  </div>
								  @if($listdata->application_status=='granted')
								  <div class="tdBox">
								    Date Of Payment:
									<h5>@if(!empty($listdata->date_of_payment)){{date('d-M-Y',strtotime($listdata->date_of_payment))}}@endif</h5>   
								  </div>
								  <div class="tdBox">
								    Payment Amount:
									<h5>{{$listdata->payment_amount}}</h5>  
								  </div>
								  @endif
								  @if($listdata->application_status=='pending')
									<div class="tdBox">
								    Pending Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								  @if($listdata->application_status=='rejected')
									<div class="tdBox">
								    Rejection Reason:
									<h5>{{$listdata->reason_for_pending}}</h5>  
								  </div>  
								  @endif
								</td>
								<td class="pr-0">
								   <div class="{{$status_class}} pt-5">
								    Application Status:
									<h5>{{ucfirst($listdata->application_status)}}</h5>  
								  </div>  
								</td>
								
							   	<td>
								 <a href="{{url('acceo/mis/edit-exgratia/'.encrypt($listdata->id))}}">Edit</a>   
								</td>
							   </tr> 
							  <tr><td colspan="7" class="td-Blank">&nbsp;</td></tr>
								@php $count++; @endphp
							  @endforeach	
						  						  </tbody>
						   </table>	
						   @else
							   <div class="tableWrap"><table class="table table-borderless tableLess"><tbody><tr><td>No Data Found</td></tbody></table></div>
						   @endif
                          <!-- End Of tableWrap Div -->
						                        </div>
                        <!-- End Of pndg Div -->
                      </div>
                      <!-- End Of tab-content Div -->
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
  </div>
</div>
</section>
@endsection
@section('script')
<script>
<?php if(session()->has('success_msg')){?>
	setTimeout(function(){ $(".alert-dismissible").hide(); }, 3000);
<?php }?>
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#update_noncases").click(function(){
	$("#errorMsg").text('');
	$("#successMsg").text('');
	var checkboxval = $('input[name="nocases"]:checked').val();
	var remarks = $("#remarksval").val();
	jQuery.ajax({
    url: "add-no-pending-cases",
            type: 'POST',
            dataType: 'json',
            data: {remarksval:remarks, caseid: checkboxval},
			//data: {st_code: state_code},
            success:function(data)
            {
				if(data.success===true){
					$("#successMsg").text(data.msg);
				}else{
					$("#errorMsg").text(data.msg);
				}
				setTimeout(function(){ $("#errorMsg").text(''); $("#successMsg").text('');}, 3000);
            },
            error: function(error) {
				console.log(error.responseText);
            }
    });
	
}); 
</script>

@endsection