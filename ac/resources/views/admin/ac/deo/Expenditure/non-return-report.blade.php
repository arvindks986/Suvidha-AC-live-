@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Non Return Data List')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
@php
  $st_code=!empty($st_code) ? $st_code : '';
  $cons_no=!empty($cons_no) ? $cons_no : '';
  $st=getstatebystatecode($st_code);
 
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $pcdetails=getacbyacno($st_code, $cons_no); 
  $acName=!empty($pcdetails->AC_NAME) ? $pcdetails->AC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
$distName=!empty($distname->DIST_NAME)? $distname->DIST_NAME:'All';
@endphp
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h2 class="mr-auto">Non Return Data List : {{$count}}</h2></div> 
                <div class="col"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>District Name:</b> <span class="badge badge-info">{{ $distName}}</span>
                        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>

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
                            <table id="example-new" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>AC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                        <th>Date Of Lodging</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <?php $j = 0; ?>
                                @if(!empty($nonreturnCandList))
                                @foreach($nonreturnCandList as $candDetails)  
                                <?php
                                $pc = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                                $date = new DateTime($candDetails->created_at);
                                //echo $date->format('d.m.Y'); // 31.07.2012
                                $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                                $j++;
                                ?>
                                <tr>
                                    <td>@if(!empty($pc->AC_NO))  {{ $pc->AC_NO}}-{{ $pc->AC_NAME}} @endif</td>
                                    <td>
                                          <a href="javascript:void(0)" onclick="getProfile('{{$candDetails->candidate_id}}')">
                                        @if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</a>
                                    </td>
                                    <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                    <td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>
                                     <td >
                                             @if($candDetails->final_by_ro=='1')   
                                             <a href="{{url('/')}}/acdeo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}/{{base64_encode($candDetails->constituency_no)}}" class="btn btn-primary" target="_blank">Scrutiny Report</a>
                                    <!-- <a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary" target="_blank">DEO Scrutiny Report</a>  -->
                                     @else
                                      N/A
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
               <!--  <div class="col-lg-6 col-md-12 col-sm-12">
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
<!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
<?php //print_r($PreviewData);die;    ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Profile</h4></center></div>
                <br>
                <div class="profileData"></div>
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
 /*   google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var id = 1;
        $.ajax({
            type: "get",
            url: "{{url('/')}}/ropc/candidateListByfinalizeDatagraph",
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Overall summary of Finalized',                         
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


    } */
          function getProfile(candidate_id){
        //var candidate_id = $(this).attr('id');
        jQuery.ajax({
            url: "{{url('/acdeo/getProfile')}}",
            type: 'GET',
            data: {candidate_id: candidate_id},
            dataType: 'html',
            success: function (result) {
                 
                $('.profileData').html(result);
                $('#ModalProfile').modal('show');
                 

            }
        });
    }
</script>
<!--graph implementation start here-Manoj -->
<script>
$(document).ready(function() {
    var table = $('#example-new').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
               filename: function() {
                return 'non-return-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   District Name:'.$distName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection


