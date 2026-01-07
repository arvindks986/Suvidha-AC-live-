@extends('admin.central.common.theme')
@section('title', 'Update Officer Details')
<?php
$breadcrumbs = [];
$breadcrumbs[] = [
    'href' => Common::generate_url('mis/officer-details'),
    'name' => 'Officer Details'
];
?>
@section('content')
 <?php  $st=getstatebystatecode($user_data->st_code); 
        //$pc=getpcbypcno($user_data->st_code,$user_data->pc_no); 
        // dd($pc);
    ?>
  
<main role="main" class="inner cover mb-3">
<section class="mt-5">
  <div class="container-fluid">
    @if(Session::has('success_mes'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_mes')) }}</strong> 
              </div>
          @endif
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h6 class="mr-auto">Officer Details</h6></div> 
             <div class="col"><p class="mb-0 text-right">
              <b>State Name:</b> 
              <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
              <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
              <b></b> <span class="badge badge-info"></span>
              </p></div>
            </div>

            <div class="row">
            <div class="col"><p class="mb-0 text-center">
              <span class="alert alert-info">Please enter valid mobile number / email id as same will be used in OTP verification while login.</span>&nbsp;&nbsp; 
          </p></div> </div>

            </div>
<div class="card-body">  
         @if (session('success_error'))
            <div class="alert alert-danger">
                {{ session('success_error') }}
            </div>
          @endif
          @if (session('success_success'))
            <div class="alert alert-success">
                {{ session('success_success') }}
            </div>
          @endif

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
					<th>Account Activated</th>
					<th>Action</th>
				</tr>
			  </thead>
			  <tbody>
				  @if(count($officerlist)>0)
				  @php $test = ['colorTd-parrot', 'colorTd-orange', 'colorTd-blue','colorTd-yellow','colorTd-green']; @endphp
				  @foreach($officerlist as $k=>$v)
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
					<td >@if(!empty($v->password)) Yes @else No @endif</td>
				    <td>
					  <a href="{{url('/acceo/mis/officer-profile/'.encrypt($v->id).'/')}}" class="actn-btn-icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
					</td>
				  </tr>
				  @endforeach
				  @endif
				  
			  </tbody>
			</table>
           </div> <!-- end reponcive-->
          </div>
        </div>
  </div>
  </div>
  </section>
  </main>

@endsection