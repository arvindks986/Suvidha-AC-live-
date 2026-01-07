@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Pre-Finalization Of EVM & Postal Ballot Votes')
@section('content') 
  <style type="text/css">
    .text-danger{
      width: 100%;
      float: left;
      font-size: 10px;
    }
    .input-error{
      border-color: red;
    }
    .evm_input{
      width: 150px;
    }
    .table td:last-child {
      width: 150px;
    }
    #preview_evem_votes input{
      border:0px;
      background: transparent;
    }
    #preview_evem_votes select {
        border: 0px solid #fff;
        background-color: transparent;
        }
     #preview_evem_votes select option [disabled]{
        border:0px solid #fff;
         
      }
	  
	 .table-wrap {height: 325px; margin-bottom: 1.85rem; overflow: hidden;}
	 .table-scroll{height: 100%; overflow-y: auto;}

  </style>
 
 <main role="main" class="inner cover mb-3">
		 <section class="statistics counting_dash">
		 <div class="container-fluid mt-4 mb-2">
          <div class="row">
		  <div class="col" style="max-width:300px; margin:0 auto;">
              <!-- Income-->
             
            </div>
		  </div>
		  </div>
		  </section>
    <section class="my-4">
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4 class="mr-auto">Pre-Finalization Of EVM & Postal Ballot Votes</h4>  </div>  
                 
                 <div class="col-md-7"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp;  <b class="bolt">AC Name:</b> 
                <span class="badge badge-info">{{$ac_name}}</span></p></div>
         
                </div>
                </div>
 
       
    <div class="card-body">
	<h4>PS With Null Count</h4>
	 <div class="table-wrap">
	 <div class="table-scroll">
       <table class="table table-bordered">  
        <thead>
		     <tr>
			  <th>PS No.</th>
			  <th>PS Name</th>
			  <th style="text-align:right;">Votes Polled</th>
			 </tr>		 
			</thead>	   
        <tbody>	
			@php $total_voters = 0;  @endphp
			@if(!empty($empty_ps_list))
			@foreach($empty_ps_list as $k=>$v)
           <tr>
		     <td>{{$v->PS_NO}}</td>
			 <td>{{$v->PS_NAME_EN}}</td>
			 <td style="text-align:right;">{{$v->electors_total}}</td>
		   </tr>
		   @php $total_voters = $total_voters + $v->electors_total;  @endphp
           @endforeach
		   <tr>
			<td colspan="2" align="right"><b>Total Votes Polled</b></td>
			<td colspan="2" align="right"><b>{{$total_voters}}</b></td>
		   </tr>
		   @else
		   <tr>
			<td colspan="3" align="center">No record found</td>
		   </tr>
			@endif
        </tbody>
       </table>
	   
	   </div><!-- End Of table-scroll Div -->
	   </div><!-- End Of table-wrap Div -->
	    <h4>Postal Rejected Votes Details</h4>
	   
	   <table class="table table-bordered">
		<tr>
			<td align="right"><b>Postal Rejected Votes</b></td>
			<td align="right"><b>{{$rejected_votes}}</b></td>
		</tr>
	   </table>
	   
	   <h4>Margin Of Votes Between Leading & Trailing</h4>
	   <table class="table table-bordered">  
        <thead>
		     <tr>
			  <th>Leading Candidate Name</th>
			  <th>Leading Candidate Party</th>
			  <th>Trailing Candidate Name</th>
			  <th>Trailing Candidate Party</th>
			  <th>Leading Candidate Votes</th>
			  <th>Trailing Candidate Votes</th>
			  <th style="text-align:right;">Margin Of Votes</th>
			 </tr>		 
			</thead>	   
        <tbody>	
			@php $vmargin = 0; @endphp
			@if(!empty($votes_margin_data))
			@php $vmargin = $votes_margin_data->margin; @endphp
           <tr>
			 <td>{{$votes_margin_data->lead_cand_name}}</td>
			 <td>{{$votes_margin_data->lead_cand_party}}</td>
			 <td>{{$votes_margin_data->trail_cand_name}}</td>
			 <td>{{$votes_margin_data->trail_cand_party}}</td>
			 <td>{{$votes_margin_data->lead_total_vote}}</td>
			 <td>{{$votes_margin_data->trail_total_vote}}</td>
			 <td align="right">{{$votes_margin_data->margin}}</td>
		   </tr>
		   <!--<tr>
			<td colspan="8" align="right"><b>Winning Margin</b></td>
			<td colspan="8" align="right"><b>{{$votes_margin_data->margin}}</b></td>
		   </tr>-->
		   
		   @else
		   <tr>
			<td colspan="9" align="center">No record found</td>
		   </tr>
			@endif
        </tbody>
       </table>
	   <?php 
	    $grid=6;
		if($vmargin < $total_voters && $vmargin < $rejected_votes){
			 $grid=6;
		}else{
			$grid=12;
		}
		?>
	   
	   <div class="row">
		<?php if($vmargin < $total_voters){?>
		  <div class="col-sm-<?php echo $grid;?>">
			<div class="card bg-primary text-white mb-3">
			  <div class="card-body">
				<h6 class="card-title"> Vote margin between leading candidate & nearest candidate is less than the polled votes of non counted polling stations.
</h6>
			  </div>
			</div>
		  </div>
		<?php }
		if($vmargin < $rejected_votes){
		?>
		  <div class="col-sm-<?php echo $grid;?>">
			<div class="card bg-primary text-white mb-3">
			  <div class="card-body">
				<h6 class="card-title">Vote margin between leading candidate & nearest candidate is less than the rejected postal ballots.</h6>
			  </div>
			</div>
		  </div>
		<?php }?>
		</div>
		<div class="card text-left">
		  <div class="card-body">
			<?php if($vmargin < $total_voters){?>
			@if($evm_finalized =='0')
			<div class="col-sm-12">
				<a class="btn btn-success" href="{{url('roac/counting/polling-station-wisevote-entry')}}">Editing Of PS with Null Count</a>
				<b>For editing of PS with null votes follow these steps - </b><br/>
				<a>Go to Main Menu --> Counting Preparation-->Round Schedule for AC --> Add additional number of rounds equal to the number of "PS With Null Count".</a>
			</div>
			@else
			<div class="col-sm-12">EVM Votes Finalized</div>
			@endif
			<?php }?>
			
			<?php
			if($vmargin < $rejected_votes){
			?>
			@if($postal_finalized =='0')
			<div class="col-sm-12 mt-2">
			<a class="btn btn-success" href="{{url('roac/counting/bpostal-data-entry')}}">Editing For Rejected Postal Ballots</a>
			</div>
			@else
			<div class="col-sm-12 mt-2">Postal Ballot Votes Finalized</div>
			
			@endif
			<?php }?>
		  </div>
		</div>
	   
    </div>
  <div class="card-footer">
  <div class="row">
  <div class="col">
                       
             <div class="form-group">
				@if($evm_finalized =='0' && $postal_finalized =='0')
				<div class="float-right">
					<button type="button"  class="btn btn-primary getUpdatePsData">Pre-Finalize</button>
                </div>    
                <form id="election_form" action="{{url('roac/counting/finalized-ps-verification')}}" method="POST">
					{{csrf_field()}} 
					<input type="hidden" name="vfps" value="1">
			    </form>
				@endif
             </div>
			               </div>
       </div>
  </div>
       
    
              
            
   
    </div>
    
    </section>  
    </main> 
<!-- Modal -->

@endsection
@section('script')
<script>
$(".getUpdatePsData").click(function(){
	if(confirm("Are you sure you want to pre-finalize.")){
            $(this).text('Processing...');
            $(this).prop('disabled',true);
            $("#election_form").submit();
    }else{
		
    }
});
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
