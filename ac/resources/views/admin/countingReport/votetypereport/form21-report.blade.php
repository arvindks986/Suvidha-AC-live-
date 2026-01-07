@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha AC')
@section('bradcome', 'Form 21 E Details')
@section('content')
<style type="text/css">
.Pdf-container {width:800px; background: #fff; margin: 0 auto;}
section.pdfDoc table { font-family: serif;}
section.pdfDoc table.table td{padding:5px;border: 1px solid #000;border-top: 0;}
section.pdfDoc table td {padding: 4px 0;font-size: 18px;}
section.pdfDoc table h1{font-size: 36px;  font-weight: 700;}
section.pdfDoc table h2 {font-size: 24px;  font-weight: 800;   color: #000;}
section.pdfDoc table th {font-size: 18px;  font-weight: 800;}
</style>

<div class="loader" style="display:none;"></div>
<?php if($win_can && count($array)>0 && $total_validpol >0){
	if($win_can->status=='1'){
?>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
 <div class="row">
 <div class="col-md-9 pull-left">
  <h4>Form 21 E Details</h4>
 </div>
     <div class="col-md-3  pull-right text-right report_section">
         <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('/roac/form-21-report-pdf')}}" title="Download PDF">Export PDF</a></span>
     </div>

 </div>
</div>
</section>
<?php }}?>
<?php if($win_can && count($array)>0){
if($total_validpol >0 && $win_can->status=='1'){?>
<section class="pdfDoc">
	<div class="container-fluid">
		<div class="row">
		<div class="Pdf-container card pt-4 pb-4">
		<div class="card-body">
			<table width="100%" align="center" class="">
				<tbody>
                                        <tr><td colspan="2" align="center"><h2>Conduct of Elections Rules, 1961</h2></td></tr>
                                        <tr><td colspan="2" align="center"><h5>(Statutory Rules And Order)</h5></td></tr>
					<tr><td colspan="2" align="center"><h2>FORM-21E</h2></td></tr>
                                        <tr><td colspan="2" align="center">(See Rule 64)</td></tr>
                                        <tr><td colspan="2" align="center"><b>RETURN OF ELECTION</b></td></tr>
				</tbody>
			</table>
			<br>
			<table align="center" width="100%"> 
				<tbody>
                                    <tr style="font-size: 18px;">
                                                <td>Election to the Legislative Assembly of <b><u><?php if(isset($state)){ echo $state;}?></u></b>  from <b><u><?php if(isset($acname)){?>{{$acname}}<?php }?></u></b> Assembly constituency.</td>
					</tr>
 
				
				</tbody>
			</table>
			<br>
			<table align="center">
				<tbody><tr><td align="center"><h2>RETURN OF ELECTION</h2></td></tr>
			</tbody></table>
			<table width="100%" align="center" cellspacing="0" cellpadding="8" class="table table-bordered">
    <thead>
        <tr>
            <th>Serial No.</th>
            <th>Name of Candidate</th>
            <th>Party Affiliation</th>
            <th>Number of votes polled</th>
        </tr>
    </thead>
    <tbody>
		 @php $i=0;
        @endphp
        @if(count($array)>0)
        @foreach($array as $data)
        @php $i++;
        @endphp
                <tr>
            <td>{{$i}}</td>
            <td>{{$data['candidate_name']}}</td>
            <td>{{$data['party_name']}}</td>
            <td align="right">{{$data['total_vote']}}</td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
        @endif
        </tbody>

</table>
<br/>
<table width="100%" align="left" class="ml-auto">
	<tbody><tr>
		<td align="left">
			<table style="width: 100%;margin-bottom: 10px;">
				<tbody>
				<tr>
					<td style="" width="36%">Total Numbers of Electors :- </td>
                                        <td align="right">
                                            <?php if(isset($tot_electrol)){echo $tot_electrol + @$service_vote;}?>
                                        </td>
				</tr>
			</tbody></table>
			
			<table style="
    width: 100%;
    margin-bottom: 10px;
">
				<tbody>
				<tr>
					<td style="" width="46%">Total numbers of valid votes polled :</td>
                                        <td align="right"> <?php if(isset($total_validpol)){echo $total_validpol;}?> </td>
				</tr>
                                <tr>
					<td style="" width="46%">Total numbers of None of the above (NOTA): </td>
                                        <td align="right"> <?php if(isset($total_nota)){echo $total_nota;}?> </td>
				</tr>
			</tbody>
			</table>
			
			<table style="width: 100%; margin-bottom: 10px;">
				<tbody>
					<tr>
						<td style="" width="42%">Total numbers of rejected votes :</td>
                                                <td align="right"> <?php if(isset($round_details)){?>{{@$round_details->rejected_votes}}<?php }else{echo '0';}?></td>
					</tr>
				</tbody>
			</table>
			
			<table style="margin-bottom: 10px;  width: 100%;">
				<tbody>
					<tr>
						<td style="" width="42%">Total numbers of tendered votes: </td>
                                                <td align="right"> <?php if(isset($round_details)){?>{{@$round_details->tended_votes}}<?php }else{ echo '0';}?></td>
					</tr>
				</tbody>
			</table>
			
			<table style="margin-bottom: 10px;  width: 100%;">
				<tbody>
					<tr>
						<td style=" " width="41%">I declare that :</td>
						
					</tr>
				</tbody>
			</table>
		</td>	
		</tr>
</tbody>
</table>

<table width="100%" align="left">
	<tbody><tr>
		<td style="border-bottom: 2px solid #000;font-weight:bold;">@if(isset($win_can)) @if(isset($win_can->lead_cand_name)) {{$win_can->lead_cand_name}} @endif @endif</td>
		<td style=" " width="80px" align="right"> </td>
	</tr>
</tbody></table>
<table width="100%" align="left">
	<tbody><tr>
		<td width="50px" align="left">of</td>
		<td style="border-bottom: 2px solid #000;font-weight:bold;">@if(isset($win_can)) @if(isset($win_can->candidate_residence_address)) {{ucwords(str_replace(strtolower($dist),'',strtolower($win_can->candidate_residence_address)))}}<?php  if($dist){echo ', '.$dist;}?><?php  if($candstate){echo ', '.$candstate;}?> @endif @endif</td>
		<td width="80px" align="right"> </td>
	</tr>
</tbody></table>
<table width="100%" align="left">
	<tbody><tr align="left">
		<td>has been duly elected to fill the seat.</td>
		
	</tr>
</tbody>
</table>
<?php  date_default_timezone_set("Asia/Calcutta");  ?>
<br>
<table width="100%" align="left" class="mt-2"> 
<tbody><tr><td style="text-align: left;width: 70px;">Place:-</td><td>&nbsp;</td></tr>   
</tbody></table>
<br>
<table width="100%">
	 <tbody><tr><td style=" width: 70px;">Date:- </td><td> {{ date('d-m-Y H:i:s') }}</td> <td style=" width: 40%;">Returning Officer</td> <td></td></tr></tbody></table>
		</div>	
		</div>	
		</div>	
	</div>	
</section>
<?php }else{?>
<div class="clearfix">&nbsp;</div>
<section class="pdfDoc">
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
<section class="pdfDoc">
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
@endsection
