@extends('admin.layouts.ac.report-theme')
@section('title', 'Electors Polling Stations')
@section('content') 
  <?php  
  $st=getstatebystatecode($user_data->st_code);
  $distArr=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
  $distheade='all';
  $j=0;
  ?> 
<style>
 input[type="number"] {
   
    max-width: 81px;
}
 td {
    padding: 3px!important; vertical-align:middle; font-size:11px;
}
th {
    text-align: left; padding:2px; font-size:14px;
}
.table td span {
    padding: 2px;
}
input{    border: none;
    height: 23px;
    margin-top: 5px;
    padding: 2px;
    font-size:12px;
    }
td:first-child {
    text-align: center;
    vertical-align: middle;
}
 .table-responsive::-webkit-scrollbar-track
{
	background-color: #f2f2f2;
}

.table-responsive::-webkit-scrollbar{
	height: 10px;
}


input.btn.btn-primary {
    height: auto;
}
 table, .table{margin-bottom:0px!important;}
 .table-responsive{}
 </style>
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
            <div class=" card-header">
                <div class=" row">
                  <div class="col"><h4>Electors Polling Station Info</h4></div> 
                   <div class="col"><p class="mb-0 text-right">
                   <b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                   <b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp; </p>
                   </div>
                </div>
            </div>
   <div class="row">
    <div class="col">
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
      @endif
    </div>
    </div>
       
    <div class="card-body">  
     <form class="form-horizontal" id="electorsPollingStation_form" method="POST"  action="{{url('acceo/electors-pollingstation') }}" >
        {{ csrf_field() }} 
       <table  id="acViewBody" class="table table-striped table-bordered" style="width:100%">
       <div class="" style="width:100%; margin:0 auto;"><span class="col">District Name:</span> 
			  <select name="dist_no"  id="dist_no" class="form-control party_id">
        <option value=" ">---Select District---</option>
					@foreach($all_dist as $distList)
					<option value="{{ $distList->DIST_NO }}" >{{ $distList->DIST_NAME }}</option>
        	@endforeach
          <option value="200">All District</option>
        </select>
       
		 		@if ($errors->has('dist_no'))
        <span style="color:red;">{{ $errors->first('dist_no') }}</span>
      	@endif
		  </div>
      <tbody>
        <!-- <thead>
           <tr>
            <th colspan="3"> No & Name </th>
            <th colspan="4">General Electors</th>
            <th colspan="4">Service Electors</th>
            <th colspan="3">Polling Stations</th>
           </tr>

            <tr>
            <th size="2">S.No.</th>
            <th>AC No</th>
            <th>AC Name</th>
            <th size="2">Male</th>
            <th size="2">Female</th>
            <th size="2">Third Gender</th>
            <th size="2">Total</th>

            <th size="2">Male</th>
            <th size="2">Female</th>
            <th size="2">Third Gender</th>
            <th size="2">Total</th>

            <th size="2">Regular</th>
            <th size="2">Auxillary</th>
            <th size="2">Total</th>
            </tr>
        </thead> -->
       
        </tbody>
    </table>
        <?php  $url = URL::to("/");  ?>
             <!-- <div class="form-group float-right">  
                <input type="submit" value="Save" class="btn btn-primary">
                <input type="button" value="Delete" class="btn btn-primary" onclick="location.href = '{{$url}}/ropc/counting-finalized';">
             </div> -->
        </form> 
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>
@endsection

<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script type="text/javascript">
   $(document).ready(function () {  
  //called when change the pc name
  jQuery("select[name='dist_no']").change(function(){
    var dist_no = jQuery(this).val();  
        jQuery.ajax({
            url: "{{url('/acceo/getaclistbydeo')}}",
            type: 'GET',
            data: {dist_no:dist_no},
            success: function(result){
            //  alert(result);
              $('#acViewBody').html(result);;
                }
            });
        });
});
 </script>