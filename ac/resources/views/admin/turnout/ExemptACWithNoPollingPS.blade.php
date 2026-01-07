@extends('admin.layouts.ac.theme')
@section('content')
<section class="statistics color-grey pt-4 pb-2">



	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 pull-left">
				@if (session('success'))
				<div class="alert alert-success"> {{session('success') }}</div>
				@endif

				@if (session('error'))
				<div class="alert alert-danger"> {{session('error') }}</div>
				@endif
				<h4>Exempt AC with No Polling Polling Stations</h4>
			</div>

		</div>
	</div>
</section>

<section class="dashboard-header section-padding">
	<div class="container-fluid">

		<form action="{{url('acceo/turnout/ExemptACWithNoPollingPS')}}" class="row" method="post">
			{{@csrf_field()}}
			<div class="form-group col-md-3"> <label>Phases </label>
				<select name="ac" class="form-control">
					@foreach($acs as $ac)
					@if (!in_array($ac->AC_NO, $selectedAc))
					<option value="{{$ac->AC_NO}}">({{$ac->AC_NO}}) {{$ac->AC_NAME}}</option>
					@endif
					@endforeach
				</select>
			</div>
			<div class="form-group col-md-3"> <label>&nbsp;</label>
				<button type="submit" class="btn btn-success mt-4">Exempt Now</button>
			</div>
		</form>
	</div>
</section>



<div class="container-fluid">
	<!-- Start parent-wrap div -->
	<div class="parent-wrap">
		<!-- Start child-area Div -->
		<div class="child-area">
			<div class="page-contant">
				<div class="random-area">
					<div class="table-responsive">

						<table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th> Sno. </th>
									<th> AC No </th>
									<th> AC Name </th>
									<th> Exempted At</th>
									<th> Actioin</th>
								</tr>
							</thead>
							<tbody>
								@if(count($results) > 0)
								@foreach($results as $key => $item)
								<tr>

									<td>{{$key+1}}</td>
									<td>{{$item->ac->AC_NO}}</td>
									<td>{{$item->ac->AC_NAME}}</td>
									<td>{{$item->created_at}}</td>
									<td><button class="btn btn-danger removeRow" data-id="{{$item->id}}">Remove</button></td>
								</tr>
								@endforeach
								@else
								<tr>
									<td colspan="5">No Data found</td>
								</tr>
								@endif

							</tbody>
						</table>

					</div><!-- End Of  table responsive -->
				</div><!-- End Of intra-table Div -->


			</div><!-- End Of random-area Div -->

		</div><!-- End OF page-contant Div -->
	</div>
</div><!-- End Of parent-wrap Div -->
</div>

<div class="modal modal-big fade" id="removeConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="removeConfirmationModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header mb-3">
				<h5 class="modal-title" id="exampleModalLabel">Confirmation For Removal!</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form class="form-horizontal" id="election_form" method="post" action="{{url('acceo/turnout/ExemptACWithNoPollingPSRemove')}}" autocomplete='off'> {{csrf_field()}}
				{{@csrf_field()}}
				<input type="hidden" id="id" name="id">
				<div class="modal-body">
					<div class="mb-3">
						<div style="font-size:16px;">Are you sure you want to remove ac from exempted list</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" id="submit_final_form" class="btn btn-success submit-button">Confirm</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#data_table_table').DataTable();
		$(document).on('click', '.removeRow', function() {
			$('#id').val($(this).attr('data-id'));
			$('#removeConfirmationModal').modal('show');
		})
	})
</script>
@endsection