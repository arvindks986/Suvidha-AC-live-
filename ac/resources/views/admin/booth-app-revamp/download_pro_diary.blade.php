<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{!! $heading_title !!}</title>

</head>

<body>
    <!--HEADER STARTS HERE-->
    <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
        <thead>
            <tr>
                <th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img
                        src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt="" width="100" border="0" />
                </th>
                <th style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                    SECRETARIAT OF THE<br>
                    ELECTION COMMISSION OF INDIA<br>
                    Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>
                </th>
            </tr>
            <tr>
              <th></th>
    <th align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</th>
     </tr>
        </thead>
    </table>
    <!--HEADER ENDS HERE-->
    <style type="text/css">
        .table-strip {
            border-collapse: collapse;
        }

        .table-strip th,
        .table-strip td {
            text-align: left!important; padding:4px;
        }

        .table-strip tr:nth-child(odd) {
            background-color: #f5f5f5;
        }
		.table-strip th:first-child{text-align:left;}
    </style>






    <table class="table-strip" style="width: 100%;" border="1" align="left">
        <tr>
          <td align="center">ANNEXURE 56
    <br>
PRESIDING OFFICER’S DIARY
</td>
</tr>
<tr>
  <td>

1. Name of the constituency (in block letters): {{$ac_name}}<br>
2. Date of poll: {{$data_of_polling}}<br>
3. Number and Name of the polling station: {{$ps_name}}<br>
</td>
</tr>
<tr>
  <td>Whether located in: {{$diary['building']}}<br>
</td>
</tr>
<tr>
  <td>
4. Number of Polling Officers recruited locally, if any: {{$diary['officers_recruited_locally']}}
</td>
</tr>
<tr>
  <td>
5. Appointment polling officer made in the absence of duly appointed polling officer, if any, and the reasons of such appointment:{{$diary['reason_for_such_appointment']}}
</td>
</tr>
<tr>
  <td>
6. Electronic Voting Machine –<br>
(i) Number of Control Units used:<br>
(ii) S. No.(s) of Control Units used:<br>
(iii) Number of balloting units used:<br>
(iv) S. No.(s) of balloting units used:<br>
</td>
</tr>
<tr>
  <td>
7. (i) Number of paper seals used:<br>
(ii) S. Nos. of paper seals used:<br>
</td>
</tr>
<tr>
  <td>
7A. (i) Number of special tags supplied:<br>
(ii) S. No.(s) of special tags supplied:<br>
(iii) Number of special tags used:<br>
(iv) S. No.(s) of special tags used:<br>
(v) S. No.(s) of special tags returned as unused:<br>
</td>
</tr>
<tr>
  <td>
7B. (i) Number of Strip Seals supplied:
(ii) S. No.(s) of Strip Seals supplied:
(iii) Number of Strip Seals used:
(iv) S. No.(s) of Strip Seals used:
(v) S. No.(s) of Strip Seals returned as unused:
</td>
</tr>
<tr>
  <td>
7C. Applicable in the polling stations where VVPAT system is used<br>
(i) No. of Printers used:<br>
(ii) S. No.(s) of printer(s):<br>
</td>
</tr>
<tr>
  <td>
8. Number of candidates who had appointed polling agents at the polling stations:<br>
</td>
</tr>
<tr>
  <td>
9.(i) Number of polling agents present at the commencement of poll:<br>
(ii) Number of polling agents who arrived late:<br>
(iii) Number of polling agents present at the close of the poll:<br>
</td>
</tr>
<tr>
  <td>
10.(i) Total number of voters assigned to the polling station:<br>
(ii) Number of electors allowed to vote according to marked copy of the electoral roll:<br>
(iii) Total number of electors as entered in the Register of Voters (Form 17A):<br>
(iv) Number of votes recorded as per the voting machine:<br>
(v) Number of voters deciding not to record vote, if any:<br>
Signature of the first Polling Officer Signature of Polling Officer<br>
In-charge of Register of Voters<br>
</td>
</tr>
<tr>
  <td>
11. Number of electors who voted –<br>
Men: {{$total_voter_man}}<br>
Women: {{$total_voter_woman}}.<br>
Third Gender: {{$total_voter_third}}<br>
Total: {{$total_voter}}<br>
</td>
</tr>
<tr>
  <td>
12. Challenged vote –<br>
Number allowed……………………<br>
Number rejected……………………<br>
Amount forfeited Rs……………….<br>
</td>
</tr>
<tr>
  <td>
13. Number of persons who have voted on production of Election Duty Certificate (EDC):{{$diary['total_voted_on_duty_certificate']}}
</td>
</tr>
<tr>
  <td>
13A. Number of overseas electors who voted:{{$diary['no_of_overseas_voted_electors']}}
</td></tr>
<tr>
  <td>
14. Number of electors who voted with the help of companions:{{$diary['no_of_electors_help_of_companions']}}
</td>
</tr>
<tr>
  <td>
15. Number of voters cast through proxy:{{$diary['no_of_voters_proxy']}}
</td>
</tr>
<tr>
  <td>
16. Number of tendered votes:{{$diary['no_of_tenderd_votes']}}
</td>
</tr>
<tr>
  <td>
17. No. of electors: {{$diary['no_of_elector']}}<br>
(a) From whom declarations as to their age obtained: {{$diary['declared_age_obtained']}}<br>
(b) Who refused to give such declaration: {{$diary['refused_to_declarations']}}<br>
</td>
</tr>
<tr>
  <td>
18. Whether it was necessary to adjourn the poll and if so, the reasons for such adjournment:<br>
</td>
</tr>
<tr>
  <td>
19. Number of votes cast in every two hours –<br>
From 7 a.m. to 9 a.m: {{$is_time_slap[0]}}<br>
From 9 a.m. to 11 a.m: {{$is_time_slap[1]}}<br>
From 11 a.m. to 1 p.m: {{$is_time_slap[2]}}<br>
From 1 p.m. to 3 p.m: {{$is_time_slap[3]}}<br>
From 3 p.m. to 5 p.m: {{$is_time_slap[4]}}<br>
(Necessary changes may be made depending on the hours fixed for commencement and close of poll)<br>
</td>
</tr>
<tr>
  <td>
20.(a) Number of slips issued at the closing hour of the poll to electors standing in the queue: {{$diary['number_of_slips_closing_hour']}}<br>
(b) Time at which poll finally closed after the last such elector cast his/her vote:<br>
</td>
</tr>
<tr>
  <td>
21. Electoral offences with details:<br>
Number of cases of –<br>
(a) Canvassing within one hundred meters of the polling station:<br>
(b) Impersonation of voters:<br>
(c) Fraudulent defacing, destroying or removal of the list of notice or other documents at the polling station:<br>
(d) Bribing of voters:<br>
(e) Intimidation of voters and others persons:<br>
(f) Booth capturing:<br>
</td>
</tr>
<tr>
  <td>
22. Was the poll interrupted or obstructed by –<br>
(1) Riot:<br>
(2) Open violence:<br>
(3) Natural calamity:<br>
(4) Booth capturing:<br>
(5) Failure of voting machine:<br>
(6) Any other cause:<br>
Please give details of the above.
</td>
</tr>
<tr>
  <td>
23. Was the poll vitiated by any voting machine used at the polling station having been –<br>
(a) Unlawfully taken out of the custody of the Presiding Officer:<br>
(b) Accidently or intentionally lost or destroyed:<br>
(c) Damaged or tampered with:<br>
</td>
</tr>
<tr>
  <td>
Please give details.
<tr>
  <td>
24. Serious complaints, if any, made by the candidate/agents:
</td>
</tr>
<tr>
  <td>
25. Number of cases of breach of law and order:
</td>
</tr>
<tr>
  <td>
26. Report of mistakes and irregularities committed, if any, at the polling station:
</td>
</tr>
<tr>
  <td>
27. Whether the declarations have been made before the commencement of the poll and if necessary during the course of poll when a new voting machine is used and at the end of the poll as necessary:<br>
Place:<br>
Date:<br>
Presiding Officer<br>
This diary should be forwarded to the Returning Officer with the voting machine, Visit Sheet,<br>
16-Point Observer’s Report and other sealed papers.<br>
</td>
</tr>

    </table>
    <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
        <tbody>
            <tr>
                <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>