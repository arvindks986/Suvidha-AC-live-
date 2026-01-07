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
            <div class="col-md-6"><h4>{{$heading_title}}</h4></div> 
         <div class="col-md-6 text-right">
              @foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
</div>      
  
 <div class="table-responsive card-body">
       
    @if(isset($lists))  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
              <th>Sl. No.</th>
              <th>Party Abbre</th>
              <th>Party Name</th> 
              <th>State Name</th>
              <th>Party Type</th>
              
          </tr>
        </thead>
        <tbody>
  
      @foreach ($lists as $key=>$list)
           <tr><td>{{$i}}</td>
           <td>{{$list['PARTYABBRE']}}</td>
           <td>{{$list['PARTYNAME']}}</td>
           <td>{{$list['ST_CODE']}}-{{$list['ST_NAME']}}</td>
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