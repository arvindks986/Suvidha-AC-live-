@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate scrutiny form Details')
@section('bradcome', 'Scrutiny Details')
@section('description', '')
@section('content') 
 
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
                                        <td class="bdr-none">{{!empty($Acdetail->AC_NO)?$Acdetail->AC_NO:'N/A'}}-{{!empty($Acdetail->AC_NAME)?$Acdetail->AC_NAME:'N/A'}}</td>
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


                            <!--                            <div class="col-12">
                                                            <div id="steps">
                                                                <ul>
                                                                    <li><a href="javascript:void(0);"><div class="progress_step active step1 <?php echo!empty($gexExpReport[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($gexExpReport[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($gexExpReport[0]->updated_at)) : "Account Details"; ?>">1</div></a></li>
                                                                    <li><a href="javascript:void(0);"><div class="progress_step step2  <?php echo!empty($gexExpReport[0]->rp_act) ? "done" : ""; ?>" data-desc="<?php echo!empty($gexExpReport[0]->rp_act) ? "Last saved on " . date('d-m-Y h:i A', strtotime($gexExpReport[0]->updated_at)) : "Defects In Format"; ?>">2</div></a></li>
                            
                                                                    <li><a href="javascript:void(0);"><div class="progress_step step3  <?php echo!empty($getCandidateExpData[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($getCandidateExpData[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($getCandidateExpData[0]->updated_at)) : "Expense Understated"; ?>">3</div></a></li>
                            
                                                                    <li><a href="javascript:void(0);"><div class="progress_step step4  <?php echo!empty($expenditure_fund_parties[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($expenditure_fund_parties[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($expenditure_fund_parties[0]->updated_at)) : " Funds Given By Political Party"; ?>">4</div></a></li>
                            
                                                                    <li><a href="javascript:void(0);"><div class="progress_step step5  <?php echo!empty($getSourceFundData[0]) ? "done" : ""; ?>" data-desc="<?php echo!empty($getSourceFundData[0]) ? "Last saved on " . date('d-m-Y h:i A', strtotime($getSourceFundData[0]->updated_at)) : "Other Sources"; ?>">5</div></a></li>
                            
                                                                </ul>
                                                            </div>
                                                        </div>-->

                            <div id="tab1" class="tabContainer">
                                <div class="table-responsive"> 
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">DEO Scrutiny Report Details</p>
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>

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
                                                <td class="bdr-none">{{$candidateData->cand_name}} &nbsp;&nbsp; {{$candidateData->candidate_residence_address}}</td>
                                            </tr>
                                            <tr>   
                                                <td><label> 2.</label></td>
                                                <td><label>Political Party affiliation,if any</label></td> 
                                                <td class="bdr-none">{{$candidateData->PARTYNAME}}</td>
                                            </tr>                                
                                            <tr>    
                                                <td><label> 3.</label></td>
                                                <td><label>No. and name of AC</label></td> 
                                                <td class="bdr-none">{{!empty($Acdetail->AC_NO)?$Acdetail->AC_NO:'N/A'}}-{{!empty($Acdetail->AC_NAME)?$Acdetail->AC_NAME:'N/A'}}</td>
                                            </tr>
                                            <tr>   
                                                <td><label> 4.</label></td>
                                                <td><label>Name of the elected candidate:</label></td>
                                                <td class="bdr-none">{{!empty($winn_data->lead_cand_name)? $winn_data->lead_cand_name:'N/A'}}</td>
                                            </tr>
                                            <tr>  
                                                <td><label> 5.</label></td>
                                                <td><label>Date of declaration of result :</label></td>
                                                <td class="bdr-none">23-05-2019</td>                                            
                                            </tr>                                                 
                                            <tr>
                                                <td><label> 6.</label></td>
                                                <td><label> Date of Account Reconciliation Meeting</label></td>
                                                <td>{{!empty($candidateData->date_of_account_rec_meetng)?$candidateData->date_of_account_rec_meetng:'N/A'}}</td>
                                            </tr>
<!--                                            <tr>
                                                <td rowspan="2"><label for=""> 7.</label></td>
                                                <td><label>(i) Whether the Candidate or his Agent had been informed about the Date of Account Reconciliation Meeting in writing</label></td>
                                                <td @if( !empty($candidateData->reconciliation_meeting_writing) && $candidateData->reconciliation_meeting_writing=="No") rowspan="3" @endif>
                                                     {{$candidateData->reconciliation_meeting_writing}}

                                                     {{!empty($candidateData->reconciliation_meeting_writing_comment)? $candidateData->reconciliation_meeting_writing_comment:'N/A'}}
                                            </td>
                                        </tr>-->
                                            <tr>     
                                                <td rowspan="4">7.</td>
                                                <td rowspan="2"><label>(i) Whether the candidate or his agent had been informed about the date of Account Reconciliation Meeting in writing</label></td>
                                                <td >{{$candidateData->reconciliation_meeting_writing}}</td>
                                            </tr>
                                            <tr>    
                                                <td>{{!empty($candidateData->reconciliation_meeting_writing_comment)? $candidateData->reconciliation_meeting_writing_comment:'N/A'}}</td>    
                                            </tr>
                                            <tr>    
                                                <td rowspan="2" ><label>(ii) Whether he or his agent has attended the meeting</label></td>
                                                <td>{{$candidateData->agent_attend_meeting}} </td>
                                            </tr>
                                            <tr>
                                                <td>{{!empty($candidateData->agent_attend_meeting_comment)?$candidateData->agent_attend_meeting_comment:'N/A' }}</td>    
                                            </tr>
    <!--                                        <tr>
                                                <td><label>(ii) Whether he or his Agent has attended the Meeting</label></td>
                                                <td>
                                                    {{$candidateData->agent_attend_meeting}}                                                    
                                                    {{$candidateData->agent_attend_meeting_comment}} 
                                                </td>
                                            </tr>-->
<!--                                            <tr>
                                                <td><label> 8.</label></td>
                                                <td><label>Whether all the defects reconciled by the candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 19)</label></td>
                                                <td>
                                                    {{$candidateData->defect_reconciliation_meeting}}                                                    
                                                    {{$candidateData->defect_reconciliation_meeting_comment}} 
                                                </td>
                                            </tr>-->
                                            <tr>
                                                <td rowspan="3"><label> 8.</label></td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2"><label>Whether all the defects reconciled by the candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 19)</label></td>
                                                <td> {{$candidateData->defect_reconciliation_meeting}} </td>     
                                            </tr>
                                            <tr>
                                                <td> {{$candidateData->defect_reconciliation_meeting_comment}}</td>    
                                            </tr>
                                            <tr>
                                                <td><label> 9.</label></td>
                                                <td><label> Last date prescribed for lodging Account  </label></td>
                                                <td> {{$candidateData->last_date_prescribed_acct_lodge}}</td>
                                            </tr>

                                            <tr>
                                                <td rowspan="3">10.</td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2">Whether the candidate has lodged the account</td>
                                                <td>{{$candidateData->candidate_lodged_acct}}</td>     
                                            </tr>
                                            <tr>
                                                <td> {{$candidateData->candidate_lodged_acct_comment}} </td>    
                                            </tr>
                                            <tr>
                                                <td rowspan="3"><label for=""> 11.</label></td>
                                                <td><label for=""> If the candidate has lodged the account, date of lodging of account by the candidate:</label></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td><label for="">(i) original account</label></td>
                                                <td>
                                                    {{$candidateData->date_orginal_acct}}

                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="">(ii) revised account after the Account Reconciliation Meeting</label></td>
                                                <td> {{$candidateData->date_revised_acct}}</td>

                                            </tr>
                                            <tr>
                                                <td><label for=""> 12.</label></td>
                                                <td><label for=""> Whether account lodged in time  </label></td>
                                                <td>{{$candidateData->account_lodged_time}}                                

                                                </td>
                                            </tr>
                                            @if($candidateData->account_lodged_time=="No")
                                            <tr>
                                                <td><label for=""> 12A.</label></td>
                                                <td><label for=""> If not lodged in time, period of delay</label></td>
                                                <td>
                                                    {{$candidateData->not_lodged_period_delay}} in days

                                                </td>
                                            </tr>
                                            @endif 
                                            <tr>
                                                <td rowspan="3">13.</td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2"> If account not lodged or not lodged in time, whether DEO called for explanation from the candidate.
                                                    If not, reason thereof.</td>
                                                <td>{{$candidateData->reason_lodged_not_lodged}}</td>     
                                            </tr>
                                            <tr>
                                                <td>{{$candidateData->reason_lodged_not_lodged_comment}} </td>    
                                            </tr>

                                            <tr>
                                                <td><label for=""> 14.</label></td>
                                                <td><label for=""> Explanation, if any, given by the candidate</label></td>
                                                <td>{{$candidateData->explaination_by_candidate}} </td>
                                            </tr>
                                            <tr>
                                                <td><label for=""> 14A.</label></td>
                                                <td><label for=""> Comments of the DEO on the explanation if any, of the candidate</label></td>
                                                <td>{{$candidateData->comment_by_deo}} </td>
                                            </tr>
                                            <tr>
                                                <td><label for=""> 15.</label></td>
                                                <td><label for=""> Grand Total of all election expenses reported by the candidate in Part-II of the Abstract Statement</label></td>
                                                <td>
                                                    <div class="d-flex">
                                                        Rs.{{$candidateData->grand_total_election_exp_by_cadidate}}/-   
                                                    </div>

                                                </td>
                                            </tr>                                
                                        </tbody>                                                
                                    </table>                            

                                </div>
                            </div><!-- tab1 -->

                            <div id="tab2" class="tabContainer">       

                                <div class="table-responsive">
<!--                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Defects In Format of {{$candidateData->cand_name}}</p>
                                                </td>                                                 
                                            </tr>
                                        </tbody>
                                    </table>-->


                                    <table class="table table-striped table-bordered" style="width:100%">                                                     
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
                                                <td><label> Whether in the DEO's opinion, the account of election expenses of the candidate has been lodged in the manner required by the R.P. Act 1951 and C.E. Rules, 1961.</label></td>
                                                <td>{{ $candidateData->rp_act}}
                                                </td>
                                            </tr>
                                        <td rowspan="12" ><label for=""> 17.</label></td>
                                        <td rowspan="2"  >If No, then please mention the following defects with details</td>
                                        <td ></td>
                                        </tr>
                                        <tr>
                                            <td  ></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2">(i) Whether Election Expenditure Register comprising of the Day to Day Account Register, Cash Register, Bank Register, Abstract Statement has been lodged</td>
                                            <td>{{$candidateData->comprising}}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{$candidateData->comprising_comment}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2"   >(ii) Whether duly sworn in affidavit has been submitted by the candidate</td>
                                            <td   >{{$candidateData->duly_sworn}}</td>
                                        </tr>
                                        <tr>
                                            <td   >

                                                {{$candidateData->duly_sworn_comment}} 
                                            </td>

                                        </tr>
                                        <tr>
                                            <td rowspan="2"    >(iii) Whether requisite vouchers in respect of items of election expenditure submitted</td>
                                            <td    >
                                                {{$candidateData->Vouchers}}</td>
                                        </tr>
                                        <tr>
                                            <td   >


                                                {{$candidateData->Vouchers_comment}} 
                                            </td>

                                        </tr>
                                        <tr>
                                            <td rowspan="2"   >(iv) Whether separate Bank Account opened by for election</td>
                                            <td    >
                                                {{$candidateData->seprate}}</td>
                                        </tr>
                                        <tr>
                                            <td   >

                                                {{$candidateData->seprate_comment}}
                                            </td>

                                        </tr>
                                        <tr>
                                            <td  rowspan="2"   >(v) Whether all expenditure (except petty expenditure) routed through bank account</td>
                                            <td    >{{$candidateData->routed}}</td>

                                        </tr>
                                        <tr>
                                            <td >


                                                {{$candidateData->routed_comment}}
                                            </td>

                                        </tr>
                                        <tr>
                                            <td rowspan="5" >18.</td>    
                                            <td rowspan="2" >(i) Whether the DEO had issued a notice to the candidate for rectifying the defect</td>
                                            <td >{{$candidateData->rectifying}}</td>
                                        </tr>
                                        <tr>
                                            <td > 
                                                {{!empty($candidateData->notice_date) && strtotime($candidateData->notice_date)>0 ?$candidateData->notice_date:''}}<br/>
                                                {{$candidateData->rectifying_comment}}</td>

                                        </tr>
                                        <tr>
                                            <td rowspan="2"  >(ii) Whether the candidate rectified the defect</td> 
                                            <td >{{$candidateData->rectified}}</td>

                                        </tr>
                                        <tr>
                                            <td>

                                                {{$candidateData->rectified_comment}}
                                            </td>


                                        </tr>
                                        <tr>
                                            <td   >(iii) Comments of the DEO on the above, i.e. whether the defect was rectified or not.</td> 
                                            <td  >{{$candidateData->comment_of_deo}}</td>

                                        </tr>

                                        </tbody>                               

                                    </table>
                                    </form>          
                                </div>      
                            </div><!-- tab2 -->

                            <div id="tab3" class="tabContainer"> 
                                <div class="table-responsive">
<!--                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Expense Understated of {{$candidateData->cand_name}}</p>
                                                </td>                                               
                                            </tr>
                                        </tbody>
                                    </table>-->

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
                                                <td>19</td>
                                                <td><label>Whether the items of election expenses reported by the candidate correspond with the expenses shown in the Shadow Observation Register and Folder of Evidence.<br>
                                                        If no then mention the following:</label></td>    
                                                <td>    
                                                    {{!empty( $getCandidateExpData[0]->status) ?  $getCandidateExpData[0]->status:'N/A'}}                       
                                                </td>      
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <table class="table" width="100%" CELLPADDING="0" id="tblEntAttributes">
                                                        <thead>
                                                            <tr>
                                                                <th width="200">Items of expenditure</th>
                                                                <th width="140">Date</th>   
                                                                <th width="100">Page No. of Shadow Observation Register</th>  
                                                                <th width="130">Mention amount as per the Shadow Observation Register/folder of evidence</th>
                                                                <th width="130">Amount as per the account submitted by the candidate</th>   
                                                                <th width="130">Amount understated by the Candidate </th> 
                                                                <th>Description</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody >
                                                            @if($getExpData)
                                                            @foreach($getExpData as $exp)   
                                                            <tr >
                                                                <td>{{$exp->expenditure_type}}</td>
                                                                <td>{{$exp->date_understated}}</td>
                                                                <td>{{$exp->page_no_observation}}</td>
                                                                <td>{{$exp->amt_as_per_observation}}</td>
                                                                <td>{{$exp->amt_as_per_candidate}}</td>
                                                                <td>{{$exp->amt_understated_by_candidate}}</td>
                                                                <td>{{$exp->description}}</td>                                                                    
                                                            </tr>
                                                            @endforeach
                                                            @else
                                                            <tr>
                                                                <td colspan="7">N/A</td>                                                                     
                                                            </tr>
                                                            @endif

                                                        </tbody>
                                                    </table>                            
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>



                                    <table class="table table-striped table-bordered" style="width:100%">

                                        <tr>
                                            <td rowspan="3">20.</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2">Did the candidate produce his Register of the Accounting Election Expenditure for inspection by the Observer/RO/Authorized persons 3 times during campaign period</td>
                                            <td> {{!empty($getCandidateExpData[1]->status)? ucfirst($getCandidateExpData[1]->status):'N/A'}}</td>     
                                        </tr>
                                        <tr>
                                            <td>{{!empty($getCandidateExpData[1]->comment)? $getCandidateExpData[1]->comment:'N/A'}} </td>    
                                        </tr>
                                        <tr>     
                                            <td  rowspan="6">21.</td>
                                            <td  >If DEO does not agree with the facts mentioned against Row No. 19 referred to above, give the following details:-</td>
                                            <td  ></td>
                                        </tr>
                                        <tr>    
                                            <td  >(i) Were the defects notice by the DEO brought to the notice of the candidate during campaign period or during the Account Reconciliation Meeting</td>
                                            <td  >

                                                {{!empty( $getCandidateExpData[2]->status)?  ucfirst($getCandidateExpData[2]->status):'N/A'}}

                                            </td>
                                        </tr>            
                                        <tr>
                                            <td  >(ii) If yes, then annex copies of all the notices issued relating to discrepancies with English translation (if it is in regional language) and mention date of notice</td>
                                            <td  >{{!empty($getCandidateExpData[3]->extra_data)? ($getCandidateExpData[3]->extra_data):''}} </td>
                                        </tr>

                                        <tr>
                                            <td  >(iii) Did the candidate give any reply to the notice ?</td>
                                            <td  > {{!empty($getCandidateExpData[4]->status)? ucfirst($getCandidateExpData[4]->status):'N/A'}}</td>
                                        </tr>
                                        <tr>
                                            <td  >(iv) If yes, please Annex copies of such explanation received, (with the English translation of the same, if it is in regional language) and mention date of reply</td>
                                            <td  >{{!empty($getCandidateExpData[5]->comment) ? $getCandidateExpData[5]->comment:'N/A' }} </td>
                                        </tr>

                                        <tr>
                                            <td >(v) DEO's comments/observations on the candidate's explanation</td>
                                            <td >{{!empty($getCandidateExpData[6]->comment)? $getCandidateExpData[6]->comment:'N/A'}}</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="3">22.</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2"> Whether the DEO agrees that the Expenses are correctly reported by the candidate. (Should be similar to Column no. 8 of Summary Report of DEO)</td>
                                            <td> {{!empty($getCandidateExpData[7]->status) ? ucfirst($getCandidateExpData[7]->status):'N/A'}}</td>     
                                        </tr>
                                        <tr>
                                            <td> {{!empty($getCandidateExpData[7]->comment) ? $getCandidateExpData[7]->comment:'N/A'}} </td>    
                                        </tr>
                                        <tr>
                                            <td>23.</td>
                                            <td><label>Comments, if any, by the Expenditure Observer*-</label></td>
                                            <td> {{!empty($getCandidateExpData[8]->comment)? $getCandidateExpData[8]->comment:'N/A' }}</td>
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

                                    </table>
                                    </form>
                                </div>
                            </div><!-- tab3 -->

                            <div id="tab4" class="tabContainer">                                
                                <div class="table-responsive">
<!--                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Fund Given by Political Party of {{$candidateData->cand_name}}</p>
                                                </td>                                                                      
                                            </tr>
                                        </tbody>
                                    </table>-->
                                    <table id="fundParty" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th colspan="7" class="text-center" color="#ffffff">Fund Given By Political Party</th>
                                            <tr>    
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="190"><label>By Cash</label></td>
                                                <td colspan="6"> {{!empty($expenditure_fund_parties[0]->political_fund_cash)? 'Rs.'.$expenditure_fund_parties[0]->political_fund_cash.'/-':'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td width="190"><label>By Cheque/DD/RTGS</label></td>
                                                <td width="120">
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_checque)? 'Rs.'.$expenditure_fund_parties[0]->political_fund_checque.'/-':'N/A' }}

                                                </td>
                                                <td>
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_checque_date)?  $expenditure_fund_parties[0]->political_fund_checque_date:'N/A' }}

                                                </td>
                                                <td>
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_bank_name)? $expenditure_fund_parties[0]->political_fund_bank_name:'N/A' }}

                                                </td>
                                                <td>
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_acct_no)? $expenditure_fund_parties[0]->political_fund_acct_no:'N/A' }}

                                                </td>
                                                <td>
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_ifsc)? $expenditure_fund_parties[0]->political_fund_ifsc:'N/A' }}

                                                </td>
                                                <td>   
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_checque_num)? $expenditure_fund_parties[0]->political_fund_checque_num:'N/A' }}

                                                </td>
                                            </tr>

                                            <tr>
                                                <td width="190"><label>In Kind</label></td>
                                                <td>
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_kind)? 'Rs.'.$expenditure_fund_parties[0]->political_fund_kind.'/-':'N/A'}}

                                                </td>

                                                <td colspan="5">
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_kind_text)? $expenditure_fund_parties[0]->political_fund_kind_text:'N/A'}}

                                                </td>

                                            </tr> 
                                            <tr>
                                                <?php
                                                $political_fund_cash = !empty($expenditure_fund_parties[0]->political_fund_cash) ? $expenditure_fund_parties[0]->political_fund_cash : 0;
                                                $political_fund_kind = !empty($expenditure_fund_parties[0]->political_fund_kind) ? $expenditure_fund_parties[0]->political_fund_kind : 0;
                                                $political_fund_checque = !empty($expenditure_fund_parties[0]->political_fund_checque) ? $expenditure_fund_parties[0]->political_fund_checque : 0;
                                                ?>
                                                <td width="190"><label>Lump Sum Amount Given by Political Party</label></td>
                                                <td colspan="6">
                                                    {{'Rs.'.($political_fund_cash+$political_fund_kind+$political_fund_checque).'/-'}}
                                                </td>

                                            </tr>                                            
                                        </tbody>
                                    </table>
                                    </form> 
                                </div>
                            </div><!-- tab4 -->

                            <div id="tab5" class="tabContainer">                                 
                                <div class="table-responsive">
<!--                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Fund Given By Other Sources of {{$candidateData->cand_name}}</p>
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table> -->
                                    <table class="table table-bordered">
                                        <thead>                                                
                                            <tr>
                                                <th>Name</th>
                                                <th>Mode of Payment</th>
                                                <th>Amount</th>

                                            </tr>    
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            $overall_amount_source = 0;
                                            ?>

                                            @if($getSourceFundData)
                                            @foreach($getSourceFundData as $sorce)
                                            <?php
                                            $i++;
                                            $overall_amount_source += $sorce->other_source_amount;
                                            ?>
                                            <tr class="rem{{$sorce->id}}">
                                                <td>{{$sorce->other_souce_name}}</td>
                                                <td>{{$sorce->other_source_payment_mode}}</td>
                                                <td>{{$sorce->other_source_amount}}</td>

                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="3">N/A</td>
                                            </tr>
                                            @endif

                                            <tr>
                                                <td >Lump Sum Amount Given By Other Sources</td>
                                                <td colspan="2">
                                                    <?= 'Rs.' . $overall_amount_source . '/-'; ?>
                                                </td>
                                            </tr>

                                        </tbody>    
                                    </table> 
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Lump Sum Grand Total</p>
                                                </td>
                                                 <td class="bdr-none">
                                                    <p class="h6 text-center">{{'Rs.' .($political_fund_cash+$political_fund_kind+$political_fund_checque+$overall_amount_source).'/-'}}</p>
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>


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


</main>
@endsection

@section('script')

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




</script>

@endsection 