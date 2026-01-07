@extends('admin.central.common.theme')
@section('title', 'Officer Directory')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => Common::generate_url('mis/officer-directory'),
    'name' => 'Officer Directory'
  ]; 
  ?>
@section('content')


<style>	

.bolds{
	font-weight: bold;
}
</style>
<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left custom-database-table">
        <div class="card-header">
          <div class=" row">
            <div class="col"><h4>Officer List @if(session()->has('success_msg'))<div class="alert alert-success alert-dismissible">{{ session()->get('success_msg') }}</div>@endif</div>
            <div class="col">
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table" id="list-table">
			  <thead>
				<tr>
					<th>Sl.No.</th>
					<th>Profile Pic</th>
					<th>User Id</th>
					<th>Designation</th>
					<th>Place</th>
					<th>Office Name</th>
					<th>Email</th>
					<th>Mobile</th>
					<th>Action</th>
				</tr>
			  </thead>
			  <tbody>
				  @if(count($officer_list)>0)
				  @php $test = ['colorTd-parrot', 'colorTd-orange', 'colorTd-blue','colorTd-yellow','colorTd-green']; @endphp
				  @foreach($officer_list as $k=>$v)
				  <tr class="<?php echo $test[rand(0,2)];?>">
				    <td>{{++$k}}</td>
				    <td>
					   <div class="officer-pic">
						   @if(@$v->profile_pic != '' )
							<img src="{{ asset($v->profile_pic) }}"> 
						   @else
						   <img src="{{ asset('theme/img/male_avatar.png') }}"> 
						   @endif
					   </div>
					</td>
				    <td>{{$v->officername}}</td>
				    <td>{{$v->designation}}</td>
				    <td>{{$v->placename}}</td>
				    <td>{{$v->name}}</td>
				    <td>{{$v->email}}</td>
				    <td>{{$v->Phone_no}}</td>
				    <td>
					  <a href="{{url('/eci/mis/view-officer-profile/'.encrypt($v->id).'/')}}" class="actn-btn-icon"><i class="fa fa-eye" title="View officer record" aria-hidden="true"></i></a>
					</td>
				  </tr>
				  @endforeach
				  @endif
				  
			  </tbody>
			</table>			
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@endsection
@section('script')
<script>
<?php if(session()->has('success_msg')){?>
	setTimeout(function(){ $(".alert-dismissible").hide(); }, 3000);
<?php }?> 
</script>
@endsection