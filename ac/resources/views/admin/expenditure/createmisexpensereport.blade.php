@extends('admin.layouts.ac.expenditure-theme')
@section('content')
<?php
//print_r($user_data);die;
$st = getstatebystatecode($user_data->st_code);
 
$pcdetails = getacbystdistrict($user_data->st_code, $user_data->dist_no); 
 
 $namePrefix = \Route::current()->action['prefix'];

 $acno = $Acdetail->AC_NO;
 
 if($namePrefix=="/acdeo"){
    $urlback="/acdeo/constiuancyinfo/".base64_encode($acno); 
 }
 if($namePrefix=="/acceo"){
  $urlback="/acceo/allscrutiny";
 }
 if($namePrefix=="/eci-expenditure"){
    $urlback="/eci-expenditure/eciallscrutiny";
}
  $issueslist = array("Hearing Done", "Reply Issued", "Notice Issued"); 
 ?>
<main role="main" class="inner cover mb-3">
    <style>
        span.help-block strong.user {
            color: red;
        }
        .form-control:focus[readonly], .form-control:hover[readonly]{background-color:#e9ecef; }
        .mis_gap {
            margin: 18px;
            background: #b1287a;
            color: #fff;
            padding: 10px;
        }
        .final_action_btn{
            background-color: #bb4292;
            color: #fff;
            margin-left: 29px;
            border-radius: 2px;
            padding: 5px 10px 8px 10px;
        }

        a.final_action_btn:hover{
            color:#fff !important;
            text-decoration: none;
        }
    </style>
 

    <section class="mt-5">

        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%;">

                    <div class=" card-header">
                        <div class=" row d-flex align-items-center">
                            @if($user_data->role_id=='5')
                            <div class="col"><h4> District Electoral Officer (DEO)</h4></div>
                            @endif

                            @if($user_data->role_id=='18')
                            <div class="col"><h4> District Electoral Officer (DEO)</h4></div>
                            @endif

                            @if($user_data->role_id=='4')
                            <div class="col"><h4> Chief Electoral Officer (CEO)</h4></div>
                            @endif

                            @if($user_data->role_id=='28')
                            <div class="col"><h4> Election Commission of India (ECI)</h4></div>
                            @endif



                            <?php if (!empty($_GET['id'])) { ?>
                                <button type="button" id="" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Preview</button>
                            <?php } ?>
                            &nbsp;&nbsp;
<!-- <a href="{{url('/acdeo/constiuancyinfo')}}/{{base64_encode($acno)}}"><button type="submit" class="btn btn-primary"><font color="black">Back</font></button></a> -->
<a href="{{url('/')}}{{$urlback}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                        </div>

                    </div>



                    <?php // print_r($ReportSingleData);die; ?>
                    <div class="card-body">
                        @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
                        @php Session::forget('message'); @endphp
                        @elseif (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ Session::get('error') }} <br/>

                        </div>
                        @php Session::forget('error'); @endphp
                        @endif

                        <form method="post" action="{{url($namePrefix.'/StoreMisExpenseReport')}}" id="StoreMisExpenseReport">
                            <input type="hidden" name="candidate_id" value="{{!empty($cand_data->candidate_id)?$cand_data->candidate_id:''}}">
                              <input type="hidden" name="constituency_no" value="{{!empty($cand_data->constituency_no)?$cand_data->constituency_no:''}}">
                            <div class=" row">
                                {{ csrf_field() }}


                                <!--election LIST DROPDOWN ENDS-->
                                <!--AC/PC LIST DROPDOWN STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    

                                    <label for="state"> Name of AC</label>
                                    <input type="text" name="constituency_no" value="{{ !empty($Acdetail->AC_NAME)?  $Acdetail->AC_NAME:'N/A'}}" class="form-control" readonly="readonly">
                                      <input type="hidden" name="constituency_id" value="{{ !empty($acno)?  $acno:'0'}}" class="form-control" readonly="readonly">

                                  

                                  
										</div>
                                <!--AC/PC LIST DROPDOWN end-->
                                <!--NAME OF CONTESTING CANDIDATE LIST DROPDOWN STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="contensting_candiate">Name of Contesting Candidate</label>

                                    <input type="text" name="contensting_candiate" value="{{!empty($cand_data->cand_name)?$cand_data->cand_name:''}}" class="form-control" readonly="readonly">

                                   <!--  <select name="contensting_candiate" id="district" class="form-control" >
                                        <option value="">Select name of Contesting Candidate</option>
                                        @if(!empty($contensting_candiate))
                                        @foreach($contensting_candiate as $contensting_candiateitem)  
                                        <option  value="{{$contensting_candiateitem->cand_name}}">{{$contensting_candiateitem->cand_name }}</option>

                                        @endforeach
                                        @endif 

                                        @if ($errors->has('contensting_candiate'))
                                        <span class="help-block">
                                            <strong class="user">{{ $errors->first('ac_pc') }}</strong>
                                        </span>
                                        @endif
                                        </select> -->
                                </div>
                                <!--NAME OF CONTESTING CANDIDATE LIST DROPDOWN ENDS-->

                                <!--Date of Declaration of result STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Result Declaration Date</label>
                                    <input type="date" class="form-control" name="date_of_declaration" value="{{!empty($ReportSingleData['date_of_declaration']) && strtotime($ReportSingleData['date_of_declaration'])>0 ? $ReportSingleData['date_of_declaration']:'2019-05-23'}}" placeholder="Date &amp; time"  readonly="readonly">
                                    @if ($errors->has('date_of_declaration'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_declaration') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--Date of Declaration of result ENDS-->

                                <!--RETURN LIST DROPDOWN STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Return Type</label>

                                    <input type="text" class="form-control" name="return_status" value="{{!empty($countElectedCandidate) && $countElectedCandidate >0 ?'Returned':'Non-Returned'}}"  readonly="readonly">
                                </div>
                                <!--RETURN LIST DROPDOWN ENDS-->
								 @if($user_data->role_id=='28')
								<!--Scrunity Report Submit by DEO STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Scrunity Report Submit by DEO</label>
                                   <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="report_submitted_date" id="report_submitted_date" class="form-control" <?php if($namePrefix=="/eci-expenditure" || $namePrefix=="/pcceo"){ echo "readonly disabled";}?> placeholder="Date &amp; time" value="{{!empty($ReportSingleData['report_submitted_date'])?$ReportSingleData['report_submitted_date']:''}}">
                                </div>
                                <!--Scrunity Report Submit by DEO ENDS-->
								
								<!--Scrunity Send to CEO by DEO STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Scrutiny Report Send to CEO by DEO</label>
                                   <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_sending_deo" id="date_of_sending_deo" class="form-control" <?php if($namePrefix=="/eci-expenditure" || $namePrefix=="/pcceo"){ echo "readonly disabled";}?> placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_sending_deo'])?$ReportSingleData['date_of_sending_deo']:''}}">
                                </div>
                               <!--Scrunity Send to CEO by DEOENDS-->
								
								<!--Scrunity Report Receipt by CEO STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Scrunity Report Receipt By CEO</label>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_receipt" id="date_of_receipt" class="form-control" <?php if($namePrefix=="/eci-expenditure" || $namePrefix=="/pcceo"){ echo "readonly disabled";}?> placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_receipt'])?$ReportSingleData['date_of_receipt']:''}}">
                                </div>
                                <!--Scrunity Receipt by CEO ENDS-->
								
								<!--Scrunity Report send to ECI by CEO STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Scrunity Report Send to ECI By CEO</label>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_sending_ceo" id="date_of_sending_ceo" class="form-control" <?php if($namePrefix=="/eci-expenditure" || $namePrefix=="/pcceo"){ echo "readonly disabled";}?> placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_sending_ceo'])?$ReportSingleData['date_of_sending_ceo']:''}}">
                                </div>
                                <!--Scrunity Report send to ECI by CEO ENDS-->


                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of Reciept of DEO's scrutiny report from the CEO/DEO</label><span class="redClr font-weight-bold h6">*</span></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_receipt_eci" id="date_of_receipt_eci" class="form-control" placeholder="Date &amp; time"  value="{{!empty($ReportSingleData['date_of_receipt_eci'])?$ReportSingleData['date_of_receipt_eci']:''}}" required="required">
                                    @if ($errors->has('date_of_receipt_eci'))
                                    <span class="help-block">
                                         <strong class="user">{{ $errors->first('date_of_receipt_eci') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                                @endif

                                @if($user_data->role_id=='28')  
 
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Final Action</label>
                                    <select name="final_action" id="final_action" class="form-control" >
                                        <option value="">Select Final Action</option>
                                        <option value="Closed" <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Closed") { ?> selected <?php } ?>>Closed</option>
                                        <option value="Disqualified"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Disqualified") { ?> selected <?php } ?>>Disqualified</option>

                                         <option value="Notice Issued"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Notice Issued") { ?> selected <?php } ?>>Notice Issued</option>

                                          <option value="Reply Issued"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Reply Issued") { ?> selected <?php } ?>>Reply Issued</option>

                                           <option value="Hearing Done"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Hearing Done") { ?> selected <?php } ?>>Hearing Done</option>

                                            <option value="Case Dropped"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Case Dropped") { ?> selected <?php } ?>>Case Dropped</option>
                                    </select>
                                    @if ($errors->has('final_action'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('final_action') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif 

                                <!--NATURE OF DEFAULT A/C LIST DROPDOWN STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Nature of Default in A/C <span class="redClr font-weight-bold h6">*</span></label>
                                    <select name="nature_of_default_ac" id="nature_of_default_ac" required="required" class="form-control" <?php if($namePrefix=="/pcceo"){ echo "readonly disabled";}?>>
                                        <option value="">Select Nature of Default in A/C</option>
                                        @foreach ($nature_of_default_ac as $nature_ac )
                                        <option <?php
                                        if (!empty($ReportSingleData['nature_of_default_ac'])) {
                                            if ($ReportSingleData['nature_of_default_ac'] == $nature_ac->id) {
                                                echo "selected";
                                            }
                                        }
                                        ?>  value="{{ $nature_ac->id }}" >{{$nature_ac->title}}</option>

                                        @endforeach

                                    </select>
                                    @if ($errors->has('nature_of_default_ac'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('nature_of_default_ac') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--NATURE OF DEFAULT A/C LIST DROPDOWN ENDS-->

                                @if($user_data->role_id=='5' || $user_data->role_id=='18')
                                <!--DATE OF SENDING DEO'S SCRUTINY STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Date of sending DEO'S Scrutiny Report to the ECI through the CEO within 45 days of declaration of results" style="color: #212529;"><label for="ScheduleList">Date of Sending DEO'S Scrutiny Report to ECI through the CEO<span class="redClr font-weight-bold h6">*</span></label></a>
                                    <input type="date"  min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' 30 days ')):''}}" max="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' 90 days ')):''}}" name="date_of_sending_deo" id="date_of_sending_deo" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_sending_deo'])?$ReportSingleData['date_of_sending_deo']:''}}" required="required">
                                    @if ($errors->has('date_of_sending_deo'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_deo') }}</strong>
                                     </span>
                                    @endif
                                </div>

                                @endif

                                  <!--DATE OF SENDING OF DEO TO CEO Scrunity STARTS-->	
								 @if($user_data->role_id=='4')   
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of Sending DEO'S Scrutiny Report to the CEO</label></a>
                                    <input type="date"  disabled  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_sending_deo" id="date_of_sending_deo" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_sending_deo'])?$ReportSingleData['date_of_sending_deo']:''}}">
                                    @if ($errors->has('date_of_sending_deo'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_deo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif  
                             <!--DATE OF SENDING OF DEO TO CEO Scrunity ENDS-->	
							 
							
								
                                <!--NATURE OF DEFAULT A/C ENDS-->
                                @if($user_data->role_id=='5' || $user_data->role_id=='18' )
                                <!-- DATE OF RECEIPT OF ECI NOTICE STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">In Case of Default Date of Receipt of ECI Notice</label></a>
                                    <input type="date"   min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_receipt" id="date_of_receipt" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_receipt'])?$ReportSingleData['date_of_receipt']:''}}" readonly="readonly">
                                    @if ($errors->has('date_of_receipt'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_receipt') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif
								 
							    
								
                                <!--DATE OF RECEIPT OF DEO's Scrunity STARTS-->		
                                @if($user_data->role_id=='4')   
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of Receipt of DEO's Scrunity Report</label></a>
                                    <input type="date"   min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_receipt" id="date_of_receipt" class="form-control" required="required" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_receipt'])?$ReportSingleData['date_of_receipt']:''}}">
                                    @if ($errors->has('date_of_receipt'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_receipt') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif  
                              <!--DATE OF RECEIPT OF DEO's Scrunity ENDS-->	

                               <!--DATE OF SENDING DEO'S SCRUTINY STARTS-->
                                @if($user_data->role_id=='4')
                                <div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Date of Sending DEO'S Scrutiny Report to the commission" style="color: #212529;"><label for="ScheduleList">Date of Sending DEO'S Scrutiny Report to the commission</label></a>
                                    <input type="date"  min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' +30 days ')):''}}" 

 @if(in_array($ReportSingleData['final_action'], $issueslist))
max="{{date('Y-m-d')}}"
 @else
 max="{{!empty($resultDeclarationDate['start_result_declared_date']) ? date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' +90 days ')):''}}" 
 @endif


name="date_of_sending_ceo" id="date_of_sending_ceo" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_sending_ceo']) ? $ReportSingleData['date_of_sending_ceo']:''}}" required="required">
                                    @if ($errors->has('date_of_sending_ceo'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_ceo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif
								<!--DATE OF SENDING DEO'S SCRUTINY END-->							  

                                @if($user_data->role_id=='28')  
							 
                                  <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Whether acknowledge from the candidate is attached with supplementary report</label></a>
											<select name="acknowldge_from_the_candidate_eci" id="acknowldge_from_the_candidate_eci" class="form-control" >
                                        <option value="">Select Type</option>
                                        <option value="yes" <?php
                                        if (!empty($ReportSingleData['acknowldge_from_the_candidate_eci']) && $ReportSingleData['acknowldge_from_the_candidate_eci'] == "yes") {
                                            echo "selected";
                                        }
                                        ?> >Yes</option>
                                        <option value="no" <?php
                                        if (!empty($ReportSingleData['acknowldge_from_the_candidate_eci']) && $ReportSingleData['acknowldge_from_the_candidate_eci'] == "no") {
                                            echo "selected";
                                        }
                                        ?> >No</option>
                                    </select>
									@if ($errors->has('acknowldge_from_the_candidate_eci'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('acknowldge_from_the_candidate_eci') }}</strong>
                                    </span>
                                    @endif
                                </div>
									
                                  <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of any additinal information has been sought from the DEO/Candidate</label></a>
                                    <input type="date"   min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}"  name="date_of_sending_additional_info_eci" id="date_of_sending_additional_info_eci" class="form-control" placeholder="Date &amp; time"  value="{{!empty($ReportSingleData['date_of_sending_additional_info_eci'])?$ReportSingleData['date_of_sending_additional_info_eci']:''}}" >
                                    @if ($errors->has('date_of_sending_additional_info_eci'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_additional_info_eci') }}</strong>
                                    </span>
                                    @endif
                                </div>
								
                                @endif          
                                <!--DATE OF RECEIPT OF ECI NOTICE ENDS-->

                                <!-- DATE OF SERVICE OF ECI NOTICE STARTS-->
                                @if($user_data->role_id=='5' || $user_data->role_id=='18')  
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of Service of ECI Notice</label></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}"  max="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 45 days ')):''}}" name="date_of_issuance_notice" id="date_of_issuance_notice"  class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_issuance_notice'])?$ReportSingleData['date_of_issuance_notice']:''}}" readonly="readonly">
                                    @if ($errors->has('date_of_issuance_notice'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_issuance_notice') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <!--DATE OF SERVICE OF ECI NOTICE ENDS-->

                                <!-- DATE OF ADDITIONAL INFORMATION STARTS-->

                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of Seeking Additional Information</label></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_sending_additional_info" id="date_of_sending_additional_info"  class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_sending_additional_info'])?$ReportSingleData['date_of_sending_additional_info']:''}}"
                                           @if(in_array($ReportSingleData['final_action'], $issueslist)) ''  @else readonly="readonly" @endif>

                                    @if ($errors->has('date_of_sending_additional_info'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_additional_info') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif 



                                <!--DATE OF ADDITIONAL INFORMATION ENDS-->

                                @if($user_data->role_id=='5' || $user_data->role_id=='18')
                                <!-- DATE OF SENDING ACKNOWLEDGEMENT TO ECI STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of Sending Acknowledgement to ECI</label></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_sending_ack_eci" id="date_of_sending_ack_eci" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_sending_ack_eci'])?$ReportSingleData['date_of_sending_ack_eci']:''}}"
                                           @if(in_array($ReportSingleData['final_action'], $issueslist)) ''  @else readonly="readonly" @endif
                                           >

                                    @if ($errors->has('date_of_sending_ack_eci'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_ack_eci') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif
                                <!--DATE OF SENDING ACKNOWLEDGEMENT TO ECI ENDS-->

                                @if($user_data->role_id=='5' || $user_data->role_id=='18')

                                <!--DATE OF RECEIPT REPLY STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Date of Receipt of Reply-cum-representation from the candidate on ECI Notice" style="color: #212529;"><label for="ScheduleList">Date of Receipt of Reply-cum-representation from the candidate on ECI Notice</label></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_receipt_represetation" id="date_of_receipt_represetation" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_receipt_represetation'])?$ReportSingleData['date_of_receipt_represetation']:''}}"
                                           @if(in_array($ReportSingleData['final_action'], $issueslist)) ''  @else readonly="readonly" @endif >

                                    @if ($errors->has('date_of_receipt_represetation'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_receipt_represetation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--NATURE OF RECEIPT REPLY ENDS-->
                                <!--DATE OF SENDING SUPPLEMENTARY REPORT STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Date of Sending Supplementary Report on ECI Notice if any ,together of acknowledge from the DEO" style="color: #212529;"><label for="ScheduleList">Date of Sending Supplementary Report on ECI Notice if any ,together of acknowledge from the DEO</label></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_sending_supplimentary" id="date_sending_supplimentary" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_sending_supplimentary'])?$ReportSingleData['date_sending_supplimentary']:''}}" @if(in_array($ReportSingleData['final_action'], $issueslist)) ''  @else readonly="readonly" @endif >
                                    @if ($errors->has('date_sending_supplimentary'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_sending_supplimentary') }}</strong>
                                    </span>
                                    @endif         
                                </div>

                                @endif 


                                @if($user_data->role_id=='28')
                                <div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Date of Receipt Supplementary Report on Notice from DEO/CEO ( within 5 days if reply received & 25 days if reply not recieved from the candiddate) " style="color: #212529;"><label for="ScheduleList">Date of Receipt Supplementary Report on Notice from DEO/CEO ( within 5 days if reply received & 25 days if reply not recieved from the candiddate)  </label></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_sending_supplimentary_eci" id="date_sending_supplimentary_eci" class="form-control" placeholder="Date &amp; time"  value="{{!empty($ReportSingleData['date_sending_supplimentary_eci'])?$ReportSingleData['date_sending_supplimentary_eci']:''}}">
                                    @if ($errors->has('date_sending_supplimentary_eci'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_sending_supplimentary_eci') }}</strong>
                                    </span>
                                    @endif         
                                </div>
                                @endif  

                                <!--DATE OF SENDING SUPPLEMENTARY REPORT ENDS-->

                                <!--DATE OF SENDING SUPPLEMENTARY REPORT STARTS-->
                                @if($user_data->role_id=='4')

                                <div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Date of Receipt of Notice and Service" style="color: #212529;"><label for="ScheduleList">Date of Receipt of Notice and Service</label></a>
                                    <input type="date" disabled="disabled" min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_receipt_notice_service" id="date_of_receipt_notice_service" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_issuance_notice'])?$ReportSingleData['date_of_issuance_notice']:''}}">
                                    @if ($errors->has('date_of_issuance_notice'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_issuance_notice') }}</strong>
                                    </span>
                                    @endif         
                                </div>
                                @endif       
                                <!--DATE OF ISSUANCE NOTICE  ENDS-->


                                @if($user_data->role_id=='28')
                                <!--DATE OF SENDING SUPPLEMENTARY REPORT STARTS-->
                                <div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Date of issuance of notice (within 6 months from the receipt of DEO's srcutiny report) " style="color: #212529;"><label for="ScheduleList">Date of issuance of notice (within 6 months from the receipt of DEO's srcutiny report)   </label></a>
                                    <input type="date"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" name="date_of_issuance_notice" id="date_of_issuance_notice" class="form-control" placeholder="Date &amp; time" value="{{!empty($ReportSingleData['date_of_issuance_notice'])?$ReportSingleData['date_of_issuance_notice']:''}}">
                                    @if ($errors->has('date_of_issuance_notice'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_issuance_notice') }}</strong>
                                    </span>
                                    @endif         
                                </div>
                                <!--DATE OF ISSUANCE NOTICE  ENDS--> 
								<?php //print_r($ReportSingleData); ?>
								 <!--NOTICE SENDS TO CEO/DEO STARTS-->
								<div class="col-sm-6" id="form_new_mis">
                                    <a href="#" data-toggle="tooltip" title="Current Status" style="color: #212529;"><label for="ScheduleList">Send Notice To</label></a>
                                    <select name="notice_send_to" id="notice_send_to" class="form-control" >
                                        <option value="">Select Notice Send</option>
                                        <option value="ceo" <?php if (($ReportSingleData['final_by_ceo']=='0') && !empty($ReportSingleData['final_action']) && ($ReportSingleData['final_action'] == "Notice Issued" || $ReportSingleData['final_action'] == "Reply Issued" || $ReportSingleData['final_action'] == "Hearing Done")) { ?> selected <?php } ?>>CEO</option>
                                    </select>
                                           
                                </div>
                                  <!--NOTICE SENDS TO CEO/DEO ENDs-->
                                @endif

                                
                                @if(($ReportSingleData['final_action']=="Notice Issued" || $ReportSingleData['final_action']=="Reply Issued" || $ReportSingleData['final_action']=="Hearing Done") && ($user_data->role_id=='18' || $user_data->role_id=='5'))  
                                
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Final Action</label>
                                    <select name="final_action" id="final_action" class="form-control" required="required" readonly disabled="disabled">
                                        <option value="">Select Final Action</option>
                                        <option value="Closed" <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Closed") { ?> selected <?php } ?>>Closed</option>
                                        <option value="Disqualified"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Disqualified") { ?> selected <?php } ?>>Disqualified</option>

                                         <option value="Notice Issued"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Notice Issued") { ?> selected <?php } ?>>Notice Issued</option>

                                          <option value="Reply Issued"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Reply Issued") { ?> selected <?php } ?>>Reply Issued</option>

                                           <option value="Hearing Done"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Hearing Done") { ?> selected <?php } ?>>Hearing Done</option>

                                            <option value="Case Dropped"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Case Dropped") { ?> selected <?php } ?>>Case Dropped</option>
                                    </select>
                                    @if ($errors->has('final_action'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('final_action') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif 


                                @if($user_data->role_id=='4' && ($ReportSingleData['final_action']=="Notice Issued" || $ReportSingleData['final_action']=="Reply Issued" || $ReportSingleData['final_action']=="Hearing Done") &&  $user_data->role_id=='4')  
 
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Final Action</label>
                                    <select name="final_action" id="final_action" class="form-control" required="required" readonly disabled="disabled">
                                        <option value="">Select Final Action</option>
                                        <option value="Closed" <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Closed") { ?> selected <?php } ?>>Closed</option>
                                        <option value="Disqualified"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Disqualified") { ?> selected <?php } ?>>Disqualified</option>

                                         <option value="Notice Issued"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Notice Issued") { ?> selected <?php } ?>>Notice Issued</option>

                                          <option value="Reply Issued"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Reply Issued") { ?> selected <?php } ?>>Reply Issued</option>

                                           <option value="Hearing Done"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Hearing Done") { ?> selected <?php } ?>>Hearing Done</option>

                                            <option value="Case Dropped"  <?php if (!empty($ReportSingleData['final_action']) && $ReportSingleData['final_action'] == "Case Dropped") { ?> selected <?php } ?>>Case Dropped</option>
                                    </select>
                                    @if ($errors->has('final_action'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('final_action') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif 


                                @if($user_data->role_id=='4')
                                <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Whether any additional information has been sought by the commission from the DEO </label></a>
                                    <select name="date_of_sending_additional_info_ceo" id="date_of_sending_additional_info_ceo" class="form-control" required="required">
                                        <option value="">Select Type</option>
                                        <option value="yes" <?php
                                        if (!empty($ReportSingleData['return_status']) && $ReportSingleData['date_of_sending_additional_info_ceo'] == "yes") {
                                            echo "selected";
                                        }
                                        ?> >Yes</option>
                                        <option value="no" <?php
                                        if (!empty($ReportSingleData['date_of_sending_additional_info_ceo']) && $ReportSingleData['date_of_sending_additional_info_ceo'] == "no") {
                                            echo "selected";
                                        }
                                        ?> >No</option>
                                    </select>
                                    @if ($errors->has('date_of_sending_additional_info_ceo'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_additional_info_ceo') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                
                                 @if(in_array($ReportSingleData['final_action'], $issueslist))
                                 <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Send Notice to DEO </label></a>
                                    <select name="send_notice_deo"
                                     <?php
                                    if( in_array($ReportSingleData['final_action'], $issueslist)){

                                     ?>
                                      
                                  <?php }else{  ?>
                                            disabled
                                            <?php } ?>

                                    id="send_notice_deo" class="form-control" >
                                        <option value="">Select</option>
                                        <option value="deo" >DEO</option>                                         
                                    </select>
                                    @if ($errors->has('date_of_sending_additional_info_ceo'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_of_sending_additional_info_ceo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif

                                 <div class="col-sm-6" id="form_new_mis">
                                    <label for="ScheduleList">Date of sending Notice and Service to DEO</label>
                                      <input type="date" name="date_sending_notice_service_to_deo"  class="form-control"  name="date_sending_notice_service_to_deo"
                                       <?php
                                    if(!empty($ReportSingleData['final_action']) && in_array($ReportSingleData['final_action'], $issueslist)){

                                     ?>
                                      
                                  <?php } else {?>
                                  disabled
                                            <?php } ?>

                                       value="{{!empty($ReportSingleData['date_sending_notice_service_to_deo'])?$ReportSingleData['date_sending_notice_service_to_deo']:''}}">
                                    
                                    @if ($errors->has('date_sending_notice_service_to_deo'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('date_sending_notice_service_to_deo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif

                                @if($user_data->role_id=='28')
                                 <div class="col-md-4" >  
                                 <label for="ScheduleList">Comment by DEO</label>                                    
                                    <div class="alert alert-dark" role="alert">                                       
                                          <p> {{ !empty($ReportSingleData['comment_by_ro'])? $ReportSingleData['comment_by_ro']:'N/A'}}</p>
                                                                                    
                                   </div> 
                               </div>
                               <div class="col-md-4" >  
                                 <label for="ScheduleList">Comment by CEO</label>                                    
                                    <div class="alert alert-dark" role="alert">                                       
                                          <p> {{ !empty($ReportSingleData['comment_by_ceo'])? $ReportSingleData['comment_by_ceo']:'N/A'}}</p>
                                                                                    
                                   </div> 
                               </div>
                                <div class="col-md-12 soughtCom" id="form_new_mis" >
                                    <label for="ScheduleList">Comment</label>  
 <textarea class="form-control" rows="4" name="comment_by_eci" id="sending_additional_info_sought" >{{$ReportSingleData['comment_by_eci']}}</textarea> 
                                </div>
                                @endif

                                @if($user_data->role_id=='4')
                                  <div class="col-md-6" >  
                                 <label for="ScheduleList">Comment by DEO</label>                                    
                                    <div class="alert alert-dark" role="alert">                                       
                                          <p> {{ !empty($ReportSingleData['comment_by_ro'])? $ReportSingleData['comment_by_ro']:'N/A'}}</p>
                                                                                    
                                   </div> 
                               </div>
                                <div class="col-md-12 soughtCom" id="form_new_mis" >
                                    <label for="ScheduleList">Comment</label>  
  <textarea class="form-control" rows="4" name="comment_by_ceo" id="sending_additional_info_sought" >{{$ReportSingleData['comment_by_ceo']}}</textarea> 
                                </div>
                                @endif
	
		<!--RETURN LIST DROPDOWN STARTS-->


                                @if($user_data->role_id=='5' || $user_data->role_id=='18')
                                <div class="col-md-12 soughtCom" id="form_new_mis" >
                                    <label for="ScheduleList">Comment</label>  
<textarea class="form-control" rows="4" name="comment_by_ro" id="sending_additional_info_sought" >{{$ReportSingleData['comment_by_ro']}}</textarea> 
                                </div>
                                @endif

                                




  								<?php
                                if ($namePrefix == "/acdeo") {


                                    ?>
                                    <div class="col-md-6 soughtCom" >
                                            <label for="">Dated<span class="redClr font-weight-bold h6">*</span></label>  
                                            <input type="date" min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 1 days ')):''}}" max="{{date('Y-m-d')}}" name="report_submitted_date" required="required" class="form-control"  name="report_submitted_date" value="{{$ReportSingleData['report_submitted_date']}}">
                                        </div>
                                    <?php
                                    if ($ReportSingleData['final_by_ro'] != "1" && !empty($ReportSingleData['date_of_sending_deo'])) {
                                        ?>
                                          
                                       
                                    <?php }
                                } ?>
                                <span class="uploadmessage"></span>

                                <div class="col-md-12">
                                    <label for="">&nbsp;</label>
                                    <div>
                                    	<?php
                                        if($namePrefix=="/acdeo")
                                        {
                                              if($ReportSingleData['final_by_ro']!="1" && !empty($ReportSingleData['date_of_sending_deo']))
                                              {?>
 											    <!-- <input type="file" name="signedfile" id="signedfileupload"> -->
                                                <input type="submit" value="Update" class="btn btn-primary">
                                        <a href="#" class="final_action_btn comfirmReport" id="{{!empty($cand_data->candidate_id)?$cand_data->candidate_id:'0'}}&ac_no={{!empty($CONST_NO)?$CONST_NO:'0'}}" >Do you want to finalize?</a>

                                             <?php }else{ ?>
                                                <input type="submit" value="SUBMIT" class="btn btn-primary">
                                             <?php }   
                                        }
                                        elseif($namePrefix=="/acceo")
                                        {
                                            if($ReportSingleData['final_by_ceo']!="1" && !empty($ReportSingleData['date_of_receipt']))
                                              {?>
                                                <input type="submit" value="Update" class="btn btn-primary">

                                                
                                        <a href="#" class="final_action_btn comfirmReport" id="{{!empty($cand_data->candidate_id)?$cand_data->candidate_id:'0'}}&ac_no={{!empty($CONST_NO)?$CONST_NO:'0'}}" >Do you want to finalize?</a>
                                             <?php }else{?>
                                             <input type="submit" value="SUBMIT" class="btn btn-primary">
                                             <?php } 

                                        }
                                        elseif($namePrefix =="/eci-expenditure")
                                         {
                                            if($ReportSingleData['final_by_eci']!="1" && !empty($ReportSingleData['date_of_sending_additional_info_eci']))
                                              { ?>
                                            <input type="submit" value="Update" class="btn btn-primary">


                                        <!-- <a href="#" class="final_action_btn comfirmReport" id="{{!empty($cand_data->candidate_id)?$cand_data->candidate_id:'0'}}" >Do you want to finalize?</a> -->

                                         <a href="#" class="final_action_btn comfirmReport" id="{{!empty($cand_data->candidate_id)?$cand_data->candidate_id:'0'}}&ac_no={{!empty($CONST_NO)?$CONST_NO:'0'}}" >Do you want to finalize?</a>
                                              <?php }else{?>
                                             <input type="submit" value="SUBMIT" class="btn btn-primary">   
                                             <?php } 
                                             }

                                            ?>
                                    		

                                    </div>

                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


 <!-- Modal -->
        <div class="modal fade" id="myModalFINAL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">
               <div class="modal-content">
                    <div class="modal-header">
                       <h6 class="modal-title" id="myModalLabel">Kindly make sure, do you want to finalize all entries?</h6>
                   </div>
                   <div class="modal-footer mb-2">
                        <input type="button" value="Ok" id="fianlform" canID ="{{!empty($cand_data->candidate_id)?$cand_data->candidate_id:''}}" acno ="{{!empty($acno)?$acno:''}}" class="btn btn-primary mt-2">
                        <input type="button" value="Cancel" id="" class="btn btn-default mt-2" data-dismiss="modal">
                   </div>
               </div>
           </div>
        </div>
        <!-- end defectform -->
<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">
                                //*******************EXTRA VALIDATION METHODS STARTS********************//
                                //maxsize
                                $.validator.addMethod('maxSize', function (value, element, param) {
                                    return this.optional(element) || (element.files[0].size <= param)
                                });
                                //minsize
                                $.validator.addMethod('minSize', function (value, element, param) {
                                    return this.optional(element) || (element.files[0].size >= param)
                                });
                                //alphanumeric
                                $.validator.addMethod("alphnumericregex", function (value, element) {
                                    return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
                                });
                                //alphaonly
                                $.validator.addMethod("onlyalphregex", function (value, element) {
                                    return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
                                });
                                //without space
                                $.validator.addMethod("noSpace", function (value, element) {
                                    return value.indexOf(" ") < 0 && value != "";
                                }, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//

//*******************ECI FILTER FORM VALIDATION STARTS********************//
                                $("#EciCustomReportFilter").validate({
                                    rules: {
                                        state: {required: true, noSpace: true},
                                        ScheduleList: {number: true},
                                    },
                                    messages: {
                                        state: {
                                            required: "Select state name.",
                                            noSpace: "State name must be without space.",
                                        },
                                        ScheduleList: {
                                            number: "Scedule ID should be numbers only.",
                                        },
                                    },
                                    errorElement: 'div',
                                    errorPlacement: function (error, element) {
                                        var placement = $(element).data('error');
                                        if (placement) {
                                            $(placement).append(error)
                                        } else {
                                            error.insertAfter(element);
                                        }
                                    }
                                });
//********************ECI FILTER FORM VALIDATION ENDS********************//
                                $(document).ready(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });


                                $('select[name="state"]').on('change', function () {
                                    var stateID = $(this).val();

                                    if (stateID) {
                                        $.ajax({
                                            url: "{{url('/')}}/eci-expenditure/ajax-district/" + encodeURI(stateID),
                                            type: "GET",
                                            dataType: "json",
                                            success: function (data) {
                                                console.log(data);
                                                $('select[name="district"]').empty();
                                                $.each(data, function (key, value) {
                                                    $('select[name="district"]').append('<option value="' + value.DIST_NO + '">' + value.DIST_NAME + '</option>');
                                                });
                                            }
                                        });
                                    } else {
                                        $('select[name="district"]').empty();
                                    }
                                });
// end state here

</script>
<script type="text/javascript">

$(document).on('click','.comfirmReport',function(){
   $('#myModalFINAL').modal('show');  
});
    // $('select[name="date_of_sending_additional_info_ceo"]').on('change', function () {
    //     var value = $(this).val();
    //     if (value == "Yes")
    //     {
    //         $('.soughtCom').css("display", "block");
    //         $("#sending_additional_info_sought").attr("readonly", "readonly");

    //     } else
    //     {
    //         $('.soughtCom').css("display", "none");
    //     }

    // });
    // select election type start 
    $('select[name="electionType"]').on('change', function () {

        var stateID = $('select[name="state"]').val();
        var districtId = $('select[name="district"]').val();
        var electionType = $(this).val();

        if (stateID && districtId && electionType) {
            $.ajax({
                url: "{{url('/')}}/eci-expenditure/getACPC",
                type: "get",
                data: {"stateID": stateID,
                    "districtId": districtId,
                    "electionType": electionType},
                success: function (data) {

                    $('select[name="ac_pc"]').empty();
                    $.each(data, function (key, value) {
                        $('select[name="ac_pc"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('select[name="ac_pc"]').empty();
        }
    });
// end election type
    // select contesting candidate start 
    $('select[name="ac_pc"]').on('change', function () {

        var stateID = $('select[name="state"]').val();
        var districtId = $('select[name="district"]').val();
        var ac_pc = $('select[name="ac_pc"]').val();

        if (stateID && districtId && electionType) {
            $.ajax({
                url: "{{url('/')}}/eci-expenditure/getcontestingCandiate",
                type: "get",
                data: {"stateID": stateID,
                    "districtId": districtId,
                    "ac_pc": ac_pc},
                success: function (data) {
                    $('select[name="contensting_candiate"]').empty();
                    $.each(data, function (key, value) {
                        $('select[name="contensting_candiate"]').append('<option value="' + value.cand_name + '">' + value.cand_name + '</option>');
                    });
                }
            });
        } else {
            $('select[name="contensting_candiate"]').empty();
        }
    });
// end contesting candidate  

   $("#fianlform").click(function () {
	  
        var candidate_id = $(this).attr('canID');
        var acno = $(this).attr('acno');
        var namePrefix = "<?php echo $namePrefix; ?>"; 
       // var answer = confirm('Are You Sure want to confirm the Report?');
       
        // if (answer) {
            jQuery.ajax({
                url: "{{url('/')}}"+namePrefix+"/confirmReport",
                type: 'GET',
                data: {candidate_id: candidate_id,acno: acno},
                dataType: 'html',
                success: function (response) {
                    response = response.trim();
                    if (response == 1)
                    {
					  if(namePrefix == "/eci-expenditure"){
							window.location.href="{{url('/eci-expenditure/eciallscrutiny')}}";

					  }
					  if(namePrefix == "/acdeo"){
						window.location.href="{{url('/acdeo/constiuancyinfo')}}/{{base64_encode($acno)}}";  
					  }
					  
					  if(namePrefix == "/acdeo"){
						window.location.href="{{url('/acdeo/constiuancyinfo')}}/{{base64_encode($acno)}}";  
					  }
					  if(namePrefix == "/acceo"){
						window.location.href="{{url('/acceo/allscrutiny')}}";  
					  }

                    }
                }
            });
        //}
    });
</script>
<!--**********FORM VALIDATION ENDS*************-->


<script type="text/javascript">
    // file upload date
    $(document).on('click', '.signedfile', function () {
        var fileData = document.getElementById("signedfileupload");
        if (fileData.files.length == 0) {
            $('#myModalFINAL').modal('show');
           // $(".uploadmessage").text("Upload signed Candidate Scrutiny Report");
        } else {
            var name = document.getElementById("signedfileupload").files[0].name;
            var form_data = new FormData();
            var ext = name.split('.').pop().toLowerCase();
             var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById("signedfileupload").files[0]);
            var f = document.getElementById("signedfileupload").files[0];
            var fsize = f.size || f.fileSize;

            if (jQuery.inArray(ext, ['pdf']) == -1)
            {
                alert("Invalid File");
                $('#signedfileupload').val("");
                $('#signedfileupload').focus();
                return false;
            }
           
           else if (fsize > 2000000)
            {
                alert("File Size is very big");
                $('#signedfileupload').val("");
                 $('#signedfileupload').focus();
                 return false;
            } else
            {
                form_data.append("signedfileupload", document.getElementById('signedfileupload').files[0]);
                $.ajax({
                    url: "{{url('/acdeo/uploadsigned')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        //$('#uploaded_image').html("<label class='text-success'>Image Uploading...</label>");
                    },
                    success: function (data)
                    {
                        //alert(data);
                         $('#myModalFINAL').modal('show');

                    }
                });
            }

        }

//                  
    });
 
 $(document).on('change', '#final_action', function() { 
	 //ECI Level Finalize Not Show on Closed,Disqualified,Case Dropped Date 18-06-2019
        if($('#final_action').val() == 'Closed' || $('#final_action').val() == 'Disqualified' || $('#final_action').val() == 'Case Dropped') {
           $('.comfirmReport').css('display','none'); 
        } else {
			 $('.comfirmReport').css('display','-webkit-inline-box'); 
        } 
   });

</script>

 
<style type="text/css">
    #form_new_mis{
        padding-bottom: 25px;
    }
    input[type="date"], input[type="time"], input[type="datetime-local"], input[type="month"] {
        -webkit-appearance: listbox;
    }
    .uploadmessage{
        color: red;
        font-weight: 600;
        font-size: 16px; 
    }
</style>
@endsection