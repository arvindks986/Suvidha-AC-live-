@extends('admin.layouts.pc.theme')
@section('title', 'Create Schedule')
@section('content')

  <style type="text/css">
      th, td { white-space: nowrap;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
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

    <section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Symbol Wise Duplicate Candidate Reports</h2></div>

          <div class="col"><p class="mb-0 text-right"><b>State Name:</b> 
            <span class="badge badge-info"><?php
            $candidatedetails=getById('m_state','ST_CODE',$st_code);
                 echo $candidatedetails->ST_NAME;
                 ?></span> &nbsp;&nbsp; <b>PC Name:</b> 
            <span class="badge badge-info">New Delhi</span>&nbsp;&nbsp;</p></div>
                </div>
                </div>
                <div class="card-body">  
 
  <div class="table-responsive">
  <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
      <thead>
        <tr>
          <th>Symbol Name</th>
          <th>Candidate Name</th>
          <th>Party</th>

          
        </tr>
        </thead>
        <tbody>
          @foreach($lists as $list) 
          <tr>
    <?php 
          $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
          $symbol_data=getsymbolbyid($list->symbol_id);
          $partyDetails=getById('m_party','CCODE',$list->party_id);
  
    ?>
            <td>@if(isset($symbol_data)) {{$symbol_data->SYMBOL_DES}} @endif</td>
            <td>@if(isset($candidatedetails)) {{$candidatedetails->cand_name}} @endif</td>
            <td>@if(isset($partyDetails)) {{$partyDetails->PARTYNAME}} @endif</td>   
            
          </tr>
            @endforeach 
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