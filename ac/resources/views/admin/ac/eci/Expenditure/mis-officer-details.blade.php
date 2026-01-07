@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'MIS')
@section('description', '')
@section('content')
@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
$election_id=Session::get('DB_ELECTION_ID');
$election_type=Session::get('DB_ELE_TYPE');

//echo $st_code.'cons_no'.$cons_no; die;
@endphp
<main role="main" class="inner cover mb-3">
<section class="mt-5">
<div class="container-fluid">
  <div class="row text-center pt-2 pb-1">
  <div class="col-sm-12"><h4><b>ELECTION EXPENDITURE MONITORING SYSTEM GENERAL FOR ASSEMBLY ELECTION</b></h4></div>
				         <div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/eci-expenditure/mis-officer-details')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                    {{ csrf_field() }}
                      <!--STATE LIST DROPDOWN STARTS-->
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select State</label>    
                        <select name="state" id="state" class="form-control">
                      <?php if($stateName=='ALL') { ?> <option value="">All States</option> <?php } ?>
                      @foreach ($statelist as $state_List ))
                        @if ($st_code == $state_List->ST_CODE)
                              <option value="{{ $state_List->ST_CODE }}" selected>{{$state_List->ST_NAME}}</option>
                        @else
                              <option value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>
                        @endif
                      @endforeach

                      @if ($errors->has('state'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('state') }}</strong>
                      </span>
                      @endif
                      <div class="stateerrormsg errormsg errorred"></div>
                  </select> 
                        </div>
                            <!--STATE LIST DROPDOWN ENDS-->
					       	<div class="col-sm-3">
                        <label for="" class="mr-3">Select AC</label>    
                        <select name="ac" id="ac" class="consttype form-control" >
								<option value="">-- All AC --</option>
                @if (!empty($all_ac))
                <?php //dd($all_pc);?>
								@foreach($all_ac as $getAc)
								 @if ($cons_no ==  $getAc->AC_NO)
                              <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NO }} - {{$getAc->AC_NAME }}</option>
                              @else
									<option value="{{ $getAc->AC_NO }}" > 
									{{$getAc->AC_NO }} - {{$getAc->AC_NAME }}</option>
									 @endif
								@endforeach 
                @endif
							</select>
					    @if ($errors->has('ac'))
                  		  <span style="color:red;">{{ $errors->first('ac') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-2 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
						  <a href="{{url('/eci-expenditure/mis-officer-details')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a>
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
              <a href="{{url('/eci-expenditure/EciOfficerMISDetailsPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/EciOfficerMISDetailsEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <!--<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->
              </p>
              </div>
            </div>
			 <!--<div class="row" style="width:100%;"><h4> Officer's MIS Regarding DEO's Scrutiny Report On Account Of Contesting Candidates.</h4></div> -->
			 
			 <div class="row" style="width:100%;"><h4> MIS Of Account Cases Of GENERAL ELECTION To Legislative Assembly</h4></div>
			 <p class="mb-0 text-right"><b>Election Type : </b>{{ session()->get('ELE_TYPE_DESC') }}</p>
      </div>
   
 <div class="card-body"> 
<div class="table-responsive">
<table id="examples" class="table table-striped table-bordered table-hover" style="width:100%">
           <thead class="text-center">
		  <tr>
          <th>I</th>
          <th>II</th>
          <th>III</th>
          <th>IV</th> 
		   <th>V</th>
          <th>VI</th>
		  <th>VII</th>
          <th>VIII</th> 
          <th>IX</th>
          <th colspan="">X</th> 
		  <th colspan="">XI</th> 
		  <th colspan="">XII</th> 		  
         </tr>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
		  @if(empty($cons_no)) 
          <th>Total AC</th> 
	      @else
		  <th>AC Name</th> 
		  @endif
          <th>Total Candidates</th> 
		  <!--<th>Started</th> 
          <th>Not Started</th> -->
		  <th>Finalised By DEO</th> 
          <th>Pending - For Finalisation By DEO <BR /> IV-V</th> 
		   <th>Notice - DEO</th>
           <th>Pending - CEO <BR /> V-(IX+X+XI+XII) </th> 
		  <th>Notice - CEO</th>
          <th>Pending - ECI </th> 
          <th>Closed/Case Dropped</th> 
		  <th>Disqualified</th> 
         </tr>
        </thead>
       
      <?php
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
		$pendingatRO=0;
		$pendingatCEO=0;
		$Totaldisqualifiedcount=0;
		
       
        if(isset($totalContestedCandidatedata) && !empty($totalContestedCandidatedata)){
       

         foreach($totalContestedCandidatedata as $key=>$listdata){
        
         //@forelse ($totalContestedCandidatedata as $key=>$listdata)
         
         //dd($listdata);
         $TotalUsers +=$listdata->totalcandidate;
         
         $stdetails=getstatebystatecode($listdata->st_code);
         $acbystate=getacbystate($listdata->st_code);
		 $currelectionbyeid=\app(App\models\Expenditure\EciExpenditureModel::class)->expcurrentelectiondetails('AC',$listdata->st_code,$election_id,'');

         $account=count($currelectionbyeid);
         $Totalac += $account;
		  $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);
		 
		 $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('AC',$listdata->st_code,$cons_no);
         $TotalFinalByDEO += $finalbyDEO;
		 
        // $pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('AC',$listdata->st_code,$cons_no);
        // $TotalPendingatCEO += $pendingatCEO;
		 
		
		
		 
         $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('AC',$listdata->st_code,$cons_no);
         $TotalPendingatECI += $pendingatECI;
		 
		 
		 
		
		 
         $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
         $TotalfiledData +=  $filedcount;
		  
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('AC',$listdata->st_code,$cons_no);
         $Totalfinalcompletedcount += $finalcompletedcount;
		 
		 
		 $disqualifiedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalDisqualifiedbyEci('AC',$listdata->st_code,$cons_no);
         $Totaldisqualifiedcount += $disqualifiedcount;
		 
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('AC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('AC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
		  //pending at DEO
		  if($finalbyDEO >= 0 ){
			$pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
			} 
		
		 //pending at CEO	
		if($finalbyDEO >=  0 && $pendingatECI >=0 && $finalcompletedcount >=0 && $noticeatCEOCount >=0){
		 $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount + $disqualifiedcount+$noticeatCEOCount);
		 if($pendingatCEO >= 0) { $TotalPendingatCEO += $pendingatCEO; }
		}
		 
         ?>
          <tr>
            <td>{{ $count }}</td>
            <td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>
			<td align="center">@if(empty($cons_no))   {{  $account }}  @else <b>{{$acdetails->AC_NAME}}</b> @endif</td>
          
           
		   <td align="center">@if(empty($listdata->totalcandidate) || $listdata->totalcandidate < 1 )     0  @else  <a href="{{url('/')}}/eci-expenditure/allcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" >  <b>{{ $listdata->totalcandidate }}</b> @endif</a></td>
		   
		  <!-- <td align="right"><a href="{{url('/')}}/eci-expenditure/Ecistartedcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($filedcount =='' )     0  @else  <b>{{ $filedcount }}</b> @endif</a></td>
		   
		   <td align="right"> <a href="{{url('/')}}/eci-expenditure/Ecinotstarted/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($notfiledcount =='' )     0  @else <b>{{  $notfiledcount }}</b> @endif </a></td>-->
		   
		   <td align="center"> @if(empty($finalbyDEO) ||  $finalbyDEO < 1)     0  @else <a href="{{url('/')}}/eci-expenditure/EcifinalbyDEO/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $finalbyDEO }}</b> @endif </a></td>
			
            
            <td align="right"> @if(empty($pendingatRO)||  $pendingatRO < 1)  <b> 0 </b>  @else  <a href="{{url('/')}}/eci-expenditure/pendingatro/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" title="toalcandidate-pendingatCEO">    {{  $pendingatRO }}   </a> @endif</td>
			
		    <td align="right"> @if(empty($noticeatDEOCount) || $noticeatDEOCount < 1 )     0  @else  <a href="{{url('/')}}/eci-expenditure/noticeatdeo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $noticeatDEOCount }}</b> @endif </a></td>
			
            <td align="right">@if(empty($pendingatCEO) || $pendingatCEO < 1)     0  @else <a href="{{url('/')}}/eci-expenditure/pendingatceo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" >  <b>{{  $pendingatCEO }}</b> @endif</a></td>
			
		    <td align="right">@if(empty($noticeatCEOCount) || $noticeatCEOCount < 1)     0  @else <a href="{{url('/')}}/eci-expenditure/noticeatceo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $noticeatCEOCount }}</b> @endif</a></td>
			
            <td align="center"> @if(empty($pendingatECI) || $pendingatECI < 1)     0  @else  <a href="{{url('/')}}/eci-expenditure/pendingateci/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" ><b>{{  $pendingatECI }}</b> @endif</a></td>
			
            <td align="center"> @if(empty($finalcompletedcount) || $finalcompletedcount < 1 )     0  @else  <a href="{{url('/')}}/eci-expenditure/finalbyeci/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" ><b>{{  $finalcompletedcount }}</b> @endif</a></td>
			
           <td align="center">@if(empty($disqualifiedcount) || $disqualifiedcount<1)     0  @else <a href="{{url('/')}}/eci-expenditure/disqualifiedbyeci/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $disqualifiedcount }}</b> @endif</a></td>
			
		
          </tr>
          <?php $count++;  ?>
           
          
        
                <tr>
                  <td colspan="6">No Data Found For Active Users</td>                 
              </tr>
         <?php } } ?>
          <tr><td><b>Total</b></td><td></td>
	  <td align="center"><b>@if(empty($cons_no)) {{$Totalac}} @endif</b></td>
	  <td align="center"><b>{{$TotalUsers}}</b></td>
	  <td align="center"><b>{{$TotalFinalByDEO}}</b></td>
	  <td align="right"><b>{{$TotalPendingatRO}}</b></td>
	  <td align="right"><b>{{$TotalDEONotice}}</b></td>
	  <td align="right"><b>{{$TotalPendingatCEO}}</b></td>
	  <td align="right"><b>{{$TotalCEONotice}}</b></td>
	  <td align="center"><b>{{$TotalPendingatECI}}</b></td>
	  <td align="center"><b>{{$Totalfinalcompletedcount}}</b></td>
	  <td align="center"><b>{{$Totaldisqualifiedcount}}</b></td>
	  </tr>
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
        	url: '<?php echo url('/') ?>/eci-expenditure/getacbystate',
            type: 'GET',
            data: {state:state},
         
            success: function(result){  
				//console.log(result); 
                var stateselect = jQuery('form select[name=ac]');
                stateselect.empty();
                var achtml = '';
                achtml = achtml + '<option value="">-- All AC --</option> ';
                jQuery.each(result,function(key, value) { 
                    achtml = achtml + '<option value="'+value.AC_NO+'">'+value.AC_NO+' - '+value.AC_NAME + '</option>';
                    jQuery("select[name='ac']").html(achtml);
                });
                var achtml_end = '';
                jQuery("select[name='ac']").append(achtml_end)
            }
        });
    });
});

</script>
@endsection


