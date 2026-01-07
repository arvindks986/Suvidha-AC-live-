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
$district=!empty($district) ? $district : '0';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=!empty($all_ac)?$all_ac:getacbystate($st_code);

//echo $st_code.'cons_no'.$cons_no; die;
 $allStates=[];
 
@endphp
<style type="text/css">
    .fixeddatatableheader{
        overflow: visible;
    }
</style>

<main role="main" class="inner cover mb-3">
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row text-center pt-2 pb-1">
                <div class="col-sm-12"><h4><b>ELECTION EXPENDITURE MONITORING SYSTEM GENERAL AC ELECTION-2019</b></h4></div>
                <div class="col-sm-12 mt-3">
                    <!--FILTER STARTS FROM HERE-->
                    <form method="post" action="{{url('/eci-expenditure/district-report')}}" id="EcidashboardFilter">           
                        <div class="row justify-content-center">
                            {{ csrf_field() }}
                            <!--STATE LIST DROPDOWN STARTS-->
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select State</label>    
                                <select name="state" id="state" class="form-control" required="required">
                                    <?php if ($stateName == 'ALL') { ?> <option value="">All States</option> <?php } ?>
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
                            <!-- District start -->
                            <!--                            <div class="col-sm-1">
                                                             
                                                            <input type="checkbox"  name="isdistrict" id="isdistrict" />District
                                                        
                                                        </div>  
                            -->
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select District</label>    
                                <select name="district" id="district" class="form-control">
                                    <option value="">All District</option>  
                                    @foreach ($districts as $districtItem ))
                                    @if ($district == $districtItem->DIST_NO)
                                    <option value="{{ $districtItem->DIST_NO }}" selected>{{$districtItem->DIST_NAME}}</option>
                                    @else
                                    <option value="{{ $districtItem->DIST_NO }}">{{$districtItem->DIST_NAME}}</option>
                                    @endif
                                    @endforeach

                                    @if ($errors->has('district'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('district') }}</strong>
                                    </span>
                                    @endif
                                    <div class="stateerrormsg errormsg errorred"></div>
                                </select> 
                            </div>
                            <!-- District end -->
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select AC</label>    
                                <select name="ac" id="ac" class="consttype form-control" >
                                    <option value="">-- All AC --</option>
                                    @if (!empty($all_ac))

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
                                    <a href="{{url('/eci-expenditure/district-report')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a>
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
                                    <a href="{{url('/eci-expenditure/districtreportpdf')}}/{{base64_encode($st_code)}}/{{base64_encode($district)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                                    <a href="{{url('/eci-expenditure/districtreportexl')}}/{{base64_encode($st_code)}}/{{base64_encode($district)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                                    <!--<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->
                                </p>
                            </div>
                        </div>
                        <div class="row" style="width:100%;"><h4> District Wise Report Regarding DEO's Scrutiny Report On Account Of Contesting Candidates.</h4></div> 
                    </div>

                      <div class="card-body"> 
                        <div class="table-responsive">
                            <table id="exampled" class="table table-striped table-bordered table-hover"  >
                                <thead class="text-center">
                                    <tr> 
                                        <th>Serial No</th>
                                        <th>State</th> 
                                        <th>District</th> 
                                        <th>AC No & AC Name</th> 
                                        <th>Total Candidates</th> 
                                        <th>Started</th> 
                                        <th>Not Started</th> 
                                        <th>Finalised By DEO</th> 
                                        <th>Pending-DEO</th>
                                        <th>Pending-CEO</th>
                                        <th>Pending - ECI</th> 
                                        <th>Closed/Disqualified/Case Dropped</th> 
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
                                $allStates=[];
                                @endphp
                                @forelse ($totalContestedCandidate as $key=>$listdata)
                                @php
                                //dd($listdata);
                                $TotalUsers +=$listdata->totalcandidate;

                                $stdetails=getstatebystatecode($listdata->st_code);
                                $st_code=!empty($st_code)? $st_code :$listdata->st_code;       
                                $allStates[]=[
                                'st_code'=>$st_code,
                                'ac_no'=>$listdata->ac_no,
                                ];
                                $cons_no=$listdata->ac_no;
                                $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('AC',$listdata->st_code,$cons_no);
                                $TotalFinalByDEO += $finalbyDEO;

                                // $pendingatROold=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalpartiallypending('AC',$listdata->st_code,$cons_no);


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

                                //pending at DEO
                                $pendingatRO='';
           if($pendingatECI > 0 || $pendingatCEO >= 0 || $finalcompletedcount > 0){
            $pendingatRO=$listdata->totalcandidate-($pendingatCEO+$pendingatECI+$finalcompletedcount);
            $TotalPendingatRO += $pendingatRO;
            }

                                // get district start here
                                $detriectdetails = DB::table('m_ac')
                                ->where('ST_CODE',$listdata->st_code)
                                ->where('AC_NO',$listdata->ac_no)
                                ->groupBy('m_ac.DIST_NO_HDQTR')
                                ->get();
                                $districtids=[];
                                if(!empty($detriectdetails)){
                                foreach($detriectdetails as $item){                         
                                $districtids[]=$item->DIST_NO_HDQTR;
                                }

                                }

                                $allDistrict='';
                                if(!empty($districtids)){
                                foreach($districtids as $id)
                                { 
                                $district=getdistrictbydistrictno($listdata->st_code,$id);
                                $allDistrict.=$district->DIST_NAME.' ,';
                                }
                                }
                                $alldistricts1=rtrim($allDistrict,',');
                                // get district end here 
                                $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);


                                @endphp
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>
                                    <td>@if(empty($alldistricts1) && $alldistricts1=='' )   'N/A'  @else <b>{{  $alldistricts1 }}</b> @endif</td>
                                    <td align="right">{{$acdetails->AC_NO}}-{{$acdetails->AC_NAME}}</td>



                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/allcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($listdata->totalcandidate =='' )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</a></td>

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/Ecistartedcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($filedcount =='' )     0  @else  <b>{{ $filedcount }}</b> @endif</a></td>

                                    <td align="right"> <a href="{{url('/')}}/eci-expenditure/Ecinotstarted/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($notfiledcount =='' )     0  @else <b>{{  $notfiledcount }}</b> @endif </a></td>

                                    <td align="right"> <a href="{{url('/')}}/eci-expenditure/EcifinalbyDEO/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($finalbyDEO =='' )     0  @else <b>{{  $finalbyDEO }}</b> @endif </a></td>


                                    <td align="right"> @if($pendingatRO !='' )  <a href="{{url('/')}}/eci-expenditure/pendingatro/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" title="toalcandidate-pendingatCEO">    {{  $pendingatRO }}   @else <b> 0 </b>  </a> @endif</td>


<!-- <td align="right"> <a href="{{url('/')}}/eci-expenditure/noticeatdeo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($noticeatDEOCount =='' )     0  @else <b>{{  $noticeatDEOCount }}</b> @endif </a></td>-->

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/pendingatceo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($pendingatCEO =='')     0  @else <b>{{  $pendingatCEO }}</b> @endif</a></td>

<!-- <td align="right"><a href="{{url('/')}}/eci-expenditure/noticeatceo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($noticeatCEOCount =='')     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</a></td>-->

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/pendingateci/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($pendingatECI =='')     0  @else <b>{{  $pendingatECI }}</b> @endif</a></td>

                                    <td align="right"><a href="{{url('/')}}/eci-expenditure/closedbyeci/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($finalcompletedcount =='')     0  @else <b>{{  $finalcompletedcount }}</b> @endif</a></td>
                                </tr>
                                @php  $count++;  @endphp

                                @empty
                                <tr>
                                    <td colspan="6">No Data Found For Active Users</td>                  
                                </tr>
                                @endforelse

                                <?php
                                 if (!empty($allStates)) {

                                   if(!empty($allStates[0]['st_code']) && $allStates[0]['st_code']=="All"){
                                        foreach ($permitstates as $item) {                                        
                                              $Totalac += DB::table('m_ac')
                                                ->where('ST_CODE', $item)                                                 
                                                ->count();
                                        }
                                               

                                          }else{
                                             foreach ($allStates as $item) {
                                              $Totalac += DB::table('m_ac')
                                                ->where('ST_CODE', $item['st_code'])
                                                ->where('AC_NO', $item['ac_no'])
                                                ->count();
                                              }
                                          }
                                   
                                }
                                ?>
                                <tr><td><b>Total</b></td>
                                    <td></td><td></td>
                                    <td align="right"><b>{{$Totalac>0 ? $Totalac:0}}</b></td><td align="right"><b>{{$TotalUsers}}</b></td><td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>{{$TotalFinalByDEO}}</b></td><td align="right"><b>{{$TotalPendingatRO}}</b></td><td align="right"><b>{{$TotalPendingatCEO}}</b></td><td align="right"><b>{{$TotalPendingatECI}}</b></td><td align="right"><b>{{$Totalfinalcompletedcount}}</b></td></tr>
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
$(document).ready(function() {
    var table = $('#exampled').DataTable({       
        scrollX:        true,
        scrollCollapse: true,
        paging:         true,
        // fixedColumns:   {
        //     leftColumns: 3,
        //     rightColumns: 1
        // },
    lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
        pageLength: 10
    });
   });
    jQuery(document).ready(function () {

        jQuery("select[name='state']").change(function () { 
            var state = jQuery(this).val();     
            jQuery.ajax({
                url: '<?php echo url('/') ?>/eci-expenditure/getdistricts/' + state,
                type: 'GET',
                success: function (result) {
                    // console.log(result);
                    var stateselect = jQuery('form select[name=district]');
                    stateselect.empty();
                    var districthtml = '';
                    districthtml = districthtml + '<option value="">-- All District --</option> ';
                    jQuery.each(result, function (key, value) {
                        // console.log(key);

                        districthtml = districthtml + '<option value="' + value.DIST_NO + '">' + value.DIST_NAME + '</option>';
                        jQuery("select[name='district']").html(districthtml);
                    });
                    var districthtml_end = '';
                    jQuery("select[name='district']").append(districthtml_end)
                }
            });
            // all AC based on state code start here
            jQuery.ajax({
                url: '<?php echo url('/') ?>/eci-expenditure/getdistrictacs',
                type: 'get',
                data: {state: state, district: ''},
                success: function (result) {
                   
                    var achtml = '';
                    achtml = achtml + '<option value="">-- All AC --</option> ';
                    jQuery.each(result, function (key, value) {
                        achtml = achtml + '<option value="' + value.AC_NO + '">' + value.AC_NO + ' - ' + value.AC_NAME + '</option>';
                        jQuery("select[name='ac']").html(achtml);
                    });
                    var achtml_end = '';
                    jQuery("select[name='ac']").append(achtml_end)
                },

            });
            // all AC end here

            // District end here
        });
        // AC all based on state and district  start =======================
        jQuery("select[name='district']").change(function () {
           var stateselect = jQuery('form select[name=ac]');
                 stateselect.empty();
            var state = jQuery('#state').val();
            var district = jQuery(this).val();

            jQuery.ajax({
                url: '<?php echo url('/') ?>/eci-expenditure/getdistrictacs',
                type: 'get',
                data: {state: state, district: district},
                success: function (result) {
                   // console.log(result);
                  
                    var achtml = '';
                    achtml = achtml + '<option value="">-- All AC --</option> ';
                    jQuery.each(result, function (key, value) {
                        achtml = achtml + '<option value="' + value.AC_NO + '">' + value.AC_NO + ' - ' + value.AC_NAME + '</option>';
                        jQuery("select[name='ac']").html(achtml);
                    });
                    var achtml_end = '';
                    jQuery("select[name='ac']").append(achtml_end)
                },

            });


            // District end here
        });
        // AC all based on state and district  end 
        // end here
    });

</script>
@endsection


