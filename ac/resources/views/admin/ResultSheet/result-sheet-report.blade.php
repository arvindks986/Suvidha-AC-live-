@extends('admin.layouts.ac.dashboard-theme')
@section('content')

<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/jquery.stickytable.min.css') }}">

<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

#acViewBody a{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}

#acViewBody a:hover{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}
.bold{font-weight:bold;}

.swatch-yellow {
   color: #fff;
    background-color: #17a2b8; padding: 10px;
}
.form-control:disabled, .form-control[readonly]{background:#fff; height:46px; border:1px solid #d5d5d5;}
button.btn.dropdown-toggle.btn-light.bs-placeholder {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
button.btn.dropdown-toggle.btn-light {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
.form-control:disabled, .form-control[readonly]{height:37px;}
.form-control:focus, .form-control:hover{box-shadow:none;}
#divChart {
  margin: auto;
  width: 73%;
   border: 3px solid white;
   border:0px solid #ddd
}
#divChart1 {
  margin: auto;
  width: 70%;
  border: 0px !important;
}
</style>

<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-6 pull-left">
   <h4 id="heading_con">Result Sheet</h4>
   Date : {{date('d/m/Y H:i:s A')}} 
  </div>
	@if(count($result)>0)
    <div class="col-md-3  pull-right text-right"> 
      <span id="button_con" onclick="return downloadPdf();" class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="#" title="Download PDF" >Export PDF</a></span>  
    </div>
	@endif
	<div class="col-md-3  pull-right text-right"> 
      User : {{$user_data->officerlevel}}
    </div>
  </div>
</div>  
</section>


<div class="container-fluid" id="DivIdToPrint">
<div class="row">
	<div  class="col mt-5">
		<div style="text-align:center;font-weight:bold;font-size:22px;">TRENDS & RESULT {{$elec_name}}</div>
		<div>&nbsp;</div>
		<table style="margin: auto; width: 100%; font-family: Verdana;" cellspacing="0" cellpadding="5">  <tbody><tr> <td colspan="4" style="color:black;text-align:center;" align="center">
		<tr style='height: 5px'>
                    <td>

        @if($Constituencies_out_of_count>0)
        
		   <div id='divChart'>
			      <div id='divChart1'>
				  <div style="text-align:left;font-weight:bold;font-size:22px;">{{$state_name}} </div>
					 <canvas id='myChart'></canvas>
				   </div>
				  <!-- <h5> MAJORITY-36 </h5> -->
			</div>
                               
        @endif			
				</td>
                </tr>
		</table>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<table style="margin: auto; width: 100%; font-family: Verdana; border: solid 1px black;font-weight:lighter;" cellspacing="0" cellpadding="5" border="1">  <tbody><tr> <td colspan="4" style="color:black;text-align:center;Background-color:#FFC0CD;font-size:medium;Font-Names:Calibri;font-weight:bold;" align="center"> 
                
				{{$state_name}}
              <br>Result Status <div id="divStatusHR" style="font-size: 10px;"></div></td> </tr>  <tr style="text-align:center;Background-color:#FFC0CD;color:Maroon;font-size:medium;Font-Names:Calibri; color:black" align="center"><td colspan="4" align="center"><b><div style="font-size: 10px; font-weight: bold" id="divStatus"> Status Known For {{$Constituencies_out_of_count}} out of {{$Constituencies_count}} Constituencies</div></b></td></tr><tr style="text-align:center;Background-color:#FFC0CD;color:Maroon;font-size:medium;Font-Names:Calibri;" align="center;color:black"> <th align="left">Party</th> <th>Won</th><th>Leading</th><th>Total</th></tr>
			  {!! $resultPartywisedata !!}

        </tbody></table>
		
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		
		<table style="margin: auto; width: 100%; font-family: Verdana;" cellspacing="0" cellpadding="5">  <tbody><tr> <td colspan="4" style="color:black;text-align:center;" align="center">
		<tr>
			 <td align='center' style='border:1px solid #ddd' colspan='4'>
			<p style='font-style: normal;font-weight:bold'>Partywise Vote Share</p>
			   <div  id='piecharts26'></div>
		   </td>
	    </tr>
		</table>

		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<br>
		<br>
		<br>
		<br>
		<table id="list-table"  class="table table-striped table-bordered datatable  ">
<thead>	
		<tr class="sticky-header">
        <th style="color:black;"> S.No </th>
		<th style="color:black;">State Name</th>
		<th style="color:black;">AC Name</th>
        <th style="color:black;">AC No.</th>
		<th style="color:black;">Leading  Party</th>
		<th style="color:black;">Leading Candidate</th>
		<th style="background:#faebd7;color:black;">Margin</th>
		<th style="background:burlywood;color:black;">Trailing Party</th>
		<th style="background:burlywood;color:black;">Trailing Candidate</th>
		<th style="color:black;">Result status </th>
		</tr>
 </thead>
		
		<tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$status='';
		if($data->status==1){
		$status='Result Declared';	
		}
		if($data->status=='0'){
		$status='Result In Progress';	
		
		}
	
		?>
        <tr>
        <td>{{$i}}</td> 
		<td style="text-align:left;">@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_name) && (!empty($data->ac_name))){{$data->ac_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($data->ac_no) && (!empty($data->ac_no)) ){{$data->ac_no}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td style="text-align:left;">
		@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
			@if($data->status=='1' && $data->margin!='0')<span style="color:green;">({{'WINNER'}})</span>@endif
		@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:antiquewhite;">@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;background:burlywood;">@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;">@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		</tr>

		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="11">  No record available </td> 
		</tr>
		@endif
       </tbody></table>
	</div>
</div>
 </div>
<script type="text/javascript" src="{{ asset('js/Chart.js') }}"></script>	
<script type="text/javascript" src="{{ asset('js/jsapi.js') }}"></script>
<script>
 google.load('visualization', '1.0', { packages: ['corechart'] });
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var chart=null;
		var data = null;
		var options=null;
		if(document.getElementById('piecharts26')!=null)
		{
			data = new google.visualization.DataTable();
			data.addColumn('string', 'PartyName');
			data.addColumn('number', 'Votes Wise(%)');
			<?php echo $votesharedata ?>
			options = {
				legend: {position: 'left',textStyle: {fontName: 'Arial', fontSize: 10}},
				titleTextStyle:{fontName: 'Arial', fontSize: 13,bold:false},
				tooltip:{textStyle: {fontName: 'Arial', fontSize: 11,bold:false}},
				is3D: true,
				title: 'Please move your mouse over the chart or legend to view more details. Party {Votes%,Vote Count}',
				width: 700,
				height: 400,
				sliceVisibilityThreshold:0,
			    <?php echo $votesharecolor ?>
			 // slices: {0:{color: '#AA0078'},1:{color: '#FF6600'}}
			};
			chart = new google.visualization.PieChart(document.getElementById('piecharts26'));
			chart.draw(data, options);
		}
	}
		
Chart.pluginService.register({
	beforeDraw: function (chart) {
	if (chart.config.options.elements.center) {
	//Get ctx from string
	var ctx = chart.chart.ctx;
		//Get options from the center object in options
	var centerConfig = chart.config.options.elements.center;
	var fontStyle = centerConfig.fontStyle || 'Arial';
		var txt = centerConfig.text;
	var color = centerConfig.color || '#000';
	var sidePadding = centerConfig.sidePadding || 20;
	var sidePaddingCalculated = (sidePadding/100) * (chart.innerRadius * 2)
	//Start with a base font of 30px
	ctx.font = '30px ' + fontStyle;
		//Get the width of the string and also the width of the element minus 10 to give it 5px side padding
	var stringWidth = ctx.measureText(txt).width;
	var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;
	// Find out how much the font can grow in width.
	var widthRatio = elementWidth / stringWidth;
	var newFontSize = Math.floor(30 * widthRatio);
	var elementHeight = (chart.innerRadius * 2);
	// Pick a new font size so it will not be larger than the height of label.
	var fontSizeToUse = Math.min(newFontSize, elementHeight);
		//Set font settings to draw it correctly.
	ctx.textAlign = 'center';
	ctx.textBaseline = 'middle';
	var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
	var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 1.09);
	ctx.font = fontSizeToUse+'px ' + fontStyle;
	ctx.fillStyle = color;
	//Draw text in center
	ctx.fillText(txt, centerX, centerY);
	}
	}
});

var data = {
	<?php echo $semi_chart_labels ?>,
	datasets: [
	{
		<?php echo $semi_chart_data ?>,
	    <?php echo $semi_chart_bgcolor ?>,
		hoverBackgroundColor: [
	]
	}]
};
var ctx = document.getElementById('myChart');
// And for a doughnut chart
	var myDoughnutChart = new Chart(ctx, {
	type: 'doughnut',
	data: data,
	options: {
		rotation: 1 * Math.PI,
		circumference: 1 * Math.PI,
		elements: {
			center: {
				text: "<?php echo $Constituencies_out_of_count ?>"+'/'+"<?php echo $Constituencies_count?>",
		color: '#FF6384', // Default is #000000
		fontStyle: 'Arial', // Default is Arial
		sidePadding: 20 // Defualt is 20 (as a percentage)
			}
		},
		legend: {
		position: 'bottom'
		},
		labels: {
		render: 'label'
		}
	}
}); 

function downloadPdf() 
{
	$("#heading_con").hide();
	$("#button_con").hide();
	$(".nav-header").hide();
	$(".alert-warning").hide();
	
	window.print();
	$("#heading_con").show();
	$("#button_con").show();
	$(".nav-header").show();
	$(".alert-warning").show();

}

</script>
<script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.stickytable.min.js') }}"></script>
@endsection




