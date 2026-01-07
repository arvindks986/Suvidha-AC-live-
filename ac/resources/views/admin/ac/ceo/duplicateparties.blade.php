@extends('admin.layouts.pc.report-theme')
@section('title', 'Create Schedule')
@section('content') 
  <?php  $st=getstatebystatecode($user_data->st_code);   
       /* if($ele_details[0]->CONST_TYPE=="PC")
          $pc=getpcbypcno($st_code,$pc_no);*/
        $j=0;
  ?> 
<style type="text/css">
      th, td { white-space: nowrap;}
        .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;}
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {background: #ff9800;}
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 <main role="main" class="inner cover mb-3">
  <section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
  <div class=" card-header">
  <div class=" row">
  <div class="col"><h4>Duplicate Partywise Candidate</h4></div> 
  <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
  <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('pcceo/ceo-duplicateparty-pdf')}}" class="btn btn-info" role="button">Export PDF</a>
  &nbsp;&nbsp; <a href="{{url('pcceo/ceo-duplicateparty-excel')}}" class="btn btn-info" role="button">Export Excel</a></p></div></div></div>
  <div class="card-body">  
  <table class="table table-striped table-bordered" style="width:100%">
   <thead><tr><th>PC No.</th><th>PC Name</th><th>Candidate Name</th><th>Party Name</th></tr></thead>
        <tbody>
          <?php foreach($duplicatePartyList as $duplicatePartyListData) {
            $candidatedetails=getById('candidate_personal_detail','candidate_id',$duplicatePartyListData->candidate_id);
            $partyDetails=getById('m_party','CCODE',$duplicatePartyListData->party_id);
            $pcDetails=getpcbypcno($user_data->st_code,$duplicatePartyListData->pc_no);
           //print_r($candidatedetails);
           ?>
          <tr>
            <td>{{$duplicatePartyListData->pc_no}}</td>  
            <td>{{$pcDetails->PC_NAME}}</td>
            <td>{{$candidatedetails->cand_name}}</td>
            <td>{{$partyDetails->PARTYNAME}}</td> 
          </tr>
          <?php } ?>
        </tbody>
     
    </table>
    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  </main>
 
@endsection
