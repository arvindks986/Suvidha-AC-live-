@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Poll Day Schedule')
@section('content')
 <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
         $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
          $url = URL::to("/"); $j=0;
    ?>
 
 <main role="main" class="inner cover mb-3">
  
  <div class="container-fluid mt-3">
  <div class="row text-center mb-3">
   <div class="col">
   <span class="">
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$totalturnout_per}}%</span>
   <br />
				 <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Voter Turn Out</span></span>
  </div></div>
  <div class="row text-center">
								<div class="col">
				
				 
				 <span type="text" class="btn btn-outline-dark outlinDark">Female 
				 <span class="badge badge-light">{{$femaleturnout_per}}%</span>
				 </span>  
				 
				 <span type="text" class="btn btn-outline-dark outlinDark">Male 
				 <span class="badge badge-light">{{$maleturnout_per}}%</span>
				 </span>  <span type="text" class="btn btn-outline-dark outlinDark">Others 
				 <span class="badge badge-light">{{$othersturnout_per}}%</span>
				 </span>  
				
				 
				 </div>
							</div>
  <div class="row">
  					
						
						
					
					
  <div class="card text-left mt-5" style="width:100%;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4>Poll  Day Turnout Details </h4> </div> 
          <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b>AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp; 
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
         @if (\Session::has('success'))
			<div class="alert alert-success">
				<ul>
					<li>{!! \Session::get('success') !!}</li>
				</ul>
			</div>
		@endif
      
         
    </div>
    </div>
   		 
    <div class="card-body">
    <div class="table-responsive">  
        
   <table   class="table table-striped table-bordered" style="width:100%">
        <thead> <tr> <th rowspan="2">Sl. No.</th><th rowspan="2"> AC  Name</th>
          <th colspan="4" align="center">Electors</th> 
          <th colspan="4" align="center">End of Poll (Poll Start to End)</th></tr>
          <tr>  <th>Male</th> <th>female</th><th>Other</th><th>total</th> 
            <th>Male</th><th> female</th><th>Other</th><th>total</th></tr>
        </thead>  
        <tbody>@if(isset($lists))
            @foreach($lists as $list)   
            <?php $j++; 
                    $ac=getacbyacno($ele_details->ST_CODE,$list->ac_no);
                    $ele=getcdacelectorsdetails($ele_details->ST_CODE,$list->ac_no,'2019');
              ?>      
        <tr><td>{{$j}}</td><td>{{$ac->AC_NO}}- {{$ac->AC_NAME}}</td>
            <td>@if(isset($ele)) {{$ele->electors_male}} @endif</td><td>@if(isset($ele)){{$ele->electors_female}} @endif</td> <td>@if(isset($ele)){{$ele->electors_other}} @endif</td><td>@if(isset($ele)){{$ele->electors_total}} @endif</td>
            <td>{{$list->end_voter_male}}</td><td>{{$list->end_voter_female}}</td> <td>{{$list->end_voter_other}}</td><td>{{$list->end_voter_total}}</td></tr>
 
           
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
  </main>
 
@endsection
 @section('script')

<script type="text/javascript">
   $(document).ready(function () {  
  //called when key is pressed in textbox
   
  $("#election_form").submit(function(){
      
      if($("#candidate_id").val()=='')
          {  
          $("#errmsg").text("");
          $("#errmsg").text("Please select Candidate");
          $("#candidate_id").focus();
          return false;
          }
    if($("#counteraffidavit").val()=='')
          {  
          $("#errmsg").text("");
          $("#errmsg1").text("Please select pdf file");
          $("#counteraffidavit").focus();
          return false;
          }
      

 
    });
});
 </script>
 @endsection