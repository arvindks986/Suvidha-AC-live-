@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
<main role="main" class="inner cover mb-3 mb-auto">
    @if (Session::has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }} </div>
    @elseif(Session::has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
    </div>
    @endif
    @if(count($errors->error))
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.
        <br/>
        <ul>
            @foreach($errors->error->all() as $erro)
            <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                    <div class="card-header d-flex align-items-center">
                        <h3>Permission Details</h3>
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
                                <div class="col-sm-8">
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
                                    <p>Nill</p>

                                    @endif
                                </div>
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
							 @if(!empty($result->PC_NAME))
                            <div class="form-group row">
                               
                                <label class="col-sm-4 form-control-label">PC</label>
                                <div class="col-sm-8">
                                    <p>{{$result->PC_NAME}}</p>
                                </div>
                               
                            </div>
                              @endif
                             @if(!empty($allac_name))
                            <div class="form-group row">
                               
                                <label class="col-sm-4 form-control-label">AC</label>
                                <div class="col-sm-8">
                                    
                                 @foreach($allac_name as $ac)
                                    <p>{{$ac->AC_NAME}}</p>
                                    @endforeach
                                
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
                                    <p>{{GetReadableDateForm($result->date_time_start)}} {{'to'}} {{GetReadableDateForm($result->date_time_end)}}</p>
                                </div>
                            </div>




                        </form>
                    </div>


                    @endforeach
                    @endif


                </div>


                <!-- Recent Updates Widget          -->
                @php $i=0; @endphp
                @if(!empty($getNodaldetails))
                @foreach($getNodaldetails as $nodal)

                <div class="col-lg-12 pr-0 pl-0">
                    <div id="new-updates" class="card updates recent-updated">
                        <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                            <h5><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class="">Action Taken By Nodal Officer</a></h5><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class=""><i class="fa fa-angle-down"></i></a>
                        </div>

                        <div id="updates-box" role="tabpanel" class="collapse show" style="">
                            <div class="card-body getpermission ">
                                <div class="form-group row">
                                    <label class="col-sm-2">Nodal Officer Name</label>
                                    <p class="col-sm-4">{{$nodal->name}}</p>

                                    <label class="col-sm-2">Authority</label>
                                    <p class="col-sm-4">{{$nodal->auth_name}}</p>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2">Nodal Officer Remarks</label>
                                    @if(!empty($nodal->comment) && $nodal->comment != 'NULL')
                                    <p class="col-sm-4">{{$nodal->comment}}</p>
                                    @else
                                    <p class="text-info col-sm-4">No Remarks</p>
                                    @endif
                                    <label class="col-sm-2">Approved Status</label>
                                    @if($nodal->accept_status == 1)

                                    <p class="text-success col-sm-4">No Objection</p>

                                    @elseif($nodal->accept_status == 2)

                                    <p class="text-danger col-sm-4">Objection</p>

                                    @else

                                    <p class="text-info col-sm-4">Pending</p>

                                    @endif
                                </div>

                                <div class="form-group row">
                                    
                                    @if(!empty($nodal->file) && $nodal->file != 'NULL')
                                    <label class="col-md-2 ">Document Uploded by Nodal</label>
                                    @if($result->fileserver_dir == 'uploads')
                                    <a href="{{asset('uploads/Nodal-Uploaddocument')}}/{{$nodal->permission_request_id}}/{{$nodal->file}}" class="anchor col-md-4" download >Download Order Copy</a>
                                    @else
                                    <a href="{{asset('/')}}{{$nodal->file}}" class="anchor col-md-4" download >Download Order Copy</a>
                                    @endif
                                    @else
                                    <label class="col-sm-3 form-control-label">Document Uploded by Nodal</label>
                                    <p class="col-sm-4">Nill</p>

                                </div>
                            </div>

                            <div class="card-footer">
                                <form class="form-horizontal" id="formid{{$i}}" method="post" action="{{url('/roac/permission/uploadnodaldoc')}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div id="nodaldoc{{$i}}" style="display: none">
                                        <div class="form-group row">
                                            <label class="col-sm-5 form-control-label">Choose file to Upload</label>
                                            <div class="col-sm-7">
                                                <div class="file-select">
<!--                                                    <div class="file-select-name" id="noFile">No file chosen...</div> -->
                                                    <input type="hidden" value="{{$nodal->authority_id}}" name="auth_id"/>
                                                    <input type="hidden" value="{{$nodal->permission_request_id}}" name="p_req_id"/>
                                                    <input type="file" name="nodal-document" id="nodal-document{{$i}}" class="form-control mr-auto" accept=".pdf" style="height: 45px !important;" />

<!--                                                    <div class="file-select-button customchoose" id="fileName">Choose File</div>-->
                                                    <span class="text-danger">{{ $errors->error->first('nodal-document') }}</span>
                                                </div>
                                            </div>



                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <input type="submit" style="display: none" class="btn btn-success" >
                                            <button type="button" class="btn btn-primary float-right editnodal" data-formid='{{$i}}'  id='nodaledit{{$i}}' value="Edit" name="nodaledit">Edit Data</button>
                                            <button type="button"  style="display: none" class="btn btn-primary savenodal float-right " id="savenodal{{$i}}" name="savenodal" value="Save">Upload</button>
                                        </div>						
                                    </div>
                                </form>
                            </div>


                            @endif



                        </div>


                    </div>
                    <!-- Recent Updates Widget End-->


                </div>

            @php $i++; @endphp
            @endforeach
            @endif
            <!--            canddoc details-->
<!--@php $j=$i; @endphp
@if(!empty($canddoc))
@foreach($canddoc as $candata)
<div class="col-lg-12 pr-0 pl-0">
                    <div id="new-updates" class="card updates recent-updated">
                        <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                            <h5><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class="">Action Taken By Applicant</a></h5><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box" class=""><i class="fa fa-angle-down"></i></a>
                        </div>

                        <div id="updates-box" role="tabpanel" class="collapse show" style="">
                            <div class="card-body getpermission ">
                                <div class="form-group row">
                                    <label class="col-sm-2">Authority Name</label>
                                    <p class="col-sm-4">Applicant</p>

                                    <label class="col-sm-2">Authority</label>
                                    <p class="col-sm-4">Applicant</p>
                                </div>
                                 <div class="form-group row">
                                    <label class="col-sm-2">Authority Remarks</label>
                                    @if(!empty($candata->comment) && $candata->comment != 'NULL')
                                    <p class="col-sm-4">{{$candata->comment}}</p>
                                    @else
                                    <p class="text-info col-sm-4">No Remarks</p>
                                    @endif
                                    
                                    <label class="col-sm-2">Approved Status</label>
                                    @if($candata->accept_status == 1)

                                    <p class="text-success col-sm-4">No Objection</p>

                                    @elseif($candata->accept_status == 2)

                                    <p class="text-danger col-sm-4">Objection</p>

                                    @else

                                    <p class="text-info col-sm-4">Pending</p>

                                    @endif
                                 </div>

                                <div class="form-group row">
                                    
                                    @if(!empty($candata->file) && $candata->file != 'NULL')
                                    <label class="col-md-2 ">Document Uploded by Nodal</label>
                                     @if($result->fileserver_dir == 'uploads')
                                    <a href="{{asset('uploads/Nodal-Uploaddocument')}}/{{$candata->permission_request_id}}/{{$candata->file}}" class="anchor col-md-4" download >Download Order Copy</a>
                                    @else
                                    <a href="{{asset('/')}}{{$candata->file}}" class="anchor col-md-4" download >Download Order Copy</a>
                                    @endif
                                    @else
                                    <label class="col-sm-3 form-control-label">Document Uploded by Nodal</label>
                                    <p class="col-sm-4">Nill</p>

                                </div>
                            </div>

                            <div class="card-footer">
                                <form class="form-horizontal" id="formid{{$i}}" method="post" action="{{url('/roac/permission/uploadnodaldoc')}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div id="nodaldoc{{$i}}" style="display: none">
                                        <div class="form-group row">
                                            <label class="col-sm-5 form-control-label">Choose file to Upload</label>
                                            <div class="col-sm-7">
                                                <div class="file-select">
                                                    <div class="file-select-name" id="noFile1">No file chosen...</div> 
                                                    <input type="hidden" value="{{$candata->authority_id}}" name="auth_id"/>
                                                    <input type="hidden" value="{{$candata->permission_request_id}}" name="p_req_id"/>
                                                    <input type="file" name="nodal-document" id="nodal-document{{$i}}" class="form-control default-file-input" accept=".pdf" style="height: 45px !important;"/>

                                                    <div class="file-select-button customchoose" id="fileName">Choose File</div>
                                                    <span class="text-danger">{{ $errors->error->first('nodal-document') }}</span>
                                                </div>
                                            </div>



                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <input type="submit" style="display: none" class="btn btn-success" >
                                            <button type="button" class="btn btn-primary float-right editnodal" data-formid='{{$i}}'  id='nodaledit{{$i}}' value="Edit" name="nodaledit">Edit Data</button>
                                            <button type="button"  style="display: none" class="btn btn-primary savenodal float-right " id="savenodal{{$i}}" name="savenodal" value="Save">Upload</button>
                                        </div>						
                                    </div>
                                </form>
                            </div>


                            @endif



                        </div>


                    </div>
                     Recent Updates Widget End


                </div>
 @php $j++; @endphp
@endforeach
@endif-->
                <!--end canddoc details-->
            <div class="container">
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
                                    <label class="col-sm-4 form-control-label">Upload Order <span>*</span></label>
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
                                            <button class="btn btn-success float-right text-white" value="Accept" name="accept" id="accept">Accept</button>
                                            <button class="btn btn-warning float-right text-white" value="Reject" name="reject" id="reject">Reject</button>
                                            <button class="btn btn-danger float-right text-white" value="Cancel" name="cancel" id="cancel">Cancel</button>
                                            <!--                                            <button class="btn btn-primary float-right text-white">Download</button>-->
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>


                    </div>

                </div>
                <div class="col-lg-12 p-0">
                    <div class="line"></div>
                    <div class="form-group row text-center">
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-lg"><a href="{{url('/roac/permission/generate-pdf')}}/{{$result->permission_id}}{{'&'}}{{$result->location_id}}" style="color: white">Print</a></button>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

        </div>

    </section>

</main>
@endsection
@section('script')
<script type="text/javascript">
    $(function () {
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');
        $('.editnodal').on("click", function () {

            var id = $(this).attr('data-formid');

            //$('#nodaldetails #nodaldoc').css('display','');
            $('#nodaldoc' + id).css('display', '');
            $('#savenodal' + id).css('display', '');
            $('#nodaledit' + id).css('display', 'none');

            $('.savenodal').on("click", function () {
//            alert('ok');exit;
                var btn = $(this).attr('id');
                var filename = $('#nodal-document' + id).val().split('\\').pop();
//             var filename = $('#nodal-document').val();

                if (filename != '')
                {
                    var conf = confirm('Are you sure want to upload this document.');
                    if (conf == true)
                    {
                        $('#formid' + id).submit();
                    } else
                    {
                        return false;
                    }
                } else
                {
                    alert('Please choose file to upload.');
                }
            });
        });

        $('#accept').on('click', function () {
            var res = confirm('Are you sure you want accept this permission.')
            if (res == true)
            {
                return true;
            } else
            {
                return false;
            }
        });
        $('#reject').on('click', function () {
            var res = confirm('Are you sure you want reject this permission.')
            if (res == true)
            {
                return true;
            } else
            {
                return false;
            }
        });
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