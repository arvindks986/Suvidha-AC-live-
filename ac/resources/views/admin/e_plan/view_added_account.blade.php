@extends('admin.central.common.theme')
@section('title', 'Descriptive Election Period Report')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => '#',
    'name' => 'Account List'
  ]; 
  ?>
  
@section('content')
@php
	$prefix = '';
	$result = array();
	if(Auth::user()->role_id == '7'){
		$prefix 	= 'eci';
	}
@endphp


<style>	

.bolds{
	font-weight: bold;
	.SumoSelect {
    width: 450px !important;
}
}

.form-group label {
    float: left;
    text-align: left;
    font-weight: normal;
}

.form-group select {
    display: inline-block;
    width: 400px;;
    vertical-align: middle;
}
</style>

<section class="color-white statistics dashboard p-2" style="border-bottom:1px solid #eee;">
    <div class="container-fluid">
      <div class="row">  
        <div class="col-md-12">
          <form  method="post" action="{{url('acceo/ep/view_added_account')}}">
          @csrf
            
          <div class="form-group">
            <label>Select District:
                <select name="dist_no" class="form-control">
                <option value=''>Select District</option>
                
                @foreach ($districtlist as $dist)					
					        <option <?php if($selected_dist_no == $dist->DIST_NO) { ?>  selected <?php } ?> value="{{ $dist->DIST_NO }}">{{ $dist->DIST_NO }} - {{ $dist->DIST_NAME }} </option> 
				        @endforeach
                    
                </select>
                <input type="submit" name="submit" class="btn btn-primary">
            </label> 
         </div>
          
        </form> 
      </div>
      
    </div>
  </div>
</section>



<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4>District Wise Account List</div>
            <div class="col">
			
			<p class="mb-0 text-right">
              <button class="btn btn-success" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle"></i> Add Account Info</button> 
            </p>
           
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <!-- Content goes Here -->

		<table class="table table-bordered table-striped" style="width: 100%;" id="example">
			<thead>
				<tr>
					<th>Sr.No.</th>
					<th>State name</th>
					<th>Dist No</th>
					<th>Account For</th>
					<th>Account Name</th>
					<th>Mobile Number</th>
					<th>Account Email</th>
          <th>Account Number</th>
					<th>Account Type</th>
					<th>Account IFSC</th>
					<th>Account Benificiary</th>
					
				</tr>
			</thead>
			  <tbody>													
        @if(count($account_data_merge) > 0)
        @php $i = 1; @endphp
        @foreach($account_data_merge as $key => $value)
		<?php $stname = getstatebystatecode($value['st_code']); 
              
              
              ?>
                <tr>
                  <td>{{$i}}</td>
				  
                  <td>{{$stname->ST_NAME}}</td>
				   @if($value['dist_no'] == 0)
                  <td>NA</td>
                  @else
                  <td>{{$value['dist_no']}}</td>
                  @endif
				  
                  @if($value['account_payment_for'] == 1)
                  <td>Online Nomination</td>
                  @elseif($value['account_payment_for'] == 2)
                  <td>Duplicate Epic</td>
                  @endif
                  <td>{{$value['account_name']}}</td>
                  <td>{{$value['account_mobile']}}</td>
                  <td>{{$value['account_email']}}</td>
                  <td>{{$value['account_number']}}</td>
                  @if($value['account_type'] == 1)
                  <td>Current</td>
                  @elseif($value['account_type'] == 2)
                  <td>Saving</td>
                  @endif
                  <td>{{$value['account_ifsc']}}</td>
                  <td>{{$value['account_benificeary']}}</td>
                </tr>
          @php $i++; @endphp
          @endforeach
					@else 
						<tr><td colspan="9" align="center">No record found</td></tr> 
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

@endsection
