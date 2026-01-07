@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'EXPENDITURE')
@section('bradcome', 'MIS-BREACH AMOUNT')
@section('description', '')
@section('content')
@php 

$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$party = !empty($_GET['party'])?$_GET['party']:"";
$ac = !empty($_GET['ac'])?$_GET['ac']: $cons_no; 
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
  <div class="col-sm-12"><h4><b>ELECTION EXPENDITURE MONITORING SYSTEM FOR ASSEMBOLY ELECTION</b></h4></div>
				         <div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/eci-expenditure/breach-report')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                    {{ csrf_field() }}
                      <!--STATE LIST DROPDOWN STARTS-->
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select State</label>    
                        <select name="state" id="state" class="form-control">
                     <?php if($stateName=='ALL') { ?> <option value="">All States</option> <?php } ?>
                     <?php //$statelist = getallstate(); ?>
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
                              <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NO }} - {{$getAc->AC_NAME }}- {{$getAc->AC_NAME_HI}}</option>
                              @else
									<option value="{{ $getAc->AC_NO }}" > 
									{{$getAc->AC_NO }} - {{$getAc->AC_NAME }} - {{$getAc->AC_NAME_HI}}</option>
									 @endif
								@endforeach 
                        @endif
							</select>
					    @if ($errors->has('ac'))
                  		  <span style="color:red;">{{ $errors->first('ac') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
						<!---------PC/AC section ends-------->
							<!---------Start Return/Non Return Type----
							<div class="col-sm-3">
                                <label for="" class="mr-3">Select Return/Non Type</label>    
                                <select name="returnType" id="returnType" value="{{old('returnType')}}" class="consttype form-control" >
                                    <option value="">-- All--</option>
                                    <option value="return" <?php
                                    if (!empty($_GET['returnType']) && $_GET['returnType'] == 'return') {
                                        echo "selected";
                                    }
                                    ?>>Return Type</option>
									<option value="non-return" <?php
                                    if (!empty($_GET['returnType']) && $_GET['returnType'] == 'non-return') {
                                        echo "selected";
                                    }
                                    ?>>Non Return Type</option>
                                </select>
                                @if ($errors->has('returnType'))
                                <span style="color:red;">{{ $errors->first('returnType') }}</span>
                                @endif

                                <div class="acerrormsg errormsg errorred"></div>
                            </div>
							<!---------Ends Return/Non Return Type ----->
					  	<div class="col-sm-2 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
						   <a href="{{url('/eci-expenditure/breach-report')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a>
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
               <a href="{{url('/eci-expenditure/breach-report')}}?ac={{$ac}}&state={{$st_code}}&pdf=yes" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
               <a href="{{url('/eci-expenditure/breach-report')}}?ac={{$ac}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
             <!-- <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->
              </p>
              </div>
            </div>
			 <div class="row" style="width:100%;"><h4> Officer's MIS Regarding DEO's Scrutiny Report On Account Of Contesting Candidates.</h4></div> 
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
		  <th>Total Candidates Who's Expenditure is Breaching</th> 
          <th>Total Candidates Without Breaching Amount<BR /> IV-V</th> 
         </tr>
        </thead>
       
        @php  
        $count = 1; 
        $TotalUsers = 0;
        $Totalac = 0;
		$TotalBreaching=0;
		$TotalWTBreaching=0;
        @endphp
         @forelse ($candList as $key=>$listdata)
         @php
         $TotalUsers +=$listdata->totalcandidate;
         $stdetails=getstatebystatecode($listdata->st_code);
         $acbystate=getacbystate($listdata->st_code);
         $account=count($acbystate);
         $Totalac += $account;
		 $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);
       
	    $breachcount=\app(App\models\Expenditure\ExpenditureModel::class)->gettotalbreaching('AC',$listdata->st_code,$cons_no);
		 $breachcount=$breachcount[0]->breachcount;
		     if(!empty($breachcount)){  //dd($candUnderStatasDetails);
				 $TotalBreaching += $breachcount;
		     }
				
		 //without breaching amount
		  if($breachcount >= 0 ){
			$withoutBreach=$listdata->totalcandidate-($breachcount);
			}  
		 $TotalWTBreaching += $withoutBreach;

		 
         @endphp
          <tr>
            <td>{{ $count }}</td>
            <td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>

            <td align="right">@if(empty($cons_no))   {{  $account }}  @else <b>{{$acdetails->AC_NAME}}</b> @endif</td>
			
            <td align="right">@if(empty($listdata->totalcandidate) || $listdata->totalcandidate <1 )     0  @else <a href="{{url('/')}}/eci-expenditure/allcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" >  <b>{{ $listdata->totalcandidate }}</b> @endif</a></td>
			
			<td align="right" data-toggle="tooltip" data-placement="top" title='for details pls click'> @if(empty($breachcount) || $breachcount <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/breach-details/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $breachcount }}</b> @endif </a></td>
			
            <td align="right" > @if(empty($withoutBreach))     0  @else <b>{{  $withoutBreach }}</b> @endif</td>
			
          </tr>
           @php  $count++;  @endphp
          
           @empty
                <tr>
                  <td colspan="6">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td></td>
          <td align="right"><b> @if(empty($cons_no)) {{$Totalac}} @endif</b>
          </td>
          <td align="right"><b>{{$TotalUsers}}</b>
          </td>
	      <td align="right"><b>{{$TotalBreaching}}</b></td><td align="right"><b>{{$TotalWTBreaching}}</b></td></tr>
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
	$('[data-toggle="tooltip"]').tooltip();
});

</script>
@endsection


