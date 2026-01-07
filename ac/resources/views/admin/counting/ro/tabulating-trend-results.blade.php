@extends('admin.layouts.ac.theme')
@section('title', 'Candidate PS Wise Counting Details')
@section('bradcome', 'Tabulating Trends / Results')
@section('content') 
  <?php  $url = URL::to("/");  ?>

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
  
 <section>
  <div class="container">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4 class="mr-auto">Annexure for Tabulating Trends / Results</h4>  </div>  
                 
                 <div class="col-md-7"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp;  
                        <b class="bolt">AC Name:</b><span class="badge badge-info">{{$ac_name}}</span> &nbsp;&nbsp; 
                        <b class="bolt">Round No:</b><span class="badge badge-info"> {{$round}}</span></p></div>
         
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
   
    <div class="table-responsive card-body">
       <table  class="" style="width:100%">
        <thead>
         <tr><td>Select Rounds:- </td><td><select name="round_id" id="round_id" class="form-control" onchange="redirect_to_url(this.value)">
                     <option value=""> -- Select Round-- </option>
                      @if(isset($scheduled_round))
                      <?php for($i=1; $i<=$scheduled_round;$i++) { ?>
                        <option value="{{$i}}" @if($round_id==$i) selected="selected" @endif>{{$i}}</option>
                       <?php } ?>
                       @endif
               </select> </td>

         <td> @if(!empty($results))  
                <input type="button"  value="Download Tabulating Trends / Results and RDF" placeholder="" class="btn btn-success submit-button" onclick="location.href ='{{$url}}/roac/counting/download-tabulating-trend-results?round_id={{$encround}}';">  
              @endif</td>
        <td> <!-- @if(!empty($results))  
                    <input type="button" value="Results Publish" placeholder="" class="btn btn-success submit-button" onclick="location.href ='{{$url}}/roac/counting/round-wise-calculate-vote?round_id={{$encround}}';">  
              @endif --> </td></tr>
    </thead></table>
    @if(!empty($results))    
    
    <table  class="table table-striped table-bordered" style="width:100%">
        <thead>
                <tr><th colspan="2">Table No.</th>
                  <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
                          <th>{{$i}}</th> 
                 <?php  } ?>
                <th rowspan="2">Total</th><th rowspan="2">Brought From Previous Round</th><th rowspan="2">Cumulative Total</th> </tr>
              <tr><th colspan="2">Polling Booth Number</th>
                  <?php for($i=1; $i<=$total_no_tables; $i++) { $field="ps".$i; ?>
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
                    <?php for($i=1; $i<=$total_no_tables; $i++) { $field="table".$i;  ?>
                         <td> {{$md[$field]}} </td> 
                    <?php  } ?>
                  <td> {{$md['total']}} </td> <td>{{$md['previous_total']}} </td> 
                        <td>{{$md['accumlative_total']}} </td></tr>

                <?php $k++; ?> 
            @endforeach 
                 <tr><td colspan="2">Total</td>
                  <?php for($i=1; $i<=$total_no_tables; $i++) {  $field="table".$i;?>
                          <td> {{$grandresults->$field}} </td> 
                 <?php  } ?>  <td>{{$grandresults->total}}</td><td>{{$grandprevious}}</td><td>{{$grandtotal}}</td></tr>  

                  <!-- <tr><td colspan="2">&nbsp;</td>
                  <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
                          <td> Initial of Ro </td> 
                 <?php  } ?>  <td> Initial of Ro </td> <td>&nbsp;</td><td>&nbsp;</td></tr>
                 <tr><td colspan="2">&nbsp;</td>
                  <?php for($i=1; $i<=$total_no_tables; $i++) { ?>
                          <td> Initial of Observer </td> 
                 <?php  } ?>  <td> Initial of Observer </td> <td>&nbsp;</td><td>&nbsp;</td></tr> -->
            @endif 
             </tbody> 
            </table> 
    
        @else
                 <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
        @endif      
            
      </div>
    </div>
    </div>
    </div>
    </section>
    </main> 
 
  




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
    if($('#election_form #round_id').val()==''){
      $('#election_form #round_id').next('.text-danger').text("please enter rounds Number.").show();
      is_error = true;
    }
    if($('#election_form #table_id').val()==''){
      $('#election_form #table_id').next('.text-danger').text("please enter table Number.").show();
      is_error = true;
    }
    if($('#election_form #cu_no').val()==''){
      $('#election_form #cu_no').next('.text-danger').text("please enter CU Number.").show();
      is_error = true;
    }
    if($('#election_form #vvpat_no').val()==''){
      $('#election_form #vvpat_no').next('.text-danger').text("please enter VV PAT Number.").show();
      is_error = true;
    }
     if($('#election_form #ps_no').val()==''){
      $('#election_form #ps_no').next('.text-danger').text("please enter PS Number.").show();
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
      var table_id = $('#election_form #table_id').val();
      var round_id = $('#election_form #round_id').val();
      var ps_no = $('#election_form #ps_no').val();
      var cu_no = $('#election_form #cu_no').val();
      var vvpat_no = $('#election_form #vvpat_no').val();
       
      var data = [];
      $('#election_form .preview_table tbody .row_table').each(function(index,object){
        data.push($(object).find('.nom_id').val()+'_'+$(object).find('.current_vote_td').find('input').val());
      });
      console.log(data);
      $.ajax({
        url: "{!! url('/roac/counting/pswisepdf') !!}",
        type: 'GET',
        data: "ac_no={!! @$ac->AC_NO !!}&ac_name={!! @$ac->AC_NAME !!}&round="+round_id+"&table_id="+table_id+"&ps_no="+ps_no+"&cu_no="+cu_no+" &vvpat_no="+vvpat_no+"&json=1&print_table="+encodeURIComponent(data),
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

function redirect_to_url(id){
   var encodid = btoa(id);
        window.location.href = "{!! url('roac/counting/tabulating-trend-results')!!}?round_id="+encodid;
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
