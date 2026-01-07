@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<?php
//use \App\Http\Controllers\Admin\ECIReportSearch;
?>
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">
        <style type="text/css">
        hr {
            margin-top: 5px !important;
            margin-bottom: 5px !important;
        }
        </style>
        <div class="container-fluid">
             <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Search by applicant mobile number / permission Id / nodal officer number</h5>
                </div>
            </div>
            <form name="report" method="post" action="{{ url('eci/permissionreportssearch') }}"> 
            	@csrf
	            <div class="row">
	                <div class="col-sm-10 row">
	                    <label for="state" class="col-sm-4 col-form-label">Search</label>
	                    <div class="col-sm-4 distt">
	                        <select name="search_by" id="search_by" class="form-control">
                                <option value="0">-- Please select --</option>         
                                <option value="1">Applicant/Permission Id</option>
                                <option value="2">Nodal Office Mobile</option>
                            </select>
	                    </div>

	                    <div class="col-sm-4 distt">
	                        <input type="text" name="search" id="search" class="form-control" placeholder="Search by applicant mobile number/permission Id/nodal officer number">
	                    </div>
	                </div>
	                <div class="col-sm-1  row">
	                    <input type="submit" value="Submit" name="submit" class="btn btn-primary getdata">
	                </div>
	            </div>
        	</form>
        	<?php if($search_by==1){?>
        	<div class="row">
                <div class="col-sm-12 mt-2 table-responsive">
                    <table id="list-table" class="table table-striped table-bordered table-hover" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Applicant Name</th>
                                <th>Mobile</th>
                                <th>Party Name</th>
                                <th>Reference Id</th>
                                <th>Permission Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>AC</th>
                                <th>Applicant Date</th>
                                <th>Status</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $simple =''; ?>
                            @forelse($results as $key => $list)
                            <?php $simple = $list->userCreatedBy; ?>
                            <?php 
                            if($list->approved_status=='0'):
                               $approved_status = '<span class="text-info">Pending</span>';
                            elseif($list->approved_status=='1'):
                                $approved_status = '<span class="text-info">In Process</span>';
                            elseif($list->approved_status=='2'):
                                $approved_status = '<span class="text-success">Accepted</span>';
                            elseif($list->approved_status=='3'):
                                $approved_status = "<span class='text-danger'>Rejected</span>";
                            else:
                                $approved_status = "<span class='text-danger'><strong>Error.</strong></span>";
                            endif;

                            if($list->cancel_status==1):
                                $cancel_status = '<span class="text-danger">Cancelled</span>';
                            else:
                                $cancel_status = '<span class="text-danger"></span>';
                            endif;

                            if($list->permission_type_role_id==19):
                                $officername = App\Http\Controllers\Admin\ECIReportSearch\ECIReportSearchController::getOfficerNameWithAC($list->permisssionState,$list->permisssionDist,$list->permisssionAC,$list->permission_type_role_id);

                            elseif($list->permission_type_role_id==5):
                                $officername = App\Http\Controllers\Admin\ECIReportSearch\ECIReportSearchController::getOfficerNameWithDist($list->permisssionState,$list->permisssionDist,$list->permission_type_role_id);
                            
                            elseif($list->permission_type_role_id==4):
                                $officername = App\Http\Controllers\Admin\ECIReportSearch\ECIReportSearchController::getOfficerNameWithState($list->permisssionState,$list->permission_type_role_id);
                            else:
                                $officername = '<span class="text-danger">ERROR!</span>';
                            endif;
                            ?>
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $list->name }}</td>
                                <td>{{ $list->mobile }}</td>
                                <td>{{ $list->partyname }}</td>
                                <td>{{ $list->reference_id }}</td>
                                <td>{{ $list->pname }}</td>
                                <td>{{ $list->ST_NAME }}</td>
                                <td>{{ $list->DIST_NAME }}</td>
                                <td>{{ $list->AC_NAME }}</td>
                                <td>{{ Carbon\Carbon::parse($list->date_time_start)->format('d-m-Y') }}</td>
                                <td> <?= $approved_status ?> </td>
                                <td><a class="show_details btn btn-info btn-sm" data-reference_id="{{ $list->reference_id }}" data-subdate="{{ Carbon\Carbon::parse($list->subdate)->format('d-m-Y H:i:s') }}" data-show_applicant_name="{{ $list->name }}" data-mobile="{{ $list->mobile }}" data-partyname="{{ $list->partyname }}" data-pname="{{ $list->pname }}" data-ST_NAME="{{ $list->ST_NAME }}" data-DIST_NAME="{{ $list->DIST_NAME }}" data-AC_NAME="{{ $list->AC_NAME }}" data-date_time_start="{{ Carbon\Carbon::parse($list->date_time_start)->format('d-m-Y') }}" data-date_time_end="{{ Carbon\Carbon::parse($list->date_time_end)->format('d-m-Y') }}" data-cancel_status="{{ $cancel_status }}" data-approved_status="{{ $approved_status }}" data-comment="{{ $list->comment }}"data-assignedoffice_name="{{ $list->assignedoffice_name }}" data-officername="{{ $officername }}"  data-userCreatedBy="{{ $list->userCreatedBy }}" data-target="#myModaledit" data-toggle="modal"><i class="fas fa-pencil-alt"></i>View</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="11" style="text-align: center; color: red">No record found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } else if($search_by==2) {?>
        	<div class="row">
                <div class="col-sm-12 mt-2 table-responsive">
                    <table id="list-table" class="table table-striped table-bordered table-hover" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Nodal Name</th>
                                <th>Mobile</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Email</th>
                                <th>State</th>
                                <th>District</th>
                                <th>AC</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $simple =''; ?>
                            @forelse($results as $key => $list)
                            <?php $simple = $list->userCreatedBy; ?>
                            <?php 
                            if($list->approved_status=='0'):
                               $approved_status = '<span class="text-info">Pending</span>';
                            elseif($list->approved_status=='1'):
                                $approved_status = '<span class="text-info">In Process</span>';
                            elseif($list->approved_status=='2'):
                                $approved_status = '<span class="text-success">Accepted</span>';
                            elseif($list->approved_status=='3'):
                                $approved_status = "<span class='text-danger'>Rejected</span>";
                            else:
                                $approved_status = "<span class='text-danger'><strong>Error.</strong></span>";
                            endif;

                            if($list->cancel_status=='1'):
                                $cancel_status = '<span class="text-danger">Cancelled</span>';
                            else:
                                $cancel_status = '<span class="text-danger">N/A</span>';
                            endif;

                            if($list->permission_type_role_id==19):
                                $officername = App\Http\Controllers\Admin\ECIReportSearch\ECIReportSearchController::getOfficerNameWithAC($list->permisssionState,$list->permisssionDist,$list->permisssionAC,$list->permission_type_role_id);

                            elseif($list->permission_type_role_id==5):
                                $officername = App\Http\Controllers\Admin\ECIReportSearch\ECIReportSearchController::getOfficerNameWithDist($list->permisssionState,$list->permisssionDist,$list->permission_type_role_id);
                            
                            elseif($list->permission_type_role_id==4):
                                $officername = App\Http\Controllers\Admin\ECIReportSearch\ECIReportSearchController::getOfficerNameWithState($list->permisssionState,$list->permission_type_role_id);
                            else:
                                $officername = '<span class="text-danger">ERROR!</span>';
                            endif;
                            ?>
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $list->nodal_name }}</td>
                                <td>{{ $list->nodal_mobile }}</td>
                                <td>{{ $list->nodal_department }}</td>
                                <td>{{ $list->nodal_designation }}</td>
                                <td>{{ $list->nodal_email }}</td>
                                <td>{{ getstatebystatecode($list->nodal_st)->ST_NAME }}</td>
                                <td>{{ getdistrictbydistrictno($list->nodal_st,$list->nodal_dist)->DIST_NAME }}</td>
                                <td>{{ getacbyacno($list->nodal_st,$list->nodal_ac_no)->AC_NAME }}</td>
                                <td><a class="show_details btn btn-info btn-sm" data-reference_id="{{ $list->reference_id }}" data-subdate="{{ Carbon\Carbon::parse($list->subdate)->format('d-m-Y H:i:s') }}" data-show_applicant_name="{{ $list->name }}" data-mobile="{{ $list->mobile }}" data-partyname="{{ $list->partyname }}" data-pname="{{ $list->pname }}" data-ST_NAME="{{ $list->ST_NAME }}" data-DIST_NAME="{{ $list->DIST_NAME }}" data-AC_NAME="{{ $list->AC_NAME }}" data-date_time_start="{{ Carbon\Carbon::parse($list->date_time_start)->format('d-m-Y') }}" data-date_time_end="{{ Carbon\Carbon::parse($list->date_time_end)->format('d-m-Y') }}" data-cancel_status="{{ $cancel_status }}" data-approved_status="{{ $approved_status }}" data-comment="{{ $list->comment }}" data-assignedoffice_name="{{ $list->assignedoffice_name }}" data-officername="{{ $officername }}"  data-userCreatedBy="{{ $list->userCreatedBy }}" data-target="#myModaledit" data-toggle="modal"><i class="fas fa-pencil-alt"></i>View</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="11" style="text-align: center; color: red">No record found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } else { ?>
        	<div class="row">
                <div class="col-sm-12 mt-2 table-responsive">
                    <table id="list-table" class="table table-striped table-bordered table-hover" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Applicant Name</th>
                                <th>Mobile</th>
                                <th>Party Name</th>
                                <th>Reference Id</th>
                                <th>Permission Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>AC</th>
                                <th>Applicant Date</th>
                                <th>Status</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<tr><td colspan="11" style="text-align: center; color: red">No record found</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
         </div>
    </section>
 </main>
 <div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
          <div class="modal-header">
            <h5 class="modal-title">Show Detail</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <!--end modal-header-->
           <form>
			    <div class="modal-body">
                    
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="control-label label-margin">Applied on</label>
                        </div>
                        <div class="col-md-6"><span id="show_subdate"></span></div>
                    </div>
                    <div class="row mb-2" id="show_assignedoffice_name_div">
                        <div class="col-md-6">
                            <label class="control-label label-margin">Assigned</label>
                        </div>
                        <div class="col-md-6"><span id="show_assignedoffice_name"></span></div>
                    </div>
                    <div class="row mb-2" id="show_officername_div">
                        <div class="col-md-6">
                            <label class="control-label label-margin">Assigned</label>
                        </div>
                        <div class="col-md-6"><span id="show_officername"></span></div>
                    </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Applicant Name</label>
			            </div>
			            <div class="col-md-6"><span id="show_applicant_name"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Mobile</label>
			            </div>
			            <div class="col-md-6"><span id="show_mobile"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Party Name</label>
			            </div>
			            <div class="col-md-6"><span id="show_partyname"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Reference Id</label>
			            </div>
			            <div class="col-md-6"><span id="show_reference_id"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Permission Name</label>
			            </div>
			            <div class="col-md-6"><span id="show_pname"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">State Name</label>
			            </div>
			            <div class="col-md-6"><span id="show_st_name"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">District Name</label>
			            </div>
			            <div class="col-md-6"><span id="show_dist_name"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">AC Name</label>
			            </div>
			            <div class="col-md-6"><span id="show_ac_name"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Start Date</label>
			            </div>
			            <div class="col-md-6"><span id="show_date_time_start"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">End Date</label>
			            </div>
			            <div class="col-md-6"><span id="show_date_time_end"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Approved Status</label>
			            </div>
			            <div class="col-md-6"><span id="show_approved_status"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Cancel Status</label>
			            </div>
			            <div class="col-md-6"><span id="show_cancel_status"></span></div>
			        </div>
                    <hr>
			        <div class="row mb-2">
			            <div class="col-md-6">
			                <label class="control-label label-margin">Comment</label>
			            </div>
			            <div class="col-md-6"><span id="show_comment"></span></div>
			        </div>

			        <div class="modal-footer">
			            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        </div>
			    </div>
			</form>

        </div>
    </div>
</div>
<script>
var simple = '<?= @$simple; ?>';

$('#list-table').on('click','.show_details',function(){
	var reference_id  			= $(this).data('reference_id');
    var assignedoffice_name     = $(this).data('assignedoffice_name');
    var officername             = $(this).data('officername');
    var show_subdate            = $(this).data('subdate');
	var show_applicant_name  	= $(this).data('show_applicant_name');
	var mobile  				= $(this).data('mobile');
	var partyname  				= $(this).data('partyname');
	var pname  					= $(this).data('pname');
	var st_name  				= $(this).data('st_name');
	var dist_name  				= $(this).data('dist_name');
	var ac_name  				= $(this).data('ac_name');
	var date_time_start  		= $(this).data('date_time_start');
	var date_time_end  			= $(this).data('date_time_end');
	var cancel_status  			= $(this).data('cancel_status');
	var approved_status  		= $(this).data('approved_status');
	var comment  				= $(this).data('comment');
    var usercreatedby           = $(this).data('usercreatedby');

    if(simple=='1'){
        $("#show_assignedoffice_name_div").hide();
        $("#show_officername_div").show();
    } else if(simple=='2'){
        $("#show_officername_div").hide();
        $("#show_assignedoffice_name_div").show();
    } else {
        alert('something went wrong please try again.');
    }

	$('#myModaledit').modal('show');
	$('#show_reference_id').html(reference_id);
    $('#show_assignedoffice_name').html(assignedoffice_name);
    $('#show_officername').html(officername);
    $('#show_subdate').html(show_subdate);
	$('#show_applicant_name').html(show_applicant_name);
	$('#show_mobile').html(mobile);
	$('#show_partyname').html(partyname);
	$('#show_pname').html(pname);
	$('#show_st_name').html(st_name);
	$('#show_dist_name').html(dist_name);
	$('#show_ac_name').html(ac_name);
	$('#show_date_time_start').html(date_time_start);
	$('#show_date_time_end').html(date_time_end);
	$('#show_cancel_status').html(cancel_status);
	$('#show_approved_status').html(approved_status);
	$('#show_comment').html(comment);
});
</script>
 @endsection