 
 <style>

table, td, th,tr {
	font-family:arial;
  
}
td, th{border-bottom:1px solid #000}
table {
 
  width:100%;
}

th {
  height: 50px;
}
</style> 
 	
	<table border="0" cellpadding="0" cellspacing="0">
		<tr><td style="" colspan="4"><h2 align="center">Ballot Paper Of Candidate</h2></td></tr> 
		  <?php $i=1; $url = URL::to("/");  ?>
		@foreach ($cand as $key => $item)
			<tr style="border-bottom:1pt solid black;">
				<td style="text-align:left;">{{ $item->new_srno }}</td>
				<td style="text-align:left; width:50%;">{{ $item->cand_name }}</td>
				<td> @if($item->cand_image!='')
					<img src="{{$url.'/'.$item->cand_image}}" alt="Picture" border="0" style="margin:10px" width="100px" height="100px"> @else 
                      <img src="{{ asset('theme/images/User-Icon.png') }}" alt="" style="margin:10px" width="100px" height="100px">
                    @endif </td>
				<td style="text-align:left;">@if($item->SYMBOL_DES!='') {{$item->SYMBOL_DES}} &nbsp;&nbsp; <span style="font-family:freeserif;">{{$item->SYMBOL_HDES}}</span> @endif</td>
			</tr>
		@endforeach
		 
	</table>
