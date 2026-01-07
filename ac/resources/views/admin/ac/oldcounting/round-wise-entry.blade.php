@extends('admin.layouts.ac.theme')
@section('title', 'Create Schedule')
@section('content')
 <?php  $st=getstatebystatecode($ele_details->ST_CODE);  
         $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);  
         
    ?>
   
<main role="main" class="inner cover mb-3">

    <section class="mt-5">
  <div class="container">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 
          <div class="col form-inline"><h6 class="mr-auto">Rounds Wise Entry Reports</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span>  &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span></p></div>
                </div>
                </div>
                <div class="card-body">  
 
  <div class="table-responsive">
  <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead><tr><th>Sr. No</th><th>Candidate Name</th><th>Party</th>
                @for($k=1; $k<=$round_details->scheduled_round; $k++)
                  <th>Round{{$k}}</th>
                @endfor
                <th>Total Votes</th> </tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
              <?php $j++;   
                   $nom=getById('candidate_nomination_detail','nom_id',$md->nom_id);
                       if(!empty($nom)){
                       $cand=getById('candidate_personal_detail','candidate_id',$md->candidate_id); 
                       $party=getById('m_party','CCODE',$nom->party_id); 
                      }
                  
              ?>
            
              <tr><td>{{$j}}</td> <td>@if(!empty($cand)) {{$cand->cand_name}} @endif</td> <td>@if(!empty($party->PARTYNAME)) {{$party->PARTYNAME}} @endif</td>  
                 @for($k=1; $k<=$round_details->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>
                @endfor 
                
                <td>{{$md->total_vote}}</td> </tr>

            @endforeach 
            @endif 
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