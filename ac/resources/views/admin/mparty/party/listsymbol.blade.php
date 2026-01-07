@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', 'List of All Symbol')
@section('content') 
<?php $i=1; $url = URL::to("/");   ?>
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col-md-2"><h4>{{$heading_title}}</h4></div> 
            <div class="col-md-4">Total Records: {{$total}} 
              <button type="submit" id="" onclick="window.location.href='{{$url}}/mparty/add-symbol';"class="btn btn-primary custombtn" align="text-right">Add New Symbol</button></div> 
            
             <div class="col-md-3">
    <form class="form-inline pull-right">
      <div class="form-group float-right"> 
        <label for="noofcards" class="mr-3">Symbol Type</label> 
        <form name="frmparty" id="frmparty" method="POST"  action="" >
        <select name="freesymbol" id="freesymbol" onchange="this.form.submit();">
                  @if(isset($symboltype)) { 
                    @foreach($symboltype as $iterate)
                     @if($freesymbol==$iterate['id'])
                     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
                @endforeach
                @endif
                </select>
              </form>
        </div>        
         
        </form>
    </div> 
    <div class="col-md-3">
    <form class="form-inline pull-right">
      <div class="form-group float-right"> 
        <label for="noofcards" class="mr-3">Symbol</label> 
        <form name="frmparty" id="frmparty" method="POST"  action="" >
        <select name="symbol_img" id="symbol_img" onchange="this.form.submit();">
                  @if(isset($symbol)) { 
                    @foreach($symbol as $iterate)
                     @if($symbol_img==$iterate['id'])
                     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
                @endforeach
                @endif
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
              <th>Symbol Name</th>
              <th>Symbol Name In Hindi</th> 
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
           <td width="100px">@if(isset($list['Symbol_Img']) and ($list['Symbol_Img']))
                    <img src="data:{{$list['CONTENT_TYPE']}};base64, {{$list['Symbol_Img']}}" alt="" class="size-50"  style="width:75px; height:75px;"  />
                @endif </td>
           <td>{{$list['remarks']}}</td>
           <td>@if($list['updated_at']!=''){{date("d-m-Y",strtotime($list['updated_at']))}} @endif</td> 
           <td> <a href=" {{$url.'/mparty/edit-symbol?id='.encrypt_string($list['SYMBOL_NO'])}}" class="btn btn-primary">Edit</a> <br>
           <a href=" {{$url.'/mparty/symbollog-details?id='.encrypt_string($list['SYMBOL_NO'])}}">Action Logs</a>
           </td>
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