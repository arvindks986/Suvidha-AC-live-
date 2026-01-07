@extends('admin.central.common.theme')
@section('title', 'Descriptive Election Period Report')

  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => '#',
    'name' => 'Account Information'
  ]; 
  ?>
  
  
@section('content')
@php
	$prefix = '';
	$result = array();
	if(Auth::user()->role_id == '7'){
		$prefix 	= 'eci';
	}
@endphp


<style>	

.bolds{
	font-weight: bold;
	.SumoSelect {
    width: 450px !important;
}
}
</style>

@if (\Session::has('success'))
    <div class="alert alert-success" id="successMessage">
        <ul>
            <li>{!! \Session::get('success') !!}</li>
        </ul>
    </div>
@endif

@if (\Session::has('error'))
    <div class="alert alert-danger" id="errorMessage">
        <ul>
            <li>{!! \Session::get('error') !!}</li>
        </ul>
    </div>
@endif

<section class="">
<div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4>District Wise Account List</div>
            <div class="col">
			
			<p class="mb-0 text-right">
              <button class="btn btn-success" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle"></i> Add Account Info</button> 
            </p>
           
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <!-- Content goes Here -->

		<table class="table table-bordered table-striped" style="width: 100%;" id="example">
			<thead>
				<tr>
					<th>Sr.No.</th>
					<th>State name</th>
					<th>Dist No</th>
					<th>Account For</th>
					
					<th>Account Email</th>
          			<th>Account Number</th>
					<th>Account Type</th>
					<th>Account IFSC</th>
					<th>Account Benificiary</th>
					<th>Action</th>
					
				</tr>
			</thead>
			  <tbody>													
        @if(count($account_data_merge) > 0)
        @php $i = 1; @endphp
        @foreach($account_data_merge as $key => $value)
		<?php $stname = getstatebystatecode($value['st_code']); 
			  $dist = getdistrictbydistrictno($value['st_code'],$value['dist_no']);
			  $distname = $dist->DIST_NAME;

			  if(isset($value['amount_for_duplicate_epic'])){

			  }else{
				$value['amount_for_duplicate_epic'] = 0;
			  }
              
              ?>
                <tr>
                  <td>{{$i}}</td>
				  
                  <td>{{$stname->ST_NAME}}</td>
				   @if($value['dist_no'] == 0)
                  <td>NA</td>
                  @else
                  <td>{{$value['dist_no']}}</td>
                  @endif
				  
                  @if($value['account_payment_for'] == 1)
                  <td>Online Nomination</td>
                  @elseif($value['account_payment_for'] == 2)
                  <td>Duplicate Epic</td>
                  @endif
                  
                  <td>{{$value['account_email']}}</td>
                  <td>{{$value['account_number']}}</td>
                  @if($value['account_type'] == 1)
                  <td>Current</td>
                  @elseif($value['account_type'] == 2)
                  <td>Saving</td>
                  @endif
                  <td>{{$value['account_ifsc']}}</td>
                  <td>{{$value['account_benificeary']}}</td>

				  

				@if($value['is_finalised'] == 0)	
				<td>
				  	<button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-account-for = "{{$value['account_payment_for']}}" data-dist-no="{{$value['dist_no']}}" 
					data-dist-name="{{$distname}}" data-account-name="{{$value['account_name']}}" 
					data-account-mobile="{{$value['account_mobile']}}" data-account-email="{{$value['account_email']}}" data-account-number="{{$value['account_number']}}" data-account-type="{{$value['account_type']}}" data-ifsc-code="{{$value['account_ifsc']}}" data-beni-name="{{$value['account_benificeary']}}" data-charges-for="{{$value['amount_for_duplicate_epic']}}">Edit</button>
				</td>
				@else
				<td style="color:red">
				  	Finilised
				</td>

				@endif

                </tr>
          @php $i++; @endphp
          @endforeach
					@else 
						<tr><td colspan="9" align="center">No record found</td></tr> 
					@endif            
					
			  </tbody>
			</table>			
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<!--<form id="add_menu_form" action="{{ url('ecimenu/menus_submit') }}" method="post"> -->
		
		<form>
		  @csrf
			  <div class="alert alert-danger  alert-dismissible" id="errorDiv" style="display:none;">
				  <button type="button" class="close" data-dismiss="alert">×</button>
				  <strong id="errorMsg"></strong>
			  </div>
			  <div class="alert alert-success  alert-dismissible" id="successDiv" style="display:none;">
				   <button type="button" class="close" data-dismiss="alert">×</button>
					   <strong id="successMsg"></strong>
			  </div>
		  <div class="row">
			<div class="col">
			   <label>Account For: </label>
			  <select class="form-control" name="account_for" onchange="displaydiv()" id="account_select" style="width:100%;">
				<option value="">Select Account for</option>
                <option value="1">Online Nomination</option>
                <option value="2">Duplicate Epic</option>
				
			 </select>
             <div class="accforaferrormsg errormsg"></div>
			 
			</div>
			<div class="col">

			<label>Select District: </label>
			  <select class="form-control" name="dist_sel_account"  id="dist_select" style="width:100%;">
				
                <option value="">Select District</option> 
                @if(count($dist_no) > 0)
                @foreach ($dist_no as $dist)					
				<option value="{{ $dist->DIST_NO }}">{{ $dist->DIST_NO }} - {{ $dist->DIST_NAME }} </option> 
				 @endforeach
                 @endif
			 </select>
			 <div class="selectdistrictaferrormsg errormsg"></div>
			  
			</div>
			
		  </div>

		  <div class="row">
			<div class="col">
			<label>Linked Account Name</label>
			  <input type="text" class="form-control" id="account_title" name="title">
			  <div class="ferrormsg errormsg"></div>
			</div>
			<div class="col">
			<label>Mobile Number</label>
			  <input type="number" class="form-control" id="account_mobile" name="account_mob">
			  <div class="urlerrormsg errormsg"></div>
			</div>
			
		  </div>
		  
		  <div class="row">
			<div class="col">
			<label>Email Address</label>
			  <input type="email" class="form-control" id="account_email" name="account_email">
			  <div class="emailerrormsg errormsg"></div>

			</div>
			<div class="col">
			<label>Account Number</label>
			  <input type="number" class="form-control" name="acc_num" id="acc_num">
			  <div class="accnumbererrormsg errormsg"></div>
			</div>
		  </div> 

          <div class="row">
			<div class="col">
			<label>Account Type</label>
			  <select class="form-control" name="acc_typecs" id="account_type">
                  <option></option>
				  <option value="1">Current</option>
                  <option value="2">Saving</option>
			  </select>
			  <div class="acctypeerrormsg errormsg"></div>
			  
			</div>
			<div class="col">
			<label>IFSC Code</label>
			  <input type="text" class="form-control" name="acc_ifsc" id="acc_ifsc">
			  <div class="accifscerrormsg errormsg"></div>
			</div>
		  </div> 

		  <div class="row"  >
			<div class="col">
			<label>Beneficiary Name</label>
               <input type="text" class="form-control" name="acc_beni" id="acc_beni" >
			   <div class="accbenierrormsg errormsg"></div>
			</div>
			<div class="col" id="epic_charges_div">
			<label>Charges for duplicate EPIC(In Rs):</label>
			  <input type="number" class="form-control" name="amount_charge_epic"  id="amount_charge_epicid">
			  <div class="chargeerrormsgaccnameupdateepic errormsg"></div>
			</div>
			
		  </div> 

		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="save_menu" class="btn btn-primary">Save Account</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">


 <!-- Modal Header -->
 <div class="modal-header">
        <h4 class="modal-title">Account Information <span id="distname"></span> -<span id="distnoid"></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body ">
       <form class="form-horizontal" method="POST" action="{{url('acceo/update-account-distwise')}}" id="RoPsWiseDetailsUpdate">
		
         {{ csrf_field() }}
                         
         <input type="hidden" name="account_for_payemt" id="account_for_payemt" value="">
		 <input type="hidden" name="account_number_previous" id="account_number_previous" value="">
		 <input type="hidden" name="dist_no_hidden" id="dist_no_hidden" value="">
         

         <div class="form-group row">
          <label class="col-sm-4 form-control-label">Account Name <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="ACC_NAME_EN" class="form-control" name="ACC_NAME_EN" value="">
           <span class="text-danger"></span>
          </div>
        </div>

         <div class="form-group row">
          <label class="col-sm-4 form-control-label">Mobile Number <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="ACC_MOBILE_EN"  maxsize="6" minsize="1" class="form-control" name="ACC_MOBILE_EN" 
		   value="">
           <span class="text-danger"></span>
          </div>
        </div>
		
		

          <div class="form-group row">
          <label class="col-sm-4 form-control-label">Email Address <sup>*</sup></label>
          <div class="col-sm-8">
          <input type="text" id="ACC_EMAIL_EN"  maxsize="6" minsize="1" class="form-control" name="ACC_EMAIL_EN" 
		  value="" >
          <span class="text-danger"></span>
          </div>
        </div>
        

    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Account Number <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="ACC_NUM_EN"  maxsize="6" minsize="1" class="form-control" name="ACC_NUM_EN" 
		   value="">
           <span class="text-danger"></span>
          </div>
        </div>  
        


    <div class="form-group row">
		  <label class="col-sm-4 form-control-label">Account Type</label>
		  <div class="col-sm-8">
			  <select class="form-control" name="acc_typecs" id="ACC_TYPE_EN">
                  <option></option>
				  <option value="1">Current</option>
                  <option value="2">Saving</option>
			  </select>
			  <span class="text-danger"></span>
          </div>

    </div>


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">IFSC Code <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="ACC_IFSC_EN" maxsize="6" minsize="1" class="form-control" name="ACC_IFSC_EN" value="">
           <span class="text-danger"></span>
          </div>
    </div>


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Beneficiary Name <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="ACC_BENI_EN" maxsize="6" minsize="1" class="form-control" name="ACC_BENI_EN" value="">
           <span class="text-danger"></span>
          </div>
    </div>


	<div class="form-group row charges_div" style="display:none;">
          <label class="col-sm-4 form-control-label">Charges for duplicate EPIC(In Rs): <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="ACC_charges_for" maxsize="6" minsize="1" class="form-control" name="ACC_charges_for" value="">
           <span class="text-danger"></span>
          </div>
    </div>

              
    
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	   <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
	  </form>
    </div>
  </div>
</div>
  <!--EDIT POP UP ENDS-->

@endsection
@section('script')
<script>

$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });

$('#save_menu').click(function(e) {
		

		var acc_for    =  $('#account_select').val();
		var dist_select = $('#dist_select').val();
		var acc_name   =  $('#account_title').val();
		var acc_mobile =  $('#account_mobile').val();
		var acc_email  =  $('#account_email').val();
		var acc_number =  $('#acc_num').val();
		var acc_type   =  $('#account_type').val();
        var acc_ifsc   =  $('#acc_ifsc').val();
        var acc_beni   =  $('#acc_beni').val();
		var charges_epic = $('#amount_charge_epicid').val();

		
		
		if (acc_for == '') {
                $('.errormsg').html('');
                $('.accforaferrormsg').html('Select Account options');
                $("input[name='account_for']").focus();
                return false;
            }

		if (dist_select == '') {
                $('.errormsg').html('');
                $('.selectdistrictaferrormsg').html('Select District from options');
                $("input[name='dist_sel_account']").focus();
                return false;
        }

		if (acc_name == '') {
			$('.errormsg').html('');
			$('.ferrormsg').html('Enter Account Name');
			$("input[name='title']").focus();
			return false;
		}

		if (acc_mobile == '') {
			$('.errormsg').html('');
			$('.urlerrormsg').html('Please Input Mobile Number');
			$("input[name='account_mob']").focus();
			return false;
		}

		if (acc_email == '') {
			$('.errormsg').html('');
			$('.emailerrormsg').html('Please enter email id');
			$("input[name='account_email']").focus();
			return false;
		}

		if (acc_number == '') {
			$('.errormsg').html('');
			$('.accnumbererrormsg').html('Please enter account number');
			$("input[name='acc_num']").focus();
			return false;
		}

		if (acc_type == '') {
			$('.errormsg').html('');
			$('.acctypeerrormsg').html('Please select account type');
			$("input[name='acc_typecs']").focus();
			return false;
		}

		if (acc_ifsc == '') {
			$('.errormsg').html('');
			$('.accifscerrormsg').html('Please enter account ifsc');
			$("input[name='acc_ifsc']").focus();
			return false;
		}

		if (acc_beni == '') {
			$('.errormsg').html('');
			$('.accbenierrormsg').html('Please input beneficiary name');
			$("input[name='acc_beni']").focus();
			return false;
		}
		
			
		$.ajax({
                    url: "account_add",
                    type: 'post',
                    data: {
						
                        account_for:acc_for,
						account_name:acc_name,
						acc_mobile:acc_mobile,
						acc_email:acc_email,
						acc_number:acc_number,
						acc_type:acc_type,
						acc_ifsc:acc_ifsc,
                        acc_beni:acc_beni,
						dist_select:dist_select,
						charges_epic:charges_epic
						 
                    },
                    success: function(data) {
                        var error_html = '';
                            if(data.status == 'validation'){
                                $.each(data.response, function(key, value){
                                    $('#errorDiv').show();
                                    error_html += '<p>'+value+'</p>';
                                });
                                $('#errorMsg').html(error_html);
								setTimeout(function(){ $("#errorDiv").hide();}, 3000);
                            }else if(data.status == 'error'){
                                $('#errorDiv').show();
                                $('#errorMsg').html(data.response);
								setTimeout(function(){ $("#errorDiv").hide();}, 3000);
                            }else{
								$('#errorDiv').hide();
								$('#successDiv').show();
								$('#successMsg').html('Account Saved successfully.');
								setTimeout(function(){ $("#successDiv1").hide();
									location.reload();
								}, 3000);
								
                               
								
                            }    
                    },
                    error: function(data) {
                           
                    }
            });
        
        //$("#add_menu_form").submit();
    });


   

	$(document).on('click', '#nom_update_btn', function(){ 

		var acc_for    =  $('#account_for_nomid').val();
		var acc_name   =  $('#linked_acc_name_nomid').val();
		var acc_mobile =  $('#mobile_account_nomid').val();
		var acc_email  =  $('#email_account_nomid').val();
		var acc_number =  $('#account_number_nomid').val();
		var acc_type   =  $('#account_type_nomid').val();
        var acc_ifsc   =  $('#account_ifsc_nomid').val();
        var acc_beni   =  $('#account_beni_nomid').val();

		if (acc_for == '') {
                $('.errormsg').html('');
                $('.aferrormsgupdate').html('Select Account options');
                $("input[name='account_for_nom']").focus();
                return false;
            }

		if (acc_name == '') {
			$('.errormsg').html('');
			$('.ferrormsgaccnameupdate').html('Enter Account Name');
			$("input[name='linked_acc_name_nom']").focus();
			return false;
		}

		if (acc_mobile == '') {
			$('.errormsg').html('');
			$('.mobilerrormsgupdate').html('Please Input Mobile Number');
			$("input[name='mobile_account_nom']").focus();
			return false;
		}

		if (acc_email == '') {
			$('.errormsg').html('');
			$('.emailerrormsgupdate').html('Please enter email id');
			$("input[name='email_account_nom']").focus();
			return false;
		}

		if (acc_number == '') {
			$('.errormsg').html('');
			$('.accnumbererrormsgupdate').html('Please enter account number');
			$("input[name='account_number_nom']").focus();
			return false;
		}

		if (acc_type == '') {
			$('.errormsg').html('');
			$('.acctypeerrormsgupdate').html('Please select account type');
			$("input[name='account_type_nom']").focus();
			return false;
		}

		if (acc_ifsc == '') {
			$('.errormsg').html('');
			$('.ifscerrormsgupdate').html('Please enter account ifsc');
			$("input[name='account_ifsc_nom']").focus();
			return false;
		}

		if (acc_beni == '') {
			$('.errormsg').html('');
			$('.benierrormsgupdate').html('Please input beneficiary name');
			$("input[name='account_beni_nom']").focus();
			return false;
		}

		


    });


	$(document).on('click', '#epic_update_btn', function(){ 

		var acc_for    =  $('#account_for_epicid').val();
		var acc_name   =  $('#linked_acc_name_epicid').val();
		var acc_mobile =  $('#mobile_account_epicid').val();
		var acc_email  =  $('#email_account_epicid').val();
		var acc_number =  $('#account_number_epicid').val();
		var acc_type   =  $('#account_type_epicid').val();
		var acc_ifsc   =  $('#account_ifsc_epicid').val();
		var acc_beni   =  $('#account_beni_epicid').val();

		if (acc_for == '') {
				$('.errormsg').html('');
				$('.aferrormsgupdateepic').html('Select Account options');
				$("input[name='account_for_epic']").focus();
				return false;
			}

		if (acc_name == '') {
			$('.errormsg').html('');
			$('.ferrormsgaccnameupdateepic').html('Enter Account Name');
			$("input[name='linked_acc_name_epic']").focus();
			return false;
		}

		if (acc_mobile == '') {
			$('.errormsg').html('');
			$('.mobileerrormsgaccnameupdateepic').html('Please Input Mobile Number');
			$("input[name='mobile_account_epic']").focus();
			return false;
		}

		if (acc_email == '') {
			$('.errormsg').html('');
			$('.emailerrormsgaccnameupdateepic').html('Please enter email id');
			$("input[name='email_account_epic']").focus();
			return false;
		}

		if (acc_number == '') {
			$('.errormsg').html('');
			$('.accnumbererrormsgaccnameupdateepic').html('Please enter account number');
			$("input[name='account_number_epic']").focus();
			return false;
		}

		if (acc_type == '') {
			$('.errormsg').html('');
			$('.acctypeerrormsgaccnameupdateepic').html('Please select account type');
			$("input[name='account_type_epic']").focus();
			return false;
		}

		if (acc_ifsc == '') {
			$('.errormsg').html('');
			$('.ifscerrormsgaccnameupdateepic').html('Please enter account ifsc');
			$("input[name='account_ifsc_epic']").focus();
			return false;
		}

		if (acc_beni == '') {
			$('.errormsg').html('');
			$('.benierrormsgaccnameupdateepic').html('Please input beneficiary name');
			$("input[name='account_beni_epic']").focus();
			return false;
		}




});


	setTimeout(function() {
    	$('#successMessage').fadeOut('fast');
	}, 3000);

	setTimeout(function() {
    	$('#errorMessage').fadeOut('fast');
	}, 3000);


	function displaydiv(){
		var val_for = $('#account_select').val();
		if(val_for == 2){
			$('#epic_charges_div').show();
		}
		if(val_for == 1){
			$('#epic_charges_div').hide();
		}
	}


	$(document).on("click", ".PsWiseDetailspopup", function () {

		distno = $(this).attr('data-dist-no');
		distname= $(this).attr('data-dist-name');
		accountname = $(this).attr('data-account-name');
		accountmobile= $(this).attr('data-account-mobile');
		accountemail= $(this).attr('data-account-email');
		accountnumber= $(this).attr('data-account-number');
		accounttype= $(this).attr('data-account-type');
		accountifsc= $(this).attr('data-ifsc-code');
		accountbeni= $(this).attr('data-beni-name');
		account_for_payment = $(this).attr('data-account-for');
		account_number_previous = $(this).attr('data-account-number');
		dist_no_hidden = $(this).attr('data-dist-no');
		
		charges_for_epic= $(this).attr('data-charges-for');


		$('#ACC_NAME_EN').val(accountname);
		$('#distnoid').text(distno);
		$('#distname').text(distname);

		$('#ACC_MOBILE_EN').val(accountmobile);
		$('#ACC_EMAIL_EN').val(accountemail);

		$('#ACC_NUM_EN').val(accountnumber);
		$('#ACC_TYPE_EN').val(accounttype);

		$('#ACC_IFSC_EN').val(accountifsc);
		$('#ACC_BENI_EN').val(accountbeni);
		$('#account_for_payemt').val(account_for_payment);
		$('#account_number_previous').val(account_number_previous);
		$('#dist_no_hidden').val(dist_no_hidden);
		//$('#ACC_charges_for').val(charges_for_epic);

      
		if(account_for_payment == '2'){
			$('.charges_div').show();
			$('#ACC_charges_for').val(charges_for_epic);
		}else{
			$('.charges_div').hide();
		}
		
			
		});


		//validation start model form

		$("#RoPsWiseDetailsUpdate").validate({

    rules: {
		ACC_NAME_EN: { required: true,minlength:2, maxlength: 350,},
		ACC_MOBILE_EN: { required: true,number:true,noSpace: true,minlength:1, maxlength: 10,},
		ACC_EMAIL_EN: { required: true, noSpace: true,},
		ACC_NUM_EN: { required: true,number:true,noSpace: true},
		ACC_TYPE_EN: { required: true},
		ACC_IFSC_EN: { required: true},
		ACC_BENI_EN: { required: true},
		ACC_charges_for: { required: true},
            },
  messages: { 
				ACC_NAME_EN: {
                      required: "Account Name is required.",
                     
                  },
                  ACC_MOBILE_EN: {
                      required: "Mobile  Numbers required.",
                      number: "Mobile number should be numbers only.",
                      noSpace: "Enter mobile no without space.",
                      minlength: "Minlength length of mobile should be 1 characters.",
                      maxlength: "Maximum length of mobile should be 10 characters.",
                  },
                  ACC_EMAIL_EN: {
                      required: "Email is required.",
                      
                  },
                  ACC_NUM_EN: {
                      required: "Account  Numbers required.",
                     
                  },
                  ACC_TYPE_EN: {
                      required: "Account type is required",
                      
                  },
                  ACC_IFSC_EN: {
                      required: "Account IFSC is required.",
                      
                  },
                  ACC_BENI_EN: {
                      required: "Accoiunt Benificiary is required.",
                     
                  },
                  ACC_charges_for: {
                      required: "Please enter charges for ",
                     
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

		//validation ends model ends

	

</script>
@endsection
