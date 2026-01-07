@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.css') }}">
<script src="{{ asset('js/bootstrap-multiselect.js') }}"></script>
<style>
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
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card">
                            <!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>ADD Permission Day Restriction
                                </h2>
                            </div>
                           
                            <div class="card-body getpermission">



                                <form class="form-horizontal" method="POST"
                                    action="{{url('/acceo/PermissionDayRestrictionData')}}">
                                    {{csrf_field()}}

                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Select Permission
                                            <sup>*</sup></label>
                                        <div class="col-sm-8">

                                            <select name="pname" class="form-control perm_cls" required>
                                                <option value="" style="color:#CCC">Select Permission Type</option>
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
                                        <label class="col-sm-4 form-control-label">Permission Validity Day
                                            <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="restriction_day" required>
                                                <option value="">Select Days</option>
                                                <option value="01">1</option>
                                                <option value="02">2</option>
                                                <option value="03">3</option>
                                                <option value="04">4</option>
                                                <option value="05">5</option>
                                                <option value="06">6</option>
                                                <option value="07">7</option>
                                                <option value="08">8</option>
                                                <option value="09">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                                <option value="24">24</option>
                                                <option value="25">25</option>
                                                <option value="26">26</option>
                                                <option value="27">27</option>
                                                <option value="28">28</option>
                                                <option value="29">29</option>
                                                <option value="30">30</option>
                                                <option value="31">31</option>
                                            </select>
                                            <span
                                                class="text-danger">{{ $errors->error->first('restriction_day') }}</span>
                                        </div>
                                    </div>

                            </div>
                            <div class="card-footer">
                                <div class="form-group row">

                                    <div class="col">
                                        <button class="btn btn-success float-right" name="submit"
                                            value="ADD">ADD</button>
                                    </div>
                                </div>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>





            </div>
        </div>

    </section>

</main>

@endsection