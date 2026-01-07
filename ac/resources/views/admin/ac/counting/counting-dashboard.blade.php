@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Counting Dashboard')
@section('content')
 <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
       
    ?>
	@if(Session::has('success_admin'))
      <div class="alert alert-success mb-3"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
    @endif 
     @if(Session::has('error_mes'))
     <div class="alert alert-danger mb-3"><strong> {{ nl2br(Session::get('error_mes')) }}</strong></div>
    @endif 
 <section class="form-style">
  <div class="container-fluid">
    <div class="row">
	<table class="table table-bordered table-result" style="width:100%">              
                 
               <tbody>
       <tr>
      <td colspan="3" class="td-green"><h5> @if(isset($winn_data))@if($winn_data->status==0) Leading @else Winning @endif @else Leading @endif Candidates</h5></td>
      <td colspan="3" class="td-ornage"><h5>Trailing Candidate</h5></td>
      <td>Margin</td>
      
       </tr>
       
       <tr>
        <td class="td-green-light"><b>Candidate</b>@if(isset($winn_data)){{$winn_data->lead_cand_name}}@endif</td>  
                <td class="td-green-light"><b>Party</b>@if(isset($winn_data)){{$winn_data->lead_cand_party}}@endif</td>  
        <td class="td-green-light"><b>Candidate Votes</b>@if(isset($winn_data)){{$winn_data->lead_total_vote}}@endif</td>
                <td class="td-ornage-light"><b>Candidate</b>@if(isset($winn_data)){{$winn_data->trail_cand_name}}@endif</td>  
                <td class="td-ornage-light"><b>Party</b>@if(isset($winn_data)){{$winn_data->trail_cand_party}}@endif</td>                   
                <td class="td-ornage-light"><b>Candidate Votes</b>@if(isset($winn_data)){{$winn_data->trail_total_vote}}@endif</td>  
                <td><h3>@if(isset($winn_data)){{$winn_data->margin}}@endif</h3> 
                   @if(isset($winn_data)) @if($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                          <b> (Tie)  </b>
                                @endif
                    @if($winn_data->status==1) <b>Won </b>  @endif
                 @endif
                </td></tr>    
               </tbody>
               
          </table>
   

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
<div class="sticky-table sticky-ltr-cells">
    @if(!$master_data->isEmpty())
  <table class="table table-bordered table-hover" style="width:100%">
        <thead>
    <tr class="sticky-header">
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
                     // $evm_votes=App\adminmodel\ACCountingModel::evm_votes($new_table,$md->id,$md->nom_id);
                     // dd($evm_votes);
                     $evm_votes=evm_votes($new_table,$md->id,$md->nom_id);  
               ?>
            
              <tr><td>{{$j}}</td><td>{{$md->candidate_name}} <br>{{$md->candidate_hname}} 
                                        @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 
 </td>   
                                  <td>{{$md->party_name}} <br>{{$md->party_hname}} </td>   
                  
                <td>{{$evm_votes->grant_total}}  </td>
                <td>{{$md->postalballot_vote}}  </td>
                <td>{{$md->total_vote}}   @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 
 </td>
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
               <p> {!! $heading_title !!}</p>
            @endif 
     <!-- end reponcive-->
   </div>
                </div>
              </div>
  
  
  </div>
  </div>
  </section>
 


@endsection
 