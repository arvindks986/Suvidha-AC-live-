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
				  <div class="profileStep profileStepPassive profileStepOne">
					<div class="connect"></div>
					<div class="icon">Finalize Election Listing</div>
				  </div>
				  <div class="profileStep profileStepPassive profileStepOne">
					<div class="connect"></div>
					<div class="icon">Add Schedule Details</div>
				  </div>
				  <div class="profileStep profileStepActive profileStepOne">
					<div class="icon">Announce Election</div>
				  </div>
			</div>
		</div>
            @if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

		  <?php //echo ('<pre>');print_r($showElectionDetail);echo ('</pre>');?>
				<form class="form-horizontal" method="post" action="createschedulenew">
				
				<input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
					  <div class="form-group">
						<label class="control-label col-sm-4" for="doa">Date of Announcement <span class="errorred">*</span> </label>
						<div class="col-sm-8">
						  <input type="text" class="form-control" value="{{ date("d-m-Y",strtotime($showElectionDetail[0]->DT_PRESS_ANNC)) }}" data-date="" data-date-format="dd/mm/yy" id="doa" name="doa" placeholder="Date of Announcement" readonly>
						  <div class="doaerrormsg errormsg errorred"></div>
						</div>
					  </div>
					  <div class="form-group">
						<label class="control-label col-sm-4" for="countingdate">Date of Counting: <span class="errorred">*</span></label>
						<div class="col-sm-8">
						  <input type="text" class="form-control" name="countingdate" value="{{ date("d-m-Y",strtotime($showElectionDetail[0]->DATE_COUNT)) }}" id="countingdate" placeholder="Enter Date of Counting" data-date="" data-date-format="DD-MM-YYYY" readonly>
						  <div class="docerrormsg errormsg errorred"></div>
						</div>
					  </div>
					  <div class="form-group">
						<label class="control-label col-sm-4" for="dateofpollcomp">Date of Election Completion: <span class="errorred">*</span></label>
						<div class="col-sm-8">
						  <input type="text" class="form-control" name="dateofpollcomp" value="{{ date("d-m-Y",strtotime($showElectionDetail[0]->DTB_EL_COM)) }}" id="dateofpollcomp" placeholder="Date of Election Completion" data-date="" data-date-format="DD/MM/YYYY" readonly>
						  <div class="eleccomperrormsg errormsg errorred"></div>
						</div>
					  </div>
					  <div class="form-group">
						<label class="control-label col-sm-4" for="pwd">Select Election: <span class="errorred">*</span></label>
						<div class="col-sm-8"> 
							<select id="select-election" name="selectelectiontypeid">
								<option value="">--Select Election--</option>
								@foreach($list_election as $list)
								<option @if($electiontypeid ==  $list->election_id ) selected @endif value="{{ $list->election_id }}">
									{{ $list->election_sort_name."-".$list->election_type }}
								</option>
								@endforeach
							</select>
							<div class="electiontypeerrormsg  errormsg errorred"></div>
						</div>
					  </div>
					  <div class="form-group">
						<label class="control-label col-sm-4" for="pwd">States: <span class="errorred">*</span></label>
						<div class="col-sm-8">
							
							<?php $getschlist = schListingByElectionId($electiontypeid,$showElectionDetail[0]->DT_PRESS_ANNC);
							//print_r($getschlist);
							//echo ('<pre>');print_r($getschlist);echo ('</pre>'); 
							?>
							<div class="multipleStates" id="checkStateArray">
								<input type="checkbox" name="all" id="checkall" /> Check All</br>
								<?php $getstcode = array() ;?>
								<?php /*@foreach($getschlist as $getlist)
									<?php $getstcode[]  = $getlist->ST_CODE ;?>
								@endforeach
								@foreach($states as $liststate)
									@if($liststate->ST_CODE == $getlist->ST_CODE)
										{{implode(' ', $getstcode) }}
									@else
										<input type="checkbox" class="state-element states" name="states[]" value="{{ $liststate->ST_CODE }}" data-value="{{ $liststate->ST_NAME }}"/> {{ $liststate->ST_NAME }}<br/>
									@endif
								@endforeach 
								@foreach($states as $liststate)
									@foreach($getschlist as $getlist)
										
											<input type="checkbox" checked="checked" class="state-element states" name="states[]" value="{{ $liststate->ST_CODE }}" data-value="{{ $liststate->ST_NAME }}" @if($getlist->ST_CODE == $liststate->ST_CODE) checked @endif/> {{ $liststate->ST_NAME }}<br/>
										
									@endforeach
								@endforeach */?>
							</div>
							<div class="stateerrormsg errormsg errorred"></div>
							<div class="selectedstates"></div>
						</div>
					  </div>
					  <div class="form-group">
						<label class="control-label col-sm-4" for="pwd">Total Schedule: <span class="errorred">*</span></label>
						<div class="col-sm-8"> 
						  <input type="text" class="form-control" id="totalschedule" @if(count($showElectionDetail) > 0) value="{{count($showElectionDetail)}}" @endif name="totalschedule" placeholder="Total No. of Phases">
						  
						  <div class="panel-group" id="accordion">
							<div class="panel panel-default">
							@if(!empty(count($showElectionDetail)))
								<?php $i = 0; ?>
								@foreach($showElectionDetail as $showElec)
									<?php  $i++; ?>
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $i }}">
												Schedule Detail {{ $i }}
											</a>
										</h4>
									</div>
									<div id="collapse{{ $i }}" class="panel-collapse collapse in">
										<div class="panel-body">
											<label class="control-label col-sm-4" for="pwd">
												Date of poll:
											</label>
											<div class="col-sm-8"> 
												<input type="text" readonly="" onclick="getpolldatepicker()" id="polldate1" class="form-control polldate hasDatepicker" name="polldate{{ $i }}" required="required" placeholder="Enter Poll Date" value="{{ date("d-m-Y",strtotime($showElec->DATE_POLL)) }}">
												<div class="polldateerrormsg errormsg errorred"></div>
											</div>
										</div>
									</div>
								@endforeach
							@endif
							</div>
						</div>
						<div class="totalscherrormsg errormsg errorred"></div>
				     </div>
					 <div class="col-sm-4"></div>
   					 <div class="col-sm-8">
						<div class="schdata"></div>
					 </div>
					  <div class="form-group">
						<label class="control-label col-sm-4" for="pwd">Order: <span class="errorred">*</span></label>
						<div class="col-sm-8"> 
						  <input type="file" class="form-control" name="orderupload">
						</div>
					  </div>
					  <div class="form-group"> 
						<div class="col-sm-offset-4 col-sm-8">
						  <button type="submit" id="schedulebtn" class="btn btn-default">Submit</button>
						</div>
					  </div>
					</form>
          
         
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
<script>
jQuery(document).ready(function() {
	//Get Election Type
	jQuery('#select-election').change(function() {
		var electionType = jQuery(this).val();
		if(electionType == 1)
		{
			jQuery('#checkall').prop('checked', true);
			jQuery('.state-element').prop('checked', true);
			jQuery('.selectedstates').html("You have selected all states" );
		}else if(electionType == 2){
			jQuery('.state-element').attr('checked', false);
			jQuery('#checkall').attr('checked', false);
			jQuery('.selectedstates').html('');
		}else if(electionType == 3){
			jQuery('.state-element').attr('checked', false);
			jQuery('#checkall').attr('checked', false);
			jQuery('.selectedstates').html('');
		}else if(electionType == 4){
			jQuery('.state-element').attr('checked', false);
			jQuery('#checkall').attr('checked', false);
			jQuery('.selectedstates').html('');
		}else{
			jQuery('.state-element').attr('checked', false);
			jQuery('#checkall').attr('checked', false);
			jQuery('.selectedstates').html('');
		}
	});
	//Add Schedule
	jQuery('input[name="totalschedule"]').keyup(function(){
	jQuery('.schdata').empty();
	var html = '';
		nof = jQuery(this).val() ;
		html = html + '<div class="partners">';
		for(var x = 1;  x <= nof; x++) {
		html = html + '<div class="panel-group" id="accordion">';  
		
		html = html + '<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse'+x+'">Schedule Detail '+x+'</a></h4></div><div id="collapse'+x+'" class="panel-collapse collapse in"><div class="panel-body"><label class="control-label col-sm-4" for="pwd">Date of poll:</label><div class="col-sm-8"> <input type="text" readonly onclick="getpolldatepicker()" id="polldate'+x+'" class="form-control polldate" name="polldate'+x+'" required="required" placeholder="Enter Poll Date"><div class="polldateerrormsg errormsg errorred"></div></div><div></div></div>';
		
		html = html + '</div>';
		html = html + '</div>';
		  jQuery('.schdata').html(html);
		  jQuery(function() {
				jQuery( ".polldate" ).datepicker({ dateFormat: "dd-M-yy",minDate : new Date()});
		  });
		}
		var html_end = '</div>';
		jQuery('.schdata').append(html_end);
	});
	
	// Check all state element
	jQuery('#checkall').change(function () {
		jQuery('.state-element').prop('checked',this.checked);
		var states = [];
		jQuery.each(jQuery(".state-element:checked"), function(){
			var selstates = jQuery(this).val();
			states.push(selstates);
		});
		var selstatescount = jQuery('.state-element:checked').length ;
		if(selstatescount == 1){
			jQuery('.selectedstates').html("You have selected " + selstatescount +" state - "+ states.join(", ") );
		}else{
			jQuery('.selectedstates').html("You have selected all states" );
		}
	});
	
    // Get selected states value and length
	jQuery('.state-element').change(function () {
		if (jQuery('.state-element:checked').length == jQuery('.state-element').length){
			jQuery('#checkall').prop('checked',true);
		}
	    else {
			jQuery('#checkall').prop('checked',false);
			var states = [];
			jQuery.each(jQuery(".state-element:checked"), function(){
				var selstates = jQuery(this).attr('data-value');
				states.push(selstates);
			});
			var selstatescount = jQuery('.state-element:checked').length ;
			if(selstatescount == 1){
				jQuery('.selectedstates').html("You have selected " + selstatescount +" state - "+ states.join(", ") );
			}else if(selstatescount == 36){
				jQuery('.selectedstates').html("You have selected all states" );
			}else{
				jQuery('.selectedstates').html("You have selected " + selstatescount +" states - "+ states.join(", ") );
			}
		}
	});
	
    // Validations
	jQuery('#schedulebtn').click(function(){
		var doa = jQuery('#doa').val();
		var countingdate = jQuery('#countingdate').val();
		var dateofpollcomp = jQuery('#dateofpollcomp').val();
		var totalschedule = jQuery('#totalschedule').val();
		
		if(doa == ''){
			jQuery('.errormsg').html('');
			jQuery('.doaerrormsg').html('Enter date of announcement');
			jQuery( "input[name='doa']" ).focus();
			return false;
		}
		if(countingdate == ''){
			jQuery('.errormsg').html('');
			jQuery('.docerrormsg').html('Enter date of counting');
			jQuery( "input[name='countingdate']" ).focus();
			return false;
		}
		if(new Date(countingdate) <= new Date(doa))
		{
			jQuery('.errormsg').html('');
			jQuery('.docerrormsg').html('Counting date should be after date of announcement');
			jQuery( "input[name='countingdate']" ).focus();
			return false;
		}
		if(dateofpollcomp == ''){
			jQuery('.errormsg').html('');
			jQuery('.eleccomperrormsg').html('Enter date of election completion');
			jQuery( "input[name='dateofpollcomp']" ).focus();
			return false;
		}
		if(new Date(dateofpollcomp) <= new Date(countingdate))
		{
			jQuery('.errormsg').html('');
			jQuery('.eleccomperrormsg').html('Election completion date should be after counting date');
			jQuery( "input[name='dateofpollcomp']" ).focus();
			return false;
		}
		if(jQuery('#select-election').val() == ''){
			error = 1
            jQuery('.errormsg').html('');
			jQuery('.electiontypeerrormsg').html('Please select election');
			jQuery( "input[name='selectelectiontypeid']" ).focus();
			return false;
		}
		if (!(jQuery('.states').is(':checked'))) {
            error = 1
            jQuery('.errormsg').html('');
			jQuery('.stateerrormsg').html('Please select states');
			jQuery( "input[name='states[]']" ).focus();
			return false;
        }
		if(totalschedule == ''){
			jQuery('.errormsg').html('');
			jQuery('.totalscherrormsg').html('Enter total no of schedule');
			jQuery( "input[name='totalschedule']" ).focus();
			return false;
		}
		for (i = 1; i <= parseInt(totalschedule); i++) { 
			var polldate = jQuery('#polldate'+totalschedule).val();
			if(polldate == ''){
				jQuery('.errormsg').html('');
				jQuery('.polldateerrormsg').html('Enter Poll Date');
				jQuery( polldate ).focus();
				return false;
			}
			if(new Date(polldate) <= new Date(doa))
			{
				jQuery('.errormsg').html('');
				jQuery('.polldateerrormsg').html('Poll date should be after poll date');
				jQuery( "#polldate"+totalschedule ).focus();
				return false;
			}
			if(new Date(countingdate) <= new Date(polldate))
			{
				jQuery('.errormsg').html('');
				jQuery('.polldateerrormsg').html('Poll date should be before poll date');
				jQuery( "#polldate"+totalschedule).focus();
				return false;
			}
		}
	});
});
</script>
@endsection