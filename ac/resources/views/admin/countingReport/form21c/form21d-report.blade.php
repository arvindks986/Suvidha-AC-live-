@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha AC')
@section('bradcome', 'Form 21 D Details')
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
<?php if($wincan){
	if($wincan->status=='1'){
?>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
 <div class="row">
  
     <div class="col-md-12  pull-right text-right report_section">
         <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('/roac/form-21c-report-pdf')}}" title="Download PDF" >Export PDF</a></span>
		 <span class="report-btn" id="export-doc-btn"><a class="btn btn-success word-export" href="javascript:void(0)"  title="Download DOC" >Export DOC</a></span>
     </div>

 </div>
</div>
</section>
<?php }}?>
<?php if($wincan){
    if($wincan->status=='1'){
?>
<section class="pdfDoc" id="export-content">
	<div class="container-fluid">
		<div class="row">
		<div class="Pdf-container card pt-4 pb-4">
		<div class="card-body">
			<table width="100%" align="center" class="">
				<tbody>
					<tr><td colspan="2" align="center"><h4 style="font-weight:bold;">Conduct of Elections Rules, 1961 </h4></td></tr>
					<tr><td colspan="2" align="center">(Statutory Rules And Order)</td></tr>
					<tr><td colspan="2" align="center"><h5 style="font-weight:bold;">FORM 21D</h5></td></tr>
					<tr><td colspan="2" align="center">(See Rule 64) </td></tr>
					<tr><td colspan="2" align="center">(For use in election to fill a casual vacancy when seat is contested) </td></tr>
				</tbody>
			</table>
			<br>
			<table align="center" width="100%"> 
				<tbody>
					<tr>
                        <td>Declaration of the result of Election under section 66 of the Representation of the People Act, 1951. </td>
                    </tr>
				</tbody>
			</table>
			<br>
			<table width="100%" align="center"> 
				<tbody>
					<tr>
                        <td>*Election to the Legislative Assembly of <b><u><?php if(isset($ac_state)){ echo $ac_state;}?></u></b> from <b><u><?php if(isset($acname)){?>{{$acname}}<?php }?></u></b> Assembly constituency.</td>
                    </tr>
					<tr>
					<td>In pursuance of the provisions contained in section 66 of the Representation of the People Act, 1951, read with rule 64 of the Conduct of Elections Rules, 1961, I declare that- </td>
					</tr>
				</tbody>
			</table>
			<br>
			<table width="100%"> 
				<tbody>
					<tr align="center">
                        <td><b><?php if($wincan){?>{{@$wincan->lead_cand_name}}<?php }?></b> </td>
                    </tr>
					<tr align="justify">
                                        <td><b><?php if(isset($wincan)){?>{{ucwords(str_replace(strtolower($dist),'',strtolower($wincan->candidate_residence_address)))}}<?php //if($dist){ echo ', '.$dist;}?><?php //if($state){ echo ', '.$state;}?><?php }?></b>  [sponsored by <b><?php if(isset($wincan)){?>{{@$wincan->lead_cand_party}}<?php }?></b> ] has been duly elected to fill the vacancy caused in that House by the </td>
					</tr>
                                        <tr align="justify">
                                        <td>*resignation of .........................................................................................................................................</td>
					</tr>
                                        <tr align="justify">
                                        <td>*death of ...................................................................................................................................................</td>
					</tr>
                                        <tr align="justify">
                                        <td>*election of ........................................................................................having been declared void.</td>
					</tr>
                                        <tr align="justify">
                                            <td>*seat of .............................................................................................<u>having become&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>vacant.</td>
					</tr>
                                        <tr align="justify">
                                            <td style="padding-left:480px;">having been declared</td>
					</tr>
				</tbody>
			</table>


<br>
<table width="100%" align="left"> 
<tbody>
<tr><td style="text-align: left;">Place : &nbsp;</td><td></td></tr>   
    <tr><td style="text-align: left;">Date : {{ date('d-m-Y') }}</td><td></td></tr>  
</tbody></table>
<br><br><br><br><br><br><br>
<table width="100%">
	 <tbody>
         <tr><td style=" width: 100px;">&nbsp;</td><td>&nbsp;</td><td style="width: 25%;font-size:18px;" align="center"><b><?php if($user_data){?>({{$user_data->name}})<?php }?></b></td> <td></td></tr>
	 <tr><td style=" width: 100px;">&nbsp;</td><td>&nbsp;</td><td style="width: 50%;font-size:18px;" align="center"><b>Returning Officer</b></td> <td></td></tr>
         <tr><td style=" width: 100px;">&nbsp;</td><td>&nbsp;</td><td style="width: 25%;font-size:18px;" align="center"><b><?php if($user_data){?>{{$ac_name1}}<?php }?> <br> Legislative Assembly Constituency</b></td> <td></td></tr>
	 </tbody></table>
		</div>	
		</div>	
		</div>	
	</div>	
</section>
<?php }else{?>
<div class="clearfix">&nbsp;</div>
<section class="pdfDoc" id="export-content">
	<div class="container-fluid">
		<div class="row">
		<div class="Pdf-container card pt-4 pb-4">
		<div class="card-body">
		No record available, result not declared yet.
		</div>
		</div>
		</div>
		</div>
</section>
<?php }}else{?>
<div class="clearfix">&nbsp;</div>
<section class="pdfDoc" id="export-content">
	<div class="container-fluid">
		<div class="row">
		<div class="Pdf-container card pt-4 pb-4">
		<div class="card-body">
		No record available, result not declared yet.
		</div>
		</div>
		</div>
		</div>
</section>
<?php }?>
</div> 
<script>
$(document).ready(function($) {
  $("a.word-export").click(function(event) {
    $("#export-content").wordExport();
  });
});

</script>
@endsection
