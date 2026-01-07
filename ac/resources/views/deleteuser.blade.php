@extends('layouts.theme')
@section('title', 'Permission')
@section('content')

<main role="main" class="inner cover mb-3">
    <section class="mt-5 prflTop">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @if(session()->has('msg'))
                        <div class="alert alert-warning text-center">
                            {{ session()->get('msg') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>Applicant Personal Details</h4>
                        </div>

                        <div class="card-body">
                            <div>
                                <form class="form-horizontal" action="{{ url('/RemoveDummyUser') }}" id="myForm" method="post" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div >
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Userid</th>
                                                    <th scope="col">Mobile</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>{{ $datanew->name }}</td>
                                                    <td>{{ $datanew->id . '_adsasd' ?? 'sadsad' }}</td>
                                                    <td>{{ $datanew->mobile }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group row float-right">
                                        <div class="col">
                                           <button type="submit" class="btn btn-primary" id="remUser" onclick="showAlert(event)">Delete</button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- End card-body -->
                    </div> <!-- End card -->
                </div> <!-- End col-md-12 -->
            </div> <!-- End row -->
        </div> <!-- End container -->
    </section>
</main>


@endsection
@section('script')
<script type="text/javascript">
	function showAlert(event) {
        event.preventDefault();

        if (confirm("Are you sure to delete this user ")) {
            document.getElementById("myForm").submit();
        } else {
            alert("Form submission canceled");
        }
    }




</script>
@endsection
