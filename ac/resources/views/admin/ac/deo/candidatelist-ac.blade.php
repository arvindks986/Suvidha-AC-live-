@extends('admin.layouts.ac.report-theme')
@section('title', 'Create Schedule')
@section('content') 
  <?php  $st=getstatebystatecode($st_code);
		$dist=getdistrictbydistrictno($st_code,$dist_no);
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
  <div class="card text-left mt-3" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4>Candidate List AC Wise</h4></div> </div>
			<hr/>
			<div class="row">
			<div class="col-md-1"> <button type="submit" class="btn btn-primary"><a class="text-white" href="{{url('/acdeo/nomination-report')}}">Back</a></button></div>
              <div class="col">
			 
			  <p class="mb-0 text-right">
			  <b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
			  <b>District Name:</b> 
			  @if(isset($dist) && $dist)
			  <span class="badge badge-info">{{$dist->DIST_NAME}}</span> @endif &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
              <a class="btn btn-primary" href="{{ url('/acdeo/reportexcel1/'.base64_encode('all').'/'.base64_encode($ac_no)) }}" title="Download Excel" target="_blank">Export Excel</a>
             

                       </p>
              </div>
            </div>
      </div>
  
 <div class="card-body">  
    <table   class="table table-striped table-bordered" style="width:100%">
         <thead>
        <tr>
          <th>Serial No</th> 
          <th>AC Number&Name</th> 
          <th>Candidate Name</th>
          <th>Candidate Name Hindi</th> 
          <th>Party Name</th> 
          <th>Symbol</th>
		  <th>Affidavit</th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 1; 
       
       if(!empty($candListbyAC)){ ?>
       
         @foreach($candListbyAC as $candListbyACData)
         <?php
          $candidatedetails=getById('candidate_personal_detail','candidate_id',$candListbyACData->candidate_id);
          $partyDetails=getById('m_party','CCODE',$candListbyACData->party_id);
          $acDetails=getacbyacno($candListbyACData->st_code,$candListbyACData->ac_no);
          $symbolDetails=getsymbolbyid($candListbyACData->symbol_id);
		  $affidviturl = getAffidvitById($candListbyACData->nom_id ,$candListbyACData->candidate_id);
		  if(!empty($affidviturl->affidavit_path)){
			  $affidviturl_with_prefix = 'https://suvidha.eci.gov.in/suvidhaac/public/'.$affidviturl->affidavit_path;
		  }else{
			  $affidviturl_with_prefix = '';
		  }
		  
         ?>@if(isset($symbol_data)) {{$symbol_data->SYMBOL_DES}} @endif
          <tr>
            <td>{{$count}}</td>  
            <td>{{ $candListbyACData->ac_no.' - '.$acDetails->AC_NAME}}</td>
            <td><a href="{{url('/acdeo/ViewNominationDetails/'.$candListbyACData->nom_id.'/')}}">{{ $candidatedetails->cand_name}}</a></td>
            <td > @if(!empty( $candidatedetails->cand_hname)){{  $candidatedetails->cand_hname }} @endif</td>
            <td >@if(isset($partyDetails)){{ $partyDetails->PARTYNAME }}  @endif</td>
            <td >@if(isset($symbolDetails)) {{$symbolDetails->SYMBOL_DES}} @endif</td>
			<td >@if(isset($affidviturl) && !empty($affidviturl)) <a class="btn btn-primary" target="_blank" href="{{$affidviturl_with_prefix}}">View Affidavit</a> @else No Affidavit Uploaded @endif</td>
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
