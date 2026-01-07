@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'List All Accepted Candidates')
@section('content') 
<?php $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();


$appliedtotal=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->whereNotIn('application_status', [4,5,6])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();
?>

<style type="text/css">
th, td {white-space: normal!important;}
.btn {font-size: 14px;}
img.prfl-pic {   width: 100%; max-width: 250px; }
.text-red{color:red;}
.text-green{color:green;}
.box-shadowb{border-bottom: 1px solid rgba(0, 0, 0, 0.125);border-right: 1px solid rgba(0, 0, 0, 0.125);    border-left: 1px solid rgba(0, 0, 0, 0.125); margin-top:5px;}

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
                  <div class="icon"><img src="{{ asset('theme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number green">{{$totaccepted}}</div><p>Applications<strong class="text-primary">Accepted</strong></p>
               
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('theme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number orange">{{$totrej}}</div><p>Total Receipt<strong class="text-primary">Rejected</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('theme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number red">{{$totalwith}}</div><p>Applications<strong class="text-primary">Withdrawn</strong></p>
               
              </div>
            </div>
         
          
          </div>
        
        </div>
</section>
<section class="statistics color-grey">
<div class="container-fluid p-0">
	 <div class="row">
 <div class="col">
      

           @if($appliedtotal > 0)
          <div class="alert alert-danger text-center" role="alert">Please Verify Candidates Status should be <b>Accepted or Rejected or Withdrawn</b></div>
  @else

		    @if($checkval==0)
			<div class="alert alert-danger text-center" role="alert">Candidate Nominations details has not been finalized</div>
		
			@elseif($checkval==1)
			<div class="alert alert-success text-center" role="alert">Candidate Nominations details has been finalized</div>
       
            @endif
 </div>
 </div>
</div>
</section>
<section class="data_table mt-3 form">

  <div class="container-fluid">
   
  @if(!$lists->isEmpty())
     <?php  $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no'=>$ele_details->CONST_NO])->where(['application_status' =>'6'])->get()->count();
     ?>

  <div class="row">
    <div class="col">
       <form class="form-inline d-flex align-items-center">
        <h4>Mark as Contesting Candidate</h4>
       <div class="form-group mr-8 ml-auto ">
        @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
        @if (session('error_mes1'))
          <div class="alert alert-danger"> {{session('error_mes1') }}</div>
        @endif
        @if(!empty($errors->first()))
          <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
        @endif  
        </div>
            
        <div class="form-group float-right ml-4">
          <div class="input-group ">
              <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name" id="myInput"/>
              &nbsp;
              <span class="input-group-btn">
                <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-search"></i></button>
              </span>
          </div>
        </div>
        </form>
    </div>
    </div>
	<div class="row mb-3">
		<div class="col">
			<small>List showing below is all accepted nominations. Please Mark as Contesting Candidate. In case of multiple accepted nominations of the same candidate mark only one as validly nominated.<br /><span class="alert alert-info">(Only the nominations marked as validly nominated will be available for final list of candidates)</span></small>
		</div>
	</div>
	<hr />
  <div class="container">
 
    <form class="form-horizontal" id="form7" method="POST"  action="{{url('roac/change-sequence') }}" >
                {{ csrf_field() }}  
  <div class="row" id="myTable">
    <div class="col">
    <ul id="sortable1" class="connectedSortable list-group">
    <?php $i=1; $url = URL::to("/");   $val=0; ?>

     <?php
  $symbList= getsymbollist();
  $symbListArr = [];
  foreach($symbList as $symbL){
    $symbListArr[$symbL->SYMBOL_NO] = $symbL->SYMBOL_DES;
  }

  
  ?>
   
      @foreach ($lists as $key=>$list)
  <?php 
     $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);
     $party= getpartybyid($list->party_id);
     //$symb= getsymbolbyid($list->symbol_id);
     $s= getnameBystatusid($list->application_status);
     
    if(!empty($party)){
          if($party->PARTYTYPE=="N") $p="National";  
          if($party->PARTYTYPE=="S") $p="State";    
          if($party->PARTYTYPE=="U") $p="Unrecognized";    
          if($party->PARTYTYPE=="Z") $p="Independent";    
    }
   ?> 
      <li class="ui-state-default ">
      <div class="card">
  <div class="card-body">
  <table class="table datalist-move">
  
	<tr><td rowspan="4" style="padding-right: 10px;">@if($list->cand_image!='')
                       <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt=""/>
                    @else 
                      <img src="{{ asset('theme/img/male_avatar.png') }}" class="prfl-pic img-thumbnail" alt=""/>
                    @endif
					 <span class="btn btn-danger btn-number">{{$i}}</span>
					</td> <td colspan="2" style="width: 64%;"><h5 class="m-0 pb-2">@if(isset($party)){{ucwords($party->PARTYNAME)}}@endif</h5></td><td align="right" class="m-0 pb-2">Current Status <b class="text-success">@if(isset($s)){{ucwords($s)}} @endif</b></td></tr>
<tr><td>Name in English<b style="display:block;"> {{$list->cand_name}} (Candidate ID :- {{ isset($list->candidate_id) ? $list->candidate_id : '' }}, Nomination ID :- {{ isset($list->nom_id) ? $list->nom_id : '' }}) </b></td><td>Name in Hindi<b style="display:block;">@if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif </td><td>Name in Vernacular<b style="display:block;"> @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif </td></tr>
					
	<tr><td>Party Type <b>@if(isset($p)){{ucwords($p)}} @endif</b></td>	<td>Gender <b>{{ucwords($list->cand_gender)}}</td>
  	<td>Symbol <b>@if(isset($symbListArr[$list->symbol_id])) {{$symbListArr[$list->symbol_id]}}@endif</b></td></tr>

    
	
	<tr>
	<td colspan="3">
	
	<label class="p-2 row align-items-center d-flex card-footer box-shadowb" for="">@if($checkval==0) Mark as Contesting Candidate:- @if($list->finalaccepted==0)<span class="text-red"> No</span> @else <span class="text-green">Yes</span> @endif 
    <!-- @if(isset($symb->Symbol_Img))
                    <img src="data:{{$symb->CONTENT_TYPE}};base64, {{$symb->Symbol_Img}}" alt="Red dot" class="size-50"  />
                @endif -->
       <button type="button" id="{{$list->nom_id}}" class="btn btn-primary getdata ml-auto" data-toggle="modal" data-target="#changestatus" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}">Mark as Contesting Candidate</button> 
         @endif</label>  </td>	</tr>
  </table>
  
 
 
  </div>						
      </div>
    </li>
    <?php $i++; ?>
    @endforeach
  </ul>
       
  </div>
</div>
    
</form>
</div>
@else
     <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
  @endif
  @endif
</div>
</section>
 <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Mark as Contesting Candidate</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/finalaccepted') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">
      
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="1" class="custom-control-input" checked>
        <label class="custom-control-label" for="customRadioInline1">yes</label>
      </div>
       <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline2" name="marks" value="0" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline2">No</label>
      </div> 
      
      </div>
    
       <div class="mb-3">
       <p></p>
    
  </div>
  
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
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
    jQuery(document).ready(function(){
        var v = $("#noval").val();
         //By Searh Text
          jQuery("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            jQuery("#myTable div").filter(function() {
              jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
            });
          });
        for (i = 1; i <=v; i++) { 
            jQuery("#newsrno"+i).keypress(function (e) {
               //if the letter is not digit then display error and don't type anything
               if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                  //display error message
                  jQuery("#errmsg"+i).html("Digits Only").show().fadeOut("slow");
                  return false;
              }
             });
            } // end for
        });
  
   $(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       var s = $(this).attr('data-status');
       var message = $(this).attr('data-message');
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
        
   });
    
    
</script>
  @endsection