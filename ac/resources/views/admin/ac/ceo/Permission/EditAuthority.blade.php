@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
<main role="main" class="inner cover mb-3 mb-auto">
    @include('admin.ac.ceo.Permission.permission-master-menu')
    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>Update Authority</h2>
                            </div>
                            @if (Session::has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                            @endif
                             @if (session('chckmessage'))
                            <div class="alert alert-danger">
                                {{ session('chckmessage') }}
                            </div>
                            @endif
                            <div class="card-body getpermission">


                                @if(!empty($getAuthorityDetails))
                                @foreach($getAuthorityDetails as $data)
                                <form class="form-horizontal" method="POST" action="{{url('/acceo/editauthority')}}">
                                    {{csrf_field()}}


                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Authority Type <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="hidden" class="form-control" name="auth_id" value="{{$data->id}}">
                                            <input type="text" class="form-control" placeholder="Enter Authority Type" name="name" value="{{$data->name}}">
                                            <span class="text-danger">{{ $errors->error->first('name') }}</span>
                                        </div>
                                    </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group row">

                                    <div class="col">
                                        <button class="btn btn-success float-right" name="submit" value="Update">UPDATE</button>
                                    </div>
                                </div>
                            </div>
                            </form>
                            @endforeach
                            @endif

                        </div>
                    </div>
                </div>





            </div>
        </div>

    </section>

</main>

@endsection