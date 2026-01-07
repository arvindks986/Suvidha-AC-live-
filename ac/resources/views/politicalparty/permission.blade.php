@extends('admin.layouts.theme')
@section('title', 'List Candidate')
@section('content') 
@include('admin.includes.list_script')
<div class="container-fluid">
 
  <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item active ">
    <a class="nav-link " id="home-tab" data-toggle="tab" href="#total" role="tab" aria-controls="total" aria-selected="true">Total Applied Permission</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#accepted" role="tab" aria-controls="accepted" aria-selected="false">Accepted Permission</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">Rejected Permission</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="false">Pending Permission</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
<!--  Total Permission start-->
<div class="tab-pane fade show active in" id="total" role="tabpanel" aria-labelledby="home-tab">
    <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
              <th>Reference No</th><th>User Name</th>
              <th>User Type</th><th>Permission Type</th>
              <th>Date of Submission</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="pid" style="cursor: pointer;color: blue">1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
            </tr>
        </tbody>
    </table>
</div>
<!--  Total Permission End-->

<!--Accepted Permission start-->
	<div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="profile-tab">
		<div class="tab-pane fade show active in" id="total" role="tabpanel" aria-labelledby="home-tab">
			<table id="example" class="table table-striped table-bordered" style="width:100%">
				<thead>
					<tr>
					  <th>Reference No</th><th>User Name</th>
					  <th>User Type</th><th>Permission Type</th>
					  <th>Date of Submission</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td id="pid" style="cursor: pointer;color: blue">1</td>
						<td>2</td>
						<td>3</td>
						<td>4</td>
						<td>5</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<!--  Accepted Permission End-->

<!--Rejected Permission start-->
  <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="contact-tab">3</div>
<!--  Rejected Permission End-->

<!--pending Permission start-->
  <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="contact-tab">4</div>
<!--  pending Permission End-->
</div>
    
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <p></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
<!--  End Modal-->
</div> 
<input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
<script>
    $(function(){
        var token = $('meta[name="csrf-token"]').attr('content');
        var base_url = $("#base_url").val();
        $('tr #pid').click(function(){
            var id= $(this).text();
            $.ajax({
                url:base_url+'/getpermissiondetails',
                type: 'POST',
                data:{ _token:token,pid:id},
                success:function(data)
                {
                        $("#total").load(base_url+'/getpermissiondetailsview',{'data':data, _token:token});
                        $('.nav-item').toggleClass('relode-btn');
                        $('.relode-btn').on('click' ,function(){
                            location.reload();
                        });
                }
            });
        })
    });
</script>
@endsection