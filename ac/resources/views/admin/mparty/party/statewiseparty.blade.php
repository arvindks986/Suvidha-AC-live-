@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome',$bradcome)
@section('content') 
<?php $i=1; $url = URL::to("/");   ?>
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col-md-7"><h4>{{$heading_title}}</h4> </div>
            <div class="col-md-2  float-right"><b>  Total Records:-{{$total}}</b></div> 
          <div class="col-md-2  float-right"> 
            <a href=" {{$action}}">
            <button type="button" id=""  class="btn btn-primary">Add  State Party Recognized</button>
          </a>
        </div>      
  
 <div class="table-responsive card-body">
      <div class="row">
        @if(count($errors->all())>0)
         <div class="alert alert-danger">
           <ul>
            @foreach($errors->all() as $iterate_error)
            <li><p class="text-left">{!! $iterate_error !!}</p></li>
            @endforeach
          </ul>
        </div>
        @endif
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
              <th>State Name</th>
              <th>Remarks</th>
              <th>Date</th>
              <!-- <th>Action</th> -->
          </tr>
        </thead>
        <tbody>
  
      @foreach ($lists as $key=>$list) 
      <?php $st=App\models\Admin\mparty\DPartyModel::getallstate_bypartyabbre($list['PARTYABBRE']);
            //dd($st);
      ?>
           <tr><td>{{$i}}</td>
           <td>{{$list['PARTYABBRE']}}</td>
           <td>{{$list['PARTYNAME']}}</td>
           <td>@if(isset($st))
               @foreach($st as $s) 
                {{$s['ST_CODE']}}-{{$s['ST_NAME']}}<br>
            @endforeach
            @endif
          </td>
           <td>@if(isset($st))
               @foreach($st as $s) 
                {{$s['remarks']}} <br>
            @endforeach
            @endif</td>
           <td>@if(isset($st))
               @foreach($st as $s) 
                @if($s['created_at']!='') {{date("d-m-Y H:i:s",strtotime($s['created_at']))}} @endif<br>
            @endforeach
            @endif</td>
           
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
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Are You Sure delete this Political Party Recognized State </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{$saction}}" >
                {{ csrf_field() }}   
     
    <input type="hidden" name="id" id="id" value="" readonly="readonly">
    <input type="hidden" name="partyabbre" id="partyabbre" value="" readonly="readonly">
    <div class="col">
       <span> State Name:-</span><span id="stname"></span>
    </div>
    <div class="col">
       <span> Party Name:-</span><span id="ptname"> </span>
    </div>
    <br>
    <div class="mb-3">
       
       <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="dflag1" name="status" value="1" class="custom-control-input">
        <label class="custom-control-label" for="dflag1">Yes</label>
      </div> 
      <!-- <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="dflag2" name="status"  value="0" class="custom-control-input">
        <label class="custom-control-label" for="dflag2">No</label>
      </div> -->
        
      </div>
    <div class="mb-3">
       
       <div class="custom-control ">
        <label>Remarks <sup>* </sup></label>
        <textarea name="remarks" id="remarks" class="form-control"></textarea> 
        @if ($errors->has('remarks'))
                    <span style="color:red;">{{ $errors->first('remarks') }}</span>
            @endif
          <span id="err2" class="text-danger"></span>
      </div>
       
   
 
   
 <div class="modal-footer">
    <div class="col text-left">
        <button type="button" class="btn btn-secondary "  data-dismiss="modal">Close</button>
    </div>
    <div class="col text-right">
        <button type="submit" class="btn btn-primary">Delete</button>
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
      $("#election_form").submit(function(){
    var is_error = false;   
     
    if($('#election_form #remarks').val()=="") {  
        $('#election_form #remarks').next('.text-danger').text("please enter remarks.").show();
         is_error = true;
          
         } 
      if(is_error){
          return false;
        }   
    });           
           
  });
  
  $(document).on("click", ".getdata", function () {  
       
       id = $(this).attr('data-id');
       partyabbre = $(this).attr('data-partyabbre');  
       deleted = $(this).attr('data-deleted'); 
       stname = $(this).attr('data-statename'); 
       ptname = $(this).attr('data-partyname');  
       $("#id").val(id);
       $("#partyabbre").val(partyabbre);
       $("#stname").html(stname);
       $("#ptname").html(ptname);
       $("#dflag1").attr ( "checked" ,"checked" );
      //  if(deleted=='1'){
      //       $("#dflag1").attr ( "checked" ,"checked" );
      //     }
       
      // if(deleted=='0'){
      //       $("#dflag2").attr ( "checked" ,"checked" );
      //     }    
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