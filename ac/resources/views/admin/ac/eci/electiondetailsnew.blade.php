@extends('admin.layouts.themenew')
@section('title', 'Create Schedule')
@section('content') 
@include('admin.includes.script')
<?php /*echo ('<pre>');print_r($list_schedule);echo ('</pre>');exit;*/?>
<div class="ajax-loader">
	  <img src="{{ asset('admintheme/images/ajax-loader.gif') }}" class="img-responsive" />
 </div>
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
		 
			
			<strong>
			    @if(count($list_elecDetail))
					Date Of Announcement : {{date('d-M-Y', strtotime($list_elecDetail[0]->DT_PRESS_ANNC))}}<br/>
					Date Of Counting:  {{date('d-M-Y', strtotime($list_elecDetail[0]->DATE_COUNT))}}<br/> 
					Date Of Election Completion:  {{date('d-M-Y', strtotime($list_elecDetail[0]->DTB_EL_COM))}} 
				@else
					Date Of Announcement : {{date('d-M-Y', strtotime($list_schedule[0]->DT_PRESS_ANNC))}}<br/>
					Date Of Counting:  {{date('d-M-Y', strtotime($list_schedule[0]->DATE_COUNT))}}<br/> 
					Date Of Election Completion:  {{date('d-M-Y', strtotime($list_schedule[0]->DTB_EL_COM))}} 
				@endif
			</strong>
		</div>
		<?php /*print_r($list_elecDetail);exit;*/?>
			@if(count($list_schedule))
			<div class="row">
			<?php $i = 0 ;?>
			@foreach($list_schedule as $listSch)
				<?php $i++; $modulo =($i % 3)?>

				@if($modulo==0)
					<?php $bg_color = '#e0ffff' ;?>
				@elseif($modulo==1)
					<?php $bg_color = '#f5f5db' ;?>
				@elseif($modulo==2)
					<?php	$bg_color = '#ffe4e1'; ?>
				@endif
				<div class="col-sm-4 col-md-4 col-lg-4" style="background-color:{{$bg_color}}">
					
						<div class="col-sm-12">
							<strong>Date Of Poll: {{date('d-m-Y', strtotime($listSch->DATE_POLL))}}</strong><br/>
							@if($listSch->DT_ISS_NOM != '')
								Date Of Nomination : {{date('d-M-Y', strtotime($listSch->DT_ISS_NOM))}}<br/>
								Last Date Of Nomination:  {{date('d-M-Y', strtotime($listSch->LDT_IS_NOM))}}<br/> 
								Date Of Withdrawal:  {{date('d-M-Y', strtotime($listSch->LDT_WD_CAN))}} <br/>
								Date Of Scrutiny:  {{date('d-M-Y', strtotime($listSch->DT_SCR_NOM))}} 
								
								
								<div class="getstatecode">
									<input type="hidden" name="ST_CODE"/>
								</div>
								<?php 
								$getElectionDetails = getElectionDetail($listSch->ELECTION_TYPEID);
								$CONST_TYPE = '';
								$DELIM_TYPE = '';
								$ELECTION_TYPE = '';
								$ELECTION_TYPEID = '';
								$YEAR = '';
								$CURRENTELECTION = '';
								?>
								@if(count($getElectionDetails))
									@foreach($getElectionDetails as $getElecDetails)
										<?php $CONST_TYPE = $getElecDetails->CONST_TYPE ;
											  $DELIM_TYPE = $getElecDetails->DELIM_TYPE ;
											  $ELECTION_TYPE = $getElecDetails->ELECTION_TYPE ;
											  $ELECTION_TYPEID = $getElecDetails->ELECTION_TYPEID ;
											  $YEAR = $getElecDetails->YEAR ;
											  $CURRENTELECTION = $getElecDetails->CURRENTELECTION ;
										?>
										<input type="hidden" name="CONST_TYPE" value="{{ $CONST_TYPE }}"/>
										<input type="hidden" name="ELECTION_TYPE" value="{{ $ELECTION_TYPE }}"/>
								    @endforeach
								@endif 
								
								<div class="scheduleelecdata" id="{{$listSch->SCHEDULEID}}">
								
								<input type="hidden" name="DELIM_TYPE" value="{{ $DELIM_TYPE }}"/>
								
								<input type="hidden" name="ELECTION_TYPEID" value="{{ $ELECTION_TYPEID }}"/>
								<input type="hidden" name="YEAR" value="{{ $YEAR }}"/>
								<input type="hidden" name="CURRENTELECTION" value="{{ $CURRENTELECTION }}"/>
								<input type="hidden" name="SCHEDULEID" value="{{$listSch->SCHEDULEID}}"/>
								<input type="hidden" name="StatePHASE_NO" value="{{$listSch->SCHEDULEID}}"/>
								<input type="hidden" name="ELECTION_ID" value="{{$listSch->ELECTION_ID}}"/>
								<?php 
									$getElecSch = getElecSch($electiontype_id,$listSch->SCHEDULEID);
								?>
								<ul id="sortable3" class="col-sm-12 parentschedule connectedSortable" data-id="{{$listSch->SCHEDULEID}}">
								@if(count($getElecSch) != '')
									
										@foreach($getElecSch as $getElecSch)
											<li id="{{$getElecSch->ST_CODE}}">
											<?php $getStateDetail = getStatebyId($getElecSch->ST_CODE); ?>
												<?php $statename = '' ;?>
												@if(!empty($getStateDetail->ST_NAME))
													<?php echo $statename = $getStateDetail->ST_NAME; ?>
												@else
													<?php $statename = '' ;?>
												@endif
												
												
												<?php $getElecSchDetails = getElecSchDetails($electiontype_id,$listSch->SCHEDULEID,$getElecSch->ST_CODE)?>
												<Strong>Total {{$getElecSch->CONST_TYPE}}: {{count($getElecSchDetails)}}</strong>
												@if(count($getElecSchDetails))
												<a href="#" class="show_hide" data-id="statedata-{{$getElecSch->ST_CODE}}" data-content="toggle-text">Read More</a>
											
												<ul id="sortable-{{$getElecSch->ST_CODE}}" data-id="{{$getElecSch->ST_CODE}}"  data-name ="{{$statename}}" class="selectAC showac connectedSortable" data-schedule = "{{$listSch->SCHEDULEID}}" data-schdate="{{date('d-M-Y', strtotime($listSch->DATE_POLL))}}" data-name="{{$statename}}">
												
												<a class="hideState" title="Delete {{$statename}} from this schedule" data-schdate="{{date('d-M-Y', strtotime($listSch->DATE_POLL))}}" data-id="{{$listSch->SCHEDULEID}}" data-state="{{ $getElecSch->ST_CODE }}" data-name="{{$statename}}" >X</a>
												
													<input type="button" class="deleteAcs" value="Delete AC">
													@foreach($getElecSchDetails as $getElecSchData)
														<?php 
														$constType = $getElecSch->CONST_TYPE; 
														$getDetails = '';
														?>
														@if($constType == 'AC')
															<?php $getDetails = getACbyStatenAcId($getElecSchData->CONST_NO, $getElecSchData->ST_CODE);?>
														@elseif($constType == 'PC')
															<?php $getDetails = getPCbyStatenPcId($getElecSchData->CONST_NO, $getElecSchData->ST_CODE);?>
														@endif
														<?php 
															$constname = '' ;
															$constno = '';
														?>
														@if(!empty($getDetails))
															@if($constType == 'AC')
																<?php
																	$constname = $getDetails->AC_NAME;
																	$constno = $getDetails->AC_NO;
																?>
															@elseif($constType == 'PC')
																@if(!empty($getDetails->PC_NAME))
																<?php
																	$constname = $getDetails->PC_NAME;
																	$constno = $getDetails->PC_NO;
																?>
																@endif
															@endif	
														@endif
														
														<li class="recordsAcs">{{$constno}} - {{$constname}} <input type="checkbox" name="constcheck[]" class="sub_chk" value="{{$constno}}"/> <a class="hideAC" id="{{$constno}}" title="Delete {{$constno}} - {{$constname}} AC" data-name="{{$constname}}">X</a></li>
													@endforeach
													
												</ul>
												@endif
												
											</li>
										@endforeach
									
								@endif
								</ul>
								</div>
							@else
								<div class="errorred">Please add schedule details</div>
							@endif
						</div>
						<div class="col-sm-12">
							<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal-{{$listSch->SCHEDULEID}}">@if($listSch->DT_ISS_NOM != '') Modify Details @else Add Details @endif</button>
						</div>
						<!-- Start Modify Modal -->
						<div id="myModal-{{$listSch->SCHEDULEID}}" class="modal fade" role="dialog">
						  <div class="modal-dialog">

							<!-- Modal content-->
							<div class="modal-content">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Schedule {{$listSch->SCHEDULEID}}</h4>
							  </div>
							    @section('popupmodalscript')
								<script>
								  var schid = <?php echo $listSch->SCHEDULEID ?> ;
								  jQuery(document).on("focus", ".modal-body", function () {
									 var modalid = jQuery(this).attr('id');
									   jQuery("#DT_ISS_NOM"+modalid).datepicker({
										  dateFormat: "dd-M-yy",
										  minDate : new Date()
									   });
									   jQuery("#LDT_IS_NOM"+modalid).datepicker({
										  dateFormat: "dd-M-yy",  
										  minDate : new Date()										  
									   });
									   jQuery("#DT_SCR_NOM"+modalid).datepicker({
										  dateFormat: "dd-M-yy",  
										  minDate : new Date()
									   });
									   jQuery("#LDT_WD_CAN"+modalid).datepicker({
										  dateFormat: "dd-M-yy", 
										  minDate : new Date()                                  
									   });
								  });  
								</script> 
								@endsection
								<?php /* print_r($listSch);*/?>
							  <div class="modal-body" id="{{$listSch->SCHEDULEID}}">
								<form id="scheduleform{{$listSch->SCHEDULEID}}">
								
								  <!-- Schedule Id -->
								  <input type="hidden" value="{{$listSch->SCHEDULEID}}" name="SCHEDULEID" id="schid{{$listSch->SCHEDULEID}}"/>
								  
								  <!-- Schedule No -->
								  <input type="hidden" value="{{$listSch->SCHEDULENO}}" name="SCHEDULENO" id="SCHEDULENO{{$listSch->SCHEDULEID}}"/>
								  
								  <!-- Election Id -->
								  <input type="hidden" value="{{$listSch->ELECTION_ID}}" name="ELECTION_ID" id="electionid{{$listSch->SCHEDULEID}}"/>
								  
								  <input type="hidden" name="ELECTION_TYPEID" value="{{$listSch->ELECTION_TYPEID}}"/>
								  
								  <input type="hidden" name="DATE_COUNT" id="DATE_COUNT{{$listSch->SCHEDULEID}}" value="{{date('d-M-Y', strtotime($listSch->DATE_COUNT))}}"/>
								  <input type="hidden" name="DATE_POLL"  id="DATE_POLL{{$listSch->SCHEDULEID}}" value="{{date('d-M-Y', strtotime($listSch->DATE_POLL))}}"/>
								  
								  <div class="form-group row">
									<label for="DT_ISS_NOM{{$listSch->SCHEDULEID}}" class="col-sm-4 col-form-label">Date of Nomination 
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" readonly  @if($listSch->DT_ISS_NOM != '') value="{{date('d-M-Y', strtotime($listSch->DT_ISS_NOM))}}" @endif name="DT_ISS_NOM" id="DT_ISS_NOM{{$listSch->SCHEDULEID}}" placeholder="Date of Nomination">
									  <div class="startnominationerrormsg errormsg errorred"></div>
									</div>
								  </div>
								  <div class="form-group row">
									<label for="LDT_IS_NOM{{$listSch->SCHEDULEID}}" class="col-sm-4 col-form-label">Last Date of Nomination
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" readonly @if($listSch->LDT_IS_NOM != '') value="{{date('d-M-Y', strtotime($listSch->LDT_IS_NOM))}}" @endif id="LDT_IS_NOM{{$listSch->SCHEDULEID}}" name="LDT_IS_NOM" placeholder="Last Date of Nomination">
									   <div class="endnominationerrormsg errormsg errorred"></div>
									</div>
								  </div>
								  <div class="form-group row">
									<label for="DT_SCR_NOM{{$listSch->SCHEDULEID}}"  class="col-sm-4 col-form-label">Date for Scrutiny
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" readonly @if($listSch->DT_SCR_NOM != '') value="{{date('d-M-Y', strtotime($listSch->DT_SCR_NOM))}}" @endif id="DT_SCR_NOM{{$listSch->SCHEDULEID}}" name="DT_SCR_NOM" placeholder="Date for Scrutiny">
									  <div class="scrutinydateerrormsg errormsg errorred"></div>
									</div>
								  </div>
								  <div class="form-group row">
									<label for="LDT_WD_CAN{{$listSch->SCHEDULEID}}" class="col-sm-4 col-form-label">Date of Withdrawal
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" readonly @if($listSch->LDT_WD_CAN != '') value="{{date('d-M-Y', strtotime($listSch->LDT_WD_CAN))}}" @endif name="LDT_WD_CAN" id="LDT_WD_CAN{{$listSch->SCHEDULEID}}" placeholder="Date of Withdrawal">
									   <div class="withdrawalerrormsg errormsg errorred"></div>
									</div>
								  </div>
								  <div class="form-group row">
									<div class="col-sm-10">
									  <button type="button" onclick="submitfrm('{{$listSch->SCHEDULEID}}')" class="btn btn-info btn-md">Save</button>
									</div>
								  </div>
								</form>
							  </div>
							  <div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							  </div>
							</div>

						  </div>
						</div>
						<!-- End Modify Modal -->
						@section('modalscript')
						<script type="text/javascript">
							function submitfrm(schid)
						    {
								var dateofnomination = jQuery('#DT_ISS_NOM'+schid).val();
								var lastdateofnomination = jQuery('#LDT_IS_NOM'+schid).val();
								var scrutinydate = jQuery('#DT_SCR_NOM'+schid).val();
								var withdrawaldate = jQuery('#LDT_WD_CAN'+schid).val();
								var SCHID = jQuery('#schid'+schid).val();
								var SCHNO = jQuery('#SCHEDULENO'+schid).val();
								var electionid = jQuery('#electionid'+schid).val();
								var ELECTION_TYPEID = jQuery('input[name="ELECTION_TYPEID"]').val();
								var DATE_COUNT = jQuery('#DATE_COUNT'+schid).val();
								var DATE_POLL = jQuery('#DATE_POLL'+schid).val();
								
								if(dateofnomination == ''){
									jQuery('.errormsg').html('');
									jQuery('.startnominationerrormsg').html('Start date of nomination should not be blank');
									jQuery( dateofnomination ).focus();
									return false;
								}
								if(new Date(dateofnomination) > new Date(DATE_POLL))
								{
									jQuery('.errormsg').html('');
									jQuery('.startnominationerrormsg').html('Start date of nomination should not be less than date of poll');
									jQuery( dateofnomination ).focus();
									return false;
								}
								if(lastdateofnomination == ''){
									jQuery('.errormsg').html('');
									jQuery('.endnominationerrormsg').html('Last date of nomination should not be blank');
									jQuery( lastdateofnomination ).focus();
									return false;
								}
								if(new Date(dateofnomination) >= new Date(lastdateofnomination))
								{
									jQuery('.errormsg').html('');
									jQuery('.endnominationerrormsg').html('Last date of nomination should be greater than start date of nomination');
									jQuery( lastdateofnomination ).focus();
									return false;
								}
								if(new Date(lastdateofnomination) > new Date(DATE_POLL))
								{
									jQuery('.errormsg').html('');
									jQuery('.endnominationerrormsg').html('Last date of nomination should be less than date of poll');
									jQuery( lastdateofnomination ).focus();
									return false;
								}
								if(scrutinydate == ''){
									jQuery('.errormsg').html('');
									jQuery('.scrutinydateerrormsg').html('Scrutiny date of nomination should not be blank');
									jQuery( scrutinydate ).focus();
									return false;
								}
								if(new Date(scrutinydate) > new Date(DATE_POLL))
								{
									jQuery('.errormsg').html('');
									jQuery('.scrutinydateerrormsg').html('Scrutiny date should be less than date of poll');
									jQuery( scrutinydate ).focus();
									return false;
								}
								if(new Date(lastdateofnomination) >= new Date(scrutinydate))
								{
									jQuery('.errormsg').html('');
									jQuery('.scrutinydateerrormsg').html('Scrutiny date should be greater than last date of nomination');
									jQuery( scrutinydate ).focus();
									return false;
								}
								if(withdrawaldate == ''){
									jQuery('.errormsg').html('');
									jQuery('.withdrawalerrormsg').html('Scrutiny date of nomination should not be blank');
									jQuery( withdrawaldate ).focus();
									return false;
								}
								if(new Date(withdrawaldate) > new Date(DATE_POLL))
								{
									jQuery('.errormsg').html('');
									jQuery('.withdrawalerrormsg').html('Withdrawal date should be less than date of poll');
									jQuery( withdrawaldate ).focus();
									return false;
								}
								if(new Date(scrutinydate) >= new Date(withdrawaldate))
								{
									jQuery('.errormsg').html('');
									jQuery('.withdrawalerrormsg').html('Withdrawal date should be greater than scrutiny date');
									jQuery( withdrawaldate ).focus();
									return false;
								}
								jQuery.ajax({
									  url: "{{url('/eci/saveelectiondata')}}",
									  type: 'GET',
									  data: {dateofnomination:dateofnomination,lastdateofnomination:lastdateofnomination,scrutinydate:scrutinydate,withdrawaldate:withdrawaldate,SCHID:SCHID,SCHNO:SCHNO,electionid:electionid,ELECTION_TYPEID:ELECTION_TYPEID},
									  success: function(result){
											location.reload();
									  }
							   });
							}
						</script>
						@endsection
				</div>
			@endforeach
			</div>
			@endif
			<!-- State list with ac -->
			@if(count($list_elecDetail))
			<div class="schedulebox-data row">
			  <ul id="sortable4" class="connectedSortable selectstate">
			  @foreach($list_elecDetail as $listElecData)
					<?php $getstatedetail = getStatebyId($listElecData->ST_CODE); 
						$statename = '';
					?>
					@if(!empty($getstatedetail->ST_NAME))
					<li class="ui-state-highlight" id="{{$listElecData->ST_CODE}}">{{$getstatedetail->ST_NAME}} 
						
						<ul id="groupAc-{{$listElecData->ST_CODE}}" data-id="{{$listElecData->ST_CODE}}" class="selectAC connectedSortable" style="display:none;">
							<?php $getAclist = showlistsortabledata($listElecData->ST_CODE, $listSch->ELECTION_TYPEID, $YEAR, $listSch->ELECTION_ID);  ?>
							
							@if(count($getAclist) > 1)
							<li class="ui-state-highlight"><input type="checkbox" class="checkAllAcs" value="{{$listElecData->ST_CODE}}"> Check All Acs</li>
							@foreach($getAclist as $getac)
								<li class="ui-state-highlight" data-id="{{$listElecData->ST_CODE}}" data-key="{{$getac['CONST_NO']}}" id="{{$getac['CONST_NO']}}">
									<input type="checkbox" class="saveAcs" id="saveAcs-{{$listElecData->ST_CODE}}" value="{{$getac['CONST_NO']}}'">
									{{$getac['CONST_NO']}} - {{$getac['Const_Name']}}
								</li>
							@endforeach
							@else
								<li class="ui-state-highlight">All Acs are scheduled</li>
							@endif
						</ul>
					</li>
					
					@endif
			  @endforeach
			  </ul>
			</div>
			@endif
			<div class="row">
				<div class="btn btn-default"><a href="{{url('/eci/electionlisting/'.$electiontype_id)}}">Finalize</a></div>
			<div>
		</div>
	</div>  
  </div>
</div> 

@endsection

@section('script')

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.selectAC').hide();
	
	jQuery('.selectstate li').click(function(){
		jQuery('.selectstate li').removeClass('active');
		var sortablestateliid = jQuery(this).attr('id');
		jQuery('.selectstate li#'+sortablestateliid).addClass('active');
		jQuery('.selectAC').hide();
		jQuery('#groupAc-'+sortablestateliid).show();
	});
	
	jQuery('input.checkAllAcs').change(function(){
		 if (! jQuery('input:checkbox').is('checked')) {
			  jQuery('.saveAcs:checkbox').prop('checked',true);
		  } else {
			  jQuery('.saveAcs:checkbox').prop('checked', false);
		  }            
	});
	jQuery(".saveAcs").change(function(){
		var getAcid = jQuery(this).parent().attr('id');
		if (!jQuery(this).prop("checked")){
			jQuery(".checkAllAcs").prop("checked",false);
		}
	});
	
	var scheduleboxid ;
	var oldList, newList, item;
		jQuery("#sortable3, #sortable4, .selectAC, .scheduleelecdata").sortable({
			connectWith: "#sortable3, #sortable4, .selectAC, .scheduleelecdata",
			opacity: 0.6, 
			cursor: 'move', 
			tolerance: 'pointer', 
			revert: true, 
			items:'li',
			placeholder: 'state', 
			forcePlaceholderSize: true,
			start: function(event, ui) {
				item = ui.item;
				newList = oldList = ui.item.parent();
			},
			stop: function(event, ui) { 
				var scheduleelecdata = ui.item.parent().attr("data-id");
				var electiontype_id = '<?php echo $electiontype_id;?>';
				//var SCHEDULEID = jQuery('input[name="SCHEDULEID"]').val();
				var CONST_TYPE = jQuery('input[name="CONST_TYPE"]').val();
				var DELIM_TYPE = jQuery('input[name="DELIM_TYPE"]').val();
				var ELECTION_TYPE = jQuery('input[name="ELECTION_TYPE"]').val();
				var CURRENTELECTION = jQuery('input[name="CURRENTELECTION"]').val();
				var ELECTION_ID = jQuery('input[name="ELECTION_ID"]').val();
				var ELECTION_TYPEID = jQuery('input[name="ELECTION_TYPEID"]').val();
				var YEAR = jQuery('input[name="YEAR"]').val();
				var SCHEDULEID ;
				ui.item.addClass("parent");
				
				var getelecboxid = this.id; // Where items send
				var getelecboxitem = ui.item[0].id; // Which item
				
				var stcode, constid;
				/* declare an checkbox array */
				var chkArray = [];
				var reslength = '';
				/* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
				jQuery(".saveAcs:checked").each(function() {
					chkArray.push($(this).val());
					constid = jQuery(this).val();
					stcode = getelecboxitem;
					jQuery.ajax({
						beforeSend: function(){
							$('.ajax-loader').css("visibility", "visible");
						},
						url: "{{url('/eci/savesortabledata')}}",
						type: 'GET',
						data: {ST_CODE:getelecboxitem,SCHEDULEID:scheduleelecdata,CONST_NO:constid,CONST_TYPE:CONST_TYPE,DELIM_TYPE:DELIM_TYPE,ELECTION_TYPE:ELECTION_TYPE,YEAR:YEAR,CURRENTELECTION:CURRENTELECTION,StatePHASE_NO:1,ELECTION_ID:ELECTION_ID,ELECTION_TYPEID:ELECTION_TYPEID},
						data: {ST_CODE:getelecboxitem,SCHEDULEID:scheduleelecdata,CONST_NO:constid,CONST_TYPE:CONST_TYPE,DELIM_TYPE:DELIM_TYPE,ELECTION_TYPE:ELECTION_TYPE,YEAR:YEAR,CURRENTELECTION:CURRENTELECTION,StatePHASE_NO:1,ELECTION_ID:ELECTION_ID,ELECTION_TYPEID:ELECTION_TYPEID},
						success: function(result){
							//alert(result);
							location.reload();
						},
						complete: function(){
							jQuery('.ajax-loader').css("visibility", "hidden");
						}
					});
				});
				/* we join the array separated by the comma */
				var selected;
				selected = chkArray.join(',') ;
				
				/* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
				if(selected.length > 0){
					//alert("You have selected " + selected);	
				}else{
					alert("Please at least check one of the checkbox");	
					location.reload();
				}
			},
		});
		
	jQuery("#sortable3, #sortable4, .selectAC, .scheduleelecdata").disableSelection();
	
	jQuery('.showac').hide();
	jQuery(".show_hide").on("click", function () {
		var moreid = jQuery(this).attr('data-id');
		//alert(moreid);
        var txt = jQuery(".showac."+moreid).is(':visible') ? 'Read More' : 'Read Less';
        jQuery(".show_hide").text(txt);
        jQuery(this).next('.showac').slideToggle(200);
    });

	//Delete AC
	jQuery('.hideAC').click(function(){
		var acId = jQuery(this).attr('id');
		var acname = jQuery(this).attr('data-name');
		var statecodeid = jQuery(this).parent().parent().attr('data-id');
		var statename = jQuery(this).parent().parent().attr('data-name');
		var schdate = jQuery(this).parent().parent().attr('data-schdate');
		var scheduleid = jQuery(this).parent().parent().attr('data-schedule');
		var ELECTION_TYPEID = jQuery('input[name="ELECTION_TYPEID"]').val();
		var YEAR = jQuery('input[name="YEAR"]').val();
		var ELECTION_ID = jQuery('input[name="ELECTION_ID"]').val();
		var message = 'Do you really want to delete ' + acId + '-' + acname + ' having schedule date ' +schdate+ ' from '+ statename  ;
		jQuery('<div></div>').appendTo('body')
		.html('<div><h6>'+message+'?</h6></div>')
		.dialog({
			modal: true, title: 'Delete message', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () {
					//alert(acId);
					jQuery.ajax({
						url: "{{url('/eci/deletesortabledata')}}",
						type: 'GET',
						data: {ELECTION_ID:ELECTION_ID,CONST_NO:acId,ST_CODE:statecodeid,SCHEDULEID:scheduleid,YEAR:YEAR,ELECTION_TYPEID:ELECTION_TYPEID},
						success: function(result){
							alert(result)
							location.reload();
						}
					});
													
					jQuery('.page-wrap').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');
					
					jQuery(this).dialog("close");
				},
				No: function () {                           		                             
				jQuery('.page-wrap').append('<h1>Confirm Dialog Result: <i>No</i></h1>');
				
					jQuery(this).dialog("close");
				}
			},
			close: function (event, ui) {
				jQuery(this).remove();
			}
		});
		
	});
	//Delete State
	jQuery('.hideState').click(function(){
		var statename = jQuery(this).attr('data-name');
		var schdate = jQuery(this).attr('data-schdate');
		var stcode = jQuery(this).attr('data-state');
		var schid = jQuery(this).attr('data-id');
		var message ="Do you really want to delete " + statename;
		jQuery('<div></div>').appendTo('.page-wrap')
		.html('<div><h6>'+message+'?</h6></div>')
		.dialog({
			modal: true, title: 'Delete message', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () {
					jQuery.ajax({
						url: "{{url('/eci/deletesortablestate')}}",
						type: 'GET',
						data: {stcode:stcode,schid:schid},
						success: function(result){
							alert(result)
							location.reload();
						}
					});
													
					jQuery('.page-wrap').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');
					
					jQuery(this).dialog("close");
				},
				No: function () {                           		                             
				jQuery('.page-wrap').append('<h1>Confirm Dialog Result: <i>No</i></h1>');
				
					jQuery(this).dialog("close");
				}
			},
			close: function (event, ui) {
				jQuery(this).remove();
			}
		});
	});
	
	//Delete Multiple Acs
	jQuery(".deleteAcs").click(function() 
	{  
		
		var acsCheckboxes = new Array();
		var statecodeid = jQuery(this).parent().attr('data-id');
		var statename = jQuery(this).parent().attr('data-name');
		var schdate = jQuery(this).parent().attr('data-schdate');
		var scheduleid = jQuery(this).parent().attr('data-schedule');
		var ELECTION_TYPEID = jQuery('input[name="ELECTION_TYPEID"]').val();
		var YEAR = jQuery('input[name="YEAR"]').val();
		var ELECTION_ID = jQuery('input[name="ELECTION_ID"]').val();
		var message = 'Do you really want to delete these acs having schedule date ' +schdate+ ' from '+ statename  ;
		jQuery('<div></div>').appendTo('body')
		.html('<div><h6>'+message+'?</h6></div>')
		.dialog({
			modal: true, title: 'Delete message', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () {
					
					jQuery('.sub_chk:checked').each(function() {
						var acid = jQuery(this).val() ;
						if (jQuery(this).is(":checked")) {
							jQuery.ajax({
								url: "{{url('/eci/deletemultiplesortabledata')}}",
								type: 'GET',
								data: {ELECTION_ID:ELECTION_ID,CONST_NO:acid,ST_CODE:statecodeid,SCHEDULEID:scheduleid,YEAR:YEAR,ELECTION_TYPEID:ELECTION_TYPEID},
								success: function(result){
									//alert(result)
									location.reload();
								}
							});
							// acsCheckboxes.push(jQuery(this).val());
						}
					});
		
					
													
					jQuery('.page-wrap').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');
					
					jQuery(this).dialog("close");
				},
				No: function () {                           		                             
				jQuery('.page-wrap').append('<h1>Confirm Dialog Result: <i>No</i></h1>');
				
					jQuery(this).dialog("close");
				}
			},
			close: function (event, ui) {
				jQuery(this).remove();
			}
		});
	});
});
</script>
@endsection