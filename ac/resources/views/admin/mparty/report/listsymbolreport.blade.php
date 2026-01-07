@extends('admin.layouts.ac.theme')
@section('title', 'ENCORE')
@section('bradcome', $bradcome)
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
            
             <div class="col-md-6">
               @foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
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
                           
          </tr>
        </thead>
        <tbody>
       
      @foreach ($lists as $key=>$list)
           <tr><td>{{$i}}</td>
           <td>{{$list['SYMBOL_DES']}}</td>
           <td>{{$list['SYMBOL_HDES']}}</td>
            
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