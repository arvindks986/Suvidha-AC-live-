
<table align="center" class="table table-bordered  poll-table" id="scan_table">
	<thead>
		<tr>
			<th>State Name</th>
			<th>AC No & Name</th>
			<th style="background:#6ccac6;">QR</th>
			<th style="background:#6ccac6;">Epic</th>
			<th style="background:#6ccac6;">Name</th>
			<th style="background:#6ccac6;">Slip ID</th>
			<!--<th style="background:#6ccac6;">Mobile</th> -->
		</tr>

	</thead>


	<tbody>
		@if(count($results)>0)
		@foreach($results as $iterate_reseult)
		<tr>
			<td>{{$iterate_reseult['st_name']}}</td>
			<td>{{$iterate_reseult['ac_no']}}-{{$iterate_reseult['ac_name']}}</td>
			<td>{{$iterate_reseult['total_qr']}}</td>
			<td>{{$iterate_reseult['total_epic']}}</td>
			<td>{{$iterate_reseult['total_name']}}</td>
			<td>{{$iterate_reseult['total_booth_id']}}</td>
			<!--<td>{{$iterate_reseult['total_mobile']}}</td> -->
		</tr>
		@endforeach
		@else
		<tr align="center"><td colspan="7">No Record</td></tr>
		@endif
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">{{$total['st_name']}}</td>
			<td>{{$total['total_qr']}}</td>
			<td>{{$total['total_epic']}}</td>
			<td>{{$total['total_name']}}</td>
			<td>{{$total['total_booth_id']}}</td>
			<!--<td>{{$total['total_mobile']}}</td> -->
		</tr>
	</tfoot>
</table>
<script type="text/javascript">
  $(document).ready(function () {
    if($('#scan_table').length>0){
      table = $('#scan_table').DataTable({
        "pageLength": 5,
        "aaSorting": [],
        "ordering": false,
        "searching": false,
        "bLengthChange": false,
      });

    }
  });
</script>