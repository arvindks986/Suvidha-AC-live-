@extends('admin.layouts.ac.theme')
@section('title', 'Candidate PS Wise Counting Details')
@section('bradcome', 'Generate Form20')
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
    .card-header h1, .card-header h2, .card-header h3, .card-header h4, .card-header h5, .card-header h6, nav.navbar .nav-menu{margin-bottom:16px;}
   
    .rotext {
    margin: 5px;
}
  </style>
 
 <main role="main" class="inner cover mb-3">
  
 <section>
  <div class="container">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header text-center">
                
  <div class="row"> 
                 <div class="col"> <h4 class="mr-auto">FORM 20 <br> FINAL RESULT SHEET</h4> 
                    <p>[SEE RULE 56C(2)(C)]</p> <h5 class="mr-auto"> ELECTION TO THE LEGISLATIVE ASSEMBLY</h5> 
                   </div>

                 </div>
                 <div class="row">
                 <div class="col"> 
                    <p class="mb-0">Total No. of  Electors in Assembly Constituency/segment  ....<b>{{$totalelectors->total}}</b></p>
                    <p class="mb-0">Name of  Assembly/segment  ...<b>{{$ac_no}}-{{$ac_name}}</b> Assembly Election</p>
                 </div>
         
                </div>
                <div class="row">
                   <div class="col-md-7"> </div>
                  <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach </div>  
 </div>
                </div>
 
      <div class="table-responsive card-body">
         @if(!empty($results))
        <table  class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr><th rowspan="2" colspan="2">Serial No. Of Polling Station</th>
                    <th colspan="{{$totalcandidate}}" align="text-center"> No of Valid Votes Cast in favour of</th>
                    <th rowspan="2"> Total of Valid Votes</th>
                    <th rowspan="2"> No. Of Rejected Votes</th>
                    <th rowspan="2"> NOTA</th>
                    <th rowspan="2"> Total </th>
                    <th rowspan="2"> No. Of Tendered Votes</th>
                </tr>
                <tr> 
                    @foreach($columecandidate as $cand)  
                          <th><div class="rotext">{{$cand->candidate_name}}</div></th> 
                    @endforeach
                     
                </tr>
                  
          </thead>
          <tbody> 
                @foreach($results as $record)

                  <tr>  
                     @foreach($record as $rec)
                           <td>{{$rec}}</td>
                     @endforeach
                  </tr>      
                @endforeach 
                <tr><th  colspan="2">Total EVM Votes </th>
                   @foreach($grandsum as $d) 
                     
                          <th> {{$d}}  </th> 
                 @endforeach
                    
                </tr>
                <tr><td  colspan="2">Total Postal Ballot Votes </td>
                   @foreach($postal_vote as $d) 
                     <td> {{$d}}  </td> 
                 @endforeach
                    
                </tr>
                <tr><th  colspan="2">Total Votes Polled</th>
                   @foreach($grand_allsum as $d) 
                     
                          <th> {{$d}}  </th> 
                 @endforeach
                    
                </tr> 
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
