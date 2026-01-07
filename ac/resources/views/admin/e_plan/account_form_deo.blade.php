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
	if(Auth::user()->role_id == '5'){
		$prefix 	= 'acdeo';
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
            <div class="col"><h4>Accounts List</div>
            <div class="col">
			
            <p class="mb-0 text-right">
              <button class="btn btn-success" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle"></i> Add Account Info</button> 
			  <!--<a href="{{Common::generate_url('finialised-account')}}"><button  class="btn btn-primary">Finalize Account</button></a> -->
			  <a  id="finalised_account" ><button  class="btn btn-primary">Finalize Account</button></a> 
            </p>

			
			
			
          </div>

		
        </div>
      </div>

<div>

<div class="container-fluid" style="margin-top: 3%; margin-bottom:3%;">
<div class="row">

@if(count($account_data_nom) > 0)

@foreach($account_data_nom as $key => $value)

<div class="col-md-6">
  <div class="" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Online Nomination</h5>
        
      </div>
      <div class="modal-body">
		
		<form method="POST" action="{{url('acdeo/update_nom_account')}}">
		  @csrf
			  <div class="alert alert-danger  alert-dismissible" id="errorDivnom1" style="display:none;">
				  <button type="button" class="close" data-dismiss="alert">×</button>
				  <strong id="errorMsgnom1"></strong>
			  </div>
			  <div class="alert alert-success  alert-dismissible" id="successDivnom1" style="display:none;">
				   <button type="button" class="close" data-dismiss="alert">×</button>
				   <strong id="successMsgnom1"></strong>
			  </div>
			  <input type="hidden" id="xvalnom">
			  
			  <div class="row">
			<div class="col">
			   <label>Account For </label>
			  <select class="form-control" name="account_for_nom" id="account_for_nomid" style="width:100%;">
                <option value="1" selected>Online Nomination</option>	
			 </select>
             <div class="aferrormsgupdate errormsg"></div>
			 
			</div>
			<div class="col">
			  <label>Linked Account Name</label>
			  <input type="text" class="form-control" value="{{$value->account_name}}" id="linked_acc_name_nomid" name="linked_acc_name_nom">
			  <div class="ferrormsgaccnameupdate errormsg"></div>
			</div>
			
		  </div>

		  <div class="row">
			<div class="col">
			   <label>Mobile Number</label>
			  <input type="number" class="form-control" value="{{$value->account_mobile}}" id="mobile_account_nomid" name="mobile_account_nom">
			  <div class="mobilerrormsgupdate errormsg"></div>
			</div>
			<div class="col">
			  <label>Email Address</label>
			  <input type="email" class="form-control" value="{{$value->account_email}}" id="email_account_nomid" name="email_account_nom">
			  <div class="emailerrormsgupdate errormsg"></div>
			</div>
			
		  </div>
		  
		  <div class="row">
			<div class="col">
			  <label>Account Number</label>
			  <input type="number" class="form-control" value="{{$value->account_number}}" name="account_number_nom" id="account_number_nomid">

			  <input type="hidden" class="form-control" value="{{$value->account_number}}" name="account_number_nom_change">

			  <div class="accnumbererrormsgupdate errormsg"></div>
			</div>
			<div class="col">
			   <label>Account Type</label>
			  <select class="form-control" name="account_type_nom" value="{{$value->account_type}}" id="account_type_nomid">
                  
				  <option  value="1" {{($value->account_type)==1 ? 'selected' : ''}}>Current</option>
                  <option  value="2" {{($value->account_type)==2 ? 'selected' : ''}}>Saving</option>
			  </select>
			  <div class="acctypeerrormsgupdate errormsg"></div>
			</div>
		  </div> 

          <div class="row">
			<div class="col">
			  <label>IFSC Code</label>
			  <input type="text" class="form-control" value="{{$value->account_ifsc}}" name="account_ifsc_nom" id="account_ifsc_nomid">
			  <div class="ifscerrormsgupdate errormsg"></div>
			</div>
			<div class="col">
			   <label>Beneficiary Name</label>
               <input type="text" value="{{$value->account_benificeary}}" class="form-control" name="account_beni_nom" id="account_beni_nomid" >
			   <div class="benierrormsgupdate errormsg"></div>
			</div>
		  </div> 

		  <div class="row">
			<div class="col">
			  <label>Bank Name</label>
			  <input type="text" class="form-control" value="{{$value->bank_name}}" name="update_bk_name_nom_deo" id="update_bk_name_nom_deoid">
			  <div class="banknameepicerrormsgupdate errormsg"></div>
			</div>
			<div class="col">
			 
			</div>
		  </div>

		  

		  
		  <div class="modal-footer">
		  
		  @if($value->is_finalised == 0)
		  <button class="btn btn-primary" id="nom_update_btn" type="submit">Update Account Nomination</button>
		  @else
		  
		  @endif
		  
         </div>
		</form>
      </div>
      <!--<div class="modal-footer">
        <button type="button" id="update_account_nom" class="btn btn-primary">Update Account</button>
       
        
      </div>-->
    </div>
  </div>
</div>

@endforeach
@endif

@if(count($account_data_epic) > 0)

@foreach($account_data_epic as $key => $value)
<div class="col-md-6">
<div class="" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Duplicate EPIC</h5>
        
      </div>
      <div class="modal-body">
		
		<form method="POST" action="{{url('acdeo/update_epic_account')}}"> 
		  @csrf
			  <div class="alert alert-danger  alert-dismissible" id="errorDiv1" style="display:none;">
				  <button type="button" class="close" data-dismiss="alert">×</button>
				  <strong id="errorMsg1"></strong>
			  </div>
			  <div class="alert alert-success  alert-dismissible" id="successDiv1" style="display:none;">
				   <button type="button" class="close" data-dismiss="alert">×</button>
				   <strong id="successMsg1"></strong>
			  </div>
			  <input type="hidden" id="xval">
			  
			  <div class="row">
			<div class="col">
			   <label>Account For: </label>
			  <select class="form-control"  name="account_for_epic" id="account_for_epicid" style="width:100%;">
				<option value="2" selected>Duplicate Epic</option>
			 </select>
             <div class="aferrormsgupdateepic errormsg"></div>
			</div>
			<div class="col">
			  <label>Linked Account Name</label>
			  <input type="text" class="form-control" value="{{$value->account_name}}" id="linked_acc_name_epicid" name="linked_acc_name_epic">
			  <div class="ferrormsgaccnameupdateepic errormsg"></div>
			</div>
			
		  </div>

		  <div class="row">
			<div class="col">
			   <label>Mobile Number</label>
			  <input type="number" class="form-control" id="mobile_account_epicid" value="{{$value->account_mobile}}" name="mobile_account_epic">
			  <div class="mobileerrormsgaccnameupdateepic errormsg"></div>
			</div>
			<div class="col">
			  <label>Email Address</label>
			  <input type="email" class="form-control" id="email_account_epicid" value="{{$value->account_email}}" name="email_account_epic">
			  <div class="emailerrormsgaccnameupdateepic errormsg"></div>
			</div>
			
		  </div>
		  
		  <div class="row">
			<div class="col">
			  <label>Account Number</label>
			  <input type="number" class="form-control" name="account_number_epic" 
			  value="{{$value->account_number}}" id="account_number_epicid">

			  <input type="hidden" class="form-control" value="{{$value->account_number}}" name="account_number_epic_change">

			  <div class="accnumbererrormsgaccnameupdateepic errormsg"></div>
			</div>
			<div class="col">
			   <label>Account Type</label>
			  <select class="form-control"  name="account_type_epic" value="{{$value->account_type}}" id="account_type_epicid">
                  
				  <option  value="1" {{($value->account_type)==1 ? 'selected' : ''}}>Current</option>
                  <option  value="2" {{($value->account_type)==2 ? 'selected' : ''}}>Saving</option>
			  </select>
			  <div class="acctypeerrormsgaccnameupdateepic errormsg"></div>
			</div>
		  </div>

          <div class="row">
			<div class="col">
			  <label>IFSC Code</label>
			  <input type="text" class="form-control" name="account_ifsc_epic" value="{{$value->account_ifsc}}" id="account_ifsc_epicid">
			  <div class="ifscerrormsgaccnameupdateepic errormsg"></div>
			</div>
			<div class="col">
			   <label>Beneficiary Name</label>
               <input type="text" class="form-control" name="account_beni_epic" value="{{$value->account_benificeary}}" id="account_beni_epicid">
			   <div class="benierrormsgaccnameupdateepic errormsg"></div>
			</div>
		  </div> 

		  <div class="row">
			<div class="col">
			  <label>Charges for duplicate EPIC(In Rs):</label>
			  <input type="text" class="form-control" name="charges_epic_update" value="{{$value->amount_for_duplicate_epic}}" id="charges_epicid_update">
			  <div class="chargeepicerrormsgaccnameupdateepic errormsg"></div>
			</div>
			<div class="col">
			<label>Bank Name</label>
			  <input type="text" class="form-control" name="deo_bank_name_update_epic" value="{{$value->bank_name}}" id="deo_bank_name_update_epicid">
			  <div class="banknameerrormsgaccnameupdateepic errormsg"></div>
			</div>
			 
			  
		  </div> 

		   

		  <div class="modal-footer">
		  
		    @if($value->is_finalised == 0)
			<button class="btn btn-primary" id="epic_update_btn" type="submit">Update Account Epic</button>
			@else
		  	
		    @endif
         </div>

		</form>
      </div>
     <!-- <div class="modal-footer">
       <button type="button" id="edit_account_epic" class="btn btn-primary">Edit Account</button>
        <button type="button" id="update_account_epic" class="btn btn-primary" style="display:none;">Update Account</button>
      </div> -->
    </div>
  </div>
</div>
@endforeach
@endif
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
             <div class="aferrormsg errormsg"></div>
			 
			</div>
			<div class="col">
			  <label>Linked Account Name</label>
			  <input type="text" class="form-control" id="account_title" name="title">
			  <div class="ferrormsg errormsg"></div>
			</div>
			
		  </div>

		  <div class="row">
			<div class="col">
			   <label>Mobile Number</label>
			  <input type="number" class="form-control" id="account_mobile" name="account_mob">
			  <div class="urlerrormsg errormsg"></div>
			</div>
			<div class="col">
			  <label>Email Address</label>
			  <input type="email" class="form-control" id="account_email" name="account_email">
			  <div class="emailerrormsg errormsg"></div>
			</div>
			
		  </div>
		  
		  <div class="row">
			<div class="col">
			  <label>Account Number</label>
			  <input type="number" class="form-control" name="acc_num" id="acc_num">
			  <div class="accnumbererrormsg errormsg"></div>
			</div>
			<div class="col">
			   <label>Account Type</label>
			  <select class="form-control" name="acc_typecs" id="account_type">
                  <option value="">Select Account Type</option>
				  <option value="1">Current</option>
                  <option value="2">Saving</option>
			  </select>
			  <div class="acctypeerrormsg errormsg"></div>
			</div>
		  </div> 

          <div class="row">
			<div class="col">
			  <label>IFSC Code</label>
			  <input type="text" class="form-control" name="acc_ifsc" id="acc_ifsc">
			  <div class="accifscerrormsg errormsg"></div>
			</div>
			<div class="col">
			   <label>Beneficiary Name</label>
               <input type="text" class="form-control" name="acc_beni" id="acc_beni" >
			   <div class="accbenierrormsg errormsg"></div>
			</div>
		  </div>

		  

		  <div class="row"  >
			<div class="col">
			  <label>Bank Name</label>
			  <input type="text" class="form-control" value="" name="bank_account_name" id="bank_account_nameid">
			  <div class="bknameerrormsgupdate errormsg"></div>  
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

<!-- Modal HTML -->

	
<div class="modal fade" id="finalised_account_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                					
				<h4 class="modal-title w-100">Are you sure?</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p><span style="color:red">Do you really want to finalise account information. <span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok confirm_button">Confirm</a>
            </div>
        </div>
    </div>
</div>


<!-- Model Ends -->

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
		var acc_name   =  $('#account_title').val();
		var acc_mobile =  $('#account_mobile').val();
		var acc_email  =  $('#account_email').val();
		var acc_number =  $('#acc_num').val();
		var acc_type   =  $('#account_type').val();
        var acc_ifsc   =  $('#acc_ifsc').val();
        var acc_beni   =  $('#acc_beni').val();
		var acc_charges_epic = $('#amount_charge_epicid').val();
		var bank_namee = $('#bank_account_nameid').val();
		
		

		if (acc_for == '') {
                $('.errormsg').html('');
                $('.aferrormsg').html('Select Account options');
                $("input[name='account_for']").focus();
                return false;
            }
		if(acc_for ==2){
			if (acc_for == '') {
				$('.errormsg').html('');
				$('.chargeerrormsgaccnameupdateepic').html('Enter Amoind in Rs');
				$("input[name='amount_charge_epic']").focus();
				return false;
			}
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

		if (bank_namee == '') {
			$('.errormsg').html('');
			$('.bknameerrormsgupdate').html('Please Enter Bank name');
			$("input[name='bank_account_name']").focus();
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
						acc_charges_epic:acc_charges_epic,
						bank_namee:bank_namee
						 
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


    $(document).on('click', '#edit_account_epic', function(){ 
		$("input[name='linked_acc_name_epic']").attr("readonly", false); 
		$("input[name='mobile_account_epic']").attr("readonly", false); 
		$("input[name='email_account_epic']").attr("readonly", false); 
		$("input[name='account_number_epic']").attr("readonly", false); 
		$("input[name='account_type_epic']").attr("readonly", false); 
		$("input[name='account_ifsc_epic']").attr("readonly", false); 
		$("input[name='account_beni_epic']").attr("readonly", false); 
		$('#account_type_epicid').prop('disabled',false);
        $.ajax({  
                url:"edit-account-epic",
                method:"POST",  
                dataType:"json",  
                success:function(data){ 
                        $('#edit_account_epic').hide();
                        $('#update_account_epic').show();
                        
					    $('#account_for_epicid').val(data.account_data_epic.account_payment_for);
                        $('#linked_acc_name_epicid').val(data.account_data_epic.account_name);
                        $('#mobile_account_epicid').val(data.account_data_epic.account_mobile);
						$('#email_account_epicid').val(data.account_data_epic.account_email);
                        $('#account_number_epicid').val(data.account_data_epic.account_number);
                        $('#account_type_epicid').val(data.account_data_epic.account_type);
                        $('#account_ifsc_epicid').val(data.account_data_epic.account_ifsc);
                        $('#account_beni_epicid').val(data.account_data_epic.account_benificeary);
                        
                }  
           }); 
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
var acc_bank_name = $('#update_bk_name_nom_deoid').val();

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

if(acc_bank_name == ''){
	$('.errormsg').html('');
	$('.banknameepicerrormsgupdate').html('Please input bank name');
	$("input[name='update_bk_name_nom_deo']").focus();
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
var acc_charges = $('#charges_epicid_update').val();
var bank_name_epic = $('#deo_bank_name_update_epicid').val();


if (acc_for == '') { 
		$('.errormsg').html('');
		$('.aferrormsgupdateepic').html('Select Account options');
		$("input[name='account_for_epic']").focus();
		return false;
	}

	if (acc_charges == '') {
	$('.errormsg').html('');
	$('.chargeepicerrormsgaccnameupdateepic').html('Enter charges for EPIC');
	$("input[name='charges_epic_update']").focus();
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

if (bank_name_epic == '') {
	$('.errormsg').html('');
	$('.banknameerrormsgaccnameupdateepic').html('Please input bank name');
	$("input[name='deo_bank_name_update_epic']").focus();
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


	$('#finalised_account').click(function(e){
	  var st_code = '{{Auth::user()->st_code}}';
	  var dist_no = '{{Auth::user()->dist_no}}';
	  $('#finalised_account_model').modal('show');
	   
  });
  $('.confirm_button').click(function(e){
		var url = '{{Common::generate_url('finialised-account')}}';
		window.location.href = url;
  });

</script>
@endsection
