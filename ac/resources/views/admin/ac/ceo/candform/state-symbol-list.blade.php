@extends('admin.layouts.ac.theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome', 'List of Symbol Nomination')
@section('content') 
    
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List of Symbol</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>State Name:</b> 
                              <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; 
                              <b>State vernacular Language:</b> 
                              <span class="badge badge-info">{{$state_language}}</span>
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
              <th>Sl. No.</th><!-- <th>Symbol No</th> -->
              <th>Symbole  Name in English</th> 
              <th>Symbole  Name in Hindi</th>
              <th>Symbole  Name in vernacular</th>
              <th>Action</th></tr>
        </thead>
        <tbody>
        <?php $i=1; $url = URL::to("/");   //dd($record);?>
      
      @foreach ($record as $key=>$list)
         
          <tr><td>{{$i}}</td>
          <!--  <td>{{$list->symbol_no}}</td> -->
            <td>{{$list->symbol_name}}   </td>
            <td>{{$list->symbol_hname}}   </td>
            <td>{{$list->symbol_vname}}</td>
            
           <td>  <button type="button" id="{{$list->id}}" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus"   data-id="{{$list->id}}" data-st_code="{{$list->st_code}}" data-sname="{{$list->symbol_name}}"
            data-shname="{{$list->symbol_hname}}" data-svname="{{$list->symbol_vname}}">Edit</button> </td>
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
        <h4 class="modal-title" id="exampleModalLabel">Change Symbol Name vernacular</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('acceo/symbol-list-vernacular') }}" >
                {{ csrf_field() }}   
            <input type="hidden" name="id" id="id" value="" readonly="readonly">
             <input type="hidden" name="st_code" id="st_code" value="" readonly="readonly">
     <div class="mb-3">
       <p>Symbol Name in English:  </p>
            <span name="symbol_name" id="symbol_name"> </span>
       
     </div>

      <div class="mb-3">
       <p>Symbol Name in Hindi:  </p>
            <span name="symbol_hname" id="symbol_hname"> </span>
       
     </div>
    <div class="mb-3">
       <p>Symbol Name in vernacular:  </p>

          <input class="form-control" type="text" name="symbol_vname" id="symbol_vname" value="">
           <span id="err" class="text-danger"></span>
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
             if($("#symbol_vname").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Please enter name");
                    $("#symbol_vname").focus();
                    return false;
                    }
               });
        });
  
  $(document).on("click", ".getdata", function () {  
      
       st_code = $(this).attr('data-st_code');
       rid = $(this).attr('data-id'); 
       sname = $(this).attr('data-sname');
       shname = $(this).attr('data-shname');
       svname = $(this).attr('data-svname');

       $("#id").val(rid);
       $("#st_code").val(st_code);
       $("#symbol_name").html(sname);
       $("#symbol_hname").html(shname);
      
       $("#symbol_vname").val(svname);  
   });
    
    
</script>
 
@endsection