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
            <div class="col-md-8">
              <span><b>Total:-</b></span><b>{{$total}}</b>
              <button type="submit" id="" onclick="window.location.href='{{$url}}/mparty/new-party';"class="btn btn-primary custombtn" align="text-right">Add New Party</button>
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
              <th>Symbol</th>
              <th>Action</th>
          </tr>
        </thead>
        <tbody>
        	 
      @foreach ($lists as $key=>$list)  
           <tr><td>{{$i}}</td>
           <td width="100px">{{$list['PARTYABBRE']}}</td>
           <td>{{$list['PARTYNAME']}}</td>
           <td width="100px">{{$list['PARTYHABBR']}}</td>
           <td>{{$list['PARTYHNAME']}}</td>
           <td>@if($list['PARTYTYPE']=="N") National @endif 
			           @if($list['PARTYTYPE']=="S") State  @endif 
			          @if($list['PARTYTYPE']=="U") Unrecognized @endif
			     </td>
           <td>@if(isset($list['Symbol_Img']) and ($list['Symbol_Img']))
                <img src="data:{{$list['CONTENT_TYPE']}};base64, {{$list['Symbol_Img']}}" alt="{{$list['SYMBOL_DES']}}" class="size-50"  style="width:75px; height:75px;"  />
               @elseif($list['SYMBOL_DES']!='')
                    {{$list['SYMBOL_DES']}}
              @else
                   NO Symbol
                @endif </td>
             

          <td> <a href=" {{$url.'/mparty/edit-party?id='.encrypt_string($list['CCODE'])}}" class="btn btn-primary">Edit</a>

           <button type="button" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus" data-ccode="{{$list['CCODE']}}" 
           data-abbre="{{$list['PARTYABBRE']}}" data-habbre="{{$list['PARTYHABBR']}}" 
           data-name="{{$list['PARTYNAME']}}" data-hname="{{$list['PARTYHNAME']}}" 
           data-date="{{$list['updated_at']}}" data-updatedby="{{$list['updated_by']}}"  
           data-remarks="{{$list['remarks']}}">View Details</button>

           <a href=" {{$url.'/mparty/view-details?id='.encrypt_string($list['CCODE'])}}">Action Logs</a> </td>
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
        <h4 class="modal-title" id="exampleModalLabel">Political Party Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{$saction}}" >
                {{ csrf_field() }}   
     
    <input type="hidden" name="ccode" id="ccode" value="" readonly="readonly">
      <div class="mb-3">
        <table><tr><th width="200px">Party Abbre:- </th> <th><input type="text" name="abbre" id="abbre" value="" readonly="readonly">
        </th></tr></table>
      </div>
      <div class="mb-3">
      <table><tr><th width="200px">Party Abbre in Hindi:- </th> <th> <input type="text" name="habbre" id="habbre" value="" readonly="readonly">
        </th></tr></table>
      </div>
      <div class="mb-3">
       <table><tr><th width="200px">Party Name:-</th> <th> <input type="text" name="name" id="name" value="" readonly="readonly">
        </th></tr></table>
      </div>
      <div class="mb-3">
      <table><tr><th width="200px">Party name in Hindi:- </th> <th><input type="text" name="hname" id="hname" value="" readonly="readonly">
        </th></tr></table>
      </div>
      <div class="mb-3">
       <table><tr><th width="200px">Remarks:- </th> <th>
        <textarea name="remarks" id="remarks" readonly="readonly" rows="5" cols="30"></textarea> 
         
        </th></tr></table>
      </div>
      <div class="mb-3">
       <table><tr><th width="200px">Last Updated Date:- </th> <th> <input type="text" name="date" id="date" value="" readonly="readonly">
        </th></tr></table>
      </div>
      <div class="mb-3">
      <table><tr><th width="200px">Updated By:- </th> <th>  <input type="text" name="updatedby" id="updatedby" value="" readonly="readonly">
        </th></tr></table>
      </div>
       
   
 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         
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

       abbre = $(this).attr('data-abbre');
       ccode = $(this).attr('data-ccode'); 
       habbre = $(this).attr('data-habbre');
       name = $(this).attr('data-name');
       hname = $(this).attr('data-hname');
       date = $(this).attr('data-date');
       updatedby = $(this).attr('data-updatedby');
       remarks = $(this).attr('data-remarks'); 

       $("#ccode").val(ccode);
       $("#abbre").val(abbre);
       $("#habbre").val(habbre);
       $("#name").val(name);
       $("#hname").val(hname);
       $("#date").val(date);
       $("#updatedby").val(updatedby);
       $("#remarks").val(remarks); 
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