<?php
  $setting = \App\models\Admin\SettingModel::get_setting_cache(); 
  $is_two_step = 0;
  if(!empty($setting['two_step'])){
    $is_two_step = $setting['two_step'];
  }

  $auto_logout_after = 0;
  if(!empty($setting['auto_logout_after']) && $setting['auto_logout_after']>0){
    $auto_logout_after = $setting['auto_logout_after'];
  }

?>

<?php if($auto_logout_after > 0 && Auth::user() && $user_data->role_id != 7){ ?>
<div class="modal fade" id="auto_logout_after" tabindex="-1" role="dialog" aria-labelledby="auto_logout_after" aria-hidden="false" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="two_step_form" action="return false;">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
      <div class="modal-header">
        Auto Logout Warning...
      </div>
      <div class="modal-body">
        <div class="form-group row text-center">
          <p id="reset_logout_countdown_div" class="reset_logout_countdown_div text-center text-danger" style="width: 100%;"></p>
        </div>
        <div class="form-group text-center">
          <button type="button" class="btn btn-primary stay_login_extend" id="auto_logout_extend">Stay Login</button>
          <button type="button" class="btn" id="auto_logout_cancel">Logout</button>
        </div>
       
      </div>
      <div class="modal-footer">
        
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
var auto_logout_after = "{{$auto_logout_after}}";
var is_logout = 0;
var timeoutHandle;
function countdown(minutes,stat) {
    var seconds = 60;
    var mins = minutes;
     
    if(getCookie("minutes") && getCookie("seconds") && stat){
      var seconds = getCookie("seconds");
      var mins = getCookie("minutes");
    }
   
    function tick() {
  
        var counter = document.getElementById("reset_logout_countdown_div");
        setCookie("minutes",mins,10)
        setCookie("seconds",seconds,10)
        var current_minutes = mins-1
        seconds--;
        if($('#reset_logout_countdown_div').length>0){
          $('#reset_logout_countdown_div').html("Auto Logout in " + current_minutes.toString() + ":" + (seconds < 10 ? "0" : "") + String(seconds));
        }
		//save the time in cookie
        if( seconds > 0 ) {
            timeoutHandle = setTimeout(tick, 1000);
        } else {
            if(mins > 1){
              // countdown(mins-1);   never reach “00″ issue solved:Contributed by Victor Streithorst    
              setTimeout(function () { 
                countdown(parseInt(mins)-1,false); }, 1000
              );
                     
            }
        }

        if(seconds==0 && mins==1){
          clear_all_timeout();
          $('#auto_logout_after').modal();
          if(is_logout == 0){
            is_logout = 1;
            countdown(1,false);
          }else{
            is_logout = 0;
            $('#auto_logout_cancel').click();
          }
        }
    }
    tick();

}


function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname+"="+cvalue+"; "+expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

$(document).ready(function(e){

  $('#auto_logout_extend').click(function(e){
    clear_all_timeout();
    countdown(auto_logout_after,false);
    $('#auto_logout_after').modal('hide');
    is_logout = 0;
  });
  countdown(auto_logout_after,false);

  $('#auto_logout_cancel').click(function(e){
    window.location.href = "{!! url('/logout') !!}"
  });


    $('button, input, a, select, textarea').on('keyup change keydown click', function(e){
      
        clear_all_timeout();
        countdown("{{$auto_logout_after}}",false);
        is_logout = 0;
      
    });
});

  function clear_all_timeout(){
    var id = window.setTimeout(function() {}, 0);
    while (id--) {
        window.clearTimeout(id); // will do nothing if no timeout with id is present
    }
  }
</script>

<?php } ?>


<?php if($is_two_step==1 && Auth::user() && (Auth::user()->password_flag == 1) && (trim(Auth::user()->two_step_pin) == "" || Auth::user()->two_step_pin_flag == 0)){ ?>
<div class="modal fade animated zoomIn" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="two_step_form" action="return false;">
        <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
      <div class="modal-header">
        Please Setup 2 step verification pin
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-md-3">Pin</label>
          <div class="col-md-9">
          <input type="password" name="pin" value="" id="pin" class="form-control" required maxlength="6">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3">Confirm Pin</label>
          <div class="col-md-9">
          <input type="password" name="pin_confirmation" value="" id="pin_confirmation" class="form-control" required maxlength="6">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="pin_submit">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
function afterModalTransition(e) {
  e.setAttribute("style", "display: none !important;");
}

$(document).ready(function(e){
  


  $('#pin_submit').click(function(e){
      $.ajax({
        url: "{!! url('/profile/pin/update') !!}",
        type: 'POST',
        data: '_token='+$('#two_step_form .token').val()+'&pin='+$('#two_step_form #pin').val()+'&pin_confirmation='+$('#two_step_form #pin_confirmation').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#two_step_form .text-danger').remove();
          $('#two_step_form input').removeClass('input-error');
          $('#pin_submit').prop('disabled',true);
          $('#pin_submit').text("Validating...");
          $('#pin_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#exampleModal').modal('hide');
            success_messages(json['message']);
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
            if(json['errors']['pin']){
              $("#two_step_form input[name='pin']").addClass("input-error");
              $("#two_step_form input[name='pin']").after("<span class='text-danger'>"+json['errors']['pin'][0]+"</span>");
            }
            if(json['errors']['pin_confirmation']){
              $("#two_step_form input[name='pin_confirmation']").addClass("input-error");
              $("#two_step_form input[name='pin_confirmation']").after("<span class='text-danger'>"+json['errors']['pin_confirmation'][0]+"</span>");
            }
          }

          $('#pin_submit').prop('disabled',false);
          $('#pin_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#pin_submit').prop('disabled',false);
          $('#pin_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
  });

  $('#exampleModal').modal();

});

</script>
<?php } ?>

<?php if($is_two_step==1 && Auth::user() && (trim(Auth::user()->password_flag) == 0)){ ?>
  <div class="modal fade animated zoomIn" id="new_password_modal" tabindex="-1" role="dialog" aria-labelledby="new_password_modalLabel" aria-hidden="false" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="new_password_form" action="return false;">
          <input type="hidden" name="_token" class="token" value="{!! csrf_token() !!}">
        <div class="modal-header">
          Please Setup New Password
        </div>
        <div class="modal-body">

          <div class="form-group row">
            <label class="col-md-3">Password</label>
            <div class="col-md-9">
            <input type="password" name="password" value="" id="password" class="form-control" required minlength="8" maxlength="16">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3">Confirm Password</label>
            <div class="col-md-9">
            <input type="password" name="password_confirmation" value="" id="password_confirmation" class="form-control" required minlength="8" maxlength="16">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="password_submit">Update Password</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
  function afterModalTransition(e) {
    e.setAttribute("style", "display: none !important;");
  }
  
  $(document).ready(function(e){
    
    
  
    $('#password_submit').click(function(e){
        $.ajax({
          url: "{!! url('/profile/old_password/update') !!}",
          type: 'POST',
          data: '_token='+$('#new_password_form .token').val()+'&password='+$('#new_password_form #password').val()+'&password_confirmation='+$('#new_password_form #password_confirmation').val(),
          dataType: 'json', 
          beforeSend: function() {
            $('.modal').removeClass('animated shake');
            $('#new_password_form .text-danger').remove();
            $('#new_password_form input').removeClass('input-error');
            $('#password_submit').prop('disabled',true);
            $('#password_submit').text("Validating...");
            $('#password_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          },  
          complete: function() {
  
          },        
          success: function(json) {
            $('.modal').addClass('animated shake');
            $('.jq-toast-wrap').remove();
  
            if(json['status'] == true){
              $('#new_password_modal').modal('hide');
              success_messages(json['message']);
              window.location.reload();
            }
  
            if(json['status'] == false){
              if(json['login_required']){
                error_messages(json['message']);
              }
              if(json['errors']['password']){ console.log('test');
                $("#new_password_form input[name='password']").addClass("input-error");
                $("#new_password_form input[name='password']").after("<span class='text-danger'>"+json['errors']['password'][0]+"</span>");
              }
              if(json['errors']['password_confirmation']){
                $("#new_password_form input[name='password_confirmation']").addClass("input-error");
                $("#new_password_form input[name='password_confirmation']").after("<span class='text-danger'>"+json['errors']['password_confirmation'][0]+"</span>");
              }
            }
  
            $('#password_submit').prop('disabled',false);
            $('#password_submit').text("Update Password");
            $('.loading_spinner').remove();
          },
          error: function(data) {
            var errors = data.responseJSON;
            $('#password_submit').prop('disabled',false);
            $('#password_submit').text("Update Password");
            $('.loading_spinner').remove();
          }
        }); 
    });
    
    $('#new_password_modal').modal();
  
  });
  
  </script>
  <?php } ?>

<?php 
  
  $auto_logout_after = 0;
  if(!empty($setting['auto_logout_after']) && $setting['auto_logout_after']>0){
    $auto_logout_after = $setting['auto_logout_after'];
  }

?>

<?php /* Pusher */ ?>
@if(config('public_config.is_pusher'))
<div class="modal fade animated jackInTheBox" id="dialog_toaster" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        New Incident<button type="button" class="close" data-dismiss="modal">&times;</button> 
      </div>
      <div class="modal-body" id="dialog_toaster_body">      
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<audio id="booth_app_warning" style="visibility: hidden;">
  <source src="<?php echo url('theme/audio/danger-alarm2.mp3'); ?>" type="audio/ogg">
  <source src="<?php echo url('theme/audio/danger-alarm2.mp3'); ?>" type="audio/mpeg">
  Your browser does not support the audio tag.
</audio>
<script src="<?php echo url('theme/js/pusher.min.js') ?>"></script>
<script src="{{ url('theme/js/articulate.min.js') }}"></script>
<script type="text/javascript">

var soundEmbed = null;

function speak() {
  $(document).ready(function(e){
    if($('#dialog_toaster_body').length){
      $('#dialog_toaster_body').articulate('pitch',2).articulate('rate',0.5).articulate('speak');
    }
  });
}

function soundPlay(){
    soundEmbed = document.getElementById("booth_app_warning");
    playPromise = soundEmbed.play();
    if (playPromise !== undefined) {
      playPromise.then(function() {
        // Automatic playback started!
      }).catch(function(error) {
        // Automatic playback failed.
        // Show a UI element to let the user manually start playback.
        console.log("Auto Play not supported.");
      });
    }
    soundEmbed.onended = function(){
      speak();
    };
    soundEmbed.removed = false;
    document.body.appendChild(soundEmbed);
}

function additional_sound(data){
  $('#dialog_toaster_body').html(data.title);
  $('#dialog_toaster').modal('show');
  soundPlay();
}

var st_code = "{{Auth::user()->st_code}}";
var dist_no = "{{Auth::user()->dist_no}}";
var ac_no   = "{{Auth::user()->ac_no}}";
var role_id = "{{Auth::user()->role_id}}";

Pusher.logToConsole = true;
var pusher = new Pusher("<?php echo config('broadcasting.connections.pusher.key'); ?>", {
  cluster: "<?php echo config('broadcasting.connections.pusher.options.cluster'); ?>",
  forceTLS: true
});
var channel = pusher.subscribe('my-channel');
channel.bind('my-event', function(data) {
  if(data.st_code == st_code && data.dist_no == dist_no && data.ac_no == ac_no && role_id == '19'){
    additional_sound(data);
  }else if(data.st_code == st_code && data.dist_no == dist_no && role_id == '5'){
    additional_sound(data);
  }else if(data.st_code == st_code && role_id == '4'){
    additional_sound(data);
  }
  /* to run every event for ECI */
  if(role_id == '7'){
    additional_sound(data);
  }
});
channel.bind('pusher:subscription_succeeded', function(members) {
  /*console.clear();*/
});

</script>
@endif