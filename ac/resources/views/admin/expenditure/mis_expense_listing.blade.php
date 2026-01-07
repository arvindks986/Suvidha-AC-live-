@extends('admin.layouts.pc.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">

    <!--FILTER STARTS FROM HERE-->
    <div class="card-header pt-3">

<?php //print_r($PreviewData);

$namePrefix = \Route::current()->action['prefix']; ?>



<form method="get" action="{{url($namePrefix.'/expentiture_listing')}}" id="EciCustomReportFilter">
            <div class=" row">
             @if($user_data->role_id==4)

                <div class="col-sm-2">
                    <!--STATE LIST DROPDOWN STARTS-->
                    <label for="state">Name of States</label>
                    <select  id="state_id" class="form-control" name="state_id" >
                        <option value="" selected="">--Select State --</option>
                        @php $statelist = getallstate(); @endphp
                        @foreach ($statelist as $state_List ))                        
                        <option <?php
                        if (!empty($_GET['state_id']) && $_GET['state_id'] == $state_List->ST_CODE) {
                            echo "selected";
                        }
                        ?> value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>
                        @endforeach
                    </select>
                </div>
                <!--STATE LIST DROPDOWN ENDS-->
                <!--PHASE LIST DROPDOWN STARTS--> 
                <div class="col-sm-2">
                    <label for="district_id">Name of District</label>
                    <select   id="district_id" name="district_id" class="form-control">
                        <option value="" selected="">--Select District --</option>
                        @if(!empty($all_districts))
                        @foreach($all_districts as $all_distrit)

                        <option <?php
                        if (!empty($_GET['district_id']) && $_GET['district_id'] == $all_distrit->row_id) {
                            echo "selected";
                        }
                        ?> value="{{$all_distrit->row_id}}">{{ $all_distrit->DIST_NAME }}</option>

                        @endforeach
                        @endif 

                    </select>
                </div>
                @endif
                <!--ELECTION LIST DROPDOWN STARTS--> <div class="col-sm-2">
                    <label for="Elections">Name of Elections</label>

                    <select name="electionType" id="electiontypelist" class="form-control">
                        <option value="" selected="">Select Election Name</option>
                        @foreach ($electionType as $electionTypeItem )
                        <option value="{{ $electionTypeItem->id }}"> {{$electionTypeItem->title}}</option>
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
                        <a href="{{url($namePrefix.'/expentiture_listing')}}"><input type="button" value="Reset Filter" name="Cancel" class="btn"></a></div></div>



            </div>
        </form>
    </div>

    <!--FILTER ENDS HERE-->
<?php //print_r($expenditureData);die; ?>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                 <div class="pull-right"><a href="{{url($namePrefix.'/CreateMisExpenseReport')}}"> <button type="button" class="btn btn-primary" >Add NEW</button></a></div>
                <div class="card text-left" style="width:100%;">

                    <div class=" card-header">
                        <div class=" row d-flex align-items-center">
                            <div class="col"><h4> Total No. Of Contesting Candidates  - {{@$expenditureData[0]->total_rec}}</h4></div> 
                            <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
<!--                                    <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('/eci/EciElectionSchedulePdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                                    <a href="{{url('/eci/EciElectionScheduleExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;-->
                                    <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">   

                            <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                     <!-- <th>Serial No</th> -->
                                        <th>S. No.:</th>
                                        <th>PC Name</th>
                                        <th>Contesting Candidate</th>
                                        <th>Declaration Date</th> 
                                        <th>Last Date of Lodging of A/C</th> 
                                        <th>Returned/Not Returned</th>
                                        <th>Nature of Default AC</th>           
                                        <th>Date of Sending Additional Information</th> 
                                          @if($user_data->role_id==18)
                                        <th>Date of Sending DEO's Scrutiny Report to the ECI through the CEO</th>
                                         <th>In case of default date of reciept of ECI notice</th>
                                         <th>Date of service of ECI notice</th>
                                         <th>Date of Sending Acknowledgement to ECI</th>
                                         <th>Date of receipt of reply -cum-representation from the Candidate on ECI notice</th>
                                         <th>Date of Sending Supplementary Report on reply of ECI Notice</th>
                                        @endif
                                       
                                       <!-- for ECI -->
                                        @if($user_data->role_id==7)
                                        <th>Date of reciept of DEO's scrutiny report from the CEO/DEO</th>

                                        <th>Date of issuance of notice</th>
                                        <th>Date of Sending Supplementary Report on ECI Notice if any ,together of acknowledge from the DEO/CEO</th>
                                        <th>Final Action</th>
                                        @endif
                                        <!--end ECI-->
                                        <!-- for CEO -->
                                        @if($user_data->role_id==4)
                                       <th>Date of reciept of DEO's srcutiny report the commission</th>
                                        <th>Date of receipt of notice and service</th>
                                        <th>Current Status</th>
                                        @endif
                                        <!--end CEO-->
                                        <!-- for DEO -->
                                      
                                        <!--end DEO-->
                                    </tr>
                                </thead>
                                <tbody> 
                                    @php  $count = 1; @endphp
                                    @forelse ($expenditureData as $item)
                                    <tr>
                                        <td>{{ $count }}</td> 
                                        <td>{{$item->PC_NAME }}</td>
                                        <td>{{$item->contensting_candiate }}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_of_declaration)) }}</td>
                                        <td>{{$item->last_date_prescribed_acct_lodge}}</td>
                                        <td>Returned</td> 
                                        <td>{{date('d,M,Y',strtotime($item->date_of_sending_additional_info)) }}</td>
                                        @if($user_data->role_id==5)
                                        <td>{{date('d,M,Y',strtotime($item->date_of_sending_deo)) }}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_of_receipt)) }}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_of_service)) }}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_of_sending_ack_eci)) }}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_of_receipt_represetation)) }}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_sending_supplimentary)) }}</td>
                                        @endif
                                        <!-- for ECI -->
                                        @if($user_data->role_id==7)
                                         <td>{{date('d,M,Y',strtotime($item->date_of_receipt)) }}</td> 
                                         <td>{{date('d,M,Y',strtotime($item->date_of_issuance_notice)) }}</td>
                                         <td>{{date('d,M,Y',strtotime($item->date_sending_supplimentary)) }}</td>
                                        <td>{{$item->final_action }}</td>
                                        @endif
                                        <!--end ECI-->
                                        <!-- for CEO -->
                                        @if($user_data->role_id==4)
                                        <td>{{$item->date_of_receipt }}</td>
                                        <td>{{$item->date_of_receipt_notice_service }}</td>
                                        <td>{{$item->current_status }}</td>
                                        @endif
                                        <!--end CEO-->
                                        
                                       
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
    $('#example').DataTable( {
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
</script>

<!--**********FORM VALIDATION ENDS*************-->
@endsection



