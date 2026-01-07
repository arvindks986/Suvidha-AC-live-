@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    @include('admin.ac.ceo.Permission.permission-master-menu')

    <section>
        @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
        @endif
        <div class="container-fluid mt-5 mb-5">

            <div class="col-lg-12 p-0">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h2>Permission List</h2>
                    </div>
                    <div class="card-body tabular-pane">
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                            <div class="col-sm-8">
                               
                                <select name="pname" class="form-control" id="selectprmsn">
                                    <option value="0">Select Permission Type</option>
                                    @if(!empty($getAllPermsData))
                                    @foreach($getAllPermsData as $pdata)
                                    <option value="{{$pdata['enc_p_id']}}"
                                        {{ (collect(old('pname'))->contains($pdata['enc_p_id'])) ? 'selected':'' }}>
                                        {{$pdata['pname']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-md-12" id="permsn_doc">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
    <input type="hidden" value="<?php echo url('/'); ?>" id='base_url' />
</main>


@endsection
@section('script')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var table = $('#example').DataTable();

    $('select#selectprmsn').change(function() {
        var permsn_id = $(this).val();
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');  
        $.ajax({
            url: base_url + '/acceo/getdocdetails',
            type: 'POST',
            data: {
                _token: token,
                p_id: permsn_id
            },
            success: function(response) { 
                var cnt = response.length;
                var str = '';
                var required_status = '';
                var prmsnid = response[0]['permission_id_enc'];
                var j = 1;
                $('#permsn_doc').css('display', '');
                if (response != 0) {
                    str +=
                        "<table class='table table-bordered'><tr><th>S.no.</th><th>Document Details</th><th>Authority Type</th><th>Required Status</th></tr>";
                    for (var i = 0; i < cnt; i++) {
                        //                        var j=1;
                        var doc_name = response[i]['doc_name'];
                        var doc_size = response[i]['doc_size'];
                        var status = response[i]['required_status'];
                        var stcode = response[i]['st_code'];
                        var fileserver = response[i]['fileserver_dir'];
                        var ptypeid = response[i]['permission_type_id'];
                        var authname;
                        if (response[i]['auth_name'] != undefined && response[i][
                                'auth_name'
                            ] != null) {
                            authname = response[i]['auth_name'] + ',';
                        } else {
                            authname = "";
                        }
                        if (response[i]['canddoc_name'] != undefined && response[i][
                                'canddoc_name'
                            ] != '') {
                            authname += response[i]['canddoc_name'];
                        }
                        if (status == 1) {
                            required_status = 'Mandatory';
                        } else {
                            required_status = 'Not Mandatory';
                        }
                        var file_name = response[i]['file_name'];
                        var e_id = '<?php echo $user_data->election_id ?>';
                        //                         str += "<ul class='list-inline'><li>" + doc_name + "</li><li>" +doc_size+"</li><li>"+required_status+"</li><li><a href='{{asset('public/uploads/permission-document')}}/"+file_name+" ' download>"+file_name+"</a></li><li><input type='file' name='permsndoc["+i+"][p_doc]'></li></ul>";
                        //                            str += "<div class='row'><div class='col-md-12'><p>" + doc_name + " <small class='text-danger float-right'>" + required_status + "</small></p><br /><div class='custom-file browsebtn  mb-3'><input type='file' class='custom-file-input' id='customFile' name='permsndoc[" + i + "][p_doc]'><label class='custom-file-label' for='customFile'>Choose file</label></div></div></div>";
                        //                            str += "<p>" + doc_name + " <small class='text-danger float-right'>" + required_status + "</small></p><br /><div class='custom-file browsebtn  mb-3'><input type='file' class='custom-file-input' id='customFile' name='permsndoc[" + i + "][p_doc]'><label class='custom-file-label' for='customFile'>Choose file</label></div>";
                        if (ptypeid != 0) {
                            if (status == 1) {
                                if (fileserver == 'uploads') {
                                    str += "<tr><td>" + j + "</td><td><p>" + doc_name +
                                        " <span class='text-alert'>";
                                    if (file_name != 'NULL') {
                                        str +=
                                            " <a href='{{asset('uploads/permission-document')}}/" +
                                            stcode + "/" + file_name +
                                            " ' download>Download Format</a>   ";
                                    }
                                    str += "</span></p></td>";
                                } else {
                                    str += "<tr><td>" + j + "</td><td><p>" + doc_name +
                                        " <span class='text-alert'>";
                                    if (file_name != 'NULL') {
                                        str += "  <a href='{{asset('/')}}" + file_name +
                                            " ' download>Download Format</a>";
                                    }
                                    str += "</span></p></td>";
                                }
                                str += "<td>" + authname +
                                    "</td><td><span class='text-alert'>" + required_status +
                                    "</span></td</tr>";
                            } else {
                                if (fileserver == 'uploads') {
                                    str += "<tr><td>" + j + "</td><td><p>" + doc_name +
                                        " <span class='text-alert'>";
                                    if (file_name != 'NULL') {
                                        str +=
                                            "  <a href='{{asset('uploads/permission-document')}}/" +
                                            stcode + "/" + file_name +
                                            " ' download>Download Format</a>";
                                    }
                                    str += "</span></p></td>";
                                } else {
                                    str += "<tr><td>" + j + "</td><td><p>" + doc_name +
                                        " <span class='text-alert'>";
                                    if (file_name != 'NULL') {
                                        str += " <a href='{{asset('/')}}" + file_name +
                                            " ' download>Download Format</a>";
                                    }
                                    str += "</span></p></td>";
                                }
                                str += "<td>" + authname +
                                    "</td><td><span class='text-alert'>" + required_status +
                                    "</span></td</tr>"
                            }
                        }
                        j++;
                    }
                    str += "<tr rowspan='3'><td><a href='{{url('/acceo/editpermsn')}}/" +
                        prmsnid +
                        "'><span class='btn btn-success'>Edit</span></a></td></tr>"
                } else {
                    str += "<p style='color:red'>No Document Required.</p>";

                }
                str += "</table>";
                $('#permsn_doc').html(str);

            }
        });
    });
});
</script>
@endsection