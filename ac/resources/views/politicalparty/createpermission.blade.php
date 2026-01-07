@extends('layouts.theme')
@section('title', 'Permission')
@section('content')
<?PHP // print_r($user_details_location);die;?>
<style type="text/css">
h4 {
    color: #ffffff !important;
    padding: 0.5em !important;
}

.dataTables_filter {
    display: none;
}

.odd {
    display: none;
}
</style>

<style>
/* Always set the map height explicitly to define the size of the div
* element that contains the map. */
#dvMap {
    height: 300px;
    width: 100%;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
    height: 100%;
    margin: 0;
    padding: 0;
}
</style>
<section class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3>Apply Permission </h3>
                    </div>
                    @if(session::has('message'))
                    <div class="alert alert-danger">
                        {{session()->get('message')}}
                    </div>
                    @endif

                    @if(session::has('msg'))
                    <div class="alert alert-danger">
                        {{session()->get('msg')}}
                    </div>
                    @endif
                    @if($errors->any())
                    <div class="alert alert-danger">{{$errors->first()}}</div>
                    @endif
                    <div class="card-body tabular-pane">


                        <div class="row">
                            <div class="col">
                                <form class="form-horizontal" method="post" action="{{url('/Applypermission')}}"
                                    enctype="multipart/form-data" autocomplete="off" id="permission"
                                    onsubmit="return checkForm(this);">
                                    {{ csrf_field() }}
                                    @if(count($user_details)>0)
                                    @foreach ($user_details as $key=>$rosuper_list)
                                    <!--  -->
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Applicant Type</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="n"
                                                        value="{{$users=Session::get('Applicant_type')}}" readonly>
                                                    <!-- <input type="text" class="form-control" placeholder="Enter Name"> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Political Party /
                                                    Independent</label>
                                                <div class="col-sm-8">
                                                    <select name="party_master" class="form-control">
                                                        <option value="{{$rosuper_list->CCODE}}">
                                                            {{$rosuper_list->PARTYNAME}}</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  -->
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Name</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" class="form-control"
                                                        value="{{$rosuper_list->user_login_id}}" name="userid" readonly>
                                                    <input type="text" class="form-control" id="n"
                                                        value="{{$rosuper_list->name}}" name="name" readonly>
                                                    <!-- <input type="text" class="form-control" placeholder="Enter Name"> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Email ID
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" class="form-control"
                                                        value="{{$rosuper_list->election_id}}" name="election_id"
                                                        readonly>

                                                    <input type="text" class="form-control" placeholder="Enter Email ID"
                                                        value="{{$rosuper_list->email}}" name="email" readonly required pattern="[^@\s]+@[^@\s]+\.[^@\s]+">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">State
                                                </label>
                                                <div class="col-sm-8">
                                                    <select name="state" id="state" class="form-control">
                                                        <option value="{{$rosuper_list->ST_CODE}}">
                                                            {{$rosuper_list->ST_NAME}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Mobile No
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control"
                                                        value="{{$rosuper_list->mobileno}}" name="mobile" id="m"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <hr />
                                    <div class="row">
                                        <div class="col">
                                            <h5>Details of Applied for</h5>
                                        </div>
                                    </div>
                                    <hr />

                                    <div class="row">

                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Permission Type
                                                    <sup style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <select name="permission_type" id="selectprmsn"
                                                        class="form-control">
                                                        <option value="">Select Permission Type</option>
                                                        @if(count($permission_type)>0)
                                                        @foreach ($permission_type as $key=>$rosuper_list)
                                                        <option
                                                            value="{{$rosuper_list->permsn_id}}{{'#'}}{{$rosuper_list->permission_type_id}}{{'#'}}{{$rosuper_list->restriction_day}}">
                                                            {{$rosuper_list->permission_name}}</option>
                                                        @endforeach
                                                        @else
                                                        <!-- <option >Permission Type Is Not Available</option> -->

                                                        @endif
                                                    </select>
                                                    <span
                                                        class="text-danger">{{ $errors->first('permission_type') }}</span>
                                                </div>


                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group row" id="districtmsg" style="display: none;">
                                                <label class="col-sm-4 form-control-label">District<sup
                                                        style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <select name="district" id="district" class="form-control">
                                                        <option value=""> Select District</option>
                                                    </select>
                                                    <span class="text-danger">{{ $errors->first('district') }}</span>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row" id="assembly" style="display:none;">
                                        <div class="col" id="acintra">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">AC<sup
                                                        style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <select name="ac" id="ac" class="form-control">
                                                        <option value=""> Select AC</option>
                                                    </select>
                                                    <span class="text-danger">{{ $errors->first('ac') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col" id="polic" style="display:none">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Police Station
                                                    <sup style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <select name="police_station" id="ps" class="form-control">
                                                        <option value="">Select Police Station</option>

                                                    </select>
                                                    <span
                                                        class="text-danger">{{ $errors->first('police_station') }}</span>
                                                    <p style="color:red;display: none" id="police-comment">Police master
                                                        data has not beem updated!</p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" id="permsn_doc">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Event Start Date & Time<sup
                                                        style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control datetimepicker"
                                                        id="datetimepicker" data-format="MM/dd/yyyy HH:mm:ss PP"
                                                        name="start">
                                                    <span class="text-danger">{{ $errors->first('start') }}</span>
                                                    <p style="color:red" id="date-comment">Permission to be applied 48
                                                        hour before !</p>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">End Date & Time <sup
                                                        style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control datetimepicker trdtdstrpickrt"
                                                        id="datetimepicker1" data-format="MM-dd-yyyy HH:mm:ss PP"
                                                        name="end">
                                                    <span class="text-danger">{{ $errors->first('end') }}</span>
                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col" id="event" style="display:block">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Event Place<sup
                                                        style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <select name="location1" id="location1" class="form-control">
                                                        <option value="">Select Location</option>


                                                        <option value="other">Add More Location</option>
                                                    </select>
                                                    <span class="text-danger">{{ $errors->first('location1') }}</span>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group row" id="other" style="display:none;">
                                                <label class="col-sm-4 form-control-label">Add Location's<sup
                                                        style="color:red">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="other"
                                                        placeholder="Enter event place">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  -->
                                    <div class="row">
                                        <div class="col" id="event" style="display:block">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Poll Day<sup
                                                        style="color:red">*</sup></label>
                                                <div class="col-sm-8" id="poll">

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group row" id="other" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                    <!--  -->
                                    @endforeach
                                    @endif
                                    <!-- <div class="row">
                                        <div class="col">
                                            <div id="dvMap"></div>

                                        </div>

                                    </div> -->
                                    <div class="form-group float-right">
                                        <!--	                    	<input type="submit" class="btn btn-primary btn-lg" id="disabled_button" value="Submit">    -->
                                        <button type="submit" class="btn btn-primary text-center" name="AddPS"
                                            value="Save" id="disabled_button">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                </div>


            </div>
        </div>

    </div>


</section>
<!--modal start-->
<div class="modal fade" id="perm-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Permission Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Permission module has not been enabled!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <!--        <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div>
    </div>
</div>
<!--end-->
@endsection

@section('script')

<!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4mlo-kY0vyBDIdeXffR2igqE5igx3piE&callback=LoadMap"></script>-->
<script>
$(document).ready(function() {
    var rstatus;
    var get_pollday;
    var max = "";
    var newd = "";
    var serverDate = "<?php echo date('Y-m-d H:i:s'); ?>";
    console.log(serverDate);
    var date = new Date(serverDate);
    console.log(date);
    max = new Date(serverDate);
    console.log(max);
    var datetimepicker1 = null;
   max.setDate(max.getDate() + 3);
        date.setDate(date.getDate() + 2);
        $('#selectprmsn').on('click', function() {  
            if (datetimepicker1) {
                $('#datetimepicker').data("DateTimePicker").destroy(); 
            }

        });
        $('#datetimepicker1').on('click', function() {
            if (datetimepicker1) {
                $('#datetimepicker1').data("DateTimePicker").destroy(); 
            }
             
            var idperm = $('#selectprmsn').val();
            //console.log('idperm',idperm);

            if (rstatus == 0 && parseInt(get_ptypid) >  '3') {
                var ptypid = idperm.split('#');
                var get_ptypid = ptypid[2];

                if (get_ptypid != '') {
                    var get_day = get_ptypid;
                } else {
                    var get_day = '7';
                }

                var theDate = new Date(serverDate);
                var myNewDate = new Date(theDate);

                myNewDate.setDate(myNewDate.getDate() + parseInt(get_day));


                //console.log('myNewDate',myNewDate);

                datetimepicker1 = jQuery('#datetimepicker1').datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    minDate: date,
                    maxDate: myNewDate
                }).focus();

                jQuery('#date-comment').css("display", "none");
            } else {

                var idperm = $('#selectprmsn').val();
                var ptypid = idperm.split('#');
                var get_ptypid = ptypid[1];
                if (get_ptypid == 3 || get_ptypid == 8 || get_ptypid == 13 || get_ptypid == 14 || get_ptypid == 15 || get_ptypid == 16 || get_ptypid == 17 || get_ptypid == 18 || get_ptypid == 20 || get_ptypid == 25) {
                    // var get_pollday1 = get_pollday.split('-');
                    //var subget_pollday = get_pollday1[0];
                    //var pppddd = get_pollday.split("-").reverse().join("-");
                    newd = new Date(serverDate);
                    //                    console.log(newd + 'sds');
                    //                   newd.setDate(newd - 2);
                    newd.setDate(newd.getDate() - 2);

                    if (date <= newd) {
                        newd = newd;
                    } else {
                        newd = max;
                    }
                    var pd = newd.getDate();
                    var pm = newd.getMonth() + 1;
                    var py = newd.getFullYear();

                    var newpdd = new Date(py + "-" + pm + "-" + pd + " 23:59:59");
                    var get_ptypid = ptypid[2];

                    if (get_ptypid != '' && parseInt(get_ptypid) >  '3') {
                        var get_day = get_ptypid;
                    } else {
                        var get_day = '7';
                    }

                    var theDate = new Date(serverDate);
                    var myNewDate = new Date(theDate);

                    myNewDate.setDate(myNewDate.getDate() + parseInt(get_day));



                    datetimepicker1 = jQuery('#datetimepicker1').datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        minDate: date,
                        maxDate: myNewDate

                    }).focus();

                    jQuery('#date-comment').css("display", "block");
                } else {
                    //console.log(max +'eee');

                    var ptypid = idperm.split('#');
                    var get_ptypid = ptypid[2];

                    if (get_ptypid != '' && parseInt(get_ptypid) >  '3') {
                        var get_day = get_ptypid;
                    } else {
                        var get_day = '7';
                    }
                    // console.log('get_ptypid',get_ptypid);
                    //console.log('get_day',get_day);
                    var theDate = new Date(serverDate);
                    var myNewDate = new Date(theDate);

                    myNewDate.setDate(myNewDate.getDate() + parseInt(get_day));

                    datetimepicker1 = jQuery('#datetimepicker1').datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        minDate: date,
                        maxDate: myNewDate
                    }).focus();

                    jQuery('#date-comment').css("display", "block");
                }
            }
        });
        
        $('#datetimepicker').on('click', function() { 
            var config = {
                format: 'DD-MM-YYYY HH:mm',

            }

            var idperm = $('#selectprmsn').val();
            // console.log('idperm',idperm);


            var ptypid = idperm.split('#');
            var get_ptypid = ptypid[2];

            if (get_ptypid != '' && parseInt(get_ptypid) >  '3') {
                var get_day = get_ptypid;
            } else {
                var get_day = '7';
            }

            var theDate = new Date(serverDate);
            var myNewDate = new Date(theDate);

            myNewDate.setDate(myNewDate.getDate() + parseInt(get_day));

            newds = new Date(serverDate);
            newds.setDate(newds.getDate() + 2);
            //console.log(rstatus);

            /* if (rstatus == 0) {
                config['minDate'] = new Date();
                config['maxDate'] = myNewDate;
                jQuery('#date-comment').css("display", "none");
            } else {
                config['minDate'] = newds;
                config['maxDate'] = myNewDate;
                jQuery('#date-comment').css("display", "block");
            } */

            if (rstatus == 0  ) {     
                        newdss = new Date(serverDate); 
                }
                else{ 
                        newdss = newds; 
                }

            console.log(newdss);
            jQuery('#datetimepicker').datetimepicker({
                format: 'DD-MM-YYYY HH:mm',
                 minDate: newdss,
                 maxDate: myNewDate//, defaultDate: newdss
            }).focus();
            jQuery('#datetimepicker').on('dp.change', function(e) {
                date = e.date

            })
        });

    var permissioncout = "<?php echo count($permission_type); ?>";
    if (permissioncout == 0) {
        var modal = $('#perm-modal').modal('show');
    }
    $('#perm-modal').on('click', '[data-dismiss="modal"]', function() {
        window.location = "<?php echo url('/'); ?>/permission";
    })


    var st = $('#state').val();
    //alert('sss');
    jQuery.ajax({
        url: "{{url('/permissiondistrict')}}/" + st,
        type: "GET",
        dataType: "Json",
        success: function(dist) {
            if (dist) {
                $("#district").empty();
                $("#district").append('<option value="">Select District</option>');
                $.each(dist, function(key, value) {
                    $('#district').append('<option value=' + value.DIST_NO + '>' + value
                        .DIST_NAME + '</option>');
                });
            } else {
                $("#district").empty();
            }

        }
    });

    $('#district').on('change', function() { //alert('sssss');
        $('#ac').empty();
        var districtID = $(this).val();
        var stateID = $('#state').val();
        $.ajax({
            type: "GET",
            url: "{{url('/permissionAC')}}/" + stateID + "/" + districtID,
            success: function(acdata) {
                if (acdata) {
                    $("#ac").empty();
                    $("#ac").append('<option option value="">Select AC</option>');
                    $.each(acdata, function(key, data) {
                        $('#ac').append('<option value=' + data.AC_NO + '>' + data
                            .AC_NAME + '</option>');
                    });
                } else {
                    $("#ac").empty();
                }

            }
        });
        function formatToDDMMYYYY(date) {
    let day = date.getDate().toString().padStart(2, '0');
    let month = (date.getMonth() + 1).toString().padStart(2, '0'); // getMonth() is zero-indexed
    let year = date.getFullYear();
    
    return day + '-' + month + '-' + year; // Combine DD-MM-YYYY with dashes
}


// AJAX call
$.ajax({
    type: "GET",
    url: "{{url('/getdttconac')}}/" + stateID + "/" + districtID,
    success: function(poll_day) {
        get_pollday = poll_day;
        console.log(get_pollday)
        if (poll_day && poll_day.length > 0) {
          
            let dateToFormat = poll_day[0].max_date_poll;
            
            
            if (dateToFormat) {
                let formattedDate = formatToDDMMYYYY(new Date(dateToFormat));
                
                $("#poll").empty();
                $('#poll').append('<input type="text" class="form-control" value="' +
                    formattedDate +
                    '" name="electiondate" id="electiondate" readonly style="display:block;">'
                );
            } 
        } 
    },
    error: function(err) {
        console.log("Error:", err);
    }
});
        var idperm = $('#selectprmsn').val();
        var ptypid = idperm.split('#');
        var get_ptypid = ptypid[1];
        var acIDD = 0;
        if (get_ptypid == 8) {
            $.ajax({
                type: "GET",
                url: "{{url('/getpc')}}/" + stateID + "/" + acIDD + "/" + districtID,
                success: function(poll_day) {
                    get_pollday = poll_day;
                    if (poll_day) {
                        console.log(poll_day);
                        $("#poll").empty();
                        $('#poll').append(
                            '<input type="text" class="form-control" value="' +
                            poll_day +
                            '" name="electiondate" id="electiondate" readonly style="display:block;">'
                            );
                    }
                }
            });
        }
    });

    $('#ac').on('change', function() {
        var acID = $(this).val();
        var stateID = $('#state').val();
        $('#ps').empty();

        $.ajax({
            type: "GET",
            url: "{{url('/policeAC')}}/" + stateID + "/" + acID,
            success: function(police) {
                if (police) {
                    if (police.length == 0) {
                        $("#police-comment").css('display', 'block');
                    } else {
                        $("#police-comment").css('display', 'none');
                    }
                    $("#ps").empty();
                    $("#ps").append(
                        '<option option value="">Select Police Station</option>');
                    $.each(police, function(key, pol) {
                        $('#ps').append('<option value=' + pol.id + '>' + pol
                            .police_st_name + '</option>');
                    });
                } else {
                    $("#ps").empty();
                }

            }
        });
        //alert('dddddd');
        var districtID = 0;
        $.ajax({
            type: "GET",
            url: "{{url('/getpc')}}/" + stateID + "/" + acID + "/" + districtID,
            success: function(poll_day) {
                get_pollday = poll_day;
                if (poll_day) {
                    console.log(poll_day);
                    $("#poll").empty();
                    $('#poll').append('<input type="text" class="form-control" value="' +
                        poll_day +
                        '" name="electiondate" id="electiondate" readonly style="display:block;">'
                        );
                }
            }
        });
    });
    $('#ac').on('change', function() {
        //                     Initialize
        $('#datetimepicker1').datetimepicker({
            format: "DD-MM-YYYY HH:mm:ss",
        });
        $('#datetimepicker1').datetimepicker('destroy');
    });

    $('#selectprmsn').on('change', function() {
        var permissionid = $('#selectprmsn').val();
        datetimepicker1 = null;
        var pID = permissionid.split('#');
        // alert(permissionid);
        if (pID[1] > 0) { //alert('rr');
            $('#acintra,#policeintra,#district,#poll').val('');
        }

    });

    // 
    $('#selectprmsn').on('change', function() {
        //Initialize
        $('#datetimepicker1').datetimepicker({
            format: "DD-MM-YYYY HH:mm:ss",
        });
        $('#datetimepicker1').datetimepicker('destroy');
        var id = $(this).val();
        var ptypid = id.split('#');
        //alert(id);

        if (ptypid[1] == 3 || ptypid[1] == 6 || ptypid[1] == 8) {
            // $("#poll").empty();	 
            if (ptypid[1] == 8) {
                //		    		var StateId = $('#state').val();
                //		    		// alert(StateId);
                //		    		$.ajax({
                //							type:"GET",
                //							url:"{{url('/getpollday')}}/"+StateId,
                //							success:function(poll_day){ 
                //							   	if(poll_day)
                //							   	{
                //							  		$("#poll").empty();	         		
                //							        $('#poll').append('<input type="text" class="form-control" value="'+poll_day+'" name="electiondate" id="electiondate" readonly>');
                //							   	}           
                //							}
                //					});


            }

            $("#eventpol").css("display", "none");
        } else {
            $("#eventpol").css("display", "block");
        }


    });


    $('#ac').on('change', function() {
        //alert('dd');
        var stcode = jQuery("#state :selected").val();
        var district = jQuery("#district :selected").val();
        var ac = jQuery("#ac :selected").val();

        jQuery.ajax({
            url: "{{url('/politicalparty/getlocations')}}",
            type: 'GET',
            dataType: 'json',
            data: {
                stcode: stcode,
                ac: ac
            },
            success: function(result) {

                var jsonObj = JSON.stringify(result);
                var acselect = jQuery('select[name="location1"]');

                var achtml = '';
                var otherhtml = '';
                achtml = achtml + '<option value=""> Select Location</option>';
                var achtmlother = '<option value=other>Other</option>';
                jQuery.each(result, function(key, value) {
                    achtml = achtml + '<option value="' + value.id + '">' + value
                        .location_name + ', ' + value.location_details +
                        '</option>';
                });
                achtml = achtml + achtmlother;
                jQuery("select[name='location1']").html(achtml);
                var achtmlend = '';
                jQuery("select[name='location1']").append(achtmlend)
            }
        });
    });


    jQuery("select[name='location1']").change(function() {

        //alert('ll');
        var stcode = jQuery("select[name='state']").val();
        var district = jQuery("select[name='district']").val();
        var ac = jQuery("select[name='ac']").val();
        var locationid = jQuery(this).val();
        if (locationid == "other") {
            $('#other').css('display', ($(this).val() == 'other') ? 'block' : 'none');
        } else {
            $('#other').css('display', ($(this).val() == 'other') ? 'display' : 'none');
        }

    });

    function LoadMap(lat, lng) {
        var src = 'https://cvigil.eci.nic.in/GIS/' + stcodes + '.kmz';
        var markers = [{
            "lat": lat,
            "lng": lng,
        }, ];
        var mapOptions = {
            center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var myContent = 'test';
        var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        var infowindow = new google.maps.InfoWindow({});
        var marker, i;
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(markers[0].lat, markers[0].lng),
            map: map,
            icon: 'https://www.google.com/mapfiles/marker_black.png'
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(placenameslocation);
                infowindow.open(map, marker);
            }
        })(marker, i));
        var src = 'https://cvigil.eci.nic.in/GIS/' + stcodes + '.kmz';
        var kmlLayer = new google.maps.KmlLayer(src, {
            suppressInfoWindows: true,
            preserveViewport: false,
            map: map
        });
    }

    $('select#selectprmsn').change(function() {
        var permsn_id = $(this).val();
        var role_idss = 4;
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');
        // alert(permsn_id);
        $.ajax({
            url: base_url + '/getrole_iddetails',
            type: 'POST',
            data: {
                _token: token,
                permsn_id: permsn_id
            },
            success: function(response) {
                //console.log(response);
                var cnt = response.length;
                var str = '';
                var required_status = '';
                ` $('.newreq').removeAttr('required');`
                var j = 1;
                for (var i = 0; i < cnt; i++) {

                    var role_id = response[i]['role_id']
                    var std_code = response[i]['st_code']

                    //alert(role_id);

                    if (role_id == 4) {
                        $('#districtmsg').hide();
                        $('#assembly').hide();
                        $('#polldata_new').hide();
                        $('#polic').hide();
                        $('#event').hide();
                        $('#polldate_new').show();
                        $('#electiondate').show();
                        $("select#district")[0].selectedIndex = 0;
                        $("select#ac")[0].selectedIndex = 0;
                        $("select#ps")[0].selectedIndex = 0;
                        $('#district').prop('required', false);
                        $('#ac').prop('required', false);
                        $('#ps').prop('required', false);
                        $('#location1').prop('required', false);



                        $.ajax({
                            type: "GET",
                            url: "{{url('/getpolldayss')}}/" + std_code,
                            success: function(poll_day) {

                                var get_pollday = poll_day;
                                //console.log(get_pollday);
                                if (poll_day) {
                                    $("#poll").empty();
                                    $('#poll').append(
                                        '<input type="text" class="form-control" value="' +
                                        get_pollday +
                                        '" name="electiondate" id="electiondate" readonly>'
                                        );

                                } else {
                                    // $("#ps").empty();
                                    confirm(hello);
                                }

                            }
                        });

                    } else if (role_id == 5) {
                        $('#districtmsg').show();
                        $('#polldata_new').show();
                        $('#assembly').hide();
                        $('#polic').hide();
                        // $('#polldate_new').hide();
                        $('#event').hide();
                        $('#eventpol').show();
                        $('#electiondate').show();
                        $("select#district")[0].selectedIndex = 0;
                        $("select#ac")[0].selectedIndex = 0;
                        $("select#ps")[0].selectedIndex = 0;
                        $('#district').prop('required', true);
                        $('#ac').prop('required', false);
                        $('#ps').prop('required', false);
                        $('#location1').prop('required', false);

                        $.ajax({
                            type: "GET",
                            url: "{{url('/getpolldayss')}}/" + std_code,
                            success: function(poll_day) {

                                var get_pollday = poll_day;
                                //console.log(get_pollday);
                                if (poll_day) {
                                    $("#poll").empty();
                                    $('#poll').append(
                                        '<input type="text" class="form-control" value="' +
                                        get_pollday +
                                        '" name="electiondate" id="electiondate" readonly>'
                                        );

                                } else {
                                    // $("#ps").empty();
                                    confirm(hello);
                                }

                            }
                        });

                    } else if (role_id == 19) { //alert('bb');
                        $('#districtmsg').show();
                        $('#acdistrict').hide();
                        $('#psdistrict').hide();
                        $('#polldata_new').show();
                        $('#assembly').show();
                        // $('#polldate_new').show();
                        $('#polic').show();
                        $('#event').show();
                        $('#eventpol').show();
                        $('#electiondate').show();
                        $("select#district")[0].selectedIndex = 0;
                        $("select#ac")[0].selectedIndex = 0;
                        $("select#ps")[0].selectedIndex = 0;
                        $('#district').prop('required', true);
                        $('#ac').prop('required', false);
                        $('#ps').prop('required', false);
                        $('#location1').prop('required', true);


                    } else {
                        //alert('aa');
                        $('#districtmsg').show();
                        $('#assembly').show();
                        $('#polldata_new').show();
                        $('#acdistrict').show();
                        $('#psdistrict').show();
                        //	 $('#polldate_new').show();
                        $('#polic').show();
                        $('#event').show();
                        $('#eventpol').show();
                        $('#electiondate').show();
                        $("select#district")[0].selectedIndex = 0;
                        $("select#ac")[0].selectedIndex = 0;
                        $("select#ps")[0].selectedIndex = 0;
                        $('#district').prop('required', true);
                        $('#ac').prop('required', true);
                        $('#ps').prop('required', true);
                        $('#location1').prop('required', true);


                    }
                    j++;



                }
            }




        });
    });

    // select permission type
    $('select#selectprmsn').change(function() {
        var permsn_id = $(this).val();
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');
        //alert(permsn_id);
        $.ajax({
            url: base_url + '/getSelectDetails',
            type: 'POST',
            data: {
                _token: token,
                permsn_id: permsn_id
            },
            success: function(response) {

                var cnt = response.length;
                var str = '';
                var check = 0;
                var required_status = '';
                //alert(cnt);
                $('#permsn_doc').css('display', '');
                str +=
                    "<table class='table table-bordered'><tr><th>S.no.</th><th>Document Details</th><th>Upload Document <br><sup style='color:red'>Note: Ensure the file name has no special characters  (e.g., < > : \" / \\ | ? * ' ; & % $ #) before uploading.</sup></th></tr>";

                //str +=
                  //  "<tr><td>1</td><td><p> Undertaking <span class='text-alert'> <a href='uploads1/userdoc/Suvidha-Undertaking-converted.pdf' download>Download Format</a><sup style='color:red'>* Mandatory</sup></span></p></td><td><input type='hidden' name='doc[01][doc_id]' value='11'><input type='file' id='file' name='permsndoc[01][p_doc]' required></td></tr>";

                var j = 1;
                for (var i = 0; i < cnt; i++) {
                    var doc_name = response[i]['doc_name'];
                    var doc_size = response[i]['doc_size'];
                    var status = response[i]['required_status'];
                    var stcode = response[i]['st_code'];
                    var doc_id = response[i]['id']
                    if (status == 1) {
                        required_status = 'Mandatory';
                    } else {
                        required_status = '';
                    }
                    var file_name = response[i]['file_name']
                    if (response[i]['authority_type_id'] != undefined && response[i][
                            'authority_type_id'
                        ] != null) {
                        var authority_id = response[i]['authority_type_id'];
                        var authdata = authority_id.split(',');
                    }
                    //                        console.log(authdata);
                    //console.log(response[i]);

                    if (response != 0 && authdata != undefined && authdata != '' &&
                        authdata != null) {

                        $.each(authdata, function(index, value) {
                            if (value === 'cand01') {
                                if (status == 1) {
                                    if (response[i]['fileserver_dir'] ==
                                        "uploads1") {
                                        str += "<tr><td>" + j + "</td><td><p>" +
                                            doc_name +
                                            " <span class='text-alert'> <a href='/ac/public/pdf/" +
                                            response[i]['file_name'] +
                                            " 'target='_blank' >Download Format</a><sup style='color:red'>* Mandatory</sup></span></p></td><td><input type='hidden' name='doc[" +
                                            i + "][doc_id]' value='" + doc_id +
                                            "'><input type='file' id='file' name='permsndoc[" +
                                            i + "][p_doc]' required></td></tr>"
                                    } else {
                                        str += "<tr><td>" + j + "</td><td><p>" +
                                            doc_name +
                                            " <span class='text-alert'> <a href='/ac/public/pdf/" +
                                            stcode + "/" + file_name +
                                            " ' target='_blank'>Download Format</a><sup style='color:red'>* Mandatory</sup></span></p></td><td><input type='hidden' name='doc[" +
                                            i + "][doc_id]' value='" + doc_id +
                                            "'><input type='file' id='file' name='permsndoc[" +
                                            i + "][p_doc]' required></td></tr>"
                                    }

                                    // /public/uploads/permission-document/
                                } else {
                                    if (response[i]['fileserver_dir'] ==
                                        "uploads1") {
                                        str += "<tr><td>" + j + "</td><td><p>" +
                                            doc_name +
                                            " <span class='text-alert'> <a href='/ac/public/pdf/" +
                                            response[i]['file_name'] +
                                            " ' target='_blank'>Download Format</a>Not Mandatory</span></p></td><td><input type='hidden' name='doc[" +
                                            i + "][doc_id]' value='" + doc_id +
                                            "'><input type='file' id='file' name='permsndoc[" +
                                            i + "][p_doc]'></td></tr>"
                                    } else {
                                        str += "<tr><td>" + j + "</td><td><p>" +
                                            doc_name +
                                            " <span class='text-alert'> <a href='/ac/public/pdf/" +
                                            stcode + "/" + file_name +
                                            " 'target='_blank' >Download Format</a>Not Mandatory</span></p></td><td><input type='hidden' name='doc[" +
                                            i + "][doc_id]' value='" + doc_id +
                                            "'><input type='file' id='file' name='permsndoc[" +
                                            i + "][p_doc]'></td></tr>"
                                    }
                                }
                                check++;
                            } else {
                                var str1 =
                                    "<p style='color:red'>No Document Required.</p>";
                                $('#permsn_doc').html(str1);

                            }

                        });

                    } else {
                        var str2 = "<p style='color:red'>No Document Required.</p>";
                        $('#permsn_doc').html(str2);

                    }
                    j++;
                }
                if (check != 0) {
                    str += "</table>";
                    $('#permsn_doc').html(str);
                } else {
                    var str1 = "<p style='color:red'>No Document Required.</p>";
                    $('#permsn_doc').html(str1);
                }

            }
        });
    });
    //end neera

    // restrication masteer
    var StateId = jQuery('#state').val();
    var date = new Date();
    var max = new Date();
    max.setDate(max.getDate() + 5);
    date.setDate(date.getDate() + 2);
    //var rstatus;
    // alert(date);
    // alert(max);
    // alert(StateId);
    jQuery.ajax({
        url: "{{url('/datevalidation')}}/" + StateId,
        type: "GET",
        dataType: "Json",
        success: function(data) {
            var status = data[0].restriction_status;
            rstatus = status;

        }
    });


});

function checkForm(form) // Submit button clicked
{
    form.AddPS.disabled = true;
    form.AddPS.value = "Please wait...";
    return true;
}
</script>
@endsection