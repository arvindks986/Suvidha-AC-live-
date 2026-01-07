@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate scrutiny form Details')
@section('bradcome', 'Scrutiny Details')
@section('description', '')
@section('content') 
<?php
$baseurl=url('/acdeo');
$st = getstatebystatecode($user_data->st_code);
$distname = getdistrictbydistrictno($user_data->st_code, $user_data->dist_no);
$namePrefix = \Route::current()->action['prefix'];
$acName= getacbydist($user_data->st_code, $user_data->dist_no);
  

//$filelocation =url('/uploads/ExpenditureReportAC');
$urlloader=url('/admintheme/images/loader.gif');
?>
<style>
    form#defectData {
        width: 100%;
    }
    .container {
        width: 100% !important;
        max-width: 100%;
    } 
    #saveunderstated
    {
        /* width: 148px;
         padding: 8px;
         font-size: 19px;
        */
    }
    button#finalized {

    }

    button.deleteExpRecord{
        background-color: #f0587e;
        border: none;
        color: #fff;
        padding: 10px 15px 10px 15px;
        border-radius: 3px;
    }
    button.deleteRecord {
        background-color: #f0587e;
        border: none;
        color: #fff;
        padding: 10px 15px 10px 15px;
        border-radius: 3px;
    }
    /*  table#fundParty td input{
          width: 168px;
      }   */
    input#political_fund_checque_date {
        float: left;
        width: 200px !important;
        margin-left: 12px;
        padding: 9px;
    }
    input#political_fund_checque {
        width: 168px;
        float: left;
    }
    .showmessage{
        color: green;
        font-weight: 600;
        font-size: 16px;
    }
    .showmessageaccount{
        color: green;
        font-weight: 600;
        font-size: 16px;
    }
    .showmessagedefect{
        color: green;
        font-weight: 600;
        font-size: 16px;
    }
    .showmessageunderstated{
        color: green;
        font-weight: 600;
        font-size: 16px; 
    }
    .showmessagepoliticalparty{
        color: green;
        font-weight: 600;
        font-size: 16px; 
    }
    .showmessagepoliticalpartyerror{
        color: red;
        font-weight: 600;
        font-size: 16px; 
    }
    #moreThan7{
        color: red;
        font-weight: 600;
        font-size: 16px; 
    }
    #moreThan8{
        color: red;
        font-weight: 600;
        font-size: 16px; 
    }  
    #error1, #error2, #error3, #error4{
        color: red;
        font-weight: 600;
        font-size: 16px; 
    }

    .showmessageothersource{
        color: green;
        font-weight: 600;
        font-size: 16px; 
    }
    .sourcefundmsg{
        color: green;
        font-weight: 600;
        font-size: 16px;
        float:right;
    }
    .partyfundmsg{
        color: green;
        font-weight: 600;
        font-size: 16px;
    }
    .col-sm-10.text-center {
        margin-top: 30px;
    }
    .expmsgunder{color: green;
                 font-weight: 600;}

    #UpdateSourceFund{
    }
    button#UpdatePartyFund {
        /* width: 148px;
        padding: 8px;
        font-size: 19px;
        */
    }
    button#SaveExpense {        
    }
    input#noticefile {
   margin: 10px 7px 10px 0px;
}
#revisedaccountmessage{
        color: red;
        font-weight: 600;
        font-size: 16px; 
    }
    #loader-noticefile,#loader-file_commenst3,#loader-file_comment6,#loader-file_comment4{
        display: none;
    }
   
    #loader-noticefile-error, #loader-file_comment3-error,#loader-file_comment6-error,#loader-file_comment4-error{
        color: red;
        font-weight: 600;
        font-size: 16px;
    }
</style>
<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();" style="float: right;margin-right: 91px; margin-top: 7px;">Back</button>
<main role="main" class="inner cover mb-3">
    <section class="mt-5">
        <div class="container-fluid">             
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;"> 
                    <div class="card-body">                 
                        <div class="table-responsive"> 
                            <table class="table pb-5 mb-4 tableShadow">
                                <tbody>
                                    <tr>                                    
                                        <td width="40%" class="bdr-none"><strong class="grayClr">Name :</strong></td>
                                        <td class="bdr-none">{{$candidateData->cand_name}}</td>
                                    </tr>
                                    <tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Address of the Candidate :</strong></td>
                                        <td class="bdr-none">{{$candidateData->candidate_residence_address}}</td>
                                    </tr>                                
                                    <tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Political Party Affliation, If Any :</strong></td>
                                        <td class="bdr-none">{{$candidateData->PARTYNAME}} </td>
                                    </tr>
                                    <tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">No. and name of AC:</strong></td>
                                        <td class="bdr-none">{{ !empty($acdetail->AC_NO) ? $acdetail->AC_NO : ''}} &nbsp;&nbsp;{{ !empty($acdetail->AC_NAME) ? $acdetail->AC_NAME : 'N/A'}}</td>
                                    </tr>
                                    <tr>                                    
                                        <td class="bdr-none"> <strong class="grayClr">Name of the Elected Candidate :</strong></td>
                                        <td class="bdr-none">{{!empty($winn_data->lead_cand_name)? $winn_data->lead_cand_name:'N/A'}}</td>
                                    </tr>                               
                                </tbody>
                            </table>
                        </div>         
                        <div class="clearfix"></div> 
                        <section class="tab_order">             
                            <ul class="tabs">
                                <li>
                                    <a href="#tab1" id="ActiveTab1">Account Details</a>
                                </li>
                                <li>
                                    <a href="#tab2" id="ActiveTab2">Defects In Format</a>
                                </li>
                                <li>
                                    <a href="#tab3" id="ActiveTab3">Expense Understated</a>
                                </li>
                                <li>
                                    <a href="#tab4" id="ActiveTab4">Funds Given By Political Party</a>
                                </li>   
                                <li>
                                    <a href="#tab5"  id="ActiveTab5">Other Sources</a>
                                </li>   
                            </ul>    

                            <div class="col-12">
                                <div id="steps">
                                    <ul>
                                        <li><a href="javascript:void(0);"><div class="progress_step active step1 <?php echo!empty($gexExpReport[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($gexExpReport[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($gexExpReport[0]->updated_at)) : "Account Details"; ?>">1</div></a></li>
                                        <li><a href="javascript:void(0);"><div class="progress_step step2  <?php echo!empty($gexExpReport[0]->rp_act) ? "done" : ""; ?>" data-desc="<?php echo!empty($gexExpReport[0]->rp_act) ? "Last saved on " . date('d-m-Y h:i A', strtotime($gexExpReport[0]->updated_at)) : "Defects In Format"; ?>">2</div></a></li>

                                        <li><a href="javascript:void(0);"><div class="progress_step step3  <?php echo!empty($getCandidateExpData[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($getCandidateExpData[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($getCandidateExpData[0]->updated_at)) : "Expense Understated"; ?>">3</div></a></li>
                                        <li><a href="javascript:void(0);"><div class="progress_step step4  <?php echo!empty($expenditure_fund_parties[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($expenditure_fund_parties[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($expenditure_fund_parties[0]->updated_at)) : " Funds Given By Political Party"; ?>">4</div></a></li>
                                        <li><a href="javascript:void(0);"><div class="progress_step step5  <?php echo!empty($getSourceFundData[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($getSourceFundData[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($getSourceFundData[0]->updated_at)) : "Other Sources"; ?>">5</div></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div id="tab1" class="tabContainer">
                                <div class="table-responsive"> 
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Account Details: {{$candidateData->cand_name}}</p>
                                                </td>
                                                @if($candidateData->finalized_status =="0")                                
                                                <td class="bdr-none" width="110">
                                                    <button class="btn btn-primary float-right" id="editaccountaction">Edit Details </button>
                                                </td>
                                                @endif                                  
                                            </tr>
                                        </tbody>
                                    </table>

                                    <input type="hidden" id="finalized_status" name="finalized_status" value="{{$candidateData->finalized_status}}">
                                    <input type="hidden" id="editcandiateid" name="editcandiateid" value="{{$candidateData->candidate_id}}">
                                    <form method="post"   id="accountData" novalidate="novalidate">
                                        <input type="hidden" name="candidate_id" value="{{$candidateData->c_id}}" id="candidate_id">
                                        <input type="hidden" name="constituency_no" value="{{$candidateData->ac_no}}" id="candidate_id">
                                       
                                        <input type="hidden" name="candidate_id_base" value="{{base64_encode($candidateData->c_id)}}" id="candidate_id_base">

                                         <input type="hidden" name="const_id_base" value="{{base64_encode($candidateData->ac_no)}}" id="const_id_base">

                                         
                                          <input type="hidden" name="dist_no" value="{{!empty($AcData->district_no)?$AcData->district_no:0}}" id="dist_no">

                                        {{ csrf_field() }}           
                                        <table class="table table-bordered">                
                                            <thead>
                                                <tr>                                 
                                                    <th width="65">Sr. No</th> 
                                                    <th>Description</th>
                                                    <th width="450">To be Filled up by the DEO</th> 
                                                </tr>
                                            </thead>                            
                                            <tbody> 
                                                <tr>  
                                                    <td><label> 1.</label></td>
                                                    <td><label>Name & address of the candidate</label></td>                                                   
                                                    <td class="bdr-none">{{$candidateData->cand_name}} {{$candidateData->candidate_residence_address}}</td>
                                                </tr>
                                                <tr>   
                                                    <td><label> 2.</label></td>
                                                    <td><label>Political Party affiliation, if any</label></td> 
                                                    <td class="bdr-none">{{$candidateData->PARTYNAME}}</td>
                                                </tr>                                
                                                <tr>    
                                                    <td><label> 3.</label></td>
                                                     <td><label>No. and Name of AC :</label></td> 
                                                    <td class="bdr-none">{{ !empty($acdetail->AC_NO) ? $acdetail->AC_NO : ''}} &nbsp;&nbsp;{{ !empty($acdetail->AC_NAME) ? $acdetail->AC_NAME : 'N/A'}} </td>  
                                                    <input type="hidden" name="constituency_no" id="constituency_no" value="{{$acdetail->AC_NO}}">
                                                </tr>
                                                <tr>   
                                                    <td><label> 4.</label></td>
                                                    <td><label>Name of the elected candidate</label></td>
                                                    <td class="bdr-none">{{!empty($winn_data->lead_cand_name)? $winn_data->lead_cand_name:'N/A'}}</td>
                                                </tr>
                                                <tr>  
                                                    <td><label> 5.</label></td>
                                                    <td><label>Date of declaration of result</label></td>
                                                    
                            <td><select name="date_of_declaration" value="{{!empty($candidateData->date_of_declaration)?$candidateData->date_of_declaration:''}}" 
                            id="date_of_declaration" class="form-control width-130" >
                                               
                          @foreach ($date_of_decl as $date_of_decl)
                
                          <option value= "{{$date_of_decl}}">{{date('d-m-Y',strtotime($date_of_decl))}}</option>                          
                             
                           @endforeach
                            </select> 
                            </td>                                
                                                    <!-- <td class="bdr-none"><input type="date"   name="date_of_declaration" value="{{!empty($candidateData->date_of_declaration)?$candidateData->date_of_declaration:''}}"  id="date_of_declaration" class="form-control width-200"  min="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" 

max="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 2 days ')):''}}"
placeholder="Date &amp; time" ></td> -->
 <!-- <input type="hidden" id="date_of_declaration_value" name ="date_of_declaration" >        -->
                                            </tr>                                                  
                                            <tr>
                                                <td><label> 6.</label></td>
                                                <td><label>Date of Account Reconciliation Meeting</label><span class="redClr font-weight-bold h6">*</span></td>

                                                 <td><input type="date"   name="date_of_account_rec_meetng" value="{{!empty($candidateData->date_of_account_rec_meetng)?$candidateData->date_of_account_rec_meetng:''}}"  id="date_of_account_rec_meetng" min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' +1 days ')):''}}" max="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' +30 days ')):''}}" class="form-control width-200" placeholder="Date &amp; time" required></td>
                                             
                 <!-- <td><select name="date_of_account_rec_meetng" 
                value="{{!empty($candidateData->date_of_account_rec_meetng)?$candidateData->date_of_account_rec_meetng:''}}"  
             
                 id="date_of_account_rec_meetng" class="form-control width-130" >
                                               
                @foreach ($date_of_account_rec_meetng_arr as $date_of_account)
                
             <option value= "{{$date_of_account}}">{{date('d-m-Y',strtotime($date_of_account))}}</option>                          
                             
             @endforeach
                                                    </select>       

</td>
 -->
</tr>
                                            <tr>
                                                <td rowspan="2"><label for=""> 7.</label></td>
                                                <td><label>(i) Whether the candidate or his agent had been informed about the date of Account Reconciliation Meeting in writing</label></td>
                                                <td>
                                                    <select name="reconciliation_meeting_writing" value="{{$candidateData->reconciliation_meeting_writing}}" id="reconciliation_meeting_writing" class="form-control width-100">
                                                        <option value=""  {{ $candidateData->reconciliation_meeting_writing=="" ?  "selected":""}}>Select</option>                          
                                                        <option value="Yes" {{ $candidateData->reconciliation_meeting_writing=="Yes" ? "selected":""}}>Yes</option>
                                                        <option value="No" {{ $candidateData->reconciliation_meeting_writing=="No" ?  "selected":""}}>No</option>

                                                    </select>  
                                                    <textarea name="reconciliation_meeting_writing_comment"  placeholder="Write comment"  class="form-control mt-2" id="reconciliation_meeting_writing_comment" style="display:none;" rows="3">{{$candidateData->reconciliation_meeting_writing_comment}}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>(ii) Whether he or his agent has attended the meeting</label></td>
                                                <td>
                                                    <select name="agent_attend_meeting" value="{{$candidateData->agent_attend_meeting}}" id="agent_attend_meeting" class="form-control width-100">
                                                        <option value="" {{ $candidateData->agent_attend_meeting=="" ? "selected":""}} selected="">Select</option>
                                                        <option value="Yes" {{ $candidateData->agent_attend_meeting=="Yes" ? "selected":""}}>Yes</option>
                                                        <option value="No" {{ $candidateData->agent_attend_meeting=="No" ? "selected":""}}>No</option>
                                                    </select>
                                                    <textarea name="agent_attend_meeting_comment"  placeholder="Write comment" class="form-control mt-2" id="agent_attend_meeting_comment" style="display:none;" rows="3">{{$candidateData->agent_attend_meeting_comment}}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label> 8.</label></td>
                                                <td><label>Whether all the defects reconciled by the candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 19)</label></td>
                                                <td>
                                                    <select name="defect_reconciliation_meeting" value="{{$candidateData->defect_reconciliation_meeting}}" id="defect_reconciliation_meeting" class="form-control width-100">
                                                        <option value="" {{ $candidateData->defect_reconciliation_meeting=="" ? "selected":""}}>Select</option>
                                                        <option value="Yes" {{ $candidateData->defect_reconciliation_meeting=="Yes" ? "selected":""}}>Yes</option>
                                                        <option value="No" {{ $candidateData->defect_reconciliation_meeting=="No" ? "selected":""}}>No</option>
                                                    </select>
                                                    <textarea name="defect_reconciliation_meeting_comment"  placeholder="Write comment" class="form-control mt-2" id="defect_reconciliation_meeting_comment" style="display:none;" rows="3">{{$candidateData->defect_reconciliation_meeting_comment}}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label> 9.</label></td>
                                                <td><label> Last date prescribed for lodging Account <span class="redClr font-weight-bold h6">*</span></label></td>
                                                
                                                <td class="bdr-none">
                                                <input type="date"   name="last_date_prescribed_acct_lodge" value="{{!empty($candidateData->last_date_prescribed_acct_lodge)?$candidateData->last_date_prescribed_acct_lodge:''}}"  id="last_date_prescribed_acct_lodge" class="form-control width-200" min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 1 days ')):''}}" max="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 90 days ')):''}}" placeholder="Date &amp; time" >
                                                </td>
 
                                                 
                                            </tr>
                                            
                                            <tr>
                                                <td><label for=""> 10.</label></td>
                                                <td><label for=""> Whether the candidate has lodged the Account <span class="redClr font-weight-bold h6">*</span></label></td>
                                                <td>
                                                    <select name="candidate_lodged_acct" value="{{$candidateData->candidate_lodged_acct}}" id="candidate_lodged_acct" class="form-control width-100">
                                                        <!--<option value="" {{ $candidateData->candidate_lodged_acct=="" ? "selected":""}} >Select</option>-->
                                                        <option value="Yes" {{ $candidateData->candidate_lodged_acct=="Yes" ? "selected":""}}>Yes</option>
                                                        <option value="No" {{ $candidateData->candidate_lodged_acct=="No" || $candidateData->candidate_lodged_acct=="" ? "selected":""}}  >No</option>
                                                    </select>
                                                    <textarea name="candidate_lodged_acct_comment"  placeholder="Write comment" class="form-control mt-2" id="candidate_lodged_acct_comment" style="display:none;" rows="3">{{$candidateData->candidate_lodged_acct_comment}}</textarea>

                                                </td>
                                            </tr>
                                            <tr class="yeslodge">
                                                <td rowspan="3"><label for=""> 11.</label></td>
                                                <td><label for=""> If the candidate has lodged the account, date of lodging of account by the candidate:</label></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr class="yeslodge">
                                                <td><label for="">(i) original account</label></td>
                                                <td>
                                                    <input type="date" min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 1 days ')):''}}" value="{{$candidateData->date_orginal_acct}}"  name="date_orginal_acct" id="date_orginal_acct" class="form-control width-200" placeholder="Date &amp; time">
                                                </td>
                                            </tr>
                                            <tr class="yeslodge">
                                                <td><label for="">(ii) revised account after the Account Reconciliation Meeting</label></td>
                                                <td><br /><input type="date" min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 1 days ')):''}}" max="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 45 days ')):''}}" value="{{$candidateData->date_revised_acct}}"  name="date_revised_acct" id="date_revised_acct" class="form-control width-200" placeholder="Date &amp; time">
                                                    <span id="revisedaccountmessage"></span>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td><label for=""> 12.</label></td>
                                                <td><label for=""> Whether account lodged in time <span class="redClr font-weight-bold h6">*</span></label></td>
                                                <td>
                                                    <!-- <input type="hidden" name='account_lodged_time' id="account_lodged_time_set" value="{{$candidateData->account_lodged_time}}"> -->
                                                   <select   id="account_lodged_time" name='account_lodged_time'class="form-control width-100"  value="{{$candidateData->account_lodged_time}}">
                                                        
                                                        <option value="Yes" {{ $candidateData->account_lodged_time=="" ||  $candidateData->account_lodged_time=="Yes" ? "selected":""}}>Yes</option>
                                                        <option value="No"  {{ $candidateData->account_lodged_time=="No" ? "selected":""}}>No</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="isshownot_lodged_period_delay" style="display:none;">
                                                <td><label for=""> 12A.</label></td>
                                                <td><label for=""> If not lodged in time, period of delay</label></td>
                                                <td>

                                                    <input type="number"  pattern="[0-9]+" minlength="1" maxlength="3"  value="{{$candidateData->not_lodged_period_delay}}"  name="not_lodged_period_delay" id="not_lodged_period_delay" placeholder="N/A" class="form-control width-80" >
                                                    <label class="mt-2 ml-2">In&nbsp;Days</label>
                                                    <span id="moreThan8"></span>
                                                </td>




                                            </tr>
                                           
                                            <tr>
                                                <td><label for=""> 13.</label></td>
                                                <td><label for="" class="mr-3"> If account not lodged or not lodged in time, whether DEO called for explanation from the candidate.
                                                        If not, reason thereof.<span class="redClr font-weight-bold h6">*</span></label></td>
                                                <td>
                                                    <select name="reason_lodged_not_lodged" value="{{$candidateData->reason_lodged_not_lodged}}" id="reason_lodged_not_lodged" class="form-control width-100">
                                                        <option value="" {{ $candidateData->reason_lodged_not_lodged=="" ? "selected":""}} >Select</option>
                                                        <option value="Yes" {{ $candidateData->reason_lodged_not_lodged=="Yes" ? "selected":""}}>Yes</option>
                                                        <option value="No" {{ $candidateData->reason_lodged_not_lodged=="No" ? "selected":""}}>No</option>
                                                        <option value="N/A" {{ $candidateData->reason_lodged_not_lodged=="N/A" ? "selected":""}}>N/A</option>
                                                    </select>
                                                    <textarea name="reason_lodged_not_lodged_comment"  placeholder="Write comment" class="form-control mt-2" id="reason_lodged_not_lodged_comment" style="display:none;" rows="3">{{$candidateData->reason_lodged_not_lodged_comment}}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for=""> 14.</label></td>
                                                <td><label for=""> Explanation, if any, given by the candidate</label></td>
                                                <td><textarea placeholder="Write comment" name="explaination_by_candidate" id="explaination_by_candidate" class="form-control">{{$candidateData->explaination_by_candidate}}</textarea></td>
                                                    <!--<input type="text" value="{{$candidateData->explaination_by_candidate}}" name="explaination_by_candidate" id="explaination_by_candidate"  placeholder="N/A" class="form-control"></td>-->
                                            </tr>
                                            <tr>
                                                <td><label for=""> 14A.</label></td>
                                                <td><label for=""> Comments of the DEO on the explanation if any, of the candidate</label></td>
                                                <td><textarea name="comment_by_deo" placeholder="Write comment" class="form-control" id="comment_by_deo">{{$candidateData->comment_by_deo}}</textarea>
                                                    
                                                    <!--<input type="text" name="comment_by_deo" value="{{$candidateData->comment_by_deo}}" id="comment_by_deo" placeholder="N/A" class="form-control">-->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for=""> 15.</label></td>
                                                <td><label for=""> Grand Total of all election expenses reported by the candidate in Part-II of the Abstract Statement</label></td>
                                                <td>
                                                    <div class="d-flex">
                                                        <input type="number" maxlength="7" minlength="0" pattern="[0-9]+" value="{{$candidateData->grand_total_election_exp_by_cadidate}}" name="grand_total_election_exp_by_cadidate" id="grand_total_election_exp_by_cadidate" placeholder="Rs.00000.00" class="form-control width-150">
                                                        <label class="mt-2 ml-2">In&nbsp;Rupees</label>                                                          

                                                    </div>
                                                    <span id="moreThan7"></span>
                                                </td>
                                            </tr>                                
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-center" align="center">                         
                                                        <input type="button" value="Save & Continue" id="saveAccountData" class="btn btn-primary btn-lg">

                                                    </td>
                                                </tr>
                                            </tfoot>            
                                        </table>                            
                                    </form><!-- form tab1 close -->
                                </div>
                            </div><!-- tab1 -->

                            <div id="tab2" class="tabContainer">        
                                <div class="col-12">
                                    <span class="showmessageaccount"></span>
                                    <!--            <div  class="alert alert-success alert-dismissible " id="">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <span class="showmessageaccount"></span>
                                                  </div>-->
                                </div>   
                                <div class="table-responsive">
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Defects In Format of {{$candidateData->cand_name}}</p>
                                                </td> 
                                                @if($candidateData->finalized_status =="0")
                                                <td class="bdr-none"  width="110">
                                                    <button class="btn btn-primary float-right" id="editdefectaction">Edit Details </button>
                                                </td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>

                                    <form method="post" id="defectData" novalidate="novalidate">
                                        <table class="table table-striped table-bordered" style="width:100%"> 
                                            <input type="hidden" name="candidate_id" value="{{$candidateData->c_id}}" id="candidate_id">
                                            <input type="hidden" name="constituency_no" id="constituency_no" value="{{$candidateData->ac_no}}" >
                                            <input type="hidden" name="dist_no" value="{{!empty($AcData->district_no)?$AcData->district_no:0}}" id="dist_no">

                                            {{ csrf_field() }}          
                                            <thead>
                                                <tr>                                 
                                                    <th width="65">Sr. No</th> 
                                                    <th>Description</th>
                                                    <th width="450">To be Filled up by the DEO</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><label> 16.</label></td>
                                                    <td><label> Whether in the DEO's opinion, the account of election expenses of the candidate has been lodged in the manner required by the R.P. Act 1951 and C.E. Rules, 1961<span class="redClr font-weight-bold h6">*</span></label></td>
                                                    <td><select   name="rp_act" id="rp_act" class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="Yes" {{ $candidateData->rp_act == 'Yes' ? 'selected="selected"' : '' }} >Yes</option>
                                                            <option value="No" {{ $candidateData->rp_act == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                            <option value="N/A" {{ $candidateData->rp_act=="N/A" ? "selected":""}}>N/A</option>
                                                        </select>                                                             
                                                    </td>
                                                </tr>
                                                <tr class="is17th">
                                                    <td rowspan="6"><label for=""> 17.</label></td>
                                                    <td><label for=""> If No, then please mention the following defects with details</label></td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr class="is17th">
                                                    <td><label for=""> (i) Whether Election Expenditure Register comprising of the Day to Day Account Register,
                                                            <br />Cash Register, Bank Register, Abstract Statement has been lodged</label></td>
                                                    <td>
                                                        <select name="comprising" value="{{$candidateData->comprising}}" id="comprising" class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="Yes" {{ $candidateData->comprising == 'Yes' ? 'selected="selected"' : '' }}>Yes</option>
                                                            <option value="No" {{ $candidateData->comprising == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                        </select>
                                                        <textarea name="comprising_comment" placeholder="Write comment" class="form-control mt-2" id="comprising_comment" rows="3">{{$candidateData->comprising_comment}}</textarea>
                                                    </td>
                                                </tr>
                                                <tr class="is17th">
                                                    <td><label for=""> (ii) Whether duly sworn in affidavit has been submitted by the candidate</label></td>
                                                    <td>
                                                        <select name="duly_sworn" id="duly_sworn" value="{{$candidateData->duly_sworn}}" class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="Yes" {{ $candidateData->duly_sworn == 'Yes' ? 'selected="selected"' : '' }}>Yes</option>
                                                            <option value="No" {{ $candidateData->duly_sworn == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                        </select>
                                                        <textarea name="duly_sworn_comment" placeholder="Write comment" class="form-control mt-2" id="duly_sworn_comment" rows="3">{{$candidateData->duly_sworn_comment}}</textarea>
                                                    </td>
                                                </tr>
                                                <tr class="is17th">
                                                    <td><label for=""> (iii) Whether requisite vouchers in respect of items of election expenditure submitted</label></td>
                                                    <td>
                                                        <select name="Vouchers" id="Vouchers" value="{{$candidateData->Vouchers}}" class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="Yes" {{ $candidateData->Vouchers == 'Yes' ? 'selected="selected"' : '' }}>Yes</option>
                                                            <option value="No" {{ $candidateData->Vouchers == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                        </select>
                                                        <textarea name="Vouchers_comment"  id="Vouchers_comment"  class="form-control mt-2" id="Vouchers_comment" rows="3">{{$candidateData->Vouchers_comment}}</textarea>
                                                    </td>
                                                </tr>
                                                <tr class="is17th">
                                                    <td><label> (iv) Whether separate Bank Account opened for election</label></td>
                                                    <td><select name="seprate" id="seprate" value="{{$candidateData->seprate}}" class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="Yes" {{ $candidateData->seprate == 'Yes' ? 'selected="selected"' : '' }}>Yes</option>
                                                            <option value="No" {{ $candidateData->seprate == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                        </select>
                                                        <textarea name="seprate_comment"  placeholder="Write comment"  placeholder="Write comment" class="form-control mt-2" id="seprate_comment" rows="3">{{$candidateData->seprate_comment}}</textarea>
                                                    </td>
                                                </tr>
                                                <tr class="is17th">
                                                    <td><label> (v) Whether all expenditure (except petty expenditure) routed through bank account</label></td>
                                                    <td><select name="routed" id="routed" value="{{$candidateData->routed}}" class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="Yes" {{ $candidateData->routed == 'Yes' ? 'selected="selected"' : '' }}>Yes</option>
                                                            <option value="No" {{ $candidateData->routed == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                        </select>
                                                        <textarea  name="routed_comment"  placeholder="Write comment" class="form-control mt-2" id="routed_comment" rows="3">{{$candidateData->routed_comment}}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td rowspan="3"><label> 18.</label></td>
                                                    <td><label> (i) Whether the DEO had issued a notice to the candidate for rectifying the defect</label></td>
                                                    <td style="" class="text-center">
                                                        <select name="rectifying" id="rectifying" value="{{$candidateData->rectifying}}" class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="Yes" {{ $candidateData->rectifying == 'Yes' ? 'selected="selected"' : '' }}>Yes</option>
                                                            <option value="No" {{ $candidateData->rectifying == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                            <option value="N/A" {{ $candidateData->rectifying=="N/A" ? "selected":""}}>N/A</option>
                                                        </select>
                                                        <textarea  name="rectifying_comment"   placeholder="Write comment"  class="form-control mt-2" id="rectifying_comment" rows="3">{{$candidateData->rectifying_comment}}</textarea>
                                                        <span   id="loader-noticefile-error"></span>
                                                        <img style="width: 106px;" id="loader-noticefile" src="{{$urlloader}}" alt="" />
                                                        <input style="margin-right: 130px!important;" type="file" accept="application/pdf" name="noticefile" placeholder="Only Pdf" class="notice" id="noticefile" ><span class="notice" style=" margin-top: -34px;float: right;">Note:Only Pdf</span>
                                                          @if($candidateData->rectifying =="Yes" && !empty($download_link3))
                                                                <br/>
                                                                      <a href="{{$download_link3}}"  target="_blank">Download</a>
                                                                     <br/>
                                                               @endif
                                                               
                                                        <input type="date" min="{{ !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'])):''}}" value="{{!empty($candidateData->notice_date)?$candidateData->notice_date:''}}" name="notice_date" class="notice form-control width-200">

                                                    </td> 
                                                </tr>
                                                <tr>
                                                    <td><label> (ii) Whether the candidate rectified the defect</label></td>
                                                    <td><select name="rectified" id="rectified" value="{{$candidateData->rectified}}"  class="form-control width-100">
                                                            <option value="" selected="">Select</option>
                                                            <option value="No" {{ $candidateData->rectified == 'No' ? 'selected="selected"' : '' }}>No</option>
                                                            <option value="Yes" {{ $candidateData->rectified == 'Yes' ? 'selected="selected"' : '' }}>Yes</option>
                                                             <option value="N/A" {{ $candidateData->rectified=="N/A" ? "selected":""}}>N/A</option>
                                                        </select>
                                                        <textarea  name="rectified_comment"  placeholder="Write comment"  class="form-control mt-2" id="rectified_comment" rows="3">{{$candidateData->rectified_comment}}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><label for=""> (iii) Comments of the DEO on the above, i.e. whether the defect was rectified or not.</td>
                                                    <td><textarea name="comment_of_deo"  placeholder="Write comment"  placeholder="N/A" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2">{{$candidateData->comment_of_deo}}</textarea></td>
                                                </tr>
                                            </tbody>                                
                                            <tfoot>
                                                <tr>
                                                    <td colspan=3 class="text-center" align="center">                                           
                                                        <input type="button" value="Back" id="backdefect" class="btn btn-primary btn-lg">
                                                        <input type="button" value="Skip" id="skipdefect" class="btn btn-primary btn-lg">
                                                        <input type="button" value="Save & Continue" id="saveDefectData" class="btn btn-primary btn-lg">                                    
                                                    </td>                                       
                                                </tr>
                                            </tfoot>                           
                                        </table>
                                    </form>          
                                </div>      
                            </div><!-- tab2 -->
                            <div id="tab3" class="tabContainer">
                                <div class="col-12">
                                    <span class="showmessagedefect"></span>
                                    <!--              <div  class="alert alert-success alert-dismissible ">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <span class="showmessagedefect"></span>
                                                  </div>-->
                                </div>
                                <div class="table-responsive">
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Expense Understated of {{$candidateData->cand_name}}</p>
                                                </td>
                                                @if($candidateData->finalized_status =="0")                 
                                                <td class="bdr-none"  width="110">
                                                    <button class="btn btn-primary float-right" id="editunderstatedaction">Edit Details </button>
                                                </td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>
                                    <form method="post" action="#" id="understatedData" >
                                        {{ csrf_field() }}
                                        <input type="hidden" name="1[understated][status]" value="" id="status1">
                                        <input type="hidden" name="candidate_id" value="{{$candidateData->c_id}}" id="candidate_id">
                                        <input type="hidden" name="constituency_no" value="{{$candidateData->ac_no}}" id="constituency_no">
                                       <input type="hidden" name="district_no" value="{{!empty($AcData->district_no)?$AcData->district_no:0}}" id="district_no">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="65">S. No.</th>
                                                    <th>Description</th>   
                                                    <th width="450">To be filled up by the DEO</th>      
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>19.</td>
                                                    <td><label>Whether the items of election expenses reported by the candidate correspond with the expenses shown in the Shadow Observation Register and Folder of Evidence.<br>
                                                            If no then mention the following.</label></td>    
                                                    <td>    
                                                        <select class="form-control width-150" name="1[understated][status]" id="exampleFormControlSelect1">
                                                            <option value="" selected="">Select</option>
                                                            <option value="yes" <?php
                                                            if (!empty($getCandidateExpData[0]) && $getCandidateExpData[0]->status == "yes") {
                                                                echo "selected";
                                                            }
                                                            ?>>Yes</option>
                                                            <option value="no" <?php
                                                            if (!empty($getCandidateExpData[0]) && $getCandidateExpData[0]->status == "no") {
                                                                echo "selected";
                                                            }
                                                            ?>>No</option>

                                                            <option value="N/A" <?php
                                                            if (!empty($getCandidateExpData[0]) && $getCandidateExpData[0]->status == "N/A") {
                                                                echo "selected";
                                                            }
                                                            ?>>N/A</option>                                
                                                        </select>                            
                                                    </td>      
                                                </tr>
                                                <tr class="hide_tr">
                                                    <td colspan="3">
                                                        <table class="table" width="100%" CELLPADDING="0" id="tblEntAttributes">
                                                            <thead>
                                                                <tr>
                                                                    <th width="50">S.No.:</th>
                                                                    <th width="150">Items of expenditure</th>
                                                                    <th width="130">Date</th>   
                                                                    <th width="100">Page No. of Shadow Observation Register</th>
                                                                    <th width="130">Mention amount as per the Shadow Observation Register/folder of evidence</th>
                                                                    <th width="130">Amount as per the account submitted by the candidate</th>   
                                                                    <th width="130">Amount understated by the candidate</th>
                                                                    <th width="140">Description</th>
                                                                     <th width="105">Action</th>      
                                                                </tr>
                                                            </thead>
                                                            <tbody >
                                                              
                                                               @if(empty($getExpData[0]->expenditure_type))

                                                               <!--
                                                                 <table id="dynamic_field">
                                                               
                                                                
                                                               <tr>
                                                                    <td>1.</td>
                                                                    <td>
                                                                        <input type="text"  name="datas[expenditure_type][]" id="expenditure_type" class="form-control" value="{{!empty($getExpData[0]->expenditure_type)?$getExpData[0]->expenditure_type:''}} " >
                                                                       
                                                                    </td>                           
                                                                    <td style=""><input type="date"  name="datas[date_understated][]" id="date_understated" class="form-control"  placeholder="Enter date"  max="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" value="{{!empty($getExpData[0]->date_understated)?$getExpData[0]->date_understated:''}}"

                                                                        ></td>
                                                                    <td><input type="number" name="datas[page_no_observation][]" id="page_no_observation" class="form-control"  placeholder="" pattern="\d*" maxlength="4"
                                                                    value="{{!empty($getExpData[0]->page_no_observation)?$getExpData[0]->page_no_observation:''}}"
                                                                        >
                                                                        <span id="error1"></span></td>
                                                                    <td><input type="number" name="datas[amt_as_per_observation][]" id="amt_as_per_observation" class="form-control asperobservation" min="1" max="7" placeholder="" pattern="\d*" maxlength="7"
                                                                     value="{{!empty($getExpData[0]->amt_as_per_observation)?$getExpData[0]->amt_as_per_observation:''}}"
                                                                        >
                                                                        <span id="error2"></span>
                                                                    </td>
                                                                    <td><input type="number" name="datas[amt_as_per_candidate][]" id="amt_as_per_candidate" class="form-control aspercand"  placeholder="" pattern="\d*" maxlength="7"
                                                                        value="{{!empty($getExpData[0]->amt_as_per_candidate)?$getExpData[0]->amt_as_per_candidate:''}}"
                                                                        >
                                                                        <span id="error3"></span></td>
                                                                    <td><input type="number" name="datas[amt_understated_by_candidate][]" id="amt_understated_by_candidate" class="form-control understatedamt"  placeholder="" pattern="\d*" maxlength="7" readonly="readonly"
                                                                        value="{{!empty($getExpData[0]->amt_understated_by_candidate)?$getExpData[0]->amt_understated_by_candidate:''}}"
                                                                        >
                                                                        <span id="error4"></span>
                                                                    </td>
                                                                    <td><textarea  placeholder="Write description"  name="datas[description][]" id="description" placeholder="" class="form-control" id="exampleFormControlTextarea1" rows="3"> {{!empty($getExpData[0]->description)?$getExpData[0]->description:''}}
                                                                    </textarea>
                                                                    </td>

                                                                      <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>    
                                                                </tr>
                                                            </table>
                                                -->  
                                          @endif
                                        
                                                <table id="hello">
                                                     <?php $i=1; 

                                                        $countis=array();
                                                     ?>
                                                  @if($candidateData->finalized_status =="0")   
                                                    <button type="button" name="add" id="add_edit" class="btn btn-success" style="float:right">Add More </button>
                                                    @endif
                                                    
                                                  
                                                     
                                                   @foreach($getExpData as $getExp)
                                                   @if(!empty($getExp->expenditure_type)) 

                                                    @php $countis[]= $getExp->expenditure_type; @endphp
                                             
                                                   <tr>
        
                                                    
              
                                                                    <td>{{$i++}}</td>
                                                                    <td>
                                                                        <input type="text"  name="datas[expenditure_type][]" id="expenditure_type" required class="form-control" value="{{!empty($getExp->expenditure_type)?$getExp->expenditure_type:''}} " >
                                                                      
                                                                    </td>   
                                                                    <td style=""><input type="date"  name="datas[date_understated][]" id="date_understated" class="form-control"  placeholder="Enter date"  max="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" value="{{!empty($getExp->date_understated)?$getExp->date_understated:''}}"

                                                                        ></td>
                                                                    <td><input type="number" name="datas[page_no_observation][]" id="page_no_observation" class="form-control"  placeholder="" pattern="\d*" maxlength="4"
                                                                    value="{{!empty($getExp->page_no_observation)?$getExp->page_no_observation:''}}"
                                                                        >
                                                                        <span id="error1"></span></td>
                                                                    <td><input type="number" name="datas[amt_as_per_observation][]" id="amt_as_per_observation" class="form-control asperobservation" min="1" max="7" placeholder="" pattern="\d*" maxlength="7"
                                                                     value="{{!empty($getExp->amt_as_per_observation)?$getExp->amt_as_per_observation:''}}"
                                                                        >
                                                                        <span id="error2"></span>
                                                                    </td>
                                                    <td><input type="number" name="datas[amt_as_per_candidate][]" id="amt_as_per_candidate" class="form-control aspercand"  placeholder="" pattern="\d*" maxlength="7"value="{{!empty($getExp->amt_as_per_candidate)?$getExp->amt_as_per_candidate:''}}">
                                                                        <span id="error3"></span></td>

                                                                    <td><input type="number" name="datas[amt_understated_by_candidate][]" id="amt_understated_by_candidate" class="form-control understatedamt"  placeholder="" pattern="\d*" maxlength="7" readonly="readonly"
                                                                        value="{{!empty($getExp->amt_understated_by_candidate)?$getExp->amt_understated_by_candidate:''}}">
                                                                        <span id="error4"></span>
                                                                    </td>
                                                                      <td><textarea  placeholder="Write description"  name="datas[description][]" id="description" placeholder="" class="form-control" id="exampleFormControlTextarea1" rows="3"> {{!empty($getExp->description)?$getExp->description:''}}
                                                                    </textarea>
                                                                    </td>
                                                                     <td><button type="button" name="remove" id="{{$getExp->id}}" class="btn btn-danger deleteRecord">X</button></td>                
                                                                   
                                                                </tr>
                                                                @endif
          
        </tr>
    @endforeach


                                                 

                                                </table>


                                                  
                                         
                                        
                                                <table>






                                                              <table>
                                                             <tr>
                                                                   


                                                                <!-- list item end -->
                                                                @php 
                                                                      $countedit=count($countis);
                                                             
                                                                    $amt_as_per_observation=array_sum(array_column($getExpData, 'amt_as_per_observation'));  
                                                                    $amt_as_per_candidate=array_sum(array_column($getExpData, 'amt_as_per_candidate'));
                                                                    $amt_understated_by_candidate=array_sum(array_column($getExpData, 'amt_understated_by_candidate'));
                                                                     
                                                                @endphp 
                                                                <tr>
                                                                      
                                                                     
                                                                    <td colspan="4">Total Amount</td>
                                                                    <td><span class="undertatedamt">Amount As Per Observation</span><br><input type="text" value="{{$amt_as_per_observation}}" class="amt_as_per_observation form-control" readonly="readonly"></td>
                                                                    <td><span class="undertatedamt" readonly="readonly">Amount As Per Candidate</span><input type="text" value="{{$amt_as_per_candidate}}" class="form-control amt_as_per_candidate" readonly="readonly"></td>
                                                                    <td><span class="undertatedamt">Understated Amount</span><input type="text" value="{{$amt_understated_by_candidate}}" class="form-control amt_understated" readonly="readonly"></td>
                                                                    <td colspan="2"></td>
                                                                </tr>
                                                                </tr>      </table>                                                          
                                                            </tbody>
                                                        </table>                            
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-striped table-bordered" style="width:100%">
 
                                            <tr>
                                                <td width="65"> 20.</td>
                                                <td> Did the candidate produce his Register of Accounting Election Expenditure for inspection by the Observer/RO/Authorized persons 3 times during campaign period</td> 
                                                <td width="450">                                 
                                                    <select class="form-control width-100" name="2[understated][status]" id="understated_status">
                                                        <option value="" selected="">Select</option>
                                                        <option value="Yes" <?php
                                                        if (!empty($getCandidateExpData[1]) && $getCandidateExpData[1]->status == "Yes") {
                                                            echo "selected";
                                                        }
                                                        ?>>Yes</option>
                                                        <option value="No" <?php
                                                        if (!empty($getCandidateExpData[1]) && $getCandidateExpData[1]->status == "No") {
                                                            echo "selected";
                                                        }
                                                        ?>>No</option>                  
                                                    </select>  
                                                    <div class="mt-2"></div>                             
                                                    <textarea class="form-control"  placeholder="Write comment" value="<?php echo!empty($getCandidateExpData[1]->comment) ? $getCandidateExpData[1]->comment : ""; ?>"  name="2[understated][comment]" rows="3" id="understated_comment"  <?php if (empty($getCandidateExpData[1]->comment)) { ?> style="display: none;" <?php } ?>><?php echo!empty($getCandidateExpData[1]->comment) ? $getCandidateExpData[1]->comment : ""; ?></textarea>

                                                </td>
                                            </tr>
                                         
                                            
                                             
                                            <tr class="21th" >
                                                <td rowspan="6"> 21.</td>
                                                <td></label>If DEO does not agree with the facts mentioned against Row No. 19 referred to above, give the following details:-</label></td>    
                                                <td></td>      
                                            </tr>
                                            <tr class="21th">                            
                                                <td>(i) Were the defects noticed by the DEO brought to the notice of the candidate during campaign period or during the Account Reconciliation Meeting</td>    
                                                <td>                        
                                                    <select class="form-control width-100" name="3[understated][status]" id="understated_status3">
                                                        <option value="" selected="">Select</option>
                                                        <option value="Yes" <?php
                                                        if (!empty($getCandidateExpData[2]) && $getCandidateExpData[2]->status == "Yes") {
                                                            echo "selected";
                                                        }
                                                        ?>>Yes</option>
                                                        <option value="No" <?php
                                                        if (!empty($getCandidateExpData[2]) && $getCandidateExpData[2]->status == "No") {
                                                            echo "selected";
                                                        }
                                                        ?>>No</option>

                                                         <option value="N/A" <?php
                                                        if (!empty($getCandidateExpData[2]) && $getCandidateExpData[2]->status == "N/A") {
                                                            echo "selected";
                                                        }
                                                        ?>>N/A</option>                                
                                                    </select> 
                                                    <div class="mt-2"></div>
                                                    <textarea  placeholder="Write comment" <?php if (empty($getCandidateExpData[2]->comment)) { ?> style="display: none;" <?php } ?> class="form-control" rows="2" name="3[understated][comment]" id="exampleFormControlTextarea1" value="<?php echo!empty($getCandidateExpData[2]->comment) ? $getCandidateExpData[2]->comment : ""; ?>" ><?php echo!empty($getCandidateExpData[2]->comment) ? $getCandidateExpData[2]->comment : ""; ?></textarea>

                                                </td>      
                                            </tr>
                                            <tr class="21th" > 
                                                <td class="understated_comment3">(ii) If yes, then annex copies of all the notices issued relating to discrepancies with English translation (if it is in regional language) and mention date of notice</td>    
                                                <td class="understated_comment3">
                                                    <span   id="loader-file_commenst3-error"></span>
                                                    <img style="width: 106px;" id="loader-file_commenst3" src="{{$urlloader}}" alt="" />
                                                    <input type="file" name="4[understated][comment]" accept="application/pdf" class="fsorm-control" id="file_comment3"><span class="id="file_comment3"  style="float: right;">Note:Only Pdf</span>
                                                    <input type="hidden" name="4[understated][comment]" class="fsorm-control" id="file_commenst3">
                                                    <br/> <br/>
                                                       @if(!empty($getCandidateExpData[2]->status) && $getCandidateExpData[2]->status =="Yes" && !empty($download_link1)) <br/>
                                                <a href="{{$download_link1}}"  target="_blank">Download</a>
                                            @endif
                                                    <input type="date" name="4[understated][extra_data]" 
                                                     value="{{!empty($getCandidateExpData[3]->extra_data) ? $getCandidateExpData[3]->extra_data : "" }}" class="form-control width-200 fsorm-control" max="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}">
                                                </td>   
                                            </tr>
                                            <tr class="21th">                            
                                                <td>(iii) Did the candidate give any reply to the notice ?</td>  
                                                 
                                                <td>                             
                                                    <select class="form-control width-100" name="5[understated][status]" id="understated_status5">
                                                        <option value="" selected="">Select</option>
                                                        <option value="No" <?php
                                                        if (!empty($getCandidateExpData[4]) && $getCandidateExpData[4]->status == "No") {
                                                            echo "selected";
                                                        }
                                                        ?>>No</option>
                                                        <option value="Yes" <?php
                                                        if (!empty($getCandidateExpData[4]) && $getCandidateExpData[4]->status == "Yes") {
                                                            echo "selected";
                                                        }
                                                        ?>>Yes</option>
                                                        <option value="N/A" <?php
                                                        if (!empty($getCandidateExpData[4]) && $getCandidateExpData[4]->status == "N/A") {
                                                            echo "selected";
                                                        }
                                                        ?>>N/A</option>                                 
                                                    </select> 
                                                    <div class="mt-2"></div>

                                                    <textarea <?php if (empty($getCandidateExpData[4]->comment)) { ?> style="display: none;" <?php } ?>  class="form-control" name="5[understated][comment]" id="understated_status5_comment" rows="3" ><?php echo!empty($getCandidateExpData[4]->comment) ? $getCandidateExpData[4]->comment : ""; ?></textarea>                                 
                                                </td>         
                                            </tr>
                                            <tr class="21th">                            
                                                <td <?php if (empty($getCandidateExpData[5]->comment)) { ?> style="display: none;" <?php } ?>  class="understated_comment5">(iv) If yes, please Annex copies of such explanation received, (with the English translation of the same, if it is in regional language) and mention date of reply</td>    
                                                <td <?php if (empty($getCandidateExpData[5]->comment)) { ?> style="display: none;" <?php } ?> class="understated_comment5">
                        <!--                            <textarea class="form-control" name="6[understated][comment]" id="exampleFormControlTextarea1" rows="2" >
                                                    <?php //echo!empty($getCandidateExpData[5]->comment) ? $getCandidateExpData[5]->comment : ""; ?></textarea>-->
                                                    <span   id="loader-file_comment6-error"></span>
                                                    <img style="width: 106px;" id="loader-file_comment6" src="{{$urlloader}}" alt="" />
                                                    <input type="file" name="6[understated][comment]" accept="application/pdf" class="sform-control" id="file_comment6"><span class="file_comment6" style="float: right;">Note:Only Pdf</span>
                                                          @if(!empty($getCandidateExpData[4]->status) && $getCandidateExpData[4]->status =="Yes" && !empty($download_link2))
                                                <br/>
                                              <a href="{{$download_link2}}"  target="_blank">Download</a>
                                            
                                            @endif
                                                    <input type="hidden" name="6[understated][comment]" class="sform-control" id="filedd_comment6">

                                                </td>      
                                            </tr>
                                            <tr class="21th">                            
                                                <td>(V) DEO's comments/observations on the candidate's explanation</td>    
                                                <td><textarea class="form-control exampleFormControlTextarea1"  placeholder="Write comment" name="7[understated][comment]" id="exampleFormControlTextarea1" rows="2" ><?php echo!empty($getCandidateExpData[6]->comment) ? $getCandidateExpData[6]->comment : ""; ?></textarea></td>      
                                            </tr>
                                            
                                            <tr> 
                                                <td> 22.</td>                           
                                                <td>Whether the DEO agrees that the expenses are correctly reported by the candidate.<br>(Should be similar to Column no. 8 of Summary Reports of DEO)<span class="redClr font-weight-bold h6">*</span></td>    
                                                <td>
                                                    <select class="form-control width-100" name="8[understated][status]" id="understated_status8">
                                                        <option value="" selected="">Select</option>
                                                        <option value="Yes" <?php
                                                        if (!empty($getCandidateExpData[7]) && $getCandidateExpData[7]->status == "Yes") {
                                                            echo "selected";
                                                        }
                                                        ?>>Yes</option>
                                                        <option value="No" <?php
                                                        if (!empty($getCandidateExpData[7]) && $getCandidateExpData[7]->status == "No") {
                                                            echo "selected";
                                                        }
                                                        ?>>No</option>  
                                                        <option value="N/A" <?php
                                                        if (!empty($getCandidateExpData[7]) && $getCandidateExpData[7]->status == "N/A") {
                                                            echo "selected";
                                                        }
                                                        ?>>N/A</option>                               
                                                    </select> 
                                                    <div class="mt-2"></div>
                                                    <textarea <?php if (empty($getCandidateExpData[7]->comment)) { ?> style="display: none;" <?php } ?>  class="form-control" name="8[understated][comment]" id="understated_comment8" rows="3" ><?php echo!empty($getCandidateExpData[7]->comment) ? $getCandidateExpData[7]->comment : ""; ?></textarea>
                                                </td>      
                                            </tr>


                                            <tr>
                                                <td> 23.</td>
                                                <td><label>Comments, If any, by the Expenditure Observer*-</label></td>
                                                <td><textarea   placeholder="Write comment" class="form-control" name="9[understated][comment]" id="exampleFormControlTextarea1" palceholder="N/A" rows="3" ><?php echo!empty($getCandidateExpData[8]->comment) ? $getCandidateExpData[8]->comment : ""; ?></textarea><br/>
                                                    <span   id="loader-file_comment4-error"></span>
                                                <img style="width: 106px;" id="loader-file_comment4" src="{{$urlloader}}" alt="" /> 
                                                <input type="file" name="9[understated][extra_data]" accept="application/pdf" class="fsorm-control" id="file_comment4"><span   style="float: right;">Note:Only Pdf</span>
                                                    @if(!empty($download_link4))
                                            <br/>
                                                <a href="{{$download_link4}}"  target="_blank">Download</a>
                                                    <br/>
                                            @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan='3'></td>                           
                                            </tr>
                                            <tr>
                                                <td colspan='3'>
                                                    <p>* If the Expenditure Observer has some more facts that have not been covered in the DEO's report, he may annex separate note to that effect.</p>
                                                    <p>** The DEO scrutiny report is to be compiled by the CEO and forwarded to the Commission.</p>
                                                    <p> If the CEO feels like given additional comments, he or she may forward the comments separately.</p>
                                                </td>                           
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan=3 class="text-center" align="center">
                                                        <label for="">&nbsp;</label>
                                                        <div>
                                                            <input type="button" value="Back" id="backunderstated" class="btn btn-primary btn-lg">
                                                            <input type="button" value="Skip" id="skipunderstated" class="btn btn-primary btn-lg">
                                                            <input type="button" id="saveunderstated" value="Save & Continue" class="btn btn-primary btn-lg"></div>
                                                    </td>
                                                </tr>

                                                <tr>

                                                </tr>
                                            </tfoot>
                                        </table>
                                    </form>
                                </div>
                            </div><!-- tab3 -->

                            <div id="tab4" class="tabContainer">
                                <div class="col-12">
                                    <span class="showmessageunderstated"></span>
                                    <!--            <div  class="alert alert-success alert-dismissible " id="">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <span class="showmessageunderstated"></span>
                                                  </div>-->
                                </div> 
                                <div class="table-responsive">
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Fund Given by Political Party of {{$candidateData->cand_name}}</p>
                                                </td>
                                                @if($candidateData->finalized_status =="0")
                                                <td class="bdr-none"  width="110">
                                                    <button class="btn btn-primary float-right" id="editpoliticalaction">Edit Details </button>
                                                </td>
                                                @endif                      
                                            </tr>
                                        </tbody>
                                    </table>
                                    <form method="post"  id="UpdatePartyFundData" >
                                        {{ csrf_field() }}
                                        <input type="hidden" name="candidate_id" value="{{$candidateData->c_id}}" id="candidate_id">
                                        <input type="hidden" name="district_no" value="{{!empty($AcData->district_no)?$AcData->district_no:0}}" id="district_no">


                                        <input type="hidden" name="constituency_no" id="constituency_no" value="{{$acdetail->AC_NO}}">

                                        <table id="fundParty" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th colspan="7" class="text-center" color="#ffffff">Fund Given By Political Party</th>
                                                <tr>    
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td width="190"><label>By Cash</label></td>
                                                    <td colspan="6"><input type="text" name="political_fund_cash" placeholder="0.00" id="political_fund_cash" placeholder="0" class="form-control  width-200 overallsum_political" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_cash) ? $expenditure_fund_parties[0]->political_fund_cash : 0; ?>" pattern="\d*" ></td>
                                                </tr>
                                                <tr>
                                                    <td width="190"><label>By Cheque/DD/RTGS/NEFT/OTHER ELECTRONIC MEDIUM</label></td>
                                                    <td width="120">
                                                        <input type="text" name="political_fund_checque"  id="political_fund_checque" placeholder="0.00" class="form-control overallsum_political" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_checque) ? $expenditure_fund_parties[0]->political_fund_checque : 0; ?>" pattern="\d*" > 
                                                    </td>
                                                    <td>
                                                        <input type="date"  max="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}"" name="political_fund_checque_date" id="political_fund_checque_date" placeholder="0" class="form-control" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_checque_date) ? $expenditure_fund_parties[0]->political_fund_checque_date : ""; ?>" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="political_fund_bank_name" id="political_fund_bank_name"  class="form-control" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_bank_name) ? $expenditure_fund_parties[0]->political_fund_bank_name : ""; ?>" placeholder="Bank Name" pattern="[a-zA-Z0-9\s]+" maxlength="100">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="political_fund_acct_no" id="political_fund_acct_no"  class="form-control" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_acct_no) ? $expenditure_fund_parties[0]->political_fund_acct_no : ""; ?>" placeholder="Account Number" pattern="[0-9]" maxlength="16">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="political_fund_ifsc" id="political_fund_ifsc"  class="form-control" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_ifsc) ? $expenditure_fund_parties[0]->political_fund_ifsc : ""; ?>" placeholder="IFSC code" pattern="[a-zA-Z0-9\s]+" maxlength="15">
                                                    </td>
                                                    <td>    
                                                        <input type="text" name="political_fund_checque_num" id="political_fund_checque_num"   class="form-control" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_checque_num) ? $expenditure_fund_parties[0]->political_fund_checque_num : ""; ?>" placeholder="cheque number" pattern="[a-zA-Z0-9\s]+" maxlength="20">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="190"><label>In Kind</label></td>
                                                    <td>
                                                        <input type="text" name="political_fund_kind" id="political_fund_kind" placeholder="0.00" class="form-control width-200 overallsum_political" value="<?php echo!empty($expenditure_fund_parties[0]->political_fund_kind) ? $expenditure_fund_parties[0]->political_fund_kind : "0"; ?>" pattern="\d*" >
                                                    </td>

                                                    <td colspan="5">
                                                        <textarea name="political_fund_kind_text" id="political_fund_kind_text" placeholder="About kind items" class="form-control mt-2 " maxlength="200"><?php echo!empty($expenditure_fund_parties[0]->political_fund_kind_text) ? $expenditure_fund_parties[0]->political_fund_kind_text : ""; ?></textarea>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <?php 
                                                    $political_fund_cash=!empty($expenditure_fund_parties[0]->political_fund_cash)? $expenditure_fund_parties[0]->political_fund_cash:0;
                                                    $political_fund_kind=!empty($expenditure_fund_parties[0]->political_fund_kind)? $expenditure_fund_parties[0]->political_fund_kind:0;
                                                    $political_fund_checque=  !empty($expenditure_fund_parties[0]->political_fund_checque) ? $expenditure_fund_parties[0]->political_fund_checque : 0;
                                                    ?>
                                                    <td width="190"><label>Lump Sum Amount Given by Political Party</label></td>
                                                    <td colspan="6">
                                                        <input type="text" class="form-control width-200" value="{{$political_fund_cash+$political_fund_kind+$political_fund_checque}}" name="overallsum_source_political"  readonly="" id="overallsum_source_political"  > 
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="7" align="center">  
                                                        <input type="button" value="Back" id="backparty" class="btn btn-primary btn-lg">
                                                        <input type="button" value="Skip" id="skipparty" class="btn btn-primary btn-lg">
                                                        <button type="button" class="btn btn-primary btn-lg" id="UpdatePartyFund">Save & Continue </button>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form> 
                                </div>
                            </div><!-- tab4 -->

                            <div id="tab5" class="tabContainer">
                                <div class="col-12">
                                    <span class="showmessagepoliticalparty"></span>
                                    <span class="showmessagepoliticalpartyerror"></span>


                                    <!--            <div  class="alert alert-success alert-dismissible " id="">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <span class="showmessagepoliticalparty"></span>
                                                  </div>-->
                                </div>
                                <div class="table-responsive">
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Fund Given By Other Sources of {{$candidateData->cand_name}}</p>
                                                </td>
                                                @if($candidateData->finalized_status =="0")
                                                <td class="bdr-none"  width="110">
                                                    <button class="btn btn-primary float-right" id="editfundaction">Edit Details </button>
                                                </td>
                                                @endif                      
                                            </tr>
                                        </tbody>
                                    </table> 
                                    <form method="post"  id="UpdateSourceFundData" >
                                        {{ csrf_field() }}
                                        <input type="hidden" name="candidate_id" value="{{$candidateData->c_id}}" id="candidate_id">
                                        <input type="hidden" name="district_no" value="{{!empty($AcData->district_no)?$AcData->district_no:0}}" id="district_no">
                                       <input type="hidden" name="constituency_no" id="constituency_no" value="{{$acdetail->AC_NO}}">

                                        <table class="table table-bordered">
                                            <thead>                                                
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Mode of Payment</th>
                                                    <th>Amount</th>                                    
                                                </tr>    
                                            </thead>
                                            <tbody>  
                                            @php
                                             $other_souce_name_cash='';
                                             $other_source_payment_mode_cash='Cash';
                                             $other_source_amount_cash=0;

                                             $other_souce_name_cheque='';
                                             $other_source_payment_mode_cheque='Cheque';
                                             $other_source_amount_cheque=0;

                                             $other_souce_name_kind='';
                                             $other_source_payment_mode_kind='In Kind';
                                             $other_source_amount_kind=0;
                                             if(!empty($getSourceFundData) && count($getSourceFundData)){
                                             foreach($getSourceFundData as $item){
                                             	if($item->other_source_payment_mode=='Cash'){
													 $other_souce_name_cash=$item->other_souce_name;
		                                             $other_source_payment_mode_cash=$item->other_source_payment_mode;
		                                             $other_source_amount_cash=$item->other_source_amount;
                                                }
                                                if($item->other_source_payment_mode=='Cheque'){
                                                    $other_souce_name_cheque=$item->other_souce_name;
                                                    $other_source_payment_mode_cheque=$item->other_source_payment_mode;
                                                    $other_source_amount_cheque=$item->other_source_amount;

                                                }
                                                if($item->other_source_payment_mode=='In Kind'){
                                                    $other_souce_name_kind=$item->other_souce_name;
                                                    $other_source_payment_mode_kind=$item->other_source_payment_mode;
                                                    $other_source_amount_kind=$item->other_source_amount;
                                                }
                                              }
                                          }
                                              

                                        @endphp                                      
                                                <tr>
                                                    <td><input type="text" name="other_souce_name_cash" placeholder="Source name" id="other_souce_name_cash" 
                                                        value="{{$other_souce_name_cash}}" class="form-control" maxlength="200" ></td>
                                                    <td>
                                                        <input type="hidden" 
                                                        name="other_source_payment_mode_cash"  id="other_source_payment_mode_cash" 
                                                        value="{{ $other_source_payment_mode_cash}}"
                                                         class="form-control"   >
                                                        Cash
                                                    </td>
                                                    <td><input name="other_source_amount_cash"  id="other_source_amount_cash" placeholder="amount" min="0" max="9999999"   value="{{$other_source_amount_cash}}"   type="text" class="form-control overallsum_source"   pattern="\d*" ></td>
                                                </tr>
                                                 <tr>
                                                    <td>                                                    
                                                        <input type="text" name="other_souce_name_cheque"  placeholder="Source name" class="form-control" maxlength="200" value="{{ $other_souce_name_cheque}}"></td>
                                                        <td> <input type="hidden" 
                                                        name="other_source_payment_mode_cheque"     value="{{$other_source_payment_mode_cheque}}"
                                                         class="form-control" maxlength="30" >
                                                        Cheque/DD/RTGS/NEFT/OTHER ELECTRONIC MEDIUM 
                                                    </td>
                                                    <td><input name="other_source_amount_cheque" id="other_source_amount_cheque"  placeholder="amount" min="0" max="9999999"  " value="{{$other_source_amount_cheque}}"     type="text" class="form-control overallsum_source" placholder="0" pattern="\d*"  ></td>
                                                 <tr>
                                                    <td>                                                     
                                                        <input type="text" name="other_souce_name_kind" placeholder="Source name" value="{{$other_souce_name_kind}}" class="form-control" maxlength="200" ></td>
                                                        <td> <input type="hidden" 
                                                        name="other_source_payment_mode_kind"  id="other_souce_name" value="{{$other_source_payment_mode_kind}}" 
                                                         class="form-control" maxlength="30" >
                                                       In Kind
                                                    </td>
                                                    <td><input name="other_source_amount_kind"  placeholder="amount" min="0" max="9999999" id="other_source_amount" value="{{$other_source_amount_kind}}"     type="text" class="form-control overallsum_source" placholder="0" pattern="\d*"  ></td>
                                                </tr>  
                                                 @php
                                                        $cashamt=!empty($getSourceFundData[0]->other_source_amount)?$getSourceFundData[0]->other_source_amount:0;

                                                         $cheque=!empty($getSourceFundData[1]->other_source_amount)?$getSourceFundData[1]->other_source_amount:0;

                                                         $kind=!empty($getSourceFundData[2]->other_source_amount)?$getSourceFundData[2]->other_source_amount:0;

                                                         $overall_amount_source=$cashamt+$cheque+$kind;

                                                 @endphp
                                                <tr>
                                                    <td>Lump Sum Amount Given By Other Sources</td>
                                                    <td><input type="text" class="form-control width-200" name="overallsum_source_amount" id="overallsum_source_amount" value="{{$overall_amount_source}}" readonly=""/></td>
                                                </tr>
                                                 <tr>
                                                    <td>Lump Sum Amount Given by Political Party</td>
                                                    <td><input type="text" class="form-control width-200" name="overallsum_source_political_grand" id="overallsum_source_political_grand" value=" {{$political_fund_cash+$political_fund_kind+$political_fund_checque}}" readonly=""/></td>
                                                </tr>
                                                 <tr>
                                                    <td>Lump Sum Grand Total</td>
                                                    <td><input type="text" class="form-control width-200" name="grand_total_political_source" id="grand_total_political_source"  value=" {{$political_fund_cash+$political_fund_kind+$political_fund_checque+$overall_amount_source}}" readonly=""/></td>
                                                </tr>
                                                
                                         
                                                  <tr><td colspan="5"> 
                                            <center>
                                                <input type="button" value="Back" id="backothersource" class="btn btn-primary btn-lg">
                                                <button type="button" class="btn btn-primary btn-lg" id="UpdateSourceFund" >Save</button> <input type="hidden" name="candidate_id" value="{{$candidateData->c_id}}" id="candidate_id">
                                                <button type="button" class="btn btn-primary btn-lg" id="finalized" >Save & Finalize </button></center> </center><span style="float: right;" class="redClr font-weight-bold h6">Note: if finalized you will not be edit/modify in the future</span></center> </td></tr>
                                            </tbody>    
                                        </table> 
                                    </form>

                                </div>
                            </div><!-- tab5 -->


                        </section>      
                        <!-- section close -->

                        <!-- for form start here-->
                        <!-- Nav tabs -->

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal -->
    <div class="modal fade" id="myModalSucc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel" style="text-align: -webkit-center;">Continue to add more scrutiny reports for candidates.</h6>
                </div>
                <div class="modal-footer mb-2">

                    <input type="button" minlength="3" value="No" ids="" constt="" id="getlist" class="btn btn-default btncl mt-2">
                    <input type="button" value="Yes" id="addmore"  class="btn btn-primary btncl mt-2" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModalErr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">Kindly make sure, do you want to finalize all entries?</h6>
                </div>
                <div class="modal-footer mb-2">
                    <input type="button" value="Ok" id="fianlform" class="btn btn-primary mt-2">
                    <input type="button" value="Cancel" id="" class="btn btn-default mt-2" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>
    <!-- end defectform -->

</main>
@endsection

@section('script')
<style>
    .isactivesuccess{
        display: none;
    }
    .isactiveerror{
        display: none;
    }

</style>
<script type="text/javascript">
    // for back button
    $("#backdefect").click(function () {
        $("#ActiveTab1")[0].click();
    });
    $("#backunderstated").click(function () {
        $("#ActiveTab2")[0].click();
    });
    $("#backparty").click(function () {
        $("#ActiveTab3")[0].click();
    });
    $("#backothersource").click(function () {
        $("#ActiveTab4")[0].click();
    });


    // back button end here 
</script>
<script type="text/javascript">
    $(document).on('click', '#addmore', function (e) {
       
        
        window.location.href = "{{url('/acdeo/scrutinyExpenditure')}}";
    });

    $(document).on('click', '#getlist', function (e) {
        var candidate_id = $(this).attr('ids'); 
         var const_id = $(this).attr('constt'); 
         //alert(const_id);
        

        window.location.href = "{{url('/acdeo/editExpenditureReport?candidate_id=')}}"+candidate_id +'&ac_no='+const_id;
    });



</script>
<script type="text/javascript">
    // edit action for account start here
    $(document).ready(function () {
        var finalized_status = $("#finalized_status").val();//
        var candidate_id = $("#editcandiateid").val();
        if (finalized_status == "1") {
            $("#accountData :input").prop("disabled", true);
            $("#defectData :input").prop("disabled", true);
            $("#understatedData :input").prop("disabled", true);
            $("#UpdatePartyFundData :input").prop("disabled", true);
            $("#UpdateSourceFundData :input").prop("disabled", true);
        }
        if (finalized_status == "0") {
            $("#accountData :input").prop("disabled", true);
            $("#defectData :input").prop("disabled", true);
            $("#understatedData :input").prop("disabled", true);
            $("#UpdatePartyFundData :input").prop("disabled", true);
            $("#UpdateSourceFundData :input").prop("disabled", true);
        }


    });



    $(document).on('click', "#editaccountaction", function () {
        $("#accountData :input").prop("disabled", false);

    })
    $(document).on('click', "#editdefectaction", function () {
        $("#defectData :input").prop("disabled", false);

    })
    $(document).on('click', "#editdefectaction", function () {
        $("#defectData :input").prop("disabled", false);

    })
    $(document).on('click', "#editunderstatedaction", function () {
        $("#understatedData :input").prop("disabled", false);

    })
    $(document).on('click', "#editpoliticalaction", function () {
        $("#UpdatePartyFundData :input").prop("disabled", false);

    })
    $(document).on('click', "#editfundaction", function () {
        $("#UpdateSourceFundData :input").prop("disabled", false);

    })


    // edit action for account end here
</script>
<script>
    $(document).ready(function () {
        $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
            var $el = $(this);
            $el.toggleClass('active-dropdown');
            var $parent = $(this).offsetParent(".dropdown-menu");
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');

            $(this).parent("li").toggleClass('show');

            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                $('.dropdown-menu .show').removeClass("show");
                $el.removeClass('active-dropdown');
            });

            if (!$parent.parent().hasClass('navbar-nav')) {
                $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
            }

            return false;
        });
    });
    jQuery('ul.tabs').each(function () {
        var $active, $content, $links = jQuery(this).find('a');
        $active = jQuery($links.filter('[href="' + location.hash + '"]')[0] || $links[0]);
        $active.addClass('active');
        $content = jQuery($active[0].hash);
        $links.not($active).each(function () {
            jQuery(this.hash).hide();
        });
        jQuery(this).on('click', 'a', function (e) {
            $active.removeClass('active');
            $content.hide();
            $active = jQuery(this);
            $content = jQuery(this.hash);
            $active.addClass('active');
            $content.show();
            e.preventDefault();
        });
    });

    var checkUnderStated = "<?php echo!empty($getCandidateExpData[0]->status) ? $getCandidateExpData[0]->status : ""; ?>";

    jQuery(document).ready(function () {
        $('.hide_tr').hide();
        if (checkUnderStated == "no") {
            $(".hide_tr").show();
        }
        if (checkUnderStated == "") {
            $(".hide_tr").hide();
        }
        //Check Validation
        jQuery('#constiuancyinfo').click(function () {
            var pcname = jQuery('select[name="pc"]').val();
            if (pcname == '') {
                jQuery('.errormsg').html('');
                jQuery('.pcerrormsg').html('Please select pc');
                jQuery("input[name='pc']").focus();
                return false;
            }
        });
    });
//    $('.datepicker').datepicker({
//        format: 'mm/dd/yyyy',
//        startDate: '-3d'
//    })

    $('#exampleFormControlSelect1').on('change', function () {
        var check_value = this.value;
        if (check_value == "no")
        {
            $('#status1').val('no');
            $(".hide_tr").show();
        }
        if (check_value == "yes")
        {
            $('#status1').val('yes');
            $(".hide_tr").hide();
        }
    });
// added by manoj
 var exampleFormControlSelect1 = $("#exampleFormControlSelect1").val();
 var understated_status = $("#understated_status").val();
  if(exampleFormControlSelect1=="yes" && understated_status=="Yes")
         {
             $('.21th').css('display','none');
         }
         else{
             $('.21th').css('display','');
         }
$('#exampleFormControlSelect1,#understated_status').on('change', function () {
         var exampleFormControlSelect1 = $("#exampleFormControlSelect1").val();
         var understated_status = $("#understated_status").val();
         if(exampleFormControlSelect1=="yes" && understated_status=="Yes")
         {
             $('.21th').css('display','none');
         }
         else{
             $('.21th').css('display','');
         }
         
    });


    //////////////save understated expenses data ////////////////////
    $(document).on('click', '#saveunderstated', function (e) {
        var data = $("#understatedData").serialize();
        
        var exampleFormControlSelect1 = $('#exampleFormControlSelect1').val();
        var understated_status = $('#understated_status').val();
        var understated_comment = $('#understated_comment').val();
        var understated_status5 = $('#understated_status5').val();
        var understated_status3 = $('#understated_status3').val(); 
        //var  understated_status5_comment =  $('#understated_status5_comment').val();
        var understated_status8 = $('#understated_status8').val();
        var understated_comment8 = $('#understated_comment8').val();
        var exampleFormControlTextarea1 = $('.exampleFormControlTextarea1').val();
        
        // validate 
         var date_understated = $('#date_understated').val();
         var page_no_observation = $('#page_no_observation').val();
         var amt_as_per_observation = $('#amt_as_per_observation').val();
         var amt_as_per_candidate = $('#amt_as_per_candidate').val();
         
         if (exampleFormControlSelect1 =="") {
            $('#exampleFormControlSelect1').css('border', "2px solid red");
            $('#exampleFormControlSelect1').focus(); 
        }
        else if (exampleFormControlSelect1 !="" && date_understated !="" && page_no_observation =="" && page_no_observation <= 0) {
             $('#error1').text("Page no. must be greater than 1 & should not blank"); 
             $('#page_no_observation').focus(); 
        }
        else if (exampleFormControlSelect1 !="" && date_understated !="" &&  amt_as_per_observation =="" && amt_as_per_observation <= 0) {
              $('#error2').text("Amount must be greater than 1 & should not blank");
              $('#amt_as_per_observation').focus(); 
        }
        else if (exampleFormControlSelect1 !="" && date_understated !="" && amt_as_per_candidate =="" &&  amt_as_per_candidate <= 0) {
            $('#error3').text("Amount must be greater than 1 & should not blank"); 
            $('#amt_as_per_candidate').focus(); 
        }       
        
       else if (exampleFormControlSelect1 !="" && understated_status === "No" && $.trim(understated_comment) === "") {
            $('#understated_comment').css('border', "2px solid red");
            $('#understated_comment').focus();
        }

 else if (understated_status =="") {
            $('#understated_status').css('border', "2px solid red");
            $('#understated_status').focus(); 
        }
         else if (understated_status == "No" && $.trim(understated_comment) == "") {
            $('#understated_comment').css('border', "2px solid red");
            $('#understated_comment').focus(); 
        }
         else if (exampleFormControlSelect1 !="yes" && understated_status !="Yes" && understated_status3 =="") {
            $('#understated_status3').css('border', "2px solid red");
            $('#understated_status3').focus(); 
        }
         else if (exampleFormControlSelect1 !="yes" && understated_status !="Yes" && understated_status5 =="") {
            $('#understated_status5').css('border', "2px solid red");
            $('#understated_status5').focus(); 
        }

         else if (exampleFormControlSelect1 !="yes" && understated_status !="Yes" && understated_status5 == "No" && $.trim(exampleFormControlTextarea1) == "") {

            $('.exampleFormControlTextarea1').css('border', "2px solid red");
            $('.exampleFormControlTextarea1').focus();
        } else if ($.trim(understated_status8) == "") {
            $('#understated_status8').css('border', "2px solid red");
            $('#understated_status8').focus();
        } 
        else if ($.trim(understated_status8) == "No" && $.trim(understated_comment8) === "") {
            $('#understated_comment8').css('border', "2px solid red");
            $('#understated_comment8').focus();
        }
        
        else {
            $.ajax({
                data: data,
                type: "post",
                url: "{{url('/acdeo/updateUnderstatedDetail')}}",
                success: function (response) {
                    response = response.trim();
                    if (response == 1)
                    {
//alert(response);
                        $('.showmessageunderstated').text("Saved Successfully.");
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth() + 1; //January is 0!

                        var yyyy = today.getFullYear();
                        if (dd < 10) {
                            dd = '0' + dd;
                        }
                        if (mm < 10) {
                            mm = '0' + mm;
                        }

                        var dt = new Date();
                        var hours = dt.getHours() > 12 ? dt.getHours() - 12 : dt.getHours();
                        var minutes = dt.getMinutes() < 10 ? "0" + dt.getMinutes() : dt.getMinutes();

                        var time = dt.getHours() + ":" + dt.getMinutes();
                        var today = dd + '-' + mm + '-' + yyyy + " " + hours + ':' + minutes;

                        $('.showmessageaccount').text(response.message);

                        $('.step3').removeClass('active');
                        $('.step3').addClass('done');
                        $('.step4').addClass('active');
                        $('.step3').attr("data-desc", "Last saved on " + today);
                        // move top
                        $('html,body').animate({scrollTop: 0}, 3000);
                        // for next page start
                        $("#ActiveTab4")[0].click();
                        // $("#add").hide();

                        // end next page

                    }
                    if (response == 0)
                    {

                        $('.showmessageunderstated').text("Error in updating.");
                    }



                }
            });
        }

    });

    ////////////////// end /////////////////
</script>
<!--manoj-->
<script type="text/javascript">
 var status = $("#understated_status5").val();
            
        if (status == "Yes") {
         $(".understated_comment5").css("display", "");            
        } else {
            $(".understated_comment5").css("display", "none");
        }
    $(document).on('change', '#understated_status5', function (e) {
        var status = $("#understated_status5").val();
       if (status == "Yes") {
         $(".understated_comment5").css("display", "");            
        } else {
            $(".understated_comment5").css("display", "none");
        }
    });

    $(document).on('change', '#understated_status3', function (e) {
        var status = $("#understated_status3").val();
        //alert(status);    
        if (status == "No") {
            //console.log(status);
            $(".understated_comment3").css("display", "none");
        } else {
            $(".understated_comment3").css("display", "");
        }
    });

    $(document).on('change', '#understated_status8', function (e) {
        var status = $("#understated_status8").val();
        if (status == "Yes" || status == "N/A") {
            // console.log(status);
            $("#understated_comment8").css("display", "none");
        } else {
            $("#understated_comment8").css("display", "block");
        }
    });

    $(document).on('change', '#understated_status', function (e) {
        var status = $("#understated_status").val();
        if (status == "Yes") {
            //console.log(status);
            $("#understated_comment").css("display", "none");
        } else {
            $("#understated_comment").css("display", "block");
        }
    });
    var status = $("#reconciliation_meeting_writing").val();
    if (status == "Yes" || status == "") {
        $("#reconciliation_meeting_writing_comment").css("display", "none");
    } else {
        $("#reconciliation_meeting_writing_comment").css("display", "");
    }
    $(document).on('change', '#reconciliation_meeting_writing', function (e) {
        var status = $("#reconciliation_meeting_writing").val();
        if (status == "Yes") {
            // console.log(status);
            $("#reconciliation_meeting_writing_comment").css("display", "none");
        } else {
            $("#reconciliation_meeting_writing_comment").css("display", "block");
        }
    });
    var status = $("#agent_attend_meeting").val();
    if (status == "Yes" || status == "") {
        $("#agent_attend_meeting_comment").css("display", "none");
    } else {
        $("#agent_attend_meeting_comment").css("display", "");
    }
    $(document).on('change', '#agent_attend_meeting', function (e) {
        var status = $("#agent_attend_meeting").val();
        if (status == "Yes") {
            // console.log(status);
            $("#agent_attend_meeting_comment").css("display", "none");
        } else {
            $("#agent_attend_meeting_comment").css("display", "block");
        }
    });
    var status = $("#defect_reconciliation_meeting").val();
    if (status == "Yes" || status == "") {
        $("#defect_reconciliation_meeting_comment").css("display", "none");
    } else {
        $("#defect_reconciliation_meeting_comment").css("display", "");
    }
    $(document).on('change', '#defect_reconciliation_meeting', function (e) {
        var status = $("#defect_reconciliation_meeting").val();
        if (status == "Yes") {

            $("#defect_reconciliation_meeting_comment").css("display", "none");
        } else {
            $("#defect_reconciliation_meeting_comment").css("display", "block");
        }
    });
    var status = $("#candidate_lodged_acct").val();
      //$("#account_lodged_time").prop("disabled", true);
    if (status == "Yes" || status == "") {
      //  $("#account_lodged_time").attr('disabled','disabled');
        $("#not_lodged_period_delay").attr('disabled','disabled');
        $(".yeslodge").css("display", "");
        $("#candidate_lodged_acct_comment").css("display", "none");
    }     
    else {
        $("#candidate_lodged_acct_comment").css("display", "");
        $(".yeslodge").css("display", "none");
        $("#account_lodged_time").val('');
    }
    // add new 
    if (status == "No") {
        $("#account_lodged_time").val('');
    }
    
    $(document).on('change', '#candidate_lodged_acct', function (e) {
        var status = $("#candidate_lodged_acct").val();
        if (status == "Yes") {
           // $("#account_lodged_time").attr('disabled','disabled');
            $("#not_lodged_period_delay").attr('disabled','disabled');
            $(".yeslodge").css("display", "");
            $("#candidate_lodged_acct_comment").css("display", "none");
        }
        
        else {
            $("#candidate_lodged_acct_comment").css("display", "block");
            $(".yeslodge").css("display", "none");
             $("#account_lodged_time").val('');
        }
    });
    var status = $("#account_lodged_time").val();
    if (status == "No") {

        $("#isshownot_lodged_period_delay").css("display", "");
    } else {
        $("#isshownot_lodged_period_delay").css("display", "none");
    }
    $(document).on('change', '#account_lodged_time', function (e) {
        var status = $("#account_lodged_time").val();

        if (status == "Yes") {
            $("#isshownot_lodged_period_delay").css("display", "none");
        } else {
            $("#isshownot_lodged_period_delay").css("display", "");

        }
    });
      var status = $("#reason_lodged_not_lodged").val();
    if (status == "No") {
        $("#reason_lodged_not_lodged_comment").css("display", "");
    } else {
        $("#reason_lodged_not_lodged_comment").css("display", "none");
    }
    $(document).on('change', '#reason_lodged_not_lodged', function (e) {
        var status = $("#reason_lodged_not_lodged").val();
        if (status == "Yes" || status=="" ) {
            $("#reason_lodged_not_lodged_comment").css("display", "none");
        }
        else if(status == "N/A"){
            $("#reason_lodged_not_lodged_comment").css("display", "none");
        }
        else {
            $("#reason_lodged_not_lodged_comment").css("display", "block");

        }
    });


    // for defect
    //rp_act
    var status = $("#rp_act").val();
    if (status == "Yes" || status == "") {
        $(".is17th").css("display", "none");
    } else {
        $(".is17th").css("display", "");
    }
    $(document).on('change', '#rp_act', function (e) {
        var status = $("#rp_act").val();
        if (status == "Yes" || status == "" || status == "N/A") {
            $(".is17th").css("display", "none");
        } else {
            $(".is17th").css("display", "");
        }
    });
    //rp_act
    //comprising
    var status = $("#comprising").val();
    if (status == "Yes" || status == "") {
        $("#comprising_comment").css("display", "none");
    } else {
        $("#comprising_comment").css("display", "block");
    }
    $(document).on('change', '#comprising', function (e) {
        var status = $("#comprising").val();
        if (status == "Yes" || status == "") {
            $("#comprising_comment").css("display", "none");
        } else {
            $("#comprising_comment").css("display", "block");
        }
    });
    //comprising

    //duly_sworn
    var status = $("#duly_sworn").val();
    if (status == "Yes" || status == "") {
        $("#duly_sworn_comment").css("display", "none");
    } else {
        $("#duly_sworn_comment").css("display", "block");
    }
    $(document).on('change', '#duly_sworn', function (e) {
        var status = $("#duly_sworn").val();
        if (status == "Yes" || status == "") {
            $("#duly_sworn_comment").css("display", "none");
        } else {
            $("#duly_sworn_comment").css("display", "block");
        }
    });
    //duly_sworn
    //Vouchers_comment
    var status = $("#Vouchers").val();
    if (status == "Yes" || status == "") {
        $("#Vouchers_comment").css("display", "none");
    } else {
        $("#Vouchers_comment").css("display", "block");
    }
    $(document).on('change', '#Vouchers', function (e) {
        var status = $("#Vouchers").val();
        if (status == "Yes" || status == "") {
            $("#Vouchers_comment").css("display", "none");
        } else {
            $("#Vouchers_comment").css("display", "block");
        }
    });
    //Vouchers_comment
    //seprate_comment
    var status = $("#seprate").val();
    if (status == "Yes" || status == "") {
        $("#seprate_comment").css("display", "none");
    } else {
        $("#seprate_comment").css("display", "block");
    }
    $(document).on('change', '#seprate', function (e) {
        var status = $("#seprate").val();
        if (status == "Yes" || status == "") {
            $("#seprate_comment").css("display", "none");
        } else {
            $("#seprate_comment").css("display", "block");
        }
    });
    //seprate_comment
     //rectifying_comment start
    var status = $("#rectifying").val();
    
    if (status == "Yes" ) {          
         $('.notice').css('display','block');
    }
    else{
         $('.notice').css('display','none');
    }
    
    if (status == "Yes" || status == "") {
    $("#rectifying_comment").css("display", "none");
     
    } else {
    $("#rectifying_comment").css("display", "block");
      
    }
     
    $(document).on('change', '#rectifying', function (e) {
    var status = $("#rectifying").val();
    if (status == "Yes") {
      $('.notice').css('display','block');
      $("#rectifying_comment").css("display", "none");
        }
        else if(status == "N/A"  ){
            $('.notice').css('display','none'); 
              $("#rectifying_comment").css("display", "none");
        }
        else{
              $('.notice').css('display','none'); 
              $("#rectifying_comment").css("display", "block");
        }
  
    });
    //rectifying_comment end
    //rectified_comment start
    var status = $("#rectified").val();
    if (status == "Yes" || status == "") {
        $("#rectified_comment").css("display", "none");
    } else {
        $("#rectified_comment").css("display", "block");
    }
    $(document).on('change', '#rectified', function (e) {
        var status = $("#rectified").val();
        if (status == "Yes" || status == "" || status == "N/A") {
            $("#rectified_comment").css("display", "none");
        } else {
            $("#rectified_comment").css("display", "block");
        }
    });
    //rectified_comment end
    $("#grand_total_election_exp_by_cadidate").keyup(function () {
        $('#moreThan7').text("");
    });
    // new change start for orginnal account
     
    $('#date_orginal_acct').change(function(){
       var last_date_prescribed_acct_lodge = $('#last_date_prescribed_acct_lodge').val();
       var date_orginal_acct = $('#date_orginal_acct').val();
       const date1 = new Date(last_date_prescribed_acct_lodge);         
       const date2 = new Date(date_orginal_acct); 
       const diffTime = date2.getTime() - date1.getTime();
       const diffDayss = diffTime >= 0 ? Math.ceil(diffTime) : Math.floor(diffTime);
       const diffDays = Math.ceil(diffDayss / (1000 * 60 * 60 * 24));        
       if(diffDays > 0){ 

              $("#account_lodged_time").val("No");
              //$("#account_lodged_time").attr('disabled','disabled');
              var status= $("#account_lodged_time").val(); 
               //$('#account_lodged_time_set').val('No');
              $("#reason_lodged_not_lodged").val("Yes");           
                    if (status == "No") {     
                          $('#account_lodged_time').attr("disabled", true); 
                        $("#isshownot_lodged_period_delay").css("display", "");
                    } else { 
                        $('#account_lodged_time').attr("disabled", true);
                        $("#isshownot_lodged_period_delay").css("display", "none");
                    }
                    $("#not_lodged_period_delay").val(Math.abs(diffDays));
       } else{
              $("#account_lodged_time").val("Yes");
            //   $('#account_lodged_time_set').val('Yes');
              $("#reason_lodged_not_lodged").val("Yes"); 
              $("#isshownot_lodged_period_delay").css("display", "");
              $("#isshownot_lodged_period_delay").css("display", "");    
              //$("#not_lodged_period_delay").val();

              $("#not_lodged_period_delay").val(5);
       }
    });
  $('#date_revised_acct').change(function(){
      
       var date_orginal_acct = $('#date_orginal_acct').val(); 
       var date_revised_acct = $('#date_revised_acct').val(); 
 
       const date1 = new Date(date_orginal_acct); 
        
       const date2 = new Date(date_revised_acct); 
       const diffTime = date2.getTime() - date1.getTime();
       const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));        
      if(date2.getTime()  >= date1.getTime()){           
               $("#revisedaccountmessage").text('');             
       } else{
             $("#revisedaccountmessage").text("Revised account date should be greater than original account date."); 
       }
    });
    
    
 
       ///============================
    // end new change


</script>
<!--manoj end-->

<<script type="text/javascript">

//////////////save other soucrce fund data ////////////////////

    $(document).ready(function () {
        $("#UpdateSourceFundData").validate({
            rules: {
                other_souce_name: "required",
                other_source_amount: {number: true, required: true},

            },
            messages: {
                other_souce_name: "Please enter name",
                other_source_amount: "Please enter only number",

            }
        })

        $('#UpdateSourceFund').click(function () {
            if ($("#UpdateSourceFundData").valid())
            {
                //////////////save party fund data ////////////////////
                $(document).on('click', '#UpdateSourceFund', function (e) {
                    var data = $("#UpdateSourceFundData").serialize();
                   // $('#UpdateSourceFund').attr("disabled", true);
                    $.ajax({
                        data: data,
                        type: "post",
                        url: "{{url('/acdeo/UpdateSourceFundData')}}",
                        success: function (response) {

                            response = response.trim();
                            $('.showmessagepoliticalpartyerror').text("");

                                $('.showmessagepoliticalparty').text("Saved Successfully.");
                               
                            if (response == 1)
                            {
                                

                                var today = new Date();
                                var dd = today.getDate();
                                var mm = today.getMonth() + 1; //January is 0!

                                var yyyy = today.getFullYear();
                                if (dd < 10) {
                                    dd = '0' + dd;
                                }
                                if (mm < 10) {
                                    mm = '0' + mm;
                                }

                                var dt = new Date();
                                var hours = dt.getHours() > 12 ? dt.getHours() - 12 : dt.getHours();
                                var minutes = dt.getMinutes() < 10 ? "0" + dt.getMinutes() : dt.getMinutes();

                                var time = dt.getHours() + ":" + dt.getMinutes();
                                var today = dd + '-' + mm + '-' + yyyy + " " + hours + ':' + minutes;

                                //$('.showmessageaccount').text(response.message);

                                $('.step5').removeClass('active');
                                $('.step5').addClass('done');
                                // $('.step5').addClass('active');
                                $('.step5').attr("data-desc", "Last saved on " + today);
                                // move top
                                $('html,body').animate({scrollTop: 0}, 3000);


                            }
                            /* if (response == 0)
                            {
                                $('.showmessagepoliticalparty').text("");

                                $('.showmessagepoliticalpartyerror').text("Please Enter Details.");
                            } */



                        }
                    });

                });


////////////////// end /////////////////

            }
        });
    });






////////////////// end /////////////////



///////////////// save expenses data //////////////


    $(document).on('click', '#SaveExpense', function (e) {
        var datas = $("#SaveExpenseData").serialize();
        $.ajax({
            data: datas,
            type: "post",
            url: "{{url('/acdeo/SaveExpenseData/')}}",
            success: function (response) {
                //alert(data);
                // response = response.trim(response);
                if (response == 1)
                {
                    // $("#tblEntAttributes tbody").append(newRowContent);
                    $('.expmsgunder').text("Saved Successfully.");
                    //$('html,body').animate({ scrollTop: 0 }, 3000);
                    // return false;
                }

                if (response == 0)
                {
                    $('.expmsgunder').text("Error in updating.");
                }//


            }
        });

    });
/////////////////// end //////////////////
</script>
<script type="text/javascript">
    function viewCandidate(id) {
        jQuery("#showcandidatelist").css("display", "none");
        jQuery("#showformlist").css("display", "block");
        jQuery.ajax({
            url: "{{url('/')}}/acdeo/viewbyid/" + id,
            type: "get",
            success: function (response) {
                var response = JSON.parse(response);

                $('#candidate_id').val(response.c_id);
                $('#partyid').val(response.PARTYNAME);
                $("#electedCandidatename").val(response.cand_name);
                $("#showdetail").text(response.cand_name + ", " + response.candidate_residence_address);
                $('#showdetailcand').text(response.cand_name);
                $('#account_lodged_time').val(response.account_lodged_time);
                $('#agent_attend_meeting').val(response.agent_attend_meeting);
                $('#candidate_lodged_acct').val(response.candidate_lodged_acct);
                $('#comment_by_deo').val(response.comment_by_deo);
                $('#date_of_account_rec_meetng').val(response.date_of_account_rec_meetng);
                $('#date_of_declaration').val(response.date_of_declaration);
                $('#date_revised_acct').val(response.date_revised_acct);
                $('#defect_reconciliation_meeting').val(response.defect_reconciliation_meeting);
                $('#explaination_by_candidate').val(response.explaination_by_candidate);
                $('#grand_total_election_exp_by_cadidate').val(response.grand_total_election_exp_by_cadidate);
                $('#last_date_prescribed_acct_lodge').val(response.last_date_prescribed_acct_lodge);
                $('#not_lodged_period_delay').val(response.not_lodged_period_delay);
                $('#reason_lodged_not_lodged').val(response.reason_lodged_not_lodged);
                $('#reconciliation_meeting_writing').val(response.reconciliation_meeting_writing);
                $('#grand_total_election_exp_by_cadidate').val(response.grand_total_election_exp_by_cadidate);
            }
        });
    }
</script>
    <script type="text/javascript">
    <!-- for save account Data form start-->
    jQuery(document).on('click', '#saveAccountData', function (e) 
    {
    //alert("sdsdsdsd");
    //reconciliation_meeting_writing
    var data = jQuery("#accountData").serialize();
    var ac_nois=$('#ac_no').val();
    var date_of_declaration = $('#date_of_declaration').val();
    var date_of_account_rec_meetng = $('#date_of_account_rec_meetng').val();

    //
    //reconciliation_meeting_writing
    var reconciliation_meeting_writing = $('#reconciliation_meeting_writing').val();
    var reconciliation_meeting_writing_comment = $('#reconciliation_meeting_writing_comment').val();
    var agent_attend_meeting = $('#agent_attend_meeting').val();
    var agent_attend_meeting_comment = $('#agent_attend_meeting_comment').val();

    var defect_reconciliation_meeting = $('#defect_reconciliation_meeting').val();
    var defect_reconciliation_meeting_comment = $('#defect_reconciliation_meeting_comment').val();


    var candidate_lodged_acct = $('#candidate_lodged_acct').val();
    var account_lodged_time = $('#account_lodged_time').val();
    var not_lodged_period_delay = $('#not_lodged_period_delay').val();
    var reason_lodged_not_lodged = $('#reason_lodged_not_lodged').val();


    var last_date_prescribed_acct_lodge = $('#last_date_prescribed_acct_lodge').val();    


    var grand_total_election_exp_by_cadidate = $('#grand_total_election_exp_by_cadidate').val();
    //var candidate_lodged_acct = $('#candidate_lodged_acct').val();
    var candidate_lodged_acct_comment = $('#candidate_lodged_acct_comment').val();
    // var account_lodged_time = $('#account_lodged_time').val();


     var account_lodged_time = $('#account_lodged_time').val();
     var not_lodged_period_delay = $('#not_lodged_period_delay').val();

// reason
    //  var reason_lodged_not_lodged = $('#reason_lodged_not_lodged').val();
    var reason_lodged_not_lodged_comment = $('#reason_lodged_not_lodged_comment').val();

  if(date_of_declaration== ""){
         $('#date_of_declaration').css('border', "2px solid red");
            $('#date_of_declaration').focus();
    }
    else if (reconciliation_meeting_writing === ""){    
    $('#reconciliation_meeting_writing').css('border', "2px solid red");
            $('#reconciliation_meeting_writing').focus();
    }
    else if (reconciliation_meeting_writing === "No" && $.trim(reconciliation_meeting_writing_comment) === ""){
    $('#reconciliation_meeting_writing_comment').css('border', "2px solid red");
            $('#reconciliation_meeting_writing_comment').focus();
    }
    else if (agent_attend_meeting === ""){
    $('#agent_attend_meeting').css('border', "2px solid red");
            $('#agent_attend_meeting').focus();
    }
    else if (agent_attend_meeting === "No" && $.trim(agent_attend_meeting_comment) === ""){
    $('#agent_attend_meeting_comment').css('border', "2px solid red");
            $('#agent_attend_meeting_comment').focus();
    }
    else if (defect_reconciliation_meeting === ""){
    $('#defect_reconciliation_meeting').css('border', "2px solid red");
            $('#defect_reconciliation_meeting').focus();
    }

    else if (defect_reconciliation_meeting === "No" && $.trim(defect_reconciliation_meeting_comment) === ""){
    $('#defect_reconciliation_meeting_comment').css('border', "2px solid red");
            $('#defect_reconciliation_meeting_comment').focus();
    }
    else if (agent_attend_meeting === "No" && $.trim(agent_attend_meeting_comment) === ""){
    $('#agent_attend_meeting_comment').css('border', "2px solid red");
            $('#agent_attend_meeting_comment').focus();
    }
    else if (defect_reconciliation_meeting === "No" && $.trim(defect_reconciliation_meeting_comment) === ""){
    $('#defect_reconciliation_meeting_comment').css('border', "2px solid red");
            $('#defect_reconciliation_meeting_comment').focus();
    }
    else if (date_of_account_rec_meetng === ""){
    $('#date_of_account_rec_meetng').css('border', "2px solid red");
            $('#date_of_account_rec_meetng').focus();
    }
    else if (last_date_prescribed_acct_lodge === ""){
    $('#last_date_prescribed_acct_lodge').css('border', "2px solid red");
            $('#last_date_prescribed_acct_lodge').focus();
    }
    else if (candidate_lodged_acct === ""){
    $('#candidate_lodged_acct').css('border', "2px solid red");
            $('#candidate_lodged_acct').focus(); }
    else if (candidate_lodged_acct === "No" && $.trim(candidate_lodged_acct_comment) === ""){
    $('#candidate_lodged_acct_comment').css('border', "2px solid red");
            $('#candidate_lodged_acct_comment').focus();
    }
//    else if (account_lodged_time === ""){
//    $('#account_lodged_time').css('border', "2px solid red");
//            $('#account_lodged_time').focus(); }
//    else if (account_lodged_time === "No" && $.trim(not_lodged_period_delay) === ""){
//    $('#not_lodged_period_delay').css('border', "2px solid red");
//            $('#not_lodged_period_delay').focus();
//    }
    // else if (account_lodged_time === "No" && not_lodged_period_delay <= 0){
    // $('#moreThan8').text("Day should  more than 1");
    // }
    else if (reason_lodged_not_lodged === ""){
    $('#reason_lodged_not_lodged').css('border', "2px solid red");
            $('#reason_lodged_not_lodged').focus();
    }
    else if (reason_lodged_not_lodged === "No" && $.trim(reason_lodged_not_lodged_comment) === ""){
    $('#reason_lodged_not_lodged_comment').css('border', "2px solid red");
            $('#reason_lodged_not_lodged_comment').focus();
    }
//    else if (account_lodged_time === ""){
//    $('#account_lodged_time').css('border', "2px solid red");
//            $('#account_lodged_time').focus();
//    }
   /*  else if (grand_total_election_exp_by_cadidate == "" || grand_total_election_exp_by_cadidate == "0"){
    $('#grand_total_election_exp_by_cadidate').css('border', "2px solid red");
            $('#grand_total_election_exp_by_cadidate').focus();
    }
    else if (grand_total_election_exp_by_cadidate <= 0){
    $('#moreThan7').text("Total amount shoud  more than 1");
    } */
    else if (grand_total_election_exp_by_cadidate > 9999999){
    $('#moreThan7').text("Total amount shoud not more than 9999999");
    }
    else{
    $('#date_of_declaration').css('border', "");
            $('#date_of_account_rec_meetng').css('border', "");
            $('#last_date_prescribed_acct_lodge').css('border', "");
            $('#date_orginal_acct').css('border', "");
            $('#date_revised_acct').css('border', "");
            $.ajax({
            data: data,
                    type: "post",
                    dataType: "json",
                    url: "{{url('/acdeo/updateAccountDeoForm')}}",
                    success: function (response) {

                    var today = new Date();
                            var dd = today.getDate();
                            var mm = today.getMonth() + 1; //January is 0!

                            var yyyy = today.getFullYear();
                            if (dd < 10) {
                    dd = '0' + dd;
                    }
                    if (mm < 10) {
                    mm = '0' + mm;
                    }

                    var dt = new Date();
                            var hours = dt.getHours() > 12 ? dt.getHours() - 12 : dt.getHours();
                            var minutes = dt.getMinutes() < 10 ? "0" + dt.getMinutes() : dt.getMinutes();
                            var time = dt.getHours() + ":" + dt.getMinutes();
                            var today = dd + '-' + mm + '-' + yyyy + " " + hours + ':' + minutes;
                            $('.showmessageaccount').text(response.message);
                            $('.step1').removeClass('active');
                            $('.step1').addClass('done');
                            $('.step2').addClass('active');
                            $('.step1').attr("data-desc", "Last saved on " + today);
                            // move top
                            $('html,body').animate({ scrollTop: 0 }, 3000);
                            // for next page start
                            $("#ActiveTab2")[0].click();
                            setTimeout(function() {
                            $('.alert-success').fadeOut('fast');
                            }, 5000);
                            // end next page


                    }
       });
        }// end else
    });

</script>
<!-- end account form-->
<!-- for defect form start-->
        <script type="text/javascript">

            jQuery(document).on('click', '#saveDefectData', function (e) {
            var data = jQuery("#defectData").serialize();
            //comprise
//console.log(data);
            var rp_act = $('#rp_act').val();

            var comprising = $('#comprising').val();
            var comprising_comment = $('#comprising_comment').val();
            var duly_sworn = $('#duly_sworn').val();
            var duly_sworn_comment = $('#duly_sworn_comment').val();
            var Vouchers = $('#Vouchers').val();
            var Vouchers_comment = $('#Vouchers_comment').val();
            var seprate = $('#seprate').val();
            var seprate_comment = $('#seprate_comment').val();
            var routed = $('#routed').val();
            var routed_comment = $('#routed_comment').val();
            var rectifying = $('#rectifying').val();
            var rectifying_comment = $('#rectifying_comment').val();
            var rectified = $('#rectified').val();
            var rectified_comment = $('#rectified_comment').val();
            if (rp_act == "") {
                $('#rp_act').css('border', "2px solid red");
                $('#rp_act').focus();
            } else if (comprising == "" && rp_act == "No") {
                $('#comprising').css('border', "2px solid red");
                $('#comprising').focus();
            } else if (comprising === "No" && $.trim(comprising_comment) === "" && rp_act == "No") {
                $('#comprising_comment').css('border', "2px solid red");
                $('#comprising_comment').focus();
            } else if (duly_sworn == "" && rp_act == "No") {
                $('#duly_sworn').css('border', "2px solid red");
                $('#duly_sworn').focus();
            } else if (duly_sworn === "No" && $.trim(duly_sworn_comment) === "" && rp_act == "No") {
                $('#duly_sworn_comment').css('border', "2px solid red");
                $('#duly_sworn_comment').focus();
            } else if (Vouchers == "" && rp_act == "No") {
                $('#Vouchers').css('border', "2px solid red");
                $('#Vouchers').focus();
            } else if (Vouchers === "No" && $.trim(Vouchers_comment) === "" && rp_act == "No") {
                $('#Vouchers_comment').css('border', "2px solid red");
                $('#Vouchers_comment').focus();
            } else if (seprate == "" && rp_act == "No") {
                $('#seprate').css('border', "2px solid red");
                $('#seprate').focus();
            } else if (seprate === "No" && $.trim(seprate_comment) === "" && rp_act == "No") {
                $('#seprate_comment').css('border', "2px solid red");
                $('#seprate_comment').focus();
            } else if (routed == "" && rp_act == "No") {
                $('#routed').css('border', "2px solid red");
                $('#routed').focus();
            } else if (routed === "No" && $.trim(routed_comment) === "" && rp_act == "No") {
                $('#routed_comment').css('border', "2px solid red");
                $('#routed_comment').focus();
            } else if (rectifying == "") {
                $('#rectifying').css('border', "2px solid red");
                $('#rectifying').focus();
            }
            ////////////////////
            else if (rectifying === "No" && $.trim(rectifying_comment) === "") {
                $('#rectifying_comment').css('border', "2px solid red");
                $('#rectifying_comment').focus();
            } else if (rectified == "") {
                $('#rectified').css('border', "2px solid red");
                $('#rectified').focus();
            } else if (rectified === "No" && $.trim(rectified_comment) === "") {
                $('#rectified_comment').css('border', "2px solid red");
                $('#rectified_comment').focus();
            } else {
                $.ajax({
                    data: data,
                    type: "post",
                    dataType: "json",
                    url: "{{url('/acdeo/updateDefectDeoForm')}}",
                    success: function (response) {

                        $('.showmessagedefect').text(response.message);
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth() + 1; //January is 0!

                        var yyyy = today.getFullYear();
                        if (dd < 10) {
                            dd = '0' + dd;
                        }
                        if (mm < 10) {
                            mm = '0' + mm;
                        }

                        var dt = new Date();
                        var hours = dt.getHours() > 12 ? dt.getHours() - 12 : dt.getHours();
                        var minutes = dt.getMinutes() < 10 ? "0" + dt.getMinutes() : dt.getMinutes();
                        var time = dt.getHours() + ":" + dt.getMinutes();
                        var today = dd + '-' + mm + '-' + yyyy + " " + hours + ':' + minutes;
                        $('.showmessageaccount').text(response.message);
                        $('.step2').removeClass('active');
                        $('.step2').addClass('done');
                        $('.step3').addClass('active');
                        $('.step2').attr("data-desc", "Last saved on " + today);
                        // move top
                        $('html,body').animate({scrollTop: 0}, 3000);
                        // for next page start
                        $("#ActiveTab3")[0].click();
                        setTimeout(function () {
                            $('.alert-success').fadeOut('fast');
                        }, 5000);
                        // end next page
                    }
                });
                }
            }
            );
            $.validator.addMethod('regex', function (value, element, param) {
                return this.optional(element) ||
                        value.match(typeof param == 'string' ? new RegExp(param) : param);
            },
                    'Please enter a value in the correct format.');
            $(document).ready(function () {

                $("#UpdatePartyFundData").validate({
                    rules: {
                        political_fund_cash: {number: true, min: 0, max: 9999999},
                        political_fund_checque: {number: true, min: 0, max: 9999999},
                        political_fund_kind: {number: true, min: 0, max: 9999999},
                        political_fund_bank_name: {maxlength: 100, minlength: 3},
                        political_fund_acct_no: {maxlength: 16, minlength: 8, number: true, regex: /^[0-9]*$/},
                        political_fund_ifsc: {maxlength: 15, minlength: 3, regex: /^[a-zA-Z0-9]*$/},
                        political_fund_checque_num: {minlength: 6, maxlength: 20, regex: /^[a-zA-Z0-9]*$/}
                    },
                    messages: {
                        political_fund_cash: {number: "Please enter only number", max: "Please enter value not more than 9999999", min: "Please enter value not less than 1"},
                        political_fund_checque: {number: "Please enter only number", max: "Please enter value not more than 9999999", min: "Please enter value not less than 1"},
                        political_fund_kind: {number: "Please enter only number", max: "Please enter value not more than 9999999", min: "Please enter value not less than 1"},
                        political_fund_bank_name: {minlength: "Please enter valid bank name", maxlength: "Please enter valid bank name"},
                        political_fund_acct_no: {minlength: "Please enter valid account number", number: "Please enter valid account number"},
                        political_fund_ifsc: {minlength: "Please enter valid ifsc code", maxlength: "Please enter valid ifsc code"},
                        political_fund_checque_num: {minlength: "Please enter valid cheque number", maxlength: "Please enter valid cheque number"}


                    }
                })

                $('#UpdatePartyFund').click(function () {
                    if ($("#UpdatePartyFundData").valid())
                    {
                        //////////////save party fund data ////////////////////
                        $(document).on('click', '#UpdatePartyFund', function (e) {
                            var data = $("#UpdatePartyFundData").serialize();
                            $.ajax({
                                data: data,
                                type: "post",
                                url: "{{url('/acdeo/UpdatePartyFundData')}}",
                                success: function (response) {
                                    response = response.trim();
                                    if (response == 1)
                                    {

                                        $('.showmessagepoliticalparty').text("Saved Successfully.");
                                        var today = new Date();
                                        var dd = today.getDate();
                                        var mm = today.getMonth() + 1; //January is 0!

                                        var yyyy = today.getFullYear();
                                        if (dd < 10) {
                                            dd = '0' + dd;
                                        }
                                        if (mm < 10) {
                                            mm = '0' + mm;
                                        }

                                        var dt = new Date();
                                        var hours = dt.getHours() > 12 ? dt.getHours() - 12 : dt.getHours();
                                        var minutes = dt.getMinutes() < 10 ? "0" + dt.getMinutes() : dt.getMinutes();
                                        var time = dt.getHours() + ":" + dt.getMinutes();
                                        var today = dd + '-' + mm + '-' + yyyy + " " + hours + ':' + minutes;
                                        $('.showmessageaccount').text(response.message);
                                        $('.step4').removeClass('active');
                                        $('.step4').addClass('done');
                                        $('.step5').addClass('active');
                                        $('.step4').attr("data-desc", "Last saved on " + today);
                                        // move top
                                        $('html,body').animate({scrollTop: 0}, 3000);
                                        $("#ActiveTab5")[0].click();
                                    }
                                    if (response == 0)
                                    {

                                        $('.showmessagepoliticalparty').text("Error in updating.");
                                    }

                                }
                            });
                        });
                        ////////////////// end /////////////////

                    }
                });
            });
// for 19 Sno start
  $(document).on('click', '.addNew19', function () {
             var total=$('.clone19').length;
            if(total<=7){                 
                 var $this = $(this)
                  $cloneElement = $this.closest('.clone19'),
                   $clone = $cloneElement.clone();
                $clone.addClass('hide').find('input').val('');
                $cloneElement.after($clone);
                $clone.fadeIn(1000, 'linear', function () {
                    $(this).removeClass('hide');
                });
                $this.html($this.data('text')).addClass($this.data('toggle-class')).removeClass('addNew19'); 
            }else{
                  alert("No more option Available.");
            }
               
                      
            });
          $(document).on('click','.remove-field19',function(){
                 
                $(this).closest('tr').remove();
            });
    // end
          // Add Fields in Fund Given By Political Party//
            $(document).on('click', '.addNew', function () {
             var total=$('.clone').length;
              
            if(total<=2){                 
                 var $this = $(this)
                  $cloneElement = $this.closest('.clone'),
                   $clone = $cloneElement.clone();
                $clone.addClass('hide').find('input').val('');
                $cloneElement.after($clone);
                $clone.fadeIn(1000, 'linear', function () {
                    $(this).removeClass('hide');
                });
                $this.html($this.data('text')).addClass($this.data('toggle-class')).removeClass('addNew'); 
            }else{
                  alert("No more option Available.");
            }
               
                      
            });
            // remove row start 19
            
            $(document).on('click','.remove-field',function(){
                 
                $(this).closest('tr').remove();
            });
            // end 
            ////////////////////delete func soource data/////////
            $(document).on('click', '.deleteRecord', function () {
             
                var delID = $(this).attr('id');
              
                var retrav = confirm("Are you sure want to delete this record?");
                if (retrav)
                {
                    $.ajax({
                        data: {"_token": "{{ csrf_token() }}", delID: delID},
                        type: "post",
                        url: "{{url('/acdeo/DeleteSourceFundData')}}",
                        success: function (data) {
                            response = data.trim();
                            if (response == 1)
                            {
                                //alert("Successfully Deleleted");
                                $('.rem' + delID).remove();
                            }

                            if (response == 0)
                            {
                                alert("Internal Server Error");
                            }
                        window.location.reload(true);

                        }

                    });
                }

            }
            );
            $(document).on('click', '.deleteExpRecord', function () {
                var delID = $(this).attr('id');
                $.ajax({
                    data: {"_token": "{{ csrf_token() }}", delID: delID},
                    type: "post",
                    url: "{{url('/acdeo/DeleteUnderStatedData')}}",
                    success: function (data) {
                        response = data.trim();
                        if (response == 1)
                        {
                            //alert("Successfully Deleleted");
                            $('.remexp' + delID).remove();
                             totalIt()
                totalIt2()
                totalIt3()
                        }

                        if (response == 0)
                        {
                            alert("Internal Server Error");
                        }


                    }
                });
            });
            // $('input.aspercand').change(function(){
//     var $tr = $(this).closest('tr');
//     var price = parseFloat($tr.find('td').eq(4).val());
//     var amt = parseInt($(this).val());
//     $(this).closest('tr').find('input.understatedamt').val(amt * price);
// });

            $(document).on('click', '#finalized', function () {
                 var data = $("#UpdateSourceFundData").serialize();
                            $.ajax({
                                data: data,
                                type: "post",
                                url: "{{url('/acdeo/UpdateSourceFundData')}}",
                                success: function (response) {
                                    response = response.trim();
                                    $('.showmessagepoliticalpartyerror').text("");

                                        $('.showmessagepoliticalparty').text("Saved Successfully.");
                                        $('#UpdateSourceFund').attr("disabled", true);
                                        $('#myModalErr').modal('show');
                                }
                            });
            });
            $(document).on('click', '#fianlform', function () {
                var candidate_id = $('#candidate_id').val();
                var candID = $('#candidate_id_base').val();
                var const_no=$("#const_id_base").val();
            
                $('#myModalErr').modal('hide');

                //  var retVal = confirm("Kindly make sure, do you want to finalized all entries?");
                //   if( retVal == true ) {
                $.ajax({
                    data: {"_token": "{{ csrf_token() }}", candidate_id: candidate_id,const_no: const_no},
                    type: "post",
                    url: "{{url('/acdeo/FinalizedData')}}",
                    success: function (data) {
                        response = data.trim();
                        if (response == 1) {
                            //alert("Finalized");
                            $('#myModalSucc').modal('show');
                            $('#getlist').attr('ids',candID);
                            $('#getlist').attr('constt',const_no);
                        }
                        if (response == 0) {
                            $('#myModalErr').modal('show');
                        }
                    }
                });
                // }
                // else{
                //    return false;
                // }  
            });
            function totalIt() {
                var total = 0;
                $(".asperobservation").each(function () {
                    var val = this.value;
                    total += val == "" || isNaN(val) ? 0 : parseInt(val);
                });
                $(".amt_as_per_observation").val(total);
            }

            function totalIt2() {
                var total = 0;
                $(".aspercand").each(function () {
                    var val = this.value;
                    total += val == "" || isNaN(val) ? 0 : parseInt(val);
                });
                $(".amt_as_per_candidate").val(total);
            }

            function totalIt3() {
                var total = 0;
                $(".understatedamt").each(function () {
                    var val = this.value;
                    total += val == "" || isNaN(val) ? 0 : parseInt(val);
                });
                $(".amt_understated").val(total);
            }

            $(document).on("keyup", ".asperobservation, .aspercand", function () {
                var $row = $(this).closest("tr"),
                        prce = parseInt($row.find('.asperobservation').val()),
                        qnty = parseInt($row.find('.aspercand').val()),
                        subTotal = prce - qnty;
                $row.find('.understatedamt').val(isNaN(subTotal) ? 0 : subTotal);
                totalIt()
                totalIt2()
                totalIt3()
            });
 // for auto calculate start
    
                                                                                          
                                                                                          
        $('.asperobservation, .aspercand').each(function() {
              var $row = $(this).closest("tr"),
                prce = parseInt($row.find('.asperobservation').val()),
                qnty = parseInt($row.find('.aspercand').val()),
                subTotal = prce - qnty;
                $row.find('.understatedamt').val(isNaN(subTotal) ? 0 : subTotal);
                totalIt()
                totalIt2()
                totalIt3()
                                                                                                
                                                                                                
             });

 // end 

//    
//    $("#saveunderstated").click(function(){
//        
//        $("#ActiveTab4")[0].click();
//        setTimeout(function() {
//    $('.alert-success').fadeOut('fast');
//}, 5000); // <-- time in milliseconds       
//        
//    }); 
            $("#UpdatePartyFund").click(function () {

                $("#ActiveTab5")[0].click();
                setTimeout(function () {
                    $('.alert-success').fadeOut('fast');
                }, 5000); // <-- time in milliseconds

            });
            ///////////////for upload file from understated------1 6[understated][comment//////////////
            ///////////////for upload file from understated//////////////
            //$(document).ready(function () {

                // file 1 upload
                $(document).on('change', '#file_comment3', function () {
                    var name = document.getElementById("file_comment3").files[0].name;
                    var form_data = new FormData();
                    var ext = name.split('.').pop().toLowerCase();
                    if (jQuery.inArray(ext, ['pdf']) == -1)
                    {
                        alert("Invalid File");
                         $('#file_comment3').val("");
                    }
                    var oFReader = new FileReader();
                    oFReader.readAsDataURL(document.getElementById("file_comment3").files[0]);
                    var f = document.getElementById("file_comment3").files[0];
                    var fsize = f.size || f.fileSize;
                    if (fsize > 2000000)
                    {
                        alert("File Size is very big");
                         $('#file_comment3').val("");
                    } else
                    {
                        $('#loader-file_commenst3').css('display','block');
                        form_data.append("4[understated][comment]", document.getElementById('file_comment3').files[0]);
                        $.ajax({
                            url: "{{url('/acdeo/update_understated_file1')}}",
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
                                 if(data==1){                                   
                                     $('#loader-file_commenst3').css('display','none');
                                 }else{
                                     $('#loader-file_commenst3').css('display','none');
                                     $('#loader-file_commenst3-error').text('File not uploaded.Something went wrong.Try after sometime.');
                                 }
                            }
                        });
                    }
                });
                // end here file1
                // file 2 upload
                $(document).on('change', '#file_comment6', function () {
                    var name = document.getElementById("file_comment6").files[0].name;
                    var form_data = new FormData();
                    var ext = name.split('.').pop().toLowerCase();
                    if (jQuery.inArray(ext, ['pdf']) == -1)
                    {
                        alert("Invalid File");
                        $('#file_comment6').val("");
                    }
                    var oFReader = new FileReader();
                    oFReader.readAsDataURL(document.getElementById("file_comment6").files[0]);
                    var f = document.getElementById("file_comment6").files[0];
                    var fsize = f.size || f.fileSize;
                    if (fsize > 2000000)
                    {
                        alert("File Size is very big");
                         $('#file_comment6').val("");
                    } else
                    {
                        $('#loader-file_comment6').css('display','block');
                        form_data.append("6[understated][comment]", document.getElementById('file_comment6').files[0]);
                        $.ajax({
                            url: "{{url('/acdeo/update_understated_file2')}}",
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
                                if(data==1){                                   
                                     $('#loader-file_comment6').css('display','none');
                                 }else{
                                     $('#loader-file_comment6').css('display','none');
                                     $('#loader-file_comment6-error').text('File not uploaded.Something went wrong.Try after sometime.');
                                 }

                            }
                        });
                    }
                });
                // end here file2
            
    
                $(document).on('change', '#noticefile', function () {
                    var name = document.getElementById("noticefile").files[0].name;
                    var form_data = new FormData();
                    var ext = name.split('.').pop().toLowerCase();
                    if (jQuery.inArray(ext, ['pdf']) == -1)
                    {
                        alert("Invalid File");
                        $('#noticefile').val("");
                    }
                    var oFReader = new FileReader();
                    oFReader.readAsDataURL(document.getElementById("noticefile").files[0]);
                    var f = document.getElementById("noticefile").files[0];
                    var fsize = f.size || f.fileSize;
                    if (fsize > 2000000)
                    {
                        alert("File Size is very big");
                         $('#noticefile').val("");
                    } else
                    {
                        $('#loader-noticefile').css('display','block');
                        form_data.append("6[understated][comment]", document.getElementById('noticefile').files[0]);
                        $.ajax({
                            url: "{{url('/acdeo/updateNoticeFile')}}",
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
                                if(data==1){                                   
                                     $('#loader-noticefile').css('display','none');
                                 }else{
                                     $('#loader-noticefile').css('display','none');
                                     $('#loader-noticefile-error').text('File not uploaded.Something went wrong.Try after sometime.');
                                 }
                            }
                        });
                    }
                });
                
                // file upload date
                 $(document).on('change', '#file_comment4', function () {
                    var name = document.getElementById("file_comment4").files[0].name;
                    var form_data = new FormData();
                    var ext = name.split('.').pop().toLowerCase();
                    if (jQuery.inArray(ext, ['pdf']) == -1)
                    {
                        alert("Invalid File");
                        $('#file_comment4').val("");
                    }
                    var oFReader = new FileReader();
                    oFReader.readAsDataURL(document.getElementById("file_comment4").files[0]);
                    var f = document.getElementById("file_comment4").files[0];
                    var fsize = f.size || f.fileSize;
                    if (fsize > 2000000)
                    {
                        alert("File Size is very big");
                         $('#file_comment4').val("");
                    } else
                    {
                        $('#loader-file_comment4').css('display','block');
                        form_data.append("9[understated][comment]", document.getElementById('file_comment4').files[0]);
                        $.ajax({
                            url: "{{url('/acdeo/update_understated_file4')}}",
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
                                if(data==1){                                   
                                     $('#loader-file_comment4').css('display','none');
                                 }else{
                                     $('#loader-file_comment4').css('display','none');
                                     $('#loader-file_comment4-error').text('File not uploaded.Something went wrong.Try after sometime.');
                                 }

                            }
                        });
                    }
                });
                // end here file2
            
                // end here
            
            // skip option addded
             
              $("#skipdefect").click(function () {
                   $("#ActiveTab3")[0].click();                   
                   $('html,body').animate({scrollTop: 0}, 3000);                                      

                 });
                  $("#skipunderstated").click(function () {
                   $("#ActiveTab4")[0].click();                   
                   $('html,body').animate({scrollTop: 0}, 3000);                                      

                 });
                    $("#skipparty").click(function () {
                   $("#ActiveTab5")[0].click();                   
                   $('html,body').animate({scrollTop: 0}, 3000);                                      

                 });
                  $(document).on("keyup", ".overallsum_political", function() {
                     var $row = $(this).closest("tr");
                     var overallsum_political = parseInt($row.find('.overallsum_political').val());                     
                     var total = 0;
                    $(".overallsum_political").each(function() {
                      var val = this.value;
                      total += val == "" || isNaN(val) ? 0 : parseInt(val);
                    });
                    $("#overallsum_source_political").val(total);                     
                    $("#overallsum_source_political_grand").val(total);
                      // overall 
                     var overallsum_political = parseInt($row.find('.overallsum_source').val());                     
                     var total1 = 0;
                    $(".overallsum_source").each(function() {
                      var val = this.value;
                      total1 += val == "" || isNaN(val) ? 0 : parseInt(val);
                    })
                    $('#grand_total_political_source').val(total+total1);
                    // end
                     
                 });
          
                 $(document).on("keyup", ".overallsum_source", function() {


                     var $row = $(this).closest("tr");
                     var overallsum_political = parseInt($row.find('.overallsum_source').val());                     
                     var total = 0;
                    $(".overallsum_source").each(function() {
                      var val = this.value;
                     // alert(val);
                      //total += val == "" || isNaN(val) ? 0 : parseFloat(val);
                        total += val == "" || isNaN(val) ? 0 : parseInt(val);
                    });
                    $("#overallsum_source_amount").val(total); 
                    
                    // overall 
                     var overallsum_political = parseInt($row.find('.overallsum_political').val());                     
                     var total1 = 0;
                    $(".overallsum_political").each(function() {
                      var val = this.value;
                      total1 += val == "" || isNaN(val) ? 0 : parseInt(val);
                    })
                    $('#grand_total_political_source').val(total+total1);
                    // end 
                 });
                 
                 
                  
                 
                       
 // Set Ammount of validation 
 $('#political_fund_checque').keypress(function (e) {
    var character = String.fromCharCode(e.keyCode)
    var newValue = this.value + character;
    if (isNaN(newValue) || parseFloat(newValue) * 100 % 1 > 0) {
        e.preventDefault();
        return false;
    }
}); 
$('#political_fund_cash').keypress(function (e) {
    var character = String.fromCharCode(e.keyCode)
    var newValue = this.value + character;
    if (isNaN(newValue) || parseFloat(newValue) * 100 % 1 > 0) {
        e.preventDefault();
        return false;
    }
}); 
$('#political_fund_kind').keypress(function (e) {
    var character = String.fromCharCode(e.keyCode)
    var newValue = this.value + character;
    if (isNaN(newValue) || parseFloat(newValue) * 100 % 1 > 0) {
        e.preventDefault();
        return false;
    }
}); 
    $('#other_source_amount_cheque').keypress(function (e) {
    var character = String.fromCharCode(e.keyCode)
    var newValue = this.value + character;
    if (isNaN(newValue) || parseFloat(newValue) * 100 % 1 > 0) {
        e.preventDefault();
        return false;
    }
}); 
$('#other_source_amount_cash').keypress(function (e) {
    var character = String.fromCharCode(e.keyCode)
    var newValue = this.value + character;
    if (isNaN(newValue) || parseFloat(newValue) * 100 % 1 > 0) {
        e.preventDefault();
        return false;
    }
}); 

$('#other_source_amount').keypress(function (e) {
    var character = String.fromCharCode(e.keyCode)
    var newValue = this.value + character;
    if (isNaN(newValue) || parseFloat(newValue) * 100 % 1 > 0) {
        e.preventDefault();
        return false;
    }
}); 

           
                 


   


            /////////////////////end here //////////////////////////


</script>

<script type="text/javascript">  
    $(document).ready(function(){        
      
      var i=1;
      var j=<?php echo $countedit; ?>    
  
      $('#add').click(function(){    
           i++;    
           $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td>'+i+' </td><td><input type="text"  name="datas[expenditure_type][]" id="expenditure_type" class="form-control" value="{{!empty($getExpData[0]->expenditure_type)?$getExpData[0]->expenditure_type:''}} " ></td>                           <td style=""><input type="date"  name="datas[date_understated][]" id="date_understated" class="form-control"  placeholder="Enter date"  max="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" value="{{!empty($getExpData[0]->date_understated)?$getExpData[0]->date_understated:''}}"></td><td><input type="number" name="datas[page_no_observation][]" id="page_no_observation" class="form-control"  placeholder="" pattern="\d*" maxlength="4"value="{{!empty($getExpData[0]->page_no_observation)?$getExpData[0]->page_no_observation:''}}"><span id="error1"></span></td><td><input type="number" name="datas[amt_as_per_observation][]" id="amt_as_per_observation" class="form-control asperobservation" min="1" max="7" placeholder="" pattern="\d*" maxlength="7" value="{{!empty($getExpData[0]->amt_as_per_observation)?$getExpData[0]->amt_as_per_observation:''}}"><span id="error2"></span></td><td><input type="number" name="datas[amt_as_per_candidate][]" id="amt_as_per_candidate" class="form-control aspercand"  placeholder="" pattern="\d*" maxlength="7"value="{{!empty($getExpData[0]->amt_as_per_candidate)?$getExpData[0]->amt_as_per_candidate:''}}"><span id="error3"></span></td><td><input type="number" name="datas[amt_understated_by_candidate][]" id="amt_understated_by_candidate" class="form-control understatedamt"  placeholder="" pattern="\d*" maxlength="7" readonly="readonly"value="{{!empty($getExpData[0]->amt_understated_by_candidate)?$getExpData[0]->amt_understated_by_candidate:''}}"><span id="error4"></span></td><td><textarea  placeholder="Write description"  name="datas[description][]" id="description" placeholder="" class="form-control" id="exampleFormControlTextarea1" rows="3"> {{!empty($getExpData[0]->description)?$getExpData[0]->description:''}}</textarea></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');    
      });  

      $('#add_edit').click(function(){    
           j++;    
           $('#hello').append('<tr id="rows'+j+'" class="dynamic-addeds"><td>'+j+' </td><td><input type="text"  name="datass[expenditure_type][]" id="expenditure_type" class="form-control" value=" " ></td>                           <td style=""><input type="date"  name="datass[date_understated][]" id="date_understated" class="form-control"  placeholder="Enter date"  max="{{!empty($resultDeclarationDate['start_result_declared_date'])?$resultDeclarationDate['start_result_declared_date']:''}}" value=""></td><td><input type="number" name="datass[page_no_observation][]" id="page_no_observation" class="form-control"  placeholder="" pattern="\d*" maxlength="4"value=""><span id="error1"></span></td><td><input type="number" name="datass[amt_as_per_observation][]" id="amt_as_per_observation" class="form-control asperobservation" min="1" max="7" placeholder="" pattern="\d*" maxlength="7" value=""><span id="error2"></span></td><td><input type="number" name="datass[amt_as_per_candidate][]" id="amt_as_per_candidate" class="form-control aspercand"  placeholder="" pattern="\d*" maxlength="7"value=""><span id="error3"></span></td><td><input type="number" name="datass[amt_understated_by_candidate][]" id="amt_understated_by_candidate" class="form-control understatedamt"  placeholder="" pattern="\d*" maxlength="7" readonly="readonly"value=""><span id="error4"></span></td><td><textarea  placeholder="Write description"  name="datass[description][]" id="description" placeholder="" class="form-control" id="exampleFormControlTextarea1" rows="3"> </textarea></td><td><button type="button" name="removes" id="'+j+'" class="btn btn-danger btn_removes">X</button></td></tr>'); 
      });  
  
      $(document).on('click', '.btn_remove', function(){ 
        
           var button_id = $(this).attr("id");     
           $('#row'+button_id+'').remove();    
      });
      $(document).on('click', '.btn_removes', function(){    
           var button_id = $(this).attr("id");     
           $('#rows'+button_id+'').remove();    
      }); 


  
      $('#submit').click(function(){              
           $.ajax({    
                url:postURL,    
                method:"POST",    
                data:$('#add_name').serialize(),  
                type:'json',  
                success:function(data)    
                {  
                    i=1;  
                    $('.dynamic-added').remove();  
                    $('#add_name')[0].reset();  
                            alert('Record Inserted Successfully.');  
                }    
           });    
      });  
  
    });    

    </script>

       
        @endsection 