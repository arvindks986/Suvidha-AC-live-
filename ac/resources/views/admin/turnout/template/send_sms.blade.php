@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }


  </style>

  <div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">


      
    </div> 

  </div>
</div>  
</section>

  <section>
        <div class="container">
           <br />

            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
			
			@if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            
        </div>
    </section>
 
 

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>
  
     <div class="content">
      <form class="form-controal" method="post" action="{!! $action !!}" onsubmit="return confirm('Are you sure you want to send message?');">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
			<div class="form-group row">
			
				<div class="col-sm-2">
					<input type="radio" name="environment" id="environment" class="" value="1" style="width: 30px; height: 30px;" checked /> <span style="margin: 10px;" >Test</span>
					<input type="radio" name="environment" value="2" style="width: 30px; height: 30px;" /> <span style="margin: 10px;">Live</span>
				</div>
			
			
                 <div class="col-sm-2" id="mobile_div">
					<input type="text" name="mobile" id="mobile" placeholder="Enter Mobile No." class="form-control" required="required"  />
				</div>				
			
                 <div class="col-sm-4">
					<textarea name="message" rows=4 cols=50  placeholder="Message" class="form-control" id="message" required></textarea>
				</div>	
				<div class="col-sm-4">
					<button type="submit" class="btn btn-large btn-primary">Submit</button>
				</div>				
			</div>       
      </form>
    </div>
  
  
  
  

   <div class="table-responsive">
      
     <table id="data_table_table" class="table table-striped table-bordered" style="width:100%"><thead>
       <tr>
        <th> Sn. </th>
        <th> Name </th>
        <th colspan="1">Designation</th>
         <th colspan="1">Email</th>
         <th colspan="1">Mobile</th>
         <th colspan="1">Status</th>
       </tr>


    </thead>
        <tbody>
      @foreach($results as $key => $result)
        <tr>
        <td>{{$key+1}}</td> 
        <td>{{$result->name }}</td>
        <td>{{ $result->designation }}</td>
        <td>{{$result->email }}</td>
        <td>{{$result->mobile }}</td>
        <td>@if($result->status =='1') Active @else InActive @endif</td>
         </tr>
        @endforeach
  
       </tbody></table>

         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 


<!-- Spouse/Self Delete Modal Start-->
<div class="modal fade" id="PopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog " role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Conformation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
               <h5>Are you sure you want to send message?</h5>
               <input type="hidden" name="modal_delete_spouse_id" id="modal_delete_spouse_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary" onclick="javascript:delete_spouse_entry()">Yes</button>
         </div>
      </div>
   </div>
</div>
<!-- Spouse/Self Modal End-->

@endsection
@section('script')
<script type="text/javascript">

$(document).ready(function () {

	 $('input[name=environment]').change(function(){
		 var radioValue = $( 'input[name=environment]:checked' ).val();
		 
		   if(radioValue == 1){
			    $('#mobile_div').show();
				$("#mobile").attr("required", "required");
		   } else if(radioValue == 2){
				$("#mobile").removeAttr("required");
				$('#mobile_div').hide();
		   }
	});

   });
	


   function send_sms()
   {
       //$("#PopModal").modal('show');
   }
</script>
@endsection