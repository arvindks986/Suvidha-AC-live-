@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Postal Ballot Vote Entry')
@section('content') 
  <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
           $j=0;
  ?> 

<style type="text/css">
     
              div.dataTables_wrapper {margin:0 auto;} 
              table.table.table-bordered.preview_table td:first-child {  width: 80px;}
               table.table.table-bordered.preview_table td:first-child span.english {  width:auto;}
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
                 <div class="col"><h4>Postal Ballot Vote Entry Form </h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b>  <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp; </p></div>
         
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
  

        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/verifypostalentry') }}" >
                {{ csrf_field() }} 

                 <input type="hidden" name="round_id" value="@if(isset($round_details)){{$round_details->id}} @endif"> 
                 <input type="hidden" name="leading_id" value="@if(isset($winn_data)){{$winn_data->leading_id}} @endif">
                 <input type="hidden" name="CONST_TYPE" value="@if(isset($ele_details)){{$ele_details->CONST_TYPE}} @endif">
                 <input type="hidden" name="CONST_NO" value="@if(isset($ele_details)){{$ele_details->CONST_NO}} @endif">
                 <input type="hidden" name="ST_CODE" value="@if(isset($ele_details)){{$ele_details->ST_CODE}} @endif">
                 <input type="hidden" name="ELECTION_ID" value="@if(isset($ele_details)){{$ele_details->ELECTION_ID}} @endif">
    <table class="table  table-bordered preview_table" style="width:100%">
        <thead>
            <tr><th class="width">Sr. No</th><th width="200">Candidate Name</th><th>Party</th> <th>Postal Votes</th>@if($finalize==1) <th>Total Votes</th> @endif</tr>
        </thead>
        <tbody>
          <?php $j=0;  //dd($round_details); //dd($master_data); ?>
            @if(!empty($master_data))
            @foreach($master_data as $md)  
              <?php $j++;   $evm_votes=evm_votes($new_table,$md->id,$md->nom_id); ?>
             <input type="hidden" name="mid{{$j}}" value="{{$md->id}}">
             <input type="hidden" name="nom_id{{$j}}" value="{{$md->nom_id}}">
             <input type="hidden" name="candidate_id{{$j}}" value="{{$md->candidate_id}}">
              <tr class="row_table">
                <td  class="text-center width text_td"><span class="english">{{$j}}</span></td>  
                <td  class="text_td"><span class="english">{{$md->candidate_name}}</span> 
                  <br>{{$md->candidate_hname}}  
                              @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> {{config('public_config.label_lead') }} </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>{{config('public_config.label_wonning') }}</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>{{config('public_config.label_trail') }}</b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                          <b> {{config('public_config.label_tie') }}  </b>
                                @endif 

                          
                </td>   
                <td class="text_td"><span class="english">{{$md->party_name}}</span> <br>{{$md->party_hname}}</td>

               <!--  <td class="previous_vote_td"><input type="hidden" name="priviousvote{{$j}}" value="{{$evm_votes->grant_total}}" readonly="readonly"><span>{{$evm_votes->grant_total}}</span></td>  -->

              @if($finalize==0)  
              <td class="current_vote_td"> <input type="text" name="currentvote{{$j}}" maxlength="6" class="evm_input" id="currentvote{{$j}}" value="@if($md->postalballot_vote!=0){{isset($md->postalballot_vote) ?$md->postalballot_vote:old('currentvote'.$j)}}@endif"> 
                <span id="errmsg{{$j}}" class="text-danger"></span> 
              </td> 

              @else 
                <td><span>{{$md->postalballot_vote}}</span></td> 
                <td><span>{{$md->total_vote}}</span></td> 
              @endif
              
              </tr>

            @endforeach 
            @endif 


                <input type="hidden" name="val" id="va" value="{{$j}}">
           @if($finalize==0)
            <tr class="row_table"><td colspan="1">&nbsp;</td>   <td colspan="2" class="text_td rejected_votes"><span class="english">Rejected Votes</span></td>  
              <td  class="current_vote_td"> <input type="text" name="rejectedvotes" id="rejectedvotes" class="evm_input" value="@if(isset($round_details))@if($round_details->rejected_votes!=0){{isset($round_details->rejected_votes) ?$round_details->rejected_votes:old('rejectedvotes') }}@endif @endif"><span id="errrejecte" class="text-danger"></span></td>  </tr>








            <tr><td colspan="1">&nbsp;</td>   <td colspan="2"><b>Postal Total Votes</b></td>  
              <td> <input type="text" name="totalvotes" id="totalvotes" class="evm_input" value="@if(isset($round_details))@if($round_details->postal_total_votes!=0){{isset($round_details->postal_total_votes) ?$round_details->postal_total_votes:old('totalvotes') }}@endif @endif">
                <span id="errtotal" class="text-danger"></span></td>  </tr>
           <input type="hidden" name="tended_votes" id="tended_votes" class="evm_input1" value="0">
                
             @endif
        </tbody>
     
    </table>
         <?php  $url = URL::to("/");  ?>
         @if($finalize==0)
             <div class="form-group float-right">  
                <buttin type="button" id="submit_form" class="btn btn-primary">Print Preview</buttin>
                
                <input type="button" value="Finalize Postal Ballot votes" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/counting-finalized';" >
             
                  
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
        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Edit</button>
        <button type="button" id="preview_print" class="btn btn-primary">Print</button>
        <button type="button" id="preview_submit" class="btn btn-primary">Submit</button>
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
        calculate_total();
      });
    });

    $('#election_form .evm_input1').on('keyup change',function (e) {
        if (parseInt($('#election_form .evm_input1').val()) >= 0 && !isNaN($('#election_form .evm_input1').val()) && $('#election_form .evm_input1').val().indexOf('.') == '-1'){
          $('#election_form .evm_input1').removeClass("input-error");
          $('#election_form .evm_input1').parent('td').find('.text-danger').text("").hide();
          $('#election_form .evm_input1').val(trim_number($('#election_form .evm_input1').val()));
        }else{
          $('#election_form .evm_input1').addClass("input-error");
          $('#election_form .evm_input1').parent('td').find('.text-danger').text("please enter positive numeric value..").show();
          $('#election_form .evm_input1').val('');
        }
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


    if (parseInt($('#election_form .evm_input1').val()) >= 0 && !isNaN($('#election_form .evm_input1').val()) && $('#election_form .evm_input1').val().indexOf('.') == '-1'){
          $('#election_form .evm_input1').removeClass("input-error");
          $('#election_form .evm_input1').parent('td').find('.text-danger').text("").hide();
          $('#election_form .evm_input1').val(trim_number($('#election_form .evm_input1').val()));
    }else{
        $('#election_form .evm_input1').addClass("input-error");
        $('#election_form .evm_input1').parent('td').find('.text-danger').text("please enter positive numeric value..").show();
        $('#election_form .evm_input1').val('');
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
    
    var head = "<html><head><title></title><style>table {border-collapse: collapse;}td,th{font-size:13px;vertical-align:top;}</style></head><body style='padding:20px 20px;width:600px;'>";
  
      var foot = "</body>";
      var body = '';
       var aggragate_total=0; 
  
      $('#election_form .preview_table tbody .row_table').each(function(index,object){
        body += "<tr>";
        var total = 0;
        $(object).find('td').each(function(index2, object2){

            if($(object2).hasClass('text_td')){
              var colspan = '';
              if($(object2).hasClass('has_class_total')){
                colspan = 5;
              }
              if($(object2).hasClass('rejected_votes')){
                colspan = 3;
              }
              body += "<td colspan='"+colspan+"'>"+$(object2).find('.english').html()+"</td>";
            }

            // if($(object2).hasClass('previous_vote_td')){
            //     total += parseInt($(object2).text());
            //     body += "<td>"+$(object2).text()+"</td>";
            // }
            
            if($(object2).hasClass('current_vote_td')){
                total += parseInt($(object2).find('input').val());
                body += "<td>"+parseInt($(object2).find('input').val())+"</td>";
            }
          
        });
        // body += "<td>"+total+"</td>";
        body += "</tr>";
         aggragate_total += total;
      });
      html = '';
      body += "<tr><td colspan='3'>Total</td><td>"+aggragate_total+"</td></tr>";
      html += "<table class='' border='1' cellpadding='15' style='verticle-align:top;'>";
      html += body;
      html += "</table>";
  

      $.ajax({
        url: "{!! url('/roac/counting/ballot_pdf') !!}",
        type: 'GET',
        data: "ac_no={!! @$ac->AC_NO !!}&ac_name={!! @$ac->AC_NAME !!}&round='Ballot'&json=1&print_table="+encodeURIComponent(body),
        dataType: 'json', 
        beforeSend: function() {
        },  
        complete: function() {
        },        
        success: function(json) {
          window.open("{!! url('/roac/counting/ballot_pdf') !!}","_blank");
          $('#preview_submit').removeClass("display_none");
        },
        error: function(data) {
          var errors = data.responseJSON;
          console.log(errors);
        }
      });  
    // $(this).addClass("display_none");
    
  });

  $('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to submit the postal vote data.Before Submission make sure you have taken the printout and Verified the Postal details. Upon submission the same data will be reflected in trends and result Website. You can edit the vote after the entry also.")){
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
    if($(object).attr('id') != 'totalvotes' && parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
      total_count = parseInt(total_count) + parseInt($(object).val());
    }
  });
  $('#election_form #totalvotes').val(total_count);
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