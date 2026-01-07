@extends('admin.layouts.ac.theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome', 'List of candidate finalize')
@section('content') 
   <?php  $st=getstatebystatecode($user_data->st_code);   ?> 
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> All Nomination Finalize</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
               </p>
              </div>
            </div>
      </div>
  
 <div class="card-body">
       @if(Session::has('success_mes'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_mes')) }}</strong> 
              </div>
          @endif
      @if(Session::has('error_messsage'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('error_messsage')) }}</strong> 
              </div>
          @endif    
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
            <tr>
              <th>Sl. No.</th><th>Constituency Name</th><th>List of Contesting Candidates </th> <th>Finalized</th><!--<th>Finalize Message</th> <th>De-Finalize Message</th>-->
              <th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php $i=1; $url = URL::to("/");   ?>
      
      @foreach ($lists as $key=>$list)
        <?php  
            if($list->const_type=='AC') {
              $const=getacbyacno($list->st_code,$list->const_no);
              $const_name=$const->AC_NAME; 
              }
          elseif($list->const_type=='PC') {
              $const=getpcbypcno($list->st_code,$list->const_no);
              $const_name=$const->PC_NAME;  
              } 
        ?> 
        
        
          <tr><td>{{$i}}</td>
           <td>{{$list->const_no}}-{{$const_name}}</td>
           <td> 
            <!-- <button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acceo/download-form7a-english?st_code={{$list->st_code}}&ac_no={{$list->const_no}}';">Download Form7A in English</button>  -->
			<?php $disable_form=1; //$disable_stcode_arr = array('S14','S29','S10','S17');@if(!in_array($user_data->st_code, $disable_stcode_arr))?>
			@if($disable_form=='0')
            <!--<button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acceo/download-form7a-vernacular?st_code={{$list->st_code}}&ac_no={{$list->const_no}}';">Download Form7A in Vernacular</button> 
			<button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acceo/download-form7a-bilingual?st_code={{$list->st_code}}&ac_no={{$list->const_no}}';">Download Form7A in Bilingual</button>--> 
			@endif
             <button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acceo/download-contesting-candidate/{{$list->const_no}}';">Download Form7A in English</button>  


           </td>
           
           <td>@if($list->finalized_ac==1) Yes @else NO @endif</td>
           <!--<td>{{$list->finalized_message}} </td> <td>{{$list->definalized_message}} </td> -->
           <td>@if($list->finalize_date!='') Finalize D:- {{date("d-m-Y",strtotime($list->finalize_date))}} @endif <br>
            @if($list->definalize_date!='') Definalize D:- {{date("d-m-Y",strtotime($list->definalize_date))}} @endif</td>
			<td style="display:none;">&nbsp;</td>
            <td>@if($list->finalized_ac==1) <button type="button" id="{{$list->id}}" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus" data-constno="{{$list->const_no}}" data-id="{{$list->id}}" data-consttype="{{$list->const_type}}" data-message="{{$list->definalized_message}}">De-Finalize</button> @endif</td>
          </tr>
           <?php $i++;?>
          @endforeach
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

  <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Nomination De-Finalize</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('acceo/definalizevalidation') }}" >
                {{ csrf_field() }}   
            <input type="hidden" name="st_code" id="st_code" value="{{$st_code}}" readonly="readonly">
            <input type="hidden" name="id" id="id" value="" readonly="readonly">
            <input type="hidden" name="actype" id="actype" value="" readonly="readonly">
            <input type="hidden" name="ac_no" id="ac_no" value="" readonly="readonly">
               
    
    <div class="mb-3">
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="marks1" name="action" value="1" class="custom-control-input" checked>
        <label class="custom-control-label" for="customRadioInline1">De-Finalize</label>
      </div>
       
      </div>
    
       <div class="mb-3">
       <p>COE De-finalize Message: <sup>*</sup></p>
    
    <textarea class="form-control " id="definalized_message" name="definalized_message" placeholder="Required example textarea" required="required"></textarea>
    <span id="err" class="text-danger"></span>
    <div class="invalid-feedback">
      Please enter a message in the textarea.
    </div>
    
    

  </div>
  
   
 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->
@endsection
@section('script')
<script type="text/javascript">
           jQuery(document).ready(function(){
          
          $("#election_form").submit(function(){
             if($("#definalized_message").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Please enter message");
                    $("#definalized_message").focus();
                    return false;
                    }
               });
        });
  
  $(document).on("click", ".getdata", function () {  
     
       constno = $(this).attr('data-constno');
       rid = $(this).attr('data-id'); 
       var consttype = $(this).attr('data-consttype');
       var message = $(this).attr('data-message');
       $("#id").val(rid);
       $("#actype").val(consttype);
       $("#ac_no").val(constno);
       $("#definalized_message").val(message);  
   });
    
    
</script>
 
@endsection
