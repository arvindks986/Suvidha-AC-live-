@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
@include('admin.ac.deo.Permission.permission-master-menu')
    <section>
        @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                   @endif
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
             <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h2>Permission Cell Incharge Details</h2>
                </div>
                <div class="card-body tabular-pane">
                 <table id="example1" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
                <th>UserName</th>
                <th>Incharge Name</th>
              <th>Email</th><th>Mobile</th>
              <th>Role Name</th>
              <th>Edit/Update</th>
              <th>Active/Inactive</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($getAllPCIData))
            @foreach($getAllPCIData as $data)
            <tr>
                <td>{{$data->username}}</td>
                <td>{{$data->name}}</td>
                <td>{{$data->email}}</td>
                <td>{{$data->mobile}}</td>
                <td>{{$data->role_name}}</td>
                <td id="edit"><a href="{{url('/acdeo/editpci')}}/{{$data->id}}"><span class=" btn btn-success">Edit</span></a></td>
                @if($data->status == 1)
                <td id='setStatus'><span class='btn btn-success setStatus' id='1#{{$data->pci_id}}#{{$data->role_id}}#{{$data->officer_login_id}}'>Active</span></td>
                @else
                <td id='setStatus'><span class='btn btn-danger setStatus' id='0#{{$data->pci_id}}#{{$data->role_id}}#{{$data->officer_login_id}}'>Inactive</span></td>
                @endif
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
                    {{ $getAllPCIData->links() }}
                </div>
              </div>
            </div>
</div>
</div>

</section>
@endsection
@section('script')
<script type="text/javascript">
  $(function () {
      $('#example1').dataTable({
                "bPaginate": false
            });
  var base_url = $("#base_url").val();
  var token = $('meta[name="csrf-token"]').attr('content');
  $('.setStatus').on('click',function(){
            var status=$(this).attr('id');
            var getArray= status.split("#");
            if(getArray[0] == 1)
            {
            var res=confirm('Are you sure you want to Inactive this user');
            }else
            {
                var res=confirm('Are you sure you want to Active this user');
            }
            if(res == true)
            {
                $.ajax({
                    url: base_url + '/acdeo/pcistatus',
                    type: 'POST',
                    data: {_token: token, status: status},
                    success: function (data)
                    {
                         if(data == 2)
                         {
                             alert('One user of this role is already active. To make this user active you need to Inactive the active User.');
                         }
                         else
                         {
                          location.reload();
                         }
                    }
                });
            }
            else
            {
                return false;
            }
        });
    });
    </script>
@endsection
