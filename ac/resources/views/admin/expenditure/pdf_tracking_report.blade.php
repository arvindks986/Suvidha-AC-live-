<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Candidate Scrutiny Reports</title>
        <!--HEADER STARTS HERE-->

        <!--HEADER ENDS HERE-->
        <style type="text/css">
            .table-strip{border-collapse: collapse;}
            .table-strip th,.table-strip td{text-align: center;}
            .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
        </style>
    </head>
    <body>

        <table style="width:98%; border: 1px solid #000;" border="0" align="center" cellpadding="5">
            <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/images/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br> 
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
            </thead>
        </table>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
            <tr>
                <td  style="width:49%;">
                    <table  style="width:100%">ST_NAME
                        <tbody>
                            <tr>
                                <td><strong>Candidate Scrutiny Reports</strong></td>
                            </tr>
                            <tr>  
                                <td><strong>State:</strong> {{!empty($stateDetail->ST_NAME)?$stateDetail->ST_NAME:''}}</td>
                            </tr>


                        </tbody>
                    </table>  
                </td>
                <td  style="width:49%">
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <td align="right"><strong>Date of Print:</strong> {{ date('d.m.Y h:i a') }}</td>
                            </tr>


                            <tr>  
                                <td align="right">&nbsp;</td>
                            </tr> 
                        </tbody>
                    </table>
                </td>
            </tr>
            
        </table>
		   <?php 
           $date_of_declarationnot = !empty($resultDeclarationDate['start_result_declared_date']) ? date('d-m-Y',strtotime($resultDeclarationDate['start_result_declared_date'])):'N/A';
           $last_date_prescribed_acct_lodgenot = !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('d-m-Y',strtotime($resultDeclarationDate['start_result_declared_date'].' + 30 days ')):'';

                 $date_of_declaration=!empty($candList[0]->date_of_declaration) && strtotime($candList[0]->date_of_declaration)>0 ? date('d-m-Y',strtotime($candList[0]->date_of_declaration)):$date_of_declarationnot;
                 $last_date_prescribed_acct_lodge = !empty($candList[0]->last_date_prescribed_acct_lodge) && strtotime($candList[0]->last_date_prescribed_acct_lodge) > 0 ?date('d-m-Y', strtotime($candList[0]->last_date_prescribed_acct_lodge)) : 
                 $last_date_prescribed_acct_lodgenot;


				?>

        <div class=" text-left" style="width:100%;">

            <div class="collapse show">
                <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #ff9800; color: #ffffff; padding: 6px;"><b>SUMMARY REPORT OF DEO FOR EACH CONSTITUENCY ON LODGING OF ELECTION EXPENSES ACCOUNTS BY CANDIDATES</b></p>

                <div class="row"> 
                    <div class="col">
                        <p style="font-size: 11pt; font-family: Arial;">(a) No. and Name of Assembly Constituency: <Strong>{{!empty($acdetail->AC_NO) ? $acdetail->AC_NO : ''}}-{{!empty($acdetail->AC_NAME) ? $acdetail->AC_NAME : ''}}</Strong></p>
                        <p style="font-size: 11pt; font-family: Arial;">(b) Total No. Contesting Candidates: <strong>{{count($candList)}}</strong></p>
                        <p style="font-size: 11pt; font-family: Arial;">(c) State and District: <strong>{{!empty($stateDetail->ST_NAME)?$stateDetail->ST_NAME:''}}&nbsp;&nbsp;{{!empty($districtDetails->DIST_NAME)? $districtDetails->DIST_NAME:''}}</strong></p>
                        <p style="font-size: 11pt; font-family: Arial;">(d) Date of Declaration of Result of Election/Bye-election: <strong>{{$date_of_declaration}}</strong></p>
                        <p style="font-size: 11pt; font-family: Arial;">(e) Last Date of Lodging Accounts: <strong>{{$last_date_prescribed_acct_lodge}}</strong></p>
                        <p style="font-size: 11pt; font-family: Arial;">(f) Name of the Elected Candidate:<strong>{{!empty($winn_data->lead_cand_name)? $winn_data->lead_cand_name:'N/A'}}</strong></p>
                    </div>
                </div>
                <br />
                <table style="width:100%; font-size: 8pt; font-family: Arial; border-collapse: collapse; border: 1px solid #7c7d80; color: #000000" border="0" align="center" cellpadding="5" bgcolor="#f5f6f8">
                    <thead class="text-center">
                        <tr>
                            <th style="border: 1px solid #363637; padding: 10px;">1.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">2.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">3.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">4.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">5.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">6.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">7.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">8.</th>
                            <th style="border: 1px solid #363637; padding: 10px;" colspan="2">9.</th>
                            <th style="border: 1px solid #363637; padding: 10px;" colspan="2">10.</th>
                            <th style="border: 1px solid #363637; padding: 10px;">11.</th>
                        </tr>
                        <tr>  
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Sr .No.</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Name of the Candidate and Party Affiliation</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Due Date of Lodging of Account</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Date of Lodging of Account by the Candidate</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Whether Lodged in the Prescribed Format (Yes or No)</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Whether Lodged in the manner required by Law (Yes or No)</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Grand Total of the Expenses Incurred/Authorized by the Candidate/Agent (as mentioned in <em>Part-II</em> of Abstract Statement)</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Whether the DEO agrees with the amount shown by the candidate against all items of expenditure (Should be similar to point no. <b>22</b> of DEO's Scrutiny Report i.e. Annexure -C3)</th>
                            <th style="border: 1px solid #363637; padding: 10px;" colspan="2">Total Expenses incurred by the Party (As reported in Part-III of Abstract Statement)</th>
                            <th style="border: 1px solid #363637; padding: 10px;" colspan="2">Total Expenses incurred by others/entities as reported in Part-III of Abstract Statement</th>
                            <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Remarks of the Expenditure Observer</th>          
                        </tr>
                        <tr>
                            <th style="border: 1px solid #363637; padding: 10px;">Lump Sum Amount in cash or cheque given to candidate by each Political Party</th>
                            <th style="border: 1px solid #363637; padding: 10px;">Grand Total of other Expenses kind by the Political Party</th>
                            <th style="border: 1px solid #363637; padding: 10px;">Lump Sum Amount in cash/cheque given to the candidate (and mention names of donors)</th>
                            <th style="border: 1px solid #363637; padding: 10px;">Grand Total of other expenses in kind incurred for the candidate</th>    
                        </tr>
                    </thead>
                    <tbody>
                    

                        @if(count($candList)>0)
                        @foreach($candList as $key =>$item)
                        <tr>
                             
                                    
                                   
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{ ++$key }}</td>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{$item->cand_name}}-{{$item->PARTYNAME}}</td>
                             
                                     <!-- <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($item->last_date_prescribed_acct_lodge) && strtotime($item->last_date_prescribed_acct_lodge)>0? date('d-m-Y',strtotime($item->last_date_prescribed_acct_lodge)):'N/A' }}</td> -->
                                     <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{$last_date_prescribed_acct_lodge}}</td>
                                     <td  style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($item->date_orginal_acct) && strtotime($item->date_orginal_acct)>0 && !empty($item->candidate_lodged_acct) && $item->candidate_lodged_acct =="Yes"? date('d-m-Y',strtotime($item->date_orginal_acct)):'Not Lodged' }}</td>

                                    <!-- <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($item->last_date_prescribed_acct_lodge) && strtotime($item->last_date_prescribed_acct_lodge)>0? date('d-m-Y',strtotime($item->last_date_prescribed_acct_lodge)):'N/A' }}</td> -->
                                    <?php                                  
                                    $cc_amt=!empty($item->political_fund_cash)?$item->political_fund_cash:0;
                                    $cq_amt=!empty($item->political_fund_checque)?$item->political_fund_checque:0;
                                    ?>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($item->candidate_lodged_acct)?$item->candidate_lodged_acct:'N/A'}}</td>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($item->rp_act)?$item->rp_act:'N/A'}}</td>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{$item->grand_total_election_exp_by_cadidate}}</td>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($item->status)?$item->status:'N/A'}} </td>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($cc_amt+$cq_amt) && ($cc_amt+$cq_amt)>0? $cc_amt+$cq_amt:'N/A'}}</td>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($item->political_fund_kind)?$item->political_fund_kind:'N/A'}} </td>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">
                                    
                                    {{!empty($item->other_source_amt_cc) && ($item->other_source_amt_cc)>0?$item->other_source_amt_cc:'N/A'}}
                                    
                                    </td>
                                    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">
                                    {{!empty($item->other_source_amt_kind)?$item->other_source_amt_kind:'N/A'}} 
                                   
                                    
                                     </td>
                                     <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""> {{!empty($item->comment_9)?$item->comment_9:'N/A'}}</td> 
                        </tr>
                        @endforeach
                        @endif
                        </tr>

                    </tbody>
                </table><br />
                 <table style="width:100%; font-size: 10pt; font-family: Arial; border-collapse: collapse; border: 1px solid #e8e8ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
                            <tr>
                                <td class="bdr-none pl-5 pr-5 pt-3">
                                    <p>Comments of the Expenditure Observer, If any, &nbsp;&nbsp; ______________ &nbsp;&nbsp; Signature of the DEO &nbsp;&nbsp; ____________________
                                    </p>
                                </td>
                            </tr>
                        </table>
<!--                <table style="width:100%; font-size: 10pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Date</td>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Signature of the Candidate</td>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Place</td>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Name</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">03-06-2019</td>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">&nbsp;</td>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Andhra Pradesh</td>
                            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Anumula Vamsikrishna</td>
                        </tr>
                    </tbody>
                </table> -->
            </div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
        </div>

        <table style="width:98%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
                </tr>
            </tbody>
        </table>
    </body>
</html>