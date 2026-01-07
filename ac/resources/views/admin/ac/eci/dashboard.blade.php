@extends('admin.layouts.ac.theme')
<link rel="stylesheet" href="{{ asset('/theme/css/dashboard.css') }}">
@section('content')
@section('title', 'ECI ')
@section('bradcome', 'DASHBOARD')
<main>
<?php if(isset($booth_dashboard)){ ?>
 {!! $booth_dashboard !!} 
<?php }?>
@if($user_data->role_id !='27')
<style>
span.non-num,span.num{padding: 0; min-width: auto; min-height: auto; line-height: 34px; font-size: 1rem;}
.head-title {
    color: #d34c89;
    border-bottom: 1px dashed #d34c89;
    padding: 0.85rem 0;
    border-top: 1px dashed #d34c89;
}
.table-td-nowrap td{white-space: nowrap;}
</style>


<section class="statistics color-grey" style="border-bottom:1px solid #eee;">
        <div class="container-fluid pt-2"> 
			  <!--<div class="row text-center">
			  <div class="col"><h3 class="mt-4 mb-2 display 3"><b style="font-weight: 600;text-decoration:underline;">ECI DASHBOARD</b></h3></div>
			 
			  </div>-->        
		  <div class="dash">
		  
				<div class="card p-0">
					<div class="card-body">	
						<div class="row text-center align-items-center">
						   <div class="col py-2"><h4 class="head-title"><b>State</b></h4></div>
						   <div class="col py-2"><h4 class="head-title"><b>District</b></h4></div>
						   <div class="col py-2"><h4 class="head-title"><b>AC's</b></h4></div>
                        </div>	

						@foreach($st_arr as $raw)
						
						<div class="row text-center">								
						    <div class="col"><span class="non-num">{{$raw['state_name']}}</span></div>
							<div class="col"><span class="num">{{$raw['total_dist']}}</span></div>
							<div class="col"><span class="num">{{$raw['total_ac']}}</span></div>
							<!--<div class="col"><h4><b>Polling Station</b></h4><span class="num">{{$total_ps}}</span></div>
							<div class="col pt-5"><h4><b>Electors</b></h4><span class="num">{{$total_electors}}</span></div>-->
						</div>						
      		
		 
		  
		  @endforeach
		  </div>
		   </div>
		  </div>
        
		<!--<table class="table table-bordered">
		  <thead>
		    <tr>
			  <th>State</th>
			  <th>District</th>
			  <th>AC's</th>
			</tr>
		  </thead>
		  <tbody>
		  @foreach($st_arr as $raw)
		    <tr>
			  <td>{{$raw['state_name']}}</td>
			  <td>{{$raw['total_dist']}}</td>
			  <td>{{$raw['total_ac']}}</td>
			</tr>
			@endforeach
		  </tbody>
		</table>-->
		
		 <table id="" class="table table-striped table-bordered table-hover table-td-nowrap" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
		  <th>State</th>
          <th>Poll Events (Phase)</th>           
          <th>Total ACs in Phase</th> 
          <th>Date of Issue of Gazette Notification</th> 
          <th>Last Date For Making Nominations</th> 
          <th>Date for Scrutiny of Nominations</th> 
          <th>Last Date For Withdrawl of Candidature</th> 
          <th>Date Of Poll</th> 
          <th>Date Of Counting</th> 
          <th>Date Of Completion</th>
        </tr>
        </thead>
        <tbody>
        @php  $count = 1; @endphp
         @forelse ($results as $result)
		 
		 
		 <?php// dd($result); ?>
		 
          <tr>
            <td>{{ $count }}</td>
			<td><a  style="color:#000000">{!! $result['label'] !!}</a></td>
            <td><a  style="color:#000000">Phase - {{$result['sid'] }}</a></td>

            <td><a  style="color:#000000">{{$result['acs'] }}</a></td>

            <td><a  style="<?php echo $result['start_nomi_class'] ?>">{{GetReadableDateFormat($result['start_nomi_date']) }}</a></td>

            <td><a  style="<?php echo $result['last_nomi_class'] ?>">{{GetReadableDateFormat($result['last_nomi_date']) }}</a></td>


            <td><a  style="<?php echo $result['nomi_scr_class'] ?>">{{GetReadableDateFormat($result['dt_nomi_scr']) }}</a></td>

            <td><a  style="<?php echo $result['last_wid_class'] ?>">{{GetReadableDateFormat($result['last_wid_date']) }}</a></td>

            <td><a  style="<?php echo $result['poll_date_class'] ?>">{{GetReadableDateFormat($result['poll_date']) }}</a></td>

            <td><a  style="<?php echo $result['count_date_class'] ?>">{{GetReadableDateFormat($result['count_date']) }}</a></td>

            <td><a  style="<?php echo $result['complete_date_class'] ?>">{{GetReadableDateFormat($result['complete_date']) }}</a></td>
            
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Election Schedule</td>                 
              </tr>
          @endforelse
        </tbody>
    </table>
		
		
</section>
	 @endif
</main>  
 

@endsection