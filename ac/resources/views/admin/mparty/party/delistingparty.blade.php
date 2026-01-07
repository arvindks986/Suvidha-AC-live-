@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'List of delisted Parties')
@section('content') 
<?php $i=1; $url = URL::to("/");   ?>
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4>{{$heading_title}}</h4></div> 
            <div class="col-md-8"> <span><b>Total Records:- {{$total}}</b></span>
            <button type="button" class="btn btn-primary getdata" data-toggle="modal" data-target="#delisting"> Delisting Parties</button> 
		<form class="form-inline pull-right">
			
			 
          
			<div class="form-group float-right"> 
				<label for="noofcards" class="mr-3">Select Party Type</label> 
			  <form name="frmparty" id="frmparty" method="POST"  action="" >
				<select name="party_type" id="party_type" onchange="this.form.submit();">
                  @foreach($mpartytype as $iterate)
                     @if($party_type==$iterate['id'])
              	     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
            		@endforeach
                </select>
              </form>
		    </div>				
		     
        </form>
		</div>   
            </div>
      </div>
  
 <div class="table-responsive card-body">
      <div class="row">
	    @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
           <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         
	</div>
    @if(isset($lists))  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
              <th>Sl. No.</th>
              <th>Party Abbre</th>
              <th>Party Name</th> 
              <th>Party Abbre In Hindi</th>
              <th>Party Name In Hindi</th> 
              <th>Party Type</th>
              <th>Action</th>
               
          </tr>
        </thead>
        <tbody>
        	 
      @foreach ($lists as $key=>$list)
         <?php 

         ?>
           <tr><td>{{$i}}</td>
           <td>{{$list['PARTYABBRE']}}</td>
           <td>{{$list['PARTYNAME']}}</td>
           <td width="100px">{{$list['PARTYHABBR']}}</td>
           <td width="100px">{{$list['PARTYHNAME']}}</td>
           <td>@if($list['PARTYTYPE']=="N") National @endif 
			           @if($list['PARTYTYPE']=="S") State  @endif 
			          @if($list['PARTYTYPE']=="U") Unrecognized @endif
			     </td>
           <td>  <a href=" {{$url.'/mparty/view-delisted-details?id='.encrypt_string($list['CCODE'])}}">Action Logs</a> </td>
           
          </tr>
           <?php $i++;?>
          @endforeach
        </tbody>
    </table>

	</div>
	 @else
      <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
  @endif
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>
  <!-- Modal Content Starts here -->
<!-- Modal -->
<div class="modal fade" id="delisting" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content">
<div class="modal-header mb-3">
<h4 class="modal-title" id="exampleModalLabel">Delisting Parties</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
    <form class="form-horizontal" id="election_formd" method="post" action="{{$action}}" enctype="multipart/form-data" autocomplete='off'>
{{csrf_field()}}

<div class="form-group row">
<div class="col-md-4">Political Party:- <sup>*</sup></div>  

<div class="col-md-8">
  @if(isset($parties) and ($parties)) 
      <select name="party" id="party1"  class="form-control">
      <option value=""> -- Select One --</option>
      @foreach($parties as $p)
      <option value="{{$p['CCODE']}}">{{$p['PARTYABBRE']}}-{{$p['PARTYNAME']}}</option>
      @endforeach

      </select>
      @if ($errors->has('party'))
      <span style="color:red;">{{ $errors->first('party') }}</span>
      @endif
     <span  class="text-danger"></span>
 
@endif
</div>
</div>
  
<div class="form-group row">
<div class="col-md-4"> Remarks:- <sup>*</sup></div>  

<div class="col-md-8">
      <textarea name="remarks" id="remarks1" rows="3" cols="35"></textarea>
      <span class="text-danger"></span>
</div>
</div>
 <div class="modal-footer">
    <div class="col text-left">
        <button type="button" class="btn btn-secondary "  data-dismiss="modal">Close</button>
    </div>
    <div class="col text-right">
        <button type="submit" class="btn btn-primary">Delisting</button>
    </div>
      </div> 


</form>

</div>

</div>
</div>
</div>
<!-- Modal Content Ends Here -->

  <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Are You Sure Delisting Political Party </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{$saction}}" >
                {{ csrf_field() }}   
     
    <input type="hidden" name="ccode" id="ccode" value="" readonly="readonly">
    <div class="mb-3">
       
       <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="dflag1" name="status" value="Y" class="custom-control-input">
        <label class="custom-control-label" for="dflag1">Yes</label>
      </div> 
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="dflag2" name="status"  value="N" class="custom-control-input">
        <label class="custom-control-label" for="dflag2">No</label>
      </div>
        
      </div>
    <div class="mb-3">
       <p><b>Remarks:-</b> <sup>*</sup></p>
          <textarea class="form-control" name="remarks" id="remarks"></textarea>
          
           <span id="err" class="text-danger"></span>
     </div>
    
       
   
 
   
   <div class="modal-footer">
    <div class="col text-left">
        <button type="button" class="btn btn-secondary "  data-dismiss="modal">Close</button>
    </div>
    <div class="col text-right">
        <button type="submit" class="btn btn-primary">OK</button>
    </div>
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
    
  $("#party1").change(function () {
       if($("#party1").val()!=""){
      $('#election_formd #party1').next('.text-danger').text("").hide();
      }
    });
    $("#remarks1").keypress(function () {
       if($("#remarks1").val()!=""){
      $('#election_formd #remarks1').next('.text-danger').text("").hide();
       }
    });

  $("#election_formd").submit(function(){
          var is_error = false;   
    
     if($('#election_formd #party1').val()=="") {  
          $('#election_formd #party1').next('.text-danger').text("please  select party.").show();
         is_error = true;
         }
     if($('#election_formd #remarks1').val()=="") {  
        $('#election_formd #remarks1').next('.text-danger').text("please enter remarks.").show();
         is_error = true;
          
         } 
       
      if(is_error){
          return false;
        }   
    });     
           
    });
  
  $(document).on("click", ".getdata", function () {  
       deletef = $(this).attr('data-delete');
       ccode = $(this).attr('data-ccode'); 
       remarks = $(this).attr('data-remarks'); 

       $("#ccode").val(ccode);    
       $("#remarks").val(remarks);
        if(deletef=='Y'){
            $("#dflag1").attr ( "checked" ,"checked" );
          }
       
      if(deletef=='N'){
            $("#dflag2").attr ( "checked" ,"checked" );
          }    
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