@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Finalize EVM Rounds')
@section('content')
 
<style>
.modal-big .modal-dialog{max-width: 900px;}
.modal-big .modal-header{background-color: #f0587e; color: #fff; text-shadow: 1px 1px 1px #666; text-align: center;}
.mcenter{font-size:18px; line-height: 30px;}
</style>
<main role="main" class="inner cover mb-3">

    <section class="mt-2">
  <div class="container-fluid">
  <div class="row">
   @if(Session::has('success_admin'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
       @endif   
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
      @endif
      @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
      @endif
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
               
          <div class="col form-inline"><h6 class="mr-auto">Finalize EVM Rounds</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st_name}}</span>  &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac_name}}</span></p></div>
                </div>
                </div>
                <div class="card-body">  
 
  <div class="table-responsive">
  <table   class="table table-striped table-bordered table-hover" style="width:100%">
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
               
              ?>
             
              <tr><td>{{$j}}</td> <td>{{$md->candidate_name}} <br>{{$md->candidate_hname}}   @if($md->nom_id==$winn_data->nomination_id) <b>(Winning) </b>@endif @if($md->nom_id==$winn_data->trail_nomination_id) <b>(Trailing)</b>  @endif</td>   
        <td>{{$md->party_name}} <br>{{$md->party_hname}}  </td>
                 @for($k=1; $k<=$round_details->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>
                @endfor 
                
                <td>{{$md->total_vote-$md->postalballot_vote}} </td> </tr>

            @endforeach 
            @endif 
             </tbody>
     
    </table>
    </div> <!-- end reponcive-->
    @if($evm_finalized==0)
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/finalize-evm') }}" >
            {{ csrf_field() }} 
       <input type="hidden" name="new_table" value="{{$new_table}}">
        
                 <?php  $url = URL::to("/");  ?>
              <div class="form-group float-right">  
                  <input type="button" value="Cancel" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/polling-station-wisevote-entry';">
          <input type="button" value="Verify & Finalize" class="btn btn-success" id="preview_submit" data-toggle="modal" data-target="#changestatus">
              </div>
             
      </form>
      @endif
                </div>
              </div>
  
  
  </div>
  </div>
  </section>
  </main>
 <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal modal-big fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Declaration Alert</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form1" method="get">
                 {{csrf_field()}} 
				 <input type="hidden" id="roname" name="roname">
   <div class="mb-3">
     <ol class="mcenter">
      <li> &nbsp; I, <strong>{{Auth::user()->name}}</strong> certify that EVM votes has been compared and matches with the Form-20 compiled manually.</li>

     <li> &nbsp;  I, understand that upon pressing the 'Finalize' button below,editing can't be done.</li>
    </ol>
		<ol class="mcenter">
      <p align="left"><input type="checkbox" name="verify">&nbsp;&nbsp;I agree to finalize the evm votes </p>
      <p align="left"><span id="errorMsg" class="red"></span></p>
	  </ol>
      </div>
    
    
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="submit_final_form" class="btn btn-success submit-button">Verify & Finalize</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function(e){
    $('#submit_final_form').click(function(e){
	var atLeastOneIsChecked = $('input[name="verify"]:checked').length > 0;
	if(atLeastOneIsChecked === false){
		$("#errorMsg").text("Please agree to finalize the evm votes.");
		return false;
	}else{
		$(this).prop('disabled',true);
		$("#election_form").submit();
	}
	
  });

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
