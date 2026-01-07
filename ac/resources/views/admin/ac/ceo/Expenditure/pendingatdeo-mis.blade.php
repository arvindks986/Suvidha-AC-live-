@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
@php
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $acdetails=getacbyacno($st_code, $cons_no); 
  $acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
  //echo $st_code.'cons_no=>'.$cons_no;
@endphp
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col-md-5 mt-2 mb-2"><h5 class="mr-auto">Pending At DEO: {{$count}}</h5></div> 
                <div class="col-md-7 mt-2 mb-2 text-right"><p class="mb-0">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>AC:</b> <span class="badge badge-info">{{ $acName }}</span>
                        <a href="{{url('/acceo/exppendingatroPDF')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/acceo/exppendingatroEXL')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                        <b></b><a href="{{url('/acceo/mis-officer')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
                        <div class="card-body" id="demo" class="collapse show">  
                            <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
		 <th>State</th>
          <th>AC No & Name</th>
          <th>Candidate Name</th>
          <th>Party Name</th>
          <th>Last Date of Submission</th>
          <th>Date of Scrutiny Report Submission</th>
         <th>Date of Lodging A/C By Candidate</th>
         <th>Date of Sending to the CEO</th>
         <th>Date of Receipt By CEO</th>
          <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
    @if(!empty($partiallyCandList))
    @foreach($partiallyCandList as $candDetails)  
      <?php
       //dd($candDetails);
       $ac = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
       $date = new DateTime($candDetails->created_at);
       //echo $date->format('d.m.Y'); // 31.07.2012
       $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
      // dd($candDetails);
        $j++; 
        $stDetails=getstatebystatecode($candDetails->ST_CODE);
      // dd($candDetails);
        $j++; 
        ?>
<tr>
<td>@if(!empty($stDetails->ST_NAME)) {{ $stDetails->ST_NAME}} @endif</td>
 <td>@if(!empty($ac->AC_NO))  {{ $ac->AC_NO }}-{{ $ac->AC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '22-06-2019'}} @endif</td>
<td>@if(!empty($candDetails->finalized_date)) {{ date('d-m-Y',strtotime($candDetails->finalized_date))}}  @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_orginal_acct)) {{ date('d-m-Y',strtotime($candDetails->date_orginal_acct))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_sending_deo)) {{  date('d-m-Y',strtotime($candDetails->date_of_sending_deo))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_receipt) && ($candDetails->date_of_receipt !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->date_of_receipt))}}  @else {{ 'N/A'}} @endif</td>

<td>  @if(($candDetails->final_by_ro=='1'))
                <a href="{{url('/')}}/acceo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a> 
                 @else <span class="btn-secondary text-white btn btn-sm width-100">Partially Filled</span>@endif</td>
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
                <!-- <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">

                        <div class="card-body"  class="collapse show">
                            @if($count>0)
                            <div id="barchart"></div>
                            @else
                            No data for graph. 
                            @endif
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </section>
</main>

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var id = 1;
        $.ajax({
            type: "get",
            url: "{{url('/')}}/eci-expenditure/candidateListBydataentryStartgraph/{{$st_code}}/{{$cons_no}}",
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Overall summary of Data entry started',                         
                    },
                    bars: 'vertical' // Required for Material Bar Charts.
                };

                var chart = new google.charts.Bar(document.getElementById('barchart'));
                chart.draw(data, google.charts.Bar.convertOptions(options));

            },
            errors: function (errors) {
                console.log(errors);
            }
        });


    }
</script>
<!--graph implementation start here-Manoj -->
@endsection