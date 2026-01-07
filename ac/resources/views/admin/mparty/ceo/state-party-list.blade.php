@extends('admin.central.common.theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => url('/mparty/ceo/state-party-list'),
    'name' => 'List Of All Political Parties'
  ]; 
  ?>
@endsection
 
@section('content') 
 <?php $i=1; $url = URL::to("/");   ?>
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
    <div class="row">
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
           <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         
     </div>
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4>List Of All Parties</h4></div> 
            <div class="col-md-8">
               <p class="mb-0 text-right"><b>State Name:</b> 
                <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; 
                  <b>State vernacular Language:</b> 
                  <span class="badge badge-info">{{$state_language}}</span>
               </p>
              </div>
            </div>
      </div>
  <div class=" card-header">
      <div class=" row">
      <div class="col">&nbsp; </div>
  <div class="col text-right"><label>Select Party Type</label> </div>
  <div class="col-md-2 text-right">
        <form name="frmparty" id="frmparty" method="POST"  action="" >
           {{ csrf_field() }}  
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
    @if(isset($record))  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
            <tr>
              <th>Sl. No.</th><!-- <th>Party Id</th> -->
              <th>Party Abbree</th>
              <!-- <th>Party HAbbree</th> -->
              <th>Party Name</th> 
              <th>Party Hindi Name</th>
              <th>Party vernacular</th>
              <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php $i=1; $url = URL::to("/");   //dd($record);?>
      
      @foreach ($record as $key=>$list)
         
          <tr><td>{{$i}}</td>
          <!--  <td>{{$list->party_id}}</td> -->
            <td>{{$list->party_abbre}}   </td>
            <!-- <td>{{$list->party_habbre}}   </td> -->
            <td>{{$list->party_name}}</td>
            <td>{{$list->party_hname}}</td>
            <td>{{$list->party_vname}}</td>
             
           
           <td>  <button type="button" id="{{$list->id}}" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus"   data-id="{{$list->id}}" data-st_code="{{$list->st_code}}" data-pname="{{$list->party_name}}"
            data-phname="{{$list->party_hname}}" data-pvname="{{$list->party_vname}}">Edit</button> </td>
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
        <h4 class="modal-title" id="exampleModalLabel">Change Party Name vernacular</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{$action}}" >
                {{ csrf_field() }}   
            <input type="hidden" name="id" id="id" value="" readonly="readonly">
             <input type="hidden" name="st_code" id="st_code" value="" readonly="readonly">
     <div class="mb-3">
       <span><b>Party Name in English:- </b></span>
            <span name="party_name" id="party_name"> </span>
       
     </div>
     <div class="mb-3">
       <span><b>Party Name in Hindi:- </b></span>
            <span name="party_hname" id="party_hname"> </span>
       
     </div>
    <div class="mb-3">
       <p><b>Party Name in vernacular:-</b></p>

          <input class="form-control" type="text" name="party_vname" id="party_vname" value="">
           <span id="err" class="text-danger"></span>
     </div>

      
   
  <div class="modal-footer">
    <div class="col text-left">
        <button type="button" class="btn btn-secondary "  data-dismiss="modal">Close</button>
    </div>
    <div class="col text-right">
        <button type="submit" class="btn btn-primary">Save</button>
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
             if($("#party_vname").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Please enter name");
                    $("#party_vname").focus();
                    return false;
                    }
               });
        });
  
  $(document).on("click", ".getdata", function () {  
      
       st_code = $(this).attr('data-st_code');
       rid = $(this).attr('data-id'); 
       pname = $(this).attr('data-pname');
       phname = $(this).attr('data-phname');
       pvname = $(this).attr('data-pvname');

       $("#id").val(rid);
       $("#st_code").val(st_code);
       $("#party_name").html(pname);
       $("#party_hname").html(phname);
      
       $("#party_vname").val(pvname);  
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
