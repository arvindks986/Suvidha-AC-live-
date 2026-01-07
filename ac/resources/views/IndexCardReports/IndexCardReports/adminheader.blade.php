<?php  
$st_code  = '';
$bst_code  = 'U05';
$ac_no    = 0;
$dist_no  = 0;
$phase_no = 1;
$allowed_st_code  = [];
$allowed_acs      = [];
$allowed_dist_no  = [];
if(Auth::user()){
  $st_code  = Auth::user()->st_code;
  $dist_no  = Auth::user()->dist_no;
  $ac_no    = Auth::user()->ac_no;
}
if($st_code == 'S19'){
  $allowed_st_code = ['S19'];
  $allowed_acs = ['29'];
  $allowed_dist_no = ['4'];
}
if($st_code == 'S13'){
  $allowed_st_code = ['S13'];
  $allowed_acs = ['215'];
  $allowed_dist_no = ['25'];
}
$setting = \App\models\Admin\SettingModel::get_setting_cache();
if(!empty($setting['booth_app'])){
  foreach($setting['booth_app'] as $iterate_booth_app){
    if($iterate_booth_app['states'] == $st_code){
      $allowed_st_code[] = $st_code;
    }
    if($iterate_booth_app['states'] == $st_code && $iterate_booth_app['districts'] == $dist_no){
      $allowed_dist_no[] = $dist_no;
    }
    if($iterate_booth_app['states'] == $st_code && $iterate_booth_app['districts'] == $dist_no && in_array($ac_no,$iterate_booth_app['acs'])){
      $allowed_acs[] = $ac_no;
    }
  }
}
?>
<header class="header">
   <nav>
      <div class="nav-header">
    <div class="container-fluid d-flex flex-md-row align-items-md-center">
    <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
     <!-- ROAC Login Section--> 
      <div class="col-xs-1"><span class="text-white" style="font-size:30px;cursor:pointer" onclick="openNavR()"><small class="text-white" style="font-size: 18px; position: relative; top: -5px;">MENU &nbsp;&nbsp;</small>☰</span>
                </div>
      
         <div id="mySidenavR" class="sidenavR">
		 
			<div class="Closedbtn">
			<a href="javascript:void(0)" class="closebtn" onclick="closeNavR()">
			<span>Close &nbsp;</span>×</a>
			</div>
		 @if($user_data->role_id=='19' || $user_data->role_id=='21')
        <ul class="float-right mainmenu">
          <!-- <li class="active"><a href="{{url('/roac/dashboard')}}">Home</a></li> -->
          <li class="active"><a href="{{url('/roac/dashboard')}}"> Home</a></li>
      
     
          
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
          <ul>
           
             
            <li><a rel="" href="{{url('/roac/createnomination')}}"  > <span>Nomination</span></a></li>
            <li><a rel="" href="{{url('/roac/multiplenomination')}}"  > <span>Multiple Nomination</span></a></li>
           
            <li><a rel="" href="{{url('/roac/candidateaffidavit')}}"  > <span>Upload Affidavit</span></a></li>
            <li><a rel="" href="{{url('/roac/counteraffidavit')}}"  > <span>Upload Counter Affidavit</span></a></li>
            <li><a rel="" href="{{url('/roac/listnomination')}}"  > <span>List of Applicants</span></a></li>
            <li><a rel="" href="{{url('/roac/scrutiny-candidates')}}"  > <span>Scrutiny of Candidates</span></a></li>
             <li><a rel="" href="{{url('/roac/accepted-candidate')}}"  >Mark validly nominated candidates</a></li>
            <li><a rel="" href="{{url('/roac/withdrawn-candidates')}}"  > <span>Withdrawl of Candidates</span></a></li>
            <li><a rel="" href="{{url('/roac/symbol-upload')}}"  >Assign Symbol </a></li>
			<li><a rel="" href="{{url('/roac/update-form7A-details')}}"  >Update Form 7A Details</a></li>
            <li><a rel="" href="{{url('/roac/contested-application')}}"  > Contesting Candidates</a></li>
          <!-- <li><a rel="" href="{{url('/roac/ElectorsDetails')}}"  > Electors Details</a></li> --> 
		       <li><a class="dropdown-item" href="{!! url('roac/nomination/list-of-nomination') !!}">List of nominated candidate</a></li>    
	   
             
            </ul>
          </li>
          <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
              <ul>
                <li><a rel="" href="{{url('/roac/permission/allmasters')}}"  >Add/Update Master Data </a></li>
                <li><a rel="" href="{{url('/roac/permission/offlinePermission')}}"  > Offline permission Module</a></li>
                <li><a rel="" href="{{url('/roac/permission/allPermissionRequest')}}"  > Accept/Reject permission</a></li>
                <li><a rel="" href="{{url('/roac/permission/agentCreation')}}"  > Create Agent</a></li>
            </ul>

         </li> 
    		 @if(Auth::user() && Auth::user()->st_code == 'S24' && Auth::user()->ac_no == '228')
             <li><a href="javascript:void(0)" >Booth App<span class="arrow-down"></span></a>
          <ul>
          <li><a rel="" href="{{url('/roac/booth-app/voter-list')}}"  >Booth Slip</a></li>
          <li><a rel="" href="{{url('/roac/booth-app/officer-list')}}"  >Assign Officer</a></li>
          <li><a rel="" href="{{url('/roac/booth-app/dashboard')}}"  >Dashboard</a></li>
                </ul>
            </li>
        @elseif(in_array($st_code,$allowed_st_code) && in_array($ac_no,$allowed_acs) && in_array($dist_no,$allowed_dist_no))
          <li><a href="javascript:void(0)" >Booth App<span class="arrow-down"></span></a>
        <ul>
		


                  <li><a rel="" href="#">Officer assignment</a>
                  <ul>
					<li><a rel="" href="{{url('/roac/booth-app-revamp/officer-list')}}">Assign Officer(PRO/PO)</a></li>
                  <li><a rel="" href="{{url('/roac/booth-app-revamp/assign-so')}}">Assign SM</a></li>
                  <li><a rel="" href="{{url('/roac/booth-app-revamp/location')}}">Polling Station Location</a></li>
                  <li><a rel="" href="{{url('/roac/booth-app-revamp/assign-blo')}}">Assign BLO</a></li>
				  <li><a rel="" href="{{url('/roac/booth-app-revamp/import-excel')}}">Import PO/PRO</a></li>
				  </ul>
				  </li>
				 <li><a rel="" href="{{url('roac/booth-app-revamp/electors-verification-by-ps')}}">Verify Electors</a></li>
				 <li><a rel="" href="{{url('roac/randomize-details')}}">Polling Party Schedule</a></li>
                  <li><a rel="" href="{{url('/roac/booth-app-revamp/dashboard')}}">Dashboard</a></li>
                  <li><a rel="" href="{{url('/roac/booth-app-revamp/exempted')}}">Exempt Polling Station</a></li>
				  <li><a rel="" href="{{url('/roac/booth-app-revamp/exempted-turnout')}}">Exempted Turnout Form</a></li>
                  
                  
                  <li><a rel="" href="{{url('/roac/booth-app-revamp/get-form-17-a')}}">Forms</a></li>
				  <li><a rel="" href="#">Reports</a>
                  <ul>
                    <li><a href="{!! Common::generate_url('booth-app-revamp/poll-material/ac/ps') !!}">Poll Material Report</a></li>
					<li><a href="{{Common::generate_url('booth-app-revamp/officer-assignment-report/ac/ps')}}">Officer Assignment Report</a></li>
					<li><a href="{{url('roac/booth-app-revamp/poll-turnout-report/state/ac')}}">Poll Turnout Report</a></li>
          <li><a href="{{url('roac/booth-app-revamp/poll-event-report')}}">Poll Event Report</a></li>
		  
		  <li><a href="{{url('roac/booth-app-revamp/exempt-turnout-report/state/ac')}}">Exempted PS Turnout Report</a></li>
					<!--<li><a href="{{url('roac/booth-app-revamp/evm-comparision/state/ac')}}">Evm Comparision</a></li>
					<li><a href="{{url('roac/booth-app-revamp/ac/blo-pro-difference')}}">BLO/PRO Turnout</a></li>-->
					</ul>
					</li>
                  
            </ul>
      </li>
        @endif
		
   
    <li><a href="javascript:void(0)" >Voter Turnout<span class="arrow-down"></span></a>
			<ul>
                <li><a rel="" href="{{url('/roac/turnout/estimate-turnout-entry')}}" >Estimate Turnout Entry</a></li>
                <li><a rel="" href="{{url('/roac/turnout/schedule-entry')}}" >End of Poll Turnout </a></li> 
                <li><a rel="" href="{{url('/roac/turnout/ElectorsDetails')}}" >Electors Details</a></li>
                <li><a rel="" href="{{url('/roac/turnout/RoPsWiseDetails')}}?state={{$user_data->st_code}}&ac_id={{$user_data->ac_no}}" >PS Wise Voter Turn Out</a></li>
            </ul>
      <?php /*<ul>
        <li><a href="{{url('roac/turnout/RoPsWiseDetails?')}}ac_id={{$user_data->ac_no}}&state={{$user_data->st_code}}">PS Wise Voter Turnout</a></li>  
		<li><a rel="" href="{{url('/roac/ElectorsDetails')}}"  >Electors Details</a></li>   
        <!--<li><a rel="" href="{{url('/roac/voting/list-schedule')}}"  >List of Poll Turnout</a></li>-->
        <?php /*
        <li><a rel="" href="{{url('/roac/voting/schedule-entry')}}"  >End of Poll Turnout</a></li>
        <li><a href="{{url('roac/RoPsWiseDetails')}}?state={{$user_data->st_code}}&ac_id={{$user_data->ac_no}}">PS Wise Voter Turnout</a></li>
          </ul> */?>
    </li>  
@if(Session::has('DB_id') && in_array(Session::get('DB_id'),[5,2]))	  
	 <li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul>
         <!--  <li><a rel="" href="{{url('/roac/counting/prepare-counting')}}"  >Prepare Counting Data</a></li> -->
          <li><a rel="" href="{{url('/roac/counting/round-schedule')}}"  >1.- Round Schedule </a></li>
          <li><a rel="" href="{{url('/roac/counting/counting-data-entry')}}"  >2.- EVM Votes Data Entry </a></li>
          <li><a rel="" href="{{url('/roac/counting/postal-data-entry')}}"  >3.- Postal Ballot Votes </a></li>
       
          <li><a rel="" href="{{url('/roac/counting/counting-results')}}"  >4.- Results Declaration </a></li>
           
           </ul> 
          </li> 

@else	
       <li><a href="javascript:void(0)" >Counting Preparation<span class="arrow-down"></span></a>
            <ul class="dropdown">
            @if($user_data->designation=="ROAC")
           <li><a rel="" href="{{url('/roac/counting/counting-user')}}">1.- User Creation </a></li>
            <li><a rel="" href="{{url('/roac/counting/counting-center-details')}}">2.- Counting Center Details </a></li>
            <li><a rel="" href="{{url('/roac/counting/round-schedule-details')}}">3.- Round Schedule for AC</a></li>
            <li><a rel="" href="{{url('/roac/counting/user-assign-table-details')}}">4.- Table assignment </a></li> 
 
          @endif
          </ul> 
      </li> 


       
        <li><a href="javascript:void(0)" >PS Wise Counting<span class="arrow-down"></span></a>
            <ul class="dropdown">
            @if($user_data->designation=="ROAC")
           <!--  <li><a rel="" href="{{url('/roac/counting/counting-user')}}">1.- User Creation </a></li>
            <li><a rel="" href="{{url('/roac/counting/counting-center-details')}}">2.- Counting Center Details </a></li>
            <li><a rel="" href="{{url('/roac/counting/round-schedule-details')}}">3.- Round Schedule for AC</a></li>
            <li><a rel="" href="{{url('/roac/counting/user-assign-table-details')}}">4.- Table assignment </a></li>
 -->
          <li><a rel="" href="{{url('/roac/counting/polling-station-wisevote-entry')}}">1.- PS Wise EVM Votes </a></li>
            <li><a rel="" href="{{url('/roac/counting/round-wise-results')}}">2.- Round Declaration  </a></li>
             <li><a rel="" href="{{url('/roac/counting/evm-votes-finalized')}}">3.- Finalize EVM Votes </a></li>
            <li><a rel="" href="{{url('/roac/counting/bpostal-data-entry')}}">4.- Entry of Postal Ballot Votes </a></li>
            <li><a rel="" href="{{url('/roac/counting/boothcounting-results')}}">5.- Result Declaration </a></li>
             
          @else
             <li><a rel="" href="{{url('/roac/counting/polling-station-wisevote-entry')}}">1.- PS Wise EVM Votes</a></li>
             <li><a rel="" href="{{url('/roac/counting/tabulating-trend-results')}}">2.- Roundwise Result</a></li>
          @endif
          </ul> 
      </li>    
          <li><a href="javascript:void(0)" >PS Wise Counting Report<span class="arrow-down"></span></a>
        <ul>
           <li><a rel="" href="{{url('/roac/counting/constituency-wise-report')}}"  >AC Result Report</a></li>
          <li><a rel="" href="{{url('/roac/form-21c-report')}}"  >Form 21 C/D Details</a></li>
          <li><a rel="" href="{{url('/roac/form-21-report-upload')}}"  >Upload Form 21 C/D</a></li>
           
          <li><a rel="" href="{{url('/roac/counting/tabulating-trend-results')}}"> Trending Result</a></li>
          <li><a rel="" href="{{url('/roac/counting/generate-form20')}}"> Generate Form 20 </a></li>
      <li><a rel="" href="{{url('roac/counting/report_state/state/ac?')}}ac_no={{base64_decode($user_data->ac_no)}}"  > Table Scheduled</a></li>
      <li><a rel="" href="{{url('/roac/booth-counting/active-user-report')}}">Active User Report</a></li>
      <li><a rel="" href="{{url('/roac/booth-counting/candidate-wise-report')}}"  >Candidate Wise Report</a></li>

        </ul>
       </li>    
@endif

          <?php /*
          
          */ ?>
          
		  
		  
      <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
          <ul>
              <li><a rel="" href="{{url('/roac/datewisereport')}}"  >Nomination Report</a></li> 
       <!-- #waseem -->
               <li><a rel="" href="{{url('/roac/report/scrutiny')}}"  >Scrutiny Report</a></li> 
      
                  <li><a rel="" href="{{url('/roac/reportro')}}"  >Datewise Permission Report</a></li> 
                          
                  <li><a rel="" href="{{url('/roac/permissionraw')}}"  > Permission Raw Report</a></li> 
                          
                  <li><a rel="" href="{{url('/roac/partywise')}}"  >PartyWise Permission Report</a></li> 
                            
                  <li><a rel="" href="{{url('/roac/permissiontype')}}"  >PermissionWise Report</a></li>   
 
            <!-- <li><a rel="" href="{{url('/roac/permission/permissioncount')}}"  > Permission Report</a></li>-->
			 
			 <?php /*
        <li><a rel="" href="{{url('/ropc/datewisereport')}}"  >Nomination Report</a></li>  
          <li><a rel="" href="{{url('/ropc/form3A-report')}}"  >Form 3A Report</a></li>   
          <li><a rel="" href="{{url('/ropc/form4A-report')}}"  >Form 4A Report</a></li>     */?>
		  
		  
          </ul>
          </li>
          
      <!-- <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
        <ul>
          <li class="active"><a href="{{url('/roac/round-wise-details')}}">Round Wise Details</a></li>
          <li><a rel="" href="{{url('/roac/datewisereport')}}"  >Nomination Report</a></li> 
          <li><a rel="" href="{{url('/roac/constituency-wise-report')}}"  >AC Result Report</a></li>
          <li><a rel="" href="{{url('/eci/round-wise-report')}}"  >Round  Wise Report</a></li>
          <li><a rel="" href="{{url('/roac/candidate-wise-report')}}"  >Candidate Wise Report</a></li>
          <?php /*<li><a rel="" href="{{url('/roac/form-21-report')}}"  >Form 21 E Details</a></li> */ ?>
          <li><a rel="" href="{{url('/roac/form-21c-report')}}"  >Form 21 C/D Details</a></li>
          <li><a rel="" href="{{url('/roac/form-21-report-upload')}}"  >Upload Form 21 C/D</a></li>
        </ul>
       </li> -->
       
	@if(is_valid_indexcard())
      <li><a href="javascript:void(0)" >Index Card<span class="arrow-down"></span></a>
         <ul>
            <li><a   href="{{url('roac/elector/edit?')}}ac_no={{$user_data->ac_no}}&year={{getElectionYear()}}">Update Electors/Voters</a></li>
            <li><a   href="{!! url('roac/voters/edit?') !!}ac_no={{$user_data->ac_no}}&year={{getElectionYear()}}">Update Voters</a></li>
            <!--<li><a   href="{!! url('roac/indexcard/finalize') !!}">Finalize Index Card</a></li>-->
            <li><a   href="{!! url('roac/index-card') !!}">Index Card Report</a></li>
            <li><a   href="{!! url('roac/nomination/list-of-nomination') !!}">List of nominated candidates</a></li>
          </ul>
      </li>
      @endif
	  <!--<li><a href="javascript:void(0)">Feedback<span class="arrow-down"></span></a>
            <ul>
              <li><a rel="" href="{{url('/roac/feedback')}}"  >Feedback Form</a></li>
            </ul>
      </li>-->
	  
      <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
        <ul>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
           <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
         </ul>
      </li>
      
        </ul>
         @endif
        <!-- End RO ACModel-->

        <!-- RO-Computer Assistant Login Section--> 
      
       @if($user_data->role_id=='36')
        <ul class="float-right mainmenu">
         <li class="active"><a href="{{url('/roac/dashboard')}}"> Home</a></li>
           
         <li><a href="javascript:void(0)" >Booth Counting<span class="arrow-down"></span></a>
            <ul>
             <li><a rel="" href="{{url('/roac/counting/polling-station-wisevote-entry')}}">1.- PS Wise EVM Votes</a></li>
             <li><a rel="" href="{{url('/roac/counting/tabulating-trend-results')}}">2.- Roundwise Result</a></li>
           </ul> 
        </li>     
      
      
       
      <!-- <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
        <ul>
          <li class="active"><a href="{{url('/roac/round-wise-details')}}">Round Wise Details</a></li>
          <li><a rel="" href="{{url('/roac/datewisereport')}}"  >Nomination Report</a></li> 
          <li><a rel="" href="{{url('/roac/constituency-wise-report')}}"  >AC Result Report</a></li>
          <li><a rel="" href="{{url('/eci/round-wise-report')}}"  >Round  Wise Report</a></li>
          <li><a rel="" href="{{url('/roac/candidate-wise-report')}}"  >Candidate Wise Report</a></li>
          
           
          <li><a rel="" href="{{url('/roac/counting/tabulating-trend-results')}}"> Trending Result</a></li>
          <li><a rel="" href="{{url('/roac/counting/generate-form20')}}"> Generate Form 20 </a></li>

        </ul>
       </li> -->
       
    
      <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
        <ul>
           <!--<li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>-->
           <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
         </ul>
      </li>
      
        </ul>
         @endif
        <!-- End RO-Computer Assistant-->
      

        <!-- CEOAC Login Section-->
       @if($user_data->role_id=='4')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/acceo/dashboard')}}">Home</a></li>
		       
           <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <ul>
            <li><a rel="" href="{{url('/acceo/candidate-finalize')}}"  > <span>List of Nomination Finalize</span></a></li>
            <li><a class="dropdown-item" href="{!! url('acceo/nomination/list-of-nomination') !!}">List of nominated candidate</a></li>
			<li><a class="dropdown-item" href="{!! url('acceo/ceo-form7A-details') !!}"> Update Form7A Detils </a></li>
			<li><a rel="" href="{{url('/acceo/state-party-list')}}"  > <span>List of Political Parties</span></a></li>
            <li><a rel="" href="{{url('/acceo/symbol-list')}}"  > <span>List of Symbol</span></a></li>
            </ul>
          </li>
		  @if(Session::has('DB_id') && !in_array(Session::get('DB_id'),[5,2]))
      @if(in_array($st_code,$allowed_st_code))
  <li><a href="javascript:void(0)" >Booth App<span class="arrow-down"></span></a>
    <ul>

     <li><a rel="" href="{{url('/acceo/booth-app-revamp/dashboard')}}"  >Dashboard</a></li>
      <li><a rel="" href="#">Reports</a>
        <ul>
        	<li><a href="{{url('acceo/booth-app-revamp/mapped-location-report')}}">PS Location Mapping</a></li>
          <li><a href="{{Common::generate_url('booth-app-revamp/officer-assignment-report/ac')}}">Officer Assignment Report</a></li>
          <li><a href="{{url('acceo/booth-app-revamp/elector-verify-report')}}">Electors verification report</a></li>
		  <li><a href="{!! Common::generate_url('booth-app-revamp/poll-material/ac') !!}">Poll Material Report</a></li>
          <li><a href="{{url('acceo/booth-app-revamp/poll-turnout-report')}}">Poll Turnout Report</a></li>
          <li><a href="{{url('acceo/booth-app-revamp/poll-event-report')}}">Poll Event Report</a></li>
          <li><a href="{{url('acceo/booth-app-revamp/exemted-ps-count-report')}}">Exempted PS Count Report</a></li>
		  <li><a href="{{url('acceo/booth-app-revamp/exempt-turnout-report')}}">Exempted PS Turnout Report</a></li>
          <!--<li><a href="{{url('acceo/booth-app-revamp/exempt-turnout-report')}}">Exempted PS Turnout Report</a></li>
		  <li><a href="{{url('acceo/booth-app-revamp/state/blo-pro-difference')}}">BLO/PRO Turnout</a></li>
		  <li><a href="{{url('acceo/booth-app-revamp/evm-comparision/state')}}">Evm Comparision</a></li>-->

        </ul>
      </li>

    </ul>
  </li>
  @endif
      @endif
      
      <li><a href="javascript:void(0)" >Voter Turn Out<span class="arrow-down"></span></a>
              <ul>
			  <li><a href="{{url('acceo/turnout/AcCeoMissedAc')}}">ACs Not Filled</a></li>
                <li><a href="{{url('acceo/turnout/estimate-poll-percent')}}">Estimate Poll Percentage</a></li>
                <li><a href="{{url('acceo/turnout/CeoPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
                <li><a   href="{{url('acceo/turnout/AcCeoEndOfPoll')}}">End Of Poll</a></li>
                <li><a href="{{url('acceo/turnout/EndOfPollFinalised')}}">End Of Poll Finalised</a></li>
              </ul>
          </li>
            

               

        
          <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
             <ul>
                <li><a rel="" href="{{url('/acceo/allmasters')}}"  >Add/Update Master Data </a></li>
        <li><a rel="" href="{{url('/acceo/offlinePermission')}}"  > Offline permission Module</a></li>
                 <li><a rel="" href="{{url('/acceo/allPermissionRequest')}}"  > Accept/Reject permission</a></li>
                <li><a rel="" href="{{url('/acceo/permissioncount')}}"  > Permission Report</a></li>
                <li><a rel="" href="{{url('/acceo/agentCreation')}}"  > Create CEO-Agent</a></li>
              </ul>
              
         </li>

        <li><a href="javascript:void(0)" >PS Wise Counting Report<span class="arrow-down"></span></a>
            <ul>  
           <li><a rel="" href="{{url('acceo/counting/report_state')}}"  >Table Scheduled</a></li>
                 <li><a rel="" href="{{url('/acceo/counting/BoothCountingStatusCeo')}}"  >Counting Status Report</a></li>
                 <li><a rel="" href="{{url('/acceo/counting/schedule-report')}}"  >Scheduled Rounds Report</a></li>
                 <li><a rel="" href="{{url('/acceo/counting/boothround-wise-report')}}"  >Round  Wise Report</a></li>
                 <li><a rel="" href="{{url('/acceo/counting/constituency-wise-report')}}"  >AC Result Report</a></li>
                 <li><a rel="" href="{{url('/acceo/booth-counting/active-user-report')}}">Active User Report</a></li>
                 <li><a rel="" href="{{url('/acceo/booth-counting/candidate-wise-report')}}">Candidate Wise Report</a></li>
				 <li><a rel="" href="{{url('/acceo/counting/get_form_20')}}">Generate Form 20</a></li>
                 <li><a rel="" href="{{url('/acceo/booth-counting/form21-download')}}"> <span>Download Form 21 C/D</span></a></li>
            </ul>
          </li>

        <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul>
                  <li><a rel="" href="{{url('/acceo/nomination-report')}}"  >Nomination Report</a></li>
        <!-- waseem asgar -->
                 <li><a rel="" href="{{url('/acceo/report/scrutiny')}}"  >Scrutiny Report</a></li> 
         <li><a rel="" href="{{url('/acceo/districtvalue')}}"  >DistrictWise Permission Report</a></li>
             <li><a rel="" href="{{url('/acceo/reportceo')}}"  >DateWise Permission Report</a></li> 
        <li><a rel="" href="{{url('/acceo/ceoreport')}}"  >Permission Raw Report</a></li>
       <li><a rel="" href="{{url('/acceo/partywise')}}"  >PartyWise Permission Report</a></li>
        <li><a rel="" href="{{url('/acceo/permissiontype')}}"  >PermissionWise Report</a></li>
       
        
              <!-- End waseem asgar -->
        
              <!--<li><a rel="" href="{{url('/acceo/aclist')}}"  >List Of acs With Candidate Details</a></li>
              <li><a rel="" href="{{ url('acceo/duplicate-symbol-view') }}"  >Duplicate Symbols</a></li>
              <li><a rel="" href="{{url('/acceo/duplicateparties')}}"  > Duplicate Parties  </a></li>
              
              
              <li><a rel="" href="{{url('/acceo/candidate-symbol-no-200')}}"  >List of Candidates with Symbol No 200</a></li>
      <li><a rel="" href="{{url('/acceo/login-detail')}}"  >CEO Officer Login Report</a></li>-->
    
          <!--PRADEEP REPORTS LINKS STARTS-->
              <li><a rel="" href="{{url('/acceo/CountingStatus')}}"  >Counting Status Report</a></li>
             <!-- <li><a rel="" href="{{url('/acceo/CeoElectionSchedule')}}"  >Election Schedule</a></li>-->
			 <li><a rel="" href="{{url('/acceo/ElectionScheduleState')}}"  >Election Schedule</a></li> 
              <!--PRADEEP REPORTS LINKS ENDS-->

            </ul>
          </li>
     <!-- <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
            <ul>
                 <li><a rel="" href="{{url('/acceo/CountingStatus')}}"  >Counting Status Report</a></li>
                 <li><a rel="" href="{{url('/acceo/schedule-report')}}"  >Scheduled Rounds Report</a></li>
                 <li><a rel="" href="{{url('/acceo/constituency-wise-report')}}"  >AC Result Report</a></li>
                 <li><a rel="" href="{{url('/eci/round-wise-report')}}"  >Round  Wise Report</a></li>
                 <li><a rel="" href="{{url('/acceo/candidate-wise-report')}}"  >Candidate Wise Report</a></li>
                 <li><a rel="" href="{{url('/acceo/form21-download')}}"  > <span>Download Form 21 C/D</span></a></li>
            </ul>
          </li> -->
      
	 	@if(is_valid_indexcard())
      <li><a href="javascript:void(0)" >Index Card<span class="arrow-down"></span></a>
            <ul>
      
              <!--WASEEM LINKS STARTS-->
                  <li><a   href="{!! url('acceo/elector/edit') !!}">Update Electors</a></li>
                  <li><a   href="{!! url('acceo/voters/edit') !!}">Update Voters</a></li>
                  <li><a   href="{!! url('acceo/indexcard/finalize') !!}">Finalize AC's</a></li>
                 <li><a   href="{!! url('acceo/index-card') !!}">Index Card Report</a></li>
                 <li><a rel="" href="{{url('/acceo/indexcard/IndexCardFinalize')}}"  >Index Card Finalization Report</a></li>
                 <!--<li><a   href="{!! url('/acceo/report/candidate') !!}">List of nominated candidate</a></li>-->
                               <!--WASEEM LINKS ENDS-->
            </ul>
          </li>
		@endif
		  
      <!-- Expenditure Section Start -->
        <li class="inactive"><a href="{{url('/acceo/statusExpdashboard')}}">Expenditure</a></li>
       <!-- Expenditure Section End -->
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
       <li><a rel="" href="{{url('/acceo/officer-details')}}"  > <span>Update Officer Details</span></a></li>
            <li><a rel="" href="{{url('/acceo/officer/reset-password')}}"  > Officer's Pin Reset</a></li>
    
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
            <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li> 
        </ul>
         @endif
        <!-- End CEO AC Model-->
    <!--  CEO Agent  AC Model-->
         @if($user_data->role_id=='23')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/acceo/dashboard')}}">Home</a></li>

      <li><a href="{{url('acceo/voting/list-schedule/state')}}">Poll Turn Out</a></li>

          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <ul>
            <li><a rel="" href="{{url('/acceo/candidate-finalize')}}"  > <span>List of Nomination Finalize</span></a></li>
            
            </ul>
          </li>
            <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
             <ul>
        <li><a rel="" href="{{url('/acceo/offlinePermission')}}"  > Offline permission Module</a></li>
              </ul>
              
         </li>
          
          
       
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul>
        <li><a rel="" href="{{url('/acceo/nomination-report')}}"  >Nomination Report</a></li>
        <!-- waseem asgar -->
              <li><a rel="" href="{{url('/acceo/report/scrutiny')}}"  >Scrutiny Report</a></li> 
			  <li><a rel="" href="{{url('/acceo/districtvalue')}}"  >DistrictWise Permission Report</a></li>
              <li><a rel="" href="{{url('/acceo/reportceo')}}"  >DateWise Permission Report</a></li> 
        <li><a rel="" href="{{url('/acceo/ceoreport')}}"  >Permission Raw Report</a></li>
       <li><a rel="" href="{{url('/acceo/partywise')}}"  >PartyWise Permission Report</a></li>
        <li><a rel="" href="{{url('/acceo/permissiontype')}}"  >PermissionWise Report</a></li>
              <!-- End waseem asgar -->
        
              <!--<li><a rel="" href="{{url('/acceo/aclist')}}"  >List Of acs With Candidate Details</a></li>
              <li><a rel="" href="{{ url('acceo/duplicate-symbol-view') }}"  >Duplicate Symbols</a></li>
              <li><a rel="" href="{{url('/acceo/duplicateparties')}}"  > Duplicate Parties  </a></li>
              
              
              <li><a rel="" href="{{url('/acceo/candidate-symbol-no-200')}}"  >List of Candidates with Symbol No 200</a></li>
      <li><a rel="" href="{{url('/acceo/login-detail')}}"  >CEO Officer Login Report</a></li>-->
    
          <!--PRADEEP REPORTS LINKS STARTS-->
              <li><a rel="" href="{{url('/acceo/CountingStatus')}}"  >Counting Status Report</a></li>
              <li><a rel="" href="{{url('/acceo/CeoElectionSchedule')}}"  >Election Schedule</a></li>
              <!--PRADEEP REPORTS LINKS ENDS-->

            </ul>
          </li>
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
           <!--<li><a rel="" href="{{url('/acceo/change-password')}}"  > Change Password</a></li>-->
            <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
           <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
            </ul>
         @endif
        <!--  End CEO Agent  AC Model-->

        <!-- DEOAC Login Section-->
       @if($user_data->role_id=='5')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/acdeo/dashboard')}}">Home</a></li>

    		  @if(Session::has('DB_id') && !in_array(Session::get('DB_id'),[5,2]))
          @if(in_array($st_code,$allowed_st_code) && in_array($dist_no,$allowed_dist_no) )
  <li><a href="javascript:void(0)" >Booth App<span class="arrow-down"></span></a>
    <ul>

     <li><a rel="" href="{{url('/acdeo/booth-app-revamp/dashboard')}}"  >Dashboard</a></li>
      <li><a rel="" href="#">Reports</a>
        <ul>
        	<li><a href="{{url('acdeo/booth-app-revamp/mapped-location-report/state/ac')}}">PS Location Mapping</a></li>
		  <li><a href="{{Common::generate_url('booth-app-revamp/officer-assignment-report/ac/ps')}}">Officer Assignment Report</a></li>
      <li><a href="{{url('acdeo/booth-app-revamp/elector-verify-report/state/ac')}}">Electors verification report</a></li>
		  <li><a href="{!! Common::generate_url('booth-app-revamp/poll-material/ac') !!}">Poll Material Report</a></li>
       <li><a href="{{url('acdeo/booth-app-revamp/poll-turnout-report/state/ac')}}">Poll Turnout Report</a></li>
       <li><a href="{{url('acdeo/booth-app-revamp/poll-event-report')}}">Poll Event Report</a></li>
       <li><a href="{{url('acdeo/booth-app-revamp/exemted-ps-count-report/state/ac')}}">Exempted PS Count Report</a></li>
	   <li><a href="{{url('acdeo/booth-app-revamp/exempt-turnout-report/state/ac')}}">Exempted PS Turnout Report</a></li>
       <!--<li><a href="{{url('acdeo/booth-app-revamp/exempt-turnout-report/state/ac')}}">Exempt Turnout Report</a></li>
	   <li><a href="{{url('acdeo/booth-app-revamp/ac/blo-pro-difference')}}">BLO/PRO Turnout</a></li>
	   <li><a href="{{url('acdeo/booth-app-revamp/evm-comparision/state/ac')}}">Evm Comparision</a></li>-->
     
        </ul>
      </li>

    </ul>
  </li>
  @endif

          @endif
      
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
            <ul>
            <li><a class="dropdown-item" href="{!! url('acdeo/nomination/list-of-nomination') !!}">List of nominated candidate</a></li>
			 <li><a rel="" href="{{url('/acdeo/candidate-finalize')}}"> <span>List of Nomination Finalize</span></a></li>

            </ul>
          </li>
            <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
              <ul>
                 <!-- <li><a rel="" href="{{url('/acdeo/allmasters')}}"  >Add/Update Master Data </a></li>-->
              @if($user_data->st_code=='U01' || $user_data->st_code=='U02' || $user_data->st_code=='U03' || $user_data->st_code=='U04' || $user_data->st_code=='U05' || $user_data->st_code=='U06' || $user_data->st_code=='U07' || $user_data->st_code=='S16')
                <li><a rel="" href="{{url('/acdeo/allmasters')}}"  >Add/Update Master Data </a></li>
                @endif
        <li><a rel="" href="{{url('/acdeo/offlinePermission')}}"  > Offline permission Module</a></li>
                <li><a rel="" href="{{url('/acdeo/allPermissionRequest')}}"  > Accept/Reject permission</a></li>
                 <li><a rel="" href="{{url('/acdeo/agentCreation')}}"  > Create DEO-Agent</a></li>
          </ul>

         </li>
		 
		 <li><a href="javascript:void(0)">Voter Turn Out<span class="arrow-down"></span></a>
            <ul>
              <li><a href="{{url('acdeo/turnout/estimate-poll-percent/state/ac')}}">Estimate Poll Percentage</a></li>
              <li><a href="{{url('acdeo/turnout/AcDeoEndOfPollAc')}}">End Of Poll</a></li>
              {{-- <li><a href="{{url('eci/turnout/EciPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
              <li><a href="{{url('eci/turnout/EndOfPollFinalised')}}">End Of Poll Finalised</a></li> --}}
            </ul>
          </li>   
        <li><a href="javascript:void(0)">PS Wise Counting Report<span class="arrow-down"></span></a>
          <ul>
		  <li><a rel="" href="{{url('acdeo/counting/report_state/state/ac?')}}/state={{base64_encode($user_data->st_code)}}"  >Table Scheduled</a></li>
		  <li><a rel="" href="{{url('acdeo/counting/BoothCountingStatusCeo')}}"  >Counting Status Report</a></li>
		  <li><a rel="" href="{{url('/acdeo/counting/constituency-wise-report')}}"  >AC Result Report</a></li>
          <li><a rel="" href="{{url('/acdeo/booth-counting/active-user-report')}}">Active User Report</a></li>
		  <li><a rel="" href="{{url('/acdeo/counting/get_form_20')}}">Generate Form 20</a></li>
		  <li><a rel="" href="{{url('/acdeo/booth-counting/form21-download')}}">Download Form21 C/D</a></li>
          <li><a rel="" href="{{url('/acdeo/booth-counting/candidate-wise-report')}}">Candidate Wise Report</a></li>
		<li><a rel="" href="{{url('/acdeo/counting/boothround-wise-report')}}"  >Round  Wise Report</a></li>
          </ul>
      </li>
	  
	  

          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul>
              <!--<li><a rel="" href="{{url('/acdeo/datewisereport')}}"  >Nomination Report</a></li>
               <li><a rel="" href="{{url('/acdeo/permissioncount')}}"  > Permission Report</a></li>-->
			   <li><a rel="" href="{{url('/acdeo/nomination-report')}}"  >Nomination Report</a></li>
          <li><a rel="" href="{{url('/acdeo/reportdeo')}}"  >DateWise Permission Report</a></li>
        <li><a rel="" href="{{url('/acdeo/permissionraw')}}"  >Permission Raw Report</a></li>
        <li><a rel="" href="{{url('/acdeo/partywise')}}"  >PartyWise Permission Report</a></li>
        <li><a rel="" href="{{url('/acdeo/permissiontype')}}"  >PermissionWise Report</a></li>
            </ul>
          </li>
      
        

      <!-- <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
            <ul>
       <li><a rel="" href="{{url('/acdeo/schedule-report')}}"  >Scheduled Rounds Report</a></li>
       <li><a rel="" href="{{url('/acdeo/constituency-wise-report')}}"  >AC Result Report</a></li>
           <li><a rel="" href="{{url('/eci/round-wise-report')}}"  >Round  Wise Report</a></li>
            <li><a rel="" href="{{url('/acdeo/candidate-wise-report')}}"  >Candidate Wise Report</a></li>
            </ul>
          </li> -->
		  <!--
		  <li><a href="javascript:void(0)">Feedback<span class="arrow-down"></span></a>
            <ul>
              <li><a rel="" href="{{url('/acdeo/feedback')}}"  >Feedback Form</a></li>
            </ul>
      </li>-->
      <!-- Expenditure Section Start -->
      <li class="inactive"><a href="{{url('/acdeo/expdashboard')}}">Expenditure</a></li>
       <!-- Expenditure Section End -->
	   <li><a href="javascript:void(0)" >Index Card<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a href="{{ url('/acdeo/index-card') }}">Index Card Report</a></li>			  
            </ul>
          </li>
	   
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
		   <li><a rel="" href="{{url('/acdeo/officer-details')}}"  > <span>Update Officer Details</span></a></li>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
            <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li> 
        </ul>
         @endif
        <!-- End DEO ac Model-->
    <!--DEO PC AGENT URLS STARTS-->
       @if($user_data->role_id=='24')<ul class="float-right mainmenu">
       <li class="active"><a href="{{url('/acdeo/dashboard')}}">Home</a></li>
       <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
       <li><a rel="" href="{{url('/acdeo/offlinePermission')}}" class="dropdown-item"> Offline permission Module</a></li>
       </ul>

         </li>
		  <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul>
          <li><a rel="" href="{{url('/acdeo/reportdeo')}}"  >DateWise Permission Report</a></li>
        <li><a rel="" href="{{url('/acdeo/permissionraw')}}"  >Permission Raw Report</a></li>
        <li><a rel="" href="{{url('/acdeo/partywise')}}"  >PartyWise Permission Report</a></li>
        <li><a rel="" href="{{url('/acdeo/permissiontype')}}"  >PermissionWise Report</a></li>
            </ul>
          </li>
         
       <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
        <ul class="dropdown">
        <li><a rel="" href="{{url('/acdeo/changepassword')}}" class="dropdown-item"> Change Password</a></li>
         <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"><span class="d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
        </ul>
        </li></ul>
       @endif
 <!--DEO PC AGENT URLS ENDS-->
    <!--DEO PCI URLS STARTS-->
       @if($user_data->officerlevel=='PCI')<ul class="float-right mainmenu">
       <li class="active"><a href="{{url('/acdeo/dashboard')}}">Home</a></li>
       <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
       <li><a rel="" href="{{url('/acdeo/allPermissionRequest')}}"  > Accept/Reject permission</a></li></ul></li>
       <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
        <ul class="dropdown">
        <li><a rel="" href="{{url('/acdeo/changepassword')}}" class="dropdown-item"> Change Password</a></li>
         <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"><span class="d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
        </ul>
        </li></ul>
       @endif
       <!--DEO PCI URLS ENDS-->
    
    <!-- Index Card Eci Login Section-->
       @if($user_data->role_id=='27')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/eci-index/dashboard')}}">Home</a></li>
          <li><a href="#">Index Card<span class="arrow-down"></span></a>
			  <ul>
			  <li><a href="{{url('eci-index/index-card')}}">Index Card Report</a></li>
			  <li><a href="#">Statistical Reports</a>
				<ul>
					<li><a href="{{url('eci-index/statistical-report-listing')}}">Gen-Election Statistical Reports</a></li>
					<li><a href="{{url('eci-index/bye-election-verify-report')}}">Bye-Election Index Card Report</a></li>
				</ul>
			  </li>
			  <li><a rel="" href="{{url('/eci-index/indexcard/IndexCardFinalizeTotal')}}"  >Index Card Finalization Report</a></li>
			  <li><a rel="" href="{{url('/eci-index/indexcard/de-finalize-acs')}}"  >De-Finalize Constituency</a></li>
            </ul>
          </li>
         
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
             <li><a rel="" href="{{url('/logout')}}"  ><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
      
        </ul>
        @endif
    
    <!-- Index Card Eci Login Section-->
       @if($user_data->role_id=='28')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/eci-expenditure/dashboard')}}">Home</a></li>
          <li><a href="#">Expenditure<span class="arrow-down"></span></a>
      <ul>
      
            </ul>
          </li>
         
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
             <li><a rel="" href="{{url('/logout')}}"  ><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
      
        </ul>
        @endif
		
		<!-- eci subagent -->
        @if($user_data->role_id == '26')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('eci/dashboard')}}">Home</a></li>
         
           <li><a href="javascript:void(0)" >Voter Turn Out<span class="arrow-down"></span></a>
              <ul>
                <li><a href="{{url('eci/turnout/estimate-poll-percent')}}">Estimate Poll Percentage</a></li>
				<li><a href="{{url('eci/turnout/end-of-poll-percent')}}">End Of Poll Percent</a></li>
                <!--<li><a href="{{url('eci/turnout/EciPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
                <li><a href="{{url('eci/turnout/end-of-poll')}}">End Of Poll</a></li>
                <li><a href="{{url('eci/turnout/EndOfPollFinalised')}}">End Of Poll Finalised</a></li>-->
                <li><a href="{{url('eci/turnout/get_missed')}}">Ac's not Filled</a></li>
              </ul>
          </li>
			<li><a href="javascript:void(0)" >PS Wise Counting Report<span class="arrow-down"></span></a>
              <ul>
                <li><a rel="" href="{{url('/eci/booth-counting/form21c-download')}}">Download Form21 C/D</a></li>
              </ul>
          </li>
		  
		  <?php /* ?> 
		   <li><a href="javascript:void(0)" >Voter Turn Out<span class="arrow-down"></span></a>
              <ul>
                <li><a href="{{url('eci/turnout/estimate-poll-percent')}}">Estimate Poll Percentage</a></li>
                <li><a href="{{url('eci/turnout/EciPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
                <li><a href="{{url('eci/turnout/end-of-poll')}}">End Of Poll</a></li>
				<li><a href="{{url('eci/turnout/end-of-poll-percent')}}">End Of Poll Percent</a></li>
                <li><a href="{{url('eci/turnout/EndOfPollFinalised')}}">End Of Poll Finalised</a></li>
                <li><a href="{{url('eci/turnout/get_missed')}}">Ac's not Filled</a></li>
				<li><a href="{{url('eci/report/voting/end-of-poll-summary')}}">End of Poll Consolidate Report</a></li>
              </ul>
          </li>
		  
		   @if(Session::has('DB_id') && in_array(Session::get('DB_id'),[1,2,3,4,5,6]))
			  <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
          <ul>
            <li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}"  >Counting Status Report</a></li>
            <li><a rel="" href="{{url('/eci/schedule-report')}}"  >Scheduled Rounds Report</a></li>
            <li><a rel="" href="{{url('/eci/constituency-wise-report')}}"  >AC Result Report</a></li>
            <li><a rel="" href="{{url('/eci/round-wise-report')}}"  >Round  Wise Report</a></li>
            <li><a rel="" href="{{url('/eci/candidate-wise-report')}}"  >Candidate Wise Report</a></li>
            <li><a rel="" href="{{url('/eci/form21c-download')}}"  >Download Form21 C/D</a></li>
          </ul>
       </li>
       @else 
       <li><a href="javascript:void(0)" >Booth Counting Report<span class="arrow-down"></span></a>
            <ul>
              <li><a rel="" href="{{url('eci/counting/report_state')}}"  >Table Scheduled</a></li>
			  <!--<li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}"  >Counting Status Report</a></li>-->
              <li><a rel="" href="{{url('eci/counting/BoothCountingStatusReport')}}"  >Counting Status Report</a></li>
              <li><a rel="" href="{{url('/eci/counting/BoothCountingScheduleReport')}}"  >Scheduled Rounds Report</a></li>
			  
              <li><a rel="" href="{{url('/eci/counting/boothround-wise-report')}}"  >Round  Wise Report</a></li>
              <li><a rel="" href="{{url('/eci/counting/constituency-wise-report')}}"  >AC Result Report</a></li>
                  
              <li><a rel="" href="{{url('/eci/booth-counting/active-user-report')}}">Active User Report</a></li>
			  <li><a rel="" href="{{url('/eci/counting/get_form_20')}}">Generate Form 20</a></li>
              <li><a rel="" href="{{url('/eci/booth-counting/form21c-download')}}">Download Form21 C/D</a></li>
              <li><a rel="" href="{{url('/eci/booth-counting/candidate-wise-report')}}">Candidate Wise Report</a></li>
			  <li><a rel="" href="{{url('/eci/booth-counting/winning-candidate-list')}}">Winning Candidate Details</a></li>
			  
            </ul>
        </li>
		
       
      
      
      @endif
      @if($user_data->id=='1')
         <!--  <li><a href="{{url('eci/voting/list-schedule')}}">Poll Turn Out</a></li> -->

           
           <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul>
     
             <li><a rel="" href="{{url('/eci/EciActiveUsers')}}"  >Active Users Report</a></li>
             <!--<li><a rel="" href="{{url('/eci/EciElectionSchedule')}}"  >Election Schedule</a></li>-->
			 <li><a rel="" href="{{url('/eci/ElectionScheduleState')}}"  >Election Schedule</a></li>
             <li><a rel="" href="{{url('/eci/EciPartyData')}}"  >Party Data Report</a></li>
             <li><a rel="" href="{{url('/eci/EciSymbolData')}}"  >Symbol Data Report</a></li>
             <li><a rel="" href="{{url('/eci/EciPhaseInfoData')}}"  >Valid Nomination Report</a></li>
             <li><a rel="" href="{{url('/eci/EciNominationFinalized')}}"  >ACs Finalized</a></li>
     
            <li><a rel="" href="{{url('/eci/report/scrutiny/state')}}"  >Scrutiny Report</a></li> 
       

      
       
       <!--PRADEEP LINKS ENDS-->
       
          <li><a rel="" href="{{url('/eci/report')}}"  >DateWise Permission Report</a></li>
          <li><a rel="" href="{{url('/eci/districtreport')}}" class="dropdown-item">District DateWise Permission Report</a></li>
          <li><a rel="" href="{{url('/eci/partywise')}}"  >PartyWise Permission Report</a></li>
          <li><a rel="" href="{{url('/eci/permissiontype')}}"  >PermissionWise Report</a></li>
        
            </ul>
          </li>
      @endif
		  
		  
		  
		  <?php */ ?>
		  
		  
		  
		  
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
            <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
        </li>
      
        </ul>
         @endif
          <!-- eci subagent ends-->


        <!-- ECIac Login Section-->
       @if($user_data->role_id=='7' && $user_data->officername != 'boothapp')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/eci/dashboard')}}">Home</a></li>
		  
		  <li><a href="javascript:void(0)" >Booth App<span class="arrow-down"></span></a>
        <ul>
           <li><a rel="" href="{{url('/eci/booth-app-revamp/dashboard')}}"  >Dashboard</a></li>
		
          <li><a rel="" href="#">Reports</a>
      			<ul>
             <li><a href="{{url('eci/booth-app-revamp/mapped-location-report')}}">PS Location Mapping</a></li>


            
			  <li><a href="{{Common::generate_url('booth-app-revamp/officer-assignment-report')}}">Officer Assignment Report</a></li>
        <li><a href="{{url('eci/booth-app-revamp/elector-verify-report')}}">Electors verification report</a></li>
			  <li><a href="{!! Common::generate_url('booth-app-revamp/poll-material') !!}">Poll Material Report</a></li>
				<li><a href="{{url('eci/booth-app-revamp/poll-turnout-report')}}">Poll Turnout Report</a></li>
               <li><a href="{{url('eci/booth-app-revamp/poll-event-report?phase_no='.$phase_no)}}">Poll Event Report</a></li>
               <li><a href="{{url('eci/booth-app-revamp/exemted-ps-count-report')}}">Exempted PS Count Report</a></li>
			   <li><a href="{{url('eci/booth-app-revamp/exempt-turnout-report')}}">Exempted PS Turnout Report</a></li>
               
      				 <li><a href="{{url('eci/booth-app-revamp/state/blo-pro-difference')}}">BLO/PRO Turnout</a></li>
      				 <li><a href="{{url('eci/booth-app-revamp/evm-comparision/state/')}}">Evm Comparision</a></li>
					 <li><a href="{{url('eci/booth-app-revamp/pro-diary?phase_no='.$phase_no)}}">PRO Diary</a></li>
      				<li><a rel="" href="{{url('/eci/booth-app-revamp/get-form-17-a?phase_no='.$phase_no)}}">Forms</a></li>
            </ul>
      		</li>

        </ul>
      </li>
       <li><a href="javascript:void(0)" >Voter Turn Out<span class="arrow-down"></span></a>
              <ul>
                <li><a href="{{url('eci/turnout/estimate-poll-percent')}}">Estimate Poll Percentage</a></li>
                <li><a href="{{url('eci/turnout/EciPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
                <li><a href="{{url('eci/turnout/end-of-poll')}}">End Of Poll</a></li>
				<li><a href="{{url('eci/turnout/end-of-poll-percent')}}">End Of Poll Percent</a></li>
                <li><a href="{{url('eci/turnout/EndOfPollFinalised')}}">End Of Poll Finalised</a></li>
                <li><a href="{{url('eci/turnout/get_missed')}}">Ac's not Filled</a></li>
				<!--<li><a href="{{url('eci/report/voting/end-of-poll-summary')}}">End of Poll Consolidate Report</a></li>-->
              </ul>
          </li>
		  
		   @if(Session::has('DB_id') && in_array(Session::get('DB_id'),[1,2,3,4,5,6]))
			  <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
          <ul>
            <li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}"  >Counting Status Report</a></li>
            <li><a rel="" href="{{url('/eci/schedule-report')}}"  >Scheduled Rounds Report</a></li>
            <li><a rel="" href="{{url('/eci/constituency-wise-report')}}"  >AC Result Report</a></li>
            <li><a rel="" href="{{url('/eci/round-wise-report')}}"  >Round  Wise Report</a></li>
            <li><a rel="" href="{{url('/eci/candidate-wise-report')}}"  >Candidate Wise Report</a></li>
            <li><a rel="" href="{{url('/eci/form21c-download')}}"  >Download Form21 C/D</a></li>
          </ul>
       </li>
       @else 
       <li><a href="javascript:void(0)" >Booth Counting Report<span class="arrow-down"></span></a>
            <ul>
              <li><a rel="" href="{{url('eci/counting/report_state')}}"  >Table Scheduled</a></li>
			  <!--<li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}"  >Counting Status Report</a></li>-->
              <li><a rel="" href="{{url('eci/counting/BoothCountingStatusReport')}}"  >Counting Status Report</a></li>
              <li><a rel="" href="{{url('/eci/counting/BoothCountingScheduleReport')}}"  >Scheduled Rounds Report</a></li>
			  
              <li><a rel="" href="{{url('/eci/counting/boothround-wise-report')}}"  >Round  Wise Report</a></li>
              <li><a rel="" href="{{url('/eci/counting/constituency-wise-report')}}"  >AC Result Report</a></li>
                  
              <li><a rel="" href="{{url('/eci/booth-counting/active-user-report')}}">Active User Report</a></li>
			  <li><a rel="" href="{{url('/eci/counting/get_form_20')}}">Generate Form 20</a></li>
              <li><a rel="" href="{{url('/eci/booth-counting/form21c-download')}}">Download Form21 C/D</a></li>
              <li><a rel="" href="{{url('/eci/booth-counting/candidate-wise-report')}}">Candidate Wise Report</a></li>
			  <li><a rel="" href="{{url('/eci/booth-counting/winning-candidate-list')}}">Winning Candidate Details</a></li>
			  <li><a rel="" href="{{url('/eci/booth-counting/result-sheet-report')}}"  >Result Sheet Report</a></li>
            </ul>
        </li>
		
       
      
      
      @endif
      @if($user_data->id=='1')
         <!--  <li><a href="{{url('eci/voting/list-schedule')}}">Poll Turn Out</a></li> -->

           
           <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul>
      <!--PRADEEP LINKS STARTS-->
             <li><a rel="" href="{{url('/eci/EciActiveUsers')}}"  >Active Users Report</a></li>
             <!--<li><a rel="" href="{{url('/eci/EciElectionSchedule')}}"  >Election Schedule</a></li>-->
			 <li><a rel="" href="{{url('/eci/ElectionScheduleState')}}"  >Election Schedule</a></li>
             <li><a rel="" href="{{url('/eci/EciPartyData')}}"  >Party Data Report</a></li>
             <li><a rel="" href="{{url('/eci/EciSymbolData')}}"  >Symbol Data Report</a></li>
             <li><a rel="" href="{{url('/eci/EciPhaseInfoData')}}"  >Valid Nomination Report</a></li>
             <li><a rel="" href="{{url('/eci/EciNominationFinalized')}}"  >ACs Finalized</a></li>
        <!-- waseem asgar -->
            <li><a rel="" href="{{url('/eci/report/scrutiny/state')}}"  >Scrutiny Report</a></li> 
       
 <!--PRADEEP LINKS ENDS-->
      
       
       <!--PRADEEP LINKS ENDS-->
       
          <li><a rel="" href="{{url('/eci/report')}}"  >DateWise Permission Report</a></li>
          <li><a rel="" href="{{url('/eci/districtreport')}}" class="dropdown-item">District DateWise Permission Report</a></li>
          <li><a rel="" href="{{url('/eci/partywise')}}"  >PartyWise Permission Report</a></li>
          <li><a rel="" href="{{url('/eci/permissiontype')}}"  >PermissionWise Report</a></li>
        
            </ul>
          </li>
      @endif
       <li><a href="javascript:void(0)">Index Card<span class="arrow-down"></span></a>
            <ul>
                 <li><a   href="{!! url('eci/index-card') !!}">Index Card Report</a></li>
				 <li><a class="dropdown-item" href="{!! url('eci/nomination/list-of-nomination') !!}">List of nominated candidates</a></li>
                 <li><a rel="" href="{{url('/eci/indexcard/IndexCardFinalizeTotal')}}"  >Index Card Finalization Report</a></li> 
                  <li><a   href="{!! url('eci/de-finalize-log') !!}"> Candidates Log Reports</a></li> 
                              
            </ul>
          </li>
          
          
 
       
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
		   <li><a rel="" href="{{url('eci/setting/broadcast')}}">Broadcast Message</a></li>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
           <li><a rel="" href="{{url('/logout')}}"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
        </ul>
         @endif
        <!-- End ECI ac Model-->
    <!--Start ECI Expenditure AC Model-->
         @if($user_data->role_id=='28')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/eci-expenditure/expdashboard')}}">Home</a></li>
          <li class=""><a href="{{url('/eci-expenditure/mis-officer')}}">Expenditure</a></li>
          <li><a href="{{url('/logout')}}" class="nav-link logout"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
        
        </ul>
         @endif
		 
		 
		 <!-- only for boothapp users -->
		  @if(Session::has('DB_id') && !in_array(Session::get('DB_id'),[5,2]))
		  
		 @if($user_data->role_id == '7' && $user_data->officername == 'boothapp')
        <ul class="float-right mainmenu">
		  <li class="active"><a href="{{url('/eci/dashboard')}}">Home</a></li>
		  
		  <li><a href="javascript:void(0)" >Booth App<span class="arrow-down"></span></a>
        <ul>
           <li><a rel="" href="{{url('/eci/booth-app-revamp/dashboard')}}"  >Dashboard</a></li>
		
          <li><a rel="" href="#">Reports</a>
      			<ul>
             <li><a href="{{url('eci/booth-app-revamp/mapped-location-report')}}">PS Location Mapping</a></li>


            
			  <li><a href="{{Common::generate_url('booth-app-revamp/officer-assignment-report')}}">Officer Assignment Report</a></li>
        <li><a href="{{url('eci/booth-app-revamp/elector-verify-report')}}">Electors verification report</a></li>
			  <li><a href="{!! Common::generate_url('booth-app-revamp/poll-material') !!}">Poll Material Report</a></li>
				<li><a href="{{url('eci/booth-app-revamp/poll-turnout-report')}}">Poll Turnout Report</a></li>
               <li><a href="{{url('eci/booth-app-revamp/poll-event-report?phase_no='.$phase_no)}}">Poll Event Report</a></li>
      				 <li><a href="{{url('eci/booth-app-revamp/state/blo-pro-difference')}}">BLO/PRO Turnout</a></li>
      				 <li><a href="{{url('eci/booth-app-revamp/evm-comparision/state/')}}">Evm Comparision</a></li>
					 <li><a href="{{url('eci/booth-app-revamp/pro-diary?phase_no='.$phase_no)}}">PRO Diary</a></li>
					 
					 
      				<li><a rel="" href="{{url('/eci/booth-app-revamp/get-form-17-a?phase_no='.$phase_no)}}">Forms</a></li>
            </ul>
      		</li>

        </ul>
      </li>
       <li><a href="javascript:void(0)" >Voter Turn Out<span class="arrow-down"></span></a>
              <ul>
                <li><a href="{{url('eci/turnout/estimate-poll-percent')}}">Estimate Poll Percentage</a></li>
                <li><a href="{{url('eci/turnout/EciPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
                <li><a href="{{url('eci/turnout/end-of-poll')}}">End Of Poll</a></li>
				<li><a href="{{url('eci/turnout/end-of-poll-percent')}}">End Of Poll Percent</a></li>
                <li><a href="{{url('eci/turnout/EndOfPollFinalised')}}">End Of Poll Finalised</a></li>
                <li><a href="{{url('eci/turnout/get_missed')}}">Ac's not Filled</a></li>
				<!--<li><a href="{{url('eci/report/voting/end-of-poll-summary')}}">End of Poll Consolidate Report</a></li>-->
              </ul>
          </li>
		  
		   @if(Session::has('DB_id') && in_array(Session::get('DB_id'),[1,2,3,4,5,6]))
			  <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
          <ul>
            <li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}"  >Counting Status Report</a></li>
            <li><a rel="" href="{{url('/eci/schedule-report')}}"  >Scheduled Rounds Report</a></li>
            <li><a rel="" href="{{url('/eci/constituency-wise-report')}}"  >AC Result Report</a></li>
            <li><a rel="" href="{{url('/eci/round-wise-report')}}"  >Round  Wise Report</a></li>
            <li><a rel="" href="{{url('/eci/candidate-wise-report')}}"  >Candidate Wise Report</a></li>
            <li><a rel="" href="{{url('/eci/form21c-download')}}"  >Download Form21 C/D</a></li>
          </ul>
       </li>
       @else 
       <li><a href="javascript:void(0)" >Booth Counting Report<span class="arrow-down"></span></a>
            <ul>
              <li><a rel="" href="{{url('eci/counting/report_state')}}"  >Table Scheduled</a></li>
			  <!--<li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}"  >Counting Status Report</a></li>-->
              <li><a rel="" href="{{url('eci/counting/BoothCountingStatusReport')}}"  >Counting Status Report</a></li>
              <li><a rel="" href="{{url('/eci/counting/BoothCountingScheduleReport')}}"  >Scheduled Rounds Report</a></li>
			  
              <li><a rel="" href="{{url('/eci/counting/boothround-wise-report')}}"  >Round  Wise Report</a></li>
              <li><a rel="" href="{{url('/eci/counting/constituency-wise-report')}}"  >AC Result Report</a></li>
                  
              <li><a rel="" href="{{url('/eci/booth-counting/active-user-report')}}">Active User Report</a></li>
			  <li><a rel="" href="{{url('/eci/counting/get_form_20')}}">Generate Form 20</a></li>
              <li><a rel="" href="{{url('/eci/booth-counting/form21c-download')}}">Download Form21 C/D</a></li>
              <li><a rel="" href="{{url('/eci/booth-counting/candidate-wise-report')}}">Candidate Wise Report</a></li>
			  <li><a rel="" href="{{url('/eci/booth-counting/winning-candidate-list')}}">Winning Candidate Details</a></li>
            </ul>
        </li>
		
       
      
      
      @endif
	   <li><a href="{{url('/logout')}}" class="nav-link logout"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
	   </ul>
         @endif
		 @endif
		 
		@if($user_data->role_id=='37')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/maintenance/dashboard')}}">Home</a></li>
          <li class=""><a href="{{url('/maintenance/table')}}">Tables</a></li>
          <li class=""><a href="{{url('/maintenance/setting/setting')}}">Setting</a></li>
          <li class=""><a href="{{url('/maintenance/officer/reset-password')}}">Reset Password</a></li>
		   <li class=""><a href="{{url('/maintenance/booth-app-revamp/send_sms_to_boothapp')}}" target="_blank">Send BoothApp Link to Officers</a></li>
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
            <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>

        </ul>
         @endif

         @if($user_data->role_id=='39')
          <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('etpbs/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
            <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>

        </ul>
        @endif
		
		@if($user_data->role_id=='42')
          <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/nfd/dashboard')}}">Home</a></li>
          <li class="active"><a href="{{url('/nfd/nomination')}}">Nomination</a></li>
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul>
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
            <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>

        </ul>
        @endif
		 
		 
        <!-- End ECI Expenditure AC Model-->
    
      </div>
      
	  
	  
	  
	  </div>
      </div>
  <!--     <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div> -->

    </nav>
   </header>
   
   <?php
$setting = \App\models\Admin\SettingModel::get_first_result('config');
if($setting && $setting['key'] == 'message'){
?>
<div class="alert-warning text-center">
<marquee>
{{$setting['value']}}
</marquee>
</div>
<?php } ?>
   
