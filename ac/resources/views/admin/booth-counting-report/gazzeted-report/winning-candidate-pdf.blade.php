<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
        <meta charset="utf-8">
        <title>Winning Candidate</title>
        <style>
            @page {
                footer: page-footer;
                header: page-header;
            }
            #mid-sec th{
                font-family:freeserif; font-size:20px;
            }
            #lstsec tr th{
                font-family:freeserif;
               
                font-weight: 400;
            }
            .hr{
	     border:0;
     	      border-top:1px solid #000;
		 padding-top:5px; 
		 font-weight:bold;
		 border-bottom:1px solid #000;		 
	}
        </style>
    </head>
    <body>
    <htmlpageheader  name="page-header">
        <div><span>{PAGENO}</span> </div>

    </htmlpageheader>
                        <table border="0" id="mid-sec">  
                            <tbody>
                                <tr>
                                    <th  style="width:49%;" align="left">
                                        <strong><span style="border-bottom:1px solid #000;font-family:freeserif;">रजि ट्री सं. डी .एल.-33004/99</span></strong>
                                    </th>
                                    <th  style="width:49%;" align="right">
                                        <strong><span style="border-bottom:1px solid #000;font-family:freeserif;">REGD. NO. D. L.-33004/99</span></strong>
                                    </th>			 
                                </tr>  
                                <tr>
                                    <th colspan="2" align="center" ><img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt="logo"/></th>
                                </tr>
                                <tr>
                                    <th colspan="2" align="center" ><span style="font-family:freeserif;">असाधारण</span></th>
                                </tr>
                                <tr>
                                    <th colspan="2" align="center">EXTRAORDINARY</th>
                                </tr>		   
                                <tr>
                                    <th colspan="2" align="center" ><span style="font-family:freeserif;">भागII—खण्ड 3—उप-खण्ड्(iii)<br/>PART II—Section 3—Sub-section (iii)</span></th>
                                </tr>
                                <tr>
                                    <th colspan="2" align="center" ><span style="font-family:freeserif;">प्राधिकार से प्रकाशित</span></th>
                                </tr>
                                <tr>
                                    <th colspan="2" align="center" ><span style="font-family:freeserif;">PUBLISHED BY AUTHORITY</span></th>
                                </tr>
                                <tr>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                </tr>	
                                <tr>
                                    <th align="left"><span style="font-family:freeserif;">सं.123]</span> <br/> No.123]</th>
                                   
                                    <th align="right" width="100%"><span style="font-family:freeserif;">नई दिल्ली, मंगलवार,  फरवरी 11,  2020 </span>
                                        <br/>NEW DELHI,TUESDAY,  FEBRUARY 11,  2020</th>		
                               
                                </tr>	
                                 <tr>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                </tr>
                            </tbody>
                        </table> 

                    
                        <table width="100%" align="center" class="">
                            <tbody>
                                <tr><td colspan="2" align="center"><h4 style="font-weight:bold;">ELECTION COMMISSION OF INDIA</h4></td></tr>
                                <tr><td colspan="2" align="center"><h4 style="font-weight:bold;">NOTIFICATION</h4></td></tr>
                                <tr><td colspan="2" align="center">New Delhi, the 11th February, 2020</td></tr>
                            </tbody>
                        </table>
                        <br>
                        <table width="100%" align="justify">
                            <tbody>
                                <tr><td><b>O.N. 136(E).</b>—Whereas, in pursuance of the Notifications No. H.11024(1)/2019-Leg-II, issued
                                        by the President of India on 11th February 2020, under sub-section (2) of Section 14 of the Representation of
                                        the People Act, 1951 (43 of 1951), a Assembly Constituency has been held for the purpose of constituting a new
                                        House of the People; and </td></tr>
                            </tbody>
                        </table>
                        <table width="100%" align="justify">
                            <tbody>
                                <tr><td>Now, therefore, in pursuance of Section-73 of the Representation of the People Act, 1951 (43 of
                                        1951), the Election Commission of India hereby notifies the names of the members elected in respect of
                                        those Constituencies, along with their party affiliations, if any, in the SCHEDULE annexed to this
                                        Notification.
                                    </td></tr>
                            </tbody>
                        </table>
                        <table align="center">
                            <tbody><tr><td align="center"><h2>SCHEDULE</h2></td></tr>
                            </tbody></table>
                        <table width="100%" align="center" cellspacing="0" cellpadding="8" style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <th style="border: 1px solid black;border-collapse: collapse;">Sl. No</th>
                                    <th style="border: 1px solid black;border-collapse: collapse;">No. and Name of Assembly Constituency</th>
                                    <th style="border: 1px solid black;border-collapse: collapse;">Name of the Elected Member</th>
                                    <th style="border: 1px solid black;border-collapse: collapse;">Party Affiliation (if any)</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $i = 1;
                                if (count($engarr) > 0) {
                                    foreach ($engarr as $k => $v) {
                                        ?>
                                        <tr style="border: 1px solid black;border-collapse: collapse;"><td style="border: 1px solid black;border-collapse: collapse;" colspan="4" align="center"><b><?php echo $k; ?></b></td></tr>
        <?php foreach ($engarr[$k] as $value) {
            ?>

                                            <tr style="border: 1px solid black;border-collapse: collapse;">
                                                <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $i; ?></td>
                                                <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $value['ac_name']; ?></td>
                                                <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $value['lead_cand_name']; ?></td>
                                                <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $value['lead_cand_party']; ?></td>
                                            </tr>
            <?php $i++;
        }
    }
} else { ?>
                                    <tr style="border: 1px solid black;border-collapse: collapse;"><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
<?php } ?>
                            </tbody>

                        </table>



</body>
</html>


