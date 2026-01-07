<!DOCTYPE HTML>
      <html lang="{{ app()->getLocale() }}">
 <head>
    <?php $url=url('/'); ?> 
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="poppins" content="all,follow">
    <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
    <title>Candidate & Counting  Management System</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{ asset('admintheme/vendor/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="{{ asset('admintheme/vendor/font-awesome/css/font-awesome.min.css')}}">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="{{ asset('admintheme/css/fontastic.css')}}">
    <!-- Google fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <!-- jQuery Circle-->
   <link rel="stylesheet" href="{{ asset('admintheme/css/grasp_mobile_progress_circle-1.0.0.min.css')}}">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="{{ asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css')}}">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{ asset('admintheme/css/style.red.css')}}" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{ asset('admintheme/css/custom.css')}}">
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{ asset('admintheme/img/favicon.ico')}}">
    
  <!-- Scripts -->
 
</head>

<body>
 <main>
   <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
	 <img src="{{ asset('admintheme/img/vendor/background.png') }}" alt="" />
     </div>
			
			 
    <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">
  
  
   <div class="col-md-6 login-page "> 
  <figure class="evm-logo">
  <span style="margin: auto;">
   <img style="max-width: 300px;" src="{{ asset('admintheme/img/logo/eci-logo.png') }}" alt="" /><p>Election Commission of India</p> </span></figure> </div>
    <div class="col-md-6">
    <div class="login-right">
	
	
	@if (session('success_error'))
          <div class="alert alert-danger"> {{session('success_error') }}</div>
      @endif
    
      <form class="log-frm-area" method="POST" action=""  autocomplete='off' enctype="x-www-urlencoded">
	  <div id="otpinfo">
	  <div class="alert alert-success" id="otpinfomsg" style="display:none"> </div>
	  <div class="alert alert-warning" id="otpinfomsgerr" style="display:none"> </div>
	  
        <div class="form-group">
          <div class="inpt-btn">
		  <input type="hidden" name="uid"  id="uid" class="form-control"  value="{{$rec->id }}" readonly >
		<input type="text" name="email_id"  id="email_id" class="form-control" placeholder="username" value="{{$rec->officername }}" readonly >
				 <!--button type="submit">Send</button-->  
		  </div><!-- End Of inpt-btn Div -->	  
        </div>
		
		<div class="form-group">
		 <div class="inpt-btn">	
          <input type="text" class="form-control" placeholder="OTP" name="otp"  id="otp" maxlength="6" minlength="6">
		 	  
         </div><!-- End Of inpt-btn Div -->
		</div>
		
		<div class="form-group">
		 <div class="inpt-btn">	
		  <!--button type="submit">Submit</button--> 
		<input type="button" class="btn btn-primary" name="test" value="Verify" id="varifyotp">		  
         </div><!-- End Of inpt-btn Div -->
		</div>
		
		<div class="form-group">
		 <div class="inpt-btn">	
          <a href="#" class="achr-btn" id="resendotp">Resend OTP</a>
         </div><!-- End Of inpt-btn Div -->
		</div>
		</div>
		<div class="updateprofile" id="profileinfo" style="display:none; ">
			<div class="alert alert-success" id="sucemsg" style="display:none"> </div>
			<div class="alert alert-warning" id="errmsg" style="display:none"> </div>
			<div class="alert alert-warning" id="loginlink" style="display:none"> </div>
			
			<div class="form-group">
			     <input type="hidden" name="createpassurl" id="createpassurl" value="<?php echo $url.'/officer-login'; ?>">
                 <input type="hidden" name="userid" class="form-control" id="userid" value="" maxlength="80" value="">
                 <input type="test" name="email" class="form-control" id="email" value="" maxlength="80" value="{{$rec->officername }}" readonly >
			     <input type="hidden" class="form-control" placeholder="Name" name="name" class="form-control" id="name" readonly>
			</div>  
			<div class="form-group">
			  <input type="password" id="psw" name="password" placeholder="Password" class="form-control"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"  required>
			</div>
			<div class="form-group">
				<div id="message">
				 <div class="vrfy-msg">	
				  <!--<p id="letter" class="invalid">Password must contain only upper, lower letters,numbers and special characters(!$#%@) and atleast 8 characters</p>-->
				  <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
          <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
          <p id="number" class="invalid">A <b>number</b></p>
          <p id="length" class="invalid">Minimum <b>8 characters</b></p>
				 </div><!-- End Of vrfy-msg Div -->  
				</div>
			</div>
			<div class="form-group">
			  <input type="password" id="pswcnf" name="password_confirmation" class="form-control" placeholder="Confirm Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
			</div>
			<div class="form-group">
			<div id="cnfmessage">
			 <div class="vrfy-msg">		
			  <!--<p id="letter" class="invalid">Password must contain only upper, lower letters,numbers and special characters(!$#%@) and atleast 8 characters</p>-->
        <h6>Confirm Password must contain the following:</h6>
        <p id="cnfletter" class="invalid">A <b>lowercase</b> letter</p>
        <p id="cnfcapital" class="invalid">A <b>capital (uppercase)</b> letter</p>
        <p id="cnfnumber" class="invalid">A <b>number</b></p>
        <p id="cnflength" class="invalid">Minimum <b>8 characters</b></p>
			 </div><!-- End Of vrfy-msg Div -->  
			</div>
			</div>
			
			
			
			<div class="form-group">
			  <input type="password" id="pin" name="pin" class="form-control" placeholder="Pin" pattern="\d{6}" title="Must contain only 6 digit number" required>
			</div>
			
			<div class="form-group">
			<div id="pinmessage">
			 <div class="vrfy-msg">		
			  <!--<p id="letter" class="invalid">Password must contain only upper, lower letters,numbers and special characters(!$#%@) and atleast 8 characters</p>-->
        <h6>Pin must contain the following:</h6>
        <p id="pinletter" class="invalid">Please enter a six-digit number.</p>
			 </div><!-- End Of vrfy-msg Div -->  
			</div>
			</div>
			
			<div class="form-group">
			  <input type="password" id="pincnf" name="pincnf" class="form-control" placeholder="Confirm Pin" pattern="\d{6}" title="Must contain only 6 digit number" required>
			</div>
			
			<div class="form-group">
			<div id="cnfpinmessage">
			 <div class="vrfy-msg">		
			  <!--<p id="letter" class="invalid">Password must contain only upper, lower letters,numbers and special characters(!$#%@) and atleast 8 characters</p>-->
        <h6>Confirm Pin must contain the following:</h6>
        <p id="cnfpinletter" class="invalid">Please enter a six-digit number.</p>
			 </div><!-- End Of vrfy-msg Div -->  
			</div>
			</div>
			

                   
			
			
			<div class="actn-btn"> 
	        	<input type="button" name="test" value="Submit" id="updateprofile" class="btn btn-primary" >
			</div><!-- End Of actn-btn Div -->
		</div>
      </form>
    </div>    
    </div>    
    </div>
        </div>
   </section>
  
  </main>
    <script src="{{ asset('admintheme/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/popper.js/umd/popper.min.js') }}"> </script>
    <script src="{{ asset('admintheme/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admintheme/js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/jquery.cookie/jquery.cookie.js') }}"> </script>
    <script src="{{ asset('admintheme/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <!-- Main File-->
    <script src="{{ asset('admintheme/js/front.js') }}"></script>

<script>
$(document).ready(function(){
    var username = $( "#email_id" ).val();  
    $("#varifyotp").click(function(){
		var uid = $( "#uid" ).val();

		uid = $.trim(uid);
		var otp = $( "#otp" ).val();
		
		 	if(otp) {
		       $.ajax({
			    url:'<?=$url;?>/otpvarify/'+uid+'/'+otp,
                type: "GET",
                 dataType: "html",
                success:function(data) {  
				var obj = JSON.parse(data);    
				if(data==1)
				{
						$('#sucemsg').show();	
						$( "#sucemsg" ).html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong> OTP Verified. Please Create Password and Pin</strong>  ' );	
						$('#profileinfo').show();
						$('#otpinfo').hide();
						$('#otpinfomsgerr').hide();
						$('#otpinfomsg').hide();
						$('#errmsg').hide();
						$('#loginlink').hide();
						$( "#userid" ).val(uid);
				 		$( "#email" ).val(username);
				 }else{
					 	$( "#otpinfomsgerr" ).html('Please Enter Correct OTP </strong> ' );	
						$( "#otp" ).val('');
						$( "#otp" ).focus();
						$('#otpinfomsgerr').show();					
						$('#otpinfomsg').hide();					
					}
											
                }
            });
			
            }else{
			$( "#otpinfomsgerr" ).html('Please Enter OTP </strong> ' );	
			$('#otpinfomsgerr').show();
			$('#otpinfomsg').hide();
			 $( "#otp" ).focus();
			
			//alert('Please Enter OTP');
			}	
    });
	////////////////////////update Profile/////////////////////////////////////////
	$("#updateprofile").click(function(){
	     
		var uid = $( "#uid" ).val();
		var password = $( "#psw" ).val();
		var name = $( "#name" ).val();
		var password_confirmation = $( "#pswcnf" ).val();
		var pin = $( "#pin" ).val();
		var cnfpin = $( "#pincnf" ).val();
		var currentURL = $( "#createpassurl" ).val();
		//var regex = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/;
		
		if(password!=password_confirmation)
		{    

		alert('Password and Confirm Password do not match.');
		$( "#errmsg" ).html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Password and Confirm Password do not match.</strong>  ' );
		$('#errmsg').show();
		$('#sucemsg').hide();
		
	
		}else if (pin!=cnfpin){
			
		alert('Pin and Confirm Pin do not match.');
		$( "#errmsg" ).html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Pin and Confirm Pin do not match.</strong>  ' );
		$('#errmsg').show();
		$('#sucemsg').hide();
		
		} else {			
		
		 $.ajax({
               url: '<?=$url;?>/updateuserpass/'+uid+'/'+password+'/'+pin,
                type: "GET",
                 dataType: "html",
                success:function(data) {	
				 alert(data);
				 
				 
				 
				 
				$('#loginlink').show();
				$('#errmsg').hide();
				$( "#loginlink" ).html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Password Created Sucessfully  Please <a href="'+currentURL+'">click</a> on the link to login in system </strong>  ' );	
				$('.form-group').hide();
				$('.actn-btn').hide();
				$('#sucemsg').hide();
			 
											
                }
            });
			
		}	
		
		
           
    });
	
	$("#resendotp").click(function(){
		$('#resendotp').hide();
		var uid = $( "#uid" ).val();
		uid = $.trim(uid);
	
		if(uid) {
		       $.ajax({
               url: '<?=$url;?>/resendotp/'+uid,
                type: "GET",
                 dataType: "html",
                success:function(data) {
				 
				var obj = JSON.parse(data);
				if(data==1)
				{
					$('#resendotp').show();
					$("#otpinfomsg" ).html('OTP has been sent to your registered mobile number.' );	
					$('#otpinfomsg').show(); 
					$('#otpinfomsgerr').hide(); 
				}else{
					$('#resendotp').show();
					$('#otpinfomsgerr').show();
					$("#otpinfomsgerr" ).html('Mobile no is not updated  ' );	
					$('#otpinfomsg').hide(); 
				}
				
											
                }
            });
			
            }
		
	  });
	
	
});
</script>




<script>
document.getElementById("pin").disabled = true;
	  document.getElementById("pincnf").disabled = true;
var myInput = document.getElementById("psw");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");
 document.getElementById("cnfmessage").style.display = "none";
 document.getElementById("message").style.display = "none";
 document.getElementById("pinmessage").style.display = "none";
 document.getElementById("cnfpinmessage").style.display = "none";
 
// When the user clicks on the password field, show the message box
myInput.onfocus = function() {
    document.getElementById("message").style.display = "block";
   
}

// When the user clicks outside of the password field, hide the message box
myInput.onblur = function() {
    document.getElementById("message").style.display = "none";
}


var pinInput = document.getElementById("pin");



// When the user clicks on the password field, show the message box
pinInput.onfocus = function() {
    document.getElementById("pinmessage").style.display = "block";
   
}

// When the user clicks outside of the password field, hide the message box
pinInput.onblur = function() {
    document.getElementById("pinmessage").style.display = "none";
}


var cnfpinInput = document.getElementById("pincnf");



// When the user clicks on the password field, show the message box
cnfpinInput.onfocus = function() {
    document.getElementById("cnfpinmessage").style.display = "block";
   
}

// When the user clicks outside of the password field, hide the message box
cnfpinInput.onblur = function() {
    document.getElementById("cnfpinmessage").style.display = "none";
}



// When the user starts to type something inside the password field
myInput.onkeyup = function() {
	document.getElementById("updateprofile").style.display = "none";
//alert("ok");
  // Validate lowercase letters
  var lowerCaseLetters = /[a-z]/g;
  if(myInput.value.match(lowerCaseLetters)) {  
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");

  }
  
  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput.value.match(upperCaseLetters)) {  
    capital.classList.remove("invalid");
    capital.classList.add("valid");

  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");

  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput.value.match(numbers)) {  
    number.classList.remove("invalid");
    number.classList.add("valid");

  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");

  }
  
  // Validate length
  if(myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
	
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
	
  }
  
  if(myInput.value.match(lowerCaseLetters) && myInput.value.match(upperCaseLetters) && myInput.value.match(numbers)  && myInput.value.length >= 8)
  {
	  document.getElementById("updateprofile").style.display = "block";
	  document.getElementById("pin").disabled = false;
	  document.getElementById("pincnf").disabled = false;
  }else{
	  document.getElementById("updateprofile").style.display = "none";
	  document.getElementById("pin").disabled = true;
	  document.getElementById("pincnf").disabled = true;
	}
  
}


//////////////////////////////////////test/////////////////////////
var cnfmyInput = document.getElementById("pswcnf");
var cnfletter = document.getElementById("cnfletter");
var cnfcapital = document.getElementById("cnfcapital");
var cnfnumber = document.getElementById("cnfnumber");
var cnflength = document.getElementById("cnflength");

// When the user clicks on the password field, show the cnfmessage box
cnfmyInput.onfocus = function() {
    document.getElementById("cnfmessage").style.display = "block";
}

// When the user clicks outside of the password field, hide the cnfmessage box
cnfmyInput.onblur = function() {
    document.getElementById("cnfmessage").style.display = "none";
}

// When the user starts to type something inside the password field
cnfmyInput.onkeyup = function() {
	document.getElementById("updateprofile").style.display = "none";
//alert("ok");
  // Validate lowercase cnfletters
  var cnflowerCasecnfletters = /[a-z]/g;
  if(cnfmyInput.value.match(cnflowerCasecnfletters)) {  
    cnfletter.classList.remove("invalid");
    cnfletter.classList.add("valid");
  } else {
    cnfletter.classList.remove("valid");
    cnfletter.classList.add("invalid");

  }
  
  // Validate cnfcapital cnfletters
  var cnfupperCasecnfletters = /[A-Z]/g;
  if(cnfmyInput.value.match(cnfupperCasecnfletters)) {  
    cnfcapital.classList.remove("invalid");
    cnfcapital.classList.add("valid");

  } else {
    cnfcapital.classList.remove("valid");
    cnfcapital.classList.add("invalid");

  }

  // Validate cnfnumbers
  var cnfnumbers = /[0-9]/g;
  if(cnfmyInput.value.match(cnfnumbers)) {  
    cnfnumber.classList.remove("invalid");
    cnfnumber.classList.add("valid");

  } else {
    cnfnumber.classList.remove("valid");
    cnfnumber.classList.add("invalid");

  }
  
  // Validate cnflength
  if(cnfmyInput.value.length >= 8) {
    cnflength.classList.remove("invalid");
    cnflength.classList.add("valid");
	
  } else {
    cnflength.classList.remove("valid");
    cnflength.classList.add("invalid");
	
  }
  
  if(cnfmyInput.value.match(cnflowerCasecnfletters) && cnfmyInput.value.match(cnfupperCasecnfletters) && cnfmyInput.value.match(cnfnumbers)  && cnfmyInput.value.length >= 8)
  {
	  document.getElementById("updateprofile").style.display = "block";
	  document.getElementById("pin").disabled = false;
	  document.getElementById("pincnf").disabled = false;
  }else{
	  document.getElementById("updateprofile").style.display = "none";
	  document.getElementById("pin").disabled = true;
	  document.getElementById("pincnf").disabled = true;
	}
  
}



// When the user starts to type something inside the pin field
pinInput.onkeyup = function() {
	document.getElementById("updateprofile").style.display = "none";
//alert("ok");


  // Validate numbers
  var numbers = /[0-9]/g;
  if(pinInput.value.match(numbers)) {  
    number.classList.remove("invalid");
    number.classList.add("valid");

  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");

  }
  
  // Validate length
  if(pinInput.value.length >= 6) {
    length.classList.remove("invalid");
    length.classList.add("valid");
	
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
	
  }
  
  if( pinInput.value.match(numbers)  && pinInput.value.length == 6)
  {
	  document.getElementById("updateprofile").style.display = "block";
  }else{
	  document.getElementById("updateprofile").style.display = "none";
	}
  
}

// When the user starts to type something inside the pin cnf field
cnfpinInput.onkeyup = function() {
	document.getElementById("updateprofile").style.display = "none";
//alert("ok");


  // Validate numbers
  var numbers = /[0-9]/g;
  if(cnfpinInput.value.match(numbers)) {  
    number.classList.remove("invalid");
    number.classList.add("valid");

  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");

  }
  
  // Validate length
  if(cnfpinInput.value.length >= 6) {
    length.classList.remove("invalid");
    length.classList.add("valid");
	
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
	
  }
  
  if( cnfpinInput.value.match(numbers)  && cnfpinInput.value.length == 6)
  {
	  document.getElementById("updateprofile").style.display = "block";
  }else{
	  document.getElementById("updateprofile").style.display = "none";
	}
  
}

</script>
 
  </body>
</html>