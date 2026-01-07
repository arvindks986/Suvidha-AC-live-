@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'Upload Candidate Affidavit')
@section('content')
 <?php   
         $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
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
              .file-upload{width: 80%;}
  </style>

  <?php
  $getlastwithdrawl_stateACwise = getlastwithdrawl_stateACwise($ele_details->ST_CODE,$ele_details->CONST_NO);
   
    $countdate_btn= date('Y-m-d', strtotime($getlastwithdrawl_stateACwise->DATE_COUNT));
    $closeupdate_btn= date('Y-m-d', strtotime($getlastwithdrawl_stateACwise->LDT_WD_CAN));
    ?>
  
  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
<div class="row">
          
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>Upload Candidate Affidavit </h4> </div> 
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
         @if (\Session::has('success'))
      <div class="alert alert-success">
        <ul>
          <li>{!! \Session::get('success') !!}</li>
        </ul>
      </div>
    @endif
      
         
    </div>
    </div>
      <?php
                   if(date("Y-m-d") <= $closeupdate_btn) {
                    //if($closeupdate_btn >= date("Y-m-d") ){
                    $status=  '';
                    }else{
                      $status=  'disabled';
                    }
                    ?>
       
    <div class="card-border">  
       <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/verifycandidateaffidavit')}}" enctype="multipart/form-data" autocomplete='off'>
  {{csrf_field()}}
    <input type="hidden" name="affidavit_name" value="Form 26" id='test'/>
     
      <div class="row">
        <div class="col-md-12">
        
          
          <div class="row d-flex align-items-center ">
            <div class="col">
                <label for="candidate_id" class="col-form-label">Candidate Name <span class="errorred">*</span></label> &nbsp; &nbsp;
                <select name="candidate_id" id="candidate_id" class="form-control" <?=$status?>>
                  <option value="" class=>-- Select Candidate Name --</option>
                    @foreach($cand_data as $candidate)
                    <?php  $cand=getById('candidate_personal_detail','candidate_id',$candidate->candidate_id); 
                    if(@$cand->cand_name=="NOTA") continue; ?>      
                    <option value="{{$candidate->nom_id}}" @if($lastid==$candidate->nom_id) selected="selected" @endif >{{$candidate->nom_id}}-{{@$cand->cand_name}}-C/o:-{{$cand->candidate_father_name}}</option>
                    @endforeach
                </select>
                @if ($errors->has('candidate_id'))
                                     <span style="color:red;">{{ $errors->first('candidate_id') }}</span>
                                  @endif
                          <span id="errmsg" class="text-danger"></span> 
                </div>  
                
                      
    
          <div class="col">
          <label for="affidavit" class="col-form-label">Candidate Affidavit File Only PDF <span class="errorred">*</span> (Maximum size 10 MB)</label>
          <div class="file-upload">
            <div class="file-select">
              <div class="file-select-name" id="noFile">No file chosen...</div> 
            <input type="file" name="affidavit" id="affidavit" class="custom-file-input affidavit form-control mr-auto" accept=".pdf" <?=$status?>>
            <div class="file-select-button customchoose" id="fileName">Choose File</div>
  </div>
</div>
          @if ($errors->has('affidavit'))
                                     <span style="color:red;">{{ $errors->first('affidavit') }}</span>
                                  @endif
                <span id="errmsg1" class="text-danger"></span>
                
                
              </div>
      <div class="col-md-1 p-0 m-0">

        <button type="submit" id="candnomination" class="btn btn-primary custombtn" <?=$status?>>Upload</button>
      </div>
      
      </div>
          
          </div>
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
       <table class="table table-striped table-bordered table-hover" style="width:100%">
          <thead>
            <tr>
              <th>Sl. No.</th>
              <th>Candidate Name</th>
              <th>Party Name</th>
              <th>No of Affidavit</th>  
              <th>Affidavit Details</th>
            </tr>
          </thead>
          <tbody>@if(!empty($cand_data))
            @foreach($cand_data as $list)
            <?php $j++;   $cand=getById('candidate_personal_detail','candidate_id',$list->candidate_id); 
                          $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);
                          $party=getpartybyid($list->party_id);
                           $newid = base64_encode($list->nom_id);
                          //$cnt = countrecords('candidate_affidavit_detail', 'nom_id', $list->nom_id);
                           $cnt = countrecordsaffidavit('candidate_affidavit_detail', 'nom_id', $list->nom_id);
            if(@$cand->cand_name=="NOTA") continue; ?>
            <tr>
              <td>{{$j}}</td>
              <td>Nom Id-{{$list->nom_id}}-{{@$cand->cand_name}}-S/O or W/O :-{{@$cand->candidate_father_name}}</td>
              <td align="left">{{$party->PARTYABBRE}}-{{$party->PARTYNAME}}</td>
              <td>{{$cnt}} </td>

              <td> @if($cnt>0) <button type="submit" target="_blank" id="candnomination" class="btn btn-primary active" onclick="location.href='{{$url}}/roac/countffidavitdetails/{{$newid}}';">Details</button> @else No Affidavit @endif</td>
            </tr>
              <!-- <td> @if(!empty($affidavit->affidavit_name)) <a href="{{asset($affidavit->affidavit_path)}}"
                  download>{{$affidavit->affidavit_name}} </a>@else No Affidavit @endif </td> -->
            </tr>


            @endforeach
            @endif
          </tbody>
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
    if($("#affidavit").val()=='')
          {  
          $("#errmsg").text("");
          $("#errmsg1").text("Please select pdf file");
          $("#affidavit").focus();
          return false;
          }
      

 
    });
});
 </script>


 @endsection