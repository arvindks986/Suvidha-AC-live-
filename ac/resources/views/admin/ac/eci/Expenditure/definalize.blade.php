@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
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
              <form method="post" action="{{url('/eci-expenditure/definalize')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                    {{ csrf_field() }}
                      <!--STATE LIST DROPDOWN STARTS-->
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select State</label>    
                        <select name="state" id="state" class="form-control">
                      <option value="">All States</option>
                     @php $statelist = getallstate(); @endphp
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
            <div class="col"><h4> Definalize Data</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; 
              <b></b> 
              <!-- <span class="badge badge-info"></span>&nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/EciOfficerMISPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/EciOfficerMISEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
             <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->
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
	  <th>Not Started</th> 
	  <th>Final By DEO</th> 
	  <th>Final By CEO</th> 
	  <th>Final By ECI</th> 
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
$Totalac = 0;

@endphp
 @forelse ($definalizeData as $key=>$listdata)
 @php
 //dd($listdata);
 $TotalUsers +=$listdata->totalcandidate;
 
 $stdetails=getstatebystatecode($listdata->st_code);
 $acbystate=getacbystate($listdata->st_code);
  $account=count($acbystate);
  $Totalac += $account;
 $pendingatRO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalpartiallypending('AC',$listdata->st_code,$cons_no);
 $TotalPendingatRO += $pendingatRO;
 $pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('AC',$listdata->st_code,$cons_no);
 $TotalPendingatCEO += $pendingatCEO;
 $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('AC',$listdata->st_code,$cons_no);
 $TotalPendingatECI += $pendingatECI;
 $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('AC',$listdata->st_code,$cons_no);
 
 // Get Pending Data Count 
 $notfiledcount= $listdata->totalcandidate - $filedcount;
 $TotalnotfiledData += $notfiledcount;
 
 @endphp
  <tr>
	<td>{{ $count }}</td>
	<td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>
	<td align="right">@if($account =='' )   0  @else <b>{{  $account }}</b> @endif</td>
	<td align="right">@if($listdata->totalcandidate =='' )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
	<td align="right">@if($notfiledcount =='')     0  @else <b>{{  $notfiledcount }}</b> @endif</td>
   
   <td align="right"> <a href="{{url('/')}}/eci-expenditure/finalbyDEO/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" >@if($pendingatRO =='' )     0  @else <b>{{  $pendingatRO }}</b> @endif </a></td>
	
	<td align="right"><a href="{{url('/')}}/eci-expenditure/finalbyCEO/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}">@if($pendingatCEO =='')     0  @else <b>{{  $pendingatCEO }}</b> @endif</a></td>
	
	<td align="right"><a href="{{url('/')}}/eci-expenditure/finalbyECI/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}">@if($pendingatECI =='') 0  @else <b>{{  $pendingatECI }}</b> @endif</a></td>
   
   @php  $count++;  @endphp
   
   @empty
		<tr>
		  <td colspan="6">No Data Found For Active Users</td>                 
	  </tr>
  @endforelse
  <tr><td><b>Total</b></td><td></td><td align="right"><b>{{$Totalac}}</b></td><td align="right"><b>{{$TotalUsers}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>{{$TotalPendingatRO}}</b></td><td align="right"><b>{{$TotalPendingatCEO}}</b></td><td align="right"><b>{{$TotalPendingatECI}}</b></td></tr>
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


