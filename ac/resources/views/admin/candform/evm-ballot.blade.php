<!DOCTYPE html>
<html lang="en">
<?php   $url = URL::to("/");  ?>
    <head>

        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
      <style type="text/css">
        @page {
        header: page-header;
        footer: page-footer;
	        @page { width: 397px; height: 1308px; } 
        }
        
       html,body{font-family: {{$font_data}}, sans-serif;  margin:0; overflow-x:hidden;}        
       * {-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%}
		table, td, th,tr {font-family:arial;}
		td, th{border-bottom:3px solid #000; vertical-align: middle;}
		table { width: 397px; height: 1308px; margin-left: 15px;}
        
	</style>
      
    </head>
    <body>
 
 	
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="height: 36px; font-size: 10px;" colspan="2"><strong>S.No. <span style="font-size: 18px; margin-left: 10px; display: inline-block; font-weight: 100;">{{$subhead}}</span></strong></td>
			<td align="right"  style="height: 36px; font-size: 10px;" colspan="2"><strong><span style="font-size: 10px; text-align:right;">{{$subhead1}}</span></strong></td>
		</tr> 
		  <?php $i=0; $url = URL::to("/");  ?>
		@foreach ($record as $key => $item) <?php $i++;  ?>
			<tr style="border-bottom:5pt solid black; height:100px;">
				<td style="text-align:left;" width="35">{{ $i }}.</td>
				<td style="text-align:left;">
					<span style="font-family:{{$fonts}};">
						@if($item->party_id=='1180') {{ $item->cand_name }} 
						@else {{ $item->cand_vname }} @endif 
					</span>
				</td>
				<td align="center"> @if(isset($item->cand_image) and $item->party_id!='1180')
					<img src="{{public_path($item->cand_image)}}" style="width:75px; height:75px;" class="prfl-pic img-thumbnail" alt="">
				  @else

				   @endif
               </td>
				<td align="center"> @if(isset($item->Symbol_Img))
      			<img src="data:{{$item->CONTENT_TYPE}};base64, {{$item->Symbol_Img}}" alt="" style="width:75px; height:75px;" />
                @else

    			@endif
    				 
 				</td>
			</tr>
		@endforeach
		 
	</table>
</body>
</html>