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

    .modal-big .modal-dialog{max-width: 900px;}
    .modal-big .modal-header{background-color: #f0587e; color: #fff; text-shadow: 1px 1px 1px #666; text-align: center;}
    .mcenter{font-size:18px; line-height: 30px;}

  </style>
 
 <main role="main" class="inner cover mb-3">
  
 <section>
  <div class="container">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4 class="mr-auto">Published Evm Votes</h4>  </div>  
                 
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
                         <td> {{$md[$field]}}</td> 
                    <?php  } ?>
                  <td> @if(isset($md['total'])){{$md['total']}} @endif</td> <td>@if($md['previous_total']>0){{$md['previous_total']}} @endif</td> 
                        <td>@if(isset($md['accumlative_total'])){{$md['accumlative_total']}}@endif </td></tr>

                <?php $k++; ?> 
            @endforeach 
                 <tr><td colspan="2">Total</td>
                  <?php for($i=1; $i<=$total_no_tables; $i++) {  $field="table".$i;?>
                          <td> @if($grandresults->$field>0){{$grandresults->$field}}@endif </td> 
                 <?php  } ?>  <td>{{$grandresults->total}}</td><td>@if($grandprevious>0){{$grandprevious}}@endif</td><td>{{$grandtotal}}</td></tr>  

                  
            @endif 
             </tbody> 
            </table>   
            <div>
            <table>
            <tr><td> 
                  <input type="button" value="Back" class="btn btn-primary mt-2" onclick="location.href = '{{$url}}/roac/counting/round-wise-results';"> 
              </td>
              <td>
                  <input type="button"  value="Download Annexure for Tabulating Trends / Results and RDF Report" class="btn btn-info custombtn mt-2" onclick="location.href ='{{$url}}/roac/counting/download-tabulating-trend-results?round_id={{$encround}}';">
              </td>
              <td>   
             
             @if($publish==0)
              <button type="button"  class="btn btn-success submit-button mt-2 getdata" data-toggle="modal" data-target="#changestatus"> Proceed for Publication  </button> 
<!-- 
               <form action="{{url('roac/counting/round-wise-calculate-vote?round_id=')}}{{$encround}}" method="get">
                 {{csrf_field()}} 
                 <input type="hidden" name="round_id" value="{{$encround}}">
                 
                <input type="submit" value=" Publish" placeholder="" class="btn btn-success submit-button"  >
              </form> -->
             @endif  
			     
          </td>
            
          </tr></table>
            </div>

            <div>
           
           
            </div>
        @else
                 <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
        @endif      
            <!-- onclick="location.href ='{{$url}}/roac/counting/round-wise-calculate-vote?round_id={{$encround}}';" -->
      </div>
    </div>
    </div>
    </div>
    </section>
    </main> 
      
        <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal modal-big fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Certificate of Correctness of Round-wise data</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form1" action="{{url('roac/counting/round-wise-calculate-vote?round_id=')}}{{$encround}}" method="get">
                 {{csrf_field()}} 
                 <input type="hidden" name="round_id" value="{{$encround}}" readonly="readonly">
				 <input type="hidden" id="roname" name="roname">
   <div class="mb-3">
     <ol class="mcenter">
      <li> &nbsp; I, <strong>{{$name}} </strong> certify that the table-wise data entered/ updated for  round <strong>{{$round}}</strong> has been printed & manually verified by me & the observer and is correct.</li>

     <li> &nbsp;  I, understand that upon pressing the 'Publish' button below,the round will be immediately published/ updated with the correct data and round-wise data will be  available in public domain.</li>

     <li> &nbsp; I, certify that the round-wise publication on the server and at the counting center is done simultaneously.  </li>
    </ol>
      <p align="right"> <strong>Please enter your name:-</strong> <span><input type="text" name="ename" id="ename" value=""> </span> <span id="errmsg22" class="text-danger" style="font-size:16px; font-weight:bold;"></span></p>
		<input type="hidden" id="ronamedb" value="{{str_replace(" ","",Auth::user()->name)}}">
      <h6 align="right">{{$name}}<br> Returning Officer: <br><small>{{$sub_date}}  </small></h6>
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
<!-- Modal Content Ends Here -->


@endsection
@section('script')
 <script>
    function ConfirmDelete()
    {
      var x = confirm("Are you sure, You still want to display the result?");
        if (x) {
               return true;
               
            }
        else{
              return false;
            }
      }
	  
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
		  $("#election_form1").submit();
	  }
    });
   
   $(document).ready(function () {  
  //called when key is pressed in textbox
   
  $("#election_form").submit(function(){
       
    if($("#resultstrends").val()=='')
          {  
          $("#errmsg").text("");
          $("#errmsg1").text("Please select pdf file");
          $("#resultstrends").focus();
          return false;
          }
      

 
    });

  $("#election_form1").submit(function(){
       
    if($("#ename").val()=='')
          {  
          $("#errmsg2").text("");
          $("#errmsg2").text("Please enter your name");
          $("#ename").focus();
          return false;
          }
      

 
    });
});
  
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
