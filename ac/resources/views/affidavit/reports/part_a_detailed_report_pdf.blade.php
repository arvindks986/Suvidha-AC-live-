<!DOCTYPE html>
<html>
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    
<meta charset="UTF-8">
  <head>
    <style>
	
	body, p, th, td, div, span, h1, h2, h3, h4, h5, h6, h7 { font-family: freesans; }
	
      
     body {
		font-size: 11pt;
		line-height: 1.5;
	}
	table {
		max-width:824px;
		margin:0 auto;
		border-collapse: separate;
		border-spacing: 0;
		color: #4a4a4d;	
		line-height: 1.5;	
		font-family: "Times New Roman", Times, sans-serif;
	}	        	  
	table, th, td {
		border-collapse: collapse;			  
		padding:05px;
		color:#101010;		
	}
	th{
		font-weight: bold;		
	}
	.bold{font-weight: bold;}
	.inputLine{
		padding-left: 10px; 
		padding-right: 10px; 
		width: 150px; 
		font-weight: bold;
	}	 
    table{
	    width: 100%;	    	    
    }
    td.n-bordered{
			border: none!important;
		}
	.padd-0{
		padding: 0px!important;
	}
	td.bdrLeass{
		border-style: hidden!important;
	}
	.bdrLeass{
		border-style: hidden!important;
	}
	td.bordered {
	  border: 1px solid black!important;
	}
	.allBordered td{
		border: 1px solid black!important;
	}	
	input{
		width:200px;		
		outline: 0;
		border:0!important;
		
	}
	.inputbox{
		width:200px;
		border-bottom: solid black 1px;
	}
	textarea{
		outline: 0;
		width: 100%;
		border:0;
		border-bottom: solid black 1px;
	}
	.padd-0{
		padding: 0px!important;
	}
	.bdrLeass{
		border-style: hidden!important;
		border:none!important;
	}

	.bdr_less td{  border:none!important;border-style: hidden!important; }
	
	.red{color:red;}
	.block{
		display: block;
	}
	.inBlock{
		display: inline-block;
	}
	.w-20{width: 20px; display: inline-block;}
	.pad-20{
		padding-left: 27px;
	}
	.pad-35{
		padding-left: 35px;
	}

	.top td, .top td * {
	    vertical-align: top;
	}
	.top td{
	    vertical-align: top;
	}
	.top th{
	    vertical-align: top;
	}
	.top-20{
		margin-top: 20px;
	}
	.w-100{
		width: 100%;
	}
	tr.noBorder td {
		  border: 0!important;
	}
	tr.noBorder th{
	  border: 0!important;
	}
	.lineHeght-25{
		line-height: 25px;
	}
	.inputLine{
		padding-left: 10px; 
		padding-right: 10px; 
		width: 190px; 
		font-weight: bold;
	}
	.Line{
		width:300px;
		border-bottom:solid black 1px;
	}
	p {
align:justify;
}
.reporthd{
				background-color:#ccc;
			}
			.thHeading{
				background-color:#ccc;
			}
			
	@page{
            header: page-header;
            footer: page-footer;
        }
	
	@page :first{
			header: #;
            footer: page-footer;
        }
	@page { margin-top: 160px; }
	@page :first{
	  margin-top: 50%;
	}		
	
    #header { position: relative!important; left: 0px;  right: 0px; height: 140px; text-align: center;  }
 
    </style>
  </head>
<body> 
<htmlpageheader name="page-header">

 <table  align="center" id="header" border="1">
    <tr>
      <td  style="width:50%;">
        <table  style="100%">
          <tbody>
            <tr class=""> 
               <td align="left"> <?php if(@$data['qrcode']) { ?> <img src="{!! @$data['qrcode'] !!}" style="max-width: 80px;float:right;"><?php } ?></td> 
               <td align="left">{{Lang::get('affidavit.affidavit_id')}}: <strong>{{@$data['cand_details']->affidavit_id}}</strong> </td>
			    <td align="left"><strong>{{ __('download.print') }}:</strong> {{ date('d-M-Y h:i a') }}</td> 
            </tr>
        </table>
      </td>
    </tr>
  </table>

  
  	</htmlpageheader>
  
  <header name="page-header">

 <table  align="center" id="header" border="1">
    <tr>
      <td  style="width:50%;">
        <table  style="100%">
          <tbody>
            <tr class=""> 
               <td align="left"> <?php if(@$data['qrcode']) { ?> <img src="{!! @$data['qrcode'] !!}" style="max-width: 80px;float:right;"><?php } ?></td> 
               <td align="left">{{Lang::get('affidavit.affidavit_id')}}: <strong>{{@$data['cand_details']->affidavit_id}}</strong> </td>
			    <td align="left"><strong>{{ __('download.print') }}:</strong> {{ date('d-M-Y h:i a') }} @if(@$data['cand_details']->finalized != '1') <strong>Draft</strong> @endif</td> 
            </tr>
        </table>
      </td>
    </tr>
  </table>

  
  	</header>
  
  <br/>
  <br/>
  
  <div class="bordertestreport">

	@include('affidavit.report_common')

</div>

<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
 
</tr>

</table>
 </htmlpagefooter>

</body>

</html>