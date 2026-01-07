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
              @foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
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