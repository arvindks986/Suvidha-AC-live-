<!DOCTYPE html>
<html lang="en">
<head>
<title>&nbsp;</title>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 
<style type="text/css">
.table{width: 100%; border-collapse: collapse;  font-family: Verdana; margin: auto; color: #000;}
tr.declaredbg {background-color: #e5fbe3;}
tr.progressbg {background-color: #f9efe0;}
</style>
</head>

<body>
    <!--HEADER STARTS HERE-->
    <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
        <thead>
            <tr>
                <th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img
                        src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt="" width="100" border="0" />
                </th>
                <th style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                    SECRETARIAT OF THE<br>
                    ELECTION COMMISSION OF INDIA<br>
                    Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>
                </th>
            </tr>
        </thead>
    </table>
	
	<style type="text/css">
        .table-strip {
            border-collapse: collapse;
        }

        .table-strip th,
        .table-strip td {
            text-align: center;
        }

        .table-strip tr:nth-child(odd) {
            background-color: #f5f5f5;
        }
    </style>
    <table style="width:100%; border: 1px solid #000;" border="0" align="center">

        <tr>
            <td style="width:50%;">
                <table style="width:100%">
                    <tbody>

                        <tr>
                            <td><strong>User:</strong>{{$user_data->placename}}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:50%">
                <table style="width:100%">
                    <tbody>
                        <tr>
                            <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>

                        </tr>

                        <tr>
                            <td align="right">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

	
		
		@if(count($data) > 0 )
		
		@foreach($data as  $key => $rawdata)
		
		
			<table id="list-table"  class="table" border="1" cellpadding="5">
			<thead>				
					<tr>
						<th colspan="5" style="background:#f0587e;color:black;"> State - {{getstatebystatecode($key)->ST_NAME}}</th>
					</tr>
					<tr style="background:#dee2e6;color:black;">
						<th align="center" style="color:black;">Sl. No. </th>
						<th style="color:black;">Party Name</th>				
						<th style="color:black;">Won</th>
						<th style="color:black;">Leading</th>					
						<th style="color:black;">Total </th>
					</tr>
			</thead>	
			
			<tbody>

			@php $i=1; @endphp
			
			@if(count($rawdata['topten']) >0)

			@foreach($rawdata['topten'] as $ten)
			<tr>
				<td align="center"> {{$i}} </td> 
				<td> {{$ten->lead_cand_party}}</td> 
				<td style="text-align: right;"> {{$ten->win}}</td> 
				<td style="text-align: right;"> {{$ten->lead}}</td> 
				<td style="text-align: right;"> {{$ten->win + $ten->lead}}</td> 
			</tr>
				@php $i++; @endphp
			@endforeach		
			@endif
			
			@if($rawdata['independent'])
			<tr>
				<td align="center"> {{$i}}</td> 
				<td> {{$rawdata['independent']->lead_cand_party}}</td> 
				<td style="text-align: right;"> {{$rawdata['independent']->win}}</td> 
				<td style="text-align: right;"> {{$rawdata['independent']->lead}}</td> 
				<td style="text-align: right;"> {{$rawdata['independent']->win + $rawdata['independent']->lead}}</td> 
			</tr>
			@php $i++; @endphp
			@endif
						
			@if(count($rawdata['others'])> 0)
			<tr>
				<td align="center"> {{$i}}</td> 
				<td> {{$rawdata['others']['lead_cand_party']}}</td> 
				<td style="text-align: right;"> {{$rawdata['others']['win']}}</td> 
				<td style="text-align: right;"> {{$rawdata['others']['lead']}}</td> 
				<td style="text-align: right;"> {{$rawdata['others']['win'] + $rawdata['others']['lead']}}</td> 
			</tr>
			@endif
		
		</tbody>
		 </table>
		 
		 <br />
		 <br />
		<!-- <div style="page-break-before:always;"> </div> -->
		 
		@endforeach	  
		@endif
	      		
	
   
	       <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
        <tbody>
            <tr>
                <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
            </tr>
        </tbody>
    </table>

 </body>
 </html>




