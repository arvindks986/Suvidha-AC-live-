@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Postal Ballot Vote Entry')
@section('content') 
  <?php $j=0; ?> 

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
	.modal-big .modal-dialog{max-width: 900px;}
    .modal-big .modal-header{background-color: #f0587e; color: #fff; text-shadow: 1px 1px 1px #666; text-align: center;}
    .mcenter{font-size:18px; line-height: 30px;}
  </style>


 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Postal Ballot Vote Entry Form </h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b>  <span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp; </p></div>
         
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
  

        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/verify-postal-entry') }}" >
                {{ csrf_field() }} 

                 <input type="hidden" name="round_id" value="@if(isset($round_details)){{$round_details->id}} @endif"> 
                 <input type="hidden" name="leading_id" value="@if(isset($winn_data)){{$winn_data->leading_id}} @endif">
                 <input type="hidden" name="CONST_TYPE" value="@if(isset($ele_details)){{$ele_details->CONST_TYPE}} @endif">
                 <input type="hidden" name="CONST_NO" value="@if(isset($ele_details)){{$ele_details->CONST_NO}} @endif">
                 <input type="hidden" name="ST_CODE" value="@if(isset($ele_details)){{$ele_details->ST_CODE}} @endif">
                 <input type="hidden" name="ELECTION_ID" value="@if(isset($ele_details)){{$ele_details->ELECTION_ID}} @endif">
				 <input type="hidden" id="roname" name="roname">
    <table class="table  table-bordered preview_table" style="width:100%">
        <thead>
            <tr><th class="width">Sr. No</th><th width="200">Candidate Name</th><th>Party</th> <th>Postal Votes</th>@if($postal_finalized==1) <th>Total Votes</th> @endif</tr>
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
 

              @if($postal_finalized==0)  
              <td class="current_vote_td"> 
                <input type="text" name="currentvote{{$j}}" maxlength="6" class="evm_input postalentry" id="currentvote{{$j}}" value="@if($md->postalballot_vote!=0 or $round_details->postal==1){{isset($md->postalballot_vote) ?$md->postalballot_vote:old('currentvote'.$j)}}@endif"> 
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
           @if($postal_finalized==0)
            <tr class="row_table"><td colspan="1">&nbsp;</td>   <td colspan="2" class="text_td rejected_votes"><span class="english">Rejected Votes</span></td>  
              <td  class="current_vote_td"> <input type="text" name="rejectedvotes" id="rejectedvotes" class="evm_input postalentry" value="@if(isset($round_details))@if($round_details->rejected_votes!=0 or $round_details->postal==1){{isset($round_details->rejected_votes) ?$round_details->rejected_votes:old('rejectedvotes') }}@endif @endif"><span id="errrejecte" class="text-danger"></span></td>  </tr>








            <tr><td colspan="1">&nbsp;</td>   <td colspan="2"><b>Postal Total Votes</b></td>  
              <td> <input type="text" name="totalvotes" id="totalvotes" class="evm_input" value="@if(isset($round_details))@if($round_details->postal_total_votes!=0){{isset($round_details->postal_total_votes) ?$round_details->postal_total_votes:old('totalvotes') }}@endif @endif">
                <span id="errtotal" class="text-danger"></span></td>  </tr>
           <input type="hidden" name="tended_votes" id="tended_votes" class="evm_input1" value="0">
                
             @endif
        </tbody>
     
    </table>
         <?php  $url = URL::to("/");  ?>
         @if($postal_finalized==0)
             <div class="form-group float-right">  
                <button type="button" id="submit_form" class="btn btn-primary">Print Preview & Submit</button>
            </div>
           
         <div class="form-group float-left">       
          <input type="button" value="Finalize Postal Ballot votes" class="btn btn-primary checkInputBeforeSubmit">
           </div>
        @if($round_details->postal==1) @endif    
                  
            
            @endif
         
        </form> 
		
		@if($postal_finalized==1)
				
			@endif
		
		<div style="display:none;">
        @if($postal_finalized==0)<br><br>
        <div class="form-group float-left"> 
             <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/counting/upload-postal-results')}}" enctype="multipart/form-data" autocomplete='off'>
			  
               {{csrf_field()}} 
                <input type="hidden" name="file_name" value="postalvotes"> 
           <div class="row d-flex align-items-center ">
            <div class="col">
                <label for="resultstrends" class="col-form-label">Upload RDF Only PDF <span class="errorred">*</span>(Maximum size 10 MB)</label>
              <div class="file-upload">
                  <div class="file-select">
                    <div class="file-select-name" id="noFile">No file chosen...</div> 
                      <input type="file" name="resultstrends" id="resultstrends" class="custom-file-input affidavit form-control mr-auto" mutliple="true"  accept=".pdf">
                      <div class="file-select-button customchoose" id="fileName">Choose File</div>
                </div>
              </div>
                @if ($errors->has('resultstrends'))
                    <span style="color:red;">{{ $errors->first('resultstrends') }}</span>
                @endif
                <span id="errmsg1" class="text-danger"></span>
              
            </div>
             <div class="col-md-1 p-0 m-0" style="margin-bottom:O;">
                  <button type="submit" id="resultsuploads" class="btn btn-primary custombtn mt-2">Upload RDF</button> 
              </div>
               <div class="col-md-2"> &nbsp; </div>
          </div>

            </form>
           
            </div> 
        @endif
        @else
                 <p>No Records  Founds </p>  
        @endif      
            </div>
    

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
        <button type="button" id="preview_print" class="btn btn-primary">Download & Print</button>
        <button type="button" id="preview_submit" class="btn btn-primary">Submit</button>
      </div>
      
      
    </div>
  </div>
</div>
<div class="modal fade" id="preview_alert" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Please fill the postal ballot vote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">    
            <p>Postal ballot votes not updated.please enter votes to finalize.</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="preview_print" class="btn btn-primary" class="close" data-dismiss="modal">Close</button>
      </div>
      
      
    </div>
  </div>
</div>
<div class="modal modal-big fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Certificate of Correctness of Postal Ballot Votes</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form1">
   <div class="mb-3">
     <ol class="mcenter">
      <li> &nbsp; I, <strong>{{Auth::user()->name}} </strong> certify that the postal ballot vote entered/ updated </strong> has been printed & manually verified by me & the observer and is correct.</li>

     <li> &nbsp;  I, understand that upon pressing the 'Publish' button below,the postal ballot votes will be immediately published/ updated with the correct data and postal ballot votes will be  available in public domain.</li>

     <li> &nbsp; I, certify that the postal ballot votes publication on the server and at the counting center is done simultaneously.  </li>
    </ol>
      <p align="right"> <strong>Please enter your name:-</strong> <span><input type="text" name="ename" id="ename" value=""> </span> <span id="errmsg22" class="text-danger" style="font-size:16px; font-weight:bold;"></span></p>
	  <input type="hidden" id="ronamedb" value="{{str_replace(" ","",Auth::user()->name)}}">
      <h6 align="right">{{Auth::user()->name}}<br> Returning Officer: <br><small>{{date("d-m-Y H:i:s")}}  </small></h6>
      </div>
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="submit_final_form" class="btn btn-success submit-button">Publish</button>
      </div>
    </form>
      </div>
    </div>
  </div>
</div>

 
@endsection
@section('script')



<script type="text/javascript">
  $(".checkInputBeforeSubmit").click(function(){
	var is_error = false;
    var total = 0;
	var allcan = 0;
	var errcnt = 0;
    $('#election_form .postalentry').each(function(i,object){
      $(".postalentry").removeClass("input-error");
      if (parseInt($(object).val()) >= 0 && !isNaN($(object).val())  && $(object).val().indexOf('.') == '-1'){
        $(object).removeClass("input-error");
        $(object).parent('td').find('.text-danger').text("").hide();
        $(object).val(trim_number($(object).val()));
		//location.href = '{{$url}}/roac/counting/postal-counting-finalized';
		//alert(i);
		errcnt++;
      }else{
        $(object).addClass("input-error");
        $(object).parent('td').find('.text-danger').text("please enter postal ballot vote.").show();
        $(object).val('');
        is_error = false;
      }
      if($(object).attr('id') != 'totalvotes'){
        total += parseInt($(object).val());
      }
	  
    });
	allcan = $('#election_form .postalentry').length;
	if(allcan == errcnt){
		location.href = '{{$url}}/roac/counting/postal-counting-finalized';
	}else{
		$('#preview_alert').modal('show');
		
	}
  });
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
        url: "{!! url('/roac/counting/boothballot_pdf') !!}",
        type: 'GET',

        data: "ac_no={!!@$ac_no!!}&ac_name={!!@$ac_name!!}&round='Ballot'&json=1&print_table="+encodeURIComponent(body),
        dataType: 'json', 
        beforeSend: function() {
        },  
        complete: function() {
        },        
        success: function(json) {
          window.open("{!! url('/roac/counting/boothballot_pdf') !!}","_blank");
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
    /*if(confirm("Are you sure you want to submit the postal vote data.Before Submission make sure you have taken the printout and Verified the Postal details. Upon submission the same data will be reflected in trends and result Website. You can edit the vote after the entry also.")){
      $(this).text('Processing...');
      $(this).prop('disabled',true);
      $("#election_form").submit();
    }else{

    }*/
	$('#preview_evem_votes').modal('hide');
	$('#changestatus').modal('show');
  });
  $('#submit_final_form').click(function(e){
	  var txtrname = $("#ename").val();
	  var dbrname = $("#ronamedb").val();
	  $("#roname").val(txtrname);
	  if(txtrname==''){
		 $("#ename").focus();
		  $("#errmsg22").text("Please enter returning officer name.");
		  return false;
	  }
	  txtrname = txtrname.replaceAll(/\s/g,'');
	  if(txtrname != dbrname){
		  $("#errmsg22").text("Please enter correct name of returning officer.");
		  return false;
	  }else{
		  $("#election_form").submit();
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
