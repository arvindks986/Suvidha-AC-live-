@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
    @include('admin.ac.ceo.Permission.permission-master-menu')
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
                            <h2>Authority List</h2>
                        </div>
                        <div class="card-body tabular-pane">
                            <table id="list-table" class="table table-striped table-bordered table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Authority Type Name</th>
                                        <th>Edit/Update</th>
                                    </tr>
                                </thead>
                                <tbody><?php $i='1'; ?>
                                    @if(!empty($getAllAuthorityData))
                                    @foreach($getAllAuthorityData as $data)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$data->name}}</td>
                                        <td id="edit"><a
                                                href="{{url('/acceo/editauthority')}}/{{Crypt::encryptString($data->id)}}"><span
                                                    class=" btn btn-success">Edit</span></a></td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                    @endif
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</main>

@endsection