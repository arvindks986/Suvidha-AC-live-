@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate scrutiny Details')
@section('bradcome', 'Scrutiny Details')
@section('description', '')
@section('content') 
<?php
$st = getstatebystatecode($user_data->st_code);
$distname = getdistrictbydistrictno($user_data->st_code, $user_data->dist_no);

//dd($pcdetails);
?>
<main role="main" class="inner cover mb-3">
    <?php if (empty($candList)) { ?> 
        <section>	 
            <div class="container">
                <div class="row">
                    <div class="card text-left" style="width:100%; margin:0 auto;">
                        <div class=" card-header">
                            <div class=" row">
                                <div class="col"><h4>Scrutiny  Details</h4></div> 
                                <div class="col"><p class="mb-0 text-right">
                                        <b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                                        <b>Dist Name</b><span class="badge badge-info">{{$distname->DIST_NAME}}</span>&nbsp;&nbsp;  </p></div>
                            </div> <!-- end col-->
                        </div><!-- end row-->
                        <form enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('acdeo/reports') }}" >
                            {{ csrf_field() }}
                            <div class="card-body"> 
                                <div class="row">
                                    <input type="hidden" name="st_code" value="{{$user_data->st_code}}">
                                    <input type="hidden" name="dist_no" value="{{$user_data->dist_no}}">
                                    <div class="col-sm-3"><label for="Constiuancy">Constiuancy<sup>*</sup></label></div>
                                    <div class="col">
                                        <div class="" style="width:100%;">
                                            <select name="ac" class="consttype form-control" >
                                                <option value="">-- Select AC --</option>
                                                @foreach($all_ac as $getAc)
                                                <option value="{{ $getAc->AC_NO }}" > 
                                                    {{$getAc->AC_NO }} - {{$getAc->AC_NAME }}
                                                </option>
                                                @endforeach 
                                            </select>
                                            @if ($errors->has('ac'))
                                            <span style="color:red;">{{ $errors->first('ac') }}</span>
                                            @endif
                                            <div class="pcerrormsg errormsg errorred"></div>
                                        </div>
                                    </div> 


                                    <div class="col-sm-3"><label for="year">Year <sup>*</sup></label></div>
                                    <div class="col"><div class="" style="width:100%;">
                                            <select name="year" class="consttype form-control" >
                                                <option value="2019">2019</option>
                                                <option value="2020">2020</option>
                                                <option value="2021">2021</option>
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row float-right">       
                                        <div class="col">
                                            <button type="submit" id="constiuancyinfo" name="constiuancyinfo" value="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>	
                                </div><!-- end row-->
                            </div><!-- end card-body-->
                        </form>
                    </div>
                </div>
            </div>
            </div>
            </div>	  
        </section>
        <?php
    } else {
        $acdetails = getacbyacno($user_data->st_code, $ac_no);
        //dd($acdetails);
		if(!empty($acdetails)){
        ?> 
        <section class="mt-5">
            <div class="container-fluid">
                <div class="row">
                    <div class="card text-left" style="width:100%; margin:0 auto;">
                        <div class=" card-header">
                            <div class=" row">
                                <div class="col"><h2 class="mr-auto">Scrutiny Details</h2></div> 
                                <div class="col"><p class="mb-0 text-right">
                                        <b>State Name:</b> 
                                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                                        <b>District:</b><span class="badge badge-info">{{$distname->DIST_NAME}}</span>&nbsp;&nbsp; 
                                        <b>AC:</b> <span class="badge badge-info">{{ $acdetails->AC_NAME}}</span>
                                        <b></b> <button type="submit" class="btn btn-primary"><a href="{{url('/acdeo/scrutinyExpenditure')}}"><font color="black">Back</font></a></button>
                                    </p></div>
                            </div><!-- end row-->
                        </div><!-- end card-header-->
                       <div class="card-body">  
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Candidate Name</th>

<!--          <th>Candidate Name In Hindi</th>
--><!--          <th>Candidate Father Name</th>
                                        -->          <th>Election Year/Type</th>
                                        <!-- <th>Election Type</th> -->
                              <!--           <th>View</th> 
                                        -->        
                                        <th>Last Date of Submission</th> 
                                        <th>Date of Scrutiny Report Submission</th>
                                        <th>Date of Lodging A/C By Candidate</th>
                                        <th>Date of Sending to the CEO</th>
                                        <th>Date of Receipt By CEO</th>
                                        
                                        <th > Action </th>   
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                 <tbody>
                                <?php $j = 0; ?>
                                @if(!empty($candList))
                                @foreach($candList as $candDetails)  
                                <?php
                                // dd($candDetails);
                                $j++;
                                ?>
                                <tr>
                                    <td><a href="javascript:void(0)" onclick="getProfile('{{$candDetails->candidate_id}}')" >@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif </a></td>
                                    <td>@if(!empty($candDetails->YEAR)) {{$candDetails->YEAR}} @endif/@if(!empty($candDetails->ELECTION_TYPE)) {{$candDetails->ELECTION_TYPE}} AC @endif</td>
                                 
                                    <td>
                                        22-06-2019
                                    </td>
                                    <td>
                                        <?php echo!empty($candDetails->finalized_date) ? date('d-m-Y', strtotime($candDetails->finalized_date)) : 'N/A' ?>
                                    </td>
                                    <td><?php echo!empty($candDetails->date_orginal_acct) ? date('d-m-Y', strtotime($candDetails->date_orginal_acct)) : 'N/A' ?></td>
                                    <td><?php echo!empty($candDetails->date_of_sending_deo) ? date('d-m-Y', strtotime($candDetails->date_of_sending_deo)) : 'N/A' ?></td>
                                    <td><?php echo!empty($candDetails->date_of_receipt) ? date('d-m-Y', strtotime($candDetails->date_of_receipt)) : 'N/A' ?></td>
                                   @if($candDetails->final_by_ro=="1")
                                    
                                    <td>
                                        <!--@if(!empty($candDetails->date_of_declaration) && strtotime($candDetails->date_of_declaration)>0)-->
                                        <a href="{{url('/')}}/acdeo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-60" target="_blank">Report</a> 
                                        
                                         <!--@endif-->
                                        <!--<a href="javascript:void(0)" class="btn btn-info btn-sm width-60" onclick="getStatus('{{$candDetails->candidate_id}}')">Status</a>--> 
                                         
                                        <a href="{{url('/')}}/acdeo/view/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-secondary btn-sm width-60" >View</a>
                                    </td>
                                    @else
                                    <td>N/A</td>
                                    @endif
                                    <td>
                                        @if(empty($candDetails->date_of_declaration))
                                        <a href="{{url('/')}}/acdeo/deoformview/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-warning btn-sm width-90 text-white ">Not-Started</a>
                                        @elseif(!empty($candDetails->date_of_declaration) && strtotime($candDetails->date_of_declaration)>0 && $candDetails->finalized_status=="0")
                                        <a href="{{url('/')}}/acdeo/deoformview/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-success btn-sm width-90 text-white ">In progress</a>
                                        @elseif($candDetails->final_by_ro=="0" && $candDetails->finalized_status=="1")
                                        <a href="{{url('/acdeo/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-90 text-white ">Update Info</a>
                                        @elseif($candDetails->final_by_ro=="1")
                                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm width-90 text-white ">Finalized</a>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach 
                                @endif 
                               
                                </tbody>
                            </table>
                        </div> <!-- end responcive-->
                    </div> <!-- end card-body-->
                    </div>
                </div>
            </div>
            </div>
        </section>
		<?php  } } ?> 
</main>

<!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;      ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Profile</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
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
        </div>

    </div>
</div>
<!-- ProfileRO-->
<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<script type="text/javascript">


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
                                            // end profile ECI pop up
</script>
<!--graph implementation start here-Manoj -->
<script>
    jQuery(document).ready(function () {
//Check Validation
    jQuery('#constiuancyinfo').click(function () {
    var acname = jQuery('select[name="ac"]').val();
    if (acname == '') {
    jQuery('.errormsg').html('');
    jQuery('.pcerrormsg').html('Please select ac');
    jQuery("input[name='ac']").focus();
    return false;
    }
    });
    });

</script>
@endsection