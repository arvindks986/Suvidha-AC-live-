@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<style type="text/css">
  .heading th{
    text-transform: capitalize;
    text-align: left;
  }
  .complain-heading-main{
    text-transform: capitalize;
    text-align: center;
  }
</style>

<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  
          <div class="form-group col-md-3"> <label>State</label> 
          
            <select name="st_code" id="st_code" class="form-control" onchange ="filter('1')">
              <option value="">Select State</option>
            @foreach($states as $iterate_state)
				@if($st_code == $iterate_state->st_code)
					<option value="{{$iterate_state->st_code}}" selected="selected" >{{$iterate_state->st_name}}</option> 
				@else 			
					<option value="{{$iterate_state->st_code}}">{{$iterate_state->st_name}}</option>
				@endif            
            @endforeach
        
            </select>
          </div>

          <div class="form-group col-md-3"> <label>AC </label> 
          
            <select name="ac_no" id="ac_no" class="form-control" onchange ="filter('2')">
				<option value="">Select AC</option>
				@foreach($acs as $result) 
					@if($ac_no == $result->ac_no)
						<option value="{{$result->ac_no}}" selected="selected">{{$result->ac_no}}-{{$result->ac_name}}</option> 
					@else 			
						<option value="{{$result->ac_no}}" >{{$result->ac_no}}-{{$result->ac_name}}</option>
					@endif                 
				@endforeach       
            </select>
          </div>
		  
		  
		  <div class="form-group col-md-3"> <label>Candidate Name</label> 
          
            <select name="candidate_id" id="candidate_id" class="form-control" onchange ="filter('3')">
            <option value="">Select Candidate</option>
            @foreach($candidate as $result)
				@if($candidate_id == $result->candidate_id)
					<option value="{{$result->candidate_id}}" selected="selected">{{$result->cand_name}}</option> 
				@else
					<option value="{{$result->candidate_id}}">{{$result->cand_name}}</option>  
				@endif
            @endforeach
        
            </select>
          </div>
         
        </form>   
  
    
  </div>
</section>
<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   
 


		<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>De-Finalize Log Report</h4></div> 
                  <div class="col"><p class="mb-0 text-right">			
						<label class="mr-3"><b>Report: </b></label>
						<a href="{{url('eci/de-finalize-log/pdf')}}" target="_blank"><button type="button" class="btn btn-primary">Export PDF</button></a>
						<a href="{{url('eci/de-finalize-log/excel')}}" target="_blank"><button type="button" class="btn btn-success">Export CSV</button></a>
						</p>
				  </div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table-strip" style="width: 100%;" border="1" align="center">
         <thead>
         <tr>
          <th>Sl No</th>
          <th>State Name</th> 
          <th>AC No - AC Name</th> 
          <th>Candidate Name</th> 
          <th>Gender</th> 
          <th>Age</th> 
          <th>Category</th> 
          <th>Party Name</th> 
          <th>Symbol</th> 
          <th>Updated By</th> 
          <th>Updated At</th>        
        </tr>
        </thead>
        <tbody>
        @php  

        $count = 1;
         @endphp

        @forelse($results as $result)
          <tr>
            <td>{{ $count }}</td>
            <td>{{ $result->st_name }}</td>
            <td>{{ $result->ac_no }} - {{ $result->ac_name }}  </td>
			<td>{{ $result->cand_name }}  </td>
            <td>{{ ucfirst($result->cand_gender) }}  </td>
            <td>{{ $result->cand_age }}  </td>
            <td>{{ strtoupper($result->cand_category) }}  </td>
            <td>{{ $result->party_name }}  </td>
            <td>{{ $result->symbol_name }}  </td>
            <td>{{ $result->log_updated_by }}  </td>
            <td>{{ date('d-m-Y h:i A', strtotime($result->log_updated_at)) }}  </td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="11">No Data Found For Index Card Finalize Statusss</td>                 
              </tr>
          @endforelse
        </tbody>
    </table>
         </div><!-- End Of  table responsive -->  
       </div>
     </div>
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
</section>
</main>


<script type="text/javascript">
function filter(st){
  var url = "<?php echo $current_page ?>";
  var query = '';
    
    if($("#st_code").val() != ''){
      query += '&st_code='+$("#st_code").val();
    }
	
	if(st == '2'){
		if($("#ac_no").val() != ''){
		  query += '&ac_no='+$("#ac_no").val();
		}
	}
	
	if(st == '3'){
		if($("#ac_no").val() != ''){
		  query += '&ac_no='+$("#ac_no").val();
		}
		if($("#candidate_id").val() != ''){
		  query += '&candidate_id='+$("#candidate_id").val();
		}
	}
		
    window.location.href = url+'?'+query.substring(1);
}
</script>
@endsection