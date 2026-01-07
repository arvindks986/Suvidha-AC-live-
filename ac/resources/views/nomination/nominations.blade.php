@extends('layouts.theme')
@section('title', 'Nomination')
@section('content')
<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
<title>My Nominations</title>
</head>
<script>

function deleteNomination(id, nom, st, ac){ 
	$("#copyloadDelete").hide();
	$("#deleteId").val(id);
	$('#delete_nomination_'+id).modal('show');
}
function finalizeNomination(id, nom_primary_id){ 
	var dsd = jQuery.noConflict();	
	dsd("#messageNeedToShow").val(id);
	dsd("#nom_primary_id").val(nom_primary_id);
	dsd('#finalizeNomination').modal('show');
}

function showLoaderP(){
var fnp = jQuery.noConflict();	
var nom_primary_id = fnp("#nom_primary_id").val();
fnp("#fnp").show();
$.ajax({
type: "POST",
url: "<?php echo url('/'); ?>/nomination/finalize-nomination-payment", 
data: {
"_token": "{{ csrf_token() }}",
"nom_primary_id": nom_primary_id,
},
dataType: "html",
	success: function(msg){ console.log(msg);
			if(msg==1){	
				var messageNeedToShow=fnp("#messageNeedToShow").val(); 
				fnp("#online_pay4_"+nom_primary_id).show();
				fnp("#online_pay2_"+nom_primary_id).hide();
				fnp("#online_pay2_"+nom_primary_id).hide();
				fnp("#fnp").hide();
				fnp("#Edit_"+nom_primary_id).hide();
				fnp("#Delete_"+nom_primary_id).hide();
				fnp('#finalizeNomination').modal('hide');
				fnp('#successNomination').modal('show');

			} else {
				fnp("#failed").html("<?php echo __('messages.delIssue'); ?>"); 
				fnp("#online_pay4_"+nom_primary_id).show();
				fnp("#online_pay2_"+nom_primary_id).hide();
				fnp("#fnp").hide();
				fnp('#finalizeNomination').modal('hide');
				fnp('#successNomination').modal('show');
			}

	},
error: function(error){
fnp("#fnp").hide();
console.log(error);
console.log(error.responseText);				
var obj =  $.parseJSON(error.responseText);
}
});
		}


function doDelete(st, ac){
	$("#copyloadDelete").show();
	var id =  $("#deleteId").val();
	
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/delete-nomination", 
		data: {
			"_token": "{{ csrf_token() }}",
			"id": id, 
			"st": st,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
			if(msg==1){ 
				$('#delete_nomination_'+id).modal('hide');
				$('#messageAfterDelete').modal('show');
				$('#textMessages').html("<?php echo __('messages.delSucc'); ?>");
				$('#'+id).hide();
			} else {
				$('#delete_nomination_'+id).modal('hide'); 
				$('#messageAfterDelete').modal('show');
				$('#textMessages').html("<?php echo __('messages.delIssue'); ?>");
			}
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	  });
}


function copyNomination(id, nom, st, ac){
	$("#copyload").hide();
	$("#clickid").val(id);
	$("#clicknom").val(nom);
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/copy-nomination", 
		data: {
			"_token": "{{ csrf_token() }}",
			"id": id,
			"nom": nom,
			"st": st,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
			if(msg >= 4){
				      $('#already4Filled').modal('show');
				      return false;
			}else{
					  var j=jQuery.noConflict();	
					  $('#copy_nomination_'+id).modal('show');
			}
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	  });
}


function doCopy(st, ac){
	$("#copyload").show();
	var id =  $("#clickid").val();
	var nom = $("#clicknom").val();
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/do-copy", 
		data: {
			"_token": "{{ csrf_token() }}",
			"id": id,
			"nom": nom,
			"st": st,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
			if(msg >= 4){
				$("#copyload").hide();
				$('#already4Filled').modal('show');
				return false;
			} else {
				window.location.href="apply-nomination-finalize";
			}
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	  });
}
</script>

<main class="pt-3 pb-5 pl-5 pr-5">
	<section>
	@if(count($errors->all())>0)
		  <div class="container">
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $iterate_error)
              <li><p class="text-left">{!! $iterate_error !!}</p></li>
              @endforeach
            </ul>
          </div>
		  </div>    	
    @endif
		  
	@if (session('flash-message'))
		<div class="container">
			<div class="row">
           @if (session('flash-message'))
           <div class="alert alert-success"> {{session('flash-message') }}</div>
           @endif
		</div>    
    @endif
    </section>	
  <?php $result=array(); $result['id']='NA'; ?>
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-8 col-12">
            <h4>{{ __('nomination.Nominations') }}</h4>
          </div>
		  <?php   
				  $isDraData = 0;	
				  $stmd='';
				   if(!empty($_REQUEST['std'])){
					$stmd = decrypt_String($_REQUEST['std']);
					}
					$receipt='';
					if(!empty($stmd)){
					$receipt = 	app(App\Http\Controllers\Nomination\NominationController::class)->getReceipt($stmd);
					}
		  ?>


          @if(!empty($receipt))
		  <div class="col-md-4 col-12" style="float:right;">{{ __('messages.Receipt') }}: <span><a href="{{$receipt}}" target="_blank">{{ __('messages.Download') }}</a></span></div>
		  @endif	
		  
        </div>
      </div>
      <div class="custom-tab-area mt-3"> 
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
          <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#submtd">{{ __('nomination.Submitted') }}</a> </li>
          <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#drft">{{ __('nomination.Draft') }}</a> </li>
        </ul>
        <div class="card card-shadow mt-4">
          <div class="card-body p-0"> 
            <!-- Tab panes -->
            <div class="tab-content">
              <div id="submtd" class="tab-pane active">
				<?php $i=0; $k=1;  
					$acdd=''; $stdd=''; 

					if(!empty($results)){   
					if(!empty($_REQUEST['acs']) && ($_REQUEST['std'])){
					$acdd = decrypt_String($_REQUEST['acs']);
					$stdd = decrypt_String($_REQUEST['std']);
					}
				
					$passst='';
					$passac='';	
				?>	
					
				
					<!-- Delete Nomination Modek -->
						
						<div class="modal fade modal-confirm" id="messageAfterDelete">
						<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
						  <div class="modal-content">
						   <div class="pop-header pt-3 pb-1">
							  <div class="animte-tick"><span>&#10003;</span></div>	
							  <h5 class="modal-title"></h5>
							<div class="header-caption">
							  <p style="color:white;" id="textMessages"></p>	
							  <ul class="list-inline">
								<li class="list-inline-item mr-4"></li>
							  </ul>
							</div>		
							</div>
							 <div class="confirm-footer">
							  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>&nbsp;&nbsp;&nbsp;
							</div>
						  </div>
						</div>
						</div>
						
						
					<div class="modal fade modal-confirm" id="already4Filled">
					<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
					  <div class="modal-content">
					   <div class="pop-header pt-3 pb-1">
						<div class="animte-tick"><span>&#10003;</span></div>	
						<h5 class="modal-title" id="failed">{{ __('messages.copnom') }}</h5> 
						<div class="header-caption">
						</div>		
						</div>
						<div class="modal-body" style="text-align: center;">
						<ul>
						<li><label  id="oneqqq"><?php echo __('messages.4filled'); ?></li>
						 </ul> 
						</div>
						<div class="confirm-footer"> 
						<button type="button" id="can" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('nomination.ok') }}</button>
						</div>
					  </div>
					</div>
				  </div><!-- End Of confirm Modal popup Div -->
						
						
					<div class="modal fade modal-confirm" id="successNomination">
					<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
					  <div class="modal-content">
					   <div class="pop-header pt-3 pb-1">
						<div class="animte-tick"><span>&#10003;</span></div>	
						<h5 class="modal-title" id="failed">{{ __('messages.fn') }}</h5> 
						<div class="header-caption">
						</div>		
						</div>
						<div class="modal-body" style="text-align: center;">
						<ul>
						<li><label  id="oneqqq">{{ __('messages.nmf') }}</li>
						 </ul> 
						</div>
						<div class="confirm-footer"> 
						<button type="button" id="can" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('nomination.ok') }}</button>
						</div>
					  </div>
					</div>
				  </div><!-- End Of confirm Modal popup Div -->

						
						
						
						
					<div class="modal fade modal-confirm" id="finalizeNomination">
					 <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/finalize-nomination-payment') }}" autocomplete='off' enctype="x-www-urlencoded">
					{{ csrf_field() }}
					<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
					  <div class="modal-content">
					   <div class="pop-header pt-3 pb-1">
						<div class="animte-tick"><span>&#10003;</span></div>	
						<h5 class="modal-title">{{ __('messages.fn') }}</h5> 
						<div class="header-caption">
						</div>		
						</div>
						<div class="modal-body" style="text-align: center;">
						<ul>
						<li><label  id="oneqqq">{{ __('messages.fnsure') }}</li>
						 </ul> 
						</div>
						<div class="confirm-footer" id="oneqqq2"> 
						<input type="hidden" id="messageNeedToShow">
						<input type="hidden" id="nom_primary_id">
						<button type="button" id="can" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
						<button type="button" class="btn btn-primary" style="background: #bb4292; border: none;" id="reareyu" onclick="return showLoaderP();">
						{{ __('messages.Yes') }}</button>
						</div>
						<button id="oneqqq3" type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;display:none;">{{ __('nomination.ok') }}</button>
						
						  <span style="text-align: center;display:none;" id="fnp">
						 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
						</span>
					  
						
					  </div>
					</div>
					</form>
				  </div><!-- End Of confirm Modal popup Div -->
						
				   <?php   $iddataa = 0;
						   $iddataa = app(App\Http\Controllers\Nomination\NominationController::class)->getdateNom($stdd, $acdd);
					?>
					
					
                    <div class="tab-body tab-panel-bg" style="padding-bottom: 65px;">
				    <div style="padding: 15px; width: 48%;">
					
					
					<select name="acs" class="form-control" onchange="return changeDish(this.value);">
					<option value=""> {{ __('nomination.select_ac') }} </option>
					@if(count($submittedpre)> 0 )
					@foreach($submittedpre as $resd)	
					<?php 
					
					$passst=encrypt_string($resd['st_code']);
					$passac=encrypt_string($resd['ac_no']);
					?>
					<option @if(($resd['st_code']==$stdd) && ($resd['ac_no']==$acdd)){{'selected'}}@endif value="{{$passac.'***+++'.$passst}}">{{$resd['state']}}-{{$resd['ac_name']}}</option>
					@endforeach
					@endif
					</select>
					</div>	
					
					
                  
				  
				  <div class="row">
				
                @foreach($results as $result)
				@if($result['is_finalize'] == 1)
				<?php //echo "<pre>"; print_r($result);
					$ddd= __('nomination.NA');
					$exp = '';
					$cng = 0;
					//
					$nid=encrypt_string($result['id']);

					if(isset($result['appoinment_scheduled_datetime']) && ($result['appoinment_scheduled_datetime']!=='0000-00-00 00:00:00')){
					$exp = explode(" ", $result['appoinment_scheduled_datetime']);	
						$time = strtotime($result['appoinment_scheduled_datetime']);
						$ddd =  date("d M Y", $time).' '.$exp[1];
					}
					$send='';
					
					$send='apply-nomination-step-2?nid='.$nid.'&setintosession=ppp';
					
					$scrutiny='';	$yes=0; $background=''; $raise='';
					
					if($result['is_appoinment_scheduled']==1 && $result['appoinment_status']!=2){
						$scrutiny= __('nomination.Appointment_Scheduled');
						$background="background:pink";
						$yes=1;
					}
					if($result['is_appoinment_scheduled']==1 && $result['appoinment_status']==2){
						$scrutiny=__('nomination.Appointment_Canceled');
						$yes=3;
						$background="background:red;color: white; padding: 2px;";
					}
					if($result['is_appoinment_scheduled']==2 && $result['is_appoinment_scheduled']!=2){ 
						$scrutiny=__('nomination.Pending');
					}
					if($result['prescrutiny_status']==2){ 
						$raise=__('nomination.Query_Raised');
						$background="background:yellow;";
						$yes=2;
					}
					if($result['prescrutiny_status']==1 && $yes==0){ 
						$background="background:green;color:white;padding:2px;";
				    }
					
					if($result['is_appoinment_scheduled']==1 && $result['appoinment_status']=='' && $result['prescrutiny_status']==2){ 
						$raise=__('nomination.Appointment_Scheduled_with_query');
						$background="background:pink";
						$yes=2;
						$cng=1;
					}
					/////////////////////////
					
					
					$n=0; $pre=0; $defected=0; $noned=0; 
					$st='';
					if($result['is_apply_prescrutiny']==1  && $result['prescrutiny_status']!=1  && $result['prescrutiny_status']!=2 
					&& $result['is_appoinment_scheduled']!=1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2   ){ 
					$st= __('nomination.Pre_scrutiny_submitted');
					$n=1;
					}
					if($result['is_apply_prescrutiny']==1  && $result['prescrutiny_status']==1  && $result['prescrutiny_status']!=2 
					&& $result['is_appoinment_scheduled']!=1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2   ){ 
					$st= __('nomination.Pre_scrutiny_cleared');
					$n=2;
					}
					if($result['is_apply_prescrutiny']==1  && $result['prescrutiny_status']!=1  && $result['prescrutiny_status']==2 
					&& $result['is_appoinment_scheduled']!=1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2   ){ 
					$st=__('nomination.Pre_scrutiny_Defect');
					$n=3;
					}
					if($result['is_apply_prescrutiny']==1  && $result['prescrutiny_status']!=1  && $result['prescrutiny_status']==2 
					&& $result['is_appoinment_scheduled']!=1){ 
					$st=__('nomination.Pre_scrutiny_Defect');
					$n=3;
					//$pre=1;
					}
					
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']==1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2 && $result['prescrutiny_status']!=2   ){ 
					
					$st=__('nomination.Appointment_Scheduled');
					$n=4;
					}
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']==1 && $result['prescrutiny_status']==1){ 
					$datadd=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($result['nomination_no']); 
					if($datadd!='0'){ 
					   $st=$datadd;	
					   $n=4;	
					}
					}
					
					
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']!=1 && $result['prescrutiny_status']==2  ){ 
					$datadd=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($result['nomination_no']); 
					if($datadd!='0'){ 
					   $st=$datadd.", ". __('nomination.Defected_Nomination'); 	
					   //$st="Nomination Cancelled, Defected Nomination";	
					   $defected=1;
					   $n=4;
					}
					}
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']!=1 && $result['appoinment_status']!=1 && $result['appoinment_status']==2 && $result['prescrutiny_status']==1  ){ 
					$datadd=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($result['nomination_no']); 
					if($datadd!='0'){ 
					     $st=$datadd.", ". __('nomination.Defected_Nomination'); 				   
						//$st="Nomination Cancelled";	
						$noned=1; 
						$n=4;
					}
					}
					
					
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']==1  && $result['prescrutiny_status']==2  && $result['prescrutiny_status']!=1 ){ 
					$datadd=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($result['nomination_no']); 
					if($datadd!='0'){ 
					    $st=$datadd.", Defected Nomination";					   
						//$st="Appoinment Scheduled, Defected Nomination";	
						$defected=1;
						$n=4;
						$pre=1;
					} else {
						$st= __('nomination.Appointment_Scheduled'); 
						$defected=1;
						$n=4;
						$pre=1;
						}
					}
					
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']!=1 && $result['prescrutiny_status']==2 && $result['appoinment_status']==2  ){ 
					$st= __('nomination.Cancel_Defected_Nomination'); 
				    $defected=1; 
				    $n=4;
					}
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']!=1 && $result['prescrutiny_status']==1 && $result['appoinment_status']==2  ){ 
					$st= __('nomination.Cancel');
				    $n=4;
					}
					
					/*if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']==1 && $result['appoinment_status']==2   ){ 
					$st="Appointment not cleared";	
					$n=6;
					} */
					if($st==''){
						$st= __('nomination.Not_Submitted');						
						$n=7;
					}
					
					$link=encrypt_string($result['prescrutiny_date']);
					if(!empty($result['appoinment_scheduled_datetime'])){
					    $exp = '';
						$exp = explode(" ", $result['appoinment_scheduled_datetime']);
						$yrdata= strtotime($result['appoinment_scheduled_datetime']);
						$dt = date('d M Y', $yrdata).' ';
					
					} else {
						if(!empty($result['prescrutiny_date'])){
								$exp = '';
								$exp = explode(" ", $result['prescrutiny_date']);
								$yrdata= strtotime($result['prescrutiny_date']);
								$dt = date('d M Y', $yrdata).' ';
							
						}
					}	
					//echo "<pre>"; print_r($result);
					?>
					
					<!-- Copy Nomination -->
					<div class="modal fade modal-confirm" id="copy_nomination_<?php echo $result['id']; ?>">
						<input type="hidden" id="clickid">
						<input type="hidden" id="clicknom">
						<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
						  <div class="modal-content">
						   <div class="pop-header pt-3 pb-1">
							  <div class="animte-tick"><span>&#10003;</span></div>	
							  <h5 class="modal-title"></h5>
							<div class="header-caption">
							  <p style="color:white;">{{ __('messages.copyMess') }}</p>	
							  <ul class="list-inline">
								<li class="list-inline-item mr-4"></li>
							  </ul>
							</div>		
							</div>
							 <div class="confirm-footer">
							  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.Cancel') }}</button>&nbsp;&nbsp;&nbsp;
							<button type="submit" class="btn dark-purple-btn" onclick="return doCopy('<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');">{{ __('nomination.Yes') }}</button>
							</div>
							<span style="text-align: center;display:none;" id="copyload">
								 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
							</span>
						  </div>
						</div>
						</div>
						<!-- Delete Nomination Modek -->
						<div class="modal fade modal-confirm" id="delete_nomination_<?php echo $result['id']; ?>">
						<input type="hidden" id="deleteId">
						<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
						  <div class="modal-content">
						   <div class="pop-header pt-3 pb-1">
							  <div class="animte-tick"><span>&#10003;</span></div>	
							  <h5 class="modal-title"></h5>
							<div class="header-caption">
							  <p style="color:white;">{{ __('messages.deleNom') }}</p> 	
							  <ul class="list-inline">
								<li class="list-inline-item mr-4"></li>
							  </ul>
							</div>		
							</div>
							 <div class="confirm-footer">
							  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.Cancel') }}</button>&nbsp;&nbsp;&nbsp;
							<button type="submit" class="btn dark-purple-btn" onclick="return doDelete('<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');">{{ __('nomination.Yes') }}</button>
							</div>
							<span style="text-align: center;display:none;" id="copyloadDelete">
								 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
							</span>
						  </div>
						</div>
						</div>
					
				    @if($n==2)
					
                    <div class="col-sm-6 col-12" id="{{$result['id']}}">
                      <div class="appnt-detail list-detail"> <span class="semi-circle-left"></span> <span class="semi-circle-right"></span>
                        <div class="d-flex justify-content-between align-items-center py-1 px-4 cleared">
                           <div class="cunt">{{$k}}</div>
                          <div class="ttle">
                            <h2>{{ __('messages.apf') }}</h2>
                          </div>
                          <div class="symbl tck"><i class="fa fa-check" aria-hidden="true"></i></div>
                        </div>
						
					@if($iddataa==0)	
						<div class="nomin-btns d-flex justify-content-between">
						@if($result['finalize_after_payment']!=1)	
						
									<span id="Edit_<?php echo $result['id']; ?>"> <a href="{{$send}}" ><i class="fa fa-pencil" aria-hidden="true"></i> <span>{{ __('nomination.Edit') }}</span> </a> </span>
								
								<span id="Delete_<?php echo $result['id']; ?>" onclick="return deleteNomination( '<?php echo $result['id']; ?>',  '<?php echo $result['nomination_no']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');"><i class="fa fa-trash-o" aria-hidden="true"></i>
								{{ __('messages.Delete') }} 
								</span> 
								@endif	
								
								
								<span onclick="return copyNomination( '<?php echo $result['id']; ?>',  '<?php echo $result['nomination_no']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');"><i class="fa fa-clone" aria-hidden="true"></i>
								{{ __('messages.copnom') }}
								</span>	
								
						<span style="display:none;" id="online_pay4_<?php echo $result['id']; ?>"><a><i class="fa fa-check-circle-o" aria-hidden="true"></i> <span>{{ __('messages.nmf') }}
						</span></a></span>	
						
						
						<?php 
						$psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus($result['id']);
						$isChallanSubmitted = app(App\Http\Controllers\Nomination\NominationController::class)->getChallan($result['st_code'], $result['ac_no']);
						?>
						@if((count($psta)>0) or (count($isChallanSubmitted)>0))
							@if($result['finalize_after_payment']!=1)	
							<span><a id="online_pay2_<?php echo $result['id']; ?>" onclick="return finalizeNomination('online_pay2', '<?php echo $result['id']; ?>');"><i class="fa fa-check-circle-o" aria-hidden="true"></i>  <span>{{ __('messages.fn') }}</span></a></span>
							@else 
							<span><a id="online_pay3_<?php echo $result['id']; ?>"><i class="fa fa-check-circle-o" aria-hidden="true"></i> <span>{{ __('messages.nmf') }}</span></a></span>	
						@endif			
						@endif
						</div>
					@endif
							
						
						
                        <ul>
                          <li class="rmark d-flex justify-content-between"> <strong>{{ __('nomination.Remark') }}</strong> 
                            <div class="rmark-detail" style="width:89%;">
                              <div class="rmark-full-info">
                                <div class="targetDiv" id="div1">
                                  <p style="text-align: justify;">{{ __('nomination.Remark_text1') }}</p>
                                </div>
                              </div>
                            </div>
                          </li>
						  
						  <?php //echo "<pre>"; print_r($result); ?>
						  
                          <li><strong>{{ __('nomination.Nomination_No') }}</strong> <span>{{$result['nomination_no']}}</span></li>
                          <li><strong>{{ __('nomination.Name') }}</strong> <span>{{$result['name']}}</span></li>
                          <li><strong>{{ __('nomination.Election') }}</strong> <span>{{$result['election_name']}}</span></li>
                          <li><strong>{{ __('nomination.State') }}</strong> <span>{{$result['state']}}</span></li>
                          <li><strong>{{ __('nomination.ac') }}. &amp; {{ __('nomination.Name') }}</strong> <span>{{$result['ac_name']}}</span></li>
                          <li><strong>{{ __('nomination.Party') }}</strong> <span>{{$result['party_name']}}</span></li>
                          <!--<li><strong>{{ __('nomination.Status') }}</strong> <span>{{$st}}</span></li>-->
						</ul>
						
                        <div class="row p-3">
                          <div class="col-md-4 col-12"><strong>{{ __('nomination.Nomination') }}</strong></div>
                          <div class="col-md-8 col-12 text-right">
                            <div class="apt-btn"><a href="{{$result['view_href']}}" class="btn dark-pink-btn">{{ __('nomination.View') }}</a> 
							<a href="{{$result['download_href']}}" class="btn dark-purple-btn">{{ __('nomination.DownloadnPrint') }}</a> </div>
                          </div>
                        </div>
						
						<div class="row p-3">
                          <div class="col-md-4 col-12"><strong> {{ __('messages.e-Affidavit') }}  </strong></div> 
                          <div class="col-md-8 col-12 text-right">
                            <div class="apt-btn"><a href="<?php echo url('/') ?>/part-a-detailed-report?affidavit_id=<?php echo $result['assigned_e_affidavit']; ?>" class="btn dark-pink-btn" target="_blank">{{ __('nomination.View') }}</a> 
							<a href="<?php echo url('/') ?>/part-a-detailed-report?pdf=yes&affidavit_id=<?php echo $result['assigned_e_affidavit']; ?>" class="btn dark-purple-btn" target="_blank">{{ __('nomination.DownloadnPrint') }}</a> </div>
                          </div>
                        </div>
						
						
						<form method="post" action="save-affidavit" enctype="multipart/form-data">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <input type="hidden" name="recognized_party" value="recognized">
                          <input type="hidden" name="nomination_id" id="nid" value="{{$result['id']}}">
						
						<div class="col ">
							  <div class="form-group row float-left"> <span style="color: red; margin-top: -9px; position: absolute; margin-left: 13px; font-size: 13px;" id="checkafferror_{{$result['id']}}"></span>
							</div>
						</div>
						
						<fieldset class="fullwidth" id="{{$result['id']}}" style="display:none;margin-top: 50px;">
                              <div id="affidavit-preview_{{$result['id']}}" class="affidavit-preview_{{$result['id']}} min-width">
                                <iframe id="if__{{$result['id']}}" src="" width="100%" height="500"></iframe>
                              </div>
                        </fieldset>
						<!--
						<div class="row p-3">
                          <div class="col-md-4 col-12" style="font-size: 14px;"><strong>{{ __('nomination.Upload_Signed_Copy') }}</strong></div>
                          <div class="col-md-4 col-8" style="margin-left: 180px;">
                            <div > 
							
							<button class="file_{{$result['id']}} btn btn-primary"  type="button" onclick="return uploadPdf({{$result['id']}});">{{ __('nomination.Browse') }} <i class="fa fa-upload"></i></button> 
                            <input type="hidden" name="affidavit" id="affidavit_{{$result['id']}}" class="affidavit_{{$result['id']}}" value="">
							<button type="submit" class="btn btn-primary save_next" onclick="return checkaff({{$result['id']}});" style="margin-left: 4px; position: absolute;">{{ __('nomination.Upload') }} </button>
							
							</div>
                          </div>
                        </div>-->
						
						
							<div class="modal fade modal-cancel" id="cancel_{{$result['id']}}">
							<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
							  <div class="modal-content">
							    <div class="modal-header">
								<h5 class="modal-title" id="areyu">{{ __('nomination.are_you') }} </h5>			
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>
							  </div>
								
								<div class="confirm-footer">
								  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.Cancel') }}</button>&nbsp;&nbsp;&nbsp;
								  <button type="submit" class="btn dark-purple-btn" onclick="submitForm('loader4_<?php echo $result['id']; ?>');">{{ __('nomination.Yes') }}</button>
								</div>
								
								
								
								<span style="text-align: center;display:none;" id="loader4_{{$result['id']}}">
								 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
								</span>
								
							  </div>
							</div>
						   </div>
						</form> 
						<?php $datalink = app(App\Http\Controllers\Nomination\NominationController::class)->getDataLink($result['id']);  ?>
						<?php $stst = app(App\Http\Controllers\Nomination\NominationController::class)->getSchStatus($result['st_code'], $result['ac_no'] ); ?>
						
                        <div class="nomin-foot">
                          <div class="custom-control custom-checkbox">                            
							<!--<input type="checkbox"  class="custom-control-input cls_{{$result['id']}}" id="customCheck-{{$k}}" name="is_scrutiny_completed" value="{{$result['id']}}" onclick="return setRadio('<?php echo $result['id']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');">-->
						@if($stst==0)
							
						<!--<label class="" for="" onclick="return bookAnAppointment('<?php echo $result['id']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');">SCHEDULE APPOINTMENT</label> -->  
						<!--<label class="" for="" >{{ __('nomination.Appointment') }} </label>-->
						
						@else	
							

						<!--	<label  for="customCheck-{{$k}}">
							<a style="color:white;" href="{!! url('nomination/book-details') !!}?query={{$link}}&id=<?php echo $datalink; ?>&data={{$link}}">APPOINTMENT SCHEDULED</a>   </label>-->
								<label  for="customCheck-{{$k}}">
							<a style="color:white;" >{{ __('nomination.Appointment') }}</a>   </label>
						
						@endif
							
						  </div>
                        </div>
                      </div>
                      <!-- End Of appnt-detail Div --> 
                    </div>                    
				    @endif
					
					
					@if($n==4)
					<div class="col-sm-6 col-12" id="{{$result['id']}}">
                      <div class="appnt-detail list-detail"> <span class="semi-circle-left"></span> <span class="semi-circle-right"></span>
                        <div class="d-flex justify-content-between align-items-center py-1 px-4 cleared">
                          <div class="cunt">{{$k}}</div>
                          <div class="ttle">
                            <h2>{{ __('messages.Appointment4') }}</h2>
                          </div>
                          <div class="symbl tck"><i class="fa fa-check" aria-hidden="true"></i></div>
                        </div>
						
					@if($iddataa==0)		
						<div class="nomin-btns d-flex justify-content-between">
						
                    @if($result['finalize_after_payment']!=1)		
						
							<span id="Edit_<?php echo $result['id']; ?>"> <a href="{{$send}}"><i class="fa fa-pencil" aria-hidden="true"></i> <span>{{ __('nomination.Edit') }}</span> </a> 
						   </span>
						
							<span id="Delete_<?php echo $result['id']; ?>" onclick="return deleteNomination( '<?php echo $result['id']; ?>',  '<?php echo $result['nomination_no']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');"><i class="fa fa-trash-o" aria-hidden="true"></i>	{{ __('messages.Delete') }}</span> 
							@endif
							
							<span onclick="return copyNomination( '<?php echo $result['id']; ?>',  '<?php echo $result['nomination_no']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');"><i class="fa fa-clone" aria-hidden="true"></i>	{{ __('messages.copnom') }} </span>
							
							<span style="display:none;" id="online_pay4_<?php echo $result['id']; ?>"><a><i class="fa fa-check-circle-o" aria-hidden="true"></i>  <span> {{__('messages.nmf') }} </span></a></span>	
							
							<?php 
								$psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus($result['id']);
								$isChallanSubmitted = app(App\Http\Controllers\Nomination\NominationController::class)->getChallan($result['st_code'], $result['ac_no']);
								?>
								@if((count($psta)>0) or (count($isChallanSubmitted)>0))
									@if($result['finalize_after_payment']!=1)	
									<span><a id="online_pay2_<?php echo $result['id']; ?>" onclick="return finalizeNomination('online_pay2', '<?php echo $result['id']; ?>');"> <i class="fa fa-check-circle-o" aria-hidden="true"></i> <span>{{ __('messages.fn') }}</span></a></span>
									@else 
							<span><a id="online_pay3_<?php echo $result['id']; ?>"><i class="fa fa-check-circle-o" aria-hidden="true"></i> <span> {{   __('messages.nmf') }}</span></a>
						   </span>	
								@endif			
								@endif 
							
                          </div>
					@endif	  
						  
						  
                        <ul>
                          <li class="rmark d-flex justify-content-between"> <strong>{{ __('nomination.Remark') }}</strong>
                            <div class="rmark-detail" style="width:89%;">
                              <div class="rmark-full-info">
                                <div class="targetDiv" id="div4">
                                  <p style="text-align: justify;">{{ __('nomination.Remark_text4') }}.</p>
                                </div>
                              </div>
                            </div>
                          </li>
						  
						  <li><strong>{{ __('nomination.Nomination_No') }}</strong> <span>{{$result['nomination_no']}}</span></li>
                          <li><strong>{{ __('nomination.Name') }}</strong> <span>{{$result['name']}}</span></li>
                          <li><strong>{{ __('nomination.Election') }}</strong> <span>{{$result['election_name']}}</span></li>
                          <li><strong>{{ __('nomination.State') }}</strong> <span>{{$result['state']}}</span></li>
                          <li><strong>{{ __('nomination.ac') }}. &amp; {{ __('nomination.Name') }}</strong> <span>{{$result['ac_name']}}</span></li>
                          <li><strong>{{ __('nomination.Party') }}</strong> <span>{{$result['party_name']}}</span></li>
                          <!--<li><strong>{{ __('nomination.Status') }}</strong> <span>{{$st}}</span></li>-->
						   @if($defected==1)
						  <!--  &nbsp;<a target="_blank" style="color:red;position: absolute;" href="prescootiny/<?php echo encrypt_string($result['id']);  ?>?acs=<?php echo $_REQUEST['acs'];  ?>&std=<?php echo  $_REQUEST['std'];  ?>" >See</a></span> -->
					      @endif
					   </li>
                        </ul>
                        <div class="row p-3">
                          <div class="col-md-4 col-12"><strong>{{ __('nomination.Nomination') }}</strong></div>
                          <div class="col-md-8 col-12 text-right">
                            <div class="apt-btn"> 
							
							<a href="{{$result['view_href']}}" class="btn dark-pink-btn">{{ __('nomination.View') }}</a> 
							<a href="{{$result['download_href']}}" class="btn dark-purple-btn">{{ __('nomination.DownloadnPrint') }}</a> 							
							</div>
                          </div>
                        </div>						
						<div class="row p-3">
                          <div class="col-md-4 col-12"><strong> {{ __('messages.e-Affidavit') }}</strong></div> 
                          <div class="col-md-8 col-12 text-right">
                            <div class="apt-btn"><a href="<?php echo url('/') ?>/part-a-detailed-report?affidavit_id=<?php echo $result['assigned_e_affidavit']; ?>" class="btn dark-pink-btn" target="_blank">{{ __('nomination.View') }}</a> 
							<a href="<?php echo url('/') ?>/part-a-detailed-report?pdf=yes&affidavit_id=<?php echo $result['assigned_e_affidavit']; ?>" class="btn dark-purple-btn" target="_blank">{{ __('nomination.DownloadnPrint') }}</a> </div>
                          </div>
                        </div>						
						<form method="post" action="save-affidavit" enctype="multipart/form-data">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <input type="hidden" name="recognized_party" value="recognized">
                          <input type="hidden" name="nomination_id" id="nid" value="{{$result['id']}}">						
						<div class="col ">
							  <div class="form-group row float-left"> <span style="color: red; margin-top: -9px; position: absolute; margin-left: 13px; font-size: 13px;" id="checkafferror_{{$result['id']}}"></span>
							</div>
						</div>						
						<fieldset class="fullwidth" id="{{$result['id']}}" style="display:none;margin-top: 50px;">
                              <div id="affidavit-preview_{{$result['id']}}" class="affidavit-preview_{{$result['id']}} min-width">
                                <iframe id="if__{{$result['id']}}" src="" width="100%" height="500"></iframe>
                              </div>
                        </fieldset>						
						<!--<div class="row p-3">
                          <div class="col-md-4 col-12" style="font-size: 14px;"><strong>{{ __('nomination.Upload_Signed_Copy') }}</strong></div>
                          <div class="col-md-4 col-8" style="margin-left: 180px;">
                            <div > 
							
							<button class="file_{{$result['id']}} btn btn-primary"  type="button" onclick="return uploadPdf({{$result['id']}});">{{ __('nomination.Browse') }} <i class="fa fa-upload"></i></button> 
                            <input type="hidden" name="affidavit" id="affidavit_{{$result['id']}}" class="affidavit_{{$result['id']}}" value="">
							<button type="submit" class="btn btn-primary save_next" onclick="return checkaff({{$result['id']}});" style="margin-left: 4px; position: absolute;">{{ __('nomination.Upload') }}</button>
							
							</div>
                          </div>
                        </div>-->
						
						
							<div class="modal fade modal-cancel" id="cancel_{{$result['id']}}">
							<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
							  <div class="modal-content">
							    <div class="modal-header">
								<h5 class="modal-title" id="areyu">{{ __('nomination.are_you') }}</h5>			
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>
							  </div>
								
								<div class="confirm-footer">
								  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.Cancel') }}</button>&nbsp;&nbsp;&nbsp;
								  <button type="submit" class="btn dark-purple-btn" onclick="submitForm('loader_<?php echo $result['id']; ?>');">{{ __('nomination.Yes') }}</button>
								</div>
								
								<span style="text-align: center;display:none;" id="loader_{{$result['id']}}">
								 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
								</span>
								
							  </div>
							</div>
						   </div>
						</form> 
						
                        <div class="nomin-foot">
                          <div class="custom-control custom-checkbox">
						  <!--
							<input type="checkbox"  class="custom-control-input cls_{{$result['id']}}" id="customCheck-04" name="is_scrutiny_completed" value="{{$result['id']}}" onclick="return setRadio('<?php echo $result['id']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');"> -->
							
							
						<?php $datalink = app(App\Http\Controllers\Nomination\NominationController::class)->getDataLink($result['id']); ?>						
							
                            <label  for="customCheck-{{$k}}">
							@if($noned==1 or $defected==1)
							<!--<a style="color:white;" href="{!! url('nomination/book-details') !!}?query={{$link}}&id=<?php echo $datalink; ?>&data={{$link}}">APPOINTMENT SCHEDULED</a>-->
							<a style="color:white;" >{{ __('messages.Appointment4') }} </a>
							
						   @else 
							<!--<a style="color:white;" href="{!! url('nomination/book-details') !!}?query={{$link}}&id=<?php echo $datalink; ?>&data={{$link}}">APPOINTMENT SCHEDULED</a> 
						
							<a style="color:white;">{{ __('messages.Appointment4') }} </a>     -->
						   @endif
						</label>
                          </div>
                        </div>
                      </div>
                      <!-- End Of appnt-detail Div --> 
                    </div>
                    
					@endif
				  
				  
				    <?php $i++; $k++;  ?>
					@endif
                    @endforeach 
				  
				  
				  
				  </div>
                </div>
				@if($i>0)
				<div class="card-footer">				  
					<div class="panel-foot">
					 
					 <?php 
					 
					 $datalink = app(App\Http\Controllers\Nomination\NominationController::class)->getDataLink($results[0]['id']);  ?>
						<?php $stst = app(App\Http\Controllers\Nomination\NominationController::class)->getSchStatus($results[0]['st_code'], $results[0]['ac_no'] ); ?>
						<?php $isdr = app(App\Http\Controllers\Nomination\NominationController::class)->isDefectResolved($result['nomination_no']);  ?>
                        
						@if($stst==0)
						@if($n!=1)
							
						
						@if($isdr > 0) 
						<!--DD<div class="apt-btn text-right" onclick="return openPopup('<?php echo $results[0]['id']; ?>', '<?php echo $results[0]['st_code']; ?>', '<?php echo $results[0]['ac_no']; ?>');"> <a  class="btn btn-lg font-big dark-purple-btn" style="color:white;">{{ __('nomination.PROCCED_TO_SCHEDULE_APPOINTMENT') }} </a> </div>-->
					
						<div class="apt-btn text-right" onclick="return openPopup_prev('<?php echo $results[0]['id']; ?>', '<?php echo $results[0]['st_code']; ?>', '<?php echo $results[0]['ac_no']; ?>');"> <a  class="btn btn-lg font-big dark-purple-btn" style="color:white;">{{ __('nomination.PROCCED_TO_SCHEDULE_APPOINTMENT') }} </a> </div>
						
						@else
							
						
						<!--DD<div class="apt-btn text-right" onclick="return bookAnAppointment('<?php echo $results[0]['id']; ?>', '<?php echo $results[0]['st_code']; ?>', '<?php echo $results[0]['ac_no']; ?>');"> <a  class="btn btn-lg font-big dark-purple-btn" style="color:white;">{{ __('nomination.PROCCED_TO_SCHEDULE_APPOINTMENT') }} </a> </div> -->
					@if($iddataa==0)		
						<div class="apt-btn text-right"  onclick="return bookAnAppointment_prev('<?php echo $results[0]['id']; ?>', '<?php echo $results[0]['st_code']; ?>', '<?php echo $results[0]['ac_no']; ?>');"> <a  class="btn btn-lg font-big dark-purple-btn" style="color:white;">{{ __('nomination.PROCCED_TO_SCHEDULE_APPOINTMENT') }}</a> </div>
					@endif
						
						
						
						
						@endif
						
						@else 
						<div class="apt-btn text-right" > <a  class="" style="color:gray;">{{ __('nomination.Pre_Scrutiny_not_done') }}</a> </div>	
						@endif
						@else	
						
							
							<!--DD <div class="apt-btn text-right"> <a  class="btn btn-lg font-big dark-purple-btn" style="color:white;" href="{!! url('nomination/book-details') !!}?query={{$link}}&id=<?php echo $datalink; ?>&data={{$link}}">{{ __('nomination.Appointment_Scheduled') }}</a> </div> -->
						  @if($iddataa==0)		
							<div class="apt-btn text-right" onclick="return bookAnAppointment_prev('<?php echo $results[0]['id']; ?>', '<?php echo $results[0]['st_code']; ?>', '<?php echo $results[0]['ac_no']; ?>');"> <a  class="btn btn-lg font-big dark-purple-btn" style="color:white;">{{ __('nomination.Appointment_Scheduled') }}</a> </div>
						 @endif	
								
						
						
						@endif
					 
					 
					 
					 
					 
					 
				   </div>						
					<div id="queryshow"> </div>
				 </div>
				 @endif
			 <?php  } ?>
				 @if($i==0)
					<?php 
					   $md='';
						$tststs = app(App\Http\Controllers\Nomination\NominationController::class)->getProfileD(); 
						if($tststs =='One' ){
						  $md = '/nomination/apply-nomination-step-2';
						} else {
						  $md ='/nomination/apply-nomination-step-1';
						}
					?>
				 
				 
				  <div class="no-data-area">
				   <div class="msg-alrt">
					  {{ __('nomination.nosub') }}
				   </div> 
				 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="<?php echo url('/'); ?>{{$md}}"> {{ __('nomination.Apply_New') }}<br/> {{ __('nomination.Nomination') }}</a>
					   </div>
					 </div>	  	
				</div>
				@endif
              </div>
			  <?php // dd($redt); ?>

              <div id="drft" class="tab-pane">
                <div class="tab-body">
                  <table class="table tableCustom">
                    <tbody>
					<?php $k=1; if(isset($redt)) { ?>
					@foreach($redt as $result)

					@if($result['is_finalize'] == '0')
					
					<?php 
				//	print_r($redt);exit;
					$ddd= 'NA';
					$exp = '';
					if(isset($result['id'])){
					$nid=encrypt_string($result['id']);
					}
					if(isset($result['updated_at'])){
					$exp = explode(" ", $result['updated_at']);	
						$time = strtotime($result['updated_at']);
						//$ddd =  date("d M Y", $time).' '.@$exp[1];
						$ddd =  date("d M Y", $time);
					}
					$send='';

					if($result['step']==1 || empty($result['step'])){
						$send=$result['edit_href'];
					}
					if($result['step']==2){

						$send='apply-nomination-step-3?nid='.$nid;
					}
					if($result['step']==3){

						$send='apply-nomination-step-4?nid='.$nid;
					}
					if($result['step']==4){
						$send='apply-nomination-step-5?nid='.$nid;
					}
					if($result['step']==5){
						$send='apply-nomination-finalize?nid='.$nid;
					}
					$scrutiny='';	
					$st='';
					if($result['is_finalize']==1){
					 $st="Submitted";	
					}
					if($result['is_finalize']==0){
					 $st="Not Submitted";	
					}
				
					if(!empty($result['updated_at'])){
					$exp = '';
					$exp = explode(" ", $result['updated_at']);
					$yrdata= strtotime($result['updated_at']);
					$dt = date('d M Y', $yrdata).' ';
					//$tm = @$exp[1];
					}
					?>
					
					<!-- Pre Scrutiny Submitted from Preview Page -->	
						<div class="modal fade modal-confirm" id="delete_{{$result['id']}}">
						<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
						  <div class="modal-content">
						   <div class="pop-header pt-3 pb-1">
							  <div class="animte-tick"><span>&#10003;</span></div>	
							  <h5 class="modal-title"></h5>
							<div class="header-caption">
							  <p style="color:white;" > {{ __('messages.deleNom') }}</p>	
							  <ul class="list-inline">
								<li class="list-inline-item mr-4"></li>
							  </ul>
							</div>		
							</div>
							<div class="confirm-footer">
							  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.Cancel') }}</button>&nbsp;&nbsp;&nbsp;
							<button type="submit" class="btn dark-purple-btn" onclick="return doDraftDelete({{$result['id']}}); ">{{ __('nomination.Yes') }}</button> 
							</div>
						  </div>
						</div>
						</div>
					
					 <?php   
						     $isDraData = app(App\Http\Controllers\Nomination\NominationController::class)->getdateNom($result['st_code'], $result['ac_no']);
							 
					?>
					
				  
                      <tr id="main_{{$result['id']}}">
                        <td>{{$k}}.</td>
                        <td><div class=""> {{ __('nomination.Nomination_No') }} <span><strong>{{$result['nomination_no']}}</strong></span></div>
                          <div>
							@if($result['step']>=2)
							  <span class="apt-btn">{{ __('nomination.Action') }} : <a href="{{$result['view_href']}}" class="btn sm-btn dark-pink-btn">{{ __('nomination.View') }} </a> 
								  <a href="{{$result['download_href']}}" class="btn sm-btn dark-purple-btn">{{ __('nomination.Download') }} </a></span>
							@endif
							  @if($isDraData==0)		
							 <span class="apt-btn" onclick="return DeleteDraft({{ $result['id'] }} );"> <a style="color:white;" class="btn sm-btn dark-pink-btn">{{ __('messages.Delete') }} </a> </span>
							 @endif
							
						  </div>
						 </td>
                        <td><div class="">{{ __('nomination.Name') }}  <span><strong>{{$result['name']}}</strong></span></div>
                          <div class="">{{ __('nomination.Party') }}  <span><strong>{{$result['party_name']}}</strong></span></div>
						</td>
                        <td>
							<div class="">{{ __('nomination.ac') }}  &amp; {{ __('nomination.Name') }}  <span><strong>{{$result['ac_name']}}</strong></span></div>
                             <div>&nbsp;</div>
						 </td>
                        <td>
							<div class="">{{ __('nomination.Election') }}  <span><strong>{{$result['election_name']}}</strong></span></div>
                         
						  <div class="dt-tm"><span>{{$dt}} </span></div>
						</td>
					@if($isDraData==0)			
						<td class="td-edt-btn">
							<a href="{{$send}}"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span>{{ __('nomination.Edit') }}</span> </a>
						</td>
					@endif		
                      </tr>
					@php $k++ @endphp
					@endif  

					@endforeach
					
					<?php } ?>
					@if($k==1)
						
					<?php 
					   $md='';
						$tststs = app(App\Http\Controllers\Nomination\NominationController::class)->getProfileD(); 
						if($tststs =='One' ){
						  $md = '/nomination/apply-nomination-step-2';
						} else {
						  $md ='/nomination/apply-nomination-step-1';
						}
					?>
                     <div class="no-data-area"> 
					   <div class="msg-alrt">
						  {{ __('nomination.nodraft') }}
					   </div> 
					   @if($isDraData==0)			
						<div class="tab-actn-btn my-5">
						   <div class="apply-btn d-inline-flex">
							   <span class="apply-icon"></span><a href="<?php echo url('/'); ?>{{$md}}">{{ __('nomination.Apply_New') }}<br/> {{ __('nomination.Nomination') }}</a>
						   </div>
						 </div>	  	
					  @endif	 
					</div>
					@endif  
                    </tbody>
                  </table>
                </div>
              </div>
        
		   </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Pre Scrutiny Submitted from Preview Page -->	
	<div class="modal fade modal-confirm" id="is_sub_id">
	<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
	  <div class="modal-content">
	   <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
		  <h5 class="modal-title"></h5>
		<div class="header-caption">
		  <p> {{ __('messages.application_finalize') }}</p>	
		  <ul class="list-inline">
			<li class="list-inline-item mr-4"></li>
		  </ul>
		</div>		
		</div>
		 <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
		</div>
	  </div>
	</div>
	</div>
	
	<div class="col" id="nons_{{$result['id']}}" style="margin-left:0px; padding: 3px; margin-bottom: 16px;  width: 24%; color: white;"></div>	
							
	<div class="modal fade modal-confirm" id="confirm">
	<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
	  <div class="modal-content">
	   <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
		  <h5 class="modal-title"></h5>
		<div class="header-caption">
		  <p>{{ __('nomination.your_online') }}</p>	
		  <ul class="list-inline">
			<li class="list-inline-item mr-4"></li>
		  </ul>
		</div>		
		</div>
		 <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
		</div>
	  </div>
	</div>
	</div>						
   
    
	
	
	
	
   

<form name="app" id="election_form" method="POST"  action="{{url('/nomination/schedule-appointment/post') }}" autocomplete='off' enctype="x-www-urlencoded">
{{ csrf_field() }}
<input name="st_code" id="st_code" type="hidden" value="U05">
<input name="ac" id="ac" type="hidden" value="1">
<input name="reason" type="hidden" value="reason">
<input name="name" type="hidden" value="name">
<input name="email" type="hidden" value="email">
<input name="mobile" type="hidden" value="9988776655">
<input name="date" type="hidden" value="2020-04-11">
<input name="time" type="hidden" value="10 to 11">
<input type="hidden" name="selectRadioButton" id="selectRadioButton">
</form>


<div class="modal fade modal-confirm" id="delSuccOk">
<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
  <div class="modal-content">
   <div class="pop-header pt-3 pb-1">
	  <div class="animte-tick"><span>&#10003;</span></div>	
	  <h5 class="modal-title"></h5>
	<div class="header-caption">
	  <p style="color:white;" id="tm"></p>	
	  <ul class="list-inline">
		<li class="list-inline-item mr-4"></li>
	  </ul>
	</div>		
	</div>
	 <div class="confirm-footer">
	  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>&nbsp;&nbsp;&nbsp;
	</div>
  </div>
</div>
</div>


@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>
	<script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/week-scheduale.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>  
<script>
	
	function doDraftDelete(id){
		var dddd = jQuery.noConflict();		
		$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/delete-draft-nomination", 
		data: {
			"_token": "{{ csrf_token() }}",
			"id": id
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==1){ 
				
				dddd('#delete_'+id).modal('hide');	
				dddd('#delSuccOk').modal('show');
				dddd('#tm').html("<?php echo __('messages.delSucc'); ?>");
				dddd('#main_'+id).hide();
			} else {
				dddd('#delSuccOk').modal('show');
				dddd('#tm').html("<?php echo __('messages.delIssue'); ?>");
			} 
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	});
		
	}
	
	function DeleteDraft(id){
	    var ii = jQuery.noConflict();		
		         ii('#delete_'+id).modal('show');	
	}	
	
	function changeDish(ent){
		if(ent!=''){
		   var dd = ent.split('***+++');	
		   
		   window.location.href="<?php echo url('/'); ?>/nomination/nominations?acs="+dd[0]+'&std='+dd[1];
		}		
	}
	
	function submitForm(id){
		var j = jQuery.noConflict();		
		j("#"+id).show();
	}
	
	<?php if(session('is_scheduled')!==null){
        if(session('is_scheduled') == 'yes'){ ?>
		var j = jQuery.noConflict();		
		j('#confirm').modal('show');
	<?php } } ?>
	
	function openPopup(id, st_code, ac){ 
		var j = jQuery.noConflict();
	    j('#schedule_'+id).modal('show');	
	}
	function openPopup_prev(id, st_code, ac){ 
		var j = jQuery.noConflict();
	    j('#schedule_prev'+id).modal('show');	
	}
	function checkaff(id){ 
	  var j = jQuery.noConflict();
	  var checkaff = j("#affidavit_"+id).val();	
		if(checkaff==''){
		  j("#checkafferror_"+id).html("<?php echo __('nomination.sign'); ?>");
		  j("#affidavit_"+id).focus();	
		 return false;
		}
	   	
	   j('#checkafferror_'+id).hide();	
	   j('#cancel_'+id).modal('show');	
	   return false;
	}
	
	function uploadPdf(id){		  	
      $('#form-upload').remove();
      $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');
      $('#form-upload input[name=\'file\']').trigger('click');
      if (typeof timer != 'undefined') {
        clearInterval(timer);
      }
      timer = setInterval(function() {
        if ($('#form-upload input[name=\'file\']').val() != '') {
		    var nid=id;                      
           // alert("<?php echo url('/'); ?>/Nomination/upload-affidavit-final?_token=<?php echo csrf_token(); ?>&nid="+nid);                 	
            clearInterval(timer);
            $.ajax({
            url: "<?php echo url('/'); ?>/nomination/upload-affidavit-final?_token=<?php echo csrf_token(); ?>&nid="+nid,
            type: 'POST',
            dataType: 'json',
            data: new FormData($('#form-upload')[0]),
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
              $('.file-frame').removeClass("file-frame-error");
              $('.file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
              $('.file').prop('disabled', true);
              $('.text-danger').remove();
            },
            complete: function() {
              $('.file i').replaceWith('<i class="fa fa-upload"></i>');
              $('.file').prop('disabled', false);
            },
            success: function(json) {   console.log(json);
              if(json['success'] == false) {
				$("#checkafferror_"+nid).html("<?php echo __('nomination.onlypdf'); ?>");  
                //$('.file-frame').after("<span class='text-danger'>"+json['errors']+"</span>");
                //$('.file-frame').addClass("file-frame-error");
              }
              if (json['success'] == true) {
				$("#"+nid).show();  
				
				$("#affidavit_"+nid).val(json['path']);  
                $('.file-frame').find('.affidavit_'+nid).val(json['path']);
                $('.affidavit-preview_'+nid+ ' iframe').attr("src","<?php echo url('/'); ?>/"+json['path']);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
				console.log( xhr.responseText);
				console.log(xhr);
				
			//  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
			  $("#checkafferror_"+nid).html("<?php echo __('messages.file_type_error'); ?>");
			  //$("#checkafferror_"+nid).html("<?php echo __('nomination.onlypdf'); ?>");  
            }
          });
        }
      }, 500);
	  
	}
	
	/*$(function(){
		//This Function For Read More Content	
		var j = jQuery.noConflict();
		j('.more-btn').click(function() {
		  j('#div' + $(this).attr('target')).toggleClass('expnd');
			  if(j('.rmark-info').hasClass('expnd')){
				   j(this).text('Close');
				  }else{j(this).text('Read More...');} 
		}); 
	});
	*/
	<?php if(session('is_sub')!==null){ 
	if(session('is_sub') == 'yes'){ ?>
	var j = jQuery.noConflict();	
	j('#is_sub_id').modal('show');
	<?php } } ?> 	
	
	
	  function hello(id){
		 $("#queryshow").toggle();
		 $.get('prescootiny/'+id, {}, function(data){  
		   $("#queryshow").html(data);
		});
	  }	
	
	 $(document).ready(function() {
              var owl = $('.owl-carousel');
              owl.owlCarousel({
                margin: 2,
                nav: true,
                loop: true,
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items: 2
                  },
                  1000: {
                    items: itmCount
                  }
                }
              });
		  });
      	
	  
		 var itmCount = $('.item').length;
		
		if(itmCount == 1){
			$('.list-detail').addClass('one-appoint');
		}else if(itmCount == 2){
		   $('.list-detail').removeClass('one-appoint').addClass('two-appoint');
		}else if(itmCount == 3){
			$('.list-detail').removeClass('one-appoint, two-appoint').addClass('three-appoint');
		}else if(itmCount >= 4){
			$('.list-detail').removeClass('one-appoint, two-appoint, three-appoint');
			itmCount = 4;
		}
	
	
    function showQuery(id){
	$("#"+id).toggle();  
   }
   function setRadio(id, state, ac){ 
   
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/get-nomination-start-end-date", 
		data: {
			"_token": "{{ csrf_token() }}",
			"sId": state,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==0){
			$(".cls_"+id).prop('checked', false);  
			alert("<?php echo __('nomination.notstarted'); ?>"); 
			return false;	
		  } 
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	});
	
	$("#selectRadioButton").val(id);  
	$("#st_code").val(state);  
	$("#ac").val(ac);  
	
	
	
   }
   function bookAnAppointment(id, st_code, ac){  
   
   
	if(id==''){
		alert("<?php echo __('nomination.sign'); ?>");
		return false;
	}
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/get-nomination-start-end-date", 
		data: {
			"_token": "{{ csrf_token() }}",
			"sId": st_code,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==0){
			alert("<?php echo __('nomination.notstarted'); ?>");
			return false;	
		  } else {
			window.location.href = "{!! url('nomination/set-param') !!}?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id="+id+'&st_code='+st_code+'&ac='+ac+'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9';  
		  }
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	});
  }
  
  function bookAnAppointment_prev(id, st_code, ac){  
   
    
	if(id==''){
		alert("<?php echo __('nomination.sign'); ?>");
		return false;
	}
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/get-nomination-start-end-date", 
		data: {
			"_token": "{{ csrf_token() }}",
			"sId": st_code,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==0){
			alert("<?php echo __('nomination.notstarted'); ?>");
			return false;	
		  } else {
			window.location.href = "{!! url('nomination/set-param-prev') !!}?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id="+id+'&st_code='+st_code+'&ac='+ac+'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9';  
		  }
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	});
  }
  
  
  function RebookAnAppointment(id, st_code, ac){ 
	if(id==''){
		alert("<?php echo __('nomination.sign'); ?>");
		return false;
	}
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/get-nomination-start-end-date", 
		data: {
			"_token": "{{ csrf_token() }}",
			"sId": st_code,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==0){
			alert("<?php echo __('nomination.notstarted'); ?>");
			return false;	
		  } else {
			var r = confirm("<?php echo __('nomination.are_res'); ?>");
			if(r == true){ 
			 window.location.href = "{!! url('nomination/confirm-schedule-appointment') !!}?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id="+id+'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9';
			}
		  }
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	  });
    }
	

    $(document).ready(function(){  
     if($('#breadcrumb').length){
       var breadcrumb = '';
       $.each({!! json_encode($breadcrumbs) !!},function(index, object){
        breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
       });
       $('#breadcrumb').html(breadcrumb);
      }
    });

  $(document).ready(function(e){
      let scanner = '';
      $('#open_webcam').click(function(e){
        $('.parent_qr_code').removeClass("display_none");
        scanner = new Instascan.Scanner({ 
          backgroundScan: false,
          video: document.getElementById('preview') 
        });
        scanner.addListener('scan', function (content) {
          window.location.href = "{!! url('nomination/detail') !!}"+'/'+content;
        });

        Instascan.Camera.getCameras().then(function (cameras) {
          if (cameras.length > 0) {
            scanner.start(cameras[0]);
          } else {
            console.error('No cameras found.');
          }
        }).catch(function (e) {
          console.error(e);
        });
      });

      $('#close_webcam').click(function(e){
        scanner.stop().then(function () {

        });
        $('.parent_qr_code').addClass("display_none");
      });

     });
</script>
@endsection