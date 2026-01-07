@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Rounds Wise Entry Details')
@section('content')
<?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
         
    ?>
<style type="text/css">
.table-dot td, .table-dot th {white-space: nowrap;}

hr {
    margin-top: 0;
    margin-bottom: 0;   
}
</style>
  


    
<section class="form-style">
<div class="container-fluid">
<div class="row">
	<table class="table table-bordered table-result" style="width:100%">              
                @if(isset($winn_data))
               <tbody>
		   <tr>
			<td colspan="3" class="td-green"><h5>@if(isset($winn_data))@if($winn_data->status==0) Leading @else Winning @endif @endif Candidates</h5></td>
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
                    @if($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                          <b> (Tie)  </b>
                                @endif
                    @if($winn_data->status==1) <b>Won </b>  @endif

                </td></tr>    
               </tbody>
               @endif
          </table>
   
	
  </div>
  </div>
 </div> 

  <div class="container-fluid mt-3">
  <div class="row">   
  <div class="card text-left" style="width:100%; margin:0 auto;">
                 <div class=" card-header">
                <div class=" row">
				 
         <div class="col form-inline"><h6 class="mr-auto">Rounds Wise Entry Reports</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  </p></div>
                </div>
                </div>
  		
 <div class="sticky-table sticky-ltr-cells">
    @if(!$result->isEmpty())
  <table class="table table-bordered table-hover" style="width:100%">
        <thead>
  
    <tr class="sticky-header">
      <th class="sticky-cell">Sr. No</th>
      <th class="sticky-cell cand_name" data-breakpoints="xs sm">Candidate Name</th>
      <th class="sticky-cell cand_name">Party</th>  
      @if(isset($rounds))    
        @for($k=1; $k<=$rounds->scheduled_round; $k++)
      <th data-breakpoints="xs sm md lg">  Round&nbsp;&nbsp; {{$k}}</th>
        @endfor
      @endif
         <th data-breakpoints="xs sm md lg">Postal Votes</th>
          <th class="sticky-cell-opposite">Total Votes</th>  
    </tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
           
            @foreach($result as $md)  
              <?php $j++;   
                    
                  
              ?>
            
              <tr><td class="sticky-cell">{{$j}}</td> 
           <td class="sticky-cell">{{$md->candidate_name}} <br>{{$md->candidate_hname}} 
                                     @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 
                  </td>   
                                  <td class="sticky-cell">{{$md->party_name}} <br>{{$md->party_hname}} </td>   
                @if(isset($rounds))
                 @for($k=1; $k<=$rounds->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>
                @endfor 
                @endif
                <td>{{$md->postalballot_vote}}</td> 
                <td class="sticky-cell-opposite">{{$md->total_vote}}   
                                      @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 
                        </td></tr>

            @endforeach 
            
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
  </section>
 


@endsection
  