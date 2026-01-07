<header class="header">
  <nav>
    <div class="nav-header">
      <div class="container-fluid d-flex flex-md-row align-items-md-center">
        <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/central-login/garuda.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
        <!-- ROAC Login Section-->
         @if(Auth::user())<div class="col-xs-1"><span class="text-white" style="font-size:30px;cursor:pointer" onclick="openNavR()"><small class="text-white" style="font-size: 18px; position: relative; top: -5px;">MENU &nbsp;&nbsp;</small>☰</span>
        </div>@else <div class="col-xs-1"><small class="text-white" style="font-size: 18px; position: relative; top: -5px;"><a class="login" href="{{url('garudapp/login')}}">Login</a> &nbsp;&nbsp;</small>
        </div> @endif

        <div id="mySidenavR" class="sidenavR">

          <div class="Closedbtn">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNavR()">
              <span>Close &nbsp;</span>×</a>
          </div>
		 
		 @if($user_data->role_id=='5')
			<ul class="float-right mainmenu">
            <li class="active"><a href="{{url('garudapp/dashboard')}}">Home</a></li>
			<li class="active"><a href="{{url('acdeo/mis/list-exgratia')}}">Ex-Gratia</a></li>
			
		   <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul>
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('garudapp/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul> 
		 @endif
		 
		 @if($user_data->role_id=='4')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('garudapp/dashboard')}}">Home</a></li>
			<li class="active"><a href="{{url('acceo/mis/list-exgratia')}}">Ex-Gratia</a></li>
			
		   <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul>
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('garudapp/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul>
		@endif
		


        </div>




      </div>
    </div>


  </nav>
</header>

<?php
//$setting = \App\models\Admin\SettingModel::get_first_result('config');
//if ($setting && $setting['key'] == 'message') {
?>@if(Auth::user())
  <div class="alert-warning text-center">
    <marquee>
	{{-- {{$setting['value']}} --}}
    </marquee>
  </div>@endif
<?php //} ?>