@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'EXPENDITURE')
@section('bradcome', 'MIS')
@section('description', '')
@section('content')
@php 
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $acdetails=getacbyacno($st_code, $cons_no); 
  $acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
  //echo $st_code.'cons_no=>'.$cons_no;

@endphp

<main role="main" class="inner cover mb-3">
<section class="mt-5">
<div class="container-fluid">
  <div class="row text-center pt-2 pb-1">
  <div class="col-sm-12"><h4><b>ELECTION EXPENDITURE MONITORING SYSTEM GENERAL AC ELECTION-{{strtoupper($stateName)}}</b></h4></div>
		<div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/acceo/mis-officer')}}" id="EcidashboardFilter">           
                     <div class="row justify-content-center">
                    {{ csrf_field() }}
					       	<div class="col-sm-3">
                  <label for="" class="mr-3">Select AC</label>    
                  <select name="ac" id="ac" class="consttype form-control" >
                    <option value="">-- All AC --</option>
                    @php $all_ac = getacbystate($user_data->st_code); @endphp
                    @foreach($all_ac as $getAc)
                    @if ( $cons_no == $getAc->AC_NO)
                      <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NO}}-{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                      @else
                      <option value="{{ $getAc->AC_NO }}">{{$getAc->AC_NO}}-{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                    @endif
								@endforeach 
							</select>
					    @if ($errors->has('ac'))
                  		  <span style="color:red;">{{ $errors->first('ac') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-1 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
            	</p>
                        </div>
                    </div>
                </form>  
                 <!--FILTER ENDS HERE-->
				</div> 
  <div class="card text-left mt-3" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4></h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; 
              <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;
              <a href="{{url('/acceo/OfficerMISPDF')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/acceo/OfficerMISEXL')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
             <!-- <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->
              </p>
              </div>
            </div>
			 <div class="row" style="width:100%;"><h4> Officer's MIS Regarding DEO's Scrutiny Report On Account Of Contesting Candidates.</h4></div> 
      </div>
   
 <div class="card-body"> 
<div class="table-responsive">
<table id="examples" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
		  <th>District</th> 
		  <th>AC Name</th> 
          <th>Total Candidates</th> 
		  <th>Started</th> 
          <th>Not Started</th> 
		  <th>Not In Time</th> 
		  <th>Finalised By DEO</th> 
          <th>Pending - DEO</th> 
		  <!--<th>Notice At DEO</th> -->
          <th>Pending - CEO</th> 
		  <th>Notice At CEO</th>
         </tr>
        </thead>
       
        @php  
        $count = 1; 
        $TotalUsers = 0;
        $TotalPendingatRO = 0;
        $TotalPendingatCEO = 0;
        $TotalPendingatECI= 0;
        $TotalfiledData = 0;
        $TotalnotfiledData = 0;
        $Totalfinalcompletedcount= 0;
        $Totalac = 0;
		$TotalDEONotice = 0;
		$TotalCEONotice = 0;
		$TotalfiledData = 0;
		$TotalFinalByDEO = 0;
		$TotalNotinTime= 0;
        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php
		// dd($listdata);
         $TotalUsers +=$listdata->totalcandidate;
         $cons_no=$listdata->ac_no;
         $stdetails=getstatebystatecode($listdata->st_code);
         $acbystate=getacbystate($listdata->st_code);
         $account=count($acbystate);
         $Totalac += $account;
		 $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);
       
	     $distdetails=getdistrictbydistrictno($st_code,$listdata->district_no);
		 
		 $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('AC',$listdata->st_code,$cons_no);
         $TotalFinalByDEO += $finalbyDEO;
		 
         $pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('AC',$listdata->st_code,$cons_no);

         $TotalPendingatCEO += $pendingatCEO;
		 
         $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('AC',$listdata->st_code,$cons_no);
         $TotalPendingatECI += $pendingatECI;
		 
         $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
		 
         $TotalfiledData +=  $filedcount;
		  
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('AC',$listdata->st_code,$cons_no);
         $Totalfinalcompletedcount += $finalcompletedcount;
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('AC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('AC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
		 $notinTime=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalNotinTime('AC',$listdata->st_code,$cons_no);
		 $TotalNotinTime += $notinTime;
		  
		  //pending at DEO
            if($finalbyDEO >= 0 ){
			$pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
			}  			
		 
         @endphp
          <tr>
            <td>{{ $count }}</td>
			<td align="left">@if(!empty($distdetails->DIST_NAME))   {{ $distdetails->DIST_NAME }}  @else <b> N/A </b> @endif</td>
            <td align="left">@if(!empty($acdetails->AC_NAME))   {{ $acdetails->AC_NAME }}  @else <b> N/A </b> @endif</td>
            <td align="right">@if($listdata->totalcandidate =='' ) 0   @else <a href="{{url('/')}}/acceo/expallcandidate/{{base64_encode($cons_no)}}" >        <b> {{ $listdata->totalcandidate }}</b> @endif</a></td>
			
			<td align="right">@if($filedcount =='' )     0  @else <a href="{{url('/')}}/acceo/expstartedcandidate/{{base64_encode($cons_no)}}" >    <b>{{ $filedcount }}</b> @endif</a></td>
			
            <td align="right"> @if($notfiledcount =='' )     0  @else <a href="{{url('/')}}/acceo/expnotstarted/{{base64_encode($cons_no)}}" >  <b>{{  $notfiledcount }}</b> @endif </a></td>
			
			 <td align="right">@if($notinTime =='' )     0  @else <a href="{{url('/')}}/acceo/expnotintimecandidate/{{base64_encode($cons_no)}}" >  <b>{{  $notinTime }}</b> @endif</a></td>
			
			<td align="right">@if($finalbyDEO =='' )     0   @else <a href="{{url('/')}}/acceo/expfinalbyDEO/{{base64_encode($cons_no)}}" >   <b>{{  $finalbyDEO }}</b> @endif </a></td>
			
            <td align="right" >@if($pendingatRO =='' )     0  @else<a href="{{url('/')}}/acceo/exppendingatro/{{base64_encode($cons_no)}}" title="toalcandidate-pendingatCEO">   <b>{{  $pendingatRO }}</b> @endif </a></td>
			
			<!--<td align="right"> <a href="{{url('/')}}/acceo/noticeatdeo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($noticeatDEOCount =='' )     0  @else <b>{{  $noticeatDEOCount }}</b> @endif </a></td>-->
			
            <td align="right"> @if($pendingatCEO =='')     0  @else <a href="{{url('/')}}/acceo/exppendingatceo/{{base64_encode($cons_no)}}" > <b>{{  $pendingatCEO }}</b> @endif</a></td>
			
			<td align="right"> @if($noticeatCEOCount =='')     0  @else  <a href="{{url('/')}}/acceo/noticeatceo/{{base64_encode($cons_no)}}" ><b>{{  $noticeatCEOCount }}</b> @endif</a></td>
			 
          
          </tr>
           @php  $count++;  @endphp
          
           @empty
                <tr>
                  <td colspan="6">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td>
          <td align="right"><b> </b></td>
		   <td align="right"><b> </b></td>
          <td align="right"><b>{{$TotalUsers}}</b>
          </td>
	      <td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>{{$TotalNotinTime}}</b></td><td align="right"><b>
		  {{$TotalFinalByDEO}}</b></td><td align="right"><b>{{$TotalPendingatRO}}</b></td><td align="right"><b>{{$TotalPendingatCEO}}</b></td><td align="right"><b>{{$TotalDEONotice}}</b></td></tr>
        <tbody> </tbody>
    </table>
	</div> 
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

@endsection

@section('script')

<script>
jQuery(document).ready(function(){ 
	jQuery("select[name='state']").change(function(){
		var state = jQuery(this).val();  
   // alert(state);
        jQuery.ajax({ 
        	url: '<?php echo url('/') ?>/acceo/getacbystate',
            type: 'GET',
            data: {state:state},
         
            success: function(result){  
				console.log(result); 
                var stateselect = jQuery('form select[name=ac]');
                stateselect.empty();
                var achtml = '';
                achtml = achtml + '<option value="">-- All AC --</option> ';
                jQuery.each(result,function(key, value) { 
                    achtml = achtml + '<option value="'+value.AC_NO+'">'+value.AC_NO+' - '+value.AC_NAME + ' - ' +value.AC_NAME_HI+'</option>';
                    jQuery("select[name='ac']").html(achtml);
                });
                var achtml_end = '';
                jQuery("select[name='ac']").append(achtml_end)
            }
        });
    });
	/*
	//Check Validation
    jQuery('#psinfo').click(function(){
		var distt = jQuery('select[name="state"]').val();
		var acname = jQuery('select[name="pc"]').val();
		
		if(distt == ''){
			jQuery('.errormsg').html('');
			jQuery('.stateerrormsg').html('Please select district');
			jQuery( "input[name='district']" ).focus();
			return false;
		}
		if(acname == ''){
            jQuery('.errormsg').html('');
			jQuery('.acerrormsg').html('Please select ac');
			jQuery( "input[name='ac']" ).focus();
			return false;
		}
	});
  */
	
});

</script>
@endsection


