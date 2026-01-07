 
<header class="header">
   <nav>
      <div class="nav-header">
    <div class="container-fluid d-flex flex-md-row align-items-md-center">
    <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
     <!-- ROAC Login Section-->
       @if($user_data->role_id=='19' || $user_data->role_id=='21')
        <?php   

      $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
      if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
                $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
              }
      $seched=getschedulebyid($ele_details->ScheduleID);
      $sechdul=checkscheduledetails($seched); //dd($sechdul);
 ?>
        <ul class="float-right">
          <li class="active"><a href="{{url('/roac/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
          <ul class="dropdown">
             @if($cand_finalize_ro=='0')
             
            <li><a rel="" href="{{url('/roac/createnomination')}}" class="dropdown-item"> <span>Nomination</span></a></li>
            <li><a rel="" href="{{url('/roac/multiplenomination')}}" class="dropdown-item"> <span>Multiple Nomination</span></a></li>
           @endif
            <li><a rel="" href="{{url('/roac/candidateaffidavit')}}" class="dropdown-item"> <span>Upload Affidavit</span></a></li>
             
             
            <li><a rel="" href="{{url('/roac/counteraffidavit')}}" class="dropdown-item"> <span>Upload Counter Affidavit</span></a></li>
             
            <li><a rel="" href="{{url('/roac/listnomination')}}" class="dropdown-item"> <span>List of Applicants</span></a></li>
             @if($cand_finalize_ro=='0')
             
            <li><a rel="" href="{{url('/roac/scrutiny-candidates')}}" class="dropdown-item"> <span>Scrutiny of Candidates</span></a></li>
             <li><a rel="" href="{{url('/roac/accepted-candidate')}}" class="dropdown-item">Mark validly nominated candidates</a></li>
            <li><a rel="" href="{{url('/roac/withdrawn-candidates')}}" class="dropdown-item"> <span>Withdrawl of Candidates</span></a></li>
            <li><a rel="" href="{{url('/roac/symbol-upload')}}" class="dropdown-item">Assign Symbol </a></li>
             @endif   
            
           <li><a rel="" href="{{url('/roac/contested-application')}}" class="dropdown-item"> Contesting Candidates</a></li> 
             
            </ul>
          </li>
           <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/roac/permission/allmasters')}}" class="dropdown-item">Add/Update Master Data </a></li>
                <li><a rel="" href="{{url('/roac/permission/offlinePermission')}}" class="dropdown-item"> Offline permission Module</a></li>
                <li><a rel="" href="{{url('/roac/permission/allPermissionRequest')}}" class="dropdown-item"> Accept/Reject permission</a></li>
                <li><a rel="" href="{{url('/roac/permission/agentCreation')}}" class="dropdown-item"> Create Agent</a></li>
            </ul>

         </li>
          <li><a href="javascript:void(0)" >Voter Turnout<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <!--<li><a rel="" href="{{url('/roac/voting/list-schedule')}}" class="dropdown-item">List of Poll Turnout</a></li>-->
				<li><a rel="" href="{{url('/roac/ElectorsDetails')}}" class="dropdown-item">Electors Details</a></li>
                <li><a rel="" href="{{url('/roac/voting/schedule-entry')}}" class="dropdown-item">End of Poll Turnout</a></li>
                
            </ul>

         </li>   
          
          
           <li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
         <!-- <li><a rel="" href="{{url('/roac/counting/prepare-counting')}}" class="dropdown-item">Prepare Counting Data</a></li>
          <li><a rel="" href="{{url('/roac/counting/round-schedule')}}" class="dropdown-item"> Round Schedule </a></li>
          <li><a rel="" href="{{url('/roac/counting/counting-data-entry')}}" class="dropdown-item"> Counting Data Entry </a></li>
          <li><a rel="" href="{{url('/roac/counting/postal-data-entry')}}" class="dropdown-item"> Postal Ballot Votes </a></li>
          <li><a rel="" href="{{url('/roac/counting/round-wise-entry')}}" class="dropdown-item">Round Wise Entry Details</a></li>
          <li><a rel="" href="{{url('/roac/counting/counting-results')}}" class="dropdown-item"> Results Declaration </a></li>-->
           
           </ul> 
          </li> 
       <!--<li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
             <li><a rel="" href="{{url('/roac/electors-ropollingstationlist')}}" class="dropdown-item"> <span>Electors & PS Info</span></a></li>
            </ul>
          </li> -->
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
          <ul class="dropdown">
		  <li><a rel="" href="{{url('/roac/datewisereport')}}" class="dropdown-item">Nomination Report</a></li> 
		   <!-- #waseem -->
          <li><a rel="" href="{{url('/roac/report/scrutiny')}}" class="dropdown-item">Scrutiny Report</a></li> 
		  
	<li><a rel="" href="{{url('/roac/reportro')}}" class="dropdown-item">Datewise Permission Report</a></li> 
		    	
	<li><a rel="" href="{{url('/roac/permissionraw')}}" class="dropdown-item"> Permission Raw Report</a></li> 
          
	<li><a rel="" href="{{url('/roac/partywise')}}" class="dropdown-item">PartyWise Permission Report</a></li> 
          	
	<li><a rel="" href="{{url('/roac/permissiontype')}}" class="dropdown-item">PermissionWise Report</a></li> 
 
           <!-- <li><a rel="" href="{{url('/roac/permission/permissioncount')}}" class="dropdown-item"> Permission Report</a></li>
          <li><a rel="" href="{{url('/ropc/datewisereport')}}" class="dropdown-item">Nomination Report</a></li>  
          <li><a rel="" href="{{url('/ropc/form3A-report')}}" class="dropdown-item">Form 3A Report</a></li>   
          <li><a rel="" href="{{url('/ropc/form4A-report')}}" class="dropdown-item">Form 4A Report</a></li> -->      
          </ul>
          </li>
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <!--<li><a rel="" href="{{url('/roac/changepassword')}}" class="dropdown-item"> Change Password</a></li>-->
            <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
      
        </ul>
         @endif
        <!-- End RO ACModel-->


      

        <!-- CEOAC Login Section-->
       @if($user_data->role_id=='4')
        <ul class="float-right">
          <li class="active"><a href="{{url('/acceo/dashboard')}}">Home</a></li>
		  <li><a href="javascript:void(0)" >Voter Turn Out<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a class="dropdown-item" href="{{url('acceo/AcCeoEndOfPoll')}}">End Of Poll</a></li>
			</ul>
          </li>

      <li><a href="{{url('acceo/voting/list-schedule/state')}}">Poll Turn Out</a></li>

          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <ul class="dropdown">
            <li><a rel="" href="{{url('/acceo/candidate-finalize')}}" class="dropdown-item"> <span>List of Nomination Finalize</span></a></li>
            
            </ul>
          </li>
            <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
             <ul class="dropdown">
                <li><a rel="" href="{{url('/acceo/allmasters')}}" class="dropdown-item">Add/Update Master Data </a></li>
				<li><a rel="" href="{{url('/acceo/offlinePermission')}}" class="dropdown-item"> Offline permission Module</a></li>
                 <li><a rel="" href="{{url('/acceo/allPermissionRequest')}}" class="dropdown-item"> Accept/Reject permission</a></li>
                <li><a rel="" href="{{url('/acceo/permissioncount')}}" class="dropdown-item"> Permission Report</a></li>
                <li><a rel="" href="{{url('/acceo/agentCreation')}}" class="dropdown-item"> Create CEO-Agent</a></li>
              </ul>
              
         </li>
          <!--<li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="#" class="dropdown-item">comming </a></li>
            <li><a rel="" href="#" class="dropdown-item">Comming </a></li>
             
           </ul>
          </li>-->
           <li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/acceo/officer-details')}}" class="dropdown-item"> <span>Update Officer Details</span></a></li>
			  <li><a rel="" href="{{url('/acceo/electors-pollingstationlist')}}" class="dropdown-item"> <span>Electors & PS Info</span></a></li>
            <!--<li><a rel="" href="{{url('/acceo/psinfo')}}" class="dropdown-item"> <span>PS Details & AMF</span></a></li>-->
            </ul> 
          </li>
       
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
				<li><a rel="" href="{{url('/acceo/nomination-report')}}" class="dropdown-item">Nomination Report</a></li>
				<!-- waseem asgar -->
              <li><a rel="" href="{{url('/acceo/report/scrutiny')}}" class="dropdown-item">Scrutiny Report</a></li> 
			   <li><a rel="" href="{{url('/acceo/districtvalue')}}" class="dropdown-item">DistrictWise Permission Report</a></li>
              <li><a rel="" href="{{url('/acceo/reportceo')}}" class="dropdown-item">DateWise Permission Report</a></li> 
			  <li><a rel="" href="{{url('/acceo/ceoreport')}}" class="dropdown-item">Permission Raw Report</a></li>
			 <li><a rel="" href="{{url('/acceo/partywise')}}" class="dropdown-item">PartyWise Permission Report</a></li>
			  <li><a rel="" href="{{url('/acceo/permissiontype')}}" class="dropdown-item">PermissionWise Report</a></li>
			 
			  
              <!-- End waseem asgar -->
				
              <!--<li><a rel="" href="{{url('/acceo/aclist')}}" class="dropdown-item">List Of acs With Candidate Details</a></li>
              <li><a rel="" href="{{ url('acceo/duplicate-symbol-view') }}" class="dropdown-item">Duplicate Symbols</a></li>
              <li><a rel="" href="{{url('/acceo/duplicateparties')}}" class="dropdown-item"> Duplicate Parties  </a></li>
              
              
              <li><a rel="" href="{{url('/acceo/candidate-symbol-no-200')}}" class="dropdown-item">List of Candidates with Symbol No 200</a></li>
      <li><a rel="" href="{{url('/acceo/login-detail')}}" class="dropdown-item">CEO Officer Login Report</a></li>-->
	  
	        <!--PRADEEP REPORTS LINKS STARTS-->
              <li><a rel="" href="{{url('/acceo/CountingStatus')}}" class="dropdown-item">Counting Status Report</a></li>
              <li><a rel="" href="{{url('/acceo/CeoElectionSchedule')}}" class="dropdown-item">Election Schedule</a></li>
              <!--PRADEEP REPORTS LINKS ENDS-->

            </ul>
          </li>
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <!--<li><a rel="" href="{{url('/acceo/change-password')}}" class="dropdown-item"> Change Password</a></li>-->
            <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
            </ul>
          </li>
          
        </ul>
         @endif
        <!-- End CEO AC Model-->
		<!--  CEO Agent  AC Model-->
         @if($user_data->role_id=='23')
        <ul class="float-right">
          <li class="active"><a href="{{url('/acceo/dashboard')}}">Home</a></li>

      <li><a href="{{url('acceo/voting/list-schedule/state')}}">Poll Turn Out</a></li>

          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <ul class="dropdown">
            <li><a rel="" href="{{url('/acceo/candidate-finalize')}}" class="dropdown-item"> <span>List of Nomination Finalize</span></a></li>
            
            </ul>
          </li>
            <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
             <ul class="dropdown">
<!--                <li><a rel="" href="{{url('/acceo/allmasters')}}" class="dropdown-item">Add/Update Master Data </a></li>-->
				<li><a rel="" href="{{url('/acceo/offlinePermission')}}" class="dropdown-item"> Offline permission Module</a></li>
<!--                 <li><a rel="" href="{{url('/acceo/allPermissionRequest')}}" class="dropdown-item"> Accept/Reject permission</a></li>-->
                <li><a rel="" href="{{url('/acceo/permissioncount')}}" class="dropdown-item"> Permission Report</a></li>
<!--                <li><a rel="" href="{{url('/acceo/agentCreation')}}" class="dropdown-item"> Create CEO-Agent</a></li>-->
              </ul>
              
         </li>
          <!--<li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="#" class="dropdown-item">comming </a></li>
            <li><a rel="" href="#" class="dropdown-item">Comming </a></li>
             
           </ul>
          </li>-->
           <li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/acceo/officer-details')}}" class="dropdown-item"> <span>Update Officer Details</span></a></li>
			  <li><a rel="" href="{{url('/acceo/electors-pollingstationlist')}}" class="dropdown-item"> <span>Electors & PS Info</span></a></li>
            <!--<li><a rel="" href="{{url('/acceo/psinfo')}}" class="dropdown-item"> <span>PS Details & AMF</span></a></li>-->
            </ul> 
          </li>
       
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
				<li><a rel="" href="{{url('/acceo/nomination-report')}}" class="dropdown-item">Nomination Report</a></li>
				<!-- waseem asgar -->
              <li><a rel="" href="{{url('/acceo/report/scrutiny')}}" class="dropdown-item">Scrutiny Report</a></li> 
              <li><a rel="" href="{{url('/acceo/reportceo')}}" class="dropdown-item">DateWise Permission Report</a></li> 
			  <li><a rel="" href="{{url('/acceo/ceoreport')}}" class="dropdown-item">Permission Raw Report</a></li>
			 <li><a rel="" href="{{url('/acceo/partywise')}}" class="dropdown-item">PartyWise Permission Report</a></li>
			  <li><a rel="" href="{{url('/acceo/permissiontype')}}" class="dropdown-item">PermissionWise Report</a></li>
              <!-- End waseem asgar -->
				
              <!--<li><a rel="" href="{{url('/acceo/aclist')}}" class="dropdown-item">List Of acs With Candidate Details</a></li>
              <li><a rel="" href="{{ url('acceo/duplicate-symbol-view') }}" class="dropdown-item">Duplicate Symbols</a></li>
              <li><a rel="" href="{{url('/acceo/duplicateparties')}}" class="dropdown-item"> Duplicate Parties  </a></li>
              
              
              <li><a rel="" href="{{url('/acceo/candidate-symbol-no-200')}}" class="dropdown-item">List of Candidates with Symbol No 200</a></li>
      <li><a rel="" href="{{url('/acceo/login-detail')}}" class="dropdown-item">CEO Officer Login Report</a></li>-->
	  
	        <!--PRADEEP REPORTS LINKS STARTS-->
              <li><a rel="" href="{{url('/acceo/CountingStatus')}}" class="dropdown-item">Counting Status Report</a></li>
              <li><a rel="" href="{{url('/acceo/CeoElectionSchedule')}}" class="dropdown-item">Election Schedule</a></li>
              <!--PRADEEP REPORTS LINKS ENDS-->

            </ul>
          </li>
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <!--<li><a rel="" href="{{url('/acceo/change-password')}}" class="dropdown-item"> Change Password</a></li>-->
            <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
            </ul>
         @endif
        <!--  End CEO Agent  AC Model-->

        <!-- DEOAC Login Section-->
       @if($user_data->role_id=='5' || $user_data->role_id=='24')
        <ul class="float-right">
          <li class="active"><a href="{{url('/acdeo/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <!--<ul class="dropdown">
           <li><a rel="" href="#" class="dropdown-item">comming </a></li>
            <li><a rel="" href="#" class="dropdown-item">Comming </a></li>
            </ul>-->
          </li>
           <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
              @if($user_data->st_code=='U01' || $user_data->st_code=='U02' || $user_data->st_code=='U03' || $user_data->st_code=='U04' || $user_data->st_code=='U05' || $user_data->st_code=='U06' || $user_data->st_code=='U07' || $user_data->st_code=='S16')
                <li><a rel="" href="{{url('/acdeo/allmasters')}}" class="dropdown-item">Add/Update Master Data </a></li>
                @endif
				<li><a rel="" href="{{url('/acdeo/offlinePermission')}}" class="dropdown-item"> Offline permission Module</a></li>
                <li><a rel="" href="{{url('/acdeo/allPermissionRequest')}}" class="dropdown-item"> Accept/Reject permission</a></li>
                 <li><a rel="" href="{{url('/acdeo/agentCreation')}}" class="dropdown-item"> Create DEO-Agent</a></li>
          </ul>

         </li>
          <!--<li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
         <li><a rel="" href="#" class="dropdown-item">comming </a></li>
            <li><a rel="" href="#" class="dropdown-item">Comming </a></li>
           </ul>
          </li>-->
          
       
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <!--<li><a rel="" href="{{url('/acdeo/datewisereport')}}" class="dropdown-item">Nomination Report</a></li>
               <li><a rel="" href="{{url('/acdeo/permissioncount')}}" class="dropdown-item"> Permission Report</a></li>-->
			    <li><a rel="" href="{{url('/acdeo/reportdeo')}}" class="dropdown-item">DateWise Permission Report</a></li>
				<li><a rel="" href="{{url('/acdeo/permissionraw')}}" class="dropdown-item">Permission Raw Report</a></li>
			  <li><a rel="" href="{{url('/acdeo/partywise')}}" class="dropdown-item">PartyWise Permission Report</a></li>
			  <li><a rel="" href="{{url('/acdeo/permissiontype')}}" class="dropdown-item">PermissionWise Report</a></li>
            </ul>
          </li>
      
        <li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
                  <li><a rel="" href="{{url('/acdeo/officer-details')}}" class="dropdown-item"> <span> Update Officer Details</span></a></li>
				  <li><a rel="" href="{{url('/acdeo/electors-deopollingstationlist')}}" class="dropdown-item"> <span>Electors & PS Info</span></a></li>
				              </ul>

            </ul>
          </li>
        <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
             <!--<li><a rel="" href="{{url('/acdeo/changepassword')}}" class="dropdown-item"> Change Password</a></li>-->
       <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>
      
        </ul>
         @endif
        <!-- End DEO ac Model-->

        <!-- ECIac Login Section-->
       @if($user_data->role_id=='7')
        <ul class="float-right">
          <li class="active"><a href="{{url('/eci/dashboard')}}">Home</a></li>


          
		  
		  <li><a href="javascript:void(0)" >Voter Turnout<span class="arrow-down"></span></a>
            <ul class="dropdown">
			<li><a href="{{url('eci/voting/list-schedule')}}">Poll Turn Out</a></li>
			<li><a rel="" href="{!! url('eci/report/voting/end-of-poll') !!}" class="dropdown-item">End of Poll</a></li>
			</ul>
		</li>
		  
		  

          <!--<li><a href="javascript:void(0)">Schedule<span class="arrow-down"></span></a>
            <ul class="dropdown">
            <li><a rel="" href="{{url('/eci/create-election-schedule')}}" class="dropdown-item">Create Election Schedule </a></li>
            <li><a rel="" href="{{url('/eci/election-schedule-details')}}" class="dropdown-item">Election Schedule Details </a></li>
           
            </ul> 
          </li>
          <li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="#" class="dropdown-item">comming </a></li>
            <li><a rel="" href="#" class="dropdown-item">Comming </a></li>
           </ul>
          </li>
          <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
            <!--<ul class="dropdown">
             <li><a rel="" href="#" class="dropdown-item">comming </a></li>
             <li><a rel="" href="#" class="dropdown-item">Comming </a></li>     
            </ul>-->
          </li>
           <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
			<!--PRADEEP LINKS STARTS-->
      			 <li><a rel="" href="{{url('/eci/EciActiveUsers')}}" class="dropdown-item">Active Users Report</a></li>
             <li><a rel="" href="{{url('/eci/EciElectionSchedule')}}" class="dropdown-item">Election Schedule</a></li>
             <li><a rel="" href="{{url('/eci/EciPartyData')}}" class="dropdown-item">Party Data Report</a></li>
             <li><a rel="" href="{{url('/eci/EciSymbolData')}}" class="dropdown-item">Symbol Data Report</a></li>
             <li><a rel="" href="{{url('/eci/EciPhaseInfoData')}}" class="dropdown-item">Valid Nomination Report</a></li>
             <li><a rel="" href="{{url('/eci/EciNominationFinalized')}}" class="dropdown-item">ACs Finalized</a></li>
			  <!-- waseem asgar -->
            <li><a rel="" href="{{url('/eci/report/scrutiny/state')}}" class="dropdown-item">Scrutiny Report</a></li> 
			 <li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}" class="dropdown-item">Counting Status Report</a></li>
 <!--PRADEEP LINKS ENDS-->
			
			 
			 <!--PRADEEP LINKS ENDS-->
			 
			 <li><a rel="" href="{{url('/eci/report')}}" class="dropdown-item">DateWise Permission Report</a></li>
			 <li><a rel="" href="{{url('/eci/partywise')}}" class="dropdown-item">PartyWise Permission Report</a></li>
			 <li><a rel="" href="{{url('/eci/permissiontype')}}" class="dropdown-item">PermissionWise Report</a></li>
			 
			
              <!--<li><a rel="" href="#" class="dropdown-item"> Report 3 </a></li>-->    
            </ul>
          </li>
          <li><a href="{{url('/logout')}}" class="nav-link logout"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
      
        </ul>
         @endif
        <!-- End ECI ac Model-->
      </div>
      </div>
      <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div>
    </nav>
   </header>