@extends('admin.layouts.pc.report-theme')
@section('content')
<?php
   $namePrefix = \Route::current()->action['prefix'];
?>

<main role="main" class="inner cover mb-3">
  <!--FILTER STARTS FROM HERE-->
    <div class="card-header pt-3">

     <form method="get" action="{{url($namePrefix.'/GetTrackingReportData')}}" id="EciCustomReportFilter">
            <div class=" row">
                 <div class="col-sm-2">
                    <label for="Elections">Name of Elections</label>
                    <select name="electionType" id="electiontypelist" class="form-control">
                        <option value="" selected="">Select Election Name</option>
                        @foreach ($electionType as $electionTypeItem )
                        <option value="{{ $electionTypeItem->title }}" <?php
                        if (!empty($_GET['electionType']) && $_GET['electionType'] == $electionTypeItem->title) {
                            echo "selected";
                        }
                        ?>> {{$electionTypeItem->title}}</option>
                        @endforeach    
                    </select>

                </div>
                <!--ELECTION LIST DROPDOWN ENDS-->
                <!--PHASE LIST DROPDOWN STARTS--> <div class="col-sm-2">
                    <label for="yearlist">Select Year</label>
                    <select name="year" id="yearlist" class="form-control">

                        @php  $year= range(date('Y'),2010); @endphp
                        <option value="" selected="">Select Year</option>
                        @foreach ($year as  $yearItem ))                         
                        <option <?php
                        if (!empty($_GET['year']) && $_GET['year'] == $yearItem) {
                            echo "selected";
                        }
                        ?> value="{{ $yearItem }}" > {{$yearItem}}</option>
                        @endforeach         

                    </select>
                </div>
                <!--PHASE LIST DROPDOWN ENDS-->



                <div class="col-md-4">
                    <label for="">&nbsp;</label>
                    <div>
                        <input type="submit" value="Filter" class="btn btn-primary">
                                              <!--  <input type="reset " value="Reset Filter" name="Cancel" class="btn"> --></div></div>



            </div>
        </form>
    </div>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <!--  <div class="pull-right"><a href="{{url($namePrefix.'/CreateMisExpenseReport')}}"> <button type="button" class="btn btn-primary" >Add NEW</button></a></div> -->
                <div class="card text-left" style="width:100%;">
                    <div class=" card-header">
                        <div class=" row d-flex align-items-center">
                            <div class="col"><h4> Total No. Of Contesting Candidates  - {{@$total_rec}}</h4></div> 
                            <div class="col"><p class="mb-0 text-right"><b>PC Name:</b>  <span class="badge badge-info">{{$user_data->PC_NAME}}</span>
                                 &nbsp;&nbsp; <b></b> 
 
                                    <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">   

                            <table id="exampledemo" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                     <!-- <th>Serial No</th> -->
                                        <th><div class="width-40">S. No.:</div></th>                                                                           
                                        <th><div class="width-90">PC Name</div></th>
                                        <th><div class="width-175">Contesting Candidate</div></th>
                                        <th>Declaration Date</th> 
                                        <th><div class=" width-175">Returned/Non-returned</div></th> 
                                        <th><div class=" width-255">Nature of Default A/C</div></th>   
                                        <th><div class="width-120">Date of Sending RO's Scrutiny Report to the ECI through the CEO</div></th>
                                        <th>Date of Seeking Additional Information</th> 
                                        <th>In case of default date of reciept of ECI notice</th>
                                        <th>Date of service of ECI notice</th>
                                        <th>Date of receipt of reply -cum-representation from the Candidate on ECI notice</th>
                                        <th>Date of Sending Supplementary Report on ECI Notice if any ,together of acknowledge from the RO/DEO</th>
                                        <th><div class="width-200">Action</div></th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @php  $count = 1; @endphp
                                    @forelse ($expenditureData as $item)
                                    <tr class="edit_{{$item->id}}">
                                        <td>{{ $count }}</td>                                        
                                        <td>{{$item->PC_NO }}  - {{$item->PC_NAME }}</td>
                                        <td>{{$item->contensting_candiate}}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_of_declaration)) }}</td>

                                        <td>
                                            <select name="return_status" id="{{$item->id}}" class="form-control editData">
                                        <option value="">Select Return Type</option>
                                        <option value="Returned" <?php if(!empty($item->return_status) && $item->return_status=="Returned"){ echo "selected"; }?> >Returned</option>
                                         <option value="Non-Returned" <?php if(!empty($item->return_status) && $item->return_status=="Non-Returned"){ echo "selected"; }?>>Non-Returned</option>

                                        </select>
                                        </td>

                                        <td> 
                                            <?php //print_r($ReportSingleData); ?>
                                            <select name="nature_of_default_ac" id="{{$item->id}}" class="form-control nature_of_default_ac_{{$item->id}} editData" >
                                        <option value="">Select Nature of Default in A/C</option>
                                        @foreach ($nature_of_default_ac as $nature_ac )
                                        <option <?php
                                        if (!empty($item->nature_of_default_ac)) {
                                            if ($item->nature_of_default_ac == $nature_ac->title) {
                                                echo "selected";
                                            }
                                        }
                                        ?>  value="{{ $nature_ac->id }}" >{{$nature_ac->title}}</option>

                                        @endforeach

                                        </select>
                                        </td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_of_sending_deo}}" name="date_of_sending_deo" class="form-control editData" id="{{$item->id}}"> </td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_of_sending_additional_info}}" name="date_of_sending_additional_info" class="form-control editData disabled_{{$item->id}}" id="{{$item->id}}"></td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_of_receipt}}" class="form-control editData disabled_{{$item->id}}" name="date_of_receipt" id="{{$item->id}}"></td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_of_receipt}}" class="form-control editData disabled_{{$item->id}}" name="date_of_receipt" id="{{$item->id}}"></td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_of_receipt_represetation}}" name="date_of_receipt_represetation" class="form-control editData disabled_{{$item->id}}" id="{{$item->id}}"></td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_sending_supplimentary}}" name="date_sending_supplimentary" class="form-control editData disabled_{{$item->id}}" id="{{$item->id}}"></td>
                                        <td ><button type="button" class="editTable btn btn-primary" id="{{$item->id}}">Edit</button>&nbsp;<button type="button" class="btn btn-secondary repliedByro" id="{{$item->candidate_id}}">Scrutiny Report</button>&nbsp;

                                            <!-- <button type="button" class="comfirmReport comfirmReports_{{$item->candidate_id}}" id="{{$item->candidate_id}}">Confirm</button> --></td>
                                    </tr>
                                    @php  $count++;  @endphp
                                    @empty
                                    <tr>
                                        <td colspan="4">No Data Found</td>                 
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div><!-- End Of  table responsive --> 
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<!---/////////////////modal for data preview /////////////////-->
    <!-- Modal -->
    <div class="modal fade" id="ModalReply" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <?php //print_r($PreviewData);die;   ?>
                <div class="modal-body">
                   
                    <div class="scruitData"></div>
                </div>
             
            </div>

        </div>
    </div>



<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>



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
// get district by state code


// end 

</script>
<script>

    var namePrefix ="<?php echo $namePrefix; ?>";
    $('select[name="state_id"]').on('change', function () {
        var stateID = $(this).val();

        if (stateID) {
            $.ajax({
                url: "{{url('/')}}/expenditure/getDistricAjax/" + encodeURI(stateID),
                type: "GET",
                dataType: "json",
                success: function (data) {
                    //alert(data);
                    $('select[name="district_id"]').empty();
                    $.each(data, function (key, value) {
                        $('select[name="district_id"]').append('<option value="' + value.rowid + '">' + value.DIST_NAME_EN + '</option>');
                    });
                }
            });
        } else {
            $('select[name="district_id"]').empty();
        }
    });
	
	$(document).ready(function() {
    $('#exampledemo').DataTable( {
        "order": [[ "desc" ]],
		/*"columnDefs": [
      { "width": "10px", "targets": 0 },
      { "width": "40px", "targets": 1 },
      { "width": "100px", "targets": 2 },
      { "width": "100px", "targets": 3 },
      { "width": "100px", "targets": 4 },
      { "width": "100px", "targets": 5 }
	  { "width": "100px", "targets": 6 },
      { "width": "100px", "targets": 7 },
      { "width": "100px", "targets": 8 },
      { "width": "100px", "targets": 9 },
      { "width": "100px", "targets": 10 },
      { "width": "100px", "targets": 11 }
    ]*/
    } );
} );


$(document).ready(function() {
     $("#exampledemo :input").attr("disabled", true);
    $(':input[type="button"]').prop('disabled', false);

     //$("#editTable tr td :button").attr("disabled", false);

}); 

$( ".editTable" ).click(function() {
var  tbid = $(this).attr('id');
var value = $('.nature_of_default_ac_'+tbid).val();
var column = $('.nature_of_default_ac_'+tbid).attr('name');
    if(column=="nature_of_default_ac" && value=="5")
    {
          $(".edit_"+tbid+" :input").attr("disabled", false);

           $(".disabled_"+tbid).attr("disabled", true);
         
    }
    else
    {
         $(".edit_"+tbid+" :input").attr("disabled", false);

        $(".disabled_"+tbid).attr("disabled", false);
    }


});


$( ".editData" ).change(function() {
    var value = $(this).val();
    var  tbid = $(this).attr('id');
    var column = $(this).attr('name');


if(column=="nature_of_default_ac" && value=="5")
    {
           $(".disabled_"+tbid).attr("disabled", true);
         
    }
    else
    {
        $(".disabled_"+tbid).attr("disabled", false);
    }

     $.ajax({
                url: "{{url('/ropc/updateData')}}",
                type: "get",
                data: {"tbid": tbid,value:value,column:column},
                success: function (data) {
                    
                }
            });

});



$( ".repliedByro" ).click(function() {
    var candidate_id = $(this).attr('id');
    jQuery.ajax({
            url: "{{url('/ropc/getscrutinyreport')}}",
            type: 'GET',
            data: {candidate_id:candidate_id},
            dataType:'html',
            success: function(result){
                //alert(result);
                $('.scruitData').html(result);
                $('#ModalReply').modal('show');
                $('#cmd').attr('ids',candidate_id);

                }
        });

});








$( "#cmd" ).click(function() {
    var candidate_id = $(this).attr('ids');
    jQuery.ajax({
            url: "{{url('/ropc/generatePDF')}}",
            type: 'POST',
            data: {candidate_id:candidate_id,"_token": "{{ csrf_token() }}"},
            success: function(result){
                alert(result);
                // $('.scruitData').html(result);
                // $('#ModalReply').modal('show');


                }
        });

});



// $( ".comfirmReport" ).click(function() {
//     var candidate_id = $(this).attr('id');
//     var answer = confirm('Are You Sure want to confirm the Report?');
//     if(answer){
//     jQuery.ajax({
//             url: "{{url('/ropc/confirmReport')}}",
//             type: 'GET',
//             data: {candidate_id:candidate_id},
//             dataType:'html',
//             success: function(response){
//                     response  = response.trim();
//                     if(response==1)
//                     {
//                       $(".comfirmReports_"+candidate_id).attr("disabled", true);

//                     }
//                 }
//         });
//     }
// });




</script>

<!--**********FORM VALIDATION ENDS*************-->
@endsection



