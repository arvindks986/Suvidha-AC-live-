@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Round Wise Details')
@section('content')
<style type="text/css">
td, th {white-space: nowrap;}
</style>
<?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
         
    ?>
 <section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card">
	<div class="card-body">
		  
     <div class="form-group float-left"> 
           <table class="table table-bordered" style="width:80%">
                <thead><tr> <th width="20%">Leading Candidate</th>
                      <th >Leading Party</th>
                      <th width="20%">Trailing Candidate</th>
                      <th>Trailing Party</th>
                      <th>Leading Cand. Votes</th>
                      <th>Trailing Cand. Votes</th> 
                      <th>Margin</th></tr> </thead>
                @if(isset($winn_data))
               <tbody><tr><td class="sticky-cell">{{$winn_data->lead_cand_name}}</td>  
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
 <div class="sticky-table sticky-ltr-cells">
    @if(!$result->isEmpty())
  <table class="table table-bordered table-hover" style="width:100%">
        <thead>
  
		<tr class="sticky-header">
			<th class="sticky-cell">Sr. No</th>
			<th class="sticky-cell" data-breakpoints="xs sm">Candidate Name</th>
			<th class="sticky-cell">Party</th>			
				@for($k=1; $k<=$rounds->scheduled_round; $k++)
			<th data-breakpoints="xs sm md lg">  Round&nbsp;&nbsp; {{$k}}</th>
				@endfor
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
			     <td class="sticky-cell">{{$md->candidate_name}} <b>Demo</b><br>{{$md->candidate_hname}} @if($md->nom_id==$winn_data->nomination_id) <b>(Winning) </b>@endif
                                  @if($md->nom_id==$winn_data->trail_nomination_id) <b>(Trailing)</b>  @endif</td>   
                                  <td class="sticky-cell">{{$md->party_name}} <b>Demo</b><br>{{$md->party_hname}} </td>   
			 
                 @for($k=1; $k<=$rounds->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>
                @endfor 
                <td>{{$md->postalballot_vote}}</td> 
                <td class="sticky-cell-opposite">{{$md->total_vote}}</td></tr>

            @endforeach 
            
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
  </section>
 


@endsection
@section('script')
<script type="text/javascript">
           jQuery(document).ready(function(){
          //By Dropdown 
          jQuery("select[name='dis_ac']").change(function(){
            var dis_ac = jQuery(this).val();
             
            jQuery.ajax({
                    url: "{{url('/ac-wise-counting')}}",
                    type: 'POST',
                    data: {dis_ac:dis_ac},
                    success: function(result){
              }
            });
          });
          
           
          
        });
  
  
    
</script>


<script type="text/javascript">

function filter(){
    var url = "<?php echo url('ropc/counting-details') ?>";
    var query = '';

    if(jQuery("#dis_ac").val() != ''){
      query += '&dis_ac='+jQuery("#dis_ac").val();
    }

    window.location.href = url+'?'+query.substring(1);
}
</script>

@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif


 
@endsection