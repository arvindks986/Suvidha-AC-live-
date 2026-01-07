@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Notification Details')
@section('bradcome', 'Dashboard')
@section('description', '')
@section('content')
@php
 
 $cons_no = !empty($_GET['ac']) ? trim($_GET['ac']) : "0";

$stCode = !empty($_GET['state']) ? $_GET['state'] : "";
$st=getstatebystatecode($stCode);
$acdetails=getacbyacno($stCode,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($stCode);
 $case = !empty($_GET['case']) ? trim($_GET['case']) : "";
  
$ac = !empty($_GET['ac']) ? trim($_GET['ac']) : "";
$acdetail = getacbyacno($stCode, $ac);
$statedetail = getstatebystatecode($stCode);
$case = !empty($_GET['case']) ? trim($_GET['case']) : "";
@endphp 
<style>
    .showmessagereceived{
        color: green;
        font-weight: 600;
        font-size: 16px;
    }
</style>
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">

                <div class="col-sm-12 mt-3">
                    <!--FILTER STARTS FROM HERE-->
                    <form method="get" action="{{url('/eci-expenditure/receivedNotification')}}" id="EcidashboardFilter">
                    <div class="row justify-content-center">
                            <!--STATE LIST DROPDOWN STARTS-->
                            <div class="col-sm-3">                                 
                                <label>Specific Case</label>
                                <input type="radio" name="case" @if(!empty($case) && $case=="specific") checked @endif value="specific">
                                <label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bulk Cases</label>
                                <input type="radio" name="case" @if(!empty($case) && $case=="bulk") checked @endif value="bulk"  >
                            </div> 
                             
                        </div>            
                        <div class="row justify-content-center">
                            <!--STATE LIST DROPDOWN STARTS-->
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select State</label>    
                                <select name="state" id="state" class="form-control">
                                    <option value="">All States</option>
                                    @php $statelist = getallstate(); @endphp
                                    @foreach ($statelist as $state_List ))
                                    <option value="{{ $state_List->ST_CODE }}" <?php
                                    if (!empty($_GET['state']) && $state_List->ST_CODE == $_GET['state']) {
                                        echo "selected";
                                    }
                                    ?>>{{$state_List->ST_NAME}}</option>
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

                <!--                <div class="col"><h6 class="mr-auto mt-2">Scrutiny Candidate List : </h6>
                                 
                                </div> -->

                <div class="col"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{!empty($statedetail->ST_NAME)? $statedetail->ST_NAME:'All'}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>AC:</b> <span class="badge badge-info">{{!empty($acdetail->AC_NAME)? $acdetail->AC_NAME:'All'}}</span>
                        <b>

                        </b>
                        <!--<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->

                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
        <form method="get" action="{{url('/eci-expenditure/updateReceived')}}">   
            <div class="row">

                @if (Session::has('message'))
                <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
                @php Session::forget('message'); @endphp
                @elseif (Session::has('error'))
                <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ Session::get('error') }} <br/>

                </div>
                @php Session::forget('error'); @endphp
                @endif

                <?php
                if (session()->get('success')) {
                    ?>
                    <div class="alert alert-success">
                        <?php echo session()->get('success'); ?>    
                    </div>
                <?php } ?>
                        

                    <span class="showmessagereceived"></span>

                    <div class="col-sm-12 mt-3">
                        <!--FILTER STARTS FROM HERE-->

                        <div class="row justify-content-center">                            
                            <div class="col-sm-3">
                                <label  class="mr-3">Select Final Action</label>    
                                <select name="final_action" id="final_action" required class="form-control" >
                                    <option value="">Select Final Action</option>
                                    <option value="Received">Received</option>
                                    <option value="Closed">Closed</option>                                        
                                    <option value="Disqualified">Disqualified</option>
                                    <option value="Case Dropped">Case Dropped</option>
                                </select>
                                @if ($errors->has('final_action'))
                                <span class="help-block">
                                    <strong class="user">{{ $errors->first('final_action') }}</strong>
                                </span>
                                @endif

                                <div class="acerrormsg errormsg errorred"></div>
                            </div>
                            <div class="col-sm-1 mt-2">
                                <p class="mt-4 text-left">                                     
                                    <input type="submit" value="Submit" id="Submit" class="btn btn-primary">
                                </p>
                            </div>
                        </div>

                        <!-- final action-->
                    </div> 
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card text-left" style="width:100%;">
                            <!--SELECT CANDIDATE-->

                            <div class="card-body" id="demo" class="collapse show">  

                                {{ csrf_field() }} 
                                <table id="example2" class="table table-striped table-bordered table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>State</th>
                                            <th>AC No & Name</th>
                                            <th>Candidate Name</th>
                                            <th>Party Name</th>
                                            <th>Date Of Lodging</th>
                                            <th class="width-180"> View </th>   
                                            <th> Status </th>
                                            <th> Action <br/>
                                  @if($case =="bulk")
                                                All &nbsp;<input type="checkbox"  id="ckbCheckAll">                                               
                                                @endif 
                                
                                                
                                                <!--<input type="button" value="Update" id="receivedDataAction" class="btn btn-primary">-->

                                            </th>
                                        </tr>
                                    </thead>
                                    <?php $j = 0; ?>
                                    @if(!empty($scrutinycandidate))
                                    @foreach($scrutinycandidate as $candDetails)  
                                    <?php
                                    $AcDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                                    $date = new DateTime($candDetails->created_at);
                                    $sts = getstatebystatecode($candDetails->st_code);
                                    //echo $date->format('d.m.Y'); // 31.07.2012
                                    $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                                    // dd($candDetails);
                                    $j++;
                                    ?>
                                    <tr>
                                        <td>@if(!empty($sts->ST_NAME)) {{$sts->ST_NAME}} @endif</td>
                                        <td>@if(!empty($candDetails->ac_no)) {{ $candDetails->ac_no}} - {{ $AcDetails->AC_NAME}} @endif</td>
                                        <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                                        <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                        <td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>

                                        <td>
                                            @if(($candDetails->final_by_ro=="1" && $candDetails->finalized_status=="1")  || (strtotime($candDetails->report_submitted_date)>0 && $candDetails->finalized_status=="1"))

                                            <a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-110" target="_blank">Scrutiny Report</a> 
                                            @endif

                                            @if((!empty($candDetails->form_fill_start) && strtotime($candDetails->form_fill_start)>0 && $candDetails->finalized_status=="0") || $candDetails->finalized_status=="1")
                                            <a href="{{url('/')}}/eci-expenditure/view/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-secondary btn-sm width-60" >View</a>
                                            @endif
                                            @if($candDetails->final_by_ro !="1" && $candDetails->finalized_status !="1")
                                            N/A
                                            @endif
                                             @if($candDetails->final_by_eci !='1')
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-140" 
                               target="_blank">Update Info</a>
                                            @endif
                                        </td>


                                        <?php
                                        $issueslist = array("Hearing Done", "Reply Issued", "Notice Issued");
                                        ?>
                                        <td>
                                            @if($candDetails->final_by_eci !='1')

                                            @if(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist))
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-140" 
                                               target="_blank">{{!empty($candDetails->final_action)? $candDetails->final_action:'N/A'}}</a>

                                            @elseif($candDetails->date_of_receipt_eci && strtotime($candDetails->date_of_receipt_eci)>0)
                                            Received
                                            @else
                                            Not Received
                                            @endif



                                            @elseif($candDetails->final_by_eci=="1")
                                            <span class="btn-secondary text-white btn btn-sm width-75">Finalized</span>
                                            @endif




                                        </td>
                                        <td>

                                            @if(!empty($candDetails->date_of_receipt_eci) && strtotime($candDetails->date_of_receipt_eci)>0 && !empty($candDetails->final_action))
                                            {{ !empty($candDetails->final_action)? $candDetails->final_action:'N/A'}}
                                            @else
                                            <input type="checkbox"   class="checkBoxClass"
                                                  
                          
                                                   name="received[]" value="{{$candDetails->st_code.':'.$candDetails->ac_no.':'.$candDetails->candidate_id}}">
                                                   @endif
                                        </td>
                                    </tr>
                                    @endforeach 
                                    @endif 
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>


                        </div>

                        <!--END OF SELECT CANDIDATE-->
                    </div>
                
            </div>
            </form>
        </div>
    </section>
</main>
<!---/////////////////modal for data preview /////////////////-->
<!-- Modal -->
<div class="modal fade" id="ModalReply" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;       ?>
            <div class="modal-body">
                <div class="col"><center><h4>Scrutiny Report Filed By RO</h4></center></div>
                <br>
                <div class="scruitData"></div>
            </div>


        </div>

    </div>
</div>
<!---/////////////////modal for profile preview /////////////////-->
<!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;       ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Status</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
<!-- ProfileECI-->
<div class="modal fade" id="ModalProfileECI" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;       ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Profile</h4></center></div>
                <br>
                <div class="profileDataECI"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
<!-- end pop up -->


<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> -->
<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">


//*******************EXTRA VALIDATION METHODS STARTS********************//
//maxsize
$.validator.addMethod('maxSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
});
//minsize
$.validator.addMethod('minSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size >= param)
});
//alphanumeric
$.validator.addMethod("alphnumericregex", function (value, element) {
    return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
});
//alphaonly
$.validator.addMethod("onlyalphregex", function (value, element) {
    return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
});
//without space
$.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//

</script> 
<!--**********FORM VALIDATION ENDS*************-->
<!-- graph start here manoj-->
<script type="text/javascript">
    /*  google.charts.load('current', {'packages': ['bar']});
     google.charts.setOnLoadCallback(drawChart);
     function drawChart() {
     var id = 1;
     $.ajax({
     type: "get",
     url: "{{url('/')}}/pcceo/candidateListBydataentryStartgraph/{{$cons_no}}",
     dataType: "json",
     success: function (response) {
     var data = google.visualization.arrayToDataTable(response);
     var options = {
     chart: {
     title: 'Overall summary of Data Entry Start',                         
     },
     bars: 'horizontal' // Required for Material Bar Charts.
     };
     
     var chart = new google.charts.Bar(document.getElementById('barchart'));
     chart.draw(data, google.charts.Bar.convertOptions(options));
     
     },
     errors: function (errors) {
     console.log(errors);
     }
     });
     
     
     } */
</script>
<script type="text/javascript">

    jQuery(document).ready(function () {
        jQuery("select[name='state']").change(function () {
            var state = jQuery(this).val();
            jQuery.ajax({
                url: '<?php echo url('/') ?>/eci-expenditure/getacbystate',
                type: 'GET',
                data: {state: state},

                success: function (result) {
                    
                    var stateselect = jQuery('form select[name=ac]');
                    stateselect.empty();
                    var achtml = '';
                    achtml = achtml + '<option value="">-- All AC --</option> ';
                    jQuery.each(result, function (key, value) {
                        achtml = achtml + '<option value="' + value.AC_NO + '">' + value.AC_NO + ' - ' + value.AC_NAME + ' - ' + value.AC_NAME_HI + '</option>';
                        jQuery("select[name='ac']").html(achtml);
                    });
                    var achtml_end = '';
                    jQuery("select[name='ac']").append(achtml_end)
                }
            });
        });
    });


//
//    jQuery(document).ready(function () {
//        var state = '<?php echo $stCode; ?>';
//// alert(state);
//        jQuery.ajax({
//            url: '<?php echo url('/') ?>/eci-expenditure/getpcbystate',
//            type: 'GET',
//            data: {state: state},
//
//            success: function (result) {
//                console.log(result);
//                var stateselect = jQuery('form select[name=pc]');
//                stateselect.empty();
//                var pchtml = '';
//                pchtml = pchtml + '<option value="">-- All PC --</option> ';
//                jQuery.each(result, function (key, value) {
//                    pchtml = pchtml + '<option value="' + value.PC_NO + '">' + value.PC_NO + ' - ' + value.PC_NAME + ' - ' + value.PC_NAME_HI + '</option>';
//                    jQuery("select[name='pc']").html(pchtml);
//                });
//                var pchtml_end = '';
//                jQuery("select[name='pc']").append(pchtml_end)
//            }
//        });
//    });
 //check all received
jQuery(document).ready(function(){
 $("#ckbCheckAll").click(function () { 
    $(".checkBoxClass").prop('checked', $(this).prop('checked'));
});
});
$(document).ready(function (){
    
    var table = $('#example2').DataTable({
       'lengthMenu': [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
       'pageLength': 10
    });
    });

    jQuery(document).on('click', '#receivedDataAction', function (e)
    {
        var data = jQuery("#receivedData").serialize();
        $.ajax({
            data: data,
            type: "post",
            dataType: "json",
            url: '<?php echo url('/') ?>/eci-expenditure/updateReceived',
            success: function (response) {
                $('.showmessagereceived').text(response.message);
            }
        });// end else
    });
//  end check all here
</script>
<!--graph implementation start here-Manoj -->
@endsection

