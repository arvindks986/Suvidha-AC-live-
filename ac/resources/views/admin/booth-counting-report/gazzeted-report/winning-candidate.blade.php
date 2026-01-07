@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha AC')
@section('bradcome', 'Winning Candidate Details')
@section('content')
<style type="text/css">
.Pdf-container {width:800px; background: #fff; margin: 0 auto;}
section.pdfDoc table { font-family: serif;}
section.pdfDoc table.table td{padding:5px;border: 1px solid #000;border-top: 0;}
section.pdfDoc table td {padding: 4px 0;font-size: 18px;}
section.pdfDoc table h1{font-size: 36px;  font-weight: 700;}
section.pdfDoc table h2 {font-size: 24px;  font-weight: 800;   color: #000;}
section.pdfDoc table th {font-size: 20px;  font-weight: 800;}
.showname {font-size: 18px; margin-left: 10px; margin-right:10px; font-weight:bold;    text-decoration-style: solid; border-bottom: 1px solid #000;}
</style>
<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/FileSaver.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.wordexport.js') }}"></script>

<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
 <div class="row">
  
     <div class="col-md-12  pull-right text-right report_section">
         <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('/eci/booth-counting/winning-candidate-list-pdf')}}" title="Download PDF" >Export PDF</a></span>
     </div>

 </div>
</div>
</section>

<section class="pdfDoc" id="export-content">
	<div class="container-fluid">
		<div class="row">
		<div class="Pdf-container card pt-4 pb-4">
		<div class="card-body">
                    <table border="0" id="mid-sec">  
                            <tbody>
                                <tr width="100%">
                                    <td align="left" style="width: 49%;">
                                        <span style="border-bottom:1px solid #000;"><b>रजि ट्री सं. डी .एल.-33004/99</b></span>
                                    </td>
                                    <td align="right">
                                        <span style="border-bottom:1px solid #000;"><b>REGD. NO. D. L.-33004/99</b></span>
                                    </td>			 
                                </tr>  
                                <tr>
                                    <th colspan="2" align="center" ><img src="<?php echo url('/'); ?>/img/pdf-logo.jpg" alt="logo"/></th>
                                </tr>
                                <tr width="100%">
                                    <td colspan="2" align="center" ><b>असाधारण</b></td>
                                </tr>
                                <tr width="100%">
                                    <td colspan="2" align="center"><b>EXTRAORDINARY</b></td>
                                </tr>		   
                                <tr width="100%">
                                    <td colspan="2" align="center" ><b>भागII—खण्ड 3—उप-खण्ड्(iii)<br/>PART II—Section 3—Sub-section (iii)</b></td>
                                </tr>
                                <tr width="100%">
                                    <td colspan="2" align="center" ><b>प्राधिकार से प्रकाशित</b></td>
                                </tr>
                                <tr width="100%">
                                    <td colspan="2" align="center" ><b>PUBLISHED BY AUTHORITY</b></td>
                                </tr>
                                <tr>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                </tr>	
                                <tr width="100%">
                                    <td align="left"><b>सं.123]</span> <br/> No.123]</b></td>
                                   
                                    <td align="right" width="100%"><b>नई दिल्ली, मंगलवार,  फरवरी 11 2020
                                            <br/>NEW DELHI,TUESDAY,  FEBRUARY 11,  2020</b></td>		
                               
                                </tr>	
                                 <tr>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                    <th colspan="2" align="center" ><hr class="hr"></th>
                                </tr>
                            </tbody>
                        </table> 

		</div>	
                <div class="card-body">
			<table width="100%" align="center" class="">
				<tbody>
					<tr><td colspan="2" align="center"><h4 style="font-weight:bold;">ELECTION COMMISSION OF INDIA</h4></td></tr>
					<tr><td colspan="2" align="center"><h4 style="font-weight:bold;">NOTIFICATION</h4></td></tr>
					<tr><td colspan="2" align="center">New Delhi, the 11th FEBRUARY, 2020</td></tr>
				</tbody>
			</table>
			<br>
                        <table width="100%" align="justify">
                            <tbody>
                                <tr><td><b>O.N. 136(E).</b>—Whereas, in pursuance of the Notifications No. H.11024(1)/2019-Leg-II, issued
                                        by the President of India on 11th FEBRUARY 2020, under sub-section (2) of Section 14 of the Representation of
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
			<table width="100%" align="center" cellspacing="0" cellpadding="8" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>No. and Name of Assembly Constituency</th>
                                <th>Name of the Elected Member</th>
                                <th>Party Affiliation (if any)</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                                    <?php 
                                    $i=1;
                                    if(count($engarr)>0){
                                        foreach($engarr as $k=>$v){?>
                                            <tr><td colspan="4" align="center"><b><?php echo $k;?></b></td></tr>
                                           <?php foreach($engarr[$k] as $value){
                                    ?>
                                        
                                        <tr>
                                            <td><?php echo $i;?></td>
                                            <td><?php echo $value['ac_name'];?></td>
                                            <td><?php echo $value['lead_cand_name'];?></td>
                                            <td><?php echo $value['lead_cand_party'];?></td>
                                        </tr>
                                            <?php $i++; }}}else{?>
                            <tr><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
                                    <?php }?>
                            </tbody>

                    </table>
			


<br>
		</div>	    
		</div>	
		</div>	
	</div>	
</section>

@endsection
