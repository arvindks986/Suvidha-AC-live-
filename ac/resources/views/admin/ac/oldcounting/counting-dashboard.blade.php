@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Counting Dashboard')
@section('content')
 <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
         
    ?>
<section class="mt-5">
  <div class="container">
    <div class="row">
  <div class="card">
  <div class="card-body">
      
           
     <div class="form-group float-left"> 
           <table class="table table-bordered table-hover" style="width:100%">
                <thead><tr> <th>Leading Candidate</th>
                      <th>Leading Party</th>
                      <th>Trailing Candidate</th>
                      <th>Trailing Party</th>
                      <th>Leading Cand. Votes</th>
                      <th>Trailing Cand. Votes</th>
                      <th>Margin</th></tr> </thead>
              @if(isset($winn_data))
               <tbody><tr><td class="sticky-cell">@if($winn_data->lead_cand_name!='') {{$winn_data->lead_cand_name}} @endif</td>  
                <td class="sticky-cell">{{$winn_data->lead_cand_party}}</td>  
                <td class="sticky-cell">{{$winn_data->trail_cand_name}}</td>  
                <td class="sticky-cell">{{$winn_data->trail_cand_party}}</td>  
                <td class="sticky-cell">{{$winn_data->lead_total_vote}}</td> 
                <td class="sticky-cell">{{$winn_data->trail_total_vote}}</td> 
                  
                <td class="sticky-cell">{{$winn_data->margin}}</td>  </tr>   
               </tbody>
              @endif
          </table>
     </div>           

     
  </div>
  </div>
  </div>
  <div class="row">
     
      
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 
          <div class="col form-inline"><h6 class="mr-auto">Rounds Wise Entry Reports</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  </p></div>
                </div>
                </div>
                <div class="card-body">  
   
 <div class="table-responsive">
    @if(!$master_data->isEmpty())
  <table class="table table-bordered table-hover" style="width:100%">
        <thead>
		<tr>
			<th>Sr. No</th>
			<th data-breakpoints="xs sm">Candidate Name</th>
			<th>Party</th>
      <th>EVM Votes</th>
      <th>Postal Votes</th>
			<th>Total Votes</th> 
		</tr>
        </thead>
        <tbody>
            <?php $j=0; $evm_votes=0; ?>
              
            @foreach($master_data as $md)  
              <?php $j++;   
                     $evm_votes=$md->total_vote-$md->postalballot_vote;  
                  
              ?>
            
              <tr><td>{{$j}}</td><td>{{$md->candidate_name}} <b>Demo</b><br>{{$md->candidate_hname}}   </td>   
                                  <td>{{$md->party_name}} <b>Demo </b><br>{{$md->party_hname}} </td> 
			            
                <td>{{$evm_votes}}  </td>
                <td>{{$md->postalballot_vote}}  </td>
                <td>{{$md->total_vote}}  </td>
          </tr>

            @endforeach 
                    <tr><td colspan="2">&nbsp;</td> 
                            <td colspan="2"><b> Rejected Votes</b> </td>   
                            <td>@if(isset($round_details)){{$round_details->rejected_votes}}  @endif</td><td>&nbsp;</td></tr>
                            <tr><td colspan="2">&nbsp;</td> 
                            <td colspan="2"><b> Postal Total Votes</b> </td>   
                            <td>@if(isset($round_details)){{$round_details->postal_total_votes}} @endif</td><td>&nbsp;</td></tr>
             </tbody>
     
    </table>
    @else
               <p> Counting Data Not exit! Ro is not activate  for counting</p>
            @endif 
     <!-- end reponcive-->
   </div>
                </div>
              </div>
  
  
  </div>
  </div>
  </section>
 


@endsection