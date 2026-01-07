@extends('admin.layouts.ac.theme')
@section('content')


<section class="statistics color-grey pt-4 pb-2">
  <div class="row pl-5" style="font-size:16px;"><strong style="color:blue;">Disclaimer : </strong><span>* This is approximate trend as data from some Polling Stations(PS) takes time. Final data for each PS is shared in Form 17C with all Polling Agents.</span></div>
  <div class="row">
    <hr>
  </div>
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

<section class="dashboard-header section-padding">
  <div class="container-fluid">


    <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
      <div class="form-group col-md-3"> <label>State </label>

        <select name="state" id="state" class="form-control" onchange="filter()">
          <option value="">Select State</option>
          @foreach($states as $result)
          @if($state== base64_decode($result['code']))
          <option value="{{$result['code']}}" selected="selected">{{$result['name']}}</option>
          @else
          <option value="{{$result['code']}}">{{$result['name']}}</option>
          @endif
          @endforeach

        </select>
      </div>

      <?php if (isset($phases) && count($phases) > 0) { ?>
        <div class="form-group col-md-2"> <label>Election Phase</label>

          <select name="phase" id="phase" class="form-control" onchange="filter()">
            <option value="all">All Phase</option>
            @foreach($phases as $result)
            @if($phase==$result->PHASE_NO)
            <option value="{{$result->PHASE_NO}}" selected="selected">{{$result->StatePHASE_NO}}-Phase</option>
            @else
            <option value="{{$result->PHASE_NO}}">{{$result->StatePHASE_NO}}-Phase</option>
            @endif
            @endforeach

          </select>
        </div>
      <?php  } else { ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
      <?php } ?>



    </form>


  </div>
</section>



<div class="container-fluid">
  <!-- Start parent-wrap div -->
  <div class="parent-wrap">
    <!-- Start child-area Div -->
    <div class="child-area">
      <div class="page-contant">
        <div class="random-area">
          <div class="table-responsive">
            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
              <thead>

                <tr>
                  <th colspan="14" class="text-center">{!! $heading_title_with_all !!}</th>
                </tr>


                <tr>
                  <th> State </th>
                  <th> AC No</th>
                  <th> AC Name </th>
                  <!-- <th> Loksabha Election - 2019</th> -->
                  <th> Legislative Assembly - 2018</th>
                  <th> Legislative Assembly - 2023</th>
                  <th> Change In Percentage</th>
                </tr>
              </thead>
              <tbody>
                @foreach($results as $result)
                <tr>
                  <td>{{$result['st_name']}}</td>
                  <td>{{$result['ac_no']}}</td>
                  <td>{{$result['ac_name']}}</td>
                  <!-- <td>{{$result['levt_vt']}}</td> -->
                  <td>{{$result['lavt_vt']}}</td>
                  <td>{{$result['est_turnout_total']}}</td>
                  <td style="background-color:{{ ($result['change_in_percentage'] >= 0) ? '':'red' }};">{{$result['change_in_percentage']}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>

          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->


      </div><!-- End Of random-area Div -->

    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->
</div>


<script type="text/javascript">
  function filter() {
    var url = "<?php echo $action ?>";
    var query = '';

    if (jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined') {
      query += "&state=" + jQuery("#state").val();
    }
    if (jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined') {
      query += '&phase=' + jQuery("#phase").val();
    }
    window.location.href = url + '?' + query.substring(1);
  }

  setTimeout(function(e) {
    referesh_page();
  }, 300000);

  function referesh_page() {
    location.reload();
  }
</script>
@endsection