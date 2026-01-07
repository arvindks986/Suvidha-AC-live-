@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'List of All Parties')
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
             
            </div>
      </div>
  
 <div class="card-body">
      <div class="row">
	    @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
           <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         
	</div>
    @if(isset($lists)) 
    <form name="frmsymbol" id="frmsymbol" method="GET"  action="" >
    <table class="table table-striped table-bordered table-hover" style="width:100%">
        <tbody> 
        <tr> 
          <td>Total Records: {{$total}}</td>
          <td>Symbol Type</td>
           <td> <select name="freesymbol" id="freesymbol" onchange="this.form.submit();">
                 @if(isset($symboltype)) { 
                  @foreach($symboltype as $iterate)
                     @if($freesymbol==$iterate['id'])
                     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
                @endforeach
                @endif
                </select> </td>

          <td>Symbol Images</td>
          <td> <select name="symbol_img" id="symbol_img" onchange="this.form.submit();">
                  @if(isset($symbol)) { 
                    @foreach($symbol as $iterate)
                     @if($symbol_img==$iterate['id'])
                     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
                @endforeach
                @endif
                </select></td>
      </tr>
    </tbody>
    </table>
  </form>
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
              <th>Sl. No.</th>
              <th>Symbol Name</th>
              <th>Symbol Name In Hindi</th> 
              <th>Symbol Type</th>
              <th>Symbol</th>
              <th>Remarks</th>
              <th>Date</th>
             <th>Action</th>
          </tr>
        </thead>
        <tbody>
       
      @foreach ($lists as $key=>$list)
           <tr><td>{{$i}}</td>
           <td>{{$list['SYMBOL_DES']}}</td>
           <td>{{$list['SYMBOL_HDES']}}</td>
           <td width="100px">{{$list['Ind_Symbol']}}</td>
           <td width="100px">@if(isset($list['Symbol_Img']))
                    <img src="data:{{$list['CONTENT_TYPE']}};base64, {{$list['Symbol_Img']}}" alt="Red dot" class="size-50"  />
                @endif </td>
           <td>{{$list['remarks']}}</td>
           <td>{{$list['updated_at']}}</td> 
           <td> <a href=" {{$url.'/mparty/edit-symbol?id='.encrypt_string($list['SYMBOL_NO'])}}" class="btn btn-primary">Edit</a> </td>
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
        <h4 class="modal-title" id="exampleModalLabel">Delete Political Party Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{$eaction}}" >
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
       <label>I have Sure delete this Political Party </label> 
      </div>
    
       
   
 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">change Status</button>
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
       
       deletef = $(this).attr('data-delete');
       ccode = $(this).attr('data-ccode'); 
        
       $("#ccode").val(ccode);
       //$("#deletef").val(deletef);
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