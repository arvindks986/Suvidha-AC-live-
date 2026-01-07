<style type="text/css">
/******************************Drop-down menu work on hover**********************************/
<!-- .mainmenu{background: none;border: 0 solid;margin: 0;padding: 0;min-height:20px;float: left;}
@media only screen and (min-width: 767px) {
.mainmenu .collapse ul li{position:relative;}
.mainmenu .collapse ul li:hover> ul{display:block}
.mainmenu .collapse ul ul{position:absolute;top:100%;left:0;min-width:250px;display:none}
/*******/
.mainmenu .collapse ul ul li{position:relative;display: -webkit-box;}
.mainmenu .collapse ul ul li:hover> ul{display:block}
.mainmenu .collapse ul ul ul{position:absolute;top:0;left:100%;min-width:250px;display:none}
/*******/
.mainmenu .collapse ul ul ul li{position:relative}
.mainmenu .collapse ul ul ul li:hover ul{display:block}
.mainmenu .collapse ul ul ul ul{position:absolute;top:0;left:-100%;min-width:250px;display:none;z-index:1}

} -->
</style>

<style type="text/css" >        
      <!--   @media print {     
    .myheader{display: none !important;}
    .footer{display: none !important;}
    .slicknav_menu{display: none !important;}
    .mybradcom{display: none !important;}
}  -->
</style>

 <header class="header ">
   <nav>
    <div class="nav-header">
      <div class="container-fluid d-flex flex-md-row align-items-md-center">
        <div class="float-left mr-auto">
          <a href="#" class="navbar-brand ">
            <img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span>
          </a>
        </div>
    
        <div class="col-xs-1">
          <span class="text-white" style="font-size:30px;cursor:pointer" onclick="openNavR()">
            <small class="text-white" style="font-size: 18px; position: relative; top: -5px;">
            MENU &nbsp;&nbsp;</small>☰</span>
        </div>
       <div id="mySidenavR" class="sidenavR">
        <div class="Closedbtn">
          <a href="javascript:void(0)" class="closebtn" onclick="closeNavR()">
            <span>Close &nbsp;</span>×
          </a>
        </div>
      
    <!-- CEOAC Login Section-->
    @if($user_data->role_id=='4')
    <div class="slider-menu">
      <nav class="slider-menu__container" role="navigation" aria-label="Menu">
        <ul class="mainmenu">
          <li class="active slider-menu__item">
            <a href="{{url('/acceo/dashboard')}}" class="slider-menu__link">Home</a>
          </li>
          <!-- <li class="active slider-menu__item">
            <a href="{{url('/acceo/notification')}}" class="slider-menu__link">Expenditure<span class="span"><?php echo session()->get('countscrutiny'); ?></span></a>
          </li> -->
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Dashboard<span class="arrow-down"></span></a>
              <ul class="slider-menu__menu">
                <li class="slider-menu__item"><a rel="" href="{{url('/acceo/expdashboard')}}" class="slider-menu__link"> Analytical Dashboard</a></li>
                <li class="slider-menu__item"><a rel="" href="{{url('/acceo/statusExpdashboard')}}" class="slider-menu__link"> Status Dashboard</a></li>
              </ul>
          </li>
           <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">MIS<span class="arrow-down"></span></a>
              <ul class="slider-menu__menu">
               <li><a class="slider-menu__item" href="{{url('/acceo/mis-officer')}}">Officer MIS</a></li>
              </ul>
          </li>
          <li class="active slider-menu__item">
            <a href="{{url('/acceo/allscrutiny')}}" class="slider-menu__link">Notification</a>
          </li>
          <li class="active slider-menu__item">
            <a href="{{url('/acceo/definalizedcandidate')}}" class="slider-menu__link">Definalized Candidate</a>
          </li>
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Account<span class="arrow-down"></span></a>
           <ul class="slider-menu__menu">             
            <li class="slider-menu__item"><a rel="" href="{{url('/logout')}}" class="slider-menu__link"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
        </ul>
      </nav>
    </div>
    @endif
    <!-- End CEO AC Model-->
    
    <!-- ECIAC ECI Expenditure Level Login Dashboard Header-->
    @if($user_data->role_id=='28')
    <div class="slider-menu">
      <nav class="slider-menu__container" role="navigation" aria-label="Menu">
        <ul class="mainmenu">
          <li class="active slider-menu__item">
            <!-- <a href="{{url('/eci-expenditure/expdashboard')}}" class="slider-menu__link">Home</a> -->
             <a href="{{url('/eci-expenditure/statusdashboard')}}" class="slider-menu__link">Home</a>
          </li>
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Dashboard<span class="arrow-down"></span></a>
              <ul class="slider-menu__menu">
                <!-- <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/expdashboard')}}" class="slider-menu__link"> Analytical Dashboard</a></li> -->
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/statusdashboard')}}" class="slider-menu__link"> Currrent Status Dashboard</a></li>
              </ul>
          </li>
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">MIS<span class="arrow-down"></span></a>
              <ul class="slider-menu__menu">
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/mis-officer')}}" class="slider-menu__link"> Officer MIS(Summary)</a></li>
				<li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/mis-officer-details')}}" class="slider-menu__link"> Officer MIS(Details)</a></li>
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/mis-candidate')}}" class="slider-menu__link"> Candidate MIS</a></li>
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/report-officer')}}" class="slider-menu__link"> Summary Status Report</a></li>
              </ul>
          </li>
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Reports<span class="arrow-down"></span></a>
              <ul class="slider-menu__menu">
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/reports')}}" class="slider-menu__link"> Summary Report</a></li>
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/fund-nationalparties')}}" class="slider-menu__link"> Fund Given By National Parties</a></li>

                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/district-report')}}" class="slider-menu__link"> District Wise Status Report</a></li>
				<li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/breach-report')}}" class="slider-menu__link">Breaching Report</a></li>
              </ul>
          </li>
           <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Notification<span class="arrow-down"></span></a>
              <ul class="slider-menu__menu">
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/eciallscrutiny')}}" class="slider-menu__link"> Received via CEO</a></li>
                <li class="slider-menu__item"><a rel="" href="{{url('/eci-expenditure/eciallscrutinybyepass')}}" class="slider-menu__link"> Received via ECI</a></li>
              </ul>
          </li>
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Account<span class="arrow-down"></span></a>
           <ul class="slider-menu__menu">    
           <li class="slider-menu__item"><a rel="" href="{{url('/profile/password')}}" class="slider-menu__link"> <span class="d-none d-sm-inline-block">Change Password</span> <i class="fa fa-sign-out"></i></a></li>
           <li class="slider-menu__item"><a rel="" href="{{url('/profile/pin')}}" class="slider-menu__link"> <span class="d-none d-sm-inline-block">Change PIN</span> <i class="fa fa-sign-out"></i></a></li>         
            <li class="slider-menu__item"><a rel="" href="{{url('/logout')}}" class="slider-menu__link"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
         </ul>
      </nav>
    </div>
    @endif
    <!-- End of ECIAC ECI Expenditure Level Login Dashboard Header-->
          
    <!-- DEOAC Login Section-->
    @if($user_data->role_id=='5' || $user_data->role_id=='24')
    <div class="slider-menu">
      <nav class="slider-menu__container" role="navigation" aria-label="Menu">
        <ul class="mainmenu">
          <li class="active slider-menu__item">
            <a href="{{url('/eci-expenditure/expdashboard')}}" class="slider-menu__link">Home</a>
          </li>
         <!--  <li class="active slider-menu__item">
            <a href="{{url('/eci-expenditure/expdashboard')}}" class="slider-menu__link">Expenditure</a>
          </li> -->
          <li class="active slider-menu__item">
            <a href="{{url('/acdeo/scrutinyExpenditure')}}" class="slider-menu__link">Fill DEO Scrutiny Report</a>
          </li>
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Dashboard<span class="arrow-down"></span></a>
              <ul class="slider-menu__menu">
                <li class="slider-menu__item"><a rel="" href="{{url('/acdeo/expdashboard')}}" class="slider-menu__link"> Analytical Dashboard</a></li>
                <li class="slider-menu__item"><a rel="" href="{{url('/acdeo/statusdashboard')}}" class="slider-menu__link"> Status Dashboard</a></li>
              </ul>
          </li>
          <li class="active slider-menu__item">
            <a href="{{url('/acdeo/Summary')}}" class="slider-menu__link">Summary Report</a>
          </li>
          <li class="slider-menu--has-children slider-menu__item"><a href="javascript:void(0)" class="slider-menu__link">Account<span class="arrow-down"></span></a>
           <ul class="slider-menu__menu">
            <li class="slider-menu__item"><a rel="" href="{{url('/logout')}}" class="slider-menu__link"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
        </ul>
      </nav>
    </div>
    @endif
    <!-- End of DEOAC Login Section-->
  
      
    </div>

    </div>
  </div>
      <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div>

  </nav>
</header>