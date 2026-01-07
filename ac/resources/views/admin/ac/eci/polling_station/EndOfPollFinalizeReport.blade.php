@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', '')
@section('content')


@if($errors->any())
<div class="alert alert-info">{{$errors->first()}}</div>
@endif

@if (session('error'))
<div class="alert alert-info">{{ session('error') }}</div>
@endif


<style type="text/css">
  .loader {
    position: fixed;
    left: 50%;
    right: 50%;
    border: 16px solid #f3f3f3;
    /* Light grey */
    border-top: 16px solid #3498db;
    /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    z-index: 99999;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>

<div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-7 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col-md-5  pull-right text-right">

        @foreach($buttons as $button)
        <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if ($button['target']) { ?> target='_blank' <?php } ?>>{{ $button['name'] }}</a></span>
        @endforeach

      </div>

    </div>
  </div>
</section>

@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
        <?php $but = explode(':', $button); ?>
        <span class="pull-right" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach
      </div>
    </div>
  </div>
</section>
@endif


<div class="container-fluid">
  <!-- Start parent-wrap div -->
  <div class="parent-wrap">
    <!-- Start child-area Div -->
    <div class="child-area">
      <div class="page-contant">
        <div class="random-area">
          <br>



          <div class="table-responsive">

            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
              <thead>

                <tr>
                  <th colspan="10" class="text-center">{!! $heading_title !!}</th>
                </tr>


                <tr>
                  <th>Serial No</th>
                  <th>Phase</th>
                  <th>Election Type</th>
                  <th>State Code</th>
                  <th>State</th>
                  <th>Total AC</th>
                  <th>RO Finalize</th>
                  <th>DEO Finalize</th>
                  <th>CEO Finalize</th>
                  <th>Total Published</th>
                </tr>
              </thead>
              <tbody>
                @php $count = 1; @endphp
                @forelse ($results as $key=>$listdata)
                <tr>
                  <td>{{ $count++ }}</td>
                  <td>{{ $self->getPhaseForReport($listdata->phase) }}</td>
                  <td>{{ $self->getElectionType($listdata->ELECTION_TYPEID)}}</td>
                  <td>{{$listdata->st_code}}</td>
                  <td>{{$listdata->st_name}}</td>
                  <td>{{$listdata->totalac}}</td>
                  <td>{{$listdata->ro_finalize}}</td>
                  <td>{{$listdata->deo_finalize}}</td>
                  <td>{{$listdata->ceo_finalize}}</td>
                  <td>{{$listdata->publish}}</td>
                </tr>
                @empty
                <tr>
                  <td class="text-center" colspan="10">No Data Found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->
      </div><!-- End Of random-area Div -->
    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->

@endsection

@section('script')
<script type="text/javascript">

</script>
@endsection