@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Notification Details')
@section('bradcome', 'Notification Candidate List')
@section('description', '')
@section('content') 

<?php
//print_r($user_data);
//$st = getstatebystatecode($user_data->st_code);
$distname = getdistrictbydistrictno($user_data->st_code, $user_data->dist_no);
$acdetails = getacbyacno($user_data->st_code, $user_data->ac_no);
$stCode = !empty($st_code) ? $st_code : '0';

$acName = !empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
$cons_no = !empty($_GET['ac']) ? trim($_GET['ac']) : "0";

$stCode = !empty($_GET['state']) ? $_GET['state'] : $stCode;

$st = getstatebystatecode($stCode);


$stateName = !empty($st) ? $st->ST_NAME : 'ALL';

$all_ac = getacbystate($stCode);
$ac = !empty($_GET['ac']) ? trim($_GET['ac']) : "";
$acdetail = getacbyacno($stCode, $ac);
$statedetail = getstatebystatecode($stCode);
$userid_check = array();
$final_action = !empty($_GET['final_action']) ? $_GET['final_action'] : "";
$issueslist = array("Hearing Done", "Reply Issued", "Notice Issued");
?>
@if(!empty($scrutinycandidate))
@foreach($scrutinycandidate as $candDetailss) 
<?php
$action_list = array("Closed", "Disqualified", "Case Dropped");
if (!in_array($candDetailss->final_action, $action_list)) {
    $userid_check[] = $candDetailss->candidate_id;
}
?>                                       
@endforeach
@endif 


<?php
$action_status = '';

$finalscrutinycandidate = [];
if (isset($_GET['action_status']) && !empty($_GET['action_status'])) {
    $action_status = !empty($_GET['action_status']) ? trim($_GET['action_status']) : '';
    foreach ($scrutinycandidate as $item) {

        if ($action_status == 'received' && strtotime($item->date_of_receipt_eci) > 0) {

            $finalscrutinycandidate[] = $item;
        } elseif ($action_status == 'not-received' && empty($item->date_of_receipt_eci)) {
            $finalscrutinycandidate[] = $item;
        } else {
            
        }
    }
} else {
    $finalscrutinycandidate = $scrutinycandidate;
}
?>
<?php if (empty($userid_check)) { ?>
    <style type="text/css">
        th.select-checkbox.sorting_disabled.selected:after {
            display: none;
        }
    </style>
<?php } ?>

<?php if ($stateName === "ALL") { ?>
    <style type="text/css">
        th.select-checkbox.sorting_disabled {
            cursor: not-allowed;
        }
        th.select-checkbox.sorting_disabled span {
            display: none;
            cursor: not-allowed;
        }
        th.select-checkbox.sorting_disabled:after {
            display: none;
            cursor: not-allowed;
        }
    </style>
<?php } ?>


<style type="text/css">

    .width-280{width: 320px;}
    .btn-sm {
        margin: 2px;
    }

    .desleected {
        background-color: #999999;
        color: #999999;
    }

    table.dataTable tbody td.desleected:before, table.dataTable tbody th.desleected:before {content: '&#9989;';
                                                                                            display: none !important;
    }

    table.dataTable tbody td.desleected:after, table.dataTable tbody th.desleected:after {content: '&#9989;';
                                                                                          display: none !important;
    }
    .desleected {    background-color: #999999;
                     cursor: not-allowed;}
    .mt-5, .my-5{margin-top: 1rem!important;}
    .definalizeForm{width: 87%;
                    margin: 0 auto;}
    textarea#definalization_reason {
        border: 1px solid #6666;
        border-radius: 2px;
        height: 100px;
    }
    #definalized_error{    color: red;
                           font-size: 15px;}

</style>
<style type="text/css">
    table.dataTable tbody>tr.selected,table.dataTable tbody>tr>.selected{background-color:#B0BED9}table.dataTable.stripe tbody>tr.odd.selected,table.dataTable.stripe tbody>tr.odd>.selected,table.dataTable.display tbody>tr.odd.selected,table.dataTable.display tbody>tr.odd>.selected{background-color:#acbad4}table.dataTable.hover tbody>tr.selected:hover,table.dataTable.hover tbody>tr>.selected:hover,table.dataTable.display tbody>tr.selected:hover,table.dataTable.display tbody>tr>.selected:hover{background-color:#aab7d1}table.dataTable.order-column tbody>tr.selected>.sorting_1,table.dataTable.order-column tbody>tr.selected>.sorting_2,table.dataTable.order-column tbody>tr.selected>.sorting_3,table.dataTable.order-column tbody>tr>.selected,table.dataTable.display tbody>tr.selected>.sorting_1,table.dataTable.display tbody>tr.selected>.sorting_2,table.dataTable.display tbody>tr.selected>.sorting_3,table.dataTable.display tbody>tr>.selected{background-color:#acbad5}table.dataTable.display tbody>tr.odd.selected>.sorting_1,table.dataTable.order-column.stripe tbody>tr.odd.selected>.sorting_1{background-color:#a6b4cd}table.dataTable.display tbody>tr.odd.selected>.sorting_2,table.dataTable.order-column.stripe tbody>tr.odd.selected>.sorting_2{background-color:#a8b5cf}table.dataTable.display tbody>tr.odd.selected>.sorting_3,table.dataTable.order-column.stripe tbody>tr.odd.selected>.sorting_3{background-color:#a9b7d1}table.dataTable.display tbody>tr.even.selected>.sorting_1,table.dataTable.order-column.stripe tbody>tr.even.selected>.sorting_1{background-color:#acbad5}table.dataTable.display tbody>tr.even.selected>.sorting_2,table.dataTable.order-column.stripe tbody>tr.even.selected>.sorting_2{background-color:#aebcd6}table.dataTable.display tbody>tr.even.selected>.sorting_3,table.dataTable.order-column.stripe tbody>tr.even.selected>.sorting_3{background-color:#afbdd8}table.dataTable.display tbody>tr.odd>.selected,table.dataTable.order-column.stripe tbody>tr.odd>.selected{background-color:#a6b4cd}table.dataTable.display tbody>tr.even>.selected,table.dataTable.order-column.stripe tbody>tr.even>.selected{background-color:#acbad5}table.dataTable.display tbody>tr.selected:hover>.sorting_1,table.dataTable.order-column.hover tbody>tr.selected:hover>.sorting_1{background-color:#a2aec7}table.dataTable.display tbody>tr.selected:hover>.sorting_2,table.dataTable.order-column.hover tbody>tr.selected:hover>.sorting_2{background-color:#a3b0c9}table.dataTable.display tbody>tr.selected:hover>.sorting_3,table.dataTable.order-column.hover tbody>tr.selected:hover>.sorting_3{background-color:#a5b2cb}table.dataTable.display tbody>tr:hover>.selected,table.dataTable.display tbody>tr>.selected:hover,table.dataTable.order-column.hover tbody>tr:hover>.selected,table.dataTable.order-column.hover tbody>tr>.selected:hover{background-color:#a2aec7}table.dataTable tbody td.select-checkbox,table.dataTable tbody th.select-checkbox{position:relative}table.dataTable tbody td.select-checkbox:before,table.dataTable tbody td.select-checkbox:after,table.dataTable tbody th.select-checkbox:before,table.dataTable tbody th.select-checkbox:after{display:block;position:absolute;top:1.2em;left:50%;width:12px;height:12px;box-sizing:border-box}table.dataTable tbody td.select-checkbox:before,table.dataTable tbody th.select-checkbox:before{content:' ';margin-top:-6px;margin-left:-6px;border:1px solid black;border-radius:3px}table.dataTable tr.selected td.select-checkbox:after,table.dataTable tr.selected th.select-checkbox:after{content:'\2714';margin-top:-11px;margin-left:-4px;text-align:center;text-shadow:1px 1px #B0BED9, -1px -1px #B0BED9, 1px -1px #B0BED9, -1px 1px #B0BED9}div.dataTables_wrapper span.select-info,div.dataTables_wrapper span.select-item{margin-left:0.5em}@media screen and (max-width: 640px){div.dataTables_wrapper span.select-info,div.dataTables_wrapper span.select-item{margin-left:0;display:block}}

    table.dataTable tr th.select-checkbox.selected::after {
        content: "✔";
        margin-top: 0px;
        margin-left: -25px;
        position: absolute;
        text-align: center;
        text-shadow: rgb(176, 190, 217) 1px 1px, rgb(176, 190, 217) -1px -1px, rgb(176, 190, 217) 1px -1px, rgb(176, 190, 217) -1px 1px;
    }
</style>

<?php if (!empty($userid_check)) { ?>
    <style>
        span.checkbox1 {
            border: 3px solid white;
            padding: 0px 16px;
            margin-left: -6px;
        }
    </style>
<?php } else { ?>
    <style>
        span.checkbox1
        {   border: 3px solid white;
            padding: 1px 11px;
            display: none;
        }
    </style>
<?php } ?>
<style type="text/css">
    table.dataTable{width:100%;margin:0 auto;clear:both;border-collapse:separate;border-spacing:0}table.dataTable thead th,table.dataTable tfoot th{font-weight:bold}table.dataTable thead th,table.dataTable thead td{padding:10px 18px;border-bottom:1px solid #111}table.dataTable thead th:active,table.dataTable thead td:active{outline:none}table.dataTable tfoot th,table.dataTable tfoot td{padding:10px 18px 6px 18px;border-top:1px solid #111}table.dataTable thead .sorting,table.dataTable thead .sorting_asc,table.dataTable thead .sorting_desc,table.dataTable thead .sorting_asc_disabled,table.dataTable thead .sorting_desc_disabled{cursor:pointer;*cursor:hand;background-repeat:no-repeat;background-position:center right}table.dataTable thead .sorting{background-image:url("../images/sort_both.png")}table.dataTable thead .sorting_asc{background-image:url("../images/sort_asc.png")}table.dataTable thead .sorting_desc{background-image:url("../images/sort_desc.png")}table.dataTable thead .sorting_asc_disabled{background-image:url("../images/sort_asc_disabled.png")}table.dataTable thead .sorting_desc_disabled{background-image:url("../images/sort_desc_disabled.png")}table.dataTable tbody tr{background-color:#ffffff}table.dataTable tbody tr.selected{background-color:#B0BED9}table.dataTable tbody th,table.dataTable tbody td{padding:8px 10px}table.dataTable.row-border tbody th,table.dataTable.row-border tbody td,table.dataTable.display tbody th,table.dataTable.display tbody td{border-top:1px solid #ddd}table.dataTable.row-border tbody tr:first-child th,table.dataTable.row-border tbody tr:first-child td,table.dataTable.display tbody tr:first-child th,table.dataTable.display tbody tr:first-child td{border-top:none}table.dataTable.cell-border tbody th,table.dataTable.cell-border tbody td{border-top:1px solid #ddd;border-right:1px solid #ddd}table.dataTable.cell-border tbody tr th:first-child,table.dataTable.cell-border tbody tr td:first-child{border-left:1px solid #ddd}table.dataTable.cell-border tbody tr:first-child th,table.dataTable.cell-border tbody tr:first-child td{border-top:none}table.dataTable.stripe tbody tr.odd,table.dataTable.display tbody tr.odd{background-color:#f9f9f9}table.dataTable.stripe tbody tr.odd.selected,table.dataTable.display tbody tr.odd.selected{background-color:#acbad4}table.dataTable.hover tbody tr:hover,table.dataTable.display tbody tr:hover{background-color:#f6f6f6}table.dataTable.hover tbody tr:hover.selected,table.dataTable.display tbody tr:hover.selected{background-color:#aab7d1}table.dataTable.order-column tbody tr>.sorting_1,table.dataTable.order-column tbody tr>.sorting_2,table.dataTable.order-column tbody tr>.sorting_3,table.dataTable.display tbody tr>.sorting_1,table.dataTable.display tbody tr>.sorting_2,table.dataTable.display tbody tr>.sorting_3{background-color:#fafafa}table.dataTable.order-column tbody tr.selected>.sorting_1,table.dataTable.order-column tbody tr.selected>.sorting_2,table.dataTable.order-column tbody tr.selected>.sorting_3,table.dataTable.display tbody tr.selected>.sorting_1,table.dataTable.display tbody tr.selected>.sorting_2,table.dataTable.display tbody tr.selected>.sorting_3{background-color:#acbad5}table.dataTable.display tbody tr.odd>.sorting_1,table.dataTable.order-column.stripe tbody tr.odd>.sorting_1{background-color:#f1f1f1}table.dataTable.display tbody tr.odd>.sorting_2,table.dataTable.order-column.stripe tbody tr.odd>.sorting_2{background-color:#f3f3f3}table.dataTable.display tbody tr.odd>.sorting_3,table.dataTable.order-column.stripe tbody tr.odd>.sorting_3{background-color:whitesmoke}table.dataTable.display tbody tr.odd.selected>.sorting_1,table.dataTable.order-column.stripe tbody tr.odd.selected>.sorting_1{background-color:#a6b4cd}table.dataTable.display tbody tr.odd.selected>.sorting_2,table.dataTable.order-column.stripe tbody tr.odd.selected>.sorting_2{background-color:#a8b5cf}table.dataTable.display tbody tr.odd.selected>.sorting_3,table.dataTable.order-column.stripe tbody tr.odd.selected>.sorting_3{background-color:#a9b7d1}table.dataTable.display tbody tr.even>.sorting_1,table.dataTable.order-column.stripe tbody tr.even>.sorting_1{background-color:#fafafa}table.dataTable.display tbody tr.even>.sorting_2,table.dataTable.order-column.stripe tbody tr.even>.sorting_2{background-color:#fcfcfc}table.dataTable.display tbody tr.even>.sorting_3,table.dataTable.order-column.stripe tbody tr.even>.sorting_3{background-color:#fefefe}table.dataTable.display tbody tr.even.selected>.sorting_1,table.dataTable.order-column.stripe tbody tr.even.selected>.sorting_1{background-color:#acbad5}table.dataTable.display tbody tr.even.selected>.sorting_2,table.dataTable.order-column.stripe tbody tr.even.selected>.sorting_2{background-color:#aebcd6}table.dataTable.display tbody tr.even.selected>.sorting_3,table.dataTable.order-column.stripe tbody tr.even.selected>.sorting_3{background-color:#afbdd8}table.dataTable.display tbody tr:hover>.sorting_1,table.dataTable.order-column.hover tbody tr:hover>.sorting_1{background-color:#eaeaea}table.dataTable.display tbody tr:hover>.sorting_2,table.dataTable.order-column.hover tbody tr:hover>.sorting_2{background-color:#ececec}table.dataTable.display tbody tr:hover>.sorting_3,table.dataTable.order-column.hover tbody tr:hover>.sorting_3{background-color:#efefef}table.dataTable.display tbody tr:hover.selected>.sorting_1,table.dataTable.order-column.hover tbody tr:hover.selected>.sorting_1{background-color:#a2aec7}table.dataTable.display tbody tr:hover.selected>.sorting_2,table.dataTable.order-column.hover tbody tr:hover.selected>.sorting_2{background-color:#a3b0c9}table.dataTable.display tbody tr:hover.selected>.sorting_3,table.dataTable.order-column.hover tbody tr:hover.selected>.sorting_3{background-color:#a5b2cb}table.dataTable.no-footer{border-bottom:1px solid #111}table.dataTable.nowrap th,table.dataTable.nowrap td{white-space:nowrap}table.dataTable.compact thead th,table.dataTable.compact thead td{padding:4px 17px 4px 4px}table.dataTable.compact tfoot th,table.dataTable.compact tfoot td{padding:4px}table.dataTable.compact tbody th,table.dataTable.compact tbody td{padding:4px}table.dataTable th.dt-left,table.dataTable td.dt-left{text-align:left}table.dataTable th.dt-center,table.dataTable td.dt-center,table.dataTable td.dataTables_empty{text-align:center}table.dataTable th.dt-right,table.dataTable td.dt-right{text-align:right}table.dataTable th.dt-justify,table.dataTable td.dt-justify{text-align:justify}table.dataTable th.dt-nowrap,table.dataTable td.dt-nowrap{white-space:nowrap}table.dataTable thead th.dt-head-left,table.dataTable thead td.dt-head-left,table.dataTable tfoot th.dt-head-left,table.dataTable tfoot td.dt-head-left{text-align:left}table.dataTable thead th.dt-head-center,table.dataTable thead td.dt-head-center,table.dataTable tfoot th.dt-head-center,table.dataTable tfoot td.dt-head-center{text-align:center}table.dataTable thead th.dt-head-right,table.dataTable thead td.dt-head-right,table.dataTable tfoot th.dt-head-right,table.dataTable tfoot td.dt-head-right{text-align:right}table.dataTable thead th.dt-head-justify,table.dataTable thead td.dt-head-justify,table.dataTable tfoot th.dt-head-justify,table.dataTable tfoot td.dt-head-justify{text-align:justify}table.dataTable thead th.dt-head-nowrap,table.dataTable thead td.dt-head-nowrap,table.dataTable tfoot th.dt-head-nowrap,table.dataTable tfoot td.dt-head-nowrap{white-space:nowrap}table.dataTable tbody th.dt-body-left,table.dataTable tbody td.dt-body-left{text-align:left}table.dataTable tbody th.dt-body-center,table.dataTable tbody td.dt-body-center{text-align:center}table.dataTable tbody th.dt-body-right,table.dataTable tbody td.dt-body-right{text-align:right}table.dataTable tbody th.dt-body-justify,table.dataTable tbody td.dt-body-justify{text-align:justify}table.dataTable tbody th.dt-body-nowrap,table.dataTable tbody td.dt-body-nowrap{white-space:nowrap}table.dataTable,table.dataTable th,table.dataTable td{box-sizing:content-box}.dataTables_wrapper{position:relative;clear:both;*zoom:1;zoom:1}.dataTables_wrapper .dataTables_length{float:left}.dataTables_wrapper .dataTables_filter{float:right;text-align:right}.dataTables_wrapper .dataTables_filter input{margin-left:0.5em}.dataTables_wrapper .dataTables_info{clear:both;float:left;padding-top:0.755em}.dataTables_wrapper .dataTables_paginate{float:right;text-align:right;padding-top:0.25em}.dataTables_wrapper .dataTables_paginate .paginate_button{box-sizing:border-box;display:inline-block;min-width:1.5em;padding:0.5em 1em;margin-left:2px;text-align:center;text-decoration:none !important;cursor:pointer;*cursor:hand;color:#333 !important;border:1px solid transparent;border-radius:2px}.dataTables_wrapper .dataTables_paginate .paginate_button.current,.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{color:#333 !important;border:1px solid #979797;background-color:white;background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #fff), color-stop(100%, #dcdcdc));background:-webkit-linear-gradient(top, #fff 0%, #dcdcdc 100%);background:-moz-linear-gradient(top, #fff 0%, #dcdcdc 100%);background:-ms-linear-gradient(top, #fff 0%, #dcdcdc 100%);background:-o-linear-gradient(top, #fff 0%, #dcdcdc 100%);background:linear-gradient(to bottom, #fff 0%, #dcdcdc 100%)}.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active{cursor:default;color:#666 !important;border:1px solid transparent;background:transparent;box-shadow:none}.dataTables_wrapper .dataTables_paginate .paginate_button:hover{color:white !important;border:1px solid #111;background-color:#585858;background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #585858), color-stop(100%, #111));background:-webkit-linear-gradient(top, #585858 0%, #111 100%);background:-moz-linear-gradient(top, #585858 0%, #111 100%);background:-ms-linear-gradient(top, #585858 0%, #111 100%);background:-o-linear-gradient(top, #585858 0%, #111 100%);background:linear-gradient(to bottom, #585858 0%, #111 100%)}.dataTables_wrapper .dataTables_paginate .paginate_button:active{outline:none;background-color:#2b2b2b;background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #2b2b2b), color-stop(100%, #0c0c0c));background:-webkit-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);background:-moz-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);background:-ms-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);background:-o-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);background:linear-gradient(to bottom, #2b2b2b 0%, #0c0c0c 100%);box-shadow:inset 0 0 3px #111}.dataTables_wrapper .dataTables_paginate .ellipsis{padding:0 1em}.dataTables_wrapper .dataTables_processing{position:absolute;top:50%;left:50%;width:100%;height:40px;margin-left:-50%;margin-top:-25px;padding-top:20px;text-align:center;font-size:1.2em;background-color:white;background:-webkit-gradient(linear, left top, right top, color-stop(0%, rgba(255,255,255,0)), color-stop(25%, rgba(255,255,255,0.9)), color-stop(75%, rgba(255,255,255,0.9)), color-stop(100%, rgba(255,255,255,0)));background:-webkit-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 25%, rgba(255,255,255,0.9) 75%, rgba(255,255,255,0) 100%);background:-moz-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 25%, rgba(255,255,255,0.9) 75%, rgba(255,255,255,0) 100%);background:-ms-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 25%, rgba(255,255,255,0.9) 75%, rgba(255,255,255,0) 100%);background:-o-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 25%, rgba(255,255,255,0.9) 75%, rgba(255,255,255,0) 100%);background:linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 25%, rgba(255,255,255,0.9) 75%, rgba(255,255,255,0) 100%)}.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_processing,.dataTables_wrapper .dataTables_paginate{color:#333}.dataTables_wrapper .dataTables_scroll{clear:both}.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody{*margin-top:-1px;-webkit-overflow-scrolling:touch}.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>thead>tr>th,.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>thead>tr>td,.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>tbody>tr>th,.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>tbody>tr>td{vertical-align:middle}.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>thead>tr>th>div.dataTables_sizing,.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>thead>tr>td>div.dataTables_sizing,.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>tbody>tr>th>div.dataTables_sizing,.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>tbody>tr>td>div.dataTables_sizing{height:0;overflow:hidden;margin:0 !important;padding:0 !important}.dataTables_wrapper.no-footer .dataTables_scrollBody{border-bottom:1px solid #111}.dataTables_wrapper.no-footer div.dataTables_scrollHead table.dataTable,.dataTables_wrapper.no-footer div.dataTables_scrollBody>table{border-bottom:none}.dataTables_wrapper:after{visibility:hidden;display:block;content:"";clear:both;height:0}@media screen and (max-width: 767px){.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate{float:none;text-align:center}.dataTables_wrapper .dataTables_paginate{margin-top:0.5em}}@media screen and (max-width: 640px){.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{float:none;text-align:center}.dataTables_wrapper .dataTables_filter{margin-top:0.5em}}

    .errormessage{
        color: red;
        font-weight: 600;
        font-size: 16px;
        margin-left:480px;
    }
    .successmessage{
        color: green;
        font-weight: 600;
        font-size: 16px;
        margin-left:480px;
    }

    table.dataTable tbody td.select-checkbox:before, table.dataTable tbody th.select-checkbox:before {
        content: ' ';
        margin-top: -11px;
        margin-left: -5px;
        border: 1px solid black;
        border-radius: 3px;
    }

    table.dataTable tbody td.select-checkbox:before, table.dataTable tbody td.select-checkbox:after, table.dataTable tbody th.select-checkbox:before, table.dataTable tbody th.select-checkbox:after {
        display: block;
        position: absolute;
        top: 1.4em;
        left: 22%;
        width: 41px;
        color: black;
        background-color: transparent;
        height: 25px;
        box-sizing: border-box;
    }
</style>
<main role="main" class="inner cover mb-3">
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 mt-3">
                    <div class="errormessage"></div>
                    <div class="successmessage"></div>
                    @if($check_filter=='1')
                    <form method="get" action="{{url('/eci-expenditure/eciallscrutinybyepass')}}" id="EcidashboardFilter"> 

                        <div class="row justify-content-center">
                            <!--STATE LIST DROPDOWN STARTS-->
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select State</label>    
                                <select name="state" id="state" class="form-control">
<?php if ($stateName == 'ALL') { ?> <option value="">All States</option> <?php } ?>

                                    @foreach ($statelist as $state_List )
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

                                    @foreach($all_ac as $getac)
                                    @if ($cons_no ==  $getac->AC_NO)
                                    <option value="{{ $getac->AC_NO }}" selected>{{$getac->AC_NO }} - {{$getac->AC_NAME }}</option>
                                    @else
                                    <option value="{{ $getac->AC_NO }}" > 
                                        {{$getac->AC_NO }} - {{$getac->AC_NAME }}</option>
                                    @endif
                                    @endforeach 
                                    @endif
                                </select>
                                @if ($errors->has('ac'))
                                <span style="color:red;">{{ $errors->first('ac') }}</span>
                                @endif

                                <div class="acerrormsg errormsg errorred"></div>
                            </div>
                            <div class="col-sm-3">                                
                                <label for="" class="mr-3">Select Case(Received/Not received)</label>    
                                <select name="action_status"  class=" form-control" >
                                    <option value="" @if($action_status=='') selected @endif>-- All --</option>                                    
                                    <option value="received" @if($action_status=='received') selected @endif >Received</option>                                    
                                    <option  value="not-received" @if($action_status=='not-received') selected @endif >Not Received</option>                                    
                                </select> 
                            </div>
                            <div class="col-sm-2 mt-2">
                                <p class="mt-4 text-left">
                                    <!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
                                    <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
                                    <a href="{{url('/eci-expenditure/eciallscrutinybyepass')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a>
                                </p>
                            </div>
                        </div>

                    </form> 
                    @endif
                    <!-- final action start-->
                    @if(!empty($action_status))
                    <div class="row justify-content-center">                            
                        <div class="col-sm-3">
                            <label  class="mr-3">Select Action</label>    
                            <select name="final_action" id="final_action" required class="form-control" >
                                <option value="">--Select--</option>
                                @if($action_status=="not-received")
                                <option value="Received" @if($final_action=='') selected @endif>Received</option>
                                @endif
                                @if($action_status=="received" && $user_data->officername !='eciexpend')
                                <option value="Closed" @if($final_action=='Closed') selected @endif>Closed</option>                                        
                                <option value="Disqualified" @if($final_action=='Disqualified') selected @endif>Disqualified</option>
                                <option value="Case Dropped" @if($final_action=='Case Dropped') selected @endif>Case Dropped</option>
                                @endif
                            </select>
                            @if ($errors->has('final_action'))
                            <span class="help-block">
                                <strong class="user">{{ $errors->first('final_action') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-1 mt-2">
                            <p class="mt-4 text-left">                                     
                                <input type="submit" value="Submit" id="button" class="btn btn-primary">
                            </p>
                        </div>

                    </div>
                    @endif
                    <!--                  final action start
                                     @if($check_filter=='1' && !empty($userid_check) && $user_data->officername !='eciexpend')
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
                    
                                                   
                                                </div>
                                                <div class="col-sm-1 mt-2">
                                                    <p class="mt-4 text-left">                                     
                                                        <input type="submit" value="Submit" id="button" class="btn btn-primary">
                                                    </p>
                                                </div>
                                                
                                            </div>
                                             
                                            @endif
                                             final action-->
                    <!--FILTER ENDS HERE-->
                </div> 
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
                            <div class="col"><p class="mb-0 text-right">
                                    <b>State Name:</b> 
                                    <span class="badge badge-info">{{!empty($statedetail->ST_NAME)? $statedetail->ST_NAME:'All'}}</span> &nbsp;&nbsp; 
                                    <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                                    <b>AC:</b> <span class="badge badge-info">{{!empty($acdetail->AC_NAME)? $acdetail->AC_NAME:'All'}}</span>
                                    <b>

                                    </b>
                                    <!--<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->

                                </p></div>
                        </div><!-- end row-->
                    </div><!-- end card-header-->
                    <div > 
                        <div class="table-responsive">

                            @if($check_filter=='1')
                            <table id="example2" class="display" cellspacing="0" width="100%">
                                <!-- <button id="button">checkid</button> -->
                                <thead>
                                    <tr>

                                        <th <?php if (!empty($userid_check)) { ?> class="test_class" <?php } ?>> </th>

                                        <th >State</th>
                                        <th>AC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                        <th>Date Of Lodging</th>
                                        <th class="width-280"> View </th>   
                                        <th> Status </th>

                                    </tr>
                                </thead>
                                <tbody>
<?php $j = 0;
$userid = array();
?>
                                    @if(!empty($finalscrutinycandidate))
                                    @foreach($finalscrutinycandidate as $candDetails)  
<?php
$acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
$date = new DateTime($candDetails->created_at);
$sts = getstatebystatecode($candDetails->st_code);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $date->format('d-m-Y'); // 31-07-2012
// dd($candDetails);
$j++;

$action_list = array("Closed", "Disqualified", "Case Dropped", "Notice Issued", "Hearing Done", "Reply Issued");

if (!in_array($candDetails->final_action, $action_list)) {
    $userid[] = $candDetails->candidate_id;
}
?>

                                    <tr <?php if (in_array(trim($candDetails->final_action), $action_list)) { ?> style="background-color: #999999;cursor: not-allowed;"  <?php } ?>  class="getid" <?php if (!in_array($candDetails->final_action, $action_list)) { ?>  <?php } ?>>

                                        <td id="{{$candDetails->candidate_id}}" style="color: transparent;"<?php if (in_array($candDetails->final_action, $action_list)) { ?> class="desleected" <?php } ?>>
                                            {{$candDetails->candidate_id}}
                                        </td>

                                        <td <?php if (in_array($candDetails->final_action, $action_list)) { ?> style="background-color: #999999;cursor: not-allowed;"   <?php } ?>>@if(!empty($sts->ST_NAME)) {{$sts->ST_NAME}} @endif</td>


                                        <td>@if(!empty($candDetails->ac_no)) {{ $candDetails->ac_no}} - {{ $acDetails->AC_NAME}} @endif</td>
                                        <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                                        <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                        <td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>

                                        <td>
                                            @if(($candDetails->final_by_ro=="1" && $candDetails->finalized_status=="1")  || (strtotime($candDetails->report_submitted_date)>0 && $candDetails->finalized_status=="1"))

                                            <a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-110" target="_blank">Scrutiny Report</a> 
                                            @endif

                                            @if((!empty($candDetails->form_fill_start) && strtotime($candDetails->form_fill_start)>0 && $candDetails->finalized_status=="0") || $candDetails->finalized_status=="1")
                                            <a href="{{url('/')}}/eci-expenditure/view/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-60" >View</a>
                                            @endif
                                            @if($candDetails->final_by_ro !="1" && $candDetails->finalized_status !="1")
                                            N/A
                                            @endif
                                            @if($candDetails->final_by_eci !='1' && $user_data->officername !='eciexpend'  && ! in_array($candDetails->final_action, $issueslist))
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-140" 
                                               target="_blank">Action</a>
                                            @endif
                                        </td>



                                        <td>
                                            @if($candDetails->final_by_eci !='1')

                                            @if(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist))
                                             <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-140" 
                                               target="_blank">   {{!empty($candDetails->final_action)? $candDetails->final_action:'N/A'}} </a>

                                            @elseif($candDetails->date_of_receipt_eci && strtotime($candDetails->date_of_receipt_eci)>0)
                                            Received
                                            @else
                                            Not Received
                                            @endif

                                            @elseif(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist) && $candDetails->final_by_eci!="1")
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-140" 
                                               target="_blank">                                                
                                                {{!empty($candDetails->final_action)? $candDetails->final_action:'N/A'}}                                            
                                            </a>
                                            @elseif(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist) && $candDetails->final_by_eci=="1")
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-140" 
                                               target="_blank">  

                                                {{!empty($candDetails->final_action)? $candDetails->final_action:'N/A'}}</a

                                            @elseif($candDetails->final_by_eci=="1")
                                            <span class="btn-warning text-white btn btn-sm width-100">{{!empty($candDetails->final_action)? $candDetails->final_action:'Finalized'}}</span>
                                            @endif

                                        </td>


                                    </tr>
                                    @endforeach 
                                    @endif 

                                </tbody>
                            </table>
                            @else
                            <table id="example" class="display" cellspacing="0" width="100%">
                                <!-- <button id="button">checkid</button> -->
                                <thead>
                                    <tr> 

                                        <th >State</th>
                                        <th>AC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                        <th>Date Of Lodging</th>
                                        <th class="width-280"> View </th>   
                                        <th> Status </th>

                                    </tr>
                                </thead>
                                <tbody>
<?php $j = 0; ?>
                                    @if(!empty($finalscrutinycandidate))
                                    @foreach($finalscrutinycandidate as $candDetails)  
<?php
$acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
$date = new DateTime($candDetails->created_at);
$sts = getstatebystatecode($candDetails->st_code);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $date->format('d-m-Y'); // 31-07-2012
// dd($candDetails);
$j++;
?>

                                    <tr>

                                        <td>@if(!empty($sts->ST_NAME)) {{$sts->ST_NAME}} @endif</td>
                                        <td>@if(!empty($candDetails->ac_no)) {{ $candDetails->ac_no}} - {{ $acDetails->AC_NAME}} @endif</td>
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
                                            @if($candDetails->final_by_eci !='1' && $user_data->officername !='eciexpend' && ! in_array($candDetails->final_action, $issueslist))
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-140" 
                                               target="_blank">Update Info</a>
                                            @endif
                                        </td>



                                        <td>
                                            @if($candDetails->final_by_eci !='1')

                                            @if(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist))
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-140" 
                                               target="_blank">   {{!empty($candDetails->final_action)? $candDetails->final_action:'N/A'}}</a>

                                            @elseif($candDetails->date_of_receipt_eci && strtotime($candDetails->date_of_receipt_eci)>0)
                                            Received
                                            @else
                                            Not Received
                                            @endif

                                            @elseif(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist) && $candDetails->final_by_eci!="1")
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-140" 
                                               target="_blank">                                                
                                                {{!empty($candDetails->final_action)? $candDetails->final_action:'N/A'}}                                            
                                            </a>
                                            @elseif(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist) && $candDetails->final_by_eci=="1")
                                            <a href="{{url('/eci-expenditure/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-140" 
                                               target="_blank">  

                                                {{!empty($candDetails->final_action)? $candDetails->final_action:'N/A'}}</a

                                            @elseif($candDetails->final_by_eci=="1")
                                            <span class="btn-warning text-white btn btn-sm width-100">{{!empty($candDetails->final_action)? $candDetails->final_action:'Finalized'}}</span>
                                            @endif




                                        </td>


                                    </tr>
                                    @endforeach 
                                    @endif 

                                </tbody>
                            </table>
                            @endif



                        </div> <!-- end responcive-->
                    </div> <!-- end card-body-->
                </div>
            </div>
        </div>
        </div>
    </section>

</main>

<!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
<?php //print_r($PreviewData);die;      ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Status</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
<!-- ProfileRO-->

<!-- end pop up -->

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<!--<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>-->
<!--<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>-->
<!--<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>-->

<!-- Modal -->
<div class="modal fade" id="myModalcheck" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel" style="text-align: -webkit-center;">Are you sure give permission to update scrutiny report?<Br>IF YES GIVE REASON</h6><br>

            </div>
            <div class="form-group definalizeForm">
                <textarea name="definalization_reason" class="form-control" id="definalization_reason"></textarea>
                <span id="definalized_error"></span>
            </div>
            <div class="modal-footer mb-2">
                <input type="hidden" value="" id="definalizedreport">
                <input type="button" value="Submit" id="definalized" class="btn btn-primary mt-2">
                <input type="button" value="Cancel" id="" class="btn btn-default mt-2" data-dismiss="modal">
               <!--  <input type="button" value="" id="definalizedreport"  class="btn btn-primary btncl mt-2" data-dismiss="modal"> -->
            </div>
        </div>
    </div>
</div>

<!-- Modal -->



<div class="modal fade" id="myModaldefi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel"><center>Scrutiny Report is successfully definalized.</center></h6>
            </div>
            <div class="modal-footer mb-2">
                <input type="button" value="Ok" id="" class="btn btn-primary mt-2" data-dismiss="modal">
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="count_by_ceo_count_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel"><center>Scrutiny Report Definalization limit reached at CEO level. </center></h6>
            </div>
            <div class="modal-footer mb-2">
                <input type="button" value="Ok" id="" class="btn btn-primary mt-2" data-dismiss="modal">
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $.noConflict();
    let example = $('#example2').DataTable({

        columnDefs: [{
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }],
        select: {
            style: 'os',
            selector: 'td:first-child',
            style: 'multi'
        },
        order: [
            [1, 'asc']
        ]
    });

    $("#button").click(function () {
        var ids;
        var ids = $.map(example.rows('.selected').data(), function (item) {
            return item[0];
        });

        var received = example.rows('.selected').data().length;
        var final_action = $('#final_action').val();
        if (received.length <= 0) {
            $('.errormessage').text('Please checked at Least one.');
        } else if ($.trim(final_action) === "") {
            $('.errormessage').text('Please select final action.');
        } else {
            $('#loader').css('display', 'block');
            var data = {'received': ids, 'final_action': final_action, "_token": "{{ csrf_token() }}"};
            $.ajax({
                data: data,
                type: "post",
                url: "{{route('updateReceived')}}",
                success: function (response) {
                    $('.errormessage').text('');
                    $('.successmessage').text(response);
                    $('#loader').css('display', 'none');
                    location.reload(true);

                }
            });
        }//successmessage

    });
    var check_all = '<?php echo $stateName; ?>';
    if (check_all == "ALL") {
        $(".test_class").removeClass("test_class select-checkbox sorting_disabled");
        $(".checkbox1").remove();
    } else {

        example.on("click", "th.select-checkbox", function () {
            if ($("th.select-checkbox").hasClass("selected")) {
                example.rows().deselect();
                console.log(example.row(this).data());
                $("th.select-checkbox").removeClass("selected");
            } else {
                example.rows().select();
                // console.log( table.row( this ).data() );
                $("th.select-checkbox").addClass("selected");
            }

        }).on("select deselect", function () {
            ("Some selection or deselection going on")
            if (example.rows({
                selected: true
            }).count() !== example.rows().count()) {
                $("th.select-checkbox").removeClass("selected");
            } else {
                $("th.select-checkbox").addClass("selected");
            }
        });
    }
});


$(document).ready(function () {
    $('.test_class').append($('<span class="checkbox1"></span>'))
});
</script>
<!--graph implementation start here-Manoj -->
@endsection
