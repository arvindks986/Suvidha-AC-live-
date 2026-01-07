@extends('admin.layouts.pc.expenditure-theme')
@section('content')
<?php
   $namePrefix = \Route::current()->action['prefix'];
?>
<main role="main" class="inner cover mb-3">
  <!--FILTER STARTS FROM HERE-->
    <div class="card-header pt-3">

     <form method="get" action="{{url($namePrefix.'/tracking')}}" id="EciCustomReportFilter">
            <div class=" row">
                 <div class="col-sm-2">
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
                        <a href="{{url($namePrefix.'/tracking')}}"><input type="button" value="Reset Filter" name="Cancel" class="btn"></a></div></div>



            </div>
        </form>
    </div>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                 <!-- <div class="pull-right"><a href="{{url($namePrefix.'/CreateMisExpenseReport')}}">
                    <button type="button" class="btn btn-primary" >Add NEW</button></a>
                </div> -->
                <div class="card text-left" style="width:100%;">
                    <div class=" card-header">
                        <div class=" row d-flex align-items-center">
                            <div class="col"><h4> Total No. Of Contesting Candidates  - {{@$total_rec}}</h4></div> 
                            <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
 
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
                                        <th>Returned Type</th> 
                                        <th>Nature of Default AC</th>   
                                        <th>Date of Sending RO's Scrutiny Report to the ECI through the CEO</th>
                                        <th>Date of Seeking Additional Information</th> 
                                        <th>In case of default date of reciept of ECI notice</th>
                                        <th>Date of service of ECI notice</th>
                                        <th>Date of receipt of reply -cum-representation from the Candidate on ECI notice</th>
                                        <th>Date of Sending Supplementary Report on ECI Notice if any ,together of acknowledge from the RO</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @php  $count = 1; @endphp
                                    @forelse ($expenditureData as $item)
                                    <tr>
                                        <td>{{ $count }}</td>                                        
                                        <td>{{$item->PC_NAME }}</td>
                                        <td>{{$item->cand_name }} | {{$item->cand_email}}</td>
                                        <td>{{date('d,M,Y',strtotime($item->date_of_declaration)) }}</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
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



