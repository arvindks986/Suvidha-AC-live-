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
            <div class="col-md-4"><h4>{{$heading_title}}</h4></div> 
            <div class="col-md-2">Total Records: {{$total}}</div> 
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
              <!-- <th>Symbol Type</th> -->
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
           <!-- <td width="100px">{{$list['Ind_Symbol']}}</td> -->
           <td width="100px">@if(isset($list['Symbol_Img']) and ($list['Symbol_Img']))
                    <img src="data:{{$list['CONTENT_TYPE']}};base64, {{$list['Symbol_Img']}}" alt="Red dot" class="size-50" style="width:75px; height:75px;"  />
                @endif </td>
           <td>{{$list['remarks']}}</td>
           <td>@if($list['updated_at']!=''){{date("d-m-Y",strtotime($list['updated_at']))}} @endif</td> 
           <td>  <button type="button" id="" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus" data-symbol="{{$list['Ind_Symbol']}}" data-no="{{$list['SYMBOL_NO']}}"  > @if($list['Ind_Symbol']=='T') Mark Free @else  Not Mark Free @endif</button> </td>
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
        <h4 class="modal-title" id="exampleModalLabel">Mark Free Symbol</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{$action}}" >
                {{ csrf_field() }}   
     
    <input type="hidden" name="sysno" id="sysno" value="" readonly="readonly">
    <div class="mb-3">
       
       <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="msys1" name="status" value="T" class="custom-control-input">
        <label class="custom-control-label" for="msys1">Mark Free</label>
      </div> 
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="msys2" name="status"  value="F" class="custom-control-input">
        <label class="custom-control-label" for="msys2">Mark Not Free</label>
      </div>
       <label>I have Mark this Symbol as Free </label> 
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
       ind_symbol = $(this).attr('data-symbol');
       sysno = $(this).attr('data-no'); 
        
       $("#sysno").val(sysno);
       if(ind_symbol=='T'){
            $("#msys1").attr ( "checked" ,"checked" );
          }
       
      if(ind_symbol=='F'){
            $("#msys2").attr ( "checked" ,"checked" );
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