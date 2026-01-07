<?php if(!isset($is_active)){
  $is_active = 0;
}
$user = Auth()->user();
$roleid = $user->role_id;
$UserMobile = $user->mobile;
//dd($roleid);
?>

   <header class="header"> <nav class="">
      <div class="nav-header">
    <div class="container-fluid d-flex flex-md-row align-items-md-center">
         @if($roleid == 2)
    <div class="float-left mr-auto"><a href="{{url('/dashboard-nomination-new')}}" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
	
	
	<div style="font-size: 13px;">
        <span><a href="{{url('/nomination/apply-nomination-step-1')}}" style="color:white;">Profile</a></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span><a href="{{url('/candidatelogout')}}" style="color:white;">Logout</a></span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         @if($UserMobile=='9871124359')
        <span><a href="{{url('/deleteuser')}}" style="color:white;">Delete</a></span>
         @endif
    </div>
    @else
         <div class="float-left mr-auto">
        <a href="#" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
     <!-- ROPC Login Section-->
	   <div class="col-xs-1"><span class="text-white" style="font-size:30px;cursor:pointer" onclick="openNavR()"><small class="text-white" style="font-size: 18px; position: relative; top: -5px;">MENU &nbsp;&nbsp;</small>&#9776;</span>
                </div>
	   <div id="mySidenavR" class="sidenavR">
      <div class="Closedbtn">
			<a href="javascript:void(0)" class="closebtn" onclick="closeNavR()">
			<span>Close &nbsp;</span>&#10006;</a>
			</div>
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/permission')}}">Dashboard</a></li>

         
          <li><a href="{{url('/create')}}">Apply Permission</a></li>
          <li><a href="{{url('/update profile')}}">Profile</a></li>
           @if($UserMobile=='9871124359')
          <li><a href="{{url('/deleteuser')}}">Delete</a></li>
         @endif
          <li><a href="{{url('/candidatelogout')}}" class="nav-link logout"> <span class="">Logout</span> <i class="fa fa-sign-out"></i></a></li>
        </ul>
          
      </div>
         @endif
      </div>
      <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div>
	  </div>
    </nav>
   </header>
<?php
$setting = \App\models\Admin\SettingModel::get_first_result('candidate');
if($setting && $setting['key'] == 'message'){
?>
<div class="alert-warning text-center">
<marquee>
{{$setting['value']}}
</marquee>
</div>
<?php } ?>