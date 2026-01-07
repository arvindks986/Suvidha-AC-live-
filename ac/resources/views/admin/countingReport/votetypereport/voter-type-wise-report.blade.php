@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
<style>
    .bootstrap-select>.dropdown-toggle {
    background-color: #fff;
    border: 1px solid #ced4da;
    padding: 0.6rem 1rem;
}
</style>
<main role="main" class="inner cover mb-3">
    <!--FILTER STARTS FROM HERE-->
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="nav-header welcome float-left">
                        <ul class="float-right"> 
                            <li><a>  Candidate Wise Report</a> </li>
                        </ul>
                    </div>
                    <div class="nav-header welcome float-right">
                        <ul class="float-right"> 
                            <li><a href="javascript:void(0)">   Welcome :- <?php echo $user_data->officername;?> </a> </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="card-header">
        <div class=" row">
            <div class="col-sm-12">
                <form method="post">
                    {{ csrf_field() }}


                    <!--STATE LIST DROPDOWN STARTS-->
                    <label>Select State* </label>
                    @if($user_data->role_id=='7')
                    <select name="state" id="state" class="selectpicker">
                        <option value="">Select States</option>
                        @foreach ($list_state as $state_List ))
                        <option value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>
                        @endforeach
                    </select>
                    @else
                    <select name="state" id="state" class="selectpicker">
                        <option value="">Select States</option>
                        @foreach ($list_state as $state_List ))
                        <option value="{{ $state_List->ST_CODE }}" <?php if(count($list_state)==1){echo 'selected';}?>>{{$state_List->ST_NAME}}</option>
                        @endforeach
                    </select>
                    @endif
                    
                    <!--STATE LIST DROPDOWN ENDS-->
                    <label>Select AC* </label>
                    @if($user_data->role_id=='7')
                    <select name="acval[]" id="acval" class="selectpicker" multiple="" data-actions-box="true" >
                    </select>
                    @elseif ($user_data->role_id=='19')
                    <select name="acval[]" id="acval" class="selectpicker" multiple="">
                    <?php foreach ($ac_list as $k => $v) { ?>
                        <option value="<?php echo $v->AC_NO; ?>"<?php if(count($ac_list)==1){echo 'selected';}?>><?php echo $v->AC_NO.'-'.$v->AC_NAME; ?></option>
                    <?php } ?>
                    </select>
                    @else
                    <select name="acval[]" id="acval" class="selectpicker" data-actions-box="true" required="" multiple="multiple">
                    <?php foreach ($ac_list as $k => $v) { ?>
                        <option value="<?php echo $v->AC_NO; ?>"<?php if(count($ac_list)==1){echo 'selected';}?>><?php echo $v->AC_NO.'-'.$v->AC_NAME; ?></option>
                    <?php } ?>
                    </select>
                    @endif
                    <label>Select Candidate* </label>
                    @if($user_data->role_id=='7')
                    <select name="party_id[]" id="party_id" class="selectpicker" required="" multiple="" data-actions-box="true">
                    </select>
                    @else
                    <select name="party_id[]" id="party_id" class="selectpicker" data-actions-box="true" required="" multiple="multiple">

                        <?php foreach ($list_party as $k => $v) { ?>
                            <option value="<?php echo $v->candidate_id; ?>"><?php echo $v->candidate_name.' ('.$v->party_abbre.' )'; ?></option>
                        <?php } ?>
                    </select>
                    @endif
                    <input type="button" value="Filter" id="search_record" class="btn btn-primary">
                    <input type="reset" id="reset" value="Reset Filter"  name="Cancel" class="btn btn-default">
                    <img src="{{ asset('/img/loading-icon.gif')}}" style="display: none;" class="loadingIcon"/>
                </form>
            </div> 
            <div class="col"> <h4> 
            </div>
        </div>
    </div>

    <!--FILTER ENDS HERE-->   
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class="card-header report_section" style="display: none;">
                        <div class="row">
                            <div class="col"><p class="mb-0 text-right"><span class="badge badge-info"></span>
                                    <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="Javascript:;" class="btn btn-info" role="button" onclick="DownloadPdf();">PDF Download</a> &nbsp;&nbsp;
                                    <a href="Javascript:;" class="btn btn-info" role="button" onclick="DownloadExcel();">Export Excel</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body report_section" style="display: none;">
                        <div id="datashow" class="head-title"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <form id="exportFrm" method="post">
        {{ csrf_field() }}											
        <input type ="hidden" name="statevalue" id='statevalue' value = "">
        <input type ="hidden" name="acvalue" id='acvalue' value = "">
        <input type ="hidden" name="partyvalue" id='partyvalue' value = "">
    </form>
</main>
<script type="text/javascript">
    var role_id = '<?php echo $user_data->role_id ?>';
    var prefix = '';
    if (role_id != '') {
        switch (role_id) {
            case '7':
                prefix = 'eci';
                break;
            case '4':
                prefix = 'acceo';
                break;
            case '5':
                prefix = 'acdeo';
                break;
            case '19':
                prefix = 'roac';
                break;
            case '20':
                prefix = 'aro';
                break;
        }
    }

    //Select pc list by state start 
    $("#state").change(function () {
        $("#errMsg").html("&nbsp;");
        var state = $(this).val();
        if (state != '') {
            $.ajax({
                url: '<?php echo url('/'); ?>/' + prefix + '/candidate-wise-report-get-ac-state/' + encodeURI(state),
                type: "GET",
                dataType: "html",
                success: function (msg) {
                    $('#party_id').html('').selectpicker('refresh');
                    var jsonText = $.parseJSON(msg);
                    var ac_arrval = jsonText.ac_arr;
                    var party_arrval = jsonText.party_arr;

                    var text = [];
                    var text1 = [];
                    if (msg.length > 0) {
                        for (var i = 0; i < ac_arrval.id.length; i++) {
                            text.push('<option value=' + ac_arrval.id[i] + ' >' +  ac_arrval.id[i] +'-'+ ac_arrval.val[i] + '</option>');
                        }
                        $('#acval').html(text).selectpicker('refresh');
                    } else {
                        text.push('<option selected value="No_Dis">PC Not Found</option>');
                        $('#acval').html('').selectpicker('refresh');
                        ;
                    }

//                    if(party_arrval.id.length > 0){
//                            for (var i=0; i<party_arrval.id.length; i++) { 
//                                    text1.push('<option value=' + party_arrval.id[i] + ' >' + party_arrval.val[i]+'</option>');   
//                            }						
//                            $('#party_id').html(text1).selectpicker('refresh');
//                    } else {
//                            text1.push('<option selected value="No_Dis">Party Not Found</option>');
//                            $('#party_id').html('').selectpicker('refresh');;
//                    }
                },
            });
        }
    });

    //Select pc list by state ends 

    //Select ac list by pc start 
    $("#acval").change(function () {
        $("#errMsg").html("&nbsp;");
        var ac = $(this).val();
        var state_code = $("#state").val();
        var selectedValues = [];
        $("#acval :selected").each(function () {
            selectedValues.push($(this).val());
        });

        if (ac != '' && state_code != '') {
            $.ajax({
                url: '<?php echo url('/'); ?>/' + prefix + '/candidate-wise-report-get-party/' + encodeURI(selectedValues) + '/' + encodeURI(state_code),
                type: "GET",
                dataType: "html",
                success: function (msg) {

                    var jsonText = $.parseJSON(msg);
                    var text = [];
                    if (msg.length > 2) {
                        for (var i = 0; i < jsonText.id.length; i++) {
                            text.push('<option value=' + jsonText.id[i] + ' >' + jsonText.val[i] + '</option>');
                        }
                        $('#party_id').html(text).selectpicker('refresh');
                    } else {
                        text.push('<option selected value="No_Dis">Candidate Not Found</option>');
                        $('#party_id').html('');
                    }
                },
            });
        }
    });

    //Select ac list by pc ends 
    $("#party_id").change(function () {
        $("#errMsg").html("&nbsp;");
        $("#party_id").selectpicker('refresh');
    });

    //Searching start here
    $("#search_record").click(function () {
        var state_code = $("#state").val();
        var acno = $("#acval").val();
        var party_id = $("#party_id").val();
        if (state_code == '') {
            $("#errBox").show();
            alert("Please select state.");
            $("#state").focus();
            return false;
        } else {
            //$("#errBox").hide();
        }
        if (acno == '') {
            $("#errBox").show();
            alert("Please select AC.");
            $("#acval").focus();
            return false;
        } else {
            //$("#errBox").hide();
        }
        if (party_id == '') {
            $("#errBox").show();
            alert("Please select candidate.");
            $("#party_id").focus();
            return false;
        } else {
            //$("#errBox").hide();
        }

        if (state_code != '' && acno != '' && party_id != '') {
            $.ajax({
                type: "POST",
                url: '<?php echo url('/'); ?>/' + prefix + '/candidate-wise-report-ac',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "stateid": state_code,
                    "acno": acno,
                    "party": party_id
                },
                dataType: "html",
                beforeSend: function (xhr) {
                    $('#search_record').prop("disabled", true);
                    $('#datashow').hide();
                    $(".loadingIcon").show();
                },
                success: function (msg) {
                    $(".loadingIcon").hide();
                    $('#datashow').show();
                    $('#search_record').prop("disabled", false);
                    var tview = msg.split("|||")[0];
                    if (tview != "") {
                        $("#datashow").show();
                        $("#datashow").html(tview);
                    } else {
                        $("#datashow").hide();
                        $(".report_section").hide();
                    }

                    var rowCount = parseInt(msg.split("|||")[1]);
                    if (rowCount > 0) {
                        $(".report_section").show();
                    } else {
                        $(".report_section").hide();
                    }
					$('#example').dataTable();
                },
                error: function (msg) {
                    console.log(msg);
                    //console.log("Error");
                }
            });
        }
    });

    $("#reset").click(function () {
        referesh_page();

    });

    function DownloadExcel() {
        var state_code = $("#state").val();
        var selectedValues = [];
        $("#acval :selected").each(function () {
            selectedValues.push($(this).val());
        });
        var selectedValues1 = [];
        $("#party_id :selected").each(function () {
            selectedValues1.push($(this).val());
        });
        var pcno = selectedValues;
        var party_id = selectedValues1;

        var acurl = '<?php echo url('/'); ?>/' + prefix + '/candidate-wise-report-excel';

        $('#exportFrm').attr('action', acurl);
        $("#statevalue").val(state_code);
        $("#acvalue").val(pcno);
        $("#partyvalue").val(party_id);
        $("#exportFrm").submit();
    }
    function DownloadPdf() {
        var state_code = $("#state").val();
        var selectedValues = [];
        $("#acval :selected").each(function () {
            selectedValues.push($(this).val());
        });
        var pcno = selectedValues;
        var selectedValues1 = [];
        $("#party_id :selected").each(function () {
            selectedValues1.push($(this).val());
        });
        var pcno = selectedValues;
        var party_id = selectedValues1;

        var acurl = '<?php echo url('/'); ?>/' + prefix + '/candidate-wise-report-pdf';

        $('#exportFrm').attr('action', acurl);
        $("#statevalue").val(state_code);
        $("#acvalue").val(pcno);
        $("#partyvalue").val(party_id);
        $("#exportFrm").submit();
    }

    function referesh_page() {
        location.reload();
    }
</script>
<script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
@endsection


