@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate scrutiny Details')
@section('bradcome', 'Scrutiny Details')
@section('description', '')
@section('content') 
<?php
$st = getstatebystatecode($user_data->st_code);
$distname = getdistrictbydistrictno($user_data->st_code, $user_data->dist_no);  
 $last_date_prescribed_acct_lodge = !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 30 days ')):'';
  

?>
<style type="text/css">
       textarea#definalization_reason {
    border: 1px solid #6666;
    border-radius: 2px;
    height: 100px;
}
#definalized_error{    color: red;
    font-size: 15px;}
</style>
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
                        <form enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('acdeo/constiuancyinfo') }}" >
                            {{ csrf_field() }}
                            <div class="card-body"> 
                                <div class="row">
                                   
                                    <div class="col-sm-12"><label for="Constiuancy"><b style="text-align: center;">You Can Fill Scrutiny Report From 09-01-2023</b><sup></sup></label></div>
                                   


                                    
                                   
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
                                        <b></b> 
                                        <a href="{{url('/acdeo/scrutinyExpenditure')}}"><button type="submit" class="btn btn-primary"><font color="black">Back</font></button></a>

                                    </p></div>
                            </div><!-- end row-->
                        </div><!-- end card-header-->
                       <div class="card-body">  
                        
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
                    <h6 class="modal-title" id="myModalLabel"><center>Scrutiny Report Definalization limit reached at DEO level.</center></h6>
                </div>
                <div class="modal-footer mb-2">
                    <input type="button" value="Ok" id="" class="btn btn-primary mt-2" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>
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


$(document).on('click', '#changeStatus', function (e) {
    var candidate_id = $(this).val();
    $('#definalizedreport').val(candidate_id)
    $('#myModalcheck').modal('show');
  });

  
 $(document).on('click', '#definalized', function (e) {
    var candidate_id = $('#definalizedreport').val();
    var reason = $("#definalization_reason").val();
    if($.trim(reason).length>0){
    jQuery.ajax({
    url: "{{url('/acdeo/updateStatusReport')}}",
            type: 'GET',
            data: {candidate_id: candidate_id,reason:reason},
            success: function (result) {
                result = result.trim();
                if(result=="1")
                {
          $('#myModalcheck').modal('hide');
          $('#definalized_error').css('display','none');
                  $('#myModaldefi').modal('show');
                  setTimeout(function() {
              location.reload();
          }, 5000);
                }
            }
    });
  }
  else
  {
    $("#definalized_error").text("Please give reason for definalization of candidate.");
  }


    
    
    });
$(document).on('click', '#count_by_ceo', function (e) {
    $('#count_by_ceo_count_modal').modal('show');
  });
</script>
@endsection