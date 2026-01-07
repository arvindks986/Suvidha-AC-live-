@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'Upload Candidate Counter Affidavit')
@section('content')
 <?php   
          $url = URL::to("/"); $j=0;
    ?>
  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
  <div class="row">
  					
  <div class="card mt-3" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4>Upload/View Candidate Counter Affidavit</h4> </div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
        @if (session('success'))
			     <div class="alert alert-success"> {{session('success') }}</div>
    		@endif
            @if(!empty($errors->first()))
        <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
      @endif 
         
    </div>
    </div>
   		
       
    <div class="card-border">  
       <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/counteraffidavit')}}" enctype="multipart/form-data" autocomplete='off'>
  {{csrf_field()}}
		<input type="hidden" name="affidavit_name" value="Counter"/>
	   <div class="row d-flex align-items-center ">
						<div class="col">
								<label for="candidate_id" class="col-form-label">Candidate Name <span class="errorred">*</span></label>
									<select name="candidate_id" id="candidate_id" class="form-control" required>
											<option value="">-- Select Candidate Name --</option>
											@foreach($cand_data as $candidate)
							           <?php  $cand=getById('candidate_personal_detail','candidate_id',$candidate->candidate_id); 
            				  if($cand->cand_name=="NOTA") continue; ?>      
															<option value="{{$candidate->nom_id}}" >{{$candidate->nom_id}}-{{$cand->cand_name}}-{{$cand->cand_mobile}}-C/o:-{{$cand->candidate_father_name}}</option>
														@endforeach
													</select>
													@if ($errors->has('candidate_id'))
                                     <span style="color:red;">{{ $errors->first('candidate_id') }}</span>
                                  @endif
													<span id="errmsg" class="text-danger"></span>
						</div>
											
										 
					<div class="col">
					<label for="counteraffidavit" class="col-form-label">Counter Affidavit File Only PDF <span class="errorred">*</span>(Maximum size 10 MB)</label>
					<div class="file-upload">
						<div class="file-select">
					   	<div class="file-select-name" id="noFile">No file chosen...</div> 
						<input type="file" name="counteraffidavit" id="counteraffidavit" class="custom-file-input affidavit form-control mr-auto" mutliple="true"  accept=".pdf">
						<div class="file-select-button customchoose" id="fileName">Choose File</div>
  </div>
</div>
					@if ($errors->has('counteraffidavit'))
                                     <span style="color:red;">{{ $errors->first('counteraffidavit') }}</span>
                                  @endif
								<span id="errmsg1" class="text-danger"></span>
							
						</div>
						<div class="col-md-1 p-0 m-0">
				<button type="submit" id="candnomination" class="btn btn-primary custombtn mt-2">Upload</button> </div>
				</div>
					 
			 
		</form>   
  
        

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  
  <section class="mt-3">
  <div class="container">
<div class="row">
<div class="card">
	<div class="card-body">
	
	
				 <table   class="table table-striped table-bordered datatable" style="width:100%">
					<thead> 
							<tr> <th>Sl. No.</th><th> Candidate Name</th><th>Party Name</th><th>No of Counter Affidavit</th><th>Counter Affidavit Details</th></tr></thead>
        <tbody>@if(!empty($cand_data))
             @foreach($cand_data as $list) 
            <?php $j++;   $cand=getById('candidate_personal_detail','candidate_id',$list->candidate_id); 
                    $cnt=countrecords('candidate_counteraffidavit_detail','nom_id',$list->nom_id); 
                    $affidavit=getById('candidate_counteraffidavit_detail','nom_id',$list->nom_id);
                    $party=getpartybyid($list->party_id);
            if($cand->cand_name=="NOTA") continue; ?>      
        <tr><td>{{$j}}</td> <td>Nom Id:-  {{$list->nom_id}}-{{$cand->cand_name}}-S/O or H/O:-{{$cand->candidate_father_name}}</td><td align="left">{{$party->PARTYABBRE}}-{{$party->PARTYNAME}}</td>
          <td>{{$cnt}}</td>
          <td> @if($cnt>0) <button type="submit" id="candnomination" class="btn btn-primary"  onclick="location.href='{{$url}}/roac/counteraffidavitdetails/{{$list->nom_id}}';" >Details</button> @else No Records @endif</td></tr>
 
           
            @endforeach 
            @endif 
        </tbody>
     
    </table>
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