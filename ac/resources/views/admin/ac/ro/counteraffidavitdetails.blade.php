@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'Counter Affidavit details')
@section('content')
 <?php   
          $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
      th, td { white-space: nowrap;}
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 <main role="main" class="inner cover mb-3">
  
  <div class="container">
  <div class="row">
  					
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4>Candidate Counter Affidavit Details</h4> </div> 
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
        
   <table   class="table table-striped table-bordered" style="width:100%">
        <thead> <tr> <th>Sl. No.</th><th>Candidate Name</th><th>Candidate Details</th><th>Counter Affidavit Details</th></tr></thead>
        <tbody>@if(isset($list))
            @foreach($list as $lis)   
            <?php $j++; 
                    $nom=getById('candidate_nomination_detail','nom_id',$lis->nom_id);
                    $cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
                    $affidavit=getById('candidate_counteraffidavit_detail','id',$lis->id);
                    $party=getpartybyid($nom->party_id);

              ?>      
        <tr><td>{{$j}}</td><td>Nom Id :-{{$lis->nom_id}}-{{$cand->cand_name}} <br> S/O or H/O:-{{$cand->candidate_father_name}}</td><td>Adderss:- {{$cand->candidate_residence_address}} <br>Party:- {{$party->PARTYABBRE}}-{{$party->PARTYNAME}}</td>
           
          <td>@if(!empty($affidavit->affidavit_name)) <a href="{{asset($affidavit->affidavit_path)}}" download>Counter Affidavit</a>@else No Affidavit @endif</td></tr>
 
           
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