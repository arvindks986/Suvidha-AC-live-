<script src="{{ asset('public/js/jquery.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
@extends('admin.layouts.ac.dashboard-theme')
@section('content')
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
</style>

<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2"> 
<div style="margin-left:100px;"><h5>Round Wise Report</h5></div>
<div class="container-fluid">
  <div class="row">
   <div class="col-md-12  pull-right text-right">   
      <span style="display:none;" onclick="return downloadExcel();" class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="#" title="Download Excel">Export Excel</a></span>    
      <span style="display:none;" onclick="return downloadPdf();" class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="#" title="Download PDF" >Export PDF</a></span>  
  </div> 
  </div>
</div>  
</section>



<div class="btns-actn">
{{ Form::model('', ['action' => 'Admin\RoundWiseReport\RoundWiseReportController@getCompleteResult', 'name'=>'pdfForm' ]) }}
<input type="hidden" name="download" value="2">		
<input type="hidden" name="st_code[]" id="pdfstate">	
<input type="hidden" name="pc[]" id="pdfpc">	
<input type="hidden" name="ac[]" id="pdfac">	
<input type="hidden" name="condidate[]" id="pdfcondidate">		
</form>	
</div> 



<div class="row" id="valShow" style="display:none;">
  <div class="col" style="position: absolute; margin-left: 97px;"><h5> </h5></div> 
  <div class="col"><p class="mb-0 text-right"><b>State:</b> <span class="badge badge-info" id="statename"></span> &nbsp;&nbsp; <b></b> 
  <span class="badge badge-info"></span>&nbsp;&nbsp; <span class="badge badge-info" ></span>&nbsp;&nbsp;  <b>AC:</b>  <span class="badge badge-info" id="acname"></span>  &nbsp;&nbsp; </p>
  </div>
</div>


<div class="btns-actn">
{{ Form::model('', ['action' => 'Admin\RoundWiseReport\RoundWiseReportController@csvDownload', 'name'=>'csvDownload' ]) }}
<input type="hidden" name="download" value="2">		
<input type="hidden" name="st_code[]" id="csvstate">	
<input type="hidden" name="pc[]" id="csvpc">	
<input type="hidden" name="ac[]" id="csvac">	
<input type="hidden" name="condidate[]" id="csvcondidate">		
</form>	
</div> 



<section class="dashboard-header section-padding">
  <div class="container-fluid" style="margin-left: 10em;">
  
    <form id="generate_report_id" class="row" method="get" onsubmit="return false;"> 
       
          <div class="form-group col-md-2"> <label>Select State <span style="color:red;">*</span></label> 
            <select name="state" id="state" class="form-control" onchange ="return getAcByStateAndPcId(this.value); getCandidate();" {{$disabled}}>
			<option value="">Select State</option>
           @foreach($state as $st)
			<option value="{{$st->ST_CODE}}" @if(isset($state_id) && ($state_id==$st->ST_CODE)){{'selected'}}@endif>{{$st->ST_NAME}}</option>
          @endforeach
            </select>
          </div>
		
		@if(empty($state_id))  
		  <div class="form-group col-md-2"> <label>Select AC<span style="color:red;">*</span></label> 
            <select name="ac" id="ac" class="form-control " onchange ="return getCandidate();">
            <option value="">Select AC</option>
           <option> </option>
            </select>
          </div>
		@endif  
		
		@if(!empty($state_id) && (empty($ac_no)) && ($user_type!='DEO'))  
		  <div class="form-group col-md-2"> <label>Select AC<span style="color:red;">*</span></label> 
            <select name="ac" id="ac" class="form-control" onchange ="return getCandidate();">
			<option value="">Select AC</option>
			<option value="000" selected>All AC</option>
             @foreach($acData as $adata)
				<option value="{{$adata->AC_NO}}" @if(isset($ac_no) && ($ac_no==$adata->AC_NO)){{'selected'}}@endif>{{$adata->AC_NO}}- {{$adata->AC_NAME}}</option>
			 @endforeach
            </select>
          </div>
		@endif  
		
		@if(!empty($state_id) && (!empty($ac_no)) && ($user_type!='DEO'))
		  <div class="form-group col-md-2"> <label>Select AC<span style="color:red;">*</span></label> 
            <select name="ac" id="ac" class="form-control" {{$disabled}}>
			<option value="">Select AC</option>
			<option value="000" selected>All AC</option>
             @foreach($acData as $adata)
				<option value="{{$adata->AC_NO}}" @if(isset($ac_no) && ($ac_no==$adata->AC_NO)){{'selected'}}@endif>{{$adata->AC_NO}}- {{$adata->AC_NAME}}</option>
			 @endforeach
            </select>
          </div>
		@endif  		

		@if(isset($acDataDeo) && ( !empty($acDataDeo))  && ($user_type=='DEO'))
		<div class="form-group col-md-2"> <label>Select AC <span style="color:red;">*</span></label> 
            <select name="ac" id="ac" class="form-control" onchange ="return getCandidate();">
			<option value="">Select AC</option>
			<option value="DEO_{{$dist_no}}" selected>All AC</option>
             @foreach($acDataDeo as $dataDeo)
				<option value="{{$dataDeo->AC_NO}}" @if(isset($ac_no) && ($ac_no==$dataDeo->AC_NO)){{'selected'}}@endif>{{$dataDeo->AC_NO}}- {{$dataDeo->AC_NAME}} </option>
			 @endforeach
            </select>
          </div>
		@endif
		@if(empty($state_id) && empty($ac_no) && ($user_type!='DEO'))  
		   <div class="form-group col-md-2"> <label>Select Candidate<span style="color:red;">*</span></label> 
            <select name="condidate" id="condidate" class="form-control">
            <option value="">Select Candidate</option>
           <option> </option>
            </select>
          </div>
		@endif  
		
		@if(!empty($state_id)&& (empty($condidateDataAc)) && ($user_type!='DEO'))  
		   <div class="form-group col-md-2"> <label>Select Candidate<span style="color:red;">*</span></label> 
            <select name="condidate" id="condidate" class="form-control">
            <option value="">Select Candidate</option>
			<option value="000" selected>All Candidate</option>
            @foreach($condidateData as $cData)
				<option value="{{$cData->candidate_id}}">{{$cData->candidate_name}}({{$cData->party_abbre}})</option>
			 @endforeach
            </select>
          </div>
		@endif  

		@if(!empty($condidateDataAc) && (!empty($condidateDataAc)) && ($user_type!='DEO')) 
		   <div class="form-group col-md-2"> <label>Select Candidate<span style="color:red;">*</span></label> 
            <select name="condidate" id="condidate" class="form-control">
            <option value="">Select Candidate</option>
			<option value="000" selected>All Candidate</option>
            @foreach($condidateDataAc as $cData)
				<option value="{{$cData->candidate_id}}">{{$cData->candidate_name}}({{$cData->party_abbre}})</option>
			 @endforeach
            </select>
          </div>
		@endif 

		@if($user_type=='DEO')
		   <div class="form-group col-md-2"> <label>Select Candidate<span style="color:red;">*</span></label> 
            <select name="condidate" id="condidate" class="form-control">
            <option value="">Select Candidate</option>
			<option value="000" selected>All Candidate</option>
            @foreach($condidateDeo as $DeoCnd)
				<option value="{{$DeoCnd->candidate_id}}">{{$DeoCnd->candidate_name}}({{$DeoCnd->party_abbre}})</option>
			 @endforeach
            </select>
          </div>
		@endif 
		  
	   <div class="form-group col-md-2"> <label>Select Round</label> 
		<select name="phase" id="phase" class="form-control">
		<option value="All">All</option>
	   <option> </option>
		</select>
	  </div>
		<div class="col-md-12" style="text-align: center; margin-top: 47px; margin-left: -9em;"  id="resultDiv">   
		<span class="report-btn">
			<a class="btn btn-primary" href="#" onclick="return getResultECI();" title="Download Excel" >Search</a>
		</span> 
		</div> 
		<div class="col-md-12" style="text-align: center; margin-top: 47px; margin-left: -9em;display:none;" id="loading">   
		<span class="report-btn">
			<a class="btn btn-primary" href="#" title="Download Excel" >Loading... Please Wait</a>
		</span> 
		</div> 
        </form>  
  </div>
</section>

<div class="container-fluid">
  <div class="parent-wrap">
    <div class="child-area">
     <div class="page-contant">
		
		
	
     <div class="random-area">
  <br> 
				<div id="showResult"> </div>
	</div>
      </div>
    </div>
    </div>      
  </div>
  </div> 
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<link rel="stylesheet" href="{{asset('css/dataTables.bootstrap.min.css')}}">    

<script> 
function downloadPdf(){ 
	document.pdfForm.submit();
}
function downloadExcel(){ 
	document.csvDownload.submit();
}
function getResultECI(){ 
	
	var state = [];    
		$("#state :selected").each(function(){
		state.push($(this).val()); 
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
		alert("Please select satate");
		$('#state').focus();
		return false;
	}
	
	if(ac==''){
		alert("Please select ac");
		$('#ac').focus();
		return false;
	}

	if(condidate==''){
		alert("Please select candidate");
		$('#condidate').focus();
		return false;
	}

	$('#loading').show();
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/eci/get-all-result-eci", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": state,
				"ac": ac,
				"condidate": condidate
				},
			dataType: "html",
			success: function(msg){ 
				$('#showResult').show();
				$('#pdfstate').val(state);
				$('#pdfac').val(ac);
				$('#pdfcondidate').val(condidate);
				$('#csvstate').val(state);
				$('#csvac').val(ac);
				$('#csvcondidate').val(condidate); 
				$('#resultDiv').show();
				$('#loading').hide();
				$('#export-csv-btn').show();
				$('#export-pdf-btn').show();
				$('#showResult').html(msg); 
				$('#valShow').show();
				$('#statename').text($("#state option:selected").text());
				$('#acname').text($("#ac option:selected").text());
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
}

function getAcByStateAndPcId(sId){ 
   $('#showResult').hide();
	
	
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/eci/get-ac-by-state-and-pc-id-eci", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": sId
				},
			dataType: "html",
			success: function(msg){ 
			var jsonText = $.parseJSON(msg); 
			var text = [];
			text.push('<option value="" selected>Select AC </option>');
			text.push('<option value="000">All AC</option>');
			for (i=0; i<jsonText.AC_NO.length; i++) {
				text.push('<option value=' + jsonText.AC_NO[i] + '>' + jsonText.AC_NO[i] +'-'+ jsonText.AC_NAME[i]  + '</option>');
			}
			$('#ac').html(text);
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});	
}
function getCandidate(){
	var acC = [];    
		$("#ac :selected").each(function(){
		acC.push($(this).val()); 
	});

	var state= $('#state').val();

	
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/eci/get-condidate-details-eci-ac-wise", 
			data: {
				"_token": "{{ csrf_token() }}",
				"stateok": state,
				"ac": acC
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



