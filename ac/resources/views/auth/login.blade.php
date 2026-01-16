@section('content')
<?php  $url = URL::to("/");   ?>
<?php $elec_details=get_election_history_details('AC'); ?>
<?php 
//dd(session()->all(),$errors);
//echo"<pre>";
//print_R(Request::url());
if(Session::has('DB_id')){
          $DB_id = Session::get('DB_id');
        }else{
          $DB_id = 0;
        }
        //dd($DB_id);
     ?>
   

     <style type="text/css">
       .inputGroup input:checked ~ label:after {
        background-color: #0d6efd;
       border-color: #ffc517;
      }
    .error{
      color: red;
      }
</style>

     <!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="{{ asset('theme/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('loginpage/css/custom.css') }}" rel="stylesheet">
  <link href="{{ asset('loginpage/css/responsive.css') }}" rel="stylesheet">
  


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
              <!-- <form action="" class="form"> -->

              
@if(session()->has("error"))

<div class="alert alert-{{session("message_type")}}">
  <p> {{session("error")}} </p>
</div>
@endif
@if (session('message'))
    <div class="alert">{{ Session::get('message', 'alert-info') }}</div>
@endif

@if($errors->any())
<div class="alert" id="main_sec_2" style="color:red"> {{$errors->first()}}</div>

@endif

              <div class="anchor-tab">
                  <h6>Select Election Type*</h6>
                  <ul>
                <li><a href="#" class="btn btn-outline-primary" id="pc" onclick="redirect_parliament();" role="button" aria-pressed="true"><span id="pctext">Parliamentary Election</span></a></li>
                    <li><a href="#" class="btn btn-outline-primary active" id="ac" onclick="redirect_assembaly();" role="button" aria-pressed="true"><span id="actext">Assembly Election</span></a></li> 
             <!--  <li><button type="button" class="btn btn-primary btn-lg" id="pc" onclick="redirect_parliament();">Parliamentary Election</button></li>
          <li><button type="button" class="btn btn-primary btn-lg"  id="ac" onclick="redirect_assembaly();">Assembly Election</button></li>  -->          
                    
                  </ul>
                </div>


                <input type="hidden" name="const" id="const">

               
               <form method="POST" action="{!! url('change-database') !!}" id="change_databsse"> 

      <input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
      
      
      <div class="custom-field select">
            <select name="database" class="form-control{{$errors->has('election') ? ' is-invalid' : '' }}" id="new" onchange="submit()">
                 <option value="" selected="selected">Select Election*</option> 
                @if(isset($elec_details))
                @foreach($elec_details as $details)
                @if($details->candidate_active_status == 1)
          <option value="{{$details->id}}" @if($DB_id == $details->id) selected="selected" @endif  >{{$details->description}}</option>
          
          @endif
          @endforeach
          @endif
        </select>
    @if ($errors->has('election'))
          <span class="invalid-feedback"><strong>{{ $errors->first('election') }}</strong></span>            
        @endif 
        <label for="" class="form-label">Select Election*</label>
        </div>
    </form>
     
 
               <form class="log-frm-area" method="POST" action="{{ url('/user-postlogin') }}" autocomplete='off' enctype="x-www-urlencoded" id="otpsend"  autocomplete="off">
               {{ csrf_field() }}
      
                  <span class="help-block"> 
                      <strong>{{ Session::get('log_message') }}</strong>
                  </span>
                  <div class="custom-field">
                  <input type="text" id="mobile" class="form-control" id=""  name="mobile" value="{{old('mobile')}}" autocomplete="off" maxlength="10" minlength="10" autofocus placeholder="" oncopy="return false"
       onpaste="return false" >

                    
                  <label for="" class="form-label">Enter Mobile No.</label>
                  @if ($errors->has('mobile'))
                   <span class="invalid-feedback"><strong>{{ $errors->first('mobile') }}</strong>
                      </span>
                  @endif 
                  <input type="hidden" name="election" id="election" value="{{$DB_id}}">
                </div>
                <div class="mb-4">
                  <div class="captcha">
                    <ul>
                      <li> <span id="captcha"><img id="refresh" src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default" style="height: 45;"></span></li>

                      
                                                          
                       <li><span id="btn-refresh" class="btn-refresh captcha-img"><a href="#">Reload Captcha <img src="{{ asset('loginpage/img/icons/reload-icon.png') }}" alt=""></span></a></span></li>
                      <!-- <li><span id="btn-refresh" class="btn btn-success btn-refresh captcha-img">Reload Captcha <img src="img/icons/reload-icon.png" alt=""></span></li> -->
                      <!-- <li><button type="button" data-refresh-config="default" id="btn-refresh" class="btn btn-success btn-refresh captcha-img"></i>Reload Captcha <img src="img/icons/reload-icon.png" alt=""></a></li> -->
                    </ul>
                     </div>
                </div>
                <div class="custom-field">
                  <input type="text"  id="captcha" class="form-control{{$errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha" placeholder="" autocomplete="off">
                  <label for="" class="form-label">Enter Captcha</label>
                </div>
                <div class="sumt-btn">
                  <!-- <button type="submit" class="btn btn-lg btn-primary w-100">Submit</button> -->
                  <input type="submit" class="btn btn-lg btn-primary w-100" value="Submit"/> 
                </div>
                 @if ($errors->has('captcha'))
          <span class="invalid-feedback">
              <strong>{{ __('messages.captcha_error') }}</strong>
          </span>
       @endif
       @if($errors->has('captcha'))
          @php
            $ErrorMessage['eventTime']= date('Y-m-d H:i:s');
            $ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
            $ErrorMessage['MobNo']= old('mobile') ?? '';
            $ErrorMessage['applicationType']= 'WebApp';
            $ErrorMessage['Module']= 'SUVIDHA';
            $ErrorMessage['TransectionType']= 'User';
            $ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
            $ErrorMessage['TransectionAction']= 'Captcha_Verify';
            $ErrorMessage['TransectionStatus']= 'FAILURE';
            $ErrorMessage['LogDescription']= 'Captcha Invalid';
            App\Helpers\LogNotification::LogInfo($ErrorMessage);
          @endphp
        @endif
 
  </form>
  <div class="mob-app">
              <ul class="wrap-down">
                 <li><img src="{{ asset('loginpage/img/apply.png') }}" alt=""></li>
                 
                 <li><a href="https://play.google.com/store/apps/details?id=suvidha.eci.gov.in.candidateapp&pli=1" target="_blank"><img src="{{ asset('loginpage/img/android.jpg') }}" alt=""></a></li>
                 <li><a href="https://apps.apple.com/app/suvidha-candidate/id6449588487" target="_blank"><img src="{{ asset('loginpage/img/app-ios.jpg') }}" alt=""></a></li>
              </ul>
            </div><!-- End of mob-app Div -->
            </div><!-- End of login-form Div -->
            <!-- <div class="copyright">
              <ul>
                <li>Copyright@ECI2024</li>
               
              </ul>
            </div> -->
            
          </div><!-- End of login-wrap Div -->
          <div class="appDownload">
              <ul class="wrap-down">
                 <li><img src="{{ asset('loginpage/img/apply.png') }}" alt=""></li>
                 
                 <li><a href="https://play.google.com/store/apps/details?id=suvidha.eci.gov.in.candidateapp&pli=1" target="_blank"><img src="{{ asset('loginpage/img/android.jpg') }}" alt=""></a></li>
                 <li><a  href="https://apps.apple.com/app/suvidha-candidate/id6449588487" target="_blank"><img src="{{ asset('loginpage/img/app-ios.jpg') }}" alt=""></a></li>
              </ul>
            </div><!-- End of appDownload Div -->
        </div><!-- End of shape-box div -->
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
    

</body>

</html>
<script type="text/javascript">
 
  function redirect_parliament(){
    

    window.location.href = "{{ config('public_config.pc_url_cand') }}";

    //$('#pc').toggleClass('clicked');
     var active =  document.getElementById('const').value='PC';

    if(active.length> 0){
    $('#pc').css({"background-color":'#0d6efd'});
    $('#pctext').css({"color":' #fdfefe'});
    $('#actext').css({"color":'#0d6efd'});
    $('#ac').css({"background-color":'#fdfefe'});
    //$('#ac').addClass('');
  }else{

  }
}
  function redirect_assembaly(){

   // window.location.href = "{{ config('public_config.ac_url_cand') }}";
var active = document.getElementById('const').value='AC';

if(active.length> 0){
    $('#ac').css({"background-color":'#0d6efd'});
     $('#actext').css({"color":'#fdfefe'});
     $('#pc').css({"background-color":'#fdfefe'});
     $('#pctext').css({"color":'#0d6efd'});
  }else{

    
  }
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
         url: "{!! url('refresh_captcha') !!}", 
    
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

      setTimeout(function() {
    $('#main_sec_2').fadeOut('fast');
}, 7000);
    </script>