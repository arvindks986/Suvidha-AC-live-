@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'Symbol Assign to Candidate')
@section('content')
 
<section class="statistics color-grey pt-3 pb-2 border-bottom">
	<div class="container-fluid">
			<div class="row">
			<div class="col">
			 <h5> Symbol Assign to Candidate</h5>
			</div>
       @if (\Session::has('success_mes'))
          <div class="alert alert-success"> {!! \Session::get('success_mes') !!} </div>
      @endif
      @if (\Session::has('error_mes'))
         <div class="alert alert-danger"> {!! \Session::get('error_mes') !!} </div>
      @endif
			</div>
	</div>
</section>
 
<section class="data_table mt-5 form">
  <div class="container-fluid">
  <p>Disclaimer: Symbols should first be allocated as per the extant provisions and then to be entered matching in Encore.</p><br>
 <div class="row" id="myTable">
  <?php $url = URL::to("/");   $j=0; ?>
  @if(!$lists->isEmpty())
  
      @foreach ($lists as $key=>$list)  
          <?php $j++;
              $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id); 
              $party= getpartybyid($list->party_id);
              $symb= getsymbolbyid($list->symbol_id);
              $s= getnameBystatusid($list->application_status);
             
           ?> 
   
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4  mb-3">
    <div class="card">
      <div class="card-header d-flex align-items-center">
      
        <h6 class="mr-auto">@if(isset($party)){{ucwords($party->PARTYNAME)}}@endif</h6>     
        <!-- <small class="text-data text-success">Status:-<i class="fa fa-check"></i>@if(isset($s)) {{ucwords($s)}} @endif</small>    -->  
        
      </div>
     
      <div class="table-responsive card-body">
      <table class="table">                    
              <tbody>
              <tr class="space">
                <td rowspan="3" class="profileimg td-01">
				<span class="btn-sno">{{$j}}</span>
				@if($list->cand_image!='')
                       <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt="">
                    @else 
                      <img src="{{ asset('theme/img/male_avatar.png') }}" class="prfl-pic img-thumbnail" alt="">
                    @endif </td>
                  <td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi: <br>  Name in Vernacular:</label></td>
        <td class="td-03" style="width: 40%"><p>{{$list->cand_name}}  <br> @if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif <br>  @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif</p></td>         
                
               </tr> 
              <tr class="space">
			  <td><label for="FName">Father's / Mother's Name / Husband's Name:</label></td>
			  <td><p>{{$list->candidate_father_name}}</p></td>
			  </tr> 
              <tr class="space">
			  <td><label for="DateOfsubmission">Date of Submission:</label></td>
			  <td><p>{{date("d-F-Y",strtotime($list->date_of_submit))}}</p></td>
               </tr> 
			   <tr class="space">
          <td rowspan="2">
           <!--  @if(isset($symb->Symbol_Img))
                    <img src="data:{{$symb->CONTENT_TYPE}};base64, {{$symb->Symbol_Img}}" alt="Red dot" class="size-50"  />
                @endif -->
            </td>
				<td><label for="Symbol">Symbol</label></td>
				<td><p>@if(isset($symb)) {{$symb->SYMBOL_DES}}@endif</p></td>
			   </tr>
			   <tr>
					<td><label for="Ptype">Party Type</label></td>
					<td><p>@if(!empty($party))  @if($party->PARTYTYPE=="N") National   @endif @if($party->PARTYTYPE=="S") State  @endif @if($party->PARTYTYPE=="U") Unrecognized  @endif @if($party->PARTYTYPE=="Z") Independent  @endif @endif</p></td>
			   </tr>
			   
          
          
              </tbody>
      </table>
      </div>
     
     <div class="card-footer">
      <div class="row ">
      <div class="col d-flex align-items-center">
      @if($list->symbol_id==0 || $list->symbol_id=='200') 
      <small class="text-muted mr-auto"><i>Symbol is not assign</i></small>
      <div class="btn-group float-right" role="group" aria-label="Basic example">       
        <!--<small class="text-success btn"><i class="fa fa-check"></i> Already Assigned</small>-->      
        <button type="button" id="{{$list->nom_id}}" class="btn btn-primary getdata" data-toggle="modal" data-target="#assignsymbol" data-nomid="{{$list->nom_id}}" 
            data-candname="{{$list->cand_name}}"> Assign Symbol</button>  
          
      </div>
      @else 
        <small class="text-success mr-auto"><i>Symbol is already assigned</i></small>
      
      <div class="btn-group float-right" role="group" aria-label="Basic example">   
        <!--<small class="text-success btn"><i class="fa fa-check"></i> Already Assigned</small> -->     
        
      </div>
      @endif
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
<!-- ==========================-->
    
    
  
</div>
</section>
  <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="assignsymbol" tabindex="-1" role="dialog" aria-labelledby="assignsymbol" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Assign Symbol</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST" action="{{url('roac/updatesymbol')}}" >
                {{ csrf_field() }}   
         
      <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
      <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">Candidate Name:- <input type="text" name="candidate_name" size="15" id="candidate_name" value="" readonly="readonly"></div>
    <div class="mb-3">

      Select Symbol : - <span class="pagespanred">*</span></td> <td> 
            <select name="symbol" id="symbol" style="width:200px;">
             <option value="" selected="selected">Selected</option>
                         @foreach($sym as $s)
                           <option value="{{$s->SYMBOL_NO}}">{{$s->SYMBOL_DES}}-{{$s->SYMBOL_HDES}}</option>
                             @endforeach 
            </select> <span id="err" class="text-danger"></span>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         <button type="submit" class="btn btn-primary">Save changes</button>
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
           $("#election_form").submit(function(){
             if($("#symbol").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Please select symbol");
                    $("#symbol").focus();
                    return false;
                    }
               });
        });
  $(document).on("click", ".getdata", function () { 
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       candname = $(this).attr('data-candname'); 
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
       $("#candidate_name").val(candname); 
   });

</script>
 
@endsection