@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Officers Report')
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
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp

<main role="main" class="inner cover mb-3">
<section class="mt-5">
<div class="container-fluid">
  <div class="row text-center pt-2 pb-1">
  <div class="col-sm-12"><h4><b> ECI ELECTION EXPENDITURE MONITORING SYSTEM GENERAL AC ELECTION-2019</b></h4></div>
				         <div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/eci-expenditure/report-officer')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                    {{ csrf_field() }}
            <!--Year LIST DROPDOWN STARTS--
            <div class="col-sm-1">
            <label for="" class="mr-1">Select Year</label>    
            <select name="year" id="year" class="form-control">
              <option value="2019" selected="selected" >2019</option>
              </select> 
            </div>
                <!--Year LIST DROPDOWN ENDS-->
              <!--Election Type DROPDOWN STARTS--
          <div class="col-sm-2">
              <label for="" class="mr-2">Select Election Type</label>    
              <select name="election_type" id="election_type" class="form-control">
                 <option value="0">Select Election Type</option>
                 <option value="1">PC-GENERAL</option>
                  <option value="2">PC-BYE</option>
                </select> 
          </div>
          <!--Election Type LIST DROPDOWN ENDS-->
                     
                          <!--STATE LIST DROPDOWN STARTS-->
					<div class="col-sm-3">
					<label for="" class="mr-3">Select State</label>    
                      <select name="state" id="state" class="form-control">
                    <?php if($stateName=='ALL') { ?> <option value="">All States</option> <?php } ?>
					 @if (!empty($statelist))
                      @foreach ($statelist as $state_List ))
                        @if ($st_code == $state_List->ST_CODE)
                              <option value="{{ $state_List->ST_CODE }}" selected>{{ $state_List->ST_CODE }}-{{$state_List->ST_NAME}}</option>
                        @else
                              <option value="{{ $state_List->ST_CODE }}">{{ $state_List->ST_CODE }}-{{$state_List->ST_NAME}}</option>
                        @endif
                      @endforeach
                     @endif 
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
            <div class="col"><h4> Summary Report</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; 
              <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/EciOfficerReportPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/EciOfficerReportEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body"> 
  <div class="table-responsive">
     <table id="examples" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
		  <th>Total AC</th> 
          <th>Total Candidate</th> 
          <th>Completed</th> 
          <th>InProgress</th> 
          <th>NotStarted</th> 
         </tr>
        </thead>
       
        @php  
        $count = 1; 
        $TotalUsers = 0;
        $TotalfiledData = 0;
        $TotalnotfiledData = 0;
        $Totalfinalcompletedcount= 0;
        $Totalac = 0;
        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php
         //dd($listdata);
         $TotalUsers +=$listdata->totalcandidate;
         
         $stdetails=getstatebystatecode($listdata->st_code);
		 $acbystate=getacbystate($listdata->st_code);
		 $account=count($acbystate);
		 $Totalac += $account;
         $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
       
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('AC',$listdata->st_code,$cons_no);
         $TotalfiledData +=  $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $Totalfinalcompletedcount += $finalcompletedcount;
         @endphp
          <tr>
            <td>{{ $count }}</td>
            <td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>
			<td align="right">@if($account =='' )   0  @else <b>{{  $account }}</b> @endif</td>
            <td align="right">@if($listdata->totalcandidate =='' )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
            <td align="right"> @if($finalcompletedcount =='' )     0  @else <b>{{  $finalcompletedcount }}</b> @endif </td>
            <td align="right"> @if($filedcount =='')     0  @else <b>{{  $filedcount }}</b> @endif</td>
            <td align="right">@if($notfiledcount =='')     0  @else <b>{{  $notfiledcount }}</b> @endif</td>
          </tr>
           @php  $count++;  @endphp
          
           @empty
                <tr>
                  <td colspan="6">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td></td><td align="right"><b>{{$Totalac}}</b></td><td align="right"><b>{{$TotalUsers}}</b></td><td align="right"><b>{{$Totalfinalcompletedcount}}</b></td><td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td></tr>
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


