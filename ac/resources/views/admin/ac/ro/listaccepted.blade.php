@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'List All Accepted Candidates')
@section('content') 
 
<style type="text/css">
th, td {white-space: normal!important;}
.connectedSortable td{padding:4px;}
img.prfl-pic {max-width: 225px;}
.border_top{border-top: 2px solid #dedede;}
.form-shadow {box-shadow: none!important;   border: 1px solid red; height:auto;}
.b-padding {padding: 13px;}
</style>
  <section class="statistics color-grey pt-2 pb-5" style="border-bottom:1px solid #eee;">
        <div class="container-fluid">
          <div class="text-right m-3">
             <!--<a href="{{url('/roac/update-form7A-details')}}" class="btn btn-primary">Update Form 7A Details</a>-->
          </div>
          <div class="d-flex">
            <div class="item">
              <!-- Income-->
            <a href="{{url('roac/download-form7a-english')}}?st_code={{$ele_details->ST_CODE}}&ac_no={{$ele_details->CONST_NO}} ">
            <div class="card income text-center">
                <div class="img-icon"><img src="{{ asset('theme/img/icon/from-dowload-icon.png') }}" alt="" /></div>
                <div class="number yellow"> </div><small>Download</small><p>Form7A in English </p>
              </div>
            </a>
            </div>
       
             
    <?php //$disable_stcode_arr = array('S14','S29','S10','S17');@if(!in_array($user_data->st_code, $disable_stcode_arr)) 
      $disable_form=1;
      ?>
      @if($disable_form=='0')
      <div class="item">
              <!-- Income-->
        <a href="{{url('roac/download-form7a-vernacular')}}?st_code={{$ele_details->ST_CODE}}&ac_no={{$ele_details->CONST_NO}} ">
            <div class="card income text-center">
                <div class="img-icon"><img src="{{ asset('theme/img/icon/from-dowload-icon.png') }}" alt="" /></div>
                <div class="number yellow"> </div><small>Download</small><p>Form7A in Vernacular </p>
                
            </div>
        </a>
            </div> 
      <div class="item">
              <!-- Income-->
        <a href="{{url('roac/download-form7a-bilingual')}}?st_code={{$ele_details->ST_CODE}}&ac_no={{$ele_details->CONST_NO}} ">
            <div class="card income text-center">
                <div class="img-icon"><img src="{{ asset('theme/img/icon/from-dowload-icon.png') }}" alt="" /></div>
                <div class="number yellow"> </div><small>Download</small><p>Form7A in Bilingual </p>
                
            </div>
        </a>
            </div> 
      @endif
   
          </div>
          
        </div>
</section>
<section>
<div class="container-fluid p-0">
  <div class="row">
    <div class="col">
       @if($checkval==0)
          <div class="alert alert-danger text-center p-3">Contesting Candidates has not been finalised.
           <span class="ml-3"> <a href="{{url('roac/finalize-ac') }}" >
            <button type="button" class="btn btn-danger b-padding" data-toggle="modal" data-target="#finalise"> Finalise Contesting Candidates</button></a></span>
          </div>

          @elseif($checkval==1)
                    <div class="alert alert-success text-center  custom-alert ">
                   <div class="customAlert"> Contesting Candidates has been finalised.</div>
                  </div> 
            @endif
    </div>
  </div>
</div>
</section>
<section class="data_table mt-3 form">
  <div class="container-fluid">
  
  @if(!$lists->isEmpty())
     <?php  $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'ac_no'=>$ele_details->CONST_NO])->where(['application_status' =>'6'])->get()->count();
     
     ?>
   
    <div class="row mb-3">
      <div class="col-md-10">
         <h5 class="text-capitalize">Please update the serial number of Contesting candidate as per rule</h5>
          
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
          <div class="col-md-2 p-0">
            <div class="form-group">
              <div class="input-group ">
                      <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name" id="myInput"/>
                  &nbsp;
                  <span class="input-group-btn">
                    <button class="btn btn-primary b-padding" type="submit"><i class="fa fa-search"></i></button>
                  </span>
                </div>
                </div>
          </div>
     </div>
  
    </div>
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
    // $symb= getsymbolbyid($list->symbol_id);
     $s= getnameBystatusid($list->application_status);
     
    if($list->cand_party_type=="N") $p="National";
         if($list->cand_party_type=="S") $p="State";
         if($list->cand_party_type=="U" || $list->cand_party_type=="0") $p="Unrecognized";
         if($list->cand_party_type=="Z") $p="Independent";
   ?> 
      <li class="ui-state-default ">
      <div class="card">
  <div class="">
  <table class="table" cellspacing="0" class="table datalist-move">
  <tr class="border-bottom">
    <td rowspan="4" class="profileimg" style="width: 16%;" >@if($list->cand_image!='')
                       <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt="">
                    @else 
                      <img src="{{ asset('theme/img/male_avatar.png') }}" class="prfl-pic img-thumbnail" alt="">
                    @endif 
      <span class="btn btn-danger btn-number">{{$list->new_srno}}</span></td>
    <td colspan="3"><h5 class="m-0 p-0">@if(isset($party)){{ucwords($party->PARTYNAME)}}/{{ !empty($party->PARTYHNAME) ? trim($party->PARTYHNAME) : ''}}@endif</h5></td>
    <td class="text-right m-0 p-0">Current Status <b class="text-success">@if(isset($s)){{ucwords($s)}} @endif  </b>&nbsp; </td>
    
  </tr>
  <tr>
  <td colspan="4">
  <table class="table">     
  
  <tr>
        <td>Name in English <b> {{$list->cand_name}} </b></td>
        <td>Name in Hindi <b>@if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif </b></td>
        <td>Name in Vernacular <b> @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif </b></td>
  </tr>
  <tr>
    <td>Party Type <b>@if(isset($p)){{ucwords($p)}} @endif</b></td>
    <td>Gender <b>{{ucwords($list->cand_gender)}}</b></td>
    <!-- <td>Symbol <b>@if(isset($symb)) {{$symb->SYMBOL_DES}}@endif</b></td> -->
    <td><p>@if(isset($symbListArr[$list->symbol_id])) {{$symbListArr[$list->symbol_id]}}@endif</p></td>
    
  </tr>
    </table>
  </td>
  </tr>
  
  
  
  <tr class="border_top">
    <td><!-- @if(isset($symb->Symbol_Img))
      <img src="data:{{$symb->CONTENT_TYPE}};base64, {{$symb->Symbol_Img}}" alt="Red dot" class="size-50"  />
                
    @endif -->
  </td>
  <td colspan="3" > 
    

    <div class="form-inline float-right"><label for=""> @if($checkval==0)Enter New Sr.No 
       <input style="max-height:36px;" type="text" placeholder="Enter Sr No." class="form-control form-shadow ml-2" min="0" max="30" value="{{old('newsrno'.$i)}}" name="newsrno{{$i}}" id="newsrno{{$i}}"/> 
            <input type="hidden" name="nom_id{{$i}}" value="{{$list->nom_id}}" /> @endif</label>  </div><span id="errmsg{{$i}}" class="text-danger"></span>
  </td>
    
    
    
  </tr>
  </table>
 
  </div>
      </div>
    </li>
    <?php $i++; ?>
    @endforeach
  </ul>
      @if($checkval==0)
          <div class="form-group row float-right">       
            <div class="col">
            <button type="submit" id="candnomination" class="btn btn-primary">Update</button>
            </div>
         </div>
          @endif
         <input type="hidden" name="totalvalue" value="{{$total}}" />
          <input type="hidden" value="{{$i}}" name="noval" id="noval"/>
  </div>
</div>
    
</form>
</div>
@else
     <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
  @endif
</div>
</section>
<!-- Modal Content Starts here -->
<!-- The Modal -->
  <div class="modal" id="finalise">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header modal-custom-header">
          <h4 class="modal-title">Finalise Contesting Candidate</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          Are you sure you have verified complete details  Once Finalised, All details will become non editable. Are you sure you want to finalise.
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-primary">Ok</button>
          <button type="button" class="btn btn-secodary" data-dismiss="cancel">Close</button>
        </div>
        
      </div>
    </div>
  </div>
  

   


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
  
   /* $( function() {
          $( "#sortable1, #sortable2" ).sortable({
            connectWith: ".connectedSortable"
          }).disableSelection();
      }); */
    
</script>
  @endsection
