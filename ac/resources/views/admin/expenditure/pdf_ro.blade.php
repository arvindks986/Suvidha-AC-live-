<?php
//$filelocation =url('/uploads/ExpenditureReportAC'); 
  $countingDate=\app(App\models\Expenditure\ExpenditureModel::class)->getResultDeclarationDate();
  $DB_MONTH=Session::get('DB_MONTH');
  $DB_MONTH=!empty($DB_MONTH) ? $DB_MONTH : '0';
  $DB_YEAR=Session::get('DB_YEAR');
  $DB_CONS_TYPE=Session::get('DB_CONS_TYPE');
  $DB_ELE_TYPE=Session::get('DB_ELE_TYPE');
  $monthName = date("F", mktime(0, 0, 0, $DB_MONTH, 10));
   
?>
   
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Scrutiny Candidate Reports</title>
        <style type="text/css">
            .table-strip{border-collapse: collapse;}
            .table-strip th,.table-strip td{text-align: center;}
            .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
        </style>
    </head>
    <body>
        <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
            <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/images/logo/suvidha-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br> 
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
            </thead>
        </table>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
            <tr>
                <td  style="width:49%;">
                    <table  style="width:100%" align="center">ST_NAME
                        <tbody>
                            <tr>
                                <td align="center"><strong>Candidate Scrutiny Report</strong></td>
                            </tr>
                            <tr>
                                <td align="center"><strong>Date Of Submit Scrutiny Form:</strong> {{!empty($submitedData) && strtotime($submitedData) >0 ? date('d.m.Y',strtotime($submitedData)):'N/A'}}
                                </td>
                            </tr>
                           
                        </tbody>
                    </table>  
                </td>
            </tr>
        </table>
        <!-- <p style="float: left; font-weight: bold;">Serial Number of the Candidate in Summary Report of the DEO</p> -->
        <!-- <p style="border-bottom: 1px dotted #000; float: left; font-weight: bold; padding-top: -55px; margin-left: 480px;">abcdefghijk xyz</p> -->



        <p style="text-align: left; font-size: 12pt; background-color: #eaebed; padding: 5px; border: 1px solid #eaebed;"><strong>State:&nbsp;</strong>{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->ST_NAME:''}}&nbsp;&nbsp;&nbsp;&nbsp;<strong>District:&nbsp;</strong>{{!empty($districtDetails->DIST_NAME)? $districtDetails->DIST_NAME:'N/A'}}&nbsp;&nbsp;&nbsp;<strong>Election Type & Year:&nbsp;</strong>{{!empty($electionType) ? $electionType.' '.$monthName.'-'.$DB_YEAR : ''}}</p>
        <font face="Arial" size="2pt">
        <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;; margin-top: 30px;">
            <thead>
                <tr>                                 
                    <th colspan="7" bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px;text-align: center; color: white;">
                        DEO's SCRUTINY REPORT ON ELECTION EXPENSES OF THE CANDIDATE UNDER RULE 89 OF C.E. RULES, 1961
                    </th> 

                </tr>
                <tr>                                 
                    <th bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px;text-align: left; color: white;" width="15%">Sr. No</th> 
                    <th colspan="4" bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px;text-align: left; color: white;" width="50%">Description</th>
                    <th colspan="2" bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px;text-align: left; color: white;" width="35%">To be Filled up by the DEO</th> 
                </tr>
            </thead>
            
             
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">1.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Name & address of the candidate:</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->cand_name:''}}  &nbsp; &nbsp; &nbsp; {{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->candidate_residence_address:'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">2.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Political Party affiliation, if any</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($partyname)?$partyname:''}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">3.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">No. and name of Assembly Constituency</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($acdetail->AC_NO)?$acdetail->AC_NO:''}} &nbsp;&nbsp;{{!empty($acdetail->AC_NAME)? $acdetail->AC_NAME:'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">4.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Name of the elected candidate</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($winn_data->lead_cand_name)? $winn_data->lead_cand_name:'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">5.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Date of declaration of result</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]) && (strtotime($scrutinyReportData[0]->date_of_declaration)>0) ?GetDateFormat($scrutinyReportData[0]->date_of_declaration):'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">6.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Date of Account Reconciliation Meeting</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]) && strtotime($scrutinyReportData[0]->date_of_account_rec_meetng)>0 ? GetDateFormat($scrutinyReportData[0]->date_of_account_rec_meetng):''}}</td>
            </tr>
            <tr>
                <td rowspan="4" style="border: 1px solid #5d5f61; padding: 12px;">7.</td>
                <td rowspan="2" colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">(i) Whether the candidate or his agent had been informed about the date of Account Reconciliation Meeting in writing</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{$scrutinyReportData[0]->reconciliation_meeting_writing}}</td>
            </tr>
            <tr>    
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->reconciliation_meeting_writing_comment) ? $scrutinyReportData[0]->reconciliation_meeting_writing_comment:'N/A'}}</td>

            </tr>
            <tr>
                <td rowspan="2" colspan="4" style="border: 1px solid #5d5f61; padding: 12px;" >(ii) Whether he or his agent has attended the Meeting</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{$scrutinyReportData[0]->agent_attend_meeting}}</td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->agent_attend_meeting_comment)?$scrutinyReportData[0]->agent_attend_meeting_comment:'N/A'}}</td>
            </tr>
<!--            <tr>
                <td  rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;">8.</td>
                <td colspan="4" @if( !empty($scrutinyReportData[0]->defect_reconciliation_meeting) && $scrutinyReportData[0]->defect_reconciliation_meeting=="No") rowspan="2" @endif  style="border: 1px solid #5d5f61; padding: 12px;">Whether all the defects Reconciled by the Candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 19) :</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">
                    {{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->defect_reconciliation_meeting:'N/A'}}
                </td>
            </tr>
            @if(!empty($scrutinyReportData[0]->defect_reconciliation_meeting) && $scrutinyReportData[0]->defect_reconciliation_meeting=="No")
            <tr>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->defect_reconciliation_meeting_comment)?$scrutinyReportData[0]->defect_reconciliation_meeting_comment:'N/A'}}</td>
            </tr>
            @endif-->

            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;">8.</td>
                <td colspan="4" @if( !empty($scrutinyReportData[0]->defect_reconciliation_meeting) && $scrutinyReportData[0]->defect_reconciliation_meeting=="No") rowspan="2" @endif style="border: 1px solid #5d5f61; padding: 12px;">Whether all the defects reconciled by the candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 19)</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->defect_reconciliation_meeting:'N/A'}}</td>
            </tr>
            @if(!empty($scrutinyReportData[0]->defect_reconciliation_meeting) && $scrutinyReportData[0]->defect_reconciliation_meeting=="No")
            <tr>
                <td colspan="3" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->defect_reconciliation_meeting_comment)?$scrutinyReportData[0]->defect_reconciliation_meeting_comment:'N/A'}}</td>
            </tr>
            @else
            <tr><td colspan="3"></td></tr>
            @endif
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">9.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Last date prescribed for lodging Account</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]) && strtotime($scrutinyReportData[0]->last_date_prescribed_acct_lodge)>0 ? GetDateFormat($scrutinyReportData[0]->last_date_prescribed_acct_lodge):''}}</td>
            </tr>
            <tr>
<!--    <td rowspan="3" style="border: 1px solid #5d5f61; padding: 12px;">10.</td>
   <td rowspan="3" style="border: 1px solid #5d5f61; padding: 12px;" >Whether the Candidate has Lodged the Account :</td>     
 </tr>
 <tr>
   <td  @if(!empty($scrutinyReportData[0]->candidate_lodged_acct) &&  $scrutinyReportData[0]->candidate_lodged_acct=="No") style="border: 1px solid #5d5f61; padding: 12px;" @endif
         >3</td>
 </tr> 
  <tr>
   <td @if(!empty($scrutinyReportData[0]->candidate_lodged_acct) &&  $scrutinyReportData[0]->candidate_lodged_acct=="No") style="border: 1px solid #5d5f61; padding: 12px;" @endif>4</td>     
 </tr>-->
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;">10.</td>
                <td colspan="4" @if(!empty($scrutinyReportData[0]->candidate_lodged_acct) &&  $scrutinyReportData[0]->candidate_lodged_acct=="No") rowspan="2" @endif style="border: 1px solid #5d5f61; padding: 12px;">Whether the candidate has lodged the account</td>

                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->candidate_lodged_acct:'N/A'}}</td>
            </tr>
            @if(!empty($scrutinyReportData[0]->candidate_lodged_acct) &&  $scrutinyReportData[0]->candidate_lodged_acct=="No")
            <tr>
                <td colspan="3" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->candidate_lodged_acct_comment)?$scrutinyReportData[0]->candidate_lodged_acct_comment:'N/A'}}</td>
            </tr>
            @else
            <tr><td colspan="3"></td></tr>
            @endif

            @if(!empty($scrutinyReportData[0]->candidate_lodged_acct) && $scrutinyReportData[0]->candidate_lodged_acct=="Yes")
            <tr>     
                <td style="border: 1px solid #5d5f61; padding: 12px;" rowspan="3">11.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">If the candidate has lodged the account, date of lodging of account by the candidate:</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;"></td>
            </tr>
            <tr>    
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">(i) original account</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]) && strtotime($scrutinyReportData[0]->date_orginal_acct)>0 ? GetDateFormat($scrutinyReportData[0]->date_orginal_acct):''}}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;" >(ii) Revised account after the Account Reconciliation Meeting</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0])&& strtotime($scrutinyReportData[0]->date_revised_acct)>0 ? GetDateFormat($scrutinyReportData[0]->date_revised_acct):''}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">12.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Whether account lodged in time</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->account_lodged_time) ?$scrutinyReportData[0]->account_lodged_time:'N/A'}}</td>
            </tr>
            @endif
            @if(!empty($scrutinyReportData[0]->account_lodged_time) &&  $scrutinyReportData[0]->account_lodged_time=="No" && $scrutinyReportData[0]->candidate_lodged_acct=="Yes")
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">12A.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">If not lodged in time, period of delay</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]) ?$scrutinyReportData[0]->not_lodged_period_delay:0}} in days</td>
            </tr>
            @endif  
             
            <tr>
                <td rowspan="3" style="border: 1px solid #5d5f61; padding: 12px;">13.</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="4" style="border: 1px solid #5d5f61; padding: 12px;"> If account not lodged or not lodged in time, whether DEO called for explanation from the candidate.
                    If not, reason thereof.</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->reason_lodged_not_lodged)?$scrutinyReportData[0]->reason_lodged_not_lodged:'N/A' }}</td>     
            </tr>
            <tr>
                <td colspan="3"  style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]->reason_lodged_not_lodged_comment)?$scrutinyReportData[0]->reason_lodged_not_lodged_comment:'N/A'}} </td>    
            </tr>            

            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">14.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Explanation, if any, given by the candidate</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0]) ?$scrutinyReportData[0]->explaination_by_candidate:''}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" >14A.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Comments of the DEO on the explanation if any, of the candidate</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->comment_by_deo:''}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" >15.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Grand Total of all election expenses reported by the candidate in Part-II of the Abstract Statement</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0])? 'Rs.'.$scrutinyReportData[0]->grand_total_election_exp_by_cadidate.'/-':''}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">16.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Whether in the DEO's opinion, the account of election expenses of the candidate has been lodged in the manner required by the R.P. Act 1951 and C.E. Rules, 1961</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->rp_act:''}}</td>
            </tr>
 

 

            <tr>
                <td rowspan="12" style="border: 1px solid #5d5f61; padding: 12px;">17.</td>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">If No, then please mention the following defects with details</td>
                <td  style="border: 1px solid #5d5f61; padding: 12px;" colspan="4"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4" ></td>
            </tr>
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(i) Whether Election Expenditure Register comprising of the Day to Day Account Register, Cash Register, Bank Register, Abstract Statement has been lodged</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->comprising)?$scrutinyReportData[0]->comprising:'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->comprising_comment) && $scrutinyReportData[0]->comprising=="No"?$scrutinyReportData[0]->comprising_comment:'N/A'}}</td>

            </tr>
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(ii) Whether duly sworn in affidavit has been submitted by the candidate</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->duly_sworn)?$scrutinyReportData[0]->duly_sworn:'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->duly_sworn_comment)&& $scrutinyReportData[0]->duly_sworn=="No"?$scrutinyReportData[0]->duly_sworn_comment:'N/A'}}</td>

            </tr>
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(iii) Whether requisite vouchers in respect of items of election expenditure submitted</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->Vouchers)?$scrutinyReportData[0]->Vouchers:'N/A'}}</td>
            </tr>
            <tr>
                <td  style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->Vouchers_comment)&& $scrutinyReportData[0]->Vouchers=="No" ?$scrutinyReportData[0]->Vouchers_comment:'N/A'}}</td>

            </tr>
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(iv) Whether separate Bank Account opened by for election</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->seprate)?$scrutinyReportData[0]->seprate:'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->seprate_comment)&& $scrutinyReportData[0]->seprate=="No"?$scrutinyReportData[0]->seprate_comment:'N/A'}}</td>

            </tr>
            <tr>
                <td  rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(v) Whether all expenditure (except petty expenditure) routed through bank account</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->routed)?$scrutinyReportData[0]->routed:'N/A'}}</td>

            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->routed_comment) && $scrutinyReportData[0]->routed =="No"?$scrutinyReportData[0]->routed_comment:'N/A'}}</td>

            </tr>

            <tr>
                <td rowspan="5" style="border: 1px solid #5d5f61; padding: 12px;">18.</td>    
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(i) Whether the DEO had issued a notice to the candidate for rectifying the defect</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->rectifying:''}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4"> @if(!empty($scrutinyReportData[0]) && $scrutinyReportData[0]->rectifying=="Yes")
                     @if(!empty($scrutinyReportData[0]->rectifying) && $scrutinyReportData[0]->rectifying =="Yes" && !empty($download_link3))
                    <br/>
                     <a href="{{$download_link3}}"  target="_blank">Download</a>
                    <br/>
                    @endif
                    {{ !empty($scrutinyReportData[0]->notice_date) && strtotime($scrutinyReportData[0]->notice_date)>0 ? GetDateFormat($scrutinyReportData[0]->notice_date):''}}
                    @else
                    {{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->rectifying_comment:''}}
                    @endif
                </td>

            </tr>
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(ii) Whether the candidate rectified the defect</td> 
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->rectified)?$scrutinyReportData[0]->rectified:''}}</td>

            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->rectified_comment)?$scrutinyReportData[0]->rectified_comment:''}}</td>


            </tr>
            <tr>
                <td  style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">(iii) Comments of the DEO on the above, i.e. whether the defect was rectified or not</td> 
                <td style="border: 1px solid #5d5f61; padding: 12px;" colspan="4">{{!empty($scrutinyReportData[0]->comment_of_deo)?$scrutinyReportData[0]->comment_of_deo:''}}</td>

            </tr>



<!--            <tr>
               <td style="border: 1px solid #5d5f61; padding: 12px;">19.</td>
               <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Whether the items of Election Expenses Reported by the Candidate correspond with the Expenses shown in the Shadow Observation Register and Folder of Evidence.<br />If no then mention the following.</td>
               <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[0])?ucfirst($expenseunderstated[0]->status):''}}</td>
           </tr>-->
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;">19.</td>
                <td colspan="4" @if(!empty($expenseunderstated[0]->status) &&  $expenseunderstated[0]->status=="no") rowspan="2" @endif style="border: 1px solid #5d5f61; padding: 12px;">Whether the items of election expenses reported by the candidate correspond with the expenses shown in the Shadow Observation Register and Folder of Evidence<br />If No then mention the following:</td>

                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[0]->status)? ucfirst($expenseunderstated[0]->status):'N/A'}}</td>
            </tr>
            @if(!empty($expenseunderstated[0]->status) &&  $expenseunderstated[0]->status=="no")
            <tr>
                <td colspan="3" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[0]->comment)?$expenseunderstated[0]->comment:'N/A'}}</td>
            </tr>
            @else
            <tr><td colspan="3"></td></tr>
            @endif


            @if(!empty($expenseunderstated) && $expenseunderstated[0]->status =="no") 
            <tr>
                <!--<td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">&nbsp; S.No.</td>-->
                <td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">Items of expenditure</td>
                <td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">Date</td>
                <td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">Page No. of Shadow Observation Register</td>
                <td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">Mention amount as per the Shadow Observation Register/folder of evidence</td>
                <td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">Amount as per the Account Submitted by the Candidate</td>
                <td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">Amount Understated by the Candidate</td>
                 <td bgcolor="#eaebed" style="border: 1px solid #5d5f61; padding: 12px;">Description</td>
                
            </tr>
              <?php $i=1;  ?>
              @if(isset($getExpData))
            @foreach($getExpData as $item)
            @if(!empty($item->date_understated) && !empty($item->page_no_observation) ) 
            <tr>
                <!--<td style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($item->expenditure_type)? $item->expenditure_type:'N/A'}}</td>-->
                <td style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($item->expenditure_type)? $item->expenditure_type:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($item->date_understated)? date('d-m-Y',strtotime($item->date_understated)):'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($item->page_no_observation)? $item->page_no_observation:'N/A'}}</td>
                <td style='border: 1px solid #ddd; padding: 12px;'> {{!empty($item->amt_as_per_observation)? $item->amt_as_per_observation:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($item->amt_as_per_candidate)? $item->amt_as_per_candidate:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($item->amt_understated_by_candidate)? $item->amt_understated_by_candidate:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($item->description)? $item->description:'N/A'}}</td>
            </tr>
            <?php $i++; ?>
            @endif
            @endforeach
             @endif

            @endif

 
            <tr>
                <td rowspan="2" style="border: 1px solid #5d5f61; padding: 12px;">20.</td>
                <td colspan="4" @if(!empty($expenseunderstated[1]->status) &&  $expenseunderstated[1]->status=="no") rowspan="2" @endif style="border: 1px solid #5d5f61; padding: 12px;">Did the candidate produce his Register of the Accounting Election Expenditure for inspection by the Observer/RO/Authorized persons 3 times during campaign period</td>

                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[1]->status)? ucfirst($expenseunderstated[1]->status):'N/A'}}</td>
            </tr>
            @if(!empty($expenseunderstated[1]->status) &&  $expenseunderstated[1]->status=="no")
            <tr>
                <td colspan="3" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[1]->comment)?$expenseunderstated[1]->comment:'N/A'}}</td>
            </tr>
            @else
            <tr><td colspan="3"></td></tr>
            @endif
             @if(!empty($expenseunderstated[0]->status) && $expenseunderstated[0]->status=="yes" && !empty($expenseunderstated[1]->status) && $expenseunderstated[1]->status=="yes")
                                          @else
            <tr>     
                <td style="border: 1px solid #5d5f61; padding: 12px;" rowspan="6">21.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">If DEO does not agree with the facts mentioned against Row No. 19 referred to above, give the following details:-</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;"></td>
            </tr>
            <tr>    
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">(i) Were the defects notice by the DEO brought to the notice of the candidate during campaign period or during the Account Reconciliation Meeting</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[2]->status)? ucfirst($expenseunderstated[2]->status):''}}{{!empty($expenseunderstated[2]->comment)? $expenseunderstated[2]->comment:''}}</td>
            </tr>            
            
            <tr>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">(ii) If yes, then annex copies of all the notices issued relating to discrepancies with English translation (if it is in regional language) and mention date of notice</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[3]->extra_data) && strtotime($expenseunderstated[3]->extra_data)>0 && ($expenseunderstated[2]->status=="yes" || $expenseunderstated[2]->status=="Yes") ? date('d-m-Y',strtotime($expenseunderstated[3]->extra_data)):'N/A'}}
                    @if(!empty($expenseunderstated[2]->status) && ($expenseunderstated[2]->status=="yes" || $expenseunderstated[2]->status=="Yes") && !empty($download_link1))
                    <br/> 
                <a href="{{$download_link1}}"  target="_blank">Download</a>
                @endif                            
                </td>
            </tr>
           
            <tr>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">(iii) Did the candidate give any reply to the notice ?</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[4])? ucfirst($expenseunderstated[4]->status):''}}</td>
            </tr>

            <tr>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">(iv) If yes, please Annex copies of such explanation received, (with the English translation of the same, if it is in regional language) and mention date of reply</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">
                  
                     @if(!empty($expenseunderstated[4]->status) && ($expenseunderstated[4]->status=="yes" || $expenseunderstated[4]->status=="Yes" ) && !empty($download_link2))
                     <br/>
                     <a href="{{$download_link2}}"  target="_blank">Download</a>
                    @endif
                </td>
            </tr>

            <tr>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">(v) DEO's comments/observations on the candidate's explanation</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[6])? $expenseunderstated[6]->comment:''}}</td>
            </tr>
            @endif
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">22.</td>
                <td colspan="4" style="border: 1px solid #5d5f61; padding: 12px;">Whether the DEO agrees that the expenses are correctly reported by the candidate.<br />(Should be similar to Column no. 8 of Summary Reports of DEO<br>
                    <br/><br/><br/>Date:<br/></td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px;">{{!empty($expenseunderstated[7]->status)? ucfirst($expenseunderstated[7]->status):''}}<br/><br/>
                    <span style="text-decoration: overline;"> {{!empty($expenseunderstated[7]->comment)&& $expenseunderstated[7]->status=="no"? $expenseunderstated[7]->comment:'N/A'}}</span>
                    <br/><br/><br/><br/><br/>Signature<br/>(Name of the DEO)</td>
                </td>
            </tr>
             
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px;">23.</td>
                <td colspan="6" style="border: 1px solid #5d5f61; padding: 12px;"> <span style="text-decoration: underline;"> Comments, if any, by the Expenditure Observer*-</span><br/>
                    {{!empty($expenseunderstated[8]->status)? ucfirst($expenseunderstated[8]->status):''}}<br/>
                   {{!empty($expenseunderstated[8]->comment)? $expenseunderstated[8]->comment:''}}<br/><br/> 
                     @if(!empty($download_link4))
                                            <br/>
                                                <!-- <a href="{{$download_link4}}"  target="_blank">Download</a> -->
                                                    <br/> <br/>
                                            @endif
                   Date:___________________
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Signatutre of the Expenditure Observer<br/><br/></td>
            </tr>        
        </table>
        </font><br><br><br>

        <p style="float: left; font-size: 10pt;">* If the Expenditure Observer has some more facts that have not been covered in the DEO's report, he may annex separate note to that effect.<br>** The DEO scrutiny report is to be compiled by the CEO and forwarded to the Commission.<br>
            If the CEO feels like given additional comments, he or she may forward the comments separately.
        </p>
       <!--<p style="text-align: center; font-size: 14pt; font-family: Arial;"><b>Fund Given by Political Party</b></p>-->
       <font face="Arial" size="2pt">
        <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
            <tr>
                <th bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: center; color: white;" colspan="7">Fund Given By Political Party</th>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">By Cash</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;" colspan="6">{{!empty($scrutinyReportData[0]) && $scrutinyReportData[0]->political_fund_cash >0 ?number_format((float)$scrutinyReportData[0]->political_fund_cash, 2, '.', ''):'N/A'}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">By Cheque/DD/RTGS/NEFT/OTHER ELECTRONIC MEDIUM</td>
                @if(!empty($scrutinyReportData[0])&& $scrutinyReportData[0]->political_fund_checque >0))
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0]->political_fund_checque)?number_format((float)$scrutinyReportData[0]->political_fund_checque, 2, '.', ''):'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0]) && strtotime($scrutinyReportData[0]->political_fund_checque_date) > 0? date('d-m-Y',strtotime($scrutinyReportData[0]->political_fund_checque_date)):'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_bank_name:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_acct_no:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_ifsc:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_checque_num:'N/A'}}</td>

                @else
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;" colspan="6">N/A</td>
                @endif
            </tr> 
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">In Kind</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;" colspan="6">{{(!empty($scrutinyReportData[0]->political_fund_kind) && $scrutinyReportData[0]->political_fund_kind >0)?number_format((float)$scrutinyReportData[0]->political_fund_kind, 2, '.', ''):'N/A'}}</td>
            </tr>
            <tr>
                <?php
                $political_fund_cash = !empty($scrutinyReportData[0]->political_fund_cash) ? number_format((float)$scrutinyReportData[0]->political_fund_cash, 2, '.', ''): 0;
                $political_fund_kind = !empty($scrutinyReportData[0]->political_fund_kind) ? number_format((float)$scrutinyReportData[0]->political_fund_kind, 2, '.', '') : 0;
                $political_fund_checque = !empty($scrutinyReportData[0]->political_fund_checque) ? number_format((float)$scrutinyReportData[0]->political_fund_checque, 2, '.', '') : 0;
                $t= $political_fund_cash+$political_fund_kind+$political_fund_checque;
                $fundTotal =  number_format((float)$t, 2, '.', '');
                ?>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">Lump Sum Amount Given by Political Party</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;" colspan="6">
                    {{'Rs.'.($fundTotal).'/-'}}
                </td>

            </tr>
<!--            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">Grand Total of all Election Expenses:</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;" colspan="6">{{$scrutinyReportData[0]->political_fund_cash+$scrutinyReportData[0]->political_fund_kind+$scrutinyReportData[0]->political_fund_checque}}</td>
            </tr>-->
        </table>
        </font>
        <br/><br/>
        <!--<p style="text-align: center; font-size: 14pt; font-family: Arial;"><b>Fund Given By Other Sources</b></p>-->
       
        <?php
         $sumFundSource=array_sum(array_column($expensesourecefundbyitem, 'other_source_amount')); 
         $overall_amount_source = 0;
         $overall_Cash_amount=0;
         $overall_Cheque_amount=0;
         $overall_Kind_amount=0;
         $overall_Others_amount=0;
// print_r($expensesourecefundbyitem);die;
       //  $overall_amount_source = 
            foreach ($expensesourecefundbyitem as $items){           
            $overall_amount_source += !empty($items->other_source_amount) ? $items->other_source_amount : 0;
            if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="Cash" ){
                //$overall_Cash_name1='Others';
                $overall_Cash_name1=!empty($items->other_souce_name) ? $items->other_souce_name : 'Others';
                $overall_Cash_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                $overall_Cash_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
            }
             if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="Cheque" ){
                //$overall_Cheque_name1='Others';
                $overall_Cash_name1=!empty($items->other_souce_name) ? $items->other_souce_name : 'Others';
                $overall_Cheque_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                $overall_Cheque_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
            }
            if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="In Kind" ){
               
                //$overall_kind_name1='Others';
                $overall_Cash_name1=!empty($items->other_souce_name) ? $items->other_souce_name : 'Others';
                $overall_kind_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                $overall_Kind_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
            }
            if(!empty($items->other_source_payment_mode) && $items->other_source_payment_mode=="Others" ){
                $overall_other_name1='Others';
                $overall_other_name2=!empty($items->other_source_payment_mode) ? $items->other_source_payment_mode : '';
                $overall_Others_amount+=!empty($items->other_source_amount) ? $items->other_source_amount : 0;
            }
            }
        ?>
        
        <font face="Arial" size="2pt">
        <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
            <tr>
                
                <th bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px; color: white;">Name</th>
              
                  
                <th bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px; color: white;">Mode of Payment</th>
                    
                
                <th bgcolor="#e83e8c" style="border: 1px solid #5d5f61; padding: 12px; padding-top: 12px; padding-bottom: 12px; color: white;">Amount</th>
               
            </tr>
             
            @if(count($expensesourecefundbyitem)>0)
             
             @if(!empty($overall_Cash_amount))
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Cash_name1)?$overall_Cash_name1:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Cash_name2)?$overall_Cash_name2:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Cash_amount)?number_format((float)$overall_Cash_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
               @if(!empty($overall_Cheque_amount))
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Cheque_name1)?$overall_Cheque_name1:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Cheque_name2)?$overall_Cheque_name2:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Cheque_amount)?number_format((float)$overall_Cheque_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
              @if(!empty($overall_Kind_amount))
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_kind_name1)?$overall_kind_name1:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_kind_name2)?$overall_kind_name2:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Kind_amount)?number_format((float)$overall_Kind_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
              @if(!empty($overall_Others_amount))
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_other_name1)?$overall_other_name1:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_other_name2)?$overall_other_name2:'N/A'}}</td>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">{{!empty($overall_Others_amount)?number_format((float)$overall_Others_amount, 2, '.', ''):'N/A'}}</td>
                
            </tr>
            @endif
                  
            @else
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;" colspan="3"> N/A</td>
            </tr>
            @endif
            <tr>
                <td style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">Lump Sum Amount Given By Other Sources</td>
                <td colspan="2" style="border: 1px solid #5d5f61; padding: 12px; text-align: center;">
                    <?= 'Rs.' . number_format((float)$sumFundSource, 2, '.', '') . '/-'; ?>
                </td>
            </tr>
        </table><br/>
        <?php
        $totalamount=$political_fund_cash+$political_fund_kind+$political_fund_checque+$overall_amount_source;
        $grandTotal = number_format((float)$totalamount, 2, '.', '');
        ?>
        <p style="text-align: center; font-size: 14pt; font-family: Arial;">Lump Sum Grand Total:{{'Rs.' .($grandTotal).'/-'}}</p><br/>
        <p style="text-align: center; font-size: 14pt; font-family: Arial;"><b>Dated:{{!empty($scrutinyReportData[0]->report_submitted_date) && strtotime($scrutinyReportData[0]->report_submitted_date)>0 ? date('d-m-Y',strtotime($scrutinyReportData[0]->report_submitted_date)):'N/A'}}</b></p>
        </font>
        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
        <table style="width:100%; border-collapse: collapse; margin-top: 30px;" align="center" border="1" cellpadding="5">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
                </tr>
            </tbody>
        </table>
    </body>
</html>