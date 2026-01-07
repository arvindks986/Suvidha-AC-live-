@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
@include('admin.ac.deo.Permission.permission-master-menu')
<main role="main" class="inner cover mb-3 mb-auto">
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
 @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    
    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>Assign permission To PCI</h2>
                            </div>
                            <div class="card-body getpermission">



                                <form class="form-horizontal" method="POST" action="{{url('/acdeo/assigntopcidata')}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                                        <div class="col-sm-8">
<!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                                            <select name="pname" class="form-control" id="selectprmsn">
                                                <option value="0">Select Permission</option>
                                                @if(!empty($permissionDetails))
                                                @foreach($permissionDetails as $pdata)
                                                <option value="{{$pdata->permission_type_id}}" {{ (collect(old('pname'))->contains($pdata->permission_type_id)) ? 'selected':'' }}>
                                                {{$pdata->permission_name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                        </div>
                                    </div>
                                        <div class="form-group">
                                            <div class="col-md-12" id="permsn_doc">

                                            </div>
                                        </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

    </section>

</main>
@endsection
@section('script')
<script type="text/javascript">
     $(document).ready(function() {
     $('select#selectprmsn').change(function () {
            var permsn_id = $(this).val();
            var base_url = $("#base_url").val();
            var token = $('meta[name="csrf-token"]').attr('content');
            //alert(permsn_id);
            $.ajax({
                url: base_url + '/acdeo/getPCIDetails',
                type: 'POST',
                data: {_token: token, p_id: permsn_id},
                success: function (response)
                { 
                    var str = '';
                   var j=1;
                    $('#permsn_doc').css('display', '');
                    if (response != 0)
                    {
                         str +="<table class='table table-bordered'><tr><th>S.no.</th><th>PCI Name</th><th>Role Type</th><th>PCI Mobile</th><th>Department</th></tr>";
                        var name = response[0]['name'];
                        var mb = response[0]['mobile'];
                        var dept = response[0]['department'];
                        var role = response[0]['role_name'];
                        str +="<tr><td>"+j+"</td><td>"+ name +"</td><td>"+role+"</td><td>"+mb+"</td><td>"+dept+"</td>";
                    }
                    else
                       {
                           str += "<p style='color:red'>No permission cell incharge assigned</p>";

                       }
                    str +="</table>";
                    $('#permsn_doc').html(str);

                }
            });
        });
} );
</script>
 
@endsection