@extends('admin.layouts.ac.dashboard-theme')
@section('title', 'Absentee Voter MIS')
@section('bradcome')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => 'javascript:void(0)',
    'name' => 'Operational Dashboard'
  ]; 
  ?>
  @foreach($breadcrumbs as $itr_bread)
  <li><a href="{{$itr_bread['href']}}"><span class="icon icon-beaker"> </span> {{$itr_bread['name']}}</a></li>
  @endforeach
@endsection
@section('content') 
<style>
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 36px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}
.info-box-content {
    padding: 5px 10px;
    text-align: center;
}
.bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body {
    background-color: #00c0ef !important;
}

.StateTable tr{position:relative;}
.stateModal .modal-body {  overflow-y: auto;   height: 65vh;   position:relative; overflow-x: hidden;    padding: 0;    margin: 15px 15px 0 15px;}
.StateTable thead th {  position: sticky!important;    top: 0;    z-index: 9;    background: #B22682;}
.StateTable { margin-bottom:0px;}
.StateTable td {  padding: 5px;}
.StateTable thead th:first-child {  width: 120px;}
.StateTable tr td:last-child {background:#f7f7f7;}
.StateTable td b { font-weight:500;}
.modal-full-width{ max-width: 1200px;}
.dnone{display:none;}
.dblock{display:block;}
.cp{cursor:pointer;}
</style>
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4>Reset Password</div> 
            </div>
      </div>
  
 <div class="card-body">
	  <h6 class="mb-3">Officer Count</h6>
	  <div class="row">
		@php $colorArr=['#8142FF','#22CECE','#FFCD56']; @endphp
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="info-box" style="border-bottom:4px solid #22CECE;">
			<div id="dataset">
			<table class="table table-striped opdb">
				<thead>
				<tr>
					<th>User Name</th>
					<th>Officer Name</th>
					<th>Designation</th>
					<th>Account Status</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
				@foreach($get_officer_count as $k=> $entry)
					<tr>
						<td>{{ $entry->officername }}</td>
						<td>{{ $entry->name }}</td>
						<td>{{ $entry->designation }}</td>
						<td id="actext_{{$k}}">{{ ($entry->is_active=='1')?'Active':'Inactive' }}</td>
						<td><a class="lcon"{{$k}} style="display:none;"><img src="{{ asset('img/loading-img.gif')}}" alt="" width="40"/></a><a href="Javascript:;" onclick="changeStatus('{{$entry->id}}','{{$entry->is_active}}','{{$k}}')"><i class="fa fa-pencil-square" title="Change Status"></i></a> | <a href="Javascript:;" onclick="changePassword('{{$entry->id}}')"><i class="fa fa-lock" title="Reset Password"></i></a> | <a href="Javascript:;" onclick="changePin('{{$entry->id}}')"><i class="fa fa-key" title="Reset Pin"></i></a></td>
					</tr>
				@endforeach
				</tbody>
			</table>
			</div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
      </div>
	  
	  <div>&nbsp;</div>
	  
    </div>
    </div>
  </div>
  
  </div>
  </section>
  </main>
<!-- set up the modal to start hidden and fade in and out -->
<div class="modal fade confirmAction" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="padding-top:15%;">
    <div class="modal-dialog">
        <div class="modal-content">
			<input type="hidden" id="oid" value="">
			<input type="hidden" id="sts" value="">
			<input type="hidden" id="k" value="">
            <!-- dialog body -->
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                Do you want to change status?
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer">
			<button type="button" class="btn btn-success changeStatus">Yes</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
			</div>
        </div>
    </div>
</div>
<div class="modal fade PasswordUpdate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="padding-top:15%;">
    <div class="modal-dialog">
        <div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLongTitle">Reset Password</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
            <!-- dialog body -->
            <div class="modal-body">
		<form id="change_password" method="POST" action="" autocomplete="off">			
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="pid" id="pid" value="">
                            <div class="form-group row">
                                <label for="new-password" class="col-md-4 control-label">New password <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('password')){ echo 'is-invalid'; } ?>" onkeyup="CheckPasswordStrength(this.value,'newpass')" name="password" id="password" value="" autocomplete="off">
                                    @if ($errors->has('password'))
          <span class="newpassword errormsg errorred">{{ $errors->first('password') }}</span>
        @endif
		<span class="newpass errormsg errorred" style="display:none;"></span>
                                                                    </div>
                                

                                


                            </div>

                            <div class="form-group row">
                                <label for="new-password-confirm" class="col-md-4 control-label">Confirm New password <sup>*</sup></label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control <?php if($errors->has('password_confirmation')){ echo 'is-invalid'; } ?>" onkeyup="CheckPasswordStrength(this.value,'conpass')" name="password_confirmation" id="password_confirmation" value="" autocomplete="off">
                                    @if ($errors->has('password_confirmation'))
          <span class="newpassword errormsg errorred">{{ $errors->first('password_confirmation') }}</span>
        @endif
		<span class="conpass errormsg errorred" style="display:none;"></span>
                                </div>
                                
                            </div>
           
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer">
			<button type="button" class="btn btn-success secure_password_check">Submit</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
			</div>
			 </form>
        </div>
    </div>
</div>
<div class="modal fade PinUpdate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="padding-top:15%;">
    <div class="modal-dialog">
        <div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLongTitle">Reset Pin</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
            <!-- dialog body -->
            <div class="modal-body">
		<form id="change_pin" method="POST" action="" autocomplete="off">			
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="pinid" id="pinid" value="">
                            <div class="form-group row">
                                <label for="pin" class="col-md-4 control-label">New Pin <sup>*</sup></label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control <?php if($errors->has('pin')){ echo 'is-invalid'; } ?>" onkeyup="CheckPasswordStrength(this.value,'newpass')" name="pin" id="pin" value="" maxlength="4" autocomplete="off">
                                    @if ($errors->has('pin'))
          <span class="newpin errormsg errorred">{{ $errors->first('pin') }}</span>
        @endif
		<span class="newpass errormsg errorred" style="display:none;"></span>
                                                                    </div>
                                

                                


                            </div>

                            <div class="form-group row">
                                <label for="new-pin-confirm" class="col-md-4 control-label">Confirm New pin <sup>*</sup></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control <?php if($errors->has('pin_confirmation')){ echo 'is-invalid'; } ?>" onkeyup="CheckPasswordStrength(this.value,'conpass')"  name="pin_confirmation" id="pin_confirmation" value="" maxlength="4" autocomplete="off">
                                    @if ($errors->has('pin_confirmation'))
          <span class="newpin errormsg errorred">{{ $errors->first('pin_confirmation') }}</span>
        @endif
		<span class="conpass errormsg errorred" style="display:none;"></span>
                                </div>
                                
                            </div>
           
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer">
			<button type="button" class="btn btn-success secure_pin_check">Submit</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
			</div>
			 </form>
        </div>
    </div>
</div>
    
        
   
@endsection
@section('script');
<script src="{{ asset('theme/js/shaen.js') }}"></script>
<script>
var alldata = '<?php echo json_encode($get_officer_count) ?>';
$("#oselect").change(function(){
	var val = $(this).val();
	if(val=='All'){
		$("#otype").text('All');
		$("#ocount").text($.parseJSON(alldata).length);
		createUserData(alldata,'All');
	}else{
		var nval=val.split('_');
		var role_id=nval[0];
		var role_name=nval[1];
		var cnt=0;
		$.each($.parseJSON(alldata), function(index, item) {
			if (item.role_id == role_id) {
				cnt++;
			}
			});
		$("#otype").text(cnt);
		$("#ocount").text(cnt);
		createUserData(alldata,role_id);
	}
});
function createUserData(alldata,role_id){
	var html_str='';
		html_str +='<table class="table table-striped opdb"><thead><tr><th>User Name</th><th>Officer Name</th><th>Designation</th><th>Account Status</th><th>Action</th></tr></thead>';
		
		if (role_id !='All') {
			$.each($.parseJSON(alldata), function(index, item) {
				if (item.role_id == role_id) {
					html_str +='<tr>';
						html_str +='<td>'+item.officername+'</td>';
						html_str +='<td>'+item.name+'</td>';
						html_str +='<td>'+item.designation+'</td>';
						if(item.is_active=='1'){
							html_str +='<td id="actext_'+index+'">Active</td>';
						}else{
							html_str +='<td id="actext_'+index+'">Inactive</td>';
						}
						html_str +='<td><a href="Javascript:;" onclick="changeStatus('+item.id+','+item.is_active+','+index+')"><i class="fa fa-pencil-square" title="Change Status"></i></a> | <a href="Javascript:;" onclick="changePassword('+item.id+')"><i class="fa fa-lock" title="Reset Password"></i></a> | <a href="Javascript:;" onclick="changePin('+item.id+')"><i class="fa fa-key" title="Reset Pin"></i></a></td>';
					html_str +='</tr>';
				}
			});
		}else{
				$.each($.parseJSON(alldata), function(index, item) {
				html_str +='<tr>';
					html_str +='<td>'+item.officername+'</td>';
					html_str +='<td>'+item.name+'</td>';
					html_str +='<td>'+item.designation+'</td>';
					if(item.is_active=='1'){
						html_str +='<td id="actext_'+index+'">Active</td>';
					}else{
						html_str +='<td id="actext_'+index+'">Inactive</td>';
					}
					html_str +='<td><a href="Javascript:;" onclick="changeStatus('+item.id+','+item.is_active+','+index+')"><i class="fa fa-pencil-square" title="Change Status"></i></a> | <a href="Javascript:;" onclick="changePassword('+item.id+')"><i class="fa fa-lock" title="Reset Password"></i></a> | <a href="Javascript:;" onclick="changePin('+item.id+')"><i class="fa fa-key" title="Reset Pin"></i></a></td>';
				html_str +='</tr>';
			});

		}
		html_str +='<tbody></table>';
		$("#dataset").html(html_str);
		$('.opdb').DataTable();
}
$(document).ready(function() {  
    $('.opdb1').DataTable({order: [[2, 'desc']]});
	$('.opdb').DataTable();
});

function changeStatus(oid,sts,k){
	if(oid !=''){
		$("#oid").val(oid);
		$("#sts").val(sts);
		$("#k").val(k);
		$(".confirmAction").modal('show');
	}
}
function changePassword(oid){
	if(oid !=''){
		$("#pid").val(oid);
		$(".PasswordUpdate").modal('show');
		$("#password").val('');
		$("#password_confirmation").val('');
	}
}
function changePin(oid){
	if(oid !=''){
		$("#pinid").val(oid);
		$(".PinUpdate").modal('show');
		$("#pin").val('');
		$("#pin_confirmation").val('');
	}
}


$(".changeStatus").click(function(){
	var oid = $("#oid").val();
	var k = $("#k").val();
	var sts = $("#actext_"+k).text();
	if(oid!='' && sts!=''){
		$.ajax({
            url: "changeUserStatus",
				type: 'POST',
				data: {_token:'{{csrf_token()}}', ox: oid,sts: sts},
				success: function(response) {
					if(response=='Y'){
						if(sts=='Inactive'){
							$("#actext_"+k).text('Active');
						}else{
							$("#actext_"+k).text('Inactive');
						}
						success_messages("Success! Status updated");
						$(".confirmAction").modal('hide');
					}else{
						error_messages("Error! Please try again.");
					}
				},
				error: function(error) {
				console.log(error.responseText);
				}
		});
	}
});


$('.secure_password_check').click(function(e){
	  if($("#password").val()==''){
		  $("#password").focus();
		  $(".newpass").show().text("Please enter new password");return false;
	  }else{
		  $(".newpass").hide().text("");
	  }
	  
	  if($("#password_confirmation").val()==''){
		  $("#password_confirmation").focus();
		  $(".conpass").show().text("Please enter confirm password");return false;
	  }else{
		  $(".conpass").hide().text("");
		  if($("#password").val() != $("#password_confirmation").val()){
			$("#password_confirmation").focus();
			$(".conpass").show().text("Confirm password does not match.");return false;  
		  }else{
			$(".conpass").hide().text("");  
		  }
		  
	  }

	  var password = SHA256($("#password").val());
	  var password_confirmation = SHA256($("#password_confirmation").val());
	  $("#password").val(password);
	  $("#password_confirmation").val(password_confirmation);
	  
	  
      $.ajax({
        url: "userpassupdate",
        type: 'POST',
        data: '_token={!! csrf_token() !!}&password='+$('#change_password input[name="password"]').val()+'&password_confirmation='+$('#change_password input[name="password_confirmation"]').val()+'&ox='+$('#change_password input[name="pid"]').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('#change_password .text-danger').remove();
          $('#change_password input').removeClass('is-invalid');
          $('.secure_password_check').prop('disabled',true);
          $('.secure_password_check').text("Validating...");
          $('.secure_password_check').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
        
        },        
        success: function(json) {
          if(json['status'] == true){
            $(".PasswordUpdate").modal('hide');
			success_messages(json['message']);
			location.reload();
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
			$("#password").val('');
			$("#password_confirmation").val('');

            if(json['errors']['password']){
              $("#change_password input[name='password']").addClass("is-invalid");
              $("#change_password input[name='password']").after("<span class='text-danger'>"+json['errors']['password'][0]+"</span>");
			  
            }
            if(json['errors']['password_confirmation']){
              $("#change_password input[name='password_confirmation']").addClass("is-invalid");
              $("#change_password input[name='password_confirmation']").after("<span class='text-danger'>"+json['errors']['password_confirmation'][0]+"</span>");
			  
            }
          }

          $('.secure_pin_check').prop('disabled',false);
          $('.secure_pin_check').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('.secure_pin_check').prop('disabled',false);
          $('.secure_pin_check').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
});
$('.secure_pin_check').click(function(e){
	  if($("#pin").val()==''){
		  $("#pin").focus();
		  $(".newpass").show().text("Please enter new pin");return false;
	  }else{
		  $(".newpass").hide().text("");
	  }
	  
	  if($("#pin_confirmation").val()==''){
		  $("#pin_confirmation").focus();
		  $(".conpass").show().text("Please enter confirm pin");return false;
	  }else{
		  $(".conpass").hide().text("");
		  if($("#pin").val() != $("#pin_confirmation").val()){
			$("#pin_confirmation").focus();
			$(".conpass").show().text("Confirm pin does not match.");return false;  
		  }else{
			$(".conpass").hide().text("");  
		  }
		  
	  }

	  var pin = $("#pin").val();
	  var pin_confirmation = $("#pin_confirmation").val();

      $.ajax({
        url: "userpinupdate",
        type: 'POST',
        data: '_token={!! csrf_token() !!}&pin='+$('#change_pin input[name="pin"]').val()+'&pin_confirmation='+$('#change_pin input[name="pin_confirmation"]').val()+'&ox='+$('#change_pin input[name="pinid"]').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('#change_pin .text-danger').remove();
          $('#change_pin input').removeClass('is-invalid');
          $('.secure_pin_check').prop('disabled',true);
          $('.secure_pin_check').text("Validating...");
          $('.secure_pin_check').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
        
        },        
        success: function(json) {
          if(json['status'] == true){
            $(".PinUpdate").modal('hide');
			success_messages(json['message']);
			location.reload();
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
			
			$("#pin").val('');
			$("#pin_confirmation").val('');

            if(json['errors']['pin']){
              $("#change_pin input[name='pin']").addClass("is-invalid");
              $("#change_pin input[name='pin']").after("<span class='text-danger'>"+json['errors']['pin'][0]+"</span>");
			  
            }
            if(json['errors']['pin_confirmation']){
              $("#change_pin input[name='pin_confirmation']").addClass("is-invalid");
              $("#change_pin input[name='pin_confirmation']").after("<span class='text-danger'>"+json['errors']['password_confirmation'][0]+"</span>");
			  
            }
          }

          $('.secure_pin_check').prop('disabled',false);
          $('.secure_pin_check').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('.secure_pin_check').prop('disabled',false);
          $('.secure_pin_check').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
});
function CheckPasswordStrength(password,errClass) {
        //var password_strength = $(".errorred");
 
        //TextBox left blank.
        if (password.length == 0) {
            $("."+errClass).text("").css("color","red");
            return;
        }
 
        //Regular Expressions.
        var regex = new Array();
        regex.push("[A-Z]"); //Uppercase Alphabet.
        regex.push("[a-z]"); //Lowercase Alphabet.
        regex.push("[0-9]"); //Digit.
        regex.push("[$@$!%*#?&]"); //Special Character.
 
        var passed = 0;
 
        //Validate for each Regular Expression.
        for (var i = 0; i < regex.length; i++) {
            if (new RegExp(regex[i]).test(password)) {
                passed++;
            }
        }
 
        //Validate for length of Password.
        if (passed > 2 && password.length > 8) {
            passed++;
        }
		
        //Display status.
        var color = "";
        var strength = "";
        switch (passed) {
            case 0:
            case 1:
                strength = "Weak";
                color = "red";
                break;
            case 2:
                strength = "Good";
                color = "darkorange";
                break;
            case 3:
            case 4:
                strength = "Strong";
                color = "green";
                break;
            case 5:
                strength = "Very Strong";
                color = "darkgreen";
                break;
        }
        $("."+errClass).text(strength).css("color",color).show();
        //password_strength.style.color = color;
}

</script>
@endsection
 