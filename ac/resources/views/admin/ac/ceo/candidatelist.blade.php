@extends('admin.layouts.ac.report-theme')
@section('title', 'Create Schedule')
@section('content') 
  <?php  $st=getstatebystatecode($st_code);   
       /* if($ele_details[0]->CONST_TYPE=="PC")
          $pc=getpcbypcno($st_code,$pc_no);*/
        //$j=0;
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

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4>Candidate List PC Wise</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
              <a href="{{url('pcceo/ceo-candidatelist-pdf/'.$pc_no.'/')}}" class="btn btn-info" role="button">Export PDF</a> &nbsp;&nbsp;
              <a href="{{url('pcceo/ceo-candidatelist-excel/'.$pc_no.'/')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <!--<a href="{{url('pcceo/ceo-candidatelist-excel/'.$pc_no.'/')}}" class="btn btn-info" role="button">Export Summary</a> &nbsp;&nbsp;-->
            </p>
              </div>
            </div>
      </div>
  
 <div class="card-body">  
    <table   class="table table-striped table-bordered" style="width:100%">
         <thead>
        <tr>
          <th>Serial No</th> 
          <th>PC Number&Name</th> 
          <th>Candidate Name</th> 
          <th>Party Name</th> 
          <th>Symbol</th> 
        </tr>
        </thead>
        <tbody>
        <?php $count = 1; 
       
       if(count($candListbyPC)>1){ ?>
       
         @foreach($candListbyPC as $candListbyPCData)
         <?php
          $candidatedetails=getById('candidate_personal_detail','candidate_id',$candListbyPCData->candidate_id);
          $partyDetails=getById('m_party','CCODE',$candListbyPCData->party_id);
          $pcDetails=getpcbypcno($user_data->st_code,$candListbyPCData->pc_no);
          $symbolDetails=getsymbolbyid($candListbyPCData->symbol_id);
         // print_r( $candidatedetails);
         ?>@if(isset($symbol_data)) {{$symbol_data->SYMBOL_DES}} @endif
          <tr>
            <td>{{$count}}</td>  
            <td >{{ $candListbyPCData->pc_no.' - '.$pcDetails->PC_NAME}}</td>
            <td >{{ $candidatedetails->cand_name}}</td>
            <td >@if(isset($partyDetails)){{ $partyDetails->PARTYNAME }}  @endif</td>
            <td >@if(isset($symbol_data)) {{$symbol_data->SYMBOL_DES}} @endif</td>
          </tr>
          <?php $count++ ?>
          @endforeach
          <?php } else { ?>
          <tr>
            <td class="col-md-6" colspan='6'> <p>No Records  Founds </p></td>
          </tr>   
          <?php }  ?>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>
 
@endsection
