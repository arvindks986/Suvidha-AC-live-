@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Notification List')
@section('bradcome', 'Notification List')
@section('description', '')
@section('content') 
<?php 
  $cons_no= !empty($_GET['ac']) ? $_GET['ac'] : 0;
$acdetails = getacbyacno($user_data->st_code, $cons_no);
 
$acName = !empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
 
$all_ac="";
 
$st = getstatebystatecode($user_data->st_code);  
$receivefilter = !empty($_GET['receivefilter']) ? $_GET['receivefilter'] : "";

$userid_check = array();

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
<?php if (empty($userid_check)) { ?>
    <style type="text/css">
        th.select-checkbox.sorting_disabled.selected:after {
            display: none;
        }
    </style>

<?php } ?>

<?php if ($acName === "ALL") { ?>
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
    #loader {
        display: none;
        position: absolute;
        left: 50%;
        top: 70%;
        z-index: 1;
        width: 150px;
        height: 150px;
        margin: -75px 0 0 -75px;
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
    }

    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Add animation to "page content" */
    .animate-bottom {
        position: relative;
        -webkit-animation-name: animatebottom;
        -webkit-animation-duration: 1s;
        animation-name: animatebottom;
        animation-duration: 1s
    }

    @-webkit-keyframes animatebottom {
        from { bottom:-100px; opacity:0 } 
        to { bottom:0px; opacity:1 }
    }

    @keyframes animatebottom { 
        from{ bottom:-100px; opacity:0 } 
        to{ bottom:0; opacity:1 }
    }

    #myDiv {
        display: none;
        text-align: center;
    }


    .select-checkbox::selection {color:transparent;} 
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
            padding: 0px 14px;
            margin-left: 7px;
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
    th.test_class.select-checkbox::selection {
        color: #fff;
    } 
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
        width: 25px;
        color: black;
        background-color: transparent;
        height: 25px;
        box-sizing: border-box;
    }
</style>

<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">

                <div class="col-sm-12 mt-3">
                     <div class="errormessage"></div>
                    <div class="successmessage"></div>
               <!--FILTER STARTS FROM HERE-->
                @if($check_filter =="1")
              <form method="get" action="{{url('/acceo/definalizedcandidate')}}" id="EcidashboardFilter">           
                 <div class="row justify-content-center">
                    
                            <div class="col-sm-3">
                  <label for="" class="mr-3">Select AC</label>    
                  <select name="ac" id="ac" class="consttype form-control" >
                    <option value="">-- All AC --</option>
                    @php $all_ac = getacbystate($user_data->st_code); @endphp
                    @foreach($all_ac as $getAc)
                    @if ($cons_no == $getAc->AC_NO)
                      <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                      @else
                      <option value="{{ $getAc->AC_NO }}" <?php if(!empty($_GET['ac']) && $getAc->AC_NO==$_GET['ac']){ echo "selected";} ?>>{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                    @endif
                        @endforeach 
                    </select>
                        @if ($errors->has('ac'))
                          <span style="color:red;">{{ $errors->first('ac') }}</span>
                        @endif
                     
                            <div class="acerrormsg errormsg errorred"></div>
                        </div>
                       <!-- <div class="col-sm-3">                                
                                 <label for="" class="mr-3">Select Received/Not received</label>    
                                <select name="receivefilter"   class=" form-control" >
                                    <option value="" @if($receivefilter=='') selected @endif>-- All --</option>                                    
                                    <option value="y" @if($receivefilter=='y') selected @endif >Received</option>                                    
                                    <option  value="n" @if($receivefilter=='n') selected @endif >Not Received</option>                                    
                                </select> 
                            </div> -->
                        <div class="col-sm-1 mt-2">
                            <p class="mt-4 text-left">
                            <!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
                          <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
                </p>
                        </div>
                    </div>
                </form> 
                @endif
                 <!-- final action start-->
                    <!-- @if($check_filter=='1')
                    <div class="row justify-content-center">                            
                        <div class="col-sm-3">
                            <label  class="mr-3">Select Action</label>    
                            <select name="final_action" id="final_action" required class="form-control" >
                                <option value="">Select Action </option>
                                <option value="Received">Received</option>
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
-->
                    <!-- final action-->
                 <!--FILTER ENDS HERE-->
                </div> 

                <div class="col"><h6 class="mr-auto mt-2">Finalized Candidate List : </h6>


                </div> 
                <!--
<div class="col"><p class="mb-0 text-right">
        <b>State Name:</b> 
        <span class="badge badge-info"><?php //$st->ST_NAME    ?></span> &nbsp;&nbsp; 
        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
        <b>PC:</b> <span class="badge badge-info"><?php //$pcName    ?></span>
        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>

    </p></div>-->
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
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

                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
                        <div class="card-body" id="demo" class="collapse show">  
                             <table id="example2" class="display table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                         <!-- <th <?php if (!empty($userid_check)) { ?> class="test_class" <?php } ?>> All</th> -->
                                        <th>AC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                        <th>Date Of Lodging</th>                                          
                                        <th class="width-180"> Action </th>   
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <?php $j = 0;
                                
                                ?>
                                @if(!empty($scrutinycandidate))
                                @foreach($scrutinycandidate as $candDetails) 
							
                                <?php
                               
                                
                                $date = new DateTime($candDetails->created_at);
                                
                                $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                                 
                               
                                $j++;
								//dd($candDetails);
								$ac=getacbyacno($candDetails->st_code,$candDetails->ac_no);
                                ?>
                                <tr  <?php if (!empty($candDetails->date_of_receipt)) { ?> style="background-color: #999999;cursor: not-allowed;"  <?php } ?>  class="getid" <?php if (empty($candDetails->date_of_receipt)) { ?>  <?php } ?>>
                                  
                                    <!-- <td id="{{$candDetails->candidate_id}}" style="color: transparent;"<?php if (!empty($candDetails->date_of_receipt)) { ?> class="desleected" <?php } ?>>
                                        <?php if (empty($candDetails->date_of_receipt)) { ?> {{$candDetails->candidate_id}} <?php } ?>
                                    </td> -->
                                    
                                    <td>@if(!empty($candDetails->ac_no)){{ $ac->AC_NO}}-{{ $ac->AC_NAME}} @endif</td>
                                    <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                                    <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                    <td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td> 
                                                                         
                                    <td>
                                    @if($candDetails->finalized_status =='1')
                                    
                                    <input type="hidden" name ="ac_no_is" id="ac_no_is" value= "{{$candDetails->ac_no}}">
     <input type="hidden" name ="candidateid" id="candidateid" value= "{{$candDetails->candidate_id}}">
     <button type="button" class="btn btn-primary btn-mm" id="finalized" value= "{{$candDetails->candidate_id}}-{{$candDetails->ac_no}}"  onClick="updatestatus(this.value)" >DeFinalized </button>
  
                                    @else
                                    Completed
                                        @endif
                                               <!-- @if($candDetails->final_by_ro=="1" || strtotime($candDetails->report_submitted_date)>0 )
                                        
                                        <a href="{{url('/')}}/acceo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-120" target="_blank">DeFinalized</a> 
                                        @endif -->

                                                                       
                                    </td>
                                           <?php 

                                                      $issueslist=array("Hearing Done","Reply Issued","Notice Issued");                                 
                                            ?>                            
                                    <td>
                                    @if($candDetails->finalized_status =='1')
                                    
                                    Finalized 
                                    @else
                                    DeFinalized
                                        @endif
                                               <!-- @if($candDetails->final_by_ro=="1" || strtotime($candDetails->report_submitted_date)>0 )
                                        
                                        <a href="{{url('/')}}/acceo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-120" target="_blank">DeFinalized</a> 
                                        @endif -->
 
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
        </div>
    </section>
  
</main> 
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>
   

<script type="text/javascript">
$(document).ready(function () {

    $('.test_class').append($('<span class="checkbox1"></span>'))
});
$(document).ready(function () {
    $.noConflict();
    let example = $('#example2').DataTable({

        columnDefs: [{
                orderable: false,
                //className: 'select-checkbox',
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
            $('.errormessage').text('Please select Options.');
        } else {
          $('#loader').css('display', 'block');
            var data = {'received': ids, 'final_action': final_action, "_token": "{{ csrf_token() }}"};
            $.ajax({
                data: data,
                type: "post",
                url: "{{route('updateReceived')}}",
                url: '<?php echo url('/') ?>/acceo/updateReceived',
                success: function (response) {
                    $('.errormessage').text('');
                    $('.successmessage').text(response);
                    $('#loader').css('display', 'none');
                    setTimeout(function () {
                        location.reload(true);
                    }, 2000)
                }
            });
        }//successmessage

    });

    var check_all = '<?php echo $acName; ?>';
    if (check_all == "ALL") {
        
        $(".test_class").removeClass("test_class select-checkbox sorting_disabled");
        $(".checkbox1").remove();
    } else {

        example.on("click", "th.select-checkbox", function () {
            if ($("th.select-checkbox").hasClass("selected")) {
                example.rows().deselect();
                
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




function updatestatus(valuis){ 

let text = "Are You Sure Definalized for Candidates.";
if (confirm(text) == true) {

var candid=valuis;

var ac_no=$("#ac_no_is").val();
//alert(candid);

var data = {'candid': candid,'ac_no': ac_no,  "_token": "{{ csrf_token() }}"};

           
                        $.ajax({
                            data: data,
                            type: "post",
                        
                            url: '<?php echo url('/') ?>/acceo/UpdateStatusData',
                            success: function (data) {
                                response = data.trim();
                               
                    if (response == 1) {
                        setTimeout(function () {
                    location.reload(true);
                }, 1000)
                        $('#myModalSucc').modal('show');
                       // $('#getlist').attr('ids',candID);
                    }
                    if (response == 0) {
                        $('#myModalErr').modal('show');
                    }
                }
                        });

                    }else{

                        //window.location.href = "{{url('/acdeo/definalizedcandidate?ac=')}}"+ac_no;
                    }
}



/*
$(document).on('click', '#finalized', function () {
    let text = "Are You Sure Definalized for Candidates.";
  if (confirm(text) == true) {
    
    var candid=$("#candidateid").val();
    var ac_no=$("#ac_no_is").val();
    
    var data = {'candid': candid,  "_token": "{{ csrf_token() }}"};
   
               
                            $.ajax({
                                data: data,
                                type: "post",
                            
                                url: '<?php echo url('/') ?>/acceo/UpdateStatusData',
                                success: function (data) {
                                    response = data.trim();
                                   
                        if (response == 1) {
                            setTimeout(function () {
                        location.reload(true);
                    }, 2000)
                            $('#myModalSucc').modal('show');
                           // $('#getlist').attr('ids',candID);
                        }
                        if (response == 0) {
                            $('#myModalErr').modal('show');
                        }
                    }
                            });

                        }else{

                            //window.location.href = "{{url('/acdeo/definalizedcandidate?ac=')}}"+ac_no;
                        }
            });

*/



</script>

@endsection
