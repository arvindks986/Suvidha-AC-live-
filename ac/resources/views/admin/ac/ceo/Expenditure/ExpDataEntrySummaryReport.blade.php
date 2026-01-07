@extends('admin.layouts.pc.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">
    <div class="card-header pt-3" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center pt-3 pb-3">
                <div class="col-sm-12"><h4><b> Data Entry Started Finalised</b></h4></div>  
            </div> 
        </div>
    </div>
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <ul id="breadcrumb" class="pt-1">
                        <li><a href="#">Data Entry Started Finalised - Summary</a></li>
                    </ul>
                </div>
            </div>
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%;">
                    <!--SELECT CANDIDATE-->
                    <div class="card-body" id="demo" class="collapse show">  
                        <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                 <!-- <th>Serial No</th> -->
                                    <th>Name</th> 
                                    <th>Total AC</th>
                                    <th>Data Entry by AC</th>
                                    <th>Data Entry by AC</th>
                                    <th>View Details</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>Nathu Ram Sinodiya</td>
                                    <td>123000</td>
                                    <td>345</td>
                                    <td>6789</td>
                                    <td><a href="" id="" data-toggle="modal" data-target="#myModal">View</a></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div id="barchart_material"></div>
                    <!-- The Modal -->
                    <div class="modal fade" id="myModal">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">

                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title">Data Entry Started Finalised - Summary</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <!-- Modal body -->

                                <div id="barchart_material1"></div>


                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- The Modal End-->

                </div>
                <!--END OF SELECT CANDIDATE-->
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

//*******************ECI FILTER FORM VALIDATION STARTS********************//
$("#EciCustomReportFilter").validate({
    rules: {
        state: {required: true, noSpace: true},
        ScheduleList: {number: true},
    },
    messages: {
        state: {
            required: "Select state name.",
            noSpace: "State name must be without space.",
        },
        ScheduleList: {
            number: "Scedule ID should be numbers only.",
        },
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error)
        } else {
            error.insertAfter(element);
        }
    }
});
//********************ECI FILTER FORM VALIDATION ENDS********************//

</script>
<!--graph implementation start here-Manoj-->
<script type="text/javascript">
//    $(document).ready(function () {
//        console.log("working Hurrah");
//        var id = 1;
//        $.ajax({
//            type: "get",
//            url: "{{url('/')}}/ropc/summary-graph/" + id,
//            dataType: "json",
//            success: function (response) {
//                console.log(response);
//            },
//            errors: function (errors) {
//                console.log(errors);
//            }
//        });
//    });
</script>
<script type="text/javascript">
    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {        
        var id = 1;
        $.ajax({
            type: "get",
            url: "{{url('/')}}/ropc/summary-graph/" + id,
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Data Entry stated and Finalized',
                        subtitle: 'Summary',
                    },
                    bars: 'vertical' // Required for Material Bar Charts.
                };

                var chart = new google.charts.Bar(document.getElementById('barchart_material'));
                var chart1 = new google.charts.Bar(document.getElementById('barchart_material1'));

                chart.draw(data, google.charts.Bar.convertOptions(options));
                chart1.draw(data, google.charts.Bar.convertOptions(options));
            },
            errors: function (errors) {
                console.log(errors);
            }
        });
        

    }
</script>
<!--graph implementation start here-Manoj
<!--**********FORM VALIDATION ENDS*************-->
@endsection
