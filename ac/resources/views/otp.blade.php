




<?php $url=url('/'); 

$checkpass=checkpassword($mobile);

//dd($checkpass->verify_otp,$checkpass->password);
if(!empty($checkpass))
    {$checkis=1;}
else
    {$checkis=0;}



?> 

@if($errors->any())
    {{ implode('', $errors->all('<div style="align-content: center;">:message</div>')) }}
@endif
     <style type="text/css">
       .inputGroup input:checked ~ label:after {
    background-color: #ffc517;
    border-color: #ffc517;

     }
     .error{
    color:red;
     }
     </style>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
   <link href="{{ asset('theme/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('loginpage/css/custom.css') }}" rel="stylesheet">
  <link href="{{ asset('loginpage/css/responsive.css') }}" rel="stylesheet">
  

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('loginpage/fonts/FontAwesome/FontAwesome.6.2.1.css') }}" />
  <title>Suvidha - Login</title>
</head>

<body class="login-banner">
  <main class="main">
    <section class="login-box">
      <div class="container-fluid">
        <div class="shape-box">
          <div class="left-shape">
            <img src="{{ asset('loginpage/img/sveep-logo-ii.png') }}" alt="">
          </div>
          <div class="login-wrap">
            <div class="login-form">
              <div class="logo-rti">
                <img src="{{ asset('loginpage/img/eci-logo.png') }}" alt="">
                <img src="{{ asset('loginpage/img/sveep-logo-ii.png') }}" alt="">
              </div>
              <!-- <h2>OTP Verification !</h2> -->
              <!-- <form action="" class="form"> -->
                  @php 
             
              $maskedMobile = substr_replace($mobile, str_repeat('*', strlen($mobile) - 4), 0, strlen($mobile) - 4);
                 
               @endphp
                 @if($checkpass->verify_otp==1 && empty($checkpass->password))

                  <div class="edit-otp">
                <ul>
                  <li>Create Password For Login</li>
                  <li>{{$maskedMobile}} or <a href="{{url('/login')}}">Edit <img src="img/icons/edit.png" alt=""></a></li>
                </ul>

              </div><!-- End of edit-otp Div -->

                 @else
                   
                    <!-- <div class="edit-otp">
                <ul>
                  <li>Enter OTP we have send on </li>
                  <li>{{$mobile}} or <a href="{{url('/login')}}">Edit <img src="img/icons/edit.png" alt=""></a></li>
                </ul>

              </div> --><!-- End of edit-otp Div -->

                 @endif
                <div id="messageis" style="text-align: center;"></div>    
                <div class="form-inline">
     @if (session('error'))
           <div class="alert alert-danger" id="idsessiond" style="text-align: center;">{{ session('error') }}</div>
            <script type="text/javascript">
            setTimeout(function() {
                    $('#idsessiond').removeClass('alert alert-danger').text('');
                     }, 4000);
            </script>
      @endif
      
      @if($errors->any())
        <div class="alert alert-info" id="idsessionoinfo" style="text-align: center;">{{$errors->first()}}</div>
        <script type="text/javascript">
            setTimeout(function() {
                    $('#idsessionoinfo').removeClass('alert alert-info').text('');
                     }, 4000);
            </script>
      @endif

      @if (session('success'))

           <div class="alert alert-info success" id="idsessions" style="text-align: center;">{{ session('success') }}</div>
           <script type="text/javascript">
            setTimeout(function() {
                    $('#idsessions').removeClass('alert alert-info success').text('');
                     }, 4000);
            </script>
      @endif

    </div>
    
    <div  id="otpsend" style="text-align: center;"></div> 
     
     <div id="attempts" style="text-align: center;"></div>  

          <input id="mobile" type="hidden" class="form-control col-md-9" name="mobile" value="{{$mobile}}"  placeholder="Mobile Number" maxlength="10" minlength="10" readonly="readonly">
          
        
                @if($checkpass->verify_otp==1 && !empty($checkpass->password))
                <div class="edit-otp">
                <ul>
                  <li>Enter OTP and Password </li>
                  <li>{{$maskedMobile}} or <a href="{{url('/login')}}">Edit <img src="img/icons/edit.png" alt=""></a></li>
                </ul>

              <form class="log-frm-area" method="POST" action="{{ url('customlogin') }}" autocomplete='off' enctype="x-www-urlencoded" id="loginval"  autocomplete="off" >
      {{ csrf_field() }}
       <!--MOBILE NUMBERT FIELD STARTS-->
       <div class=" form-inline{{ $errors->has('mobile') ? ' has-error' : '' }}">
          
          <input id="mobile" type="hidden" class="form-control col-md-9" name="mobile" value="{{$mobile}}"  placeholder="Enter Mobile No." maxlength="10" minlength="10" readonly="readonly">
          
          
          @if ($errors->has('mobile'))
                <span class="invalid-feedback"> <strong>{{ $errors->first('mobile') }}</strong>   </span>
          @endif
        </div>

          <div class="form-group{{ $errors->has('otp') ? ' has-error' : '' }}">
               <div class="custom-field">
                  <input id="otp" type="password" class="form-control w-60" name="otp" value="{{ old('otp') }}"  placeholder="Mobile Otp" maxlength="6" minlength="6" autocomplete="off" autofocus>&nbsp;
                    <label for="" class="form-label">Enter OTP.</label>
                      <a href="#" id="fgpassword" style="float: right;" class="resendotpform" >Resend OTP <img src="{{ asset('loginpage/img/icons/reload-icon.png')}}" alt="" height="16px"></a>

                    </div>
                    @if ($errors->has('otp'))
                            <span class="help-block">
                           <strong>{{ $errors->first('otp') }}</strong>
                       </span> @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <div class="custom-field">
                  <input type="password" id="password" class="form-control w-60"   name="password" value="{{old('password')}}" autocomplete="off"  autofocus placeholder="Password">
                  <label for="" class="form-label">Enter Password.</label>
                  <a href="#" id="fgpassword" style="float: right;" onclick="return fgpassword();">Reset Password?</a>
                    </div>
                            @if ($errors->has('password'))
                            <span class="help-block">
                           <strong style="color:red">{{ $errors->first('password') }}</strong>
                       </span>

                         @endif

                </div>
                <input type="submit" class="btn btn-lg btn-primary w-100" id="btnsub" value="Submit"> 

          </form>
     
      @elseif($checkpass->verify_otp==0)
              <h2>OTP Verification !</h2> 
                     <div class="edit-otp">
                <ul>
                  <li>Enter OTP we have send on </li>
                  <li>{{$maskedMobile}} or <a href="{{url('/login')}}">Edit <img src="img/icons/edit.png" alt=""></a></li>
                </ul>

              </div>
       
                <div class="form-group{{ $errors->has('otp') ? ' has-error' : '' }}">

                <div class="otp-container digit-group d-flex justify-content-center gap-2 my-3">
                    @for ($i = 0; $i < 6; $i++)
                <input
                       type="text"
                            name="otp[]"
                            maxlength="1"
                            class="otp-input"
                            id="otp-{{ $i }}"
                            data-index="{{ $i }}"
                            autocomplete="off"
                    />
                @endfor
                 <input type="hidden" name="otp_full" id="otp-hidden" required>
                 </div>

               <!-- <div class="custom-field">
                   <input id="otp" type="password" class="form-control w-60" name="otp" value="{{ old('otp') }}"  placeholder="" maxlength="6" minlength="6" autocomplete="off">&nbsp; 
                    <label for="" class="form-label">Enter OTP.</label> 
                  </div> -->




                <div class="otp-exp">
                  <ul>
                    <li>OTP expires in <span>2 min</span> </li>
                    <li><a href="#" class="resendotpform">Resend OTP <img src="img/icons/reload-icon.png" alt=""></a></li>
                  </ul>
                </div>

            </div>
        
        @elseif($checkpass->verify_otp==1 && empty($checkpass->password))
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <div class="custom-field">
                  <input type="password" id="password" class="form-control w-60" id=""  name="password" value="{{old('password')}}" autocomplete="off"  autofocus placeholder="" minlength="8" >
                    <label for="" class="form-label">Password.</label>

                    </div>
                            @if ($errors->has('password'))
                            <span class="help-block">
                           <strong>{{ $errors->first('password') }}</strong>
                       </span>

                         @endif
                </div>
                <div class="form-group{{ $errors->has('cpassword') ? ' has-error' : '' }}">
                    <div class="custom-field">
                  <input type="password" id="cpassword" class="form-control w-60"   name="cpassword" value="{{old('cpassword')}}" autocomplete="off"  autofocus placeholder="" minlength="8" >
                   <label for="" class="form-label">Confirm Password.</label>
                    </div>
                     @if ($errors->has('cpassword'))
                            <span class="help-block">
                           <strong>{{ $errors->first('cpassword') }}</strong>
                       </span>

                         @endif
                </div> 
                <p style="color:blue">Note: Your password must be have at </p>
                <ul>
                 <li>8 characters long </li>
                 <li>1 uppercase & 1 lowercase character </li>
                 <li>1 number & 1 Special character</li>
                 <ul>   
                
                @endif
                <div class="otp-exp">
                 <!--  <ul>
                    <li>OTP expires in <span>09:48 min</span> </li>
                    <li><a href="">Resend OTP <img src="img/icons/reload-icon.png" alt=""></a></li>
                  </ul> -->


                </div>
                <div class="sumt-btn">
                     @if($checkpass->verify_otp==0)
                        <span class="btn btn-lg btn-primary w-100 verifyotp">Verify OTP</span>&nbsp&nbsp
                      <!-- <span class="btn btn-primary mt-3 resendotpform">Resend OTP</span>  -->
                       @elseif($checkpass->verify_otp==1 && empty($checkpass->password))
                  <button type="submit" class="btn btn-lg btn-primary w-100 setpassword">Generate Password</button>
                  @else
                    
                  @endif
                </div>
            
    
     
            
            </div><!-- End of login-form Div -->
            <div class="copyright">
              <ul>
                <li>Copyright@ECI2024</li>
                <!-- <li><a href="#">Privacy Policy</a></li> -->
              </ul>
            </div>
          </div><!-- End of login-wrap Div -->
        </div>
      </div>
    </section>
  </main>
  <footer class="stickyFooter">

  </footer>
  <script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
<!-- Validation  JavaScript -->
<!--**********DCO FORM VALIDATION STARTS**********-->
    <script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
    <!--**********DCO FORM VALIDATIONS SCRIPT**********-->
    <script src="{{ asset('formvalidations/loginformvalidations.js') }}"></script>
    <script src="{{ asset('theme/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
     
<script type="text/javascript" src="{{ asset('appoinment/js/crypto-js.min.js') }}"></script> 
</body>


<script type="text/javascript">
  function redirect_parliament(){
    window.location.href = "{{ config('public_config.pc_url_cand') }}";
  }

 

</script>
 
    <script type="text/javascript">
function dynamic_select(url){
        window.location = url; // redirect
   } 
$(function(){
         var db = $("#new :selected").val();
    if(db == '')
    {
        $('#logincond').css('display','none');
        $('#logincondrow').html('<div class="alert alert-warning mb-4" role="alert">'+
        '<i class="fa fa-bullhorn"></i>&nbsp; &nbsp; <b class="text-center">No elections are scheduled</b>.</div>');
    }
    else
    {
        $('#logincond').css('display','block');
    }
     
   
    $('select#new').change(function () {
        var db1 = $(this).val();
         if(db1 == '')
        {
            $('#logincond').css('display','none');
            $('#logincondrow').html('<div class="alert alert-warning mb-4" role="alert">'+
      '<i class="fa fa-bullhorn"></i>&nbsp; &nbsp; <b class="text-center">No elections are scheduled</b>.</div>');
        }
        else
        {
            $('#logincond').css('display','block');
            //$('#logincondrow').html('');
        }
    });
    });
 /*function refereshcaptch(){    
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/refresh_captcha",           
       success: function (data) {
         jQuery("#captcha").html(data.captcha);
       },
       error: function (data, textStatus, errorThrown) {
             //do something

       }
           });
    }*/

//CAPTCHA REFRESH STARTS HERE
 $('.captcha-img').on('click', function () {
    var captcha = $(this);
    var config = captcha.data('refresh-config');
    $.ajax({
        method: 'GET',
        url: '/refresh_captcha',
    }).done(function (response) {
        
        // $("#refresh").html(response.captcha);
        $('#refresh').prop('src', response);
    });
});
//CAPTCHA REFRESH ENDS HERE
</script>

<script type="text/javascript">
      function change_database(){
        $('#change_databsse').submit();
      }
    </script>
    <script type="text/javascript">
//RESEND OTP LOGIN STARTS


$(document).on("click", ".resendotpform", function () {    

    var mobile = $("#mobile").val();


        $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
           // url: APP_URL + '/resendotp',    
             url: "{!! url('resendotp') !!}",            
            type: 'POST',
            data: 'mobile='+btoa(mobile),
            success: function (data) {
                if(data == 1){
                    $('#otpsend').hide();
                    $('#attempts').addClass('alert alert-info').text('Reached maximum otp attempts. Request for new OTP.');
                    setTimeout(function() {
                    $('#attempts').removeClass('alert alert-info').text('');
                     }, 4000);

                }else if(data == 3){
                    $('#otpsend').hide();
                    $('.success').hide();
                    $('#attempts').addClass('alert alert-info').text('Can Send only 1 OTP per minute.');
                    setTimeout(function() {
                    $('#attempts').removeClass('alert alert-info').text('');
                     }, 4000);

                }else{
                  $('#attempts').hide();
                  $('#otpsend').addClass('alert alert-info').text('OTP successfully Send.');
                   setTimeout(function() {
                    $('#otpsend').removeClass('alert alert-info').text('');
                   }, 4000);
                         //$('#attempts').hide();
                }
                
            }
        });
    
});
//RESEND OTP LOGIN ENDS 

//Verify OTP STARTS


$(document).on("click", ".verifyotp", function () {

    let firstVal  = $("#otp-0").val();   // get first box
    let secondVal = $("#otp-1").val();
    let thirdVal  = $("#otp-2").val();
    let fourthVal = $("#otp-3").val();
    let fifthVal  = $("#otp-4").val();
    let sixVal    = $("#otp-5").val();
    let otp2      = firstVal + secondVal + thirdVal + fourthVal + fifthVal + sixVal;


    var mobile = $("#mobile").val();
   // var otp = $("#otp").val();
    var otp = otp2;
     //var mydata=[];


     

    
    if(mobile.length !=10 || otp.length != 6)
    {
         $('#otpsend').addClass('alert alert-danger').text('Please Check Input');
         setTimeout(function() {
    $('#otpsend').removeClass('alert alert-danger').text('');
}, 4000);
        return false;
    }

        $.ajax({
            headers: {

            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{!! url('verifyotpfirst') !!}",
                            
            type: 'POST',
            
            //data: 'mobile='+encryptyData(mobile)+'&otp='+encryptyData(otp),
            data: 'mobile='+btoa(mobile)+'&otp='+btoa(otp),

            success: function (data) {
                if(data == 1){
                    $('#otpsend').hide();
                    $('#attempts').addClass('alert alert-success').text('OTP successfully verified');
                     window.location.reload();
                }else if(data == 3){
                    $('#otpsend').hide();
                    $('.success').hide();
                    $('#attempts').addClass('alert alert-info').text('Can Send only 1 OTP per minute.');
                    setTimeout(function() {
                     $('#attempts').removeClass('alert alert-info').text('');
                      }, 4000);

                }else if(data == 4){
                    $('#otpsend').hide();
                    $('.success').hide();
                    $('#messageis').addClass('alert alert-danger').text('Your OTP is Expired!');


                    setTimeout(function(){window.location="{{url('/login')}}";}, 3000) 

                }else{
                  $('#attempts').hide();
                  $('#otpsend').addClass('alert alert-danger').text('OTP Mismatch.');
                   setTimeout(function() {
                     $('#otpsend').removeClass('alert alert-danger').text('');
                      }, 4000);
                         //$('#attempts').hide();
                }
                
            }
        });
    
});
//Verify OTP LOGIN ENDS  


$(document).on("click", ".setpassword", function () {    

    var mobile = $("#mobile").val();
    var password = $("#password").val()
    var cpassword = $("#cpassword").val();
    if(mobile.length==0 || password.length==0 ||  cpassword.length==0)
    {
         $('#otpsend').addClass('alert alert-danger').text('Input Field Should Not be Empty');
         setTimeout(function() {
                     $('#otpsend').removeClass('alert alert-danger').text('');
                      }, 4000);
        return false;
    }
    if(password!=cpassword)
    {
         $('#otpsend').addClass('alert alert-danger').text('Passwords and confirm password Mismatch');
         setTimeout(function() {
                     $('#otpsend').removeClass('alert alert-danger').text('');
                      }, 4000);
        return false;
    }

        $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
             url: "{!! url('setpassword') !!}",
                            
            type: 'POST',
            data: 'mobile='+btoa(mobile)+'&password='+btoa(password)+'&cpassword='+btoa(cpassword) ,
            success: function (data) {
                if(data == 1){
                    $('#otpsend').hide();
                    $('#messageis').addClass('alert alert-success').text('Your password created successfully .');
                    
                  setTimeout(function(){window.location="{{url('/login')}}";}, 4000) 
                   
                
                }else if(data == 3){
                    $('#otpsend').hide();
                    $('.success').hide();
                    $('#attempts').addClass('alert alert-danger').text('Password Format is Wrong');
                     setTimeout(function() {
                     $('#attempts').removeClass('alert alert-danger').text('');
                      }, 4000);
                
                }else if(data == 4){
                    $('#otpsend').hide();
                    $('.success').hide();
                    $('#attempts').addClass('alert alert-danger').text('Password Mismatch');
                    setTimeout(function() {
                     $('#attempts').removeClass('alert alert-danger').text('');
                      }, 4000);
                }else{
                  $('#attempts').hide();
                  $('#otpsend').addClass('alert alert-info').text('Please Check Input Value');
                  setTimeout(function() {
                     $('#otpsend').removeClass('alert alert-info').text('');
                      }, 4000);

                         //$('#attempts').hide();
                }
                
            }
        });
    
});

         function fgpassword()
          {

            var mobile=$("#mobile").val();
            if(mobile.length!=10)
            {
                 $('#otpsend').addClass('alert alert-danger').text('Please Check Mobile Number');
                 setTimeout(function() {
                             $('#otpsend').removeClass('alert alert-danger').text('');
                              }, 4000);
                return false;
            }
              

               $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
             url: "{!! url('fgpassword') !!}",
                            
            type: 'POST',
              data: 'mobile='+btoa(mobile) ,
            success: function (data) {
                if(data == 1){
                    $('#otpsend').hide();
                    $('#attempts').hide();
                 // $("#messageis").html("Reset Password Successfully");
                // $("#attempts").fadeOut(2000);
                     $('#messageis').addClass('alert alert-success').text('To reset your password, pls go to login page and verify OTP and set your new password');
                      $('#btnsub').hide();
                     // setTimeout(function() {
                     // $('#messageis').removeClass('alert alert-success').text('');
                     //  }, 3000000);
                     setTimeout(function(){window.location="{{url('/login')}}";}, 5000) 
                  //  window.location.href = "{{url('/login')}}";
             

                         
                }else{
                  $('#attempts').hide();
                  $('#otpsend').addClass('alert alert-danger').text('Please Check Mobile Number');
                  setTimeout(function() {
                     $('#otpsend').removeClass('alert alert-info').text('');
                      }, 4000);

                         //$('#attempts').hide();
                }
                
            }
        });
    



          }




$("#password,#cpassword").keypress(function(e) {
         //var regex = new RegExp("^[a-zA-Z0-9 ,.#_-]+$");
          var regex = new RegExp("^[a-zA-Z0-9@&!*^|/,.#_$]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
   });
$("#otp").keypress(function(e) {
         //var regex = new RegExp("^[a-zA-Z0-9 ,.#_-]+$");
          var regex = new RegExp("^[0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
   });






  function encryptyData(data) 
     {
    const key = 'AwdL2cXoGHtULolvWERioSDF';
    let k = CryptoJS.enc.Utf8.parse(key);
    encryptedAES = CryptoJS.AES.encrypt(data, k, { mode: CryptoJS.mode.ECB });
    return encryptedAES.toString();
    
     }
</script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll(".otp-input");
    const hiddenOtp = document.getElementById("otp-hidden");
    const form = document.querySelector("form");

    // Auto-move cursor 123434
    inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            input.value = input.value.replace(/[^0-9]/g, "");
               
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });



    // On form submit, collect OTP
    form.addEventListener("submit", function (e) {
        let otp = "";
        inputs.forEach(input => otp += input.value);
        hiddenOtp.value = otp;

        if (otp.length < 6) {
            alert("dfd");
            e.preventDefault();
            alert("Please enter full 6-digit OTP");
        }
    });
});


</script>

<style type="text/css">
    .digit-group input {
    width: 60px;
    height: 60px;
    background-color: lighten($BaseBG, 5%);
    border: none;
    line-height: 50px;
    text-align: center;
    font-size: 24px;
    font-family: 'Raleway', sans-serif;
    font-weight: 800;
    color: black;
    margin: 0 2px;
    border-radius: 18px;
    border: 3px solid #293886;
  }

.prompt {
  margin-bottom: 20px;
  font-size: 20px;
  color: white;
}

::-webkit-input-placeholder {
  /* Edge */
  font-weight: 800;
  color: #9c9a9a;
}

:-ms-input-placeholder {
  /* Internet Explorer */
  font-weight: 800;
  color: #9c9a9a;
}

::placeholder {
  font-weight: 900;
  color: #9c9a9a;
}

.otp-input {
    width: 50px;
    height: 55px;
    font-size: 22px;
    text-align: center;
    border-radius: 8px;
    border: 2px solid #ccc;
    font-weight: bold;
    transition: all 0.2s;
}

.otp-input:focus {
    border-color: #293886;
    box-shadow: 0 0 5px rgba(41, 56, 134, 0.5);
    outline: none;
}


</style>