
@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'List of All Applications')
@section('content') 
<?php  
 
    $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->get()->count();
     
     ?>
<main>
<style type="text/css">
th, td {white-space: normal!important;}
.text-warning{color: #4CAF50 !important;}

</style>
<section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
        <div class="container-fluid">
          <div class="row d-flex">
            <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                <div class="icon"><img src="{{ asset('theme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number yellow">{{$total}}</div><p>Applications<strong class="text-primary">Applied</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                  <div class="icon"><img src="{{ asset('theme/img/icon/verified.png') }}" alt="" /></div>
                <div class="number green">{{$totaccepted}}</div><p>Applications<strong class="text-primary">Accepted </strong></p>
               
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('theme/img/icon/generate.png') }}" alt="" /></div>
                <div class="number orange">{{$totrej}}</div><p>Total Receipt<strong class="text-primary">Rejected</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('theme/img/icon/notverified.png') }}" alt="" /></div>
                <div class="number red">{{$totalwith}}</div><p>Applications<strong class="text-primary">Withdrawn</strong></p>
              </div>
            </div>
          </div>
		
		
		
      
			
			
			
        
		
		
		</div>
</section>
<section class="statistics color-grey">
<div class="container-fluid p-0"><div class="row"><div class="col"> 
     
			@if($cand_finalize_ro==0)
			<div class="alert alert-danger text-center" role="alert">Candidate Nominations details has not been finalized</div>		
			@elseif($checkval==1)
			<div class="alert alert-success text-center" role="alert">Candidate Nominations details has been finalized</div>
			@endif		
			
</div></div></div>
</section>

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
		<h5>List of All Applications</h5>
	</div>
		<div class="col-md-8">
		<form class="form-inline pull-right">
         
          
			<div class="form-group float-right"> 
				<label for="noofcards" class="mr-3">Select Status</label> 
				<form name="frmstatus" id="frmstatus" method="POST"  action="" >
				<select name="cand_status" id="cand_status" onchange="this.form.submit();">
              <option value="" @if($status=='') selected="selected" @endif>All</option>
              @if(isset($status_list))
              @foreach($status_list as $s) 
              @if($s->id==1 || $s->id==4|| $s->id==5|| $s->id==6)   
              <option value="{{$s->id}}" @if($status==$s->id) selected="selected" @endif >@if(isset($s)){{ucwords($s->status)}}  @endif</option>
              @endif
              @endforeach @endif
        </select>
		    </div>				
		    <div class="form-group float-right ml-4">
                <div class="input-group ">
                    <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name"  />
					&nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </form>
		</div>
		</div>
	 
	<div class="row" id="myTable">

       <?php
  $symbList= getsymbollist();
  $symbListArr = [];
  foreach($symbList as $symbL){
    $symbListArr[$symbL->SYMBOL_NO] = $symbL->SYMBOL_DES;
  }

  
  ?>


	<?php $url = URL::to("/");    ?>
	@if(!$lists->isEmpty())
		<?php $k=1; ?>
	@foreach ($lists as $key=>$list)
	<?php  $getid = Crypt::encrypt($list->nom_id);
		 $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);// \app(App\adminmodel\Candidateaffidavit::class)->where(['nom_id' =>$list->nom_id])->first(); 
		  if(!empty($affidavit))
		   {
		  $cnt = countrecordsaffidavit('candidate_affidavit_detail', 'nom_id', $list->nom_id);
		   }
		 $party= getpartybyid($list->party_id);
		// $symb= getsymbolbyid($list->symbol_id);
		 $s= getnameBystatusid($list->application_status);
	?>   
	
		<div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex" data-id="key{{$s}}">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<h6 class="mr-auto">
					@if(!empty($party))
						{{$party->PARTYNAME}}/{{ !empty($party->PARTYHNAME) ? trim($party->PARTYHNAME) : ''}} 
					@endif
				</h6>
				</div>
			 
			<div class="table-responsive card-body">
		
			<table class="table " border="0">                    
			  <tbody>
				<tr class="space">
				<td rowspan="4" class="profileimg td-01" style="width: 30%">
				<span class="btn-sno">{{$k}}<!--{{$list->cand_sl_no }}--></span>	@if($list->cand_image!='')
                      <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt="no images" width="50" height="60">
                    @else 
                      <img src="{{ asset('theme/images/User-Icon.png') }}" class="prfl-pic img-thumbnail" alt="" width="50" height="60">
                    @endif
				</td>
				<td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi: <br>  Name in Vernacular : </label></td>
				<td class="td-03" style="width: 40%"><p>{{$list->cand_name}}  <br> @if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif <br>  @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif</p></td></tr>
				<tr class="space">
				<td><label for="FName">Candidate ID:</label></td>
				<td><p>{{$list->candidate_id}} </p></td>
				
				</tr>  
				<tr class="space">
				<td><label for="FName">Father's / Mother's Name / Husband's Name:</label></td>
				<td><p>{{$list->candidate_father_name}}</p></td>
				
				</tr> 
				<tr class="space">
				<td><label for="DateOfsubmission">Date of Submission:</label></td>
				<td><p>{{date("d M Y",strtotime($list->date_of_submit))}}</p></td>
				
				</tr> 
				<tr class="space">
					<td rowspan="2">
						<!-- @if(isset($symb->Symbol_Img))
          					<img src="data:{{$symb->CONTENT_TYPE}};base64, {{$symb->Symbol_Img}}" alt="Red dot" class="size-50"  />
      					@endif -->
				    </td>
                <td>
				<label for="Symbol">Symbol</label></td>
				<!-- <td><p>@if(!empty($symb)) {{$symb->SYMBOL_DES}} @endif</p> </td> -->
				 <td><p>@if(isset($symbListArr[$list->symbol_id])) {{$symbListArr[$list->symbol_id]}}@endif</p></td>
				</tr>
				<tr class="space">
                <td> <label for="Ptype">Party Type</label></td><td><p>
							<!-- @if($party->PARTYTYPE=="N") 
								National  
							@endif 
							@if($party->PARTYTYPE=="S") 
								State  
							@endif 
							@if($party->PARTYTYPE=="U") 
								Unrecognized  
							@endif 
							@if($party->PARTYTYPE=="Z") 
								Independent  
							@endif -->
							  <?php 	if($list->cand_party_type=="N") $p="National";
         if($list->cand_party_type=="S") $p="State";
         if($list->cand_party_type=="U" || $list->cand_party_type=="0") $p="Unrecognized";
         if($list->cand_party_type=="Z") $p="Independent"; ?>
							{{ $p }}
						</p> </td>
				</tr>
				<tr>
                <td rowspan="2">
           <!--  @if(isset($symb->Symbol_Img))
                    <img src="data:{{$symb->CONTENT_TYPE}};base64, {{$symb->Symbol_Img}}" alt="Red dot" class="size-50"  />
                @endif -->
            </td>
          <td ><label for="category">Age / Category : </label></td>
          <td >{{$list->cand_age}} / {{strtoupper($list->cand_category)}}</td>
        </tr>  

				</tbody>
			</table>
			</div>
				<div class="card-footer">
      <div class="row d-flex align-items-center">
	  <div class="col md-3">
	  @if($s == "accepted")
						<small class="text-data text-success"><i class="fa fa-check"></i> Accepted </small>
					@elseif($s == "rejected")
						<small class="text-data text-primary"><i class="fa fa-check"></i> Rejected </small>
					@elseif($s == "withdrawn")
						<small class="text-data text-secondary"><i class="fa fa-check"></i> Withdrwan </small>
					@else
						<small class="text-data text-warning"><i class="fa fa-check"></i>{{$s}} </small>
					@endif
					</div>
      <div class="col"> 
     <?PHP 
		?>
      <div class="btn-group float-right" role="group" aria-label="Basic example">
     		@if(!empty($affidavit->affidavit_name) && $cnt!=0)
					<!-- <a href="{{asset($affidavit->affidavit_path)}}" class="btn btn-light" download>Download Affidavit</a> -->
					<button id='ajaxlink' class="btn btn-primary btn-sm" onClick='openModal("{{$affidavit->nom_id}}")'>Download Affidavit</button>&nbsp;&nbsp;
			@endif
			@if($cand_finalize_ro==0 || $indexcard_finalize==0)
           <a href="{{'updatenomination/'.$getid}}" class="btn btn-primary">Update Profile</a>&nbsp;&nbsp;
           @endif
           @if($list->cand_name!="NOTA")
			<a href="{{'viewnomination/'.$getid}}" class="btn btn-primary">View Profile</a>
			@endif
	    &nbsp;&nbsp;
	    @if(!isset($affidavit->affidavit_name) || empty($affidavit->affidavit_name)  || ($cnt==0))
	    @if($cand_finalize_ro==0)
	    
	    @if($poll_val==0)
		<button type="button" id="{{$list->nom_id}}" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}"> Drop</button>
		@endif
	  @endif
	  @endif
      </div>
      </div>
      </div>
      </div>
			
			</div>
			
		</div>
		 <?php $k++; ?>
	@endforeach
	@else
	  <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
	@endif
	</div>
</div>
</section>
 <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
	<form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/duplicate-drop') }}" >
                {{ csrf_field() }}   
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Remove Duplicate Candidate Entry.</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">    
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
   
    	
      <div class="custom-control custom-radio mb-3"> <!-- custom-control-inline -->
        <input type="radio" id="customRadioInline1" name="marks" value="11" class="custom-control-input" required="required">
        <label class="custom-control-label" for="customRadioInline1" >Duplicate Drop</label>
      </div>
	<small style="font-size: 12px;">(Incase if the entry has been made wrongly, can be removed by this option)</small>
  
	<!-- <hr /> <small >Are you sure You want to drop this duplicate record?</small>-->
  
    </div>
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Remove</button>
      </div>
    </form>
     
      
    </div>
  </div>
</div>



<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
 
     <!-- Modal content-->
     <div class="modal-content">
      <div class="modal-header" style="background-color:blue">
        
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-bodys">
  <p>{{ session('error_mescheck') }} 
  	<br><span style="color:Blue"; >{{ session('consumer1')}} </br><br>{{ session('consumer2')}}</span></br>
  	 {{ session('error_mescheck1') }}</p>

  
    </div>
      <div class="modal-footer">
       <button type="button" class="btn btn-danger" data-dismiss="modal" >Cancel</button>
      </div>
      
     </div>
    </div>
   </div>
<!-- Modal Content Ends Here -->
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
<script type="text/javascript">
	
 
function openModal(nomid){


  $.ajax({
        
              url: "{{url('roac/getaffidavit')}}",
            type: 'GET',
            data: {nom_id:nomid},

               success: function(response){ 
               	//alert(response);
                    // Add response in Modal body
                    $('.modal-bodys').html(response);

                    // Display Modal
                    $('#myModal').modal('show'); 
               }
         });

}
</script>
