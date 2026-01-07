<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Candidate Nomination Information</title>
	<style type="text/css">
		.table-strip {
			border-collapse: collapse;
		}
		
		ul {
			list-style-type: none;
		}
		
		@page {
			header: page-header;
			footer: page-footer;
		}
		
		body, p, td, div { font-family: freesans; }
	</style>
</head>

<body>
	<htmlpageheader name="page-header">
		<table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
			<thead>
				<tr>
					<th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt="" width="100" border="0"/>
					</th>
					<th style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
					</th>
				</tr>
			</thead>
		</table>

	</htmlpageheader>

	<htmlpagebody> 
	
	   <br>
	   <br>
		<table class="table-strip" style="width: 110%;" border="1" cellpadding="9">
			<tbody>
				<tr>
					<td>
						<table style="width: 101%;" border="1">							
							<tbody>
								<tr>
									<td style="text-align:left;">Sr. No</td>
									<td style="text-align:left;">Candidate</td>
									<td style="text-align:left;">Nom</td>
									<td style="text-align:left;">State</td>
									<td style="text-align:left;">Ac</td>
									<td style="text-align:left;">Finalize</td>
									<td style="text-align:left;">Name</td>
								</tr>
					<?php $i=1; foreach($alld as $data) {?>			
								<tr>
									<td style="text-align:left;">{{$i}}</td>
									<td style="text-align:left;">{{$data->candidate_id}}</td>
									<td style="text-align:left;">{{$data->nomination_no}}</td> 
									<td style="text-align:left;">{{$data->st_code}}</td> 
									<td style="text-align:left;">{{$data->ac_no}}</td> 
									<td style="text-align:left;">{{$data->finalize_after_payment}}</td> 
									<td style="text-align:left;">{{$data->name}}</td> 
								</tr>
					<?php $i++; } ?>						
								
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>


	</htmlpagebody>
	<htmlpagefooter name="page-footer">
		<table style="width:100%; border-collapse: collapse;" align="center" cellpadding="5">
			<tbody>
				<tr>
					<td colspan="2" align="center"><strong></strong>
					</td>
				</tr>
			</tbody>
		</table>
	</htmlpagefooter>
</body>
</html>