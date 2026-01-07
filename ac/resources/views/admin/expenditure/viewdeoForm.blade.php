@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate scrutiny form Details')
@section('bradcome', 'Scrutiny Details')
@section('description', '')
@section('content') 
 <?php
//$filelocation =url('/uploads/ExpenditureReportAC'); 
 
?>
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
                                        <td class="bdr-none">{{!empty($candidateData->cand_name)?$candidateData->cand_name:'N/A'}}</td>
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
                                        <td class="bdr-none"><strong class="grayClr">No. and name of Assembly Constituency:</strong></td>
                                        <td class="bdr-none">{{!empty($acdetail->AC_NO)?$acdetail->AC_NO:''}} &nbsp;&nbsp;{{!empty($acdetail->AC_NAME)? $acdetail->AC_NAME:'N/A'}}</td>
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
                                                <td class="bdr-none">{{!empty($candidateData->cand_name)?$candidateData->cand_name:'N/A'}} &nbsp;&nbsp; {{$candidateData->candidate_residence_address}}</td>
                                            </tr>
                                            <tr>   
                                                <td><label> 2.</label></td>
                                                <td><label>Political Party affiliation,if any</label></td> 
                                                <td class="bdr-none">{{$candidateData->PARTYNAME}}</td>
                                            </tr>                                
                                            <tr>    
                                                <td><label> 3.</label></td>
                                                <td><label>No. and name of Assembly Constituency</label></td> 
                                                <td class="bdr-none">{{!empty($acdetail->AC_NO)?$acdetail->AC_NO:''}} &nbsp;&nbsp;{{!empty($acdetail->AC_NAME)? $acdetail->AC_NAME:'N/A'}}</td>
                                            </tr>
                                           
                                            <tr>   
                                                <td><label> 4.</label></td>
                                                <td><label>Name of the elected candidate:</label></td>
                                                <td class="bdr-none">{{!empty($winn_data->lead_cand_name)? $winn_data->lead_cand_name:'N/A'}}</td>
                                            </tr>
                                            <tr>  
                                                <td><label> 5.</label></td>
                                                <td><label>Date of declaration of result :</label></td>
                                                <td class="bdr-none">{{!empty($candidateData->date_of_declaration) && strtotime($candidateData->date_of_declaration)>0 ? date('d-m-Y',strtotime($candidateData->date_of_declaration)):'N/A'}}</td>                                            
                                            </tr>                                                 
                                            <tr>
                                                <td><label> 6.</label></td>
                                                <td><label> Date of Account Reconciliation Meeting</label></td>
                                                <td>{{!empty($candidateData->date_of_account_rec_meetng)?date('d-m-Y',strtotime($candidateData->date_of_account_rec_meetng)):'N/A'}}</td>
                                            </tr>
 
                                            <tr>     
                                                <td rowspan="4">7.</td>
                                                <td rowspan="2"><label>(i) Whether the candidate or his agent had been informed about the date of Account Reconciliation Meeting in writing</label></td>
                                                <td >{{!empty($candidateData->reconciliation_meeting_writing)? $candidateData->reconciliation_meeting_writing:'N/A'}}</td>
                                            </tr>
                                            <tr>    
                                                <td>{{!empty($candidateData->reconciliation_meeting_writing_comment)? $candidateData->reconciliation_meeting_writing_comment:'N/A'}}</td>    
                                            </tr>
                                            <tr>    
                                                <td rowspan="2" ><label>(ii) Whether he or his agent has attended the meeting</label></td>
                                                <td>{{!empty($candidateData->agent_attend_meeting)?$candidateData->agent_attend_meeting:'N/A'}} </td>
                                            </tr>
                                            <tr>
                                                <td>{{!empty($candidateData->agent_attend_meeting_comment)?$candidateData->agent_attend_meeting_comment:'N/A' }}</td>    
                                            </tr>
    
                                            <tr>
                                                <td rowspan="3"><label> 8.</label></td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2"><label>Whether all the defects reconciled by the candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 19)</label></td>
                                                <td> {{!empty($candidateData->defect_reconciliation_meeting)?$candidateData->defect_reconciliation_meeting:'N/A'}} </td>     
                                            </tr>
                                            <tr>
                                                <td> {{!empty($candidateData->defect_reconciliation_meeting_comment)?$candidateData->defect_reconciliation_meeting_comment:'N/A'}}</td>    
                                            </tr>
                                            <tr>
                                                <td><label> 9.</label></td>
                                                <td><label> Last date prescribed for lodging Account  </label></td>
                                                <td> {{!empty($candidateData->last_date_prescribed_acct_lodge) && strtotime($candidateData->last_date_prescribed_acct_lodge)>0 ?date('d-m-Y',strtotime($candidateData->last_date_prescribed_acct_lodge)):'N/A'}}</td>
                                            </tr>

                                            <tr>
                                                <td rowspan="3">10.</td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2">Whether the candidate has lodged the account</td>
                                                <td>{{!empty($candidateData->candidate_lodged_acct)?$candidateData->candidate_lodged_acct:'N/A'}}</td>     
                                            </tr>
                                            <tr>
                                                <td> {{!empty($candidateData->candidate_lodged_acct_comment) && $candidateData->candidate_lodged_acct=="No" ?$candidateData->candidate_lodged_acct_comment:'N/A'}} </td>    
                                            </tr>

                                        @if(!empty($candidateData->candidate_lodged_acct) && $candidateData->candidate_lodged_acct=="Yes")
                                            <tr>
                                                <td rowspan="3"><label for=""> 11.</label></td>
                                                <td><label for=""> If the candidate has lodged the account, date of lodging of account by the candidate:</label></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td><label for="">(i) original account</label></td>
                                                <td>
                                                   {{!empty($candidateData->date_orginal_acct) && strtotime($candidateData->date_orginal_acct)>0 ? date('d-m-Y',strtotime($candidateData->date_orginal_acct)):'N/A'}}


                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="">(ii) revised account after the Account Reconciliation Meeting</label></td>
                                                <td> {{!empty($candidateData->date_revised_acct) && strtotime($candidateData->date_revised_acct)>0 ? date('d-m-Y',strtotime($candidateData->date_revised_acct)):'N/A'}}</td>

                                            </tr>

                                         
                                            <tr>
                                                <td><label for=""> 12.</label></td>
                                                <td><label for=""> Whether account lodged in time  </label></td>
                                                <td>{{!empty($candidateData->account_lodged_time)?$candidateData->account_lodged_time:'N/A'}}                                

                                                </td>
                                            </tr> 

                                            @endif
                                            @if($candidateData->account_lodged_time=="No" && !empty($candidateData->candidate_lodged_acct) && $candidateData->candidate_lodged_acct=="Yes")
                                            <tr>
                                                <td><label for=""> 12A.</label></td>
                                                <td><label for=""> If not lodged in time, period of delay</label></td>
                                                <td>
                                                    {{!empty($candidateData->not_lodged_period_delay) && $candidateData->not_lodged_period_delay>0? $candidateData->not_lodged_period_delay:0}} in days

                                                </td>
                                            </tr>
                                            @endif 
                                            <tr>
                                                <td rowspan="3">13.</td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2"> If account not lodged or not lodged in time, whether DEO called for explanation from the candidate.
                                                    If not, reason thereof.</td>
                                                <td>{{!empty($candidateData->reason_lodged_not_lodged)?$candidateData->reason_lodged_not_lodged:'N/A' }}</td>     
                                            </tr>
                                            <tr>
                                                <td>{{!empty($candidateData->reason_lodged_not_lodged_comment)?$candidateData->reason_lodged_not_lodged_comment:'N/A'}} </td>    
                                            </tr>

                                            <tr>
                                                <td><label for=""> 14.</label></td>
                                                <td><label for=""> Explanation, if any, given by the candidate</label></td>
                                                <td>{{!empty($candidateData->explaination_by_candidate)?$candidateData->explaination_by_candidate:'N/A' }} </td>
                                            </tr>
                                            <tr>
                                                <td><label for=""> 14A.</label></td>
                                                <td><label for=""> Comments of the DEO on the explanation if any, of the candidate</label></td>
                                                <td>{{!empty($candidateData->comment_by_deo)?$candidateData->comment_by_deo:'N/A'}} </td>
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
                                                <td>{{ !empty($candidateData->rp_act)? $candidateData->rp_act:'N/A'}}
                                                </td>
                                            </tr>
                                             @if(!empty($candidateData->rp_act) && $candidateData->rp_act=="No")
                                            <tr>
                                        <td rowspan="12" ><label for=""> 17.</label></td>
                                        <td rowspan="2"  >If No, then please mention the following defects with details</td>
                                        <td ></td>
                                        </tr>
                                        <tr>
                                            <td  ></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2">(i) Whether Election Expenditure Register comprising of the Day to Day Account Register, Cash Register, Bank Register, Abstract Statement has been lodged</td>
                                            <td>{{!empty($candidateData->comprising)?$candidateData->comprising:'N/A'}}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{!empty($candidateData->comprising_comment) && $candidateData->comprising=="No"? $candidateData->comprising_comment:'N/A'}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2"   >(ii) Whether duly sworn in affidavit has been submitted by the candidate</td>
                                            <td   >{{!empty($candidateData->duly_sworn)?$candidateData->duly_sworn:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td>

                                                {{!empty($candidateData->duly_sworn_comment) && $candidateData->duly_sworn=="No"?$candidateData->duly_sworn_comment:'N/A'}} 
                                            </td>

                                        </tr>
                                        <tr>
                                            <td rowspan="2"    >(iii) Whether requisite vouchers in respect of items of election expenditure submitted</td>
                                            <td    >
                                                {{!empty($candidateData->Vouchers)? $candidateData->Vouchers:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td   >


                                                {{!empty($candidateData->Vouchers_comment) && $candidateData->Vouchers=="No"?$candidateData->Vouchers_comment:'N/A' }} 
                                            </td>

                                        </tr>
                                        <tr>
                                            <td rowspan="2"   >(iv) Whether separate Bank Account opened by for election</td>
                                            <td    >
                                                {{!empty($candidateData->seprate)?$candidateData->seprate:'N/A'}}</td>
                                        </tr>
                                        <tr>
                                            <td   >

                                                {{!empty($candidateData->seprate_comment)&& $candidateData->seprate=="No"?$candidateData->seprate_comment:'N/A'}}
                                            </td>

                                        </tr>
                                        <tr>
                                            <td  rowspan="2"   >(v) Whether all expenditure (except petty expenditure) routed through bank account</td>
                                            <td    >{{!empty($candidateData->routed)?$candidateData->routed:'N/A'}}</td>

                                        </tr>
                                        <tr>
                                            <td >
                                                {{!empty($candidateData->routed_comment) && $candidateData->routed=="No"?$candidateData->routed_comment:'N/A'}}
                                            </td>

                                        </tr>
                                        @endif
                                        <tr>
                                            <td rowspan="5" >18.</td>    
                                            <td rowspan="2" >(i) Whether the DEO had issued a notice to the candidate for rectifying the defect</td>
                                            <td >{{!empty($candidateData->rectifying)?$candidateData->rectifying:'N/A' }}
                                                @if($candidateData->rectifying =="Yes" && !empty($download_link3))
                                            <br/>
                     <a href="{{$download_link3}}"  target="_blank">Download</a>
                    <br/>
                                            @endif</td>
                                        </tr>
                                        <tr>
                                            <td > 
                                                {{!empty($candidateData->notice_date) && $candidateData->rectifying=="Yes" && strtotime($candidateData->notice_date)>0 ? date('d-m-Y',strtotime($candidateData->notice_date)):''}}<br/>
                                                {{!empty($candidateData->rectifying_comment) && $candidateData->rectifying=="No" ?$candidateData->rectifying_comment:''}}</td>

                                        </tr>
                                        <tr>
                                            <td rowspan="2"  >(ii) Whether the candidate rectified the defect</td> 
                                            <td >{{!empty($candidateData->rectified)? $candidateData->rectified:'N/A'}}</td>

                                        </tr>
                                        <tr>
                                            <td>

                                                {{!empty($candidateData->rectified_comment)?$candidateData->rectified_comment:'N/A'}}
                                            </td>


                                        </tr>
                                        <tr>
                                            <td   >(iii) Comments of the DEO on the above, i.e. whether the defect was rectified or not.</td> 
                                            <td  >{{!empty($candidateData->comment_of_deo)?$candidateData->comment_of_deo:'N/A'}}</td>

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
                                                    {{!empty( $getCandidateExpData[0]->status) ? ucfirst($getCandidateExpData[0]->status):'N/A'}}                       
                                                </td>      
                                            </tr>
                                             @if(!empty($getCandidateExpData[0]->status) && $getCandidateExpData[0]->status=="no")
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

                                                            @if(!empty($exp->date_understated) && !empty($exp->page_no_observation) )   
                                                            <tr >
                                                                <td>{{$exp->expenditure_type}}</td>
                                                                <td>{{!empty($exp->date_understated) && strtotime($exp->date_understated)>0 ? date('d-m-Y',strtotime($exp->date_understated)):'N/A'}}</td>
                                                                <td>{{$exp->page_no_observation}}</td>
                                                                <td>{{$exp->amt_as_per_observation}}</td>
                                                                <td>{{$exp->amt_as_per_candidate}}</td>
                                                                <td>{{$exp->amt_understated_by_candidate}}</td>
                                                                <td>{{$exp->description}}</td>                                                                    
                                                            </tr>
                                                            @endif
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
                                            @endif
                                        </tbody>
                                    </table>



                                    <table class="table table-striped table-bordered" style="width:100%">

                                        <tr>
                                            <td rowspan="3"  >20.</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2" width="800"><label>Did the candidate produce his Register of the Accounting Election Expenditure for inspection by the Observer/RO/Authorized persons 3 times during campaign period</label></td>
                                            <td> {{!empty($getCandidateExpData[1]->status)? ucfirst($getCandidateExpData[1]->status):'N/A'}}</td>     
                                        </tr>
                                        <tr>
                                            <td>{{!empty($getCandidateExpData[1]->comment)? $getCandidateExpData[1]->comment:'N/A'}} </td>    
                                        </tr>


                                     
                                        


                                        @if(!empty($getCandidateExpData[0]->status) && $getCandidateExpData[0]->status=="yes" && !empty($getCandidateExpData[1]->status) && $getCandidateExpData[1]->status=="yes")
                                          @else
                                         
                                        <tr>     
                                            <td  width="100" rowspan="6">21.</td>
                                            <td  ><label>If DEO does not agree with the facts mentioned against Row No. 19 referred to above, give the following details:-</label></td>
                                            <td  ></td>
                                        </tr>
                                        <tr>    
                                            <td  ><label>(i) Were the defects notice by the DEO brought to the notice of the candidate during campaign period or during the Account Reconciliation Meeting</label></td>
                                            <td  >

                                                {{!empty( $getCandidateExpData[2]->status)?  ucfirst($getCandidateExpData[2]->status):'N/A'}}

                                            </td>
                                        </tr>
                                           

                                        <tr>
                                            <td  >(ii) If yes, then annex copies of all the notices issued relating to discrepancies with English translation (if it is in regional language) and mention date of notice</td>
                                            <td  >{{!empty($getCandidateExpData[3]->extra_data)&& strtotime($getCandidateExpData[3]->extra_data)>0 && $getCandidateExpData[2]->status =="yes"? date('d-m-Y',strtotime($getCandidateExpData[3]->extra_data)):'N/A'}}
                                               @if(!empty($getCandidateExpData[2]->status) && ($getCandidateExpData[2]->status =="Yes" || $getCandidateExpData[2]->status =="yes") && !empty($download_link1)) <br/>
                                                <a href="{{$download_link1}}"  target="_blank">Download</a>
                                            @endif
                                            </td>
                                        </tr>
                                        

                                        <tr>
                                            <td  >(iii) Did the candidate give any reply to the notice ?</td>
                                            <td  > {{!empty($getCandidateExpData[4]->status)? ucfirst($getCandidateExpData[4]->status):'N/A'}}</td>
                                        </tr>
                                        <tr>
                                            <td  >(iv) If yes, please Annex copies of such explanation received, (with the English translation of the same, if it is in regional language) and mention date of reply</td>
                                            <td  >  
                                          @if(!empty($getCandidateExpData[4]->status) && ($getCandidateExpData[4]->status =="Yes" || $getCandidateExpData[4]->status =="yes") && !empty($download_link2))
                                                <br/>
                     <a href="{{$download_link2}}"  target="_blank">Download</a>
                                            
                                            @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td >(v) DEO's comments/observations on the candidate's explanation</td>
                                            <td >{{!empty($getCandidateExpData[6]->comment)? $getCandidateExpData[6]->comment:'N/A'}}</td>
                                        </tr>
                                        @endif


 

                                      <!--   <tr>
                                            <td rowspan="3"  >22.</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2" width="800"><label>Whether the DEO agrees that the Expenses are correctly reported by the candidate. (Should be similar to Column no. 8 of Summary Report of DEO)</label></td>
                                            <td> {{!empty($getCandidateExpData[7]->status) ? ucfirst($getCandidateExpData[7]->status):'N/A'}}</td>     
                                        </tr>
                                        <tr>
                                            <td>{{!empty($getCandidateExpData[7]->comment) ? $getCandidateExpData[7]->comment:'N/A'}} </td>    
                                        </tr> -->
 
                                        <tr>
                                            <td   rowspan="3">22.</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2"> Whether the DEO agrees that the Expenses are correctly reported by the candidate. (Should be similar to Column no. 8 of Summary Report of DEO)</td>
                                            <td> {{!empty($getCandidateExpData[7]->status) ? ucfirst($getCandidateExpData[7]->status):'N/A'}}</td>     
                                        </tr>
                                        <tr>
                                            <td> {{!empty($getCandidateExpData[7]->comment) && $getCandidateExpData[7]->status=="no" ? $getCandidateExpData[7]->comment:'N/A'}} </td>    
                                        </tr>
                                        <tr>
                                            <td>23.</td>
                                            <td><label>Comments, if any, by the Expenditure Observer*-</label></td>
                                            <td> {{!empty($getCandidateExpData[8]->comment)? $getCandidateExpData[8]->comment:'N/A' }}
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
                                                <td colspan="6"> {{!empty($expenditure_fund_parties[0]) && $expenditure_fund_parties[0]->political_fund_cash >0 ?number_format((float)$expenditure_fund_parties[0]->political_fund_cash, 2, '.', ''):'N/A'}}</td>
                                            </tr>
                                            <tr>
                                                <td width="190"><label>By Cheque/DD/RTGS/NEFT/OTHER ELECTRONIC MEDIUM</label></td>
                                                <td width="120">
                                                 
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_checque)?number_format((float)$expenditure_fund_parties[0]->political_fund_checque, 2, '.', ''):'N/A'}}

                                                </td>
                                                <td>
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_checque_date) && strtotime($expenditure_fund_parties[0]->political_fund_checque_date)>0? date('d-m-Y', strtotime($expenditure_fund_parties[0]->political_fund_checque_date)):'N/A' }}

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
                                                {{(!empty($expenditure_fund_parties[0]->political_fund_kind) && $expenditure_fund_parties[0]->political_fund_kind >0)?number_format((float)$expenditure_fund_parties[0]->political_fund_kind, 2, '.', ''):'N/A'}}

                                                </td>

                                                <td colspan="5">
                                                    {{!empty($expenditure_fund_parties[0]->political_fund_kind_text)? $expenditure_fund_parties[0]->political_fund_kind_text:'N/A'}}

                                                </td>

                                            </tr> 
                                            <tr>
                                            <?php
                                                    $political_fund_cash = !empty($expenditure_fund_parties[0]->political_fund_cash) ? number_format((float)$expenditure_fund_parties[0]->political_fund_cash, 2, '.', ''): 0;
                                                    $political_fund_kind = !empty($expenditure_fund_parties[0]->political_fund_kind) ? number_format((float)$expenditure_fund_parties[0]->political_fund_kind, 2, '.', '') : 0;
                                                    $political_fund_checque = !empty($expenditure_fund_parties[0]->political_fund_checque) ? number_format((float)$expenditure_fund_parties[0]->political_fund_checque, 2, '.', '') : 0;
                                                    $t= $political_fund_cash+$political_fund_kind+$political_fund_checque;
                                                    $fundTotal =  number_format((float)$t, 2, '.', '');
                                                    ?>
                                                <td width="190"><label>Lump Sum Amount Given by Political Party</label></td>
                                                <td colspan="6">
                                                    {{'Rs.'.($fundTotal).'/-'}}
                                                </td>

                                            </tr>                                            
                                        </tbody>
                                    </table>
                                    </form> 
                                </div>
                            </div><!-- tab4 -->

                            <div id="tab5" class="tabContainer">                                 
                                <div class="table-responsive">
                                <?php
                                 $sumFundSource=array_sum(array_column($getSourceFundData, 'other_source_amount')); 
                                 $overall_amount_source = 0;
                                 $overall_Cash_amount=0;
                                 $overall_Cheque_amount=0;
                                 $overall_Kind_amount=0;
                                 $overall_Others_amount=0;
                                    foreach ($getSourceFundData as $items){ 

                                    $overall_amount_source += !empty($items->other_source_amount) ? $items->other_source_amount : 0;
                                    if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="Cash" ){
                                        $overall_Cash_name1=!empty($items->other_souce_name) ? $items->other_souce_name : 'Others';
                                        $overall_Cash_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                                        $overall_Cash_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
                                    }
                                     if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="Cheque" ){
                                        $overall_Cheque_name1=!empty($items->other_souce_name) ? $items->other_souce_name : 'Others';
                                        $overall_Cheque_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                                        $overall_Cheque_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
                                    }
                                    if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="In Kind" ){

                                        $overall_kind_name1=!empty($items->other_souce_name) ? $items->other_souce_name : 'Others';
                                        $overall_kind_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                                        $overall_Kind_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
                                    }
                                    if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="Others" ){
                                        $overall_other_name1='Others';
                                         //$overall_Cash_name1=!empty($items->other_souce_name) ? $items->other_souce_name : 'Others';
                                        $overall_other_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                                        $overall_Others_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
                                    }
                                    }
                                ?>
                                    <table class="table table-bordered">
                                        <thead>                                                
                                            <tr>
                                                  
                                                <th>Name</th>
                                                 
                                                <th>Mode of Payment</th>
                                                  
                                                <th>Amount</th>
                                                  
                                            </tr>    
                                        </thead>
                                        <tbody>
                                             @if(count($getSourceFundData)>0)
             
                                             @if(!empty($overall_Cash_amount))
            <tr>
                <td  >{{!empty($overall_Cash_name1)?$overall_Cash_name1:'N/A'}}</td>
                <td >{{!empty($overall_Cash_name2)?$overall_Cash_name2:'N/A'}}</td>
                <td  >{{!empty($overall_Cash_amount)?number_format((float)$overall_Cash_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
               @if(!empty($overall_Cheque_amount))
            <tr>
                <td  >{{!empty($overall_Cheque_name1)?$overall_Cheque_name1:'N/A'}}</td>
                <td >{{!empty($overall_Cheque_name2)?$overall_Cheque_name2:'N/A'}}</td>
                <td >{{!empty($overall_Cheque_amount)?number_format((float)$overall_Cheque_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
              @if(!empty($overall_Kind_amount))
            <tr>
                <td  >{{!empty($overall_kind_name1)?$overall_kind_name1:'N/A'}}</td>
                <td >{{!empty($overall_kind_name2)?$overall_kind_name2:'N/A'}}</td>
                <td >{{!empty($overall_Kind_amount)?number_format((float)$overall_Kind_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
              @if(!empty($overall_Others_amount))
            <tr>
                <td >{{!empty($overall_other_name1)?$overall_other_name1:'N/A'}}</td>
                <td >{{!empty($overall_other_name2)?$overall_other_name2:'N/A'}}</td>
                <td>{{!empty($overall_Others_amount)?number_format((float)$overall_Others_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
                                            @else
                                            <tr>
                                                <td colspan="3">N/A</td>
                                            </tr>
                                            @endif

                                            <tr>
                                                <td >Lump Sum Amount Given By Other Sources</td>
                                                <td colspan="2">
                                                    <?= 'Rs.' . number_format((float)$sumFundSource, 2, '.', '') . '/-'; ?>
                                                </td>
                                            </tr>

                                        </tbody>    
                                    </table> 
                                    <?php
                                        $totalamount=$political_fund_cash+$political_fund_kind+$political_fund_checque+$overall_amount_source;
                                        $grandTotal = number_format((float)$totalamount, 2, '.', '');
                                        ?>
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bdr-none">
                                                    <p class="h6 text-center">Lump Sum Grand Total</p>
                                                </td>
                                                 <td class="bdr-none">
                                                    <p class="h6 text-center">{{'Rs.' .($grandTotal).'/-'}}</p>
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