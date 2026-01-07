<!DOCTYPE html>
<html>
<body>
<style type="text/css">
body{
	background-color:#fbfbfb;
	font-family: Arial, Helvetica, sans-serif;
	padding:0 auto;
	margin:0;
}
.main{
    background-color:#fff;
    padding-top:40px;
    padding-bottom:40px;
	}
table {
  max-width:824px;
  margin:0 auto;
  border-collapse: separate;
  border-spacing: 0;
  color: #4a4a4d;
  font: 12px/1.4 "Helvetica Neue", Helvetica, Arial, sans-serif;
}
 .bdb{
  border-bottom: 1px solid #fff;      
    }
th,td {
  padding: 7px 6px;
    font-size: 14px;
  vertical-align: middle;
}
th{
  padding: 7px 6px;
}
tbody th{    
    background-color: #8e8e8e;
    color: #fff;
    font-size: 16px;
    letter-spacing: 1px;    
    }
thead {
  background: #9E9E9E;
  color: #fff;
  font-size: 14px;
  text-transform: uppercase;
}
thead th{
      border-top: 1px solid #cecfd5;
	  
}
thead h2, thead p{
      text-align: center;
}
thead th:first-child {
  border-left: 1px solid #cecfd5;
}
thead th:last-child {
  border-right: 1px solid #cecfd5;
}

td {
  border-top: 1px solid #cecfd5;
  border-bottom: 1px solid #cecfd5;
  border-right: 1px solid #cecfd5;
}
td:first-child {
  border-left: 1px solid #cecfd5;
}
.form-title {
  display: block;
  color: #333;
  font-size: 13px;
}
    
tbody th .form-title {
    letter-spacing: .5px;
    color: #fff;
    }
.form-item {
  text-align: center;
}
.item-qty {
  text-align: center;
}
.item-price {
  text-align: right;
}
.item-multiple {
  display: block;
}
tfoot {
  text-align: left;
}
tfoot tr:last-child {
  background: #f0f0f2;
  color: #395870;
  font-weight: bold;
}
tfoot tr:last-child td:first-child {
  border-bottom-left-radius: 5px;
}
tfoot tr:last-child td:last-child {
  border-bottom-right-radius: 5px;
}
</style>
    <div class="main">
      <table width="100%">
          <thead class="" style="background: #ffffff; color: #000000;">
          <tr>
          <th colspan="" style="" width="22%">
            <img width="100" height="100" src="{{ asset('theme/img/logo/central-login/garuda.png') }}">
          </th>
          <th colspan="" width="53%">
            <h2 style="margin-bottom:0;"><strong>Election Commission of India</strong></h2>
            <p style="margin-top:0">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</p>
          </th>
          <th colspan="" style="text-align: right;" width="25%">
               <h4 style="margin-bottom:0;"><strong>Date : 11-12-2019<br/>Ref Id : ECI20190001</strong></h4>
		  </th>
        </tr>
        <tr>
          <th colspan="2" style="color:#000; background-color: #fcfcfc; text-align: left; text-transform: none; border-right: 1px solid #cecfd5;">
            <h3 style="font-weight:300; margin-bottom:0; margin-top:0;">State Profile @if($state_details)({{$state_details->ST_NAME}}) @endif<br/><strong>LoginUser:</strong>{{$user_data->designation}}<br/></h3>
          </th>
			
          <th style="color:#000; background-color: #fcfcfc; text-align: left; text-transform: none;">
            <h5 style="font-weight:normal;"><strong>Date:</strong>{{date('Y-m-d H:i A')}}</h5>
          </th>
		</tr>
          </thead>
        </table>
        <table width="100%">
        <thead>
         
        </thead>
        <tbody>
            <tr>
                <th colspan="7"><strong class="form-title">Officer Details</strong></th>
            </tr>
           <tr>
				@php $i=1;@endphp
					@if($officer_count)
						@foreach($officer_count as $k=>$v)
                <td><strong class="form-title">{{$i}}. {{$v->designation}}</strong></td>
						@php $i++;@endphp
						@endforeach
					@else
						<td><strong class="form-title">1.CEO</strong></td>
						<td><strong class="form-title">2.DEO</strong></td>
						<td><strong class="form-title">3.ROAC</strong></td>
					@php $i=4;@endphp
					@endif
					
					@php 
						$j=$i;
					@endphp
					@if($get_observer_data)
						@foreach($get_observer_data as $val)
							@if($val->OBSERVER_Type !='NotDefine')
								
                <td><strong class="form-title">{{$j}}. {{ucfirst(strtolower($val->OBSERVER_Type))}} Observer</strong></td>
				@php $j++; @endphp
						@endif
						
					@endforeach
				@else
                <td><strong class="form-title">4. General Observer</strong></td>
                <td><strong class="form-title">5. Police Observer</strong></td>
                <td><strong class="form-title">6. Expenditure Observer</strong></td>
                <td><strong class="form-title">7. Awareness Observer</strong></td>
				@endif
              </tr>
              <tr>
				@php $i=1;@endphp
				@if($officer_count)
					@foreach($officer_count as $k=>$v)
						<td>{{$v->total_officer}}</td>
					@php $i++;@endphp
					@endforeach
				@else
					<td>-</td>
					<td>-</td>
					<td>-</td>
					@php $i=4;@endphp
				@endif
				
				@php 
					$j=$i;
				@endphp
				@if($get_observer_data)
					@foreach($get_observer_data as $val)
						@if($val->OBSERVER_Type !='NotDefine')
							<td>{{$val->TotalOBS}}</td>
						@php $j++; @endphp
								@endif
								
							@endforeach
						@else
						<td>-</td>
						<td>-</td>
						<td>-</td>
						<td>-</td>
					@endif
                
              </tr>
              <tr>
             <th colspan="7"><strong class="form-title">Demographic Details</strong></th>
            </tr>
            <tr>
                <td><strong class="form-title">Revenue Districts</strong></td>
                <td><strong class="form-title">Sub Divisions</strong></td>
                <td colspan="2"><strong class="form-title">Tehesils/Talukas</strong></td>
                <td><strong class="form-title">Gram Panchayats</strong></td>
                <td><strong class="form-title">Villages</strong></td>
                <td><strong class="form-title">Municipal Corporation</strong></td>
              </tr>
              <tr>
                <td>@if($get_state_entry){{$get_state_entry->revenue_district}}@else - @endif</td>
				<td>@if($get_state_entry){{$get_state_entry->sub_division}}@else - @endif</td>
				<td colspan="2">@if($get_state_entry){{$get_state_entry->tehsil_talkuas}}@else - @endif</td>
                <td>@if($get_state_entry){{$get_state_entry->gram_panchayat}}@else - @endif</td>
				<td>@if($get_state_entry){{$get_state_entry->gram_panchayat}}@else - @endif</td>
				<td>@if($get_state_entry){{$get_state_entry->municipal_corporations}}@else - @endif
              </tr>
            <tr>
                <td><strong class="form-title">Municipalities</strong></td>
                <td><strong class="form-title">Post Offices</strong></td>
                <td><strong class="form-title">Police Stations</strong></td>
                <td><strong class="form-title">Electoral Office</strong></td>
                <td><strong class="form-title">Total ACs</strong></td>
                <td colspan="2"><strong class="form-title">Total PCs</strong></td>
              </tr>
              <tr>
                <td>@if($get_state_entry){{$get_state_entry->municipalities}}@else - @endif</td>
				<td>@if($get_state_entry){{$get_state_entry->post_offices}}@else - @endif</td>
				<td>@if($get_state_entry){{$get_state_entry->police_stations}}@else - @endif</td>
				<td>@if($get_state_entry){{$total_dist}}@else - @endif</td>
				<td>@if($get_state_entry){{$total_acs}}@else - @endif</td>
				<td colspan="2">@if($get_state_entry){{$total_pcs}}@else - @endif</td>  
              </tr>            
              <tr>
                <th colspan="7"><strong class="form-title">Popullation Details</strong></th>
            </tr>
             <tr>
              <td colspan="2"><strong class="form-title">2011 Popullation</strong></td>
              <td colspan="2"><strong class="form-title">Projected Popullation</strong></td>
              <td colspan="3"><strong class="form-title">EP Ratio</strong></td>
              </tr>
              <tr>
                <td colspan="2">@if($get_ac_entry){{$get_ac_entry->census_population}}@else - @endif</td>
                <td colspan="2">@if($get_ac_entry){{$get_ac_entry->projected_population}}@else - @endif</td>
                <td colspan="3">@if($get_ac_entry){{$get_ac_entry->ep_ratio}}@else - @endif</td>                  
              </tr>
             <tr>
                <th colspan="7"><strong class="form-title">General Electors</strong></th>
            </tr>
              <tr>
              <td colspan="2"><strong class="form-title">1.Armed Force(Form2)</strong></td>
              <td colspan="2"><strong class="form-title">2.Armed Police Force Of State Deployed Outside</strong></td>
              <td colspan="3"><strong class="form-title">3.Person Employed Under Goverment of India in a Post outside india(Form3)</strong></td>
              </tr>
              <tr>
                <td colspan="2">20</td>
                <td colspan="2">30</td>
                <td colspan="3">2</td>                  
              </tr>
             <tr>
                <th colspan="7"><strong class="form-title">Service Electors</strong></th>
            </tr>
              <tr>
              <td colspan="2"><strong class="form-title">1.Armed Force(Form2)</strong></td>
              <td colspan="2"><strong class="form-title">2.Armed Police Force Of State Deployed Outside</strong></td>
              <td colspan="3"><strong class="form-title">3.Person Employed Under Goverment of India in a Post outside india(Form3)</strong></td>
              </tr>
              <tr>
                <td colspan="2">20</td>
                <td colspan="2">30</td>
                <td colspan="3">2</td>                  
              </tr>
             <tr>
                <th colspan="7"><strong class="form-title">Persons with Disability Status(PWD)</strong></th>
            </tr>
             <tr>
              <td><strong class="form-title">1.Total Visually Impaired</strong></td>
              <td colspan="2"><strong class="form-title">2.Total Speech/Hearing Disabled</strong></td>                 
              <td colspan="2"><strong class="form-title">3.Total Locomotor Disabled</strong></td>
              <td><strong class="form-title">4.Total Other Disability</strong></td>
               <td><strong class="form-title">5.Total PWDs</strong></td>
              </tr>
              <tr>
                <td>@if($get_ac_entry){{$get_ac_entry->total_visially_impaired}}@else - @endif</td>
                <td colspan="2">@if($get_ac_entry){{$get_ac_entry->total_speech_hearig}}@else - @endif</td>
                <td colspan="2">@if($get_ac_entry){{$get_ac_entry->total_locomotor_disabled}}@else - @endif</td>
                <td>@if($get_ac_entry){{$get_ac_entry->total_Other_disability}}@else - @endif</td>
                <td>@if($get_ac_entry){{$get_ac_entry->total_pwds}}@else - @endif</td>                  
              </tr>
            <tr>
                <th colspan="7"><strong class="form-title">Assured Minimum Facilites At PS</strong></th>
            </tr>
            <tr>
              <td><strong class="form-title">1.Electricity</strong></td>
              <td colspan="2"><strong class="form-title">2.Drinking Water</strong></td>                 
              <td ><strong class="form-title">3.Toilet Facility</strong></td>
              <td><strong class="form-title">4.Ramp</strong></td>
               <td colspan="2" ><strong class="form-title">5.Situated in Ground Floor</strong></td>
              </tr>
              <tr>
                <td>0</td>
                <td colspan="2">30</td>
                <td>20</td>
                <td>30</td>
                <td colspan="2">20</td>                  
              </tr>
             <tr>
                <th colspan="7"><strong class="form-title">ERONET Forms</strong></th>
            </tr>
            <tr>
              <td colspan="2"><strong class="form-title">1.Total Pending</strong></td>
              <td colspan="2"><strong class="form-title">2.Form-6</strong></td>                 
              <td ><strong class="form-title">3.Form-7</strong></td>
              <td><strong class="form-title">4.Form-8</strong></td>
               <td><strong class="form-title">5.Form-8A</strong></td>
              </tr>
              <tr>
                <td colspan="2">10</td>
                <td  colspan="2">20</td>
                <td>05</td>
                <td>30</td>
                <td>20</td>                  
              </tr>
            <tr>
                <th colspan="7"><strong class="form-title">NGSP Complaints</strong></th>
            </tr>
           <tr>
              <td colspan="3"><strong class="form-title">1.Total Complaints</strong></td>
              <td colspan="2"><strong class="form-title">2.Total Resolved</strong></td>                 
              <td colspan="2"><strong class="form-title">3.Total Pending</strong></td>
              </tr>
              <tr>
                <td colspan="3">10</td>
                <td colspan="2">20</td>
                <td colspan="2">20</td>                  
              </tr>
             <tr>
                <th colspan="7" class="bdb"><strong class="form-title">EVM/VVPAT</strong></th>
            </tr>
              <tr>
              <tr><th colspan="7"><strong class="form-title">BU</strong></th>
            </tr>
              <tr>
                  <td colspan="3"><strong class="form-title">1.Requirement</strong></td>
                  <td colspan="2"><strong class="form-title">2.Allotted</strong></td>
                  <td colspan="2"><strong class="form-title">3.Yet to Receive</strong></td>
              </tr>
               <tr>
                <td colspan="3">10</td>
                <td colspan="2">20</td>
                <td colspan="2">20</td>                  
              </tr>
            <tr>
              <th colspan="7"><strong class="form-title">CU</strong></th>
            </tr>
              <tr>
                  <td colspan="3"><strong class="form-title">1.Requirement</strong></td>
                  <td colspan="2"><strong class="form-title">2.Allotted</strong></td>
                  <td colspan="2"><strong class="form-title">3.Yet to Receive</strong></td>
              </tr>
               <tr>
                <td colspan="3">10</td>
                <td colspan="2">20</td>
                <td colspan="2">20</td>                  
              </tr>
             <tr>
              <th colspan="7"><strong class="form-title">VVPAT</strong></th>
            </tr>
              <tr>
                  <td colspan="3"><strong class="form-title">1.Requirement</strong></td>
                  <td colspan="2"><strong class="form-title">2.Allotted</strong></td>
                  <td colspan="2"><strong class="form-title">3.Yet to Receive</strong></td>
              </tr>
               <tr>
                <td colspan="3">10</td>
                <td colspan="2">20</td>
                <td colspan="2">20</td>                  
              </tr>

              <tr>
                  <th colspan="2"><strong class="form-title">1.Total Ware House</strong></th>
                  <th colspan="2"><strong class="form-title">2.FLC Completion</strong></th>
                  <th colspan="2"><strong class="form-title">3.FLC OK</strong></th>                  
                  <th><strong class="form-title">4.FLC Reject</strong></th>                     
              </tr>
               <tr>
               
                <td colspan="2" style="text-align: center">1000</td>
                <td colspan="2" style="text-align: center">20</td>
                <td colspan="2" style="text-align: center">20</td>   
                <td style="text-align: center">50</td>                     
              </tr>
           <tr>
              <th colspan="7"><strong class="form-title">Candidate Details</strong></th>
            </tr>
             <tr>
              <td colspan="4"><strong class="form-title">Total Nominations Applied</strong></td>
              <td colspan="3"><strong class="form-title">Contesting Candidates</strong></td>                 
            </tr>
            <tr>
                <td colspan="4">@if($count_applied>0){{$count_applied}}@else - @endif</td>
                <td colspan="3">@if($count_contested>0){{$count_contested}}@else - @endif</td>
            </tr>
            <tr>
              <th colspan="7"><strong class="form-title">Nomination</strong></th>
            </tr>
            <tr>
              <td colspan="3"><strong class="form-title">Accepted</strong></td>
              <td colspan="2"><strong class="form-title">Rejected</strong></td>                                  
              <td colspan="2"><strong class="form-title">Withdraw</strong></td>                                                   
              </tr>
   
               <tr>
                <td colspan="3">@if($count_accepted>0){{$count_accepted}}@else - @endif</td>                  
                <td colspan="2">@if($count_rejected>0){{$count_rejected}}@else - @endif</td>
                <td colspan="2">@if($count_withdraw>0){{$count_withdraw}}@else - @endif</td>
              </tr>
             <tr>
              <th colspan="7"><strong class="form-title">Vulnerability/Sensitive area mapping</strong></th>
            </tr>
              <tr>
              <td colspan="3"><strong class="form-title">Vulnerable Area/Pockets</strong></td>
              <td colspan="2"><strong class="form-title">Critical PS</strong></td>
              <td colspan="2"><strong class="form-title">Expenditure Sensitive Constituencies</strong></td>
              </tr>
              <tr>
                <td colspan="3">@if($get_ac_entry){{$get_ac_entry->vulnerable_area_pockets}}@else - @endif</td>
                <td colspan="2">@if($get_ac_entry){{$get_ac_entry->critical_ps}}@else - @endif</td>
                <td colspan="2">@if($get_ac_entry){{$get_ac_entry->expenditure_sensitive_constituencies}}@else - @endif</td>                  
              </tr>
             <tr>
              <th colspan="7"><strong class="form-title">Election Schedule</strong></th>
            </tr>
              <tr>
              <td><strong class="form-title">Total ACs for elections</strong></td>
              <td colspan="2"><strong class="form-title">Notification Date</strong></td>
              <td colspan="2"><strong class="form-title">Nomination Start Date</strong></td>
              <td><strong class="form-title">Notification Last Date</strong></td>
              <td><strong class="form-title">Scrutiny Date</strong></td>                  
              </tr>
              <?php $phase = 1;
				if ($schdule) {
					foreach ($schdule as $k => $v) { ?>
						<tr>
							@if(count($schdule)>1)<td>{{$phase}}</td>@endif
							<td>{{$v->total_acs}}</td>
							<td colspan="2">{{date('d M Y',strtotime($v->DT_PRESS_ANNC))}}</td>
							<td colspan="2">{{date('d M Y',strtotime($v->DT_ISS_NOM))}}</td>
							<td>{{date('d M Y',strtotime($v->LDT_IS_NOM))}}</td>
							<td>{{date('d M Y',strtotime($v->DT_SCR_NOM))}}</td>
						</tr>  

						<?php $phase++;
					}
				} else { ?>
					<tr><td style="font-weight:normal;font-size:12px;" colspan="5">No record found</td></tr>
<?php } ?>
               <tr>
              <td><strong class="form-title">Withdraw Date</strong></td>
              <td colspan="2"><strong class="form-title">Poll Date</strong></td>
              <td colspan="2"><strong class="form-title">Date of Counting</strong></td>
              <td colspan="2"><strong class="form-title">Date of Completion of elections</strong></td>
              </tr>
              <?php $phase = 1;
if ($schdule) {
    foreach ($schdule as $k => $v) { ?>
                                                    <tr>
                                                        <td>{{date('d M Y',strtotime($v->LDT_WD_CAN))}}</td> 
                                                        <td colspan="2">{{date('d M Y',strtotime($v->DATE_POLL))}}</td>
                                                        <td colspan="2">{{date('d M Y',strtotime($v->DATE_COUNT))}}</td>
                                                        <td colspan="2">{{date('d M Y',strtotime($v->DTB_EL_COM))}}</td>   
                                                    </tr>  
        <?php $phase++;
    }
} else { ?>
                                                <tr><td style="font-weight:normal;font-size:12px;" colspan="4">No record found</td></tr>
<?php } ?>
            <tr>
              <th colspan="7"><strong class="form-title">Webcasting Details</strong></th>
            </tr>
             <tr>
              <td colspan="3"><strong class="form-title">Vulnerable Area/Pockets</strong></td>
              <td colspan="4"><strong class="form-title">Critical PS</strong></td>
              </tr>
              <tr>
                <td colspan="3">@if($get_ac_entry){{$get_ac_entry->no_of_ps_webcasting}}@else - @endif</td>
                <td colspan="4">@if($get_ac_entry){{$get_ac_entry->details_of_webcasting}}@else - @endif</td>
              </tr>
            <tr>
              <th colspan="7"><strong class="form-title">Election Results</strong></th>
            </tr>
             
        </tbody>
       
      </table>
	  <table>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Party</th>
                                                    <th class="text-right">Seat Won</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                $total_seats = 0;
                                                if (count($leadWinCount) > 0) {
                                                    foreach ($leadWinCount as $k => $val) {
                                                        $total_seats += $val->win;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $i; ?></td>
                                                            <td><?php echo $val->lead_cand_party; ?></td>
                                                            <td class="text-right"><?php echo $val->win; ?></td>
                                                        </tr>
        <?php $i++;
    }
} else { ?>
                                                    <tr>
                                                        <td colspan="2">No record found</td>
                                                    </tr>
<?php } ?>
                                            </tbody>
                                            <thead>
                                            <th>#</th>
                                            <th>Total Seats</th>
                                            <th class="text-right"><?php echo $total_seats; ?></th>  
                                            </thead>  
                                        </table>
            
	
  </div>
​</body>
</html>