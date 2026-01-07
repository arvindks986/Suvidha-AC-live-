@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Polling Station Wise Vote Entry Form')
@section('content') 
  <?php     $url = URL::to("/");   
          $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
          //if(isset($counting_pstabledeails))$psno=$counting_pstabledeails['ps_no']; else $psno=old('ps_no');
          !empty(old('ps_no'))?$psno=old('ps_no'):$psno=$counting_pstabledeails['ps_no'];
          !empty(old('rejected_vote'))?$rejected_vote=old('rejected_vote'):$rejected_vote=$counting_pstabledeails['rejected_vote'];
          !empty(old('tendered_vote'))?$tendered_vote=old('tendered_vote'):$tendered_vote=$counting_pstabledeails['tendered_vote'];
           if($rejected_vote==0) $rejected_vote=0;  
  ?>  
  

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

  </style>
 
 <main role="main" class="inner cover mb-3">
  <section class="statistics counting_dash color-grey">
		<div class="container-fluid pt-3 mb-2">
			<div class="row">
				<div class="col-lg-12">
				 <div class="d-flex justify-content-between">
					<div class="record-item">
              <!-- Income-->
              <div class="card income">
                <div><b class="text-success mr-auto">Total Rounds Scheduled</b>
                  <span class="badge badge-success float-right">{{$scheduled_round}} </span></div>
              </div>
            </div>
					<div class="record-item">
              <!-- Income-->
              <div class="card income">
               <div class="text-info"><b class="mr-auto">Total Tables </b> 
                  <span class="badge badge-info float-right">{{$total_no_tables}} </span></div>
              </div>
            </div>
					<div class="record-item">
              <!-- Income-->
              <div class="card income">
                <div><b class="text-success mr-auto">Assigned Tables  </b>
                  <span class="badge badge-success float-right">{{$totalassigntable}} </span></div>
              </div>
            </div>
					<div class="record-item">
              <!-- Income-->
              <div class="card income">
                <div class="text-warning"> <b class="mr-auto">Selected Round</b>@if($scheduled_round!=$complete_rounds || $ctype=='edit')<span class="badge badge-warning text-white float-right"> {{$current_rounds}}</span> @elseif($current_rounds!=0)
                  
                    <b>Rounds Completed</b> @endif</div>   
              </div>
            </div>
					<div class="record-item">
              <!-- Income-->
              <div class="card income">
               <div class="text-info"><b class="mr-auto">Total Polling Stations </b> 
                  <span class="badge badge-info float-right">{{count($ps_list)}} </span></div>
              </div>
            </div>
			</div>
		   <div class="d-flex justify-content-between">
            <div class="record-item">
              <!-- Income-->
              <div class="card income">
                <div><b class="text-success mr-auto">Completed Rounds  </b>
                  <span class="badge badge-success float-right">{{$complete_rounds}} </span></div>
              </div>
            </div>
             
          <div class="record-item">
               <div class="card income"> 
                <div class="text-info"><b class="mr-auto">Completed Tables</b> 
                  <span class="badge badge-info float-right">{{$completetable}} </span></div>
              </div>
            </div>
        
            <div class="record-item">
              <!-- Income-->
               <div class="card income">
               <div class="text-success"> <b class="mr-auto">Completed Assigned </b> 
                  <span class="badge badge-success float-right"> {{$selfassign_complate}}</span> </div>   
              </div>
            </div>
			  <div class="record-item">
              <!-- Income-->
              <div class="card income">
               <div class="text-warning"> <b class="mr-auto">Selected Table</b> 
                  <span class="badge badge-warning text-white float-right">@if($table_id){{$table_id}}@else &nbsp; @endif</span> </div>   
              </div>
            </div>
			<div class="record-item">
              <!-- Income-->
              <div class="card income">
               <div class="text-info"><b class="mr-auto">Total PS With Null Count </b> 
                  <span class="badge badge-info float-right tepsl"></span></div>
              </div>
            </div>
          
          </div>
				</div>
				<div class="col-lg-12"> 
				
               <button type="button" style="padding: 0px;  font-size: 15px;  text-transform: uppercase;  text-align: center;" class="btn btn-primary btn-lg pr-3 pl-3 pt-2 pb-2 pull-right" data-toggle="modal" data-target=".bd-example-modal-xl"> Quick Summary Of Table</button>
                
                 
             </div>
			</div></div>
		<hr />
		</section>
		 <section class="statistics counting_dash">
		 <div class="container-fluid mt-4 mb-2">
          <div class="row">
		  <div class="col" style="max-width:300px; margin:0 auto;">
              <!-- Income-->
             
            </div>
		  </div>
		  </div>
		  </section>
      
 <section>
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4 class="mr-auto">Polling Station Wise Vote Entry Form</h4>  </div>  
                 
                 <div class="col-md-7"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp;  <b class="bolt">AC Name:</b> 
                <span class="badge badge-info">{{$ac->AC_NAME}}</span></p></div>
         
                </div>
                </div>

   <div class="row">
    <div class="col">
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
          
         @if(Session::has('success_admin'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif

         
    </div>
    </div>
   
       
    <div class="card-body">
    <div class="row">
  <div class="round-check">
           <?php $net=0; $check=0; for($i=1; $i<=$total_no_tables; $i++) {   
            foreach ($complete_table as $k => $v) {
               if($i==$v->table_id) { $check=$i; break;}
            }
            if($i==$check) { ?>
                  <div class="check-success">
                  <?php } else  { ?>
                      <div>
                   <?php } ?>                  
                   <span><i class="fa fa-check"></i></span> 
           <br />
            <small style="font-size:50%;">Table-{{$i}}</small>
                  </div>
            <?php } ?>
                               
          </div>
        </div>  
  </div>  
    @if(!$master_data->isEmpty())    
         <form class="form-horizontal mb-0" id="election_form" method="POST"  action="{{url('roac/counting/verifypolling-station-wisevote-entry') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{csrf_field()}}  
                 <input type="hidden" name="new_table" value="{{$new_table}}">
                 <input type="hidden" name="CONST_TYPE" value="{{$ele_details->CONST_TYPE}}">
                 <input type="hidden" name="CONST_NO" value="{{$ele_details->CONST_NO}}">
                 <input type="hidden" name="ST_CODE" value="{{$ele_details->ST_CODE}}">
                 <input type="hidden" name="ELECTION_ID" value="{{$ele_details->ELECTION_ID}}">
                 <input type="hidden" name="round_id" id="round_id" value="{{$round_id}}"> 
                 <input type="hidden" name="scheduled_round" value="{{$scheduled_round}}">
                 <input type="hidden" name="complete_rounds" value="{{$complete_rounds}}">        
                 <input type="hidden" name="ctype" id="ctype" value="{{$ctype}}">

         <table class="table table-bordered preview_table" style="width:100%">
          <tr>  
		  <th width="20%"> <label>Table Number<sup>*</sup></label></th>
               <td width="30%"> 
                  @if($scheduled_round!=$complete_rounds || $ctype=='edit')

                <select name="table_id" id="table_id" class="form-control" onchange="redirect_to_url(this.value)">
                     <option value=""> -- Select Table-- </option>
                      @if(isset($total_no_tables))
                      <?php for($i=1; $i<=$total_no_tables;$i++) { $v=0; 
                      $check=0;
                       foreach ($complete_table as $k => $val) {
                             if($i==$val->table_id) { $check=1; break;}
                          }  
 
                    if($ctype=='' || ($ctype=="NULL")){
                     if($listassigntable!=''){
                          foreach($listassigntable as $assign){
                             if($assign==$i) {$v=1;  break;}
                          }
                        } 
                        ?>
                        @if($v==1)
                        <option value="{{$i}}" @if($check==1) style="color:red;font-weight: bold;" @endif @if($table_id==$i) selected="selected" @endif>{{$i}} 
                          
                        </option>
                         @endif
                       <?php } else { ?>
                        <option value="{{$i}}" @if($check==1) style="color:red;font-weight: bold;" @endif @if($table_id==$i) selected="selected" @endif>{{$i}}</option>
                       <?php } // else 
                           }  ?>
                       @endif
                   

                        
               </select>
                   @if ($errors->has('table_id'))
                      <span class="text-danger">{{ $errors->first('table_id') }}</span>
                  @endif      <span id="errmstable" class="text-danger"></span> 
                  @endif
              </td>
          <!--  <th> <label> C.U. Number<sup>*</sup> </label></th>  -->
               <td colspan="2"> <input type="hidden"   placeholder="C.U. Number" class="form-control" name="cu_no" id="cu_no"   
                value="{{isset($counting_pstabledeails)?$counting_pstabledeails['cu_no']:old('cu_no') }}">
                <span id="table_value">
				
                @if(count($counting_ps_evmvote)>0 && ($counting_ps_evmvote)) 
                       <p style="color:red;font-weight: bold;"> Edit of  Table -{{$table_id}}</p> 
                     <input type="hidden" name="poll" value="edit"/>   
                @elseif($table_id!='')
                         <p style="color:red;font-weight: bold;"> Fresh Entry of Table -{{$table_id}}</p>
                         <input type="hidden" name="poll" value="new"/> 
                @endif </span>
                  
                </td>
          </tr>  
           <input type="hidden"   placeholder="VV PAT Number" class="form-control" name="vvpat_no" id="vvpat_no" value="{{isset($counting_pstabledeails)?$counting_pstabledeails['vvpat_no']:old('vvpat_no') }}">
                   
            <tr> <th> <label> Polling Station Number<sup>*</sup> </label></th>
              <td> <select name="ps_no" id="ps_no" class="form-control" onchange="getpsname(this.value)">

                     <option value=""> -- Select PS No-- </option>
					 @php $old_ps=''; $filled_ps=0; @endphp 
                      @if(isset($ps_list))
                      @foreach($ps_list as $ps)
                      <?php   $check=0;  
                            foreach ($allpollingstationlist as $k => $list) { 
                            
                             if($ps->PS_NO==$list) { $filled_ps++; $check=1; break;}
                             if($ps->PS_NO==$psno) { $old_ps = $ps->PS_NO;}
                          } ?>
                        <option value="{{$ps->PS_NO}}" @if($check==1) style="color:red;font-weight: bold;" @endif @if($ps->PS_NO==$psno) selected="selected" @endif>
                          <b>{{$ps->PS_NO}}</b></option>       
                       @endforeach
                       @endif
               </select> 
			   <input type="hidden" value="{{$old_ps}}" name="old_ps">
                  @if ($errors->has('ps_no'))
                      <span class="text-danger">{{ $errors->first('ps_no') }}</span>
                  @endif   <span id="errmsgps" class="text-danger"></span> 
                </td>
               
                <td colspan="2"> <span id="psname" name="psname"  style="color:red;font-weight: bold;">{{$psname}}</span></td>
              
            </tr>
       </table>
   <table class="table table-bordered preview_table" style="width:100%">
        <thead>
          <tr class="sticky-header">
                  <th class="text-center">Sr. No</th>
                  <th>Candidate Name</th>
                  <th data-breakpoints="xs sm">Party</th>
                  <th data-breakpoints="xs sm">Number Of Votes </th>
          </tr>
        </thead>
        <tbody><?php $j=0;  ?>
             @if(!empty($master_data))
            @foreach($master_data as $md)  
           <?php $j++;  $evm='';   
                  if(!empty($counting_ps_evmvote)) 
                          foreach($counting_ps_evmvote as $evm){ 
                             if($md->nom_id==$evm['nom_id'] and $md->candidate_id==$evm['candidate_id']) 
                               { $cval=$evm['evm_vote']; break;   }
                          }
                         
            ?>         

              <tr data-expanded="true" class="row_table">
                <input type="hidden" name="candidate_id{{$j}}" value="@if(!empty($md)){{$md->candidate_id}} @endif"/>
                <input type="hidden" name="mid{{$j}}" value="@if(!empty($md)){{$md->id}} @endif"/>
                <input type="hidden" class="nom_id" name="nom_id{{$j}}" value="@if(!empty($md)){{$md->nom_id}} @endif"/>
                <input type="hidden" class="party_id" name="party_id{{$j}}" value="@if(!empty($md)){{$md->party_id}} @endif"/>

               <td class="text-center text_td"><span class="english">{{$j}}</span></td> 
              <td class="text_td"><span class="english">{{$md->candidate_name}}</span> 
                      <br>{{$md->candidate_hname}} </td>   
              <td class="text_td"><span class="english">{{$md->party_name}}</span> 
                      <br>{{$md->party_hname}} </td>
 

       
              <td  class="current_vote_td">  
                <input type="text" name="currentvote{{$j}}" class="evm_input" id="currentvote{{$j}}" value="{{isset($cval) ?$cval:old('currentvote'.$j) }} " maxlength="6"> 
                   <span id="errmsg{{$j}}" class="text-danger"></span>
              </td>  
             </tr>
               @endforeach 
            @endif 
            <input type="hidden" name="val" id="va" value="{{$j}}"> 
           
             <!-- <tr data-expanded="true">
              <td class="text-right" colspan="3">Rejected Votes </td> 
              <td>  --><input type="hidden" name="rejected_vote" class="evm_input input-error" id="rejected_vote" value="0"> 
              <!-- <span id="errmsg11" class="text-danger"></span>
              </td>  
            </tr> -->
            <tr data-expanded="true">
              <td class="text-right" colspan="3">Tendered Votes <br><small class="text-danger">Tendered Votes  not added to total votes.</small></td> 
              <td> <input type="text" name="tendered_vote" class="evm_input input-error" id="tendered_vote" value="{{$tendered_vote}}"> 
              <span id="errmsg11" class="text-danger"></span>
              </td>  
            </tr> 
             <tr data-expanded="true">
              <td class="text-right" colspan="3">Total <br><small class="text-danger">Please verify this total with manual record.</small></td> 
              <td>                
              <input type="text" name="total" class="evm_input input-error" id="total" value="" readonly="readonly"> 
              <span id="errmsg11" class="text-danger"></span>
              </td>  
            </tr>
          
        </tbody>
     
    </table>
   
  <div class="card-footer">
  <div class="row">
  <div class="col">
            @if($evm_finalized==0)
           
             <div class="form-group">
             <div class="float-left">
              @if($user_data->designation=="ROAC") 
                <input type="button" value="Round Declaration" class="btn btn-primary mr-auto pull-right" onclick="location.href = '{{$url}}/roac/counting/round-wise-results';">
              @endif
             </div>
             <div class="float-right">
             @if($user_data->designation=="ROAC") 
                <button type="button" id="evm" class="btn btn-primary getdata" data-toggle="modal" data-target="#evmvote">
                  Edit Previous Rounds  Votes</button> 
             @endif 
             @if($scheduled_round!=$complete_rounds || $ctype=='edit')
                <input type="button" id="submit_form" value="Print Preview" placeholder="" class="btn btn-success submit-button">
             @endif 
               </div>    
               
             </div>
			  @endif
             </div>
       </div>
  </div>
       
        </form> 
    
        @else
                 <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
        @endif      
            
      </div>
    </div>
    </div>
    </div>
    </section>
    </main> 
<!-- Modal -->
<div class="modal fade" id="evmvote" tabindex="-1" role="dialog" aria-labelledby="evmvote" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Polling Station Wise EVM Rounds Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form class="form-horizontal mb-0" id="election_form" method="POST" action="{{url('roac/counting/polling-station-wisevote-entry-edit')}}" >
      <div class="modal-body">    
                {{ csrf_field() }}         
    <div class=""> Select Round (You want to edit)<sup class="pagespanred">*</sup>
            <select name="rid" id="rid" class="form-control" required="required">
             <option value="" selected="selected">Select</option>
              <?php for($i=1;$i<=$complete_rounds; $i++){   ?> <option value="{{$i}}">{{$i}}</option> <?php } ?>
           </select>   
             
      </div>
</div>
      <div class="modal-footer">
         <button type="submit" class="btn btn-primary">Go</button>
      </div>
      </form>
      
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->
 <!-- Modal Content Ends Here  QUICK --> 
   <div class="modal fade bd-example-modal-xl show"  tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" data-target="#exampleModalCenter" aria-hidden="false" >
  <div class="modal-dialog modal-xl">
  
      <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title h4" id="myExtraLargeModalLabel">Table Wise Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
          <section class="mt-0">

  <div class="row">
  
<div class="col">
 
<div class="sticky-table sticky-ltr-cells">  
    @if(!empty($results))    
    
    <table  class="table table-striped table-bordered" style="width:100%">
        <thead>
                <tr><th colspan="2">Table No.</th>
                  <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
                          <th>{{$i}}</th> 
                 <?php  } ?>
                <th rowspan="2">Total</th><th rowspan="2">Brought From Previous Round</th><th rowspan="2">Cumulative Total</th> </tr>
              <tr><th colspan="2">Polling Booth Number</th>
                  <?php for($i=1; $i<=$total_no_tables; $i++) {   $field="ps".$i; ?>
                          <th> {{$pollingstationlist[$field]}}  </th> 
                 <?php  } ?>  </tr>
            <tr><th>Sr No.</th><th>Candidate Name</th>
                  <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
                          <th>  </th> 
                 <?php  } ?> <th>   </th><th>   </th><th>   </th> </tr>
        </thead>
      <tbody>
           <?php  $j=0; $k=0;   $sum = 0;?>
              @if(!empty($results))
            @foreach($results as $md)  
            <?php $j++;   ?>
              <tr><td>{{$j}}</td> <td>{{$md['candidate_name']}} </td> 
                    <?php for($i=1; $i<=$total_no_tables; $i++) {  $field="table".$i;  ?>
                         <td> {{$md[$field]}} </td> 
                    <?php  } ?>
                  <td> {{$md['total']}} </td> <td>{{$md['previous_total']}} </td> 
                        <td>{{$md['accumlative_total']}} </td></tr>

                <?php $k++; ?> 
            @endforeach 
                 <tr><td colspan="2">Total</td>
                  <?php for($i=1; $i<=$total_no_tables; $i++) {  $field="table".$i; ?>
                          <td> {{$grandresults->$field}} </td> 
                 <?php  } ?>  
                 <td>{{$grandresults->total}}</td><td>{{$grandprevious}}</td><td>{{$grandtotal}}</td></tr>  

                
                  
            @endif 
             </tbody> 
            </table> 
     @else
                 <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Enter current Rounds</h4></div>
        @endif      
  
   </div><!-- end responsive-->
  </div><!-- end col-->
  </div>

  </section>
      </div>
    
    </div> 
  </div>
</div>
 

<!-- end Model Content-->
 

<div class="modal fade" id="preview_evem_votes" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preview your entry</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Edit</button>
        <button type="button" id="preview_print" class="btn btn-primary">Download & Print</button>
        <button type="button" id="preview_submit" class="btn btn-success submit-button">Submit</button>
      </div>
      
      
    </div>
  </div>
</div>





@endsection
@section('script')

<!-- Waseem validation -->
<script type="text/javascript">
var filled_ps = '<?php echo $filled_ps ?>';
var total_ps = '<?php echo count($ps_list) ?>';
var empty_ps = parseInt(total_ps) - parseInt(filled_ps);
$(".tepsl").text(empty_ps);
$(document).ready(function () {  
  $('#election_form .evm_input').each(function(i,object){
      $(".evm_input").removeClass("input-error");
      $(object).on('keyup change keydown',function (e) {
          if (parseInt($(object).val()) >= 0 && !isNaN($(object).val()) && $(object).val().indexOf('.') == '-1'){
                $(object).removeClass("input-error");
                $(object).parent('td').find('.text-danger').text("").hide();
                $(object).val(trim_number($(object).val()));
      }else{
                $(object).addClass("input-error");
                $(object).parent('td').find('.text-danger').text("please enter positive numeric value..").show();
                $(object).val('');
      }
      calculate_total();
    });
    
  });
     $("#ps_no").focusout(function(){
          var ps_no1= $("#ps_no").val();
           
        $.ajax({
                url: '<?php echo url('/') ?>/roac/counting/pollingstationdetails',
                type: 'GET',
                data: "ps_no="+ps_no1+"&ac_no={!! @$ac->AC_NO !!}&st_code=<?php echo $st->ST_CODE ?>",
                dataType: 'json', 
            success: function(result){   
                 console.log(result);
               if(result['success'] == true){
                 $("#ps_no").val(ps_no1);
                 $("#psname").text(result['name']);
               }else{
                console.log('Error');
                $('#election_form #ps_no').next('.text-danger').text("This polling Station votes is allready Enter in your system").show();
                $("#psname").text('');
                $("#ps_no").val('');
               }
                
            }

      }); 
     }); 
    

  $("#election_form #submit_form").click(function(){
    var is_error = false;
    var total = 0;
    var ps_no=$('#election_form #ps_no').val();
    var table_id=$('#election_form #table_id').val();
    var tendered_vote=$('#election_form #tendered_vote').val();

    $('#election_form .evm_input').each(function(i,object){
        $(".evm_input").removeClass("input-error");
            if (parseInt($(object).val()) >= 0 && !isNaN($(object).val()) && $(object).val().indexOf('.') == '-1'){
                  $(object).removeClass("input-error");
                  $(object).parent('td').find('.text-danger').text("").hide();
                  $(object).val(trim_number($(object).val()));
      }else{
                  $(object).addClass("input-error");
                  $(object).parent('td').find('.text-danger').text("please enter positive numeric value..").show();
                  $(object).val('');
                  is_error = true;
      }
      if($(object).attr('id') != 'total'){
                  total += parseInt($(object).val());


      }
    });
     total=total-tendered_vote;
    if(total != $('#election_form #total').val()){
            $('#election_form #total').next('.text-danger').text("Total mismatched.").show();
            is_error = true;
    }
    if($('#election_form #round_id').val()==''){
            $('#election_form #round_id').next('.text-danger').text("please enter rounds Number.").show();
            is_error = true;
    }
    if($('#election_form #table_id').val()==''){
              $('#election_form #table_id').next('.text-danger').text("please enter table Number.").show();
              is_error = true;
    }
    // if($('#election_form #cu_no').val()==''){
    //   $('#election_form #cu_no').next('.text-danger').text("please enter CU Number.").show();
    //   is_error = true;
    // }
    // if($('#election_form #vvpat_no').val()==''){
    //   $('#election_form #vvpat_no').next('.text-danger').text("please enter VV PAT Number.").show();
    //   is_error = true;
    // }
     $("#ps_no").removeClass("text-danger");
     if($('#election_form #ps_no').val()==''){
     $('#election_form #ps_no').next('.text-danger').text("Please enter polling station no.").show();
      is_error = true;
    }
    if(is_error){
          return false;
    }else{
              $('#preview_evem_votes .modal-body').html('');
              $('#preview_evem_votes .modal-body').html($('.preview_table').clone()); 
              $('#preview_evem_votes').modal("show");
              $('#preview_evem_votes input').prop('disabled',true);
              $('#preview_evem_votes select').prop('disabled',true);
             
              $('#preview_evem_votes #table_id').val(table_id);
              $('#preview_evem_votes #ps_no').val(ps_no);
    }

  });

  $('#preview_print').click(function(e){
      var table_id  =   $('#election_form #table_id').val();
      var round_id  =   $('#election_form #round_id').val();
      var ps_no     =   $('#election_form #ps_no').val();
      var cu_no     =   $('#election_form #cu_no').val();
      var vvpat_no  =   $('#election_form #vvpat_no').val();
      var rejected_vote  =   $('#election_form #rejected_vote').val();
      var tendered_vote  =   $('#election_form #tendered_vote').val();
      var psname     =   $('#election_form #psname').text(); 
      var data = [];
      $('#election_form .preview_table tbody .row_table').each(function(index,object){
              data.push($(object).find('.nom_id').val()+'_'+$(object).find('.current_vote_td').find('input').val());
      });
      
      $.ajax({
                url: "{!! url('/roac/counting/pswisepdf') !!}",
                type: 'GET',
                data: "ac_no={!! @$ac->AC_NO !!}&ac_name={!! @$ac->AC_NAME !!}&round="+round_id+"&table_id="+table_id+"&ps_no="+ps_no+"&cu_no="+cu_no+" &vvpat_no="+vvpat_no+"&rejected_vote="+rejected_vote+"&tendered_vote="+tendered_vote+"&psname="+psname+"&json=1&print_table="+encodeURIComponent(data),
                dataType: 'json', 
        beforeSend: function() {
        },  
        complete: function() {
        },        
        success: function(json) {
                window.open("{!! url('/roac/counting/pswisepdf') !!}","_blank");
                $('#preview_submit').removeClass("display_none");
        },
        error: function(data) {
               var errors = data.responseJSON;
        }
      }); 
      
  });

  $('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to submit the table data. Before Submission make sure you have taken the printout and Verified the table details. Upon submission the data will be reflected in trends and results website. You can edit the data after the entry also.")){
            $(this).text('Processing...');
            $(this).prop('disabled',true);
            $("#election_form").submit();
    }else{
    }
  });

  calculate_total();

});

function trim_number(s) {
      while (s.substr(0,1) == '' && s.length>1) { s = s.substr(1,9999); }
      return s;
}

function calculate_total(){
  var total_count = 0;
  $('#election_form .evm_input').each(function(i,object){
    if($(object).attr('id') != 'total' && parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
            total_count = parseInt(total_count) + parseInt($(object).val());
    }
  });
     total_count=total_count-$('#election_form #tendered_vote').val();
  $('#election_form #total').val(total_count);
}

function redirect_to_url(id){   
  var round_id = $('#election_form #round_id').val();
  var ctype = $('#election_form #ctype').val();
   
  var encodround_id = btoa(round_id);
  var encodid = btoa(id);
  window.location.href = "{!! url('roac/counting/polling-station-wisevote-entry') !!}?ctype="+ctype+"&round_id="+encodround_id+"&table_id="+encodid;
}
  function getpsname(ps_no1){
             $('#election_form #ps_no').next('.text-danger').text("").hide();
             //alert(ps_no1);
        $.ajax({
                url: '<?php echo url('/') ?>/roac/counting/pollingstationdetails',
                type: 'GET',
                data: "ps_no="+ps_no1+"&ac_no={!! @$ac->AC_NO !!}&st_code=<?php echo $st->ST_CODE ?>",
                dataType: 'json', 
            success: function(result){   
                 console.log(result);
               if(result['success'] == true){
                $("#psname").text(result['name']);
               }else{
                console.log('Error');
                $('#election_form #ps_no').next('.text-danger').text("This polling Station votes is allready Enter in your system").show();
                $("#psname").text('');
                $("#ps_no").val('');
               }
                
            }

      }); 
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
