@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')

@section('bradcome', 'Index Card Ac Wise')

@section('content')


<style> 

img#theImg{
    display: none;
}
.cent{
    text-align: center;
}
.fa-eye:before {
    content: "\f06e";
    color: #f15d86;
    font-size: 20px;
    margin: auto;
}


</style>
<?php if(Auth::user()->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if(Auth::user()->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}   ?>


<section class="">

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
   <!-- Modal content-->
   <div class="modal-content">
    <!--  <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal">&times;</button>
       <h4 class="modal-title"></h4>
     </div> -->
     <div class="modal-body">
       <p>Please verify all reports then click Confirm Report Verification Button</p>
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     </div>
   </div>
 </div>
</div>

<!-- Modal -->
<div id="myModalnew" class="modal fade" role="dialog">
 <div class="modal-dialog">
   <!-- Modal content-->
   <div class="modal-content">
    <!--  <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal">&times;</button>
       <h4 class="modal-title"></h4>
     </div> -->
     <div class="modal-body">
       <p>Once verification confirmed all the editing in the data will be disabled</p>
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     </div>
   </div>
 </div>
</div>


    <div class="container-fluid">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}</h4></div> 
						

                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive" style="width: 100%;">
                        <!-- Content goes Here -->

		<div class="col-sm-10" style="margin:auto;">				
		<form class="form-inline" style="text-align:center;" method = "post" action="{!! url(''.$prefix.'/statistical-report-listing') !!}">
			@csrf 
            <div class="col-sm-8 form-group">
                <label class="col-sm-4 control-label"><b style="width: 100%;text-align: right;">Select State : &nbsp;</b></label>
		<select class="col-sm-8 form-control" name="st_code"required>
			<option value="" class="form-control">Select State</option>
			@foreach($stateList as $state)
			<option value="{{$state->ST_CODE}}" @if(isset($_POST['st_code']) && ($_POST['st_code']== $state->ST_CODE))    selected @endif>{{$state->ST_NAME}}</option>
			@endforeach
		</select>
        </div>


<div class="col-sm-4 text-left">
		<button class="btn btn-success" style="margin:0px 5px;background-color:#dc3545;color:#fff;border:none;" type="submit">Submit</button>
        </div>
		</form>
		</div>
					@if(isset($_POST['st_code'])) 
					<?php $st_code =  $_POST['st_code']; ?>
					@endif

					@if(isset($_POST['st_code']))
						
                        <div class="col-sm-12 text-center mt-3 mb-3">
                            <span class="h5">Statistical Reports</span>
							

                           
							<?php  if (verifyreport(8888,$st_code) == 0){ ?>
							
								<a style="margin: 16px; float: right; margin-top: 0px;"><button type="button" id="btnSubmitToPublished" class="btn btn-info float-right" onclick="verifycheck(8888,'{{$st_code}}')" style="background-color: #28a745 !important;">All Reports Published On ECI Website</button></a>
							<?php }else{ ?>
							
								<span style="padding: 5px; color: #fff;background-color: #28a745 !important;" class="float-right"> All Reports Published On ECI Website: {{date('d-m-Y H:i A', strtotime(verifyreport(8888,$st_code)))}}</span>
							<?php } ?>
							
							<?php  if (verifyreport(7777,$st_code) == 0){ ?>							
								<a><button type="button" id="btnSubmitToCheckOut" class="btn btn-info float-right" onclick="verifycheck(7777,'{{$st_code}}')">Confirm Report Verification</button></a>
							<?php }else{ ?>
							
								<span style="margin-right: 10px;padding: 5px; color: #fff;background-color: #007bff !important;" class="float-right"> All Reports Finalised On: {{date('d-m-Y H:i A', strtotime(verifyreport(7777,$st_code)))}}</span>
							<?php } ?>
							
                        </div>
						
                        <table class="table table-bordered" style="width: 100%;overflow: hidden;">
                            <thead>
                            <th>SL. No.</th>
                            <th>Report Name</th>
                            <!-- <th style="overflow: hidden;"><p style="text-align: center;">View Report</p></th> -->
							<th><p style="">View Report</p></th>
                            <th><p style="">Check for final preview and Verify</p></th>
                            </thead>
                            <tbody>
                                  
                                <tr><td>1.</td>
                                    <td><a href="{!! url('/'.$prefix.'/other-abbreviations-and-description/'.$_POST['st_code']) !!}" target="_blank">Other Abbreviations and Description 
                                        </a></td>
										
										<?php $st_code =  $_POST['st_code'];
										
											
											
										?>
                                    
									
									<?php  if (verifyreport(1 ,$st_code) != 0){ ?>
									
									
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/other-abbreviations-and-description/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/other-abbreviations-and-description/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									
									
									
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,1,'{{$st_code}}')" <?php  if (verifyreport(1,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(1 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>

                                <tr><td>2.</td>
                                    <td><a href="{!! url('/'.$prefix.'/list-of-successful-candidates/'.$_POST['st_code']) !!}" target="_blank">List of Successful Candidates</a></td>
                                    
									
									<?php  if (verifyreport(2 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/list-of-successful-candidates/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/list-of-successful-candidates/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,2,'{{$st_code}}')" <?php  if (verifyreport(2,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(2 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>

                                <tr><td>3.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/list-of-political-parties-participated/'.$_POST['st_code']) !!}">List Of Political Parties Participated</a></td>
                                    <!--<td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/list-of-political-parties-participated/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td> -->
									
									<?php  if (verifyreport(3 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/list-of-political-parties-participated/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/list-of-political-parties-participated/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,3,'{{$st_code}}')" <?php  if (verifyreport(3,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(3 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
                                <tr><td>4.</td>
                                    <td><a href="{!! url('/'.$prefix.'/highlights/'.$_POST['st_code']) !!}" target="_blank">Highlights
                                        </a></td>
                                    <!--<td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/highlights/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td> -->
									
									<?php  if (verifyreport(4 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/highlights/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/highlights/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,4,'{{$st_code}}')" <?php  if (verifyreport(4,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(4 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>

                                <tr><td>5.</td>
                                    <td><a href="{!! url('/'.$prefix.'/performance-of-political-parties/'.$_POST['st_code']) !!}" target="_blank">Performance of Political Parties</a></td>
                                    <!-- <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/performance-of-political-parties/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td> -->
									
									<?php  if (verifyreport(5 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/performance-of-political-parties/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/performance-of-political-parties/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,5,'{{$st_code}}')" <?php  if (verifyreport(5,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(5 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>

                                <tr><td>6.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/electorsdatasummary/'.$_POST['st_code']) !!}">Electors Data Summary</a></td>
                                    
									
									<?php  if (verifyreport(6 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/electorsdatasummary/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/electorsdatasummary/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,6,'{{$st_code}}')" <?php  if (verifyreport(6,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(6 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
								
								<tr><td>7.</td>
                                    <td><a href="{!! url('/'.$prefix.'/performance-of-women-candidates/'.$_POST['st_code']) !!}" target="_blank">Performance of Women Candidates
                                        </a></td>
                                    
									
									<?php  if (verifyreport(7 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/performance-of-women-candidates/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/performance-of-women-candidates/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,7,'{{$st_code}}')" <?php  if (verifyreport(7,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(7 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
                                  <tr><td>8.</td>
                                    <td><a href="{!! url('/'.$prefix.'/constituency-data-summary/'.$_POST['st_code']) !!}" target="_blank">Constituency Data Summary
                                        </a></td>
                                   
									
									<?php  if (verifyreport(8 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/constituency-data-summary/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/constituency-data-summary/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,8,'{{$st_code}}')" <?php  if (verifyreport(8,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(8 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>

                                <tr><td>9.</td>
                                    <td><a href="{!! url('/'.$prefix.'/candidate-data-summary/'.$_POST['st_code']) !!}" target="_blank">Candidate Data Summary</a></td>
                                   
									
									<?php  if (verifyreport(9 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/candidate-data-summary/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/candidate-data-summary/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,9,'{{$st_code}}')" <?php  if (verifyreport(9,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(9 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>

                                <tr><td>10.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/detailed-results/'.$_POST['st_code']) !!}">Detailed Results</a></td>
                                    
									
									<?php  if (verifyreport(10 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/detailed-results/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/detailed-results/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,10,'{{$st_code}}')" <?php  if (verifyreport(10,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(10 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
								
								  <tr><td>11.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/ac-wise-no-of-electors/'.$_POST['st_code']) !!}">AC Wise Number Of Electors</a></td>
                                    
									
									<?php  if (verifyreport(12 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/ac-wise-no-of-electors/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/ac-wise-no-of-electors/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,12,'{{$st_code}}')" <?php  if (verifyreport(12,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(12 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
								
								<tr><td>12.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/ac-wise-voters-information/'.$_POST['st_code']) !!}">AC Wise Voters Information</a></td>
                                    
									
									<?php  if (verifyreport(13 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/ac-wise-voters-information/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/ac-wise-voters-information/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,13,'{{$st_code}}')" <?php  if (verifyreport(13,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(13 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
								
								
								<tr><td>13.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/ac-wise-candidate-data-summary/'.$_POST['st_code']) !!}"> AC Wise Candidate data Summary </a></td>
                                    
									
									<?php  if (verifyreport(14 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/ac-wise-candidate-data-summary/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/ac-wise-candidate-data-summary/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,14,'{{$st_code}}')" <?php  if (verifyreport(14,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?>>
											<?php  if (verifyreport(14 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
								
								
								

                                <tr><td>14.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/annxure/'.$_POST['st_code']) !!}">ANNXURE - 1 (ELECTORS DATA SUMMARY )</a></td>
                                    
									
									<?php  if (verifyreport(11 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/annxure/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/annxure/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,11,'{{$st_code}}')" <?php  if (verifyreport(11,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?> >
											<?php  if (verifyreport(11 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>



								<tr><td>15.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/constituency-wise-detailed-result/'.$_POST['st_code']) !!}">Constituency wise detailed Result</a></td>
                                    
									
									<?php  if (verifyreport(15 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/constituency-wise-detailed-result/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/constituency-wise-detailed-result/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,15,'{{$st_code}}')" <?php  if (verifyreport(15,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?> >
											<?php  if (verifyreport(15 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>
								
								
								
								<tr><td>16.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/list-of-successful-candidates-b/'.$_POST['st_code']) !!}">List Of the Successful Candidate (B)</a></td>
                                    
									
									<?php  if (verifyreport(16 ,$st_code) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/list-of-successful-candidates-b/'.$_POST['st_code']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{!! url('/'.$prefix.'/list-of-successful-candidates-b/'.$_POST['st_code']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,16,'{{$st_code}}')" <?php  if (verifyreport(16,$st_code) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(7777,$st_code) != 0){ ?> disabled <?php } ?> >
											<?php  if (verifyreport(16 ,$st_code) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>
                                </tr>






                              
                            </tbody>
                        </table>

                    @endif
					
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">

function insert_verify(obj,report_number,st_code) {

  if($(obj).is(":checked")){

    $.ajax({
    type: "GET",
    url: "./statistical-report-listing-verify-checkbox",
    data: {is_verified:1,report_no:report_number, st_code:st_code},
    dataType: "JSON",
    success: function(data) {
    location.reload();
    },
    error: function(data){
            window.console.log(data);
        }

    });

  }else{
    //alert("Not checked"); //when not checked

    $.ajax({
    type: "GET",
    url: "./statistical-report-listing-verify-checkbox",
    data: {is_verified:0, report_no:report_number, st_code:st_code},
    dataType: "JSON",
    success: function(data) {
    location.reload();
    },
    error: function(data){
            window.console.log(data);
        }

    });
  }




}

// to verify all reports are verified or not

function verifycheck(report_number, st_code){
var i=0;
$('.checkifset').each(function () {
         var checked = $(this).val();
         if ($(this).is(':checked')) {
    
                 i++;
             
         } 
     });
  if(i==14){
   // $('.checkifset').prop('disabled',true);\

   

    if(confirm('Once verification confirmed all the editing in the data will be disabled')){

    $.ajax({
    type: "GET",
    url: "./statistical-report-listing-verify-all-report",
    data: {is_verified:1, report_number:report_number, st_code:st_code},
    dataType: "JSON",
    success: function(data) {
    $('.checkifset').prop('disabled',true);
    location.reload();
    },
    error: function(data){
            window.console.log(data);
        }

    });

    }
   
  

  }else{
    $('#myModal').modal('show');
  }
}

</script>
@endsection