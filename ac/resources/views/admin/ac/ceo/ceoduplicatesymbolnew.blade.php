@extends('admin.layouts.pc.report-theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome', 'Duplicate Symbol Candidate Reports')
@section('content') 
  <?php  $st=getstatebystatecode($st_code);  
  date_default_timezone_set('Asia/Kolkata');
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
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Duplicate Symbol Candidate Reports</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp;  
           <a href="{{url('pcceo/ceo-duplicatesymol-pdf')}}" class="btn btn-info" role="button">Export PDF</a> &nbsp;&nbsp;
           <a href="{{url('pcceo/ceo-duplicatesymol-excel')}}" class="btn btn-info" role="button">Export Excel</a>
          </p></div>
         
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
        
        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/postal-data-entry') }}" >
                {{ csrf_field() }} 
                 
          
   <table   class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
              <th>PC No.</th>
              <th>PC Name</th>
              <th>Symbol Name</th>
              <th>Candidate Name</th>
              <th>Party</th>

            </tr>
        </thead>
        <tbody> 
             @foreach($lists as $list) 
          <tr>
    <?php
    // print_r($list);die;
    $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
    $pclist=getpcbypcno($list->st_code,$list->pc_no);
    $symbol_data=getsymbolbyid($list->symbol_id);
    $partyDetails=getpartybyid($list->party_id);
    ?>
             <td>{{$list->pc_no}}</td>
             <td>@if(isset($pclist)) {{$pclist->PC_NAME}}@endif</td>
              <td>@if(isset($symbol_data)) {{$symbol_data->SYMBOL_DES}} @endif</td>
              <td>{{$candidatedetails->cand_name}}</td>
              <td>@if(isset($partyDetails)) {{$partyDetails->PARTYNAME}} @endif</td>  
            
          </tr>
            @endforeach
      
        </tbody>
     
    </table>
     </form>  
    
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