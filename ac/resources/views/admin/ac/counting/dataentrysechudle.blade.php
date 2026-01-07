@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'EVM Vote Entry Form')
@section('content') 
  <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
         $comp_round1=$comp_round;
         $totalround=$round_details->scheduled_round; $j=0;  
         if($rid==0) $cr=$comp_round+1;  else { $cr=$rid; $comp_round=$rid; }
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
  </style>

 <main role="main" class="inner cover mb-3">
  <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-3 pl-0">
              <!-- Income-->
              <div class="card income">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div><b class="text-success mr-auto">Total Round Scheduled &nbsp; </b>
                  <span class="badge badge-success float-right">{{$round_details->scheduled_round}}</span></div>
              </div>
            </div>
          <div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b class="mr-auto">Complete Rounds</b> &nbsp; 
                  <span class="badge badge-info float-right">{{$comp_round1}}</span></div>
              </div>
            </div>
          <div class="col-lg-3 pr-0">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-warning">@if($round_details->scheduled_round!=$comp_round )<b class="mr-auto">Selected Rounds</b> &nbsp; <span class="badge badge-warning text-white float-right">{{$cr}}</span>
                  @else <b>Rounds Completed</b>@endif</div>   
              </div>
            </div>
      <div class="col-lg-3 pr-0">
              <!-- Income-->
              <div class="card income p-0">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <button type="button" style="padding: 18px; font-size:18px;" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target=".bd-example-modal-xl">Quick Summary of Rounds   </button>
                 
              </div>
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
                 <div class="col">
                  @if($round_details->scheduled_round >= $cr)
                  <h4 class="mr-auto">EVM Vote Entry Form  Round - {{$cr}}</h4>
                  @else
                  <h4 class="mr-auto">EVM Vote Entry</h4>
                  @endif
                </div> 




                <div class="col-md-7"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp;  <b class="bolt">AC Name:</b> 
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
           <?php $net=0; for($i=1; $i<=$totalround; $i++) {   if($i<=$comp_round1) { ?>
                  <div class="check-success">
                  <?php } else  { ?>
                      <div>
                   <?php } ?>                  
                   <span><i class="fa fa-check"></i></span> 
           <br />
            <small style="font-size:50%;">Round-{{$i}}</small>
                  </div>
            <?php } ?>
                               
          </div>
        </div>  
  </div>
        @if(!$master_data->isEmpty())
        
        <form class="form-horizontal mb-0" id="election_form" method="POST"  action="{{url('roac/counting/verifycounting-data-entry') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{csrf_field()}}  
                 <input type="hidden" name="new_table" value="{{$new_table}}">
                 <input type="hidden" name="leading_id" value="{{$winn_data->leading_id}}">
                 <input type="hidden" name="CONST_TYPE" value="{{$ele_details->CONST_TYPE}}">
                 <input type="hidden" name="CONST_NO" value="{{$ele_details->CONST_NO}}">
                 <input type="hidden" name="ST_CODE" value="{{$ele_details->ST_CODE}}">
                 <input type="hidden" name="ELECTION_ID" value="{{$ele_details->ELECTION_ID}}">
                 <input type="hidden" name="totalround" value="{{$round_details->scheduled_round}}">
                 <input type="hidden" name="complete_round" value="{{$comp_round1}}">
                 <input type="hidden" name="nrid" value="{{$rid}}">
                 
                 <input type="hidden" name="cschedule" value="{{$cr}}"> 

      
   <table class="table table-bordered preview_table" style="width:100%">
        <thead>
              <tr class="sticky-header">
      <th class="text-center">Sr. No</th>
      <th>Candidate Name</th>
      <th data-breakpoints="xs sm">Party</th>
      @if($round_details->scheduled_round!=$comp_round ||  $rid!="")
        <th data-breakpoints="xs sm">Previous Votes</th>
      @endif
      
      @if($round_details->scheduled_round >= $cr)
      <th data-breakpoints="xs sm"> Current Round - {{$cr}} </th>
      @else
     <th data-breakpoints="xs sm">Total Votes</th>
      @endif
    
      </tr>
        </thead>
        <tbody><?php $j=0;  ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  


            <?php 

              $array_generate = [];
              foreach ($md as $key => $value) {
                $array_generate[$key] = $value;
              }

              $previous_round_total = 0;
              $round_scheduled      = $cr;
              for($i=1; $i < $cr; $i++){
                $previous_round_total += $array_generate['round'.$i];
              }
 

            ?>

              <?php $j++;  
                
              if( $field!='') {
               //   $filter_ele = ['id'=>$md->id,'nom_id'=>$md->nom_id,'ac_no'=> $ac_no];
               // $total_value='';
               // $total_value=$this->CountingModel->grandtotalsum($new_table,$round,$filter_ele);
               //  evm_votes($table_name,$md->id,$md->nom_id,$data = array()){ 
                  $cval=$md->$field;  
                  $pvot=$md->total_vote-$cval-$md->postalballot_vote;
                   if($rid=="") { if( $cval==0)  $cval=''; }  if($rid!="") $cval=$md->$field; 
                }
              else {
                 $cval='';
                $pvot=$md->total_vote;
              }
              ?>
           
              <input type="hidden" name="candidate_id{{$j}}" value="@if(!empty($md)){{$md->candidate_id}} @endif"/>
              <tr data-expanded="true" class="row_table">

                <input type="hidden" name="mid{{$j}}" value="{{$md->id}}">
                <input type="hidden" class="nom_id" name="nom_id{{$j}}" value="{{$md->nom_id}}"/>

        <td class="text-center text_td"><span class="english">{{$j}}</span></td> 
        <td class="text_td"><span class="english">{{$md->candidate_name}}</span> <br>{{$md->candidate_hname}}    </td>   
        <td class="text_td"><span class="english">{{$md->party_name}}</span> <br>{{$md->party_hname}}   </td>

                <input type="hidden" name="priviousvote{{$j}}" value="{{$pvot}}">
              @if($round_details->scheduled_round!=$comp_round ||  $rid!="")
              <td class="previous_vote_td"><span>{{$previous_round_total}}</span> </td> @endif

       
              <td  class="current_vote_td"> @if($finalized_round==0 && $round_details->scheduled_round >= $cr)
               
               
                  <input type="text" name="currentvote{{$j}}" class="evm_input" id="currentvote{{$j}}" value="{{isset($cval) ?$cval:old('currentvote'.$j) }}" maxlength="6"> 
                   <span id="errmsg{{$j}}" class="text-danger"></span>
                
                @else <span>{{$pvot}}</span>@endif 
                
               </td>  
             </tr>
               <?php if($cval) $net=$net+$cval;  if($rid=="") {if($net==0) $net=''; }   ?>
            @endforeach 
            @endif 
            <input type="hidden" name="val" id="va" value="{{$j}}"> 

            @if($finalized_round==0 && $round_details->scheduled_round >= $cr)
             <tr data-expanded="true">
              <td class="text-right" colspan="4">Total <br><small class="text-danger">Please verify this total with manual record.</small></td> 
              <td>                
              <input type="text" name="total" class="evm_input input-error" id="total" value="{{$net}}" readonly="readonly"> 
              <span id="errmsg11" class="text-danger"></span>
              </td>  
            </tr>
          @endif
        </tbody>
     
    </table>
   
  <div class="card-footer">
  <div class="row">
  <div class="col">
  
  
          <?php  $url = URL::to("/");  ?>
           @if($finalized_round==0)
             <div class="form-group float-right"> 
        <button type="button" id="evm" class="btn btn-primary getdata" data-toggle="modal" data-target="#evmvote">Edit EVM Votes</button>  
             @if($round_details->scheduled_round==$comp_round and $rid==0)
                   <input type="button" value="Finalize Rounds" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/counting-evm-finalized';">
              @elseif( $rid==0) 
                  <input type="button" id="submit_form" value="Print Preview" placeholder="" class="btn btn-success submit-button">
              @else 
                   <input type="button" id="submit_form" value="Print Preview" placeholder="" class="btn btn-success submit-button">
              @endif
             </div>
             </div>
       </div>
  </div>
            @endif

          
         
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
<!-- Modal Content Ends Here --> 
   <div class="modal fade bd-example-modal-xl show"  tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" data-target="#exampleModalCenter" aria-hidden="false" >
  <div class="modal-dialog modal-xl">
  
      <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title h4" id="myExtraLargeModalLabel">Round Wise Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
          <section class="mt-0">

  <div class="row">
  
<div class="col">
 
<div class="sticky-table sticky-ltr-cells">  
  <table class="table   table-bordered table-hover modal-table" style="width:100%">
        <thead>  <tr class="sticky-header"><th>Sr. No</th><th data-breakpoints="xs sm">Candidate Name</th><th>Party</th>
                @for($k=1; $k<=$round_details->scheduled_round; $k++)
                  <th data-breakpoints="xs sm md lg">  Round&nbsp;&nbsp; {{$k}}</th>
                @endfor
                <th>Total Votes </th> </tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
              <?php $j++;    
               ?>
            <tr><td>{{$j}}</td> <td>{{$md->candidate_name}} <br>{{$md->candidate_hname}} </td> <td>{{$md->party_name}}  <br>{{$md->party_hname}} </td> 
            @for($k=1; $k<=$round_details->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>   
            @endfor 
            <td>{{@$md->total_vote-@$md->postalballot_vote}}  </td> 
             </tr>
            @endforeach 



            
            @endif 




        </tbody>   
     
    </table>
   </div><!-- end responsive-->
  </div><!-- end col-->
  </div>

  </section>
      </div>
    
    </div> 
  </div>
</div>
 

<!-- end Model Content-->

<!-- Modal -->
<div class="modal fade" id="evmvote" tabindex="-1" role="dialog" aria-labelledby="evmvote" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">EVM Rounds Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form class="form-horizontal mb-0" id="election_form" method="POST" action="{{url('roac/counting/counting-data-entry-edit')}}" >
      <div class="modal-body">    
                {{ csrf_field() }}         
    <div class=""> Select Round <sup class="pagespanred">*</sup>
            <select name="rid" id="rid" class="form-control" required="required">
             <option value="" selected="selected">Select</option>
              <?php for($i=1;$i<=$comp_round1; $i++){   ?> <option value="{{$i}}">{{$i}}</option> <?php } ?>
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
        <button type="button" id="preview_print" class="btn btn-primary">Print</button>
        <button type="button" id="preview_submit" class="btn btn-primary">Submit</button>
      </div>
      
      
    </div>
  </div>
</div>





@endsection
@section('script')

<!-- Waseem validation -->
<script type="text/javascript">
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

  $("#election_form #submit_form").click(function(){
    var is_error = false;
    var total = 0;
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

    if(total != $('#election_form #total').val()){
      $('#election_form #total').next('.text-danger').text("Total mismatched.").show();
      is_error = true;
    }

    if(is_error){
      return false;
    }else{
      $('#preview_evem_votes .modal-body').html('');
      $('#preview_evem_votes .modal-body').html($('.preview_table').clone());
      $('#preview_evem_votes').modal("show");
      $('#preview_evem_votes input').prop('disabled',true);
    }

  });

  $('#preview_print').click(function(e){
    
      var data = [];
      $('#election_form .preview_table tbody .row_table').each(function(index,object){
        data.push($(object).find('.nom_id').val()+'_'+$(object).find('.current_vote_td').find('input').val());
      });

      $.ajax({
        url: "{!! url('/roac/counting/pdf') !!}",
        type: 'GET',
        data: "ac_no={!! @$ac->AC_NO !!}&ac_name={!! @$ac->AC_NAME !!}&round={!! @$cr !!}&json=1&print_table="+encodeURIComponent(data),
        dataType: 'json', 
        beforeSend: function() {
        },  
        complete: function() {
        },        
        success: function(json) {
          window.open("{!! url('/roac/counting/pdf') !!}","_blank");
          $('#preview_submit').removeClass("display_none");
        },
        error: function(data) {
          var errors = data.responseJSON;
          console.log(errors);
        }
      }); 
      
  });

  $('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to submit the round data. Before Submission make sure you have taken the printout and Verified the round details. Upon submission the data will be reflected in trends and results website. You can edit the data after the entry also.")){
      $(this).text('Processing...');
      $(this).prop('disabled',true);
      $("#election_form").submit();
    }else{
    }
  });

  calculate_total();

});

function trim_number(s) {
  while (s.substr(0,1) == '0' && s.length>1) { s = s.substr(1,9999); }
  return s;
}

function calculate_total(){
  var total_count = 0;
  $('#election_form .evm_input').each(function(i,object){
    if($(object).attr('id') != 'total' && parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
      total_count = parseInt(total_count) + parseInt($(object).val());
    }
  });

  $('#election_form #total').val(total_count);
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
