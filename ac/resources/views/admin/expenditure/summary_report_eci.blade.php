@extends('admin.layouts.ac.expenditure-theme')
@section('content')
<?php
$namePrefix = \Route::current()->action['prefix'];


$stateName = !empty($ReportSingleData['state']) ? $ReportSingleData['state'] : "N/A";
//$date_of_declaration = !empty($ReportSingleData['date_of_declaration']) && strtotime($ReportSingleData['date_of_declaration']) > 0 ? $ReportSingleData['date_of_declaration'] : "23-06-2019";

 $last_date_prescribed_acct_lodgenot = !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('d-m-Y',strtotime($resultDeclarationDate['start_result_declared_date'].' + 30 days ')):'N/A';

$last_date_prescribed_acct_lodge = !empty($candList[0]->last_date_prescribed_acct_lodge) && strtotime($candList[0]->last_date_prescribed_acct_lodge) > 0 ? date('d-m-Y', strtotime($candList[0]->last_date_prescribed_acct_lodge)) : $last_date_prescribed_acct_lodgenot;

$date_of_declarationnot = !empty($resultDeclarationDate['start_result_declared_date']) ? date('d-m-Y',strtotime($resultDeclarationDate['start_result_declared_date'])):'N/A';

$date_of_declaration = !empty($candList[0]->date_of_declaration) && strtotime($candList[0]->date_of_declaration) > 0 ? date('d-m-Y', strtotime($candList[0]->date_of_declaration)) : $date_of_declarationnot;
?>
@php
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($ac_no) ? $ac_no : '0';
$st=getstatebystatecode($st_code);

$distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
$acdetails=getacbyacno($st_code, $cons_no); 
$acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
$stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
$all_ac=getacbystate($st_code);
//echo  $stateName.'-'.$st_code.'cons_no=>'.$cons_no;

@endphp 

<main role="main" class="inner cover mb-3">
    <!--FILTER STARTS FROM HERE-->
    <!--    <div class="card-header pt-3">
    
            <form method="get" action="{{url($namePrefix.'/GetTrackingReportData')}}" id="EciCustomReportFilter">
                <div class=" row">
                    <div class="col-sm-12 text-center">
                        <h4><b>SUMMARY REPORT</b></h4>
                    </div>
                </div>
            </form>
        </div>-->
    <div class="card-header pt-3" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center pt-2 pb-1">
                <div class="col-sm-12"><h4><b>
                            SUMMARY REPORT OF PC ELECTION-2019
                        </b></h4></div>
                <div class="col-sm-12 mt-3">
                    <!--FILTER STARTS FROM HERE-->
                    <form method="post" action="{{url('/eci-expenditure/reports')}}" id="EcidashboardFilter">           
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
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select AC</label>    
                                <select name="ac" id="ac" class="consttype form-control" required="required" >
                                     
                                    @if (!empty($all_ac))
                                    <?php //dd($all_pc); ?> 
                                    @foreach($all_ac as $getac) 
                                    @if ($cons_no == $getac->AC_NO)
                                    <option value="{{ $getac->AC_NO }}" selected>{{$getac->AC_NO }} - {{$getac->AC_NAME }}- {{$getac->AC_NAME_HI}}</option>
                                    @else
                                    <option value="{{ $getac->AC_NO }}" >{{$getac->AC_NO }} - {{$getac->AC_NAME }} - {{$getac->AC_NAME_HI}}</option>
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
                                    <a href="{{url('/eci-expenditure/reports')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a>
                                </p>
                            </div>
                        </div>
                    </form> 
                    <!--FILTER ENDS HERE-->
                </div> 
            </div> 
        </div>
    </div>
    <section class="mt-4">
        <div class="container-fluid">
            <div class="row">
                <!--  <div class="pull-right"><a href="{{url($namePrefix.'/CreateMisExpenseReport')}}"> <button type="button" class="btn btn-primary" >Add NEW</button></a></div> -->
                <div class="card text-left" style="width:100%;">
                    <div class=" card-header">
                        <div class=" row d-flex align-items-center">
                            <div class="col text-center mb-3 mt-3"><h4>Summary Report of DEO For Each Constituency on Lodging of Election Expenses Accounts by Candidate</h4>
                            </div> 
                            <a href="{{url($namePrefix.'/trackingReportprint/'.base64_encode($st_code).'/'.base64_encode($cons_no))}}"  class="btn btn-primary" target="_blank">Print</a>
                        </div>

                        <div class=" row d-flex align-items-center"> 
                            <div class="col"><p class="mb-1 mt-3">(a) No. and Assembly Constituency:  <strong> &nbsp; {{!empty($Acdetail->AC_NO) ? $Acdetail->AC_NO : ''}}-{{!empty($Acdetail->AC_NAME) ? $Acdetail->AC_NAME : ''}}</strong></p></div>
                            <div class="col"><p class="mb-1 mt-3">(b) Total No. Contesting Candidates: <strong>{{count($candList)}}</strong></p></div>
                        </div>
                        <div class=" row d-flex align-items-center">  
                            <div class="col"><p class="mb-1">(c) State  <strong> {{!empty($stateDetail->ST_NAME)?$stateDetail->ST_NAME:''}}&nbsp;&nbsp;</strong></p></div>
                            <div class="col"><p class="mb-1">(d) Date of Declaration of Result of Election/Bye-election: <strong>{{$date_of_declaration}}</strong></p></div>
                        </div>
                        <div class=" row d-flex align-items-center"> 
                            <div class="col"><p class="mb-2">(e) Last Date of Lodging Accounts: <strong>{{$last_date_prescribed_acct_lodge}}</strong></p></div>
                            <div class="col"><p class="mb-2">(f) Name of the Elected Candidate:<strong> {{!empty($winn_data->lead_cand_name)? $winn_data->lead_cand_name:'N/A'}}</strong></p></div>
                        </div>
                    </div>
                    <!-- End of Summary Report Form -->

                    <div class="table-responsive"><table class="table table-bordered">
                            <thead class="text-center">
                                <tr>
                                    <th>1.</th>
                                    <th>2.</th>
                                    <th>3.</th>
                                    <th>4.</th>
                                    <th>5.</th>
                                    <th>6.</th>
                                    <th>7.</th>
                                    <th>8.</th>
                                    <th colspan="2">9.</th>
                                    <th colspan="2">10.</th>
                                    <th>11.</th>
                                </tr>
                                <tr>  
                                    <th rowspan="2">Sr .No.</th>
                                    <th rowspan="2">Name of the Candidate and Party Affiliation</th>
                                    <th rowspan="2">Due Date of Lodging of Account</th>
                                    <th rowspan="2">Date of Lodging of Account by the Candidate</th>
                                    <th rowspan="2">Whether Lodged in the Prescribed Format (Yes or No)</th>
                                    <th rowspan="2">Whether Lodged in the manner required by Law (Yes or No)</th>
                                    <th rowspan="2">Grand Total of the Expenses Incurred/Authorized by the Candidate/Agent (as mentioned in <em>Part-II</em> of Abstract Statement)</th>
                                    <th rowspan="2">Whether the DEO agrees with the amount shown by the candidate against all items of expenditure (Should be similar to point no. <b>22</b> of DEO's Scrutiny Report i.e. Annexure -C3)</th>
                                    <th colspan="2">Total Expenses incurred by the Party (As reported in Part-III of Abstract Statement)</th>
                                    <th colspan="2">Total Expenses incurred by others/entities as reported in Part-III of Abstract Statement</th>
                                    <th rowspan="2">Remarks of the Expenditure Observer</th>          
                                </tr>
                                <tr>
                                    <th>Lump Sum Amount in cash or cheque given to candidate by each Political Party</th>
                                    <th>Grand Total of other Expenses kind by the Political Party</th>
                                    <th>Lump Sum Amount in cash/cheque given to the candidate (and mention names of donors)</th>
                                    <th>Grand Total of other expenses in kind incurred for the candidate</th>    
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($candList)>0)
                                @foreach($candList as $key =>$item)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{$item->cand_name}}-{{$item->PARTYNAME}}</td>
                                    <!-- <td>{{!empty($item->last_date_prescribed_acct_lodge) && strtotime($item->last_date_prescribed_acct_lodge)>0? date('d-m-Y',strtotime($item->last_date_prescribed_acct_lodge)):'N/A' }}</td> -->

                                    <?php
                                    $cc_amt = !empty($item->political_fund_cash) ? $item->political_fund_cash : 0;
                                    $cq_amt = !empty($item->political_fund_checque) ? $item->political_fund_checque : 0;
                                    ?>
                                    <td>{{$last_date_prescribed_acct_lodge}}</td>
                                    <td>{{!empty($item->date_orginal_acct) && strtotime($item->date_orginal_acct)>0 && !empty($item->candidate_lodged_acct) && $item->candidate_lodged_acct =="Yes"? date('d-m-Y',strtotime($item->date_orginal_acct)):'Not Lodged' }}</td>
                                    <td>{{!empty($item->candidate_lodged_acct)?$item->candidate_lodged_acct:'N/A'}}</td>
                                    <td>{{!empty($item->rp_act)?$item->rp_act:'N/A'}}</td>
                                    <td>{{$item->grand_total_election_exp_by_cadidate}}</td>
                                    <td>{{!empty($item->status)?$item->status:'N/A'}} </td>
                                    <td>{{!empty($cc_amt+$cq_amt) && ($cc_amt+$cq_amt)>0? $cc_amt+$cq_amt:'N/A'}} </td>
                                    <td>{{!empty($item->political_fund_kind)?$item->political_fund_kind:'N/A'}} </td>
                                    <td>{{!empty($item->other_source_amt_cc) && ($item->other_source_amt_cc)>0?$item->other_source_amt_cc:'N/A'}} </td>
                                    <td>{{!empty($item->other_source_amt_kind)?$item->other_source_amt_kind:'N/A'}} </td>
                                    <td> {{!empty($item->comment_9)?$item->comment_9:'N/A'}}</td>  

                                </tr>
                                @endforeach
                                @endif

                            </tbody>
                        </table>
                        <table class="table text-center">
                            <tr>
                                <td class="bdr-none pl-5 pr-5 pt-3">
                                    <p>Comments of the Expenditure Observer, If any, &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline mr-5"> &nbsp;&nbsp; Signature of the DEO &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline"></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- End of Summary Report Form -->
                </div>
            </div>
        </div>
    </section>
</main>

@endsection

@section('script')
<script>
    jQuery(document).ready(function () {
        jQuery("select[name='state']").change(function () {
            var state = jQuery(this).val();
            // alert(state);
            jQuery.ajax({
                url: '<?php echo url('/') ?>/eci-expenditure/getacbystate',
                type: 'GET',
                data: {state: state},

                success: function (result) {

                    var stateselect = jQuery('form select[name=ac]');
                    stateselect.empty();
                    var pchtml = '';
                    
                    jQuery.each(result, function (key, value) {
                        pchtml = pchtml + '<option value="' + value.AC_NO + '">' + value.AC_NO + ' - ' + value.AC_NAME + ' - ' + value.AC_NAME_HI + '</option>';
                        jQuery("select[name='ac']").html(pchtml);
                    });
                    var pchtml_end = '';
                    jQuery("select[name='ac']").append(pchtml_end)
                }
            });
        });

    });

</script>
@endsection




