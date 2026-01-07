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
<div class="col-sm-2">
                    <label for="Elections">Select PC Name</label>
                   <select name="pcname" id="pcname" class="form-control" >
                                        <option value="all">Select name of PC</option>
                                        @php $pclist = getpcbystate($user_data->st_code); @endphp

                                        @foreach ($pclist as $allpc ))
                                        <option value="{{ $allpc->PC_NO }}" <?php
                        if (!empty($_GET['pcname']) && $_GET['pcname'] == $allpc->PC_NO) {
                            echo "selected";
                        }
                        ?>>{{$allpc->PC_NAME}}</option>

                                        @endforeach

                                        @if ($errors->has('state'))
                                        <span class="help-block">
                                            <strong class="user">{{ $errors->first('state') }}</strong>
                                        </span>
                                        @endif

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
                        <!-- <a href="{{url($namePrefix.'/tracking')}}"><input type="button" value="Reset Filter" name="Cancel" class="btn"></a> --></div></div>



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
                            <div class="col"><p class="mb-0 text-right"><b>State Name :</b> <span class="badge badge-info">{{$user_data->ST_NAME}}</span>

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
                                        <th><div class="width-90">PC NO & Name</div></th>
                                        <th><div class="width-175">Contesting Candidate</div></th>
                                        <th>Date of receipt of DEO's Scrutiny Report</th> 
                                        <th>Date of sending the DEO's scrutiny report to the commission</th> 
                                        <th><div class=" width-255">Nature of Default A/C</div></th>   
                                        <th><div class="width-120">Whether any additional information has been sought by the commission from the DEO </div></th>
                                        <th>Date of receipt and service of notice</th> 
                                        <th><div class="width-200">Current Status</div></th>
                                       
                                        <th><div class="width-255">Action</div></th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @php  $count = 1; @endphp
                                    @forelse ($expenditureData as $item)
                                    <tr class="edit_{{$item->id}}">
                                        <td>{{ $count }}</td>  
                                        <td>{{$item->PC_NO }}  - {{$item->PC_NAME }}</td>
                                        <td>{{$item->contensting_candiate}}</td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_of_receipt}}" name="date_of_receipt" class="form-control editData" id="{{$item->id}}"> </td>
                                        <td><input type="date"  min="2019-05-01" max="2019-06-18"value="{{$item->date_of_sending_deo}}" name="date_of_sending_deo" class="form-control editData" id="{{$item->id}}"> </td>

                                       
                                        <td> 
                                            <?php //print_r($ReportSingleData); ?>
                                            <select name="nature_of_default_ac" id="{{$item->id}}" class="form-control editData" >
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
                                        <td class="selected_{{$item->id}}">

                                           <!--  <input type="date" value="{{$item->date_of_sending_additional_info}}" name="date_of_sending_additional_info" class="editData" id="{{$item->id}}">  -->

                                            <select name="date_of_sending_additional_info_ceo"  id="{{$item->id}}" class="form-control editData ">
                                        <option value="">Select Status</option>
                                        <option value="yes" <?php if(!empty($item->return_status) && $item->return_status=="yes"){ echo "selected"; }?> >Yes</option>
                                         <option value="no" <?php if(!empty($item->return_status) && $item->return_status=="no"){ echo "selected"; }?>>No</option>

                                        </select>


                                        </td>
                                        <td><input type="date" min="2019-05-01" max="2019-06-18" value="{{$item->date_of_receipt_notice_service}}" name="date_of_receipt_notice_service" class="form-control editData" id="{{$item->id}}"></td>
                                        <td>
                                            <select name="current_status" id="{{$item->id}}" class="form-control editData">
                                        <option value="">Select Election Name</option>
                                        @foreach ($current_status as $current )
                                        <option value="{{ $current->id }}" <?php
                                        if (!empty($item->current_status)) {
                                            if ($item->current_status == $current->id) {
                                                echo "selected";
                                            }
                                        }
                                        ?>> {{$current->title}}</option>
                                        @endforeach    
                                        </select>
                                        </td>
                                        <td >
    
                                             <?php if(!empty($item->final_by_ceo==1)) { ?>
                                                <button type="submit" class="editTable btn btn-primary" id="{{$item->id}}" disabled="disabled">Edit</button>&nbsp;
                                                <button type="submit" class="btn btn-secondary" disabled="disabled">Confirmed</button>
                                                <?php } else{ ?>
                                            <button type="button" class="editTable btn btn-primary" id="{{$item->id}}">Edit</button>&nbsp;        
                                            <button type="button" class="btn btn-secondary comfirmReport comfirmReports_{{$item->candidate_id}}" id="{{$item->candidate_id}}">Confirm</button>
                                           

                                            <?php } ?>
                                            <button type="button" class="btn btn-secondary repliedByro" id="{{$item->candidate_id}}">Report</button>

                                        </td>
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
    //alert(tbid);
   $(".edit_"+tbid+" :input").attr("disabled", false);

});


$('.editData').keyup(function(event){

    var value = $(this).val();
    var  tbid = $(this).attr('id');
    var column = $(this).attr('name');

    if(column=="date_of_sending_additional_info_ceo" && value=="yes" )
    {
         $('.selected_'+tbid ).append('<input type="text" name="sending_additional_info_sought"  class="form-control editData" id="'+tbid+'" >');
    }

    if(column=="date_of_sending_additional_info_ceo" && (value=="no" || value==""))
    {
        $('.selecteded_'+tbid).remove();
    }


     $.ajax({
                url: "{{url('/pcceo/updateData')}}",
                type: "get",
                data: {"tbid": tbid,value:value,column:column},
                success: function (data) {
                    
                }
            });

});

$( ".editData" ).change(function() {
    var value = $(this).val();
    var  tbid = $(this).attr('id');
    var column = $(this).attr('name');

    if(column=="date_of_sending_additional_info_ceo" && value=="yes" )
    {
         $('.selected_'+tbid ).append('<textarea name="sending_additional_info_sought" value=""  class="form-control editData selecteded_'+tbid+' " id="'+tbid+'" ></textarea>');
    }

    if(column=="date_of_sending_additional_info_ceo" && (value=="no" || value==""))
    {
        $('.selecteded_'+tbid).remove();
    }

    


     $.ajax({
                url: "{{url('/pcceo/updateData')}}",
                type: "get",
                data: {"tbid": tbid,value:value,column:column},
                success: function (data) {
                    
                }
            });

});


$( ".repliedByro" ).click(function() {
    var candidate_id = $(this).attr('id');
    jQuery.ajax({
            url: "{{url('/pcceo/getscrutinyreport')}}",
            type: 'GET',
            data: {candidate_id:candidate_id},
            dataType:'html',
            success: function(result){
                //alert(result);
                $('.scruitData').html(result);
                $('#ModalReply').modal('show');


                }
        });

});


$( ".comfirmReport" ).click(function() {
    var candidate_id = $(this).attr('id');
    var answer = confirm('Are You Sure want to confirm the Report?');
    if(answer){
    jQuery.ajax({
            url: "{{url('/pcceo/confirmReport')}}",
            type: 'GET',
            data: {candidate_id:candidate_id},
            dataType:'html',
            success: function(response){
                    response  = response.trim();
                    if(response==1)
                    {
                      $(".comfirmReports_"+candidate_id).attr("disabled", true);
                      loaction.reload();

                    }
                }
        });
    }
});



</script>

<!--**********FORM VALIDATION ENDS*************-->
@endsection



