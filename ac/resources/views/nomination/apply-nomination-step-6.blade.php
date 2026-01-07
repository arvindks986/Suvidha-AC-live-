        @extends('layouts.theme')
        @section('title', 'Nomination')
        @section('content')
      
		
		<link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
		<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">   
		  
		<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
		<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
		
        <style type="text/css">
          .fullwidth{
            width: 100%;
            float: left;
          }
          .button-next{
            margin-top: 30px;
          }
          .button-next button{
            float: right;
          }
          .affidavit-preview{
            min-height: 600px;
          }
		  .affidavit-preview.min-width{min-height:0px;}
        </style>
        <main role="main" class="inner cover mb-3">
           @if(count($errors->all())>0)
                 <div class="alert alert-danger">
                  <ul>
                   @foreach($errors->all() as $iterate_error)
                   <li><p class="text-left">{!! $iterate_error !!}</p></li>
                   @endforeach
                 </ul>
               </div>
               @endif

               @if (session('flash-message'))
               <div class="alert alert-success"> {{session('flash-message') }}</div>
               @endif

  
<div class="container">
 <div class="step-wrap mt-4">
	 <ul>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step1') }}</span></li>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step2') }}</span></li>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step3') }}</span></li>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step4') }}</span></li>
	   <li class="step-success"><b>&#10004;</b><span>{{ __('step1.step5') }}</span></li>
	   <li class="step-current"><b>&#10004;</b><span>{{ __('step1.step6') }}</span></li>
	   <li class=""><b>&#10004;</b><span>{{ __('step1.step7') }}</span></li>
	 </ul>
 </div>
</div>

</div>
               
       <body onload="return mm();">
        <section >
        

        <div class="container p-0">
		
          <div class="row">

            <div class="col-md-12">
              <div class="card">
				<div class="row" style="margin-top:15px;margin-right:10px;">
				<div class="fullwidth" style="float: left;width: 100%;">
				@if(isset($reference_id) && isset($href_download_application))
                <div class="col-md-5 float-right">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right">{{ __('election_details.ref') }}: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
                    <li class="list-inline-item text-right"><a href="{!! $href_download_application !!}" class="btn btn-primary" target="_blank">{{ __('election_details.down') }}</a></li>
                  </ul>
                </div>
                @endif
              </div>
           </div>
		   <?php 
				$st='';	
				$st2='';	
				if(!empty($assigned_e_affidavit) && (count($af) > 0 )){
				$st='block';	
				$st2='none';	
				} else {
				$st='none';	
				$st2='block';
				}
				?>
		   
               <div class="card-header d-flex align-items-center">
                <!-- <h4>{{ __('part3a.Affidavit') }}</h4>-->
				<h4>{{ __('messages.Affidavit') }}</h4>
               </div>
               <div class="card-body">
				 <div class="card-header d-flex align-items-center">
					
						<div class="col"> 
						
						<div id="my" style="font-size:18px;color:black;"></div>						
						<div id="showprev" style="font-size: 18px; margin-top: 30px; width: 51%; float: right;display:none;">
						 <ul class="list-inline">						  
							<li class="list-inline-item text-right btn btn-primary" onclick="return showPo();">{{ __('messages.preaff') }}</li>
						 </ul>
						</div>
						
						
						<div id="e-affidavit">
						<!--<span style="margin-left: 85px;">e-Affidavit</span>-->
						
						<?php  //	echo "<pre>"; print_r($af);  ?>
						@if(count($af) > 0 )
							
						
						@if(!empty($assigned_e_affidavit))	
						<div id="afterLink">	
						<div style="font-size:18px;color:black;">
						{{ __('messages.thenom') }} - ({{$reference_id}}) {{ __('messages.linked') }} - ({{$assigned_e_affidavit}}) 
						</div>
						
						<div style="float: right; margin-right: 40px;">
						<a href="{{url('part-a-detailed-report?pdf=yes&affidavit_id='.$assigned_e_affidavit)}}" class="btn btn-primary" target="_blank">{{ __('messages.downaff') }}</a>
						</div>
						
						<br>
						
						<div style="font-size: 18px; margin-top: 30px; width: 97%; border: 2px solid gray;">						
						<iframe src="<?php echo url('/'); ?>/part-a-detailed-report?affidavit_id=<?php echo $assigned_e_affidavit ?>"  height="600" width="1000" id="ppalam"></iframe>
						</div>
						</div>
						<br>
						<br>
						<br>
						<br>
						@endif	
						
						 <?php $kk=0; //echo "<pre>"; print_r($af); ?>
						
						@if(empty($assigned_e_affidavit))							
						<span style="margin-left:6em;">{{ __('messages.selaff') }}</span>
						<select name="assign_affidavit" id="assign_affidavit" class="form-control col-sm-4 col-12 ac_no" style="margin-left: 6em;margin-bottom:35px;"
						onchange="return showAction(this.value);">						 
						  <option value="">{{ __('messages.selaff') }}</option>
						 @foreach ($af as $key => $da)
						
						 <?php
						 $finalizeSttaus='';	
						 if($da['finalized']==0 or $da['finalized']==null){
							$finalizeSttaus=''; 
						 } else {
							 $finalizeSttaus=$da['finalized']; 
						 }
						 $kk++;
						 ?>
						  <option  value="{{$da['affidavit_id']}}*{{$finalizeSttaus}}*{{$da['id']}}" <?php if($assigned_e_affidavit==$da['affidavit_id']) { echo "selected";} ?>>{{$da['affidavit_id']}}</option>
						 @endforeach
						</select>		
						
						<div class="modal-content" style="display:none;" id="ifrDiv"> 
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">{{ __('messages.fillaff') }}</h5> 
							<div style="float: right; padding: 3px;margin-bottom:20px;">
							 <div onclick="return closePopup();" class="btn btn-lg  dark-pink-btn">{{ __('messages.Close') }}</div>
							 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							 <div style="float: right; margin-right: -14px;" onclick="return downloadaffidavit();" class="btn btn-lg btn-primary"> {{ __('messages.downaff') }}
							 </div>
							</div>
						  </div>
						  <div class="modal-body" style="border-bottom: 1px solid #e9ecef;">
							<div id="ifr" style="display:block;">
							  <iframe src="<?php echo url('/'); ?>/part-a-detailed-report"  height="600" width="1000" id="myframe"></iframe>
							</div>
						  </div> 
						  <div>
						  <div style="float: right;padding: 3px;margin-bottom:20px;">
						  <div onclick="return closePopup();" class="btn btn-lg  dark-pink-btn"> {{ __('messages.Close') }}</div>
						  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						  <div class="btn  btn-lg btn-primary" onclick="return editEaffidavit();">{{ __('messages.Edit') }}</div> 
						  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						  <div class="btn btn-lg btn-primary" onclick="return MakeEAffidavitFinalize();">{{ __('messages.Finalize') }} </div> 
						  <br>
						  </div>
						  </div>
						</div>
						 <div class="col-md-5"  id="mainDivX" style="display:none;">
						  <ul class="list-inline float-right">						  
							<li class="list-inline-item text-right" onclick="return pageRid();" ><a  class="btn btn-primary" style="color:white;">{{ __('messages.preaff') }}</a></li>
							<!--<li id="link" class="list-inline-item text-right" onclick="return show_affidavit_popup();"><a  class="btn btn-primary" style="color:white;">Link</a></li>-->
						  </ul>
						</div>
						<div class="col-md-5"  id="PreviewX" style="display:{{$st}};">
						  <ul class="list-inline float-right">						  
							<li class="list-inline-item text-right" onclick="return pageRid();" ><a  class="btn btn-primary" style="color:white;">{{ __('messages.preaff') }}</a></li>
						  </ul>
						</div>
						<br>
						<br>
						<br>	
						<div class="col-md-12" id="Instruction1"><span style="color:red">{{ __('messages.Instruction') }}</span>:-<br>
						1.  {{ __('messages.ins1') }}<br><br>
						2.  {{ __('messages.ins2a') }} ({{$reference_id}}) {{ __('messages.ins2b') }}  <br><br>
						3.  {{ __('messages.ins3') }}<br><br>
						4.  {{ __('messages.ins4') }}  <br> <br>
						5.  {{ __('messages.ins5') }} <br><br>
						6.  {{ __('messages.ins6') }}<br><br>
						</div>
						@endif
						
						@else 
						<span class="col-md-5" > {{ __('messages.noaff') }}</span>	<br>
						<span class="col-md-5" onclick="return first();"><a class="btn btn-primary" style="color:white;">{{ __('messages.fillaff') }}</a></span>
						<br><br>
						<div class="col-md-12" id="Instruction2"><span style="color:red">{{ __('messages.Instruction') }}</span>:-<br>
						1. {{ __('messages.ins1') }}<br><br>
						2. {{ __('messages.ins2a') }} ({{$reference_id}}) {{ __('messages.ins2b') }} <br><br>
						3. {{ __('messages.ins3') }} <br><br>
						4. {{ __('messages.ins4') }} <br> <br>
						5. {{ __('messages.ins5') }} <br><br>
						6. {{ __('messages.ins6') }}<br><br>
						</div>	<br>
						@endif
						</div>
						</div>
				</div>
				</div>
                 <div class="row" id="pdf" style="display:{{$st}};margin-left:0px;width:100%;">
                   <div class="col">
				   <div style="font-size:18px;margin-bottom:20px;"> 
				   <span style="color: blue;"> {{ __('messages.Optional') }} </span> : <br> 
				  {{ __('messages.Optional_message') }}</div>
				   <br>
                     <div class="form-group row">
                      <!-- fieldsets -->  
                      <div class="nomination-parts box recognized fullwidth">
                        <form method="post" action="{!! $action !!}" enctype="multipart/form-data">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <input type="hidden" name="recognized_party" value="recognized">
                          <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
                          <div class="fullwidth">                            
                            <div class="fullwidth mb-3">       
                          <div class="file-frame" style="width: 100%;">
                            <button class="file btn btn-primary"  type="button" style="width: 100%;">{{ __('part3a.Browse') }} <i class="fa fa-upload"></i></button>
                            <input type="hidden" name="affidavit" id="affidavit" class="affidavit" value="{{$affidavit}}">
                          </div>  
                            </div>
                            <fieldset class="fullwidth"  style="margin-bottom: 30px;">
                              <div id="affidavit-preview" class="affidavit-preview min-width">
                                <iframe src="" width="100%" height="200"></iframe>
                              </div>
                            </fieldset>
                          </div>
						  <div class="card-footer">
							<div class="row align-items-center">
							  <div class="col-sm-6 col-12"> <a href="{{$href_back}}" id="" class="btn btn-lg btn-secondary font-big">{{ __('step1.Back') }}</a> </div>
							  <div class="col-sm-6 col-12">
								<div class="apt-btn text-right"> 	<span id="checkafferror" style="color:red; margin-right: 50px;"></span>		
								<a href="<?php echo url('/'); ?>/dashboard-nomination-new" class="btn btn-lg font-big dark-pink-btn">{{ __('step1.Cancel') }}</a> 	
								&nbsp;
								&nbsp;
								&nbsp;	
							<!--<button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return checkaff();">{{ __('step1.Save_Next') }}</button>-->
							<button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn">{{ __('step1.Save_Next') }}</button>
								</div>          
							  </div>
							</div>
						  </div> 
						  
                        </form>
                      </div>
                    </form>
                  </div> 
                </div>
              </div>
			   <div class="card-footer" style="display:{{$st2}};" id="back">
				    <div class="row align-items-center">
					  <div class="col-sm-6 col-12"> <a href="{{$href_back}}"  class="btn btn-lg btn-secondary font-big">{{ __('step1.Back') }}</a> </div>
					</div>
				</div> 
            </div>
          </div>
        </div>
      </div>
    </div>    
  </section>
 </body>	
  </main>
  
   <div class="modal fade" id="basicExampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">	
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">{{ __('messages.linkaff') }}</h5> 
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" style="border-bottom: 1px solid #e9ecef;" id="q"> 
			<div class="row">
				<div class="col-md-8 col-12">
				  <ul class="list-inline" style="width: 157%;">
				   <li class="list-inline-item">{{ __('messages.areryousure') }}</span></li>
				  </ul>  
				</div>   
			  </div>
		  </div>
		  <input type="hidden" id="assined_id">
		  <input type="hidden" id="nom" value="{{$reference_id}}">
		  <div id="bt">
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" onclick="return showmsg('loader3');">Link</button>
		  </div>		  
		  <div style="text-align: center;display:none;" id="loader3">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </div>
		  </div>
		  <br>
		  <span style="text-align: center;display:none;" id="bt1"> e-Affidavit linked successfully </span>
		  <br>
		  <button type="button" id="bt2" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;display:none;" onclick="return showreload()">Ok</button>
		</div>
	  </div>
	</div>
	
	
	<div class="modal fade" id="delinkId" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">	
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">De-Link e-Affidavit</h5> 
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" style="border-bottom: 1px solid #e9ecef;" id="qr">
			<div class="row">
				<div class="col-md-8 col-12">
				  <ul class="list-inline" style="width: 157%;">
				   <li class="list-inline-item">Are you sure to de-link?</span></li>
				  </ul>  
				</div>   
			  </div>
		  </div>
		  <input type="hidden" id="didlink">
		  <input type="hidden" id="nom" value="{{$reference_id}}">
		  <div id="btr">
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" onclick="return deLinkToThisId('loader5');">De-Link</button>
		  </div>		  
		  <div style="text-align: center;display:none;" id="loader5">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </div>
		  </div>
		  <br>
		  <span style="text-align: center;display:none;" id="bt1r"> e-Affidavit de-linked successfully </span>
		  <br>
		  <button type="button" id="bt2r" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;display:none;" onclick="return showreload()">Ok</button>
		</div>
	  </div>
	</div>
	
	
	<div class="modal fade" id="first" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">	
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">{{ __('messages.fillaff') }}</h5> 
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" style="border-bottom: 1px solid #e9ecef;">
			<div class="row">
				<div class="col-md-8 col-12">
				  <ul class="list-inline" style="width: 157%;">
				   <li class="list-inline-item">{{ __('messages.ins2a') }} ({{$reference_id}}) {{ __('messages.ins2b') }}  </span></li>
				  </ul>  
				</div>   
			  </div>
		  </div>
		  <input type="hidden" id="assined_id">
		  <input type="hidden" id="nom" value="{{$reference_id}}">
		  <div id="bt">
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;"><a onclick="return closeAdd();" target="_blank" href="<?php echo url('/'); ?>/affidavitdashboard" style="color:white;">
			{{ __('messages.Yes') }}</a></button>
		  </div>
		  </div>
		</div>
	  </div>
	</div>
	<div class="modal fade" id="nf" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">	
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Fill e-Affidavit</h5> 
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" style="border-bottom: 1px solid #e9ecef;">
			<div class="row">
				<div class="col-md-8 col-12">
				  <ul class="list-inline" style="width: 157%;">
				   <li class="list-inline-item">This e-Affidavit not yet finalized. Do you want to finalize this affidavit? </span></li>
				  </ul>  
				</div>   
			  </div>
		  </div>
		  <input type="hidden" id="alam">
		
		  <div id="bt">
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" onclick="return submitforfinalized();">Yes</button>
		  </div>
		  </div>
		</div>
	  </div>
	</div>
  <input type="hidden" id="affidavit_primary_id">
  
  @endsection

  @section('script')
  
  
  
	<script> 
	function downloadaffidavit(){
		var dd = jQuery.noConflict();	
		var afid = dd('#assign_affidavit :selected').val();
		var getid= afid.split("*");
		url="<?php echo url('/'); ?>/part-a-detailed-report?pdf=yes&affidavit_id="+getid[0];
		var win = window.open(url, '_blank');
		win.focus();	
	}
   
	function closeAdd(){
	 var j = jQuery.noConflict();	
	  j('#first').modal('hide');		
	}
	
	
	function mm(){ 
	console.log("This is only testing");
	var ii = jQuery.noConflict();	 
    ii('#ppalam').contents().find('.header').hide();
    ii('#ppalam').contents().find('.breadcrumb-section').hide();
    ii('#ppalam').contents().find('.col').hide();
    ii('#ppalam').contents().find('.step-wrap').hide();
    ii('#ppalam').contents().find('.mastfoot').hide();
    ii('#ppalam').contents().find('.nextBtn').hide();
	
	
	}
	
	
	function MakeEAffidavitFinalize(){
		var j = jQuery.noConflict();	
		var aid = j("#alam").val();
		
			j.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/finalize-e-affidavit", 
				data: {
					"_token": "{{ csrf_token() }}",
					"aid": aid,
					"nom": "<?php echo $reference_id  ?>",
					},
				dataType: "html", 
				success: function(msg){ 
				if(msg==1){
					location.reload();
					
				} else {
					j("#bt1").show().text("Some issue occurred, please try later");
				}
				},
				error: function(error){ alert(error);
					console.log("Error"+error);
					console.log(error.responseText);				
					var obj =  j.parseJSON(error.responseText);
				}
			});
		
	}
  
	function closePopup(){
	var j = jQuery.noConflict();	
	j('#ifrDiv').hide();	
	j('#assign_affidavit').val("");	
	}
	
	
	function showIfr(){ 
	 var j = jQuery.noConflict();	
	  j('#ifrDiv').modal('show');	
	}
  
  
	function showPo(){
	    var j = jQuery.noConflict();	
		var assined_id = j("#alam").val();
		url="<?php echo url('/'); ?>/part-a-detailed-report?affidavit_id="+assined_id;
		var win = window.open(url, '_blank');
		win.focus();	
		
	}
	
	function pageRid(){
		var j = jQuery.noConflict();	
		var assined_id = j("#assined_id").val();	
		url="<?php echo url('/'); ?>/part-a-detailed-report?affidavit_id="+assined_id;
		var win = window.open(url, '_blank');
		win.focus();
	}
  
	
	function editEaffidavit(){
		
		
		
		var j = jQuery.noConflict();	
		var alam = j("#alam").val();	
		var affidavit_primary_id = j("#affidavit_primary_id").val();	
		url="<?php echo url('/'); ?>/affidavitdashboard/edit/"+affidavit_primary_id;
		var win = window.open(url, '_blank');
		win.focus();
		j('#nf').modal('hide');	
	}
	
	function submitforfinalized(){
		
		var j = jQuery.noConflict();	
		var alam = j("#alam").val();	
		url="<?php echo url('/'); ?>/preview?affidavit_id="+alam;
		var win = window.open(url, '_blank');
		win.focus();
		j('#nf').modal('hide');	
	}
    function first(){
		var j = jQuery.noConflict();	
		j('#first').modal('show');	
	}
  
	function showreload(){
	window.location.reload();	
	}
	
	
	function showAction(val){ 
	var j = jQuery.noConflict();	
	var mdata=val.split('*');
	
	//alert(mdata[2]);
	j("#affidavit_primary_id").val(mdata[2]);
	    
	j('#myframe').contents().find('.header').hide();
    j('#myframe').contents().find('.breadcrumb-section').hide(); 
	j('#myframe').contents().find('.col').hide();
	j('#myframe').contents().find('.btn').hide();
	j('#myframe').contents().find('.nextBtn').hide();
	j('#myframe').contents().find('.mastfoot').hide();
	j('#myframe').contents().find('.step-wrap').hide();
	j('#myframe').contents().find('.modal-content').css("width", '179%');
	j('#myframe').contents().find('.modal-content').css("margin-left", '-200px');
	j('#myframe').contents().find('.modal-content').css("margin-top", '-30px');
	
	
	
	
	
	
	var val = mdata[0];
	j("#alam").val(val);
	
	
	
	if(mdata[1]==''){
		j('#ifrDiv').show();	
		//j('#nf').modal('show');	
		j('#mainDiv').hide();	
		j('#Preview').hide();	
		j('#pdf').hide();	
		j('#back').show();	
		return false;	
	}
	
			j.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/assign-e-affidavit", 
				data: {
					"_token": "{{ csrf_token() }}",
					"aid": val,
					"nom": "<?php echo $reference_id  ?>",
					},
				dataType: "html", 
				success: function(msg){ 
				if(msg==1){ location.reload();
					j("#my").hide().html("<?php echo __('messages.thenom');  ?> - (<?php echo $reference_id  ?>) <?php echo __('messages.linked');  ?> - ("+val+") ")
					//j("#bt").hide();
					//j("#link").hide();
					//j("#Instruction2").hide();
					//j("#Instruction1").hide();
					//j("#showprev").hide();					
					//j("#bt1").show();					
					//j("#bt1").show();					
					//j("#pdf").show();					
					//j("#afterLink").show();
					//j("#back").hide();	
					//j("#e-affidavit").hide();	
					//j("#q").hide();
				} else {
					j("#bt").hide();
					j("#bt2").hide();
					j("#Instruction2").show();
					j("#Instruction1").show();
					j("#afterLink").hide();
					j("#link").show();
					j("#showprev").hide();					
					j("#pdf").hide();		
					j("#e-affidavit").show();	
					j("#back").show();		
					j("#q").show();
					j("#bt1").show().text("Some issue occurred, please try later");
				}
				},
				error: function(error){
					console.log("Error"+error);
					console.log(error.responseText);				
					var obj =  j.parseJSON(error.responseText);
				}
			});
	
	
	
	
	if(val==''){
		alert("Please select E-affidavit");
		j("#assign_affidavit").fucus();
		return false;
	}
	j("#mainDiv").show();		
	j("#Preview").hide();		
	
	
	j("#assined_id").val(val);		
	
	
	  if(val=="<?php  echo $assigned_e_affidavit?>"){
	    j("#link").hide();			
		j("#pdf").show();		
		j("#back").hide();		
	  } else {
		j("#link").show();  
		j("#pdf").hide();		
		j("#back").show();		
	  }
	  
				
	  
	  
	 


		/////////////////////
		
		
	  
	  
	  
	}
	
	function show_affidavit_popup_for_delink(aid){
	var j = jQuery.noConflict();	
	j("#didlink").val(aid);
	j("#bt2").hide();
	j("#bt1").hide();
	j("#loader3").hide();
	j("#bt1").hide();
	j("#bt").show();
	j('#delinkId').modal('show');	
	}
	
	
	function show_affidavit_popup(){
	var j = jQuery.noConflict();	
	j("#bt2").hide();
	j("#bt1").hide();
	j("#loader3").hide();
	j("#bt1").hide();
	j("#bt").show();
	j('#basicExampleModal').modal('show');	
	}
	
	function deLinkToThisId(id){ 
		  var j = jQuery.noConflict();	
		  j('#'+id).show();
		  var assined_id = j("#didlink").val();	
		  var nom = j("#nom").val();	
		 if(assined_id==''){
			alert("Please select e-affidavit");
			return false;	
		 }
		 j.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/delink-e-affidavit", 
				data: {
					"_token": "{{ csrf_token() }}",
					"aid": assined_id,
					"nom": nom,
					},
				dataType: "html",
				success: function(msg){ 
				if(msg==1){
					j("#btr").hide();
					j("#bt1r").show();					
					j("#bt2r").show();
					j("#qr").hide();
				} else {
					j("#btr").hide();
					j("#bt2r").hide();
					j("#qr").show();
					j("#bt1r").show().text("Some issue occurred, please try later");
				}
				
				  
				},
				error: function(error){
					console.log("Error"+error);
					console.log(error.responseText);				
					var obj =  j.parseJSON(error.responseText);
				}
			});	
	}
	
	function showmsg(id){ 
		  var j = jQuery.noConflict();	
		  j('#'+id).show();
		  var assined_id = j("#assined_id").val();	
		  var nom = j("#nom").val();	
		 if(assined_id==''){
			alert("Please select e-affidavit");
			return false;	
		 }
		 j.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/assign-e-affidavit", 
				data: {
					"_token": "{{ csrf_token() }}",
					"aid": assined_id,
					"nom": nom,
					},
				dataType: "html",
				success: function(msg){ 
				if(msg==1){
					j("#bt").hide();
					j("#bt1").show();					
					j("#bt2").show();
					j("#q").hide();
				} else {
					j("#bt").hide();
					j("#bt2").hide();
					j("#q").show();
					j("#bt1").show().text("Some issue occurred, please try later");
				}
				
				  
				},
				error: function(error){
					console.log("Error"+error);
					console.log(error.responseText);				
					var obj =  j.parseJSON(error.responseText);
				}
			});	
	}
  
    var x = jQuery.noConflict();	
    x(document).ready(function(){
	 var k = jQuery.noConflict();			
     if(k('#breadcrumb').length){
       var breadcrumb = '';
       k.each({!! json_encode($breadcrumbs) !!},function(index, object){
        breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
      });
       k('#breadcrumb').html(breadcrumb);
     }
   });
	function checkaff(){
	 var L = jQuery.noConflict();		
	 var checkaff = L("#affidavit").val();	
		if(checkaff==''){
		  L("#checkafferror").html("<?php echo __('part3a.plaff') ?>"); 
		  L("#affidavit").focus();	
		 return false;
		}
	}
    function read_url(input) {
	 var M = jQuery.noConflict();			
      if (input.files && input.files[0]) {
        var file = input.files[0];
        var fileName = file.name;
        var ext = fileName.split('.').reverse()[0]
        if(file.type === 'application/pdf') {
          var file_object = URL.createObjectURL(file);
          M('.affidavit-preview').html('<iframe src="' +  file_object + '" width="100%" height="500"></iframe>');
        }else{
          alert("Please select a PDF file.");
        }
      }
    }

  </script>

<script type="text/javascript">
  var Y = jQuery.noConflict();	
  Y(document).ready(function () {
    Y('.file').on('click', function() {
      Y('#form-upload').remove();
      Y('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');
      Y('#form-upload input[name=\'file\']').trigger('click');
      if (typeof timer != 'undefined') {
        clearInterval(timer);
      }
      timer = setInterval(function() {
        if (Y('#form-upload input[name=\'file\']').val() != '') {
          clearInterval(timer);
          Y.ajax({
            url: "<?php echo $href_file_upload; ?>?_token=<?php echo csrf_token(); ?>",
            type: 'POST',
            dataType: 'json',
            data: new FormData(Y('#form-upload')[0]),
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
              Y('.file-frame').removeClass("file-frame-error");
              Y('.file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
              Y('.file').prop('disabled', true);
              Y('.text-danger').remove();
            },
            complete: function() {
              Y('.file i').replaceWith('<i class="fa fa-upload"></i>');
              Y('.file').prop('disabled', false);
            },
            success: function(json) { console.log(json);
              if(json['success'] == false) {
                Y('.file-frame').after("<span class='text-danger'>"+'<?php echo __('part3a.nota') ?>'+"</span>");
                Y('.file-frame').addClass("file-frame-error");
              }
              if (json['success'] == true) {
                Y('.file-frame').find('.affidavit').val(json['path']);
                Y('.affidavit-preview iframe').attr("src","<?php echo url('/'); ?>/"+json['path']);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			  Y('.file-frame').after("<span class='text-danger'><?php echo __('messages.file_type_error'); ?></span>");
            }
          });
        }
      }, 500);
    });

    <?php if($affidavit){ ?>
      Y('.affidavit-preview iframe').attr("src","<?php echo url($affidavit); ?>");

    <?php } ?>

  });

	 
	
</script>
@endsection