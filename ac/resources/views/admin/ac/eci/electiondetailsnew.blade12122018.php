@extends('admin.layouts.themenew')
@section('title', 'Create Schedule')
@section('content') 
<?php /*echo ('<pre>');print_r($list_schedule);echo ('</pre>');exit;*/?>
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
		<div class="nw-crte-usr newschedule">
		 <div class="row">
		    <div class="profileSteps">
				  <div class="profileStep profileStepPassive profileStepOne">
					<div class="connect"></div>
					<div class="icon">Election Listing</div>
				  </div>
				  <div class="profileStep profileStepActive profileStepOne">
					<div class="connect"></div>
					<div class="icon">Election Details</div>
				  </div>
				  <div class="profileStep profileStepActive profileStepOne">
					<div class="connect"></div>
					<div class="icon">Election Schedule</div>
				  </div>
				  <div class="profileStep profileStepActive profileStepOne">
					<div class="icon">Create Schedule</div>
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
		<?php print_r($list_elecDetail);exit;?>
			@if(count($list_elecDetail))
			<div class="row">
			<?php $i = 0 ;?>
			@foreach($list_elecDetail as $listSch)
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
								<input type="text" name="SCHEDULEID" value="{{$listSch->SCHEDULEID}}">
								<input type="text" name="CONST_TYPE" value="{{$listSch->CONST_TYPE}}">
								<div class="scheduleelecdata" id="{{$listSch->SCHEDULEID}}">
								
								<?php 
								$data = DB::table('m_schedule')
									->join('m_election_details', 'm_election_details.ScheduleID','=','m_schedule.ScheduleID')
									->select('m_election_details.ST_CODE','m_election_details.CONST_TYPE','m_election_details.CONST_NO', 'm_schedule.DT_PRESS_ANNC','m_schedule.*')
									->where('m_election_details.ELECTION_ID',$election_id)
									->where('m_election_details.SCHEDULEID',$listSch->SCHEDULEID)
									->get();
								?>
								
								@if(count($data) != '')
									
									<?php $elecstcode = ''; ?>
									@foreach($data as $elcdata)
										<?php $elecstcode = $elcdata->ST_CODE ;?>
									@endforeach
									<?php $getStateDetail = getStatebyId($elecstcode)?>
									<li>{{$getStateDetail->ST_NAME}}
									<Strong>Total AC: {{count($data)}}</strong>
									<a href="#" class="show_hide" data-id="statedata-{{$elecstcode}}" data-content="toggle-text">Read More</a>
										<ul class="showac statedata-{{$elecstcode}}">
											@foreach($data as $elcdata)
											
												<?php $getAcData = getACbyStatenAcId($elcdata->CONST_NO,$elcdata->ST_CODE );?>
												<li>{{$getAcData->AC_NAME}}</li>
											@endforeach
										</ul>
									</li>
								@endif
								
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
							  <div class="modal-body">
								<form id="scheduleform{{$listSch->SCHEDULEID}}">
								
								  <!-- Schedule Id -->
								  <input type="hidden" value="{{$listSch->SCHEDULEID}}" name="SCHEDULEID" id="schid{{$listSch->SCHEDULEID}}"/>
								  
								  <!-- Schedule No -->
								  <input type="hidden" value="{{$listSch->SCHEDULENO}}" name="SCHEDULENO" id="SCHEDULENO{{$listSch->SCHEDULEID}}"/>
								  
								  <!-- Election Id -->
								  <input type="hidden" value="{{$listSch->ELECTION_ID}}" name="ELECTION_ID" id="electionid{{$listSch->SCHEDULEID}}"/>
								  
								  <div class="form-group row">
									<label for="DT_ISS_NOM{{$listSch->SCHEDULEID}}" class="col-sm-4 col-form-label">Date of Nomination 
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" name="DT_ISS_NOM" id="DT_ISS_NOM{{$listSch->SCHEDULEID}}" placeholder="Date of Nomination">
									</div>
								  </div>
								  <div class="form-group row">
									<label for="LDT_IS_NOM{{$listSch->SCHEDULEID}}" class="col-sm-4 col-form-label">Last Date of Nomination
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" id="LDT_IS_NOM{{$listSch->SCHEDULEID}}" name="LDT_IS_NOM" placeholder="Last Date of Nomination">
									</div>
								  </div>
								  <div class="form-group row">
									<label for="DT_SCR_NOM{{$listSch->SCHEDULEID}}" class="col-sm-4 col-form-label">Date for Scrutiny
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" id="DT_SCR_NOM{{$listSch->SCHEDULEID}}" name="DT_SCR_NOM" placeholder="Date for Scrutiny">
									</div>
								  </div>
								  <div class="form-group row">
									<label for="LDT_WD_CAN{{$listSch->SCHEDULEID}}" class="col-sm-4 col-form-label">Date of Withdrawal
										<span class="red">*</span>
									</label>
									<div class="col-sm-8">
									  <input type="text" class="form-control" name="LDT_WD_CAN" id="LDT_WD_CAN{{$listSch->SCHEDULEID}}" placeholder="Date of Withdrawal">
									</div>
								  </div>
								  <div class="form-group row">
									<div class="col-sm-10">
									  <button type="button" onclick="submitfrm('{{$listSch->SCHEDULEID}}')" class="btn btn-info btn-md">Submit</button>
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
								
								jQuery.ajax({
									  url: "{{url('/eci/saveelectiondata')}}",
									  type: 'GET',
									  data: {dateofnomination:dateofnomination,lastdateofnomination:lastdateofnomination,scrutinydate:scrutinydate,withdrawaldate:withdrawaldate,SCHID:SCHID,SCHNO:SCHNO,electionid:electionid},
									  success: function(result){
											//alert(result);
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
			  <ul id="sortable3" class="connectedSortable selectstate">
			  @foreach($list_elecDetail as $listElecData)
					<?php $getstatedetail = getStatebyId($listElecData->ST_CODE); ?>
					<li class="ui-state-highlight" id="{{$listElecData->ST_CODE}}">{{$getstatedetail->ST_NAME}} 
						<ul id="sortable-{{$listElecData->ST_CODE}}" class="selectAC connectedSortable"></ul>
					</li>
			  @endforeach
			  </ul>
			</div>
			@endif
			<div class="row">
				<div class="btn btn-default"><a href="{{url('/eci/electionlisting/'.$election_id)}}">Finalize</a></div>
			<div>
		</div>
	</div>  
  </div>
</div> 

@endsection

@section('script')

<script type="text/javascript">
jQuery(document).ready(function() {
	// Get AC By State ID
	jQuery('.selectstate li').mouseover(function(){
		var sortablestateliid = jQuery(this).attr('id');
		var url = '{{ url('state') }}/' + sortablestateliid + '/ac';
		jQuery.get(url, function(data) {
			var getac = jQuery('.selectstate li ul#sortable-'+sortablestateliid);
			
			jQuery('.selectstate li ul.selectAC').empty();
			//jQuery('.selectstate li#'+sortablestateliid+' .totalac').html(jQuery(data).length);
			jQuery.each(data,function(key, value) {
				 getac.append('<li class="ui-state-highlight" data-key="'+value.AC_NO+'" id=' + value.AC_NO + '>' + value.AC_NAME + '</li>');
			});
		});
	});
	var oldList, newList, item;
	jQuery("#sortable3, .selectAC, .scheduleelecdata").sortable({
		connectWith: "#sortable3, .selectAC, .scheduleelecdata",
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
			
			
			var scheduleelecdata = ui.item.parent().attr("id");
			var electionid = '<?php echo $election_id;?>';
			var consttype = '<?php /*echo $eledata['CONST_TYPE'] ;*/?>';
			var StatePHASE_NO = '<?php /*echo $eledata['StatePHASE_NO'] ;*/?>';
			ui.item.addClass("parent");
			if(ui.item.hasClass("parent")){
				 jQuery('.parent ul').addClass("child");
				 var getelecboxid = this.id; // Where items send
					var getelecboxitem = ui.item[0].id; // Which item
					jQuery.ajax({
						url: "{{url('/eci/savesortabledata')}}",
						type: 'GET',
						data: {ST_CODE:getelecboxitem,scheduleelecdata:scheduleelecdata,SCHEDULEID:scheduleelecdata,electionid:electionid,consttype:consttype,StatePHASE_NO:StatePHASE_NO,ELECTION_TYPEID:ELECTION_TYPEID,YEAR:YEAR,CURRENTELECTION:CURRENTELECTION},
						success: function(result){
							//alert(result);
							location.reload();
						}
					});
			}
		},
	});
	jQuery("#sortable3, .selectAC, .scheduleelecdata").disableSelection();
	
	jQuery('.showac').hide();
	/*jQuery('.readmore').click(function(){
		var moreid = jQuery(this).attr('data-id');
		var txt = jQuery(".showac").is(':visible') ? 'Read More' : 'Read Less';
		jQuery(".show_hide").text(txt);
		jQuery('#' + moreid + ' ul.showac').show();
	});*/
	jQuery(".show_hide").on("click", function () {
		var moreid = jQuery(this).attr('data-id');
        var txt = jQuery(".showac."+moreid).is(':visible') ? 'Read More' : 'Read Less';
        jQuery(".show_hide").text(txt);
        jQuery(this).next('.showac').slideToggle(200);
    });

});
</script>
@endsection