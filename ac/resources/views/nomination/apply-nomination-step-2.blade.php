@extends('layouts.theme')
@section('title', 'Nomination')
@section('content')






<link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">	
<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">

<section >
  <div class="container">
        @if (session('flash-message'))
        <div class="alert alert-success mt-4"> {{session('flash-message') }}</div>
        @endif
 </section>

<div class="container">
 <div class="step-wrap mt-4">
     <ul>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step1') }}</span></li>
       <li class="step-current"><b>&#10004;</b><span>{{ __('step1.step2') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step3') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step4') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step5') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step6') }}</span></li>
       <li class=""><b>&#10004;</b><span>{{ __('step1.step7') }}</span></li>
     </ul>
 </div>
</div>

	
	



  <section>
    <div class="container"> 
      <form id="election_form" name="subElec" method="POST"  action="{{$action}}" autocomplete='off' enctype="x-www-urlencoded">
        @if(isset($nomination_id) && $nomination_id)
        <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
        @endif
        <div class="row">
            <div class="card">
              <div class="card-header">
			    <div class="row">
                <div class="col-md-6">
                <h4 class="pt-2">{!! $heading_title !!}</h4>
                </div>
                @if(isset($reference_id) && isset($href_download_application))
                <div class="col-md-6 text-right">
                  <ul class="list-inline">
                    <li class="list-inline-item">{{ __('election_details.ref') }}: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
					@if($stepCond >=2)
                    <li class="list-inline-item"><a href="{!! $href_download_application !!}" class="btn btn-primary" target="_blank">{{ __('election_details.down') }}</a></li>
					@endif
					
					
                  </ul>
                </div>
				</div>
                @endif
              </div>
              <div class="card-body">

                <input type="hidden" name="_token" value="{{csrf_token()}}">
				<?php $dis=''; ?>
				
				@if($finalize==1)	
				<input type="hidden" name="election_id" value="{{$election_id}}">	
				<input type="hidden" name="st_code" value="{{$st_code}}">	
				<input type="hidden" name="ac_no" value="{{$ac_no}}">	
				<?php $dis='disabled'; ?>
				@endif	
				<input type="hidden" name="eid" id="eid">	
				
                 <div class="form-group row">
                  <div class="col-sm-2"><label for="statename">{{ __('election_details.election_type') }}<sup>*</sup></label></div>
                  <div class="col">
                    <div class="" style="width:100%;"> 
                      <select name="election_id" class="form-control" id="election_id" onchange="filter_respective_state(this.value)" <?php echo $dis;  ?>>
                        <option value="">-- {{ __('election_details.select_election') }} --</option>
                        @foreach($election_types as $iterate_election)
                          @if($election_id == $iterate_election['election_type_id'])
                          <option value="{{ $iterate_election['election_type_id'] }}" data_type_id= "{{ $iterate_election['election_type_id'] }}" selected="selected">{{ $iterate_election['name'] }}</option>
                          @else 
                          <option value="{{ $iterate_election['election_type_id'] }}" data_type_id= "{{ $iterate_election['election_type_id'] }}"> {{ $iterate_election['name'] }}</option>
                          @endif
                        @endforeach

                      </select>
                      @if ($errors->has('election_id'))
                      <!--<span class="error">{{ $errors->first('election_id') }}</span>-->
                      <span class="error">{{ __('step1.election_error') }}</span>
                      @endif 
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-2"><label for="statename">{{ __('election_details.state') }}<sup>*</sup></label></div>
                  <div class="col">
                    <div class="" style="width:100%;">
                      <select name="st_code" class="form-control" id="st_code" onchange="filter_respective_acs(this.value)" <?php echo $dis;  ?>>
                        <option value="">{{ __('election_details.selectstate') }}</option>
                      </select>
                      @if ($errors->has('st_code'))
                      <!--<span class="error">{{ $errors->first('st_code') }}</span> -->
					  <span class="error">{{ __('step1.select_state') }}</span> 
                      @endif 
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-2"><label for="statename">{{ __('election_details.ac') }}<sup>*</sup></label></div>
                  <div class="col">
                    <div class="" style="width:100%;">
                      <select name="ac_no" class="consttype form-control" id="ac_no" <?php echo $dis;  ?> onchange="return getStartEndDate();">
                        <option value="">-- {{ __('election_details.selectac') }} --</option>
                      </select>
                      @if ($errors->has('ac_no'))
                      <!--<span class="error">{{ $errors->first('ac_no') }}</span>-->
                      <span class="error">{{ __('step1.select_ac') }}</span>
                      @endif
                    </div>
                  </div>
                </div>




              </div>
              <!--<div class="card-footer">
                <div class="form-group row ">                 
                    <div class="col ">
                      <a href="{{url('nomination/apply-nomination-step-1')}}" id="" class="btn btn-secondary float-left">Back</a>
                    </div>
                    <div class="form-group row float-right" style="margin-right: 13px;">
                      <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button>
                      <button type="submit" id="candnomination" class="btn btn-primary float-left">Save & Next</button>
                    </div>					
                </div>
              </div>-->
			  
			  <div class="card-footer">
                  <div class="form-group row ">
                   <div class="col ">
                  <!-- <div class="col-sm-6 col-12"> <a href="{{url('nomination/apply-nomination-step-1')}}" id="" class="btn btn-lg btn-secondary font-big">Back</a> </div>-->
                  </div>
                    <div class="col ">
                      <div class="form-group row float-right">
						<a href="<?php echo url('/'); ?>/dashboard-nomination-new" class="btn btn-lg font-big dark-pink-btn">{{ __('step1.Cancel') }}</a> 
						&nbsp;
						&nbsp;
						&nbsp;
						  <button type="button" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return sameAbove();">{{ __('step1.Save_Next') }}</button>
                      </div>
                    </div>
                  </div>
                </div>
			     
			  <!-- Modal confirm schedule -->
					<div class="modal fade modal-confirm" id="notStarted">
					<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
					  <div class="modal-content">
					   <div class="pop-header pt-3 pb-1">
						  <div class="animte-tick"><span>&#10003;</span></div>	
						<div class="header-caption">
						  <p style="color:white;font-size:20px;" id="one">{{ __('nomination.notstarted') }}</p>	
						  <p style="color:white;font-size:14px;" id="acm"></p>	
						  
						   <p style="color:white;font-size:14px;display:none" id="bihar">
						    <a style=" color: white; text-decoration-line: underline;" href="https://www.eci.gov.in/eci-backend/public/api/download?url=LMAhAK6sOPBp%2FNFF0iRfXbEB1EVSLT41NNLRjYNJJP1KivrUxbfqkDatmHy12e%2FzX%2FLARKC1lI3JwqUiIIk3e6jbGk1CRYxaGzABkOgdfk8DYQVtl8QBUpiBVKUEB0x1O4jyvJqQIR5ZMChqCiQwfQ%3D%3D" target=_blank>
							  View Notification PDF (General Election)
							</a>	 

							</p> 
						    <p style="color:white;font-size:14px;display:none" id="other">
						    <a style=" color: white; text-decoration-line: underline;" href="https://www.eci.gov.in/eci-backend/public/api/download?url=LMAhAK6sOPBp%2FNFF0iRfXbEB1EVSLT41NNLRjYNJJP1KivrUxbfqkDatmHy12e%2FzX%2FLARKC1lI3JwqUiIIk3e6jbGk1CRYxaGzABkOgdfk%2BECD1BPpOaD90tTXTjOUD0k8gAh89oMPUf2WfwOoBjDg%3D%3D" target=_blank>
						  View Notification PDF (Bye Election)
							</a>
						  </p> 

						  </p>	
						   <!-- <p style="color:white;font-size:14px;display:none" id="others">
						    <a style=" color: white; text-decoration-line: underline;" href="https://www.eci.gov.in/eci-backend/public/api/download?url=LMAhAK6sOPBp%2FNFF0iRfXbEB1EVSLT41NNLRjYNJJP1KivrUxbfqkDatmHy12e%2FzGjJMI0%2FjETs7fjrM8lYn4mJFS24%2B6KLdZH1l7RiuKqEGtRcBKH%2B4ACCxImmH6o%2BYO4jyvJqQIR5ZMChqCiQwfQ%3D%3D" target=_blank>
							View Notification PDF (General AC)
							</a> -->
						  
						</div>		
						</div>
						<!-- Modal footer -->
						<div class="confirm-footer">
						  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
						  <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
						</div>
					  </div>
					</div>
				  </div><!-- End Of confirm Modal popup Div -->		
				  
				  
				   <!-- Modal confirm schedule -->
					<div class="modal fade modal-confirm" id="notStarted2">
					<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
					  <div class="modal-content">
					   <div class="pop-header pt-3 pb-1">
						  <div class="animte-tick"><span>&#10003;</span></div>	
						<div class="header-caption">
						  <p style="color:white;font-size:20px;" id="one2">{{ __('nomination.notstarted') }}</p>	
						  <p style="color:white;font-size:14px;" id="acm2"></p>	
						  
						    <p style="color:white;font-size:14px;display:none" id="bihar2">
						    <a style=" color: white; text-decoration-line: underline;" href="https://www.eci.gov.in/eci-backend/public/api/download?url=LMAhAK6sOPBp%2FNFF0iRfXbEB1EVSLT41NNLRjYNJJP1KivrUxbfqkDatmHy12e%2FzX%2FLARKC1lI3JwqUiIIk3e6jbGk1CRYxaGzABkOgdfk8DYQVtl8QBUpiBVKUEB0x1O4jyvJqQIR5ZMChqCiQwfQ%3D%3D" target=_blank>
							  View Notification PDF (General Election)
							</a>	
						  </p> 
						  <p style="color:white;font-size:14px;display:none" id="other2">
						    <a style=" color: white; text-decoration-line: underline;" href="https://www.eci.gov.in/eci-backend/public/api/download?url=LMAhAK6sOPBp%2FNFF0iRfXbEB1EVSLT41NNLRjYNJJP1KivrUxbfqkDatmHy12e%2FzX%2FLARKC1lI3JwqUiIIk3e6jbGk1CRYxaGzABkOgdfk%2BECD1BPpOaD90tTXTjOUD0k8gAh89oMPUf2WfwOoBjDg%3D%3D" target=_blank>
							View Notification PDF (Bye Election)
							</a>
						  </p>	
						  
						  
						</div>		
						</div>
						<!-- Modal footer -->
						<div class="confirm-footer">
						  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
						  <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
						</div>
					  </div>
					</div>	
			  
            </div>
          
        </div>
      </form>
    </div>
  </section>
  
  
   <!-- Modal confirm schedule -->
	<div class="modal fade modal-confirm" id="ttttttttt">
	<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
	  <div class="modal-content">
	   <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
		<div class="header-caption">
		  <p style="color:white;font-size:20px;">{{ __('messages.lastDayNom') }}</p>	
		</div>		
		</div>
		<!-- Modal footer -->
		<div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
		  <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
		</div>
	  </div>
	</div>
  </div>	
  
  
  
  @endsection
  @section('script')
  <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>
  <script>
    $(document).ready(function(){  

      if($('#breadcrumb').length){
        var breadcrumb = '';
        $.each({!! json_encode($breadcrumbs) !!},function(index, object){
          breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
        });
        $('#breadcrumb').html(breadcrumb);
      }
    });
	
	function getStartEndDate(){  
		var st_code=$("#st_code").val();
		var ac_no=$("#ac_no").val();
		$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/nomination/get-start-end-date", 
			data: {
				"_token": "{{ csrf_token() }}",
				"sId": st_code,
				"ac": ac_no
				},
			dataType: "html",
			success: function(msg){ 
			  var con = msg.split("***");
			   
			  if(con[0]=='EQUOL'){ 
				$('#ttttttttt').modal('show'); 
			  }
			  
			  if(con[0]=='No'){				  
			    var note = 'NA';
				if(st_code=='S04'){
				 note = 'https://eci.gov.in/files/file/13931-press-note-for-the-general-election-to-legislative-assemblies-of-goa-manipur-punjab-uttarakhand-and-uttar-pradesh-2022-reg/';
				} else {
				 note= 'NA';	
				} 
				
			   
				
				
				if(con[4]=='future'){
				   $('#notStarted').modal('show'); 	
				  // if(st_code=='S17'){
				   	  if(st_code=='S04'){
				   	  
				    $("#bihar").show();
				    $("#other").hide();
				   } else {
				   //	alert("11111");
					  $("#bihar").hide();
				      $("#other").show();
				     // $("#others").show();
				   }
				   
				   $('#acm').html('<?php echo __('nomination.dateforAc'); ?> ('+con[3]+') <?php echo __('nomination.is'); ?> : '+con[1]);
				} 
				
				if(con[4]=='past'){
				   $('#notStarted2').modal('show'); 	
				   
				  if(st_code=='S04'){
				  //	alert("ssdds");
				    $("#bihar2").show();
				    $("#other2").hide();
				   } else {
				   	//alert("ssdds1213");
					  $("#bihar2").hide();
				      $("#other2").show();
				   }
				   
				   $('#one2').html('<?php echo __('nomination.past'); ?>');
				   $('#acm2').html('<?php echo __('nomination.lastpast'); ?> ('+con[3]+') <?php echo __('nomination.was'); ?>  : '+con[2]);
				}
				var note = 'NA';
				return false;	
			  } 
			  if(con[0]=='Yes'){ 
				  //document.subElec.submit();
				  
				  //$("#subnnext").show();
				  
			  }
			  if(msg==0){  
				alert("Nomination details not found");
				return false;
			 }
			},
			error: function(error){
				console.log("Error"+error);
				console.log(error.responseText);				
				var obj =  $.parseJSON(error.responseText);
			}
		});
	}
	
	function sameAbove(){  
		var st_code=$("#st_code").val();
		var ac_no=$("#ac_no").val();
		$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/nomination/get-start-end-date", 
			data: {
				"_token": "{{ csrf_token() }}",
				"sId": st_code,
				"ac": ac_no
				},
			dataType: "html",
			success: function(msg){ 
			  var con = msg.split("***");
			   
			  if(con[0]=='EQUOL'){ 
				$('#ttttttttt').modal('show'); 
			  }
			  
			  if(con[0]=='No'){				  
			    var note = 'NA';
				if(st_code=='S04'){
				 note = 'https://eci.gov.in/files/file/13931-press-note-for-the-general-election-to-legislative-assemblies-of-goa-manipur-punjab-uttarakhand-and-uttar-pradesh-2022-reg/';
				} else {
				 note= 'NA';	
				} 
				
			   
				
				
				if(con[4]=='future'){
				   $('#notStarted').modal('show'); 	
				   if(st_code=='S17'){
				    $("#bihar").show();
				    $("#other").hide();
				     $("#others").show();
				   } else {
					  $("#bihar").hide();
				      $("#other").show();
				      $("#others").show();
				   }
				   
				   $('#acm').html('<?php echo __('nomination.dateforAc'); ?> ('+con[3]+') <?php echo __('nomination.is'); ?> : '+con[1]);
				} 
				
				if(con[4]=='past'){
				   $('#notStarted2').modal('show'); 	
				   
				   if(st_code=='S17'){
				    $("#bihar2").show();
				    $("#other2").hide();
				   } else {
					  $("#bihar2").hide();
				      $("#other2").show();
				   }
				   
				   $('#one2').html('<?php echo __('nomination.past'); ?>');
				   $('#acm2').html('<?php echo __('nomination.lastpast'); ?> ('+con[3]+') <?php echo __('nomination.was'); ?>  : '+con[2]);
				}
				var note = 'NA';
				return false;	
			  } 
			  if(con[0]=='Yes'){ 
				  document.subElec.submit(); 
			 }
			  if(msg==0){  
				alert("Nomination details not found");
				return false;
			 }
			},
			error: function(error){
				console.log("Error"+error);
				console.log(error.responseText);				
				var obj =  $.parseJSON(error.responseText);
			}
		});
	}

    function filter_respective_state(election_type_id){ 
      html = '';
      html += "<option value=''><?php echo __('election_details.selectstate'); ?></option>";
      var states = <?php echo json_encode($states); ?>;
	  
      var st_code = "<?php echo $st_code ?>";
      $.each(states, function(index, object){
		  console.log('Below object');
		  console.log(object);
	
        if(object.election_type == election_type_id){ 
		$("#eid").val(object.election_id); 
		if(object.st_code == st_code){
            html += "<option value='"+object.st_code+"' selected='selected'>"+object.st_name+"</option>";
          }else{
            html += "<option value='"+object.st_code+"'>"+object.st_name+"</option>";
          }
        }
      });
	  console.log('Below HML');
	  console.log(html);
      $("#st_code").empty().append(html);
      if(st_code == ''){
        $("#st_code").val($("#st_code option:first").val());
      }
    }

    function filter_respective_acs(st_code){ 
      html = '';
      html += "<option value=''><?php echo __('election_details.selectac'); ?></option></option>";
      var acs = <?php echo json_encode($acs); ?>;
      var etype = $('#election_id').val();
	 
      var st_code = $('#st_code').val();
      var ac_no = "<?php echo $ac_no; ?>";
      $.each(acs, function(index, object){
        if(object.st_code == st_code && object.election_type == etype){
          if(object.ac_no == ac_no){
            html += "<option value='"+object.ac_no+"' selected='selected'>"+object.ac_name+"</option>";
          }else{
            html += "<option value='"+object.ac_no+"'>"+object.ac_name+"</option>";
          }
        }
      });
      $("#ac_no").empty().append(html);
      if(ac_no == ''){
        $("#ac_no").val($("#ac_no option:first").val());
      }
    }

    $(document).ready(function(e){
      filter_respective_state("<?php echo $election_id ?>");
      filter_respective_acs("<?php echo $st_code ?>");
    });


  </script>
  @endsection