@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.css') }}">
<script src="{{ asset('js/bootstrap-multiselect.js') }}"></script>
<style>
.multiselect-native-select .btn-group,
.multiselect-native-select button {
    width: 100%;
    text-align: left;
    background-color: transparent;
}

.form-inline .multiselect-container li a label.checkbox input[type=checkbox],
.form-inline .multiselect-container li a label.radio input[type=radio] {
    margin-right: 7px;
}

.form-inline .multiselect-container label.checkbox,
.form-inline .multiselect-container label.radio {
    padding: 3px 20px 3px 25px;
}

.getpermission label {
    font-size: 14px !important;
}

.dropdown-toggle::after {
    right: 10px;
    top: 14px;
    position: absolute;
}

.multiselect-container {
    width: 100%;
}

.file-select-name {
    max-width: 185px;
}

.perm_cls option:disabled {
    color: #cf4b8b !important;
}
</style>
<main role="main" class="inner cover mb-3 mb-auto">
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    @if(count($errors->error))
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.
        <br />
        <ul>
            @foreach($errors->error->all() as $erro)
            <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @include('admin.ac.ceo.Permission.permission-master-menu')
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    <section class="mt-5" id="wrapper">
        <div class="container-fluid mt-5 mb-5">

            <div class="col-lg-12 p-0">
                <div class="sidebar__inner">
                    <div class="card">
                        <!--  style="max-width:700px; margin:0 auto;" -->
                        <div class="card-header d-flex align-items-center">
                            <h2>Add Permission</h2>
                        </div>
                        <div class="card-body getpermission">



                            <form class="form-horizontal" method="POST" action="{{url('/acceo/AddPermissionData')}}"
                                enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>  -->
                                        <select name="pname" class="form-control perm_cls" required>
                                            <option value="">Select Permission Type</option>
                                            @if(!empty($getAllPermissiontype))
                                            @foreach($getAllPermissiontype as $pdata)
                                            <option value="{{$pdata->id}}"
                                                {{ ( ( in_array($pdata->id, $getasignperm) ) ? 'disabled' : '' ) }}>
                                                {{$pdata->permission_name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                    </div>

                                </div>
                                 <div class="form-group row">
                         <label class="col-sm-4 form-control-label">Maximum Days Allowed<br/>to Submit the <b>Online Permission</b><sup>*</sup></label>
                          <div class="col-sm-8">
                            <select class="form-control" id="daySelector" name="restriction_day" required>
                                 
                            </select>
                           <span class="text-danger">{{ $errors->error->first('restriction_day') }}</span>
                          </div>
                        </div> 
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Assigned to Level<sup>*</sup></label>
                                    <div class="col-sm-8">
                                        <!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                                        <select name="ofcrlevel" class="form-control"
                                            onchange='return vis_check_opt(this.value)' required>
                                            <option value="">Select Assigned to Level</option>
                                            @if(!empty($getrole))
                                            @foreach($getrole as $pdata)
                                            <option value="{{$pdata->role_id}}"
                                                {{ (collect(old('ofcrlevel'))->contains($pdata->role_id)) ? 'selected':'' }}>
                                                {{$pdata->role_name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger">{{ $errors->error->first('ofcrlevel') }}</span>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Visibility Type <sup></sup></label>

                                    <div class="col-sm-8">

                                        <label class="checkbox-inline mr-3">
                                            <input id="4_inlineCheckbox1" type="checkbox" name="visible_type[]"
                                                value="CEO" class="vis_tp_chk"> <span>CEO</span>

                                            <span class="text-danger">{{ $errors->error->first('visible_type') }}</span>
                                        </label>
                                        <label class="checkbox-inline mr-3"  id="deo">
                                            <input id="5_inlineCheckbox1" type="checkbox" name="visible_type[]"
                                                value="DEO" class="vis_tp_chk"> <span>DEO</span>

                                            <span class="text-danger">{{ $errors->error->first('visible_type') }}</span>
                                        </label>
                                          <label class="checkbox-inline mr-3" style="display: none;">
                                            <input id="19_inlineCheckbox1" type="checkbox" name="visible_type[]"
                                                value="ROAC" class="vis_tp_chk"> <span>ROAC</span>

                                            <span class="text-danger">{{ $errors->error->first('visible_type') }}</span>
                                        </label> 






                                    </div>

                                </div>
                                <!--                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Approval Required from Authority <sup>*</sup></label>

                                        <div class="col-sm-8">
                                            @if(!empty($getAuthType))
                                            @foreach($getAuthType as $authdata)
                                            <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox1" type="checkbox" name="auth_name[]" value="{{$authdata->id}}"> {{$authdata->name}}
                                                <span class="text-danger">{{ $errors->error->first('authtype') }}</span>
                                            </label>
                                            @endforeach
                                            @else
                                            <label class="checkbox-inline mr-3"><span class="alert alert-danger">Please Add Authority Type first</span></label>
                                            @endif
                                        </div>
                                    </div>-->
                                <div id="dynamic_field">
                                    <div class="row d-flex align-items-center form-inline">
                                        <div class="col"> <label class="sr-only" for="inlineFormInputName2">Document
                                                Name</label>
                                            <input style="width:100%;" type="text" name="doc[0][Dname]"
                                                value="{{old('doc.0.Dname]')}}" class="form-control"
                                                id="inlineFormInputName2" placeholder="Document Name" required>
                                            <span class="text-danger">{{ $errors->error->first('doc.0.Dname') }}</span>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="doc[0][chck]"
                                                value="1" id="inlineFormCheck">
                                            <label class="form-check-label" for="inlineFormCheck">
                                                Mandatory
                                            </label>
                                        </div>
                                        <div class="col">
                                            <div class="file-box" id="active_div0">
                                                <div class="file-select">
                                                    <div class="file-select-name noFile0" id="">No file chosen...</div>
                                                    <input type="file" name="doc[0][format]" onchange="getfile(0)"
                                                        id="customFile0"
                                                        class="custom-file-input affidavit form-control mr-auto"
                                                        accept=".pdf" required>
                                                    <div class="file-select-button customchoose" id="fileName" >Choose
                                                        File</div>
                                                </div>
                                                <!-- <span class="text-danger">{{ $errors->error->first('doc.0.format') }}</span> -->
                                            </div>
                                        </div>
                                        <div class="col">
                                            <select name="doc[0][approvalauthority][]"
                                                class="multiselect-ui form-control" required>
                                                <option value="cand01">Applicant</option>
                                                @if(!empty($getAuthType))
                                                @foreach($getAuthType as $authdata)
                                                <option value="{{$authdata->id}}">{{$authdata->name}}</option>
                                                @endforeach
                                                <label class="checkbox-inline mr-3"><span
                                                        class="alert alert-danger">Please Add Authority Type
                                                        first</span></label>
                                                @endif
                                            </select>
                                            <span
                                                class="text-danger">{{ $errors->error->first('doc.0.approvalauthority') }}</span>
                                        </div>

                                        <button type="button" class="btn btn-primary btn-sm" id="add"
                                            style="height: 30px;">Add New</button>

                                    </div>
                                </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group row">

                                <div class="col">
                                    <button class="btn btn-success float-right">Submit</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>
@endsection
@section('script')
<script type="text/javascript">
$(function() {
    $('.multiselect-ui').multiselect({
        includeSelectAllOption: false,
        nonSelectedText: 'Approval Required from Authority'
    });
    var authtype = <?php print_r(json_encode($getAuthType)) ?>;
    var option;
    option += '<option value="cand01">Applicant</option>';
    if (authtype != '' && authtype != undefined) {
        $.each(authtype, function(index, value) {
            option += '<option value="' + value.id + '">' + value.name + '</option>';
        });
    } else {
        option +=
            '<label class="checkbox-inline mr-3"><span class="alert alert-danger">Please Add Authority Type first</span></label>';
    }
    var i = 0;
    $('#add').click(function() {

        i++;
        $('#dynamic_field').append(
            '<div class="row d-flex align-items-center form-inline dynamic-added mt-2" id="row' +
            i + '"><div class="col">\n\
   <label class="sr-only" for="inlineFormInputName2">Document Name</label>\n\
  <input style="width:100%;" type="text" name="doc[' + i + '][Dname]" class="form-control" id="inlineFormInputName2" placeholder="Document Name"></div>\n\
<div class="form-check mb-2 mr-sm-2"> <input class="form-check-input" type="checkbox" name="doc[' + i +
            '][chck]" value="1" id="inlineFormCheck' + i +
            '"><label class="form-check-label" for="inlineFormCheck' + i +
            '">Mandatory</label></div>\n\<div class="col"><div class="file-box" id="active_div' +
            i + '"><div class="file-select"><div class="file-select-name noFile' + i +
            '" id="">No file chosen...</div><input type="file" onchange="getfile(' + i +
            ')" name="doc[' + i + '][format]" class="custom-file-input" id="customFile' + i + '" ><div class="file-select-button customchoose" id="fileName">Choose File</div></div></div></div>\n\
 <div class="col"><select name="doc[' + i + '][approvalauthority][]" class="multiselect-ui form-control">\n\
     ' + option + '\n\
</select></div>\n\
<button type="button" name="remove" class="btn btn-warning btn_remove" id="' + i + '">Remove</button>\n\
</div>\n\
</div>\n\
   ');

        $('.multiselect-ui').multiselect({
            includeSelectAllOption: false,
            nonSelectedText: 'Approval Required from Authority'
        });
    });


    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
</script>
<script type="text/javascript">
//$('#customFile'+id).bind('change', function () {
function getfile(id) {
    var filename = $("#customFile" + id).val();
    if (/^\s*$/.test(filename)) {
        $("#active_div" + id).removeClass('file-upload active');
        $(".noFile" + id).text("No file chosen...");
    } else {
        $("#active_div" + id).addClass('file-upload active');
        $(".noFile" + id).text(filename.replace("C:\\fakepath\\", ""));
    }
}
</script>
<script type="text/javascript">
//$('#customFile'+id).bind('change', function () {
function getfile(id) {
    var filename = $("#customFile" + id).val();
    if (/^\s*$/.test(filename)) {
        $("#active_div" + id).removeClass('file-upload active');
        $(".noFile" + id).text("No file chosen...");
    } else {
        $("#active_div" + id).addClass('file-upload active');
        $(".noFile" + id).text(filename.replace("C:\\fakepath\\", ""));
    }
}

function vis_check_opt(id){
    $(".vis_tp_chk").prop("checked", false);  
    $("#"+id+"_inlineCheckbox1").prop("checked", true);

    //$("#"+id+"_inlineCheckbox1").prop("disabled", true); 
    
    
     if ($("#4_inlineCheckbox1").prop("checked")) {
         $("#deo").css("display", "none");
         
    } else {
         $("#deo").show();
       
    }
}

// function vis_check_opt(id) {
//     $(".vis_tp_chk").prop("checked", false);
//     $("#" + id + "_inlineCheckbox1").prop("checked", true);
// }
</script>
<script>
    $(document).ready(function() {
      function populateDays(numberOfDays, defaultValue) {
        var select = $('#daySelector');
        select.empty();

        // Add default option with selected attribute
        var defaultOption = $('<option>').text('Select Days').val('').attr('selected', true);
        select.append(defaultOption);

        for (var i = 1; i <= numberOfDays; i++) {
          var option = $('<option>').text(i).val(i);
          select.append(option);
        }
        select.val(defaultValue); // Set default value
      }

      // Do not specify a default value (remove the following line or set it to null)
      var defaultValue = null;

      // Call the function without specifying a default value
      populateDays(75, defaultValue);
    });
  </script>
@endsection