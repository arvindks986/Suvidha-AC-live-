@extends('admin.layouts.ac.dashboard-theme')
@section('content')

<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/jquery.stickytable.min.css') }}">

<script>
$("#pc").selectpicker('refresh');
</script>


<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

#acViewBody a{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}

#acViewBody a:hover{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}
.bold{font-weight:bold;}

.swatch-yellow {
   color: #fff;
    background-color: #17a2b8; padding: 10px;
}
.form-control:disabled, .form-control[readonly]{background:#fff; height:46px; border:1px solid #d5d5d5;}
button.btn.dropdown-toggle.btn-light.bs-placeholder {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
button.btn.dropdown-toggle.btn-light {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
.form-control:disabled, .form-control[readonly]{height:37px;}
.form-control:focus, .form-control:hover{box-shadow:none;}
</style>

<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-9 pull-left">
   <h4>AC Result Report</h4>
  </div>
   <div class="col-md-3  pull-right text-right">   
      <span style="display:none;" onclick="return downloadExcel();" class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="#" title="Download Excel">Export Excel</a></span>    
      <span style="display:none;" onclick="return downloadPdf();" class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="#" title="Download PDF" >Export PDF</a></span>  
  </div> 
  </div>
</div>  
</section>

<div>
{{ Form::model('', ['action' => 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCompleteResult', 'name'=>'pdfForm' ]) }}
<input type="hidden" name="download" value="2">	
<input type="hidden" name="st_code[]" id="pdfstate">	
<input type="hidden" name="pc[]" id="pdfpc">
<input type="hidden" name="result_counting" id="pdfresult_counting">
<input type="hidden" name="result_type" id="pdfresult_type">
<input type="hidden" name="user_type" id="user_type_a" value="{{$user_type}}">
<input type="hidden" name="result_counting_text" id="result_counting_text">
<input type="hidden" name="result_type_text" id="result_type_text">
</form>	
</div> 

<div class="btns-actn">
{{ Form::model('', ['action' => 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@csvDownload', 'name'=>'csvDownload' ]) }}
<input type="hidden" name="download" value="2">	
<input type="hidden" name="st_code[]" id="csvstate">	
<input type="hidden" name="pc[]" id="csvpc">	
<input type="hidden" name="result_counting" id="csvresult_counting">
<input type="hidden" name="result_type" id="csvresult_type">
<input type="hidden" name="user_type" id="user_type_a" value="{{$user_type}}">	
</form>	
</div> 

<section class="dashboard-header p-2 mb-0">
  <div class="container-fluid">  
    <form id="generate_report_id" class="row" method="get" onsubmit="return false;"> 
		  <input type="hidden" name="user_type" id="user_type_a" value="{{$user_type}}">
          <div class="form-group col-md-4"> <label>Select State<span style="color:red;">*</span></label> 
            <select name="state" id="state" class="form-control " onchange ="return getPcByStateId(this.value)"  data-actions-box="true" required 
			title="select State" {{$disabled}}>
			<option value=""> Select State </option>
		    @foreach($state as $st)
			<option value="{{$st->ST_CODE}}" @if(isset($state_id) && ($state_id==$st->ST_CODE)){{'selected'}}@endif>{{$st->ST_NAME}}</option>
             @endforeach
            </select>
          </div>

		 @if((isset($state_id) && (empty($pc_no))) && ($url=='eci'))
		 
		  <div class="form-group col-md-4" id="pcDiv"> <label>Select AC.<span style="color:red;">*</span></label> 
            <select name="pc" id="pc" class="form-control selectpicker"  multiple data-actions-box="true" required 
			title="select AC">
            </select>
          </div>
		 @endif	
		
		
		 @if((isset($state_id) && (empty($pc_no))) && ($url!='roac'))
		  @if(isset($get_Pc_data))
		  <div class="form-group col-md-4" id="pcDiv"> <label>Select AC<span style="color:red;">*</span></label> 
            <select name="pc" id="pc" class="form-control selectpicker"  multiple data-actions-box="true" required 
			title="select AC">
			 @foreach($get_Pc_data as $pcdata)
				<option value="{{$pcdata->AC_NO}}"  @if(isset($state_id) && ($pc_no==$pcdata->AC_NO)){{'selected'}}@endif>{{$pcdata->AC_NO}}- {{$pcdata->AC_NAME}}</option>
			 @endforeach
            </select>
          </div>
		    @endif	
		 @endif	
	 
		<!-- if more than one state than this PC will show -->
		<div class="form-group col-md-4" id="pc22" style="display:none;"> 
			<label>Select PC<span style="color:red;">*</span></label> 
			<select name="pc" id="pcone" class="form-control selectpicker" disabled>
			<option value="000" selected>All AC</option>
			</select>
		</div>
		<!--End if more than one state than this PC will show -->
		
		 @if($url=='roac') 
			@if(isset($get_Pc_data)) 
		    <div class="form-group col-md-4"> <label>Select AC<span style="color:red;">*</span></label> 
            <select name="pc" id="pc" class="form-control selectpicker" {{$disabled}}>
            <option value="">Select PC</option>
               @foreach($get_Pc_data as $pcdata)
				<option value="{{$pcdata->AC_NO}}" selected >{{$pcdata->AC_NO}}- {{$pcdata->AC_NAME}}</option>
			 @endforeach
            </select>
           </div>
		  @endif
		 @endif	
		
		<div class="form-group col-md-4" id="result_counting_div"> 
			<label>Select Result Status<span style="color:red;">*</span></label>  <!--  onchange="return hideCase(this.value);" -->
			<select name="result_counting" id="result_counting" class="form-control">
			<option value="">Select Result Status</option>
			<option value="000">Both</option>
			<option value="1">Result Declared</option>
			<option value="2">Result In Progress</option>
			</select>
		</div>
		
		<div class="form-group col-md-4" id="result_type_div"> 
			<label>Result Type<span style="color:red;">*</span></label> 
			<select name="result_type" id="result_type" class="form-control">
			<option value="000">All Type</option>
			<option value="1">Leading Only</option>
			<option value="2">Leading With Trailing</option>
			</select>
		</div>
		 
		  <div class="form-group col-md-4"  id="resultDiv" style="">
			<div class="row"><label class="col" for="">&nbsp;</label></div>		
			<a class="btn btn-primary btn-block" href="#" style="width: 70px;" onclick="return getResultECI();" title="Download Excel" >Search</a>
		  </div> 
		
		<div class="col-md-12" style="text-align: center; margin-top: 47px; margin-left: -9em;display:none;" id="loading">   
		<span class="report-btn">
			<a class="btn btn-primary" href="#" title="Download Excel" >Loading... Please Wait</a>
		</span> 
		</div> 
        </form>  
</div>
</section>

<div id="valShow" class="container-fluid pr-5 pl-5 pt-2 pb-2 swatch-yellow" style="display:none;"> 
<div class="row">
<div class="col-md-3"><span> State : </span><span id="state_text" class="bold">  </span></div>
<div class="col-md-3"><span> Result : </span><span id="result_text" class="bold">  </span></div>
<div class="col-md-6"><span> Result Type : </span><span id="result_type_text_below" class="bold">  </span> </div>
</div>
</div>

<div class="container-fluid">
<div class="row">
	<div id="showResult" class="col mt-5"></div>
</div>


 </div>

 

   

<script> 

function hideCase(id){
	if(id==000 || id==2){
	 $('#result_type_div').hide();	
	} else {
	 $('#result_type_div').show();	
	}
	
}

function downloadPdf(){ 
	document.pdfForm.submit();
}
function downloadExcel(){ 
	document.csvDownload.submit();
}
function getResultECI(){ 
	
	var pc22 =  $('#pcone').val();
	var result_counting =  $('#result_counting').val();
	var result_type =  $('#result_type').val();
	
	
	
	var state = [];    
		$("#state :selected").each(function(){
		state.push($(this).val()); 
	});
	
	
	
	var pc = [];    
		$("#pc :selected").each(function(){
		pc.push($(this).val()); 
	});
	var ac = [];    
		$("#ac :selected").each(function(){
		ac.push($(this).val()); 
	});
	var condidate = [];    
		$("#condidate :selected").each(function(){
		condidate.push($(this).val()); 
	});

    if(state==''){
		alert("Please select state");
		$('#state').focus();
		return false;
	}
	
	
	
	if(state.length==1){
		if(pc==''){
			alert("Please select ac");
			$('#pc').focus();
			return false;
		}
	}
	if(result_counting==''){
		alert("Please select result");
		$('#result_counting').focus();
		return false;
	}
	
	if(pc==''){
	  pc =	pc22;		
	}
	//alert(pc); return false;
	//alert(state+'-'+pc+'-'+result_counting+'-'+result_type);
	var user_type_a = $('#user_type_a').val();
	
	$('#loading').show();
	
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/<?php echo $url; ?>/get-all-result-eci-pcwise-constituency", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": state,
				"pc": pc,
				"result_counting": result_counting,
				"result_type": result_type,
				"user_type": user_type_a
				},
				dataType: "html",
				success: function(msg){ 
				$('#showResult').show();
				$('#pdfstate').val(state);
				$('#pdfpc').val(pc);
				$('#pdfresult_counting').val(result_counting);
				$('#pdfresult_type').val(result_type);
				$('#csvstate').val(state);
				$('#csvpc').val(pc);
				$('#csvresult_counting').val(result_counting);
				$('#csvresult_type').val(result_type);
				$('#state_text').text($("#state option:selected").text());
				$('#result_counting_text').val($("#result_counting option:selected").text());
				$('#result_type_text').val($("#result_type option:selected").text());
				$('#resultDiv').show();
				$('#loading').hide();
				if(msg.length > 1550){ 
				$('#export-csv-btn').show();
				$('#export-pdf-btn').show();
				} else {
				$('#export-csv-btn').hide();
				$('#export-pdf-btn').hide();
				}
				$('#showResult').html(msg); 
				$('#valShow').show();
				$('#result_text').text($("#result_counting option:selected").text());
				$('#result_type_text_below').text($("#result_type option:selected").text());

			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
}


function getPcByStateId(st_code){ 
	$('#showResult').hide();
	$('#valShow').hide();
	
	var state = [];    
		$("#state :selected").each(function(){
		state.push($(this).val()); 
	});
	
	if(state.length == 0){ 
	  $('#pc').empty();
	  $('.selectpicker').selectpicker('refresh');
	  $('.selectpicker').selectpicker('refresh');
	}	
	
	if(state.length > 1){
	$('#pcDiv').hide();	
	$('#pc22').show();		
	} else {
	$('#pcDiv').show();	
	$('#pc22').hide();		
	}
	
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/<?php echo $url; ?>/get-pc-by-state-id-eci-pcwise-constituency", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": st_code
				},
			dataType: "html",
			success: function(msg){ 
			var jsonText = $.parseJSON(msg); 
			var text = [];
			//text.push('<option value="">Select PC </option>');
			//text.push('<option value="000" selected>All PC</option>');
			for (i=0; i<jsonText.AC_NO.length; i++) {
				text.push('<option value=' + jsonText.AC_NO[i] + '>' + jsonText.AC_NO[i] +'-'+ jsonText.AC_NAME[i]  + '</option>');
			}
			 $('#pc').html(text).selectpicker('refresh');
			// $('#pc').html(text);
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
}

function getAcByStateAndPcId(acId){ 
   $('#showResult').hide();
   $('#valShow').hide();
	var state = [];    
		$("#state :selected").each(function(){
		state.push($(this).val()); 
	});
	//alert(state+'-'+acId);
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/<?php echo $url; ?>/get-ac-by-state-and-pc-id-eci-pcwise-constituency", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": state,
				"acId": acId
				},
			dataType: "html",
			success: function(msg){ 
			var jsonText = $.parseJSON(msg); 
			var text = [];
			text.push('<option value="">Select AC </option>');
			text.push('<option value="000" selected>All AC</option>');
			for (i=0; i<jsonText.AC_NO.length; i++) {
				text.push('<option value=' + jsonText.AC_NO[i] + '>' + jsonText.AC_NO[i] +'-'+ jsonText.AC_NAME[i]  + '</option>');
			}
			$('#ac').html(text);
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
	
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/<?php echo $url; ?>/get-condidate-details-eci-pcwise", 
			data: {
				"_token": "{{ csrf_token() }}",
				"stateok": state,
				"pcId": pcId
				},
			dataType: "html",
			success: function(msg){
			var jsonText = $.parseJSON(msg); 
			var text = [];
			
			if(msg.length>1){
					text.push('<option value="">Select Candidate </option>');
					text.push('<option value="000" selected>All Candidate </option>');
					for (i=0; i<jsonText.candidate_id.length; i++) {
						text.push('<option value=' + jsonText.candidate_id[i] + '>' + jsonText.cParty[i]  + '</option>');
					}
					$('#condidate').html(text);
			} else { 
					text.push('<option value="" selected>No candidate found</option>');
					$('#condidate').html(text);
			}
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
	
}
</script>
<script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.stickytable.min.js') }}"></script>
@endsection




