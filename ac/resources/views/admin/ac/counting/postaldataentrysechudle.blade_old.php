@extends('admin.layouts.ac.theme')
@section('title', 'Create Schedule')
@section('content') 
  <?php  $st=getstatebystatecode($st_code);   
         $ac=getacbyacno($st_code,$ac_no);
        $j=0;
  ?> 
 
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Postal Ballot Vote Entry Form</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp; </p></div>
         
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
        @if(!empty($master_data))
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/verifypostalentry') }}" >
                {{ csrf_field() }} 
                 <input type="hidden" name="new_table" value="{{$new_table}}"> 
                  <input type="hidden" name="round_id" value="{{$round_details->id}}"> 
                 <input type="hidden" name="leading_id" value="{{$winn_data->leading_id}}">
                 <input type="hidden" name="CONST_TYPE" value="{{$ele_details->CONST_TYPE}}">
                 <input type="hidden" name="CONST_NO" value="{{$ele_details->CONST_NO}}">
                 <input type="hidden" name="ST_CODE" value="{{$ele_details->ST_CODE}}">
                 <input type="hidden" name="ELECTION_ID" value="{{$ele_details->ELECTION_ID}}">

    <table   class="table table-striped table-bordered" style="width:100%">
        <thead>  
            <tr><th>Sr. No</th><th>Candidate Name</th><th>Party</th><th>EVM Votes</th><th>Postal Votes</th>@if($finalize==1) <th>Total Votes</th> @endif</tr>
        </thead>
        <tbody><?php $j=0;  //dd($master_data); ?>
           @if(!empty($master_data))
            @foreach($master_data as $md)  
              <?php $j++;     $cval=$md->postalballot_vote;  
                  $pvot=$md->total_vote-$cval;
                    if( $cval==0)  $cval='';
              ?>
              <input type="hidden" name="mid{{$j}}" value="{{$md->id}}">
             <input type="hidden" name="nom_id{{$j}}" value="{{$md->nom_id}}">
             <input type="hidden" name="candidate_id{{$j}}" value="{{$md->candidate_id}}">
              
              <tr><td>{{$j}}</td> <td>{{$md->candidate_name}} <br>{{$md->candidate_hname}}<b>Demo</b>   @if($md->nom_id==$winn_data->nomination_id) <b>(Winning) </b>@endif @if($md->nom_id==$winn_data->trail_nomination_id) <b>(Trailing)</b>  @endif</td>   
        <td>{{$md->party_name}} <br>{{$md->party_hname}}<b>Demo</b>  </td> 
               
              <td><input type="hidden" name="priviousvote{{$j}}" value="{{$pvot}}" readonly="readonly"><span>{{$pvot}}</span></td> 
             @if($finalize==0)
              <td><input type="text" name="currentvote{{$j}}" maxlength="6" id="currentvote{{$j}}" value="{{isset($cval) ?$cval:old('currentvote'.$j) }}"> 
                <span id="errmsg{{$j}}" class="text-danger"></span> </td> @else 
                <td><span>{{$cval}}</span></td> 
                <td><span>{{$md->total_vote}}</span></td> 
                @endif
                </tr>

            @endforeach 
            @endif 
               <input type="hidden" name="val" id="va" value="{{$j}}">  
             @if($finalize==0)
            <tr><td colspan="3">&nbsp;</td>   <td><b>Rejected Votes</b></td>  
              <td > <input type="number" name="rejectedvotes" id="rejectedvotes" value="{{isset($round_details) ?$round_details->rejected_votes:old('rejectedvotes') }}"><span id="errrejecte" class="text-danger"></span></td>  </tr>
            <tr><td colspan="3">&nbsp;</td>   <td><b>Postal Total Votes</b></td>  
              <td> <input type="number" name="totalvotes" id="totalvotes" value="{{isset($round_details) ?$round_details->postal_total_votes:old('totalvotes') }}"><span id="errtotal" class="text-danger"></span></td>  </tr>
            @else
            <tr><td colspan="3">&nbsp;</td>   <td><b>Rejected Votes</b></td>  <td><span>{{$round_details->rejected_votes}}</span></td> </tr>
            <tr><td colspan="3">&nbsp;</td>   <td><b>Postal Total Votes</b></td> <td><span>{{$round_details->postal_total_votes}}</span></td> </tr>
             @endif
              
        </tbody>
     
    </table>
          <?php  $url = URL::to("/");  ?>
         @if($finalize==0)
             <div class="form-group float-right">  
                <input type="submit" value="Update" class="btn btn-primary">
                @if($round_details->postal_total_votes!=0)
                <input type="button" value="Finalized AC" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/counting-finalized';" >
                @endif  
             </div>
            @endif
         
        </form>  
        @else
                <div class="col-md-6"> <p>No Records  Founds </p> </div> 
        @endif      
            
    

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  </main>
 
@endsection

<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script type="text/javascript">
   $(document).ready(function () {  
  //called when key is pressed in textbox
  var v = $("#va").val();
 $("#rejectedvotes").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errrejecte").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });  
$("#totalvotes").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errtotal").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });  
for (i = 1; i <=v; i++) { 
    $("#currentvote"+i).keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg"+i).html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });
  } // end for
  $("#election_form").submit(function(){
    var v = $("#va").val();
    
    for (i = 1; i <=v; i++) { 
         var k=i-1;
       var cvote = $("#currentvote"+i).val();
       
       if($("#currentvote"+i).val()=='')
          {  
          $("#errmsg"+k).text("");
          $("#errmsg"+i).text("Please enter Votes");
          $("#currentvote".i).focus();
          return false;
          }
    }
      

 
    });
});
 </script>