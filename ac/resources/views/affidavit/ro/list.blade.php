
@extends('admin.layouts.ac.theme')
@section('title', 'Candidate e-Affidavit Details')
@section('bradcome', 'List of All e-Affidavit')
@section('content') 

<main>
<style type="text/css">
th, td {white-space: normal!important;}
.text-warning{color: #4CAF50 !important;}

</style>


<section class="data_table mt-5 form">
  <div class="container-fluid">

	<div class="row">
	    @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
           <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         @if (session('finalize_mes'))
          <div class="alert alert-success"> {{session('finalize_mes') }}</div>
        @endif
	</div>
	<div class="row d-flex align-items-center mb-3">
	<div class="col">
		<h5>List of All e-Affidavit</h5>
	</div>
	</div>
		
	 
	<div class="row" id="myTable" width="100%">
	
	
	
	<?php $url = URL::to("/");    ?>
	@if(!$lists->isEmpty())
	@foreach ($lists as $key=>$list)
	<?php  $getid = Crypt::encrypt($list->nom_id);
		 $party= getpartybyid($list->partyabbre);
	?>   
	
		<div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex" width="50%">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<h6 class="mr-auto">
					@if(!empty($party))
						{{$party->PARTYNAME}} 
					@else
						Independent
					@endif</h6>
					
				 
				</div>
			 
			<div class="table-responsive card-body">
		
			<table class="table " border="0">                    
			  <tbody>
				<tr class="space">
				<td rowspan="4" class="profileimg td-01" style="width: 30%">
				<span class="btn-sno">{{$key+1}}</span>	@if($list->cimage!='')
                      <img src="{{$url.'/'.$list->cimage}}" class="prfl-pic img-thumbnail" alt="no images" width="50" height="60">
                    @else 
                      <img src="{{ asset('theme/images/User-Icon.png') }}" class="prfl-pic img-thumbnail" alt="" width="50" height="60">
                    @endif
				</td>
				<td class="td-02" style="width: 30%"><label for="name">Candidate Name: </label></td>
				<td class="td-03" style="width: 40%"><p>{{$list->cand_name}}</p></td>
				</tr>
				<tr class="space">
				<td><label for="FName">Affidavit ID:</label></td>
				<td><p>{{$list->affidavit_id}} </p></td>
				
				</tr>  
				<tr class="space">
				<td><label for="FName"> @if($list->relation_name == '3') Husband's Name @else Father's Name @endif :</label></td>
				<td><p>{{$list->son_daughter_wife_of}}</p></td>
				
				</tr> 
				<tr class="space">
				<td><label for="DateOfsubmission">Date of Submission:</label></td>
				<td><p>{{date("d M Y",strtotime($list->created_at))}}</p></td>
				
				</tr> 
				
				<tr class="space">
                <td></td>
                <td> <label for="Ptype">Party Type</label></td>
				<td><p>
							@if($list->partytype=="N") 
								National Party 
							@elseif($list->partytype=="S") 
								State Party 
							@elseif($list->partytype=="U") 
								Unrecognized Party
							@else
								Independent
							@endif
						</p> </td>
				</tr>

				</tbody>
			</table>
			</div>
				<div class="card-footer">
      <div class="row d-flex align-items-center">
		@if($list->finalized == 1)
					<a href="../part-a-detailed-report?pdf=yes&affidavit_id={{@$list->affidavit_id}}" class="btn btn-light" download>Download Affidavit</a>
			@else 
				Not Finalize Yet
			@endif
      <div class="col">
     
      <div class="btn-group float-right" role="group" aria-label="Basic example">
     		
			@if($cand_finalize_ro==0)
           <a href="{{url('roac/affidavitdashboard/edit/'.@$list->id)}}" class="btn btn-primary">Update e-Affidavit</a>&nbsp;&nbsp;
           @endif
          
	    &nbsp;&nbsp;
	    
      </div>
      </div>
      </div>
      </div>
			
			</div>
			
		</div>
		 
	@endforeach
	@else
	  <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
	@endif
	</div>
</div>
</section>

</main>  
@endsection
@section('script')
<script type = "text/javascript">  
window.onload = function () {  
	document.onkeydown = function (e) {  
		return (e.which || e.keyCode) != 116;  
	};  
}  
jQuery(document).ready(function(){
	//By Dropdown 
	jQuery("select[name='cand_status']").change(function(){
		var cand_status = jQuery(this).val();
		//alert(candStatus);
		jQuery.ajax({
            url: "{{url('/listnomination')}}",
            type: 'POST',
            data: {cand_status:cand_status},
            success: function(result){
			}
		});
	});
	
	//By Searh Text
	jQuery("#myInput").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		jQuery("#myTable div").filter(function() {
			jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
});
$(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
        
   });
</script>  
@endsection
