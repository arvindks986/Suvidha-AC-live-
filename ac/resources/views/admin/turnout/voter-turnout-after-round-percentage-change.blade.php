@extends('admin.layouts.ac.theme')
@section('content')

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-7 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col-md-5  pull-right text-right">

        @foreach($buttons as $button)
        <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{$button['name']}}" <?php if ($button['target']) { ?> target='_blank' <?php } ?>>{{ $button['name'] }}</a></span>
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
      <div class="form-group col-md-2"> <label>Election Type</label>
        <select name="election_type" id="election_type" class="form-control" onchange="filter()">
          <option value="" {{ (Request::get('election_type')=='') ? 'selected' : '' }}>All Election Type</option>
          <option value="3" {{ (Request::get('election_type')=='3') ? 'selected' : '' }}>AC-GENERAL</option>
          <option value="4" {{ (Request::get('election_type')=='4') ? 'selected' : '' }}>AC-BYE</option>
        </select>
      </div>

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

      <?php if (isset($phases) && count($phases) > 0 && $state != null) { ?>
        <div class="form-group col-md-2"> <label>Election Phase</label>
          <select name="phase" id="phase" class="form-control" onchange="filter()">
            <option value="all">All Phase</option>
            @foreach($phases as $key => $result)
            @if($phase==$result->PHASE_NO)
            <option value="{{$result->PHASE_NO}}" selected="selected">{{$result->statePHASE_NO}}-Phase</option>
            @else
            <option value="{{$result->PHASE_NO}}">{{$result->statePHASE_NO}}-Phase</option>
            @endif
            @endforeach
          </select>
        </div>
      <?php  } else { ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
      <?php } ?>

  </div>
</section>

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
                  <th colspan="17" class="text-center">{!! $heading_title_with_all !!}</th>
                </tr>
                <tr>
                  <td></td>
                  <td>VT %</td>
                  <td>Missing No. Of ACs</td>
                  <td>Change of VT %</td>
                  <td>Final VT %</td>
                </tr>

              </thead>
              @if($state != null)
              <tbody>
                <tr>
                  <td>09:30</td>
                  <td>{{$results['round1_per_exclude_missed_ac']}} %</td>
                  <td>{{$results['round1_missed_ac_count']}}</td>
                  <td>{{number_format(($results['round1_per_include_missed_ac']-$results['round1_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
                  <td>{{$results['round1_per_include_missed_ac']}} %</td>
                </tr>
                <tr>
                  <td>11:30</td>
                  <td>{{$results['round2_per_exclude_missed_ac']}} %</td>
                  <td>{{$results['round2_missed_ac_count']}}</td>
                  <td>{{number_format(($results['round2_per_include_missed_ac']-$results['round2_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
                  <td>{{$results['round2_per_include_missed_ac']}} %</td>
                </tr>
                <tr>
                  <td>01:30</td>
                  <td>{{$results['round3_per_exclude_missed_ac']}} %</td>
                  <td>{{$results['round3_missed_ac_count']}}</td>
                  <td>{{number_format(($results['round3_per_include_missed_ac']-$results['round3_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
                  <td>{{$results['round3_per_include_missed_ac']}} %</td>
                </tr>
                <tr>
                  <td>03:30</td>
                  <td>{{$results['round4_per_exclude_missed_ac']}} %</td>
                  <td>{{$results['round4_missed_ac_count']}}</td>
                  <td>{{number_format(($results['round4_per_include_missed_ac']-$results['round4_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
                  <td>{{$results['round4_per_include_missed_ac']}} %</td>
                </tr>
                <tr>
                  <td>05:30</td>
                  <td>{{$results['round5_per_exclude_missed_ac']}} %</td>
                  <td>{{$results['round5_missed_ac_count']}}</td>
                  <td>{{number_format(($results['round5_per_include_missed_ac']-$results['round5_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
                  <td>{{$results['round5_per_include_missed_ac']}} %</td>
                </tr>
                <tr>
                  <th>State</th>
                  <th>{{$state_name}}</th>
                  <th colspan="2">Final Voter Turnout %</th>
                  <th>{{$results['final']}} %</th>
                </tr>
                @if(count($results) == 0)
                <tr>
                  <td colspan="17" align="center">No data found</td>
                </tr>
                @endif
              </tbody>
              @endif
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
    var url = "<?= $action ?>";
    var state = "<?= $state_encoded ?>";
    var query = '';

    if (jQuery("#election_type").val() != '' && jQuery("#election_type").val() != undefined) {
      query += '&election_type=' + jQuery("#election_type").val();
    }
    if (jQuery("#state").val() != '' && jQuery("#state").val() != undefined) {
      query += "&state=" + jQuery("#state").val();
    }
    if (state == jQuery("#state").val()) {
      if (jQuery("#phase").val() != '' && jQuery("#phase").val() != undefined) {
        query += '&phase=' + jQuery("#phase").val();
      }
      if (jQuery("#round").val() != '' && jQuery("#round").val() != undefined) {
        query += '&round=' + jQuery("#round").val();
      }
      if (jQuery("#ac").val() != undefined) {
        query += '&ac=' + jQuery("#ac").val();
      }
    }


    window.location.href = url + '?' + query.substring(1);
  }
</script>
@endsection