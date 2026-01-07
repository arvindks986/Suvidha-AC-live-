@extends('admin.layouts.ac.dashboard-theme')
@section('content')
			<link rel="stylesheet" href="{{ asset('theme/feedback/css/bootstrap.min.css') }}">
					<link rel="stylesheet" href="{{ asset('theme/feedback/css/custom.css') }}">
		<!-- 	<style>
				.font-300 {
					font-weight: 300;
				}
			
				.grayClr {
					color: #555;
				}
			
				.smallText {
					font-size: 1.0rem;
				}
			
				.green-btn {
					bacground: green !important;
				}
			
				.green-btn:hover,
				.green-btn:focus {
					bacground: green !important;
				}
			</style> -->
			<div class="container mt-5">
			<form name="appModule" id="appModule" method="post">
								<input type="hidden" name="formtype" value=1>
								{!! Form::token() !!}
			<div class="card">
				
				<div class="card-header">
					<h3 class="text-center mb-0 text-pink" > Booth Application feedback & Suggestions </h3>
				</div>
				
				<div class="card-body">
				<div class="row">
						<div class="col bg-white text-center">
							<ul class="list-inline py-3">
								<li class="list-inline-item"><span class="text-pink">Officer Name : </span></li>
								<li class="list-inline-item"> <span class="font-300 ">{{ $uname }}</span> </li>
								<li class="list-inline-item"><span class="text-pink">Designation :</span></li>
								<li class="list-inline-item"><span class="font-300 "> {{ $udesig }} </span></li>
								<li class="list-inline-item"><span class="text-pink">State : </span></li>
								<li class="list-inline-item"><span class="font-300   pr-2">{{ $ustate }}</span></li>
								<li class="list-inline-item"><span class="text-pink">Place : </span></li>
								<li class="list-inline-item"><span class="font-300   pr-2"> {{ $uplace }} </span></li>
							</ul>
						</div>
					</div>
					<div class="row">
						
								
								<div class="col-md-12">
									<div class="row">
										<div class="col-sm-5">
											<h5 class="mt-2">Select Polling Station :</h5>
										</div>
										<div class="col-sm-7">
											<select class="form-control" id="appid" name="appid" required>
												<option value="" disabled selected>---Please Select---</option>
												@foreach( $apps as $app)
												@if($app->CCODE == $apid)
												<option value={{$app->CCODE.'_'.$app->PART_NO.'_'.$app->PS_NO.'_'.$app->ST_CODE.'_'.$app->AC_NO}} selected>{{$app->PS_NAME_EN}}</option>
												@else
												<option value={{$app->CCODE.'_'.$app->PART_NO.'_'.$app->PS_NO.'_'.$app->ST_CODE.'_'.$app->AC_NO}}>{{$app->PS_NAME_EN}}</option>
												@endif
												@endforeach
											</select>
										</div>
									</div>
								</div>
								
								<div class="border-top1"></div>
								
								<div class="col-md-12">
									<div class="row">
										<div class="col-sm-5">
											<h5 class="mt-2"> Select User :</h5>
										</div>
										<div class="col-sm-7">
											<select class="form-control" id="moduleid" name="moduleid" required>
												<option value="" disabled selected>---Please Select---</option>
												@foreach( $mods as $modl)
												@if($modl->id == $mdid)
												<option value={{$modl->id}} selected>{{$modl->name}}</option>
												@else
												<option value={{$modl->id}}>{{$modl->name}}</option>
												@endif
												@endforeach
											</select>
										</div>
									</div>
								</div>
								
							
							
					</div>
					
				</div>
				<div class="card-footer">
					<button type="submit" class="green-btn btn btn-primary float-right">Load Form</button>
				</div>
			
			</div>
				</form>
			
				<div class="clearfix"></div>
				@if($formtype==2)
				<div class="clearfix"></div>
			
			
				<section class="bg-white bdrline" id="dform">
					@if($orec==1)
					<h4 class="text-center py-4 text-green"> This survey is completed by you on {{$stime}} </h4>
					@else
					<h4 class="text-center py-4 text-pink"><span class="text-pink">*</span> All fields are mandatory. Please fill the data
						carefully. </h4>
					@endif
					<div class="col-12">
						<form name="appModule1" id="appModule1" method="post">
							<input type="hidden" name="formtype" value=2>
							<input type="hidden" name="appid" value={{ $apid }}>
							<input type="hidden" name="moduleid" value={{ $mdid }}>
							<input type="hidden" name="userid" value={{ $usrid }}>
							<input type="hidden" name="orec" value={{ $orec }}>
							{!! Form::token() !!}
							<div id="ENCORE_App">
								<table class="table table-bordered">
									<tbody>
										<tr>
											<td>1</td>
											<td width="44%">Did you use this module ?</td>
											<td>
												<label class="radio-inline"> <input type="radio" name="q1" value=1
														<?php echo ($q1 == 1)? "checked":"";?> {{ ($orec ==1) ? 'disabled' : '' }}>
													Yes</label>
												<label class="radio-inline"> <input type="radio" name="q1" value=2
														<?php echo ($q1 == 2)? "checked":"";?> {{ ($orec ==1) ? 'disabled' : '' }}>
													No</label>
											</td>
										</tr>
										<tr>
											<td>2.</td>
											<td width="48%">Was it easy to use ?</td>
											<td width="48%">
												<label class="radio-inline"> <input type="radio" name="q2" value=1
														<?php echo ($q2 == 1)? "checked":"";?> {{ ($orec ==1) ? 'disabled' : '' }}>
													Yes</label>
												<label class="radio-inline"> <input type="radio" name="q2" value=2
														<?php echo ($q2 == 2)? "checked":"";?> {{ ($orec ==1) ? 'disabled' : '' }}>
													No</label>
											</td>
										</tr>
										<tr>
											<td>3.</td>
											<td width="48%">Was the Options Simple / Complex ?</td>
											<td width="48%">
												<label class="radio-inline"> <input type="radio" name="q3" value=1
														<?php echo ($q3 == 1)? "checked":"";?> {{ ($orec ==1) ? 'disabled' : '' }}>
													Simple</label>
												<label class="radio-inline"> <input type="radio" name="q3" value=2
														<?php echo ($q3 == 2)? "checked":"";?> {{ ($orec ==1) ? 'disabled' : '' }}>
													Complex</label>
											</td>
										</tr>
										<tr>
											<td>4</td>
											<td width="48%">What was the best feature in this module ?</td>
											<td width="48%">
												<textarea class="form-control" rows="2" placeholder="Type here :" name="q4" required
													{{ ($orec ==1) ? 'disabled' : '' }}>{{ $q4 }}</textarea>
											</td>
										</tr>
										<tr>
											<td>5</td>
											<td width="48%">What feature in the module need improvement ?</td>
											<td width="48%">
												<textarea class="form-control" rows="2" placeholder="Type here :" name="q5" required
													{{ ($orec ==1) ? 'disabled' : '' }}>{{ $q5 }}</textarea>
											</td>
										</tr>
										<tr>
											<td>6.</td>
											<td width="48%">How would you rate?</td>
											<td width="48%">
												<ul class="list-inline bdrLine">
													<li class="list-inline-item">1.</li>
													<li class="list-inline-item width-180">Overall: </li>
													<li class="list-inline-item">
														<select name="q6a" id="q6a" class="form-control" required
															{{ ($orec ==1) ? 'disabled' : '' }}>
															<option value="" disabled selected>---</option>
															<option value=1 <?php echo ($q61 == 1)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>1</option>
															<option value=2 <?php echo ($q61 == 2)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>2</option>
															<option value=3 <?php echo ($q61 == 3)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>3</option>
															<option value=4 <?php echo ($q61 == 4)? "selected":"";?>>4</option>
															<option value=5 <?php echo ($q61 == 5)? "selected":"";?>>5</option>
														</select>
													</li>
													<li class="list-inline-item" id="6as">
														<?php
																		for($i=0;$i<$q61;$i++)
																		echo ' <span><i class="fa fa-star fa-lg text-green" aria-hidden="true"></i></span>';
																	?>
													</li>
			
												</ul>
												<div class="clearfix"></div>
												<ul class="list-inline bdrLine">
													<li class="list-inline-item">2.</li>
													<li class="list-inline-item width-180">Speed: </li>
													<li class="list-inline-item">
														<select name="q6b" id="q6b" class="form-control" required
															{{ ($orec ==1) ? 'disabled' : '' }}>
															<option value="" disabled selected>---</option>
															<option value=1 <?php echo ($q62 == 1)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>1</option>
															<option value=2 <?php echo ($q62 == 2)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>2</option>
															<option value=3 <?php echo ($q62 == 3)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>3</option>
															<option value=4 <?php echo ($q62 == 4)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>4</option>
															<option value=5 <?php echo ($q62 == 5)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>5</option>
														</select>
													</li>
													<li class="list-inline-item" id="6bs">
														<?php
																		for($i=0;$i<$q62;$i++)
																		echo ' <span><i class="fa fa-star fa-lg text-green" aria-hidden="true"></i></span>';
																	?>
													</li>
												</ul>
												<div class="clearfix"></div>
												<ul class="list-inline bdrLine">
													<li class="list-inline-item">3.</li>
													<li class="list-inline-item width-180">Usefulness: </li>
													<li class="list-inline-item">
														<select name="q6c" id="q6c" class="form-control" required
															{{ ($orec ==1) ? 'disabled' : '' }}>
															<option value="" disabled selected>---</option>
															<option value=1 <?php echo ($q63 == 1)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>1</option>
															<option value=2 <?php echo ($q63 == 2)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>2</option>
															<option value=3 <?php echo ($q63 == 3)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>3</option>
															<option value=4 <?php echo ($q63 == 4)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>4</option>
															<option value=5 <?php echo ($q63 == 5)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>5</option>
														</select>
													</li>
													<li class="list-inline-item" id="6cs">
														<?php
																		for($i=0;$i<$q63;$i++)
																		echo ' <span><i class="fa fa-star fa-lg text-green" aria-hidden="true"></i></span>';
																	?>
													</li>
												</ul>
												<div class="clearfix"></div>
												<ul class="list-inline bdrLine">
													<li class="list-inline-item">4.</li>
													<li class="list-inline-item width-180">Training: </li>
													<li class="list-inline-item">
														<select name="q6d" id="q6d" class="form-control" required
															{{ ($orec ==1) ? 'disabled' : '' }}>
															<option value="" disabled selected>---</option>
															<option value=1 <?php echo ($q64 == 1)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>1</option>
															<option value=2 <?php echo ($q64 == 2)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>2</option>
															<option value=3 <?php  echo($q64 == 3)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>3</option>
															<option value=4 <?php echo ($q64 == 4)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>4</option>
															<option value=5 <?php echo ($q64 == 5)? "selected":"";?>
																{{ ($orec ==1) ? 'disabled' : '' }}>5</option>
														</select>
													</li>
													<li class="list-inline-item" id="6ds">
														<?php
																		for($i=0;$i<$q64;$i++)
																		echo ' <span><i class="fa fa-star fa-lg text-green" aria-hidden="true"></i></span>';
																	?>
													</li>
												</ul>
												<div class="clearfix"></div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="text-center mt-3">
									<button type="button" class="btn btn-primary" id="btdb">Dashboard</button>
									@if($orec == 0)
									<button type="submit" class=" btn btn-primary">Submit</button> <!-- green-btn -->
									@endif
			
			
								</div>
								<p class="py-2"></p>
							</div>
						</form>
					</div>
			
				</section>
			
				@endif
			
			</div>
			</div>
			<input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
			<input type="hidden" id="ftype" value={{ $formtype }}>
			
			<!-- <footer>
				<div class="container">
					<div class="row">
						<div class="col-12">
							<p class="text-center pt-3">© Copyright Election Commission of India</p>
						</div>
					</div>
				</div>
			</footer> -->
			<!-- Modal -->
			<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
				aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLongTitle"></h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							Thank you for submitting the survey form. Your response has been recorded.
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			
						</div>
					</div>
				</div>
			</div>
			<!-- Modal -->
			</div>
			<script src="{{ asset('theme/feedback/js/jquery-3.3.1.min.js') }}"></script>
			<script src="{{ asset('theme/feedback/js/bootstrap.min.js') }}"></script>
			
			
			<script type="text/javascript">
				$.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							}
						});
						
						$("#moduleid").change(function(e){
							var ftype = $( "#ftype").val(); 
							if(ftype > 1)
							{
								$('#dform').hide();
							}
						});
						
						$("#appid").change(function(){				
							var base_url = $("#base_url").val();
							var idval = $( "#appid").val(); 
							var ftype = $( "#ftype").val(); 
							if(ftype > 1)
							{
								$('#dform').hide();
							}
							
							<?php if($udesig == 'DEO'){ $des = 'acdeo';} elseif($udesig == 'ROAC'){ $des = 'roac';} ?>
								var desig = '<?php echo $des ?>';
								var url='';
								url= base_url+'/'+desig+'/ajaxGetModule';
							
							//alert(idval);
							$.ajax({
								type:'POST',
								url: url,
								data:{appid : idval},
								success:function(data) {
									//alert(data.msg);
									var mopt='<option value="" selected disabled>---Please Select---</option>';
									var mdls = data.msg.split("##");
									for(var i=0; i < (mdls.length-1); i++)
									{
										var opt = mdls[i].split("||");
										mopt+='<option value='+opt[0]+'>'+opt[1]+'</option>';
									}
									$("#moduleid").html(mopt);	
								}
							});
							
						});
						
						$("#btdb").click(function(e){
							var base_url = $("#base_url").val() + "/officer-login";
							window.location.replace(base_url);
						});
						
						///Pop-up on Page Load
						
						$(document).ready(function() {
							@if($status==1)
							$('#exampleModalCenter').modal('show');
							@endif
							@if($formtype == 3)
							@if($q1==4)
							$('#other').css('display', 'block'); 
							@else
							$('#other').css('display', 'none');
							@endif
							@endif
						});
						
						
						$('#other').change(function(){
							$('#othval').val($('#other').val());
						});
						
						$('#types').change(function(){
							var textval = $('#types option:selected').html();
							$('#other').val(textval);
							if($('#types').val() == 4){ 
								//$('#other').val("");
								$('#other').css('display', 'block'); 
								$('#other').val($('#othval').val());
							}
							else{
								$('#other').css('display', 'none');
							}
						});
						
						$('#q6a').change(function(){
							var txtval="";
							var sval = $('#q6a').val();
							for(var i=0; i < sval ; i++)
							txtval = txtval + ' <span><i class=\"fa fa-star fa-lg text-green\" aria-hidden=\"true\"></i></span>';
							//alert(txtval);
							$('#6as').html(txtval);	
						});
						
						$('#q6b').change(function(){
							var txtval="";
							var sval = $('#q6b').val();
							for(var i=0; i < sval ; i++)
							txtval = txtval + ' <span><i class=\"fa fa-star fa-lg text-green\" aria-hidden=\"true\"></i></span>';
							//alert(txtval);
							$('#6bs').html(txtval);	
						});
						
						$('#q6c').change(function(){
							var txtval="";
							var sval = $('#q6c').val();
							for(var i=0; i < sval ; i++)
							txtval = txtval + ' <span><i class=\"fa fa-star fa-lg text-green\" aria-hidden=\"true\"></i></span>';
							//alert(txtval);
							$('#6cs').html(txtval);	
						});
						
						$('#q6d').change(function(){
							var txtval="";
							var sval = $('#q6d').val();
							for(var i=0; i < sval ; i++)
							txtval = txtval + ' <span><i class=\"fa fa-star fa-lg text-green\" aria-hidden=\"true\"></i></span>';
							//alert(txtval);
							$('#6ds').html(txtval);	
						});
						
						$('#q6e').change(function(){
							var txtval="";
							var sval = $('#q6e').val();
							for(var i=0; i < sval ; i++)
							txtval = txtval + ' <span><i class=\"fa fa-star fa-lg text-green\" aria-hidden=\"true\"></i></span>';
							//alert(txtval);
							$('#6es').html(txtval);	
						});
						
						$('#q3s').change(function(){
							var txtval="";
							var sval = $('#q3s').val();
							for(var i=0; i < sval ; i++)
							txtval = txtval + ' <span><i class=\"fa fa-star fa-lg text-green\" aria-hidden=\"true\"></i></span>';
							//alert(txtval);
							$('#q3as').html(txtval);	
						});				
			</script>
@endsection()