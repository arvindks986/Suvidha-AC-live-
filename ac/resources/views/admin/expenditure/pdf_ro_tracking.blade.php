<?php
$pc_no = !empty($profileData[0]) ? $profileData[0]->pc_no : '';
$st_code = !empty($profileData[0]) ? $profileData[0]->st_code : '';
$party_id = !empty($profileData[0]) ? $profileData[0]->party_id : '';

 
$candiatePcName = !empty($candiatePcName) ? $candiatePcName->AC_NAME : 'N/A';
$stateName = getstatebystatecode($st_code);
$stateName = !empty($stateName) ? $stateName->ST_NAME : 'N/A';
$partyname = getpartybyid($party_id);
$partyname = !empty($partyname) ? $partyname->PARTYNAME : 'N/A';

$candidateId = !empty($profileData[0]) ? $profileData[0]->candidate_id : 0;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Tracking Status</title>
        <!--HEADER STARTS HERE-->

        <!--HEADER ENDS HERE-->
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
                    <th  style="width:49%" align="left" style="border-bottom: 1px solid #d7d7d7;">
					<!--<img src="<?php echo url('/'); ?>/admintheme/images/logo/suvidha-logo.png" alt=""  width="100" border="0"/>-->
					</th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px solid #d7d7d7;">
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
                    <table  style="width:100%">ST_NAME
                        <tbody>
                            <tr>
                                <td><strong>Candidate Status Reports</strong></td>
                            </tr>
                            <tr>  
                                <td><strong>State:</strong> {{$stateName}}</td>
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
        <div class=" text-left" style="width:100%;">
            <!--SELECT CANDIDATE-->
            <div  class="collapse show">
                <p style="text-align: center; font-size: 14pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 10px;"><b>Candidate Detail</b></p>
                <table style="width:100%; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
                    <tbody>
                        @if(count($profileData)>0)
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="60%">Name</td>
                            <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="40%">{{!empty($profileData[0]) ? $profileData[0]->cand_name:'--'}}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px;">Phone/Mobile no</td>
                            <td style="border: 1px solid #454546; padding: 12px;">{{!empty($profileData[0]->cand_mobile) ? $profileData[0]->cand_mobile:'N/A'}}</td>
                        </tr>
                       
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px;">AC Name</td>
                            <td style="border: 1px solid #454546; padding: 12px;">{{$candiatePcName}}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Party Name</td>
                            <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{$partyname}}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px;">Residence Address</td>
                            <td style="border: 1px solid #454546; padding: 12px;">{{!empty($profileData[0]) ? $profileData[0]->candidate_residence_address:'N/A'}}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Election Type</td>
                            <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">
                                <!--{{!empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE:'N/A'}}-->
                                AC
                            </td>
                        </tr>                
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px;">State</td>
                            <td style="border: 1px solid #454546; padding: 12px;">{{$stateName}}</td>
                        </tr>

                        @else
                        <tr>
                            <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" colspan="2">No Record Available</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div><br />
            <div  class="collapse show">
                <p style="text-align: center; font-size: 14pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 10px;"><b>ECI Status</b></p> 
                <table style="width:100%;  border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
            <tbody>

                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="60%">Result Declaration Date</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="40%">{{!empty($ReportSingleData['date_of_declaration'])?$ReportSingleData['date_of_declaration']:''}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Return Type</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['return_status'])?$ReportSingleData['return_status']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Nature of Default in A/C</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['default_nature_text'])?$ReportSingleData['default_nature_text']:'N/A'}} </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of Reciept of DEO's scrutiny report from the CEO/DEO </td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['date_of_receipt_eci'])?$ReportSingleData['date_of_receipt_eci']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Date of any additinal information has been sought from the DEO/Candidate  </td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['date_of_sending_additional_info_eci'])?$ReportSingleData['date_of_sending_additional_info_eci']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of issuance of notice (within 6 months from the receipt of DEO's srcutiny report)    </td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['date_of_issuance_notice'])?$ReportSingleData['date_of_issuance_notice']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Date of Receipt Supplementary Report on Notice from DEO/CEO ( within 5 days if reply received & 25 days if reply not recieved from the candiddate) </td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['date_sending_supplimentary_eci'])?$ReportSingleData['date_sending_supplimentary_eci']:'N/A'}}</td>
                </tr>                
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Whether acknowledge from the candidate is attached with supplementary report</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['date_of_sending_additional_info_ceo'])?$ReportSingleData['acknowldge_from_the_candidate_eci']:'N/A'}}</td>
                </tr>


                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Final Action</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['final_action'])?$ReportSingleData['final_action']:'N/A'}}</td>
                </tr>
                

                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Comment By ECI</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['comment_by_eci'])?$ReportSingleData['comment_by_eci']:'N/A'}}</td>
                </tr>

            </tbody>
        </table>
            </div><br />
                <div  class="collapse show">
                <p style="text-align: center; font-size: 14pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 10px;"><b>RO Status</b></p> 
                <table style="width:100%; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
            <tbody>

                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="60%">Result Declaration Date</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="40%">{{!empty($ReportSingleData['date_of_declaration'])?$ReportSingleData['date_of_declaration']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Return Type</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['return_status'])?$ReportSingleData['return_status']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Nature of Default in A/C</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['default_nature_text'])?$ReportSingleData['default_nature_text']:'N/A'}} </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of Sending DEO'S Scrutiny Report to the commission</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['date_of_sending_deo'])?$ReportSingleData['date_of_sending_deo']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Date of Receipt of DEO's Scrunity Report</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['date_of_sending_ceo'])?$ReportSingleData['date_of_sending_ceo']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Whether any additional information has been sought by the commission from the DEO</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['date_of_sending_additional_info_ceo'])?$ReportSingleData['date_of_sending_additional_info_ceo']:'N/A'}}</td>
                </tr>
                 <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of receipt and service of notice </td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['date_of_receipt_notice_service'])?$ReportSingleData['date_of_receipt_notice_service']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Current Status</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['current_status_text'])?$ReportSingleData['current_status_text']:'N/A'}}</td>
                </tr>                
                
                <tr> 
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Comment By CEO</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['comment_by_ceo'])?$ReportSingleData['comment_by_ceo']:'N/A'}}</td>
                </tr>

            </tbody>
        </table>
            </div><br />
            <div  class="collapse show">
                <p style="text-align: center; font-size: 14pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 10px;"><b>RO Status</b></p> 
                 <table style="width:100%; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
            <tbody>

                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="60%">Result Declaration Date</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;" width="40%">{{!empty($ReportSingleData['date_of_declaration'])?$ReportSingleData['date_of_declaration']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Return Type</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['return_status'])?$ReportSingleData['return_status']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Nature of Default in A/C</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{!empty($ReportSingleData['default_nature_text'])?$ReportSingleData['default_nature_text']:'N/A'}} </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of Sending DEO'S Scrutiny Report to ECI through the CEO</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{(!empty($ReportSingleData['date_of_sending_deo']) && strtotime($ReportSingleData['date_of_sending_deo'])>0)?$ReportSingleData['date_of_sending_deo']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">In Case of Default Date of Receipt of ECI Notice</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{(!empty($ReportSingleData['date_of_receipt']) && strtotime($ReportSingleData['date_of_receipt'])>0)?$ReportSingleData['date_of_receipt']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of Service of ECI Notice</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{(!empty($ReportSingleData['date_of_service']) && strtotime($ReportSingleData['date_of_service'])>0)?$ReportSingleData['date_of_service']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Date of Seeking Additional Information</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{(!empty($ReportSingleData['date_of_sending_additional_info']) && strtotime($ReportSingleData['date_of_sending_additional_info'])>0)?$ReportSingleData['date_of_sending_additional_info']:'N/A'}}</td>
                </tr>                
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of Sending Acknowledgement to ECI</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{(!empty($ReportSingleData['date_of_sending_ack_eci']) && strtotime($ReportSingleData['date_of_sending_ack_eci'])>0)?$ReportSingleData['date_of_sending_ack_eci']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">Date of Receipt of Reply-cum-representation from the candidate on ECI Notice</td>
                    <td style="border: 1px solid #454546; padding: 12px; background-color: #e8e8ea;">{{(!empty($ReportSingleData['date_of_receipt_represetation']) && strtotime($ReportSingleData['date_of_receipt_represetation'])>0)?$ReportSingleData['date_of_receipt_represetation']:'N/A'}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Date of Sending Supplementary Report on ECI Notice if any ,together of acknowledge from the RO/DEO</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{(!empty($ReportSingleData['date_sending_supplimentary']) && $ReportSingleData['date_sending_supplimentary']>0)?$ReportSingleData['date_sending_supplimentary']:'N/A'}}</td>
                </tr>              

                 <tr>
                    <td style="border: 1px solid #454546; padding: 12px;">Comment By RO</td>
                    <td style="border: 1px solid #454546; padding: 12px;">{{!empty($ReportSingleData['comment_by_ro'])?$ReportSingleData['comment_by_ro']:'N/A'}}</td>
                </tr>              


            </tbody>
        </table>
            </div><br />
        </div>
        <br/><br/><br/> <br/> 
        <table style="width:100%; border-collapse: collapse; margin-top: 30px;" align="center" border="1" cellpadding="5">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
                </tr>
            </tbody>
        </table>
    </body>
</html>