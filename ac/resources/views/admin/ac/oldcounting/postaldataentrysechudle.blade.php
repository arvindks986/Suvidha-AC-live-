@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Postal Ballot Vote Entry')
@section('content') 
  <?php  $st=getstatebystatecode($st_code);   
        if($ele_details->CONST_TYPE=="PC")
          $pc=getpcbypcno($st_code,$pc_no);
        $j=0;
  ?> 
<style type="text/css">
      th, td { white-space: nowrap;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
<!-- waseem css -->
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
    .table td:nth-child(1) span, .table td:nth-child(2) span{
      width: 300px;
      word-break: break-all;
      white-space: initial;
      float: left;
    }
    #preview_evem_votes input{
      border:0px;
      background: transparent;
    }
    #preview_evem_votes .table td:nth-child(1) span, #preview_evem_votes .table td:nth-child(2) span{
      width: auto !important;
    }
  </style>


 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Postal Ballot Vote Entry Form</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp; </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
    @if(Session::has('success_admin'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
       @endif   
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
    </div>
   
       
    <div class="card-body"> 
  
    @if(!$master_data->isEmpty())
  

        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/counting/verifypostalentry') }}" >
                {{ csrf_field() }} 
                  
                 <input type="hidden" name="leading_id" value="{{$winn_data->leading_id}}">
                 <input type="hidden" name="CONST_TYPE" value="{{$ele_details->CONST_TYPE}}">
                 <input type="hidden" name="CONST_NO" value="{{$ele_details->CONST_NO}}">
                 <input type="hidden" name="ST_CODE" value="{{$ele_details->ST_CODE}}">
                 <input type="hidden" name="ELECTION_ID" value="{{$ele_details->ELECTION_ID}}">
    <table class="table  table-bordered preview_table" style="width:100%">
        <thead>
            <tr><th>Sr. No</th><th width="200">Candidate Name</th><th>Party</th><th>EVM Votes</th><th>Postal Votes</th>@if($finalize==1) <th>Total Votes</th> @endif</tr>
        </thead>
        <tbody><?php $j=0;  //dd($master_data); ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
              <?php $j++;   $rejvote=$md->rejectedvote;  $totalpostalvote=$md->postaltotalvote;  ?>
             <input type="hidden" name="mid{{$j}}" value="{{$md->id}}">
             <input type="hidden" name="nom_id{{$j}}" value="{{$md->nom_id}}">
             <input type="hidden" name="candidate_id{{$j}}" value="{{$md->candidate_id}}">
              <tr>
                <td>{{$j}}</td>  
                <td><span>{{$md->candidate_name}} <b>Demo</b><br>{{$md->candidate_hname}}  @if($md->nom_id==$winn_data->nomination_id) <b>(Winning) </b>@endif @if($md->nom_id==$winn_data->trail_nomination_id) <b>(Trailing)</b>  @endif</span></td>   
                <td><span>{{$md->party_name}} <b>Demo</b><br>{{$md->party_hname}} </span></td> 
                <td><input type="hidden" name="priviousvote{{$j}}" value="{{$md->evm_vote}}" readonly="readonly"><span>{{$md->evm_vote}}</span></td> 
              @if($finalize==0)  
              <td> <input type="text" name="currentvote{{$j}}" maxlength="6" class="evm_input" id="currentvote{{$j}}" value="@if($md->postal_vote!=0){{isset($md->postal_vote) ?$md->postal_vote:old('currentvote'.$j)}}@endif"> 
                <span id="errmsg{{$j}}" class="text-danger"></span> </td> @else 
                <td><span>{{$md->postal_vote}}</span></td> 
                <td><span>{{$md->total_vote}}</span></td> 
                @endif
                </tr>

            @endforeach 
            @endif 
                <input type="hidden" name="val" id="va" value="{{$j}}">
           @if($finalize==0)
            <tr><td colspan="3">&nbsp;</td>   <td><b>Rejected Votes</b></td>  
              <td > <input type="text" name="rejectedvotes" id="rejectedvotes" class="evm_input" value="@if($rejvote!=0){{isset($rejvote) ?$rejvote:old('rejectedvotes') }}@endif"><span id="errrejecte" class="text-danger"></span></td>  </tr>
            <tr><td colspan="3">&nbsp;</td>   <td><b>Postal Total Votes</b></td>  
              <td> <input type="text" name="totalvotes" id="totalvotes" class="evm_input" value="@if($totalpostalvote!=0){{isset($totalpostalvote) ?$totalpostalvote:old('totalvotes') }}@endif">
                <span id="errtotal" class="text-danger"></span></td>  </tr>
             @endif
        </tbody>
     
    </table>
          <?php  $url = URL::to("/");  ?>
         @if($finalize==0)
             <div class="form-group float-right">  
                <input  id="submit_form" value="Update" type="button" class="btn btn-primary">
                @if($val==0)
                <input type="button" value="Finalized PC" class="btn btn-primary" onclick="location.href = '{{$url}}/ropc/counting/counting-finalized';" >
                @endif  
             </div>
            @endif
         
        </form>  
        @else
                 <p>No Records  Founds </p>  
        @endif      
            
    

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  </main>


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
        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Close</button>
        <button type="button" id="preview_submit" class="btn btn-primary">Ok</button>
      </div>
      
      
    </div>
  </div>
</div>

 
@endsection
@section('script')

<script type="text/javascript">
   
  $(document).ready(function () { 
    $('#election_form .evm_input').each(function(i,object){
      $(".evm_input").removeClass("input-error");
      $(object).on('keyup change',function (e) {
        if (parseInt($(object).val()) >= 0 && !isNaN($(object).val()) && $(object).val().indexOf('.') == '-1'){
          $(object).removeClass("input-error");
          $(object).parent('td').find('.text-danger').text("").hide();
          $(object).val(trim_number($(object).val()));
        }else{
          $(object).addClass("input-error");
          $(object).parent('td').find('.text-danger').text("please enter positive numeric value..").show();
          $(object).val('');
        }
      });
    });


  $("#election_form #submit_form").click(function(){
    
    var is_error = false;
    var total = 0;
    $('#election_form .evm_input').each(function(i,object){
      $(".evm_input").removeClass("input-error");
      if (parseInt($(object).val()) >= 0 && !isNaN($(object).val())  && $(object).val().indexOf('.') == '-1'){
        $(object).removeClass("input-error");
        $(object).parent('td').find('.text-danger').text("").hide();
        $(object).val(trim_number($(object).val()));
      }else{
        $(object).addClass("input-error");
        $(object).parent('td').find('.text-danger').text("please enter positive numeric value..").show();
        $(object).val('');
        is_error = true;
      }
      if($(object).attr('id') != 'totalvotes'){
        total += parseInt($(object).val());
      }
    });

    if(total != $('#election_form #totalvotes').val()){
      $('#election_form #totalvotes').next('.text-danger').text("Total mismatched.").show();
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

  $('#preview_submit').click(function(e){
    $(this).text('Processing...');
    $(this).prop('disabled',true);
    $("#election_form").submit();
  });

});

function trim_number(s) {
  while (s.substr(0,1) == '0' && s.length>1) { s = s.substr(1,9999); }
  return s;
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