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
			<!--<li class="active"><a href="{{url('acdeo/add_account_info')}}">Account Information</a></li>-->
			
		   <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul>
				<li><a rel="" href="{{url('/acdeo/mis/officer-details')}}"  > <span>Update Officer Details</span></a></li>
				<li><a rel="" href="{{url('/acdeo/mis/update-officer-profile')}}"  > <span>Update Profile</span></a></li>
                <li><a rel="" href="{{url('/central/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/central/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('garudapp/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul> 
		 @endif
		 
		 @if($user_data->role_id=='7')
			<ul class="float-right mainmenu">
            <li class="active"><a href="{{url('garudapp/dashboard')}}">Home</a></li>
			<!--<li><a href="{{url('eci/view_account_info')}}">View Account Info</a></li>-->
			<!-- <li><a href="{{url('eci/getcountreport')}}">State Wise Count Report</a></li> -->
			<li class="active"><a href="{{url('eci/mis/list-exgratia')}}">Ex-Gratia</a></li>
			<li class="active"><a href="{{url('eci/mis/officer-directory')}}">Officer Directory</a></li>
		   <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul>
                <li><a rel="" href="{{url('/central/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/central/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('garudapp/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul> 
		 @endif
		 
		 @if($user_data->role_id=='50')
			<ul class="float-right mainmenu">
            <li class="active"><a href="{{url('garudapp/dashboard')}}">Home</a></li>
			<li class="active"><a href="{{url('seczonal/mis/list-exgratia')}}">Ex-Gratia</a></li>
			
		   <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul>
                <li><a rel="" href="{{url('/central/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/central/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('garudapp/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul> 
		 @endif
		 
		 @if($user_data->role_id=='4')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('garudapp/dashboard')}}">Home</a></li>
			<li class=""><a href="{{url('acceo/mis/list-exgratia')}}">Ex-Gratia</a></li>
			<!--<li class=""><a href="{{url('acceo/add_payment_info')}}">Account Information</a></li>-->
			 <li><a href="javascript:void(0)" >Mparty<span class="arrow-down"></span></a>
      <ul>
            <li class="active"><a href="{{url('/mparty/dashboard')}}">Dashboard</a></li>
            <li><a href="javascript:void(0)">Party & Symbol<span class="arrow-down"></span></a>
            <ul>
              <li><a rel="" href="{{url('/mparty/ceo/state-party-list')}}"  > <span>List of Political Parties</span></a></li>
              <li><a rel="" href="{{url('/mparty/ceo/symbol-list')}}"  > <span>List of Symbol</span></a></li>
            </ul>
            </li> 
            <li><a href="javascript:void(0)">Party Reports<span class="arrow-down"></span></a>
            <ul>
              <li><a rel="" href="{{url('/mparty/ceo/partywise-reports')}}"  > <span>Political Party Reports</span></a></li>
              <li><a rel="" href="{{url('/mparty/ceo/symbol-reports')}}"  > <span>Symbol Reports</span></a></li>
            </ul>
            </li> 
       </ul>
        </li>
		   <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul>
				<li><a rel="" href="{{url('/acceo/mis/officer-details')}}"  > <span>Update Officer Details</span></a></li>
				<li><a rel="" href="{{url('/acceo/mis/update-officer-profile')}}"  > <span>Update Profile</span></a></li>
                <li><a rel="" href="{{url('/central/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/central/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('garudapp/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul>
		@endif
		@if($user_data->role_id=='19' || $user_data->role_id=='21')
			<ul class="float-right mainmenu">
            <li class="active"><a href="{{url('garudapp/dashboard')}}">Home</a></li>
			<li><a href="javascript:void(0)" >MIS<span class="arrow-down"></span></a>
					<ul>
					  <li><a href="{{url('roac/mis/ac-profiling-entry')}}">Profile Form</a></li>
					</ul>
				  </li>
				  
				  <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul>
				<li><a rel="" href="{{url('/roac/mis/officer-details')}}"  > <span>Update Officer Details</span></a></li>
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