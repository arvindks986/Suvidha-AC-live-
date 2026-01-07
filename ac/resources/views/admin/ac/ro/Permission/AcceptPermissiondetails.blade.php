@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">

    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card"> 
                            <div class="card-header d-flex align-items-center">
                                <h2>Permission Details</h2>
                                <h6 style="margin-left: auto;"> 
                            Approving Authority: {{$getDetails[0]->Approval_name}} 
                            @switch($getDetails[0]->Approval_name)
                                @case('CEO')
                                <small>(S-{{$getDetails[0]->ST_NAME}})</small>
                                @break
                                
                                @case('DEO')
                                <small>(D-{{$getDetails[0]->DIST_NAME}})</small>
                                @break
                                 @case('ROAC')
                              <small>(AC-{{$getDetails[0]->AC_NAME}})</small>
                               @break
                                
                                @default
                                <small>(AC-{{$getDetails[0]->AC_NAME}})</small>
                            @endswitch
                        </h6>
                            </div>				
                            @if(!empty($getDetails))
                            @foreach($getDetails as $result)
                            <div class="card-body getpermission">



                                <form class="form-horizontal">
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Reference ID</label>
                                        <div class="col-sm-8">
                                            @if(!empty($result->reference_id))
                                            <p>{{$result->reference_id}}</p>
                                            @else
                                            <p>{{$result->permission_id}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Name</label>
                                        <div class="col-sm-8">
                                            <p>{{$result->name}}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Address</label>
                                        <div class="col-sm-8">
                                            <p>{{$result->address}}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Mobile No	</label>
                                        <div class="col-sm-8">
                                            <p>{{$result->mobile}}</p>
                                        </div>
                                    </div>
                                    <!--                                    <div class="form-group row">
                                                                            <label class="col-sm-4 form-control-label">Epic No</label>
                                                                            @if(!empty($result->epic_no))
                                                                            <div class="col-sm-8">
                                                                                <p>{{$result->epic_no}}</p>
                                                                            </div>
                                                                            @else
                                                                            <div class="col-sm-8">
                                                                                <p>Nill--</p>
                                                                            </div>
                                                                            @endif
                                                                        </div>-->
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Permission Type</label>
                                        <div class="col-sm-8">
                                            <p>{{$result->pname}}</p>
                                        </div>
                                    </div>
                                            <div class="form-group row">
                                       <label class="col-sm-4 form-control-label">Party Name</label>
                                       <div class="col-sm-8">
                                           <p>{{$result->PARTYNAME}}</p>
                                       </div>
                                   </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Document uploaded by Applicant</label>
                                        @if(!empty($result->required_files) && ($result->required_files != 'NULL' && $result->required_files != 'null'))
                                        @php
                                        $docdata=explode(',',$result->required_files);
                                        @endphp
                                        @if(!empty($docdata))
                                        @for($i=0;$i < count($docdata); $i++)
                                        @if(!empty($docdata[$i]))
                                         @if($result->fileserver_dir == 'uploads')
                                            <small><a href="{{asset('uploads/userdoc/permission-document')}}/{{$docdata[$i]}}" download>Download Document</a></small><br>
                                            @else
                                               <small><a href="{{asset('/')}}{{$docdata[$i]}}" download>Download Document</a></small><br>
                                            @endif
                                        @endif
                                        @endfor
                                        @endif
                                        @else
                                        <div class="col-sm-8">
                                            <p>Nill</p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">State</label>
                                        <div class="col-sm-8">
                                            <p>{{$result->ST_NAME}}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">District</label>
                                        <div class="col-sm-8">
                                            <p>{{$result->DIST_NAME}}</p>
                                        </div>
                                    </div>
					@if(!empty($result->AC_NAME))
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">AC</label>
                                        <div class="col-sm-8">
                                            <p>{{$result->AC_NAME}}</p>
                                        </div>
                                    </div>
                                    @endif
                                     @if(!empty($allps_name))
                            <div class="form-group row">
                                 
                                <label class="col-sm-4 form-control-label">Police Station</label>
                                 
                                <div class="col-sm-8">
                                   
                                 @foreach($allps_name as $ps)
                                    <p>{{$ps->police_st_name}}</p>
                                    @endforeach
                               
                                </div>
                            </div>
                              @endif
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Location</label>
                                        @if(!empty($result->location_name))
                                        <div class="col-sm-8">
                                            <p>{{$result->location_name}}</p>
                                        </div>
                                        @else
                                        <div class="col-sm-8">
                                            <p>{{$result->Other_location}}</p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="form-group row">
                                <label class="col-sm-4 form-control-label">Submission Date &amp; Timing</label>
                                <div class="col-sm-8">
                                    <p>{{GetReadableDateForm($result->subdate)}}</p>
                                </div>
                            </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Date &amp; Timing</label>
                                        <div class="col-sm-8">
                                            <p>{{GetReadableDateForm($result->date_time_start)}} {{'to'}} {{ GetReadableDateForm($result->date_time_end)}}</p>
                                        </div>
                                    </div>




                                </form>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>


                <!-- Recent Updates Widget          -->
                @if(!empty($getNodaldetails))
                @foreach($getNodaldetails as $nodal)
                <div class="col-lg-12 pr-0">
                    <div id="new-updates" class="card updates recent-updated">
                        <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                            <h2 class="h5 display"><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class="">Action Taken By Nodal Officer</a></h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class=""><i class="fa fa-angle-down"></i></a>
                        </div>

                        <div id="updates-box" role="tabpanel" class="collapse show" style="">


                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Nodal Officer Name</label>
                                    <div class="col-sm-7">
                                        <p>{{$nodal->name}} </p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Authority Type</label>
                                    <div class="col-sm-7">
                                        <p>{{$nodal->auth_name}}</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Nodal Officer Remarks</label>
                                    @if(!empty($nodal->comment) && $nodal->comment != 'NULL')
                                    <div class="col-sm-7">
                                        <p>{{$nodal->comment}}</p>
                                    </div>
                                    @else
                                    <div class="text-info col-sm-7">
                                        <p>No Remarks</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Approved Status</label>
                                    @if($nodal->accept_status == 1)
                                    <div class="col-sm-7">
                                        <p class="text-warning">No Objection</p>
                                    </div>
                                    @elseif($nodal->accept_status == 2)
                                    <div class="col-sm-7">
                                        <p class="text-warning">Objection</p>
                                    </div>
                                    @else
                                    <div class="col-sm-7">
                                        <p class="text-warning">Pending</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Document Uploded by Nodal</label>
                                    @if(!empty($nodal->file) && $nodal->file != 'NULL')
                                    <div class="col-sm-7">
                                        @if($result->fileserver_dir == 'uploads')
                                    <a href="{{asset('uploads/Nodal-Uploaddocument')}}/{{$nodal->permission_request_id}}/{{$nodal->file}}" class="anchor col-md-4" download >Download Order Copy</a>
                                    @else
                                    <a href="{{asset('/')}}{{$nodal->file}}" class="anchor col-md-4" download >Download Order Copy</a>
                                    @endif
                                    </div>
                                    @else
                                    <div class="col-sm-7">
                                        <p>Nill</p>
                                    </div>
                                    @endif
                                </div> 
                            </div>
                        </div>

                    </div>
                    <!-- Recent Updates Widget End-->
                </div>
                @endforeach
                @endif
 
                <!-- ro -->
                @if(!empty($getRodetails))
                @foreach($getRodetails as $RO)
                <div class="col-lg-12 pr-0">

                    <!-- Recent Updates Widget          -->
                    <div id="new-updates" class="card updates recent-updated">
                        <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                            <h2 class="h5 display"><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class="">Action Taken By RO</a></h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class=""><i class="fa fa-angle-down"></i></a>
                        </div>

                        <div id="updates-box" role="tabpanel" class="collapse show" style="">
@if($RO->cancel_status ==0)
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">ARO Comment</label>
                                    <div class="col-sm-7">
                                        @if(!empty($RO->comment))
                                        <p>{{$RO->comment}}</p>
                                        @else
                                        <p>Pending</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Approved Status</label>
                                    @if(!empty($RO->approved_status))
                                    @if($RO->approved_status == 2)
                                    <div class="col-sm-7">
                                        <p class="text-warning">Accepted</p>
                                    </div>
                                    @elseif($RO->approved_status == 1)
                                    <div class="col-sm-7">
                                        <p class="text-warning">Inprogress</p>
                                    </div>
                                    @else
                                    <div class="col-sm-7">
                                        <p class="text-warning">Rejected</p>
                                    </div>
                                    @endif
                                    @else
                                    <p>Pending</p>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Document Uploded by RO</label>
                                    @if(!empty($RO->file) && $RO->file != 'NULL')
                                    <div class="col-sm-7">
                                        @if($result->fileserver_dir == 'uploads')
                                        <a href="{{asset('uploads/RO-Uploaddocument')}}/{{$RO->permission_request_id}}/{{$RO->file}}" download >Download Order Copy</a>
                                        @else
                                        <a href="{{asset('/')}}{{$RO->file}}" download >Download Order Copy</a>
                                        @endif
                                    </div>
                                    @else
                                    <div class="col-sm-7">
                                        <p>Nill</p>
                                    </div>
                                    @endif
                                </div> 
                            </div>
                            @else
                             <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">ARO Comment</label>
                                    <div class="col-sm-7">
                                        @if(!empty($RO->comment))
                                        <p>{{$RO->comment}}</p>
                                        @else
                                        <p>Pending</p>
                                        @endif
                                    </div>
                                </div>
                                 @if($RO->ro_cancel_status == 0)
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Approved Status</label>
                                    @if(!empty($RO->approved_status))
                                    @if($RO->approved_status == 2)
                                    <div class="col-sm-7">
                                        <p class="text-warning">Accepted</p>
                                    </div>
                                    @elseif($RO->approved_status == 1)
                                    <div class="col-sm-7">
                                        <p class="text-warning">Inprogress</p>
                                    </div>
                                    @else
                                    <div class="col-sm-7">
                                        <p class="text-warning">Rejected</p>
                                    </div>
                                    @endif
                                    @else
                                    <p>Pending</p>
                                    @endif
                                </div>
                                 @else
                                 <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Approved Status</label>
                                    <div class="col-sm-7">
                                        <p class="text-warning">Cancelled</p>
                                    </div>
                                </div>
                                 @endif
                                <div class="form-group row">
                                    <label class="col-sm-5 form-control-label">Document Uploded by RO</label>
                                    @if(!empty($RO->file) && $RO->file != 'NULL')
                                    <div class="col-sm-7">
                                        @if($result->fileserver_dir == 'uploads')
                                        <a href="{{asset('uploads/RO-Uploaddocument')}}/{{$RO->permission_request_id}}/{{$RO->file}}" download >Download Order Copy</a>
                                        @else
                                        <a href="{{asset('/')}}{{$RO->file}}" download >Download Order Copy</a>
                                        @endif
                                    </div>
                                    @else
                                    <div class="col-sm-7">
                                        <p>Nill</p>
                                    </div>
                                    @endif
                                </div> 
                            </div>
                            @endif
                        </div>

                        <!-- Recent Updates Widget End-->
                    </div>

                </div>
                @endforeach
                @endif
                @if(!empty($result))
                @if($result->role_id == '19')
                 @if($result->cancel_status == 0)
                <div class="col-lg-12 pl-0 pr-0">
                    @if(!empty($result->permission_id))
                    <div class="card mb-3">        
                        <div class="card-header d-flex align-items-center">
                            <h5 class="mr-auto">Action Taken By RO</h5> 
                        </div>	
                        <form class="form-horizontal" method="POST" action="{{url('/roac/permission/updateaction')}}" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <input type="hidden" value="{{$result->permission_id}}" name="p_id" />
                            <input type="hidden" value="{{$result->approved_status}}" name="ro_status" />
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Add Comment<span>*</span></label>
                                    <div class="col-sm-8">
                                        <textarea name="comment" id="" class="form-control" cols="3" placeholder="Comment here" rows="4" required></textarea>
                                        <span class="text-danger">{{ $errors->error->first('comment') }}</span>
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Upload Order<span>*</span></label>
                                    <div class="col-sm-8">
                                        <div class="file-select">
<!--                                            <div class="file-select-name" id="noFile">No file chosen...</div> -->
                                            <input type="file" class="form-control mr-auto" id="customFile" name="rofile"  accept=".pdf" style="height: 45px !important;" required />
<!--                                            <div class="file-select-button customchoose" id="fileName">Choose File</div>-->
                                            <span class="text-danger">{{ $errors->error->first('rofile') }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group row">
                                    <div class="col">
                                        <div class="btn-group float-right">
<!--                                            <button class="btn btn-success float-right text-white" value="Accept" name="accept" id="accept">Accept</button>
                                            <button class="btn btn-warning float-right text-white" value="Reject" name="reject" id="reject">Reject</button>-->
                                            <button class="btn btn-danger float-right text-white" value="Cancel" name="cancel" id="cancel">Cancel</button>
                                            <!--                                            <button class="btn btn-primary float-right text-white">Download</button>-->
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>


                    </div>
                    @endif
                </div>
                 @endif
                <div class="col-lg-12 p-0">
                    <div class="line"></div>
                    <div class="form-group row text-center">
                        <div class="col">
                            <button type="submit" class="btn btn-primary"><a href="{{url('/roac/permission/generate-pdf')}}/{{$result->permission_id}}{{'&'}}{{$result->location_id}}" style="color: white">Print</a></button>
                        </div>
                    </div>
                </div>
                @else
                @endif
               
                @endif
            </div>

    </section>

</main>
@endsection
@section('script')
<script type="text/javascript">
    $(function () {
        $('#cancel').on('click', function () {
            var res = confirm('Are you sure you want cancel this permission.')
            if (res == true)
            {
                return true;
            } else
            {
                return false;
            }
        });
    });
</script>
@endsection