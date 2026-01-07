@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
 }
 @keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<div class="loader" style="display:none;"></div>

@include('admin/common/form-filter')

<section class="statistics pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4></h4>
  </div>

   <div class="col-md-5  pull-right text-right">


<span class="report-btn"><a class="btn btn-primary" href="" title="Download Excel"  ></a></span>

      
    </div>

  </div>
</div>  
</section>



<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        
            
            <span class="pull-right" style="margin-right: 10px;">
            <span><b></b></span>
            <span class="badge badge-info"></span>

            </span>
            
      
      </div>
    </div>
  </div>
</section>





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
       <div class="random-area">


         <div class="table-responsive">
          <table class="table table-bordered " id="my-list-table" data-page-length='50'>
           <thead>
		   
            <tr> 
              <th>S. No.</th>
              <th>ac_no</th>
              <th>ps_no</th>
              <th>age_18_25</th>
              <th>age_26_30</th>
              <th>age_31_40</th>
              <th>age_41_50</th>
              <th>age_51_60</th>
              <th>age_61_70</th>
              <th>age_71_80</th>
              <th>age_81_90</th>
              <th>age_91_100</th>
              <th>age_100_above</th>
              <th>time_700_730</th>
              <th>time_731_800</th>
              <th>time_801_830</th>
              <th>time_831_900</th>
              <th>time_901_930</th>
              <th>time_931_1000</th>
              <th>time_1001_1030</th>
              <th>time_1031_1100</th>
              <th>time_1101_1130</th>
              <th>time_1131_1200</th>
              <th>time_1201_1230</th>
              <th>time_1231_1300</th>
              <th>time_1301_1330</th>
              <th>time_1331_1400</th>
              <th>time_1401_1430</th>
              <th>time_1431_1500</th>
              <th>time_1501_1530</th>
              <th>time_1531_1600</th>
              <th>time_1601_1630</th>
              <th>time_1631_1700</th>
              <th>time_1701_1730</th>
              <th>time_1731_1800</th>
              <th>time_pollend</th>
              <th>poll_started</th>
              <th>poll_ended</th>
              <th>pm_received</th>
              <th>pm_submitted</th>
              <th>incident</th>
              <th>pro_diary</th>
              <th>mockpoll</th>
              <th>poll_party_reach</th>
              <th>form_49_count</th>
              <th>final_sync</th>
              <th>voting</th>
              <th>in_qtn</th>
              <th>out_qtn</th>
              <th>electors</th>
              <th>male_electors</th>
              <th>female_electors</th>
              <th>other_electors</th>
              <th>pwd_electors</th>
              <th>pwd_male_electors</th>
              <th>pwd_female_electors</th>
              <th>pwd_other_electors</th>
              <th>pwd_voter_count</th>
              <th>pwd_male_voters</th>
              <th>pwd_female_voters</th>
              <th>pwd_other_voters</th>
              <th>scan_qr</th>
              <th>scan_epicno</th>
              <th>scan_name</th>
              <th>scan_srno</th>
              <th>scan_mobile</th>
              <th>blo_turnout</th>
              <th>pro_turnout</th>
              <th>total_turnout</th>
              <th>male_turnout</th>
              <th>female_turnout</th>
              <th>other_turnout</th>
              <th>booth_exemp_status</th>
              <th>created_at</th>
              <th>updated_at</th>
              
            </tr>
          </thead>
          <tbody>  
            		 
			<?php $i =1; ?>
		  @if(count($analytics)>0)
		  @foreach($analytics as $value)
              
			  
            <tr>
              <td>{{$i}}</td>
              <td>{{$value['ac_no']}}</td>
              <td>{{$value['ps_no']}}</td>
              <td>{{$value['age_18_25']}}</td>
              <td>{{$value['age_26_30']}}</td>
              <td>{{$value['age_31_40']}}</td>
              <td>{{$value['age_41_50']}}</td>
              <td>{{$value['age_51_60']}}</td>
              <td>{{$value['age_61_70']}}</td>
              <td>{{$value['age_71_80']}}</td>
              <td>{{$value['age_81_90']}}</td>
              <td>{{$value['age_91_100']}}</td>
              <td>{{$value['age_100_above']}}</td>
              <td>{{$value['time_700_730']}}</td>
              <td>{{$value['time_731_800']}}</td>
              <td>{{$value['time_801_830']}}</td>
              <td>{{$value['time_831_900']}}</td>
              <td>{{$value['time_901_930']}}</td>
              <td>{{$value['time_931_1000']}}</td>
              <td>{{$value['time_1001_1030']}}</td>
              <td>{{$value['time_1031_1100']}}</td>
              <td>{{$value['time_1101_1130']}}</td>
              <td>{{$value['time_1131_1200']}}</td>
              <td>{{$value['time_1201_1230']}}</td>
              <td>{{$value['time_1231_1300']}}</td>
              <td>{{$value['time_1301_1330']}}</td>
              <td>{{$value['time_1331_1400']}}</td>
              <td>{{$value['time_1401_1430']}}</td>
              <td>{{$value['time_1431_1500']}}</td>
              <td>{{$value['time_1501_1530']}}</td>
              <td>{{$value['time_1531_1600']}}</td>
              <td>{{$value['time_1601_1630']}}</td>
              <td>{{$value['time_1631_1700']}}</td>
              <td>{{$value['time_1701_1730']}}</td>
              <td>{{$value['time_1731_1800']}}</td>
              <td>{{$value['time_pollend']}}</td>
              <td>{{$value['poll_started']}}</td>
              <td>{{$value['poll_ended']}}</td>
              <td>{{$value['pm_received']}}</td>
              <td>{{$value['pm_submitted']}}</td>
              <td>{{$value['incident']}}</td>
              <td>{{$value['pro_diary']}}</td>
              <td>{{$value['mockpoll']}}</td>
              <td>{{$value['poll_party_reach']}}</td>
              <td>{{$value['form_49_count']}}</td>
              <td>{{$value['final_sync']}}</td>
              <td>{{$value['voting']}}</td>
              <td>{{$value['in_qtn']}}</td>
              <td>{{$value['out_qtn']}}</td>
              <td>{{$value['electors']}}</td>
              <td>{{$value['male_electors']}}</td>
              <td>{{$value['female_electors']}}</td>
              <td>{{$value['other_electors']}}</td>
              <td>{{$value['pwd_electors']}}</td>
              <td>{{$value['pwd_male_electors']}}</td>
              <td>{{$value['pwd_female_electors']}}</td>
              <td>{{$value['pwd_other_electors']}}</td>
              <td>{{$value['pwd_voter_count']}}</td>
              <td>{{$value['pwd_male_voters']}}</td>
              <td>{{$value['pwd_female_voters']}}</td>
              <td>{{$value['pwd_other_voters']}}</td>
              <td>{{$value['scan_qr']}}</td>
              <td>{{$value['scan_epicno']}}</td>
              <td>{{$value['scan_name']}}</td>
              <td>{{$value['scan_srno']}}</td>
			  
              <td>{{$value['scan_mobile']}}</td>
              <td>{{$value['blo_turnout']}}</td>
              <td>{{$value['pro_turnout']}}</td>
              <td>{{$value['total_turnout']}}</td>
              <td>{{$value['male_turnout']}}</td>
              <td>{{$value['female_turnout']}}</td>
              <td>{{$value['other_turnout']}}</td>
			  <td>{{$value['booth_exemp_status']}}</td>
              <td>{{$value['created_at']}}</td>
              <td>{{$value['updated_at']}}</td>

              
            </tr>
			<?php $i++; ?>
			@endforeach
			
			@else 
            <tr>
              <td colspan="8">
                No Record Found.
              </td>
            </tr>
            @endif
		
          </tbody>
          
          
        </table>
      </div><!-- End Of  table responsive -->  
    </div><!-- End Of intra-table Div -->   
    
    
  </div><!-- End Of random-area Div -->
  
</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 


@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 500,
        "aaSorting": []
      });
    }
  });
</script>
@endsection