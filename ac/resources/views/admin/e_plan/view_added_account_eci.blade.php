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




<section class="color-white statistics dashboard p-2" style="border-bottom:1px solid #eee;">
    <div class="container-fluid">
      
          <form  method="post" action="{{url('eci/view_account_info')}}">
          @csrf
         <div class="row">   
          <div class="form-group col-sm-3 col-12">
            <label>Select State:</label>
                <select name="st_codeee" class="form-control" name="st_code_select" id="st_code_select"onChange="getDistrict(this.value);">
                <option value="">All States</option>
                @foreach ($st_code_array as $st_list)					
                    <option  <?php if($selected_state == $st_list->ST_CODE) { ?>  selected <?php } ?> value="{{ $st_list->ST_CODE }}">{{ $st_list->ST_CODE }} - {{ $st_list->ST_NAME }} </option> 
                @endforeach
                </select>
           </div>
           <div class="form-group  col-sm-3 col-12"> 
           <label>Select District:</label>
            <select class="form-control" name="dist_no" id="ac" placeholder="Select AC">
	      	    <option value="">Select District</option> 
                @if(count($districtlist) > 0)
                @foreach ($districtlist as $dist)					
				<option <?php if($selected_district == $dist->DIST_NO) { ?>  selected <?php } ?> value="{{ $dist->DIST_NO }}">{{ $dist->DIST_NO }} - {{ $dist->DIST_NAME }} </option> 
				 @endforeach
                 @endif   	
	        </select>
         </div>
         
         <div class="form-group  col-sm-3 col-12"> 
           <label>Select Account:</label>
            <select class="form-control" name="acc_for"  placeholder="Select Account For">
	      	    <option value="">Select Account For</option> 
                				<option <?php if($selected_acc_type == 1) { ?> selected <?php } ?> value="1">Online Nomination</option>
                        <option <?php if($selected_acc_type == 2) { ?> selected <?php } ?> value="2">Duplicate Epic</option> 
					
	        </select>
         </div>
         <div class="col-sm-3 col-12">
            <div class="mt-2">&nbsp;</div>
            <input type="submit" name="submit" class="btn btn-primary">
          </div>
        </div>  
        </form> 
      
  </div>
</section>





<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> Account List</div>
            
            <div class="col text-right">
            <p class="mb-0  ">
              <a target="_blank" href="{{url('eci/getcountreport')}}" class="btn btn-primary float-right">Account Information Count Report</a>
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
              <?php //print_r($account_data_merge); die; ?> 		
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
						<tr><td colspan="10" align="center">No record found</td></tr> 
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
function getDistrict(val) {
   
	$.ajax({
	type: "GET",
	url: "ajaxdistrictcall",
	data:'st_code='+val,
	success: function(data){
		$("#ac").html(data);
	}
	});
}
</script>
@endsection
