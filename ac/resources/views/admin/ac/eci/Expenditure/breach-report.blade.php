@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Breach Details Report')
@section('description', '')
@section('content') 

@php 

$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$party = !empty($_GET['party'])?$_GET['party']:"";
$ac = !empty($_GET['ac']) ? $_GET['ac'] : $cons_no; 

$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
 
$graphText='';
if(!empty($st->ST_NAME)){
$graphText.=$st->ST_NAME;
}
if(!empty($acdetails->AC_NAME)){
$graphText.=' '.$acdetails->AC_NAME.'(AC)';
}
 if(empty($graphText)){
  $graphText='All States';
}
 $noData='';

@endphp


<style type="text/css">
    .mt-5, .my-5{margin-top: 1rem!important;}
</style>
<main role="main" class="inner cover mb-3">
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 mt-3"></div> <!---This Section Reserved for filter--->
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
                        @php Session::forget('message'); @endphp
                        @elseif (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ Session::get('error') }} <br/>

                        </div>
                        @php Session::forget('error'); @endphp
                        @endif
                        <div class=" row">
                            <div class="col"><h2 class="mr-auto">Candidate Wise Breaching Details</h2></div> 
                            <div class="col"><p class="mb-0 text-right">
                                    <b>State Name:</b> 
                                    <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                                    <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                                    <b>AC:</b> <span class="badge badge-info">{{$acName}}</span>
                                    <span class="badge badge-info"></span>&nbsp;&nbsp;
                                    <a href="{{url('/eci-expenditure/breach-details')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}?ac={{$ac}}&state={{$st_code}}&pdf=yes" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                                    <a href="{{url('/eci-expenditure/breach-details')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}?ac={{$ac}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                                    <a href="{{url('/')}}/eci-expenditure/breach-report/"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                                </p></div>
                        </div><!-- end row-->
                    </div><!-- end card-header-->
                    <div class="card-body"> 
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
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
										</tr>
                                            <tr>
                                                <th>S. No.:</th>
                                                <th>Candidate Name</th>
                                                <th>State</th>
                                                <th>AC No & AC Name</th>
                                                <th>Election Year</th>
                                                <th>Election Type</th>
												<th>Total Expenditure Assessed <br />By DEO(Rs.)</th>
												<th>Total Expenditure Declared <br />By Candidate(Rs.)</th>
												<th>Total Breach Amount(Rs.) <br /> (VIII-VII)</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $j = 0;
                                        $grandTotal = 0;
										$grandTotalAssessbyDEO=0;
										$avgTotalbycand=0;
										$avgbyAssessbyDEO=0;
										$grandTotalBreachAmnt=0;
										$count=1;
                                        ?>
                                        @if(!empty($candList))
                                        @foreach($candList as $candDetails)  
                                        <?php
									// dd($candDetails);
									 $count=count($candList);
									 $candidate_id=$candDetails->candidate_id;
                                     $acdetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
									 //dd($acdetails);
                                     $st = getstatebystatecode($candDetails->ST_CODE);
								     $totalamntassesbyDEO=$candDetails->amt_as_per_observation;
                                        ?>
                                      @php 
									   $grandTotalAssessbyDEO += $totalamntassesbyDEO;
                                       $totalamount = !empty($candDetails->grand_total_election_exp_by_cadidate)? $candDetails->grand_total_election_exp_by_cadidate : 0; 
                                        $grandTotal += $totalamount;
										$BreachAmnt=0;
										if(!empty($totalamntassesbyDEO) && ($totalamount != $totalamntassesbyDEO)){ 
										   $BreachAmnt=$totalamntassesbyDEO-$totalamount;
										}
										if(!empty($BreachAmnt) && $BreachAmnt > 0){
											$BreachAmnt = '+'.$BreachAmnt;
										}elseif(!empty($BreachAmnt) && $BreachAmnt < 0){
											$BreachAmnt = $BreachAmnt;
										}else{
											$BreachAmnt = 0;
										}
									     //$grandTotalBreachAmnt += $BreachAmnt;
										
                                        @endphp
                                        <tr>
                                            <td>{{++$j}}</td>
                                            <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif </td>
                                            <td>@if(!empty($st->ST_NAME)) {{$st->ST_NAME}}  @endif</td>
                                            <td>@if(!empty($acdetails->AC_NO)) {{$acdetails->AC_NO}} - {{$acdetails->AC_NAME}}  @endif</td>
                                            <td>@if(!empty($candDetails->YEAR)) {{$candDetails->YEAR}} @endif</td>
                                            <td>@if(!empty($candDetails->election_type)) {{strtoupper($candDetails->election_type)}} @endif</td>
											<td align="right"> {{$totalamntassesbyDEO}}</td>
											 <td align="right">{{$totalamount}}</td>
											<td align="right">{{$BreachAmnt}}</td>
                                        </tr>
                                        @endforeach 
                                        @endif 
                                        <tfoot>
                                            <tr>
											<td></td>
                                                <td colspan="5">Total(Rs.)</td>
												<td align="right"><b> {{$grandTotalAssessbyDEO}}</b></td> 
                                                <td align="right"><b> {{$grandTotal}}</b></td>
												<td align="right"><b></b></td>
                                            </tr>
											 @php
										     $avgTotalbycand= round($grandTotal/$count);
										     $avgbyAssessbyDEO= round($grandTotalAssessbyDEO/$count);
											 $avgBreachAmnt =round($grandTotalBreachAmnt/$count);
										     @endphp
											 <tr>
											    <td></td>
                                                <td colspan="5">Average(Rs.)</td>
												<td align="right"><b> {{$avgbyAssessbyDEO}}</b></td>
                                                <td align="right"><b> {{$avgTotalbycand}}</b></td>
												<td align="right"><b></b></td>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div> <!-- end responcive-->
                            </div>
                        </div>
                       
                    </div> <!-- end card-body-->
                </div>
            </div>
        </div>
        
    </section>

</main>
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery("select[name='state']").change(function () {
        var state = jQuery(this).val();
        // alert(state);
        jQuery.ajax({
            url: '<?php echo url('/') ?>/eci-expenditure/getacbystate',
            type: 'GET',
            data: {state: state},

            success: function (result) {
                console.log(result);
                var stateselect = jQuery('form select[name=ac]');
                stateselect.empty();
                var achtml = '';
                achtml = achtml + '<option value="">-- All AC --</option> ';
                jQuery.each(result, function (key, value) {
                    achtml = achtml + '<option value="' + value.AC_NO + '">' + value.AC_NO + ' - ' + value.AC_NAME + ' - ' + value.AC_NAME_HI + '</option>';
                    jQuery("select[name='ac']").html(achtml);
                });
                var pchtml_end = '';
                jQuery("select[name='ac']").append(achtml_end)
            }
        });
    });
});


</script>

<script>
$(document).ready(function() {
    $('#example1').append('<caption style="caption-side: top;">Candidate Wise Expenditure</caption>');
    var table = $('#example1').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
                footer:true,
               filename: function() {
                return 'candidate_wise_expenditure-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   AC:'.$acName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection
