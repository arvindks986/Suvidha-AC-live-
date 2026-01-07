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
 .list-inline li{
  display: inline;
}
td {
    height: 100px!important;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
table td{position:relative;}
p.btn.btn-table {
    position: absolute;
    bottom: 0;
    width: 100%;
    text-align: center;
    left: 0;
    background: #17a2b8;
    border-radius: 0;
}
p.btn.btn-add {   position: absolute;   bottom: 0;   width: 100%;   text-align: center;    left: 0;
    background: #c0c0c0;   border-radius: 0;   height: 100%;   padding: 18%;}
p.btn.btn-table a, p.btn.btn-add a { color: #fff; font-size:14px;}
p.btn.btn-table:hover{background:#49a8a4;}
p.btn.btn-add:hover{background:#49a8a4;}
</style>

<?php   
		$user_data = Auth::user();
		$st_code = $user_data['st_code'];
		$ac_no = $user_data['ac_no'];
?>

<div class="container-fluid">
  <div class="px-2 py-2">
    <form   method="post" action="view-exempted-pollingstation">
  @csrf
		 
		     
          <div class="form-group"> 
		    <label>Select PS:</label>
		  <div class="d-flex justify-content-between align-item-middle">	
            <select name="turnout_type"  class="form-control mr-2">
            
            <option value="">Select Turnout Type</option>
			@if($turnout_data == 0)
			<option value="0" selected="selected"> Zero Turnout</option> 
			<option value="1"> All</option>
			@else
            <option value="0"> Zero Turnout</option>  
			<option value="1" selected="selected"> All</option> 
			@endif
					 
					
				    
            </select>
		    <input type="submit" name="submit" class="btn btn-primary getdata">
		  </div>	
          </div>
	
      </form>
  </div>
</div>




<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col-md-3  pull-right  text-right">
      @if(count($results)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif    
    </div>
  </div>
</div>  
</section>


@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="pull-right" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach
      </div>
    </div>
  </div>
</section>
@endif





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
      @if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
 $class = 'alert-success';
}
else{
  $class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif

	
  


	<?php $st_code = Auth::user()->st_code;  ?>
	<div class="alert alert-success  alert-dismissible" id="successDiv1" style="display:none;">
				   <button type="button" class="close" data-dismiss="alert">×</button>
				   <strong id="successMsg1"></strong>
		</div>
			  
     <div class="page-contant">
       <div class="random-area">
        <br>
		<!-- <div class="col-sm-12">
            <button class="btn btn-success pull-right mb-3" type="button" id="verify_all" >Mark all as Exempted</button>
        </div>  -->
		
		

        <div class="table-responsive">
      
            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%"><thead>

      <tr>


       <tr>
       <!--  <th>Serial No</th> -->
          <th>PS No</th>
          <th>PS Name</th> 
		  <th>Electors Male</th> 
          <th>Electors Female</th> 
          <th>Electors Other</th> 
          <th>Electors Total</th> 
          <th>Voter Male</th> 
          <th>Voter Female</th> 
          <th>Voter Other</th> 
          <th>Voter Total</th> 
          <th>Mark Exempt</th> 
          
         
       </tr>


    </thead>
        <tbody>
          
         @forelse ($results as $key=>$listdata)
			
        


          <tr>
          
            <td>{{$listdata['ps_no']}}</td>
            <td>{{$listdata['ps_name']}}</td>
            
            <td>{{$listdata['male_electors']}}</td>
            <td>{{$listdata['female_electors']}}</td>
            <td>{{$listdata['other_electors']}}</td>
			<td>{{$listdata['male_electors']+$listdata['female_electors']+$listdata['other_electors']}}</td>
            <td>{{$listdata['male_turnout']}}</td>
            <td>{{$listdata['female_turnout']}}</td>
            <td>{{$listdata['other_turnout']}}</td>
            <td>{{$listdata['male_turnout']+$listdata['female_turnout']+$listdata['other_turnout']}}</td>
            @if($listdata['booth_exemp_status'] === 0)
			<td>
				<button type="button" class="btn btn-primary" onclick="exempt_ps('{{$listdata['ps_no']}}')">Mark Exempt</button>
			</td>
			@else
			<td><span style="color:green">Exempted</span></td>
            @endif
            
            
			
			
         
          </tr>
       
      
           @empty
                <tr>
                  <td class="text-center" colspan="14">No Data Found For Polling Station</td>                 
              </tr>
          @endforelse   

         
        
       </tbody></table>
    </div><!-- End Of intra-table Div -->   


  </div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 



<div class="modal fade" id="myModal12" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        
        <div class="modal-body">
          <p>Once Confirmed it will exempt all the polling station</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default confirm_button" data-dismiss="modal">Confirm</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>

 <!--EDIT POP UP STARTS-->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Polling Station <span id="psnameid"></span>-<span id="psnoid"></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <form class="form-horizontal" method="POST" action="{{url('roac/booth-app-revamp/update_turnout_pswise')}}" id="RoPsWiseDetailsUpdate">

         {{ csrf_field() }}
                         
         <input type="hidden" name="psnoinput" id="psnoinput" value="">
         <input type="hidden" name="psccode" id="psccode" value="">
         <input type="hidden" name="ac_no" id="ac_no" value="">

         <div class="form-group row">
          <label class="col-sm-4 form-control-label">PS Name <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="PS_NAME_EN" class="form-control" name="PS_NAME_EN" value="">
           <span class="text-danger"></span>
          </div>
        </div>

         <div class="form-group row">
          <label class="col-sm-4 form-control-label">Electors Male <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="electors_male"  maxsize="6" minsize="1" class="form-control" name="electors_male" value="">
           <span class="text-danger"></span>
          </div>
        </div>

          <div class="form-group row">
          <label class="col-sm-4 form-control-label">Electors Female <sup>*</sup></label>
          <div class="col-sm-8">
          <input type="text" id="electors_female"  maxsize="6" minsize="1" class="form-control" name="electors_female" value="">
          <span class="text-danger"></span>
          </div>
        </div>
        

    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Electors Other <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="electors_other"  maxsize="6" minsize="1" class="form-control" name="electors_other" value="">
           <span class="text-danger"></span>
          </div>
        </div>  
        


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Voter Male <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="voter_male" maxsize="6" minsize="1" class="form-control" name="voter_male" value="">
           <span class="text-danger"></span>
          </div>
    </div>


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Voter Female <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="voter_female" maxsize="6" minsize="1" class="form-control" name="voter_female" value="">
           <span class="text-danger"></span>
          </div>
    </div>


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Voter Other <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="voter_other" maxsize="6" minsize="1" class="form-control" name="voter_other" value="">
           <span class="text-danger"></span>
          </div>
    </div>


    

        <input type="submit" name="Update">
              
    </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
  <!--EDIT POP UP ENDS-->
@endsection
@section("script")

<script type="text/javascript">

$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });
		
$(document).ready(function () {
	 
	
	$('#add_new_so_submit').click(function(){
		
      $.ajax({
        url: "{!! $action !!}",
        type: 'POST',
        data: $('#add_new_so_modal form').serialize(),
		
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#add_new_so_modal .text-danger').remove();
          $('#add_new_so_modal input, #add_new_so_modal textarea').removeClass('input-error');
          $('#add_new_so_submit').prop('disabled',true);
          $('#add_new_so_submit').text("Validating...");
          $('#add_new_so_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {

          if(json['success'] == true){
            location.reload();
          }

          if(json['success'] == false){
            if(json['errors']['warning']){
              alert(json['errors']['warning']);
            }
            if(json['errors']['ps_no']){
              $("#add_new_so_modal #ps_no_div").addClass("input-error");
              $("#add_new_so_modal #ps_no_div").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['ps_no'][0]+"</span>");
            }
            if(json['errors']['reason']){
              $("#add_new_so_modal #reason").addClass("input-error");
              $("#add_new_so_modal #reason_div").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['reason'][0]+"</span>");
            }
          }

          $('#add_new_so_submit').prop('disabled',false);
          $('#add_new_so_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#add_new_so_submit').prop('disabled',false);
          $('#add_new_so_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
    });
	
	
	$('#verify_all').click(function(e){
		var st_code = '{{$st_code}}';
      $('#myModal12').modal('show');
	  
	  $('.confirm_button').click(function(e){
		  
		  $.ajax({
                type:'POST',
				data:{st_code:st_code},
                url:"post-exempted-boothapp-pollingstation",
				
                success:function(data){
					console.log(data.success);
					$('#successDiv1').show();
                    $('#successMsg1').html(data.success);
					setTimeout(function(){ $("#successDiv1").hide();
									location.reload();
								}, 3000);
				
                }
            });
		  
	  });
	  
    });
});

  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 50,
        "aaSorting": []
      });
    }
	

  });
  
  function turnout_manual(st_code,ps_no,ac_no){
	  
	  $('#st_code').val(st_code);
	  $('#ac_no').val(ac_no);
	  $('#ps_no').val(ps_no);
	  
	  $('#add_new_so_modal').modal('show');
  }
  
  //*******************POLLIN STATION FORM VALIDATION STARTS********************//
$("#RoPsWiseDetailsUpdate").validate({
    rules: {
              PS_NAME_EN: { required: true,minlength:2, maxlength: 350,},
              electors_male: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_female: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_other: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_total: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_male: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_female: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_other: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_total: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
            },
  messages: { 
                PS_NAME_EN: {
                      required: "Polling station name required.",
                      minlength: "Minlength length of Polling station should be 2 characters.",
                      maxlength: "Maximum length of Polling station should be 100 characters.",
                  },
                  electors_male: {
                      required: "Electors Male Numbers required.",
                      number: "Electors Male should be numbers only.",
                      noSpace: "Enter Electors Male without space.",
                      minlength: "Minlength length of Electors Male should be 1 characters.",
                      maxlength: "Maximum length of Electors Male should be 7 characters.",
                  },
                  electors_female: {
                      required: "Electors Female Numbers required.",
                      number: "Electors Female should be numbers only.",
                      noSpace: "Enter Electors Female without space.",
                      minlength: "Minlength length of Electors Female should be 1 characters.",
                      maxlength: "Maximum length of Electors Female should be 7 characters.",
                  },
                  electors_other: {
                      required: "Electors Other Numbers required.",
                      number: "Electors Other should be numbers only.",
                      noSpace: "Enter Electors Other without space.",
                      minlength: "Minlength length of Electors Other should be 1 characters.",
                      maxlength: "Maximum length of Electors Other should be 7 characters.",
                  },
                  electors_total: {
                      required: "Electors Total Numbers required.",
                      number: "Electors Total should be numbers only.",
                      noSpace: "Enter Electors Total without space.",
                      minlength: "Minlength length of Electors Total should be 1 characters.",
                      maxlength: "Maximum length of Electors Total should be 7 characters.",
                  },
                  voter_male: {
                      required: "Voter Male Numbers required.",
                      number: "Voter Male should be numbers only.",
                      noSpace: "Voter Enter Male without space.",
                      minlength: "Minlength length of Voter Male should be 1 characters.",
                      maxlength: "Maximum length of Voter Male should be 7 characters.",
                  },
                  voter_female: {
                      required: "Voter Female Numbers required.",
                      number: "Voter Female should be numbers only.",
                      noSpace: "Enter Female without space.",
                      minlength: "Minlength length of Voter Female should be 1 characters.",
                      maxlength: "Maximum length of Voter Female should be 7 characters.",
                  },
                  voter_other: {
                      required: "Voter Other Numbers required.",
                      number: "Voter Other should be numbers only.",
                      noSpace: "Enter Other without space.",
                      minlength: "Minlength length of Voter Other should be 1 characters.",
                      maxlength: "Maximum length of Voter Other should be 7 characters.",
                  },
                  voter_total: {
                      required: "Voter Total Numbers required.",
                      number: "Voter Total should be numbers only.",
                      noSpace: "Enter Voter Total without space.",
                      minlength: "Minlength length of Voter Total should be 1 characters.",
                      maxlength: "Maximum length of Voter Total should be 7 characters.",
                  },
            },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = $(element).data('error');
              if (placement) {
                  $(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//********************POLLIN STATION FORM VALIDATION ENDS********************//

$(document).on("click", ".PsWiseDetailspopup", function () {


       psname = $(this).attr('data-psname');
       emale = $(this).attr('data-emale');
       efemale = $(this).attr('data-efemale');
       eother = $(this).attr('data-eother');
       etotal = $(this).attr('data-etotal');
       vmale = $(this).attr('data-vmale');
       vfemale = $(this).attr('data-vfemale');
       vother = $(this).attr('data-vother');
       vtotal = $(this).attr('data-vtotal');
       psname = $(this).attr('data-psname');
       psno = $(this).attr('data-psno');
       ccode = $(this).attr('data-ccode');

       $('#PS_NAME_EN').val(psname);
       $('#electors_male').val(emale);
       $('#electors_female').val(efemale);
       $('#electors_other').val(eother);
       $('#electors_total').val(etotal);
       $('#voter_male').val(vmale);
       $('#voter_female').val(vfemale);
       $('#voter_other').val(vother);
       $('#voter_total').val(vtotal);
       $('#psnameid').text(psname);
       $('#psnoid').text(psno);
       $('#psnoinput').val(psno);
       $('#psccode').val(ccode);

       
      

   });

   function exempt_ps(ps_no){
		var st_code = '{{$st_code}}';
        var ac_no = '{{$ac_no}}';
        var ps_no = ps_no;
		
		$.ajax({
                type:'POST',
				data:{
				st_code:st_code,
				ac_no:ac_no,
				ps_no:ps_no
				},
                url:"exempt-ps-wise",
				
                success:function(data){
					console.log(data.success);
					$('#successDiv1').show();
                    $('#successMsg1').html(data.success);
					setTimeout(function(){ $("#successDiv1").hide();
									location.reload();
								}, 3000);
				
                }
            });
	}

</script>


@endsection